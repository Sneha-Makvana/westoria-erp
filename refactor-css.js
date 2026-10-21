const fs = require('fs');
const path = require('path');

const cssPath = path.join(__dirname, 'public', 'admin', 'assets', 'css', 'style.css');

if (!fs.existsSync(cssPath)) {
    console.error('style.css not found at', cssPath);
    process.exit(1);
}

console.log('Reading style.css...');
let css = fs.readFileSync(cssPath, 'utf8');

// Regex to find hex colors
const hexRegex = /#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})\b/g;

function hexToRgb(hex) {
    hex = hex.replace(/^#/, '');
    if (hex.length === 3) {
        hex = hex.split('').map(char => char + char).join('');
    }
    const bigint = parseInt(hex, 16);
    return {
        r: (bigint >> 16) & 255,
        g: (bigint >> 8) & 255,
        b: bigint & 255
    };
}

function getLuminance({ r, g, b }) {
    const a = [r, g, b].map(function (v) {
        v /= 255;
        return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
    });
    return a[0] * 0.2126 + a[1] * 0.7152 + a[2] * 0.0722;
}

const uniqueColors = new Set();
let match;
while ((match = hexRegex.exec(css)) !== null) {
    uniqueColors.add(match[0].toLowerCase());
}

console.log(`Found ${uniqueColors.size} unique colors.`);

const colorVars = {};
let lightRoot = ':root, :root[data-theme="light"] {\n';
let darkRoot = ':root[data-theme="dark"] {\n';

// Custom dark mode overrides
const darkOverrides = {
    '#ffffff': '#1e293b', // Card backgrounds
    '#fff': '#1e293b',
    '#f8f9fa': '#0f172a', // Body background
    '#fafbfe': '#0f172a',
    '#f4f4f4': '#0f172a',
    '#000000': '#ffffff', // Text
    '#000': '#ffffff',
    '#212529': '#f1f5f9',
    '#212b36': '#f1f5f9',
    '#333333': '#e2e8f0',
    '#333': '#e2e8f0',
    '#e9ecef': '#334155', // Borders
    '#eef1f6': '#334155',
    '#edf1f6': '#334155',
    '#1b2850': '#1b2850', // Sidebar (keep it dark or slightly adjust)
};

const keepColors = [
    '#ff9f43', '#fe820e', '#ff8c2e', // Orange accents
    '#8bc34a', '#28c76f', // Greens
    '#e53935', '#ea5455', '#ff3b30' // Reds
];

let counter = 1;
uniqueColors.forEach(hex => {
    const varName = `--theme-color-${counter++}`;
    colorVars[hex] = varName;
    
    lightRoot += `  ${varName}: ${hex};\n`;
    
    let darkHex = hex;
    if (keepColors.includes(hex)) {
        darkHex = hex;
    } else if (darkOverrides[hex]) {
        darkHex = darkOverrides[hex];
    } else {
        const rgb = hexToRgb(hex);
        const lum = getLuminance(rgb);
        
        if (lum > 0.8) {
            darkHex = '#1a2234'; 
        } else if (lum > 0.5) {
            darkHex = '#2a3447';
        } else if (lum < 0.1) {
            darkHex = '#f8fafc';
        } else if (lum < 0.4) {
            darkHex = '#cbd5e1';
        }
    }
    
    darkRoot += `  ${varName}: ${darkHex};\n`;
});

lightRoot += '}\n\n';
darkRoot += '}\n\n';

console.log('Replacing hex values with CSS variables...');
let newCss = css;

// Sort by length descending to replace #ffffff before #fff
const sortedHex = Object.keys(colorVars).sort((a, b) => b.length - a.length);

sortedHex.forEach(hex => {
    const varName = colorVars[hex];
    const regex = new RegExp(hex + '(?![a-fA-F0-9])', 'gi');
    newCss = newCss.replace(regex, `var(${varName})`);
});

const finalCss = lightRoot + darkRoot + newCss;

fs.writeFileSync(cssPath, finalCss, 'utf8');
console.log('Successfully refactored style.css for Dark Mode!');

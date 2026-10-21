<?php
$cssPath = __DIR__ . '/admin/assets/css/style.css';

if (!file_exists($cssPath)) {
    die('style.css not found at ' . $cssPath);
}

$css = file_get_contents($cssPath);

// Prevent running multiple times
if (strpos($css, ':root[data-theme="dark"]') !== false) {
    die('style.css is already refactored.');
}

// Regex to find hex colors
preg_match_all('/#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})\b/', $css, $matches);
$uniqueColors = array_unique(array_map('strtolower', $matches[0]));

function hexToRgb($hex) {
    $hex = ltrim($hex, '#');
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
    }
    $colorVal = hexdec($hex);
    return [
        'r' => 0xFF & ($colorVal >> 0x10),
        'g' => 0xFF & ($colorVal >> 0x8),
        'b' => 0xFF & $colorVal
    ];
}

function getLuminance($r, $g, $b) {
    $a = [$r, $g, $b];
    foreach ($a as &$v) {
        $v /= 255;
        if ($v <= 0.03928) {
            $v = $v / 12.92;
        } else {
            $v = pow((($v + 0.055) / 1.055), 2.4);
        }
    }
    return $a[0] * 0.2126 + $a[1] * 0.7152 + $a[2] * 0.0722;
}

$colorVars = [];
$lightRoot = ":root, :root[data-theme=\"light\"] {\n";
$darkRoot = ":root[data-theme=\"dark\"] {\n";

$darkOverrides = [
    '#ffffff' => '#1e293b',
    '#fff' => '#1e293b',
    '#f8f9fa' => '#0f172a',
    '#fafbfe' => '#0f172a',
    '#f4f4f4' => '#0f172a',
    '#000000' => '#ffffff',
    '#000' => '#ffffff',
    '#212529' => '#f1f5f9',
    '#212b36' => '#f1f5f9',
    '#333333' => '#e2e8f0',
    '#333' => '#e2e8f0',
    '#e9ecef' => '#334155',
    '#eef1f6' => '#334155',
    '#edf1f6' => '#334155',
    '#1b2850' => '#1b2850',
];

$keepColors = [
    '#ff9f43', '#fe820e', '#ff8c2e', 
    '#8bc34a', '#28c76f', 
    '#e53935', '#ea5455', '#ff3b30'
];

$counter = 1;
foreach ($uniqueColors as $hex) {
    $varName = '--theme-color-' . $counter++;
    $colorVars[$hex] = $varName;
    
    $lightRoot .= "  $varName: $hex;\n";
    
    $darkHex = $hex;
    if (in_array($hex, $keepColors)) {
        $darkHex = $hex;
    } elseif (array_key_exists($hex, $darkOverrides)) {
        $darkHex = $darkOverrides[$hex];
    } else {
        $rgb = hexToRgb($hex);
        $lum = getLuminance($rgb['r'], $rgb['g'], $rgb['b']);
        
        if ($lum > 0.8) {
            $darkHex = '#1a2234'; 
        } elseif ($lum > 0.5) {
            $darkHex = '#2a3447';
        } elseif ($lum < 0.1) {
            $darkHex = '#f8fafc';
        } elseif ($lum < 0.4) {
            $darkHex = '#cbd5e1';
        }
    }
    
    $darkRoot .= "  $varName: $darkHex;\n";
}

$lightRoot .= "}\n\n";
$darkRoot .= "}\n\n";

$newCss = $css;
// Sort by length to match longer hexes first
uksort($colorVars, function($a, $b) {
    return strlen($b) - strlen($a);
});

foreach ($colorVars as $hex => $varName) {
    $regex = '/' . preg_quote($hex, '/') . '(?![a-fA-F0-9])/i';
    $newCss = preg_replace($regex, "var($varName)", $newCss);
}

$finalCss = $lightRoot . $darkRoot . $newCss;
file_put_contents($cssPath, $finalCss);

echo "Successfully refactored style.css for Dark Mode!";
?>

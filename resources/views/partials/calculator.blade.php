<div class="floating-calculator-wrapper">
    <!-- The calculator UI (hidden by default) -->
    <div id="floating-calculator" class="floating-calculator card shadow-lg" style="display: none;">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center p-2">
            <h6 class="m-0 text-white"><i class="fas fa-calculator"></i> Calculator</h6>
            <button type="button" class="btn-close btn-close-white" style="font-size: 12px; margin: 0; padding: 0;" aria-label="Close" onclick="toggleCalculator()"></button>
        </div>
        <div class="card-body p-2">
            <input type="text" id="calc-display" class="form-control mb-2 text-end" disabled style="background:#fff; font-size:1.2rem; font-weight:bold; color: #333;">
            <div class="calc-grid">
                <button type="button" class="btn btn-secondary calc-btn" onclick="clearCalc()">C</button>
                <button type="button" class="btn btn-secondary calc-btn" onclick="appendCalc('%')">%</button>
                <button type="button" class="btn btn-secondary calc-btn" onclick="deleteChar()"><i class="fas fa-backspace"></i></button>
                <button type="button" class="btn btn-info calc-btn text-white" onclick="appendCalc('/')">/</button>

                <button type="button" class="btn btn-light calc-btn" onclick="appendCalc('7')">7</button>
                <button type="button" class="btn btn-light calc-btn" onclick="appendCalc('8')">8</button>
                <button type="button" class="btn btn-light calc-btn" onclick="appendCalc('9')">9</button>
                <button type="button" class="btn btn-info calc-btn text-white" onclick="appendCalc('*')">*</button>

                <button type="button" class="btn btn-light calc-btn" onclick="appendCalc('4')">4</button>
                <button type="button" class="btn btn-light calc-btn" onclick="appendCalc('5')">5</button>
                <button type="button" class="btn btn-light calc-btn" onclick="appendCalc('6')">6</button>
                <button type="button" class="btn btn-info calc-btn text-white" onclick="appendCalc('-')">-</button>

                <button type="button" class="btn btn-light calc-btn" onclick="appendCalc('1')">1</button>
                <button type="button" class="btn btn-light calc-btn" onclick="appendCalc('2')">2</button>
                <button type="button" class="btn btn-light calc-btn" onclick="appendCalc('3')">3</button>
                <button type="button" class="btn btn-info calc-btn text-white" onclick="appendCalc('+')">+</button>

                <button type="button" class="btn btn-light calc-btn btn-zero" onclick="appendCalc('0')">0</button>
                <button type="button" class="btn btn-light calc-btn" onclick="appendCalc('.')">.</button>
                <button type="button" class="btn btn-primary calc-btn btn-equal" onclick="calculateResult()">=</button>
            </div>
        </div>
    </div>

    <!-- The floating button -->
    <button type="button" class="btn btn-primary floating-calc-btn shadow" onclick="toggleCalculator()">
        <i class="fas fa-calculator"></i>
    </button>
</div>

<style>
.floating-calculator-wrapper {
    position: fixed;
    bottom: 20px;
    right: 20px;
    /* Above Bootstrap modals (~1055) so it stays usable on Print Barcode etc. */
    z-index: 11000;
}
.floating-calc-btn {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    font-size: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: absolute;
    bottom: 40px;
    right: 40px;
    transition: transform 0.2s;
}
.floating-calc-btn:hover {
    transform: scale(1.1);
}
.floating-calculator {
    position: absolute;
    bottom: 70px;
    right: 0;
    width: 250px;
    border-radius: 10px;
    overflow: hidden;
}
.calc-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 5px;
}
.calc-btn {
    height: 40px;
    font-size: 16px;
    font-weight: 600;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #ddd;
}
.calc-btn:active {
    transform: scale(0.95);
}
.btn-zero {
    grid-column: span 2;
}
@media (max-width: 767.98px) {
    .floating-calculator-wrapper {
        right: 0;
        bottom: 90px;
    }
    .floating-calc-btn {
        right: 0;
        bottom: 0;
        width: 28px;
        height: 44px;
        padding: 0 0 0 4px;
        border-radius: 12px 0 0 12px;
        font-size: 14px;
        opacity: .85;
        box-shadow: -2px 2px 8px rgba(0, 0, 0, .18) !important;
    }
    .floating-calc-btn:hover,
    .floating-calc-btn:active {
        transform: none;
        opacity: 1;
    }
    .floating-calculator {
        right: 8px;
        bottom: 52px;
        width: min(250px, calc(100vw - 16px));
    }
}
</style>

<script>
    function toggleCalculator() {
        let calc = document.getElementById('floating-calculator');
        if (calc.style.display === 'none') {
            clearCalc();
            calc.style.display = 'block';
        } else {
            calc.style.display = 'none';
        }
    }

    function appendCalc(val) {
        let calcDisplay = document.getElementById('calc-display');
        if (calcDisplay.value === 'Error') calcDisplay.value = '';
        calcDisplay.value += val;
    }

    function clearCalc() {
        document.getElementById('calc-display').value = '';
    }

    function deleteChar() {
        let calcDisplay = document.getElementById('calc-display');
        if (calcDisplay.value === 'Error') calcDisplay.value = '';
        calcDisplay.value = calcDisplay.value.slice(0, -1);
    }

    function calculateResult() {
        let calcDisplay = document.getElementById('calc-display');
        try {
            if(calcDisplay.value) {
                // Ensure only safe math characters before eval, now including %
                let safeVal = calcDisplay.value.replace(/[^-()\d/*+.%]/g, '');
                
                // Handle percentage for addition and subtraction: X + Y% -> X + (X * Y / 100)
                safeVal = safeVal.replace(/(\d+(?:\.\d+)?)([\+\-])(\d+(?:\.\d+)?)%/g, '($1$2($1*$3/100))');
                // Handle percentage for multiplication and division: X * Y% -> X * (Y / 100)
                safeVal = safeVal.replace(/([\*\/])(\d+(?:\.\d+)?)%/g, '$1($2/100)');
                // Handle any standalone percentage like 50% -> (50/100)
                safeVal = safeVal.replace(/(\d+(?:\.\d+)?)%/g, '($1/100)');

                calcDisplay.value = eval(safeVal);
                
                if(!isFinite(calcDisplay.value) || isNaN(calcDisplay.value)){
                   calcDisplay.value = 'Error';
                }
            }
        } catch (e) {
            calcDisplay.value = 'Error';
        }
    }
    
    // Add keyboard support
    document.addEventListener('keydown', function(event) {
        let calc = document.getElementById('floating-calculator');
        if (calc && calc.style.display === 'block') {
            const key = event.key;
            if (/^[0-9\/\*\-\+\.%]$/.test(key)) {
                appendCalc(key);
            } else if (key === 'Enter' || key === '=') {
                event.preventDefault();
                calculateResult();
            } else if (key === 'Backspace') {
                deleteChar();
            } else if (key === 'Escape') {
                toggleCalculator();
            } else if (key === 'c' || key === 'C') {
                clearCalc();
            }
        }
    });
</script>

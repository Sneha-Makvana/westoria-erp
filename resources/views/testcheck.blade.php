

@extends('layout.app')

@section('title', 'Test Barcode Print Modal')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Print / Edit Barcode Test Page</h4>
                <h6>Test and preview the complete Barcode Modal component</h6>
            </div>
        </div>

        {{-- Sample Demo Trigger Button for Testing --}}
        <div class="card">
            <div class="card-body">
                <h5>Demo Product Barcode Trigger</h5>
                <p class="text-muted">Click the button below to test opening the Print / Edit Barcode modal with mock product data:</p>
                
                <button type="button" 
                        class="btn btn-primary print-barcode-btn" 
                        data-id="1" 
                        data-name="GPG2304 CHITARMAY SHRI DURGASHAPATASHATI" 
                        data-name-hindi="चित्रमय श्री दुर्गासप्तशती"
                        data-barcode="GPG2304" 
                        data-price="450.00" 
                        data-mrp="450.00" 
                        data-batch-no="J26" 
                        data-label-ref="1115"
                        data-specific-qty="" 
                        data-label-size="35x25x3"
                        data-barcode-details='{"GPG2304":{"price":"450.00","mrp":"450.00","batch_no":"J26","label_reference":"1115","specific_qty":""}}'
                        data-selling-prices='[{"name":"Rate 1","price":"450.00","mrp":"450.00"},{"name":"Wholesale","price":"380.00","mrp":"450.00"}]'>
                    <i class="fas fa-print me-1"></i> Open Print / Edit Barcode Modal
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================================ --}}
{{-- 1. CSS STYLES --}}
{{-- ============================================================================ --}}
<style>
    /* ── The Sticker Label Format Preview ── */
    .label-box {
        padding: 2mm 1mm;
        border-radius: 10px;
        border: 1px solid #000;
        box-sizing: border-box;
        background: #fff;
        margin: 0 auto;
        overflow: hidden;
        height: 40mm;
        text-align: center;
        width: 100%;
        color: #000;
    }
    
    .company-name { font-size: 12px; font-weight: bold; }
    .address { font-size: 10px; font-weight: bold; margin-top: 1px; }
    .product-name { font-size: 10px; font-weight: bold; margin-top: 2px; text-transform: uppercase; }
    .label-code { font-size: 10px; font-weight: bold; margin-top: 2px; }
    .price-line { font-size: 11px; font-weight: bold; margin-top: 2px; }
    .price-line .mrp { font-size: 0.95em; font-weight: bold; }
    .price-line .rate { font-size: 1.05em; font-weight: bold; }
    
    .label-box table { width: 100%; border-collapse: collapse; margin-top: 2px; border: none; }
    .label-box td { vertical-align: middle; padding: 0; border: none; }
    
    .barcode-container { text-align: center; padding: 0 1mm; }
    .label-box .barcode-img {
        display: block !important;
        width: 100% !important;
        height: 22px !important;
        max-width: 100% !important;
        max-height: none !important;
        flex-shrink: 0;
    }
    .label-box.split-preview .barcode-img {
        height: 34px !important;
    }
    .unit-text { font-size: 14px; font-weight: bold; text-align: center; line-height: 1; }
    
    .batch-text { font-size: 9px; font-weight: bold; text-align: center; margin-top: 3px; word-wrap: break-word; }
    .ref-text { font-size: 9px; font-weight: bold; text-align: center; line-height: 1.1; margin-top: 3px; word-wrap: break-word; }

    /* Modal Form Inputs & Switches */
    #printBarcodeModal .form-check-input {
        width: 2.2em;
        height: 1.15em;
        cursor: pointer;
    }
    #printBarcodeModal .form-check-input:checked {
        background-color: #ff9f43;
        border-color: #ff9f43;
    }
    #printBarcodeModal .form-check-label {
        cursor: pointer;
        font-weight: 600;
    }

    #printBarcodeModal .pb-print-target {
        flex: 1 1 260px;
        min-width: 0;
    }
    #printBarcodeModal .pb-print-target select {
        flex: 1 1 auto;
        min-width: 0;
    }
    #printBarcodeModal #pbBtLanguageWrap {
        flex: 0 1 150px;
        min-width: 0;
    }

    @media (max-width: 767px) {
        #printBarcodeModal .modal-dialog {
            margin: 0.5rem;
            max-width: calc(100% - 1rem);
        }
        #printBarcodeModal .modal-body {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }
        #printBarcodeModal .pb-print-target,
        #printBarcodeModal #pbBtLanguageWrap,
        #printBarcodeModal .pb-print-actions {
            flex: 1 1 100%;
        }
        #printBarcodeModal .pb-print-actions .btn {
            flex: 1 1 0;
            padding-top: 8px;
            padding-bottom: 8px;
        }
    }

    /* Mobile Print Fullscreen Overlay */
    #mobilePrintOverlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 20000;
        background: #fff;
        flex-direction: column;
    }
    #mobilePrintOverlay.show {
        display: flex;
    }
    #mobilePrintOverlay .mobile-print-header {
        flex: 0 0 auto;
        padding: 12px 14px;
        background: #092C4C;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }
    #mobilePrintOverlay .mobile-print-header h6 {
        margin: 0;
        font-size: 15px;
        font-weight: 600;
    }
    #mobilePrintOverlay .mobile-print-help {
        flex: 0 0 auto;
        padding: 10px 14px;
        background: #e8f4fd;
        border-bottom: 1px solid #b6dff9;
        font-size: 13px;
        line-height: 1.45;
        color: #0c5460;
    }
    #mobilePrintOverlay .mobile-print-preview {
        flex: 1 1 auto;
        min-height: 0;
        background: #f8f9fa;
        overflow: hidden;
    }
    #mobilePrintFrame {
        width: 100%;
        height: 100%;
        border: 0;
        background: #fff;
    }
</style>

{{-- ============================================================================ --}}
{{-- 2. HTML MODAL MARKUP --}}
{{-- ============================================================================ --}}

<!-- Print Barcode / Edit Label Modal -->
<div class="modal fade" id="printBarcodeModal" tabindex="-1" aria-labelledby="printBarcodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printBarcodeModalLabel"><i class="fas fa-print text-primary me-2"></i> Print / Edit Barcode Info - <span id="pbProductName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <div id="pbMobilePrintHelp" class="alert alert-info mb-3 d-none">
                    <strong><i class="fas fa-mobile-alt me-1"></i> Print from phone</strong>
                    <p class="mb-1 small">Tap <strong>Print Barcode</strong>. A new tab opens — tap the orange <strong>Print / Select Printer</strong> button there.</p>
                    <p class="mb-0 small"><strong>Android:</strong> Install RawBT or your printer app, pair Bluetooth, then choose it in the print screen. If missing, use <strong>Open PDF</strong> → Share to printer app.</p>
                </div>
                <form id="printBarcodeForm">
                    <input type="hidden" id="pbProductId" name="product_id">
                    <input type="hidden" id="pbCurrentEditBarcode" name="current_edit_barcode">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Barcode No</label>
                            <input type="hidden" id="pbBarcode" name="barcode">
                            <div class="d-flex">
                                <input type="text" class="form-control" id="pbBarcode_input" placeholder="Enter Barcode (e.g. 11MH66)" oninput="this.value=this.value.replace(/[^A-Za-z0-9\-_.\/+]/g,'')">
                                <button class="btn btn-secondary ms-2 px-3" type="button" id="add_pbBarcode_btn" style="height: 38px;">Add</button>
                            </div>
                            <div id="pbBarcode_tags_container" class="d-flex flex-wrap gap-2 mt-2"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Label Size (mm)</label>
                            <select class="form-select" id="pbLabelSize" name="label_size">
                                <option value="35x25x3">35 x 25 mm, 3 columns, gap 0 (105 x 25 mm = 4.13 × 0.98 in)</option>
                                <option value="35x25x1">35 x 25 mm, 1 column</option>
                                <option value="38x25x2">38 x 25 x 2 (2 labels / row)</option>
                                <option value="25x50x2">25 x 50 x 2 (2 tall labels / row)</option>
                            </select>
                            <small class="text-muted">TVS LP46 stock: <strong>4.13 × 0.98 in</strong> = 3 × (35×25 mm) labels.</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Label Printer Type</label>
                            <select class="form-select" id="pbPrinter" name="printer">
                                <option value="tvs">TVS LP46 Dlite</option>
                                <option value="tsc">TSC TE244</option>
                            </select>
                            <small id="pbPrinterMobileHint" class="text-muted d-none">Choose layout for your label roll size.</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Barcode Template</label>
                            <select class="form-select" id="pbBarcodeTemplate" name="barcode_template">
                                <option value="classic">Default (Classic)</option>
                                <option value="split">New format (barcode left, price right)</option>
                            </select>
                            <small class="text-muted d-block mt-1">Selected template is saved as default for every product.</small>
                            <a href="{{ route('barcode.designer') }}" class="btn btn-outline-primary btn-sm mt-2 w-100" target="_blank" style="border-color: #ff9f43;">
                                <i class="fas fa-paint-brush"></i> Custom Designer
                            </a>
                            <!-- Keep hidden for backward compatibility in existing JS -->
                            <input type="hidden" id="pbLabelFormat" name="label_format" value="classic">
                        </div>
                        <div class="col-md-4 mb-3 d-none">
                            <label class="form-label">MRP</label>
                            <input type="number" step="0.01" class="form-control" id="pbMrp" name="mrp">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">MRP / Rate</label>
                            <select class="form-select" id="pbRateSelect">
                                <option value="">Select MRP &amp; Rate</option>
                            </select>
                            <input type="hidden" id="pbPrice" name="price">
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <label class="form-label mb-0" for="pbShowBatch">Batch No</label>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" id="pbShowBatch" checked>
                                </div>
                            </div>
                            <input type="text" class="form-control mt-1" id="pbBatchNo" name="batch_no">
                            <div class="form-text">Same batch for every product. Changing it updates all products.</div>
                        </div>
                       
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <label class="form-label mb-0" for="pbShowWholesale">Wholesale</label>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" id="pbShowWholesale" checked>
                                </div>
                            </div>
                            <input type="text" class="form-control mt-1" id="pbLabelRef" name="label_reference">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Other</label>
                            <input type="text" class="form-control" id="pbSpecificQty" name="specific_qty" >
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">No. of Copies</label>
                            <input type="number" class="form-control" id="pbPrintCopies" name="print_copies" value="1" min="1" max="200" step="1">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Print Barcodes</label>
                            <select class="form-select" id="pbPrintScope" name="print_scope">
                                <option value="selected">Selected barcode only</option>
                                <option value="all">All barcodes</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="pbIncludeTitle">Company Title</label>
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox" role="switch" id="pbIncludeTitle" checked>
                                <label class="form-check-label" for="pbIncludeTitle" id="pbIncludeTitleLabel">Include</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="pbHindiName">Product Name</label>
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox" role="switch" id="pbHindiName">
                                <label class="form-check-label" for="pbHindiName">Print in Hindi</label>
                            </div>
                            <div id="pbHindiNameEdit" class="mt-2" style="display:none;">
                                <input type="text" class="form-control" id="pbHindiNameInput" placeholder="Hindi product name" style="font-family: 'Nirmala UI', 'Noto Sans Devanagari', sans-serif;">
                                <small class="text-muted d-block" id="pbHindiNameStatus"></small>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3 d-flex flex-column align-items-start justify-content-start">
                            <div id="pbBarcodeImagesContainer" class="d-flex justify-content-start align-items-start"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-block" style="padding: 8px 10px;">
                <div class="pb-print-footer d-flex flex-wrap flex-md-nowrap align-items-center gap-2 w-100">
                    <div class="pb-print-target d-flex align-items-center gap-2">
                        <select class="form-select form-select-sm" id="pbPrintTarget" title="Print To">
                            <option value="browser">Print dialog (choose in browser)</option>
                        </select>
                        <button type="button" class="btn btn-outline-primary btn-sm text-nowrap" id="pbBtChooseBtn" title="Find a Bluetooth printer">
                            <i class="fab fa-bluetooth-b me-1"></i> Bluetooth
                        </button>
                    </div>
                    <div class="d-none" id="pbBtLanguageWrap">
                        <select class="form-select form-select-sm" id="pbBtLanguage" title="Bluetooth Printer Kind">
                            <option value="tspl">Label (TSPL)</option>
                            <option value="escpos">Receipt (ESC/POS)</option>
                        </select>
                    </div>
                    <div class="pb-print-actions d-flex gap-2 ms-md-auto">
                        <button type="button" class="btn btn-secondary btn-sm text-nowrap" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary btn-sm text-nowrap" id="saveAndPrintBarcodeBtn">Print Barcode</button>
                    </div>
                </div>
                <small id="pbBtStatus" class="d-block small text-muted"></small>
            </div>
        </div>
    </div>
</div>

<!-- Custom Barcode Settings Modal -->
<div class="modal fade" id="customBarcodeSettingsModal" tabindex="-1" aria-labelledby="customBarcodeSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="customBarcodeSettingsModalLabel">Custom Barcode</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 14px;">X</button>
            </div>
            <div class="modal-body px-4 pb-4 pt-3">
                <div class="row">
                    <!-- Settings Side -->
                    <div class="col-md-6 pe-md-4">
                        <h6 class="mb-3 fw-bold text-dark" style="font-size: 14px;">Label size</h6>
                        <div class="row mb-4">
                            <div class="col-6">
                                <label class="form-label text-muted small mb-1">Width</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" class="form-control" id="cbWidth" value="35" style="border-color: #eee; box-shadow: none;">
                                    <span class="input-group-text bg-white" style="border-color: #eee; color: #999;">mm</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted small mb-1">Height</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" class="form-control" id="cbHeight" value="25" style="border-color: #eee; box-shadow: none;">
                                    <span class="input-group-text bg-white" style="border-color: #eee; color: #999;">mm</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile full-screen print preview (same page — no new tab) -->
<div id="mobilePrintOverlay" aria-hidden="true">
    <div class="mobile-print-header">
        <h6><i class="fas fa-print me-2"></i>Print barcode labels</h6>
        <button type="button" class="btn btn-sm btn-light" id="mobilePrintCloseBtn">Close</button>
    </div>
    <div class="mobile-print-help">
        Stay on this page. Tap <strong>Select Printer &amp; Print</strong>, choose your Bluetooth / Wi‑Fi printer, then this screen closes automatically. Android: RawBT or your printer app works well. If print fails, tap <strong>Open PDF</strong> and share to the printer app.
    </div>
    <div class="mobile-print-preview">
        <iframe id="mobilePrintFrame" title="Barcode print preview"></iframe>
    </div>
    <div class="mobile-print-actions">
        <button type="button" class="btn btn-primary btn-print-main" id="mobilePrintNowBtn"><i class="fas fa-print me-1"></i> Select Printer &amp; Print</button>
        <a class="btn btn-dark" id="mobilePrintOpenPdfBtn" href="#" target="_blank" rel="noopener">Open PDF</a>
        <button type="button" class="btn btn-outline-secondary" id="mobilePrintCloseBtn2">Cancel</button>
    </div>
</div>
@endsection

{{-- ============================================================================ --}}
{{-- 3. JAVASCRIPT LOGIC & LIBRARIES --}}
{{-- ============================================================================ --}}
@push('js')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Open+Sans:wght@400;700&family=Montserrat:wght@400;700&family=Lato:wght@400;700&family=Oswald:wght@400;700&family=Poppins:wght@400;700&family=Noto+Sans:wght@400;700&family=Noto+Sans+Devanagari:wght@600;700&family=Roboto+Condensed:wght@400;700&family=PT+Sans:wght@400;700&family=Source+Sans+3:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.5/JsBarcode.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
    @include('partials.label-bluetooth-print')

    <script>
        $(function() {
            if (window.LabelBluetoothPrint && typeof window.LabelBluetoothPrint.init === 'function') {
                window.LabelBluetoothPrint.init({
                    select: '#pbPrintTarget',
                    button: '#pbBtChooseBtn',
                    status: '#pbBtStatus',
                    language: '#pbBtLanguage',
                    languageWrap: '#pbBtLanguageWrap'
                });
                $('#printBarcodeModal').on('show.bs.modal', function() {
                    window.LabelBluetoothPrint.refreshPrinters();
                });
            }
        });
    </script>

    <script>
        // Global barcode helpers — Fabric.js loadFromJSON callbacks need these outside click-handler scope
        window.barcodeModuleCache = window.barcodeModuleCache || {};
        window.countBarcodeModules = function(code) {
            var key = String(code || '0');
            if (window.barcodeModuleCache[key]) return window.barcodeModuleCache[key];
            if (typeof JsBarcode === 'undefined') return 80;
            var src = document.createElement('canvas');
            try {
                JsBarcode(src, key, {
                    format: 'CODE128',
                    displayValue: false,
                    margin: 0,
                    width: 1,
                    height: 10,
                    flat: true
                });
            } catch (e) {
                return 80;
            }
            var count = Math.max(20, src.width || 80);
            window.barcodeModuleCache[key] = count;
            return count;
        };

        window.layoutBarcodeDraw = function(code, density, boxWpx, maxWpx, pxPerMm, targetHpx) {
            pxPerMm = pxPerMm || 10;
            var dens = parseFloat(density);
            if (!isFinite(dens) || dens <= 0) dens = 1;
            dens = Math.max(0.5, Math.min(2, dens));
            var boxMm = Math.max(1, (boxWpx || 0) / pxPerMm);
            var heightMm = Math.max(1, (targetHpx || 40) / pxPerMm);
            var pngW = Math.max(8, Math.round(boxWpx || boxMm * pxPerMm));
            var pngH = Math.max(8, Math.round(targetHpx || heightMm * pxPerMm));
            var bars = window.countBarcodeModules(code);
            var quiet = 4;
            var symbols = bars + quiet * 2;
            var targetModulePx = Math.max(1, Math.round((2 * dens) * (pxPerMm / 8)));
            var modulePx = Math.floor(pngW / symbols);
            if (modulePx < 1) {
                quiet = 2;
                symbols = bars + quiet * 2;
                modulePx = Math.floor(pngW / symbols);
            }
            if (modulePx < 1) {
                quiet = 0;
                symbols = bars;
                modulePx = Math.max(1, Math.floor(pngW / Math.max(1, symbols)));
            }
            if (modulePx < targetModulePx && quiet > 0) {
                var lessQuiet = quiet === 4 ? 2 : 0;
                var symbols2 = bars + lessQuiet * 2;
                var module2 = Math.floor(pngW / symbols2);
                if (module2 > modulePx) {
                    quiet = lessQuiet;
                    symbols = symbols2;
                    modulePx = module2;
                }
            }
            if (modulePx > targetModulePx) modulePx = targetModulePx;
            if (modulePx < 1) modulePx = 1;
            return {
                modulePx: modulePx,
                quiet: quiet,
                pngW: pngW,
                heightPx: pngH,
                widthMm: boxMm,
                heightMm: heightMm,
                displayWidthPx: pngW,
                displayHeightPx: pngH,
                tooThin: ((pngW / Math.max(1, bars + 4)) / pxPerMm) < 0.2,
                grew: false,
                moduleMm: modulePx / pxPerMm
            };
        };

        window.densityToModuleWidth = function(density) {
            var dens = parseFloat(density);
            if (!isFinite(dens) || dens <= 0) dens = 1;
            dens = Math.max(0.5, Math.min(2, dens));
            return Math.max(2, Math.min(4, Math.round(dens * 2)));
        };

        window.densityToOccupancy = function() {
            return 1;
        };

        window.buildBarcodeSvgDataUrl = function(code, density, targetWpx, targetHpx, maxWpx, pxPerMm) {
            if (typeof JsBarcode === 'undefined') return { src: '', width: 0, height: 0, tooThin: true };
            var layout = window.layoutBarcodeDraw(code, density, targetWpx, maxWpx, pxPerMm || 10, targetHpx);
            try {
                var bitKey = String(code || '0');
                window.barcodeBitCache = window.barcodeBitCache || {};
                if (!window.barcodeBitCache[bitKey]) {
                    var bitSrc = document.createElement('canvas');
                    JsBarcode(bitSrc, bitKey, {
                        format: 'CODE128',
                        lineColor: '#000000',
                        background: '#ffffff',
                        displayValue: false,
                        margin: 0,
                        width: 1,
                        height: 8,
                        flat: true
                    });
                    var bitRow = bitSrc.getContext('2d').getImageData(0, 2, bitSrc.width, 1).data;
                    var bitStr = '';
                    for (var bi = 0; bi < bitSrc.width; bi++) {
                        bitStr += bitRow[bi * 4] < 128 ? '1' : '0';
                    }
                    window.barcodeBitCache[bitKey] = bitStr || '101';
                }
                var bits = window.barcodeBitCache[bitKey];
                var pngW = Math.max(8, layout.pngW || 8);
                var pngH = Math.max(8, layout.heightPx || 8);
                var quiet = 2;
                var slots = bits.length + quiet * 2;
                var out = document.createElement('canvas');
                out.width = pngW;
                out.height = pngH;
                var ctx = out.getContext('2d');
                ctx.imageSmoothingEnabled = false;
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, pngW, pngH);
                ctx.fillStyle = '#000000';
                var run = 0;
                function slotX(i) { return Math.round(i * pngW / slots); }
                for (var i = 0; i <= bits.length; i++) {
                    if (i < bits.length && bits.charAt(i) === '1') {
                        run++;
                    } else if (run) {
                        var x0 = slotX(quiet + i - run);
                        var x1 = slotX(quiet + i);
                        if (x1 <= x0) x1 = x0 + 1;
                        ctx.fillRect(x0, 0, x1 - x0, pngH);
                        run = 0;
                    }
                }
                return {
                    src: out.toDataURL('image/png'),
                    width: layout.displayWidthPx,
                    height: layout.displayHeightPx,
                    pngWidth: out.width,
                    pngHeight: out.height,
                    widthMm: layout.widthMm,
                    heightMm: layout.heightMm,
                    tooThin: layout.tooThin,
                    grew: layout.grew,
                    moduleMm: layout.moduleMm
                };
            } catch (e) {
                return { src: '', width: 0, height: 0, tooThin: true };
            }
        };

        function escapeHtmlAttr(str) {
            return String(str == null ? '' : str)
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        }

        function escapeHtmlText(str) {
            return String(str == null ? '' : str)
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        }
    </script>

    <script>
        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            window.globalBatchNo = window.globalBatchNo || '';

            // ===== Helper Functions =====
            function isMobileDevice() {
                return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
                    || (window.matchMedia && window.matchMedia('(max-width: 767px)').matches);
            }

            function finishBarcodePrintScript() {
                return 'function finishBarcodePrint(){' +
                    'try{if(window.parent&&window.parent!==window){window.parent.postMessage({type:"barcodePrintDone"},"*");return;}}catch(e){}' +
                    'try{window.close();}catch(e){}}';
            }

            function openPrintTab(preparingMessage) {
                var features = 'popup=yes,width=920,height=720,scrollbars=yes,resizable=yes';
                var w = window.open('about:blank', 'barcodePrintWin', features);
                if (!w) {
                    w = window.open('about:blank', '_blank');
                }
                if (!w) {
                    return null;
                }
                try { w.opener = null; } catch (e) {}
                w.document.open();
                w.document.write(
                    '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">' +
                    '<title>Print Barcode</title></head><body style="font-family:Arial,sans-serif;padding:24px;text-align:center;">' +
                    (preparingMessage || 'Preparing labels...') + '</body></html>'
                );
                w.document.close();
                try { w.focus(); } catch (e) {}
                return w;
            }

            function buildMobilePdfShell(pdfUrl) {
                var safeUrl = String(pdfUrl || '').replace(/"/g, '&quot;');
                return '<!DOCTYPE html><html><head><meta charset="utf-8">' +
                    '<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">' +
                    '<title>Print Barcode</title><style>' +
                    'body{margin:0;font-family:Arial,sans-serif;background:#f5f5f5;}' +
                    '.tip{padding:12px;background:#e8f4fd;font-size:13px;line-height:1.5;color:#0c5460;}' +
                    '.pdf-wrap{height:calc(100vh - 120px);background:#fff;}' +
                    'iframe,embed{width:100%;height:100%;border:0;}' +
                    '@media print{.tip{display:none!important}.pdf-wrap{height:auto}}' +
                    '</style></head><body>' +
                    '<div class="tip">Preview ready. Use <b>Select Printer &amp; Print</b> on the screen below, or Open PDF if needed.</div>' +
                    '<div class="pdf-wrap"><iframe id="pdfFrame" src="' + safeUrl + '"></iframe></div>' +
                    '<script>' + finishBarcodePrintScript() +
                    'function startPrint(){try{window.focus();window.print();}catch(e){}' +
                    'setTimeout(function(){if(document.hasFocus())finishBarcodePrint();},800);}' +
                    'window.startPrint=startPrint;' +
                    'window.addEventListener("afterprint",finishBarcodePrint);' +
                    'try{window.onafterprint=finishBarcodePrint;}catch(e){}' +
                    '<\/script></body></html>';
            }

            function prepareMobilePrintHtml(html) {
                if (!html) return html;
                var bridge =
                    '<style id="bd-mobile-embed-hide">' +
                    '.mobile-print-toolbar,.print-help{display:none!important;}' +
                    'body.mobile-print-mode{padding-bottom:0!important;}' +
                    '</style>' +
                    '<script>' + finishBarcodePrintScript() +
                    'window.addEventListener("afterprint",finishBarcodePrint);' +
                    'try{window.onafterprint=finishBarcodePrint;}catch(e){}' +
                    'if(typeof window.startPrint!=="function"){' +
                    'window.startPrint=function(){try{window.focus();window.print();}catch(e){}' +
                    'setTimeout(function(){if(document.hasFocus())finishBarcodePrint();},800);};}' +
                    '<\/script>';
                if (/<\/body>/i.test(html)) {
                    return html.replace(/<\/body>/i, bridge + '</body>');
                }
                return html + bridge;
            }

            function writePrintTab(w, html, pdfUrl) {
                if (!w || w.closed) return false;
                try {
                    w.document.open();
                    if (html) {
                        w.document.write(html);
                    } else if (pdfUrl) {
                        w.document.write(
                            '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Print Barcode</title>' +
                            '<style>html,body{margin:0;padding:0;height:100%;overflow:hidden;background:#fff;}' +
                            'embed{width:100%;height:100vh;display:block;border:0;}</style></head><body>' +
                            '<embed src="' + String(pdfUrl).replace(/"/g, '&quot;') + '" type="application/pdf" />' +
                            '<script>' + finishBarcodePrintScript() +
                            'var printed=false;' +
                            'function runPrint(){if(printed)return;printed=true;try{window.focus();window.print();}catch(e){}' +
                            'setTimeout(function(){if(document.hasFocus())finishBarcodePrint();},600);}' +
                            'window.addEventListener("afterprint",finishBarcodePrint);' +
                            'try{window.onafterprint=finishBarcodePrint;}catch(e){}' +
                            'window.addEventListener("load",function(){setTimeout(runPrint,700);});' +
                            'setTimeout(runPrint,1800);<\/script></body></html>'
                        );
                    } else {
                        w.document.write('<p style="font-family:Arial;padding:24px;">Nothing to print.</p>');
                    }
                    w.document.close();
                    w.focus();
                    return true;
                } catch (e) {
                    return false;
                }
            }

            function writeMobilePrintFrame(html, pdfUrl) {
                var frame = document.getElementById('mobilePrintFrame');
                if (!frame) return false;
                if (html) {
                    frame.removeAttribute('src');
                    var doc = frame.contentDocument || frame.contentWindow.document;
                    doc.open();
                    doc.write(prepareMobilePrintHtml(html));
                    doc.close();
                    return true;
                }
                if (pdfUrl) {
                    frame.removeAttribute('src');
                    var pdfDoc = frame.contentDocument || frame.contentWindow.document;
                    pdfDoc.open();
                    pdfDoc.write(buildMobilePdfShell(pdfUrl));
                    pdfDoc.close();
                    return true;
                }
                return false;
            }

            function showMobilePrintOverlay(html, pdfUrl) {
                if (!writeMobilePrintFrame(html || null, html ? null : pdfUrl)) {
                    Swal.fire('Error', 'Unable to open mobile print preview.', 'error');
                    return;
                }
                var frame = document.getElementById('mobilePrintFrame');
                var $openPdf = $('#mobilePrintOpenPdfBtn');
                if (frame) {
                    if (pdfUrl) {
                        frame.setAttribute('data-pdf-url', pdfUrl);
                        $openPdf.attr('href', pdfUrl).addClass('show');
                    } else {
                        frame.removeAttribute('data-pdf-url');
                        $openPdf.attr('href', '#').removeClass('show');
                    }
                }
                $('#mobilePrintOverlay').addClass('show').attr('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }

            function closeMobilePrintOverlay() {
                $('#mobilePrintOverlay').removeClass('show').attr('aria-hidden', 'true');
                document.body.style.overflow = '';
                $('#mobilePrintOpenPdfBtn').attr('href', '#').removeClass('show');
                var frame = document.getElementById('mobilePrintFrame');
                if (frame) {
                    frame.removeAttribute('data-pdf-url');
                    frame.removeAttribute('src');
                    try {
                        var doc = frame.contentDocument || frame.contentWindow.document;
                        doc.open();
                        doc.write('');
                        doc.close();
                    } catch (e) {}
                }
            }

            function runMobilePrintDialog() {
                var frame = document.getElementById('mobilePrintFrame');
                if (!frame) {
                    Swal.fire('Error', 'Print preview not ready.', 'error');
                    return;
                }
                try {
                    var frameWin = frame.contentWindow;
                    if (frameWin && typeof frameWin.startPrint === 'function') {
                        frameWin.startPrint();
                        return;
                    }
                    if (frameWin) {
                        frameWin.focus();
                        frameWin.print();
                        return;
                    }
                } catch (e) {}
                var pdfUrl = frame.getAttribute('data-pdf-url') || '';
                if (pdfUrl) {
                    window.open(pdfUrl, '_blank');
                    return;
                }
                Swal.fire('Print', 'Preview not ready. Close and try again.', 'warning');
            }

            window.addEventListener('message', function (event) {
                if (!event || !event.data || event.data.type !== 'barcodePrintDone') return;
                if ($('#mobilePrintOverlay').hasClass('show')) {
                    closeMobilePrintOverlay();
                }
            });

            $('#mobilePrintNowBtn').on('click', function() {
                runMobilePrintDialog();
            });
            $('#mobilePrintCloseBtn, #mobilePrintCloseBtn2').on('click', function() {
                closeMobilePrintOverlay();
            });
            $('#mobilePrintOpenPdfBtn').on('click', function (e) {
                var href = $(this).attr('href');
                if (!href || href === '#') {
                    e.preventDefault();
                }
            });

            // ===== Modal Open Trigger Handler =====
            $(document).on('click', '.print-barcode-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var $btn = $(this);
                $('#pbProductId').val($btn.data('id'));
                var englishProductName = $btn.attr('data-name') || 'Product';
                var savedHindiName = $btn.attr('data-name-hindi') || '';
                $('#pbProductName').text(englishProductName).attr('data-english', englishProductName).attr('data-hindi', savedHindiName);
                $('#pbHindiNameInput').val(savedHindiName);
                $('#pbHindiName').prop('checked', false);
                $('#pbHindiNameEdit').hide();
                $('#pbHindiNameStatus').text('');
                
                $('#pbBarcode').val($btn.data('barcode'));
                $('#pbPrice').val($btn.data('price'));
                $('#pbMrp').val($btn.data('mrp'));
                $('#pbBatchNo').val($btn.data('batch-no'));
                $('#pbShowWholesale').prop('checked', true);
                $('#pbShowBatch').prop('checked', true);
                $('#pbLabelRef').val($btn.data('label-ref'));
                $('#pbSpecificQty').val($btn.data('specific-qty'));
                let savedLabelSize = $btn.data('label-size') || '35x25x3';
                if (savedLabelSize === '35x25x1') {
                    savedLabelSize = '35x25x3';
                }
                $('#pbLabelSize').val(savedLabelSize);
                $('#pbPrintCopies').val(1);
                $('#pbPrintScope').val('selected');

                // Render Barcode
                let barcodeVal = String($btn.data('barcode') || '').trim();
                $('#pbBarcode').val(barcodeVal);
                
                let pbBarcodes = barcodeVal ? barcodeVal.split(',').map(b => b.trim()).filter(b => b) : [];
                
                let barcodeDetailsStr = $btn.attr('data-barcode-details') || '{}';
                try {
                    window.currentBarcodeDetails = JSON.parse(barcodeDetailsStr);
                    if (typeof window.currentBarcodeDetails === 'string') {
                        window.currentBarcodeDetails = JSON.parse(window.currentBarcodeDetails);
                    }
                } catch(e) {
                    window.currentBarcodeDetails = {};
                }

                try {
                    window.productSellingPrices = JSON.parse($btn.attr('data-selling-prices') || '[]');
                    if (!Array.isArray(window.productSellingPrices)) {
                        window.productSellingPrices = [];
                    }
                } catch (e) {
                    window.productSellingPrices = [];
                }

                // Default active barcode is the first one, or empty
                let activeBarcode = pbBarcodes.length > 0 ? pbBarcodes[0] : '';
                $('#pbCurrentEditBarcode').val(activeBarcode);

                function sellingRateLabel(rate, idx) {
                    let name = rate.name || ('Rate ' + (idx + 1));
                    let priceTxt = parseFloat(rate.price).toFixed(2);
                    if (rate.mrp !== undefined && rate.mrp !== null && rate.mrp !== '') {
                        return name + ' — MRP ' + parseFloat(rate.mrp).toFixed(2) + ' / Rate ' + priceTxt;
                    }
                    return name + ' — Rate ' + priceTxt;
                }

                function fillRateSelect(selectedPrice, selectedMrp) {
                    let rates = window.productSellingPrices || [];
                    let $sel = $('#pbRateSelect');
                    $sel.empty().append('<option value="">Select MRP &amp; Rate</option>');
                    rates.forEach(function(rate, idx) {
                        if (!rate || rate.price === '' || rate.price === null) return;
                        if (/wholesale/i.test(String(rate.name || ''))) return;
                        let priceAttr = parseFloat(rate.price).toFixed(2);
                        let mrpAttr = (rate.mrp !== undefined && rate.mrp !== null && rate.mrp !== '') ? parseFloat(rate.mrp).toFixed(2) : '';
                        $sel.append(`<option value="${idx}" data-price="${priceAttr}" data-mrp="${mrpAttr}" data-is-wholesale="0">${sellingRateLabel(rate, idx)}</option>`);
                    });
                    let matched = $();
                    if (selectedPrice !== undefined && selectedPrice !== null && selectedPrice !== '') {
                        let priceMatches = $sel.find('option').filter(function() {
                            let p = $(this).data('price');
                            return p !== undefined && p !== '' && Math.abs(parseFloat(p) - parseFloat(selectedPrice)) < 0.0001;
                        });
                        if (selectedMrp !== undefined && selectedMrp !== null && selectedMrp !== '') {
                            matched = priceMatches.filter(function() {
                                let m = $(this).data('mrp');
                                return m !== undefined && m !== '' && Math.abs(parseFloat(m) - parseFloat(selectedMrp)) < 0.0001;
                            }).first();
                        }
                        if (!matched.length) {
                            matched = priceMatches.first();
                        }
                    }
                    if (!matched.length) {
                        let productPrice = parseFloat($btn.data('price'));
                        if (Number.isFinite(productPrice)) {
                            matched = $sel.find('option').filter(function() {
                                let p = $(this).attr('data-price');
                                return p !== undefined && p !== '' && Math.abs(parseFloat(p) - productPrice) < 0.0001;
                            }).first();
                        }
                    }
                    if (matched.length) {
                        $sel.val(matched.val());
                    }
                }

                $('#pbRateSelect').off('change.rate').on('change.rate', function() {
                    saveCurrentBarcodeDetails();
                    let $opt = $(this).find('option:selected');
                    if ($opt.val() === '') {
                        $('#pbPrice').val('').trigger('input');
                        $('#pbMrp').val('').trigger('input');
                        saveCurrentBarcodeDetails();
                        updateBarcodePreview();
                        return;
                    }
                    let price = $opt.attr('data-price');
                    let mrp = $opt.attr('data-mrp');
                    let isWholesale = $opt.attr('data-is-wholesale') === '1';
                    if (price !== undefined && price !== '') {
                        $('#pbPrice').val(price).trigger('input');
                    }
                    $('#pbMrp').val(mrp !== undefined && mrp !== '' ? mrp : '').trigger('input');
                    if (isWholesale && price !== undefined && price !== '') {
                        var wholesaleVal = parseFloat(price);
                        $('#pbLabelRef').val(Number.isFinite(wholesaleVal) ? String(Math.round(wholesaleVal) === wholesaleVal ? Math.round(wholesaleVal) : wholesaleVal) : price).trigger('input');
                    }
                    saveCurrentBarcodeDetails();
                    updateBarcodePreview();
                });

                function populateBarcodeDetails(barcode) {
                    $('#pbCurrentEditBarcode').val(barcode);
                    let details = window.currentBarcodeDetails[barcode] || {};
                    let rateVal = details.price !== undefined ? details.price : $btn.data('price');
                    let mrpVal = details.mrp !== undefined ? details.mrp : $btn.data('mrp');
                    fillRateSelect(rateVal, mrpVal);

                    if ($('#pbRateSelect').val()) {
                        let $opt = $('#pbRateSelect option:selected');
                        let optPrice = $opt.attr('data-price');
                        let optMrp = $opt.attr('data-mrp');
                        $('#pbPrice').val(optPrice !== undefined && optPrice !== '' ? optPrice : (rateVal || '')).trigger('input');
                        $('#pbMrp').val(optMrp !== undefined && optMrp !== '' ? optMrp : (mrpVal || '')).trigger('input');
                    } else {
                        $('#pbPrice').val('').trigger('input');
                        $('#pbMrp').val('').trigger('input');
                    }

                    let wholesaleVal = resolveProductWholesaleValue($btn, details);
                    let batchRaw = details.batch_no !== undefined ? details.batch_no : $btn.data('batch-no');
                    $('#pbBatchNo').val(sanitizeBatchNoValue(batchRaw, wholesaleVal)).trigger('input');
                    if (String(window.globalBatchNo || '').trim() !== '') {
                        $('#pbBatchNo').val(window.globalBatchNo).trigger('input');
                    }

                    let labelRef = '';
                    if (details.label_reference !== undefined && details.label_reference !== null && String(details.label_reference).trim() !== '') {
                        labelRef = formatWholesaleInputValue(details.label_reference);
                    } else {
                        labelRef = wholesaleVal;
                    }
                    $('#pbLabelRef').val(labelRef).trigger('input');
                    $('#pbSpecificQty').val(details.specific_qty !== undefined ? details.specific_qty : $btn.data('specific-qty')).trigger('input');
                }

                if (activeBarcode) {
                    populateBarcodeDetails(activeBarcode);
                    $('#pbBarcode_input').val(activeBarcode);
                }
                
                function renderPbChips() {
                    let container = $('#pbBarcode_tags_container');
                    container.empty();
                    let currentEdit = $('#pbCurrentEditBarcode').val();
                    pbBarcodes.forEach(function(barcode) {
                        let isActive = (barcode === currentEdit) ? 'bg-primary text-white' : 'bg-light text-dark';
                        let chipHtml = `
                            <div class="barcode-chip ${isActive} pb-chip-selectable" data-barcode="${barcode}" style="cursor: pointer; padding: 5px 10px; border-radius: 4px; border: 1px solid #ccc;">
                                ${barcode} 
                                <span class="remove-chip pb-remove-chip ms-2" data-barcode="${barcode}">&times;</span>
                            </div>
                        `;
                        container.append(chipHtml);
                    });
                    $('#pbBarcode').val(pbBarcodes.join(',')).trigger('change');
                }

                renderPbChips();
                applyDefaultBarcodeTemplate();
                $('#printBarcodeModal').modal('show');
                
                $('#add_pbBarcode_btn').off('click').on('click', function(e) {
                    e.preventDefault();
                    let val = $('#pbBarcode_input').val().trim();
                    if (val !== "") {
                        if (!pbBarcodes.includes(val)) {
                            let currentDetails = {
                                price: $('#pbPrice').val(),
                                mrp: $('#pbMrp').val(),
                                batch_no: $('#pbBatchNo').val(),
                                label_reference: $('#pbLabelRef').val(),
                                specific_qty: $('#pbSpecificQty').val()
                            };
                            pbBarcodes.push(val);
                            saveCurrentBarcodeDetails();
                            window.currentBarcodeDetails[val] = currentDetails;
                            populateBarcodeDetails(val);
                            renderPbChips();
                        }
                        $('#pbBarcode_input').val('');
                    }
                });

                $('#pbBarcode_input').off('keypress').on('keypress', function(e) {
                    if (e.which === 13) {
                        e.preventDefault();
                        $('#add_pbBarcode_btn').click();
                    }
                });

                $(document).off('click', '.pb-remove-chip').on('click', '.pb-remove-chip', function(e) {
                    e.stopPropagation();
                    let barcodeToRemove = String($(this).data('barcode'));
                    pbBarcodes = pbBarcodes.filter(b => b !== barcodeToRemove);
                    if ($('#pbCurrentEditBarcode').val() === barcodeToRemove) {
                        $('#pbCurrentEditBarcode').val(pbBarcodes.length > 0 ? pbBarcodes[0] : '');
                        if (pbBarcodes.length > 0) populateBarcodeDetails(pbBarcodes[0]);
                    }
                    renderPbChips();
                });

                function parseLabelSizeParts(labelSize) {
                    var parts = String(labelSize || '35x25x3').split('x');
                    return {
                        width: parseFloat(parts[0]) || 35,
                        height: parseFloat(parts[1]) || 25,
                        cols: Math.max(1, parseInt(parts[2], 10) || 1)
                    };
                }

                window.pbHindiNameCache = window.pbHindiNameCache || {};

                function barcodeProductNameForPrint() {
                    var english = ($('#pbProductName').attr('data-english') || $('#pbProductName').text() || '').trim();
                    if (!$('#pbHindiName').is(':checked')) {
                        return english;
                    }
                    var hindi = ($('#pbHindiNameInput').val() || $('#pbProductName').attr('data-hindi') || '').trim();
                    return hindi || english;
                }
                window.barcodeProductNameForPrint = barcodeProductNameForPrint;

                function getCustomLabelPrintContext() {
                    var currentBarcode = ($('#pbCurrentEditBarcode').val() || $('#pbBarcode_input').val() || '').trim();
                    var englishName = ($('#pbProductName').attr('data-english') || $('#pbProductName').text() || '').trim();
                    var prodName = barcodeProductNameForPrint();
                    var hindiName = $('#pbHindiName').is(':checked') && /[\u0900-\u097F]/.test($('#pbProductName').attr('data-hindi') || '');
                    var bNo = barcodeBatchNoForPrint();
                    var lblRef = formatBarcodeWholesaleRef($('#pbLabelRef').val() || '');
                    var qty = ($('#pbSpecificQty').val() || '').trim();
                    var mrp = '';
                    var price = '';
                    var $rateOpt = $('#pbRateSelect').find('option:selected');
                    if ($rateOpt.length && $rateOpt.val() !== '') {
                        mrp = ($('#pbMrp').val() || $rateOpt.attr('data-mrp') || '').toString().trim();
                        price = ($('#pbPrice').val() || $rateOpt.attr('data-price') || '').toString().trim();
                    }
                    var blankPad = '______';
                    var mrpNum = (mrp !== '' && !isNaN(parseFloat(mrp))) ? parseFloat(mrp).toLocaleString('en-IN') : '';
                    var rateNum = (price !== '' && !isNaN(parseFloat(price))) ? parseFloat(price).toLocaleString('en-IN') : '';
                    var mrpTxt = 'MRP-' + (mrpNum || blankPad);
                    var rateTxt = 'RATE-' + (rateNum || blankPad);
                    var compName = 'POOJA SAMAN.IN';
                    var compAddr = 'DURG,RISALI,RAIPUR';
                    var extras = [qty, bNo, lblRef].filter(Boolean).join('  ');
                    var barcodeFooter = currentBarcode + (extras ? '  ' + extras : '');
                    return {
                        barcode: currentBarcode,
                        prodName: prodName,
                        batchNo: bNo,
                        labelRef: lblRef,
                        qty: qty,
                        mrp: mrpNum,
                        price: rateNum,
                        mrpTxt: mrpTxt,
                        rateTxt: rateTxt,
                        companyName: compName,
                        companyAddress: compAddr,
                        barcodeFooter: barcodeFooter,
                        includeTitle: $('#pbIncludeTitle').is(':checked'),
                        hindiName: hindiName
                    };
                }

                function getCustomTemplateFieldPresence(canvas) {
                    var presence = {
                        hasBarcodeText: false,
                        hasBatch: false,
                        hasWholesale: false,
                        hasQty: false,
                        hasBarcode: false
                    };
                    if (!canvas || typeof canvas.getObjects !== 'function') return presence;
                    canvas.getObjects().forEach(function(obj) {
                        if (!(obj.type === 'i-text' || obj.type === 'text' || obj.type === 'textbox')) return;
                        var vf = '';
                        if (obj.isVariable && obj.variableField) {
                            vf = String(obj.variableField).replace(/[{}@]/g, '').trim().toLowerCase();
                        } else {
                            var sample = String(obj.text || '');
                            var m = sample.match(/\{\{\s*([a-z_]+)\s*\}\}/i);
                            if (m) vf = m[1].toLowerCase();
                        }
                        if (vf === 'barcode_text') presence.hasBarcodeText = true;
                        else if (vf === 'batch_no') presence.hasBatch = true;
                        else if (vf === 'label_code' || vf === 'sku' || vf === 'wholesale') presence.hasWholesale = true;
                        else if (vf === 'qty' || vf === 'other') presence.hasQty = true;
                        else if (vf === 'barcode') presence.hasBarcode = true;
                    });
                    return presence;
                }

                function resolveBarcodeTextValue(ctx, presence) {
                    presence = presence || {};
                    if (presence.hasBatch || presence.hasWholesale || presence.hasQty) {
                        return ctx.barcode || '';
                    }
                    return ctx.barcodeFooter || ctx.barcode || '';
                }

                function applyCustomTemplateObjectText(obj, ctx, presence) {
                    if (!(obj.type === 'i-text' || obj.type === 'text' || obj.type === 'textbox')) return;
                    presence = presence || {};
                    var txt = obj.text || '';
                    var vf = '';
                    if (obj.isVariable && obj.variableField) {
                        vf = String(obj.variableField).replace(/[{}@]/g, '').trim().toLowerCase();
                        if (vf === 'product_name') txt = ctx.prodName;
                        else if (vf === 'company_name') txt = ctx.companyName;
                        else if (vf === 'company_address' || vf === 'address') txt = ctx.companyAddress;
                        else if (vf === 'barcode') txt = ctx.barcode;
                        else if (vf === 'barcode_text') txt = resolveBarcodeTextValue(ctx, presence);
                        else if (vf === 'mrp') txt = ctx.mrpTxt;
                        else if (vf === 'price' || vf === 'selling_price' || vf === 'rate') txt = ctx.rateTxt;
                        else if (vf === 'batch_no') txt = ctx.batchNo;
                        else if (vf === 'label_code' || vf === 'sku' || vf === 'wholesale') txt = ctx.labelRef;
                        else if (vf === 'qty' || vf === 'other') txt = ctx.qty;
                    } else {
                        if (obj.isPriceLine || (/\{\{\s*mrp\s*\}\}/i.test(txt) && /\{\{\s*(selling_price|price|rate)\s*\}\}/i.test(txt))) {
                            txt = ctx.mrpTxt + '  ' + ctx.rateTxt;
                            obj.set('isPriceLine', true);
                        } else {
                            txt = txt.replace(/\{\{\s*product_name\s*\}\}/gi, ctx.prodName);
                            txt = txt.replace(/\{\{\s*company_name\s*\}\}/gi, ctx.companyName);
                            txt = txt.replace(/\{\{\s*(company_address|address)\s*\}\}/gi, ctx.companyAddress);
                            txt = txt.replace(/\{\{\s*barcode_text\s*\}\}/gi, resolveBarcodeTextValue(ctx, presence));
                            txt = txt.replace(/\{\{\s*barcode\s*\}\}/gi, ctx.barcode);
                            txt = txt.replace(/\{\{\s*mrp\s*\}\}/gi, ctx.mrpTxt);
                            txt = txt.replace(/\{\{\s*(price|selling_price|rate)\s*\}\}/gi, ctx.rateTxt);
                            txt = txt.replace(/\{\{\s*batch_no\s*\}\}/gi, ctx.batchNo);
                            txt = txt.replace(/\{\{\s*(label_code|sku|wholesale)\s*\}\}/gi, ctx.labelRef);
                            txt = txt.replace(/\{\{\s*(qty|other)\s*\}\}/gi, ctx.qty);
                            if (/^\s*MRP\s*-?\s*$/i.test(txt)) { txt = ctx.mrpTxt; vf = 'mrp'; }
                            else if (/^\s*RATE\s*-?\s*$/i.test(txt)) { txt = ctx.rateTxt; vf = 'selling_price'; }
                            else if (/^\s*MRP\s*-/i.test(txt) && !/\d/.test(txt)) { txt = ctx.mrpTxt; vf = 'mrp'; }
                            else if (/^\s*RATE\s*-/i.test(txt) && !/\d/.test(txt)) { txt = ctx.rateTxt; vf = 'selling_price'; }
                            txt = txt.replace(/POOJA SAMAN\.IN\s+DURG,RISALI,RAIPUR/gi, ctx.companyName + ' ' + ctx.companyAddress);
                            txt = txt.replace(/Fablead Erp/gi, ctx.companyName);
                            txt = txt.replace(/12345454/g, ctx.barcode);
                        }
                    }
                    obj.set('text', txt);
                    if (obj.isPriceLine || vf === 'mrp' || vf === 'price' || vf === 'selling_price' || vf === 'rate' ||
                        /^MRP-/i.test(txt) || /^RATE-/i.test(txt)) {
                        obj.set({ splitByGrapheme: false, objectCaching: false });
                        try {
                            if (typeof obj.initDimensions === 'function') obj.initDimensions();
                            var needed = (obj.calcTextWidth ? obj.calcTextWidth() : 0) + 4;
                            if (needed > (obj.width || 0)) obj.set('width', Math.ceil(needed));
                        } catch (e) {}
                    }
                    if (obj.isVariable && obj.variableField) {
                        var key = String(obj.variableField).replace(/[{}@]/g, '').trim().toLowerCase();
                        if (ctx.hindiName && key === 'product_name') {
                            obj.set('fontFamily', '"Noto Sans Devanagari", "Nirmala UI", sans-serif');
                        } else if (key === 'company_name' || key === 'company_address' || key === 'address' || key === 'product_name') {
                            obj.set('text', String(obj.text || '').toUpperCase());
                        }
                    }
                }

                function fabricOriginToCss(ox, oy, obj) {
                    if (!obj || obj.centeredRotation !== false) return 'center center';
                    var x = 'left', y = 'top';
                    if (ox === 'center') x = 'center'; else if (ox === 'right') x = 'right';
                    if (oy === 'center') y = 'center'; else if (oy === 'bottom') y = 'bottom';
                    return x + ' ' + y;
                }

                function cssRotationInline(angleDeg, originCss) {
                    var a = parseFloat(angleDeg) || 0;
                    if (Math.abs(a) < 0.05) return '';
                    var o = originCss || 'center center';
                    return 'transform:rotate(' + a.toFixed(3) + 'deg);-webkit-transform:rotate(' + a.toFixed(3) + 'deg);transform-origin:' + o + ';-webkit-transform-origin:' + o + ';';
                }

                function getUnrotatedBoxTopLeft(obj, boxW, boxH) {
                    var angle = parseFloat(obj.angle) || 0;
                    if (Math.abs(angle) >= 0.5 && typeof obj.getCenterPoint === 'function') {
                        try {
                            var c = obj.getCenterPoint();
                            return { left: c.x - boxW / 2, top: c.y - boxH / 2 };
                        } catch (e) {}
                    }
                    if (obj.barcodeBoxWidth && obj.barcodeBoxHeight && obj.barcodeBoxLeft != null) {
                        return { left: obj.barcodeBoxLeft, top: obj.barcodeBoxTop != null ? obj.barcodeBoxTop : (obj.top || 0) };
                    }
                    var leftPx = obj.left || 0, topPx = obj.top || 0;
                    var ox = obj.originX || 'left', oy = obj.originY || 'top';
                    if (ox === 'center') leftPx -= boxW / 2; else if (ox === 'right') leftPx -= boxW;
                    if (oy === 'center') topPx -= boxH / 2; else if (oy === 'bottom') topPx -= boxH;
                    return { left: leftPx, top: topPx };
                }

                function fabricPrintTextPayload(obj) {
                    var raw = String(obj.text || '');
                    var wrap = obj.type === 'textbox';
                    var lines = null;
                    try {
                        if (obj._textLines && obj._textLines.length) {
                            lines = obj._textLines.map(function(line) { return Array.isArray(line) ? line.join('') : String(line); });
                            if (lines.length > 1) wrap = true;
                        }
                    } catch (e) {}
                    if (/\r?\n/.test(raw)) wrap = true;
                    var text = (lines && lines.length) ? lines.join('\n') : raw;
                    return { text: String(text || '').replace(/\s+$/g, ''), wrap: !!wrap, lineCount: (lines && lines.length) ? lines.length : (wrap && /\n/.test(text) ? text.split(/\n/).length : 1) };
                }

                function resolveTextGlyphBox(obj, boxLeftPx, boxWidthPx) {
                    var align = obj.textAlign || 'left';
                    var lines = 1;
                    try { if (obj._textLines && obj._textLines.length) lines = obj._textLines.length; } catch (e) {}
                    if (align === 'left' || lines > 1) return { leftPx: boxLeftPx, widthPx: boxWidthPx, align: align };
                    var textWidth = 0;
                    try { if (typeof obj.calcTextWidth === 'function') textWidth = obj.calcTextWidth(); } catch (e) {}
                    if (!textWidth || !isFinite(textWidth) || textWidth <= 0) return { leftPx: boxLeftPx, widthPx: boxWidthPx, align: align };
                    var offset = 0;
                    if (align === 'center') offset = (boxWidthPx - textWidth) / 2;
                    else if (align === 'right') offset = boxWidthPx - textWidth;
                    return { leftPx: boxLeftPx + offset, widthPx: textWidth, align: 'left' };
                }

                function wrapLabelWords(measure, source, maxWidth) {
                    var words = String(source || '').split(/\s+/).filter(Boolean);
                    if (!words.length) return [''];
                    var wrapped = [], current = '';
                    words.forEach(function(word) {
                        var trial = current ? (current + ' ' + word) : word;
                        if (measure.measureText(trial).width <= maxWidth || !current) {
                            current = trial;
                        } else {
                            wrapped.push(current);
                            current = word;
                        }
                    });
                    if (current) wrapped.push(current);
                    return wrapped;
                }

                function ellipsizeLabelLine(measure, line, maxWidth) {
                    var chars = Array.from(String(line || '').replace(/\s+$/, ''));
                    while (chars.length && measure.measureText(chars.join('') + '...').width > maxWidth) {
                        chars.pop();
                    }
                    return chars.join('').replace(/\s+$/, '') + '...';
                }

                function clipProductNameToTwoLines(obj) {
                    var field = '';
                    if (obj.isVariable && obj.variableField) {
                        field = String(obj.variableField).replace(/[{}@]/g, '').trim().toLowerCase();
                    }
                    if (field !== 'product_name') return;
                    var fontSize = Math.max(8, parseFloat(obj.fontSize) || 18);
                    var family = obj.fontFamily || 'Arial, Helvetica, sans-serif';
                    var weight = (obj.fontWeight === 'normal' || obj.fontWeight === '400') ? 'normal' : 'bold';
                    var style = obj.fontStyle === 'italic' ? 'italic ' : '';
                    var measure = document.createElement('canvas').getContext('2d');
                    measure.font = style + weight + ' ' + fontSize + 'px ' + family;
                    var maxWidth = Math.max(8, (obj.width || 1) * (obj.scaleX || 1) - 4);
                    var lines = [];
                    String(obj.text || '').split(/\r?\n/).forEach(function(part) {
                        wrapLabelWords(measure, part, maxWidth).forEach(function(line) { lines.push(line); });
                    });
                    if (lines.length <= 2) return;
                    lines = [lines[0], ellipsizeLabelLine(measure, lines[1], maxWidth)];
                    obj.set('text', lines.join('\n'));
                    try { if (typeof obj.initDimensions === 'function') obj.initDimensions(); } catch (e) {}
                }

                function hardenCustomLabelTextObjects(canvas, heightMm) {
                    canvas.getObjects().forEach(function(obj) {
                        if (!(obj.type === 'i-text' || obj.type === 'text' || obj.type === 'textbox')) return;
                        var textVal = String(obj.text || '');
                        var vf = '';
                        if (obj.isVariable && obj.variableField) {
                            vf = String(obj.variableField).replace(/[{}@]/g, '').trim().toLowerCase();
                        }
                        var sx = obj.scaleX || 1, sy = obj.scaleY || 1;
                        var fontSize = parseFloat(obj.fontSize) || 18;
                        var width = parseFloat(obj.width) || 100;
                        if (Math.abs(sx - 1) > 0.01 || Math.abs(sy - 1) > 0.01) {
                            fontSize = Math.max(6, Math.round(fontSize * sy));
                            width = Math.max(20, width * sx);
                        }
                        if (vf === 'company_name' || vf === 'company_address' || vf === 'address' || vf === 'product_name') {
                            textVal = textVal.toUpperCase();
                        }
                        var isPrice = !!obj.isPriceLine || vf === 'mrp' || vf === 'price' || vf === 'selling_price' || vf === 'rate' ||
                            /^MRP-/i.test(textVal) || /^RATE-/i.test(textVal);

                        obj.set({
                            text: textVal,
                            fontFamily: obj.fontFamily || 'Arial, Helvetica, sans-serif',
                            fontWeight: 'bold',
                            fontStyle: obj.fontStyle === 'italic' ? 'italic' : 'normal',
                            fontSize: fontSize,
                            width: width,
                            textAlign: obj.textAlign || 'left',
                            fill: '#000000',
                            stroke: null,
                            strokeWidth: 0,
                            opacity: 1,
                            objectCaching: false,
                            charSpacing: 0,
                            lineHeight: obj.lineHeight || 1,
                            linethrough: false,
                            underline: false,
                            splitByGrapheme: false,
                            scaleX: 1,
                            scaleY: 1
                        });

                        try {
                            if (typeof obj.initDimensions === 'function') obj.initDimensions();
                            if (isPrice && typeof obj.calcTextWidth === 'function') {
                                var needed = obj.calcTextWidth() + 8;
                                if (needed > (obj.width || 0)) obj.set('width', Math.ceil(needed));
                            }
                        } catch (e) {}

                        clipProductNameToTwoLines(obj);
                        if (typeof obj.setCoords === 'function') obj.setCoords();
                    });
                }

                function renderCustomTemplateLayers(tpl, opts) {
                    return new Promise(function(resolve, reject) {
                        if (!tpl || !tpl.design_json || !window.fabric) {
                            reject(new Error('Custom template is not ready'));
                            return;
                        }
                        opts = opts || {};
                        var ctx = getCustomLabelPrintContext();
                        if (!ctx.barcode) {
                            reject(new Error('Barcode number is missing'));
                            return;
                        }
                        var designWidthMm = parseFloat(tpl.width_mm) || 35;
                        var designHeightMm = parseFloat(tpl.height_mm) || 25;
                        var widthMm = parseFloat(opts.printWidthMm) || designWidthMm;
                        var heightMm = parseFloat(opts.printHeightMm) || designHeightMm;
                        var designScale = 10;
                        var sx = widthMm / designWidthMm;
                        var sy = heightMm / designHeightMm;
                        var designWidthPx = Math.round(designWidthMm * designScale);
                        var designHeightPx = Math.round(designHeightMm * designScale);
                        var holder = document.createElement('canvas');
                        holder.width = designWidthPx;
                        holder.height = designHeightPx;
                        holder.style.position = 'fixed';
                        holder.style.left = '-10000px';
                        holder.style.top = '0';
                        document.body.appendChild(holder);

                        var canvas = new fabric.StaticCanvas(holder, {
                            width: designWidthPx,
                            height: designHeightPx,
                            backgroundColor: '#ffffff',
                            renderOnAddRemove: false,
                            enableRetinaScaling: false,
                            imageSmoothingEnabled: true
                        });

                        function rasterizeLabelTextImage(obj) {
                            var text = String(obj.text || '');
                            var fontSize = Math.max(8, parseFloat(obj.fontSize) || 18);
                            var family = obj.fontFamily || 'Arial, Helvetica, sans-serif';
                            var weight = (obj.fontWeight === 'normal' || obj.fontWeight === '400') ? 'normal' : 'bold';
                            var style = obj.fontStyle === 'italic' ? 'italic ' : '';
                            var scale = 4;
                            var measure = document.createElement('canvas').getContext('2d');
                            var font = style + weight + ' ' + (fontSize * scale) + 'px ' + family;
                            measure.font = font;
                            var pad = Math.ceil(fontSize * scale * 0.15);
                            var lineH = Math.ceil(fontSize * scale * 1.2);
                            var field = obj.isVariable && obj.variableField ? String(obj.variableField).replace(/[{}@]/g, '').trim().toLowerCase() : '';
                            var isPrice = !!obj.isPriceLine || field === 'mrp' || field === 'price' || field === 'selling_price' || field === 'rate' || /^MRP-/i.test(text) || /^RATE-/i.test(text);
                            var boxW = Math.max(1, (obj.width || 1) * (obj.scaleX || 1));
                            var maxWidth = Math.max(8, boxW * scale - pad * 2);
                            var shouldWrap = !isPrice && (obj.type === 'textbox' || field === 'product_name');

                            function wrapLine(source) {
                                var words = String(source || '').split(/\s+/).filter(Boolean);
                                if (!words.length) return [''];
                                var wrapped = [], current = '';
                                words.forEach(function(word) {
                                    var trial = current ? (current + ' ' + word) : word;
                                    if (measure.measureText(trial).width <= maxWidth || !current) {
                                        current = trial;
                                    } else {
                                        wrapped.push(current);
                                        current = word;
                                    }
                                });
                                if (current) wrapped.push(current);
                                return wrapped;
                            }

                            var lines = [];
                            if (shouldWrap && obj._textLines && obj._textLines.length > 1) {
                                obj._textLines.forEach(function(line) { lines.push(Array.isArray(line) ? line.join('') : String(line)); });
                            } else {
                                text.split(/\r?\n/).forEach(function(source) {
                                    if (shouldWrap) wrapLine(source).forEach(function(line) { lines.push(line); });
                                    else lines.push(source);
                                });
                            }
                            if (!lines.length) lines = [''];
                            if (field === 'product_name' && lines.length > 2) {
                                lines = [lines[0], ellipsizeLabelLine(measure, lines[1], maxWidth)];
                            }

                            var maxLine = 0;
                            lines.forEach(function(line) { maxLine = Math.max(maxLine, measure.measureText(line || ' ').width); });
                            if (shouldWrap) maxLine = Math.min(maxLine, maxWidth);

                            var board = document.createElement('canvas');
                            board.width = Math.max(1, Math.ceil(maxLine) + pad * 2);
                            board.height = Math.max(1, lineH * lines.length + pad);
                            var c = board.getContext('2d');
                            c.font = font;
                            c.fillStyle = '#000000';
                            c.textBaseline = 'middle';
                            lines.forEach(function(line, i) {
                                c.fillText(line, pad, pad / 2 + lineH * i + lineH / 2);
                            });
                            return {
                                src: board.toDataURL('image/png'),
                                widthPx: board.width / scale,
                                heightPx: board.height / scale,
                                wrapped: lines.length > 1
                            };
                        }

                        function cleanup() {
                            try { canvas.dispose(); } catch (e) {}
                            if (holder && holder.parentNode) holder.parentNode.removeChild(holder);
                        }

                        var design = tpl.design_json;
                        if (typeof design === 'string') {
                            try { design = JSON.parse(design); } catch (e) { design = tpl.design_json; }
                        }

                        canvas.loadFromJSON(design, function() {
                            var presence = getCustomTemplateFieldPresence(canvas);
                            canvas.getObjects().forEach(function(obj) {
                                applyCustomTemplateObjectText(obj, ctx, presence);
                                if (!ctx.includeTitle && (obj.type === 'i-text' || obj.type === 'text' || obj.type === 'textbox')) {
                                    var vf = obj.isVariable ? String(obj.variableField || '').replace(/[{}]/g, '').trim() : '';
                                    var raw = String(obj.text || '');
                                    if (vf === 'company_name' || vf === 'company_address' || vf === 'address' ||
                                        /POOJA SAMAN\.IN/i.test(raw) || /DURG,RISALI,RAIPUR/i.test(raw)) {
                                        obj.set('text', '');
                                        obj.set('visible', false);
                                    }
                                }
                            });
                            hardenCustomLabelTextObjects(canvas, designHeightMm);

                            var elements = [];
                            canvas.getObjects().forEach(function(obj) {
                                if (obj.visible === false) return;
                                var boxW = Math.max(1, (obj.width || 1) * (obj.scaleX || 1));
                                var boxH = Math.max(1, (obj.height || 1) * (obj.scaleY || 1));
                                if (obj.isBarcode && obj.barcodeBoxWidth && obj.barcodeBoxHeight) {
                                    boxW = obj.barcodeBoxWidth;
                                    boxH = obj.barcodeBoxHeight;
                                }
                                var tl = getUnrotatedBoxTopLeft(obj, boxW, boxH);
                                var leftPx = tl.left;
                                var topPx = tl.top;
                                var ox = obj.originX || 'left';
                                var oy = obj.originY || 'top';
                                var angle = parseFloat(obj.angle) || 0;
                                var originCss = fabricOriginToCss(ox, oy, obj);

                                if (obj.isBarcode) {
                                    var leftMm = (leftPx / designScale) * sx;
                                    var topMm = (topPx / designScale) * sy;
                                    var wMm = (boxW / designScale) * sx;
                                    var hMm = (boxH / designScale) * sy;
                                    var density = parseFloat(obj.barcodeDensity);
                                    if (!isFinite(density) || density <= 0) density = 1;
                                    var printPxPerMm = 8;
                                    var boxPxW = Math.max(8, Math.round(wMm * printPxPerMm));
                                    var boxPxH = Math.max(8, Math.round(hMm * printPxPerMm));
                                    var drawn = window.buildBarcodeSvgDataUrl(ctx.barcode, density, boxPxW, boxPxH, boxPxW, printPxPerMm);
                                    var src = drawn && drawn.src ? drawn.src : '';
                                    if (!src) return;
                                    elements.push({
                                        type: 'barcode',
                                        leftMm: leftMm,
                                        topMm: topMm,
                                        widthMm: Math.max(1, wMm),
                                        heightMm: Math.max(2, hMm),
                                        angle: angle,
                                        transformOrigin: originCss,
                                        src: src,
                                        bits: (window.barcodeBitCache || {})[String(ctx.barcode || '0')] || ''
                                    });
                                    return;
                                }

                                if (obj.type === 'i-text' || obj.type === 'text' || obj.type === 'textbox') {
                                    var payload = fabricPrintTextPayload(obj);
                                    var textVal = payload.text;
                                    if (!textVal) return;
                                    var fontSizePx = parseFloat(obj.fontSize) || 18;
                                    var fontSizeMm = (fontSizePx / designScale) * ((sx + sy) / 2);
                                    var useLeft = leftPx;
                                    var useW = boxW;
                                    var useAlign = obj.textAlign || 'left';
                                    if (!payload.wrap && Math.abs(angle) < 0.5) {
                                        var glyph = resolveTextGlyphBox(obj, leftPx, boxW);
                                        useLeft = glyph.leftPx;
                                        useW = glyph.widthPx;
                                        useAlign = glyph.align;
                                    }
                                    var minH = fontSizeMm * 1.2 * Math.max(1, payload.lineCount);
                                    var outWmm = Math.max(2, (useW / designScale) * sx);
                                    var outHmm = Math.max(minH, (boxH / designScale) * sy);
                                    var outLeftMm = (useLeft / designScale) * sx;
                                    var outTopMm = (topPx / designScale) * sy;

                                    var textImg = null;
                                    try { textImg = rasterizeLabelTextImage(obj); } catch (eRaster) { textImg = null; }

                                    if (textImg && textImg.src) {
                                        var textWmm = (textImg.widthPx / designScale) * sx;
                                        var textHmm = (textImg.heightPx / designScale) * sy;
                                        var boxWmm = (boxW / designScale) * sx;
                                        var boxLeftMm = (leftPx / designScale) * sx;
                                        var align = obj.textAlign || 'left';
                                        var textLeftMm = boxLeftMm;
                                        if (align === 'center') textLeftMm += Math.max(0, (boxWmm - textWmm) / 2);
                                        else if (align === 'right') textLeftMm += Math.max(0, boxWmm - textWmm);
                                        elements.push({
                                            type: 'image',
                                            textImage: true,
                                            leftMm: textLeftMm,
                                            topMm: outTopMm,
                                            widthMm: textWmm,
                                            heightMm: textHmm,
                                            angle: angle,
                                            transformOrigin: originCss,
                                            src: textImg.src
                                        });
                                    } else {
                                        elements.push({
                                            type: 'text',
                                            text: textVal,
                                            leftMm: outLeftMm,
                                            topMm: outTopMm,
                                            widthMm: outWmm,
                                            heightMm: outHmm,
                                            fontSizeMm: fontSizeMm,
                                            fontFamily: obj.fontFamily || 'Arial, Helvetica, sans-serif',
                                            fontWeight: (obj.fontWeight === 'normal' || obj.fontWeight === '400') ? 'normal' : 'bold',
                                            fontStyle: obj.fontStyle === 'italic' ? 'italic' : 'normal',
                                            textAlign: useAlign,
                                            wrap: payload.wrap,
                                            angle: angle,
                                            transformOrigin: originCss
                                        });
                                    }
                                }
                            });

                            cleanup();
                            resolve({
                                elements: elements,
                                printWidthMm: widthMm,
                                printHeightMm: heightMm
                            });
                        });
                    });
                }

                function buildCustomLabelPrintHtml(dataUrlOrLayers, widthMm, heightMm, cols, copies, autoPrint) {
                    cols = Math.max(1, parseInt(cols, 10) || 1);
                    copies = Math.max(1, parseInt(copies, 10) || 1);
                    widthMm = parseFloat(widthMm) || 35;
                    heightMm = parseFloat(heightMm) || 25;
                    autoPrint = autoPrint !== false;
                    var pageWidthMm = widthMm * cols;
                    var pageHeightMm = heightMm;
                    var isLayers = dataUrlOrLayers && typeof dataUrlOrLayers === 'object' && Array.isArray(dataUrlOrLayers.elements);
                    var layers = isLayers ? dataUrlOrLayers : null;
                    var dataUrl = isLayers ? '' : dataUrlOrLayers;
                    var orient = pageWidthMm > pageHeightMm ? 'Landscape' : 'Portrait';

                    function mmToPt(mm) { return (parseFloat(mm) || 0) * 2.834645669; }

                    function renderCellInner() {
                        if (layers) {
                            var html = '<div class="label-abs">';
                            layers.elements.forEach(function(el) {
                                if (el.type === 'text') {
                                    var pt = Math.max(5, Math.round(mmToPt(el.fontSizeMm)));
                                    var wrapClass = el.wrap ? ' wrap' : '';
                                    var wrapCss = el.wrap
                                        ? 'white-space:pre-wrap;word-break:break-word;overflow:visible;height:' + el.heightMm.toFixed(3) + 'mm;'
                                        : 'white-space:nowrap;';
                                    var textHtml = escapeHtmlText(el.text).replace(/\r\n|\n|\r/g, '<br>');
                                    html += '<div class="lbl-txt' + wrapClass + '" style="' +
                                        'left:' + el.leftMm.toFixed(3) + 'mm;' +
                                        'top:' + el.topMm.toFixed(3) + 'mm;' +
                                        'width:' + el.widthMm.toFixed(3) + 'mm;' +
                                        'font-size:' + pt + 'pt;' +
                                        'font-family:Arial, Helvetica, sans-serif;' +
                                        'font-weight:800;' +
                                        'font-style:' + (el.fontStyle || 'normal') + ';' +
                                        'text-align:' + (el.textAlign || 'left') + ';' +
                                        wrapCss +
                                        cssRotationInline(el.angle, el.transformOrigin) +
                                        '">' + textHtml + '</div>';
                                } else if (el.type === 'barcode' || el.type === 'image') {
                                    html += '<div class="lbl-bc' + (el.textImage ? ' lbl-text' : '') + '" style="' +
                                        'left:' + el.leftMm.toFixed(3) + 'mm;' +
                                        'top:' + el.topMm.toFixed(3) + 'mm;' +
                                        'width:' + el.widthMm.toFixed(3) + 'mm;' +
                                        'height:' + el.heightMm.toFixed(3) + 'mm;' +
                                        cssRotationInline(el.angle, el.transformOrigin) +
                                        '"><img src="' + escapeHtmlAttr(el.src) + '"' +
                                        (el.bits ? ' data-bits="' + escapeHtmlAttr(el.bits) + '" data-quiet="2"' : '') +
                                        ' alt="label"></div>';
                                }
                            });
                            html += '</div>';
                            return html;
                        }
                        return '<img class="lbl-img" src="' + escapeHtmlAttr(dataUrl) + '" alt="label">';
                    }

                    var cellInner = renderCellInner();
                    var items = [];
                    for (var i = 0; i < copies; i++) {
                        items.push(cellInner);
                    }

                    var html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Print Barcode</title><style>' +
                        '@page { size: ' + pageWidthMm + 'mm ' + pageHeightMm + 'mm; margin: 0; }' +
                        '* { box-sizing: border-box; margin: 0; padding: 0; border: 0 !important; outline: 0 !important;' +
                        ' box-shadow: none !important; -webkit-font-smoothing: none; -moz-osx-font-smoothing: unset;' +
                        ' font-smooth: never; text-rendering: geometricPrecision; }' +
                        'html, body { margin: 0; padding: 0; background: #fff; width: ' + pageWidthMm + 'mm; max-width: ' + pageWidthMm + 'mm;' +
                        ' color: #000; font-family: Arial, Helvetica, sans-serif; overflow: hidden; }' +
                        '.print-help { font-family: Arial, Helvetica, sans-serif; background: #fff3cd; padding: 10px 14px;' +
                        ' font-size: 13px; line-height: 1.45; -webkit-font-smoothing: auto; max-width: ' + pageWidthMm + 'mm;' +
                        ' width: ' + pageWidthMm + 'mm; box-sizing: border-box; overflow: hidden; }' +
                        '.print-help button { background: #ff9f43; color: #fff; border: 0; border-radius: 4px;' +
                        ' padding: 6px 12px; margin-top: 6px; cursor: pointer; }' +
                        'table.page { width: ' + pageWidthMm + 'mm; height: ' + pageHeightMm + 'mm; border-collapse: collapse;' +
                        ' table-layout: fixed; page-break-after: always; break-after: page; page-break-inside: avoid; break-inside: avoid; }' +
                        'table.page:last-child { page-break-after: auto; break-after: auto; }' +
                        'table.page td.col { width: ' + widthMm + 'mm; height: ' + heightMm + 'mm; vertical-align: top;' +
                        ' overflow: hidden; position: relative; background: #fff; color: #000; padding: 0; }' +
                        '.label-abs { position: absolute; left: 0; top: 0; width: ' + widthMm + 'mm; height: ' + heightMm + 'mm; overflow: hidden; }' +
                        '.lbl-txt { position: absolute; overflow: visible; line-height: 1.15;' +
                        ' color: #000 !important; letter-spacing: 0.03em; font-weight: 800;' +
                        ' -webkit-text-stroke: 0.28px #000; text-shadow: 0.2px 0 0 #000, -0.2px 0 0 #000; }' +
                        '.lbl-txt.wrap { white-space: pre-wrap; word-break: break-word; }' +
                        '.lbl-bc { position: absolute; overflow: hidden; background: #fff; }' +
                        '.lbl-bc.lbl-text { overflow: visible; background: transparent; }' +
                        '.lbl-bc.lbl-text img { object-fit: contain; object-position: left center; image-rendering: auto; }' +
                        '.lbl-bc img { display: block; width: 100%; height: 100%; object-fit: fill;' +
                        ' shape-rendering: crispEdges; image-rendering: pixelated; image-rendering: crisp-edges;' +
                        ' -ms-interpolation-mode: nearest-neighbor;' +
                        ' -webkit-print-color-adjust: exact; print-color-adjust: exact; }' +
                        '.lbl-img { width: 100%; height: 100%; display: block; object-fit: fill; object-position: center top;' +
                        ' -webkit-print-color-adjust: exact; print-color-adjust: exact; }' +
                        '@media print {' +
                        ' .print-help { display: none !important; height: 0 !important; overflow: hidden !important; padding: 0 !important; margin: 0 !important; }' +
                        ' html, body { width: ' + pageWidthMm + 'mm !important; max-width: ' + pageWidthMm + 'mm !important;' +
                        ' margin: 0 !important; padding: 0 !important; background: #fff; }' +
                        ' * { -webkit-print-color-adjust: exact; print-color-adjust: exact; color: #000 !important; }' +
                        '}' +
                        '</style></head><body>' +
                        '<div class="print-help">' +
                        '<strong>Select your label printer</strong> (not Save as PDF / Microsoft Print to PDF).<br>' +
                        'If it is missing, click <strong>Print using system dialog (Ctrl+Shift+P)</strong>.<br>' +
                        'Paper: <strong>' + pageWidthMm + ' x ' + pageHeightMm + ' mm</strong> &nbsp;|&nbsp; ' +
                        'Layout: <strong>' + widthMm + ' x ' + heightMm + ' mm, ' + cols + ' columns, gap 0</strong> &nbsp;|&nbsp; ' +
                        'Orientation: <strong>' + orient + '</strong> &nbsp;|&nbsp; ' +
                        'Scale: <strong>100% / Actual size</strong> &nbsp;|&nbsp; Auto-rotate: <strong>Off</strong><br>' +
                        '<button type="button" onclick="startPrint()">Print now</button></div>';

                    for (var i = 0; i < items.length; i += cols) {
                        html += '<table class="page"><tr>';
                        for (var j = 0; j < cols; j++) {
                            if (i + j < items.length) {
                                html += '<td class="col">' + items[i + j] + '</td>';
                            } else {
                                html += '<td class="col"></td>';
                            }
                        }
                        html += '</tr></table>';
                    }

                    html += '<script>' +
                        '(function(){var closed=false;' +
                        'function finishBarcodePrint(){if(closed)return;closed=true;' +
                        'try{if(window.parent&&window.parent!==window){window.parent.postMessage({type:"barcodePrintDone"},"*");return;}}catch(e){}' +
                        'try{window.close();}catch(e){}}' +
                        'function startPrint(){try{window.focus();window.print();}catch(e){}' +
                        'setTimeout(function(){if(document.hasFocus())finishBarcodePrint();},600);}' +
                        'window.startPrint=startPrint;' +
                        'window.addEventListener("afterprint",finishBarcodePrint);' +
                        'try{window.onafterprint=finishBarcodePrint;}catch(e){}' +
                        (autoPrint
                            ? 'window.addEventListener("load",function(){setTimeout(startPrint,350);});'
                            : '') +
                        '})();' +
                        '<\/script></body></html>';
                    return html;
                }

                window.parseLabelSizeParts = parseLabelSizeParts;
                window.getCustomLabelPrintContext = getCustomLabelPrintContext;
                window.getCustomTemplateFieldPresence = getCustomTemplateFieldPresence;
                window.applyCustomTemplateObjectText = applyCustomTemplateObjectText;
                window.hardenCustomLabelTextObjects = hardenCustomLabelTextObjects;
                window.renderCustomTemplateLayers = renderCustomTemplateLayers;
                window.buildCustomLabelPrintHtml = buildCustomLabelPrintHtml;

                function saveCurrentBarcodeDetails() {
                    let currentEdit = $('#pbCurrentEditBarcode').val();
                    let inputValue = $('#pbBarcode_input').val().trim();

                    if (currentEdit) {
                        if (inputValue && inputValue !== currentEdit && !pbBarcodes.includes(inputValue)) {
                            let index = pbBarcodes.indexOf(currentEdit);
                            if (index !== -1) {
                                pbBarcodes[index] = inputValue;
                                $('#pbBarcode').val(pbBarcodes.join(','));
                                if (window.currentBarcodeDetails && window.currentBarcodeDetails[currentEdit]) {
                                    window.currentBarcodeDetails[inputValue] = window.currentBarcodeDetails[currentEdit];
                                    delete window.currentBarcodeDetails[currentEdit];
                                }
                                currentEdit = inputValue;
                                $('#pbCurrentEditBarcode').val(currentEdit);
                            }
                        }

                        let previousDetails = window.currentBarcodeDetails[currentEdit] || {};
                        window.currentBarcodeDetails[currentEdit] = {
                            price: $('#pbPrice').val(),
                            mrp: $('#pbMrp').val(),
                            batch_no: $('#pbBatchNo').val(),
                            label_reference: $('#pbLabelRef').val(),
                            specific_qty: $('#pbSpecificQty').val()
                        };
                        if (previousDetails.label_code) {
                            window.currentBarcodeDetails[currentEdit].label_code = previousDetails.label_code;
                        }
                    }
                }

                $(document).off('click', '.pb-chip-selectable').on('click', '.pb-chip-selectable', function() {
                    saveCurrentBarcodeDetails();
                    let barcode = String($(this).data('barcode'));
                    populateBarcodeDetails(barcode);
                    $('#pbBarcode_input').val(barcode);
                    renderPbChips();
                });

                $('#pbSpecificQty').off('input').on('input', function() {
                    saveCurrentBarcodeDetails();
                    renderPbChips();
                });

                $('#pbHindiNameInput').off('input').on('input', function() {
                    $('#pbProductName').attr('data-hindi', ($(this).val() || '').trim());
                    if ($('#pbHindiName').is(':checked')) {
                        updateBarcodePreview();
                    }
                });
                $('#pbHindiNameInput').off('blur').on('blur', storeEditedHindiName);

                // ===== Print Barcode Button Submit Handler =====
                $('#saveAndPrintBarcodeBtn').off('click').on('click', function() {
                    if ($('#pbHindiName').is(':checked')) {
                        storeEditedHindiName();
                    }
                    var authToken = localStorage.getItem("authToken");
                    var productId = $('#pbProductId').val();
                    var printCopies = Math.max(1, parseInt($('#pbPrintCopies').val(), 10) || 1);
                    var printScope = $('#pbPrintScope').val() || 'selected';
                    var selectedBarcode = ($('#pbCurrentEditBarcode').val() || $('#pbBarcode_input').val() || '').trim();
                    var printPrinter = ($('#pbPrinter').val() || localStorage.getItem('barcodePrinter') || 'tvs');
                    var printLabelSize = $('#pbLabelSize').val() || '35x25x3';
                    if (printLabelSize === '35x25x1') {
                        printLabelSize = '35x25x3';
                    }
                    var printLabelFormat = syncBarcodeTemplateFormat();
                    if (printLabelFormat !== 'split') {
                        printLabelFormat = 'classic';
                    }
                    $('#pbLabelFormat').val(printLabelFormat);
                    var includeTitle = $('#pbIncludeTitle').is(':checked') ? '1' : '0';
                    localStorage.setItem('barcodePrinter', printPrinter);
                    try {
                        localStorage.setItem('barcodeLabelFormat', ($('#pbBarcodeTemplate').val() || 'classic').toString());
                    } catch (e) {}
                    localStorage.setItem('barcodeIncludeTitle', includeTitle);
                    var isMobile = isMobileDevice();
                    var btTarget = window.LabelBluetoothPrint ? window.LabelBluetoothPrint.target() : '';
                    var printWindow = (isMobile || btTarget) ? null : openPrintTab('Preparing labels...');

                    function runBluetoothPrint(job) {
                        Swal.fire({
                            title: 'Printing...',
                            text: 'Sending labels to the printer',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });
                        job().then(function() {
                            Swal.close();
                            $('#printBarcodeModal').modal('hide');
                        }).catch(function(err) {
                            Swal.fire('Print failed', (err && err.message) ? err.message : 'Could not print the labels', 'error');
                        });
                    }

                    saveCurrentBarcodeDetails();

                    var formData = new FormData($('#printBarcodeForm')[0]);
                    if (!$('#pbRateSelect').val()) {
                        formData.delete('price');
                        formData.delete('mrp');
                    }
                    if (window.currentBarcodeDetails) {
                        formData.append('all_barcode_details', JSON.stringify(window.currentBarcodeDetails));
                    }

                    Swal.fire({
                        title: 'Printing...',
                        text: isMobile ? 'Preparing labels for your phone' : 'Opening print dialog',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    function triggerPdfPrint(url, html, printer, labelFormat) {
                        Swal.close();
                        $('#printBarcodeModal').modal('hide');

                        if (isMobileDevice()) {
                            if (printWindow && !printWindow.closed) {
                                try { printWindow.close(); } catch (e) {}
                            }
                            if (html) {
                                showMobilePrintOverlay(html, url || null);
                            } else if (url) {
                                showMobilePrintOverlay(null, url);
                            } else {
                                Swal.fire('Error', 'Failed to prepare labels for printing.', 'error');
                            }
                            return;
                        }

                        if (writePrintTab(printWindow, html, url)) {
                            return;
                        }

                        var w = openPrintTab('Preparing labels...');
                        if (writePrintTab(w, html, url)) {
                            return;
                        }

                        Swal.fire('Error', 'Failed to open print window. Please allow popups and try again.', 'error');
                    }

                    $.ajax({
                        url: '/api/update-product-barcode-info',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'Authorization': 'Bearer ' + authToken
                        },
                        success: function(response) {
                            if (!response.status) {
                                if (printWindow && !printWindow.closed) {
                                    printWindow.close();
                                }
                                Swal.fire('Error', response.message || 'Failed to update barcode info', 'error');
                                return;
                            }

                            var typedBatch = ($('#pbBatchNo').val() || '').trim();
                            if (typeof rememberSharedBatchOnButtons === 'function') {
                                rememberSharedBatchOnButtons(typedBatch);
                            }
                            var typedWholesale = $('#pbLabelRef').val();
                            var activeCode = ($('#pbCurrentEditBarcode').val() || '').trim();
                            $('.print-barcode-btn[data-id="' + productId + '"]').each(function() {
                                var $printBtn = $(this);
                                $printBtn.attr('data-batch-no', typedBatch);
                                $printBtn.data('batch-no', typedBatch);
                                $printBtn.attr('data-label-ref', typedWholesale);
                                $printBtn.data('label-ref', typedWholesale);
                                try {
                                    var details = JSON.parse($printBtn.attr('data-barcode-details') || '{}');
                                    if (typeof details === 'string') details = JSON.parse(details);
                                    if (!details || typeof details !== 'object') details = {};
                                    if (activeCode) {
                                        details[activeCode] = details[activeCode] || {};
                                        details[activeCode].batch_no = typedBatch;
                                        details[activeCode].label_reference = typedWholesale;
                                    }
                                    $printBtn.attr('data-barcode-details', JSON.stringify(details));
                                } catch (e) {}
                            });
                            
                            let templateVal = $('#pbBarcodeTemplate').val();
                            if (templateVal && templateVal.startsWith('custom_')) {
                                let tplId = templateVal.replace('custom_', '');
                                let tpl = window.customBarcodeTemplates ? window.customBarcodeTemplates[tplId] : null;

                                if (tpl && tpl.design_json) {
                                    var sizeParts = (typeof window.parseLabelSizeParts === 'function')
                                        ? window.parseLabelSizeParts(printLabelSize)
                                        : { width: 35, height: 25, cols: 3 };
                                    var cols = sizeParts.cols;
                                    var printW = sizeParts.width || parseFloat(tpl.width_mm) || 35;
                                    var printH = sizeParts.height || parseFloat(tpl.height_mm) || 25;
                                    window.renderCustomTemplateLayers(tpl, {
                                        printWidthMm: printW,
                                        printHeightMm: printH
                                    }).then(function(layers) {
                                        if (btTarget) {
                                            var btW = layers.printWidthMm || printW;
                                            var btH = layers.printHeightMm || printH;
                                            var btHtml = window.buildCustomLabelPrintHtml(layers, btW, btH, cols, printCopies, false);
                                            runBluetoothPrint(function() {
                                                return window.LabelBluetoothPrint.printHtml(btHtml, btW * cols, btH);
                                            });
                                            return;
                                        }
                                        var html = window.buildCustomLabelPrintHtml(
                                            layers,
                                            layers.printWidthMm || printW,
                                            layers.printHeightMm || printH,
                                            cols,
                                            printCopies,
                                            !isMobile
                                        );
                                        triggerPdfPrint('', html, printPrinter, 'custom');
                                    }).catch(function(err) {
                                        if (printWindow && !printWindow.closed) {
                                            printWindow.close();
                                        }
                                        Swal.fire('Error', (err && err.message) ? err.message : 'Failed to prepare custom label print', 'error');
                                    });
                                    return;
                                }
                            }

                            $.ajax({
                                url: '/api/product-barcode-pdf/' + productId
                                    + '?copies=' + encodeURIComponent(printCopies)
                                    + '&printer=' + encodeURIComponent(printPrinter)
                                    + '&label_size=' + encodeURIComponent(printLabelSize)
                                    + '&label_format=' + encodeURIComponent(printLabelFormat)
                                    + '&include_title=' + encodeURIComponent(includeTitle)
                                    + '&mobile=' + (isMobile ? '1' : '0')
                                    + '&print_wholesale_label=' + (shouldShowWholesaleOnBarcode() ? '1' : '0')
                                    + '&print_batch_label=' + ($('#pbShowBatch').is(':checked') ? '1' : '0')
                                    + '&print_name=' + encodeURIComponent((typeof window.barcodeProductNameForPrint === 'function') ? window.barcodeProductNameForPrint() : '')
                                    + '&omit_mrp_rate=' + (!$('#pbRateSelect').val() ? '1' : '0')
                                    + '&price=' + encodeURIComponent($('#pbRateSelect').val() ? ($('#pbPrice').val() || '') : '')
                                    + '&mrp=' + encodeURIComponent($('#pbRateSelect').val() ? ($('#pbMrp').val() || '') : '')
                                    + (printScope === 'selected' && selectedBarcode ? '&barcode=' + encodeURIComponent(selectedBarcode) : '')
                                    + (btTarget ? '&bt=1' : ''),
                                type: 'GET',
                                headers: {
                                    "Authorization": "Bearer " + authToken,
                                },
                                success: function(pdfResponse) {
                                    if (btTarget) {
                                        var rows = (pdfResponse && pdfResponse.row_images) || [];
                                        if (!pdfResponse.status || (!pdfResponse.print_html && !rows.length)) {
                                            Swal.fire('Error', 'This barcode layout cannot be printed directly. Choose Print dialog instead.', 'error');
                                            return;
                                        }
                                        runBluetoothPrint(function() {
                                            if (pdfResponse.print_html) {
                                                return window.LabelBluetoothPrint.printHtml(
                                                    pdfResponse.print_html,
                                                    pdfResponse.page_width_mm,
                                                    pdfResponse.page_height_mm
                                                );
                                            }
                                            return window.LabelBluetoothPrint.printRows(
                                                rows,
                                                pdfResponse.page_width_mm,
                                                pdfResponse.page_height_mm
                                            );
                                        });
                                        return;
                                    }
                                    if (pdfResponse.status && (pdfResponse.print_html || pdfResponse.file_url)) {
                                        triggerPdfPrint(
                                            pdfResponse.file_url,
                                            pdfResponse.print_html,
                                            printPrinter,
                                            printLabelFormat
                                        );
                                    } else {
                                        if (printWindow && !printWindow.closed) {
                                            printWindow.close();
                                        }
                                        Swal.fire('Error', 'Failed to generate PDF', 'error');
                                    }
                                },
                                error: function(xhr) {
                                    if (printWindow && !printWindow.closed) {
                                        printWindow.close();
                                    }
                                    var message = (xhr.responseJSON && xhr.responseJSON.message)
                                        ? xhr.responseJSON.message
                                        : 'Failed to generate PDF';
                                    Swal.fire('Error', message, 'error');
                                }
                            });
                        },
                        error: function(xhr) {
                            if (printWindow && !printWindow.closed) {
                                printWindow.close();
                            }
                            let errMsg = 'An error occurred';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errMsg = xhr.responseJSON.message;
                            }
                            Swal.fire('Error', errMsg, 'error');
                        }
                    });
                });
            });

            // ===== Field & Value Parsing Helpers =====
            function parseBarcodeWholesaleValue(raw) {
                raw = String(raw || '').trim();
                if (!raw) return null;
                let numeric = parseFloat(raw.replace(/[^\d.]/g, ''));
                return Number.isFinite(numeric) && numeric > 0 ? numeric : null;
            }

            function formatLabelAmountForBarcode(numeric) {
                let n = Math.round(numeric * 100) / 100;
                if (Math.abs(n - Math.round(n)) < 0.001) {
                    return String(Math.round(n));
                }
                return n.toFixed(2).replace(/\.?0+$/, '');
            }

            function formatWholesaleInputValue(val) {
                let parsed = parseBarcodeWholesaleValue(val);
                if (parsed === null) return '';
                if (Math.round(parsed) === parsed) return String(Math.round(parsed));
                return String(parsed);
            }

            function wholesaleBarcodeSuffix(val) {
                return formatBarcodeWholesaleRef(val);
            }

            function sanitizeBatchNoValue(batchVal, wholesaleVal) {
                batchVal = String(batchVal || '').trim();
                if (!batchVal) return '';
                let suffix = wholesaleBarcodeSuffix(wholesaleVal);
                if (suffix && batchVal.endsWith(' ' + suffix)) {
                    return batchVal.slice(0, -(suffix.length + 1)).trim();
                }
                let plain = formatWholesaleInputValue(wholesaleVal);
                if (plain && batchVal.endsWith(' ' + plain)) {
                    return batchVal.slice(0, -(plain.length + 1)).trim();
                }
                let mergedSuffix = batchVal.match(/^(.*)\s+(11[\d.]+)$/);
                if (mergedSuffix && mergedSuffix[1]) {
                    let base = mergedSuffix[1].trim();
                    let suffixPart = mergedSuffix[2];
                    if (!wholesaleVal || suffix === suffixPart) {
                        return base;
                    }
                }
                return batchVal;
            }

            function resolveProductWholesaleValue($btn, details) {
                let sources = [
                    $btn.data('wholesale-price'),
                    $btn.attr('data-wholesale-price'),
                    details && details.label_reference,
                    $btn.attr('data-label-ref')
                ];
                for (let i = 0; i < sources.length; i++) {
                    let formatted = formatWholesaleInputValue(sources[i]);
                    if (formatted) return formatted;
                }
                let rates = window.productSellingPrices || [];
                for (let j = 0; j < rates.length; j++) {
                    if (/wholesale/i.test(String(rates[j].name || ''))) {
                        let formattedRate = formatWholesaleInputValue(rates[j].price);
                        if (formattedRate) return formattedRate;
                    }
                }
                return '';
            }

            function isWholesaleRateSelected() {
                let $opt = $('#pbRateSelect option:selected');
                return $opt.length > 0 && $opt.val() !== '' && $opt.attr('data-is-wholesale') === '1';
            }

            function hasWholesalePrice() {
                return parseBarcodeWholesaleValue($('#pbLabelRef').val()) !== null;
            }

            function shouldShowWholesaleOnBarcode() {
                return $('#pbShowWholesale').is(':checked') && hasWholesalePrice();
            }

            function barcodeBatchNoForPrint() {
                if (!$('#pbShowBatch').is(':checked')) return '';
                return ($('#pbBatchNo').val() || '').trim();
            }

            function formatBarcodeWholesaleRef(val) {
                if (!shouldShowWholesaleOnBarcode()) return '';
                val = String(val || '').trim();
                if (!val) return '';
                let parsed = parseBarcodeWholesaleValue(val);
                if (parsed === null) return '';
                let amount = formatLabelAmountForBarcode(parsed);
                return '11' + amount;
            }

            function syncBarcodeTemplateFormat() {
                var templateVal = ($('#pbBarcodeTemplate').val() || 'classic').toString();
                if (templateVal === 'split' || templateVal === 'classic') {
                    $('#pbLabelFormat').val(templateVal);
                } else if (templateVal.indexOf('custom_') === 0) {
                    $('#pbLabelFormat').val('classic');
                }
                try {
                    localStorage.setItem('barcodeLabelFormat', templateVal);
                } catch (e) {}
                if (templateVal.indexOf('custom_') === 0) {
                    var tplId = templateVal.replace('custom_', '');
                    var tpl = window.customBarcodeTemplates ? window.customBarcodeTemplates[tplId] : null;
                    var printer = ($('#pbPrinter').val() || 'tvs').toString().toLowerCase();
                    var cur = ($('#pbLabelSize').val() || '').toString();
                    var cols = 3;
                    var curParts = cur.split('x');
                    if (curParts.length >= 3) {
                        cols = parseInt(curParts[2], 10) || 3;
                    }
                    var w = 35;
                    var h = 25;
                    if (tpl) {
                        var tw = Math.round(parseFloat(tpl.width_mm) || 35);
                        var th = Math.round(parseFloat(tpl.height_mm) || 25);
                        if (tw >= 28 && tw <= 38 && th >= 24 && th <= 26) {
                            w = 35;
                            h = 25;
                            if (printer === 'tvs') cols = 3;
                        } else {
                            w = tw;
                            h = th;
                        }
                    } else if (printer === 'tvs') {
                        cols = 3;
                    }
                    var value = w + 'x' + h + 'x' + cols;
                    var text = w + ' x ' + h + ' mm, ' + cols + ' columns'
                        + (printer === 'tvs' && w === 35 && h === 25 && cols === 3
                            ? ' (TVS stock 4.13×0.98 in)'
                            : ' (template)');
                    if ($('#pbLabelSize option[value="' + value + '"]').length === 0) {
                        $('#pbLabelSize').append(new Option(text, value));
                    }
                    $('#pbLabelSize').val(value);
                }
                return $('#pbLabelFormat').val() || 'classic';
            }

            // ===== Live Barcode Preview Update =====
            function updateBarcodePreview() {
                let currentBarcode = $('#pbCurrentEditBarcode').val() || $('#pbBarcode_input').val();
                $('#pbBarcodeImagesContainer').empty();
                
                if (currentBarcode) {
                    let templateVal = $('#pbBarcodeTemplate').val();
                    syncBarcodeTemplateFormat();
                    if (templateVal && templateVal.startsWith('custom_')) {
                        let tplId = templateVal.replace('custom_', '');
                        let tpl = window.customBarcodeTemplates ? window.customBarcodeTemplates[tplId] : null;
                        
                        if (tpl && tpl.design_json) {
                            let widthMm = parseFloat(tpl.width_mm);
                            let heightMm = parseFloat(tpl.height_mm);
                            let displayScale = 3.78;
                            let designScale = 10;
                            
                            let displayWidthPx = Math.round(widthMm * displayScale);
                            let displayHeightPx = Math.round(heightMm * displayScale);
                            let designWidthPx = Math.round(widthMm * designScale);
                            let designHeightPx = Math.round(heightMm * designScale);
                            
                            let html = `<div class="label-box" style="margin: 0 auto; width: ${displayWidthPx}px; height: ${displayHeightPx}px; max-width: 100%; border: 1px solid #ccc; position: relative; background: #fff; overflow: hidden; aspect-ratio: ${widthMm} / ${heightMm};">
                                <div style="position: absolute; left: 0; top: 0; width: ${designWidthPx}px; height: ${designHeightPx}px; transform: scale(${displayScale/designScale}); transform-origin: top left;">
                                    <canvas id="custom_preview_canvas"></canvas>
                                </div>
                            </div>`;
                            $('#pbBarcodeImagesContainer').append(html);
                            
                            if (window.fabric) {
                                let canvas = new fabric.StaticCanvas('custom_preview_canvas', {
                                    width: designWidthPx,
                                    height: designHeightPx,
                                    backgroundColor: '#ffffff',
                                    enableRetinaScaling: false,
                                    imageSmoothingEnabled: false
                                });
                                
                                canvas.loadFromJSON(tpl.design_json, function() {
                                    let ctx = (typeof window.getCustomLabelPrintContext === 'function')
                                        ? window.getCustomLabelPrintContext()
                                        : null;
                                    if (!ctx || !ctx.barcode) {
                                        let qty = ($('#pbSpecificQty').val() || '').trim();
                                        let bNo = (typeof barcodeBatchNoForPrint === 'function') ? barcodeBatchNoForPrint() : ($('#pbBatchNo').val() || '').trim();
                                        let lblRef = formatBarcodeWholesaleRef($('#pbLabelRef').val() || '');
                                        let extras = [qty, bNo, lblRef].filter(Boolean).join('  ');
                                        ctx = {
                                            barcode: currentBarcode,
                                            prodName: (typeof window.barcodeProductNameForPrint === 'function') ? window.barcodeProductNameForPrint() : $('#pbProductName').text(),
                                            hindiName: $('#pbHindiName').is(':checked'),
                                            batchNo: bNo,
                                            labelRef: lblRef,
                                            qty: qty,
                                            mrpTxt: 'MRP-' + (($('#pbRateSelect').val() && $('#pbMrp').val()) ? parseFloat($('#pbMrp').val()).toLocaleString('en-IN') : '______'),
                                            rateTxt: 'RATE-' + (($('#pbRateSelect').val() && $('#pbPrice').val()) ? parseFloat($('#pbPrice').val()).toLocaleString('en-IN') : '______'),
                                            companyName: 'POOJA SAMAN.IN',
                                            companyAddress: 'DURG,RISALI,RAIPUR',
                                            barcodeFooter: currentBarcode + (extras ? '  ' + extras : ''),
                                            includeTitle: $('#pbIncludeTitle').is(':checked')
                                        };
                                    }

                                    var presence = (typeof window.getCustomTemplateFieldPresence === 'function')
                                        ? window.getCustomTemplateFieldPresence(canvas)
                                        : {};
                                    canvas.getObjects().forEach(function(obj) {
                                        if (typeof window.applyCustomTemplateObjectText === 'function') {
                                            window.applyCustomTemplateObjectText(obj, ctx, presence);
                                        }
                                        if (!ctx.includeTitle && (obj.type === 'i-text' || obj.type === 'text' || obj.type === 'textbox')) {
                                            let vf = obj.isVariable ? String(obj.variableField || '').replace(/[{}]/g, '').trim() : '';
                                            let raw = String(obj.text || '');
                                            if (vf === 'company_name' || vf === 'company_address' || vf === 'address' ||
                                                /POOJA SAMAN\.IN/i.test(raw) || /DURG,RISALI,RAIPUR/i.test(raw)) {
                                                obj.set('text', '');
                                                obj.set('visible', false);
                                            }
                                        }
                                    });
                                    if (typeof window.hardenCustomLabelTextObjects === 'function') {
                                        window.hardenCustomLabelTextObjects(canvas, heightMm);
                                    }
                                    canvas.renderAll();
                                    
                                    let barcodeObjects = canvas.getObjects().filter(o => o.isBarcode);
                                    if (barcodeObjects.length > 0) {
                                        barcodeObjects.forEach(function(bObj) {
                                            var density = parseFloat(bObj.barcodeDensity);
                                            if (!isFinite(density) || density <= 0) density = 1;
                                            var boxLeft = bObj.left || 0;
                                            var boxTop = bObj.top || 0;
                                            var boxW = Math.max(1, (bObj.width || 1) * (bObj.scaleX || 1));
                                            var boxH = Math.max(1, (bObj.height || 1) * (bObj.scaleY || 1));
                                            if (bObj.barcodeBoxWidth && bObj.barcodeBoxHeight) {
                                                boxLeft = bObj.barcodeBoxLeft != null ? bObj.barcodeBoxLeft : boxLeft;
                                                boxTop = bObj.barcodeBoxTop != null ? bObj.barcodeBoxTop : boxTop;
                                                boxW = bObj.barcodeBoxWidth;
                                                boxH = bObj.barcodeBoxHeight;
                                            }
                                            var drawnBarcode = (typeof window.buildBarcodeSvgDataUrl === 'function')
                                                ? window.buildBarcodeSvgDataUrl(String(currentBarcode), density, boxW, boxH, boxW, 10)
                                                : null;
                                            var barcodeDataUrl = drawnBarcode && drawnBarcode.src ? drawnBarcode.src : '';
                                            if (!barcodeDataUrl) return;
                                            fabric.Image.fromURL(barcodeDataUrl, function(img) {
                                                var pngW = Math.max(1, (drawnBarcode && drawnBarcode.pngWidth) ? drawnBarcode.pngWidth : (img.width || boxW));
                                                var pngH = Math.max(1, (drawnBarcode && drawnBarcode.pngHeight) ? drawnBarcode.pngHeight : (img.height || boxH));
                                                img.set({
                                                    left: boxLeft,
                                                    top: boxTop,
                                                    originX: 'left',
                                                    originY: 'top',
                                                    scaleX: boxW / pngW,
                                                    scaleY: boxH / pngH,
                                                    angle: bObj.angle,
                                                    isBarcode: true,
                                                    barcodeDensity: density,
                                                    barcodeBoxLeft: boxLeft,
                                                    barcodeBoxTop: boxTop,
                                                    barcodeBoxWidth: boxW,
                                                    barcodeBoxHeight: boxH,
                                                    objectCaching: false,
                                                    imageSmoothing: false
                                                });
                                                canvas.remove(bObj);
                                                canvas.add(img);
                                                canvas.renderAll();
                                            });
                                        });
                                    }
                                }, function(o, object) {
                                    if (o) {
                                        if (o.isBarcode !== undefined) object.isBarcode = o.isBarcode;
                                        if (o.isVariable !== undefined) object.isVariable = o.isVariable;
                                        if (o.variableField !== undefined) object.variableField = o.variableField;
                                        if (o.isPriceLine !== undefined) object.isPriceLine = o.isPriceLine;
                                        if (o.barcodeDensity !== undefined) object.barcodeDensity = o.barcodeDensity;
                                        if (o.barcodeBaseWidth !== undefined) object.barcodeBaseWidth = o.barcodeBaseWidth;
                                    }
                                });
                            }
                            return;
                        }
                    }

                    let svgId = 'pbBarcodeImage_preview';
                    let compName = 'POOJA SAMAN.IN'; 
                    let compAddr = 'DURG,RISALI,RAIPUR'; 
                    
                    let prodName = (typeof window.barcodeProductNameForPrint === 'function') ? window.barcodeProductNameForPrint() : $('#pbProductName').text();
                    let nameFont = $('#pbHindiName').is(':checked') ? '"Noto Sans Devanagari", "Nirmala UI", sans-serif' : 'inherit';
                    let nameTransform = $('#pbHindiName').is(':checked') ? 'none' : 'uppercase';
                    let bNo = (typeof barcodeBatchNoForPrint === 'function') ? barcodeBatchNoForPrint() : ($('#pbBatchNo').val() || '');
                    let lblRef = formatBarcodeWholesaleRef($('#pbLabelRef').val() || '');
                    let qty = $('#pbSpecificQty').val() || '';
                    
                    let mrp = '';
                    let price = '';
                    if ($('#pbRateSelect').val()) {
                        mrp = $('#pbMrp').val();
                        price = $('#pbPrice').val();
                    }
                    let blankPad = '______';
                    let mrpTxt = 'MRP-' + (mrp ? parseFloat(mrp).toLocaleString('en-IN') : blankPad);
                    let rateTxt = 'RATE-' + (price ? parseFloat(price).toLocaleString('en-IN') : blankPad);
                    let extras = [qty, bNo, lblRef].filter(Boolean).join('  ');
                    let barcodeFooter = currentBarcode + (extras ? '  ' + extras : '');
                    let format = $('#pbLabelFormat').val() || 'classic';
                    let includeTitle = $('#pbIncludeTitle').is(':checked');
                    
                    let labelSize = $('#pbLabelSize').val() || '35x25x3';
                    let sizeMap = {
                        '35x25x1': { width: 35, height: 25, font: 11.5, barcode: 16, tall: false },
                        '35x25x3': { width: 35, height: 25, font: 11.5, barcode: 16, tall: false },
                        '38x25x2': { width: 38, height: 25, font: 12, barcode: 16, tall: false },
                        '25x50x2': { width: 25, height: 50, font: 9.5, barcode: 40, tall: true }
                    };
                    
                    let size;
                    if (sizeMap[labelSize]) {
                        size = sizeMap[labelSize];
                    } else {
                        let parts = labelSize.split('x');
                        let customW = parts.length > 0 ? (parseFloat(parts[0]) || 35) : 35;
                        let customH = parts.length > 1 ? (parseFloat(parts[1]) || 25) : 25;
                        size = { 
                            width: customW, 
                            height: customH, 
                            font: customH < 20 ? 8 : (customH > 35 ? 11.5 : 10),
                            barcode: customH < 20 ? 12 : 16,
                            tall: customH > customW 
                        };
                    }
                    let widthMm = size.width;
                    let heightMm = size.height;
                    
                    let scale = 3.78;
                    let widthPx = Math.round(widthMm * scale);
                    let heightPx = Math.round(heightMm * scale);
                    let baseFontSize = size.font;
                    let barcodeHeight = size.barcode;
                    let verticalGap = size.tall ? '2px' : '1px';
                    let companyHtml = includeTitle
                        ? `<div class="company-name" style="font-size: ${format === 'split' ? '1.32em' : '1.12em'}; font-weight: bold; line-height: 1; text-transform: uppercase; letter-spacing: -0.2px; text-align: center;">${compName}</div>
                        <div class="address" style="font-size: ${format === 'split' ? '0.86em' : '0.92em'}; font-weight: bold; margin-top: ${verticalGap}; line-height: 1; text-transform: uppercase; text-align: center;">${compAddr}</div>`
                        : '';
                    let html = '';
                    if (format === 'split') {
                        barcodeHeight = size.tall ? 42 : 34;
                        html = `
                    <div class="label-box split-preview" style="margin: 0 auto; width: ${widthPx}px; height: ${heightPx}px; max-width: 100%; display: flex; flex-direction: column; justify-content: flex-start; align-items: stretch; text-align: center; padding: 3px 10px 3px 4px; overflow: hidden; font-size: ${baseFontSize}px;">
                        ${companyHtml}
                        <div class="product-name" style="font-size: 0.92em; font-weight: bold; margin-top: ${verticalGap}; text-transform: ${nameTransform}; font-family: ${nameFont}; line-height: 1.15; white-space: normal; word-break: break-word;">${escapeHtmlText(prodName)}</div>
                        <div style="display: flex; align-items: center; margin-top: 3px; width: 100%;">
                            <div style="flex: 0 0 62%; min-width: 0; text-align: center; padding: 0 2px;">
                                <svg id="${svgId}" class="barcode-img"></svg>
                                <div style="font-size: 0.7em; font-weight: bold; line-height: 1; margin-top: 1px; white-space: nowrap; overflow: hidden;">${barcodeFooter}</div>
                            </div>
                            <div style="flex: 1; text-align: left; padding-left: 3px; padding-right: 2px; font-weight: bold; line-height: 1.15; overflow: hidden;">
                                <div style="font-size: 0.82em; white-space: nowrap;">${mrpTxt}</div>
                                <div style="font-size: 0.88em; white-space: nowrap;">${rateTxt}</div>
                            </div>
                        </div>
                    </div>`;
                    } else {
                        let priceLine = mrpTxt + '&nbsp;&nbsp;' + rateTxt;
                        html = `
                    <div class="label-box" style="margin: 0 auto; width: ${widthPx}px; height: ${heightPx}px; max-width: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; padding: 8px 6px 6px 6px; overflow: hidden; font-size: ${baseFontSize}px;">
                        ${companyHtml}
                        <div class="product-name" style="font-size: 0.95em; font-weight: bold; margin-top: ${verticalGap}; text-transform: ${nameTransform}; font-family: ${nameFont}; line-height: 1.15; text-align: center; white-space: normal; word-break: break-word;">${escapeHtmlText(prodName)}</div>
                        <div class="price-line" style="font-size: 1em; font-weight: bold; margin-top: ${verticalGap}; line-height: 1; text-align: center;">${priceLine}</div>
                        <svg id="${svgId}" class="barcode-img"></svg>
                        <div style="font-size: 0.78em; font-weight: bold; line-height: 1; margin-top: 1px; white-space: nowrap;">${barcodeFooter}</div>
                    </div>`;
                    }
                    
                    $('#pbBarcodeImagesContainer').append(html);
                    
                    try {
                        let modules = 35 + String(currentBarcode).length * 11;
                        let barArea = format === 'split' ? (widthPx * 0.58) : (widthPx - 10);
                        let barW = Math.max(format === 'split' ? 1.8 : 1.6, Math.min(3.0, barArea / modules));
                        if (format !== 'split') {
                            barcodeHeight = Math.max(barcodeHeight, size.tall ? 44 : 30);
                        }
                        JsBarcode("#" + svgId, String(currentBarcode), {
                            format: "CODE128",
                            lineColor: "#000",
                            background: "#fff",
                            width: barW,
                            height: barcodeHeight,
                            displayValue: false,
                            margin: 4,
                            marginTop: 0,
                            marginBottom: 0
                        });
                    } catch(e) {}
                }
            }

            // ===== Hindi Name Management =====
            function showSavedHindiOnLabel(hindi) {
                $('#pbProductName').attr('data-hindi', hindi);
                $('#pbHindiNameInput').val(hindi);
                $('#pbHindiNameEdit').show();
                $('#pbHindiNameStatus').text('Saved on this product. You can edit it.');
                var productId = $('#pbProductId').val();
                if (productId) {
                    $('.print-barcode-btn[data-id="' + productId + '"]').attr('data-name-hindi', hindi);
                }
                var refresh = function() { updateBarcodePreview(); };
                if (document.fonts && document.fonts.load) {
                    document.fonts.load('700 16px "Noto Sans Devanagari"').then(refresh).catch(refresh);
                } else {
                    refresh();
                }
            }

            function storeEditedHindiName() {
                var hindi = ($('#pbHindiNameInput').val() || '').trim();
                var productId = $('#pbProductId').val();
                if (!productId || !hindi) return;
                $('#pbProductName').attr('data-hindi', hindi);
                $('.print-barcode-btn[data-id="' + productId + '"]').attr('data-name-hindi', hindi);
                $.ajax({
                    url: '/api/save-product-hindi-name',
                    method: 'POST',
                    data: { product_id: productId, hindi: hindi },
                    headers: { Authorization: 'Bearer ' + localStorage.getItem('authToken') }
                });
            }

            $('#pbHindiName').on('change', function() {
                var saved = ($('#pbProductName').attr('data-hindi') || '').trim();
                if (!this.checked) {
                    $('#pbHindiNameEdit').hide();
                    $('#pbHindiNameStatus').text('');
                    updateBarcodePreview();
                    return;
                }
                $('#pbHindiNameEdit').show();
                if (/[\u0900-\u097F]/.test(saved)) {
                    showSavedHindiOnLabel(saved);
                    return;
                }
                var english = ($('#pbProductName').attr('data-english') || $('#pbProductName').text() || '').trim();
                if (!english) {
                    $('#pbHindiName').prop('checked', false);
                    return;
                }
                $('#pbHindiNameStatus').text('Translating...');
                $('#saveAndPrintBarcodeBtn').prop('disabled', true);
                $.ajax({
                    url: '/api/translate-product-name',
                    method: 'GET',
                    data: { text: english, product_id: $('#pbProductId').val() },
                    headers: { Authorization: 'Bearer ' + localStorage.getItem('authToken') },
                    success: function(res) {
                        $('#saveAndPrintBarcodeBtn').prop('disabled', false);
                        if (!res || !res.status || !res.hindi || !/[\u0900-\u097F]/.test(res.hindi)) {
                            $('#pbHindiName').prop('checked', false);
                            $('#pbHindiNameStatus').text('');
                            Swal.fire('Translate', (res && res.message) ? res.message : 'Could not translate this product name.', 'warning');
                            return;
                        }
                        showSavedHindiOnLabel(res.hindi);
                    },
                    error: function(xhr) {
                        $('#saveAndPrintBarcodeBtn').prop('disabled', false);
                        $('#pbHindiName').prop('checked', false);
                        $('#pbHindiNameStatus').text('');
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Could not translate this product name.';
                        Swal.fire('Translate', msg, 'warning');
                    }
                });
            });

            // ===== Shared Batch No Management =====
            var sharedBatchDirty = false;

            function rememberSharedBatchOnButtons(batch) {
                batch = String(batch || '').trim();
                window.globalBatchNo = batch;
                $('.print-barcode-btn').each(function() {
                    var $printBtn = $(this);
                    $printBtn.attr('data-batch-no', batch);
                    $printBtn.data('batch-no', batch);
                    try {
                        var details = JSON.parse($printBtn.attr('data-barcode-details') || '{}');
                        if (typeof details === 'string') details = JSON.parse(details);
                        if (details && typeof details === 'object') {
                            Object.keys(details).forEach(function(code) {
                                if (details[code] && typeof details[code] === 'object') {
                                    details[code].batch_no = batch;
                                }
                            });
                            $printBtn.attr('data-barcode-details', JSON.stringify(details));
                        }
                    } catch (e) {}
                });
                if (window.currentBarcodeDetails && typeof window.currentBarcodeDetails === 'object') {
                    Object.keys(window.currentBarcodeDetails).forEach(function(code) {
                        if (window.currentBarcodeDetails[code] && typeof window.currentBarcodeDetails[code] === 'object') {
                            window.currentBarcodeDetails[code].batch_no = batch;
                        }
                    });
                }
            }
            window.rememberSharedBatchOnButtons = rememberSharedBatchOnButtons;

            function saveSharedBatchNo() {
                var batch = ($('#pbBatchNo').val() || '').trim();
                if (batch === String(window.globalBatchNo || '').trim()) return;
                $.ajax({
                    url: '/api/apply-global-batch-no',
                    method: 'POST',
                    data: {
                        batch_no: batch,
                        sub_branch_id: (typeof selectedSubAdminId !== 'undefined' ? selectedSubAdminId : '') || ''
                    },
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('authToken'),
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res && res.status) {
                            rememberSharedBatchOnButtons(res.batch_no || '');
                        }
                    }
                });
            }

            $('#pbBatchNo').on('input', function(e) {
                if (e.originalEvent) sharedBatchDirty = true;
            });
            $('#pbBatchNo').on('blur', function() {
                if (!sharedBatchDirty) return;
                sharedBatchDirty = false;
                saveSharedBatchNo();
            });

            $('#pbBarcodeTemplate, #pbBarcode, #pbMrp, #pbPrice, #pbBatchNo, #pbLabelRef, #pbSpecificQty, #pbBarcode_input, #pbLabelSize, #pbPrinter, #pbLabelFormat, #pbIncludeTitle, #pbShowWholesale, #pbShowBatch, #pbRateSelect').on('change input', function() {
                if (this.id === 'pbIncludeTitle') {
                    $('#pbIncludeTitleLabel').text($('#pbIncludeTitle').is(':checked') ? 'Include' : 'Hide');
                }
                if (this.id === 'pbBarcodeTemplate') {
                    syncBarcodeTemplateFormat();
                }
                updateBarcodePreview();
            });
            
            // ===== Trigger initial render when modal opens =====
            $('#printBarcodeModal').on('shown.bs.modal', function () {
                var mobile = isMobileDevice();
                $('#pbMobilePrintHelp').toggleClass('d-none', !mobile);
                $('#pbPrinterMobileHint').toggleClass('d-none', !mobile);

                let savedPrinter = localStorage.getItem('barcodePrinter');
                if (savedPrinter === 'tsc' || savedPrinter === 'tvs') {
                    $('#pbPrinter').val(savedPrinter);
                }
                let savedIncludeTitle = localStorage.getItem('barcodeIncludeTitle');
                $('#pbIncludeTitle').prop('checked', savedIncludeTitle !== '0');
                $('#pbIncludeTitleLabel').text($('#pbIncludeTitle').is(':checked') ? 'Include' : 'Hide');
                applyDefaultBarcodeTemplate();
                syncBarcodeTemplateFormat();
                updateBarcodePreview();
            });

            loadCustomBarcodeTemplates();
        });

        function getSavedBarcodeTemplate() {
            try {
                return (localStorage.getItem('barcodeLabelFormat') || 'classic').toString();
            } catch (e) {
                return 'classic';
            }
        }

        function applyDefaultBarcodeTemplate() {
            var saved = getSavedBarcodeTemplate();
            var $sel = $('#pbBarcodeTemplate');
            if (!$sel.length) return;
            if ($sel.find('option[value="' + saved + '"]').length) {
                $sel.val(saved);
            } else if (saved === 'split' || saved === 'classic') {
                $sel.val(saved);
            } else {
                $sel.val('classic');
            }
            var fmt = ($sel.val() === 'split') ? 'split' : 'classic';
            $('#pbLabelFormat').val(fmt);
        }

        // ===== Load Custom Barcode Templates from API =====
        window.customBarcodeTemplates = {};
        function loadCustomBarcodeTemplates() {
            $.ajax({
                url: '/api/barcode-templates',
                method: 'GET',
                success: function(res) {
                    if (res.status && res.data) {
                        let select = $('#pbBarcodeTemplate');
                        select.find('option:gt(1)').remove(); 
                        select.find('optgroup').remove();
                        
                        let optGroup = $('<optgroup label="Custom Templates"></optgroup>');
                        res.data.forEach(function(tpl) {
                            window.customBarcodeTemplates[tpl.id] = tpl;
                            optGroup.append(new Option(tpl.name + ' (' + parseFloat(tpl.width_mm) + 'x' + parseFloat(tpl.height_mm) + 'mm)', 'custom_' + tpl.id));
                        });
                        
                        if(res.data.length > 0) {
                            select.append(optGroup);
                        }

                        applyDefaultBarcodeTemplate();
                        if ($('#printBarcodeModal').hasClass('show')) {
                            $('#pbBarcodeTemplate').trigger('change');
                        }
                    }
                },
                error: function(err) {
                    console.log("Could not load custom barcode templates", err);
                }
            });
        }
    </script>
@endpush


{{-- Shared sales invoice print dialog (same as All Sales). --}}
    <!-- Invoice print preview (F10 to print) -->
    <div class="modal fade" id="salesInvoicePrintModal" tabindex="-1" aria-labelledby="salesInvoicePrintModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="true">
        <div class="modal-dialog modal-fullscreen-lg-down modal-xl modal-dialog-centered sales-invoice-print-dialog">
            <div class="modal-content sales-invoice-print-content d-flex flex-column">
                <div class="modal-header py-2 flex-shrink-0">
                    <div class="pe-2">
                        <h5 class="modal-title mb-0" id="salesInvoicePrintModalLabel">
                            <span class="d-none d-md-inline">Print Invoice</span>
                            <span class="d-md-none">Invoice Printing</span>
                        </h5>
                        <small class="text-muted d-none d-md-inline">Select printer â†’ Press <kbd>F10</kbd> or Print</small>
                        <small class="text-muted d-md-none">Choose a printer, then Print.</small>
                    </div>
                    @if(isset($setting) && $setting->invoice_size === 'small')
                    @php $preferredInvTpl = invoice_print_js_prefs()['template'] ?? 1; @endphp
                    <div class="d-flex gap-1 ms-3 mt-2 align-items-center flex-grow-1 justify-content-center" id="pdfTemplateButtons"
                         data-preferred-template="{{ $preferredInvTpl }}">
                        <span class="text-muted small fw-semibold me-1">Format:</span>
                        <button type="button" class="btn btn-sm btn-primary py-0 px-2 pdf-format-btn" data-template="1" onclick="loadPdfTemplate(1, this)" title="Template 1 â€” Cash sale bill">T1</button>
                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 pdf-format-btn" data-template="2" onclick="loadPdfTemplate(2, this)" title="Template 2">T2</button>
                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 pdf-format-btn" data-template="3" onclick="loadPdfTemplate(3, this)" title="Template 3">T3</button>
                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 pdf-format-btn" data-template="4" onclick="loadPdfTemplate(4, this)" title="Template 4">T4</button>
                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 pdf-format-btn" data-template="5" onclick="loadPdfTemplate(5, this)" title="Template 5">T5</button>
                    </div>
                    @endif
                    <button type="button" class="btn btn-outline-secondary btn-sm invoice-preview-toggle" title="Back to preview" aria-label="Back to preview">
                        <i class="fa fa-arrow-left"></i>
                        <span>Preview</span>
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div class="modal-body p-0 sales-invoice-print-body flex-grow-1">
                    <div class="phone-print-panel d-md-none" data-select="#salesPrinterSelect" data-status="#salesQzStatus" data-storage="posSelectedPrinter" data-bt="#salesBtPrinterList">
                        <div class="phone-print-tabs">
                            <button type="button" class="phone-print-tab is-active" data-tab="bt">Bluetooth</button>
                            <button type="button" class="phone-print-tab" data-tab="wifi">WIFI</button>
                        </div>
                        <div id="salesBtPrinterList" class="bt-printer-list" data-print-pane="bt">
                            <div class="bt-printer-empty">Tap Show printers and choose the receipt printer.</div>
                        </div>
                        <div id="salesWifiPrinterList" class="bt-printer-list d-none" data-print-pane="wifi">
                            <div class="bt-printer-empty">Looking for the Wiâ€‘Fi printerâ€¦</div>
                        </div>
                    </div>
                    <div class="invoice-print-preview">
                    <div class="sales-invoice-print-loading" id="salesInvoicePrintLoading" aria-live="polite">
                        <div class="spinner-border" role="status" aria-hidden="true"></div>
                        <span>Loading invoice...</span>
                    </div>
                    <iframe id="salesInvoicePrintFrame" title="Invoice preview" src="about:blank"></iframe>
                    </div>
                </div>
                <div class="modal-footer py-2 sales-invoice-print-footer flex-shrink-0">
                    <div class="sales-invoice-print-footer-inner w-100">
                        <div class="sales-invoice-print-footer-controls">
                            <label class="mb-0 small text-muted text-nowrap" for="salesPrinterSelect">Printer</label>
                            <select id="salesPrinterSelect" class="form-select form-select-sm sales-invoice-printer-select">
                                <option value="">Connectingâ€¦</option>
                            </select>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="salesRefreshPrintersBtn" title="Refresh printer list">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm d-none d-md-inline-block desktop-bt-print-btn" data-select="#salesPrinterSelect" data-status="#salesQzStatus" data-storage="posSelectedPrinter" title="Choose a nearby Bluetooth printer">
                                Bluetooth
                            </button>
                            <div class="form-check mb-0 d-flex align-items-center gap-1">
                                <input class="form-check-input" type="checkbox" id="salesThermalAutoCut">
                                <label class="form-check-label small" for="salesThermalAutoCut">Auto cut</label>
                            </div>
                            <div class="form-check mb-0 d-flex align-items-center gap-1" title="Bluetooth only: prints as printer text (much faster). Turn off to print the exact invoice image.">
                                <input class="form-check-input bt-fast-print-toggle" type="checkbox" id="salesBtFastPrint" checked>
                                <label class="form-check-label small" for="salesBtFastPrint">Fast print</label>
                            </div>
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" id="salesPrintHindi">
                                <label class="form-check-label small" for="salesPrintHindi">Print in Hindi</label>
                            </div>
                            <span class="small text-nowrap" id="salesQzStatus" style="color:#6c757d;" title="helper"></span>
                            <a href="https://qz.io/download/" target="_blank" rel="noopener" class="small" id="salesQzInstallLink" style="display:none;">Install QZ Tray</a>
                            <a href="{{ url('/qz/install-qz-trust.bat') }}" class="small text-nowrap d-none d-md-inline" id="salesQzTrustLink" title="Fixes Allow disabled when Remember is checked">Fix Allow</a>
                        </div>
                        <div class="sales-invoice-print-footer-actions">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary btn-sm" id="salesInvoicePrintBtn" disabled>
                                Print (F10)
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Original-style scrollable preview; footer Print stays visible */
        #salesInvoicePrintModal {
            z-index: 10650 !important;
        }
        body:has(#salesInvoicePrintModal.show) .modal-backdrop {
            z-index: 10640 !important;
        }
        .sales-invoice-print-dialog {
            max-width: 920px;
            margin: 1rem auto;
        }

        .sales-invoice-print-content {
            overflow: hidden;
            max-height: calc(100vh - 2rem);
            max-height: calc(100dvh - 2rem);
        }

        .sales-invoice-print-body {
            height: min(78vh, 820px);
            background: #f3f4f6;
            position: relative;
            overflow: auto;
            -webkit-overflow-scrolling: touch;
        }

        .sales-invoice-print-body iframe {
            width: 100%;
            height: 100%;
            min-height: 100%;
            border: 0;
            display: block;
            background: #fff;
        }

        .sales-invoice-print-footer {
            border-top: 1px solid #dee2e6;
            background: #fff;
            z-index: 5;
        }

        .sales-invoice-print-footer-inner {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem 0.75rem;
        }

        .sales-invoice-print-footer-controls {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.4rem 0.5rem;
            min-width: 0;
            flex: 1 1 220px;
        }

        .sales-invoice-printer-select {
            min-width: 140px;
            max-width: 240px;
            flex: 1 1 160px;
        }

        .sales-invoice-print-footer-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex: 0 0 auto;
            margin-left: auto;
        }

        .sales-invoice-print-footer-actions .btn {
            min-width: 88px;
        }

        .sales-invoice-print-footer-actions #salesInvoicePrintBtn {
            min-width: 110px;
            font-weight: 600;
        }

        .sales-invoice-print-loading {
            position: absolute;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 10px;
            background: rgba(243, 244, 246, 0.92);
            color: #1b2850;
            font-size: 14px;
            font-weight: 600;
            z-index: 2;
        }

        .sales-invoice-print-loading.is-visible {
            display: flex;
        }

        .sales-invoice-print-loading .spinner-border {
            width: 2rem;
            height: 2rem;
            color: #1b2850;
        }

        .sales-invoice-print-footer .btn[disabled] {
            opacity: 0.55;
            pointer-events: none;
        }

        #salesInvoicePrintModal kbd {
            background: #1b2850;
            color: #fff;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 12px;
        }

        #salesInvoicePrintModal .modal-header {
            align-items: flex-start;
            gap: 12px;
        }

        #salesInvoicePrintModal .modal-header .btn-close {
            margin-top: 2px;
            flex-shrink: 0;
        }

        @media screen and (max-width: 991.98px) {
            #salesInvoicePrintModal .modal-dialog.sales-invoice-print-dialog {
                margin: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
                height: 100%;
                min-height: 100dvh;
            }

            #salesInvoicePrintModal .sales-invoice-print-content {
                height: 100%;
                min-height: 100dvh;
                max-height: 100dvh;
                border-radius: 0;
                border: 0;
            }

            #salesInvoicePrintModal .sales-invoice-print-body {
                height: auto;
                flex: 1 1 auto;
                min-height: 0;
                overflow: auto;
            }

            #salesInvoicePrintModal .sales-invoice-print-footer {
                display: block;
                padding: 12px 14px calc(12px + env(safe-area-inset-bottom, 0px)) !important;
                border-top: 1px solid #e5e7eb;
            }

            #salesInvoicePrintModal .sales-invoice-print-footer-inner {
                flex-direction: column;
                align-items: stretch;
            }

            #salesInvoicePrintModal .sales-invoice-print-footer-actions {
                width: 100%;
                margin-left: 0;
            }

            #salesInvoicePrintModal .sales-invoice-print-footer-actions .btn {
                flex: 1 1 0;
                min-height: 44px;
                font-size: 15px;
                font-weight: 600;
            }
        }
    </style>

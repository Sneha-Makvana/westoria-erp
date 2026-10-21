<script>
    if (!window.__salesInvoicePrintModalScriptBound) {
        window.__salesInvoicePrintModalScriptBound = true;
        function getSalesInvoicePrintModal() {
            const modalEl = document.getElementById('salesInvoicePrintModal');
            if (!modalEl) {
                return null;
            }
            if (!window._salesInvoicePrintModalInstance) {
                window._salesInvoicePrintModalInstance = bootstrap.Modal.getOrCreateInstance(modalEl, {
                    backdrop: 'static',
                    keyboard: true
                });
            }
            return window._salesInvoicePrintModalInstance;
        }

        function setSalesInvoicePrintLoading(isLoading) {
            const $loader = $('#salesInvoicePrintLoading');
            const $printBtn = $('#salesInvoicePrintBtn');
            if (isLoading) {
                $loader.addClass('is-visible');
                $printBtn.prop('disabled', true);
            } else {
                $loader.removeClass('is-visible');
                $printBtn.prop('disabled', false);
            }
        }

        function syncSalesInvoicePrintModalLayout() {
            const modalEl = document.getElementById('salesInvoicePrintModal');
            const bodyEl = modalEl ? modalEl.querySelector('.sales-invoice-print-body') : null;
            if (!modalEl || !bodyEl || !modalEl.classList.contains('show')) {
                return;
            }
            if (window.matchMedia('(max-width: 991.98px)').matches) {
                const contentEl = modalEl.querySelector('.sales-invoice-print-content');
                const headerEl = modalEl.querySelector('.modal-header');
                const footerEl = modalEl.querySelector('.sales-invoice-print-footer');
                const viewportHeight = window.visualViewport ? window.visualViewport.height : window.innerHeight;
                const chromeHeight = (headerEl ? headerEl.offsetHeight : 0) + (footerEl ? footerEl.offsetHeight : 0);
                const nextHeight = Math.max(220, viewportHeight - chromeHeight);
                bodyEl.style.height = nextHeight + 'px';
                bodyEl.style.maxHeight = '';
                bodyEl.style.flex = '';
                if (contentEl) {
                    contentEl.style.minHeight = viewportHeight + 'px';
                    contentEl.style.height = '';
                    contentEl.style.maxHeight = '';
                }
            } else {
                bodyEl.style.height = '';
                bodyEl.style.maxHeight = '';
                bodyEl.style.flex = '';
                const dialogEl = modalEl.querySelector('.sales-invoice-print-dialog');
                if (dialogEl) {
                    dialogEl.style.height = '';
                    dialogEl.style.maxHeight = '';
                    dialogEl.style.marginTop = '';
                    dialogEl.style.marginBottom = '';
                }
                const contentEl = modalEl.querySelector('.sales-invoice-print-content');
                if (contentEl) {
                    contentEl.style.height = '';
                    contentEl.style.maxHeight = '';
                    contentEl.style.minHeight = '';
                }
            }
        }

        function markSalesInvoicePrintReady() {
            window._salesInvoicePrintReady = true;
            setSalesInvoicePrintLoading(false);
            syncSalesInvoicePrintModalLayout();
            trySalesInvoiceAutoPrint();
        }

        function trySalesInvoiceAutoPrint() {
            if (!window._salesInvoiceAutoPrintPending || !window._salesInvoicePrintReady) {
                return;
            }
            if (window.matchMedia && window.matchMedia('(max-width: 1024.98px)').matches) {
                window._salesInvoiceAutoPrintPending = false;
                return;
            }
            if (!window.InvoiceQzPrint) {
                return;
            }
            const printerName = ($('#salesPrinterSelect').val() || '').trim();
            if (!printerName) {
                return;
            }
            if (printerName === '__browser__' && !window._salesInvoiceQuickPrint) {
                return;
            }
            window._salesInvoiceAutoPrintPending = false;
            window._salesInvoiceAutoPrintTimer = setTimeout(function() {
                const modalEl = document.getElementById('salesInvoicePrintModal');
                if (!modalEl || !modalEl.classList.contains('show')) {
                    return;
                }
                printSalesInvoiceFromModal();
            }, 300);
        }

            function refreshSalesPrinterList(pick) {
                function run() {
                    if (!window.InvoiceQzPrint) {
                        $('#salesPrinterSelect').html('<option value="">Print helper loadingâ€¦</option>');
                        $('#salesQzInstallLink').show();
                        return Promise.reject(new Error('InvoiceQzPrint missing'));
                    }
                    return InvoiceQzPrint.refreshPrinters({
                        select: '#salesPrinterSelect',
                        status: '#salesQzStatus',
                        installLink: '#salesQzInstallLink',
                        storageKey: 'posSelectedPrinter',
                        list: '#salesBtPrinterList',
                        wifiList: '#salesWifiPrinterList',
                        pick: !!pick
                    }).then(function(printers) {
                        try {
                            var ver = window.__INVOICE_QZ_PRINT_VER || '';
                            if (ver) {
                                $('#salesQzStatus').attr('title', 'QZ helper ' + ver);
                            }
                        } catch (e) {}
                        trySalesInvoiceAutoPrint();
                        return printers;
                    });
                }
                if (window.InvoiceQzPrint) {
                    return run();
                }
                return new Promise(function(resolve, reject) {
                    var tries = 0;
                    var t = setInterval(function() {
                        tries++;
                        if (window.InvoiceQzPrint || tries > 50) {
                            clearInterval(t);
                            run().then(resolve).catch(reject);
                        }
                    }, 100);
                });
            }

        function printSalesInvoiceFromModal() {
            if (window.InvoiceQzPrint && InvoiceQzPrint.shouldDeferPrintForPicker(document.getElementById('salesInvoicePrintBtn'))) {
                return;
            }
            const modalEl = document.getElementById('salesInvoicePrintModal');
            if (!modalEl || !modalEl.classList.contains('show')) {
                return;
            }
            if (window._salesInvoicePrintBusy) {
                return;
            }
            const frame = document.getElementById('salesInvoicePrintFrame');
            if (!frame || !frame.src || frame.src === 'about:blank' || !window._salesInvoicePrintReady) {
                return;
            }
            const printerName = ($('#salesPrinterSelect').val() || '').trim();
            const doAutoCut = $('#salesThermalAutoCut').is(':checked');
            const pdfUrl = String(frame.src || '').split('#')[0];
            if (!window.InvoiceQzPrint) {
                window.open(pdfUrl, '_blank');
                return;
            }
            window._salesInvoicePrintBusy = true;
            $('#salesInvoicePrintBtn').prop('disabled', true);
            InvoiceQzPrint.printInvoice({
                printerName: printerName,
                pdfUrl: pdfUrl,
                autoCut: doAutoCut,
                frame: frame,
                status: '#salesQzStatus',
                button: '#salesInvoicePrintBtn',
                select: '#salesPrinterSelect',
                list: '#salesBtPrinterList',
                storageKey: 'posSelectedPrinter'
            }).catch(function() {}).then(function() {
                if (window._salesInvoicePrintReady) {
                    $('#salesInvoicePrintBtn').prop('disabled', false);
                }
                // Short lock only to block double-tap; button is enabled again immediately above.
                setTimeout(function() {
                    window._salesInvoicePrintBusy = false;
                }, 800);
            });
        }

        window.currentPrintOrderId = null;

        function salesInvoicePdfPreviewUrl(path) {
            // Plain PDF URL â€” Chrome viewer scrollable like before
            return String(path || '').split('#')[0];
        }
        
        window.loadPdfTemplate = function(templateId, btnElement) {
            if (!window.currentPrintOrderId) return;
            
            // Handle active button state
            if (btnElement) {
                $('.pdf-format-btn').removeClass('btn-primary').addClass('btn-outline-primary');
                $(btnElement).removeClass('btn-outline-primary').addClass('btn-primary');
            }
            
            const frame = document.getElementById('salesInvoicePrintFrame');
            if (frame) {
                setSalesInvoicePrintLoading(true);
                window._salesInvoicePrintReady = false;
                var tpl = parseInt(templateId, 10) || 1;
                var hindi = $('#salesPrintHindi').is(':checked') ? '1' : '0';
                frame.src = salesInvoicePdfPreviewUrl('/sales/invoice/pdf/' + encodeURIComponent(window.currentPrintOrderId) + '?template=' + tpl + '&hindi=' + hindi);
                if (window.InvoiceQzPrint && InvoiceQzPrint.preparePhonePrint) {
                    InvoiceQzPrint.preparePhonePrint(frame.src);
                }
            }
        };

        window.openSalesInvoicePrintModal = function(orderId, options) {
            if (!orderId) {
                return;
            }
            options = options || {};
            
            // Reset all format buttons to inactive state
            $('.pdf-format-btn').removeClass('btn-primary').addClass('btn-outline-primary');
            $('#salesPrintHindi').prop('checked', !!(window.__INVOICE_PRINT_PREFS || {}).hindi);
            $('#salesThermalAutoCut').prop('checked', /^(bt|serial):/.test(String($('#salesPrinterSelect').val() || '')) || !!(window.__INVOICE_PRINT_PREFS || {}).autoCut);
            
            window.currentPrintOrderId = orderId;
            window._salesInvoiceQuickPrint = !!options.autoPrint;
            window._salesInvoiceAutoPrintPending = !!options.autoPrint;
            const frame = document.getElementById('salesInvoicePrintFrame');
            const modal = getSalesInvoicePrintModal();
            if (!frame || !modal) {
                return;
            }
            if (window._salesInvoicePrintLoadTimer) {
                clearTimeout(window._salesInvoicePrintLoadTimer);
            }
            window._salesInvoicePrintReady = false;
            setSalesInvoicePrintLoading(true);
            frame.onload = function() {
                if (window._salesInvoicePrintLoadTimer) {
                    clearTimeout(window._salesInvoicePrintLoadTimer);
                    window._salesInvoicePrintLoadTimer = null;
                }
                markSalesInvoicePrintReady();
            };
            frame.onerror = function() {
                if (window._salesInvoicePrintLoadTimer) {
                    clearTimeout(window._salesInvoicePrintLoadTimer);
                    window._salesInvoicePrintLoadTimer = null;
                }
                setSalesInvoicePrintLoading(false);
            };
            var preferredTpl = parseInt($('#pdfTemplateButtons').data('preferred-template'), 10) || 1;
            if (window.matchMedia && window.matchMedia('(max-width: 767.98px)').matches) {
                preferredTpl = 1;
            }
            preferredTpl = Math.min(5, Math.max(1, preferredTpl));
            var prefBtn = $('.pdf-format-btn[data-template="' + preferredTpl + '"]');
            if (prefBtn.length) {
                prefBtn.removeClass('btn-outline-primary').addClass('btn-primary');
            }
            frame.src = salesInvoicePdfPreviewUrl('/sales/invoice/pdf/' + encodeURIComponent(orderId) + '?template=' + preferredTpl + '&hindi=0');
            if (window.InvoiceQzPrint && InvoiceQzPrint.preparePhonePrint) {
                InvoiceQzPrint.preparePhonePrint(frame.src);
            }
            window._salesInvoicePrintLoadTimer = setTimeout(function() {
                if (!window._salesInvoicePrintReady) {
                    markSalesInvoicePrintReady();
                }
                window._salesInvoicePrintLoadTimer = null;
            }, 4000);
            connectBluetoothPrinter(false);
            modal.show();
            setTimeout(syncSalesInvoicePrintModalLayout, 60);
        };

        var isBluetoothConnecting = false;

        function connectBluetoothPrinter(isManualRefresh) {
            isManualRefresh = isManualRefresh === true;
            
            var isMobile = false;
            try {
                if (navigator.userAgentData && navigator.userAgentData.mobile) {
                    isMobile = true;
                } else if (/Android|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent)) {
                    isMobile = true;
                }
            } catch (e) {}

            var hasWebBluetooth = !!(navigator.bluetooth && typeof navigator.bluetooth.requestDevice === 'function');
            var bluetoothOpen = $('#salesBtPrinterList').is(':visible');

            if (isMobile && hasWebBluetooth) {
                if (isBluetoothConnecting && !isManualRefresh) return;
                isBluetoothConnecting = true;
                
                const $status = $('#salesQzStatus');
                $status.text('Connecting...').css('color', '#ea5455');
                
                let timeoutId = setTimeout(function() {
                    isBluetoothConnecting = false;
                    $status.text('Printer not found. Tap refresh to try again.').css('color', '#ea5455');
                }, 30000);

                let cleanup = function() {
                    clearTimeout(timeoutId);
                    isBluetoothConnecting = false;
                };

                let tryPicker = function() {
                    let pickerResult;
                    try {
                        if (window.InvoiceQzPrint && InvoiceQzPrint.pickNearbyPrinter) {
                            pickerResult = InvoiceQzPrint.pickNearbyPrinter({
                                select: '#salesPrinterSelect',
                                status: '#salesQzStatus',
                                storageKey: 'posSelectedPrinter',
                                list: '#salesBtPrinterList'
                            });
                        }
                    } catch (e) {
                        if (e.name === 'NotFoundError' || e.name === 'SecurityError') {
                            cleanup();
                            if (e.name === 'SecurityError') {
                                $status.text('No user gesture. Tap refresh to try again.').css('color', '#ea5455');
                            }
                            return;
                        }
                    }
                    
                    if (pickerResult && typeof pickerResult.catch === 'function') {
                        pickerResult.catch(function(e) {
                            if (e && (e.name === 'NotFoundError' || e.name === 'SecurityError')) {
                                cleanup();
                                if (e.name === 'SecurityError') {
                                    $status.text('No user gesture. Tap refresh to try again.').css('color', '#ea5455');
                                }
                            }
                        });
                    }

                    refreshSalesPrinterList(false).then(cleanup).catch(cleanup);
                };

                if (isManualRefresh) {
                    tryPicker();
                    return;
                }

                if (navigator.bluetooth.getDevices) {
                    navigator.bluetooth.getDevices().then(function(devices) {
                        let paired = devices.filter(function(d) { return d.id && d.name; });
                        if (paired.length > 0) {
                            let device = paired[0];
                            let connectPromise = Promise.resolve();
                            if (device.gatt && !device.gatt.connected) {
                                connectPromise = device.gatt.connect().catch(function(e) {
                                    if (device.watchAdvertisements) {
                                        return device.watchAdvertisements().then(function() {
                                            return new Promise(function(resolve, reject) {
                                                let advTimeout = setTimeout(function() { reject(new Error('timeout')); }, 5000);
                                                device.addEventListener('advertisementreceived', function() {
                                                    clearTimeout(advTimeout);
                                                    device.gatt.connect().then(resolve).catch(reject);
                                                }, { once: true });
                                            });
                                        });
                                    }
                                    throw e;
                                });
                            }
                            
                            connectPromise.then(function() {
                                return refreshSalesPrinterList(false).then(cleanup);
                            }).catch(function(err) {
                                try {
                                    tryPicker();
                                } catch (e) {
                                    cleanup();
                                    $status.text('Printer not found. Tap refresh to try again.').css('color', '#ea5455');
                                }
                            });
                        } else {
                            tryPicker();
                        }
                    }).catch(function(err) {
                        tryPicker();
                    });
                } else {
                    tryPicker();
                }
            } else {
                if (isManualRefresh && bluetoothOpen && window.InvoiceQzPrint && InvoiceQzPrint.pickNearbyPrinter) {
                    InvoiceQzPrint.pickNearbyPrinter({
                        select: '#salesPrinterSelect',
                        status: '#salesQzStatus',
                        storageKey: 'posSelectedPrinter',
                        list: '#salesBtPrinterList'
                    });
                }
                refreshSalesPrinterList(false).catch(function() {});
            }
        }

        $('#salesInvoicePrintBtn').on('click', function() {
            printSalesInvoiceFromModal();
        });

        $(document).on('click', '#saleDetailsPrintBtn', function(e) {
            e.preventDefault();
            var orderId = $(this).data('order-id') || $('#selse_id').val();
            if (orderId && typeof window.openSalesInvoicePrintModal === 'function') {
                window.openSalesInvoicePrintModal(orderId);
            }
        });

        $('#salesRefreshPrintersBtn').on('click', function() {
            connectBluetoothPrinter(true);
        });

        $('#salesPrinterSelect').on('change', function() {
            const name = $(this).val() || '';
            try {
                if (name) localStorage.setItem('posSelectedPrinter', name);
            } catch (e) {}
        });

        $('#salesThermalAutoCut').on('change', function() {
            try {
                localStorage.setItem('posThermalAutoCut', $(this).is(':checked') ? '1' : '0');
            } catch (e) {}
        });

        $('#salesPrintHindi').on('change', function() {
            if (!window.currentPrintOrderId) return;
            var active = $('.pdf-format-btn.btn-primary').data('template') || $('#pdfTemplateButtons').data('preferred-template') || 1;
            window.loadPdfTemplate(active, null);
        });

        $('#salesInvoicePrintModal').on('shown.bs.modal', function() {
            syncSalesInvoicePrintModalLayout();
            if (window.innerWidth >= 768) {
                refreshSalesPrinterList().catch(function() {});
            }
        });

        $('#salesInvoicePrintModal').on('hidden.bs.modal', function() {
            const frame = document.getElementById('salesInvoicePrintFrame');
            window._salesInvoicePrintReady = false;
            window._salesInvoicePrintBusy = false;
            window._salesInvoiceAutoPrintPending = false;
            window._salesInvoiceQuickPrint = false;
            if (window._salesInvoiceAutoPrintTimer) {
                clearTimeout(window._salesInvoiceAutoPrintTimer);
                window._salesInvoiceAutoPrintTimer = null;
            }
            if (window._salesInvoicePrintLoadTimer) {
                clearTimeout(window._salesInvoicePrintLoadTimer);
                window._salesInvoicePrintLoadTimer = null;
            }
            setSalesInvoicePrintLoading(false);
            if (frame) {
                frame.onload = null;
                frame.onerror = null;
                frame.src = 'about:blank';
            }
            const bodyEl = this.querySelector('.sales-invoice-print-body');
            if (bodyEl) {
                bodyEl.style.height = '';
            }
        });

        $(window).on('resize.salesInvoicePrint orientationchange.salesInvoicePrint', function() {
            syncSalesInvoicePrintModalLayout();
        });

        if (window.visualViewport) {
            window.visualViewport.addEventListener('resize', syncSalesInvoicePrintModalLayout);
        }

        $(document).on('keydown.salesInvoicePrint', function(e) {
            const modalEl = document.getElementById('salesInvoicePrintModal');
            if (!modalEl || !modalEl.classList.contains('show')) {
                return;
            }
            if (e.key === 'F10' || e.keyCode === 121) {
                e.preventDefault();
                e.stopPropagation();
                printSalesInvoiceFromModal();
            }
        });
    }
</script>

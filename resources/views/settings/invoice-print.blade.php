@extends('layout.app')

@section('title', 'Invoice Print')

@section('content')
@php
    $defaultPrinter = ($setting->invoice_size ?? 'big') === 'small' ? 'thermal' : 'regular';
    $thermal['thermal_theme'] = (int) ($thermal['thermal_theme'] ?? 0) ?: 1;
    $sizes = ['small' => 'Small', 'medium' => 'Medium', 'large' => 'Large'];
    $wordFormats = ['indian' => 'Indian (e.g. 1,00,000)', 'international' => 'International (e.g. 100,000)'];

    $regularSections = [
        'Printer Settings' => [
            ['select', 'text_size', 'Print text size', 'Font size used for the whole bill.', $sizes],
            ['select', 'page_size', 'Page size', 'Paper the bill is laid out for.', ['a4' => 'A4 (210 × 297 mm)', 'a5' => 'A5 (148 × 210 mm)']],
            ['select', 'orientation', 'Orientation', 'Portrait (tall) or landscape (wide) page.', ['portrait' => 'Portrait', 'landscape' => 'Landscape']],
        ],
        'Company Info / Header' => [
            ['toggle', 'warn_unsaved', 'Show warning for unsaved changes', 'Ask before leaving this page when changes are not saved.'],
            ['toggle', 'show_company_name', 'Print Company Name', 'Shop name at the top of the bill.'],
            ['select', 'company_name_size', 'Company Name Text Size', 'Size of the shop name.', ['small' => 'Small', 'medium' => 'Medium', 'large' => 'Large', 'xlarge' => 'Extra Large']],
            ['toggle', 'show_logo', 'Company Logo', 'Logo from Shop Settings.'],
            ['toggle', 'show_watermark', 'Logo Watermark', 'Faded logo behind the bill.'],
            ['toggle', 'show_address', 'Address', 'Shop address.'],
            ['toggle', 'show_email', 'Email', 'Shop email.'],
            ['toggle', 'show_phone', 'Phone number', 'Shop phone number.'],
            ['toggle', 'show_gstin', 'GSTIN on Sale', 'Shop GST number on GST bills.'],
            ['toggle', 'bill_of_supply', 'Print Bill of Supply for non tax invoices', 'Title the bill "Bill of Supply" when it has no tax.'],
            ['stepper', 'extra_top_space', 'Extra spaces on top of PDF', 'Blank lines above the bill, for pre-printed letterheads.', 0, 20],
            ['toggle', 'print_original_duplicate', 'Print Original/Duplicate', 'Print two copies marked Original and Duplicate.'],
        ],
        'Totals & Taxes' => [
            ['toggle', 'expand_table', 'Expand table to print on whole page', 'Add empty rows so the item table fills the page.'],
            ['stepper', 'min_item_rows', 'Min. No. of rows in Item Table', 'Empty rows are added until the table has this many rows.', 0, 40],
            ['toggle', 'show_total_qty', 'Total Item Quantity', 'Total quantity row under the items.'],
            ['toggle', 'amount_decimal', 'Amount with Decimal (eg 0.00)', 'Show paise in amounts.'],
            ['toggle', 'show_received', 'Received amount', 'Amount paid by the customer.'],
            ['toggle', 'show_balance', 'Balance amount', 'Amount still due on this bill.'],
            ['toggle', 'show_party_balance', 'Print Current Balance of Party', 'Total due from this customer across all bills.'],
            ['toggle', 'show_tax_details', 'Tax details', 'Tax-wise summary (CGST, SGST, IGST…).'],
            ['toggle', 'amount_grouping', 'Amount Grouping', 'Commas in amounts, e.g. 1,00,000.'],
            ['toggle', 'show_amount_words', 'Amount in words', 'Grand total written in words.'],
            ['select', 'amount_words_format', 'Amount in words format', 'Indian uses lakh/crore, international uses million.', $wordFormats],
            ['toggle', 'show_you_saved', 'You Saved', 'Savings against MRP and discounts.'],
            ['toggle', 'show_bank_details', 'Bank details', 'Bank name, account and IFSC from Shop Settings.'],
            ['toggle', 'show_qr', 'Payment QR', 'Payment QR code from Shop Settings.'],
        ],
        'Footer' => [
            ['toggle', 'show_description', 'Print description', 'Remarks written on the bill.'],
            ['toggle', 'show_terms', 'Terms & Conditions', 'Print your terms at the bottom.'],
            ['link', 'terms', 'Set Terms & Conditions', 'Edit the terms text.'],
            ['toggle', 'show_received_by', 'Print Received by details', 'Space for the receiver name and signature.'],
            ['toggle', 'show_delivered_by', 'Print Delivered by details', 'Space for the delivery person name and signature.'],
            ['toggle', 'show_signature_text', 'Print Signature Text', 'Signature line with your text.'],
            ['link', 'signature', 'Set Custom Signature Text', 'Text under the signature, e.g. Authorized Signatory.'],
            ['toggle', 'show_payment_mode', 'Payment Mode', 'Cash, Online, EMI…'],
            ['toggle', 'show_acknowledgement', 'Print Acknowledgement', 'Tear-off receipt slip for the customer to sign.'],
        ],
    ];

    $thermalSections = [
        'Thermal Printer Settings' => [
            ['link', 'printer', 'Set default thermal printer', 'Printer selected first in the print window.'],
            ['toggle', 'language', 'Native language printing', 'Print product names in Hindi by default.'],
            ['select', 'paper_size', 'Thermal printer page size', 'Width of the receipt roll.', ['2inch' => '2 inch (58mm)', '3inch' => '3 inch (80mm)']],
            ['stepper', 'extra_end_lines', 'Extra lines at print end', 'Blank lines fed after the bill.', 0, 10],
            ['stepper', 'print_copies', 'Number of copies', 'Copies printed each time.', 1, 5],
            ['toggle', 'text_styling', 'Use Text Styling (Bold, Italic...)', 'Bold text on the receipt.'],
            ['toggle', 'auto_cut', 'Auto cut paper after printing', 'Cut the paper after every bill (cutter printers only).'],
            ['toggle', 'open_cash_drawer', 'Open cash drawer after printing', 'Kick open a drawer connected to the printer.'],
        ],
        'Themes' => [
            ['select', 'thermal_theme', 'Change Thermal printer theme', 'Receipt layout selected in the print window.', [1 => 'Theme 1', 2 => 'Theme 2', 3 => 'Theme 3', 4 => 'Theme 4', 5 => 'Theme 5']],
        ],
        'Company Info / Header' => [
            ['toggle', 'show_company_name', 'Print Company Name', 'Shop name at the top.'],
            ['toggle', 'show_logo', 'Company Logo', 'Logo from Shop Settings.'],
            ['toggle', 'show_shop_address', 'Address', 'Shop address.'],
            ['toggle', 'show_shop_email', 'Email', 'Shop email.'],
            ['toggle', 'show_shop_phone', 'Phone number', 'Shop phone number.'],
            ['toggle', 'show_shop_gst', 'GSTIN on Sale', 'Shop GST number.'],
            ['toggle', 'bill_of_supply', 'Print Bill of Supply for non tax invoices', 'Title the bill "Bill of Supply" when it has no tax.'],
        ],
        'Item Table' => [
            ['toggle', 'show_serial', 'S.No', 'Serial number for each item.'],
            ['toggle', 'show_unit', 'Units of measurement', 'Unit after the quantity, e.g. 2Pcs.'],
            ['toggle', 'show_item_mrp', 'MRP', 'MRP column.'],
            ['toggle', 'show_item_description', 'Description', 'Product description under the name.'],
        ],
        'Totals & Taxes' => [
            ['toggle', 'show_total_item_count', 'Total Item Quantity', 'Total quantity next to the total.'],
            ['toggle', 'decimal_places', 'Amount with Decimal (eg 0.00)', 'Show paise in amounts.'],
            ['toggle', 'show_received', 'Received amount', 'Amount paid by the customer.'],
            ['toggle', 'show_balance', 'Balance amount', 'Amount still due on this bill.'],
            ['toggle', 'show_customer_outstanding', 'Print Current Balance of Party', 'Amount due from the customer.'],
            ['toggle', 'show_tax_details', 'Tax details', 'Tax-wise summary.'],
            ['toggle', 'amount_grouping', 'Amount Grouping', 'Commas in amounts, e.g. 1,00,000.'],
            ['toggle', 'show_amount_words', 'Amount in words', 'Grand total written in words.'],
            ['select', 'amount_words_format', 'Amount in words format', 'Indian uses lakh/crore, international uses million.', $wordFormats],
            ['toggle', 'show_savings', 'You Saved', 'Savings against MRP and discounts.'],
        ],
        'Footer' => [
            ['toggle', 'show_remarks', 'Print description', 'Remarks written on the bill.'],
            ['toggle', 'enable_terms', 'Terms & Conditions', 'Print your terms at the bottom.'],
            ['link', 'terms', 'Set Terms & Conditions', 'Edit the terms text.'],
        ],
    ];

    $toggleOn = function ($group, $key, $values) {
        $v = $values[$key] ?? null;
        if ($group === 'thermal' && $key === 'language') {
            return $v === 'hindi';
        }
        if ($group === 'thermal' && $key === 'decimal_places') {
            return (int) $v > 0;
        }
        return !empty($v);
    };
@endphp

<div class="ip-page" id="invoicePrintPage" data-save-url="{{ route('setting.invoice-print.save') }}">
    <div class="ip-shell">
        <div class="ip-top">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('setting.generalsettings') }}" class="ip-back" aria-label="Back"><i class="fas fa-chevron-left"></i></a>
            <h4 class="ip-title">Invoice Print</h4>
            <div class="ip-top-actions">
                @if ($previewOrderId)
                    <button type="button" class="ip-btn ip-btn-light" id="ipPreview"><i class="far fa-eye"></i><span> Preview</span></button>
                @endif
                <button type="button" class="ip-btn ip-btn-primary d-none d-md-inline-flex" id="ipSaveTop"><i class="fas fa-check"></i> Save</button>
            </div>
        </div>

        <div class="ip-tabs" role="tablist">
            <button type="button" class="ip-tab {{ $defaultPrinter === 'regular' ? 'is-active' : '' }}" data-tab="regular">Regular Printer</button>
            <button type="button" class="ip-tab {{ $defaultPrinter === 'thermal' ? 'is-active' : '' }}" data-tab="thermal">Thermal Printer</button>
        </div>

        <input type="hidden" id="ipDefaultPrinter" value="{{ $defaultPrinter }}">
        <textarea class="d-none" id="ipTermsValue">{{ $setting->terms_condition_eng ?? '' }}</textarea>

        @foreach (['regular' => $regularSections, 'thermal' => $thermalSections] as $group => $sections)
            @php $values = $group === 'regular' ? $regular : $thermal; @endphp
            <div class="ip-pane {{ $defaultPrinter === $group ? '' : 'd-none' }}" data-pane="{{ $group }}">
                <div class="ip-card">
                    <div class="ip-card-title">Set Default</div>
                    <label class="ip-row">
                        <span class="ip-label">Make {{ $group === 'regular' ? 'Regular' : 'Thermal' }} Printer Default</span>
                        <span class="ip-switch">
                            <input type="checkbox" class="ip-default-toggle" data-printer="{{ $group }}" {{ $defaultPrinter === $group ? 'checked' : '' }}>
                            <span class="ip-slider"></span>
                        </span>
                    </label>
                </div>

                @if ($group === 'regular')
                    <div class="ip-card">
                        <div class="ip-card-title">Themes</div>
                        <button type="button" class="ip-row ip-row-link" data-open="#ipThemeModal">
                            <span class="ip-label">Change Theme and Colors <i class="ip-info far fa-question-circle" data-info="Accent colour used for headings and table header."></i></span>
                            <span class="ip-value"><span class="ip-swatch-dot" id="ipThemeDot" style="background: {{ $regular['theme_color'] }}"></span><i class="fas fa-chevron-right"></i></span>
                        </button>
                        <input type="hidden" data-group="regular" data-key="theme_color" data-type="text" id="ipThemeColor" value="{{ $regular['theme_color'] }}">
                    </div>
                @endif

                @foreach ($sections as $title => $rows)
                    <div class="ip-card">
                        <div class="ip-card-title">
                            {{ $title }}
                            @if ($title === 'Company Info / Header')
                                <a href="{{ route('setting.generalsettings') }}" class="ip-edit" title="Edit shop details"><i class="fas fa-pencil-alt"></i></a>
                            @endif
                        </div>
                        @foreach ($rows as $row)
                            @php [$type, $key, $label, $info] = $row; @endphp
                            @if ($type === 'toggle')
                                <label class="ip-row">
                                    <span class="ip-label">{{ $label }} <i class="ip-info far fa-question-circle" data-info="{{ $info }}"></i></span>
                                    <span class="ip-switch">
                                        <input type="checkbox" data-group="{{ $group }}" data-key="{{ $key }}" data-type="toggle"
                                            @if ($group === 'thermal' && $key === 'decimal_places') data-on="{{ max(1, (int) ($thermal['decimal_places'] ?? 2)) }}" @endif
                                            {{ $toggleOn($group, $key, $values) ? 'checked' : '' }}>
                                        <span class="ip-slider"></span>
                                    </span>
                                </label>
                            @elseif ($type === 'select')
                                <label class="ip-row">
                                    <span class="ip-label">{{ $label }} <i class="ip-info far fa-question-circle" data-info="{{ $info }}"></i></span>
                                    <span class="ip-select-wrap">
                                        <select class="ip-select" data-group="{{ $group }}" data-key="{{ $key }}" data-type="text">
                                            @foreach ($row[4] as $optVal => $optLabel)
                                                <option value="{{ $optVal }}" {{ (string) ($values[$key] ?? '') === (string) $optVal ? 'selected' : '' }}>{{ $optLabel }}</option>
                                            @endforeach
                                        </select>
                                        <i class="fas fa-chevron-down"></i>
                                    </span>
                                </label>
                            @elseif ($type === 'stepper')
                                <div class="ip-row">
                                    <span class="ip-label">{{ $label }} <i class="ip-info far fa-question-circle" data-info="{{ $info }}"></i></span>
                                    <span class="ip-stepper">
                                        <button type="button" class="ip-step" data-step="-1" aria-label="Decrease"><i class="fas fa-minus"></i></button>
                                        <input type="text" inputmode="numeric" readonly data-group="{{ $group }}" data-key="{{ $key }}" data-type="number"
                                            data-min="{{ $row[4] }}" data-max="{{ $row[5] }}" value="{{ str_pad((string) (int) ($values[$key] ?? $row[4]), 2, '0', STR_PAD_LEFT) }}">
                                        <button type="button" class="ip-step" data-step="1" aria-label="Increase"><i class="fas fa-plus"></i></button>
                                    </span>
                                </div>
                            @elseif ($type === 'link')
                                <button type="button" class="ip-row ip-row-link" data-open="{{ ['terms' => '#ipTermsModal', 'signature' => '#ipSignatureModal', 'printer' => '#ipPrinterModal'][$key] }}">
                                    <span class="ip-label">{{ $label }} <i class="ip-info far fa-question-circle" data-info="{{ $info }}"></i></span>
                                    <span class="ip-value">
                                        @if ($key === 'printer')
                                            <span class="ip-value-text" id="ipPrinterLabel">{{ ($thermal['default_printer_label'] ?? '') ?: ($thermal['default_printer'] ?: 'Not set') }}</span>
                                        @elseif ($key === 'signature')
                                            <span class="ip-value-text" id="ipSignatureLabel">{{ $regular['signature_text'] }}</span>
                                        @endif
                                        <i class="fas fa-chevron-right"></i>
                                    </span>
                                </button>
                                @if ($key === 'signature')
                                    <input type="hidden" data-group="regular" data-key="signature_text" data-type="text" id="ipSignatureValue" value="{{ $regular['signature_text'] }}">
                                @elseif ($key === 'printer')
                                    <input type="hidden" data-group="thermal" data-key="default_printer" data-type="text" id="ipPrinterValue" value="{{ $thermal['default_printer'] }}">
                                    <input type="hidden" data-group="thermal" data-key="default_printer_label" data-type="text" id="ipPrinterLabelValue" value="{{ $thermal['default_printer_label'] ?? '' }}">
                                @endif
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    <div class="ip-savebar d-md-none">
        <button type="button" class="ip-btn ip-btn-primary w-100" id="ipSaveBottom"><i class="fas fa-check"></i> Save Settings</button>
    </div>
</div>

<div class="modal fade ip-modal" id="ipThemeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Theme Colour</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="ip-swatches">
                    @foreach (['#ff9f43', '#1b2850', '#e8334a', '#0a6cdc', '#198754', '#6f42c1', '#0dcaf0', '#212529'] as $color)
                        <button type="button" class="ip-swatch {{ strtolower($regular['theme_color']) === $color ? 'is-active' : '' }}" data-color="{{ $color }}" style="background: {{ $color }}" aria-label="{{ $color }}"></button>
                    @endforeach
                </div>
                <label class="ip-custom-color">
                    <span>Custom colour</span>
                    <input type="color" id="ipCustomColor" value="{{ $regular['theme_color'] }}">
                </label>
            </div>
        </div>
    </div>
</div>

<div class="modal fade ip-modal" id="ipTermsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Terms &amp; Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" id="ipTermsInput" rows="6" placeholder="e.g. Goods once sold will not be taken back."></textarea>
                <small class="text-muted d-block mt-2">Same terms as Shop Settings &rarr; Delivery Terms. Printed on regular and thermal bills when Terms &amp; Conditions is on.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="ip-btn ip-btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="ip-btn ip-btn-primary" id="ipTermsApply">Done</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade ip-modal" id="ipSignatureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Custom Signature Text</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control" id="ipSignatureInput" maxlength="60" placeholder="Authorized Signatory">
            </div>
            <div class="modal-footer">
                <button type="button" class="ip-btn ip-btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="ip-btn ip-btn-primary" id="ipSignatureApply">Done</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade ip-modal" id="ipPrinterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Default Thermal Printer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="ip-printer-head">
                    <span class="ip-printer-status" id="ipPrinterStatus">Looking for printers…</span>
                    <span class="ip-printer-actions">
                        <button type="button" class="ip-chip" id="ipPrinterRefresh"><i class="fas fa-sync-alt"></i> Refresh</button>
                        <button type="button" class="ip-chip" id="ipPrinterScan"><i class="fab fa-bluetooth-b"></i> Search nearby</button>
                    </span>
                </div>
                <div class="ip-printer-list" id="ipPrinterList"></div>
                <label class="form-label small text-muted mb-1 mt-3" for="ipPrinterInput">Or type a printer name</label>
                <input type="text" class="form-control" id="ipPrinterInput" placeholder="e.g. POS-80 or TVS RP 3160">
                <small class="text-muted d-block mt-2">The chosen printer is selected automatically in the print window. On a PC, keep QZ Tray running. Tap Search nearby to add a Bluetooth printer.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="ip-btn ip-btn-light" id="ipPrinterClear">Clear</button>
                <button type="button" class="ip-btn ip-btn-primary" id="ipPrinterApply">Done</button>
            </div>
        </div>
    </div>
</div>

<style>
    .ip-page {
        min-height: calc(100vh - 60px);
        padding: 20px 16px 40px;
        background: #e8f1fb;
    }
    .ip-shell { max-width: 760px; margin: 0 auto; }
    .ip-top { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
    .ip-back {
        display: inline-flex; align-items: center; justify-content: center;
        width: 34px; height: 34px; border-radius: 50%;
        color: #2f3447 !important; font-size: 18px;
    }
    .ip-back:hover { background: rgba(0, 0, 0, .05); }
    .ip-title { flex: 1 1 auto; margin: 0; color: #2f3447; font-size: 22px; font-weight: 500; }
    .ip-top-actions { display: flex; gap: 8px; }
    .ip-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        height: 36px; padding: 0 14px; border: 1px solid transparent; border-radius: 8px;
        font-size: 13px; font-weight: 600; cursor: pointer;
    }
    .ip-btn-primary { background: #e8334a; border-color: #e8334a; color: #fff; }
    .ip-btn-primary:disabled { opacity: .6; }
    .ip-btn-light { background: #fff; border-color: #d6dbe4; color: #2f3447; }

    .ip-tabs { display: flex; margin-bottom: 14px; background: #fff; border-radius: 10px 10px 0 0; overflow: hidden; }
    .ip-tab {
        flex: 1 1 50%; padding: 14px 8px; border: 0; border-bottom: 3px solid #b7bfcc;
        background: #fff; color: #9aa1b1; font-size: 16px;
    }
    .ip-tab.is-active { border-bottom-color: #e8334a; color: #e8334a; }

    .ip-card { margin-bottom: 14px; overflow: hidden; background: #fff; border-radius: 10px; }
    .ip-card-title {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 14px 12px; border-bottom: 1px solid #e6e9ef;
        color: #2f3447; font-size: 16px; font-weight: 600;
    }
    .ip-edit { color: #2f3447 !important; font-size: 16px; }
    .ip-row {
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
        width: 100%; min-height: 62px; margin: 0; padding: 10px 14px 10px 18px;
        border: 0; border-bottom: 1px solid #e6e9ef; background: #fff;
        color: #2f3447; font-size: 15px; text-align: left; cursor: pointer;
    }
    .ip-card .ip-row:last-child, .ip-card .ip-row:nth-last-child(2):has(+ input[type="hidden"]) { border-bottom: 0; }
    .ip-row-link:hover { background: #fafbfd; }
    .ip-label { flex: 1 1 auto; min-width: 0; line-height: 1.3; }
    .ip-info { margin-left: 6px; color: #70778a; font-size: 15px; cursor: help; }
    .ip-value { display: inline-flex; align-items: center; gap: 10px; min-width: 0; color: #70778a; }
    .ip-value-text { max-width: 180px; overflow: hidden; font-size: 14px; text-overflow: ellipsis; white-space: nowrap; }
    .ip-value .fa-chevron-right { color: #70778a; }
    .ip-swatch-dot { width: 20px; height: 20px; border: 2px solid #fff; border-radius: 50%; box-shadow: 0 0 0 1px #d6dbe4; }

    .ip-switch { position: relative; flex: 0 0 auto; width: 62px; height: 36px; }
    .ip-switch input { position: absolute; width: 0; height: 0; opacity: 0; }
    .ip-slider {
        position: absolute; inset: 0; border: 1px solid #d8dbe2; border-radius: 999px;
        background: #ececef; transition: background .2s;
    }
    .ip-slider::before {
        content: ''; position: absolute; top: 3px; left: 3px; width: 28px; height: 28px;
        border-radius: 50%; background: #fff; box-shadow: 0 1px 3px rgba(0, 0, 0, .25); transition: transform .2s;
    }
    .ip-slider::after {
        content: ''; position: absolute; top: 50%; right: 10px; width: 9px; height: 9px;
        margin-top: -5px; border: 1.5px solid #b7bcc6; border-radius: 50%;
    }
    .ip-switch input:checked + .ip-slider { border-color: #0a6cdc; background: #0a6cdc; }
    .ip-switch input:checked + .ip-slider::before { transform: translateX(26px); }
    .ip-switch input:checked + .ip-slider::after {
        right: auto; left: 13px; width: 1.5px; height: 11px; margin-top: -6px;
        border: 0; border-radius: 0; background: #fff;
    }
    .ip-switch input:focus-visible + .ip-slider { outline: 2px solid #0a6cdc; outline-offset: 2px; }

    .ip-select-wrap { position: relative; display: inline-flex; align-items: center; max-width: 60%; color: #70778a; }
    .ip-select {
        max-width: 100%; padding: 4px 22px 4px 4px; border: 0; background: transparent;
        color: #70778a; font-size: 15px; text-align: right; text-align-last: right;
        appearance: none; -webkit-appearance: none; cursor: pointer;
    }
    .ip-select:focus { outline: none; }
    .ip-select-wrap .fa-chevron-down { position: absolute; right: 2px; font-size: 12px; pointer-events: none; }

    .ip-stepper {
        display: inline-flex; flex: 0 0 auto; align-items: center; height: 40px;
        border: 1px solid #b9bfcc; border-radius: 999px; overflow: hidden;
    }
    .ip-step { width: 38px; height: 100%; border: 0; background: transparent; color: #2f3447; font-size: 14px; }
    .ip-step:active { background: #f1f3f7; }
    .ip-stepper input {
        width: 40px; height: 100%; padding: 0; border: 0; background: transparent;
        color: #2f3447; font-size: 15px; text-align: center;
    }
    .ip-stepper input:focus { outline: none; }

    .ip-savebar {
        position: fixed; right: 0; bottom: 0; left: 0; z-index: 1030;
        padding: 10px 12px calc(10px + env(safe-area-inset-bottom));
        background: #fff; box-shadow: 0 -2px 10px rgba(16, 24, 40, .12);
    }
    .ip-savebar .ip-btn { height: 46px; font-size: 15px; }

    .ip-modal .modal-content { border: 0; border-radius: 14px; }
    .ip-swatches { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 16px; }
    .ip-swatch {
        aspect-ratio: 1; border: 3px solid #fff; border-radius: 50%;
        box-shadow: 0 0 0 1px #d6dbe4; cursor: pointer;
    }
    .ip-swatch.is-active { box-shadow: 0 0 0 3px #2f3447; }
    .ip-custom-color { display: flex; align-items: center; justify-content: space-between; margin: 0; font-size: 14px; }
    .ip-custom-color input { width: 56px; height: 36px; padding: 2px; border: 1px solid #d6dbe4; border-radius: 8px; }
    .ip-printer-head { display: flex; align-items: center; justify-content: space-between; gap: 8px; flex-wrap: wrap; margin-bottom: 10px; }
    .ip-printer-status { font-size: 13px; color: #6b7280; }
    .ip-printer-actions { display: flex; gap: 6px; }
    .ip-chip {
        padding: 5px 10px; border: 1px solid #0a6cdc; border-radius: 999px;
        background: #fff; color: #0a6cdc; font-size: 12px; font-weight: 600; white-space: nowrap;
    }
    .ip-chip:disabled { opacity: .55; }
    .ip-printer-list { display: flex; flex-direction: column; gap: 8px; max-height: 260px; overflow-y: auto; }
    .ip-printer-group { margin: 4px 0 0; font-size: 11px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; color: #8a93a6; }
    .ip-printer-item {
        display: flex; align-items: center; gap: 10px; width: 100%; padding: 10px 12px;
        border: 1px solid #e3e8f0; border-radius: 10px; background: #fff; text-align: left; font-size: 14px; color: #2f3447;
    }
    .ip-printer-item i.ip-printer-icon { width: 18px; color: #0a6cdc; text-align: center; }
    .ip-printer-item .ip-printer-name { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ip-printer-item .ip-printer-tag { font-size: 11px; color: #8a93a6; }
    .ip-printer-item .ip-printer-check { color: #0a6cdc; visibility: hidden; }
    .ip-printer-item.is-selected { border-color: #0a6cdc; background: #eef5ff; }
    .ip-printer-item.is-selected .ip-printer-check { visibility: visible; }
    .ip-printer-empty { padding: 12px; border-radius: 10px; background: #f3f6fb; font-size: 13px; color: #6b7280; text-align: center; }
    :root[data-theme="dark"] .ip-printer-item { background: #1e293b; border-color: #334155; color: #f1f5f9; }
    :root[data-theme="dark"] .ip-printer-item.is-selected { background: #1e3a5f; }
    :root[data-theme="dark"] .ip-printer-empty, :root[data-theme="dark"] .ip-chip { background: #1e293b; }

    @media (max-width: 767.98px) {
        .ip-page { padding: 0 0 90px; }
        .ip-top { margin: 0; padding: 12px 10px; background: #fff; }
        .ip-title { font-size: 20px; }
        .ip-top-actions .ip-btn span { display: none; }
        .ip-tabs { border-radius: 0; }
        .ip-pane { padding: 0 10px; }
        .ip-label, .ip-select { font-size: 14px; }
        .ip-row { min-height: 58px; padding-left: 16px; }
        .ip-savebar { bottom: 60px; }
        body:has(.ip-modal.show) .ip-savebar { display: none; }
    }

    :root[data-theme="dark"] .ip-page { background: #0f172a; }
    :root[data-theme="dark"] .ip-top,
    :root[data-theme="dark"] .ip-tabs,
    :root[data-theme="dark"] .ip-tab,
    :root[data-theme="dark"] .ip-card,
    :root[data-theme="dark"] .ip-row,
    :root[data-theme="dark"] .ip-savebar { background: #1e293b; }
    :root[data-theme="dark"] .ip-title,
    :root[data-theme="dark"] .ip-card-title,
    :root[data-theme="dark"] .ip-row,
    :root[data-theme="dark"] .ip-edit,
    :root[data-theme="dark"] .ip-back,
    :root[data-theme="dark"] .ip-step,
    :root[data-theme="dark"] .ip-stepper input { color: #f1f5f9 !important; }
    :root[data-theme="dark"] .ip-card-title,
    :root[data-theme="dark"] .ip-row { border-color: #334155; }
</style>

@include('partials.invoice-qz-print-script')
@endsection

@push('js')
<script>
    $(function() {
        const $page = $('#invoicePrintPage');
        const previewOrderId = @json($previewOrderId);
        let dirty = false;
        let saving = false;

        function markDirty() { dirty = true; }

        function warnUnsaved() {
            return $('[data-group="regular"][data-key="warn_unsaved"]').is(':checked');
        }

        window.addEventListener('beforeunload', function(e) {
            if (dirty && warnUnsaved()) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        $page.on('click', '.ip-tab', function() {
            const tab = $(this).data('tab');
            $('.ip-tab').removeClass('is-active');
            $(this).addClass('is-active');
            $('.ip-pane').addClass('d-none');
            $('.ip-pane[data-pane="' + tab + '"]').removeClass('d-none');
        });

        $page.on('change', '.ip-default-toggle', function() {
            const printer = $(this).data('printer');
            const other = printer === 'regular' ? 'thermal' : 'regular';
            const value = $(this).is(':checked') ? printer : other;
            $('#ipDefaultPrinter').val(value);
            $('.ip-default-toggle').each(function() {
                $(this).prop('checked', $(this).data('printer') === value);
            });
            markDirty();
        });

        $page.on('change', '[data-group]', markDirty);

        $page.on('click', '.ip-info', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const label = $(this).closest('.ip-label').clone().children().remove().end().text().trim();
            Swal.fire({ title: label, text: $(this).data('info'), confirmButtonColor: '#e8334a' });
        });

        $page.on('click', '.ip-step', function() {
            const $input = $(this).siblings('input');
            const min = parseInt($input.data('min'), 10) || 0;
            const max = parseInt($input.data('max'), 10) || 99;
            let value = (parseInt($input.val(), 10) || 0) + parseInt($(this).data('step'), 10);
            value = Math.max(min, Math.min(max, value));
            $input.val(String(value).padStart(2, '0')).trigger('change');
        });

        $page.on('click', '[data-open]', function() {
            const target = $(this).data('open');
            if (target === '#ipTermsModal') $('#ipTermsInput').val($('#ipTermsValue').val());
            if (target === '#ipSignatureModal') $('#ipSignatureInput').val($('#ipSignatureValue').val());
            if (target === '#ipPrinterModal') openPrinterPicker();
            bootstrap.Modal.getOrCreateInstance(document.querySelector(target)).show();
        });

        function setThemeColor(color) {
            $('#ipThemeColor').val(color);
            $('#ipThemeDot').css('background', color);
            $('#ipCustomColor').val(color);
            $('.ip-swatch').removeClass('is-active').filter('[data-color="' + color + '"]').addClass('is-active');
            markDirty();
        }
        $(document).on('click', '.ip-swatch', function() { setThemeColor($(this).data('color')); });
        $(document).on('input change', '#ipCustomColor', function() { setThemeColor($(this).val()); });

        $(document).on('click', '#ipTermsApply', function() {
            $('#ipTermsValue').val($('#ipTermsInput').val());
            markDirty();
            bootstrap.Modal.getInstance(document.getElementById('ipTermsModal')).hide();
        });

        $(document).on('click', '#ipSignatureApply', function() {
            const text = $('#ipSignatureInput').val().trim() || 'Authorized Signatory';
            $('#ipSignatureValue').val(text);
            $('#ipSignatureLabel').text(text);
            markDirty();
            bootstrap.Modal.getInstance(document.getElementById('ipSignatureModal')).hide();
        });

        let printerRows = [];
        let pickedPrinter = null;

        function printerApi() {
            return window.InvoiceQzPrint || null;
        }

        function printerIcon(kind) {
            if (kind === 'bluetooth') return 'fab fa-bluetooth-b';
            if (kind === 'wifi') return 'fas fa-wifi';
            return 'fas fa-print';
        }

        function renderPrinterList() {
            const $list = $('#ipPrinterList').empty();
            const current = pickedPrinter ? pickedPrinter.value : '';
            if (!printerRows.length) {
                $list.append($('<div class="ip-printer-empty"></div>').text('No printers found. Start QZ Tray and tap Refresh, or tap Search nearby for a Bluetooth printer.'));
                return;
            }
            const groups = [
                ['Connected printers', printerRows.filter(function(r) { return r.kind !== 'bluetooth'; })],
                ['Bluetooth printers', printerRows.filter(function(r) { return r.kind === 'bluetooth'; })]
            ];
            groups.forEach(function(group) {
                if (!group[1].length) return;
                $list.append($('<div class="ip-printer-group"></div>').text(group[0]));
                group[1].forEach(function(row) {
                    const tag = row.kind === 'thermal' ? 'Thermal' : (row.kind === 'bluetooth' ? 'Bluetooth' : (row.kind === 'wifi' ? 'Wi-Fi' : 'PC'));
                    const $btn = $('<button type="button" class="ip-printer-item"></button>')
                        .toggleClass('is-selected', row.value === current)
                        .data('row', row);
                    $btn.append($('<i class="ip-printer-icon"></i>').addClass(printerIcon(row.kind)));
                    $btn.append($('<span class="ip-printer-name"></span>').text(row.label));
                    $btn.append($('<span class="ip-printer-tag"></span>').text(tag));
                    $btn.append('<i class="fas fa-check-circle ip-printer-check"></i>');
                    $list.append($btn);
                });
            });
        }

        function addPrinterRow(row) {
            if (!row || !row.value) return;
            if (!printerRows.some(function(r) { return r.value === row.value; })) printerRows.push(row);
        }

        function loadPrinters() {
            const api = printerApi();
            const $status = $('#ipPrinterStatus');
            if (!api || typeof api.listAllPrinters !== 'function') {
                $status.text('Printer finder not loaded. Reload the page.');
                renderPrinterList();
                return Promise.resolve();
            }
            $status.text('Looking for printers…');
            $('#ipPrinterRefresh').prop('disabled', true);
            return api.listAllPrinters().then(function(res) {
                printerRows = [];
                (res.printers || []).forEach(addPrinterRow);
                if (pickedPrinter) addPrinterRow(pickedPrinter);
                const found = printerRows.length ? printerRows.length + ' printer(s) found' : 'No printers found';
                $status.text(res.qzConnected ? found : found + ' · QZ Tray not connected');
                renderPrinterList();
            }).catch(function() {
                $status.text('Could not list printers');
                renderPrinterList();
            }).then(function() {
                $('#ipPrinterRefresh').prop('disabled', false);
            });
        }

        function openPrinterPicker() {
            const value = $('#ipPrinterValue').val();
            pickedPrinter = value ? {
                value: value,
                label: $('#ipPrinterLabelValue').val() || value,
                kind: /^(bt|serial):/.test(value) ? 'bluetooth' : (value.indexOf('wifi:') === 0 ? 'wifi' : 'pc')
            } : null;
            printerRows = pickedPrinter ? [pickedPrinter] : [];
            $('#ipPrinterInput').val('');
            renderPrinterList();
            loadPrinters();
        }

        $(document).on('click', '.ip-printer-item', function() {
            pickedPrinter = $(this).data('row');
            $('#ipPrinterInput').val('');
            renderPrinterList();
        });
        $(document).on('input', '#ipPrinterInput', function() {
            if ($(this).val().trim()) {
                pickedPrinter = null;
                renderPrinterList();
            }
        });
        $(document).on('click', '#ipPrinterRefresh', loadPrinters);
        $(document).on('click', '#ipPrinterScan', function() {
            const api = printerApi();
            if (!api || typeof api.scanBluetoothPrinter !== 'function') return;
            const $btn = $(this).prop('disabled', true);
            api.scanBluetoothPrinter().then(function(row) {
                if (!row) return;
                addPrinterRow(row);
                pickedPrinter = row;
                $('#ipPrinterInput').val('');
                renderPrinterList();
                $('#ipPrinterStatus').text(row.label + ' added');
            }).catch(function(err) {
                Swal.fire({ icon: 'info', title: 'Bluetooth', text: (err && err.message) || 'Could not search for Bluetooth printers.', confirmButtonColor: '#e8334a' });
            }).then(function() {
                $btn.prop('disabled', false);
            });
        });
        function setPrinter(value, label) {
            $('#ipPrinterValue').val(value);
            $('#ipPrinterLabelValue').val(value ? (label || value) : '');
            $('#ipPrinterLabel').text(value ? (label || value) : 'Not set');
            markDirty();
            bootstrap.Modal.getInstance(document.getElementById('ipPrinterModal')).hide();
        }
        $(document).on('click', '#ipPrinterApply', function() {
            const typed = $('#ipPrinterInput').val().trim();
            if (typed) setPrinter(typed, typed);
            else if (pickedPrinter) setPrinter(pickedPrinter.value, pickedPrinter.label);
            else setPrinter('', '');
        });
        $(document).on('click', '#ipPrinterClear', function() { setPrinter('', ''); });

        function collect() {
            const data = { regular: {}, thermal: {} };
            $page.find('[data-group][data-key]').each(function() {
                const $el = $(this);
                const group = $el.data('group');
                const key = $el.data('key');
                let value;
                if ($el.data('type') === 'toggle') {
                    const on = $el.is(':checked');
                    if (group === 'thermal' && key === 'language') value = on ? 'hindi' : 'english';
                    else if (group === 'thermal' && key === 'decimal_places') value = on ? (parseInt($el.data('on'), 10) || 2) : 0;
                    else value = on ? 1 : 0;
                } else if ($el.data('type') === 'number') {
                    value = parseInt($el.val(), 10) || 0;
                } else {
                    value = $el.val();
                }
                data[group][key] = value;
            });
            data.default_printer = $('#ipDefaultPrinter').val();
            data.terms = $('#ipTermsValue').val();
            return data;
        }

        function save() {
            if (saving) return Promise.resolve(false);
            saving = true;
            const $buttons = $('#ipSaveTop, #ipSaveBottom').prop('disabled', true);
            return $.ajax({
                url: $page.data('save-url'),
                method: 'POST',
                contentType: 'application/json',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: JSON.stringify(collect())
            }).then(function(res) {
                dirty = false;
                const printer = $('#ipPrinterValue').val();
                if (printer) {
                    try {
                        localStorage.setItem('posSelectedPrinter', printer);
                        localStorage.setItem('posDefaultPrinterApplied', printer);
                    } catch (e) {}
                }
                Swal.fire({ icon: 'success', title: 'Saved', text: res.message || 'Invoice print settings saved', timer: 1400, showConfirmButton: false });
                return true;
            }, function(xhr) {
                Swal.fire({ icon: 'error', title: 'Not saved', text: (xhr.responseJSON && xhr.responseJSON.message) || 'Please try again.', confirmButtonColor: '#e8334a' });
                return false;
            }).always(function() {
                saving = false;
                $buttons.prop('disabled', false);
            });
        }

        $(document).on('click', '#ipSaveTop, #ipSaveBottom', save);

        $(document).on('click', '#ipPreview', function() {
            if (!previewOrderId) return;
            const mode = $('.ip-tab.is-active').data('tab') === 'thermal' ? 'thermal' : 'regular';
            const url = '/sales/invoice/pdf/' + encodeURIComponent(previewOrderId) + '?print_mode=' + mode;
            const win = window.open('about:blank', '_blank');
            const go = function() { if (win) win.location.href = url; else window.open(url, '_blank'); };
            if (dirty) {
                save().then(function(ok) { if (ok) go(); else if (win) win.close(); });
            } else {
                go();
            }
        });
    });
</script>
@endpush

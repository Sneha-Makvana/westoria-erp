@extends('layout.app')

@section('title', 'Product List')

@section('content')
    @include('inventory.partials.mobile-ui-css')
    <style>
        .sorting_1 {
            display: flex !important;
            align-items: center !important;
            gap: 5px !important;
        }

        /* ── The Sticker Label Format ── */
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

        /*
         * Compact (phone + tablet) barcode popup — driven by .pb-compact class
         * so it works even when media queries / DevTools quirks don't match.
         */
        body.pb-compact-open .mobile-bottom-nav,
        body.pb-compact-open .floating-calculator-wrapper {
            display: none !important;
        }
        #printBarcodeModal.pb-compact {
            top: 0 !important;
            padding: 0 !important;
            overflow: hidden !important;
        }
        #printBarcodeModal.pb-compact.show {
            display: flex !important;
            align-items: flex-end !important;
        }
        #printBarcodeModal.pb-compact .modal-dialog,
        #printBarcodeModal.pb-compact .modal-dialog.modal-lg {
            flex: 1 1 100% !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            transform: none !important;
        }
        #printBarcodeModal.pb-compact .modal-content {
            display: flex !important;
            flex-direction: column !important;
            width: 100% !important;
            max-width: 100% !important;
            max-height: 92dvh !important;
            margin: 0 !important;
            border: 0 !important;
            border-radius: 18px 18px 0 0 !important;
            overflow: hidden !important;
        }
        #printBarcodeModal.pb-compact .modal-header {
            flex: 0 0 auto !important;
            align-items: flex-start !important;
            gap: 10px !important;
            padding: 16px 16px 12px !important;
            border-bottom: 1px solid #eef0f4 !important;
        }
        #printBarcodeModal.pb-compact .modal-title {
            display: block !important;
            min-width: 0 !important;
            font-size: 17px !important;
            font-weight: 700 !important;
            color: #1b2850 !important;
            line-height: 1.3 !important;
        }
        #printBarcodeModal.pb-compact .modal-title > i {
            color: #ff9f43 !important;
        }
        #printBarcodeModal.pb-compact .pb-title-sep {
            display: none !important;
        }
        #printBarcodeModal.pb-compact #pbProductName {
            display: block !important;
            width: 100% !important;
            margin-top: 3px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            color: #8a92a6 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        #printBarcodeModal.pb-compact .modal-header .btn-close {
            flex: 0 0 32px !important;
            width: 32px !important;
            height: 32px !important;
            margin: 0 !important;
            padding: 0 !important;
            border-radius: 50% !important;
            background: #f2f4f7 !important;
            color: #475467 !important;
            opacity: 1 !important;
            font-size: 14px !important;
            font-weight: 700 !important;
            line-height: 32px !important;
            text-align: center !important;
        }
        #printBarcodeModal.pb-compact .modal-body {
            flex: 1 1 auto !important;
            min-height: 0 !important;
            max-height: none !important;
            overflow-y: auto !important;
            padding: 14px 16px 4px !important;
        }
        /* Phone default: single column stack */
        #printBarcodeModal.pb-compact .modal-body .row {
            --bs-gutter-x: 0 !important;
            --bs-gutter-y: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            flex-wrap: nowrap !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }
        #printBarcodeModal.pb-compact .modal-body .row > [class*="col-"],
        #printBarcodeModal.pb-compact .modal-body .row > .col-md-4,
        #printBarcodeModal.pb-compact .modal-body .row > .col-6,
        #printBarcodeModal.pb-compact .modal-body .row > .col-md-12 {
            flex: 0 0 auto !important;
            flex-basis: auto !important;
            width: 100% !important;
            max-width: 100% !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        #printBarcodeModal.pb-compact .modal-body .mb-3 {
            margin-bottom: 12px !important;
        }

        /* Tablet: 3-column grid — less scroll */
        #printBarcodeModal.pb-compact.pb-tablet .modal-body {
            padding: 12px 16px 6px !important;
        }
        #printBarcodeModal.pb-compact.pb-tablet .modal-body .row {
            display: grid !important;
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            column-gap: 12px !important;
            row-gap: 4px !important;
            flex-direction: unset !important;
            flex-wrap: unset !important;
            align-items: start !important;
        }
        #printBarcodeModal.pb-compact.pb-tablet .modal-body .row > [class*="col-"],
        #printBarcodeModal.pb-compact.pb-tablet .modal-body .row > .col-md-4,
        #printBarcodeModal.pb-compact.pb-tablet .modal-body .row > .col-6,
        #printBarcodeModal.pb-compact.pb-tablet .modal-body .row > .col-md-12 {
            width: auto !important;
            max-width: none !important;
            min-width: 0 !important;
            margin-bottom: 10px !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        /* Hidden cols must not take a grid cell */
        #printBarcodeModal.pb-compact.pb-tablet .modal-body .row > .d-none {
            display: none !important;
        }
        /* Preview spans full width under the fields */
        #printBarcodeModal.pb-compact.pb-tablet .modal-body .row > .pb-preview-col {
            grid-column: 1 / -1 !important;
            width: 100% !important;
            margin-top: 4px !important;
        }
        #printBarcodeModal.pb-compact.pb-tablet .modal-body .form-text {
            display: -webkit-box !important;
            -webkit-line-clamp: 2 !important;
            -webkit-box-orient: vertical !important;
            overflow: hidden !important;
            font-size: 10px !important;
        }
        /* Footer: printer + actions on one/two compact rows */
        #printBarcodeModal.pb-compact.pb-tablet .pb-print-footer {
            display: grid !important;
            grid-template-columns: minmax(0, 1.4fr) auto minmax(0, 1fr) !important;
            align-items: center !important;
            gap: 8px 10px !important;
        }
        #printBarcodeModal.pb-compact.pb-tablet .pb-print-target {
            flex: none !important;
            width: auto !important;
            max-width: none !important;
            grid-column: 1 / 2 !important;
        }
        #printBarcodeModal.pb-compact.pb-tablet #pbBtLanguageWrap:not(.d-none) {
            flex: none !important;
            width: auto !important;
            max-width: none !important;
            grid-column: 2 / 3 !important;
        }
        #printBarcodeModal.pb-compact.pb-tablet .pb-print-actions {
            flex: none !important;
            width: auto !important;
            max-width: none !important;
            grid-column: 3 / 4 !important;
            display: flex !important;
            gap: 8px !important;
            justify-content: flex-end !important;
        }
        #printBarcodeModal.pb-compact.pb-tablet .pb-print-actions .btn {
            min-height: 42px !important;
            padding: 0 16px !important;
            font-size: 14px !important;
            white-space: nowrap !important;
        }
        #printBarcodeModal.pb-compact .modal-body .form-label {
            margin-bottom: 5px !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #667085 !important;
        }
        #printBarcodeModal.pb-compact .modal-body .form-control,
        #printBarcodeModal.pb-compact .modal-body .form-select {
            height: 42px !important;
            border-radius: 10px !important;
            border-color: #e4e7ec !important;
            font-size: 14px !important;
        }
        #printBarcodeModal.pb-compact #add_pbBarcode_btn {
            height: 42px !important;
            border-radius: 10px !important;
            background: #1b2850 !important;
            border-color: #1b2850 !important;
            font-weight: 600 !important;
        }
        #printBarcodeModal.pb-compact .modal-body .form-text,
        #printBarcodeModal.pb-compact .modal-body small.text-muted {
            font-size: 11px !important;
            line-height: 1.3 !important;
        }
        #printBarcodeModal.pb-compact .pb-preview-col {
            align-items: stretch !important;
            padding: 12px !important;
            margin: 0 0 12px !important;
            flex: 0 0 auto !important;
            width: 100% !important;
            border-radius: 12px !important;
            background: #f6f7fb !important;
        }
        #printBarcodeModal.pb-compact .pb-preview-label {
            display: block !important;
            margin-bottom: 8px !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            letter-spacing: .04em !important;
            text-transform: uppercase !important;
            color: #8a92a6 !important;
        }
        #printBarcodeModal.pb-compact #pbBarcodeImagesContainer {
            justify-content: center !important;
            flex-wrap: wrap !important;
            gap: 8px !important;
        }
        #printBarcodeModal.pb-compact .modal-footer {
            flex: 0 0 auto !important;
            padding: 12px 16px calc(12px + env(safe-area-inset-bottom)) !important;
            border-top: 1px solid #eef0f4 !important;
            box-shadow: 0 -4px 12px rgba(16, 24, 40, .05) !important;
        }
        #printBarcodeModal.pb-compact .pb-print-footer {
            display: flex !important;
            flex-wrap: wrap !important;
            flex-direction: row !important;
            align-items: stretch !important;
            gap: 10px !important;
            width: 100% !important;
        }
        #printBarcodeModal.pb-compact .pb-print-target,
        #printBarcodeModal.pb-compact #pbBtLanguageWrap,
        #printBarcodeModal.pb-compact .pb-print-actions {
            flex: 1 1 100% !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
        }
        #printBarcodeModal.pb-compact .pb-print-target {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }
        #printBarcodeModal.pb-compact .pb-print-target select,
        #printBarcodeModal.pb-compact #pbBtLanguageWrap select {
            height: 40px !important;
            border-radius: 10px !important;
            font-size: 13px !important;
            min-width: 0 !important;
            flex: 1 1 auto !important;
        }
        #printBarcodeModal.pb-compact #pbBtChooseBtn {
            height: 40px !important;
            padding: 0 12px !important;
            border-radius: 10px !important;
            display: inline-flex !important;
            align-items: center !important;
            flex: 0 0 auto !important;
        }
        #printBarcodeModal.pb-compact .pb-print-actions {
            display: grid !important;
            grid-template-columns: 1fr 1.4fr !important;
            gap: 10px !important;
        }
        #printBarcodeModal.pb-compact .pb-print-actions .btn {
            min-height: 46px !important;
            border-radius: 10px !important;
            font-size: 15px !important;
            font-weight: 600 !important;
        }
        #printBarcodeModal.pb-compact .pb-print-actions .btn-secondary {
            background: #f2f4f7 !important;
            border-color: #f2f4f7 !important;
            color: #344054 !important;
        }
        #printBarcodeModal.pb-compact #pbBtStatus:empty {
            display: none !important;
        }
        :root[data-theme="dark"] #printBarcodeModal.pb-compact .pb-preview-col {
            background: #0f172a !important;
        }
        :root[data-theme="dark"] #printBarcodeModal.pb-compact .modal-title {
            color: #f8fafc !important;
        }

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
        #mobilePrintOverlay .mobile-print-actions {
            flex: 0 0 auto;
            padding: 12px 14px calc(12px + env(safe-area-inset-bottom));
            background: #fff;
            border-top: 1px solid #dee2e6;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        #mobilePrintOverlay .mobile-print-actions .btn {
            flex: 1 1 calc(50% - 5px);
            min-width: 120px;
            font-weight: 600;
        }
        #mobilePrintOverlay .mobile-print-actions .btn-print-main {
            flex: 1 1 100%;
            background: #ff9f43;
            border-color: #ff9f43;
            color: #fff;
            padding: 14px 12px;
            font-size: 16px;
        }
        #mobilePrintOpenPdfBtn {
            display: none;
        }
        #mobilePrintOpenPdfBtn.show {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Styling for the barcode chips */
        .barcode-chip {
            display: inline-flex;
            align-items: center;
            background-color: #f1f3f5;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 4px 8px;
            font-size: 13px;
            font-weight: 500;
            color: #495057;
        }
        .barcode-chip .remove-chip {
            margin-left: 6px;
            cursor: pointer;
            color: #868e96;
            font-weight: bold;
        }
        .barcode-chip .remove-chip:hover {
            color: #dc3545;
        }

        .nav-tabs-solid .nav-link {
            background-color: transparent !important;
            color: #6c757d !important;
            border: 1px solid #dee2e6 !important;
            margin-right: 6px;
            border-radius: 6px !important;
            font-weight: 600;
            padding: 8px 16px;
        }

        .nav-tabs-solid .nav-link.active {
            background-color: #ff9f43 !important;
            border-color: #ff9f43 !important;
            color: #fff !important;
        }

        .product-list-tabs {
            margin-bottom: 14px;
        }

        .product-list-tabs .nav-tabs {
            border-bottom: 0;
        }

        body.showing-deleted-products .page-btn-active-only {
            display: none !important;
        }
        html[data-theme="dark"] .barcode-chip,
        [data-layout-mode="dark"] .barcode-chip,
        .dark .barcode-chip,
        body.dark .barcode-chip {
            background-color: #1f2937;
            border-color: #374151;
            color: #ffffff;
        }

        /* .category-width{
                            width: 207px;
                        } */
        .product-name-link {
            font-weight: 500;
            margin-left: 8px;
            color: #1b2850;
            text-decoration: none;
        }

        html[data-theme="dark"] .product-name-link,
        [data-layout-mode="dark"] .product-name-link,
        .dark .product-name-link,
        body.dark .product-name-link {
            color: #ffffff !important;
        }

        #filterCategory,
        #filterBrand {
            width: 100% !important;
        }

        #filterDate {
            width: 100% !important;
        }

        .product-toolbar-filters .form-group.pl-date-field {
            flex: 0 0 160px;
            min-width: 0;
        }

        .product-toolbar-filters .form-group.pl-date-range {
            flex: 0 0 280px;
            min-width: 0;
        }

        .pl-date-range input[type="date"] {
            height: 38px;
            font-size: 13px;
        }

        .pl-date-range input[type="date"] {
            min-width: 0;
            flex: 1 1 0;
        }

        .pl-filter-sheet-body .pl-date-range {
            min-width: 0;
        }

        .pl-page .product-toolbar-filters .category-width.pl-date-range.is-hidden,
        .pl-page .pl-filter-sheet-body .category-width.pl-date-range.is-hidden,
        .pl-filter-sheet-body .category-width.pl-date-range.is-hidden {
            display: none !important;
        }

        .table-scroll-top {
            overflow-x: auto;
            overflow-y: hidden;
            height: 20px;
            width: 100%;
            margin-bottom: 5px;
        }

        .table-scroll-top div {
            height: 1px;
        }

        .table-scroll-top {
            display: none;
        }

        /* Word wrapping + compact desktop layout */
        table.datanew {
            table-layout: auto !important;
            table-layout: fixed !important;
        }

        table.datanew td,
        table.datanew th {
            white-space: normal !important;
            word-break: break-word !important;
            overflow-wrap: anywhere !important;
            vertical-align: middle;
            padding: 10px 8px !important;
        }

        /* Keep short-value columns compact so extra blank gaps are reduced */
        table.datanew thead th:nth-child(3),
        table.datanew tbody td:nth-child(3),
        table.datanew thead th:nth-child(5),
        table.datanew tbody td:nth-child(5),
        table.datanew thead th:nth-child(7),
        table.datanew tbody td:nth-child(7),
        table.datanew thead th:nth-child(8),
        table.datanew tbody td:nth-child(8) {
            width: 1%;
            white-space: nowrap !important;
        }

        /* Long text columns should wrap cleanly */
        table.datanew tbody td:nth-child(1),
        table.datanew tbody td:nth-child(4),
        table.datanew tbody td:nth-child(6),
        table.datanew tbody td:nth-child(9) {
            white-space: normal !important;
            word-break: break-word !important;
            overflow-wrap: anywhere !important;
            line-height: 1.3;
        }


        @media (min-width: 1025px) and (max-width: 1199.98px) {
            .category-width {
                width: 207px;
            }
            
            .product-toolbar {
                display: flex;
                flex-wrap: wrap;
                gap: 15px;
                justify-content: space-between;
                align-items: flex-end;
                margin-bottom: 15px;
            }

            .product-toolbar-filters {
                display: flex;
                flex-wrap: wrap;
                gap: 15px;
                align-items: flex-end;
                flex: 1;
            }
            
            .product-toolbar-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                align-items: flex-end;
            }

            /* .product-toolbar {
                        flex-wrap: nowrap;
                    }

                    .product-toolbar-filters {
                        flex-wrap: nowrap;
                    }

                    .product-toolbar-filters .form-group,
                    .product-toolbar-filters .category-width {
                        flex: 0 0 207px;
                        max-width: 207px;
                    } */
        }

        @media screen and (max-width: 1199px) {
            .table-scroll-top {
                display: block;
                -webkit-overflow-scrolling: touch !important;
                /* smooth scrolling on iOS */
            }

            select#filterCategory {
                font-size: 12px !important;
            }

            select#filterBrand {
                font-size: 12px !important;
                width: 164px !important;
            }

            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch !important;
            }

            .form-control-sm {
                min-height: calc(1.5em + .5rem + 2px);
                padding: .25rem .5rem;
                font-size: 13px !important;
                border-radius: .2rem;
            }

            .search-set {
                margin-right: 1rem !important;
            }
        }

        .dataTables_filter,
        .dataTables_length,
        .dataTables_info,
        .dataTables_paginate {
            display: none !important;
        }

        .dataTables_wrapper .row:first-child {
            display: none !important;
        }

        .dataTables_wrapper {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }

        .pagination .page-item .page-link {
            background-color: #5d6d7e;
            color: #fff;
            border: none;
            margin: 0 3px;
            margin: 0 3px;
            padding: 4px 10px;
            font-weight: bold;
        }

        .pagination .page-item.active .page-link {
            background-color: #ff9f43 !important;
            color: #fff;
        }

        .pagination .page-item .page-link:hover {
            background-color: #4a5766;
            color: #fff;
        }

        .pagination .page-item.active .page-link:hover {
            background-color: #e68a35 !important;
        }

        /* Previous and Next buttons */
        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
            background-color: #fff;
            color: #6c757d;
            border: 1px solid #dee2e6;
        }

        .pagination .page-item:first-child .page-link:hover,
        .pagination .page-item:last-child .page-link:hover {
            background-color: #f8f9fa;
            color: #495057;
            border-color: #dee2e6;
        }

        .pagination .page-item.disabled .page-link {
            background-color: #fff !important;
            color: #dee2e6 !important;
            border: 1px solid #dee2e6 !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
        }

        .product-toolbar {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .product-toolbar-filters {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            flex-wrap: wrap;
            flex: 1 1 auto;
            min-width: 0;
        }

        .product-toolbar-search {
            flex: 1 1 320px;
            min-width: 220px;
            max-width: 260px;
        }

        .product-toolbar-filters .form-group {
            flex: 0 0 180px;
        }

        /* .product-toolbar-filters .form-group,
                .product-toolbar-filters .category-width {
                    flex: 1 1 200px;
                    min-width: 180px;
                    max-width: 260px;
                }

                .product-toolbar-filters .select2-container {
                    width: 100% !important;
                } */

        .search-input input {
            padding-left: 35px !important;
            border-radius: 5px;
            width: 100%;
        }

        .search-input {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .btn-searchset {
            position: absolute;
            left: 10px;
            z-index: 10;
            padding: 0;
            top: 7px !important;
        }

        .product-toolbar-actions {
            display: flex;
            gap: 8px;
            margin-left: auto;
            flex-wrap: wrap;
        }

        .product-toolbar-actions .btn {
            white-space: nowrap;
        }

        /* Action column */
        table.datanew th:last-child,
        table.datanew td:last-child {
            width: 60px !important;
            min-width: 60px !important;
            max-width: 60px !important;
            text-align: center;
            white-space: nowrap !important;
        }

        /* Desktop: show all columns normally */
        @media (min-width: 1025px) and (max-width: 1199.98px) {
            .product-toolbar {
                flex-wrap: nowrap;
                align-items: flex-end;
            }

            .product-toolbar-filters {
                flex-wrap: nowrap;
            }

            .product-toolbar-actions {
                margin-left: auto;
                flex-wrap: nowrap;
            }

            .pl-page > .card,
            .pl-page > .card > .card-body,
            .pl-page .table-responsive {
                width: 100%;
                max-width: none;
            }

            .pl-page .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .pl-page table.datanew {
                width: 100% !important;
            }

            table.datanew thead th,
            table.datanew tbody td {
                display: table-cell !important;
            }

            /* Hide the Details toggle column on desktop */
            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(2) {
                display: none !important;
            }

            /* Desktop column sizing to avoid extra white gaps */
            table.datanew thead th:nth-child(1),
            table.datanew tbody td:nth-child(1) {
                width: 24% !important;
            }

            table.datanew thead th:nth-child(3),
            table.datanew tbody td:nth-child(3) {
                width: 12% !important;
            }

            table.datanew thead th:nth-child(4),
            table.datanew tbody td:nth-child(4) {
                width: 13% !important;
            }

            table.datanew thead th:nth-child(5),
            table.datanew tbody td:nth-child(5) {
                width: 7% !important;
            }

            table.datanew thead th:nth-child(6),
            table.datanew tbody td:nth-child(6) {
                width: 10% !important;
            }

            table.datanew thead th:nth-child(7),
            table.datanew tbody td:nth-child(7),
            table.datanew thead th:nth-child(8),
            table.datanew tbody td:nth-child(8),
            table.datanew thead th:nth-child(9),
            table.datanew tbody td:nth-child(9) {
                width: 8% !important;
                text-align: center;
                white-space: nowrap !important;
            }

            table.datanew thead th.pl-col-print,
            table.datanew tbody td.pl-col-print {
                width: 6% !important;
                text-align: center;
            }

            table.datanew thead th.pl-col-action,
            table.datanew tbody td.pl-col-action {
                width: 4% !important;
                text-align: center;
            }
        }
        /* Desktop (1025px and above): hide Details toggle column */
        @media (min-width: 1025px) {
            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(2) {
                display: none !important;
            }

            /* Details column hide thay etle bakina columns ni width fix karo (overlap na thay) */
            table.datanew {
                table-layout: auto !important;
                width: 100% !important;
            }

            table.datanew thead th:nth-child(3),
            table.datanew tbody td:nth-child(3) {
                width: 14% !important;
                white-space: normal !important;
                word-break: break-all !important;
            }

            table.datanew thead th:nth-child(4),
            table.datanew tbody td:nth-child(4),
            table.datanew thead th:nth-child(6),
            table.datanew tbody td:nth-child(6) {
                width: 12% !important;
                white-space: normal !important;
                word-break: break-word !important;
            }

            table.datanew thead th:nth-child(5),
            table.datanew tbody td:nth-child(5) {
                width: 7% !important;
                white-space: nowrap !important;
            }
        }
        /* Mobile: hide non-essential columns, show Details toggle */
        @media (max-width: 1024.98px), (width: 1032px) and (height: 1376px), (width: 1280px) and (height: 800px) {

            table.datanew thead th:nth-child(n+3),
            table.datanew tbody td:nth-child(n+3) {
                display: none !important;
            }

            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(2) {
                display: table-cell !important;
                text-align: center;
                vertical-align: top !important;
                width: 56px !important;
                min-width: 56px !important;
                max-width: 56px !important;
                padding: 12px 6px !important;
            }

            .toggle-details i {
                font-size: 18px;
            }

            .action-buttons {
                flex-wrap: wrap;
                gap: 8px;
            }

            /* Add these styles for product name wrapping */
            table.datanew tbody td:first-child {
                /* display: flex !important; */
                align-items: center !important;
                max-width: calc(100vw - 100px) !important;
                /* Adjust based on your layout */
            }

            table.datanew tbody td:first-child a {
                /* display: -webkit-box !important; */
                display: -ms-flexbox !important;
                /* display: flex !important; */
                -webkit-box-align: center !important;
                -ms-flex-align: center !important;
                align-items: center !important;
                text-align: left !important;
                max-width: 100% !important;
                word-wrap: break-word !important;
                word-break: break-word !important;
                overflow-wrap: break-word !important;
                white-space: normal !important;
                line-height: 1.3 !important;
            }

            /* For the product name text specifically */
            table.datanew tbody td:first-child a[style*="font-weight: 500"] {
                display: inline-block !important;
                max-width: calc(100% - 70px) !important;
                /* Account for image width */
                margin-left: 8px !important;
                font-size: 14px !important;
                word-break: break-word !important;
                hyphens: auto !important;
                -webkit-hyphens: auto !important;
                -ms-hyphens: auto !important;
            }

            /* If you want to limit to 2 lines with ellipsis */
            table.datanew tbody td:first-child a.product-name {
                display: -webkit-box !important;
                -webkit-line-clamp: 2 !important;
                -webkit-box-orient: vertical !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }

            .product-toolbar {
                gap: 10px;
                align-items: stretch;
            }

            .product-toolbar-filters {
                width: 100%;
                gap: 10px;
            }

            .product-toolbar-filters .form-group,
            .product-toolbar-filters .category-width {
                flex: 1 1 calc(50% - 5px);
                min-width: 150px;
                max-width: none;
            }

            .product-toolbar-search {
                flex: 1 1 100%;
                min-width: 100%;
            }

            .product-toolbar-actions {
                width: 100%;
                margin-left: 0;
                justify-content: space-between;
            }

            #exportAllChallan,
            #exportPdf,
            #downloadAllBarcodes {
                width: 31% !important;
                flex: 0 0 31% !important;
                max-width: 31% !important;
                min-width: 31% !important;
                margin: 0 !important;
                padding: 6px 0px !important;
                font-size: 12px !important;
            }

            #exportAllChallan i,
            #exportPdf i,
            #downloadAllBarcodes i {
                font-size: 12px !important;
                margin-right: 3px !important;
            }
        }

        /* Tablet specific fixes */
        @media screen and (min-width: 769px) and (max-width: 1024.98px) {

            /* Ensure table is properly responsive */
            .table-responsive {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
            }

            /* Make sure details column is visible and properly sized */
            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(2) {
                display: table-cell !important;
                width: 60px !important;
                min-width: 60px !important;
                max-width: 60px !important;
            }

            /* Ensure toggle icon is visible and clickable */
            .toggle-details {
                display: inline-block !important;
                padding: 8px !important;
                z-index: 10 !important;
            }

            .toggle-details i {
                font-size: 20px !important;
                width: 24px !important;
                height: 24px !important;
                line-height: 24px !important;
            }

            /* Adjust product name width for tablet */
            table.datanew tbody td:first-child a[style*="font-weight: 500"] {
                max-width: calc(100% - 80px) !important;
                font-size: 14px !important;
            }
        }

        /* ===== Action Column UI Fix ===== */

        table.datanew td:last-child {
            white-space: normal !important;
        }

        /* Action column fix - keep in one row */
        .action-buttons {
            display: flex;
            flex-wrap: nowrap;
            /* ✅ Prevent wrapping */
            align-items: center;
            gap: 10px;
            white-space: nowrap;
            /* ✅ Keep buttons in one line */
        }

        .action-buttons .btn {
            font-size: 12px;
            padding: 4px 8px;
        }

        .icon-btn {
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            background: #f5f5f5;
        }

        .icon-btn:hover {
            background: #ececec;
        }

        .product-action-dropdown {
            display: inline-flex;
            justify-content: center;
            width: 100%;
        }

        .product-action-dropdown .action-menu-toggle {
            align-items: center;
            background: #fff;
            border: 1px solid #d7dde8;
            border-radius: 4px;
            color: #1b2850;
            display: inline-flex;
            height: 28px;
            justify-content: center;
            padding: 0;
            width: 34px;
        }

        .product-action-dropdown .action-menu-toggle:hover,
        .product-action-dropdown .action-menu-toggle:focus {
            background: #f8fafc;
            border-color: #b8c2d2;
            color: #1b2850;
        }

        .product-action-menu {
            border: 1px solid #e8ebed;
            border-radius: 6px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.12);
            min-width: 168px;
            padding: 6px;
            z-index: 9999;
        }

        .product-action-menu-floating {
            position: fixed !important;
            z-index: 999999 !important;
        }

        .product-action-menu .dropdown-item {
            align-items: center;
            border-radius: 4px;
            color: #344054;
            display: flex;
            font-size: 12px;
            gap: 8px;
            padding: 7px 8px;
            width: 100%;
        }

        .product-action-menu .dropdown-item i {
            color: #667085;
            font-size: 13px;
            text-align: center;
            width: 15px;
        }

        .product-action-menu .dropdown-item.text-danger,
        .product-action-menu .dropdown-item.text-danger i {
            color: #ea5455 !important;
        }

        .product-action-menu .dropdown-item:hover {
            background: #f4f6f8;
            color: #1b2850;
        }

        #quantityHistoryModal .modal-content {
            max-height: 85vh;
        }

        #quantityHistoryModal .modal-body,
        #quantityHistoryModal #quantityHistoryContent {
            max-height: calc(85vh - 130px);
            overflow-y: auto;
            overflow-x: hidden;
        }

        #quantityHistoryModal .quantity-history-close {
            border: 0;
            background: transparent;
            width: 30px;
            height: 30px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            line-height: 1;
            color: #000;
            opacity: 1;
        }

        #quantityHistoryModal .quantity-history-close:hover {
            background: #ea5455;
            color: #fff;
        }

        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 15px;
            }

            .page-header .page-btn {
                width: 100%;
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }

            .page-header .page-btn .btn-added {
                flex: 1 1 calc(50% - 8px);
                justify-content: center;
                align-items: center;
                text-align: center;
                padding: 8px;
                font-size: 12px;
                white-space: nowrap;
                display: flex;
            }
        }
        
        .variants-modal-header {
            background-color: #f8f9fa;
        }

        html[data-theme="dark"] #variantsModal .variants-modal-header,
        [data-layout-mode="dark"] #variantsModal .variants-modal-header,
        .dark #variantsModal .variants-modal-header,
        body.dark #variantsModal .variants-modal-header {
            background-color: #1a2234 !important;
            border-bottom-color: #2b3a53;
        }

        html[data-theme="dark"] #variantsModal .variants-modal-header .modal-title,
        [data-layout-mode="dark"] #variantsModal .variants-modal-header .modal-title,
        .dark #variantsModal .variants-modal-header .modal-title,
        body.dark #variantsModal .variants-modal-header .modal-title {
            color: #ffffff !important;
        }

        html[data-theme="dark"] #variantsModal .variants-close-btn,
        [data-layout-mode="dark"] #variantsModal .variants-close-btn,
        .dark #variantsModal .variants-close-btn,
        body.dark #variantsModal .variants-close-btn {
            background-color: #2b3a53 !important;
            border-color: #2b3a53 !important;
            color: #ffffff !important;
        }

        html[data-theme="dark"] #variantsModal .variants-btn-close,
        [data-layout-mode="dark"] #variantsModal .variants-btn-close,
        .dark #variantsModal .variants-btn-close,
        body.dark #variantsModal .variants-btn-close {
            color: #ffffff !important;
        }

        .pl-m-meta {
            display: none;
        }
        .product-toolbar-search .search-input {
            position: relative;
        }
        .pl-scan-btn {
            position: absolute;
            top: 50%;
            right: 4px;
            transform: translateY(-50%);
            z-index: 3;
            width: 34px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 8px;
            background: #ff9f43;
            color: #fff;
            font-size: 14px;
        }
        @media (max-width: 991.98px), (width: 1032px) and (height: 1376px), (width: 1280px) and (height: 800px) {
    .product-toolbar-search #search-input { padding-right: 44px !important; }
}
        #plScanReader {
            width: 100%;
            min-height: 240px;
            border-radius: 10px;
            overflow: hidden;
            background: #000;
        }
        #plScanReader video {
            width: 100% !important;
            object-fit: cover;
        }
        .pl-row-main {
            min-width: 0;
        }
        .pl-m-card {
            display: none;
        }

        @media (max-width: 1024.98px), (width: 1032px) and (height: 1376px), (width: 1280px) and (height: 800px) {
            .pl-page table.datanew tbody tr > td:not(:first-child):not(.dataTables_empty),
            .pl-page table.datanew .pl-row-head,
            .pl-page table.datanew .pl-details {
                display: none !important;
            }
            .pl-page table.datanew tbody tr {
                padding: 12px !important;
                border: 0 !important;
                border-radius: 10px !important;
                box-shadow: 0 1px 3px rgba(16, 24, 40, 0.08) !important;
            }
            .pl-page .pl-m-card {
                display: grid;
                grid-template-columns: 44px minmax(0, 1fr);
                column-gap: 10px;
                row-gap: 8px;
            }
            .pl-m-img {
                width: 44px;
                height: 44px;
                border-radius: 8px;
                overflow: hidden;
                background: #f3f4f6;
            }
            .pl-m-img img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            .pl-m-body {
                min-width: 0;
            }
            .pl-m-top {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 8px;
            }
            .pl-m-name {
                font-size: 14px;
                font-weight: 600;
                color: #1b2850 !important;
                text-transform: uppercase;
                line-height: 1.3;
                min-width: 0;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .pl-m-amount {
                font-size: 14px;
                font-weight: 700;
                color: #1b2850;
                white-space: nowrap;
            }
            .pl-m-badges {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 4px 8px;
                margin-top: 5px;
                min-width: 0;
            }
            .pl-m-badge {
                font-size: 10px;
                font-weight: 600;
                padding: 2px 9px;
                border-radius: 12px;
                white-space: nowrap;
            }
            .pl-m-badge.is-ok {
                background: #e7f8ef;
                color: #12a150;
            }
            .pl-m-badge.is-low {
                background: #fdecec;
                color: #e5483d;
            }
            .pl-m-badge.is-deleted {
                background: #f2f4f7;
                color: #667085;
            }
            .pl-m-code {
                font-size: 11px;
                color: #8b93a7;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                max-width: 100%;
            }
            .pl-m-var {
                font-size: 11px;
                font-weight: 600;
                color: #ff9f43 !important;
                background: #fff4e8;
                border-radius: 12px;
                padding: 2px 9px;
            }
            .pl-m-foot {
                grid-column: 1 / -1;
                display: flex;
                align-items: flex-end;
                gap: 16px;
                padding-top: 8px;
                border-top: 1px dashed #eceef3;
            }
            .pl-m-stat {
                min-width: 0;
            }
            .pl-m-stat span {
                display: block;
                font-size: 11px;
                color: #8b93a7;
            }
            .pl-m-stat strong {
                display: block;
                font-size: 13px;
                color: #1b2850;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .pl-m-stat.quantity-history {
                cursor: pointer;
            }
            .pl-m-cat {
                flex: 1 1 auto;
            }
            .pl-m-acts {
                margin-left: auto;
                display: flex;
                align-items: center;
                gap: 2px;
                flex-shrink: 0;
            }
            .pl-m-acts > .text-center {
                display: contents;
            }
            .pl-m-acts .quick-print-barcode-btn,
            .pl-m-acts .quick-print-barcode-btn:hover,
            .pl-m-acts .quick-print-barcode-btn:focus,
            .pl-m-acts .quick-print-barcode-btn:active {
                flex: 0 0 32px;
                width: 32px !important;
                min-width: 32px !important;
                height: 32px !important;
                padding: 0 !important;
                border: 0 !important;
                border-radius: 8px !important;
                background: transparent !important;
                color: #8b93a7 !important;
                box-shadow: none !important;
                outline: 0 !important;
                font-size: 0 !important;
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
            }
            .pl-m-acts .quick-print-barcode-btn:active {
                background: #f1f2f6 !important;
            }
            .pl-m-acts .quick-print-barcode-btn i {
                font-size: 17px;
                margin: 0 !important;
            }
            .pl-m-acts .action-menu-toggle {
                width: 32px;
                height: 32px;
                font-size: 17px;
                box-shadow: none !important;
                padding: 0 !important;
                border: 0 !important;
                background: transparent !important;
                color: #8b93a7 !important;
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
            }
            .pl-m-acts .action-menu-toggle i {
                transform: rotate(90deg);
            }
        }

        @media screen and (min-width: 768px) and (max-width: 1024.98px), screen and (width: 1032px) and (height: 1376px), screen and (width: 1280px) and (height: 800px) {
            .pl-page .table-scroll-top {
                display: none !important;
            }
            .pl-page .table-responsive {
                overflow: visible !important;
                border: 0 !important;
            }
            .pl-page table.datanew {
                display: block !important;
                width: 100% !important;
                margin: 0 !important;
                border: 0 !important;
            }
            .pl-page table.datanew thead {
                display: none !important;
            }
            .pl-page table.datanew tbody {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                align-items: start;
                gap: 10px;
                width: 100%;
            }
            .pl-page table.datanew tbody > tr {
                display: block !important;
                width: auto !important;
                min-width: 0;
                margin: 0 !important;
                padding: 12px !important;
                border: 1px solid #eceef3 !important;
                border-radius: 12px !important;
                background: #fff !important;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .05);
            }
            .pl-page table.datanew tbody > tr > td {
                background: transparent !important;
                border: 0 !important;
                box-shadow: none !important;
            }
            .pl-page table.datanew tbody > tr > td:first-child {
                display: block !important;
                width: 100% !important;
                min-width: 0;
                max-width: none !important;
                padding: 0 !important;
            }
            .pl-page table.datanew tbody > tr:has(> td.dataTables_empty) {
                grid-column: 1 / -1;
            }
            .pl-page table.datanew tbody > tr > td.dataTables_empty {
                display: block !important;
                width: 100% !important;
            }
        }

        /* Phone header + filters */
        @media (max-width: 767.98px) {
            /* Product cards */
            .pl-page .table-scroll-top {
                display: none !important;
            }
            .pl-page .table-responsive {
                overflow: visible !important;
                border: 0 !important;
            }
            .pl-page table.datanew {
                display: block !important;
                width: 100% !important;
                margin: 0 !important;
                border: 0 !important;
            }
            .pl-page table.datanew thead {
                display: none !important;
            }
            .pl-page table.datanew tbody {
                display: flex !important;
                flex-direction: column;
                gap: 8px;
                width: 100%;
            }
            .pl-page table.datanew tbody tr {
                display: flex !important;
                align-items: flex-start;
                gap: 6px;
                width: 100%;
                padding: 10px !important;
                border: 1px solid #eceef3 !important;
                border-radius: 12px;
                background: #fff !important;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .05);
            }
            .pl-page table.datanew tbody tr > td {
                background: transparent !important;
                border: 0 !important;
                box-shadow: none !important;
            }
            .pl-page table.datanew tbody tr > td:first-child {
                display: block !important;
                flex: 1 1 auto;
                min-width: 0;
                max-width: none !important;
                width: auto !important;
                padding: 0 !important;
            }
            .pl-page table.datanew tbody tr > td:nth-child(2) {
                flex: 0 0 34px;
                width: 34px !important;
                min-width: 34px !important;
                max-width: 34px !important;
                padding: 0 !important;
            }
            .pl-page table.datanew tbody tr > td.dataTables_empty {
                width: 100% !important;
                text-align: center;
                color: #8a92a6;
            }
            .pl-page .toggle-details {
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
                width: 34px;
                height: 34px;
                border-radius: 10px;
                background: #fff6ec;
            }
            .pl-page .toggle-details i {
                font-size: 18px !important;
            }

            .pl-page .pl-row-head {
                align-items: center;
                gap: 10px;
            }
            .pl-page .pl-row-head .product-img {
                flex: 0 0 48px;
                width: 48px;
                height: 48px;
                margin: 0 !important;
                border-radius: 10px;
                overflow: hidden;
                background: #f3f4f6;
            }
            .pl-page .pl-row-head .product-img img {
                width: 48px !important;
                height: 48px !important;
                max-width: none !important;
                object-fit: cover;
            }
            .pl-page .pl-row-main {
                flex: 1 1 auto;
                display: flex;
                flex-direction: column;
                gap: 3px;
            }
            .pl-page .pl-row-main .product-name-link {
                display: -webkit-box !important;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                color: #1b2850 !important;
                font-size: 13px !important;
                font-weight: 700 !important;
                line-height: 1.3 !important;
            }
            .pl-page .pl-m-meta {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                gap: 4px 8px;
            }
            .pl-page .pl-m-sku {
                font-size: 11px;
                color: #8a92a6;
                word-break: break-all;
            }
            .pl-page .pl-m-stats {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                margin-left: auto;
            }
            .pl-page .pl-m-price {
                font-size: 13px;
                font-weight: 800;
                color: #1a9e4b;
            }
            .pl-page .pl-m-qty {
                padding: 2px 8px;
                border-radius: 999px;
                background: #f1f2f6;
                color: #1b2850;
                font-size: 11px;
                font-weight: 700;
                cursor: pointer;
            }
            .pl-page .pl-m-qty.is-low {
                background: #fdecec;
                color: #e5484d;
            }
            .pl-page .pl-row-head .view-variants-btn {
                margin: 0 !important;
            }

            .pl-page .pl-details {
                margin-top: 10px !important;
                padding-top: 10px;
                border-top: 1px dashed #e5e7eb;
            }
            .pl-page .pl-details-info {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px 10px;
            }
            .pl-page .pl-details-info p {
                margin: 0 !important;
                min-width: 0;
                font-size: 12px;
                color: #1b2850;
                word-break: break-word;
            }
            .pl-page .pl-details-info p strong {
                display: block;
                font-size: 10px;
                font-weight: 600;
                color: #8a92a6;
                text-transform: uppercase;
                letter-spacing: .03em;
            }
            .pl-page .pl-details-info p div {
                max-width: none !important;
            }
            .pl-page .pl-details-print {
                margin: 10px 0 8px !important;
            }
            .pl-page .pl-details-print .text-center {
                text-align: left !important;
            }
            .pl-page .pl-details-print .btn {
                width: 100%;
                height: 36px;
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
                border-radius: 10px !important;
                font-size: 13px !important;
                font-weight: 600;
            }
            .pl-page .pl-details-actions {
                margin-top: 0 !important;
            }
            .pl-page .pl-details-actions .action-buttons {
                display: grid !important;
                grid-template-columns: repeat(auto-fill, minmax(38px, 1fr));
                gap: 6px !important;
            }
            .pl-page .pl-details-actions .action-buttons .icon-btn {
                width: 100%;
                height: 38px;
                border-radius: 10px;
                background: #f4f5f8;
            }
            .pl-page .pl-details-actions .action-buttons .btn {
                grid-column: span 2;
                height: 38px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 10px !important;
                font-size: 12px !important;
            }

            .pl-page.content {
                padding-left: 8px !important;
                padding-right: 8px !important;
            }
            .pl-page .page-header {
                position: relative;
                display: block !important;
                margin-bottom: 10px !important;
                padding: 0 !important;
            }
            .pl-page .page-header .page-title {
                min-height: 34px;
                display: flex;
                align-items: center;
                padding-right: 130px;
                margin: 0 0 8px !important;
            }
            .pl-page .page-header .page-title h4 {
                margin: 0 !important;
                font-size: 17px !important;
            }
            .pl-page .page-header .page-btn {
                display: flex !important;
                flex-wrap: nowrap !important;
                gap: 6px !important;
                margin: 0 !important;
                overflow-x: auto;
                scrollbar-width: none;
                -webkit-overflow-scrolling: touch;
            }
            .pl-page .page-header .page-btn::-webkit-scrollbar {
                display: none;
            }
            .pl-page .page-header .page-btn > .btn {
                flex: 0 0 auto;
                width: auto !important;
                height: 32px !important;
                margin: 0 !important;
                padding: 0 12px !important;
                line-height: 30px !important;
                align-items: center;
                border-radius: 999px !important;
                font-size: 12px !important;
                font-weight: 600;
                white-space: nowrap;
            }
            .pl-page .page-header .page-btn > .pl-new-product-btn {
                position: absolute;
                top: 0;
                right: 0;
                height: 34px !important;
                padding: 0 14px !important;
                border-radius: 10px !important;
                background: #1b2850 !important;
                border-color: #1b2850 !important;
                color: #fff !important;
            }

            .pl-page .product-list-tabs {
                margin-bottom: 10px !important;
            }
            .pl-page .product-list-tabs .nav-tabs {
                display: grid !important;
                grid-template-columns: 1fr 1fr;
                gap: 0;
                padding: 3px;
                border: 0 !important;
                border-radius: 10px;
                background: #f1f2f6;
            }
            .pl-page .product-list-tabs .nav-item {
                margin: 0 !important;
                width: auto !important;
            }
            .pl-page .product-list-tabs .nav-link {
                display: flex !important;
                align-items: center;
                justify-content: center;
                width: 100%;
                height: 34px;
                margin: 0 !important;
                padding: 0 8px !important;
                border: 0 !important;
                border-radius: 8px !important;
                background: transparent !important;
                color: #5b6670 !important;
                font-size: 12px !important;
                font-weight: 600;
                white-space: nowrap;
            }
            .pl-page .product-list-tabs .nav-link.active {
                background: #ff9f43 !important;
                color: #fff !important;
                box-shadow: 0 1px 3px rgba(0, 0, 0, .12);
            }

            .pl-page > .card > .card-body {
                padding: 10px !important;
            }
            .pl-page .product-toolbar {
                gap: 8px !important;
                margin-bottom: 10px !important;
            }
            .pl-page .product-toolbar-filters {
                display: grid !important;
                grid-template-columns: 1fr 1fr;
                gap: 8px !important;
                width: 100%;
            }
            .pl-page .product-toolbar-search {
                grid-column: 1 / -1;
                width: 100% !important;
                max-width: none !important;
            }
            .pl-page .product-toolbar-search > .form-label {
                display: none !important;
            }
            .pl-page .product-toolbar-filters .category-width {
                width: auto !important;
                max-width: none !important;
                min-width: 0;
                flex: none !important;
            }
            .pl-page .product-toolbar-filters .form-label {
                margin-bottom: 2px !important;
                font-size: 11px !important;
                color: #8a92a6;
            }
            .pl-page .product-toolbar-filters .form-control,
            .pl-page .product-toolbar-search input {
                height: 38px !important;
                font-size: 13px !important;
            }
            .pl-page .product-toolbar-actions {
                display: grid !important;
                grid-template-columns: repeat(3, 1fr);
                gap: 6px !important;
                width: 100%;
            }
            .pl-page .product-toolbar-actions #exportAllChallan,
            .pl-page .product-toolbar-actions #exportPdf,
            .pl-page .product-toolbar-actions #downloadAllBarcodes {
                width: 100% !important;
                min-width: 0 !important;
                max-width: none !important;
                flex: none !important;
                gap: 5px;
                overflow: hidden;
            }
            .pl-page .product-toolbar-actions #exportAllChallan i,
            .pl-page .product-toolbar-actions #exportPdf i,
            .pl-page .product-toolbar-actions #downloadAllBarcodes i {
                margin: 0 !important;
            }
            .pl-page .product-toolbar-actions .btn {
                width: 100% !important;
                height: 34px !important;
                margin: 0 !important;
                padding: 0 6px !important;
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
                border-radius: 8px !important;
                font-size: 12px !important;
                white-space: nowrap;
            }
        }

        /* Phone header panel (Sales style) */
        .pl-filter-backdrop,
        .pl-filter-sheet {
            display: none;
        }
        @media (max-width: 767.98px) {
            .pl-page.content {
                padding-left: 6px !important;
                padding-right: 6px !important;
            }
            .pl-page > .page-header,
            .pl-page .product-toolbar-actions,
            .pl-page .product-toolbar-filters > .category-width {
                display: none !important;
            }
            .pl-m-head {
                background: #fff;
                border-radius: 12px 12px 0 0;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
                padding: 12px 10px 10px;
            }
            .pl-m-head-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                margin-bottom: 10px;
            }
            .pl-m-title {
                margin-left: 2px;
                font-size: 15px;
                font-weight: 600;
                color: #1b2850;
                white-space: nowrap;
            }
            .pl-m-head-actions {
                display: flex;
                gap: 6px;
                flex-shrink: 0;
            }
            .pl-m-head-actions .btn {
                display: inline-flex;
                align-items: center;
                height: 30px;
                margin: 0 !important;
                padding: 0 10px !important;
                font-size: 12px !important;
                font-weight: 600;
                white-space: nowrap;
                border-radius: 8px !important;
                background: #ff9f43 !important;
                border: 1px solid #ff9f43 !important;
                color: #fff !important;
            }
            .pl-m-head-actions .pl-m-new {
                background: #1b2850 !important;
                border-color: #1b2850 !important;
            }
            .pl-m-tools {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 6px;
            }
            .pl-m-tool {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                min-width: 0;
                height: 34px;
                padding: 0 6px;
                border: 0;
                border-radius: 8px;
                font-size: 11.5px;
                font-weight: 600;
                text-decoration: none;
                white-space: nowrap;
            }
            .pl-m-tool span {
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .pl-m-tool i {
                flex: 0 0 auto;
                font-size: 13px;
            }
            .pl-m-tool.is-info { background: #e6f8fc; color: #0aa2c0; }
            .pl-m-tool.is-orange { background: #fff3e6; color: #e07b14; }
            .pl-m-tool.is-green { background: #e7f6ec; color: #198754; }
            .pl-m-tool.is-red { background: #fdecee; color: #dc3545; }
            .pl-m-tool.is-navy { background: #e9ecf5; color: #1b2850; }

            .pl-page .product-list-tabs {
                margin: 0 0 8px !important;
                padding: 0 10px 10px;
                background: #fff;
                border-radius: 0 0 12px 12px;
                box-shadow: 0 2px 3px rgba(16, 24, 40, .06);
            }

            .pl-page > .card {
                background: transparent !important;
                border: 0 !important;
                box-shadow: none !important;
                margin: 0 !important;
            }
            .pl-page > .card > .card-body {
                padding: 0 !important;
            }
            .pl-page .product-toolbar {
                margin: 0 0 8px !important;
                padding: 8px 10px !important;
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
            }
            .pl-page .product-toolbar-filters {
                display: block !important;
            }
            .pl-page .product-toolbar-search {
                display: flex !important;
                align-items: center;
                gap: 8px;
            }
            .pl-page .product-toolbar-search .search-input {
                flex: 1 1 auto;
                min-width: 0;
                width: auto !important;
                margin: 0 !important;
            }
            .pl-filter-toggle {
                position: relative;
                flex: 0 0 38px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 38px;
                height: 38px;
                padding: 0;
                border: 0;
                border-radius: 50%;
                background: #f05a6a;
                color: #fff;
                font-size: 15px;
                box-shadow: 0 2px 6px rgba(240, 90, 106, .35);
            }
            .pl-filter-toggle.has-active {
                background: #e8334a;
            }
            .pl-filter-count {
                position: absolute;
                top: -4px;
                right: -4px;
                min-width: 18px;
                height: 18px;
                padding: 0 5px;
                border-radius: 999px;
                background: #1b2850;
                color: #fff;
                font-size: 11px;
                line-height: 18px;
                text-align: center;
            }

            .pl-filter-backdrop {
                display: block;
                position: fixed;
                inset: 0;
                z-index: 1040;
                background: rgba(16, 24, 40, .45);
                opacity: 0;
                pointer-events: none;
                transition: opacity .2s ease;
            }
            .pl-filter-sheet {
                position: fixed;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 1045;
                display: flex;
                flex-direction: column;
                max-height: 85vh;
                background: #fff;
                border-radius: 16px 16px 0 0;
                box-shadow: 0 -8px 24px rgba(16, 24, 40, .18);
                transform: translateY(105%);
                transition: transform .25s ease;
            }
            body.pl-filter-open .pl-filter-backdrop {
                opacity: 1;
                pointer-events: auto;
            }
            body.pl-filter-open .pl-filter-sheet {
                transform: translateY(0);
            }
            body.pl-filter-open {
                overflow: hidden;
            }
            body.pl-filter-open .mobile-bottom-nav,
            body.pl-filter-open .floating-calculator-wrapper {
                display: none !important;
            }
            .pl-filter-sheet-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 16px 18px;
                border-bottom: 1px solid #eef0f4;
                font-size: 18px;
                font-weight: 600;
                color: #1b2850;
            }
            .pl-filter-sheet-close {
                width: 32px;
                height: 32px;
                padding: 0;
                border: 0;
                background: transparent;
                color: #2f6fd8;
                font-size: 20px;
            }
            .pl-filter-sheet-body {
                flex: 1 1 auto;
                overflow-y: auto;
                padding: 4px 18px;
            }
            .pl-filter-sheet-body .category-width {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                flex: none !important;
                margin: 0 !important;
                padding: 12px 0 !important;
                border-bottom: 1px solid #eef0f4;
            }
            .pl-filter-sheet-body .form-label {
                display: block;
                margin: 0 0 6px !important;
                font-size: 14px !important;
                color: #344054 !important;
            }
            .pl-filter-sheet-body .select2-container {
                width: 100% !important;
            }
            .pl-filter-sheet-body .select2-container .select2-selection--single {
                height: 40px !important;
                border-radius: 8px !important;
            }
            .pl-filter-sheet-body .select2-container .select2-selection--single .select2-selection__rendered {
                line-height: 40px !important;
            }
            .pl-filter-sheet-body .select2-container .select2-selection--single .select2-selection__arrow {
                height: 38px !important;
            }
            .pl-filter-sheet-foot {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 12px;
                padding: 14px 18px calc(14px + env(safe-area-inset-bottom));
                border-top: 1px solid #eef0f4;
            }
            .pl-filter-clear,
            .pl-filter-apply {
                height: 46px;
                border: 0;
                border-radius: 24px;
                font-size: 15px;
                font-weight: 600;
            }
            .pl-filter-clear {
                background: #eef0f4;
                color: #475467;
            }
            .pl-filter-apply {
                background: #e8334a;
                color: #fff;
            }
            body.pl-filter-open .select2-container--open .select2-dropdown {
                z-index: 1060;
            }

            :root[data-theme="dark"] .pl-m-head,
            :root[data-theme="dark"] .pl-page .product-list-tabs,
            :root[data-theme="dark"] .pl-page .product-toolbar,
            :root[data-theme="dark"] .pl-filter-sheet {
                background: #1e293b;
            }
            :root[data-theme="dark"] .pl-m-title,
            :root[data-theme="dark"] .pl-filter-sheet-head,
            :root[data-theme="dark"] .pl-filter-sheet-body .form-label {
                color: #f8fafc !important;
            }
        }

        @media screen and (min-width: 768px) and (max-width: 1024.98px), screen and (width: 1032px) and (height: 1376px), screen and (width: 1280px) and (height: 800px) {
            .pl-page.content {
                padding-left: 6px !important;
                padding-right: 6px !important;
            }
            .pl-page > .page-header,
            .pl-page .product-toolbar-actions,
            .pl-page .product-toolbar-filters > .category-width {
                display: none !important;
            }
            .pl-page .pl-m-head.d-md-none {
                display: block !important;
                background: #fff;
                border-radius: 12px 12px 0 0;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
                padding: 12px 10px 10px;
            }
            .pl-m-head-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                margin-bottom: 10px;
            }
            .pl-m-title {
                margin-left: 2px;
                font-size: 15px;
                font-weight: 600;
                color: #1b2850;
                white-space: nowrap;
            }
            .pl-m-head-actions {
                display: flex;
                gap: 6px;
                flex-shrink: 0;
            }
            .pl-m-head-actions .btn {
                display: inline-flex;
                align-items: center;
                height: 30px;
                margin: 0 !important;
                padding: 0 10px !important;
                font-size: 12px !important;
                font-weight: 600;
                white-space: nowrap;
                border-radius: 8px !important;
                background: #ff9f43 !important;
                border: 1px solid #ff9f43 !important;
                color: #fff !important;
            }
            .pl-m-head-actions .pl-m-new {
                background: #1b2850 !important;
                border-color: #1b2850 !important;
            }
            .pl-m-tools {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 6px;
            }
            .pl-m-tool {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                min-width: 0;
                height: 34px;
                padding: 0 6px;
                border: 0;
                border-radius: 8px;
                font-size: 11.5px;
                font-weight: 600;
                text-decoration: none;
                white-space: nowrap;
            }
            .pl-m-tool span {
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .pl-m-tool i {
                flex: 0 0 auto;
                font-size: 13px;
            }
            .pl-m-tool.is-info { background: #e6f8fc; color: #0aa2c0; }
            .pl-m-tool.is-orange { background: #fff3e6; color: #e07b14; }
            .pl-m-tool.is-green { background: #e7f6ec; color: #198754; }
            .pl-m-tool.is-red { background: #fdecee; color: #dc3545; }
            .pl-m-tool.is-navy { background: #e9ecf5; color: #1b2850; }

            .pl-page .product-list-tabs {
                margin: 0 0 8px !important;
                padding: 0 10px 10px;
                background: #fff;
                border-radius: 0 0 12px 12px;
                box-shadow: 0 2px 3px rgba(16, 24, 40, .06);
            }
            .pl-page .product-list-tabs .nav-tabs {
                display: grid !important;
                grid-template-columns: 1fr 1fr;
                gap: 0;
                padding: 3px;
                border: 0 !important;
                border-radius: 10px;
                background: #f1f2f6;
            }
            .pl-page .product-list-tabs .nav-item {
                margin: 0 !important;
                width: auto !important;
            }
            .pl-page .product-list-tabs .nav-link {
                display: flex !important;
                align-items: center;
                justify-content: center;
                width: 100%;
                height: 34px;
                margin: 0 !important;
                padding: 0 8px !important;
                border: 0 !important;
                border-radius: 8px !important;
                background: transparent !important;
                color: #5b6670 !important;
                font-size: 12px !important;
                font-weight: 600;
                white-space: nowrap;
            }
            .pl-page .product-list-tabs .nav-link.active {
                background: #ff9f43 !important;
                color: #fff !important;
                box-shadow: 0 1px 3px rgba(0, 0, 0, .12);
            }

            .pl-page .product-toolbar {
                display: block !important;
                gap: 8px !important;
                margin-bottom: 10px !important;
                padding: 8px 10px !important;
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
            }
            .pl-page .product-toolbar-filters {
                display: block !important;
            }
            .pl-page .product-toolbar-search {
                display: flex !important;
                align-items: center;
                gap: 8px;
                width: 100% !important;
                max-width: none !important;
            }
            .pl-page .product-toolbar-search > .form-label {
                display: none !important;
            }
            .pl-page .product-toolbar-search .search-input {
                flex: 1 1 auto;
                min-width: 0;
                width: auto !important;
                margin: 0 !important;
            }
            .pl-page #plScanBarcodeBtn.d-lg-none {
                display: inline-flex !important;
            }
            .pl-filter-toggle.d-md-none {
                position: relative;
                flex: 0 0 38px;
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
                width: 38px;
                height: 38px;
                padding: 0;
                border: 0;
                border-radius: 50%;
                background: #f05a6a;
                color: #fff;
                font-size: 15px;
                box-shadow: 0 2px 6px rgba(240, 90, 106, .35);
            }
            .pl-filter-toggle.has-active {
                background: #e8334a;
            }
            .pl-filter-count {
                position: absolute;
                top: -4px;
                right: -4px;
                min-width: 18px;
                height: 18px;
                padding: 0 5px;
                border-radius: 999px;
                background: #1b2850;
                color: #fff;
                font-size: 11px;
                line-height: 18px;
                text-align: center;
            }

            .pl-filter-backdrop.d-md-none {
                display: block !important;
                position: fixed;
                inset: 0;
                z-index: 1040;
                background: rgba(16, 24, 40, .45);
                opacity: 0;
                pointer-events: none;
                transition: opacity .2s ease;
            }
            .pl-filter-sheet.d-md-none {
                position: fixed;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 1045;
                display: flex !important;
                flex-direction: column;
                max-height: 85vh;
                background: #fff;
                border-radius: 16px 16px 0 0;
                box-shadow: 0 -8px 24px rgba(16, 24, 40, .18);
                transform: translateY(105%);
                transition: transform .25s ease;
            }
            body.pl-filter-open .pl-filter-backdrop {
                opacity: 1;
                pointer-events: auto;
            }
            body.pl-filter-open .pl-filter-sheet {
                transform: translateY(0);
            }
            body.pl-filter-open {
                overflow: hidden;
            }
            body.pl-filter-open .mobile-bottom-nav,
            body.pl-filter-open .floating-calculator-wrapper {
                display: none !important;
            }
            .pl-filter-sheet-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 16px 18px;
                border-bottom: 1px solid #eef0f4;
                font-size: 18px;
                font-weight: 600;
                color: #1b2850;
            }
            .pl-filter-sheet-close {
                width: 32px;
                height: 32px;
                padding: 0;
                border: 0;
                background: transparent;
                color: #2f6fd8;
                font-size: 20px;
            }
            .pl-filter-sheet-body {
                flex: 1 1 auto;
                overflow-y: auto;
                padding: 4px 18px;
            }
            .pl-filter-sheet-body .category-width {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                flex: none !important;
                margin: 0 !important;
                padding: 12px 0 !important;
                border-bottom: 1px solid #eef0f4;
            }
            .pl-filter-sheet-body .form-label {
                display: block;
                margin: 0 0 6px !important;
                font-size: 14px !important;
                color: #344054 !important;
            }
            .pl-filter-sheet-body .select2-container {
                width: 100% !important;
            }
            .pl-filter-sheet-body .select2-container .select2-selection--single {
                height: 40px !important;
                border-radius: 8px !important;
            }
            .pl-filter-sheet-body .select2-container .select2-selection--single .select2-selection__rendered {
                line-height: 40px !important;
            }
            .pl-filter-sheet-body .select2-container .select2-selection--single .select2-selection__arrow {
                height: 38px !important;
            }
            .pl-filter-sheet-foot {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 12px;
                padding: 14px 18px calc(14px + env(safe-area-inset-bottom));
                border-top: 1px solid #eef0f4;
            }
            .pl-filter-clear,
            .pl-filter-apply {
                height: 46px;
                border: 0;
                border-radius: 24px;
                font-size: 15px;
                font-weight: 600;
            }
            .pl-filter-clear {
                background: #eef0f4;
                color: #475467;
            }
            .pl-filter-apply {
                background: #e8334a;
                color: #fff;
            }
            body.pl-filter-open .select2-container--open .select2-dropdown {
                z-index: 1060;
            }

            :root[data-theme="dark"] .pl-m-head,
            :root[data-theme="dark"] .pl-page .product-list-tabs,
            :root[data-theme="dark"] .pl-page .product-toolbar,
            :root[data-theme="dark"] .pl-filter-sheet {
                background: #1e293b;
            }
            :root[data-theme="dark"] .pl-m-title,
            :root[data-theme="dark"] .pl-filter-sheet-head,
            :root[data-theme="dark"] .pl-filter-sheet-body .form-label {
                color: #f8fafc !important;
            }
        }

        @media screen and (min-width: 768px) and (max-width: 1024.98px), screen and (width: 1032px) and (height: 1376px), screen and (width: 1280px) and (height: 800px) {
            /* Kill fixed-header spacer on tablet (header is in-flow) */
            .page-wrapper:has(.pl-page),
            body:has(.pl-page) .page-wrapper {
                padding-top: 0 !important;
                margin-top: 0 !important;
            }

            body:has(.pl-page) .page-wrapper .content.pl-page,
            .pl-page.content {
                padding-top: 8px !important;
                padding-left: 12px !important;
                padding-right: 12px !important;
            }

            /* Compact toolbar: tools in one row */
            .pl-page .pl-m-head.d-md-none {
                padding: 10px 12px 8px !important;
                margin: 0 !important;
            }

            .pl-m-head-top {
                margin-bottom: 8px !important;
            }

            .pl-m-title {
                font-size: 18px !important;
                font-weight: 700 !important;
            }

            .pl-m-head-actions .btn {
                height: 36px !important;
                padding: 0 14px !important;
                font-size: 13px !important;
            }

            .pl-m-tools {
                display: grid !important;
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
                gap: 8px !important;
            }

            .pl-m-tool {
                height: 42px !important;
                font-size: 12.5px !important;
                border-radius: 10px !important;
                padding: 0 8px !important;
            }

            .pl-page .product-list-tabs {
                margin-bottom: 10px !important;
                padding: 0 12px 12px !important;
            }

            .pl-page .product-list-tabs .nav-link {
                height: 40px !important;
                font-size: 13px !important;
            }

            .pl-page .product-toolbar {
                padding: 10px 12px !important;
                margin-bottom: 12px !important;
            }

            /* Product cards: roomier 2-col */
            .pl-page table.datanew tbody {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 12px !important;
            }

            .pl-page table.datanew tbody > tr {
                padding: 14px !important;
                border-radius: 14px !important;
                border: 1px solid #e8eaef !important;
                min-height: 118px;
                box-shadow: 0 2px 8px rgba(16, 24, 40, .06) !important;
            }

            .pl-page .pl-m-card {
                grid-template-columns: 56px minmax(0, 1fr) !important;
                column-gap: 12px !important;
                row-gap: 10px !important;
            }

            .pl-page .pl-m-img {
                width: 56px !important;
                height: 56px !important;
                border-radius: 10px !important;
            }

            .pl-page .pl-m-name {
                font-size: 14px !important;
                line-height: 1.35 !important;
                -webkit-line-clamp: 2 !important;
                max-width: none !important;
            }

            .pl-page .pl-m-amount {
                font-size: 15px !important;
            }

            .pl-page .pl-m-foot {
                gap: 12px !important;
                padding-top: 10px !important;
            }

            .pl-page .pl-m-stat span {
                font-size: 11px !important;
            }

            .pl-page .pl-m-stat strong {
                font-size: 13px !important;
            }

            /* Printer + menu: clearer tap targets */
            .pl-page .pl-m-acts {
                gap: 6px !important;
            }

            .pl-page .pl-m-acts .quick-print-barcode-btn,
            .pl-page .pl-m-acts .quick-print-barcode-btn:hover,
            .pl-page .pl-m-acts .quick-print-barcode-btn:focus,
            .pl-page .pl-m-acts .action-menu-toggle {
                width: 38px !important;
                min-width: 38px !important;
                height: 38px !important;
                border-radius: 10px !important;
                background: #f3f4f6 !important;
                color: #1b2850 !important;
            }

            .pl-page .pl-m-acts .quick-print-barcode-btn i,
            .pl-page .pl-m-acts .action-menu-toggle {
                font-size: 16px !important;
            }
        }

        @media (min-width: 1025px) and (max-width: 1199.98px) {
            #productListPageHeader {
                box-sizing: border-box;
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                gap: 12px 16px;
                width: 100%;
            }

            #productListPageHeader > .page-title {
                flex: 1 1 160px;
                min-width: 0;
                text-align: left;
            }

            #productListPageHeader > .page-btn {
                display: flex;
                flex: 0 1 auto;
                flex-wrap: wrap;
                justify-content: flex-end;
                gap: 8px;
                min-width: 0;
                max-width: 100%;
                margin-left: auto;
            }
        }

        @media (min-width: 1025px) and (max-width: 1199.98px) {
            .pl-page .product-toolbar {
                display: grid !important;
                grid-template-columns: minmax(0, 1fr);
                align-items: start;
                gap: 12px;
                min-width: 0;
            }

            .pl-page .product-toolbar-filters {
                display: grid !important;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                align-items: end;
                gap: 12px;
                flex: none;
                min-width: 0;
                width: 100%;
            }

            .pl-page .product-toolbar-search,
            .pl-page .product-toolbar-filters > .category-width {
                box-sizing: border-box;
                flex: none;
                min-width: 0;
                width: auto;
                max-width: none;
            }

            .pl-page .product-toolbar-search .search-input,
            .pl-page .product-toolbar-search input {
                box-sizing: border-box;
                min-width: 0;
                width: 100%;
            }

            .pl-page .product-toolbar-filters select#filterCategory,
            .pl-page .product-toolbar-filters select#filterBrand,
            .pl-page .product-toolbar-filters .select2-container,
            .pl-page .product-toolbar-filters .select2-selection,
            .pl-page .product-toolbar-filters .select2-selection__rendered {
                box-sizing: border-box;
                width: 100% !important;
                min-width: 0;
                max-width: 100%;
            }

            .pl-page .product-toolbar-actions {
                display: flex;
                grid-column: 1;
                flex: none;
                flex-wrap: wrap;
                justify-content: flex-end;
                gap: 8px;
                min-width: 0;
                margin: 0;
                width: 100%;
            }

            .pl-page .product-toolbar-actions .btn {
                max-width: 100%;
            }

            .pl-page,
            .pl-page > .card,
            .pl-page > .card > .card-body {
                min-width: 0;
                max-width: 100%;
            }

            .pl-page .table-responsive {
                box-sizing: border-box;
                display: block;
                width: 100%;
                max-width: 100%;
                overflow-x: auto !important;
                overflow-y: hidden;
                overscroll-behavior-x: contain;
                -webkit-overflow-scrolling: touch;
            }

            .pl-page .table-responsive > .dataTables_wrapper,
            .pl-page .table-responsive > .dt-container {
                box-sizing: border-box;
                width: max-content;
                min-width: 100%;
                max-width: none;
            }

            .pl-page table.datanew {
                box-sizing: border-box;
                width: 1240px !important;
                min-width: 1240px;
                max-width: none !important;
                table-layout: fixed !important;
            }

            .pl-page table.datanew thead th,
            .pl-page table.datanew tbody td {
                box-sizing: border-box;
                min-width: 0;
            }

            .pl-page table.datanew thead th:nth-child(1),
            .pl-page table.datanew tbody td:nth-child(1) {
                width: 230px !important;
                min-width: 230px !important;
                white-space: normal !important;
                word-break: normal !important;
                overflow-wrap: break-word !important;
            }

            .pl-page table.datanew thead th:nth-child(2),
            .pl-page table.datanew tbody td:nth-child(2) {
                width: 56px !important;
                min-width: 56px !important;
            }

            .pl-page table.datanew thead th:nth-child(3),
            .pl-page table.datanew tbody td:nth-child(3) {
                width: 112px !important;
                min-width: 112px !important;
                white-space: nowrap !important;
                word-break: normal !important;
                overflow-wrap: normal !important;
            }

            .pl-page table.datanew thead th:nth-child(4),
            .pl-page table.datanew tbody td:nth-child(4) {
                width: 150px !important;
                min-width: 150px !important;
            }

            .pl-page table.datanew thead th:nth-child(5),
            .pl-page table.datanew tbody td:nth-child(5) {
                width: 76px !important;
                min-width: 76px !important;
                white-space: nowrap !important;
            }

            .pl-page table.datanew thead th:nth-child(6),
            .pl-page table.datanew tbody td:nth-child(6) {
                width: 128px !important;
                min-width: 128px !important;
            }

            .pl-page table.datanew thead th:nth-child(7),
            .pl-page table.datanew tbody td:nth-child(7) {
                width: 84px !important;
                min-width: 84px !important;
                white-space: nowrap !important;
            }

            .pl-page table.datanew thead th:nth-child(8),
            .pl-page table.datanew tbody td:nth-child(8),
            .pl-page table.datanew thead th:nth-child(9),
            .pl-page table.datanew tbody td:nth-child(9) {
                width: 100px !important;
                min-width: 100px !important;
                white-space: nowrap !important;
            }

            .pl-page table.datanew thead th.pl-col-print,
            .pl-page table.datanew tbody td.pl-col-print {
                width: 160px !important;
                min-width: 160px !important;
                white-space: nowrap !important;
                word-break: normal !important;
                overflow-wrap: normal !important;
            }

            .pl-page table.datanew tbody td.pl-col-print .btn {
                min-width: 104px;
                white-space: nowrap !important;
            }

            .pl-page table.datanew thead th.pl-col-action,
            .pl-page table.datanew tbody td.pl-col-action {
                width: 96px !important;
                min-width: 96px !important;
                max-width: 96px !important;
                white-space: nowrap !important;
                word-break: normal !important;
                overflow-wrap: normal !important;
            }

            .pl-page .table-scroll-top {
                display: block;
                scrollbar-color: #97a1af #eef0f4;
                scrollbar-width: thin;
                touch-action: pan-x;
            }

            .pl-page .table-scroll-top::-webkit-scrollbar {
                height: 8px;
            }

            .pl-page .table-scroll-top::-webkit-scrollbar-track {
                background: #eef0f4;
                border-radius: 4px;
            }

            .pl-page .table-scroll-top::-webkit-scrollbar-thumb {
                background: #97a1af;
                border-radius: 4px;
            }
        }
        table.datanew thead th.pl-col-extra,
        table.datanew tbody td.pl-col-extra {
            min-width: 100px;
            white-space: nowrap !important;
            word-break: normal !important;
            overflow-wrap: normal !important;
        }

        table.datanew tbody td.pl-col-extra.pl-col-wide {
            min-width: 180px;
            max-width: 260px;
            white-space: normal !important;
            overflow-wrap: anywhere !important;
        }

        .pl-page table.datanew.pl-has-extra {
            width: max-content !important;
            min-width: 100% !important;
            table-layout: auto !important;
        }

        .pl-page table.datanew.pl-has-extra > thead > tr > th,
        .pl-page table.datanew.pl-has-extra > tbody > tr > td {
            word-break: normal !important;
            overflow-wrap: normal !important;
        }

        .pl-page table.datanew.pl-has-extra > thead > tr > th:nth-child(1),
        .pl-page table.datanew.pl-has-extra > tbody > tr > td:nth-child(1) {
            min-width: 240px !important;
            white-space: normal !important;
        }

        .pl-page table.datanew.pl-has-extra > thead > tr > th:nth-child(n+3):nth-child(-n+9),
        .pl-page table.datanew.pl-has-extra > tbody > tr > td:nth-child(n+3):nth-child(-n+9) {
            min-width: 80px;
            white-space: nowrap !important;
        }

        .pl-page table.datanew.pl-has-extra > thead > tr > th.pl-col-print,
        .pl-page table.datanew.pl-has-extra > tbody > tr > td.pl-col-print {
            min-width: 110px !important;
            white-space: nowrap !important;
        }

        .pl-col-chooser .dropdown-menu {
            width: 440px;
            max-width: calc(100vw - 32px);
            max-height: 65vh;
            overflow-y: auto;
            padding: 10px 12px;
            border: 1px solid #ece9f1;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
        }

        .pl-col-chooser .pl-col-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding-bottom: 8px;
            margin-bottom: 6px;
            border-bottom: 1px solid #f0eef5;
            font-size: 12px;
            font-weight: 700;
            color: #5e5873;
        }

        .pl-col-chooser .pl-col-head .btn-link {
            font-size: 12px;
            padding: 0 4px;
            text-decoration: none;
        }

        .pl-col-chooser .pl-col-group {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #ff9f43;
            margin: 8px 0 4px;
        }

        .pl-col-chooser .pl-col-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 2px 12px;
        }

        .pl-col-chooser .pl-col-grid label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            padding: 4px 6px;
            border-radius: 6px;
            cursor: pointer;
            margin: 0;
            user-select: none;
        }

        .pl-col-chooser .pl-col-grid label:hover {
            background: #fff6ec;
        }

        .pl-col-chooser .pl-col-grid input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: #ff9f43;
            cursor: pointer;
        }

        #plColumnsBtn {
            background: #fff;
            color: #ff9f43;
            border: 1px solid #ff9f43;
            font-weight: 600;
        }

        #plColumnsBtn .badge {
            background: #ff9f43;
            font-size: 10px;
        }

        @media (max-width: 1024.98px), (width: 1032px) and (height: 1376px), (width: 1280px) and (height: 800px) {
            .pl-col-chooser {
                display: none !important;
            }
        }

        @media screen and (width: 1032px) and (height: 1376px) {
            .pl-page .table-scroll-top {
                display: none !important;
            }

            .pl-page .table-responsive {
                overflow: visible !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            .pl-page .table-responsive > .dataTables_wrapper,
            .pl-page .table-responsive > .dt-container {
                width: 100% !important;
                min-width: 0 !important;
                max-width: 100% !important;
            }

            .pl-page table.datanew {
                width: 100% !important;
                min-width: 0 !important;
                max-width: 100% !important;
            }
            .pl-page .product-toolbar {
                display: block !important;
            }

            .pl-page .product-toolbar-filters {
                display: block !important;
                width: 100% !important;
            }

            .pl-page .product-toolbar-search {
                display: flex !important;
                align-items: center;
                gap: 8px;
                width: 100% !important;
                max-width: none !important;
                min-width: 0 !important;
                flex: none !important;
            }

            .pl-page .product-toolbar-search .search-input {
                flex: 1 1 auto !important;
                width: auto !important;
                min-width: 0 !important;
                margin: 0 !important;
            }
        }
    </style>
    @if (session('error'))
        <div class="alert alert-danger" id="error-message">
            {{ session('error') }}
        </div>

        <style>
            .fade-out {
                opacity: 1;
                transition: opacity 0.5s ease-out;
            }

            .fade-out.hidden {
                opacity: 0;
            }
        </style>

        <script>
            setTimeout(function() {
                let alert = document.getElementById('error-message');
                if (alert) {
                    alert.classList.add('hidden'); // Triggers the fade-out transition
                    // Remove the element from DOM after fadeout (optional)
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 500); // match the CSS transition duration (0.5s)
                }
            }, 4000);
        </script>
    @endif

    <!-- Quantity History Modal -->
    <div class="modal fade" id="quantityHistoryModal" tabindex="-1" aria-labelledby="quantityHistoryLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quantityHistoryLabel">Quantity History</h5>
                    <button type="button" class="close quantity-history-close" data-dismiss="modal" data-bs-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="quantityHistoryContent">
                    <!-- History content will be injected here -->
                </div>
            </div>
        </div>
    </div>

    @if (app('hasPermission')(17, 'add') || app('hasPermission')(17, 'edit'))
    <!-- Same Update Stock modal as Stock Inventory -->
    <div class="modal fade ep-modal" id="editProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="ep-head">
                        <h5 class="modal-title">Update Stock</h5>
                        <small>Add or remove quantity for this product</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">x</button>
                </div>
                <form id="editProductForm">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editProductId">
                        <input type="hidden" name="product_variant_id" id="editProductVariantId">

                        <div class="ep-summary">
                            <input type="text" class="ep-name" name="name" id="editProductName" readonly tabindex="-1">
                            <div class="ep-stats">
                                <div class="ep-stat">
                                    <span>Price</span>
                                    <div class="ep-stat-val"><em>₹</em><input type="number" name="price" id="editProductPrice" readonly tabindex="-1"></div>
                                </div>
                                <div class="ep-stat">
                                    <span>Initial</span>
                                    <input type="number" name="initial_stock" id="editProductinitialStock" step="0.01" readonly tabindex="-1">
                                </div>
                                <div class="ep-stat is-current">
                                    <span>Current</span>
                                    <input type="number" name="current_stock" id="editProductStock" step="0.01" readonly tabindex="-1">
                                </div>
                            </div>
                        </div>

                        <label class="ep-label">What do you want to do?</label>
                        <div class="ep-actions">
                            <input type="radio" name="stock_action" id="addStock" value="add">
                            <label for="addStock" class="ep-action is-add"><i class="fa fa-plus"></i> Add Stock</label>
                            <input type="radio" name="stock_action" id="minusStock" value="minus">
                            <label for="minusStock" class="ep-action is-minus"><i class="fa fa-minus"></i> Remove Stock</label>
                        </div>

                        <div class="ep-qty d-none" id="stockQuantityBox">
                            <label class="ep-label" id="stockQuantityLabel" for="stockQuantity">Quantity</label>
                            <div class="ep-stepper">
                                <button type="button" class="ep-step" data-step="-1" aria-label="Decrease">−</button>
                                <input type="number" class="form-control" name="stock_quantity" id="stockQuantity"
                                    step="0.01" min="0.01" inputmode="decimal" placeholder="0">
                                <button type="button" class="ep-step" data-step="1" aria-label="Increase">+</button>
                            </div>
                            <div class="ep-preview" id="epNewStock"></div>
                        </div>

                        <div id="stockError" class="ep-error" style="display:none;"></div>

                        <label class="ep-label" for="stockRemarks">Remarks <small>(optional)</small></label>
                        <textarea class="form-control" name="remarks" id="stockRemarks" rows="2"
                            placeholder="e.g. New purchase, damaged items"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light ep-btn" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary ep-btn">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <div class="content pl-page">
        <div class="pl-m-head d-md-none">
            <div class="pl-m-head-top">
                <div class="pl-m-title" id="plMobileTitle">All Products</div>
                @if (app('hasPermission')(1, 'add'))
                    <div class="pl-m-head-actions page-btn-active-only">
                        <a href="{{ route('product.import') }}" class="btn btn-sm btn-added">
                            <i class="fas fa-file-import me-1"></i> Import
                        </a>
                        <a href="{{ route('product.add') }}" class="btn btn-sm btn-added pl-m-new">
                            <i class="fas fa-plus me-1"></i> New Product
                        </a>
                    </div>
                @endif
            </div>
            <div class="pl-m-tools">
                @if (app('hasPermission')(1, 'view'))
                    <button type="button" class="pl-m-tool is-info page-btn-active-only" data-pl-proxy="#productPriceHistoryBtn">
                        <i class="fas fa-history"></i><span>Price History</span>
                    </button>
                @endif
                @if (app('hasPermission')(1, 'add') || app('hasPermission')(1, 'edit'))
                    <a href="{{ route('product.quick-update') }}" class="pl-m-tool is-orange page-btn-active-only">
                        <i class="fas fa-bolt"></i><span>Quick Update</span>
                    </a>
                @endif
                @if (app('hasPermission')(1, 'add'))
                    <button type="button" class="pl-m-tool is-orange page-btn-active-only" data-bs-toggle="modal" data-bs-target="#quickProductModal">
                        <i class="fa fa-search"></i><span>Quick Product</span>
                    </button>
                @endif
                @if (app('hasPermission')(1, 'view'))
                    <button type="button" class="pl-m-tool is-green" data-pl-proxy="#exportAllChallan">
                        <i class="fas fa-file-excel"></i><span>Excel</span>
                    </button>
                    <button type="button" class="pl-m-tool is-red" data-pl-proxy="#exportPdf">
                        <i class="fas fa-file-pdf"></i><span>PDF</span>
                    </button>
                    <button type="button" class="pl-m-tool is-navy" data-pl-proxy="#downloadAllBarcodes">
                        <i class="fas fa-barcode"></i><span>All Barcodes</span>
                    </button>
                @endif
            </div>
        </div>

        <div class="pl-filter-backdrop d-md-none" id="plFilterBackdrop"></div>
        <div class="pl-filter-sheet d-md-none" id="plFilterSheet" role="dialog" aria-modal="true" aria-labelledby="plFilterSheetTitle">
            <div class="pl-filter-sheet-head">
                <span id="plFilterSheetTitle">Filter By</span>
                <button type="button" class="pl-filter-sheet-close" id="plFilterSheetClose" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="pl-filter-sheet-body" id="plFilterSheetBody"></div>
            <div class="pl-filter-sheet-foot">
                <button type="button" class="pl-filter-clear" id="plFilterClear">Clear</button>
                <button type="button" class="pl-filter-apply" id="plFilterApply">Apply</button>
            </div>
        </div>

        <div class="page-header" id="productListPageHeader">
            <div class="page-title">
                <h4 id="productListPageTitle">All Products</h4>
            </div>
            <div class="page-btn d-flex flex-wrap gap-2 page-btn-active-only">
                @if (app('hasPermission')(1, 'view'))
                    <button id="productPriceHistoryBtn" class="btn btn-sm btn-info" style="color:#fff; height: 35px; align-self: center;">
                        <i class="fas fa-history me-1"></i> Product Price History
                    </button>
                @endif
                @if (app('hasPermission')(1, 'add') || app('hasPermission')(1, 'edit'))
                    <a href="{{ route('product.quick-update') }}" class="btn btn-sm btn-added"
                        style="background: #ff9f43; color: #fff; border: 1px solid #ff9f43; height: 35px; align-self: center; display: inline-flex; align-items: center;">
                        <i class="fas fa-bolt me-1"></i> Quick Update
                    </a>
                @endif
                @if (app('hasPermission')(1, 'add'))
                    <button type="button" id="syncProductsToBranchesBtn" class="btn btn-sm btn-warning"
                        style="height: 35px; align-self: center; color:#fff; display: none;">
                        <i class="fas fa-share-alt me-1"></i>Copy ALL Products to All Branches
                    </button>
                    <button type="button" class="btn btn-sm btn-added" data-bs-toggle="modal" data-bs-target="#quickProductModal"
                        style="background: #ff9f43; color: #fff; border: 1px solid #ff9f43; height: 35px; align-self: center;">
                        <i class="fa fa-search me-1"></i> Quick Product
                    </button>
                    <a href="{{ route('product.import') }}" class="btn btn-sm btn-added"
                        style="background: #ff9f43; color: #ffffffff; border: 1px solid #ff9f43;">
                        <i class="fas fa-file-import me-1"></i>Import Products
                    </a>
                    <a href="{{ route('product.add') }}" class="btn btn-sm btn-added pl-new-product-btn"><img
                            src="{{ env('ImagePath') . '/admin/assets/img/icons/plus.svg' }}" alt="img"
                            class="me-1 d-none d-md-inline"><i class="fas fa-plus me-1 d-md-none"></i>New
                        Product</a>
                @endif
            </div>
        </div>

        <div class="product-list-tabs">
            <ul class="nav nav-tabs nav-tabs-solid" id="productListTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ request()->boolean('deleted') ? '' : 'active' }}" href="javascript:void(0);"
                        data-deleted="0" id="tabActiveProducts">
                        <i class="fas fa-box me-1"></i> Active Products
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ request()->boolean('deleted') ? 'active' : '' }}" href="javascript:void(0);"
                        data-deleted="1" id="tabDeletedProducts">
                        <i class="fas fa-trash-alt me-1"></i> Deleted Products
                    </a>
                </li>
            </ul>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="product-toolbar">
                    <div class="product-toolbar-filters">
                        <div class="product-toolbar-search">
                            <label for="search-input" class="form-label mb-1">Search</label>
                            <div class="search-input">
                                <a class="btn btn-searchset">
                                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}"
                                        alt="img">
                                </a>
                                <input type="text" id="search-input" class="form-control" placeholder="Search name, SKU or barcode...">
                                <button type="button" id="plScanBarcodeBtn" class="pl-scan-btn d-lg-none" title="Scan barcode with camera" aria-label="Scan barcode with camera">
                                    <i class="fas fa-camera"></i>
                                </button>
                            </div>
                            <button type="button" id="plFilterToggle" class="pl-filter-toggle d-md-none" aria-label="Filters" aria-expanded="false">
                                <i class="fas fa-filter"></i>
                                <span class="pl-filter-count d-none" id="plFilterCount">0</span>
                            </button>
                        </div>
                        <div class="form-group mb-0 category-width">
                            <label for="filterCategory" class="form-label mb-1">Category</label>
                            <select id="filterCategory" class="form-control form-control-sm select-filter">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-0 category-width">
                            <label for="filterBrand" class="form-label mb-1">Brand</label>
                            <select id="filterBrand" class="form-control form-control-sm select-filter">
                                <option value="">All Brands</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-0 category-width pl-date-field">
                            <label for="filterDate" class="form-label mb-1">Date</label>
                            <select id="filterDate" class="form-control form-control-sm select-filter" data-minimum-results-for-search="Infinity">
                                <option value="">All Time</option>
                                <option value="today">Today</option>
                                <option value="yesterday">Yesterday</option>
                                <option value="last5">Last 5 Days</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>

                        <div class="form-group mb-0 category-width pl-date-range is-hidden" id="filterDateRangeWrap">
                            <label class="form-label mb-1">From – To</label>
                            <div class="d-flex gap-1">
                                <input type="date" id="filterDateFrom" class="form-control form-control-sm" max="{{ now()->format('Y-m-d') }}">
                                <input type="date" id="filterDateTo" class="form-control form-control-sm" max="{{ now()->format('Y-m-d') }}">
                            </div>
                        </div>
                    </div>



                    <div class="product-toolbar-actions">
                        <div class="dropdown pl-col-chooser">
                            <button type="button" id="plColumnsBtn" class="btn btn-sm dropdown-toggle"
                                data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                <i class="fas fa-columns me-1"></i> Columns <span class="badge rounded-pill ms-1" id="plColumnsCount">0</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="plColumnsBtn">
                                <div class="pl-col-head">
                                    <span>Show fields in list</span>
                                    <span>
                                        <button type="button" class="btn btn-link" id="plColumnsAll">Select all</button>
                                        <button type="button" class="btn btn-link" id="plColumnsDefault">Default</button>
                                    </span>
                                </div>
                                <div id="plColumnsList"></div>
                            </div>
                        </div>
                        @if (app('hasPermission')(1, 'view'))
                            <!-- <button id="customBarcodeSettingsBtn" type="button" class="btn btn-sm" style="background:#fff; color:#333; border:1px solid #ccc; font-weight:600;" data-bs-toggle="modal" data-bs-target="#customBarcodeSettingsModal">
                                <i class="fas fa-cog me-1"></i> Custome Barcode
                            </button> -->
                            <button id="exportAllChallan" class="btn btn-sm btn-success">
                                <i class="fas fa-file-excel me-1"></i> Excel
                            </button>
                            <button id="exportPdf" class="btn btn-sm btn-danger">
                                <i class="fas fa-file-pdf me-1"></i> PDF
                            </button>
                            <button id="downloadAllBarcodes" class="btn btn-sm"
                                style="background:#1b2850; color:#fff; border:1px solid #1b2850;">
                                <i class="fas fa-barcode me-1"></i> All Barcodes
                            </button>
                        @endif
                    </div>
                </div>

                <div class="table-scroll-top">
                    <div></div>
                </div>
                <div class="table-responsive">
                    <table class="table  datanew">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th class="details-column">Details</th>
                                <th>SKU</th>
                                <th>Category </th>
                                <th>Unit</th>
                                <th>Brand</th>
                                    <th class="text-center">Qty</th>
                                <th class="text-center">MRP</th>
                                    <th class="text-center">Price</th>
                                <th class="text-center pl-col-print" id="plPrintBarcodeTh">Print Barcode</th>
                                <th class="action-column pl-col-action">Action</th>
                            </tr>
                        </thead>
                        <tbody>


                        </tbody>
                    </table>
                </div>

                <div
                    class="pagination-controls d-flex flex-column flex-md-row justify-content-between align-items-center mt-3">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        <span class="me-2" style="font-size: 14px; color: #555;">Show per page :</span>
                        <select id="per-page-select" class="form-select form-select-sm"
                            style="width: auto; border: 1px solid #ddd;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="ms-3" style="font-size: 14px; color: #555;">
                            <span id="pagination-from">0</span> - <span id="pagination-to">0</span> of <span
                                id="pagination-total">0</span> items
                        </span>
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0" id="pagination-numbers">
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="plScanModal" tabindex="-1" aria-labelledby="plScanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title" id="plScanModalLabel"><i class="fas fa-barcode me-2"></i>Scan Barcode</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-2">
                    <div id="plScanReader"></div>
                    <p class="text-muted small text-center mt-2 mb-0" id="plScanStatus">Point the camera at the product barcode</p>
                </div>
            </div>
        </div>
    </div>

    <!-- All Product Price History Modal -->
    <div class="modal fade" id="allProductPriceHistoryModal" tabindex="-1" aria-labelledby="allProductPriceHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="allProductPriceHistoryModalLabel"><i class="fa fa-history text-primary me-2"></i> Product Price History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div class="modal-body p-0" style="max-height: 60vh; overflow-y: auto;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Product Name</th>
                                    <th>Old MRP</th>
                                    <th>New MRP</th>
                                    <th>Old Price</th>
                                    <th>New Price</th>
                                    <th>Date Time</th>
                                </tr>
                            </thead>
                            <tbody id="allProductPriceHistoryModalBody">
                                <!-- Loaded dynamically via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 10px;">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Variants Modal -->
    <div class="modal fade" id="variantsModal" tabindex="-1" aria-labelledby="variantsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header variants-modal-header">
                    <h5 class="modal-title" id="variantsModalLabel"><i class="fa fa-list-alt text-primary me-2"></i> Product Variants</h5>
                    <button type="button" class="btn-close variants-btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Size</th>
                                    <th>Color</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody id="variantsModalBody">
                                <!-- Loaded dynamically via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 10px;">
                    <button type="button" class="btn btn-secondary btn-sm variants-close-btn" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Barcode History Modal -->
    <div class="modal fade" id="barcodeHistoryModal" tabindex="-1" aria-labelledby="barcodeHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="barcodeHistoryModalLabel"><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> Barcode History - <span id="bhProductName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div class="modal-body p-0" style="max-height: 60vh; overflow-y: auto;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Old Details</th>
                                    <th>New Details</th>
                                    <th>Changed Date</th>
                                </tr>
                            </thead>
                            <tbody id="barcodeHistoryModalBody">
                                <!-- Loaded dynamically via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 10px;">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Print Barcode / Edit Label Modal -->
    <div class="modal fade" id="printBarcodeModal" tabindex="-1" aria-labelledby="printBarcodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="printBarcodeModalLabel"><i class="fas fa-print text-primary me-2"></i> <span class="pb-title-text">Print / Edit Barcode Info</span><span class="pb-title-sep"> - </span><span id="pbProductName"></span></h5>
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
                            <div class="col-md-4 mb-3 d-none" id="pbLabelSizeCol">
                                <label class="form-label">Label Size (mm)</label>
                                <select class="form-select" id="pbLabelSize" name="label_size">
                                    <option value="35x25x3">35 x 25 mm, 3 columns, gap 0 (105 x 25 mm = 4.13 × 0.98 in)</option>
                                    <option value="35x25x1">35 x 25 mm, 1 column</option>
                                    <option value="38x25x2">38 x 25 x 2 (2 labels / row)</option>
                                    <option value="25x50x2">25 x 50 x 2 (2 tall labels / row)</option>
                                </select>
                                <small class="text-muted">TVS LP46 stock: <strong>4.13 × 0.98 in</strong> = 3 × (35×25 mm) labels.</small>
                            </div>
                            <div class="col-md-4 mb-3 d-none" id="pbPrinterCol">
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
                                <a href="{{ route('barcode.designer') }}" id="pbOpenCustomDesigner" class="btn btn-outline-primary btn-sm mt-2 w-100" target="_blank" style="border-color: #ff9f43;">
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
                            <div class="col-6 col-md-4 mb-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="form-label mb-0" for="pbShowBatch">Batch No</label>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch" id="pbShowBatch" checked>
                                    </div>
                                </div>
                                <input type="text" class="form-control mt-1" id="pbBatchNo" name="batch_no">
                                <div class="form-text">Same batch for every product. Changing it updates all products.</div>
                            </div>
                           
                            <div class="col-6 col-md-4 mb-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="form-label mb-0" for="pbShowWholesale">Wholesale</label>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch" id="pbShowWholesale" checked>
                            </div>
                                </div>
                                <input type="text" class="form-control mt-1" id="pbLabelRef" name="label_reference">
                            </div>
                            <div class="col-6 col-md-4 mb-3">
                                <label class="form-label">Other</label>
                                <input type="text" class="form-control" id="pbSpecificQty" name="specific_qty" >
                            </div>
                            <div class="col-6 col-md-4 mb-3">
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
                                <div class="d-flex gap-3">
                                    <div class="flex-fill">
                                        <label class="form-label" for="pbIncludeTitle">Company Title</label>
                                        <div class="form-check form-switch mt-1">
                                            <input class="form-check-input" type="checkbox" role="switch" id="pbIncludeTitle" checked>
                                            <label class="form-check-label" for="pbIncludeTitle" id="pbIncludeTitleLabel">Include</label>
                                        </div>
                                    </div>
                                    <div class="flex-fill">
                                        <label class="form-label" for="pbHindiName">Product Name</label>
                                        <div class="form-check form-switch mt-1">
                                            <input class="form-check-input" type="checkbox" role="switch" id="pbHindiName">
                                            <label class="form-check-label" for="pbHindiName">Print in Hindi</label>
                                        </div>
                                    </div>
                                </div>
                                <div id="pbHindiNameEdit" class="mt-2" style="display:none;">
                                    <input type="text" class="form-control" id="pbHindiNameInput" placeholder="Hindi product name" style="font-family: 'Nirmala UI', 'Noto Sans Devanagari', sans-serif;">
                                    <small class="text-muted d-block" id="pbHindiNameStatus"></small>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3 d-flex flex-column align-items-start justify-content-start pb-preview-col">
                                <div class="pb-preview-label d-md-none">Label Preview</div>
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
                                    <label class="form-label text-muted small mb-1">Width <i class="far fa-question-circle text-light-muted"></i></label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control" id="cbWidth" value="35" style="border-color: #eee; box-shadow: none;">
                                        <span class="input-group-text bg-white" style="border-color: #eee; color: #999;">mm</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted small mb-1">Height <i class="far fa-question-circle text-light-muted"></i></label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control" id="cbHeight" value="25" style="border-color: #eee; box-shadow: none;">
                                        <span class="input-group-text bg-white" style="border-color: #eee; color: #999;">mm</span>
                                    </div>
                                </div>
                            </div>

                            <h6 class="mb-3 fw-bold text-dark d-flex align-items-center" style="font-size: 14px;">
                                More Settings <i class="fas fa-chevron-down ms-1 text-muted" style="font-size: 10px;"></i>
                            </h6>
                            <div class="row mb-4">
                                <div class="col-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Columns <i class="far fa-question-circle text-light-muted"></i></label>
                                    <input type="number" class="form-control form-control-sm" id="cbColumns" value="3" style="border-color: #eee; box-shadow: none;">
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Gap <i class="far fa-question-circle text-light-muted"></i></label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control" id="cbGap" value="0" style="border-color: #eee; box-shadow: none;">
                                        <span class="input-group-text bg-white" style="border-color: #eee; color: #999;">mm</span>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Left Right <i class="far fa-question-circle text-light-muted"></i></label>
                                    <input type="number" class="form-control form-control-sm" id="cbLeftRight" value="0.00" step="0.01" style="border-color: #eee; box-shadow: none;">
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Up Down <i class="far fa-question-circle text-light-muted"></i></label>
                                    <input type="number" class="form-control form-control-sm" id="cbUpDown" value="0.00" step="0.01" style="border-color: #eee; box-shadow: none;">
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Shape</label>
                                    <div class="d-flex gap-2 align-items-center" style="height: 31px;" id="cbShapeSelector">
                                        <div class="border shape-option" data-shape="square" style="width:28px;height:18px;border-radius:0 !important;cursor:pointer;border-color:#ddd !important;transition:all 0.2s;"></div>
                                        <div class="border border-primary shape-option active" data-shape="rounded" style="width:30px;height:20px;border-radius:3px;cursor:pointer;border-width:2px !important;transition:all 0.2s; border-color:#0d6efd !important;"></div>
                                        <div class="border shape-option" data-shape="circle" style="width:28px;height:18px;border-radius:50% !important;cursor:pointer;border-color:#ddd !important;transition:all 0.2s;"></div>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Background <i class="far fa-question-circle text-light-muted"></i></label>
                                    <div class="d-flex align-items-center gap-2" style="height: 31px;">
                                        <div id="cbBgColorPreview" class="border" style="width:18px;height:18px;background:#ffffff;border-color:#ddd !important;border-radius:2px;"></div>
                                        <select class="form-select form-select-sm border-0 shadow-none p-0 text-muted" id="cbBgColorSelect" style="width: 75px; background-color: transparent; cursor: pointer; font-size: 13px;">
                                            <option value="#ffffff">#ffffff</option>
                                        </select>
                                        <div class="position-relative ms-auto" style="width:24px;height:24px;">
                                            <button type="button" class="btn btn-sm btn-light border d-flex align-items-center justify-content-center text-muted position-absolute" style="left:0; top:0; width:24px;height:24px;padding:0; z-index:1;">+</button>
                                            <input type="color" id="cbBgColorInput" value="#ffffff" style="opacity:0; position:absolute; left:0; top:0; width:24px; height:24px; cursor:pointer; z-index:2;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="button" class="btn btn-primary w-100 fw-bold" id="customBarcodeConfirmBtn" style="background-color: #ff9f43; border-color: #ff9f43; padding: 10px 0; border-radius: 8px;">Confirm</button>
                        </div>
                        
                        <!-- Preview Side -->
                        <div class="col-md-6 p-2 p-md-4">
                            <div class="d-flex align-items-center justify-content-center w-100 h-100" style="background-color: #f8f9fc; min-height: 350px; border-radius: 12px;">
                                <div class="position-relative d-flex">
                                    <!-- Left Dimension (Height) -->
                                    <div class="position-absolute d-flex align-items-center justify-content-center" style="left: -12px; top: 5px; height: 50px; width: 6px;">
                                        <div style="border-top: 1px solid #5bc0de; border-bottom: 1px solid #5bc0de; border-left: 1px solid #5bc0de; width: 100%; height: 100%;"></div>
                                        <span id="previewHeightLabel" class="position-absolute" style="left: -20px; font-size: 11px; color: #5bc0de; padding: 2px; font-weight: 500; transform: rotate(-90deg);">25mm</span>
                                    </div>
                                    
                                    <div class="position-relative">
                                        <!-- Top Dimension (Width) -->
                                        <div class="position-absolute d-flex align-items-center justify-content-center" style="top: -12px; left: 5px; width: 70px; height: 6px;">
                                            <div style="border-left: 1px solid #5bc0de; border-right: 1px solid #5bc0de; border-top: 1px solid #5bc0de; width: 100%; height: 100%;"></div>
                                            <span id="previewWidthLabel" class="position-absolute" style="top: -18px; font-size: 11px; color: #5bc0de; background: #f8f9fc; padding: 0 4px; font-weight: 500;">35mm</span>
                                        </div>

                                        <!-- Boxes -->
                                        <div id="previewBoxesContainer" class="d-flex border rounded p-1 bg-white" style="border-color: #5bc0de !important; gap: 0;">
                                            <div class="preview-box border rounded" style="width: 70px; height: 50px; border-color: #eee !important; background: white; margin: 1px;"></div>
                                            <div class="preview-box border rounded" style="width: 70px; height: 50px; border-color: #eee !important; background: white; margin: 1px;"></div>
                                            <div class="preview-box border rounded" style="width: 70px; height: 50px; border-color: #eee !important; background: white; margin: 1px;"></div>
                                        </div>

                                        <!-- Bottom Gap Dimension -->
                                        <div class="position-absolute d-flex flex-column align-items-center" style="bottom: -15px; left: 76px;">
                                            <div style="width: 1px; height: 6px; background-color: #5bc0de; margin-bottom: 2px;"></div>
                                            <span id="previewGapLabel" style="font-size: 11px; color: #5bc0de; font-weight: 500; line-height: 1;">0mm</span>
                                        </div>
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

@push('js')
    @include('inventory.partials.mobile-ui-js')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Open+Sans:wght@400;700&family=Montserrat:wght@400;700&family=Lato:wght@400;700&family=Oswald:wght@400;700&family=Poppins:wght@400;700&family=Noto+Sans:wght@400;700&family=Noto+Sans+Devanagari:wght@600;700&family=Roboto+Condensed:wght@400;700&family=PT+Sans:wght@400;700&family=Source+Sans+3:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.5/JsBarcode.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
    @include('partials.label-bluetooth-print')
    <script>
        $(function() {
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
    </script>
    <script>
        $(document).ready(function() {

            var authToken = localStorage.getItem("authToken");
            window.globalBatchNo = window.globalBatchNo || '';
            // Use server-side setting so headers and row data are always in sync
            var enableVariantsFromSetting = {{ \App\Models\Setting::first()?->enable_variant_product ? 1 : 0 }};

            var PL_COLUMNS_STORAGE = 'productListColumns.v1';
            var plMoneyFmt = { symbol: '₹', position: 'left' };

            function plMoney(v) {
                if (v === undefined || v === null || v === '' || isNaN(parseFloat(v))) return '-';
                var n = parseFloat(v).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                return plMoneyFmt.position === 'left' ? plMoneyFmt.symbol + n : n + plMoneyFmt.symbol;
            }

            function plText(v) {
                v = (v === undefined || v === null) ? '' : String(v).trim();
                return v === '' ? '-' : escapeHtmlText(v);
            }

            function plDate(v) {
                if (!v) return '-';
                var d = new Date(String(v).replace(' ', 'T'));
                if (isNaN(d.getTime())) return plText(v);
                return escapeHtmlText(d.toLocaleString('en-IN', {
                    day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true
                }));
            }

            function plRate2(product) {
                var rates = product.selling_prices;
                if (typeof rates === 'string') {
                    try { rates = JSON.parse(rates); } catch (e) { rates = []; }
                }
                if (!Array.isArray(rates)) return null;
                var fallback = null;
                for (var i = 0; i < rates.length; i++) {
                    var name = String((rates[i] && rates[i].name) || '').trim();
                    if (/^rate\s*2$/i.test(name)) return rates[i];
                    if (!fallback && name !== '' && !/wholesale/i.test(name) && !/^rate\s*1$/i.test(name)) fallback = rates[i];
                }
                return fallback;
            }

            var PL_EXTRA_COLUMNS = [
                { key: 'name_hindi', label: 'Hindi Name', group: 'Basic', wide: true, render: function(p) { return plText(p.name_hindi); } },
                { key: 'barcode', label: 'Barcode', group: 'Basic', render: function(p) { return plText(p.barcode); } },
                { key: 'product_code', label: 'Product Code', group: 'Basic', render: function(p) { return plText(p.product_code); } },
                { key: 'wholesale', label: 'Wholesale', group: 'Price & Stock', render: function(p) { return plMoney(p.wholesale_price); } },
                { key: 'purchase_price', label: 'Purchase', group: 'Price & Stock', render: function(p) { return plMoney(p.purchase_price); } },
                { key: 'rate2_price', label: 'Rate 2 Price', group: 'Price & Stock', render: function(p) { var r = plRate2(p); return plMoney(r ? r.price : null); } },
                { key: 'rate2_mrp', label: 'Rate 2 MRP', group: 'Price & Stock', render: function(p) { var r = plRate2(p); return plMoney(r ? r.mrp : null); } },
                { key: 'cost_price', label: 'Cost Price', group: 'Price & Stock', render: function(p) { return plMoney(p.cost_price); } },
                { key: 'landing_cost', label: 'Landing Cost', group: 'Price & Stock', render: function(p) { return plMoney(p.landing_cost); } },
                { key: 'minimum_stock_qty', label: 'Min Stock Qty', group: 'Price & Stock', render: function(p) { return plText(p.minimum_stock_qty); } },
                { key: 'gst_option', label: 'GST Option', group: 'Details', render: function(p) {
                    return p.gst_option ? escapeHtmlText(String(p.gst_option).replace(/_/g, ' ').replace(/\b\w/g, function(c) { return c.toUpperCase(); })) : '-';
                } },
                { key: 'hsn_code', label: 'HSN Code', group: 'Details', render: function(p) { return plText(p.hsn_code); } },
                { key: 'batch_no', label: 'Batch No', group: 'Details', render: function(p) { return plText(p.batch_no); } },
                { key: 'specific_qty', label: 'Specific Qty', group: 'Details', render: function(p) { return plText(p.specific_qty); } },
                { key: 'label_code', label: 'Label Code', group: 'Details', render: function(p) { return plText(p.label_code); } },
                { key: 'label_reference', label: 'Label Reference', group: 'Details', render: function(p) { return plText(p.label_reference); } },
                { key: 'item_location', label: 'Item Location', group: 'Details', render: function(p) { return plText(p.item_location); } },
                { key: 'description', label: 'Description', group: 'Details', wide: true, render: function(p) { return plText(p.description); } },
                { key: 'created_at', label: 'Created Date', group: 'Details', render: function(p) { return plDate(p.created_at); } },
                { key: 'updated_at', label: 'Updated Date', group: 'Details', render: function(p) { return plDate(p.updated_at); } }
            ];

            // Fixed columns: 1 = Name, 2 = mobile details toggle, last = Action (always shown).
            var PL_BASE_COLUMNS = [
                { key: 'sku', label: 'SKU', group: 'Basic', index: 3 },
                { key: 'category', label: 'Category', group: 'Basic', index: 4 },
                { key: 'unit', label: 'Unit', group: 'Basic', index: 5 },
                { key: 'brand', label: 'Brand', group: 'Basic', index: 6 },
                { key: 'qty', label: 'Qty', group: 'Price & Stock', index: 7 },
                { key: 'mrp', label: 'MRP', group: 'Price & Stock', index: 8 },
                { key: 'price', label: 'Price', group: 'Price & Stock', index: 9 },
                { key: 'print', label: 'Print Barcode', group: 'Details', index: 10 + PL_EXTRA_COLUMNS.length }
            ];
            PL_EXTRA_COLUMNS.forEach(function(c, i) { c.index = 10 + i; });

            var PL_ALL_COLUMNS = PL_BASE_COLUMNS.concat(PL_EXTRA_COLUMNS);
            var PL_GROUP_ORDER = ['Basic', 'Price & Stock', 'Details'];

            function plDefaultKeys() {
                return PL_BASE_COLUMNS.map(function(c) { return c.key; });
            }

            function plLoadKeys() {
                try {
                    var saved = JSON.parse(localStorage.getItem(PL_COLUMNS_STORAGE) || 'null');
                    if (Array.isArray(saved)) {
                        return saved.filter(function(k) { return PL_ALL_COLUMNS.some(function(c) { return c.key === k; }); });
                    }
                } catch (e) {}
                return plDefaultKeys();
            }

            var plVisibleKeys = plLoadKeys();
            // Phone / tablet card layout: keep the table at screen width.
            var plCompactQuery = window.matchMedia('(max-width: 1024.98px), (width: 1032px) and (height: 1376px), (width: 1280px) and (height: 800px)');
            var plResizeTimer = null;
            window.addEventListener('resize', function() {
                clearTimeout(plResizeTimer);
                plResizeTimer = setTimeout(function() { plApplyColumns(); }, 150);
            });

            if ($.fn.DataTable && $.fn.DataTable.isDataTable('.datanew')) {
                $('.datanew').DataTable().destroy();
            }
            if (!$('.datanew thead th.pl-col-extra').length) {
                PL_EXTRA_COLUMNS.forEach(function(c) {
                    $('<th class="pl-col-extra text-center"></th>').text(c.label).insertBefore('#plPrintBarcodeTh');
                });
            }

            function plApplyColumns() {
                var rules = [];
                PL_ALL_COLUMNS.forEach(function(c) {
                    if (plVisibleKeys.indexOf(c.key) === -1) {
                        rules.push('.pl-page table.datanew > thead > tr > th:nth-child(' + c.index + '), '
                            + '.pl-page table.datanew > tbody > tr > td:nth-child(' + c.index + ')');
                    }
                });
                var css = rules.length ? rules.join(',\n') + ' { display: none !important; }' : '';
                var $style = $('#plColumnsHideStyle');
                if (!$style.length) {
                    $style = $('<style id="plColumnsHideStyle"></style>').appendTo('head');
                }
                $style.text(css);
                var anyExtra = PL_EXTRA_COLUMNS.some(function(c) { return plVisibleKeys.indexOf(c.key) !== -1; });
                $('.datanew').toggleClass('pl-has-extra', anyExtra && !plCompactQuery.matches);
                $('#plColumnsCount').text(plVisibleKeys.length);
            }

            function plRenderChooser() {
                var html = '';
                PL_GROUP_ORDER.forEach(function(g) {
                    var cols = PL_ALL_COLUMNS.filter(function(c) { return c.group === g; })
                        .sort(function(a, b) { return a.index - b.index; });
                    if (!cols.length) return;
                    html += '<div class="pl-col-group">' + escapeHtmlText(g) + '</div><div class="pl-col-grid">';
                    cols.forEach(function(c) {
                        html += '<label><input type="checkbox" class="pl-col-toggle" value="' + c.key + '"'
                            + (plVisibleKeys.indexOf(c.key) !== -1 ? ' checked' : '') + '> ' + escapeHtmlText(c.label) + '</label>';
                    });
                    html += '</div>';
                });
                $('#plColumnsList').html(html);
            }

            function plSetColumns(keys) {
                plVisibleKeys = keys.slice();
                try { localStorage.setItem(PL_COLUMNS_STORAGE, JSON.stringify(plVisibleKeys)); } catch (e) {}
                plRenderChooser();
                plApplyColumns();
                var topInner = document.querySelector('.table-scroll-top > div');
                var tableEl = document.querySelector('.datanew');
                if (topInner && tableEl) topInner.style.width = tableEl.scrollWidth + 'px';
            }

            $(document).on('change', '#plColumnsList .pl-col-toggle', function() {
                plSetColumns($('#plColumnsList .pl-col-toggle:checked').map(function() { return this.value; }).get());
            });
            $('#plColumnsAll').on('click', function() {
                plSetColumns(PL_ALL_COLUMNS.map(function(c) { return c.key; }));
            });
            $('#plColumnsDefault').on('click', function() {
                plSetColumns(plDefaultKeys());
            });

            plRenderChooser();
            plApplyColumns();

            var table = $('.datanew').DataTable({
                "destroy": true,
                "bFilter": false,
                "paging": false,
                "info": false,
                "searching": false,
                "ordering": false,
                "dom": 't'
            });
            plApplyColumns();

            function plTagCellClasses() {
                plApplyColumns();
                var printIdx = 10 + PL_EXTRA_COLUMNS.length;
                $('.datanew > tbody > tr').each(function() {
                    var $cells = $(this).children('td');
                    if ($cells.length < printIdx + 1) return;
                    PL_EXTRA_COLUMNS.forEach(function(c) {
                        $cells.eq(c.index - 1).addClass('pl-col-extra text-center' + (c.wide ? ' pl-col-wide' : ''));
                    });
                    $cells.eq(printIdx - 1).addClass('pl-col-print');
                    $cells.eq(printIdx).addClass('pl-col-action');
                });
            }

            let currentPage = 1;
            let lastPage = 1;
            let perPage = 10;
            let searchQuery = '';
            let showDeletedProducts = {{ request()->boolean('deleted') ? 'true' : 'false' }};

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
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');
            }

            function syncProductListModeUI() {
                const deleted = !!showDeletedProducts;
                $('#tabActiveProducts').toggleClass('active', !deleted);
                $('#tabDeletedProducts').toggleClass('active', deleted);
                $('#productListPageTitle, #plMobileTitle').text(deleted ? 'Deleted Products' : 'All Products');
                document.body.classList.toggle('showing-deleted-products', deleted);
                document.title = deleted ? 'Deleted Products' : 'Product List';
                try {
                    const url = new URL(window.location.href);
                    if (deleted) {
                        url.searchParams.set('deleted', '1');
                    } else {
                        url.searchParams.delete('deleted');
                    }
                    window.history.replaceState({}, '', url.pathname + url.search);
                } catch (e) {}
            }

            syncProductListModeUI();

            $('#productListTabs').on('click', '[data-deleted]', function (e) {
                e.preventDefault();
                const nextDeleted = String($(this).data('deleted')) === '1';
                if (nextDeleted === showDeletedProducts) {
                    return;
                }
                showDeletedProducts = nextDeleted;
                syncProductListModeUI();
                searchQuery = String($('#search-input').val() || '').trim();
                fetchProducts(1);
            });

            $(document).on('click', '.pl-m-tool[data-pl-proxy]', function () {
                $($(this).data('pl-proxy')).trigger('click');
            });

            (function initPhoneFilterSheet() {
                const sheetBody = document.getElementById('plFilterSheetBody');
                const toolbarFilters = document.querySelector('.product-toolbar-filters');
                const toggle = document.getElementById('plFilterToggle');
                if (!sheetBody || !toolbarFilters || !toggle) return;
                const filterSheetQuery = window.matchMedia('(max-width: 1024.98px), (width: 1032px) and (height: 1376px), (width: 1280px) and (height: 800px)');

                function placeFilterFields() {
                    const fields = filterSheetQuery.matches
                        ? toolbarFilters.querySelectorAll(':scope > .category-width')
                        : sheetBody.querySelectorAll('.category-width');
                    fields.forEach(function (field) {
                        (filterSheetQuery.matches ? sheetBody : toolbarFilters).appendChild(field);
                    });
                    if (!filterSheetQuery.matches) closeSheet();
                }
                function openSheet() {
                    placeFilterFields();
                    document.body.classList.add('pl-filter-open');
                    toggle.setAttribute('aria-expanded', 'true');
                }
                function closeSheet() {
                    document.body.classList.remove('pl-filter-open');
                    toggle.setAttribute('aria-expanded', 'false');
                }
                function updateCount() {
                    const n = ['#filterCategory', '#filterBrand', '#filterDate'].filter(function (sel) {
                        return !!$(sel).val();
                    }).length;
                    $('#plFilterCount').text(n).toggleClass('d-none', n === 0);
                    $(toggle).toggleClass('has-active', n > 0);
                }

                toggle.addEventListener('click', openSheet);
                ['plFilterSheetClose', 'plFilterApply', 'plFilterBackdrop'].forEach(function (id) {
                    const el = document.getElementById(id);
                    if (el) el.addEventListener('click', closeSheet);
                });
                $('#plFilterClear').on('click', function () {
                    $('#filterCategory').val('').trigger('change.select2');
                    $('#filterDateFrom, #filterDateTo').val('');
                    $('#filterDate').val('').trigger('change.select2');
                    $('#filterDateRangeWrap').addClass('is-hidden');
                    $('#filterBrand').val('').trigger('change');
                    updateCount();
                });
                $('#filterCategory, #filterBrand, #filterDate').on('change', updateCount);
                window.addEventListener('resize', placeFilterFields);
                placeFilterFields();
                updateCount();
            })();
            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');

            $('#syncProductsToBranchesBtn').on('click', function () {
                Swal.fire({
                    title: 'Copy ALL products to ALL branches?',
                    html: 'Main Branch has ~<b>29,000+</b> products.<br>'
                        + 'Sub Branch / 3rd Branch currently have only a partial list.<br><br>'
                        + 'This will copy <b>every missing product</b> to every other branch with <b>Qty 0</b>.<br>'
                        + 'Keep this page open until it says <b>Complete</b>.',
                    icon: 'question',
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonColor: '#ff9f43',
                    denyButtonColor: '#6c757d',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Continue (recommended)',
                    denyButtonText: 'Restart from beginning'
                }).then(function (result) {
                    if (!result.isConfirmed && !result.isDenied) {
                        return;
                    }

                    var reset = result.isDenied ? 1 : 0;

                    Swal.fire({
                        title: 'Starting sync...',
                        html: 'Preparing to copy all products to all branches.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: function () { Swal.showLoading(); }
                    });

                    $.ajax({
                        url: '/api/sync-products-to-branches/start',
                        method: 'POST',
                        data: { reset: reset, from_main: 1 },
                        headers: {
                            'Authorization': 'Bearer ' + authToken,
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            if (!res || !res.status) {
                                Swal.fire('Error', (res && res.message) || 'Could not start sync', 'error');
                                return;
                            }
                            pollCatalogSyncStatus();
                        },
                        error: function (xhr) {
                            var msg = (xhr.responseJSON && xhr.responseJSON.message)
                                ? xhr.responseJSON.message
                                : 'Could not start sync';
                            Swal.fire('Error', msg, 'error');
                        }
                    });
                });
            });

            function pollCatalogSyncStatus() {
                $.ajax({
                    url: '/api/sync-products-to-branches/status',
                    method: 'GET',
                    headers: { 'Authorization': 'Bearer ' + authToken },
                    success: function (res) {
                        var d = (res && res.data) ? res.data : {};
                        var sourceCount = parseInt(d.source_count || 0, 10);
                        var processed = parseInt(d.processed || 0, 10);
                        var remaining = parseInt(d.remaining || 0, 10);
                        var created = parseInt(d.created || 0, 10);
                        var skipped = parseInt(d.skipped || 0, 10);
                        var failed = parseInt(d.failed || 0, 10);
                        var pct = sourceCount > 0 ? Math.min(100, Math.round((processed / sourceCount) * 100)) : 0;

                        var branchHtml = '';
                        if (d.branch_stats && d.branch_stats.length) {
                            branchHtml = '<br><br><b>Products per branch now:</b>';
                            d.branch_stats.forEach(function (b) {
                                branchHtml += '<br>' + (b.name || ('Branch ' + b.id)) + ': <b>' + b.count + '</b>';
                            });
                        }

                        if (d.done) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Complete — all products copied',
                                html: 'Main Branch products processed: <b>' + processed + '</b>'
                                    + '<br>Created in other branches: <b>' + created + '</b>'
                                    + '<br>Already existed: <b>' + skipped + '</b>'
                                    + '<br>Failed: <b>' + failed + '</b>'
                                    + branchHtml
                                    + '<br><br>Open <b>Sub Branch</b> / <b>3rd branch</b> and refresh.'
                            }).then(function () {
                                if (typeof loadProducts === 'function') {
                                    loadProducts(currentPage);
                                }
                            });
                            return;
                        }

                        if (d.error && !d.running) {
                            Swal.fire('Sync error', d.error || d.message || 'Sync failed', 'error');
                            return;
                        }

                        Swal.fire({
                            title: 'Copying products to all branches...',
                            html: (sourceCount ? ('Main total: <b>' + sourceCount + '</b><br>') : '')
                                + 'Progress: <b>' + pct + '%</b>'
                                + '<br>Processed: <b>' + processed + '</b>'
                                + (remaining ? (' | Remaining: <b>' + remaining + '</b>') : '')
                                + '<br>Created: <b>' + created + '</b> | Skipped: <b>' + skipped + '</b> | Failed: <b>' + failed + '</b>'
                                + branchHtml
                                + '<br><br><small>Keep this page open. Do not close until Complete.</small>',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: function () { Swal.showLoading(); }
                        });

                        setTimeout(pollCatalogSyncStatus, 2500);
                    },
                    error: function () {
                        setTimeout(pollCatalogSyncStatus, 4000);
                    }
                });
            }

            // Initialize Select2 for custom class
            $('.select-filter').select2();

            function buildProductActionDropdown(product) {
                let hasBarcode = (product.barcode && product.barcode.trim() !== '');
                if (!hasBarcode && product.variants && product.variants.length > 0) {
                    hasBarcode = product.variants.some(v => v.barcode && v.barcode.trim() !== '');
                }
                const barcodeVal = hasBarcode ? '1' : '';
                const productName = product.name ? product.name : 'Product';

                if (showDeletedProducts) {
                    return `
                    <div class="dropdown product-action-dropdown">
                        <button class="btn action-menu-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end product-action-menu">
                            @if (app('hasPermission')(1, 'view'))
                                <a class="dropdown-item" href="/product-view/${product.id}">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            @endif
                            @if (app('hasPermission')(1, 'edit') || app('hasPermission')(1, 'delete'))
                                <a class="dropdown-item text-success restore-product" data-id="${product.id}" href="javascript:void(0);">
                                    <i class="fas fa-undo"></i> Restore Product
                                </a>
                            @endif
                        </div>
                    </div>`;
                }

                return `
                    <div class="dropdown product-action-dropdown">
                        <button class="btn action-menu-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end product-action-menu">
                            @if (app('hasPermission')(17, 'view'))
                                <a class="dropdown-item" href="/inventory-View/${product.id}">
                                    <i class="fas fa-history"></i> History
                                </a>
                            @endif
                            @if (app('hasPermission')(17, 'add') || app('hasPermission')(17, 'edit'))
                                <a class="dropdown-item edit-product-stock" href="javascript:void(0);" data-id="${product.id}">
                                    <i class="fas fa-boxes"></i> Add / Edit Stock
                                </a>
                            @endif
                            @if (app('hasPermission')(1, 'view'))
                                <a class="dropdown-item" href="/product-view/${product.id}">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            @endif
                            @if (app('hasPermission')(1, 'edit'))
                                <a class="dropdown-item" href="/edit-product/${product.id}">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            @endif
                            <a class="dropdown-item" href="/pricing-updates?product_id=${product.id}">
                                <i class="fas fa-tags"></i> Price Updates
                            </a>
                            <a class="dropdown-item download-barcode-btn"
                               href="javascript:void(0);"
                               data-id="${product.id}"
                               data-barcode="${barcodeVal}"
                               data-name="${escapeHtmlAttr(productName)}"
                               data-sku="${escapeHtmlAttr(product.SKU || '')}">
                                <i class="fas fa-download"></i> Download Barcode
                            </a>
                            <a class="dropdown-item print-barcode-btn"
                               href="javascript:void(0);"
                               data-id="${product.id}"
                               data-barcode="${product.barcode || ''}"
                               data-price="${product.price || ''}"
                               data-mrp="${product.mrp || ''}"
                               data-label-code="${escapeHtmlAttr(product.label_code || '')}"
                               data-batch-no="${escapeHtmlAttr(product.batch_no || '')}"
                               data-label-ref="${escapeHtmlAttr(product.label_reference || '')}"
                               data-wholesale-price="${product.wholesale_price != null && product.wholesale_price !== '' ? product.wholesale_price : ''}"
                               data-qty="${product.quantity || ''}"
                               data-specific-qty="${escapeHtmlAttr(product.specific_qty || '')}"
                               data-label-size="${product.label_size || '35x25x3'}"
                               data-name="${escapeHtmlAttr(productName)}"
                               data-name-hindi="${escapeHtmlAttr(product.name_hindi || '')}"
                               data-selling-prices='${JSON.stringify(product.selling_price_options || product.selling_prices || []).replace(/'/g, "&#39;")}'
                               data-barcode-details='${JSON.stringify(product.barcode_details || {})}'>
                                <i class="fas fa-print"></i> Print Barcode
                            </a>
                            <a class="dropdown-item barcode-history-btn"
                               href="javascript:void(0);"
                               data-id="${product.id}"
                               data-name="${escapeHtmlAttr(productName)}">
                                <i class="fas fa-barcode"></i> Barcode History
                            </a>
                            @if (app('hasPermission')(1, 'delete'))
                                <a class="dropdown-item text-danger confirm-text delete-product" data-id="${product.id}" href="javascript:void(0);">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            @endif
                        </div>
                    </div>
                `;
            }

            function positionProductActionMenu(dropdown) {
                const toggle = dropdown.querySelector('.action-menu-toggle');
                const menu = dropdown.querySelector('.product-action-menu');
                if (!toggle || !menu) {
                    return;
                }

                const rect = toggle.getBoundingClientRect();
                const menuWidth = menu.offsetWidth || 168;
                const menuHeight = menu.offsetHeight || 180;
                const viewportWidth = window.innerWidth || document.documentElement.clientWidth;
                const viewportHeight = window.innerHeight || document.documentElement.clientHeight;

                let left = rect.right - menuWidth;
                let top = rect.bottom + 6;

                if (left < 8) {
                    left = rect.left;
                }

                if (left + menuWidth > viewportWidth - 8) {
                    left = viewportWidth - menuWidth - 8;
                }

                if (top + menuHeight > viewportHeight - 8) {
                    top = Math.max(8, rect.top - menuHeight - 6);
                }

                menu.classList.add('product-action-menu-floating');
                menu.style.setProperty('left', `${Math.max(8, left)}px`, 'important');
                menu.style.setProperty('top', `${Math.max(8, top)}px`, 'important');
                menu.style.setProperty('right', 'auto', 'important');
                menu.style.setProperty('bottom', 'auto', 'important');
                menu.style.setProperty('transform', 'none', 'important');
            }

            $(document).on('shown.bs.dropdown', '.product-action-dropdown', function() {
                positionProductActionMenu(this);
                requestAnimationFrame(() => positionProductActionMenu(this));
            });

            $(document).on('hidden.bs.dropdown', '.product-action-dropdown', function() {
                const menu = this.querySelector('.product-action-menu');
                if (!menu) {
                    return;
                }

                menu.classList.remove('product-action-menu-floating');
                menu.removeAttribute('style');
            });

            $(window).on('scroll resize', function() {
                $('.product-action-dropdown.show').each(function() {
                    positionProductActionMenu(this);
                });
            });

            let productListRequest = null;
            let productListFetchId = 0;

            function isShowingDeletedProducts() {
                if ($('#tabDeletedProducts').hasClass('active')) {
                    return true;
                }
                if ($('#tabActiveProducts').hasClass('active')) {
                    return false;
                }
                try {
                    return new URLSearchParams(window.location.search).get('deleted') === '1';
                } catch (e) {
                    return !!showDeletedProducts;
                }
            }

            // Define the function FIRST
            function fetchProducts(page = 1) {
                var categoryId = $('#filterCategory').val();
                var brandId = $('#filterBrand').val();
                showDeletedProducts = isShowingDeletedProducts();
                var deletedFlag = showDeletedProducts ? 1 : 0;
                var fetchId = ++productListFetchId;

                if (productListRequest && productListRequest.readyState !== 4) {
                    productListRequest.abort();
                }

                productListRequest = $.ajax({
                    url: "/api/getAllProduct",
                    type: "GET",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    data: {
                        page: page,
                        per_page: perPage,
                        search: String(searchQuery || '').trim(),
                        category_id: categoryId,
                        brand_id: brandId,
                        sub_branch_id: selectedSubAdminId,
                        deleted: deletedFlag,
                        status: deletedFlag ? 'deleted' : 'active',
                        quick_update: 1,
                        date_filter: $('#filterDate').val() || '',
                        date_field: 'any',
                        date_from: $('#filterDate').val() === 'custom' ? ($('#filterDateFrom').val() || '') : '',
                        date_to: $('#filterDate').val() === 'custom' ? ($('#filterDateTo').val() || '') : ''
                    },
                    success: function(response) {
                        // Ignore stale responses (tab/search race)
                        if (fetchId !== productListFetchId) {
                            return;
                        }
                        currentPage = page;
                        window.productListCurrentPage = currentPage;
                        if (response.status) {
                            let products = response.data;
                            let currencySymbol = response.currencySymbol || '₹';
                            let currencyPosition = response.currencyPosition || 'left';
                            plMoneyFmt = { symbol: currencySymbol, position: currencyPosition };
                            let tableBody = [];

                            let lowStockThreshold = response.lowStockThreshold || 0;
                            if (response.globalBatchNo !== undefined && response.globalBatchNo !== null) {
                                window.globalBatchNo = String(response.globalBatchNo);
                            }
                            let enableVariants = enableVariantsFromSetting;
                            let pagination = response.pagination || null;

                            if (pagination) {
                                currentPage = pagination.current_page;
                                lastPage = pagination.last_page;
                                updatePaginationUI(pagination);
                            }

                            // Function to capitalize first letter of each word
                            function capitalizeWords(str) {
                                if (!str || str.trim() === '') return 'N/A';
                                return str.replace(/\b\w/g, function(char) {
                                    return char.toUpperCase();
                                });
                            }
                            $.each(products, function(index, product) {
                                let productName = capitalizeWords(product.name || 'N/A');
                                let sku = `<div style="word-break: break-all; max-width: 200px; white-space: normal;">${product.barcode || product.SKU || product.product_code || 'N/A'}</div>`;
                                let quantity = product.quantity ?? 'N/A';
                                let categoryName = capitalizeWords(product.category ? product
                                    .category.name : "N/A");
                                let unitName = capitalizeWords(product.unit ? product.unit
                                    .unit_name : "N/A");
                                let brandName = capitalizeWords(product.brand ? product.brand
                                    .name : "N/A");

                                let createdAt = capitalizeWords(product.product_type ? product
                                    .product_type
                                    .name : "N/A");

                                let quickPrintBarcodeBtn = showDeletedProducts ? '' : `
                                    <div class="text-center">
                                        <a href="javascript:void(0);"
                                           class="btn btn-sm btn-outline-primary print-barcode-btn quick-print-barcode-btn"
                                           style="border-color:#ff9f43;color:#ff9f43;font-size:12px;padding:4px 10px;"
                                           data-id="${product.id}"
                                           data-barcode="${product.barcode || ''}"
                                           data-price="${product.price || ''}"
                                           data-mrp="${product.mrp || ''}"
                                           data-label-code="${escapeHtmlAttr(product.label_code || '')}"
                                           data-batch-no="${escapeHtmlAttr(product.batch_no || '')}"
                                           data-label-ref="${escapeHtmlAttr(product.label_reference || '')}"
                                           data-wholesale-price="${product.wholesale_price != null && product.wholesale_price !== '' ? product.wholesale_price : ''}"
                                           data-qty="${product.quantity || ''}"
                                           data-specific-qty="${escapeHtmlAttr(product.specific_qty || '')}"
                                           data-label-size="${product.label_size || '35x25x3'}"
                                           data-name="${escapeHtmlAttr(productName)}"
                                           data-name-hindi="${escapeHtmlAttr(product.name_hindi || '')}"
                                           data-selling-prices='${JSON.stringify(product.selling_price_options || product.selling_prices || []).replace(/'/g, "&#39;")}'
                                           data-barcode-details='${JSON.stringify(product.barcode_details || {})}'
                                           title="Print Barcode">
                                            <i class="fas fa-print me-1"></i> Print
                                        </a>
                                    </div>`;

                                let detailsToggle = `
    <a href="#details-${product.id}" class="toggle-details" data-bs-toggle="collapse">
        <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
    </a>
`;

                                let productImage =
                                    '{{ env('ImagePath') . 'admin/assets/img/product/noimage.png' }}';
                                const ImagePath = "{{ env('ImagePath') . 'storage/' }}";
                                if (product.images) {
                                    let imagesArray = typeof product.images === "string" ? JSON
                                        .parse(product.images) : product.images;
                                    if (Array.isArray(imagesArray) && imagesArray.length > 0) {
                                        productImage = `${ImagePath}${imagesArray[0]}`;
                                    }
                                }

                                let formattedPrice = 'N/A';
                                if (product.price !== undefined && product.price !== null &&
                                    product.price !== '') {
                                    let priceVal = parseFloat(product.price).toLocaleString(
                                        undefined, {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        });
                                    if (currencyPosition === 'left') {
                                        formattedPrice = currencySymbol + priceVal;
                                    } else {
                                        formattedPrice = priceVal + currencySymbol;
                                    }
                                }

                                const isLowStock = quantity !== 'N/A' && parseInt(quantity) <=
                                    lowStockThreshold;
                                let quantityDisplay =
                                    `<span class="quantity-history" data-id="${product.id}" style="cursor:pointer; border: 1px solid #ececec; padding: 4px 8px; border-radius: 4px; display: inline-block; color: ${isLowStock ? '#ea5455' : 'inherit'}; font-weight: ${isLowStock ? '600' : '400'};">${quantity}</span>`;
                                if (isLowStock) {
                                    quantityDisplay +=
                                        `<br><span class="badge bg-danger mt-1" style="font-size: 0.75em;">Low Stock</span>`;
                                }

                                let hasBarcodeMobile = (product.barcode && product.barcode.trim() !== '');
                                if (!hasBarcodeMobile && product.variants && product.variants.length > 0) {
                                    hasBarcodeMobile = product.variants.some(v => v.barcode && v.barcode.trim() !== '');
                                }
                                const barcodeValMobile = hasBarcodeMobile ? '1' : '';

                                let mobileActionButtons = showDeletedProducts ? `
                <div class="action-buttons">
                    @if (app('hasPermission')(1, 'view'))
                        <a class="icon-btn" href="/product-view/${product.id}" title="View">
                            <i class="fas fa-eye" style="color:#092C4C;"></i>
                        </a>
                    @endif
                    @if (app('hasPermission')(1, 'edit') || app('hasPermission')(1, 'delete'))
                        <a class="btn btn-sm btn-success restore-product" data-id="${product.id}" href="javascript:void(0);" title="Restore"
                           style="color:#fff;font-size:13px;">
                            <i class="fas fa-undo me-1"></i> Restore
                        </a>
                    @endif
                </div>` : `
                <div class="action-buttons">
                    @if (app('hasPermission')(17, 'view'))
                        <a class="btn btn-sm btn-primary" style="color:white; font-size: 13px;" href="/inventory-View/${product.id}">
                            History
                        </a>
                    @endif
                    @if (app('hasPermission')(17, 'add') || app('hasPermission')(17, 'edit'))
                        <a class="btn btn-sm btn-warning edit-product-stock" href="javascript:void(0);" data-id="${product.id}" title="Add / Edit Stock" style="color:#fff;font-size:13px;">
                            <i class="fas fa-boxes me-1"></i> Stock
                        </a>
                    @endif
                    @if (app('hasPermission')(1, 'view'))
                        <a class="icon-btn" href="/product-view/${product.id}" title="View">
                            <i class="fas fa-eye" style="color:#092C4C;"></i>
                        </a>
                    @endif
                    @if (app('hasPermission')(1, 'edit'))
                        <a class="icon-btn" href="/edit-product/${product.id}" title="Edit">
                            <i class="fas fa-edit" style="color:#092C4C;"></i>
                        </a>
                    @endif
                    @if (app('hasPermission')(1, 'delete'))
                        <a class="icon-btn confirm-text delete-product" data-id="${product.id}" href="javascript:void(0);" title="Delete">
                            <i class="fas fa-trash" style="color:#092C4C;"></i>
                        </a>
                    @endif
                    <a class="icon-btn download-barcode-btn"
                       href="javascript:void(0);"
                       data-id="${product.id}"
                       data-barcode="${barcodeValMobile}"
                       data-name="${escapeHtmlAttr(product.name || '')}"
                       data-sku="${escapeHtmlAttr(product.SKU || '')}"
                       title="Download Barcode">
                        <i class="fas fa-download" style="color:#092C4C;"></i>
                    </a>
                    <a class="icon-btn print-barcode-btn"
                       href="javascript:void(0);"
                       data-id="${product.id}"
                       data-barcode="${product.barcode || ''}"
                       data-price="${product.price || ''}"
                       data-mrp="${product.mrp || ''}"
                       data-label-code="${escapeHtmlAttr(product.label_code || '')}"
                       data-batch-no="${escapeHtmlAttr(product.batch_no || '')}"
                       data-label-ref="${escapeHtmlAttr(product.label_reference || '')}"
                       data-qty="${product.quantity || ''}"
                       data-specific-qty="${escapeHtmlAttr(product.specific_qty || '')}"
                       data-label-size="${product.label_size || '35x25x3'}"
                       data-name="${escapeHtmlAttr(product.name || '')}"
                       data-name-hindi="${escapeHtmlAttr(product.name_hindi || '')}"
                       data-selling-prices='${JSON.stringify(product.selling_price_options || product.selling_prices || []).replace(/'/g, "&#39;")}'
                       data-barcode-details='${JSON.stringify(product.barcode_details || {})}'
                       title="Print Barcode">
                        <i class="fas fa-print" style="color:#092C4C;"></i>
                    </a>
                    <a class="icon-btn barcode-history-btn"
                       href="javascript:void(0);"
                       data-id="${product.id}"
                       data-name="${escapeHtmlAttr(product.name || '')}"
                       title="Barcode History">
                        <i class="fas fa-barcode" style="color:#092C4C;"></i>
                    </a>
                    <a class="icon-btn" href="/pricing-updates?product_id=${product.id}" title="Price Updates">
                        <i class="fas fa-tags" style="color:#092C4C;"></i>
                    </a>
                </div>`;

                                let mobileMrp = '-';
                                if (product.mrp !== undefined && product.mrp !== null && product.mrp !== '' && parseFloat(product.mrp) > 0) {
                                    const mrpNum = parseFloat(product.mrp).toLocaleString(undefined, {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                    mobileMrp = currencyPosition === 'left' ? currencySymbol + mrpNum : mrpNum + currencySymbol;
                                }
                                const mobileCode = escapeHtmlText(product.barcode || product.SKU || product.product_code || '');
                                const mobileCard = `
                                    <div class="pl-m-card">
                                        <a href="/product-view/${product.id}" class="pl-m-img">
                                            <img src="${productImage}" alt="" loading="lazy">
                                        </a>
                                        <div class="pl-m-body">
                                            <div class="pl-m-top">
                                                <a href="/product-view/${product.id}" class="pl-m-name">${escapeHtmlText(productName)}</a>
                                                <span class="pl-m-amount">${formattedPrice}</span>
                </div>
                                            <div class="pl-m-badges">
                                                <span class="pl-m-badge ${showDeletedProducts ? 'is-deleted' : (isLowStock ? 'is-low' : 'is-ok')}">${showDeletedProducts ? 'DELETED' : (isLowStock ? 'LOW STOCK' : 'IN STOCK')}</span>
                                                ${mobileCode ? `<span class="pl-m-code">${mobileCode}</span>` : ''}
                                                ${(product.variants && product.variants.length > 0) ? `<a href="javascript:void(0);" class="view-variants-btn pl-m-var" data-id="${product.id}">${product.variants.length} variants</a>` : ''}
                                            </div>
                                        </div>
                                        <div class="pl-m-foot">
                                            <div class="pl-m-stat quantity-history" data-id="${product.id}"><span>Qty</span><strong class="${isLowStock ? 'text-danger' : ''}">${quantity}</strong></div>
                                            <div class="pl-m-stat"><span>MRP</span><strong>${mobileMrp}</strong></div>
                                            <div class="pl-m-stat pl-m-cat"><span>Category</span><strong>${escapeHtmlText(categoryName)}</strong></div>
                                            <div class="pl-m-acts">
                                                ${quickPrintBarcodeBtn}
                                                ${buildProductActionDropdown(product)}
                                            </div>
                                        </div>
                                    </div>`;

                                let rowData = [
                                    `${mobileCard}<div style="display: flex;" class="pl-row-head">

                                    <a href="/product-view/${product.id}" class="product-img mb-1">
                                        <img src="${productImage}" alt="product" style="max-width: 60px;">
                                    </a>
                                    <div class="pl-row-main">
                                        <a href="/product-view/${product.id}" class="product-name-link">
                                            ${productName}
                                        </a>
                                        <div class="pl-m-meta">
                                            <span class="pl-m-sku">${escapeHtmlText(product.barcode || product.SKU || product.product_code || '')}</span>
                                            <span class="pl-m-stats">
                                                <span class="pl-m-price">${formattedPrice}</span>
                                                <span class="pl-m-qty quantity-history${isLowStock ? ' is-low' : ''}" data-id="${product.id}">Qty ${quantity}</span>
                                            </span>
                </div>
                                    </div>
                                    ${(product.variants && product.variants.length > 0) ? `
                                    <a href="javascript:void(0);" class="view-variants-btn ms-2 mt-1" data-id="${product.id}" title="View Variants">
                                        <i class="fa fa-exclamation-circle text-info fa-lg"></i>
                                    </a>` : ''}

                                </div>

                                  <!-- Collapsible Details (visible only on mobile) -->
    <div class="collapse mt-2 d-xl-none pl-details" id="details-${product.id}">
        <div class="">
            <div class="pl-details-info">
                <p class="mb-1"><strong>SKU:</strong> ${sku}</p>
                <p class="mb-1"><strong>Category:</strong> ${categoryName}</p>
                <p class="mb-1"><strong>Unit:</strong> ${unitName}</p>
                <p class="mb-1"><strong>Brand:</strong> ${brandName}</p>
            </div>
            <div class="mt-2 mb-2 pl-details-print">${quickPrintBarcodeBtn}</div>
            <div class="mt-2 pl-details-actions">
                ${mobileActionButtons}
            </div>
        </div>
    </div>`,
                                    detailsToggle,
                                    `<div style="white-space: normal; word-wrap: break-word; word-break: break-all; min-width: 100px;">${sku}</div>`,
                                    categoryName,
                                    unitName,
                                    brandName
                                ];

                                let formattedMrp = '-';
                                if (product.mrp !== undefined && product.mrp !== null && product.mrp !== '' && parseFloat(product.mrp) > 0) {
                                    let mrpVal = parseFloat(product.mrp).toLocaleString(undefined, {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                    formattedMrp = currencyPosition === 'left' ? currencySymbol + mrpVal : mrpVal + currencySymbol;
                                }

                                    rowData.push(`<div class="text-center">${quantityDisplay}</div>`);
                                rowData.push(`<div class="text-center">${formattedMrp}</div>`);
                                    rowData.push(`<div class="text-center">${formattedPrice}</div>`);

                                PL_EXTRA_COLUMNS.forEach(function(col) {
                                    rowData.push(col.render(product));
                                });

                                rowData.push(quickPrintBarcodeBtn);

                                rowData.push(
                                    // createdAt,
                                    buildProductActionDropdown(product)
                                );

                                tableBody.push(rowData);
                            });
                            table.clear().rows.add(tableBody).draw();
                            plTagCellClasses();

                            // Sync top scrollbar
                            const topScroll = document.querySelector('.table-scroll-top');
                            const tableResponsive = document.querySelector('.table-responsive');
                            const tableElement = document.querySelector('.datanew');

                            if (topScroll && tableResponsive && tableElement) {
                                const topInnerDiv = topScroll.querySelector('div');
                                topInnerDiv.style.width = tableElement.scrollWidth + 'px';

                                topScroll.onscroll = () => {
                                    tableResponsive.scrollLeft = topScroll.scrollLeft;
                                };
                                tableResponsive.onscroll = () => {
                                    topScroll.scrollLeft = tableResponsive.scrollLeft;
                                };
                            }

                        } else {
                            table.clear().draw();
                            $(".datanew tbody").html('<tr><td colspan="' + (11 + PL_EXTRA_COLUMNS.length) + '">No products found</td></tr>');
                            $('#pagination-from').text(0);
                            $('#pagination-to').text(0);
                            $('#pagination-total').text(0);
                            $('#pagination-numbers').html('');
                            $('.pagination-controls').hide();
                        }
                    },
                    error: function(xhr, status) {
                        if (status === 'abort') {
                            return;
                        }
                        alert("Error fetching products.");
                    }
                });
            }

            function updatePaginationUI(pagination) {
                let from = (pagination.current_page - 1) * pagination.per_page;
                let to = pagination.current_page * pagination.per_page;
                if (to > pagination.total) to = pagination.total;
                if (pagination.total === 0) from = 0;

                $('#pagination-from').text(from);
                $('#pagination-to').text(to);
                $('#pagination-total').text(pagination.total);

                let paginationHtml = '';

                // Previous button
                paginationHtml += `
        <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0);" data-page="${pagination.current_page - 1}">Previous</a>
        </li>
    `;

                // Show only 3 page numbers at a time
                const visiblePageCount = 2;
                let startPage = Math.floor((pagination.current_page - 1) / visiblePageCount) * visiblePageCount + 1;
                let endPage = Math.min(pagination.last_page, startPage + visiblePageCount - 1);

                // Show previous ellipsis if there are pages before startPage
                if (startPage > 1) {
                    paginationHtml += `
            <li class="page-item">
                <a class="page-link" href="javascript:void(0);" data-page="${startPage - 1}" data-action="prev-group">..</a>
            </li>
        `;
                }

                // Generate page numbers
                for (let i = startPage; i <= endPage; i++) {
                    paginationHtml += `
            <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                <a class="page-link" href="javascript:void(0);" data-page="${i}">${i}</a>
            </li>
        `;
                }

                // Show next ellipsis if there are more pages after endPage
                if (endPage < pagination.last_page) {
                    paginationHtml += `
            <li class="page-item">
                <a class="page-link" href="javascript:void(0);" data-page="${endPage + 1}" data-action="next-group">..</a>
            </li>
        `;
                }

                // Next button
                paginationHtml += `
        <li class="page-item ${pagination.current_page === pagination.last_page || pagination.last_page === 0 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0);" data-page="${pagination.current_page + 1}">Next</a>
        </li>
    `;

                $('#pagination-numbers').html(paginationHtml);
                $('.pagination-controls').show();
            }
            // Update Stock modal (same APIs as Stock Inventory)
            $('#editProductModal').on('show.bs.modal', function() {
                $('input[name="stock_action"]').prop('checked', false);
                $('#stockQuantityBox').addClass('d-none');
                $('#stockQuantity').val('');
                $('#stockRemarks').val('');
                $('#stockError').hide().text('');
            });

            $('#addStock, #minusStock').on('change', function() {
                const stockBox = $('#stockQuantityBox');
                const stockLabel = $('#stockQuantityLabel');
                if ($(this).val() === 'add') {
                    stockBox.removeClass('d-none');
                    stockLabel.text('How much to Add?');
                } else if ($(this).val() === 'minus') {
                    stockBox.removeClass('d-none');
                    stockLabel.text('How much to Minus?');
                }
            });

            $(document).on('click', '.edit-product-stock', function() {
                const productId = $(this).data('id');
                const variantId = $(this).data('variant-id');
                let reqUrl = '/api/products_edit_inventroy/' + productId;
                if (variantId) {
                    reqUrl += '?variant_id=' + variantId;
                }
                $.ajax({
                    url: reqUrl,
                    type: 'GET',
                    headers: { Authorization: 'Bearer ' + authToken },
                    success: function(response) {
                        if (response.status) {
                            const product = response.product;
                            $('#editProductId').val(product.id);
                            $('#editProductVariantId').val(product.product_variant_id || variantId || '');
                            $('#editProductName').val(product.product_name);
                            $('#editProductPrice').val(product.price);
                            $('#editProductinitialStock').val(product.initial_stock);
                            $('#editProductStock').val(product.current_stock);
                            $('#stockRemarks').val('');
                            $('#editProductModal').modal('show');
                        } else {
                            alert('Product not found!');
                        }
                    },
                    error: function() {
                        alert('Could not load stock details.');
                    }
                });
            });

            $('#editProductForm').on('submit', function(e) {
                e.preventDefault();
                const $submitBtn = $(this).find('button[type="submit"]');
                if ($submitBtn.prop('disabled')) return;
                if (!$submitBtn.data('default-html')) {
                    $submitBtn.data('default-html', $submitBtn.html());
                }
                function toggleStockSubmitLoading(isLoading) {
                    if (isLoading) {
                        $submitBtn.prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving...'
                        );
                    } else {
                        $submitBtn.prop('disabled', false).html($submitBtn.data('default-html'));
                    }
                }

                const productId = $('#editProductId').val();
                const variantId = $('#editProductVariantId').val();
                const stockAction = $("input[name='stock_action']:checked").val();
                const stockQuantity = $('#stockQuantity').val();

                $('#stockError').hide().text('');
                if (!stockAction) {
                    $('#stockError').text('Please select Add Quantity or Minus Quantity').show();
                    return;
                }
                if (!stockQuantity || stockQuantity <= 0) {
                    $('#stockError').text('Please enter quantity').show();
                    return;
                }

                const formData = {
                    name: $('#editProductName').val(),
                    price: $('#editProductPrice').val(),
                    current_stock: $('#editProductStock').val(),
                    initial_stock: $('#editProductinitialStock').val(),
                    sub_branch_id: localStorage.getItem('selectedSubAdminId'),
                    stock_action: stockAction,
                    stock_quantity: stockQuantity,
                    remarks: $('#stockRemarks').val().trim()
                };
                if (variantId) {
                    formData.product_variant_id = variantId;
                }

                $.ajax({
                    url: '/api/inventory_update/' + productId,
                    type: 'POST',
                    beforeSend: function() { toggleStockSubmitLoading(true); },
                    headers: { Authorization: 'Bearer ' + authToken },
                    data: formData,
                    success: function(response) {
                        if (response.status) {
                            $('#editProductModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Quantity successfully updated',
                                confirmButtonColor: '#ff9f43'
                            }).then(function() {
                                fetchProducts(currentPage);
                            });
                        } else {
                            $('#stockError').text(response.message || 'Update failed').show();
                        }
                    },
                    error: function(xhr) {
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Update failed';
                        $('#stockError').text(msg).show();
                    },
                    complete: function() { toggleStockSubmitLoading(false); }
                });
            });

            $(document).on('click', '.quantity-history', function() {
                const productId = $(this).data('id');

                $.ajax({
                    url: `/api/product-quantity-history/${productId}`,
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(res) {
                        if (res.status) {
                            let content = `<h5>${res.product}</h5><ul class="list-group">`;
                            res.history.forEach(item => {
                                content += `
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>${item.type}</strong> - ${item.note}<br>
                                                <small>${item.date}</small>
                                            </div>
                                        <span class="badge bg-${item.type === 'Added' ? 'success' : 'danger'} px-4 py-2 small">${item.quantity}</span>
                                        </li>
                                    `;
                            });
                            content += `</ul>`;
                            $('#quantityHistoryContent').html(content);
                            $('#quantityHistoryModal').modal('show');
                        } else {
                            alert(res.message || "Failed to load quantity history.");
                        }
                    },
                    error: function() {
                        alert("Error fetching quantity history.");
                    }
                });
            });

            // Initial fetch
            fetchProducts(currentPage);
            window.fetchProducts = fetchProducts;

            // Handle search input (name, SKU, or barcode — including scanner Enter)
            let searchTimer = null;
            function runProductSearch() {
                searchQuery = String($('#search-input').val() || '').trim();
                fetchProducts(1);
            }
            $('#search-input').on('input', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(runProductSearch, 300);
            });
            $('#search-input').on('keydown', function(e) {
                if (e.key === 'Enter' || e.which === 13) {
                    e.preventDefault();
                    clearTimeout(searchTimer);
                    runProductSearch();
                }
            });

            (function initProductListScanner() {
                var modalEl = document.getElementById('plScanModal');
                var btn = document.getElementById('plScanBarcodeBtn');
                if (!modalEl || !btn) return;
                var modal = {
                    show: function() { bootstrap.Modal.getOrCreateInstance(modalEl).show(); },
                    hide: function() { bootstrap.Modal.getOrCreateInstance(modalEl).hide(); }
                };
                var scanner = null;
                var handled = false;
                var $status = $('#plScanStatus');

                function loadLib() {
                    if (window.Html5Qrcode) return Promise.resolve();
                    return new Promise(function(resolve, reject) {
                        var s = document.createElement('script');
                        s.src = 'https://unpkg.com/html5-qrcode';
                        s.onload = resolve;
                        s.onerror = reject;
                        document.head.appendChild(s);
                    });
                }

                function stopScanner() {
                    if (!scanner) return Promise.resolve();
                    var s = scanner;
                    scanner = null;
                    return (s.isScanning ? s.stop() : Promise.resolve())
                        .catch(function() {})
                        .then(function() { try { s.clear(); } catch (e) {} });
                }

                function onCode(text) {
                    if (handled) return;
                    var code = String(text || '').trim();
                    if (!code) return;
                    handled = true;
                    if (navigator.vibrate) navigator.vibrate(80);
                    $('#search-input').val(code);
                    clearTimeout(searchTimer);
                    runProductSearch();
                    modal.hide();
                }

                btn.addEventListener('click', function() {
                    handled = false;
                    $status.removeClass('text-danger').text('Starting camera...');
                    modal.show();
                });

                modalEl.addEventListener('shown.bs.modal', function() {
                    loadLib().then(function() {
                        var config = {
                            fps: 12,
                            qrbox: function(w, h) {
                                return {
                                    width: Math.max(200, Math.min(320, Math.floor(w * 0.9))),
                                    height: Math.max(90, Math.min(160, Math.floor(h * 0.45)))
                                };
                            }
                        };
                        if (window.Html5QrcodeSupportedFormats) {
                            var F = Html5QrcodeSupportedFormats;
                            config.formatsToSupport = [F.CODE_128, F.CODE_39, F.CODE_93, F.EAN_13, F.EAN_8,
                                F.UPC_A, F.UPC_E, F.ITF, F.CODABAR, F.QR_CODE].filter(function(f) { return f !== undefined; });
                        }
                        scanner = new Html5Qrcode('plScanReader');
                        return scanner.start({ facingMode: 'environment' }, config, onCode, function() {})
                            .then(function() {
                                if (!modalEl.classList.contains('show')) return stopScanner();
                                $status.removeClass('text-danger').text('Point the camera at the product barcode');
                            });
                    }).catch(function(err) {
                        $status.addClass('text-danger').text('Could not open camera. Allow camera permission and use HTTPS. ' + (err && err.message ? err.message : ''));
                    });
                });

                modalEl.addEventListener('hidden.bs.modal', function() {
                    stopScanner();
                });
            })();

            // Handle page number clicks
            // Handle page number clicks with ellipsis support
            $(document).on('click', '#pagination-numbers .page-link', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                let action = $(this).data('action');

                // Handle ellipsis clicks to load next/previous groups
                if (action === 'next-group') {
                    // Load the page that starts the next group
                    if (page && page <= lastPage) {
                        fetchProducts(page);
                    }
                    return;
                }

                if (action === 'prev-group') {
                    // Load the previous group's starting page
                    let prevStartPage = Math.max(1, page - 2);
                    if (prevStartPage >= 1 && prevStartPage <= lastPage) {
                        fetchProducts(prevStartPage);
                    }
                    return;
                }

                // Regular page navigation
                if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                    fetchProducts(page);
                }
            });

            // Handle per-page change
            $('#per-page-select').on('change', function() {
                perPage = $(this).val();
                fetchProducts(1);
            });

            // Filter change
            $('#filterCategory, #filterBrand').on('change', function() {
                fetchProducts(1);
            });

            $('#filterDate').on('change', function() {
                var isCustom = $(this).val() === 'custom';
                $('#filterDateRangeWrap').toggleClass('is-hidden', !isCustom);
                if (isCustom) {
                    if (!$('#filterDateFrom').val() && !$('#filterDateTo').val()) {
                        $('#filterDateFrom').trigger('focus');
                        return;
                    }
                }
                fetchProducts(1);
            });
            $('#filterDateFrom, #filterDateTo').on('change', function() {
                if ($('#filterDate').val() === 'custom') fetchProducts(1);
            });

            // Handle Barcode History button click
            $(document).on('click', '.barcode-history-btn', function(e) {
                e.preventDefault();
                let productId = $(this).data('id');
                let productName = $(this).attr('data-name');
                let authToken = localStorage.getItem("authToken");

                $('#bhProductName').text(productName);
                $('#barcodeHistoryModalBody').html('<tr><td colspan="3" class="text-center">Loading...</td></tr>');
                $('#barcodeHistoryModal').modal('show');

                $.ajax({
                    url: `/api/product-barcode-history/${productId}`,
                    type: "GET",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        if (response.status) {
                            let histories = response.data;
                            let html = '';
                            if (histories.length > 0) {
                                $.each(histories, function(index, history) {
                                    let date = new Date(history.created_at).toLocaleString();
                                    
                                    function formatBarcodeDetailsBlock(barcodeStr, detailsStr) {
                                        if (!barcodeStr) return '-';
                                        let barcodes = barcodeStr.split(',').map(b => b.trim()).filter(b => b);
                                        let details = {};
                                        if (detailsStr) {
                                            try {
                                                details = typeof detailsStr === 'string' ? JSON.parse(detailsStr) : detailsStr;
                                            } catch(e) {}
                                        }
                                        
                                        // If details is empty, just return the comma separated barcodes
                                        if (Object.keys(details).length === 0) {
                                            return barcodes.join(', ');
                                        }

                                        let result = '<div class="mt-1">';
                                        barcodes.forEach(b => {
                                            let d = details[b] || {};
                                            let parts = [];
                                            if (d.price) parts.push(`Price: ${d.price}`);
                                            if (d.mrp) parts.push(`MRP: ${d.mrp}`);
                                            if (d.batch_no) parts.push(`Batch: ${d.batch_no}`);
                                            if (d.label_code) parts.push(`Lbl Code: ${d.label_code}`);
                                            if (d.label_reference) parts.push(`Wholesale: ${d.label_reference}`);
                                            if (d.quantity !== undefined && d.quantity !== '') parts.push(`Qty: ${d.quantity}`);
                                            if (d.specific_qty !== undefined && d.specific_qty !== '') parts.push(`Other: ${d.specific_qty}`);
                                            
                                            if (parts.length > 0) {
                                                result += `<div class="mb-2"><span class="badge bg-secondary">${b}</span><div class="text-wrap text-break mt-1" style="font-size: 0.8em; color: #555; line-height: 1.4;">${parts.join(' | ')}</div></div>`;
                                            } else {
                                                result += `<div class="mb-2"><span class="badge bg-secondary">${b}</span></div>`;
                                            }
                                        });
                                        result += '</div>';
                                        return result;
                                    }

                                    let oldDetails = '';
                                    if(history.old_barcode !== null) {
                                        let displayBarcode = formatBarcodeDetailsBlock(history.old_barcode, history.old_barcode_details);
                                        oldDetails += `<b>Barcodes:</b> ${displayBarcode}<br>`;
                                    }
                                    if(history.old_price !== null) oldDetails += `<b>Main Price:</b> ${history.old_price || '-'}<br>`;
                                    if(history.old_mrp !== null) oldDetails += `<b>Main MRP:</b> ${history.old_mrp || '-'}<br>`;
                                    if(history.old_batch_no !== null) oldDetails += `<b>Main Batch:</b> ${history.old_batch_no || '-'}<br>`;
                                    if(history.old_label_code !== null) oldDetails += `<b>Main Lbl Code:</b> ${history.old_label_code || '-'}<br>`;
                                    if(history.old_label_reference !== null) oldDetails += `<b>Main Wholesale:</b> ${history.old_label_reference || '-'}<br>`;
                                    if(history.old_qty !== null && history.old_qty !== undefined) oldDetails += `<b>Main Qty:</b> ${history.old_qty}<br>`;
                                    if(history.old_unit !== null && history.old_unit !== undefined) oldDetails += `<b>Unit:</b> ${history.old_unit || '-'}<br>`;
                                    if(!oldDetails) oldDetails = '-';

                                    let newDetails = '';
                                    if(history.new_barcode !== null) {
                                        let displayBarcode = formatBarcodeDetailsBlock(history.new_barcode, history.new_barcode_details);
                                        newDetails += `<b>Barcodes:</b> ${displayBarcode}<br>`;
                                    }
                                    if(history.new_price !== null) newDetails += `<b>Main Price:</b> ${history.new_price || '-'}<br>`;
                                    if(history.new_mrp !== null) newDetails += `<b>Main MRP:</b> ${history.new_mrp || '-'}<br>`;
                                    if(history.new_batch_no !== null) newDetails += `<b>Main Batch:</b> ${history.new_batch_no || '-'}<br>`;
                                    if(history.new_label_code !== null) newDetails += `<b>Main Lbl Code:</b> ${history.new_label_code || '-'}<br>`;
                                    if(history.new_label_reference !== null) newDetails += `<b>Main Wholesale:</b> ${history.new_label_reference || '-'}<br>`;
                                    if(history.new_qty !== null && history.new_qty !== undefined) newDetails += `<b>Main Qty:</b> ${history.new_qty}<br>`;
                                    if(history.new_unit !== null && history.new_unit !== undefined) newDetails += `<b>Unit:</b> ${history.new_unit || '-'}<br>`;
                                    if(!newDetails) newDetails = '-';

                                    html += `
                                        <tr>
                                            <td>${oldDetails}</td>
                                            <td>${newDetails}</td>
                                            <td>${date}</td>
                                        </tr>
                                    `;
                                });
                            } else {
                                html = '<tr><td colspan="3" class="text-center">No barcode history found.</td></tr>';
                            }
                            $('#barcodeHistoryModalBody').html(html);
                        } else {
                            $('#barcodeHistoryModalBody').html('<tr><td colspan="3" class="text-center text-danger">Failed to load history.</td></tr>');
                        }
                    },
                    error: function(xhr) {
                        $('#barcodeHistoryModalBody').html('<tr><td colspan="3" class="text-center text-danger">Error loading history.</td></tr>');
                    }
                });
            });

            // ===== All Product Price History Modal Logic =====
            $(document).on('click', '#productPriceHistoryBtn', function(e) {
                e.preventDefault();
                let authToken = localStorage.getItem("authToken");

                $('#allProductPriceHistoryModalBody').html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');
                $('#allProductPriceHistoryModal').modal('show');

                $.ajax({
                    url: `/api/all-product-price-history`,
                    type: "GET",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        if (response.status) {
                            let histories = response.data;
                            let html = '';
                            if (histories.length > 0) {
                                $.each(histories, function(index, history) {
                                    let date = new Date(history.created_at).toLocaleString();
                                    let productName = history.product ? history.product.name : 'Unknown';
                                    let oldMrp = history.old_mrp !== null ? history.old_mrp : '-';
                                    let newMrp = history.new_mrp !== null ? history.new_mrp : '-';
                                    let oldPrice = history.old_price !== null ? history.old_price : '-';
                                    let newPrice = history.new_price !== null ? history.new_price : '-';

                                    html += `
                                        <tr>
                                            <td>${productName}</td>
                                            <td>${oldMrp}</td>
                                            <td>${newMrp}</td>
                                            <td>${oldPrice}</td>
                                            <td>${newPrice}</td>
                                            <td>${date}</td>
                                        </tr>
                                    `;
                                });
                            } else {
                                html = '<tr><td colspan="6" class="text-center">No price history found.</td></tr>';
                            }
                            $('#allProductPriceHistoryModalBody').html(html);
                        } else {
                            $('#allProductPriceHistoryModalBody').html('<tr><td colspan="6" class="text-center text-danger">Failed to load history.</td></tr>');
                        }
                    },
                    error: function(xhr) {
                        $('#allProductPriceHistoryModalBody').html('<tr><td colspan="6" class="text-center text-danger">Error loading history.</td></tr>');
                    }
                });
            });

            // ===== Print Barcode / Edit Label Modal Logic =====
            function isMobileDevice() {
                // Treat phone + tablet the same for barcode popup / Bluetooth print UX
                return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
                    || (window.matchMedia && window.matchMedia('(max-width: 1024.98px), (width: 1032px) and (height: 1376px), (width: 1280px) and (height: 800px)').matches);
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
                if (!html) {
                    return html;
                }
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
                if (!w || w.closed) {
                    return false;
                }

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
                if (!frame) {
                    return false;
                }

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
                if (!event || !event.data || event.data.type !== 'barcodePrintDone') {
                    return;
                }
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
                        // Wholesale has its own field. It is not a selling rate for the label.
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
                        // No MRP/Rate selected → keep labels, clear numbers only
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

                    // Only keep MRP/Rate when a selling-rate option is selected
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
                            // Capture current form state to inherit
                            let currentDetails = {
                                price: $('#pbPrice').val(),
                                mrp: $('#pbMrp').val(),
                                batch_no: $('#pbBatchNo').val(),
                                label_reference: $('#pbLabelRef').val(),
                                specific_qty: $('#pbSpecificQty').val()
                            };

                            pbBarcodes.push(val);
                            saveCurrentBarcodeDetails();
                            
                            // Assign inherited details to the new barcode
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
                if (!canvas || typeof canvas.getObjects !== 'function') {
                    return presence;
                }
                canvas.getObjects().forEach(function(obj) {
                    if (!(obj.type === 'i-text' || obj.type === 'text' || obj.type === 'textbox')) {
                        return;
                    }
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
                // If template has separate batch/wholesale/qty fields, barcode_text is number-only
                // so those values are not printed twice.
                presence = presence || {};
                if (presence.hasBatch || presence.hasWholesale || presence.hasQty) {
                    return ctx.barcode || '';
                }
                return ctx.barcodeFooter || ctx.barcode || '';
            }

            function applyCustomTemplateObjectText(obj, ctx, presence) {
                if (!(obj.type === 'i-text' || obj.type === 'text' || obj.type === 'textbox')) {
                    return;
                }
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
                    // Combined Classic price line: "@{{mrp}}  @{{selling_price}}"
                    if (obj.isPriceLine || (/\{\{\s*mrp\s*\}\}/i.test(txt) && /\{\{\s*(selling_price|price|rate)\s*\}\}/i.test(txt))) {
                        txt = ctx.mrpTxt + '  ' + ctx.rateTxt;
                        obj.set('isPriceLine', true);
                    } else {
                    // Recover variables if designer text was edited but still contains placeholders / MRP|RATE labels
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
                    // If object only says "MRP" / "RATE" (common after manual edit), fill full value
                    if (/^\s*MRP\s*-?\s*$/i.test(txt)) {
                        txt = ctx.mrpTxt;
                        vf = 'mrp';
                    } else if (/^\s*RATE\s*-?\s*$/i.test(txt)) {
                        txt = ctx.rateTxt;
                        vf = 'selling_price';
                    } else if (/^\s*MRP\s*-/i.test(txt) && !/\d/.test(txt)) {
                        txt = ctx.mrpTxt;
                        vf = 'mrp';
                    } else if (/^\s*RATE\s*-/i.test(txt) && !/\d/.test(txt)) {
                        txt = ctx.rateTxt;
                        vf = 'selling_price';
                    }
                    txt = txt.replace(/POOJA SAMAN\.IN\s+DURG,RISALI,RAIPUR/gi, ctx.companyName + ' ' + ctx.companyAddress);
                    txt = txt.replace(/Fablead Erp/gi, ctx.companyName);
                    txt = txt.replace(/12345454/g, ctx.barcode);
                    }
                }
                obj.set('text', txt);
                // Price lines must stay on one line (no grapheme wrap clipping "MRP-130" to "MRP")
                if (obj.isPriceLine || vf === 'mrp' || vf === 'price' || vf === 'selling_price' || vf === 'rate' ||
                    /^MRP-/i.test(txt) || /^RATE-/i.test(txt)) {
                    obj.set({
                        splitByGrapheme: false,
                        objectCaching: false
                    });
                    try {
                        if (typeof obj.initDimensions === 'function') {
                            obj.initDimensions();
                        }
                        var needed = (obj.calcTextWidth ? obj.calcTextWidth() : 0) + 4;
                        if (needed > (obj.width || 0)) {
                            obj.set('width', Math.ceil(needed));
                        }
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

            // Fabric rotates around originX/originY; map that to CSS transform-origin
            // after converting left/top to the unrotated box top-left.
            // Fabric default centeredRotation=true → always rotate around center.
            function fabricOriginToCss(ox, oy, obj) {
                if (!obj || obj.centeredRotation !== false) {
                    return 'center center';
                }
                var x = 'left';
                var y = 'top';
                if (ox === 'center') x = 'center';
                else if (ox === 'right') x = 'right';
                if (oy === 'center') y = 'center';
                else if (oy === 'bottom') y = 'bottom';
                return x + ' ' + y;
            }

            function cssRotationInline(angleDeg, originCss) {
                var a = parseFloat(angleDeg) || 0;
                if (Math.abs(a) < 0.05) return '';
                var o = originCss || 'center center';
                return 'transform:rotate(' + a.toFixed(3) + 'deg);' +
                    '-webkit-transform:rotate(' + a.toFixed(3) + 'deg);' +
                    'transform-origin:' + o + ';-webkit-transform-origin:' + o + ';';
            }

            // Unrotated top-left of object box (handles rotation + center origin)
            function getUnrotatedBoxTopLeft(obj, boxW, boxH) {
                var angle = parseFloat(obj.angle) || 0;
                if (Math.abs(angle) >= 0.5 && typeof obj.getCenterPoint === 'function') {
                    try {
                        var c = obj.getCenterPoint();
                        return { left: c.x - boxW / 2, top: c.y - boxH / 2 };
                    } catch (e) {}
                }
                if (obj.barcodeBoxWidth && obj.barcodeBoxHeight && obj.barcodeBoxLeft != null) {
                    return {
                        left: obj.barcodeBoxLeft,
                        top: obj.barcodeBoxTop != null ? obj.barcodeBoxTop : (obj.top || 0)
                    };
                }
                var leftPx = obj.left || 0;
                var topPx = obj.top || 0;
                var ox = obj.originX || 'left';
                var oy = obj.originY || 'top';
                if (ox === 'center') leftPx -= boxW / 2;
                else if (ox === 'right') leftPx -= boxW;
                if (oy === 'center') topPx -= boxH / 2;
                else if (oy === 'bottom') topPx -= boxH;
                return { left: leftPx, top: topPx };
            }

            function densityToModuleWidth(density) {
                return window.densityToModuleWidth(density);
            }

            function densityToOccupancy(density) {
                return window.densityToOccupancy(density);
            }

            function buildBarcodeSvgDataUrl(code, density, targetWpx, targetHpx) {
                return window.buildBarcodeSvgDataUrl(code, density, targetWpx, targetHpx);
            }

            function fabricPrintTextPayload(obj) {
                var raw = String(obj.text || '');
                var wrap = obj.type === 'textbox';
                var lines = null;
                try {
                    if (obj._textLines && obj._textLines.length) {
                        lines = obj._textLines.map(function(line) {
                            return Array.isArray(line) ? line.join('') : String(line);
                        });
                        if (lines.length > 1) wrap = true;
                    }
                } catch (e) {}
                if (/\r?\n/.test(raw)) wrap = true;
                var text = (lines && lines.length) ? lines.join('\n') : raw;
                return {
                    text: String(text || '').replace(/\s+$/g, ''),
                    wrap: !!wrap,
                    lineCount: (lines && lines.length) ? lines.length : (wrap && /\n/.test(text) ? text.split(/\n/).length : 1)
                };
            }

            // Fabric centers/right-aligns text inside its own box using canvas metrics.
            // Re-doing that with CSS shifts fields and makes them overlap, so resolve the
            // exact glyph start X here and print left-aligned from that point.
            function resolveTextGlyphBox(obj, boxLeftPx, boxWidthPx) {
                var align = obj.textAlign || 'left';
                var lines = 1;
                try {
                    if (obj._textLines && obj._textLines.length) lines = obj._textLines.length;
                } catch (e) {}
                if (align === 'left' || lines > 1) {
                    return { leftPx: boxLeftPx, widthPx: boxWidthPx, align: align };
                }
                var textWidth = 0;
                try {
                    if (typeof obj.calcTextWidth === 'function') textWidth = obj.calcTextWidth();
                } catch (e) {}
                if (!textWidth || !isFinite(textWidth) || textWidth <= 0) {
                    return { leftPx: boxLeftPx, widthPx: boxWidthPx, align: align };
                }
                var offset = 0;
                if (align === 'center') offset = (boxWidthPx - textWidth) / 2;
                else if (align === 'right') offset = boxWidthPx - textWidth;
                return { leftPx: boxLeftPx + offset, widthPx: textWidth, align: 'left' };
            }

            function wrapLabelWords(measure, source, maxWidth) {
                var words = String(source || '').split(/\s+/).filter(function(word) { return word !== ''; });
                if (!words.length) {
                    return [''];
                }
                var wrapped = [];
                var current = '';
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
                if (field !== 'product_name') {
                    return;
                }
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
                if (lines.length <= 2) {
                    return;
                }
                lines = [lines[0], ellipsizeLabelLine(measure, lines[1], maxWidth)];
                obj.set('text', lines.join('\n'));
                try {
                    if (typeof obj.initDimensions === 'function') {
                        obj.initDimensions();
                    }
                } catch (e) {}
            }

            function hardenCustomLabelTextObjects(canvas, heightMm) {
                // Keep designer font size, position, and alignment — only fix stretch / clip / color
                canvas.getObjects().forEach(function(obj) {
                    if (!(obj.type === 'i-text' || obj.type === 'text' || obj.type === 'textbox')) {
                        return;
                    }
                    var textVal = String(obj.text || '');
                    var vf = '';
                    if (obj.isVariable && obj.variableField) {
                        vf = String(obj.variableField).replace(/[{}@]/g, '').trim().toLowerCase();
                    }

                    // Bake scaleX/scaleY into fontSize/width so print is not stretched
                    var sx = obj.scaleX || 1;
                    var sy = obj.scaleY || 1;
                    var fontSize = parseFloat(obj.fontSize) || 18;
                    var width = parseFloat(obj.width) || 100;
                    if (Math.abs(sx - 1) > 0.01 || Math.abs(sy - 1) > 0.01) {
                        fontSize = Math.max(6, Math.round(fontSize * sy));
                        width = Math.max(20, width * sx);
                    }

                    if (vf === 'company_name' || vf === 'company_address' || vf === 'address' || vf === 'product_name') {
                        textVal = textVal.toUpperCase();
                    }

                    var isPrice = !!obj.isPriceLine ||
                        vf === 'mrp' || vf === 'price' || vf === 'selling_price' || vf === 'rate' ||
                        /^MRP-/i.test(textVal) || /^RATE-/i.test(textVal) ||
                        (/^MRP-/i.test(textVal) && /RATE-/i.test(textVal));

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
                        if (typeof obj.initDimensions === 'function') {
                            obj.initDimensions();
                        }
                        // Widen price boxes if text would clip — do not move left/top
                        if (isPrice && typeof obj.calcTextWidth === 'function') {
                            var needed = obj.calcTextWidth() + 8;
                            if (needed > (obj.width || 0)) {
                                obj.set('width', Math.ceil(needed));
                            }
                        }
                    } catch (e) {}

                    clipProductNameToTwoLines(obj);
                    if (typeof obj.setCoords === 'function') {
                        obj.setCoords();
                    }
                });
            }

            function renderCustomTemplateDataUrl(tpl, opts) {
                // Kept for modal preview compatibility — print uses HTML path below
                return renderCustomTemplateLayers(tpl, opts).then(function(layers) {
                    return new Promise(function(resolve, reject) {
                        try {
                            var designScale = 10;
                            var exportMultiplier = 8;
                            var w = Math.round(layers.printWidthMm * designScale * exportMultiplier);
                            var h = Math.round(layers.printHeightMm * designScale * exportMultiplier);
                            var c = document.createElement('canvas');
                            c.width = w;
                            c.height = h;
                            var ctx2d = c.getContext('2d');
                            ctx2d.fillStyle = '#ffffff';
                            ctx2d.fillRect(0, 0, w, h);
                            ctx2d.fillStyle = '#000000';
                            ctx2d.textBaseline = 'top';

                            var pending = 0;
                            var failed = false;
                            function doneOne() {
                                pending -= 1;
                                if (pending > 0 || failed) return;
                                resolve({
                                    dataUrl: c.toDataURL('image/png'),
                                    widthMm: layers.printWidthMm,
                                    heightMm: layers.printHeightMm
                                });
                            }

                            layers.elements.forEach(function(el) {
                                var x = (el.leftMm / layers.printWidthMm) * w;
                                var y = (el.topMm / layers.printHeightMm) * h;
                                var ew = (el.widthMm / layers.printWidthMm) * w;
                                var eh = (el.heightMm / layers.printHeightMm) * h;
                                var ang = parseFloat(el.angle) || 0;
                                var hasRot = Math.abs(ang) >= 0.05;

                                function applyRot() {
                                    if (!hasRot) return;
                                    ctx2d.save();
                                    var ox = x;
                                    var oy = y;
                                    var origin = String(el.transformOrigin || 'top left');
                                    if (origin.indexOf('center') === 0 || origin === 'center center') ox = x + ew / 2;
                                    else if (origin.indexOf('right') === 0) ox = x + ew;
                                    if (/\scenter$/.test(origin) || origin === 'center center') oy = y + eh / 2;
                                    else if (origin.indexOf('bottom') >= 0) oy = y + eh;
                                    ctx2d.translate(ox, oy);
                                    ctx2d.rotate(ang * Math.PI / 180);
                                    ctx2d.translate(-ox, -oy);
                                }

                                if (el.type === 'text') {
                                    applyRot();
                                    var fs = Math.max(8, (el.fontSizeMm / layers.printHeightMm) * h);
                                    ctx2d.font = (el.fontWeight === 'bold' ? 'bold ' : '') +
                                        (el.fontStyle === 'italic' ? 'italic ' : '') +
                                        fs + 'px ' + (el.fontFamily || 'Arial');
                                    ctx2d.textAlign = el.textAlign || 'left';
                                    var tx = x;
                                    if (el.textAlign === 'center') tx = x + ew / 2;
                                    else if (el.textAlign === 'right') tx = x + ew;
                                    var lines = String(el.text || '').split(/\r\n|\n|\r/);
                                    var lineH = fs * 1.15;
                                    for (var li = 0; li < lines.length; li++) {
                                        ctx2d.fillText(lines[li], tx, y + li * lineH, ew);
                                    }
                                    if (hasRot) ctx2d.restore();
                                } else if ((el.type === 'barcode' || el.type === 'image') && el.src) {
                                    pending += 1;
                                    var img = new Image();
                                    img.onload = function() {
                                        try {
                                            applyRot();
                                            ctx2d.imageSmoothingEnabled = false;
                                            ctx2d.drawImage(img, x, y, ew, eh);
                                            if (hasRot) ctx2d.restore();
                                        } catch (e) {}
                                        doneOne();
                                    };
                                    img.onerror = function() { doneOne(); };
                                    img.src = el.src;
                                }
                            });
                            if (pending === 0) {
                                resolve({
                                    dataUrl: c.toDataURL('image/png'),
                                    widthMm: layers.printWidthMm,
                                    heightMm: layers.printHeightMm
                                });
                            }
                        } catch (err) {
                            reject(err);
                        }
                    });
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
                        var field = '';
                        if (obj.isVariable && obj.variableField) {
                            field = String(obj.variableField).replace(/[{}@]/g, '').trim().toLowerCase();
                        }
                        var isPrice = !!obj.isPriceLine || field === 'mrp' || field === 'price' || field === 'selling_price' || field === 'rate' || /^MRP-/i.test(text) || /^RATE-/i.test(text);
                        var boxW = Math.max(1, (obj.width || 1) * (obj.scaleX || 1));
                        var maxWidth = Math.max(8, boxW * scale - pad * 2);
                        var shouldWrap = !isPrice && (obj.type === 'textbox' || field === 'product_name');

                        function wrapLine(source) {
                            var words = String(source || '').split(/\s+/).filter(function(word) { return word !== ''; });
                            if (!words.length) {
                                return [''];
                            }
                            var wrapped = [];
                            var current = '';
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
                            obj._textLines.forEach(function(line) {
                                lines.push(Array.isArray(line) ? line.join('') : String(line));
                            });
                        } else {
                            text.split(/\r?\n/).forEach(function(source) {
                                if (shouldWrap) {
                                    wrapLine(source).forEach(function(line) { lines.push(line); });
                                } else {
                                    lines.push(source);
                                }
                            });
                        }
                        if (!lines.length) lines = [''];
                        if (field === 'product_name' && lines.length > 2) {
                            lines = [lines[0], ellipsizeLabelLine(measure, lines[1], maxWidth)];
                        }

                        var maxLine = 0;
                        lines.forEach(function(line) {
                            maxLine = Math.max(maxLine, measure.measureText(line || ' ').width);
                        });
                        if (shouldWrap) {
                            maxLine = Math.min(maxLine, maxWidth);
                        }
                        var board = document.createElement('canvas');
                        board.width = Math.max(1, Math.ceil(maxLine) + pad * 2);
                        board.height = Math.max(1, lineH * lines.length + pad);
                        var c = board.getContext('2d');
                        c.font = font;
                        c.fillStyle = '#000000';
                        c.textBaseline = 'middle';
                        var lineAlign = obj.textAlign || 'left';
                        var innerW = board.width - pad * 2;
                        lines.forEach(function(line, i) {
                            var x = pad;
                            if (lineAlign === 'center' || lineAlign === 'right') {
                                var free = Math.max(0, innerW - c.measureText(line).width);
                                x += lineAlign === 'center' ? free / 2 : free;
                            }
                            c.fillText(line, x, pad / 2 + lineH * i + lineH / 2);
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
                        if (holder && holder.parentNode) {
                            holder.parentNode.removeChild(holder);
                        }
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

                        // True WYSIWYG — print exact designer positions (no auto-shift / gap compression)
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
                                var drawn = window.buildBarcodeSvgDataUrl(
                                    ctx.barcode,
                                    density,
                                    boxPxW,
                                    boxPxH,
                                    boxPxW,
                                    printPxPerMm
                                );
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
                                // Keep full textbox width when wrapping — glyph shrink causes cut-off on print
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

                                // Draw the words at their real size. A heavy stroke made print too bold and cut letters.
                                var textImg = null;
                                try {
                                    textImg = rasterizeLabelTextImage(obj);
                                } catch (eRaster) {
                                    textImg = null;
                                }

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
                    }, function(o, object) {
                        if (o && object) {
                            if (o.isBarcode !== undefined) object.isBarcode = o.isBarcode;
                            if (o.isVariable !== undefined) object.isVariable = o.isVariable;
                            if (o.variableField !== undefined) object.variableField = o.variableField;
                            if (o.isPriceLine !== undefined) object.isPriceLine = o.isPriceLine;
                            if (o.barcodeDensity !== undefined) object.barcodeDensity = o.barcodeDensity;
                            if (o.barcodeBaseWidth !== undefined) object.barcodeBaseWidth = o.barcodeBaseWidth;
                            if (o.barcodeBoxLeft !== undefined) object.barcodeBoxLeft = o.barcodeBoxLeft;
                            if (o.barcodeBoxTop !== undefined) object.barcodeBoxTop = o.barcodeBoxTop;
                            if (o.barcodeBoxWidth !== undefined) object.barcodeBoxWidth = o.barcodeBoxWidth;
                            if (o.barcodeBoxHeight !== undefined) object.barcodeBoxHeight = o.barcodeBoxHeight;
                        }
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

                function mmToPt(mm) {
                    return (parseFloat(mm) || 0) * 2.834645669;
                }

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

                // Table layout like Classic — flex often breaks in Chrome thermal print preview
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
            window.renderCustomTemplateDataUrl = renderCustomTemplateDataUrl;
            window.renderCustomTemplateLayers = renderCustomTemplateLayers;
            window.buildCustomLabelPrintHtml = buildCustomLabelPrintHtml;
            // buildBarcodeSvgDataUrl / density helpers are defined globally above (do not overwrite)

                function saveCurrentBarcodeDetails() {
                    let currentEdit = $('#pbCurrentEditBarcode').val();
                    let inputValue = $('#pbBarcode_input').val().trim();

                    if (currentEdit) {
                        // If they changed the barcode number in the input box, rename it!
                        if (inputValue && inputValue !== currentEdit && !pbBarcodes.includes(inputValue)) {
                            let index = pbBarcodes.indexOf(currentEdit);
                            if (index !== -1) {
                                pbBarcodes[index] = inputValue;
                                $('#pbBarcode').val(pbBarcodes.join(','));
                                
                                // Transfer details to new barcode name
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
                    // Keep full template selection (classic / split / custom_*) as default for next products
                    try {
                        localStorage.setItem('barcodeLabelFormat', ($('#pbBarcodeTemplate').val() || 'classic').toString());
                    } catch (e) {}
                    localStorage.setItem('barcodeIncludeTitle', includeTitle);
                    var isMobile = isMobileDevice();
                    var btTarget = window.LabelBluetoothPrint ? window.LabelBluetoothPrint.target() : '';
                    // Mobile: stay on same page (overlay). Desktop: popup that auto-closes after print.
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
                    // Omit empty price/mrp so print-without-rate does not look like a catalog price wipe
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
                                    // Print on selected stock size (35×25 × 3 = 4.13×0.98 in), scale design to fit
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
            
            function parseBarcodeWholesaleValue(raw) {
                raw = String(raw || '').trim();
                if (!raw) {
                    return null;
                }
                // Keep the typed wholesale. 111115 must stay 111115, not 11 + 1115.
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
                if (parsed === null) {
                    return '';
                }
                if (Math.round(parsed) === parsed) {
                    return String(Math.round(parsed));
                }
                return String(parsed);
            }

            function wholesaleBarcodeSuffix(val) {
                return formatBarcodeWholesaleRef(val);
            }

            function sanitizeBatchNoValue(batchVal, wholesaleVal) {
                batchVal = String(batchVal || '').trim();
                if (!batchVal) {
                    return '';
                }
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
                    if (formatted) {
                        return formatted;
                    }
                }
                let rates = window.productSellingPrices || [];
                for (let j = 0; j < rates.length; j++) {
                    if (/wholesale/i.test(String(rates[j].name || ''))) {
                        let formattedRate = formatWholesaleInputValue(rates[j].price);
                        if (formattedRate) {
                            return formattedRate;
                        }
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
                if (!$('#pbShowBatch').is(':checked')) {
                    return '';
                }
                return ($('#pbBatchNo').val() || '').trim();
            }

            function formatBarcodeWholesaleRef(val) {
                if (!shouldShowWholesaleOnBarcode()) {
                    return '';
                }
                val = String(val || '').trim();
                if (!val) {
                    return '';
                }
                let parsed = parseBarcodeWholesaleValue(val);
                if (parsed === null) {
                    return '';
                }
                let amount = formatLabelAmountForBarcode(parsed);
                // 11 is the barcode prefix on every wholesale amount. 117 prints as 11117.
                return '11' + amount;
            }

            function syncBarcodeTemplateFormat() {
                var templateVal = ($('#pbBarcodeTemplate').val() || 'classic').toString();
                if (templateVal === 'split' || templateVal === 'classic') {
                    $('#pbLabelFormat').val(templateVal);
                } else if (templateVal.indexOf('custom_') === 0) {
                    // Custom designer templates still use classic layout engine + canvas overlay
                    $('#pbLabelFormat').val('classic');
                }
                try {
                    // Remember for every product (classic / split / custom_*)
                    localStorage.setItem('barcodeLabelFormat', templateVal);
                } catch (e) {}
                // Custom templates: keep 35×25 × 3 columns to match TVS stock 4.13×0.98 in (105×25 mm strip)
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
                        // Near-standard sticker → use 35×25 (one label on the 4.13" strip)
                        if (tw >= 28 && tw <= 38 && th >= 24 && th <= 26) {
                            w = 35;
                            h = 25;
                            if (printer === 'tvs') {
                                cols = 3;
                            }
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
                            let designScale = 10; // Designer uses 10px per mm
                            
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
                                    // Normalize stretch only — keep designer font sizes / positions
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
                    // Only fill numbers when a rate option is chosen; labels stay visible.
                    if ($('#pbRateSelect').val()) {
                        mrp = $('#pbMrp').val();
                        price = $('#pbPrice').val();
                    }
                    let blankPad = '______';
                    let mrpTxt = 'MRP-' + (mrp ? parseFloat(mrp).toLocaleString('en-IN') : blankPad);
                    let rateTxt = 'RATE-' + (price ? parseFloat(price).toLocaleString('en-IN') : blankPad);
                    let extras = [qty, bNo, lblRef].filter(function (v) { return v; }).join('  ');
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
                    
                    // 1mm = 3.78px (96dpi)
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
                        // Prefer thicker modules so printed labels remain scannable.
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
                    } catch(e) {
                        // ignore invalid barcodes
                    }
                }
            }

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
                if (!productId || !hindi) {
                    return;
                }
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
                if (batch === String(window.globalBatchNo || '').trim()) {
                    return;
                }
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
                if (e.originalEvent) {
                    sharedBatchDirty = true;
                }
            });
            $('#pbBatchNo').on('blur', function() {
                if (!sharedBatchDirty) {
                    return;
                }
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
            
            // Open Custom Designer with this product's print details as sample/preview values
            $('#pbOpenCustomDesigner').on('click', function(e) {
                e.preventDefault();
                var ctx = typeof getCustomLabelPrintContext === 'function' ? getCustomLabelPrintContext() : {};
                var sample = {
                    company_name: ctx.companyName || 'POOJA SAMAN.IN',
                    company_address: ctx.companyAddress || 'DURG, RISALI, RAIPUR',
                    address: ctx.companyAddress || 'DURG, RISALI, RAIPUR',
                    product_name: ctx.prodName || ($('#pbProductName').text() || '').trim(),
                    mrp: ctx.mrpTxt || '',
                    selling_price: ctx.rateTxt || '',
                    price: ctx.rateTxt || '',
                    barcode: ctx.barcode || '',
                    barcode_text: ctx.barcode || '',
                    sku: ctx.labelRef || '',
                    wholesale: ctx.labelRef || '',
                    batch_no: ctx.batchNo || '',
                    other: ctx.qty || '',
                    qty: ctx.qty || ''
                };
                try {
                    localStorage.setItem('barcodeDesignerSample', JSON.stringify(sample));
                } catch (err) {}
                var tpl = String($('#pbBarcodeTemplate').val() || '').trim();
                var url = @json(route('barcode.designer'));
                var qs = [];
                // Dropdown uses custom_3; designer API needs numeric id=3
                if (tpl.indexOf('custom_') === 0) {
                    var numericId = tpl.replace(/^custom_/, '');
                    if (numericId) {
                        qs.push('id=' + encodeURIComponent(numericId));
                    }
                }
                qs.push('preview=1');
                url += (url.indexOf('?') >= 0 ? '&' : '?') + qs.join('&');
                window.open(url, '_blank');
            });

            function isTabletBarcodeUi() {
                return window.matchMedia
                    && window.matchMedia('(min-width: 768px) and (max-width: 1024.98px), (width: 1032px) and (height: 1376px), (width: 1280px) and (height: 800px)').matches;
            }

            function applyBarcodePopupCompactUi() {
                var compact = isMobileDevice();
                var tablet = compact && isTabletBarcodeUi();
                var $modal = $('#printBarcodeModal');
                $modal.toggleClass('pb-compact', compact);
                $modal.toggleClass('pb-tablet', tablet);
                $('body').toggleClass('pb-compact-open', compact && $modal.hasClass('show'));
                $('#pbMobilePrintHelp').toggleClass('d-none', !compact);
                $('#pbPrinterMobileHint').toggleClass('d-none', !compact);
            }

            // Apply compact UI before paint so tablet never flashes desktop layout
            $('#printBarcodeModal').on('show.bs.modal', function () {
                applyBarcodePopupCompactUi();
            });
            
            // Trigger initial render when modal opens
            $('#printBarcodeModal').on('shown.bs.modal', function () {
                applyBarcodePopupCompactUi();

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

            $('#printBarcodeModal').on('hidden.bs.modal', function () {
                $('body').removeClass('pb-compact-open');
                $(this).removeClass('pb-compact pb-tablet');
            });

            $(window).on('resize.pbCompact', function() {
                if ($('#printBarcodeModal').hasClass('show')) {
                    applyBarcodePopupCompactUi();
                }
            });
        })

        /** Restore last chosen barcode template as default for every product. */
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
                // Custom template not loaded yet / deleted — fall back to classic
                $sel.val('classic');
            }
            var fmt = ($sel.val() === 'split') ? 'split' : 'classic';
            $('#pbLabelFormat').val(fmt);
        }


        $(document).on('click', '.delete-product', function() {
            var productId = $(this).data('id'); // Get product ID
            var authToken = localStorage.getItem("authToken");
            Swal.fire({
                title: "Move to Deleted Products?",
                text: "Product will be hidden from Active list. You can restore it anytime from Deleted Products.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#ff9f43",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Yes, delete it"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/api/deleteProduct/${productId}`,
                        type: 'POST',
                        headers: {
                            "Authorization": "Bearer " + authToken,
                        },
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    title: "Deleted!",
                                    text: response.message || "Moved to Deleted Products",
                                    icon: "success",
                                    confirmButtonColor: "#ff9f43",
                                    confirmButtonText: "OK"
                                });
                                if (typeof window.fetchProducts === 'function') {
                                    window.fetchProducts(window.productListCurrentPage || 1);
                            } else {
                                    $(`.delete-product[data-id="${productId}"]`).closest('tr').remove();
                                }
                            } else {
                                Swal.fire("Error!", response.error || response.message ||
                                    "Unknown error", "error");
                            }
                        },
                        error: function(xhr) {
                            let message = "Something went wrong!";
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                message = xhr.responseJSON.error;
                            }
                            Swal.fire({
                                title: "Error!",
                                text: message,
                                icon: "error",
                                confirmButtonColor: "#ff9f43",
                                confirmButtonText: "OK"
                            });
                        }
                    });
                }
            });
        });

        $(document).on('click', '.restore-product', function() {
            var productId = $(this).data('id');
            var authToken = localStorage.getItem("authToken");
            Swal.fire({
                title: "Restore this product?",
                text: "It will appear again in Active Products.",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#28a745",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Yes, restore"
            }).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }
                $.ajax({
                    url: `/api/restoreProduct/${productId}`,
                    type: 'POST',
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                title: "Restored!",
                                text: response.message || "Product restored successfully",
                                icon: "success",
                                confirmButtonColor: "#ff9f43",
                                confirmButtonText: "OK"
                            });
                            if (typeof window.fetchProducts === 'function') {
                                window.fetchProducts(window.productListCurrentPage || 1);
                            } else {
                                $(`.restore-product[data-id="${productId}"]`).closest('tr').remove();
                            }
                        } else {
                            Swal.fire("Error!", response.error || response.message || "Unknown error", "error");
                        }
                    },
                    error: function(xhr) {
                        let message = "Something went wrong!";
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            message = xhr.responseJSON.error;
                        }
                        Swal.fire({
                            title: "Error!",
                            text: message,
                            icon: "error",
                            confirmButtonColor: "#ff9f43",
                            confirmButtonText: "OK"
                        });
                    }
                });
            });
        });

        function plDateQuery() {
            var preset = $('#filterDate').val() || '';
            if (!preset) return '';
            var params = {
                date_filter: preset,
                date_field: 'any'
            };
            if (preset === 'custom') {
                params.date_from = $('#filterDateFrom').val() || '';
                params.date_to = $('#filterDateTo').val() || '';
            }
            return '&' + $.param(params);
        }

        $('#exportAllChallan').click(function() {
            let selectedCategory = $('#filterCategory').val() || '';
            let selectedBrand = $('#filterBrand').val() || '';
            const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
            let authToken = localStorage.getItem("authToken");

            let url =
                `/api/export-product?category_id=${selectedCategory}&brand_id=${selectedBrand}&selectedSubAdminId=${selectedSubAdminId}` + plDateQuery();

            $.ajax({
                url: url,
                method: "GET",
                headers: {
                    "Authorization": "Bearer " + authToken
                },

                beforeSend: function() {
                    Swal.fire({
                        title: "Exporting Excel...",
                        text: "Please wait while we generate your file.",
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },

                success: function(response) {
                    Swal.close();

                    if (response.status && response.file_url) {
                        Swal.fire({
                            icon: "success",
                            title: "Excel Exported!",
                            text: "Click the button below to download your Excel file.",
                            showConfirmButton: true,
                            confirmButtonColor: "#28a745",
                            confirmButtonText: "Download File"
                        }).then(() => {
                            let link = document.createElement('a');
                            link.href = response.file_url;
                            link.download = response.file_name || "Products.xlsx";
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Export Failed!",
                            text: "Unable to export Excel file. Please try again."
                        });
                    }
                },

                error: function(xhr, status, error) {
                    Swal.close();
                    console.error("Export failed:", error);

                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Export failed. Please try again."
                    });
                }
            });
        });


        $('#exportPdf').click(function() {
            let selectedCategory = $('#filterCategory').val() || '';
            let selectedBrand = $('#filterBrand').val() || '';
            const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
            let authToken = localStorage.getItem("authToken");

            let url =
                `/api/export-product-pdf?category_id=${selectedCategory}&brand_id=${selectedBrand}&selectedSubAdminId=${selectedSubAdminId}` + plDateQuery();

            $.ajax({
                url: url,
                method: "GET",
                headers: {
                    "Authorization": "Bearer " + authToken
                },

                beforeSend: function() {
                    Swal.fire({
                        title: "Generating PDF...",
                        text: "Please wait",
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },

                success: function(response) {
                    Swal.close();

                    if (response.status && response.file_url) {
                        Swal.fire({
                            icon: "success",
                            title: "PDF Generated Successfully!",
                            text: "Your product PDF is ready.",
                            showConfirmButton: true,
                            confirmButtonColor: "#28a745",
                            confirmButtonText: "Download PDF"
                        }).then(() => {
                            let link = document.createElement('a');
                            link.href = response.file_url;
                            link.download = response.file_name || "products.pdf";
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Failed!",
                            text: "Could not generate the PDF. Please try again."
                        });
                    }
                },

                error: function(xhr, status, error) {
                    Swal.close();
                    console.error("Export PDF failed:", error);

                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Export PDF failed. Please try again."
                    });
                }
            });
        });

        // ===== Download All Barcodes PDF =====
        $('#downloadAllBarcodes').click(function() {
            let selectedCategory = $('#filterCategory').val() || '';
            let selectedBrand = $('#filterBrand').val() || '';
            const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
            let authToken = localStorage.getItem("authToken");

            let url =
                `/api/export-all-barcodes-pdf?category_id=${selectedCategory}&brand_id=${selectedBrand}&selectedSubAdminId=${selectedSubAdminId}` + plDateQuery();

            $.ajax({
                url: url,
                method: "GET",
                headers: {
                    "Authorization": "Bearer " + authToken
                },
                beforeSend: function() {
                    Swal.fire({
                        title: "Generating Barcodes PDF...",
                        text: "Please wait",
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function(response) {
                    Swal.close();
                    if (response.status && response.file_url) {
                        let link = document.createElement('a');
                        link.href = response.file_url;
                        link.download = response.file_name || "All_Barcodes.pdf";
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Failed!",
                            text: "Could not generate barcodes PDF.",
                            confirmButtonColor: "#ff9f43"
                        });
                    }
                },
                error: function() {
                    Swal.close();
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Export barcodes PDF failed. Please try again.",
                        confirmButtonColor: "#ff9f43"
                    });
                }
            });
        });

        $(document).on('click', '.toggle-details', function() {
            let icon = $(this).find('i');
            if (icon.hasClass('fa-plus-circle')) {
                icon.removeClass('fa-plus-circle')
                    .addClass('fa-minus-circle')
                    .css('color', 'red'); // or any color you want for minus icon
            } else {
                icon.removeClass('fa-minus-circle')
                    .addClass('fa-plus-circle')
                    .css('color', '#ff9f43'); // your desired orange color
            }
        });

        // ===== Barcode PDF Download =====
        $(document).on('click', '.download-barcode-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var productId = $(this).data('id');
            var barcode = String($(this).data('barcode') || '');
            var name = $(this).attr('data-name') || 'Product';
            var authToken = localStorage.getItem("authToken");

            if (!barcode || barcode.trim() === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Barcode',
                    text: 'This product does not have a barcode assigned.',
                    confirmButtonColor: '#ff9f43'
                });
                return;
            }

            Swal.fire({
                title: 'Generating PDF...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: '/api/product-barcode-pdf/' + productId,
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + authToken
                },
                success: function(response) {
                    Swal.close();
                    if (response.status && response.file_url) {
                        var link = document.createElement('a');
                        link.href = response.file_url;
                        link.download = response.file_name || 'barcode.pdf';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: 'Could not generate barcode PDF.',
                            confirmButtonColor: '#ff9f43'
                        });
                    }
                },
                error: function() {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to generate barcode PDF.',
                        confirmButtonColor: '#ff9f43'
                    });
                }
            });
        });

        // ===== View Variants Modal =====
        $(document).on('click', '.view-variants-btn', function() {
            let productId = $(this).data('id');
            let authToken = localStorage.getItem("authToken");

            Swal.fire({
                title: 'Loading Variants...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: `/api/products/${productId}/variants`,
                type: "GET",
                headers: {
                    "Authorization": "Bearer " + authToken
                },
                success: function(response) {
                    Swal.close();
                    if(response.status) {
                        let tbody = $('#variantsModalBody');
                        tbody.empty();
                        
                        let currencyPosition = localStorage.getItem("currencyPosition") || 'left';
                        let currencySymbol = localStorage.getItem("currencySymbol") || '₹';

                        if(response.data && response.data.length > 0) {
                            response.data.forEach(function(variant) {
                                let formattedPrice = parseFloat(variant.price).toLocaleString(undefined, {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                                let displayPrice = currencyPosition === 'left' ? currencySymbol + formattedPrice : formattedPrice + currencySymbol;

                                tbody.append(`
                                    <tr>
                                        <td>${variant.size}</td>
                                        <td>${variant.color}</td>
                                        <td>${variant.qty}</td>
                                        <td>${displayPrice}</td>
                                    </tr>
                                `);
                            });
                        } else {
                            tbody.append('<tr><td colspan="4" class="text-center text-muted">No variants found for this product.</td></tr>');
                        }
                        $('#variantsModal').modal('show');
                    } else {
                        Swal.fire('Error', response.message || 'Could not load variants', 'error');
                    }
                },
                error: function() {
                    Swal.close();
                    Swal.fire('Error', 'Error fetching product variants', 'error');
                }
            });
        });

        // Custom Barcode Settings Modal JS
        function updateCustomBarcodePreview() {
            let width = $('#cbWidth').val() || 0;
            let height = $('#cbHeight').val() || 0;
            let columns = $('#cbColumns').val() || 1;
            let gap = $('#cbGap').val() || 0;
            
            let shape = $('#cbShapeSelector .shape-option.active').data('shape') || 'rounded';
            let borderRadius = shape === 'square' ? '0' : (shape === 'circle' ? '50%' : '4px');
            let bgColor = $('#cbBgColorSelect').val() || '#ffffff';

            $('#previewWidthLabel').text(width + 'mm');
            $('#previewHeightLabel').text(height + 'mm');
            $('#previewGapLabel').text(gap + 'mm');

            let boxesHtml = '';
            for (let i = 0; i < columns; i++) {
                boxesHtml += `<div class="preview-box border" style="width: 70px; height: 50px; border-color: #eee !important; background: ${bgColor}; margin: 1px; border-radius: ${borderRadius};"></div>`;
            }
            $('#previewBoxesContainer').html(boxesHtml);
            
            $('#cbBgColorPreview').css('background', bgColor);
        }

        $('#cbWidth, #cbHeight, #cbColumns, #cbGap').on('input', updateCustomBarcodePreview);

        $('#cbShapeSelector .shape-option').on('click', function() {
            // Reset all
            $('#cbShapeSelector .shape-option').each(function() {
                this.style.setProperty('border-color', '#ddd', 'important');
                this.style.setProperty('border-width', '1px', 'important');
                this.style.setProperty('width', '28px');
                this.style.setProperty('height', '18px');
            });
            
            // Set active
            this.style.setProperty('border-color', '#0d6efd', 'important');
            this.style.setProperty('border-width', '2px', 'important');
            this.style.setProperty('width', '30px');
            this.style.setProperty('height', '20px');
            
            $('#cbShapeSelector .shape-option').removeClass('active');
            $(this).addClass('active');
            
            updateCustomBarcodePreview();
        });
        
        $('#cbBgColorSelect').on('change', function() {
            let color = $(this).val();
            $('#cbBgColorInput').val(color);
            updateCustomBarcodePreview();
        });
        
        $('#cbBgColorInput').on('input', function() {
            let color = $(this).val();
            if ($('#cbBgColorSelect option[value="' + color + '"]').length === 0) {
                $('#cbBgColorSelect').append(new Option(color, color));
            }
            $('#cbBgColorSelect').val(color);
            updateCustomBarcodePreview();
        });

        // Save Custom Barcode Size to Print Modal Dropdown
        $('#customBarcodeConfirmBtn').on('click', function() {
            let width = $('#cbWidth').val() || 35;
            let height = $('#cbHeight').val() || 25;
            let columns = $('#cbColumns').val() || 1;
            let gap = $('#cbGap').val() || 0;
            
            let value = `${width}x${height}x${columns}`;
            let text = `${width} x ${height} mm, ${columns} columns, gap ${gap}`;
            
            // Add to dropdown if it doesn't exist
            if ($(`#pbLabelSize option[value="${value}"]`).length === 0) {
                $('#pbLabelSize').append(new Option(text, value));
            }
            
            // Select it in the Print Modal
            $('#pbLabelSize').val(value);
            
            // Close this modal
            $('#customBarcodeSettingsModal').modal('hide');
            
            Swal.fire({
                icon: 'success',
                title: 'Custom Size Saved',
                text: 'Your custom label size has been added to the Print Barcode dropdown.',
                timer: 2000,
                showConfirmButton: false
            });
        });

        // Load custom barcode templates into Print Modal
        window.customBarcodeTemplates = {};
        function loadCustomBarcodeTemplates() {
            $.ajax({
                url: '/api/barcode-templates',
                method: 'GET',
                success: function(res) {
                    if (res.status && res.data) {
                        let select = $('#pbBarcodeTemplate');
                        // Keep the default options
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

                        // Re-apply saved default (supports custom_* templates)
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

        // Call immediately on page load
        $(document).ready(function() {
            loadCustomBarcodeTemplates();

        });

    </script>
@endpush

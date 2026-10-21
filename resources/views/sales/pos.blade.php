@extends('layout.app')

@section('title',
    request('sale_type') === 'rental'
    ? 'Add Rental'
    : (request('sale_type') === 'quotation'
    ? 'Add
    Quotation'
    : (request('sale_type') === 'advance_receipt'
    ? 'Add Advance Receipt'
    : 'Add Sale')))

@section('content')
    <style>
        .setvaluecash ul li a {
            border: 1px solid #e9ecef;
            color: #000;
            font-size: 12px;
            font-weight: 600;
            min-height: 59px;
            border-radius: 5px;
            padding: 8px 7px;
        }

        .disabled-product {
            opacity: 0.85;
        }

        .swal2-container.swal-over-pos-modal {
            z-index: 20000 !important;
        }

        .swal2-container.swal-over-pos-modal .swal2-popup {
            pointer-events: auto !important;
        }

        .swal2-container.swal-over-pos-modal .pos-rate-qty {
            pointer-events: auto !important;
            user-select: text !important;
            -webkit-user-select: text !important;
        }

        .pos-rate-pick-list {
            text-align: left;
            max-height: 360px;
            overflow: auto;
            padding: 2px 2px 0;
        }

        .pos-rate-pick-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 12px;
            border: 1px solid #e8e8e8;
            border-radius: 8px;
            margin-bottom: 8px;
            background: #fff;
        }

        .pos-rate-pick-row .pos-rate-name {
            font-weight: 600;
            font-size: 14px;
            color: #212b36;
            line-height: 1.2;
        }

        .pos-rate-pick-row .pos-rate-meta {
            font-size: 12px;
            color: #637381;
            margin-top: 2px;
        }

        .pos-rate-pick-row .pos-rate-avail {
            font-size: 12px;
            font-weight: 700;
            color: #0f766e;
            margin-top: 3px;
        }

        .pos-rate-pick-row .pos-rate-avail.is-low {
            color: #dc3545;
        }

        .pos-rate-pick-row .pos-rate-qty-wrap {
            width: 92px;
            flex-shrink: 0;
        }

        .pos-rate-pick-row .pos-rate-qty-wrap label {
            font-size: 11px;
            color: #637381;
            margin-bottom: 2px;
            display: block;
        }

        .pos-rate-pick-row .pos-rate-qty {
            text-align: center;
            font-weight: 600;
        }

        .pos-rate-pick-hint {
            font-size: 12px;
            color: #98a2b3;
            margin-top: 4px;
        }

        .barcode-scanner-layout {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .barcode-scanner-pane {
            text-align: center;
        }

        .barcode-scanner-cart {
            display: none;
        }

        @media (min-width: 768px) {
            #barcodeScannerModal .barcode-scanner-dialog {
                max-width: 960px;
            }

            #barcodeScannerModal .modal-body.barcode-scanner-body {
                padding: 18px 20px;
            }

            .barcode-scanner-layout {
                flex-direction: row;
                align-items: stretch;
                text-align: left;
            }

            .barcode-scanner-pane {
                flex: 1;
                min-width: 0;
            }

            .barcode-scanner-cart {
                display: flex;
                flex-direction: column;
                width: 340px;
                flex-shrink: 0;
                border: 1px solid #eadfcf;
                border-radius: 10px;
                background: #fffaf3;
                overflow: hidden;
            }

            .barcode-scanner-cart-header {
                padding: 10px 12px;
                font-weight: 700;
                font-size: 14px;
                color: #1b2850;
                border-bottom: 1px solid #eadfcf;
                background: #fff;
            }

            .barcode-scanner-cart-list {
                flex: 1;
                overflow-y: auto;
                max-height: 380px;
                padding: 8px;
            }

            .barcode-scanner-empty {
                color: #888;
                font-size: 13px;
                text-align: center;
                padding: 36px 12px;
            }

            .scanner-cart-row {
                display: flex;
                align-items: center;
                gap: 8px;
                background: #fff;
                border: 1px solid #eee;
                border-radius: 8px;
                padding: 8px;
                margin-bottom: 8px;
            }

            .scanner-cart-row.just-scanned {
                border-color: #22b428;
                background: #eaf8ee;
                box-shadow: 0 0 0 2px rgba(34, 180, 40, 0.22);
                animation: posScanPulse 0.7s ease-out 2;
            }

            .scanner-added-banner {
                background: #22b428;
                color: #fff;
                border-radius: 8px;
                padding: 8px 10px;
                font-size: 12px;
                font-weight: 700;
                margin-bottom: 8px;
                line-height: 1.35;
            }

            .scanner-added-banner span {
                display: block;
                font-size: 11px;
                font-weight: 600;
                opacity: 0.92;
            }

            .just-scanned-badge {
                display: inline-block;
                background: #22b428;
                color: #fff;
                font-size: 10px;
                font-weight: 700;
                padding: 2px 7px;
                border-radius: 999px;
                margin-left: 6px;
                vertical-align: middle;
                white-space: nowrap;
            }

            .scanner-cart-row img {
                width: 38px;
                height: 38px;
                object-fit: cover;
                border-radius: 6px;
                flex-shrink: 0;
            }

            .scanner-cart-info {
                flex: 1;
                min-width: 0;
            }

            .scanner-cart-info h6 {
                margin: 0;
                font-size: 13px;
                font-weight: 700;
                text-transform: capitalize;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .scanner-cart-info small {
                color: #22b428;
                font-weight: 700;
            }

            .scanner-cart-qty {
                display: flex;
                align-items: center;
                gap: 0;
                flex-shrink: 0;
            }

            .scanner-cart-qty .button-minus,
            .scanner-cart-qty .button-plus {
                width: 26px;
                height: 26px;
                border: 1px solid #ff9f43;
                background: #fff;
                color: #ff9f43;
                border-radius: 4px;
                font-weight: 700;
                line-height: 24px;
                padding: 0;
            }

            .scanner-cart-qty .quantity-field {
                width: 36px;
                height: 26px;
                text-align: center;
                border: 1px solid #ddd;
                margin: 0 3px;
                border-radius: 4px;
                font-size: 12px;
                font-weight: 700;
            }

            .scanner-cart-remove {
                border: 0;
                background: transparent;
                color: #dc3545;
                padding: 0 2px;
                font-size: 14px;
                line-height: 1;
            }

            .barcode-scanner-cart-footer {
                padding: 8px 12px;
                border-top: 1px solid #eadfcf;
                font-size: 13px;
                font-weight: 700;
                background: #fff;
            }
        }

        .barcode-scanner-mobile-bar,
        .scanner-viewfinder .scanner-frame,
        .barcode-scanner-sheet-handle,
        .barcode-scanner-cart-cols {
            display: none;
        }

        @media (max-width: 767.98px) {
            body:has(#barcodeScannerModal.show) .floating-calculator-wrapper {
                display: none !important;
            }

            #barcodeScannerModal {
                padding: 0 !important;
                z-index: 2000 !important;
            }

            #barcodeScannerModal .barcode-scanner-dialog {
                position: fixed;
                inset: 0;
                margin: 0;
                max-width: none;
                width: 100%;
                height: 100%;
                min-height: 100%;
                transform: none !important;
            }

            #barcodeScannerModal .modal-content {
                height: 100%;
                min-height: 100vh;
                border: 0;
                border-radius: 0;
                background: #1a1a1a;
            }

            #barcodeScannerModal .modal-header,
            #barcodeScannerModal .modal-footer,
            #barcodeScannerModal #scan-keep-open-hint,
            #barcodeScannerModal .barcode-scanner-cart-header,
            #barcodeScannerModal .barcode-scanner-cart-footer,
            #barcodeScannerModal .scanner-added-banner,
            #barcodeScannerModal .just-scanned-badge,
            #barcodeScannerModal .scanner-cart-row img,
            #barcodeScannerModal .scanner-cart-info small {
                display: none !important;
            }

            #barcodeScannerModal .modal-body.barcode-scanner-body {
                padding: 0;
                height: 100%;
                overflow: hidden;
            }

            #barcodeScannerModal .barcode-scanner-layout {
                position: relative;
                display: flex;
                flex-direction: column;
                height: 100%;
                gap: 0;
            }

            #barcodeScannerModal .barcode-scanner-pane {
                flex: 1 1 auto;
                min-height: 0;
                position: relative;
                background: #111;
            }

            #barcodeScannerModal .scanner-viewfinder {
                position: absolute;
                inset: 0;
                overflow: hidden;
                background: #111;
            }

            #barcodeScannerModal #qr-reader {
                display: block !important;
                width: 100% !important;
                height: 100% !important;
                max-width: none !important;
                min-height: 0 !important;
                margin: 0 !important;
                background: #111;
                border-radius: 0;
            }

            #barcodeScannerModal #qr-reader video,
            #barcodeScannerModal #qr-reader__scan_region,
            #barcodeScannerModal #qr-reader__scan_region video {
                width: 100% !important;
                height: 100% !important;
                object-fit: cover;
            }

            #barcodeScannerModal #qr-shaded-region,
            #barcodeScannerModal #qr-reader__dashboard,
            #barcodeScannerModal #qr-reader__dashboard_section,
            #barcodeScannerModal #qr-reader__header_message {
                display: none !important;
            }

            #barcodeScannerModal .scanner-viewfinder .scanner-frame {
                display: block;
                position: absolute;
                left: 12%;
                right: 12%;
                top: 34%;
                height: 26%;
                border: 2px solid rgba(255, 255, 255, 0.92);
                box-shadow: none;
                pointer-events: none;
                z-index: 3;
            }

            #barcodeScannerModal .scanner-redline {
                position: absolute;
                left: 0;
                right: 0;
                top: 50%;
                height: 2px;
                background: #e53935;
                box-shadow: 0 0 8px rgba(229, 57, 53, 0.8);
                transform: translateY(-50%);
            }

            #barcodeScannerModal .barcode-scanner-mobile-bar {
                display: block;
                position: absolute;
                inset: 0;
                z-index: 6;
                pointer-events: none;
            }

            #barcodeScannerModal .scanner-fab {
                pointer-events: auto;
                position: absolute;
                width: 48px;
                height: 48px;
                border: 0;
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.22);
            }

            #barcodeScannerModal .scanner-fab-back {
                top: calc(12px + env(safe-area-inset-top, 0px));
                left: 14px;
                background: #fff;
                color: #334155;
            }

            #barcodeScannerModal .scanner-fab-torch {
                top: calc(72px + env(safe-area-inset-top, 0px));
                left: 14px;
                background: #fff;
                color: #2563eb;
            }

            #barcodeScannerModal .scanner-fab-torch.is-on {
                background: #2563eb;
                color: #fff;
            }

            #barcodeScannerModal .scanner-fab-done {
                top: calc(12px + env(safe-area-inset-top, 0px));
                right: 14px;
                background: #2563eb;
                color: #fff;
            }

            #barcodeScannerModal #scan-message {
                display: none !important;
            }

            #barcodeScannerModal .barcode-scanner-manual {
                display: none;
            }

            #barcodeScannerModal.scanner-manual-only .barcode-scanner-manual {
                display: block;
                padding: 16px 16px 8px;
            }

            #barcodeScannerModal .barcode-scanner-cart {
                display: flex;
                flex-direction: column;
                position: relative;
                z-index: 7;
                width: 100%;
                flex: 0 0 38%;
                min-height: 220px;
                max-height: 46%;
                border: 0;
                border-radius: 18px 18px 0 0;
                background: #fff;
                overflow: hidden;
            }

            #barcodeScannerModal.is-sheet-expanded .barcode-scanner-cart {
                max-height: 78%;
            }

            #barcodeScannerModal.is-sheet-expanded .barcode-scanner-pane {
                flex: 0 0 22%;
            }

            #barcodeScannerModal .barcode-scanner-sheet-handle {
                display: flex;
                justify-content: center;
                padding: 10px 0 2px;
                cursor: pointer;
            }

            #barcodeScannerModal .barcode-scanner-sheet-handle::before {
                content: "";
                width: 42px;
                height: 4px;
                border-radius: 99px;
                background: #d0d5dd;
            }

            #barcodeScannerModal .barcode-scanner-cart-cols {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 18px 10px;
                border-bottom: 1px solid #eceff3;
                color: #98a2b3;
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 0.08em;
            }

            #barcodeScannerModal .barcode-scanner-cart-list {
                flex: 1;
                overflow-y: auto;
                max-height: none;
                padding: 4px 8px 12px;
            }

            #barcodeScannerModal .barcode-scanner-empty {
                color: #98a2b3;
                font-size: 13px;
                text-align: center;
                padding: 22px 12px;
            }

            #barcodeScannerModal .scanner-cart-row {
                display: flex;
                align-items: center;
                gap: 8px;
                background: transparent;
                border: 0;
                border-radius: 0;
                padding: 12px 10px;
                margin: 0;
            }

            #barcodeScannerModal .scanner-cart-info {
                flex: 1;
                min-width: 0;
            }

            #barcodeScannerModal .scanner-cart-info h6 {
                margin: 0;
                font-size: 14px;
                font-weight: 500;
                color: #1f2937;
                line-height: 1.35;
                text-transform: uppercase;
                white-space: normal;
                overflow: visible;
                text-overflow: unset;
            }

            #barcodeScannerModal .scanner-cart-qty {
                display: flex;
                align-items: center;
                flex-shrink: 0;
                border: 1px solid #e5e7eb;
                border-radius: 6px;
                overflow: hidden;
            }

            #barcodeScannerModal .scanner-cart-qty .button-minus,
            #barcodeScannerModal .scanner-cart-qty .button-plus {
                width: 34px;
                height: 32px;
                border: 0;
                background: #fff;
                color: #667085;
                border-radius: 0;
                font-size: 16px;
                font-weight: 600;
                line-height: 32px;
            }

            #barcodeScannerModal .scanner-cart-qty .quantity-field {
                width: 34px;
                height: 32px;
                margin: 0;
                border: 0;
                border-left: 1px solid #e5e7eb;
                border-right: 1px solid #e5e7eb;
                border-radius: 0;
                background: #fff;
                color: #2563eb;
                font-size: 15px;
                font-weight: 600;
            }

            #barcodeScannerModal .scanner-cart-remove {
                border: 0;
                background: transparent;
                color: #98a2b3;
                padding: 0 4px 0 8px;
                font-size: 22px;
                line-height: 1;
            }
        }

        .paymentmethod.active {
            /* background-color: #1b2850; */
            color: white;
            /* border-radius: 5px; */
        }

        .paymentmethod.active:hover {
            /* background-color: white; */
            color: white;
            /* Dark text for contrast */
            /* border: 1px solid #1b2850; */
            /* Optional: border to define shape */
        }

        .tabs_wrapper ul.tabs {
            display: flex;
            overflow-x: auto;
            gap: 10px;
            padding: 0;
            margin: 0;
            list-style: none;
            scrollbar-width: thin;
            width: auto;
            /* scrollbar-color: #ff9f43 #f1f1f1; */
        }

        .tabs_wrapper ul.tabs::-webkit-scrollbar {
            display: none;
        }

        .tabs_wrapper ul.tabs li {
            flex: 0 0 auto;
            cursor: pointer;
            white-space: nowrap;
            width: auto;
            /* ðŸ‘ˆ Prevent tab name from wrapping */
            padding: 0;
        }

        .product-details {
            background: #fff;
            padding: 10px 16px;
            box-shadow: none;
            border: none !important;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: 0.3s ease;
            min-width: max-content;
            /* ðŸ‘ˆ Ensure box width fits content */
        }

        .product-details h6 {
            font-size: 14px;
            color: #000;
            margin: 0;
            text-align: center;
            white-space: nowrap;
            /* ðŸ‘ˆ Prevent text wrap */
            overflow: hidden;
            text-overflow: ellipsis;
            /* ðŸ‘ˆ Optional: show ... if too long */
            max-width: 100%;
            /* ðŸ‘ˆ Avoid overflow outside parent */
        }

        .tabs_wrapper ul.tabs li.active .product-details {
            background: transparent;
            /* border-bottom: 2px solid #ff9f43 !important; */
        }

        .tabs_wrapper ul.tabs li.active .product-details h6 {
            color: #ff9f43;
        }

        .payment_panel {
            position: fixed;
            bottom: 0;
            background-color: white;
            width: 38%;
        }

        .page-wrapper .content {
            padding-bottom: 220px !important;
        }

        .body_space {
            /*margin-bottom: 2rem;*/
        }

        .paymentmethod.active {
            background-color: #1b2850;
            color: #ffffff;
        }

        .paymentmethod.active svg {
            fill: #ffffff;
        }

        .productsetimgin {
            height: 100px;
            object-fit: contain;
            margin: 1rem auto 0;
            display: block;
        }

        .productset {
            position: relative;
        }

        .pos-oos-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(255,0,0,0.85);
            color: white; 
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 12px;
            z-index: 10;
        }

        .product-edit-icon {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 26px;
            height: 26px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            background: #fff;
            border: 1px solid #ff9f43;
            color: #ff9f43;
            z-index: 5;
            text-decoration: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        }

        .product-edit-icon:hover {
            color: #fff;
            background: #ff9f43;
        }

        .productsetbtn button {
            display: none;
        }

        .search_custom_product {
            width: 100%;
        }

        .scanner-search-wrap {
            width: 100%;
        }

        .scanner-search-wrap .header-search {
            margin-bottom: 4px !important;
        }

        .mobile-scanner-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            line-height: 1.2;
            border-radius: 4px;
            border: 1px solid #d1d5db;
            padding: 3px 8px;
            background: #ffffff;
            color: #6b7280;
            min-height: 22px;
        }

        .mobile-scanner-status::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: currentColor;
            opacity: 0.8;
        }

        .mobile-scanner-status.is-connected {
            color: #166534;
            border-color: #86efac;
            background: #f0fdf4;
        }

        .mobile-scanner-status.is-disconnected {
            color: #6b7280;
            border-color: #d1d5db;
            background: #ffffff;
        }

        .mobile-scanner-status.is-checking {
            color: #475569;
            border-color: #cbd5e1;
            background: #f8fafc;
        }

        .payment_panel {
            z-index: 999;
        }

        .price {
            display: flex;
            flex-direction: column;
            width: 180px;
            font-size: 12px;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .sub-total span {
            color: #ff9f43;
        }

        .gst-inc span {
            color: #007bff;
        }

        .discount span {
            color: red;
        }

        .final-total {
            font-weight: bold;
            margin-top: 4px;
            padding-top: 3px;
            border-top: 1px solid #ddd;
        }

        .final-total span {
            color: green;
        }

        .responsive-mobile-view-1 {
            display: none !important;
        }

        .pos-back-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            border: 1px solid #ff9f43;
            background: #ff9f43;
            color: #fff;
            border-radius: 4px;
            padding: 3px 8px;
            font-size: 12px;
            font-weight: 600;
            line-height: 1;
            height: 30px;
        }

        .pos-back-btn:hover {
            background: #ff9f43;
            color: #fff;
        }

        .pos-back-btn i {
            font-size: 12px;
            line-height: 1;
        }

        .pos-top-controls {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            width: 100%;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .pos-top-controls-left {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .pos-quick-toolbar {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 16px;
        }

        .pos-action-toolbar {
            width: 100%;
            margin-bottom: 10px;
            flex-shrink: 0;
            overflow: visible;
        }

        .pos-action-toolbar-row {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            min-height: 44px;
            padding: 6px 10px;
            background: #fff;
            border: 1px solid #e8e8e8;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
            flex-wrap: wrap;
            overflow: visible;
        }

        .pos-action-toolbar-row::-webkit-scrollbar {
            height: 4px;
        }

        .pos-toolbar-group {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .pos-toolbar-divider {
            width: 1px;
            height: 26px;
            background: #e5e7eb;
            flex-shrink: 0;
        }

        .pos-toolbar-order-details {
            flex: 1 1 auto;
            min-width: 0;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 8px;
            margin-left: auto;
        }

        .pos-toolbar-order-field {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
        }

        .pos-toolbar-order-field label {
            font-size: 11px;
            font-weight: 600;
            color: #6b7280;
            margin: 0;
            white-space: nowrap;
            line-height: 1.2;
        }

        .pos-toolbar-order-details .form-control,
        .pos-toolbar-order-details select {
            height: 32px;
            min-height: 32px;
            font-size: 12px;
            padding: 0 8px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .pos-toolbar-order-details .form-control:focus,
        .pos-toolbar-order-details select:focus {
            background: #fff;
            border-color: #ff9f43;
            box-shadow: 0 0 0 3px rgba(255, 159, 67, 0.12);
            outline: none;
        }

        .pos-toolbar-order-field--date {
            flex: 1 1 170px;
            max-width: 205px;
        }

        .pos-toolbar-order-field--staff {
            flex: 1 1 150px;
            max-width: 185px;
        }

        .pos-toolbar-order-field--type {
            flex: 0 1 130px;
            max-width: 150px;
        }

        .pos-toolbar-order-details .select2-container .select2-selection--single {
            height: 32px !important;
            min-height: 32px;
            border-radius: 6px;
            border-color: #e5e7eb;
            background: #f9fafb;
        }

        .pos-toolbar-order-details .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 30px !important;
            font-size: 12px;
            padding-left: 8px;
        }

        .pos-toolbar-order-details .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 30px !important;
        }

        .pos-toolbar-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin: 0;
            padding: 5px 10px;
            border: 1px solid #e5e7eb;
            border-radius: 999px;
            background: #f9fafb;
            color: #374151;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            cursor: pointer;
            user-select: none;
            transition: background 0.15s, border-color 0.15s, color 0.15s;
            flex-shrink: 0;
        }

        .pos-toolbar-chip:hover {
            border-color: #ff9f43;
            background: #fff8f0;
        }

        .pos-toolbar-chip input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
            pointer-events: none;
        }

        .pos-toolbar-chip:has(input:checked) {
            background: #fff3e6;
            border-color: #ff9f43;
            color: #c45d00;
            box-shadow: inset 0 0 0 1px rgba(255, 159, 67, 0.25);
        }

        .pos-gst-segment {
            display: inline-flex;
            align-items: center;
            padding: 2px;
            border-radius: 999px;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            gap: 2px;
            flex-shrink: 0;
        }

        .pos-gst-segment-btn {
            display: inline-flex;
            align-items: center;
            margin: 0;
            cursor: pointer;
        }

        .pos-gst-segment-btn input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
            pointer-events: none;
        }

        .pos-gst-segment-btn span {
            display: inline-flex;
            align-items: center;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            white-space: nowrap;
            transition: background 0.15s, color 0.15s;
        }

        .pos-gst-segment-btn input:checked + span {
            background: #fff;
            color: #1b2850;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08);
        }

        .pos-toolbar-search {
            flex: 1 1 auto;
            min-width: 0;
            overflow: visible;
        }

        .pos-toolbar-end {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1 1 220px;
            min-width: 180px;
            margin-left: auto;
        }

        .pos-action-toolbar .pos-back-btn {
            flex-shrink: 0;
            height: 36px;
            padding: 0 12px;
            border-radius: 8px;
        }

        .pos-toolbar-search .header-search {
            margin-bottom: 0 !important;
            display: flex;
            align-items: center;
            gap: 6px;
            overflow: visible;
        }

        .pos-scan-btn {
            flex: 0 0 36px;
            width: 36px;
            height: 36px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            color: #374151;
        }

        .pos-scan-btn:hover {
            background: #fff3e6;
            border-color: #ff9f43;
            color: #c45d00;
        }

        .pos-toolbar-search-field {
            position: relative;
            flex: 1 1 auto;
            min-width: 0;
            overflow: visible;
        }

        .pos-toolbar-search-field .pos-search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            opacity: 0.55;
            pointer-events: none;
            z-index: 2;
        }

        .pos-toolbar-search-field input {
            width: 100%;
            height: 36px;
            padding: 0 10px 0 32px;
            font-size: 13px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #f9fafb;
        }

        .pos-toolbar-search-field input:focus {
            background: #fff;
            border-color: #ff9f43;
            box-shadow: 0 0 0 3px rgba(255, 159, 67, 0.15);
            outline: none;
        }

        .pos-toolbar-search-field #searchResults1 {
            top: calc(100% + 4px);
            left: 0;
            width: 100%;
            max-height: 320px;
        }

        .pos-bill-items .pos-bill-search-wrap {
            flex: 1 1 auto;
            min-width: 0;
            margin-bottom: 0;
        }

        .pos-bill-items .pos-bill-search-wrap .header-search {
            width: 100%;
        }

        .pos-bill-items .pos-toolbar-search-field input {
            height: 36px;
        }

        .totalitem {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: nowrap;
            margin-bottom: 10px;
            padding: 2px 0;
        }

        .totalitem h4 {
            flex: 0 0 auto;
            margin: 0 2px 0 0;
            white-space: nowrap;
            font-size: 14px;
            font-weight: 600;
            padding: 6px 12px;
        }

        .totalitem .pos-bill-clear-wrap {
            flex: 0 0 auto;
            white-space: nowrap;
        }

        .totalitem .pos-bill-search-wrap .pos-scan-btn {
            width: 34px;
            height: 34px;
            flex: 0 0 34px;
        }

        #searchResults1.pos-search-dropdown-floating,
        .pos-bill-search-results.pos-search-dropdown-floating {
            position: fixed !important;
            z-index: 10060 !important;
            max-height: min(320px, 50vh) !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 10px !important;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.18) !important;
            padding: 4px !important;
            background: #fff !important;
            display: block !important;
            visibility: visible !important;
            pointer-events: auto !important;
        }

        #pos-right-panel .pos-bill-search-results.pos-search-dropdown-floating {
            position: fixed !important;
            top: auto;
            left: auto;
            right: auto;
            bottom: auto;
            max-height: min(320px, 50vh) !important;
            overflow-y: auto !important;
            z-index: 10060 !important;
        }

        #searchResults1,
        .pos-bill-search-results {
            padding: 4px;
            border-radius: 10px;
        }

        .pos-bill-tabs-bar {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 8px;
            width: auto;
            max-width: 100%;
            margin-bottom: 10px;
            min-height: 28px;
        }
        #posBillTabsBar #posBillTabs.pos-bill-tabs {
            display: inline-flex !important;
            align-items: center;
            gap: 6px;
            overflow-x: auto;
            flex: 0 0 auto !important;
            width: max-content !important;
            max-width: calc(100% - 40px);
            min-width: 0;
            scrollbar-width: thin;
        }
        .pos-bill-tabs::-webkit-scrollbar {
            height: 4px;
        }
        #posBillTabsBar .pos-bill-tab {
            display: inline-flex !important;
            align-items: center;
            justify-content: flex-start;
            gap: 4px;
            flex: 0 0 auto !important;
            width: max-content !important;
            max-width: 160px !important;
            height: 28px !important;
            min-height: 28px !important;
            min-width: 0 !important;
            border: 0;
            background: #3a3f4b;
            color: #fff;
            border-radius: 999px;
            padding: 0 6px 0 10px !important;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            line-height: 1;
            white-space: nowrap;
            box-sizing: border-box;
            overflow: hidden;
        }
        #posBillTabsBar .pos-bill-tab.active {
            background: #ff9f43;
            color: #fff;
        }
        .pos-bill-tab-name {
            flex: 0 1 auto;
            max-width: 90px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            text-align: left;
        }
        .pos-bill-tab-count { 
            display: none;
            min-width: 14px;
            height: 14px;
            border-radius: 50%;
            background: rgba(255,255,255,0.22);
            font-size: 9px;
            align-items: center;
            justify-content: center;
            padding: 0 3px;
            line-height: 1;
        }
        .pos-bill-tab.has-items .pos-bill-tab-count {
            display: inline-flex;
        }
        #posBillTabsBar .pos-bill-tab-close {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            flex: 0 0 14px !important;
            width: 14px !important;
            height: 14px !important;
            min-width: 14px !important;
            min-height: 14px !important;
            max-width: 14px !important;
            max-height: 14px !important;
            border-radius: 50%;
            border: 0;
            background: rgba(255,255,255,0.22);
            color: #fff;
            font-size: 9px !important;
            font-weight: 700;
            line-height: 1 !important;
            padding: 0 !important;
            margin: 0 !important;
            overflow: hidden;
            box-sizing: border-box;
            cursor: pointer;
        }
        #posBillTabsBar .pos-bill-tab-close:hover {
            background: rgba(0,0,0,0.28);
        }
        .pos-leave-draft-intro {
            font-size: 14px;
            color: #637381;
            margin: 0 0 4px;
            text-align: left;
        }
        .pos-leave-draft-list {
            text-align: left;
            max-height: 240px;
            overflow-y: auto;
            margin-top: 10px;
        }
        .pos-leave-draft-list label {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border: 1px solid #ececec;
            border-radius: 8px;
            margin-bottom: 8px;
            cursor: pointer;
            font-size: 14px;
            color: #333;
        }
        .pos-leave-draft-list label:last-child {
            margin-bottom: 0;
        }
        .pos-leave-draft-list input[type="checkbox"] {
            width: 16px;
            height: 16px;
            flex: 0 0 auto;
        }
        .pos-leave-draft-name {
            flex: 1;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .pos-add-bill-tab {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 0;
            background: #1b2850;
            color: #fff;
            font-size: 16px;
            line-height: 1;
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .pos-add-bill-tab:hover {
            background: #ff9f43;
        }

        .pos-quick-toolbar .pos-top-controls {
            margin-bottom: 0;
        }

        .pos-quick-toolbar .scanner-search-wrap {
            margin-right: 0 !important;
            margin-bottom: 0 !important;
        }

        .pos-quick-toolbar .scanner-search-wrap .header-search {
            width: 100%;
        }

        .pos-quick-toolbar .scanner-search-wrap .header-search input {
            width: 100%;
        }

        .pos-quick-toolbar .pos-top-controls-left {
            width: 100%;
        }

        .pos-gst-options {
            display: inline-flex;
            align-items: center;
            gap: 14px;
        }

        .pos-search-rates {
            font-size: 11px;
            color: #4b5563;
            line-height: 1.3;
            margin-top: 2px;
        }

        .pos-search-dropdown {
            padding: 4px;
            border-radius: 10px;
            background: #fff;
        }

        .pos-search-dropdown-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 6px 8px 4px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #9ca3af;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 2px;
        }

        .pos-search-dropdown-head .btn-close {
            width: 22px;
            height: 22px;
            padding: 0;
            opacity: 0.55;
        }

        .pos-search-item {
            display: grid;
            grid-template-columns: 34px minmax(0, 1fr) auto;
            grid-template-areas:
                "thumb body price"
                "thumb meta price";
            align-items: center;
            gap: 2px 10px;
            width: 100%;
            margin: 0 0 2px;
            padding: 8px 10px;
            border: 0;
            border-radius: 8px;
            background: #fff;
            text-align: left;
            cursor: pointer;
            transition: background 0.12s ease, box-shadow 0.12s ease;
        }

        .pos-search-item:last-child {
            margin-bottom: 0;
        }

        .pos-search-item:hover,
        .pos-search-item.is-active {
            background: #fff8f0;
            box-shadow: inset 0 0 0 1px rgba(255, 159, 67, 0.35);
        }

        .pos-search-item.is-oos {
            opacity: 0.72;
        }

        .pos-search-item-thumb {
            grid-area: thumb;
            width: 34px;
            height: 34px;
            border-radius: 6px;
            overflow: hidden;
            background: #f3f4f6;
            flex-shrink: 0;
        }

        .pos-search-item-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .pos-search-item-body {
            grid-area: body;
            min-width: 0;
        }

        .pos-search-item-name {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #1f2937;
            line-height: 1.25;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pos-search-item-meta {
            grid-area: meta;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
            min-width: 0;
            font-size: 11px;
            color: #6b7280;
            line-height: 1.2;
        }

        .pos-search-item-code {
            font-family: ui-monospace, Consolas, monospace;
            color: #4b5563;
        }

        .pos-search-item-badge {
            display: inline-flex;
            align-items: center;
            padding: 1px 6px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            line-height: 1.3;
            white-space: nowrap;
        }

        .pos-search-item-badge.is-gst {
            background: #ecfdf5;
            color: #047857;
        }

        .pos-search-item-badge.is-no-gst {
            background: #f3f4f6;
            color: #6b7280;
        }

        .pos-search-item-badge.is-oos {
            background: #fef2f2;
            color: #b91c1c;
        }

        .pos-search-item-badge.is-stock {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .pos-search-item-price {
            grid-area: price;
            justify-self: end;
            align-self: start;
            font-size: 13px;
            font-weight: 700;
            color: #1b2850;
            white-space: nowrap;
            line-height: 1.2;
        }

        .pos-search-item-mrp {
            display: block;
            font-size: 10px;
            font-weight: 500;
            color: #9ca3af;
            text-decoration: line-through;
            text-align: right;
        }

        .pos-search-empty {
            padding: 14px 12px;
            text-align: center;
            font-size: 13px;
            color: #6b7280;
        }

        .pos-search-create-product {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            border: 0;
            border-top: 1px solid #eee;
            background: #fff8f1;
            color: #ff9f43;
            font-size: 13px;
            font-weight: 600;
            padding: 10px 12px;
            cursor: pointer;
        }
        .pos-search-create-product:hover {
            background: #ff9f43;
            color: #fff;
        }
        .pos-create-product-btn {
            flex: 0 0 auto;
            white-space: nowrap;
            background: #ff9f43 !important;
            border-color: #ff9f43 !important;
            color: #fff !important;
            font-weight: 600;
            font-size: 12px;
            border-radius: 6px;
            padding: 5px 10px;
        }
        .pos-bill-search-wrap {
            display: flex;
            align-items: center;
            gap: 6px;
            width: 100%;
            position: relative;
        }
        .pos-bill-search-wrap .pos-bill-search-input {
            flex: 1 1 auto;
            min-width: 0;
        }

        .pos-search-barcode {
            font-size: 11px;
            font-family: ui-monospace, Consolas, monospace;
            color: #1f2937;
            margin-top: 2px;
        }

        .pos-search-barcode-more {
            font-size: 11px;
            color: #ff9f43;
            font-weight: 700;
        }

        .pos-gst-options .custom-radio-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin: 0;
            white-space: nowrap;
            font-size: 14px;
        }

        .pos-quotation-toggle {
            margin: 0;
            padding-left: 0;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .pos-quotation-toggle .form-check-input {
            margin: 0;
            margin-left: 0;
            float: none;
        }

        .pos-quotation-toggle .form-check-label {
            margin: 0;
            font-weight: 500;
        }

        .card .card-body {
            padding: 20px;
        }

        .tabs_container {
            max-height: calc(260vh - 170px);
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 5px;
        }

        .tabs_container::-webkit-scrollbar {
            width: 6px;
        }

        .tabs_container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .tabs_container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .tabs_container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .tabs_container.card .card-body {
            padding-bottom: 180px;
        }

        .card-order .card-body {
            padding-bottom: 20px;
        }

        /* ── Payment Panel Drag Handle ── */
        .payment-panel-handle {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 6px 12px 4px;
            cursor: grab;
            background: linear-gradient(to bottom, #f0f2f5, #e8eaf0);
            border-radius: 12px 12px 0 0;
            border-bottom: 1px solid #d8dce6;
            user-select: none;
            position: relative;
        }

        .payment-panel-handle:active {
            cursor: grabbing;
        }

        .handle-pill {
            width: 40px;
            height: 4px;
            background: #b0b8cc;
            border-radius: 99px;
            flex-shrink: 0;
        }

        .handle-hint {
            font-size: 10px;
            color: #8892a4;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        .panel-min-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: #e2e6ed;
            border: none;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #555;
            transition: background 0.2s;
            padding: 0;
        }

        .panel-min-btn:hover {
            background: #cdd3dd;
        }

        #paymentPanelBody {
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        #paymentPanelBody.panel-collapsed {
            max-height: 0 !important;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background: #ff9f43;
            color: #fff !important;
        }

        @media screen and (max-width: 992px) {
            .pos-top-controls {
                align-items: flex-start;
            }
        }

        @media screen and (min-width: 768px) and (max-width: 992px) {
            .tabs_container .row {
                --bs-gutter-x: 12px;
                --bs-gutter-y: 12px;
            }

            .tabs_container .row>.col-lg-3.d-flex.position-relative {
                padding-left: calc(var(--bs-gutter-x) * 0.5);
                padding-right: calc(var(--bs-gutter-x) * 0.5);
            }

            .tabs_container .productset {
                width: 100%;
                min-height: 100%;
            }

            .payment_panel {
                position: fixed;
                left: 16px;
                right: 16px;
                bottom: 60px;
                width: auto;
                margin-left: 0;
                border-radius: 14px 14px 0 0;
                background-color: white;
                overflow: hidden;
                box-sizing: border-box;
            }

            .card-order .card-body {
                padding-bottom: 20px;
            }
        }

        @media screen and (min-width: 993px) and (max-width: 1199px) {
            .tabs_container .row>.col-lg-3.d-flex.position-relative {
                flex: 0 0 33.333333%;
                max-width: 33.333333%;
            }

            .tabs_container .productset {
                min-height: 100%;
            }

            .payment_panel {
                position: fixed;
                right: 16px;
                left: auto;
                bottom: 0;
                width: min(420px, calc(100vw - 32px));
                margin-left: 0;
                border-radius: 14px 14px 0 0;
                background-color: white;
                overflow: visible;
                box-sizing: border-box;
                z-index: 999;
            }

            .payment_panel .setvaluecash {
                display: block !important;
                margin: 0 0 12px;
            }

            .payment_panel .setvaluecash ul {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin: 0;
                padding: 0;
            }

            .payment_panel .setvaluecash ul li {
                width: calc(33.33% - 8px);
                margin: 0;
                list-style: none;
            }

            .payment_panel .setvaluecash ul li a {
                min-height: 38px;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 4px;
                padding: 4px 8px;
                text-align: center;
                font-size: 11px;
            }

            .payment_panel .setvaluecash ul li a i {
                font-size: 14px !important;
                margin-bottom: 0 !important;
            }

            .payment_panel .btn-totallabel {
                margin: 0;
            }

            .card-order .card-body {
                padding-bottom: 20px;
            }
        }

        @media screen and (device-width: 1024px) and (device-height: 1366px) and (orientation: portrait) {
            .tabs_container .row {
                --bs-gutter-x: 12px;
                --bs-gutter-y: 12px;
            }

            .tabs_container .row>.col-lg-3.d-flex.position-relative {
                flex: 0 0 50%;
                max-width: 50%;
            }

            /* iPad Pro portrait: payment panel sits at the bottom of the right column,
                       not floating over the totals */
            .payment_panel {
                position: static !important;
                width: 100% !important;
                bottom: auto !important;
                right: auto !important;
                left: auto !important;
                border-radius: 0 !important;
                margin-top: 12px;
            }

            .card-order .card-body {
                padding-bottom: 20px !important;
            }
        }

        /* iPad Pro landscape (1366x1024) */
        @media screen and (device-width: 1366px) and (device-height: 1024px) and (orientation: landscape) {
            .payment_panel {
                position: static !important;
                width: 100% !important;
                bottom: auto !important;
                right: auto !important;
                left: auto !important;
                border-radius: 0 !important;
                margin-top: 12px;
            }

            .card-order .card-body {
                padding-bottom: 20px !important;
            }
        }

        /* iPad Pro viewport-based fix (1024px wide, portrait) — covers modern browsers
                   where device-width queries may not fire */
        @media screen and (min-width: 1024px) and (max-width: 1024px) {
            .payment_panel {
                position: static !important;
                width: 100% !important;
                bottom: auto !important;
                right: auto !important;
                left: auto !important;
                border-radius: 8px !important;
                margin-top: 12px;
                box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.06);
            }

            .card-order .card-body {
                padding-bottom: 20px !important;
            }
        }

        @media screen and (max-width: 767px) {
            .tabs_container {
                max-height: 360px !important;
                overflow-y: auto !important;
                overflow-x: hidden !important;
            }

            .page-wrapper .content {
                padding-bottom: 260px !important;
            }

            .payment_panel {
                position: fixed;
                left: 0;
                right: 0;
                bottom: 60px;
                background-color: #fff;
                width: auto;
                margin-left: 0;
                border-radius: 16px 16px 0 0;
                box-shadow: 0 -8px 28px rgba(15, 23, 42, 0.14);
                z-index: 1035;
                overflow: hidden;
            }

            .gst-info {
                margin: 4px 0 !important;
                padding: 3px 8px !important;
                background: #f8f9fa !important;
                border-radius: 4px !important;
                border-left: 3px solid #4caf50 !important;
                width: 80% !important;
            }

            .product-lists .gst-no-message {
                margin: 4px 0 !important;
                padding: 6px 8px !important;
                background: #fff8e1 !important;
                border-left: 3px solid #ff9f43 !important;
                border-radius: 4px !important;
                color: #640a06 !important;
                font-size: 12px !important;
                line-height: 1.4 !important;
                width: 80% !important;
            }

            .pos-action-toolbar-row {
                flex-wrap: wrap;
                overflow-x: visible;
            }

            .pos-toolbar-order-details {
                flex: 1 1 100%;
                justify-content: flex-start;
                margin-left: 0;
            }

            .pos-toolbar-order-field--date,
            .pos-toolbar-order-field--staff,
            .pos-toolbar-order-field--type {
                flex: 1 1 calc(50% - 4px);
                max-width: none;
            }

            .pos-toolbar-end {
                flex: 1 1 100%;
                min-width: 0;
                margin-left: 0;
                order: 10;
            }

            .pos-toolbar-search {
                flex: 1 1 auto;
                min-width: 0;
                margin-left: 0;
            }

            .responsive-mobile-view {
                display: none !important;
            }

            .responsive-mobile-view-1 {
                display: block !important;
            }



            .responsive-mobile-view-1 .form-check-input {
                margin-top: 0;
                accent-color: #ff9f43;
            }

            .pos-quick-toolbar {
                display: flex !important;
            }

            .customer-section-title {
                display: flex !important;
                flex-wrap: wrap;
                align-items: center;
                gap: 6px 8px;
                padding: 0 !important;
                margin-bottom: 4px !important;
                color: black !important;
            }

            .customer-section-title>div:first-child {
                order: 1;
                flex: 1 1 auto;
                min-width: 0;
                font-weight: 700;
                color: black !important;
            }

            .customer-section-title .customer-action-btns {
                order: 2;
                margin-left: auto;
            }

            .customer-section-title .customer-action-btns .btn {
                width: 30px !important;
                height: 30px !important;
                padding: 0;
                background: rgba(255, 255, 255, 0.06) !important;
                border-color: rgba(255, 255, 255, 0.18) !important;
            }

            .customer-section-title .customer-action-btns .btn i {
                color: #ff9f43 !important;
            }

            .customer-section-title>.d-flex.align-items-center.gap-3.ms-auto {
                order: 3;
                width: 100%;
                margin-left: 0 !important;
                padding-top: 10px;
                border-top: 1px dashed rgba(255, 255, 255, 0.32);
                justify-content: space-between !important;
                align-items: center;
                gap: 10px !important;
            }

            .customer-section-title .pos-fullscreen-btn {
                width: 30px;
                height: 30px;
                min-width: 30px;
                padding: 0;
                border: 1px solid #d1d5db;
                background: #fff;
                color: #4b5563
            }

            .customer-section-title .pos-order-number-badge {
                margin-left: auto;
                margin-right: 0;
                background: #fff(255, 255, 255, 0.06);
                border: 1.5px solid #e0e0e0;
                color: black;
                box-shadow: none;
            }

            .customer-section-title .pos-order-number-label,
            .customer-section-title .pos-order-number-value {
                color: black;
            }

            .customer-section-title .page-btn {
                margin-left: 0;
            }

            .customer-section-title .pos-back-btn {
                min-width: 74px;
                height: 30px;
                border-radius: 999px;
                justify-content: center;
                font-size: 12px;
            }

            #openEditCustomerBtn {
                display: none !important;
            }


            .productset {
                display: flex;
            }

            .productset .productsetimg {

                width: 30%;
            }

            .productsetimgin {
                object-fit: cover;
                height: 90px;
                margin: 0;
                max-width: 110px;
            }

            .productsetcontent {
                text-align: left !important;
            }



            .productsetbtn {
                position: absolute;
                right: 10px;
                top: 43px;
            }

            .productsetbtn button {
                display: block;
                width: 100%;
                height: 33px;
                background-color: #1b2850;
                color: white !important;
                border: none;
                border-radius: 5px;
            }

            .productsetbtn button:active {
                color: white;
            }

            .product-lists {
                display: grid;
                grid-template-columns: minmax(0, 1fr) auto;
                grid-template-areas:
                    "details delete"
                    "qty qty"
                    "discount discount"
                    "price price";
                align-items: flex-start;
                position: relative;
                overflow: hidden;
                gap: 10px;
                padding: 10px 42px 10px 0;
            }

            .product-lists>li.cart-col-item {
                grid-area: details;
                min-width: 0;
            }

            .product-lists>li.cart-col-qty {
                grid-area: qty;
                width: 100%;
            }

            .product-lists>li.cart-col-disc {
                grid-area: discount;
                width: 100%;
            }

            .product-lists>li.price {
                grid-area: price;
                width: 100%;
                margin-top: 0;
                padding: 8px 10px;
                border-radius: 10px;
                background: #f8f9fa;
            }

            .product-lists>li.delete-col {
                grid-area: delete;
                width: 32px;
                margin-left: 0;
                text-align: right;
                flex-shrink: 0;
                position: absolute;
                top: 8px;
                right: 6px;
                z-index: 2;
            }

            .product-lists>li.cart-col-sno {
                position: absolute;
                left: 8px;
                top: 12px;
                width: 22px;
                text-align: center;
                font-size: 12px;
                font-weight: 700;
                color: #6b7280;
            }

            .product-lists {
                padding-left: 28px;
            }

            .product-lists.pos-bill-search-row {
                display: block;
                padding: 8px 10px;
                overflow: visible;
            }

            .pos-bill-search-row .cart-col-sno,
            .pos-bill-search-row .cart-col-qty,
            .pos-bill-search-row .cart-col-disc,
            .pos-bill-search-row .cart-col-amt,
            .pos-bill-search-row .delete-col {
                display: none !important;
            }

            .pos-bill-search-row .pos-bill-search-cell {
                width: 100%;
            }

            .pos-bill-search-wrap {
                position: relative;
                width: 100%;
            }

            .pos-bill-search-input {
                height: 38px;
                font-size: 14px;
            }

            .pos-bill-search-results {
                position: absolute;
                top: calc(100% + 2px);
                left: 0;
                right: 0;
                z-index: 1060;
                max-height: 280px;
                overflow-y: auto;
                display: none;
                border-radius: 6px;
            }

            .productimg {
                display: flex;
                align-items: flex-start;
                gap: 5px;
            }

            .productimgs {
                flex: 0 0 72px;
            }

            .productimgs img {
                width: 72px;
                height: 72px;
                object-fit: cover;
                border-radius: 10px;
            }

            .productcontet {
                flex: 1 1 auto;
                min-width: 0;
            }

            .productcontet h4 {
                max-width: none;
                margin-bottom: 8px;
            }

            .increment-decrement {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 8px;
            }

            .increment-decrement .input-groups {
                width: 60%;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
            }

            .increment-decrement .input-groups input[type=button] {
                width: 75px;
                height: 25px;
            }

            .increment-decrement .quantity-field {
                flex: 1 1 auto;
                width: 100% !important;
                min-width: 0;
            }

            .increment-decrement>div:last-child {
                order: 0;
                margin: 0 !important;
                width: 100%;
                padding-left: 40px;
                padding-right: 46px;
            }

            .increment-decrement .product-price-input {
                width: 100%;
            }

            .price {
                width: 100%;
            }

            .price-row {
                margin-bottom: 4px;
            }

            .product-table {
                max-height: none;
                overflow: visible;
                -ms-overflow-style: none;
                scrollbar-width: none;
                padding-bottom: 8px;
            }

            .product-table::-webkit-scrollbar {
                display: none;
            }

            /* .body_space_two {
                                        margin-bottom: 4rem;
                                    } */

            a.confirm-text.remove-item {
                /* font-size: 116px; */
                margin: 6px;
            }


            .mobile-top-options {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                background: #ffffff;
                padding: 10px 15px;
                z-index: 9999;
                border-bottom: 1px solid #eee;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            /* Give space below so content not hide */
            .card-order .card-body {
                margin-top: 10px;
                padding: 12px;
            }

            /* Make radio inline properly */
            .mobile-top-options label {
                font-size: 13px;
                font-weight: 600;
                margin-right: 10px;
            }

            .mobile-top-options input[type="radio"],
            .mobile-top-options input[type="checkbox"] {
                margin-right: 4px;
            }

            .totalitem {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                flex-wrap: wrap;
                margin-bottom: 10px;
            }

            .product-discount-box {
                width: 100%;
                gap: 10px;
                margin-top: 0;
            }

            .discount-field {
                flex: 1 1 0;
            }

            .product-discount-percentage,
            .product-discount-amount {
                width: 100%;
                min-width: 145px;
            }

            .price-row {
                gap: 10px;
            }

            .price-row span:last-child {
                text-align: right;
            }

            .setvalue {
                padding: 0 0 16px 0 !important;
            }

            .setvalue ul li {
                padding: 8px 0;
            }

            .setvaluecash ul {
                display: flex;
                flex-wrap: nowrap;
                gap: 4px;
                margin: 0;
                padding: 0 10px 10px;
                overflow-x: auto;
            }

            .setvaluecash ul::-webkit-scrollbar {
                display: none;
            }

            .setvaluecash ul li {
                margin: 0;
                flex: 1;
                width: auto !important;
            }

            .setvaluecash ul li a {
                min-height: 40px;
                height: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 2px;
                text-align: center;
                padding: 4px 6px;
                font-size: 10px;
            }

            .setvaluecash ul li a i {
                font-size: 14px !important;
                margin-bottom: 0 !important;
            }

            #posPaidTypeBox,
            #cashOnlineBox,
            #bankSelectionBox {
                padding: 0 12px 12px;
            }

            .btn-totallabel {
                margin: 0 8px 8px;
            }

        }

        @media screen and (max-width: 991.98px) {
            body:has(.pos-mall-layout) {
                overflow-x: hidden;
            }

            .page-wrapper .content {
                padding: 8px 10px 12px !important;
                padding-bottom: calc(250px + env(safe-area-inset-bottom, 0px)) !important;
                overflow-x: hidden;
            }

            .pos-mall-layout,
            #pos-right-panel,
            .card-order,
            .card-order .card-body,
            .pos-bill-scroll {
                overflow-x: hidden;
                max-width: 100%;
            }

            .pos-bill-tabs-bar {
                margin-bottom: 8px;
            }

            #posBillTabsBar .pos-bill-tab {
                height: 32px !important;
                min-height: 32px !important;
                max-width: 130px !important;
            }

            .pos-add-bill-tab {
                width: 32px;
                height: 32px;
            }

            .pos-action-toolbar {
                margin-bottom: 8px;
            }

            .pos-action-toolbar-row {
                flex-wrap: wrap;
                align-items: center;
                gap: 8px;
                padding: 8px 10px;
                min-height: 0;
            }

            .pos-toolbar-modes {
                flex: 1 1 100%;
                display: flex;
                flex-wrap: nowrap;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
                gap: 6px;
                max-width: 100%;
                padding-bottom: 2px;
            }

            .pos-toolbar-modes::-webkit-scrollbar {
                display: none;
            }

            .pos-toolbar-divider {
                display: none !important;
            }

            .pos-toolbar-gst,
            .pos-action-toolbar-row > label.pos-toolbar-chip {
                flex-shrink: 0;
            }

            .pos-toolbar-chip {
                font-size: 11px;
                padding: 5px 9px;
            }

            .pos-gst-segment-btn span {
                padding: 5px 10px;
                font-size: 11px;
            }

            .pos-toolbar-end {
                flex: 1 1 100%;
                width: 100%;
                min-width: 0;
                margin-left: 0;
                order: 10;
            }

            .pos-toolbar-search-field input {
                height: 40px;
                font-size: 14px;
            }

            .pos-scan-btn {
                width: 40px;
                height: 40px;
                flex: 0 0 40px;
            }

            .payment_panel {
                position: fixed !important;
                left: 0 !important;
                right: 0 !important;
                bottom: 60px !important;
                top: auto !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                border-radius: 16px 16px 0 0;
                background: #fff !important;
                box-shadow: 0 -8px 28px rgba(15, 23, 42, 0.14);
                z-index: 1035;
                box-sizing: border-box;
            }

            .payment-panel-handle {
                display: flex !important;
            }

            .handle-hint {
                display: none;
            }

            #pos-right-panel .card-order .card-body {
                padding: 10px 10px 12px;
            }

            .customer-section-title {
                font-size: 13px !important;
                margin-bottom: 4px !important;
                padding: 0 !important;
            }

            .customer-details-box {
                padding: 6px 8px 4px !important;
                margin-bottom: 6px !important;
            }

            .customer-details-box label,
            .pos-order-row label {
                font-size: 11px;
                margin-bottom: 2px !important;
            }

            .customer-details-box .form-control,
            .pos-order-row .form-control,
            .pos-order-row select {
                height: 32px !important;
                min-height: 32px;
                font-size: 13px;
            }

            .customer-details-box .select2-container .select2-selection--single,
            .pos-order-row .select2-container .select2-selection--single {
                height: 32px !important;
            }

            .customer-details-box .select2-container--default .select2-selection--single .select2-selection__rendered,
            .pos-order-row .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 30px !important;
                font-size: 13px;
            }

            .customer-details-box .select2-container--default .select2-selection--single .select2-selection__arrow,
            .pos-order-row .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 30px !important;
            }

            #pos-right-panel .pos-customer-row,
            #pos-right-panel .pos-order-row {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
                margin: 0 0 4px !important;
            }

            #pos-right-panel .pos-customer-row > .pos-customer-field,
            #pos-right-panel .pos-order-row > [class*="col-"] {
                flex: 1 1 100%;
                max-width: 100%;
                width: 100%;
                padding: 0;
            }

            .pos-bill-head {
                display: none !important;
            }

            .product-table {
                max-height: none;
                overflow: visible;
            }

            .product-lists {
                display: grid;
                grid-template-columns: minmax(0, 1fr) auto;
                grid-template-areas:
                    "details delete"
                    "qty qty"
                    "discount discount"
                    "price price";
                align-items: flex-start;
                position: relative;
                gap: 8px;
                padding: 12px 40px 12px 30px;
                margin: 0 0 8px;
                border: 1px solid #ececec;
                border-radius: 10px;
                background: #fff;
            }

            .product-lists>li.cart-col-item {
                grid-area: details;
                min-width: 0;
            }

            .product-lists>li.cart-col-qty {
                grid-area: qty;
                width: 100%;
            }

            .product-lists>li.cart-col-disc {
                grid-area: discount;
                width: 100%;
            }

            .product-lists>li.price {
                grid-area: price;
                width: 100%;
                margin-top: 0;
                padding: 8px 10px;
                border-radius: 8px;
                background: #f8f9fa;
            }

            .product-lists>li.delete-col {
                grid-area: delete;
                position: absolute;
                top: 8px;
                right: 6px;
                width: 32px;
                height: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .product-lists>li.cart-col-sno {
                position: absolute;
                left: 8px;
                top: 14px;
                width: 20px;
                text-align: center;
                font-size: 12px;
                font-weight: 700;
                color: #6b7280;
            }

            .product-lists.pos-bill-search-row {
                display: block;
                padding: 8px 10px;
                overflow: visible;
            }

            .pos-bill-search-row .cart-col-sno,
            .pos-bill-search-row .cart-col-qty,
            .pos-bill-search-row .cart-col-disc,
            .pos-bill-search-row .cart-col-amt,
            .pos-bill-search-row .delete-col {
                display: none !important;
            }

            .pos-bill-search-input {
                height: 40px;
                font-size: 14px;
            }

            .productimg {
                display: flex;
                align-items: flex-start;
                gap: 8px;
            }

            .productimgs {
                flex: 0 0 64px;
            }

            .productimgs img {
                width: 64px;
                height: 64px;
                object-fit: cover;
                border-radius: 8px;
            }

            .productcontet {
                flex: 1 1 auto;
                min-width: 0;
            }

            .productcontet h4 {
                max-width: none !important;
                margin-bottom: 4px;
                font-size: 14px;
                line-height: 1.3;
                white-space: normal !important;
                word-break: break-word;
            }

            .productcontet h4 .cart-item-name {
                flex: 1 1 100% !important;
                max-width: 100% !important;
                white-space: normal !important;
                word-break: break-word;
                display: block;
            }

            .increment-decrement {
                display: flex;
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
            }

            .increment-decrement .input-groups {
                width: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }

            .increment-decrement .input-groups input[type=button] {
                width: 40px;
                height: 36px;
                font-size: 16px;
            }

            .increment-decrement .quantity-field {
                flex: 1 1 auto;
                width: auto !important;
                min-width: 0;
                height: 36px;
                font-size: 15px;
            }

            .increment-decrement > div:last-child {
                order: 0;
                margin: 0 !important;
                width: 100%;
                padding: 0;
            }

            .increment-decrement .product-price-input {
                width: 100%;
                height: 36px;
            }

            .product-discount-box {
                width: 100%;
                gap: 10px;
            }

            .product-discount-percentage,
            .product-discount-amount {
                width: 100%;
                min-width: 0;
                height: 36px;
            }

            .totalitem {
                margin-bottom: 10px;
            }

            .totalitem h4 {
                font-size: 14px;
            }

            .body_space_two .d-flex.justify-content-between.align-items-center.mb-1 {
                flex-wrap: wrap;
                gap: 8px;
            }

            .body_space_two .d-flex.justify-content-between .d-flex.align-items-center.gap-2 {
                flex-wrap: wrap;
                justify-content: flex-start;
                width: 100%;
                gap: 8px !important;
            }

            .setvalue {
                padding: 0 4px 12px !important;
            }

            .setvalue h5 {
                font-size: 13px;
            }

            .setvalue h6 {
                font-size: 14px;
            }

            .setvaluecash ul {
                display: flex;
                flex-wrap: nowrap;
                gap: 6px;
                margin: 0;
                padding: 8px 10px 6px;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
            }

            .setvaluecash ul::-webkit-scrollbar {
                display: none;
            }

            .setvaluecash ul li {
                margin: 0;
                flex: 0 0 auto;
                min-width: 72px;
            }

            .setvaluecash ul li a {
                min-height: 52px;
                min-width: 72px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 3px;
                padding: 6px 8px;
                font-size: 11px;
                border-radius: 8px;
            }

            .setvaluecash ul li a i {
                font-size: 16px !important;
                margin-bottom: 0 !important;
            }

            #posPaidTypeBox,
            #cashOnlineBox,
            #bankSelectionBox,
            #emiBox {
                padding: 0 10px 10px;
            }

            .btn-totallabel {
                margin: 0 10px 10px !important;
                padding: 12px 14px !important;
                border-radius: 10px;
                min-height: 52px;
            }

            .btn-totallabel h6,
            .btn-totallabel h5 {
                font-size: 14px;
            }

            #searchResults1.pos-search-dropdown-floating,
            .pos-bill-search-results.pos-search-dropdown-floating {
                max-height: min(50vh, 320px) !important;
                left: 10px !important;
                right: 10px !important;
                width: auto !important;
            }
        }

        @media screen and (max-width: 575px) {
            .page-wrapper .content {
                padding-left: 8px !important;
                padding-right: 8px !important;
            }

            .pos-action-toolbar-row {
                padding: 8px;
            }

            .pos-toolbar-chip {
                font-size: 10px;
                padding: 4px 8px;
            }

            .pos-gst-segment-btn span {
                padding: 4px 8px;
                font-size: 10px;
            }

            #posBillTabsBar .pos-bill-tab {
                max-width: 110px !important;
                font-size: 11px;
            }

            .setvaluecash ul li {
                min-width: 64px;
            }

            .setvaluecash ul li a {
                min-width: 64px;
                min-height: 48px;
                font-size: 10px;
            }

            .btn-totallabel h5 {
                font-size: 13px;
            }
        }

        .gst-info {
            margin: 4px 0;
            padding: 3px 8px;
            background: #f8f9fa;
            border-radius: 4px;
            border-left: 3px solid #4caf50;
            width: 135px;
        }

        .product-lists .gst-info small {
            display: block;
            line-height: 1.3;
        }

        .product-lists .gst-no-message {
            margin: 4px 0;
            padding: 6px 8px;
            background: #fff8e1;
            border-left: 3px solid #ff9f43;
            border-radius: 4px;
            color: #640a06;
            font-size: 12px;
            line-height: 1.4;
            width: 135px;
        }

        .productset .productsetimg .gst-hover-badge {
            position: absolute;
            color: #fff;
            font-size: 10px;
            padding: 5px;
            border-radius: 5px;
            top: 55px;
            right: 20px;
            transform: translatey(-100px);
            transition: all .5s;
            z-index: 11;
            font-weight: 600;
        }

        .productset:hover .productsetimg .gst-hover-badge {
            transform: translatey(0);
        }

        .gst-hover-badge.with-gst {
            background: #4caf50;
        }

        .gst-hover-badge.no-gst {
            background: #9e9e9e;
        }

        .product-discount-box {
            display: flex;
            gap: 12px;
            align-items: flex-end;
            margin-top: 6px;
            /* padding-right: 20px; */
        }

        .discount-field {
            display: flex;
            flex-direction: column;
        }

        .discount-field label {
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 3px;
            color: #444;
        }

        .product-discount-percentage,
        .product-discount-amount {
            width: 50px;
            height: 30px;
            padding: 4px 6px;
            font-size: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: #f9f9f9;
            text-align: center;
        }

        .product-discount-percentage:focus,
        .product-discount-amount:focus {
            border-color: #ff9f43;
            background: #fff;
            box-shadow: 0 0 3px rgba(255, 159, 67, 0.3);
        }

        .product-price-input {
            width: 90px;
            height: 34px;
            padding: 4px 8px;
            font-size: 14px;
            font-weight: 700;
            border: 1px solid #7a7979;
            border-radius: 5px;
            background: #fff;
            text-align: center;
        }

        .product-price-input:focus {
            outline: none;
            border-color: #ff9f43;
            box-shadow: 0 0 0 2px rgba(255, 159, 67, 0.15);
        }

        .product-lists {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 4px 10px 4px 0;
            border-bottom: 1px solid #eee;
            margin: 0 0 4px;
            gap: 5px;
        }

        .product-lists.just-scanned {
            background: #eaf8ee;
            border: 2px solid #22b428;
            border-radius: 6px;
            padding: 4px 8px;
            margin-bottom: 4px;
            animation: posScanPulse 0.7s ease-out 2;
        }

        .pos-category-catalog {
            display: none !important;
        }

        .pos-bill-head {
            display: none;
        }

        /*
         * Exchange Details sits in the narrow POS extras column (~300–400px).
         * Stack Material + Type full width so dropdowns never crush.
         */
        #exchange_charges_wrapper {
            width: 100%;
            max-width: 100%;
            min-width: 0;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        #exchange_charges_wrapper .card {
            overflow: visible;
            min-width: 0;
            width: 100%;
        }

        #exchange_rows_container {
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-width: 0;
            width: 100%;
        }

        .exchange-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) minmax(0, 1.15fr) 34px;
            grid-template-areas:
                "material material material material"
                "type type type type"
                "qty rate amt del";
            gap: 8px 8px;
            align-items: end;
            padding: 10px;
            margin: 0;
            border: 1px solid #ffe0b8;
            border-radius: 8px;
            background: #fff;
            box-sizing: border-box;
            min-width: 0;
            width: 100%;
        }

        .exchange-row.is-single {
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) minmax(0, 1.15fr);
            grid-template-areas:
                "material material material"
                "type type type"
                "qty rate amt";
        }

        .exchange-row.is-single .exchange-field-del {
            display: none;
        }

        .exchange-field-material { grid-area: material; min-width: 0; }
        .exchange-field-type { grid-area: type; min-width: 0; }
        .exchange-field-qty { grid-area: qty; min-width: 0; }
        .exchange-field-rate { grid-area: rate; min-width: 0; }
        .exchange-field-amt { grid-area: amt; min-width: 0; }
        .exchange-field-del { grid-area: del; }

        .exchange-field {
            min-width: 0;
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        .exchange-field .form-label {
            font-size: 11px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 4px;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .exchange-field .form-select,
        .exchange-field .form-control {
            width: 100%;
            min-width: 0;
            max-width: 100%;
            height: 36px;
            font-size: 13px;
            padding: 4px 8px;
        }

        .exchange-field .form-select {
            text-overflow: ellipsis;
        }

        .exchange-field-amt .exchange-amount-input {
            background: #f0fff4 !important;
            font-weight: 700 !important;
            color: #28c76f !important;
            text-align: right;
        }

        .exchange-field-del {
            display: flex;
            align-items: flex-end;
            justify-content: flex-end;
        }

        .exchange-field-del .remove-exchange-row-btn {
            width: 34px;
            height: 36px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .pos-customer-balance-chip {
            display: none;
            margin-top: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #0f766e;
        }

        .pos-customer-balance-chip.is-visible {
            display: block;
        }

        .pos-balance-snapshot {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px dashed #ffd4a8;
            font-size: 11px;
            color: #6b7280;
            font-weight: 600;
        }

        .pos-balance-snapshot b {
            color: #1b2850;
            font-weight: 700;
        }

        #exchange_surplus_options {
            margin-top: 10px !important;
        }

        #exchange_surplus_options .d-flex {
            gap: 12px !important;
        }

        @media screen and (max-width: 767.98px) {
            #exchange_surplus_options .d-flex {
                flex-direction: column;
                align-items: flex-start !important;
            }
        }

        #posInvoicePrintModal {
            overflow: hidden;
            z-index: 10650 !important;
        }

        body:has(#posInvoicePrintModal.show) .modal-backdrop {
            z-index: 10640 !important;
        }

        body.modal-open .payment_panel,
        body.modal-open .btn-totallabel {
            z-index: 1 !important;
        }

        .pos-invoice-print-dialog {
            max-width: 920px;
            width: calc(100% - 1.5rem);
            margin: 0.75rem auto;
            max-height: calc(100dvh - 1.5rem);
            height: calc(100dvh - 1.5rem);
        }

        .pos-invoice-print-content {
            overflow: hidden !important;
            height: 100%;
            max-height: 100%;
        }

        .pos-invoice-print-body {
            flex: 1 1 auto;
            min-height: 0;
            height: auto;
            background: #f3f4f6;
            position: relative;
            overflow: hidden !important;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .pos-invoice-print-body::-webkit-scrollbar {
            width: 0 !important;
            height: 0 !important;
            display: none !important;
        }

        .pos-invoice-print-body iframe {
            width: 100%;
            height: 100%;
            border: 0;
            display: block;
            background: #fff;
            overflow: hidden !important;
        }

        #posInvoicePrintModal,
        #posInvoicePrintModal .modal-dialog,
        #posInvoicePrintModal .modal-content,
        #posInvoicePrintModal .modal-body {
            overflow: hidden !important;
        }

        #posInvoicePrintModal ::-webkit-scrollbar {
            width: 0 !important;
            height: 0 !important;
            display: none !important;
        }

        .pos-invoice-print-loading {
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

        .pos-invoice-print-loading.is-visible {
            display: flex;
        }

        .pos-invoice-print-loading .spinner-border {
            width: 2rem;
            height: 2rem;
            color: #1b2850;
        }

        .pos-invoice-print-footer {
            overflow: visible;
            background: #fff;
            z-index: 6;
        }

        .pos-invoice-print-footer-inner {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 8px 12px;
            width: 100%;
        }

        .pos-invoice-print-footer-controls {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px 8px;
            min-width: 0;
            flex: 1 1 220px;
        }

        .pos-invoice-printer-select {
            min-width: 140px;
            max-width: 240px;
            flex: 1 1 160px;
        }

        .pos-invoice-print-status {
            flex: 1 1 100%;
            min-width: 0;
            color: #6c757d;
            line-height: 1.35;
            word-break: break-word;
        }

        .pos-invoice-print-footer-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            flex: 0 0 auto;
            margin-left: auto;
        }

        .pos-invoice-print-footer-actions .btn {
            min-width: 88px;
            white-space: nowrap;
        }

        .pos-invoice-print-footer-actions #posInvoicePrintBtn {
            min-width: 110px;
            font-weight: 600;
        }

        .pos-invoice-print-footer .btn[disabled] {
            opacity: 0.55;
            pointer-events: none;
        }

        #posInvoicePrintModal kbd {
            background: #1b2850;
            color: #fff;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 12px;
        }

        #posInvoicePrintModal .modal-header {
            align-items: flex-start;
            gap: 12px;
        }

        #posInvoicePrintModal .modal-header .btn-close {
            margin-top: 2px;
            flex-shrink: 0;
        }

        @media screen and (max-width: 991.98px) {
            #posInvoicePrintModal .modal-dialog {
                margin: 0;
                max-width: 100%;
                width: 100%;
                height: 100%;
                max-height: 100dvh;
                min-height: 100dvh;
            }

            #posInvoicePrintModal .pos-invoice-print-content {
                height: 100%;
                max-height: 100dvh;
                min-height: 100dvh;
                border-radius: 0;
                border: 0;
            }

            #posInvoicePrintModal .pos-invoice-print-body {
                height: auto;
                flex: 1 1 auto;
                min-height: 0;
                overflow: hidden;
            }

            #posInvoicePrintModal .pos-invoice-print-footer {
                display: block;
                padding: 12px 14px calc(12px + env(safe-area-inset-bottom, 0px)) !important;
                border-top: 1px solid #e5e7eb;
            }

            #posInvoicePrintModal .pos-invoice-print-footer-inner {
                flex-direction: column;
                align-items: stretch;
            }

            #posInvoicePrintModal .pos-invoice-print-footer-actions {
                width: 100%;
                margin-left: 0;
            }

            #posInvoicePrintModal .pos-invoice-print-footer-actions .btn {
                flex: 1 1 0;
                min-height: 44px;
                font-size: 15px;
                font-weight: 600;
            }

            #posInvoicePrintModal .modal-title {
                font-size: 17px;
            }

            #posInvoicePrintModal .modal-header small {
                font-size: 12px;
                line-height: 1.4;
            }
        }

        :root[data-theme="dark"] #posInvoicePrintModal .pos-invoice-print-content {
            background: #0f172a;
            color: #f8fafc;
        }

        :root[data-theme="dark"] #posInvoicePrintModal .pos-invoice-print-body,
        :root[data-theme="dark"] #posInvoicePrintModal .pos-invoice-print-loading {
            background: #111827;
            color: #e2e8f0;
        }

        :root[data-theme="dark"] #posInvoicePrintModal .pos-invoice-print-loading.is-visible {
            background: rgba(17, 24, 39, 0.94);
        }

        :root[data-theme="dark"] #posInvoicePrintModal .pos-invoice-print-footer {
            border-top-color: rgba(255, 255, 255, 0.12) !important;
        }

        .pos-added-notice {
            display: none;
            align-items: center;
            gap: 10px;
            background: #22b428;
            color: #fff;
            border-radius: 10px;
            padding: 10px 12px;
            margin: 0 0 10px;
            font-size: 14px;
            font-weight: 700;
            line-height: 1.3;
            box-shadow: 0 6px 16px rgba(34, 180, 40, 0.25);
        }

        .pos-added-notice.is-visible {
            display: flex;
        }

        .pos-added-notice .pos-added-check {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 16px;
        }

        .pos-added-notice .pos-added-text {
            min-width: 0;
        }

        .pos-added-notice .pos-added-text small {
            display: block;
            font-size: 12px;
            font-weight: 600;
            opacity: 0.95;
        }

        .just-scanned-badge {
            display: inline-block;
            background: #22b428;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 999px;
            margin-left: 6px;
            vertical-align: middle;
            white-space: nowrap;
        }

        .totalitem h4.just-scanned-total {
            color: #22b428;
        }

        @keyframes posScanPulse {
            0% { box-shadow: 0 0 0 0 rgba(34, 180, 40, 0.45); }
            70% { box-shadow: 0 0 0 10px rgba(34, 180, 40, 0); }
            100% { box-shadow: 0 0 0 0 rgba(34, 180, 40, 0); }
        }

        .product-lists>li {
            list-style: none;
        }

        .product-lists li:last-child {
            width: 40px;
            text-align: center;
            flex-shrink: 0;
        }

        .remove-item img {
            width: 25px;
            height: 25px;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .remove-item img:hover {
            transform: scale(1.1);
            opacity: 0.7;
        }

        .setvalue ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .product-table {
            max-height: 400px;
            overflow: auto;
        }

        .pos-split-handle {
            display: none;
        }

        #posMallLayout > #pos-left-panel,
        #posMallLayout > #posSplitHandle {
            display: none !important;
        }

        /* Hide raw stacked bill HTML until JS builds the final 2-column workspace */
        #posMallLayout.pos-layout-booting #pos-right-panel .pos-bill-scroll {
            opacity: 0;
            pointer-events: none;
        }

        #posMallLayout.pos-layout-ready #pos-right-panel .pos-bill-scroll {
            opacity: 1;
            pointer-events: auto;
            transition: opacity .12s ease;
        }

        /* Keep chip/shipping styles even before workspace exists (prevents FOUC if boot is slow) */
        #pos-right-panel .pos-bill-extras-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            width: 100%;
        }

        #pos-right-panel .pos-bill-extra-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            flex: 0 0 auto;
            min-width: 0;
            padding: 4px 6px 4px 8px;
            border: 1px solid #ece9f1;
            border-radius: 8px;
            background: #fafafa;
        }

        #pos-right-panel .pos-bill-extra-chip > span {
            font-size: 12px;
            font-weight: 600;
            color: #5e5873;
            white-space: nowrap;
            line-height: 1.2;
        }

        #pos-right-panel .pos-bill-extra-btn {
            border-radius: 6px !important;
            width: 26px !important;
            height: 26px !important;
            min-width: 26px !important;
            padding: 0 !important;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            background: #fff !important;
            border-color: #d8d6de !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            flex-shrink: 0;
        }

        #pos-right-panel .setvalue ul li[style*="display: none"],
        #pos-right-panel .setvalue ul li[style*="display:none"] {
            display: none !important;
        }

        #posMallLayout > #pos-right-panel {
            flex: 1 1 100% !important;
            max-width: 100% !important;
            width: 100% !important;
            min-width: 0;
        }

        .pos-mall-layout {
            width: 100%;
            margin-left: 0;
            margin-right: 0;
        }

        @media (min-width: 992px) {
            html:has(.pos-mall-layout),
            body:has(.pos-mall-layout),
            body:has(.pos-mall-layout) .main-wrapper {
                overflow: hidden;
                height: 100%;
            }

            body:has(.pos-mall-layout) > footer {
                display: none !important;
            }

            body:has(.pos-mall-layout) .page-wrapper {
                display: flex;
                flex-direction: column;
                height: 100vh;
                max-height: 100vh;
                overflow: hidden;
                box-sizing: border-box;
                padding-bottom: 0;
            }

            .page-wrapper .content {
                padding: 8px 12px 8px !important;
                padding-bottom: 8px !important;
                flex: 1 1 0%;
                min-height: 0;
                height: auto;
                max-height: none;
                overflow: hidden;
                display: flex;
                flex-direction: column;
                box-sizing: border-box;
            }

            .pos-bill-tabs-bar {
                flex-shrink: 0;
            }

            .pos-mall-layout {
                display: flex;
                flex-wrap: nowrap;
                align-items: stretch;
                --pos-left-width: min(320px, 30%);
                flex: 1 1 0%;
                min-height: 0;
                height: 0;
                max-height: none;
                overflow: hidden;
            }

            #posMallLayout > #pos-right-panel {
                float: none;
                padding-left: 8px;
                padding-right: 8px;
                height: 100% !important;
                max-height: 100% !important;
                min-height: 0 !important;
                align-self: stretch;
                flex: 1 1 100% !important;
                max-width: 100% !important;
                width: 100% !important;
                min-width: 0;
                overflow: hidden !important;
                display: flex !important;
                flex-direction: column !important;
                padding-bottom: 0;
            }

            #pos-right-panel .card-order,
            #pos-right-panel .card-order > .card-body {
                flex: 1 1 auto !important;
                min-height: 0 !important;
                height: 100% !important;
                max-height: 100% !important;
                max-width: 100%;
                display: flex !important;
                flex-direction: column !important;
                overflow: hidden !important;
                margin: 0;
            }

            /* Height chain: panel → card → body → row → col → scroll → workspace (no whole-panel scroll) */
            #pos-right-panel .card-order > .card-body > .row,
            #pos-right-panel .pos-bill-col,
            #pos-right-panel .pos-bill-scroll {
                flex: 1 1 auto !important;
                height: 100% !important;
                max-height: 100% !important;
                min-height: 0 !important;
                max-width: 100%;
                width: 100%;
                display: flex !important;
                flex-direction: column !important;
                overflow: hidden !important;
                margin: 0;
            }

            #pos-right-panel .card-order > .card-body > .row {
                flex-wrap: nowrap !important;
            }

            #pos-right-panel .pos-bill-col {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            #pos-right-panel .pos-bill-workspace {
                flex: 1 1 auto !important;
                min-height: 0 !important;
                height: auto !important;
                max-height: none !important;
                overflow: hidden !important;
                order: 1;
            }

            #pos-right-panel .payment_panel,
            #pos-right-panel #paymentPanel {
                flex: 0 0 auto !important;
                position: relative !important;
                bottom: auto !important;
                top: auto !important;
                order: 2;
            }

            #pos-right-panel .pos-bill-scroll {
                overflow: hidden !important;
            }

            #pos-right-panel .pos-bill-scroll.pos-has-workspace > .pos-bill-scroll-hidden {
                display: none !important;
                height: 0 !important;
                max-height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden !important;
                border: 0 !important;
            }

            #pos-right-panel .pos-bill-scroll.pos-has-workspace > #mobile-customer-container:empty {
                display: none !important;
                margin: 0 !important;
                padding: 0 !important;
                height: 0 !important;
            }

            #posAddedNotice,
            #pos-right-panel .pos-added-notice,
            #pos-right-panel .pos-added-notice.is-visible {
                display: none !important;
                height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden !important;
            }

            .pos-split-handle {
                display: flex;
                align-items: center;
                justify-content: center;
                flex: 0 0 10px;
                width: 10px;
                align-self: stretch;
                cursor: col-resize;
                user-select: none;
                background: transparent;
            }

            .pos-split-handle span {
                width: 4px;
                height: 56px;
                border-radius: 99px;
                background: #d8d6de;
            }

            .pos-split-handle:hover span,
            .pos-mall-layout.is-resizing .pos-split-handle span {
                background: #ff9f43;
            }

            .pos-mall-layout.is-resizing {
                cursor: col-resize;
                user-select: none;
            }

            #pos-left-panel .pos-quick-toolbar,
            #pos-left-panel > ul.tabs {
                flex-shrink: 0;
            }

            #pos-left-panel #mobile-customer-container {
                display: none;
            }

            #pos-left-panel .tabs_container {
                display: block !important;
                flex: 1 1 0% !important;
                height: 0 !important;
                min-height: 0 !important;
                max-height: none !important;
                overflow-y: auto !important;
                overflow-x: hidden;
                background: #f7f8fa;
                padding: 8px 10px 12px 8px;
                border-radius: 8px;
                scrollbar-gutter: stable;
            }

            #pos-left-panel .tabs_container::-webkit-scrollbar {
                width: 6px;
                display: block;
            }

            #pos-left-panel .tabs_container::-webkit-scrollbar-track {
                background: transparent;
                border-radius: 8px;
            }

            #pos-left-panel .tabs_container::-webkit-scrollbar-thumb {
                background: #d0d0d0;
                border-radius: 8px;
            }

            #pos-left-panel .tabs_container::-webkit-scrollbar-thumb:hover {
                background: #ff9f43;
            }

            #pos-left-panel .tabs_container .tab_content.active {
                display: block !important;
                min-height: 0;
                width: 100%;
            }

            #pos-left-panel .tabs_container .row {
                margin-left: 0 !important;
                margin-right: 0 !important;
                --bs-gutter-x: 8px;
                --bs-gutter-y: 8px;
            }

            #pos-left-panel .tabs_container .row > [class*="col-"] {
                flex: 0 0 33.333333%;
                max-width: 33.333333%;
                padding-left: 4px;
                padding-right: 4px;
                margin-bottom: 8px;
                min-width: 0;
                box-sizing: border-box;
            }

            #pos-left-panel .productset {
                display: block;
                flex-direction: unset;
                align-items: unset;
                text-align: center;
                gap: 0;
                width: 100%;
                padding: 4px 3px 6px;
                margin: 0 !important;
                min-height: 0;
                border: 1px solid #e5e7eb !important;
                border-radius: 6px;
                background: #fff;
                box-shadow: none;
                overflow: hidden;
                box-sizing: border-box;
            }

            #pos-left-panel .productset:hover,
            #pos-left-panel .productset.active {
                border-color: #ff9f43 !important;
            }

            #pos-left-panel .productsetimg {
                width: 100%;
                flex: none;
                margin: 0 auto;
            }

            #pos-left-panel .productsetimgin {
                height: 40px;
                width: auto;
                max-width: 100%;
                margin: 4px auto 0;
                object-fit: contain;
                display: block;
            }

            #pos-left-panel .productsetimg h6 {
                display: block;
                font-size: 9px;
                margin: 1px 0 0;
            }

            #pos-left-panel .productsetcontent {
                flex: none;
                min-width: 0;
                text-align: center !important;
                padding: 2px 4px 0 !important;
            }

            #pos-left-panel .productsetcontent h4 {
                font-size: 10px !important;
                font-weight: 600;
                text-transform: capitalize;
                margin: 2px 0 0 !important;
                line-height: 1.2;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            #pos-left-panel .productsetcontent h5 {
                font-size: 9px;
                margin: 0;
                text-transform: capitalize;
                color: #333;
            }

            #pos-left-panel .productsetcontent h6 {
                font-size: 11px;
                font-weight: 700;
                margin: 2px 0 0;
                color: #ff9f43;
            }

            #pos-left-panel .pos-card-rates {
                margin-top: 2px;
                line-height: 1.2;
            }

            #pos-left-panel .pos-card-rate {
                font-size: 8px;
                color: #4b5563;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            #pos-left-panel .pos-card-rate b {
                color: #1f2937;
                font-weight: 700;
            }

            #pos-left-panel .pos-card-mrp {
                color: #9ca3af;
                text-decoration: line-through;
                margin-right: 2px;
            }

            #pos-left-panel .pos-card-barcode {
                margin-top: 2px;
                font-size: 8px;
                color: #6b7280;
                font-family: ui-monospace, Consolas, monospace;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            #pos-left-panel .pos-card-more {
                font-size: 8px;
                color: #ff9f43;
                font-weight: 700;
                margin-top: 1px;
            }

            #pos-left-panel .pos-card-extra {
                display: none;
            }

            #pos-left-panel .productset .productsetimg .gst-hover-badge {
                position: static;
                transform: none !important;
                display: inline-block;
                font-size: 8px;
                padding: 1px 4px;
                margin: 2px 0 0;
                border-radius: 4px;
            }

            #pos-left-panel .product-edit-icon {
                width: 16px;
                height: 16px;
                top: 4px;
                right: 4px;
                font-size: 8px;
            }

            #pos-left-panel .pos-oos-badge {
                top: 4px;
                left: 4px;
                font-size: 8px;
                padding: 1px 4px;
                font-weight: 700;
            }

            #pos-left-panel .tabs {
                margin-bottom: 8px !important;
            }

            .card-order .card-body {
                padding: 12px 12px 8px;
            }

            .customer-section-title {
                font-size: 13px !important;
                margin-bottom: 4px !important;
                min-height: 28px;
            }

            .customer-section-title .customer-action-btns .btn,
            .customer-section-title .pos-fullscreen-btn {
                width: 24px !important;
                height: 24px !important;
            }

            .customer-section-title .pos-back-btn {
                font-size: 12px;
                padding: 2px 8px;
            }

            .customer-details-box {
                padding: 4px 8px 4px !important;
                margin-bottom: 4px !important;
            }

            .customer-details-box label,
            .pos-order-row label {
                margin-bottom: 1px !important;
                font-size: 11px;
                line-height: 1.2;
            }

            .customer-details-box .form-control,
            .pos-order-row .form-control,
            .pos-order-row select {
                height: 28px !important;
                min-height: 28px;
                font-size: 12px;
                padding: 2px 8px;
            }

            .customer-details-box .select2-container .select2-selection--single,
            .pos-order-row .select2-container .select2-selection--single,
            .customer-details-box .select2-container--default .select2-selection--single .select2-selection__arrow,
            .pos-order-row .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 28px !important;
            }

            .customer-details-box .select2-container--default .select2-selection--single .select2-selection__rendered,
            .pos-order-row .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 26px !important;
                font-size: 12px;
            }

            #pos-right-panel .pos-customer-row {
                display: flex;
                flex-wrap: wrap;
                align-items: flex-start;
                gap: 8px;
                margin: 0;
                height: auto !important;
                min-height: 0 !important;
                flex: 0 0 auto;
            }

            #pos-right-panel .pos-customer-row > .pos-customer-field,
            #pos-right-panel .pos-customer-row > .pos-customer-field--name,
            #pos-right-panel .pos-customer-row > .pos-customer-field--date {
                grid-column: auto;
                flex: 1 1 140px;
                align-self: flex-start;
                width: auto;
                min-width: 140px;
                height: auto !important;
                padding: 0;
                margin: 0;
            }

            #pos-right-panel .pos-order-row {
                display: flex;
                flex-wrap: wrap;
                align-items: flex-start;
                gap: 8px;
                margin: 4px 0 6px !important;
                height: auto !important;
                min-height: 0 !important;
                flex: 0 0 auto;
            }

            #pos-right-panel .pos-order-row > [class*="col-"] {
                flex: 1 1 140px;
                align-self: flex-start;
                max-width: none;
                width: auto;
                height: auto !important;
                min-height: 0 !important;
                padding: 0;
                margin: 0;
            }

            #pos-right-panel .totalitem {
                margin-bottom: 6px;
            }

            #pos-right-panel .pos-bill-items,
            #pos-right-panel .body_space_two,
            #pos-right-panel .setvalue,
            #pos-right-panel .customer-details-box,
            #pos-right-panel .customer-section-title {
                height: auto !important;
                min-height: 0 !important;
                flex: none;
            }

            #pos-right-panel .pos-bill-area-items .pos-bill-items {
                flex: 1 1 auto !important;
                min-height: 0 !important;
                height: 100% !important;
            }

            #pos-right-panel .customer-section-title {
                padding: 0 0 4px !important;
                margin-bottom: 4px !important;
            }

            .product-table {
                max-height: none;
                overflow: visible;
                scroll-behavior: smooth;
            }

            #pos-right-panel .pos-bill-area-items .product-table {
                overflow-x: hidden !important;
                overflow-y: auto !important;
                max-height: none !important;
            }

            #pos-right-panel .pos-bill-grid {
                display: flex;
                flex-direction: column;
                min-height: 0;
            }

            #pos-right-panel .pos-bill-head {
                flex-shrink: 0;
            }

            #pos-right-panel .product-table {
                overflow-y: auto !important;
                overflow-x: hidden;
                -webkit-overflow-scrolling: touch;
            }

            .pos-bill-grid {
                --pos-bill-cols: 24px minmax(0, 1.2fr) 150px 120px 110px 26px;
                min-width: 0;
            }

            .pos-bill-head {
                display: grid;
                grid-template-columns: var(--pos-bill-cols);
                align-items: end;
                gap: 6px;
                width: 100%;
                box-sizing: border-box;
                padding: 0 8px 6px;
                margin: 0 0 6px;
                font-size: 10px;
                font-weight: 700;
                flex-shrink: 0;
                letter-spacing: 0.03em;
                text-transform: uppercase;
                color: #6b7280;
                border-bottom: 1px solid #ececec;
            }

            .pos-bill-head-item {
                min-width: 0;
                align-self: end;
                padding-bottom: 2px;
            }

            .pos-bill-head-qty,
            .pos-bill-head-disc,
            .pos-bill-head-sno {
                text-align: center;
            }

            .pos-bill-head-sno {
                align-self: end;
                padding-bottom: 2px;
            }

            .pos-bill-head-qty,
            .pos-bill-head-disc {
                display: flex;
                flex-direction: column;
                align-items: stretch;
                gap: 3px;
                line-height: 1.15;
            }

            .pos-bill-head .pos-head-main {
                display: block;
                text-align: center;
                font-size: 10px;
                font-weight: 700;
                color: #6b7280;
            }

            .pos-bill-head .pos-head-subs {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 4px;
                width: 100%;
            }

            .pos-bill-head .pos-head-subs > span {
                display: block;
                text-align: center;
                font-size: 9px;
                font-weight: 700;
                letter-spacing: 0.02em;
                text-transform: none;
                color: #9ca3af;
                background: #f5f5f7;
                border: 1px solid #eceaf1;
                border-radius: 4px;
                padding: 2px 0;
                line-height: 1.2;
            }

            .pos-bill-head-disc .pos-head-subs > span:first-child {
                color: #7367f0;
                background: #f3f2ff;
                border-color: #e0dcf9;
            }

            .pos-bill-head-disc .pos-head-subs > span:last-child {
                color: #e08a1e;
                background: #fff4e6;
                border-color: #ffe0b8;
            }

            .pos-bill-head-amt {
                display: flex;
                align-items: flex-end;
                justify-content: flex-end;
                width: 100%;
                text-align: right;
                align-self: end;
                padding-bottom: 2px;
                padding-right: 2px;
                box-sizing: border-box;
            }

            #pos-right-panel .product-lists > li.cart-col-sno {
                justify-content: center;
                font-size: 12px;
                font-weight: 700;
                color: #6b7280;
            }

            #pos-right-panel .pos-bill-search-wrap {
                position: relative;
                width: 100%;
                min-width: 0;
                overflow: visible;
                z-index: 2;
            }

            #pos-right-panel .product-lists.pos-bill-search-row {
                overflow: visible;
                height: auto;
                min-height: 40px;
                padding: 4px 8px;
                border-style: dashed;
                border-color: #d1d5db;
                background: #fafafa;
                position: relative;
                z-index: 3;
            }

            #pos-right-panel .product-lists.pos-bill-search-row:focus-within {
                z-index: 20;
            }

            #pos-right-panel .product-lists.pos-bill-search-row > li {
                height: auto;
                min-height: 32px;
            }

            #pos-right-panel .pos-bill-search-input {
                height: 30px;
                font-size: 13px;
                padding: 4px 8px;
                border-radius: 4px;
            }

            #pos-right-panel .pos-bill-search-results {
                position: absolute;
                top: calc(100% + 2px);
                left: 0;
                right: 0;
                z-index: 1060;
                max-height: 280px;
                overflow-y: auto;
                display: none;
                border-radius: 6px;
            }

            #pos-right-panel .pos-bill-search-plus {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 28px;
                height: 28px;
                border-radius: 6px;
                border: 1px solid #ffc48a;
                background: #fff8f1;
                font-size: 18px;
                font-weight: 700;
                color: #ff9f43;
                line-height: 1;
                cursor: pointer;
                user-select: none;
                padding: 0;
            }
            #pos-right-panel .pos-bill-search-plus:hover {
                background: #ff9f43;
                color: #fff;
                border-color: #ff9f43;
            }
            #pos-right-panel .pos-bill-search-wrap {
                display: flex;
                align-items: center;
                gap: 6px;
                flex-wrap: nowrap;
                width: 100%;
                position: relative;
            }
            #pos-right-panel .pos-bill-search-input {
                flex: 1 1 auto;
                min-width: 0;
            }
            #pos-right-panel .pos-create-product-btn {
                flex: 0 0 auto;
                white-space: nowrap;
                background: #ff9f43;
                border: 1px solid #ff9f43;
                color: #fff;
                font-weight: 600;
                font-size: 12px;
                border-radius: 6px;
                padding: 4px 10px;
                line-height: 1.4;
                box-shadow: 0 1px 2px rgba(255, 159, 67, 0.35);
            }
            #pos-right-panel .pos-create-product-btn:hover,
            #pos-right-panel .pos-create-product-btn:focus {
                background: #e8892e;
                border-color: #e8892e;
                color: #fff;
            }
            #pos-right-panel .pos-bill-search-results {
                left: 0;
                right: 0;
            }

            #pos-right-panel .product-lists {
                display: grid;
                grid-template-columns: var(--pos-bill-cols);
                align-items: center;
                justify-content: stretch;
                gap: 6px;
                min-height: 44px;
                height: auto;
                padding: 6px 8px;
                margin: 0 0 6px;
                border: 1px solid #ececec;
                border-radius: 8px;
                background: #fff;
                overflow: visible;
                box-sizing: border-box;
            }

            #pos-right-panel .product-lists.just-scanned {
                padding: 6px 8px;
                margin-bottom: 6px;
                border-radius: 8px;
                height: auto;
                min-height: 44px;
            }

            #pos-right-panel .product-lists > li {
                min-width: 0;
                margin: 0;
                padding: 0;
                height: auto;
                min-height: 0;
                display: flex;
                align-items: center;
                overflow: visible;
            }

            #pos-right-panel .product-lists > li.cart-col-item {
                overflow: hidden;
                justify-content: flex-start;
                text-align: left;
                align-self: center;
                min-width: 0;
            }

            #pos-right-panel .product-lists > li.cart-col-disc {
                align-self: center;
                overflow: visible;
            }

            #pos-right-panel .product-lists > li.price,
            #pos-right-panel .product-lists > li.cart-col-amt {
                width: 100%;
                margin: 0;
                padding: 0 2px 0 0;
                background: transparent;
                border-radius: 0;
                justify-content: flex-end;
                align-items: flex-end;
                align-self: center;
                text-align: right;
                box-sizing: border-box;
            }

            #pos-right-panel .price {
                display: flex;
                flex-direction: column;
                flex-wrap: nowrap;
                align-items: flex-end;
                justify-content: center;
                gap: 2px;
                width: 100%;
                font-size: 12px;
                white-space: nowrap;
                text-align: right;
            }

            #pos-right-panel .price-row {
                margin: 0;
                gap: 4px;
                width: auto;
                max-width: 100%;
                justify-content: flex-end;
            }

            #pos-right-panel .price-row.sub-total {
                display: none;
            }

            #pos-right-panel .price-row.gst-inc {
                display: inline-flex;
                justify-content: flex-end;
                align-items: center;
                gap: 4px;
                order: 2;
                font-size: 10px;
                font-weight: 700;
                color: #9a5b00;
                line-height: 1.2;
                margin: 0;
                padding: 2px 7px;
                border-radius: 999px;
                background: #fff4e6;
                border: 1px solid #ffd7a8;
                width: auto;
                max-width: 100%;
            }

            /* Discount already shown in Discount column — hide duplicate under amount */
            #pos-right-panel .price-row.discount {
                display: none !important;
            }

            #pos-right-panel .price-row.gst-inc span:first-child {
                display: inline;
                opacity: 0.85;
            }

            #pos-right-panel .final-total {
                order: 1;
                margin: 0;
                padding: 0;
                border: none;
                font-size: 13px;
                font-weight: 800;
                color: #28c76f;
                line-height: 1.2;
                text-align: right;
                width: 100%;
            }

            #pos-right-panel .final-total span {
                text-align: right;
            }

            #pos-right-panel .final-total span:first-child {
                display: none;
            }

            #pos-right-panel .product-lists > li.delete-col,
            #pos-right-panel .product-lists li:last-child {
                position: static;
                top: auto;
                right: auto;
                width: 26px;
                margin: 0;
                flex: none;
                justify-content: center;
            }

            #pos-right-panel .productimg {
                display: flex;
                align-items: center;
                justify-content: flex-start;
                gap: 6px;
                min-width: 0;
                width: 100%;
                text-align: left;
            }

            #pos-right-panel .productimgs,
            #pos-right-panel .productimg .productimgs {
                flex: 0 0 28px;
                width: 28px;
                height: 28px;
                margin: 0;
                overflow: hidden;
                font-size: 0;
                line-height: 0;
            }

            #pos-right-panel .productimgs img,
            #pos-right-panel .productimg .productimgs img {
                width: 28px;
                height: 28px;
                border-radius: 4px;
                object-fit: cover;
            }

            #pos-right-panel .productcontet {
                min-width: 0;
                flex: 1 1 auto;
                overflow: hidden;
                text-align: left;
            }

            #pos-right-panel .productcontet h4,
            #pos-right-panel .productimg .productcontet h4 {
                display: flex;
                align-items: center;
                justify-content: flex-start;
                gap: 4px;
                flex: 1 1 auto;
                min-width: 0;
                max-width: none;
                margin: 0 !important;
                font-size: 12px;
                font-weight: 700;
                line-height: 1.2;
                height: auto;
                min-height: 28px;
                text-align: left;
                white-space: nowrap !important;
                word-break: normal;
                overflow: hidden;
            }

            #pos-right-panel .cart-item-name {
                flex: 1 1 0;
                min-width: 0;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                text-align: left;
            }

            #pos-right-panel .productimg .productcontet h4 img {
                display: inline-block;
                opacity: 1;
                margin: 0;
                width: 12px;
                height: 12px;
            }

            #pos-right-panel .product-lists .rental-datetime,
            #pos-right-panel .product-lists .rent-box,
            #pos-right-panel .product-lists .serial-no-box,
            #pos-right-panel .below-purchase-warning {
                display: none !important;
            }

            /* Compact product-wise GST under item name */
            #pos-right-panel .product-lists .gst-info {
                display: flex !important;
                flex-wrap: wrap;
                align-items: center;
                gap: 4px;
                margin: 4px 0 0 !important;
                padding: 0 !important;
                background: transparent !important;
                border: 0 !important;
                border-radius: 0 !important;
                width: auto !important;
                max-width: 100%;
            }

            #pos-right-panel .product-lists .gst-info .pos-gst-chip {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 2px 7px;
                border-radius: 999px;
                background: #fff4e6;
                border: 1px solid #ffd7a8;
                color: #9a5b00;
                font-size: 10px;
                font-weight: 700;
                line-height: 1.3;
                white-space: nowrap;
            }

            #pos-right-panel .product-lists .gst-info .pos-gst-chip strong {
                font-weight: 800;
                color: #7a4500;
            }

            #pos-right-panel .product-lists .gst-info #gstrates,
            #pos-right-panel .product-lists .gst-info #gstamount,
            #pos-right-panel .product-lists .gst-info small {
                display: none !important;
            }

            #pos-right-panel .product-lists .gst-no-message {
                display: none !important;
            }

            #pos-right-panel .cart-col-qty .increment-decrement {
                display: grid !important;
                grid-template-columns: 1fr 1fr;
                align-items: center;
                gap: 4px;
                margin: 0 !important;
                width: 100%;
            }

            #pos-right-panel .increment-decrement .input-groups {
                width: 100%;
                margin: 0 !important;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 1px;
                flex-shrink: 0;
            }

            #pos-right-panel .increment-decrement .input-groups input[type=button] {
                width: 20px;
                height: 28px;
                font-size: 13px;
                line-height: 1;
            }

            #pos-right-panel .increment-decrement .quantity-field {
                width: 28px !important;
                min-width: 28px;
                height: 28px;
                padding: 0;
                font-size: 12px;
                font-weight: 700;
            }

            #pos-right-panel .increment-decrement > div:last-child {
                order: 0;
                width: 100%;
                margin: 0 !important;
                padding: 0;
            }

            #pos-right-panel .product-price-input,
            #pos-right-panel .increment-decrement .product-price-input {
                width: 100% !important;
                height: 28px;
                padding: 0 4px;
                font-size: 12px;
            }

            #pos-right-panel .price-input-container {
                position: relative;
                flex-shrink: 0;
                width: 100%;
            }

            #pos-right-panel .product-discount-box {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                align-items: stretch !important;
                justify-content: stretch;
                gap: 4px !important;
                margin: 0 !important;
                width: 100% !important;
                min-height: 0;
            }

            #pos-right-panel .discount-field {
                display: flex !important;
                flex: 1 1 0 !important;
                flex-direction: row !important;
                align-items: stretch !important;
                margin: 0 !important;
                width: auto !important;
                min-width: 0 !important;
                border: 1px solid #d8d6de;
                border-radius: 6px;
                overflow: hidden;
                background: #fff;
                height: 28px;
                min-height: 28px;
                box-sizing: border-box;
            }

            #pos-right-panel .discount-field label,
            #pos-right-panel .discount-field .pos-disc-suffix {
                display: none !important;
            }

            #pos-right-panel .discount-field.is-percent {
                border-color: #d7d2f5;
                background: #fcfbff;
            }

            #pos-right-panel .discount-field.is-amount {
                border-color: #ffd7a8;
                background: #fffaf3;
            }

            #pos-right-panel .product-discount-percentage,
            #pos-right-panel .product-discount-amount {
                flex: 1 1 auto !important;
                width: 100% !important;
                min-width: 0 !important;
                max-width: none !important;
                height: 100% !important;
                margin: 0 !important;
                padding: 0 4px !important;
                border: 0 !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                background: transparent !important;
                text-align: center;
                font-size: 12px;
                font-weight: 600;
            }

            #pos-right-panel .product-discount-percentage:focus,
            #pos-right-panel .product-discount-amount:focus {
                outline: none;
                background: #fff !important;
            }

            #pos-right-panel .discount-field:focus-within {
                border-color: #ff9f43;
                box-shadow: 0 0 0 2px rgba(255, 159, 67, 0.15);
            }

            #pos-right-panel .just-scanned-badge {
                flex-shrink: 0;
                font-size: 9px;
                padding: 1px 5px;
            }

            #pos-right-panel .payment_panel .setvaluecash,
            .payment_panel .setvaluecash {
                margin: 0 !important;
            }

            #pos-right-panel .payment_panel .setvaluecash ul,
            .payment_panel .setvaluecash ul {
                display: flex;
                flex-wrap: nowrap;
                gap: 6px;
                margin: 0;
                padding: 6px 8px 4px;
            }

            #pos-right-panel .payment_panel .setvaluecash ul li,
            .payment_panel .setvaluecash ul li {
                flex: 1 1 0;
                width: auto;
                min-width: 0;
                margin: 0;
                list-style: none;
            }

            #pos-right-panel .payment_panel .setvaluecash ul li a,
            .payment_panel .setvaluecash ul li a {
                min-height: 44px;
                height: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 2px;
                padding: 5px 4px;
                font-size: 11px;
                line-height: 1.15;
                text-align: center;
            }

            #pos-right-panel .payment_panel .setvaluecash ul li a i,
            .payment_panel .setvaluecash ul li a i {
                font-size: 14px !important;
                margin-bottom: 0 !important;
                display: block !important;
            }

            #pos-right-panel .payment_panel .btn-totallabel,
            .payment_panel .btn-totallabel {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                margin: 4px 8px 8px;
                padding: 10px 14px;
                background: #1b2850;
                color: #fff;
                border-radius: 6px;
            }

            #pos-right-panel .payment_panel .btn-totallabel h6,
            #pos-right-panel .payment_panel .btn-totallabel h5,
            .payment_panel .btn-totallabel h6,
            .payment_panel .btn-totallabel h5 {
                color: #fff;
                margin: 0;
                font-size: 14px;
            }

            #pos-right-panel .payment_panel,
            .payment_panel {
                position: relative !important;
                bottom: auto !important;
                top: auto !important;
                left: auto !important;
                right: auto !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 8px 0 0;
                background: #fff !important;
                box-sizing: border-box;
                box-shadow: 0 -8px 18px rgba(15, 23, 42, 0.12);
                z-index: 20;
                overflow-x: hidden;
                overflow-y: visible;
                flex: 0 0 auto !important;
            }

            #pos-right-panel .payment_panel .btn-totallabel,
            .payment_panel .btn-totallabel {
                margin: 4px 8px 0;
            }

            #pos-right-panel .payment-panel-handle {
                display: none;
            }

            #pos-right-panel .setvalue {
                padding-bottom: 8px !important;
            }
        }

        .product-table::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }

        .product-table::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .product-table::-webkit-scrollbar-thumb {
            background: #FF9F43;
            border-radius: 4px;
        }

        .product-table::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .setvalue ul li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 0;
            border-bottom: 1px dashed #eee;
        }

        .setvalue ul li:last-child {
            border-bottom: none;
        }

        .setvalue h5 {
            font-size: 14px;
            font-weight: 500;
            margin: 0;
            color: #555;
        }

        .setvalue h6 {
            font-size: 15px;
            font-weight: 600;
            margin: 0;
            color: #222;
        }

        .setvalue {
            padding: 0 0 40px 0 !important;
        }

        .pos-bill-area-side .setvalue,
        #pos-right-panel .pos-bill-area-side .setvalue {
            padding: 14px 16px !important;
        }

        .setvalue .total-value h6 {
            font-size: 18px;
            font-weight: 700;
            color: #28a745;
        }

        /* Fix customer row layout */
        .row.select-group.w-100 {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            gap: 15px;
            /* space between boxes */
            margin: 0;
        }

        /* Ensure both columns use equal width */
        .row.select-group .col-md-6 {
            flex: 1;
            max-width: 50%;
            padding: 0;
        }

        /* Remove unnecessary nested width conflicts */
        .select-split,
        .select-group {
            width: 100%;
        }

        /* Improve input/select appearance */
        #customer_name,
        #customer_phone,
        #order_date,
        #order_date_display {
            width: 100%;
        }

        .pos-order-date-group .input-groupicon {
            position: relative;
        }

        .pos-order-date-group .addonset {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            z-index: 2;
        }

        .pos-order-date-group .addonset img {
            width: 16px;
            height: 16px;
        }

        #order_date_display {
            padding-right: 38px;
            background-color: #fff;
            cursor: pointer;
        }

        /* Fix for labour items select */
        .select2-labour+.select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            height: 38px;
            padding-top: 4px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 29px !important;
        }

        #qr-reader {
            min-height: 300px;
            width: 100%;
            background: #f5f5f5;
            /* optional: shows a background while loading */
        }

        .productcontet h4 {
            font-size: 13px;
            line-height: 1.3;
            min-width: 0;
            max-width: none;
        }

        #pos-right-panel .productimg,
        #pos-right-panel .productimg .productcontet h4 {
            justify-content: flex-start;
            text-align: left;
        }

        /* ===== POS PAYMENT PANEL FIELDS FIX ===== */
        #posPaidTypeBox,
        #cashOnlineBox,
        #bankSelectionBox,
        #emiBox {
            width: 100%;
            box-sizing: border-box;
        }

        #posPaidTypeBox .form-group,
        #cashOnlineBox .form-group,
        #bankSelectionBox .form-group,
        #emiBox .form-group {
            display: flex;
            flex-direction: column;
            width: 100%;
            margin-bottom: 10px;
        }

        #posPaidTypeBox .form-group label,
        #cashOnlineBox .form-group label,
        #bankSelectionBox .form-group label,
        #emiBox .form-group label {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 4px;
            color: #444;
            width: 100%;
        }

        #posPaidTypeBox .form-group select,
        #posPaidTypeBox .form-group input,
        #cashOnlineBox .form-group input,
        #bankSelectionBox .form-group select,
        #emiBox .form-group input,
        #emiBox .form-group select {
            width: 100% !important;
            box-sizing: border-box;
            height: 36px;
            font-size: 13px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 4px 10px;
            background: #fff;
        }

        #posPaidAmountFields {
            width: 100%;
        }

        #cashOnlineBox .row,
        #bankSelectionBox .row,
        #emiBox .row {
            margin-left: 10px;
            margin-right: 5px;
        }

        #cashOnlineBox .row>[class*="col-"],
        #bankSelectionBox .row>[class*="col-"],
        #emiBox .row>[class*="col-"] {
            padding-left: 6px;
            padding-right: 6px;
        }

        #emiBox {
            max-height: 190px;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 8px 6px 0 0;
        }

        #emiBox::-webkit-scrollbar {
            width: 5px;
        }

        #emiBox::-webkit-scrollbar-thumb {
            background: #d7dde8;
            border-radius: 10px;
        }

        .bank-label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 4px;
        }

        .bank-add-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            border: 1px solid #ff9f43;
            background: #fff7ed;
            color: #ff9f43;
            border-radius: 4px;
            padding: 3px 10px;
            font-size: 12px;
            font-weight: 600;
            line-height: 1.2;
            text-decoration: none;
        }

        .bank-add-btn:hover {
            color: #fff;
            background: #ff9f43;
        }

        #posPaidAmountFields .form-group {
            display: flex;
            flex-direction: column;
            width: 100%;
            margin-bottom: 10px;
        }

        #posPaidAmountFields .form-group label {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 4px;
            color: #444;
        }

        #posPaidAmountFields .form-group input {
            width: 100% !important;
            box-sizing: border-box;
            height: 36px;
            font-size: 13px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 4px 10px;
            background: #fff;
        }

        #posPendingAmount {
            background: #f8f9fa !important;
            color: #555;
        }

        /* Readonly/computed fields */
        #posPaidAmount[readonly],
        #posCashOnlineOnlineAmount[readonly] {
            background: #f8f9fa !important;
            color: #555;
            cursor: not-allowed;
        }

        /* ===== POS PAYMENT PANEL - TWO COLUMN LAYOUT FOR FIELDS ===== */

        /* Make the payment panel containers use grid layout */
        .payment_panel #posPaidTypeBox,
        .payment_panel #cashOnlineBox,
        .payment_panel #bankSelectionBox,
        .payment_panel #emiBox {
            display: block;
            width: 100%;
        }

        /* Apply grid to form groups inside payment boxes */
        .payment_panel #posPaidTypeBox .form-group,
        .payment_panel #cashOnlineBox .form-group,
        .payment_panel #bankSelectionBox .form-group,
        .payment_panel #emiBox .form-group {
            display: block;
            width: 100%;
            margin-bottom: 12px;
        }

        /* For the Paid Type Box - keep select full width, but amount fields in 2 columns */
        #posPaidTypeBox #posPaidAmountFields {
            display: grid;
            /* grid-template-columns: repeat(2, 1fr); */
            gap: 12px;
            margin-top: 8px;
        }

        #posPaidTypeBox #posPaidAmountFields .form-group {
            margin-bottom: 0;
        }

        #posPaidTypeBox #posPaidAmountFields .form-group:first-child {
            grid-column: 1 / 2;
        }

        #posPaidTypeBox #posPaidAmountFields .form-group:last-child {
            grid-column: 2 / 3;
        }

        /* For Cash+Online box - cash and online amounts in 2 columns */
        /* #cashOnlineBox {
            display: grid !important;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-top: 10px;
        } */

        #cashOnlineBox .form-group {
            margin-bottom: 0;
        }

        /* For Bank Selection box - keep full width (but can be full width) */
        #bankSelectionBox .form-group {
            width: 100%;
        }

        /* Make all inputs inside payment panel have consistent styling */
        .payment_panel input,
        .payment_panel select {
            width: 100% !important;
            box-sizing: border-box;
        }

        .payment-detail-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 8px;
        }

        .payment-detail-cancel {
            border: 1px solid #dc3545;
            background: #fff;
            color: #dc3545;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
            line-height: 1;
            padding: 8px 12px;
        }

        .payment-detail-cancel:hover {
            background: #dc3545;
            color: #fff;
        }

        /* Responsive adjustments for mobile */
        @media screen and (max-width: 767px) {

            /* On mobile, stack them vertically */
            #posPaidTypeBox #posPaidAmountFields {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            #cashOnlineBox {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .bank-label-row {
                align-items: flex-start;
                /* flex-direction: column; */
                gap: 6px;
            }

            #posPaidTypeBox #posPaidAmountFields .form-group:first-child,
            #posPaidTypeBox #posPaidAmountFields .form-group:last-child {
                grid-column: auto;
            }

            /* Mobile validation message styling */
            .error_total {
                display: block !important;
                width: 100% !important;
                min-height: auto !important;
                height: auto !important;
                /* padding: 12px !important; */
                margin-bottom: 15px !important;
                margin-top: 10px !important;
                box-sizing: border-box !important;
                line-height: 1.4 !important;
                white-space: normal !important;
                word-wrap: break-word !important;
            }

            .pos-customer-row>.pos-customer-field--name,
            .pos-customer-row>.pos-customer-field--name .select-split,
            .pos-customer-row>.pos-customer-field--name .select-group,
            .pos-customer-row>.pos-customer-field--name .select2-container,
            .pos-customer-row>.pos-customer-field--name .select2-selection {
                width: 100% !important;
                max-width: 100% !important;
            }
        }

        /* For tablet devices */
        @media screen and (min-width: 768px) and (max-width: 1024px) {
            #posPaidTypeBox #posPaidAmountFields {
                /* grid-template-columns: repeat(2, 1fr); */
                gap: 15px;
            }

            #cashOnlineBox {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }

            .pos-customer-row {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px;
            }

            .pos-customer-row>.pos-customer-field {
                width: 100%;
                max-width: none;
                padding: 0;
                flex: none;
            }

            .pos-customer-row>.pos-customer-field--date {
                grid-column: 1 / -1;
            }

            /* Name + phone share one row on tablet */
            .pos-customer-row>.pos-customer-field--name {
                grid-column: auto;
            }

            .pos-customer-row>.pos-customer-field--name .select2-container,
            .pos-customer-row>.pos-customer-field--name .form-control {
                width: 100% !important;
            }

            #mobile-customer-container .pos-customer-row {
                display: grid !important;
                grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr) !important;
                gap: 12px !important;
                width: 100% !important;
            }

            #mobile-customer-container .pos-customer-row > .pos-customer-field,
            #mobile-customer-container .pos-customer-row > .pos-customer-field--name {
                grid-column: auto !important;
                width: auto !important;
                max-width: none !important;
                min-width: 0 !important;
                flex: none !important;
            }
        }

        /* ===== POS ORDER NUMBER BADGE ===== */
        .pos-order-number-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #fff;
            border: 1.5px solid #e0e0e0;
            border-radius: 8px;
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 600;
            color: #1b2850;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.07);
            white-space: nowrap;
            flex-shrink: 0;
        }

        .pos-order-number-label {
            color: #888;
            font-weight: 500;
            font-size: 12px;
        }

        .pos-order-number-value {
            color: #ff9f43;
            font-weight: 700;
            font-size: 13px;
            letter-spacing: 0.3px;
        }

        .pos-fullscreen-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border: 1px solid #d1d5db;
            background: #fff;
            color: #4b5563;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .pos-fullscreen-btn:hover {
            background: #f3f4f6;
            color: #111827;
        }

        @media screen and (max-width: 767px) {
            .pos-order-number-badge {
                font-size: 11px;
                padding: 4px 9px;
            }

            .pos-order-number-value {
                font-size: 11px;
            }
        }
        /* New Bill modal - Dark theme text fix */
        .new-bill-modal-subtext {
            color: #555;
        }

        :root[data-theme="dark"] .new-bill-modal-subtext {
            color: #cbd5e1 !important;
        }

        :root[data-theme="dark"] #newBillTypeModalLabel {
            color: #f8fafc !important;
        }

        :root[data-theme="dark"] #newBillTypeModal .modal-content {
            background-color: #0f172a !important;
            border-color: rgba(255, 255, 255, 0.14) !important;
        }
        :root[data-theme="dark"] #newBillTypeModal p[style*="555"] {
            color: #cbd5e1 !important;
        }
        :root[data-theme="dark"] .tabs_wrapper ul.tabs li .product-details {
            background: #0f172a !important;
            border: 1px solid rgba(255, 255, 255, 0.14) !important;
            border-radius: 6px;
        }

        :root[data-theme="dark"] .tabs_wrapper ul.tabs li .product-details h6 {
            color: #f8fafc !important;
        }

        :root[data-theme="dark"] .tabs_wrapper ul.tabs li.active .product-details {
            background: transparent !important;
        }

        :root[data-theme="dark"] .tabs_wrapper ul.tabs li.active .product-details h6 {
            color: #ff9f43 !important;
        }
        /* Fullscreen button, order number badge, customer action buttons - Dark theme */
        :root[data-theme="dark"] .pos-fullscreen-btn {
            background: #0f172a !important;
            border-color: rgba(255, 255, 255, 0.14) !important;
            color: #cbd5e1 !important;
        }

        :root[data-theme="dark"] .pos-fullscreen-btn:hover {
            background: #1e293b !important;
            color: #fff !important;
        }

        :root[data-theme="dark"] .pos-order-number-badge {
            background: #0f172a !important;
            border-color: rgba(255, 255, 255, 0.14) !important;
            color: #f8fafc !important;
        }

        :root[data-theme="dark"] .pos-order-number-label {
            color: #94a3b8 !important;
        }

        :root[data-theme="dark"] .pos-order-number-value {
            color: #ff9f43 !important;
        }

        /* Add New Customer stays visible even when a customer is already selected */
        #openQuickAddCustomerBtn {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
        }

        /* Customer details section title + action buttons (+ icon) */
        :root[data-theme="dark"] .customer-section-title {
            color: #f8fafc !important;
        }

        :root[data-theme="dark"] #openQuickAddCustomerBtn,
        :root[data-theme="dark"] #openEditCustomerBtn {
            background: #0f172a !important;
            border-color: rgba(255, 255, 255, 0.14) !important;
        }
        /* Customer details box - Dark theme */
        :root[data-theme="dark"] .customer-details-box {
            background: #0f172a !important;
            border-color: #ff9f43 !important;
        }

        :root[data-theme="dark"] .customer-details-box label {
            color: #cbd5e1 !important;
        }

        :root[data-theme="dark"] .customer-details-box .form-control {
            background-color: #1e293b !important;
            border-color: rgba(255, 255, 255, 0.14) !important;
            color: #f8fafc !important;
        }

        :root[data-theme="dark"] .customer-details-box .form-control::placeholder {
            color: #64748b !important;
        }

        /* Select2 dropdown inside customer details box */
        :root[data-theme="dark"] .customer-details-box .select2-container .select2-selection--single {
            background-color: #1e293b !important;
            border-color: rgba(255, 255, 255, 0.14) !important;
        }

        :root[data-theme="dark"] .customer-details-box .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #f8fafc !important;
        }
    </style>
    <div class="content">
        @php
            $isQuotationMode = request('sale_type') === 'quotation';
            $isAdvanceReceiptMode = request('sale_type') === 'advance_receipt';
            $isRentalMode = request('sale_type') === 'rental';
            $showNewBillModal = false;
        @endphp
        <input type="hidden" id="quotation_status" name="quotation_status"
            value="{{ $isQuotationMode ? 'quotation' : ($isAdvanceReceiptMode ? 'advance_receipt' : ($isRentalMode ? 'rental' : 'sales')) }}">

        {{-- <div class="page-header">
            <div class="page-title">
                <h4 id="posPageTitle">{{ $isQuotationMode ? 'Add Quotation' : ($isAdvanceReceiptMode ? 'Add Advance Receipt' : ($isRentalMode ? 'Add Rental' : 'Add Sale')) }}</h4>
            </div>
            <div class="d-flex align-items-center gap-3 ms-auto">
                
                Fullscreen Toggle
                <button type="button" class="pos-fullscreen-btn" id="posFullscreenToggle" title="Expand panel">
                    <i class="fas fa-expand"></i>
                </button>

                Order Number Badge
                <div class="pos-order-number-badge" id="posOrderNumberBadge">
                    <span class="pos-order-number-label" id="posOrderNumberLabel">
                        @if ($isQuotationMode)
                            Quotation No:
                        @elseif($isAdvanceReceiptMode)
                            Advance Receipt No:
                        @elseif($isRentalMode ?? false)
                            Rental No:
                        @else
                            Order No:
                        @endif
                    </span>
                    <span class="pos-order-number-value" id="posOrderNumberValue">
                        @if ($isQuotationMode)
                            {{ $nextQuotationNo ?? 'Q-1' }}
                        @elseif($isAdvanceReceiptMode)
                            {{ $nextAdvanceNo ?? 'ADV-1' }}
                        @elseif($isRentalMode ?? false)
                            {{ $nextRentalNo ?? 'RO-000001' }}
                        @else
                            {{ $nextSaleNo ?? 'SI/HO/1' }}
                        @endif
                    </span>
                </div>
                <div class="page-btn">
                     <a href="{{ route('sales.list') }}" class="pos-back-btn">
                                    <i class="fa-solid fa-arrow-left"></i>
                                    Back
                                </a>
                </div>
            </div>
        </div> --}}
        {{-- Pass next numbers as JS variables for dynamic switching --}}
        <script>
            window.posNextNumbers = {
                sale: "{{ $nextSaleNo ?? 'SI/HO/1' }}",
                quotation: "{{ $nextQuotationNo ?? 'Q-1' }}",
                advance_receipt: "{{ $nextAdvanceNo ?? 'ADV-1' }}",
                rental: "{{ $nextRentalNo ?? 'RO-000001' }}"
            };
        </script>

        @if ($showNewBillModal)
            <div class="modal fade" id="newBillTypeModal" tabindex="-1" aria-labelledby="newBillTypeModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-body text-center py-4 px-3">
                            <h3 id="newBillTypeModalLabel" class="mb-3" style="font-size: 18px; font-weight: 700;">
                                Create New Bill
                            </h3>
                            <p class="mb-4 new-bill-modal-subtext" style="font-size: 14px;">
                                Choose bill type for this new entry.
                            </p>
                            <div class="d-flex flex-wrap align-items-center justify-content-center gap-2">
                                <a href="{{ route('sales.add', ['sale_type' => 'sales']) }}"
                                    class="btn px-3 py-1 text-white text-nowrap"
                                    style="background-color:#22c55e; border-color:#22c55e; font-weight:600; font-size:13px;">Sales</a>
                                <a href="{{ route('sales.add', ['sale_type' => 'quotation']) }}"
                                    class="btn px-3 py-1 text-white text-nowrap"
                                    style="background-color:#ff9f43; border-color:#ff9f43; font-weight:600; font-size:13px;">Quotation</a>
                                @if (optional(\App\Models\Setting::first())->advance_receipt)
                                    <a href="{{ route('sales.add', ['sale_type' => 'advance_receipt']) }}"
                                        class="btn px-3 py-1 text-white text-nowrap"
                                        style="background-color:#7367f0; border-color:#7367f0; font-weight:600; font-size:13px;">Advance
                                        Receipt</a>
                                @endif
                                @if (optional(\App\Models\Setting::first())->rental)
                                    <a href="{{ route('sales.add', ['sale_type' => 'rental']) }}"
                                        class="btn px-3 py-1 text-white text-nowrap"
                                        style="background-color:#eab308; border-color:#eab308; font-weight:600; font-size:13px;">Rental</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Variant Modal --}}
        <div class="modal fade" id="variantModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Select Variant</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <div class="modal-body">
                        <h6 id="variantProductName" class="mb-3"></h6>

                        <div class="mb-3">
                            <label class="form-label">Size</label>
                            <select id="variantSize" class="form-select">
                                <option value="">Select Size</option>
                            </select>
                        </div>

                        <div class="mb-3" id="variantColorContainer" style="display:none;">
                            <label class="form-label">Color</label>
                            <select id="variantColor" class="form-select">
                                <option value="">Select Color</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between mb-3 bg-light p-2 rounded">
                            <div>
                                <small class="text-muted d-block">Price</small>
                                <strong id="variantPriceDisplay">₹0.00</strong>
                            </div>
                            <div>
                                <small class="text-muted d-block">Available Stock</small>
                                <strong id="variantStockDisplay">0</strong>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" id="variantQty" class="form-control" value="1" min="1">
                        </div>

                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-primary text-white" id="confirmVariantBtn">Add to
                            Cart</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- IMEI Modal --}}
        <div class="modal fade" id="imeiModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Select IMEI Number</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h6 id="imeiProductName" class="mb-3"></h6>
                        <select id="imeiNumberInput" class="form-control">
                            <option value="">Select IMEI number</option>
                        </select>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-warning text-white" id="confirmImeiBtn">Add</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== QUICK ADD CUSTOMER MODAL ===== --}}
        <div class="modal fade" id="quickAddCustomerModal" tabindex="-1" aria-labelledby="quickAddCustomerModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form id="quickAddCustomerForm" enctype="multipart/form-data">
                        <input type="hidden" id="qac_edit_id" value="">
                        <div class="modal-header">
                            <h5 class="modal-title" id="quickAddCustomerModalLabel">Add New Customer</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close">x</button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                    <label>Customer Code</label>
                                    <input type="text" name="qac_customer_code" id="qac_customer_code"
                                        class="form-control" maxlength="255">
                                </div>
                              
                                <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                    <label>Customer Name <span class="text-danger">*</span></label>
                                    <input type="text" name="qac_customer_name" id="qac_customer_name"
                                        class="form-control" maxlength="80" placeholder="Customer Name">
                                    <div class="text-danger small qac-error" id="qac_error_customer_name"></div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                    <label>Company Name</label>
                                    <input type="text" name="qac_company_name" id="qac_company_name"
                                        class="form-control" maxlength="80" placeholder="Company Name">
                                    <div class="text-danger small qac-error" id="qac_error_company_name"></div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                    <label>Phone <span class="text-danger"></span></label>
                                    <input type="text" name="qac_phone" id="qac_phone" class="form-control"
                                        placeholder="Phone Number">
                                    <div class="text-danger small qac-error" id="qac_error_phone"></div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                    <label>Alternate Phone</label>
                                    <input type="text" name="qac_alternate_phone" id="qac_alternate_phone"
                                        class="form-control" placeholder="Phone Number (optional)">
                                    <div class="text-danger small qac-error" id="qac_error_alternate_phone"></div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                    <label>Email</label>
                                    <input type="email" name="qac_email" id="qac_email" class="form-control"
                                        placeholder="Email">
                                    <div class="text-danger small qac-error" id="qac_error_email"></div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                    <label>GST Number</label>
                                    <div style="position:relative;">
                                        <input type="text" name="qac_gst_number" id="qac_gst_number"
                                            class="form-control" maxlength="15" placeholder="GST Number (auto-fill)"
                                            autocomplete="off">
                                        <span id="qac-gst-loader"
                                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);display:none;color:#ff9f43;"><i
                                                class="fas fa-spinner fa-spin"></i></span>
                                    </div>
                                    <div class="text-danger small qac-error" id="qac_error_gst_number"></div>
                                    <div id="qac-gst-msg" style="font-size:12px;margin-top:3px;min-height:16px;"></div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                    <label>PAN Number</label>
                                    <input type="text" name="qac_pan_number" id="qac_pan_number" class="form-control"
                                        maxlength="10" placeholder="PAN Number">
                                    <div class="text-danger small qac-error" id="qac_error_pan_number"></div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                    <label>State Code</label>
                                    <input type="text" name="qac_state_code" id="qac_state_code" class="form-control"
                                        placeholder="e.g. 27">
                                    <div class="text-danger small qac-error" id="qac_error_state_code"></div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                    <label>State Name</label>
                                    <input type="text" name="qac_state_name" id="qac_state_name" class="form-control"
                                        placeholder="Auto-filled from state code" readonly>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                    <label>Country</label>
                                    <input type="text" name="qac_country" id="qac_country" class="form-control"
                                        placeholder="Country">
                                </div>
                                <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                    <label>City</label>
                                    <input type="text" name="qac_city" id="qac_city" class="form-control"
                                        placeholder="City">
                                </div>
                                <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                    <label>Address</label>
                                    <textarea name="qac_address" id="qac_address" class="form-control" rows="2" placeholder="Address"></textarea>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="mb-0">Delivery Address</label>
                                        <div class="form-check mb-0">
                                            <input class="form-check-input" type="checkbox" id="qac_use_same_address">
                                            <label class="form-check-label small mb-0"
                                                style="text-transform: none; font-weight: normal;"
                                                for="qac_use_same_address">Use Address</label>
                                        </div>
                                    </div>
                                    <textarea name="qac_delivery_address" id="qac_delivery_address" class="form-control" rows="2"
                                        placeholder="Delivery Address"></textarea>
                                </div>
                                  <div class="col-lg-3 col-sm-6 col-6 mb-3 text-center">
                                    <div style="width: 100px; height: 100px; border: 1px dashed #ccc; border-radius: 50%; margin: 0 auto; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
                                        <img id="qac_face_preview" src="" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                        <i class="fas fa-camera text-muted" id="qac_face_icon" style="font-size: 24px;"></i>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary mt-2 btn-open-face-scan" data-target="qac_register">
                                        <i class="fas fa-camera"></i> Scan Face
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn text-white" id="qacSaveBtn"
                                style="background-color:#ff9f43;">
                                <span class="spinner-border spinner-border-sm d-none" id="qacBtnSpinner" role="status"
                                    aria-hidden="true"></span>
                                <span id="qacSaveBtnText">Save Customer</span>
                            </button>
                            <button type="button" class="btn btn-secondary btn-cancel"
                                data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- ===== END QUICK ADD CUSTOMER MODAL ===== --}}

        <div class="modal fade" id="addBankModal" tabindex="-1" aria-labelledby="addBankModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="addBankForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addBankModalLabel">Add Bank</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close">x</button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label for="add_bank_name" class="form-label">Bank Name</label>
                                    <input type="text" class="form-control" id="add_bank_name" name="bank_name">
                                    <div class="text-danger small" id="addBankNameError"></div>
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="add_account_number" class="form-label">Account Number</label>
                                    <input type="text" class="form-control" id="add_account_number"
                                        name="account_number">
                                    <div class="text-danger small" id="addAccountNumberError"></div>
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="add_ifsc_code" class="form-label">IFSC Code</label>
                                    <input type="text" class="form-control" id="add_ifsc_code" name="ifsc_code">
                                    <div class="text-danger small" id="addIfscCodeError"></div>
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="add_branch_name" class="form-label">Branch Name</label>
                                    <input type="text" class="form-control" id="add_branch_name" name="branch_name">
                                    <div class="text-danger small" id="addBranchNameError"></div>
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="add_opening_balance" class="form-label">Opening Balance</label>
                                    <input type="number" class="form-control" id="add_opening_balance"
                                        name="opening_balance" min="0" step="0.01" value="0">
                                    <div class="text-danger small" id="addOpeningBalanceError"></div>
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="add_bank_status" class="form-label">Status</label>
                                    <select class="form-select" id="add_bank_status" name="status">
                                        <option value="1" selected>Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                    <div class="text-danger small" id="addBankStatusError"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-submit text-white" id="saveBankBtn"
                                style="background-color: #ff9f43;">Save Bank</button>
                            <button type="button" class="btn btn-secondary btn-cancel"
                                data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ===== ADD / EDIT REMARKS MODAL ===== --}}
        <div class="modal fade" id="remarksModal" tabindex="-1" aria-labelledby="remarksModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="remarksModalLabel">Add Remarks</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="remarksModalTextarea" style="font-weight: 500; color: #5e5873;">
                                Remarks <span class="text-muted" style="font-size:12px;">(Optional)</span>
                            </label>
                            <textarea id="remarksModalTextarea" class="form-control" rows="5" placeholder="Enter remarks..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn text-white" id="saveRemarksBtn"
                            style="background-color: #ff9f43;">Save Remarks</button>
                        <button type="button" class="btn btn-secondary btn-cancel"
                            data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- ===== END ADD / EDIT REMARKS MODAL ===== --}}

        <div class="d-flex align-items-center justify-content-between mb-3  responsive-mobile-view-1">

            <!-- Left Side -->

            <!-- Center / Right Controls -->
            {{-- <div class="d-flex align-items-center gap-3"> --}}

            <div class="d-flex align-items-center flex-wrap" style="gap: 12px; width: 100%;">

            </div>

        </div>

                @php
                    $user = auth()->user();
                    $subAdminId = session('selectedSubAdminId');

                    if (empty($role ?? '')) {
                        $role = Auth::user()->role ?? '';
                    }
                @endphp

        <div class="pos-bill-tabs-bar" id="posBillTabsBar">
            <div class="pos-bill-tabs" id="posBillTabs"></div>
            <button type="button" class="pos-add-bill-tab" id="posAddBillTabBtn" title="New bill (Shift + T)">+</button>
            <div style="margin-left: auto;">
                <a href="{{ route('sales.list') }}" class="btn btn-primary btn-sm" style="display: flex; align-items: center; gap: 4px; padding: 4px 10px; font-weight: 600; border-radius: 4px;">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="pos-action-toolbar" id="posActionToolbar">
            <div class="pos-action-toolbar-row">
                <div class="pos-toolbar-group pos-toolbar-modes">
                            @if ($role == 'admin' || $role == 'staff' || $role == 'sub-admin')
                        <label class="pos-toolbar-chip" for="quotationToggle2">
                            <input class="quotationToggle" type="checkbox" id="quotationToggle2"
                                        value="quotation" {{ $isQuotationMode ? 'checked' : '' }}>
                            <span>Quotation</span>
                        </label>
                            @endif
                            @if (optional(\App\Models\Setting::first())->advance_receipt)
                        <label class="pos-toolbar-chip" for="advanceReceiptToggle2">
                            <input class="advanceReceiptToggle" type="checkbox" id="advanceReceiptToggle2"
                                value="advance_receipt" {{ $isAdvanceReceiptMode ? 'checked' : '' }}>
                            <span>Advance Receipt</span>
                        </label>
                            @endif
                            @if (optional(\App\Models\Setting::first())->rental)
                        <label class="pos-toolbar-chip" for="rentalToggle2">
                            <input class="rentalToggle" type="checkbox" id="rentalToggle2"
                                        name="rental_status" value="rental" {{ $isRentalMode ? 'checked' : '' }}>
                            <span>Rental</span>
                        </label>
                            @endif
                    <label class="pos-toolbar-chip" for="wholesaleToggle2">
                        <input class="wholesaleToggle" type="checkbox" id="wholesaleToggle2" value="wholesale">
                        <span>Wholesale</span>
                    </label>
                </div>
                <div class="pos-toolbar-divider" aria-hidden="true"></div>
                <div class="pos-toolbar-group pos-toolbar-gst">
                    <div class="pos-gst-segment" role="group" aria-label="GST option">
                        <label class="pos-gst-segment-btn">
                                    <input type="radio" name="gst_option" id="without_gst" value="without" checked />
                            <span>Without GST</span>
                                </label>
                        <label class="pos-gst-segment-btn">
                                    <input type="radio" name="gst_option" id="with_gst" value="with" />
                            <span>With GST</span>
                                </label>
                            </div>
                        </div>
                <button type="button" class="pos-toolbar-more-btn" id="posToolbarMoreBtn" aria-expanded="false" aria-controls="posToolbarOrderDetails">
                    <i class="fas fa-sliders-h"></i>
                    <span>Details</span>
                    <i class="fas fa-chevron-down pos-toolbar-more-caret"></i>
                </button>
                <div class="pos-toolbar-divider" aria-hidden="true"></div>
                <div class="pos-toolbar-group pos-toolbar-order-details pos-order-row" id="posToolbarOrderDetails">
                    <div class="pos-toolbar-order-field pos-toolbar-order-field--date">
                        <label for="order_date">Order Date</label>
                        <input type="datetime-local" id="order_date" name="order_date" class="form-control"
                            value="{{ now()->format('Y-m-d\TH:i') }}" autocomplete="off">
                    </div>

                    @if (isset($staffList) && $staffList->count() > 0 && (!isset($setting) || ($setting->show_assign_staff ?? 1) == 1))
                        <div class="pos-toolbar-order-field pos-toolbar-order-field--staff">
                            <label for="assigned_staff_id">Assign Staff</label>
                            <select id="assigned_staff_id" name="assigned_staff_id" class="form-control select2">
                                <option value="">Select Staff (Optional)</option>
                                @foreach ($staffList as $staff)
                                    <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                @endforeach
                            </select>
                            </div>
                    @endif

                    @if (!isset($setting) || ($setting->show_order_type ?? 1) == 1)
                        <div class="pos-toolbar-order-field pos-toolbar-order-field--type">
                            <label for="order_type">Order Type</label>
                            <select id="order_type" name="order_type" class="form-control">
                                <option value="self_pickup" selected>Self Pickup</option>
                                <option value="delivery">Delivery</option>
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="order_type" id="order_type" value="self_pickup">
                    @endif
                    </div>
                </div>
        </div>

        <div class="row pos-mall-layout pos-layout-booting" id="posMallLayout">
            <div class="col-lg-4 col-sm-12 tabs_wrapper" id="pos-left-panel">

                @php
                    $hasProducts = false; // start as false
                @endphp

                <div class="pos-category-catalog">
                <ul class="tabs border-0 mb-4">
                    @foreach ($categories as $cat)
                        @php
                            $availableProducts = $cat->products;
                        @endphp

                        @if ($availableProducts->count() > 0)
                            @php
                                $hasProducts = true; // at least one product exists
                            @endphp
                            <li id="{{ $cat->id }}">
                                <div class="product-details">
                                    <h6 style="text-transform: capitalize;">{{ $cat->name }}</h6>
                                </div>
                            </li>
                        @endif
                    @endforeach
                </ul>

                @if (!$hasProducts)
                    <div class="product-details text-center">
                        <h6>No data available</h6>
                        <a href="{{ url('add-product') }}" class="btn btn-primary mt-2">
                            + Add Product
                        </a>
                    </div>
                @endif
                <div class="tabs_container">

                </div>
            </div>
            </div>
            <div class="pos-split-handle" id="posSplitHandle" title="Drag to resize products and bill">
                <span></span>
            </div>
            <div class="col-12" id="pos-right-panel">

                <div class="card card-order">
                    <div class="card-body body_space">
                        <div class="row">

                            <div class="col-lg-12 pos-bill-col">
                                <div class="pos-bill-scroll">
                                <!-- Barcode Scanner Modal -->
                                <div class="modal fade" id="barcodeScannerModal" tabindex="-1"
                                    aria-labelledby="barcodeScannerModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered barcode-scanner-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="barcodeScannerModalLabel">Scan Barcode</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close">x</button>
                                            </div>
                                            <div class="barcode-scanner-mobile-bar">
                                                <button type="button" class="scanner-fab scanner-fab-back" data-bs-dismiss="modal" aria-label="Back">
                                                    <i class="fas fa-arrow-left"></i>
                                                </button>
                                                <button type="button" class="scanner-fab scanner-fab-torch" id="scannerTorchBtn" aria-label="Flashlight">
                                                    <i class="fas fa-bolt"></i>
                                                </button>
                                                <button type="button" class="scanner-fab scanner-fab-done" data-bs-dismiss="modal" aria-label="Done">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </div>
                                            <div class="modal-body barcode-scanner-body">
                                                <div class="barcode-scanner-layout">
                                                    <div class="barcode-scanner-pane">
                                                <div class="scanner-viewfinder">
                                                    <div id="qr-reader" style="width:100%; max-width: 400px; margin: 0 auto 20px auto; border-radius: 8px; overflow: hidden; display: none;"></div>
                                                    <div class="scanner-frame" aria-hidden="true">
                                                        <span class="scanner-redline"></span>
                                                    </div>
                                                </div>
                                                <div id="scan-message" class="text-muted mb-3">Initializing camera...</div>
                                                        <p class="text-muted small mb-2" id="scan-keep-open-hint">Scanner stays open. Scan more products, then click Close.</p>
                                                <div class="barcode-scanner-manual">
                                                <p class="text-muted mb-4" id="manual_entry_text_pos">No camera found on this device.<br>Enter barcode manually below:</p>
                                                <div class="d-flex justify-content-center align-items-center mb-3">
                                                    <input type="text" id="manualBarcodeInput" class="form-control" placeholder="Enter barcode / product code" style="max-width: 250px; margin-right: 10px;" autocomplete="off" autofocus>
                                                    <button type="button" id="manualBarcodeSubmit" class="btn text-white" style="background-color: #ff9f43; white-space: nowrap;">Search</button>
                                                </div>
                                                <div id="manualBarcodeError" class="text-danger mt-2 small"></div>
                                                </div>
                                                    </div>
                                                    <div class="barcode-scanner-cart">
                                                        <div class="barcode-scanner-sheet-handle" role="button" tabindex="0" aria-label="Expand scanned items"></div>
                                                        <div class="barcode-scanner-cart-header">
                                                            Scanned items (<span id="scannerCartCount">0</span>)
                                                        </div>
                                                        <div class="barcode-scanner-cart-cols">
                                                            <span>NAME</span>
                                                            <span>QUANTITY</span>
                                                        </div>
                                                        <div id="scannerCartList" class="barcode-scanner-cart-list">
                                                            <div class="barcode-scanner-empty">Scan a product to add it here</div>
                                                        </div>
                                                        <div class="barcode-scanner-cart-footer">
                                                            Total qty: <span id="scannerCartQty">0</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer" style="justify-content: flex-start;">
                                                <button type="button" class="btn btn-dark"
                                                    data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <style>
                                    .pos-gst-input-wrap {
                                        position: relative;
                                    }

                                    .pos-gst-loader {
                                        position: absolute;
                                        right: 10px;
                                        top: 50%;
                                        transform: translateY(-50%);
                                        display: none;
                                        color: #ff9f43;
                                        pointer-events: none;
                                        z-index: 5;
                                    }

                                    #pos_gst_number.loading {
                                        padding-right: 36px;
                                    }

                                    .pos-gst-msg {
                                        font-size: 12px;
                                        margin-top: 3px;
                                    }

                                    .customer-section-title {
                                        font-size: 13px;
                                        font-weight: 600;
                                        color: #333;
                                        margin-bottom: 4px !important;
                                        padding: 0 !important;
                                        display: flex;
                                        align-items: center;
                                        gap: 6px;
                                    }

                                    .customer-section-title .customer-action-btns .btn {
                                        width: 26px !important;
                                        height: 26px !important;
                                    }

                                    .customer-details-box {
                                        border: 1px solid #ff9f43;
                                        border-radius: 8px;
                                        padding: 6px 8px 4px !important;
                                        background: #fff;
                                        margin-bottom: 6px !important;
                                    }

                                    .customer-details-box label {
                                        display: flex;
                                        align-items: center;
                                        font-weight: 500;
                                        font-size: 11px;
                                        color: #5e5873;
                                        margin-bottom: 2px !important;
                                        line-height: 1.2;
                                    }

                                    .customer-details-box .form-control {
                                        background-color: #f8f9fa !important;
                                        border: 1px solid #e0e0e0;
                                        border-radius: 6px;
                                        color: #333;
                                        height: 32px !important;
                                        min-height: 32px !important;
                                        padding: 2px 8px !important;
                                        font-size: 13px;
                                    }

                                    /* Fix select2 height and background */
                                    .customer-details-box .select2-container .select2-selection--single {
                                        background-color: #f8f9fa !important;
                                        border: 1px solid #e0e0e0;
                                        border-radius: 6px;
                                        height: 32px !important;
                                        min-height: 32px !important;
                                    }

                                    .customer-details-box .select2-container--default .select2-selection--single .select2-selection__rendered {
                                        line-height: 30px !important;
                                        padding-left: 8px;
                                        font-size: 13px;
                                    }

                                    .customer-details-box .select2-container--default .select2-selection--single .select2-selection__arrow {
                                        height: 30px !important;
                                    }

                                    .customer-details-box .pos-customer-row {
                                        margin: 0 !important;
                                        gap: 6px !important;
                                    }

                                    .customer-details-box .pos-customer-field {
                                        margin-bottom: 0 !important;
                                        padding-left: 4px !important;
                                        padding-right: 4px !important;
                                    }

                                    #mobile-customer-container.mb-3 {
                                        margin-bottom: 6px !important;
                                    }

                                    /* Bill layout: items left | customer+shipping+totals right (separate scrolls) */
                                    .pos-bill-workspace {
                                        display: grid;
                                        grid-template-columns: minmax(0, 1.55fr) minmax(300px, 0.95fr);
                                        grid-template-areas: "items side";
                                        gap: 12px 16px;
                                        align-items: stretch;
                                        width: 100%;
                                        margin-top: 4px;
                                        flex: 1 1 auto;
                                        min-height: 0;
                                        height: 100%;
                                        max-height: none;
                                    }
                                    .pos-bill-area-items {
                                        grid-area: items;
                                        min-width: 0;
                                        min-height: 0;
                                        height: 100%;
                                        overflow: hidden;
                                        display: flex;
                                        flex-direction: column;
                                    }
                                    .pos-bill-area-side {
                                        grid-area: side;
                                        min-width: 0;
                                        min-height: 0;
                                        height: 100%;
                                        overflow-x: hidden;
                                        overflow-y: auto;
                                        display: flex;
                                        flex-direction: column;
                                        gap: 8px;
                                        padding-right: 4px;
                                        -webkit-overflow-scrolling: touch;
                                    }
                                    .pos-bill-area-side .pos-bill-area-totals,
                                    .pos-bill-area-side .setvalue {
                                        min-width: 0;
                                        width: 100%;
                                        margin: 0 !important;
                                        border: 1px solid #ece9f1;
                                        border-radius: 8px;
                                        background: #fff;
                                        padding: 14px 16px !important;
                                        box-sizing: border-box;
                                        flex-shrink: 0;
                                    }
                                    .pos-bill-area-side .setvalue ul {
                                        margin: 0 !important;
                                        padding: 0 !important;
                                    }
                                    .pos-bill-area-side .setvalue ul li {
                                        justify-content: space-between;
                                        align-items: center;
                                        gap: 10px;
                                        padding: 8px 4px !important;
                                    }
                                    /* Keep hidden summary rows hidden (GST/shipping/labour only when applied) */
                                    .pos-bill-area-side .setvalue ul li[style*="display: none"],
                                    .pos-bill-area-side .setvalue ul li[style*="display:none"] {
                                        display: none !important;
                                    }
                                    .pos-bill-area-side .setvalue ul li:not([style*="display: none"]):not([style*="display:none"]) {
                                        display: flex !important;
                                    }
                                    .pos-bill-area-side .setvalue ul li h5,
                                    .pos-bill-area-side .setvalue ul li h6 {
                                        margin: 0 !important;
                                    }
                                    .pos-bill-area-side .customer-section-title {
                                        margin-bottom: 2px !important;
                                        flex-shrink: 0;
                                    }
                                    .pos-bill-area-side .customer-details-box {
                                        flex-shrink: 0;
                                    }
                                    .pos-bill-area-side .customer-details-box .pos-customer-row {
                                        display: grid !important;
                                        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                                        gap: 8px !important;
                                        align-items: end;
                                    }
                                    .pos-bill-area-side .customer-details-box .pos-customer-field,
                                    .pos-bill-area-side .customer-details-box .pos-customer-field--name {
                                        width: 100% !important;
                                        max-width: 100% !important;
                                        flex: none !important;
                                        padding-left: 0 !important;
                                        padding-right: 0 !important;
                                        grid-column: auto !important;
                                    }
                                    .pos-bill-area-side .customer-details-box .pos-customer-field--name {
                                        grid-column: 1 / 2 !important;
                                    }
                                    .pos-bill-area-side .customer-details-box .pos-customer-field:not(.pos-customer-field--name):not(#customer_gst_field) {
                                        grid-column: 2 / 3 !important;
                                    }
                                    .pos-bill-area-side .customer-details-box #customer_gst_field {
                                        grid-column: 1 / -1 !important;
                                        width: 100% !important;
                                        max-width: 100% !important;
                                    }
                                    .pos-bill-area-side .customer-details-box .pos-customer-field--name .select2-container,
                                    .pos-bill-area-side .customer-details-box .pos-customer-field--name .form-control,
                                    .pos-bill-area-side .customer-details-box .pos-customer-field .form-control {
                                        width: 100% !important;
                                    }
                                    .pos-bill-area-side .body_space_two {
                                        border: 1px solid #ece9f1;
                                        border-radius: 8px;
                                        padding: 10px 12px !important;
                                        background: #fff;
                                        flex-shrink: 0;
                                        min-width: 0;
                                        overflow-x: hidden;
                                    }
                                    .pos-bill-area-side #exchange_charges_wrapper,
                                    .pos-bill-area-side #exchange_charges_wrapper .card,
                                    .pos-bill-area-side #exchange_rows_container,
                                    .pos-bill-area-side .exchange-row {
                                        width: 100% !important;
                                        max-width: 100% !important;
                                        min-width: 0;
                                        margin-left: 0 !important;
                                        margin-right: 0 !important;
                                    }
                                    .pos-bill-area-side .pos-bill-extras-actions {
                                        display: flex;
                                        flex-wrap: wrap;
                                        align-items: center;
                                        gap: 8px;
                                        width: 100%;
                                    }
                                    .pos-bill-area-side .pos-bill-extra-chip {
                                        display: inline-flex;
                                        align-items: center;
                                        gap: 6px;
                                        flex: 0 0 auto;
                                        min-width: 0;
                                        padding: 4px 6px 4px 8px;
                                        border: 1px solid #ece9f1;
                                        border-radius: 8px;
                                        background: #fafafa;
                                    }
                                    .pos-bill-area-side .pos-bill-extra-chip > span {
                                        font-size: 12px;
                                        font-weight: 600;
                                        color: #5e5873;
                                        white-space: nowrap;
                                        line-height: 1.2;
                                    }
                                    .pos-bill-area-side .pos-bill-extra-btn {
                                        border-radius: 6px !important;
                                        width: 26px !important;
                                        height: 26px !important;
                                        min-width: 26px !important;
                                        padding: 0 !important;
                                        display: inline-flex !important;
                                        align-items: center;
                                        justify-content: center;
                                        background: #fff !important;
                                        border-color: #d8d6de !important;
                                        box-shadow: 0 1px 2px rgba(0,0,0,0.04);
                                        flex-shrink: 0;
                                    }
                                    .pos-bill-area-side .pos-bill-extra-btn i {
                                        font-size: 11px;
                                    }
                                    .pos-bill-area-side .pos-bill-shipping-label {
                                        display: block;
                                        font-size: 12px;
                                        font-weight: 600;
                                        color: #5e5873;
                                        margin: 2px 0 6px !important;
                                    }
                                    .pos-bill-area-side .pos-bill-extras #shipping,
                                    .pos-bill-area-side .pos-bill-extras #additional_discount_percent,
                                    .pos-bill-area-side .pos-bill-extras #additional_discount_amount {
                                        height: 36px;
                                    }
                                    .pos-bill-area-side .pos-bill-cost-row {
                                        display: flex;
                                        gap: 8px;
                                        align-items: flex-end;
                                        flex-wrap: wrap;
                                    }
                                    .pos-bill-area-side .pos-bill-cost-row > .pos-bill-cost-field {
                                        flex: 1 1 120px;
                                        min-width: 0;
                                    }
                                    .pos-bill-area-side .pos-bill-cost-row .pos-bill-discount-inputs {
                                        display: flex;
                                        gap: 6px;
                                        align-items: center;
                                    }
                                    .pos-bill-area-side .pos-bill-cost-row .pos-bill-discount-inputs .input-group {
                                        height: 36px;
                                    }
                                    .pos-bill-area-side .pos-bill-cost-row .pos-bill-discount-inputs .input-group-text {
                                        padding: 0 8px;
                                        font-size: 12px;
                                        background: #f8f8f8;
                                    }
                                    .pos-bill-area-side .pos-bill-cost-row .pos-bill-discount-pct {
                                        flex: 0 0 88px;
                                        max-width: 88px;
                                    }
                                    .pos-bill-area-side .pos-bill-cost-row .pos-bill-discount-amt {
                                        flex: 1 1 auto;
                                        min-width: 0;
                                    }
                                    .pos-bill-area-items .pos-bill-items {
                                        border: 1px solid #ece9f1;
                                        border-radius: 8px;
                                        padding: 12px 14px 14px !important;
                                        background: #fff;
                                        box-sizing: border-box;
                                        flex: 1 1 auto;
                                        min-height: 0;
                                        height: 100%;
                                        display: flex;
                                        flex-direction: column;
                                        overflow: hidden;
                                    }
                                    .pos-bill-area-items .totalitem {
                                        gap: 12px !important;
                                        margin-bottom: 12px !important;
                                        padding: 2px 0 4px !important;
                                        flex-shrink: 0;
                                    }
                                    .pos-bill-area-items .totalitem h4 {
                                        margin: 0 4px 0 0 !important;
                                        padding: 6px 12px !important;
                                    }
                                    .pos-bill-area-items .totalitem .pos-scan-btn {
                                        margin-left: 2px !important;
                                    }
                                    .pos-bill-area-items .pos-bill-grid {
                                        flex: 1 1 auto;
                                        min-height: 0;
                                        display: flex;
                                        flex-direction: column;
                                        overflow: hidden;
                                    }
                                    .pos-bill-area-items .pos-bill-head {
                                        flex-shrink: 0;
                                    }
                                    .pos-bill-area-items .product-table {
                                        flex: 1 1 auto;
                                        min-height: 0;
                                        max-height: none !important;
                                        overflow-x: hidden !important;
                                        overflow-y: auto !important;
                                        -webkit-overflow-scrolling: touch;
                                    }
                                    .pos-bill-area-items .product-table,
                                    .pos-bill-area-side {
                                        scrollbar-width: thin;
                                        scrollbar-color: #ff9f43 #f1f1f1;
                                    }
                                    .pos-bill-area-items .product-table::-webkit-scrollbar,
                                    .pos-bill-area-side::-webkit-scrollbar {
                                        width: 6px;
                                    }
                                    .pos-bill-area-items .product-table::-webkit-scrollbar-thumb,
                                    .pos-bill-area-side::-webkit-scrollbar-thumb {
                                        background: #ff9f43;
                                        border-radius: 4px;
                                    }
                                    @media (max-width: 991px) {
                                        .pos-bill-workspace {
                                            grid-template-columns: 1fr;
                                            grid-template-areas:
                                                "side"
                                                "items";
                                            height: auto;
                                            max-height: none;
                                            min-height: 0;
                                        }
                                        .pos-bill-area-items,
                                        .pos-bill-area-side {
                                            height: auto;
                                            max-height: none;
                                        }
                                        .pos-bill-area-side {
                                            overflow: visible;
                                        }
                                        .pos-bill-area-items .pos-bill-items {
                                            height: auto;
                                            max-height: 50vh;
                                        }
                                        .pos-bill-area-items .product-table {
                                            max-height: 36vh !important;
                                        }
                                    }
                                </style>
                                <style>
                                    .pos-toolbar-more-btn,
                                    .product-lists .pos-del-icon {
                                        display: none;
                                    }

                                    @media (min-width: 992px) {
                                        .pos-bill-workspace > #mobile-customer-container {
                                            display: none !important;
                                        }
                                    }

                                    @media (max-width: 991.98px) {
                                        #pos-right-panel .pos-bill-workspace {
                                            grid-template-areas:
                                                "cust"
                                                "items"
                                                "side";
                                        }
                                        .pos-bill-workspace > #mobile-customer-container {
                                            grid-area: cust;
                                            margin-bottom: 0 !important;
                                        }
                                        .pos-bill-workspace > #mobile-customer-container:empty {
                                            display: none !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .totalitem {
                                            display: flex !important;
                                            flex-wrap: nowrap !important;
                                            align-items: center !important;
                                            gap: 8px !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .totalitem h4 {
                                            flex: 0 1 auto !important;
                                            min-width: 0;
                                            margin: 0 auto 0 0 !important;
                                            overflow: hidden;
                                            text-overflow: ellipsis;
                                        }
                                        #pos-right-panel .pos-bill-area-items .totalitem .pos-bill-search-wrap {
                                            flex: 0 0 auto !important;
                                            width: auto !important;
                                            max-width: none !important;
                                            margin: 0 !important;
                                            padding: 0 !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .totalitem .pos-bill-clear-wrap {
                                            flex: 0 0 auto !important;
                                            width: auto !important;
                                            margin: 0 !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .totalitem .clear_items {
                                            display: inline-flex;
                                            align-items: center;
                                            height: 34px;
                                            padding: 0 4px;
                                            white-space: nowrap;
                                        }
                                    }

                                    /* Phone checkout: items first, compact settings, fixed pay bar */
                                    @media (max-width: 991.98px) {
                                        body:has(#posMallLayout) .mobile-bottom-nav {
                                            display: none !important;
                                        }
                                        body:has(#posMallLayout) {
                                            padding-bottom: 0 !important;
                                        }
                                        body:has(#posMallLayout) .page-wrapper .content {
                                            padding-bottom: calc(var(--pos-pay-h, 150px) + 16px) !important;
                                        }
                                        body:has(#posMallLayout) .floating-calculator-wrapper {
                                            bottom: calc(var(--pos-pay-h, 150px) + 16px) !important;
                                            right: 0 !important;
                                        }

                                        /* Bill settings: modes + GST on top, the rest behind "Details" */
                                        #posActionToolbar .pos-action-toolbar-row {
                                            gap: 6px;
                                            padding: 8px;
                                        }
                                        #posActionToolbar .pos-toolbar-modes {
                                            flex: 1 1 100%;
                                        }
                                        #posActionToolbar .pos-toolbar-gst {
                                            flex: 1 1 auto;
                                        }
                                        #posActionToolbar .pos-toolbar-more-btn {
                                            display: inline-flex;
                                            align-items: center;
                                            gap: 6px;
                                            flex: 0 0 auto;
                                            margin-left: auto;
                                            height: 30px;
                                            padding: 0 10px;
                                            border: 1px solid #e0e0e0;
                                            border-radius: 999px;
                                            background: #fff;
                                            color: #1b2850;
                                            font-size: 12px;
                                            font-weight: 600;
                                        }
                                        #posActionToolbar .pos-toolbar-more-btn .pos-toolbar-more-caret {
                                            font-size: 10px;
                                            transition: transform .15s ease;
                                        }
                                        #posActionToolbar.is-details-open .pos-toolbar-more-btn {
                                            border-color: #ff9f43;
                                            color: #ff9f43;
                                        }
                                        #posActionToolbar.is-details-open .pos-toolbar-more-caret {
                                            transform: rotate(180deg);
                                        }
                                        #posActionToolbar .pos-toolbar-order-details {
                                            display: none !important;
                                        }
                                        #posActionToolbar.is-details-open .pos-toolbar-order-details {
                                            display: grid !important;
                                            grid-template-columns: repeat(2, minmax(0, 1fr));
                                            gap: 8px;
                                            flex: 1 1 100%;
                                            padding-top: 8px;
                                            border-top: 1px dashed #e5e7eb;
                                        }
                                        #posActionToolbar.is-details-open .pos-toolbar-order-field--date {
                                            grid-column: 1 / -1;
                                        }
                                        #posActionToolbar .pos-toolbar-order-field {
                                            max-width: none !important;
                                            min-width: 0;
                                        }
                                        #posActionToolbar .pos-toolbar-order-field .form-control,
                                        #posActionToolbar .pos-toolbar-order-field .select2-container {
                                            width: 100% !important;
                                        }

                                        /* Items list flows with the page instead of a tiny inner scroll box */
                                        #pos-right-panel .pos-bill-workspace {
                                            gap: 10px;
                                        }
                                        #pos-right-panel .pos-bill-area-items .pos-bill-items {
                                            max-height: none !important;
                                            height: auto !important;
                                            overflow: visible !important;
                                            padding: 10px !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .pos-bill-grid {
                                            overflow: visible !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .product-table {
                                            max-height: none !important;
                                            overflow: visible !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .totalitem {
                                            margin-bottom: 8px !important;
                                        }

                                        /* Edge-to-edge layout: drop the nested side padding */
                                        body:has(#posMallLayout) .page-wrapper .content {
                                            padding-left: 6px !important;
                                            padding-right: 6px !important;
                                        }
                                        #posMallLayout,
                                        #posMallLayout > .row,
                                        #pos-right-panel,
                                        #pos-right-panel .card-order,
                                        #pos-right-panel .card-order > .card-body,
                                        #pos-right-panel .card-order > .card-body > .row,
                                        #pos-right-panel .pos-bill-col,
                                        #pos-right-panel .pos-bill-scroll {
                                            margin-left: 0 !important;
                                            margin-right: 0 !important;
                                            padding-left: 0 !important;
                                            padding-right: 0 !important;
                                        }
                                        #pos-right-panel .card-order {
                                            border: 0 !important;
                                            box-shadow: none !important;
                                            background: transparent !important;
                                        }
                                        #pos-right-panel .card-order > .card-body {
                                            padding-top: 0 !important;
                                        }
                                        #pos-right-panel .pos-bill-workspace {
                                            padding: 0 !important;
                                            margin: 0 !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items,
                                        #pos-right-panel .pos-bill-area-side {
                                            padding-left: 0 !important;
                                            padding-right: 0 !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .pos-bill-items {
                                            padding: 8px !important;
                                        }
                                        #posActionToolbar {
                                            margin-left: 0 !important;
                                            margin-right: 0 !important;
                                        }
                                        /* Customer name + phone side by side */
                                        #mobile-customer-container .customer-details-box {
                                            padding: 8px !important;
                                        }
                                        #mobile-customer-container .pos-customer-row {
                                            display: grid !important;
                                            grid-template-columns: minmax(0, 1.35fr) minmax(0, 1fr);
                                            gap: 8px;
                                            width: 100% !important;
                                            margin: 0 !important;
                                        }
                                        #mobile-customer-container .pos-customer-row > .pos-customer-field {
                                            width: auto !important;
                                            max-width: none !important;
                                            min-width: 0;
                                            flex: none !important;
                                            margin: 0 !important;
                                            padding: 0 !important;
                                        }
                                        #mobile-customer-container .pos-customer-field label {
                                            margin-bottom: 3px !important;
                                            font-size: 11px !important;
                                            white-space: nowrap;
                                            overflow: hidden;
                                            text-overflow: ellipsis;
                                        }
                                        #mobile-customer-container .pos-customer-field .form-control,
                                        #mobile-customer-container .pos-customer-field .select2-container {
                                            width: 100% !important;
                                            min-width: 0 !important;
                                        }
                                        #mobile-customer-container .pos-customer-field .select2-selection__rendered {
                                            white-space: nowrap;
                                            overflow: hidden;
                                            text-overflow: ellipsis;
                                        }

                                        /* Cart item card */
                                        #pos-right-panel .pos-bill-area-items .product-lists:not(.pos-bill-search-row) {
                                            display: grid !important;
                                            grid-template-columns: minmax(0, 1fr) auto !important;
                                            grid-template-areas:
                                                "details delete"
                                                "qty qty"
                                                "discount price" !important;
                                            align-items: center;
                                            gap: 10px 10px;
                                            padding: 10px !important;
                                            margin: 0 0 10px !important;
                                            border: 1px solid #e8eaef;
                                            border-radius: 12px;
                                            background: #fff;
                                            box-shadow: 0 1px 3px rgba(16, 24, 40, .06);
                                        }
                                        #pos-right-panel .pos-bill-area-items .product-lists.just-scanned {
                                            border-color: #8fd99a;
                                            background: #f3fcf4;
                                        }
                                        #pos-right-panel .pos-bill-area-items .product-lists > li {
                                            position: static !important;
                                            min-width: 0;
                                            width: auto !important;
                                            height: auto !important;
                                            margin: 0 !important;
                                            padding: 0 !important;
                                            border: 0 !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .product-lists > li.cart-col-sno {
                                            display: none !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .product-lists > li.cart-col-item {
                                            grid-area: details;
                                            display: block !important;
                                            overflow: visible;
                                        }
                                        #pos-right-panel .pos-bill-area-items .productimg {
                                            display: flex !important;
                                            align-items: center;
                                            gap: 10px;
                                            width: 100%;
                                        }
                                        #pos-right-panel .pos-bill-area-items .productimg .productimgs {
                                            flex: 0 0 44px !important;
                                            width: 44px !important;
                                            height: 44px !important;
                                            margin: 0 !important;
                                            border-radius: 8px;
                                            overflow: hidden;
                                            background: #f3f4f6;
                                        }
                                        #pos-right-panel .pos-bill-area-items .productimg .productimgs img {
                                            width: 44px !important;
                                            height: 44px !important;
                                            object-fit: cover;
                                            border-radius: 8px;
                                        }
                                        #pos-right-panel .pos-bill-area-items .productcontet {
                                            flex: 1 1 auto;
                                            min-width: 0;
                                            overflow: visible;
                                        }
                                        #pos-right-panel .pos-bill-area-items .productcontet h4 {
                                            display: flex !important;
                                            flex-wrap: wrap;
                                            align-items: center;
                                            gap: 4px 6px;
                                            min-height: 0 !important;
                                            margin: 0 !important;
                                            font-size: 13px !important;
                                            font-weight: 700;
                                            line-height: 1.3 !important;
                                            white-space: normal !important;
                                            overflow: visible !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .cart-item-name {
                                            flex: 1 1 100% !important;
                                            width: 100% !important;
                                            max-width: 100% !important;
                                            display: -webkit-box !important;
                                            -webkit-line-clamp: 2;
                                            -webkit-box-orient: vertical;
                                            overflow: hidden;
                                            white-space: normal !important;
                                            word-break: break-word;
                                        }
                                        #pos-right-panel .pos-bill-area-items .just-scanned-badge {
                                            font-size: 10px;
                                            padding: 1px 7px;
                                        }
                                        #pos-right-panel .pos-bill-area-items .productcontet h4 .edit-item {
                                            margin: 0 !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .productcontet h4 .edit-item img {
                                            width: 14px !important;
                                            height: 14px !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .product-lists > li.delete-col {
                                            grid-area: delete;
                                            align-self: start;
                                            display: flex !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .product-lists .remove-item {
                                            display: inline-flex;
                                            align-items: center;
                                            justify-content: center;
                                            width: 34px;
                                            height: 34px;
                                            border-radius: 10px;
                                            background: #fff1f1;
                                            color: #e5484d;
                                        }
                                        #pos-right-panel .pos-bill-area-items .product-lists .remove-item img {
                                            display: none !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .product-lists .pos-del-icon {
                                            display: inline-block;
                                            font-size: 14px;
                                        }

                                        #pos-right-panel .pos-bill-area-items .product-lists > li.cart-col-qty {
                                            grid-area: qty;
                                            display: block !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .cart-col-qty .increment-decrement {
                                            display: flex !important;
                                            flex-direction: row !important;
                                            flex-wrap: nowrap !important;
                                            justify-content: flex-start !important;
                                            align-items: center;
                                            gap: 8px !important;
                                            width: 100%;
                                            margin: 0 !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .increment-decrement .input-groups {
                                            display: inline-flex !important;
                                            flex: 0 0 auto;
                                            align-items: center;
                                            width: auto !important;
                                            margin: 0 !important;
                                            border: 1px solid #e0e3e8;
                                            border-radius: 10px;
                                            overflow: hidden;
                                            background: #fff;
                                        }
                                        #pos-right-panel .pos-bill-area-items .increment-decrement .input-groups input[type=button] {
                                            width: 38px !important;
                                            height: 38px !important;
                                            margin: 0 !important;
                                            border: 0 !important;
                                            border-radius: 0 !important;
                                            background: #f6f7f9 !important;
                                            color: #1b2850;
                                            font-size: 18px !important;
                                            font-weight: 700;
                                        }
                                        #pos-right-panel .pos-bill-area-items .increment-decrement .quantity-field {
                                            width: 44px !important;
                                            min-width: 44px !important;
                                            height: 38px !important;
                                            margin: 0 !important;
                                            padding: 0 !important;
                                            border: 0 !important;
                                            text-align: center;
                                            font-size: 15px !important;
                                            font-weight: 700;
                                        }
                                        #pos-right-panel .pos-bill-area-items .price-input-container {
                                            position: relative;
                                            flex: 1 1 auto;
                                            width: auto !important;
                                            min-width: 0;
                                        }
                                        #pos-right-panel .pos-bill-area-items .price-input-container::before {
                                            content: "Rate";
                                            position: absolute;
                                            left: 10px;
                                            top: 20px;
                                            transform: translateY(-50%);
                                            font-size: 11px;
                                            font-weight: 600;
                                            color: #8a92a6;
                                            pointer-events: none;
                                        }
                                        #pos-right-panel .pos-bill-area-items .product-price-input {
                                            width: 100% !important;
                                            height: 40px !important;
                                            padding: 0 10px 0 44px !important;
                                            border: 1px solid #e0e3e8 !important;
                                            border-radius: 10px !important;
                                            text-align: right;
                                            font-size: 15px !important;
                                            font-weight: 700;
                                        }

                                        #pos-right-panel .pos-bill-area-items .product-lists > li.cart-col-disc {
                                            grid-area: discount;
                                            display: block !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .product-discount-box {
                                            display: grid !important;
                                            grid-template-columns: 1fr 1fr !important;
                                            gap: 6px !important;
                                            width: 100% !important;
                                            margin: 0 !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .discount-field {
                                            display: flex !important;
                                            flex-direction: row !important;
                                            flex-wrap: nowrap !important;
                                            align-items: center !important;
                                            height: 36px !important;
                                            min-height: 36px !important;
                                            margin: 0 !important;
                                            padding: 0 8px !important;
                                            border: 1px solid #e0e3e8 !important;
                                            border-radius: 10px !important;
                                            background: #fafbfc !important;
                                            overflow: hidden;
                                        }
                                        #pos-right-panel .pos-bill-area-items .discount-field label {
                                            display: none !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .discount-field.is-percent::after {
                                            content: "%";
                                            font-size: 12px;
                                            font-weight: 700;
                                            color: #8a92a6;
                                        }
                                        #pos-right-panel .pos-bill-area-items .discount-field.is-amount::before {
                                            content: "\20B9";
                                            font-size: 12px;
                                            font-weight: 700;
                                            color: #8a92a6;
                                        }
                                        #pos-right-panel .pos-bill-area-items .product-discount-percentage,
                                        #pos-right-panel .pos-bill-area-items .product-discount-amount {
                                            flex: 1 1 auto !important;
                                            width: 100% !important;
                                            min-width: 0 !important;
                                            height: 100% !important;
                                            padding: 0 4px !important;
                                            border: 0 !important;
                                            background: transparent !important;
                                            box-shadow: none !important;
                                            text-align: center;
                                            font-size: 14px !important;
                                            font-weight: 600;
                                        }
                                        #pos-right-panel .pos-bill-area-items .discount-field:focus-within {
                                            border-color: #ff9f43 !important;
                                            background: #fff !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .product-lists > li.price {
                                            grid-area: price;
                                            display: flex !important;
                                            flex-direction: column;
                                            align-items: flex-end;
                                            justify-content: center;
                                            gap: 2px;
                                            min-width: 84px;
                                            background: transparent !important;
                                            border-radius: 0 !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .price-row {
                                            display: flex;
                                            margin: 0 !important;
                                            padding: 0 !important;
                                            border: 0 !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .price-row.sub-total {
                                            display: none !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .price-row.final-total {
                                            flex-direction: column;
                                            align-items: flex-end;
                                            gap: 0;
                                            line-height: 1.15;
                                        }
                                        #pos-right-panel .pos-bill-area-items .price-row.final-total span:first-child {
                                            display: block !important;
                                            font-size: 10px;
                                            font-weight: 600;
                                            color: #8a92a6;
                                            text-transform: uppercase;
                                            letter-spacing: .03em;
                                        }
                                        #pos-right-panel .pos-bill-area-items .price-row.final-total span:last-child {
                                            font-size: 16px;
                                            font-weight: 800;
                                            color: #1a9e4b;
                                        }
                                        #pos-right-panel .pos-bill-area-items .price-row.gst-inc {
                                            font-size: 10px;
                                            color: #9a5b00;
                                        }
                                        #pos-right-panel .pos-bill-search-input {
                                            height: 44px !important;
                                            font-size: 15px !important;
                                        }
                                        #pos-right-panel .pos-bill-area-side .body_space_two,
                                        #pos-right-panel .pos-bill-area-side .setvalue {
                                            padding: 10px !important;
                                        }
                                        #pos-right-panel .pos-bill-area-side .pos-bill-extra-chip {
                                            padding: 6px 6px 6px 10px;
                                        }
                                        #pos-right-panel .pos-bill-area-side .pos-bill-extra-btn {
                                            width: 30px !important;
                                            height: 30px !important;
                                            min-width: 30px !important;
                                        }

                                        /* Pay bar: pinned to the bottom, tap the handle to hide/show payment options */
                                        #pos-right-panel #paymentPanel {
                                            position: fixed !important;
                                            top: auto !important;
                                            bottom: 0 !important;
                                            left: 0 !important;
                                            right: 0 !important;
                                            padding-bottom: env(safe-area-inset-bottom, 0px);
                                            border-radius: 14px 14px 0 0;
                                        }
                                        #paymentPanel #paymentPanelHandle {
                                            display: flex !important;
                                            padding: 6px 12px 2px;
                                            background: #fff;
                                            border-bottom: 0;
                                            cursor: pointer;
                                        }
                                        #paymentPanel #paymentPanelHandle .handle-hint {
                                            display: none !important;
                                        }
                                        #paymentPanel #paymentSection ul {
                                            gap: 6px;
                                            padding: 4px 10px 6px;
                                        }
                                        #paymentPanel #paymentSection ul li {
                                            flex: 0 0 auto;
                                            min-width: 0;
                                        }
                                        #paymentPanel #paymentSection ul li a {
                                            flex-direction: row;
                                            gap: 6px;
                                            min-width: 0;
                                            min-height: 36px;
                                            padding: 6px 12px;
                                            font-size: 12px;
                                            border-radius: 999px;
                                            white-space: nowrap;
                                        }
                                        #paymentPanel #paymentSection ul li a i {
                                            display: inline-block !important;
                                            font-size: 13px !important;
                                            margin: 0 !important;
                                        }
                                        #paymentPanel.pos-pay-collapsed #paymentSection {
                                            display: none !important;
                                        }
                                        #paymentPanel .btn-totallabel {
                                            margin: 2px 10px 8px !important;
                                            min-height: 48px;
                                            padding: 8px 14px !important;
                                        }
                                    }

                                    /* Phone only: billed-item cards */
                                    @media screen and (max-width: 767.98px) {
                                        #pos-right-panel .pos-bill-area-items .product-lists:not(.pos-bill-search-row) {
                                            display: grid !important;
                                            grid-template-columns: 32px minmax(0, 1fr) auto 24px !important;
                                            grid-template-areas:
                                                "sno details amt delete"
                                                "qty qty qty qty"
                                                "discount discount discount discount" !important;
                                            align-items: start !important;
                                            gap: 8px 8px !important;
                                            padding: 12px 12px 10px !important;
                                            margin: 0 0 10px !important;
                                            border: 0 !important;
                                            border-radius: 14px !important;
                                            background: #eef4fb !important;
                                            box-shadow: none !important;
                                            overflow: hidden !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .product-lists:not(.pos-bill-search-row) > li.cart-col-sno {
                                            display: flex !important;
                                            grid-area: sno !important;
                                            align-items: center;
                                            justify-content: center;
                                            position: static !important;
                                            left: auto !important;
                                            top: auto !important;
                                            width: 28px !important;
                                            height: 28px !important;
                                            margin: 0 !important;
                                            padding: 0 !important;
                                            border-radius: 50%;
                                            background: #3b82f6;
                                            color: #fff !important;
                                            font-size: 11px !important;
                                            font-weight: 700;
                                            line-height: 1;
                                        }
                                        #pos-right-panel .pos-bill-area-items .product-lists:not(.pos-bill-search-row) > li.cart-col-sno::before {
                                            content: "#";
                                        }
                                        #pos-right-panel .pos-bill-area-items .product-lists:not(.pos-bill-search-row) > li.cart-col-item {
                                            grid-area: details !important;
                                            min-width: 0 !important;
                                            padding: 0 !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .product-lists:not(.pos-bill-search-row) > li.cart-col-qty {
                                            grid-area: qty !important;
                                            display: flex !important;
                                            flex-wrap: wrap;
                                            align-items: center;
                                            justify-content: space-between;
                                            gap: 4px 8px;
                                            width: 100% !important;
                                            padding: 0 2px !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .product-lists:not(.pos-bill-search-row) > li.cart-col-qty::before {
                                            content: "Item Subtotal";
                                            color: #8b9cb3;
                                            font-size: 12px;
                                            font-weight: 500;
                                            white-space: nowrap;
                                            flex: 1 1 auto;
                                            min-width: 0;
                                        }
                                        #pos-right-panel .pos-bill-area-items .product-lists:not(.pos-bill-search-row) > li.cart-col-disc {
                                            grid-area: discount !important;
                                            display: flex !important;
                                            align-items: center;
                                            justify-content: space-between;
                                            gap: 8px;
                                            width: 100% !important;
                                            padding: 0 2px 2px !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .product-lists:not(.pos-bill-search-row) > li.cart-col-disc::before {
                                            content: "Discount (%):";
                                            color: #f08a3a;
                                            font-size: 12px;
                                            font-weight: 600;
                                            white-space: nowrap;
                                        }
                                        #pos-right-panel .pos-bill-area-items .product-lists:not(.pos-bill-search-row) > li.price,
                                        #pos-right-panel .pos-bill-area-items .product-lists:not(.pos-bill-search-row) > li.cart-col-amt {
                                            grid-area: amt !important;
                                            min-width: 0 !important;
                                            width: auto !important;
                                            align-self: start !important;
                                            justify-content: flex-end !important;
                                            padding: 2px 0 0 !important;
                                            background: transparent !important;
                                            margin: 0 !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .product-lists:not(.pos-bill-search-row) > li.delete-col {
                                            grid-area: delete !important;
                                            position: static !important;
                                            top: auto !important;
                                            right: auto !important;
                                            width: 24px !important;
                                            height: 24px !important;
                                            margin: 0 !important;
                                            align-self: start !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .productimg {
                                            gap: 0 !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .productimg .productimgs {
                                            display: none !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .gst-info,
                                        #pos-right-panel .pos-bill-area-items .gst-no-message {
                                            display: none !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .productcontet h4 {
                                            display: block !important;
                                            flex-wrap: wrap !important;
                                            align-items: flex-start !important;
                                            gap: 4px !important;
                                            margin: 0 !important;
                                            font-size: 14px !important;
                                            line-height: 1.3 !important;
                                            min-height: 0 !important;
                                            overflow: visible !important;
                                            color: #1b3a6b !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .cart-item-name {
                                            flex: 1 1 auto !important;
                                            width: auto !important;
                                            max-width: 100% !important;
                                            display: inline !important;
                                            overflow: visible !important;
                                            text-overflow: unset !important;
                                            white-space: normal !important;
                                            word-break: break-word !important;
                                            -webkit-line-clamp: unset !important;
                                            font-size: 14px !important;
                                            font-weight: 700 !important;
                                            color: #1b3a6b !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .just-scanned-badge {
                                            flex: 0 0 auto;
                                            font-size: 9px;
                                            padding: 0 5px;
                                            white-space: nowrap;
                                        }
                                        #pos-right-panel .pos-bill-area-items .productcontet h4 .edit-item {
                                            display: none !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .product-lists .remove-item {
                                            width: 24px;
                                            height: 24px;
                                            border-radius: 6px;
                                            background: transparent;
                                        }
                                        #pos-right-panel .pos-bill-area-items .cart-col-qty .increment-decrement {
                                            display: flex !important;
                                            flex-direction: row !important;
                                            align-items: center !important;
                                            justify-content: flex-end !important;
                                            gap: 4px !important;
                                            width: auto !important;
                                            max-width: 100%;
                                            margin-left: auto;
                                            flex: 0 1 auto;
                                        }
                                        #pos-right-panel .pos-bill-area-items .increment-decrement .input-groups {
                                            border-radius: 8px;
                                            flex: 0 0 auto;
                                            width: auto !important;
                                            gap: 0 !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .increment-decrement .input-groups::after {
                                            content: "×";
                                            display: inline-flex;
                                            align-items: center;
                                            margin: 0 4px 0 6px;
                                            color: #8b9cb3;
                                            font-size: 13px;
                                            font-weight: 600;
                                        }
                                        #pos-right-panel .pos-bill-area-items .increment-decrement .input-groups input[type=button] {
                                            width: 26px !important;
                                            height: 26px !important;
                                            font-size: 14px !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .increment-decrement .quantity-field {
                                            width: 34px !important;
                                            min-width: 34px !important;
                                            height: 26px !important;
                                            font-size: 13px !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .increment-decrement > div:last-child {
                                            width: auto !important;
                                            order: 0 !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .price-input-container {
                                            flex: 0 0 auto;
                                            min-width: 52px;
                                            width: 58px;
                                        }
                                        #pos-right-panel .pos-bill-area-items .price-input-container::before {
                                            display: none !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .product-price-input {
                                            height: 26px !important;
                                            width: 58px !important;
                                            padding: 0 4px !important;
                                            font-size: 13px !important;
                                            border-radius: 6px !important;
                                            text-align: right;
                                            background: transparent !important;
                                            border: 0 !important;
                                            color: #8b9cb3 !important;
                                            font-weight: 600;
                                        }
                                        #pos-right-panel .pos-bill-area-items .below-purchase-warning {
                                            display: none !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .discount-field {
                                            height: 26px !important;
                                            min-height: 26px !important;
                                            padding: 0 6px !important;
                                            border-radius: 6px !important;
                                            border-color: #f3d2b3 !important;
                                            background: transparent !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .product-discount-percentage,
                                        #pos-right-panel .pos-bill-area-items .product-discount-amount {
                                            font-size: 12px !important;
                                            color: #f08a3a !important;
                                            font-weight: 600;
                                        }
                                        #pos-right-panel .pos-bill-area-items .product-discount-box {
                                            gap: 6px !important;
                                            width: auto !important;
                                            margin-left: auto;
                                        }
                                        #pos-right-panel .pos-bill-area-items .serial-no-box {
                                            width: 100%;
                                            margin-top: 6px !important;
                                            padding: 0 !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .price-row.sub-total,
                                        #pos-right-panel .pos-bill-area-items .price-row.gst-inc {
                                            display: none !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .price-row.final-total {
                                            flex-direction: column !important;
                                            align-items: flex-end !important;
                                            justify-content: flex-start !important;
                                            gap: 0 !important;
                                            height: auto;
                                            white-space: nowrap;
                                        }
                                        #pos-right-panel .pos-bill-area-items .price-row.final-total span:first-child {
                                            display: none !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .price-row.final-total span:last-child {
                                            display: block !important;
                                            font-size: 15px !important;
                                            font-weight: 700;
                                            color: #1b3a6b;
                                            line-height: 1.2;
                                            white-space: nowrap;
                                        }

                                        /* Search row: only the search + Create product, hide leftover + */
                                        #pos-right-panel .pos-bill-area-items .product-lists.pos-bill-search-row {
                                            display: flex !important;
                                            grid-template-columns: none !important;
                                            grid-template-areas: none !important;
                                            padding: 0 !important;
                                            margin: 8px 0 0 !important;
                                            border: 0 !important;
                                            background: transparent !important;
                                            box-shadow: none !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .product-lists.pos-bill-search-row > li {
                                            display: none !important;
                                        }
                                        #pos-right-panel .pos-bill-area-items .product-lists.pos-bill-search-row > li.cart-col-item,
                                        #pos-right-panel .pos-bill-area-items .product-lists.pos-bill-search-row > li.pos-bill-search-cell {
                                            display: block !important;
                                            width: 100% !important;
                                            flex: 1 1 auto;
                                        }
                                        #pos-right-panel .pos-bill-area-items .pos-bill-search-plus {
                                            display: none !important;
                                        }
                                    }

                                    /* ===== Tablet / iPad sale bill (768–1024) ===== */
                                    @media screen and (min-width: 768px) and (max-width: 1024.98px) {
                                        /* Kill the empty top spacer (fixed-header padding + leftover toolbar row) */
                                        body:has(#posMallLayout) .page-wrapper {
                                            padding-top: 0 !important;
                                        }

                                        body:has(#posMallLayout) .responsive-mobile-view-1 {
                                            display: none !important;
                                            margin: 0 !important;
                                            min-height: 0 !important;
                                            height: 0 !important;
                                            overflow: hidden !important;
                                        }

                                        body:has(#posMallLayout) .page-wrapper .content {
                                            padding: 4px 8px 8px !important;
                                            padding-bottom: calc(var(--pos-pay-h, 150px) + 12px) !important;
                                        }

                                        body:has(#posMallLayout) .pos-bill-tabs-bar {
                                            margin-top: 0 !important;
                                            margin-bottom: 4px !important;
                                        }

                                        body:has(#posMallLayout) .floating-calculator-wrapper {
                                            bottom: calc(var(--pos-pay-h, 170px) + 20px) !important;
                                            right: 12px !important;
                                        }

                                        #posMallLayout,
                                        #pos-right-panel,
                                        #pos-right-panel .card-order,
                                        #pos-right-panel .card-order > .card-body,
                                        #pos-right-panel .pos-bill-col,
                                        #pos-right-panel .pos-bill-scroll {
                                            max-width: 100% !important;
                                            width: 100% !important;
                                        }

                                        #pos-right-panel .card-order {
                                            border: 0 !important;
                                            border-radius: 0 !important;
                                            box-shadow: none !important;
                                            background: transparent !important;
                                            overflow: hidden;
                                        }

                                        #pos-right-panel .card-order > .card-body {
                                            padding: 4px 0 6px !important;
                                        }

                                        /* Toolbar: compact chips */
                                        #posActionToolbar {
                                            margin-bottom: 6px !important;
                                        }

                                        #posActionToolbar .pos-action-toolbar-row {
                                            gap: 6px;
                                            padding: 6px 8px;
                                            border-radius: 10px;
                                            min-height: 0;
                                        }

                                        #posActionToolbar .pos-toolbar-modes {
                                            flex: 1 1 auto;
                                            flex-wrap: wrap;
                                            overflow: visible;
                                            gap: 6px;
                                            max-width: none;
                                            padding-bottom: 0;
                                        }

                                        #posActionToolbar .pos-toolbar-chip {
                                            font-size: 12px !important;
                                            padding: 5px 10px !important;
                                            min-height: 32px;
                                            border-radius: 999px;
                                        }

                                        #posActionToolbar .pos-gst-segment-btn span {
                                            padding: 5px 10px !important;
                                            font-size: 13px !important;
                                            min-height: 38px;
                                            display: inline-flex;
                                            align-items: center;
                                        }

                                        #posActionToolbar .pos-toolbar-more-btn {
                                            height: 38px !important;
                                            padding: 0 14px !important;
                                            font-size: 13px !important;
                                        }

                                        #posActionToolbar.is-details-open .pos-toolbar-order-details {
                                            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
                                            gap: 10px !important;
                                        }

                                        #posBillTabsBar .pos-bill-tab {
                                            height: 38px !important;
                                            min-height: 38px !important;
                                            max-width: 180px !important;
                                            font-size: 13px !important;
                                        }

                                        .pos-add-bill-tab {
                                            width: 38px !important;
                                            height: 38px !important;
                                        }

                                        /* Dense tablet workspace: customer + items stacked tight */
                                        #pos-right-panel .pos-bill-workspace {
                                            display: grid !important;
                                            grid-template-columns: 1fr !important;
                                            grid-template-areas:
                                                "cust"
                                                "items"
                                                "side" !important;
                                            gap: 6px !important;
                                            height: auto !important;
                                            max-height: none !important;
                                            align-items: start;
                                            padding: 0 !important;
                                        }

                                        .pos-bill-workspace > #mobile-customer-container {
                                            grid-area: cust !important;
                                            margin-bottom: 0 !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items {
                                            grid-area: items !important;
                                            height: auto !important;
                                            max-height: none !important;
                                            overflow: visible !important;
                                            padding: 0 !important;
                                        }

                                        #pos-right-panel .pos-bill-area-side {
                                            grid-area: side !important;
                                            height: auto !important;
                                            max-height: none !important;
                                            overflow: visible !important;
                                            padding: 0 !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .pos-bill-items {
                                            padding: 8px !important;
                                            border-radius: 10px;
                                            border: 1px solid #e8eaef;
                                            background: #fff;
                                            max-height: none !important;
                                            height: auto !important;
                                            overflow: visible !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .product-table {
                                            max-height: none !important;
                                            overflow: visible !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .totalitem {
                                            margin-bottom: 6px !important;
                                            gap: 6px !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .totalitem h4 {
                                            font-size: 13px !important;
                                            padding: 4px 8px !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .totalitem .clear_items {
                                            height: 32px !important;
                                            font-size: 12px !important;
                                            padding: 0 6px !important;
                                        }

                                        /* Customer: compact fields */
                                        #mobile-customer-container .customer-section-title {
                                            font-size: 13px !important;
                                            margin-bottom: 4px !important;
                                        }

                                        #mobile-customer-container .customer-details-box {
                                            display: block !important;
                                            padding: 8px 10px !important;
                                            border-radius: 10px;
                                            border: 1px solid #e8eaef;
                                            background: #fff;
                                            margin-bottom: 0 !important;
                                        }

                                        #mobile-customer-container .customer-section-title.d-lg-none,
                                        #mobile-customer-container .customer-details-box.d-lg-none {
                                            display: flex !important;
                                        }

                                        #mobile-customer-container .customer-details-box.d-lg-none {
                                            display: block !important;
                                        }

                                        #mobile-customer-container .pos-customer-row,
                                        #mobile-customer-container .customer-details-box .pos-customer-row {
                                            display: grid !important;
                                            grid-template-columns: minmax(0, 1.35fr) minmax(0, 1fr) !important;
                                            gap: 8px !important;
                                            width: 100% !important;
                                            align-items: end !important;
                                            flex-wrap: nowrap !important;
                                        }

                                        #mobile-customer-container .pos-customer-row > .pos-customer-field,
                                        #mobile-customer-container .pos-customer-row > .pos-customer-field--name,
                                        #mobile-customer-container .pos-customer-row > [class*="col-"] {
                                            grid-column: auto !important;
                                            width: auto !important;
                                            max-width: none !important;
                                            min-width: 0 !important;
                                            flex: 1 1 0 !important;
                                            margin: 0 !important;
                                            padding: 0 !important;
                                        }

                                        #mobile-customer-container .pos-customer-field label {
                                            font-size: 11px !important;
                                            margin-bottom: 3px !important;
                                        }

                                        #mobile-customer-container .pos-customer-field .form-control,
                                        #mobile-customer-container .pos-customer-field .select2-container .select2-selection--single {
                                            height: 36px !important;
                                            min-height: 36px !important;
                                            font-size: 13px !important;
                                        }

                                        #mobile-customer-container .pos-customer-field .select2-container--default .select2-selection--single .select2-selection__rendered {
                                            line-height: 34px !important;
                                            font-size: 13px !important;
                                        }

                                        #mobile-customer-container .pos-customer-field .select2-container--default .select2-selection--single .select2-selection__arrow {
                                            height: 34px !important;
                                        }

                                        /* Extras chips: compact 3-col */
                                        #pos-right-panel .pos-bill-area-side .body_space_two,
                                        #pos-right-panel .pos-bill-area-side .setvalue {
                                            padding: 8px !important;
                                            border-radius: 10px;
                                            border: 1px solid #e8eaef;
                                            background: #fff;
                                        }

                                        #pos-right-panel .pos-bill-extras-actions,
                                        #pos-right-panel .pos-bill-area-side .pos-bill-extras-actions {
                                            display: grid !important;
                                            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
                                            gap: 6px !important;
                                            width: 100%;
                                        }

                                        #pos-right-panel .pos-bill-extra-chip,
                                        #pos-right-panel .pos-bill-area-side .pos-bill-extra-chip {
                                            width: 100%;
                                            justify-content: space-between;
                                            padding: 5px 6px 5px 8px !important;
                                            border-radius: 8px !important;
                                            min-height: 34px;
                                        }

                                        #pos-right-panel .pos-bill-extra-chip > span,
                                        #pos-right-panel .pos-bill-area-side .pos-bill-extra-chip > span {
                                            font-size: 11px !important;
                                        }

                                        #pos-right-panel .pos-bill-extra-btn,
                                        #pos-right-panel .pos-bill-area-side .pos-bill-extra-btn {
                                            width: 28px !important;
                                            height: 28px !important;
                                            min-width: 28px !important;
                                            border-radius: 6px !important;
                                        }

                                        #pos-right-panel .pos-bill-area-side .setvalue h5 {
                                            font-size: 12px !important;
                                        }

                                        #pos-right-panel .pos-bill-area-side .setvalue h6 {
                                            font-size: 14px !important;
                                        }

                                        /*
                                         * Tablet cart: shared fixed tracks for header + rows
                                         * so Item / Qty / Rate / Discount / Amount line up.
                                         * Qty col = qty controls (88) + gap (10) + rate (72) = 170
                                         * Disc col = % (44) + gap (4) + ₹ (50) = 98
                                         */
                                        #pos-right-panel .pos-bill-area-items .pos-bill-grid {
                                            --pos-tablet-gap: 6px;
                                            --pos-tablet-qty-rate-gap: 10px;
                                            --pos-tablet-qty: 170px;
                                            --pos-tablet-disc: 98px;
                                            --pos-tablet-amt: 72px;
                                            --pos-tablet-del: 30px;
                                            --pos-bill-cols: minmax(0, 1fr) var(--pos-tablet-qty) var(--pos-tablet-disc) var(--pos-tablet-amt) var(--pos-tablet-del);
                                        }

                                        #pos-right-panel .pos-bill-area-items .pos-bill-head {
                                            display: grid !important;
                                            grid-template-columns: var(--pos-bill-cols) !important;
                                            align-items: end !important;
                                            column-gap: var(--pos-tablet-gap) !important;
                                            row-gap: 0 !important;
                                            width: 100% !important;
                                            box-sizing: border-box !important;
                                            padding: 6px 8px 4px !important;
                                            margin: 0 0 2px !important;
                                            font-size: 10px !important;
                                            font-weight: 700 !important;
                                            letter-spacing: 0.02em !important;
                                            text-transform: none !important;
                                            color: #6b7280 !important;
                                            border-bottom: 1px solid #e5e7eb !important;
                                            flex-shrink: 0 !important;
                                            background: #f8fafc !important;
                                            border-radius: 8px 8px 0 0 !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .pos-bill-head-sno {
                                            display: none !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .pos-bill-head-item {
                                            min-width: 0 !important;
                                            align-self: end !important;
                                            padding-bottom: 2px !important;
                                            font-size: 10px !important;
                                            font-weight: 700 !important;
                                            text-transform: uppercase !important;
                                            color: #6b7280 !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .pos-bill-head-qty,
                                        #pos-right-panel .pos-bill-area-items .pos-bill-head-disc {
                                            display: block !important;
                                            width: 100% !important;
                                            min-width: 0 !important;
                                            line-height: 1.15 !important;
                                            text-align: center !important;
                                        }

                                        /* Qty/Rate stay flat; Discount keeps a title so % / ₹ are clear */
                                        #pos-right-panel .pos-bill-area-items .pos-bill-head-qty .pos-head-main {
                                            display: none !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .pos-bill-head-disc {
                                            display: flex !important;
                                            flex-direction: column !important;
                                            align-items: stretch !important;
                                            gap: 2px !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .pos-bill-head-disc .pos-head-main {
                                            display: block !important;
                                            text-align: center !important;
                                            font-size: 10px !important;
                                            font-weight: 700 !important;
                                            letter-spacing: 0.02em !important;
                                            text-transform: uppercase !important;
                                            color: #6b7280 !important;
                                            line-height: 1.2 !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .pos-bill-head .pos-head-subs {
                                            display: grid !important;
                                            width: 100% !important;
                                            gap: 4px !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .pos-bill-head-qty .pos-head-subs {
                                            grid-template-columns: minmax(0, 1fr) 72px !important;
                                            column-gap: var(--pos-tablet-qty-rate-gap) !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .pos-bill-head-disc .pos-head-subs {
                                            grid-template-columns: 44px 50px !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .pos-bill-head .pos-head-subs > span {
                                            display: block !important;
                                            text-align: center !important;
                                            font-size: 10px !important;
                                            font-weight: 700 !important;
                                            letter-spacing: 0.02em !important;
                                            text-transform: uppercase !important;
                                            color: #6b7280 !important;
                                            background: transparent !important;
                                            border: 0 !important;
                                            border-radius: 0 !important;
                                            padding: 0 0 2px !important;
                                            line-height: 1.2 !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .pos-bill-head-disc .pos-head-subs > span:first-child {
                                            color: #7367f0 !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .pos-bill-head-disc .pos-head-subs > span:last-child {
                                            color: #e08a1e !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .pos-bill-head-amt {
                                            display: flex !important;
                                            align-items: flex-end !important;
                                            justify-content: flex-end !important;
                                            width: 100% !important;
                                            text-align: right !important;
                                            align-self: end !important;
                                            padding-bottom: 2px !important;
                                            font-size: 10px !important;
                                            font-weight: 700 !important;
                                            text-transform: uppercase !important;
                                            color: #6b7280 !important;
                                            box-sizing: border-box !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .pos-bill-head-del {
                                            width: var(--pos-tablet-del) !important;
                                        }

                                        /*
                                         * Tablet cart: ONE compact row — same tracks as header above.
                                         */
                                        #pos-right-panel .pos-bill-area-items .product-lists:not(.pos-bill-search-row) {
                                            display: grid !important;
                                            grid-template-columns: var(--pos-bill-cols) !important;
                                            grid-template-areas: "details qty discount price delete" !important;
                                            align-items: center !important;
                                            column-gap: var(--pos-tablet-gap) !important;
                                            row-gap: 0 !important;
                                            padding: 6px 8px !important;
                                            margin: 0 0 6px !important;
                                            border-radius: 8px !important;
                                            border: 1px solid #e8eaef !important;
                                            background: #fff !important;
                                            box-shadow: none !important;
                                            overflow: hidden !important;
                                            min-height: 0 !important;
                                            box-sizing: border-box !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .product-lists > li.cart-col-sno {
                                            display: none !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .product-lists > li.cart-col-item {
                                            grid-area: details !important;
                                            min-width: 0 !important;
                                            max-width: none !important;
                                            width: auto !important;
                                            overflow: hidden !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .product-lists > li.cart-col-qty {
                                            grid-area: qty !important;
                                            width: 100% !important;
                                            min-width: 0 !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .product-lists > li.cart-col-disc {
                                            grid-area: discount !important;
                                            width: 100% !important;
                                            min-width: 0 !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .product-lists > li.price {
                                            grid-area: price !important;
                                            width: 100% !important;
                                            min-width: 0 !important;
                                            justify-self: stretch !important;
                                            align-items: flex-end !important;
                                            background: transparent !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .product-lists > li.delete-col {
                                            grid-area: delete !important;
                                            position: static !important;
                                            width: var(--pos-tablet-del) !important;
                                            height: 30px !important;
                                            align-self: center !important;
                                            justify-self: end !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .productimg {
                                            display: flex !important;
                                            align-items: center !important;
                                            gap: 6px !important;
                                            width: 100% !important;
                                            min-width: 0 !important;
                                        }

                                        /* Hide thumbnail on tablet — frees name space */
                                        #pos-right-panel .pos-bill-area-items .productimg .productimgs {
                                            display: none !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .productcontet {
                                            min-width: 0 !important;
                                            flex: 1 1 auto !important;
                                            overflow: hidden !important;
                                            width: 100% !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .productcontet h4 {
                                            display: flex !important;
                                            flex-wrap: nowrap !important;
                                            align-items: center !important;
                                            gap: 4px !important;
                                            margin: 0 !important;
                                            font-size: 12px !important;
                                            font-weight: 700 !important;
                                            line-height: 1.25 !important;
                                            white-space: nowrap !important;
                                            overflow: hidden !important;
                                            max-width: none !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .productcontet h4 .edit-item {
                                            display: none !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .cart-item-name {
                                            flex: 1 1 auto !important;
                                            width: auto !important;
                                            max-width: 100% !important;
                                            display: block !important;
                                            overflow: hidden !important;
                                            text-overflow: ellipsis !important;
                                            white-space: nowrap !important;
                                            word-break: normal !important;
                                            -webkit-line-clamp: unset !important;
                                            line-clamp: unset !important;
                                            font-size: 12px !important;
                                            color: #1b2850 !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .just-scanned-badge {
                                            display: none !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .cart-col-qty .increment-decrement {
                                            display: flex !important;
                                            flex-direction: row !important;
                                            flex-wrap: nowrap !important;
                                            align-items: center !important;
                                            justify-content: flex-end !important;
                                            gap: var(--pos-tablet-qty-rate-gap) !important;
                                            width: 100% !important;
                                            margin: 0 !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .increment-decrement .input-groups {
                                            display: inline-flex !important;
                                            flex: 0 0 auto !important;
                                            width: auto !important;
                                            margin: 0 !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .increment-decrement .input-groups input[type=button] {
                                            width: 28px !important;
                                            height: 28px !important;
                                            font-size: 14px !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .increment-decrement .quantity-field {
                                            width: 32px !important;
                                            min-width: 32px !important;
                                            height: 28px !important;
                                            font-size: 12px !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .price-input-container {
                                            flex: none !important;
                                            width: 72px !important;
                                            min-width: 72px !important;
                                            max-width: 72px !important;
                                            justify-self: stretch !important;
                                        }

                                        /* Header already says RATE — hide inline "Rate" prefix */
                                        #pos-right-panel .pos-bill-area-items .price-input-container::before {
                                            content: none !important;
                                            display: none !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .product-price-input {
                                            width: 100% !important;
                                            height: 28px !important;
                                            padding: 0 6px !important;
                                            font-size: 12px !important;
                                            border-radius: 6px !important;
                                            text-align: right !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .product-discount-box {
                                            display: grid !important;
                                            grid-template-columns: 44px 50px !important;
                                            gap: 4px !important;
                                            width: 100% !important;
                                            margin: 0 !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .discount-field {
                                            height: 28px !important;
                                            min-height: 28px !important;
                                            padding: 0 4px !important;
                                            border-radius: 6px !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .product-discount-percentage,
                                        #pos-right-panel .pos-bill-area-items .product-discount-amount {
                                            font-size: 11px !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .price-row.final-total span:first-child {
                                            display: none !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .price-row.final-total span:last-child {
                                            font-size: 13px !important;
                                        }

                                        #pos-right-panel .pos-bill-area-items .product-lists .remove-item {
                                            width: 28px !important;
                                            height: 28px !important;
                                        }

                                        #pos-right-panel .pos-bill-search-input {
                                            height: 36px !important;
                                            font-size: 13px !important;
                                        }

                                        /* Payment bar — slightly shorter */
                                        #pos-right-panel #paymentPanel {
                                            left: 8px !important;
                                            right: 8px !important;
                                            width: auto !important;
                                            max-width: none !important;
                                            border-radius: 12px 12px 0 0 !important;
                                            box-shadow: 0 -8px 24px rgba(15, 23, 42, 0.14) !important;
                                        }

                                        #paymentPanel #paymentSection ul {
                                            gap: 6px !important;
                                            padding: 4px 10px 6px !important;
                                            flex-wrap: wrap !important;
                                        }

                                        #paymentPanel #paymentSection ul li {
                                            flex: 1 1 calc(33.333% - 6px) !important;
                                            min-width: 90px;
                                        }

                                        #paymentPanel #paymentSection ul li a {
                                            min-height: 40px !important;
                                            width: 100%;
                                            justify-content: center;
                                            padding: 6px 10px !important;
                                            font-size: 12px !important;
                                            border-radius: 10px !important;
                                            gap: 4px !important;
                                        }

                                        #paymentPanel #paymentSection ul li a i {
                                            font-size: 14px !important;
                                        }

                                        #paymentPanel .btn-totallabel {
                                            margin: 2px 10px 8px !important;
                                            min-height: 46px !important;
                                            padding: 8px 14px !important;
                                            border-radius: 10px !important;
                                        }

                                        #paymentPanel .btn-totallabel h6,
                                        #paymentPanel .btn-totallabel h5 {
                                            font-size: 14px !important;
                                        }
                                    }

                                    /* Narrow tablet portrait: already single-column; keep extras at 3-col */
                                    @media screen and (min-width: 768px) and (max-width: 899.98px) and (orientation: portrait) {
                                        #pos-right-panel .pos-bill-extras-actions,
                                        #pos-right-panel .pos-bill-area-side .pos-bill-extras-actions {
                                            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
                                        }

                                        /* If name is long, allow 2-line clamp only */
                                        #pos-right-panel .pos-bill-area-items .cart-item-name {
                                            white-space: normal !important;
                                            display: -webkit-box !important;
                                            -webkit-line-clamp: 2 !important;
                                            -webkit-box-orient: vertical !important;
                                        }
                                    }
                                </style>

                                <div id="mobile-customer-container" class="mb-3"></div>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const mobileContainer = document.getElementById('mobile-customer-container');
                                        const desktopContainer = document.querySelector('.customer-details-box .pos-customer-row');

                                        const customerNameField = document.querySelector('.pos-customer-field--name');
                                        const phoneInput = document.getElementById('customer_phone');
                                        const customerPhoneField = phoneInput ? phoneInput.closest('.pos-customer-field') : null;

                                        const desktopTitleText = document.querySelector('.customer-section-title > div:first-child');
                                        const actionBtns = document.querySelector('.customer-action-btns');
                                        const gstField = document.getElementById('customer_gst_field');

                                        if (!customerNameField || !customerPhoneField || !desktopContainer || !mobileContainer) {
                                            // Still build desktop bill layout even if mobile rearrange can't run
                                            if (typeof window.buildPosBillWorkspace === 'function') {
                                                window.buildPosBillWorkspace();
                                            }
                                            return;
                                        }

                                        const mobileBox = document.createElement('div');
                                        mobileBox.className = 'customer-details-box d-lg-none';

                                        const mobileTitle = document.createElement('div');
                                        mobileTitle.className = 'customer-section-title d-flex d-lg-none align-items-center';
                                        mobileTitle.style.marginBottom = '4px';

                                        const mobileRow = document.createElement('div');
                                        mobileRow.className = 'row w-105 pos-customer-row m-0';

                                        mobileBox.appendChild(mobileRow);

                                        function rearrangeCustomerDetails() {
                                            const desktopDetailsBox = desktopContainer.parentElement;

                                            function updateDesktopBoxVisibility() {
                                                if (window.innerWidth <= 991) {
                                                    if (gstField && gstField.style.display !== 'none') {
                                                        desktopDetailsBox.style.display = '';
                                                    } else {
                                                        desktopDetailsBox.style.display = 'none';
                                                    }
                                                } else {
                                                    desktopDetailsBox.style.display = '';
                                                }
                                            }

                                            if (window.innerWidth <= 991) {
                                                if (!mobileRow.contains(customerNameField)) {
                                                    if (desktopTitleText) mobileTitle.appendChild(desktopTitleText);
                                                    if (actionBtns) mobileTitle.appendChild(actionBtns);

                                                    mobileContainer.appendChild(mobileTitle);
                                                    mobileContainer.appendChild(mobileBox);

                                                    mobileRow.appendChild(customerNameField);
                                                    mobileRow.appendChild(customerPhoneField);
                                                }
                                            } else {
                                                if (!desktopContainer.contains(customerNameField)) {
                                                    const desktopTitle = document.querySelector('.customer-section-title:not(.d-md-none):not(.d-lg-none)');
                                                    if (desktopTitle) {
                                                        if (actionBtns) desktopTitle.insertBefore(actionBtns, desktopTitle.querySelector('.ms-auto'));
                                                        if (desktopTitleText) desktopTitle.insertBefore(desktopTitleText, actionBtns);
                                                    }

                                                    if (gstField) {
                                                        desktopContainer.insertBefore(customerPhoneField, gstField);
                                                    } else {
                                                        desktopContainer.appendChild(customerPhoneField);
                                                    }
                                                    desktopContainer.insertBefore(customerNameField, customerPhoneField);
                                                }
                                            }

                                            updateDesktopBoxVisibility();
                                        }

                                        window.addEventListener('resize', rearrangeCustomerDetails);
                                        setTimeout(rearrangeCustomerDetails, 50);

                                        if (gstField) {
                                            const observer = new MutationObserver(function() {
                                                const desktopDetailsBox = desktopContainer.parentElement;
                                                if (window.innerWidth <= 991) {
                                                    if (gstField.style.display !== 'none') {
                                                        desktopDetailsBox.style.display = '';
                                                    } else {
                                                        desktopDetailsBox.style.display = 'none';
                                                    }
                                                }
                                            });
                                            observer.observe(gstField, { attributes: true, attributeFilter: ['style'] });
                                        }

                                        if (typeof window.buildPosBillWorkspace === 'function') {
                                            window.buildPosBillWorkspace();
                                        }
                                    });

                                    // Desktop bill layout: items left, customer+shipping+totals right
                                    window.markPosBillLayoutReady = function markPosBillLayoutReady() {
                                        var layout = document.getElementById('posMallLayout');
                                        if (!layout) return;
                                        layout.classList.remove('pos-layout-booting');
                                        layout.classList.add('pos-layout-ready');
                                    };

                                    window.buildPosBillWorkspace = function buildPosBillWorkspace() {
                                        if (document.querySelector('.pos-bill-workspace')) {
                                            window.markPosBillLayoutReady();
                                            return true;
                                        }

                                        var items = document.querySelector('.pos-bill-items');
                                        var custTitle = document.querySelector('.customer-section-title:not(.d-md-none):not(.d-lg-none)');
                                        var custBox = document.querySelector('.customer-details-box:not(.d-md-none):not(.d-lg-none)');
                                        if (!custBox) {
                                            custBox = document.querySelector('#pos-right-panel .customer-details-box');
                                        }
                                        var bodyTwo = document.querySelector('.body_space_two');
                                        var setvalue = document.querySelector('#pos-right-panel .setvalue');
                                        var payment = document.getElementById('paymentPanel');
                                        var host = document.querySelector('#pos-right-panel .pos-bill-scroll')
                                            || (payment && payment.parentNode)
                                            || document.querySelector('#pos-right-panel .card-order .card-body');

                                        if (!host || !items || !custBox || !bodyTwo || !setvalue) return false;

                                        var workspace = document.createElement('div');
                                        workspace.className = 'pos-bill-workspace';

                                        var main = document.createElement('div');
                                        main.className = 'pos-bill-area-items';
                                        main.appendChild(items);

                                        var side = document.createElement('div');
                                        side.className = 'pos-bill-area-side';
                                        if (custTitle) side.appendChild(custTitle);
                                        side.appendChild(custBox);
                                        side.appendChild(bodyTwo);

                                        var totals = document.createElement('div');
                                        totals.className = 'pos-bill-area-totals';
                                        totals.appendChild(setvalue);
                                        side.appendChild(totals);

                                        var mobileCustomer = document.getElementById('mobile-customer-container');
                                        if (mobileCustomer) workspace.appendChild(mobileCustomer);
                                        workspace.appendChild(main);
                                        workspace.appendChild(side);

                                        // Keep workspace + payment as the only visible flex children
                                        // (leftover empty nodes above were creating the big blank gap)
                                        host.insertBefore(workspace, host.firstChild);
                                        if (payment) {
                                            host.appendChild(payment);
                                        }
                                        host.classList.add('pos-has-workspace');

                                        Array.prototype.forEach.call(host.children, function(child) {
                                            if (child === workspace || child === payment) return;
                                            if (child.classList && child.classList.contains('modal')) return;
                                            if (child.id === 'mobile-customer-container') return;
                                            if (child.tagName === 'STYLE' || child.tagName === 'SCRIPT' || child.tagName === 'TEXTAREA') {
                                                return;
                                            }
                                            // Hide empty wrappers left after moving bill sections
                                            child.classList.add('pos-bill-scroll-hidden');
                                        });

                                        window.markPosBillLayoutReady();
                                        return true;
                                    };

                                    document.addEventListener('DOMContentLoaded', function() {
                                        if (typeof window.buildPosBillWorkspace === 'function') {
                                            window.buildPosBillWorkspace();
                                        }
                                        // Failsafe: never leave the bill panel invisible
                                        setTimeout(function() {
                                            if (typeof window.markPosBillLayoutReady === 'function') {
                                                window.markPosBillLayoutReady();
                                            }
                                        }, 600);
                                    });
                                </script>

                                <div class="customer-section-title"
                                    style="display: flex; align-items: center; margin-bottom: 4px; padding: 0;">
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <i class="fas fa-user-friends" style="color: #ff9f43;"></i> Customer details
                                    </div>
                                    <div class="customer-action-btns" style="display:flex; gap:4px; align-items:center;">
                                        <button type="button" id="openQuickAddCustomerBtn"
                                            class="btn btn-outline-secondary btn-sm"
                                            style="border-radius: 6px; width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center; background: #fff; border-color: #d8d6de; box-shadow: 0 1px 2px rgba(0,0,0,0.05);"
                                            title="Add New Customer">
                                            <i class="fas fa-plus" style="color: #ff9f43; font-size: 12px;"></i>
                                        </button>
                                        <button type="button" id="posScanFaceBtn"
                                            class="btn btn-outline-secondary btn-sm btn-open-face-scan"
                                            style="border-radius: 6px; width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; background: #fff; border-color: #d8d6de; box-shadow: 0 1px 2px rgba(0,0,0,0.05);"
                                            title="Scan Customer Face">
                                            <i class="fas fa-camera" style="color: #ff9f43; font-size: 12px;"></i>
                                        </button>
                                        <button type="button" id="openEditCustomerBtn"
                                            class="btn btn-outline-secondary btn-sm"
                                            style="border-radius: 6px; width: 26px; height: 26px; display: none; align-items: center; justify-content: center; background: #fff; border-color: #d8d6de; box-shadow: 0 1px 2px rgba(0,0,0,0.05);"
                                            title="Edit Selected Customer">
                                            <i class="fas fa-pencil-alt" style="color: #ff9f43; font-size: 12px;"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="customer-details-box">
                                    <div class="row w-105 pos-customer-row">
                                        <!-- Customer Name -->
                                        <div class="col-md-4 col-12 pos-customer-field pos-customer-field--name">
                                            <div class="select-split select-group w-100">
                                                <div class="select-group w-100">
                                                    <label><i class="far fa-user-circle"
                                                            style="color: #ff9f43; margin-right: 5px;"></i> Customer
                                                        name</label>
                                                    <select id="customer_name" name="customer_name" style="z-index:1;"
                                                        class="form-control select2">
                                                        <option value="">Select Customer</option>
                                                        @foreach ($customers as $username)
                                                            <option value="{{ $username->id }}"
                                                                data-phone="{{ $username->phone ?? '' }}"
                                                                data-gst="{{ $username->gst_number ?? '' }}">
                                                                {{ $username->company_name ?: $username->name }}{{ $username->phone ? ' - ' . $username->phone : '' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="error_customer_name text-danger"></span>
                                                    <div id="posCustomerBalanceChip" class="pos-customer-balance-chip">
                                                        Current balance: <b id="posCustomerBalanceText">{{ $currency_symbol }}0.00</b>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Customer Phone -->
                                        <div class="col-md-4 col-12 pos-customer-field">
                                            <div class="select-split">
                                                <div class="select-group w-100">
                                                    <label><i class="fas fa-phone-alt"
                                                            style="color: #ff9f43; margin-right: 5px;"></i> Customer
                                                        phone</label>
                                                    <input type="tel" id="customer_phone" class="form-control"
                                                        placeholder="Customer number" name="customer_phone">
                                                    <span class="error_customer_phone text-danger"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Customer GST Number -->
                                        <div class="col-md-4 col-12 pos-customer-field" id="customer_gst_field"
                                            style="display: none;">
                                            <div class="select-split">
                                                <div class="select-group w-100">
                                                    <label><i class="fas fa-file-invoice"
                                                            style="color: #ff9f43; margin-right: 5px;"></i> Customer GST
                                                        no</label>
                                                    <div class="pos-gst-input-wrap">
                                                        <input type="text" id="pos_gst_number" class="form-control"
                                                            placeholder="Enter GST to auto-fill"
                                                            name="customer_gst_number" maxlength="15" autocomplete="off">
                                                        <span class="pos-gst-loader" id="pos-gst-loader">
                                                            <i class="fas fa-spinner fa-spin"></i>
                                                        </span>
                                                    </div>
                                                    <div class="pos-gst-msg" id="pos-gst-msg"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>
                            /* Sync native <select> change → fill phone and GST when GST is enabled */
                            document.getElementById('customer_name').addEventListener('change', function() {
                                var selectedOption = this.options[this.selectedIndex];
                                var phone = selectedOption.getAttribute('data-phone') || '';
                                var gst = selectedOption.getAttribute('data-gst') || '';

                                document.getElementById('customer_phone').value = phone;

                                if ($("input[name='gst_option']:checked").val() === 'with') {
                                    document.getElementById('pos_gst_number').value = gst;
                                }
                            });
                        </script>
                        {{-- <div class="split-card">

                        </div> --}}
                        <div class="pt-0 pos-bill-items">
                            <div class="totalitem">
                                <h4>Total items : 0</h4>
                                <div class="pos-bill-search-wrap scanner-search-wrap">
                                    <button type="button" id="scanBarcodeBtn" class="btn btn-sm pos-scan-btn" title="Scan Barcode">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            fill="currentColor" class="bi bi-qr-code-scan" viewBox="0 0 16 16" aria-hidden="true">
                                            <path
                                                d="M0 .5A.5.5 0 0 1 .5 0h3a.5.5 0 0 1 0 1H1v2.5a.5.5 0 0 1-1 0zm12 0a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0V1h-2.5a.5.5 0 0 1-.5-.5M.5 12a.5.5 0 0 1 .5.5V15h2.5a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5v-3a.5.5 0 0 1 .5-.5m15 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1 0-1H15v-2.5a.5.5 0 0 1 .5-.5M4 4h1v1H4z" />
                                            <path d="M7 2H2v5h5zM3 3h3v3H3zm2 8H4v1h1z" />
                                            <path d="M7 9H2v5h5zm-4 1h3v3H3zm8-6h1v1h-1z" />
                                            <path
                                                d="M9 2h5v5H9zm1 1v3h3V3zM8 8v2h1v1H8v1h2v-2h1v2h1v-1h2v-1h-3V8zm2 2H9V9h1zm4 2h-1v1h-2v1h3zm-4 2v-1H8v1z" />
                                            <path d="M12 9h2V8h-2z" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="pos-bill-clear-wrap d-flex align-items-center">
                                    <a href="javascript:void(0);" class="clear_items">Clear all</a>
                                </div>
                            </div>
                            <div id="posAddedNotice" class="pos-added-notice" aria-live="polite"></div>
                            <div class="pos-bill-grid">
                            <div class="pos-bill-head">
                                <span class="pos-bill-head-sno">#</span>
                                <span class="pos-bill-head-item">Item</span>
                                <span class="pos-bill-head-qty">
                                    <span class="pos-head-main">Qty / Rate</span>
                                    <span class="pos-head-subs">
                                        <span>Qty</span>
                                        <span>Rate</span>
                                    </span>
                                </span>
                                <span class="pos-bill-head-disc">
                                    <span class="pos-head-main">Discount</span>
                                    <span class="pos-head-subs">
                                        <span>%</span>
                                        <span>₹</span>
                                    </span>
                                </span>
                                <span class="pos-bill-head-amt">Amount</span>
                                <span class="pos-bill-head-del"></span>
                            </div>
                            <div class="product-table">

                            </div>
                            </div>
                        </div>
                        {{-- <div class="split-card">
                        </div> --}}
                        <div class="pt-0 pb-2 body_space_two select-group w-100">
                            @if ($role == 'admin' || $role == 'staff' || $role == 'sub-admin')
                                <!-- Labour Items Section -->
                                <div class="col-lg-12 mb-3" id="labour_items_section" style="display: none;">
                                    <div class="select-split">
                                        <div class="select-group w-100">
                                            <hr>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h5 style=" font-weight: 400; font-size: 16px; margin-bottom: 0;">Labour
                                                    Items</h5>
                                                <button type="button" class="btn btn-sm btn-outline-warning px-3"
                                                    data-bs-toggle="modal" data-bs-target="#addLabourModal"
                                                    style="color: #ff9f43; border-color: #ff9f43; background: transparent; border-radius: 4px;">+
                                                    Add Labour</button>
                                            </div>
                                            <div id="labour-items-container">
                                                <!-- Labour items will be added here dynamically -->
                                            </div>
                                            <!-- <button type="button" class="btn btn-primary mt-2" id="add-labour-item">
                                                                                                                                                    <i class="fas fa-plus"></i> Add Labour Item
                                                                                                                                                </button> -->
                                            <hr>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Labour Items Section -->

                                <!-- Add Labour Modal -->
                                <div class="modal fade" id="addLabourModal" tabindex="-1"
                                    aria-labelledby="addLabourModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addLabourModalLabel"
                                                    style="font-weight: 700; font-size: 18px; color: #333;">Add New Labour
                                                    Item</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close">x</button>
                                            </div>
                                            <div class="modal-body" style="text-align: left;">
                                                <div class="mb-3">
                                                    <label for="new_labour_name" class="form-label"
                                                        style="font-weight: 600; color: #555;">Labour Name <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="new_labour_name"
                                                        placeholder="Enter labour name"
                                                        style="border: 1px solid #ccc; border-radius: 4px; padding: 10px;">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="new_labour_price" class="form-label"
                                                        style="font-weight: 600; color: #555;">Labour Price <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" id="new_labour_price"
                                                        placeholder="Enter price" min="0" step="0.01"
                                                        style="border: 1px solid #ccc; border-radius: 4px; padding: 10px;">
                                                </div>
                                            </div>
                                            <div class="modal-footer" style="border-top: none;">
                                                <button type="button" class="btn btn-dark px-4" data-bs-dismiss="modal"
                                                    style="border-radius: 4px;">Close</button>
                                                <button type="button" class="btn btn-warning px-4" id="saveLabourBtn"
                                                    style="background-color:#ff9f43; color:white; border:none; border-radius: 4px;">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        $('#saveLabourBtn').on('click', function() {
                                            let name = $('#new_labour_name').val().trim();
                                            let price = $('#new_labour_price').val();

                                            if (!name || !price) {
                                                Swal.fire('Error', 'Please enter both Labour Name and Price', 'error');
                                                return;
                                            }

                                            let $btn = $(this);
                                            let originalText = $btn.text();
                                            $btn.text('Saving...').prop('disabled', true);

                                            $.ajax({
                                                url: "/api/add-labour-item",
                                                type: "POST",
                                                dataType: "json",
                                                data: {
                                                    item_name: name,
                                                    price: price,
                                                    sub_admin_id: typeof selectedSubAdminId !== 'undefined' ?
                                                        selectedSubAdminId : null
                                                },
                                                headers: {
                                                    "Authorization": "Bearer " + (typeof authToken !== 'undefined' ? authToken :
                                                        ''),
                                                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                                                },
                                                success: function(response) {
                                                    $btn.text(originalText).prop('disabled', false);
                                                    if (response.status) {
                                                        Swal.fire({
                                                            title: "Success",
                                                            text: response.message ||
                                                                "Labour item added successfully!",
                                                            icon: "success",
                                                            confirmButtonColor: "#ff9f43"
                                                        });
                                                        $('#addLabourModal').modal('hide');
                                                        $('#new_labour_name').val('');
                                                        $('#new_labour_price').val('');

                                                        let newItem = {
                                                            id: response.data ? response.data.id : '',
                                                            item_name: response.data ? response.data.item_name : name,
                                                            price: response.data ? response.data.price : price
                                                        };

                                                        if (typeof labourItemsList !== 'undefined') {
                                                            labourItemsList.push(newItem);
                                                        }

                                                        let newOption =
                                                            `<option value="${newItem.id}" data-price="${newItem.price}">${newItem.item_name}</option>`;
                                                        $('.labour-select').append(newOption);

                                                        // Find the first empty select, or default to the last one
                                                        let $targetSelect = $('.labour-select').filter(function() {
                                                            return !$(this).val();
                                                        }).first();

                                                        if ($targetSelect.length === 0) {
                                                            $targetSelect = $('.labour-select').last();
                                                        }

                                                        // Select the newly added item
                                                        $targetSelect.val(newItem.id).trigger('change');

                                                        if ($('.labour-select').hasClass('select2-hidden-accessible')) {
                                                            $('.labour-select').trigger('change.select2');
                                                        }
                                                    } else {
                                                        Swal.fire('Error', response.message || 'Failed to add labour item',
                                                            'error');
                                                    }
                                                },
                                                error: function(xhr) {
                                                    $btn.text(originalText).prop('disabled', false);
                                                    let msg = 'Failed to add labour item';
                                                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                                                        let errors = Object.values(xhr.responseJSON.errors);
                                                        msg = errors[0][0];
                                                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                                        msg = xhr.responseJSON.message;
                                                    }
                                                    Swal.fire('Error', msg, 'error');
                                                }
                                            });
                                        });
                                    });
                                </script>
                            @endif
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {

                                    var toggleBtn = document.getElementById('toggle_labour_items');
                                    var labourSection = document.getElementById('labour_items_section');
                                    var icon = toggleBtn.querySelector('i');

                                    if (toggleBtn && labourSection) {

                                        toggleBtn.addEventListener('click', function(e) {
                                            e.preventDefault();

                                            if (labourSection.style.display === 'none' || labourSection.style.display === '') {

                                                labourSection.style.display = 'block';

                                                icon.classList.remove('fa-plus');
                                                icon.classList.add('fa-minus');

                                            } else {

                                                labourSection.style.display = 'none';

                                                icon.classList.remove('fa-minus');
                                                icon.classList.add('fa-plus');

                                                // Clear/reset labour items when hiding the labour section
                                                if (typeof $ !== 'undefined') {
                                                    $("#labour-items-container").html("");
                                                    if (typeof window.addLabourRow === 'function') {
                                                        window.addLabourRow();
                                                    } else if (typeof addLabourRow === 'function') {
                                                        addLabourRow();
                                                    }
                                                    if (typeof window.calculateTotals === 'function') {
                                                        window.calculateTotals();
                                                    } else if (typeof calculateTotals === 'function') {
                                                        calculateTotals();
                                                    }
                                                }
                                            }
                                            if (typeof window.fitPosProductGrid === 'function') {
                                                setTimeout(window.fitPosProductGrid, 50);
                                            }
                                        });

                                    }

                                });
                            </script>
                            <div class="row">

                            </div>
                            <div class="row ">
                                <div class="col-lg-12">
                                    <div class="select-split ">
                                        <div class="select-group w-100 pos-bill-extras">
                                            <div class="pos-bill-extras-actions mb-2">
                                                    <div class="pos-bill-extra-chip">
                                                        <span>Shipping</span>
                                                        <a href="javascript:void(0);" id="toggle_shipping_cost"
                                                            class="btn btn-outline-secondary btn-sm pos-bill-extra-btn"
                                                            title="Toggle Shipping Cost">
                                                            <i class="fas fa-plus" style="color: #ff9f43;"></i>
                                                        </a>
                                                    </div>
                                                    <div class="pos-bill-extra-chip">
                                                        <span>Discount</span>
                                                        <a href="javascript:void(0);" id="toggle_additional_discount"
                                                            class="btn btn-outline-secondary btn-sm pos-bill-extra-btn"
                                                            title="Toggle Additional Discount">
                                                            <i class="fas fa-plus" style="color: #ff9f43;"></i>
                                                        </a>
                                                    </div>
                                                    {{-- Other Charges toggle --}}
                                                    <div class="pos-bill-extra-chip">
                                                        <span>Other Charges</span>
                                                        <a href="javascript:void(0);" id="toggle_other_charges"
                                                            class="btn btn-outline-secondary btn-sm pos-bill-extra-btn"
                                                            title="Toggle Other Charges">
                                                            <i class="fas fa-plus" style="color: #ff9f43;"></i>
                                                        </a>
                                                    </div>
                                                    @if ($role == 'admin' || $role == 'staff' || $role == 'sub-admin')
                                                        <div class="pos-bill-extra-chip">
                                                            <span>Labour</span>
                                                            <a href="javascript:void(0);" id="toggle_labour_items"
                                                                class="btn btn-outline-secondary btn-sm pos-bill-extra-btn"
                                                                title="Add Labour Items">
                                                                <i class="fas fa-plus" style="color: #ff9f43;"></i>
                                                            </a>
                                                        </div>
                                                    @endif
                                                    {{-- Exchange Charges toggle --}}
                                                    <div class="pos-bill-extra-chip">
                                                        <span>Exchange</span>
                                                        <a href="javascript:void(0);" id="toggle_exchange_charges"
                                                            class="btn btn-outline-secondary btn-sm pos-bill-extra-btn"
                                                            title="Toggle Exchange Charges">
                                                            <i class="fas fa-plus" style="color: #ff9f43;"></i>
                                                        </a>
                                                    </div>
                                                    {{-- Remarks section title + add/edit buttons --}}
                                                    <div class="pos-bill-extra-chip">
                                                        <span>Remarks</span>
                                                        <button type="button" id="openAddRemarksBtn"
                                                            class="btn btn-outline-secondary btn-sm pos-bill-extra-btn"
                                                            title="Add Remarks">
                                                            <i class="fas fa-plus" style="color: #ff9f43;"></i>
                                                        </button>
                                                        <button type="button" id="openEditRemarksBtn"
                                                            class="btn btn-outline-secondary btn-sm pos-bill-extra-btn"
                                                            style="display: none;"
                                                            title="Edit Remarks">
                                                            <i class="fas fa-pencil-alt" style="color: #ff9f43;"></i>
                                                        </button>
                                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2" id="shipping_cost_wrapper" style="display: none;">
                                <div class="col-lg-12">
                                    <div class="select-split">
                                        <div class="select-group w-100">
                                            <label for="shipping" class="pos-bill-shipping-label mb-1">Shipping Cost</label>
                                            <input type="number" class="form-control" placeholder="Shipping Cost."
                                                name="shipping" id="shipping" min="0" step="0.01">
                                            <span class="error_shipping"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2" id="additional_discount_wrapper" style="display: none;">
                                <div class="col-lg-12">
                                    <div class="select-split">
                                        <div class="select-group w-100">
                                            <label class="pos-bill-shipping-label mb-1">Additional Discount</label>
                                            <div class="pos-bill-discount-inputs d-flex align-items-center gap-2">
                                                <div class="input-group pos-bill-discount-pct" style="max-width: 110px;">
                                                    <input type="number" class="form-control" id="additional_discount_percent"
                                                        name="additional_discount_percent" min="0" max="100" step="0.01"
                                                        placeholder="0" value="" title="Discount %">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                                <div class="input-group pos-bill-discount-amt" style="flex: 1;">
                                                    <span class="input-group-text">₹</span>
                                                    <input type="number" class="form-control" id="additional_discount_amount"
                                                        name="additional_discount_amount" min="0" step="0.01"
                                                        placeholder="0.00" value="" title="Discount amount">
                                                </div>
                                            </div>
                                            <span class="error_discount"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2" id="other_charges_wrapper" style="display: none;">
                                <div class="col-lg-12">
                                    <div class="select-split ">
                                        <div class="select-group w-100">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="mb-0">Other Charges</label>
                                                <button type="button" id="add_other_charge_btn" class="btn btn-outline-secondary btn-sm" style="border-radius: 6px; width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; background: #fff; border-color: #d8d6de; box-shadow: 0 2px 4px rgba(0,0,0,0.05);" title="Add Other Charge">
                                                    <i class="fas fa-plus" style="color: #ff9f43;"></i>
                                                </button>
                                            </div>
                                            <div id="other_charges_container">
                                                <!-- Dynamic rows will go here -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    function togglePosExtraPanel(btn, panelEl) {
                                        if (!btn || !panelEl) return;
                                        btn.addEventListener('click', function() {
                                            const icon = this.querySelector('i');
                                            const isHidden = panelEl.style.display === 'none' || panelEl.style.display === '';
                                            if (isHidden) {
                                                panelEl.style.display = 'flex';
                                                if (icon) {
                                                    icon.classList.remove('fa-plus');
                                                    icon.classList.add('fa-minus');
                                                }
                                            } else {
                                                panelEl.style.display = 'none';
                                                if (icon) {
                                                    icon.classList.remove('fa-minus');
                                                    icon.classList.add('fa-plus');
                                                }
                                            }
                                        });
                                    }

                                    togglePosExtraPanel(
                                        document.getElementById('toggle_shipping_cost'),
                                        document.getElementById('shipping_cost_wrapper')
                                    );
                                    togglePosExtraPanel(
                                        document.getElementById('toggle_additional_discount'),
                                        document.getElementById('additional_discount_wrapper')
                                    );

                                    let syncingAdditionalDiscount = false;
                                    $(document).on('input', '#additional_discount_percent', function() {
                                        if (syncingAdditionalDiscount || window._syncingAdditionalDiscountFields) return;
                                        syncingAdditionalDiscount = true;
                                        window._posAdditionalDiscountMode = 'percent';
                                        const pct = Math.max(0, Math.min(100, parseFloat($(this).val()) || 0));
                                        const base = Math.max(0, (window._posAfterItemDiscountBase || 0));
                                        const amt = base > 0 ? (base * pct) / 100 : 0;
                                        $('#additional_discount_amount').val(amt > 0 ? amt.toFixed(2) : '');
                                        syncingAdditionalDiscount = false;
                                        if (typeof window.calculateTotals === 'function') window.calculateTotals();
                                        else if (typeof calculateTotals === 'function') calculateTotals();
                                    });
                                    $(document).on('input', '#additional_discount_amount', function() {
                                        if (syncingAdditionalDiscount || window._syncingAdditionalDiscountFields) return;
                                        syncingAdditionalDiscount = true;
                                        window._posAdditionalDiscountMode = 'amount';
                                        const amt = Math.max(0, parseFloat($(this).val()) || 0);
                                        const base = Math.max(0, (window._posAfterItemDiscountBase || 0));
                                        const pct = (amt > 0 && base > 0) ? Math.min(100, (amt / base) * 100) : 0;
                                        $('#additional_discount_percent').val(pct > 0 ? Number(pct.toFixed(2)).toString() : '');
                                        syncingAdditionalDiscount = false;
                                        if (typeof window.calculateTotals === 'function') window.calculateTotals();
                                        else if (typeof calculateTotals === 'function') calculateTotals();
                                    });

                                    const toggleOtherChargesBtn = document.getElementById('toggle_other_charges');
                                    const otherChargesWrapper = document.getElementById('other_charges_wrapper');
                                    if(toggleOtherChargesBtn) {
                                        toggleOtherChargesBtn.addEventListener('click', function() {
                                            const icon = this.querySelector('i');
                                            if(otherChargesWrapper.style.display === 'none' || otherChargesWrapper.style.display === '') {
                                                otherChargesWrapper.style.display = 'flex';
                                                icon.classList.remove('fa-plus');
                                                icon.classList.add('fa-minus');
                                            } else {
                                                otherChargesWrapper.style.display = 'none';
                                                icon.classList.remove('fa-minus');
                                                icon.classList.add('fa-plus');
                                            }
                                        });
                                    }
                                    $(document).on('click', '#add_other_charge_btn', function() {
                                        const rowHtml = `
                                            <div class="d-flex align-items-center gap-2 mb-2 other-charge-row">
                                                <input type="text" class="form-control" name="other_charges_name[]" placeholder="Charge Name (e.g. Battery Charges)">
                                                <input type="number" class="form-control other-charge-amount" name="other_charges_amount[]" placeholder="Amount" min="0" step="0.01" oninput="if(typeof window.calculateTotals === 'function') window.calculateTotals(); else if(typeof calculateTotals === 'function') calculateTotals();">
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-other-charge-btn" style="border-radius: 6px; width: 32px; height: 30px; display: inline-flex; align-items: center; justify-content: center;" title="Remove">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        `;
                                        $('#other_charges_container').append(rowHtml);
                                    });

                                    $(document).on('click', '.remove-other-charge-btn', function() {
                                        $(this).closest('.other-charge-row').remove();
                                        if(typeof window.calculateTotals === 'function') window.calculateTotals(); else if(typeof calculateTotals === 'function') calculateTotals();
                                    });

                                    window.setPosShippingVisible = function(visible) {
                                        const panel = document.getElementById('shipping_cost_wrapper');
                                        if (!panel) return;
                                        panel.style.display = visible ? 'flex' : 'none';
                                        const icon = document.querySelector('#toggle_shipping_cost i');
                                        if (icon) {
                                            icon.classList.toggle('fa-plus', !visible);
                                            icon.classList.toggle('fa-minus', !!visible);
                                        }
                                    };
                                    window.setPosAdditionalDiscountVisible = function(visible) {
                                        const panel = document.getElementById('additional_discount_wrapper');
                                        if (!panel) return;
                                        panel.style.display = visible ? 'flex' : 'none';
                                        const icon = document.querySelector('#toggle_additional_discount i');
                                        if (icon) {
                                            icon.classList.toggle('fa-plus', !visible);
                                            icon.classList.toggle('fa-minus', !!visible);
                                        }
                                    };
                                });
                            </script>
                            <div class="mt-2" id="exchange_charges_wrapper" style="display: none;">
                                    <div class="card p-2 mb-0" style="background: #fff8f0; border: 1px dashed #ff9f43; border-radius: 8px;">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong style="color: #ff9f43; font-size: 13px;"><i class="fas fa-recycle me-1"></i> Exchange Details</strong>
                                            <div class="d-flex align-items-center gap-1">
                                                <button type="button" id="add_exchange_row_btn" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 11px;" title="Add another material">
                                                    <i class="fas fa-plus"></i> Add More
                                                </button>
                                                <button type="button" id="clear_exchange_btn" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 11px;">Clear</button>
                                            </div>
                                        </div>
                                        <div id="exchange_rows_container"></div>
                                        <input type="hidden" id="pos_exchange_amount" name="exchange_amount" value="">
                                        <input type="hidden" id="pos_exchange_material_id" name="exchange_material_id" value="">
                                        <input type="hidden" id="pos_exchange_type_id" name="exchange_type_id" value="">
                                        <input type="hidden" id="pos_exchange_price" name="exchange_price" value="">
                                        <input type="hidden" id="pos_exchange_weight" name="exchange_weight" value="">
                                        <input type="hidden" id="pos_exchange_surplus" name="exchange_surplus" value="0">
                                        <input type="hidden" id="pos_exchange_surplus_action" name="exchange_surplus_action" value="">
                                        <input type="hidden" id="pos_customer_deposit_applied" name="customer_deposit_applied" value="0">
                                        <input type="hidden" id="pos_customer_deposit_balance" value="0">

                                        {{-- When exchange > bill: return cash OR deposit to customer --}}
                                        <div id="exchange_surplus_options" class="mt-2 p-2" style="display:none;border:1px dashed #ff9f43;border-radius:8px;background:#fff8f0;">
                                            <div style="font-size:12px;font-weight:700;color:#c2410c;margin-bottom:6px;">
                                                Remaining balance:
                                                <span id="exchange_surplus_options_amount">{{ $currency_symbol }}0.00</span>
                                            </div>
                                            <div class="d-flex flex-wrap gap-3" style="font-size:12px;">
                                                <label class="mb-0 d-flex align-items-center gap-1" style="cursor:pointer;">
                                                    <input type="radio" name="exchange_surplus_action_radio" value="return_cash">
                                                    Return Cash
                                                </label>
                                                <label class="mb-0 d-flex align-items-center gap-1" style="cursor:pointer;">
                                                    <input type="radio" name="exchange_surplus_action_radio" value="deposit">
                                                    Deposit to Customer
                                                </label>
                                            </div>
                                            <div id="pos_surplus_balance_snapshot" class="pos-balance-snapshot" style="display:none;">
                                                <div>Current balance: <b id="posSurplusBalanceCurrent">{{ $currency_symbol }}0.00</b></div>
                                                <div>After this: <b id="posSurplusBalanceAfter">{{ $currency_symbol }}0.00</b></div>
                                            </div>
                                            <div id="exchange_surplus_deposit_hint" class="mt-1" style="display:none;font-size:11px;color:#6b7280;">
                                                Amount will be saved on this customer and can be used on future bills.
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <script type="application/json" id="exchange_materials_json">
                                @php
                                    $exchangeMaterialsJson = collect($materials ?? [])->map(function ($material) {
                                        return [
                                            'id' => (int) $material->id,
                                            'name' => (string) $material->name,
                                            'price' => (float) ($material->price ?? 0),
                                            'types' => collect($material->types ?? [])->map(function ($type) {
                                                return [
                                                    'id' => (int) $type->id,
                                                    'type_name' => (string) ($type->type_name ?? $type->name ?? ''),
                                                ];
                                            })->values()->all(),
                                        ];
                                    })->values();
                                @endphp
                                {!! json_encode($exchangeMaterialsJson, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) !!}
                            </script>
                            <script type="text/template" id="exchange_material_options_template">
                                <option value="">Select Material</option>
                                @if(isset($materials))
                                    @foreach($materials as $material)
                                        <option value="{{ $material->id }}" data-price="{{ $material->price }}">
                                            {{ $material->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </script>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const toggleExchangeBtn = document.getElementById('toggle_exchange_charges');
                                    const exchangeWrapper = document.getElementById('exchange_charges_wrapper');
                                    const $rowsContainer = $('#exchange_rows_container');
                                    const materialOptionsHtml = ($('#exchange_material_options_template').html() || '').trim();
                                    let exchangeMaterialsById = {};
                                    try {
                                        const materialsList = JSON.parse(($('#exchange_materials_json').text() || '[]').trim());
                                        (materialsList || []).forEach(function(m) {
                                            if (m && m.id != null) {
                                                exchangeMaterialsById[String(m.id)] = m;
                                            }
                                        });
                                    } catch (e) {
                                        exchangeMaterialsById = {};
                                    }

                                    if (toggleExchangeBtn) {
                                        toggleExchangeBtn.addEventListener('click', function() {
                                            const icon = this.querySelector('i');
                                            if (exchangeWrapper.style.display === 'none' || exchangeWrapper.style.display === '') {
                                                exchangeWrapper.style.display = 'block';
                                                icon.classList.remove('fa-plus');
                                                icon.classList.add('fa-minus');
                                                if ($rowsContainer.children('.exchange-row').length === 0) {
                                                    addExchangeRow();
                                                }
                                            } else {
                                                exchangeWrapper.style.display = 'none';
                                                icon.classList.remove('fa-minus');
                                                icon.classList.add('fa-plus');
                                            }
                                        });
                                    }

                                    let exchangeOptionsRefreshing = false;
                                    let exchangeTotalsTimer = null;

                                    function getSelectedMaterialIds(exceptRow) {
                                        const ids = [];
                                        $rowsContainer.find('.exchange-row').each(function() {
                                            if (exceptRow && this === exceptRow) return;
                                            const val = $(this).find('.exchange-material-select').val();
                                            if (val) ids.push(String(val));
                                        });
                                        return ids;
                                    }

                                    function refreshExchangeMaterialOptions($row) {
                                        const $select = $row.find('.exchange-material-select');
                                        const currentVal = String($select.val() || '');
                                        const usedIds = getSelectedMaterialIds($row[0]);
                                        const $temp = $('<select>').html(materialOptionsHtml);

                                        exchangeOptionsRefreshing = true;
                                        try {
                                            $select[0].innerHTML = '';
                                            $temp.find('option').each(function() {
                                                const val = String($(this).attr('value') || '');
                                                if (val && usedIds.includes(val) && val !== currentVal) {
                                                    return;
                                                }
                                                $select.append($(this).clone());
                                            });
                                            if (currentVal) {
                                                $select.val(currentVal);
                                            }
                                        } finally {
                                            exchangeOptionsRefreshing = false;
                                        }
                                    }

                                    function refreshAllExchangeMaterialOptions() {
                                        if (exchangeOptionsRefreshing) return;
                                        exchangeOptionsRefreshing = true;
                                        try {
                                            $rowsContainer.find('.exchange-row').each(function() {
                                                const $row = $(this);
                                                const $select = $row.find('.exchange-material-select');
                                                const currentVal = String($select.val() || '');
                                                const usedIds = getSelectedMaterialIds(this);
                                                const $temp = $('<select>').html(materialOptionsHtml);
                                                $select[0].innerHTML = '';
                                                $temp.find('option').each(function() {
                                                    const val = String($(this).attr('value') || '');
                                                    if (val && usedIds.includes(val) && val !== currentVal) {
                                                        return;
                                                    }
                                                    $select.append($(this).clone());
                                                });
                                                if (currentVal) {
                                                    $select.val(currentVal);
                                                }
                                            });
                                        } finally {
                                            exchangeOptionsRefreshing = false;
                                        }
                                    }

                                    function buildExchangeRow() {
                                        const $row = $(`
                                            <div class="exchange-row">
                                                <div class="exchange-field exchange-field-material">
                                                    <label class="form-label mb-1">Material</label>
                                                    <select class="form-select form-select-sm exchange-material-select"></select>
                                                </div>
                                                <div class="exchange-field exchange-field-type">
                                                    <label class="form-label mb-1">Type</label>
                                                    <select class="form-select form-select-sm exchange-type-select">
                                                        <option value="">Select Type</option>
                                                    </select>
                                                </div>
                                                <div class="exchange-field exchange-field-qty">
                                                    <label class="form-label mb-1">Wt/Qty</label>
                                                    <input type="number" step="0.001" min="0" class="form-control form-control-sm exchange-weight-input" value="1.000" placeholder="Qty">
                                                </div>
                                                <div class="exchange-field exchange-field-rate">
                                                    <label class="form-label mb-1">Rate</label>
                                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm exchange-price-input" placeholder="0.00">
                                                </div>
                                                <div class="exchange-field exchange-field-amt">
                                                    <label class="form-label mb-1">Amount</label>
                                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm exchange-amount-input" placeholder="0.00" readonly>
                                                </div>
                                                <div class="exchange-field exchange-field-del">
                                                    <button type="button" class="btn btn-sm btn-outline-danger remove-exchange-row-btn" title="Remove row">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        `);
                                        $row.find('.exchange-material-select').html(materialOptionsHtml);
                                        return $row;
                                    }

                                    function addExchangeRow(prefill) {
                                        const availableCount = $('<select>').html(materialOptionsHtml).find('option[value!=""]').length;
                                        const usedCount = getSelectedMaterialIds().length;
                                        if (!prefill && usedCount >= availableCount && availableCount > 0) {
                                            if (typeof toastr !== 'undefined') {
                                                toastr.warning('All materials are already selected.');
                                            } else {
                                                alert('All materials are already selected.');
                                            }
                                            return null;
                                        }

                                        const $row = buildExchangeRow();
                                        $rowsContainer.append($row);
                                        refreshExchangeMaterialOptions($row);

                                        if (prefill) {
                                            if (prefill.material_id) {
                                                exchangeOptionsRefreshing = true;
                                                try {
                                                    $row.find('.exchange-material-select').val(String(prefill.material_id));
                                                } finally {
                                                    exchangeOptionsRefreshing = false;
                                                }
                                                populateExchangeTypes($row, prefill.type_id || null, true);
                                            }
                                            if (prefill.price !== undefined && prefill.price !== null && prefill.price !== '') {
                                                $row.find('.exchange-price-input').val(prefill.price);
                                            }
                                            if (prefill.weight !== undefined && prefill.weight !== null && prefill.weight !== '') {
                                                $row.find('.exchange-weight-input').val(prefill.weight);
                                            }
                                            calculateExchangeRowAmount($row, true);
                                        }

                                        refreshAllExchangeMaterialOptions();
                                        updateRemoveExchangeButtons();
                                        updateExchangeHiddenTotals();
                                        return $row;
                                    }

                                    function updateRemoveExchangeButtons() {
                                        const $rows = $rowsContainer.find('.exchange-row');
                                        const showDelete = $rows.length > 1;
                                        $rows.toggleClass('is-single', !showDelete);
                                        $rows.find('.remove-exchange-row-btn').toggle(showDelete);
                                    }

                                    function populateExchangeTypes($row, selectedTypeId, skipTotals) {
                                        const $material = $row.find('.exchange-material-select');
                                        const $typeSelect = $row.find('.exchange-type-select');
                                        const $selected = $material.find('option:selected');
                                        const materialId = String($material.val() || '');
                                        $typeSelect.empty().append('<option value="">Select Type</option>');

                                        if (!materialId) {
                                            $row.find('.exchange-price-input').val('');
                                            calculateExchangeRowAmount($row, skipTotals);
                                            return;
                                        }

                                        const materialMeta = exchangeMaterialsById[materialId] || null;
                                        const price = parseFloat(
                                            (materialMeta && materialMeta.price != null)
                                                ? materialMeta.price
                                                : ($selected.attr('data-price') || $selected.data('price') || 0)
                                        ) || 0;
                                        if (!$row.find('.exchange-price-input').val()) {
                                            $row.find('.exchange-price-input').val(price.toFixed(2));
                                        } else if (!selectedTypeId) {
                                            $row.find('.exchange-price-input').val(price.toFixed(2));
                                        }

                                        let types = (materialMeta && Array.isArray(materialMeta.types))
                                            ? materialMeta.types
                                            : [];
                                        // Legacy fallback if JSON map is missing
                                        if (!types.length) {
                                            let rawTypes = $selected.attr('data-types') || $selected.data('types');
                                            if (typeof rawTypes === 'string') {
                                                try { types = JSON.parse(rawTypes); } catch (e) { types = []; }
                                            } else if (Array.isArray(rawTypes)) {
                                                types = rawTypes;
                                            }
                                        }

                                        if (Array.isArray(types) && types.length > 0) {
                                            types.forEach(function(t) {
                                                if (!t) return;
                                                const id = t.id != null ? t.id : '';
                                                const name = t.type_name || t.name || ('Type ' + id);
                                                if (id === '' || name === '') return;
                                                $typeSelect.append(
                                                    $('<option></option>').val(String(id)).text(String(name))
                                                );
                                            });
                                            if (selectedTypeId) {
                                                $typeSelect.val(String(selectedTypeId));
                                            } else if (types.length === 1) {
                                                $typeSelect.val(String(types[0].id));
                                            }
                                        } else {
                                            $typeSelect.append(
                                                $('<option></option>').val('').text('No type linked').prop('disabled', true)
                                            );
                                        }
                                        calculateExchangeRowAmount($row, skipTotals);
                                    }

                                    function calculateExchangeRowAmount($row, skipTotals) {
                                        const price = parseFloat($row.find('.exchange-price-input').val()) || 0;
                                        const weight = parseFloat($row.find('.exchange-weight-input').val()) || 0;
                                        const amount = price * weight;
                                        $row.find('.exchange-amount-input').val(amount > 0 ? amount.toFixed(2) : '');
                                        if (!skipTotals) {
                                            updateExchangeHiddenTotals();
                                        }
                                    }

                                    function collectExchangeRows() {
                                        const items = [];
                                        $rowsContainer.find('.exchange-row').each(function() {
                                            const $row = $(this);
                                            const materialId = $row.find('.exchange-material-select').val();
                                            if (!materialId) return;
                                            const $matOpt = $row.find('.exchange-material-select option:selected');
                                            const $typeOpt = $row.find('.exchange-type-select option:selected');
                                            items.push({
                                                material_id: materialId,
                                                material_name: ($matOpt.text() || '').trim(),
                                                type_id: $row.find('.exchange-type-select').val() || null,
                                                type_name: ($typeOpt.val() ? ($typeOpt.text() || '').trim() : ''),
                                                price: parseFloat($row.find('.exchange-price-input').val()) || 0,
                                                weight: parseFloat($row.find('.exchange-weight-input').val()) || 0,
                                                amount: parseFloat($row.find('.exchange-amount-input').val()) || 0
                                            });
                                        });
                                        return items;
                                    }

                                    function updateExchangeHiddenTotals() {
                                        const items = collectExchangeRows();
                                        const total = items.reduce((sum, item) => sum + (parseFloat(item.amount) || 0), 0);
                                        const first = items[0] || null;
                                        $('#pos_exchange_amount').val(total > 0 ? total.toFixed(2) : '');
                                        $('#pos_exchange_material_id').val(first ? first.material_id : '');
                                        $('#pos_exchange_type_id').val(first && first.type_id ? first.type_id : '');
                                        $('#pos_exchange_price').val(first ? first.price : '');
                                        $('#pos_exchange_weight').val(first ? first.weight : '');

                                        if (exchangeTotalsTimer) {
                                            clearTimeout(exchangeTotalsTimer);
                                        }
                                        exchangeTotalsTimer = setTimeout(function() {
                                            if (typeof window.calculateTotals === 'function') {
                                                window.calculateTotals();
                                            } else if (typeof calculateTotals === 'function') {
                                                calculateTotals();
                                            }
                                        }, 0);
                                    }

                                    window.getPosExchangeItems = collectExchangeRows;
                                    window.getPosExchangeDetailsPayload = function() {
                                        const items = collectExchangeRows();
                                        if (!items.length) return null;
                                        const total = items.reduce((sum, item) => sum + (parseFloat(item.amount) || 0), 0);
                                        const names = items.map(i => i.material_name + (i.type_name ? (' - ' + i.type_name) : '')).join(', ');
                                        const weights = items.map(i => i.weight).filter(w => w > 0);
                                        return {
                                            items: items,
                                            material_id: items[0].material_id,
                                            material_name: names,
                                            type_id: items[0].type_id,
                                            type_name: items.length === 1 ? (items[0].type_name || '') : '',
                                            price: items[0].price,
                                            weight: weights.length === 1 ? weights[0] : weights.reduce((a, b) => a + b, 0),
                                            amount: total
                                        };
                                    };
                                    window.setPosExchangeRows = function(rows) {
                                        exchangeOptionsRefreshing = true;
                                        $rowsContainer.empty();
                                        exchangeOptionsRefreshing = false;
                                        const list = Array.isArray(rows) ? rows.filter(Boolean) : [];
                                        if (!list.length) {
                                            addExchangeRow();
                                            return;
                                        }
                                        list.forEach(function(row) {
                                            addExchangeRow(row);
                                        });
                                        refreshAllExchangeMaterialOptions();
                                        updateExchangeHiddenTotals();
                                    };
                                    window.clearPosExchangeRows = function() {
                                        exchangeOptionsRefreshing = true;
                                        $rowsContainer.empty();
                                        exchangeOptionsRefreshing = false;
                                        addExchangeRow();
                                    };

                                    $(document).on('change', '.exchange-material-select', function() {
                                        if (exchangeOptionsRefreshing) return;
                                        const $row = $(this).closest('.exchange-row');
                                        populateExchangeTypes($row, null);
                                        refreshAllExchangeMaterialOptions();
                                        updateExchangeHiddenTotals();
                                    });

                                    $(document).on('input', '.exchange-price-input, .exchange-weight-input', function() {
                                        calculateExchangeRowAmount($(this).closest('.exchange-row'));
                                    });

                                    $(document).on('click', '#add_exchange_row_btn', function() {
                                        addExchangeRow();
                                    });

                                    $(document).on('click', '.remove-exchange-row-btn', function() {
                                        if ($rowsContainer.find('.exchange-row').length <= 1) return;
                                        $(this).closest('.exchange-row').remove();
                                        refreshAllExchangeMaterialOptions();
                                        updateRemoveExchangeButtons();
                                        updateExchangeHiddenTotals();
                                    });

                                    $(document).on('click', '#clear_exchange_btn', function() {
                                        window.clearPosExchangeRows();
                                    });

                                    // Start with one empty row (hidden until toggled)
                                    addExchangeRow();
                                });
                            </script>
                            @if ((bool) ($setting->tds_apply ?? false))
                                <div class="row mt-2">
                                    <div class="col-lg-6 col-6">
                                        <div class="select-group w-100">
                                            <label for="tds_percentage">TDS Percentage (%)</label>
                                            <input type="text" class="form-control" name="tds_percentage"
                                                id="tds_percentage">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-6">
                                        <div class="select-group w-100 ">
                                            <label for="tds_amount">TDS Amount</label>
                                            <input type="text" class="form-control" name="tds_amount" id="tds_amount"
                                                min="0" value="0.00" readonly>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="row mt-2" id="deposit_advance_section" style="display: {{ ($isRentalMode ?? false) ? 'flex' : 'none' }};">
                                <div class="col-lg-12">
                                    <div class="select-group w-100">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label for="deposit_amount" class="mb-0">Deposit / Advance</label>
                                        </div>
                                        <input type="number" class="form-control" placeholder="Deposit Amount"
                                            name="deposit_amount" id="deposit_amount" min="0" step="0.01">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="labourItemModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form id="labourItemForm">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="labourItemModalLabel">Add Labour Item</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close">x</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group mb-3">
                                                <label>Labour Name <span class="text-danger">*</span></label>
                                                <input type="text" name="item_name" id="labour_item_name"
                                                    class="form-control" placeholder="Enter labour name">
                                                <span class="text-danger" id="labour_item_name_error"></span>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label>Labour Price <span class="text-danger">*</span></label>
                                                <input type="number" name="price" id="labour_item_price"
                                                    class="form-control" placeholder="Enter price" step="0.01">
                                                <span class="text-danger" id="labour_item_price_error"></span>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        {{-- Hidden remarks textarea — keeps working with existing order submission JS --}}
                        <textarea name="remarks" id="remarks" style="display:none;"></textarea>
                        <div class="setvalue">
                            <ul>
                                <li>

                                    <h5>Total (Product)</h5>
                                    <h6 style="color: green;">
                                        @if ($currency_position === 'right')
                                            <span class="subtotal-value">0.00</span>{{ $currency_symbol }}
                                        @else
                                            {{ $currency_symbol }}<span class="subtotal-value">0.00</span>
                                        @endif
                                    </h6>
                                    <input type="hidden" name="subtotal" value="" class="tax-hidden">
                                </li>

                                <li class="gst-summary-row" style="display: none;">
                                    <h5>Total GST Amount</h5>
                                    <h6 class="gst-total-amount">
                                        @if ($currency_position === 'right')
                                            <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                            {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                </li>

                                <li class="discount-summary-row" style="display: none;">
                                    <h5>Item Discount</h5>
                                    <h6 class="item-discount-amount discount-amount">
                                        @if ($currency_position === 'right')
                                            <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                            {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                    <input type="hidden" name="discount_amount" value="0.00" class="tax-hidden">
                                    <input type="hidden" name="discount" id="bill_discount_percent" value="0" class="tax-hidden">
                                </li>
                                <li class="additional-discount-summary-row" style="display: none;">
                                    <h5>Additional Discount (<span class="additional-discount-percent-label">0</span>%)</h5>
                                    <h6 class="additional-discount-amount">
                                        @if ($currency_position === 'right')
                                            <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                            {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                </li>

                                <li class="subtotal-summary-row" style="display: none;">
                                    <h5>SubTotal</h5>
                                    <h6 class="price-after-discount">
                                        {{-- @if ($currency_position === 'right')
                                            0.00{{ $currency_symbol }} + 0.00{{ $currency_symbol }} - 0.00{{ $currency_symbol }} = 0.00{{ $currency_symbol }}
                                        @else
                                            {{ $currency_symbol }}0.00 + {{ $currency_symbol }}0.00 - {{ $currency_symbol }}0.00 = {{ $currency_symbol }}0.00
                                        @endif --}}
                                    </h6>
                                    <input type="hidden" name="price_after_discount" value="0.00" class="tax-hidden">
                                </li>

                                <li class="shipping-summary-row" style="display: none;">
                                    <h5>Shipping Cost</h5>
                                    <h6 class="shipping-cost-summary">
                                        @if ($currency_position === 'right')
                                            <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                            {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                </li>

                                <li class="labour-summary-row" style="display: none;">
                                    <h5>Labour Charge</h5>
                                    <h6 class="labour-total-amount">
                                        @if ($currency_position === 'right')
                                            <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                            {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                </li>

                                <li class="tds-summary-row" style="display: none;">
                                    <h5>TDS (<span class="tds-percentage-summary">0.00</span>%)</h5>
                                    <h6 class="tds-amount-summary">
                                        @if ($currency_position === 'right')
                                            <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                            {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                </li>

                                {{-- @foreach ($taxRates as $tax)
                                        <li class="taxList">
                                            <h5>{{ $tax->tax_name }} ({{ $tax->tax_rate }}%) Tax</h5>
                                            <h6 class="tax-value" data-rate="{{ $tax->tax_rate }}"
                                                data-symbol="{{ $currency_symbol }}"
                                                data-position="{{ $currency_position }}">
                                                @if ($currency_position === 'right')
                                                    <span>0.00</span>{{ $currency_symbol }}
                                                @else
                                                    {{ $currency_symbol }}<span>0.00</span>
                                                @endif
                                            </h6>
                                        </li>
                                    @endforeach --}}




                                <li class="round-off-row d-none">
                                    <h5>Round Off</h5>
                                    <h6 class="round-off-amount">
                                        @if ($currency_position === 'right')
                                            <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                            {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                    <input type="hidden" name="round_off" value="0.00" class="tax-hidden">
                                </li>

                                <li class="advance-paid-summary-row" style="display: none;">
                                    <h5>Advance Paid</h5>
                                    <h6 class="advance-paid-amount-summary" style="color: red;">
                                        @if ($currency_position === 'right')
                                            <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                            {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                </li>

                                <li class="other-charges-summary-row" style="display: none;">
                                    <h5>Other Charges</h5>
                                    <h6 class="other-charges-total-summary">
                                        @if ($currency_position === 'right')
                                            <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                            {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                </li>

                                <li class="exchange-charges-summary-row" style="display: none;">
                                    <h5>Exchange Deduction</h5>
                                    <h6 class="exchange-charges-total-summary" style="color: #28c76f;">
                                        @if ($currency_position === 'right')
                                            - <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                            - {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                </li>

                                <li class="exchange-surplus-summary-row" style="display: none;">
                                    <h5>Exchange Surplus (return to customer)</h5>
                                    <h6 class="exchange-surplus-total-summary" style="color: #ea5455;">
                                        @if ($currency_position === 'right')
                                            <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                            {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                </li>

                                <li class="customer-deposit-apply-row" style="display: none;">
                                    <h5>
                                        Use Customer Deposit
                                        <small class="customer-deposit-balance-label" style="display:block;font-weight:500;color:#6b7280;font-size:11px;margin-top:2px;">
                                            Current: {{ $currency_symbol }}0.00
                                        </small>
                                        <small class="customer-deposit-after-label" style="display:block;font-weight:600;color:#0f766e;font-size:11px;margin-top:2px;">
                                            After this: {{ $currency_symbol }}0.00
                                        </small>
                                    </h5>
                                    <h6 style="display:flex;align-items:center;gap:6px;justify-content:flex-end;">
                                        <input type="checkbox" id="use_customer_deposit" title="Apply deposit on this bill">
                                        <input type="number" id="customer_deposit_apply_amount" class="form-control form-control-sm"
                                            min="0" step="0.01" value="" placeholder="0" style="width:90px;height:28px;padding:2px 6px;text-align:right;" disabled>
                                    </h6>
                                </li>

                                <li class="total-value">
                                    <h5>Total</h5>
                                    <h6>
                                        @if ($currency_position === 'right')
                                            <span class="total-amount">0</span>{{ $currency_symbol }}
                                        @else
                                            {{ $currency_symbol }}<span class="total-amount">0</span>
                                        @endif
                                    </h6>
                                    <input type="hidden" name="total" value="" class="tax-hidden">
                                </li>

                                <li class="error-message-container">
                                    <span class="error_total" style="color:red; display:none;"></span>
                                </li>

                                @if ($isRentalMode)
                                <li class="deposit-summary-row" style="display: none;">
                                    <h5>Deposit/Advance</h5>
                                    <h6 class="deposit-amount-summary" style="color: red;">
                                        @if ($currency_position === 'right')
                                            -<span>0.00</span>{{ $currency_symbol }}
                                        @else
                                            -{{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                </li>

                                <li class="balance-due-summary-row" style="display: none;">
                                    <h5>Balance Due</h5>
                                    <h6 class="balance-due-amount-summary" style="font-weight: bold; color: #1b2850;">
                                        @if ($currency_position === 'right')
                                            <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                            {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                </li>
                                @endif
                            </ul>
                        </div>
                        </div>
                        <div class="payment_panel" id="paymentPanel">
                            <!-- ⬆ Drag handle — grab to move the panel up/down -->
                            <div id="paymentPanelHandle" class="payment-panel-handle" title="Drag to move this panel">
                                <span class="handle-pill"></span>
                                <span class="handle-hint">⬆ drag to reveal products ⬇</span>
                                <button type="button" id="paymentPanelMinBtn" class="panel-min-btn d-none"
                                    title="Minimise / Restore">
                                    <svg id="panelMinIcon" xmlns="http://www.w3.org/2000/svg" width="14"
                                        height="14" fill="currentColor" viewBox="0 0 16 16">
                                        <path
                                            d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z" />
                                    </svg>
                                </button>
                            </div>
                            <div id="paymentPanelBody">
                                <div class="setvaluecash" id="paymentSection">
                                    <ul>
                                        <li id="payLaterOption"
                                            style="display: {{ $isRentalMode || !isPaymentMethodEnabled('pay_later') ? 'none' : 'block' }};">
                                            <a href="javascript:void(0);" class="paymentmethod{{ !$isRentalMode && isPaymentMethodEnabled('pay_later') ? ' active' : '' }}">
                                                <input type="radio" name="payment_method" value="pending" hidden
                                                    {{ !$isRentalMode && isPaymentMethodEnabled('pay_later') ? 'checked' : '' }}>
                                                <i class="fas fa-history"
                                                    style="font-size: 16px; display: block; margin-bottom: 4px;"></i>
                                                Pay Later
                                            </a>
                                        </li>
                                        <li style="display: {{ isPaymentMethodEnabled('cash') ? 'block' : 'none' }};">
                                            <a href="javascript:void(0);" class="paymentmethod">
                                                <input type="radio" name="payment_method" value="cash" hidden>
                                                <i class="fas fa-money-bill-wave"
                                                    style="font-size: 16px; display: block; margin-bottom: 4px;"></i>
                                                Cash
                                            </a>
                                        </li>
                                        <li
                                            style="display: {{ isPaymentMethodEnabled('debit card') ? 'block' : 'none' }};">
                                            <a href="javascript:void(0);" class="paymentmethod">
                                                <input type="radio" name="payment_method" value="debit card" hidden>
                                                <i class="fas fa-credit-card"
                                                    style="font-size: 16px; display: block; margin-bottom: 4px;"></i>
                                                Debit
                                            </a>
                                        </li>
                                        <li
                                            style="display: {{ isPaymentMethodEnabled('cash+online') ? 'block' : 'none' }};">
                                            <a href="javascript:void(0);" class="paymentmethod">
                                                <input type="radio" name="payment_method" value="cash+online" hidden>
                                                <i class="fas fa-money-check-alt"
                                                    style="font-size: 16px; display: block; margin-bottom: 4px;"></i>
                                                Cash+Online
                                            </a>
                                        </li>

                                        <li style="display: {{ isPaymentMethodEnabled('scan') ? 'block' : 'none' }};">
                                            <a href="javascript:void(0);" class="paymentmethod">
                                                <input type="radio" name="payment_method" value="scan" hidden>
                                                <i class="fas fa-qrcode"
                                                    style="font-size: 16px; display: block; margin-bottom: 4px;"></i>
                                                Scan
                                            </a>
                                        </li>
                                        <li id="emiPaymentOption"
                                            style="display: {{ $isAdvanceReceiptMode || !isPaymentMethodEnabled('emi') ? 'none' : 'block' }};">
                                            <a href="javascript:void(0);" class="paymentmethod">
                                                <input type="radio" name="payment_method" value="emi" hidden>
                                                <i class="fas fa-calendar-alt"
                                                    style="font-size: 16px; display: block; margin-bottom: 4px;"></i>
                                                EMI
                                            </a>
                                        </li>

                                    </ul>

                                    {{-- Advance Receipt: Warning Message --}}
                                    <div id="advanceReceiptFields" class="mt-2 px-2"
                                        style="display: {{ $isAdvanceReceiptMode ? 'block' : 'none' }};">
                                        <p class="text-muted" style="font-size:11px; margin-top:4px;">
                                            <i class="fas fa-lock" style="color:#7367f0;"></i>
                                            Product price will be <strong>locked</strong> at current price when advance is
                                            paid.
                                        </p>
                                    </div>

                                    <div id="paymentDetailActions" class="payment-detail-actions" style="display:none;">
                                        <button type="button" id="cancelPaymentDetails"
                                            class="payment-detail-cancel me-2">
                                            Cancel
                                        </button>
                                    </div>
                                    <div id="posPaidTypeBox" style="display:none; margin-top:10px;">
                                        <div class="form-group mb-2" id="posPaidTypeDropdownGroup">
                                            <label for="posPaidType">Paid Type</label>
                                            <select id="posPaidType" class="form-control">
                                                <option value="" selected disabled>Select Paid Type</option>
                                                <option value="fully">Fully</option>
                                                <option value="partially">Partially</option>
                                            </select>
                                            <span class="error_paidtype text-danger"></span>
                                        </div>
                                        <div id="posPaidAmountFields" style="display:none;">
                                            <div class="row g-2">
                                                <div class="col-md-6 col-6">
                                                    <div class="form-group mb-2">
                                                        <label for="posPaidAmount">Enter Amount</label>
                                                        <input type="number" class="form-control" id="posPaidAmount"
                                                            placeholder="0.00" min="0" step="0.01">
                                                        <span class="error_paidamount text-danger"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-6">
                                                    <div class="form-group">
                                                        <label for="posPendingAmount">Pending Amount</label>
                                                        <input type="number" class="form-control" id="posPendingAmount"
                                                            placeholder="0.00" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="cashOnlineBox" style="display:none; margin-top:10px;">
                                        <div class="row g-2">
                                            <div class="col-md-6 col-6">
                                                <div class="form-group">
                                                    <label>Cash Amount</label>
                                                    <input type="number" class="form-control"
                                                        id="posCashOnlineCashAmount" placeholder="Enter Cash Amount">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-6">
                                                <div class="form-group">
                                                    <label>Online Amount</label>
                                                    <input type="number" class="form-control"
                                                        id="posCashOnlineOnlineAmount" placeholder="Enter Online Amount"
                                                        value="0" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="bankSelectionBox" style="display:none; margin-top:10px;">
                                        <div class="row g-2">
                                            <div class="col-md-6 col-6">
                                                <div class="form-group">
                                                    <div class="bank-label-row">
                                                        <label>Select Bank</label>
                                                        <button type="button" id="openAddBankModal"
                                                            class="bank-add-btn">Add Bank</button>
                                                    </div>
                                                    <select name="bank_id" id="bank_id" class="form-control">
                                                        <option value="">Select Bank</option>
                                                        @foreach ($banks as $bank)
                                                            <option value="{{ $bank->id }}">{{ $bank->bank_name }}
                                                                ({{ $bank->account_number }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="error_bank text-danger"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-6">
                                                <div class="form-group">
                                                    <label for="posPaymentRemark">Remark</label>
                                                    <input type="text" class="form-control" id="posPaymentRemark"
                                                        placeholder="Enter remark">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="emiBox" style="display:none; margin-top:10px;">
                                        <div class="row g-2">
                                            <div class="col-md-6 col-6">
                                                <div class="form-group">
                                                    <label>Down Payment (Optional)</label>
                                                    <input type="number" class="form-control" id="emiDownPayment"
                                                        placeholder="₹ Amount" min="0" step="0.01">
                                                    <small class="text-danger d-none" id="emiDownPaymentError"></small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-6">
                                                <div class="form-group">
                                                    <label>Loan Amount</label>
                                                    <input type="number" class="form-control" id="emiLoanAmount"
                                                        placeholder="Auto Calculate" min="0" step="0.01"
                                                        readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-6">
                                                <div class="form-group">
                                                    <label>EMI Tenure</label>
                                                    <select class="form-control" id="emiTenure">
                                                        <option value="">Select Tenure</option>
                                                        <option value="3">3 Months</option>
                                                        <option value="6">6 Months</option>
                                                        <option value="9">9 Months</option>
                                                        <option value="12">12 Months</option>
                                                        <option value="custom">Custom</option>
                                                    </select>
                                                    <small class="text-danger d-none" id="emiTenureError"></small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-6 d-none" id="emiCustomTenureCol">
                                                <div class="form-group">
                                                    <label>Custom Tenure (Months)</label>
                                                    <input type="number" class="form-control" id="emiCustomTenure"
                                                        min="1" max="120" step="1"
                                                        placeholder="Enter months">
                                                    <small class="text-danger d-none" id="emiCustomTenureError"></small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-6">
                                                <div class="form-group">
                                                    <label>Interest Rate (%) <small
                                                            class="text-muted">Optional</small></label>
                                                    <input type="number" class="form-control" id="emiInterestRate"
                                                        value="0" min="0" step="0.01">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-6">
                                                <div class="form-group">
                                                    <label>Monthly EMI</label>
                                                    <input type="number" class="form-control" id="emiMonthlyAmount"
                                                        placeholder="Auto Calculate" min="0" step="0.01"
                                                        readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-6">
                                                <div class="form-group">
                                                    <label>Aadhar Number</label>
                                                    <input type="text" class="form-control" id="emiAadharNumber"
                                                        placeholder="Customer Aadhar Number">
                                                    <small class="text-danger d-none" id="emiAadharError"></small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-6">
                                                <div class="form-group">
                                                    <label>DO ID <small class="text-muted"></small></label>
                                                    <input type="text" class="form-control" id="emiDoId"
                                                        placeholder="DO ID">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-6">
                                                <div class="form-group">
                                                    <label>PAN Number <small class="text-muted">Optional</small></label>
                                                    <input type="text" class="form-control" id="emiPanNumber"
                                                        placeholder="PAN Number">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-6">
                                                <div class="form-group">
                                                    <label>Guarantor Name <small
                                                            class="text-muted">Optional</small></label>
                                                    <input type="text" class="form-control" id="emiGuarantorName"
                                                        placeholder="Guarantor Name">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <div class="bank-label-row">
                                                        <label class="mb-0">Select Bank <small
                                                                class="text-muted">Optional</small></label>
                                                        <button type="button" id="openAddBankModalEmi"
                                                            class="bank-add-btn">Add Bank</button>
                                                    </div>
                                                    <select class="form-control" id="emiBankId">
                                                        <option value="">Select Bank</option>
                                                        @foreach ($banks as $bank)
                                                            <option value="{{ $bank->id }}">{{ $bank->bank_name }}
                                                                ({{ $bank->account_number }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <small class="text-danger d-none" id="emiBankError"></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="error_peymentmethod"></span>
                                </div>

                                <div class="btn-totallabel">
                                    <h6>Total Amount : 60.00$</h6>
                                    <h5>{{ $isAdvanceReceiptMode ? 'Generate Advance Receipt' : 'Generate Bill' }} <svg
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16"
                                            style="margin-bottom: 0.1rem;">
                                            <path fill-rule="evenodd"
                                                d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8" />
                                        </svg></h5>
                                </div>
                            </div><!-- /#paymentPanelBody -->
                        </div>
                        <script>
                            // Build final bill layout as soon as markup is parsed (before footer scripts)
                            (function () {
                                function run() {
                                    if (typeof window.buildPosBillWorkspace === 'function') {
                                        window.buildPosBillWorkspace();
                                    }
                                }
                                run();
                                if (!document.querySelector('.pos-bill-workspace')) {
                                    setTimeout(run, 0);
                                }
                            })();
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice print preview (F10 to print) -->
    <div class="modal fade" id="posInvoicePrintModal" tabindex="-1" aria-labelledby="posInvoicePrintModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="true">
        <div class="modal-dialog modal-fullscreen-lg-down modal-xl modal-dialog-centered pos-invoice-print-dialog">
            <div class="modal-content pos-invoice-print-content d-flex flex-column">
                <div class="modal-header py-2 flex-shrink-0">
                    <div class="pe-2">
                        <h5 class="modal-title mb-0" id="posInvoicePrintModalLabel">
                            <span class="d-none d-md-inline">Bill Generated</span>
                            <span class="d-md-none">Invoice Printing</span>
                        </h5>
                        <small class="text-muted d-none d-md-inline">Select printer → Press <kbd>F10</kbd> or Print</small>
                        <small class="text-muted d-md-none">Choose a printer, then Print.</small>
                    </div>
                    @if(isset($setting) && $setting->invoice_size === 'small')
                    @php $preferredInvTpl = invoice_print_js_prefs()['template'] ?? 1; @endphp
                    <div class="d-flex gap-1 ms-3 mt-2 align-items-center flex-grow-1 justify-content-center" id="posPdfTemplateButtons"
                         data-preferred-template="{{ $preferredInvTpl }}">
                        <span class="text-muted small fw-semibold me-1">Format:</span>
                        <button type="button" class="btn btn-sm btn-primary py-0 px-2 pos-pdf-format-btn" data-template="1" onclick="posLoadPdfTemplate(1, this)" title="Template 1 — Cash sale bill">T1</button>
                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 pos-pdf-format-btn" data-template="2" onclick="posLoadPdfTemplate(2, this)" title="Template 2">T2</button>
                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 pos-pdf-format-btn" data-template="3" onclick="posLoadPdfTemplate(3, this)" title="Template 3">T3</button>
                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 pos-pdf-format-btn" data-template="4" onclick="posLoadPdfTemplate(4, this)" title="Template 4">T4</button>
                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 pos-pdf-format-btn" data-template="5" onclick="posLoadPdfTemplate(5, this)" title="Template 5">T5</button>
                    </div>
                    @endif
                    <button type="button" class="btn btn-outline-secondary btn-sm invoice-preview-toggle" title="Back to preview" aria-label="Back to preview">
                        <i class="fa fa-arrow-left"></i>
                        <span>Preview</span>
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div class="modal-body p-0 pos-invoice-print-body flex-grow-1">
                    <div class="phone-print-panel d-md-none" data-select="#posPrinterSelect" data-status="#posQzStatus" data-storage="posSelectedPrinter" data-bt="#posBtPrinterList">
                        <div class="phone-print-tabs">
                            <button type="button" class="phone-print-tab is-active" data-tab="bt">Bluetooth</button>
                            <button type="button" class="phone-print-tab" data-tab="wifi">WIFI</button>
                        </div>
                        <div id="posBtPrinterList" class="bt-printer-list" data-print-pane="bt">
                            <div class="bt-printer-empty">Tap Show printers and choose the receipt printer.</div>
                        </div>
                        <div id="posWifiPrinterList" class="bt-printer-list d-none" data-print-pane="wifi">
                            <div class="bt-printer-empty">Looking for the Wi‑Fi printer…</div>
                        </div>
                    </div>
                    <div class="invoice-print-preview">
                    <div class="pos-invoice-print-loading" id="posInvoicePrintLoading" aria-live="polite">
                        <div class="spinner-border" role="status" aria-hidden="true"></div>
                        <span>Loading invoice...</span>
                    </div>
                    <iframe id="posInvoicePrintFrame" title="Invoice preview" src="about:blank" scrolling="no"></iframe>
                    </div>
                </div>
                <div class="modal-footer py-2 pos-invoice-print-footer flex-shrink-0">
                    <div class="pos-invoice-print-footer-inner w-100">
                        <div class="pos-invoice-print-footer-controls">
                            <label class="mb-0 small text-muted text-nowrap" for="posPrinterSelect">Printer</label>
                            <select id="posPrinterSelect" class="form-select form-select-sm pos-invoice-printer-select">
                                <option value="">Connecting…</option>
                            </select>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="posRefreshPrintersBtn" title="Refresh printer list">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm d-none d-md-inline-block desktop-bt-print-btn" data-select="#posPrinterSelect" data-status="#posQzStatus" data-storage="posSelectedPrinter" title="Choose a nearby Bluetooth printer">
                                Bluetooth
                            </button>
                            <div class="form-check mb-0 d-flex align-items-center gap-1">
                                <input class="form-check-input" type="checkbox" id="posThermalAutoCut">
                                <label class="form-check-label small" for="posThermalAutoCut">Auto cut</label>
                            </div>
                            <div class="form-check mb-0 d-flex align-items-center gap-1" title="Bluetooth only: prints as printer text (much faster). Turn off to print the exact invoice image.">
                                <input class="form-check-input bt-fast-print-toggle" type="checkbox" id="posBtFastPrint" checked>
                                <label class="form-check-label small" for="posBtFastPrint">Fast print</label>
                            </div>
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" id="posPrintHindi">
                                <label class="form-check-label small" for="posPrintHindi">Print in Hindi</label>
                            </div>
                            <span class="small pos-invoice-print-status" id="posQzStatus"></span>
                            <a href="https://qz.io/download/" target="_blank" rel="noopener" class="small text-nowrap" id="posQzInstallLink" style="display:none;">Install QZ Tray</a>
                            <a href="{{ url('/qz/install-qz-trust.bat') }}" class="small text-nowrap d-none d-md-inline" id="posQzTrustLink" title="Fixes Allow disabled when Remember is checked">Fix Allow</a>
                        </div>
                        <div class="pos-invoice-print-footer-actions">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary btn-sm" id="posInvoicePrintBtn" disabled>
                                <span class="d-none d-md-inline">Print (F10)</span>
                                <span class="d-md-none">Print</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add / Edit Product Modal (also used for Quick Add on unknown barcode scan) -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <form id="addProductForm">
                        <div class="row">
                            <div class="form-group col-md-12 mb-3">
                                <label>Product Type</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="item_type_modal"
                                            id="item_type_normal_modal" value="normal" checked>
                                        <label class="form-check-label" for="item_type_normal_modal">Normal
                                            Product</label>
                                    </div>
                                    @php
                                        $userBranch = auth()->user()->branch_id;
                                        $setting =
                                            \App\Models\Setting::where('branch_id', $userBranch)->first() ??
                                            \App\Models\Setting::first();
                                        $enableVariant = $setting ? $setting->enable_variant_product : 1;
                                    @endphp
                                    @if ($enableVariant)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="item_type_modal"
                                                id="item_type_variant_modal" value="variant">
                                            <label class="form-check-label" for="item_type_variant_modal">Variant
                                                Product</label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-md-6 col-6 mb-3">
                                <label>Product Name <span class="text-danger">*</span></label>
                                <input type="text" id="product_modal_name" class="form-control"
                                    placeholder="Enter Product Name">
                                <span class="text-danger" id="product_modal_name_error"></span>
                            </div>
                            <div class="form-group col-md-6 col-6 mb-3">
                                <label>Category <span class="text-danger">*</span></label>
                                <select id="product_modal_category" class="form-control">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger" id="product_modal_category_error"></span>
                            </div>
                            <div class="form-group col-md-6 col-6 mb-3">
                                <label>Brand</label>
                                <select id="product_modal_brand" class="form-control">
                                    <option value="">Select Brand</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger" id="product_modal_brand_error"></span>
                            </div>
                            <div class="form-group col-md-6 col-6 mb-3">
                                <label>Unit <span class="text-danger">*</span></label>
                                <select id="product_modal_unit" class="form-control">
                                    <option value="">Select Unit</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger" id="product_modal_unit_error"></span>
                            </div>

                            <div class="form-group col-md-6 col-6 mb-3">
                                <label>SKU</label>
                                <input type="text" id="product_modal_sku" class="form-control"
                                    placeholder="Enter SKU">
                                <span class="text-danger" id="product_modal_sku_error"></span>
                            </div>
                            <div class="form-group col-md-6 col-6 mb-3">
                                <label>HSN Code</label>
                                <input type="text" id="product_modal_hsn_code" class="form-control"
                                    placeholder="Enter HSN Code">
                                <span class="text-danger" id="product_modal_hsn_code_error"></span>
                            </div>
                            <div class="form-group col-md-6 col-6 mb-3 normal-product-field-modal">
                                <label>Purchase Price <span class="text-danger">*</span></label>
                                <input type="text" id="product_modal_purchase_price" class="form-control"
                                    inputmode="decimal" placeholder="Enter Purchase Price">
                                <span class="text-danger" id="product_modal_purchase_price_error"></span>
                            </div>
                            <div class="form-group col-md-6 col-6 mb-3 normal-product-field-modal">
                                <label>Price <span class="text-danger">*</span></label>
                                <input type="text" id="product_modal_price" class="form-control"
                                    inputmode="decimal" placeholder="Enter Price">
                                <span class="text-danger" id="product_modal_price_error"></span>
                            </div>
                            <div class="form-group col-md-6 col-6 mb-3 normal-product-field-modal">
                                <label>Qty</label>
                                <input type="text" id="product_modal_quantity" class="form-control"
                                    inputmode="decimal" placeholder="Enter Quantity" value="0">
                                <span class="text-danger" id="product_modal_quantity_error"></span>
                            </div>
                            <div class="form-group col-md-6 col-6 mb-3">
                                <label>Status</label>
                                <select id="product_modal_status" class="form-control">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <span class="text-danger" id="product_modal_status_error"></span>
                            </div>
                            <div class="form-group col-md-6 col-6 mb-3">
                                <label>Stock</label>
                                <select id="product_modal_availability" class="form-control">
                                    <option value="in_stock">In Stock</option>
                                    <option value="out_stock">Out Of Stock</option>
                                </select>
                                <span class="text-danger" id="product_modal_availability_error"></span>
                            </div>
                            <div class="form-group col-md-6 col-6 mb-3">
                                <label>GST Option</label>
                                <select id="product_modal_gst_option" class="form-control">
                                    <option value="without_gst">Without GST</option>
                                    <option value="with_gst">With GST</option>
                                </select>
                                <span class="text-danger" id="product_modal_gst_option_error"></span>
                            </div>
                            <div class="form-group col-md-6 col-6 mb-3 normal-product-field-modal">
                                <label>Barcode</label>
                                <input type="text" id="product_modal_barcode" class="form-control"
                                    placeholder="Enter Barcode">
                                <span class="text-danger" id="product_modal_barcode_error"></span>
                            </div>
                            <div class="form-group col-md-6 col-6 mb-3 normal-product-field-modal">
                                <label>MRP</label>
                                <input type="text" id="product_modal_mrp" class="form-control"
                                    inputmode="decimal" placeholder="Enter MRP">
                                <span class="text-danger" id="product_modal_mrp_error"></span>
                            </div>
                            <div class="form-group col-md-6 col-6 mb-3 normal-product-field-modal">
                                <label>Batch No</label>
                                <input type="text" id="product_modal_batch_no" class="form-control"
                                    placeholder="Enter Batch No">
                                <span class="text-danger" id="product_modal_batch_no_error"></span>
                            </div>
                            <div class="form-group col-md-6 col-6 mb-3 normal-product-field-modal">
                                <label>Label Code</label>
                                <input type="text" id="product_modal_label_code" class="form-control"
                                    placeholder="Enter Label Code">
                                <span class="text-danger" id="product_modal_label_code_error"></span>
                            </div>
                            <div class="form-group col-md-6 col-6 mb-3 normal-product-field-modal">
                                <label>Wholesale</label>
                                <input type="text" id="product_modal_label_ref" class="form-control"
                                    placeholder="Enter Wholesale">
                                <span class="text-danger" id="product_modal_label_ref_error"></span>
                            </div>
                            <div class="form-group col-md-6 col-6 mb-3 normal-product-field-modal">
                                <label>Other</label>
                                <input type="text" id="product_modal_specific_qty" class="form-control"
                                    placeholder="Enter Other">
                                <span class="text-danger" id="product_modal_specific_qty_error"></span>
                            </div>                            
                            <!-- Rental Fields -->
                            <div class="form-group col-md-6 col-6 mb-3 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="product_modal_is_rent_available" id="product_modal_is_rent_available" value="1">
                                    <label class="form-check-label fw-bold" for="product_modal_is_rent_available" style="cursor: pointer;">Available for Rent</label>
                                </div>
                            </div>
                            
                            <div class="form-group col-md-6 col-6 mb-3 product-modal-rental-fields" style="display: none;">
                                <label class="form-label-icon"> Rent Type</label>
                                <select class="form-control" name="product_modal_rent_period" id="product_modal_rent_period">
                                    <option value="">Select</option>
                                    <option value="hour">Hour</option>
                                    <option value="day">Day</option>
                                    <option value="week">Week</option>
                                    <option value="month">Month</option>
                                    <option value="year">Year</option>
                                </select>
                            </div>
                            
                            <div class="form-group col-md-6 col-6 mb-3 product-modal-rental-fields" style="display: none;">
                                <label class="form-label-icon"> Rent Price</label>
                                <input type="number" name="product_modal_rent_price" id="product_modal_rent_price" class="form-control" min="0" step="0.01">
                            </div>
                            <div class="form-group col-md-12 mb-3 d-none" id="product_modal_tax_wrapper">
                                <label>GST Rates <span class="text-danger">*</span></label>
                                <select id="product_modal_taxes" class="form-control select2" multiple
                                    style="width: 100%;">
                                    @foreach ($taxes as $tax)
                                        <option value="{{ $tax->id }}">
                                            {{ $tax->tax_name }} ({{ $tax->tax_rate }}%)
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger" id="product_modal_taxes_error"></span>
                            </div>
                            <div class="form-group col-md-12 mb-3">
                                <label>Description</label>
                                <textarea id="product_modal_description" class="form-control" rows="4" placeholder="Enter Description"></textarea>
                                <span class="text-danger" id="product_modal_description_error"></span>
                            </div>
                            <div class="form-group col-md-12 mb-3">
                                <label>Product Image</label>
                                <div class="product-modal-upload">
                                    <input type="file" id="product_modal_images" multiple accept="image/*">
                                    <small class="product-modal-help">Select one or more product images.</small>
                                </div>
                                <div id="product_modal_image_preview" class="mt-2 d-flex gap-2 flex-wrap"></div>
                                <span class="text-danger" id="product_modal_images_error"></span>
                            </div>
                            <!-- Product Variants Section for Modal -->
                            <div class="col-lg-12 mt-3 variant-product-field-modal" style="display: none;">
                                <div class="card">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">Product Variants</h5>
                                        <button type="button" class="btn btn-primary btn-sm"
                                            id="addVariantBtnModal">+ Add More</button>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered border" id="variantsTableModal">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>Size</th>
                                                        <th>Color</th>
                                                        <th>Qty</th>
                                                        <th>Purchase Price</th>
                                                        <th>Price</th>
                                                        <th>Barcode</th>
                                                        <th class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="variantsBodyModal">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Variants Section -->
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const rentCheckbox = document.getElementById('product_modal_is_rent_available');
                                const rentFields = document.querySelectorAll('.product-modal-rental-fields');
                                if(rentCheckbox) {
                                    rentCheckbox.addEventListener('change', function() {
                                        rentFields.forEach(field => {
                                            field.style.display = this.checked ? 'block' : 'none';
                                        });
                                    });
                                }
                            });
                        </script>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-submit" id="saveProductBtn">Update Product</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Face Scan Modal Component -->
    @include('components.face-scan-modal')
@endsection
@push('js')
    <script>
        window.enabledPaymentMethods = @json(getEnabledPaymentMethods());
    </script>
    <script>
        // Store scanned face embedding temporarily in case of Quick Add
        window.lastScannedFaceEmbedding = null;

        // POS Face Scanning Logic
        window.onFaceCaptured = function(embeddingArray, faceDataUrl) {
            if (window.faceScanTarget === 'qac_register') {
                // Show preview in QAC modal
                $('#qac_face_preview').attr('src', faceDataUrl).show();
                $('#qac_face_icon').hide();
                window.lastScannedFaceEmbedding = embeddingArray;
                window.lastScannedFaceDataUrl = faceDataUrl;
                
                // Show quick success
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                });
                Toast.fire({
                    icon: 'success',
                    title: 'Face attached to new customer'
                });
                return;
            }

            $.ajax({
                url: '/api/customers/face-match',
                method: 'POST',
                data: { embedding: embeddingArray },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                success: function(res) {
                    if (res.match && res.customer) {
                        let displayName = res.customer.name || "";
                        if (res.customer.phone) {
                            displayName += " - " + res.customer.phone;
                        } else if (res.customer.customer_code) {
                            displayName += " (" + res.customer.customer_code + ")";
                        }

                        if (res.distance <= 0.40) {
                            // High Confidence
                            autoSelectCustomer(res.customer, true);
                        } else if (res.distance <= 0.55) {
                            // Medium Confidence
                            Swal.fire({
                                title: "Possible Match",
                                text: `Use customer: ${displayName}?`,
                                icon: "question",
                                showCancelButton: true,
                                confirmButtonText: "Yes",
                                cancelButtonText: "No"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    autoSelectCustomer(res.customer, false);
                                }
                            });
                        }
                    } else {
                        // No Match
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                        });
                        Toast.fire({
                            icon: 'info',
                            title: 'No matching customer found.'
                        });
                        
                        // Store embedding to auto-attach if they click Quick Add Customer
                        window.lastScannedFaceEmbedding = embeddingArray;
                    }
                },
                error: function() {
                    Swal.fire("Error", "Face matching failed.", "error");
                }
            });
        };

        function autoSelectCustomer(customer, showToast = false) {
            let displayName = customer.name || "";
            if (customer.phone) {
                displayName += " - " + customer.phone;
            } else if (customer.customer_code) {
                displayName += " (" + customer.customer_code + ")";
            }
            const customerName = displayName.trim();
            const $customerSelect = $('#customer_name');
            
            if ($customerSelect.find("option[value='" + customer.id + "']").length) {
                $customerSelect.val(customer.id).trigger('change');
            } else {
                const newOption = new Option(customerName, customer.id, true, true);
                $(newOption).attr('data-phone', customer.phone || '');
                $(newOption).attr('data-gst', customer.gst_number || '');
                $customerSelect.append(newOption).trigger('change');
            }

            if (customer.phone) {
                $('#customer_phone').val(customer.phone);
            }
            if (customer.gst_number) {
                $('#pos_gst_number').val(customer.gst_number);
            }
            
            if (showToast) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
                Toast.fire({
                    icon: 'success',
                    title: 'Customer matched: ' + customerName
                });
            }
        }
    </script>
    {{-- <script src="https://unpkg.com/html5-qrcode@2.3.4/minified/html5-qrcode.min.js"></script> --}}
    @include('partials.invoice-qz-print-script')
    <script>
        if (typeof Html5Qrcode === 'undefined') {
            var script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js';
            script.onload = function() {
                console.log('Library loaded from fallback');
            };
            document.head.appendChild(script);
        }
    </script>
    <script>
        function fetchAndShowEditModal(productId, cartId = null) {
            // Fetch product details
            $.ajax({
                url: '/api/product-view/' + productId,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Authorization': 'Bearer ' + authToken,
                },
                success: function(response) {
                    let product = response.product;

                    $("#addProductModalLabel").text("Edit Product");
                    $("#addProductForm")[0].reset();
                    $(".text-danger.error-text").text("");
                    $("#saveProductBtn").data('mode', 'edit');

                    // Set item type robustly
                    let itemType = (product.item_type || 'normal').toString().toLowerCase().trim();
                    if (itemType === 'variant') {
                        $("#item_type_variant_modal").prop("checked", true);
                    } else {
                        $("#item_type_normal_modal").prop("checked", true);
                    }
                    toggleProductTypeModalFields(itemType);

                    // Populate fields
                    $("#product_modal_name").val(product.name);
                    $("#product_modal_category").val(product.category_id).trigger('change');
                    $("#product_modal_brand").val(product.brand_id).trigger('change');
                    $("#product_modal_unit").val(product.unit_id).trigger('change');
                    $("#product_modal_sku").val(product.SKU);
                    $("#product_modal_hsn_code").val(product.hsn_code);
                    $("#product_modal_purchase_price").val(product.purchase_price);
                    $("#product_modal_price").val(product.price);
                    $("#product_modal_quantity").val(product.quantity);
                    $("#product_modal_status").val(product.status).trigger('change');
                    $("#product_modal_availability").val(product.availablility).trigger('change');
                    $("#product_modal_gst_option").val(product.gst_option).trigger('change');
                    $("#product_modal_barcode").val(product.barcode);
                    $("#product_modal_mrp").val(product.mrp || '');
                    $("#product_modal_batch_no").val(product.batch_no || '');
                    $("#product_modal_label_code").val(product.label_code || '');
                    $("#product_modal_label_ref").val(product.label_reference || '');
                    $("#product_modal_specific_qty").val(product.specific_qty || '');
                    
                    // Populate rental fields
                    if (product.is_rent_available == 1) {
                        $('#product_modal_is_rent_available').prop('checked', true);
                        $('.product-modal-rental-fields').show();
                        $('#product_modal_rent_period').val(product.rent_period);
                        $('#product_modal_rent_price').val(product.rent_price);
                    } else {
                        $('#product_modal_is_rent_available').prop('checked', false);
                        $('.product-modal-rental-fields').hide();
                        $('#product_modal_rent_period').val('');
                        $('#product_modal_rent_price').val('');
                    }
                    $("#product_modal_description").val(product.description);

                    if (product.gst_option === 'with_gst') {
                        $("#product_modal_tax_wrapper").removeClass('d-none');
                        try {
                            let taxes = typeof product.product_gst === 'string' ? JSON.parse(product
                                .product_gst) : product.product_gst;
                            let taxIds = [];
                            if (Array.isArray(taxes)) {
                                taxIds = taxes.map(t => t.tax_id || t.id);
                            }
                            $("#product_modal_taxes").val(taxIds).trigger('change');
                        } catch (e) {}
                    } else {
                        $("#product_modal_tax_wrapper").addClass('d-none');
                        $("#product_modal_taxes").val([]).trigger('change');
                    }

                    // Initialize select2 for taxes if not already done
                    if (!$('#product_modal_taxes').hasClass("select2-hidden-accessible")) {
                        $('#product_modal_taxes').select2({
                            dropdownParent: $('#addProductModal'),
                            width: '100%'
                        });
                    }

                    // Load Image Preview
                    $("#product_modal_image_preview").empty();
                    if (product.image) {
                        try {
                            let cleanImageString = product.image.replace(/\\/g, '').replace(/^"(.*)"$/, '$1');
                            let imageBasePath = '{{ env('ImagePath') }}';
                            let imagesArray = [];
                            if (cleanImageString.startsWith('[')) {
                                imagesArray = JSON.parse(cleanImageString);
                            } else {
                                imagesArray = [cleanImageString];
                            }
                            imagesArray.forEach(img => {
                                if (img) {
                                    $("#product_modal_image_preview").append(`
                                        <div style="position: relative; border: 1px solid #ddd; padding: 5px; border-radius: 4px;">
                                            <img src="${imageBasePath}/storage/${img}" style="width: 80px; height: 80px; object-fit: cover;" alt="Product Image">
                                        </div>
                                    `);
                                }
                            });
                        } catch (e) {}
                    }

                    // Render variants if exist
                    let variantsHtml = '';
                    if (product.variants && product.variants.length > 0) {
                        product.variants.forEach((v, idx) => {
                            variantsHtml += `
                                <tr>
                                    <td><input type="text" name="modal_variants[${idx}][size]" class="form-control" value="${v.size || ''}" required placeholder="e.g. M"></td>
                                    <td><input type="text" name="modal_variants[${idx}][color]" class="form-control" value="${v.color || ''}" required placeholder="e.g. Red"></td>
                                    <td><input type="number" name="modal_variants[${idx}][qty]" class="form-control" value="${v.qty || 0}" min="0" required placeholder="0"></td>
                                    <td><input type="number" name="modal_variants[${idx}][purchase_price]" class="form-control" value="${v.purchase_price || 0}" min="0" step="0.01" required placeholder="0.00"></td>
                                    <td><input type="number" name="modal_variants[${idx}][price]" class="form-control" value="${v.price || 0}" min="0" step="0.01" required placeholder="0.00"></td>
                                    <td>
                                        <input type="text" name="modal_variants[${idx}][barcode]" class="form-control" value="${v.barcode || ''}" placeholder="Auto-generate">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm remove-variant-modal" ${product.variants.length === 1 ? 'disabled' : ''}><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        variantsHtml = `
                            <tr>
                                <td><input type="text" name="modal_variants[0][size]" class="form-control" required placeholder="e.g. M"></td>
                                <td><input type="text" name="modal_variants[0][color]" class="form-control" required placeholder="e.g. Red"></td>
                                <td><input type="number" name="modal_variants[0][qty]" class="form-control" min="0" required placeholder="0"></td>
                                <td><input type="number" name="modal_variants[0][purchase_price]" class="form-control" min="0" step="0.01" required placeholder="0.00"></td>
                                <td><input type="number" name="modal_variants[0][price]" class="form-control" min="0" step="0.01" required placeholder="0.00"></td>
                                <td>
                                    <input type="text" name="modal_variants[0][barcode]" class="form-control" placeholder="Auto-generate">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm remove-variant-modal" disabled><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        `;
                    }
                    $("#variantsBodyModal").html(variantsHtml);

                    // Change submit button behavior to UPDATE
                    $("#saveProductBtn").text("Update Product").data('id', productId).data('cart-id', cartId)
                        .off("click").on("click", submitUpdateProduct);

                    $("#addProductModal").modal("show");
                },
                error: function() {
                    Swal.fire('Error', 'Failed to fetch product details', 'error');
                }
            });
        }

        $(document).on("change", "#product_modal_gst_option", function() {
            if ($(this).val() === 'with_gst') {
                $("#product_modal_tax_wrapper").removeClass('d-none');
            } else {
                $("#product_modal_tax_wrapper").addClass('d-none');
                $("#product_modal_taxes").val([]).trigger('change');
            }
        });

        // Toggle item type visibility
        function toggleProductTypeModalFields(type) {
            if (type === 'variant') {
                $(".normal-product-field-modal").hide();
                $(".variant-product-field-modal").show();
            } else {
                $(".variant-product-field-modal").hide();
                $(".normal-product-field-modal").show();
            }
        }

        $(document).on("change", "input[name='item_type_modal']", function() {
            toggleProductTypeModalFields($(this).val());
        });

        // Add variant row in modal
        $(document).on('click', '#addVariantBtnModal', function() {
            let rowCount = $('#variantsBodyModal tr').length;
            let newRow = `
                <tr>
                    <td><input type="text" name="modal_variants[${rowCount}][size]" class="form-control" required placeholder="e.g. M"></td>
                    <td><input type="text" name="modal_variants[${rowCount}][color]" class="form-control" required placeholder="e.g. Red"></td>
                    <td><input type="number" name="modal_variants[${rowCount}][qty]" class="form-control" min="0" required placeholder="0"></td>
                    <td><input type="number" name="modal_variants[${rowCount}][purchase_price]" class="form-control" min="0" step="0.01" required placeholder="0.00"></td>
                    <td><input type="number" name="modal_variants[${rowCount}][price]" class="form-control" min="0" step="0.01" required placeholder="0.00"></td>
                    <td>
                        <input type="text" name="modal_variants[${rowCount}][barcode]" class="form-control" placeholder="Auto-generate">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-variant-modal"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
            `;
            $('#variantsBodyModal').append(newRow);
            updateModalRemoveButtons();
        });

        $(document).on('click', '.remove-variant-modal', function() {
            if ($('#variantsBodyModal tr').length > 1) {
                $(this).closest('tr').remove();
                updateModalRemoveButtons();
            }
        });

        function updateModalRemoveButtons() {
            let rows = $('#variantsBodyModal tr');
            if (rows.length === 1) {
                rows.find('.remove-variant-modal').prop('disabled', true);
            } else {
                rows.find('.remove-variant-modal').prop('disabled', false);
            }
        }



        $(document).on("click", ".edit-item", function() {
            let itemId = $(this).attr('data-id'); // stringId
            let item = selectedItems.get(itemId);
            if (!item) {
                Swal.fire('Warning', 'Product not found in cart.', 'warning');
                return;
            }
            fetchAndShowEditModal(item.productId, itemId);
        });

        function submitUpdateProduct() {
            let $btn = $("#saveProductBtn");
            let originalText = $btn.text();
            let productId = $btn.data('id');
            let cartId = $btn.data('cart-id');

            let formData = new FormData();
            formData.append('product_id', productId);
            let itemType = $("input[name='item_type_modal']:checked").val();
            formData.append('item_type', itemType);
            formData.append('name', $("#product_modal_name").val().trim());
            formData.append('category_id', $("#product_modal_category").val());
            formData.append('brand_id', $("#product_modal_brand").val());
            formData.append('unit_id', $("#product_modal_unit").val());
            formData.append('SKU', $("#product_modal_sku").val().trim());
            formData.append('hsn_code', $("#product_modal_hsn_code").val().trim());
            formData.append('purchase_price', $("#product_modal_purchase_price").val().trim());
            formData.append('price', $("#product_modal_price").val().trim());
            formData.append('quantity', $("#product_modal_quantity").val().trim());
            formData.append('status', $("#product_modal_status").val());
            formData.append('availablility', $("#product_modal_availability").val());
            formData.append('gst_option', $("#product_modal_gst_option").val());
            formData.append('barcode', $("#product_modal_barcode").val().trim());
            formData.append('mrp', $("#product_modal_mrp").val().trim());
            formData.append('batch_no', $("#product_modal_batch_no").val().trim());
            formData.append('label_code', $("#product_modal_label_code").val().trim());
            formData.append('label_reference', $("#product_modal_label_ref").val().trim());
            formData.append('specific_qty', $("#product_modal_specific_qty").val().trim());
            
            let isRentAvailable = $('#product_modal_is_rent_available').is(':checked') ? 1 : 0;
            formData.append('is_rent_available', isRentAvailable);
            if(isRentAvailable) {
                formData.append('rent_period', $('#product_modal_rent_period').val() || '');
                formData.append('rent_price', $('#product_modal_rent_price').val() || '');
            }
            formData.append('description', $("#product_modal_description").val().trim());

            if (itemType === 'variant') {
                $('#variantsBodyModal tr').each(function(index) {
                    let size = $(this).find('input[name^="modal_variants"][name$="[size]"]').val();
                    let color = $(this).find('input[name^="modal_variants"][name$="[color]"]').val();
                    let qty = $(this).find('input[name^="modal_variants"][name$="[qty]"]').val();
                    let purchase_price = $(this).find('input[name^="modal_variants"][name$="[purchase_price]"]')
                        .val();
                    let price = $(this).find('input[name^="modal_variants"][name$="[price]"]').val();
                    let barcode = $(this).find('input[name^="modal_variants"][name$="[barcode]"]').val();

                    if (size && color && qty && price) {
                        formData.append(`variants[${index}][size]`, size);
                        formData.append(`variants[${index}][color]`, color);
                        formData.append(`variants[${index}][qty]`, qty);
                        formData.append(`variants[${index}][purchase_price]`, purchase_price || price);
                        formData.append(`variants[${index}][price]`, price);
                        formData.append(`variants[${index}][barcode]`, barcode || '');
                    }
                });
            }

            if ($("#product_modal_gst_option").val() === 'with_gst') {
                let taxes = $("#product_modal_taxes").val();
                if (taxes && taxes.length > 0) {
                    taxes.forEach(tax => formData.append('product_gst[]', tax));
                }
            }

            let images = $("#product_modal_images")[0].files;
            if (images && images.length > 0) {
                for (let i = 0; i < images.length; i++) {
                    formData.append('images[]', images[i]);
                }
            }

            $btn.html(
                '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Updating...'
            ).prop('disabled', true);

            $.ajax({
                url: '/api/updateProduct',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Authorization': 'Bearer ' + authToken,
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $btn.html(originalText).prop('disabled', false);
                    if (response.status) {
                        $("#addProductModal").modal("hide");
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Product updated successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        let p = response.product || response.data || null;
                        if (!p && response.status === true) {
                            // If API doesn't return the updated product, we use the submitted values to update the cart immediately
                            p = {
                                name: $("#product_modal_name").val().trim(),
                                price: $("#product_modal_price").val().trim(),
                                quantity: $("#product_modal_quantity").val().trim(),
                                gst_option: $("#product_modal_gst_option").val(),
                            };
                            if (p.gst_option === 'with_gst') {
                                let selectedTaxes = [];
                                $("#product_modal_taxes option:selected").each(function() {
                                    let text = $(this).text();
                                    let rateMatch = text.match(/\(([\d\.]+)\%\)/);
                                    let rate = rateMatch ? rateMatch[1] : 0;
                                    let name = text.replace(/\([\d\.]+\%\)/, '').trim();
                                    selectedTaxes.push({
                                        tax_id: $(this).val(),
                                        tax_name: name,
                                        tax_rate: rate
                                    });
                                });
                                p.product_gst = JSON.stringify(selectedTaxes);
                            } else {
                                p.product_gst = null;
                            }
                        }

                        if (p) {
                            if (selectedItems.has(cartId)) {
                                let item = selectedItems.get(cartId);
                                item.name = p.name || item.name;
                                item.price = formatCurrency(parseFloat(p.price || item.price).toFixed(2),
                                    '{{ $currency_symbol }}', '{{ $currency_position }}');
                                item.stock = p.quantity !== undefined ? p.quantity : item.stock;
                                item.gst_option = p.gst_option || item.gst_option;
                                item.product_gst = p.product_gst !== undefined ? p.product_gst : item
                                    .product_gst;
                                selectedItems.set(cartId, item);
                            }

                            // Re-render UI
                            if (typeof window.updateTotalItems === 'function') {
                                window.updateTotalItems();
                            }

                            // Let's also update the DOM of the product card if it exists in the #product-list
                            let $productCard = $(".productset[data-id='" + productId + "']");
                            if ($productCard.length > 0) {
                                $productCard.find("h4").text(p.name);
                                $productCard.find(".productsetcontent h6").text(p.price);
                                $productCard.data("stock", p.quantity);
                                $productCard.data("gst-option", p.gst_option);
                                $productCard.data("product-gst", typeof p.product_gst === 'object' ? JSON
                                    .stringify(p.product_gst) : p.product_gst);
                            }
                        }
                    } else {
                        Swal.fire('Error', response.message || 'Failed to update product', 'error');
                    }
                },
                error: function(xhr) {
                    $btn.html(originalText).prop('disabled', false);
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        for (let key in errors) {
                            $("#product_modal_" + key + "_error").text(errors[key][0]);
                        }
                    } else {
                        Swal.fire('Error', 'An unexpected error occurred', 'error');
                    }
                }
            });
        }

        function clearProductModalFieldErrors() {
            $("#addProductForm .text-danger").text("");
            $("#addProductForm .is-invalid").removeClass("is-invalid");
        }

        function buildProductModalFormData() {
            let formData = new FormData();
            let itemType = $("input[name='item_type_modal']:checked").val() || 'normal';
            formData.append('item_type', itemType);
            formData.append('name', $("#product_modal_name").val().trim());
            formData.append('category_id', $("#product_modal_category").val());
            formData.append('brand_id', $("#product_modal_brand").val() || '');
            formData.append('unit_id', $("#product_modal_unit").val());
            formData.append('SKU', $("#product_modal_sku").val().trim());
            formData.append('hsn_code', $("#product_modal_hsn_code").val().trim());
            formData.append('purchase_price', $("#product_modal_purchase_price").val().trim());
            formData.append('price', $("#product_modal_price").val().trim());
            formData.append('quantity', $("#product_modal_quantity").val().trim() || '0');
            formData.append('status', $("#product_modal_status").val() || 'active');
            formData.append('availablility', $("#product_modal_availability").val() || 'out_stock');
            formData.append('gst_option', $("#product_modal_gst_option").val() || 'without_gst');
            formData.append('barcode', $("#product_modal_barcode").val().trim());
            formData.append('mrp', $("#product_modal_mrp").val().trim());
            formData.append('batch_no', $("#product_modal_batch_no").val().trim());
            formData.append('label_code', $("#product_modal_label_code").val().trim());
            formData.append('label_reference', $("#product_modal_label_ref").val().trim());
            formData.append('specific_qty', $("#product_modal_specific_qty").val().trim());

            let isRentAvailable = $('#product_modal_is_rent_available').is(':checked') ? 1 : 0;
            formData.append('is_rent_available', isRentAvailable);
            if (isRentAvailable) {
                formData.append('rent_period', $('#product_modal_rent_period').val() || '');
                formData.append('rent_price', $('#product_modal_rent_price').val() || '');
            }
            formData.append('description', $("#product_modal_description").val().trim());

            if (itemType === 'variant') {
                $('#variantsBodyModal tr').each(function(index) {
                    let size = $(this).find('input[name^="modal_variants"][name$="[size]"]').val();
                    let color = $(this).find('input[name^="modal_variants"][name$="[color]"]').val();
                    let qty = $(this).find('input[name^="modal_variants"][name$="[qty]"]').val();
                    let purchase_price = $(this).find('input[name^="modal_variants"][name$="[purchase_price]"]').val();
                    let price = $(this).find('input[name^="modal_variants"][name$="[price]"]').val();
                    let barcode = $(this).find('input[name^="modal_variants"][name$="[barcode]"]').val();

                    if (size && color && qty && price) {
                        formData.append(`variants[${index}][size]`, size);
                        formData.append(`variants[${index}][color]`, color);
                        formData.append(`variants[${index}][qty]`, qty);
                        formData.append(`variants[${index}][purchase_price]`, purchase_price || price);
                        formData.append(`variants[${index}][price]`, price);
                        formData.append(`variants[${index}][barcode]`, barcode || '');
                    }
                });
            }

            if ($("#product_modal_gst_option").val() === 'with_gst') {
                let taxes = $("#product_modal_taxes").val();
                if (taxes && taxes.length > 0) {
                    taxes.forEach(tax => formData.append('product_gst[]', tax));
                }
            }

            let images = $("#product_modal_images")[0] ? $("#product_modal_images")[0].files : null;
            if (images && images.length > 0) {
                for (let i = 0; i < images.length; i++) {
                    formData.append('images[]', images[i]);
                }
            }

            let selectedAdmin = localStorage.getItem('selectedSubAdminId');
            if (selectedAdmin && selectedAdmin !== 'null' && selectedAdmin !== 'undefined') {
                formData.append('branch_id', selectedAdmin);
            }

            return formData;
        }

        function validateProductModalForCreate() {
            clearProductModalFieldErrors();
            let isValid = true;
            let itemType = $("input[name='item_type_modal']:checked").val() || 'normal';
            const required = [
                { id: 'product_modal_name', errId: 'product_modal_name_error', label: 'Product name is required.' },
                { id: 'product_modal_category', errId: 'product_modal_category_error', label: 'Category is required.' },
                { id: 'product_modal_unit', errId: 'product_modal_unit_error', label: 'Unit is required.' }
            ];
            if (itemType !== 'variant') {
                required.push({ id: 'product_modal_price', errId: 'product_modal_price_error', label: 'Price is required.' });
            }
            required.forEach(function(f) {
                let val = $('#' + f.id).val();
                if (!val || val.toString().trim() === '') {
                    $('#' + f.errId).text(f.label);
                    $('#' + f.id).addClass('is-invalid');
                    isValid = false;
                }
            });
            return isValid;
        }

        function prefillQuickAddFromLastProduct() {
            let branchId = localStorage.getItem('selectedSubAdminId');
            let url = '/api/last-added-product';
            if (branchId && branchId !== 'null' && branchId !== 'undefined') {
                url += '?sub_branch_id=' + encodeURIComponent(branchId);
            }
            $.ajax({
                url: url,
                type: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + authToken
                },
                success: function(res) {
                    if (!res || !res.status || !res.product) {
                        return;
                    }
                    if ($("#saveProductBtn").data('mode') !== 'create') {
                        return;
                    }
                    let product = res.product;
                    if (product.category_id) {
                        $("#product_modal_category").val(product.category_id).trigger('change');
                    }
                    if (product.brand_id) {
                        $("#product_modal_brand").val(product.brand_id).trigger('change');
                    }
                    if (product.unit_id) {
                        $("#product_modal_unit").val(product.unit_id).trigger('change');
                    }
                    if (product.hsn_code) {
                        $("#product_modal_hsn_code").val(product.hsn_code);
                    }
                    if (product.gst_option) {
                        $("#product_modal_gst_option").val(product.gst_option).trigger('change');
                        if (product.gst_option === 'with_gst' && product.product_gst) {
                            try {
                                let taxes = typeof product.product_gst === 'string' ? JSON.parse(product.product_gst) : product.product_gst;
                                let taxIds = Array.isArray(taxes) ? taxes.map(t => t.tax_id || t.id) : [];
                                $("#product_modal_taxes").val(taxIds).trigger('change');
                            } catch (e) {}
                        }
                    }
                }
            });
        }

        function openQuickAddProductModal(barcodeOrOpts) {
            var opts = {};
            if (barcodeOrOpts && typeof barcodeOrOpts === 'object') {
                opts = barcodeOrOpts;
            } else {
                opts = { barcode: barcodeOrOpts };
            }
            var barcode = (opts.barcode || '').toString().trim();
            var prefillName = (opts.name || '').toString().trim();

            if ($('#addProductModal').hasClass('show') && $("#saveProductBtn").data('mode') === 'create') {
                if (barcode) {
                    $("#product_modal_barcode").val(barcode);
                }
                if (prefillName && !$("#product_modal_name").val()) {
                    $("#product_modal_name").val(prefillName);
                }
                return;
            }

            clearProductModalFieldErrors();
            $("#addProductForm")[0].reset();
            $("#product_modal_image_preview").empty();
            $("#variantsBodyModal").empty();
            $("#item_type_normal_modal").prop("checked", true);
            toggleProductTypeModalFields('normal');
            $("#product_modal_tax_wrapper").addClass('d-none');
            $("#product_modal_taxes").val([]).trigger('change');
            $('#product_modal_is_rent_available').prop('checked', false);
            $('.product-modal-rental-fields').hide();
            $("#product_modal_quantity").val('0');
            $("#product_modal_status").val('active');
            $("#product_modal_availability").val('out_stock');
            $("#product_modal_gst_option").val('without_gst');
            $("#product_modal_barcode").val(barcode);
            if (prefillName) {
                $("#product_modal_name").val(prefillName);
            }

            if (!$('#product_modal_taxes').hasClass("select2-hidden-accessible")) {
                $('#product_modal_taxes').select2({
                    dropdownParent: $('#addProductModal'),
                    width: '100%'
                });
            }

            $("#addProductModalLabel").text("Quick Add Product");
            $("#saveProductBtn").text("Save & Add to Bill")
                .removeData('id')
                .removeData('cart-id')
                .data('mode', 'create')
                .off("click")
                .on("click", submitCreateProduct);

            $('.pos-bill-search-results').hide().empty();

            $("#addProductModal").modal("show");
            prefillQuickAddFromLastProduct();

            setTimeout(function() {
                if (prefillName) {
                    $("#product_modal_price").trigger('focus');
                } else {
                    $("#product_modal_name").trigger('focus');
                }
            }, 350);
        }
        window.openQuickAddProductModal = openQuickAddProductModal;

        function openQuickAddProductFromSearch($input) {
            var typed = ($input && $input.length ? $input.val() : '') || '';
            typed = String(typed).trim();
            var opts = {};
            if (/^\d{4,}$/.test(typed)) {
                opts.barcode = typed;
            } else if (typed) {
                opts.name = typed;
            }
            openQuickAddProductModal(opts);
        }
        window.openQuickAddProductFromSearch = openQuickAddProductFromSearch;

        $(document).on('click', '.pos-bill-search-plus, .pos-create-product-btn, #openQuickAddProductBtn, .pos-search-create-product', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $input = $(this).closest('.pos-bill-search-row, .pos-bill-search-wrap, .pos-search-dropdown')
                .find('.pos-bill-search-input').first();
            if (!$input.length) {
                var $box = $(this).closest('.pos-bill-search-results, .pos-search-dropdown');
                var linkedInput = $box.data('pos-search-input');
                if (linkedInput) {
                    $input = $(linkedInput);
                }
            }
            if (!$input.length) {
                $input = $('.pos-bill-search-input').filter(':visible').last();
            }
            openQuickAddProductFromSearch($input);
        });

        // On New Sales, header "Quick Product" opens create modal (not only search).
        $(document).on('click', '[data-bs-target="#quickProductModal"]', function(e) {
            e.preventDefault();
            e.stopPropagation();
            openQuickAddProductModal({});
        });

        function submitCreateProduct() {
            if (!validateProductModalForCreate()) {
                return;
            }

            let $btn = $("#saveProductBtn");
            let originalText = $btn.text();
            let formData = buildProductModalFormData();

            $btn.html(
                '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...'
            ).prop('disabled', true);

            $.ajax({
                url: '/api/createProduct',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Authorization': 'Bearer ' + authToken,
                },
                data: formData,
                processData: false,
                contentType: false,
                success: async function(response) {
                    $btn.html(originalText).prop('disabled', false);
                    if (!response.status || !response.product) {
                        Swal.fire('Error', response.message || 'Failed to create product', 'error');
                        return;
                    }

                    $("#addProductModal").modal("hide");

                    try {
                        const added = await window.addProductToCart(response.product);
                        if (typeof showScanToast === 'function') {
                            showScanToast('success', added
                                ? ((response.product.name || 'Product') + ' added')
                                : 'Product created');
                        }
                    } catch (e) {
                        console.error('Quick add cart error', e);
                        Swal.fire('Created', 'Product saved, but could not add to bill automatically.', 'warning');
                    }
                },
                error: function(xhr) {
                    $btn.html(originalText).prop('disabled', false);
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        for (let key in errors) {
                            let errKey = key.replace(/\.\d+$/, '').replace(/\./g, '_');
                            let $err = $("#product_modal_" + errKey + "_error");
                            if ($err.length) {
                                $err.text(errors[key][0]);
                            }
                        }
                        let firstMsg = Object.values(errors)[0];
                        Swal.fire('Validation', (firstMsg && firstMsg[0]) ? firstMsg[0] : 'Please check the form.', 'warning');
                    } else {
                        let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to create product';
                        Swal.fire('Error', msg, 'error');
                    }
                }
            });
        }

        $(document).on('change', '#product_modal_gst_option', function() {
            if ($(this).val() === 'with_gst') {
                $('#product_modal_tax_wrapper').removeClass('d-none');
            } else {
                $('#product_modal_tax_wrapper').addClass('d-none');
            }
        });
    </script>
    <script>
        // ==================== BARCODE SCANNER ====================
        // let html5QrCode = null;
        // ✅ MUST be defined BEFORE initScanner calls it
        // async function onScanSuccess(decodedText) {
        //     console.log("Scan success:", decodedText);
        //     stopScanner();

        //     document.activeElement && document.activeElement.blur();
        //     $('#barcodeScannerModal').modal('hide');
        //     $('#scan-message').text('');

        //     try {
        //         const product = await fetchProductByBarcode(decodedText);
        //         if (product) {
        //             addProductToCart(product);
        //             Swal.fire({
        //                 icon: 'success',
        //                 title: 'Product Added',
        //                 text: product.name + ' added to cart',
        //                 timer: 1500,
        //                 showConfirmButton: false
        //             });
        //         } else {
        //             Swal.fire({
        //                 icon: 'error',
        //                 title: 'Product Not Found',
        //                 text: 'No product with barcode: ' + decodedText
        //             });
        //         }
        //     } catch (error) {
        //         Swal.fire({
        //             icon: 'error',
        //             title: 'Error',
        //             text: error || 'Failed to fetch product. Please try again.'
        //         });
        //     }
        // }
        let html5QrCode = null;

        function showScanToast(icon, title) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1400,
                timerProgressBar: true
            });
            Toast.fire({
                icon: icon,
                title: title
            });
        }

        function playBeep() {
            try {
            const audioCtx = new(window.AudioContext || window.webkitAudioContext)();

            function beep(time, freq) {
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();

                osc.connect(gain);
                gain.connect(audioCtx.destination);

                osc.type = "square";
                osc.frequency.setValueAtTime(freq, audioCtx.currentTime + time);

                gain.gain.setValueAtTime(1, audioCtx.currentTime + time);

                osc.start(audioCtx.currentTime + time);
                osc.stop(audioCtx.currentTime + time + 0.2);
            }

            beep(0, 1200);
            beep(0.25, 1500);
            } catch (e) {}
        }

        window.posBarcodeScanQueue = (function() {
            const queue = [];
            let processing = false;
            let activeCode = '';
            const RAPID_DUPLICATE_MS = 450;
            let lastEnqueuedCode = '';
            let lastEnqueuedAt = 0;
            const productCache = new Map();
            const CACHE_MS = 30000;

            function normalizeCode(code) {
                return String(code || '').trim();
            }

            function isDuplicate(code) {
                const now = Date.now();
                if (code === lastEnqueuedCode && (now - lastEnqueuedAt) < RAPID_DUPLICATE_MS) {
                    return true;
                }
                return false;
            }

            function markSeen(code) {
                lastEnqueuedCode = code;
                lastEnqueuedAt = Date.now();
            }

            function updateQueueMessage() {
                if (!$('#barcodeScannerModal').hasClass('show')) {
                    return;
                }
                const waiting = queue.length;
                if (processing) {
                    if (waiting > 0) {
                        $('#scan-message').text('Adding product... ' + waiting + ' more in queue').css('color', '#ff9f43');
                    } else {
                        $('#scan-message').text('Adding product...').css('color', '#ff9f43');
                    }
                    return;
                }
                if (waiting > 0) {
                    $('#scan-message').text(waiting + ' scan(s) queued...').css('color', '#ff9f43');
                } else {
                    $('#scan-message').text('Point camera at barcode — scan next product').css('color', 'green');
                }
            }

            function fetchCached(code) {
                const cached = productCache.get(code);
                if (cached && (Date.now() - cached.at) < CACHE_MS) {
                    return Promise.resolve(cached.product);
                }
                return fetchProductByBarcode(code).then(function(product) {
                if (product) {
                        productCache.set(code, { product: product, at: Date.now() });
                    }
                    return product;
                });
            }

            async function processOne(item) {
                const code = item.code;
                const opts = item.options || {};
                activeCode = code;
                updateQueueMessage();

                if (opts.playBeep !== false && typeof playBeep === 'function') {
                    playBeep();
                }

                try {
                    const product = opts.product || await fetchCached(code);
                    if (!product) {
                        if (opts.showErrors !== false) {
                            showScanToast('warning', 'Product not found — Quick Add');
                        }
                        if ($('#barcodeScannerModal').hasClass('show')) {
                            $('#scan-message').text('Not found: ' + code + ' — add product').css('color', 'red');
                        }
                        if (typeof window.openQuickAddProductModal === 'function') {
                            window.openQuickAddProductModal(code);
                        }
                        return false;
                    }

                    if (opts.focusBillSearch) {
                        window.posBillSearchFocusNext = true;
                    }

                    const added = await addProductToCart(product);
                    if (added) {
                        if (typeof window.renderScannerCartList === 'function') {
                            window.renderScannerCartList();
                        }
                        if (opts.showErrors !== false) {
                            showScanToast('success', product.name + ' added');
                        }
                        if ($('#barcodeScannerModal').hasClass('show')) {
                            $('#scan-message').text('Added: ' + product.name + ' — scan next product').css('color', 'green');
                        }
                        return true;
                    }

                    if ($('#barcodeScannerModal').hasClass('show')) {
                        $('#scan-message').text('Select a selling rate, then scan the next product').css('color', '#ff9f43');
                    }
                    return false;
            } catch (error) {
                    console.error('Barcode scan queue error', error);
                    if (opts.showErrors !== false) {
                        showScanToast('error', 'Failed to fetch product');
                    }
                    if ($('#barcodeScannerModal').hasClass('show')) {
                        $('#scan-message').text('Error fetching product — try again').css('color', 'red');
                    }
                    return false;
                }
            }

            async function drainQueue() {
                if (processing) {
                    return;
                }
                processing = true;
                updateQueueMessage();

                while (queue.length > 0) {
                    const item = queue.shift();
                    await processOne(item);
                    if (queue.length > 0) {
                        await new Promise(function(resolve) {
                            setTimeout(resolve, 120);
                        });
                    }
                }

                processing = false;
                activeCode = '';
                updateQueueMessage();
            }

            return {
                enqueue: function(code, options) {
                    code = normalizeCode(code);
                    if (!code) {
                        return false;
                    }
                    if (isDuplicate(code)) {
                        return false;
                    }
                    markSeen(code);
                    queue.push({ code: code, options: options || {} });
                    drainQueue();
                    return true;
                },
                reset: function() {
                    queue.length = 0;
                    processing = false;
                    activeCode = '';
                    lastEnqueuedCode = '';
                    lastEnqueuedAt = 0;
                },
                getPendingCount: function() {
                    return queue.length + (processing ? 1 : 0);
                }
            };
        })();

        async function onScanSuccess(decodedText) {
            const barcode = String(decodedText || '').trim();
            if (!barcode) {
                return;
            }
            window.posBarcodeScanQueue.enqueue(barcode, {
                focusBillSearch: false,
                playBeep: true,
                showErrors: true
            });
        }

        function onScanError(errorMessage) {
            console.debug("Scan error:", errorMessage);
        }

        function stopScanner() {
            if (!html5QrCode) return;

            if (html5QrCode.isScanning) {
                html5QrCode.stop()
                    .then(() => {
                        html5QrCode = null;
                    })
                    .catch(err => {
                        console.error("Stop error:", err);
                        html5QrCode = null;
                    });
            } else {
                html5QrCode = null;
            }
            $('#scan-message').text('');
        }

        function waitForLibrary(callback, retries = 20) {
            if (typeof Html5Qrcode !== "undefined") {
                callback();
            } else if (retries > 0) {
                setTimeout(() => waitForLibrary(callback, retries - 1), 200);
            } else {
                $('#scan-message').text('Scanner library failed to load. Please refresh.').css('color', 'red');
            }
        }

        function startScanner() {
            console.log("startScanner called");
            waitForLibrary(function() {
                if (html5QrCode) {
                    let stopPromise = html5QrCode.isScanning ?
                        html5QrCode.stop() :
                        Promise.resolve();
                    stopPromise.then(() => {
                        html5QrCode = null;
                        initScanner();
                    }).catch(() => {
                        html5QrCode = null;
                        initScanner();
                    });
                } else {
                    initScanner();
                }
            });
        }

        // function initScanner() {
        //     try {
        //         html5QrCode = new Html5Qrcode("qr-reader");
        //     } catch (e) {
        //         console.error("Failed to create Html5Qrcode:", e);
        //         $('#scan-message').text('Failed to initialize scanner.').css('color', 'red');
        //         return;
        //     }

        //     const config = {
        //         fps: 10,
        //         qrbox: {
        //             width: 250,
        //             height: 250
        //         }
        //     };
        //     $('#scan-message').text('Starting camera...').css('color', '');

        //     // Try rear camera first
        //     html5QrCode.start({
        //             facingMode: "environment"
        //         },
        //         config,
        //         onScanSuccess,
        //         onScanError
        //     ).then(() => {
        //         $('#scan-message').text('Point camera at barcode').css('color', 'green');
        //     }).catch(() => {
        //         console.warn("Rear camera failed, trying front camera...");
        //         html5QrCode.start({
        //                 facingMode: "user"
        //             },
        //             config,
        //             onScanSuccess,
        //             onScanError
        //         ).then(() => {
        //             $('#scan-message').text('Point camera at barcode').css('color', 'green');
        //         }).catch(() => {
        //             console.warn("Front camera also failed, trying any camera...");
        //             Html5Qrcode.getCameras().then(cameras => {
        //                 if (cameras && cameras.length > 0) {
        //                     html5QrCode.start(
        //                         cameras[0].id,
        //                         config,
        //                         onScanSuccess,
        //                         onScanError
        //                     ).then(() => {
        //                         $('#scan-message').text('Point camera at barcode').css(
        //                             'color', 'green');
        //                     }).catch(err => {
        //                         console.error("All camera attempts failed:", err);
        //                         html5QrCode = null;
        //                         showManualBarcodeInput();
        //                     });
        //                 } else {
        //                     html5QrCode = null;
        //                     showManualBarcodeInput();
        //                 }
        //             }).catch(() => {
        //                 html5QrCode = null;
        //                 showManualBarcodeInput();
        //             });
        //         });
        //     });
        // }
        function initScanner() {
            try {
                html5QrCode = new Html5Qrcode("qr-reader");
            } catch (e) {
                console.error("Failed to create Html5Qrcode:", e);
                return;
            }

            const config = {
                fps: 10,
                qrbox: function(viewfinderWidth, viewfinderHeight) {
                    return {
                        width: Math.max(220, Math.min(320, Math.floor(viewfinderWidth * 0.9))),
                        height: Math.max(80, Math.min(140, Math.floor(viewfinderHeight * 0.35)))
                    };
                }
            };

            if (typeof Html5QrcodeSupportedFormats !== 'undefined') {
                config.formatsToSupport = [
                    Html5QrcodeSupportedFormats.CODE_128,
                    Html5QrcodeSupportedFormats.CODE_39,
                    Html5QrcodeSupportedFormats.CODE_93,
                    Html5QrcodeSupportedFormats.QR_CODE,
                    Html5QrcodeSupportedFormats.DATA_MATRIX,
                    Html5QrcodeSupportedFormats.EAN_13,
                    Html5QrcodeSupportedFormats.EAN_8,
                    Html5QrcodeSupportedFormats.UPC_A,
                    Html5QrcodeSupportedFormats.UPC_E,
                    Html5QrcodeSupportedFormats.ITF,
                    Html5QrcodeSupportedFormats.CODABAR
                ];
            }

            $('#scan-message').text('Starting camera...');

            // 🔥 NEW CODE (IMPORTANT)
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    $('#qr-reader').show();
                    $('#manual_entry_text_pos').html('Or enter barcode manually below:');

                    let cameraId = devices.find(device =>
                        device.label.toLowerCase().includes('back') ||
                        device.label.toLowerCase().includes('rear')
                    )?.id;

                    // fallback
                    if (!cameraId) {
                        cameraId = devices[0].id;
                    }

                    html5QrCode.start(
                        cameraId,
                        config,
                        onScanSuccess,
                        onScanError
                    ).then(() => {
                        $('#barcodeScannerModal').removeClass('scanner-manual-only');
                        $('#scan-message').text('Point camera at barcode').css('color', 'green');
                        syncScannerTorchButton();
                    }).catch(err => {
                        console.error("Camera start failed:", err);
                        $('#scan-message').text('Unable to start camera.');
                        $('#barcodeScannerModal').addClass('scanner-manual-only');
                    });

                } else {
                    console.log("No camera found");
                    $('#qr-reader').hide();
                    $('#scan-message').hide();
                    $('#barcodeScannerModal').addClass('scanner-manual-only');
                    $('#manual_entry_text_pos').html('No camera found on this device.<br>Enter barcode manually below:');
                }
            }).catch(err => {
                console.error("Camera error:", err);
                $('#qr-reader').hide();
                $('#scan-message').hide();
                $('#barcodeScannerModal').addClass('scanner-manual-only');
                $('#manual_entry_text_pos').html('Camera access denied or unsupported.<br>Enter barcode manually below:');
            });
        }
        
        $(document).on('click', '#manualBarcodeSubmit', function() {
            handleManualBarcode();
        });

        $(document).on('keydown', '#manualBarcodeInput', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                handleManualBarcode();
            }
        });

        async function handleManualBarcode() {
            let barcode = $('#manualBarcodeInput').val().trim();

            if (!barcode) {
                $('#manualBarcodeError').text('Please enter a barcode.');
                return;
            }

            $('#manualBarcodeError').text('');
            const enqueued = window.posBarcodeScanQueue.enqueue(barcode, {
                focusBillSearch: false,
                playBeep: true,
                showErrors: true
            });

            if (enqueued) {
                $('#manualBarcodeInput').val('');
                // Don't steal focus while Select Rate / other Swal dialogs are open over this modal
                setTimeout(function() {
                    if (typeof Swal !== 'undefined' && Swal.isVisible()) {
                        return;
                    }
                    if ($('#barcodeScannerModal').hasClass('show')) {
                        $('#manualBarcodeInput').trigger('focus');
                    }
                }, 50);
            } else {
                $('#manualBarcodeError').text('Please wait — previous scan is still processing.');
            }
        }

        function fetchProductByBarcode(barcode) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '/api/product-by-barcode/' + encodeURIComponent(barcode),
                    type: 'GET',
                    headers: {
                        "Authorization": "Bearer " + authToken,
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        resolve(response.status && response.product ? response.product : null);
                    },
                    error: function(xhr) {
                        reject(xhr.responseJSON?.message || 'Network error');
                    }
                });
            });
        }

        function isQuotationModeEnabled() {
            return $('.quotationToggle:checked').length > 0 ||
                new URLSearchParams(window.location.search).get('sale_type') === 'quotation';
        }

        function isWarrantyCategoryProduct(product) {
            if (!product) return false;
            if (product.isWarrantyCategory === true || product.is_warranty_category === true) return true;
            if (String(product.isWarrantyCategory || product.is_warranty_category || '').toLowerCase() === 'true')
                return true;
            if (String(product.isWarrantyCategory || product.is_warranty_category || '') === '1') return true;

            const categoryName = String(product.category_name || product.categoryName || product.category?.name || '')
                .trim().toLowerCase();
            return categoryName === 'warranty';
        }

        /**
         * Add product to cart (works for both barcode and manual search)
         * FIX: Ensure product ID is stored as a string so that Map keys match
         */
        /**
         * Add product to cart (works for barcode scan, manual entry, or any product source)
         * @param {Object} product - Product object from API or search
         */
        
        // Global helpers to avoid ReferenceError when called from addProductToCart
        window.parseItemPriceValue = function(price) {
            return parseFloat(String(price).replace(/[^0-9.-]+/g, "")) || 0;
        };
        window.parseQuantityValue = function(quantity) {
            const parsedQuantity = parseFloat(String(quantity).replace(/[^0-9.-]+/g, ""));
            return Number.isFinite(parsedQuantity) ? parsedQuantity : 0;
        };
        window.posScanRateMemory = window.posScanRateMemory || {};

        function getProductSellingRates(product) {
            const raw = product && (product.selling_prices || product.selling_price_options || product.sellingPrices);
            if (!Array.isArray(raw)) {
                return [];
            }
            return raw.filter(function(r) {
                return r && r.price !== '' && r.price !== null && !isNaN(parseFloat(r.price));
            });
        }

        function isWholesaleRateName(name) {
            return /wholesale/i.test(String(name || '').trim());
        }

        /** Retail rates only (hide Wholesale rows until Wholesale mode is on). */
        window.getRetailSellingRatesForPicker = function(rates) {
            if (!Array.isArray(rates)) {
                return [];
            }
            return rates.filter(function(r) {
                if (!r || r.price === '' || r.price === null || isNaN(parseFloat(r.price))) {
                    return false;
                }
                return !isWholesaleRateName(r.name);
            });
        };

        function isWholesaleModeEnabled() {
            return $('.wholesaleToggle:checked').length > 0;
        }

        window.resolveWholesalePriceFromRates = function(rates, retailPrice) {
            const wholesaleRates = window.getWholesaleRatesFromSellingRates(rates, retailPrice);
            return wholesaleRates.length ? wholesaleRates[0].price : null;
        };

        window.getWholesaleRatesFromSellingRates = function(rates, retailPrice) {
            if (!Array.isArray(rates) || !rates.length) {
                return [];
            }
            const retail = retailPrice !== null && retailPrice !== undefined && retailPrice !== ''
                ? parseFloat(retailPrice)
                : null;
            const options = [];
            const seen = {};

            function pushOption(name, price, mrp) {
                const parsedPrice = parseFloat(price);
                if (!Number.isFinite(parsedPrice)) {
                    return;
                }
                const key = parsedPrice.toFixed(4);
                if (seen[key]) {
                    return;
                }
                seen[key] = true;
                const label = String(name || '').trim();
                options.push({
                    name: label !== '' ? label : ('Wholesale ' + (options.length + 1)),
                    price: parsedPrice,
                    mrp: mrp !== undefined && mrp !== null && mrp !== '' ? parseFloat(mrp) : null
                });
            }

            rates.forEach(function(rate) {
                const name = String(rate.name || '').trim();
                if (name && /wholesale/i.test(name)) {
                    pushOption(name, rate.price, rate.mrp);
                }
            });

            rates.forEach(function(rate) {
                const price = parseFloat(rate.price);
                if (!Number.isFinite(price)) {
                    return;
                }
                const name = String(rate.name || '').trim();
                const isRetail = retail !== null && Number.isFinite(retail) && Math.abs(price - retail) < 0.001;
                const isRate1 = /^rate\s*1$/i.test(name);
                const isWholesaleNamed = name !== '' && /wholesale/i.test(name);
                if (isWholesaleNamed || (isRate1 && isRetail) || (isRetail && !isWholesaleNamed)) {
                    return;
                }
                pushOption(name, price, rate.mrp);
            });

            return options;
        };

        function getProductWholesaleRates(product, retailPrice) {
            if (!product) {
                return [];
            }
            const retail = retailPrice !== null && retailPrice !== undefined && retailPrice !== ''
                ? parseFloat(retailPrice)
                : parseFloat(product.price);
            const sellingRates = getProductSellingRates(product);
            const rates = window.getWholesaleRatesFromSellingRates(sellingRates, Number.isFinite(retail) ? retail : null);

            if (product.wholesale_price !== undefined && product.wholesale_price !== null && product.wholesale_price !== '') {
                const direct = parseFloat(product.wholesale_price);
                if (Number.isFinite(direct)) {
                    const exists = rates.some(function(rate) {
                        return Math.abs(parseFloat(rate.price) - direct) < 0.001;
                    });
                    if (!exists) {
                        rates.unshift({ name: 'Wholesale', price: direct, mrp: null });
                    }
                }
            }

            if (Array.isArray(product.wholesale_rates) && product.wholesale_rates.length) {
                product.wholesale_rates.forEach(function(rate) {
                    const price = parseFloat(rate.price);
                    if (!Number.isFinite(price)) {
                        return;
                    }
                    const exists = rates.some(function(existing) {
                        return Math.abs(parseFloat(existing.price) - price) < 0.001;
                    });
                    if (!exists) {
                        rates.push({
                            name: String(rate.name || 'Wholesale').trim() || 'Wholesale',
                            price: price,
                            mrp: rate.mrp != null && rate.mrp !== '' ? parseFloat(rate.mrp) : null
                        });
                    }
                });
            }

            return rates;
        }

        async function pickWholesalePrice(product, retailPrice) {
            const rates = getProductWholesaleRates(product, retailPrice);
            if (!rates.length) {
                await window.showPosWholesaleRateWarning(product.name || 'Product');
                return null;
            }
            if (rates.length === 1) {
                return [{
                    index: 0,
                    name: rates[0].name || 'Wholesale',
                    price: parseFloat(rates[0].price),
                    mrp: rates[0].mrp != null && rates[0].mrp !== '' ? parseFloat(rates[0].mrp) : null,
                    qty: 1
                }];
            }
            const memoryKey = 'w_' + String(product.id || product.productIdNum || '');
            if (memoryKey && window.posScanRateMemory[memoryKey] != null) {
                const remembered = parseFloat(window.posScanRateMemory[memoryKey]);
                if (Number.isFinite(remembered)) {
                    return [{
                        index: 0,
                        name: 'Wholesale',
                        price: remembered,
                        mrp: null,
                        qty: 1
                    }];
                }
            }
            const selections = await window.showPosSellingRatePicker(rates, 'Select Wholesale Rate', {
                stock: product.quantity,
                productId: product.id
            });
            if (selections && selections.length && memoryKey) {
                window.posScanRateMemory[memoryKey] = selections[0].price;
            }
            return selections;
        }

        function resolveWholesalePrice(product) {
            const rates = getProductWholesaleRates(product);
            if (!rates.length) {
                return null;
            }
            return parseFloat(rates[0].price);
        }

        window.showPosWholesaleRateWarning = function(productName) {
            return Swal.fire({
                icon: 'warning',
                title: 'No wholesale rate',
                text: (productName ? ('"' + productName + '" has ') : 'This product has ') +
                    'no wholesale rate. Add it in product selling rates (e.g. Rate 2 / Wholesale).',
                confirmButtonText: 'OK'
            });
        };

        function applyWholesalePricesToCart(enableWholesale) {
            if (typeof selectedItems === 'undefined' || !selectedItems || typeof selectedItems.forEach !== 'function') {
                return;
            }
            selectedItems.forEach(function(item) {
                if (enableWholesale) {
                    if (item.regular_price === undefined || item.regular_price === null || item.regular_price === '') {
                        item.regular_price = item.price;
                    }
                    const wholesalePrice = item.wholesale_price != null
                        ? parseFloat(item.wholesale_price)
                        : window.resolveWholesalePriceFromRates(item.selling_prices || [], item.regular_price);
                    if (wholesalePrice != null && !isNaN(wholesalePrice)) {
                        item.wholesale_price = wholesalePrice;
                        item.price = wholesalePrice;
                    }
                } else if (item.regular_price != null && item.regular_price !== '') {
                    item.price = item.regular_price;
                }
            });
        }

        /**
         * Bootstrap modals (e.g. Scan Barcode) trap focus and steal it from SweetAlert inputs.
         * Disable that trap + mark the scanner modal inert while Select Rate is open.
         */
        window.posSwalFocusGuard = {
            _handler: null,
            _cameraPaused: false,
            _enforceFocusOrig: null,
            _inertModals: [],
            activate: function() {
                const self = this;
                this.deactivate();

                // 1) Turn off Bootstrap 5 modal focus enforcement (root cause)
                try {
                    if (window.bootstrap && bootstrap.Modal && bootstrap.Modal.prototype) {
                        if (!bootstrap.Modal.prototype._posOrigEnforceFocus) {
                            bootstrap.Modal.prototype._posOrigEnforceFocus = bootstrap.Modal.prototype._enforceFocus;
                        }
                        this._enforceFocusOrig = bootstrap.Modal.prototype._posOrigEnforceFocus;
                        bootstrap.Modal.prototype._enforceFocus = function() {};
                    }
                } catch (err) {}

                // Also clear any jQuery BS focusin handlers if present
                try {
                    $(document).off('focusin.bs.modal');
                } catch (err) {}

                // 2) Make open Bootstrap modals non-interactive so they can't steal clicks/focus
                this._inertModals = [];
                document.querySelectorAll('.modal.show').forEach(function(modalEl) {
                    try {
                        modalEl.setAttribute('inert', '');
                        modalEl.setAttribute('aria-hidden', 'true');
                        self._inertModals.push(modalEl);
                    } catch (err) {}
                });

                // 3) Keep focus inside Swal if something still tries to steal it
                this._handler = function(e) {
                    if (typeof Swal === 'undefined' || !Swal.isVisible()) {
                        return;
                    }
                    const swalEl = Swal.getContainer();
                    if (!swalEl) {
                        return;
                    }
                    if (e.target && swalEl.contains(e.target)) {
                        return;
                    }
                    e.preventDefault();
                    e.stopPropagation();
                    if (typeof e.stopImmediatePropagation === 'function') {
                        e.stopImmediatePropagation();
                    }
                    const activeQty = swalEl.querySelector('.pos-rate-qty:focus');
                    const lastQty = swalEl.querySelector('.pos-rate-qty[data-pos-last-focus="1"]') ||
                        swalEl.querySelector('.pos-rate-qty');
                    const fallback = Swal.getHtmlContainer() || swalEl.querySelector('.swal2-popup');
                    const target = activeQty || lastQty || fallback;
                    if (target && typeof target.focus === 'function') {
                        setTimeout(function() {
                            try {
                                target.focus({ preventScroll: true });
                            } catch (err) {
                                target.focus();
                            }
                        }, 0);
                    }
                };
                document.addEventListener('focusin', this._handler, true);

                // Pause live camera so further scans don't fight the qty inputs
                try {
                    if (typeof html5QrCode !== 'undefined' && html5QrCode && html5QrCode.isScanning &&
                        typeof html5QrCode.pause === 'function') {
                        html5QrCode.pause(true);
                        self._cameraPaused = true;
                    }
                } catch (err) {
                    self._cameraPaused = false;
                }
            },
            deactivate: function() {
                if (this._handler) {
                    document.removeEventListener('focusin', this._handler, true);
                    this._handler = null;
                }

                // Restore Bootstrap focus trap
                try {
                    if (window.bootstrap && bootstrap.Modal && bootstrap.Modal.prototype &&
                        bootstrap.Modal.prototype._posOrigEnforceFocus) {
                        bootstrap.Modal.prototype._enforceFocus = bootstrap.Modal.prototype._posOrigEnforceFocus;
                    }
                } catch (err) {}
                this._enforceFocusOrig = null;

                // Remove inert from scanner/other modals
                (this._inertModals || []).forEach(function(modalEl) {
                    try {
                        modalEl.removeAttribute('inert');
                        // Keep aria-hidden in sync with open state
                        if (modalEl.classList.contains('show')) {
                            modalEl.removeAttribute('aria-hidden');
                        }
                    } catch (err) {}
                });
                this._inertModals = [];

                if (this._cameraPaused) {
                    try {
                        if (typeof html5QrCode !== 'undefined' && html5QrCode &&
                            typeof html5QrCode.resume === 'function') {
                            html5QrCode.resume();
                        }
                    } catch (err) {}
                    this._cameraPaused = false;
                }
                // Return focus to scanner manual input if that modal is still open
                if ($('#barcodeScannerModal').hasClass('show') && $('#manualBarcodeInput').length) {
                    setTimeout(function() {
                        if (typeof Swal === 'undefined' || !Swal.isVisible()) {
                            $('#manualBarcodeInput').trigger('focus');
                        }
                    }, 80);
                }
            }
        };

        window.withPosRateAvailableQty = function(rates, productStock, productPrice) {
            const main = parseFloat(productPrice);
            return (rates || []).map(function(rate) {
                const copy = Object.assign({}, rate);
                const price = parseFloat(rate.price);
                const isMain = Number.isFinite(main) && Number.isFinite(price) && Math.abs(price - main) < 0.001;
                const hasOwn = rate.qty !== undefined && rate.qty !== null && rate.qty !== '';
                if (isMain) {
                    copy.availableQty = window.parseQuantityValue(productStock);
                } else if (hasOwn) {
                    copy.availableQty = window.parseQuantityValue(rate.qty);
                } else {
                    copy.availableQty = 0;
                }
                return copy;
            });
        };

        /**
         * Rate picker: list with qty per rate.
         * Each rate uses its own availableQty. Rate 1 qty is the product quantity.
         * Returns [{ price, qty, name, mrp }, ...] or null.
         */
        window.showPosSellingRatePicker = function(sellingRates, title, options) {
            options = options || {};

            function formatAvailQty(n) {
                const v = window.parseQuantityValue(n);
                return Math.abs(v - Math.round(v)) < 0.0001 ? String(Math.round(v)) : String(Math.round(v * 1000) / 1000);
            }

            function rateAvailable(rate) {
                let base = null;
                if (rate && rate.availableQty !== undefined && rate.availableQty !== null && rate.availableQty !== '') {
                    base = window.parseQuantityValue(rate.availableQty);
                } else if (options.stock !== undefined && options.stock !== null && options.stock !== '') {
                    base = window.parseQuantityValue(options.stock);
                }
                if (base == null) return null;
                let reserved = 0;
                if (options.productId != null && typeof window.posReservedQtyForProductPrice === 'function') {
                    reserved = window.posReservedQtyForProductPrice(options.productId, rate.price);
                }
                // Do not clamp — negative stock (oversell) must remain visible and sellable.
                return base - reserved;
            }

            const rowsHtml = (sellingRates || []).map(function(rate, idx) {
                const name = rate.name || ('Rate ' + (idx + 1));
                const priceTxt = parseFloat(rate.price).toFixed(2);
                const mrpTxt = (rate.mrp !== undefined && rate.mrp !== null && rate.mrp !== '')
                    ? ('MRP ' + parseFloat(rate.mrp).toFixed(2) + ' · ')
                    : '';
                const safeName = String(name).replace(/</g, '&lt;').replace(/>/g, '&gt;');
                const available = rateAvailable(rate);
                const availHtml = available == null
                    ? ''
                    : '<div class="pos-rate-avail' + (available <= 0 ? ' is-low' : '') + '">Available: ' + formatAvailQty(available) + '</div>';
                return ''
                    + '<div class="pos-rate-pick-row" data-idx="' + idx + '" data-available="' + (available == null ? '' : available) + '">'
                    +   '<div style="min-width:0;flex:1;">'
                    +     '<div class="pos-rate-name">' + safeName + '</div>'
                    +     '<div class="pos-rate-meta">' + mrpTxt + 'Rate ₹' + priceTxt + '</div>'
                    +     availHtml
                    +   '</div>'
                    +   '<div class="pos-rate-qty-wrap">'
                    +     '<label>Qty</label>'
                    +     '<input type="text" class="form-control form-control-sm pos-rate-qty" inputmode="decimal" autocomplete="off" value="" placeholder="0" data-idx="' + idx + '">'
                    +   '</div>'
                    + '</div>';
            }).join('');

            const hint = 'Each price has its own available qty. Enter qty, then click Add. Selling with 0 or negative stock is allowed.';

            return Swal.fire({
                title: title || 'Select Rate',
                html: '<div class="pos-rate-pick-list">'
                    + rowsHtml
                    + '<div class="pos-rate-pick-hint">' + hint + '</div>'
                    + '</div>',
                showCancelButton: true,
                confirmButtonText: 'Add',
                cancelButtonText: 'Cancel',
                allowOutsideClick: false,
                allowEnterKey: false,
                focusConfirm: false,
                customClass: {
                    container: 'swal-over-pos-modal'
                },
                didOpen: function() {
                    if (window.posSwalFocusGuard) {
                        window.posSwalFocusGuard.activate();
                    }
                    const container = Swal.getHtmlContainer();
                    const first = container && container.querySelector('.pos-rate-qty');
                    if (first) {
                        first.setAttribute('data-pos-last-focus', '1');
                        setTimeout(function() {
                            try {
                                first.focus({ preventScroll: true });
                                first.select();
                            } catch (err) {
                                first.focus();
                            }
                        }, 50);
                    }
                    // Ensure click/tap can focus qty even over Bootstrap modal
                    $(container).on('mousedown.posRatePick click.posRatePick', '.pos-rate-qty', function(e) {
                        e.stopPropagation();
                        container.querySelectorAll('.pos-rate-qty').forEach(function(el) {
                            el.removeAttribute('data-pos-last-focus');
                        });
                        this.setAttribute('data-pos-last-focus', '1');
                        if (document.activeElement !== this) {
                            try {
                                this.focus({ preventScroll: true });
                            } catch (err) {
                                this.focus();
                            }
                        }
                    });
                    $(container).on('focusin.posRatePick', '.pos-rate-qty', function() {
                        container.querySelectorAll('.pos-rate-qty').forEach(function(el) {
                            el.removeAttribute('data-pos-last-focus');
                        });
                        this.setAttribute('data-pos-last-focus', '1');
                    });
                    $(container).on('keydown.posRatePick', '.pos-rate-qty', function(e) {
                        // Allow typing digits; only Enter confirms
                        e.stopPropagation();
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            Swal.clickConfirm();
                        }
                    });
                    // Block invalid chars in qty (text input used so BS focus trap / spinner don't fight number inputs)
                    function refreshRateAvailability() {
                        if (!container) return;
                        container.querySelectorAll('.pos-rate-qty').forEach(function(input) {
                            const row = input.closest('.pos-rate-pick-row');
                            if (!row || row.getAttribute('data-available') === '') return;
                            const available = window.parseQuantityValue(row.getAttribute('data-available'));
                            const q = parseFloat(String(input.value || '').trim());
                            const entered = Number.isFinite(q) && q > 0 ? q : 0;
                            // Allow oversell / negative stock — show remaining even when below zero.
                            const left = available - entered;
                            const el = row.querySelector('.pos-rate-avail');
                            if (!el) return;
                            el.textContent = 'Available: ' + formatAvailQty(left);
                            el.classList.toggle('is-low', left <= 0);
                        });
                    }
                    $(container).on('input.posRatePick', '.pos-rate-qty', function() {
                        let v = String(this.value || '').replace(/[^0-9.]/g, '');
                        const parts = v.split('.');
                        if (parts.length > 2) {
                            v = parts[0] + '.' + parts.slice(1).join('');
                        }
                        if (this.value !== v) {
                            this.value = v;
                        }
                        refreshRateAvailability();
                    });
                    refreshRateAvailability();
                },
                willClose: function() {
                    const container = Swal.getHtmlContainer();
                    if (container) {
                        $(container).off('.posRatePick');
                    }
                    if (window.posSwalFocusGuard) {
                        window.posSwalFocusGuard.deactivate();
                    }
                },
                preConfirm: function() {
                    const container = Swal.getHtmlContainer();
                    if (!container) {
                        return false;
                    }
                    const selections = [];
                    container.querySelectorAll('.pos-rate-pick-row').forEach(function(row) {
                        const idx = parseInt(row.getAttribute('data-idx'), 10);
                        const qtyInput = row.querySelector('.pos-rate-qty');
                        const qtyRaw = qtyInput ? String(qtyInput.value || '').trim() : '';
                        if (qtyRaw === '') {
                            return;
                        }
                        const qty = parseFloat(qtyRaw);
                        if (!Number.isFinite(qty) || qty <= 0) {
                            return;
                        }
                        const rate = sellingRates[idx];
                        if (!rate) {
                            return;
                        }
                        const price = parseFloat(rate.price);
                        if (!Number.isFinite(price)) {
                            return;
                        }
                        selections.push({
                            index: idx,
                            name: rate.name || ('Rate ' + (idx + 1)),
                            price: price,
                            mrp: rate.mrp != null && rate.mrp !== '' ? parseFloat(rate.mrp) : null,
                            qty: qty
                        });
                    });
                    if (!selections.length) {
                        Swal.showValidationMessage('Enter quantity for at least one rate');
                        return false;
                    }
                    // Negative / oversell buying is allowed — do not block when qty > available.
                    return selections;
                }
            }).then(function(result) {
                if (!result.isConfirmed || !result.value || !result.value.length) {
                    return null;
                }
                return result.value;
            });
        };

        window.addPosProductRateSelections = function(baseProductData, selections) {
            if (!selections || !selections.length || typeof window.executeAddProduct !== 'function') {
                return;
            }
            selections.forEach(function(sel) {
                const data = Object.assign({}, baseProductData, {
                    productPrice: sel.price,
                    _priceResolved: true
                });
                window.executeAddProduct(data, null, [], sel.qty);
            });
        };

        function resolveBarcodeProductImage(product) {
            let productImage = '{{ env('ImagePath') }}/admin/assets/img/product/noimage.png';
            const imageSource = product.image_url || product.image;
            if (!imageSource) {
                return productImage;
            }
                try {
                let cleanImage = String(imageSource).replace(/\\/g, '').replace(/^"(.*)"$/, '$1');
                if (cleanImage.startsWith('http://') || cleanImage.startsWith('https://') || cleanImage.startsWith('/')) {
                    return cleanImage;
                }
                    let basePath = '{{ env('ImagePath') }}';
                    if (cleanImage.startsWith('[')) {
                        let imagesArray = JSON.parse(cleanImage);
                        if (Array.isArray(imagesArray) && imagesArray.length > 0) {
                            productImage = `${basePath}/storage/${imagesArray[0]}`;
                        }
                    } else {
                        productImage = `${basePath}/storage/${cleanImage}`;
                    }
                } catch (e) {
                    console.warn('Image parse error, using default');
                }
            return productImage;
        }

        /**
         * Add product to cart (barcode scan, manual entry, or any product source).
         * Uses the same checkout path as clicking a product card, including selling rate.
         */
        async function addProductToCart(product, initialQty) {
            if (!product) {
                return false;
            }
            const qtyToAdd = window.parseQuantityValue(
                initialQty != null ? initialQty : (product._pendingQty != null ? product._pendingQty : 1)
            );
            const addQty = qtyToAdd > 0 ? qtyToAdd : 1;

            const sellingRates = getProductSellingRates(product);
            let finalPrice = parseFloat(product.price);
            const retailPrice = Number.isFinite(finalPrice) ? finalPrice : parseFloat(sellingRates[0]?.price);

            const fromBarcode = !!(product.matched_barcode || product.rate_from_barcode);
            const wholesaleMode = isWholesaleModeEnabled();
            const hasBarcodePrice = fromBarcode && Number.isFinite(finalPrice);

            // Build cart payload once for multi-rate add (click or scan)
            function buildPosAddBaseData(defaultPrice) {
                const baseId = parseInt(product.id, 10) || 0;
                let productGst = product.product_gst || null;
                if (productGst && typeof productGst === 'string') {
                    try { productGst = JSON.parse(productGst.replace(/\\/g, '')); } catch (e) { productGst = null; }
                }
                if (!Array.isArray(productGst) || !productGst.length) productGst = null;
            let gstOption = product.gst_option || 'without_gst';
                if (!productGst) gstOption = 'without_gst';
                return {
                    productIdStr: String(baseId),
                    productIdNum: baseId,
                    categoryId: product.category_id || product.categoryId || 0,
                    productName: product.name || 'Unknown',
                    productPrice: defaultPrice,
                    regularPrice: retailPrice,
                    wholesalePrice: resolveWholesalePrice(product),
                    sellingPrices: sellingRates,
                    purchasePrice: parseFloat(product.purchase_price) || 0,
                    productImage: resolveBarcodeProductImage(product),
                    productStock: window.parseQuantityValue(product.quantity),
                    isWarrantyCategory: isWarrantyCategoryProduct(product),
                    gstOption: gstOption,
                    productGst: productGst,
                    $checkIcon: null,
                    isRentAvailable: Number(product.is_rent_available || 0),
                    rentPeriod: product.rent_period || null,
                    rentPrice: product.rent_price || null,
                    variant_id: product.matched_variant_id || product.variant_id || null,
                    _priceResolved: true
                };
            }

            if (wholesaleMode) {
                const wholesaleSelections = await pickWholesalePrice(product, retailPrice);
                if (!wholesaleSelections || !wholesaleSelections.length) {
                    return false;
                }
                window.addPosProductRateSelections(
                    buildPosAddBaseData(wholesaleSelections[0].price),
                    wholesaleSelections
                );
                return true;
            }

            // Same Select Rate (list + qty) popup for product click AND barcode scan
            // Wholesale rates stay hidden unless Wholesale mode is enabled (handled above)
            const retailRates = window.withPosRateAvailableQty(
                window.getRetailSellingRatesForPicker(sellingRates),
                product.quantity,
                product.price
            );
            if (!product._ratePicked && retailRates.length > 1) {
                const selections = await window.showPosSellingRatePicker(retailRates, 'Select Rate', {
                    productId: product.id
                });
                if (!selections || !selections.length) {
                    return false;
                }
                window.addPosProductRateSelections(
                    buildPosAddBaseData(selections[0].price),
                    selections
                );
                return true;
            }

            if (hasBarcodePrice) {
                product._ratePicked = true;
            } else if (retailRates.length === 1 && (isNaN(finalPrice) || product._ratePicked !== true)) {
                finalPrice = parseFloat(retailRates[0].price);
            } else if (sellingRates.length === 1 && (isNaN(finalPrice) || product._ratePicked !== true)) {
                finalPrice = parseFloat(sellingRates[0].price);
            }

            if (!Number.isFinite(finalPrice)) {
                finalPrice = 0;
            }

            product.price = finalPrice;
            product._ratePicked = true;

            const numericId = parseInt(product.id, 10) || 0;
            let productIdStr = String(numericId);
            if (product.matched_variant_id) {
                productIdStr = productIdStr + '_v' + product.matched_variant_id;
            } else if (fromBarcode && Number.isFinite(finalPrice)) {
                productIdStr = productIdStr + '_r' + String(finalPrice);
            }

            let productGst = product.product_gst || null;
            if (productGst) {
                if (typeof productGst === 'string') {
                    productGst = productGst.replace(/\\/g, '');
                    if (productGst === 'null' || productGst === '' || productGst === '[]') {
                        productGst = null;
                    } else {
                        try {
                            productGst = JSON.parse(productGst);
                        } catch (e) {
                            productGst = null;
                        }
                    }
                }
                if (!Array.isArray(productGst) || productGst.length === 0) {
                    productGst = null;
                }
            }

            let gstOption = product.gst_option || 'without_gst';
            if (gstOption !== 'with_gst' && gstOption !== 'without_gst') {
                gstOption = 'without_gst';
            }
            if (!productGst) {
                gstOption = 'without_gst';
            }

            const productData = {
                productIdStr: productIdStr,
                productIdNum: numericId,
                categoryId: product.category_id || product.categoryId || 0,
                productName: product.name || 'Unknown',
                productPrice: finalPrice,
                regularPrice: retailPrice,
                wholesalePrice: resolveWholesalePrice(product),
                sellingPrices: sellingRates,
                purchasePrice: parseFloat(product.purchase_price) || 0,
                productImage: resolveBarcodeProductImage(product),
                productStock: window.parseQuantityValue(product.quantity),
                isWarrantyCategory: isWarrantyCategoryProduct(product),
                gstOption: gstOption,
                productGst: productGst,
                $checkIcon: null,
                isRentAvailable: Number(product.is_rent_available || 0),
                rentPeriod: product.rent_period || null,
                rentPrice: product.rent_price || null,
                variant_id: product.matched_variant_id || product.variant_id || null,
                _priceResolved: !!product._ratePicked
            };

            if (typeof window.executeAddProduct === 'function') {
                window.lastScannedCartId = productIdStr;
                window.executeAddProduct(productData, product._pendingImei || null, [], addQty);
                return true;
            }

            const cart = (typeof window.getPosSelectedItems === 'function')
                ? window.getPosSelectedItems()
                : (typeof selectedItems !== 'undefined' ? selectedItems : null);
            if (cart && typeof cart.set === 'function') {
                const parseQuantityValue = window.parseQuantityValue;
                window.lastScannedCartId = productIdStr;
                if (cart.has(productIdStr)) {
                    let item = cart.get(productIdStr);
                    item.quantity = parseQuantityValue(item.quantity) + addQty;
                    item.addedAt = Date.now();
                    cart.set(productIdStr, item);
                } else {
                    cart.set(productIdStr, {
                        id: productIdStr,
                        productId: numericId,
                        categoryId: productData.categoryId,
                        name: productData.productName,
                        price: finalPrice,
                        purchase_price: productData.purchasePrice,
                        image: productData.productImage,
                        quantity: addQty,
                        variant_id: productData.variant_id,
                        stock: productData.productStock,
                        isWarrantyCategory: productData.isWarrantyCategory,
                        gst_option: gstOption,
                        product_gst: productGst,
                        discount_percentage: 0,
                        discount_amount: 0,
                        is_rent_available: productData.isRentAvailable,
                        rent_period: productData.rentPeriod,
                        rent_price: productData.rentPrice,
                        regular_price: finalPrice,
                        rent_value: 1,
                        addedAt: Date.now()
                    });
                }
                if (typeof window.updateTotalItems === 'function') {
                    window.updateTotalItems();
                }
                return true;
            }

            return false;
        }
        window.addProductToCart = addProductToCart;

        async function handlePosBarcodeScan(barcode, options) {
            return window.posBarcodeScanQueue.enqueue(barcode, Object.assign({
                focusBillSearch: true,
                playBeep: true,
                showErrors: true,
                product: null
            }, options || {}));
        }
        window.handlePosBarcodeScan = handlePosBarcodeScan;

        (function initPosGlobalBarcodeScanner() {
            let buffer = '';
            let lastKeyTime = 0;
            const MAX_GAP_MS = 120;
            const MIN_BARCODE_LEN = 2;

            function isPosBarcodeCaptureTarget() {
                const el = document.activeElement;
                if (!el) {
                    return true;
                }
                const $el = $(el);
                if ($el.is('#manualBarcodeInput, .pos-bill-search-input')) {
                    return false;
                }
                if ($el.is('input, textarea, select, [contenteditable="true"]')) {
                    return false;
                }
                if ($('.swal2-container:visible').length) {
                    return false;
                }
                if ($('#barcodeScannerModal').hasClass('show')) {
                    return false;
                }
                if ($('#addProductModal').hasClass('show')) {
                    return false;
                }
                return true;
            }

            $(document).on('keydown.posGlobalBarcode', function(e) {
                if (!isPosBarcodeCaptureTarget()) {
                    if (!$(e.target).is('#manualBarcodeInput, .pos-bill-search-input')) {
                        buffer = '';
                    }
                    return;
                }

                if (e.ctrlKey || e.metaKey || e.altKey) {
                return;
            }

                const now = Date.now();
                if (now - lastKeyTime > MAX_GAP_MS) {
                    buffer = '';
                }
                lastKeyTime = now;

                if (e.key === 'Enter') {
                    const code = buffer.trim();
                    buffer = '';
                    if (code.length < MIN_BARCODE_LEN) {
                        return;
                    }
                    e.preventDefault();
                    e.stopPropagation();
                    window.handlePosBarcodeScan(code, {
                        focusBillSearch: true,
                        playBeep: true
                    });
                    return;
                }

                if (e.key.length === 1) {
                    buffer += e.key;
                }
            });
        })();

        let deviceScanPollTimer = null;
        let deviceScanRequestInFlight = false;
        let mobileScannerConnected = false;

        function setMobileScannerStatus(connected, deviceName = '') {
            const $status = $('#mobileScannerStatus');
            if (!$status.length) return;

            mobileScannerConnected = !!connected;
            $status.removeClass('is-checking is-connected is-disconnected');

            if (mobileScannerConnected) {
                const label = deviceName ? `Mobile scanner: ${deviceName}` : 'Mobile scanner: connected';
                $status
                    .text(label)
                    .addClass('is-connected');
            } else {
                $status
                    .text('Mobile scanner: disconnected')
                    .addClass('is-disconnected');
            }
        }

        async function processConnectedDeviceScans(scans) {
            if (!Array.isArray(scans) || scans.length === 0) {
                return;
            }

            for (const scan of scans) {
                const barcode = String(scan?.barcode || '').trim();
                if (!barcode) {
                    continue;
                }
                window.posBarcodeScanQueue.enqueue(barcode, {
                    focusBillSearch: true,
                    playBeep: true,
                    showErrors: true
                });
            }
        }

        function pullConnectedDeviceScans() {
            if (deviceScanRequestInFlight) {
                return;
            }

            deviceScanRequestInFlight = true;

            $.ajax({
                url: '/pull-device-scans',
                type: 'GET',
                data: {
                    limit: 15
                },
                success: async function(response) {
                    if (!response || response.connected !== true) {
                        setMobileScannerStatus(false);
                        return;
                    }

                    setMobileScannerStatus(true, response.device_name || '');
                    await processConnectedDeviceScans(response.scans || []);
                },
                error: function() {
                    // Keep previous status; next polling cycle will refresh.
                },
                complete: function() {
                    deviceScanRequestInFlight = false;
                }
            });
        }

        function startConnectedDeviceScannerSync() {
            if (deviceScanPollTimer) {
                return;
            }

            $.get('/get-session-device', function(res) {
                setMobileScannerStatus(!!res.connected, res.device_name || '');
            }).fail(function() {
                // Keep initial "checking" state if this check fails once.
            });

            pullConnectedDeviceScans();
            deviceScanPollTimer = setInterval(pullConnectedDeviceScans, 1200);
        }

        // ==================== MODAL EVENT LISTENERS ====================
        $('#scanBarcodeBtn').on('click', function() {
            if (mobileScannerConnected) {
                Swal.fire({
                    icon: 'info',
                    title: 'Mobile scanner is active',
                    text: 'Scan products from your connected phone in Setting > Connected Devices. Products will auto-add here.',
                    timer: 1800,
                    showConfirmButton: false
                });
                return;
            }
            $('#barcodeScannerModal').modal('show');
        });

        $('#barcodeScannerModal').on('shown.bs.modal', function() {
            if (typeof window.renderScannerCartList === 'function') {
                window.renderScannerCartList();
            }
            setTimeout(startScanner, 400);
        });

        $('#barcodeScannerModal').on('hidden.bs.modal', function() {
            stopScanner();
            window.scannerTorchOn = false;
            $('#scannerTorchBtn').removeClass('is-on').prop('disabled', false);
            $('#barcodeScannerModal').removeClass('is-sheet-expanded scanner-manual-only');
            if (window.posBarcodeScanQueue) {
                window.posBarcodeScanQueue.reset();
            }
            $('#qr-reader').html('').css('min-height', '300px');
            $('#scan-message').text('Initializing camera...').show().css('color', '');
            $('#manualBarcodeInput').val('');
            $('#manualBarcodeError').text('');
        });

        window.scannerTorchOn = false;

        function syncScannerTorchButton() {
            const $btn = $('#scannerTorchBtn');
            if (!$btn.length) {
                return;
            }
            $btn.removeClass('is-on');
            window.scannerTorchOn = false;
            if (!html5QrCode || !html5QrCode.isScanning) {
                return;
            }
            try {
                const caps = html5QrCode.getRunningTrackCapabilities && html5QrCode.getRunningTrackCapabilities();
                if (caps && Object.prototype.hasOwnProperty.call(caps, 'torch') && !caps.torch) {
                    $btn.prop('disabled', true);
                    return;
                }
            } catch (e) {}
            $btn.prop('disabled', false);
        }

        $(document).on('click', '#scannerTorchBtn', async function() {
            if (!html5QrCode || !html5QrCode.isScanning) {
                return;
            }
            const next = !window.scannerTorchOn;
            try {
                await html5QrCode.applyVideoConstraints({
                    advanced: [{ torch: next }]
                });
                window.scannerTorchOn = next;
                $(this).toggleClass('is-on', next);
            } catch (e1) {
                try {
                    await html5QrCode.applyVideoConstraints({ torch: next });
                    window.scannerTorchOn = next;
                    $(this).toggleClass('is-on', next);
                } catch (e2) {
                    $(this).prop('disabled', true);
                }
            }
        });

        $(document).on('click', '.barcode-scanner-sheet-handle', function() {
            $('#barcodeScannerModal').toggleClass('is-sheet-expanded');
        });
    </script>
    <script>
        let labourItemsList = [];
        let authToken = localStorage.getItem("authToken");
        let selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
        let labourModalOpen = false;

        function findExistingLabourOptionByText($select, text) {
            const normalizedText = (text || '').trim().toLowerCase();
            let $match = $();

            $select.find('option').each(function() {
                const optionText = ($(this).text() || '').trim().toLowerCase();
                if (optionText === normalizedText) {
                    $match = $(this);
                    return false;
                }
            });

            return $match;
        }

        function createLabourTagOnlyWhenNew(selector) {
            return function(params) {
                const term = $.trim(params.term || '');
                if (!term) return null;

                return findExistingLabourOptionByText($(selector), term).length ? null : {
                    id: term,
                    text: term,
                    newTag: true
                };
            };
        }

        function loadLabourItems() {

            $.ajax({
                url: "/api/get-all-labour-items",
                type: "GET",
                dataType: "json",
                data: {
                    selectedSubAdminId: selectedSubAdminId
                },
                headers: {
                    "Authorization": "Bearer " + authToken,
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {

                    if (response.status) {
                        labourItemsList = response.data;

                        $("#labour-items-container").html(""); // clear
                        addLabourRow(); // first row
                    }
                },
                error: function() {
                    // console.log("Labour items load failed");
                }
            });
        }

        function labourOptionsHtml() {

            let html = `<option value="">Select Labour</option>`;

            labourItemsList.forEach(item => {
                html += `
            <option value="${item.id}" data-price="${item.price}">
                ${item.item_name}
            </option>`;
            });

            return html;
        }

        function addLabourRow() {

            let row = `
                <div class="row gx-1 align-items-center labour-row mb-2">
                    <div class="col-5">
                        <select class="form-control labour-select select2-labour">
                            ${labourOptionsHtml()}
                        </select>
                    </div>
                    <div class="col-3">
                        <input type="number" class="form-control form-control labour-qty"
                            value="1" min="1" disabled>
                    </div>
                    <div class="col-3">
                        <input type="number" class="form-control form-control labour-price"
                            placeholder="Price" disabled>
                    </div>
                    <div class="col-1">
                        <button type="button" class="btn btn-danger  remove-labour" style="display:none; width: 40px; height: 38px; padding: 0;">×</button>
                        <button type="button" class="btn btn-success  add-labour" style="width: 40px; height: 38px; padding: 0;">+</button>
                    </div>
                </div>
            `;

            let $row = $(row);
            $("#labour-items-container").append($row);
            $row.find(".select2-labour").select2({
                width: '100%',
                tags: true,
                createTag: createLabourTagOnlyWhenNew('.select2-labour'),
                allowClear: true
            });
            updateLabourButtons();
            updateLabourDropdowns();
        }
        window.addLabourRow = addLabourRow;

        function updateLabourButtons() {
            let rows = $(".labour-row");
            rows.each(function(index) {
                if (index === rows.length - 1) {
                    $(this).find(".add-labour").show();
                    $(this).find(".remove-labour").hide();
                } else {
                    $(this).find(".add-labour").hide();
                    $(this).find(".remove-labour").show();
                }
            });
        }

        $(document).on("click", ".remove-labour", function() {
            $(this).closest(".labour-row").remove();
            updateLabourButtons();
            updateLabourDropdowns();
            calculateTotals();
        });

        function updateLabourDropdowns() {
            let selectedValues = [];
            $(".select2-labour").each(function() {
                if ($(this).val()) {
                    selectedValues.push($(this).val());
                }
            });

            $(".select2-labour").each(function() {
                let currentValue = $(this).val();
                let optionsChanged = false;

                $(this).find("option").each(function() {
                    let optionValue = $(this).val();
                    if (optionValue) {
                        let shouldBeDisabled = (optionValue !== currentValue && selectedValues.includes(
                            optionValue));
                        if ($(this).prop('disabled') !== shouldBeDisabled) {
                            $(this).prop('disabled', shouldBeDisabled);
                            optionsChanged = true;
                        }
                    }
                });

                if (optionsChanged && $(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                    $(this).select2({
                        width: '100%',
                        tags: true,
                        createTag: createLabourTagOnlyWhenNew('.select2-labour'),
                        allowClear: true
                    });
                }
            });
        }

        $(document).on("input change", ".labour-qty, .labour-price", function() {
            calculateTotals();
        });

        $(document).on("change", ".labour-select", function() {

            let labourId = $(this).val();
            let price = $(this).find(":selected").data("price") || 0;
            let $row = $(this).closest(".labour-row");
            let $qtyInput = $row.find(".labour-qty");
            let $priceInput = $row.find(".labour-price");

            if (!labourId) {
                $qtyInput.val(1).prop("disabled", true);
                $priceInput.val("").prop("disabled", true);
                calculateTotals();
                return;
            }

            $qtyInput.prop("disabled", false);
            $priceInput.val(price).prop("disabled", false);

            const isLastRow = $row.is($(".labour-row").last());
            const allRowsSelected = $(".labour-row").toArray().every(function(rowEl) {
                return $(rowEl).find(".labour-select").val();
            });

            if (isLastRow && allRowsSelected) {
                addLabourRow();
            } else {
                updateLabourButtons();
            }

            updateLabourDropdowns();
            calculateTotals(); // recalc bill
        });

        $(document).on('select2:select', '.labour-select', function(e) {
            if (labourModalOpen) return;

            const data = e.params.data || {};
            const value = data.id;
            const name = (data.text || '').trim();
            const isCustom = String(data.newTag || '').toLowerCase() === 'true' || String(value).trim() === name;

            if (!isCustom || !name) return;

            const $select = $(this);
            const existingOption = findExistingLabourOptionByText($select, name);
            if (existingOption.length && String(existingOption.val()) !== String(value)) {
                $select.val(existingOption.val()).trigger('change');
                return;
            }

            labourModalOpen = true;
            $('#labourItemForm')[0].reset();
            $('#labour_item_name_error, #labour_item_price_error').text('');
            $('#labour_item_name').val(name);
            $('#labourItemModalLabel').text('Add New Labour Item');
            $('#labourItemModal').modal('show');
        });

        $('#labourItemModal').on('hidden.bs.modal', function() {
            labourModalOpen = false;
        });

        $('#labourItemForm').on('submit', function(e) {
            e.preventDefault();

            const itemName = ($('#labour_item_name').val() || '').trim();
            const price = ($('#labour_item_price').val() || '').trim();

            $('#labour_item_name_error, #labour_item_price_error').text('');

            if (!itemName) {
                $('#labour_item_name_error').text('Please enter labour name.');
                return;
            }

            if (!price) {
                $('#labour_item_price_error').text('Please enter labour price.');
                return;
            }

            const $submitBtn = $('#labourItemForm button[type="submit"]');
            if ($submitBtn.prop('disabled')) return;

            $.ajax({
                url: '/api/add-labour-item',
                type: 'POST',
                data: {
                    item_name: itemName,
                    price: price,
                    selectedSubAdminId: selectedSubAdminId
                },
                headers: {
                    "Authorization": "Bearer " + authToken,
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $submitBtn.prop('disabled', true).text('Saving...');
                },
                success: function(response) {
                    if (response && response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message || 'Labour item saved successfully.',
                            confirmButtonColor: '#ff9f43'
                        });
                        $('#labourItemModal').modal('hide');
                        labourModalOpen = false;
                        loadLabourItems();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: (response && response.message) ? response.message :
                                'Unable to save labour item.'
                        });
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        if (errors.item_name) $('#labour_item_name_error').text(errors.item_name[0]);
                        if (errors.price) $('#labour_item_price_error').text(errors.price[0]);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong while saving the labour item.'
                        });
                    }
                },
                complete: function() {
                    $submitBtn.prop('disabled', false).text('Save');
                }
            });
        });
        $(document).on("click", ".add-labour", function() {
            let $currentRow = $(this).closest(".labour-row");
            let selectedLabourId = $currentRow.find(".labour-select").val();

            if (!selectedLabourId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Select Labour Item',
                    text: 'Please select a labour item before adding a new labour row.'
                });
                return;
            }

            addLabourRow();
        });
        loadLabourItems();
    </script>
    <script>
        let selectedItems = new Map();

        window.posReservedQtyForProductPrice = function(productId, price) {
            let used = 0;
            const id = String(productId == null ? '' : productId);
            const ratePrice = parseFloat(price);
            if (!id || !Number.isFinite(ratePrice) || typeof selectedItems === 'undefined' || !selectedItems) {
                return 0;
            }
            selectedItems.forEach(function(item) {
                if (String(item.productId) === id && Math.abs(parseFloat(item.price) - ratePrice) < 0.001) {
                    used += window.parseQuantityValue(item.quantity);
                }
            });
            return used;
        };

        window.posReservedQtyForProduct = function(productId) {
            let used = 0;
            const id = String(productId == null ? '' : productId);
            if (!id || typeof selectedItems === 'undefined' || !selectedItems) {
                return 0;
            }
            selectedItems.forEach(function(item) {
                if (String(item.productId) === id) {
                    used += window.parseQuantityValue(item.quantity);
                }
            });
            return used;
        };
        const isTdsEnabled = @json((bool) ($setting->tds_apply ?? false));
        let isAdvanceReceiptMode = @json($isAdvanceReceiptMode);

        function updatePosPageTitle(isQuotationMode) {
            let titleText = 'Add Sale';
            if (isAdvanceReceiptMode) {
                titleText = 'Add Advance Receipt';
            } else if (isQuotationMode) {
                titleText = 'Add Quotation';
            } else if ($('.rentalToggle').is(':checked')) {
                titleText = 'Add Rental';
            }
            $('#posPageTitle').text(titleText);
            document.title = titleText;
        }

        // Auto-calculate remaining balance for Advance Receipt mode


        function resetPosAddBankForm() {
            if ($('#addBankForm').length) {
                $('#addBankForm')[0].reset();
            }
            $('#add_opening_balance').val('0');
            $('#add_bank_status').val('1');
            $('#addBankForm .text-danger').text('');
        }

        function upsertPosBankOption(bank) {
            if (!bank || !bank.id) {
                return;
            }

            const bankId = String(bank.id);
            const bankName = bank.bank_name || 'Unnamed Bank';
            const accountNumber = bank.account_number ? ` (${bank.account_number})` : '';
            const optionLabel = `${bankName}${accountNumber}`;
            const existingOption = $('#bank_id option[value="' + bankId + '"]');

            if (existingOption.length) {
                existingOption.text(optionLabel);
            } else {
                $('#bank_id').append(new Option(optionLabel, bankId));
            }

            // Also sync the EMI bank dropdown
            const existingEmiOption = $('#emiBankId option[value="' + bankId + '"]');
            if (existingEmiOption.length) {
                existingEmiOption.text(optionLabel);
            } else {
                $('#emiBankId').append(new Option(optionLabel, bankId));
            }

            $('#bank_id').val(bankId).trigger('change');
        }



        document.querySelectorAll('.paymentmethod').forEach(el => {
            el.addEventListener('click', function() {
                document.querySelectorAll('input[name="payment_method"]').forEach(radio => radio.checked =
                    false);
                this.querySelector('input[type="radio"]').checked = true;
                document.querySelectorAll('.paymentmethod').forEach(item => item.classList.remove(
                    'active'));
                this.classList.add('active');
            });
        });
        $(document).ready(function() {


            function toAmount(value) {
                const parsed = parseFloat(value);
                return Number.isFinite(parsed) ? parsed : 0;
            }

            function clampAmount(value, min, max) {
                return Math.min(Math.max(value, min), max);
            }

            function getCurrentSaleTotal() {
                let total = toAmount($("input[name='total']").val());
                let advancePaid = @json($isPosEdit ?? false) ? 0 : toAmount("{{ $convertOrderAdvancePaid ?? 0 }}");
                let payable = Math.max(0, total - advancePaid);
                
                if ($(".rentalToggle").is(":checked")) {
                    let depositAmount = toAmount($("#deposit_amount").val());
                    if (depositAmount > 0) {
                        return depositAmount;
                    }
                }
                return payable;
            }

            function getSelectedPaymentMethod() {
                return $("input[name='payment_method']:checked").val() || "";
            }

            function refreshPosPaymentUi(resetAmounts = false) {
                const selectedPayment = getSelectedPaymentMethod();
                const total = getCurrentSaleTotal();
                const $paidType = $("#posPaidType");

                $(".error_bank, .error_paidtype, .error_paidamount").text("");
                if (selectedPayment && selectedPayment !== "pending") {
                    $("#paymentDetailActions").css("display", "flex");
                } else {
                    $("#paymentDetailActions").hide();
                }

                if (!selectedPayment || selectedPayment === "pending") {
                    $("#posPaidTypeBox, #posPaidAmountFields, #cashOnlineBox, #bankSelectionBox, #emiBox").hide();
                    $("#posPaidAmount, #posPendingAmount, #posCashOnlineCashAmount, #posCashOnlineOnlineAmount")
                        .val("");
                    $("#posPaidType").val("");
                    return;
                }

                if (selectedPayment === "emi") {
                    $("#posPaidTypeBox, #posPaidAmountFields, #cashOnlineBox, #bankSelectionBox").hide();
                    $("#posPaidAmount, #posPendingAmount, #posCashOnlineCashAmount, #posCashOnlineOnlineAmount")
                        .val("");
                    $("#emiBox").show();
                    const downPayment = toAmount($("#emiDownPayment").val());
                    const loanAmount = Math.max(total - downPayment, 0);
                    $("#emiLoanAmount").val(loanAmount.toFixed(2));
                    const tenure = $("#emiTenure").val();
                    const isCustomTenure = tenure === "custom";
                    if (isCustomTenure) {
                        $("#emiCustomTenureCol").removeClass("d-none").show();
                    } else {
                        $("#emiCustomTenureCol").addClass("d-none").hide();
                    }
                    const months = isCustomTenure ?
                        parseInt($("#emiCustomTenure").val() || "0", 10) :
                        parseInt(tenure || "0", 10);
                    const annualRate = toAmount($("#emiInterestRate").val());

                    // Inline EMI validation
                    $("#emiCustomTenureError").addClass("d-none").text("");
                    if (isCustomTenure) {
                        const rawCustom = ($("#emiCustomTenure").val() || "").trim();
                        if (rawCustom !== "" && (months <= 0 || !Number.isInteger(months))) {
                            $("#emiCustomTenureError").removeClass("d-none").text(
                                "Please enter a valid positive whole number.");
                        }
                    }
                    if (downPayment > 0 && downPayment >= total && total > 0) {
                        $("#emiDownPaymentError").removeClass("d-none").text(
                            "Down payment must be less than loan amount.");
                    } else {
                        $("#emiDownPaymentError").addClass("d-none").text("");
                    }

                    let emi = 0;
                    if (loanAmount > 0 && months > 0) {
                        if (annualRate === 0) {
                            // Zero interest: simple division
                            emi = loanAmount / months;
                        } else {
                            // Standard amortization formula: EMI = P×R×(1+R)^N / [(1+R)^N - 1]
                            const R = annualRate / 12 / 100;
                            const pow = Math.pow(1 + R, months);
                            emi = (loanAmount * R * pow) / (pow - 1);
                        }
                        $("#emiMonthlyAmount").val(emi.toFixed(2));

                        // Update Total Amount bar: Down Payment + (Monthly EMI × N)
                        const emiTotal = downPayment + (emi * months);
                        $(".btn-totallabel h6").html('Total Amount : ₹' + emiTotal.toFixed(2));
                    } else {
                        $("#emiMonthlyAmount").val("");
                        // Reset to cart total when EMI not fully configured
                        $(".btn-totallabel h6").html('Total Amount : ₹' + total.toFixed(2));
                    }
                    return;
                }

                $("#emiCustomTenureCol").addClass("d-none").hide();

                $("#posPaidTypeBox").show();
                if (isAdvanceReceiptMode) {
                    $paidType.val('partially');
                    $("#posPaidTypeDropdownGroup").hide();
                } else {
                    $("#posPaidTypeDropdownGroup").show();
                }
                const paidType = $paidType.val();

                if (!paidType) {
                    $("#posPaidAmountFields, #cashOnlineBox, #bankSelectionBox, #emiBox").hide();
                    $("#posPaidAmount, #posPendingAmount, #posCashOnlineCashAmount, #posCashOnlineOnlineAmount")
                        .val("");
                    return;
                }

                if (selectedPayment === "debit card" || selectedPayment === "scan" || selectedPayment ===
                    "cash+online") {
                    $("#bankSelectionBox").show();
                } else {
                    $("#bankSelectionBox").hide();
                    $("#bank_id").val("");
                }

                $("#posPaidAmountFields").show();
                let paidAmount = 0;
                let cashAmount = 0;
                let onlineAmount = 0;

                if (selectedPayment === "cash+online") {
                    $("#cashOnlineBox").show();

                    const rawCashAmount = ($("#posCashOnlineCashAmount").val() || "").trim();
                    const rawOnlineAmount = ($("#posCashOnlineOnlineAmount").val() || "").trim();
                    const enteredCashAmount = toAmount(rawCashAmount);
                    const enteredOnlineAmount = toAmount(rawOnlineAmount);

                    cashAmount = clampAmount(enteredCashAmount, 0, total);
                    onlineAmount = enteredOnlineAmount;

                    if (paidType === "fully") {
                        onlineAmount = Math.max(total - cashAmount, 0);
                        $("#posCashOnlineOnlineAmount").prop("readonly", true).addClass("bg-light");
                        $("#posCashOnlineOnlineAmount").val(onlineAmount.toFixed(2));
                    } else {
                        onlineAmount = clampAmount(onlineAmount, 0, total);
                        if (cashAmount + onlineAmount > total) {
                            onlineAmount = Math.max(total - cashAmount, 0);
                        }
                        $("#posCashOnlineOnlineAmount").prop("readonly", false).removeClass("bg-light");

                        if (rawOnlineAmount === "") {
                            $("#posCashOnlineOnlineAmount").val("");
                        } else if (enteredOnlineAmount !== onlineAmount) {
                            $("#posCashOnlineOnlineAmount").val(onlineAmount.toFixed(2));
                        }
                    }

                    paidAmount = clampAmount(cashAmount + onlineAmount, 0, total);

                    if (rawCashAmount === "") {
                        $("#posCashOnlineCashAmount").val("");
                    } else if (enteredCashAmount !== cashAmount) {
                        $("#posCashOnlineCashAmount").val(cashAmount.toFixed(2));
                    }

                    $("#posPaidAmount").val(paidAmount.toFixed(2)).prop("readonly", true).addClass("bg-light");
                } else {
                    $("#emiDownPayment, #emiLoanAmount, #emiMonthlyAmount, #emiAadharNumber, #emiPanNumber, #emiGuarantorName")
                        .val("");
                    $("#emiTenure").val("");
                    $("#emiInterestRate").val("0");
                    $("#emiBankId").val("");
                    $("#cashOnlineBox").hide();
                    $("#posCashOnlineCashAmount, #posCashOnlineOnlineAmount").val("");

                    if (paidType === "fully") {
                        paidAmount = total;
                        $("#posPaidAmount").val(total.toFixed(2)).prop("readonly", true).addClass("bg-light");
                    } else {
                        const rawPaidAmount = ($("#posPaidAmount").val() || "").trim();
                        const entered = toAmount(rawPaidAmount);
                        paidAmount = clampAmount(entered, 0, total);

                        if (resetAmounts) {
                            $("#posPaidAmount").val("");
                        } else if (rawPaidAmount === "") {
                            $("#posPaidAmount").val("");
                        } else if (entered !== paidAmount) {
                            $("#posPaidAmount").val(paidAmount.toFixed(2));
                        }

                        $("#posPaidAmount").prop("readonly", false).removeClass("bg-light");
                    }
                }

                const pendingAmount = Math.max(total - paidAmount, 0);
                $("#posPendingAmount").val(pendingAmount.toFixed(2));
            }

            $(document).on('input change', '#emiDownPayment, #emiInterestRate, #emiTenure, #emiCustomTenure',
                function() {
                    clearPosEmiValidation();
                    if (getSelectedPaymentMethod() === 'emi') {
                        refreshPosPaymentUi();
                    }
                });

            function getPosPaymentMeta() {
                const selectedPayment = getSelectedPaymentMethod();
                const paidType = $("#posPaidType").val();
                const paidAmount = toAmount($("#posPaidAmount").val());
                const pendingAmount = toAmount($("#posPendingAmount").val());
                const cashAmount = toAmount($("#posCashOnlineCashAmount").val());
                const onlineAmount = toAmount($("#posCashOnlineOnlineAmount").val());
                const emiDownPayment = toAmount($("#emiDownPayment").val());
                const emiLoanAmount = toAmount($("#emiLoanAmount").val());
                const emiInterestRate = toAmount($("#emiInterestRate").val());
                const emiTenure = $("#emiTenure").val();
                const emiCustomTenure = $("#emiCustomTenure").val();
                const emiMonthlyAmount = toAmount($("#emiMonthlyAmount").val());
                const emiAadharNumber = ($("#emiAadharNumber").val() || "").trim();
                const emiDoId = ($("#emiDoId").val() || "").trim();
                const emiPanNumber = ($("#emiPanNumber").val() || "").trim();
                const emiGuarantorName = ($("#emiGuarantorName").val() || "").trim();
                const emiBankId = $("#emiBankId").val() || "";

                return {
                    selectedPayment,
                    paidType,
                    paidAmount,
                    pendingAmount,
                    cashAmount,
                    onlineAmount,
                    emiDownPayment,
                    emiLoanAmount,
                    emiInterestRate,
                    emiTenure,
                    emiCustomTenure,
                    emiMonthlyAmount,
                    emiAadharNumber,
                    emiDoId,
                    emiPanNumber,
                    emiGuarantorName,
                    emiBankId
                };
            }

            function resetPosPaymentUi() {
                $("#posPaidType").val("");
                $("#posPaidAmount, #posPendingAmount, #posCashOnlineCashAmount, #posCashOnlineOnlineAmount, #posPaymentRemark")
                    .val("");
                $("#bank_id").val("");
                $("#emiBankId").val("");
                $("#emiDownPayment, #emiLoanAmount, #emiTenure, #emiInterestRate, #emiMonthlyAmount, #emiAadharNumber, #emiDoId, #emiPanNumber, #emiGuarantorName")
                    .val("");
                $("#emiTenure").val("");
                $("#emiInterestRate").val("0");
                $("#emiMonthlyAmount").val("");
                $("#paymentDetailActions, #posPaidTypeBox, #posPaidAmountFields, #cashOnlineBox, #bankSelectionBox, #emiBox")
                    .hide();
                $(".is-invalid").removeClass("is-invalid");
                $("#emiTenureError, #emiAadharError, #emiBankError, #emiCustomTenureError, #emiDownPaymentError")
                    .addClass("d-none").text("");
            }

            function selectPayLaterPayment() {
                const $payLater = $(".paymentmethod").has("input[name='payment_method'][value='pending']");
                if (!$payLater.length || $payLater.closest('li').is(':hidden')) {
                    return false;
                }
                $("input[name='payment_method']").prop("checked", false);
                $payLater.find("input[name='payment_method']").prop("checked", true);
                $(".paymentmethod").removeClass("active");
                $payLater.addClass("active");
                resetPosPaymentUi();
                $(".error_peymentmethod").text("");
                refreshPosPaymentUi(true);
                return true;
            }

            function selectDefaultPaymentMethod() {
                if (selectPayLaterPayment()) {
                    return;
                }
                const $firstVisible = $(".setvaluecash .paymentmethod").filter(function() {
                    return $(this).closest("li").is(":visible");
                }).first();
                if ($firstVisible.length) {
                    $firstVisible.trigger("click");
                }
            }

            window.selectPayLaterPayment = selectPayLaterPayment;
            window.selectDefaultPaymentMethod = selectDefaultPaymentMethod;

            function clearPosEmiValidation() {
                $("#emiTenure, #emiAadharNumber, #emiBankId").removeClass("is-invalid");
                $("#emiTenureError, #emiAadharError, #emiBankError").addClass("d-none").text("");
            }

            function validatePosEmiFields() {
                clearPosEmiValidation();
                if (getSelectedPaymentMethod() !== "emi") {
                    return true;
                }

                let valid = true;
                const tenure = $("#emiTenure").val();
                const customMonths = parseInt($("#emiCustomTenure").val() || "0", 10);
                const aadhar = ($("#emiAadharNumber").val() || "").trim();
                const bankId = $("#emiBankId").val();

                if (!tenure) {
                    $("#emiTenure").addClass("is-invalid");
                    $("#emiTenureError").removeClass("d-none").text("EMI tenure is required.");
                    valid = false;
                } else if (tenure === "custom" && (!Number.isFinite(customMonths) || customMonths <= 0)) {
                    $("#emiCustomTenure").addClass("is-invalid");
                    $("#emiCustomTenureError").removeClass("d-none").text("Enter a valid number of months.");
                    valid = false;
                }

                if (!bankId) {
                    $("#emiBankId").addClass("is-invalid");
                    $("#emiBankError").removeClass("d-none").text("Bank selection is required.");
                    valid = false;
                }

                return valid;
            }

            window.refreshPosPaymentUi = refreshPosPaymentUi;
            window.getPosPaymentMeta = getPosPaymentMeta;
            window.resetPosPaymentUi = resetPosPaymentUi;
            window.clearPosEmiValidation = clearPosEmiValidation;
            window.validatePosEmiFields = validatePosEmiFields;

            function getPosInvoicePrintModal() {
                const modalEl = document.getElementById('posInvoicePrintModal');
                if (!modalEl) {
                    return null;
                }
                if (!window._posInvoicePrintModalInstance) {
                    window._posInvoicePrintModalInstance = bootstrap.Modal.getOrCreateInstance(modalEl, {
                        backdrop: 'static',
                        keyboard: true
                    });
                }
                return window._posInvoicePrintModalInstance;
            }

            function setPosInvoicePrintLoading(isLoading) {
                const $loader = $('#posInvoicePrintLoading');
                const $printBtn = $('#posInvoicePrintBtn');
                if (isLoading) {
                    $loader.addClass('is-visible');
                    $printBtn.prop('disabled', true);
                } else {
                    $loader.removeClass('is-visible');
                    $printBtn.prop('disabled', false);
                }
            }

            function syncPosInvoicePrintModalLayout() {
                const modalEl = document.getElementById('posInvoicePrintModal');
                const bodyEl = modalEl ? modalEl.querySelector('.pos-invoice-print-body') : null;
                if (!modalEl || !bodyEl || !modalEl.classList.contains('show')) {
                    return;
                }
                if (window.matchMedia('(max-width: 991.98px)').matches) {
                    const contentEl = modalEl.querySelector('.pos-invoice-print-content');
                    const headerEl = modalEl.querySelector('.modal-header');
                    const footerEl = modalEl.querySelector('.pos-invoice-print-footer');
                    const viewportHeight = window.visualViewport ? window.visualViewport.height : window.innerHeight;
                    const chromeHeight = (headerEl ? headerEl.offsetHeight : 0) + (footerEl ? footerEl.offsetHeight : 0);
                    const nextHeight = Math.max(220, viewportHeight - chromeHeight);
                    bodyEl.style.height = nextHeight + 'px';
                    if (contentEl) {
                        contentEl.style.minHeight = viewportHeight + 'px';
                    }
                } else {
                    bodyEl.style.height = '';
                }
            }

            function markPosInvoicePrintReady() {
                window._posInvoicePrintReady = true;
                setPosInvoicePrintLoading(false);
                syncPosInvoicePrintModalLayout();
                tryPosInvoiceAutoPrint();
            }

            function tryPosInvoiceAutoPrint() {
                if (!window._posInvoiceAutoPrintPending || !window._posInvoicePrintReady) {
                    return;
                }
                if (window.matchMedia && window.matchMedia('(max-width: 1024.98px)').matches) {
                    window._posInvoiceAutoPrintPending = false;
                    return;
                }
                if (!window.InvoiceQzPrint) {
                    return;
                }
                const printerName = ($('#posPrinterSelect').val() || '').trim();
                if (!printerName || printerName === '__browser__') {
                    return;
                }
                window._posInvoiceAutoPrintPending = false;
                setTimeout(function() {
                    printPosInvoiceFromModal();
                }, 300);
            }

            function setPosQzStatus(text, isError) {
                const $el = $('#posQzStatus');
                $el.text(text || '');
                $el.css('color', isError ? '#ea5455' : '#28c76f');
            }

            function refreshPosPrinterList(pick) {
                function run() {
                    if (!window.InvoiceQzPrint) {
                        $('#posPrinterSelect').html('<option value="">Print helper loading…</option>');
                        $('#posQzInstallLink').show();
                        setPosQzStatus('Loading…', true);
                        return Promise.reject(new Error('InvoiceQzPrint missing'));
                    }
                    return InvoiceQzPrint.refreshPrinters({
                        select: '#posPrinterSelect',
                        status: '#posQzStatus',
                        installLink: '#posQzInstallLink',
                        storageKey: 'posSelectedPrinter',
                        list: '#posBtPrinterList',
                        wifiList: '#posWifiPrinterList',
                        pick: !!pick
                    }).then(function(printers) {
                        tryPosInvoiceAutoPrint();
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

            function printPosInvoiceFromModal() {
                if (window.InvoiceQzPrint && InvoiceQzPrint.shouldDeferPrintForPicker(document.getElementById('posInvoicePrintBtn'))) {
                    return;
                }
                const frame = document.getElementById('posInvoicePrintFrame');
                if (!frame || !frame.src || frame.src === 'about:blank' || !window._posInvoicePrintReady) {
                    return;
                }
                if (window._posInvoicePrintBusy) {
                    return;
                }
                const printerName = ($('#posPrinterSelect').val() || '').trim();
                const doAutoCut = $('#posThermalAutoCut').is(':checked');
                const pdfUrl = String(frame.src || '').split('#')[0];
                if (!window.InvoiceQzPrint) {
                    window.open(pdfUrl, '_blank');
                    return;
                }
                window._posInvoicePrintBusy = true;
                $('#posInvoicePrintBtn').prop('disabled', true);
                InvoiceQzPrint.printInvoice({
                    printerName: printerName,
                    pdfUrl: pdfUrl,
                    autoCut: doAutoCut,
                    frame: frame,
                    status: '#posQzStatus',
                    button: '#posInvoicePrintBtn',
                    select: '#posPrinterSelect',
                    list: '#posBtPrinterList',
                    storageKey: 'posSelectedPrinter'
                }).catch(function() {}).then(function() {
                    if (window._posInvoicePrintReady) {
                        $('#posInvoicePrintBtn').prop('disabled', false);
                    }
                    setTimeout(function() {
                        window._posInvoicePrintBusy = false;
                    }, 800);
                });
            }

            window.printPosInvoiceFromModal = printPosInvoiceFromModal;
            window.refreshPosPrinterList = refreshPosPrinterList;

            window.currentPosPrintOrderId = null;

            function posInvoicePdfPreviewUrl(path) {
                var base = String(path || '').split('#')[0];
                return base + '#toolbar=0&navpanes=0&scrollbar=0&view=FitH';
            }

            window.posLoadPdfTemplate = function(templateId, btnElement) {
                if (!window.currentPosPrintOrderId) return;
                
                // Handle active button state
                if (btnElement) {
                    $('.pos-pdf-format-btn').removeClass('btn-primary').addClass('btn-outline-primary');
                    $(btnElement).removeClass('btn-outline-primary').addClass('btn-primary');
                }
                
                const frame = document.getElementById('posInvoicePrintFrame');
                if (frame) {
                    setPosInvoicePrintLoading(true);
                    window._posInvoicePrintReady = false;
                    var tpl = parseInt(templateId, 10) || 1;
                    var hindi = $('#posPrintHindi').is(':checked') ? '1' : '0';
                    var qs = '?template=' + tpl + '&hindi=' + hindi;
                    frame.src = posInvoicePdfPreviewUrl('/sales/invoice/pdf/' + encodeURIComponent(window.currentPosPrintOrderId) + qs);
                    if (window.InvoiceQzPrint && InvoiceQzPrint.preparePhonePrint) {
                        InvoiceQzPrint.preparePhonePrint(frame.src);
                    }
                }
            };

            window.openPosInvoicePrintModal = function(orderId, options) {
                if (!orderId) {
                    return;
                }
                options = options || {};
                
                // Reset all format buttons to inactive state
                $('.pos-pdf-format-btn').removeClass('btn-primary').addClass('btn-outline-primary');
                $('#posPrintHindi').prop('checked', !!(window.__INVOICE_PRINT_PREFS || {}).hindi);
                $('#posThermalAutoCut').prop('checked', /^(bt|serial):/.test(String($('#posPrinterSelect').val() || '')) || !!(window.__INVOICE_PRINT_PREFS || {}).autoCut);
                
                window.currentPosPrintOrderId = orderId;
                window._posInvoiceAutoPrintPending = !!options.autoPrint;
                const frame = document.getElementById('posInvoicePrintFrame');
                const modal = getPosInvoicePrintModal();
                if (!frame || !modal) {
                    return;
                }
                if (window._posInvoicePrintLoadTimer) {
                    clearTimeout(window._posInvoicePrintLoadTimer);
                }
                window._posInvoicePrintReady = false;
                setPosInvoicePrintLoading(true);
                frame.onload = function() {
                    if (window._posInvoicePrintLoadTimer) {
                        clearTimeout(window._posInvoicePrintLoadTimer);
                        window._posInvoicePrintLoadTimer = null;
                    }
                    markPosInvoicePrintReady();
                };
                frame.onerror = function() {
                    if (window._posInvoicePrintLoadTimer) {
                        clearTimeout(window._posInvoicePrintLoadTimer);
                        window._posInvoicePrintLoadTimer = null;
                    }
                    setPosInvoicePrintLoading(false);
                };
                var preferredTpl = parseInt($('#posPdfTemplateButtons').data('preferred-template'), 10) || 1;
                if (window.matchMedia && window.matchMedia('(max-width: 767.98px)').matches) {
                    preferredTpl = 1;
                }
                preferredTpl = Math.min(5, Math.max(1, preferredTpl));
                var prefBtn = $('.pos-pdf-format-btn[data-template="' + preferredTpl + '"]');
                if (prefBtn.length) {
                    prefBtn.removeClass('btn-outline-primary').addClass('btn-primary');
                }
                var qs = '?template=' + preferredTpl + '&hindi=0';
                frame.src = posInvoicePdfPreviewUrl('/sales/invoice/pdf/' + encodeURIComponent(orderId) + qs);
                if (window.InvoiceQzPrint && InvoiceQzPrint.preparePhonePrint) {
                    InvoiceQzPrint.preparePhonePrint(frame.src);
                }
                window._posInvoicePrintLoadTimer = setTimeout(function() {
                    if (!window._posInvoicePrintReady) {
                        markPosInvoicePrintReady();
                    }
                    window._posInvoicePrintLoadTimer = null;
                }, 4000);
                refreshPosPrinterList(false).catch(function() {});
                if (window.InvoiceQzPrint && InvoiceQzPrint.autoPickOnOpen) {
                    InvoiceQzPrint.autoPickOnOpen({
                        select: '#posPrinterSelect',
                        status: '#posQzStatus',
                        storageKey: 'posSelectedPrinter',
                        list: '#posBtPrinterList'
                    });
                }
                modal.show();
                setTimeout(syncPosInvoicePrintModalLayout, 60);
            };

            $('#posInvoicePrintBtn').on('click', function() {
                printPosInvoiceFromModal();
            });

            $('#posRefreshPrintersBtn').on('click', function() {
                var bluetoothOpen = $('#posBtPrinterList').is(':visible');
                if (bluetoothOpen && window.InvoiceQzPrint && InvoiceQzPrint.pickNearbyPrinter) {
                    InvoiceQzPrint.pickNearbyPrinter({
                        select: '#posPrinterSelect',
                        status: '#posQzStatus',
                        storageKey: 'posSelectedPrinter',
                        list: '#posBtPrinterList'
                    });
                }
                refreshPosPrinterList(false).catch(function() {});
            });

            $('#posPrinterSelect').on('change', function() {
                const name = $(this).val() || '';
                try {
                    if (name) localStorage.setItem('posSelectedPrinter', name);
                } catch (e) {}
            });

            $('#posThermalAutoCut').on('change', function() {
                try {
                    localStorage.setItem('posThermalAutoCut', $(this).is(':checked') ? '1' : '0');
                } catch (e) {}
            });

            $('#posPrintHindi').on('change', function() {
                if (!window.currentPosPrintOrderId) return;
                var active = $('.pos-pdf-format-btn.btn-primary').data('template') || $('#posPdfTemplateButtons').data('preferred-template') || 1;
                window.posLoadPdfTemplate(active, null);
            });

            $('#posInvoicePrintModal').on('shown.bs.modal', function() {
                syncPosInvoicePrintModalLayout();
                if (window.innerWidth >= 768) {
                    refreshPosPrinterList().catch(function() {});
                }
            });

            $('#posInvoicePrintModal').on('hidden.bs.modal', function() {
                const frame = document.getElementById('posInvoicePrintFrame');
                window._posInvoicePrintReady = false;
                window._posInvoicePrintBusy = false;
                window._posInvoiceAutoPrintPending = false;
                if (window._posInvoicePrintLoadTimer) {
                    clearTimeout(window._posInvoicePrintLoadTimer);
                    window._posInvoicePrintLoadTimer = null;
                }
                setPosInvoicePrintLoading(false);
                if (frame) {
                    frame.onload = null;
                    frame.onerror = null;
                    frame.src = 'about:blank';
                }
                const bodyEl = this.querySelector('.pos-invoice-print-body');
                if (bodyEl) {
                    bodyEl.style.height = '';
                }
            });

            $(window).on('resize.posInvoicePrint orientationchange.posInvoicePrint', function() {
                syncPosInvoicePrintModalLayout();
            });

            if (window.visualViewport) {
                window.visualViewport.addEventListener('resize', syncPosInvoicePrintModalLayout);
            }

            $(document).on('keydown.posInvoicePrint', function(e) {
                const modalEl = document.getElementById('posInvoicePrintModal');
                if (!modalEl || !modalEl.classList.contains('show')) {
                    return;
                }
                if (e.key === 'F10' || e.keyCode === 121) {
                    e.preventDefault();
                    e.stopPropagation();
                    printPosInvoiceFromModal();
                }
            });

            // Mobile → Desktop
            $(document).on('change', 'input[name="gst_option_mobile"]', function() {
                var val = $(this).val();
                $('input[name="gst_option"][value="' + val + '"]').prop('checked', true);
                toggleCustomerGstField();
                if (typeof window.renderSelectedItems === 'function') window.renderSelectedItems();
                if (typeof window.calculateTotals === 'function') window.calculateTotals();
            });

            // Desktop → Mobile
            $(document).on('change', 'input[name="gst_option"]', function() {
                var val = $(this).val();
                $('input[name="gst_option_mobile"][value="' + val + '"]').prop('checked', true);
                toggleCustomerGstField();
                if (typeof window.renderSelectedItems === 'function') window.renderSelectedItems();
                if (typeof window.calculateTotals === 'function') window.calculateTotals();
            });

            // On page load: make sure mobile reflects desktop default (without = checked)
            var currentGst = $('input[name="gst_option"]:checked').val() || 'without';
            $('input[name="gst_option_mobile"][value="' + currentGst + '"]').prop('checked', true);
            toggleCustomerGstField();

            function toggleCustomerGstField() {
                var isWithGst = $('input[name="gst_option"]:checked').val() === 'with';
                var $gstField = $('#customer_gst_field');
                var $gstInput = $('#pos_gst_number');
                var $gstMsg = $('#pos-gst-msg');
                var $gstLoader = $('#pos-gst-loader');

                $gstField.toggle(isWithGst);

                if (isWithGst) {
                    var $selectedOption = $('#customer_name option:selected');
                    var gst = $selectedOption.attr('data-gst') || '';
                    if ($('#customer_name').val()) {
                        $gstInput.val(gst);
                    }
                } else {
                    $gstInput.val('');
                    $gstMsg.empty();
                    $gstLoader.hide();
                    $gstInput.removeClass('loading').prop('readonly', false);
                }
            }


            $(".paymentmethod").on("click", function() {
                const value = $(this).find("input[name='payment_method']").val();

                $("input[name='payment_method']").prop("checked", false);
                $(this).find("input[name='payment_method']").prop("checked", true);
                $(".paymentmethod").removeClass("active");
                $(this).addClass("active");

                // Set Paid Type = Fully by default on selecting any payment method (except pending)
                if (value !== "pending") {
                    $("#posPaidType").val("fully");
                } else {
                    $("#posPaidType").val("");
                }

                // Automatically select the first/default Bank (or show Select Bank placeholder if none exists)
                const firstBankVal = $("#bank_id option").filter(function() {
                    return this.value !== "";
                }).first().val() || "";
                $("#bank_id").val(firstBankVal);

                const firstEmiBankVal = $("#emiBankId option").filter(function() {
                    return this.value !== "";
                }).first().val() || "";
                $("#emiBankId").val(firstEmiBankVal);

                // Clear/reset any previously entered bank, amount, pending amount, remarks, and EMI fields
                $("#posPaidAmount, #posPendingAmount, #posCashOnlineCashAmount, #posCashOnlineOnlineAmount, #posPaymentRemark")
                    .val("");
                $("#emiDownPayment, #emiLoanAmount, #emiMonthlyAmount, #emiAadharNumber, #emiDoId, #emiPanNumber, #emiGuarantorName")
                    .val("");
                $("#emiTenure").val("");
                $("#emiInterestRate").val("0");

                refreshPosPaymentUi(true);
            });

            $("#cancelPaymentDetails").on("click", function() {
                selectPayLaterPayment();
            });

            $("#posPaidType").on("change", function() {
                refreshPosPaymentUi(false);
            });

            $("#posPaidAmount, #posCashOnlineCashAmount, #posCashOnlineOnlineAmount").on("input", function() {
                refreshPosPaymentUi(false);
            });

            selectDefaultPaymentMethod();
        });

        $(document).ready(function() {

            // hide payment buttons when quotation checked
            $(document).ready(function() {

                // $("#quotationToggle").on("change", function() {

                //     if ($(this).is(":checked")) {

                //         // Hide payment section
                //         $("#paymentSection").slideUp();

                //         // Optional: clear selected payment
                //         $("input[name='payment_method']").prop("checked", false);
                //         $(".paymentmethod").removeClass("active");

                //         // Hide extra boxes
                //         $("#cashOnlineBox").hide();
                //         $("#bankSelectionBox").hide();

                //     } else {

                //         // Show payment section again
                //         $("#paymentSection").slideDown();
                //     }
                // });
                $(".quotationToggle").on("change", function() {
                    const isChecked = $(this).is(":checked");
                    $(".quotationToggle").prop("checked", isChecked);

                    if (isChecked) {
                        // Uncheck others
                        $(".advanceReceiptToggle").prop("checked", false);
                        $(".rentalToggle").prop("checked", false);
                        $(".wholesaleToggle").prop("checked", false);
                        $("#deposit_advance_section").hide();

                        // ✅ mark quotation
                        $("#quotation_status").val("quotation");

                        // Hide payment UI
                        $("#paymentSection").slideUp();

                        // Clear payment selection
                        $("input[name='payment_method']").prop("checked", false);
                        $(".paymentmethod").removeClass("active");

                        if (typeof window.resetPosPaymentUi === "function") {
                            window.resetPosPaymentUi();
                        } else {
                            $("#posPaidTypeBox, #posPaidAmountFields, #cashOnlineBox, #bankSelectionBox")
                                .hide();
                        }

                    } else {

                        // ✅ normal sale
                        $("#quotation_status").val("sales");

                        $("#paymentSection").slideDown();
                        if (typeof selectDefaultPaymentMethod === "function") {
                            selectDefaultPaymentMethod();
                        }
                    }
                });

                $(".advanceReceiptToggle").on("change", function() {
                    const isChecked = $(this).is(":checked");
                    $(".advanceReceiptToggle").prop("checked", isChecked);

                    if (isChecked) {
                        // Uncheck others
                        $(".quotationToggle").prop("checked", false);
                        $(".rentalToggle").prop("checked", false);
                        $(".wholesaleToggle").prop("checked", false);
                        $("#deposit_advance_section").hide();

                        $("#quotation_status").val("advance_receipt");
                        $("#paymentSection").slideDown(); // ensure payment UI is visible
                    } else {
                        $("#quotation_status").val("sales");
                    }
                });

                // Sync rental checkboxes
                $(".rentalToggle").on("change", function() {
                    const isChecked = $(this).is(":checked");
                    $(".rentalToggle").prop("checked", isChecked);

                    if (isChecked) {
                        // Uncheck others
                        $(".quotationToggle").prop("checked", false);
                        $(".advanceReceiptToggle").prop("checked", false);
                        $(".wholesaleToggle").prop("checked", false);

                        $("#deposit_advance_section").show();

                        // Ensure payment UI is visible in case we switched from Quotation
                        $('#paymentSection').show();
                        $('.setvaluecash').show();
                        if (typeof window.refreshPosPaymentUi === "function") {
                            window.refreshPosPaymentUi(true);
                        }

                        // Change URL without reloading
                        window.history.pushState(null, '', '/add-sales?sale_type=rental');
                        if (typeof updatePosPageTitle === "function") {
                            updatePosPageTitle(false);
                        }

                        // Hide Pay Later for rental
                        $("#payLaterOption").hide();

                        // Clear Pay Later for rental, then pick first available method
                        if ($("input[name='payment_method']:checked").val() === 'pending') {
                            $("input[name='payment_method']").prop("checked", false);
                            $(".paymentmethod").removeClass("active");
                            if (typeof selectDefaultPaymentMethod === "function") {
                                selectDefaultPaymentMethod();
                            }
                        }

                        // ✅ mark rental
                        $("#quotation_status").val("rental");

                        // Remove non-rental products
                        selectedItems.forEach((item, key) => {
                            if (Number(item.is_rent_available) !== 1) {
                                selectedItems.delete(key);
                            } else {
                                if (item.rent_price) {
                                    item.price = item.rent_price;
                                }
                            }
                        });
                        $(".error_peymentmethod").text("");
                    } else {
                        $("#deposit_advance_section").hide();

                        // Change URL without reloading
                        window.history.pushState(null, '', '/add-sales?sale_type=sales');
                        if (typeof updatePosPageTitle === "function") {
                            updatePosPageTitle(false);
                        }

                        // Show Pay Later again
                        if (window.enabledPaymentMethods && window.enabledPaymentMethods.includes(
                                'pay_later')) {
                            $("#payLaterOption").show();
                        }

                        // ✅ normal sale
                        $("#quotation_status").val("sales");

                        // Revert rental prices to regular prices
                        selectedItems.forEach((item, key) => {
                            if (item.regular_price) {
                                item.price = item.regular_price;
                            }
                        });
                    }

                    if (typeof window.updateOrderNumberBadge === 'function') {
                        window.updateOrderNumberBadge();
                    }

                    // Force re-render of selected items with updated prices
                    renderSelectedItems();
                    if (typeof window.initRentalDatePickers === "function") {
                        window.initRentalDatePickers();
                    }
                    if (typeof window.calculateTotals === "function") {
                        window.calculateTotals();
                    } else if (typeof calculateTotals === "function") {
                        calculateTotals();
                    }

                    // Reload products for current active tab (only when category catalog is visible)
                    if ($('.pos-category-catalog').is(':visible')) {
                    let activeTab = $(".tabs li.active").attr("id");
                    if (activeTab) {
                        loadProductsByCategory(activeTab);
                    }
                    }
                });

                $(".wholesaleToggle").on("change", function() {
                    const isChecked = $(this).is(":checked");
                    $(".wholesaleToggle").prop("checked", isChecked);

                    if (isChecked) {
                        $(".quotationToggle").prop("checked", false);
                        $(".advanceReceiptToggle").prop("checked", false);
                        $(".rentalToggle").prop("checked", false);
                        $("#deposit_advance_section").hide();
                        $("#quotation_status").val("sales");
                        $("#paymentSection").slideDown();
                        if (window.enabledPaymentMethods && window.enabledPaymentMethods.includes('pay_later')) {
                            $("#payLaterOption").show();
                        }
                    }

                    applyWholesalePricesToCart(isChecked);

                    if (typeof window.updateOrderNumberBadge === 'function') {
                        window.updateOrderNumberBadge();
                    }
                    renderSelectedItems();
                    if (typeof window.calculateTotals === "function") {
                        window.calculateTotals();
                    } else if (typeof calculateTotals === "function") {
                        calculateTotals();
                    }

                    const $billInput = $('.pos-bill-search-input:focus');
                    if ($billInput.length && $billInput.val().trim().length) {
                        $billInput.trigger('input');
                    }
                });

            });

            selectedItems = new Map();
            // console.log('authToken', authToken);
            // console.log('selectedSubAdminId', selectedSubAdminId);
            // Setup CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize tax rows as hidden on page load
            $(".taxList").hide();

            function formatCurrency(amount, symbol, position) {
                return position === 'right' ? amount + symbol : symbol + amount;
            }

            function parseItemPriceValue(price) {
                return parseFloat(String(price).replace(/[^0-9.-]+/g, "")) || 0;
            }

            function parseQuantityValue(quantity) {
                const parsedQuantity = parseFloat(String(quantity).replace(/[^0-9.-]+/g, ""));
                return Number.isFinite(parsedQuantity) ? parsedQuantity : 0;
            }

            // Share quantity parser across script blocks.
            window.parseQuantityValue = parseQuantityValue;

            // Share stock availability UI updater across script blocks.
            window.refreshProductAvailabilityUI = function() {
                const allowOutOfStockForQuotation = isQuotationModeEnabled();

                $('.productset').each(function() {
                    const $product = $(this);
                    const stock = window.parseQuantityValue($product.data('stock'));
                    const isWarrantyCategory = isWarrantyCategoryProduct({
                        is_warranty_category: $product.data('is-warranty-category'),
                        category_name: $product.data('category-name')
                    });
                    const $button = $product.find('.productsetbtn button');

                    if (stock <= 0 && !isWarrantyCategory) {
                        $product.removeClass('disabled-product');
                        if ($button.length) {
                            $button.prop('disabled', false);
                        }
                    } else {
                        $product.removeClass('disabled-product');
                        if ($button.length) {
                            $button.prop('disabled', false);
                        }
                    }
                });
                
                if (typeof window.updateTotalItems === 'function') {
                    // Update all product card labels to match current cart state
                    $(".card-qty-label").hide().text('');
                    selectedItems.forEach(item => {
                        let qty = parseQuantityValue(item.quantity);
                        if (qty > 0) {
                            $(`.card-qty-label[data-product-id='${item.productId}']`).text(`Added ${qty} Qty`).show();
                        }
                    });
                }
            };

            function formatQuantityValue(quantity) {
                const parsedQuantity = parseQuantityValue(quantity);
                return Number.isInteger(parsedQuantity) ? String(parsedQuantity) : String(parsedQuantity);
            }

            window.normalizePosProductGst = function(rawGst) {
                if (!rawGst) return [];
                let gstData = rawGst;
                if (typeof gstData === 'string') {
                    try {
                        gstData = JSON.parse(gstData.replace(/\\/g, ''));
                    } catch (e) {
                        return [];
                    }
                }
                if (!Array.isArray(gstData)) return [];

                return gstData.map(function(tax) {
                    if (!tax || typeof tax !== 'object') return null;
                    let rate = parseFloat(tax.tax_rate != null ? tax.tax_rate : tax.rate);
                    if (!Number.isFinite(rate) || rate <= 0) {
                        const nameMatch = String(tax.tax_name || '').match(/(\d+(?:\.\d+)?)\s*%/);
                        rate = nameMatch ? parseFloat(nameMatch[1]) : 0;
                    }
                    if (!Number.isFinite(rate) || rate <= 0) return null;
                    return {
                        tax_name: String(tax.tax_name || tax.name || 'GST').replace(/\s*\(\d+(?:\.\d+)?%\s*\)\s*$/, '').trim() || 'GST',
                        tax_rate: rate,
                        tax_id: tax.tax_id || tax.id || null
                    };
                }).filter(Boolean);
            };

            window.calculateTotals = function() {

                const firstTaxElement = $(".tax-value").first();
                const currencySymbol = firstTaxElement.data("symbol") || '{{ $currency_symbol }}';
                const currencyPosition = firstTaxElement.data("position") || '{{ $currency_position }}';

                let baseSubtotal = 0;
                let subtotalAfterDiscount = 0;
                let totalProductDiscount = 0;
                let totalProductGst = 0;
                let labourTotal = 0;

                const gstOption = $("input[name='gst_option']:checked").val();

                // Calculate Labour Total
                $(".labour-row").each(function() {
                    let qty = parseFloat($(this).find(".labour-qty").val()) || 0;
                    let price = parseFloat($(this).find(".labour-price").val()) || 0;
                    labourTotal += qty * price;
                });

                selectedItems.forEach(function(item) {

                    let price = parseItemPriceValue(item.price);
                    let isRentalItem = $(".rentalToggle").is(":checked") && Number(item
                        .is_rent_available) === 1;
                    let rentMultiplier = isRentalItem ? (item.rent_value || 1) : 1;
                    let enteredAmount = price * rentMultiplier * item.quantity;

                    let baseAmount = enteredAmount;
                    let productGst = 0;

                    // ===== GST CALCULATION =====
                    if (gstOption === "with" && item.gst_option === "with_gst" && item.product_gst) {
                        try {
                            const gstData = window.normalizePosProductGst(item.product_gst);
                            let totalGstRate = 0;
                            gstData.forEach(function(tax) {
                                totalGstRate += (tax.tax_rate / 100);
                            });

                            if (totalGstRate > 0) {
                                baseAmount = enteredAmount;
                                productGst = enteredAmount * totalGstRate;
                            }

                        } catch (e) {
                            // console.error("GST error:", e);
                        }
                    }

                    // ✅ GST INCLUDED AMOUNT
                    let gstIncludedAmount = enteredAmount + productGst;

                    // ===== DISCOUNT ON GST INCLUDED PRICE =====
                    let discountAmount = 0;

                    if (item.discount_percentage > 0) {
                        discountAmount =
                            (gstIncludedAmount * item.discount_percentage) / 100;
                    }

                    item.discount_amount = discountAmount;
                    selectedItems.set(item.id, item);

                    // ✅ FINAL PRODUCT AMOUNT AFTER DISCOUNT
                    let afterDiscountAmount = gstIncludedAmount - discountAmount;

                    // ===== TOTALS =====
                    baseSubtotal += baseAmount;
                    subtotalAfterDiscount += afterDiscountAmount;
                    totalProductDiscount += discountAmount;
                    totalProductGst += productGst;
                });
                // ================= ADDITIONAL (BILL) DISCOUNT =================
                window._posAfterItemDiscountBase = Math.max(0, subtotalAfterDiscount - totalProductGst);
                let additionalDiscountPercent = Math.max(0, Math.min(100, parseFloat($("#additional_discount_percent").val()) || 0));
                let additionalDiscountAmount = Math.max(0, parseFloat($("#additional_discount_amount").val()) || 0);
                const discountMode = window._posAdditionalDiscountMode || (additionalDiscountPercent > 0 ? 'percent' : 'amount');
                // % mode: amount follows cart. ₹ mode: amount stays fixed, % is re-derived.
                if (discountMode === 'percent' && additionalDiscountPercent > 0) {
                    additionalDiscountAmount = (window._posAfterItemDiscountBase * additionalDiscountPercent) / 100;
                } else if (additionalDiscountAmount > 0) {
                    if (window._posAfterItemDiscountBase > 0) {
                        additionalDiscountPercent = Math.min(100, (additionalDiscountAmount / window._posAfterItemDiscountBase) * 100);
                    } else {
                        additionalDiscountPercent = 0;
                        additionalDiscountAmount = 0;
                    }
                } else if (additionalDiscountPercent > 0) {
                    additionalDiscountAmount = (window._posAfterItemDiscountBase * additionalDiscountPercent) / 100;
                } else {
                    additionalDiscountPercent = 0;
                    additionalDiscountAmount = 0;
                }
                if (additionalDiscountAmount > window._posAfterItemDiscountBase) {
                    additionalDiscountAmount = window._posAfterItemDiscountBase;
                    additionalDiscountPercent = window._posAfterItemDiscountBase > 0
                        ? (additionalDiscountAmount / window._posAfterItemDiscountBase) * 100
                        : 0;
                }
                // Keep visible % / ₹ inputs in sync when products are added/updated/removed
                window._syncingAdditionalDiscountFields = true;
                if (additionalDiscountPercent > 0 || additionalDiscountAmount > 0) {
                    $("#additional_discount_percent").val(additionalDiscountPercent > 0 ? Number(additionalDiscountPercent.toFixed(2)).toString() : '');
                    $("#additional_discount_amount").val(additionalDiscountAmount > 0 ? additionalDiscountAmount.toFixed(2) : '');
                } else {
                    const pctRaw = ($("#additional_discount_percent").val() || '').trim();
                    const amtRaw = ($("#additional_discount_amount").val() || '').trim();
                    if (pctRaw === '0' || pctRaw === '0.00') $("#additional_discount_percent").val('');
                    if (amtRaw === '0' || amtRaw === '0.00') $("#additional_discount_amount").val('');
                }
                window._syncingAdditionalDiscountFields = false;
                let globalDiscountAmount = additionalDiscountAmount;
                let priceAfterGlobalDiscount = window._posAfterItemDiscountBase - globalDiscountAmount;

                // ================= FINAL TOTAL =================
                let shipping = parseFloat($("#shipping").val()) || 0;
                
                // Calculate Other Charges
                let otherChargesTotal = 0;
                $(".other-charge-amount").each(function() {
                    otherChargesTotal += parseFloat($(this).val()) || 0;
                });
                
                // Calculate Exchange Amount
                let exchangeTotal = parseFloat($("#pos_exchange_amount").val()) || 0;
                
                const rawTdsPercentage = isTdsEnabled ? ($("#tds_percentage").val() || "").trim() : "";
                const tdsPercentageInput = isTdsEnabled ? (parseFloat(rawTdsPercentage) || 0) : 0;
                const tdsPercentage = Math.max(0, Math.min(100, tdsPercentageInput));

                const grossBeforeExchange = Math.max(0, priceAfterGlobalDiscount + totalProductGst + shipping + labourTotal + otherChargesTotal);
                const exchangeSurplus = Math.max(0, exchangeTotal - grossBeforeExchange);
                const preTdsTotal = Math.max(0, grossBeforeExchange - exchangeTotal);
                const tdsAmount = isTdsEnabled ? (preTdsTotal * tdsPercentage) / 100 : 0;

                let finalTotal = preTdsTotal - tdsAmount;
                let roundedTotal = Math.round(finalTotal);
                let roundOffAmount = roundedTotal - finalTotal;

                let advancePaid = @json($isPosEdit ?? false) ? 0 : (parseFloat("{{ $convertOrderAdvancePaid ?? 0 }}") || 0);
                let payableTotal = roundedTotal;
                if (advancePaid > 0) {
                    payableTotal = Math.max(0, roundedTotal - advancePaid);
                }

                // Apply customer deposit (from previous exchange surplus deposits)
                const customerDepositBalance = Math.max(0, parseFloat($("#pos_customer_deposit_balance").val()) || 0);
                const payableBeforeDeposit = payableTotal;
                let customerDepositApplied = 0;
                if ($("#use_customer_deposit").is(":checked") && customerDepositBalance > 0 && payableBeforeDeposit > 0) {
                    let applyRaw = parseFloat($("#customer_deposit_apply_amount").val());
                    if (!Number.isFinite(applyRaw) || applyRaw < 0) {
                        applyRaw = Math.min(customerDepositBalance, payableBeforeDeposit);
                        $("#customer_deposit_apply_amount").val(applyRaw.toFixed(2));
                    }
                    customerDepositApplied = Math.min(applyRaw, customerDepositBalance, payableBeforeDeposit);
                    payableTotal = Math.max(0, payableBeforeDeposit - customerDepositApplied);
                }
                $("#pos_customer_deposit_applied").val(customerDepositApplied > 0 ? customerDepositApplied.toFixed(2) : '0');
                $("#pos_exchange_surplus").val(exchangeSurplus > 0 ? exchangeSurplus.toFixed(2) : '0');

                const custId = $("#customer_name option:selected").val();
                const custName = ($("#customer_name option:selected").text() || '').trim();
                const showCustBalance = custId && /^\d+$/.test(String(custId)) && !/default customer/i.test(custName);
                $("#posCustomerBalanceChip").toggleClass('is-visible', !!showCustBalance);
                $("#posCustomerBalanceText").text(formatCurrency(customerDepositBalance.toFixed(2), currencySymbol, currencyPosition));

                let afterBalance = customerDepositBalance;
                if (exchangeSurplus > 0 && $("input[name='exchange_surplus_action_radio']:checked").val() === 'deposit') {
                    afterBalance += exchangeSurplus;
                }
                afterBalance = Math.max(0, afterBalance - customerDepositApplied);

                let depositAmount = parseFloat($("#deposit_amount").val()) || 0;
                let balanceDue = payableTotal;
                if ($(".rentalToggle").is(":checked")) {
                    balanceDue = Math.max(0, payableTotal - depositAmount);
                }

                // ================= UI UPDATE =================

                $(".setvalue li:nth-child(1) h6").html(
                    formatCurrency(baseSubtotal.toFixed(2), currencySymbol, currencyPosition)
                );

                $(".discount-amount, .item-discount-amount").html(
                    formatCurrency(totalProductDiscount.toFixed(2), currencySymbol,
                        currencyPosition)
                );
                $(".additional-discount-percent-label").text(additionalDiscountPercent.toFixed(2));
                $(".additional-discount-amount").html(
                    formatCurrency(additionalDiscountAmount.toFixed(2), currencySymbol, currencyPosition)
                );

                $(".price-after-discount").html(
                    `${formatCurrency((priceAfterGlobalDiscount + totalProductGst).toFixed(2), currencySymbol, currencyPosition)}`
                );

                $(".shipping-cost-summary").html(
                    formatCurrency(shipping.toFixed(2), currencySymbol, currencyPosition)
                );
                
                if(otherChargesTotal > 0) {
                    $(".other-charges-summary-row").show();
                    $(".other-charges-total-summary").html(
                        formatCurrency(otherChargesTotal.toFixed(2), currencySymbol, currencyPosition)
                    );
                } else {
                    $(".other-charges-summary-row").hide();
                }

                if(exchangeTotal > 0) {
                    $(".exchange-charges-summary-row").show();
                    $(".exchange-charges-total-summary").html(
                        `- ${formatCurrency(exchangeTotal.toFixed(2), currencySymbol, currencyPosition)}`
                    );
                } else {
                    $(".exchange-charges-summary-row").hide();
                }

                if (exchangeSurplus > 0) {
                    $(".exchange-surplus-summary-row").show();
                    $(".exchange-surplus-total-summary").html(
                        formatCurrency(exchangeSurplus.toFixed(2), currencySymbol, currencyPosition)
                    );
                    $("#exchange_surplus_options").show();
                    $("#exchange_surplus_options_amount").text(
                        formatCurrency(exchangeSurplus.toFixed(2), currencySymbol, currencyPosition)
                    );
                    const selectedSurplusAction = $("input[name='exchange_surplus_action_radio']:checked").val() || '';
                    $("#pos_exchange_surplus_action").val(selectedSurplusAction);
                    $("#exchange_surplus_deposit_hint").toggle(selectedSurplusAction === 'deposit');
                    $("#pos_surplus_balance_snapshot").show();
                    $("#posSurplusBalanceCurrent").text(formatCurrency(customerDepositBalance.toFixed(2), currencySymbol, currencyPosition));
                    $("#posSurplusBalanceAfter").text(formatCurrency(afterBalance.toFixed(2), currencySymbol, currencyPosition));
                } else {
                    $(".exchange-surplus-summary-row").hide();
                    $("#exchange_surplus_options").hide();
                    $("input[name='exchange_surplus_action_radio']").prop('checked', false);
                    $("#pos_exchange_surplus_action").val('');
                    $("#exchange_surplus_deposit_hint").hide();
                    $("#pos_surplus_balance_snapshot").hide();
                }

                if (customerDepositBalance > 0 && payableBeforeDeposit > 0) {
                    $(".customer-deposit-apply-row").show();
                    $(".customer-deposit-balance-label").text(
                        'Current: ' + formatCurrency(customerDepositBalance.toFixed(2), currencySymbol, currencyPosition)
                    );
                    $(".customer-deposit-after-label").text(
                        'After this: ' + formatCurrency(afterBalance.toFixed(2), currencySymbol, currencyPosition)
                    );
                    $("#customer_deposit_apply_amount").prop('disabled', !$("#use_customer_deposit").is(":checked"));
                    const maxApply = Math.min(customerDepositBalance, payableBeforeDeposit);
                    $("#customer_deposit_apply_amount").attr('max', maxApply);
                } else {
                    $(".customer-deposit-apply-row").hide();
                    if (customerDepositBalance <= 0 || payableBeforeDeposit <= 0) {
                        $("#use_customer_deposit").prop('checked', false);
                        $("#customer_deposit_apply_amount").val('').prop('disabled', true);
                        $("#pos_customer_deposit_applied").val('0');
                    }
                }

                if (isTdsEnabled) {
                    $("#tds_amount").val(tdsAmount.toFixed(2));
                    $(".tds-percentage-summary").text(tdsPercentage.toFixed(2));
                    $(".tds-amount-summary").html(
                        `- ${formatCurrency(tdsAmount.toFixed(2), currencySymbol, currencyPosition)}`
                    );
                    $(".tds-summary-row").show();
                } else {
                    $(".tds-summary-row").hide();
                }

                $(".round-off-amount").html(
                    formatCurrency(roundOffAmount.toFixed(2), currencySymbol, currencyPosition)
                );

                if (advancePaid > 0) {
                    $(".advance-paid-amount-summary").html(
                        `- ${formatCurrency(advancePaid.toFixed(2), currencySymbol, currencyPosition)}`
                    );
                    $(".advance-paid-summary-row").show();
                } else {
                    $(".advance-paid-summary-row").hide();
                }

                $(".total-value h6").html(
                    formatCurrency(payableTotal.toFixed(0), currencySymbol, currencyPosition)
                );

                $(".btn-totallabel h6").html(
                    'Total Amount : ' + formatCurrency(payableTotal.toFixed(0), currencySymbol,
                        currencyPosition)
                );

                $("input[name='subtotal']").val(baseSubtotal.toFixed(2));
                $("input[name='discount_amount']").val((totalProductDiscount + globalDiscountAmount).toFixed(2));
                $("#bill_discount_percent").val(additionalDiscountPercent.toFixed(2));
                $("input[name='price_after_discount']").val((priceAfterGlobalDiscount + totalProductGst)
                    .toFixed(2));
                $("input[name='round_off']").val(roundOffAmount.toFixed(2));
                // Payable after deposit apply (order total still recalculated on server)
                $("input[name='total']").val(payableTotal.toFixed(0));

                if ($(".rentalToggle").is(":checked")) {
                    $(".deposit-summary-row").show();
                    $(".deposit-amount-summary").html(
                        `- ${formatCurrency(depositAmount.toFixed(2), currencySymbol, currencyPosition)}`
                    );
                    $(".balance-due-summary-row").show();
                    $(".balance-due-amount-summary").html(
                        formatCurrency(balanceDue.toFixed(2), currencySymbol, currencyPosition)
                    );
                } else {
                    $(".deposit-summary-row").hide();
                    $(".balance-due-summary-row").hide();
                }

                // Expose balanceDue to global window object for payment ui
                window.currentBalanceDue = balanceDue;
                window.currentDepositAmount = depositAmount;

                if (typeof window.refreshPosPaymentUi === "function") {
                    window.refreshPosPaymentUi(false);
                }

                // ================= GST DISPLAY =================

                if (gstOption === "with" && totalProductGst > 0) {
                    if ($(".gst-summary-row").length) {
                        $(".gst-summary-row")
                            .show()
                            .find("h6")
                            .html(formatCurrency(totalProductGst.toFixed(2), currencySymbol, currencyPosition));
                    }
                } else {
                    $(".gst-summary-row").hide();
                }

                if (totalProductDiscount > 0) {
                    $(".discount-summary-row").show();
                } else {
                    $(".discount-summary-row").hide();
                }
                if (additionalDiscountAmount > 0) {
                    $(".additional-discount-summary-row").show();
                } else {
                    $(".additional-discount-summary-row").hide();
                }
                if ((totalProductDiscount + additionalDiscountAmount) > 0) {
                    $(".subtotal-summary-row").show();
                } else {
                    $(".subtotal-summary-row").hide();
                }

                if (shipping > 0) {
                    $(".shipping-summary-row").show();
                } else {
                    $(".shipping-summary-row").hide();
                }

                // ================= LABOUR DISPLAY =================

                if (labourTotal > 0) {
                    $(".labour-summary-row")
                        .show()
                        .find("h6")
                        .html(formatCurrency(labourTotal.toFixed(2), currencySymbol, currencyPosition));
                } else {
                    $(".labour-summary-row").hide();
                }

                if (typeof window.updateRemainingBalance === "function") {
                    window.updateRemainingBalance();
                }
            }


            window.updateTotalItems = function() {
                let totalQty = 0;
                $(".card-qty-label").hide().text(''); // reset all labels

                selectedItems.forEach(item => {
                    totalQty += parseQuantityValue(item.quantity);
                    let qty = parseQuantityValue(item.quantity);
                    if (qty > 0) {
                        $(`.card-qty-label[data-product-id='${item.productId}']`).text(`Added ${qty} Qty`).show();
                    }
                });
                $(".totalitem h4").text("Total items : " + formatQuantityValue(totalQty));
                if (window.lastScannedCartId) {
                    $('.totalitem h4').addClass('just-scanned-total');
                    setTimeout(function() {
                        $('.totalitem h4').removeClass('just-scanned-total');
                    }, 1800);
                }
                renderSelectedItems();
                calculateTotals();
                if (typeof window.renderScannerCartList === 'function') {
                    window.renderScannerCartList();
                }
                if (typeof window.refreshActivePosBillTab === 'function') {
                    window.refreshActivePosBillTab();
                }
            }

            function getCartItemsForDisplay() {
                const items = [];
                selectedItems.forEach(function(item) {
                    items.push(item);
                });
                const lastId = window.lastScannedCartId ? String(window.lastScannedCartId) : '';
                items.sort(function(a, b) {
                    if (lastId) {
                        if (String(a.id) === lastId) return 1;
                        if (String(b.id) === lastId) return -1;
                    }
                    return (a.addedAt || 0) - (b.addedAt || 0);
                });
                return items;
            }

            function showPosAddedNotice() {
                /* Keep the green banner out of the bill layout. The row already shows Just added. */
            }

            function isLastScannedCartItem(item) {
                return !!(window.lastScannedCartId && String(item.id) === String(window.lastScannedCartId));
            }

            function scrollToLatestCartItem(cartId) {
                if (!cartId) {
                    return;
                }
                const cartIdStr = String(cartId);

                function getRowTopWithin(container, row) {
                    let top = 0;
                    let node = row;
                    while (node && node !== container) {
                        top += node.offsetTop;
                        node = node.offsetParent;
                        if (!node || !container.contains(node)) {
                            break;
                        }
                    }
                    return top;
                }

                function runScroll() {
                    if (typeof window.syncPosBillTableHeight === 'function') {
                        window.syncPosBillTableHeight();
                    }

                    const row = document.querySelector(
                        '#pos-right-panel .product-table [data-cart-row="' + cartIdStr.replace(/"/g, '\\"') + '"]'
                    );
                    if (!row) {
                        return false;
                    }

                    const table = row.closest('.product-table');
                    if (table) {
                        const rowTop = getRowTopWithin(table, row);
                        const targetTop = rowTop + row.offsetHeight - table.clientHeight + 12;
                        table.scrollTo({
                            top: Math.max(0, targetTop),
                            behavior: 'smooth'
                        });
                        return true;
                    }

                    const panel = document.getElementById('pos-right-panel');
                    const paymentPanel = document.getElementById('paymentPanel');
                    if (panel) {
                        const rowRect = row.getBoundingClientRect();
                        const panelRect = panel.getBoundingClientRect();
                        const paymentHeight = paymentPanel ? paymentPanel.getBoundingClientRect().height : 0;
                        const visibleBottom = panelRect.bottom - paymentHeight - 16;
                        if (rowRect.bottom > visibleBottom) {
                            panel.scrollBy({
                                top: rowRect.bottom - visibleBottom,
                                behavior: 'smooth'
                            });
                        }
                        return true;
                    }

                    row.scrollIntoView({ block: 'end', behavior: 'smooth' });
                    return true;
                }

                requestAnimationFrame(function() {
                    requestAnimationFrame(function() {
                        if (!runScroll()) {
                            setTimeout(runScroll, 80);
                        }
                        setTimeout(runScroll, 220);
                    });
                });
            }

            window.syncPosBillTableHeight = function() {
                const panel = document.getElementById('pos-right-panel');
                const table = document.querySelector('#pos-right-panel .product-table');
                const billItems = document.querySelector('#pos-right-panel .pos-bill-items');
                const paymentPanel = document.getElementById('paymentPanel');
                const workspace = document.querySelector('#pos-right-panel .pos-bill-workspace');
                if (!panel || !table || !billItems || window.innerWidth < 992) {
                    if (table) {
                        table.style.maxHeight = '';
                    }
                    return;
                }

                // Dual-column layout: CSS flex handles both scrolls — clear JS max-height
                if (workspace) {
                    table.style.maxHeight = '';
                    table.style.overflowY = 'auto';
                    return;
                }

                const billItemsRect = billItems.getBoundingClientRect();
                const panelRect = panel.getBoundingClientRect();
                const paymentHeight = paymentPanel ? paymentPanel.getBoundingClientRect().height : 0;
                const totalItem = billItems.querySelector('.totalitem');
                const billHead = billItems.querySelector('.pos-bill-head');
                const chromeHeight =
                    (totalItem ? totalItem.offsetHeight : 0) +
                    (billHead ? billHead.offsetHeight : 0) +
                    20;
                const available = panelRect.bottom - billItemsRect.top - paymentHeight - chromeHeight;

                table.style.maxHeight = Math.max(160, Math.floor(available)) + 'px';
                table.style.overflowY = 'auto';
            };

            window.renderScannerCartList = function() {
                const $list = $('#scannerCartList');
                if (!$list.length) {
                    return;
                }

                const currencySymbol = '{{ $currency_symbol }}';
                const currencyPosition = '{{ $currency_position }}';
                const items = getCartItemsForDisplay();
                let count = items.length;
                let totalQty = 0;
                let html = '';
                let latestName = '';
                let latestQty = 0;

                items.forEach(function(item) {
                    const qty = parseQuantityValue(item.quantity);
                    totalQty += qty;
                    const price = parseItemPriceValue(item.price);
                    const priceText = currencyPosition === 'right'
                        ? price.toFixed(2) + currencySymbol
                        : currencySymbol + price.toFixed(2);
                    const isLatest = isLastScannedCartItem(item);
                    if (isLatest) {
                        latestName = item.name;
                        latestQty = qty;
                    }
                    html += `
                        <div class="scanner-cart-row${isLatest ? ' just-scanned' : ''}" data-scan-row="${item.id}">
                            <img src="${item.image || ''}" alt="">
                            <div class="scanner-cart-info">
                                <h6 title="${item.name}">${item.name}${isLatest ? ' <span class="just-scanned-badge">Just added</span>' : ''}</h6>
                                <small>${priceText}</small>
                            </div>
                            <div class="scanner-cart-qty">
                                <input type="button" value="-" class="button-minus dec button" data-id="${item.id}">
                                <input type="text" value="${formatQuantityValue(qty)}" class="quantity-field" data-id="${item.id}" data-stock="${item.stock}" inputmode="decimal">
                                <input type="button" value="+" class="button-plus inc button" data-id="${item.id}">
                            </div>
                            <button type="button" class="scanner-cart-remove remove-item" data-id="${item.id}" title="Remove">&times;</button>
                        </div>`;
                });

                if (!html) {
                    html = '<div class="barcode-scanner-empty">Scan a product to add it here</div>';
                } else if (latestName) {
                    html = `<div class="scanner-added-banner">Added to bill: ${latestName}<span>Qty ${formatQuantityValue(latestQty)} · shown at bottom of the list</span></div>` + html;
                }

                $list.html(html);
                $('#scannerCartCount').text(count);
                $('#scannerCartQty').text(formatQuantityValue(totalQty));

                if ($('#barcodeScannerModal').hasClass('show')) {
                    const $latest = $list.find('.just-scanned').first();
                    if ($latest.length && $latest[0].scrollIntoView) {
                        $latest[0].scrollIntoView({ block: 'nearest', behavior: 'smooth', inline: 'nearest' });
                    }
                }
            };

            window.renderSelectedItems = renderSelectedItems;

            window.getPosBillSearchRowHtml = function() {
                return `<ul class="product-lists pos-bill-search-row" data-bill-search-row="1">
                    <li class="cart-col-sno">
                        <button type="button" class="pos-bill-search-plus" title="Create new product" aria-label="Create new product">+</button>
                    </li>
                    <li class="cart-col-item pos-bill-search-cell">
                        <div class="pos-bill-search-wrap">
                            <input type="text" class="form-control form-control-sm pos-bill-search-input" placeholder="Search name, category or barcode..." autocomplete="off">
                            <button type="button" class="btn btn-sm pos-create-product-btn" title="Create new product">
                                <i class="fas fa-plus"></i> Create product
                            </button>
                            <div class="pos-bill-search-results pos-search-dropdown list-group bg-white shadow"></div>
                        </div>
                    </li>
                    <li class="cart-col-qty"></li>
                    <li class="cart-col-disc"></li>
                    <li class="cart-col-amt"></li>
                    <li class="delete-col"></li>
                </ul>`;
            };

            window.resetPosSearchDropdownPosition = function($resultBox) {
                if (!$resultBox || !$resultBox.length) {
                    return;
                }
                $resultBox.each(function() {
                    const $box = $(this);
                    const $host = $box.data('pos-search-host');
                    const inputEl = $box.data('pos-search-input');
                    $box.removeClass('pos-search-dropdown-floating').css({
                        position: '',
                        top: '',
                        left: '',
                        right: '',
                        bottom: '',
                        width: '',
                        maxHeight: '',
                        zIndex: ''
                    });
                    if ($host && $host.length && !$host.find($box).length) {
                        $host.append($box);
                    }
                    if (inputEl) {
                        $(inputEl).removeData('pos-search-results');
                    }
                    $box.removeData('pos-search-host');
                    $box.removeData('pos-search-input');
                });
            };

            window.getPosSearchResultsForInput = function($input) {
                if (!$input || !$input.length) {
                    return $();
                }
                let $box = $input.data('pos-search-results');
                if ($box && $box.length) {
                    return $box;
                }
                $box = $input.siblings('.pos-bill-search-results');
                if ($box.length) {
                    return $box;
                }
                return $('.pos-bill-search-results').filter(function() {
                    return $(this).data('pos-search-input') === $input[0];
                });
            };

            window.positionPosSearchDropdown = function($input, $resultBox) {
                if (!$input || !$input.length || !$resultBox || !$resultBox.length) {
                    return;
                }

                const $wrap = $input.closest('.pos-bill-search-wrap, .scanner-search-wrap');
                if ($wrap.length && !$resultBox.data('pos-search-host')) {
                    $resultBox.data('pos-search-host', $wrap);
                }
                $resultBox.data('pos-search-input', $input[0]);
                $input.data('pos-search-results', $resultBox);

                // Escape overflow:hidden parents so the list stays fully clickable
                if ($resultBox.parent()[0] !== document.body) {
                    $('body').append($resultBox);
                }

                const rect = $input[0].getBoundingClientRect();
                const isBillSearch = $input.hasClass('pos-bill-search-input');
                const minW = isBillSearch ? 320 : 300;
                const maxW = isBillSearch ? 440 : 400;
                const width = Math.min(maxW, Math.max(minW, rect.width));
                let left = rect.left;
                if (left + width > window.innerWidth - 8) {
                    left = Math.max(8, window.innerWidth - width - 8);
                }

                const spaceBelow = window.innerHeight - rect.bottom - 12;
                const spaceAbove = rect.top - 12;
                const preferredMax = Math.min(320, Math.floor(window.innerHeight * 0.5));
                const openUp = spaceBelow < 180 && spaceAbove > spaceBelow;
                const available = Math.max(140, openUp ? spaceAbove : spaceBelow);
                const maxHeight = Math.min(preferredMax, available);

                const css = {
                    position: 'fixed',
                    left: Math.round(left) + 'px',
                    width: Math.round(width) + 'px',
                    maxHeight: Math.round(maxHeight) + 'px',
                    zIndex: 10060,
                    display: 'block',
                    visibility: 'visible'
                };

                if (openUp) {
                    css.top = 'auto';
                    css.bottom = Math.round(window.innerHeight - rect.top + 4) + 'px';
                } else {
                    css.top = Math.round(rect.bottom + 4) + 'px';
                    css.bottom = 'auto';
                }

                $resultBox.addClass('pos-search-dropdown-floating').css(css);
            };

            window.escapePosSearchHtml = function(value) {
                return String(value == null ? '' : value)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;');
            };

            window.setActivePosSearchItem = function($resultBox, index) {
                const $items = $resultBox.find('.pos-search-item');
                if (!$items.length) {
                    return;
                }
                let nextIndex = index;
                if (nextIndex < 0) {
                    nextIndex = $items.length - 1;
                }
                if (nextIndex >= $items.length) {
                    nextIndex = 0;
                }
                $items.removeClass('is-active');
                const $active = $items.eq(nextIndex).addClass('is-active');
                if ($active.length && $active[0].scrollIntoView) {
                    $active[0].scrollIntoView({ block: 'nearest' });
                }
                $resultBox.data('pos-search-active-index', nextIndex);
            };

            window.handlePosSearchKeyboard = function(e, $input, $resultBox) {
                if (!$resultBox || !$resultBox.is(':visible')) {
                    return;
                }
                const $items = $resultBox.find('.pos-search-item');
                if (!$items.length) {
                    return;
                }
                const key = e.key;
                let index = Number($resultBox.data('pos-search-active-index'));
                if (Number.isNaN(index)) {
                    index = -1;
                }

                if (key === 'ArrowDown') {
                    e.preventDefault();
                    window.setActivePosSearchItem($resultBox, index + 1);
                    return;
                }
                if (key === 'ArrowUp') {
                    e.preventDefault();
                    window.setActivePosSearchItem($resultBox, index - 1);
                    return;
                }
                if (key === 'Enter' && index >= 0) {
                    e.preventDefault();
                    $items.eq(index).trigger('click');
                    return;
                }
                if (key === 'Escape') {
                    e.preventDefault();
                    window.resetPosSearchDropdownPosition($resultBox);
                    $resultBox.hide().empty();
                }
            };

            window.repositionVisiblePosSearchDropdowns = function() {
                $('.pos-bill-search-input').each(function() {
                    const $input = $(this);
                    const $results = window.getPosSearchResultsForInput($input);
                    if ($results.is(':visible')) {
                        window.positionPosSearchDropdown($input, $results);
                    }
                });
            };

            if (!window._posSearchDropdownListenersBound) {
                window._posSearchDropdownListenersBound = true;
                $(window).on('resize.posSearchDropdown scroll.posSearchDropdown', function() {
                    window.repositionVisiblePosSearchDropdowns();
                });
                $(document).on('scroll.posSearchDropdown', '#pos-right-panel, #pos-right-panel .product-table, .page-wrapper, .pos-mall-layout', function() {
                    window.repositionVisiblePosSearchDropdowns();
                });
                $(document).on('click.posSearchDropdown', function(e) {
                    if ($(e.target).closest('.pos-bill-search-results, .pos-bill-search-input, .pos-search-item, .close-search-result').length) {
                        return;
                    }
                    window.resetPosSearchDropdownPosition($('.pos-bill-search-results'));
                    $('.pos-bill-search-results').hide().empty();
                });
            }

            window.buildPosSearchProductListItemHtml = function(product) {
                const isRentalActive = $(".rentalToggle").is(":checked");
                const isWholesaleActive = isWholesaleModeEnabled();
                if (isRentalActive && Number(product.is_rent_available) !== 1) {
                    return '';
                }

                const sellingRates = Array.isArray(product.selling_prices) ? product.selling_prices : [];
                const retailPrice = parseFloat(product.price);
                const wholesaleRates = getProductWholesaleRates(product);
                let salePrice = product.price_formatted ?? product.price ?? 'N/A';
                let salePriceValue = product.price;

                if (isRentalActive && product.rent_price) {
                    salePrice = product.rent_price_formatted ?? product.rent_price;
                    salePriceValue = product.rent_price;
                } else if (isWholesaleActive) {
                    if (!wholesaleRates.length) {
                        return '';
                    }
                    if (wholesaleRates.length === 1) {
                        salePriceValue = wholesaleRates[0].price;
                        salePrice = product.wholesale_price_formatted ?? Number(wholesaleRates[0].price).toFixed(2);
                    } else {
                        const minWholesale = Math.min.apply(null, wholesaleRates.map(function(rate) {
                            return parseFloat(rate.price);
                        }));
                        salePriceValue = minWholesale;
                        salePrice = 'From ' + (product.wholesale_price_formatted || Number(minWholesale).toFixed(2));
                    }
                }
                let mrpHtml = '';
                if (sellingRates.length) {
                    const primaryRate = sellingRates[0];
                    if (primaryRate && primaryRate.mrp != null && primaryRate.mrp !== '' &&
                        Math.abs(parseFloat(primaryRate.mrp) - parseFloat(primaryRate.price || product.price || 0)) > 0.001) {
                        mrpHtml = '<span class="pos-search-item-mrp">' + window.escapePosSearchHtml(Number(primaryRate.mrp).toFixed(2)) + '</span>';
                    }
                }

                const barcodes = String(product.barcode || '').split(',').map(function(code) {
                    return code.trim();
                }).filter(Boolean);
                const visibleCodes = barcodes.slice(0, 1);
                const codeText = visibleCodes.length
                    ? visibleCodes.map(function(code) {
                        return window.escapePosSearchHtml(code);
                    }).join(', ')
                    : (product.SKU ? window.escapePosSearchHtml(product.SKU) : '');

                const searchStock = window.parseQuantityValue(product.quantity ?? product.stock ?? product.current_stock ?? product.available_qty ?? 0);
                const searchWarranty = typeof isWarrantyCategoryProduct === 'function' ? isWarrantyCategoryProduct(product) : false;
                let extraRateStock = 0;
                (Array.isArray(product.selling_prices) ? product.selling_prices : []).forEach(function(rate) {
                    if (!rate || rate.qty === undefined || rate.qty === null || rate.qty === '') return;
                    const ratePrice = parseFloat(rate.price);
                    const mainPrice = parseFloat(product.price);
                    if (Number.isFinite(mainPrice) && Number.isFinite(ratePrice) && Math.abs(ratePrice - mainPrice) < 0.001) return;
                    extraRateStock += window.parseQuantityValue(rate.qty);
                });
                const searchOos = !searchWarranty && searchStock <= 0 && extraRateStock <= 0;
                const sellingPricesAttr = encodeURIComponent(JSON.stringify(sellingRates));
                const productName = window.escapePosSearchHtml(product.name ?? 'N/A');
                const productNameAttr = (product.name ?? 'N/A').replace(/"/g, '&quot;');
                const gstBadge = product.gst_option === 'with_gst'
                    ? '<span class="pos-search-item-badge is-gst">GST</span>'
                    : '<span class="pos-search-item-badge is-no-gst">No GST</span>';
                const stockQtyText = window.formatQuantityValue ? window.formatQuantityValue(searchStock) : searchStock;
                let stockLabel = 'Stock ' + stockQtyText;
                const mainSearchPrice = parseFloat(product.price);
                (Array.isArray(product.selling_prices) ? product.selling_prices : []).forEach(function(rate) {
                    if (!rate || /wholesale/i.test(String(rate.name || ''))) return;
                    const ratePrice = parseFloat(rate.price);
                    if (Number.isFinite(mainSearchPrice) && Number.isFinite(ratePrice) && Math.abs(ratePrice - mainSearchPrice) < 0.001) return;
                    if (rate.qty === undefined || rate.qty === null || rate.qty === '') return;
                    const rateQtyText = window.formatQuantityValue ? window.formatQuantityValue(rate.qty) : rate.qty;
                    stockLabel += ' · ' + (rate.name || 'Rate') + ' ' + rateQtyText;
                });
                const stockBadge = searchOos
                    ? '<span class="pos-search-item-badge is-oos">Out of stock</span>'
                    : '<span class="pos-search-item-badge is-stock">' + window.escapePosSearchHtml(stockLabel) + '</span>';
                const wholesaleBadge = isWholesaleActive
                    ? (wholesaleRates.length > 1
                        ? '<span class="pos-search-item-badge is-gst">' + wholesaleRates.length + ' wholesale rates</span>'
                        : '<span class="pos-search-item-badge is-gst">Wholesale</span>')
                    : '';
                const wholesalePrice = wholesaleRates.length ? wholesaleRates[0].price : null;
                let productGstAttr = '';
                if (product.product_gst) {
                    productGstAttr = typeof product.product_gst === 'string'
                        ? product.product_gst
                        : JSON.stringify(product.product_gst);
                    productGstAttr = productGstAttr
                        .replace(/\\/g, '\\\\')
                        .replace(/'/g, '&#39;');
                }

                return `<button type="button" class="pos-search-item add-product${searchOos ? ' is-oos' : ''}"
                        data-id="${product.id}"
                        data-name="${productNameAttr}"
                        data-price="${salePriceValue}"
                        data-regular-price="${Number.isFinite(retailPrice) ? retailPrice : salePriceValue}"
                        data-wholesale-price="${wholesalePrice != null && !isNaN(wholesalePrice) ? wholesalePrice : ''}"
                        data-image="${product.image}"
                        data-stock="${searchStock}"
                        data-category="${product.category_id ?? 0}"
                        data-category-name="${product.category_name ?? ''}"
                        data-is-warranty-category="${isWarrantyCategoryProduct(product) ? 1 : 0}"
                        data-gst-option="${product.gst_option || 'without_gst'}"
                        data-product-gst='${productGstAttr}'
                        data-purchase-price="${product.purchase_price || 0}"
                        data-rent="${product.is_rent_available || 0}"
                        data-rent-period="${product.rent_period || ''}"
                        data-rent-price="${product.rent_price || 0}"
                        data-selling-prices="${sellingPricesAttr}">
                    <span class="pos-search-item-thumb">
                        <img src="${product.image}" alt="">
                    </span>
                    <span class="pos-search-item-body">
                        <span class="pos-search-item-name" title="${productName}">${productName}</span>
                    </span>
                    <span class="pos-search-item-meta">
                        ${codeText ? '<span class="pos-search-item-code">' + codeText + '</span>' : ''}
                        ${wholesaleBadge}
                        ${gstBadge}
                        ${stockBadge}
                    </span>
                    <span class="pos-search-item-price">
                        ${window.escapePosSearchHtml(salePrice)}
                        ${mrpHtml}
                    </span>
                </button>`;
            };

            window.renderPosProductSearchResults = function($resultBox, products, $input, meta) {
                $resultBox.empty();
                let renderedCount = 0;
                const list = Array.isArray(products) ? products.slice(0, 50) : [];
                const totalMatches = meta && meta.total != null ? parseInt(meta.total, 10) : list.length;

                if (list.length > 0) {
                    // Build once — appending 2000 nodes one-by-one freezes the browser
                    const chunks = [];
                    list.forEach(function(product) {
                        const itemHtml = window.buildPosSearchProductListItemHtml(product);
                        if (itemHtml) {
                            chunks.push(itemHtml);
                            renderedCount++;
                        }
                    });
                    if (chunks.length) {
                        $resultBox.append(chunks.join(''));
                    }
                }

                const createBtnHtml = `<button type="button" class="pos-search-create-product">
                    <i class="fas fa-plus"></i> Create new product
                </button>`;

                if (!renderedCount) {
                    $resultBox.html(
                        '<div class="pos-search-empty">No matching products</div>' + createBtnHtml
                    );
                } else {
                    let label;
                    if (totalMatches > renderedCount) {
                        label = 'Showing ' + renderedCount + ' of ' + totalMatches + ' products — type more to narrow';
                    } else {
                        label = renderedCount === 1 ? '1 product' : renderedCount + ' products';
                    }
                    $resultBox.prepend(
                        `<div class="pos-search-dropdown-head">
                            <span>${label}</span>
                            <button type="button" class="btn-close close-search-result" aria-label="Close"></button>
                        </div>`
                    );
                    $resultBox.append(createBtnHtml);
                    $resultBox.data('pos-search-active-index', 0);
                    window.setActivePosSearchItem($resultBox, 0);
                }

                $resultBox.show();
                if ($input && $input.length) {
                    window.positionPosSearchDropdown($input, $resultBox);
                    requestAnimationFrame(function() {
                        window.positionPosSearchDropdown($input, $resultBox);
                    });
                }
            };

            function renderSelectedItems() {
                try {
                    var $productTable = $(".product-table");
                    var productHtml = "";
                    let rowNum = 0;

                    // Get the global GST option
                    const globalGstOption = $("input[name='gst_option']:checked").val();

                    getCartItemsForDisplay().forEach(function(item) {
                        rowNum++;
                        const hasProductGST = item.gst_option === "with_gst";
                        const gstRate = item.product_gst || null;
                        const showNoGstMessage = globalGstOption === "with" && !hasProductGST;

                        // Parse GST data if available AND global option is "with"
                        let gstDisplay = '';
                        let productGstTotal = 0;

                        const price = parseItemPriceValue(item.price);
                        let isRentalItem = $(".rentalToggle").is(":checked") && Number(item
                            .is_rent_available) === 1;
                        let rentMultiplier = isRentalItem ? (item.rent_value || 1) : 1;
                        
                        // Auto-populate rental dates if empty
                        if (isRentalItem && !item.rent_start_at) {
                            const start = moment();
                            item.rent_start_at = start.format("YYYY-MM-DD HH:mm:ss");
                            let end = start.clone();
                            const newDuration = item.rent_value || 1;
                            
                            if (item.rent_period === 'hour') {
                                end.add(newDuration, 'hours');
                            } else if (item.rent_period === 'day') {
                                end.add(newDuration, 'days');
                            } else if (item.rent_period === 'week') {
                                end.add(newDuration, 'weeks');
                            } else if (item.rent_period === 'month') {
                                end.add(newDuration, 'months');
                            } else if (item.rent_period === 'year') {
                                end.add(newDuration, 'years');
                            }
                            item.rent_end_at = end.format("YYYY-MM-DD HH:mm:ss");
                        }
                        const productTotal = price * rentMultiplier * item.quantity;

                        let gstIncludedTotal = productTotal;
                        let discountAmount = 0;
                        let finalProductTotal = productTotal;
                        let basePriceWithoutGst = productTotal;

                        if (globalGstOption === "with" && hasProductGST && gstRate) {

                            try {
                                const normalizedTaxes = window.normalizePosProductGst(gstRate);

                                let totalGstRate = 0;
                                normalizedTaxes.forEach(function(tax) {
                                    totalGstRate += tax.tax_rate / 100;
                                });

                                let totalGstAmount = 0;
                                if (totalGstRate > 0) {
                                    basePriceWithoutGst = productTotal;
                                    totalGstAmount = productTotal * totalGstRate;
                                }

                                gstDisplay = normalizedTaxes.map(function(tax) {
                                    const taxAmount = totalGstRate > 0
                                        ? totalGstAmount * ((tax.tax_rate / 100) / totalGstRate)
                                        : 0;
                                    productGstTotal += taxAmount;
                                    const rateLabel = Number.isInteger(tax.tax_rate)
                                        ? String(tax.tax_rate)
                                        : tax.tax_rate.toFixed(2).replace(/\.00$/, '');
                                    return `<span class="pos-gst-chip" title="${tax.tax_name} ${rateLabel}% = ${formatCurrency(taxAmount.toFixed(2), '{{ $currency_symbol }}', '{{ $currency_position }}')}">
                                        ${tax.tax_name} <strong>${rateLabel}%</strong>
                                        <span>${formatCurrency(taxAmount.toFixed(2), '{{ $currency_symbol }}', '{{ $currency_position }}')}</span>
                                    </span>`;
                                }).join('');

                                // ✅ Exclusive: GST Included Total is Base + GST
                                gstIncludedTotal = productTotal + totalGstAmount;

                                // ✅ Discount on GST Included Amount
                                discountAmount = (gstIncludedTotal * (item.discount_percentage || 0)) / 100;

                                finalProductTotal = gstIncludedTotal - discountAmount;

                            } catch (e) {
                                console.error("GST parse error:", e);
                            }

                        } else {

                            // ✅ Discount on Base Amount
                            discountAmount = (productTotal * (item.discount_percentage || 0)) / 100;

                            finalProductTotal = productTotal - discountAmount;
                        }



                        let rentalDateHtml = "";
                        if (isRentalItem) {
                            let dateRangeValue = "";
                            if (item.rent_start_at && item.rent_end_at) {
                                dateRangeValue = moment(item.rent_start_at).format("DD/MM hh:mm A") +
                                    " - " + moment(item.rent_end_at).format("DD/MM hh:mm A");
                            }
                            rentalDateHtml = `
                                <div class="rental-datetime mt-2">
                                    <input type="text" class="form-control form-control-sm rent-daterange" data-id="${item.id}" value="${dateRangeValue || ''}" placeholder="Select date & time range">
                                    <small class="text-muted">${item.rent_period}s : <span class="rent-duration" data-id="${item.id}">${item.rent_value || 1}</span></small>
                                    <div class="rent-error text-danger mt-1" data-id="${item.id}" style="display:none; font-size:11px;"></div>
                                </div>`;
                        }

                        let rentHtml = "";
                        if (isRentalItem) {
                            const rentalAmount = price * (item.rent_value || 1);
                            let displayRentalAmount = formatCurrency(rentalAmount.toFixed(2),
                                '{{ $currency_symbol }}', '{{ $currency_position }}');
                            rentHtml = `
                                <div class="rent-box d-flex align-items-center gap-1 mt-2">
                                    <input type="number" class="rent-input" data-id="${item.id}" value="${item.rent_value || 1}" min="1" style="width: 50px;">
                                    <span class="rent-period">${item.rent_period}</span>
                                    <div class="rental-calculation ms-2" style="font-size: 13px; color: #22b428; font-weight: 600;">${displayRentalAmount}</div>
                                </div>
                            `;
                        }

                        // Build HTML for the product
                        const isLatestScan = isLastScannedCartItem(item);
                        const itemNameAttr = String(item.name || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;');
                        productHtml += `
                                    <ul class="product-lists${isLatestScan ? ' just-scanned' : ''}" data-cart-row="${item.id}">
                                        <li class="cart-col-sno">${rowNum}</li>
                                        <li class="cart-col-item">
                                            <div class="productimg">
                                                <div class="productimgs">
                                                    <img src="${item.image || '{{ env('ImagePath') }}/admin/assets/img/product/noimage.png'}" alt="" onerror="this.onerror=null;this.src='{{ env('ImagePath') }}/admin/assets/img/product/noimage.png';">
                                                </div>
                                                <div class="productcontet">
                                                    <h4 style="text-transform: capitalize;">
                                                        <span class="cart-item-name" title="${itemNameAttr}">${item.name}</span>
                                                        ${isLatestScan ? '<span class="just-scanned-badge">Just added</span>' : ''}
                                                        <a href="javascript:void(0);" class="ms-2 edit-item" data-id="${item.id}">
                                                            <img src="{{ env('ImagePath') . '/admin/assets/img/icons/edit-5.svg' }}" alt="edit">
                                                        </a>
                                                    </h4>

                                                    ${(globalGstOption === "with" && hasProductGST && gstDisplay) ?
                                                        `<div class="gst-info">${gstDisplay}</div>`
                                                        : ''
                                                    }
                                                    ${rentalDateHtml}
                                                    ${rentHtml}
                                                     </div>
                                                </div>
                                            </li>
                                            <li class="cart-col-qty">
                                                    <div class="increment-decrement d-flex align-items-center gap-2">
                                                        <div class="input-groups">
                                                            <input type="button" value="-" class="button-minus dec button" data-id="${item.id}">
                                                            <input type="text" name="quantity" value="${formatQuantityValue(item.quantity)}" class="quantity-field" data-id="${item.id}" data-stock="${item.stock}" inputmode="decimal">
                                                            <input type="button" value="+" class="button-plus inc button" data-id="${item.id}">
                                                        </div>
                                                        <div class="price-input-container">
                                                            <input
                                                                type="text"
                                                                class="product-price-input product-price-field form-control form-control-sm"
                                                                data-id="${item.id}"
                                                                data-purchase-price="${item.purchase_price || 0}"
                                                                value="${price.toFixed(2)}"
                                                                min="0"
                                                                step="0.01">
                                                            ${(item.purchase_price > 0 && price < item.purchase_price && !($(".rentalToggle").is(":checked"))) ?
                                                                `<div class="below-purchase-warning" style="color:#dc3545; font-size:10px; margin-top:2px; line-height:1.2;">Below last purchase<br>&#8377;${parseFloat(item.purchase_price).toFixed(2)}</div>`
                                                                : `<div class="below-purchase-warning" style="display:none; color:#dc3545; font-size:10px; margin-top:2px; line-height:1.2;"></div>`
                                                            }
                                                     </div>
                                                </div>
                                            </li>
                                            <li class="cart-col-disc">
                                                <div class="product-discount-box">
                                                    <div class="discount-field is-percent" title="Discount percentage (%)">
                                                        <input type="text"
                                                            id="pos-disc-pct-${item.id}"
                                                            class="product-discount-percentage"
                                                            data-id="${item.id}"
                                                            value="${item.discount_percentage || 0}"
                                                            min="0"
                                                            max="100"
                                                            placeholder="0"
                                                            inputmode="decimal"
                                                            aria-label="Discount percentage">
                                                    </div>
                                                    <div class="discount-field is-amount" title="Discount amount (₹)">
                                                        <input type="text"
                                                            id="pos-disc-amt-${item.id}"
                                                            class="product-discount-amount"
                                                            data-id="${item.id}"
                                                            value="${item.discount_amount || 0}"
                                                            min="0"
                                                            step="0.01"
                                                            placeholder="0"
                                                            inputmode="decimal"
                                                            aria-label="Discount amount">
                                                    </div>
                                                </div>
                                                ${(item.imei_no || (item.available_serials && item.available_serials.length > 0)) ? `
                                                        <div class="serial-no-box" style="margin-top: 10px; padding: 0 10px;">
                                                            <label style="font-size: 12px; color: #555; font-weight: 600; display: block; margin-bottom: 2px;">IMEI No:</label>
                                                            <select class="form-control cart-imei-select" data-id="${item.id}" style="font-size: 12px; padding: 2px 5px; height: 35px;">
                                                                <option value="">Select IMEI</option>
                                                                ${(item.available_serials && item.available_serials.length > 0) ? 
                                                                    item.available_serials.map(s => `<option value="${s}" ${s === item.imei_no ? 'selected' : ''}>${s}</option>`).join('')
                                                                    : `<option value="${item.imei_no}">${item.imei_no}</option>`
                                                                }
                                                            </select>
                                                        </div>
                                                        ` : ''}
                                            </li>
                                     <li class="price cart-col-amt">

    <div class="price-row sub-total">
        <span>Sub</span>
        <span>${formatCurrency(basePriceWithoutGst.toFixed(2), '{{ $currency_symbol }}', '{{ $currency_position }}')}</span>
    </div>

    ${(globalGstOption === "with" && hasProductGST && productGstTotal > 0) ? `
                                                <div class="price-row gst-inc">
                                                    <span>+ GST</span>
                                                    <span>${formatCurrency(productGstTotal.toFixed(2), '{{ $currency_symbol }}', '{{ $currency_position }}')}</span>
                                                </div>` : ''}

    <div class="price-row final-total" title="${discountAmount > 0 ? ('Discount: -' + formatCurrency(discountAmount.toFixed(2), '{{ $currency_symbol }}', '{{ $currency_position }}')) : ''}">
        <span>Amt</span>
        <span>${formatCurrency(finalProductTotal.toFixed(2), '{{ $currency_symbol }}', '{{ $currency_position }}')}</span>
    </div>

</li>

                                        <li class="delete-col">
                                            <a href="javascript:void(0);" class="remove-item" data-id="${item.id}" title="Remove item">
                                                <img src="{{ env('ImagePath') }}/admin/assets/img/icons/delete-2.svg" alt="delete">
                                                <i class="fas fa-trash-alt pos-del-icon" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                    </ul>
                                `;
                    });
                    productHtml += window.getPosBillSearchRowHtml();
                    // Close any floating search dropdown left on body before cart HTML is replaced
                    $('body > .pos-bill-search-results').each(function() {
                        const $box = $(this);
                        window.resetPosSearchDropdownPosition($box);
                        $box.hide().empty().remove();
                    });
                    $productTable.html(productHtml);
                    if (typeof window.initRentalDatePickers === 'function') {
                        window.initRentalDatePickers();
                    }
                    if (typeof window.fitPosProductGrid === 'function') {
                        window.fitPosProductGrid();
                    }
                    if (window.posBillSearchFocusNext) {
                        window.posBillSearchFocusNext = false;
                        const $billSearchInput = $productTable.find('.pos-bill-search-input').last();
                        if ($billSearchInput.length) {
                            $billSearchInput.focus();
                        }
                    }
                    if (window.lastScannedCartId) {
                        scrollToLatestCartItem(window.lastScannedCartId);
                    }
                } catch (e) {
                    console.error("Error in renderSelectedItems:", e);
                }
            }

            // Enforce max 6 decimal places for price and discount inputs
            $(document).on("input", ".product-price-field, .product-discount-percentage, .product-discount-amount",
                function() {
                    let val = $(this).val();

                    // Allow only numbers and one decimal point
                    val = val.replace(/[^0-9.]/g, '');

                    let parts = val.split('.');
                    // Prevent multiple decimal points
                    if (parts.length > 2) {
                        parts = [parts[0], parts.slice(1).join('')];
                    }

                    // Limit to max 6 decimal places
                    if (parts.length === 2 && parts[1].length > 6) {
                        parts[1] = parts[1].substring(0, 6);
                    }

                    let newVal = parts.join('.');
                    if ($(this).val() !== newVal) {
                        $(this).val(newVal);
                    }
                });

            // Product Discount Percentage
            $(document).on("change", ".product-discount-percentage", function() {

                // let id = $(this).data("id");
                let id = $(this).attr('data-id');
                let percent = parseFloat($(this).val()) || 0;

                if (percent < 0) percent = 0;
                if (percent > 100) percent = 100;

                let item = selectedItems.get(id);

                let baseTotal = getItemBaseAmount(item); // ✅ GST aware

                let discountAmount = (baseTotal * percent) / 100;

                item.discount_percentage = percent;
                item.discount_amount = discountAmount;

                selectedItems.set(id, item);

                $(`.product-discount-amount[data-id="${id}"]`)
                    .val(discountAmount.toFixed(2));

                updateTotalItems();
            });

            // Real-time purchase price validation while typing
            $(document).on("input", ".product-price-field", function() {
                let amount = parseFloat($(this).val());
                let id = $(this).attr('data-id');
                const $warning = $(this).closest('.price-input-container').find('.below-purchase-warning');

                if (!selectedItems.has(id)) return;
                let item = selectedItems.get(id);
                const purchasePrice = parseFloat(item.purchase_price) || parseFloat($(this).attr('data-purchase-price')) || 0;

                const isRentalMode = $(".rentalToggle").is(":checked");
                if (!isRentalMode && purchasePrice > 0 && !isNaN(amount) && amount < purchasePrice) {
                    $warning.html('Below last purchase \u20b9' + purchasePrice.toFixed(2)).css('display','block');
                } else {
                    $warning.css('display','none');
                }
            });

            $(document).on("change", ".product-price-field", function() {

                let id = $(this).attr('data-id');
                let amount = parseFloat($(this).val());

                if (!selectedItems.has(id)) return;

                let item = selectedItems.get(id);

                if (isNaN(amount) || amount < 0) {
                    amount = 0;
                }

                item.price = formatCurrency(amount.toFixed(2), '{{ $currency_symbol }}',
                    '{{ $currency_position }}');

                let baseTotal = getItemBaseAmount(item);

                if (item.discount_percentage > 0) {
                    item.discount_amount = (baseTotal * item.discount_percentage) / 100;
                } else if (item.discount_amount > 0) {
                    item.discount_amount = Math.min(item.discount_amount, baseTotal);
                    item.discount_percentage = baseTotal > 0 ? (item.discount_amount / baseTotal) * 100 : 0;
                }

                selectedItems.set(id, item);

                $(this).val(amount.toFixed(2));

                // Show/hide below-purchase-price warning
                const purchasePrice = parseFloat(item.purchase_price) || parseFloat($(this).attr('data-purchase-price')) || 0;
                const $warning = $(this).closest('.price-input-container').find('.below-purchase-warning');
                const isRentalMode = $(".rentalToggle").is(":checked");
                if (!isRentalMode && purchasePrice > 0 && amount < purchasePrice) {
                    $warning.html('Below last purchase \u20b9' + purchasePrice.toFixed(2)).css('display','block');
                } else {
                    $warning.css('display','none');
                }

                updateTotalItems();
            });

            // Product Discount Amount
            $(document).on("change", ".product-discount-amount", function() {

                // let id = $(this).data("id");
                let id = $(this).attr('data-id');
                let amount = parseFloat($(this).val()) || 0;

                if (amount < 0) amount = 0;

                let item = selectedItems.get(id);

                let baseTotal = getItemBaseAmount(item); // ✅ GST aware

                if (amount > baseTotal) amount = baseTotal;

                let percent = (amount / baseTotal) * 100;

                item.discount_percentage = percent;
                item.discount_amount = amount;

                selectedItems.set(id, item);

                $(`.product-discount-percentage[data-id="${id}"]`)
                    .val(percent.toFixed(2));

                updateTotalItems();
            });

            // Add this helper function if not already defined
            function formatCurrency(amount, symbol, position) {
                // Ensure the amount is a float and format it with commas
                let formattedAmount = parseFloat(amount).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                if (position === 'right') {
                    return formattedAmount + symbol;
                } else {
                    return symbol + formattedAmount;
                }
            }
            window.executeAddProduct = function(data, imei = null, available_serials = [], initialQty = 1) {
                var productIdNum = data.productIdNum;

                let initialPrice = data.productPrice;
                const regularPrice = data.regularPrice != null && data.regularPrice !== ''
                    ? parseFloat(data.regularPrice)
                    : parseFloat(data.productPrice);
                const sellingPrices = Array.isArray(data.sellingPrices) ? data.sellingPrices : [];
                const wholesalePrice = data.wholesalePrice != null && data.wholesalePrice !== ''
                    ? parseFloat(data.wholesalePrice)
                    : window.resolveWholesalePriceFromRates(sellingPrices, regularPrice);

                if ($(".rentalToggle").is(":checked") && Number(data.isRentAvailable) === 1 && data.rentPrice) {
                    initialPrice = data.rentPrice;
                } else if (!data._priceResolved && isWholesaleModeEnabled() && wholesalePrice != null && !isNaN(wholesalePrice)) {
                    initialPrice = wholesalePrice;
                }

                // Same product + different rate = separate cart lines; same rate = qty++
                var productIdStr = String(data.productIdStr || productIdNum || '');
                productIdStr = productIdStr.replace(/_r-?\d+(?:\.\d+)?$/, '');
                const priceNum = parseFloat(initialPrice);
                if (Number.isFinite(priceNum)) {
                    productIdStr = productIdStr + '_r' + priceNum.toFixed(2);
                }
                data.productIdStr = productIdStr;
                window.lastScannedCartId = productIdStr;

                if (selectedItems.has(productIdStr)) {
                    let item = selectedItems.get(productIdStr);
                    const nextQuantity = parseQuantityValue(item.quantity) + initialQty;
                        item.quantity = nextQuantity;
                    item.price = priceNum;
                    item.addedAt = Date.now();
                        if (imei) item.imei_no = imei;
                        if (available_serials && available_serials.length > 0) item.available_serials =
                            available_serials;

                        let baseAmount = getItemBaseAmount(item);
                        if (item.discount_percentage > 0) {
                            item.discount_amount = (baseAmount * item.discount_percentage) / 100;
                        }

                        selectedItems.set(productIdStr, item);
                        updateTotalItems();
                    showPosAddedNotice(item);
                    return;
                }

                const newItem = {
                    id: productIdStr,
                    productId: productIdNum,
                    categoryId: data.categoryId,
                    name: data.productName,
                    price: initialPrice,
                    purchase_price: parseFloat(data.purchasePrice) || 0,
                    image: data.productImage,
                    quantity: initialQty,
                    variant_id: data.variant_id || null,
                    stock: data.productStock,
                    isWarrantyCategory: data.isWarrantyCategory || false,
                    gst_option: data.gstOption,
                    product_gst: data.productGst,
                    discount_percentage: 0,
                    discount_amount: 0,
                    imei_no: imei || null,
                    available_serials: available_serials,
                    is_rent_available: data.isRentAvailable,
                    rent_period: data.rentPeriod,
                    rent_price: data.rentPrice,
                    regular_price: regularPrice,
                    wholesale_price: wholesalePrice,
                    selling_prices: sellingPrices,
                    rent_value: 1,
                    addedAt: Date.now()
                };

                selectedItems.set(productIdStr, newItem);

                if (data.$checkIcon && data.$checkIcon.length) {
                    data.$checkIcon.addClass("selected");
                }
                updateTotalItems();
                showPosAddedNotice(newItem);
            }

            $(document).on("click", "#confirmImeiBtn", function() {
                var imei = $('#imeiNumberInput').val() || '';
                imei = imei.trim();


                var available_serials = [];
                $('#imeiNumberInput option').each(function() {
                    if ($(this).val()) {
                        available_serials.push($(this).val());
                    }
                });

                if (window.pendingImeiProduct) {
                    var imeiQty = window.pendingImeiQty || 1;
                    executeAddProduct(window.pendingImeiProduct, imei, available_serials, imeiQty);
                    window.pendingImeiProduct = null;
                    window.pendingImeiQty = 1;
                }
                $('#imeiModal').modal('hide');
            });

            $(document).on("click", ".productset", function(e) {
                var $product = $(this);
                if ($(e.target).closest('.product-edit-icon').length) {
                    return;
                }
                var productIdNum = $product.data("id");
                var productIdStr = String(productIdNum);
                var productName = $product.find("h4").text().trim();
                var productPrice = $product.data("price-value");
                if (productPrice === undefined || productPrice === null || productPrice === '') {
                    productPrice = $product.find(".productsetcontent h6").text().trim();
                }
                var sellingPrices = [];
                try {
                    sellingPrices = JSON.parse(decodeURIComponent($product.attr('data-selling-prices') || '%5B%5D'));
                    if (!Array.isArray(sellingPrices)) sellingPrices = [];
                } catch (e) {
                    sellingPrices = [];
                }
                var categoryId = $product.find(".productsetcontent h1").text().trim();
                var productImage = $product.find("img").attr("src");
                var $checkIcon = $product.find(".check-product i");
                var productStock = parseQuantityValue($product.attr("data-stock") ?? $product.data("stock"));
                var gstOption = $product.data("gst-option") || "without_gst";
                var purchasePrice = parseFloat($product.data("purchase-price")) || 0;

                var isWarrantyCategory = isWarrantyCategoryProduct({
                    is_warranty_category: $product.data("is-warranty-category"),
                    category_name: $product.data("category-name")
                });

                // Get product GST data and parse it
                var productGst = $product.data("product-gst");
                if (productGst && productGst !== "null") {
                    try {
                        productGst = JSON.parse(productGst);
                    } catch (e) {
                        productGst = productGst;
                    }
                } else {
                    productGst = null;
                }

                var wholesalePrice = window.resolveWholesalePriceFromRates(sellingPrices, productPrice);
                var productData = {
                    productIdStr: productIdStr,
                    productIdNum: productIdNum,
                    categoryId: categoryId,
                    productName: productName,
                    productPrice: productPrice,
                    regularPrice: productPrice,
                    wholesalePrice: wholesalePrice,
                    purchasePrice: purchasePrice,
                    productImage: productImage,
                    productStock: productStock,
                    isWarrantyCategory: isWarrantyCategory,
                    gstOption: gstOption,
                    productGst: productGst,
                    $checkIcon: $checkIcon,
                    isRentAvailable: Number($product.data("rent") || 0),
                    rentPeriod: $product.data("rent-period") || null,
                    rentPrice: $product.data("rent-price") || null,
                    sellingPrices: sellingPrices
                };

                function proceedWithProduct(finalPrice, qty) {
                    productData.productPrice = finalPrice;
                    productData._priceResolved = true;
                    productData._pendingQty = (qty != null && qty > 0) ? qty : 1;
                $.ajax({
                    url: '/api/products/' + productIdNum + '/variants',
                    type: 'GET',
                    headers: {
                        "Authorization": "Bearer " + authToken,
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status && response.data && response.data.length > 0) {
                            openVariantsModal(productData, response.data);
                        } else {
                                continueProductClick(productData, productData._pendingQty || 1);
                        }
                    },
                    error: function() {
                            continueProductClick(productData, productData._pendingQty || 1);
                        }
                    });
                }

                function proceedWithRateSelections(selections) {
                    if (!selections || !selections.length) {
                        return;
                    }
                    // Variant products: use first selected rate (variant modal has its own qty)
                    $.ajax({
                        url: '/api/products/' + productIdNum + '/variants',
                        type: 'GET',
                        headers: {
                            "Authorization": "Bearer " + authToken,
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.status && response.data && response.data.length > 0) {
                                productData.productPrice = selections[0].price;
                                productData._priceResolved = true;
                                openVariantsModal(productData, response.data);
                                return;
                            }
                            window.addPosProductRateSelections(productData, selections);
                        },
                        error: function() {
                            window.addPosProductRateSelections(productData, selections);
                        }
                    });
                }

                if (isWholesaleModeEnabled()) {
                    const wholesaleProduct = {
                        id: productIdNum,
                        price: productPrice,
                        quantity: productStock,
                        wholesale_price: wholesalePrice,
                        selling_prices: sellingPrices,
                        wholesale_rates: window.getWholesaleRatesFromSellingRates(sellingPrices, productPrice)
                    };
                    pickWholesalePrice(wholesaleProduct, productPrice).then(function(selections) {
                        if (!selections || !selections.length) {
                            return;
                        }
                        proceedWithRateSelections(selections);
                    });
                    return;
                }

                const retailPrices = typeof window.withPosRateAvailableQty === 'function'
                    ? window.withPosRateAvailableQty(
                        typeof window.getRetailSellingRatesForPicker === 'function'
                            ? window.getRetailSellingRatesForPicker(sellingPrices)
                            : sellingPrices,
                        productStock,
                        productPrice
                    )
                    : sellingPrices;
                if (retailPrices.length > 1) {
                    window.showPosSellingRatePicker(retailPrices, 'Select Rate', {
                        productId: productIdNum
                    }).then(function(selections) {
                        if (!selections || !selections.length) return;
                        proceedWithRateSelections(selections);
                    });
                    return;
                }

                proceedWithProduct(retailPrices.length === 1 ? parseFloat(retailPrices[0].price) : productPrice, 1);
            });

            // Variant logic
            window.pendingVariantProduct = null;
            window.currentVariants = [];

            function openVariantsModal(productData, variants) {
                window.pendingVariantProduct = productData;
                window.currentVariants = variants;
                $('#variantProductName').text(productData.productName);

                // Populate unique sizes
                const sizes = [...new Set(variants.map(v => v.size).filter(s => s))];
                let sizeOptions = '<option value="">Select Size</option>';
                sizes.forEach(s => {
                    sizeOptions += `<option value="${s}">${s}</option>`;
                });
                $('#variantSize').html(sizeOptions);
                $('#variantColor').html('<option value="">Select Color</option>');
                $('#variantColorContainer').hide();
                $('#variantPriceDisplay').text('₹0.00');
                $('#variantStockDisplay').text('0');
                $('#variantQty').val(1).attr('max', 1);

                $('#variantModal').modal('show');
            }

            $('#variantSize').on('change', function() {
                const size = $(this).val();
                if (!size) {
                    $('#variantColorContainer').hide();
                    $('#variantPriceDisplay').text('₹0.00');
                    $('#variantStockDisplay').text('0');
                    return;
                }

                // Get variants for this size
                const sizeVariants = window.currentVariants.filter(v => v.size === size);
                const colors = [...new Set(sizeVariants.map(v => v.color).filter(c => c))];

                if (colors.length > 0) {
                    $('#variantColorContainer').show();
                    let colorOptions = '<option value="">Select Color</option>';
                    colors.forEach(c => {
                        colorOptions += `<option value="${c}">${c}</option>`;
                    });
                    $('#variantColor').html(colorOptions);
                    $('#variantPriceDisplay').text('₹0.00');
                    $('#variantStockDisplay').text('0');
                } else {
                    $('#variantColorContainer').hide();
                    // Just size, no color needed
                    const v = sizeVariants[0];
                    updateVariantDisplay(v);
                }
            });

            $('#variantColor').on('change', function() {
                const size = $('#variantSize').val();
                const color = $(this).val();
                if (!size || !color) return;

                const v = window.currentVariants.find(v => v.size === size && v.color === color);
                if (v) {
                    updateVariantDisplay(v);
                }
            });

            function updateVariantDisplay(variant) {
                const currencySymbol = '{{ $currency_symbol }}';
                const price = parseFloat(variant.price) > 0 ? parseFloat(variant.price) : window
                    .pendingVariantProduct.productPrice;
                $('#variantPriceDisplay').text(currencySymbol + parseFloat(price).toFixed(2));
                $('#variantStockDisplay').text(variant.qty);
                $('#variantQty').attr('max', variant.qty).val(1);
            }

            $('#confirmVariantBtn').on('click', function() {
                const size = $('#variantSize').val();
                if (!size) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Wait',
                        text: 'Please select a size.'
                    });
                    return;
                }
                const colorContainerVisible = $('#variantColorContainer').is(':visible');
                const color = $('#variantColor').val();
                if (colorContainerVisible && !color) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Wait',
                        text: 'Please select a color.'
                    });
                    return;
                }

                const v = window.currentVariants.find(v => v.size === size && (colorContainerVisible ? v
                    .color === color : true));
                if (!v) return;

                const qty = parseInt($('#variantQty').val());
                if (!qty || qty <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Quantity',
                        text: 'Please enter a valid quantity.'
                    });
                    return;
                }

                // Modify productData for cart
                let pData = {
                    ...window.pendingVariantProduct
                };
                let vDetails = ` - ${size}`;
                if (color) vDetails += ` / ${color}`;

                pData.productName += vDetails;
                pData.productPrice = parseFloat(v.price) > 0 ? v.price : pData.productPrice;
                pData.productStock = v.qty;
                pData.variant_id = v.id;
                // Add unique string ID to avoid merging with different variants
                pData.productIdStr = pData.productIdStr + "_v" + v.id;

                $('#variantModal').modal('hide');

                // Proceed to add to cart
                executeAddProduct(pData, null, [], qty);
            });

            function continueProductClick(productData, initialQty) {
                var categoryId = productData.categoryId;
                var productName = productData.productName;
                var productIdNum = productData.productIdNum;
                var qtyToAdd = (initialQty != null && initialQty > 0) ? initialQty : 1;

                // Check if it's mobile category
                if (String(categoryId).toLowerCase().includes('mobile') || String(productName).toLowerCase()
                    .includes('mobile')) {
                    $('#imeiProductName').text(productName);
                    if ($('#imeiNumberInput').hasClass("select2-hidden-accessible")) {
                        $('#imeiNumberInput').select2('destroy');
                    }
                    $('#imeiNumberInput').html('<option value="">Loading serials...</option>');
                    if ($.fn.select2) {
                        $('#imeiNumberInput').select2({
                            dropdownParent: $('#imeiModal'),
                            width: '100%'
                        });
                    }
                    window.pendingImeiProduct = productData;
                    window.pendingImeiQty = qtyToAdd;
                    $('#imeiModal').modal('show');

                    // Fetch available serial numbers
                    $.ajax({
                        url: '/api/get-available-serials/' + productIdNum,
                        type: 'GET',
                        headers: {
                            "Authorization": "Bearer " + authToken,
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            let selectHtml = '<option value="">Select serial number</option>';
                            if (response.status && response.serials.length > 0) {
                                response.serials.forEach(serial => {
                                    selectHtml +=
                                        `<option value="${serial}">${serial}</option>`;
                                });
                            } else {
                                selectHtml = '<option value="">No serial numbers available</option>';
                            }
                            if ($('#imeiNumberInput').hasClass("select2-hidden-accessible")) {
                                $('#imeiNumberInput').select2('destroy');
                            }
                            $('#imeiNumberInput').html(selectHtml);
                            if ($.fn.select2) {
                                $('#imeiNumberInput').select2({
                                    dropdownParent: $('#imeiModal'),
                                    width: '100%'
                                });
                            }
                        },
                        error: function() {
                            if ($('#imeiNumberInput').hasClass("select2-hidden-accessible")) {
                                $('#imeiNumberInput').select2('destroy');
                            }
                            $('#imeiNumberInput').html(
                                '<option value="">Failed to load serials</option>');
                            if ($.fn.select2) {
                                $('#imeiNumberInput').select2({
                                    dropdownParent: $('#imeiModal'),
                                    width: '100%'
                                });
                            }
                        }
                    });

                    return;
                }

                executeAddProduct(productData, null, [], qtyToAdd);
            }

            function getItemBaseAmount(item) {

                let price = parseItemPriceValue(item.price);
                let productTotal = price * item.quantity;

                const globalGstOption = $("input[name='gst_option']:checked").val();

                if (
                    globalGstOption === "with" &&
                    item.gst_option === "with_gst" &&
                    item.product_gst
                ) {
                    try {
                        const gstData = Array.isArray(item.product_gst) ?
                            item.product_gst :
                            JSON.parse(item.product_gst);

                        let totalGstRate = 0;
                        gstData.forEach(tax => {
                            totalGstRate += (parseFloat(tax.tax_rate) / 100);
                        });

                        let productGstTotal = productTotal * totalGstRate;

                        return productTotal + productGstTotal; // ✅ GST Included Amount

                    } catch (e) {
                        return productTotal;
                    }
                }

                return productTotal; // Without GST
            }

            $(document).on("click", ".button-plus", function() {

                // var itemId = $(this).data("id");
                var itemId = $(this).attr('data-id');

                if (!selectedItems.has(itemId)) return;

                let item = selectedItems.get(itemId);

                const nextQuantity = parseQuantityValue(item.quantity) + 1;
                item.quantity = nextQuantity;

                // Increase quantity
                item.quantity = nextQuantity;

                // 🔥 Recalculate discount properly
                let baseAmount = getItemBaseAmount(item);

                if (item.discount_percentage > 0) {
                    item.discount_amount = (baseAmount * item.discount_percentage) / 100;
                } else if (item.discount_amount > 0) {
                    item.discount_percentage = (item.discount_amount / baseAmount) * 100;
                }

                selectedItems.set(itemId, item);

                updateTotalItems();
            });

            $(document).on("change", ".cart-imei-select", function() {
                var itemId = $(this).attr('data-id');
                var newImei = $(this).val();

                if (!selectedItems.has(itemId)) return;

                let item = selectedItems.get(itemId);
                item.imei_no = newImei;
                selectedItems.set(itemId, item);
                // We don't necessarily need to call updateTotalItems() and re-render everything
                // just for an IMEI change, but it keeps state consistent if we do,
                // or we can just leave it as is since the state is updated in the map.
            });

            $(document).on("change", ".quantity-field", function() {

                // var itemId = $(this).data("id");
                var itemId = $(this).attr('data-id');
                var newQty = parseQuantityValue($(this).val());

                if (!selectedItems.has(itemId)) return;

                let item = selectedItems.get(itemId);

                if (newQty < 0.01) {
                    newQty = 1;
                }

                item.quantity = newQty;

                // 🔥 Recalculate discount properly
                let baseAmount = getItemBaseAmount(item);

                if (item.discount_percentage > 0) {
                    item.discount_amount = (baseAmount * item.discount_percentage) / 100;
                } else if (item.discount_amount > 0) {
                    item.discount_percentage = (item.discount_amount / baseAmount) * 100;
                }

                selectedItems.set(itemId, item);

                $(this).val(formatQuantityValue(newQty));

                updateTotalItems();
            });

            $(document).on("click", ".button-minus", function() {

                // var itemId = $(this).data("id");
                var itemId = $(this).attr('data-id');

                if (!selectedItems.has(itemId)) return;

                let item = selectedItems.get(itemId);

                if (parseQuantityValue(item.quantity) <= 1) return;

                // Decrease quantity
                item.quantity = Math.max(1, parseQuantityValue(item.quantity) - 1);

                // 🔥 Recalculate discount properly
                let baseAmount = getItemBaseAmount(item);

                if (item.discount_percentage > 0) {
                    item.discount_amount = (baseAmount * item.discount_percentage) / 100;
                } else if (item.discount_amount > 0) {
                    item.discount_percentage = (item.discount_amount / baseAmount) * 100;
                }

                selectedItems.set(itemId, item);

                updateTotalItems();
            });

            $(document).on("click", ".remove-item", function(e) {
                e.preventDefault();
                var itemId = $(this).attr('data-id'); // string key
                var item = selectedItems.get(itemId);
                if (item) {
                    $(".productset[data-id='" + item.productId + "'] .check-product i").removeClass(
                        "selected");
                    selectedItems.delete(itemId);
                    updateTotalItems();
                }
            });

            $(".clear_items").click(function() {
                selectedItems.clear();
                $(".check-product i").removeClass("selected");
                updateTotalItems();
            });

            $("#shipping").on("input", function() {
                calculateTotals();
            });
            $("#tds_percentage").on("input", function() {
                calculateTotals();
            });
            $("#deposit_amount").on("input", function() {
                calculateTotals();
            });
            $("#tds_percentage").on("blur", function() {
                if (!isTdsEnabled) {
                    return;
                }

                const rawValue = ($(this).val() || "").trim();
                if (rawValue === "") {
                    $("#tds_amount").val("0.00");
                    calculateTotals();
                    return;
                }

                const normalizedValue = Math.max(0, Math.min(100, parseFloat(rawValue) || 0));
                $(this).val(normalizedValue.toFixed(2));
                calculateTotals();
            });



            $(document).on("change", "input[name='gst_option']", function() {

                selectedItems.forEach(function(item, id) {

                    let baseAmount = getItemBaseAmount(item); // GST aware amount

                    if (item.discount_percentage > 0) {
                        item.discount_amount = (baseAmount * item.discount_percentage) / 100;
                    } else if (item.discount_amount > 0) {
                        item.discount_percentage = (item.discount_amount / baseAmount) * 100;
                    }

                    selectedItems.set(id, item);
                });

                updateTotalItems(); // 🔥 re-render + re-calc totals
            });
            $(document).on("change", "input[name='gst_option']", function() {
                let gstOption = $("input[name='gst_option']:checked").val();


                if (gstOption === "with") {
                    $(".taxList").show();
                } else {
                    $(".taxList").hide();
                }

                // Update product display and recalc totals
                renderSelectedItems();
                calculateTotals();
            });
            $(".btn-totallabel").click(async function(event) {
                // ================= LABOUR ITEMS =================
                let labourItems = [];

                $(".labour-row").each(function() {

                    let labourId = $(this).find(".labour-select").val();
                    let qty = parseFloat($(this).find(".labour-qty").val()) || 0;
                    let price = parseFloat($(this).find(".labour-price").val()) || 0;

                    if (labourId && qty > 0) {

                        labourItems.push({
                            labour_item_id: labourId,
                            qty: qty,
                            price: price,
                            total: qty * price
                        });
                    }
                });

                event.preventDefault();
                var $btn = $(this); // cache the button
                var originalText = $btn.html();
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

                // Reset errors
                $(".error_customer_name, .error_customer_phone, .error_peymentmethod, .error_discount, .error_bank, .error_paidtype, .error_paidamount")
                    .text("");
                $(".total-value .error_total").remove();

                // Get form values
                // let name = $("input[name='customer_name']").val().trim();
                let name = $("#customer_name option:selected").val();
                let phone = $("input[name='customer_phone']").val().trim();
                let orderDate = $("#order_date").val();
                let selectedPayment = $("input[name='payment_method']:checked").val();
                let bank_id = $("#bank_id").val();
                let subtotal = parseFloat($("input[name='subtotal']").val()) || 0;
                let total = parseFloat($("input[name='total']").val()) || 0;
                let discount_amount = parseFloat($("input[name='discount_amount']").val()) || 0;
                let remarks = $("#remarks").val().trim();
                let paymentRemarks = $("#posPaymentRemark").val().trim();
                let shipping = parseFloat($("#shipping").val()) || 0;
                let tds_percentage = parseFloat($("#tds_percentage").val()) || 0;
                let tds_amount = parseFloat($("#tds_amount").val()) || 0;
                let deposit_amount = parseFloat($("#deposit_amount").val()) || 0;
                let posPaymentMeta = (typeof window.getPosPaymentMeta === "function") ? window
                    .getPosPaymentMeta() : null;
                // Validation
                let isValid = true;

                // Customer is now optional; walk-in-customer will be used by default if empty

                // if (phone === "") {
                //     $(".error_customer_phone").text("Customer number is required").css("color", "red");
                //     isValid = false;
                // } else if (!/^\d{10}$/.test(phone)) {
                //     $(".error_customer_phone").text("Enter a valid 10-digit phone number").css("color", "red");
                //     isValid = false;
                // }

                // Determine if this is a quotation, advance receipt, or a sale
                let quotationStatus;
                if (isAdvanceReceiptMode) {
                    quotationStatus = 'advance_receipt';
                } else if ($('.quotationToggle:checked').length > 0) {
                    quotationStatus = 'quotation';
                } else if ($(".rentalToggle").is(":checked")) {
                    quotationStatus = 'rental';
                } else {
                    quotationStatus = 'sales';
                }



                // require a payment method for sales, advance_receipt, and rental
                let requiresPaymentMethod = false;
                if (quotationStatus === 'sales' || quotationStatus === 'advance_receipt' || quotationStatus === 'rental') {
                    requiresPaymentMethod = true;
                }

                const exchangeSurplusAmt = parseFloat($('#pos_exchange_surplus').val()) || 0;
                const exchangeSurplusAction = ($('#pos_exchange_surplus_action').val() || '').trim();
                if (quotationStatus === 'sales' && exchangeSurplusAmt > 0 && !exchangeSurplusAction) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Exchange surplus',
                        text: 'Exchange is higher than bill total. Choose Return Cash or Deposit to Customer.'
                    });
                    isValid = false;
                }
                if (quotationStatus === 'sales' && exchangeSurplusAmt > 0 && exchangeSurplusAction === 'deposit') {
                    const custId = $("#customer_name option:selected").val();
                    const custName = ($("#customer_name option:selected").text() || '').trim();
                    if (!custId || !/^\d+$/.test(String(custId)) || /default customer/i.test(custName)) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Select customer',
                            text: 'Select a customer to deposit the remaining exchange balance.'
                        });
                        isValid = false;
                    }
                }

                // When bill payable is 0 (full exchange / deposit cover), Pay Later is fine.
                const billPayableNow = parseFloat($("input[name='total']").val()) || 0;
                const depositAppliedNow = parseFloat($('#pos_customer_deposit_applied').val()) || 0;

                if (requiresPaymentMethod) {
                    if (!selectedPayment) {
                        $(".error_peymentmethod").text("Please select a payment method").css("color",
                            "red");
                        isValid = false;
                    } else if (quotationStatus === 'advance_receipt' && selectedPayment === 'pending') {
                        $(".error_peymentmethod").text("Advance Receipts cannot use 'Pay Later'").css(
                            "color",
                            "red");
                        isValid = false;
                    }
                }

                if (requiresPaymentMethod &&
                    selectedPayment && selectedPayment !== "pending" &&
                    !(billPayableNow <= 0 && (exchangeSurplusAmt > 0 || depositAppliedNow > 0))) {
                    if (selectedPayment !== "emi" && (!posPaymentMeta || !posPaymentMeta.paidType)) {
                        $(".error_paidtype").text("Please select paid type.");
                        isValid = false;
                    } else if (selectedPayment !== "emi" && posPaymentMeta.paidAmount <= 0) {
                        $(".error_paidamount").text("Enter a valid payment amount.");
                        isValid = false;
                    } else if (selectedPayment !== "emi" && posPaymentMeta.paidType === "partially" &&
                        posPaymentMeta.paidAmount >=
                        total) {
                        $(".error_paidamount").text(
                            "Partial amount must be less than total amount.");
                        isValid = false;
                    }

                    if (selectedPayment === "cash+online") {
                        if (posPaymentMeta.cashAmount < 0 || posPaymentMeta.onlineAmount < 0) {
                            $(".error_paidamount").text("Negative amount is not allowed.");
                            isValid = false;
                        }

                        if ((posPaymentMeta.cashAmount + posPaymentMeta.onlineAmount) > total) {
                            $(".error_paidamount").text(
                                "Cash + Online total cannot exceed total amount.");
                            isValid = false;
                        }
                    }
                }

                // when quotation, we intentionally skip the generic check below
                // so do not run the unconditional validation block


                if (quotationStatus === 'sales' &&
                    (selectedPayment === "debit card" || selectedPayment === "scan" ||
                        selectedPayment ===
                        "cash+online") &&
                    !bank_id) {

                    $(".error_bank").text("Please select a bank").css("color", "red");
                    isValid = false;
                }

                if (typeof window.validatePosEmiFields === "function" && !window
                .validatePosEmiFields()) {
                    isValid = false;
                }

                // if (subtotal <= 0) {
                //     $(".total-value").append("<span class='error_total' style='color:red; display:block;'>Please select at least one product</span>");
                //     isValid = false;
                // }
                if (subtotal <= 0) {
                    $(".error_total")
                        .text("Please select at least one product")
                        .show();
                    isValid = false;
                } else {
                    $(".error_total").hide().text("");
                }

                // Skip below-purchase-price check for rental orders (rent price is expected to be lower than purchase price)
                let isCurrentlyRental = $(".rentalToggle").is(":checked") || quotationStatus === 'rental';
                let hasBelowPurchasePrice = false;
                if (!isCurrentlyRental) {
                    selectedItems.forEach(function(item) {
                        let price = parseItemPriceValue(item.price);
                        if (item.purchase_price > 0 && price < item.purchase_price) {
                            hasBelowPurchasePrice = true;
                        }
                    });
                }

                if (hasBelowPurchasePrice) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Price',
                        text: 'One or more products are being sold below their purchase price. Please adjust the price.'
                    });
                    isValid = false;
                }

                // if (!isValid) return;
                if (!isValid) {
                    $btn.html(originalText).prop('disabled', false); // <-- important line
                    return;
                }

                // Ask confirmation before placing order/purchase/quotation
                const actionVerb = quotationStatus === 'advance_receipt' ?
                    'create this advance receipt' :
                    (quotationStatus === 'quotation' ? 'save this quotation' :
                        (quotationStatus === 'rental' ? 'create this rental order' :
                            'confirm and place this order'));

                const confirmResult = await Swal.fire({
                    title: 'Are you sure?',
                    text: `Do you want to ${actionVerb}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ff9f43',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, confirm!',
                    cancelButtonText: 'Cancel'
                });

                if (!confirmResult.isConfirmed) {
                    $btn.html(originalText).prop('disabled', false);
                    return;
                }

                // Show loading text and disable the button
                $btn.html(
                    '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Placing Order...'
                ).prop('disabled', true);


                // Prepare tax data
                let taxes = [];
                let gstOption = $("input[name='gst_option']:checked").val();

                // Prepare order items data
                // In the order submission section
                let orderItems = [];
                selectedItems.forEach(function(item) {
                    const price = parseItemPriceValue(item.price);
                    let isRentalItem = $(".rentalToggle").is(":checked") && Number(item
                        .is_rent_available) === 1;
                    let rentMultiplier = isRentalItem ? (item.rent_value || 1) : 1;
                    const itemTotal = price * rentMultiplier * item.quantity;

                    // Calculate product GST if applicable
                    let productGstDetails = [];
                    let productGstTotal = 0;

                    if (gstOption === "with" && item.gst_option === "with_gst" && item
                        .product_gst) {
                        try {

                            const gstData = Array.isArray(item.product_gst) ? item.product_gst :
                                JSON.parse(item.product_gst);
                            const totalGstRate = gstData.reduce((sum, tax) => sum + (parseFloat(
                                tax
                                .tax_rate || 0) / 100), 0);
                            const totalGstAmount = totalGstRate > 0 ? itemTotal * totalGstRate /
                                (
                                    1 + totalGstRate) : 0;

                            gstData.forEach(tax => {
                                const taxRate = parseFloat(tax.tax_rate) / 100;
                                const taxAmount = totalGstRate > 0 ? totalGstAmount * (
                                    taxRate / totalGstRate) : 0;
                                productGstTotal += taxAmount;

                                productGstDetails.push({
                                    tax_name: tax.tax_name,
                                    tax_rate: tax.tax_rate,
                                    tax_amount: taxAmount
                                });
                            });
                        } catch (e) {
                            // console.error("Error calculating product GST:", e);
                        }
                    }

                    orderItems.push({
                        product_id: item.productId,
                        variant_id: item.variant_id || null,
                        quantity: item.quantity,
                        categoryId: item.categoryId,
                        price: price,
                        discount_percentage: item.discount_percentage || 0,
                        discount_amount: item.discount_amount || 0,
                        total: itemTotal,
                        gst_option: item.gst_option,
                        product_gst_details: productGstDetails,
                        product_gst_total: productGstTotal,
                        final_price: itemTotal + productGstTotal,
                        imei_no: item.imei_no || null,
                        rent_value: item.rent_value || null,
                        rent_period: item.rent_period || null,
                        is_rental: isRentalItem ? 1 : 0,
                        rent_price: isRentalItem ? item.rent_price : null,
                        rent_start_at: item.rent_start_at || null,
                        rent_end_at: item.rent_end_at || null
                    });
                });

                // Prepare tax data
                // let taxes = [];
                // $(".taxList").each(function () {

                //     const taxName = $(this).find("h5").text().replace(" Tax", "");
                //     const taxAmount = parseFloat($(this).find("h6").text().replace(/[^0-9.]/g, ""));


                //     const taxRate = parseFloat($(this).find(".tax-value").data("rate"));
                //     taxes.push({
                //         tax_name: taxName,
                //         rate: taxRate,
                //         amount: taxAmount
                //     });
                // });
                if (gstOption === "with") {
                    $(".taxList").each(function() {
                        const taxName = $(this).find("h5").text().replace(" Tax", "");
                        const taxAmount = parseFloat($(this).find("h6").text().replace(
                            /[^0-9.]/g,
                            "")) || 0;
                        const taxRate = parseFloat($(this).find(".tax-value").data("rate")) ||
                        0;

                        taxes.push({
                            tax_name: taxName,
                            rate: taxRate,
                            amount: taxAmount
                        });
                    });
                } else {
                    taxes = []; // ✅ explicitly empty when "without GST"
                }

                // Prepare order data
                // In the order submission section, add gst_total to orderData
                
                let otherChargesName = [];
                let otherChargesAmount = [];
                $(".other-charge-row").each(function() {
                    const cName = $(this).find('input[name="other_charges_name[]"]').val();
                    const cAmount = parseFloat($(this).find('.other-charge-amount').val()) || 0;
                    if(cName && cAmount > 0) {
                        otherChargesName.push(cName);
                        otherChargesAmount.push(cAmount);
                    }
                });
                
                // build base payload
                let orderData = {
                    convert_order_id: new URLSearchParams(window.location.search).get(
                        'convert_order_id') || new URLSearchParams(window.location.search).get(
                        'edit_order_id') || null,
                    edit_order_id: new URLSearchParams(window.location.search).get('edit_order_id') || null,
                    is_pos_edit: @json($isPosEdit ?? false),
                    selectedSubAdminId: selectedSubAdminId,
                    customer_id: name,
                    customer_phone: phone,
                    customer_gst_number: ($('#pos_gst_number').val() || '').trim().toUpperCase(),
                    order_date: orderDate,
                    subtotal: subtotal,
                    discount: parseFloat($("#bill_discount_percent").val()) || parseFloat($("#additional_discount_percent").val()) || 0,
                    discount_amount: discount_amount,
                    tax: taxes,
                    gst_option: gstOption,
                    total: total,
                    items: orderItems,
                    remarks: remarks,
                    shipping: shipping,
                    tds_percentage: tds_percentage,
                    tds_amount: tds_amount,
                    other_charges_name: otherChargesName,
                    other_charges_amount: otherChargesAmount,
                    exchange_material_id: $('#pos_exchange_material_id').val() || null,
                    exchange_type_id: $('#pos_exchange_type_id').val() || null,
                    exchange_price: parseFloat($('#pos_exchange_price').val()) || 0,
                    exchange_weight: parseFloat($('#pos_exchange_weight').val()) || 0,
                    exchange_amount: parseFloat($('#pos_exchange_amount').val()) || 0,
                    exchange_surplus: parseFloat($('#pos_exchange_surplus').val()) || 0,
                    exchange_surplus_action: $('#pos_exchange_surplus_action').val() || null,
                    customer_deposit_applied: parseFloat($('#pos_customer_deposit_applied').val()) || 0,
                    exchange_details: (typeof window.getPosExchangeDetailsPayload === 'function')
                        ? window.getPosExchangeDetailsPayload()
                        : ($('#pos_exchange_material_id').val() ? {
                        material_id: $('#pos_exchange_material_id').val(),
                        material_name: '',
                        type_id: $('#pos_exchange_type_id').val() || null,
                        type_name: '',
                        price: parseFloat($('#pos_exchange_price').val()) || 0,
                        weight: parseFloat($('#pos_exchange_weight').val()) || 0,
                        amount: parseFloat($('#pos_exchange_amount').val()) || 0
                    } : null),
                    deposit_amount: deposit_amount,
                    rental_status: $(".rentalToggle").is(":checked") ? "rental" : "sales",
                    labour_items: labourItems,
                    quotation_status: quotationStatus, // ✅ IMPORTANT
                    assigned_staff_id: $('#assigned_staff_id').val() || null,
                    order_type: $('#order_type').val() || 'self_pickup',
                };

                // add payment fields only when this is a sale, advance_receipt, or rental
                if (quotationStatus === 'sales' || quotationStatus === 'advance_receipt' || quotationStatus === 'rental') {
                    orderData.payment_method = selectedPayment;
                    orderData.bank_id = bank_id;
                    if (selectedPayment === "pending") {
                        orderData.payment_amount = 0;
                        orderData.pending_amount = total;
                    } else {
                        orderData.payment_amount = posPaymentMeta ? posPaymentMeta.paidAmount : 0;
                        orderData.pending_amount = posPaymentMeta ? posPaymentMeta.pendingAmount :
                        total;
                    }
                    orderData.payment_remarks = paymentRemarks;
                    if (selectedPayment === "emi" && posPaymentMeta) {
                        orderData.emi_down_payment = posPaymentMeta.emiDownPayment || 0;
                        orderData.emi_loan_amount = posPaymentMeta.emiLoanAmount || 0;
                        orderData.emi_interest_rate = posPaymentMeta.emiInterestRate || 0;
                        orderData.emi_tenure = posPaymentMeta.emiTenure || "";
                        orderData.emi_custom_tenure = posPaymentMeta.emiCustomTenure || "";
                        orderData.emi_monthly_amount = posPaymentMeta.emiMonthlyAmount || 0;
                        orderData.emi_aadhar_number = posPaymentMeta.emiAadharNumber || "";
                        orderData.emi_do_id = posPaymentMeta.emiDoId || "";
                        orderData.emi_pan_number = posPaymentMeta.emiPanNumber || "";
                        orderData.emi_guarantor_name = posPaymentMeta.emiGuarantorName || "";
                        orderData.bank_id = posPaymentMeta.emiBankId || "";
                    }

                    if (selectedPayment === "cash") {
                        orderData.paid_type = (posPaymentMeta && posPaymentMeta.paidType ===
                                "partially") ?
                            "cash_partially" : "cash_fully";
                        orderData.amount = posPaymentMeta ? posPaymentMeta.paidAmount : 0;
                    } else if (selectedPayment === "debit card" || selectedPayment === "scan") {
                        orderData.online_type = (posPaymentMeta && posPaymentMeta.paidType ===
                                "partially") ?
                            "online_partially" : "online_fully";
                        orderData.amount = posPaymentMeta ? posPaymentMeta.paidAmount : 0;
                    } else if (selectedPayment === "cash+online") {
                        orderData.cash_online_type = (posPaymentMeta && posPaymentMeta.paidType ===
                            "partially") ? "cash_online_partially" : "cash_online_fully";
                        orderData.cash_amount = posPaymentMeta ? posPaymentMeta.cashAmount : 0;
                        orderData.online_amount = posPaymentMeta ? posPaymentMeta.onlineAmount : 0;
                    }
                }





                // Submit order via AJAX
                $.ajax({
                    url: "/api/order_sale",
                    type: "POST",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function(xhr) {
                        // Ensure CSRF token is included
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]')
                            .attr(
                                'content'));
                    },
                    data: JSON.stringify(orderData),
                    success: function(response) {
                        // Re-enable and reset the button
                        $btn.html(originalText).prop('disabled', false);
                        if (response.status) {
                            orderId = response.order_id;
                            // alert("Order placed successfully!");
                            // location.reload();
                            const quotationStatus = document.getElementById(
                                    'quotation_status')
                                ?.value || 'sales';
                            const successTitle = quotationStatus === 'advance_receipt' ?
                                'Advance Receipt Created!' :
                                (quotationStatus === 'quotation' ?
                                    'Quotation Saved!' :
                                    (quotationStatus === 'rental' ? 'Rental Created!' :
                                        'Order Placed!'));
                            Swal.fire({
                                title: successTitle,
                                text: response.message,
                                icon: "success",
                                timer: 1800,
                                showConfirmButton: false,
                                confirmButtonColor: "#ff9f43"
                            }).then(function() {
                                if (typeof window.openPosInvoicePrintModal === 'function') {
                                    // Auto-print when invoice loads (silent if POS opened via Direct Print launcher)
                                    window.openPosInvoicePrintModal(orderId, { autoPrint: true });
                                }
                            });
                            if (typeof window.posBillTabsCloseCurrentAfterSave === 'function') {
                                window.posBillTabsCloseCurrentAfterSave();
                            } else {
                            selectedItems.clear();
                            $(".check-product i").removeClass("selected");
                            $("input[name='customer_name'], input[name='customer_phone']")
                                    .val("");
                            setPosOrderDate("{{ now()->format('Y-m-d') }}");
                            if (typeof window.selectDefaultPaymentMethod === "function") {
                                window.selectDefaultPaymentMethod();
                            } else if (typeof window.selectPayLaterPayment === "function") {
                                window.selectPayLaterPayment();
                            } else {
                                $("input[name='payment_method']").prop("checked", false);
                                $(".paymentmethod").removeClass("active");
                                if (typeof window.resetPosPaymentUi === "function") {
                                    window.resetPosPaymentUi();
                                }
                            }
                            if (typeof window.resetPosRemarks === "function") {
                                window.resetPosRemarks();
                            }
                            if (typeof window.resetPosAdditionalDiscount === "function") {
                                window.resetPosAdditionalDiscount();
                            }
                            updateTotalItems();
                            }
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    // error: function(xhr, status, error) {
                    //     // Re-enable and reset the button
                    //     $btn.html(originalText).prop('disabled', false);

                    //     alert("Error placing order. Please try again.");
                    // }
                    error: function(xhr, status, error) {
                        $btn.html(originalText).prop('disabled', false);

                        // Try to extract error message from response
                        let response = xhr.responseJSON;

                        if (xhr.status === 422 && response && response.errors) {
                            let errors = response.errors;
                            $.each(errors, function(key, msgs) {
                                let errorKey = key;
                                // The payload sends 'customer_id' but the UI class is 'error_customer_name'
                                if (key === 'customer_id') {
                                    errorKey = 'customer_name';
                                }
                                $('.error_' + errorKey).text(msgs[0]).css("color",
                                    "red");
                            });
                        } else {
                            // Show all backend errors (including DB duplicate) as a SweetAlert
                            // Never show a "phone already registered" error for auto-filled phones
                            Swal.fire({
                                title: "Error",
                                text: response && response.message ?
                                    response.message :
                                    "Error placing order. Please try again.",
                                icon: "error",
                                confirmButtonColor: "#ff9f43"
                            });
                        }
                    }
                });
            });

            $(".owl-carousel").owlCarousel({
                items: 5,
                loop: false,
                nav: true,
                dots: false,
                margin: 10
            });

            // Handle category click
            $(".tabs li").click(function() {
                var categoryId = $(this).attr('id');


                // Remove active class from all tabs and tab contents
                $(".tabs li").removeClass('active');
                $(".tab_content").removeClass('active');

                // Add active class to clicked tab
                $(this).addClass('active');

                // Find the corresponding tab content
                var tabContentSelector = '.tab_content[data-tab="' + categoryId + '"]';
                var $tabContent = $(tabContentSelector);

                if ($tabContent.length) {
                    $tabContent.addClass('active');


                    // Check if content already loaded
                    if ($tabContent.find('.productset').length === 0) {
                        loadProductsByCategory(categoryId);
                    }
                } else {

                    // Create new tab content if not found
                    $('.tabs_container').append('<div class="tab_content active" data-tab="' + categoryId +
                        '"></div>');
                    loadProductsByCategory(categoryId);
                }
            });

            // Function to load products by category
            function loadProductsByCategory(categoryId) {

                let isRentalMode = $(".rentalToggle").is(":checked") ? "?rental=1" : "";

                $.ajax({
                    url: "/api/getProductsByCategory/" + categoryId + isRentalMode,
                    type: "GET",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // console.log('respone:', response)
                        const currencySymbol = response.currency_symbol || 'Ã¢â€šÂ¹';
                        const currencyPosition = response.currency_position || 'left';


                        if (response.status) {
                            renderProducts(response.data, categoryId, currencySymbol, currencyPosition);
                        } else {
                            renderProducts([], categoryId, currencySymbol, currencyPosition);
                        }
                    },
                    error: function(xhr, status, error) {

                        renderProducts([], categoryId);
                    }
                });
            }

            // Function to render products
            function renderProducts(products, categoryId, currencySymbol, currencyPosition) {

                var productsHtml = '';

                if (products.length === 0) {
                    productsHtml = '<div class="col-12"><p>No products found in this category</p></div>';
                } else {
                    products.forEach(function(product) {
                        // Default image
                        let productImage =
                            '{{ env('ImagePath') . '/admin/assets/img/product/noimage.png' }}';

                        // Handle different image formats
                        if (product.image) {
                            try {
                                // Clean the image string
                                let cleanImageString = product.image.replace(/\\/g, '');
                                cleanImageString = cleanImageString.replace(/^"(.*)"$/, '$1');

                                let imageBasePath =
                                    '{{ env('ImagePath') }}'; // or config('app.url') if you prefer

                                if (cleanImageString.startsWith('[')) {
                                    let imagesArray = JSON.parse(cleanImageString);
                                    if (Array.isArray(imagesArray) && imagesArray.length > 0) {
                                        productImage = `${imageBasePath}/storage/${imagesArray[0]}`;
                                    }
                                } else {
                                    productImage = `${imageBasePath}/storage/${cleanImageString}`;
                                }
                            } catch (e) {

                            }
                        }
                        // Parse GST data
                        let gstBadge = '';
                        let gstOption = product.gst_option || "without_gst";
                        let productGst = product.product_gst || null;

                        if (gstOption === "with_gst" && productGst) {
                            try {
                                const gstData = JSON.parse(productGst);
                                const totalRate = gstData.reduce((sum, tax) => sum + parseFloat(tax
                                    .tax_rate), 0);
                                gstBadge = `<span class="gst-hover-badge with-gst">
                        GST: ${totalRate}%
                    </span>`;
                            } catch (e) {
                                gstBadge = `<span class="gst-hover-badge with-gst">
                        With GST
                    </span>`;
                            }
                        } else {
                            gstBadge = `<span class="gst-hover-badge no-gst">
                    No GST
                </span>`;
                        }

                        let isRentalActive = $(".rentalToggle").is(":checked") && Number(product
                            .is_rent_available) === 1;
                        let gridDisplayPrice = isRentalActive ? (product.rent_price || 0) : (product
                            .price || 0);

                        // Format price
                        let displayPrice = formatCurrency(gridDisplayPrice, currencySymbol,
                            currencyPosition);

                        const sellingRates = (function(raw) {
                            if (!Array.isArray(raw)) {
                                return [];
                            }
                            const wholesaleOn = typeof isWholesaleModeEnabled === 'function' && isWholesaleModeEnabled();
                            return raw.filter(function(rate) {
                                if (!rate || rate.price === '' || rate.price === null || isNaN(parseFloat(rate.price))) {
                                    return false;
                                }
                                const isWh = /wholesale/i.test(String(rate.name || '').trim());
                                // Cards: wholesale rows only while Wholesale mode is on; otherwise retail only
                                return wholesaleOn ? isWh : !isWh;
                            });
                        })(product.selling_prices || product.selling_price_options || []);
                        let ratesHtml = '';
                        if (sellingRates.length) {
                            ratesHtml = '<div class="pos-card-rates">' + sellingRates.map(function(rate, idx) {
                                const rateName = (rate.name || ('Rate ' + (idx + 1))).replace(/</g, '&lt;');
                                const ratePrice = formatCurrency(rate.price, currencySymbol, currencyPosition);
                                const hasMrp = rate.mrp !== undefined && rate.mrp !== null && rate.mrp !== '' &&
                                    Math.abs(parseFloat(rate.mrp) - parseFloat(rate.price)) > 0.001;
                                const mrpBit = hasMrp
                                    ? '<span class="pos-card-mrp">' + formatCurrency(rate.mrp, currencySymbol, currencyPosition) + '</span> '
                                    : '';
                                const extraClass = idx > 0 ? ' pos-card-extra' : '';
                                return '<div class="pos-card-rate' + extraClass + '">' + rateName + ': ' + mrpBit + '<b>' + ratePrice + '</b></div>';
                            }).join('') + (sellingRates.length > 1
                                ? '<div class="pos-card-more">' + sellingRates.length + ' rates</div>'
                                : '') + '</div>';
                        } else if (!isWholesaleModeEnabled() && product.mrp && Math.abs(parseFloat(product.mrp) - parseFloat(gridDisplayPrice)) > 0.001) {
                            ratesHtml = '<div class="pos-card-rates"><div class="pos-card-rate"><span class="pos-card-mrp">' +
                                formatCurrency(product.mrp, currencySymbol, currencyPosition) + '</span></div></div>';
                        } else if (isWholesaleModeEnabled()) {
                            const whRates = typeof getProductWholesaleRates === 'function'
                                ? getProductWholesaleRates(product, gridDisplayPrice)
                                : [];
                            if (whRates.length) {
                                ratesHtml = '<div class="pos-card-rates">' + whRates.map(function(rate, idx) {
                                    const rateName = (rate.name || 'Wholesale').replace(/</g, '&lt;');
                                    const ratePrice = formatCurrency(rate.price, currencySymbol, currencyPosition);
                                    return '<div class="pos-card-rate">' + rateName + ': <b>' + ratePrice + '</b></div>';
                                }).join('') + '</div>';
                            }
                        }

                        const barcodes = String(product.barcode || '').split(',').map(function(code) {
                            return code.trim();
                        }).filter(Boolean);
                        let barcodeHtml = '';
                        if (barcodes.length) {
                            barcodeHtml = '<div class="pos-card-barcodes">' + barcodes.map(function(code, idx) {
                                const extraClass = idx > 0 ? ' pos-card-extra' : '';
                                return '<div class="pos-card-barcode' + extraClass + '" title="' + code.replace(/"/g, '&quot;') + '">' +
                                    code.replace(/</g, '&lt;') + '</div>';
                            }).join('') + (barcodes.length > 1
                                ? '<div class="pos-card-more">' + barcodes.length + ' barcodes</div>'
                                : '') + '</div>';
                        }

                        // Check if product is out of stock
                        let isWarrantyCategory = isWarrantyCategoryProduct(product);
                        let outOfStockBadge = '';
                        let disabledClass = '';
                        let addButtonDisabled = '';

                        if (parseQuantityValue(product.quantity) <= 0 && !isWarrantyCategory) {
                            outOfStockBadge = `<div class="pos-oos-badge">Out of Stock</div>`;
                        }

                        // ✅ ADD data-gst-option and data-product-gst ATTRIBUTES HERE
                        productsHtml += `
                <div class="col-lg-3 col-sm-6 d-flex position-relative">
                    <div class="productset flex-fill text-center ${disabledClass}"
                         data-id="${product.id}"
                         data-stock="${product.quantity}"
                         data-is-warranty-category="${isWarrantyCategory ? 1 : 0}"
                         data-category-name="${product.category_name || ''}"
                         data-gst-option="${gstOption}"
                         data-product-gst='${JSON.stringify(productGst).replace(/'/g, "&#39;")}'
                         data-rent="${product.is_rent_available || 0}"
                         data-rent-period="${product.rent_period || ''}"
                         data-rent-price="${product.rent_price || 0}"
                         data-purchase-price="${product.purchase_price || 0}"
                         data-price-value="${isRentalActive ? (product.rent_price || 0) : (product.price || 0)}"
                         data-selling-prices="${encodeURIComponent(JSON.stringify(product.selling_prices || []))}"
                         style="position: relative;">
                        <a href="javascript:void(0)"
                           class="product-edit-icon"
                           title="Edit Product"
                           onclick="event.stopPropagation(); fetchAndShowEditModal(${product.id}, null);">
                            <i class="fas fa-edit"></i>
                        </a>
                        ${outOfStockBadge}
                        <div class="productsetimg">
                            <img src="${productImage}" alt="${product.name}" class="productsetimgin">
                            <h6>Qty: ${product.quantity || '0'}</h6>
                            ${gstBadge}
                        </div>
                        <div class="productsetcontent">
                            <h4>
                                ${product.name}
                            </h4>
                             <h5 class="text-dark">${product.unit || ''}</h5>
                            <h1 class="d-none">${product.categoryId || 'Uncategorized'}</h1>

                            <h6>${displayPrice}</h6>
                            ${ratesHtml}
                            ${barcodeHtml}
                        </div>
                        <div class="productsetbtn" style="display: flex; gap: 5px; align-items: center; justify-content: flex-end;">
                            <span class="card-qty-label d-md-none" style="font-size: 11px; color: #28c76f; font-weight: 700; display: none; text-align: right; line-height: 1.2;" data-product-id="${product.id}"></span>
                            <button class="btn btn-added" ${addButtonDisabled}>+ Add</button>
                        </div>
                    </div>
                </div>
            `;
                    });
                }

                // Update the tab content
                var $tabContent = $('.tab_content[data-tab="' + categoryId + '"]');
                if ($tabContent.length) {
                    $tabContent.html('<div class="row">' + productsHtml + '</div>');
                    window.refreshProductAvailabilityUI();
                    if (typeof window.fitPosProductGrid === 'function') {
                        window.fitPosProductGrid();
                    }

                } else {

                }
            }



            // Load products for the first category by default (category catalog hidden — search/barcode only)
            if ($('.pos-category-catalog').length && $('.pos-category-catalog').is(':visible')) {
            var firstCategory = $(".tabs li:first");
            if (firstCategory.length) {
                var firstCategoryId = firstCategory.attr('id');
                firstCategory.addClass('active');
                var firstTabContent = $('.tab_content[data-tab="' + firstCategoryId + '"]');

                if (firstTabContent.length) {
                    firstTabContent.addClass('active');
                } else {
                    $('.tabs_container').append('<div class="tab_content active" data-tab="' + firstCategoryId +
                        '"></div>');
                }

                loadProductsByCategory(firstCategoryId);
            }
            }


            // Initialize with empty state
            startConnectedDeviceScannerSync();

            function getPosDraftStorageKey() {
                const branchId = localStorage.getItem('selectedSubAdminId') || 'main';
                const saleType = new URLSearchParams(window.location.search).get('sale_type') || 'sales';
                return 'posBillDrafts_' + branchId + '_' + saleType;
            }

            function loadPosStoredDrafts() {
                try {
                    const raw = localStorage.getItem(getPosDraftStorageKey());
                    const parsed = raw ? JSON.parse(raw) : [];
                    return Array.isArray(parsed) ? parsed : [];
                } catch (e) {
                    return [];
                }
            }

            function appendPosStoredDrafts(newDrafts) {
                if (!Array.isArray(newDrafts) || !newDrafts.length) {
                    return;
                }
                const existing = loadPosStoredDrafts();
                localStorage.setItem(getPosDraftStorageKey(), JSON.stringify(existing.concat(newDrafts)));
            }

            function clearPosStoredDrafts() {
                localStorage.removeItem(getPosDraftStorageKey());
            }

            window.initPosBillTabs = function() {
                const MAX_TABS = 12;
                let tabs = [];
                let activeId = null;
                let nextNum = 1;
                let switching = false;

                function cloneItems(mapRef) {
                    try {
                        return JSON.parse(JSON.stringify(Array.from(mapRef.entries())));
                    } catch (e) {
                        return [];
                    }
                }

                function emptyState() {
                    return {
                        items: [],
                        customerId: '',
                        customerName: '',
                        customerPhone: '',
                        gstOption: 'without',
                        paymentMethod: '',
                        shipping: '',
                        shippingVisible: false,
                        additionalDiscountPercent: '',
                        additionalDiscountAmount: '',
                        additionalDiscountVisible: false,
                        remarks: '',
                        staffId: '',
                        orderType: 'self_pickup',
                        depositAmount: '',
                        tdsPercentage: '',
                        otherCharges: [],
                        otherChargesVisible: false,
                        exchangeMaterialId: '',
                        exchangeTypeId: '',
                        exchangePrice: '',
                        exchangeWeight: '1.000',
                        exchangeAmount: '',
                        exchangeVisible: false
                    };
                }

                function currentCustomerName() {
                    const val = $('#customer_name').val();
                    if (!val) return '';
                    const $opt = $('#customer_name option:selected');
                    let text = ($opt.text() || '').trim();
                    if (!text) return '';
                    const lower = text.toLowerCase();
                    if (lower === 'choose' || lower === 'select' || lower === 'choose customer') return '';
                    const phone = String($opt.data('phone') || $('#customer_phone').val() || '').trim();
                    if (phone && text.endsWith(' - ' + phone)) {
                        text = text.slice(0, -(phone.length + 3)).trim();
                    }
                    return text;
                }

                function snapshotState() {
                    const otherCharges = [];
                    $('#other_charges_container .other-charge-row').each(function() {
                        otherCharges.push({
                            name: $(this).find('input[name="other_charges_name[]"]').val() || '',
                            amount: $(this).find('input[name="other_charges_amount[]"]').val() || ''
                        });
                    });
                    return {
                        items: cloneItems(selectedItems),
                        customerId: $('#customer_name').val() || '',
                        customerName: currentCustomerName(),
                        customerPhone: $('#customer_phone').val() || '',
                        gstOption: $("input[name='gst_option']:checked").val() || 'without',
                        paymentMethod: $("input[name='payment_method']:checked").val() || '',
                        shipping: $('#shipping').val() || '',
                        shippingVisible: $('#shipping_cost_wrapper').is(':visible'),
                        additionalDiscountPercent: $('#additional_discount_percent').val() || '',
                        additionalDiscountAmount: $('#additional_discount_amount').val() || '',
                        additionalDiscountVisible: $('#additional_discount_wrapper').is(':visible'),
                        remarks: $('#remarks').val() || '',
                        staffId: $('#assigned_staff_id').val() || '',
                        orderType: $('#order_type').val() || 'self_pickup',
                        depositAmount: $('#deposit_amount').val() || '',
                        tdsPercentage: $('#tds_percentage').val() || '',
                        otherCharges: otherCharges,
                        otherChargesVisible: $('#other_charges_wrapper').is(':visible'),
                        exchangeRows: (typeof window.getPosExchangeItems === 'function') ? window.getPosExchangeItems() : [],
                        exchangeMaterialId: $('#pos_exchange_material_id').val() || '',
                        exchangeTypeId: $('#pos_exchange_type_id').val() || '',
                        exchangePrice: $('#pos_exchange_price').val() || '',
                        exchangeWeight: $('#pos_exchange_weight').val() || '',
                        exchangeAmount: $('#pos_exchange_amount').val() || '',
                        exchangeVisible: $('#exchange_charges_wrapper').is(':visible')
                    };
                }

                function snapshotActive() {
                    if (switching || !activeId) return;
                    const tab = tabs.find(t => t.id === activeId);
                    if (!tab) return;
                    tab.state = snapshotState();
                }

                function itemCount(state) {
                    return (state && state.items) ? state.items.length : 0;
                }

                function tabTitle(tab) {
                    const name = (tab.state && tab.state.customerName) ? String(tab.state.customerName).trim() : '';
                    return name || ('Bill ' + tab.number);
                }

                function renderTabs() {
                    const $wrap = $('#posBillTabs');
                    if (!$wrap.length) return;
                    $wrap.empty();
                    tabs.forEach(function(tab) {
                        const count = itemCount(tab.state);
                        const title = tabTitle(tab);
                        const safeTitle = $('<div>').text(title).html();
                        const $btn = $(`
                            <div class="pos-bill-tab${tab.id === activeId ? ' active' : ''}${count ? ' has-items' : ''}" role="button" tabindex="0" data-bill-id="${tab.id}" title="${safeTitle}" style="width:max-content;max-width:160px;flex:none;height:28px;">
                                <span class="pos-bill-tab-name">${safeTitle}</span>
                                <span class="pos-bill-tab-count">${count}</span>
                                <span class="pos-bill-tab-close" data-close-bill="${tab.id}" title="Close">&times;</span>
                            </div>
                        `);
                        $wrap.append($btn);
                    });
                }

                function restoreState(state) {
                    switching = true;
                    const data = state || emptyState();
                    selectedItems.clear();
                    (data.items || []).forEach(function(entry) {
                        if (Array.isArray(entry) && entry.length >= 2) {
                            selectedItems.set(String(entry[0]), entry[1]);
                        }
                    });
                    $('#customer_name').val(data.customerId || '').trigger('change');
                    $('#customer_phone').val(data.customerPhone || '');
                    const gstVal = data.gstOption || 'without';
                    $("input[name='gst_option'][value='" + gstVal + "']").prop('checked', true);
                    $("input[name='payment_method']").prop('checked', false);
                    $('.paymentmethod').removeClass('active');
                    if (data.paymentMethod) {
                        const $pay = $("input[name='payment_method'][value='" + data.paymentMethod + "']");
                        $pay.prop('checked', true);
                        $pay.closest('a.paymentmethod, .paymentmethod').addClass('active');
                    }
                    if (!data.paymentMethod && typeof window.resetPosPaymentUi === 'function') {
                        window.resetPosPaymentUi();
                    }
                    $('#shipping').val(data.shipping || '');
                    $('#additional_discount_percent').val(data.additionalDiscountPercent || '');
                    $('#additional_discount_amount').val(data.additionalDiscountAmount || '');
                    const showShipping = !!(data.shippingVisible || parseFloat(data.shipping || 0) > 0);
                    const showDiscount = !!(data.additionalDiscountVisible || parseFloat(data.additionalDiscountPercent || 0) > 0 || parseFloat(data.additionalDiscountAmount || 0) > 0);
                    if (typeof window.setPosShippingVisible === 'function') {
                        window.setPosShippingVisible(showShipping);
                    } else {
                        const shipPanel = document.getElementById('shipping_cost_wrapper');
                        if (shipPanel) shipPanel.style.display = showShipping ? 'flex' : 'none';
                    }
                    if (typeof window.setPosAdditionalDiscountVisible === 'function') {
                        window.setPosAdditionalDiscountVisible(showDiscount);
                    } else {
                        const discPanel = document.getElementById('additional_discount_wrapper');
                        if (discPanel) discPanel.style.display = showDiscount ? 'flex' : 'none';
                    }
                    $('#remarks').val(data.remarks || '');
                    $('#remarksModalTextarea').val(data.remarks || '');
                    if (typeof window.syncPosRemarksButtons === 'function') {
                        window.syncPosRemarksButtons();
                    } else if ($('#openEditRemarksBtn').length) {
                        if (data.remarks) {
                            $('#openAddRemarksBtn').hide();
                            $('#openEditRemarksBtn').css('display', 'inline-flex');
                        } else {
                            $('#openAddRemarksBtn').css('display', 'inline-flex');
                            $('#openEditRemarksBtn').hide();
                        }
                    }
                    if ($('#assigned_staff_id').length) {
                        $('#assigned_staff_id').val(data.staffId || '').trigger('change');
                    }
                    if ($('#order_type').length && $('#order_type').is('select')) {
                        $('#order_type').val(data.orderType || 'self_pickup').trigger('change');
                    }
                    $('#deposit_amount').val(data.depositAmount || '');
                    $('#tds_percentage').val(data.tdsPercentage || '');
                    $('#other_charges_container').empty();
                    (data.otherCharges || []).forEach(function(row) {
                        $('#other_charges_container').append(`
                            <div class="d-flex align-items-center gap-2 mb-2 other-charge-row">
                                <input type="text" class="form-control" name="other_charges_name[]" placeholder="Charge Name (e.g. Battery Charges)" value="${$('<div>').text(row.name || '').html()}">
                                <input type="number" class="form-control other-charge-amount" name="other_charges_amount[]" placeholder="Amount" min="0" step="0.01" value="${row.amount || ''}">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-other-charge-btn" style="border-radius: 6px; width: 32px; height: 30px; display: inline-flex; align-items: center; justify-content: center;" title="Remove">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `);
                    });
                    if (data.otherChargesVisible) {
                        $('#other_charges_wrapper').css('display', 'flex');
                    } else {
                        $('#other_charges_wrapper').hide();
                    }
                    if (data.exchangeRows && data.exchangeRows.length && typeof window.setPosExchangeRows === 'function') {
                        window.setPosExchangeRows(data.exchangeRows);
                    } else if (data.exchangeMaterialId && typeof window.setPosExchangeRows === 'function') {
                        window.setPosExchangeRows([{
                            material_id: data.exchangeMaterialId,
                            type_id: data.exchangeTypeId || null,
                            price: data.exchangePrice || '',
                            weight: data.exchangeWeight || '1.000',
                            amount: data.exchangeAmount || ''
                        }]);
                    } else if (typeof window.clearPosExchangeRows === 'function') {
                        window.clearPosExchangeRows();
                    } else {
                        $('#pos_exchange_material_id').val('');
                        $('#pos_exchange_type_id').val('');
                        $('#pos_exchange_price').val('');
                        $('#pos_exchange_weight').val('');
                        $('#pos_exchange_amount').val('');
                    }
                    if (data.exchangeVisible) {
                        $('#exchange_charges_wrapper').css('display', 'block');
                        $('#toggle_exchange_charges i').removeClass('fa-plus').addClass('fa-minus');
                    } else {
                        $('#exchange_charges_wrapper').hide();
                        $('#toggle_exchange_charges i').removeClass('fa-minus').addClass('fa-plus');
                    }
                    $('.check-product i').removeClass('selected');
                    selectedItems.forEach(function(item) {
                        if (item && item.productId) {
                            $(".productset[data-id='" + item.productId + "'] .check-product i").addClass('selected');
                        }
                    });
                    if (typeof window.updateTotalItems === 'function') {
                        window.updateTotalItems();
                    }
                    switching = false;
                }

                function addTab(makeActive, options) {
                    if (tabs.length >= MAX_TABS) {
                        Swal.fire('Limit reached', 'You can keep up to ' + MAX_TABS + ' bills open.', 'info');
                        return;
                    }
                    snapshotActive();
                    const keepCurrent = !!(options && options.keepCurrent);
                    const tab = {
                        id: 'bill_' + Date.now() + '_' + nextNum,
                        number: nextNum++,
                        state: keepCurrent ? snapshotState() : emptyState()
                    };
                    tabs.push(tab);
                    if (makeActive !== false) {
                        activeId = tab.id;
                        if (!keepCurrent) {
                            restoreState(tab.state);
                        }
                    }
                    renderTabs();
                }

                function switchTab(id) {
                    if (!id || id === activeId) return;
                    snapshotActive();
                    const tab = tabs.find(t => t.id === id);
                    if (!tab) return;
                    activeId = tab.id;
                    restoreState(tab.state);
                    renderTabs();
                }

                function closeTab(id) {
                    const idx = tabs.findIndex(t => t.id === id);
                    if (idx === -1) return;
                    if (tabs.length === 1) {
                        tabs[0].state = emptyState();
                        restoreState(tabs[0].state);
                        renderTabs();
                        return;
                    }
                    const wasActive = tabs[idx].id === activeId;
                    tabs.splice(idx, 1);
                    if (wasActive) {
                        const next = tabs[Math.max(0, idx - 1)];
                        activeId = next.id;
                        restoreState(next.state);
                    }
                    renderTabs();
                }

                window.refreshActivePosBillTab = function() {
                    if (switching || !activeId) return;
                    snapshotActive();
                    renderTabs();
                };

                window.getActivePosBillTitle = function() {
                    const tab = tabs.find(t => t.id === activeId);
                    return tab ? tabTitle(tab) : 'this bill';
                };

                window.getPosSelectedItems = function() {
                    return selectedItems;
                };

                window.posBillTabsCloseCurrentAfterSave = function() {
                    snapshotActive();
                    closeTab(activeId);
                    if (!tabs.length) {
                        addTab(true);
                    }
                };

                function stateHasUnsavedData(state) {
                    if (!state) {
                        return false;
                    }
                    if (Array.isArray(state.items) && state.items.length > 0) {
                        return true;
                    }
                    if (String(state.customerId || '').trim()) {
                        return true;
                    }
                    if (String(state.customerPhone || '').trim()) {
                        return true;
                    }
                    if (String(state.remarks || '').trim()) {
                        return true;
                    }
                    if (String(state.paymentMethod || '').trim()) {
                        return true;
                    }
                    if (parseFloat(state.shipping || 0) > 0) {
                        return true;
                    }
                    if (parseFloat(state.additionalDiscountPercent || 0) > 0 || parseFloat(state.additionalDiscountAmount || 0) > 0) {
                        return true;
                    }
                    if (parseFloat(state.depositAmount || 0) > 0) {
                        return true;
                    }
                    if (parseFloat(state.tdsPercentage || 0) > 0) {
                        return true;
                    }
                    if (Array.isArray(state.otherCharges) && state.otherCharges.some(function(row) {
                        return String(row.name || '').trim() !== '' || parseFloat(row.amount || 0) > 0;
                    })) {
                        return true;
                    }
                    return false;
                }

                window.posHasUnsavedBillData = function() {
                    if (window.posLeaveGuardBypass) {
                        return false;
                    }
                    snapshotActive();
                    return tabs.some(function(tab) {
                        return stateHasUnsavedData(tab.state);
                    });
                };

                window.getPosBillTabsForLeaveGuard = function() {
                    snapshotActive();
                    return tabs.map(function(tab) {
                        return {
                            id: tab.id,
                            title: tabTitle(tab),
                            number: tab.number,
                            hasUnsaved: stateHasUnsavedData(tab.state)
                        };
                    });
                };

                window.savePosBillDraftsByIds = function(selectedIds) {
                    snapshotActive();
                    const idSet = new Set(Array.isArray(selectedIds) ? selectedIds : []);
                    const drafts = tabs
                        .filter(function(tab) {
                            return idSet.has(tab.id) && stateHasUnsavedData(tab.state);
                        })
                        .map(function(tab) {
                            return {
                                title: tabTitle(tab),
                                state: JSON.parse(JSON.stringify(tab.state || emptyState())),
                                savedAt: new Date().toISOString()
                            };
                        });
                    appendPosStoredDrafts(drafts);
                };

                $(document).on('change select2:select', '#customer_name', function() {
                    if (typeof window.refreshActivePosBillTab === 'function') {
                        window.refreshActivePosBillTab();
                    }
                    if (typeof window.refreshPosCustomerDepositBalance === 'function') {
                        window.refreshPosCustomerDepositBalance();
                    }
                });

                window.refreshPosCustomerDepositBalance = function() {
                    const custId = $("#customer_name option:selected").val();
                    const custName = ($("#customer_name option:selected").text() || '').trim();
                    if (!custId || !/^\d+$/.test(String(custId)) || /default customer/i.test(custName)) {
                        $("#pos_customer_deposit_balance").val('0');
                        $("#use_customer_deposit").prop('checked', false);
                        $("#customer_deposit_apply_amount").val('').prop('disabled', true);
                        $("#posCustomerBalanceChip").removeClass('is-visible');
                        if (typeof calculateTotals === 'function') {
                            calculateTotals();
                        } else if (typeof window.calculateTotals === 'function') {
                            window.calculateTotals();
                        }
                        return;
                    }
                    $.ajax({
                        url: '/api/customer-deposit-balance/' + encodeURIComponent(custId),
                        method: 'GET',
                        headers: { "Authorization": "Bearer " + (localStorage.getItem("authToken") || '') },
                        success: function(res) {
                            const bal = (res && res.status && res.balance != null) ? parseFloat(res.balance) : 0;
                            $("#pos_customer_deposit_balance").val(Number.isFinite(bal) ? bal.toFixed(2) : '0');
                            if (typeof calculateTotals === 'function') {
                                calculateTotals();
                            } else if (typeof window.calculateTotals === 'function') {
                                window.calculateTotals();
                            }
                        },
                        error: function() {
                            $("#pos_customer_deposit_balance").val('0');
                        }
                    });
                };

                $(document).on('change', 'input[name="exchange_surplus_action_radio"]', function() {
                    const action = $(this).val() || '';
                    $("#pos_exchange_surplus_action").val(action);
                    $("#exchange_surplus_deposit_hint").toggle(action === 'deposit');
                    if (typeof calculateTotals === 'function') {
                        calculateTotals();
                    } else if (typeof window.calculateTotals === 'function') {
                        window.calculateTotals();
                    }
                    if (action === 'deposit') {
                        const custId = $("#customer_name option:selected").val();
                        const custName = ($("#customer_name option:selected").text() || '').trim();
                        if (!custId || !/^\d+$/.test(String(custId)) || /default customer/i.test(custName)) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Select customer',
                                text: 'Choose the customer who will receive this deposit.'
                            });
                        }
                    }
                });

                $(document).on('change', '#use_customer_deposit', function() {
                    const on = $(this).is(':checked');
                    $("#customer_deposit_apply_amount").prop('disabled', !on);
                    if (on) {
                        const bal = Math.max(0, parseFloat($("#pos_customer_deposit_balance").val()) || 0);
                        const payable = Math.max(0, parseFloat($("input[name='total']").val()) || 0);
                        // payable already may include prior apply; use balance + current total as rough max
                        const suggest = Math.min(bal, payable > 0 ? payable + (parseFloat($("#pos_customer_deposit_applied").val()) || 0) : bal);
                        if (!$("#customer_deposit_apply_amount").val()) {
                            $("#customer_deposit_apply_amount").val(suggest > 0 ? suggest.toFixed(2) : '');
                        }
                    } else {
                        $("#customer_deposit_apply_amount").val('');
                        $("#pos_customer_deposit_applied").val('0');
                    }
                    if (typeof calculateTotals === 'function') {
                        calculateTotals();
                    } else if (typeof window.calculateTotals === 'function') {
                        window.calculateTotals();
                    }
                });

                $(document).on('input change', '#customer_deposit_apply_amount', function() {
                    if (typeof calculateTotals === 'function') {
                        calculateTotals();
                    } else if (typeof window.calculateTotals === 'function') {
                        window.calculateTotals();
                    }
                });

                $('#posAddBillTabBtn').on('click', function() {
                    addTab(true);
                });
                $(document).on('click', '.pos-bill-tab', function(e) {
                    if ($(e.target).closest('.pos-bill-tab-close').length) return;
                    switchTab(String($(this).attr('data-bill-id') || ''));
                });
                $(document).on('click', '.pos-bill-tab-close', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    closeTab(String($(this).data('close-bill')));
                });
                $(document).on('keydown', function(e) {
                    if (!e.shiftKey || (e.key !== 'T' && e.key !== 't')) return;
                    if ($(e.target).is('input, textarea, select, [contenteditable="true"]')) return;
                    e.preventDefault();
                    addTab(true);
                });

                function restoreStoredDraftTabs(storedDrafts) {
                    if (!Array.isArray(storedDrafts) || !storedDrafts.length) {
                        return;
                    }
                    tabs = [];
                    activeId = null;
                    storedDrafts.forEach(function(draft, index) {
                        const tab = {
                            id: 'bill_' + Date.now() + '_' + (nextNum + index),
                            number: nextNum++,
                            state: draft.state || emptyState()
                        };
                        tabs.push(tab);
                        if (index === 0) {
                            activeId = tab.id;
                        }
                    });
                    restoreState(tabs[0].state);
                    renderTabs();
                }

                function promptRestoreStoredDrafts(storedDrafts) {
                    Swal.fire({
                        title: 'Restore saved drafts?',
                        text: 'You have ' + storedDrafts.length + ' bill draft(s) saved from your last visit.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Restore drafts',
                        cancelButtonText: 'Discard drafts',
                        confirmButtonColor: '#ff9f43',
                        reverseButtons: true
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            restoreStoredDraftTabs(storedDrafts);
                        } else {
                            addTab(true, { keepCurrent: true });
                        }
                        clearPosStoredDrafts();
                    });
                }

                const pendingDrafts = loadPosStoredDrafts();
                if (pendingDrafts.length) {
                    promptRestoreStoredDrafts(pendingDrafts);
                } else {
                    addTab(true, { keepCurrent: true });
                }
            };
            if ($('#posBillTabs').length) {
                window.initPosBillTabs();
            } else {
                function snapshotSingleBillState() {
                    const otherCharges = [];
                    $('#other_charges_container .other-charge-row').each(function() {
                        otherCharges.push({
                            name: $(this).find('input[name="other_charges_name[]"]').val() || '',
                            amount: $(this).find('input[name="other_charges_amount[]"]').val() || ''
                        });
                    });
                    let items = [];
                    if (typeof selectedItems !== 'undefined') {
                        try {
                            items = JSON.parse(JSON.stringify(Array.from(selectedItems.entries())));
                        } catch (e) {
                            items = [];
                        }
                    }
                    return {
                        items: items,
                        customerId: $('#customer_name').val() || '',
                        customerName: ($('#customer_name option:selected').text() || '').trim(),
                        customerPhone: $('#customer_phone').val() || '',
                        gstOption: $("input[name='gst_option']:checked").val() || 'without',
                        paymentMethod: $("input[name='payment_method']:checked").val() || '',
                        shipping: $('#shipping').val() || '',
                        shippingVisible: $('#shipping_cost_wrapper').is(':visible'),
                        additionalDiscountPercent: $('#additional_discount_percent').val() || '',
                        additionalDiscountAmount: $('#additional_discount_amount').val() || '',
                        additionalDiscountVisible: $('#additional_discount_wrapper').is(':visible'),
                        remarks: $('#remarks').val() || '',
                        staffId: $('#assigned_staff_id').val() || '',
                        orderType: $('#order_type').val() || 'self_pickup',
                        depositAmount: $('#deposit_amount').val() || '',
                        tdsPercentage: $('#tds_percentage').val() || '',
                        otherCharges: otherCharges,
                        otherChargesVisible: $('#other_charges_wrapper').is(':visible'),
                        exchangeRows: (typeof window.getPosExchangeItems === 'function') ? window.getPosExchangeItems() : [],
                        exchangeMaterialId: $('#pos_exchange_material_id').val() || '',
                        exchangeTypeId: $('#pos_exchange_type_id').val() || '',
                        exchangePrice: $('#pos_exchange_price').val() || '',
                        exchangeWeight: $('#pos_exchange_weight').val() || '',
                        exchangeAmount: $('#pos_exchange_amount').val() || '',
                        exchangeVisible: $('#exchange_charges_wrapper').is(':visible')
                    };
                }

                function singleBillHasUnsavedData(state) {
                    if (!state) return false;
                    if (Array.isArray(state.items) && state.items.length > 0) return true;
                    if (String(state.customerId || '').trim()) return true;
                    if (String(state.customerPhone || '').trim()) return true;
                    if (String(state.remarks || '').trim()) return true;
                    if (String(state.paymentMethod || '').trim()) return true;
                    if (parseFloat(state.additionalDiscountPercent || 0) > 0 || parseFloat(state.additionalDiscountAmount || 0) > 0) return true;
                    if (String(state.exchangeMaterialId || '').trim()) return true;
                    return false;
                }

                window.getPosBillTabsForLeaveGuard = function() {
                    const state = snapshotSingleBillState();
                    const customerName = String(state.customerName || '').trim();
                    return [{
                        id: 'single_bill',
                        title: customerName || 'Bill 1',
                        hasUnsaved: singleBillHasUnsavedData(state)
                    }];
                };

                window.savePosBillDraftsByIds = function(selectedIds) {
                    if (!Array.isArray(selectedIds) || !selectedIds.includes('single_bill')) {
                        return;
                    }
                    const state = snapshotSingleBillState();
                    if (!singleBillHasUnsavedData(state)) {
                        return;
                    }
                    appendPosStoredDrafts([{
                        title: String(state.customerName || '').trim() || 'Bill 1',
                        state: state,
                        savedAt: new Date().toISOString()
                    }]);
                };

                window.posHasUnsavedBillData = function() {
                    if (window.posLeaveGuardBypass) {
                        return false;
                    }
                    return singleBillHasUnsavedData(snapshotSingleBillState());
                };
            }

            (function initPosLeaveGuard() {
                window.posLeaveGuardBypass = false;

                function shouldGuardNavigation() {
                    return typeof window.posHasUnsavedBillData === 'function' && window.posHasUnsavedBillData();
                }

                function isPosSafeNavigationLink($el, href) {
                    if (!href || href === '#' || href.indexOf('#') === 0 || href.indexOf('javascript:') === 0) {
                        return true;
                    }
                    if ($el.attr('target') === '_blank' || $el.attr('download') !== undefined) {
                        return true;
                    }
                    if ($el.closest('.swal2-container, .modal, #posBillTabsBar, .pos-bill-tab').length) {
                        return true;
                    }
                    return false;
                }

                function buildLeaveDraftHtml(tabsForDraft) {
                    let html = '<p class="pos-leave-draft-intro">Select which bills to save as draft. Unchecked bills will be discarded.</p>';
                    html += '<div class="pos-leave-draft-list">';
                    tabsForDraft.forEach(function(tab) {
                        const safeTitle = $('<div>').text(tab.title || 'Bill').html();
                        html += '<label><input type="checkbox" class="pos-leave-draft-check" value="' +
                            $('<div>').text(tab.id).html() + '" checked>' +
                            '<span class="pos-leave-draft-name">' + safeTitle + '</span></label>';
                    });
                    html += '</div>';
                    return html;
                }

                function confirmLeavePage(onConfirm) {
                    const tabsForDraft = (typeof window.getPosBillTabsForLeaveGuard === 'function')
                        ? window.getPosBillTabsForLeaveGuard().filter(function(tab) {
                            return tab.hasUnsaved;
                        })
                        : [];

                    if (!tabsForDraft.length) {
                        window.posLeaveGuardBypass = true;
                        if (typeof onConfirm === 'function') {
                            onConfirm();
                        }
                        return;
                    }

                    if (typeof Swal === 'undefined') {
                        if (window.confirm('Save selected bills as draft before leaving?')) {
                            if (typeof window.savePosBillDraftsByIds === 'function') {
                                window.savePosBillDraftsByIds(tabsForDraft.map(function(tab) {
                                    return tab.id;
                                }));
                            }
                            window.posLeaveGuardBypass = true;
                            if (typeof onConfirm === 'function') {
                                onConfirm();
                            }
                        }
                        return;
                    }

                    Swal.fire({
                        title: 'Save bills as draft?',
                        html: buildLeaveDraftHtml(tabsForDraft),
                        icon: 'question',
                        showCancelButton: true,
                        showDenyButton: true,
                        confirmButtonText: 'Save selected & leave',
                        denyButtonText: 'Leave without saving',
                        cancelButtonText: 'Stay on bill',
                        confirmButtonColor: '#ff9f43',
                        reverseButtons: true,
                        focusCancel: true,
                        preConfirm: function() {
                            const selectedIds = [];
                            document.querySelectorAll('.pos-leave-draft-check:checked').forEach(function(el) {
                                selectedIds.push(el.value);
                            });
                            return selectedIds;
                        }
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            const selectedIds = Array.isArray(result.value) ? result.value : [];
                            if (selectedIds.length && typeof window.savePosBillDraftsByIds === 'function') {
                                window.savePosBillDraftsByIds(selectedIds);
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Drafts saved',
                                        text: selectedIds.length + ' bill draft(s) saved. Open New Sales to restore them.',
                                        timer: 2200,
                                        showConfirmButton: false
                                    });
                                }
                            }
                            window.posLeaveGuardBypass = true;
                            if (typeof onConfirm === 'function') {
                                setTimeout(function() {
                                    onConfirm();
                                }, selectedIds.length ? 1200 : 0);
                            }
                            return;
                        }
                        if (result.isDenied) {
                            window.posLeaveGuardBypass = true;
                            if (typeof onConfirm === 'function') {
                                onConfirm();
                            }
                        }
                    });
                }

                $(document).on('click', 'a[href]', function(e) {
                    if (window.posLeaveGuardBypass || !shouldGuardNavigation()) {
                        return;
                    }
                    const $link = $(this);
                    const href = String($link.attr('href') || '').trim();
                    if (!href || isPosSafeNavigationLink($link, href)) {
                        return;
                    }
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    confirmLeavePage(function() {
                        window.location.href = href;
                    });
                });

                window.addEventListener('beforeunload', function(e) {
                    if (window.posLeaveGuardBypass || !shouldGuardNavigation()) {
                        return;
                    }
                    e.preventDefault();
                    e.returnValue = '';
                    return '';
                });
            })();

            window.fitPosProductGrid = function() {
                if (typeof window.syncPosBillTableHeight === 'function') {
                    window.syncPosBillTableHeight();
                }
            };

            window.fitPosProductGrid();
            setTimeout(window.fitPosProductGrid, 200);
            setTimeout(window.fitPosProductGrid, 800);
            $(window).on('resize.fitPosProductGrid', function() {
                window.fitPosProductGrid();
            });
            if (typeof ResizeObserver !== 'undefined' && !window._posBillFitObserver) {
                const paymentEl = document.getElementById('paymentPanel');
                let fitTimer = null;
                window._posBillFitObserver = new ResizeObserver(function() {
                    clearTimeout(fitTimer);
                    fitTimer = setTimeout(function() {
                        if (typeof window.fitPosProductGrid === 'function') {
                            window.fitPosProductGrid();
                        }
                        if (typeof window.updatePaymentPanelWidth === 'function') {
                            window.updatePaymentPanelWidth();
                        }
                    }, 50);
                });
                if (paymentEl) {
                    window._posBillFitObserver.observe(paymentEl);
                }
            }

            (function initPosMallSplit() {
                const layout = document.getElementById('posMallLayout');
                const handle = document.getElementById('posSplitHandle');
                if (!layout || !handle) return;

                const storageKey = 'posMallLeftWidthV3';
                const saved = localStorage.getItem(storageKey);
                if (saved) {
                    layout.style.setProperty('--pos-left-width', saved);
                }

                let dragging = false;

                function setWidthFromClientX(clientX) {
                    const rect = layout.getBoundingClientRect();
                    if (!rect.width) return;
                    let pct = ((clientX - rect.left) / rect.width) * 100;
                    pct = Math.max(28, Math.min(58, pct));
                    const value = pct.toFixed(1) + '%';
                    layout.style.setProperty('--pos-left-width', value);
                    localStorage.setItem(storageKey, value);
                    if (typeof window.updatePaymentPanelWidth === 'function') {
                        window.updatePaymentPanelWidth();
                    }
                    if (typeof window.fitPosProductGrid === 'function') {
                        window.fitPosProductGrid();
                    }
                }

                handle.addEventListener('mousedown', function(e) {
                    if (window.innerWidth < 992) return;
                    e.preventDefault();
                    dragging = true;
                    layout.classList.add('is-resizing');
                });
                document.addEventListener('mousemove', function(e) {
                    if (!dragging) return;
                    setWidthFromClientX(e.clientX);
                });
                document.addEventListener('mouseup', function() {
                    if (!dragging) return;
                    dragging = false;
                    layout.classList.remove('is-resizing');
                });
            })();
            updateTotalItems();
        });
        $(".select2").not('#customer_name').select2({
            tags: true,
        });

        // ==================== CUSTOMER SELECT2 — TYPE & AUTO-SAVE ====================
        $('#customer_name').select2({
            tags: true, // allows typing a new value
            width: '100%',
            createTag: function(params) {
                var term = $.trim(params.term);
                if (!term) return null;
                return {
                    id: term, // use the typed name as a temporary id
                    text: term,
                    newTag: true // flag so we know it's a new entry
                };
            },
            templateResult: function(data) {
                if (data.newTag) {
                    return $('<span style="color:white;">' + data.text + '</span>');
                }
                return data.text;
            },
            matcher: function(params, data) {
                if ($.trim(params.term) === '') return data;
                if (typeof data.text === 'undefined') return null;
                var term = params.term.toLowerCase();
                var text = (data.text || '').toLowerCase();
                var phone = ($(data.element).data('phone') || '').toString().toLowerCase();
                var normTerm = term.replace(/[^0-9a-z]/g, '');
                var normPhone = phone.replace(/[^0-9]/g, '');
                if (text.indexOf(term) > -1 || phone.indexOf(term) > -1) return data;
                if (normTerm && normPhone.indexOf(normTerm) > -1) return data;
                return null;
            }
        });

        // On select: fill phone and GST for existing, or auto-save for new
        $('#customer_name').on('select2:select', function(e) {
            var data = e.params.data;

            // Existing customer — fill phone and GST then done
            if (!data.newTag) {
                var phone = $(data.element).data('phone') || '';
                var gst = $(data.element).data('gst') || '';
                $('#customer_phone').val(phone);
                $('#pos_gst_number').val(gst);
                return;
            }

            // New name typed — auto-save to DB
            var newName = data.text.trim();
            var $select = $(this);

            // Temporarily disable while saving
            $select.prop('disabled', true);
            $(".error_customername").text('');

            // Generate a safe dummy phone that can never collide with real Indian numbers
            // Real Indian phones never start with 0 (they start with 6-9)
            // Format: 00000XXXXX where XXXXX is random 5 digits
            function generateSafeDummyPhone() {
                var rand = Math.floor(10000 + Math.random() * 90000); // 5-digit random
                return '00000' + rand;
            }

            function attemptCreateCustomer(phone, retryCount) {
                var formData = new FormData();
                formData.append('customer_name', newName);
                formData.append('phone', phone);

                var sid = localStorage.getItem('selectedSubAdminId');
                if (sid && sid !== 'null' && sid !== 'undefined') {
                    formData.append('selectedSubAdminId', sid);
                }

                $.ajax({
                    url: '/api/createCustomer',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Authorization': 'Bearer ' + localStorage.getItem('authToken')
                    },
                    success: function(response) {
                        if (response.status) {
                            // Replace the temp tag option with a real one using the returned ID
                            var realId = response.customer_id || response.id || newName;
                            // Remove the temp tag option
                            $select.find('option[value="' + newName + '"]').remove();
                            // Add a proper option and select it
                            var $opt = new Option(newName, realId, true, true);
                            $($opt).attr('data-phone', '');
                            $select.append($opt).trigger('change');
                            $('#customer_phone').val('');
                        } else {
                            // If phone collision, retry once with new dummy
                            if (retryCount > 0 && response.message && response.message.toLowerCase()
                                .includes('phone')) {
                                return attemptCreateCustomer(generateSafeDummyPhone(), retryCount - 1);
                            }
                            // Deselect and show error
                            $select.val('').trigger('change');
                            $(".error_customername")
                                .text(response.message || 'Failed to save customer.')
                                .css('color', 'red');
                        }
                    },
                    error: function(xhr) {
                        var errors = (xhr.responseJSON || {}).errors || {};
                        // If phone collision error on auto-save, retry silently with a new dummy
                        if (retryCount > 0 && errors.phone) {
                            return attemptCreateCustomer(generateSafeDummyPhone(), retryCount - 1);
                        }
                        $select.val('').trigger('change');
                        var msg = errors.customer_name ?
                            errors.customer_name[0] :
                            (errors.phone ?
                                'Failed to create customer (phone conflict). Please try again.' :
                                ((xhr.responseJSON || {}).message || 'Failed to save customer.'));
                        $(".error_customername").text(msg).css('color', 'red');
                    },
                    complete: function() {
                        $select.prop('disabled', false);
                    }
                });
            }

            attemptCreateCustomer(generateSafeDummyPhone(), 3);
        });

        function toggleCustomerButtons() {
            var val = $('#customer_name').val();
            $('#openQuickAddCustomerBtn').css('display', 'inline-flex');
            if (val) {
                $('#openEditCustomerBtn').css('display', 'inline-flex');
            } else {
                $('#openEditCustomerBtn').hide();
            }
        }

        // Fill phone and GST when selecting an existing customer via the change event
        $('#customer_name').on('change', function() {
            var val = $(this).val();
            var $selected = $(this).find('option[value="' + val + '"]');
            // Only fill phone/gst for real existing customers (data-phone attribute set)
            if ($selected.attr('data-phone') !== undefined) {
                $('#customer_phone').val($selected.data('phone') || '');
                $('#pos_gst_number').val($selected.data('gst') || '');
                $('.error_customer_phone').text('');
            }
            toggleCustomerButtons();
        });

        (function initPosPhoneCustomerLookup() {
            var lookupTimer = null;
            var lastLookup = '';

            function phoneDigits(value) {
                return String(value || '').replace(/\D/g, '');
            }

            function phonesMatch(a, b) {
                var x = phoneDigits(a);
                var y = phoneDigits(b);
                if (!x || !y) return false;
                if (x === y) return true;
                return x.slice(-10) === y.slice(-10);
            }

            function findLocalCustomerByPhone(phone) {
                var $match = $('#customer_name option').filter(function() {
                    var id = $(this).val();
                    if (!id) return false;
                    return phonesMatch($(this).attr('data-phone') || $(this).data('phone') || '', phone);
                }).first();
                return $match.length ? $match : null;
            }

            function setPhoneValidation(message) {
                $('.error_customer_phone').text(message || '').css('color', '#dc3545');
            }

            function selectCustomerFromOption($opt) {
                if (!$opt || !$opt.length) return;
                $('#customer_name').val($opt.val()).trigger('change');
                setPhoneValidation('');
            }

            function lookupCustomerFromPhone(fromBlur) {
                var raw = $('#customer_phone').val();
                var digits = phoneDigits(raw);
                if (!digits) {
                    setPhoneValidation('');
                    lastLookup = '';
                    return;
                }
                if (digits.length < 8) {
                    if (fromBlur) {
                        setPhoneValidation('Enter a valid phone number');
                    }
                    return;
                }

                var selectedPhone = $('#customer_name option:selected').data('phone');
                if (phonesMatch(selectedPhone, digits)) {
                    setPhoneValidation('');
                    lastLookup = digits;
                    return;
                }

                if (digits === lastLookup && !fromBlur) {
                    return;
                }
                lastLookup = digits;

                var $opt = findLocalCustomerByPhone(digits);
                if ($opt) {
                    selectCustomerFromOption($opt);
                    return;
                }

                $.ajax({
                    url: '/search-users',
                    method: 'GET',
                    data: { query: digits },
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        var users = (data && data.users) ? data.users : [];
                        var match = null;
                        users.forEach(function(user) {
                            if (phonesMatch(user.phone, digits)) {
                                match = user;
                            }
                        });
                        if (match && match.id) {
                            if (typeof autoSelectCustomer === 'function') {
                                autoSelectCustomer({
                                    id: match.id,
                                    name: match.name,
                                    phone: match.phone || digits,
                                    gst_number: match.gst_number || ''
                                }, false);
                            } else {
                                var $existing = $('#customer_name option[value="' + match.id + '"]');
                                if ($existing.length) {
                                    selectCustomerFromOption($existing);
                                }
                            }
                            setPhoneValidation('');
                            return;
                        }
                        setPhoneValidation('No customer found with this number');
                    },
                    error: function() {
                        setPhoneValidation('No customer found with this number');
                    }
                });
            }

            $('#customer_phone').on('input', function() {
                setPhoneValidation('');
                clearTimeout(lookupTimer);
                lookupTimer = setTimeout(function() {
                    lookupCustomerFromPhone(false);
                }, 350);
            });

            $('#customer_phone').on('blur', function() {
                clearTimeout(lookupTimer);
                lookupCustomerFromPhone(true);
            });

            $('#customer_phone').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(lookupTimer);
                    lookupCustomerFromPhone(true);
                }
            });
        })();

        // Initialize button states
        toggleCustomerButtons();

        // ===== GST API LOOKUP FOR POS (same as addcustomer page) =====
        (function() {
            var authToken = localStorage.getItem('authToken');
            var $gstInput = $('#pos_gst_number');
            var $gstLoader = $('#pos-gst-loader');
            var $gstMsg = $('#pos-gst-msg');

            function setGstMsg(msg, color) {
                $gstMsg.html('<span style="color:' + (color || '#333') + ';">' + msg + '</span>');
            }

            $gstInput.on('input', function() {
                // Normalize: uppercase, alphanumeric only, max 15
                var raw = $(this).val().toUpperCase().replace(/[^0-9A-Z]/g, '').substring(0, 15);
                $(this).val(raw);

                $gstMsg.html('');

                if (raw.length < 15) return; // wait until 15 chars

                // Check: does an existing customer already have this GST?
                var $existingOpt = $('#customer_name option').filter(function() {
                    return ($(this).data('gst') || '').toUpperCase() === raw;
                }).first();

                if ($existingOpt.length) {
                    // Select existing customer — no duplicate needed
                    var existingVal = $existingOpt.val();
                    $('#customer_name').val(existingVal).trigger('change');
                    // Also re-trigger select2 selection display
                    if ($('#customer_name').data('select2')) {
                        $('#customer_name').val(existingVal).trigger('change.select2');
                    }
                    setGstMsg('<i class="fas fa-check-circle"></i> Existing customer found & selected.',
                        '#28a745');
                    return;
                }

                // Fetch from GST API
                $gstLoader.show();
                $gstInput.addClass('loading').prop('readonly', true);
                setGstMsg('<i class="fas fa-spinner fa-spin"></i> Fetching GST details...', '#1B2850');

                $.ajax({
                    url: '/api/fetch-gst-details',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        gst_number: raw
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Authorization': 'Bearer ' + authToken
                    },
                    success: function(res) {
                        if (res.error || !res.legal_name) {
                            setGstMsg('<i class="fas fa-times-circle"></i> GST details not found.',
                                '#dc3545');
                            return;
                        }

                        var companyName = (res.company_name || '').trim();
                        var legalName = (res.legal_name || '').trim();
                        var fetchedName = companyName ? companyName : legalName;

                        // Double-check: search existing customers by name too
                        var $nameOpt = $('#customer_name option').filter(function() {
                            return $(this).text().toLowerCase().indexOf(fetchedName
                                .toLowerCase()) > -1;
                        }).first();

                        if ($nameOpt.length) {
                            // Existing customer matched by name — select & update GST attr
                            $nameOpt.attr('data-gst', raw);
                            var existingVal = $nameOpt.val();
                            $('#customer_name').val(existingVal).trigger('change');
                            if ($('#customer_name').data('select2')) {
                                $('#customer_name').val(existingVal).trigger('change.select2');
                            }
                            setGstMsg(
                                '<i class="fas fa-check-circle"></i> Existing customer matched & selected.',
                                '#28a745');
                        } else {
                            // New customer — pre-fill name in select2 as a typed new tag
                            var $newOpt = new Option(fetchedName, fetchedName, true, true);
                            $($newOpt).data('newTag', true).data('phone', '').data('gst', raw);
                            $('#customer_name').append($newOpt).trigger('change');
                            if ($('#customer_name').data('select2')) {
                                $('#customer_name').val(fetchedName).trigger('change.select2');
                            }
                            setGstMsg('<i class="fas fa-info-circle"></i> New customer: "' +
                                fetchedName + '". Please fill phone & save bill.', '#ff9f43');
                        }
                    },
                    error: function() {
                        setGstMsg(
                            '<i class="fas fa-times-circle"></i> Failed to fetch GST details.',
                            '#dc3545');
                    },
                    complete: function() {
                        $gstLoader.hide();
                        $gstInput.removeClass('loading').prop('readonly', false);
                    }
                });
            });
        })();
        $(document).ready(function() {
            const addBankModalElement = document.getElementById('addBankModal');
            const addBankModal = addBankModalElement ? new bootstrap.Modal(addBankModalElement) : null;

            @if ($showNewBillModal)
                const newBillTypeModalElement = document.getElementById('newBillTypeModal');
                if (newBillTypeModalElement) {
                    const newBillTypeModal = new bootstrap.Modal(newBillTypeModalElement, {
                        backdrop: 'static',
                        keyboard: false
                    });
                    newBillTypeModal.show();
                }
            @endif

            $('#openAddBankModal').on('click', function() {
                resetPosAddBankForm();
                if (addBankModal) {
                    addBankModal.show();
                }
            });

            // EMI box "Add Bank" button — reuses the same modal
            $('#openAddBankModalEmi').on('click', function() {
                resetPosAddBankForm();
                if (addBankModal) {
                    addBankModal.show();
                }
            });

            $('#addBankForm').on('submit', function(e) {
                e.preventDefault();

                $('#addBankForm .text-danger').text('');

                const formData = new FormData(this);
                if (selectedSubAdminId && selectedSubAdminId !== "null" && selectedSubAdminId !==
                    "undefined") {
                    formData.append('selectedSubAdminId', selectedSubAdminId);
                }

                const saveButton = $('#saveBankBtn');
                saveButton.prop('disabled', true).text('Saving...');

                $.ajax({
                    url: '/api/banks',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(response) {
                        upsertPosBankOption(response.data || null);
                        $(".error_bank").text("");

                        if (addBankModal) {
                            addBankModal.hide();
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message || 'Bank added successfully.',
                            confirmButtonText: 'OK'
                        });
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors || {};

                        $('#addBankNameError').text(errors.bank_name ? errors.bank_name[0] :
                            '');
                        $('#addAccountNumberError').text(errors.account_number ? errors
                            .account_number[0] : '');
                        $('#addIfscCodeError').text(errors.ifsc_code ? errors.ifsc_code[0] :
                            '');
                        $('#addBranchNameError').text(errors.branch_name ? errors.branch_name[
                                0] :
                            '');
                        $('#addOpeningBalanceError').text(errors.opening_balance ? errors
                            .opening_balance[0] : '');
                        $('#addBankStatusError').text(errors.status ? errors.status[0] : '');

                        if (!Object.keys(errors).length) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message ||
                                    'Failed to add bank.',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    complete: function() {
                        saveButton.prop('disabled', false).text('Save Bank');
                    }
                });
            });

            $(document).on('click', '.close-search-result', function() {
                const $box = $(this).closest('.pos-bill-search-results');
                window.resetPosSearchDropdownPosition($box);
                $box.hide().empty();
            });

            $(document).on('mouseenter', '.pos-search-item', function() {
                const $item = $(this);
                const $box = $item.closest('.pos-bill-search-results');
                const index = $box.find('.pos-search-item').index($item);
                if (index >= 0) {
                    $box.data('pos-search-active-index', index);
                    $box.find('.pos-search-item').removeClass('is-active');
                    $item.addClass('is-active');
                }
            });

            function clearPosSearchContext($btn) {
                const $fromResults = $btn.closest('.pos-bill-search-results');
                let $billInput = $();
                let $billResults = $();

                if ($fromResults.length) {
                    $billResults = $fromResults;
                    const inputEl = $fromResults.data('pos-search-input');
                    if (inputEl) {
                        $billInput = $(inputEl);
                    }
                }

                if (!$billInput.length) {
                    const $billRow = $btn.closest('.pos-bill-search-row');
                    if ($billRow.length) {
                        $billInput = $billRow.find('.pos-bill-search-input');
                        $billResults = window.getPosSearchResultsForInput($billInput);
                    }
                }

                if ($billInput.length) {
                    $billInput.val('');
                }
                if ($billResults.length) {
                    window.resetPosSearchDropdownPosition($billResults);
                    $billResults.hide().empty();
                }
                // Also close any orphaned floating dropdowns left on body after cart re-render
                $('.pos-bill-search-results').each(function() {
                    const $box = $(this);
                    window.resetPosSearchDropdownPosition($box);
                    $box.hide().empty();
                });
                window.posBillSearchFocusNext = true;
            }

            let posBillSearchTimer = null;
            let posBillSearchXhr = null;

            $(document).on('input', '.pos-bill-search-input', function() {
                const $input = $(this);
                const query = $input.val().trim();
                const $resultBoxBill = window.getPosSearchResultsForInput($input);

                if (posBillSearchTimer) {
                    clearTimeout(posBillSearchTimer);
                    posBillSearchTimer = null;
                }
                if (posBillSearchXhr && posBillSearchXhr.readyState !== 4) {
                    posBillSearchXhr.abort();
                    posBillSearchXhr = null;
                }

                if (query.length < 1) {
                    window.resetPosSearchDropdownPosition($resultBoxBill);
                    $resultBoxBill.hide().empty();
                    return;
                }

                posBillSearchTimer = setTimeout(function() {
                    const currentQuery = $input.val().trim();
                    if (currentQuery.length < 1) {
                        return;
                    }

                    const token = localStorage.getItem('authToken') || (typeof authToken !== 'undefined' ? authToken : '');
                    const branchId = localStorage.getItem('selectedSubAdminId') || (typeof selectedSubAdminId !== 'undefined' ? selectedSubAdminId : '');
                    const imageBase = "{{ rtrim(env('ImagePath'), '/') }}/storage/";
                    const noImage = "{{ env('ImagePath') . 'admin/assets/img/product/noimage.png' }}";

                    // Same product search as All Products (/api/getAllProduct + matchingSearch)
                    posBillSearchXhr = $.ajax({
                        url: '/api/getAllProduct',
                    method: 'GET',
                        cache: false,
                    headers: {
                            "Authorization": "Bearer " + token,
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                            search: currentQuery,
                            page: 1,
                            per_page: 25,
                            sub_branch_id: branchId || undefined,
                            _ts: Date.now()
                    },
                    dataType: 'json',
                    success: function(data) {
                            if ($input.val().trim() !== currentQuery) {
                                return;
                            }
                            if (!data || !data.status) {
                                window.resetPosSearchDropdownPosition($resultBoxBill);
                                $resultBoxBill.hide();
                                return;
                            }

                            const currencySymbol = data.currencySymbol || '₹';
                            const currencyPosition = data.currencyPosition || 'left';
                            const formatMoney = function(amount) {
                                const n = parseFloat(amount);
                                if (!Number.isFinite(n)) return 'N/A';
                                const txt = n.toFixed(2);
                                return currencyPosition === 'left' ? (currencySymbol + txt) : (txt + currencySymbol);
                            };

                            const products = (data.data || []).map(function(product) {
                                let image = noImage;
                                try {
                                    let imagesArray = product.images;
                                    if (typeof imagesArray === 'string') {
                                        imagesArray = JSON.parse(imagesArray);
                                    }
                                    if (Array.isArray(imagesArray) && imagesArray.length > 0) {
                                        image = imageBase + imagesArray[0];
                                    }
                                } catch (e) {}

                                let sellingPrices = product.selling_prices;
                                if (typeof sellingPrices === 'string') {
                                    try { sellingPrices = JSON.parse(sellingPrices); } catch (e) { sellingPrices = []; }
                                }
                                if (!Array.isArray(sellingPrices)) {
                                    sellingPrices = [];
                                }

                                const categoryName = product.category
                                    ? (product.category.name || '')
                                    : (product.category_name || '');

                                let productGst = product.product_gst || null;
                                if (productGst && typeof productGst !== 'string') {
                                    try {
                                        productGst = JSON.stringify(productGst);
                                    } catch (e) {
                                        productGst = null;
                                    }
                                }
                                if (typeof productGst === 'string') {
                                    const trimmedGst = productGst.trim();
                                    if (!trimmedGst || trimmedGst === 'null' || trimmedGst === '[]') {
                                        productGst = null;
                                    } else {
                                        productGst = trimmedGst;
                                    }
                                }

                                let gstOption = product.gst_option || 'without_gst';
                                if (gstOption !== 'with_gst' && gstOption !== 'without_gst') {
                                    gstOption = 'without_gst';
                                }
                                if (gstOption === 'with_gst' && !productGst) {
                                    gstOption = 'without_gst';
                                }

                                return {
                                    id: product.id,
                                    name: product.name,
                                    price: product.price,
                                    price_formatted: formatMoney(product.price),
                                    mrp: product.mrp,
                                    quantity: product.quantity,
                                    purchase_price: product.purchase_price,
                                    category_id: product.category_id,
                                    category_name: categoryName,
                                    gst_option: gstOption,
                                    product_gst: productGst,
                                    barcode: product.barcode || product.SKU || product.product_code || '',
                                    SKU: product.SKU || product.barcode || '',
                                    selling_prices: sellingPrices,
                                    is_rent_available: product.is_rent_available || 0,
                                    rent_price: product.rent_price,
                                    rent_period: product.rent_period || '',
                                    rent_price_formatted: product.rent_price != null ? formatMoney(product.rent_price) : null,
                                    image: image,
                                    is_warranty_category: String(categoryName || '').toLowerCase().trim() === 'warranty'
                                };
                            });

                            const total = (data.pagination && data.pagination.total != null)
                                ? data.pagination.total
                                : products.length;

                            window.renderPosProductSearchResults(
                                $resultBoxBill,
                                products,
                                $input,
                                {
                                    total: total,
                                    limit: 25
                                }
                            );
                        },
                        error: function(xhr) {
                            if (xhr && xhr.statusText === 'abort') {
                                return;
                            }
                            window.resetPosSearchDropdownPosition($resultBoxBill);
                            $resultBoxBill.hide();
                        }
                    });
                }, 280);
            });

            $(document).on('keydown', '.pos-bill-search-input', async function(e) {
                const $input = $(this);
                const $resultBoxBill = window.getPosSearchResultsForInput($input);

                if ($resultBoxBill.is(':visible') && $resultBoxBill.find('.pos-search-item').length) {
                    if (e.key === 'ArrowDown' || e.key === 'ArrowUp' || e.key === 'Escape') {
                        window.handlePosSearchKeyboard(e, $input, $resultBoxBill);
                        return;
                    }
                    if (e.key === 'Enter') {
                        const activeIndex = Number($resultBoxBill.data('pos-search-active-index'));
                        if (!Number.isNaN(activeIndex) && activeIndex >= 0) {
                            window.handlePosSearchKeyboard(e, $input, $resultBoxBill);
                            return;
                        }
                    }
                }

                if (e.key !== 'Enter') {
                    return;
                }
                e.preventDefault();
                const exactQuery = $input.val().trim();
                if (exactQuery.length < 1) {
                    return;
                }

                try {
                    const added = await window.handlePosBarcodeScan(exactQuery, {
                        focusBillSearch: true,
                        playBeep: true
                    });
                    if (added) {
                        $input.val('');
                        window.resetPosSearchDropdownPosition($resultBoxBill);
                        $resultBoxBill.hide().empty();
                    }
                } catch (err) {
                    console.error("Bill search barcode auto-add error", err);
                }
            });

            $(document).on('focus', '.pos-bill-search-input', function() {
                const $input = $(this);
                const $ownResults = window.getPosSearchResultsForInput($input);
                $('.pos-bill-search-results').not($ownResults).each(function() {
                    window.resetPosSearchDropdownPosition($(this));
                    $(this).hide();
                });
            });

            $(document).on("click", ".add-product", function() {
                const $btn = $(this);
                const productIdNum = $btn.data("id"); // number
                const productIdStr = String(productIdNum); // string key
                const productName = $btn.data("name");
                const productPrice = $btn.data("price");
                const productImage = $btn.data("image");
                const productStock = parseQuantityValue($btn.data("stock"));
                const categoryId = $btn.data("category");
                const isWarrantyCategory = isWarrantyCategoryProduct({
                    is_warranty_category: $btn.data("is-warranty-category"),
                    category_name: $btn.data("category-name")
                });
                const gstOptionItem = $btn.data("gst-option") || "without_gst";
                let productGst = $btn.attr("data-product-gst") || $btn.data("product-gst") || null;
                const purchasePrice = parseFloat($btn.data("purchase-price")) || 0;
                const isRentAvailable = Number($btn.data("rent") || 0);
                const rentPeriod = $btn.data("rent-period") || null;
                const rentPrice = $btn.data("rent-price") || null;
                let sellingPrices = [];
                try {
                    sellingPrices = JSON.parse(decodeURIComponent($btn.attr('data-selling-prices') || '%5B%5D'));
                    if (!Array.isArray(sellingPrices)) sellingPrices = [];
                } catch (e) {
                    sellingPrices = [];
                }

                if (productGst && productGst !== "null" && productGst !== "[]") {
                    if (typeof productGst === 'string') {
                        try {
                            productGst = JSON.parse(productGst.replace(/&#39;/g, "'"));
                        } catch (e) {
                            productGst = null;
                        }
                    }
                    if (!Array.isArray(productGst) || !productGst.length) {
                        productGst = null;
                    }
                } else {
                    productGst = null;
                }

                let resolvedGstOption = gstOptionItem;
                if (resolvedGstOption !== 'with_gst' && resolvedGstOption !== 'without_gst') {
                    resolvedGstOption = 'without_gst';
                }
                if (!productGst) {
                    resolvedGstOption = 'without_gst';
                }

                const regularPrice = $btn.data("regular-price") != null ? $btn.data("regular-price") : productPrice;
                const wholesalePriceAttr = $btn.data("wholesale-price");
                const wholesalePrice = wholesalePriceAttr !== '' && wholesalePriceAttr != null && !isNaN(parseFloat(wholesalePriceAttr))
                    ? parseFloat(wholesalePriceAttr)
                    : window.resolveWholesalePriceFromRates(sellingPrices, regularPrice);

                var productData = {
                    productIdStr: productIdStr,
                    productIdNum: productIdNum,
                    categoryId: categoryId,
                    productName: productName,
                    productPrice: productPrice,
                    regularPrice: regularPrice,
                    wholesalePrice: wholesalePrice,
                    purchasePrice: purchasePrice,
                    productImage: productImage,
                    productStock: productStock,
                    isWarrantyCategory: isWarrantyCategory,
                    gstOption: resolvedGstOption,
                    productGst: productGst,
                    $checkIcon: null,
                    isRentAvailable: isRentAvailable,
                    rentPeriod: rentPeriod,
                    rentPrice: rentPrice,
                    sellingPrices: sellingPrices
                };

                function addFromSearch(finalPrice, qty) {
                    productData.productPrice = finalPrice;
                    productData._priceResolved = true;
                    window.executeAddProduct(productData, null, [], (qty != null && qty > 0) ? qty : 1);
                    clearPosSearchContext($btn);
                }

                // Check if it's mobile category
                if (String(categoryId).toLowerCase().includes('mobile') || String(productName).toLowerCase()
                    .includes('mobile')) {
                    $('#imeiProductName').text(productName);
                    $('#imeiNumberInput').val('');
                    window.pendingImeiProduct = productData;
                    window.pendingImeiQty = 1;
                    $('#imeiModal').modal('show');
                    clearPosSearchContext($btn);
                    return;
                }

                if (isWholesaleModeEnabled()) {
                    const wholesaleProduct = {
                        id: productIdNum,
                        price: regularPrice,
                        quantity: productStock,
                        wholesale_price: wholesalePrice,
                        selling_prices: sellingPrices,
                        wholesale_rates: window.getWholesaleRatesFromSellingRates(sellingPrices, regularPrice)
                    };
                    pickWholesalePrice(wholesaleProduct, regularPrice).then(function(selections) {
                        if (!selections || !selections.length) {
                            return;
                        }
                        window.addPosProductRateSelections(productData, selections);
                        clearPosSearchContext($btn);
                    });
                    return;
                }

                const retailSearchPrices = typeof window.withPosRateAvailableQty === 'function'
                    ? window.withPosRateAvailableQty(
                        typeof window.getRetailSellingRatesForPicker === 'function'
                            ? window.getRetailSellingRatesForPicker(sellingPrices)
                            : sellingPrices,
                        productStock,
                        regularPrice
                    )
                    : sellingPrices;
                if (retailSearchPrices.length > 1 && typeof window.showPosSellingRatePicker === 'function') {
                    window.showPosSellingRatePicker(retailSearchPrices, 'Select Rate', {
                        productId: productIdNum
                    }).then(function(selections) {
                        if (!selections || !selections.length) return;
                        window.addPosProductRateSelections(productData, selections);
                        clearPosSearchContext($btn);
                    });
                    return;
                }

                addFromSearch(retailSearchPrices.length === 1 ? parseFloat(retailSearchPrices[0].price) : productPrice, 1);
            });
            // isAdvanceReceiptMode is already declared globally above

            $('.advanceReceiptToggle').on('change', function() {
                const isChecked = $(this).is(':checked');
                syncAdvanceReceiptMode(isChecked);
                if (isChecked) {
                    syncQuotationMode(false); // mutually exclusive
                }
            });

            $('.quotationToggle').on('change', function() {
                const isChecked = $(this).is(':checked');
                syncQuotationMode(isChecked);
                if (isChecked) {
                    syncAdvanceReceiptMode(false); // mutually exclusive
                }
            });

            function updateOrderNumberBadge() {
                var $badgeLabel = $('#posOrderNumberLabel');
                var $badgeValue = $('#posOrderNumberValue');
                if (!$badgeLabel.length || !$badgeValue.length) return;

                if (!window.posNextNumbers) return;

                if (isAdvanceReceiptMode) {
                    $badgeLabel.text('Advance Receipt No:');
                    $badgeValue.text(window.posNextNumbers.advance_receipt);
                } else if ($('.quotationToggle').is(':checked')) {
                    $badgeLabel.text('Quotation No:');
                    $badgeValue.text(window.posNextNumbers.quotation);
                } else if ($('.rentalToggle').is(':checked')) {
                    $badgeLabel.text('Rental No:');
                    $badgeValue.text(window.posNextNumbers.rental);
                } else {
                    $badgeLabel.text('Order No:');
                    $badgeValue.text(window.posNextNumbers.sale);
                }
            }
            window.updateOrderNumberBadge = updateOrderNumberBadge;

            function syncAdvanceReceiptMode(isChecked) {
                $('.advanceReceiptToggle').prop('checked', isChecked);
                isAdvanceReceiptMode = isChecked;
                $('#quotation_status').val(isChecked ? 'advance_receipt' : ($('.quotationToggle').is(':checked') ?
                    'quotation' : ($('.rentalToggle').is(':checked') ? 'rental' : 'sales')));
                if (typeof updatePosPageTitle === "function") {
                    updatePosPageTitle($('.quotationToggle').is(':checked'));
                }
                updateOrderNumberBadge();

                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('sale_type', isChecked ? 'advance_receipt' : ($('.quotationToggle').is(
                    ':checked') ? 'quotation' : ($('.rentalToggle').is(':checked') ? 'rental' :
                    'sales')));
                window.history.replaceState({}, '', currentUrl.toString());

                if (isChecked) {
                    $('#advanceReceiptFields').show();
                    $('#emiPaymentOption').hide();
                    $('.btn-totallabel h5').html(
                        'Generate Advance Receipt <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16" style="margin-bottom: 0.1rem;"><path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8" /></svg>'
                    );
                } else {
                    $('#advanceReceiptFields').hide();
                    if (window.enabledPaymentMethods && window.enabledPaymentMethods.includes('emi')) {
                        $('#emiPaymentOption').show();
                    }
                }

                if (typeof window.refreshPosPaymentUi === "function") {
                    window.refreshPosPaymentUi(true);
                }
            }

            function syncQuotationMode(isChecked) {
                $('.quotationToggle').prop('checked', isChecked);
                $('#quotation_status').val(isChecked ? 'quotation' : ($('.advanceReceiptToggle').is(':checked') ?
                    'advance_receipt' : ($('.rentalToggle').is(':checked') ? 'rental' : 'sales')));
                if (typeof updatePosPageTitle === "function") {
                    updatePosPageTitle(isChecked);
                }
                updateOrderNumberBadge();

                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('sale_type', isChecked ? 'quotation' : ($('.advanceReceiptToggle').is(
                    ':checked') ? 'advance_receipt' : ($('.rentalToggle').is(':checked') ? 'rental' :
                    'sales')));
                window.history.replaceState({}, '', currentUrl.toString());

                if (isChecked) {
                    $('#paymentSection').hide();
                    $("input[name='payment_method']").prop('checked', false);
                    $('.paymentmethod').removeClass('active');
                    if (typeof window.resetPosPaymentUi === "function") {
                        window.resetPosPaymentUi();
                    } else {
                        $('#posPaidTypeBox, #posPaidAmountFields, #cashOnlineBox, #bankSelectionBox').hide();
                    }
                    $('.setvaluecash').hide();
                    $('input[name="gst_option"][value="without"]').prop('checked', true).trigger('change');
                } else {
                    $('#paymentSection').show();
                    $('.setvaluecash').show();
                    if (typeof window.selectDefaultPaymentMethod === "function") {
                        window.selectDefaultPaymentMethod();
                    } else if (typeof window.refreshPosPaymentUi === "function") {
                        window.refreshPosPaymentUi(true);
                    }
                    $('input[name="gst_option"][value="without"]').prop('checked', true).trigger('change');
                }

                window.refreshProductAvailabilityUI();
            }

            // Initial Sync
            syncQuotationMode(new URLSearchParams(window.location.search).get('sale_type') === 'quotation' ||
                $('.quotationToggle:checked').length > 0);
            syncAdvanceReceiptMode(new URLSearchParams(window.location.search).get('sale_type') ===
                'advance_receipt' ||
                $('.advanceReceiptToggle:checked').length > 0);
        });
    </script>

    <script>
        (function() {
            var phoneQuery = window.matchMedia('(max-width: 991.98px)');
            var toolbar = document.getElementById('posActionToolbar');
            var moreBtn = document.getElementById('posToolbarMoreBtn');
            if (toolbar && moreBtn) {
                moreBtn.addEventListener('click', function() {
                    var open = !toolbar.classList.contains('is-details-open');
                    toolbar.classList.toggle('is-details-open', open);
                    moreBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
                });
            }

            var panel = document.getElementById('paymentPanel');
            var handle = document.getElementById('paymentPanelHandle');
            if (!panel) return;

            if (handle) {
                handle.addEventListener('click', function(e) {
                    if (!phoneQuery.matches) return;
                    if (e.target.closest && e.target.closest('#paymentPanelMinBtn')) return;
                    panel.classList.toggle('pos-pay-collapsed');
                });
            }

            function syncPayHeight() {
                var h = phoneQuery.matches ? Math.ceil(panel.getBoundingClientRect().height) : 0;
                document.documentElement.style.setProperty('--pos-pay-h', (h || 150) + 'px');
            }
            syncPayHeight();
            if (window.ResizeObserver) new ResizeObserver(syncPayHeight).observe(panel);
            window.addEventListener('resize', syncPayHeight);
        })();
    </script>

    <script>
        /* =====================================================
               Payment Panel — Drag Handle + Minimize/Restore
               ===================================================== */
        (function() {
            var panel = document.getElementById('paymentPanel');
            var handle = document.getElementById('paymentPanelHandle');
            var body = document.getElementById('paymentPanelBody');
            var minBtn = document.getElementById('paymentPanelMinBtn');
            var minIcon = document.getElementById('panelMinIcon');

            if (!panel || !handle) return;

            var isDragging = false;
            var startY = 0;
            var startTop = 0;
            var isCollapsed = false;

            /* ── Only apply dragging when the panel is position:fixed (mobile/tablet) ── */
            function isPanelFixed() {
                return getComputedStyle(panel).position === 'fixed';
            }

            /* ── Initialise top position ── */
            function initPanelTop() {
                if (!isPanelFixed()) return;
                if (!panel.style.top) {
                    var rect = panel.getBoundingClientRect();
                    panel.style.top = rect.top + 'px';
                    panel.style.bottom = 'auto';
                }
            }

            /* ── Mouse drag ── */
            handle.addEventListener('mousedown', function(e) {
                if (!isPanelFixed()) return;
                if (e.target === minBtn || minBtn.contains(e.target)) return;
                initPanelTop();
                isDragging = true;
                startY = e.clientY;
                startTop = parseInt(panel.style.top, 10) || panel.getBoundingClientRect().top;
                document.body.style.userSelect = 'none';
                e.preventDefault();
            });

            document.addEventListener('mousemove', function(e) {
                if (!isDragging) return;
                var delta = e.clientY - startY;
                var newTop = startTop + delta;
                // Clamp: don't drag above 60px or below (viewport - 60px)
                newTop = Math.max(60, Math.min(window.innerHeight - 60, newTop));
                panel.style.top = newTop + 'px';
                panel.style.bottom = 'auto';
            });

            document.addEventListener('mouseup', function() {
                if (isDragging) {
                    isDragging = false;
                    document.body.style.userSelect = '';
                }
            });

            /* ── Touch drag (mobile) ── */
            handle.addEventListener('touchstart', function(e) {
                if (!isPanelFixed()) return;
                if (e.target === minBtn || minBtn.contains(e.target)) return;
                initPanelTop();
                isDragging = true;
                startY = e.touches[0].clientY;
                startTop = parseInt(panel.style.top, 10) || panel.getBoundingClientRect().top;
            }, {
                passive: true
            });

            document.addEventListener('touchmove', function(e) {
                if (!isDragging) return;
                var delta = e.touches[0].clientY - startY;
                var newTop = startTop + delta;
                newTop = Math.max(60, Math.min(window.innerHeight - 60, newTop));
                panel.style.top = newTop + 'px';
                panel.style.bottom = 'auto';
            }, {
                passive: true
            });

            document.addEventListener('touchend', function() {
                isDragging = false;
            });

            /* ── Minimize / Restore button ── */
            var expandIcon =
                '<path d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z"/>';
            var collapseIcon =
                '<path d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>';

            if (minBtn) {
                minBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    isCollapsed = !isCollapsed;
                    if (isCollapsed) {
                        body.style.maxHeight = body.scrollHeight + 'px';
                        requestAnimationFrame(function() {
                            body.classList.add('panel-collapsed');
                        });
                        minIcon.innerHTML = collapseIcon;
                    } else {
                        body.classList.remove('panel-collapsed');
                        body.style.maxHeight = body.scrollHeight + 'px';
                        setTimeout(function() {
                            body.style.maxHeight = '';
                        }, 320);
                        minIcon.innerHTML = expandIcon;
                    }
                });
            }
        })();
    </script>

    <script>
        $(document).ready(function() {
            const convertOrder = @json($convertOrder ?? null);
            const isPosEdit = @json($isPosEdit ?? false);

            if (convertOrder) {
                if (isPosEdit) {
                    $('.page-title').text('Edit: ' + (convertOrder.order_number || ''));
                } else {
                // Update UI title to indicate we are converting a sale
                $('.page-title').text('Convert Sale: ' + (convertOrder.order_number || ''));

                // Force the quotationStatus to be "sales"
                if (typeof syncQuotationMode === 'function') {
                    syncQuotationMode(false);
                    syncAdvanceReceiptMode(false);
                }
                $('#quotation_status').val('sales');
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('sale_type', 'sales');
                window.history.replaceState({}, '', currentUrl.toString());
                }

                if (convertOrder.gst_option === 'with' || convertOrder.gst_option === 'with_gst') {
                    $('#with_gst').prop('checked', true).trigger('change');
                } else {
                    $('#without_gst').prop('checked', true).trigger('change');
                }
                if (convertOrder.remarks) {
                    $('#remarks').val(convertOrder.remarks);
                    $('#remarksModalTextarea').val(convertOrder.remarks);
                    if (typeof window.syncPosRemarksButtons === 'function') {
                        window.syncPosRemarksButtons();
                    }
                }
                if (convertOrder.shipping) {
                    $('#shipping').val(convertOrder.shipping);
                    if (typeof window.setPosShippingVisible === 'function') {
                        window.setPosShippingVisible(true);
                    }
                }
                const convertBillDiscount = parseFloat(convertOrder.discount || 0);
                if (convertBillDiscount > 0) {
                    $('#additional_discount_percent').val(convertBillDiscount.toFixed(2));
                    if (typeof window.setPosAdditionalDiscountVisible === 'function') {
                        window.setPosAdditionalDiscountVisible(true);
                    }
                }
                if (convertOrder.deposit_amount) {
                    $('#deposit_amount').val(convertOrder.deposit_amount);
                }
                if (convertOrder.tds_percentage) {
                    $('#tds_percentage').val(convertOrder.tds_percentage);
                }
                if (convertOrder.order_type && $('#order_type').length) {
                    $('#order_type').val(convertOrder.order_type).trigger('change');
                }
                if (convertOrder.exchange_details || convertOrder.exchange_material_id) {
                    let exchangeRows = [];
                    const details = convertOrder.exchange_details;
                    if (details && Array.isArray(details.items) && details.items.length) {
                        exchangeRows = details.items;
                    } else if (Array.isArray(details) && details.length) {
                        exchangeRows = details;
                    } else if (convertOrder.exchange_material_id) {
                        exchangeRows = [{
                            material_id: convertOrder.exchange_material_id,
                            type_id: convertOrder.exchange_type_id || (details && details.type_id) || null,
                            price: convertOrder.exchange_price || (details && details.price) || '',
                            weight: convertOrder.exchange_weight || (details && details.weight) || '1.000',
                            amount: convertOrder.exchange_amount || (details && details.amount) || ''
                        }];
                    }
                    if (exchangeRows.length && typeof window.setPosExchangeRows === 'function') {
                        window.setPosExchangeRows(exchangeRows);
                    }
                    $('#exchange_charges_wrapper').css('display', 'block');
                    $('#toggle_exchange_charges i').removeClass('fa-plus').addClass('fa-minus');
                }

                // Prefill customer and assigned staff
                if (convertOrder.user_id) {
                    setTimeout(() => {
                        $('#customer_name').val(convertOrder.user_id).trigger('change');
                        const phone = (convertOrder.user && convertOrder.user.phone) ? convertOrder.user.phone : (convertOrder.customer_phone || '');
                        if (phone) {
                            $('#customer_phone').val(phone);
                        }
                        if (typeof window.refreshActivePosBillTab === 'function') {
                            window.refreshActivePosBillTab();
                        }
                    }, 300);
                }
                if (convertOrder.staff_id) {
                    setTimeout(() => {
                        $('#assigned_staff_id').val(convertOrder.staff_id).trigger('change');
                    }, 300);
                }

                // Load products into the cart using each line's saved quantity (not default 1)
                if (convertOrder.order_items && Array.isArray(convertOrder.order_items)) {
                    (async function loadPosEditOrderItems() {
                        const cartApi = function() {
                            return (typeof window.getPosSelectedItems === 'function')
                                ? window.getPosSelectedItems()
                                : (typeof selectedItems !== 'undefined' ? selectedItems : null);
                        };
                        for (const item of convertOrder.order_items) {
                            if (!item.product) {
                                continue;
                            }
                            const product = {
                                ...item.product
                            };
                            product._ratePicked = true;
                            product.price = item.price;
                            product.sale_price = item.price;
                            product.discount_percentage = item.discount_percentage;
                            product._pendingImei = item.imei_no || null;
                            const lineQty = window.parseQuantityValue
                                ? window.parseQuantityValue(item.quantity)
                                : Number(item.quantity);
                            await addProductToCart(product, lineQty > 0 ? lineQty : 1);

                            const cart = cartApi();
                            const cartId = window.lastScannedCartId;
                            if (cart && cartId && cart.has(cartId)) {
                                const cartItem = cart.get(cartId);
                                if (item.discount_percentage != null) {
                                    cartItem.discount_percentage = Number(item.discount_percentage) || 0;
                                }
                                if (item.discount_amount != null) {
                                    cartItem.discount_amount = Number(item.discount_amount) || 0;
                                }
                                if (item.imei_no) {
                                    cartItem.imei_no = item.imei_no;
                                }
                                cartItem.is_locked = !isPosEdit;
                                cart.set(cartId, cartItem);
                            }
                        }
                        if (typeof window.updateTotalItems === 'function') {
                            window.updateTotalItems();
                        } else if (typeof updateTotalItems === 'function') {
                            updateTotalItems();
                        }
                        if (typeof window.refreshActivePosBillTab === 'function') {
                            window.refreshActivePosBillTab();
                        }
                    })();
                }
            }
        });
    </script>

    {{-- ===== DECLARE GLOBAL SAVED REMARK VARIABLE ===== --}}
    <script>
        var savedRemark = ''; // Global variable accessible to all IIFEs
    </script>

    {{-- ===== QUICK ADD CUSTOMER MODAL JS ===== --}}
    <script>
        (function() {
            var authToken = localStorage.getItem('authToken');

            var qacModalEl = document.getElementById('quickAddCustomerModal');
            var qacModal = qacModalEl && typeof bootstrap !== 'undefined' ?
                new bootstrap.Modal(qacModalEl) :
                null;

            var stateCodeToName = {
                "01": "Jammu and Kashmir",
                "02": "Himachal Pradesh",
                "03": "Punjab",
                "04": "Chandigarh",
                "05": "Uttarakhand",
                "06": "Haryana",
                "07": "Delhi",
                "08": "Rajasthan",
                "09": "Uttar Pradesh",
                "10": "Bihar",
                "11": "Sikkim",
                "12": "Arunachal Pradesh",
                "13": "Nagaland",
                "14": "Manipur",
                "15": "Mizoram",
                "16": "Tripura",
                "17": "Meghalaya",
                "18": "Assam",
                "19": "West Bengal",
                "20": "Jharkhand",
                "21": "Odisha",
                "22": "Chhattisgarh",
                "23": "Madhya Pradesh",
                "24": "Gujarat",
                "27": "Maharashtra",
                "29": "Karnataka",
                "33": "Tamil Nadu",
                "36": "Telangana"
            };

            var stateNameToCode = {
                "Jammu and Kashmir": "01",
                "Himachal Pradesh": "02",
                "Punjab": "03",
                "Chandigarh": "04",
                "Uttarakhand": "05",
                "Haryana": "06",
                "Delhi": "07",
                "Rajasthan": "08",
                "Uttar Pradesh": "09",
                "Bihar": "10",
                "Sikkim": "11",
                "Arunachal Pradesh": "12",
                "Nagaland": "13",
                "Manipur": "14",
                "Mizoram": "15",
                "Tripura": "16",
                "Meghalaya": "17",
                "Assam": "18",
                "West Bengal": "19",
                "Jharkhand": "20",
                "Odisha": "21",
                "Chhattisgarh": "22",
                "Madhya Pradesh": "23",
                "Gujarat": "24",
                "Maharashtra": "27",
                "Karnataka": "29",
                "Tamil Nadu": "33",
                "Telangana": "36"
            };

            /* ── Helpers ── */
            function resetQacForm() {
                $('#quickAddCustomerForm')[0].reset();
                $('#qac_edit_id').val('');
                $('#qac_customer_code').val('');
                $('.qac-error').html('');
                $('#qac-gst-msg').html('');
                $('#qac-gst-loader').hide();
                $('#qac_use_same_address').prop('checked', false);
                $('#qac_face_preview').hide().attr('src', '');
                $('#qac_face_icon').show();
                window.lastScannedFaceEmbedding = null;
                window.lastScannedFaceDataUrl = null;
            }

            function setQacMode(mode) { // 'add' | 'edit'
                var isEdit = mode === 'edit';
                $('#quickAddCustomerModalLabel').text(isEdit ? 'Edit Customer' : 'Add New Customer');
                $('#qacSaveBtnText').text(isEdit ? 'Update Customer' : 'Save Customer');
            }

            function fillQacForm(c) {
                $('#qac_customer_code').val(c.customer_code || '');
                $('#qac_customer_name').val(c.name || '');
                $('#qac_company_name').val(c.company_name || '');
                $('#qac_phone').val(c.phone || '');
                $('#qac_alternate_phone').val(c.alternate_phone || '');
                $('#qac_email').val(c.email || '');
                $('#qac_gst_number').val(c.gst_number || '');
                $('#qac_pan_number').val(c.pan_number || '');
                $('#qac_state_code').val(c.state_code || '');
                $('#qac_state_name').val(c.state_name || stateCodeToName[c.state_code] || '');
                $('#qac_country').val(c.country || (c.details && c.details.country) || '');
                $('#qac_city').val(c.city || (c.details && c.details.city) || '');
                $('#qac_address').val(c.address || (c.details && c.details.address) || '');
                $('#qac_delivery_address').val(c.delivery_address || (c.details && c.details.delivery_address) || '');

                var addressVal = $('#qac_address').val();
                var deliveryAddressVal = $('#qac_delivery_address').val();
                if (addressVal && addressVal === deliveryAddressVal) {
                    $('#qac_use_same_address').prop('checked', true);
                } else {
                    $('#qac_use_same_address').prop('checked', false);
                }
            }

            /* ── Show/hide edit pencil button ── */


            // function syncEditBtn() {

            //     if (savedRemark.trim() !== '') {
            //         $('#openAddRemarksBtn').hide();
            //         $('#openEditRemarksBtn').css('display', 'inline-flex');
            //     } else {
            //         $('#openAddRemarksBtn').css('display', 'inline-flex');
            //         $('#openEditRemarksBtn').hide();
            //     }

            // }

            /* ── Open ADD modal ── */
            $(document).on('click', '#openQuickAddCustomerBtn', function() {
                resetQacForm();
                setQacMode('add');
                if (qacModal) qacModal.show();

                // Fetch next customer code
                $.get('/api/next-customer-code', function(res) {
                    if (res.status && res.customer_code) {
                        $('#qac_customer_code').val(res.customer_code);
                    }
                });
            });

            /* ── Open EDIT modal ── */
            $(document).on('click', '#openEditCustomerBtn', function() {
                var customerId = $('#customer_name').val();
                if (!customerId || !/^\d+$/.test(String(customerId))) return;

                resetQacForm();
                setQacMode('edit');
                $('#qac_edit_id').val(customerId);

                // Show a loading state in the modal body
                if (qacModal) qacModal.show();
                $('#qacSaveBtn').prop('disabled', true);
                $('#qacBtnSpinner').removeClass('d-none');

                $.ajax({
                    url: '/api/getCustomer/' + customerId,
                    method: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Authorization': 'Bearer ' + authToken
                    },
                    success: function(res) {
                        if (res.status && res.customer) {
                            var c = res.customer;
                            // Merge details sub-object into top level for convenience
                            if (c.details) {
                                c.country = c.country || c.details.country;
                                c.city = c.city || c.details.city;
                                c.address = c.address || c.details.address;
                                c.delivery_address = c.delivery_address || c.details
                                    .delivery_address;
                            }
                            fillQacForm(c);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Could not load customer details.'
                            });
                            if (qacModal) qacModal.hide();
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to fetch customer details.'
                        });
                        if (qacModal) qacModal.hide();
                    },
                    complete: function() {
                        $('#qacSaveBtn').prop('disabled', false);
                        $('#qacBtnSpinner').addClass('d-none');
                    }
                });
            });

            /* ── State code → state name ── */
            $(document).on('input', '#qac_state_code', function() {
                var raw = this.value.replace(/[^0-9]/g, '').substring(0, 3);
                this.value = raw;
                $('#qac_state_name').val(stateCodeToName[raw] || '');
            });

            /* ── GST lookup ── */
            $(document).on('input', '#qac_gst_number', function() {
                var raw = $(this).val().toUpperCase().replace(/[^0-9A-Z]/g, '').substring(0, 15);
                $(this).val(raw);
                $('#qac-gst-msg').html('');
                if (raw.length < 15) return;

                var $loader = $('#qac-gst-loader');
                var $msg = $('#qac-gst-msg');

                $loader.show();
                $(this).prop('readonly', true);
                $msg.html(
                    '<span style="color:#1B2850;"><i class="fas fa-spinner fa-spin"></i> Fetching GST details...</span>'
                );

                $.ajax({
                    url: '/api/fetch-gst-details',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        gst_number: raw
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Authorization': 'Bearer ' + authToken
                    },
                    success: function(res) {
                        $msg.html('');
                        if (res.error) {
                            $msg.html('<span class="text-danger">GST details not found.</span>');
                            return;
                        }
                        var stateName = res.state || '';
                        var stateCode = stateNameToCode[stateName] || '';
                        if (stateCode) $('#qac_state_code').val(stateCode);
                        if (stateName) $('#qac_state_name').val(stateName);
                        if (res.legal_name) $('#qac_customer_name').val(res.legal_name);
                        if (res.company_name) $('#qac_company_name').val(res.company_name);
                        if (res.primary_address) $('#qac_address').val(res.primary_address);
                        if (res.city) $('#qac_city').val(res.city);
                        if (res.country) $('#qac_country').val(res.country);
                        if (raw.length >= 12) $('#qac_pan_number').val(raw.substring(2, 12));
                        $msg.html(
                            '<span style="color:#28a745;"><i class="fas fa-check-circle"></i> Details fetched successfully.</span>'
                        );
                    },
                    error: function() {
                        $('#qac-gst-msg').html(
                            '<span class="text-danger">Failed to fetch GST details.</span>');
                    },
                    complete: function() {
                        $loader.hide();
                        $('#qac_gst_number').prop('readonly', false);
                    }
                });
            });

            /* ── Phone digits only ── */
            /*
            $(document).on('input', '#qac_phone', function() {
                this.value = this.value.replace(/\D/g, '').substring(0, 10);
            });
            $(document).on('input', '#qac_alternate_phone', function() {
                this.value = this.value.replace(/\D/g, '').substring(0, 10);
            });
            */

            /* ── Form submit (Add or Edit) ── */
            $(document).on('submit', '#quickAddCustomerForm', function(e) {
                e.preventDefault();
                $('.qac-error').html('');

                var editId = $('#qac_edit_id').val();
                var isEdit = !!editId;

                var $btn = $('#qacSaveBtn');
                var $spinner = $('#qacBtnSpinner');
                $btn.prop('disabled', true);
                $spinner.removeClass('d-none');

                var formData = new FormData();
                formData.append('customer_code', $('#qac_customer_code').val().trim());
                formData.append('customer_name', $('#qac_customer_name').val().trim());
                formData.append('company_name', $('#qac_company_name').val().trim());
                formData.append('phone', $('#qac_phone').val().trim());
                formData.append('alternate_phone', $('#qac_alternate_phone').val().trim());
                formData.append('email', $('#qac_email').val().trim());
                formData.append('gst_number', $('#qac_gst_number').val().trim());
                formData.append('pan_number', $('#qac_pan_number').val().trim());
                formData.append('state_code', $('#qac_state_code').val().trim());
                formData.append('state_name', $('#qac_state_name').val().trim());
                formData.append('country', $('#qac_country').val().trim());
                formData.append('city', $('#qac_city').val().trim());
                formData.append('address', $('#qac_address').val().trim());
                formData.append('delivery_address', $('#qac_delivery_address').val().trim());

                var sid = localStorage.getItem('selectedSubAdminId');
                if (sid && sid !== 'null' && sid !== 'undefined') {
                    formData.append('selectedSubAdminId', sid);
                }

                if (isEdit) {
                    formData.append('_method', 'POST'); // updateCustomer uses POST
                }

                var apiUrl = isEdit ?
                    '/api/updateCustomer/' + editId :
                    '/api/createCustomer';

                $.ajax({
                    url: apiUrl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Authorization': 'Bearer ' + authToken
                    },
                    success: function(response) {
                        if (response.status) {
                            var name = $('#qac_customer_name').val().trim();
                            var company = $('#qac_company_name').val().trim();
                            var phone = $('#qac_phone').val().trim();
                            var gst = $('#qac_gst_number').val().trim();
                            var label = (company || name) + (phone ? ' - ' + phone : '');

                            var $select = $('#customer_name');

                            if (isEdit) {
                                // 1. Update the <option> in the DOM
                                var $existing = $select.find('option[value="' + editId + '"]');
                                if ($existing.length) {
                                    $existing.text(label)
                                        .attr('data-phone', phone)
                                        .attr('data-gst', gst);
                                } else {
                                    // Option not in DOM (rare) — add it
                                    $select.append(
                                        $('<option>', {
                                            value: editId,
                                            text: label
                                        })
                                        .attr('data-phone', phone)
                                        .attr('data-gst', gst)
                                    );
                                }

                                // 2. Destroy & reinit select2 so it re-reads the DOM options,
                                //    then re-select the updated customer
                                var select2Options = $select.data('select2') && $select.data(
                                        'select2').options ?
                                    $select.data('select2').options.options :
                                    null;

                                if ($select.data('select2')) {
                                    $select.select2('destroy');
                                }

                                // Reinitialise with the same config used at page-load
                                $select.select2({
                                    tags: true,
                                    width: '100%',
                                    createTag: function(params) {
                                        var term = $.trim(params.term);
                                        if (!term) return null;
                                        return {
                                            id: term,
                                            text: term,
                                            newTag: true
                                        };
                                    },
                                    templateResult: function(data) {
                                        if (data.newTag) return $(
                                            '<span style="color:white;">' + data
                                            .text + '</span>');
                                        return data.text;
                                    },
                                    matcher: function(params, data) {
                                        if ($.trim(params.term) === '') return data;
                                        if (typeof data.text === 'undefined')
                                            return null;
                                        var term = params.term.toLowerCase();
                                        var text = (data.text || '').toLowerCase();
                                        var phone = ($(data.element).data('phone') ||
                                            '').toString().toLowerCase();
                                        var normTerm = term.replace(/[^0-9a-z]/g, '');
                                        var normPhone = phone.replace(/[^0-9]/g, '');
                                        if (text.indexOf(term) > -1 || phone.indexOf(
                                                term) > -1) return data;
                                        if (normTerm && normPhone.indexOf(normTerm) > -
                                            1) return data;
                                        return null;
                                    }
                                });

                                $select.val(editId).trigger('change');

                            } else {
                                var realId = response.customer_id || response.id;
                                $select.find('option[value="' + realId + '"]').remove();
                                var $opt = $('<option>', {
                                        value: realId,
                                        text: label
                                    })
                                    .attr('data-phone', phone)
                                    .attr('data-gst', gst);
                                $select.append($opt);
                                $select.val(realId).trigger('change');
                                if ($select.data('select2')) $select.trigger('change.select2');
                            }

                            // Save face embedding if we scanned one for this customer
                            var targetCustomerId = isEdit ? editId : (response.customer_id || response.id);
                            if (window.lastScannedFaceEmbedding && targetCustomerId) {
                                $.ajax({
                                    url: '/api/customers/face-register',
                                    method: 'POST',
                                    data: { 
                                        customer_id: targetCustomerId,
                                        embedding: window.lastScannedFaceEmbedding,
                                        image_data: window.lastScannedFaceDataUrl
                                    },
                                    headers: {
                                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                                    },
                                    success: function() {
                                        window.lastScannedFaceEmbedding = null;
                                        window.lastScannedFaceDataUrl = null;
                                    }
                                });
                            }

                            // Sync phone & GST fields
                            $('#customer_phone').val(phone);
                            if (gst) $('#pos_gst_number').val(gst);

                            if (qacModal) qacModal.hide();
                            syncEditBtn();

                            Swal.fire({
                                icon: 'success',
                                title: isEdit ? 'Customer Updated' : 'Customer Added',
                                text: name + (isEdit ? ' updated' : ' added') +
                                    ' successfully.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Operation failed.'
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = (xhr.responseJSON || {}).errors || {};
                            $.each(errors, function(key, msgs) {
                                $('#qac_error_' + key).html(msgs[0]);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: (xhr.responseJSON || {}).message ||
                                    'Something went wrong.'
                            });
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                        $spinner.addClass('d-none');
                    }
                });
            });
            $(document).ready(function() {

                function toggleRemarkEdit() {
                    if ($('#remarks').val().trim() !== '') {
                        $('#edit_remark_btn').show();
                    } else {
                        $('#edit_remark_btn').hide();
                    }
                }

                toggleRemarkEdit();

                $('#remarks').on('keyup change', function() {
                    toggleRemarkEdit();
                });

            });

            // Sync QAC Address to Delivery Address
            $('#qac_use_same_address').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#qac_delivery_address').val($('#qac_address').val());
                }
            });

            $('#qac_address').on('input', function() {
                if ($('#qac_use_same_address').is(':checked')) {
                    $('#qac_delivery_address').val($(this).val());
                }
            });

            $('#qac_delivery_address').on('input', function() {
                if ($('#qac_use_same_address').is(':checked') && $(this).val() !== $('#qac_address').val()) {
                    $('#qac_use_same_address').prop('checked', false);
                }
            });

            // Payment panel width sync
            $(document).ready(function() {
                function updatePaymentPanelWidth() {
                    const panel = document.getElementById('paymentPanel');
                    const col = document.querySelector('#pos-right-panel .pos-bill-col');
                    if (panel) {
                        panel.style.position = '';
                        panel.style.left = '';
                        panel.style.width = '';
                        panel.style.maxWidth = '';
                        panel.style.right = '';
                        panel.style.top = '';
                        panel.style.bottom = '';
                    }
                    if (col) {
                        col.style.paddingBottom = '';
                    }
                }
                window.updatePaymentPanelWidth = updatePaymentPanelWidth;
                updatePaymentPanelWidth();
                setTimeout(updatePaymentPanelWidth, 200);
                setTimeout(updatePaymentPanelWidth, 800);

                // Adjust on window resize to remain responsive
                $(window).resize(function() {
                        updatePaymentPanelWidth();
                });
            });
        })();
    </script>
    {{-- ===== END QUICK ADD CUSTOMER MODAL JS ===== --}}

    {{-- ===== RENTAL DATES & AVAILABILITY JS ===== --}}
    <script>
        (function() {
            window.checkRentalAvailabilityOnServer = function(productId, rentStart, rentEnd, quantity) {
                return $.ajax({
                    url: '/api/check-rental-availability',
                    type: 'POST',
                    data: {
                        product_id: productId,
                        rent_start_at: rentStart,
                        rent_end_at: rentEnd,
                        quantity: quantity
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Authorization': typeof authToken !== 'undefined' ? 'Bearer ' + authToken : ''
                    }
                }).then(response => {
                    return response.available;
                }).catch(error => {
                    console.error("Availability check failed:", error);
                    return false;
                });
            };

            window.initRentalDatePickers = function() {
                $(".rent-daterange").each(function() {
                    const $input = $(this);
                    const itemId = String($input.attr("data-id"));
                    const item = selectedItems.get(itemId);

                    if (!item) return;

                    const period = item.rent_period || 'day';

                    if ($input.data("daterangepicker")) {
                        $input.data("daterangepicker").remove();
                    }

                    // Set default start and end dates
                    let defaultStart = moment();
                    let defaultEnd = moment();

                    // Set end date based on period
                    if (period === 'hour') {
                        defaultEnd = moment().add(1, 'hour');
                    } else if (period === 'day') {
                        defaultEnd = moment().add(1, 'day');
                    } else if (period === 'month') {
                        defaultEnd = moment().add(1, 'month');
                    }

                    // If item already has dates, use them
                    if (item.rent_start_at && item.rent_end_at) {
                        defaultStart = moment(item.rent_start_at);
                        defaultEnd = moment(item.rent_end_at);
                    }

                    function isDateDisabled(date, period) {
                        const now = moment();
                        if (period === 'hour') {
                            if (date.isBefore(now, 'day')) {
                                return true;
                            } else if (date.isSame(now, 'day')) {
                                return date.isBefore(now, 'hour');
                            }
                            return false;
                        } else if (period === 'day' || period === 'month') {
                            return date.isBefore(now, 'day');
                        }
                        return date.isBefore(now, 'day');
                    }

                    $input.daterangepicker({
                        timePicker: true,
                        timePicker24Hour: false,
                        timePickerIncrement: period === 'hour' ? 30 : 60,
                        autoUpdateInput: false,
                        startDate: defaultStart,
                        endDate: defaultEnd,
                        opens: 'left',
                        drops: 'auto',
                        isInvalidDate: function(date) {
                            return isDateDisabled(date, period);
                        },
                        locale: {
                            format: "DD/MM hh:mm A",
                            cancelLabel: "Clear",
                            applyLabel: "Select"
                        }
                    });

                    $input.on("apply.daterangepicker", function(ev, picker) {
                        const itemId = String($(this).attr("data-id"));
                        const item = selectedItems.get(itemId);
                        if (!item) return;

                        const period = item.rent_period || 'day';
                        let start = picker.startDate;
                        let end = picker.endDate;
                        const errorEl = $(`.rent-error[data-id="${itemId}"]`);

                        if (end.isSameOrBefore(start)) {
                            end = start.clone().add(1, 'minute');
                            picker.setEndDate(end);
                        }

                        let isValid = true;
                        let errorMessage = '';
                        const durationHours = end.diff(start, 'hours', true);
                        const durationDays = end.diff(start, 'days', true);

                        if (period === 'hour') {
                            if (durationHours < 1) {
                                end = start.clone().add(1, 'hour');
                                picker.setEndDate(end);
                            } else if (durationHours > 24) {
                                errorMessage = 'Maximum rental period is 24 hours for hourly rate';
                                isValid = false;
                            }
                        } else if (period === 'day') {
                            const durationMinutesTotal = end.diff(start, 'minutes', true);
                            const durationHours = durationMinutesTotal / 60;
                            const startTime = start.format('HH:mm');
                            const endTime = end.format('HH:mm');

                            if (durationHours < 24) {
                                errorMessage =
                                    'Daily rental requires minimum 24 hours (1 full day)';
                                isValid = false;
                            } else if (startTime !== endTime) {
                                const fullDays = Math.floor(durationHours / 24);
                                const remainderMinutes = durationMinutesTotal % (24 * 60);

                                if (remainderMinutes !== 0) {
                                    errorMessage =
                                        `For daily rental, times should match. Adjusting to ${startTime} to ${startTime}`;
                                    end = start.clone().add(fullDays, 'days');
                                    if (durationHours % 24 > 0) {
                                        end = end.add(1, 'day');
                                    }
                                    end.hour(start.hour()).minute(start.minute()).second(start
                                        .second());
                                    picker.setEndDate(end);
                                }
                            }
                        } else if (period === 'month') {
                            if (durationDays < 30) {
                                errorMessage = 'Monthly rental requires minimum 30 days';
                                isValid = false;
                            } else if (start.date() !== end.date()) {
                                errorMessage = 'Adjusting to same day of month for monthly rental';
                                end = start.clone().add(Math.ceil(durationDays / 30), 'months');
                                picker.setEndDate(end);
                            }
                        }

                        if (!isValid) {
                            errorEl.html(
                                `<i class="fas fa-exclamation-triangle me-1"></i> ${errorMessage}`
                            ).addClass('show').show();
                        } else {
                            if (errorMessage) {
                                errorEl.html(
                                        `<i class="fas fa-info-circle me-1"></i> ${errorMessage}`)
                                    .addClass('show').css('color', '#17a2b8').show();
                            } else {
                                errorEl.removeClass('show').text("").hide();
                            }

                            item.rent_start_at = start.format("YYYY-MM-DD HH:mm:ss");
                            item.rent_end_at = end.format("YYYY-MM-DD HH:mm:ss");

                            let duration;
                            switch (period) {
                                case "hour":
                                    duration = Math.ceil(end.diff(start, "hours", true));
                                    break;
                                case "day":
                                    duration = Math.ceil(end.diff(start, "days", true));
                                    break;
                                case "month":
                                    duration = Math.max(1, Math.ceil(end.diff(start, "months",
                                        true)));
                                    break;
                            }

                            item.rent_value = duration;
                            selectedItems.set(itemId, item);

                            if (!errorMessage) {
                                $(this).val(start.format("DD/MM hh:mm A") + " - " + end.format(
                                    "DD/MM hh:mm A"));
                            }

                            checkRentalAvailabilityOnServer(item.productId || item.id, item
                                    .rent_start_at, item.rent_end_at, item.quantity)
                                .then(isAvailable => {
                                    if (!isAvailable) {
                                        const $errorDiv = $(`.rent-error[data-id="${itemId}"]`);
                                        $errorDiv.html(
                                            '<i class="fas fa-times-circle me-1"></i> Not enough stock for selected dates. Please reduce quantity.'
                                        ).show();
                                    } else {
                                        $(`.rent-error[data-id="${itemId}"]`).hide();
                                    }
                                });

                            updateTotalItems();
                        }
                    });

                    $input.on("cancel.daterangepicker", function() {
                        const itemId = String($(this).attr("data-id"));
                        const item = selectedItems.get(itemId);
                        if (!item) return;

                        item.rent_start_at = null;
                        item.rent_end_at = null;
                        item.rent_value = 1;

                        selectedItems.set(itemId, item);
                        $(this).val("");

                        $(`.rent-error[data-id="${itemId}"]`).removeClass('show').text("").hide();
                        updateTotalItems();
                    });
                });
            };

            $(document).on('change', '.rent-input', function() {
                const itemId = String($(this).attr('data-id'));
                const newDuration = parseInt($(this).val()) || 1;

                const item = selectedItems.get(itemId);
                if (item) {
                    item.rent_value = newDuration;
                    selectedItems.set(itemId, item);

                    // If dates exist, we should adjust end date based on new duration
                    if (!item.rent_start_at) {
                        item.rent_start_at = moment().format("YYYY-MM-DD HH:mm:ss");
                    }
                    if (item.rent_start_at) {
                        const start = moment(item.rent_start_at);
                        let end = start.clone();

                        if (item.rent_period === 'hour') {
                            end.add(newDuration, 'hours');
                        } else if (item.rent_period === 'day') {
                            end.add(newDuration, 'days');
                        } else if (item.rent_period === 'week') {
                            end.add(newDuration, 'weeks');
                        } else if (item.rent_period === 'month') {
                            end.add(newDuration, 'months');
                        } else if (item.rent_period === 'year') {
                            end.add(newDuration, 'years');
                        }

                        item.rent_end_at = end.format("YYYY-MM-DD HH:mm:ss");
                        selectedItems.set(itemId, item);
                    }
                    updateTotalItems();
                }
            });

            // Re-init pickers when cart renders
            const originalRenderSelectedItems = window.renderSelectedItems;
            window.renderSelectedItems = function() {
                originalRenderSelectedItems();
                if (typeof window.initRentalDatePickers === 'function') {
                    window.initRentalDatePickers();
                }
            };

        })();
    </script>

    {{-- ===== REMARKS MODAL JS ===== --}}
    <script>
        (function() {
            var remarksModalEl = document.getElementById('remarksModal');
            var remarksModal = remarksModalEl && typeof bootstrap !== 'undefined' ?
                new bootstrap.Modal(remarksModalEl) :
                null;



            function syncEditBtn() {

                if (savedRemark.trim() !== '') {
                    $('#openAddRemarksBtn').hide();
                    $('#openEditRemarksBtn').css('display', 'inline-flex');
                } else {
                    $('#openAddRemarksBtn').css('display', 'inline-flex');
                    $('#openEditRemarksBtn').hide();
                }

            }

            window.syncEditBtn = syncEditBtn;

            /* ── Open ADD modal (+ button) ── */
            $(document).on('click', '#openAddRemarksBtn', function() {
                $('#remarksModalLabel').text('Add Remarks');
                $('#remarksModalTextarea').val(savedRemark); // pre-fill if exists
                if (remarksModal) remarksModal.show();
            });

            /* ── Open EDIT modal (pencil button) ── */
            $(document).on('click', '#openEditRemarksBtn', function() {
                $('#remarksModalLabel').text('Edit Remarks');
                $('#remarksModalTextarea').val(savedRemark);
                if (remarksModal) remarksModal.show();
            });

            /* ── Save button ── */
            /* ── Save button ── */
            $(document).on('click', '#saveRemarksBtn', function() {

                savedRemark = $('#remarksModalTextarea').val().trim();

                $('#remarks').val(savedRemark);

                syncEditBtn();

                $(this).blur();

                if (remarksModal) {
                    remarksModal.hide();
                }

            });

            /* ── Reset when POS form is cleared (e.g. after order placed) ── */
            // Hook into the existing clear flow by observing when selectedItems is cleared.
            // We expose a helper that the order-submit success handler can call.
            window.resetPosRemarks = function() {

                savedRemark = '';

                $('#remarks').val('');
                $('#remarksModalTextarea').val('');

                syncEditBtn();

            };

            window.resetPosAdditionalDiscount = function() {
                $('#additional_discount_percent').val('');
                $('#additional_discount_amount').val('');
                $('#bill_discount_percent').val('0');
                window._posAdditionalDiscountMode = 'percent';
                $('.additional-discount-summary-row').hide();
                $('#shipping').val('');
                if (typeof window.setPosAdditionalDiscountVisible === 'function') {
                    window.setPosAdditionalDiscountVisible(false);
                } else {
                    const discPanel = document.getElementById('additional_discount_wrapper');
                    if (discPanel) discPanel.style.display = 'none';
                    $('#toggle_additional_discount i').removeClass('fa-minus').addClass('fa-plus');
                }
                if (typeof window.setPosShippingVisible === 'function') {
                    window.setPosShippingVisible(false);
                } else {
                    const shipPanel = document.getElementById('shipping_cost_wrapper');
                    if (shipPanel) shipPanel.style.display = 'none';
                    $('#toggle_shipping_cost i').removeClass('fa-minus').addClass('fa-plus');
                }
                if (typeof window.calculateTotals === 'function') {
                    window.calculateTotals();
                }
            };

            // Initial state
            syncEditBtn();
        })();
    </script>
    {{-- ===== END REMARKS MODAL JS ===== --}}
@endpush

<style>
    .ep-modal .modal-dialog {
        max-width: 460px;
    }
    .ep-modal .modal-content {
        margin-top: 0 !important;
        border: 0;
        border-radius: 16px;
        overflow: hidden;
    }
    .ep-modal .modal-header {
        align-items: flex-start;
        padding: 16px 18px 12px;
        border-bottom: 1px solid #f0f1f4;
    }
    .ep-head .modal-title {
        font-size: 17px;
        font-weight: 700;
        color: #1b2850;
        margin: 0;
    }
    .ep-head small {
        display: block;
        font-size: 12px;
        color: #8b93a7;
        margin-top: 2px;
    }
    .ep-modal .modal-body {
        padding: 14px 18px 6px;
    }
    .ep-summary {
        background: #fff7ef;
        border: 1px solid #ffe2c4;
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 14px;
    }
    .ep-summary input {
        border: 0 !important;
        background: transparent !important;
        padding: 0 !important;
        box-shadow: none !important;
        outline: 0;
        width: 100%;
        min-width: 0;
        pointer-events: none;
        -moz-appearance: textfield;
    }
    .ep-summary input::-webkit-outer-spin-button,
    .ep-summary input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .ep-summary .ep-name {
        font-size: 15px;
        font-weight: 700;
        color: #1b2850;
        text-transform: uppercase;
        text-overflow: ellipsis;
        margin-bottom: 10px;
    }
    .ep-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
    }
    .ep-stat {
        background: #fff;
        border-radius: 10px;
        padding: 8px 10px;
        min-width: 0;
    }
    .ep-stat span {
        display: block;
        font-size: 11px;
        color: #8b93a7;
        margin-bottom: 2px;
    }
    .ep-stat input,
    .ep-stat-val em {
        font-size: 15px;
        font-weight: 700;
        font-style: normal;
        color: #1b2850;
    }
    .ep-stat-val {
        display: flex;
        align-items: baseline;
        gap: 1px;
    }
    .ep-stat.is-current input {
        color: #ff9f43;
    }
    .ep-stats.ep-stats-1 {
        grid-template-columns: 1fr;
    }
    .ep-branches {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
        align-items: end;
        gap: 8px;
        margin-bottom: 14px;
    }
    .ep-branch {
        min-width: 0;
    }
    .ep-branch .form-control,
    .ep-branch .form-select {
        height: 46px;
        border-radius: 10px;
        font-size: 14px;
        text-overflow: ellipsis;
    }
    .ep-branch .form-control[readonly] {
        background: #f9fafb;
        color: #344054;
        font-weight: 600;
    }
    .ep-branch-arrow {
        width: 32px;
        height: 46px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ff9f43;
    }
    .ep-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #344054;
        margin-bottom: 6px;
    }
    .ep-label small {
        font-weight: 400;
        color: #8b93a7;
    }
    .ep-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 14px;
    }
    .ep-actions input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .ep-action {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 46px;
        border-radius: 10px;
        border: 1.5px solid #e4e7ec;
        background: #fff;
        font-size: 14px;
        font-weight: 600;
        color: #475467;
        cursor: pointer;
        margin: 0;
        transition: all 0.15s;
    }
    .ep-action i {
        font-size: 12px;
    }
    #addStock:checked + .ep-action {
        border-color: #12a150;
        background: #e7f8ef;
        color: #12a150;
    }
    #minusStock:checked + .ep-action {
        border-color: #e5483d;
        background: #fdecec;
        color: #e5483d;
    }
    .ep-qty {
        margin-bottom: 14px;
    }
    .ep-stepper {
        display: flex;
        align-items: stretch;
        gap: 8px;
    }
    .ep-stepper input {
        flex: 1 1 auto;
        min-width: 0;
        height: 46px;
        text-align: center;
        font-size: 18px;
        font-weight: 700;
        border-radius: 10px;
    }
    .ep-step {
        flex: 0 0 46px;
        height: 46px;
        border-radius: 10px;
        border: 1.5px solid #e4e7ec;
        background: #f9fafb;
        font-size: 20px;
        font-weight: 600;
        color: #344054;
        line-height: 1;
    }
    .ep-preview {
        font-size: 12px;
        color: #12a150;
        margin-top: 6px;
        min-height: 16px;
    }
    .ep-preview.is-negative {
        color: #e5483d;
    }
    .ep-error {
        background: #fdecec;
        color: #e5483d;
        font-size: 13px;
        border-radius: 8px;
        padding: 8px 10px;
        margin-bottom: 12px;
    }
    .ep-modal textarea {
        border-radius: 10px;
        font-size: 14px;
        resize: none;
    }
    .ep-modal textarea::placeholder {
        text-transform: none;
    }
    .ep-modal .modal-footer {
        display: grid;
        grid-template-columns: 1fr 1.4fr;
        gap: 10px;
        padding: 12px 18px 16px;
        border-top: 0;
    }
    .ep-modal .modal-footer > * {
        margin: 0;
    }
    .ep-btn {
        min-height: 46px;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 600;
    }
    .ep-modal .btn-light {
        background: #f2f4f7;
        border-color: #f2f4f7;
        color: #344054;
    }
    :root[data-theme="dark"] .ep-summary {
        background: #0f172a;
        border-color: #334155;
    }
    :root[data-theme="dark"] .ep-stat,
    :root[data-theme="dark"] .ep-action,
    :root[data-theme="dark"] .ep-step {
        background: #1e293b;
        border-color: #334155;
        color: #e5e7eb;
    }
    :root[data-theme="dark"] .ep-head .modal-title,
    :root[data-theme="dark"] .ep-summary .ep-name,
    :root[data-theme="dark"] .ep-stat input,
    :root[data-theme="dark"] .ep-stat-val em,
    :root[data-theme="dark"] .ep-label {
        color: #e5e7eb;
    }

    @media (max-width: 575.98px) {
        .ep-modal.show {
            display: flex !important;
            align-items: flex-end;
        }
        .ep-modal .modal-dialog {
            margin: 0 !important;
            flex: 1 1 100%;
            max-width: none !important;
            width: 100% !important;
            min-height: 0;
            display: block;
        }
        .ep-modal.fade .modal-dialog {
            transform: translateY(100%);
        }
        .ep-modal.show .modal-dialog {
            transform: none;
        }
        .ep-modal .modal-content {
            width: 100% !important;
            max-width: none !important;
            border-radius: 18px 18px 0 0;
            max-height: 92dvh;
        }
        .ep-modal form {
            display: flex;
            flex-direction: column;
            min-height: 0;
        }
        .ep-modal .modal-body {
            overflow-y: auto;
            min-height: 0;
        }
        .ep-modal .modal-footer {
            padding-bottom: calc(14px + env(safe-area-inset-bottom, 0px));
        }
        body:has(.ep-modal.show) .mobile-bottom-nav,
        body:has(.ep-modal.show) .floating-calculator-wrapper {
            display: none !important;
        }
    }

    @media (max-width: 767.98px) {
        .content:has(.inv-m-head) {
            padding: 6px 6px 90px !important;
        }
        .content:has(.inv-m-head) > .page-header,
        .inv-tabs-row,
        .inventory-tab-nav.inv-tabs-row,
        .inv-main-card .table-responsive,
        .inv-main-card .table-scroll-top {
            display: none !important;
        }
        .inv-m-head {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, 0.08);
            padding: 12px 10px 10px;
            margin-bottom: 8px;
        }
        .inv-m-head-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 10px;
        }
        .inv-m-head-top h4 {
            font-size: 15px;
            font-weight: 700;
            color: #1b2850;
            margin: 0;
        }
        .inv-m-link {
            font-size: 13px;
            font-weight: 600;
            color: #1b2850;
            white-space: nowrap;
        }
        .inv-m-tabs {
            display: flex;
            gap: 6px;
        }
        .inv-m-tabs a {
            flex: 1 1 0;
            text-align: center;
            font-size: 12px;
            font-weight: 600;
            padding: 7px 4px;
            border-radius: 8px;
            border: 1px solid #ffd2a6;
            color: #ff9f43 !important;
            background: #fff;
        }
        .inv-m-tabs a.is-active {
            background: #ff9f43;
            border-color: #ff9f43;
            color: #fff !important;
        }
        .inv-main-card {
            background: transparent !important;
            border: 0 !important;
            box-shadow: none !important;
            margin-bottom: 0 !important;
        }
        .inv-main-card > .card-body {
            padding: 0 !important;
        }
        .inv-filter-row {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, 0.08);
            padding: 8px 10px;
            margin: 0 0 8px !important;
            --bs-gutter-x: 0;
        }
        .inv-search-col {
            display: flex;
            flex-direction: row-reverse;
            align-items: center;
            gap: 8px;
        }
        .inv-search-col .search-input {
            flex: 1 1 auto;
            min-width: 0;
        }
        .inv-search-col .search-input input {
            height: 38px;
            border-radius: 8px;
        }
        .inv-m-filter-btn {
            flex: 0 0 auto;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 0;
            background: #f25f5c;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .inv-date-col {
            display: none;
        }
        .inv-filter-row.is-date-open .inv-date-col {
            display: block;
            padding-top: 4px;
        }
        .inv-card-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .inv-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, 0.08);
            padding: 12px;
        }
        .inv-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 8px;
        }
        .inv-card-name {
            font-size: 15px;
            font-weight: 600;
            color: #1b2850;
            text-transform: uppercase;
            min-width: 0;
            overflow-wrap: anywhere;
        }
        .inv-card-price {
            font-size: 14px;
            font-weight: 700;
            color: #1b2850;
            white-space: nowrap;
        }
        .inv-card-badges {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 6px 0 8px;
            min-width: 0;
        }
        .inv-badge {
            font-size: 11px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 12px;
            white-space: nowrap;
        }
        .inv-badge.is-ok {
            background: #e7f8ef;
            color: #12a150;
        }
        .inv-badge.is-low {
            background: #fdecec;
            color: #e5483d;
        }
        .inv-card-sku {
            font-size: 11px;
            color: #8b93a7;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .inv-card-bottom {
            display: flex;
            align-items: flex-end;
            gap: 22px;
        }
        .inv-stat span {
            display: block;
            font-size: 12px;
            color: #8b93a7;
        }
        .inv-stat strong {
            font-size: 15px;
            color: #1b2850;
        }
        .inv-acts {
            margin-left: auto;
            display: flex;
            gap: 2px;
        }
        .inv-act {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #8b93a7;
            border-radius: 50%;
        }
        .inv-act:active {
            background: #f2f4f7;
        }
        .inv-card-remarks {
            margin-top: 6px;
            font-size: 12px;
            color: #667085;
        }
        .inv-var-toggle {
            margin-top: 8px;
            border: 0;
            background: #fff4e8;
            color: #ff9f43;
            font-size: 12px;
            font-weight: 600;
            border-radius: 12px;
            padding: 4px 12px;
        }
        .inv-var-toggle i {
            font-size: 10px;
            margin-left: 4px;
            transition: transform 0.2s;
        }
        .inv-card.is-var-open .inv-var-toggle i {
            transform: rotate(180deg);
        }
        .inv-var-list {
            display: none;
            margin-top: 8px;
            border-top: 1px dashed #e4e7ec;
        }
        .inv-card.is-var-open .inv-var-list {
            display: block;
        }
        .inv-var-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 0;
            border-bottom: 1px dashed #e4e7ec;
        }
        .inv-var-name {
            flex: 1 1 auto;
            min-width: 0;
            font-size: 13px;
            color: #1b2850;
        }
        .inv-var-name small {
            display: block;
            color: #8b93a7;
        }
        .inv-var-stock {
            font-weight: 700;
            color: #ff9f43;
        }
        .inv-var-row .inv-acts {
            margin-left: 0;
        }
        .inv-card-empty {
            background: #fff;
            border-radius: 10px;
            padding: 24px;
            text-align: center;
            color: #8b93a7;
        }
        .inv-main-card .pagination-controls {
            background: #fff;
            border-radius: 12px;
            padding: 10px;
            margin-top: 8px !important;
        }
    }

    :root[data-theme="dark"] .inv-m-head,
    :root[data-theme="dark"] .inv-filter-row,
    :root[data-theme="dark"] .inv-card,
    :root[data-theme="dark"] .inv-card-empty,
    :root[data-theme="dark"] .inv-main-card .pagination-controls {
        background: #1e293b !important;
    }
    :root[data-theme="dark"] .inv-card-name,
    :root[data-theme="dark"] .inv-card-price,
    :root[data-theme="dark"] .inv-stat strong,
    :root[data-theme="dark"] .inv-var-name,
    :root[data-theme="dark"] .inv-m-head-top h4,
    :root[data-theme="dark"] .inv-m-link {
        color: #e5e7eb !important;
    }
</style>

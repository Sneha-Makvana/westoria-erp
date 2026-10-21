<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Barcode Label Designer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Open+Sans:wght@400;700&family=Montserrat:wght@400;700&family=Lato:wght@400;700&family=Oswald:wght@400;700&family=Poppins:wght@400;700&family=Noto+Sans:wght@400;700&family=Roboto+Condensed:wght@400;700&family=PT+Sans:wght@400;700&family=Source+Sans+3:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bd-accent: #ff9f43;
            --bd-accent-dark: #e88d36;
            --bd-ink: #1b2850;
            --bd-muted: #6c757d;
            --bd-line: #e6e8ec;
            --bd-panel: #ffffff;
            --bd-stage: #dfe3ea;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            background: #f3f4f7;
            color: var(--bd-ink);
            font-family: "Segoe UI", Tahoma, Arial, sans-serif;
        }
        .bd-topbar {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            background: var(--bd-panel);
            border-bottom: 1px solid var(--bd-line);
            z-index: 20;
            flex-shrink: 0;
        }
        .bd-topbar h1 {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 700;
            white-space: nowrap;
        }
        .bd-topbar .hint {
            color: var(--bd-muted);
            font-size: 12px;
            margin-left: 4px;
        }
        .bd-actions {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }
        .btn-accent {
            background: var(--bd-accent);
            border-color: var(--bd-accent);
            color: #fff;
            font-weight: 600;
        }
        .btn-accent:hover {
            background: var(--bd-accent-dark);
            border-color: var(--bd-accent-dark);
            color: #fff;
        }
        .bd-shell {
            flex: 1;
            min-height: 0;
            display: grid;
            grid-template-columns: 220px 1fr 280px;
        }
        .bd-panel {
            background: var(--bd-panel);
            overflow: auto;
            padding: 14px;
        }
        .bd-panel-left { border-right: 1px solid var(--bd-line); }
        .bd-panel-right { border-left: 1px solid var(--bd-line); }
        .bd-section-title {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--bd-muted);
            margin: 0 0 10px;
        }
        .bd-tool-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 16px;
        }
        .bd-tool {
            border: 1px solid var(--bd-line);
            background: #f8f9fb;
            border-radius: 8px;
            padding: 8px 6px;
            text-align: center;
            cursor: pointer;
            user-select: none;
            transition: border-color .15s, background .15s, transform .1s;
            font-size: 11px;
            font-weight: 600;
            color: var(--bd-ink);
            line-height: 1.25;
        }
        .bd-tool i {
            display: block;
            font-size: 14px;
            margin-bottom: 4px;
            color: var(--bd-accent);
        }
        .bd-tool:hover {
            border-color: var(--bd-accent);
            background: #fff7ef;
        }
        .bd-tool:active { transform: scale(0.98); }
        .bd-tool.wide { grid-column: 1 / -1; }
        .bd-stage {
            background:
                linear-gradient(180deg, #e8ebf0 0%, var(--bd-stage) 100%);
            position: relative;
            overflow: auto;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        .bd-stage-toolbar {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: rgba(255,255,255,0.85);
            border-bottom: 1px solid var(--bd-line);
            flex-shrink: 0;
            backdrop-filter: blur(4px);
        }
        .bd-size-pill {
            background: #fff;
            border: 1px solid var(--bd-line);
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 600;
        }
        .bd-canvas-wrap {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px;
            position: relative;
            min-height: 280px;
        }
        .canvas-wrapper {
            background: #fff;
            box-shadow: 0 10px 30px rgba(27, 40, 80, 0.18);
            border: 1px solid #cfd5df;
            border-radius: 2px;
            position: relative;
            line-height: 0;
        }
        /* Safe top margin guide (~3mm) — above fabric canvases */
        .canvas-wrapper::before { display: none; }
        .bd-safe-top {
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            height: 12%;
            min-height: 12px;
            background: repeating-linear-gradient(
                -45deg,
                rgba(255, 159, 67, 0.28),
                rgba(255, 159, 67, 0.28) 4px,
                rgba(255, 159, 67, 0.08) 4px,
                rgba(255, 159, 67, 0.08) 8px
            );
            border-bottom: 2px dashed #ff9f43;
            pointer-events: none;
            z-index: 20;
            box-sizing: border-box;
        }
        .bd-safe-top::after {
            content: 'safe top';
            position: absolute;
            right: 4px;
            bottom: 1px;
            font-size: 9px;
            font-weight: 700;
            color: #c45f00;
            line-height: 1;
            letter-spacing: 0.02em;
        }
        .canvas-wrapper::after {
            content: attr(data-size);
            position: absolute;
            left: 0;
            top: calc(100% + 8px);
            font-size: 12px;
            color: var(--bd-muted);
            font-weight: 600;
            line-height: 1.2;
        }
        .bd-empty {
            position: absolute;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            z-index: 5;
        }
        .bd-empty.show { display: flex; }
        .bd-empty-card {
            pointer-events: auto;
            background: #fff;
            border: 1px dashed #b7bfcc;
            border-radius: 12px;
            padding: 22px 24px;
            max-width: 360px;
            text-align: center;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }
        .bd-empty-card h3 {
            font-size: 1rem;
            margin: 0 0 8px;
        }
        .bd-empty-card p {
            margin: 0 0 14px;
            color: var(--bd-muted);
            font-size: 13px;
        }
        .bd-help {
            background: #fff8f0;
            border: 1px solid #ffe0bd;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 12px;
            color: #7a4b12;
            margin-bottom: 14px;
            line-height: 1.45;
        }
        .bd-help strong { color: var(--bd-ink); }
        .bd-prop-card {
            border: 1px solid var(--bd-line);
            border-radius: 10px;
            padding: 12px;
            background: #fafbfc;
        }
        #noElementSelected {
            text-align: center;
            color: var(--bd-muted);
            padding: 18px 8px;
        }
        #noElementSelected i {
            font-size: 1.6rem;
            margin-bottom: 8px;
            opacity: 0.7;
        }
        .bd-preview-on .bd-stage-toolbar {
            background: #fff8f0;
            border-bottom-color: #ffd7a8;
        }
        .bd-preview-btn.active {
            background: var(--bd-accent) !important;
            border-color: var(--bd-accent) !important;
            color: #fff !important;
        }
        .bd-sample-panel {
            display: none;
            border: 1px solid #ffe0bd;
            background: #fff8f0;
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 14px;
        }
        .bd-sample-panel.show { display: block; }
        .bd-sample-panel .bd-section-title { color: #7a4b12; }
        .bd-sample-panel .form-label { margin-bottom: 2px; }
        .bd-align-btns .btn.active,
        .bd-style-btns .btn.active {
            background: var(--bd-accent);
            border-color: var(--bd-accent);
            color: #fff;
        }
        /* Word-like font family picker */
        .bd-font-picker {
            position: relative;
        }
        .bd-font-trigger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            width: 100%;
            padding: 6px 10px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            background: #fff;
            cursor: pointer;
            font-size: 13px;
            text-align: left;
            min-height: 34px;
        }
        .bd-font-trigger:hover,
        .bd-font-trigger:focus {
            border-color: var(--bd-accent);
            outline: none;
            box-shadow: 0 0 0 0.15rem rgba(255, 159, 67, 0.25);
        }
        .bd-font-trigger .bd-font-name {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .bd-font-trigger .bd-font-sample {
            color: var(--bd-muted);
            font-size: 12px;
            flex-shrink: 0;
        }
        .bd-font-menu {
            display: none;
            position: absolute;
            left: 0;
            right: 0;
            top: calc(100% + 4px);
            z-index: 40;
            max-height: 260px;
            overflow-y: auto;
            background: #fff;
            border: 1px solid var(--bd-line);
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            padding: 4px;
        }
        .bd-font-menu.open { display: block; }
        .bd-font-option {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            width: 100%;
            border: 0;
            background: transparent;
            padding: 8px 10px;
            border-radius: 6px;
            cursor: pointer;
            text-align: left;
        }
        .bd-font-option:hover,
        .bd-font-option.active {
            background: #fff4e8;
        }
        .bd-font-option .bd-font-label {
            font-size: 14px;
            color: var(--bd-ink);
            line-height: 1.2;
        }
        .bd-font-option .bd-font-preview {
            font-size: 13px;
            color: #555;
            white-space: nowrap;
        }
        #propFontFamily { display: none; }
        .bd-shortcut-hint {
            font-size: 0.72rem;
            color: var(--bd-muted);
            margin-top: 6px;
            line-height: 1.35;
        }
        @media (max-width: 900px) {
            .bd-shell {
                grid-template-columns: 1fr;
                grid-template-rows: auto 1fr auto;
                overflow: auto;
            }
            .bd-panel { max-height: 220px; }
            .bd-stage { min-height: 420px; }
        }
    </style>
</head>
<body>
    <div class="bd-topbar">
        <div>
            <h1>Barcode Label Designer</h1>
            <div class="hint">Click a field to add · drag to move · Save when done</div>
        </div>
        <div class="bd-actions">
            <select id="templateSelector" class="form-select form-select-sm" style="min-width: 180px;" title="Saved templates">
                <option value="">New template…</option>
                </select>
            <button type="button" id="btnRenameTemplate" class="btn btn-outline-secondary btn-sm" title="Rename selected template" disabled>
                <i class="fas fa-pen"></i> Rename
            </button>
            <button type="button" id="btnLoadClassicLayout" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-magic"></i> Default layout
            </button>
            <button type="button" id="btnSaveTemplate" class="btn btn-accent btn-sm">
                <i class="fas fa-save"></i> Save
                </button>
            <button type="button" id="btnDeleteTemplate" class="btn btn-outline-danger btn-sm" title="Delete selected template" disabled>
                <i class="fas fa-trash"></i> Delete
                </button>
                <a href="{{ route('product.list') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

    <div class="bd-shell">
        <aside class="bd-panel bd-panel-left">
            <p class="bd-section-title">Add to label</p>
            <div class="bd-help">
                <strong>Tip:</strong> Click any item below to place it on the label. Or use <strong>Default layout</strong> for a ready-made design.
            </div>

            <p class="bd-section-title">Basics</p>
            <div class="bd-tool-grid">
                <div class="bd-tool wide" draggable="true" data-type="text" data-content="Text">
                    <i class="fas fa-font"></i> Custom text
                </div>
                <div class="bd-tool wide" draggable="true" data-type="barcode" data-content="@{{barcode}}">
                    <i class="fas fa-barcode"></i> Barcode image
                </div>
            </div>
            
            <p class="bd-section-title">Product fields</p>
            <div class="bd-tool-grid">
                <div class="bd-tool" draggable="true" data-type="variable" data-content="@{{company_name}}"><i class="fas fa-building"></i> Company</div>
                <div class="bd-tool" draggable="true" data-type="variable" data-content="@{{company_address}}"><i class="fas fa-map-marker-alt"></i> Address</div>
                <div class="bd-tool wide" draggable="true" data-type="variable" data-content="@{{product_name}}"><i class="fas fa-box"></i> Product name</div>
                <div class="bd-tool" draggable="true" data-type="variable" data-content="@{{mrp}}"><i class="fas fa-tag"></i> MRP</div>
                <div class="bd-tool" draggable="true" data-type="variable" data-content="@{{selling_price}}"><i class="fas fa-tags"></i> Rate</div>
                <div class="bd-tool" draggable="true" data-type="variable" data-content="@{{barcode}}"><i class="fas fa-hashtag"></i> Barcode no.</div>
                <div class="bd-tool" draggable="true" data-type="variable" data-content="@{{barcode_text}}"><i class="fas fa-align-center"></i> Barcode text</div>
                <div class="bd-tool" draggable="true" data-type="variable" data-content="@{{sku}}"><i class="fas fa-barcode"></i> SKU</div>
                <div class="bd-tool" draggable="true" data-type="variable" data-content="@{{batch_no}}"><i class="fas fa-layer-group"></i> Batch</div>
                <div class="bd-tool" draggable="true" data-type="variable" data-content="@{{wholesale}}"><i class="fas fa-store"></i> Wholesale</div>
                <div class="bd-tool" draggable="true" data-type="variable" data-content="@{{other}}"><i class="fas fa-ellipsis-h"></i> Other</div>
            </div>
        </aside>

        <main class="bd-stage">
            <div class="bd-stage-toolbar">
                <span class="bd-size-pill" id="sizePill">35 × 25 mm</span>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnZoomOut" title="Zoom out"><i class="fas fa-search-minus"></i></button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnZoomIn" title="Zoom in"><i class="fas fa-search-plus"></i></button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnZoomReset">Reset zoom</button>
                <button type="button" class="btn btn-sm btn-outline-secondary bd-preview-btn ms-2" id="btnTogglePreview" title="Show sample print values">
                    <i class="fas fa-eye"></i> Preview
                </button>
                <button type="button" class="btn btn-sm btn-accent ms-2" id="btnTestPrint" title="Print one test label with sample values">
                    <i class="fas fa-print"></i> Test print
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="btnUndo" title="Undo (Ctrl+Z)" disabled>
                    <i class="fas fa-undo"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnRedo" title="Redo (Ctrl+Y)" disabled>
                    <i class="fas fa-redo"></i>
                </button>
                <span class="ms-auto small text-muted" id="objectCountLabel">0 items</span>
            </div>
            <div class="bd-canvas-wrap" id="canvasContainer">
                <div class="bd-empty" id="emptyState">
                    <div class="bd-empty-card">
                        <h3>Your label is empty</h3>
                        <p>Start with a ready layout, or click fields on the left to build your own.</p>
                        <button type="button" class="btn btn-accent btn-sm" id="btnEmptyDefault">
                            <i class="fas fa-magic"></i> Use default layout
                        </button>
            </div>
            </div>
                <div class="canvas-wrapper" id="canvasWrapper" data-size="35 × 25 mm">
                    <canvas id="labelCanvas"></canvas>
                    <div class="bd-safe-top" id="safeTopGuide" title="Print safe margin — keep text below this band"></div>
            </div>
            </div>
        </main>

        <aside class="bd-panel bd-panel-right">
            <div id="previewSamplePanel" class="bd-sample-panel">
                <p class="bd-section-title">Sample print values</p>
                <p class="small text-muted mb-2">Edit these to see how the label will look when printing. Not saved with the template.</p>
                <div class="mb-2">
                    <label class="form-label small">Company</label>
                    <input type="text" class="form-control form-control-sm sample-input" data-key="company_name">
            </div>
                <div class="mb-2">
                    <label class="form-label small">Address</label>
                    <input type="text" class="form-control form-control-sm sample-input" data-key="company_address">
            </div>
                <div class="mb-2">
                    <label class="form-label small">Product name</label>
                    <input type="text" class="form-control form-control-sm sample-input" data-key="product_name">
            </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label small">MRP</label>
                        <input type="text" class="form-control form-control-sm sample-input" data-key="mrp">
            </div>
                    <div class="col-6">
                        <label class="form-label small">Rate</label>
                        <input type="text" class="form-control form-control-sm sample-input" data-key="selling_price">
        </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label small">Barcode no.</label>
                        <input type="text" class="form-control form-control-sm sample-input" data-key="barcode">
                    </div>
                    <div class="col-6">
                        <label class="form-label small">Batch</label>
                        <input type="text" class="form-control form-control-sm sample-input" data-key="batch_no">
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label small">Barcode text</label>
                    <input type="text" class="form-control form-control-sm sample-input" data-key="barcode_text">
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label small">SKU</label>
                        <input type="text" class="form-control form-control-sm sample-input" data-key="sku">
                    </div>
                    <div class="col-6">
                        <label class="form-label small">Wholesale</label>
                        <input type="text" class="form-control form-control-sm sample-input" data-key="wholesale">
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label small">Other</label>
                    <input type="text" class="form-control form-control-sm sample-input" data-key="other">
            </div>
        </div>

            <p class="bd-section-title">Label size (mm)</p>
            <div class="bd-prop-card mb-3">
                <div class="row g-2">
                <div class="col-6">
                        <label class="form-label small mb-1">Width</label>
                        <input type="number" id="labelWidth" class="form-control form-control-sm" value="35" min="10" step="0.5">
                </div>
                <div class="col-6">
                        <label class="form-label small mb-1">Height</label>
                        <input type="number" id="labelHeight" class="form-control form-control-sm" value="25" min="10" step="0.5">
                </div>
                    <div class="col-12">
                        <button type="button" id="btnApplySize" class="btn btn-outline-secondary btn-sm w-100">Apply size</button>
                        <p class="small text-muted mb-0 mt-1">One label: <strong>35 × 25 mm</strong>. TVS stock strip: <strong>4.13 × 0.98 in</strong> (3 labels).</p>
                    </div>
                </div>
            </div>

            <div id="elementProperties" class="bd-prop-card" style="display:none;">
                <p class="bd-section-title">Selected item</p>
                <div class="mb-2" id="propTextWrap">
                    <label class="form-label small mb-1">Text / value</label>
                    <input type="text" id="propText" class="form-control form-control-sm">
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label small mb-1">X (mm)</label>
                        <input type="number" id="propX" class="form-control form-control-sm" step="0.1">
                    </div>
                    <div class="col-6">
                        <label class="form-label small mb-1">Y (mm)</label>
                        <input type="number" id="propY" class="form-control form-control-sm" step="0.1">
                    </div>
                </div>
                <div id="propTextStyleWrap">
                    <div class="mb-2">
                        <label class="form-label small mb-1">Font</label>
                        <div class="bd-font-picker" id="bdFontPicker">
                            <button type="button" class="bd-font-trigger" id="bdFontTrigger" aria-haspopup="listbox" aria-expanded="false">
                                <span class="bd-font-name" id="bdFontTriggerName" style="font-family: Arial, Helvetica, sans-serif;">Arial</span>
                                <span class="bd-font-sample" id="bdFontTriggerSample" style="font-family: Arial, Helvetica, sans-serif;">Aa Bb</span>
                                <i class="fas fa-caret-down text-muted"></i>
                            </button>
                            <div class="bd-font-menu" id="bdFontMenu" role="listbox"></div>
                        </div>
                        <select id="propFontFamily" aria-hidden="true" tabindex="-1"></select>
                    </div>
                    <div class="row g-2 mb-2">
                    <div class="col-6">
                            <label class="form-label small mb-1">Font size</label>
                            <input type="number" id="propFontSize" class="form-control form-control-sm" min="6" max="72">
                    </div>
                    <div class="col-6">
                            <label class="form-label small mb-1">Align</label>
                            <div class="btn-group w-100 bd-align-btns" role="group" aria-label="Text align">
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnAlignLeft" data-align="left" title="Align left">
                                    <i class="fas fa-align-left"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnAlignCenter" data-align="center" title="Align center">
                                    <i class="fas fa-align-center"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnAlignRight" data-align="right" title="Align right">
                                    <i class="fas fa-align-right"></i>
                                </button>
                            </div>
                            <select id="propAlign" class="d-none" aria-hidden="true">
                            <option value="left">Left</option>
                            <option value="center">Center</option>
                            <option value="right">Right</option>
                        </select>
                    </div>
                </div>
                    <div class="btn-group w-100 mb-2 bd-style-btns">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnPropBold" title="Bold (Ctrl+B)"><i class="fas fa-bold"></i></button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnPropItalic" title="Italic (Ctrl+I)"><i class="fas fa-italic"></i></button>
                    </div>
                </div>
                <div id="propBarcodeWrap" style="display:none;">
                    <div class="mb-2">
                        <label class="form-label small mb-1 d-flex justify-content-between">
                            <span>Density</span>
                            <strong id="propBarcodeDensityVal">100%</strong>
                        </label>
                        <input type="range" class="form-range" id="propBarcodeDensity" min="50" max="200" step="1" value="100">
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Low</span>
                            <span>Normal</span>
                            <span>High</span>
            </div>
                        <p class="small text-muted mb-0 mt-1">Drag the edges or type the size. The bars fill that box. Density makes the lines clearer without changing the size.</p>
                        <p class="small mb-0 mt-1 text-muted" id="propBarcodeScanHint"></p>
            </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small mb-1">Width (mm)</label>
                            <input type="number" id="propBarcodeWidth" class="form-control form-control-sm" min="4" max="90" step="0.1" value="20">
        </div>
                        <div class="col-6">
                            <label class="form-label small mb-1">Height (mm)</label>
                            <input type="number" id="propBarcodeHeight" class="form-control form-control-sm" min="2" max="40" step="0.1" value="6">
                        </div>
                    </div>
                </div>
                <button type="button" id="btnDeleteElement" class="btn btn-outline-danger btn-sm w-100">
                    <i class="fas fa-trash"></i> Delete item
                </button>
                <p class="bd-shortcut-hint">Shortcuts: <kbd>Ctrl</kbd>+<kbd>B</kbd> bold · <kbd>Ctrl</kbd>+<kbd>I</kbd> italic · arrows move · <kbd>Ctrl</kbd>+<kbd>Z</kbd> undo · <kbd>Del</kbd> delete</p>
    </div>

            <div id="noElementSelected">
                <i class="fas fa-hand-pointer d-block"></i>
                <p class="small mb-0">Click an item on the label to edit size, position, and style here.</p>
            </div>
        </aside>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.5/JsBarcode.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Sanctum stateful API requires CSRF on mutating requests (POST/PUT/DELETE)
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const scale = 10; 
            let viewZoom = 1;
            let currentTemplateId = null;
            let draggedElement = null;
            let previewMode = false;

            // Word-like font list (system + Google Fonts loaded in <head>)
            var LABEL_FONTS = [
                { name: 'Arial', value: 'Arial, Helvetica, sans-serif', sample: 'Aa Bb 123' },
                { name: 'Helvetica', value: 'Helvetica, Arial, sans-serif', sample: 'Aa Bb 123' },
                { name: 'Times New Roman', value: '"Times New Roman", Times, serif', sample: 'Aa Bb 123' },
                { name: 'Georgia', value: 'Georgia, serif', sample: 'Aa Bb 123' },
                { name: 'Verdana', value: 'Verdana, Geneva, sans-serif', sample: 'Aa Bb 123' },
                { name: 'Tahoma', value: 'Tahoma, Geneva, sans-serif', sample: 'Aa Bb 123' },
                { name: 'Trebuchet MS', value: '"Trebuchet MS", Helvetica, sans-serif', sample: 'Aa Bb 123' },
                { name: 'Courier New', value: '"Courier New", Courier, monospace', sample: 'Aa Bb 123' },
                { name: 'Lucida Console', value: '"Lucida Console", Monaco, monospace', sample: 'Aa Bb 123' },
                { name: 'Impact', value: 'Impact, Charcoal, sans-serif', sample: 'Aa Bb 123' },
                { name: 'Comic Sans MS', value: '"Comic Sans MS", cursive', sample: 'Aa Bb 123' },
                { name: 'Roboto', value: 'Roboto, Arial, sans-serif', sample: 'Aa Bb 123' },
                { name: 'Open Sans', value: '"Open Sans", Arial, sans-serif', sample: 'Aa Bb 123' },
                { name: 'Montserrat', value: 'Montserrat, Arial, sans-serif', sample: 'Aa Bb 123' },
                { name: 'Lato', value: 'Lato, Arial, sans-serif', sample: 'Aa Bb 123' },
                { name: 'Oswald', value: 'Oswald, Arial, sans-serif', sample: 'Aa Bb 123' },
                { name: 'Poppins', value: 'Poppins, Arial, sans-serif', sample: 'Aa Bb 123' },
                { name: 'Noto Sans', value: '"Noto Sans", Arial, sans-serif', sample: 'Aa Bb 123' },
                { name: 'Roboto Condensed', value: '"Roboto Condensed", Arial, sans-serif', sample: 'Aa Bb 123' },
                { name: 'PT Sans', value: '"PT Sans", Arial, sans-serif', sample: 'Aa Bb 123' },
                { name: 'Source Sans 3', value: '"Source Sans 3", Arial, sans-serif', sample: 'Aa Bb 123' }
            ];
            var defaultFontFamily = 'Arial, Helvetica, sans-serif';
            try {
                var savedFont = localStorage.getItem('barcodeDesignerFontFamily');
                if (savedFont && LABEL_FONTS.some(function(f) { return f.value === savedFont; })) {
                    defaultFontFamily = savedFont;
                }
            } catch (e) {}

            function findFontMeta(value) {
                var v = String(value || '').trim();
                for (var i = 0; i < LABEL_FONTS.length; i++) {
                    if (LABEL_FONTS[i].value === v) return LABEL_FONTS[i];
                }
                // Match by first family name
                var first = v.split(',')[0].replace(/["']/g, '').trim().toLowerCase();
                for (var j = 0; j < LABEL_FONTS.length; j++) {
                    var n = LABEL_FONTS[j].name.toLowerCase();
                    if (n === first || LABEL_FONTS[j].value.toLowerCase().indexOf(first) === 0) {
                        return LABEL_FONTS[j];
                    }
                }
                return { name: first || 'Arial', value: v || defaultFontFamily, sample: 'Aa Bb 123' };
            }

            function buildFontPicker() {
                var $sel = $('#propFontFamily');
                var $menu = $('#bdFontMenu');
                $sel.empty();
                $menu.empty();
                LABEL_FONTS.forEach(function(f) {
                    $sel.append($('<option></option>').attr('value', f.value).text(f.name));
                    var $btn = $('<button type="button" class="bd-font-option" role="option"></button>');
                    $btn.attr('data-value', f.value);
                    $btn.append($('<span class="bd-font-label"></span>').text(f.name).css('font-family', f.value));
                    $btn.append($('<span class="bd-font-preview"></span>').text(f.sample).css('font-family', f.value));
                    $menu.append($btn);
                });
                setFontPickerUI(defaultFontFamily);
            }

            function setFontPickerUI(fontValue) {
                var meta = findFontMeta(fontValue);
                $('#propFontFamily').val(meta.value);
                $('#bdFontTriggerName').text(meta.name).css('font-family', meta.value);
                $('#bdFontTriggerSample').text(meta.sample).css('font-family', meta.value);
                $('#bdFontMenu .bd-font-option').removeClass('active');
                $('#bdFontMenu .bd-font-option[data-value="' + meta.value.replace(/"/g, '\\"') + '"]').addClass('active');
                // Attribute selector with quotes in value can fail — match manually
                $('#bdFontMenu .bd-font-option').each(function() {
                    if ($(this).attr('data-value') === meta.value) {
                        $(this).addClass('active');
                    }
                });
            }

            function applyFontFamily(fontValue, saveHistory) {
                var obj = canvas.getActiveObject();
                if (!isTextObject(obj)) return;
                var meta = findFontMeta(fontValue);
                obj.set({ fontFamily: meta.value, scaleX: 1, scaleY: 1 });
                if (typeof obj.setCoords === 'function') obj.setCoords();
                canvas.requestRenderAll();
                setFontPickerUI(meta.value);
                try { localStorage.setItem('barcodeDesignerFontFamily', meta.value); } catch (e) {}
                defaultFontFamily = meta.value;
                if (saveHistory !== false) captureHistory();
            }

            buildFontPicker();

            $('#bdFontTrigger').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $menu = $('#bdFontMenu');
                var open = !$menu.hasClass('open');
                $menu.toggleClass('open', open);
                $(this).attr('aria-expanded', open ? 'true' : 'false');
            });
            $('#bdFontMenu').on('click', '.bd-font-option', function(e) {
                e.preventDefault();
                e.stopPropagation();
                applyFontFamily($(this).attr('data-value'), true);
                $('#bdFontMenu').removeClass('open');
                $('#bdFontTrigger').attr('aria-expanded', 'false');
            });
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#bdFontPicker').length) {
                    $('#bdFontMenu').removeClass('open');
                    $('#bdFontTrigger').attr('aria-expanded', 'false');
                }
            });
            $('#propFontFamily').on('change', function() {
                applyFontFamily($(this).val(), true);
            });

            var sampleData = {
                company_name: 'POOJA SAMAN.IN',
                company_address: 'DURG, RISALI, RAIPUR',
                product_name: 'VT POPLIN 4444 KALA NL',
                mrp: 'MRP-70',
                selling_price: 'RATE-50',
                barcode: '54538',
                barcode_text: '54538',
                sku: 'SKU-01',
                batch_no: 'B01',
                wholesale: 'W-50',
                other: 'NOTE',
                qty: '1 PC',
                price: 'RATE-50',
                address: 'DURG, RISALI, RAIPUR'
            };

            // When opened from Product List → Custom Designer, use that product's print details.
            var pendingProductPreview = false;
            (function applyIncomingSampleFromProduct() {
                var raw = null;
                try { raw = localStorage.getItem('barcodeDesignerSample'); } catch (e) {}
                if (!raw) return;
                try { localStorage.removeItem('barcodeDesignerSample'); } catch (e2) {}
                var data = null;
                try { data = JSON.parse(raw); } catch (e3) { data = null; }
                if (!data || typeof data !== 'object') return;
                Object.keys(data).forEach(function(key) {
                    if (data[key] === undefined || data[key] === null) return;
                    sampleData[key] = String(data[key]);
                });
                sampleData.address = sampleData.company_address || sampleData.address || '';
                sampleData.price = sampleData.selling_price || sampleData.price || '';
                if (!sampleData.barcode_text) {
                    sampleData.barcode_text = sampleData.barcode || '';
                }
                pendingProductPreview = true;
            })();

            function fieldKey(vf) {
                return String(vf || '').replace(/[{}@\s]/g, '').trim().toLowerCase();
            }

            function placeholderForKey(key) {
                return '@{{' + key + '}}';
            }

            function fillSampleInputs() {
                $('.sample-input').each(function() {
                    var key = $(this).data('key');
                    $(this).val(sampleData[key] || '');
                });
            }

            function syncSampleFromInputs() {
                $('.sample-input').each(function() {
                    var key = $(this).data('key');
                    sampleData[key] = $(this).val();
                });
                // Keep aliases in sync
                sampleData.address = sampleData.company_address;
                sampleData.price = sampleData.selling_price;
                if (!sampleData.barcode_text) {
                    sampleData.barcode_text = sampleData.barcode || '';
                }
            }

            function densityToModuleWidth(density) {
                var dens = parseFloat(density);
                if (!isFinite(dens) || dens <= 0) dens = 1;
                dens = Math.max(0.5, Math.min(2, dens));
                return Math.max(2, Math.min(4, Math.round(dens * 2)));
            }

            function densityToOccupancy() {
                return 1;
            }

            var barcodeModuleCache = {};
            function countBarcodeModules(code) {
                var key = String(code || '0');
                if (barcodeModuleCache[key]) return barcodeModuleCache[key];
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
                barcodeModuleCache[key] = count;
                return count;
            }

            // Bars are drawn inside the box the user set. Density only changes bar thickness.
            function layoutBarcodeDraw(code, density, boxWpx, maxWpx, pxPerMm, targetHpx) {
                pxPerMm = pxPerMm || 10;
                var dens = parseFloat(density);
                if (!isFinite(dens) || dens <= 0) dens = 1;
                dens = Math.max(0.5, Math.min(2, dens));
                var boxMm = Math.max(1, (boxWpx || 0) / pxPerMm);
                var heightMm = Math.max(1, (targetHpx || 40) / pxPerMm);
                var pngW = Math.max(8, Math.round(boxWpx || boxMm * pxPerMm));
                var pngH = Math.max(8, Math.round(targetHpx || heightMm * pxPerMm));
                var bars = countBarcodeModules(code);
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
            }

            var barcodeBitCache = {};
            function barcodeBits(code) {
                var key = String(code || '0');
                if (barcodeBitCache[key]) return barcodeBitCache[key];
                var src = document.createElement('canvas');
                JsBarcode(src, key, {
                    format: 'CODE128',
                    lineColor: '#000000',
                    background: '#ffffff',
                    displayValue: false,
                    margin: 0,
                    width: 1,
                    height: 8,
                    flat: true
                });
                var row = src.getContext('2d').getImageData(0, 2, src.width, 1).data;
                var bits = '';
                for (var i = 0; i < src.width; i++) {
                    bits += row[i * 4] < 128 ? '1' : '0';
                }
                barcodeBitCache[key] = bits || '101';
                return barcodeBitCache[key];
            }

            function paintBarcodeCanvas(code, layout) {
                var bits = barcodeBits(code);
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
                return out;
            }

            function buildBarcodeDataUrl(code, density, targetWpx, targetHpx, maxWpx, pxPerMm) {
                if (typeof JsBarcode === 'undefined') return { src: '', width: 0, height: 0, tooThin: true };
                var layout = layoutBarcodeDraw(code, density, targetWpx, maxWpx, pxPerMm || scale || 10, targetHpx);
                try {
                    var out = paintBarcodeCanvas(code, layout);
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
            }

            function showBarcodeScanHint(tooThin, grew) {
                var $hint = $('#propBarcodeScanHint');
                if (!$hint.length) return;
                if (tooThin) {
                    $hint.removeClass('text-muted').addClass('text-danger')
                        .text('Bars are still thin for this number. Drag the barcode wider.');
                } else {
                    $hint.removeClass('text-danger').addClass('text-muted')
                        .text('The bars fill the size you set.');
                }
            }

            function getBarcodeBox(obj) {
                if (obj.barcodeBoxWidth && obj.barcodeBoxHeight) {
                    return {
                        left: obj.barcodeBoxLeft != null ? obj.barcodeBoxLeft : obj.left,
                        top: obj.barcodeBoxTop != null ? obj.barcodeBoxTop : obj.top,
                        width: obj.barcodeBoxWidth,
                        height: obj.barcodeBoxHeight
                    };
                }
                return {
                    left: obj.left || 0,
                    top: obj.top || 0,
                    width: Math.max(1, (obj.width || 100) * (obj.scaleX || 1)),
                    height: Math.max(1, (obj.height || 40) * (obj.scaleY || 1))
                };
            }

            function makeBarcodePlaceholderRect(box, density) {
                var rect = new fabric.Rect({
                    left: box.left,
                    top: box.top,
                    width: box.width,
                    height: box.height,
                    fill: '#cccccc',
                    selectable: true,
                    scaleX: 1,
                    scaleY: 1
                });
                markAsBarcode(rect, density || 1);
                return rect;
            }

            function renderBarcodePreviewImage(bObj, done) {
                if (!bObj || !bObj.isBarcode) {
                    if (done) done();
                    return;
                }
                if (typeof JsBarcode === 'undefined') {
                    bObj.set('fill', '#222222');
                    if (done) done();
                    return;
                }
                var density = parseFloat(bObj.barcodeDensity) || 1;
                var box = getBarcodeBox(bObj);
                var code = (sampleData.barcode || sampleData.barcode_text || '54538');
                var drawn = buildBarcodeDataUrl(code, density, box.width, box.height, box.width, scale);
                var url = drawn && drawn.src ? drawn.src : '';
                showBarcodeScanHint(drawn && drawn.tooThin, drawn && drawn.grew);
                if (!url) {
                    if (done) done();
                    return;
                }
                var wasActive = canvas.getActiveObject() === bObj;
                // Preserve rotation — recreating the image used to reset angle to 0
                var keepAngle = parseFloat(bObj.angle) || 0;
                var keepCenter = null;
                try {
                    if (typeof bObj.getCenterPoint === 'function') {
                        keepCenter = bObj.getCenterPoint();
                    }
                } catch (e) {}
                fabric.Image.fromURL(url, function(img) {
                    var pngW = Math.max(1, drawn.pngWidth || img.width || box.width);
                    var pngH = Math.max(1, drawn.pngHeight || img.height || box.height);
                    img.set({
                        left: box.left,
                        top: box.top,
                        originX: 'left',
                        originY: 'top',
                        angle: keepAngle,
                        centeredRotation: true,
                        scaleX: box.width / pngW,
                        scaleY: box.height / pngH,
                        selectable: true,
                        lockUniScaling: false,
                        objectCaching: false,
                        imageSmoothing: false,
                        isBarcode: true,
                        barcodeDensity: density,
                        barcodeSizeWidth: bObj.barcodeSizeWidth,
                        barcodeSizeHeight: bObj.barcodeSizeHeight,
                        barcodeBaseWidth: box.width,
                        barcodeBoxLeft: box.left,
                        barcodeBoxTop: box.top,
                        barcodeBoxWidth: box.width,
                        barcodeBoxHeight: box.height,
                        barcodePreviewImage: true
                    });
                    // Image is drawW x drawH; if occupancy < 1 it is smaller than box but still centered
                    canvas.remove(bObj);
                    canvas.add(img);
                    if (wasActive) {
                        canvas.setActiveObject(img);
                        updatePropertiesPanel();
                    }
                    canvas.requestRenderAll();
                    if (done) done();
                });
            }

            function restoreBarcodePlaceholders() {
                var barcodes = canvas.getObjects().filter(function(o) { return o.isBarcode; });
                barcodes.forEach(function(obj) {
                    if (obj.type === 'rect' && !obj.barcodePreviewImage) {
                        obj.set('fill', '#cccccc');
                        return;
                    }
                    var box = getBarcodeBox(obj);
                    var density = parseFloat(obj.barcodeDensity) || 1;
                    var keepAngle = parseFloat(obj.angle) || 0;
                    var keepCenter = null;
                    try {
                        if (typeof obj.getCenterPoint === 'function') keepCenter = obj.getCenterPoint();
                    } catch (e) {}
                    var rect = makeBarcodePlaceholderRect(box, density);
                    if (keepCenter) {
                        rect.set({
                            left: keepCenter.x,
                            top: keepCenter.y,
                            originX: 'center',
                            originY: 'center',
                            angle: keepAngle,
                            centeredRotation: true
                        });
                    } else {
                        rect.set({ angle: keepAngle, centeredRotation: true });
                    }
                    var wasActive = canvas.getActiveObject() === obj;
                    canvas.remove(obj);
                    canvas.add(rect);
                    if (wasActive) canvas.setActiveObject(rect);
                });
                canvas.requestRenderAll();
            }

            function refreshAllBarcodePreviews(done) {
                var barcodes = canvas.getObjects().filter(function(o) { return o.isBarcode; });
                if (!barcodes.length) {
                    if (done) done();
                    return;
                }
                var pending = barcodes.length;
                barcodes.forEach(function(obj) {
                    renderBarcodePreviewImage(obj, function() {
                        pending -= 1;
                        if (pending <= 0) {
                            canvas.requestRenderAll();
                            if (done) done();
                        }
                    });
                });
            }

            function applyPreviewToCanvas() {
                canvas.getObjects().forEach(function(obj) {
                    if (obj.isBarcode) {
                        return; // handled below as real barcode image
                    }
                    if (!isTextObject(obj)) return;
                    // Classic combined price line
                    if (obj.isPriceLine || (/\{\{\s*mrp\s*\}\}/i.test(String(obj.text || '')) &&
                        /\{\{\s*(selling_price|price|rate)\s*\}\}/i.test(String(obj.text || ''))) ||
                        (/^MRP-/i.test(String(obj.text || '')) && /RATE-/i.test(String(obj.text || '')))) {
                        if (previewMode) {
                            obj.set('text', (sampleData.mrp || 'MRP-70') + '  ' + (sampleData.selling_price || 'RATE-50'));
                        } else {
                            obj.set('text', '@{{mrp}}  @{{selling_price}}');
                        }
                        return;
                    }
                    if (obj.isVariable && obj.variableField) {
                        var key = fieldKey(obj.variableField);
                        if (previewMode) {
                            var val = sampleData[key];
                            if (val === undefined || val === null || val === '') {
                                val = placeholderForKey(key);
                            }
                            obj.set('text', String(val));
                        } else {
                            obj.set('text', placeholderForKey(key));
                        }
                    }
                });
                if (previewMode) {
                    refreshAllBarcodePreviews(function() {
                        updateChrome();
                    });
                } else {
                    restoreBarcodePlaceholders();
                    canvas.requestRenderAll();
                    updateChrome();
                }
            }

            function setPreviewMode(on) {
                previewMode = !!on;
                $('#btnTogglePreview').toggleClass('active', previewMode);
                $('#btnTogglePreview').html(previewMode
                    ? '<i class="fas fa-eye-slash"></i> Exit preview'
                    : '<i class="fas fa-eye"></i> Preview');
                $('body').toggleClass('bd-preview-on', previewMode);
                $('#previewSamplePanel').toggleClass('show', previewMode);
                if (previewMode) {
                    fillSampleInputs();
                }
                applyPreviewToCanvas();
            }
            
            let canvas = new fabric.Canvas('labelCanvas', {
                width: 35 * scale,
                height: 25 * scale,
                backgroundColor: '#ffffff',
                preserveObjectStacking: true,
                selection: true,
                enableRetinaScaling: false,
                imageSmoothingEnabled: false
            });

            // Undo / redo history
            var historyStack = [];
            var historyIndex = -1;
            var historyLocked = false;
            var HISTORY_LIMIT = 40;
            var HISTORY_PROPS = ['isVariable', 'variableField', 'isBarcode', 'isPriceLine', 'barcodeDensity', 'barcodeBaseWidth', 'barcodeBoxLeft', 'barcodeBoxTop', 'barcodeBoxWidth', 'barcodeBoxHeight', 'barcodeSizeWidth', 'barcodeSizeHeight', 'barcodePreviewImage'];

            function updateHistoryButtons() {
                $('#btnUndo').prop('disabled', historyIndex <= 0);
                $('#btnRedo').prop('disabled', historyIndex < 0 || historyIndex >= historyStack.length - 1);
            }

            function captureHistory() {
                if (historyLocked) return;
                try {
                    var snapshot = JSON.stringify(canvas.toJSON(HISTORY_PROPS));
                    if (historyIndex >= 0 && historyStack[historyIndex] === snapshot) {
                        return;
                    }
                    historyStack = historyStack.slice(0, historyIndex + 1);
                    historyStack.push(snapshot);
                    if (historyStack.length > HISTORY_LIMIT) {
                        historyStack.shift();
                    }
                    historyIndex = historyStack.length - 1;
                    updateHistoryButtons();
                } catch (err) {}
            }

            function restoreHistory(index) {
                if (index < 0 || index >= historyStack.length) return;
                historyLocked = true;
                var snapshot = historyStack[index];
                canvas.loadFromJSON(snapshot, function() {
                    canvas.getObjects().forEach(function(obj) {
                        // properties restored via reviver below in loadFromJSON 2nd arg — use fabricReviver if available later
                    });
                canvas.renderAll();
                    historyIndex = index;
                    historyLocked = false;
                    updateHistoryButtons();
                    updateChrome();
                    var active = canvas.getActiveObject();
                    if (active) {
                        updatePropertiesPanel();
                    } else {
                        $('#elementProperties').hide();
                        $('#noElementSelected').show();
                    }
                }, function(o, object) {
                    if (!o || !object) return;
                    if (o.isBarcode !== undefined) object.isBarcode = o.isBarcode;
                    if (o.isVariable !== undefined) object.isVariable = o.isVariable;
                    if (o.variableField !== undefined) object.variableField = o.variableField;
                    if (o.isPriceLine !== undefined) object.isPriceLine = o.isPriceLine;
                    if (o.barcodeDensity !== undefined) object.barcodeDensity = o.barcodeDensity;
                    if (o.barcodeBaseWidth !== undefined) object.barcodeBaseWidth = o.barcodeBaseWidth;
                });
            }

            function undoHistory() {
                if (historyIndex <= 0) return;
                restoreHistory(historyIndex - 1);
            }

            function redoHistory() {
                if (historyIndex >= historyStack.length - 1) return;
                restoreHistory(historyIndex + 1);
            }

            function resetHistory() {
                historyStack = [];
                historyIndex = -1;
                captureHistory();
            }

            function setTextAlign(align) {
                var obj = canvas.getActiveObject();
                if (!isTextObject(obj)) return;
                obj.set('textAlign', align);
                if (typeof obj.initDimensions === 'function') {
                    try { obj.initDimensions(); } catch (e) {}
                }
                if (typeof obj.setCoords === 'function') obj.setCoords();
                canvas.requestRenderAll();
                $('#propAlign').val(align);
                $('.bd-align-btns .btn').removeClass('active');
                $('.bd-align-btns .btn[data-align="' + align + '"]').addClass('active');
                captureHistory();
            }

            function toggleBold() {
                var obj = canvas.getActiveObject();
                if (!isTextObject(obj)) return;
                var isBold = obj.fontWeight === 'bold' || obj.fontWeight === '700';
                obj.set('fontWeight', isBold ? 'normal' : 'bold');
                canvas.requestRenderAll();
                $('#btnPropBold').toggleClass('active', !isBold);
                captureHistory();
            }

            function toggleItalic() {
                var obj = canvas.getActiveObject();
                if (!isTextObject(obj)) return;
                var isItalic = obj.fontStyle === 'italic';
                obj.set('fontStyle', isItalic ? 'normal' : 'italic');
                canvas.requestRenderAll();
                $('#btnPropItalic').toggleClass('active', !isItalic);
                captureHistory();
            }

            function ensureBarcodeMeta(obj) {
                if (!obj || !obj.isBarcode) return;
                var density = parseFloat(obj.barcodeDensity);
                if (!isFinite(density) || density <= 0) {
                    density = 1;
                    obj.barcodeDensity = 1;
                }
                var displayW = Math.max(1, (obj.width || 100) * (obj.scaleX || 1));
                if (!obj.barcodeBaseWidth || obj.barcodeBaseWidth < 1) {
                    obj.barcodeBaseWidth = displayW / density;
                }
            }

            function barcodeUserSize(obj, box) {
                var density = parseFloat(obj.barcodeDensity);
                if (!isFinite(density) || density <= 0) density = 1;
                var userW = parseFloat(obj.barcodeSizeWidth);
                var userH = parseFloat(obj.barcodeSizeHeight);
                if (!isFinite(userW) || userW < 8) userW = box.width / density;
                if (!isFinite(userH) || userH < 8) userH = box.height;
                return { width: userW, height: userH, density: density };
            }

            function applyBarcodeFrame(obj, displayW, displayH, recordHistory, redraw) {
                if (!obj || !obj.isBarcode) return;
                var box = getBarcodeBox(obj);
                var canvasW = Math.max(40, canvas.getWidth() || displayW);
                displayW = Math.max(20, Math.min(canvasW - 2, displayW));
                displayH = Math.max(12, displayH);
                var centerX = box.left + box.width / 2;
                var left = centerX - displayW / 2;
                if (left < 0) left = 0;
                if (left + displayW > canvasW) left = Math.max(0, canvasW - displayW);
                var density = parseFloat(obj.barcodeDensity);
                if (!isFinite(density) || density <= 0) density = 1;
                obj.set({
                    barcodeDensity: density,
                    barcodeSizeWidth: displayW,
                    barcodeSizeHeight: displayH,
                    barcodeBaseWidth: displayW,
                    barcodeBoxLeft: left,
                    barcodeBoxTop: box.top,
                    barcodeBoxWidth: displayW,
                    barcodeBoxHeight: displayH
                });
                if (!redraw) return;
                if (previewMode) {
                    renderBarcodePreviewImage(obj, function() {
                        if (recordHistory) captureHistory();
                    });
                } else if (obj.type === 'rect') {
                    obj.set({
                        left: left,
                        top: box.top,
                        originX: 'left',
                        originY: 'top',
                        width: displayW,
                        height: displayH,
                        scaleX: 1,
                        scaleY: 1,
                        fill: '#cccccc'
                    });
                    if (typeof obj.setCoords === 'function') obj.setCoords();
                    canvas.requestRenderAll();
                    if (recordHistory) captureHistory();
                } else if (recordHistory) {
                    captureHistory();
                }
            }

            function applyBarcodeDensity(obj, densityPct, recordHistory, redraw) {
                if (!obj || !obj.isBarcode) return;
                var density = Math.max(0.5, Math.min(2, (parseFloat(densityPct) || 100) / 100));
                obj.set({ barcodeDensity: density });
                $('#propBarcodeDensity').val(Math.round(density * 100));
                $('#propBarcodeDensityVal').text(Math.round(density * 100) + '%');
                if (!redraw) return;
                if (previewMode) {
                    renderBarcodePreviewImage(obj, function() {
                        if (recordHistory) captureHistory();
                    });
                } else if (recordHistory) {
                    captureHistory();
                }
            }

            var densityRedrawTimer = null;
            function scheduleBarcodeDensityRedraw(obj, densityPct, recordHistory) {
                applyBarcodeDensity(obj, densityPct, false, false);
                if (densityRedrawTimer) {
                    clearTimeout(densityRedrawTimer);
                }
                densityRedrawTimer = setTimeout(function() {
                    densityRedrawTimer = null;
                    var active = canvas.getActiveObject();
                    var target = (active && active.isBarcode) ? active : obj;
                    if (!target || !target.isBarcode) return;
                    applyBarcodeDensity(target, $('#propBarcodeDensity').val(), !!recordHistory, true);
                }, 120);
            }

            function syncBarcodeBaseFromObject(obj) {
                if (!obj || !obj.isBarcode) return;
                obj.barcodeBaseWidth = Math.max(1, (obj.width || 100) * (obj.scaleX || 1));
            }

            function deleteActiveObject() {
                var obj = canvas.getActiveObject();
                if (!obj) return;
                canvas.remove(obj);
                canvas.discardActiveObject();
                canvas.requestRenderAll();
                $('#elementProperties').hide();
                $('#noElementSelected').show();
                updateChrome();
                captureHistory();
            }

            function classicFontSizeForField(fieldKey, heightMm) {
                // Match Classic print pt sizes at 10px/mm (1pt ≈ 0.353mm → ≈ 3.53px)
                var tall = (parseFloat(heightMm) || 25) > 35;
                var key = String(fieldKey || '').replace(/[{}@]/g, '').trim().toLowerCase();
                var map = {
                    company_name: tall ? 32 : 25,      // ~9pt / 7.2pt
                    company_address: tall ? 25 : 20,   // ~7.2pt / 5.8pt
                    address: tall ? 25 : 20,
                    product_name: tall ? 27 : 21,      // ~7.6pt / 6pt
                    mrp: tall ? 26 : 21,               // ~7.4pt / 6pt
                    selling_price: tall ? 28 : 23,     // ~8pt / 6.4pt
                    price: tall ? 28 : 23,
                    rate: tall ? 28 : 23,
                    barcode: tall ? 22 : 18,            // ~6.2pt / 5.2pt
                    barcode_text: tall ? 22 : 18,
                    sku: tall ? 22 : 18,
                    batch_no: tall ? 22 : 18,
                    wholesale: tall ? 22 : 18,
                    qty: tall ? 22 : 18,
                    other: tall ? 22 : 18
                };
                return map[key] || (tall ? 22 : 18);
            }

            function isTextObject(obj) {
                return obj && (obj.type === 'text' || obj.type === 'textbox' || obj.type === 'i-text');
            }

            function updateChrome() {
                var w = parseFloat($('#labelWidth').val()) || 35;
                var h = parseFloat($('#labelHeight').val()) || 25;
                var label = w + ' × ' + h + ' mm';
                $('#sizePill').text(label);
                $('#canvasWrapper').attr('data-size', label);
                // Safe-top guide = 3mm of label height
                var safePct = Math.max(6, Math.min(20, (3.0 / h) * 100));
                $('#safeTopGuide').css('height', safePct + '%');
                var count = canvas.getObjects().length || 0;
                $('#objectCountLabel').text(count + ' items' + (previewMode ? ' · preview' : ''));
                var empty = count === 0;
                $('#emptyState').toggleClass('show', empty && !previewMode);
            }

            function applyViewZoom() {
                $('#canvasWrapper').css('transform', 'scale(' + viewZoom + ')');
                $('#canvasWrapper').css('transform-origin', 'center center');
            }

            function normalizeTextObjectScale(obj) {
                if (!isTextObject(obj)) return;
                var sx = obj.scaleX || 1;
                var sy = obj.scaleY || 1;
                if (Math.abs(sx - 1) < 0.01 && Math.abs(sy - 1) < 0.01) {
                    obj.set({ scaleX: 1, scaleY: 1 });
                    return;
                }
                obj.set({
                    fontSize: Math.max(6, Math.round((obj.fontSize || 18) * sy)),
                    width: Math.max(20, (obj.width || 100) * sx),
                    scaleX: 1,
                    scaleY: 1,
                    objectCaching: false
                });
                if (typeof obj.setCoords === 'function') obj.setCoords();
            }

            function normalizeAllTextScales() {
                canvas.getObjects().forEach(normalizeTextObjectScale);
                canvas.renderAll();
                updateChrome();
            }

            function createVariableTextbox(content, left, top, opts) {
                opts = opts || {};
                var heightMm = parseFloat($('#labelHeight').val()) || 25;
                var fieldKey = String(content || '').replace(/[{}]/g, '').trim();
                var fontSize = opts.fontSize || classicFontSizeForField(fieldKey, heightMm);
                var width = opts.width || Math.max(120, canvas.getWidth() * 0.9);
                var text = new fabric.Textbox(String(content), {
                    left: left,
                    top: top,
                    width: width,
                    splitByGrapheme: false,
                    fontSize: fontSize,
                    fontFamily: defaultFontFamily || 'Arial, Helvetica, sans-serif',
                    fontWeight: 'bold',
                        fill: '#000000',
                    textAlign: opts.textAlign || 'center',
                    selectable: true,
                    objectCaching: false,
                    lockScalingFlip: true,
                    scaleX: 1,
                    scaleY: 1
                });
                if (opts.isVariable !== false) {
                    text.set('isVariable', true);
                    text.set('variableField', content);
                }
                return text;
            }

            function loadDefaultClassicLayout() {
                // Mirror Default (Classic) print: company → address → product → MRP+RATE → barcode → number
                var wMm = parseFloat($('#labelWidth').val()) || 35;
                var hMm = parseFloat($('#labelHeight').val()) || 25;
                var tall = hMm > 35;
                canvas.clear();
                canvas.setBackgroundColor('#ffffff', canvas.renderAll.bind(canvas));
                canvas.setWidth(wMm * scale);
                canvas.setHeight(hMm * scale);

                var padX = Math.round(1.8 * scale); // Classic side pad ~1.8mm
                var fullW = wMm * scale - (padX * 2);
                // Classic top pad — keep clear space above company title (matches print)
                var y = Math.round(3.0 * scale);
                var gap = tall ? 2 : 1;

                var company = createVariableTextbox('@{{company_name}}', padX, y, {
                    width: fullW, fontSize: classicFontSizeForField('company_name', hMm), textAlign: 'center'
                });
                y += company.fontSize + gap + 1;

                var address = createVariableTextbox('@{{company_address}}', padX, y, {
                    width: fullW, fontSize: classicFontSizeForField('company_address', hMm), textAlign: 'center'
                });
                y += address.fontSize + gap + 2;

                var product = createVariableTextbox('@{{product_name}}', padX, y, {
                    width: fullW, fontSize: classicFontSizeForField('product_name', hMm), textAlign: 'center'
                });
                y += product.fontSize + gap + 2;

                // One centered price line like Classic (MRP-70  RATE-50)
                var priceLine = createVariableTextbox('@{{mrp}}  @{{selling_price}}', padX, y, {
                    width: fullW,
                    fontSize: classicFontSizeForField('selling_price', hMm),
                    textAlign: 'center',
                    isVariable: false
                });
                priceLine.set('isVariable', false);
                priceLine.set('variableField', null);
                priceLine.set('isPriceLine', true);
                y += priceLine.fontSize + (tall ? 5 : 3);

                var barcodeH = Math.round(5.4 * scale); // Classic .bc-wrap is 5.4mm
                barcodeH = Math.max(54, Math.min(barcodeH, tall ? 90 : 60));
                var barcodeW = fullW * 0.94; // Classic .bc-wrap width 94%
                var barcode = new fabric.Rect({
                    left: padX + (fullW - barcodeW) / 2,
                    top: y,
                    width: barcodeW,
                    height: barcodeH,
                    fill: '#cccccc',
                        selectable: true
                    });
                barcode.set('isBarcode', true);
                markAsBarcode(barcode, 1);
                y += barcodeH + (tall ? 4 : 2);

                var barcodeNo = createVariableTextbox('@{{barcode}}', padX, y, {
                    width: fullW, fontSize: classicFontSizeForField('barcode', hMm), textAlign: 'center'
                });

                canvas.add(company, address, product, priceLine, barcode, barcodeNo);
                canvas.discardActiveObject();
                ensureSafeCanvasMargins();
                if (previewMode) {
                    applyPreviewToCanvas();
                } else {
                    canvas.renderAll();
                    updateChrome();
                }
                resetHistory();
            }

            function placeElement(type, content, atCenter) {
                var dropX = atCenter ? canvas.getWidth() * 0.08 : 20;
                var dropY = atCenter ? Math.max(8, canvas.getObjects().length * 18) : 20;

                if (type === 'text' || type === 'variable') {
                    var text = createVariableTextbox(String(content), dropX, dropY, {
                        isVariable: type === 'variable',
                        width: Math.max(140, canvas.getWidth() * 0.84),
                        textAlign: 'center'
                    });
                    if (type !== 'variable') {
                        text.set('isVariable', false);
                        text.set('variableField', null);
                    }
                    canvas.add(text);
                    canvas.setActiveObject(text);
                    if (previewMode && type === 'variable') {
                        applyPreviewToCanvas();
                }
                } else if (type === 'barcode') {
                    var rect = new fabric.Rect({
                        left: dropX,
                        top: dropY,
                        width: Math.max(140, canvas.getWidth() * 0.85),
                        height: 45,
                        fill: previewMode ? '#222222' : '#cccccc',
                        selectable: true
                    });
                    markAsBarcode(rect, 1);
                    canvas.add(rect);
                    canvas.setActiveObject(rect);
                }
                canvas.renderAll();
                updatePropertiesPanel();
                updateChrome();
                captureHistory();
            }

            function markAsBarcode(rect, density) {
                density = parseFloat(density);
                if (!isFinite(density) || density <= 0) density = 1;
                var w = Math.max(1, (rect.width || 100) * (rect.scaleX || 1));
                var h = Math.max(1, (rect.height || 40) * (rect.scaleY || 1));
                rect.set('isBarcode', true);
                rect.set('barcodeDensity', density);
                rect.set('barcodeBaseWidth', w);
                rect.set('barcodeBoxLeft', rect.left || 0);
                rect.set('barcodeBoxTop', rect.top || 0);
                rect.set('barcodeBoxWidth', w);
                rect.set('barcodeBoxHeight', h);
                rect.set('centeredRotation', true);
                return rect;
            }

            function ensureSafeCanvasMargins() {
                // Keep ~3mm top clear WITHOUT squashing gaps between text and barcode.
                // Only shift down; if bottom overflows, shrink barcodes — never compress text spacing.
                var objs = canvas.getObjects();
                if (!objs.length) return false;
                var padTop = 3.0 * scale;
                var padLeft = 1.8 * scale;
                var padRight = 1.8 * scale;
                var padBottom = 1.0 * scale;
                var canvasW = canvas.getWidth();
                var canvasH = canvas.getHeight();
                var minBarcodeH = 3.2 * scale; // allow shorter than classic if needed to keep gaps
                var minGap = 0.6 * scale; // min space between text above and barcode

                function objBox(obj) {
                    var left = obj.left || 0;
                    var top = obj.top || 0;
                    var w = Math.max(1, (obj.width || 1) * (obj.scaleX || 1));
                    var h = Math.max(1, (obj.height || 1) * (obj.scaleY || 1));
                    if (obj.isBarcode && obj.barcodeBoxWidth && obj.barcodeBoxHeight) {
                        left = obj.barcodeBoxLeft != null ? obj.barcodeBoxLeft : left;
                        top = obj.barcodeBoxTop != null ? obj.barcodeBoxTop : top;
                        w = obj.barcodeBoxWidth;
                        h = obj.barcodeBoxHeight;
                    }
                    var ox = obj.originX || 'left';
                    var oy = obj.originY || 'top';
                    if (ox === 'center') left -= w / 2;
                    else if (ox === 'right') left -= w;
                    if (oy === 'center') top -= h / 2;
                    else if (oy === 'bottom') top -= h;
                    return { left: left, top: top, width: w, height: h };
                }

                function setBoxTopLeft(obj, boxLeft, boxTop, boxW, boxH) {
                    var ox = obj.originX || 'left';
                    var oy = obj.originY || 'top';
                    var left = boxLeft;
                    var top = boxTop;
                    if (ox === 'center') left = boxLeft + boxW / 2;
                    else if (ox === 'right') left = boxLeft + boxW;
                    if (oy === 'center') top = boxTop + boxH / 2;
                    else if (oy === 'bottom') top = boxTop + boxH;
                    var patch = { left: left, top: top };
                    if (obj.isBarcode) {
                        var curH = Math.max(1, (obj.height || 1) * (obj.scaleY || 1));
                        var curW = Math.max(1, (obj.width || 1) * (obj.scaleX || 1));
                        if (Math.abs(boxH - curH) > 0.5) {
                            patch.scaleY = (obj.scaleY || 1) * (boxH / curH);
                        }
                        if (Math.abs(boxW - curW) > 0.5) {
                            patch.scaleX = (obj.scaleX || 1) * (boxW / curW);
                        }
                        obj.barcodeBoxLeft = boxLeft;
                        obj.barcodeBoxTop = boxTop;
                        obj.barcodeBoxWidth = boxW;
                        obj.barcodeBoxHeight = boxH;
                    }
                    obj.set(patch);
                    if (typeof obj.setCoords === 'function') obj.setCoords();
                }

                // 1) Shift everything down if top is too high
                var minTop = Infinity;
                var minLeft = Infinity;
                objs.forEach(function(obj) {
                    var b = objBox(obj);
                    minTop = Math.min(minTop, b.top);
                    minLeft = Math.min(minLeft, b.left);
                });
                if (!isFinite(minTop)) return false;

                var shiftY = minTop < padTop ? (padTop - minTop) : 0;
                var shiftX = minLeft < padLeft ? (padLeft - minLeft) : 0;
                if (shiftY || shiftX) {
                    objs.forEach(function(obj) {
                        var b = objBox(obj);
                        setBoxTopLeft(obj, b.left + shiftX, b.top + shiftY, b.width, b.height);
                    });
                }

                // 2) Enforce minimum gap between text above barcode and barcode top
                objs.filter(function(o) { return o.isBarcode; }).forEach(function(bc) {
                    var bb = objBox(bc);
                    var textBottom = -Infinity;
                    objs.forEach(function(obj) {
                        if (obj.isBarcode) return;
                        if (!(obj.type === 'i-text' || obj.type === 'text' || obj.type === 'textbox')) return;
                        var tb = objBox(obj);
                        // Only consider text that overlaps horizontally with barcode
                        if (tb.left + tb.width < bb.left || tb.left > bb.left + bb.width) return;
                        if (tb.top + tb.height <= bb.top + 2) {
                            textBottom = Math.max(textBottom, tb.top + tb.height);
                        }
                    });
                    if (isFinite(textBottom) && textBottom > -Infinity) {
                        var needTop = textBottom + minGap;
                        if (bb.top < needTop) {
                            var push = needTop - bb.top;
                            setBoxTopLeft(bc, bb.left, bb.top + push, bb.width, bb.height);
                            bb = objBox(bc);
                        }
                    }
                });

                // 3) If anything past bottom, shrink barcodes only (preserve text gaps)
                var maxBottom = -Infinity;
                objs.forEach(function(obj) {
                    var b = objBox(obj);
                    maxBottom = Math.max(maxBottom, b.top + b.height);
                });
                var limitBottom = canvasH - padBottom;
                if (maxBottom > limitBottom + 1) {
                    var overflow = maxBottom - limitBottom;
                    objs.filter(function(o) { return o.isBarcode; }).forEach(function(bc) {
                        if (overflow <= 0) return;
                        var bb = objBox(bc);
                        var canShrink = Math.max(0, bb.height - minBarcodeH);
                        var shrink = Math.min(canShrink, overflow);
                        if (shrink > 0) {
                            setBoxTopLeft(bc, bb.left, bb.top, bb.width, bb.height - shrink);
                            overflow -= shrink;
                        }
                    });
                }

                // 4) Side clamp — nudge left if too far right
                objs.forEach(function(obj) {
                    var b = objBox(obj);
                    var maxRight = canvasW - padRight;
                    if (b.left + b.width > maxRight + 1) {
                        var nl = Math.max(padLeft, maxRight - b.width);
                        setBoxTopLeft(obj, nl, b.top, Math.min(b.width, maxRight - padLeft), b.height);
                    }
                });

                canvas.requestRenderAll();
                return true;
            }

            function fabricReviver(o, object) {
                if (!o || !object) return;
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
                if (o.barcodeSizeWidth !== undefined) object.barcodeSizeWidth = o.barcodeSizeWidth;
                if (o.barcodeSizeHeight !== undefined) object.barcodeSizeHeight = o.barcodeSizeHeight;
                if (object.isBarcode) ensureBarcodeMeta(object);
            }

            function loadTemplateById(id) {
                // Product list uses custom_3; API expects numeric 3
                id = String(id || '').replace(/^custom_/, '').trim();
                if (!id) {
                    currentTemplateId = null;
                    syncDeleteTemplateButton();
                    canvas.clear();
                    canvas.setBackgroundColor('#ffffff', canvas.renderAll.bind(canvas));
                    updateChrome();
                    resetHistory();
                    return;
                }
                $.ajax({
                    url: '/api/barcode-templates/' + encodeURIComponent(id),
                    method: 'GET',
                    success: function(res) {
                        if (!res.status || !res.data) {
                            Swal.fire('Error', 'Could not load template', 'error');
                            return;
                        }
                        var tpl = res.data;
                        currentTemplateId = tpl.id;
                        syncDeleteTemplateButton();
                        $('#labelWidth').val(tpl.width_mm);
                        $('#labelHeight').val(tpl.height_mm);
                        canvas.setWidth((parseFloat(tpl.width_mm) || 35) * scale);
                        canvas.setHeight((parseFloat(tpl.height_mm) || 25) * scale);

                        var design = tpl.design_json;
                        if (typeof design === 'string') {
                            try { design = JSON.parse(design); } catch (e) { design = null; }
                        }
                        if (design && (design.objects || design.version)) {
                            canvas.loadFromJSON(design, function() {
                                normalizeAllTextScales();
                                // Do NOT auto-shift layout on load — keep saved positions exactly
                                if (previewMode) {
                                    applyPreviewToCanvas();
                                } else {
                    canvas.renderAll();
                                    updateChrome();
                                }
                                if (canvas.getObjects().length === 0) {
                                    // Saved empty — offer default
                                    $('#emptyState').addClass('show');
                                }
                                resetHistory();
                            }, fabricReviver);
                        } else {
                            canvas.clear();
                            canvas.setBackgroundColor('#ffffff', canvas.renderAll.bind(canvas));
                            updateChrome();
                            resetHistory();
                            Swal.fire({
                                icon: 'info',
                                title: 'Empty template',
                                text: 'This template has no design yet. Load the default layout to start.',
                                confirmButtonText: 'Use default layout',
                                showCancelButton: true
                            }).then(function(r) {
                                if (r.isConfirmed) loadDefaultClassicLayout();
                            });
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Could not load template', 'error');
                    }
                });
            }

            $('#btnTogglePreview').on('click', function() {
                if (!previewMode && canvas.getObjects().length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Nothing to preview',
                        text: 'Add fields or load Default layout first.',
                        confirmButtonColor: '#ff9f43'
                    });
                    return;
                }
                setPreviewMode(!previewMode);
            });

            // Fabric rotates around originX/originY; CSS needs matching transform-origin
            // after left/top is converted to the unrotated box top-left.
            // Fabric default centeredRotation=true → rotate around center.
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

            function buildDesignerTestPrintHtml(layersOrUrl, widthMm, heightMm) {
                widthMm = parseFloat(widthMm) || 35;
                heightMm = parseFloat(heightMm) || 25;
                var isLayers = layersOrUrl && typeof layersOrUrl === 'object' && Array.isArray(layersOrUrl.elements);
                function esc(s) {
                    return String(s == null ? '' : s)
                        .replace(/&/g, '&amp;').replace(/"/g, '&quot;')
                        .replace(/</g, '&lt;').replace(/>/g, '&gt;');
                }
                function mmToPt(mm) {
                    return (parseFloat(mm) || 0) * 2.834645669;
                }
                var inner = '';
                if (isLayers) {
                    inner = '<div class="label-abs">';
                    layersOrUrl.elements.forEach(function(el) {
                        if (el.type === 'text') {
                            var pt = Math.max(5, Math.round(mmToPt(el.fontSizeMm)));
                            var wrapCss = el.wrap
                                ? 'white-space:pre-wrap;word-break:break-word;overflow:visible;height:' + el.heightMm.toFixed(3) + 'mm;'
                                : 'white-space:nowrap;';
                            var textHtml = esc(el.text).replace(/\r\n|\n|\r/g, '<br>');
                            inner += '<div class="lbl-txt' + (el.wrap ? ' wrap' : '') + '" style="left:' + el.leftMm.toFixed(3) + 'mm;top:' + el.topMm.toFixed(3) +
                                'mm;width:' + el.widthMm.toFixed(3) + 'mm;' +
                                'font-size:' + pt + 'pt;font-family:Arial, Helvetica, sans-serif;' +
                                'font-weight:800;font-style:' + (el.fontStyle || 'normal') +
                                ';text-align:' + (el.textAlign || 'left') + ';' + wrapCss +
                                cssRotationInline(el.angle, el.transformOrigin) +
                                '">' + textHtml + '</div>';
                        } else if (el.type === 'barcode' || el.type === 'image') {
                            inner += '<div class="lbl-bc" style="left:' + el.leftMm.toFixed(3) + 'mm;top:' + el.topMm.toFixed(3) +
                                'mm;width:' + el.widthMm.toFixed(3) + 'mm;height:' + el.heightMm.toFixed(3) +
                                'mm;' + cssRotationInline(el.angle, el.transformOrigin) +
                                '"><img src="' + esc(el.src) + '" alt="label"></div>';
                        }
                    });
                    inner += '</div>';
                } else {
                    inner = '<img class="lbl-img" src="' + esc(layersOrUrl) + '" alt="label">';
                }
                return '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Test Print Label</title><style>' +
                    '@page { size: ' + widthMm + 'mm ' + heightMm + 'mm; margin: 0; }' +
                    '* { box-sizing: border-box; margin: 0; padding: 0; border: 0;' +
                    ' -webkit-font-smoothing: none; font-smooth: never; text-rendering: geometricPrecision; }' +
                    'html, body { margin: 0; padding: 0; background: #fff; width: ' + widthMm + 'mm;' +
                    ' font-family: Arial, Helvetica, sans-serif; color: #000; }' +
                    '.page { width: ' + widthMm + 'mm; height: ' + heightMm + 'mm; overflow: hidden; position: relative; }' +
                    '.col { width: ' + widthMm + 'mm; height: ' + heightMm + 'mm; overflow: hidden; position: relative; background: #fff; }' +
                    '.label-abs { position: absolute; left: 0; top: 0; width: 100%; height: 100%; }' +
                    '.lbl-txt { position: absolute; overflow: visible; line-height: 1.15; color: #000; font-weight: 800;' +
                    ' letter-spacing: 0.03em; -webkit-text-stroke: 0.28px #000;' +
                    ' text-shadow: 0.2px 0 0 #000, -0.2px 0 0 #000; }' +
                    '.lbl-txt.wrap { white-space: pre-wrap; word-break: break-word; }' +
                    '.lbl-bc { position: absolute; overflow: hidden; background: #fff; }' +
                    '.lbl-bc img { display: block; width: 100%; height: 100%; object-fit: fill;' +
                    ' shape-rendering: crispEdges; image-rendering: pixelated; image-rendering: crisp-edges;' +
                    ' -ms-interpolation-mode: nearest-neighbor;' +
                    ' -webkit-print-color-adjust: exact; print-color-adjust: exact; }' +
                    '.lbl-img { width: 100%; height: 100%; display: block; object-fit: fill; object-position: center top; }' +
                    '</style></head><body><div class="page"><div class="col">' + inner + '</div></div>' +
                    '<script>(function(){var closed=false;' +
                    'function closePrintWindow(){if(closed)return;closed=true;try{window.close();}catch(e){}}' +
                    'function startPrint(){try{window.focus();window.print();}catch(e){}' +
                    'setTimeout(function(){if(document.hasFocus())closePrintWindow();},600);}' +
                    'window.addEventListener("afterprint",closePrintWindow);' +
                    'try{window.onafterprint=closePrintWindow;}catch(e){}' +
                    'window.addEventListener("load",function(){setTimeout(startPrint,300);});' +
                    '})();<\/script>' +
                    '</body></html>';
            }

            // Fabric aligns text inside its own box with canvas metrics; redoing that in CSS
            // shifts fields and overlaps them, so print left-aligned from the real glyph start.
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

            function buildDesignerBarcodeSvg(code, density, targetWpx, targetHpx, maxWpx) {
                return buildBarcodeDataUrl(code, density, targetWpx, targetHpx, maxWpx, scale);
            }

            function applySampleTextToExportObject(obj) {
                if (!(obj.type === 'i-text' || obj.type === 'text' || obj.type === 'textbox')) return;
                var txt = String(obj.text || '');
                if (obj.isPriceLine || (/\{\{\s*mrp\s*\}\}/i.test(txt) && /\{\{\s*(selling_price|price|rate)\s*\}\}/i.test(txt)) ||
                    (/^MRP-/i.test(txt) && /RATE-/i.test(txt))) {
                    obj.set('text', (sampleData.mrp || 'MRP-70') + '  ' + (sampleData.selling_price || 'RATE-50'));
                    return;
                }
                if (obj.isVariable && obj.variableField) {
                    var key = fieldKey(obj.variableField);
                    var val = sampleData[key];
                    if (val === undefined || val === null || val === '') {
                        val = placeholderForKey(key);
                    }
                    if (key === 'company_name' || key === 'company_address' || key === 'address' || key === 'product_name') {
                        val = String(val).toUpperCase();
                    }
                    obj.set('text', String(val));
                    return;
                }
                txt = txt.replace(/\{\{\s*product_name\s*\}\}/gi, sampleData.product_name || '');
                txt = txt.replace(/\{\{\s*company_name\s*\}\}/gi, sampleData.company_name || '');
                txt = txt.replace(/\{\{\s*(company_address|address)\s*\}\}/gi, sampleData.company_address || '');
                txt = txt.replace(/\{\{\s*barcode_text\s*\}\}/gi, sampleData.barcode || '');
                txt = txt.replace(/\{\{\s*barcode\s*\}\}/gi, sampleData.barcode || '');
                txt = txt.replace(/\{\{\s*mrp\s*\}\}/gi, sampleData.mrp || '');
                txt = txt.replace(/\{\{\s*(price|selling_price|rate)\s*\}\}/gi, sampleData.selling_price || '');
                txt = txt.replace(/\{\{\s*batch_no\s*\}\}/gi, sampleData.batch_no || '');
                txt = txt.replace(/\{\{\s*(label_code|sku|wholesale)\s*\}\}/gi, sampleData.wholesale || sampleData.sku || '');
                txt = txt.replace(/\{\{\s*(qty|other)\s*\}\}/gi, sampleData.other || sampleData.qty || '');
                obj.set('text', txt);
            }

            function renderDesignerTestPrintLayers() {
                return new Promise(function(resolve, reject) {
                    syncSampleFromInputs();
                    var widthMm = parseFloat($('#labelWidth').val()) || 35;
                    var heightMm = parseFloat($('#labelHeight').val()) || 25;
                    var designScale = 10;
                    var designWidthPx = Math.round(widthMm * designScale);
                    var designHeightPx = Math.round(heightMm * designScale);
                    var holder = document.createElement('canvas');
                    holder.width = designWidthPx;
                    holder.height = designHeightPx;
                    holder.style.position = 'fixed';
                    holder.style.left = '-10000px';
                    document.body.appendChild(holder);

                    var exportCanvas = new fabric.StaticCanvas(holder, {
                        width: designWidthPx,
                        height: designHeightPx,
                        backgroundColor: '#ffffff',
                        renderOnAddRemove: false,
                        enableRetinaScaling: false,
                        imageSmoothingEnabled: true
                    });

                    function cleanup() {
                        try { exportCanvas.dispose(); } catch (e) {}
                        if (holder && holder.parentNode) holder.parentNode.removeChild(holder);
                    }

                    var design = canvas.toJSON(['isVariable', 'variableField', 'isBarcode', 'isPriceLine', 'barcodeDensity', 'barcodeBaseWidth', 'barcodeBoxLeft', 'barcodeBoxTop', 'barcodeBoxWidth', 'barcodeBoxHeight', 'barcodeSizeWidth', 'barcodeSizeHeight', 'barcodePreviewImage']);
                    exportCanvas.loadFromJSON(design, function() {
                        exportCanvas.getObjects().forEach(function(obj) {
                            applySampleTextToExportObject(obj);
                            // Bake stretch out of text like product print
                            if (obj.type === 'i-text' || obj.type === 'text' || obj.type === 'textbox') {
                                var sx = obj.scaleX || 1;
                                var sy = obj.scaleY || 1;
                                var fontSize = parseFloat(obj.fontSize) || 18;
                                var width = parseFloat(obj.width) || 100;
                                if (Math.abs(sx - 1) > 0.01 || Math.abs(sy - 1) > 0.01) {
                                    fontSize = Math.max(6, Math.round(fontSize * sy));
                                    width = Math.max(20, width * sx);
                                    obj.set({ fontSize: fontSize, width: width, scaleX: 1, scaleY: 1, fill: '#000000' });
                                }
                            }
                        });

                        var elements = [];
                        exportCanvas.getObjects().forEach(function(obj) {
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
                            var angleObj = parseFloat(obj.angle) || 0;
                            var originCss = fabricOriginToCss(ox, oy, obj);

                            if (obj.isBarcode) {
                                var dens = parseFloat(obj.barcodeDensity) || 1;
                                var drawn = buildDesignerBarcodeSvg(
                                    sampleData.barcode || '54538',
                                    dens,
                                    boxW,
                                    boxH,
                                    boxW
                                );
                                var src = drawn && drawn.src ? drawn.src : (typeof drawn === 'string' ? drawn : '');
                                if (!src) return;
                                elements.push({
                                    type: 'barcode',
                                    leftMm: leftPx / designScale,
                                    topMm: topPx / designScale,
                                    widthMm: Math.max(1, boxW / designScale),
                                    heightMm: Math.max(2, boxH / designScale),
                                    angle: angleObj,
                                    transformOrigin: originCss,
                                    src: src
                                });
                                return;
                            }

                            if (obj.type === 'i-text' || obj.type === 'text' || obj.type === 'textbox') {
                                var payload = fabricPrintTextPayload(obj);
                                var textVal = payload.text;
                                if (!textVal) return;
                                var fontSizePx = parseFloat(obj.fontSize) || 18;
                                var fontSizeMm = fontSizePx / designScale;
                                var useLeft = leftPx;
                                var useW = boxW;
                                var useAlign = obj.textAlign || 'left';
                                if (!payload.wrap && Math.abs(angleObj) < 0.5) {
                                    var glyph = resolveTextGlyphBox(obj, leftPx, boxW);
                                    useLeft = glyph.leftPx;
                                    useW = glyph.widthPx;
                                    useAlign = glyph.align;
                                }
                                var minH = fontSizeMm * 1.2 * Math.max(1, payload.lineCount);
                                var outWmm = Math.max(2, useW / designScale);
                                var outHmm = Math.max(minH, boxH / designScale);
                                var outLeftMm = useLeft / designScale;
                                var outTopMm = topPx / designScale;

                                // Same weight as the designer. No extra stroke, so print is not overly bold or clipped.
                                var textImgSrc = '';
                                try {
                                    obj.set({
                                        fill: '#000000',
                                        stroke: null,
                                        strokeWidth: 0,
                                        paintFirst: 'fill',
                                        objectCaching: false
                                    });
                                    if (typeof obj.setCoords === 'function') obj.setCoords();
                                    textImgSrc = obj.toDataURL({
                                        format: 'png',
                                        multiplier: 4,
                                        enableRetinaScaling: false,
                                        withoutTransform: true,
                                        withoutShadow: true
                                    });
                                } catch (eRaster) {
                                    textImgSrc = '';
                                }

                                if (textImgSrc) {
                                    elements.push({
                                        type: 'image',
                                        leftMm: outLeftMm,
                                        topMm: outTopMm,
                                        widthMm: outWmm,
                                        heightMm: outHmm,
                                        angle: angleObj,
                                        transformOrigin: originCss,
                                        src: textImgSrc
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
                                        angle: angleObj,
                                        transformOrigin: originCss
                                    });
                                }
                            }
                        });

                        cleanup();
                        resolve({ elements: elements, printWidthMm: widthMm, printHeightMm: heightMm });
                    }, function(o, object) {
                        if (!o || !object) return;
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
                if (o.barcodeSizeWidth !== undefined) object.barcodeSizeWidth = o.barcodeSizeWidth;
                if (o.barcodeSizeHeight !== undefined) object.barcodeSizeHeight = o.barcodeSizeHeight;
                        if (o.barcodePreviewImage !== undefined) object.barcodePreviewImage = o.barcodePreviewImage;
                    });
                });
            }

            $('#btnTestPrint').on('click', function() {
                if (canvas.getObjects().length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Nothing to print',
                        text: 'Add fields or load Default layout first.',
                        confirmButtonColor: '#ff9f43'
                    });
                    return;
                }
                var $btn = $(this);
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Printing…');
                syncSampleFromInputs();
                renderDesignerTestPrintLayers().then(function(layers) {
                    var html = buildDesignerTestPrintHtml(layers, layers.printWidthMm, layers.printHeightMm);
                    var features = 'popup=yes,width=920,height=720,scrollbars=yes,resizable=yes';
                    var w = window.open('about:blank', 'barcodeDesignerPrintWin', features);
                    if (!w) {
                        w = window.open('about:blank', '_blank');
                    }
                    if (!w) {
                        Swal.fire('Popup blocked', 'Allow popups for this site to test print.', 'warning');
                        return;
                    }
                    try { w.opener = null; } catch (e) {}
                    w.document.open();
                    w.document.write(html);
                    w.document.close();
                    try { w.focus(); } catch (e) {}
                }).catch(function(err) {
                    Swal.fire('Error', (err && err.message) ? err.message : 'Failed to prepare test print', 'error');
                }).finally(function() {
                    $btn.prop('disabled', false).html('<i class="fas fa-print"></i> Test print');
                });
            });

            $(document).on('input', '.sample-input', function() {
                if (!previewMode) return;
                syncSampleFromInputs();
                applyPreviewToCanvas();
            });

            $('#btnApplySize').on('click', function() {
                var w = parseFloat($('#labelWidth').val()) || 35;
                var h = parseFloat($('#labelHeight').val()) || 25;
                canvas.setWidth(w * scale);
                canvas.setHeight(h * scale);
                    canvas.renderAll();
                updateChrome();
            });

            function confirmDefaultLayout() {
                if (canvas.getObjects().length === 0) {
                    loadDefaultClassicLayout();
                    return;
                }
                Swal.fire({
                    title: 'Replace current design?',
                    text: 'Default layout will replace items currently on the label.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Load default',
                    confirmButtonColor: '#ff9f43'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        if (!$('#labelWidth').val()) $('#labelWidth').val(35);
                        if (!$('#labelHeight').val()) $('#labelHeight').val(25);
                        loadDefaultClassicLayout();
                    }
                });
            }

            $('#btnLoadClassicLayout, #btnEmptyDefault').on('click', confirmDefaultLayout);

            $('#btnZoomIn').on('click', function() {
                viewZoom = Math.min(2.2, viewZoom + 0.15);
                applyViewZoom();
            });
            $('#btnZoomOut').on('click', function() {
                viewZoom = Math.max(0.6, viewZoom - 0.15);
                applyViewZoom();
            });
            $('#btnZoomReset').on('click', function() {
                viewZoom = 1;
                applyViewZoom();
            });

            // Click to add (user-friendly) + drag support
            $('.bd-tool').on('click', function() {
                placeElement($(this).data('type'), $(this).data('content'), true);
            });
            $('.bd-tool').on('dragstart', function(e) {
                draggedElement = {
                    type: $(this).data('type'),
                    content: $(this).data('content')
                };
                e.originalEvent.dataTransfer.setData('text/plain', 'tool');
            });
            $('#canvasWrapper').on('dragover', function(e) { e.preventDefault(); });
            $('#canvasWrapper').on('drop', function(e) {
                e.preventDefault();
                if (!draggedElement) return;
                var offset = $(this).offset();
                var dropX = (e.originalEvent.clientX - offset.left) / viewZoom;
                var dropY = (e.originalEvent.clientY - offset.top) / viewZoom;
                var type = draggedElement.type;
                var content = draggedElement.content;
                draggedElement = null;

                if (type === 'text' || type === 'variable') {
                    var text = createVariableTextbox(String(content), dropX, dropY, {
                        isVariable: type === 'variable',
                        width: Math.max(140, canvas.getWidth() * 0.7),
                        textAlign: 'center'
                    });
                    if (type !== 'variable') {
                        text.set('isVariable', false);
                        text.set('variableField', null);
                    }
                    canvas.add(text);
                    canvas.setActiveObject(text);
                    if (previewMode && type === 'variable') {
                        applyPreviewToCanvas();
                    }
                } else if (type === 'barcode') {
                    var rect = new fabric.Rect({
                        left: dropX, top: dropY,
                        width: Math.max(140, canvas.getWidth() * 0.85),
                        height: 45,
                        fill: previewMode ? '#222222' : '#cccccc',
                        selectable: true
                    });
                    markAsBarcode(rect, 1);
                    canvas.add(rect);
                    canvas.setActiveObject(rect);
                }
                    canvas.renderAll();
                updatePropertiesPanel();
                updateChrome();
                captureHistory();
            });

            canvas.on('selection:created', updatePropertiesPanel);
            canvas.on('selection:updated', updatePropertiesPanel);
            canvas.on('selection:cleared', function() {
                $('#elementProperties').hide();
                $('#noElementSelected').show();
            });
            var barcodeInteractMode = null; // 'moving' | 'scaling' | null

            canvas.on('object:moving', function(e) {
                if (e && e.target && e.target.isBarcode) {
                    barcodeInteractMode = 'moving';
                    // Move only — keep designed width/height; map position via center when rotated
                    var keepW = e.target.barcodeBoxWidth || Math.max(1, (e.target.width || 1) * (e.target.scaleX || 1));
                    var keepH = e.target.barcodeBoxHeight || Math.max(1, (e.target.height || 1) * (e.target.scaleY || 1));
                    var cx = e.target.left || 0;
                    var cy = e.target.top || 0;
                    try {
                        if (typeof e.target.getCenterPoint === 'function') {
                            var c = e.target.getCenterPoint();
                            cx = c.x;
                            cy = c.y;
                        }
                    } catch (err) {}
                    e.target.barcodeBoxLeft = cx - keepW / 2;
                    e.target.barcodeBoxTop = cy - keepH / 2;
                    e.target.barcodeBoxWidth = keepW;
                    e.target.barcodeBoxHeight = keepH;
                }
                updatePropertiesPanel();
            });
            canvas.on('object:scaling', function(e) {
                if (e && e.target && e.target.isBarcode) {
                    barcodeInteractMode = 'scaling';
                }
                updatePropertiesPanel();
            });
            canvas.on('object:rotating', function(e) {
                if (e && e.target && e.target.isBarcode) {
                    barcodeInteractMode = 'rotating';
                }
                updatePropertiesPanel();
            });
            canvas.on('object:modified', function(e) {
                if (e && e.target) {
                    if (isTextObject(e.target)) {
                        normalizeTextObjectScale(e.target);
                    } else if (e.target.isBarcode) {
                        var dens = parseFloat(e.target.barcodeDensity) || 1;
                        var occupancy = densityToOccupancy(dens);
                        var keepAngle = parseFloat(e.target.angle) || 0;
                        var displayW = e.target.barcodeBoxWidth || Math.max(1, (e.target.width || 100) * (e.target.scaleX || 1));
                        var displayH = e.target.barcodeBoxHeight || Math.max(1, (e.target.height || 40) * (e.target.scaleY || 1));
                        var cx = e.target.left || 0;
                        var cy = e.target.top || 0;
                        try {
                            if (typeof e.target.getCenterPoint === 'function') {
                                var cPt = e.target.getCenterPoint();
                                cx = cPt.x;
                                cy = cPt.y;
                            }
                        } catch (err) {}

                        if (barcodeInteractMode === 'moving' || barcodeInteractMode === 'rotating') {
                            // Move / rotate only — never rewrite size or wipe angle
                            e.target.set({
                                barcodeDensity: dens,
                                barcodeBaseWidth: displayW,
                                barcodeBoxLeft: cx - displayW / 2,
                                barcodeBoxTop: cy - displayH / 2,
                                barcodeBoxWidth: displayW,
                                barcodeBoxHeight: displayH,
                                angle: keepAngle,
                                centeredRotation: true
                            });
                            // No re-render (re-render was resetting angle to 0)
                        } else {
                            var visualW = Math.max(20, (e.target.width || 1) * (e.target.scaleX || 1));
                            var visualH = Math.max(12, (e.target.height || 1) * (e.target.scaleY || 1));
                            e.target.set({
                                left: cx,
                                top: cy,
                                originX: 'center',
                                originY: 'center'
                            });
                            applyBarcodeFrame(e.target, visualW, visualH, false, previewMode && !!e.target.barcodePreviewImage);
                        }
                        barcodeInteractMode = null;
                    }
                    canvas.requestRenderAll();
                    updatePropertiesPanel();
                }
                updateChrome();
                captureHistory();
            });
            canvas.on('object:added object:removed', updateChrome);

            function updatePropertiesPanel() {
                var obj = canvas.getActiveObject();
                if (!obj) return;
                $('#noElementSelected').hide();
                $('#elementProperties').show();
                $('#propX').val(((obj.left || 0) / scale).toFixed(1));
                $('#propY').val(((obj.top || 0) / scale).toFixed(1));
                if (isTextObject(obj)) {
                    $('#propTextWrap, #propTextStyleWrap').show();
                    $('#propBarcodeWrap').hide();
                    $('#propText').val(obj.text).prop('disabled', false);
                    $('#propFontSize').val(obj.fontSize).prop('disabled', false);
                    setFontPickerUI(obj.fontFamily || defaultFontFamily);
                    $('#bdFontTrigger').prop('disabled', false);
                    var align = obj.textAlign || 'left';
                    $('#propAlign').val(align);
                    $('.bd-align-btns .btn').removeClass('active').prop('disabled', false);
                    $('.bd-align-btns .btn[data-align="' + align + '"]').addClass('active');
                    $('#btnPropBold').prop('disabled', false)
                        .toggleClass('active', obj.fontWeight === 'bold' || obj.fontWeight === '700');
                    $('#btnPropItalic').prop('disabled', false)
                        .toggleClass('active', obj.fontStyle === 'italic');
                } else if (obj.isBarcode) {
                    $('#propTextWrap, #propTextStyleWrap').hide();
                    $('#propBarcodeWrap').show();
                    ensureBarcodeMeta(obj);
                    var densityPct = Math.round((parseFloat(obj.barcodeDensity) || 1) * 100);
                    $('#propBarcodeDensity').val(densityPct);
                    $('#propBarcodeDensityVal').text(densityPct + '%');
                    var scanCode = sampleData.barcode || sampleData.barcode_text || '54538';
                    var scanBox = getBarcodeBox(obj);
                    var scanLayout = layoutBarcodeDraw(scanCode, parseFloat(obj.barcodeDensity) || 1, scanBox.width, scanBox.width, scale, scanBox.height);
                    showBarcodeScanHint(scanLayout.tooThin, scanLayout.grew);
                    var box = getBarcodeBox(obj);
                    $('#propBarcodeWidth').val((box.width / scale).toFixed(1));
                    $('#propBarcodeHeight').val((box.height / scale).toFixed(1));
                    $('#propX').val((box.left / scale).toFixed(1));
                    $('#propY').val((box.top / scale).toFixed(1));
                } else {
                    $('#propTextWrap, #propTextStyleWrap').show();
                    $('#propBarcodeWrap').hide();
                    $('#propText').val('[Shape]').prop('disabled', true);
                    $('#propFontSize').val('').prop('disabled', true);
                    $('#bdFontTrigger').prop('disabled', true);
                    setFontPickerUI(defaultFontFamily);
                    $('#propAlign').val('');
                    $('.bd-align-btns .btn').removeClass('active').prop('disabled', true);
                    $('#btnPropBold, #btnPropItalic').removeClass('active').prop('disabled', true);
                }
            }

            $('#propText').on('change', function() {
                var obj = canvas.getActiveObject();
                if (!isTextObject(obj)) return;
                var val = $(this).val();
                obj.set('text', val);
                if (previewMode && obj.isVariable && obj.variableField) {
                    var key = fieldKey(obj.variableField);
                    sampleData[key] = val;
                    $('.sample-input[data-key="' + key + '"]').val(val);
                    if (key === 'company_address') sampleData.address = val;
                    if (key === 'selling_price') sampleData.price = val;
                }
                    canvas.renderAll();
                captureHistory();
            });
            $('#propText').on('input', function() {
                var obj = canvas.getActiveObject();
                if (!isTextObject(obj)) return;
                var val = $(this).val();
                obj.set('text', val);
                if (previewMode && obj.isVariable && obj.variableField) {
                    var key = fieldKey(obj.variableField);
                    sampleData[key] = val;
                    $('.sample-input[data-key="' + key + '"]').val(val);
                    if (key === 'company_address') sampleData.address = val;
                    if (key === 'selling_price') sampleData.price = val;
                }
                canvas.renderAll();
            });
            $('#propFontSize').on('change', function() {
                var obj = canvas.getActiveObject();
                if (isTextObject(obj)) {
                    obj.set({ fontSize: parseInt($(this).val(), 10) || 18, scaleX: 1, scaleY: 1 });
                    canvas.renderAll();
                    captureHistory();
                }
            });
            $('#propX').on('change', function() {
                var obj = canvas.getActiveObject();
                if (obj) { obj.set('left', parseFloat($(this).val()) * scale || 0); canvas.renderAll(); captureHistory(); }
            });
            $('#propY').on('change', function() {
                var obj = canvas.getActiveObject();
                if (obj) { obj.set('top', parseFloat($(this).val()) * scale || 0); canvas.renderAll(); captureHistory(); }
            });
            $('#propBarcodeDensity').on('input', function() {
                var obj = canvas.getActiveObject();
                if (!obj || !obj.isBarcode) return;
                // Update % instantly; redraw barcode after a short pause so slider stays smooth
                scheduleBarcodeDensityRedraw(obj, $(this).val(), false);
            });
            $('#propBarcodeDensity').on('change', function() {
                var obj = canvas.getActiveObject();
                if (!obj || !obj.isBarcode) return;
                if (densityRedrawTimer) {
                    clearTimeout(densityRedrawTimer);
                    densityRedrawTimer = null;
                }
                applyBarcodeDensity(obj, $(this).val(), true, true);
            });
            function applyBarcodeSizeFromInputs() {
                var obj = canvas.getActiveObject();
                if (!obj || !obj.isBarcode) return;
                var labelW = parseFloat($('#labelWidth').val()) || 90;
                var labelH = parseFloat($('#labelHeight').val()) || 40;
                var wMm = parseFloat($('#propBarcodeWidth').val());
                var hMm = parseFloat($('#propBarcodeHeight').val());
                if (!isFinite(wMm) || wMm < 4) wMm = 4;
                if (wMm > labelW) wMm = labelW;
                if (!isFinite(hMm) || hMm < 2) hMm = 2;
                if (hMm > labelH) hMm = labelH;
                $('#propBarcodeWidth').val(wMm.toFixed(1));
                $('#propBarcodeHeight').val(hMm.toFixed(1));
                applyBarcodeFrame(obj, wMm * scale, hMm * scale, true, true);
            }
            $('#propBarcodeWidth, #propBarcodeHeight').on('change', applyBarcodeSizeFromInputs);
            $('.bd-align-btns').on('click', '[data-align]', function() {
                setTextAlign($(this).data('align'));
            });
            $('#propAlign').on('change', function() {
                setTextAlign($(this).val());
            });
            $('#btnPropBold').on('click', function() { toggleBold(); });
            $('#btnPropItalic').on('click', function() { toggleItalic(); });
            $('#btnDeleteElement').on('click', function() { deleteActiveObject(); });
            $('#btnUndo').on('click', function() { undoHistory(); });
            $('#btnRedo').on('click', function() { redoHistory(); });

            var arrowNudgeHistoryTimer = null;
            function nudgeSelectedByArrows(dxPx, dyPx) {
                var obj = canvas.getActiveObject();
                if (!obj) return false;
                // Don't hijack arrows while editing text
                if (obj.isEditing) return false;

                function nudgeOne(o, dx, dy) {
                    if (!o) return;
                    var nl = (o.left || 0) + dx;
                    var nt = (o.top || 0) + dy;
                    o.set({ left: nl, top: nt });
                    if (o.isBarcode) {
                        var boxL = o.barcodeBoxLeft != null ? o.barcodeBoxLeft : (o.left || 0);
                        var boxT = o.barcodeBoxTop != null ? o.barcodeBoxTop : (o.top || 0);
                        // If using center origin for barcode preview image, keep box in sync
                        o.set({
                            barcodeBoxLeft: boxL + dx,
                            barcodeBoxTop: boxT + dy
                        });
                    }
                    if (typeof o.setCoords === 'function') o.setCoords();
                }

                if (obj.type === 'activeSelection') {
                    (obj.getObjects() || []).forEach(function(o) {
                        nudgeOne(o, dxPx, dyPx);
                    });
                    if (typeof obj.setCoords === 'function') obj.setCoords();
                } else {
                    nudgeOne(obj, dxPx, dyPx);
                }

                canvas.requestRenderAll();
                updatePropertiesPanel();
                updateChrome();
                if (arrowNudgeHistoryTimer) clearTimeout(arrowNudgeHistoryTimer);
                arrowNudgeHistoryTimer = setTimeout(function() {
                    arrowNudgeHistoryTimer = null;
                    captureHistory();
                }, 350);
                return true;
            }

            $(document).on('keydown', function(e) {
                var inField = $(e.target).is('input, textarea, select') || $(e.target).is('[contenteditable="true"]');
                var mod = e.ctrlKey || e.metaKey;
                var key = (e.key || '').toLowerCase();

                // Arrow keys: move selected item (Shift = 1mm, otherwise 0.1mm)
                if (!inField && !mod && (
                    e.key === 'ArrowLeft' || e.key === 'ArrowRight' ||
                    e.key === 'ArrowUp' || e.key === 'ArrowDown' ||
                    e.keyCode === 37 || e.keyCode === 38 || e.keyCode === 39 || e.keyCode === 40
                )) {
                    var stepMm = e.shiftKey ? 1 : 0.1;
                    var stepPx = stepMm * scale;
                    var dx = 0;
                    var dy = 0;
                    if (e.key === 'ArrowLeft' || e.keyCode === 37) dx = -stepPx;
                    if (e.key === 'ArrowRight' || e.keyCode === 39) dx = stepPx;
                    if (e.key === 'ArrowUp' || e.keyCode === 38) dy = -stepPx;
                    if (e.key === 'ArrowDown' || e.keyCode === 40) dy = stepPx;
                    if (nudgeSelectedByArrows(dx, dy)) {
                        e.preventDefault();
                        e.stopPropagation();
                        return;
                    }
                }

                if (mod && key === 'z' && !e.shiftKey) {
                    if (inField) return;
                    e.preventDefault();
                    undoHistory();
                    return;
                }
                if (mod && (key === 'y' || (key === 'z' && e.shiftKey))) {
                    if (inField) return;
                    e.preventDefault();
                    redoHistory();
                    return;
                }
                if (mod && key === 'b') {
                    if (inField) return;
                    e.preventDefault();
                    toggleBold();
                    return;
                }
                if (mod && key === 'i') {
                    if (inField) return;
                    e.preventDefault();
                    toggleItalic();
                    return;
                }
                if ((e.key === 'Delete' || e.key === 'Backspace') && !inField) {
                    e.preventDefault();
                    deleteActiveObject();
                }
            });

            function syncDeleteTemplateButton() {
                var hasTpl = !!currentTemplateId;
                $('#btnDeleteTemplate').prop('disabled', !hasTpl);
                $('#btnRenameTemplate').prop('disabled', !hasTpl);
            }

            function loadTemplates(selectId) {
                $.ajax({
                    url: '/api/barcode-templates',
                    method: 'GET',
                    success: function(res) {
                        if (!res.status) return;
                        var select = $('#templateSelector');
                        select.find('option:not(:first)').remove();
                        (res.data || []).forEach(function(tpl) {
                            select.append(new Option(tpl.name + ' (' + parseFloat(tpl.width_mm) + '×' + parseFloat(tpl.height_mm) + ')', tpl.id));
                        });
                        if (selectId) {
                            select.val(String(selectId));
                            currentTemplateId = selectId;
                        } else if (currentTemplateId) {
                            select.val(String(currentTemplateId));
                        }
                        syncDeleteTemplateButton();
                    }
                });
            }

            $('#btnRenameTemplate').on('click', function() {
                if (!currentTemplateId) {
                    Swal.fire({
                        icon: 'info',
                        title: 'No template selected',
                        text: 'Select a saved template from the dropdown first.',
                        confirmButtonColor: '#ff9f43'
                    });
                    return;
                }
                var currentLabel = $('#templateSelector option:selected').text() || '';
                var currentName = currentLabel.replace(/\s*\([^)]*\)\s*$/, '').trim() || 'Template';
                Swal.fire({
                    title: 'Rename template',
                    input: 'text',
                    inputLabel: 'Template name',
                    inputValue: currentName,
                    inputPlaceholder: 'Enter new name',
                    showCancelButton: true,
                    confirmButtonText: 'Rename',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#ff9f43',
                    inputValidator: function(value) {
                        if (!value || !String(value).trim()) {
                            return 'Please enter a template name';
                        }
                        if (String(value).trim().length > 255) {
                            return 'Name is too long (max 255 characters)';
                        }
                        return null;
                    }
                }).then(function(result) {
                    if (!result.isConfirmed) return;
                    var newName = String(result.value || '').trim();
                    $.ajax({
                        url: '/api/barcode-templates/' + currentTemplateId + '/rename',
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            name: newName
                        },
                        success: function(res) {
                            if (!res.status) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: res.message || 'Could not rename template',
                                    confirmButtonColor: '#ff9f43'
                                });
                                return;
                            }
                            loadTemplates(currentTemplateId);
                            Swal.fire({
                                icon: 'success',
                                title: 'Renamed',
                                text: 'Template name updated.',
                                timer: 1400,
                                showConfirmButton: false
                            });
                        },
                        error: function(xhr) {
                            var msg = (xhr.responseJSON && xhr.responseJSON.message)
                                ? xhr.responseJSON.message
                                : 'Could not rename template';
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: msg,
                                confirmButtonColor: '#ff9f43'
                            });
                        }
                    });
                });
            });

            $('#btnDeleteTemplate').on('click', function() {
                if (!currentTemplateId) {
                    Swal.fire({
                        icon: 'info',
                        title: 'No template selected',
                        text: 'Select a saved template from the dropdown first.',
                        confirmButtonColor: '#ff9f43'
                    });
                    return;
                }
                var name = $('#templateSelector option:selected').text() || 'this template';
                Swal.fire({
                    title: 'Delete template?',
                    html: 'Delete <strong>' + $('<div>').text(name).html() + '</strong>?<br>This cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#dc3545'
                }).then(function(result) {
                    if (!result.isConfirmed) return;
                    var deleteId = currentTemplateId;
                    $.ajax({
                        url: '/api/barcode-templates/' + deleteId,
                        method: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            if (!res.status) {
                                Swal.fire('Error', res.message || 'Could not delete template', 'error');
                                return;
                            }
                            currentTemplateId = null;
                            canvas.clear();
                            canvas.setBackgroundColor('#ffffff', canvas.renderAll.bind(canvas));
                            $('#templateSelector').val('');
            loadTemplates();
                            syncDeleteTemplateButton();
                            updateChrome();
                            resetHistory();
                            $('#emptyState').addClass('show');
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted',
                                text: 'Template removed.',
                                timer: 1600,
                                showConfirmButton: false
                            });
                        },
                        error: function(xhr) {
                            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Could not delete template';
                            Swal.fire('Error', msg, 'error');
                        }
                    });
                });
            });

            $('#btnSaveTemplate').on('click', function() {
                var method = currentTemplateId ? 'PUT' : 'POST';
                var url = '/api/barcode-templates' + (currentTemplateId ? '/' + currentTemplateId : '');
                var doSave = function(templateName) {
                    // Save exact positions the user designed — do not auto-move layout on save
                    var restorePreview = previewMode;
                    historyLocked = true;

                    if (previewMode) {
                        // Switch to placeholders for storage, without running margin auto-fit
                        previewMode = false;
                        $('#btnTogglePreview').removeClass('active').html('<i class="fas fa-eye"></i> Preview');
                        $('body').removeClass('bd-preview-on');
                        $('#previewSamplePanel').removeClass('show');
                        restoreBarcodePlaceholders();
                    }

                    normalizeAllTextScales();
                    // Ensure variables store placeholders (text only — positions unchanged)
                    canvas.getObjects().forEach(function(obj) {
                        if (!isTextObject(obj)) return;
                        if (obj.isPriceLine) {
                            obj.set('text', '@{{mrp}}  @{{selling_price}}');
                            return;
                        }
                        if (obj.isVariable && obj.variableField) {
                            obj.set('text', placeholderForKey(fieldKey(obj.variableField)));
                        }
                    });
                    canvas.renderAll();
                    var data = {
                        name: templateName,
                        width_mm: $('#labelWidth').val(),
                        height_mm: $('#labelHeight').val(),
                        design_json: JSON.stringify(canvas.toJSON(['isVariable', 'variableField', 'isBarcode', 'isPriceLine', 'barcodeDensity', 'barcodeBaseWidth', 'barcodeBoxLeft', 'barcodeBoxTop', 'barcodeBoxWidth', 'barcodeBoxHeight', 'barcodeSizeWidth', 'barcodeSizeHeight'])),
                        _token: $('meta[name="csrf-token"]').attr('content')
                    };
                    var originalText = $('#btnSaveTemplate').html();
                    $('#btnSaveTemplate').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving…');
                    $.ajax({
                        url: url,
                        method: method,
                        data: data,
                        success: function(res) {
                            $('#btnSaveTemplate').prop('disabled', false).html(originalText);
                            historyLocked = false;
                            if (res.status) {
                                currentTemplateId = res.data.id;
                                loadTemplates(currentTemplateId);
                                // Restore preview so the label looks the same as before Save
                                if (restorePreview) {
                                    setPreviewMode(true);
                                }
                                Swal.fire({ icon: 'success', title: 'Saved', text: res.message || 'Template saved', timer: 1400, showConfirmButton: false });
                            } else {
                                if (restorePreview) setPreviewMode(true);
                                Swal.fire('Error', res.message || 'Could not save', 'error');
                            }
                        },
                        error: function(xhr) {
                            $('#btnSaveTemplate').prop('disabled', false).html(originalText);
                            historyLocked = false;
                            if (restorePreview) setPreviewMode(true);
                            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Could not save template';
                            Swal.fire('Error', msg, 'error');
                        }
                    });
                };

                if (!currentTemplateId) {
                    Swal.fire({
                        title: 'Name this template',
                        input: 'text',
                        inputPlaceholder: 'e.g. 35x25 shop label',
                        showCancelButton: true,
                        confirmButtonText: 'Save',
                        confirmButtonColor: '#ff9f43',
                        inputValidator: function(value) {
                            if (!value) return 'Please enter a name';
                        }
                    }).then(function(result) {
                        if (result.isConfirmed) doSave(result.value);
                    });
                } else {
                    var name = $('#templateSelector option:selected').text().replace(/\s*\([^)]*\)\s*$/, '');
                    doSave(name || 'Template');
                }
            });

            $('#templateSelector').on('change', function() {
                if (previewMode) setPreviewMode(false);
                loadTemplateById($(this).val());
            });

            // Boot
            fillSampleInputs();
            loadTemplates();
            syncDeleteTemplateButton();
            updateChrome();
            applyViewZoom();
            resetHistory();

            // If opened with ?id= or empty canvas, show empty helper (don't auto-overwrite saved work)
            var params = new URLSearchParams(window.location.search);
            var bootId = params.get('id') || params.get('template');
            var wantPreview = pendingProductPreview || params.get('preview') === '1';
            function enableProductPreviewWhenReady() {
                if (!wantPreview) return;
                var tries = 0;
                var timer = setInterval(function() {
                    tries++;
                    if (canvas.getObjects().length > 0) {
                        clearInterval(timer);
                        setPreviewMode(true);
                    } else if (tries > 40) {
                        clearInterval(timer);
                        fillSampleInputs();
                    }
                }, 150);
            }
            if (bootId) {
                loadTemplates(bootId);
                loadTemplateById(bootId);
                enableProductPreviewWhenReady();
            } else if (canvas.getObjects().length === 0) {
                $('#emptyState').addClass('show');
                if (wantPreview) {
                    fillSampleInputs();
                }
            } else if (wantPreview) {
                setPreviewMode(true);
            }
        });
    </script>
    @include('layout.partials.caps-lock')
</body>
</html>

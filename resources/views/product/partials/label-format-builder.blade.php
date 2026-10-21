<style>
    #labelFormatBuilderModal .lfb-layout {
        min-height: 520px;
    }
    #labelFormatBuilderModal .lfb-canvas-wrap {
        background: #f3f4f6;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        padding: 16px;
        overflow: auto;
        min-height: 360px;
        display: flex;
        align-items: flex-start;
        justify-content: center;
    }
    #lfbCanvasInner {
        position: relative;
        background: #fff;
        box-shadow: 0 2px 10px rgba(0,0,0,.08);
    }
    .lfb-element {
        position: absolute;
        border: 1px dashed #94a3b8;
        background: rgba(255,159,67,.08);
        cursor: move;
        overflow: hidden;
        box-sizing: border-box;
    }
    .lfb-element.is-selected {
        border: 2px solid #ff9f43;
        z-index: 5;
    }
    .lfb-element-text .lfb-text-content {
        width: 100%;
        height: 100%;
        padding: 1px 2px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    .lfb-element-barcode {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
    }
    .lfb-barcode-placeholder {
        font-size: 10px;
        letter-spacing: 1px;
        color: #111;
        font-weight: 700;
    }
    .lfb-resize-handle {
        position: absolute;
        right: -4px;
        bottom: -4px;
        width: 10px;
        height: 10px;
        background: #ff9f43;
        border: 1px solid #fff;
        border-radius: 2px;
        cursor: nwse-resize;
    }
    .lfb-format-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
    }
    .lfb-format-item.active {
        background: #fff7ed;
        border-color: #ff9f43;
    }
</style>

<div class="modal fade" id="labelFormatBuilderModal" tabindex="-1" aria-labelledby="labelFormatBuilderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2">
                <div>
                    <h5 class="modal-title mb-0" id="labelFormatBuilderModalLabel">Custom Label Format Designer</h5>
                    <small class="text-muted">Drag elements on the label, resize from corner, save multiple formats for your branch.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body lfb-layout">
                <div class="row g-3">
                    <div class="col-lg-3">
                        <label class="form-label small mb-1">Saved formats</label>
                        <div class="list-group mb-2" id="lfbFormatList" style="max-height:220px;overflow:auto;"></div>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="lfbNewFormatBtn">+ New format</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="lfbPresetSplitBtn">Load split preset</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="lfbPresetClassicBtn">Load classic preset</button>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="row g-2 mb-2">
                            <div class="col-md-5">
                                <label class="form-label small mb-1">Format name</label>
                                <input type="text" class="form-control form-control-sm" id="lfbFormatName" placeholder="My label format">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small mb-1">Width (mm)</label>
                                <input type="number" class="form-control form-control-sm" id="lfbWidthMm" value="35" min="10" max="120" step="0.1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small mb-1">Height (mm)</label>
                                <input type="number" class="form-control form-control-sm" id="lfbHeightMm" value="25" min="10" max="120" step="0.1">
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <button type="button" class="btn btn-sm btn-light" id="lfbAddTextBtn">+ Text field</button>
                            <button type="button" class="btn btn-sm btn-light" id="lfbAddBarcodeBtn">+ Barcode</button>
                        </div>
                        <div class="lfb-canvas-wrap">
                            <div id="lfbCanvasInner"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label small mb-1">Element properties</label>
                        <div id="lfbProperties" class="border rounded p-2 bg-light">
                            <p class="text-muted small mb-0">Select an element on the label.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-outline-danger btn-sm" id="lfbDeleteFormatBtn">Delete format</button>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-sm" id="lfbSaveFormatBtn">Save format</button>
            </div>
        </div>
    </div>
</div>

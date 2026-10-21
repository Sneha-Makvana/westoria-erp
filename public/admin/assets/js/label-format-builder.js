(function(window, $) {
    'use strict';

    var PX_PER_MM = 3.78;
    var FIELD_OPTIONS = [
        { id: 'company_name', label: 'Company name' },
        { id: 'address', label: 'Address' },
        { id: 'product_name', label: 'Product name' },
        { id: 'label_code', label: 'Label code' },
        { id: 'mrp_line', label: 'MRP line (MRP-xxx)' },
        { id: 'rate_line', label: 'Rate line (RATE-xxx)' },
        { id: 'price_line', label: 'MRP + Rate line' },
        { id: 'barcode', label: 'Barcode image' },
        { id: 'barcode_number', label: 'Barcode number' },
        { id: 'barcode_footer', label: 'Barcode + batch + wholesale' },
        { id: 'batch_no', label: 'Batch no' },
        { id: 'wholesale', label: 'Wholesale ref' },
        { id: 'qty', label: 'Other / Qty' },
        { id: 'custom_text', label: 'Custom text' }
    ];

    var state = {
        formats: [],
        activeFormatId: '',
        selectedElementId: '',
        drag: null,
        previewContext: {}
    };

    function authHeaders() {
        var token = localStorage.getItem('authToken') || '';
        return token ? { Authorization: 'Bearer ' + token } : {};
    }

    function uid() {
        return 'el_' + Math.random().toString(36).slice(2, 10);
    }

    function newFormat(name) {
        return {
            id: '',
            name: name || 'New format',
            width_mm: 35,
            height_mm: 25,
            elements: []
        };
    }

    function splitPreset() {
        return {
            id: '',
            name: 'Split layout preset',
            width_mm: 35,
            height_mm: 25,
            elements: [
                { id: uid(), type: 'text', field: 'company_name', x_pct: 0, y_pct: 2, w_pct: 100, h_pct: 10, font_pt: 8, bold: true, align: 'center' },
                { id: uid(), type: 'text', field: 'address', x_pct: 0, y_pct: 12, w_pct: 100, h_pct: 8, font_pt: 6.5, bold: true, align: 'center' },
                { id: uid(), type: 'text', field: 'product_name', x_pct: 0, y_pct: 22, w_pct: 100, h_pct: 10, font_pt: 7, bold: true, align: 'center' },
                { id: uid(), type: 'barcode', field: 'barcode', x_pct: 2, y_pct: 52, w_pct: 58, h_pct: 28 },
                { id: uid(), type: 'text', field: 'barcode_footer', x_pct: 2, y_pct: 82, w_pct: 58, h_pct: 8, font_pt: 5.5, bold: true, align: 'center' },
                { id: uid(), type: 'text', field: 'mrp_line', x_pct: 62, y_pct: 58, w_pct: 36, h_pct: 10, font_pt: 6.5, bold: true, align: 'left' },
                { id: uid(), type: 'text', field: 'rate_line', x_pct: 62, y_pct: 70, w_pct: 36, h_pct: 10, font_pt: 7, bold: true, align: 'left' }
            ]
        };
    }

    function classicPreset() {
        return {
            id: '',
            name: 'Classic layout preset',
            width_mm: 35,
            height_mm: 25,
            elements: [
                { id: uid(), type: 'text', field: 'company_name', x_pct: 0, y_pct: 4, w_pct: 100, h_pct: 10, font_pt: 8, bold: true, align: 'center' },
                { id: uid(), type: 'text', field: 'address', x_pct: 0, y_pct: 14, w_pct: 100, h_pct: 8, font_pt: 6.5, bold: true, align: 'center' },
                { id: uid(), type: 'text', field: 'product_name', x_pct: 0, y_pct: 24, w_pct: 100, h_pct: 10, font_pt: 7, bold: true, align: 'center' },
                { id: uid(), type: 'text', field: 'price_line', x_pct: 0, y_pct: 36, w_pct: 100, h_pct: 10, font_pt: 7, bold: true, align: 'center' },
                { id: uid(), type: 'barcode', field: 'barcode', x_pct: 8, y_pct: 50, w_pct: 84, h_pct: 26 },
                { id: uid(), type: 'text', field: 'barcode_footer', x_pct: 0, y_pct: 78, w_pct: 100, h_pct: 10, font_pt: 5.5, bold: true, align: 'center' }
            ]
        };
    }

    function getActiveFormat() {
        return state.formats.find(function(f) { return f.id === state.activeFormatId; }) || null;
    }

    function setPreviewContext(ctx) {
        state.previewContext = ctx || {};
    }

    function resolveFieldText(field, element) {
        if (field === 'custom_text') {
            return String(element.text || 'TEXT');
        }
        var map = state.previewContext || {};
        return String(map[field] || (FIELD_OPTIONS.find(function(o) { return o.id === field; }) || {}).label || field);
    }

    function renderCanvas() {
        var format = getActiveFormat();
        var $canvas = $('#lfbCanvasInner');
        if (!format || !$canvas.length) {
            return;
        }

        var w = Math.round(format.width_mm * PX_PER_MM);
        var h = Math.round(format.height_mm * PX_PER_MM);
        $canvas.css({ width: w + 'px', height: h + 'px' }).empty();

        (format.elements || []).forEach(function(el) {
            var left = (el.x_pct / 100) * w;
            var top = (el.y_pct / 100) * h;
            var width = Math.max(8, (el.w_pct / 100) * w);
            var height = Math.max(8, (el.h_pct / 100) * h);
            var isSelected = el.id === state.selectedElementId;
            var label = el.type === 'barcode'
                ? 'Barcode'
                : resolveFieldText(el.field, el);

            var $el = $('<div class="lfb-element' + (isSelected ? ' is-selected' : '') + '" data-id="' + el.id + '"></div>');
            $el.css({ left: left + 'px', top: top + 'px', width: width + 'px', height: height + 'px' });

            if (el.type === 'barcode') {
                $el.addClass('lfb-element-barcode').append('<div class="lfb-barcode-placeholder">|||||</div>');
            } else {
                $el.addClass('lfb-element-text').append(
                    $('<div class="lfb-text-content"></div>')
                        .css({ fontSize: Math.max(8, (el.font_pt || 8) * 0.95) + 'px', fontWeight: el.bold ? '700' : '600', textAlign: el.align || 'left' })
                        .text(label)
                );
            }

            $el.append('<span class="lfb-resize-handle"></span>');
            $canvas.append($el);
        });
    }

    function renderFormatList() {
        var $list = $('#lfbFormatList').empty();
        state.formats.forEach(function(format) {
            var active = format.id === state.activeFormatId ? ' active' : '';
            $list.append(
                '<button type="button" class="list-group-item list-group-item-action lfb-format-item' + active + '" data-id="' + format.id + '">' +
                '<span>' + $('<div>').text(format.name).html() + '</span>' +
                '<small class="text-muted">' + format.width_mm + '×' + format.height_mm + ' mm</small>' +
                '</button>'
            );
        });
    }

    function bindProperties() {
        var format = getActiveFormat();
        var el = format ? (format.elements || []).find(function(item) { return item.id === state.selectedElementId; }) : null;
        var $panel = $('#lfbProperties');
        if (!el) {
            $panel.html('<p class="text-muted small mb-0">Select an element on the label to edit position, size, and content.</p>');
            return;
        }

        var fieldOptions = FIELD_OPTIONS.map(function(opt) {
            var sel = opt.id === el.field ? ' selected' : '';
            return '<option value="' + opt.id + '"' + sel + '>' + opt.label + '</option>';
        }).join('');

        $panel.html(
            '<div class="mb-2"><label class="form-label small mb-1">Type</label>' +
            '<select class="form-select form-select-sm" id="lfbPropType">' +
            '<option value="text"' + (el.type === 'text' ? ' selected' : '') + '>Text</option>' +
            '<option value="barcode"' + (el.type === 'barcode' ? ' selected' : '') + '>Barcode</option>' +
            '</select></div>' +
            '<div class="mb-2 lfb-prop-text-only"><label class="form-label small mb-1">Field</label>' +
            '<select class="form-select form-select-sm" id="lfbPropField">' + fieldOptions + '</select></div>' +
            '<div class="mb-2 lfb-prop-custom-text" style="display:none"><label class="form-label small mb-1">Custom text</label>' +
            '<input type="text" class="form-control form-control-sm" id="lfbPropCustomText" value="' + $('<div>').text(el.text || '').html() + '"></div>' +
            '<div class="row g-2 mb-2">' +
            '<div class="col-6"><label class="form-label small mb-1">X %</label><input type="number" step="0.1" min="0" max="100" class="form-control form-control-sm" id="lfbPropX" value="' + el.x_pct + '"></div>' +
            '<div class="col-6"><label class="form-label small mb-1">Y %</label><input type="number" step="0.1" min="0" max="100" class="form-control form-control-sm" id="lfbPropY" value="' + el.y_pct + '"></div>' +
            '<div class="col-6"><label class="form-label small mb-1">Width %</label><input type="number" step="0.1" min="1" max="100" class="form-control form-control-sm" id="lfbPropW" value="' + el.w_pct + '"></div>' +
            '<div class="col-6"><label class="form-label small mb-1">Height %</label><input type="number" step="0.1" min="1" max="100" class="form-control form-control-sm" id="lfbPropH" value="' + el.h_pct + '"></div>' +
            '</div>' +
            '<div class="mb-2 lfb-prop-text-only"><label class="form-label small mb-1">Font (pt)</label><input type="number" step="0.5" min="5" max="24" class="form-control form-control-sm" id="lfbPropFont" value="' + (el.font_pt || 8) + '"></div>' +
            '<div class="mb-2 lfb-prop-text-only"><label class="form-label small mb-1">Align</label>' +
            '<select class="form-select form-select-sm" id="lfbPropAlign">' +
            ['left', 'center', 'right'].map(function(a) {
                return '<option value="' + a + '"' + ((el.align || 'left') === a ? ' selected' : '') + '>' + a + '</option>';
            }).join('') + '</select></div>' +
            '<div class="form-check mb-2 lfb-prop-text-only"><input class="form-check-input" type="checkbox" id="lfbPropBold"' + (el.bold ? ' checked' : '') + '>' +
            '<label class="form-check-label small" for="lfbPropBold">Bold</label></div>' +
            '<button type="button" class="btn btn-outline-danger btn-sm" id="lfbDeleteElement">Remove element</button>'
        );

        toggleCustomTextField(el.field);
    }

    function toggleCustomTextField(field) {
        var isCustom = field === 'custom_text';
        $('.lfb-prop-custom-text').toggle(isCustom);
        $('.lfb-prop-text-only').toggle(field !== 'barcode');
        if (field === 'barcode') {
            $('.lfb-prop-text-only').hide();
        }
    }

    function updateSelectedElement(patch) {
        var format = getActiveFormat();
        if (!format) return;
        var el = (format.elements || []).find(function(item) { return item.id === state.selectedElementId; });
        if (!el) return;
        Object.assign(el, patch);
        renderCanvas();
        bindProperties();
    }

    function loadFormats(callback) {
        $.ajax({
            url: '/api/label-formats',
            headers: authHeaders(),
            success: function(res) {
                if (res.status) {
                    state.formats = res.formats || [];
                    if (!state.activeFormatId && state.formats.length) {
                        state.activeFormatId = state.formats[0].id;
                    }
                    renderFormatList();
                    renderCanvas();
                    bindProperties();
                    refreshPrintFormatDropdown();
                }
                if (typeof callback === 'function') callback();
            }
        });
    }

    function saveActiveFormat() {
        var format = getActiveFormat();
        if (!format) return;
        format.name = $('#lfbFormatName').val() || format.name;
        format.width_mm = parseFloat($('#lfbWidthMm').val()) || format.width_mm;
        format.height_mm = parseFloat($('#lfbHeightMm').val()) || format.height_mm;
        var payload = JSON.parse(JSON.stringify(format));
        if (String(payload.id || '').indexOf('draft_') === 0) {
            payload.id = '';
        }

        $.ajax({
            url: '/api/label-formats',
            method: 'POST',
            headers: authHeaders(),
            contentType: 'application/json',
            data: JSON.stringify(payload),
            success: function(res) {
                if (res.status) {
                    state.formats = res.formats || state.formats;
                    if (res.format && res.format.id) {
                        state.activeFormatId = res.format.id;
                    }
                    renderFormatList();
                    refreshPrintFormatDropdown();
                    Swal.fire({ icon: 'success', title: 'Saved', text: 'Label format saved.', timer: 1400, showConfirmButton: false });
                }
            },
            error: function(xhr) {
                Swal.fire('Error', (xhr.responseJSON && xhr.responseJSON.message) || 'Could not save format', 'error');
            }
        });
    }

    function refreshPrintFormatDropdown() {
        var $sel = $('#pbLabelFormat');
        if (!$sel.length) return;
        var current = $sel.val() || localStorage.getItem('barcodeLabelFormat') || 'classic';
        $sel.find('optgroup[data-group="custom"]').remove();
        if (!state.formats.length) return;
        var $group = $('<optgroup label="Custom formats" data-group="custom"></optgroup>');
        state.formats.forEach(function(format) {
            $group.append('<option value="custom:' + format.id + '">' + $('<div>').text(format.name).html() + '</option>');
        });
        $sel.append($group);
        if (current && $sel.find('option[value="' + current + '"]').length) {
            $sel.val(current);
        }
    }

    function getCustomFormatById(id) {
        if (!id) return null;
        return state.formats.find(function(f) { return f.id === id; }) || null;
    }

    function renderCustomPreview($container, format, context) {
        if (!format || !$container.length) return;
        setPreviewContext(context);
        var w = Math.round(format.width_mm * PX_PER_MM);
        var h = Math.round(format.height_mm * PX_PER_MM);
        var html = '<div class="label-box lfb-print-preview" style="margin:0 auto;width:' + w + 'px;height:' + h + 'px;position:relative;overflow:hidden;border:1px solid #ddd;background:#fff;">';
        (format.elements || []).forEach(function(el) {
            var left = (el.x_pct / 100) * w;
            var top = (el.y_pct / 100) * h;
            var width = Math.max(8, (el.w_pct / 100) * w);
            var height = Math.max(8, (el.h_pct / 100) * h);
            if (el.type === 'barcode') {
                html += '<div style="position:absolute;left:' + left + 'px;top:' + top + 'px;width:' + width + 'px;height:' + height + 'px;display:flex;align-items:center;justify-content:center;">' +
                    '<svg class="lfb-preview-barcode" data-barcode="1" style="width:100%;height:100%;"></svg></div>';
            } else {
                var text = resolveFieldText(el.field, el);
                html += '<div style="position:absolute;left:' + left + 'px;top:' + top + 'px;width:' + width + 'px;height:' + height + 'px;overflow:hidden;font-weight:' + (el.bold ? '700' : '600') +
                    ';font-size:' + Math.max(8, (el.font_pt || 8) * 0.95) + 'px;text-align:' + (el.align || 'left') + ';line-height:1.1;">' +
                    $('<div>').text(text).html() + '</div>';
            }
        });
        html += '</div>';
        $container.html(html);
        if (context.barcodeValue && typeof JsBarcode !== 'undefined') {
            try {
                JsBarcode($container.find('.lfb-preview-barcode')[0], String(context.barcodeValue), {
                    format: 'CODE128', lineColor: '#000', background: '#fff', width: 1.2, height: 40, displayValue: false, margin: 0
                });
            } catch (e) {}
        }
    }

    function initDrag() {
        var $doc = $(document);
        $doc.off('.lfbDrag');
        $('#lfbCanvasInner').off('.lfbDrag');

        $('#lfbCanvasInner').on('mousedown.lfbDrag', '.lfb-element', function(e) {
            if ($(e.target).hasClass('lfb-resize-handle')) return;
            var id = $(this).data('id');
            state.selectedElementId = id;
            bindProperties();
            renderCanvas();
            var format = getActiveFormat();
            if (!format) return;
            var el = (format.elements || []).find(function(item) { return item.id === id; });
            if (!el) return;
            state.drag = { id: id, mode: 'move', startX: e.clientX, startY: e.clientY, origX: el.x_pct, origY: el.y_pct, origW: el.w_pct, origH: el.h_pct };
            e.preventDefault();
        });

        $('#lfbCanvasInner').on('mousedown.lfbDrag', '.lfb-resize-handle', function(e) {
            var id = $(this).closest('.lfb-element').data('id');
            state.selectedElementId = id;
            var format = getActiveFormat();
            var el = format ? (format.elements || []).find(function(item) { return item.id === id; }) : null;
            if (!el) return;
            state.drag = { id: id, mode: 'resize', startX: e.clientX, startY: e.clientY, origW: el.w_pct, origH: el.h_pct };
            e.preventDefault();
            e.stopPropagation();
        });

        $doc.on('mousemove.lfbDrag', function(e) {
            if (!state.drag) return;
            var format = getActiveFormat();
            if (!format) return;
            var el = (format.elements || []).find(function(item) { return item.id === state.drag.id; });
            if (!el) return;
            var canvas = document.getElementById('lfbCanvasInner');
            if (!canvas) return;
            var rect = canvas.getBoundingClientRect();
            var dx = ((e.clientX - state.drag.startX) / rect.width) * 100;
            var dy = ((e.clientY - state.drag.startY) / rect.height) * 100;
            if (state.drag.mode === 'move') {
                el.x_pct = Math.max(0, Math.min(100 - el.w_pct, state.drag.origX + dx));
                el.y_pct = Math.max(0, Math.min(100 - el.h_pct, state.drag.origY + dy));
            } else {
                el.w_pct = Math.max(5, Math.min(100 - el.x_pct, state.drag.origW + dx));
                el.h_pct = Math.max(4, Math.min(100 - el.y_pct, state.drag.origH + dy));
            }
            renderCanvas();
            bindProperties();
        });

        $doc.on('mouseup.lfbDrag', function() {
            state.drag = null;
        });
    }

    function bindUi() {
        $(document).off('.lfbUi');

        $(document).on('click.lfbUi', '#lfbOpenBtn', function() {
            loadFormats(function() {
                var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('labelFormatBuilderModal'));
                modal.show();
            });
        });

        $(document).on('click.lfbUi', '.lfb-format-item', function() {
            state.activeFormatId = $(this).data('id');
            var format = getActiveFormat();
            if (format) {
                $('#lfbFormatName').val(format.name);
                $('#lfbWidthMm').val(format.width_mm);
                $('#lfbHeightMm').val(format.height_mm);
            }
            state.selectedElementId = '';
            renderFormatList();
            renderCanvas();
            bindProperties();
        });

        $(document).on('click.lfbUi', '#lfbNewFormatBtn', function() {
            var fmt = newFormat('Custom format ' + (state.formats.length + 1));
            fmt.id = 'draft_' + Date.now();
            state.formats.unshift(fmt);
            state.activeFormatId = fmt.id;
            $('#lfbFormatName').val(fmt.name);
            $('#lfbWidthMm').val(fmt.width_mm);
            $('#lfbHeightMm').val(fmt.height_mm);
            state.selectedElementId = '';
            renderFormatList();
            renderCanvas();
            bindProperties();
        });

        $(document).on('click.lfbUi', '#lfbPresetSplitBtn', function() {
            var fmt = splitPreset();
            fmt.id = 'draft_' + Date.now();
            state.formats.unshift(fmt);
            state.activeFormatId = fmt.id;
            $('#lfbFormatName').val(fmt.name);
            $('#lfbWidthMm').val(fmt.width_mm);
            $('#lfbHeightMm').val(fmt.height_mm);
            renderFormatList();
            renderCanvas();
        });

        $(document).on('click.lfbUi', '#lfbPresetClassicBtn', function() {
            var fmt = classicPreset();
            fmt.id = 'draft_' + Date.now();
            state.formats.unshift(fmt);
            state.activeFormatId = fmt.id;
            $('#lfbFormatName').val(fmt.name);
            $('#lfbWidthMm').val(fmt.width_mm);
            $('#lfbHeightMm').val(fmt.height_mm);
            renderFormatList();
            renderCanvas();
        });

        $(document).on('click.lfbUi', '#lfbAddTextBtn', function() {
            var format = getActiveFormat();
            if (!format) return;
            var el = { id: uid(), type: 'text', field: 'product_name', x_pct: 5, y_pct: 20, w_pct: 90, h_pct: 10, font_pt: 8, bold: true, align: 'center' };
            format.elements.push(el);
            state.selectedElementId = el.id;
            renderCanvas();
            bindProperties();
        });

        $(document).on('click.lfbUi', '#lfbAddBarcodeBtn', function() {
            var format = getActiveFormat();
            if (!format) return;
            var el = { id: uid(), type: 'barcode', field: 'barcode', x_pct: 10, y_pct: 45, w_pct: 80, h_pct: 30 };
            format.elements.push(el);
            state.selectedElementId = el.id;
            renderCanvas();
            bindProperties();
        });

        $(document).on('click.lfbUi', '#lfbSaveFormatBtn', saveActiveFormat);

        $(document).on('click.lfbUi', '#lfbDeleteFormatBtn', function() {
            var format = getActiveFormat();
            if (!format || !format.id) {
                state.formats = state.formats.filter(function(f) { return f !== format; });
                state.activeFormatId = state.formats[0] ? state.formats[0].id : '';
                renderFormatList();
                renderCanvas();
                return;
            }
            Swal.fire({ title: 'Delete format?', icon: 'warning', showCancelButton: true }).then(function(result) {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: '/api/label-formats/' + encodeURIComponent(format.id),
                    method: 'DELETE',
                    headers: authHeaders(),
                    success: function() {
                        loadFormats();
                    }
                });
            });
        });

        $(document).on('change.lfbUi input.lfbUi', '#lfbPropType, #lfbPropField, #lfbPropCustomText, #lfbPropX, #lfbPropY, #lfbPropW, #lfbPropH, #lfbPropFont, #lfbPropAlign, #lfbPropBold', function() {
            var patch = {};
            if ($('#lfbPropType').length) patch.type = $('#lfbPropType').val();
            if ($('#lfbPropField').length) patch.field = $('#lfbPropField').val();
            if ($('#lfbPropCustomText').length) patch.text = $('#lfbPropCustomText').val();
            if ($('#lfbPropX').length) patch.x_pct = parseFloat($('#lfbPropX').val());
            if ($('#lfbPropY').length) patch.y_pct = parseFloat($('#lfbPropY').val());
            if ($('#lfbPropW').length) patch.w_pct = parseFloat($('#lfbPropW').val());
            if ($('#lfbPropH').length) patch.h_pct = parseFloat($('#lfbPropH').val());
            if ($('#lfbPropFont').length) patch.font_pt = parseFloat($('#lfbPropFont').val());
            if ($('#lfbPropAlign').length) patch.align = $('#lfbPropAlign').val();
            if ($('#lfbPropBold').length) patch.bold = $('#lfbPropBold').is(':checked');
            updateSelectedElement(patch);
            if ($('#lfbPropField').length) toggleCustomTextField($('#lfbPropField').val());
        });

        $(document).on('click.lfbUi', '#lfbDeleteElement', function() {
            var format = getActiveFormat();
            if (!format) return;
            format.elements = (format.elements || []).filter(function(el) { return el.id !== state.selectedElementId; });
            state.selectedElementId = '';
            renderCanvas();
            bindProperties();
        });

        $(document).on('change.lfbUi', '#lfbWidthMm, #lfbHeightMm', function() {
            var format = getActiveFormat();
            if (!format) return;
            format.width_mm = parseFloat($('#lfbWidthMm').val()) || format.width_mm;
            format.height_mm = parseFloat($('#lfbHeightMm').val()) || format.height_mm;
            renderCanvas();
        });
    }

    window.LabelFormatBuilder = {
        init: function() {
            bindUi();
            initDrag();
            loadFormats();
        },
        loadFormats: loadFormats,
        refreshPrintFormatDropdown: refreshPrintFormatDropdown,
        getCustomFormatById: getCustomFormatById,
        renderCustomPreview: renderCustomPreview,
        buildPreviewContext: function(opts) {
            opts = opts || {};
            return {
                company_name: opts.includeTitle === false ? '' : (opts.companyName || 'POOJA SAMAN.IN'),
                address: opts.includeTitle === false ? '' : (opts.address || 'DURG,RISALI,RAIPUR'),
                product_name: opts.productName || '',
                label_code: opts.labelCode || '',
                mrp_line: opts.mrpTxt || '',
                rate_line: opts.rateTxt || '',
                price_line: [opts.mrpTxt, opts.rateTxt].filter(Boolean).join(' '),
                barcode_number: opts.barcode || '',
                barcode_footer: opts.barcodeFooter || '',
                batch_no: opts.batchNo || '',
                wholesale: opts.wholesaleRef || '',
                qty: opts.qty || '',
                barcodeValue: opts.barcode || ''
            };
        }
    };
})(window, jQuery);

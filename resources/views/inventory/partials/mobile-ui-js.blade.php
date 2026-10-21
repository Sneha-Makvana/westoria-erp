<script>
    (function($) {
        function esc(value) {
            return String(value ?? '').replace(/[&<>"']/g, function(c) {
                return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[c];
            });
        }

        function titleCase(str) {
            if (!str || String(str).trim() === '') return 'N/A';
            return String(str).replace(/\b\w/g, function(c) { return c.toUpperCase(); });
        }

        function money(value) {
            if (value === null || value === undefined || value === '' || value === 'N/A') return 'N/A';
            const n = Number(value);
            if (Number.isNaN(n)) return value;
            return n.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        function can(moduleId, action) {
            return typeof hasStaffPermission !== 'function' || hasStaffPermission(moduleId, action);
        }

        function buildCard(product, lowStockThreshold, opts) {
            const id = product.id;
            const name = esc(titleCase(product.product_name || 'N/A'));
            const currentStock = product.current_stock ?? 'N/A';
            const initialStock = product.initial_stock ?? 'N/A';
            const isLow = currentStock !== 'N/A' && parseInt(currentStock) <= (lowStockThreshold || 0);
            const canView = can(opts.moduleId, 'view');
            const canEdit = can(opts.moduleId, 'add') || can(opts.moduleId, 'edit');
            const variants = product.variants || [];

            let actions = '';
            if (canView) {
                actions += `<a class="inv-act" href="${opts.historyUrl}${id}" title="View history"><i class="fa fa-history"></i></a>`;
            }
            if (canEdit) {
                actions += `<a class="inv-act edit-product" data-id="${id}" href="javascript:void(0);" title="Add / Edit stock"><i class="fa fa-pen"></i></a>`;
                if (opts.transfer) {
                    actions += `<a class="inv-act transfer-stock" data-id="${id}" href="javascript:void(0);" title="Transfer stock"><i class="fa fa-exchange-alt"></i></a>`;
                }
            }

            let variantHtml = '';
            if (variants.length) {
                const rows = variants.map(function(v) {
                    const label = esc(`${v.size || ''} ${v.color || ''}`.trim() || 'Default');
                    const vStock = v.current_stock ?? (v.stock ?? 0);
                    let vActs = '';
                    if (canView) {
                        vActs += `<a class="inv-act" href="${opts.historyUrl}${id}?variant_id=${v.variant_id}" title="View history"><i class="fa fa-history"></i></a>`;
                    }
                    if (canEdit) {
                        vActs += `<a class="inv-act edit-product" data-id="${id}" data-variant-id="${v.variant_id}" href="javascript:void(0);" title="Add / Edit stock"><i class="fa fa-pen"></i></a>`;
                    }
                    return `<div class="inv-var-row">
                            <div class="inv-var-name">${label}<small>₹${money(v.price || product.price)}</small></div>
                            <div class="inv-var-stock">${esc(vStock)}</div>
                            <div class="inv-acts">${vActs}</div>
                        </div>`;
                }).join('');
                variantHtml = `<button type="button" class="inv-var-toggle" aria-expanded="false">${variants.length} variant${variants.length > 1 ? 's' : ''} <i class="fa fa-chevron-down"></i></button>
                    <div class="inv-var-list">${rows}</div>`;
            }

            const remarks = product.remarks && product.remarks !== 'N/A'
                ? `<div class="inv-card-remarks">${esc(product.remarks)}</div>` : '';
            const nameHtml = opts.productUrl
                ? `<a class="inv-card-name" href="${opts.productUrl}${id}">${name}</a>`
                : `<span class="inv-card-name">${name}</span>`;

            return `<div class="inv-card">
                    <div class="inv-card-top">
                        ${nameHtml}
                        <span class="inv-card-price">₹${money(product.price)}</span>
                    </div>
                    <div class="inv-card-badges">
                        <span class="inv-badge ${isLow ? 'is-low' : 'is-ok'}">${isLow ? 'LOW STOCK' : 'IN STOCK'}</span>
                        ${product.sku && product.sku !== 'N/A' ? `<span class="inv-card-sku">${esc(product.sku)}</span>` : ''}
                    </div>
                    <div class="inv-card-bottom">
                        <div class="inv-stat"><span>Initial</span><strong>${esc(initialStock)}</strong></div>
                        <div class="inv-stat"><span>Current</span><strong class="${isLow ? 'text-danger' : ''}">${esc(currentStock)}</strong></div>
                        <div class="inv-acts">${actions}</div>
                    </div>
                    ${remarks}
                    ${variantHtml}
                </div>`;
        }

        window.InventoryMobileUI = {
            esc: esc,
            renderCards: function(products, lowStockThreshold, opts) {
                opts = $.extend({
                    list: '#inventoryCardList',
                    moduleId: 17,
                    historyUrl: '/inventory-View/',
                    productUrl: '/product-view/',
                    transfer: true,
                    emptyText: 'No products found'
                }, opts || {});
                const $list = $(opts.list);
                if (!products || !products.length) {
                    $list.html(`<div class="inv-card-empty">${esc(opts.emptyText)}</div>`);
                    return;
                }
                $list.html(products.map(function(p) {
                    return buildCard(p, lowStockThreshold, opts);
                }).join(''));
            }
        };

        $(document).on('click', '.inv-var-toggle', function() {
            const $card = $(this).closest('.inv-card');
            const open = !$card.hasClass('is-var-open');
            $card.toggleClass('is-var-open', open);
            $(this).attr('aria-expanded', open ? 'true' : 'false');
        });

        $(document).on('click', '#invDateToggle', function() {
            const $row = $('.inv-filter-row');
            const open = !$row.hasClass('is-date-open');
            $row.toggleClass('is-date-open', open);
            $(this).attr('aria-expanded', open ? 'true' : 'false');
        });

        function updateEditStockPreview() {
            const action = $("input[name='stock_action']:checked").val();
            const qty = parseFloat($('#stockQuantity').val());
            const current = parseFloat($('#editProductStock').val()) || 0;
            const $preview = $('#epNewStock');
            if (!action || !(qty > 0)) {
                $preview.text('').removeClass('is-negative');
                return;
            }
            const next = action === 'add' ? current + qty : current - qty;
            $preview
                .html(`New stock will be <strong>${next.toFixed(2)}</strong>`)
                .toggleClass('is-negative', next < 0);
        }

        function updateTransferPreview() {
            const qty = parseFloat($('#transferQuantity').val());
            const available = parseFloat($('#transferAvailableStock').val()) || 0;
            const $preview = $('#transferPreview');
            if (!(qty > 0)) {
                $preview.text('').removeClass('is-negative');
                return;
            }
            const left = available - qty;
            $preview
                .html(left < 0
                    ? `Only <strong>${available.toFixed(2)}</strong> available to transfer`
                    : `<strong>${left.toFixed(2)}</strong> will remain in this branch`)
                .toggleClass('is-negative', left < 0);
        }

        $(document).on('change', '#editProductModal #addStock, #editProductModal #minusStock', function() {
            updateEditStockPreview();
            setTimeout(function() { $('#stockQuantity').trigger('focus'); }, 0);
        });
        $(document).on('input change', '#stockQuantity', updateEditStockPreview);
        $(document).on('input change', '#transferQuantity', updateTransferPreview);

        $(document).on('click', '.ep-modal .ep-step', function() {
            const $input = $($(this).data('target') || '#stockQuantity');
            const value = (parseFloat($input.val()) || 0) + Number($(this).data('step'));
            $input.val(value > 0 ? +value.toFixed(2) : '').trigger('input');
        });

        $(document).on('show.bs.modal', '#editProductModal', function() {
            $('#epNewStock').text('').removeClass('is-negative');
        });
        $(document).on('show.bs.modal', '#transferStockModal', function() {
            $('#transferPreview').text('').removeClass('is-negative');
        });
    })(jQuery);
</script>

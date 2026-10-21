@extends('layout.app')

@section('title', 'Import Preview')

@push('css')
<style>
    .import-preview-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .import-preview-summary { font-size: 14px; color: #637381; }

    .import-preview-search-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
        flex: 1 1 320px;
        max-width: 420px;
    }

    .import-preview-search-wrap .form-control {
        height: 38px;
        font-size: 13px;
    }

    .import-preview-search-wrap .btn-clear-search {
        white-space: nowrap;
    }

    .import-preview-toolbar-right {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .import-preview-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 14px;
    }

    .import-tab {
        border: 1px solid #e8e8e8;
        background: #fff;
        color: #637381;
        border-radius: 8px;
        padding: 8px 16px;
        font-weight: 600;
        font-size: 14px;
    }

    .import-tab.active {
        background: #ff9f43;
        border-color: #ff9f43;
        color: #fff;
    }

    .import-preview-table-wrap {
        max-height: calc(100vh - 360px);
        overflow: auto;
        border: 1px solid #e8e8e8;
        border-radius: 8px;
        min-height: 240px;
    }

    .import-preview-table { margin-bottom: 0; font-size: 13px; }

    .import-preview-table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #fafafa;
        white-space: nowrap;
    }

    .import-preview-table td,
    .import-preview-table th { vertical-align: middle; }

    .import-preview-table .col-check { width: 42px; text-align: center; }
    .import-preview-table .col-row { width: 56px; white-space: nowrap; }

    .import-preview-table .text-truncate-cell {
        max-width: 180px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .import-action-badge {
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 999px;
        font-weight: 600;
        white-space: nowrap;
    }

    .import-action-create { background: #e7f8ef; color: #1e7e34; }
    .import-action-update { background: #fff4e5; color: #b26a00; }
    .import-action-skip { background: #fdecea; color: #c0392b; }

    .import-preview-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #ececec;
        flex-wrap: wrap;
    }

    .import-preview-pagination {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .import-preview-loading-row td {
        text-align: center;
        padding: 28px;
        color: #637381;
    }

    #importSubmitBtn.is-disabled {
        opacity: 0.65;
        pointer-events: none;
        cursor: not-allowed;
    }

    .import-processing-panel {
        text-align: center;
        padding: 48px 20px;
        color: #637381;
    }

    .import-processing-panel h5 {
        margin-bottom: 10px;
        color: #333;
    }

    .import-processing-count {
        font-size: 24px;
        font-weight: 700;
        color: #ff9f43;
        margin: 12px 0;
    }
</style>
@endpush

@section('content')
<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Import Preview</h4>
            <h6 id="importPreviewFileName">{{ $file_name }}</h6>
        </div>
        <div class="page-btn">
            <a href="{{ route('product.import') }}" class="btn btn-cancel">Back to Upload</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="import-preview-toolbar">
                <div>
                    <h5 class="mb-1">Review items before import</h5>
                    <div class="import-preview-summary" id="importPreviewSummary">Preparing preview...</div>
                </div>
                <div class="import-preview-toolbar-right">
                    <div class="import-preview-search-wrap d-none" id="importSearchWrap">
                        <input type="text" class="form-control" id="importSearchInput"
                            placeholder="Search name, item code, category, brand, row #..." autocomplete="off">
                        <button type="button" class="btn btn-outline-secondary btn-sm btn-clear-search d-none" id="importClearSearchBtn">Clear</button>
                    </div>
                    <label class="mb-0 align-items-center gap-2 d-none" id="importSelectAllWrap">
                        <input type="checkbox" id="importSelectAll" checked>
                        <span id="importSelectAllLabel">Select all new rows</span>
                    </label>
                </div>
            </div>

            <div class="import-preview-tabs d-none" id="importActionTabs">
                <button type="button" class="import-tab active" data-tab="create" id="importTabCreate">Insert New</button>
                <button type="button" class="import-tab" data-tab="update" id="importTabUpdate">Update</button>
            </div>

            <div class="import-processing-panel" id="importProcessingPanel">
                <div class="spinner-border text-warning mb-3" role="status"></div>
                <h5>Reading your Excel file</h5>
                <div>Large files with 30k+ rows usually take 1-2 minutes after upload.</div>
                <div class="import-processing-count" id="importProcessingCount">0 rows processed</div>
                <div class="text-muted">Please keep this page open.</div>
            </div>

            <div class="import-preview-table-wrap" id="importPreviewTableWrap" style="display:none;">
                <table class="table table-striped import-preview-table">
                    <thead>
                        <tr>
                            <th class="col-check">
                                <input type="checkbox" id="importSelectAllTable" checked>
                            </th>
                            <th class="col-row">Row</th>
                            <th>Item Name</th>
                            <th>Item Code</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Sale Price</th>
                            <th>MRP</th>
                            <th>Wholesale</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="importPreviewBody"></tbody>
                </table>
            </div>

            <div class="import-preview-actions" id="importPreviewActions" style="display:none;">
                <div class="import-preview-pagination">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="importPrevPageBtn" disabled>Previous</button>
                    <span id="importPageInfo" class="text-muted">Page 1</span>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="importNextPageBtn" disabled>Next</button>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('product.import') }}" class="btn btn-cancel">Cancel</a>
                    <a href="javascript:void(0);" class="btn btn-submit is-disabled" id="importSubmitBtn">Import Selected</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        var importToken = @json($token);
        var fileName = @json($file_name);
        var initialStatus = @json($status);
        var perPage = {{ (int) $per_page }};
        var currentPage = 1;
        var lastPage = 1;
        var validCount = {{ (int) $valid_count }};
        var totalCount = {{ (int) $total_count }};
        var createCount = 0;
        var updateCount = 0;
        var activeTab = 'create';
        var selectAllCreates = true;
        var deselectedCreateRows = new Set();
        var manuallySelectedCreateRows = new Set();
        var selectAllUpdates = false;
        var deselectedUpdateRows = new Set();
        var manuallySelectedUpdateRows = new Set();
        var pageLoading = false;
        var previewReady = initialStatus === 'complete';
        var IMPORT_AJAX_TIMEOUT = 600000;
        var pollTimer = null;
        var previewErrorShown = false;
        var sawProcessing = false;

        function showPreviewErrorOnce(message) {
            if (previewErrorShown || previewReady) {
                return;
            }
            previewErrorShown = true;
            clearInterval(pollTimer);
            $('#importProcessingPanel .spinner-border').hide();
            $('#importPreviewSummary').text(message || 'Unable to read import file.');
            Swal.fire({
                icon: 'error',
                title: 'Preview Failed',
                text: message || 'Unable to read import file.',
                confirmButtonColor: '#ff9f43'
            });
        }
        var processStarted = false;
        var searchQuery = '';
        var filteredCount = 0;
        var searchDebounceTimer = null;

        function escapeHtml(value) {
            return $('<div>').text(value ?? '').html();
        }

        function formatMoney(value) {
            if (value === null || value === undefined || value === '') return '-';
            return Number(value).toLocaleString('en-IN', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
        }

        function actionBadge(action) {
            if (action === 'create') return '<span class="import-action-badge import-action-create">New</span>';
            if (action === 'update') return '<span class="import-action-badge import-action-update">Update</span>';
            return '<span class="import-action-badge import-action-skip">Skip</span>';
        }

        function selectedCreateCount() {
            if (selectAllCreates) {
                return Math.max(0, createCount - deselectedCreateRows.size);
            }
            return manuallySelectedCreateRows.size;
        }

        function selectedUpdateCount() {
            if (selectAllUpdates) {
                return Math.max(0, updateCount - deselectedUpdateRows.size);
            }
            return manuallySelectedUpdateRows.size;
        }

        function isRowSelected(item) {
            if (!item.valid) return false;
            if (item.import_action === 'update') {
                if (selectAllUpdates) return !deselectedUpdateRows.has(item.row_number);
                return manuallySelectedUpdateRows.has(item.row_number);
            }
            if (item.import_action === 'create') {
                if (selectAllCreates) return !deselectedCreateRows.has(item.row_number);
                return manuallySelectedCreateRows.has(item.row_number);
            }
            return false;
        }

        function getSelectedCount() {
            return selectedCreateCount() + selectedUpdateCount();
        }

        function syncSelectAllCheckbox() {
            var checked = activeTab === 'update'
                ? (selectAllUpdates && deselectedUpdateRows.size === 0)
                : (selectAllCreates && deselectedCreateRows.size === 0);
            $('#importSelectAll, #importSelectAllTable').prop('checked', checked);
            $('#importSelectAllLabel').text(activeTab === 'update' ? 'Select all update rows' : 'Select all new rows');
        }

        function updateTabLabels() {
            $('#importTabCreate').text('Insert New (' + createCount.toLocaleString('en-IN') + ')');
            $('#importTabUpdate').text('Update (' + updateCount.toLocaleString('en-IN') + ')');
        }

        function setSubmitEnabled(enabled) {
            $('#importSubmitBtn').toggleClass('is-disabled', !enabled);
        }

        function updateFileTitle() {
            $('#importPreviewFileName').text(
                fileName + ' — ' + totalCount.toLocaleString('en-IN') + ' row(s), ' + validCount.toLocaleString('en-IN') + ' valid'
            );
        }

        function updatePreviewSummary() {
            var createSelected = selectedCreateCount();
            var updateSelected = selectedUpdateCount();
            var selected = createSelected + updateSelected;
            var summary = createSelected.toLocaleString('en-IN') + ' of ' + createCount.toLocaleString('en-IN') + ' new row(s) selected.';

            if (updateSelected > 0) {
                summary += ' ' + updateSelected.toLocaleString('en-IN') + ' of ' + updateCount.toLocaleString('en-IN') + ' update row(s) selected.';
            } else if (updateCount > 0) {
                summary += ' Update rows (' + updateCount.toLocaleString('en-IN') + ') stay skipped until you select them on the Update tab.';
            }

            if (searchQuery) {
                summary += ' Showing ' + filteredCount.toLocaleString('en-IN') + ' match(es) for "' + searchQuery + '".';
            }

            summary += ' Page ' + currentPage + ' of ' + lastPage + '.';
            $('#importPreviewSummary').text(summary);
            $('#importSubmitBtn').text(selected > 0 ? ('Import Selected (' + selected.toLocaleString('en-IN') + ')') : 'Import Selected');
            setSubmitEnabled(previewReady && selected > 0);
            updateTabLabels();
            syncSelectAllCheckbox();
        }

        function showPreviewTable() {
            previewReady = true;
            $('#importProcessingPanel').hide();
            $('#importPreviewTableWrap').show();
            $('#importPreviewActions').show();
            $('#importSearchWrap').removeClass('d-none').addClass('d-flex');
            $('#importSelectAllWrap').removeClass('d-none').addClass('d-flex');
            $('#importActionTabs').removeClass('d-none').addClass('d-flex');
            updateFileTitle();
            updateTabLabels();
        }

        function updateProcessingUI(processed, valid) {
            $('#importProcessingCount').text(
                (processed || 0).toLocaleString('en-IN') + ' rows processed • ' + (valid || 0).toLocaleString('en-IN') + ' valid'
            );
            $('#importPreviewSummary').text('Processed ' + (processed || 0).toLocaleString('en-IN') + ' rows so far...');
        }

        function renderRows(items) {
            var $body = $('#importPreviewBody');
            $body.empty();

            if (!items.length) {
                var emptyText = searchQuery
                    ? 'No rows match "' + escapeHtml(searchQuery) + '".'
                    : (activeTab === 'update' ? 'No existing products to update.' : 'No new products to insert.');
                $body.append('<tr><td colspan="11" class="text-center py-4">' + emptyText + '</td></tr>');
                return;
            }

            items.forEach(function(item) {
                var checked = isRowSelected(item);
                var descriptionText = item.description || item.description_raw || '-';
                var wholesaleText = item.wholesale_price !== null && item.wholesale_price !== undefined
                    ? formatMoney(item.wholesale_price) : '-';

                $body.append(
                    '<tr>' +
                        '<td class="col-check">' +
                            '<input type="checkbox" class="import-row-check" data-row="' + item.row_number + '" data-valid="' + (item.valid ? '1' : '0') + '" data-action="' + escapeHtml(item.import_action || '') + '"' +
                            (item.valid ? '' : ' disabled') + (checked ? ' checked' : '') + '>' +
                        '</td>' +
                        '<td class="col-row">' + item.row_number + '</td>' +
                        '<td><div class="text-truncate-cell" title="' + escapeHtml(item.name) + '">' + escapeHtml(item.name || '-') + '</div></td>' +
                        '<td><div class="text-truncate-cell" title="' + escapeHtml(item.item_code) + '">' + escapeHtml(item.item_code || '-') + '</div></td>' +
                        '<td><div class="text-truncate-cell" title="' + escapeHtml(item.category_name) + '">' + escapeHtml(item.category_name || '-') + '</div></td>' +
                        '<td><div class="text-truncate-cell" title="' + escapeHtml(item.brand_name) + '">' + escapeHtml(item.brand_name || '-') + '</div></td>' +
                        '<td>' + formatMoney(item.price) + '</td>' +
                        '<td>' + formatMoney(item.mrp) + '</td>' +
                        '<td>' + wholesaleText + '</td>' +
                        '<td><div class="text-truncate-cell" title="' + escapeHtml(descriptionText) + '">' + escapeHtml(descriptionText) + '</div></td>' +
                        '<td>' + actionBadge(item.import_action) + '</td>' +
                    '</tr>'
                );
            });
        }

        function loadPreviewPage(page) {
            if (pageLoading || !previewReady) return;
            pageLoading = true;
            currentPage = page;

            var loadingText = searchQuery
                ? 'Searching for "' + escapeHtml(searchQuery) + '" (page ' + page + ')...'
                : 'Loading page ' + page + '...';

            $('#importPreviewBody').html(
                '<tr class="import-preview-loading-row"><td colspan="11">' +
                '<div class="spinner-border spinner-border-sm text-warning me-2" role="status"></div>' + loadingText + '</td></tr>'
            );
            $('#importPrevPageBtn, #importNextPageBtn').prop('disabled', true);

            var requestData = { page: page, per_page: perPage, action: activeTab };
            if (searchQuery) requestData.search = searchQuery;

            $.ajax({
                url: '/api/importPreviewRows/' + encodeURIComponent(importToken),
                type: 'GET',
                timeout: IMPORT_AJAX_TIMEOUT,
                data: requestData,
                headers: { Authorization: 'Bearer ' + localStorage.getItem('authToken') },
                success: function(response) {
                    pageLoading = false;
                    if (!response.status) {
                        $('#importPreviewBody').html('<tr><td colspan="11" class="text-center text-danger py-4">' + escapeHtml(response.message || 'Unable to load preview rows.') + '</td></tr>');
                        return;
                    }

                    lastPage = response.last_page || 1;
                    validCount = response.valid_count || validCount;
                    createCount = response.create_count || createCount;
                    updateCount = response.update_count || updateCount;
                    totalCount = response.total_count || totalCount;
                    filteredCount = response.filtered_count || totalCount;
                    currentPage = response.page || page;

                    renderRows(response.items || []);
                    $('#importPageInfo').text('Page ' + currentPage + ' of ' + lastPage);
                    $('#importPrevPageBtn').prop('disabled', currentPage <= 1);
                    $('#importNextPageBtn').prop('disabled', currentPage >= lastPage);
                    updatePreviewSummary();
                },
                error: function(xhr) {
                    pageLoading = false;
                    var message = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to load preview rows.';
                    $('#importPreviewBody').html('<tr><td colspan="11" class="text-center text-danger py-4">' + escapeHtml(message) + '</td></tr>');
                    $('#importPrevPageBtn, #importNextPageBtn').prop('disabled', false);
                }
            });
        }

        function pollImportStatus() {
            $.ajax({
                url: '/api/importPreviewStatus/' + encodeURIComponent(importToken),
                type: 'GET',
                headers: { Authorization: 'Bearer ' + localStorage.getItem('authToken') },
                success: function(response) {
                    if (!response.status) return;
                    if (previewErrorShown || previewReady) return;

                    if (response.process_status === 'processing' || response.process_status === 'pending') {
                        sawProcessing = true;
                    }

                    updateProcessingUI(response.processed_rows, response.valid_count);

                    if (response.process_status === 'complete') {
                        clearInterval(pollTimer);
                        validCount = response.valid_count || 0;
                        createCount = response.create_count || 0;
                        updateCount = response.update_count || 0;
                        totalCount = response.total_count || 0;
                        showPreviewTable();
                        loadPreviewPage(1);
                    }

                    if (response.process_status === 'failed' && sawProcessing) {
                        showPreviewErrorOnce(response.error || 'Unable to read import file.');
                    }
                }
            });
        }

        function startImportProcessing() {
            if (processStarted) return;
            processStarted = true;

            pollTimer = setInterval(pollImportStatus, 1000);
            pollImportStatus();

            $.ajax({
                url: '/api/processImportPreview/' + encodeURIComponent(importToken),
                type: 'POST',
                timeout: IMPORT_AJAX_TIMEOUT,
                headers: { Authorization: 'Bearer ' + localStorage.getItem('authToken') },
                success: function(response) {
                    if (response.status && response.process_status === 'complete') {
                        clearInterval(pollTimer);
                        validCount = response.valid_count || 0;
                        createCount = response.create_count || 0;
                        updateCount = response.update_count || 0;
                        totalCount = response.total_count || 0;
                        showPreviewTable();
                        loadPreviewPage(1);
                    } else if (response.status && (response.process_status === 'processing' || response.process_status === 'pending')) {
                        sawProcessing = true;
                    } else if (!response.status) {
                        showPreviewErrorOnce(response.message || 'Unable to read import file.');
                    }
                },
                error: function(xhr) {
                    if (xhr.statusText === 'timeout' || xhr.status === 0 || xhr.status === 500 || xhr.status === 502 || xhr.status === 503 || xhr.status === 504) {
                        return;
                    }
                    var message = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to read import file.';
                    showPreviewErrorOnce(message);
                }
            });
        }

        $('#importActionTabs').on('click', '.import-tab', function() {
            var nextTab = $(this).data('tab') === 'update' ? 'update' : 'create';
            if (nextTab === activeTab) return;
            activeTab = nextTab;
            $('#importActionTabs .import-tab').removeClass('active');
            $(this).addClass('active');
            currentPage = 1;
            syncSelectAllCheckbox();
            loadPreviewPage(1);
        });

        $('#importSelectAll, #importSelectAllTable').on('change', function() {
            var checked = $(this).is(':checked');
            $('#importSelectAll, #importSelectAllTable').prop('checked', checked);
            if (activeTab === 'update') {
                selectAllUpdates = checked;
                deselectedUpdateRows.clear();
                manuallySelectedUpdateRows.clear();
            } else {
                selectAllCreates = checked;
                deselectedCreateRows.clear();
                manuallySelectedCreateRows.clear();
            }
            loadPreviewPage(currentPage);
        });

        $(document).on('change', '.import-row-check', function() {
            var rowNumber = parseInt($(this).data('row'), 10);
            var valid = $(this).data('valid') === 1 || $(this).data('valid') === '1';
            var isUpdate = $(this).data('action') === 'update';
            if (!valid) return;

            if (isUpdate) {
                if (selectAllUpdates) {
                    if ($(this).is(':checked')) deselectedUpdateRows.delete(rowNumber);
                    else deselectedUpdateRows.add(rowNumber);
                } else if ($(this).is(':checked')) {
                    manuallySelectedUpdateRows.add(rowNumber);
                } else {
                    manuallySelectedUpdateRows.delete(rowNumber);
                }
            } else if (selectAllCreates) {
                if ($(this).is(':checked')) deselectedCreateRows.delete(rowNumber);
                else deselectedCreateRows.add(rowNumber);
            } else if ($(this).is(':checked')) {
                manuallySelectedCreateRows.add(rowNumber);
            } else {
                manuallySelectedCreateRows.delete(rowNumber);
            }

            updatePreviewSummary();
        });

        $('#importPrevPageBtn').on('click', function() {
            if (currentPage > 1) loadPreviewPage(currentPage - 1);
        });

        $('#importNextPageBtn').on('click', function() {
            if (currentPage < lastPage) loadPreviewPage(currentPage + 1);
        });

        $('#importSearchInput').on('input', function() {
            clearTimeout(searchDebounceTimer);
            var value = $(this).val().trim();
            $('#importClearSearchBtn').toggleClass('d-none', value === '');

            searchDebounceTimer = setTimeout(function() {
                searchQuery = value;
                filteredCount = 0;
                currentPage = 1;
                loadPreviewPage(1);
            }, 400);
        });

        $('#importClearSearchBtn').on('click', function() {
            clearTimeout(searchDebounceTimer);
            searchQuery = '';
            filteredCount = 0;
            currentPage = 1;
            $('#importSearchInput').val('');
            $(this).addClass('d-none');
            loadPreviewPage(1);
        });

        $('#importSearchInput').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchDebounceTimer);
                searchQuery = $(this).val().trim();
                filteredCount = 0;
                currentPage = 1;
                loadPreviewPage(1);
            }
        });

        $('#importSubmitBtn').on('click', function(e) {
            e.preventDefault();
            if ($(this).hasClass('is-disabled')) return;

            if (getSelectedCount() <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'No Rows Selected',
                    text: 'Please select at least one row to import.',
                    confirmButtonColor: '#ff9f43'
                });
                return;
            }

            setSubmitEnabled(false);
            $('#importSubmitBtn').text('Importing...');

            function runImportBatch(fileOffset, totals, batchSize, retryCount) {
                totals = totals || { created: 0, updated: 0 };
                batchSize = batchSize || 25;
                retryCount = retryCount || 0;
                var formData = new FormData();
                formData.append('import_token', importToken);
                formData.append('select_all_creates', selectAllCreates ? '1' : '0');
                formData.append('deselected_create_rows', JSON.stringify(Array.from(deselectedCreateRows)));
                formData.append('selected_create_rows', JSON.stringify(Array.from(manuallySelectedCreateRows)));
                formData.append('select_all_updates', selectAllUpdates ? '1' : '0');
                formData.append('selected_update_rows', JSON.stringify(Array.from(manuallySelectedUpdateRows)));
                formData.append('deselected_update_rows', JSON.stringify(Array.from(deselectedUpdateRows)));
                formData.append('file_offset', String(fileOffset || 0));
                formData.append('batch_size', String(batchSize));

                var selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
                if (selectedSubAdminId) formData.append('sub_branch_id', selectedSubAdminId);

                $.ajax({
                    url: '/api/importProducts',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    timeout: IMPORT_AJAX_TIMEOUT,
                    headers: { Authorization: 'Bearer ' + localStorage.getItem('authToken') },
                    success: function(response) {
                        if (!response.status) {
                            updatePreviewSummary();
                            setSubmitEnabled(true);
                            Swal.fire({
                                icon: 'error',
                                title: 'Import Failed',
                                html: response.message || 'Unable to import products.',
                                confirmButtonColor: '#ff9f43'
                            });
                            return;
                        }

                        totals.created = response.created_count || totals.created;
                        totals.updated = response.updated_count || totals.updated;
                        var processed = (totals.created || 0) + (totals.updated || 0);
                        $('#importSubmitBtn').text('Importing... ' + processed.toLocaleString('en-IN') + ' done');

                        if (response.done) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Import Summary',
                                html: '<strong>' + (response.message || 'Import completed.') + '</strong>',
                                confirmButtonColor: '#ff9f43'
                            }).then(function() {
                                window.location.href = "{{ route('product.list') }}";
                            });
                            return;
                        }

                        runImportBatch(response.next_offset || 0, totals, batchSize, 0);
                    },
                    error: function(xhr) {
                        if ((xhr.status === 504 || xhr.status === 502 || xhr.status === 503) && batchSize > 5 && retryCount < 4) {
                            var smallerBatch = Math.max(5, Math.floor(batchSize / 2));
                            $('#importSubmitBtn').text('Retrying with smaller batch (' + smallerBatch + ')...');
                            runImportBatch(fileOffset, totals, smallerBatch, retryCount + 1);
                            return;
                        }
                        updatePreviewSummary();
                        setSubmitEnabled(true);
                        var message = 'An unexpected error occurred.';
                        if (xhr.statusText === 'timeout') {
                            message = 'Import timed out. Please try again — smaller batches will resume automatically.';
                        } else if (xhr.status === 504 || xhr.status === 502) {
                            message = 'Server timed out during import. Please try again — the import uses smaller batches automatically.';
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            message = xhr.responseText.substring(0, 300);
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Import Failed',
                            text: message,
                            confirmButtonColor: '#ff9f43'
                        });
                    }
                });
            }

            runImportBatch(0, { created: 0, updated: 0 }, 25, 0);
        });

        if (previewReady) {
            showPreviewTable();
            loadPreviewPage(1);
        } else {
            startImportProcessing();
        }
    });
</script>
@endpush

@extends('layout.app')

@section('title', 'GST Reports')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" />

<!-- Mobile Filter Backdrop & Bottom Sheet (<768px) -->
<div class="gst-filter-backdrop d-md-none" id="gstFilterBackdrop"></div>
<div class="gst-filter-sheet d-md-none" id="gstFilterSheet" role="dialog" aria-modal="true" aria-labelledby="gstFilterSheetTitle">
    <div class="gst-filter-sheet-head">
        <span id="gstFilterSheetTitle"><i class="fas fa-sliders-h me-2 text-warning"></i>Filter GST Reports</span>
        <button type="button" class="gst-filter-sheet-close" id="gstFilterSheetClose" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="gst-filter-sheet-body" id="gstFilterSheetBody"></div>
    <div class="gst-filter-sheet-foot">
        <button type="button" class="gst-filter-clear" id="gstFilterClear">Reset</button>
        <button type="button" class="gst-filter-apply" id="gstFilterApply">Apply Filters</button>
    </div>
</div>

<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>GST Reports</h4>
        </div>
        <div class="page-btn">
            <a href="{{ route('gst.validation.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-file-circle-check"></i> Validate GST Excel
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Mobile Filter Trigger Bar (<768px) -->
            <div class="gst-filter-bar d-md-none mb-3">
                <div class="gst-filter-summary" id="gstFilterSummaryTrigger">
                    <i class="fas fa-filter text-muted me-1"></i>
                    <span class="gst-filter-summary-text" id="gstFilterSummaryText">Tap to filter by search, date, year...</span>
                </div>
                <button type="button" class="gst-filter-toggle" id="gstFilterToggle" aria-label="Filters">
                    <i class="fas fa-sliders-h"></i>
                    <span class="gst-filter-count d-none" id="gstFilterCount">0</span>
                </button>
            </div>

            <!-- Desktop Filter Container (>=768px) -->
            <div id="desktopFilterContainer" class="d-none d-md-block mb-3">
                <form id="gstFilterForm">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3 filter-col filter-col-search">
                            <label for="gst_search_input" class="form-label fw-bold">Search</label>
                            <input type="text" id="gst_search_input" class="form-control form-control-sm" placeholder="Search report name...">
                        </div>
                        <div class="col-md-3 filter-col">
                            <label for="from_date" class="form-label fw-bold">Start Date</label>
                            <input type="date" id="from_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3 filter-col">
                            <label for="to_date" class="form-label fw-bold">End Date</label>
                            <input type="date" id="to_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3 filter-col">
                            <label for="year_select" class="form-label fw-bold">Year</label>
                            <select id="year_select" class="form-control form-control-sm">
                                <option value="">-- Select Year --</option>
                                @for ($i = date('Y'); $i >= 2020; $i--)
                                <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="mb-3">
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th style="width: 70px;">No</th>
                                <th>GST Reports Name</th>
                                <th class="text-end">Download PDF/Excel</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Goods and Services Tax - GSTR-2B</td>
                                <td class="text-end">
                                    {{-- <a href="{{ route('gst.gstr3b.export') }}" class="btn btn-primary py-1">
                                    <i class="fa-solid fa-file-pdf"></i> Export PDF
                                    </a> --}}
                                    <!-- <a href="{{ route('gst.sales.report.gstr3b.export') }}"
                                        class="btn btn-success export-link">
                                        <i class="fa-solid fa-file-excel"></i> Export Excel
                                    </a> -->

                                    <a class="btn btn-success" id="exportGSTR2B">
                                        <i class="fa-solid fa-file-excel"></i> Export Excel
                                    </a>

                                </td>
                            </tr>

                            <tr>
                                <td>2</td>
                                <td>Goods and Services Tax - GSTR-1</td>
                                <td class="text-end">
                                    {{-- <a class="btn btn-primary py-1">
                                            <i class="fa-solid fa-file-pdf"></i> Export PDF
                                        </a> --}}
                                    <a class="btn btn-success" id="exportExcel">
                                        <i class="fa-solid fa-file-excel"></i> Export Excel
                                    </a>



                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>GSTR-3B Summary</td>
                                <td class="text-end">
                                    <a href="#" id="exportGstr3bPdf" class="btn btn-primary py-1">
                                        <i class="fa-solid fa-file-pdf"></i> Export PDF
                                    </a>
                                    {{-- <a href="{{ route('sales.gstr3b.export') }}" class="btn btn-success">
                                    <i class="fa-solid fa-file-excel"></i> Export Excel
                                    </a> --}}
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>

            <style>
                .pdf-btn {
                    color: #ffffff !important;
                    /* Text white */
                }

                .form-select-sm {

                    width: 14% !important;

                }

                .pdf-btn i {
                    color: #ffffff !important;
                    /* Icon white */
                }

                .pdf-btn:hover {
                    background-color: #c82333 !important;
                    /* Hover color (red shade for PDF feel) */
                    color: #ffffff !important;
                    /* Text white on hover */
                }

                .pdf-btn:hover i {
                    color: #ffffff !important;
                    /* Icon white on hover */
                }

                .table tbody tr td a {
                    color: #ffffff !important;
                }

                /* Mobile Filter Trigger Bar (<768px) */
                .gst-filter-bar {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    background: #fff;
                    border-radius: 12px;
                    padding: 8px 10px;
                    margin-bottom: 12px;
                    box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
                }

                .gst-filter-summary {
                    flex: 1 1 auto;
                    min-width: 0;
                    display: flex;
                    align-items: center;
                    gap: 6px;
                    padding: 8px 12px;
                    background: #f8fafc;
                    border: 1px solid #e2e8f0;
                    border-radius: 8px;
                    cursor: pointer;
                }

                .gst-filter-summary-text {
                    font-size: 12.5px;
                    color: #475467;
                    font-weight: 500;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }

                .gst-filter-toggle {
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
                    background: #ff9f43;
                    color: #fff;
                    font-size: 15px;
                    box-shadow: 0 2px 6px rgba(255, 159, 67, .35);
                    cursor: pointer;
                }

                .gst-filter-toggle.has-active {
                    background: #ea580c;
                }

                .gst-filter-count {
                    position: absolute;
                    top: -4px;
                    right: -4px;
                    min-width: 18px;
                    height: 18px;
                    padding: 0 5px;
                    border-radius: 999px;
                    background: #1b2850;
                    color: #fff;
                    font-size: 10.5px;
                    font-weight: 700;
                    line-height: 18px;
                    text-align: center;
                }

                /* Bottom Sheet Filter */
                .gst-filter-backdrop {
                    display: block;
                    position: fixed;
                    inset: 0;
                    z-index: 1040;
                    background: rgba(16, 24, 40, .45);
                    opacity: 0;
                    pointer-events: none;
                    transition: opacity .2s ease;
                }

                .gst-filter-sheet {
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

                body.gst-filter-open .gst-filter-backdrop {
                    opacity: 1;
                    pointer-events: auto;
                }

                body.gst-filter-open .gst-filter-sheet {
                    transform: translateY(0);
                }

                body.gst-filter-open {
                    overflow: hidden;
                }

                body.gst-filter-open .mobile-bottom-nav,
                body.gst-filter-open .floating-calculator-wrapper {
                    display: none !important;
                }

                .gst-filter-sheet-head {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 16px 18px;
                    border-bottom: 1px solid #eef0f4;
                    font-size: 16px;
                    font-weight: 600;
                    color: #1b2850;
                }

                .gst-filter-sheet-close {
                    width: 32px;
                    height: 32px;
                    padding: 0;
                    border: 0;
                    background: transparent;
                    color: #64748b;
                    font-size: 20px;
                    cursor: pointer;
                }

                .gst-filter-sheet-body {
                    flex: 1 1 auto;
                    overflow-y: auto;
                    padding: 14px 18px;
                }

                .gst-filter-sheet-body #gstFilterForm .row {
                    margin: 0 !important;
                }

                .gst-filter-sheet-body #gstFilterForm .filter-col {
                    width: 100% !important;
                    max-width: 100% !important;
                    flex: 0 0 100% !important;
                    padding: 0 !important;
                    margin-bottom: 12px !important;
                }

                .gst-filter-sheet-body label {
                    display: block;
                    margin-bottom: 6px !important;
                    font-size: 13px !important;
                    font-weight: 600 !important;
                    color: #344054 !important;
                }

                .gst-filter-sheet-body .form-control {
                    height: 42px !important;
                    border-radius: 8px !important;
                    border-color: #d0d5dd !important;
                    font-size: 14px !important;
                    width: 100% !important;
                }

                .gst-filter-sheet-foot {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 12px;
                    padding: 14px 18px calc(14px + env(safe-area-inset-bottom));
                    border-top: 1px solid #eef0f4;
                    background: #fff;
                }

                .gst-filter-clear,
                .gst-filter-apply {
                    height: 44px;
                    border: 0;
                    border-radius: 22px;
                    font-size: 14.5px;
                    font-weight: 600;
                    cursor: pointer;
                }

                .gst-filter-clear {
                    background: #f1f5f9;
                    color: #475467;
                }

                .gst-filter-apply {
                    background: #ff9f43;
                    color: #fff;
                }

                @media (max-width: 767.98px) {
                    .dataTables_filter {
                        display: none !important;
                    }

                    .content {
                        padding: 12px 10px !important;
                    }

                    .page-header {
                        margin-bottom: 12px !important;
                    }

                    .page-header .page-title h4 {
                        font-size: 18px;
                    }

                    .page-header .btn {
                        padding: 6px 12px;
                        font-size: 12px;
                    }
                }

                :root[data-theme="dark"] .gst-filter-bar,
                :root[data-theme="dark"] .gst-filter-sheet,
                :root[data-theme="dark"] .gst-filter-sheet-head,
                :root[data-theme="dark"] .gst-filter-sheet-foot {
                    background: #1e293b !important;
                    border-color: #334155 !important;
                    color: #f8fafc !important;
                }

                :root[data-theme="dark"] .gst-filter-summary {
                    background: #0f172a !important;
                    border-color: #334155 !important;
                }

                :root[data-theme="dark"] .gst-filter-summary-text,
                :root[data-theme="dark"] .gst-filter-sheet-body label {
                    color: #cbd5e1 !important;
                }

                :root[data-theme="dark"] .gst-filter-clear {
                    background: #334155 !important;
                    color: #e2e8f0 !important;
                }
            </style>
            @endsection
            @push('js')
            <script>
                $('#exportGSTR2B').click(function(e) {
                    e.preventDefault();

                    const authToken = localStorage.getItem("authToken");
                    const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                    const from_date = $('#from_date').val();
                    const to_date = $('#to_date').val();
                    const year = $('#year_select').val();

                    const btn = $(this);
                    const originalHtml = btn.html();
                    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Exporting...');

                    $.ajax({
                        url: '/api/gstr3b/export-excel',
                        method: 'GET',
                        headers: {
                            "Authorization": "Bearer " + authToken
                        },
                        data: {
                            from_date: from_date,
                            to_date: to_date,
                            year: year,
                            selectedSubAdminId: selectedSubAdminId,
                        },
                        xhrFields: {
                            responseType: 'blob'
                        },
                        success: function(blob) {
                            // Check if the server returned an Excel file, not an error page
                            if (blob.type && blob.type.indexOf('spreadsheet') === -1 && blob.type.indexOf('excel') === -1 && blob.type.indexOf('octet-stream') === -1) {
                                // Server returned an error (HTML/JSON) — read and show it
                                const reader = new FileReader();
                                reader.onload = function() {
                                    try {
                                        const err = JSON.parse(reader.result);
                                        alert('Export failed: ' + (err.message || err.error || 'Server error'));
                                    } catch(e) {
                                        alert('Export failed: Server returned an invalid response. Please check your login session.');
                                    }
                                };
                                reader.readAsText(blob);
                                btn.prop('disabled', false).html(originalHtml);
                                return;
                            }
                            const url = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = 'GSTR-2B.xlsx';
                            document.body.appendChild(a);
                            a.click();
                            a.remove();
                            window.URL.revokeObjectURL(url);
                            btn.prop('disabled', false).html(originalHtml);
                        },
                        error: function(xhr) {
                            let msg = 'Failed to export Excel!';
                            if (xhr.responseText) {
                                try { msg = JSON.parse(xhr.responseText).message || msg; } catch(e) {}
                            }
                            alert(msg);
                            btn.prop('disabled', false).html(originalHtml);
                        }
                    });
                });


                $('#exportExcel').click(function(e) {
                    e.preventDefault();

                    const authToken = localStorage.getItem("authToken");
                    const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                    const from_date = $('#from_date').val();
                    const to_date = $('#to_date').val();
                    const year = $('#year_select').val();

                    const btn = $(this);
                    const originalHtml = btn.html();
                    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Exporting...');

                    $.ajax({
                        url: '/api/gstr1/export-excel',
                        method: 'GET',
                        data: {
                            from_date: from_date,
                            to_date: to_date,
                            year: year,
                            selectedSubAdminId: selectedSubAdminId,
                        },
                        headers: {
                            "Authorization": "Bearer " + authToken
                        },
                        xhrFields: {
                            responseType: 'blob'
                        },
                        success: function(blob) {
                            // Validate it's actually an Excel file, not an error page
                            if (blob.type && blob.type.indexOf('spreadsheet') === -1 && blob.type.indexOf('excel') === -1 && blob.type.indexOf('octet-stream') === -1) {
                                const reader = new FileReader();
                                reader.onload = function() {
                                    try {
                                        const err = JSON.parse(reader.result);
                                        alert('Export failed: ' + (err.message || err.error || 'Server error'));
                                    } catch(e) {
                                        alert('Export failed: Server returned an invalid response. Please check your login session.');
                                    }
                                };
                                reader.readAsText(blob);
                                btn.prop('disabled', false).html(originalHtml);
                                return;
                            }
                            const url = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = 'GSTR1_Multiple_Sheets.xlsx';
                            document.body.appendChild(a);
                            a.click();
                            a.remove();
                            window.URL.revokeObjectURL(url);
                            btn.prop('disabled', false).html(originalHtml);
                        },
                        error: function(xhr) {
                            let msg = 'Failed to export Excel!';
                            if (xhr.responseText) {
                                try { msg = JSON.parse(xhr.responseText).message || msg; } catch(e) {}
                            }
                            alert(msg);
                            btn.prop('disabled', false).html(originalHtml);
                        }
                    });
                });

                $('#exportGstr3bPdf').click(function(e) {
                    e.preventDefault();

                    const authToken = localStorage.getItem("authToken");
                    const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                    const from_date = $('#from_date').val();
                    const to_date = $('#to_date').val();
                    const year = $('#year_select').val();

                    const btn = $(this);
                    const originalHtml = btn.html();
                    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Exporting...');

                    $.ajax({
                        url: '/api/gst/gstr-3b/export',
                        method: 'GET',
                        data: {
                            from_date: from_date,
                            to_date: to_date,
                            year: year,
                            selectedSubAdminId: selectedSubAdminId,
                        },
                        headers: {
                            "Authorization": "Bearer " + authToken
                        },
                        success: function(response) {
                            if (response.status && response.file_url) {
                                const a = document.createElement('a');
                                a.href = response.file_url;
                                a.download = response.file_name || 'GSTR-3B-Summary.pdf';
                                document.body.appendChild(a);
                                a.click();
                                a.remove();
                            } else {
                                alert('Export failed: ' + (response.message || 'Unknown error'));
                            }
                            btn.prop('disabled', false).html(originalHtml);
                        },
                        error: function() {
                            alert('Failed to export PDF!');
                            btn.prop('disabled', false).html(originalHtml);
                        }
                    });
                });

                $('.export-link').click(function(e) {
                    e.preventDefault();
                    let url = $(this).attr('href');
                    const from_date = $('#from_date').val();
                    const to_date = $('#to_date').val();
                    const year = $('#year_select').val();

                    let params = [];
                    if (from_date) params.push('from_date=' + from_date);
                    if (to_date) params.push('to_date=' + to_date);
                    if (year) params.push('year=' + year);

                    if (params.length > 0) {
                        url += (url.includes('?') ? '&' : '?') + params.join('&');
                    }

                    window.location.href = url;
                });

                // Mobile Filter Bottom Sheet Logic
                initMobileFilterSheet();

                function initMobileFilterSheet() {
                    const sheetBody = document.getElementById('gstFilterSheetBody');
                    const desktopContainer = document.getElementById('desktopFilterContainer');
                    const form = document.getElementById('gstFilterForm');
                    const filterQuery = window.matchMedia('(max-width: 767.98px)');

                    function placeFilterFields() {
                        if (!sheetBody || !desktopContainer || !form) return;
                        if (filterQuery.matches) {
                            if (!sheetBody.contains(form)) {
                                sheetBody.appendChild(form);
                            }
                        } else {
                            if (!desktopContainer.contains(form)) {
                                desktopContainer.appendChild(form);
                                closeGstFilterSheet();
                            }
                        }
                    }

                    function openGstFilterSheet() {
                        placeFilterFields();
                        document.body.classList.add('gst-filter-open');
                        $('#gstFilterToggle').attr('aria-expanded', 'true');
                    }

                    function closeGstFilterSheet() {
                        document.body.classList.remove('gst-filter-open');
                        $('#gstFilterToggle').attr('aria-expanded', 'false');
                    }

                    window.closeGstFilterSheet = closeGstFilterSheet;

                    function updateFilterSummary() {
                        const search = ($('#gst_search_input').val() || '').trim();
                        const fromDate = $('#from_date').val();
                        const toDate = $('#to_date').val();
                        const year = $('#year_select').val();

                        let count = 0;
                        let parts = [];

                        if (search) {
                            count++;
                            parts.push(`"${search}"`);
                        }
                        if (fromDate) {
                            count++;
                            parts.push(`From: ${fromDate}`);
                        }
                        if (toDate) {
                            count++;
                            parts.push(`To: ${toDate}`);
                        }
                        if (year) {
                            count++;
                            parts.push(year);
                        }

                        const countEl = $('#gstFilterCount');
                        const toggleEl = $('#gstFilterToggle');
                        if (count > 0) {
                            countEl.text(count).removeClass('d-none');
                            toggleEl.addClass('has-active');
                        } else {
                            countEl.addClass('d-none');
                            toggleEl.removeClass('has-active');
                        }

                        const summaryText = parts.length > 0 ? parts.join(' • ') : 'Tap to filter by search, date, year...';
                        $('#gstFilterSummaryText').text(summaryText);
                    }

                    window.updateGstFilterSummary = updateFilterSummary;

                    $(document).on('click', '#gstFilterToggle, #gstFilterSummaryTrigger', function() {
                        openGstFilterSheet();
                    });

                    $(document).on('click', '#gstFilterSheetClose, #gstFilterBackdrop', function() {
                        closeGstFilterSheet();
                    });

                    $(document).on('keydown', function(e) {
                        if (e.key === 'Escape') closeGstFilterSheet();
                    });

                    $('#gstFilterClear').on('click', function() {
                        $('#gst_search_input').val('');
                        $('#from_date').val('');
                        $('#to_date').val('');
                        $('#year_select').val('');
                        if ($.fn.DataTable.isDataTable('.datanew')) {
                            $('.datanew').DataTable().search('').draw();
                        }
                        updateFilterSummary();
                    });

                    $('#gstFilterApply').on('click', function() {
                        closeGstFilterSheet();
                        const searchVal = ($('#gst_search_input').val() || '').trim();
                        if ($.fn.DataTable.isDataTable('.datanew')) {
                            $('.datanew').DataTable().search(searchVal).draw();
                        }
                        updateFilterSummary();
                    });

                    $(document).on('change', '#from_date, #to_date, #year_select', function() {
                        updateFilterSummary();
                    });

                    $(document).on('keyup input', '#gst_search_input', function() {
                        const val = $(this).val();
                        if ($.fn.DataTable.isDataTable('.datanew')) {
                            $('.datanew').DataTable().search(val).draw();
                        }
                        updateFilterSummary();
                    });

                    $(document).on('keyup input', '.dataTables_filter input', function() {
                        $('#gst_search_input').val($(this).val());
                        updateFilterSummary();
                    });

                    placeFilterFields();
                    updateFilterSummary();

                    if (typeof filterQuery.addEventListener === 'function') {
                        filterQuery.addEventListener('change', placeFilterFields);
                    } else if (typeof filterQuery.addListener === 'function') {
                        filterQuery.addListener(placeFilterFields);
                    }
                }
            </script>
            @endpush
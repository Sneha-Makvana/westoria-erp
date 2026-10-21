@extends('layout.app')

@section('title', 'Ticket Report')

@section('content')
<style>
    /* ── Badges ─────────────────────────────────────────────── */
    .ticket-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }
    .ticket-priority-low      { background: #eef6ff; color: #1d4ed8; }
    .ticket-priority-medium   { background: #fff7e6; color: #b45309; }
    .ticket-priority-high     { background: #fff1f2; color: #be123c; }
    .ticket-priority-urgent   { background: #fee2e2; color: #991b1b; }
    .ticket-status-open       { background: #eef6ff; color: #1d4ed8; }
    .ticket-status-in_progress{ background: #fff7e6; color: #b45309; }
    .ticket-status-resolved   { background: #ecfdf5; color: #047857; }
    .ticket-status-closed     { background: #f3f4f6; color: #4b5563; }

    /* ── Table ───────────────────────────────────────────────── */
    .datanew td,
    .datanew th {
        vertical-align: middle;
        white-space: normal;
        word-break: break-word;
    }

    /* ── DataTable chrome suppression ────────────────────────── */
    .dataTables_filter,
    .dataTables_length,
    .dataTables_info,
    .dataTables_paginate { display: none !important; }
    .dataTables_wrapper .row:first-child { display: none !important; }
    .dataTables_wrapper { margin-top: 0 !important; padding-top: 0 !important; }

    /* ── Custom Pagination ───────────────────────────────────── */
    .pagination .page-item .page-link {
        background-color: #5d6d7e;
        color: #fff;
        border: none;
        margin: 0 3px;
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: bold;
    }
    .pagination .page-item:first-child .page-link,
    .pagination .page-item:last-child .page-link {
        background-color: #fff;
        color: #6c757d;
        border: 1px solid #dee2e6;
    }
    .pagination .page-item.active .page-link {
        background-color: #ff9f43 !important;
        color: #fff;
    }
    .pagination .page-item .page-link:hover  { background-color: #4a5766; color: #fff; }
    .pagination .page-item.active .page-link:hover { background-color: #e68a35 !important; }
    .pagination .page-item.disabled .page-link {
        background-color: #fff !important;
        color: #dee2e6 !important;
        border: 1px solid #dee2e6 !important;
        pointer-events: none !important;
    }

    /* ── Mobile toggle button ────────────────────────────────── */
    .mobile-toggle-btn-table {
        background: #ff9f43;
        color: white;
        border: none;
        border-radius: 50%;
        width: 32px; height: 32px;
        min-width: 32px; min-height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 18px;
        font-weight: bold;
        transition: all 0.3s ease;
        padding: 0; margin: 0;
        box-shadow: 0 2px 4px rgba(0,0,0,.1);
    }
    .mobile-toggle-btn-table:hover { background: #ff8c2e; }
    .mobile-toggle-btn-table.minus { background: #dc3545; }
    .mobile-toggle-btn-table.minus:hover { background: #c82333; }

    /* ── Expandable rows ─────────────────────────────────────── */
    .order-details-row { display: none; }
    .order-details-row.show { display: table-row; }

    .order-details-content .detail-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #e9ecef;
    }
    .order-details-content .detail-item:last-child { border-bottom: none; }
    .order-details-content .detail-label { font-weight: 600; color: #495057; }
    .order-details-content .detail-value  { color: #212529; }

    /* ── Search input ────────────────────────────────────────── */
    .search-input { position: relative; display: flex; align-items: center; }
    .search-input input { padding-left: 35px !important; border-radius: 5px; height: 30px; }
    .btn-searchset {
        position: absolute; left: 10px; z-index: 10;
        padding: 0; top: 4px !important;
    }

    /* ── Responsive: hide Details col on desktop ─────────────── */
    @media screen and (min-width: 1025px) {
        .datanew thead th:nth-child(2),
        .datanew tbody td:nth-child(2) { display: none; }
        .order-details-row { display: none !important; }
    }
    @media screen and (min-width: 768px) and (max-width: 1024px) {
        .datanew thead th:nth-child(2),
        .datanew tbody td:nth-child(2) { display: none; }
    }
    @media screen and (max-width: 767px) {
        .table-responsive { overflow-x: visible !important; }
        .datanew thead th:nth-child(n+4),
        .datanew tbody td:nth-child(n+4) { display: none; }
    }

    /* ── Wordset dropdown ────────────────────────────────────── */
    .wordset .dropdown-menu {
        display: none; position: absolute; right: 0; left: auto;
        flex-direction: column; z-index: 2000;
    }
    .wordset .dropdown-menu.show { display: block; }
    .wordset .dropdown-menu li { display: block; float: none; }
    .wordset .dropdown-menu .dropdown-item { display: block; }

    /* ── Summary count boxes ─────────────────────────────────── */
    .summary-badges-row {
        display: flex; flex-wrap: wrap; gap: 10px; margin-top: 12px;
    }
    .summary-badge-box {
        display: inline-flex; align-items: center; gap: 6px;
        min-height: 34px; padding: 6px 12px; border-radius: 6px;
        border: 1px solid; font-size: 14px; font-weight: 600; background: #fff;
    }
    .summary-badge-box.open       { color: #1d4ed8; border-color: #bfdbfe; background: #eff6ff; }
    .summary-badge-box.in-progress{ color: #b45309; border-color: #fde68a; background: #fffbeb; }
    .summary-badge-box.resolved   { color: #047857; border-color: #a7f3d0; background: #ecfdf5; }
    .summary-badge-box.closed     { color: #4b5563; border-color: #d1d5db; background: #f9fafb; }

    @media screen and (max-width: 767.98px) {
        .summary-badges-row { flex-direction: column; gap: 8px; width: 100%; }
        .summary-badge-box  { display: flex; justify-content: flex-start; width: 100%; }
    }
</style>

<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Ticket Report</h4>
        </div>
        <div class="page-btn">
            <a href="{{ route('ticket.list') }}" class="btn btn-added">Back</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body" style="overflow:auto;">

            {{-- ── Toolbar (mirrors salesreport layout) ── --}}
            <div class="table-top mb-2">
                <form method="GET" action="{{ route('ticket.report') }}" id="ticket-report-form">
                    <div class="search-set d-flex flex-wrap align-items-center gap-2">

                        {{-- Search --}}
                        <div class="search-input" style="min-width:180px;">
                            <a class="btn btn-searchset">
                                <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}" alt="img">
                            </a>
                            <input type="text" name="search" id="search-input"
                                   class="form-control" placeholder="Ticket no, subject…"
                                   value="{{ request('search') }}">
                        </div>

                        {{-- Status --}}
                        <select name="status" class="form-select form-select-sm" style="min-width:130px;">
                            <option value="">All Status</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('_', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Priority --}}
                        <select name="priority" class="form-select form-select-sm" style="min-width:130px;">
                            <option value="">All Priority</option>
                            @foreach ($priorities as $priority)
                                <option value="{{ $priority }}" {{ request('priority') === $priority ? 'selected' : '' }}>
                                    {{ ucfirst($priority) }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Customer --}}
                        <select name="customer_id" class="form-select form-select-sm" style="min-width:160px;">
                            <option value="">All Customers</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}"
                                    {{ (string) request('customer_id') === (string) $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Assigned Staff --}}
                        <select name="assigned_to" class="form-select form-select-sm" style="min-width:160px;">
                            <option value="">All Staff</option>
                            @foreach ($staffMembers as $staff)
                                <option value="{{ $staff->id }}"
                                    {{ (string) request('assigned_to') === (string) $staff->id ? 'selected' : '' }}>
                                    {{ $staff->name }}
                                </option>
                            @endforeach
                        </select>

                        {{-- From date --}}
                        <input type="date" name="from_date" class="form-control form-control-sm"
                               style="min-width:130px;" value="{{ request('from_date') }}">

                        {{-- To date --}}
                        <input type="date" name="to_date" class="form-control form-control-sm"
                               style="min-width:130px;" value="{{ request('to_date') }}">

                        {{-- Go / Reset --}}
                        <button class="btn btn-submit btn-sm" type="submit">Go</button>
                        <a href="{{ route('ticket.report') }}" class="btn btn-cancel btn-sm">Reset</a>
                    </div>
                </form>

                {{-- Export / PDF buttons (same wordset style as sales report) --}}
                <div class="wordset mt-2">
                    <ul>
                        <li>
                            <a href="javascript:void(0);" id="ticket-report-pdf-btn"
                               data-bs-toggle="tooltip" data-bs-placement="top" title="Download PDF">
                                <button class="btn btn-primary btn-sm">
                                    <i class="fas fa-file-pdf"></i> View PDF
                                </button>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- ── Summary count badges ── --}}
            @php
                $openCount       = $tickets->where('status', 'open')->count();
                $inProgCount     = $tickets->where('status', 'in_progress')->count();
                $resolvedCount   = $tickets->where('status', 'resolved')->count();
                $closedCount     = $tickets->where('status', 'closed')->count();
            @endphp
            <div class="summary-badges-row mb-3">
                @if ($openCount)
                <div class="summary-badge-box open">
                    <span>Open:</span><span>{{ $openCount }}</span>
                </div>
                @endif
                @if ($inProgCount)
                <div class="summary-badge-box in-progress">
                    <span>In Progress:</span><span>{{ $inProgCount }}</span>
                </div>
                @endif
                @if ($resolvedCount)
                <div class="summary-badge-box resolved">
                    <span>Resolved:</span><span>{{ $resolvedCount }}</span>
                </div>
                @endif
                @if ($closedCount)
                <div class="summary-badge-box closed">
                    <span>Closed:</span><span>{{ $closedCount }}</span>
                </div>
                @endif
            </div>

            {{-- ── Table ── --}}
            <div class="table-responsive mt-2">
                <table class="table datanew" id="ticket-report-table">
                    <thead>
                        <tr>
                            <th>Ticket No</th>
                            <th class="text-center">Details</th>
                            <th>Customer</th>
                            <th>Subject</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <th>Assigned By</th>
                            <th>Resolved</th>
                            <th>Closed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tickets as $ticket)
                            @php
                                $priorityClass = 'ticket-priority-' . strtolower($ticket->priority ?? 'medium');
                                $statusClass   = 'ticket-status-'   . strtolower($ticket->status   ?? 'open');
                                $rowId         = 'tr-ticket-' . $ticket->id;
                            @endphp
                            <tr data-ticket-id="{{ $ticket->id }}">
                                <td><strong>{{ $ticket->ticket_no ?? '-' }}</strong></td>

                                {{-- Mobile toggle --}}
                                <td class="text-center">
                                    <button class="mobile-toggle-btn-table"
                                            onclick="toggleTicketRowDetails('{{ $ticket->id }}')">+</button>
                                </td>

                                <td>{{ $ticket->customer->name ?? '-' }}</td>
                                <td>{{ $ticket->subject }}</td>
                                <td><span class="ticket-badge {{ $priorityClass }}">{{ ucfirst($ticket->priority ?? '-') }}</span></td>
                                <td><span class="ticket-badge {{ $statusClass }}">{{ ucwords(str_replace('_', ' ', $ticket->status ?? '-')) }}</span></td>
                                <td>{{ $ticket->assignedTo->name ?? 'Unassigned' }}</td>
                                <td>{{ $ticket->assignedBy->name ?? '-' }}</td>
                                <td>{{ optional($ticket->resolved_at)->format('d-m-Y h:i A') ?? '-' }}</td>
                                <td>{{ optional($ticket->closed_at)->format('d-m-Y h:i A') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted">No tickets found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ── Pagination (custom style matching sales report) ── --}}
            <div class="pagination-controls d-flex flex-column flex-md-row justify-content-between align-items-center mt-3">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <span class="me-2" style="font-size:14px;color:#555;">Show per page:</span>
                    <span class="ms-3" style="font-size:14px;color:#555;">
                        {{ $tickets->firstItem() ?? 0 }} –
                        {{ $tickets->lastItem()  ?? 0 }} of
                        {{ $tickets->total() }} items
                    </span>
                </div>
                <nav aria-label="Ticket report pagination">
                    {{ $tickets->links() }}
                </nav>
            </div>

        </div>
    </div>
</div>
@endsection

@push('js')
<script>
// ── Ticket report data map (for expandable rows) ──────────────────
window.ticketReportDataMap = {};

@foreach ($tickets as $ticket)
window.ticketReportDataMap['{{ $ticket->id }}'] = {
    ticket_no    : @json($ticket->ticket_no ?? '-'),
    branch       : @json($ticket->branch->name ?? '-'),
    customer     : @json($ticket->customer->name ?? '-'),
    subject      : @json($ticket->subject),
    priority     : @json(ucfirst($ticket->priority ?? '-')),
    priorityClass: @json('ticket-priority-' . strtolower($ticket->priority ?? 'medium')),
    status       : @json(ucwords(str_replace('_', ' ', $ticket->status ?? '-'))),
    statusClass  : @json('ticket-status-' . strtolower($ticket->status ?? 'open')),
    assigned_to  : @json($ticket->assignedTo->name ?? 'Unassigned'),
    assigned_by  : @json($ticket->assignedBy->name ?? '-'),
    resolved_at  : @json(optional($ticket->resolved_at)->format('d-m-Y h:i A') ?? '-'),
    closed_at    : @json(optional($ticket->closed_at)->format('d-m-Y h:i A') ?? '-'),
};
@endforeach

// ── Build expandable row HTML ─────────────────────────────────────
function buildTicketExpandableContent(item) {
    return `
        <div class="order-details-content p-3">
            <div class="detail-item">
                <span class="detail-label">Ticket No:</span>
                <span class="detail-value"><strong>${item.ticket_no}</strong></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Customer:</span>
                <span class="detail-value">${item.customer}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Subject:</span>
                <span class="detail-value">${item.subject}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Priority:</span>
                <span class="detail-value">
                    <span class="ticket-badge ${item.priorityClass}">${item.priority}</span>
                </span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Status:</span>
                <span class="detail-value">
                    <span class="ticket-badge ${item.statusClass}">${item.status}</span>
                </span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Assigned To:</span>
                <span class="detail-value">${item.assigned_to}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Assigned By:</span>
                <span class="detail-value">${item.assigned_by}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Resolved:</span>
                <span class="detail-value">${item.resolved_at}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Closed:</span>
                <span class="detail-value">${item.closed_at}</span>
            </div>
        </div>`;
}

// ── Toggle expandable row ─────────────────────────────────────────
function toggleTicketRowDetails(ticketId) {
    if ($(window).width() > 1024) return;

    const row       = $(`tr[data-ticket-id="${ticketId}"]`);
    const detailsRow= row.next(`tr.order-details-row[data-ticket-details-id="${ticketId}"]`);
    const btn       = row.find('.mobile-toggle-btn-table');

    if (detailsRow.length === 0) {
        const item = window.ticketReportDataMap[ticketId];
        if (!item) return;
        const newRow = $(`
            <tr class="order-details-row show" data-ticket-details-id="${ticketId}">
                <td colspan="10">${buildTicketExpandableContent(item)}</td>
            </tr>`);
        row.after(newRow);
        btn.addClass('minus').html('-');
    } else {
        if (detailsRow.hasClass('show')) {
            detailsRow.removeClass('show');
            btn.removeClass('minus').html('+');
        } else {
            detailsRow.addClass('show');
            btn.addClass('minus').html('-');
        }
    }
}

// ── Remove expandable rows on resize to desktop ───────────────────
let resizeTimer;
$(window).on('resize', function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
        if ($(window).width() > 1024) {
            $('.order-details-row').remove();
            $('.mobile-toggle-btn-table').removeClass('minus').html('+');
        }
    }, 250);
});

// ── PDF button: opens PDF export with current filters ─────────────
$(document).ready(function () {
    // Init DataTable (display-only, no built-in controls)
    if (!$.fn.DataTable.isDataTable('#ticket-report-table')) {
        $('#ticket-report-table').DataTable({
            dom:       't',
            paging:    false,
            info:      false,
            searching: false,
        });
    }

    $('#ticket-report-pdf-btn').on('click', function () {
        const params = new URLSearchParams({
            search      : '{{ request('search') }}',
            status      : '{{ request('status') }}',
            priority    : '{{ request('priority') }}',
            customer_id : '{{ request('customer_id') }}',
            assigned_to : '{{ request('assigned_to') }}',
            from_date   : '{{ request('from_date') }}',
            to_date     : '{{ request('to_date') }}',
            export      : 'pdf',
        });
        window.open('/ticket-report-pdf?' + params.toString(), '_blank');
    });
});
</script>
@endpush

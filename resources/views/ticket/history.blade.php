@extends('layout.app')

@section('title', 'Customer Ticket History')

@section('content')
<style>
    /* ── Badges (identical to index.blade.php) ───────── */
    .ticket-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }
    .ticket-priority-low      { background:#eef6ff; color:#1d4ed8; }
    .ticket-priority-medium   { background:#fff7e6; color:#b45309; }
    .ticket-priority-high     { background:#fff1f2; color:#be123c; }
    .ticket-priority-urgent   { background:#fee2e2; color:#991b1b; }
    .ticket-status-open       { background:#eef6ff; color:#1d4ed8; }
    .ticket-status-in_progress{ background:#fff7e6; color:#b45309; }
    .ticket-status-resolved   { background:#ecfdf5; color:#047857; }
    .ticket-status-closed     { background:#f3f4f6; color:#4b5563; }

    /* ── Customer info card ──────────────────────────── */
    .customer-info-card {
        background: linear-gradient(135deg, #fff7ee 0%, #fff 100%);
        border: 1px solid #fde2c0;
        border-radius: 10px;
        padding: 18px 20px;
        margin-bottom: 20px;
    }
    .customer-info-card .customer-name {
        font-size: 20px;
        font-weight: 700;
        color: #1b2850;
    }
    .customer-info-card .customer-meta {
        font-size: 13px;
        color: #6c757d;
        margin-top: 4px;
    }
    .ticket-count-badge {
        background: #ff9f43;
        color: #fff;
        border-radius: 20px;
        padding: 4px 14px;
        font-size: 13px;
        font-weight: 600;
        display: inline-block;
    }

    /* ── Back button ─────────────────────────────────── */
    .history-back-btn {
        background: #ff9f43;
        border-color: #ff9f43;
        color: #fff;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .history-back-btn:hover {
        background: #f08f2e;
        border-color: #f08f2e;
        color: #fff;
    }

    /* ── Table (desktop) ─────────────────────────────── */
    table.history-table {
        table-layout: auto !important;
        width: 100% !important;
    }
    table.history-table td,
    table.history-table th {
        vertical-align: middle;
        white-space: normal;
        word-break: break-word;
        overflow-wrap: anywhere;
        padding: 10px 8px !important;
    }
    table.history-table th {
        background: #f8f9fa;
        font-weight: 600;
        white-space: nowrap;
    }

    /* ── Action buttons (identical to index) ─────────── */
    .action-buttons {
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }
    .icon-btn {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        text-decoration: none;
    }
    .icon-btn:hover { background: #ececec; }

    /* ── Empty state ─────────────────────────────────── */
    .empty-history {
        text-align: center;
        padding: 50px 20px;
        color: #6c757d;
    }
    .empty-history i {
        font-size: 48px;
        color: #dee2e6;
        margin-bottom: 12px;
        display: block;
    }

    /* ── DESKTOP: hide details-column, show all cols ─── */
    @media (min-width: 1200px) {
        table.history-table thead th.details-column,
        table.history-table tbody td.details-cell { display: none !important; }
    }

    /* ── MOBILE/TABLET (<1200px): same as index ──────── */
    @media (max-width: 1199px) {
        /* Hide all columns except first two */
        table.history-table thead th:nth-child(n+3),
        table.history-table tbody td:nth-child(n+3) { display: none !important; }

        /* Show first cell */
        table.history-table thead th:first-child,
        table.history-table tbody td:first-child { display: table-cell !important; }

        /* Hide separate details column — toggle is inside first cell */
        table.history-table thead th.details-column,
        table.history-table tbody td.details-cell { display: none !important; }

        /* First cell: position relative for absolute toggle button */
        table.history-table tbody td:first-child {
            position: relative;
            padding-right: 48px !important;
        }

        /* Toggle button — identical to index.blade.php */
        .ticket-toggle-inline {
            position: absolute;
            top: 10px;
            right: 8px;
            background: #ff9f43;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #fff;
            font-size: 20px;
            font-weight: bold;
            line-height: 1;
            text-decoration: none;
            transition: background 0.2s;
            flex-shrink: 0;
        }
        .ticket-toggle-inline.open { background: #dc3545; }
        .ticket-toggle-inline:hover { opacity: 0.85; }

        /* Links inside toggle button keep white text */
        .table tbody tr td a { color: white !important; }

        /* Customer name under ticket number in first cell */
        table.history-table tbody td:first-child .ticket-customer-mobile {
            white-space: normal !important;
            word-break: break-word !important;
            overflow-wrap: anywhere !important;
            font-size: 13px;
            color: #333;
            margin-top: 2px;
        }
    }

    /* ── iPad Pro (768–1199px): keep single-row toolbar ─ */
    @media (min-width: 768px) and (max-width: 1199px) {
        .customer-info-card .customer-name { font-size: 17px; }
    }

    /* ── Phones (<768px) ─────────────────────────────── */
    @media (max-width: 767px) {
        .customer-info-card .customer-name { font-size: 16px; }
    }
</style>

<div class="content">

    {{-- ── Page Header ── --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="page-title">
            <h4>Customer Ticket History</h4>
        </div>
        <a href="{{ url()->previous() }}" class="btn btn-sm history-back-btn">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    {{-- ── Customer Info Card ── --}}
    <div class="customer-info-card d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <div class="customer-name">{{ $customer->name }}</div>
            <div class="customer-meta">
                @if($customer->phone)
                    <i class="fas fa-phone me-1"></i>{{ $customer->phone }}
                @endif
                @if($customer->email)
                    @if($customer->phone) &nbsp;·&nbsp; @endif
                    <i class="fas fa-envelope me-1"></i>{{ $customer->email }}
                @endif
            </div>
        </div>
        <div class="d-flex align-items-center gap-3 mt-1">
            <span class="ticket-count-badge">
                {{ $tickets->count() }} {{ Str::plural('Ticket', $tickets->count()) }}
            </span>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            @if($tickets->isEmpty())
                <div class="empty-history">
                    <i class="fas fa-ticket-alt"></i>
                    <h5>No tickets found</h5>
                    <p>This customer has no ticket history yet.</p>
                </div>
            @else

                {{-- ── Table (same structure as index.blade.php) ── --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped history-table mb-0">
                        <thead>
                            <tr>
                                <th>Ticket No</th>
                                <th class="details-column" style="display:none;"></th>
                                <th>Order Number</th>
                                <th>Product Name</th>
                                <th>Subject</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="text-center" style="width:80px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $index => $ticket)
                                @php
                                    $priority      = $ticket->priority ?? 'medium';
                                    $status        = $ticket->status   ?? 'open';
                                    $priorityClass = 'ticket-priority-' . $priority;
                                    $statusClass   = 'ticket-status-'   . $status;
                                    $statusLabel   = ucwords(str_replace('_', ' ', $status));
                                    $priorityLabel = ucfirst($priority);
                                    $assignedName  = optional($ticket->assignedTo)->name ?? 'Unassigned';
                                    $createdDate   = optional($ticket->created_at)->format('d-m-Y') ?? '-';
                                    $ticketNo      = $ticket->ticket_no ?? '-';
                                    $subject       = $ticket->subject ?? '-';
                                    $orderName     = optional($ticket->order)->order_number ?? '-';
                                    $productName   = optional($ticket->product)->name ?? optional($ticket->product)->product_name ?? (optional($ticket->product)->id ? 'Product #'.$ticket->product->id : '-');
                                @endphp
                                <tr>
                                    {{-- ── First cell: ticket no + mobile toggle + collapse ── --}}
                                    <td style="position:relative;">
                                        {{-- Toggle button — same as index.blade.php --}}
                                        <a href="#history-details-{{ $ticket->id }}"
                                           class="ticket-toggle-inline toggle-details d-xl-none"
                                           data-bs-toggle="collapse"
                                           data-id="{{ $ticket->id }}">
                                            <span class="toggle-icon">+</span>
                                        </a>

                                        {{-- Ticket number --}}
                                        <div><strong>{{ $ticketNo }}</strong></div>

                                        {{-- Collapsible details for mobile — same structure as index --}}
                                        <div class="collapse mt-2 d-xl-none" id="history-details-{{ $ticket->id }}">
                                            <p class="mb-1"><strong>Order:</strong> {{ $orderName }}</p>
                                            <p class="mb-1"><strong>Product:</strong> {{ $productName }}</p>
                                            <p class="mb-1"><strong>Subject:</strong> {{ $subject }}</p>
                                            <p class="mb-1"><strong>Priority:</strong> <span class="ticket-badge {{ $priorityClass }}">{{ $priorityLabel }}</span></p>
                                            <p class="mb-1"><strong>Status:</strong> <span class="ticket-badge {{ $statusClass }}">{{ $statusLabel }}</span></p>
                                            <p class="mb-1"><strong>Created:</strong> {{ $createdDate }}</p>
                                            <div class="mt-2">
                                                <div class="action-buttons">
                                                    @if(app('hasPermission')(33, 'view'))
                                                        <a class="icon-btn" href="{{ route('ticket.view', $ticket->id) }}" title="View Ticket">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M12 9C11.206 9.00524 10.4459 9.32299 9.88447 9.88447C9.32299 10.4459 9.00524 11.206 9 12C9 13.642 10.358 15 12 15C13.641 15 15 13.642 15 12C15 10.359 13.641 9 12 9Z" fill="#092C4C"/>
                                                                <path d="M12 5C4.36704 5 2.07304 11.617 2.05204 11.684L1.94604 12L2.05105 12.316C2.07305 12.383 4.36704 19 12 19C19.633 19 21.927 12.383 21.948 12.316L22.054 12L21.949 11.684C21.927 11.617 19.633 5 12 5ZM12 17C6.64904 17 4.57604 13.154 4.07404 12C4.57804 10.842 6.65204 7 12 7C17.351 7 19.424 10.846 19.926 12C19.422 13.158 17.348 17 12 17Z" fill="#092C4C"/>
                                                            </svg>
                                                        </a>
                                                    @endif
                                                    @if(app('hasPermission')(33, 'edit'))
                                                        <a class="icon-btn" href="{{ route('ticket.edit', $ticket->id) }}" title="Edit">
                                                            <svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M15.045 5.401C15.423 5.023 15.631 4.521 15.631 3.987C15.631 3.453 15.423 2.951 15.045 2.573L13.459 0.987001C13.081 0.609001 12.579 0.401001 12.045 0.401001C11.511 0.401001 11.009 0.609001 10.632 0.986001L0 11.585V16H4.413L15.045 5.401ZM12.045 2.401L13.632 3.986L12.042 5.57L10.456 3.985L12.045 2.401ZM2 14V12.415L9.04 5.397L10.626 6.983L3.587 14H2ZM0 18H16V20H0V18Z" fill="#092C4C"/>
                                                            </svg>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- ── Hidden details-cell (spacer) ── --}}
                                    <td class="details-cell" style="display:none;"></td>

                                    {{-- ── Desktop columns ── --}}
                                    <td>{{ $orderName }}</td>
                                    <td>{{ $productName }}</td>
                                    <td>{{ $subject }}</td>
                                    <td><span class="ticket-badge {{ $priorityClass }}">{{ $priorityLabel }}</span></td>
                                    <td><span class="ticket-badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                                    <td>{{ $createdDate }}</td>
                                    <td class="text-center">
                                        <div class="action-buttons">
                                            @if(app('hasPermission')(33, 'view'))
                                                <a class="icon-btn" href="{{ route('ticket.view', $ticket->id) }}" title="View Ticket">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M12 9C11.206 9.00524 10.4459 9.32299 9.88447 9.88447C9.32299 10.4459 9.00524 11.206 9 12C9 13.642 10.358 15 12 15C13.641 15 15 13.642 15 12C15 10.359 13.641 9 12 9Z" fill="#092C4C"/>
                                                        <path d="M12 5C4.36704 5 2.07304 11.617 2.05204 11.684L1.94604 12L2.05105 12.316C2.07305 12.383 4.36704 19 12 19C19.633 19 21.927 12.383 21.948 12.316L22.054 12L21.949 11.684C21.927 11.617 19.633 5 12 5ZM12 17C6.64904 17 4.57604 13.154 4.07404 12C4.57804 10.842 6.65204 7 12 7C17.351 7 19.424 10.846 19.926 12C19.422 13.158 17.348 17 12 17Z" fill="#092C4C"/>
                                                    </svg>
                                                </a>
                                            @endif
                                            @if(app('hasPermission')(33, 'edit'))
                                                <a class="icon-btn" href="{{ route('ticket.edit', $ticket->id) }}" title="Edit">
                                                    <svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M15.045 5.401C15.423 5.023 15.631 4.521 15.631 3.987C15.631 3.453 15.423 2.951 15.045 2.573L13.459 0.987001C13.081 0.609001 12.579 0.401001 12.045 0.401001C11.511 0.401001 11.009 0.609001 10.632 0.986001L0 11.585V16H4.413L15.045 5.401ZM12.045 2.401L13.632 3.986L12.042 5.57L10.456 3.985L12.045 2.401ZM2 14V12.415L9.04 5.397L10.626 6.983L3.587 14H2ZM0 18H16V20H0V18Z" fill="#092C4C"/>
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ── Status summary ── --}}
                <div class="d-flex justify-content-end mt-3">
                    <div class="d-flex gap-3 flex-wrap">
                        @php $statusCounts = $tickets->groupBy('status')->map->count(); @endphp
                        @foreach($statusCounts as $status => $count)
                            <span class="ticket-badge ticket-status-{{ $status }}">
                                {{ ucwords(str_replace('_', ' ', $status)) }}: {{ $count }}
                            </span>
                        @endforeach
                    </div>
                </div>

            @endif
        </div>
    </div>
</div>

@push('js')
<script>
$(document).ready(function () {
    // ── Toggle details icon — identical to index.blade.php ──
    $(document).on('click', '.toggle-details', function () {
        const btn  = $(this);
        const icon = btn.find('.toggle-icon');
        if (btn.hasClass('open')) {
            btn.removeClass('open');
            icon.text('+');
        } else {
            btn.addClass('open');
            icon.text('−');
        }
    });
});
</script>
@endpush

@endsection

@extends('layout.app')

@section('title', 'View Ticket')

@section('content')
<style>
    .new-btn {
        min-width: 90px;
        background: #1b2850;
        color: #fff;
        font-size: 14px;
        font-weight: 700;
        padding: 6px 10px !important;
        transition: all .5s ease;
    }

    .ticket-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 13px;
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

    .detail-label {
        font-weight: 600;
        color: #495057;
        font-size: 13px;
        margin-bottom: 4px;
    }
    .detail-value {
        color: #1b2850;
        font-size: 14px;
        word-break: break-word;
    }

    .ticket-header-bar {
        background: linear-gradient(135deg, #fff7ee 0%, #fff 100%);
        border: 1px solid #fde2c0;
        border-radius: 10px;
        padding: 16px 20px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }
    .ticket-no-display {
        font-size: 18px;
        font-weight: 700;
        color: #1b2850;
    }
    .ticket-no-display span {
        color: #ff9f43;
    }

    .section-title {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 6px;
        margin-bottom: 14px;
    }

    .attachment-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        color: #1b2850;
        font-size: 13px;
        text-decoration: none;
        transition: all 0.2s;
    }
    .attachment-link:hover {
        background: #ff9f43;
        color: #fff;
        border-color: #ff9f43;
    }

    .desc-box {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 14px 16px;
        font-size: 14px;
        color: #1b2850;
        line-height: 1.7;
        white-space: pre-wrap;
        word-break: break-word;
    }

    .action-row {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 6px;
    }

    @media (max-width: 575px) {
        .ticket-header-bar {
            flex-direction: column;
            align-items: flex-start;
        }
        .action-row .btn {
            flex: 1;
        }
    }
</style>

<div class="content">
    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-title">
            <h4>Ticket Details</h4>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if (app('hasPermission')(33, 'edit'))
                <a href="{{ route('ticket.edit', $ticket->id) }}" class="btn new-btn btn-primary text-white">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
            @endif
            <a href="{{ route('ticket.list') }}" class="btn new-btn btn-cancel">
                <i class="fa-solid fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    {{-- Ticket No + Badge Header Bar --}}
    <div class="ticket-header-bar">
        <div class="ticket-no-display">
            Ticket: <span>{{ $ticket->ticket_no ?? 'N/A' }}</span>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @php
                $priority      = $ticket->priority ?? 'medium';
                $status        = $ticket->status   ?? 'open';
                $priorityLabel = ucfirst($priority);
                $statusLabel   = ucwords(str_replace('_', ' ', $status));
            @endphp
            <span class="ticket-badge ticket-priority-{{ $priority }}">{{ $priorityLabel }}</span>
            <span class="ticket-badge ticket-status-{{ $status }}">{{ $statusLabel }}</span>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            {{-- ── Section 1: Basic Info ── --}}
            <div class="section-title">Ticket Information</div>
            <div class="row g-3 mb-4">

                <div class="col-6 col-lg-3">
                    <div class="detail-label">Customer</div>
                    <div class="detail-value">
                        {{ optional($ticket->customer)->name ?? 'N/A' }}
                        @if(optional($ticket->customer)->phone)
                            <div style="font-size:12px;color:#6c757d;">
                                <i class="fas fa-phone me-1"></i>{{ $ticket->customer->phone }}
                            </div>
                        @endif
                    </div>
                </div>

                @if($ticket->order)
                <div class="col-6 col-lg-3">
                    <div class="detail-label">Order Number</div>
                    <div class="detail-value">{{ $ticket->order->order_number }}</div>
                </div>
                @endif

                @if($ticket->product)
                <div class="col-6 col-lg-3">
                    <div class="detail-label">Product Name</div>
                    <div class="detail-value">{{ $ticket->product->name ?? $ticket->product->product_name ?? 'Product #'.$ticket->product_id }}</div>
                </div>
                @endif

                <div class="col-6 col-lg-3">
                    <div class="detail-label">Subject</div>
                    <div class="detail-value">{{ $ticket->subject }}</div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="detail-label">Priority</div>
                    <div class="detail-value">
                        <span class="ticket-badge ticket-priority-{{ $priority }}">{{ $priorityLabel }}</span>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="detail-label">Status</div>
                    <div class="detail-value">
                        <span class="ticket-badge ticket-status-{{ $status }}">{{ $statusLabel }}</span>
                    </div>
                </div>


                <div class="col-6 col-lg-3">
                    <div class="detail-label">Created</div>
                    <div class="detail-value">{{ optional($ticket->created_at)->format('d-m-Y') ?? 'N/A' }}</div>
                </div>

                @if($ticket->resolved_at)
                <div class="col-6 col-lg-3">
                    <div class="detail-label">Resolved At</div>
                    <div class="detail-value">{{ $ticket->resolved_at->format('d-m-Y h:i A') }}</div>
                </div>
                @endif

                @if($ticket->closed_at)
                <div class="col-6 col-lg-3">
                    <div class="detail-label">Closed At</div>
                    <div class="detail-value">{{ $ticket->closed_at->format('d-m-Y h:i A') }}</div>
                </div>
                @endif

            </div>

            {{-- ── Section 2: Description ── --}}
            <!-- <div class="section-title">Description</div>
            <div class="mb-4">
                <div class="desc-box">{{ $ticket->description ?: 'No description provided.' }}</div>
            </div> -->

            {{-- ── Section 3: Remarks ── --}}
            @if($ticket->remarks)
            <div class="section-title">Remarks</div>
            <div class="mb-4">
                <div class="desc-box">{{ $ticket->remarks }}</div>
            </div>
            @endif

            {{-- ── Section 4: Attachment ── --}}
            @if($ticket->attachment)
            <div class="section-title">Attachment</div>
            <div class="mb-4">
                <a href="{{ asset('storage/' . $ticket->attachment) }}" target="_blank" class="attachment-link">
                    <i class="fas fa-paperclip"></i>
                    {{ basename($ticket->attachment) }}
                    <i class="fas fa-external-link-alt" style="font-size:11px;"></i>
                </a>
            </div>
            @endif

            {{-- ── Action Buttons ── --}}
            {{-- <div class="action-row">
                @if (app('hasPermission')(33, 'edit'))
                    <a href="{{ route('ticket.edit', $ticket->id) }}" class="btn btn-warning text-white">
                        <i class="fas fa-edit me-1"></i> Edit Ticket
                    </a>
                @endif
                <a href="/ticket-history/{{ $ticket->customer_id }}" class="btn btn-secondary">
                    <i class="fas fa-history me-1"></i> Customer History
                </a>
                <a href="{{ route('ticket.list') }}" class="btn btn-cancel">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
            </div> --}}

        </div>
    </div>
</div>
@endsection

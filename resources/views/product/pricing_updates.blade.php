@extends('layout.app')

@section('title', 'Pricing Updates')

@section('content')
<div class="content">
<div class="page-header">
    <div class="page-title">
        <h4>Pricing Updates from Other Branches</h4>
        <h6>Confirm or reject price changes initiated by other branches for matching products</h6>
    </div>
     <div class="back-button">
                <a href="{{ route('product.list') }}" class="btn back-button btn-primary"> <i class="fa-solid fa-arrow-left"></i>
                    Back</a></br>
                <span class="success_submit text-danger"></span>
            </div>
</div>

<div class="card">
    <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-solid nav-justified" id="pricingUpdateTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-updates" type="button" role="tab"
                    aria-controls="pending-updates" aria-selected="true">Pending Confirmations</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#update-history" type="button" role="tab"
                    aria-controls="update-history" aria-selected="false">Update History</a>
            </li>
        </ul>

        <div class="tab-content mt-4" id="pricingUpdateTabsContent">
            <!-- PENDING TAB -->
            <div class="tab-pane fade show active" id="pending-updates" role="tabpanel" aria-labelledby="pending-tab">
                <div class="table-responsive">
                    <table class="table table-striped table-hover border" id="pending-table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>SKU</th>
                                <th>Barcode</th>
                                <th>Current <br><small>(Purchase/MRP/Selling)</small></th>
                                <th>Proposed <br><small>(Purchase/MRP/Selling)</small></th>
                                <th>Requested On</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($pending->count() == 0)
                                <tr>
                                    <td colspan="7" class="text-center">No pending price updates found.</td>
                                </tr>
                            @else
                                @foreach($pending as $item)
                                    <tr>
                                        <td>{{ $item->targetProduct ? $item->targetProduct->name : 'Unknown Product' }}</td>
                                        <td>{{ $item->targetProduct ? $item->targetProduct->SKU : '' }}</td>
                                        <td>{{ $item->targetProduct ? $item->targetProduct->barcode : '' }}</td>
                                        <td>{{ $item->old_purchase_price !== null ? '₹' . number_format((float)$item->old_purchase_price, 2) : 'N/A' }} / {{ $item->old_mrp !== null ? '₹' . number_format((float)$item->old_mrp, 2) : 'N/A' }} / ₹{{ number_format((float)$item->old_price, 2) }}</td>
                                        <td class="text-primary fw-bold">{{ $item->new_purchase_price !== null ? '₹' . number_format((float)$item->new_purchase_price, 2) : 'N/A' }} / {{ $item->new_mrp !== null ? '₹' . number_format((float)$item->new_mrp, 2) : 'N/A' }} / ₹{{ number_format((float)$item->new_price, 2) }}</td>
                                        <td>{{ $item->created_at->format('d/m/Y h:i A') }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-success me-2" onclick="confirmUpdate({{ $item->id }})">Confirm</button>
                                            <button class="btn btn-sm btn-danger" onclick="rejectUpdate({{ $item->id }})">Reject</button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- HISTORY TAB -->
            <div class="tab-pane fade" id="update-history" role="tabpanel" aria-labelledby="history-tab">
                <div class="table-responsive">
                    <table class="table table-striped table-hover border" id="history-table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>SKU</th>
                                <th>Branch</th>
                                <th>Old <br><small>(Purchase/MRP/Selling)</small></th>
                                <th>New <br><small>(Purchase/MRP/Selling)</small></th>
                                <th>Status</th>
                                <th>Resolved By</th>
                                <th>Resolved On</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($history->count() == 0)
                                <tr>
                                    <td colspan="7" class="text-center">No history found.</td>
                                </tr>
                            @else
                                @foreach($history as $item)
                                    <tr>
                                        <td>{{ $item->targetProduct ? $item->targetProduct->name : 'Unknown Product' }}</td>
                                        <td>{{ $item->targetProduct ? $item->targetProduct->SKU : '' }}</td>
                                        <td>{{ $item->branch ? $item->branch->name : 'Main Branch' }}</td>
                                        <td>{{ $item->old_purchase_price !== null ? '₹' . number_format((float)$item->old_purchase_price, 2) : 'N/A' }} / {{ $item->old_mrp !== null ? '₹' . number_format((float)$item->old_mrp, 2) : 'N/A' }} / ₹{{ number_format((float)$item->old_price, 2) }}</td>
                                        <td>{{ $item->new_purchase_price !== null ? '₹' . number_format((float)$item->new_purchase_price, 2) : 'N/A' }} / {{ $item->new_mrp !== null ? '₹' . number_format((float)$item->new_mrp, 2) : 'N/A' }} / ₹{{ number_format((float)$item->new_price, 2) }}</td>
                                        <td>
                                            @if($item->status == 'accepted')
                                                <span class="badge bg-success px-2 py-1">Accepted</span>
                                            @else
                                                <span class="badge bg-danger px-2 py-1">Rejected</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->resolver ? $item->resolver->name : 'System' }}</td>
                                        <td>{{ $item->updated_at->format('d/m/Y h:i A') }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
</div>
</div>
</div>

@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmUpdate(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to approve this price change?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, approve it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/pricing-updates/${id}/confirm`,
                    type: "POST",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire('Approved!', 'Price update confirmed successfully.', 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message || "Error confirming update.", 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', "An error occurred while confirming.", 'error');
                    }
                });
            }
        });
    }

    function rejectUpdate(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to reject this price change?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, reject it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/pricing-updates/${id}/reject`,
                    type: "POST",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire('Rejected!', 'Price update has been rejected.', 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message || "Error rejecting update.", 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', "An error occurred while rejecting.", 'error');
                    }
                });
            }
        });
    }
</script>
@endpush

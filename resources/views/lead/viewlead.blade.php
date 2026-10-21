@extends('layout.app')

@section('title', 'View Lead')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    .profile-card {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        min-height: 250px;
    }

    .profile-photo {
        width: 150px;
        height: 150px;
        border: 1px solid #dee2e6;
        border-radius: 50%;
        object-fit: cover;
        background: #f8fafc;
    }

    .profile-fallback {
        width: 150px;
        height: 150px;
        border: 1px solid #dee2e6;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        color: #1b2850;
        font-size: 56px;
        font-weight: 700;
    }

    .profile-value {
        color: #111827;
        word-break: break-word;
    }
</style>

<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Lead Details</h4>
        </div>
        <div class="page-btn d-flex gap-2">
            @if (app('hasPermission')(32, 'edit'))
                <a href="javascript:void(0);" id="editLeadBtn" class="btn btn-added">
                    <i class="bi bi-pencil-square me-1"></i> Edit
                </a>
            @endif
            @if (app('hasPermission')(32, 'view'))
                <a href="{{ route('lead.list') }}" class="btn" style="background: #1b2850; color: #fff;">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            @endif
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="row">
                <div class="col-xl-4 col-sm-4">
                    <div class="card profile-card">
                        <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                            <div id="leadImageHolder">
                                <div class="profile-fallback">L</div>
                            </div>
                            <h5 class="mt-3"><span class="profile-name" id="leadCardName">Loading...</span></h5>
                            <div class="mt-2">
                                <span class="lead-badge" id="leadStatusBadge">-</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-8 col-sm-8">
                    <div class="card">
                        <div class="card-body pt-3">
                            <div class="tab-content pt-2">
                                <div class="tab-pane fade show active profile-overview" id="profile-overview">
                                    <h5 class="card-title">Profile Details</h5>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 label">Name</div>
                                        <div class="col-lg-6 col-md-6 profile-value" id="leadName">-</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 label">Phone</div>
                                        <div class="col-lg-6 col-md-6 profile-value" id="leadPhone">-</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 label">Lead Source</div>
                                        <div class="col-lg-6 col-md-6 profile-value" id="leadSource">-</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 label">Assigned To</div>
                                        <div class="col-lg-6 col-md-6 profile-value" id="leadAssigned">-</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 label">Company</div>
                                        <div class="col-lg-6 col-md-6 profile-value" id="leadCompany">-</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 label">Email</div>
                                        <div class="col-lg-6 col-md-6 profile-value" id="leadEmail">-</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 label">WhatsApp</div>
                                        <div class="col-lg-6 col-md-6 profile-value" id="leadWhatsApp">-</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 label">SIC Code</div>
                                        <div class="col-lg-6 col-md-6 profile-value" id="leadSicCode">-</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 label">Address</div>
                                        <div class="col-lg-6 col-md-6 profile-value" id="leadAddress">-</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 label">Comment</div>
                                        <div class="col-lg-6 col-md-6 profile-value" id="leadComment">-</div>
                                    </div>
                                    <hr>
                                    {{-- <div class="row">
                                        <div class="col-lg-6 col-md-6 label">Barcode</div>
                                        <div class="col-lg-6 col-md-6">
                                            <div id="leadBarcodeHtml"></div>
                                            <div class="text-muted small mt-2" id="leadBarcodeText"></div>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        const authToken = localStorage.getItem('authToken');
        const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
        const leadId = "{{ $id }}";

        $.ajax({
            url: `/api/lead/${leadId}/show${selectedSubAdminId ? '?selectedSubAdminId=' + selectedSubAdminId : ''}`,
            method: 'GET',
            headers: { 'Authorization': 'Bearer ' + authToken },
            success: function(resp) {
                if (!resp.status) return;

                const lead = resp.data || {};
                const name = lead.name || 'Lead Details';
                const initials = name
                    .split(' ')
                    .filter(Boolean)
                    .map(part => part.charAt(0))
                    .join('')
                    .substring(0, 2)
                    .toUpperCase() || 'L';

                $('#leadName').text(name);
                $('#leadCardName').text(name);
                $('#leadStatusBadge')
                    .text(lead.lead_status || 'N/A')
                    .removeClass()
                    .addClass('lead-badge status-' + String(lead.lead_status || '').toLowerCase().replace(/\s+/g, '-'));

                $('#leadPhone').text(lead.phone || '-');
                $('#leadSource').text(lead.lead_source || '-');
                $('#leadAssigned').text(lead.assigned_user?.name || lead.assignedUser?.name || '-');
                $('#leadCompany').text(lead.company_name || '-');
                $('#leadEmail').text(lead.email || '-');
                $('#leadWhatsApp').text(lead.whatsapp || '-');
                $('#leadSicCode').text(lead.sic_code || '-');
                $('#leadAddress').text(lead.address || '-');
                $('#leadComment').text(lead.comment || '-');

                const imagePath = "{{ env('ImagePath') }}";
                if (lead.image) {
                    const imgSrc = lead.image.startsWith('http')
                        ? lead.image
                        : `${imagePath}/storage/${lead.image}`;

                    $('#leadImageHolder').html(`
                        <img src="${imgSrc}" class="profile-photo"
                             onerror="this.onerror=null;this.parentElement.innerHTML='<div class=\\'profile-fallback\\'>${initials}</div>';">
                    `);
                } else {
                    $('#leadImageHolder').html(`<div class="profile-fallback">${initials}</div>`);
                }

                if (lead.barcode_html) {
                    $('#leadBarcodeHtml').html(lead.barcode_html);
                    $('#leadBarcodeText').text(lead.barcode || 'N/A');
                }

                $('#editLeadBtn').attr('href', `/edit-lead/${leadId}`);
            },
            error: function() {
                $('#lead-details').html('<div class="col-12 text-center text-danger">Unable to load lead details.</div>');
            }
        });
    });
</script>
@endpush

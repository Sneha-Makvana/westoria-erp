@extends('layout.app')

@section('title', 'View Follow Up')

@section('content')
    @php
        $canViewFollowUp = app('hasPermission')(30, 'view');
        $canEditFollowUp = app('hasPermission')(30, 'edit');
    @endphp
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        .profile-card {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            min-height: 250px;
        }

        .profile-icon {
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
                <h4>Follow Up Details</h4>
            </div>
            <div class="page-btn d-flex gap-2">
                @if ($canEditFollowUp)
                    <a href="#" id="editFollowUpBtn" class="btn btn-added">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                    </a>
                @endif
                @if ($canViewFollowUp)
                    <a href="{{ route('followup.list') }}" class="btn" style="background: #1b2850; color: #fff;">
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
                                <div class="profile-icon"><i class="bi bi-clipboard-check"></i></div>
                                <h5 class="mt-3"><span class="profile-name" id="followUpTitle">Loading...</span></h5>
                                <div class="mt-2">
                                    <span class="status-badge" id="followUpStatusBadge">-</span>
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
                                            <div class="col-lg-6 col-md-6 label">Subject</div>
                                            <div class="col-lg-6 col-md-6 profile-value" id="followUpSubject">-</div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 label">Purpose</div>
                                            <div class="col-lg-6 col-md-6 profile-value" id="followUpPurpose">-</div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 label">Priority</div>
                                            <div class="col-lg-6 col-md-6 profile-value" id="followUpPrioritySummary">-</div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 label">Status</div>
                                            <div class="col-lg-6 col-md-6 profile-value" id="followUpStatusText">-</div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 label">Follow Up Date</div>
                                            <div class="col-lg-6 col-md-6 profile-value" id="followUpDateSummary">-</div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 label">Assigned To</div>
                                            <div class="col-lg-6 col-md-6 profile-value" id="followUpAssignedSummary">-</div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 label">Comment</div>
                                            <div class="col-lg-6 col-md-6 profile-value" id="followUpComment">-</div>
                                        </div>
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
        const canEditFollowUp = @json((bool) $canEditFollowUp);

        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
            const normalizedSelectedSubAdminId = (selectedSubAdminId && selectedSubAdminId !== 'null' && selectedSubAdminId !== 'undefined') ? selectedSubAdminId : '';
            const followUpId = "{{ $id }}";

            function loadFollowUpDetails() {
                let url = `/follow-up/${followUpId}/show`;
                if (normalizedSelectedSubAdminId) {
                    url += `?selectedSubAdminId=${encodeURIComponent(normalizedSelectedSubAdminId)}`;
                }

                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        if (response.status) {
                            displayFollowUpDetails(response.data);
                        } else {
                            showError(response.message || "Follow up not found.");
                        }
                    },
                    error: function() {
                        showError("Failed to load follow up details.");
                    }
                });
            }

            function formatFollowUpDate(followUp) {
                if (followUp.formatted_follow_up_datetime) {
                    return followUp.formatted_follow_up_datetime;
                }
                if (!followUp.follow_up_datetime) return 'N/A';

                const rawDate = String(followUp.follow_up_datetime).replace(' ', 'T');
                const parsedDate = new Date(rawDate);
                if (isNaN(parsedDate.getTime())) return followUp.follow_up_datetime;

                const day    = String(parsedDate.getDate()).padStart(2, '0');
                const month  = String(parsedDate.getMonth() + 1).padStart(2, '0');
                const year   = parsedDate.getFullYear();
                const hours24 = parsedDate.getHours();
                const hours12 = String(hours24 % 12 || 12).padStart(2, '0');
                const minutes = String(parsedDate.getMinutes()).padStart(2, '0');
                const amPm   = hours24 >= 12 ? 'PM' : 'AM';

                return `${day}-${month}-${year} ${hours12}:${minutes} ${amPm}`;
            }

            function displayFollowUpDetails(followUp) {
                // Set edit button href once we have the ID
                if (canEditFollowUp) {
                    $('#editFollowUpBtn').attr('href', `/edit-follow-up/${followUp.id}`);
                }

                const priorityBadge = `<span class="priority-badge priority-${(followUp.priority || '').toLowerCase()}">${followUp.priority || 'N/A'}</span>`;
                const statusBadge   = `<span class="status-badge status-${(followUp.status || '').toLowerCase()}">${followUp.status || 'N/A'}</span>`;

                const assignedHtml = followUp.assigned_user
                    ? `<strong>${followUp.assigned_user.name}</strong>
                       <br><small class="text-muted">${followUp.assigned_user.email || ''}${followUp.assigned_user.phone ? ' | ' + followUp.assigned_user.phone : ''}</small>`
                    : 'Not Assigned';

                $('#followUpTitle').text(followUp.subject_name || 'Follow Up Details');
                $('#followUpSubject').text(followUp.subject_name || '-');
                $('#followUpPurpose').text(followUp.purpose || '-');
                $('#followUpStatusBadge').replaceWith(`<span class="status-badge status-${(followUp.status || '').toLowerCase()}" id="followUpStatusBadge">${followUp.status || 'N/A'}</span>`);
                $('#followUpStatusText').text(followUp.status || '-');
                $('#followUpPrioritySummary').text(followUp.priority || 'N/A');
                $('#followUpDateSummary').text(formatFollowUpDate(followUp));
                $('#followUpAssignedSummary').text(followUp.assigned_user ? followUp.assigned_user.name : 'Not Assigned');
                $('#followUpComment').text(followUp.comment || 'No comments');
            }

            function showError(message) {
                $('#followUpDetails').html(`
                    <div class="alert alert-danger text-center">
                        <i class="bi bi-exclamation-triangle me-2"></i>${message}
                    </div>
                `);
            }

            loadFollowUpDetails();
        });
    </script>
@endpush

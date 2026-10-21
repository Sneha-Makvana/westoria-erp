@extends('layout.app')

@section('title', 'View Meeting')

@section('content')
    @php
        $canEditMeeting = app('hasPermission')(31, 'edit');
        $canDeleteMeeting = app('hasPermission')(31, 'delete');
        $canManageMeeting = $canEditMeeting || $canDeleteMeeting;
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
                <h4>Meeting Details</h4>
            </div>
            <div class="page-btn d-flex gap-2">
                @if ($canEditMeeting)
                    <a href="{{ route('meeting.edit', ['id' => $id]) }}" class="btn btn-added">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                    </a>
                @endif
                {{-- @if ($canDeleteMeeting)
                    <button class="btn btn-danger delete-meeting" data-id="{{ $id }}">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                @endif --}}
                @if (app('hasPermission')(31, 'view'))
                    <a href="{{ route('meeting.list') }}" class="btn" style="background: #1b2850; color: #fff;">
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
                                <div class="profile-icon"><i class="bi bi-calendar-event"></i></div>
                                <h5 class="mt-3"><span class="profile-name" id="meetingTitle">Loading...</span></h5>
                                <div class="mt-2">
                                    <span class="status-badge" id="meetingStatusBadge">-</span>
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
                                            <div class="col-lg-6 col-md-6 label">Title</div>
                                            <div class="col-lg-6 col-md-6 profile-value" id="meetingTitleDetail">-</div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 label">Type</div>
                                            <div class="col-lg-6 col-md-6 profile-value" id="meetingTypeSummary">-</div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 label">Status</div>
                                            <div class="col-lg-6 col-md-6 profile-value" id="meetingStatusText">-</div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 label">Scheduled On</div>
                                            <div class="col-lg-6 col-md-6 profile-value" id="meetingDateSummary">-</div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 label">Customer</div>
                                            <div class="col-lg-6 col-md-6 profile-value" id="meetingCustomerSummary">-</div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 label">Assigned To</div>
                                            <div class="col-lg-6 col-md-6 profile-value" id="meetingAssignedSummary">-</div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 label">Branch</div>
                                            <div class="col-lg-6 col-md-6 profile-value" id="meetingBranch">-</div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 label">Address</div>
                                            <div class="col-lg-6 col-md-6 profile-value" id="meetingAddress">-</div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 label">Agenda</div>
                                            <div class="col-lg-6 col-md-6 profile-value" id="meetingAgenda">-</div>
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
        const canManageMeeting = @json((bool) $canManageMeeting);
        const canDeleteMeeting = @json((bool) $canDeleteMeeting);

        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
            const normalizedSelectedSubAdminId = (selectedSubAdminId && selectedSubAdminId !== 'null' && selectedSubAdminId !== 'undefined') ? selectedSubAdminId : '';
            const meetingId = "{{ $id }}";

            function loadMeetingDetails() {
                let url = `/meeting/${meetingId}/show`;
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
                            displayMeetingDetails(response.data);
                        } else {
                            showError(response.message || 'Meeting not found.');
                        }
                    },
                    error: function() {
                        showError('Failed to load meeting details. Please try again.');
                    }
                });
            }

            function displayMeetingDetails(meeting) {
                const statusClass = `status-${(meeting.status || '').toLowerCase()}`;
                const statusBadge = `<span class="status-badge ${statusClass}">${meeting.status || 'N/A'}</span>`;

                const customerHtml = meeting.customer
                    ? `<strong>${meeting.customer.name}</strong>`
                    : 'N/A';

                const assignedHtml = meeting.assigned_user
                    ? `<strong>${meeting.assigned_user.name}</strong>`
                    : 'N/A';

                const addressHtml = meeting.address
                    ? `<div class="address-box mt-1">${meeting.address}</div>`
                    : 'N/A';

                const agendaHtml = meeting.agenda
                    ? `<div class="agenda-box mt-1">${meeting.agenda}</div>`
                    : 'N/A';

                const createdAt = meeting.created_at ? new Date(meeting.created_at).toLocaleString() : 'N/A';
                const updatedAt = meeting.updated_at ? new Date(meeting.updated_at).toLocaleString() : 'N/A';

                $('#meetingTitle').text(meeting.meeting_title || 'Meeting Details');
                $('#meetingTitleDetail').text(meeting.meeting_title || '-');
                $('#meetingStatusBadge').replaceWith(`<span class="status-badge ${statusClass}" id="meetingStatusBadge">${meeting.status || 'N/A'}</span>`);
                $('#meetingStatusText').text(meeting.status || '-');
                $('#meetingTypeSummary').text(meeting.meeting_type || 'N/A');
                $('#meetingDateSummary').text(meeting.formatted_scheduled_on || 'N/A');
                $('#meetingCustomerSummary').text(meeting.customer ? meeting.customer.name : 'N/A');
                $('#meetingAssignedSummary').text(meeting.assigned_user ? meeting.assigned_user.name : 'N/A');
                $('#meetingBranch').text(meeting.branch ? meeting.branch.name : 'N/A');
                $('#meetingAddress').html(addressHtml);
                $('#meetingAgenda').html(agendaHtml);
            }

            function showError(message) {
                $('#meetingDetails').html(`
                    <div class="alert alert-danger text-center">
                        <i class="bi bi-exclamation-triangle me-2"></i>${message}
                    </div>
                `);
            }

            // Delete meeting
            // $(document).on('click', '.delete-meeting', function() {
            //     var id = $(this).data('id');
            //     Swal.fire({
            //         title: "Are you sure?",
            //         text: "You won't be able to revert this!",
            //         icon: "warning",
            //         showCancelButton: true,
            //         confirmButtonColor: "#ff9f43",
            //         cancelButtonColor: "#6c757d",
            //         confirmButtonText: "Yes, delete it!"
            //     }).then((result) => {
            //         if (result.isConfirmed) {
            //             $.ajax({
            //                 url: `/meeting/${id}/delete`,
            //                 type: "DELETE",
            //                 data: normalizedSelectedSubAdminId ? { selectedSubAdminId: normalizedSelectedSubAdminId } : {},
            //                 headers: {
            //                     "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            //                     "Authorization": "Bearer " + authToken,
            //                 },
            //                 success: function(response) {
            //                     if (response.status) {
            //                         Swal.fire({
            //                             title: "Deleted!",
            //                             text: "Meeting has been deleted.",
            //                             icon: "success",
            //                             confirmButtonColor: "#ff9f43"
            //                         }).then(() => {
            //                             window.location.href = "{{ route('meeting.list') }}";
            //                         });
            //                     } else {
            //                         Swal.fire({
            //                             title: "Error!",
            //                             text: response.message || "Failed to delete meeting.",
            //                             icon: "error",
            //                             confirmButtonColor: "#ff9f43"
            //                         });
            //                     }
            //                 },
            //                 error: function() {
            //                     Swal.fire({
            //                         title: "Error!",
            //                         text: "Failed to delete meeting. Please try again.",
            //                         icon: "error",
            //                         confirmButtonColor: "#ff9f43"
            //                     });
            //                 }
            //             });
            //         }
            //     });
            // });

            loadMeetingDetails();
        });
    </script>
@endpush

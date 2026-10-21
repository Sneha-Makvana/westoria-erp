@extends('layout.app')

@section('title', 'Financer Details')

@section('content')
<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Financer View</h4>
        </div>

        <div class="page-btn d-flex gap-2">
            @if (app('hasPermission')(10, 'edit'))
            <a href="{{ route('financer.edit', $id) }}" class="btn btn-added">
                <i class="fa fa-edit me-1"></i> Edit
            </a>
            @endif
            <a href="{{ route('financer.list') }}" class="btn" style="background: #1b2850; color: #fff;">
                <i class="fa fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="">
        <div class="card-body">
            <div class="row">
                <div class="col-xl-4 col-sm-4">
                    <div class="card">
                        <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                            <img src="" alt="Profile" class="img-fluid rounded-circle border"
                                style="width: 150px; height: 150px; object-fit: cover;">
                            <h5 class="mt-3"><span class="profile-name"></span></h5>
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
                                        <div class="col-lg-3 col-md-4 label">Name</div>
                                        <div class="col-lg-9 col-md-8"><span class="profile-name"></span></div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Email</div>
                                        <div class="col-lg-9 col-md-8"><span class="profile-email"></span></div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Phone</div>
                                        <div class="col-lg-9 col-md-8"><span class="profile-phone"></span></div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">PAN Number</div>
                                        <div class="col-lg-9 col-md-8"><span class="profile-pan_number"></span></div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">GST Number</div>
                                        <div class="col-lg-9 col-md-8"><span class="profile-gst_number"></span></div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Address</div>
                                        <div class="col-lg-9 col-md-8"><span class="profile-address"></span></div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">City</div>
                                        <div class="col-lg-9 col-md-8"><span class="profile-city"></span></div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">State</div>
                                        <div class="col-lg-9 col-md-8"><span class="profile-state_name"></span></div>
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
        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            let financerId = '{{ $id }}';

            if (!financerId) {
                $('.profile-name').text('N/A');
                return;
            }

            $.ajax({
                url: `/api/financer/${financerId}`,
                method: 'GET',
                headers: {
                    "Authorization": "Bearer " + authToken,
                },
                success: function(res) {
                    const f = res.data || {};
                    $('.profile-name').text(f.name ?? 'N/A');
                    $('.profile-email').text(f.email ?? 'N/A');
                    $('.profile-phone').text(f.phone ?? 'N/A');
                    $('.profile-pan_number').text(f.pan_number && f.pan_number.trim() !== '' ? f.pan_number : 'N/A');
                    $('.profile-gst_number').text(f.gst_number && f.gst_number.trim() !== '' ? f.gst_number : 'N/A');
                    $('.profile-address').text(f.address && f.address.trim() !== '' ? f.address : 'N/A');
                    $('.profile-city').text(f.city && f.city.trim() !== '' ? f.city : 'N/A');
                    $('.profile-state_name').text(f.state_name ?? 'N/A');

                    let imageBasePath = '{{ env('ImagePath') }}';
                    if (f.profile_image_url) {
                        $('img[alt="Profile"]').attr('src', f.profile_image_url);
                    } else if (f.profile_image) {
                        $('img[alt="Profile"]').attr('src', `${imageBasePath}/storage/${f.profile_image}`);
                    } else {
                        $('img[alt="Profile"]').attr('src', `${imageBasePath}/admin/assets/img/customer/customer5.jpg`);
                    }
                },
                error: function(err) {
                    $('#financer-details').html('<p class="text-danger">Financer not found or access denied.</p>');
                }
            });
        });
    </script>
@endpush

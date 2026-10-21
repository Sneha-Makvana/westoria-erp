@extends('layout.app')

@section('title', 'Profile')

@section('content')
    <style>
        .field-error {
            font-size: 13px;
            margin-top: 6px;
            line-height: 1.3;
        }

        .field-error:empty {
            display: none !important;
        }

        #validationErrors {
            margin-bottom: 16px;
        }

        .staff-face-preview {
            border-radius: 8px;
            display: block;
            max-height: 90px;
            object-fit: cover;
            width: auto;
        }

        @media screen and (max-width: 768px) {
            .form-group {
                margin-bottom: 15px !important
            }

            .profile-set {
                margin-bottom: 0 !important;
            }
        }
    </style>
    @php
        $isStaffProfile = ($user->role ?? '') === 'staff';
        $staffDetails = $user->userDetail ?? $user->details ?? null;
        $staffJoiningDate = !empty($staffDetails?->joining_date)
            ? \Carbon\Carbon::parse($staffDetails->joining_date)->format('Y-m-d')
            : '';
        $activePlan = $user->getActivePlan();
        $planExpiryStatus = $user->checkPlanExpiryStatus();
    @endphp
    <div class="content">
        <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="page-title">    
                <h4>Profile</h4>
                <h6>User Profile</h6>
            </div>
            <!-- <div class="d-flex align-items-center gap-3">
                @if ($activePlan && in_array($user->role, ['admin', 'sub-admin']))
                    <a href="{{ route('plans.myplan') }}" class="text-decoration-none" title="View Plan Details">
                        <div class="d-flex align-items-center border rounded px-2 py-1 bg-white" style="border-color: #e0e0e0 !important; min-width: max-content; height: 38px; transition: all 0.2s ease;">
                            <i class="fas fa-crown me-2" style="color: #ff9f43; font-size: 16px;"></i>
                            <div class="d-flex flex-column me-2 text-start" style="line-height: 1.2;">
                                <span style="font-size: 12px; font-weight: 600; color: #333;">{{ $activePlan->name }}</span>
                                <span style="font-size: 10px; color: #888;">Expires: {{ $activePlan->end_date ? \Carbon\Carbon::parse($activePlan->end_date)->format('d-m-Y') : 'N/A' }}</span>
                            </div>
                            @if ($planExpiryStatus['status'] === 'expired')
                                <span class="badge" style="background-color: #dc3545; color: white; font-size: 9px; padding: 4px 6px; font-weight: 500; border-radius: 4px;">Expired</span>
                            @elseif ($planExpiryStatus['status'] === 'warning')
                                <span class="badge" style="background-color: #ff9f43; color: white; font-size: 9px; padding: 4px 6px; font-weight: 500; border-radius: 4px;">Expiring Soon</span>
                            @else
                                <span class="badge" style="background-color: #198754; color: white; font-size: 9px; padding: 4px 6px; font-weight: 500; border-radius: 4px;">Active Plan</span>
                            @endif
                        </div>
                    </a>
                @endif
                
            </div> -->
        <a href="{{ route('auth.dashboard') }}" class="btn" style="background: #1b2850; color: #fff;">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
        </div>
        <form id="updateProfileForm" enctype="multipart/form-data" autocomplete="off">
            @csrf
            {{-- <div id="validationErrors" class="alert alert-danger d-none" role="alert">
                <ul id="validationErrorList" class="mb-0"></ul>
            </div> --}}
            <div class="card">
                <div class="card-body">
                    <div class="profile-set">
                        <div class="profile-head"></div>
                        <div class="profile-top">
                            <div class="profile-content">
                                <div class="profile-contentimg">
                                    @if (isset($user->profile_image))
                                        <img src="{{ env('ImagePath') . 'storage/' . $user->profile_image }}" alt="img"
                                            id="blah">
                                    @else
                                        <img src="{{ env('ImagePath') . 'admin/assets/img/customer/customer5.jpg' }}"
                                            alt="img" id="blah">
                                    @endif

                                    <div class="profileupload">
                                        <input type="file" name="profile_image" id="imgInp" accept=".jpg,.jpeg,.png,.gif,.webp,.bmp,image/*">
                                        <a href="javascript:void(0);">
                                            <img src="{{ env('ImagePath') . 'admin/assets/img/icons/edit-set.svg' }}"
                                                alt="img">
                                        </a>
                                    </div>
                                    <div class="invalid-feedback d-block field-error" data-field="profile_image"></div>
                                </div>
                                <div class="profile-contentname">
                                    <h2>{{ $user->name }}</h2>
                                    <h4>Update Your Photo and Personal Details.</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" placeholder="William" value="{{ $user->name }}">
                                <div class="invalid-feedback d-block field-error" data-field="name"></div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="text" name="email" placeholder="william@example.com"
                                    value="{{ $user->email }}">
                                <div class="invalid-feedback d-block field-error" data-field="email"></div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" name="phone" placeholder="+1452 876 5432"
                                    value="{{ $user->phone }}">
                                <div class="invalid-feedback d-block field-error" data-field="phone"></div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label>Password</label>
                                <div class="pass-group">
                                    <input type="password" name="password" class="pass-input" id="password"
                                        placeholder="********" autocomplete="new-password" value="">
                                    <span class="fas toggle-password fa-eye-slash"></span>
                                </div>
                                <div class="invalid-feedback d-block field-error" data-field="password"></div>
                            </div>
                        </div>
                        @if ($isStaffProfile)
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Salary</label>
                                    <input type="number" name="salary" class="form-control" min="0" step="0.01"
                                        value="{{ old('salary', $staffDetails?->salary) }}">
                                    <div class="invalid-feedback d-block field-error" data-field="salary"></div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Joining Date</label>
                                    <input type="date" name="joining_date" class="form-control"
                                        value="{{ old('joining_date', $staffJoiningDate) }}">
                                    <div class="invalid-feedback d-block field-error" data-field="joining_date"></div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="form-group">
                                    <label>Department</label>
                                    <input type="hidden" name="department_id" value="{{ $staffDetails?->department_id }}">
                                    <select id="profile_department_id" class="form-control" disabled>
                                        <option value="">Select Department</option>
                                        @foreach (($departments ?? collect()) as $department)
                                            <option value="{{ $department->id }}" @selected((string) ($staffDetails?->department_id ?? '') === (string) $department->id)>
                                                {{ $department->department_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback d-block field-error" data-field="department_id"></div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="form-group">
                                    <label>Designation</label>
                                    <input type="hidden" name="designation_id" value="{{ $staffDetails?->designation_id }}">
                                    <select id="profile_designation_id" class="form-control" disabled>
                                        <option value="">Select Designation</option>
                                    </select>
                                    <div class="invalid-feedback d-block field-error" data-field="designation_id"></div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="form-group">
                                    <label>Country</label>
                                    <input type="text" name="country" value="{{ old('country', $staffDetails?->country) }}">
                                    <div class="invalid-feedback d-block field-error" data-field="country"></div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" name="city" value="{{ old('city', $staffDetails?->city) }}">
                                    <div class="invalid-feedback d-block field-error" data-field="city"></div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea name="address" class="form-control" style="height: 40px;" rows="1">{{ old('address', $staffDetails?->address) }}</textarea>
                                    <div class="invalid-feedback d-block field-error" data-field="address"></div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label>Face Photo</label>
                                    <input type="file" name="face_photo" id="profileFacePhoto" class="form-control"
                                        accept=".jpg,.jpeg,.png,.gif,.webp,.bmp,image/*">
                                    <div class="invalid-feedback d-block field-error" data-field="face_photo"></div>
                                    @if (!empty($staffDetails?->face_photo))
                                        <img src="{{ env('ImagePath') . 'storage/' . $staffDetails->face_photo }}"
                                            alt="Face Photo" id="profileFacePreview" class="staff-face-preview mt-2">
                                    @else
                                        <img src="" alt="Face Photo" id="profileFacePreview"
                                            class="staff-face-preview mt-2 d-none">
                                    @endif
                                </div>
                            </div>
                        @endif
                        <div class="col-12">
                            <button type="submit" class="btn btn-submit me-2">Submit</button>
                            <!-- <button type="reset" class="btn btn-cancel">Cancel</button> -->
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            const isStaffProfile = @json($isStaffProfile);
            const profileDesignations = @json(($designations ?? collect())->values());
            const selectedProfileDesignationId = @json((string) ($staffDetails?->designation_id ?? ''));
            const $profileSubmitBtn = $("#updateProfileForm button[type='submit']");
            const profileSubmitBtnDefaultHtml = $profileSubmitBtn.html();

            function toggleProfileSubmitLoading(isLoading) {
                if (isLoading) {
                    $profileSubmitBtn
                        .prop("disabled", true)
                        .html(
                            '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving...'
                        );
                } else {
                    $profileSubmitBtn
                        .prop("disabled", false)
                        .html(profileSubmitBtnDefaultHtml);
                }
            }

            $("#password").val("");

            function renderProfileDesignationOptions(departmentId, selectedId = '') {
                const $designation = $("#profile_designation_id");
                if (!$designation.length) {
                    return;
                }

                $designation.empty().append('<option value="">Select Designation</option>');

                if (!departmentId) {
                    $designation.prop("disabled", true);
                    return;
                }

                profileDesignations
                    .filter(item => String(item.department_id || '') === String(departmentId))
                    .forEach(item => {
                        $designation.append(
                            `<option value="${item.id}" ${String(selectedId) === String(item.id) ? 'selected' : ''}>${item.designation_name}</option>`
                        );
                    });

                $designation.prop("disabled", true);
            }

            if (isStaffProfile) {
                renderProfileDesignationOptions($("#profile_department_id").val(), selectedProfileDesignationId);
            }

            $("#imgInp").on("change", function() {
                var file = this.files && this.files[0] ? this.files[0] : null;
                var $fieldError = $('.field-error[data-field="profile_image"]');

                if (!file) {
                    $fieldError.text("");
                    return;
                }

                if (!file.type || file.type.indexOf("image/") !== 0) {
                    this.value = "";
                    $fieldError.text("Please select a valid image file.");
                    Swal.fire({
                        icon: "error",
                        title: "Invalid file",
                        text: "Only image files are allowed.",
                    });
                    return;
                }

                $fieldError.text("");

                var reader = new FileReader();
                reader.onload = function(event) {
                    $("#blah").attr("src", event.target.result);
                };
                reader.readAsDataURL(file);
            });

            $("#profileFacePhoto").on("change", function() {
                var file = this.files && this.files[0] ? this.files[0] : null;
                var $fieldError = $('.field-error[data-field="face_photo"]');

                if (!file) {
                    $fieldError.text("");
                    return;
                }

                if (!file.type || file.type.indexOf("image/") !== 0) {
                    this.value = "";
                    $fieldError.text("Please select a valid image file.");
                    Swal.fire({
                        icon: "error",
                        title: "Invalid file",
                        text: "Only image files are allowed.",
                    });
                    return;
                }

                $fieldError.text("");

                var reader = new FileReader();
                reader.onload = function(event) {
                    $("#profileFacePreview").attr("src", event.target.result).removeClass("d-none");
                };
                reader.readAsDataURL(file);
            });

            $("#updateProfileForm").submit(function(e) {
                e.preventDefault();

                if ($profileSubmitBtn.prop("disabled")) {
                    return;
                }

                var formData = new FormData(this);
                var $form = $(this);
                var $validationErrors = $("#validationErrors");
                var $validationErrorList = $("#validationErrorList");

                $validationErrors.addClass("d-none");
                $validationErrorList.html("");
                $form.find(".field-error").text("");
                $form.find(".is-invalid").removeClass("is-invalid");

                $.ajax({
                    url: "/api/updateProfile",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        toggleProfileSubmitLoading(true);
                    },
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: "success",
                                title: "Profile Updated!",
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Update Failed",
                                text: "Failed to update profile.",
                            });
                        }
                    },
                    error: function(xhr) {
                        // console.log(xhr.responseJSON);
                        let errorMessage = "Error updating profile!";

                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;

                            Object.keys(errors).forEach(function(field) {
                                var fieldMessages = errors[field];
                                var inputField = $form.find('[name="' + field + '"]');
                                var fieldError = $form.find('.field-error[data-field="' + field + '"]');

                                fieldMessages.forEach(function(message) {
                                    $validationErrorList.append("<li>" + message + "</li>");
                                });

                                if (inputField.length) {
                                    inputField.addClass("is-invalid");
                                }

                                if (fieldError.length) {
                                    fieldError.text(fieldMessages.join(" "));
                                }
                            });

                            $validationErrors.removeClass("d-none");
                            return;
                        }

                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Error updating profile!",
                        });
                    },
                    complete: function() {
                        toggleProfileSubmitLoading(false);
                    }
                });
            });
        });
    </script>
@endpush

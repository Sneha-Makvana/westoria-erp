@extends('layout.app')

@section('title', 'Edit Staff')

@section('content')
    <style>
        @media screen and (max-width: 768px) {
            .form-group {
                margin-bottom: 10px !important
            }
            label.form-check-label {
                font-size: 13px;
            }
        }
        a.btn.back-button {
            background: #ff9f43;
            color: #fff;
        }
        .btn-capture-face {
            background: #1b2850;
            color: #fff;
            width: 100%;
            font-weight: 600;
            padding: 8px;
            border-radius: 5px;
        }

        .btn-capture-face:hover {
            background: #2a3a6a;
            color: #fff;
        }

        .btn-register-face {
            border: 1px solid #ff9f43;
            color: #ff9f43;
            background: #fff;
        }

        .btn-register-face:hover {
            background: #fff7ef;
            color: #e8892f;
        }

        .face-recognition-video-shell {
            border: 2px solid #ffedd5;
            border-radius: 18px;
            overflow: hidden;
            background: linear-gradient(135deg, #111827, #374151);
        }
        .face-recognition-video {
            width: 100%;
            min-height: 340px;
            object-fit: cover;
            display: block;
            background: #111827;
        }
    </style>
    <div class="content">
        {{-- <div class="page-header">
            <div class="page-title">
                <h4>Edit Staff</h4>

            </div>
        </div> --}}
         <div class="page-header ">
            <div class="page-title">
                <h4>Edit Staff</h4>
            </div>
             <div class="back-button">
                <a href="{{ route('staff.list') }}" class="btn back-button"> <i class="fa-solid fa-arrow-left"></i> Back</a></br>
                            <span class="success_submit text-danger"></span>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <input type="hidden" name="face_descriptor" id="face_descriptor">
                <input type="hidden" name="role" id="role" value="staff">
                <div class="row">
                    <!-- Customer Name -->
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            {{-- <label>Staff Name</label> --}}
                            <label>Staff Name <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" maxlength="80" id="customer_name"
                                class="form-control">
                            <div class="text-danger error-customer_name"></div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control" autocomplete="off">
                            <div class="text-danger error-email"></div>
                        </div>
                    </div>


                    <!-- Phone -->
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Phone <span class="text-danger">*</span></label>
                            <input type="text" name="phone" id="phone" maxlength="10" pattern="\d{10}"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,10)" class="form-control">
                            <div class="text-danger error-phone"></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Salary <span class="text-danger">*</span></label>
                            <input type="number" id="salary" name="salary" min="0" step="0.01" class="form-control" placeholder="Enter salary amount">
                            <div class="text-danger error-salary"></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Joining Date <span class="text-danger">*</span></label>
                            <input type="date" id="joining_date" name="joining_date" class="form-control">
                            <div class="text-danger error-joining_date"></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Department</label>
                            <select name="department_id" id="department_id" class="form-control">
                                <option value="">Select Department</option>
                            </select>
                            <div class="text-danger error-department_id"></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Designation</label>
                            <select name="designation_id" id="designation_id" class="form-control" disabled>
                                <option value="">Select Designation</option>
                            </select>
                            <div class="text-danger error-designation_id"></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Country</label>
                            <input type="text" id="country" name="country" class="form-control">
                            <div class="text-danger error-country"></div>

                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" id="city" name="city" class="form-control">
                            <div class="text-danger error-city"></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Password</label>
                            <div class="pass-group">
                                <input type="password" id="new_password" name="password" class="pass-input form-control" placeholder="Leave blank to keep unchanged">
                                <span class="fas toggle-password fa-eye-slash"></span>
                            </div>
                            <div class="text-danger error-password"></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6" id="branch-select-div">
                        <div class="form-group">
                            <label>Branch</label>
                            <select name="sub_admin_id" id="sub_admin_id" class="form-control">
                                <option value="">Main Branch</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <label>Address</label>
                            <textarea id="address" name="address" class="form-control"></textarea>
                            <div class="text-danger error-address"></div>

                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>Photo</label>
                            <div class="image-upload">
                                <input type="file" id="avatar-input" name="avatar" accept="image/*">
                                <div class="image-uploads">
                                    <img src="{{ env('ImagePath') . '/admin/assets/img/icons/upload.svg' }}"
                                        alt="Upload Icon">
                                    <h4>Drag and drop a file to upload</h4>
                                </div>
                            </div>
                            <div class="text-danger error-avatar"></div>

                            <!-- Profile Image Preview -->
                            <div id="avatar-preview-container" style="display: none; margin-top: 10px;">
                                <img id="avatar-preview" src="" alt="Profile Image"
                                    style="max-width: 100px; border-radius: 8px;">
                            </div>
                        </div>
                    </div>

                    <!-- Face Photo Upload/Capture -->
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>Face Photo <span class="text-muted small">(upload or use camera)</span></label>
                            <div class="image-upload" id="face-photo-upload-box" style="margin: 0; position: relative; overflow: hidden; display: flex; justify-content: center; align-items: center; min-height: 120px;">
                                <input type="file" name="face_photo" id="face_photo_input" class="form-control" accept="image/*">
                                <div class="image-uploads" id="face-upload-content">
                                    <img src="{{ env('ImagePath') . '/admin/assets/img/icons/upload.svg' }}" alt="Upload Icon">
                                    <h4 id="face-photo-text">Drag and drop a file to upload</h4>
                                </div>
                                <img id="face-preview" src="" alt="Face Preview" style="display: none; max-width: 100%; max-height: 120px; border-radius: 8px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); pointer-events: none;">
                            </div>
                            <div class="text-danger error-face_photo"></div>
                            
                            <button type="button" class="btn btn-capture-face mt-2" id="captureFaceBtn">
                                <i class="fa-solid fa-camera me-1"></i>Capture Face via Camera
                            </button>
                            
                            <input type="hidden" name="captured_photo" id="captured_photo">
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <button type="submit" class="btn btn-submit me-2" id="updateCustomer">Update</button>
                        <a class="btn btn-cancel" href="{{ route('staff.list') }}">Cancel</a>
                    </div>
                </div>
                <hr>
                <div class="text-danger error-permissions mt-2"></div>
                {{--
                <h5 class="mt-4 fw-bold d-flex align-items-center">
                    PERMISSION :
                    <div class="form-check form-check-inline ms-3 mb-0">
                        <input class="form-check-input" type="radio" name="permission_type" id="withPermission"
                            value="1" required>
                        <label class="form-check-label" for="withPermission">With Permission</label>
                    </div>

                    <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input" type="radio" name="permission_type" id="withoutPermission"
                            value="0">
                        <label class="form-check-label" for="withoutPermission">Without Permission</label>
                    </div>
                </h5> --}}

                <div class="d-flex align-items-center mt-4">
                    <h5 class="fw-bold mb-0 me-3">PERMISSION:</h5>

                    <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input" type="radio" name="permission_type" id="withPermission"
                            value="1">
                        <label class="form-check-label" for="withPermission">With Permission</label>
                    </div>

                    <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input" type="radio" name="permission_type" id="withoutPermission"
                            value="0">
                        <label class="form-check-label" for="withoutPermission">Without Permission</label>
                    </div>
                </div>

                <div id="permissionsSection" style="display:none; margin-top:15px;">
                    <div class="form-group mb-3">
                        <label class="fw-normal">
                            <input type="checkbox" id="select_all_module"> Select All Module
                        </label>
                    </div>

                    <div class="table-responsive">
                        <p class="text-muted small mb-2">For <strong>Report</strong> modules: <strong>View</strong> = access report page · <strong>Insert</strong> = show chart/graph</p>
                        <table class="table table-bordered table-hover align-middle" id="module-permission-table">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th class="text-start">Module Name</th>
                                    <th>View</th>
                                    <th>Insert</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                    <th>All</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Filled dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
    @include('partials.face-recognition-modal')
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    @include('partials.face-recognition-script')
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
    <script>
        const IMAGE_PATH = "{{ rtrim(env('ImagePath'), '/') }}";
        $(document).ready(function() {
            // Show/hide permission section based on radio selection
            $('input[name="permission_type"]').on('change', function() {
                if ($('#withPermission').is(':checked')) {
                    $('#permissionsSection').slideDown();
                } else {
                    $('#permissionsSection').slideUp();
                    // Optional: uncheck all checkboxes when hiding
                    $('#permissionsSection').find('input[type="checkbox"]').prop('checked', false);
                }
            });
        });
        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            var selectedSubAdminId = localStorage.getItem('selectedSubAdminId');

            let allDesignations = [];
            let url = window.location.pathname;
            let customerId = url.split("/").pop();
            
            let loadedCustomerBranchId = null;
            let subBranchesLoaded = false;
            
            function applyCustomerBranchSelection() {
                if (subBranchesLoaded && loadedCustomerBranchId !== null) {
                    // Check if the customer's branch_id exists in our dropdown
                    if ($("#sub_admin_id option[value='" + loadedCustomerBranchId + "']").length > 0) {
                        $("#sub_admin_id").val(loadedCustomerBranchId);
                    } else {
                        // If it doesn't exist (it's likely the admin ID), select Main Branch
                        $("#sub_admin_id").val("");
                    }
                }
            }

            // Always fetch sub-branches first to determine if selectedSubAdminId is a valid sub-branch
            $.ajax({
                url: "/api/getAllSubbranch?per_page=100",
                type: "GET",
                headers: {
                    "Authorization": "Bearer " + authToken
                },
                success: function(response) {
                    if (response.status && response.data) {
                        var branchSelect = $("#sub_admin_id");
                        branchSelect.empty();
                        branchSelect.append('<option value="">Main Branch</option>');
                        
                        var isSubBranchActive = false;

                        response.data.forEach(function(branch) {
                            if (branch.role !== 'admin') {
                                branchSelect.append('<option value="' + branch.id + '">' + branch.name + '</option>');
                                if (String(branch.id) === String(selectedSubAdminId)) {
                                    isSubBranchActive = true;
                                }
                            }
                        });
                        
                        if (isSubBranchActive) {
                            $("#branch-select-div").hide();
                        } else {
                            $("#branch-select-div").show();
                        }
                        
                        subBranchesLoaded = true;
                        applyCustomerBranchSelection();
                    }
                }
            });

            function renderDesignationOptions(departmentId, selectedId = '') {
                const $designation = $('#designation_id');
                $designation.empty().append('<option value="">Select Designation</option>');

                if (!departmentId) {
                    $designation.prop('disabled', true);
                    return;
                }

                allDesignations
                    .filter(item => String(item.department_id) === String(departmentId))
                    .forEach(item => {
                        $designation.append(
                            `<option value="${item.id}" ${String(selectedId) === String(item.id) ? 'selected' : ''}>${item.designation_name}</option>`
                        );
                    });

                $designation.prop('disabled', false);
            }

            function loadDepartmentDesignationOptions(selectedDepartmentId = '', selectedDesignationId = '') {
                return $.when(
                    $.ajax({ url: '/api/departments', headers: { Authorization: `Bearer ${authToken}` } }),
                    $.ajax({ url: '/api/designations', headers: { Authorization: `Bearer ${authToken}` } })
                ).done(function(departmentsRes, designationsRes) {
                    const departments = departmentsRes[0]?.data || departmentsRes[0]?.departments || [];
                    allDesignations = designationsRes[0]?.data || designationsRes[0]?.designations || [];

                    const $department = $('#department_id');
                    $department.empty().append('<option value="">Select Department</option>');
                    departments.forEach(item => {
                        $department.append(
                            `<option value="${item.id}" ${String(selectedDepartmentId) === String(item.id) ? 'selected' : ''}>${item.department_name}</option>`
                        );
                    });

                    renderDesignationOptions(selectedDepartmentId, selectedDesignationId);
                });
            }

            $('#department_id').on('change', function() {
                renderDesignationOptions($(this).val());
            });

            // AJAX call to get customer data
            $.ajax({
                url: `/api/getStaff/${customerId}`,
                type: "GET",
                headers: {
                    Authorization: `Bearer ${authToken}`,
                },
                success: function(response) {
                    if (response.status) {
                        let customer = response.customer;

                        // Populate form fields
                        $("#role").val(customer.role || "");
                        // if (customer.haspermission) {
                        // console.log(customer.haspermission);
                        // console.log(customer.haspermission);

                        $('input[name="permission_type"][value="' + String(customer.haspermission) +
                            '"]').prop('checked', true);
                        // Use the shared change handler to toggle UI consistently
                        $('input[name="permission_type"]').trigger('change');
                        // }
                        
                        if (customer.branch_id) {
                            loadedCustomerBranchId = customer.branch_id;
                            applyCustomerBranchSelection();
                        }

                        $("#customer_name").val(customer.name);
                        $("#email").val(customer.email || ""); // Use empty string if email is null
                        $("#phone").val(customer.phone);
                        $("#salary").val(customer.details?.salary || "");
                        $("#joining_date").val(customer.details?.joining_date || "");
                        loadDepartmentDesignationOptions(
                            customer.details?.department_id || '',
                            customer.details?.designation_id || ''
                        );
                        $("#country").val(customer.details?.country || ""); 
                        $("#city").val(customer.details?.city || ""); 
                        $("#address").val(customer.details?.address || "");

                        // Show profile image if exists
                        if (customer.profile_image) {
                            $("#avatar-preview").attr("src", `${IMAGE_PATH}/storage/${customer.profile_image}`);
                            $("#avatar-preview-container").show(); // Make sure preview is visible
                        }

                        // Show face image if exists
                        if (customer.face_image_path) {
                            $("#face-preview").attr("src", `${IMAGE_PATH}/storage/${customer.face_image_path}`);
                            $("#face-preview-container").show();
                        }
                        
                        if (customer.face_descriptor) {
                            $("#face_descriptor").val(JSON.stringify(customer.face_descriptor));
                        }

                        let modules = response.modules;
                        let permissions = response.permissions;

                        let tbody = "";
                        modules.forEach(module => {
                            let perm = permissions.find(p => p.module_id === module.id) || {};
                            // Check if all 4 permissions are enabled
                            let isAllChecked = perm.view && perm.add && perm.edit && perm
                                .delete;

                            tbody += `
                                        <tr>
                            <td class="align-middle">
                                <i class="fa fa-folder-open text-primary me-2"></i> ${module.module}
                                <input type="hidden" name="modules[${module.id}][module_id]" value="${module.id}">
                            </td>

                            <td class="text-center">
                                <input type="hidden" name="modules[${module.id}][view]" value="0">
                                <input type="checkbox" name="modules[${module.id}][view]" value="1"
                                    data-module="${module.id}" data-type="view"
                                    class="permission-checkbox form-check-input" ${perm.view ? 'checked' : ''}>
                            </td>

                            <td class="text-center">
                                <input type="hidden" name="modules[${module.id}][add]" value="0">
                                <input type="checkbox" name="modules[${module.id}][add]" value="1"
                                    data-module="${module.id}" data-type="add"
                                    class="permission-checkbox form-check-input" ${perm.add ? 'checked' : ''}>
                            </td>

                            <td class="text-center">
                                <input type="hidden" name="modules[${module.id}][edit]" value="0">
                                <input type="checkbox" name="modules[${module.id}][edit]" value="1"
                                    data-module="${module.id}" data-type="edit"
                                    class="permission-checkbox form-check-input" ${perm.edit ? 'checked' : ''}>
                            </td>

                            <td class="text-center">
                                <input type="hidden" name="modules[${module.id}][delete]" value="0">
                                <input type="checkbox" name="modules[${module.id}][delete]" value="1"
                                    data-module="${module.id}" data-type="delete"
                                    class="permission-checkbox form-check-input" ${perm.delete ? 'checked' : ''}>
                            </td>

                            <td class="text-center">
                                <input type="checkbox" class="check-all-row form-check-input" ${isAllChecked ? 'checked' : ''}>
                            </td>
                        </tr>`;
                        });


                        $("#module-permission-table tbody").html(tbody);

                        // Select all module checkboxes
                        $(document).on("change", "#select_all_module", function() {
                            $(".permission-checkbox").prop("checked", $(this).is(":checked"));
                        });

                        // Row-level "All" checkbox
                        $(document).on("change", ".check-all-row", function() {
                            const row = $(this).closest("tr");
                            const isChecked = $(this).is(":checked");
                            row.find(".permission-checkbox").prop("checked", isChecked);
                        });

                    } else {
                        Swal.fire("Error!", "Customer not found!", "error");

                    }
                },
                error: function() {
                    Swal.fire("Error!", "Customer not found!", "error");

                },
            });

            // Handle File Input Change (Live Preview)
            $("#avatar-input").change(function(event) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $("#avatar-preview").attr("src", e.target.result);
                    $("#avatar-preview-container").show();
                };
                reader.readAsDataURL(event.target.files[0]);
            });

            if (typeof window.OmsaiFaceRecognition !== 'undefined') {
                const faceRecognition = window.OmsaiFaceRecognition.init();

                $('#captureFaceBtn').on('click', function() {
                    const staffName = $('#customer_name').val().trim() || 'Staff';

                    faceRecognition.open({
                        title: `Update Face for ${staffName}`,
                        subtitle: 'Ask the staff member to face the camera and capture one clear frame.',
                        captureLabel: 'Capture & Set',
                        onCapture: async function(descriptor, modal, imageDataUrl) {
                            $('#face_descriptor').val(JSON.stringify(descriptor));
                            
                            if (imageDataUrl) {
                                // Update preview
                                $('#face-preview').attr('src', imageDataUrl).show();
                                $('#face-upload-content').hide();
                                
                                // Create a hidden input for the captured photo if it doesn't exist
                                if ($('#captured_photo').length === 0) {
                                    $('.card-body').append('<input type="hidden" name="captured_photo" id="captured_photo">');
                                }
                                $('#captured_photo').val(imageDataUrl);
                            }

                            modal.showMatchInfo('Face captured successfully.');

                            setTimeout(function() {
                                modal.close();
                            }, 600);
                        }
                    });
                });
            }

            // Face photo file input change
            $('#face_photo_input').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        $('#face-preview').attr('src', event.target.result).show();
                        $('#face-upload-content').hide();
                        // Optional: Clear captured photo if file is uploaded
                        $('#captured_photo').val('');
                    };
                    reader.readAsDataURL(file);
                }
            });

            $("#updateCustomer").on("click", function(e) {
                e.preventDefault();
                $(".text-danger").html("");
                $(".error-permissions").html("");

                let hasError = false;

                // Staff Name validation
                let staffName = $("#customer_name").val().trim();
                if (staffName === "") {
                    $(".error-customer_name").html(" Staff name is required. ");
                    hasError = true;
                } else if (staffName.length < 3) {
                    $(".error-customer_name").html(" Staff name must be at least 3 characters. ");
                    hasError = true;
                } else if (staffName.length > 80) {
                    $(".error-customer_name").html(" Staff name must not exceed 80 characters. ");
                    hasError = true;
                }

                // Email validation
                let email = $("#email").val().trim();
                let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (email === "") {
                    $(".error-email").html(" Email is required. ");
                    hasError = true;
                } else if (!emailPattern.test(email)) {
                    $(".error-email").html(" Please enter a valid email address. ");
                    hasError = true;
                }

                // Password validation (optional)
                let password = $("#new_password").val().trim();
                if (password !== "") {
                    if (password.length < 8) {
                        $(".error-password").html(" Password must be at least 8 characters. ");
                        hasError = true;
                    }
                }

                // Phone validation
                let phone = $("#phone").val().trim();
                let phonePattern = /^[0-9]{10}$/;
                if (phone === "") {
                    $(".error-phone").html(" Phone number is required. ");
                    hasError = true;
                } else if (!phonePattern.test(phone)) {
                    $(".error-phone").html(" Please enter a valid 10-digit phone number. ");
                    hasError = true;
                }

                // Salary validation
                let salary = $("#salary").val().trim();
                if (salary === "") {
                    $(".error-salary").html(" Salary is required. ");
                    hasError = true;
                } else if (parseFloat(salary) < 0) {
                    $(".error-salary").html(" Salary must be a positive number. ");
                    hasError = true;
                }

                // Joining Date validation
                let joiningDate = $("#joining_date").val().trim();
                if (joiningDate === "") {
                    $(".error-joining_date").html(" Joining date is required. ");
                    hasError = true;
                }


                // Permission check
                if (!$('input[name="permission_type"]:checked').length) {
                    $(".error-permissions").html("** Please select With or Without Permission. **");
                    hasError = true;
                }

                // If errors exist, stop
                if (hasError) {
                    return false;
                }

                // If "With Permission" selected, ensure at least one permission checkbox is checked
                if ($('#withPermission').is(':checked')) {
                    if ($('.permission-checkbox:checked').length === 0) {
                        $(".error-permissions").html("** Please select at least one permission. **");
                        return false;
                    }
                }


                const $btn = $(this);
                const originalText = $btn.html();

                $btn.html(
                    '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...'
                ).prop("disabled", true);

                let formData = new FormData();
                if (password !== "") {
                    formData.append("password", password);
                }
                formData.append("role", $("#role").val());
                formData.append("staff_name", $("#customer_name").val());
                formData.append("email", $("#email").val());
                formData.append("phone", $("#phone").val());
                formData.append("salary", $("#salary").val());
                formData.append("joining_date", $("#joining_date").val());
                formData.append("department_id", $("#department_id").val());
                formData.append("designation_id", $("#designation_id").val());
                formData.append("country", $("#country").val());
                formData.append("city", $("#city").val());
                formData.append("address", $("#address").val());
                let permissionType = $('input[name="permission_type"]:checked').val();

                // Append to FormData
                formData.append("permission_type", permissionType);

                let avatar = $("#avatar-input")[0].files[0];
                if (avatar) {
                    formData.append("avatar", avatar);
                }

                let subAdminId = "";
                if ($("#branch-select-div").is(":visible")) {
                    subAdminId = $("#sub_admin_id").val();
                } else {
                    subAdminId = selectedSubAdminId;
                }
                formData.append("sub_admin_id", subAdminId);

                let faceDescriptor = $("#face_descriptor").val();
                if (faceDescriptor) {
                    formData.append("face_descriptor", faceDescriptor);
                }
                
                let capturedPhoto = $("#captured_photo").val();
                if (capturedPhoto) {
                    formData.append("captured_photo", capturedPhoto);
                }
                
                let facePhotoFile = $("#face_photo_input")[0]?.files[0];
                if (facePhotoFile) {
                    formData.append("face_photo", facePhotoFile);
                }

                // Collect modules and permissions
                let modules = {};
                $(".permission-checkbox").each(function() { // remove :checked
                    let moduleId = $(this).data("module");
                    let type = $(this).data("type");

                    if (!modules[moduleId]) {
                        modules[moduleId] = {
                            module_id: moduleId,
                            view: 0,
                            add: 0,
                            edit: 0,
                            delete: 0
                        };
                    }

                    // Always set 1 if checked, 0 if unchecked
                    modules[moduleId][type] = $(this).is(":checked") ? 1 : 0;
                });

                // Append module permissions to formData
                let i = 0;
                Object.values(modules).forEach((mod) => {
                    formData.append(`modules[${i}][module_id]`, mod.module_id);
                    formData.append(`modules[${i}][view]`, mod.view);
                    formData.append(`modules[${i}][add]`, mod.add);
                    formData.append(`modules[${i}][edit]`, mod.edit);
                    formData.append(`modules[${i}][delete]`, mod.delete);
                    i++;
                });

                $.ajax({
                    url: `/api/updateStaff/${customerId}`,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        Authorization: `Bearer ${authToken}`,
                    },
                    success: function(response) {
                        $btn.html(originalText).prop("disabled", false);
                        if (response.status) {
                            Swal.fire({
                                title: "Success!",
                                text: "Staff updated successfully!",
                                icon: "success",
                                confirmButtonText: "OK",
                                confirmButtonColor: "#ff9f43",
                            }).then(() => {
                                window.location.href = "/staff";
                            });
                        }
                    },
                    error: function(xhr) {
                        $btn.html(originalText).prop("disabled", false);
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $(".error-" + key).html(value[0]);
                            });
                        } else {
                            Swal.fire({
                                title: "Error!",
                                text: "Something went wrong. Please try again.",
                                icon: "error",
                                confirmButtonText: "OK"
                            });
                        }
                    },
                });
            });


        });
    </script>


    <script>
        $(document).ready(function() {
            // When 'Select All Module' is clicked
            $('#select_all_module').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('.permission-checkbox, .check-all-row').prop('checked', isChecked);
            });

            // When 'All' checkbox in each row is clicked
            $('.check-all-row').on('change', function() {
                const isChecked = $(this).is(':checked');
                const row = $(this).closest('tr');
                row.find('.permission-checkbox').prop('checked', isChecked);
            });

            // If any permission checkbox is changed manually, update the 'All' checkbox in that row
            $('.permission-checkbox').on('change', function() {
                const row = $(this).closest('tr');
                const allChecked = row.find('.permission-checkbox').length === row.find(
                    '.permission-checkbox:checked').length;
                row.find('.check-all-row').prop('checked', allChecked);
            });

            // Sync master checkbox if all checkboxes are checked or unchecked
            function syncSelectAllModule() {
                const totalPermissions = $('.permission-checkbox').length;
                const totalChecked = $('.permission-checkbox:checked').length;
                $('#select_all_module').prop('checked', totalPermissions === totalChecked);
            }

            // Call sync function when any permission checkbox is changed
            $('.permission-checkbox, .check-all-row').on('change', syncSelectAllModule);
        });
    </script>
@endpush

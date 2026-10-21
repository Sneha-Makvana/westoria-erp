@extends('layout.app')

@section('title', 'Edit Vendor')

@section('content')
    <style>
        @media screen and (max-width: 768px) {
            .form-group {
                margin-bottom: 10px !important
            }
        }

        a.btn.back-button {
            background: #ff9f43;
            color: #fff;
        }
    </style>
    <div class="content">
        {{-- <div class="page-header">
        <div class="page-title">
            <h4>Edit Vendor</h4>
        </div>
    </div> --}}
        <div class="page-header ">
            <div class="page-title">
                <h4>Edit Vendor</h4>
            </div>
            <div class="back-button">
                <a href="{{ route('vendor.list') }}" class="btn back-button"> <i class="fa-solid fa-arrow-left"></i>
                    Back</a></br>
                <span class="success_submit text-danger"></span>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Vendor Name <span class="text-danger">*</span></label>
                            <input type="text" id="customer_name" maxlength="80" class="form-control">
                            <span class="text-danger error-name"></span>

                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Company Name <span class="text-danger">*</span></label>
                            <input type="text" id="company_name" maxlength="80" class="form-control">
                            <span class="text-danger error-company_name"></span>

                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" id="email" class="form-control">
                            <span class="text-danger error-email"></span>

                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Phone <span class="text-danger">*</span></label>
                            <input type="text" id="phone" maxlength="10" class="form-control">
                            <span class="text-danger error-phone"></span>

                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Country</label>
                            <input type="text" id="country" class="form-control">
                            <span class="text-danger error-country"></span>

                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" id="city" class="form-control">
                            <span class="text-danger error-city"></span>

                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>State Code</label>
                            <input type="text" id="state_code" class="form-control">
                            <span class="text-danger error-state_code"></span>

                        </div>
                    </div>
                    <!-- State Name (auto-filled) -->
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>State Name</label>
                            <input type="text" id="state_name" name="state_name" class="form-control" readonly placeholder="Auto-filled from state code">
                            <span class="text-danger error-state_name"></span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>PAN Number</label>
                            <input type="text" id="pan_number" maxlength="10" class="form-control">
                            <span class="text-danger error-pan_number"></span>

                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>GST Number</label>
                            <input type="text" id="gst_number" maxlength="15" class="form-control">
                            <span class="text-danger error-gst_number"></span>

                        </div>
                    </div>
                    <div class="col-lg-3 col-12">
                        <div class="form-group">
                            <label>Address</label>
                            <textarea id="address" class="form-control"></textarea>
                            <span class="text-danger error-address"></span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-12">
                        <div class="form-group">
                            <label>Address Line 2</label>
                            <textarea id="address_line2" class="form-control"></textarea>
                            <span class="text-danger error-address_line2"></span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-12">
                        <div class="form-group">
                            <label>Address Line 3</label>
                            <textarea id="address_line3" class="form-control"></textarea>
                            <span class="text-danger error-address_line3"></span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Pin Code</label>
                            <input type="text" id="pin_code" class="form-control">
                            <span class="text-danger error-pin_code"></span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Phone 2</label>
                            <input type="text" id="phone_2" class="form-control">
                            <span class="text-danger error-phone_2"></span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Supplier Since</label>
                            <input type="date" id="supplier_since" class="form-control">
                            <span class="text-danger error-supplier_since"></span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Supplier Category</label>
                            <input type="text" id="supplier_category" class="form-control">
                            <span class="text-danger error-supplier_category"></span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>TIN Number</label>
                            <input type="text" id="tin_number" class="form-control">
                            <span class="text-danger error-tin_number"></span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Credit Days</label>
                            <input type="number" id="credit_days" class="form-control">
                            <span class="text-danger error-credit_days"></span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>PAN Status</label>
                            <input type="text" id="pan_status" class="form-control">
                            <span class="text-danger error-pan_status"></span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>GSTIN Status</label>
                            <input type="text" id="gstin_status" class="form-control">
                            <span class="text-danger error-gstin_status"></span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Account Group</label>
                            <input type="text" id="account_group" class="form-control">
                            <span class="text-danger error-account_group"></span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>TAN Status</label>
                            <input type="text" id="tan_status" class="form-control">
                            <span class="text-danger error-tan_status"></span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>TAN Number</label>
                            <input type="text" id="tan_number" class="form-control">
                            <span class="text-danger error-tan_number"></span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>MSME Status</label>
                            <input type="text" id="msme_status" class="form-control">
                            <span class="text-danger error-msme_status"></span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>MSME Number</label>
                            <input type="text" id="msme_number" class="form-control">
                            <span class="text-danger error-msme_number"></span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" id="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <span class="text-danger error-status"></span>
                        </div>
                    </div>
                    <div class="col-lg-5 col-12">
                        <div class="form-group">
                            <label>Photo</label>
                            <div class="image-upload">
                                <input type="file" id="avatar-input" accept="image/*">
                                <div class="image-uploads">
                                    <img src="{{ env('ImagePath') . '/admin/assets/img/icons/upload.svg' }}"
                                        alt="Upload Icon">
                                    <h4>Drag and drop a file to upload</h4>
                                </div>
                            </div>
                            <!-- Profile Image Preview -->
                            <div id="avatar-preview-container" style="display: none; margin-top: 10px;">
                                <img id="avatar-preview" src="" alt="Profile Image"
                                    style="max-width: 150px; border-radius: 8px;">
                            </div>
                            <span class="text-danger error-avatar"></span>

                        </div>
                    </div>
                    <div class="col-lg-12">
                        <button type="submit" class="btn btn-submit me-2" id="updateCustomer">Update</button>
                        <a class="btn btn-cancel" href="{{ route('vendor.list') }}">Cancel</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {

            // State code → name lookup
            const stateCodeToName = {
                "01": "Jammu and Kashmir", "02": "Himachal Pradesh", "03": "Punjab",
                "04": "Chandigarh", "05": "Uttarakhand", "06": "Haryana", "07": "Delhi",
                "08": "Rajasthan", "09": "Uttar Pradesh", "10": "Bihar", "11": "Sikkim",
                "12": "Arunachal Pradesh", "13": "Nagaland", "14": "Manipur",
                "15": "Mizoram", "16": "Tripura", "17": "Meghalaya", "18": "Assam",
                "19": "West Bengal", "20": "Jharkhand", "21": "Odisha", "22": "Chhattisgarh",
                "23": "Madhya Pradesh", "24": "Gujarat", "27": "Maharashtra",
                "29": "Karnataka", "33": "Tamil Nadu", "36": "Telangana"
            };

            $('#state_code').on('input', function() {

                let value = this.value;

                // GST number validation and auto-fill hoy to allow (24 - Gujarat)
                if (value.includes(' - ')) {
                    return;
                }

                // Only number allow + max 15 digit (GST number standard)
                this.value = value.replace(/[^0-9]/g, '').substring(0, 15);

                // Auto-fill state name
                const padded = this.value.trim().padStart(2, '0');
                $('#state_name').val(stateCodeToName[padded] || stateCodeToName[this.value.trim()] || '');

            });

            $("#phone").on("input", function() {

                let value = $(this).val().replace(/\D/g, '');

                if (value.length > 10) {
                    value = value.substring(0, 10);
                }

                $(this).val(value);

            });

            $("#customer_name").on("input", function() {

                let value = $(this).val();

                if (value.length > 80) {
                    $(this).val(value.substring(0, 80));
                }

            });

            var authToken = localStorage.getItem("authToken");
            let url = window.location.pathname;
            let customerId = url.split("/").pop();

            // AJAX call to get customer data
            $.ajax({
                url: `/api/getSupplier/${customerId}`,
                type: "GET",
                headers: {
                    Authorization: `Bearer ${authToken}`,
                },
                success: function(response) {
                    if (response.status) {
                        let customer = response.customer;

                        // Populate form fields
                        $("#customer_name").val(customer.name);
                        $("#email").val(customer.email);
                        $("#phone").val(customer.phone);
                        $("#country").val(customer.details.country);
                        $("#city").val(customer.details.city);
                        $("#state_code").val(customer.state_code);
                        $("#address").val(customer.details?.address || "");
                        $("#pan_number").val(customer.pan_number || "");
                        $("#gst_number").val(customer.gst_number || "");
                        $("#company_name").val(customer.company_name || "");
                        
                        $("#address_line2").val(customer.details?.address_line2 || "");
                        $("#address_line3").val(customer.details?.address_line3 || "");
                        $("#pin_code").val(customer.details?.pin_code || "");
                        $("#phone_2").val(customer.details?.phone_2 || "");
                        $("#supplier_since").val(customer.details?.supplier_since || "");
                        $("#supplier_category").val(customer.details?.supplier_category || "");
                        $("#tin_number").val(customer.details?.tin_number || "");
                        $("#credit_days").val(customer.details?.credit_days || "");
                        $("#pan_status").val(customer.details?.pan_status || "");
                        $("#gstin_status").val(customer.details?.gstin_status || "");
                        $("#account_group").val(customer.details?.account_group || "");
                        $("#tan_status").val(customer.details?.tan_status || "");
                        $("#tan_number").val(customer.details?.tan_number || "");
                        $("#msme_status").val(customer.details?.msme_status || "");
                        $("#msme_number").val(customer.details?.msme_number || "");
                        $("#status").val(customer.details?.status || "active");

                        // Populate state_name: use stored value or derive from code
                        let savedStateName = customer.state_name || '';
                        if (!savedStateName && customer.state_code) {
                            savedStateName = stateCodeToName[String(customer.state_code).padStart(2, '0')] || '';
                        }
                        $("#state_name").val(savedStateName);

                // GST details fetching functionality (same as addsupplier.blade.php)
                $('#gst_number').on('input', function() {
                    let $gstInput = $(this);
                    let $errorDiv = $('.error-gst_number');
                    let normalizedGst = $gstInput.val().toUpperCase().replace(/[^0-9A-Z]/g, '').substring(0, 15);
                    
                    if (normalizedGst.includes(' - ')) return;
                    
                    $gstInput.val(normalizedGst);
                    
                    // Show loading state
                    $gstInput.addClass('loading');
                    $errorDiv.html('<span style="color: #1B2850;"><i class="fas fa-spinner fa-spin"></i> Fetching details...</span>');
                    
                    $.ajax({
                        url: '/api/fetch-gst-details',
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            gst_number: normalizedGst
                        },
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                            "Authorization": "Bearer " + authToken
                        },
                        beforeSend: function() {
                            $gstInput.prop('readonly', true);
                        },
                        success: function(res) {
                            $errorDiv.html('');
                            if (res.error) {
                                $errorDiv.html('<span class="text-danger">GST details not found.</span>');
                                return;
                            }
                            
                            // Populate state code, PAN number, country, city, company name from GST details
                            if (res.state_code) $('#state_code').val(res.state_code || '');
                            if (res.pan_number) $('#pan_number').val(res.pan_number || '');
                            if (res.country) $('#country').val(res.country || '');
                            if (res.city) $('#city').val(res.city || '');
                            if (res.company_name) $('#company_name').val(res.company_name || '');
                            // Auto-fill state_name
                            if (res.state) {
                                $('#state_name').val(res.state);
                            } else if (res.state_code) {
                                const padded = String(res.state_code).padStart(2, '0');
                                $('#state_name').val(stateCodeToName[padded] || '');
                            }
                        },
                        error: function(xhr) {
                            $errorDiv.html('<span class="text-danger">Failed to fetch GST details.</span>');
                            console.error('GST fetch failed', xhr);
                        },
                        complete: function() {
                            $gstInput.removeClass('loading');
                            $gstInput.prop('readonly', false);
                        }
                    });
                });

                        // Show profile image if exists
                        if (customer.profile_image) {
                            $("#avatar-preview").attr("src",
                                `{{ env('ImagePath') . '/storage/' }}${customer.profile_image}`);
                            $("#avatar-preview-container").show(); // Make sure preview is visible
                        }
                    } else {
                        Swal.fire("Error!", "Vendor not found!", "error");
                        window.location.href = "{{ route('vendor.list') }}";
                    }
                },
                error: function() {
                    Swal.fire("Error!", "Vendor not found!", "error");
                    window.location.href = "{{ route('vendor.list') }}";
                },
            });

            // Handle File Input Change (Live Preview)
            $("#avatar-input").change(function(event) {

                let file = event.target.files[0];

                if (!file) return;

                const allowedTypes = [
                    "image/jpeg",
                    "image/png",
                    "image/jpg",
                    "image/webp",
                    "image/gif"
                ];

                if (!allowedTypes.includes(file.type)) {

                    $(".error-avatar").html("Only image files are allowed (JPG, PNG, WEBP, GIF)");

                    $(this).val("");
                    $("#avatar-preview-container").hide();

                    return;
                }

                $(".error-avatar").html("");

                let reader = new FileReader();
                reader.onload = function(e) {
                    $("#avatar-preview").attr("src", e.target.result);
                    $("#avatar-preview-container").show();
                };

                reader.readAsDataURL(file);

            });

            $("#updateCustomer").on("click", function(e) {
                e.preventDefault();

                // Reference the update button and store original HTML
                const $btn = $(this);
                const originalText = $btn.html();
                if ($btn.prop("disabled")) {
                    return;
                }

                let formData = new FormData();
                formData.append("customer_name", $("#customer_name").val());
                formData.append("company_name", $("#company_name").val());
                formData.append("email", $("#email").val());
                formData.append("phone", $("#phone").val());
                formData.append("country", $("#country").val());
                formData.append("city", $("#city").val());
                formData.append("state_code", $("#state_code").val());
                formData.append("state_name", $("#state_name").val());
                formData.append("address", $("#address").val());
                formData.append("pan_number", $("#pan_number").val());
                formData.append("gst_number", $("#gst_number").val());
                
                formData.append("address_line2", $("#address_line2").val());
                formData.append("address_line3", $("#address_line3").val());
                formData.append("pin_code", $("#pin_code").val());
                formData.append("phone_2", $("#phone_2").val());
                formData.append("supplier_since", $("#supplier_since").val());
                formData.append("supplier_category", $("#supplier_category").val());
                formData.append("tin_number", $("#tin_number").val());
                formData.append("credit_days", $("#credit_days").val());
                formData.append("pan_status", $("#pan_status").val());
                formData.append("gstin_status", $("#gstin_status").val());
                formData.append("account_group", $("#account_group").val());
                formData.append("tan_status", $("#tan_status").val());
                formData.append("tan_number", $("#tan_number").val());
                formData.append("msme_status", $("#msme_status").val());
                formData.append("msme_number", $("#msme_number").val());
                formData.append("status", $("#status").val());

                let avatar = $("#avatar-input")[0].files[0]; // Corrected here
                if (avatar) {
                    formData.append("avatar", avatar);
                }
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

                formData.append("selectedSubAdminId", selectedSubAdminId);

                $.ajax({
                    url: `/api/updateSupplier/${customerId}`,
                    type: "POST", // Laravel doesn't support PUT with FormData directly
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $btn.html(
                                '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...'
                            )
                            .prop("disabled", true);
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        Authorization: `Bearer ${authToken}`,
                    },
                    success: function(response) {
                        Swal.fire({
                            title: "Success!",
                            text: "Vendor update successfully!",
                            icon: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#ff9f43",
                        }).then(() => {
                            window.location.href = "/vendors";
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $(".error-" + key).html(value[
                                    0]); // Show error below each field
                            });
                        } else {
                            Swal.fire("Error!", "Something went wrong. Please try again.",
                                "error");
                        }
                    },
                    complete: function() {
                        $btn.html(originalText).prop("disabled", false);
                    }
                });
            });
        });
    </script>
@endpush

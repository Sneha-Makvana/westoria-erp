@extends('layout.app')

@section('title', 'Edit Customer')

@section('content')
    <style>
        @media screen and (max-width: 768px) {
            .form-group {
                margin-bottom: 10px !important
            }

            .customer-address-col,
            .customer-delivery-address-col {
                width: 100% !important;
                flex: 0 0 100% !important;
                max-width: 100% !important;
            }

            .customer-delivery-header {
                display: flex;
                flex-direction: row;
                align-items: center !important;
                justify-content: space-between !important;
                gap: 8px;
            }

            .customer-delivery-header > label {
                flex: 0 1 auto;
            }

            .customer-delivery-header .form-check {
                flex: 0 0 auto;
                margin-left: 0;
            }
        }

        .customer-delivery-header {
            display: flex;
            width: 100%;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

     a.btn.back-button {
    background: #ff9f43;
    color: #fff;
}
</style>
<div class="content">
    {{-- <div class="page-header">
        <div class="page-title">
            <h4>Edit Customer</h4>

            </div>
        </div>
    </div> --}}
    <div class="page-header ">
            <div class="page-title">
                <h4>Edit Customer</h4>
            </div>
             <div class="back-button">
                <a href="{{ route('customer.list') }}" class="btn back-button"> <i class="fa-solid fa-arrow-left"></i> Back</a></br>
                            <span class="success_submit text-danger"></span>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Customer Code </label>
                            <input type="text" id="customer_code" maxlength="255" class="form-control">
                            <div class="text-danger error-customer_code"></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Customer Name <span class="text-danger">*</span></label>
                            <input type="text" id="customer_name" maxlength="80" class="form-control">
                            <div class="text-danger error-customer_name"></div>

                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Company Name </label>
                            <input type="text" id="company_name" maxlength="80" class="form-control">
                            <div class="text-danger error-company_name"></div>

                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" id="email" class="form-control">
                            <div class="text-danger error-email"></div>

                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Phone <span class="text-danger"></span></label>
                            <input type="text" id="phone" class="form-control">
                            <div class="text-danger error-phone"></div>

                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Alternate Phone <small class="text-muted">Optional</small></label>
                            <input type="text" id="alternate_phone" class="form-control">
                            <div class="text-danger error-alternate_phone"></div>

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
                            <label>State Code</label>
                            <input type="text" id="state_code" name="state_code" class="form-control">
                            <div class="text-danger error-state_code"></div>

                        </div>
                    </div>
                    <!-- State Name (auto-filled) -->
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>State Name</label>
                            <input type="text" id="state_name" name="state_name" class="form-control" readonly placeholder="Auto-filled from state code">
                            <div class="text-danger error-state_name"></div>
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

                    <div class="col-lg-3 col-sm-12 col-6 customer-address-col">
                        <div class="form-group">
                            <label>Address</label>
                            <textarea id="address" name="address" class="form-control"></textarea>
                            <div class="text-danger error-address"></div>

                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-12 col-6 customer-delivery-address-col">
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center mb-1 customer-delivery-header">
                                <label class="mb-0">Delivery Address</label>
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" id="use_same_address">
                                    <label class="form-check-label small mb-0" style="text-transform: none; font-weight: normal;" for="use_same_address">Use Address</label>
                                </div>
                            </div>
                            <textarea id="delivery_address" name="delivery_address" class="form-control"></textarea>
                            <div class="text-danger error-delivery_address"></div>

                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-12 col-12">
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="mb-0">Photo</label>
                                <button type="button" class="btn btn-sm btn-outline-primary btn-open-face-scan" data-target="edit_customer">
                                    <i class="fas fa-camera"></i> Scan Face
                                </button>
                            </div>
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
                                    style="max-width: 150px; border-radius: 8px;">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <button type="submit" class="btn btn-submit me-2" id="updateCustomer">Update</button>
                        <a class="btn btn-cancel" href="{{ route('customer.list') }}">Cancel</a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Face Scan Modal -->
    <x-face-scan-modal />
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            let url = window.location.pathname;
            let customerId = url.split("/").pop();

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

            // AJAX call to get customer data
            $.ajax({
                url: `/api/getCustomer/${customerId}`,
                type: "GET",
                headers: {
                    Authorization: `Bearer ${authToken}`,
                },
                success: function(response) {
                    if (response.status) {
                        let customer = response.customer;

                        // Populate form fields
                        $("#customer_name").val(customer.name);
                        $("#email").val(customer.email || ""); // Use empty string if email is null
                        $("#phone").val(customer.phone);
                        $("#alternate_phone").val(customer.alternate_phone || "");
                        $("#pan_number").val(customer.pan_number ||
                        ""); // Use empty string if pan_number is null
                        $("#gst_number").val(customer.gst_number || ""); // Use empty string if gst
                        $("#company_name").val(customer.company_name || ""); // Use empty string if company_name is null
                        let existingCode = customer.customer_code || "";
                        if (!existingCode) {
                            // Auto-generate if no code exists
                            let customerId = customer.id || 1;
                            existingCode = 'CUST' + String(customerId).padStart(4, '0');
                        }
                        $("#customer_code").val(existingCode);

                // GST details fetching functionality (same as addsupplier.blade.php and addsupplier.blade.php)
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
                            // Auto-fill state_name from fetched state or state_code
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
                        $("#country").val(customer.details.country ||
                        ""); // Use empty string if country is null
                        $("#city").val(customer.details.city || ""); // Use empty string if city is null
                        $("#state_code").val(customer.state_code ||
                        ""); // Use empty string if state_code is null
                        $("#address").val(customer.details.address ||
                        ""); // Use empty string if address is null
                        $("#delivery_address").val(customer.details.delivery_address ||
                        ""); // Use empty string if delivery_address is null

                        if (customer.details.address && customer.details.address === customer.details.delivery_address) {
                            $("#use_same_address").prop("checked", true);
                        }

                        // Populate state_name: use stored value or derive from code
                        let savedStateName = customer.state_name || '';
                        if (!savedStateName && customer.state_code) {
                            savedStateName = stateCodeToName[String(customer.state_code).padStart(2, '0')] || '';
                        }
                        $("#state_name").val(savedStateName);

                        // Show profile image if exists
                        if (customer.profile_image) {
                            $("#avatar-preview").attr("src", `/storage/${customer.profile_image}`);
                            $("#avatar-preview-container").show(); // Make sure preview is visible
                        }
                    } else {
                        Swal.fire("Error!", "Customer not found!", "error");
                        window.location.href = "{{ route('customer.list') }}";
                    }
                },
                error: function() {
                    Swal.fire("Error!", "Customer not found!", "error");
                    window.location.href = "{{ route('customer.list') }}";
                },
            });

            // Handle File Input Change (Live Preview)
            // $("#avatar-input").change(function(event) {
            //     let reader = new FileReader();
            //     reader.onload = function(e) {
            //         $("#avatar-preview").attr("src", e.target.result);
            //         $("#avatar-preview-container").show();
            //     };
            //     reader.readAsDataURL(event.target.files[0]);
            // });
            
            window.onFaceCaptured = function(embeddingArray, faceDataUrl) {
                if (window.faceScanTarget === 'edit_customer') {
                    // Show in preview
                    $("#avatar-preview").attr("src", faceDataUrl);
                    $("#avatar-preview-container").show();
                    
                    // Immediately save embedding and image
                    $.ajax({
                        url: '/api/customers/face-register',
                        method: 'POST',
                        data: {
                            customer_id: customerId,
                            embedding: embeddingArray,
                            image_data: faceDataUrl
                        },
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                            "Authorization": `Bearer ${authToken}`
                        },
                        success: function(res) {
                            if (res.status) {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    icon: 'success',
                                    title: 'Face & Photo updated successfully'
                                });
                            }
                        }
                    });
                }
            };
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

                    $(".error-avatar").html("Only image files are allowed.");

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

            $('#state_code').on('input', function() {

                if (this.value.includes(' - ')) return;

                this.value = this.value.replace(/[^0-9]/g, '').substring(0, 15);

                // Auto-fill state name
                const padded = this.value.trim().padStart(2, '0');
                $('#state_name').val(stateCodeToName[padded] || stateCodeToName[this.value.trim()] || '');

            });

            $("#customer_name").on("input", function() {
                let value = $(this).val();

                if (value.length > 80) {
                    $(this).val(value.substring(0, 80));
                }
            });

            /*
            $("#phone, #alternate_phone").on("input", function() {
                let value = $(this).val();

                // remove non-numbers
                value = value.replace(/\D/g, '');

                // limit 10 digits
                if (value.length > 10) {
                    value = value.substring(0, 10);
                }

                $(this).val(value);
            });
            */

            $("#updateCustomer").on("click", function(e) {
                e.preventDefault();
                const $btn = $(this);
                const originalText = $btn.html();

                // Disable button and show loader
                $btn.html(
                        '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...'
                        )
                    .prop("disabled", true);
                // console.log($("#country").val());

                let formData = new FormData();
                formData.append("customer_name", $("#customer_name").val());
                formData.append("company_name", $("#company_name").val());
                formData.append("customer_code", $("#customer_code").val());
                formData.append("email", $("#email").val());
                formData.append("phone", $("#phone").val());
                formData.append("alternate_phone", $("#alternate_phone").val());
                formData.append("pan_number", $("#pan_number").val());
                formData.append("gst_number", $("#gst_number").val());
                formData.append("country", $("#country").val());
                formData.append("city", $("#city").val());
                formData.append("state_code", $("#state_code").val());
                formData.append("state_name", $("#state_name").val());
                formData.append("address", $("#address").val());
                formData.append("delivery_address", $("#delivery_address").val());

                let avatar = $("#avatar-input")[0].files[0];
                // console.log(avatar);
                if (avatar) {
                    formData.append("avatar", avatar);
                }

                $.ajax({
                    url: `/api/updateCustomer/${customerId}`,
                    type: "POST", // Laravel doesn't support `PUT` directly with FormData, so use POST with `_method` as PUT
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        Authorization: `Bearer ${authToken}`,
                    },
                    success: function(response) {
                        $btn.html(originalText).prop("disabled", false); // Restore button
                        if (response.status) {
                            Swal.fire({
                                title: "Success!",
                                text: "Customer update successfully!",
                                icon: "success",
                                confirmButtonText: "OK",
                                confirmButtonColor: "#ff9f43",
                            }).then(() => {
                                window.location.href = "/customer";
                            });
                        }
                    },
                    error: function(xhr) {
                        $btn.html(originalText).prop("disabled", false); // Restore button
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $(".error-" + key).html(value[
                                0]); // Show error below each field
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

            // Sync Address to Delivery Address if "Use Address" is checked
            $('#use_same_address').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#delivery_address').val($('#address').val());
                }
            });

            $('#address').on('input', function() {
                if ($('#use_same_address').is(':checked')) {
                    $('#delivery_address').val($(this).val());
                }
            });

            $('#delivery_address').on('input', function() {
                if ($('#use_same_address').is(':checked') && $(this).val() !== $('#address').val()) {
                    $('#use_same_address').prop('checked', false);
                }
            });
        });
    </script>
@endpush

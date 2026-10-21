@extends('layout.app')

@section('title', 'Add Customer')

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
                flex-direction: row;
                align-items: center !important;
                justify-content: space-between !important;
                gap: 8px;
            }

            .customer-delivery-header .form-check {
                margin-left: 0;
            }

            .customer-delivery-header > label {
                flex: 0 1 auto;
            }

            .customer-delivery-header .form-check {
                flex: 0 0 auto;
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

        .gst-input-wrap {
            position: relative;
        }

        .gst-loader {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            display: none;
            color: #ff9f43;
            pointer-events: none;
        }

        #gst_number.loading {
            padding-right: 36px;
        }
    </style>
<div class="content">
    {{-- <div class="page-header">
        <div class="page-title">
            <h4>Add Customer</h4>

            </div>
        </div>
    </div> --}}
     <div class="page-header ">
            <div class="page-title">
                <h4>Add Customer</h4>
            </div>
             <div class="back-button">
                <a href="{{ route('customer.list') }}" class="btn back-button"> <i class="fa-solid fa-arrow-left"></i> Back</a></br>
                            <span class="success_submit text-danger"></span>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form id="customerForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="face_embedding" id="face_embedding" value="">
                    <div class="row">
                        <!-- Customer Code -->
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Customer Code </label>
                                <input type="text" name="customer_code" maxlength="255" id="customer_code"
                                    class="form-control"
                                    value="{{ $nextCustomerCode ?? '' }}">
                                <div class="text-danger error-customer_code"></div>
                            </div>
                        </div>
                        <!-- Customer Name -->
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Customer Name <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" maxlength="80" id="customer_name"
                                    class="form-control">
                                <div class="text-danger error-customer_name"></div>
                            </div>
                        </div>
                        <!-- Company Name -->
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Company Name </label>
                                <input type="text" name="company_name" maxlength="80" id="company_name" class="form-control">
                                <div class="text-danger error-company_name"></div>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" id="email" class="form-control">
                                <div class="text-danger error-email"></div>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="phone" class="form-control">
                                <div class="text-danger error-phone"></div>
                            </div>
                        </div>

                        <!-- Alternate Phone -->
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Alternate Phone <small class="text-muted">Optional</small></label>
                                <input type="text" name="alternate_phone" id="alternate_phone" class="form-control">
                                <div class="text-danger error-alternate_phone"></div>
                            </div>
                        </div>

                        <!-- Country -->
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Country</label>
                                <input type="text" name="country" id="country" class="form-control">
                                <div class="text-danger error-country"></div>
                            </div>
                        </div>

                        <!-- City -->
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>City</label>
                                <input type="text" name="city" id="city" class="form-control">
                                <div class="text-danger error-city"></div>
                            </div>
                        </div>

                        <!-- State Code -->
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>State Code</label>
                                <input type="text" name="state_code" id="state_code" class="form-control">
                                <div class="text-danger error-state_code"></div>
                            </div>
                        </div>

                        <!-- State Name (auto-filled) -->
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>State Name</label>
                                <input type="text" name="state_name" id="state_name" class="form-control" readonly placeholder="Auto-filled from state code">
                                <div class="text-danger error-state_name"></div>
                            </div>
                        </div>

                        <!-- PAN Number -->
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>PAN Number</label>
                                <input type="text" name="pan_number"  maxlength="10" id="pan_number" class="form-control">
                                <span class="text-danger error-pan_number"></span>
                            </div>
                        </div>

                        <!-- GST Number -->
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>GST Number</label>
                                <div class="gst-input-wrap">
                                    <input type="text" name="gst_number" maxlength="15" id="gst_number"
                                        class="form-control">
                                    <span id="gst-loader" class="gst-loader">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </span>
                                </div>
                                <span class="text-danger error-gst_number"></span>
                            </div>
                        </div>


                        <!-- Address -->
                        <div class="col-lg-3 col-sm-6 col-6 customer-address-col">
                            <div class="form-group">
                                <label>Address</label>
                                <textarea name="address" id="address" class="form-control"></textarea>
                                <div class="text-danger error-address"></div>
                            </div>
                        </div>

                        <!-- Delivery Address -->
                        <div class="col-lg-3 col-sm-6 col-6 customer-delivery-address-col">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center mb-1 customer-delivery-header">
                                    <label class="mb-0">Delivery Address</label>
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox" id="use_same_address">
                                        <label class="form-check-label small mb-0" style="text-transform: none; font-weight: normal;" for="use_same_address">Use Address</label>
                                    </div>
                                </div>
                                <textarea name="delivery_address" id="delivery_address" class="form-control"></textarea>
                                <div class="text-danger error-delivery_address"></div>
                            </div>
                        </div>

                        <!-- Avatar Upload -->
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="mb-0">Photo</label>
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-open-face-scan" id="btnScanFace" style="border-radius: 4px; padding: 4px 8px; font-size: 12px;">
                                        <i class="fas fa-camera"></i> Scan Face
                                    </button>
                                </div>
                                <div class="image-upload">
                                    <input type="file" name="avatar" id="avatar" class="form-control"
                                        accept="image/*">
                                    <div class="image-uploads">
                                        <img src="{{ env('ImagePath') . '/admin/assets/img/icons/upload.svg' }}"
                                            alt="Upload Icon">
                                        <h4>Drag and drop a file to upload</h4>
                                    </div>
                                </div>
                                <div class="text-danger error-avatar"></div>

                                <!-- Avatar Preview -->
                                <div id="avatar-preview-container" style="display: none; margin-top: 10px;">
                                    <img id="avatar-preview" src="" alt="Avatar Preview"
                                        style="max-width: 150px; border-radius: 8px;">
                                </div>
                            </div>
                        </div>

                        <!-- Submit & Cancel Buttons -->
                        <div class="col-lg-12">
                            <button type="submit" id="submitCustomerBtn" class="btn btn-submit me-2">Submit</button>
                            <a href="{{ route('customer.list') }}" class="btn btn-cancel">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- Face Scan Modal Component -->
    @include('components.face-scan-modal')
@endsection

@push('js')
    <!-- Load face-api.js from CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.js"></script>
    <script>
        $(document).ready(function() {

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

            var authToken = localStorage.getItem("authToken");
            let $gstLoader = $('#gst-loader');

            // State code ↔ name lookup tables
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

            $('#gst_number').on('input', function() {
                let $gstInput = $(this);
                let $errorDiv = $('.error-gst_number');

                var normalizedGst = $(this).val().toUpperCase().replace(/[^0-9A-Z]/g, '').substring(0, 15);
                $(this).val(normalizedGst);

                var gst = normalizedGst.trim();
                if (!/^[0-9A-Z]{15}$/.test(gst)) {
                    $gstLoader.hide();
                    $gstInput.removeClass('loading');
                    return;
                }

                const stateMap = {
                    "Jammu and Kashmir": "01",
                    "Himachal Pradesh": "02",
                    "Punjab": "03",
                    "Chandigarh": "04",
                    "Uttarakhand": "05",
                    "Haryana": "06",
                    "Delhi": "07",
                    "Rajasthan": "08",
                    "Uttar Pradesh": "09",
                    "Bihar": "10",
                    "Sikkim": "11",
                    "Arunachal Pradesh": "12",
                    "Nagaland": "13",
                    "Manipur": "14",
                    "Mizoram": "15",
                    "Tripura": "16",
                    "Meghalaya": "17",
                    "Assam": "18",
                    "West Bengal": "19",
                    "Jharkhand": "20",
                    "Odisha": "21",
                    "Chhattisgarh": "22",
                    "Madhya Pradesh": "23",
                    "Gujarat": "24",
                    "Maharashtra": "27",
                    "Karnataka": "29",
                    "Tamil Nadu": "33",
                    "Telangana": "36"
                };
                $.ajax({
                    url: '/api/fetch-gst-details',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        gst_number: gst
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        "Authorization": "Bearer " + authToken,
                    },
                    beforeSend: function() {
                        $gstLoader.show();
                        $gstInput.addClass('loading');
                        $gstInput.prop('readonly', true);
                        $errorDiv.html(
                            '<span style="color: #1B2850;"><i class="fas fa-spinner fa-spin"></i> Fetching details...</span>'
                        );
                    },
                    success: function(res) {
                        $errorDiv.html('');
                        if (res.error) {
                            $errorDiv.html(
                                '<span class="text-danger">GST details not found.</span>');
                            return;
                        }

                        let stateName = res.state || '';
                        let stateCode = stateMap[stateName] || '';

                        if (stateCode) {
                            $('#state_code').val(stateCode);
                        } else {
                            $('#state_code').val('');
                        }
                        $('#state_name').val(stateName);

                        // Populate fields based on API response
                        $('#customer_name').val(res.legal_name || '');
                        $('#company_name').val(res.company_name || '');
                        $('#address').val(res.primary_address || '');
                        $('#city').val(res.city || '');
                        $('#country').val(res.country || '');

                        // Extract PAN from GST (chars 3 to 12)
                        if (gst.length >= 12) {
                            const pan = gst.substring(2, 12);
                            $('#pan_number').val(pan);
                        }
                    },
                    error: function(xhr, status, error) {
                        $errorDiv.html(
                            '<span class="text-danger">Failed to fetch GST details.</span>');
                        console.error('GST fetch failed', xhr, status, error);
                    },
                    complete: function() {
                        $gstLoader.hide();
                        $gstInput.removeClass('loading');
                        $gstInput.prop('readonly', false);
                    }
                });
            });

            $('#state_code').on('input', function() {

                if (this.value.includes(' - ')) return;

                this.value = this.value.replace(/[^0-9]/g, '').substring(0, 3);

                // Auto-fill state name from code
                const code = this.value.trim();
                const name = stateCodeToName[code] || '';
                $('#state_name').val(name);

            }); 
            // Handle form submission
            $("#customerForm").submit(function(e) {
                e.preventDefault(); // Prevent default form submission

                let formData = new FormData(this);
                $(".text-danger").html(""); // Clear previous error messages

                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

                formData.append("selectedSubAdminId", selectedSubAdminId);


                const $btn = $("#submitCustomerBtn"); // Replace with your actual button ID
                const originalText = $btn.html();

                // Show spinner and disable button
                $btn.html(
                        '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...'
                        )
                    .prop("disabled", true);

                $.ajax({
                    url: "/api/createCustomer", // Ensure API route is correct
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        if (response.status) {
                            // If we have a face embedding, register it before redirecting
                            let faceEmbedding = $("#face_embedding").val();
                            if (faceEmbedding && response.customer_id) {
                                $.ajax({
                                    url: '/api/customers/face-register',
                                    method: 'POST',
                                    data: {
                                        customer_id: response.customer_id,
                                        embedding: JSON.parse(faceEmbedding)
                                    },
                                    headers: {
                                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                                        "Authorization": "Bearer " + authToken,
                                    },
                                    success: function() {
                                        redirectSuccess();
                                    },
                                    error: function() {
                                        redirectSuccess(); // Still redirect even if register fails
                                    }
                                });
                            } else {
                                redirectSuccess();
                            }

                            function redirectSuccess() {
                                $btn.html(originalText).prop("disabled", false);
                                Swal.fire({
                                    title: "Success!",
                                    text: "Customer added successfully!",
                                    icon: "success",
                                    confirmButtonText: "OK",
                                    confirmButtonColor: "#ff9f43",
                                }).then(() => {
                                    window.location.href = "/customer";
                                });
                            }
                        } else {
                            $btn.html(originalText).prop("disabled", false);
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

            // Remove error message when user starts typing
            $("input, select").on("input", function() {
                let fieldName = $(this).attr("name");
                $(".error-" + fieldName).html("");
            });

            // Handle File Input Change (Live Preview)
            // $("#avatar").change(function(event) {
            //     let reader = new FileReader();
            //     reader.onload = function(e) {
            //         $("#avatar-preview").attr("src", e.target.result);
            //         $("#avatar-preview-container").show();
            //     };
            //     reader.readAsDataURL(event.target.files[0]);
            // });

            $("#avatar").change(function(event) {

                let file = event.target.files[0];

                if (!file) return;

                const allowedTypes = [
                    "image/jpeg",
                    "image/png",
                    "image/jpg",
                    "image/webp",
                    "image/gif"
                ];

                // Validate file type
                if (!allowedTypes.includes(file.type)) {
                    $(".error-avatar").html("Only image files (JPG, PNG, WEBP, GIF) are allowed.");
                    $(this).val("");
                    $("#avatar-preview-container").hide();
                    return;
                }

                $(".error-avatar").html("");

                // Preview Image
                let reader = new FileReader();
                reader.onload = function(e) {
                    $("#avatar-preview").attr("src", e.target.result);
                    $("#avatar-preview-container").show();
                };

                reader.readAsDataURL(file);
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

            // Face Recognition Callback from face-scan-modal component
            window.onFaceCaptured = function(embeddingArray, faceDataUrl) {
                $.ajax({
                    url: '/api/customers/face-match',
                    method: 'POST',
                    data: { embedding: embeddingArray },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    success: function(res) {
                        if (res.match && res.customer) {
                            Swal.fire({
                                title: "Customer Found!",
                                text: `Match found for ${res.customer.first_name} ${res.customer.last_name} (${res.customer.customer_code})`,
                                icon: "success",
                                showCancelButton: true,
                                confirmButtonText: "Edit Customer",
                                cancelButtonText: "Cancel"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = `/edit-customer/${res.customer.id}`;
                                }
                            });
                        } else {
                            $("#face_embedding").val(JSON.stringify(embeddingArray));
                            
                            // Set the image preview
                            $("#avatar-preview").attr("src", faceDataUrl);
                            $("#avatar-preview-container").show();
                            
                            // Create a File object and set it to the avatar input
                            fetch(faceDataUrl)
                                .then(res => res.blob())
                                .then(blob => {
                                    const file = new File([blob], "scanned_face.jpg", { type: "image/jpeg" });
                                    const dataTransfer = new DataTransfer();
                                    dataTransfer.items.add(file);
                                    const avatarInput = document.getElementById('avatar');
                                    avatarInput.files = dataTransfer.files;
                                    // Trigger the change event so the standard preview logic runs
                                    $(avatarInput).trigger('change');
                                });
                            
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                            });
                            Toast.fire({
                                icon: 'info',
                                title: 'Face recorded for new customer'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire("Error", "Face matching failed.", "error");
                    }
                });
            };


        });
    </script>
@endpush


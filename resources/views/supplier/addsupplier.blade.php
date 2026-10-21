@extends('layout.app')

@section('title', 'Add Vendor')

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
            <h4>Add Vendor</h4>
        </div>
    </div> --}}
        <div class="page-header ">
            <div class="page-title">
                <h4>Add Vendor</h4>
            </div>
            <div class="back-button">
                <a href="{{ route('vendor.list') }}" class="btn back-button"> <i class="fa-solid fa-arrow-left"></i>
                    Back</a></br>
                <span class="success_submit text-danger"></span>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form id="customerForm">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Vendor Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" maxlength="80" id="supplier_name" class="form-control">
                                <span class="text-danger error-name"></span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Company Name </label>
                                <input type="text" name="company_name" maxlength="80" id="company_name" class="form-control">
                                <span class="text-danger error-company_name"></span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Email </label>
                                <input type="email" name="email" id="email" class="form-control">
                                <span class="text-danger error-email"></span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" c id="phone" class="form-control">
                                <span class="text-danger error-phone"></span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Country</label>
                                <input type="text" name="country" id="country" class="form-control">
                                <span class="text-danger error-country"></span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>City</label>
                                <input type="text" name="city" id="city" class="form-control">
                                <span class="text-danger error-city"></span>
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
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>PAN Number</label>
                                <input type="text" name="pan_number" maxlength="10" id="pan_number" class="form-control">
                                <span class="text-danger error-pan_number"></span>
                            </div>
                        </div>
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
                        <div class="col-lg-3 col-12">
                            <div class="form-group">
                                <label>Address</label>
                                <textarea name="address" id="address" class="form-control"></textarea>
                                <span class="text-danger error-address"></span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-12">
                            <div class="form-group">
                                <label>Address Line 2</label>
                                <textarea name="address_line2" id="address_line2" class="form-control"></textarea>
                                <span class="text-danger error-address_line2"></span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-12">
                            <div class="form-group">
                                <label>Address Line 3</label>
                                <textarea name="address_line3" id="address_line3" class="form-control"></textarea>
                                <span class="text-danger error-address_line3"></span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Pin Code</label>
                                <input type="text" name="pin_code" id="pin_code" class="form-control">
                                <span class="text-danger error-pin_code"></span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Phone 2</label>
                                <input type="text" name="phone_2" id="phone_2" class="form-control">
                                <span class="text-danger error-phone_2"></span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Supplier Since</label>
                                <input type="date" name="supplier_since" id="supplier_since" class="form-control">
                                <span class="text-danger error-supplier_since"></span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Supplier Category</label>
                                <input type="text" name="supplier_category" id="supplier_category" class="form-control">
                                <span class="text-danger error-supplier_category"></span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>TIN Number</label>
                                <input type="text" name="tin_number" id="tin_number" class="form-control">
                                <span class="text-danger error-tin_number"></span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Credit Days</label>
                                <input type="number" name="credit_days" id="credit_days" class="form-control">
                                <span class="text-danger error-credit_days"></span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>PAN Status</label>
                                <input type="text" name="pan_status" id="pan_status" class="form-control">
                                <span class="text-danger error-pan_status"></span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>GSTIN Status</label>
                                <input type="text" name="gstin_status" id="gstin_status" class="form-control">
                                <span class="text-danger error-gstin_status"></span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Account Group</label>
                                <input type="text" name="account_group" id="account_group" class="form-control">
                                <span class="text-danger error-account_group"></span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>TAN Status</label>
                                <input type="text" name="tan_status" id="tan_status" class="form-control">
                                <span class="text-danger error-tan_status"></span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>TAN Number</label>
                                <input type="text" name="tan_number" id="tan_number" class="form-control">
                                <span class="text-danger error-tan_number"></span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>MSME Status</label>
                                <input type="text" name="msme_status" id="msme_status" class="form-control">
                                <span class="text-danger error-msme_status"></span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>MSME Number</label>
                                <input type="text" name="msme_number" id="msme_number" class="form-control">
                                <span class="text-danger error-msme_number"></span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control" name="status" id="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <span class="text-danger error-status"></span>
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Photo</label>
                                <div class="image-upload">
                                    <input type="file" name="avatar" id="avatar-input" class="form-control"
                                        accept="image/*">
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
                            <button type="submit" id="submitButton" class="btn btn-submit me-2">Submit</button>
                            <a href="{{ route('vendor.list') }}" class="btn btn-cancel">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            let $gstLoader = $('#gst-loader');

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
                        $('#supplier_name').val(res.legal_name || '');
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

                // Auto-fill state name
                const padded = this.value.trim().padStart(2, '0');
                $('#state_name').val(stateCodeToName[padded] || stateCodeToName[this.value.trim()] || '');

            });
            // $('#state_code').on('input', function() {
            //     this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
            // });

            $("#phone").on("input", function() {

                let value = $(this).val().replace(/\D/g, '');

                if (value.length > 10) {
                    value = value.substring(0, 10);
                }

                $(this).val(value);

            });

            $("#supplier_name").on("input", function() {

                let value = $(this).val();

                if (value.length > 80) {
                    $(this).val(value.substring(0, 80));
                }

            });

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

            // Handle form submission
            $("#customerForm").submit(function(e) {
                e.preventDefault(); // Prevent default form submission

                // Target the submit button
                const $btn = $("#submitButton"); // Make sure your submit button has id="submitButton"
                const originalText = $btn.html();


                // Show loader and disable button
                $btn.html(
                        '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...'
                    )
                    .prop("disabled", true);

                let formData = new FormData(this);
                $(".text-danger").html(""); // Clear previous error messages
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

                formData.append("selectedSubAdminId", selectedSubAdminId);

                $.ajax({
                    url: "/api/createSupplier", // Ensure API route is correct
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        $btn.html(originalText).prop("disabled", false); // Restore button
                        if (response.status) {

                            Swal.fire({
                                title: "Success!",
                                text: "Vendor added successfully!",
                                icon: "success",
                                confirmButtonText: "OK",
                                confirmButtonColor: "#ff9f43",
                            }).then(() => {
                                window.location.href = "/vendors";
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
                            Swal.fire("Error!", "Something went wrong. Please try again.",
                                "error");
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
            $("#avatar-input").change(function(event) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $("#avatar-preview").attr("src", e.target.result);
                    $("#avatar-preview-container").show();
                };
                reader.readAsDataURL(event.target.files[0]);
            });
        });
    </script>
@endpush

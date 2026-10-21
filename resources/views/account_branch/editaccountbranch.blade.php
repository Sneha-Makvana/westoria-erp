@extends('layout.app')

@section('title', 'Edit Account Branch')

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
        .form-label-icon {
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
            margin-bottom: 6px;
        }
        .form-label-icon i {
            color: #ff9f43;
            font-size: 13px;
            width: 14px;
            text-align: center;
        }
        .form-label-icon .required {
            color: #dc3545;
            margin-left: 2px;
        }
    </style>

    <div class="content">
        <div class="page-header ">
            <div class="page-title">
                <h4>Edit Account Branch</h4>
            </div>
             <div class="back-button">
                <a href="{{ route('account_branch.list') }}" class="btn back-button"> <i class="fa-solid fa-arrow-left"></i> Back</a><br>
                <span class="success_submit text-danger"></span>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form id="account_branchForm">
                    <input type="hidden" name="id" id="account_branch_id" value="{{ $id }}">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-barcode"></i> Branch Code</label>
                                <input type="text" name="branch_code" id="branch_code" class="form-control">
                                <span class="error_branch_code text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-building"></i> Branch Name <span class="required">*</span></label>
                                <input type="text" name="name" id="name" class="form-control">
                                <span class="error_name text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-industry"></i> Branch Company Name</label>
                                <input type="text" name="branch_company_name" id="branch_company_name" class="form-control">
                                <span class="error_branch_company_name text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-envelope"></i> Email Id</label>
                                <input type="email" name="email" id="email" class="form-control">
                                <span class="error_email text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-phone"></i> Phone #1</label>
                                <input type="text" name="phone" id="phone" class="form-control">
                                <span class="error_phone text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-phone"></i> Phone #2</label>
                                <input type="text" name="phone_2" id="phone_2" class="form-control">
                                <span class="error_phone_2 text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-map-marker-alt"></i> Address Line1</label>
                                <textarea name="address" id="address" class="form-control" rows="2"></textarea>
                                <span class="error_address text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-map-marker-alt"></i> Address Line2</label>
                                <textarea name="address_line_2" id="address_line_2" class="form-control" rows="2"></textarea>
                                <span class="error_address_line_2 text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-map-marker-alt"></i> Address Line3</label>
                                <textarea name="address_line_3" id="address_line_3" class="form-control" rows="2"></textarea>
                                <span class="error_address_line_3 text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-mail-bulk"></i> Pin Code</label>
                                <input type="text" name="zip_code" id="zip_code" class="form-control">
                                <span class="error_zip_code text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-city"></i> City/Town</label>
                                <input type="text" name="city" id="city" class="form-control">
                                <span class="error_city text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-map-pin"></i> Area</label>
                                <input type="text" name="area" id="area" class="form-control">
                                <span class="error_area text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-map"></i> State</label>
                                <input type="text" name="state" id="state" class="form-control">
                                <span class="error_state text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-file-invoice"></i> TIN</label>
                                <input type="text" name="tin" id="tin" class="form-control">
                                <span class="error_tin text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-file-invoice-dollar"></i> GSTIN</label>
                                <input type="text" name="gstin" id="gstin" class="form-control">
                                <span class="error_gstin text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-id-card"></i> PAN</label>
                                <input type="text" name="pan" id="pan" class="form-control">
                                <span class="error_pan text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-regular fa-calendar"></i> Opened On</label>
                                <input type="date" name="opened_on" id="opened_on" class="form-control">
                                <span class="error_opened_on text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-regular fa-calendar-times"></i> Closed On</label>
                                <input type="date" name="closed_on" id="closed_on" class="form-control">
                                <span class="error_closed_on text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-code-branch"></i> Branch Type</label>
                                <input type="text" name="branch_type" id="branch_type" class="form-control">
                                <span class="error_branch_type text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-toggle-on"></i> Status</label>
                                <select class="select" name="status" id="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">InActive</option>
                                </select>
                                <span class="error_status text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <a href="javascript:void(0);" class="btn btn-submit me-2 submit">Update</a>
                            <a href="{{ route('account_branch.list') }}" class="btn btn-cancel">Cancel</a><br>
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
            $('.select').select2();
            var authToken = localStorage.getItem("authToken");
            var branchId = $('#account_branch_id').val();

            // Fetch Data
            $.ajax({
                url: `/api/getAccountBranchById/${branchId}`,
                type: "GET",
                headers: {
                    "Authorization": "Bearer " + authToken,
                },
                success: function(response) {
                    if (response.status) {
                        let data = response.account_branch || response.data;
                        $('#branch_code').val(data.branch_code);
                        $('#name').val(data.name);
                        $('#branch_company_name').val(data.branch_company_name);
                        $('#email').val(data.email);
                        $('#phone').val(data.phone);
                        $('#phone_2').val(data.phone_2);
                        $('#address').val(data.address);
                        $('#address_line_2').val(data.address_line_2);
                        $('#address_line_3').val(data.address_line_3);
                        $('#city').val(data.city);
                        $('#area').val(data.area);
                        $('#state').val(data.state);
                        $('#zip_code').val(data.zip_code);
                        $('#tin').val(data.tin);
                        $('#gstin').val(data.gstin);
                        $('#pan').val(data.pan);
                        $('#opened_on').val(data.opened_on);
                        $('#closed_on').val(data.closed_on);
                        $('#branch_type').val(data.branch_type);
                        $('#status').val(data.status).trigger('change');
                    }
                },
                error: function(xhr) {
                    console.error("Error fetching data", xhr);
                }
            });

            $(document).on('click', '.submit', function(e) {
                e.preventDefault();
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                var $btn = $(this);
                var originalText = $btn.html();

                $btn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Updating...').prop('disabled', true);

                let formData = new FormData($('#account_branchForm')[0]);
                formData.append('id', branchId);

                if (selectedSubAdminId) {
                    formData.append("sub_admin_id", selectedSubAdminId);
                }

                $('[class^="error_"]').text('');

                $.ajax({
                    url: "/api/updateAccountBranch",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        $btn.html(originalText).prop('disabled', false);

                        if (response.status) {
                            Swal.fire({
                                title: "Success",
                                text: "Account Branch updated successfully",
                                icon: "success",
                                confirmButtonText: "OK",
                                confirmButtonColor: "#ff9f43"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "{{ route('account_branch.list') }}";
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        $btn.html(originalText).prop('disabled', false);

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $('[class^="error_"]').text('');

                            $.each(errors, function(key, value) {
                                let errorKey = key.split('.')[0];
                                let errorMsg = value.join(' ');
                                $('.error_' + errorKey).text(errorMsg);
                            });
                        } else {
                            Swal.fire("Error", "Something went wrong", "error");
                        }
                    }
                });
            });
        });
    </script>
@endpush

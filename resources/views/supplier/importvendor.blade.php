@extends('layout.app')

@section('title', 'Import Vendor')

@section('content')
<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Import Vendors</h4>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="page-header">
                <div class="requiredfield">
                    <h4>Upload CSV File</h4>
                </div>
                <div class="page-btn">
                    <a href="{{ route('vendor.import.sample') }}" class="btn btn-submit w-100">Download Sample File</a>
                </div>
            </div>

            <div class="row">
                <!-- Left Column: Upload & Download -->
                <div class="col-lg-6 col-sm-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <div class="image-upload">
                                    <input type="file" name="csv_file" id="csv_file">
                                    <div class="image-uploads">
                                        <img src="{{ env('ImagePath').'admin/assets/img/icons/upload.svg'}}" alt="upload icon">
                                        <h4 class="upload-message">Drag and drop a file to upload</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Submit & Cancel -->
                        <div class="col-lg-12 text-end">
                            <div class="form-group mb-3">
                                <a href="javascript:void(0);" class="btn btn-submit submit me-2">Submit</a>
                                <a href="{{ route('vendor.list') }}" class="btn btn-cancel">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Field Details -->
                <div class="col-lg-6 col-sm-12">
                    <div class="row">
                        <!-- Required Fields -->
                        <div class="col-lg-12">
                            <div class="productdetails productdetailnew mb-3">
                                <ul class="product-bar">
                                    <li>
                                        <h4>Supplier Name</h4>
                                        <h6 class="manitorygreen">This Field is required</h6>
                                    </li>
                                    <li>
                                        <h4>Phone #1</h4>
                                        <h6 class="manitorygreen">This Field is required (10 digits)</h6>
                                    </li>
                                    <li>
                                        <h4>Email id</h4>
                                        <h6 class="manitoryblue">Field optional (must be unique)</h6>
                                    </li>
                                    <li>
                                        <h4>State</h4>
                                        <h6 class="manitoryblue">Field optional (e.g. Gujarat, Maharashtra)</h6>
                                    </li>
                                    <li>
                                        <h4>Supplier Category</h4>
                                        <h6 class="manitoryblue">Field optional</h6>
                                    </li>
                                    <li>
                                        <h4>PAN / GSTIN</h4>
                                        <h6 class="manitoryblue">Field optional</h6>
                                    </li>
                                </ul>
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
        $(document).on('click', '.submit', function(e) {
            e.preventDefault();

            var authToken = localStorage.getItem("authToken");
            var csv_file = $('#csv_file')[0].files[0];

            if (!csv_file) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please select a CSV file.',
                    confirmButtonColor: '#ff9f43',
                    confirmButtonText: 'OK'
                });
                return;
            }

            var formData = new FormData();
            formData.append("csv_file", csv_file);

            // Change button to loading state
            var $btn = $(this);
            var originalText = $btn.text();
            $btn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Importing...').addClass('disabled');

            $.ajax({
                url: "/api/importVendors",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    "Authorization": "Bearer " + authToken,
                },
                success: function(response) {
                    $btn.html(originalText).removeClass('disabled');
                    if (response.status) {
                        let messageParts = [];
                        if (response.message) {
                            messageParts.push(`<strong>${response.message}</strong>`);
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Import Summary',
                            html: messageParts.join('<br><br>'),
                            confirmButtonColor: '#ff9f43',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = "{{ route('vendor.list') }}";
                        });

                    } else {
                        let message = response.message || "Failed to import vendors.";

                        if (response.invalid_skus && response.invalid_skus.length > 0) {
                            message += `<br><br><strong>Errors:</strong><br>${response.invalid_skus.join('<br>')}`;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Import Failed',
                            html: message,
                            confirmButtonColor: '#ff9f43',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr) {
                    $btn.html(originalText).removeClass('disabled');
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorList = "";
                        $.each(errors, function(key, value) {
                            errorList += value[0] + "\n";
                        });

                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: errorList,
                            confirmButtonColor: '#ff9f43',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An unexpected error occurred.',
                            confirmButtonColor: '#ff9f43',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        });
    });
</script>
<script>
    document.getElementById('csv_file').addEventListener('change', function() {
        const fileName = this.files[0]?.name;
        const messageDiv = document.querySelector('.upload-message');
        if (fileName) {
            messageDiv.textContent = fileName;
        } else {
            messageDiv.textContent = 'Drag and drop a file to upload';
        }
    });
</script>
@endpush

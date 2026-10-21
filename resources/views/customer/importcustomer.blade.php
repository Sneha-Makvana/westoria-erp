@extends('layout.app')

@section('title', 'Import Customers')

@section('content')
<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Import Customers</h4>
            <h6>Upload CSV, XLS, or XLSX files with customer details.</h6>
        </div>
        <div class="back-button">
            <a href="{{ route('customer.list') }}" class="btn btn-cancel">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="customerImportForm" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-lg-10 col-md-12 col-12">
                        <div class="alert alert-light border mb-3">
                            <strong>Supported columns:</strong>
                            S.No, Customer Name, Membership #, Customer Since, Address Line1, Address Line2,
                            Address Line3, Pin Code, City/Town, State, Phone #1, Phone #, Email Id, Category,
                            Age, DOB, DOA, PAN, TIN, GSTIN Status, GSTIN, Gender, Status
                        </div>
                    </div>

                    <div class="col-lg-8 col-md-10 col-12">
                        <div class="form-group">
                            <label>Upload Customer File <span class="text-danger">*</span></label>
                            <div class="image-upload">
                                <input type="file" id="customerImportFile" name="file" accept=".csv,.xls,.xlsx" required>
                                <div class="image-uploads">
                                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/upload.svg' }}" alt="Upload Icon">
                                    <h4 id="customerImportFileName">Drag and drop a file to upload</h4>
                                </div>
                            </div>
                            <div class="text-danger mt-1" id="customerImportFileError"></div>
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-submit me-2" id="customerImportSubmit">
                            Import
                        </button>
                        <a href="{{ route('customer.list') }}" class="btn btn-cancel">Cancel</a>
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
    $('#customerImportFile').on('change', function() {
        const fileName = this.files && this.files.length ? this.files[0].name : 'Drag and drop a file to upload';
        $('#customerImportFileName').text(fileName);
        $('#customerImportFileError').text('');
    });

    $('#customerImportForm').on('submit', function(e) {
        e.preventDefault();

        const fileInput = $('#customerImportFile')[0];
        if (!fileInput.files.length) {
            $('#customerImportFileError').text('Please select a CSV or Excel file.');
            return;
        }

        const $button = $('#customerImportSubmit');
        const originalHtml = $button.html();
        const formData = new FormData(this);
        formData.append('selectedSubAdminId', localStorage.getItem('selectedSubAdminId') || '');

        $button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Importing...');
        $('#customerImportFileError').text('');

        $.ajax({
            url: '{{ route('customer.import-list') }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                const errors = response.errors && response.errors.length
                    ? '<br><small>' + response.errors.slice(0, 5).join('<br>') + '</small>'
                    : '';

                Swal.fire({
                    title: 'Import Complete',
                    html: `Processed: ${response.processed || 0}<br>Created: ${response.created || 0}<br>Updated: ${response.updated || 0}<br>Unchanged: ${response.unchanged || 0}<br>Skipped: ${response.skipped || 0}${errors}`,
                    icon: 'success',
                    confirmButtonColor: '#ff9f43'
                }).then(function() {
                    window.location.href = '{{ route('customer.list') }}';
                });
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Import failed. Please check the file and try again.';
                $('#customerImportFileError').text(message);
                Swal.fire('Import Failed', message, 'error');
            },
            complete: function() {
                $button.prop('disabled', false).html(originalHtml);
            }
        });
    });
});
</script>
@endpush

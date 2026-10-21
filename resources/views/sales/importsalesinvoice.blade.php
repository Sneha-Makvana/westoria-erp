@extends('layout.app')

@section('title', 'Import Sales Invoice List')

@section('content')
<style>
    /* Alert-light box - Dark theme */
    :root[data-theme="dark"] .alert-light {
        background-color: #0f172a !important;
        border-color: rgba(255, 255, 255, 0.14) !important;
        color: #cbd5e1 !important;
    }

    :root[data-theme="dark"] .alert-light strong {
        color: #f8fafc !important;
    }
</style>
<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Import Sales Invoice List</h4>
            <h6>Upload CSV, XLS, or XLSX files with Party Name and document details.</h6>
        </div>
        <div class="back-button">
            <a href="{{ route('sales.list') }}" class="btn btn-cancel">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="salesImportForm" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-lg-8 col-md-10 col-12">
                        <div class="alert alert-light border mb-3">
                            <strong>Required columns:</strong>
                            SN, Party Name, Doc No, Doc.Date, Doc.Value, Financier, Status, DO Status, Item/Model Code, Item/Model, Qty., Rate, Amount
                        </div>
                    </div>

                    <div class="col-lg-8 col-md-10 col-12">
                        <div class="form-group">
                            <label>Upload Sales Invoice List File <span class="text-danger">*</span></label>
                            <div class="image-upload">
                                <input type="file" id="salesImportFile" name="file" accept=".csv,.xls,.xlsx" required>
                                <div class="image-uploads">
                                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/upload.svg' }}" alt="Upload Icon">
                                    <h4 id="salesImportFileName">Drag and drop a file to upload</h4>
                                </div>
                            </div>
                            <div class="text-danger mt-1" id="salesImportFileError"></div>
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-submit me-2" id="salesImportSubmit">
                            Import
                        </button>
                        <a href="{{ route('sales.list') }}" class="btn btn-cancel">Cancel</a>
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
    $('#salesImportFile').on('change', function() {
        const fileName = this.files && this.files.length ? this.files[0].name : 'Drag and drop a file to upload';
        $('#salesImportFileName').text(fileName);
        $('#salesImportFileError').text('');
    });

    $('#salesImportForm').on('submit', function(e) {
        e.preventDefault();

        const fileInput = $('#salesImportFile')[0];
        if (!fileInput.files.length) {
            $('#salesImportFileError').text('Please select a CSV or Excel file.');
            return;
        }

        const $button = $('#salesImportSubmit');
        const originalHtml = $button.html();
        const formData = new FormData(this);
        formData.append('selectedSubAdminId', localStorage.getItem('selectedSubAdminId') || '');

        $button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Importing...');
        $('#salesImportFileError').text('');

        $.ajax({
            url: '{{ route('sales.import-list') }}',
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
                    html: `Imported: ${response.imported || 0}<br>Updated: ${response.updated || 0}<br>Skipped: ${response.skipped || 0}${errors}`,
                    icon: 'success',
                    confirmButtonColor: '#ff9f43'
                }).then(function() {
                    window.location.href = '{{ route('sales.list') }}';
                });
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Import failed. Please check the file and try again.';
                $('#salesImportFileError').text(message);
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

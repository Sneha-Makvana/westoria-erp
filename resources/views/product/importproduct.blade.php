@extends('layout.app')

@section('title', 'Import Product')

@section('content')
<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Import Products</h4>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="importUploadForm" action="{{ route('product.import.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="page-header">
                    <div class="requiredfield">
                        <h4>Upload CSV or Excel File (.csv / .xlsx)</h4>
                    </div>
                    <div class="page-btn">
                        <a href="{{ route('product.import.sample') }}" class="btn btn-submit w-100">Download Sample File</a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-sm-12">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <div class="image-upload">
                                        <input type="file" name="csv_file" id="csv_file" accept=".csv,.xlsx,.xls" required>
                                        <div class="image-uploads">
                                            <img src="{{ env('ImagePath').'admin/assets/img/icons/upload.svg'}}" alt="upload icon">
                                            <h4 class="upload-message">Drag and drop a CSV or Excel (.xlsx) file to upload</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="alert alert-light border mb-3">
                                    <strong>How to import:</strong><br>
                                    1. Select your Excel/CSV file<br>
                                    2. Click <strong>Preview Import</strong> (large files upload directly — faster for 30k+ rows)<br>
                                    3. Wait on preview page while rows are read<br>
                                    4. Uncheck rows you do not want, then click <strong>Import Selected</strong>
                                </div>
                            </div>

                            @if ($errors->any())
                                <div class="col-lg-12">
                                    <div class="alert alert-danger">
                                        {{ $errors->first() }}
                                    </div>
                                </div>
                            @endif

                            <div class="col-lg-12 text-end">
                                <div class="form-group mb-3">
                                    <button type="submit" class="btn btn-submit me-2" id="previewImportBtn">Preview Import</button>
                                    <a href="{{ route('product.list') }}" class="btn btn-cancel">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-sm-12">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="productdetails productdetailnew mb-3">
                                    <ul class="product-bar">
                                        <li>
                                            <h4>Item name / ITEMS</h4>
                                            <h6 class="manitorygreen">Product name — "Item name" or "ITEMS"</h6>
                                        </li>
                                        <li>
                                            <h4>Item code (Barcode)</h4>
                                            <h6 class="manitorygreen">Excel: "Item code" — used as barcode</h6>
                                        </li>
                                        <li>
                                            <h4>Description / Wholesale</h4>
                                            <h6 class="manitorygreen">Description starting with 11 (e.g. 11243) or a Wholesale column is saved as wholesale rate</h6>
                                        </li>
                                        <li>
                                            <h4>Category / Prices / Stock</h4>
                                            <h6 class="manitoryblue">Category, HSN, Default MRP, Sale price, Purchase rate, Opening stock, Tax, Unit</h6>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
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
        var MAX_IMPORT_FILE_BYTES = 50 * 1024 * 1024;

        @if(session('import_error'))
            Swal.fire({
                icon: 'error',
                title: 'Preview Unavailable',
                text: @json(session('import_error')),
                confirmButtonColor: '#ff9f43'
            });
        @endif

        $('#csv_file').on('change', function() {
            var file = this.files[0];
            var messageDiv = document.querySelector('.upload-message');
            if (!file) {
                if (messageDiv) messageDiv.textContent = 'Drag and drop a CSV or Excel (.xlsx) file to upload';
                return;
            }
            if (messageDiv) messageDiv.textContent = file.name;
        });

        $('#importUploadForm').on('submit', function(e) {
            var file = $('#csv_file')[0].files[0];
            if (!file) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'No File Selected',
                    text: 'Please select a CSV or Excel file first.',
                    confirmButtonColor: '#ff9f43'
                });
                return;
            }

            if (file.size > MAX_IMPORT_FILE_BYTES) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'Maximum allowed size is 50 MB.',
                    confirmButtonColor: '#ff9f43'
                });
                return;
            }

            var sizeMb = (file.size / (1024 * 1024)).toFixed(2);
            $('#previewImportBtn').prop('disabled', true).text('Uploading ' + sizeMb + ' MB...');
        });
    });
</script>
@endpush

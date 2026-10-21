@extends('layout.app')

@section('title', 'GST Workbook Validator')

@section('content')
<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>GST Workbook Validator</h4>
            <h6>Upload a GST Excel workbook to validate tax calculations across all sheets.</h6>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('gst.validation.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-bold" for="gst_workbook">GST Excel Workbook (.xlsx)</label>
                        <input
                            type="file"
                            name="gst_workbook"
                            id="gst_workbook"
                            class="form-control"
                            accept=".xlsx,.xls"
                            required
                        >
                        <small class="text-muted d-block mt-2">
                            The validator reads all sheets, detects headers automatically, and checks IGST/CGST/SGST/Cess totals with a 0.50 tolerance.
                        </small>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        Validate Workbook
                    </button>
                    <a href="{{ route('gst.sales_list') }}" class="btn btn-light">Back</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

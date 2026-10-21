@extends('layout.app')

@section('title', 'GST Validation Result')

@section('content')
@php
    $summary = $result['summary'] ?? [];
    $money = fn($value) => number_format((float) $value, 2);
@endphp

<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>GST Validation Result</h4>
            <h6>{{ $result['file_name'] ?? 'Uploaded workbook' }}</h6>
        </div>
        <div class="page-btn">
            <a href="{{ route('gst.validation.download', $result['id']) }}" class="btn btn-success">
                Download Corrected Excel
            </a>
            <a href="{{ route('gst.validation.create') }}" class="btn btn-primary">
                Validate Another
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="dash-widget">
                <div class="dash-widgetcontent">
                    <h5>{{ $summary['total_sheets'] ?? 0 }}</h5>
                    <h6>Total Sheets</h6>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="dash-widget">
                <div class="dash-widgetcontent">
                    <h5>{{ $summary['total_rows'] ?? 0 }}</h5>
                    <h6>Validated Rows</h6>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="dash-widget">
                <div class="dash-widgetcontent">
                    <h5 class="text-success">{{ $summary['total_correct_rows'] ?? 0 }}</h5>
                    <h6>Correct Rows</h6>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="dash-widget">
                <div class="dash-widgetcontent">
                    <h5 class="text-danger">{{ $summary['total_incorrect_rows'] ?? 0 }}</h5>
                    <h6>Incorrect Rows</h6>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">Summary</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th>Total HSN</th>
                            <td>{{ $summary['total_hsn'] ?? 0 }}</td>
                            <th>Total Taxable Value</th>
                            <td>{{ $money($summary['total_taxable_value'] ?? 0) }}</td>
                        </tr>
                        <tr>
                            <th>Total CGST</th>
                            <td>{{ $money($summary['total_cgst'] ?? 0) }}</td>
                            <th>Total SGST</th>
                            <td>{{ $money($summary['total_sgst'] ?? 0) }}</td>
                        </tr>
                        <tr>
                            <th>Total IGST</th>
                            <td>{{ $money($summary['total_igst'] ?? 0) }}</td>
                            <th>Total Cess</th>
                            <td>{{ $money($summary['total_cess'] ?? 0) }}</td>
                        </tr>
                        <tr>
                            <th>Total GST</th>
                            <td>{{ $money($summary['total_gst'] ?? 0) }}</td>
                            <th>Total Invoice Value</th>
                            <td>{{ $money($summary['total_invoice_value'] ?? 0) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @foreach (($result['sheets'] ?? []) as $sheet)
        @php
            $sheetSummary = $sheet['summary'] ?? [];
            $rows = collect($sheet['rows'] ?? []);
            $incorrectRows = $rows->where('status', '!=', 'Correct')->take(50);
        @endphp

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="mb-1">{{ $sheet['sheet_name'] ?? 'Sheet' }}</h5>
                        <small class="text-muted">Header row: {{ $sheet['header_row'] ?? 'Not detected' }}</small>
                    </div>
                    <div>
                        <span class="badge bg-success">Correct: {{ $sheetSummary['total_correct_rows'] ?? 0 }}</span>
                        <span class="badge bg-danger">Incorrect: {{ $sheetSummary['total_incorrect_rows'] ?? 0 }}</span>
                    </div>
                </div>

                @if ($rows->isEmpty())
                    <div class="alert alert-warning mb-0">
                        No GST validation rows found on this sheet.
                    </div>
                @elseif ($incorrectRows->isEmpty())
                    <div class="alert alert-success mb-0">
                        All validated rows on this sheet are correct.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Row</th>
                                    <th>Status</th>
                                    <th>Error</th>
                                    <th>HSN</th>
                                    <th>Rate</th>
                                    <th>Taxable</th>
                                    <th>CGST</th>
                                    <th>SGST</th>
                                    <th>IGST</th>
                                    <th>Total Value</th>
                                    <th>Correct Taxable</th>
                                    <th>Correct CGST</th>
                                    <th>Correct SGST</th>
                                    <th>Correct IGST</th>
                                    <th>Correct Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($incorrectRows as $row)
                                    <tr>
                                        <td>{{ $row['row_number'] }}</td>
                                        <td><span class="badge bg-danger">{{ $row['status'] }}</span></td>
                                        <td>{{ $row['error_message'] }}</td>
                                        <td>{{ $row['hsn'] }}</td>
                                        <td>{{ $money($row['rate']) }}</td>
                                        <td>{{ $money($row['taxable_value']) }}</td>
                                        <td>{{ $money($row['cgst']) }}</td>
                                        <td>{{ $money($row['sgst']) }}</td>
                                        <td>{{ $money($row['igst']) }}</td>
                                        <td>{{ $money($row['total_value']) }}</td>
                                        <td>{{ $money($row['correct_taxable_value']) }}</td>
                                        <td>{{ $money($row['correct_cgst']) }}</td>
                                        <td>{{ $money($row['correct_sgst']) }}</td>
                                        <td>{{ $money($row['correct_igst']) }}</td>
                                        <td>{{ $money($row['correct_total_value']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <small class="text-muted">
                        Showing first 50 incorrect rows for this sheet. Download corrected Excel for full details.
                    </small>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endsection

@extends('layout.app')
@section('title', 'Incentive Calculation')
@section('content')
<style>
   .capitalize-text { text-transform: capitalize; }
   .summary-card { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; margin-bottom: 20px; text-align: center; }
   .summary-card h3 { color: #FF9F43; margin-bottom: 0; font-weight: bold; }
   .incentive-expanded-details {
       display: none;
       margin-top: 8px;
       padding: 8px 10px;
       border: 1px solid #eceff3;
       border-radius: 8px;
       background: #fafbfc;
   }
   .incentive-expanded-details.show {
       display: block;
   }
   .incentive-detail-row {
       display: flex;
       justify-content: space-between;
       align-items: center;
       gap: 8px;
       font-size: 12px;
       padding: 4px 0;
   }
   .incentive-detail-label {
       color: #6b7280;
       white-space: nowrap;
   }
   .incentive-detail-value {
       text-align: right;
       min-width: 0;
   }
   .incentive-expand-toggle {
       width: 22px;
       height: 22px;
       border: 0;
       border-radius: 50%;
       background: #ff9f43;
       color: #fff;
       padding: 0;
       font-size: 18px;
       line-height: 1;
       display: inline-flex;
       align-items: center;
       justify-content: center;
       box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
   }
   .incentive-expand-toggle span {
       display: block;
       transform: translateY(-1px);
   }
   .details-column {
       display: none;
   }
   .mobile-action-label {
       display: none;
   }
   .incentive-toolbar {
       display: flex;
       justify-content: space-between;
       align-items: center;
       gap: 12px;
   }
   .incentive-toolbar-actions {
       display: flex;
       align-items: center;
       gap: 12px;
       flex-wrap: wrap;
       justify-content: flex-end;
   }
   .incentive-summary-text {
       font-size: 16px;
   }

   @media (max-width: 576px) {
       .incentive-toolbar {
           flex-direction: column;
           align-items: stretch;
           flex-wrap: nowrap;
           gap: 8px;
       }
       .incentive-toolbar > .card-title {
           width: 100%;
           font-size: 12px;
           line-height: 1.2;
           margin-right: 0;
       }
       .incentive-toolbar-actions {
           width: 100%;
           display: grid;
           grid-template-columns: 1fr;
           gap: 6px;
           justify-content: stretch;
       }
       .incentive-toolbar-actions .form-control,
       .incentive-toolbar-actions .btn,
       .incentive-toolbar-actions a {
           width: 100%;
           min-width: 0;
       }
       .incentive-toolbar-actions .form-control {
           max-width: 100%;
           font-size: 11px;
           padding-left: 6px;
           padding-right: 4px;
       }
       .incentive-toolbar-actions .action-row {
           display: grid;
           grid-template-columns: repeat(2, minmax(0, 1fr));
           gap: 6px;
       }
       .incentive-toolbar-actions .action-row .btn,
       .incentive-toolbar-actions .action-row a {
           justify-content: center;
           padding-left: 8px;
           padding-right: 8px;
           font-size: 11px;
       }
       .incentive-summary-text {
           font-size: 13px;
       }
       .summary-card {
           padding: 12px 10px;
           margin-bottom: 14px;
       }
       .summary-card h3 {
           font-size: 20px;
       }
       .col-sales,
       .col-percent,
       .col-amount,
       .col-action {
           display: none !important;
       }
       .details-column {
           display: table-cell !important;
           width: 52px !important;
           min-width: 52px !important;
           max-width: 52px !important;
           text-align: center;
           vertical-align: top;
           padding: 12px 4px !important;
       }
       .col-main {
           display: table-cell !important;
           vertical-align: top;
       }
       #incentive-table tbody td.col-main {
           max-width: calc(100vw - 120px) !important;
       }
       #incentive-table td.col-main .d-flex {
           align-items: flex-start !important;
       }
       #incentive-table td.col-main img {
           width: 26px !important;
           height: 26px !important;
       }
       #incentive-table td.col-main > .d-flex > div > div:first-child {
           font-size: 11px;
           line-height: 1.15;
       }
       #incentive-table td.col-action {
           display: none !important;
       }
       .desktop-details-label {
           display: none;
       }
       .mobile-action-label {
           display: inline;
       }
       #incentive-table thead th,
       #incentive-table tbody td {
           font-size: 12px;
           vertical-align: middle;
       }
       #incentive-table thead th.col-action {
           display: none !important;
       }
       #incentive-table .input-group {
           width: 100% !important;
           max-width: 100%;
       }
   }
</style>

<div class="row">
  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        
        <div class="incentive-toolbar mb-4">
            <h4 class="card-title mb-0">Incentive Calculation</h4>
            <div class="incentive-toolbar-actions">
                <input type="month" id="salaryMonth" name="salaryMonth" class="form-control form-control-sm"
                    value="{{ $month }}" />
                <div class="action-row">
                    <button type="button" class="btn btn-primary" id="saveAllBtn" style="white-space: nowrap;">
                        {{ collect($employees)->contains('is_saved', true) ? 'Update All' : 'Save All' }}
                    </button>
                    <a href="/payrollview" class="btn btn-primary" style="white-space: nowrap;">
                        <i class="fa fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <div class="summary-card">
            <p class="mb-1 text-muted incentive-summary-text">Total Incentive Payout for {{ date('F Y', strtotime($month)) }}</p>
            <h3>₹{{ number_format($totalIncentivePayout, 2) }}</h3>
        </div>

        <form id="incentiveForm">
        @csrf
        <input type="hidden" name="month" value="{{ $month }}">
        <div class="table-responsive">
            <table class="table table-striped" id="incentive-table">
              <thead>
                <tr>
                  <th>Staff Name</th>
                  <th>Designation</th>
                  <th class="col-sales" style="width: 15%;">Individual Total Sales (?)</th>
                  <th class="col-percent" style="width: 15%;">Incentive Percentage (%)</th>
                  <th class="col-amount" style="width: 15%;">Calculated Incentive (?)</th>
                  <th class="col-details details-column">
                      <span class="desktop-details-label">Details</span>
                      <span class="mobile-action-label">Action</span>
                  </th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($employees as $emp)
                <tr>
                    <td class="capitalize-text col-main">
                        <div class="d-flex align-items-center gap-2">
                            @if (!empty($emp['profile_image']))
                                <img src="{{ env('ImagePath') }}/storage/{{ $emp['profile_image'] }}" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                            @else
                                <img src="{{ env('ImagePath') }}/admin/assets/img/customer/customer5.jpg" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                            @endif
                            <div>
                                <div>{{ $emp['firstname'] }}</div>
                                <div class="d-md-none text-muted" style="font-size: 10px; line-height: 1.1;">{{ $emp['designation'] }}</div>
                                <div class="incentive-expanded-details d-md-none" id="incentive-details-{{ $emp['id'] }}">
                                    <div class="incentive-detail-row">
                                        <span class="incentive-detail-label">Sales</span>
                                        <span class="incentive-detail-value">₹{{ number_format($emp['total_sales'], 2) }}</span>
                                    </div>
                                    <div class="incentive-detail-row">
                                        <span class="incentive-detail-label">Incentive %</span>
                                        <span class="incentive-detail-value">{{ number_format($emp['incentive_percentage'], 2) }}%</span>
                                    </div>
                                    <div class="incentive-detail-row">
                                        <span class="incentive-detail-label">Incentive</span>
                                        <span class="incentive-detail-value" style="color:#28a745; font-weight:700;">₹{{ number_format($emp['incentive_amount'], 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="col-main">{{ $emp['designation'] }}</td>
                    <td class="col-sales">
                        <input type="hidden" name="employee_id[]" value="{{ $emp['id'] }}">
                        <div class="input-group input-group-sm" style="width: 130px; margin: 0 auto;">
                            <span class="input-group-text px-2">₹</span>
                            <input type="number" step="0.01" class="form-control total-sales-input text-end" name="total_sales[]" value="{{ $emp['total_sales'] }}" style="padding: 0.25rem 0.5rem;">
                        </div>
                    </td>
                    <td class="col-percent">
                        <div class="input-group input-group-sm" style="width: 90px; margin: 0 auto;">
                            <input type="number" step="0.01" class="form-control incentive-percent-input text-end" name="incentive_percentage[]" value="{{ $emp['incentive_percentage'] }}" style="padding: 0.25rem 0.5rem;">
                            <span class="input-group-text px-2">%</span>
                        </div>
                    </td>
                    <td class="col-amount" style="font-weight: bold; color: #28a745; text-align: center;">
                        <div class="input-group input-group-sm" style="width: 120px; margin: 0 auto;">
                            <span class="input-group-text px-2 border-0 bg-transparent">₹</span>
                            <input type="text" class="form-control incentive-amount-input text-start" name="incentive_amount[]" value="{{ $emp['incentive_amount'] }}" readonly style="background-color: transparent; border: none; padding: 0.25rem 0; font-weight: bold; color: #28a745;">
                        </div>
                    </td>
                    <td class="col-details details-column">
                        <button type="button" class="incentive-expand-toggle d-md-none" data-target="incentive-details-{{ $emp['id'] }}">
                            <span>+</span>
                        </button>
                    </td>
                    <td class="col-action">
                        <button type="button"
                            class="btn btn-sm {{ $emp['is_saved'] ? 'btn-outline-warning' : 'btn-outline-primary' }} save-row-btn"
                            data-id="{{ $emp['id'] }}">
                            {{ $emp['is_saved'] ? 'Update' : 'Save' }}
                        </button>
                    </td>
                </tr>
                @endforeach
              </tbody>
            </table>
        </div>
        </form>

      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const salaryMonthInput = document.getElementById('salaryMonth');
    if (salaryMonthInput) {
      salaryMonthInput.addEventListener('change', function() {
        const selectedMonth = this.value;
        if (selectedMonth) {
          window.location.href = `{{ route('payroll.incentive') }}?month=${selectedMonth}`;
        }
      });
    }

    let dt = null;
    if($.fn.DataTable) {
        dt = $('#incentive-table').DataTable({
            "bFilter": true,
            "bLengthChange": true,
            "pageLength": 25,
            "ordering": false // Disable ordering so inputs don't jump around when typing
        });
    }

    // Dynamic Calculation
    $('#incentive-table').on('input', '.total-sales-input, .incentive-percent-input', function() {
        const row = $(this).closest('tr');
        const sales = parseFloat(row.find('.total-sales-input').val()) || 0;
        const percent = parseFloat(row.find('.incentive-percent-input').val()) || 0;
        
        const amount = (sales * (percent / 100)).toFixed(2);
        row.find('.incentive-amount-input').val(amount);
    });

    // Save All
    $('#saveAllBtn').click(function() {
        // Serialize all data from datatable (including hidden pages)
        const formData = dt ? dt.$('input').serialize() : $('#incentiveForm').serialize();
        const month = $('input[name="month"]').val();
        const token = $('input[name="_token"]').val();

        $.ajax({
            url: '{{ route("payroll.incentive.save") }}',
            type: 'POST',
            data: formData + '&month=' + month + '&_token=' + token,
            success: function(response) {
                if(response.status) {
                    Swal.fire({
                        title: "Success!",
                        text: "All incentives saved successfully!",
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(err) {
                Swal.fire({
                    title: "Error!",
                    text: "Error saving incentives.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
            }
        });
    });

    // Save Single Row
    $('#incentive-table').on('click', '.save-row-btn', function() {
        const row = $(this).closest('tr');
        const id = $(this).data('id');
        const sales = row.find('.total-sales-input').val();
        const percent = row.find('.incentive-percent-input').val();
        const amount = row.find('.incentive-amount-input').val();
        const month = $('input[name="month"]').val();
        const token = $('input[name="_token"]').val();

        $.ajax({
            url: '{{ route("payroll.incentive.save") }}',
            type: 'POST',
            data: {
                _token: token,
                month: month,
                employee_id: [id],
                total_sales: [sales],
                incentive_percentage: [percent],
                incentive_amount: [amount]
            },
            success: function(response) {
                if(response.status) {
                    Swal.fire({
                        title: "Success!",
                        text: "Incentive saved successfully!",
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(err) {
                Swal.fire({
                    title: "Error!",
                    text: "Error saving incentive.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
            }
        });
    });
    $(document).on('click', '.incentive-expand-toggle', function() {
        const targetId = $(this).data('target');
        const details = document.getElementById(targetId);
        if (!details) return;

        const isOpen = details.classList.toggle('show');
        $(this).find('span').text(isOpen ? '-' : '+');
    });

});
</script>
@endsection


@php
    $ticket           = $ticket ?? null;
    $formMode         = $formMode ?? 'create';
    $selectedCustomer = old('customer_id', optional($ticket)->customer_id ?? ($preselectedCustomerId ?? ''));
    $selectedPriority = old('priority',    optional($ticket)->priority    ?? '');
    $selectedStatus   = old('status',      optional($ticket)->status      ?? '');
    $selectedAssigned = old('assigned_to', optional($ticket)->assigned_to ?? '');
    $subjectValue     = old('subject',     optional($ticket)->subject     ?? '');
    $descValue        = old('description', optional($ticket)->description ?? '');
    $remarksValue     = old('remarks',     optional($ticket)->remarks     ?? '');
    $ticketNoValue    = optional($ticket)->ticket_no ?: 'Auto-generated after save';
    $selectedOrder    = old('order_id', optional($ticket)->order_id ?? ($preselectedOrderId ?? ''));
    $selectedProduct  = old('product_id', optional($ticket)->product_id ?? '');
    $selectedCustomer = old('customer_id', optional($ticket)->customer_id ?? ($preselectedCustomerId ?? ''));
    $ticketRaisedIn   = old('ticket_raised_in', optional($ticket)->ticket_raised_in ?? '');
@endphp

<style>
    /* ── Responsive grid ─────────────────────────────────── */

    /* Mobile (< 576px): 2 columns — col-6 handles this natively, no override needed */

    @media (min-width: 576px) and (max-width: 767.98px) {
        #ticketFormCard .select2-container {
            width: 100% !important;
        }
    }

    @media (min-width: 768px) and (max-width: 1199.98px) {
        #ticketFormCard .col-lg-3 {
            flex: 0 0 50%;
            max-width: 50%;
        }
        #ticketFormCard .select2-container {
            width: 100% !important;
        }
    }

    /* Always keep Select2 full-width inside its column */
    #ticketFormCard .select2-container {
        width: 100% !important;
    }

    /* Action button row: stack buttons on mobile */
    @media (max-width: 575.98px) {
        #ticketFormCard .col-12.d-flex.gap-2 .btn {
            flex: 1 1 45%;
        }
    }
    .form-group textarea {
        height: 40px !important;
    }

    /* Modern Radio Button UI */
    .custom-radio-label {
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        position: relative;
        margin: 0;
    }

    .custom-radio-label input[type="radio"] {
        appearance: none;
        -webkit-appearance: none;
        width: 18px;
        height: 18px;
        border: 2px solid #888;
        border-radius: 50%;
        position: relative;
        cursor: pointer;
        transition: all 0.25s ease;
        margin: 0;
    }

    .custom-radio-label input[type="radio"]:checked {
        border-color: #0056b3;
        background-color: #0056b3;
    }

    .custom-radio-label input[type="radio"]:checked::after {
        content: "";
        display: block;
        width: 8px;
        height: 8px;
        background: white;
        border-radius: 50%;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .custom-radio-label:hover input[type="radio"] {
        border-color: #5a50cc;
    }

    /* Modern Toggle Switch */
    .modern-toggle {
        display: flex;
        align-items: center;
        cursor: pointer;
        user-select: none;
        gap: 10px;
        margin-bottom: 0;
    }
    
    .modern-toggle input[type="checkbox"] {
        display: none;
    }
    
    .toggle-slider {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
        min-width: 44px;
        flex-shrink: 0;
        background-color: #d1d5db;
        border-radius: 24px;
        transition: .4s;
    }
    
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        border-radius: 50%;
        transition: .4s;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    
    .modern-toggle input:checked + .toggle-slider {
        background-color: #ff9f43; /* Matches theme color */
    }
    
    .modern-toggle input:checked + .toggle-slider:before {
        transform: translateX(20px);
    }
</style>

<div class="card" id="ticketFormCard">
    <div class="card-body">
        <div class="row g-3">

            {{-- Ticket No (shown only on edit) --}}
            @if ($formMode === 'edit' && $ticket)
            <div class="col-6 col-lg-3 d-none">
                <div class="form-group">
                    <label>Ticket No</label>
                    <input type="text" class="form-control" value="{{ $ticketNoValue }}" disabled>
                </div>
            </div>
            @endif

            {{-- Customer --}}
            <div class="col-6 col-lg-3">
                <div class="form-group">
                    <label>Customer Name <span class="text-danger">*</span></label>
                    <select name="customer_id" id="customer_id" class="form-control select2-ticket">
                        <option value="">Select Customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}"
                                data-email="{{ $customer->email }}"
                                data-phone="{{ $customer->phone }}"
                                {{ (string) $selectedCustomer === (string) $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}{{ $customer->phone ? ' - ' . $customer->phone : '' }}
                            </option>
                        @endforeach
                    </select>
                    <span class="text-danger field-error" data-field="customer_id"></span>
                </div>
            </div>

            {{-- Order --}}
            <div class="col-6 col-lg-3">
                <div class="form-group">
                    <label>Order Number <span class="text-danger">*</span></label>
                    <select name="order_id" id="order_id" class="form-control select2-ticket" data-selected="{{ $selectedOrder }}">
                        <option value="">Select Order</option>
                    </select>
                    <span class="text-danger field-error" data-field="order_id"></span>
                </div>
            </div>



            {{-- Priority --}}
            <div class="col-6 col-lg-3">
                <div class="form-group">
                    <label>Priority <span class="text-danger">*</span></label>
                    <select name="priority" class="form-control select2-ticket">
                        <option value="">Select Priority</option>
                        @foreach ($priorities as $priority)
                            <option value="{{ $priority }}"
                                {{ $selectedPriority === $priority ? 'selected' : '' }}>
                                {{ ucfirst($priority) }}
                            </option>
                        @endforeach
                    </select>
                    <span class="text-danger field-error" data-field="priority"></span>
                </div>
            </div>

            {{-- Status --}}
            <div class="col-6 col-lg-3">
                <div class="form-group">
                    <label>Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-control select2-ticket">
                        <option value="">Select Status</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}"
                                {{ $selectedStatus === $status ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                    <span class="text-danger field-error" data-field="status"></span>
                </div>
            </div>



            {{-- Attachment --}}
            <div class="col-6 col-lg-3">
                <div class="form-group">
                    <label>Attachment</label>
                    <input type="file" name="attachment" class="form-control">
                    @if (!empty(optional($ticket)->attachment))
                        <small class="d-block mt-1">
                            <a href="{{ asset('storage/' . $ticket->attachment) }}" target="_blank">
                                <i class="fa fa-paperclip me-1"></i>View current attachment
                            </a>
                        </small>
                    @endif
                    <span class="text-danger field-error" data-field="attachment"></span>
                </div>
            </div>

            {{-- Subject --}}
            <div class="col-6 col-lg-3">
                <div class="form-group">
                    <label>Subject <span class="text-danger">*</span></label>
                    <input type="text" name="subject" class="form-control"
                        value="{{ $subjectValue }}" placeholder="Enter subject">
                    <span class="text-danger field-error" data-field="subject"></span>
                </div>
            </div>


            {{-- Remarks --}}
            <div class="col-6 col-lg-6">
                <div class="form-group">
                    <label>Remarks</label>
                    <textarea name="remarks" class="form-control" rows="1"
                        placeholder="Internal remarks">{{ $remarksValue }}</textarea>
                    <span class="text-danger field-error" data-field="remarks"></span>
                </div>
            </div>

            {{-- Communication Options --}}
            <div class="col-12 col-lg-6">
                <div class="form-group d-flex gap-4 mt-1">
                    <label class="modern-toggle">
                        <input type="checkbox" id="send_email_checkbox" name="send_email" value="1" {{ str_contains(strtolower($ticketRaisedIn), 'email') ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                        <span>Send Email</span>
                    </label>
                    <label class="modern-toggle">
                        <input type="checkbox" id="send_whatsapp_checkbox" name="send_whatsapp" value="1" {{ str_contains(strtolower($ticketRaisedIn), 'whatsapp') ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                        <span>Send WhatsApp</span>
                    </label>
                </div>
            </div>

            {{-- Selected Product Details Area --}}
            <div class="col-12 d-none" id="product_details_area">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 80px;" class="text-center">Select</th>
                                <th style="width: 80px;">Image</th>
                                <th>Product Name</th>
                                <th style="width: 150px;">Quantity</th>
                                <th style="width: 150px;">Price</th>
                            </tr>
                        </thead>
                        <tbody id="product_details_tbody">
                            <!-- Product details will be shown here -->
                        </tbody>
                    </table>
                </div>
                {{-- Product Validation Error --}}
                <div class="mt-2">
                    <span class="text-danger field-error" data-field="product_id"></span>
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="col-12 d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-submit" id="ticketSubmitBtn">
                    {{ $formMode === 'edit' ? 'Update' : 'Save' }}
                </button>
                <a href="{{ route('ticket.list') }}" class="btn btn-cancel">Cancel</a>
            </div>

        </div>{{-- /.row --}}
    </div>{{-- /.card-body --}}
</div>{{-- /.card --}}

@push('js')
<script>
$(document).ready(function () {
    // Initialise Select2 for all dropdowns inside this form
    $('.select2-ticket').select2({
        placeholder: 'Select an option',
        allowClear: true,
        width: '100%'
    });

    // Clear individual field error on change / input
    $('.field-error').each(function () {
        const field = $(this).data('field');
        $('[name="' + field + '"]').on('change input', function () {
            $('.field-error[data-field="' + field + '"]').html('');
        });
    });

    var isAutoSelecting = false;
    var initialLoadProduct = '{{ $selectedProduct }}';

    // Auto-fetch orders based on selected customer
    $('#customer_id').on('change', function() {
        if (isAutoSelecting) return;
        var customerId = $(this).val();
        fetchCustomerOrders(customerId);
    });

    // Auto-fetch products based on selected order and auto-select customer
    $('#order_id').on('change', function() {
        var orderId = $(this).val();
        
        var customerId = $(this).find(':selected').data('customer-id');
        var currentCustomer = $('#customer_id').val();
        
        if (customerId && customerId != currentCustomer) {
            isAutoSelecting = true;
            $('#customer_id').val(customerId).trigger('change.select2'); // Update UI
            fetchCustomerOrders(customerId, orderId); // Fetch and preselect order
            isAutoSelecting = false;
        } else {
            fetchOrderProducts(orderId, initialLoadProduct);
            initialLoadProduct = null; // Clear it so subsequent changes don't reuse it
        }
    });

    function fetchCustomerOrders(customerId, selectedOrderId = null) {

        $.ajax({
            url: "{{ route('ticket.get_customer_orders') }}",
            type: "GET",
            data: { customer_id: customerId },
            success: function(res) {
                if (res.status) {
                    let options = '<option value="">Select Order</option>';
                    res.data.forEach(function(order) {
                        let selected = (selectedOrderId && selectedOrderId == order.id) ? 'selected' : '';
                        options += `<option value="${order.id}" data-customer-id="${order.user_id}" ${selected}>${order.order_number}</option>`;
                    });
                    $('#order_id').html(options).trigger('change');
                }
            }
        });
    }

    function fetchOrderProducts(orderId, selectedProductId = null) {
        if (!orderId) {
            $('#product_details_area').addClass('d-none');
            $('#product_details_tbody').html('');
            return;
        }

        $.ajax({
            url: "{{ route('ticket.get_order_products') }}",
            type: "GET",
            data: { order_id: orderId },
            success: function(res) {
                if (res.status && res.data.length > 0) {
                    let rows = '';
                    res.data.forEach(function(product) {
                        let isChecked = (selectedProductId && selectedProductId == product.id) ? 'checked' : '';
                        rows += `
                            <tr>
                                <td class="text-center align-middle">
                                    <label class="custom-radio-label">
                                        <input type="radio" name="product_id" value="${product.id}" ${isChecked}>
                                    </label>
                                </td>
                                <td class="align-middle">
                                    <img src="${product.image}" alt="Product" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                </td>
                                <td class="align-middle">${product.name}</td>
                                <td class="align-middle">${product.qty || 0}</td>
                                <td class="align-middle">${product.price || 0}</td>
                            </tr>
                        `;
                    });
                    $('#product_details_tbody').html(rows);
                    $('#product_details_area').removeClass('d-none');
                } else {
                    $('#product_details_area').addClass('d-none');
                    $('#product_details_tbody').html('');
                }
            }
        });
    }

    // On edit load or create load
    var initialCustomer = $('#customer_id').val();
    var preselectedOrder = $('#order_id').data('selected');
    if (initialCustomer && preselectedOrder) {
        fetchCustomerOrders(initialCustomer, preselectedOrder);
    } else {
        fetchCustomerOrders('', preselectedOrder);
    }

    // Event listener to clear product error when a radio is selected
    $(document).on('change', 'input[name="product_id"]', function() {
        $('.field-error[data-field="product_id"]').html('');
    });

    // Handle send email checkbox validation
    $('#send_email_checkbox').on('change', function() {
        if ($(this).is(':checked')) {
            var selectedCustomer = $('#customer_id').find(':selected');
            var customerId = selectedCustomer.val();

            if (!customerId) {
                Swal.fire({
                    title: 'No Customer Selected',
                    text: 'Please select a customer first.',
                    icon: 'warning'
                });
                $(this).prop('checked', false);
                return;
            }

            var email = selectedCustomer.data('email');
            if (!email || email === 'null' || email === '') {
                Swal.fire({
                    title: 'Email Not Available',
                    text: 'The selected customer does not have an email address. Suggesting WhatsApp instead.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#ff9f43'
                });
                $(this).prop('checked', false);
                $('#send_whatsapp_checkbox').prop('checked', true);
            }
        }
    });

    // Handle send whatsapp checkbox validation
    $('#send_whatsapp_checkbox').on('change', function() {
        if ($(this).is(':checked')) {
            var selectedCustomer = $('#customer_id').find(':selected');
            var customerId = selectedCustomer.val();

            if (!customerId) {
                Swal.fire({
                    title: 'No Customer Selected',
                    text: 'Please select a customer first.',
                    icon: 'warning'
                });
                $(this).prop('checked', false);
                return;
            }

            var phone = selectedCustomer.data('phone');
            if (!phone || phone === 'null' || phone === '') {
                Swal.fire({
                    title: 'Phone Not Available',
                    text: 'The selected customer does not have a phone number.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#ff9f43'
                });
                $(this).prop('checked', false);
            }
        }
    });

    // Custom form submission validation for product selection
    $('#ticketSubmitBtn').closest('form').on('submit', function(e) {
        if (!$('#product_details_area').hasClass('d-none') && $('#product_details_tbody tr').length > 0) {
            if ($('input[name="product_id"]:checked').length === 0) {
                e.preventDefault();
                e.stopImmediatePropagation();
                Swal.fire({
                    title: 'Product Selection Required',
                    text: 'Please select a product from the list.',
                    icon: 'warning',
                    confirmButtonColor: '#ff9f43'
                });
                return false;
            }
        }
    });
});
</script>
@endpush

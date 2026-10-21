@extends('layout.app')

@section('title', 'Edit Challan Bill')

@section('content')
<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Edit Challan Bill</h4>
            <h6>Update existing Challan Bill</h6>
        </div>
        <div class="page-btn">
            <a href="{{ route('challan-bills.index') }}" class="btn btn-added">Back</a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('challan-bills.update', $challanBill->id) }}" method="POST" id="editForm" novalidate>
                @csrf
                @method('PUT')
                <div class="row">
                    <!-- Vendor -->
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Vendor <span class="text-danger">*</span></label>
                            <select class="select" name="vendor_id" required>
                                <option value="">Select Vendor</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" {{ $challanBill->vendor_id == $vendor->id ? 'selected' : '' }}>
                                        {{ $vendor->name }} ({{ $vendor->phone ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Customer -->
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Customer <span class="text-danger">*</span></label>
                            <select class="select" name="customer_id" id="customer_id" required>
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ $challanBill->customer_id == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} ({{ $customer->phone ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Challan Date -->
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Challan Date <span class="text-danger">*</span></label>
                            <input type="date" name="challan_date" class="form-control" value="{{ $challanBill->challan_date->format('Y-m-d') }}" required>
                        </div>
                    </div>

                    <!-- Challan Number (Auto Generated) -->
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Challan Number</label>
                            <input type="text" name="challan_number" class="form-control" value="{{ $challanBill->challan_number }}" readonly>
                        </div>
                    </div>

                </div>

                <!-- Customer Details Preview -->
                <!-- <div class="row mt-3 mb-4" id="customer-details-section">
                    <div class="col-12">
                        <h5 class="mb-2">Customer Details</h5>
                        <div class="p-3 border rounded bg-light">
                            <p class="mb-1"><strong>Name:</strong> <span id="lbl_cust_name">{{ $challanBill->customer->name ?? 'N/A' }}</span></p>
                            <p class="mb-1"><strong>Phone:</strong> <span id="lbl_cust_phone">{{ $challanBill->customer->phone ?? 'N/A' }}</span></p>
                            <p class="mb-0"><strong>Email:</strong> <span id="lbl_cust_email">{{ $challanBill->customer->email ?? 'N/A' }}</span></p>
                        </div>
                    </div>
                </div> -->

                <hr>

                <!-- Products Repeater -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Delivery Challan (Products)</h5>
                            <button type="button" class="btn btn-sm btn-primary" id="add-product-btn">
                                <i class="fas fa-plus"></i> Add More Product
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width:50px;">Sr No</th>
                                        <th style="width:250px;">Product Name <span class="text-danger">*</span></th>
                                        <th style="width:60px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="products-container">
                                    @php
                                        $billProducts = is_array($challanBill->products) ? $challanBill->products : [];
                                    @endphp
                                    
                                    @if(count($billProducts) > 0)
                                        @foreach($billProducts as $index => $item)
                                            <tr class="product-row">
                                                <td class="sr-no">{{ $index + 1 }}</td>
                                                <td>
                                                    <select class="form-control product-select" name="products[{{ $index }}][product_name]" required>
                                                        <option value="">Select Product</option>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->name }}" {{ ($item['product_name'] ?? '') == $product->name ? 'selected' : '' }}>{{ $product->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger remove-product-btn"><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="product-row">
                                            <td class="sr-no">1</td>
                                            <td>
                                                <select class="form-control product-select" name="products[0][product_name]" required>
                                                    <option value="">Select Product</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->name }}">{{ $product->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger remove-product-btn" disabled><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>Remarks</label>
                            <textarea name="remarks" class="form-control" rows="3">{{ $challanBill->remarks }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-lg-12 text-end">
                        <button type="submit" class="btn btn-submit me-2" id="submitBtn">Update</button>
                        <a href="{{ route('challan-bills.index') }}" class="btn btn-cancel">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        let productIndex = {{ count(is_array($challanBill->products) ? $challanBill->products : []) }};
        if(productIndex == 0) productIndex = 1;

        // Auto fetch customer details
        $('#customer_id').on('change', function() {
            var customerId = $(this).val();
            if(customerId) {
                $.ajax({
                    url: '/challan-bills/customer-details/' + customerId,
                    type: "GET",
                    dataType: "json",
                    success:function(data) {
                        $('#customer-details-section').show();
                        $('#lbl_cust_name').text(data.name || 'N/A');
                        $('#lbl_cust_phone').text(data.phone || 'N/A');
                        $('#lbl_cust_email').text(data.email || 'N/A');
                    }
                });
            } else {
                $('#customer-details-section').hide();
            }
        });

        // Add Product Row
        $('#add-product-btn').on('click', function() {
            var newRow = `
                <tr class="product-row">
                    <td class="sr-no"></td>
                    <td>
                        <select class="form-control product-select" name="products[${productIndex}][product_name]" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->name }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-product-btn"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
            $('#products-container').append(newRow);
            productIndex++;
            updateRowNumbers();
        });

        // Remove Product Row
        $(document).on('click', '.remove-product-btn', function() {
            if ($('.product-row').length > 1) {
                $(this).closest('tr').remove();
                updateRowNumbers();
            }
        });

        function updateRowNumbers() {
            $('.product-row').each(function(index) {
                $(this).find('.sr-no').text(index + 1);
                $(this).find('.remove-product-btn').prop('disabled', $('.product-row').length === 1);
            });
        }
        
        updateRowNumbers();

        // Form Submit with AJAX
        $('#editForm').on('submit', function(e) {
            e.preventDefault();
            
            var form = $(this);
            var url = form.attr('action');
            var formData = new FormData(this);
            // Append _method=PUT manually if it's not already in FormData by Laravel
            
            var submitBtn = $('#submitBtn');
            var originalBtnText = submitBtn.html();

            // Clear previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            // Add loader to button
            submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...').prop('disabled', true);

            $.ajax({
                url: url,
                type: 'POST', // Method PUT is handled via _method field in form
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if(response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = response.redirect_url;
                        });
                    }
                },
                error: function(xhr) {
                    submitBtn.html(originalBtnText).prop('disabled', false);
                    
                    if(xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            var inputName = key.replace(/\./g, '][');
                            if(key.indexOf('.') !== -1) {
                                inputName = key.substring(0, key.indexOf('.')) + '[' + key.substring(key.indexOf('.') + 1).replace(/\./g, '][') + ']';
                            }
                            var input = form.find('[name="' + key + '"], [name="' + inputName + '"]');
                            
                            if(input.length) {
                                input.addClass('is-invalid');
                                if (input.hasClass('select2-hidden-accessible') || input.hasClass('select')) {
                                    input.next('.select2-container').after('<div class="invalid-feedback d-block">' + value[0] + '</div>');
                                } else {
                                    input.after('<div class="invalid-feedback d-block">' + value[0] + '</div>');
                                }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Validation Error',
                                    text: value[0]
                                });
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Something went wrong. Please try again.'
                        });
                    }
                }
            });
        });
    });
</script>
@endsection

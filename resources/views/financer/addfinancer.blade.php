@extends('layout.app')

@section('title', 'Add Financer')

@section('content')
<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Add Financer</h4>
        </div>
        <div class="back-button">
            <a href="{{ route('financer.list') }}" class="btn btn-primary back-button"> <i class="fa-solid fa-arrow-left"></i> Back</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="financerForm">
                @csrf
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="form-group">
                            <label>Financer Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" >
                            <div class="text-danger error-name"></div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" id="phone" class="form-control">
                            <div class="text-danger error-phone"></div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="form-group">
                            <label>Alternate Phone</label>
                            <input type="text" name="alternate_phone" id="alternate_phone" class="form-control">
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" id="email" class="form-control">
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="form-group">
                            <label>PAN Number</label>
                            <input type="text" name="pan_number" id="pan_number" class="form-control" maxlength="10">
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="form-group">
                            <label>GST Number</label>
                            <input type="text" name="gst_number" id="gst_number" class="form-control" maxlength="15">
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="form-group">
                            <label>Credit Limit</label>
                            <input type="text" name="credit_limit" id="credit_limit" class="form-control">
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="form-group">
                            <label>Credit Days</label>
                            <input type="number" name="credit_days" id="credit_days" class="form-control">
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="form-group">
                            <label>Account Group</label>
                            <input type="text" name="account_group" id="account_group" class="form-control">
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="form-group">
                            <label>Address Line 1</label>
                            <textarea name="address_line1" id="address_line1" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city" id="city" class="form-control">
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="form-group">
                            <label>State</label>
                            <input type="text" name="state" id="state" class="form-control">
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="form-group">
                            <label>Pin Code</label>
                            <input type="text" name="pin_code" id="pin_code" class="form-control">
                        </div>
                    </div>

                    <div class="col-lg-3 col-6  mt-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="status" name="status" checked>
                            <label class="form-check-label" for="status">Active</label>
                        </div>
                    </div>

                    <div class="col-lg-12col-12 mt-3">
                        <button type="submit" id="financerSubmit" class="btn btn-submit me-2">Submit</button>
                        <a href="{{ route('financer.list') }}" class="btn btn-cancel">Cancel</a>
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
    $('#financerForm').on('submit', function(e) {
        e.preventDefault();
        $('.text-danger[class*="error-"]').text('');
        $('#financerSubmit').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...');

        const formData = new FormData(this);
        formData.append('selectedSubAdminId', localStorage.getItem('selectedSubAdminId') || '');

        $.ajax({
            url: '{{ route('financer.store') }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                Swal.fire({
                    title: 'Success',
                    text: response.message || 'Financer saved.',
                    icon: 'success',
                    confirmButtonColor: '#ff9f43'
                }).then(function() {
                    window.location.href = '{{ route('financer.list') }}';
                });
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    if (errors) {
                        $.each(errors, function(key, value) {
                            let errorDiv = $('.error-' + key);
                            if(errorDiv.length === 0) {
                                $('[name="' + key + '"]').after('<div class="text-danger error-' + key + '"></div>');
                                errorDiv = $('.error-' + key);
                            }
                            errorDiv.text(value[0]);
                        });
                    }
                    // Swal.fire('Error', xhr.responseJSON.message || 'Validation failed.', 'error');
                } else {
                    const msg = xhr.responseJSON?.message || 'Failed to save financer.';
                    // Swal.fire('Error', msg, 'error');
                }
            },
            complete: function() {
                $('#financerSubmit').prop('disabled', false).text('Submit');
            }
        });
    });
});
</script>
@endpush

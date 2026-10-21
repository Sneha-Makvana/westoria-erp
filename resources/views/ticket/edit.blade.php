@extends('layout.app')

@section('title', 'Edit Ticket')
<style>
    .new-btn {
    min-width: 90px;
    background: #1b2850;
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    padding: 6px 10px !important;
    -webkit-transition: all .5s ease;
    -ms-transition: all .5s ease;
    transition: all .5s ease;
}

.header .user-img img {
    /* width: 38px;
    border-radius: 50%; */
    margin-top: 0px !important;
}
</style>
@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Edit Ticket</h4>
            </div>
            <div class="back-button">
                <a href="{{ route('ticket.list') }}" class="btn new-btn btn-cancel">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <form id="ticketForm" enctype="multipart/form-data">
            @csrf
            @include('ticket._form', ['ticket' => $ticket, 'formMode' => 'edit'])
        </form>
    </div>
@endsection

@push('js')
<script>
$(document).ready(function () {

    $('#ticketForm').on('submit', function (e) {
        e.preventDefault();

        const $btn         = $('#ticketSubmitBtn');
        const originalHtml = $btn.html();

        // Show loader
        $btn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Updating...').prop('disabled', true);

        // Clear previous errors
        $('.field-error').html('');

        const formData = new FormData(this);
        // Laravel does not support PUT with FormData — spoof the method
        formData.append('_method', 'PUT');
        formData.append('selectedSubAdminId', localStorage.getItem('selectedSubAdminId') || '');

        $.ajax({
            url: '{{ route('ticket.update', $ticket->id) }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (response) {
                $btn.html(originalHtml).prop('disabled', false);
                if (response.status) {
                    Swal.fire({
                        title: 'Updated!',
                        text: response.message || 'Ticket updated successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ff9f43'
                    }).then(function () {
                        window.location.href = response.redirect || '{{ route('ticket.list') }}';
                    });
                }
            },
            error: function (xhr) {
                $btn.html(originalHtml).prop('disabled', false);
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors || {};
                    $.each(errors, function (field, messages) {
                        $('.field-error[data-field="' + field + '"]').html(messages[0]);
                    });
                } else {
                    Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                }
            }
        });
    });

});
</script>
@endpush

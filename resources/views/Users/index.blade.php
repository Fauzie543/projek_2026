@extends('layouts.app')

@section('header', 'Users Management')

@section('content')
<div class="bg-white p-6 rounded-md shadow-sm">
    <!-- Tombol untuk membuka modal tambah user -->
    <button id="addUserBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
        Add User
    </button>

    <table id="users-table" class="table table-bordered table-striped w-full">
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Include file modal -->
@include('users.modal')

@endsection


@push('scripts')
{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function () {
    var table = $('#users-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '{{ route('users.data') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'role', name: 'roles.name' }, // Sesuaikan untuk sorting/searching relasi
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        columnDefs: [
            {
                targets: '_all',
                className: 'py-3 align-middle'
            }
        ],
        dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>'
    });

    // --- LOGIKA MODAL ---

    // Tampilkan modal untuk TAMBAH data
    $('#addUserBtn').on('click', function () {
        $('#userForm')[0].reset();
        $('.error-message').text('');
        $('#modal_title').text('Add New User');
        $('#submitBtn').text('Save User');
        $('#form_method').val('POST');
        $('#userForm').attr('action', '{{ route('users.store') }}');
        $('#password, #password_confirmation').prop('required', true);
        $('#userModal').removeClass('hidden');
    });

    // Tampilkan modal untuk EDIT data
    $('#users-table').on('click', '.edit-btn', function () {
        var userId = $(this).data('id');
        var url = `/users/${userId}/edit`;

        $('#userForm')[0].reset();
        $('.error-message').text('');

        $.ajax({
            url: url,
            method: 'GET',
            success: function(data) {
                $('#modal_title').text('Edit User');
                $('#submitBtn').text('Update User');
                $('#form_method').val('PUT');
                $('#userForm').attr('action', `/users/${userId}`);

                // Isi data ke form
                $('#user_id').val(data.id);
                $('#name').val(data.name);
                $('#email').val(data.email);
                // Pilih role yang sesuai
                $('#role').val(data.roles[0] ? data.roles[0].id : '');

                $('#password, #password_confirmation').prop('required', false);
                $('#userModal').removeClass('hidden');
            },
            error: function() {
                Swal.fire('Error', 'Could not fetch user data.', 'error');
            }
        });
    });

    // Sembunyikan modal
    $('#cancelBtn, .close-modal').on('click', function () {
        $('#userModal').addClass('hidden');
    });

    // Kirim data form (untuk ADD dan UPDATE)
    $('#userForm').on('submit', function (e) {
        e.preventDefault();
        $('.error-message').text('');
        var url = $(this).attr('action');
        var formData = $(this).serialize();

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (response) {
                $('#userModal').addClass('hidden');
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: response.success, showConfirmButton: false, timer: 3000 });
                table.ajax.reload();
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    Swal.fire({ icon: 'error', title: 'Validation Error', text: 'Please check the form for errors.' });
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        $('#' + key + '_error').text(value[0]);
                    });
                } else {
                     Swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong! Please try again.' });
                }
            }
        });
    });

    // --- LOGIKA DELETE ---
    $('#users-table').on('click', '.delete-btn', function (e) {
        e.preventDefault();
        var deleteUrl = $(this).data('url');

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: deleteUrl,
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: response.success, showConfirmButton: false, timer: 3000 });
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                         Swal.fire('Failed!', xhr.responseJSON.error || 'There was a problem deleting the user.', 'error');
                    }
                });
            }
        });
    });

    // --- LOGIKA TOGGLE PASSWORD ---
    $('.toggle-password').on('click', function() {
        var input = $(this).closest('.relative').find('input');
        var iconShow = $(this).find('.icon-show');
        var iconHide = $(this).find('.icon-hide');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            iconShow.addClass('hidden');
            iconHide.removeClass('hidden');
        } else {
            input.attr('type', 'password');
            iconShow.removeClass('hidden');
            iconHide.addClass('hidden');
        }
    });
});
</script>
@endpush

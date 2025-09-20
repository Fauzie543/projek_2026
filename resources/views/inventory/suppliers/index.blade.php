@extends('layouts.app')

@section('header', 'Suppliers Management')

@push('styles')
<style>
    div.dt-container div.dt-search input {
        width: 15rem;
    }
</style>
@endpush

@section('content')
<div class="bg-white p-6 rounded-md shadow-sm">
    <button id="addSupplierBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
        Add Supplier
    </button>

    <table id="suppliers-table" class="table table-bordered table-striped w-full">
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>

@include('inventory.suppliers.modal')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function () {
    var table = $('#suppliers-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '{{ route('inventory.suppliers.data') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'phone', name: 'phone' },
            { data: 'email', name: 'email' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        columnDefs: [{ targets: '_all', className: 'py-3 align-middle' }],
        dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>'
    });

    // --- LOGIKA MODAL ---

    $('#addSupplierBtn').on('click', function () {
        $('#supplierForm')[0].reset();
        $('.error-message').text('');
        $('#modal_title').text('Add New Supplier');
        $('#submitBtn').text('Save Supplier');
        $('#form_method').val('POST');
        $('#supplierForm').attr('action', '{{ route('inventory.suppliers.store') }}');
        $('#supplierModal').removeClass('hidden');
    });

    $('#suppliers-table').on('click', '.edit-btn', function () {
        var supplierId = $(this).data('id');
        var url = `/inventory/suppliers/${supplierId}/edit`;

        $('#supplierForm')[0].reset();
        $('.error-message').text('');

        $.ajax({
            url: url,
            method: 'GET',
            success: function(data) {
                $('#modal_title').text('Edit Supplier');
                $('#submitBtn').text('Update Supplier');
                $('#form_method').val('PUT');
                $('#supplierForm').attr('action', `/inventory/suppliers/${supplierId}`);

                $('#supplier_id').val(data.id);
                $('#name').val(data.name);
                $('#phone').val(data.phone);
                $('#email').val(data.email);
                $('#address').val(data.address);

                $('#supplierModal').removeClass('hidden');
            },
            error: function() {
                Swal.fire('Error', 'Could not fetch supplier data.', 'error');
            }
        });
    });

    $('#cancelBtn, .close-modal').on('click', function () {
        $('#supplierModal').addClass('hidden');
    });

    $('#supplierForm').on('submit', function (e) {
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
                $('#supplierModal').addClass('hidden');
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
    $('#suppliers-table').on('click', '.delete-btn', function (e) {
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
                         Swal.fire('Failed!', xhr.responseJSON.error || 'There was a problem deleting the supplier.', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush

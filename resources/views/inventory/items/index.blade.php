@extends('layouts.app')

@section('header', 'Inventory Items')

@push('styles')
{{-- Style kustom untuk memperkecil input search DataTables --}}
<style>
    div.dt-container div.dt-search input {
        width: 15rem; /* Anda bisa sesuaikan lebar ini */
    }
</style>
@endpush

@section('content')
<div class="bg-white p-6 rounded-md shadow-sm">
    <!-- Tombol untuk membuka modal tambah item -->
    <button id="addItemBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
        Add Item
    </button>

    <table id="items-table" class="table table-bordered table-striped w-full">
        <thead>
            <tr>
                <th>No</th>
                <th>SKU</th>
                <th>Name</th>
                <th>Category</th>
                <th>Total Stock</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Include file modal -->
@include('inventory.items.modal')

@endsection

@push('scripts')
{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function () {
    var table = $('#items-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '{{ route('inventory.items.index') }}', // Route disesuaikan
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'sku', name: 'sku' },
            { data: 'name', name: 'name' },
            { data: 'category', name: 'category' },
            { data: 'total_stock', name: 'total_stock', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        columnDefs: [{ targets: '_all', className: 'py-3 align-middle' }],
        dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>'
    });

    // --- LOGIKA MODAL ---

    // Tampilkan modal untuk TAMBAH data
    $('#addItemBtn').on('click', function () {
        $('#itemForm')[0].reset();
        $('.error-message').text('');
        $('#modal_title').text('Add New Item');
        $('#submitBtn').text('Save Item');
        $('#form_method').val('POST');
        $('#itemForm').attr('action', '{{ route('inventory.items.store') }}');
        $('#itemModal').removeClass('hidden');
    });

    // Tampilkan modal untuk EDIT data
    $('#items-table').on('click', '.edit-btn', function () {
        var itemId = $(this).data('id');
        var url = `/inventory/items/${itemId}/edit`;

        $('#itemForm')[0].reset();
        $('.error-message').text('');

        $.ajax({
            url: url,
            method: 'GET',
            success: function(data) {
                $('#modal_title').text('Edit Item');
                $('#submitBtn').text('Update Item');
                $('#form_method').val('PUT');
                $('#itemForm').attr('action', `/inventory/items/${itemId}`);

                // Isi data ke form
                $('#item_id').val(data.id);
                $('#sku').val(data.sku);
                $('#name').val(data.name);
                $('#category').val(data.category);
                $('#unit').val(data.unit);
                $('#reorder_point').val(data.reorder_point);

                $('#itemModal').removeClass('hidden');
            },
            error: function() {
                Swal.fire('Error', 'Could not fetch item data.', 'error');
            }
        });
    });

    // Sembunyikan modal
    $('#cancelBtn, .close-modal').on('click', function () {
        $('#itemModal').addClass('hidden');
    });

    // Kirim data form (untuk ADD dan UPDATE)
    $('#itemForm').on('submit', function (e) {
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
                $('#itemModal').addClass('hidden');
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
    $('#items-table').on('click', '.delete-btn', function (e) {
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
                         Swal.fire('Failed!', xhr.responseJSON.error || 'There was a problem deleting the item.', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush

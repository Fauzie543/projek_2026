@extends('layouts.app')

@section('header', 'Feed Batches (Production)')

@push('styles')
<style>
    div.dt-container div.dt-search input { width: 15rem; }
</style>
@endpush

@section('content')
<div class="bg-white p-6 rounded-md shadow-sm">
    <button id="addBatchBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
        Produce Feed Batch
    </button>

    <table id="batches-table" class="table table-bordered table-striped w-full">
        <thead>
            <tr>
                <th>No</th>
                <th>Recipe Name</th>
                <th>Qty Produced (Kg)</th>
                <th>Total Cost</th>
                <th>Production Date</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>

@include('inventory.feed-batches.modal')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function () {
    var table = $('#batches-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '{{ route('inventory.feed-batches.index') }}',
        order: [[4, 'desc']], // Urutkan berdasarkan tanggal terbaru
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'recipe_name', name: 'recipe.name' },
            { data: 'qty_kg', name: 'qty_kg' },
            { data: 'cost_total', name: 'cost_total' },
            { data: 'produced_at', name: 'produced_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        columnDefs: [{ targets: '_all', className: 'py-3 align-middle' }],
        dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>'
    });

    // --- LOGIKA MODAL ---

    $('#addBatchBtn').on('click', function () {
        $('#batchForm')[0].reset();
        $('.error-message').text('');
        $('#modal_title').text('Produce New Feed Batch');
        $('#submitBtn').text('Produce Batch');
        $('#batchForm').attr('action', '{{ route('inventory.feed-batches.store') }}');
        $('#batchModal').removeClass('hidden');
    });

    $('#cancelBtn, .close-modal').on('click', function () {
        $('#batchModal').addClass('hidden');
    });

    $('#batchForm').on('submit', function (e) {
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
                $('#batchModal').addClass('hidden');
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: response.success, showConfirmButton: false, timer: 3000 });
                table.ajax.reload();
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    // Cek jika ada pesan error custom dari controller
                    if(xhr.responseJSON.error) {
                        Swal.fire({ icon: 'error', title: 'Stock Error', text: xhr.responseJSON.error });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Validation Error', text: 'Please check the form for errors.' });
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function (key, value) {
                            $('#' + key + '_error').text(value[0]);
                        });
                    }
                } else {
                     Swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong! Please try again.' });
                }
            }
        });
    });

    // --- LOGIKA DELETE ---
    $('#batches-table').on('click', '.delete-btn', function (e) {
        e.preventDefault();
        var deleteUrl = $(this).data('url');

        Swal.fire({
            title: 'Are you sure?',
            text: "This will revert the stock changes and delete the batch record. This action cannot be undone!",
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
                         Swal.fire('Failed!', xhr.responseJSON.error || 'There was a problem deleting the batch.', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush

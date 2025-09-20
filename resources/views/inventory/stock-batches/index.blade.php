@extends('layouts.app')

@section('header', 'Stock Batches Management (Penerimaan Stok)')

@push('styles')
<style>
    div.dt-container div.dt-search input {
        width: 15rem;
    }
</style>
@endpush

@section('content')
<div class="bg-white p-6 rounded-md shadow-sm">
    <button id="addBatchBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
        Add Stock Batch
    </button>

    <table id="batches-table" class="table table-bordered table-striped w-full">
        <thead>
            <tr>
                <th>No</th>
                <th>Item Name</th>
                <th>Supplier</th>
                <th>Qty</th>
                <th>Unit Cost</th>
                <th>Received</th>
                <th>Expiry</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>

@include('inventory.stock-batches.modal')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function () {
    var table = $('#batches-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '{{ route('inventory.stock-batches.data') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'item_name', name: 'item.name' },
            { data: 'supplier_name', name: 'supplier.name' },
            { data: 'qty', name: 'qty' },
            { data: 'unit_cost', name: 'unit_cost' },
            { data: 'received_at', name: 'received_at' },
            { data: 'expiry_date', name: 'expiry_date' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        columnDefs: [{ targets: '_all', className: 'py-3 align-middle' }],
        dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>'
    });

    // --- LOGIKA MODAL ---

    $('#addBatchBtn').on('click', function () {
        $('#batchForm')[0].reset();
        $('.error-message').text('');
        $('#modal_title').text('Add New Stock Batch');
        $('#submitBtn').text('Save Batch');
        $('#form_method').val('POST');
        $('#batchForm').attr('action', '{{ route('inventory.stock-batches.store') }}');
        $('#batchModal').removeClass('hidden');
    });

    $('#batches-table').on('click', '.edit-btn', function () {
        var batchId = $(this).data('id');
        var url = `/inventory/stock-batches/${batchId}/edit`;

        $('#batchForm')[0].reset();
        $('.error-message').text('');

        $.ajax({
            url: url,
            method: 'GET',
            success: function(data) {
                $('#modal_title').text('Edit Stock Batch');
                $('#submitBtn').text('Update Batch');
                $('#form_method').val('PUT');
                $('#batchForm').attr('action', `/inventory/stock-batches/${batchId}`);

                $('#batch_id').val(data.id);
                $('#inventory_item_id').val(data.inventory_item_id);
                $('#supplier_id').val(data.supplier_id);
                $('#qty').val(data.qty);
                $('#unit_cost').val(data.unit_cost).trigger('input'); // Format Rupiah
                $('#received_at').val(data.received_at);
                $('#expiry_date').val(data.expiry_date);
                $('#note').val(data.note);

                $('#batchModal').removeClass('hidden');
            },
            error: function() {
                Swal.fire('Error', 'Could not fetch batch data.', 'error');
            }
        });
    });

    $('#cancelBtn, .close-modal').on('click', function () {
        $('#batchModal').addClass('hidden');
    });

    $('#batchForm').on('submit', function (e) {
        e.preventDefault();
        $('.error-message').text('');
        
        var costInput = $('#unit_cost');
        var numericCost = unformatRupiah(costInput.val());
        costInput.val(numericCost);

        var url = $(this).attr('action');
        var formData = $(this).serialize();
        
        costInput.val(formatRupiah(numericCost.toString()));

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
    $('#batches-table').on('click', '.delete-btn', function (e) {
        e.preventDefault();
        var deleteUrl = $(this).data('url');

        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
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
    
    // --- LOGIKA FORMAT RUPIAH ---
    function formatRupiah(angka) {
        var number_string = String(angka).replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return 'Rp ' + rupiah;
    }
    
    function unformatRupiah(rupiah) {
        return parseInt(String(rupiah).replace(/[^0-9]/g, ''), 10) || 0;
    }

    $('#unit_cost').on('input', function(e) {
        $(this).val(formatRupiah($(this).val()));
    });
});
</script>
@endpush

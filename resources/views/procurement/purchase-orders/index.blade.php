@extends('layouts.app')

@section('header', 'Purchase Orders Management')

@push('styles')
<style>
    div.dt-container div.dt-search input { width: 15rem; }
</style>
@endpush

@section('content')
<div class="bg-white p-6 rounded-md shadow-sm">
    <button id="addPoBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
        Add Purchase Order
    </button>

    <table id="po-table" class="table table-bordered table-striped w-full">
        <thead>
            <tr>
                <th>No</th>
                <th>Supplier</th>
                <th>Invoice/PO No</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Order Date</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>

@include('procurement.purchase-orders.modal')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function () {
    var table = $('#po-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '{{ route('procurement.purchase-orders.index') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'supplier_name', name: 'supplier.name' },
            { data: 'invoice_no', name: 'invoice_no' },
            { data: 'total_amount', name: 'total_amount' },
            { data: 'status', name: 'status' },
            { data: 'ordered_at', name: 'ordered_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        columnDefs: [{ targets: '_all', className: 'py-3 align-middle' }],
        dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>'
    });

    // --- LOGIKA FORM DINAMIS ---
    let itemIndex = 0;
    function addItemRow(item = null) {
        const template = $('#item-template').html().replace(/INDEX/g, itemIndex);
        const newRow = $(template);
        if (item) {
            newRow.find('.item-id').val(item.inventory_item_id);
            newRow.find('.item-qty').val(item.qty_ordered);
            newRow.find('.item-cost').val(item.unit_cost);
        }
        $('#items-container').append(newRow);
        itemIndex++;
    }
    $('#addItemBtn').on('click', function() { addItemRow(); });
    $('#items-container').on('click', '.remove-item-btn', function() { $(this).closest('.item-row').remove(); });

    // --- LOGIKA MODAL ---
    $('#addPoBtn').on('click', function () {
        $('#poForm')[0].reset();
        $('#items-container').empty();
        itemIndex = 0;
        addItemRow(); // Tambah satu baris kosong
        $('.error-message').text('');
        $('#modal_title').text('Add New Purchase Order');
        $('#submitBtn').text('Save Purchase Order');
        $('#form_method').val('POST');
        $('#poForm').attr('action', '{{ route('procurement.purchase-orders.store') }}');
        $('#poModal').removeClass('hidden');
    });

    $('#po-table').on('click', '.edit-btn', function () {
        var poId = $(this).data('id');
        var url = `/procurement/purchase-orders/${poId}/edit`;
        
        $('#poForm')[0].reset();
        $('#items-container').empty();
        itemIndex = 0;
        $('.error-message').text('');

        $.ajax({
            url: url,
            method: 'GET',
            success: function(data) {
                $('#modal_title').text('Edit Purchase Order');
                $('#submitBtn').text('Update Purchase Order');
                $('#form_method').val('PUT');
                $('#poForm').attr('action', `/procurement/purchase-orders/${poId}`);

                // Isi data utama
                $('#po_id').val(data.id);
                $('#supplier_id').val(data.supplier_id);
                $('#invoice_no').val(data.invoice_no);
                $('#status').val(data.status);
                $('#ordered_at').val(data.ordered_at);

                // Loop dan tambahkan item yang ada
                if (data.items && data.items.length > 0) {
                    data.items.forEach(item => addItemRow(item));
                } else {
                    addItemRow(); // Jika tidak ada item, tambahkan satu baris kosong
                }
                
                $('#poModal').removeClass('hidden');
            },
            error: function() {
                Swal.fire('Error', 'Could not fetch purchase order data.', 'error');
            }
        });
    });

    $('.close-modal').on('click', function () { $('#poModal').addClass('hidden'); });

    // --- SUBMIT & DELETE (AJAX) ---
    $('#poForm').on('submit', function (e) {
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
                $('#poModal').addClass('hidden');
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: response.success, showConfirmButton: false, timer: 3000 });
                table.ajax.reload();
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    Swal.fire({ icon: 'error', title: 'Validation Error', text: 'Please check the form for errors.' });
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        $('#' + key.replace(/\./g, '_') + '_error').text(value[0]);
                    });
                } else {
                     Swal.fire({ icon: 'error', title: 'Oops...', text: xhr.responseJSON.error || 'Something went wrong!' });
                }
            }
        });
    });

    $('#po-table').on('click', '.delete-btn', function (e) {
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
                         Swal.fire('Failed!', xhr.responseJSON.error || 'There was a problem deleting the PO.', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush


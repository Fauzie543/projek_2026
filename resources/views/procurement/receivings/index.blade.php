@extends('layouts.app')

@section('header', 'Goods Receiving')

@push('styles')
<style>
    div.dt-container div.dt-search input { width: 15rem; }
</style>
@endpush

@section('content')
<div class="bg-white p-6 rounded-md shadow-sm">
    <button id="addReceivingBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
        Create Goods Receiving Note
    </button>

    <table id="receivings-table" class="table table-bordered table-striped w-full">
        <thead>
            <tr>
                <th>No</th>
                <th>Date</th>
                <th>PO Number</th>
                <th>Supplier</th>
                <th>Received By</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal untuk Receiving -->
<div id="receivingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 class="text-xl font-semibold">New Goods Receiving</h3>
            <button class="text-gray-500 hover:text-gray-800 text-2xl close-modal">&times;</button>
        </div>

        <form id="receivingForm">
            @csrf
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="purchase_id" class="block text-sm font-medium text-gray-700">Select Purchase Order (PO)</label>
                        <select name="purchase_id" id="purchase_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="" disabled selected>Select an ordered PO</option>
                            @isset($purchaseOrders)
                                @foreach($purchaseOrders as $po)
                                    <option value="{{ $po->id }}">
                                        {{ $po->invoice_no ?? 'PO #'.$po->id }} - {{ $po->supplier->name }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                        <span id="purchase_id_error" class="text-red-500 text-xs error-message"></span>
                    </div>
                    <div>
                        <label for="received_date" class="block text-sm font-medium text-gray-700">Receiving Date</label>
                        <input type="date" name="received_date" id="received_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <span id="received_date_error" class="text-red-500 text-xs error-message"></span>
                    </div>
                </div>
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                    <textarea name="notes" id="notes" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                </div>

                <!-- Items Section -->
                <div class="border-t pt-4">
                    <h4 class="text-lg font-medium mb-2">Items to Receive</h4>
                    <div id="items-container" class="space-y-3">
                        <p class="text-gray-500">Select a Purchase Order to load items.</p>
                    </div>
                    <span id="items_error" class="text-red-500 text-xs error-message"></span>
                </div>
            </div>

            <div class="flex justify-end items-center border-t pt-4 mt-4">
                <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2 close-modal">Cancel</button>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Submit Receiving</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function () {
    var table = $('#receivings-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '{{ route('procurement.receivings.index') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'received_date', name: 'received_date' },
            { data: 'po_invoice', name: 'purchase.invoice_no' },
            { data: 'supplier_name', name: 'supplier.name' },
            { data: 'received_by', name: 'receiver.name' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        columnDefs: [{ targets: '_all', className: 'py-3 align-middle' }],
        dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>'
    });

    // --- LOGIKA MODAL ---
    $('#addReceivingBtn').on('click', function () {
        $('#receivingForm')[0].reset();
        $('#items-container').html('<p class="text-gray-500">Select a Purchase Order to load items.</p>');
        $('.error-message').text('');
        $('#receivingModal').removeClass('hidden');
    });

    $('.close-modal').on('click', function () {
        $('#receivingModal').addClass('hidden');
    });

    // --- LOGIKA PEMILIHAN PO ---
    $('#purchase_id').on('change', function() {
        var poId = $(this).val();
        if (!poId) return;

        var url = `/procurement/purchase-orders/${poId}/items-for-receiving`;
        $('#items-container').html('<p class="text-gray-500">Loading items...</p>');

        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                var itemsContainer = $('#items-container');
                itemsContainer.empty();
                if (response.items && response.items.length > 0) {
                    $.each(response.items, function(index, item) {
                        var itemHtml = `
                            <div class="item-row grid grid-cols-5 gap-3 items-center p-2 border rounded-md">
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-500">${item.inventory_item.name}</label>
                                    <input type="hidden" name="items[${index}][inventory_item_id]" value="${item.inventory_item_id}">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500">Qty Ordered</label>
                                    <input type="text" value="${item.qty_ordered}" class="mt-1 block w-full bg-gray-100 border-gray-300 rounded-md shadow-sm" readonly>
                                </div>
                                <div>
                                    <label for="items[${index}][qty_received]" class="block text-xs text-gray-500">Qty Received</label>
                                    <input type="number" name="items[${index}][qty_received]" value="${item.qty_ordered}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required step="any" min="0">
                                </div>
                                <div>
                                    <label for="items[${index}][unit_cost]" class="block text-xs text-gray-500">Unit Cost</label>
                                    <input type="number" name="items[${index}][unit_cost]" value="${item.unit_cost}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required step="any" min="0">
                                </div>
                            </div>
                        `;
                        itemsContainer.append(itemHtml);
                    });
                } else {
                    itemsContainer.html('<p class="text-red-500">This Purchase Order has no items.</p>');
                }
            },
            error: function() {
                $('#items-container').html('<p class="text-red-500">Failed to load items.</p>');
            }
        });
    });
    
    // --- LOGIKA SUBMIT FORM ---
    $('#receivingForm').on('submit', function(e) {
        e.preventDefault();
        $('.error-message').text('');
        var url = '{{ route("procurement.receivings.store") }}';
        var formData = $(this).serialize();

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (response) {
                $('#receivingModal').addClass('hidden');
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
});
</script>
@endpush


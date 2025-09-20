<!-- Modal Background -->
<div id="poModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 id="modal_title" class="text-xl font-semibold">Add New Purchase Order</h3>
            <button class="text-gray-500 hover:text-gray-800 text-2xl close-modal">&times;</button>
        </div>

        <form id="poForm">
            @csrf
            <input type="hidden" name="_method" id="form_method" value="POST">
            <input type="hidden" name="po_id" id="po_id">

            <div class="space-y-4">
                 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label for="supplier_id" class="block text-sm font-medium text-gray-700">Supplier</label>
                        <select name="supplier_id" id="supplier_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="" disabled selected>Select a supplier</option>
                            @isset($suppliers)
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            @endisset
                        </select>
                        <span id="supplier_id_error" class="text-red-500 text-xs error-message"></span>
                    </div>
                    <div>
                        <label for="ordered_at" class="block text-sm font-medium text-gray-700">Order Date</label>
                        <input type="date" name="ordered_at" id="ordered_at" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <span id="ordered_at_error" class="text-red-500 text-xs error-message"></span>
                    </div>
                     <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="pending">Pending</option>
                            <option value="ordered">Ordered</option>
                            <option value="received">Received</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <span id="status_error" class="text-red-500 text-xs error-message"></span>
                    </div>
                </div>
                 <div>
                    <label for="invoice_no" class="block text-sm font-medium text-gray-700">Invoice / PO Number (Optional)</label>
                    <input type="text" name="invoice_no" id="invoice_no" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <!-- Dynamic Items Section -->
                <div class="border-t pt-4">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="text-lg font-medium">Items to Purchase</h4>
                        <button type="button" id="addItemBtn" class="bg-green-500 text-white px-3 py-1 text-sm rounded">Add Item</button>
                    </div>
                    <div id="items-container" class="space-y-2">
                        <!-- Item dinamis -->
                    </div>
                    <span id="items_error" class="text-red-500 text-xs error-message"></span>
                </div>
            </div>

            <div class="flex justify-end items-center border-t pt-4 mt-4">
                <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2 close-modal">Cancel</button>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Save Purchase Order</button>
            </div>
        </form>
    </div>
</div>

<!-- Template untuk baris item -->
<template id="item-template">
    <div class="item-row grid grid-cols-1 md:grid-cols-4 gap-2 items-center">
        <div class="md:col-span-2">
            <select name="items[INDEX][inventory_item_id]" class="item-id block w-full border-gray-300 rounded-md shadow-sm" required>
                <option value="" disabled selected>Select item</option>
                @isset($inventoryItems)
                    @foreach($inventoryItems as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                @endisset
            </select>
        </div>
        <input type="number" name="items[INDEX][qty_ordered]" class="item-qty block w-full border-gray-300 rounded-md shadow-sm" placeholder="Qty" required min="0" step="any">
        <div class="flex items-center space-x-2">
            <input type="number" name="items[INDEX][unit_cost]" class="item-cost flex-grow block w-full border-gray-300 rounded-md shadow-sm" placeholder="Unit Cost" required min="0" step="any">
            <button type="button" class="remove-item-btn bg-red-500 text-white p-2 rounded">&times;</button>
        </div>
    </div>
</template>


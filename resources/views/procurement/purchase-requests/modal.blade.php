<!-- Modal Background -->
<div id="prModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <!-- Modal Content -->
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-3xl max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 id="modal_title" class="text-xl font-semibold">New Purchase Request</h3>
            <button class="text-gray-500 hover:text-gray-800 text-2xl close-modal">&times;</button>
        </div>

        <!-- Modal Body -->
        <form id="prForm">
            @csrf
            <input type="hidden" name="_method" id="form_method" value="POST">
            <input type="hidden" name="pr_id" id="pr_id">

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="request_date" class="block text-sm font-medium text-gray-700">Request Date</label>
                        <input type="date" name="request_date" id="request_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <span id="request_date_error" class="text-red-500 text-xs error-message"></span>
                    </div>
                    <div>
                        <label for="needed_by_date" class="block text-sm font-medium text-gray-700">Needed By Date</label>
                        <input type="date" name="needed_by_date" id="needed_by_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <span id="needed_by_date_error" class="text-red-500 text-xs error-message"></span>
                    </div>
                </div>
                 <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">General Notes (Optional)</label>
                    <textarea name="notes" id="notes" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                </div>

                <!-- Dynamic Items Section -->
                <div class="border-t pt-4">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="text-lg font-medium">Requested Items</h4>
                        <button type="button" id="addItemBtn" class="bg-green-500 text-white px-3 py-1 text-sm rounded">Add Item</button>
                    </div>
                    <div id="items-container" class="space-y-2">
                        <!-- Item dinamis akan ditambahkan di sini -->
                    </div>
                    <span id="items_error" class="text-red-500 text-xs error-message"></span>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end items-center border-t pt-4 mt-4">
                <button type="button" id="cancelBtn" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2">
                    Cancel
                </button>
                <button type="submit" id="submitBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Template untuk baris item (disembunyikan) -->
<template id="item-template">
    <div class="item-row grid grid-cols-1 md:grid-cols-3 gap-2 items-center">
        <div class="md:col-span-2 grid grid-cols-2 gap-2">
            <select name="items[INDEX][inventory_item_id]" class="item-id block w-full border-gray-300 rounded-md shadow-sm" required>
                <option value="" disabled selected>Select item</option>
                @isset($inventoryItems)
                    @foreach($inventoryItems as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                @endisset
            </select>
            <input type="number" name="items[INDEX][qty]" class="item-qty block w-full border-gray-300 rounded-md shadow-sm" placeholder="Qty" required min="0" step="any">
        </div>
        <div class="flex items-center space-x-2">
            <input type="text" name="items[INDEX][notes]" class="item-notes flex-grow block w-full border-gray-300 rounded-md shadow-sm" placeholder="Item notes (optional)">
            <button type="button" class="remove-item-btn bg-red-500 text-white p-2 rounded">&times;</button>
        </div>
    </div>
</template>

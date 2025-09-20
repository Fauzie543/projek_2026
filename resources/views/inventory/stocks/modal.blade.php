<!-- Modal Background -->
<div id="stockModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <!-- Modal Content -->
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg">
        <!-- Modal Header -->
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 id="modal_title" class="text-xl font-semibold">Stock Adjustment</h3>
            <button class="text-gray-500 hover:text-gray-800 text-2xl close-modal">&times;</button>
        </div>

        <!-- Modal Body -->
        <form id="stockForm">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="inventory_item_id" class="block text-sm font-medium text-gray-700">Inventory Item</label>
                    <select name="inventory_item_id" id="inventory_item_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="" disabled selected>Select an item</option>
                        @isset($items)
                            @foreach($items as $item)
                                <option value="{{ $item->id }}">{{ $item->name }} ({{$item->sku}})</option>
                            @endforeach
                        @endisset
                    </select>
                    <span id="inventory_item_id_error" class="text-red-500 text-xs error-message"></span>
                </div>
                <div>
                    <label for="qty" class="block text-sm font-medium text-gray-700">Quantity (Qty)</label>
                    <input type="number" name="qty" id="qty" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required min="0" step="any">
                    <span id="qty_error" class="text-red-500 text-xs error-message"></span>
                </div>
                <div>
                    <label for="note" class="block text-sm font-medium text-gray-700">Note / Reason</label>
                    <textarea name="note" id="note" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    <span id="note_error" class="text-red-500 text-xs error-message"></span>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end items-center border-t pt-4 mt-4">
                <button type="button" id="cancelBtn" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2">
                    Cancel
                </button>
                <button type="submit" id="submitBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>
    
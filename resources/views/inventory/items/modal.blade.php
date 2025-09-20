<!-- Modal Background -->
<div id="itemModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <!-- Modal Content -->
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg">
        <!-- Modal Header -->
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 id="modal_title" class="text-xl font-semibold">Add New Item</h3>
            <button class="text-gray-500 hover:text-gray-800 text-2xl close-modal">&times;</button>
        </div>

        <!-- Modal Body -->
        <form id="itemForm">
            @csrf
            <input type="hidden" name="_method" id="form_method" value="POST">
            <input type="hidden" name="item_id" id="item_id">

            <div class="space-y-4">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="sku" class="block text-sm font-medium text-gray-700">SKU / Code</label>
                        <input type="text" name="sku" id="sku" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <span id="sku_error" class="text-red-500 text-xs error-message"></span>
                    </div>
                    <div>
                        <label for="unit" class="block text-sm font-medium text-gray-700">Unit (Satuan)</label>
                        <input type="text" name="unit" id="unit" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="e.g., kg, pcs, liter" required>
                        <span id="unit_error" class="text-red-500 text-xs error-message"></span>
                    </div>
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Item Name</label>
                    <input type="text" name="name" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    <span id="name_error" class="text-red-500 text-xs error-message"></span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                        <input type="text" name="category" id="category" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="e.g., Pakan, Vitamin">
                        <span id="category_error" class="text-red-500 text-xs error-message"></span>
                    </div>
                     <div>
                        <label for="reorder_point" class="block text-sm font-medium text-gray-700">Re-order Point</label>
                        <input type="number" name="reorder_point" id="reorder_point" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" min="0">
                        <span id="reorder_point_error" class="text-red-500 text-xs error-message"></span>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end items-center border-t pt-4 mt-4">
                <button type="button" id="cancelBtn" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2">
                    Cancel
                </button>
                <button type="submit" id="submitBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Save Item
                </button>
            </div>
        </form>
    </div>
</div>

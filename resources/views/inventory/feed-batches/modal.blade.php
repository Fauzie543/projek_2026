<!-- Modal Background -->
<div id="batchModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <!-- Modal Content -->
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg">
        <!-- Modal Header -->
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 id="modal_title" class="text-xl font-semibold">Produce New Feed Batch</h3>
            <button class="text-gray-500 hover:text-gray-800 text-2xl close-modal">&times;</button>
        </div>

        <!-- Modal Body -->
        <form id="batchForm">
            @csrf
            <div class="space-y-4">
                
                <div>
                    <label for="recipe_id" class="block text-sm font-medium text-gray-700">Feed Recipe</label>
                    <select name="recipe_id" id="recipe_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="" disabled selected>Select a recipe</option>
                        @isset($recipes)
                            @foreach($recipes as $recipe)
                                <option value="{{ $recipe->id }}">{{ $recipe->name }}</option>
                            @endforeach
                        @endisset
                    </select>
                    <span id="recipe_id_error" class="text-red-500 text-xs error-message"></span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="qty_kg" class="block text-sm font-medium text-gray-700">Quantity to Produce (Kg)</label>
                        <input type="number" name="qty_kg" id="qty_kg" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required min="0" step="any">
                        <span id="qty_kg_error" class="text-red-500 text-xs error-message"></span>
                    </div>
                     <div>
                        <label for="produced_at" class="block text-sm font-medium text-gray-700">Production Date</label>
                        <input type="date" name="produced_at" id="produced_at" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <span id="produced_at_error" class="text-red-500 text-xs error-message"></span>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end items-center border-t pt-4 mt-4">
                <button type="button" id="cancelBtn" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2">
                    Cancel
                </button>
                <button type="submit" id="submitBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Produce Batch
                </button>
            </div>
        </form>
    </div>
</div>

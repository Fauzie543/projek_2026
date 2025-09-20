<!-- Modal Background -->
<div id="recipeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <!-- Modal Content -->
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 id="modal_title" class="text-xl font-semibold">Add New Feed Recipe</h3>
            <button class="text-gray-500 hover:text-gray-800 text-2xl close-modal">&times;</button>
        </div>

        <!-- Modal Body -->
        <form id="recipeForm">
            @csrf
            <input type="hidden" name="_method" id="form_method" value="POST">
            <input type="hidden" name="recipe_id" id="recipe_id">

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Recipe Name</label>
                        <input type="text" name="name" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <span id="name_error" class="text-red-500 text-xs error-message"></span>
                    </div>
                    <div>
                        <label for="cost_per_kg" class="block text-sm font-medium text-gray-700">Est. Cost per Kg</label>
                        <input type="text" name="cost_per_kg" id="cost_per_kg" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <span id="cost_per_kg_error" class="text-red-500 text-xs error-message"></span>
                    </div>
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description (Optional)</label>
                    <textarea name="description" id="description" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                </div>

                <!-- Dynamic Components Section -->
                <div class="border-t pt-4">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="text-lg font-medium">Components</h4>
                        <button type="button" id="addComponentBtn" class="bg-green-500 text-white px-3 py-1 text-sm rounded">Add Ingredient</button>
                    </div>
                    <div id="components-container" class="space-y-2">
                        <!-- Komponen dinamis akan ditambahkan di sini oleh JavaScript -->
                    </div>
                    <span id="components_error" class="text-red-500 text-xs error-message"></span>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end items-center border-t pt-4 mt-4">
                <button type="button" id="cancelBtn" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2">
                    Cancel
                </button>
                <button type="submit" id="submitBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Save Recipe
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Template untuk baris komponen (disembunyikan) -->
<template id="component-template">
    <div class="component-row flex items-center space-x-2">
        <div class="flex-grow">
            <select name="components[INDEX][item_id]" class="component-item block w-full border-gray-300 rounded-md shadow-sm" required>
                <option value="" disabled selected>Select ingredient</option>
                @isset($inventoryItems)
                    @foreach($inventoryItems as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                @endisset
            </select>
        </div>
        <div class="w-1/4">
            <input type="number" name="components[INDEX][qty]" class="component-qty block w-full border-gray-300 rounded-md shadow-sm" placeholder="Qty" required min="0" step="any">
        </div>
        <button type="button" class="remove-component-btn bg-red-500 text-white p-2 rounded">&times;</button>
    </div>
</template>

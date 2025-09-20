<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\FeedRecipe;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class FeedRecipeController extends Controller
{
    public function index()
    {
        // Ambil data inventory items untuk dropdown komponen di modal
        $inventoryItems = InventoryItem::orderBy('name')->get();
        return view('inventory.feed-recipes.index', compact('inventoryItems'));
    }

    public function data()
    {
        $recipes = FeedRecipe::with('creator');

        return DataTables::of($recipes)
            ->addIndexColumn()
            ->addColumn('components_list', function ($recipe) {
                // Format komponen JSON menjadi daftar yang mudah dibaca
                if (empty($recipe->components)) {
                    return 'No components';
                }
                $list = '<ul class="list-disc list-inside">';
                foreach ($recipe->components as $component) {
                    $list .= '<li>' . ($component['name'] ?? 'N/A') . ': ' . ($component['qty'] ?? 0) . ' ' . ($component['unit'] ?? '') . '</li>';
                }
                $list .= '</ul>';
                return $list;
            })
            ->editColumn('cost_per_kg', function ($recipe) {
                return 'Rp ' . number_format($recipe->cost_per_kg, 0, ',', '.');
            })
            ->addColumn('action', function ($recipe) {
                $deleteUrl = route('inventory.feed-recipes.destroy', $recipe->id);
                return '
                    <a href="javascript:void(0)" data-id="' . $recipe->id . '" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-xs edit-btn">Edit</a>
                    <a href="javascript:void(0)" data-url="' . $deleteUrl . '" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-xs delete-btn ml-2">Delete</a>
                ';
            })
            ->rawColumns(['action', 'components_list'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $this->validateRecipe($request);
        $validated['created_by'] = Auth::id();
        FeedRecipe::create($validated);
        return response()->json(['success' => 'Feed Recipe created successfully.']);
    }

    public function edit(FeedRecipe $feedRecipe)
    {
        return response()->json($feedRecipe);
    }

    public function update(Request $request, FeedRecipe $feedRecipe)
    {
        $validated = $this->validateRecipe($request, $feedRecipe->id);
        $validated['updated_by'] = Auth::id();
        $feedRecipe->update($validated);
        return response()->json(['success' => 'Feed Recipe updated successfully.']);
    }

    public function destroy(FeedRecipe $feedRecipe)
    {
        try {
            if ($feedRecipe->batches()->exists()) {
                return response()->json(['error' => 'Cannot delete recipe that has been used in a feed batch.'], 403);
            }
            $feedRecipe->delete();
            return response()->json(['success' => 'Feed Recipe has been deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete feed recipe.'], 500);
        }
    }

    // Helper function untuk validasi agar tidak duplikat kode
    private function validateRecipe(Request $request, $ignoreId = null)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255', Rule::unique('feed_recipes')->ignore($ignoreId)],
            'description' => ['nullable', 'string'],
            'cost_per_kg' => ['required', 'numeric', 'min:0'],
            'components' => ['required', 'array', 'min:1'],
            'components.*.item_id' => ['required', 'integer', 'exists:inventory_items,id'],
            'components.*.qty' => ['required', 'numeric', 'min:0.01'],
        ];

        $validated = $request->validate($rules);
        
        // Proses komponen untuk menyimpan data tambahan (name, unit)
        $processedComponents = [];
        foreach ($validated['components'] as $component) {
            $item = InventoryItem::find($component['item_id']);
            $processedComponents[] = [
                'item_id' => $item->id,
                'name' => $item->name,
                'qty' => $component['qty'],
                'unit' => $item->unit,
            ];
        }
        $validated['components'] = $processedComponents;

        return $validated;
    }
}
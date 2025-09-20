<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('inventory.items.index');
    }

    public function data()
    {
        // Eager load relasi 'batches' untuk kalkulasi stok
        $items = InventoryItem::with('batches');

        return DataTables::of($items)
            ->addIndexColumn()
            ->addColumn('total_stock', function ($item) {
                // Kalkulasi total stok dari semua batch milik item ini
                return $item->batches->sum('qty') . ' ' . $item->unit;
            })
            ->addColumn('action', function ($item) {
                $deleteUrl = route('inventory.items.destroy', $item->id);
                return '
                    <a href="javascript:void(0)" data-id="' . $item->id . '" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-xs edit-btn">Edit</a>
                    <a href="javascript:void(0)" data-url="' . $deleteUrl . '" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-xs delete-btn ml-2">Delete</a>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:255', 'unique:'.InventoryItem::class],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:50'],
            'reorder_point' => ['nullable', 'numeric', 'min:0'],
        ]);

        InventoryItem::create($validated);

        return response()->json(['success' => 'Item created successfully.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InventoryItem $item)
    {
        return response()->json($item);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InventoryItem $item)
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:255', Rule::unique('inventory_items')->ignore($item->id)],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:50'],
            'reorder_point' => ['nullable', 'numeric', 'min:0'],
        ]);

        $item->update($validated);

        return response()->json(['success' => 'Item updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventoryItem $item)
    {
        try {
            // PENTING: Cegah penghapusan item jika masih ada stok
            if ($item->batches()->exists()) {
                return response()->json(['error' => 'Cannot delete item with active stock batches.'], 403);
            }

            $item->delete();
            return response()->json(['success' => 'Item has been deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete item.'], 500);
        }
    }
}
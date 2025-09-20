<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\StockBatch;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class StockBatchController extends Controller
{
    public function index()
    {
        // Ambil data items dan suppliers untuk dikirim ke dropdown di modal
        $items = InventoryItem::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        return view('inventory.stock-batches.index', compact('items', 'suppliers'));
    }

    public function data()
    {
        // Eager load relasi untuk efisiensi
        $batches = StockBatch::with(['item', 'supplier']);

        return DataTables::of($batches)
            ->addIndexColumn()
            ->addColumn('item_name', function ($batch) {
                return $batch->item->name ?? 'N/A';
            })
            ->addColumn('supplier_name', function ($batch) {
                return $batch->supplier->name ?? 'N/A';
            })
            ->editColumn('unit_cost', function ($batch) {
                return 'Rp ' . number_format($batch->unit_cost, 0, ',', '.');
            })
             ->editColumn('qty', function ($batch) {
                return $batch->qty . ' ' . ($batch->item->unit ?? '');
            })
            ->addColumn('action', function ($batch) {
                $deleteUrl = route('inventory.stock-batches.destroy', $batch->id);
                return '
                    <a href="javascript:void(0)" data-id="' . $batch->id . '" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-xs edit-btn">Edit</a>
                    <a href="javascript:void(0)" data-url="' . $deleteUrl . '" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-xs delete-btn ml-2">Delete</a>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'inventory_item_id' => ['required', 'integer', 'exists:inventory_items,id'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'qty' => ['required', 'numeric', 'min:0'],
            'unit_cost' => ['required', 'numeric', 'min:0'],
            'received_at' => ['required', 'date'],
            'expiry_date' => ['nullable', 'date', 'after_or_equal:received_at'],
            'note' => ['nullable', 'string'],
        ]);

        StockBatch::create($validated);

        return response()->json(['success' => 'Stock batch created successfully.']);
    }

    public function edit(StockBatch $stockBatch)
    {
        return response()->json($stockBatch);
    }

    public function update(Request $request, StockBatch $stockBatch)
    {
        $validated = $request->validate([
            'inventory_item_id' => ['required', 'integer', 'exists:inventory_items,id'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'qty' => ['required', 'numeric', 'min:0'],
            'unit_cost' => ['required', 'numeric', 'min:0'],
            'received_at' => ['required', 'date'],
            'expiry_date' => ['nullable', 'date', 'after_or_equal:received_at'],
            'note' => ['nullable', 'string'],
        ]);

        $stockBatch->update($validated);

        return response()->json(['success' => 'Stock batch updated successfully.']);
    }

    public function destroy(StockBatch $stockBatch)
    {
        try {
            // Logika pencegahan penghapusan bisa ditambahkan di sini
            // Misalnya, jika batch sudah digunakan dalam transaksi keluar.
            if ($stockBatch->transactions()->where('type', 'out')->exists()) {
                 return response()->json(['error' => 'Cannot delete batch that has been used.'], 403);
            }

            $stockBatch->delete();
            return response()->json(['success' => 'Stock batch has been deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete stock batch.'], 500);
        }
    }
}
<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StockController extends Controller
{
    public function index()
    {
        // Mengambil semua item untuk dropdown di modal
        $items = InventoryItem::orderBy('name')->get();
        return view('inventory.stocks.index', compact('items'));
    }

    /**
     * Mengambil data untuk DataTables.
     */
    public function data()
    {
        // Mengambil item dan menghitung total stok dari transaksi
        $items = InventoryItem::withSum(['transactions as total_in' => function ($query) {
            $query->where('type', 'in');
        }], 'qty')
        ->withSum(['transactions as total_out' => function ($query) {
            $query->where('type', 'out');
        }], 'qty');

        return DataTables::of($items)
            ->addIndexColumn()
            ->addColumn('current_stock', function ($item) {
                // Stok saat ini = (total masuk - total keluar)
                $currentStock = ($item->total_in ?? 0) - ($item->total_out ?? 0);
                $color = $currentStock <= $item->reorder_point ? 'text-red-600 font-bold' : '';
                return '<span class="' . $color . '">' . $currentStock . ' ' . $item->unit . '</span>';
            })
            ->rawColumns(['current_stock'])
            ->make(true);
    }

    /**
     * Menangani aksi stok masuk manual.
     */
    public function stockIn(Request $request)
    {
        $validated = $request->validate([
            'inventory_item_id' => ['required', 'integer', 'exists:inventory_items,id'],
            'qty' => ['required', 'numeric', 'min:0.01'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        StockTransaction::create([
            'inventory_item_id' => $validated['inventory_item_id'],
            'type' => 'in',
            'qty' => $validated['qty'],
            'note' => 'Manual Stock In: ' . $validated['note'],
            'performed_by' => Auth::id(),
            'performed_at' => now(),
        ]);

        return response()->json(['success' => 'Stock added successfully.']);
    }

    /**
     * Menangani aksi stok keluar manual.
     */
    public function stockOut(Request $request)
    {
        $validated = $request->validate([
            'inventory_item_id' => ['required', 'integer', 'exists:inventory_items,id'],
            'qty' => ['required', 'numeric', 'min:0.01'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        // Validasi agar stok tidak minus
        $item = InventoryItem::find($validated['inventory_item_id']);
        $currentStock = $item->getCurrentStock();

        if ($validated['qty'] > $currentStock) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'qty' => ['Quantity cannot be greater than current stock (' . $currentStock . ').']
                ]
            ], 422);
        }

        StockTransaction::create([
            'inventory_item_id' => $validated['inventory_item_id'],
            'type' => 'out',
            'qty' => $validated['qty'],
            'note' => 'Manual Stock Out: ' . $validated['note'],
            'performed_by' => Auth::id(),
            'performed_at' => now(),
        ]);

        return response()->json(['success' => 'Stock removed successfully.']);
    }
}
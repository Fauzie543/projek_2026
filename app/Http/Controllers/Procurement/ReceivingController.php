<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Receiving;
use App\Models\StockBatch;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ReceivingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil PO yang statusnya 'ordered' untuk dropdown
        $purchaseOrders = Purchase::where('status', 'ordered')->orderBy('ordered_at', 'desc')->get();
        return view('procurement.receivings.index', compact('purchaseOrders'));
    }

    public function data()
    {
        $receivings = Receiving::with(['purchase', 'supplier', 'receiver']);

        return DataTables::of($receivings)
            ->addIndexColumn()
            ->addColumn('po_invoice', function ($receiving) {
                return $receiving->purchase->invoice_no ?? 'PO #' . $receiving->purchase_id;
            })
            ->addColumn('supplier_name', function ($receiving) {
                return $receiving->supplier->name ?? 'N/A';
            })
            ->addColumn('received_by', function ($receiving) {
                return $receiving->receiver->name ?? 'N/A';
            })
            ->addColumn('action', function ($receiving) {
                // Aksi view detail bisa ditambahkan di sini
                return '<a href="#" class="text-blue-500">View Details</a>';
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
            'purchase_id' => ['required', 'integer', 'exists:purchases,id'],
            'received_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.inventory_item_id' => ['required', 'integer', 'exists:inventory_items,id'],
            'items.*.qty_received' => ['required', 'numeric', 'min:0'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            DB::transaction(function () use ($validated, $request) {
                $purchase = Purchase::findOrFail($validated['purchase_id']);

                $receiving = Receiving::create([
                    'purchase_id' => $purchase->id,
                    'supplier_id' => $purchase->supplier_id,
                    'received_date' => $validated['received_date'],
                    'notes' => $validated['notes'],
                    'received_by_id' => Auth::id(),
                ]);

                foreach ($validated['items'] as $itemData) {
                    // 1. Catat item yang diterima
                    $receiving->items()->create($itemData);

                    // 2. Buat Stock Batch baru
                    $batch = StockBatch::create([
                        'inventory_item_id' => $itemData['inventory_item_id'],
                        'supplier_id' => $purchase->supplier_id,
                        'qty' => $itemData['qty_received'],
                        'unit_cost' => $itemData['unit_cost'],
                        'received_at' => $validated['received_date'],
                    ]);

                    // 3. Buat Transaksi Stok "IN"
                    StockTransaction::create([
                        'inventory_item_id' => $itemData['inventory_item_id'],
                        'batch_id' => $batch->id,
                        'type' => 'in',
                        'qty' => $itemData['qty_received'],
                        'related_type' => Receiving::class,
                        'related_id' => $receiving->id,
                        'performed_by' => Auth::id(),
                        'performed_at' => now(),
                        'note' => 'Received from PO #' . $purchase->id,
                    ]);
                }

                // 4. Update status PO menjadi 'received'
                $purchase->update(['status' => 'received', 'received_at' => now()]);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred during receiving process: ' . $e->getMessage()], 500);
        }

        return response()->json(['success' => 'Goods received successfully. Stock has been updated.']);
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
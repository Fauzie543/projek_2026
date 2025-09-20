<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $inventoryItems = InventoryItem::orderBy('name')->get(); // Kirim data item ke view
        return view('procurement.purchase-orders.index', compact('suppliers', 'inventoryItems'));
    }
    
    public function data()
    {
        $purchases = Purchase::with(['supplier', 'creator']);

        return DataTables::of($purchases)
            ->addIndexColumn()
            ->addColumn('supplier_name', function ($purchase) {
                return $purchase->supplier->name ?? 'N/A';
            })
            ->editColumn('total_amount', function ($purchase) {
                return 'Rp ' . number_format($purchase->total_amount, 0, ',', '.');
            })
            ->editColumn('status', function ($purchase) {
                $status = ucfirst($purchase->status);
                $colorClass = 'bg-gray-100 text-gray-800';
                if ($purchase->status == 'ordered') $colorClass = 'bg-blue-100 text-blue-800';
                elseif ($purchase->status == 'received') $colorClass = 'bg-green-100 text-green-800';
                elseif ($purchase->status == 'cancelled') $colorClass = 'bg-red-100 text-red-800';
                return '<span class="' . $colorClass . ' text-xs font-medium me-2 px-2.5 py-0.5 rounded-full">' . $status . '</span>';
            })
            ->addColumn('action', function ($purchase) {
                $deleteUrl = route('procurement.purchase-orders.destroy', $purchase->id);
                return '
                    <a href="javascript:void(0)" data-id="' . $purchase->id . '" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-xs edit-btn">Edit</a>
                    <a href="javascript:void(0)" data-url="' . $deleteUrl . '" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-xs delete-btn ml-2">Delete</a>
                ';
            })
            ->rawColumns(['action', 'status'])
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
        $validated = $this->validatePurchase($request);

        DB::transaction(function () use ($validated) {
            $purchase = Purchase::create([
                'supplier_id' => $validated['supplier_id'],
                'invoice_no' => $validated['invoice_no'],
                'ordered_at' => $validated['ordered_at'],
                'status' => $validated['status'],
                'total_amount' => $validated['total_amount'], // Total sudah dihitung di validasi
                'created_by' => Auth::id(),
            ]);

            foreach ($validated['items'] as $item) {
                $purchase->items()->create($item);
            }
        });

        return response()->json(['success' => 'Purchase Order created successfully.']);
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
    public function edit(Purchase $purchaseOrder)
    {
        // Muat relasi items saat mengambil data untuk form edit
        return response()->json($purchaseOrder->load('items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchase $purchaseOrder)
    {
        if ($purchaseOrder->status == 'received') {
            return response()->json(['error' => 'Cannot edit a received purchase order.'], 403);
        }
        
        $validated = $this->validatePurchase($request, $purchaseOrder->id);

        DB::transaction(function () use ($validated, $purchaseOrder) {
            $purchaseOrder->update([
                'supplier_id' => $validated['supplier_id'],
                'invoice_no' => $validated['invoice_no'],
                'ordered_at' => $validated['ordered_at'],
                'status' => $validated['status'],
                'total_amount' => $validated['total_amount'],
            ]);

            // Hapus item lama, lalu buat yang baru
            $purchaseOrder->items()->delete();
            foreach ($validated['items'] as $item) {
                $purchaseOrder->items()->create($item);
            }
        });

        return response()->json(['success' => 'Purchase Order updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchaseOrder)
    {
        try {
            // Logika pencegahan bisa ditambahkan, misal: PO yang sudah diterima tidak bisa dihapus
            if ($purchaseOrder->status == 'received') {
                return response()->json(['error' => 'Cannot delete a received purchase order.'], 403);
            }

            $purchaseOrder->delete();
            return response()->json(['success' => 'Purchase Order has been deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete purchase order.'], 500);
        }
    }
    
    public function getItemsForReceiving(Purchase $purchaseOrder)
    {
        return response()->json($purchaseOrder->load('items.inventoryItem'));
    }

    private function validatePurchase(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'invoice_no' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in(['pending', 'ordered', 'received', 'cancelled'])],
            'ordered_at' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.inventory_item_id' => ['required', 'integer', 'exists:inventory_items,id'],
            'items.*.qty_ordered' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
        ]);

        // Hitung sub_total dan total_amount di backend
        $totalAmount = 0;
        foreach ($validated['items'] as $index => &$item) { // Gunakan & untuk pass by reference
            $item['sub_total'] = $item['qty_ordered'] * $item['unit_cost'];
            $totalAmount += $item['sub_total'];
        }
        
        $validated['total_amount'] = $totalAmount;
        return $validated;
    }
}
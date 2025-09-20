<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\FeedBatch;
use App\Models\FeedRecipe;
use App\Models\InventoryItem;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class FeedBatchController extends Controller
{
    public function index()
    {
        // Ambil data resep untuk dropdown di modal
        $recipes = FeedRecipe::orderBy('name')->get();
        return view('inventory.feed-batches.index', compact('recipes'));
    }

    public function data()
    {
        $batches = FeedBatch::with('recipe');

        return DataTables::of($batches)
            ->addIndexColumn()
            ->addColumn('recipe_name', function ($batch) {
                return $batch->recipe->name ?? 'N/A';
            })
            ->editColumn('cost_total', function ($batch) {
                return 'Rp ' . number_format($batch->cost_total, 0, ',', '.');
            })
            ->editColumn('produced_at', function ($batch) {
                return \Carbon\Carbon::parse($batch->produced_at)->format('Y-m-d');
            })
            ->addColumn('action', function ($batch) {
                // Biasanya, batch produksi tidak bisa di-edit, hanya bisa dihapus (jika ada kesalahan input)
                $deleteUrl = route('inventory.feed-batches.destroy', $batch->id);
                return '
                    <a href="javascript:void(0)" data-url="' . $deleteUrl . '" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-xs delete-btn">Delete</a>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipe_id' => ['required', 'integer', 'exists:feed_recipes,id'],
            'qty_kg' => ['required', 'numeric', 'min:0.01'],
            'produced_at' => ['required', 'date'],
        ]);

        $recipe = FeedRecipe::findOrFail($validated['recipe_id']);

        try {
            DB::transaction(function () use ($validated, $recipe) {
                // Validasi ketersediaan stok untuk setiap komponen
                foreach ($recipe->components as $component) {
                    $item = InventoryItem::find($component['item_id']);
                    $requiredQty = $component['qty'] * $validated['qty_kg']; // Kebutuhan bahan = qty resep * total produksi
                    $currentStock = $item->getCurrentStock();

                    if ($currentStock < $requiredQty) {
                        // Throw exception untuk menghentikan transaksi dan mengirim pesan error
                        throw new \Exception('Insufficient stock for ' . $item->name . '. Required: ' . $requiredQty . ', Available: ' . $currentStock);
                    }
                }

                // Kalkulasi total biaya
                $totalCost = $recipe->cost_per_kg * $validated['qty_kg'];

                // Buat batch produksi
                $feedBatch = FeedBatch::create([
                    'recipe_id' => $validated['recipe_id'],
                    'produced_at' => $validated['produced_at'],
                    'qty_kg' => $validated['qty_kg'],
                    'cost_total' => $totalCost,
                ]);

                // Buat transaksi stok OUT untuk setiap komponen
                foreach ($recipe->components as $component) {
                    StockTransaction::create([
                        'inventory_item_id' => $component['item_id'],
                        'type' => 'out',
                        'qty' => $component['qty'] * $validated['qty_kg'], // Kurangi stok sesuai total produksi
                        'note' => 'Used for Feed Batch ID: ' . $feedBatch->id,
                        'related_type' => FeedBatch::class,
                        'related_id' => $feedBatch->id,
                        'performed_by' => Auth::id(),
                        'performed_at' => now(),
                    ]);
                }
            });
        } catch (\Exception $e) {
            // Tangkap error (terutama dari validasi stok) dan kirim sebagai respons
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json(['success' => 'Feed Batch produced successfully. Stock has been updated.']);
    }

    public function destroy(FeedBatch $feedBatch)
    {
        try {
            DB::transaction(function () use ($feedBatch) {
                // Hapus transaksi stok yang terkait dengan batch ini
                StockTransaction::where('related_type', FeedBatch::class)
                    ->where('related_id', $feedBatch->id)
                    ->delete();
                
                // Hapus batch itu sendiri
                $feedBatch->delete();
            });

            return response()->json(['success' => 'Feed Batch and related stock transactions have been deleted.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete feed batch.'], 500);
        }
    }
}
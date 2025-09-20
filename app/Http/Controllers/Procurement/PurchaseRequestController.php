<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class PurchaseRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inventoryItems = InventoryItem::orderBy('name')->get();
        return view('procurement.purchase-requests.index', compact('inventoryItems'));
    }

    public function data()
    {
        $requests = PurchaseRequest::with('requester');

        return DataTables::of($requests)
            ->addIndexColumn()
            ->addColumn('requester_name', function ($pr) {
                return $pr->requester->name ?? 'N/A';
            })
            ->editColumn('status', function ($pr) {
                $status = ucfirst($pr->status);
                $colorClass = 'bg-gray-100 text-gray-800'; // Default for pending
                if ($pr->status == 'approved') {
                    $colorClass = 'bg-green-100 text-green-800';
                } elseif ($pr->status == 'rejected') {
                    $colorClass = 'bg-red-100 text-red-800';
                } elseif ($pr->status == 'ordered') {
                    $colorClass = 'bg-blue-100 text-blue-800';
                }
                return '<span class="' . $colorClass . ' text-xs font-medium me-2 px-2.5 py-0.5 rounded-full">' . $status . '</span>';
            })
            ->addColumn('action', function ($pr) {
                $deleteUrl = route('procurement.purchase-requests.destroy', $pr->id);
                return '
                    <a href="javascript:void(0)" data-id="' . $pr->id . '" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-xs edit-btn">Edit</a>
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
        $validated = $this->validateRequest($request);

        DB::transaction(function () use ($validated) {
            $pr = PurchaseRequest::create([
                'request_date' => $validated['request_date'],
                'needed_by_date' => $validated['needed_by_date'],
                'status' => 'pending', // Status awal selalu pending
                'notes' => $validated['notes'],
                'requester_id' => Auth::id(),
            ]);

            foreach ($validated['items'] as $item) {
                $pr->items()->create($item);
            }
        });

        return response()->json(['success' => 'Purchase Request created successfully.']);
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
    public function edit(PurchaseRequest $pr)
    {
        // Load relasi items untuk dikirim ke form edit
        return response()->json($pr->load('items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseRequest $pr)
    {
        // Hanya izinkan update jika status masih 'pending'
        if ($pr->status !== 'pending') {
            return response()->json(['error' => 'Only pending requests can be edited.'], 403);
        }

        $validated = $this->validateRequest($request);

        DB::transaction(function () use ($validated, $pr) {
            $pr->update([
                'request_date' => $validated['request_date'],
                'needed_by_date' => $validated['needed_by_date'],
                'notes' => $validated['notes'],
            ]);

            // Hapus item lama dan tambahkan yang baru (cara paling sederhana untuk sinkronisasi)
            $pr->items()->delete();
            foreach ($validated['items'] as $item) {
                $pr->items()->create($item);
            }
        });

        return response()->json(['success' => 'Purchase Request updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseRequest $pr)
    {
        try {
            if ($pr->status !== 'pending') {
                return response()->json(['error' => 'Only pending requests can be deleted.'], 403);
            }
            $pr->delete(); // Items akan terhapus otomatis karena cascade delete di database
            return response()->json(['success' => 'Purchase Request has been deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete purchase request.'], 500);
        }
    }

    private function validateRequest(Request $request)
    {
        return $request->validate([
            'request_date' => ['required', 'date'],
            'needed_by_date' => ['required', 'date', 'after_or_equal:request_date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.inventory_item_id' => ['required', 'integer', 'exists:inventory_items,id'],
            'items.*.qty' => ['required', 'numeric', 'min:0.01'],
            'items.*.notes' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
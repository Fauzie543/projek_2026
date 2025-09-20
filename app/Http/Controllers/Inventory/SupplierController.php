<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('inventory.suppliers.index');
    }

    public function data()
    {
        $suppliers = Supplier::query();

        return DataTables::of($suppliers)
            ->addIndexColumn()
            ->addColumn('action', function ($supplier) {
                $deleteUrl = route('inventory.suppliers.destroy', $supplier->id);
                return '
                    <a href="javascript:void(0)" data-id="' . $supplier->id . '" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-xs edit-btn">Edit</a>
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
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:'.Supplier::class],
            'address' => ['nullable', 'string'],
        ]);

        Supplier::create($validated);

        return response()->json(['success' => 'Supplier created successfully.']);
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
    public function edit(Supplier $supplier)
    {
        return response()->json($supplier);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('suppliers')->ignore($supplier->id)],
            'address' => ['nullable', 'string'],
        ]);

        $supplier->update($validated);

        return response()->json(['success' => 'Supplier updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        try {
            // Mencegah penghapusan jika supplier terkait dengan data lain
            if ($supplier->purchases()->exists() || $supplier->batches()->exists()) {
                return response()->json(['error' => 'Cannot delete supplier with active purchases or stock batches.'], 403);
            }

            $supplier->delete();
            return response()->json(['success' => 'Supplier has been deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete supplier.'], 500);
        }
    }
}
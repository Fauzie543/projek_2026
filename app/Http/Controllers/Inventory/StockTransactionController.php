<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class StockTransactionController extends Controller
{
    public function index()
    {
        return view('inventory.stock-transactions.index');
    }

    public function data()
    {
        // Eager load relasi untuk efisiensi query
        $transactions = StockTransaction::with(['item', 'user']);

        return DataTables::of($transactions)
            ->addIndexColumn()
            ->editColumn('performed_at', function ($transaction) {
                // Format timestamp menjadi tanggal dan waktu yang mudah dibaca
                return \Carbon\Carbon::parse($transaction->performed_at)->format('Y-m-d H:i');
            })
            ->addColumn('item_name', function ($transaction) {
                return $transaction->item->name ?? 'N/A';
            })
            ->editColumn('type', function ($transaction) {
                // Beri warna pada tipe transaksi agar mudah dibedakan
                $type = Str::upper($transaction->type);
                if ($type == 'IN') {
                    return '<span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full">IN</span>';
                } elseif ($type == 'OUT') {
                    return '<span class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full">OUT</span>';
                } else {
                    return '<span class="bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full">' . $type . '</span>';
                }
            })
            ->addColumn('qty_formatted', function ($transaction) {
                $unit = $transaction->item->unit ?? '';
                return ($transaction->type == 'out' ? '-' : '+') . $transaction->qty . ' ' . $unit;
            })
            ->addColumn('performed_by_name', function ($transaction) {
                return $transaction->user->name ?? 'System';
            })
            ->rawColumns(['type']) // Render HTML untuk kolom 'type'
            ->make(true);
    }
}
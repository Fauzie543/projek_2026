<?php

namespace App\Models;

use App\Models\PurchaseRequest as ModelsPurchaseRequest;
use App\Models\PurchaseRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequestItem extends Model
{
    use HasFactory;

    public $timestamps = false; // Umumnya item detail tidak memerlukan timestamps

    protected $fillable = [
        'purchase_request_id',
        'inventory_item_id',
        'qty',
        'notes',
    ];

    /**
     * Relasi kembali ke Purchase Request induk.
     */
    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    /**
     * Relasi ke data master inventory item.
     */
    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }
}
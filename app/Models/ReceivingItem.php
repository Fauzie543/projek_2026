<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivingItem extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'receiving_id',
        'inventory_item_id',
        'qty_received',
        'unit_cost',
    ];

    public function receiving()
    {
        return $this->belongsTo(Receiving::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockBatch extends Model
{
    protected $fillable = [
        'inventory_item_id', 'supplier_id', 'qty', 'unit_cost',
        'received_at', 'expiry_date', 'note'
    ];

    public $timestamps = false;

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function transactions()
    {
        return $this->hasMany(StockTransaction::class, 'batch_id');
    }
}

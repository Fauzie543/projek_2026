<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    protected $fillable = [
        'inventory_item_id', 'batch_id', 'type', 'qty',
        'related_type', 'related_id', 'performed_by', 'performed_at', 'note'
    ];

    public $timestamps = false;

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function batch()
    {
        return $this->belongsTo(StockBatch::class, 'batch_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
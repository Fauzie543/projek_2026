<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = ['sku', 'name', 'category', 'unit', 'reorder_point'];

    public function batches()
    {
        return $this->hasMany(StockBatch::class);
    }

    public function transactions()
    {
        return $this->hasMany(StockTransaction::class);
    }
}
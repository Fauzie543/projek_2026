<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedBatch extends Model
{
    protected $fillable = ['recipe_id', 'produced_at', 'qty_kg', 'cost_total'];

    public $timestamps = false;

    public function recipe()
    {
        return $this->belongsTo(FeedRecipe::class, 'recipe_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedRecipe extends Model
{
    protected $fillable = ['name', 'description', 'components', 'cost_per_kg', 'created_by', 'updated_by'];

    protected $casts = [
        'components' => 'array'
    ];

    public function batches()
    {
        return $this->hasMany(FeedBatch::class, 'recipe_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
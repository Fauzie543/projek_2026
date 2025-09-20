<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receiving extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'supplier_id',
        'received_by_id',
        'received_date',
        'notes',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by_id');
    }

    public function items()
    {
        return $this->hasMany(ReceivingItem::class);
    }
}
<?php

namespace App\Models;

use App\Models\PurchaseRequestItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_date',
        'needed_by_date',
        'requester_id',
        'status',
        'notes',
    ];

    /**
     * Relasi ke user yang membuat permintaan.
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    /**
     * Relasi ke item-item yang diminta.
     */
    public function items()
    {
        return $this->hasMany(PurchaseRequestItem::class);
    }
}
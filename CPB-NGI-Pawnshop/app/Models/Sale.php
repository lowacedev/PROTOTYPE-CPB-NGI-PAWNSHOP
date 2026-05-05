<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'user_id',
        'total',
        'amount_tendered',
        'change',
        'sold_at',
        'receipt_number',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'amount_tendered' => 'decimal:2',
        'change' => 'decimal:2',
        'sold_at' => 'datetime',
    ];

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

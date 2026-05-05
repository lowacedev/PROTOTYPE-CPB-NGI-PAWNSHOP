<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'item_id',
        'appraised_value',
        'quantity',
        'notes',
    ];

    protected $casts = [
        'appraised_value' => 'decimal:2',
    ];

    /**
     * Get the transaction this item belongs to
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Get the item
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}

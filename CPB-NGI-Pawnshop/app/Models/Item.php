<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Item extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'item_code',
        'name',
        'description',
        'category_id',
        'safe_id',
        'appraised_value',
        'condition',
        'location',
        'notes',
        'is_available',
        'item_status',
        'selling_price',
    ];

    protected $casts = [
        'appraised_value' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    /**
     * Get the category this item belongs to
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the safe this item is stored in
     */
    public function safe()
    {
        return $this->belongsTo(Safe::class);
    }

    /**
     * Get all transaction items for this item
     */
    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Get all transactions this item is part of
     */
    public function transactions()
    {
        return $this->belongsToMany(Transaction::class, 'transaction_items')
                    ->withPivot('appraised_value', 'quantity', 'notes')
                    ->withTimestamps();
    }

    /**
     * Get the latest transaction this item is part of
     */
    public function getLatestTransactionAttribute()
    {
        return $this->transactions()->latest('transactions.created_at')->first();
    }

    /**
     * Get the sale record for this item if sold
     */
    public function saleItem()
    {
        return $this->hasOne(SaleItem::class);
    }
}

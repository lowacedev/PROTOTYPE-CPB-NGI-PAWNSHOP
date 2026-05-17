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

    /**
     * Get the effective status based on maturity date if stored
     */
    public function getEffectiveStatusAttribute()
    {
        if ($this->item_status === 'stored') {
            $transaction = $this->latest_transaction;
            if ($transaction && $transaction->maturity_date) {
                $maturityDate = \Carbon\Carbon::parse($transaction->maturity_date);
                $auctionDate = $maturityDate->copy()->addMonths(3);
                
                if (now()->greaterThanOrEqualTo($auctionDate)) {
                    return 'for_auction';
                } elseif (now()->greaterThan($maturityDate)) {
                    return 'past_maturity';
                }
            }
        }
        return $this->item_status;
    }

    /**
     * Get the auction date calculated from the latest transaction
     */
    public function getAuctionDateAttribute()
    {
        $transaction = $this->latest_transaction;
        if ($transaction && $transaction->maturity_date) {
            return \Carbon\Carbon::parse($transaction->maturity_date)->addMonths(3);
        }
        return null;
    }
}

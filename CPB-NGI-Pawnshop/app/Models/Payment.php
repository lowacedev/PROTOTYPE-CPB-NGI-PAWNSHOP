<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Payment extends Model
{
    use Auditable;

    protected $fillable = [
        'transaction_id',
        'amount_paid',
        'payment_type',
        'payment_method',
        'payment_date',
        'receipt_number',
        'notes',
        'principal_paid',
        'interest_paid',
        'penalty_paid',
        'service_charge',
    ];

    protected function casts(): array
    {
        return [
            'amount_paid' => 'decimal:2',
            'payment_date' => 'datetime',
        ];
    }

    /**
     * Boot method - auto-generate receipt number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->receipt_number) {
                $date = now()->format('Ymd');
                $count = static::whereDate('created_at', now())->count() + 1;
                $model->receipt_number = 'RCT-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Get the transaction this payment belongs to
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Get payment type label
     */
    public function getPaymentTypeLabelAttribute()
    {
        return match ($this->payment_type) {
            'interest' => 'Interest',
            'redemption' => 'Redemption',
            'partial' => 'Partial',
            default => $this->payment_type,
        };
    }
}

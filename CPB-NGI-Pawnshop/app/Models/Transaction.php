<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Transaction extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'pawn_ticket_number',
        'customer_id',
        'user_id',
        'transaction_type',
        'loan_amount',
        'interest_rate',
        'term_days',
        'transaction_date',
        'maturity_date',
        'redemption_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'loan_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'transaction_date' => 'datetime',
        'maturity_date' => 'datetime',
        'redemption_date' => 'datetime',
    ];

    /**
     * Boot method - auto-generate pawn ticket number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->pawn_ticket_number) {
                $date = now()->format('Y-m-d');
                $count = static::whereDate('created_at', now())->count() + 1;
                $ticket = 'PT-' . $date . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
                
                // Ensure unique ticket number even if rows were deleted
                while (static::where('pawn_ticket_number', $ticket)->exists()) {
                    $count++;
                    $ticket = 'PT-' . $date . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
                }
                
                $model->pawn_ticket_number = $ticket;
            }

            // Set maturity date if not set
            if (!$model->maturity_date && $model->transaction_date) {
                $model->maturity_date = $model->transaction_date->addDays($model->term_days ?? 30);
            }
        });
    }

    // ─── Relationships ─────────────────────────────────────────

    /**
     * Get the customer for this transaction
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the user (staff) who created this transaction
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all items in this transaction
     */
    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Get all payments for this transaction
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // ─── Computed Attributes ────────────────────────────────────

    /**
     * Calculate interest amount
     */
    public function calculateInterest()
    {
        return $this->loan_amount * ($this->interest_rate / 100);
    }

    /**
     * Calculate advance interest (interest * number of terms)
     */
    public function calculateAdvanceInterest()
    {
        $terms = max(1, (int) ceil($this->term_days / 30));
        return $this->calculateInterest() * $terms;
    }

    /**
     * Get number of overdue terms (months)
     */
    public function getOverdueTermsAttribute()
    {
        $endDate = $this->redemption_date ?? now();
        if ($this->maturity_date && $endDate->greaterThan($this->maturity_date)) {
            return (int) ceil($this->maturity_date->floatDiffInMonths($endDate, true));
        }
        return 0;
    }

    /**
     * Calculate penalty amount (2% of principal per month overdue)
     */
    public function calculatePenalty()
    {
        return $this->loan_amount * 0.02 * $this->overdue_terms;
    }

    /**
     * Get total amount due (principal + unpaid interest + penalty)
     */
    public function getTotalDueAttribute()
    {
        $unpaidInterest = $this->calculateInterest() * max(1, $this->overdue_terms);
        return $this->loan_amount + $unpaidInterest + $this->calculatePenalty();
    }

    /**
     * Check if any item in this transaction has been voided
     */
    public function hasVoidedItems()
    {
        return $this->items->contains(fn($txnItem) => $txnItem->item->item_status === 'voided');
    }

    /**
     * Get total paid
     */
    public function getTotalPaidAttribute()
    {
        return $this->payments()->sum('amount_paid');
    }

    /**
     * Get remaining balance
     */
    public function getRemainingBalanceAttribute()
    {
        return $this->total_due - $this->total_paid;
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'active' => 'Active',
            'renewed' => 'Renewed',
            'redeemed' => 'Redeemed',
            'forfeited' => 'Forfeited',
            'sold' => 'Sold',
            default => $this->status,
        };
    }

    /**
     * Get transaction type label
     */
    public function getTypeLabelAttribute()
    {
        return match ($this->transaction_type) {
            'pawn' => 'Pawn',
            'renewal' => 'Renewal',
            'redemption' => 'Redemption',
            default => $this->transaction_type,
        };
    }
}

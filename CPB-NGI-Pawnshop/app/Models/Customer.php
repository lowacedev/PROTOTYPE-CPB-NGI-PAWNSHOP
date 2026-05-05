<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Customer extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone_number',
        'region_id',
        'province_id',
        'city_id',
        'barangay_id',
        'address_line',
        'id_type',
        'id_number',
        'id_image_path',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the customer's full name
     */
    public function getFullNameAttribute()
    {
        $parts = array_filter([$this->first_name, $this->middle_name, $this->last_name]);
        return implode(' ', $parts);
    }

    /**
     * Get formatted address
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address_line,
            $this->barangay?->name,
            $this->city?->name,
            $this->province?->name,
            $this->region?->name,
        ]);
        return implode(', ', $parts);
    }

    // ─── Relationships ─────────────────────────────────────────

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    /**
     * Get all transactions for this customer
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get active transactions count
     */
    public function getActiveTransactionCountAttribute()
    {
        return $this->transactions()->where('status', 'active')->count();
    }

    /**
     * Get total active loan amount
     */
    public function getTotalLoanAmountAttribute()
    {
        return $this->transactions()->where('status', 'active')->sum('loan_amount');
    }
}

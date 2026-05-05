<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Safe extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'safe_code',
        'customer_id',
        'is_personal',
        'name',
        'description',
        'location',
        'items_capacity',
        'capacity',
        'current_value',
        'notes',
    ];

    protected $casts = [
        'capacity' => 'decimal:2',
        'current_value' => 'decimal:2',
        'is_personal' => 'boolean',
        'items_capacity' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($safe) {
            if (!$safe->safe_code) {
                // Generate safe code in format: SAFE-YYYYMMDD-###
                $count = static::whereDate('created_at', today())->count() + 1;
                $code = 'SAFE-' . now()->format('Ymd') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
                
                while (static::where('safe_code', $code)->exists()) {
                    $count++;
                    $code = 'SAFE-' . now()->format('Ymd') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
                }
                
                $safe->safe_code = $code;
            }

            // Set default capacity if not provided
            if (!$safe->capacity) {
                $safe->capacity = config('safes.default_capacity', 100000);
            }

            // Auto-generate name based on customer or location
            if (!$safe->name) {
                if ($safe->is_personal && $safe->customer_id) {
                    $customer = Customer::find($safe->customer_id);
                    $safe->name = 'Safe - ' . ($customer ? $customer->full_name : 'Customer');
                } else {
                    $safe->name = 'Safe - ' . $safe->location;
                }
            }
        });
    }

    /**
     * Get the customer this safe belongs to
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get all items stored in this safe
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }
}

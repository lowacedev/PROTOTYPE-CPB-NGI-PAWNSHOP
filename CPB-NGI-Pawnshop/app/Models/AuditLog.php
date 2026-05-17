<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'changes',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'description',
    ];

    protected $casts = [
        'changes' => 'array',
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get action with formatted text
     */
    public function getActionLabelAttribute()
    {
        return match($this->action) {
            'create' => 'Created',
            'update' => 'Updated',
            'delete' => 'Deleted',
            'pawn' => 'Pawned',
            'renew' => 'Renewed',
            'redeem' => 'Redeemed',
            'payment' => 'Payment Received',
            'void_request' => 'Void Requested',
            'void_approved' => 'Void Approved',
            'void_rejected' => 'Void Rejected',
            'remove_item' => 'Item Removed',
            'remove_request' => 'Removal Requested',
            'remove_approved' => 'Removal Approved',
            'remove_rejected' => 'Removal Rejected',
            default => ucfirst($this->action)
        };
    }

    /**
     * Get action color for display
     */
    public function getActionColorAttribute()
    {
        return match($this->action) {
            'create' => 'green',
            'update' => 'blue',
            'delete' => 'red',
            'pawn' => 'indigo',
            'renew' => 'yellow',
            'redeem' => 'blue',
            'payment' => 'emerald',
            'void_request' => 'orange',
            'void_approved' => 'green',
            'void_rejected' => 'red',
            'remove_item' => 'red',
            'remove_request' => 'orange',
            'remove_approved' => 'green',
            'remove_rejected' => 'red',
            default => 'gray'
        };
    }
}

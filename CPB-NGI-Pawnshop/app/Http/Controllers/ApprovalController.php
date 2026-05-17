<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    /**
     * Display a listing of pending approvals.
     */
    public function index()
    {
        $approvals = Approval::with(['user', 'model'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(15);
            
        return view('approvals.index', compact('approvals'));
    }

    /**
     * Approve the requested action.
     */
    public function approve(Approval $approval)
    {
        if ($approval->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        if ($approval->action === 'edit_transaction') {
            $transaction = $approval->model;
            if ($transaction) {
                $transaction->update($approval->payload);
            }
        } elseif ($approval->action === 'remove_item' || $approval->action === 'void_item') {
            $item = $approval->model;
            if ($item) {
                $status = $approval->action === 'remove_item' ? 'removed' : 'voided';
                $item->update(['item_status' => $status, 'is_available' => false]);
            }
        } elseif ($approval->action === 'void_transaction') {
            $transaction = $approval->model;
            if ($transaction) {
                $transaction->update(['status' => 'voided']);
                foreach ($transaction->items as $txnItem) {
                    $txnItem->item->update(['item_status' => 'voided', 'is_available' => false]);
                }
            }
        }

        $approval->update([
            'status' => 'approved',
            'manager_id' => auth()->id()
        ]);

        if (str_starts_with($approval->action, 'void_') || $approval->action === 'remove_item') {
            $identifier = $approval->model instanceof \App\Models\Transaction 
                ? "#{$approval->model->pawn_ticket_number}" 
                : ($approval->model instanceof \App\Models\Item ? $approval->model->name : "ID {$approval->model_id}");

            $actionType = $approval->action === 'remove_item' ? 'remove_approved' : 'void_approved';
            $verb = $approval->action === 'remove_item' ? 'removal' : 'void';

            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'action' => $actionType,
                'model_type' => class_basename($approval->model_type),
                'model_id' => $approval->model_id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'description' => "Approved {$verb} request for {$identifier}.",
            ]);
        }

        return back()->with('success', 'Request approved successfully.');
    }

    /**
     * Reject the requested action.
     */
    public function reject(Approval $approval)
    {
        if ($approval->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        $approval->update([
            'status' => 'rejected',
            'manager_id' => auth()->id()
        ]);

        if (str_starts_with($approval->action, 'void_') || $approval->action === 'remove_item') {
            $identifier = $approval->model instanceof \App\Models\Transaction 
                ? "#{$approval->model->pawn_ticket_number}" 
                : ($approval->model instanceof \App\Models\Item ? $approval->model->name : "ID {$approval->model_id}");

            $actionType = $approval->action === 'remove_item' ? 'remove_rejected' : 'void_rejected';
            $verb = $approval->action === 'remove_item' ? 'removal' : 'void';

            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'action' => $actionType,
                'model_type' => class_basename($approval->model_type),
                'model_id' => $approval->model_id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'description' => "Rejected {$verb} request for {$identifier}.",
            ]);
        }

        return back()->with('success', 'Request rejected.');
    }
}

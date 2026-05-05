<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Payment;
use App\Http\Requests\StoreTransactionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of transactions.
     */
    public function index(Request $request)
    {
        $query = Transaction::with('customer', 'items.item');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('pawn_ticket_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q2) use ($search) {
                      $q2->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('items.item', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $transactions = $query->latest()->paginate(15)->withQueryString();
        return view('transactions.index', compact('transactions'));
    }

    /**
     * Show the form for creating a new transaction (pawn).
     */
    public function create()
    {
        return redirect()->route('pawn.wizard');
    }

    /**
     * Store a newly created transaction in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        $validated = $request->validated();
        $user = auth()->user();

        // Create the transaction
        $transaction = Transaction::create([
            'customer_id'      => $validated['customer_id'],
            'user_id'          => $user->id,
            'transaction_type' => 'pawn',
            'loan_amount'      => $validated['loan_amount'],
            'interest_rate'    => $validated['interest_rate'],
            'term_days'        => $validated['term_days'],
            'transaction_date' => now(),
            'maturity_date'    => now()->addDays($validated['term_days']),
            'status'           => 'active',
            'notes'            => $validated['notes'] ?? null,
        ]);

        // Add items to the transaction
        foreach ($validated['items'] as $itemData) {
            $item = Item::find($itemData['item_id']);
            $transaction->items()->create([
                'item_id'         => $itemData['item_id'],
                'appraised_value' => $item->appraised_value,
                'quantity'        => $itemData['quantity'],
            ]);
            // Mark item as unavailable (pawned)
            $item->update(['is_available' => false, 'item_status' => 'stored']);
        }

        return redirect()->route('transactions.show', $transaction)->with('success', 'Pawn transaction created successfully!');
    }

    /**
     * Display the specified transaction.
     */
    public function show(Transaction $transaction)
    {
        $transaction->load('customer', 'items.item', 'payments', 'user');
        return view('transactions.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified transaction.
     */
    public function edit(Transaction $transaction)
    {
        if ($transaction->status !== 'active') {
            return redirect()->route('transactions.show', $transaction)->with('error', 'Only active transactions can be edited!');
        }
        $customers = Customer::where('is_active', true)->get();
        $items = Item::where('is_available', true)->orWhereHas('transactions', function ($q) use ($transaction) {
            $q->where('transactions.id', $transaction->id);
        })->get();
        return view('transactions.edit', compact('transaction', 'customers', 'items'));
    }

    /**
     * Update the specified transaction in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->status !== 'active') {
            return redirect()->route('transactions.show', $transaction)->with('error', 'Only active transactions can be updated!');
        }

        $validated = $request->validate([
            'loan_amount'   => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'term_days'     => 'required|integer|min:1',
            'notes'         => 'nullable|string',
        ]);

        $transaction->update($validated);

        return redirect()->route('transactions.show', $transaction)->with('success', 'Transaction updated successfully!');
    }

    // ─── Renewal & Redemption Workflow ─────────────────────────────────

    /**
     * Show search interface for actions
     */
    public function actionSearch()
    {
        return view('transactions.action-search');
    }

    /**
     * API: Search transactions for renewal/redemption
     */
    public function searchTransactionApi(Request $request)
    {
        $query = $request->query('q');
        if (!$query) return response()->json([]);

        $transactions = Transaction::with('customer')
            ->where('pawn_ticket_number', 'like', "%{$query}%")
            ->orWhereHas('customer', function($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%");
            })
            ->whereIn('status', ['active', 'renewed'])
            ->limit(10)
            ->get();

        return response()->json($transactions);
    }

    /**
     * Show renewal form
     */
    public function showRenewalForm(Transaction $transaction)
    {
        if ($transaction->status !== 'active') {
            return redirect()->route('transactions.actions.search')->with('error', 'Only active transactions can be renewed.');
        }
        
        $interestDue = $transaction->calculateInterest();
        return view('transactions.renew', compact('transaction', 'interestDue'));
    }

    /**
     * Process renewal payment and extend terms
     */
    public function processRenewal(Request $request, Transaction $transaction)
    {
        if ($transaction->status !== 'active') {
            return redirect()->route('transactions.actions.search')->with('error', 'Only active transactions can be renewed.');
        }

        $interestDue = $transaction->calculateInterest();
        
        $request->validate([
            'amount_paid' => 'required|numeric|min:' . $interestDue
        ]);

        DB::beginTransaction();
        try {
            $payment = Payment::create([
                'transaction_id' => $transaction->id,
                'amount_paid'    => $request->amount_paid,
                'payment_type'   => 'interest',
                'payment_method' => 'cash',
                'payment_date'   => now(),
            ]);

            $transaction->update([
                'maturity_date' => $transaction->maturity_date->addDays((int) $transaction->term_days),
                // Status remains active for the extended term
            ]);

            DB::commit();

            return redirect()->route('transactions.action-receipt', [
                'transaction' => $transaction->id, 
                'payment' => $payment->id
            ])->with('success', 'Transaction renewed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Renewal failed: ' . $e->getMessage());
        }
    }

    /**
     * Show redemption form
     */
    public function showRedemptionForm(Transaction $transaction)
    {
        if (!in_array($transaction->status, ['active', 'renewed'])) {
            return redirect()->route('transactions.actions.search')->with('error', 'Only active transactions can be redeemed.');
        }

        $interestDue = $transaction->calculateInterest();
        $totalDue = $transaction->loan_amount + $interestDue;
        
        return view('transactions.redeem', compact('transaction', 'interestDue', 'totalDue'));
    }

    /**
     * Process full redemption payment and free item
     */
    public function processRedemption(Request $request, Transaction $transaction)
    {
        if (!in_array($transaction->status, ['active', 'renewed'])) {
            return redirect()->route('transactions.actions.search')->with('error', 'Only active transactions can be redeemed.');
        }

        $interestDue = $transaction->calculateInterest();
        $totalDue = $transaction->loan_amount + $interestDue;

        $request->validate([
            'amount_paid' => 'required|numeric|min:' . $totalDue
        ]);

        DB::beginTransaction();
        try {
            $payment = Payment::create([
                'transaction_id' => $transaction->id,
                'amount_paid'    => $request->amount_paid,
                'payment_type'   => 'redemption',
                'payment_method' => 'cash',
                'payment_date'   => now(),
            ]);

            $transaction->update([
                'status' => 'redeemed',
                'redemption_date' => now(),
            ]);

            foreach ($transaction->items as $txnItem) {
                $txnItem->item->update(['is_available' => true, 'item_status' => 'redeemed']);
            }

            DB::commit();

            return redirect()->route('transactions.action-receipt', [
                'transaction' => $transaction->id, 
                'payment' => $payment->id
            ])->with('success', 'Transaction redeemed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Redemption failed: ' . $e->getMessage());
        }
    }

    /**
     * Show thermal receipt for the payment action
     */
    public function actionReceipt(Transaction $transaction, Payment $payment)
    {
        $payment->load('transaction.customer', 'transaction.items.item', 'transaction.user');
        return view('transactions.action-receipt', compact('payment'));
    }

    /**
     * Forfeit a transaction (items forfeited to pawnshop).
     */
    public function forfeit(Transaction $transaction)
    {
        if (!in_array($transaction->status, ['active', 'renewed'])) {
            return redirect()->route('transactions.show', $transaction)->with('error', 'Only active or renewed transactions can be forfeited!');
        }

        DB::transaction(function() use ($transaction) {
            $transaction->update(['status' => 'forfeited']);

            foreach ($transaction->items as $transactionItem) {
                if ($transactionItem->item) {
                    $transactionItem->item->update(['item_status' => 'for_sale']);
                }
            }
        });

        return redirect()->route('transactions.show', $transaction)->with('success', 'Transaction marked as forfeited and items moved to POS for sale!');
    }

    /**
     * Remove the specified transaction from storage.
     */
    public function destroy(Transaction $transaction)
    {
        if ($transaction->status === 'active') {
            return redirect()->route('transactions.show', $transaction)->with('error', 'Cannot delete active transactions!');
        }

        $transaction->delete();
        return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Transaction;
use App\Http\Requests\StorePaymentRequest;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments.
     */
    public function index()
    {
        $payments = Payment::with('transaction.customer')->latest()->paginate(15);
        return view('payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create($transactionId = null)
    {
        $transaction = null;
        if ($transactionId) {
            $transaction = Transaction::findOrFail($transactionId);
        }
        $transactions = Transaction::where('status', 'active')->with('customer')->get();
        return view('payments.create', compact('transaction', 'transactions'));
    }

    /**
     * Store a newly created payment in storage.
     */
    public function store(StorePaymentRequest $request)
    {
        $validated = $request->validated();

        $transaction = Transaction::findOrFail($validated['transaction_id']);

        if ($transaction->status !== 'active') {
            return redirect()->back()->with('error', 'Payment can only be made for active transactions!');
        }

        $validated['payment_date'] = now();

        Payment::create($validated);

        return redirect()->route('transactions.show', $transaction)->with('success', 'Payment recorded successfully!');
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment)
    {
        $payment->load('transaction.customer');
        return view('payments.show', compact('payment'));
    }

    /**
     * Remove the specified payment from storage.
     */
    public function destroy(Payment $payment)
    {
        $transaction = $payment->transaction;
        $payment->delete();
        return redirect()->route('transactions.show', $transaction)->with('success', 'Payment deleted successfully!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Transaction;
use App\Http\Requests\StorePaymentRequest;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments with search & filter.
     */
    public function index(Request $request)
    {
        $query = Payment::with('transaction.customer');

        // Search by customer name or pawn ticket number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('transaction', function ($q2) use ($search) {
                    $q2->where('pawn_ticket_number', 'like', "%{$search}%")
                       ->orWhereHas('customer', function ($q3) use ($search) {
                           $q3->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%");
                       });
                });
            });
        }

        // Filter by payment type
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('payment_type', $request->type);
        }

        $payments = $query->latest()->paginate(15)->withQueryString();
        return view('payments.index', compact('payments'));
    }

    /**
     * API: Search payments by customer or ticket (AJAX)
     */
    public function searchApi(Request $request)
    {
        $search = $request->query('q', '');
        $type = $request->query('type');

        // Need at least a search query or a type filter
        if (strlen($search) < 2 && empty($type)) {
            return response()->json([]);
        }

        $query = Payment::with('transaction.customer');

        if (strlen($search) >= 2) {
            $query->whereHas('transaction', function ($q) use ($search) {
                $q->where('pawn_ticket_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q2) use ($search) {
                      $q2->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($type) && $type !== 'all') {
            $query->where('payment_type', $type);
        }

        $payments = $query->latest()
            ->limit(20)
            ->get()
            ->map(function ($pmt) {
                return [
                    'id'                 => $pmt->id,
                    'receipt_number'     => $pmt->receipt_number,
                    'pawn_ticket_number' => $pmt->transaction->pawn_ticket_number,
                    'customer_name'      => $pmt->transaction->customer->full_name,
                    'amount_paid'        => number_format($pmt->amount_paid, 2),
                    'payment_type'       => $pmt->payment_type_label,
                    'payment_method'     => ucfirst(str_replace('_', ' ', $pmt->payment_method)),
                    'payment_date'       => $pmt->payment_date->format('M d, Y'),
                    'transaction_id'     => $pmt->transaction_id,
                ];
            });

        return response()->json($payments);
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

        $payment = Payment::create($validated);

        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'payment',
            'model_type' => 'Payment',
            'model_id' => $payment->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => "Recorded payment of ₱" . number_format($payment->amount_paid, 2) . " for transaction #{$transaction->pawn_ticket_number}.",
        ]);

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

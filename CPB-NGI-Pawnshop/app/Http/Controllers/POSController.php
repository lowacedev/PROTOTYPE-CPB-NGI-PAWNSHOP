<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class POSController extends Controller
{
    public function index()
    {
        // Auto-forfeit expired transactions so their items appear for sale automatically
        $expiredTransactions = \App\Models\Transaction::with('items.item')
            ->whereIn('status', ['active', 'renewed'])
            ->whereDate('maturity_date', '<', today())
            ->get();

        if ($expiredTransactions->count() > 0) {
            \Illuminate\Support\Facades\DB::transaction(function () use ($expiredTransactions) {
                foreach ($expiredTransactions as $transaction) {
                    $transaction->update(['status' => 'forfeited']);
                    foreach ($transaction->items as $transactionItem) {
                        if ($transactionItem->item) {
                            $transactionItem->item->update(['item_status' => 'for_sale']);
                        }
                    }
                }
            });
        }

        $items = \App\Models\Item::where('item_status', 'for_sale')->get();
        return view('pos.index', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:items,id',
            'amount_tendered' => 'required|numeric|min:0'
        ]);

        $sale = null;

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, &$sale) {
            $items = \App\Models\Item::whereIn('id', $request->item_ids)
                ->where('item_status', 'for_sale')
                ->lockForUpdate()
                ->get();
            
            $total = $items->sum('appraised_value');

            if ($request->amount_tendered < $total) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount_tendered' => 'Amount tendered must be greater than or equal to the total.'
                ]);
            }

            $change = $request->amount_tendered - $total;

            $countToday = \App\Models\Sale::whereDate('created_at', today())->count() + 1;
            $receiptNumber = 'POS-' . date('Ymd') . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            $sale = \App\Models\Sale::create([
                'user_id' => auth()->id(),
                'total' => $total,
                'amount_tendered' => $request->amount_tendered,
                'change' => $change,
                'sold_at' => now(),
                'receipt_number' => $receiptNumber
            ]);

            foreach ($items as $item) {
                \App\Models\SaleItem::create([
                    'sale_id' => $sale->id,
                    'item_id' => $item->id,
                    'price' => $item->appraised_value
                ]);

                $item->update([
                    'item_status' => 'sold',
                    'selling_price' => $item->appraised_value
                ]);

                // Update the associated transaction's status to 'sold'
                $latestTxn = $item->latest_transaction;
                if ($latestTxn && $latestTxn->status === 'forfeited') {
                    $latestTxn->update(['status' => 'sold']);
                }
            }
        });

        return redirect()->route('pos.receipt', $sale->id)->with('success', 'Sale completed successfully!');
    }

    public function receipt(\App\Models\Sale $sale)
    {
        $sale->load('saleItems.item', 'user');
        return view('pos.receipt', compact('sale'));
    }

    public function autoForfeit()
    {
        $expiredTransactions = \App\Models\Transaction::with('items.item')
            ->whereIn('status', ['active', 'renewed'])
            ->whereDate('maturity_date', '<', today())
            ->get();

        $count = 0;
        \Illuminate\Support\Facades\DB::transaction(function () use ($expiredTransactions, &$count) {
            foreach ($expiredTransactions as $transaction) {
                $transaction->update(['status' => 'forfeited']);
                foreach ($transaction->items as $transactionItem) {
                    if ($transactionItem->item) {
                        $transactionItem->item->update(['item_status' => 'for_sale']);
                    }
                }
                $count++;
            }
        });

        return redirect()->back()->with('success', "{$count} expired transaction(s) forfeited. Items are now for sale.");
    }
}

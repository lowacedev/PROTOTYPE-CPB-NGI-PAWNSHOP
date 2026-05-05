<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Payment;
use App\Models\Item;
use App\Models\Sale;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Dashboard Summary
     */
    public function index()
    {
        $totalActiveLoans = Transaction::where('status', 'active')->sum('loan_amount');
        $totalRedeemed = Transaction::where('status', 'redeemed')->count();
        $totalForfeited = Transaction::where('status', 'forfeited')->count();
        $totalSalesRevenue = Sale::sum('total');

        return view('reports.index', compact(
            'totalActiveLoans',
            'totalRedeemed',
            'totalForfeited',
            'totalSalesRevenue'
        ));
    }

    /**
     * Daily Transaction Report
     */
    public function transactionsReport(Request $request)
    {
        $query = Transaction::with('customer', 'items.item');

        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->latest('transaction_date')->get();
        $totalLoanReleased = $transactions->sum('loan_amount');
        
        return view('reports.transactions', compact('transactions', 'totalLoanReleased'));
    }

    /**
     * Payment Report
     */
    public function paymentsReport(Request $request)
    {
        $query = Payment::with('transaction.customer');

        if ($request->filled('start_date')) {
            $query->whereDate('payment_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('payment_date', '<=', $request->end_date);
        }
        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        $payments = $query->latest('payment_date')->get();
        $totalCollected = $payments->sum('amount_paid');

        return view('reports.payments', compact('payments', 'totalCollected'));
    }

    /**
     * Sales Report (POS)
     */
    public function salesReport(Request $request)
    {
        $query = Sale::with('saleItems.item', 'user');

        if ($request->filled('start_date')) {
            $query->whereDate('sold_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('sold_at', '<=', $request->end_date);
        }

        $sales = $query->latest('sold_at')->get();
        $totalSales = $sales->sum('total');

        return view('reports.sales', compact('sales', 'totalSales'));
    }

    /**
     * Forfeited Items Report
     */
    public function forfeitedReport(Request $request)
    {
        // Items where transaction status is forfeited
        $query = Item::with('transactions')->whereIn('item_status', ['for_sale', 'stored', 'sold'])
            ->whereHas('transactions', function($q) {
                $q->where('status', 'forfeited');
            });

        if ($request->filled('start_date') || $request->filled('end_date')) {
            $query->whereHas('transactions', function($q) use ($request) {
                if ($request->filled('start_date')) {
                    $q->whereDate('maturity_date', '>=', $request->start_date);
                }
                if ($request->filled('end_date')) {
                    $q->whereDate('maturity_date', '<=', $request->end_date);
                }
            });
        }
        
        if ($request->filled('status')) {
            $query->where('item_status', $request->status);
        } else {
            $query->where('item_status', 'for_sale'); // default to not yet sold
        }

        $items = $query->latest()->get();

        return view('reports.forfeited', compact('items'));
    }

    /**
     * Export to PDF
     */
    public function exportPdf($type, Request $request)
    {
        $data = [];
        $viewName = '';
        $fileName = '';

        if ($type === 'transactions') {
            $query = Transaction::with('customer', 'items.item');
            if ($request->filled('start_date')) $query->whereDate('transaction_date', '>=', $request->start_date);
            if ($request->filled('end_date')) $query->whereDate('transaction_date', '<=', $request->end_date);
            if ($request->filled('status')) $query->where('status', $request->status);
            
            $transactions = $query->latest('transaction_date')->get();
            $data = [
                'title' => 'Pawn Transactions Report',
                'transactions' => $transactions,
                'totalLoanReleased' => $transactions->sum('loan_amount')
            ];
            $viewName = 'reports.pdf.transactions';
            $fileName = 'transactions_report_' . now()->format('YmdHis') . '.pdf';
        }
        elseif ($type === 'payments') {
            $query = Payment::with('transaction.customer');
            if ($request->filled('start_date')) $query->whereDate('payment_date', '>=', $request->start_date);
            if ($request->filled('end_date')) $query->whereDate('payment_date', '<=', $request->end_date);
            if ($request->filled('payment_type')) $query->where('payment_type', $request->payment_type);
            
            $payments = $query->latest('payment_date')->get();
            $data = [
                'title' => 'Payments Report',
                'payments' => $payments,
                'totalCollected' => $payments->sum('amount_paid')
            ];
            $viewName = 'reports.pdf.payments';
            $fileName = 'payments_report_' . now()->format('YmdHis') . '.pdf';
        }
        elseif ($type === 'sales') {
            $query = Sale::with('saleItems.item', 'user');
            if ($request->filled('start_date')) $query->whereDate('sold_at', '>=', $request->start_date);
            if ($request->filled('end_date')) $query->whereDate('sold_at', '<=', $request->end_date);
            
            $sales = $query->latest('sold_at')->get();
            $data = [
                'title' => 'POS Sales Report',
                'sales' => $sales,
                'totalSales' => $sales->sum('total')
            ];
            $viewName = 'reports.pdf.sales';
            $fileName = 'sales_report_' . now()->format('YmdHis') . '.pdf';
        }
        elseif ($type === 'forfeited') {
            $query = Item::with('transactions')->whereIn('item_status', ['for_sale', 'stored', 'sold'])
                ->whereHas('transactions', function($q) {
                    $q->where('status', 'forfeited');
                });
                
            if ($request->filled('start_date') || $request->filled('end_date')) {
                $query->whereHas('transactions', function($q) use ($request) {
                    if ($request->filled('start_date')) $q->whereDate('maturity_date', '>=', $request->start_date);
                    if ($request->filled('end_date')) $q->whereDate('maturity_date', '<=', $request->end_date);
                });
            }
            if ($request->filled('status')) {
                $query->where('item_status', $request->status);
            } else {
                $query->where('item_status', 'for_sale'); // default to not yet sold
            }
            
            $data = [
                'title' => 'Forfeited Items Report',
                'items' => $query->latest()->get()
            ];
            $viewName = 'reports.pdf.forfeited';
            $fileName = 'forfeited_items_report_' . now()->format('YmdHis') . '.pdf';
        } else {
            abort(404);
        }

        $pdf = Pdf::loadView($viewName, $data)->setPaper('a4', 'landscape');
        return $pdf->stream($fileName);
    }
}

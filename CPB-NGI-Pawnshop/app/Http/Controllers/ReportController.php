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
        // KPIs
        $totalActiveLoansCount = Transaction::whereIn('status', ['active', 'renewed'])->count();
        $totalActiveLoansAmount = Transaction::whereIn('status', ['active', 'renewed'])->sum('loan_amount');

        $loansReleasedToday = Transaction::whereDate('transaction_date', Carbon::today())->sum('loan_amount');

        $totalCollectionsToday = Payment::whereDate('payment_date', Carbon::today())->sum('amount_paid');

        $interestIncome = Payment::sum('interest_paid'); // Total overall interest income. Or maybe just today? The user said "Interest Income" which usually means overall or MTd. I will provide overall.

        return view('reports.index', compact(
            'totalActiveLoansCount',
            'totalActiveLoansAmount',
            'loansReleasedToday',
            'totalCollectionsToday',
            'interestIncome'
        ));
    }
    /**
     * Collection Summary Report
     */
    public function summaryReport(Request $request)
    {
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date)->startOfDay() 
            : Carbon::now()->startOfMonth();
            
        $endDate = $request->filled('end_date') 
            ? Carbon::parse($request->end_date)->endOfDay() 
            : Carbon::now()->endOfDay();

        $payments = Payment::whereBetween('payment_date', [$startDate, $endDate])->get();

        $totalPrincipal = $payments->sum('principal_paid');
        $totalInterest = $payments->sum('interest_paid');
        $totalPenalty = $payments->sum('penalty_paid');
        $totalServiceCharge = $payments->sum('service_charge');
        $netCollection = $payments->sum('amount_paid');

        $summaryData = [
            'total_principal' => $totalPrincipal,
            'total_interest' => $totalInterest,
            'total_penalty' => $totalPenalty,
            'total_service_charge' => $totalServiceCharge,
            'net_collection' => $netCollection,
        ];

        $transactionTypes = [
            'Redemption Payments' => [
                'count' => $payments->where('payment_type', 'redemption')->count(),
                'amount' => $totalPrincipal,
            ],
            'Interest Payments' => [
                'count' => $payments->where('payment_type', 'interest')->count(),
                'amount' => $totalInterest,
            ],
            'Penalties Collected' => [
                'count' => $payments->where('penalty_paid', '>', 0)->count(),
                'amount' => $totalPenalty,
            ],
            'Service Charges' => [
                'count' => $payments->where('service_charge', '>', 0)->count(),
                'amount' => $totalServiceCharge,
            ]
        ];

        $dateBreakdown = $payments->groupBy(function($item) {
            return $item->payment_date->format('M d');
        })->map(function($dayPayments) {
            return [
                'principal' => $dayPayments->sum('principal_paid'),
                'interest' => $dayPayments->sum('interest_paid'),
                'deductions' => $dayPayments->sum('service_charge'),
                'total' => $dayPayments->sum('amount_paid')
            ];
        });

        return view('reports.summary', compact('startDate', 'endDate', 'summaryData', 'transactionTypes', 'dateBreakdown'));
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
     * Inventory of Pawned Items Report
     */
    public function inventoryReport(Request $request)
    {
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date)->startOfDay() 
            : Carbon::now()->startOfMonth();
            
        $endDate = $request->filled('end_date') 
            ? Carbon::parse($request->end_date)->endOfDay() 
            : Carbon::now()->endOfDay();

        $items = Item::with(['category', 'transactions', 'saleItem.sale'])->get();
        $inventory = [];

        foreach ($items as $item) {
            $catName = $item->category->name ?? 'Uncategorized';
            if (!isset($inventory[$catName])) {
                $inventory[$catName] = ['category' => $catName, 'beg' => 0, 'add' => 0, 'minus' => 0, 'end' => 0];
            }
            
            $pawnedDate = $item->created_at;
            $removedDate = null;
            
            if ($item->item_status === 'voided') {
                $removedDate = $item->updated_at;
            } elseif ($item->item_status === 'redeemed') {
                $redeemTxn = $item->transactions->where('status', 'redeemed')->first();
                $removedDate = $redeemTxn && $redeemTxn->redemption_date ? $redeemTxn->redemption_date : $item->updated_at;
            } elseif ($item->item_status === 'sold') {
                $sale = $item->saleItem?->sale;
                $removedDate = $sale ? $sale->sold_at : $item->updated_at;
            }
            
            $pawnedAt = Carbon::parse($pawnedDate);
            $removedAt = $removedDate ? Carbon::parse($removedDate) : null;
            
            $wasInBegBalance = $pawnedAt->lt($startDate) && ($removedAt === null || $removedAt->gte($startDate));
            $wasAdded = $pawnedAt->between($startDate, $endDate);
            $wasRemoved = $removedAt !== null && $removedAt->between($startDate, $endDate);
            
            if ($wasInBegBalance) $inventory[$catName]['beg']++;
            if ($wasAdded) $inventory[$catName]['add']++;
            if ($wasRemoved) $inventory[$catName]['minus']++;
            
            $inventory[$catName]['end'] = $inventory[$catName]['beg'] + $inventory[$catName]['add'] - $inventory[$catName]['minus'];
        }

        // Sort by category name
        ksort($inventory);

        return view('reports.inventory', compact('inventory', 'startDate', 'endDate'));
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
        elseif ($type === 'inventory') {
            $startDate = $request->filled('start_date') 
                ? Carbon::parse($request->start_date)->startOfDay() 
                : Carbon::now()->startOfMonth();
                
            $endDate = $request->filled('end_date') 
                ? Carbon::parse($request->end_date)->endOfDay() 
                : Carbon::now()->endOfDay();

            $items = Item::with(['category', 'transactions', 'saleItem.sale'])->get();
            $inventory = [];

            foreach ($items as $item) {
                $catName = $item->category->name ?? 'Uncategorized';
                if (!isset($inventory[$catName])) {
                    $inventory[$catName] = ['category' => $catName, 'beg' => 0, 'add' => 0, 'minus' => 0, 'end' => 0];
                }
                
                $pawnedDate = $item->created_at;
                $removedDate = null;
                
                if ($item->item_status === 'voided') {
                    $removedDate = $item->updated_at;
                } elseif ($item->item_status === 'redeemed') {
                    $redeemTxn = $item->transactions->where('status', 'redeemed')->first();
                    $removedDate = $redeemTxn && $redeemTxn->redemption_date ? $redeemTxn->redemption_date : $item->updated_at;
                } elseif ($item->item_status === 'sold') {
                    $sale = $item->saleItem?->sale;
                    $removedDate = $sale ? $sale->sold_at : $item->updated_at;
                }
                
                $pawnedAt = Carbon::parse($pawnedDate);
                $removedAt = $removedDate ? Carbon::parse($removedDate) : null;
                
                $wasInBegBalance = $pawnedAt->lt($startDate) && ($removedAt === null || $removedAt->gte($startDate));
                $wasAdded = $pawnedAt->between($startDate, $endDate);
                $wasRemoved = $removedAt !== null && $removedAt->between($startDate, $endDate);
                
                if ($wasInBegBalance) $inventory[$catName]['beg']++;
                if ($wasAdded) $inventory[$catName]['add']++;
                if ($wasRemoved) $inventory[$catName]['minus']++;
                
                $inventory[$catName]['end'] = $inventory[$catName]['beg'] + $inventory[$catName]['add'] - $inventory[$catName]['minus'];
            }
            ksort($inventory);
            
            $data = [
                'title' => 'Inventory of Pawned Items Report',
                'inventory' => $inventory,
                'startDate' => $startDate,
                'endDate' => $endDate
            ];
            $viewName = 'reports.pdf.inventory';
            $fileName = 'inventory_report_' . now()->format('YmdHis') . '.pdf';
        } else {
            abort(404);
        }

        $pdf = Pdf::loadView($viewName, $data)->setPaper('a4', 'landscape');
        return $pdf->stream($fileName);
    }
}

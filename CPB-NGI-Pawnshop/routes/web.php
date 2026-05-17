<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SafeController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\LocationController;
use App\Models\Transaction;
use App\Models\Customer;
use App\Models\Item;
use App\Http\Controllers\PawnWizardController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->isTeller()) {
        return redirect()->route('transactions.index');
    }
    if ($user->isCashier()) {
        return redirect()->route('transactions.actions.search');
    }

    return view('dashboard', [
        'customerCount'    => Customer::count(),
        'activeCount'      => Transaction::where('status', 'active')->count(),
        'itemsCount'       => Item::count(),
        'totalLoanAmount'  => Transaction::where('status', 'active')->sum('loan_amount'),
        'recentTransactions' => Transaction::with('customer')->latest()->limit(5)->get(),
    ]);
})->middleware(['auth', 'role:admin,manager'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ─── Teller Module: Customers, Pawn Wizard, Items, View Transactions ───
    // Teller can create pawn tickets and manage customers but CANNOT redeem/renew/forfeit
    Route::middleware('role:teller')->group(function () {
        Route::resource('customers', CustomerController::class);
        Route::post('/items/{item}/request-void', [ItemController::class, 'requestVoid'])->name('items.request-void');
        Route::resource('items', ItemController::class);

        Route::post('/transactions/{transaction}/request-void', [TransactionController::class, 'requestVoid'])->name('transactions.request-void');
        Route::resource('transactions', TransactionController::class)->except(['index', 'show']);

        // Pawn Wizard (Multi-step) - Teller creates new pawn transactions
        Route::get('/pawn/wizard', [PawnWizardController::class, 'create'])->name('pawn.wizard');
        Route::post('/pawn/wizard', [PawnWizardController::class, 'store'])->name('pawn.wizard.store');
        Route::get('/pawn/receipt/{transaction}', [PawnWizardController::class, 'receipt'])->name('pawn.receipt');
        Route::get('/api/customers/search', [PawnWizardController::class, 'searchCustomers'])->name('api.customers.search');
        Route::get('/api/items/names/{category}', [ItemController::class, 'getNamesByCategory'])->name('api.items.names');
    });

    // ─── Transactions: View-only for Teller ─────────
    // Teller can view transactions
    Route::middleware('role:teller')->group(function () {
        Route::resource('transactions', TransactionController::class)->only(['index', 'show']);
    });

    // ─── Cashier Module: Payments, POS, Redeem/Renew/Forfeit ──────────
    // Cashier handles money: payments, redemptions, renewals, POS
    Route::middleware('role:cashier')->group(function () {
        // Payments
        Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store', 'destroy']);
        Route::get('/payments/transaction/{transactionId}', [PaymentController::class, 'create'])->name('payments.create.for-transaction');
        Route::get('/api/payments/search', [PaymentController::class, 'searchApi'])->name('api.payments.search');

        // Transaction Actions (Renewal & Redemption) - Cashier only
        Route::get('/transactions-actions/search', [TransactionController::class, 'actionSearch'])->name('transactions.actions.search');
        Route::get('/api/transactions-actions/search', [TransactionController::class, 'searchTransactionApi'])->name('api.transactions.actions.search');
        
        Route::get('/transactions/{transaction}/renew', [TransactionController::class, 'showRenewalForm'])->name('transactions.renew.form');
        Route::post('/transactions/{transaction}/renew', [TransactionController::class, 'processRenewal'])->name('transactions.renew.process');
        
        Route::get('/transactions/{transaction}/redeem', [TransactionController::class, 'showRedemptionForm'])->name('transactions.redeem.form');
        Route::post('/transactions/{transaction}/redeem', [TransactionController::class, 'processRedemption'])->name('transactions.redeem.process');
        
        Route::get('/transactions/{transaction}/action-receipt/{payment}', [TransactionController::class, 'actionReceipt'])->name('transactions.action-receipt');

        Route::post('/transactions/{transaction}/forfeit', [TransactionController::class, 'forfeit'])->name('transactions.forfeit');

        // POS Module
        Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
        Route::post('/pos/sell', [POSController::class, 'store'])->name('pos.sell');
        Route::get('/pos/receipt/{sale}', [POSController::class, 'receipt'])->name('pos.receipt');
        Route::post('/pos/auto-forfeit', [POSController::class, 'autoForfeit'])->name('pos.auto-forfeit');
    });

    // ─── Categories and Safes (Manager, Admin) ───────────────
    Route::middleware('role:manager')->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('safes', SafeController::class);
        
        // Approvals
        Route::get('/approvals', [\App\Http\Controllers\ApprovalController::class, 'index'])->name('approvals.index');
        Route::post('/approvals/{approval}/approve', [\App\Http\Controllers\ApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('/approvals/{approval}/reject', [\App\Http\Controllers\ApprovalController::class, 'reject'])->name('approvals.reject');
    });

    // ─── Location API (AJAX cascading dropdowns) ─────────────
    Route::get('/api/provinces/{region}', [LocationController::class, 'provinces'])->name('api.provinces');
    Route::get('/api/cities/{province}', [LocationController::class, 'cities'])->name('api.cities');
    Route::get('/api/barangays/{city}', [LocationController::class, 'barangays'])->name('api.barangays');

    // ─── Reports Module (Manager, Admin) ─────────────────────
    Route::middleware('role:manager')->group(function () {
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/summary', [ReportController::class, 'summaryReport'])->name('summary');
            Route::get('/transactions', [ReportController::class, 'transactionsReport'])->name('transactions');
            Route::get('/payments', [ReportController::class, 'paymentsReport'])->name('payments');
            Route::get('/sales', [ReportController::class, 'salesReport'])->name('sales');
            Route::get('/inventory', [ReportController::class, 'inventoryReport'])->name('inventory');
            Route::get('/export/pdf/{type}', [ReportController::class, 'exportPdf'])->name('export.pdf');
        });
    });
});

// Admin Only Routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');

    // User Management
    Route::resource('users', \App\Http\Controllers\UserController::class)->except(['show']);
});

require __DIR__.'/auth.php';

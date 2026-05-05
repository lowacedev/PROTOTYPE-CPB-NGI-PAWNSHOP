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
    return view('dashboard', [
        'customerCount'    => Customer::count(),
        'activeCount'      => Transaction::where('status', 'active')->count(),
        'itemsCount'       => Item::count(),
        'totalLoanAmount'  => Transaction::where('status', 'active')->sum('loan_amount'),
        'recentTransactions' => Transaction::with('customer')->latest()->limit(5)->get(),
    ]);
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ─── Pawnshop Management (Teller, Manager, Admin) ─────────
    Route::middleware('role:manager,teller')->group(function () {
        Route::resource('customers', CustomerController::class);
        Route::resource('items', ItemController::class);
        Route::resource('transactions', TransactionController::class);

        // Transaction Actions (Renewal & Redemption)
        Route::get('/transactions-actions/search', [TransactionController::class, 'actionSearch'])->name('transactions.actions.search');
        Route::get('/api/transactions-actions/search', [TransactionController::class, 'searchTransactionApi'])->name('api.transactions.actions.search');
        
        Route::get('/transactions/{transaction}/renew', [TransactionController::class, 'showRenewalForm'])->name('transactions.renew.form');
        Route::post('/transactions/{transaction}/renew', [TransactionController::class, 'processRenewal'])->name('transactions.renew.process');
        
        Route::get('/transactions/{transaction}/redeem', [TransactionController::class, 'showRedemptionForm'])->name('transactions.redeem.form');
        Route::post('/transactions/{transaction}/redeem', [TransactionController::class, 'processRedemption'])->name('transactions.redeem.process');
        
        Route::get('/transactions/{transaction}/action-receipt/{payment}', [TransactionController::class, 'actionReceipt'])->name('transactions.action-receipt');

        Route::post('/transactions/{transaction}/forfeit', [TransactionController::class, 'forfeit'])->name('transactions.forfeit');

        // Payments
        Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store', 'destroy']);
        Route::get('/payments/transaction/{transactionId}', [PaymentController::class, 'create'])->name('payments.create.for-transaction');

        // ─── Pawn Wizard (Multi-step) ────────────────────────────
        Route::get('/pawn/wizard', [PawnWizardController::class, 'create'])->name('pawn.wizard');
        Route::post('/pawn/wizard', [PawnWizardController::class, 'store'])->name('pawn.wizard.store');
        Route::get('/pawn/receipt/{transaction}', [PawnWizardController::class, 'receipt'])->name('pawn.receipt');
        Route::get('/api/customers/search', [PawnWizardController::class, 'searchCustomers'])->name('api.customers.search');
    });

    // ─── Categories and Safes (Manager, Admin) ───────────────
    Route::middleware('role:manager')->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('safes', SafeController::class);
    });

    // ─── POS Module (Cashier, Manager, Admin) ────────────────
    Route::middleware('role:manager,cashier')->group(function () {
        Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
        Route::post('/pos/sell', [POSController::class, 'store'])->name('pos.sell');
        Route::get('/pos/receipt/{sale}', [POSController::class, 'receipt'])->name('pos.receipt');
        Route::post('/pos/auto-forfeit', [POSController::class, 'autoForfeit'])->name('pos.auto-forfeit');
    });

    // ─── Location API (AJAX cascading dropdowns) ─────────────
    Route::get('/api/provinces/{region}', [LocationController::class, 'provinces'])->name('api.provinces');
    Route::get('/api/cities/{province}', [LocationController::class, 'cities'])->name('api.cities');
    Route::get('/api/barangays/{city}', [LocationController::class, 'barangays'])->name('api.barangays');

    // ─── Reports Module (Manager, Admin) ─────────────────────
    Route::middleware('role:manager')->group(function () {
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/transactions', [ReportController::class, 'transactionsReport'])->name('transactions');
            Route::get('/payments', [ReportController::class, 'paymentsReport'])->name('payments');
            Route::get('/sales', [ReportController::class, 'salesReport'])->name('sales');
            Route::get('/forfeited', [ReportController::class, 'forfeitedReport'])->name('forfeited');
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

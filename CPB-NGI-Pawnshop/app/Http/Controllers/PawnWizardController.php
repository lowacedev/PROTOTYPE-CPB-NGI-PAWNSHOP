<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Category;
use App\Models\Region;
use App\Models\Safe;
use App\Models\Item;
use App\Models\Transaction;
use App\Http\Requests\StorePawnWizardRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PawnWizardController extends Controller
{
    /**
     * Show the multi-step pawn wizard.
     */
    public function create()
    {
        $categories = Category::all();
        $regions = Region::orderBy('name')->get();
        return view('pawn-wizard.create', compact('categories', 'regions'));
    }

    /**
     * Store the wizard payload.
     */
    public function store(StorePawnWizardRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            // 1. Resolve Customer
            $customer = null;
            if ($validated['customer_type'] === 'existing') {
                $customer = Customer::findOrFail($validated['customer_id']);
            } else {
                // Handle file upload
                $imagePath = null;
                if ($request->hasFile('id_image')) {
                    $imagePath = $request->file('id_image')->store('customer-ids', 'public');
                }

                // Create new customer
                $customer = Customer::create([
                    'first_name'   => $validated['first_name'],
                    'middle_name'  => $validated['middle_name'] ?? null,
                    'last_name'    => $validated['last_name'],
                    'email'        => $validated['email'] ?? null,
                    'phone_number' => $validated['phone_number'],
                    'region_id'    => $validated['region_id'],
                    'province_id'  => $validated['province_id'],
                    'city_id'      => $validated['city_id'],
                    'barangay_id'  => $validated['barangay_id'],
                    'address_line' => $validated['address_line'] ?? null,
                    'id_type'      => $validated['id_type'],
                    'id_number'    => $validated['id_number'],
                    'id_image_path'=> $imagePath,
                    'notes'        => $validated['notes'] ?? null,
                    'is_active'    => true,
                ]);
            }

            // 2. Resolve Safe (Get first active safe, or create a default one if none exists)
            $safe = Safe::where('status', 'active')->first();
            if (!$safe) {
                $safe = Safe::create([
                    'safe_code' => 'DEFAULT-SAFE',
                    'name' => 'Main Safe',
                    'location' => 'Store Front',
                    'capacity' => 1000000,
                    'items_capacity' => 1000,
                    'status' => 'active',
                ]);
            }

            // 3. Create Item
            $item = Item::create([
                'item_code'       => 'ITEM-' . now()->format('YmdHis') . '-' . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT),
                'category_id'     => $validated['category_id'],
                'safe_id'         => $safe->id,
                'name'            => $validated['item_name'],
                'description'     => $validated['item_description'] ?? null,
                'appraised_value' => $validated['assessed_value'],
                'condition'       => $validated['condition'],
                'is_available'    => false, // Currently pawned
            ]);

            // 4. Create Transaction
            $transaction = Transaction::create([
                'customer_id'      => $customer->id,
                'user_id'          => auth()->id(),
                'transaction_type' => 'pawn',
                'loan_amount'      => $validated['loan_amount'],
                'interest_rate'    => $validated['interest_rate'],
                'term_days'        => $validated['term_days'],
                'transaction_date' => now(),
                'maturity_date'    => now()->addDays((int) $validated['term_days']),
                'status'           => 'active',
            ]);

            // 5. Link Item to Transaction
            $transaction->items()->create([
                'item_id'         => $item->id,
                'appraised_value' => $item->appraised_value,
                'quantity'        => 1,
            ]);

            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'pawn',
                'model_type' => 'Transaction',
                'model_id' => $transaction->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'description' => "Processed new pawn transaction #{$transaction->pawn_ticket_number} for customer {$customer->full_name}.",
            ]);

            DB::commit();

            return redirect()->route('pawn.receipt', $transaction)
                             ->with('success', 'Pawn transaction completed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'An error occurred while saving: ' . $e->getMessage());
        }
    }

    /**
     * Show receipt page
     */
    public function receipt(Transaction $transaction)
    {
        // Ensure user can view this receipt
        $transaction->load(['customer', 'items.item.category', 'user']);
        
        return view('pawn-wizard.receipt', compact('transaction'));
    }

    /**
     * API: Search customers
     */
    public function searchCustomers(Request $request)
    {
        $search = $request->query('q');
        
        if (empty($search)) {
            return response()->json([]);
        }

        $customers = Customer::where('first_name', 'like', "%{$search}%")
            ->orWhere('last_name', 'like', "%{$search}%")
            ->orWhere('phone_number', 'like', "%{$search}%")
            ->limit(10)
            ->get();

        $results = $customers->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => $c->full_name,
                'phone' => $c->phone_number,
                'address' => $c->full_address,
                'first_name' => $c->first_name,
                'last_name' => $c->last_name,
            ];
        });

        return response()->json($results);
    }
}

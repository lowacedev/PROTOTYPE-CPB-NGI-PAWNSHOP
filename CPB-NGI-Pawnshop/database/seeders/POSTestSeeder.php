<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;

class POSTestSeeder extends Seeder
{
    public function run(): void
    {
        // Use existing customer
        $customer = Customer::first();
        if (!$customer) {
            $this->command->error('❌ No customers found. Please create a pawn transaction first.');
            return;
        }

        // Get a staff user
        $user = User::first();

        // Create categories if they don't exist
        $jewelry     = Category::firstOrCreate(['name' => 'Gold Jewelry']);
        $electronics = Category::firstOrCreate(['name' => 'Electronics']);
        $watches     = Category::firstOrCreate(['name' => 'Watches']);
        $accessories = Category::firstOrCreate(['name' => 'Accessories']);

        // Items that are still "stored" (pawned) — NOT yet for_sale
        $items = [
            ['name' => 'Silver Ring',           'category_id' => $jewelry->id,     'appraised_value' => 1500.00,  'item_code' => 'ITM-EXP-5534'],
            ['name' => 'Rolex Watch (Replica)',  'category_id' => $watches->id,     'appraised_value' => 8000.00,  'item_code' => 'ITM-EXP-4973'],
            ['name' => 'Diamond Earrings',       'category_id' => $jewelry->id,     'appraised_value' => 12000.00, 'item_code' => 'ITM-EXP-6864'],
            ['name' => 'MacBook Pro M1',         'category_id' => $electronics->id, 'appraised_value' => 25000.00, 'item_code' => 'ITM-EXP-5746'],
            ['name' => 'Canon DSLR Camera',      'category_id' => $electronics->id, 'appraised_value' => 15000.00, 'item_code' => 'ITM-EXP-6345'],
            ['name' => 'Gold Necklace 18K',      'category_id' => $jewelry->id,     'appraised_value' => 22000.00, 'item_code' => 'ITM-EXP-7801'],
            ['name' => 'Samsung Galaxy S24',     'category_id' => $electronics->id, 'appraised_value' => 18000.00, 'item_code' => 'ITM-EXP-7902'],
            ['name' => 'Ray-Ban Aviator',        'category_id' => $accessories->id, 'appraised_value' => 3500.00,  'item_code' => 'ITM-EXP-8123'],
            ['name' => 'iPad Pro 12.9"',         'category_id' => $electronics->id, 'appraised_value' => 32000.00, 'item_code' => 'ITM-EXP-8201'],
            ['name' => 'Pearl Bracelet',          'category_id' => $jewelry->id,     'appraised_value' => 5500.00,  'item_code' => 'ITM-EXP-8302'],
            ['name' => 'Casio G-Shock',           'category_id' => $watches->id,     'appraised_value' => 4200.00,  'item_code' => 'ITM-EXP-8403'],
            ['name' => 'Sony WH-1000XM5',         'category_id' => $electronics->id, 'appraised_value' => 9800.00,  'item_code' => 'ITM-EXP-8504'],
            ['name' => 'Louis Vuitton Wallet',    'category_id' => $accessories->id, 'appraised_value' => 14000.00, 'item_code' => 'ITM-EXP-8605'],
        ];

        // Create items as "stored" (still pawned, not yet eligible for sale)
        $createdItems = [];
        foreach ($items as $data) {
            $createdItems[] = Item::updateOrCreate(
                ['item_code' => $data['item_code']],
                array_merge($data, [
                    'condition'    => 'good',
                    'is_available' => false,
                    'item_status'  => 'stored',
                ])
            );
        }

        // Create an EXPIRED active transaction (maturity_date in the past)
        // so auto-forfeit will pick it up and mark items as for_sale
        $transaction = Transaction::create([
            'customer_id'      => $customer->id,
            'user_id'          => $user->id,
            'transaction_type' => 'pawn',
            'loan_amount'      => 50000.00,
            'interest_rate'    => 3.00,
            'term_days'        => 30,
            'transaction_date' => now()->subDays(60),  // Pawned 60 days ago
            'maturity_date'    => now()->subDays(30),  // Expired 30 days ago
            'status'           => 'active',            // Still "active" — should be forfeited
        ]);

        // Link items to the expired transaction
        foreach ($createdItems as $item) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'item_id'        => $item->id,
                'appraised_value'=> $item->appraised_value,
                'quantity'       => 1,
            ]);
        }

        $this->command->info('✅ Created 13 items with an EXPIRED transaction (status=active, maturity_date=' . $transaction->maturity_date->format('Y-m-d') . ')');
        $this->command->info('   Items are currently "stored". Open the POS page to test auto-forfeit!');
    }
}

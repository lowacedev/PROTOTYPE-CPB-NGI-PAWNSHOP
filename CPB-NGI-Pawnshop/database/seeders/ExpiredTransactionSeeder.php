<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpiredTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::first() ?? \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $customer = \App\Models\Customer::first() ?? \App\Models\Customer::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '09123456789',
            'address' => '123 Fake St.',
        ]);

        $category = \App\Models\Category::first() ?? \App\Models\Category::create([
            'name' => 'Jewelry',
            'description' => 'Gold and silver',
        ]);

        $itemsData = [
            ['name' => 'Silver Ring', 'value' => 1500.00, 'loan' => 1000.00],
            ['name' => 'Rolex Watch (Replica)', 'value' => 8000.00, 'loan' => 5000.00],
            ['name' => 'Diamond Earrings', 'value' => 12000.00, 'loan' => 8000.00],
            ['name' => 'MacBook Pro M1', 'value' => 25000.00, 'loan' => 18000.00],
            ['name' => 'Canon DSLR Camera', 'value' => 15000.00, 'loan' => 10000.00]
        ];

        foreach ($itemsData as $data) {
            // Create Item
            $item = \App\Models\Item::create([
                'item_code' => 'ITM-EXP-' . rand(1000, 9999),
                'name' => $data['name'],
                'category_id' => $category->id,
                'appraised_value' => $data['value'],
                'is_available' => false,
                'item_status' => 'stored',
            ]);

            // Create Transaction from 60 days ago
            $transactionDate = now()->subDays(rand(60, 90));
            $maturityDate = $transactionDate->copy()->addDays(30);

            $transaction = \App\Models\Transaction::create([
                'customer_id' => $customer->id,
                'user_id' => $user->id,
                'transaction_type' => 'pawn',
                'loan_amount' => $data['loan'],
                'interest_rate' => 5.00,
                'term_days' => 30,
                'transaction_date' => $transactionDate,
                'maturity_date' => $maturityDate,
                'status' => 'active',
            ]);

            // Attach Item to Transaction
            \App\Models\TransactionItem::create([
                'transaction_id' => $transaction->id,
                'item_id' => $item->id,
                'appraised_value' => $data['value'],
                'quantity' => 1,
            ]);
        }
    }
}

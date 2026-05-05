<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('pawn_ticket_number')->unique();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('transaction_type', ['pawn', 'renewal', 'redemption'])->default('pawn');
            $table->decimal('loan_amount', 12, 2);
            $table->decimal('interest_rate', 5, 2)->default(5.00);
            $table->integer('term_days')->default(30);
            $table->dateTime('transaction_date');
            $table->dateTime('maturity_date');
            $table->dateTime('redemption_date')->nullable();
            $table->enum('status', ['active', 'renewed', 'redeemed', 'forfeited'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

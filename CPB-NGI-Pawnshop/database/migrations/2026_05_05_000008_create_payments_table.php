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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete();
            $table->decimal('amount_paid', 12, 2);
            $table->enum('payment_type', ['interest', 'redemption', 'partial'])->default('interest');
            $table->enum('payment_method', ['cash', 'check', 'card', 'bank_transfer'])->default('cash');
            $table->dateTime('payment_date');
            $table->string('receipt_number')->unique()->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

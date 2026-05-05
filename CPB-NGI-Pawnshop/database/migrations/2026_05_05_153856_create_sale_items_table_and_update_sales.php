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
        // SQLite doesn't easily support dropping foreign keys/columns in a single table without doctrine/dbal,
        // but Laravel 11 handles it better. Let's make sure we define it carefully.
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->dropColumn('item_id');
            $table->dropColumn('selling_price');

            $table->decimal('total', 12, 2)->after('user_id')->default(0);
            $table->decimal('amount_tendered', 12, 2)->after('total')->default(0);
            $table->decimal('change', 12, 2)->after('amount_tendered')->default(0);
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->decimal('price', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['total', 'amount_tendered', 'change']);
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->decimal('selling_price', 12, 2);
        });
    }
};

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
        Schema::create('safes', function (Blueprint $table) {
            $table->id();
            $table->string('safe_code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('location');
            $table->integer('items_capacity')->default(50);
            $table->decimal('capacity', 12, 2)->nullable();
            $table->decimal('current_value', 12, 2)->default(0);
            $table->boolean('is_personal')->default(false);
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->enum('condition', ['excellent', 'good', 'fair', 'poor'])->default('good');
            $table->enum('status', ['active', 'maintenance', 'archived'])->default('active');
            $table->string('serial_number')->nullable()->unique();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('safes');
    }
};

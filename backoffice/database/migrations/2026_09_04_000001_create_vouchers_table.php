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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->string('type', 30)->default('percentage'); // percentage, fixed_amount, free_shipping
            $table->unsignedBigInteger('amount')->default(0); // percentage value (e.g. 15) or fixed rupiah (e.g. 50000)
            $table->unsignedBigInteger('max_discount_amount')->nullable(); // Max cap for percentage discounts
            $table->unsignedBigInteger('min_order_amount')->default(0); // Minimum subtotal required
            $table->unsignedInteger('usage_limit_total')->nullable(); // Total times voucher can be used
            $table->unsignedInteger('used_count')->default(0); // Usage counter
            $table->unsignedInteger('usage_limit_per_user')->default(1); // Max uses per customer email
            $table->timestamp('valid_from')->useCurrent();
            $table->timestamp('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true); // Can be claimed / displayed publicly
            $table->timestamps();
            $table->softDeletes();

            $table->index(['code', 'is_active']);
            $table->index(['valid_from', 'valid_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};

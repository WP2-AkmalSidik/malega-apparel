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
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained('order_items')->nullOnDelete();
            $table->unsignedTinyInteger('rating')->default(5)->index(); // 1 to 5
            $table->string('headline')->nullable(); // e.g. "Bahan sangat adem & pas di badan"
            $table->text('review');
            $table->string('fit_rating')->nullable(); // true_to_size, runs_small, runs_large
            $table->boolean('is_verified_purchase')->default(true)->index();
            $table->string('status')->default('approved')->index(); // approved, pending, hidden
            $table->text('admin_reply')->nullable();
            $table->timestamp('admin_replied_at')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'status']);
            $table->index(['order_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};

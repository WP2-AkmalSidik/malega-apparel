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
        // 1. Orders table
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 32)->unique(); // e.g. MLG-20260829-0001
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('source')->default('web'); // web, backoffice, whatsapp
            $table->string('order_status')->default('pending'); // pending, processing, completed, cancelled
            $table->string('payment_status')->default('unpaid'); // unpaid, paid, refunded, failed
            $table->string('fulfillment_status')->default('unfulfilled'); // unfulfilled, partial, fulfilled, delivered, returned
            $table->unsignedBigInteger('subtotal'); // IDR integer
            $table->unsignedBigInteger('discount_total')->default(0);
            $table->unsignedBigInteger('shipping_total')->default(0);
            $table->unsignedBigInteger('tax_total')->default(0);
            $table->unsignedBigInteger('grand_total');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('order_status');
            $table->index('payment_status');
            $table->index('fulfillment_status');
            $table->index('created_at');
        });

        // 2. Order Items table (Immutable Snapshot ADR-006)
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->string('product_name'); // Snapshot
            $table->string('variant_title'); // Snapshot
            $table->string('sku'); // Snapshot
            $table->unsignedBigInteger('unit_price'); // Snapshot in IDR integer
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('subtotal'); // unit_price * quantity
            $table->timestamps();

            $table->index('order_id');
            $table->index('sku');
        });

        // 3. Order Addresses table
        Schema::create('order_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('recipient_name');
            $table->string('phone');
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->string('city');
            $table->string('province');
            $table->string('postal_code');
            $table->string('courier_name')->nullable(); // e.g. JNE, J&T, SiCepat
            $table->string('tracking_number')->nullable();
            $table->timestamps();

            $table->index('order_id');
        });

        // 4. Order Status Histories table (Audit timeline)
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('from_status');
            $table->string('to_status');
            $table->string('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('order_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
        Schema::dropIfExists('order_addresses');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};

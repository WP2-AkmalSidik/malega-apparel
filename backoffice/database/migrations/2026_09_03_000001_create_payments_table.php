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
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('payment_gateway', 32)->default('duitku'); // duitku, manual_bank, cod
            $table->string('merchant_order_id', 64)->unique(); // e.g. MLG-20260903-0001
            $table->string('reference', 64)->nullable()->index(); // Duitku Reference Number
            $table->string('payment_method', 32)->nullable(); // e.g. VC, VA, BT, B1, QR, OV, DA, SP, etc.
            $table->string('payment_method_name', 100)->nullable(); // e.g. BCA Virtual Account, QRIS, etc.
            $table->unsignedBigInteger('amount'); // IDR integer
            $table->string('status', 32)->default('pending'); // pending, success, failed, expired
            $table->text('payment_url')->nullable(); // Redirect payment URL from Duitku
            $table->string('va_number', 64)->nullable(); // Virtual account number if applicable
            $table->text('qr_string')->nullable(); // QRIS payload string if applicable
            $table->json('payload')->nullable(); // Inquiry request payload
            $table->json('callback_payload')->nullable(); // Webhook notification payload
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('status');
            $table->index('created_at');
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

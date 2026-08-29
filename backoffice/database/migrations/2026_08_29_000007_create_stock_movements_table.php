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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('type');
            $table->integer('quantity_change');
            $table->unsignedInteger('on_hand_before');
            $table->unsignedInteger('on_hand_after');
            $table->unsignedInteger('reserved_before');
            $table->unsignedInteger('reserved_after');
            $table->string('reference_note')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('inventory_item_id');
            $table->index('type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};

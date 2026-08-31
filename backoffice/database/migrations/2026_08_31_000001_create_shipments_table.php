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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('biteship_order_id')->nullable()->index();
            $table->string('biteship_tracking_id')->nullable();
            $table->string('courier_company'); // e.g. jne, sicepat, jnt
            $table->string('courier_service_name')->default('reg'); // e.g. reg, standard, instant
            $table->string('waybill_id')->index(); // Tracking Number / Nomor Resi
            $table->string('tracking_url')->nullable();
            $table->unsignedBigInteger('shipment_fee')->default(0); // IDR integer
            $table->string('status')->default('confirmed'); // confirmed, picking_up, picked, in_transit, delivered, cancelled, rejected
            $table->json('shipper_snapshot')->nullable();
            $table->json('destination_snapshot')->nullable();
            $table->json('tracking_history')->nullable(); // Chronological timeline milestones
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('shipments');
    }
};

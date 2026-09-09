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
        Schema::create('membership_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->unsignedBigInteger('min_spend')->default(0)->index();
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->nullOnDelete();
            $table->string('badge_text')->nullable();
            $table->string('badge_color')->default('slate'); // slate, amber, gold
            $table->string('discount_label')->nullable(); // e.g. "Diskon 10%", "Diskon 15%"
            $table->text('description')->nullable();
            $table->json('perks')->nullable();
            $table->integer('order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('membership_tier_id')->nullable()->after('membership_tier')->constrained('membership_tiers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('membership_tier_id');
        });

        Schema::dropIfExists('membership_tiers');
    }
};

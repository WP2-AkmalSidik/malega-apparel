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
        Schema::table('vouchers', function (Blueprint $table) {
            $table->boolean('allow_guest')->default(true)->after('is_public');
        });

        Schema::table('voucher_usages', function (Blueprint $table) {
            $table->string('customer_phone', 50)->nullable()->after('customer_email')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn('allow_guest');
        });

        Schema::table('voucher_usages', function (Blueprint $table) {
            $table->dropColumn('customer_phone');
        });
    }
};

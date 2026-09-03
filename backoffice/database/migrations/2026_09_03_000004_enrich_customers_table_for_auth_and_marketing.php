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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('password')->nullable()->after('phone');
            $table->string('avatar')->nullable()->after('password');
            $table->boolean('is_active')->default(true)->after('avatar');
            $table->boolean('marketing_opt_in')->default(true)->after('is_active');
            $table->string('membership_tier')->default('Silver')->after('marketing_opt_in'); // Silver, Gold, VIP Platinum
            $table->json('saved_addresses')->nullable()->after('total_spend_amount');
            $table->json('wishlist')->nullable()->after('saved_addresses');
            $table->timestamp('last_login_at')->nullable()->after('wishlist');
            $table->rememberToken()->after('last_login_at');

            $table->index('is_active');
            $table->index('marketing_opt_in');
            $table->index('membership_tier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'password',
                'avatar',
                'is_active',
                'marketing_opt_in',
                'membership_tier',
                'saved_addresses',
                'wishlist',
                'last_login_at',
                'remember_token',
            ]);
        });
    }
};

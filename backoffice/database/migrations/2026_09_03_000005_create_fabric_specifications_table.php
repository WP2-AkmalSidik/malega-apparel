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
        Schema::create('fabric_specifications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('brand')->default('Malega Apparel');
            $table->string('gramasi');
            $table->string('material');
            $table->string('fit_cutting')->nullable();
            $table->string('collar_hood')->nullable();
            $table->string('care_instructions')->nullable();
            $table->json('extra_specs')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Add fabric_spec_id to products table as nullable foreign key
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('fabric_spec_id')->nullable()->after('category_id')->constrained('fabric_specifications')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['fabric_spec_id']);
            $table->dropColumn('fabric_spec_id');
        });

        Schema::dropIfExists('fabric_specifications');
    }
};

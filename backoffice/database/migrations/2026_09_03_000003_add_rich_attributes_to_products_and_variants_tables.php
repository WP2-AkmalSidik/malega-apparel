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
        // 1. Enrich products table
        Schema::table('products', function (Blueprint $table) {
            $table->string('subtitle')->nullable()->after('name');
            $table->string('badge')->nullable()->after('subtitle');
            $table->decimal('rating', 3, 2)->default(4.90)->after('badge');
            $table->unsignedInteger('review_count')->default(0)->after('rating');
            $table->unsignedInteger('sold_count')->default(0)->after('review_count');
            $table->string('material')->nullable()->after('description');
            $table->unsignedSmallInteger('gsm')->nullable()->after('material');
            $table->string('fit')->nullable()->after('gsm');
            $table->string('origin')->default('Bandung, Indonesia')->after('fit');
            $table->json('features')->nullable()->after('origin');
            $table->json('specifications')->nullable()->after('features');
        });

        // 2. Enrich product_variants table
        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('color_name')->nullable()->after('title');
            $table->string('color_hex', 16)->nullable()->after('color_name');
            $table->string('size', 32)->nullable()->after('color_hex');
            $table->text('image_url')->nullable()->after('size');
        });

        // 3. Enrich collections table
        Schema::table('collections', function (Blueprint $table) {
            $table->string('subtitle')->nullable()->after('name');
            $table->string('season')->nullable()->after('subtitle');
            $table->string('release_year', 8)->nullable()->after('season');
            $table->string('badge')->nullable()->after('release_year');
            $table->text('cover_image')->nullable()->after('banner_path');
            $table->text('banner_image')->nullable()->after('cover_image');
            $table->string('featured_material')->nullable()->after('banner_image');
            $table->unsignedSmallInteger('gsm_weight')->nullable()->after('featured_material');
            $table->text('storytelling')->nullable()->after('description');
            $table->json('palette')->nullable()->after('storytelling');
            $table->json('tags')->nullable()->after('palette');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->dropColumn([
                'subtitle',
                'season',
                'release_year',
                'badge',
                'cover_image',
                'banner_image',
                'featured_material',
                'gsm_weight',
                'storytelling',
                'palette',
                'tags',
            ]);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn([
                'color_name',
                'color_hex',
                'size',
                'image_url',
            ]);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'subtitle',
                'badge',
                'rating',
                'review_count',
                'sold_count',
                'material',
                'gsm',
                'fit',
                'origin',
                'features',
                'specifications',
            ]);
        });
    }
};

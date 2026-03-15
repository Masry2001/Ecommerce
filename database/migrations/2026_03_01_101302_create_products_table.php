<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id')->constrained('categories', 'id')->cascadeOnDelete();
            $table->foreignUuid('brand_id')->nullable()->constrained('brands', 'id')->nullOnDelete();
            $table->string('slug', 50)->unique();
            $table->string('sku', 20)->unique();
            $table->string('name', 50);
            $table->string('short_description', 500)->nullable();
            $table->string('description', 5000)->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('compare_price', 10, 2)->nullable();
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->unsignedSmallInteger('stock_quantity')->default(0);
            $table->unsignedTinyInteger('low_stock_threshold')->default(10);
            $table->boolean('manage_stock')->default(true);
            $table->enum('stock_status', ['in_stock', 'out_of_stock', 'low_stock', 'on_backorder'])->default('in_stock');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('has_variants')->default(false);
            $table->decimal('weight', 4, 2)->nullable(); // max weight is 99.99
            $table->string('meta_title', 60)->nullable();
            $table->string('meta_description', 160)->nullable();
            $table->integer('views_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });


        DB::statement('ALTER TABLE products ADD CONSTRAINT chk_product_price_positive CHECK (price >= 0)');
        DB::statement('ALTER TABLE products ADD CONSTRAINT chk_product_compare_price_positive CHECK (compare_price >= 0)');
        DB::statement('ALTER TABLE products ADD CONSTRAINT chk_product_cost_price_positive CHECK (cost_price >= 0)');
        DB::statement('ALTER TABLE products ADD CONSTRAINT chk_product_stock_quantity_positive CHECK (stock_quantity >= 0)');
        DB::statement('ALTER TABLE products ADD CONSTRAINT chk_product_low_stock_threshold_positive CHECK (low_stock_threshold >= 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

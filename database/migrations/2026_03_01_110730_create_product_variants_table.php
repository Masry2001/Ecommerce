<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products', 'id')->cascadeOnDelete();
            $table->string('sku', 20)->unique();
            $table->string('name', 50);
            $table->json('options');
            $table->decimal('price', 10, 2);
            $table->decimal('compare_price', 10, 2)->nullable();
            $table->unsignedSmallInteger('stock_quantity')->default(0);
            $table->enum('stock_status', ['in_stock', 'out_of_stock', 'low_stock', 'on_backorder'])->default('in_stock');
            $table->boolean('is_active')->default(true);
            $table->smallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        DB::statement('ALTER TABLE product_variants ADD CONSTRAINT chk_product_variant_price_positive CHECK (price >= 0)');
        DB::statement('ALTER TABLE product_variants ADD CONSTRAINT chk_product_variant_compare_price_positive CHECK (compare_price >= 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};

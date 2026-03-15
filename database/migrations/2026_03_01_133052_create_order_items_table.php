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
        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->constrained('orders', 'id')->cascadeOnDelete();
            $table->foreignUuid('product_id')->nullable()->constrained('products', 'id')->nullOnDelete();
            $table->foreignUuid('product_variant_id')->nullable()->constrained('product_variants', 'id')->nullOnDelete();
            $table->string('product_name', 100);
            $table->string('product_sku', 40);
            $table->string('variant_name', 100)->nullable();
            $table->string('variant_sku', 40)->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedSmallInteger('quantity');
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });

        DB::statement('ALTER TABLE order_items ADD CONSTRAINT chk_order_item_price_positive CHECK (price >= 0)');
        DB::statement('ALTER TABLE order_items ADD CONSTRAINT chk_order_item_subtotal_positive CHECK (subtotal >= 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

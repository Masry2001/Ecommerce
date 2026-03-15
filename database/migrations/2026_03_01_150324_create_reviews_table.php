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
        Schema::create('reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')->nullable()->constrained('customers', 'id')->nullOnDelete();
            $table->foreignUuid('product_id')->constrained('products', 'id')->cascadeOnDelete();
            $table->foreignUuid('order_id')->nullable()->constrained('orders', 'id')->nullOnDelete();
            $table->string('customer_name', 50); // For the Customer UI (showing publically)
            $table->string('customer_email', 50); // For the Admin UI (identifying and contacting the reviewer)
            $table->unsignedTinyInteger('rating')->comment('Rating from 1 to 5');
            $table->string('title', 50);
            $table->string('comment', 1000);
            $table->boolean('is_verified_purchase')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
            // ensure one review per customer per product
            $table->unique(['customer_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};

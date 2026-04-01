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
        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Total history: Keep record if coupon is gone
            $table->foreignUuid('coupon_id')->nullable()->constrained('coupons', 'id')->nullOnDelete();

            // Privacy: Keep usage count if customer is gone
            $table->foreignUuid('customer_id')->nullable()->constrained('customers', 'id')->nullOnDelete();

            // Integrity: Delete usage if the order itself is erased
            $table->foreignUuid('order_id')->constrained('orders', 'id')->cascadeOnDelete();

            // Snapshots (The "Historical Record")
            $table->string('coupon_code', 20);    // Saved at the time of purchase
            $table->string('customer_email', 50); // Saved at the time of purchase
            $table->decimal('discount_amount', 8, 2); // Saved how much they actually saved
            $table->timestamps();
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE coupon_usages ADD CONSTRAINT chk_coupon_discount_amount_positive CHECK (discount_amount >= 0)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_usages');
    }
};

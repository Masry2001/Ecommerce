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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')->nullable()->constrained('customers', 'id')->nullOnDelete();
            $table->foreignUuid('coupon_id')->nullable()->constrained('coupons', 'id')->nullOnDelete();
            $table->string('order_number', 32)->unique(); //ORD-99231

            // Order amounts
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_amount', 8, 2)->default(0);
            $table->decimal('shipping_cost', 8, 2)->default(0);
            $table->decimal('tax_amount', 8, 2)->default(0);
            $table->decimal('total', 10, 2);

            // Status (Current Status for quick filtering)
            $table->enum('order_status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'returned'])->default('pending')->index();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending')->index();

            // Internal tracking
            $table->string('customer_ip', 45)->nullable(); // IPv4 max 15 char, IPv6 max 45 char.

            $table->timestamps();
            $table->softDeletes();
        });

        // Add the CHECK constraints using a raw statement, because Laravel12 doesn't support CHECK constraints directly.
        // and i don't want to use unsignedDecimal because it's deprecated since MySQL 8.0.17
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_order_subtotal_positive CHECK (subtotal >= 0)');
            DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_order_discount_positive CHECK (discount_amount >= 0)');
            DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_order_shipping_positive CHECK (shipping_cost >= 0)');
            DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_order_tax_positive CHECK (tax_amount >= 0)');
            DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_order_total_positive CHECK (total >= 0)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

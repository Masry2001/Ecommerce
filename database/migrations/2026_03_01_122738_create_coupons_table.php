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
        Schema::create('coupons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 20)->unique();
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('value', 8, 2);
            $table->decimal('min_order_value', 10, 2)->nullable();
            $table->decimal('max_discount', 8, 2)->nullable();
            $table->unsignedSmallInteger('usage_limit')->nullable()->comment('total number of times the coupon can be used by all customers');
            $table->unsignedTinyInteger('usage_limit_per_customer')->nullable()->comment('number of times the coupon can be used per customer');
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Add the CHECK constraints using a raw statement, because Laravel12 doesn't support CHECK constraints directly.
        // and i don't want to use unsignedDecimal because it's deprecated since MySQL 8.0.17
        DB::statement('ALTER TABLE coupons ADD CONSTRAINT chk_coupon_value_positive CHECK (value >= 0)');
        DB::statement('ALTER TABLE coupons ADD CONSTRAINT chk_coupon_min_order_positive CHECK (min_order_value >= 0)');
        DB::statement('ALTER TABLE coupons ADD CONSTRAINT chk_coupon_max_discount_positive CHECK (max_discount >= 0)');
        DB::statement('ALTER TABLE coupons ADD CONSTRAINT chk_coupon_usage_limit_positive CHECK (usage_limit >= 0)');
        DB::statement('ALTER TABLE coupons ADD CONSTRAINT chk_coupon_usage_limit_per_customer_positive CHECK (usage_limit_per_customer >= 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};

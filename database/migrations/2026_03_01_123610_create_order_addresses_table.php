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
    Schema::create('order_addresses', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->foreignUuid('order_id')->constrained('orders')->cascadeOnDelete();
      $table->enum('type', ['shipping', 'billing']);

      $table->string('full_name');
      $table->string('phone', 20);
      $table->string('address_line_1');
      $table->string('address_line_2')->nullable();
      $table->string('city', 20);
      $table->string('state', 20)->nullable();
      $table->string('postal_code', 10);
      $table->string('country', 20);

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('order_addresses');
  }
};

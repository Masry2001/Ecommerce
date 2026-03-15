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
        Schema::create('settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key', 64)->unique();  // e.g. 'site_name'
            $table->text('value')->nullable(); // e.g. 'My Super Shop'
            $table->string('type', 20)->default('string'); // Helps you cast to boolean/int in PHP
            $table->string('group', 32)->default('general'); // e.g. 'seo', 'social', 'contact'

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};

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
        Schema::create('blacklisted_software_statuses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('product_blacklisted_id');
            $table->bigInteger('basic_id');
            $table->bigInteger('change_log_id');
            $table->longText('caption')->nullable();
            $table->integer('license_status')->comment('1 - Newly Added, 2 - Change Detected, 3 - Missing');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blacklisted_software_statuses');
    }
};

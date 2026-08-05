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
        Schema::create('compliance_applications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('change_log_id');
            $table->bigInteger('basic_id');
            $table->longText('caption');
            $table->integer('status')->comment('1 - Newly Added, 2 - Change Detected, 3 - Missing');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_applications');
    }
};

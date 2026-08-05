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
        Schema::create('problem_impacted_devices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('device_id')->nullable();
            $table->tinyInteger('problem_id')->nullable();
            $table->boolean('status')->nullable();
            $table->timestamps();
            $table->bigInteger('handler_id')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('problem_impacted_devices');
    }
};

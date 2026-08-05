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
        Schema::create('sw_tracking_data_processed', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('serial', 191)->nullable();
            $table->string('file_path', 191)->nullable();
            $table->date('date')->nullable();
            $table->integer('device_id')->nullable();
            $table->integer('is_processed')->default(0);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sw_tracking_data_processed');
    }
};

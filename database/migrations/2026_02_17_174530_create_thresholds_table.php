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
        Schema::create('thresholds', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cat_id')->nullable()->unique('cat_id');
            $table->integer('threshold')->nullable()->default(0);
            $table->integer('notify_count')->nullable()->default(0);
            $table->dateTime('last_notified_date')->nullable();
            $table->timestamps();
            $table->unsignedTinyInteger('alerts_enabled')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thresholds');
    }
};

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
        Schema::create('live_monitor_websites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 191);
            $table->longText('url')->nullable()->comment('Website Url');
            $table->boolean('enabled')->default(false)->comment('0-No,1-Yes');
            $table->boolean('status')->default(false)->comment('0-Unknown,1-OK');
            $table->timestamps();
            $table->string('load_time_benchmark', 191)->nullable();
            $table->text('department_id')->nullable();
            $table->text('location_id')->nullable();
            $table->tinyInteger('all_user_check')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_monitor_websites');
    }
};

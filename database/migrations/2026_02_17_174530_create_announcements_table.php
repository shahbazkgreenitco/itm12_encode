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
        Schema::create('announcements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('title');
            $table->text('announcement');
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->text('department_id')->nullable();
            $table->text('location_id')->nullable();
            $table->tinyInteger('status_id')->default(0);
            $table->timestamps();
            $table->tinyInteger('all_user_check')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};

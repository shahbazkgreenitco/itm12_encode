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
        Schema::create('scheduled_maintenance_plan', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('title')->nullable();
            $table->integer('device_id')->nullable()->comment('Id from Device');
            $table->date('start_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->string('start_time', 30)->nullable();
            $table->string('approximate_duration', 30)->nullable();
            $table->integer('incharge_id')->nullable()->comment('Id from users');
            $table->tinyInteger('recursive_plan')->nullable()->comment('1- daily 2-monthly 3-yearly');
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_maintenance_plan');
    }
};

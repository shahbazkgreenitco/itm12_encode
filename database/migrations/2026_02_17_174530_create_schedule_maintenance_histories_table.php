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
        Schema::create('schedule_maintenance_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('action_type');
            $table->integer('schedule_maintenance_id');
            $table->string('schedule_maintenance_name_changed', 191)->nullable();
            $table->text('title_changed')->nullable();
            $table->longText('description_changed')->nullable();
            $table->integer('recursion_plan_changed')->nullable()->comment('1-OneTime,2-Daily,3-Weekly,4-Monthly,5-Yearly');
            $table->text('recursion_plan_time_changed')->nullable();
            $table->time('duration_changed')->nullable();
            $table->integer('manager_id_changed')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_maintenance_histories');
    }
};

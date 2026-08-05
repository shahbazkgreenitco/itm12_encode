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
        Schema::create('schedule_maintenance_lists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('schedule_maintenance_name', 191)->nullable();
            $table->text('title')->nullable();
            $table->longText('description')->nullable();
            $table->integer('recursion_plan')->nullable()->comment('1-OneTime,2-Daily,3-Weekly,4-Monthly,5-Yearly');
            $table->text('recursion_plan_time')->nullable();
            $table->time('duration')->nullable();
            $table->integer('manager_id')->nullable();
            $table->string('cron_expression', 191)->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('approval_required')->default(false);
            $table->decimal('projected_cost', 9)->nullable()->default(0);
            $table->string('currency_format', 191)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_maintenance_lists');
    }
};

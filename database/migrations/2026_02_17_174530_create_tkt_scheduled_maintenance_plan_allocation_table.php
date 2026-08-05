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
        Schema::create('tkt_scheduled_maintenance_plan_allocation', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('assigned_by');
            $table->integer('category_id')->nullable();
            $table->integer('model_id')->nullable();
            $table->string('device_id')->nullable();
            $table->integer('plan_id');
            $table->date('planned_date')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('incharge_id')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->integer('priority')->nullable();
            $table->text('comments')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->tinyInteger('assigned_to')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_scheduled_maintenance_plan_allocation');
    }
};

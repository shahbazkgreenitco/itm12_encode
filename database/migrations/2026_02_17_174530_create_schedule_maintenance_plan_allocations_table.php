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
        Schema::create('schedule_maintenance_plan_allocations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('category_id')->nullable();
            $table->integer('model_id')->nullable();
            $table->integer('plan_id');
            $table->integer('supplier_id')->nullable();
            $table->integer('handler_id')->nullable();
            $table->text('device_id')->nullable();
            $table->longText('comment')->nullable();
            $table->longText('note')->nullable();
            $table->integer('status')->nullable()->comment('0-Disable,1-Enable,2-Pause');
            $table->integer('created_by');
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
        Schema::dropIfExists('schedule_maintenance_plan_allocations');
    }
};

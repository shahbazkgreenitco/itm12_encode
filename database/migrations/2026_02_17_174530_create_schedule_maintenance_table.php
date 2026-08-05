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
        Schema::create('schedule_maintenance', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('allocated_id');
            $table->integer('plan_id');
            $table->integer('category_id');
            $table->integer('device_id')->nullable();
            $table->integer('model_id')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->integer('handler_id')->nullable();
            $table->timestamp('schedule_date')->nullable();
            $table->integer('task_step')->nullable();
            $table->integer('status')->default(1)->comment('1-Scheduled,2-InProgress,3-Complete,4-Hold,5-Canceled,6-Preponed,7-Postponed,8-Delayed');
            $table->text('remark')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->longText('approver_comment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_maintenance');
    }
};

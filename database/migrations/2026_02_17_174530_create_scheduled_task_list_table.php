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
        Schema::create('scheduled_task_list', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('order_no')->nullable();
            $table->integer('device_id')->nullable()->comment('Id from Device');
            $table->string('task_title', 30)->nullable();
            $table->integer('incharge_id')->nullable()->comment('Id from Users');
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->text('description')->nullable();
            $table->integer('scheduled_id')->nullable()->comment('id value from scheduled_maintenance_plan table');
            $table->tinyInteger('task_status')->nullable()->default(2)->comment('1 - Completed , 2 - Not Completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_task_list');
    }
};

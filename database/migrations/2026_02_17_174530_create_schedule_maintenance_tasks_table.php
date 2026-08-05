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
        Schema::create('schedule_maintenance_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('task_id');
            $table->integer('plan_id');
            $table->boolean('status')->default(false);
            $table->integer('order_no');
            $table->string('latitude', 191)->nullable();
            $table->string('longitude', 191)->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->decimal('cost', 9)->default(0);
            $table->longText('comment')->nullable();
            $table->longText('location')->nullable();
            $table->tinyInteger('task_remark')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_maintenance_tasks');
    }
};

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
        Schema::create('tkt_scheduled_maintenance_task_list', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('plan_id');
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->text('task_title')->nullable();
            $table->integer('order_no');
            $table->char('proof_required', 1)->nullable();
            $table->char('downtime_required', 1)->nullable();
            $table->text('downtime_duration')->nullable();
            $table->char('gps_required', 1)->nullable();
            $table->char('is_mandetory', 1)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_scheduled_maintenance_task_list');
    }
};

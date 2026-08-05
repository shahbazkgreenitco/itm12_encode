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
        Schema::create('maintenance_task_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('action_type');
            $table->integer('maintenance_task_id');
            $table->text('title_changed')->nullable();
            $table->longText('description_changed')->nullable();
            $table->longText('note_changed')->nullable();
            $table->boolean('gps_required_changed')->nullable();
            $table->boolean('proof_required_changed')->nullable();
            $table->boolean('downtime_changed')->nullable();
            $table->boolean('mandatory_changed')->nullable();
            $table->time('downtime_duration_changed')->nullable();
            $table->integer('order_no_changed')->nullable();
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
        Schema::dropIfExists('maintenance_task_histories');
    }
};

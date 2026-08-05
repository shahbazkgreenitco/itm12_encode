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
        Schema::create('scheduled_maintenance_history', function (Blueprint $table) {
            $table->integer('id', true);
            $table->date('serviced_date')->nullable();
            $table->integer('incharge_id')->nullable()->comment('Id from users');
            $table->string('approximate_duration', 30)->nullable();
            $table->date('start_at')->nullable();
            $table->date('completed_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->integer('plan_no')->nullable()->comment('select id from scheduled_maintenance_plan table');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_maintenance_history');
    }
};

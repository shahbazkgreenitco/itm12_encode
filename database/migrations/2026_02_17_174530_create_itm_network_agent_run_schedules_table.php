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
        Schema::create('itm_network_agent_run_schedules', function (Blueprint $table) {
            $table->integer('id', true);
            $table->dateTime('schedule_at')->nullable();
            $table->integer('tot_schedules')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm_network_agent_run_schedules');
    }
};

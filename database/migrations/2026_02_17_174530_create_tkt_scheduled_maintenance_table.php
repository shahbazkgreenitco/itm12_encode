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
        Schema::create('tkt_scheduled_maintenance', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('plan_id');
            $table->integer('asset_id');
            $table->integer('maintenance_type')->default(0)->comment('0:Scheduled,1:Repair,2:Upgrade');
            $table->date('planned_date');
            $table->integer('progress')->nullable();
            $table->bigInteger('cost')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('task_id', 500)->nullable();
            $table->string('user_id', 500)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_scheduled_maintenance');
    }
};

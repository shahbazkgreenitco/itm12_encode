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
        Schema::create('tkt_scheduled_maintenance_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('task_id');
            $table->integer('maintenance_id');
            $table->string('gps', 191)->nullable();
            $table->string('proof', 191)->nullable();
            $table->string('downtime', 191)->nullable();
            $table->integer('status')->default(0)->comment('0->Incomplete,1->Complete');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_scheduled_maintenance_tasks');
    }
};

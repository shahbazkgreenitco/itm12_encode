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
        Schema::create('maintenance_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('title')->nullable();
            $table->longText('description')->nullable();
            $table->longText('notes')->nullable();
            $table->boolean('gps_required')->nullable()->default(false);
            $table->boolean('proof_required')->nullable()->default(false);
            $table->boolean('downtime')->nullable()->default(false);
            $table->boolean('mandatory')->nullable()->default(false);
            $table->time('downtime_duration')->nullable();
            $table->integer('order_no')->nullable();
            $table->integer('plan_id')->nullable();
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
        Schema::dropIfExists('maintenance_tasks');
    }
};

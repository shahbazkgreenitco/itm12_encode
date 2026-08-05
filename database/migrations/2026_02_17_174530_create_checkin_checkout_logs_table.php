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
        Schema::create('checkin_checkout_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('asset_id')->nullable();
            $table->integer('asset_logs_id')->nullable();
            $table->text('action_type')->nullable();
            $table->text('checkin_at')->nullable();
            $table->text('rate')->nullable();
            $table->decimal('rate_cost', 9)->nullable();
            $table->decimal('cost', 9)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkin_checkout_logs');
    }
};

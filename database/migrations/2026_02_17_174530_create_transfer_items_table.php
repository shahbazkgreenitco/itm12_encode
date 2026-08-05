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
        Schema::create('transfer_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('transfer_id');
            $table->integer('device_id');
            $table->integer('transfer_status')->nullable();
            $table->integer('canceled_by')->nullable();
            $table->dateTime('canceled_at')->nullable();
            $table->integer('received_by')->nullable();
            $table->dateTime('received_at')->nullable();
            $table->timestamps();
            $table->longText('notes')->nullable();
            $table->text('cc_users')->nullable();
            $table->integer('transfer_stock_place')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_items');
    }
};

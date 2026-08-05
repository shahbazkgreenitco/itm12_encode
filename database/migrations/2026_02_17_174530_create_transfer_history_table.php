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
        Schema::create('transfer_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('transfer_id')->nullable();
            $table->string('batch_code', 191)->nullable();
            $table->integer('transfer_from')->nullable();
            $table->integer('transfer_to')->nullable();
            $table->integer('internal_place')->nullable();
            $table->date('expected_received_date')->nullable();
            $table->integer('status')->nullable();
            $table->longText('notes')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('canceled_by')->nullable();
            $table->integer('received_by')->nullable();
            $table->dateTime('canceled_at')->nullable();
            $table->dateTime('received_at')->nullable();
            $table->text('cc_users')->nullable();
            $table->timestamps();
            $table->integer('responsible_user')->nullable();
            $table->integer('device_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_history');
    }
};

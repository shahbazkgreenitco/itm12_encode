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
        Schema::create('transfers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('batch_code', 191);
            $table->integer('transfer_from');
            $table->integer('transfer_to');
            $table->integer('created_by')->nullable();
            $table->integer('canceled_by')->nullable();
            $table->dateTime('canceled_at')->nullable();
            $table->date('expected_received_date')->nullable();
            $table->timestamps();
            $table->integer('status');
            $table->longText('notes')->nullable();
            $table->integer('received_by')->nullable();
            $table->integer('internal_place')->nullable();
            $table->text('cc_users')->nullable();
            $table->dateTime('received_at')->nullable();
            $table->integer('responsible_user')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};

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
        Schema::create('po_partial_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('request_id')->nullable();
            $table->decimal('payment_amount', 12)->nullable();
            $table->text('payment_reference')->nullable();
            $table->unsignedBigInteger('procure_quotations_id')->nullable();
            $table->timestamps();
            $table->string('partial_payment_mode', 191)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po_partial_payments');
    }
};

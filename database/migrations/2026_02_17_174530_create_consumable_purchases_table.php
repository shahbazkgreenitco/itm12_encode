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
        Schema::create('consumable_purchases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('batch_no', 191)->nullable();
            $table->string('po_no', 191)->nullable();
            $table->integer('qty')->nullable();
            $table->integer('purchase_by')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('exp_date')->nullable();
            $table->longText('attachment')->nullable();
            $table->string('currency', 191)->nullable();
            $table->decimal('purchase_price', 15, 4)->nullable();
            $table->timestamps();
            $table->string('invoice_no', 191)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumable_purchases');
    }
};

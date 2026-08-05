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
        Schema::create('purchase_tax_element', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('tax_defination_id')->nullable();
            $table->integer('tax_element_id')->nullable();
            $table->decimal('value', 12)->nullable();
            $table->decimal('amount', 12)->nullable();
            $table->integer('purchase_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_tax_element');
    }
};

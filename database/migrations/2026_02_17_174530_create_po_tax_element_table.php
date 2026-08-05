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
        Schema::create('po_tax_element', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('tax_defination_id')->nullable();
            $table->integer('tax_element_id')->nullable();
            $table->decimal('value', 12)->nullable();
            $table->decimal('amount', 12)->nullable();
            $table->integer('procure_quotations_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po_tax_element');
    }
};

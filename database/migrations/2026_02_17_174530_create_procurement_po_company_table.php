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
        Schema::create('procurement_po_company', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('company');
            $table->text('logo')->nullable();
            $table->text('address')->nullable();
            $table->bigInteger('po_company_id')->nullable();
            $table->timestamps();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('gstin')->nullable();
            $table->integer('country')->nullable();
            $table->longText('terms_conditions')->nullable();
            $table->longText('payment_terms')->nullable();
            $table->longText('notes')->nullable();
            $table->text('delivery_terms')->nullable();
            $table->text('warranty_and_support')->nullable();
            $table->string('contact_no', 50)->nullable();
            $table->string('cin_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procurement_po_company');
    }
};

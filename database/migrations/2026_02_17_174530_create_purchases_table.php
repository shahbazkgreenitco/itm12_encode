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
        Schema::create('purchases', function (Blueprint $table) {
            $table->increments('id');
            $table->date('invoice_date')->nullable();
            $table->date('received_date')->nullable();
            $table->string('invoice_no', 100)->nullable();
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('supplier_id')->nullable();
            $table->string('currency', 10)->nullable();
            $table->decimal('bill_amount', 10)->default(0);
            $table->text('notes')->nullable();
            $table->text('history')->nullable();
            $table->boolean('fully_received')->nullable()->default(true)->comment('1 - All goods received; 2 - Partial goods received');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->softDeletes();
            $table->string('other_info', 191)->nullable();
            $table->string('other_info1', 191)->nullable();
            $table->integer('location_id')->nullable();
            $table->string('po_number', 191)->nullable();
            $table->integer('qty')->nullable()->default(0);
            $table->boolean('include_taxes')->default(false);
            $table->bigInteger('tax_id')->nullable();
            $table->unsignedBigInteger('pr_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};

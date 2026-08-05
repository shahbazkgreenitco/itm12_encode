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
        Schema::create('procure_quotation_items', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('pr_id')->nullable();
            $table->integer('item_id')->nullable();
            $table->decimal('price_per_item', 12)->nullable();
            $table->integer('qid')->nullable()->comment('Supplier Quotation ID');
            $table->timestamps();
            $table->decimal('po_item_amount', 30)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procure_quotation_items');
    }
};

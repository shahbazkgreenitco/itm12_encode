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
        Schema::create('consumable_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('consumable_id');
            $table->string('name')->nullable();
            $table->integer('category_id')->nullable();
            $table->integer('location_id')->nullable();
            $table->integer('internal_place_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('qty')->default(0);
            $table->tinyInteger('requestable')->default(0);
            $table->date('purchase_date')->nullable();
            $table->string('currency', 10)->nullable();
            $table->decimal('purchase_cost', 13, 4)->nullable();
            $table->string('order_number')->nullable();
            $table->unsignedInteger('company_id')->nullable();
            $table->integer('manufacturer_id')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->integer('invoice_id')->nullable();
            $table->text('notes')->nullable();
            $table->text('consumable_custom_fields')->nullable();
            $table->bigInteger('department_id')->nullable();
            $table->string('image', 191)->nullable();
            $table->integer('units')->nullable();
            $table->integer('consumable_thresholds')->nullable();
            $table->tinyInteger('thresholds_alerts')->default(0);
            $table->tinyInteger('requestable_consumables')->default(0);
            $table->integer('reorder_limits')->default(0);
            $table->string('unique_tag', 191)->nullable();
            $table->integer('scrap_qty')->nullable();
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->string('change_type', 191)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumable_histories');
    }
};

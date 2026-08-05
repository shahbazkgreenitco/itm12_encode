<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('accessory_caches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('accessory_log_id')->index()->comment('asset_logs table id');
            $table->unsignedBigInteger('accessory_record_id')->index()->comment('accessory_records table id');
            $table->string('unique_tag')->nullable();
            $table->string('name')->nullable();
            $table->integer('category_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('qty')->default(0);
            $table->boolean('requestable')->default(false);
            $table->integer('location_id')->nullable();
            $table->integer('internal_place_id')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 13, 4)->nullable();
            $table->string('purchase_currency', 10)->nullable();
            $table->string('order_number')->nullable();
            $table->integer('company_id')->nullable();
            $table->integer('manufacturer_id')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->integer('invoice_id')->nullable();
            $table->text('notes')->nullable();
            $table->integer('scrap_qty')->default(0);
            $table->string('batch_no', 30)->nullable();
            $table->text('accessories_custom_fields')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('image')->nullable();
            $table->integer('accessory_thresholds')->nullable();
            $table->boolean('thresholds_alerts')->default(false);
            $table->boolean('requestable_accessory')->default(false);
            $table->integer('reorder_limits')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accessory_caches');
    }
};

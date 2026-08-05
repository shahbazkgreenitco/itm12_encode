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
        Schema::create('consumables', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->integer('category_id')->nullable();
            $table->integer('location_id')->nullable();
            $table->integer('internal_place_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('qty')->default(0);
            $table->boolean('requestable')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->date('purchase_date')->nullable();
            $table->string('currency', 10)->nullable();
            $table->decimal('purchase_cost', 13, 4)->nullable();
            $table->string('order_number')->nullable();
            $table->unsignedInteger('company_id')->nullable()->index('consumables_company_id_foreign');
            $table->integer('manufacturer_id')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->integer('invoice_id')->nullable()->comment('Record from purchases table');
            $table->text('notes')->nullable();
            $table->text('consumable_custom_fields')->nullable();
            $table->bigInteger('department_id')->nullable();
            $table->string('image', 191)->nullable();
            $table->integer('units')->nullable();
            $table->integer('consumable_thresholds')->nullable();
            $table->boolean('thresholds_alerts')->default(false);
            $table->boolean('requestable_consumables')->default(false);
            $table->integer('reorder_limits')->default(0);
            $table->string('unique_tag', 191)->nullable();
            $table->integer('scrap_qty')->nullable();
            $table->text('_itm_departmentdrop')->nullable();
            $table->text('_itm_user_predrop')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumables');
    }
};

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
        Schema::create('licenses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('serial')->nullable();
            $table->date('purchase_date')->nullable();
            $table->string('currency', 10)->nullable();
            $table->decimal('purchase_cost', 13, 4)->nullable();
            $table->string('order_number', 50)->nullable();
            $table->integer('seats')->default(1);
            $table->text('notes')->nullable();
            $table->integer('user_id');
            $table->integer('is_tracked')->default(0);
            $table->integer('depreciation_id')->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->string('license_name', 100)->nullable();
            $table->string('license_email', 120)->nullable();
            $table->boolean('depreciate')->nullable()->default(false);
            $table->integer('supplier_id')->nullable();
            $table->date('expiration_date')->nullable();
            $table->string('purchase_order')->nullable();
            $table->date('termination_date')->nullable();
            $table->boolean('maintained');
            $table->boolean('reassignable')->default(true);
            $table->unsignedInteger('company_id')->nullable()->index('licenses_company_id_foreign');
            $table->string('support', 500)->nullable();
            $table->integer('manufacturer_id')->nullable();
            $table->string('agreement_no')->nullable();
            $table->string('invoice_id', 191)->nullable()->comment('Record from purchases table');
            $table->integer('category_id')->nullable();
            $table->tinyInteger('added_via')->nullable()->default(1);
            $table->string('product_id')->nullable();
            $table->string('Version')->nullable();
            $table->integer('license_type')->nullable();
            $table->string('publisher_name', 191)->nullable();
            $table->text('licence_custom_fields')->nullable();
            $table->bigInteger('department_id')->nullable();
            $table->bigInteger('location_id')->nullable();
            $table->integer('internal_place_id')->nullable();
            $table->string('image', 191)->nullable();
            $table->text('_itm_licencecustome')->nullable();
            $table->string('_itm_licencenumber')->nullable();
            $table->boolean('requestable_license')->default(false);
            $table->string('unique_tag', 191)->nullable();
            $table->text('_itm_sbztest')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};

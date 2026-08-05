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
        Schema::create('components', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('company_id')->nullable();
            $table->string('name')->nullable();
            $table->string('serial')->nullable();
            $table->string('unique_tag', 100)->nullable();
            $table->integer('category_id')->nullable();
            $table->integer('manufacturer_id')->nullable();
            $table->integer('location_id')->nullable();
            $table->integer('internal_place_id')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->string('order_number', 155)->nullable();
            $table->integer('invoice_id')->nullable()->comment('Record from purchases table');
            $table->date('purchase_date')->nullable();
            $table->string('purchase_currency', 10)->nullable();
            $table->decimal('purchase_cost', 20)->nullable();
            $table->text('notes')->nullable();
            $table->integer('checked_out_for')->nullable()->comment('1 = User, 2 = Device');
            $table->integer('checked_out_to')->nullable();
            $table->dateTime('checked_out_at')->nullable();
            $table->date('expected_checkin_at')->nullable();
            $table->tinyInteger('origin_from')->nullable()->comment('1 - Purchase, 2 - Existing Device, 3 - Other');
            $table->integer('parent_device')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('creator_id')->nullable();
            $table->integer('updator_id')->nullable();
            $table->tinyInteger('status')->nullable()->default(1)->comment('0 - Repair, 1 - Usable, 2 - Lost');
            $table->dateTime('checked_in_at')->nullable();
            $table->integer('monitor_id')->nullable();
            $table->text('component_custom_fields')->nullable();
            $table->bigInteger('department_id')->nullable();
            $table->string('image', 191)->nullable();
            $table->boolean('requestable_component')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('components');
    }
};

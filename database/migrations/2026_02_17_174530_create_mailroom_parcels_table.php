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
        Schema::create('mailroom_parcels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('parcel_tag', 191)->nullable();
            $table->tinyInteger('flow')->nullable()->comment('1-Incoming, 2-Outgoing');
            $table->tinyInteger('document_type')->nullable()->comment('1-Document, 2-Nondocument');
            $table->string('courier_number', 191)->nullable();
            $table->tinyInteger('courier_type')->nullable()->comment('1-Personal, 2-Official');
            $table->tinyInteger('courier_mode')->nullable()->comment('1-Domestic, 2-Intransit,3-International');
            $table->unsignedBigInteger('courier_person_id')->nullable();
            $table->integer('weight')->nullable();
            $table->decimal('charges', 10)->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->unsignedBigInteger('site_id')->nullable();
            $table->tinyInteger('receive_type')->nullable()->comment('1-Government, 2-Non-Government');
            $table->text('mrr')->nullable();
            $table->text('tpn')->nullable();
            $table->tinyInteger('transaction_mode')->nullable()->comment('1-Credit Card, 2-Debit Card,3-Paypal,4-Bank Transfer,5-Cash,6-UPI');
            $table->text('transaction_id')->nullable();
            $table->decimal('length', 10)->nullable();
            $table->decimal('width', 10)->nullable();
            $table->decimal('height', 10)->nullable();
            $table->timestamps();
            $table->string('parcelbulkbatch_id', 191)->nullable();
            $table->decimal('volumetric_charge', 20)->nullable();
            $table->boolean('insurance_applied')->default(false);
            $table->decimal('parcel_value', 20)->nullable();
            $table->decimal('insurance_handling_charge', 20)->nullable();
            $table->decimal('insurance_charge', 20)->nullable();
            $table->decimal('fragile_liquid_charge', 20)->nullable();
            $table->decimal('base_charge', 20)->nullable();
            $table->decimal('sub_total_charge', 20)->nullable();
            $table->unsignedBigInteger('tax_defination_id')->nullable();
            $table->text('tax_element_value')->nullable();
            $table->decimal('tax_charges', 20)->nullable();
            $table->decimal('total_charges', 20)->nullable();
            $table->text('_itm_description')->nullable();
            $table->string('signature', 191)->nullable();
            $table->string('proof', 191)->nullable();
            $table->string('via_transport', 191)->nullable()->comment('Transport method: 1)By Air, 2)By Road, 3)By Rail');
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->text('_itm_name_mail')->nullable();
            $table->string('_itm_mailroom_num1')->nullable();
            $table->longText('remark')->nullable();
            $table->integer('pack_type')->nullable();
            $table->integer('piece_count')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mailroom_parcels');
    }
};

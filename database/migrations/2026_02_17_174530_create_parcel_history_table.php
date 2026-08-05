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
        Schema::create('parcel_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('parcel_id')->nullable();
            $table->string('parcel_tag', 191)->nullable();
            $table->tinyInteger('new_flow')->nullable()->comment('1-Incoming, 2-Outgoing');
            $table->tinyInteger('old_flow')->nullable()->comment('1-Incoming, 2-Outgoing');
            $table->tinyInteger('new_document_type')->nullable()->comment('1-Document, 2-Nondocument');
            $table->tinyInteger('old_document_type')->nullable()->comment('1-Document, 2-Nondocument');
            $table->string('new_courier_number', 191)->nullable();
            $table->string('old_courier_number', 191)->nullable();
            $table->tinyInteger('new_courier_type')->nullable()->comment('1-Personal, 2-Official');
            $table->tinyInteger('old_courier_type')->nullable()->comment('1-Personal, 2-Official');
            $table->tinyInteger('new_courier_mode')->nullable()->comment('1-Domestic, 2-Intransit,3-International');
            $table->tinyInteger('old_courier_mode')->nullable()->comment('1-Domestic, 2-Intransit,3-International');
            $table->unsignedBigInteger('new_courier_person_id')->nullable();
            $table->unsignedBigInteger('old_courier_person_id')->nullable();
            $table->decimal('new_weight', 10)->nullable();
            $table->decimal('old_weight', 10)->nullable();
            $table->decimal('new_charges', 10)->nullable();
            $table->decimal('old_charges', 10)->nullable();
            $table->unsignedBigInteger('new_status_id')->nullable();
            $table->unsignedBigInteger('old_status_id')->nullable();
            $table->unsignedBigInteger('new_site_id')->nullable();
            $table->unsignedBigInteger('old_site_id')->nullable();
            $table->tinyInteger('new_receive_type')->nullable()->comment('1-Government, 2-Non-Government');
            $table->tinyInteger('old_receive_type')->nullable()->comment('1-Government, 2-Non-Government');
            $table->text('new_mrr')->nullable();
            $table->text('old_mrr')->nullable();
            $table->text('new_tpn')->nullable();
            $table->text('old_tpn')->nullable();
            $table->tinyInteger('new_transaction_mode')->nullable()->comment('1-Credit Card, 2-Debit Card,3-Paypal,4-Bank Transfer,5-Cash,6-UPI');
            $table->tinyInteger('old_transaction_mode')->nullable()->comment('1-Credit Card, 2-Debit Card,3-Paypal,4-Bank Transfer,5-Cash,6-UPI');
            $table->text('new_transaction_id')->nullable();
            $table->text('old_transaction_id')->nullable();
            $table->decimal('new_length', 10)->nullable();
            $table->decimal('old_length', 10)->nullable();
            $table->decimal('new_width', 10)->nullable();
            $table->decimal('old_width', 10)->nullable();
            $table->decimal('new_height', 10)->nullable();
            $table->decimal('old_height', 10)->nullable();
            $table->string('new_sender_name', 191)->nullable();
            $table->string('old_sender_name', 191)->nullable();
            $table->string('new_sender_phone', 191)->nullable();
            $table->string('old_sender_phone', 191)->nullable();
            $table->string('new_sender_email', 191)->nullable();
            $table->string('old_sender_email', 191)->nullable();
            $table->text('new_sender_address')->nullable();
            $table->text('old_sender_address')->nullable();
            $table->text('new_sender_zipcode')->nullable();
            $table->text('old_sender_zipcode')->nullable();
            $table->string('new_receiver_name', 191)->nullable();
            $table->string('old_receiver_name', 191)->nullable();
            $table->string('new_receiver_phone', 191)->nullable();
            $table->string('old_receiver_phone', 191)->nullable();
            $table->string('new_receiver_email', 191)->nullable();
            $table->string('old_receiver_email', 191)->nullable();
            $table->text('new_receiver_address')->nullable();
            $table->text('old_receiver_address')->nullable();
            $table->text('new_receiver_zipcode')->nullable();
            $table->text('old_receiver_zipcode')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->integer('action_type')->nullable()->comment('1-Create, 2-Update, 3-Delete');
            $table->timestamps();
            $table->text('_itm_old_description')->nullable();
            $table->text('_itm_new_description')->nullable();
            $table->string('signature', 191)->nullable();
            $table->string('proof', 191)->nullable();
            $table->string('old_signature', 191)->nullable();
            $table->string('old_proof', 191)->nullable();
            $table->string('old_via_transport', 191)->nullable();
            $table->string('new_via_transport', 191)->nullable();
            $table->text('_itm_old_name_mail')->nullable();
            $table->text('_itm_new_name_mail')->nullable();
            $table->string('_itm_old_mailroom_num1')->nullable();
            $table->string('_itm_new_mailroom_num1')->nullable();
            $table->longText('old_remark')->nullable();
            $table->longText('new_remark')->nullable();
            $table->integer('old_pack_type')->nullable();
            $table->integer('new_pack_type')->nullable();
            $table->integer('old_piece_count')->nullable();
            $table->integer('new_piece_count')->nullable();
            $table->string('old_sender_city', 191)->nullable();
            $table->string('new_sender_city', 191)->nullable();
            $table->string('new_batch_id', 191)->nullable();
            $table->string('old_batch_id', 191)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcel_history');
    }
};

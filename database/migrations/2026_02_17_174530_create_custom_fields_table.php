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
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('format');
            $table->string('element');
            $table->timestamps();
            $table->integer('user_id')->nullable();
            $table->bigInteger('option_type')->nullable();
            $table->bigInteger('preDefinedOptions')->nullable();
            $table->text('custom_options')->nullable();
            $table->bigInteger('reference_id')->nullable()->default(0);
            $table->integer('custom_field_types')->nullable()->default(1)->comment('1=Device, 2=Tickets, 3=SEZ, 4=Component, 5=Consumable, 6=Accessory, 7=License');
            $table->longText('help_note')->nullable()->comment('info about custome field');
            $table->longText('custom_label')->nullable()->comment('Radio/checkbox about custome lable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_fields');
    }
};

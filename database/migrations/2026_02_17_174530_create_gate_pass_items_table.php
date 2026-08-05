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
        Schema::create('gate_pass_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('gate_pass_id');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->integer('item_type')->nullable()->comment('1 = Asset, 2 = Accessories, 3 = Component, 4 = Consumable');
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gate_pass_items');
    }
};

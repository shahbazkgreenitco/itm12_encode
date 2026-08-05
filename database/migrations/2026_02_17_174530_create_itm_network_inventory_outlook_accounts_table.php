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
        Schema::create('itm_network_inventory_outlook_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('Name', 191)->nullable();
            $table->bigInteger('Size')->nullable();
            $table->timestamps();
            $table->integer('basic_id')->nullable()->index('itm_network_inventory_outlook_accounts_basic_id_foreign');
            $table->string('Path', 191)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm_network_inventory_outlook_accounts');
    }
};

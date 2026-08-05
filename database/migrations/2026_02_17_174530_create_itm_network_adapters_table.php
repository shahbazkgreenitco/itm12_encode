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
        Schema::create('itm_network_adapters', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('basic_id');
            $table->string('Name', 100)->nullable();
            $table->string('Description')->nullable();
            $table->string('PhysicalAddress', 100)->nullable();
            $table->string('OperationalStatus', 30)->nullable();
            $table->string('NetworkInterfaceType', 30)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm_network_adapters');
    }
};

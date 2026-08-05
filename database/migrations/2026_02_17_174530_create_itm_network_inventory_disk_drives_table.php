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
        Schema::create('itm_network_inventory_disk_drives', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('basic_id')->nullable()->index('itm_network_inventory_disk_drives_basic_id_foreign')->comment('Reference for basic table');
            $table->string('Caption', 100)->nullable();
            $table->string('InterfaceType', 100)->nullable();
            $table->string('Manufacturer', 100)->nullable();
            $table->string('Partitions', 100)->nullable();
            $table->string('SerialNumber', 100)->nullable();
            $table->float('Size', 30)->nullable();
            $table->timestamps();
            $table->string('Unit', 11)->nullable()->comment('To add the size unit value');
            $table->string('MediaType', 100)->nullable();

            $table->index(['id', 'basic_id', 'Caption', 'InterfaceType', 'Manufacturer', 'Partitions', 'SerialNumber', 'Size', 'Unit', 'MediaType'], 'idx_itm_network_inventory_disk_drives');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm_network_inventory_disk_drives');
    }
};

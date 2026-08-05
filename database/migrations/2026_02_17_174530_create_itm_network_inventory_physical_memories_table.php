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
        Schema::create('itm_network_inventory_physical_memories', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('basic_id')->nullable()->index('itm_network_inventory_physical_memories_basic_id_foreign')->comment('Reference for basic table');
            $table->string('MemoryType', 30)->nullable()->comment('About DDR1, DDR2, DDR3');
            $table->string('SerialNumber', 100)->nullable();
            $table->string('DeviceLocator', 30)->nullable();
            $table->float('Capacity', 30)->nullable();
            $table->string('Speed', 30)->nullable()->comment('Frequency');
            $table->string('PartNumber', 30)->nullable();
            $table->string('Caption', 100)->nullable();
            $table->string('DataWidth', 20)->nullable();
            $table->string('Manufacturer', 100)->nullable();
            $table->string('Model', 100)->nullable();
            $table->string('Tag', 50)->nullable();
            $table->timestamps();

            $table->index(['id', 'basic_id', 'MemoryType', 'SerialNumber', 'DeviceLocator', 'Capacity', 'Speed', 'PartNumber', 'Caption', 'DataWidth', 'Manufacturer', 'Model', 'Tag'], 'idx_itm_network_inventory_physical_memories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm_network_inventory_physical_memories');
    }
};

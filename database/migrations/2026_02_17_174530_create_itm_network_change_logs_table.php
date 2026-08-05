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
        Schema::create('itm_network_change_logs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('basic_id')->nullable()->index('itm_network_change_logs_basic_id_foreign')->comment('Reference for basic table');
            $table->tinyInteger('cl_item')->nullable()->comment('1 - Disk Drive, 2 - Internal Memory, 3 - OS');
            $table->tinyInteger('cl_type')->nullable()->comment('1 - Newly Added, 2 - Change Detected, 3 - Missing');
            $table->string('Caption')->nullable();
            $table->string('InterfaceType', 100)->nullable();
            $table->string('Manufacturer', 100)->nullable();
            $table->string('Partitions', 100)->nullable();
            $table->string('SerialNumber', 100)->nullable();
            $table->float('Size', 30)->nullable();
            $table->string('MemoryType', 30)->nullable()->comment('About DDR1, DDR2, DDR3');
            $table->string('DeviceLocator', 30)->nullable();
            $table->string('Capacity', 30)->nullable();
            $table->string('Speed', 30)->nullable()->comment('Frequency');
            $table->string('PartNumber', 30)->nullable();
            $table->string('DataWidth', 20)->nullable();
            $table->string('Model', 100)->nullable();
            $table->string('Tag', 50)->nullable();
            $table->timestamps();
            $table->mediumText('notified')->nullable();
            $table->string('Unit', 11)->nullable()->comment('To add the size unit value');
            $table->string('IdentifyingNumber')->nullable();
            $table->string('Vendor')->nullable();
            $table->string('Version')->nullable();
            $table->string('Publisher')->nullable();
            $table->dateTime('InstalledDate')->nullable();
            $table->float('EstimatedSize', 20)->nullable();
            $table->unsignedInteger('UsageCount')->nullable()->default(0)->comment('No of times programs get used');
            $table->string('UsageHrs', 11)->nullable()->default('0')->comment('No of hrs this programs utilized');
            $table->string('MediaType', 100)->nullable();
            $table->tinyInteger('isRegistry')->nullable()->comment('0 = Control Panel, 1 = Registry');

            $table->index(['id', 'basic_id', 'cl_item', 'cl_type', 'InterfaceType', 'Manufacturer', 'Partitions', 'SerialNumber', 'Size', 'MemoryType', 'DeviceLocator', 'Capacity', 'Speed', 'PartNumber', 'DataWidth', 'Tag', 'Unit', 'EstimatedSize', 'UsageCount', 'UsageHrs', 'MediaType'], 'idx_itm_network_change_logs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm_network_change_logs');
    }
};

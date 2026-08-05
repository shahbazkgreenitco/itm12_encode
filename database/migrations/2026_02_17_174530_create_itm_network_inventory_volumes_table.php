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
        Schema::create('itm_network_inventory_volumes', function (Blueprint $table) {
            $table->integer('id', true)->index('id_itm_ni_volumes_idx');
            $table->integer('basic_id')->nullable()->index('basic_id_itm_ni_volumes_idx')->comment('Reference for basic table');
            $table->string('Name')->nullable()->index('name_itm_ni_volumes_idx');
            $table->string('DriveType')->nullable()->index('drivetype_itm_ni_volumes_idx');
            $table->string('AvailableFreeSpace')->nullable()->index('availablefreespace_itm_ni_volumes_idx');
            $table->string('VolumeLabel')->nullable()->index('volumelabel_itm_ni_volumes_idx');
            $table->string('DriveFormat')->nullable()->index('driveformat_itm_ni_volumes_idx');
            $table->string('TotalSize')->nullable()->index('totalsize_itm_ni_volumes_idx');
            $table->timestamps();
            $table->integer('BitLockerProtectionStatus')->nullable()->default(2)->index('bitlockerprotectionstatus_itm_ni_volumes_idx')->comment('0=OFF, 1=ON, 2=UNKNOWN');

            // $table->primary(['id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm_network_inventory_volumes');
    }
};

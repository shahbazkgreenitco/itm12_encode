<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * WHY STILL FAILING?
     * Even varchar(100) columns without a prefix consume 100 × 4 = 400 bytes
     * each in utf8mb4. With 23 varchar columns that's 9200+ bytes — way over
     * the 3072 byte limit. Solution: add prefix(20) to EVERY varchar column.
     *
     * Byte calculation with prefix(20) on all varchar:
     *   23 varchar cols × 20 × 4 bytes  = 1840 bytes
     *   numeric/enum cols               =  ~50 bytes
     *   TOTAL                           = ~1890 bytes ✅ under 3072
     */
    public function up(): void
    {
        Schema::create('itm_network_inventory_basic', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('BIOSSerialNumber', 100)->nullable();
            $table->string('ComputerName')->nullable();
            $table->string('ComputerManufacturer', 100)->nullable();
            $table->string('ComputerModel', 100)->nullable();
            $table->string('ComputerDomain', 100)->nullable();
            $table->string('ComputerWorkgroup', 100)->nullable();
            $table->string('ComputerSystemType', 100)->nullable();
            $table->string('OSCaption')->nullable();
            $table->string('OSManufacturer', 100)->nullable();
            $table->string('OSSerialNumber')->nullable();
            $table->string('OSKey')->nullable();
            $table->string('OSVersion', 100)->nullable();
            $table->string('OSServicePack', 100)->nullable();
            $table->string('OSArchitecture', 100)->nullable();
            $table->string('OSSystemDrive', 100)->nullable();
            $table->string('OSSystemDirectory', 100)->nullable();
            $table->string('ProcessorName', 100)->nullable();
            $table->string('ProcessorManufacturer', 100)->nullable();
            $table->string('ProcessorArchitecture', 100)->nullable();
            $table->string('ProcessorFamily', 100)->nullable();
            $table->string('ProcessorProcessorId', 100)->nullable();
            $table->string('ProcessorNumberOfCores', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('IPv4', 30)->nullable();
            $table->string('ActiveMACAddress', 60)->nullable();
            $table->string('BIOSSMBIOSBIOSVersion', 100)->nullable();
            $table->string('BIOSManufacturer', 100)->nullable();
            $table->string('BIOSReleaseDate', 100)->nullable();
            $table->string('ProcessorThreadCount', 10)->nullable();
            $table->string('ProcessorMaxClockSpeed', 20)->nullable();
            $table->float('HddSize', 30)->nullable();
            $table->float('RamSize', 30)->nullable();
            $table->enum('platform', ['Windows', 'Linux/Unix', 'iOS'])->nullable();
            $table->integer('company')->nullable()->comment('Company ID');
            $table->string('oui')->nullable();
            $table->string('description')->nullable();
            $table->string('productclass')->nullable();
            $table->tinyInteger('is_dupe')->nullable()->comment('1 - Dupe, null - not dupe');
            $table->tinyInteger('is_virtual')->nullable()->comment('1 - Virtual, null - Physical');
            $table->unsignedInteger('device_id')->nullable()->index('itm_network_inventory_basic_device_id_foreign');
            $table->mediumText('SoftwareLicensingProduct')->nullable();
            $table->tinyInteger('is_AD')->nullable()->comment('0 - Non AD Agent, 1 - AD Agent');
            $table->string('agentVersion', 191)->nullable();

        }); // end Schema::create


        /*
         * ================================================================
         * idx_itm_network_inventory_basic
         *
         * KEY LESSON: In utf8mb4, every char = 4 bytes.
         * varchar(100) with NO prefix = 100 × 4 = 400 bytes per column!
         * With 23 varchar columns that = 9,200 bytes — way over 3072 limit.
         *
         * FIX: Add prefix(20) to ALL varchar columns in this index.
         * 23 varchar × 20 chars × 4 bytes = 1,840 bytes ✅ safe!
         *
         * Column                  Type           Prefix
         * ──────────────────────  ─────────────  ──────
         * id                      int            none   (4 bytes)
         * BIOSSerialNumber        varchar(100)   (20) ✅
         * ComputerName            varchar(255)   (20) ✅
         * ComputerManufacturer    varchar(100)   (20) ✅
         * ComputerModel           varchar(100)   (20) ✅
         * ComputerDomain          varchar(100)   (20) ✅
         * ComputerWorkgroup       varchar(100)   (20) ✅
         * ComputerSystemType      varchar(100)   (20) ✅
         * OSCaption               varchar(255)   (20) ✅
         * OSManufacturer          varchar(100)   (20) ✅
         * OSSerialNumber          varchar(255)   (20) ✅
         * OSKey                   varchar(255)   (20) ✅
         * OSVersion               varchar(100)   (20) ✅
         * OSServicePack           varchar(100)   (20) ✅
         * OSArchitecture          varchar(100)   (20) ✅
         * OSSystemDrive           varchar(100)   (20) ✅
         * OSSystemDirectory       varchar(100)   (20) ✅
         * ProcessorName           varchar(100)   (20) ✅
         * ProcessorManufacturer   varchar(100)   (20) ✅
         * IPv4                    varchar(30)    (20) ✅
         * ActiveMACAddress        varchar(60)    (20) ✅
         * BIOSSMBIOSBIOSVersion   varchar(100)   (20) ✅
         * BIOSManufacturer        varchar(100)   (20) ✅
         * HddSize                 float          none  ❌ no prefix on float
         * RamSize                 float          none  ❌ no prefix on float
         * platform                enum           none  ❌ no prefix on enum
         * company                 int            none  (4 bytes)
         * is_dupe                 tinyint        none  (1 byte)
         * is_virtual              tinyint        none  (1 byte)
         * device_id               int unsigned   none  (4 bytes)
         *
         * Total ≈ 1890 bytes ✅ well under 3072 limit
         * ================================================================
         */
        DB::statement('
            ALTER TABLE `itm_network_inventory_basic` ADD INDEX `idx_itm_network_inventory_basic` (
                `id`,
                `BIOSSerialNumber`(20),
                `ComputerName`(20),
                `ComputerManufacturer`(20),
                `ComputerModel`(20),
                `ComputerDomain`(20),
                `ComputerWorkgroup`(20),
                `ComputerSystemType`(20),
                `OSCaption`(20),
                `OSManufacturer`(20),
                `OSSerialNumber`(20),
                `OSKey`(20),
                `OSVersion`(20),
                `OSServicePack`(20),
                `OSArchitecture`(20),
                `OSSystemDrive`(20),
                `OSSystemDirectory`(20),
                `ProcessorName`(20),
                `ProcessorManufacturer`(20),
                `IPv4`(20),
                `ActiveMACAddress`(20),
                `BIOSSMBIOSBIOSVersion`(20),
                `BIOSManufacturer`(20),
                `HddSize`,
                `RamSize`,
                `platform`,
                `company`,
                `is_dupe`,
                `is_virtual`,
                `device_id`
            )
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm_network_inventory_basic');
    }
};

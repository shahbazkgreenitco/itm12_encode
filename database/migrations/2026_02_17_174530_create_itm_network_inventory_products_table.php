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
        Schema::create('itm_network_inventory_products', function (Blueprint $table) {
            $table->integer('id', true)->index('id');
            $table->integer('basic_id')->nullable()->index('basic_id')->comment('Reference for basic table');
            $table->string('IdentifyingNumber')->nullable()->index('identifyingnumber');
            $table->string('Caption')->nullable()->index('caption');
            $table->string('Vendor')->nullable()->index('vendor');
            $table->string('Version')->nullable()->index('version');
            $table->timestamps();
            $table->dateTime('InstalledDate')->nullable();
            $table->string('Publisher')->nullable()->index('publisher');
            $table->float('EstimatedSize', 20)->nullable()->index('estimatedsize')->comment('Installed Program\'s Size');
            $table->unsignedInteger('UsageCount')->nullable()->default(0)->index('usagecount')->comment('No of times programs get used');
            $table->string('UsageHrs', 11)->nullable()->default('0')->index('usagehrs')->comment('No of hrs this programs utilized');
            $table->tinyInteger('isRegistry')->nullable()->comment('0 = Control Panel, 1 = Registry');

            // $table->primary(['id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm_network_inventory_products');
    }
};

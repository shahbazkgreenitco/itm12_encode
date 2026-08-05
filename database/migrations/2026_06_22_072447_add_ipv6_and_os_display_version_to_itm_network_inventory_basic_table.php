<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('itm_network_inventory_basic', function (Blueprint $table) {
            $table
                ->string('OSDisplayVersion', 100)
                ->nullable()
                ->after('OSVersion');

            $table
                ->string('IPv6', 191)
                ->nullable()
                ->after('agentVersion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('itm_network_inventory_basic', function (Blueprint $table) {
            $table->dropColumn(['OSDisplayVersion', 'IPv6']);
        });
    }
};

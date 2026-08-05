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
        if (! Schema::hasColumn('itm_network_inventory_basic', 'IPv6')) {
            Schema::table('itm_network_inventory_basic', function (Blueprint $table) {
                $table->string('IPv6', 30)->nullable()->after('IPv4');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ( Schema::hasColumn('itm_network_inventory_basic', 'IPv6')) {
            Schema::table('itm_network_inventory_basic', function (Blueprint $table) {
                $table->dropColumn('IPv6');
            });
        }
    }
};

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
        if(!Schema::hasColumn('asset_logs', 'reason_id')) {
            Schema::table('asset_logs', function (Blueprint $table) {
                $table->unsignedBigInteger('reason_id')->nullable()->after('action_type')->comment('linked to asset_inout_reason.');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if(Schema::hasColumn('asset_logs', 'reason_id')) {
            Schema::table('asset_logs', function (Blueprint $table) {
                $table->dropColumn('reason_id');
            });
        }
    }
};

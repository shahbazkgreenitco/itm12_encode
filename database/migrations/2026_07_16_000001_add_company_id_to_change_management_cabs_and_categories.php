<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cm_cabs', function (Blueprint $table) {
            if (!Schema::hasColumn('cm_cabs', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id')->index();
            }
        });

        Schema::table('cm_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('cm_categories', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id')->index();
            }
        });

        DB::table('cm_cabs')->whereNull('company_id')->update(['company_id' => 1]);
        DB::table('cm_categories')->whereNull('company_id')->update(['company_id' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cm_categories', function (Blueprint $table) {
            if (Schema::hasColumn('cm_categories', 'company_id')) {
                $table->dropIndex(['company_id']);
                $table->dropColumn('company_id');
            }
        });

        Schema::table('cm_cabs', function (Blueprint $table) {
            if (Schema::hasColumn('cm_cabs', 'company_id')) {
                $table->dropIndex(['company_id']);
                $table->dropColumn('company_id');
            }
        });
    }
};

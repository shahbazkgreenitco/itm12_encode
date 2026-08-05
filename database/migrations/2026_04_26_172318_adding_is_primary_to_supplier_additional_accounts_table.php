<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('supplier_additional_accounts', 'is_primary')) {
            Schema::table('supplier_additional_accounts', function (Blueprint $table) {
                $table->boolean('is_primary')->after('supplier_id')->default(false);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('supplier_additional_accounts', 'is_primary')) {
            Schema::table('supplier_additional_accounts', function (Blueprint $table) {
                $table->dropColumn('is_primary');
            });
        }
    }
};

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
        if (!Schema::hasColumn('supplier_attachments', 'account_id')) {
            Schema::table('supplier_attachments', function (Blueprint $table) {
                $table->integer('account_id')->after('suplier_id')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('supplier_attachments', 'account_id')) {
            Schema::table('supplier_attachments', function (Blueprint $table) {
                $table->dropColumn('account_id');
            });
        }
    }
};

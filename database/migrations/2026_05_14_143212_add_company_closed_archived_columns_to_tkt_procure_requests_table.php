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
        Schema::table('tkt_procure_requests', function (Blueprint $table) {
            $table->integer('company_id')->default(1)->after('original_ticket_reference');
            $table->timestamp('archived_at')->nullable()->default(null)->after('updated_at');
            $table->timestamp('closed_at')->nullable()->default(null)->after('archived_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tkt_procure_requests', function (Blueprint $table) {
            $table->dropColumn(['company_id','closed_at','archived_at']);
        });
    }
};

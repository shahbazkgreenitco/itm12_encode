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
        Schema::table('tkt_incident', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable();
        });
        DB::table('tkt_incident')->whereNull('company_id')->update(['company_id' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tkt_incident', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
    }
};

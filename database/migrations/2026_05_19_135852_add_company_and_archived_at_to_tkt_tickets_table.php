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
        Schema::table('tkt_tickets', function (Blueprint $table) {
              $table->unsignedBigInteger('company_id')
                ->nullable()
                ->after('id');

            $table->timestamp('archived_at')
                ->nullable()
                ->after('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tkt_tickets', function (Blueprint $table) {
             $table->dropColumn([
                'company_id',
                'archived_at'
            ]);
        });
    }
};

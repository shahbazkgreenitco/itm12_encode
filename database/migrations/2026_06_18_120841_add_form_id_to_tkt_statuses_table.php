<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up(): void
    {
        Schema::table('tkt_statuses', function (Blueprint $table) {
            $table->unsignedBigInteger('form_id')
                  ->nullable()
                  ->after('status_form');
        });
    }

    public function down(): void
    {
        Schema::table('tkt_statuses', function (Blueprint $table) {

            $table->dropColumn('form_id');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  
    public function up()
    {
        Schema::table('mail_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')
                ->default(1)
                ->after('mail_username');
        });
    }

    public function down()
    {
        Schema::table('mail_settings', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
    }
};

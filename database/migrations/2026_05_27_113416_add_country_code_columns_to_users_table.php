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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_country_id', 10)->nullable()->after('phone');
            $table->string('phone2_country_id', 10)->nullable()->after('phone2');
            $table->string('work_phone_country_id', 10)->nullable()->after('work_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone_country_id', 'phone2_country_id', 'work_phone_country_id']);
        });
    }
};

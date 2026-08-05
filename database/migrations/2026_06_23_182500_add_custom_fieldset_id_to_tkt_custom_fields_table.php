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
        Schema::table('tkt_custom_fields', function (Blueprint $table) {
            $table->string('department_custom_fieldset_id', 191)->nullable()->after('ticket_id');
            $table->string('pc_custom_fieldset_id', 191)->nullable()->after('department_custom_fieldset_id');
            $table->string('sc_custom_fieldset_id', 191)->nullable()->after('pc_custom_fieldset_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tkt_custom_fields', function (Blueprint $table) {
            $table->dropColumn([
                'department_custom_fieldset_id',
                'pc_custom_fieldset_id',
                'sc_custom_fieldset_id',
            ]);
        });
    }
};

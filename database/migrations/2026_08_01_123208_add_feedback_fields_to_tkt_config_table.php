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
        Schema::table('tkt_config', function (Blueprint $table) {
            $table->boolean('feedback_required')
                ->nullable()
                ->after('mark_technician_as_cc_in_ticket_create');

            $table->unsignedTinyInteger('feedback_max_rating')
                ->nullable()
                ->after('feedback_required')
                ->comment('Maximum feedback rating (1-5)');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tkt_config', function (Blueprint $table) {
            $table->dropColumn([
                'feedback_required',
                'feedback_max_rating',
            ]);
        });
    }
};

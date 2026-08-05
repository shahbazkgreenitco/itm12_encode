<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * FIX: $table->comment() does not exist in Laravel 12.
     * Use DB::statement() to add table comment after creation.
     */
    public function up(): void
    {
        Schema::create('itm_agent_error_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('agent_version', 15)->nullable();
            $table->dateTime('incident_at')->nullable();
            $table->text('error_msg')->nullable();
            $table->string('file_path')->nullable();
            $table->string('error_code', 10)->nullable()->comment('Error Code');
            $table->string('ip', 50)->nullable()->comment('IP Address');
            $table->string('mac', 50)->nullable()->comment('MAC Address');
            $table->string('name')->nullable()->comment('System Name');
            $table->text('errors')->nullable()->comment('All errors');
        });

        // Table-level comment must be set via raw SQL in Laravel 12
        DB::statement("ALTER TABLE `itm_agent_error_logs` COMMENT = 'To capture the failures of agents'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm_agent_error_logs');
    }
};

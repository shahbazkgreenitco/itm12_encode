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
        Schema::create('itm_network_agent_data', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('company_id')->nullable();
            $table->string('ipv4', 80)->nullable();
            $table->string('BIOSSerialNumber', 100)->nullable();
            $table->string('ActiveMACAddress', 100)->nullable();
            $table->string('ComputerName')->nullable();
            $table->string('file_path')->nullable();
            $table->tinyInteger('is_processed')->nullable()->default(0);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->enum('protocol', ['ssh', 'ad', 'snmp', 'telnet'])->nullable();
            $table->string('agentVersion', 191)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm_network_agent_data');
    }
};

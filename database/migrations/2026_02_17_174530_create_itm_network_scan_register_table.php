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
        Schema::create('itm_network_scan_register', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('company_id')->nullable();
            $table->string('mac', 80)->nullable();
            $table->string('ipv4', 80)->nullable();
            $table->string('ipv6', 80)->nullable();
            $table->string('hostname')->nullable();
            $table->string('os')->nullable();
            $table->dateTime('manager_look_at')->nullable();
            $table->dateTime('last_active_at')->nullable();
            $table->dateTime('agent_look_at')->nullable();
            $table->string('agent_version', 10)->nullable();
            $table->timestamps();
            $table->tinyInteger('platform')->nullable()->comment('1-Windows,2-Linux,3-Others');
            $table->string('ports')->nullable()->comment('Store the open ports');
            $table->text('manufacturer')->nullable()->comment('manufacturar');
            $table->text('device_type')->nullable()->comment('device type');
            $table->mediumText('others')->nullable()->comment('others type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm_network_scan_register');
    }
};

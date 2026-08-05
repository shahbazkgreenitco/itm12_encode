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
        Schema::create('itm_other_network_items', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('sysName')->nullable();
            $table->string('sysContact')->nullable();
            $table->string('sysObjectID')->nullable();
            $table->string('sysLocation', 555)->nullable();
            $table->string('sysDescr', 555)->nullable();
            $table->string('ipv4', 50)->nullable();
            $table->integer('company_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm_other_network_items');
    }
};

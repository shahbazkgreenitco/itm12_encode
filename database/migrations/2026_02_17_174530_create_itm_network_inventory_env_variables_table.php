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
        Schema::create('itm_network_inventory_env_variables', function (Blueprint $table) {
            $table->increments('id');
            $table->string('Name', 191)->nullable();
            $table->mediumText('Value')->nullable();
            $table->timestamps();
            $table->integer('basic_id')->nullable()->index('itm_network_inventory_env_variables_basic_id_foreign');

            $table->index(['id', 'basic_id', 'Name'], 'idx_itm_network_inventory_env_variables');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm_network_inventory_env_variables');
    }
};

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
        Schema::create('itm_network_inventory_blocked_websites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('website_url')->nullable()->comment('Blocked Website Url');
            $table->boolean('is_blocked')->default(true)->comment('0-unblocked,1-blocked');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm_network_inventory_blocked_websites');
    }
};

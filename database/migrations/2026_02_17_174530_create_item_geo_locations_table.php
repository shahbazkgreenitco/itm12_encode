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
        Schema::create('item_geo_locations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('app_platform_id')->nullable()->comment('Id from app_platforms table');
            $table->float('lat', 11)->nullable()->default(0);
            $table->float('lng', 11)->nullable()->default(0);
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_geo_locations');
    }
};

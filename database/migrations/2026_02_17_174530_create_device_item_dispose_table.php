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
        Schema::create('device_item_dispose', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('device_dispose_id')->nullable();
            $table->unsignedBigInteger('asset_id')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->unsignedBigInteger('item_user_id')->nullable();
            $table->string('item_name', 191)->nullable();
            $table->string('asset_type', 191);
            $table->boolean('is_dispose')->default(true)->comment('0 = dispose, 1 = checkin, 2 = disposed permanently, 3 = checkin success');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_item_dispose');
    }
};

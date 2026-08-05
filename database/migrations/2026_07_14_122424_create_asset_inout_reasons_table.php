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
        Schema::create('asset_inout_reason', function (Blueprint $table) {
            $table->id();
            $table->integer('action_type')->nullable()->comment('1 = Checkout and 2 = Checkin');
            $table->string('name');
            $table->integer('status')->default(1)->comment('1 = active and 0 = inactive');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_inout_reason');
    }
};

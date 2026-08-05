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
        Schema::create('checkout_acceptance_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('asset_id');
            $table->string('asset_type', 191);
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('chkout_acceptance_log_id')->nullable();
            $table->integer('accepted_via')->default(0)->comment('1-Portal, 0-Email');
            $table->boolean('is_accepted')->nullable()->comment('1-Accepted, 2-Declined');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkout_acceptance_histories');
    }
};

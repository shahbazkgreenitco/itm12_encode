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
        Schema::create('asset_summary', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->integer('total_devices')->nullable();
            $table->integer('ready_to_deploy')->nullable();
            $table->integer('deployed')->nullable();
            $table->integer('scrap')->nullable();
            $table->integer('out_for_repair')->nullable();
            $table->integer('pending')->nullable();
            $table->integer('test')->nullable();
            $table->integer('testing')->nullable();
            $table->integer('testsachin')->nullable();
            $table->integer('testing_purpose')->nullable();
            $table->integer('repair_inhouse')->nullable();
            $table->integer('lost-stolen')->nullable();
            $table->integer('dispose')->nullable();
            $table->integer('ready_on_stock')->nullable();
            $table->integer('new_added_users')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_summary');
    }
};

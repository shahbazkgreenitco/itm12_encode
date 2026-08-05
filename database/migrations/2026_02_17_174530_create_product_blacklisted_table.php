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
        Schema::create('product_blacklisted', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->nullable();
            $table->integer('basic_id')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->longText('Version')->nullable();
            $table->longText('publisher')->nullable();
            $table->longText('product')->nullable();
            $table->integer('blacklist_type')->nullable()->comment('1-Version Based, 2-Publisher Based, 3-Software Based, 4-Manually Added Software');
            $table->string('manual_software', 191)->nullable();
            $table->integer('manual_sw_type')->nullable()->comment('1 - Exact Software, 2 - Similar Software');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_blacklisted');
    }
};

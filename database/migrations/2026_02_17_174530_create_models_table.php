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
        Schema::create('models', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('modelno')->nullable();
            $table->integer('manufacturer_id');
            $table->integer('category_id');
            $table->timestamps();
            $table->integer('depreciation_id')->nullable()->default(0);
            $table->integer('user_id');
            $table->integer('eol')->nullable()->default(0);
            $table->string('image')->nullable();
            $table->string('image_thumbnail')->nullable();
            $table->boolean('deprecated_mac_address')->nullable()->default(false);
            $table->softDeletes();
            $table->integer('fieldset_id')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('models');
    }
};

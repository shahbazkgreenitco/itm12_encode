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
        Schema::create('model_history', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('model_id');
            $table->string('name');
            $table->string('modelno', 191)->nullable();
            $table->integer('manufacturer_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->integer('depreciation_id')->nullable()->default(0);
            $table->integer('user_id');
            $table->integer('eol')->nullable()->default(0);
            $table->string('image', 191)->nullable();
            $table->string('image_thumbnail', 191)->nullable();
            $table->tinyInteger('deprecated_mac_address')->default(0);
            $table->integer('fieldset_id')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_history');
    }
};

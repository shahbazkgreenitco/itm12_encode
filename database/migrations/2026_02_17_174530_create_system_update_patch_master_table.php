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
        Schema::create('system_update_patch_master', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('name');
            $table->text('description')->nullable();
            $table->text('kbid')->nullable();
            $table->text('patch_type')->nullable();
            $table->text('patch_version')->nullable();
            $table->text('patch_url')->nullable();
            $table->text('patch_severity')->nullable();
            $table->text('patch_status')->nullable();
            $table->text('device_id')->nullable();
            $table->timestamps();
            $table->string('patch_publisher', 191)->nullable();
            $table->string('os', 191)->nullable()->default('1');
            $table->string('approved_by', 191)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_update_patch_master');
    }
};

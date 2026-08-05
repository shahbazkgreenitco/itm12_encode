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
        Schema::create('patch_masters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('patch_name', 191);
            $table->longText('patch_description')->nullable();
            $table->integer('os')->default(1)->comment('1-windows | 2-linux');
            $table->string('version', 191)->nullable();
            $table->boolean('reboot_required')->default(false);
            $table->boolean('approved_status')->default(false);
            $table->integer('severity')->default(4)->comment('1-Critical | 2-Important | 3 - Moderate | 4 - Low | 5 - unrated');
            $table->boolean('patch_uninstallation')->default(false);
            $table->integer('type')->default(1)->comment('1-file | 2-link');
            $table->string('file', 191)->nullable();
            $table->string('link', 191)->nullable();
            $table->timestamps();
            $table->string('original_file_name', 191)->nullable();
            $table->string('extension', 191)->nullable();
            $table->string('patch_publisher', 191)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patch_masters');
    }
};

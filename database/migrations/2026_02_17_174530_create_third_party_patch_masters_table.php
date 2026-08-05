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
        Schema::create('third_party_patch_masters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 191);
            $table->longText('description')->nullable();
            $table->string('version', 191)->nullable();
            $table->date('publisher_date')->nullable();
            $table->string('patch_type', 191)->nullable();
            $table->string('reboot', 191)->default('0')->comment('0-Not Required, 1-Required');
            $table->integer('severity')->default(4)->comment('1-Critical(9.0 - 10.0), 2-High (7.0 - 8.9), 3-Medium (4.0 - 6.9), 4-Low (0.1 - 3.9), 5-None (0.0)');
            $table->integer('os_id')->default(1)->comment('1-Windows');
            $table->integer('type')->default(0)->comment('0-file, 1-link');
            $table->string('link', 191)->nullable();
            $table->string('file', 191)->nullable();
            $table->string('original_file_name', 191)->nullable();
            $table->string('extension', 191)->nullable();
            $table->string('publisher', 191)->nullable();
            $table->string('publisher_patch_id', 191)->nullable();
            $table->longText('unique_name')->nullable()->comment('name + (publisher_date or version )');
            $table->integer('patch_type_update')->default(1)->comment('1-OS, 2-Hardware, 3- Software');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('third_party_patch_masters');
    }
};

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
        Schema::create('sw_patch', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('software_name')->nullable();
            $table->text('os')->nullable();
            $table->text('version')->nullable();
            $table->text('file')->nullable();
            $table->string('original_file_name', 191)->nullable();
            $table->string('extension', 191)->nullable();
            $table->bigInteger('uploader_id')->nullable();
            $table->timestamps();
            $table->text('link')->nullable();
            $table->bigInteger('uploaded_type')->nullable();
            $table->bigInteger('software_type')->nullable()->comment('1-Version Based, 2-Software Based, 3-Manually Added Software');
            $table->string('patch_name', 191)->nullable()->unique();
            $table->string('manual_software', 191)->nullable();
            $table->bigInteger('manual_sw_type')->nullable()->comment('1 - Exact Software, 2 - Similar Software');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sw_patch');
    }
};

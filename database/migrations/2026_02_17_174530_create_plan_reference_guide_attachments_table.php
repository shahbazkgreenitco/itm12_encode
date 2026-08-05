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
        Schema::create('plan_reference_guide_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('plan_id');
            $table->string('original_file_name', 191);
            $table->string('file_name', 191);
            $table->string('thumbnail_file_name', 191)->nullable();
            $table->string('extension', 191)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_reference_guide_attachments');
    }
};

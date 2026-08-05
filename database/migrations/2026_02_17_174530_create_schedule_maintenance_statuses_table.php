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
        Schema::create('schedule_maintenance_statuses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('status', 191);
            $table->boolean('is_enabled')->default(false);
            $table->boolean('is_editable')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->string('color')->nullable();
            $table->string('original_file_name', 191)->nullable();
            $table->string('file_name', 191)->nullable();
            $table->string('extension', 191)->nullable();
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_maintenance_statuses');
    }
};

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
        Schema::create('mailroom_status', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('status_name', 191);
            $table->boolean('status');
            $table->string('custom_field_set', 191)->nullable();
            $table->string('color_code', 191);
            $table->softDeletes();
            $table->timestamps();
            $table->boolean('default_status')->default(false);
            $table->boolean('signature')->default(false);
            $table->boolean('proof')->default(false);
            $table->boolean('is_default')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mailroom_status');
    }
};

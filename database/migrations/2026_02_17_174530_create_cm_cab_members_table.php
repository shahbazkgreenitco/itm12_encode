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
        Schema::create('cm_cab_members', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('cab_id');
            $table->integer('user_id');
            $table->integer('hierarchy_level')->nullable();
            $table->timestamps();
            $table->integer('location_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cm_cab_members');
    }
};

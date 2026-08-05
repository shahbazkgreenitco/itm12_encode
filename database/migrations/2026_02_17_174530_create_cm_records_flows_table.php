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
        Schema::create('cm_records_flows', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('reason_id');
            $table->integer('stage_id')->nullable();
            $table->integer('status_id')->nullable();
            $table->integer('actioner_id')->nullable()->comment('Who do the action');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cm_records_flows');
    }
};

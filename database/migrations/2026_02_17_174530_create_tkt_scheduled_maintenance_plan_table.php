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
        Schema::create('tkt_scheduled_maintenance_plan', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->text('title')->nullable();
            $table->integer('recursive_plan')->nullable()->comment('1-Daily,2-Weekly,3-Monthly,4-Quarterly,5-Yearly');
            $table->text('duration')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_scheduled_maintenance_plan');
    }
};

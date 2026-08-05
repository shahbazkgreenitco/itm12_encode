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
        Schema::create('tkt_auto_allocation_groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('name');
            $table->text('desc');
            $table->bigInteger('department_id')->nullable();
            $table->bigInteger('created_by');
            $table->boolean('enabled')->default(true);
            $table->timestamps();
            $table->integer('location_approval_required')->default(0);
            $table->integer('location_approval_required_for')->nullable()->default(1);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_auto_allocation_groups');
    }
};

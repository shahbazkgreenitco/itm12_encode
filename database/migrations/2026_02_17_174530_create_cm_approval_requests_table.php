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
        Schema::create('cm_approval_requests', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('record_id')->nullable();
            $table->integer('cab_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->tinyInteger('hierarchy_approval')->nullable();
            $table->integer('hierarchy_level')->nullable();
            $table->integer('approve_status')->nullable()->comment('1-Approved, 2-Rejected');
            $table->timestamps();
            $table->text('comments')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cm_approval_requests');
    }
};

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
        Schema::create('cm_review_approvals', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('record_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('approval_status')->default(3)->comment('1-Approved,2-reject,3-waiting for approval');
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cm_review_approvals');
    }
};

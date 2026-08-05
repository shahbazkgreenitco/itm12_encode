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
        Schema::create('tkt_approval_request_archived', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('pr_id');
            $table->integer('pab_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->tinyInteger('hierarchy_approval')->nullable();
            $table->tinyInteger('hierarchy_level')->nullable();
            $table->integer('approve_status')->comment('1-Approved, 2-Rejected, 3-Requested, 4-Waiting for Prior Level Approval');
            $table->longText('comments')->nullable();
            $table->bigInteger('delegated_user_id')->nullable();
            $table->bigInteger('delegated_approved_user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_approval_request_archived');
    }
};

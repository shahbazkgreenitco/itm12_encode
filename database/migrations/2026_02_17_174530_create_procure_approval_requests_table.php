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
        Schema::create('procure_approval_requests', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('pr_id')->nullable();
            $table->integer('pab_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->tinyInteger('hierarchy_approval')->nullable();
            $table->integer('hierarchy_level')->nullable();
            $table->integer('approve_status')->nullable()->comment('1-Approved, 2-Rejected, 3-Requested, 4-Waiting for Prior Level Approval');
            $table->bigInteger('approved_qnum')->nullable()->comment('Approved Quotation mentioned by its position either 1st/2nd/3rd quotation');
            $table->timestamps();
            $table->text('comments')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procure_approval_requests');
    }
};

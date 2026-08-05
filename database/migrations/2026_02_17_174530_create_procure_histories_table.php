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
        Schema::create('procure_histories', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('pr_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->timestamps();
            $table->integer('action_msg')->nullable();
            $table->longText('assign_to')->nullable()->comment('1 procurement request created,2 updated status ,3 supplier invited,4 approval request,5 supplier updated Qutation,6 po generated assin_to,7 revorke decision assin_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procure_histories');
    }
};

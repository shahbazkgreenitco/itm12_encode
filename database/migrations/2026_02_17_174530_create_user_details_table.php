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
        Schema::create('user_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('app_version', 191)->nullable();
            $table->timestamps();
            $table->bigInteger('ticket_handler_delegated_user')->nullable();
            $table->bigInteger('request_approval_delegated_user')->nullable();
            $table->bigInteger('change_approval_delegated_user')->nullable();
            $table->bigInteger('procurement_approval_delegated_user')->nullable();
            $table->boolean('opt_in_for_wa_notification')->nullable();
            $table->integer('platform_type')->default(1)->comment('1-Android, 2-IOS');
            $table->string('baseCostCode', 25)->nullable();
            $table->string('seat_no', 191)->nullable();
            $table->string('exit_type', 191)->nullable()->comment('Type of exit, e.g., 1 - Resignation, 2 - Termination, 3 - Retirement, 4 - Death, 5 - Other, 6 - Dismissal, 7 - Layoff, 8 - End of Contract, 9 - Early exit');
            $table->string('grade', 191)->nullable();
            $table->bigInteger('exit_ticket_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};

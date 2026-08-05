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
        Schema::create('schedules', function (Blueprint $table) {
            $table->integer('id', true);
            $table->tinyInteger('item_type')->nullable()->comment('1 - Device');
            $table->integer('item_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->tinyInteger('purpose_type')->nullable()->comment('1 - Request for Use, 2 - Maintenance');
            $table->tinyInteger('is_completed')->nullable()->comment('1 - Yes, 2 - No, 3 - Cancelled');
            $table->unsignedInteger('completed_ref')->nullable()->comment('proof Reference ID about Completion of Schedule Needs');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('cancelled_by')->nullable();
            $table->unsignedInteger('completed_by')->nullable();
            $table->unsignedInteger('reschedule_id')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->dateTime('completed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};

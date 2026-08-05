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
        Schema::create('device_audits', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->nullable();
            $table->integer('device_id')->nullable();
            $table->tinyInteger('ratings')->nullable()->default(1)->comment('1 - Good, 2 - Average, 3 - Bad');
            $table->text('notes')->nullable();
            $table->dateTime('audit_date')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->smallInteger('audit_type')->nullable()->default(1)->comment('1-Admin Audit, 2-Checkout Confirmation, 3-Self Audit, 4-Asset Verified, 5-Offline Audit');
            $table->integer('asset_log_id')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->integer('place_id')->nullable();
            $table->integer('internal_place_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_audits');
    }
};

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
        Schema::create('device_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('device_checkin')->default(0);
            $table->integer('device_checkout')->default(0);
            $table->integer('high_priority_ni_not_detected')->default(0);
            $table->integer('ni_not_detected')->default(10)->comment('How many days NI not detected report');
            $table->integer('ni_not_detected_type')->nullable()->comment('1-RoleBased, 2-DeviceRead Permission, 3-Email)');
            $table->string('ni_not_detected_value', 191)->nullable();
            $table->longText('ni_not_detected_email')->nullable();
            $table->integer('send_reminder')->default(0)->comment('Do you want to send reminders to users who have devices?');
            $table->integer('number_of_devices')->default(0)->comment('How many devices do you want to send the reminder to?');
            $table->integer('send_reminder_users_type')->nullable()->comment('1-RoleBased, 2-DeviceRead Permission, 3-Email');
            $table->string('send_reminder_users_value', 191)->nullable();
            $table->longText('send_reminder_users_email')->nullable();
            $table->timestamps();
            $table->integer('live_monitor_auto_refresh')->default(0);
            $table->integer('live_monitor_auto_run_in_minute')->nullable();
            $table->string('asset_tag_data')->nullable();
            $table->longText('device_export_column')->nullable();
            $table->string('device_report_email', 191)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_settings');
    }
};

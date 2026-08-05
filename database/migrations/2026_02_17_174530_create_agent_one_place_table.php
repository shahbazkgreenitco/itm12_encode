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
        Schema::create('agent_one_place', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('agent_name')->nullable();
            $table->text('os')->nullable();
            $table->text('version')->nullable();
            $table->longText('description')->nullable();
            $table->text('file')->nullable();
            $table->string('original_file_name', 191)->nullable();
            $table->string('extension', 191)->nullable();
            $table->text('link')->nullable();
            $table->bigInteger('uploader_id')->nullable();
            $table->tinyInteger('uploaded_type')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('auto_check_type')->nullable()->comment('1-update, 2-disable');
            $table->integer('disable_type')->nullable()->comment('1-permanent disable, 2-remove agent, 3-remove agent if inactive for more than x day, 4-form x-to x time');
            $table->integer('disable_days')->nullable();
            $table->integer('update_hour')->nullable();
            $table->string('form_time', 191)->nullable();
            $table->string('to_time', 191)->nullable();
            $table->integer('update_type')->nullable()->comment('1-every time, 2-schedular, 3-after x hrs');
            $table->string('schedular_plan', 191)->nullable()->comment('1-Weekly, 2-Monthly, 3-Yearly');
            $table->string('update_days', 191)->nullable();
            $table->string('hour', 191)->nullable();
            $table->string('minute', 191)->nullable();
            $table->string('monthly', 191)->nullable();
            $table->string('monthly_date', 191)->nullable();
            $table->string('monthly_numbers', 191)->nullable();
            $table->string('monthly_week', 191)->nullable();
            $table->string('monthly_day', 191)->nullable();
            $table->string('monthly_month', 191)->nullable();
            $table->string('yearly', 191)->nullable();
            $table->string('yearly_months', 191)->nullable();
            $table->string('yearly_day', 191)->nullable();
            $table->string('yearly_months_week', 191)->nullable();
            $table->string('yearly_months_week_day', 191)->nullable();
            $table->string('yearly_months2', 191)->nullable();
            $table->string('retry', 191)->nullable();
            $table->string('device_id', 191)->nullable();
            $table->boolean('status')->default(true);
            $table->date('expiry_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_one_place');
    }
};

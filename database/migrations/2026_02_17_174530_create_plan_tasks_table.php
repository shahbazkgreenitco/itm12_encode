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
        Schema::create('plan_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('task_name', 191);
            $table->integer('plan_id');
            $table->timestamps();
            $table->integer('order_no');
            $table->tinyInteger('is_order_no_need')->default(2)->comment('1 = Yes, 2 = No');
            $table->tinyInteger('is_gps_loc_need')->default(2)->comment('1 = Yes, 2 = No');
            $table->tinyInteger('is_doc_need')->default(2)->comment('1 = Yes, 2 = No');
            $table->string('orginal_file_name', 191)->nullable();
            $table->string('file_name', 191)->nullable();
            $table->string('extension', 191)->nullable();
            $table->integer('uploader_id');
            $table->longText('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_tasks');
    }
};

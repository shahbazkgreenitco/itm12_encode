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
        Schema::create('visits', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('status')->default(0)->comment('0 - New, 1 - Matched, 2 - Match Not Found, 3 - Invalid Data');
            $table->bigInteger('user_id')->nullable();
            $table->timestamp('visit_at')->nullable();
            $table->text('data')->nullable();
            $table->timestamps();
            $table->bigInteger('visit_id')->nullable();
            $table->text('token_no')->nullable();
            $table->text('name')->nullable();
            $table->bigInteger('mobile')->nullable();
            $table->bigInteger('department_id')->nullable();
            $table->bigInteger('counter_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};

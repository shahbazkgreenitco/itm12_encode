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
        Schema::create('mailroom_configure', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('email_notification')->nullable()->comment('1 = All status change, 2 = Initiated, 3 = Delivered only');
            $table->text('site_visible')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mailroom_configure');
    }
};

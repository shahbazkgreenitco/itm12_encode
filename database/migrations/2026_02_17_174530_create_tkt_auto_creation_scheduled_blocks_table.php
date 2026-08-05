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
        Schema::create('tkt_auto_creation_scheduled_blocks', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('email');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('tkt_ac_account_ids');
            $table->tinyInteger('status')->default(1)->comment('1 - Registered, 2 - Under Block, 3 - Completed');
            $table->integer('user_id')->comment('Who last updated it');
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->string('tkt_ac_account_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_auto_creation_scheduled_blocks');
    }
};

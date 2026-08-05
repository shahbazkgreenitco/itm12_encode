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
        Schema::create('tkt_user_controls', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->nullable()->comment('User Id');
            $table->boolean('ctrl_transfer')->nullable()->comment('Privilege for transfer ticket to other department');
            $table->boolean('ctrl_assign')->nullable()->comment('Privilege for transfer ticket to others members');
            $table->boolean('ctrl_delete')->nullable()->comment('Privilege to delete the ticket');
            $table->timestamps();
            $table->tinyInteger('ctrl_self_assign')->nullable()->comment('Privilege to self assign the ticket');
            $table->tinyInteger('create_for_others')->nullable()->comment('1 - allow to create ticket for others, null - ticket creation for self only');
            $table->tinyInteger('merge_privilege')->nullable()->default(0)->comment('1 - Yes, 0 - No');
            $table->tinyInteger('ctrl_change_creator')->nullable()->comment('To change the creator of a ticket');
            $table->tinyInteger('ctrl_mark_spam')->default(0);
            $table->tinyInteger('ctrl_tat')->nullable();
            $table->tinyInteger('ctrl_priority')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_user_controls');
    }
};

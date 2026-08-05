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
        Schema::create('tkt_ticket_pabs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 191);
            $table->text('description');
            $table->tinyInteger('hierarchy_approval')->comment('1 - Level By Level, 2 - Minimum Approval, 3 - Group Approval');
            $table->tinyInteger('required_minimum_approvals')->default(1)->comment('if hierarchy_approval = 2, then this value will active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_ticket_pabs');
    }
};

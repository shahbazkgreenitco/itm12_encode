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
        Schema::create('tkt_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('html_color', 7)->nullable();
            $table->string('color_code', 7)->nullable();
            $table->boolean('is_enabled');
            $table->text('attachment')->nullable();
            $table->timestamps();
            $table->tinyInteger('is_editable')->nullable()->default(0);
            $table->tinyInteger('tat_halt')->nullable()->default(0)->comment('Whether TAT hours calculation enabled (or) not');
            $table->softDeletes();
            $table->text('ticket_type')->nullable();
            $table->boolean('status_form')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_statuses');
    }
};

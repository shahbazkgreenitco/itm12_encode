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
        Schema::create('past_checks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cat_id')->nullable();
            $table->tinyInteger('action')->nullable()->comment('1 for check in; 2 for check out');
            $table->unsignedInteger('total')->nullable();
            $table->date('for_month')->nullable();
            $table->timestamp('created_at')->useCurrentOnUpdate()->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('past_checks');
    }
};

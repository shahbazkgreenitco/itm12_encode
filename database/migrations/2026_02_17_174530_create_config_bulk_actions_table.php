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
        Schema::create('config_bulk_actions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->nullable();
            $table->tinyInteger('action_type')->comment('1 - Import');
            $table->string('doc_name', 191)->comment('Original file name');
            $table->string('doc_path', 191)->comment('Unique file path or name');
            $table->integer('tot_success')->default(0);
            $table->integer('tot_failure')->default(0);
            $table->tinyInteger('module_id')->comment('1 - Project, 2 - Location, 3 - Supplier, 4 - Department, 5 - Internalplace, 6 - Purchase');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config_bulk_actions');
    }
};

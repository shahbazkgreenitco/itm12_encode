<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * FIX: `name` is a TEXT column. TEXT columns cannot be indexed without
     * a prefix length — causes Error 1071: key too long.
     *
     * Also `timestamp` columns (created_at, updated_at) do NOT need a prefix.
     * Only TEXT and VARCHAR need prefix lengths.
     */
    public function up(): void
    {
        Schema::create('ticket_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('name');
            $table->boolean('status');
            $table->timestamps();
            $table->string('department_id', 191)->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();

        });

        DB::statement('
            ALTER TABLE `ticket_types` ADD INDEX `idx_ticket_types` (
                `name`(100),
                `status`,
                `created_at`,
                `updated_at`
            )
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_types');
    }
};

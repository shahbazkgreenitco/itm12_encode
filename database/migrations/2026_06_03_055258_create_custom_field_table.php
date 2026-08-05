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
        if (!Schema::hasTable('accessories_custom_field')) {
            Schema::create('accessories_custom_field', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('accessory_id')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('asset_custom_field')) {
            Schema::create('asset_custom_field', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('asset_id')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('component_custom_field')) {
            Schema::create('component_custom_field', function (Blueprint $table) {
                $table->id();
                $table->integer('component_id')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('consumable_custom_field')) {
            Schema::create('consumable_custom_field', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('consumable_id')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('license_custom_field')) {
            Schema::create('license_custom_field', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('license_id')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('mailroom_custom_field')) {
            Schema::create('mailroom_custom_field', function (Blueprint $table) {
                $table->id();
                $table->UnsignedBigInteger('mailroom_id')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('kanban_custom_field')) {
            Schema::create('kanban_custom_field', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('kanban_id')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accessories_custom_field');
        Schema::dropIfExists('asset_custom_field');
        Schema::dropIfExists('component_custom_field');
        Schema::dropIfExists('consumable_custom_field');
        Schema::dropIfExists('license_custom_field');
        Schema::dropIfExists('mailroom_custom_field');
        Schema::dropIfExists('kanban_custom_field');
    }
};

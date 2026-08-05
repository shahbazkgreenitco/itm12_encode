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
        Schema::create('itm_network_user_accounts', function (Blueprint $table) {
            $table->integer('id', true)->index('id_itm_ni_user_idx');
            $table->integer('basic_id')->nullable()->index('basic_id_itm_ni_user_idx')->comment('Reference for basic table');
            $table->string('AccountType', 60)->nullable()->index('accounttype_itm_ni_user_idx');
            $table->string('Caption')->nullable()->index('caption_itm_ni_user_idx');
            $table->string('FullName')->nullable()->index('fullname_itm_ni_user_idx');
            $table->string('Domain')->nullable()->index('domain_itm_ni_user_idx');
            $table->string('Name')->nullable()->index('name_itm_ni_user_idx');
            $table->string('SID')->nullable()->index('sid_itm_ni_user_idx');
            $table->string('InstallDate', 25)->nullable();
            $table->string('Status', 25)->nullable()->index('status_itm_ni_user_idx');
            $table->timestamp('LastLogon')->nullable();
            $table->timestamps();

            // $table->primary(['id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm_network_user_accounts');
    }
};

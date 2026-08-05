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
        Schema::create('tkt_alias_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('alias_email')->comment('Alias Email Account Name');
            $table->integer('default_department_id')->nullable()->comment('Default department for auto ticket from email');
            $table->integer('default_prob_cat_id')->nullable()->comment('default problem category for auto ticket from email');
            $table->integer('default_sub_cat_id')->nullable()->comment('default sub category for auto ticket from email');
            $table->tinyInteger('account_status')->default(1)->comment('1 = Active, 2 = Not Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_alias_accounts');
    }
};

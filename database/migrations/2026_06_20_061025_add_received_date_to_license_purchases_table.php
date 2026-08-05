<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('license_purchases', function (Blueprint $table) {
            $table->date('received_date')->nullable()->after('purchase_date');  // after() ko apne requirement ke hisab se change kar sakte hain
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('license_purchases', function (Blueprint $table) {
            $table->dropColumn('received_date');
        });
    }
};

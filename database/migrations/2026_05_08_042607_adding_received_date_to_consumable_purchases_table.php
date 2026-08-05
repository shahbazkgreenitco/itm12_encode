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
        if(!Schema::hasColumn('consumable_purchases', 'received_date')) {
            Schema::table('consumable_purchases', function (Blueprint $table) {
                $table->date('received_date')->after('purchase_date')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if(Schema::hasColumn('consumable_purchases', 'received_date')) {
            Schema::table('consumable_purchases', function (Blueprint $table) {
                $table->dropColumn('received_date');
            });
        }
    }
};

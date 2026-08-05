<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class TicketPendencyReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('permissions')->insert([
            [
                'name' => 'TechnicianPendencyReportRead',
                'guard_name' => 'web',
                'module_id' => 22,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'TechnicianPendencyReportDownload',
                'guard_name' => 'web',
                'module_id' => 22,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'TicketPendencyReportRead',
                'guard_name' => 'web',
                'module_id' => 22,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'TicketPendencyReportDownload',
                'guard_name' => 'web',
                'module_id' => 22,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         DB::table('permissions')
            ->whereIn('name', ['TechnicianPendencyReportRead', 'TechnicianPendencyReportDownload','TicketPendencyReportRead','TicketPendencyReportDownload'])
            ->delete();
    }
}

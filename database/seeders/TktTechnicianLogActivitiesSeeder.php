<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TktTechnicianLogActivitiesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tkt_technician_log_activities')->insert([
            [
                'id' => 1,
                'name' => 'Break',
                'keep_assigning' => 1,
                'created_at' => '2022-04-08 15:26:02',
                'updated_at' => '2022-04-08 15:26:02',
            ],
            [
                'id' => 2,
                'name' => 'Logout',
                'keep_assigning' => 0,
                'created_at' => '2022-04-08 15:26:02',
                'updated_at' => '2022-04-08 15:26:02',
            ],
        ]);
    }
}
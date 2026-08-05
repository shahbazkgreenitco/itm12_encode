<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TktPrioritiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tkt_priorities')->insert([
            [
                'id' => 1,
                'name' => 'Critical',
                'html_color' => 2,
                'service_time' => 2,
                'service_time_desc' => '2 Hrs',
                'is_enabled' => 1,
                'created_at' => Carbon::parse('2017-08-19 18:49:23'),
                'updated_at' => Carbon::parse('2018-02-17 23:43:28'),
            ],
            [
                'id' => 2,
                'name' => 'High',
                'html_color' => 8,
                'service_time' => 8,
                'service_time_desc' => '8 Hrs',
                'is_enabled' => 1,
                'created_at' => Carbon::parse('2017-08-23 18:55:23'),
                'updated_at' => Carbon::parse('2017-10-10 14:57:37'),
            ],
            [
                'id' => 3,
                'name' => 'Medium',
                'html_color' => 48,
                'service_time' => 48,
                'service_time_desc' => '48 Hrs',
                'is_enabled' => 1,
                'created_at' => Carbon::parse('2017-08-23 18:56:09'),
                'updated_at' => Carbon::parse('2017-10-10 14:57:20'),
            ],
            [
                'id' => 4,
                'name' => 'Low',
                'html_color' => 96,
                'service_time' => 96,
                'service_time_desc' => '96 Hrs',
                'is_enabled' => 1,
                'created_at' => Carbon::parse('2017-10-10 14:57:57'),
                'updated_at' => Carbon::parse('2017-10-10 14:57:57'),
            ],
        ]);
    }
}

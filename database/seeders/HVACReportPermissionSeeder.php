<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HVACReportPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('permissions')->insert([
            [
                'name' => 'HvacReport',
                'guard_name' => 'web',
                'module_id' => 22,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'HvacReportDownload',
                'guard_name' => 'web',
                'module_id' => 22,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}

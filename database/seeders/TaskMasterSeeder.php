<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class TaskMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Task Statuses
        DB::table('task_statuses')->insert([
            ['id' => 1, 'name' => 'Queued', 'default_status' => 1],
            ['id' => 2, 'name' => 'Conditional', 'default_status' => 0],
            ['id' => 3, 'name' => 'Failed', 'default_status' => 0],
            ['id' => 4, 'name' => 'Cancelled', 'default_status' => 0],
            ['id' => 5, 'name' => 'Waiting for Others', 'default_status' => 0],
            ['id' => 6, 'name' => 'Assigned', 'default_status' => 0],
            ['id' => 7, 'name' => 'Completed', 'default_status' => 0],
            ['id' => 8, 'name' => 'in progress', 'default_status' => 0],
            ['id' => 9, 'name' => 'Not Applicable', 'default_status' => 0],
        ]);

        // Task Priorities
        DB::table('task_priorities')->insert([
            ['id' => 1, 'name' => 'Urgency'],
            ['id' => 2, 'name' => 'High'],
            ['id' => 3, 'name' => 'Medium'],
            ['id' => 4, 'name' => 'Low'],
        ]);

        // Task Types
        DB::table('task_types')->insert([
            ['id' => 1, 'name' => 'To Change'],
            ['id' => 2, 'name' => 'To Project'],
            ['id' => 3, 'name' => 'To Others'],
            ['id' => 4, 'name' => 'To Ticket'],
        ]);
    }
}

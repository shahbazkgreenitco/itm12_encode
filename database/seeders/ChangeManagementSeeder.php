<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChangeManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        // CM Statuses
        DB::table('cm_statuses')->upsert([
            ['id' => 1, 'name' => 'Open', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Planning', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Awaiting Approval', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Pending Release', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'name' => 'Pending Review', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'name' => 'Closed', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'name' => 'Rejected', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 8, 'name' => 'On Hold', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 9, 'name' => 'Implementation on Progress', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 10, 'name' => 'Approved', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 11, 'name' => 'Roll Out', 'description' => 'Roll Out', 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['name', 'description', 'updated_at']);

        // CM Close States
        DB::table('cm_close_states')->upsert([
            ['id' => 1, 'name' => 'Success', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Failure', 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['name', 'updated_at']);

        // CM Change Types
        DB::table('cm_change_types')->upsert([
            ['id' => 1, 'name' => 'Minor', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Standard', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Major', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Emergency', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['name', 'description', 'updated_at']);

        // CM Risks
        DB::table('cm_risks')->upsert([
            ['id' => 1, 'name' => 'Low', 'description' => 'Low', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Medium', 'description' => 'Medium', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'High', 'description' => 'High', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Very High', 'description' => 'Very High', 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['name', 'description', 'updated_at']);

        // CM Impacts
        DB::table('cm_impacts')->upsert([
            ['id' => 1, 'name' => 'Low', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Medium', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'High', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['name', 'description', 'updated_at']);

        // CM Priorities
        DB::table('cm_priorities')->upsert([
            ['id' => 1, 'name' => 'Critical', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'High', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Medium', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Low', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['name', 'description', 'updated_at']);
    }
}
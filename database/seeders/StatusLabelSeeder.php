<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusLabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $status = [
            [
                'name' => 'Ready to Deploy',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deployable' => 1,
                'pending' => 0,
                'archived' => 0,
                'notes' => 'Item is ready for production',
                'deployed' => 0,
                'sold' => null,
                'is_edit_disabled' => 1,
                'stolen_item' => null,
            ],
            [
                'name' => 'Pending',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deployable' => 0,
                'pending' => 1,
                'archived' => 0,
                'notes' => null,
                'deployed' => 0,
                'sold' => null,
                'is_edit_disabled' => 1,
                'stolen_item' => null
            ],
            [
                'name' => 'Scrap',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deployable' => 0,
                'pending' => 0,
                'archived' => 0,
                'notes' => 'These assets are permanently undeployable',
                'deployed' => 0,
                'sold' => null,
                'is_edit_disabled' => 1,
                'stolen_item' => null
            ],
            [
                'name' => 'Out for Repair',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deployable' => 0,
                'pending' => 0,
                'archived' => 0,
                'notes' => 'Item which is under repairing work',
                'deployed' => 0,
                'sold' => null,
                'is_edit_disabled' => 1,
                'stolen_item' => null,
            ],
            [
                'name' => 'Lost-Stolen',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deployable' => 0,
                'pending' => 0,
                'archived' => 1,
                'notes' => null,
                'deployed' => 0,
                'sold' => null,
                'is_edit_disabled' => 1,
                'stolen_item' => 1,
            ],
            [
                'name' => 'Deployed',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deployable' => 0,
                'pending' => 0,
                'archived' => 0,
                'notes' => 'Items are in Production',
                'deployed' => 1,
                'sold' => null,
                'is_edit_disabled' => 1,
                'stolen_item' => null,
            ],
            [
                'name' => 'Dispose',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deployable' => 0,
                'pending' => 0,
                'archived' => 1,
                'notes' => 'Item is sold',
                'deployed' => 0,
                'sold' => 1,
                'is_edit_disabled' => 1,
                'stolen_item' => null,
            ],
        ];

        DB::table('status_labels')->insert($status);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssetInoutReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('asset_inout_reason')->truncate();

        DB::table('asset_inout_reason')->insert([
            [
                'id' => 1,
                'action_type' => 2,
                'name' => 'Returned by Replacement',
                'status' => 1,
            ],
            [
                'id' => 2,
                'action_type' => 2,
                'name' => 'Returned by employee leaving',
                'status' => 1,
            ],
            [
                'id' => 3,
                'action_type' => 2,
                'name' => 'Returned by Temporary',
                'status' => 1,
            ],
            [
                'id' => 4,
                'action_type' => 1,
                'name' => 'Issued by Replacement',
                'status' => 1,
            ],
            [
                'id' => 5,
                'action_type' => 1,
                'name' => 'Issued to New joinee',
                'status' => 1,
            ],
            [
                'id' => 6,
                'action_type' => 1,
                'name' => 'Issued Standby/Temporary',
                'status' => 1,
            ],
            [
                'id' => 7,
                'action_type' => null,
                'name' => 'Laptop sent for in house repair',
                'status' => 1,
            ],
            [
                'id' => 8,
                'action_type' => null,
                'name' => 'Laptop repaired in house and made good',
                'status' => 1,
            ],
            [
                'id' => 9,
                'action_type' => null,
                'name' => 'Laptop found irreparable and made un-usable',
                'status' => 1,
            ],
            [
                'id' => 10,
                'action_type' => null,
                'name' => 'Sent to repairs to vendor',
                'status' => 1,
            ],
            [
                'id' => 11,
                'action_type' => null,
                'name' => 'Returned after repairs by vendor',
                'status' => 1,
            ],
            [
                'id' => 12,
                'action_type' => null,
                'name' => 'Not repairable by Vendor',
                'status' => 1,
            ],
            [
                'id' => 13,
                'action_type' => null,
                'name' => 'Disposed off',
                'status' => 1,
            ],
            [
                'id' => 14,
                'action_type' => null,
                'name' => 'Laptop Found in Good Condition',
                'status' => 1,
            ],
            [
                'id' => 15,
                'action_type' => null,
                'name' => 'Lost Laptop Sent for In-House Repair',
                'status' => 1,
            ],
            [
                'id' => 16,
                'action_type' => null,
                'name' => 'Lost Laptop Sent to Vendor for Repair',
                'status' => 1,
            ],
            [
                'id' => 17,
                'action_type' => null,
                'name' => 'Lost Laptop Marked as Scrap',
                'status' => 1,
            ],
            [
                'id' => 18,
                'action_type' => null,
                'name' => 'Lost Laptop Marked for Disposal',
                'status' => 1,
            ],
            [
                'id' => 19,
                'action_type' => null,
                'name' => 'Scrap Laptop Found in Good Condition',
                'status' => 1,
            ],
            [
                'id' => 20,
                'action_type' => null,
                'name' => 'Scrap Laptop Sent for In-House Repair',
                'status' => 1,
            ],
            [
                'id' => 21,
                'action_type' => null,
                'name' => 'Scrap Laptop Sent to Vendor for Repair',
                'status' => 1,
            ],
            [
                'id' => 22,
                'action_type' => null,
                'name' => 'Scrap Laptop Marked as Lost/Stolen',
                'status' => 1,
            ],
            [
                'id' => 23,
                'action_type' => null,
                'name' => 'Scrap Laptop Marked for Disposal',
                'status' => 1,
            ],
            [
                'id' => 24,
                'action_type' => null,
                'name' => 'Stock Laptop found as Scrap Lost',
                'status' => 1,
            ],
            [
                'id' => 25,
                'action_type' => 2,
                'name' => 'Checkin reasons not available',
                'status' => 1,
            ],
            [
                'id' => 26,
                'action_type' => 1,
                'name' => 'Checkout reasons not available',
                'status' => 1,
            ],
        ]);
    }
}

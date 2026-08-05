<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket\ActionControl;
use Carbon\Carbon;

class ActionControlSeeder extends Seeder
{
    public function run()
    {
        ActionControl::updateOrCreate(
            ['id' => 3], // target id
            [
                'user_id' => 3,

                'ctrl_transfer' => 1,
                'ctrl_assign' => 1,
                'ctrl_delete' => 0,
                'ctrl_self_assign' => 1,
                'ctrl_change_creator' => 1,
                'ctrl_mark_spam' => 0,
                'ctrl_tat' => 1,
                'ctrl_priority' => 1,

                'create_for_others' => 1,
                'merge_privilege' => 1,

                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );
    }
}

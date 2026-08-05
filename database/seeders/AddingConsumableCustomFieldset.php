<?php

namespace Database\Seeders;

use App\Models\Settings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddingConsumableCustomFieldset extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Settings::where('id', 1)->update([
            'consumable_custom_fieldset_id' => 11
        ]);
    }
}

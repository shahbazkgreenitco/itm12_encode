<?php

namespace Database\Seeders;

use App\Models\LeaseType;
use Illuminate\Database\Seeder;

class LeaseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LeaseType::updateOrCreate([
            'name' => 'Operating Contract',
        ]);

        LeaseType::updateOrCreate([
            'name' => 'Finance Contract',
        ]);
    }
}

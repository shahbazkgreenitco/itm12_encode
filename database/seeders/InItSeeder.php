<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Department;
use App\Models\Location;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InItSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Company::updateOrCreate(
            ['name' => 'Greenitco Technologies'],
            ['company_tag' => 'GRT', 'created_at' => Carbon::now()]
        );

        Location::updateOrCreate(
            [
                'name' => 'Mumbai',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'country' => 'India',
                'user_id' => 1,
                'country_id' => 101,
                'state_id' => 4008,
                'city_id' => 133504,
                'address' => 'Mumbai',
                'company_id' => 1,
            ]
        );

        Location::updateOrCreate(
            [
                'name' => 'Other',
                'city' => 'Other',
                'state' => 'Other',
                'country' => 'India',
                'user_id' => 1,
                'country_id' => 101,
                'state_id' => 4008,
                'city_id' => 133024,
                'address' => 'Other',
                'company_id' => 1,
            ]
        );

        Department::updateOrCreate(
            [
                'name' => 'IT Support',
                'company_id' => 1,
                'department_tag' => 'IT',
                'description' => 'IT Support',
                'asset_department' => 0,
                'module_ticket_enabled' => 0
            ]
        );

        User::updateOrCreate(
            [
                'username' => 'admin',
                'email' => 'admin@greenitco.com',
                'seat_no' => 'A-101',
                'password' => Hash::make('password'),
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'activated' => 1,
                'activated_at' => Carbon::now(),
                'employee_num' => 'EMP001',
                'jobtitle' => 'Administrator',
                'job_grade' => 'A1',
                'phone' => '1234567890',
                'company_id' => 1,
                'department_id' => 1,
                'location_id' => 1,
                'base_location_id' => 1,
                'is_vip_user' => 0,
                'archived' => 0,
                'create_mode' => 1,
            ]
        );
    }
}

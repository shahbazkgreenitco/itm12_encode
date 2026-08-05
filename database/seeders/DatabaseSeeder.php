<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        // $this->call(StatusLabelSeeder::class);
        // $this->call(thresholdSettingsSeeder::class);
        /*User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);*/        
        // $this->call(ThresholdSettingsSeeder::class);
        // $this->call(TktAutoCreationAccountsSeeder::class);
        // $this->call(TicketRequestStatusSeeder::class);
        // $this->call(TktTechnicianLogActivitiesSeeder::class);
        // $this->call(TaskMasterSeeder::class);
        // $this->call(UpdateUserPermissionsSeeder::class);
        $this->call(HVACReportPermissionSeeder::class);
        $this->call([ChangeManagementSeeder::class,]);
    }
}

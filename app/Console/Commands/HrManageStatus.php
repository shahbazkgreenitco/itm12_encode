<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Log;

class HrManageStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:hrOneManageStatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To Update the status as per Api Response';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if(config('app.client') != "shyammetalics"){
            exit;
        }
        $this->info("start");
        $users = User::select('id', 'username', 'email', 'employee_num')->whereNotNull('employee_num')->orderBy('id', 'asc')->get();

        foreach ($users as $user) {
            // $this->info($user->email . " - " . $user->employee_num);
            $response = Http::withHeaders([
                'domainCode' => 'shyam',
                'API-Key' => '58b5c017a12139b80f6efbdfde63dd6cfa5d1442359d02fdf60ebc233ce1641f',
                'Content-Type' => 'application/json',
            ])->post('https://openapi.hrone.cloud/api/external/employees', [
                // "fromCreatedDate" => "",
                // "toCreatedDate" => "",
                "employeeCode" => $user->employee_num,
                "pagination" => [
                    "pageNumber" => 1,
                    "pageSize" => 100
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $employees = isset($data['employeeInfo']) ? $data['employeeInfo'] : [];
                // $this->info("Employees fetched successfully". json_encode($employees));
                foreach ($employees as $d) {
                    $workEmail = $d['work email'] ?? null;
                    $emp_no = $d['employee Code'] ?? null;
                    $empStatus = $d['employment status'] ?? null;
                    $this->info($emp_no . " - " . $user->employee_num);
                    /*if(!in_array($empStatus, ['probation', 'confirmed', 'resigned', 'relieved', 'settled'])) {
                        $this->info($empStatus);
                    }*/
                    if (!empty($emp_no) && !empty($emp_no)) {
                        // $user = User::where('email', $workEmail)->first();
                        if($user) {
                            switch (strtolower($empStatus)) {
                                case 'probation':
                                case 'confirmed':
                                case 'resigned':
                                    $user->activated = 1;
                                    break;
                                case 'relieved':
                                case 'settled':
                                    $user->activated = 0;
                                    break;
                                default:
                                    Log::info("new user status detected: " . $empStatus);
                                    $user->activated = $user->activated; // keep existing status
                                    break;
                            }
                            $user->timestamps = false;
                            $user->save();
                            // echo "Updated $workEmail => {$user->activated}\n";
                        }
                    }
                }
            } else {
                $this->error("API call failed: " . $response->status());
                Log::info("API call failed: " . $response->status());
            }
        }
        $this->info("success");
    }
}

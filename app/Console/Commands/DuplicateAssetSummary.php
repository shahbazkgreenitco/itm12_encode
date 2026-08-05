<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Model;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Auth;

class DuplicateAssetSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:duplicate-summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Duplicate todays asset_summary records for tomorrow';

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
        try {
            DB::beginTransaction(); 
    
            function convertToDbColumn($statusName) {
                return strtolower(str_replace(' ', '_', $statusName));
            }
    
            $yesterday = Carbon::yesterday()->toDateString();
            $today = Carbon::today()->toDateString();
    
            // Get the list of columns dynamically, excluding specific ones
            $columns = DB::getSchemaBuilder()->getColumnListing('asset_summary');
            $columns = array_diff($columns, ['id', 'date', 'created_at', 'updated_at', 'total_devices']);
    
            $formattedColumns = array_map(function ($col) {
                $formatted = ucwords(str_replace('_', ' ', $col));
                $hyphenatedFormatted = str_replace(' ', ' ', $formatted);
                $exists = DB::table('status_labels')->where('name', $hyphenatedFormatted)->exists();
                $underscoreFormatted = str_replace(' ', '_', $formatted);
                $checkExists = DB::table('status_labels')->where('name', $underscoreFormatted)->exists();
                return $exists ? $hyphenatedFormatted : ($checkExists ? $underscoreFormatted : $formatted);
            }, $columns);

            // Get the list of columns dynamically, excluding specific ones
            $columnCatWise = DB::getSchemaBuilder()->getColumnListing('category_wise_asset_summaries');
            $columnsCatWise = array_diff($columnCatWise, ['id', 'category_wise_id','date', 'created_at', 'updated_at', 'total_devices']);

            $formattedColumnsCatWise = array_map(function ($col) {
                $formattedCatWise = ucwords(str_replace('_', ' ', $col));
                $hyphenatedFormattedCatWise = str_replace(' ', ' ', $formattedCatWise);
                $existsCatWise = DB::table('status_labels')->where('name', $hyphenatedFormattedCatWise)->exists();
                $underscoreFormattedCatWise = str_replace(' ', '_', $formattedCatWise);
                $checkExistsCatWise = DB::table('status_labels')->where('name', $underscoreFormattedCatWise)->exists();
                return $existsCatWise ? $hyphenatedFormattedCatWise : ($checkExistsCatWise ? $underscoreFormattedCatWise : $formattedCatWise);
            }, $columnsCatWise);

            $statusIds = DB::table('status_labels')
                ->whereIn('name', $formattedColumns)
                ->pluck('id', 'name');

            $statusIdsCatWise = DB::table('status_labels')
            ->whereIn('name', $formattedColumnsCatWise)
            ->pluck('id', 'name');

            if ($statusIds->isEmpty() || $statusIdsCatWise->isEmpty()) {
                DB::rollBack();
                $this->error("No status columns found in status_labels table.");
                return;
            }
    
            // $yesterdayRecords = DB::table('asset_summary')->whereDate('date', $yesterday)->get();
    
            // if ($yesterdayRecords->isEmpty()) {
                //$this->info("No records found for $yesterday in asset_summary. Creating record from assets table");
    
                // // Get total devices count
                // $totalDevices = DB::table('assets')->whereNull('deleted_at')->where('company_id', 1)->count();
    
                // Get status counts
                $statusCounts = [];
                $categoryWiseData = [0, 1, 2, 3];
                foreach ($categoryWiseData as $key => $value) {
                    $totalDevices[$value] = 0;
                    $model_id = $categoryId = [];
                    if($value == 1) {
                        // Get total devices count
                        $category = Category::where('name', 'laptop')->first();
                        $id = $category != null ? $category->id : null;
                        $model_id = Model::where('category_id', $id)->pluck('id')->toArray() ?? [];
                    } elseif($value == 2) {
                        // Get total devices count
                        $category = Category::where('name', 'desktop')->first();
                        $id = $category != null ? $category->id : null;
                        $model_id = Model::where('category_id', $id)->pluck('id')->toArray() ?? [];
                    } elseif($value == 3) {  
                        // Get total devices count
                        $categoryId = Category::whereIn('name', ['laptop', 'desktop'])->pluck('id')->toArray() ?? [];
                        $model_id = Model::whereNotIn('category_id', $categoryId)->pluck('id')->toArray() ?? [];
                    }
                    
                    // Get total devices count
                    if($value == 0) {
                        $totalDevices[$value] = DB::table('assets')->whereNull('deleted_at')->where('company_id', 1)->count();
                    } else{   
                        $totalDevices[$value] = DB::table('assets')->whereIn('model_id', $model_id)->whereNull('deleted_at')->where('company_id', 1)->count();
                    }
                    foreach ($statusIds as $column => $statusId) {
                        $column = convertToDbColumn($column);
                        // if($column == "new_added_user"){
                        //     $statusCounts[$column] = DB::table('users')
                        //     ->whereBetween('created_at', ["$today 00:00:00", "$today 23:59:59"])
                        //     ->whereNull('deleted_at')
                        //     ->count();
                        // }elseif($column == "exits_users"){
                        //     $statusCounts[$column] = DB::table('users')
                        //     ->where('last_working_date',$today)
                        //     ->whereNull('deleted_at')
                        //     ->count();
                         
                        // }else{
                            $statusCounts[$column] = DB::table('assets')
                            ->leftJoin("status_labels as s", "assets.status_id", "s.id")
                            ->where('assets.status_id', $statusId)
                            ->where('assets.company_id', 1)
                            ->whereNull('s.deleted_at')
                            ->whereNull('assets.deleted_at')->where('physical', 1);
                            $statusCounts[$column] = (clone $statusCounts[$column]);
                            if($value == 0) {
                                $statusCounts[$column] = $statusCounts[$column]->count();
                            } else {
                                $statusCounts[$column] = $statusCounts[$column]->whereIn('model_id', $model_id)->count();
                            }
                        // }
                    }

                    if($value == 0){
                        DB::table('asset_summary')->updateOrInsert(
                            ['date' => $today],  
                            ['total_devices' => $totalDevices[$value], 'created_at' => now(), 'updated_at' => now()] + $statusCounts
                        );  
                    } else {
                        DB::table('category_wise_asset_summaries')->updateOrInsert(
                            ['date' => $today,'category_wise_id' => $value],  
                            ['total_devices' => $totalDevices[$value], 'category_wise_id' => $value, 'created_at' => now(), 'updated_at' => now()] + $statusCounts,
                        );
                    }
                }
                DB::commit(); 
                $this->info("Asset summary successfully saved for $today.");
            // } 
            // else {
            //     $todayRecords = DB::table('asset_summary')->whereDate('date', $today)->get();
            //     if ($todayRecords->isEmpty()) {
            //         $newRecords = $yesterdayRecords->map(function ($record) use ($columns, $today) {
            //             $data = [
            //                 'date' => $today,
            //                 'total_devices' => $record->total_devices, 
            //                 'created_at' => now(),
            //                 'updated_at' => now(),
            //             ];
            //             foreach ($columns as $column) {
            //                 $data[$column] = $record->$column;
            //             }
            //             return $data;
            //         })->toArray();
        
            //         DB::table('asset_summary')->insert($newRecords);
            //         DB::commit(); 
            //         $this->info("Successfully duplicated " . count($newRecords) . " records for $today.");
            //     }else {
            //         $this->info("Records for $today already exist. No duplication needed.");
            //     }
            // }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("An error occurred: " . $e->getMessage());
        }
    }

}
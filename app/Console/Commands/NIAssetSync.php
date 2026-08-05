<?php

namespace App\Console\Commands;

use App\Helpers\Common as CommonHelper;
use App\Helpers\Ni\AllData;
use App\Models\Assets;
use App\Models\BlockedIP;
use App\Models\Category;
use App\Models\Company;
use App\Models\Component;
use App\Models\Device;
use App\Models\Device\PatchManagementGroup;
use App\Models\Device\PatchManagementGroupDevice;
use App\Models\Location;
use App\Models\Manufacture;
use App\Models\Model;
use App\Models\NetworkInventory\Basic;
use App\Models\NetworkInventory\ChangeCache;
use App\Models\NetworkInventory\ChangeLog;
use App\Models\NetworkInventory\DiskDrive;
use App\Models\NetworkInventory\EnvironmentVariable;
use App\Models\NetworkInventory\UsbPort;
use App\Models\NetworkInventory\OutlookAccount;
use App\Models\NetworkInventory\MappedLocation;
use App\Models\NetworkInventory\Monitor;
use App\Models\NetworkInventory\NetworkAdapter;
use App\Models\NetworkInventory\PhysicalMemory;
use App\Models\NetworkInventory\Product;
use App\Models\NetworkInventory\ScanRegister;
use App\Models\NetworkInventory\SoundDevice;
use App\Models\NetworkInventory\TrackedDevice;
use App\Models\NetworkInventory\UserAccount;
use App\Models\NetworkInventory\VideoController;
use App\Models\NetworkInventory\Volume;
use App\Models\User;
use Illuminate\Console\Command;
use App\Models\NetworkInventory\AgentData;
use App\Models\NetworkInventory\AgentDataProcessed;
use App\Models\NetworkInventory\ItmNetworkInventoryPatchAgentDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use stdClass;
use Log;
use DB;

class NIAssetSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'niAssetSync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync NI Agent data';

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
        $return = ["status" => "fail"];
        $firstSuperUser = User::getFirstSuperUser();
        Auth::loginUsingId($firstSuperUser->id);

        $now = new Carbon(config('app.timezone'));
        $dir_sep = DIRECTORY_SEPARATOR;
        $filePath = date("Y") . $dir_sep . date('m') . $dir_sep.date('d');
        $ni_folder_name = "ni" . $dir_sep . $filePath;
        $ni_incomplete_folder_name = "ni_data_incomplete" . $dir_sep . $filePath;
        $ni_new_serial_and_duplicate_mac = "ni_data_duplicate" . $dir_sep . $filePath;
        $ni_blocked_ip_mac = "ni_blocked_ip_mac" . $dir_sep . $filePath;


        $agentDataObj = AgentData::where('is_processed', 0)->limit(150)->get();
        if(count($agentDataObj) > 0) {
            $this->info("niAssetSync Start");
            foreach ($agentDataObj as $key => $agentData) {
                try {
                    // DB::beginTransaction();
                    // Log::info("Start - " . $agentData->id. " - ".$now);
                    // $this->info($agentData->id. " - ".$now);
                    $filepath = $agentData->file_path;
                    $content = $this->getDataFromLog($filepath);

                    /*if(!isset($content->allData)) {
                        $this->storeProcessedData($agentData);
                        continue;
                    }*/
                    $all = $content;
                    $dataArray = isset($content->allData) ? json_decode($content->allData, true) : null;

                    $allData = isset($content->allData) ? new AllData($content->allData) : null;
                    $dataParams = ["ComputerName","ComputerManufacturer","ComputerModel","ComputerDomain","ComputerWorkgroup","ComputerSystemType","OSCaption","OSManufacturer","OSSerialNumber","OSVersion","OSServicePack","OSArchitecture","OSSystemDrive","OSSystemDirectory","OSKey","ProcessorName","ProcessorManufacturer","ProcessorArchitecture","ProcessorFamily","ProcessorProcessorId","ProcessorNumberOfCores", "ActiveMACAddress", "BIOSSMBIOSBIOSVersion", "BIOSManufacturer", "BIOSReleaseDate", "IPv4", "platform","company","SoftwareLicensingProduct", "ProcessorThreadCount", "ProcessorMaxClockSpeed", "agentVersion", "IPv6", "AgentID", "OSDisplayVersion"];
                    $requestData = [];
                    if($dataArray == null) {
                        $dataArray = $content;
                        // $content = $content;
                        // $this->info("Ubuntu: ". $agentData->id);
                        foreach ($dataParams as $param) {
                            $requestData[$param] = isset($dataArray->$param) ? $dataArray->$param : null;
                        }
                    }
                    $data = $allData ? $allData->getValues($dataParams) : $requestData;
                    $agentID = isset($data['AgentID']) ? $data['AgentID'] : null;
                    unset($data['AgentID']);
                    $data['OSDisplayVersion'] = isset($data['OSDisplayVersion']) ? $data['OSDisplayVersion'] :  null;
                    if(($content->BIOSSerialNumber == null || $content->BIOSSerialNumber == "null" || $content->BIOSSerialNumber == "") && ($content->ActiveMACAddress == null || $content->ActiveMACAddress == "null" || $content->ActiveMACAddress == "")) {
                        // $this->info("Null: ". $agentData->id);
                        $this->storeProcessedData($agentData);
                    }
                    $sanitizedMacAddress = CommonHelper::sanitizeMacAddress($content->ActiveMACAddress);
                    $isInvalidSerial = CommonHelper::isInvalidSerial($content->BIOSSerialNumber);
                    $data["ActiveMACAddress"] = $sanitizedMacAddress;
                    $data["IPv4"] = $content->IPv4;
                    $data["SoftwareLicensingProduct"] = (isset($data['SoftwareLicensingProduct']) && $data['SoftwareLicensingProduct'] != "") ? json_encode($data['SoftwareLicensingProduct']) : NULL;

                    if($data['ComputerName'] == $content->IPv4) {
                        $data['IPv4'] = "";
                    }

                    /* not required to allow if both serial and mac fields are empty */
                    if(($content->BIOSSerialNumber == null || $content->BIOSSerialNumber == "null" || $content->BIOSSerialNumber == "") && ($content->ActiveMACAddress == null || $content->ActiveMACAddress == "null" || $content->ActiveMACAddress == "")) {
                        Log::info("BIOSSerialNumber error");
                        continue;
                    }

                    if(isset($data['IPv4']) && $data['IPv4'] != "") {
                        $ipObj = BlockedIP::select('ip')->where('ip', $content->IPv4)->where('type', 1)->first();
                    }
                    if(isset($content->ActiveMACAddress)) {
                        $macObj = BlockedIP::select('mac')->where('mac', $sanitizedMacAddress)->where('type', 2)->first();
                    }
                    if( !empty($ipObj) || !empty($macObj) ) {
                        $storage_path = storage_path($ni_blocked_ip_mac);
                        if(! is_dir($storage_path)) {
                            Storage::makeDirectory($ni_blocked_ip_mac);
                        }

                        $file_name = strtolower('f' . $now->format('His_') . Str::random(5) . '.txt');
                        $file_stored = Storage::put($ni_blocked_ip_mac . $dir_sep . $file_name, json_encode($all));
                        $return["msg"] = trans('content.device_fields.this_ip_mac_is_already_block');
                        $this->storeProcessedData($agentData);
                        continue;
                    }

                    $exists = [];
                    if($isInvalidSerial == false && $sanitizedMacAddress == "") {
                        // store error log & store file in ni_error folder
                        $storage_path = storage_path($ni_new_serial_and_duplicate_mac);
                        if(! is_dir($storage_path)) {
                            Storage::makeDirectory($ni_new_serial_and_duplicate_mac);
                        }

                        $file_name = strtolower('f' . $now->format('His_') . Str::random(5) . '.txt');
                        $file_stored = Storage::put($ni_new_serial_and_duplicate_mac . $dir_sep . $file_name, json_encode($all));
                        $this->storeProcessedData($agentData);
                        continue;
                    }
                    else if($isInvalidSerial == false && $sanitizedMacAddress != "") {
                        $existsSerialCount = Basic::where("BIOSSerialNumber", "=", $content->BIOSSerialNumber)->whereNull('is_dupe')->count();
                        if($existsSerialCount == 1) {
                            $exists = Basic::where("BIOSSerialNumber", "=", $content->BIOSSerialNumber)->whereNull('is_dupe')->select("id")->get();
                        } else {
                            $existsMacCount = Basic::where("ActiveMACAddress", "=", $sanitizedMacAddress)->where("BIOSSerialNumber", "not like", "itm_%")->whereNull('is_dupe')->count();
                            if($existsMacCount == 1) {
                                $diskDrivesData = $allData ? $allData->DiskDrive : $content->DiskDrive;
                                if($diskDrivesData && count($diskDrivesData) == 1) {
                                    foreach($diskDrivesData as $dd) {
                                        if(! isset($dd["SerialNumber"])) {
                                            $dd["SerialNumber"] = "";
                                        }
                                        if(! isset($dd["Size"])) {
                                            $dd["Size"] = 0;
                                        }

                                        $dd["SerialNumber"] = str_replace("-", "", $dd["SerialNumber"]);
                                        $existsDrive = DiskDrive::where("SerialNumber", "like", $dd["SerialNumber"])->first();
                                        if(!empty($existsDrive)) {
                                            // $changeThere = $existsDrive->isChangeThere($dd);
                                            // if(!$changeThere) {
                                            $exists = Basic::where("ActiveMACAddress", "=", $sanitizedMacAddress)->whereNull('is_dupe')->select("id")->get();
                                            // }
                                        } else {
                                            // store error log & store file in ni_error folder
                                            $storage_path = storage_path($ni_new_serial_and_duplicate_mac);
                                            if(! is_dir($storage_path)) {
                                                Storage::makeDirectory($ni_new_serial_and_duplicate_mac);
                                            }

                                            $file_name = strtolower('f' . $now->format('His_') . Str::random(5) . '.txt');
                                            $file_stored = Storage::put($ni_new_serial_and_duplicate_mac . $dir_sep . $file_name, json_encode($all));
                                            $this->storeProcessedData($agentData);
                                            continue;
                                        }
                                    }
                                }
                            } else if ($existsMacCount > 1 ) {
                                // store error log & store file in ni_error folder
                                $storage_path = storage_path($ni_new_serial_and_duplicate_mac);
                                if(! is_dir($storage_path)) {
                                    Storage::makeDirectory($ni_new_serial_and_duplicate_mac);
                                }

                                $file_name = strtolower('f' . $now->format('His_') . Str::random(5) . '.txt');
                                $file_stored = Storage::put($ni_new_serial_and_duplicate_mac . $dir_sep . $file_name, json_encode($all));
                                $this->storeProcessedData($agentData);
                                continue;
                            }
                        }
                    }
                    elseif( $isInvalidSerial == true && $sanitizedMacAddress != "" &&  $sanitizedMacAddress != null & $sanitizedMacAddress != "null" ) {
                        $existsMacCount = Basic::where("ActiveMACAddress", "=", $sanitizedMacAddress)->where("BIOSSerialNumber", "like", "itm_%")->whereNull('is_dupe')->count();
                        if($existsMacCount == 1) {
                            $exists = Basic::where("ActiveMACAddress", "=", $sanitizedMacAddress)->where("BIOSSerialNumber", "like", "itm_%")->whereNull('is_dupe')->select("id")->get();
                        } else if($existsMacCount > 1) {
                            // store error log & store file in ni_error folder
                            $storage_path = storage_path($ni_new_serial_and_duplicate_mac);
                            if(! is_dir($storage_path)) {
                                Storage::makeDirectory($ni_new_serial_and_duplicate_mac);
                            }

                            $file_name = strtolower('f' . $now->format('His_') . Str::random(5) . '.txt');
                            $file_stored = Storage::put($ni_new_serial_and_duplicate_mac . $dir_sep . $file_name, json_encode($all));
                            $this->storeProcessedData($agentData);
                            continue;
                        } else {
                            $diskDrivesData = $allData ? $allData->DiskDrive : $content->DiskDrive;
                            if($diskDrivesData && count($diskDrivesData) > 0) {
                                foreach($diskDrivesData as $dd) {
                                    if (!isset($dd["SerialNumber"])) {
                                        $dd["SerialNumber"] = "";
                                    }
                                    if (!isset($dd["Size"])) {
                                        $dd["Size"] = 0;
                                    }

                                    $dd["SerialNumber"] = str_replace("-", "", $dd["SerialNumber"]);
                                    $existsDrive = DiskDrive::where("SerialNumber", trim($dd["SerialNumber"]))->orderBy('id', 'asc')->first();
                                    if (!empty($existsDrive)) {
                                        $exists = Basic::where("id", "=", $existsDrive->basic_id)->where("BIOSSerialNumber", "like", "itm_%")->whereNull('is_dupe')->select("id")->get();
                                        if(!empty($exists)) {
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    elseif($isInvalidSerial == true && ($sanitizedMacAddress == "" || $sanitizedMacAddress == null || $sanitizedMacAddress == "null")) {
                        $storage_path = storage_path($ni_new_serial_and_duplicate_mac);
                        if(! is_dir($storage_path)) {
                            Storage::makeDirectory($ni_new_serial_and_duplicate_mac);
                        }

                        $file_name = strtolower('f' . $now->format('His_') . Str::random(5) . '.txt');
                        $file_stored = Storage::put($ni_new_serial_and_duplicate_mac . $dir_sep . $file_name, json_encode($all));
                        $this->storeProcessedData($agentData);
                        continue;
                    }
                    elseif( $isInvalidSerial == false) {
                        $exists = Basic::where("BIOSSerialNumber", "=", $content->BIOSSerialNumber)->whereNull('is_dupe')->select("id")->limit(1)->get();
                    }

                    $basic_id = null;
                    $data['is_AD'] = isset($content->is_AD) ? $content->is_AD : null;
                    if(count($exists)) {
                        $basic_id = $exists[0]->id;
                        $basic = Basic::find($basic_id);
                        if(!empty($basic) && Carbon::parse($basic->updated_at)->isToday() && Str::contains(strtolower($basic->OSManufacturer), ['ubuntu', 'linux'])) {
                            $this->storeProcessedData($agentData);
                            continue;
                        }
                        $asset = Assets::where('serial', $basic->BIOSSerialNumber)->first();
                        // Basic::where("BIOSSerialNumber", "like", $request->BIOSSerialNumber)->update($data);
                        if($isInvalidSerial) {
                            unset($data["BIOSSerialNumber"]);
                        }

                        if(!empty($asset)) {
                            $asset->name = $data['ComputerName'];
                            $asset->ip = $data["IPv4"];
                            $asset->mac = $data["ActiveMACAddress"];
                            $asset->updated_at = date('Y-m-d H:i:s');
                            $asset->save();
                            $data['device_id'] = $asset->id;
                            $data['company'] = (isset($data['company']) && $data['company'] > 0) ? $data['company'] : Company::whereNull('deleted_at')->first()->id;
                            $data['ProcessorThreadCount'] = isset($data['ProcessorThreadCount']) ? $data['ProcessorThreadCount'] : null;
                            $data['ProcessorMaxClockSpeed'] = isset($data['ProcessorMaxClockSpeed']) ? $data['ProcessorMaxClockSpeed'] : null;
                            $basic->update($data);
                            unset($data['ProcessorThreadCount'], $data['ProcessorMaxClockSpeed']);
                        }

                    }

                    /*if(! count($exists) && $isInvalidSerial == true) {
                        try {
                            $exists = NetworkAdapter::where("PhysicalAddress", "like", $sanitizedMacAddress)->select("basic_id")->limit(1)->get();
                            if(count($exists)) {
                                $basic_id = $exists[0]->basic_id;
                                $basic = Basic::find($basic_id);
                                $asset = Assets::where('serial', $content->BIOSSerialNumber)->first();
                                if($isInvalidSerial) {
                                    unset($data["BIOSSerialNumber"]);
                                }

                                if(!empty($asset)) {
                                    $asset->name = $data['ComputerName'];
                                    $asset->save();
                                    $data['device_id'] = $asset->id;
                                    $basic->update($data);
                                }
                            }
                        }
                        catch(\Exception $e) {
                            Log::error("Error at get basic by mac - procedure 2 : " . $sanitizedMacAddress);
                            Log::error($e->getMessage());
                        }
                    }*/

                    if(! $basic_id) {
                        $basic = new Basic;
                        $data['ProcessorThreadCount'] = isset($data['ProcessorThreadCount']) ? $data['ProcessorThreadCount'] : null;
                        $data['ProcessorMaxClockSpeed'] = isset($data['ProcessorMaxClockSpeed']) ? $data['ProcessorMaxClockSpeed'] : null;
                        $basic->fill($data);
                        unset($data['ProcessorThreadCount'], $data['ProcessorMaxClockSpeed']);

                        $basic->BIOSSerialNumber = $isInvalidSerial ? Basic::generateDummySerial() : $content->BIOSSerialNumber;
                        // $basic->BIOSSerialNumber = $request->BIOSSerialNumber;
                        $asset = Assets::where('serial', $basic->BIOSSerialNumber)->first();
                        if(!empty($asset) && $isInvalidSerial == false) {
                            if(isset($data['ComputerModel']) && $data['ComputerModel'] != "") {
                                $modelData = Model::where('id', $asset->model_id)->first();
                                if($modelData != null && $modelData->name != $data['ComputerModel']) {
                                    $modelDataByName = Model::where('name', $data['ComputerModel'])->first();
                                    if($modelDataByName == null) {
                                        $manufactureObj = Manufacture::where('name', $data['ComputerManufacturer'])->first();
                                        if($manufactureObj == null) {
                                            $createManufacture = new Manufacture();
                                            $createManufacture->name = $data['ComputerManufacturer'];
                                            $createManufacture->user_id = 1;
                                            $createManufacture->save();
                                            $manufacture_id = $createManufacture->id;
                                        } else {
                                            $manufacture_id = $manufactureObj->id;
                                        }

                                        $createModel = new Model();
                                        $createModel->name = $data['ComputerModel'];
                                        $createModel->manufacturer_id = $manufacture_id;
                                        $createModel->category_id = $modelData->category_id;
                                        $createModel->user_id = 1;
                                        $createModel->save();
                                        $asset->model_id = $createModel->id;
                                    } else {
                                        $asset->model_id = $modelDataByName->id;
                                    }
                                }
                            }

                            $asset->name = $data['ComputerName'];
                            $asset->save();
                            $basic->device_id = $asset->id;
                        }

                        $basic->save();
                        $basic_id = $basic->id;
                    }

                    $basic->company = (isset($data['company']) && $data['company'] > 0) ? $data['company'] : Company::whereNull('deleted_at')->first()->id;

                    if(isset($data['IPv4']) && $data['IPv4'] && $basic->validate_ip($data['IPv4'])) {
                        $scanRegisterData = [];
                        $scanRegisterData['agent_look_at'] = $now->format('Y-m-d H:i:s');
                        $scanRegisterData['company_id'] = isset($data["company"]) && !empty($data["company"]) ? $data["company"] : Company::whereNull('deleted_at')->first()->id;
                        $scanRegisterData['hostname'] = $data['ComputerName'];
                        $scanRegisterData['os'] = $data['OSCaption'];
                        $scanRegisterData['mac'] = $sanitizedMacAddress;
                        ScanRegister::where('ipv4', 'like', $data['IPv4'])->update($scanRegisterData);
                    }

                    /* collect */
                    $monitors = ($allData && isset($allData->monitors) ) ? $allData->monitors : (isset($content->monitors) ? $content->monitors : []);
                    // Log::error($post_keys);

                    $calc_tot_ram_size = 0;
                    $physicalMemoryChangeDetect = new stdClass();
                    $physicalMemoryChangeDetect->physical_memory_id = [];
                    $physicalMemories = ($allData && isset($allData->PhysicalMemory))? $allData->PhysicalMemory : (isset($content->PhysicalMemory) ? $content->PhysicalMemory : []);
                    if($physicalMemories && count($physicalMemories)) {
                        try{
                            foreach($physicalMemories as $pm) {
                                $pm = (array) $pm;
                                $tmp_capacity = (isset($pm["Capacity"]) && $pm["Capacity"]) ? $pm["Capacity"] : 0;
                                if(! isset($pm["SerialNumber"])) {
                                    $pm["SerialNumber"] = "";
                                }
                                $pm["SerialNumber"] = str_replace("-", "", $pm["SerialNumber"]);
                                $pm["Capacity"] = CommonHelper::byteToGb($tmp_capacity);

                                $exists = PhysicalMemory::where("SerialNumber", $pm["SerialNumber"])->where("DeviceLocator", $pm["DeviceLocator"])->where("Tag", $pm["Tag"])->where("basic_id", "=", $basic_id)->first();
                                if($exists) {
                                    $physicalMemoryChangeDetect->physical_memory_id[] = $exists->id;

                                    $changeThere = $exists->isChangeThere($pm);
                                    if($changeThere) {
                                        /* change cache - cache old data */
                                        $changeCache = $exists->dataForCache();
                                    }

                                    PhysicalMemory::where("SerialNumber", $pm["SerialNumber"])->where("DeviceLocator", $pm["DeviceLocator"])->where("Tag", $pm["Tag"])->where("basic_id", "=", $basic_id)->update($pm);

                                    if($changeThere) {
                                        /* change log - add change detected disk */
                                        $changeLog = (array) $pm;
                                        $changeLog["basic_id"] = $basic_id;
                                        $changeLog["cl_item"] = 2;
                                        $changeLog["cl_type"] = 2;
                                        $changeLog = ChangeLog::create($changeLog);

                                        $changeCache["cl_item"] = 2;
                                        $changeCache["cl_id"] = $changeLog->id;
                                        ChangeCache::create($changeCache);
                                    }
                                }
                                else {
                                    $pmObj = new PhysicalMemory;
                                    $pmObj->basic_id = $basic_id;
                                    $pmObj->fill((array) $pm);
                                    $pmObj->save();

                                    $physicalMemoryChangeDetect->physical_memory_id[] = $pmObj->id;

                                    /* change log - add newly detected disk */
                                    $changeLog = (array) $pm;
                                    $changeLog["basic_id"] = $basic_id;
                                    $changeLog["cl_item"] = 2;
                                    $changeLog["cl_type"] = 1;
                                    ChangeLog::create($changeLog);
                                }

                                $calc_tot_ram_size += $pm["Capacity"];
                            }

                            /* check for missing memory drives */
                            $get_missing_memories = PhysicalMemory::where("basic_id", "=", $basic_id)->whereNotIn("id", $physicalMemoryChangeDetect->physical_memory_id)->get();
                            if(count($get_missing_memories)) {
                                foreach($get_missing_memories as $missing_memory) {
                                    /* change log - missing detected disk */
                                    $changeLog = $missing_memory->dataForCache();
                                    $changeLog["basic_id"] = $basic_id;
                                    $changeLog["cl_item"] = 2;
                                    $changeLog["cl_type"] = 3;
                                    $changeLog = ChangeLog::create($changeLog);
                                }
                                PhysicalMemory::where("basic_id", "=", $basic_id)->whereNotIn("id", $physicalMemoryChangeDetect->physical_memory_id)->delete();
                            }

                            $basic->touch();
                        }
                        catch(\Exception $e) {
                            Log::error($e->getMessage());
                        }
                    }

                    $calc_tot_hdd_size = 0;
                    $diskDriveChangeDetect = new stdClass();
                    $diskDriveChangeDetect->serial = [];

                    $diskDrives = ($allData && isset($allData->DiskDrive))? $allData->DiskDrive : (isset($content->DiskDrive) ? $content->DiskDrive : []);
                    if($diskDrives && count($diskDrives)) {
                        try{
                            foreach($diskDrives as $dd) {
                                $dd = (array) $dd;
                                foreach($dd as $k=>$v) {
                                    $dd[$k] = trim((string) $v);
                                }

                                if(! isset($dd["SerialNumber"])) {
                                    $dd["SerialNumber"] = "";
                                }
                                if(! isset($dd["Size"])) {
                                    $dd["Size"] = 0;
                                }

                                $dd["SerialNumber"] = str_replace("-", "", $dd["SerialNumber"]);
                                $dd["Size"] = CommonHelper::byteToGb($dd["Size"]);
                                $diskDriveChangeDetect->serial[] = $dd["SerialNumber"];

                                $exists = DiskDrive::where("SerialNumber", $dd["SerialNumber"])->where("basic_id", "=", $basic_id)->first();
                                if($exists) {
                                    $changeThere = $exists->isChangeThere($dd);
                                    if($changeThere) {
                                        /* change cache - cache old data */
                                        $changeCache = $exists->dataForCache();
                                    }

                                    DiskDrive::where("SerialNumber", $dd["SerialNumber"])->where("basic_id", "=", $basic_id)->update($dd);

                                    if($changeThere) {
                                        /* change log - add change detected disk */
                                        $changeLog = (array) $dd;
                                        $changeLog["basic_id"] = $basic_id;
                                        $changeLog["cl_item"] = 1;
                                        $changeLog["cl_type"] = 2;
                                        $changeLog = ChangeLog::create($changeLog);

                                        $changeCache["cl_item"] = 1;
                                        $changeCache["cl_id"] = $changeLog->id;
                                        ChangeCache::create($changeCache);
                                    }
                                }
                                else {
                                    $ddObj = new DiskDrive;
                                    $ddObj->basic_id = $basic_id;
                                    $ddObj->fill((array) $dd);
                                    $ddObj->save();

                                    /* change log - add newly detected disk */
                                    $changeLog = (array) $dd;
                                    $changeLog["basic_id"] = $basic_id;
                                    $changeLog["cl_item"] = 1;
                                    $changeLog["cl_type"] = 1;
                                    ChangeLog::create($changeLog);
                                }
                                $calc_tot_hdd_size += $dd["Size"];
                            }

                            /* check for missing disk drives */
                            $get_missing_disks = DiskDrive::where("basic_id", "=", $basic_id)->whereNotIn("SerialNumber", $diskDriveChangeDetect->serial)->get();
                            if(count($get_missing_disks)) {
                                foreach($get_missing_disks as $missing_disk) {
                                    /* change log - missing detected disk */
                                    $changeLog = $missing_disk->dataForCache();
                                    $changeLog["basic_id"] = $basic_id;
                                    $changeLog["cl_item"] = 1;
                                    $changeLog["cl_type"] = 3;
                                    $changeLog = ChangeLog::create($changeLog);
                                }
                                DiskDrive::where("basic_id", "=", $basic_id)->whereNotIn("SerialNumber", $diskDriveChangeDetect->serial)->delete();
                            }

                            $basic->touch();
                        }
                        catch(\Exception $e) {
                            Log::error($e->getMessage());
                        }
                    }

                    $volumes = ($allData && isset($allData->Volume))? $allData->Volume : (isset($content->Volume) ? $content->Volume : []);
                    if($volumes && count($volumes)) {
                        try{
                            foreach($volumes as $volume) {
                                $volume = (array) $volume;
                                foreach($volume as $k=>$v) {
                                    $volume[$k] = trim((string) $v);
                                }

                                try {
                                    $volume["AvailableFreeSpace"] = isset($volume["AvailableFreeSpace"]) ? CommonHelper::byteToGb($volume["AvailableFreeSpace"]) : 0;
                                    $volume["TotalSize"] = isset($volume["TotalSize"]) ? CommonHelper::byteToGb($volume["TotalSize"]) : 0;
                                }
                                catch(\Exception $e) {
                                    $volume["AvailableFreeSpace"] = 0;
                                    $volume["TotalSize"] = 0;
                                }

                                $exists = Volume::where("Name", $volume["Name"])->where("basic_id", "=", $basic_id)->count();
                                if($exists) {
                                    Volume::where("Name", $volume["Name"])->where("basic_id", "=", $basic_id)->update($volume);
                                }
                                else {
                                    $volumeObj = new Volume;
                                    $volumeObj->basic_id = $basic_id;
                                    $volumeObj->fill((array) $volume);
                                    $volumeObj->save();
                                }
                            }
                            $basic->touch();
                        }
                        catch(\Exception $e) {
                            Log::error($e->getMessage());
                        }
                    }

                    if($monitors && count($monitors)) {
                        try {
                            foreach($monitors as $monitor) {
                                $monitor = (array) $monitor;
                                foreach($monitor as $k=>$v) {
                                    $monitor[$k] = trim((string) $v);
                                }

                                $existing_serial = 'SerialNumber';

                                if(!$monitor[$existing_serial] || $monitor[$existing_serial] == '0') {
                                    continue;
                                }

                                $monitor_serial = $monitor[$existing_serial];
                                $monitor_data = [];
                                $monitor_data['basic_id'] = $basic_id;
                                $monitor_data['monitor_name'] = $monitor['MonitorName'];

                                unset($monitor[$existing_serial]);
                                unset($monitor["MonitorName"]);
                                $monitor_data['others'] = json_encode($monitor);

                                $monitor_obj = Monitor::updateOrCreate(['serial' => $monitor_serial], $monitor_data);

                                if( $monitor_obj && !$monitor_obj->component_id ) {
                                    $component = Component::where('serial', 'like', $monitor_serial)->first();
                                    if($component) {
                                        $monitor_obj->component_id = $component->id;
                                        $monitor_obj->save();

                                        $component->monitor_id = $monitor_obj->id;
                                        $component->save();
                                    }
                                    else {
                                        $component = new Component();
                                        $component->category_id = Category::getMonitor()->id;
                                        $component->status = 1;
                                        $component->company_id = 1;
                                        $component->location_id = null;
                                        $component->serial = $monitor_obj->serial;
                                        $component->name = $monitor_obj->monitor_name;
                                        $component->monitor_id = $monitor_obj->id;
                                        $component->origin_from = 4; // Via Network
                                        $component->save();
                                        $component->generateUniqueTag();

                                        $monitor_obj->component_id = $component->id;
                                        $monitor_obj->save();
                                    }
                                }
                            }
                        }
                        catch(\Exception $e) {
                            Log::error("AgentData Monitor: " . $e->getMessage());
                        }
                    }

                    $softwareChangeDetect = new stdClass();
                    $softwareChangeDetect->captions = [];
                    $softwares = [];
                    if(isset($content->pkg)) {
                        $parse_pkg = json_decode($content->pkg);
                        foreach($parse_pkg as $p) {
                            $softwares[] = (array) $p;
                        }
                    }
                    else {
                        $softwares = ($allData && isset($allData->InstalledSoftware))? $allData->InstalledSoftware : (isset($content->InstalledSoftware) ? $content->InstalledSoftware : []);
                    }

                    if($softwares && count($softwares)) {
                        try {
                            foreach($softwares as $s) {
                                $s = (array) $s;
                                try {
                                    foreach($s as $k=>$v) {
                                        $s[$k] = trim((string) $v);
                                    }

                                    if( !isset($s["Caption"]) || $s["Caption"] == "" || $s["Caption"] == null || $s["Caption"] == "null") {
                                        continue;
                                    }
                                    $softwareChangeDetect->captions[] = $s["Caption"];

                                    try {
                                        $exists = Product::where("Caption", $s["Caption"])->where("basic_id", "=", $basic_id)->first();
                                    }
                                    catch(\Exception $e) {
                                        continue;
                                    }
                                    if(! $exists) {
                                        $proData = (array) $s;
                                        $proData["InstalledDate"] = isset($s['installDate']) && $s['installDate'] ? CommonHelper::getDateAs($s['installDate'], "Y-m-d H:i:s", "Ymd") : null;
                                        unset($proData["installDate"]);

                                        $productObj = new Product;
                                        $productObj->basic_id = $basic_id;
                                        $productObj->InstalledDate = isset($s['installDate']) && $s['installDate'] ? CommonHelper::getDateAs($s['installDate'], "Y-m-d H:i:s", "Ymd") : null;
                                        unset($s['installDate']);
                                        $productObj->fill((array) $s);
                                        $productObj->save();

                                        /* change log - add newly detected license */
                                        $changeLog = $proData;
                                        $changeLog["basic_id"] = $basic_id;
                                        $changeLog["cl_item"] = 3;
                                        $changeLog["cl_type"] = 1;
                                        ChangeLog::create($changeLog);
                                    }
                                    else {
                                        $proData = (array) $s;
                                        $proData["InstalledDate"] = isset($s['installDate']) && $s['installDate'] ? CommonHelper::getDateAs($s['installDate'], "Y-m-d H:i:s", "Ymd") : null;
                                        unset($proData['installDate']);

                                        $changeThere = $exists->isChangeThere($proData);
                                        if($changeThere) {
                                            /* change cache - cache old data */
                                            $changeCache = $exists->dataForCache();
                                        }

                                        // Product::where("Caption", "like", $s["Caption"])->where("basic_id", "=", $basic_id)->update($proData);
                                        $existProduct = Product::where("Caption", $s["Caption"])->where("basic_id", "=", $basic_id)->first();
                                        if(!empty($existProduct)) {
                                            $existProduct->fill((array) $proData);
                                            $existProduct->save();
                                        }

                                        if($changeThere) {
                                            /* change log - add change detected license */
                                            $changeLog = $proData;
                                            $changeLog["basic_id"] = $basic_id;
                                            $changeLog["cl_item"] = 3;
                                            $changeLog["cl_type"] = 2;
                                            $changeLog = ChangeLog::create($changeLog);

                                            $changeCache["cl_item"] = 3;
                                            $changeCache["cl_id"] = $changeLog->id;
                                            ChangeCache::create($changeCache);
                                        }
                                    }
                                }
                                catch(\Exception $e) {
                                    Log::error($e->getMessage());
                                }
                            }

                            /* check for missing softwares */
                            $get_missing_softwares = Product::where("basic_id", "=", $basic_id)->whereNotIn("Caption", $softwareChangeDetect->captions)->get();
                            if(count($get_missing_softwares)) {
                                foreach($get_missing_softwares as $missing_sw) {
                                    try {
                                        /* change log - missing software */
                                        $changeLog = $missing_sw->dataForCache();
                                        $changeLog["basic_id"] = $basic_id;
                                        $changeLog["cl_item"] = 3;
                                        $changeLog["cl_type"] = 3;
                                        $changeLog = ChangeLog::create($changeLog);
                                    }
                                    catch(\Exception $e) {
                                        Log::error($e->getMessage());
                                    }
                                }
                                Product::where("basic_id", "=", $basic_id)->whereNotIn("Caption", $softwareChangeDetect->captions)->delete();
                            }

                            $basic->touch();
                        }
                        catch(\Exception $e) {
                            Log::error($e->getMessage());
                        }
                    }

                    $videoControllers = ($allData && isset($allData->VideoController))? $allData->VideoController : (isset($content->VideoController) ? $content->VideoController : []);
                    if($videoControllers && count($videoControllers)) {
                        try{
                            foreach($videoControllers as $s) {
                                $s = (array) $s;
                                foreach($s as $k=>$v) {
                                    $s[$k] = trim((string) $v);
                                }
                                $exists = VideoController::where("Caption", "like", $s["Caption"])->where("DriverVersion", "like", $s["DriverVersion"])->where("basic_id", "=", $basic_id)->first();
                                if(!empty($exists)) {
                                    $exists->fill((array) $s);
                                    $exists->save();
                                    //VideoController::where("Caption", "like", $s["Caption"])->where("DriverVersion", "like", $s["DriverVersion"])->where("basic_id", "=", $basic_id)->update($s);
                                }
                                else {
                                    $videoObj = new VideoController;
                                    $videoObj->basic_id = $basic_id;
                                    $videoObj->fill((array) $s);
                                    $videoObj->save();
                                }
                            }
                            $basic->touch();
                        }
                        catch(\Exception $e) {
                            Log::error($e->getMessage());
                        }
                    }

                    $soundDevices = ($allData && isset($allData->SoundDevice))? $allData->SoundDevice : (isset($content->SoundDevice) ? $content->SoundDevice : []);
                    if($soundDevices && count($soundDevices)) {
                        try{
                            foreach($soundDevices as $s) {
                                $s = (array) $s;
                                foreach($s as $k=>$v) {
                                    $s[$k] = trim((string) $v);
                                }
                                $exists = SoundDevice::where("Name", "like", $s["Name"])->where("basic_id", "=", $basic_id)->count();
                                if($exists) {
                                    SoundDevice::where("Name", "like", $s["Name"])->where("basic_id", "=", $basic_id)->update($s);
                                }
                                else {
                                    $soundObj = new SoundDevice;
                                    $soundObj->basic_id = $basic_id;
                                    $soundObj->fill((array) $s);
                                    $soundObj->save();
                                }
                            }
                            $basic->touch();
                        }
                        catch(\Exception $e) {
                            Log::error($e->getMessage());
                        }
                    }

                    $userAccounts = ($allData && isset($allData->UserAccount))? $allData->UserAccount : (isset($content->UserAccount) ? $content->UserAccount : []);
                    if($userAccounts && count($userAccounts)) {
                        try {
                            foreach($userAccounts as $ua) {
                                $ua = (array) $ua;
                                try {
                                    if(! isset($ua["SID"])) {
                                        continue;
                                    }

                                    foreach($ua as $k=>$v) {
                                        $ua[$k] = trim((string) $v);
                                    }

                                    $exists = UserAccount::where("basic_id", "=", $basic_id)->where("SID", "like", $ua["SID"])->first();
                                    if(! $exists) {
                                        $uaData = (array) $ua;
                                        $ua['Name'] = CommonHelper::sanitizeUtf8($ua['Name']);
                                        $ua["LastLogon"] = isset($uaData['LastLogon']) && $uaData['LastLogon'] ? CommonHelper::getDateAs(explode(".", $uaData['LastLogon'])[0], "Y-m-d H:i:s", "YmdHis") : null;

                                        $uaObj = new UserAccount();
                                        $uaObj->basic_id = $basic_id;
                                        $uaObj->fill((array) $ua);
                                        $uaObj->save();
                                    }
                                    else {
                                        $uaData = (array) $ua;
                                        $ua['Name'] = CommonHelper::sanitizeUtf8($ua['Name']);
                                        $ua["LastLogon"] = isset($uaData['LastLogon']) && $uaData['LastLogon'] ? CommonHelper::getDateAs(explode(".", $uaData['LastLogon'])[0], "Y-m-d H:i:s", "YmdHis") : null;

                                        UserAccount::where("basic_id", "=", $basic_id)->where("SID", "like", $ua["SID"])->update($ua);
                                    }
                                }
                                catch(\Exception $e) {
                                    Log::error($e->getMessage());
                                }
                            }
                            $basic->touch();
                        }
                        catch(\Exception $e) {
                            Log::error($e->getMessage());
                        }
                    }

                    /* network adapters of a system */
                    $networkAdapters = ($allData && isset($allData->nw_adapters))? $allData->nw_adapters : (isset($content->nw_adapters) ? $content->nw_adapters : []);
                    if($networkAdapters && count($networkAdapters)) {
                        try {
                            foreach($networkAdapters as $nwa) {
                                $nwa = (array) $nwa;
                                $nwa["PhysicalAddress"] = CommonHelper::sanitizeMacAddress($nwa["PhysicalAddress"]);
                                if(! $nwa["PhysicalAddress"]) {
                                    continue;
                                }

                                foreach($nwa as $k=>$v) {
                                    $nwa[$k] = trim((string) $v);
                                }

                                $exists = NetworkAdapter::where("basic_id", "=", $basic_id)->where("PhysicalAddress", "like", $nwa["PhysicalAddress"])->first();
                                if(! $exists) {
                                    $nwaObj = new NetworkAdapter();
                                    $nwaObj->basic_id = $basic_id;
                                    $nwaObj->fill((array) $nwa);
                                    $nwaObj->save();
                                }
                                else {
                                    NetworkAdapter::where("basic_id", "=", $basic_id)->where("PhysicalAddress", "like", $nwa["PhysicalAddress"])->update($nwa);
                                }
                            }
                            $basic->touch();
                        }
                        catch(\Exception $e) {
                            Log::error($e->getMessage());
                        }
                    }

                    /*environment variables*/
                    $environmentVariables = new stdClass();
                    $environmentVariables->Name = [];
                    $envVariables = ($allData && isset($allData->environmentVariables))? $allData->environmentVariables : (isset($content->environmentVariables) ? $content->environmentVariables : []);
                    if($envVariables && count($envVariables)) {
                        try {
                            foreach($envVariables as $e) {
                                $e = (array) $e;
                                try {
                                    foreach($e as $k=>$v) {
                                        $e[$k] = trim((string) $v);
                                    }
                                    $environmentVariables->Name[] = $e["Name"];
                                    $exists = EnvironmentVariable::where("Name", $e["Name"])->where("basic_id", $basic_id)->first();
                                    if (!$exists) {
                                        $envObj = new EnvironmentVariable;
                                        $envObj->basic_id = $basic_id;
                                        $e["Value"] = isset($e['Value']) && $e['Value'] ? json_encode($e['Value']) : null;
                                        $envObj->fill((array) $e);
                                        $envObj->save();
                                    } else {
                                        $existEnv = EnvironmentVariable::where("Name", $e["Name"])->where("basic_id", $basic_id)->first();
                                        $e["Value"] = isset($e['Value']) && $e['Value'] ? json_encode($e['Value']) : null;
                                        $existEnv->fill((array) $e);
                                        $existEnv->save();
                                    }
                                } catch(\Exception $e) {
                                    Log::error("envVariables: " . $e->getMessage());
                                }
                            }

                            /* check for missing envVariables */
                            $get_missing_envs = EnvironmentVariable::where("basic_id", $basic_id)->whereNotIn("Name", $environmentVariables->Name)->get();
                            if (count($get_missing_envs)) {
                                EnvironmentVariable::where("basic_id", $basic_id)->whereNotIn("Name", $environmentVariables->Name)->delete();
                            }

                            $basic->touch();
                        } catch(\Exception $e) {
                            Log::error($e->getMessage());
                        }
                    }
                    /*end environment variables*/

                    /*usb port*/
                    $usbPorts = ($allData && isset($allData->UsbPorts))? $allData->UsbPorts : (isset($content->UsbPorts) ? $content->UsbPorts : []);
                    if($usbPorts && count($usbPorts)) {
                        try {
                            foreach($usbPorts as $u) {
                                $u = (array) $u;
                                try {
                                    foreach($u as $k=>$v) {
                                        $u[$k] = trim((string) $v);
                                    }
                                    $exists = UsbPort::where("DeviceName", $u["DeviceName"])->where("basic_id", $basic_id)->first();
                                    if (empty($exists)) {
                                        $usbObj = new UsbPort;
                                        $usbObj->basic_id = $basic_id;
                                        $usbObj->fill((array) $u);
                                        $usbObj->save();
                                    } else {
                                        $existUsb = UsbPort::where("DeviceName", $u["DeviceName"])->where("basic_id", $basic_id)->update($u);
                                    }
                                } catch(\Exception $e) {
                                    Log::error("usbPort: " . $e->getMessage());
                                }
                            }
                            $basic->touch();
                        } catch(\Exception $e) {
                            Log::error($e->getMessage());
                        }
                    }
                    /*end usb port*/

                    /*outlook account*/
                    $outlookAccounts = ($allData && isset($allData->PstFileDetails))? $allData->PstFileDetails : (isset($content->PstFileDetails) ? $content->PstFileDetails : []);
                    if($outlookAccounts && count($outlookAccounts)) {
                        try {
                            foreach($outlookAccounts as $o) {
                                $o = (array) $o;
                                try {
                                    foreach($o as $k=>$v) {
                                        $o[$k] = CommonHelper::sanitizeUtf8(trim((string) $v));
                                    }
                                    $exists = OutlookAccount::where("Name", $o["Name"])->where("basic_id", $basic_id)->first();
                                    if (empty($exists)) {
                                        $outlookObj = new OutlookAccount;
                                        $outlookObj->basic_id = $basic_id;
                                        $outlookObj->fill((array) $o);
                                        $outlookObj->save();
                                    } else {
                                        $existOutlook = OutlookAccount::where("Name", $o["Name"])->where("basic_id", $basic_id)->update($o);
                                    }
                                } catch(\Exception $e) {
                                    Log::error("outlookAccount: " . $e->getMessage());
                                }
                            }
                            $basic->touch();
                        } catch(\Exception $e) {
                            Log::error($e->getMessage());
                        }
                    }
                    /*end outlook account*/
                    /* update hdd and ram size */
                    $basic->HddSize = $calc_tot_hdd_size ? round($calc_tot_hdd_size, 2) : $calc_tot_hdd_size;
                    $basic->RamSize = $calc_tot_ram_size ? round($calc_tot_ram_size,2) : $calc_tot_ram_size;
                    $basic->save();

                    if (!empty($basic)) {
                        if(isset($agentID)) {
                            ItmNetworkInventoryPatchAgentDetail::updateOrCreate(
                                [
                                    'basic_id' => $basic->id,
                                ],
                                [
                                    'agent_custom_id' => $agentID,
                                    'updated_at' => now(),
                                    'created_at' => now(),
                                ]
                            );
                        }
                    }

                    /* check for device already exists */
                    try {
                        // if($basic->IPv4 && $basic->validate_ip($basic->IPv4)) {
                        if(isset($data['IPv4']) && $data['IPv4'] && $basic->validate_ip($data['IPv4'])) {
                            $get_existing_device = Device::where('serial', 'like', $basic->BIOSSerialNumber)->get();
                            if($get_existing_device && count($get_existing_device)) {
                                $existing_device = $get_existing_device[0];
                                $existing_device->ip = $data['IPv4'];
                                $existing_device->mac = $basic->ActiveMACAddress;

                                $mapped_locs = MappedLocation::get();
                                if(count($mapped_locs)) {
                                    foreach($mapped_locs as $ml) {
                                        if($ml->check_ip_in_range($data['IPv4'])) {
                                            $td = [
                                                "device_id" => $existing_device->id,
                                                "map_loc_id" => $ml->id,
                                                "ip" => $data['IPv4'],
                                                "location_id" => $ml->location_id,
                                                "place_id" => $ml->place_id
                                            ];
                                            $existing_device->ni_detected_location = $ml->location_id;
                                            $locData = Location::where('id', $ml->location_id)->first();
                                            if($locData != null && $existing_device->company_id != $locData->company_id) {  
                                                $existing_device->company_id = $locData->company_id;
                                                if($existing_device->rtd_location_id == null && $existing_device->internal_place_id == null){
                                                    $existing_device->rtd_location_id == $ml->location_id;
                                                    $existing_device->internal_place_id == $ml->place_id;
                                                }
                                            }
                                            TrackedDevice::create($td);
                                            break;
                                        }
                                    }
                                }

                                $existing_device->save();
                            }
                        }
                    }
                    catch(\Exception $e) {
                        Log::error($e->getMessage());
                    }
                    if (isset($basic->device_id) && config("services.patch_management.enabled") == 1) {
                        $getGroups = PatchManagementGroup::where('auto_added_new_device', 1)->select('id', 'auto_added_new_device')->get();
                        foreach ($getGroups as $key => $getGroup) {
                            // Add Devices to the Group
                            $exitsGroupDevice = PatchManagementGroupDevice::where('group_id', $getGroup->id)->where('device_id', $basic->device_id)->first();
                            if($exitsGroupDevice  == null) {
                                PatchManagementGroupDevice::insert([
                                    'group_id' => $getGroup->id,
                                    'device_id' => $basic->device_id,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);                                
                            }
                        }
                    }
                    // DB::commit();
                    $this->storeProcessedData($agentData, 1);
                    $basic->touch();
                } catch (\Exception $e) {
                    // DB::rollback();
                    Log::error($e->getMessage());
                    $this->storeProcessedData($agentData);
                }
            }
            $this->info("niAssetSync Successfully");
        }
    }

    public function storeProcessedData(AgentData $agentData, $is_process=null) {
        $now = new Carbon(config('app.timezone'));
        $agentDataProcessed = new AgentDataProcessed();
        $agentDataProcessed->id = $agentData->id;
        $agentDataProcessed->company_id = $agentData->company_id;
        $agentDataProcessed->ipv4 = $agentData->ipv4;
        $agentDataProcessed->BIOSSerialNumber = $agentData->BIOSSerialNumber;
        $agentDataProcessed->ActiveMACAddress = $agentData->ActiveMACAddress;
        $agentDataProcessed->ComputerName = $agentData->ComputerName;
        $agentDataProcessed->file_path = $agentData->file_path;
        $agentDataProcessed->is_processed = !empty($is_process) ? 1 : 0;
        $agentDataProcessed->processed_at = $now->format('Y-m-d H:i:s');
        $agentDataProcessed->protocol = $agentData->protocol;
        $agentDataProcessed->agentVersion = $agentData->agentVersion;
        $agentDataProcessed->created_at = $agentData->created_at;
        $agentDataProcessed->updated_at = $agentData->updated_at;
        $agentDataProcessed->save();
        $agentData->delete();
        // Log::info("End - " . $agentDataProcessed->id. " - ".$now);
    }

    public function getDataFromLog($file_path) {
        $return = [];

        if(! $file_path) {
            return $return;
        }

        $raw_content = Storage::get($file_path);

        if(! $raw_content) {
            return $return;
        }

        try {
            $return = json_decode($raw_content);
            return $return;
        }
        catch(\Exception $e) {
            return $return;
        }
    }
}

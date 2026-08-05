<?php

namespace App\Console\Commands;

use App\Helpers\Common;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\OutlookTrait;
use App\Models\NetworkDevice;
use Illuminate\Support\Carbon;

class LoadAzureDevices extends Command
{
    use OutlookTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loadAzureDevices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load azure devices to db';

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
        if (config("services.azure.client_id") == "") {
            return;
        }
        try {
            DB::beginTransaction();
            $devices = $this->getUserDevices(null);
            $this->saveAzureDeviceData($devices);
            if(!empty($devices['@odata.nextLink']) && $devices['@odata.nextLink'] != ''){
                $nextPageFound = true;
                while($nextPageFound){
                    $devices = $this->getUserDevices($devices['@odata.nextLink']);
                    $this->saveAzureDeviceData($devices);
                    if(!isset($devices['@odata.nextLink'])){
                        $nextPageFound = false;
                    }
                }
            }
            DB::commit();
            $this->info("Success");
            return true;
        }catch(Exception $e){
            DB::rollBack();
            Log::error("LoadAzureDevices() : " . $e->getMessage());
            return false;
        }
    }

    public function saveAzureDeviceData($devices)
    {
        try{
            DB::beginTransaction();
            if(!empty($devices['value'])) {
                foreach($devices['value'] as $d) {
                    $data = [
                        'BIOSSerialNumber' => $d['serialNumber'], 
                        'ComputerName' => $d['deviceName'] , 
                        'ComputerManufacturer' => $d['manufacturer'], 
                        'ComputerModel' => $d['model'], 
                        'ComputerDomain' => null, 
                        'ComputerWorkgroup' => $d['managedDeviceOwnerType'], 
                        'ComputerSystemType' => null, 
                        'OSCaption' => $d['operatingSystem'] , 
                        'OSManufacturer' => null, 
                        'OSSerialNumber' => null, 
                        'OSKey' => null, 
                        'OSVersion' => $d['osVersion'], 
                        'OSServicePack' => null, 
                        'OSArchitecture' => null, 
                        'OSSystemDrive' => null, 
                        'OSSystemDirectory' => null, 
                        'ProcessorName' => null, 
                        'ProcessorManufacturer' => null, 
                        'ProcessorArchitecture' => null, 
                        'ProcessorFamily' => null, 
                        'ProcessorProcessorId' => null, 
                        'ProcessorNumberOfCores' => null, 
                        'IPv4' => null, 
                        'ActiveMACAddress' => $d['wiFiMacAddress'] != null ? $d['wiFiMacAddress'] : $d['ethernetMacAddress'],
                        'BIOSSMBIOSBIOSVersion' => null, 
                        'BIOSManufacturer' => null, 
                        'BIOSReleaseDate' => null, 
                        'ProcessorThreadCount' => null, 
                        'ProcessorMaxClockSpeed' => null, 
                        'HddSize' => Common::byteToGb($d['totalStorageSpaceInBytes']), 
                        'RamSize' => Common::byteToGb($d['physicalMemoryInBytes']), 
                        'platform' => null, 
                        'company' => null, 
                        'oui' => null, 
                        'description' => null, 
                        'productclass' => null, 
                        'managedDeviceOwnerType' => $d['managedDeviceOwnerType'],
                        'enrolledDateTime'=> !empty($d['enrolledDateTime']) ? Carbon::createFromFormat('Y-m-d\TH:i:s\Z', $d['enrolledDateTime'])->format("Y-m-d H:i:s") : null, 
                        'lastSyncDateTime'=> !empty($d['lastSyncDateTime']) ? Carbon::createFromFormat('Y-m-d\TH:i:s\Z', $d['lastSyncDateTime'])->format("Y-m-d H:i:s") : null, 
                        'complianceState'=> $d['complianceState'], 
                        'jailBroken'  =>  $d['jailBroken'],  
                        'managementAgent'  =>  $d['managementAgent'],  
                        'easActivated'  =>  $d['easActivated'],  
                        'easDeviceId'  =>  $d['easDeviceId'], 
                        'easActivationDateTime'  =>  !empty($d['easActivationDateTime']) ? Carbon::createFromFormat('Y-m-d\TH:i:s\Z', $d['easActivationDateTime'])->format("Y-m-d H:i:s") : null, 
                        'azureADRegistered'  =>  $d['azureADRegistered'], 
                        'deviceEnrollmentType'  =>  $d['deviceEnrollmentType'], 
                        'activationLockBypassCode'  =>  $d['activationLockBypassCode'],  
                        'emailAddress'  =>  $d['emailAddress'],  
                        'azureADDeviceId'  =>  $d['azureADDeviceId'],  
                        'deviceRegistrationState'  =>  $d['deviceRegistrationState'],  
                        'deviceCategoryDisplayName'  =>  $d['deviceCategoryDisplayName'],  
                        'isSupervised'  =>  $d['isSupervised'],  
                        'exchangeLastSuccessfulSyncDateTime'  =>  !empty($d['exchangeLastSuccessfulSyncDateTime']) ? Carbon::createFromFormat('Y-m-d\TH:i:s\Z', $d['exchangeLastSuccessfulSyncDateTime'])->format("Y-m-d H:i:s") : null,  
                        'exchangeAccessState'  =>  $d['exchangeAccessState'],  
                        'exchangeAccessStateReason'  =>  $d['exchangeAccessStateReason'],  
                        'remoteAssistanceSessionUrl'  =>  $d['remoteAssistanceSessionUrl'],  
                        'remoteAssistanceSessionErrorDetails'  =>  $d['remoteAssistanceSessionErrorDetails'],  
                        'isEncrypted'  =>  $d['isEncrypted'],  
                        'userPrincipalName'  =>  $d['userPrincipalName'],  
                        'imei'  =>  $d['imei'],  
                        'complianceGracePeriodExpirationDateTime'  => !empty($d['complianceGracePeriodExpirationDateTime']) ? Carbon::createFromFormat('Y-m-d\TH:i:s\Z', $d['complianceGracePeriodExpirationDateTime'])->format("Y-m-d H:i:s") : null,  
                        'phoneNumber'  =>  $d['phoneNumber'],  
                        'androidSecurityPatchLevel'  =>  $d['androidSecurityPatchLevel'],  
                        'userDisplayName'  =>  $d['userDisplayName'],  
                        'deviceHealthAttestationState'  =>  $d['deviceHealthAttestationState'],  
                        'subscriberCarrier'  =>  $d['subscriberCarrier'],  
                        'meid'  =>  $d['meid'],  
                        'partnerReportedThreatState'  =>  $d['partnerReportedThreatState'],  
                        'notes' =>  $d['notes'], 
                    ];
                    $existCheck = NetworkDevice::where('BIOSSerialNumber',$d['serialNumber'])->first();
                    if($existCheck && $existCheck->count() > 0){
                        $device = NetworkDevice::find($existCheck->id);
                    }else{
                        $device = new NetworkDevice();
                    }
                    $device->fill($data);
                    if(!$device->save()){
                        Log::error("LoadAzureDevices() : Unable to add device");
                    }
                    // Log::error("LoadAzureDevices() : added device - ".$device->id);
                }
            }
            DB::commit();
            return true;
        }catch(Exception $e){
            DB::rollBack();
            Log::error("LoadAzureDevices() : " . $e->getMessage());
            return false;
        }
    }
}

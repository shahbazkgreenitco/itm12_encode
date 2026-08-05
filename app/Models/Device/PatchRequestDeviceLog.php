<?php

namespace App\Models\Device;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatchRequestDeviceLog extends Model
{
    use HasFactory;
    protected $table = "patch_request_device_logs";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];

    //statuses
    /**
     * Initiated - 1
     * Installing - 2
     * Incomplete - 3
     * Completed - 4
     * 
     */

    public static $statusArray = [
        '1' => 'Initiated',
        '2' => 'Processing',
        '3' => 'Incomplete',
        '4' => 'Completed',
    ];

    public static function getDeviceCurrentStatusTag($deviceId, $patchRequestId) {
        $obj = PatchRequestDeviceLog::where([
            'device_id' => $deviceId,
            'patch_request_id' => $patchRequestId,
        ])->orderBy('id', 'desc')->first();
        if(!empty($obj)) {
            return PatchRequestDeviceLog::$statusArray[$obj->status_id];
        }
        return 'Not Initiated';
    }

    public static function getDeviceCurrentStatus($deviceId, $patchRequestId) {
        $obj = PatchRequestDeviceLog::where([
            'device_id' => $deviceId,
            'patch_request_id' => $patchRequestId,
        ])->orderBy('id', 'desc')->first();
        if(!empty($obj)) {
            return $obj->status_id;
        }
        return null;
    }

}

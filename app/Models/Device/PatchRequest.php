<?php

namespace App\Models\Device;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatchRequest extends Model
{
    use HasFactory;
    protected $table = "patch_requests";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];



    public static function patchRequestCompleteStatus($deviceId, $patchRequestId) {
        $completedStatus = PatchRequestDeviceLog::where([
            'device_id' => $deviceId,
            'patch_request_id' => $patchRequestId,
            'status_id' => 4,
        ])->count();
        return $completedStatus == 0 ? false : true;
    }



}

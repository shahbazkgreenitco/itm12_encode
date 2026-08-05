<?php

namespace App\Models;

use App\Helpers\Common as CommonHelper;
use App\Models\Base;
use App\Models\DeviceBillingCheckinCheckout;
use App\Models\InteractCache;
use App\Models\InteractRecord;
use App\Models\Settings;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use StdClass;

class Actionlog extends Base
{
    protected $table = 'asset_logs';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = [];

    use SoftDeletes;

    public $interact_type_opts = ['i1' => 'New Add', 'i2' => 'Changes', 'i3' => 'Delete', 'i4' => 'Checkout', 'i5' => 'Checkin', 'i6' => 'Restore', 'i7' => 'Sold', 'i8' => 'Restore Disposed Asset', 'i9' => 'Transfer', 'i10' => 'Scrap', 'i11' => 'Revert Scrap'];
    public $interact_module_opts = ['m1' => 'Device', 'm2' => 'Accessory', 'm3' => 'License', 'm4' => 'Consumable'];

    const ACCEPTED = 'accepted';
    const DECLINED = 'declined';

    public static function deviceAdded($device, $user_id)
    {
        $objIA = new Actionlog();
        $objIA->interact_id = $device->id;
        $objIA->interact_type = 'i1';
        $objIA->interact_module = 'm1';
        $objIA->action_type = $objIA->interact_type_opts['i1'];
        $objIA->asset_type = 'hardware';
        $objIA->user_id = $user_id;
        if (isset($device->added_from) && $device->added_from == 2) {
            $objIA->note = 'Added via Network';
        } elseif (isset($device->added_from) && $device->added_from == 3) {
            $objIA->note = 'Added via Azure';
        } else {
            $objIA->note = 'Manually Added';
        }

        /* for old support */
        $objIA->asset_id = $device->id;
        $objIA->save();

        $objIR = new InteractRecord();
        $objIR->fill($device->dataForCache());
        $objIR->interact_log_id = $objIA->id;
        $objIR->save();

        $objIA->interact_id = $objIR->id;
        $objIA->save();
    }

    public static function deviceEdited($device, $old_data, $user_id, $customFieldsCacheData = null, $customFieldsRecordData = null)
    {
        $objIA = new Actionlog();
        $objIA->interact_id = $device->id;
        $objIA->asset_id = $device->id;
        $objIA->interact_type = 'i2';
        $objIA->interact_module = 'm1';
        $objIA->action_type = $objIA->interact_type_opts['i2'];
        $objIA->asset_type = 'hardware';
        $objIA->user_id = $user_id;
        $objIA->note = $device->notes;
        $objIA->save();

        $objIR = new InteractRecord();
        $objIR->fill($device->dataForCache());
        $objIR->interact_log_id = $objIA->id;
        if (!empty($customFieldsRecordData)) {
            foreach ($customFieldsRecordData as $key => $f) {
                $objIR->{$key} = isset($f) ? $f : null;
            }
        }
        $objIR->save();

        $objIA->interact_id = $objIR->id;
        $objIA->save();

        $objIC = new InteractCache();
        $objIC->fill($old_data);
        if (!empty($customFieldsCacheData)) {
            foreach ($customFieldsCacheData as $key => $f) {
                $objIC->{$key} = isset($f) ? $f : null;
            }
        }
        $objIC->interact_log_id = $objIA->id;
        $objIC->interact_record_id = $objIR->id;
        $objIC->save();
    }

    public static function deviceDeleted($device, $user_id)
    {
        $objIA = new Actionlog();
        $objIA->interact_id = $device->id;
        $objIA->asset_id = $device->id;
        $objIA->interact_type = 'i3';
        $objIA->interact_module = 'm1';
        $objIA->action_type = $objIA->interact_type_opts['i3'];
        $objIA->asset_type = 'hardware';
        $objIA->user_id = $user_id;
        $objIA->save();
    }

    public static function deviceRestored($device, $user_id)
    {
        $objIA = new Actionlog();
        $objIA->interact_id = $device->id;
        $objIA->asset_id = $device->id;
        $objIA->location_id = $device->rtd_location_id;
        $objIA->interact_type = 'i6';
        $objIA->interact_module = 'm1';
        $objIA->action_type = $objIA->interact_type_opts['i6'];
        $objIA->asset_type = 'hardware';
        $objIA->user_id = $user_id;
        $objIA->save();
    }

    public static function deviceCheckout($device_id, $assigned_for, $assigned_to, $last_checkout_project, $location_id, $user_id, $note, $allocationTypeId = null, $rate = null, $rate_cost = null, $billable = null, $checkout_date = null)
    {
        $objIA = new Actionlog();
        $objIA->interact_id = $device_id;
        $objIA->asset_id = $device_id;
        $objIA->interact_type = 'i4';
        $objIA->interact_module = 'm1';
        $objIA->action_type = $objIA->interact_type_opts['i4'];
        $objIA->asset_type = 'hardware';
        $objIA->checkedout_to = $assigned_to;
        $objIA->assigned_for = $assigned_for;
        $objIA->project_id = $last_checkout_project;
        $objIA->location_id = $location_id;
        $objIA->user_id = $user_id;
        $objIA->note = $note;
        $objIA->created_at = $checkout_date;
        $objIA->access_code = sha1(time());
        $objIA->allocation_type_id = $allocationTypeId;

        if ($billable == 1) {
            $objIR = new DeviceBillingCheckinCheckout();
            $objIR->rate = $rate;
            $objIR->rate_cost = $rate_cost;
            $objIR->asset_id = $device_id;
            $objIR->checkin_at = null;
            $objIR->action_type = $objIA->interact_type_opts['i4'];
            $objIR->save();
        }

        $objIA->save();
        return $objIA;
    }

    public static function deviceCheckin($data, $checkin_date)
    {
        $objIA = new Actionlog();
        $objIA->fill($data);
        $objIA->interact_id = $data['asset_id'];
        $objIA->interact_type = 'i5';
        $objIA->action_type = $objIA->interact_type_opts['i5'];
        $objIA->interact_module = 'm1';
        $objIA->asset_type = 'hardware';
        $objIA->checkin_attachment = $data['checkin_attachment'];
        $objIA->save();

        if ($checkin_date) {
            $created_at = CommonHelper::getDateAs($checkin_date, 'Y-m-d H:i:s', 'd/m/Y');
            if ($created_at) {
                $objIA->created_at = $created_at;
            }
            $objIA->save();
        }

        return $objIA;
    }

    public function allocationType()
    {
        return $this->belongsTo('App\Models\AssetAllocationType', 'allocation_type_id');
    }

    public static function deviceSold($device, $user_id)
    {
        $objDS = new Actionlog();
        $objDS->interact_id = $device->id;
        $objDS->asset_id = $device->id;
        $objDS->interact_type = 'i7';
        $objDS->interact_module = 'm1';
        $objDS->action_type = $objDS->interact_type_opts['i7'];
        $objDS->asset_type = 'hardware';
        $objDS->user_id = $device->sold_by;
        $objDS->note = $device->resale_notes;
        $objDS->save();
    }

    public static function restoreSoldDevice($device, $user_id)
    {
        $objRSD = new Actionlog();
        $objRSD->interact_id = $device->id;
        $objRSD->asset_id = $device->id;
        $objRSD->interact_type = 'i8';
        $objRSD->interact_module = 'm1';
        $objRSD->action_type = $objRSD->interact_type_opts['i8'];
        $objRSD->asset_type = 'hardware';
        $objRSD->user_id = $user_id;
        $objRSD->save();
    }

    public static function transferDevice($device, $user_id, $location_id, $responsible_user, $status)
    {
        $objTD = new Actionlog();
        $objTD->interact_id = $device->id;
        $objTD->asset_id = $device->id;
        $objTD->interact_type = 'i9';
        $objTD->interact_module = 'm1';
        $objTD->action_type = $objTD->interact_type_opts['i9'];
        $objTD->asset_type = 'hardware';
        $objTD->note = $status;
        $objTD->assigned_for = 1;  // use this code only location name show not any other requirement
        $objTD->location_id = $location_id;
        $objTD->checkedout_to = $responsible_user;
        $objTD->user_id = $user_id;
        $objTD->save();
    }

    // for accessories
    public static function accessoryAdded($accessory, $user_id)
    {
        $objIA = new Actionlog();
        $objIA->interact_id = $accessory->id;
        $objIA->asset_id = $accessory->id;
        $objIA->interact_type = 'i1';
        $objIA->interact_module = 'm2';
        $objIA->action_type = $objIA->interact_type_opts['i1'];
        $objIA->asset_type = 'accessory';
        $objIA->user_id = $user_id;
        $objIA->note = 'Accessory Added';
        $objIA->save();
    }

    public static function accessoryEdited($accessory, $old_data, $user_id, $customFieldsCacheData = null, $customFieldsRecordData = null)
    {
        $objIA = new Actionlog();
        $objIA->interact_id = $accessory->id;
        $objIA->asset_id = $accessory->id;
        $objIA->interact_type = 'i2';
        $objIA->interact_module = 'm2';
        $objIA->action_type = $objIA->interact_type_opts['i2'];
        $objIA->asset_type = 'accessory';
        $objIA->user_id = $user_id;
        $objIA->note = $accessory->notes;
        $objIA->save();

        // New Data
        $objAR = new AccessoryRecord();
        $objAR->fill($accessory->AccessorydataForCache());
        $objAR->accessory_log_id = $objIA->id;

        if (!empty($customFieldsRecordData)) {
            foreach ($customFieldsRecordData as $key => $value) {
                $objAR->{$key} = $value ?? null;
            }
        }
        $objAR->save();
        $objIA->interact_id = $objAR->id;
        $objIA->save();

        // Old Data
        $objAC = new AccessoryCache();
        $objAC->fill($old_data);
        if (!empty($customFieldsCacheData)) {
            foreach ($customFieldsCacheData as $key => $value) {
                $objAC->{$key} = $value ?? null;
            }
        }
        $objAC->accessory_log_id = $objIA->id;
        $objAC->accessory_record_id = $objAR->id;
        $objAC->save();
    }

    public static function accessoryDeleted($accessory, $user_id)
    {
        $objIA = new Actionlog();
        $objIA->interact_id = $accessory->id;
        $objIA->asset_id = $accessory->id;
        $objIA->interact_type = 'i3';
        $objIA->interact_module = 'm2';
        $objIA->action_type = $objIA->interact_type_opts['i3'];
        $objIA->asset_type = 'accessory';
        $objIA->user_id = $user_id;
        $objIA->save();
    }

    public static function accessoryRestored($accessory, $user_id)
    {
        $objIA = new Actionlog();
        $objIA->interact_id = $accessory->id;
        $objIA->asset_id = $accessory->id;
        $objIA->interact_type = 'i6';
        $objIA->interact_module = 'm2';
        $objIA->action_type = $objIA->interact_type_opts['i6'];
        $objIA->asset_type = 'accessory';
        $objIA->user_id = $user_id;
        $objIA->save();
    }

    public static function accessoryCheckout($accessory_id, $assigned_for, $assigned_to, $location_id, $user_id, $note = null)
    {
        $objIA = new Actionlog();
        $objIA->interact_id = $accessory_id;
        $objIA->asset_id = $accessory_id;
        $objIA->accessory_id = $accessory_id;
        $objIA->interact_type = 'i4';
        $objIA->interact_module = 'm2';
        $objIA->action_type = $objIA->interact_type_opts['i4'];
        $objIA->asset_type = 'accessory';
        $objIA->assigned_for = $assigned_for;
        $objIA->assigned_to_type = $assigned_for;
        $objIA->checkedout_to = $assigned_to;
        $objIA->location_id = $location_id;
        $objIA->user_id = $user_id;
        $objIA->note = $note;
        $objIA->save();
        return $objIA;
    }

    public static function accessoryCheckin($data)
    {
        $objIA = new Actionlog();
        $objIA->fill($data);
        $objIA->interact_id = $data['asset_id'];
        $objIA->asset_id = $data['asset_id'];
        $objIA->interact_type = 'i5';
        $objIA->interact_module = 'm2';
        $objIA->action_type = $objIA->interact_type_opts['i5'];
        $objIA->asset_type = 'accessory';
        $objIA->save();
        return $objIA;
    }

    public static function accessoryScrap($accessory, $user_id, $note = null)
    {
        $objIA = new Actionlog();
        $objIA->interact_id = $accessory->id;
        $objIA->asset_id = $accessory->id;
        $objIA->accessory_id = $accessory->id;
        $objIA->interact_type = 'i10';
        $objIA->interact_module = 'm2';
        $objIA->action_type = $objIA->interact_type_opts['i10'];
        $objIA->asset_type = 'accessory';
        $objIA->user_id = $user_id;
        $objIA->note = $note;
        $objIA->save();
        return $objIA;
    }

    public static function accessoryRestoreScrap($accessory, $user_id, $note = null)
    {
        $objIA = new Actionlog();
        $objIA->interact_id = $accessory->id;
        $objIA->asset_id = $accessory->id;
        $objIA->accessory_id = $accessory->id;
        $objIA->interact_type = 'i11';
        $objIA->interact_module = 'm2';
        $objIA->action_type = $objIA->interact_type_opts['i11'];
        $objIA->asset_type = 'accessory';
        $objIA->user_id = $user_id;
        $objIA->note = $note;
        $objIA->save();
        return $objIA;
    }

    public static function licenseAdded($license, $user_id, $device)
    {
        $objIA = new Actionlog();
        $objIA->interact_id = $license;
        $objIA->asset_id = $license;
        $objIA->interact_type = 'i1';
        $objIA->interact_module = 'm3';
        $objIA->action_type = $objIA->interact_type_opts['i1'];
        $objIA->asset_type = 'Software';
        $objIA->user_id = $user_id;
        if ($device == 2) {
            $objIA->note = 'Added via Network';
        } else {
            $objIA->note = 'Manually Added';
        }
        $objIA->save();
    }

    public static function licenseEdited($objLicense, $old_data, $user_id, $customFieldsCacheData = null, $customFieldsRecordData = null)
    {
        // dd($objLicense,$old_data,$user_id);
        $objIA = new Actionlog();
        $objIA->interact_id = $objLicense->id;
        $objIA->asset_id = $objLicense->id;
        $objIA->interact_type = 'i2';
        $objIA->interact_module = 'm3';
        $objIA->action_type = $objIA->interact_type_opts['i2'];
        $objIA->asset_type = 'Software';
        $objIA->user_id = $user_id;
        $objIA->note = $objLicense->notes;
        $objIA->save();

        // New Data
        $objAR = new LicenseRecord();
        $objAR->fill($objLicense->licensedataForCache());
        $objAR->License_log_id = $objIA->id;

        if (!empty($customFieldsRecordData)) {
            foreach ($customFieldsRecordData as $key => $value) {
                $objAR->{$key} = $value ?? null;
            }
        }
        $objAR->save();
        $objIA->interact_id = $objAR->id;
        $objIA->save();

        // Old Data
        $objAC = new LicenseCache();
        $objAC->fill($old_data);
        if (!empty($customFieldsCacheData)) {
            foreach ($customFieldsCacheData as $key => $value) {
                $objAC->{$key} = $value ?? null;
            }
        }
        $objAC->license_log_id = $objIA->id;
        $objAC->license_record_id = $objAR->id;
        $objAC->save();
    }

    public static function licenceDeleted($licenceDtl, $user_id)
    {
        $objIA = new Actionlog();
        $objIA->interact_id = $licenceDtl->id;
        $objIA->asset_id = $licenceDtl->id;
        $objIA->interact_type = 'i3';
        $objIA->interact_module = 'm3';
        $objIA->action_type = $objIA->interact_type_opts['i3'];
        $objIA->asset_type = 'Sofware';
        $objIA->user_id = $user_id;
        $objIA->save();
    }

    public static function licenseRestore($licenceDtl, $user_id)
    {
        $objIA = new Actionlog();
        $objIA->interact_id = $licenceDtl->id;
        $objIA->asset_id = $licenceDtl->id;
        $objIA->interact_type = 'i6';
        $objIA->interact_module = 'm3';
        $objIA->action_type = $objIA->interact_type_opts['i6'];
        $objIA->asset_type = 'Sofware';
        $objIA->user_id = $user_id;
        $objIA->save();
    }

    public static function licenseCheckout($license_id, $assigned_for, $assigned_to, $location_id, $user_id, $note = null)
    {
        $objIA = new Actionlog();
        $objIA->interact_id = $license_id;
        $objIA->asset_id = $license_id;
        $objIA->interact_type = 'i4';
        $objIA->interact_module = 'm3';
        $objIA->action_type = $objIA->interact_type_opts['i4'];
        $objIA->asset_type = 'Software';
        $objIA->assigned_for = $assigned_for;
        $objIA->assigned_to_type = $assigned_for;
        $objIA->checkedout_to = $assigned_to;
        $objIA->location_id = $location_id;
        $objIA->user_id = $user_id;
        $objIA->note = $note;
        $objIA->save();
        return $objIA;
    }

    public static function licenseCheckin($license_id, $assigned_for, $assigned_to, $location_id, $note, $user_id) {
        $objIA = new Actionlog();
        $objIA->interact_id = $license_id;
        $objIA->interact_type = 'i5';
        $objIA->action_type = $objIA->interact_type_opts['i5'];
        $objIA->interact_module = 'm3';
        $objIA->asset_type = 'Software';
        $objIA->assigned_to_type = $assigned_for;
        $objIA->checkedout_to = $assigned_to;
        $objIA->location_id = $location_id;
        $objIA->user_id = $user_id;
        $objIA->note = $note;
        $objIA->save();
        return $objIA;
    }
   
}

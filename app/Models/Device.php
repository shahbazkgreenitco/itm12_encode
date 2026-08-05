<?php

namespace App\Models;

use App\Models\Base;
use App\Models\Settings;
use App\Models\Label;
use DB;
use Mail;
use Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\Common as CommonHelper;
use App\Mail\DeviceCheckoutNotification;
use App\Models\Device\DeviceSetting;
use App\Models\CustomFieldset;
use Carbon\Carbon;
use Log;

class Device extends Base {
    use SoftDeletes;
    protected $table = "assets";
    protected $dates = ['deleted_at'];
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];
    protected $assigned_for_options = [["id"=>1,"text"=>"User"],["id"=>2,"text"=>"Place"]];
    protected $device_occure_type_options = [["id"=>"0", "text"=>"Purchase Device"], ["id"=>"1", "text"=>"Project Device"], ["id"=>"2", "text"=>"Rental"], ["id"=>"3", "text"=>"Customer Owned"]];
    
    public function deviceLog() {
        return $this->hasMany('App\Models\DeviceLog','id', 'asset_id')->withTrashed();
    }

    /* current checkout log */
    public function chkoutLog() {
        return $this->belongsTo('App\Models\Actionlog', 'chkout_log_id');
    }

    public function user() {
        return $this->belongsTo('App\Models\User', 'assigned_to');
    }


    public function ntwDevices() {
        return DB::table('assets as a')
               ->join('itm_network_inventory_basic as ntw', 'ntw.BIOSSerialNumber', '=', 'a.serial')
               ->select('ntw.id')
               ->get();
    }

    public function trackedDevices() {
        return $this->hasMany('App\Models\NetworkInventory\TrackedDevice', 'device_id', 'id');
    }
    
    public function assigneduser() {
        return $this->belongsTo('App\Models\User', 'assigned_to')->withTrashed()->withDefault(['first_name' =>'', 'last_name' => '', 'username' => '']);
    }

    public function assignedPlace() {
        if($this->assigned_for != 2) {
            return [];
        }
        return $this->belongsTo('App\Models\Place', 'assigned_to');
    }

    public function scopeWithAssignedUser($query) {
        $query->leftJoin('users', function($query1) {
            $query1->on('assets.assigned_to', '=', 'users.id')->where('assets.assigned_for', '=', 1);
        });
    }

    public function stockPlace() {
        // if(! $this->stock_place) {
        //     return [];
        // }
        return $this->belongsTo('App\Models\Place', 'stock_place');
    }

    public function projectName() {
        return $this->belongsTo('App\Models\ProjectManagement\Project', 'last_checkout_project');
    }

    public function assetOwner() {
        // commenting this since giving error in info_tab in device detail page, since it is returning an empty array but giving an error that it must return a relationship instance.
        // if(! $this->asset_owner) {
        //     return [];
        // }
        return $this->belongsTo('App\Models\User', 'asset_owner')->withTrashed()->withDefault(['first_name' =>'', 'last_name' => '']);
    }

    public function disposedBy() {
        if(! $this->dispose_by) {
            return [];
        }
        return $this->belongsTo('App\Models\User', $this->dispose_by)->withTrashed()->withDefault(['first_name' =>'', 'last_name' => '']);
    }

    public function lease() {
        return $this->belongsTo('App\Models\Lease', 'lease_id');
    }

    public static function getAssignedForOptions() {
        return (new Device)->assigned_for_options;
    }

    public static function getDeviceFrom() {
        return (new Device)->device_occure_type_options;
    }

    public function isCheckedOutToUser() {
        return $this->assigned_for == 1 && $this->assigned_to;
    }

    public function isCheckedOutToPlace() {
        return $this->assigned_for == 2 && $this->assigned_to;
    }

    public function model() {
        return $this->belongsTo('App\Models\Model', 'model_id')->withTrashed()->withDefault();
    }

    public function company() {
        return $this->belongsTo('App\Models\Company', 'company_id')->withTrashed()->withDefault();
    }

    public function status() {
        return $this->belongsTo('App\Models\Label', 'status_id')->withTrashed()->withDefault();
    }

    public function purchaseReference() {
        return $this->belongsTo('App\Models\Purchase', 'invoice_id');
    }

    public function supplier() {
        return $this->belongsTo( 'App\Models\Supplier', 'supplier_id' );
    }
    
    public function amcSupplier() {
        return $this->belongsTo( 'App\Models\Supplier', 'amc_supplier_id' );
    }

    public function location() {
        return $this->belongsTo( 'App\Models\Location', 'rtd_location_id');
    }

    public function place() {
        return $this->belongsTo( 'App\Models\Place', 'internal_place_id');
    }

    public function niDetectedLocation() {
        return $this->belongsTo('App\Models\Location', 'ni_detected_location');
    }

    public function canCheckout() {
        if($this->isCheckedOut()) {
            return false;
        }
        return ($this->status && $this->status->deployable == "1" && $this->status->archived == "0" && $this->deleted_at == null);
    }

   
    public function isCheckedOut() {
        return $this->assigned_to != null && $this->assigned_to > 0 && $this->deleted_at == null;
    }

    public function fixDeviceTag() {
        $new_tag = Settings::getSettings()->auto_increment_prefix . $this->id;
        if(!Device::where("asset_tag", "like", $new_tag)->exists()) {
            return Device::getNextId();
        }
        return $new_tag;
    }

    public static function getNextId() {
        $settings = DeviceSetting::first();
        $device_id = DB::table('assets')->where('physical', '=', '1')->max('id');
        $assetTag = Settings::getSettings()->auto_increment_prefix . $device_id;

        $separators = [0 => '', 1 => '-', 2 => '_', 3 => '/'];
        $tagData = isset($settings['asset_tag_data']) ? json_decode($settings['asset_tag_data'], true) : null;
        $separator = (!empty($tagData) && isset($tagData['asset_tag_saperator']) && isset($separators[$tagData['asset_tag_saperator']]))  ? $separators[$tagData['asset_tag_saperator']] : '';

        if (!empty($tagData) && isset($tagData['asset_tag_type']) && ($tagData['asset_tag_type'] == 2 || $tagData['asset_tag_type'] == 3)) {
            $result = DB::table('assets as a')
            ->select('a.name as hostname', 'loc.name as location', 'dept.name as deptname', DB::raw("DATE_FORMAT(purchase_date, '%m%d%Y') as purchase_date"), 'invoice_id')
            ->leftJoin('locations as loc', 'loc.id', '=', 'a.rtd_location_id')
            ->leftJoin('departments as dept', 'dept.id', '=', 'a.department_id')
            ->where('a.id', $device_id)
            ->first();

            //Asset Tag Type Is 2 (Using Hostname)
            if (!empty($tagData) && isset($tagData['asset_tag_type']) && $tagData['asset_tag_type'] == 2) {
                if (!empty($result->hostname)){
                    if(Device::where("asset_tag", "like", $result->hostname)->exists()){
                        $assetTag =  $assetTag . $separator . $result->hostname;
                    }else{
                        $assetTag = !empty($result->hostname) ? $result->hostname : $assetTag;
                    }
                }
            } elseif (!empty($tagData) && isset($tagData['asset_tag_type']) && $tagData['asset_tag_type'] == 3) {
                $invoiceDate = null;
                if(isset($result->invoice_id) && $result->invoice_id > 0) {
                    $invoiceDate = Purchase::where("id", $result->invoice_id)
                        ->value(DB::raw("DATE_FORMAT(invoice_date, '%m%d%Y')"));
                }
                // Asset Tag Type 3 (Using Custom Tag)
                $tagParts = [];
                $currentDate = Carbon::now();
                $currentMonth = $currentDate->format('m');
                $currentYear = $currentDate->format('Y');

                if (!empty($result->location) && $tagData['asset_tag_location'] == 1) {
                    $tagParts[] = $separator . $result->location;
                }
                if (!empty($result->deptname) && $tagData['asset_tag_department'] == 1) {
                    $tagParts[] = $separator . $result->deptname;
                }

                $usedDate = false;
                if ($tagData['asset_tag_pur_date'] == 1) {
                    if (!empty($result->purchase_date)) {
                        $tagParts[] = $separator . substr($result->purchase_date, 0, 2) . $separator . substr($result->purchase_date, 2, 2) . $separator . substr($result->purchase_date, 4);
                        $usedDate = true;
                    } elseif (!empty($invoiceDate)) {
                        $tagParts[] = $separator . substr($invoiceDate, 0, 2) . $separator . substr($invoiceDate, 2, 2) . $separator . substr($invoiceDate, 4);
                        $usedDate = true;
                    }
                }
                if (!$usedDate) {
                    if (!empty($currentMonth) && $tagData['asset_tag_month'] == 1) {
                        $tagParts[] = $separator . $currentMonth;
                    }
                    if (!empty($currentYear) && $tagData['asset_tag_year'] == 1) {
                        $tagParts[] = $separator . $currentYear;
                    }
                }
                $assetTag .= implode('', $tagParts);
            }
        }
        return $assetTag;
    }
  
    public function getDevices() {
        return DB::table($this->tblDevice . ' as d')
               ->join($this->tblModel . ' as mdl', 'd.model_id', '=', 'mdl.id')
               ->join($this->tblManfacture . ' as mft', 'mdl.manufact_id', '=', 'mft.id')
               ->join($this->tblCompany . ' as cmp', 'd.company_id', '=', 'cmp.id')
               ->join($this->tblLocation . ' as loc', 'd.location_id', '=', 'loc.id')
               ->join($this->tblLabel . ' as lbl', 'd.current_status', '=', 'lbl.id')
               ->leftJoin($this->tblUser . ' as u', 'd.user_id', '=', 'u.id')
               ->leftJoin($this->tblDepartment . ' as dpmt', 'd.department_id', '=', 'dpmt.id')
               ->select('d.*', 'mft.name as manufacture', 'mdl.name as model', 'cmp.name as company', 'loc.name as location')
               ->addSelect('lbl.name as label')
               ->addSelect('u.username as user', 'dpmt.name as department')
               ->addSelect(DB::raw('case when lbl.status_type = "Deployed" then true else false end as is_deployed'))
               ->addSelect(DB::raw('case when lbl.status_type = "Deployable" then true else false end as is_deployable'))
               ->get();
    }

    public function getDeviceById($id) {
        return DB::table($this->tblDevice)->where("id", "=", $id)->limit(1)->get();
    }

    public function addDevice($data) {
        $data["created_at"] = $data["updated_at"] = date('Y-m-d H:i:s');
        return DB::table($this->tblDevice)->insertGetId($data);
    }

    public function updateDeviceById($data, $id) {
        $data["updated_at"] = date('Y-m-d H:i:s');
        return DB::table($this->tblDevice)->where("id", $id)->update($data);
    }
    
    public function addDeviceLog($data) {
        $data["logged_at"] = date('Y-m-d H:i:s');
        return DB::table($this->tblDeviceLog)->insert($data);
    }
    
	public function checkinEmail() {
		return $this->model->category->checkin_email;
	}
	
	public function requireAcceptance() {
		return $this->model->category->require_acceptance || config('app.direct_accept_link');
    }
	
	public function checkout($data, $target) {
		$this->last_checkout =  CommonHelper::getDateAs($data["checkout_at"], "Y-m-d H:i:s", "d/m/Y");
		if(! $this->last_checkout) {
			$this->last_checkout = date("Y-m-d H:i:s");
        }
        
		$this->expected_checkin = CommonHelper::getDateAs($data["expected_checkin"], "Y-m-d", "d/m/Y");
        $this->assigned_for = (int) $data["assigned_for"];
        $this->last_checkout_project = isset($data["last_checkout_project"]) ? ((int) $data["last_checkout_project"]) : null;
		$this->assigned_to = $data["assigned_for"] == 2 ? $data["assigned_place"] : $data["assigned_to"];
		$this->name = $data["name"];
        
        $this->accepted = null;
        $this->stock_place = null;
        $this->chkin_log_id = null;
        if( $this->requireAcceptance() && $data["assigned_for"] == 1 ) {
            $this->accepted = "pending";
            $this->accept_link_send_at = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
            $this->chkout_acceptance_log_id = null;
        }
        $oldStatus = $this->status_id;
        $this->status_id = Label::getDeployedLabel()->id;
        if(in_array(config('app.client'), ["rolepermission", "knightfrank", "rashmi"])) {
            if(isset($this->status_id)){
                $devices = Device::where('id', $data['id'])->first();
                CommonHelper::updateStatusCounts($oldStatus, $this->status_id, $devices);
            }
        }
		if(! $this->save()) {
			return false;
		}

        /* log */
        $data['rate'] = isset($data['rate']) ? $data['rate'] : null;
        $data['rate_cost'] = isset($data['rate_cost']) ? $data['rate_cost'] : null;
        $data['billable'] = isset($data['billable']) ? $data['billable'] : null;
        $log = Actionlog::deviceCheckout($this->id, $this->assigned_for, $this->assigned_to, $this->last_checkout_project, $target->location_id, Auth::user()->id, $data["note"], $data["allocation_type_id"], $data["rate"], $data["rate_cost"], $data['billable'], $this->last_checkout);

        $this->chkout_log_id = $log->id;
        $this->save();
        
        /* log update if request_id found */
        if(isset($data['request_id']) && $data['request_id']) {
            $requestLog = Actionlog::where("id", "=", $data['request_id'])->whereNull("accepted_id")->whereNull("deleted_at")->where("user_id", "=", $data["assigned_to"])->get();
            if(count($requestLog)) {
                $requestLog[0]->accepted_id = $log->id;
                $requestLog[0]->save();
            }
        }

        /* mail */
        try {
            $alertnotify = (Settings::first()->alerts_enabled == 1) ? CommonHelper::getGlobalAlertEmail() : [];

            if( config('mail.service_enabled') && ($this->requireAcceptance() || $this->getEula()) ) {
                if($this->assigned_for != 2) {
                    if($alertnotify && $target->email && config('app.direct_accept_link') == false) {
                        Mail::to($target->email)->queue(new DeviceCheckoutNotification($this, $log, $target));
                    }
                    elseif($target->email && config('app.direct_accept_link') == true) {
                        Mail::to($target->email)->queue(new DeviceCheckoutNotification($this, $log, $target));
                    }
                    elseif( $alertnotify && !$target->email ) {
                        Mail::to($alertnotify)->queue(new DeviceCheckoutNotification($this, $log, $target));
                    }
                }
                elseif($alertnotify) {
                    Mail::to($alertnotify)->queue(new DeviceCheckoutNotification($this, $log, $target));
                }
            }
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
        }
		
		return true;
	}
	
	public function getEula() {
		if ($this->model->category->eula_text) {
			return e($this->model->category->eula_text);
		} elseif (Settings::getSettings()->default_eula_text) {
			return e(Settings::getSettings()->default_eula_text);
		} else {
			return null;
		}
	}
	
	public function getDeviceName() {
		return $this->name && strlen($this->name) ? $this->name : $this->model->name; 
    }

    public function getWarrantyByFeed($rf = 'd/m/Y') {
        $wm = intval($this->warranty_months);
        $return = "";
        
        if(($wm > 0 && $this->purchase_date && $this->warranty_start_date == null && $this->warrenty_end_date == null) || ($wm > 0 && $this->purchase_date &&  $this->warrenty_end_date == null)) {
            try {
                $pd = Carbon::createFromFormat('Y-m-d', $this->purchase_date, config('app.timezone'))->addMonths($wm);
                $now = Carbon::now(config('app.timezone'));
                $return = $now->lt($pd) ? "Warranty Availble upto " . $pd->format($rf) : "Warranty given upto ".$pd->format($rf);
            }
            catch(\Carbon\Exception\InvalidDateException $e) {
                $return = "";
            }
        }elseif($wm > 0 && $this->invoice_id && $this->purchase_date == null) {
            try {
                $invoicedate = Carbon::createFromFormat('Y-m-d', $this->purchaseReference->invoice_date, config('app.timezone'))->addMonths($wm);
                $now = Carbon::now(config('app.timezone'));
                $return = $now->lt($invoicedate) ? "Warranty Availble upto " . $invoicedate->format($rf) : "Warranty given upto ".$invoicedate->format($rf);
            }
            catch(\Carbon\Exception\InvalidDateException $e) {
                $return = "";
            }
        }

        return $return;
    }

    public function getWarrantyEndDate($rf = 'd/m/Y') {
        $wm = intval($this->warranty_months);
        $return = "";
        
        if(($wm > 0 && $this->purchase_date && $this->warranty_start_date == null && $this->warrenty_end_date == null) || ($wm > 0 && $this->purchase_date &&  $this->warrenty_end_date == null)) {
            try {
                $pd = Carbon::createFromFormat('Y-m-d', $this->purchase_date, config('app.timezone'))->addMonths($wm);
                $now = Carbon::now(config('app.timezone'));
                $return = $now->lt($pd) ? $pd->format($rf) : $pd->format($rf);
            }
            catch(\Carbon\Exception\InvalidDateException $e) {
                $return = "";
            }
        }
        elseif($wm > 0 && $this->invoice_id && $this->purchase_date == null) {
            try {
                $invoicedate = Carbon::createFromFormat('Y-m-d', $this->purchaseReference->invoice_date, config('app.timezone'))->addMonths($wm);
                $now = Carbon::now(config('app.timezone'));
                $return = $now->lt($invoicedate) ? $invoicedate->format($rf) : $invoicedate->format($rf);
            }
            catch(\Carbon\Exception\InvalidDateException $e) {
                $return = "";
            }
        }

        return $return;
    }

    public function getWarrantyExpiredStatus($rf = 'Y-m-d') {
        $wm = intval($this->warranty_months);
        $now = Carbon::now(config('app.timezone'))->Format('Y-m-d H:i:s');
        $return = "";

        if($this->isLiveWarrentyPossible() && $this->manufacturer_warranty_data) {
            $data = json_decode($this->manufacturer_warranty_data, true);
            if(!$this->model || !$this->model->manufacturer || !$this->serial) {
                return $return;
            }
            $manufacturer = strtolower(trim($this->model->manufacturer->name));
            if(stripos($manufacturer, "dell") !== false) {
                if ($data['serial'] == $this->serial && count($data['info'])) {
                    try {
                        $we = null;
                        foreach ($data['info'] as $info) {
                            $temp = new Carbon($info['endDate']);
                            if (!$we) {
                                $we = $temp;
                            } elseif ($we->lessThan($temp)) {
                                $we = $temp;
                            }
                        }

                        if($this->warranty_start_date && $this->warrenty_end_date) {
                            $we = Carbon::make($we)->format('Y-m-d');
                            // $wed = Carbon::parse(Carbon::createFromFormat('Y-m-d', $this->warrenty_end_date));
                            $wed = CommonHelper::getDateAs($this->warrenty_end_date, "Y-m-d");
                            if($wed != $we) {
                                $this->warranty_status = 4;
                                $this->timestamps = false;
                                $this->save();
                                return "Warranty End Date and OEM Date not matching";
                            }
                        } elseif($this->purchase_date && $this->warranty_months) {
                            $pd = Carbon::parse(Carbon::createFromFormat('Y-m-d', $this->purchase_date));
                            $newDate = Carbon::parse($pd->addMonths($this->warranty_months))->format('Y-m-d');
                            if($newDate != Carbon::parse($we)->format('Y-m-d')) {
                                $this->warranty_status = 5;
                                $this->timestamps = false;
                                $this->save();
                                return "Purchase Date and OEM Date not matching";
                            }
                        } elseif($this->invoice_id && $this->warranty_months) {
                            $invoice_date = Carbon::parse(Carbon::createFromFormat('Y-m-d',$this->purchaseReference->invoice_date));
                            $new_invoice_date = Carbon::parse($invoice_date->addMonths($this->warranty_months))->format('Y-m-d');
                            if($new_invoice_date != Carbon::parse($we)->format('Y-m-d')) {
                                $this->warranty_status = 6;
                                $this->timestamps = false;
                                $this->save();
                                return "Invoice Date and OEM Date not matching";
                            }
                        }
                        $this->warranty_status = $we > $now ? 2 : 1;
                        $this->timestamps = false;
                        $this->save();
                        return $we > $now ? "Not Expired" : "Expired";
                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                    }
                }
            } elseif(stripos($manufacturer, "lenovo") !== false) {
                if(count($data) > 0) {
                    try {
                        $we = null;
                        foreach ($data as $info) {
                            $temp = new Carbon($info['End']);
                            if (!$we) {
                                $we = $temp;
                            } elseif ($we->lessThan($temp)) {
                                $we = $temp;
                            }
                        }

                        if($this->warranty_start_date && $this->warrenty_end_date) {
                            $we = Carbon::make($we)->format('Y-m-d');
                            // $wed = Carbon::parse(Carbon::createFromFormat('Y-m-d', $this->warrenty_end_date));
                            $wed = CommonHelper::getDateAs($this->warrenty_end_date, "Y-m-d");
                            if($wed != $we) {
                                $this->warranty_status = 4;
                                $this->timestamps = false;
                                $this->save();
                                return "Warranty End Date and OEM Date not matching";
                            }
                        } elseif($this->purchase_date && $this->warranty_months) {
                            $pd = Carbon::parse(Carbon::createFromFormat('Y-m-d', $this->purchase_date));
                            $newDate = Carbon::parse($pd->addMonths($this->warranty_months))->format('Y-m-d');
                            if($newDate != Carbon::parse($we)->format('Y-m-d')) {
                                $this->warranty_status = 5;
                                $this->timestamps = false;
                                $this->save();
                                return "Purchase Date and OEM Date not matching";
                            }
                        } elseif($this->invoice_id && $this->warranty_months) {
                            $invoice_date = Carbon::parse(Carbon::createFromFormat('Y-m-d',$this->purchaseReference->invoice_date));
                            $new_invoice_date = Carbon::parse($invoice_date->addMonths($this->warranty_months))->format('Y-m-d');
                            if($new_invoice_date != Carbon::parse($we)->format('Y-m-d')) {
                                $this->warranty_status = 6;
                                $this->timestamps = false;
                                $this->save();
                                return "Invoice Date and OEM Date not matching";
                            }
                        }
                        $this->warranty_status = $we > $now ? 2 : 1;
                        $this->timestamps = false;
                        $this->save();
                        return $we > $now ? "Not Expired" : "Expired";
                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                    }
                }
            }

            $otherBrands = ['hp', 'asus', 'acer'];
            $isOtherBrands = false;
            foreach ($otherBrands as $brand) {
                if (stripos($manufacturer, $brand) !== false) {
                    $isOtherBrands = true;
                    break;
                }
            }

            if($isOtherBrands) {
                if ($data['serial'] == $this->serial && count($data['info'])) {
                    try {
                        $we = null;
                        if(isset($data['info']['endDate'])) {
                            $temp = new Carbon($data['info']['endDate']);
                            if (!$we) {
                                $we = $temp;
                            } elseif ($we->lessThan($temp)) {
                                $we = $temp;
                            }
                        }

                        if($this->warranty_start_date && $this->warrenty_end_date) {
                            $we = Carbon::make($we)->format('Y-m-d');
                            // $wed = Carbon::parse(Carbon::createFromFormat('Y-m-d', $this->warrenty_end_date));
                            $wed = CommonHelper::getDateAs($this->warrenty_end_date, "Y-m-d");
                            if($wed != $we) {
                                return "Warranty End Date and OEM Date not matching";
                            }
                        } elseif($this->purchase_date && $this->warranty_months) {
                            $pd = Carbon::parse(Carbon::createFromFormat('Y-m-d', $this->purchase_date));
                            $newDate = Carbon::parse($pd->addMonths($this->warranty_months))->format('Y-m-d');
                            if($newDate != Carbon::parse($we)->format('Y-m-d')) {
                                return "Purchase Date and OEM Date not matching";
                            }
                        } elseif($this->invoice_id && $this->warranty_months) {
                            $invoice_date = Carbon::parse(Carbon::createFromFormat('Y-m-d',$this->purchaseReference->invoice_date));
                            $new_invoice_date = Carbon::parse($invoice_date->addMonths($this->warranty_months))->format('Y-m-d');
                            if($new_invoice_date != Carbon::parse($we)->format('Y-m-d')) {
                                return "Invoice Date and OEM Date not matching";
                            }
                        }
                        $this->warranty_status = $we > $now ? 2 : 1;
                        $this->timestamps = false;
                        $this->save();
                        return $we > $now ? "Not Expired" : "Expired";
                    } catch (\Exception $e) {
                        Log::error("getWarrantyExpiredStatus Other:".$e->getMessage());
                    }
                }
            }
        }

        if(($this->warranty_start_date  && $this->warrenty_end_date) ||  ($this->warranty_start_date  && $this->warrenty_end_date && $this->warranty_months  && $this->purchase_date)){
            try {
                $ws = Carbon::createFromFormat('Y-m-d', $this->warranty_start_date);
                $we = Carbon::createFromFormat('Y-m-d', $this->warrenty_end_date);
                $war_exp_date_end = $we->format($rf);
                if($war_exp_date_end > $now){
                    $this->warranty_status = 2;
                    $return = "Not Expired";
                }else{
                    $this->warranty_status = 1;
                    $return = "Expired";
                }
            }
            catch(\Carbon\Exception\InvalidDateException $e) {
                $this->warranty_status = 3;
                $return = "";
            }
        }
        elseif($this->warranty_months  && $this->purchase_date){
            try {
                $pd = Carbon::createFromFormat('Y-m-d', $this->purchase_date)->addMonths($wm);
                $cur_date = Carbon::now(config('app.timezone'));
                $war_exp_date_val = $cur_date->lt($pd) ? "Warranty Available upto " . $pd->format($rf) : "";
                $war_exp_date = $pd->format($rf);
                if($war_exp_date > $now){
                    $this->warranty_status = 2;
                    $return = "Not Expired";
                }
                else {
                    $this->warranty_status = 1;
                    $return = "Expired";
                }
            }
            catch(\Carbon\Exception\InvalidDateException $e) {
                $this->warranty_status = 3;
                $return = "";
            }
        }
        elseif($this->warranty_months  && $this->invoice_id){
            try {
                $invoicedate = Carbon::createFromFormat('Y-m-d', $this->purchaseReference->invoice_date, config('app.timezone'))->addMonths($wm);
                $now = Carbon::now(config('app.timezone'));
                $inv_date = $invoicedate->format($rf);
                if($inv_date > $now){
                    $this->warranty_status = 2;
                    $return = "Not Expired";
                }else{
                    $this->warranty_status = 1;
                    $return = "Expired";
                }
            }
            catch(\Carbon\Exception\InvalidDateException $e) {
                $this->warranty_status = 3;
                $return = "";
            }
        }
        else{
            $this->warranty_status = 3;
            $return = "Not Applicable";
        }
        $this->timestamps = false;
        $this->save();
        return $return;
    }

    public function getWarrantyByEndDate() {
        $return = "";

        if($this->warranty_start_date && $this->warrenty_end_date) {
            try {
                $ws = Carbon::createFromFormat('Y-m-d', $this->warranty_start_date);
                $we = Carbon::createFromFormat('Y-m-d', $this->warrenty_end_date);
                $diff_in_months = $we->diffInMonths($ws);
                $return = $diff_in_months;
            }
            catch(\Carbon\Exception\InvalidDateException $e) {
                $return = "";
            }
        }elseif($this->warranty_start_date && $this->warrenty_end_date && $this->warranty_months && $this->purchase_date) {
            try {
                $ws = Carbon::createFromFormat('Y-m-d', $this->warranty_start_date);
                $we = Carbon::createFromFormat('Y-m-d', $this->warrenty_end_date);
                $diff_in_months = $we->diffInMonths($ws);
                $return = $diff_in_months;
            }
            catch(\Carbon\Exception\InvalidDateException $e) {
                $return = "";
            }
        }

        return $return;
    }


    /* return type boolean */
    public function isLiveWarrentyPossible(): bool {
        if(!$this->model || !$this->model->manufacturer || !$this->serial) {
            return false;
        }

        $manufacturer = strtolower(trim($this->model->manufacturer->name));
        if((stripos($manufacturer, "dell") !== false) || (stripos($manufacturer, "lenovo") !== false) || (stripos($manufacturer, "acer") !== false) || (stripos($manufacturer, "hp") !== false) || (stripos($manufacturer, "asus") !== false)) {
            return true;
        }

        return false;
    }
    
    public function getLiveWarrentyInfo() {
        $return = [];
        if(!$this->model || !$this->model->manufacturer || !$this->serial) {
            return $return;
        }
        $manufacturer = strtolower(trim($this->model->manufacturer->name));
        if(stripos($manufacturer, "dell") !== false) {
            return $this->getDellWarrentyInfo();    
        }
        elseif(stripos($manufacturer, "lenovo") !== false) {
            return $this->getLenovoWarrentyInfo();
        }
        elseif(stripos($manufacturer, "asus") !== false || stripos($manufacturer, "acer") !== false || stripos($manufacturer, "hp") !== false) {
            return $this->getMLWarrentyInfo();
        }
        return $return;
    }

    public function getLenovoWarrentyInfo() {
        $url = config('services.warrenty.lenovo.url') . '?serial=' . trim($this->serial);
        $clientID = "g+7Rk5Q3PwMR/VSU9sF1ug==";
        $headers = array(
            'Accept: application/json',
            'ClientID: ' . $clientID
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        $result = curl_exec($ch);
        if(curl_errno($ch) || curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
            return [curl_errno($ch)];
        }
        curl_close($ch);
        $return_val = json_decode($result, true);
        if(!$result || !$return_val || !is_array($return_val) || !count($return_val) || !isset($return_val["InWarranty"])) {
            return [];
        }
        $this->manufacturer_warranty_data = json_encode($return_val["Warranty"]);
        $this->save();
        return $return_val["Warranty"];
    }

    protected function getDellWarrentyInfoFromServer() {
        try {
            $token = Settings::getSettings()->getDellWarrantyToken();

            if(! $token) {
                return [];
            }

            $headers = array(
                'Accept: application/json',
                'Authorization: Bearer ' . $token
            );

            $url = 'https://apigtwb2c.us.dell.com/PROD/sbil/eapi/v5/asset-entitlements?servicetags=' . $this->serial;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            $result = curl_exec($ch);
            if(curl_errno($ch) || curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
                return [curl_errno($ch)];
            }
            curl_close($ch);

            $val = json_decode($result, true);
            if(!$result || !$val || !is_array($val) || !count($val)) {
                return [];
            }

            return isset($val[0]['entitlements']) ? $val[0]['entitlements'] : [];
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
            return [];
        }
    }

    public function getDellWarrentyInfo() {
        try {
            if(! $this->serial) {
                return [];
            }

            if($this->manufacturer_warranty_data) {
                $data = json_decode($this->manufacturer_warranty_data, true);
                if($data['serial'] == $this->serial) {
                    return $data['info'];
                }
            }

            $result = $this->getDellWarrentyInfoFromServer();

            if($result && is_array($result) && count($result)) {
                $data = [];
                $data['serial'] = $this->serial;
                $data['info'] = $result;
                $this->manufacturer_warranty_data = json_encode($data);

                if($this->save()) {
                    return $result;
                }
            } 

            return [];
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
            return [];
        }
    }

    public function getMLWarrentyInfo() {
        try {
            if(! $this->serial) {
                return [];
            }

            if($this->manufacturer_warranty_data) {
                $data = json_decode($this->manufacturer_warranty_data, true);
                if($data['serial'] == $this->serial) {
                    return $data['info'];
                }
            }
            return [];
        }
        catch(\Exception $e) {
            Log::error("getMLWarrentyInfo: " . $e->getMessage());
            return [];
        }
    }

    public function getDellWarrentyInfoOld() {
        if(!config('services.warrenty.dell.key')) {
            return [];
        }
        $data = ['ID' => trim($this->serial)]; // 1rh2vq1
        $headers = array(
    		'Accept: application/json',
    		'Content-Type: application/x-www-form-urlencoded',
    		'apikey: ' . config('services.warrenty.dell.key')
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, config('services.warrenty.dell.url'));
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_POST, true);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $result = curl_exec($ch);
        if(curl_errno($ch) || curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
            return [curl_errno($ch)];
        }
        curl_close($ch);
        $return_val = json_decode($result, true);
        if(!$result || !$return_val || !is_array($return_val) || !count($return_val)) {
            return [];
        }
        return isset($return_val['AssetWarrantyResponse'][0]['AssetEntitlementData']) ?  $return_val['AssetWarrantyResponse'][0]['AssetEntitlementData'] : [];
    }

    public function dataForCache() {
        return (array) $this->only("name", "asset_tag", "model_id", "serial", "uuid", "purchase_date", "purchase_cost", "purchase_currency", "order_number", "assigned_to", "assigned_for", "notes", "image", "user_id", "physical", "status_id", "archived", "warranty_months", "depreciate", "supplier_id", "requestable", "high_pririty", "rtd_location_id", "internal_place_id", "accepted", "last_checkout", "expected_checkin", "company_id", "amc_supplier_id", "amc_expire_date", "invoice_id", "added_from", "ip", "mac", "lease_id", "device_occure_type","asset_type_id","department_id","warranty_start_date","warrenty_end_date","stock_place","sez_device","asset_owner","product_number");
    }

    public function isChangeThere($data) {
        foreach($data as $k=>$v) {
            try {
                if(in_array($k, ["purchase_date", "last_checkout", "expected_checkin", "amc_expire_date"])) {
                    if($this->{$k} && !$v) {
                        return true;
                    }
                    elseif(!$this->{$k} && $v) {
                        return true;
                    }
                    elseif($this->{$k} && $v) {
                        $a = "";
                        $b = " ";
                        // if($k == "purchase_date") {
                        //     $a = CommonHelper::getDateAs($this->{$k}, 'Y-m-d', 'Y-m-d');
                        //     $b = CommonHelper::getDateAs($v, 'Y-m-d', 'Y-m-d');
                        // }
                        // elseif($k == "last_checkout") {
                        //     $a = CommonHelper::getDateAs($this->{$k}, 'Y-m-d', 'Y-m-d');
                        //     $b = CommonHelper::getDateAs($v, 'Y-m-d', 'Y-m-d');
                        // }
                        return $a != $b;
                    }
                }
                elseif( $this->{$k} != $v ) {
                    return true;
                }
            }
            catch(\Exception $e) {

            }
        }
        return false;
    }

    public function asset_maintenance()
    {
        return $this->hasMany('App\Models\DeviceMaintenance', 'asset_id');
    }

    public function cm_relevant_device()
    {
        return $this->hasMany('App\Models\ChangeManagement\RelevantDevice', 'device_id');
    }
    
    public function component()
    {
        return $this->hasMany('App\Models\Component', 'checked_out_to');
    }
    
    public function interacted_cache()
    {
        return $this->hasMany('App\Models\InteractCache', 'asset_tag');
    }

    public function assetDepartment()
    {
        return $this->belongsTo('App\Models\Department', 'department_id');
    }

    public function interacted_record()
    {
        return $this->hasMany('App\Models\InteractRecord', 'asset_tag');
    }
    
    public function itm_network_device_track()
    {
        return $this->hasMany('App\Models\NetworkInventory\TrackedDevice', 'device_id');
    }
    public function license_seat()
    {
        return $this->hasMany('App\Models\LicenseSeat', 'asset_id');
    }
    public function tkt_ticket()
    {
        return $this->hasMany('App\Models\Ticket\Ticket', 'device_id')->whereNotIn('tkt_tickets.status_id', [5,6]);
    }

    public function acc_checkout_device()
    {
        return $this->hasMany('App\Models\AccessoryUser', 'assigned_to')->where('assigned_for', 3);
    }

    public static function salvated_value($purchase_cost) {
        return ($purchase_cost) * (5/100);
    }

    public static function dep_rate_company_based($purchase_cost,$eol) {
    //    return ($purchase_cost - self::salvated_value($purchase_cost)) / $eol;
          return 0;
    }

    public static function book_value($purchase_cost) {
        return ;
    }

    public static function dep_rate($purchase_cost,$eol) {
        return 100 * (1- $eol * sqrt(self::salvated_value($purchase_cost)/$purchase_cost));
    }

    public static function dep_rate_it_based($purchase_cost,$eol) {
        //return self::book_value($purchase_cost) * self::dep_rate($purchase_cost,$eol);
        return 0;
    }

    public function updateWarrantyStatus(){
        $now = Carbon::now(config('app.timezone'))->Format('Y-m-d');
        $return = "";

        if($this->isLiveWarrentyPossible() && $this->manufacturer_warranty_data) {
            $data = json_decode($this->manufacturer_warranty_data, true);
            if(!$this->model || !$this->model->manufacturer || !$this->serial) {
                return $return;
            }
            $manufacturer = strtolower(trim($this->model->manufacturer->name));
            if(stripos($manufacturer, "dell") !== false) {
                if ($data['serial'] == $this->serial && count($data['info']) && $data['info'][0] !== 0) {
                    try {
                        $we = null;
                        foreach ($data['info'] as $info) {
                            $temp = new Carbon($info['endDate']);
                            if (!$we) {
                                $we = $temp;
                            } elseif ($we->lessThan($temp)) {
                                $we = $temp;
                            }
                        }

                        if($this->warranty_start_date && $this->warrenty_end_date) {
                            $we = Carbon::make($we)->format('Y-m-d');
                            // $wed = Carbon::parse(Carbon::createFromFormat('Y-m-d', $this->warrenty_end_date));
                            $wed = CommonHelper::getDateAs($this->warrenty_end_date, "Y-m-d");
                            // if(!$wed->equalTo($we)) {
                            if($wed != $we) {
                                $this->warranty_status = 4;
                                return $this->warranty_status;
                            }
                        } elseif($this->purchase_date && $this->warranty_months) {
                            $pd = Carbon::parse(Carbon::createFromFormat('Y-m-d', $this->purchase_date));
                            $newDate = Carbon::parse($pd->addMonths($this->warranty_months))->Format('Y-m-d');
                            if($newDate != Carbon::parse($we)->format('Y-m-d')) {
                                $this->warranty_status = 5;
                                return $this->warranty_status;
                            }
                        } elseif($this->invoice_id && $this->warranty_months) {
                            $invoice_date = Carbon::parse(Carbon::createFromFormat('Y-m-d',$this->purchaseReference->invoice_date));
                            $new_invoice_date = Carbon::parse($invoice_date->addMonths($this->warranty_months))->format('Y-m-d');
                            if($new_invoice_date != Carbon::parse($we)->format('Y-m-d')) {
                                $this->warranty_status = 6;
                                return $this->warranty_status;
                            }
                        }
                        $this->warranty_status = $we > $now ? 2 : 1;
                        return $this->warranty_status;
                    } catch (\Exception $e) {
                        Log::error("updateWarrantyStatus Dell:" . $e->getMessage());
                    }
                }
            } elseif(stripos($manufacturer, "lenovo") !== false) {
                if(count($data) > 0) {
                    try {
                        $we = null;
                        foreach ($data as $info) {
                            $temp = new Carbon($info['End']);
                            if (!$we) {
                                $we = $temp;
                            } elseif ($we->lessThan($temp)) {
                                $we = $temp;
                            }
                        }

                        if($this->warranty_start_date && $this->warrenty_end_date) {
                            $we = Carbon::make($we)->format('Y-m-d');
                            // $wed = Carbon::parse(Carbon::createFromFormat('Y-m-d', $this->warrenty_end_date));
                            $wed = CommonHelper::getDateAs($this->warrenty_end_date, "Y-m-d");
                            // if(!$wed->equalTo($we)) {
                            if($wed != $we) {
                                $this->warranty_status = 4;
                                return $this->warranty_status;
                            }
                        } elseif($this->purchase_date && $this->warranty_months) {
                            $pd = Carbon::parse(Carbon::createFromFormat('Y-m-d', $this->purchase_date));
                            $newDate = Carbon::parse($pd->addMonths($this->warranty_months))->Format('Y-m-d');
                            if($newDate != Carbon::parse($we)->format('Y-m-d')) {
                                $this->warranty_status = 5;
                                return $this->warranty_status;
                            }
                        } elseif($this->invoice_id && $this->warranty_months) {
                            $invoice_date = Carbon::parse(Carbon::createFromFormat('Y-m-d',$this->purchaseReference->invoice_date));
                            $new_invoice_date = Carbon::parse($invoice_date->addMonths($this->warranty_months))->format('Y-m-d');
                            if($new_invoice_date != Carbon::parse($we)->format('Y-m-d')) {
                                $this->warranty_status = 6;
                                return $this->warranty_status;
                            }
                        }
                        $this->warranty_status = $we > $now ? 2 : 1;
                        return $this->warranty_status;
                    } catch (\Exception $e) {
                        Log::error("updateWarrantyStatus Lenovo:" . $e->getMessage());
                    }
                }
            }

            $otherBrands = ['hp', 'asus', 'acer'];
            $isOtherBrands = false;
            foreach ($otherBrands as $brand) {
                if (stripos($manufacturer, $brand) !== false) {
                    $isOtherBrands = true;
                    break;
                }
            }

            if($isOtherBrands) {
                if ($data['serial'] == $this->serial && count($data['info'])) {
                    try {
                        $we = null;
                        if(isset($data['info']['endDate'])) {
                            $temp = new Carbon($data['info']['endDate']);
                            if (!$we) {
                                $we = $temp;
                            } elseif ($we->lessThan($temp)) {
                                $we = $temp;
                            }
                        }

                        if($this->warranty_start_date && $this->warrenty_end_date) {
                            $we = Carbon::make($we)->format('Y-m-d');
                            // $wed = Carbon::parse(Carbon::createFromFormat('Y-m-d', $this->warrenty_end_date));
                            $wed = CommonHelper::getDateAs($this->warrenty_end_date, "Y-m-d");
                            // if(!$wed->equalTo($we)) {
                            if($wed != $we) {
                                $this->warranty_status = 4;
                                return $this->warranty_status;
                            }
                        } elseif($this->purchase_date && $this->warranty_months) {
                            $pd = Carbon::parse(Carbon::createFromFormat('Y-m-d', $this->purchase_date));
                            $newDate = Carbon::parse($pd->addMonths($this->warranty_months))->Format('Y-m-d');
                            if($newDate != Carbon::parse($we)->format('Y-m-d')) {
                                $this->warranty_status = 5;
                                return $this->warranty_status;
                            }
                        } elseif($this->invoice_id && $this->warranty_months) {
                            $invoice_date = Carbon::parse(Carbon::createFromFormat('Y-m-d',$this->purchaseReference->invoice_date));
                            $new_invoice_date = Carbon::parse($invoice_date->addMonths($this->warranty_months))->format('Y-m-d');
                            if($new_invoice_date != Carbon::parse($we)->format('Y-m-d')) {
                                $this->warranty_status = 6;
                                return $this->warranty_status;
                            }
                        }
                        $this->warranty_status = $we > $now ? 1 : 2;
                        return $this->warranty_status;
                    } catch (\Exception $e) {
                        Log::error("updateWarrantyStatus Other:".$e->getMessage());
                    }
                }
            }
        }

        if($this->warranty_start_date && $this->warrenty_end_date){
            try {
                $we = Carbon::createFromFormat('Y-m-d', $this->warrenty_end_date)->Format('Y-m-d');
                if($we > $now){
                    $this->warranty_status = 2;
                }else{
                    $this->warranty_status = 1;
                }
                return $this->warranty_status;
            }
            catch(\Exception $e) {
                Log::error($e->getMessage());
            }
        }
        elseif($this->purchase_date && $this->warranty_months){
            try {
                $pd = Carbon::createFromFormat('Y-m-d', $this->purchase_date);
                $newDate = $pd->addMonths($this->warranty_months)->Format('Y-m-d');
                if($newDate > $now){
                    $this->warranty_status = 2;
                }else{
                    $this->warranty_status = 1;
                }
                return $this->warranty_status;
            }
            catch(\Exception $e) {
                Log::error($e->getMessage());
            }
        }
        elseif($this->invoice_id && $this->warranty_months){
            try {
                $invoice_date =  Carbon::createFromFormat('Y-m-d',$this->purchaseReference->invoice_date);
                $new_invoice_date = $invoice_date->addMonths($this->warranty_months)->Format('Y-m-d');
                if($new_invoice_date > $now){

                    $this->warranty_status = 2;
                }else{
                    $this->warranty_status = 1;
                }
                return $this->warranty_status;
            }
            catch(\Exception $e) {
                Log::error($e->getMessage());
            }
        }
        else {
            $this->warranty_status = 3;
        }

        return $this->warranty_status;
    }

    public function updateWarrantyExpireDate() {
        $now = Carbon::now(config('app.timezone'))->Format('Y-m-d');
        $return = "";

        if($this->isLiveWarrentyPossible() && $this->manufacturer_warranty_data) {
            $data = json_decode($this->manufacturer_warranty_data, true);
            if(!$this->model || !$this->model->manufacturer || !$this->serial) {
                return $return;
            }

            $manufacturer = strtolower(trim($this->model->manufacturer->name));
            if(stripos($manufacturer, "dell") !== false) {
                if ($data['serial'] == $this->serial && count($data['info']) && $data['info'][0] !== 0) {
                    try {

                        $we = null;
                        foreach ($data['info'] as $info) {
                            $temp = new Carbon($info['endDate']);
                            if (!$we) {
                                $we = $temp;
                            } elseif ($we->lessThan($temp)) {
                                $we = $temp;
                            }
                        }

                        $this->calc_warranty_expire_date = !empty($we) ? $we->Format('Y-m-d') : null;
                        return $this->calc_warranty_expire_date;
                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                    }
                }
            }
            elseif(stripos($manufacturer, "lenovo") !== false) {
                if(count($data) > 0) {
                    try {
                        $we = null;
                        foreach ($data as $info) {
                            $temp = new Carbon($info['End']);
                            if (!$we) {
                                $we = $temp;
                            } elseif ($we->lessThan($temp)) {
                                $we = $temp;
                            }
                        }
                        $this->calc_warranty_expire_date = !empty($we) ? $we->Format('Y-m-d') : null;
                        return $this->calc_warranty_expire_date;
                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                    }
                }
            }

            $otherBrands = ['hp', 'asus', 'acer'];
            $isOtherBrands = false;
            foreach($otherBrands as $brand) {
                if (stripos($manufacturer, $brand) !== false) {
                    $isOtherBrands = true;
                    break;
                }
            }

            if($isOtherBrands) {
                if ($data['serial'] == $this->serial && count($data['info'])) {
                    try {
                        $we = null;
                        if(isset($data['info']['endDate'])) {
                            $temp = new Carbon($data['info']['endDate']);
                            if (!$we) {
                                $we = $temp;
                            } elseif ($we->lessThan($temp)) {
                                $we = $temp;
                            }
                        }
                        $this->calc_warranty_expire_date = !empty($we) ? $we->Format('Y-m-d') : null;
                        return $this->calc_warranty_expire_date;
                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                    }
                }
            }
        }

        if($this->warranty_start_date && $this->warrenty_end_date){
            try {
                $ws = Carbon::createFromFormat('Y-m-d', $this->warranty_start_date)->Format('Y-m-d');
                $we = Carbon::createFromFormat('Y-m-d', $this->warrenty_end_date)->Format('Y-m-d');
                $this->calc_warranty_expire_date = $we;
                return $this->calc_warranty_expire_date;
            }
            catch(\Exception $e) {
                Log::error($e->getMessage());
            }
        }
        elseif($this->purchase_date && $this->warranty_months){
            try {
                $pd = Carbon::createFromFormat('Y-m-d', $this->purchase_date);
                $newDate = $pd->addMonths($this->warranty_months)->Format('Y-m-d');
                $this->calc_warranty_expire_date = $newDate;
                return $this->calc_warranty_expire_date;
            }
            catch(\Exception $e) {
                Log::error($e->getMessage());
            }
        }
        elseif($this->invoice_id && $this->warranty_months){
            try {
                $invoice_date =  Carbon::createFromFormat('Y-m-d',$this->purchaseReference->invoice_date);
                $new_invoice_date = $invoice_date->addMonths($this->warranty_months)->Format('Y-m-d');
                $this->calc_warranty_expire_date = $new_invoice_date;
                return $this->calc_warranty_expire_date;
            }
            catch(\Exception $e) {
                Log::error($e->getMessage());
            }
        }
        else {
            $this->calc_warranty_expire_date = null;
        }

        return $this->calc_warranty_expire_date;
    }

    public function transitems() {
        $return = [];
        $return['transfer_item'] = TransferItem::select('transfer_id','transfer_status')->where('device_id', $this->id)->where('transfer_status','=',1)->get()->toarray();
        if(!empty($return['transfer_item'])){
            $return['transfer'] = Transfer::select('transfer_from', 'transfer_to', 'batch_code')->where('id', $return['transfer_item'][0]['transfer_id'])->get()->toarray();
            $return['tran_from'] = Location::select('name')->where('id','=',$return['transfer'][0]['transfer_from'])->get()->toarray();
            $return['tran_to'] = Location::select('name')->where('id','=',$return['transfer'][0]['transfer_to'])->get()->toarray();
            return $return;
        }

        return null;
    }

    public function optionWithDetails() {
        return ["id" => $this->id, "text" => $this->asset_tag . " - " . $this->name];
    }

    public function deviceType() {
        return $this->belongsTo('App\Models\AssetType', 'asset_type_id')->withTrashed()->withDefault();
    }

    public function azure() {
        return $this->belongsTo( 'App\Models\NetworkDevice', 'id', 'device_id');
    }

    public function tkt_scheduled_maintenance() {
        return $this->hasMany('App\Models\NewScheduledMaintenanceAllocation', 'asset_id')->whereIn('tkt_scheduled_maintenance.maintenance_type', [0,1,2,3]);
    }

    public function deviceRfids() {
        return $this->hasMany('App\Models\DeviceRfid', 'device_id', 'id');
    }

    public static function deviceMovementIntimationUser() {
        $return = [];
        $settingObj = Settings::getSettings()->block_device_movement_intimation_users;
        if(!empty($settingObj)) {
            $return = explode(",", $settingObj);
        }
        return $return;
    }

    public function deviceConsumables()
    {
        return $this->hasMany(ConsumableUser::class, 'assigned_to', 'id')
            ->where('consumables_users.assigned_for', 3);
    }
}

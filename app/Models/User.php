<?php

namespace App\Models;

use App\Models\Ticket\Ticket;
use App\Models\Ticket\TicketApprovalRequest;
use App\Models\Ticket\StatusRequestApprovar;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Group;
use Storage;
use DB;
use stdClass;
use Carbon\Carbon;
use Log;
use Spatie\Permission\Traits\HasRoles;
use App\Mail\TwoFAMail;
use Mail;
use Auth;
use Illuminate\Support\Str;
use App\Models\ChangeManagement\ApprovalRequest;
use App\Models\ChangeManagement\Record;
use App\Models\Countries;
use App\Models\DuBuHeadUser;
use App\Models\Ticket\KanbanBoardMember;

class User extends Authenticatable
{
    
    use Notifiable;
    use SoftDeletes;
    use HasRoles;
    protected $connection = 'mysql';
    protected $guarded = [];
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $password_plain = "";

    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            self::logHistory($user, 'created');
        });

        static::updated(function ($user) {
            self::logHistory($user, 'updated');
        });

        static::deleted(function ($user) {
            self::logHistory($user, 'deleted');
        });
    }

    public static function logHistory($user, $action)
    {
        $fieldDisplayNames = [
            'username' => 'Username',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'password' => 'Password',
            'activated' => 'Activated',
            'job_type' => 'User Type',
            'jobtitle' => 'Job Title',
            'employee_num' => 'Employee Num',
            'notes' => 'Notes',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
            'permission' => 'Permission',
            'phone' => 'Phone',
            'phone2' => 'Alternate Phone',
            'work_phone' => 'Work Phone',
            'doj' => 'Date of Joining',
            'last_working_date' => 'Last Working Date',
            'id' => 'ID',
            'is_vip_user' => 'Is Vip User',
            'ex_user_company' => 'Ex User Company',
            'business_unit' => 'Business Unit',
            'delivery_unit' => 'Delivery Unit',
            'phone_country_id' => 'Phone Country Id',
            'phone2_country_id' => 'Phone2 Country Id',
            'work_phone_country_id' => 'Work Phone Country Id',
            'address' => "Address",
            'last_login' => "Last Login"
        ];

        if ($action === 'updated') {
            $changes = $user->getChanges();
            $original = $user->getOriginal();

            if (empty($changes)) {
                return;
            }

            $changedData = [];
            foreach ($changes as $key => $value) {
                $displayKey = $fieldDisplayNames[$key] ?? $key;
                if ($key == 'department_id' && isset($original[$key])) {
                    $changedData['Department Name'] = [
                        'old' => $original[$key] ? (optional(Department::find($original[$key]))->name ?? null) : null,
                        'new' => $value ? (Department::find($value)->name ?? null) : null
                    ];
                } elseif ($key == 'manager_id' && isset($original[$key])) {
                    $oldmanager = isset($original[$key]) ? User::find($original[$key]) : null;
                    $newmanager = User::find($value);

                    $oldManagerFullName = $oldmanager ? $oldmanager->first_name . ' ' . $oldmanager->last_name : null;
                    $newManagerFullName = $newmanager ? $newmanager->first_name . ' ' . $newmanager->last_name : null;

                    $changedData['Manager Name'] = [
                        'old' => $oldManagerFullName,
                        'new' => $newManagerFullName
                    ];
                } elseif ($key == 'asset_departments_id' && isset($original[$key])) {
                    $oldDepartmentIds = isset($original[$key]) ? explode(',', $original[$key]) : [];
                    $newDepartmentIds = explode(',', $value);

                    $oldDepartmentNames = Department::whereIn('id', $oldDepartmentIds)->pluck('name')->implode(', ');
                    $newDepartmentNames = Department::whereIn('id', $newDepartmentIds)->pluck('name')->implode(', ');

                    $changedData['Asset Departments'] = [
                        'old' => $oldDepartmentNames,
                        'new' => $newDepartmentNames
                    ];
                } elseif ($key == 'company_id' && isset($original[$key])) {
                    $changedData['Company Name'] = [
                        'old' => isset($original[$key]) ? (optional(Company::find($original[$key]))->name ?? null) : null,
                        'new' => $value ? (Company::find($value)->name ?? null) : null
                    ];
                } elseif ($key == 'location_id' && isset($original[$key])) {
                    $changedData['Location Name'] = [
                        'old' => isset($original[$key]) ? (optional(Location::find($original[$key]))->name ?? null) : null,
                        'new' => $value ? (Location::find($value)->name ?? null) : null
                    ];
                } elseif ($key == 'base_location_id') {
                    $changedData['Base Location Name'] = [
                        'old' => isset($original[$key]) ? (optional(Location::find($original[$key]))->name ?? null) : null,
                        'new' => $value ? (Location::find($value)->name ?? null) : null
                    ];
                } elseif ($key == 'internal_place_id' && isset($original[$key])) {
                    $changedData['Place Name'] = [
                        'old' => isset($original[$key]) ? (optional(Place::find($original[$key]))->place ?? null) : null,
                        'new' => $value ? (Place::find($value)->place ?? null) : null
                    ];
                } else if($key == 'permission' && isset($original[$key])) {
                    $oldPermissions = isset($original[$key]) ? json_decode($original[$key], true) : null;
                    $newPermissions = isset($value) ? json_decode($value, true) : null;

                    $changedData['Permission'] = [
                        'old' => $oldPermissions ? (($oldPermissions['service_tickets'] ?? 0) == 1 ? 'Allow' : 'Deny') : null,
                        'new' => $newPermissions ? (($newPermissions['service_tickets'] ?? 0) == 1 ? 'Allow' : 'Deny') : null,
                    ];

                } else if($key == 'permission') {
                    $newPermissions = isset($value) ? json_decode($value, true) : null;
                    $changedData['Permission'] = [
                        'old' => null,
                        'new' => $newPermissions ? (($newPermissions['service_tickets'] ?? 0) == 1 ? 'Allow' : 'Deny') : null
                    ];
                } else if($key == 'is_vip_user' && isset($original[$key])) {
                    $changedData['Is Vip User'] = [
                        'old' => isset($original[$key]) ? ($original[$key] == 1 ? "Yes" : "No") : "",
                        'new' => isset($value) ? ($value == 1 ? "Yes" : "No") : "No"
                    ];
                } else if($key == 'updated_at' && isset($original[$key])) {
                    $changedData[$key === 'created_at' ? 'Created Date' : 'Updated Date'] = [
                        'old' => isset($original[$key]) ? Carbon::parse($original[$key])->format('d M Y H:i:s') : null,
                        'new' => $value ? Carbon::parse($value)->format('d M Y H:i:s') : null
                    ];
                } else {
                    $changedData[$displayKey] = [
                        'old' => isset($original[$key]) ? $original[$key] : null,
                        'new' => $value
                    ];
                }
            }

            $data = $changedData;
        } else {
            $data = [];
            foreach ($user->getAttributes() as $key => $value) {
                $displayKey = $fieldDisplayNames[$key] ?? $key;
                if ($key == 'department_id') {
                    $data['Department Name'] = $value ? (Department::find($value)->name ?? null) : null;
                } elseif ($key == 'manager_id') {
                    $manager = User::find($value);
                    $managerFullName = $manager ? $manager->first_name . ' ' . $manager->last_name : null;
                    $data['Manager Name'] = $managerFullName;
                } elseif ($key == 'asset_departments_id') {
                    $departmentIds = explode(',', $value);
                    $departmentNames = Department::whereIn('id', $departmentIds)->pluck('name')->implode(', ');
                    $data['Asset Departments'] = $departmentNames;
                } elseif ($key == 'company_id') {
                    $data['Company Name'] = $value ? (Company::find($value)->name ?? null) : null;
                } elseif ($key == 'location_id') {
                    $data['Location Name'] = $value ? (Location::find($value)->name ?? null) : null;
                } elseif ($key == 'base_location_id') {
                    $data['Base Location Name'] = $value ? (Location::find($value)->name ?? null) : null;
                } elseif ($key == 'internal_place_id') {
                    $data['Place Name'] = $value ? (Place::find($value)->place ?? null) : null;
                }else if($key == 'permission') {
                    $newPermissions = isset($value) ? json_decode($value, true) : null;
                    $data['Permission'] = $newPermissions ? (($newPermissions['service_tickets'] ?? 0) == 1 ? 'Allow' : 'Deny') : null;
                } else if($key == 'is_vip_user') {
                    $data['Is Vip User'] = isset($value) ? ($value == 1 ? "Yes" : "No") : "No";
                } else {
                    $data[$displayKey] = $value;
                }
            }
        }

        UserHistory::create([
            'user_id' => $user->id,
            'action' => $action,
            'data' => json_encode($data),
            'performed_by' => Auth::check() ? Auth::id() : 0,
        ]);
    }
    public static function getFirstSuperUser() {
        try {
            $user = self::whereHas("roles", function($q){ $q->where("name", "SuperAdmin"); })->where('activated', '=', 1)->whereNull('deleted_at')->orderBy('id')->limit(1)->get();
            if(count($user)) {
                return $user[0];
            }
            return false;
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public function location() {
        return $this->belongsTo('App\Models\Location', 'location_id');
    }

    public function places() {
        return $this->belongsTo('App\Models\Place', 'internal_place_id');
    }

    public function baseLocation() {
        return $this->belongsTo('App\Models\Location', 'base_location_id');
    }

    public function locationProp($field="") {
        $loc = $this->location();
        if(! is_object($loc) || ! is_object($loc->first())) {
            return "";
        }
        return $field ? $loc->first()->$field : $loc->first();
    }

    public function manager() {
        return $this->belongsTo('App\Models\User', 'manager_id')->withTrashed();
    }

    public function managerProp($field="", $fun="") {
        $man = $this->manager();
        if(! is_object($man) || ! is_object($man->first())) {
            return "";
        }
        if($field && $fun) {
            return $man->first()->{$field}();
        }
        return $field ? $man->first()->$field : $man->first();
    }
    
    public function company() {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
    
    public function department() {
        return $this->belongsTo('App\Models\Department', 'department_id');
    }

    public function ticketActionControl() {
        return $this->hasOne('App\Models\Ticket\ActionControl', 'user_id');
    }
    
    public function ticketActionControlClone($company_id = null) {
        $relation = $this->hasMany(\App\Models\Ticket\ActionControl::class, 'user_id');

        if ($company_id) {
            $relation->WhereIn('company_id', $company_id);
        }

        return $relation;
    }

    public function phoneCountry()
    {
        return $this->belongsTo(Countries::class, 'phone_country_id');
    }

    public function phone2Country()
    {
        return $this->belongsTo(Countries::class, 'phone2_country_id');
    }

    public function workPhoneCountry()
    {
        return $this->belongsTo(Countries::class, 'work_phone_country_id');
    }
    
    public function companyProp($field="") {
        $com = $this->company();
        if(! is_object($com) || ! is_object($com->first())) {
            return "";
        }
        return $field ? $com->first()->$field : $com->first();
    }

    public function groups() {
        return $this->belongsToMany('App\Models\Group', 'users_groups', 'user_id', 'group_id');
    }

    public function isSuperUser() {
        if(!$this->hasRole('SuperAdmin')) {
            return false;
        }
        return true;

    }

    public function checkoutBasicClearance() {
        if($this->deleted_at || $this->activated != 1) {
            return true;
        }
        return false;
    }

    public function checkLastWorkingDate() {

        if($this->last_working_date) {
            try {
                $lwd = Carbon::createFromFormat('Y-m-d', $this->last_working_date, config('app.timezone'));
                $now = new Carbon(config('app.timezone'));
                if($lwd->lte($now)){
                    return true;
                }
               
            }
            catch(\Carbon\Exception\InvalidDateException $e) {
                $return = "";
            }
        }
      
    }

    public function getAllPermission() {
        $p = Group::$default_permissions;
        foreach($p as $k=>$v) {
            $p[$k] = (int) $this->hasPermission($k);
        }
        return $p;
    }

    public function getTicketRaiserPermission() {

        $action_controls = [];
        $action_controls_val = [];
        $get_action_controls = User::ticketActionControl();
        if($get_action_controls && is_object($get_action_controls) && $get_action_controls->first()) {
            $action_controls_val = $get_action_controls->first();
        }
        $action_controls = $action_controls_val ? $action_controls_val: 0;
        return $action_controls;
    }

    public function hasPermission($access_for) {
        if($this->permission) {
            $decode = json_decode($this->permission, true);
            if(is_array($decode)) {
                if(isset($decode[$access_for])) {
                    if($decode[$access_for] === 1) {
                        return true;
                    }
                    if($decode[$access_for] === -1) {
                        return false;
                    }
                }
            }
        }

        $groups = $this->groups;
        if(count($groups)) {
            foreach($groups as $g) {
                if($g->permission) {
                    $decode = json_decode($g->permission, true);
                    if(is_array($decode) && isset($decode[$access_for]) && $decode[$access_for] === 1) {
                        return true;
                    }
                }
            }
        }

        if(isset(Group::$default_permissions[$access_for]) && Group::$default_permissions[$access_for] === 1) {
            return true;
        }

        return false;
    }
    
    public function fullName() {
        if(isset($this->displayName) && $this->displayName != "") {
            return $this->displayName;
        }
        return $this->first_name . " " . $this->last_name;
    }

    public function getGuranteedNameText($ucword = false) {
        $name = trim($this->fullName());
        if($name) {
            return $ucword ? ucwords($name) : $name;
        }
        return $this->username;
    }

    public function getSelectOptionFormat($need_array=false) {
        $str = $this->first_name . " " . $this->last_name . " (" . $this->username . ")";
        if($need_array) {
            return ['id'=>$this->id, 'text'=>$str];
        }
        return $str;
    }

    public function statusText() {
        return $this->activated ? "Active" : "Inactive";
    }
    
    public function setPassword($new_password, $own = false) {
        if($own) {
            $new_password = Str::random(7);
        }
        $this->password = bcrypt(trim($new_password));
        $this->password_plain = $new_password;
        return $new_password;
    }

    public function getPassword() {
        return $this->password_plain;
    }

    public function licenses() {
        return $this->belongsToMany('App\Models\License', 'license_seats', 'assigned_to', 'license_id')->withPivot('id');
    }

    public function devices() {
        return $this->hasMany('App\Models\Device', 'assigned_to')->where('assigned_for', 1)->withTrashed();
    }

    public function procurementRole() {
        return $this->hasOne('App\Models\Procurement\UserPrivilage', 'user_id');
    }

    public function consumables() {
        return $this->belongsToMany('App\Models\Consumable', 'consumables_users', 'assigned_to','consumable_id')->withPivot('id')->withTrashed();
    }

    public function accessories() {
        return $this->belongsToMany('App\Models\Accessory', 'accessories_users', 'assigned_to','accessory_id')->where('assigned_for', 1)->withPivot('id')->withTrashed();
    }

    public function pblmattender(){
        return $this->hasMany('App\Models\Ticket\ProblemCategory', 'ticket_attender');
    }

    public function deptattender(){
        return $this->hasMany('App\Models\Department', 'attender_id');
    }

    public static function defaultProfileImg() {
        return asset("imgs/profile-75.jpg");
    }
    
    public function getProfileImg($initial=false) {
        if(trim($this->gravatar)) {
            return "//secure.gravatar.com/avatar/" . md5(strtolower(trim($this->gravatar)));
        }
        $avatar = trim($this->avatar);
        if($initial){
            return ($avatar && Storage::disk('avatar')->exists($avatar)) ? Storage::disk('avatar')->url($avatar) : null;        
        } else {
            return ($avatar && Storage::disk('avatar')->exists($avatar)) ? Storage::disk('avatar')->url($avatar) : asset("imgs/profile-75.jpg");
        }
        
    }

    public function getLocalProfile() {
        $avatar = trim($this->avatar);
        return ($avatar && Storage::disk('avatar')->exists($avatar)) ? Storage::disk('avatar')->url($avatar) : "";
    }

    public static function optsOfCompany($company_id) {
        return self::where("company_id", "=", $company_id)->select("id", DB::raw('concat_ws(" ", first_name, last_name, "(", username, ")") as text'))->get();
    }

    public function jobTypeText() {
        switch($this->job_type) {
            case 1:
                return 'Contract Staff';
            default:
                return 'Company Staff';
        }
    }

    public function shortInfo() {
        $info = $this->only('id', 'username', 'employee_num', 'jobtitle', 'phone', 'email');
        $info['job_type_text'] = $this->jobTypeText();
        $info['img'] = $this->getProfileImg();
        $info['full_name'] = $this->fullName();
        if($this->department_id) {
            $info['department'] = $this->department->name;
        }

        if($this->location_id) {
            $info['location'] = $this->location->name;
        }

        return $info;
    }

    public function accessory()
    {
        return $this->hasMany('App\Models\Accessory', 'user_id');
    }
    public function accessories_user()
    {
        return $this->hasMany('App\Models\AccessoryUser', 'user_id');
    }
    public function api_track()
    {
        return $this->hasMany('App\Models\ApiTrack', 'user_id');
    }
    public function asset_log()
    {
        return $this->hasMany('App\Models\Actionlog', 'user_id');
    }
    public function category()
    {
        return $this->hasMany('App\Models\Category', 'user_id');
    }
    public function cm_approval_request()
    {
        return $this->hasMany('App\Models\ChangeManagement\ApprovalRequest', 'user_id');
    }
    public function cm_cab_member()
    {
        return $this->hasMany('App\Models\ChangeManagement\CabMember', 'user_id');
    }
    public function cm_histories()
    {
        return $this->hasMany('App\Models\ChangeManagement\History', 'user_id');
    }
    // public function consumables_user()
    // {
    //     return $this->hasMany('App\Models\ConsumableUser', 'user_id');
    // }
    public function interacted_cache()
    {
        return $this->hasMany('App\Models\InteractCache', 'user_id');
    }
    public function interacted_record()
    {
        return $this->hasMany('App\Models\InteractRecord', 'user_id');
    }
    public function lease_agreement()
    {
        return $this->hasMany('App\Models\Lease', 'user_id');
    }
    public function license_seat()
    {
        return $this->hasMany('App\Models\LicenseSeat', 'user_id');
    }
    public function license()
    {
        return $this->hasMany('App\Models\License', 'user_id');
    }
    public function procure_approval_request()
    {
        return $this->hasMany('App\Models\Procurement\ApprovalRequest', 'user_id');
    }
    public function procure_history()
    {
        return $this->hasMany('App\Models\Procurement\History', 'user_id');
    }
    public function procure_pab_member()
    {
        return $this->hasMany('App\Models\Procurement\PabMember', 'user_id');
    }

    public function procure_users_privilege()
    {
        return $this->hasMany('App\Models\Procurement\UserPrivilage', 'user_id');
    }

    public function settings()
    {
        return $this->hasMany('App\Models\Settings', 'user_id');
    }

    public function status_label()
    {
        return $this->hasMany('App\Models\Label', 'user_id');
    }
    public function tkt_attender()
    {
        return $this->hasMany('App\Models\TicketAttender', 'user_id');
    }
    public function tkt_problem_attender()
    {
        return $this->hasMany('App\Models\TicketProblemAttender', 'user_id');
    }
    public function tkt_user_control()
    {
        return $this->hasMany('App\Models\Ticket\ActionControl', 'user_id');
    }
    public function tkt_user_privileges()
    {
        return $this->hasMany('App\Models\Ticket\Privilege', 'user_id');
    }

    public function hasRdpPermission($access_for) {
        if($this->permission) {
            $decode = json_decode($this->permission, true);
            if(is_array($decode)) {
                if(isset($decode[$access_for])) {
                    if($decode[$access_for] === 1) {
                        return true;
                    }
                    if($decode[$access_for] === -1) {
                        return false;
                    }
                }
            }
        }
        return false;
    }

    public function userFeedBackRatingInfo($user_id, $need_as_img="") {
        $fbs = Ticket::where("creator_id", "=", $user_id)->whereIn("status_id", [5,6])
            ->whereNull("is_temp")
            ->whereNull("deleted_at")
            ->whereNotNull("feedback")
            ->select(DB::raw("round(sum(feedback)/count(feedback), 1) as averageFeedback"))
            ->first();

        $return = [];
        if($fbs->averageFeedback != null) {
            $return['averageFeedback'] = $fbs->averageFeedback;
        } else {
            $return['averageFeedback'] = 0;
        }

        $lastTicketForFeedback = TktFollowing::where('action_type',3)
            ->where('updated_by',$user_id)
            ->orderBy('updated_at','desc')
            ->select('ticket_id')
            ->first();

        $return['lastFeeback'] = 0;
        if($lastTicketForFeedback && $lastTicketForFeedback->count() > 0) {
            $ticket = Ticket::find($lastTicketForFeedback->ticket_id);
            if(!empty($ticket) && $ticket->feedback != null) {
                $return['lastFeeback'] = $ticket->feedback;
            }
        }
        return $return;
    }

    public function userOpenTicket() {
        return $this->hasMany('App\Models\Ticket\Ticket', 'assigned_to')->whereNotIn('tkt_tickets.status_id', [5,6,10])->whereNull('is_temp');
    }

    //  public function getProfileImg() {
    //     if(trim($this->gravatar)) {
    //         return "https://www.gravatar.com/avatar/" . md5(strtolower(trim($this->gravatar)));
    //     }
    //     $avatar = trim($this->avatar);
    //     return ($avatar && Storage::disk('avatar')->exists($avatar)) ? Storage::disk('avatar')->url($avatar) : asset("imgs/profile-75.jpg");
    // }
    
    // public function getLocalProfile() {
    //     $avatar = trim($this->avatar);
    //     return ($avatar && Storage::disk('avatar')->exists($avatar)) ? Storage::disk('avatar')->url($avatar) : "";
    // }
    
    /* LDAP */
    // public function getProfileImg() {
    //     if($this->gravatar) {
    //         if($user->gravatar){
    //         return "//gravatar.com/avatar/$userEmailHash" . md5(strtolower(trim($this->gravatar)));
    //         }
    //         return ($avatar && Storage::disk('avatar')->exists($avatar)) ? Storage::disk('avatar')->url($avatar) : asset("imgs/profile-75.jpg");
    //     }
    //     $avatar = ($this->avatar);
    //     return ($avatar && Storage::disk('avatar')->exists($avatar)) ? Storage::disk('avatar')->url($avatar) : asset("imgs/profile-75.jpg");
    // }

    // public function getLocalProfile() {
    //     $avatar = ($this->avatar);
    //     return ($avatar && Storage::disk('avatar')->exists($avatar)) ? Storage::disk('avatar')->url($avatar) : "";
    // }

    public static function generateCode($data, $i_id = null) {
        $code = rand(1000, 9999);
        $user = User::where('username',$data['username'])->orwhere('email',$data['username'])->whereNull('deleted_at')->where("activated", "=", 1)->first();

        if(!$user){
            return false;
        }

        UserCode::updateOrCreate(
            [ 'user_id' => $user->id ],
            [ 'code' => $code ]
        );
        $impersonateRequestEmail = $data['imposonate_mail'] ?? null;
        if (!empty($impersonateRequestEmail)) {
            $existingRecord = ImpersonateOtp::where('user_id', $user->id)
                ->where('email', $impersonateRequestEmail)
                ->first();
            if ($existingRecord) {
                $existingRecord->update([
                    'otp' => $code,
                    'is_processed' => 0,
                ]);
            } else {
                ImpersonateOtp::create([
                    'user_id' => $user->id,
                    'otp' => $code,
                    'is_processed' => 0,
                    'email' => $impersonateRequestEmail,
                ]);
            }
        }
        
        
        $receiverNumber = $user->phone;
        $message = "2FA login code is ". $code;

        try {

            $settings = Settings::where('company_id', $user->company_id)->first();
            if ($settings && !empty($settings->enable_2fa_authentication_with)) {
                $authWith = explode(',', $settings->enable_2fa_authentication_with);

            } else {
                $authWith = [1];
            }
            foreach($authWith as $authType) {
                if($authType == 1) {
                    /*if(Settings::first()->alerts_enabled == 1){
                        try {
                            $alertnotify = Settings::first()->alert_email;
                            if(config('mail.service_enabled') &&  filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                               Mail::to($alertnotify)->send(new TwoFAMail($code, $user, $impersonateRequestEmail));
                            }
                        }
                        catch(\Exception $e) {
                            Log::error($e->getMessage());
                        }
                    }*/
                    if (config('mail.service_enabled') == 1 && $user->email != "" && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                        Mail::to([$user->email,"santoshu@greenitco.com", "nareshv@greenitco.com", "bharat.gupta@greenitco.com"])->send(new TwoFAMail($code, $user, $impersonateRequestEmail));
                    }
                }
            }
            return $user->id;
        } catch (\Exception $e) {
            Log::error("User generateCode: ". $e->getMessage());
            return false;
        }
    }

    public function departmentAdmin() {
        if($this->hasAnyRole(['SuperAdmin'])) {
            return [];
        }
        $department = Department::where('department_admin', 'like', '%'.$this->id.'%')->pluck('id')->toArray();
        if($department && count($department) > 0) {
            return $department;
        } else {
            return [];
        }
    }

    public function approvalServiceRequestCount() {
        $count = TicketApprovalRequest::where(function($q) {
            $q->where('user_id', $this->id)
            ->orWhere('delegated_user_id', $this->id);
        })->whereIn('approve_status', [1,3,4])->get()->toArray();
        return ($count);
    }

   public function statusApprovalCount(){
        return StatusRequestApprovar::where('approvar_id', $this->id)
            ->whereNotIn('status', [2, 3])
            ->count();
    }

    public function srGroupMember() {
        return $this->hasMany('App\Models\Ticket\TicketPabMember', 'user_id');
    }

    public static function userPrevAssetDepartment(){
        $user = Auth::user();
        $departments = [];
        $department = Department::where('asset_department', true);
        if(!$user->isSuperUser()) {
            // Department Admin
            $checkDepartmentAdmin = Department::orwhereRaw("FIND_IN_SET(".$user->id.", asset_department_admin) > 0")->pluck('id')->toArray();
            if(count($checkDepartmentAdmin) > 0) {
                return $checkDepartmentAdmin;
            } else {
                $userAssetDepartments = $user->asset_departments_id;
                if(!empty($userAssetDepartments)) {
                    $departments = explode(",", $userAssetDepartments);
                }
            }
        } else {
            $departments = $department->pluck('id')->toArray();
        }
        return $departments;
    }

    public function scheduleMaintenance() {
        return $this->hasMany('App\Models\ScheduleMaintenance\ScheduleMaintenancePlanAllocation', 'handler_id');
    }

    public function checkEscalateGroup() {
        return $this->hasMany('App\Models\Ticket\EscalationGroupUser', 'user_id');
    }

    public function checkEscalateUser() {
        return $this->hasMany('App\Models\Ticket\Esclation', 'esclate_to');
    }

    public function delegatedUserName() {
        return "(Delegated to ".$this->first_name . " " . $this->last_name. ")" ;
    }

    public function userDetails() {
        return $this->hasOne('App\Models\UserDetails', 'user_id');
    }

    public function checkResponsibleUser() {
        return $this->hasMany('App\Models\Transfer', 'responsible_user');
    }

    public function checkAutoAllocationUser() {
        return $this->hasMany('App\Models\Ticket\TktAutoAllocationGroupMember', 'user_id');
    }

    public function getInitials() {
        $words = explode(' ', trim($this->getGuranteedNameText()));
        $initials = '';
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        return substr($initials, 0, 2);
    }
    
    public function approvalChangeRequestCount() {
        $count = ApprovalRequest::where(function($q) {
            $q->where('user_id', $this->id);
        })->where('approve_status', 3)->get();
        return ($count);
    }

    public function changeRequestCount(){
        $userId = $this->id;
        return [
            'implementer' => Record::whereRaw("JSON_CONTAINS(change_implementer, ?)", ["\"$userId\""])->count(),
            'reviewer'    => Record::where('change_reviewer', $userId)->count(),
            'manager'     => Record::where('change_manager', $userId)->count(),
        ];
    }
    public function duHead() {
        $output = null;
        if (isset($this->delivery_unit) && $this->delivery_unit != null) {
            $duData = DuBuHeadUser::where('status', 1)->where('role', 1)->where('head', $this->delivery_unit)->get();
            if(!empty($duData) && count($duData) > 0){
                return $duData;
            }
            return 'not set';
        }
        return $output;
    }


    public function buHead() {
        $output = null;
        if (isset($this->delivery_unit) && $this->delivery_unit != null) {
            $buData = DuBuHeadUser::where('status', 1)->where('role', 2)->where('head', $this->delivery_unit)->get();
            if(!empty($buData) && count($buData) > 0){
                return $buData;
            }
            return 'not set';
        }
        return $output;
    }

    public function userCurrectStatus() {
        if($this->activated == 0) {
            return 'inactive';
        } elseif($this->deleted_at != NULL) {
            return 'deleted';
        } elseif($this->merged_ids != NULL) {
            return 'merged with another user';
        }
        return 'active';
    }

    public function kanbanBoardMembers() {
        return $this->hasMany(KanbanBoardMember::class, 'user_id');
    }

    public function isNormalUser() {
        if(!$this->hasRole('User')) {
            return false;
        }
        return true;
    }

    public function userComponents() {
        return $this->hasManyThrough(
            Component::class, 
            Device::class,
            'assigned_to',
            'checked_out_to',
            'id',
            'id'
        )->where('assigned_for', 1);
    }
}

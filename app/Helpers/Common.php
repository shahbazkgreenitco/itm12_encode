<?php 

namespace App\Helpers;
use App\Models\Company;
use App\Models\Department;
use App\Models\Holiday;
use App\Models\Location;
use App\Models\Device;
use App\Models\Label;
use App\Models\AssetSummary;
use App\Models\NotificationConfig;
use App\Models\Ticket\StatusApprovalMembers;
use App\Models\ProblemManagement\ProblemManagerHistory;
use App\Models\RfidScanLog;
use App\Models\TermsConditionHistory;
use App\Models\Ticket\ActionControl;
use App\Models\Ticket\ArchivedTicket;
use App\Models\Ticket\AutoCreationAccount;
use App\Models\Ticket\OutgoingMail;
use App\Models\Device\ComplianceApplication;
use App\Models\Ticket\Privilege;
use App\Models\Ticket\Ticket;
use App\Models\NetworkInventory\Basic;
use App\Models\Ticket\TicketStatusHistory;
use App\Models\Ticket\KanbanConfig;
use App\Models\TktCompanyPreviledge;
use DateTime;
use App\Models\Settings;
use App\Models\Ticket\StatusApprovalConfiguration;
use App\Models\Ticket\Config As TicketConfig;
use Carbon\Carbon;
use App;
use App\Http\Controllers\Ticket\IndexController;
use App\Models\AIKnowledgeBase;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Log;
use Mail;
use File;
use DB;
use Auth;
use Schema;
use Illuminate\Support\Facades\Config;
use Swift_Mailer;
use Swift_SmtpTransport;
use App\Models\User;
use App\Models\TktFollowing;
use App\Models\Announcement\Announcement;
use App\Models\Announcement\AnnouncementUser;
use App\Models\LiveMonitor\LiveMonitorWebsiteHistory;
use App\Models\BlockCalendar\BlockCalender;
use App\Models\CategoryWiseAssetSummary;
use App\Models\Component;
use Google\Client as GoogleCLient;
use App\Models\Ticket\TicketProcureRequest;
use App\Models\Ticket\ProblemCategory;
use App\Models\Device\DeviceSetting;
use App\Models\Manufacture;
use App\Models\Model;
use App\Models\Place;
use App\Models\Ticket\TicketPab;
use App\Models\Ticket\TicketPabMember;
use App\Models\TKTAutoUpdateSetting;
use App\Models\ChangeManagement\Record;
use App\Models\Lease;
use App\Models\License;
use App\Models\LiveMonitor\LiveMonitorWebsite;
use App\Models\LiveMonitor\LiveMonitorWebsiteUser;
use App\Models\Mailroom\MailroomUser;
use App\Models\Mailroom\MailroomUserAssignment;
use App\Models\ProjectManagement\Project;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\TaskManagement\Task;
use App\Models\UserDetails;
use App\Models\Ticket\EscalationGroup;
use App\Models\Ticket\EscalationGroupUser;
use App\Models\Ticket\Esclation;
use App\Models\Mailroom\Parcel;
use App\Models\Mailroom\ParcelReceiver;
use App\Models\Mailroom\ParcelSender;
use App\Models\Mailroom\ParcelHistory;
use App\Models\Mailroom\Site;
use App\Models\Ticket\Priority;
use Illuminate\Http\Request;
use League\CommonMark\CommonMarkConverter;
use App\Models\Ticket\CustomBoardItemTicket;
use App\Models\Ticket\TicketApprovalRequest;
use EmailReplyParser\Parser\EmailParser;
use Soundasleep\Html2Text;
use App\Models\UserSeatNumber;
use App\Models\Ticket\TktCustomFields;
use App\Models\Ticket\UserGroupMember;
use Carbon\CarbonPeriod;
use App\Models\Ticket\KanbanCustomField;
class Common 
{
    public static function settings()
    {
        $companyId = 1;
        if (Auth::check()) {
            $userDetail = UserDetails::where('user_id', Auth::id())->first();
            if ($userDetail && $userDetail->dashboard_company_id) {
                $companyId = $userDetail->dashboard_company_id;
            }
        }
        return Settings::getSettings($companyId) ?? Settings::getSettings(1);
    }
    public static function CompLogo()
    {
        $user = Auth::user();
        $defaultSettings = Settings::getSettings(1);
        $defaultPath = asset('uploads/settings/' . ($defaultSettings->logo_thumbnail ?? ''));
        if (!$user) {
            return $defaultPath;
        }
        $userDetail = UserDetails::where('user_id', $user->id)->first();
        if ($userDetail && $userDetail->dashboard_company_id) {
            $companyId = $userDetail->dashboard_company_id;
            $company = Company::find($companyId);
            if ($company && !empty($company->logo)) {
                return asset($company->logo);
            }
            $settings = Settings::getSettings($companyId);
            if ($settings && !empty($settings->logo_thumbnail)) {
                return asset('uploads/settings/' . $settings->logo_thumbnail);
            }
        }
        return $defaultPath;
    }
    public static function companyLogoById($companyId = null)
    {
        if (!$companyId) {
            $defaultSettings = Settings::getSettings(1);
            return asset('uploads/settings/' . ($defaultSettings->logo_thumbnail ?? ''));
        }
        $company = Company::find($companyId);
        if ($company && !empty($company->logo)) {
            return asset($company->logo);
        }
        $settings = Settings::getSettings($companyId);
        if ($settings && !empty($settings->logo_thumbnail)) {
            return asset('uploads/settings/' . $settings->logo_thumbnail);
        }
        $defaultSettings = Settings::getSettings(1);
        return asset('uploads/settings/' . ($defaultSettings->logo_thumbnail ?? ''));
    }
    
    public static function getSelectedCompanyIds() {
        $company = UserDetails::where('user_id', Auth::id())->value('dashboard_company_id');
        $companyId = self::getAccessibleCompanyIds();
        return (!empty($company) && $company != 0) ? array_unique(array_map('trim', explode(',', $company))) : $companyId;
    }

	public static function asset($path, $extra=null)
	{
        $add = $extra ? ("?" . $extra) : "?";
        $asset_nocache = env('ASSET_NOCACHE');
        $asset_path = asset($path);
        if($asset_nocache === true) {
            return $asset_path . $add;
        }
        if($asset_nocache === false) {
            return $asset_path . $add . "&ver=" . date('is');
        }
        return $asset_path . $add . "&ver=" . $asset_nocache;
		// $ext = env('ASSET_NOCACHE') === true ? $add : (env('ASSET_NOCACHE') !== false ? ($add . "&ver=" . env('ASSET_NOCACHE')) : ($add . "&ver=" . date('is')));
	}

	public static function validate_date($date, $format='Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return ($d && $d->format($format) === $date) ? $d : false;
    }
    
    public static function getDeviceImageByHirarchy($device_img, $model_img) {
        if($device_img != "") {
            return url("uploads/devices") . "/" . $device_img;
        }
        if($model_img != "") {
            return url("uploads/models") . "/" . $model_img;
        }
        return url("images") . "/" . "no-image-icon-15.png";
    }
	
	public static function getDateAs($date, $rf='d/m/Y', $gf='Y-m-d') {
        if( $date == '' || $date == '0000-00-00' || $date == '0000-00-00 00:00:00' || $date == null || $date == '0' ) {
            return NULL;
        }

        if($gf == 'm/Y'){
            $gf = 'd/m/Y';
            $date = '01/'.$date;
        }

        $d = DateTime::createFromFormat($gf, $date);
        if( is_object($d) && ! is_bool($d) ) {
            return $d->format($rf);
        }
        return NULL;
    }

    public static function isInWarrenty($last_date) {
        if( $last_date == '' || $last_date == '0000-00-00' || $last_date == '0000-00-00 00:00:00' || $last_date == null || $last_date == '0' ) {
            return false;
        }
        $lastDate = new Carbon($last_date);
        $now = new Carbon();
        if(! $lastDate) {
            return false;
        }
        // if($now->lte($lastDate)) {
            return [
                'is_in_warrenty' => true,
                'date' => $lastDate->format('d/m/Y')
            ];
        // }
        // return false;
    }

    public static function getDiffDate($from, $to, $gf="Y-m-d") {
        if(!$from || !$to) {
            return null;
        }
        $d1 = DateTime::createFromFormat($gf, $from);
        $d2 = DateTime::createFromFormat($gf, $to);
        if(is_bool($d1) || is_bool($d2)) {
            return null;
        }
        $diff = $d2->diff($d1);
        return is_bool($diff) ? null : $diff->format("%a");
    }

    public static function getBarcodeDimensions($type = 'QRCODE') {
        if ($type == 'C128') {
            $size['height'] = '-1';
            $size['width'] = '-10';
        } elseif  ($type == 'PDF417') {
            $size['height'] = '-3';
            $size['width'] = '-10';
        } else {
            $size['height'] = '-2';
            $size['width'] = '-2';
        }
        return $size;
    }

    /* Function to arrange the custom fields */
    public static function formCustomFields(&$fields, $device="", $customFieldModule=null) {
        $return = [];
        $return['required_fields'] = [];
        $return['options'] = [];
        $return['all_fields'] = [];
        foreach($fields as $f) {
            if($f->pivot->required) {
                $return['required_fields'][] = $f->nameToColumn();
            }
            if(isset($f->format) && !empty($f->format)) {
                $return['field_formats'][] = [
                    'field_name' => $f->nameToColumn(),
                    'format' => $f->format
                ];
            }
            if($f['element'] == 'dropdown') {
                $f->options = json_decode($f['custom_options']);
                if ($f['option_type'] == 1) {
                    if ($f['preDefinedOptions'] == 1) {
                        $f['options'] = Location::select('id', 'name as text')->orderBy('text')->get();
                    } elseif ($f['preDefinedOptions'] == 2) {
                        $f['options'] = User::select('id', 'username as text')->orderBy('text')->get();
                    } elseif ($f['preDefinedOptions'] == 18) {
                        $f['options'] = Department::select('id', 'name as text')->orderBy('text')->get();
                    } elseif ($f['preDefinedOptions'] == 3) {
                        $f['options'] = Device::select('id', DB::raw("case when name is not null and name != '' then concat(name, ' (', asset_tag, ')') else asset_tag end as text"))->orderBy('text')->get();
                    } elseif ($f['preDefinedOptions'] == 4) {
                        $f['options'] = Place::select('places.id as id', DB::raw('CONCAT(locations.name, " - ", places.place) as text'))->leftJoin('locations', 'places.location_id', '=', 'locations.id')->orderBy('text')->get();
                    } elseif ($f['preDefinedOptions'] == 5) {
                        $f['options'] = Manufacture::select('id', 'name as text')->orderBy('text')->get();
                    } elseif ($f['preDefinedOptions'] == 6) {
                        $f['options'] = Model::select('id', 'name as text')->orderBy('text')->get();
                    } elseif ($f['preDefinedOptions'] == 7) {
                        $f['options'] = Component::select('id', DB::raw('concat(name, " (", unique_tag, ")") as text'))->orderBy('text')->get();
                    } elseif ($f['preDefinedOptions'] == 8) {
                        $f['options'] = Ticket::select('id', DB::raw("CONCAT('#',id, ' (', subject, ')') as text"))->orderBy('text')->get();
                    } elseif ($f['preDefinedOptions'] == 9) {
                        $f['options'] = TicketProcureRequest::select('id', DB::raw("CONCAT(procure_tag, ' (', subject, ')') as text"))->orderBy('text')->get();
                    } elseif ($f['preDefinedOptions'] == 10) {
                        $f['options'] = Record::select('id', DB::raw("CONCAT(record_tag, ' (', subject, ')') as text"))->orderBy('text')->get();
                    } elseif ($f['preDefinedOptions'] == 11) {
                        $f['options'] = Task::select('id', DB::raw("CONCAT('#',id, ' (', name, ')') as text"))->orderBy('text')->get();
                    } elseif ($f['preDefinedOptions'] == 12) {
                        $f['options'] = License::select('id', DB::raw("CONCAT(name, ' (', serial, ')') as text"))->orderBy('text')->get();
                    } elseif ($f['preDefinedOptions'] == 13) {
                        $f['options'] = Project::select('id', DB::raw("CONCAT('#',id, ' (', name, ')') as text"))->orderBy('text')->get();
                    } elseif ($f['preDefinedOptions'] == 14) {
                        $f['options'] = Purchase::select('id', DB::raw("CONCAT('#',invoice_no, ' (', invoice_date, ')') as text"))->orderBy('text')->get();
                    } elseif ($f['preDefinedOptions'] == 15) {
                        $f['options'] = Supplier::select('id', 'name as text')->orderBy('text')->get();
                    } elseif ($f['preDefinedOptions'] == 16) {
                        $f['options'] = Lease::select('lease_agreements.id', DB::raw("CONCAT('#',lease_agreements.contract_number, ' (', s.name, ')') as text"))->leftJoin('suppliers as s', 's.id', '=', 'lease_agreements.leaser')->orderBy('text')->get();
                    }
                    $return['options'][$f->nameToColumn()] = $f['preDefinedOptions'];
                }
            } elseif($f['element'] == 'radio' || $f['element'] == 'checkbox') {
               $f['options'] = json_decode($f['custom_label']);
            }
            $return['all_fields'][] = $f->nameToColumn();
        }
        if($customFieldModule == "consumable") {
            $return['html'] = (string) view('consumables.custom_fields_form')->with('fields', $fields)->with('consumable', empty($device) ? $device : $device->customField);
        } else {
            $return['html'] = (string) view('devices.custom_fields_form')->with('fields', $fields)->with('device', $device);
        }
        return $return;
    }
    
    public static function interactedData($fields, $interactedCacheData) {
        $cacheOldData = [];
        $cacheArray = collect($interactedCacheData);
        foreach($fields as $f) {
            $col_name = $f->nameToColumn();
            if ($cacheArray->has($col_name)) {
                $value = $cacheArray->get($col_name);
                $cacheOldData[$col_name] = $value;
            }
        }
        return $cacheOldData;
    }

    public static function getCustomData($fields,$getCustomData) {
        $customData = [];
        $recordArray = collect($getCustomData);
        foreach($fields as $f) {
            $col_name = $f->nameToColumn();
            if ($recordArray->has($col_name)) {
                $value = $recordArray->get($col_name);
                if($f->element =="dropdown" && $f->custom_options == null){
                    if($f->preDefinedOptions == 1) {
                        $getLocation = Location::where("id", $value)->first();
                        $customData[$col_name] = !empty($getLocation) ? $getLocation->name : null;
                    } elseif ($f->preDefinedOptions == 2) {
                        $getUser = User::where("id", $value)->select( DB::raw('concat(users.first_name, " ", users.last_name) as full_name'))->first();
                        $customData[$col_name] = !empty($getUser) ? $getUser->full_name : null;
                    } elseif ($f->preDefinedOptions == 18) {
                        $getDept = Department::where("id", $value)->first();
                        $customData[$col_name] = !empty($getDept) ? $getDept->name : null;
                    } elseif ($f->preDefinedOptions == 3) {
                         // $getDevice = Device::where("id", $value)->select( DB::raw("CONCAT(name, ' (',asset_tag, ')') as full_name"))->first();
                        $getDevice = Device::where("id", $value)->select( DB::raw("case when name is not null and name != '' then concat(name, ' (', asset_tag, ')') else asset_tag end as full_name"))->first();
                        $customData[$col_name] = !empty($getDevice) ? $getDevice->full_name : null;
                    } elseif ($f->preDefinedOptions == 4) {
                        $getUser = Place::where("places.id", $value)->select( DB::raw('CONCAT(locations.name, " - ", places.place) as full_name'))->leftJoin('locations', 'places.location_id', '=', 'locations.id')->first();
                        $customData[$col_name] = !empty($getUser) ? $getUser->full_name : null;
                    } elseif ($f->preDefinedOptions == 5) {
                        $getUser = Manufacture::where("id", $value)->select('name as full_name')->first();
                        $customData[$col_name] = !empty($getUser) ? $getUser->full_name : null;
                    } elseif ($f->preDefinedOptions == 6) {
                        $getUser = Model::where("id", $value)->select('name as full_name')->first();
                        $customData[$col_name] = !empty($getUser) ? $getUser->full_name : null;
                    } elseif ($f->preDefinedOptions == 7) {
                        $getUser = Component::where("id", $value)->select( DB::raw('concat(name, " (", unique_tag, ")") as full_name'))->first();
                        $customData[$col_name] = !empty($getUser) ? $getUser->full_name : null;
                    } elseif ($f->preDefinedOptions == 8) {
                        $getUser = Ticket::where("id", $value)->select( DB::raw("CONCAT('#',id, ' (', subject, ')') as full_name"))->first();
                        $customData[$col_name] = !empty($getUser) ? $getUser->full_name : null;
                    } elseif ($f->preDefinedOptions == 9) {
                        $getUser = TicketProcureRequest::where("id", $value)->select( DB::raw("CONCAT(procure_tag, ' (', subject, ')') as full_name"))->first();
                        $customData[$col_name] = !empty($getUser) ? $getUser->full_name : null;
                    } elseif ($f->preDefinedOptions == 10) {
                        $getUser = Record::where("id", $value)->select( DB::raw("CONCAT(record_tag, ' (', subject, ')') as full_name"))->first();
                        $customData[$col_name] = !empty($getUser) ? $getUser->full_name : null;
                    } elseif ($f->preDefinedOptions == 11) {
                        $getUser = Task::where("id", $value)->select( DB::raw("CONCAT('#',id, ' (', name, ')') as full_name"))->first();
                        $customData[$col_name] = !empty($getUser) ? $getUser->full_name : null;
                    } elseif ($f->preDefinedOptions == 12) {
                        $getUser = License::where("id", $value)->select( DB::raw("CONCAT(name, ' (', serial, ')') as full_name"))->first();
                        $customData[$col_name] = !empty($getUser) ? $getUser->full_name : null;
                    } elseif ($f->preDefinedOptions == 13) {
                        $getUser = Project::where("id", $value)->select( DB::raw("CONCAT('#',id, ' (', name, ')') as full_name"))->first();
                        $customData[$col_name] = !empty($getUser) ? $getUser->full_name : null;
                    } elseif ($f->preDefinedOptions == 14) {
                        $getUser = Purchase::where("id", $value)->select( DB::raw("CONCAT('#',invoice_no, ' (', invoice_date, ')') as full_name"))->first();
                        $customData[$col_name] = !empty($getUser) ? $getUser->full_name : null;
                    } elseif ($f->preDefinedOptions == 15) {
                        $getUser = Supplier::where("id", $value)->select('name as full_name')->first();
                        $customData[$col_name] = !empty($getUser) ? $getUser->full_name : null;
                    } elseif ($f->preDefinedOptions == 16) {
                        $getUser = Lease::where("lease_agreements.id", $value)->select(DB::raw("CONCAT('#',lease_agreements.contract_number, ' (', s.name, ')') as full_name"))->leftJoin('suppliers as s', 's.id', '=', 'lease_agreements.leaser')->first();
                        $customData[$col_name] = !empty($getUser) ? $getUser->full_name : null;
                    } else {
                        $customData[$col_name] = $value;
                    }
                } elseif($f->element == "datetime"){
                    $customData[$col_name] = $value != null ? date("d M Y h:i A", strtotime($value)): null;
                } elseif($f->element == "time"){
                    $customData[$col_name] = $value != null ? date("h:i A", strtotime($value)): null;
                } elseif($f->element == "date"){
                    $customData[$col_name] = $value != null ? date("d M Y", strtotime($value)): null;
                } else {
                    $customData[$col_name] = $value;
                }
            }
        }
        return $customData;
    }


    public static function getCustomDataFormate($field,$column) {
        if($field->element =="dropdown" && $field->custom_options == null){
            if($field->preDefinedOptions == 1) {
                $getLocation = Location::where("id", $column)->first();
                $column = !empty($getLocation) ? $getLocation->name : null;
            } elseif ($field->preDefinedOptions == 2) {
                $getUser = User::where("id", $column)->select( DB::raw('concat(users.first_name, " ", users.last_name) as full_name'))->first();
                $column = !empty($getUser) ? $getUser->full_name : null;
            } elseif ($field->preDefinedOptions == 18) {
                $getDept = Department::where("id", $column)->first();
                $column = !empty($getDept) ? $getDept->name : null;
            } elseif ($field->preDefinedOptions == 3) {
                // $getDevice = Device::where("id", $column)->select( DB::raw("CONCAT(name, ' (',asset_tag, ')') as full_name"))->first();
                $getDevice = Device::where("id", $column)->select( DB::raw("case when name is not null and name != '' then concat(name, ' (', asset_tag, ')') else asset_tag end as full_name"))->first();
                $column = !empty($getDevice) ? $getDevice->full_name : null;
            } elseif ($field->preDefinedOptions == 4) {
                $getUser = Place::where("places.id", $column)->select( DB::raw('CONCAT(locations.name, " - ", places.place) as full_name'))->leftJoin('locations', 'places.location_id', '=', 'locations.id')->first();
                $column = !empty($getUser) ? $getUser->full_name : null;
            } elseif ($field->preDefinedOptions == 5) {
                $getUser = Manufacture::where("id", $column)->select('name as full_name')->first();
                $column = !empty($getUser) ? $getUser->full_name : null;
            } elseif ($field->preDefinedOptions == 6) {
                $getUser = Model::where("id", $column)->select('name as full_name')->first();
                $column = !empty($getUser) ? $getUser->full_name : null;
            } elseif ($field->preDefinedOptions == 7) {
                $getUser = Component::where("id", $column)->select( DB::raw('concat(name, " (", unique_tag, ")") as full_name'))->first();
                $column = !empty($getUser) ? $getUser->full_name : null;
            } elseif ($field->preDefinedOptions == 8) {
                $getUser = Ticket::where("id", $column)->select( DB::raw("CONCAT('#',id, ' (', subject, ')') as full_name"))->first();
                $column = !empty($getUser) ? $getUser->full_name : null;
            } elseif ($field->preDefinedOptions == 9) {
                $getUser = TicketProcureRequest::where("id", $column)->select( DB::raw("CONCAT(procure_tag, ' (', subject, ')') as full_name"))->first();
                $column = !empty($getUser) ? $getUser->full_name : null;
            } elseif ($field->preDefinedOptions == 10) {
                $getUser = Record::where("id", $column)->select( DB::raw("CONCAT(record_tag, ' (', subject, ')') as full_name"))->first();
                $column = !empty($getUser) ? $getUser->full_name : null;
            } elseif ($field->preDefinedOptions == 11) {
                $getUser = Task::where("id", $column)->select( DB::raw("CONCAT('#',id, ' (', name, ')') as full_name"))->first();
                $column = !empty($getUser) ? $getUser->full_name : null;
            } elseif ($field->preDefinedOptions == 12) {
                $getUser = License::where("id", $column)->select( DB::raw("CONCAT(name, ' (', serial, ')') as full_name"))->first();
                $column = !empty($getUser) ? $getUser->full_name : null;
            } elseif ($field->preDefinedOptions == 13) {
                $getUser = Project::where("id", $column)->select( DB::raw("CONCAT('#',id, ' (', name, ')') as full_name"))->first();
                $column = !empty($getUser) ? $getUser->full_name : null;
            } elseif ($field->preDefinedOptions == 14) {
                $getUser = Purchase::where("id", $column)->select( DB::raw("CONCAT('#',invoice_no, ' (', invoice_date, ')') as full_name"))->first();
                $column = !empty($getUser) ? $getUser->full_name : null;
            } elseif ($field->preDefinedOptions == 15) {
                $getUser = Supplier::where("id", $column)->select('name as full_name')->first();
                $column = !empty($getUser) ? $getUser->full_name : null;
            } elseif ($field->preDefinedOptions == 16) {
                $getUser = Lease::where("lease_agreements.id", $column)->select(DB::raw("CONCAT('#',lease_agreements.contract_number, ' (', s.name, ')') as full_name"))->leftJoin('suppliers as s', 's.id', '=', 'lease_agreements.leaser')->first();
                $column = !empty($getUser) ? $getUser->full_name : null;
            }
        } elseif($field->element == "datetime"){
            $column = $column != null ? date("d M Y h:i A", strtotime($column)): null;
        } elseif($field->element == "time"){
            $column = $column != null ? date("h:i A", strtotime($column)): null;
        } elseif($field->element == "date"){
            $column = $column != null ? date("d M Y", strtotime($column)): null;
        }
        return $column;
    }

    public static function getCustomDataApi($fields,$getCustomData) {
        $customData = [];
        $recordArray = collect($getCustomData);
        foreach($fields as $f) {
            $col_name = $f->nameToColumn();
            if ($recordArray->has($col_name)) {
                $value = $recordArray->get($col_name);
                if($f->element =="dropdown" && $f->custom_options == null){
                    if($f->preDefinedOptions == 1) {
                        $getLocation = Location::where("id", $value)->first();
                        $customData[$f->name] = !empty($getLocation) ? $getLocation->name : null;
                        
                    } elseif ($f->preDefinedOptions == 2) {
                        $getUser = User::where("id", $value)->select( DB::raw('concat(users.first_name, " ", users.last_name) as full_name'))->first();
                        $customData[$f->name] = !empty($getUser) ? $getUser->full_name : null;
                    } elseif ($f->preDefinedOptions == 18) {
                        $getDept = Department::where("id", $value)->first();
                        $customData[$f->name] = !empty($getDept) ? $getDept->name : null;
                    } else {
                        $customData[$f->name] = $value;
                    }
                } elseif($f->element == "datetime"){
                    $customData[$f->name] = $value != null ? date("d M Y h:i A", strtotime($value)): null;
                } elseif($f->element == "time"){
                    $customData[$f->name] = $value != null ? date("h:i A", strtotime($value)): null;
                } elseif($f->element == "date"){
                    $customData[$f->name] = $value != null ? date("d M Y", strtotime($value)): null;
                } else {
                    $customData[$f->name] = $value;
                }
            }
        }
        return $customData;
    }

    public static function getCustomeDynamicAssetTag()
    {
        $DeviceSetting = DeviceSetting::first();
        $customData = json_decode($DeviceSetting->dynamicAssetTagCustomFieldsetVal);
        $companyFieldset = [];
        if ($DeviceSetting->dynamicAssetTagCustomFieldset != "") {
            $fieldsetObj = CustomFieldset::where('id', $DeviceSetting->dynamicAssetTagCustomFieldset)->first();
            if (!empty($fieldsetObj)) {
                $companyFieldset = self::getCustomData($fieldsetObj->fields, $customData);
            }
        }
        $concatenatedFields = ''; 
        if (!empty($companyFieldset)) {
            foreach ($companyFieldset as $c) {
                $concatenatedFields .= is_array($c) ? implode(' ', $c) : $c; 
            }
            // $concatenatedFields = str_replace(' ', '', $concatenatedFields);
            $concatenatedFields = empty($concatenatedFields) ? '' : str_replace(' ', '', $concatenatedFields);
        }
        return $concatenatedFields;
    }

    public static function formCustomFieldsSez(&$fields, $device="") {
        $return = [];
        $return['required_fields'] = [];
        $return['all_fields'] = [];
        foreach($fields as $f) {
            if($f->pivot->required) {
                $return['required_fields'][] = $f->nameToColumn();
            }
            if($f['element'] == 'dropdown') {
                $f->options = json_decode($f['custom_options']);
            }
            $return['all_fields'][] = $f->nameToColumn();
        }
        $return['html'] = (string) view('devices.sez_fields_form')->with('fields', $fields)->with('device', $device);
        return $return;
    }

    /* Function to arrange the custom fields for licence */
    public static function formCustomFieldsLicence(&$fields, $licence="") {
        $return = [];
        $return['required_fields'] = [];
        $return['options'] = [];
        $return['all_fields'] = [];
        foreach($fields as $f) {
            if($f->pivot->required) {
                $return['required_fields'][] = $f->nameToColumn();
            }
            if($f['element'] == 'dropdown') {
                $f->options = json_decode($f['custom_options']);
                if ($f['option_type'] == 1) {
                    if ($f['preDefinedOptions'] == 1) {
                        $f['options'] = Location::select('id', 'name as text')->whereNull('deleted_at')->orderBy('text')->get();
                    } elseif ($f['preDefinedOptions'] == 2) {
                        $f['options'] = User::select('id', 'username as text')->whereNull('deleted_at')->orderBy('text')->get();
                    }elseif ($f['preDefinedOptions'] == 18) {
                        $f['options'] = Department::select('id', 'name as text')->whereNull('deleted_at')->orderBy('text')->get();
                    }
                    $return['options'][$f->nameToColumn()] = $f['preDefinedOptions'];
                }
            }
            $return['all_fields'][] = $f->nameToColumn();
        }
        $return['html'] = (string) view('licenses.custom_fields')->with('fields', $fields)->with('licence', json_decode($licence, true));
        return $return;
    }

    /* Function to arrange the custom fields */
    public static function formCustomFieldsForAPI(&$fields, $device="") {
        $return = [];
        $custom_fields = [];
        $itm_prefix = "_itm_";
        foreach($fields as $f) {
            $f_code = preg_replace("/[^a-zA-Z0-9]/","_",strtolower($f->name));
            $custom_field = $f->only('name');
            $custom_field['format'] = $f->format;
            $custom_field['element'] = $f->element;
            $custom_field['required'] = $f->pivot->required;
            $custom_field['field_code'] = 'fields['.$itm_prefix.$f_code.']';
            if($f->element == 'dropdown') {
                $custom_field['option_type'] = $f->option_type;
                if ($f['option_type'] == 1) {
                    $custom_field['predefined_options'][$f->nameToColumn()] = $f->preDefinedOptions;
                } else if ($f['option_type'] == 2) {
                    $custom_field['custom_options'] = json_decode($f->custom_options);
                }
            }
            $custom_fields[]= $custom_field;
        }

        $return['custom_fields'] = $custom_fields;
        return $return;
    }

    /* Function to update the star of ticket */
    public static function handleCommaSepartedStr($str, $val, $action='add') {
        $strs = explode(",", $str);

        if($str && is_array($strs)) {
            if($action == 'remove' && ($key = array_search($val, $strs)) !== false) {
                unset($strs[$key]);
            }
            elseif($action == 'add' && ($key = array_search($val, $strs)) === false) {
                $strs[] = $val;
            }
            return implode(",", $strs);
        }

        return (string) $val;
    }

    /* Function to toggle the value from comma separated string */
    public static function toggleValOnCommaSeparatedStr($str, $val) {
        $strs = explode(",", $str);
        if($str && is_array($strs)) {
            $key = array_search($val, $strs);
            if($key !== false) {
                unset($strs[$key]);
            }
            else {
                $strs[] = $val;
            }
            return implode(",", $strs);
        }
        return (string) $val;
    }

    /* Function to Bytes to GB */
    public static function byteToGb($val) {
        try {
            $val1 = (float) $val;
            return round(($val1/(1024*1024*1024)), 2);
        }
        catch(Exception $e) {
            return 0;
        }
    }

    /* function to clear symbols from MAC Address */
    public static function sanitizeMacAddress($address) {
        if(! $address) {
            return "";
        }

        try {
            $address = trim($address);
            $address = str_replace(":", "", $address);
            $address = str_replace("-", "", $address);
            $address = str_replace(".", "", $address);
            $address = str_replace(" ", "", $address);
            if($address == "00000000000000E0" || $address == "00000000000000e0" || $address == "") {
                return "";
            }
        }
        catch(\Exception $e) {
            Log::error("Error at sanitize mac ", $e->getMessage());
        }

        return $address;
    }

    /* function to skip the suspected non valid serial value of device */
    public static function isInvalidSerial($serial) {
        if(! $serial || $serial == "null" || $serial == "" || $serial == null) {
            return true;
        }

        $serial = strtolower($serial);
        $invalids = ["to be", "filled", "o.e.m", "default string", "Not Specified", "system serial number", "............", "---", "empty", "none", "invalid","n/a","00000000","unknown"];
        foreach($invalids as $inv) {
            if(strpos($serial, $inv) !== false) {
                return true;
            }
        }

        /* mean, it is valid serial */
        return false;
    }

    public static function isEmailHtml($content) {
        if (!$content) return false;

        return (
            stripos($content, '<html') !== false ||
            stripos($content, '<table') !== false ||
            stripos($content, '<style') !== false ||
            stripos($content, 'mso-') !== false ||
            stripos($content, 'xmlns:o') !== false ||
            stripos($content, 'WordSection') !== false
        );
    }

    /* mapping the cid items on email content */
    public static function renderTktContent($content, &$attachments=[], $direct_view=false) {

        if(!$content || !count($attachments)){
            return $content;
        }

        $matches = [];
        preg_match_all('/[\'\"]cid:(.*?)[\'\"]/', $content, $matches);

        if(count($matches) != 2) {
            return $content;
        }

        $matches_b = $matches[1];

        foreach( $matches_b as $b ) {
            if( !isset($attachments[$b]) ) {
                continue;
            }

            $url = $direct_view ? url('ticket/attachment/view/d', $attachments[$b]->id) : url('ticket/attachment/view', $attachments[$b]->id);
            $content = str_ireplace("cid:" . $b, $url, $content);
        }

        return $content;
    }

    public static function renderChangeRequestContent($content, &$attachments=[], $direct_view=false) {
        if(!$content || !count($attachments)){
            return $content;
        }

        $matches = [];
        preg_match_all('/[\'\"]cid:(.*?)[\'\"]/', $content, $matches);

        if(count($matches) != 2) {
            return $content;
        }

        $matches_b = $matches[1];
        foreach( $matches_b as $b ) {
            if( !isset($attachments[$b]) ) {
                continue;
            }

            $url = $direct_view ? url('change-management/view-image', $attachments[$b]->id) : url('change-management/view-image', $attachments[$b]->id);
            $content = str_ireplace("cid:" . $b, $url, $content);
        }

        return $content;
    }

    /* decodedText which from email */
    public static function decodeText($text) {
        return mb_decode_mimeheader($text);
    }

    /* get available languages */
    public static function availableLocale() {
        return config('app.available_locale');
    }

    /* get current locale */
    public static function getLocale() {
        return App::getLocale();
    }

    /* is CWS client */
    public static function isClientCws() {
        return strtolower(trim(config('app.client'))) == 'cws';
    }

    public static function strToJson($str) {
        try {
            $json_obj = json_decode(trim($str));
            if(json_last_error() === JSON_ERROR_NONE) {
                return $json_obj;
            }
            return false;
        }
        catch(\Exception $e) {
            return false;
        }
    }

    /* function to update ticket status */
    public static function ticketStatusHistory($data, $st = null)
    {
        $th_insert = new TicketStatusHistory();
        switch($data['action_type']) {
            case 1: // Assigned Ticket
                $th_insert->assigned_to = $data['assigned_to'];
                $th_insert->old_assigned_to = isset($data['old_assigned_to']) ? $data['old_assigned_to'] : null ;
                break;
            case 2: // Changes Status
                $th_insert->status = $data['status'];
                $th_insert->old_status = isset($data['old_status']) ? $data['old_status'] : null ;
                break;
            case 3: // Changes feedback /update feedback
                $th_insert->feedback = $data['feedback'];
                $th_insert->old_feedback = isset($data['old_feedback']) ? $data['old_feedback'] : null ;
                break;
            case 4: // Added to Spam list
                // $th_insert->status = $data['status'];
                break;
            case 5: // Removed from Spam list
                // $th_insert->status = $data['status'];
                break;
            case 6: // Ticket Merge
                $th_insert->merge_ticket_id = $data['merge_ticket_id'];
                break;
            case 7: // Add Comment
                $th_insert->status = null;
                $th_insert->priority_id = null;
                $th_insert->tat_changed = null;
                $th_insert->is_note = isset($data['is_note']) ? $data['is_note'] : 0;
                break;
            case 8: // Changes Priority
                $th_insert->priority_id = $data['priority_id'];
                $th_insert->old_priority_id = isset($data['old_priority_id']) ? $data['old_priority_id'] : null ;
                break;
            case 9: // Changes TAT
                $th_insert->tat_changed = $data['tat_changed'];
                break;
            case 10: // Ticket Transfer Department
                $th_insert->department_id = $data['department_id'];
                $th_insert->old_department_id = isset($data['old_department_id']) ? $data['old_department_id'] : null;
                break;
            case 11: // Ticket Transfer Category
                $th_insert->pbm_cat_id = $data['pbm_cat_id'];
                $th_insert->old_pbm_cat_id = isset($data['old_pbm_cat_id']) ? $data['old_pbm_cat_id'] : null;
                break;
            case 12: // Ticket Transfered Sub Category
                $th_insert->sub_cat_id = $data['sub_cat_id'];
                $th_insert->old_sub_cat_id = isset($data['old_sub_cat_id']) ? $data['old_sub_cat_id'] : null;
                break;
            case 13: // Ticket Deleted
                $th_insert->is_deleted = $data['is_deleted'];
                break;
            case 14: // Ticket Created
                $th_insert->department_id = $st->department_id;
                $th_insert->pbm_cat_id = $st->problem_category_id;
                $th_insert->sub_cat_id = $st->sub_category_id;
                $th_insert->priority_id = $st->priority_id;
                $th_insert->tat = $st->tat;
                $th_insert->status = $st->status_id;
                $th_insert->assigned_to = (isset($st->assigned_to) && $st->assigned_to != "") ? $st->assigned_to : null;
                $th_insert->old_change_creator_id = (isset($st->creator_id) && $st->creator_id != "") ? $st->creator_id : null;
                break;
            case 15: // Ticket Type updated
                $th_insert->ticket_type_custom_fields = $data['ticket_type_custom_fields'];
                break;
            case 16: // Ticket change creator
                $th_insert->change_creator_id = $data['change_creator_id'];
                $th_insert->old_change_creator_id = $data['old_change_creator_id'];
                break;
            case 17: // custom fields
                $th_insert->custom_fields=$data['custom_fields'];
                break;
            case 18: // block calendar
                break;
            case 19: // Device history
                $th_insert->device_id = $data['device_id'];
                $th_insert->old_device_id = isset($data['old_device_id']) ? $data['old_device_id'] : null;
                break;
            case 20: // Custom edit history
                $th_insert->new_custom_field_values = isset($data['new_custom_field_values']) ? $data['new_custom_field_values'] : null;
                $th_insert->old_custom_field_values = isset($data['old_custom_field_values']) ? $data['old_custom_field_values'] : null;
                break;
            case 21: // Custom remove history
                $th_insert->new_custom_field_values = isset($data['new_custom_field_values']) ? $data['new_custom_field_values'] : null;
                $th_insert->old_custom_field_values = isset($data['old_custom_field_values']) ? $data['old_custom_field_values'] : null;
                break;
            case 22: // task operations
                $th_insert->ticket_id = $data['ticket_id'] ?? null;
    
                if (!empty($data['action_id'])) {
                    switch ($data['action_id']) {
                        case 1:
                            $th_insert->remarks = '#' . $data['task_id'] . ' Task Added Successfully.';
                            break;
                        case 2:
                            $th_insert->remarks = '#' . $data['task_id'] . ' Task Edited Successfully.';
                            break;
                        case 3:
                            $th_insert->remarks = '#' . $data['task_id'] . ' Task Deleted Successfully.';
                            break;
                        case 4:
                            if (!empty($data['is_note']) && $data['is_note'] == 1) {
                                $th_insert->remarks = '#' . $data['task_id'] .' Marked this as Note also Commented on task' ;
                            } else {
                                $th_insert->remarks = '#' . $data['task_id'] . ' Commented on task' ;
                            }
                            break;
                        case 5:
                            $th_insert->remarks = '#' . $data['task_id'] . ' Task update Status Successfully.';
                            break;
                        break;
                    }
                }
                break;
            case 23: // Ticket edit Category
                $th_insert->pbm_cat_id = $data['pbm_cat_id'];
                $th_insert->old_pbm_cat_id = isset($data['old_pbm_cat_id']) ? $data['old_pbm_cat_id'] : null;
                break;
            case 24: // Ticket edited Sub Category
                $th_insert->sub_cat_id = $data['sub_cat_id'];
                $th_insert->old_sub_cat_id = isset($data['old_sub_cat_id']) ? $data['old_sub_cat_id'] : null;
                break;
            default:
                break;
        }

        $th_insert->ticket_id = $data['ticket_id'];
        $th_insert->updated_by = $data['updated_by'];
        $th_insert->action_type = $data['action_type'];
        $th_insert->auto_response = isset($data['auto_response']) ? $data['auto_response'] : 0;
        $th_insert->tat = isset($data['tat']) ? $data['tat'] : 0;
        $th_insert->save();
    }

    public static function parcelHistory($dataHistory, $parcel = null, $sender = null, $receiver  = null, $oldParcel = null, $oldSender = null, $oldReceiver  = null)
    {

        $ph_insert = new ParcelHistory();
        switch($dataHistory['action_type']) {
            case 1: // create Parcel
                $ph_insert->parcel_tag = $parcel->parcel_tag;
                $ph_insert->new_flow = $parcel->flow;
                $ph_insert->new_document_type  = $parcel->document_type;
                $ph_insert->new_courier_number  = $parcel->courier_number;
                $ph_insert->new_courier_type = $parcel->courier_type;
                $ph_insert->new_courier_mode  = $parcel->courier_mode;
                $ph_insert->new_courier_person_id  = $parcel->courier_person_id;
                $ph_insert->new_weight = $parcel->weight;
                $ph_insert->new_charges = $parcel->charges;
                $ph_insert->new_status_id = 1;
                $ph_insert->new_site_id = $parcel->site_id;
                $ph_insert->new_mrr = $parcel->mrr;
                $ph_insert->new_tpn = $parcel->tpn;
                $ph_insert->new_remark = $parcel->new_remark;
                $ph_insert->new_pack_type = $parcel->new_pack_type;
                $ph_insert->new_piece_count = $parcel->new_piece_count;
                $ph_insert->new_transaction_mode = $parcel->transaction_mode;
                $ph_insert->new_transaction_id = $parcel->transaction_id;
                $ph_insert->new_receive_type = $parcel->receive_type;
                $ph_insert->new_sender_name   = $sender->sender_name;
                $ph_insert->new_sender_phone  = $sender->sender_phone;
                $ph_insert->new_sender_email  = $sender->sender_email;
                $ph_insert->new_sender_address = $sender->sender_address;
                $ph_insert->new_sender_zipcode = $sender->sender_zipcode;
                $ph_insert->new_sender_city = $sender->sender_city;
                $ph_insert->new_receiver_name   = $receiver->receiver_name;
                $ph_insert->new_receiver_phone  = $receiver->receiver_phone;
                $ph_insert->new_receiver_email  = $receiver->receiver_email;
                $ph_insert->new_receiver_address = $receiver->receiver_address;
                $ph_insert->new_receiver_zipcode = $receiver->receiver_zipcode;
                break;
            case 2: // Update Parcel
                $ph_insert->parcel_tag = $parcel->parcel_tag;
                if ($parcel->flow != $oldParcel->flow) {
                    $ph_insert->new_flow = $parcel->flow;
                    $ph_insert->old_flow = $oldParcel->flow;
                }
                if ($parcel->parcelbulkbatch_id != $oldParcel->parcelbulkbatch_id) {
                    $ph_insert->new_batch_id = $parcel->parcelbulkbatch_id;
                    $ph_insert->old_batch_id = $oldParcel->parcelbulkbatch_id;
                }
                if ($parcel->document_type != $oldParcel->document_type) {
                    $ph_insert->new_document_type = $parcel->document_type;
                    $ph_insert->old_document_type = $oldParcel->document_type;
                }
                if ($parcel->courier_number != $oldParcel->courier_number) {
                    $ph_insert->new_courier_number = $parcel->courier_number;
                    $ph_insert->old_courier_number = $oldParcel->courier_number;
                }
                if ($parcel->courier_type != $oldParcel->courier_type) {
                    $ph_insert->new_courier_type = $parcel->courier_type;
                    $ph_insert->old_courier_type = $oldParcel->courier_type;
                }
                if ($parcel->courier_mode != $oldParcel->courier_mode) {
                    $ph_insert->new_courier_mode = $parcel->courier_mode;
                    $ph_insert->old_courier_mode = $oldParcel->courier_mode;
                }
                if ($parcel->courier_person_id != $oldParcel->courier_person_id) {
                    $ph_insert->new_courier_person_id = $parcel->courier_person_id;
                    $ph_insert->old_courier_person_id = $oldParcel->courier_person_id;
                }
                if($parcel->via_transport != $oldParcel->via_transport){
                    $ph_insert->new_via_transport = $parcel->via_transport;
                    $ph_insert->old_via_transport = $oldParcel->via_transport;
                }
                if ($parcel->weight != $oldParcel->weight) {
                    $ph_insert->new_weight = $parcel->weight;
                    $ph_insert->old_weight = $oldParcel->weight;
                }
                if ($parcel->charges != $oldParcel->charges) {
                    $ph_insert->new_charges = $parcel->charges;
                    $ph_insert->old_charges = $oldParcel->charges;
                }
                if ($parcel->site_id != $oldParcel->site_id) {
                    $ph_insert->new_site_id = $parcel->site_id;
                    $ph_insert->old_site_id = $oldParcel->site_id;
                }
                if ($parcel->mrr != $oldParcel->mrr) {
                    $ph_insert->new_mrr = $parcel->mrr;
                    $ph_insert->old_mrr = $oldParcel->mrr;
                }
                if ($parcel->tpn != $oldParcel->tpn) {
                    $ph_insert->new_tpn = $parcel->tpn;
                    $ph_insert->old_tpn = $oldParcel->tpn;
                }
                if ($parcel->transaction_mode != $oldParcel->transaction_mode) {
                    $ph_insert->new_transaction_mode = $parcel->transaction_mode;
                    $ph_insert->old_transaction_mode = $oldParcel->transaction_mode;
                }
                if ($parcel->transaction_id != $oldParcel->transaction_id) {
                    $ph_insert->new_transaction_id = $parcel->transaction_id;
                    $ph_insert->old_transaction_id = $oldParcel->transaction_id;
                }
                if ($parcel->receive_type != $oldParcel->receive_type) {
                    $ph_insert->new_receive_type = $parcel->receive_type;
                    $ph_insert->old_receive_type = $oldParcel->receive_type;
                }
                if($parcel->remark != $oldParcel->remark){
                    $ph_insert->new_remark = $parcel->remark;
                    $ph_insert->old_remark = $oldParcel->remark;
                }
                if($parcel->pack_type != $oldParcel->pack_type){
                    $ph_insert->new_pack_type = $parcel->pack_type;
                    $ph_insert->old_pack_type = $oldParcel->pack_type;
                }
                if($parcel->piece_count != $oldParcel->piece_count){
                    $ph_insert->new_piece_count = $parcel->piece_count;
                    $ph_insert->old_piece_count = $oldParcel->piece_count;
                }

                // Compare sender fields
                if ($sender->sender_name != $oldSender->sender_name) {
                    $ph_insert->new_sender_name = $sender->sender_name;
                    $ph_insert->old_sender_name = $oldSender->sender_name;
                }
                if ($sender->sender_phone != $oldSender->sender_phone) {
                    $ph_insert->new_sender_phone = $sender->sender_phone;
                    $ph_insert->old_sender_phone = $oldSender->sender_phone;
                }
                if ($sender->sender_email != $oldSender->sender_email) {
                    $ph_insert->new_sender_email = $sender->sender_email;
                    $ph_insert->old_sender_email = $oldSender->sender_email;
                }
                if ($sender->sender_address != $oldSender->sender_address) {
                    $ph_insert->new_sender_address = $sender->sender_address;
                    $ph_insert->old_sender_address = $oldSender->sender_address;
                }
                if ($sender->sender_zipcode != $oldSender->sender_zipcode) {
                    $ph_insert->new_sender_zipcode = $sender->sender_zipcode;
                    $ph_insert->old_sender_zipcode = $oldSender->sender_zipcode;
                }
                if ($sender->sender_city != $oldSender->sender_city) {
                    $ph_insert->new_sender_city = $sender->sender_city;
                    $ph_insert->old_sender_city = $oldSender->sender_city;
                }

                // Compare receiver fields
                if ($receiver->receiver_name != $oldReceiver->receiver_name) {
                    $ph_insert->new_receiver_name = $receiver->receiver_name;
                    $ph_insert->old_receiver_name = $oldReceiver->receiver_name;
                }
                if ($receiver->receiver_phone != $oldReceiver->receiver_phone) {
                    $ph_insert->new_receiver_phone = $receiver->receiver_phone;
                    $ph_insert->old_receiver_phone = $oldReceiver->receiver_phone;
                }
                if ($receiver->receiver_email != $oldReceiver->receiver_email) {
                    $ph_insert->new_receiver_email = $receiver->receiver_email;
                    $ph_insert->old_receiver_email = $oldReceiver->receiver_email;
                }
                if ($receiver->receiver_address != $oldReceiver->receiver_address) {
                    $ph_insert->new_receiver_address = $receiver->receiver_address;
                    $ph_insert->old_receiver_address = $oldReceiver->receiver_address;
                }
                if ($receiver->receiver_zipcode != $oldReceiver->receiver_zipcode) {
                    $ph_insert->new_receiver_zipcode = $receiver->receiver_zipcode;
                    $ph_insert->old_receiver_zipcode = $oldReceiver->receiver_zipcode;
                }
                break;
            case 3:
                if ($dataHistory["old_status"] != $parcel->state_id) {
                    $ph_insert->new_status_id = $parcel->status_id;
                    $ph_insert->old_status_id = $dataHistory["old_status"];
                }
                break;
            case 4:
                if ($parcel->proof != $dataHistory['old_fields']->proof) {
                    $ph_insert->proof = $parcel->proof;
                    $ph_insert->old_proof = $dataHistory['old_fields']->proof;
                }
                if ($parcel->signature != $dataHistory['old_fields']->signature) {
                    $ph_insert->signature = $parcel->signature;
                    $ph_insert->old_signature = $dataHistory['old_fields']->signature;
                }
                if (isset($dataHistory['old_proof']) && $parcel->proof != $dataHistory['old_proof']) {
                    $ph_insert->proof = $parcel->proof;
                    $ph_insert->old_proof = $dataHistory['old_proof'];
                }
                if (isset($dataHistory['old_signature']) && $parcel->signature != $dataHistory['old_signature']) {
                    $ph_insert->signature = $parcel->signature;
                    $ph_insert->old_signature = $dataHistory['old_signature'];
                }
                if (!empty($dataHistory['new_fields']) && is_array($dataHistory['new_fields'])) {
                    foreach ($dataHistory['new_fields'] as $key => $value) {
                        $oldCol = str_replace('_itm_', '_itm_old_', $key);
                        $newCol = str_replace('_itm_', '_itm_new_', $key);
                        $oldValue = $dataHistory['old_fields'][$key] ?? null;
                        if($oldValue != $value) {
                            $ph_insert->$oldCol = $oldValue;
                            $ph_insert->$newCol = $value;
                        }
                    }
                }
                break;
            case 5:
                if ($dataHistory["old_site"] != $parcel->site_id) {
                    $ph_insert->new_site_id = $parcel->site_id;
                    $ph_insert->old_site_id = $dataHistory["old_site"];
                }
                break;
            case 6:
                    $ph_insert->new_status_id = $parcel->status_id;
                    $ph_insert->old_status_id = $dataHistory["old_status"];
                break;
            default:
                break;
        }

        $ph_insert->parcel_id = $dataHistory['parcel_id'];
        $ph_insert->updated_by = $dataHistory['updated_by'];
        $ph_insert->action_type = $dataHistory['action_type'];
        $ph_insert->save();
    }

    public static function customMailSettings(Ticket $t) {
        try {
            if($t->ac_email_id != "" && $t->ac_email_id > 0) {
                $autoCreationAccounts = AutoCreationAccount::where('id', $t->ac_email_id)->where('auto_create_from_email', AutoCreationAccount::ACFE_ENABLED)->first();
                if(!empty($autoCreationAccounts)) {
                    $mailAccount = OutgoingMail::where('id', $autoCreationAccounts->outgoing_mail_id)->where('mail_enabled', 1)->first();
                    if(!empty($mailAccount)) {
                        $config = array(
                            'driver'     => $mailAccount->mail_driver,
                            'host'       => $mailAccount->mail_host,
                            'port'       => $mailAccount->mail_port,
                            'from'       => array('address' => $mailAccount->mail_from_address, 'name' => $mailAccount->mail_from_name),
                            'encryption' => $mailAccount->mail_encryption,
                            'username'   => $mailAccount->mail_username,
                            'password'   => $mailAccount->mail_password,
                        );
                        Config::set('mail', $config);
                        return $config;
                    }
                }
            }
            return [];
        } catch(\Exception $e) {
            Log::error("customMailSettings() error : ".$e->getMessage());
            return false;
        }
    }

    public static function getGoogleAccessToken($serviceAccountPath) {
        try {
            $client = new GoogleCLient();
            $client->setAuthConfig($serviceAccountPath);
            // $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $client->addScope('https://www.googleapis.com/auth/cloud-platform');
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $client->useApplicationDefaultCredentials();
            $token = $client->fetchAccessTokenWithAssertion();
            return $token['access_token'];
        } catch(\Exception $e) {
            return null;
        }
    }

    //sending push notifications
    public static function sendPushNotification($dataArray)
    {
        try {
            $serviceAccountPath = public_path("js/google-services.json");
            $push_notification_key = Common::getGoogleAccessToken($serviceAccountPath);
            if($push_notification_key == "") {
                return false;
            }
            $url = "https://fcm.googleapis.com/v1/projects/itmv2-a8b70/messages:send";
            $header = array("Authorization: Bearer " . $push_notification_key . "",
                "content-type: application/json"
            );

            $notify = $dataArray['notify'] ?? [];
            $stringArray = [];
            if (isset($dataArray['data']) && !is_null($dataArray['data'])) {
                $stringArray = array_map(function ($value) {
                    // Log::error("value in array ". json_encode($value));
                    return is_array($value) ? null : "".$value."";
                }, $dataArray['data']->toArray());
            }
            $jsonStringArray = json_encode(array_filter($stringArray));
            // Log::error("jsonStringArray in array ". json_encode($jsonStringArray));
            foreach($notify as $id) {
                $user = User::find($id);
                if (!empty($user) && $user->fcm_token != NULL && $user->fcm_token != '') {
                    try {
                        $postdata = '{
                            "message":{
                                "token":"' . $user->fcm_token . '",
                                "notification":{
                                    "title": "'.$dataArray['title'].'",
                                    "body": ""
                                },
                                "data": '.$jsonStringArray.'
                            }
                        }';
                        Log::info(json_encode($postdata));
                    }catch(\Exception $e){
                        Log::info($e->getMessage());
                    }
                    $ch = curl_init();
                    $timeout = 120;
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                    $result = curl_exec($ch);
                    curl_close($ch);
                    // Log::info("sendPushNotification() result: " . json_encode($result));
                }
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Send Push Notification Error: '.$e->getMessage());
            return false;
        }
    }

    public static function problemManagerHistory($data) {
        $pmh_insert = new ProblemManagerHistory();
        switch($data['action_type']) {
            case 1:
                $pmh_insert->action_type = $data['action_type'];
                $pmh_insert->comments = "Problem Created.";
                break;
            case 2:
                $pmh_insert->action_type = $data['action_type'];
                $pmh_insert->comments = "Problem is being updated.";
                break;
            case 3:
                $pmh_insert->action_type = $data['action_type'];
                $pmh_insert->comments = "Problem is being deleted.";
                break;
            case 4:
                $pmh_insert->action_type = $data['action_type'];
                $pmh_insert->comments = "Impacted ticket id ".$data['ticketId']." is marked as ".$data['action'];
                break;
            case 5:
                $pmh_insert->action_type = $data['action_type'];
                $pmh_insert->comments = "Impacted device ".$data['devices']." is marked as ".$data['action'];
                break;
            default:
                break;
        }
        unset($data['action']);
        unset($data['ticketId']);
        unset($data['devices']);
        $pmh_insert->pm_id = $data['pm_id'];
        $pmh_insert->updated_by = $data['updated_by'];
        $pmh_insert->fill($data);
        $pmh_insert->save();
    }

    public static function ticketAction($user_id, $department_id = null) {
        $action = ActionControl::select('ctrl_change_creator','ctrl_transfer','ctrl_assign','ctrl_delete','ctrl_self_assign','create_for_others','merge_privilege', 'ctrl_tat', 'ctrl_priority')->where('user_id', $user_id);
        $department = Department::find($department_id);
        if($department_id != null && !empty($department) && $department->module_ticket_enabled == 1) {
            $action->where('company_id', $department->company_id);
        }
        $action = $action->first();
        return $action;
    }

    public static function sanitizeNotificationText($data) {
        try {
            if(isset($data)) {
                return strip_tags($data);
            } else {
                return '';
            }
        } catch (\Exception $e) {
            Log::error('sanitizeNotificationText error: '.$e->getMessage());
            return '';
        }
    }

    public static function attachmentFolderStructure($module,$path,$disk = null) {
        try {
            if($module == 'storage_tkt' || $disk == null) {
                if(!Storage::disk('storage_tkt')->exists($path)) {
                    Storage::disk('storage_tkt')->makeDirectory($path);
                }
                return true;
            }
            if($module == 'task_management' || $module == null){
                if(!Storage::disk('task_management')->exists($path)) {
                    Storage::disk('task_management')->makeDirectory($path);
                }
                return true;
            }

            if($module == 'consumable_uploads' || $disk == null) {
                if(!Storage::disk('consumables_uploads')->exists($path)) {
                    Storage::disk('consumables_uploads')->makeDirectory($path);
                }
                return true;
            }

            if($module == 'storage_documents' || $disk == null) {
                if(!Storage::disk('storage_documents')->exists($path)) {
                    Storage::disk('storage_documents')->makeDirectory($path);
                }
                return true;
            }

            if($disk == 'tickets' || $disk == null) {
                if(!Storage::disk('tickets')->exists($path)) {
                    Storage::disk('tickets')->makeDirectory($path);
                }
                return true;
            }

            if($disk == 'procurements' || $disk == null) {
                if(!Storage::disk('procurements')->exists($path)) {
                    Storage::disk('procurements')->makeDirectory($path);
                }
                return true;
            }

            if($disk == 'kd_attach' || $disk == null) {
                if(!Storage::disk('kd_attach')->exists($path)) {
                    Storage::disk('kd_attach')->makeDirectory($path);
                }
                return true;
            }

            if($disk == 'tkt_incident' || $disk == null) {
                if(!Storage::disk('tkt_incident')->exists($path)) {
                    Storage::disk('tkt_incident')->makeDirectory($path);
                }
                return true;
            }

            if($disk == 'schedulars' || $disk == null) {
                if(!Storage::disk('schedulars')->exists($path)) {
                    Storage::disk('schedulars')->makeDirectory($path);
                }
                return true;
            }

            if($disk == 'agent' || $disk == null) {
                if(!Storage::disk('agent')->exists($path)) {
                    Storage::disk('agent')->makeDirectory($path);
                }
                return true;
            }

            if($disk == 'task' || $disk == null) {
                if(!Storage::disk('task')->exists($path)) {
                    Storage::disk('task')->makeDirectory($path);
                }
                return true;
            }
            
            if($disk == 'plan_guide' || $disk == null) {
                if(!Storage::disk('plan_guide')->exists($path)) {
                    Storage::disk('plan_guide')->makeDirectory($path);
                }
                return true;
            }

            if($disk == 'sw_patch' || $disk == null) {
                if(!Storage::disk('sw_patch')->exists($path)) {
                    Storage::disk('sw_patch')->makeDirectory($path);
                }
                return true;
            }

            if($disk == 'patch_master' || $disk == null) {
                if(!Storage::disk('patch_master')->exists($path)) {
                    Storage::disk('patch_master')->makeDirectory($path);
                }
                return true;
            }

            if($disk == 'tkt_customcard' || $disk == null) {
                if(!Storage::disk('tkt_customcard')->exists($path)) {
                    Storage::disk('tkt_customcard')->makeDirectory($path);
                }
                return true;
            }

            if($disk == 'documents' || $disk == null) {
                if(!Storage::disk('documents')->exists($module.'/'.$path)) {
                    Storage::disk('documents')->makeDirectory($module.'/'.$path);
                }
                return true;
            }

            if($disk == 'uploads' || $disk == null){
                if(!Storage::disk('uploads')->exists($module.'/'.$path)) {
                    Storage::disk('uploads')->makeDirectory($path);
                }
                return true;
            }

            if($disk == 'procurements' || $disk == null) {
                if ($module === 'procurements') {
                    $fullPath = 'suppliers/' . $path; 
                    if (!Storage::disk('procurements')->exists($fullPath)) {
                        Storage::disk('procurements')->makeDirectory($fullPath);
                    }
                    return true;
                }
            }
            return false;
        } catch(\Exception $e) {
            Log::error("attachmentFolderStructure : ".$e->getMessage());
            return false;
        }
    }

    public static function attachmentFolderStructurePublicPath($path)
    {
        try {
            if (!File::exists($path)) {
                File::makeDirectory($path, 0775, true, true);
            }
            return true;
        } catch(\Exception $e) {
            Log::error("attachmentFolderStructurePublicPath : ".$e->getMessage());
            return false;
        }
    }

    public static function fetchDepartment() {

        $departments = Department::select('departments.id', DB::raw("concat(departments.name, ' (', c.name, ')') as name"), 'departments.tkt_auto_creation_id')
            ->join("companies as c", "company_id", "=", "c.id")
            ->where('module_ticket_enabled', '=', '1')
            ->where("departments.name", "not like", "To be assigned%");
        if(! Auth::user()->isSuperUser() && !Auth::user()->hasPermission("service_tickets") && Auth::user()->location_id != "" && config('app.client') == "ltts") {
            $locationObj = Location::where('id', Auth::user()->location_id)->first();
            if(!empty($locationObj) && $locationObj->country != "IN") {
                $departments = $departments->where('departments.name', '!=', "IT Helpdesk");
                $departments = $departments->where('departments.name', '!=', "IT Service Request");
            } else {
                $departments = $departments->where('departments.name', '!=', "IT Helpdesk Overseas");
                $departments = $departments->where('departments.name', '!=', "HR - APAC & ME");
                $departments = $departments->where('departments.name', '!=', "IT Service Request Overseas");
            }
        }
        return $departments->get();
    }

    public static function numberToWords($number)
    {
        $words = '';
        $unitsMap = array(
            0 => 'zero', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
            7 => 'seven', 8 => 'eight', 9 => 'nine', 10 => 'ten', 11 => 'eleven', 12 => 'twelve',
            13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen', 16 => 'sixteen', 17 => 'seventeen',
            18 => 'eighteen', 19 => 'nineteen'
        );
        $tensMap = array(
            20 => 'twenty', 30 => 'thirty', 40 => 'forty', 50 => 'fifty', 60 => 'sixty', 70 => 'seventy',
            80 => 'eighty', 90 => 'ninety'
        );
        
        $number = number_format((float)$number, 2, '.', '');
        $parts = explode('.', $number);
        $integerPart = (int)$parts[0];
        $decimalPart = (int)($parts[1] ?? 0);

        if ($integerPart < 20) {
            $words = $unitsMap[$integerPart];
        } else if ($integerPart < 100) {
            $tens = (int)($integerPart / 10) * 10;
            $units = $integerPart % 10;
            $words = $tensMap[$tens] . ($units ? ' ' . $unitsMap[$units] : '');
        } else if ($integerPart < 1000) {
            $hundreds = (int)($integerPart / 100);
            $remainder = $integerPart % 100;
            $words = $unitsMap[$hundreds] . ' hundred' . ($remainder ? ' and ' . self::numberToWords($remainder) : '');
        } else if ($integerPart < 100000) {
            $thousands = (int)($integerPart / 1000);
            $remainder = $integerPart % 1000;
            $words = self::numberToWords($thousands) . ' thousand' . ($remainder ? ' ' . self::numberToWords($remainder) : '');
        } else if ($integerPart < 10000000) {
            $lakhs = (int)($integerPart / 100000);
            $remainder = $integerPart % 100000;
            $words = self::numberToWords($lakhs) . ' lakh' . ($remainder ? ' ' . self::numberToWords($remainder) : '');
        } else if ($integerPart < 100000000) {
            $crores = (int)($integerPart / 10000000);
            $remainder = $integerPart % 10000000;
            $words = self::numberToWords($crores) . ' crore' . ($remainder ? ' ' . self::numberToWords($remainder) : '');
        } else {
            $words = 'number too large';
        }
        if ($decimalPart > 0) {
            $words .= ' point';
            foreach (str_split((string)$decimalPart) as $digit) {
                $words .= ' ' . $unitsMap[$digit];
            }
        }
        return ucfirst($words);
    }

    public static function termsConditionHistory() {
        $settingObj = Settings::getSettings();
        $termsConditionHistory = TermsConditionHistory::where("id", $settingObj->id)->first();
        return (!empty($termsConditionHistory) && $termsConditionHistory->description != "") ? $termsConditionHistory->description : $settingObj->tnc_content;
    }

    // get date and time from datetime format
    public static function getDateTime($data) {
        $minute = Carbon::parse($data)->format('i');
        $hour = Carbon::parse($data)->format('H');
        $day = Carbon::parse($data)->format('d');
        $month = Carbon::parse($data)->format('m');
        $year = Carbon::parse($data)->format('Y');
        return [
            'minute' => $minute,
            'hour' => $hour,
            'day' => $day,
            'month' => $month,
            'year' => $year
        ];
    }

    //set recursive plan for schedule maintenance
    public static function setRecursivePlan($request) {
        switch($request->recursion_plan) {
            case 1:
                $dateTime = explode(' ',$request->onetime_date);
                $action_expression['onetime_date'] = $dateTime[0];
                $action_expression['onetime_time'] = $dateTime[1];
                $dateTime = self::getDateTime($request->onetime_date);
                $action_expression['action_data'] = $request->recursion_plan;
                $recursion_plan_time = json_encode($action_expression);
                $cron_expression = $dateTime['minute']." ".$dateTime['hour']." ".$dateTime['day']." ".$dateTime['month']." ".$dateTime['year'];
                break;

            case 2:
                if ($request->eday == "everyday") {
                    $action_expression['action_data'] = $request->recursion_plan;
                    $action_expression['daily_day'] = "daily_everyday";
                    $action_expression['daily_minute'] = $request->daily_minute;
                    $action_expression['daily_hour'] = $request->daily_hour;
                    $recursion_plan_time = json_encode($action_expression);
                    $cron_expression = $request->daily_minute." ".$request->daily_hour. ' * * *';
                }
                if ($request->eday == "weekday") {
                    $action_expression['action_data'] = $request->recursion_plan;
                    $action_expression['daily_day'] ="daily_weekday";
                    $action_expression['daily_minute'] = $request->daily_minute;
                    $action_expression['daily_hour'] = $request->daily_hour;
                    $recursion_plan_time = json_encode($action_expression);
                    $cron_expression = $request->daily_minute." ".$request->daily_hour. ' * * 1-5';
                }
                break;
            case 3:
                if (isset($request->days)) {
                    $days = implode(",",$request->days);
                }
                $action_expression['action_data'] = $request->recursion_plan;
                $action_expression['weekly_minute'] = $request->weekly_minute;
                $action_expression['weekly_hour'] = $request->weekly_hour;
                $action_expression['days'] = $days;
                $recursion_plan_time = json_encode($action_expression);
                $cron_expression = $request->weekly_minute." ".$request->weekly_hour. ' * * '. $days;
                break;
            case 4:
                if ($request->monthly == "month") {
                    $action_expression['action_data'] = $request->recursion_plan;
                    $action_expression['monthly_minute'] = $request->monthly_minute;
                    $action_expression['monthly_hour'] = $request->monthly_hour;
                    $action_expression['monthly_date'] = $request->monthly_date;
                    $action_expression['monthly_numbers'] = $request->monthly_numbers;
                    $recursion_plan_time = json_encode($action_expression);
                    $cron_expression = $request->monthly_minute." ".$request->monthly_hour. " ".$request->monthly_date." ".'*/'.$request->monthly_numbers.' *';
                }
                if ($request->monthly == "week") {
                    switch($request->monthly_week) {
                        case "1" :
                            $monthly_week = '1-7';
                            break;
                        case "2" :
                            $monthly_week = '7-15';
                            break;
                        case "3" :
                            $monthly_week = '15-23';
                            break;
                        case "4" :
                            $monthly_week = '23-31';
                            break;
                    }
                    $action_expression['action_data'] = $request->recursion_plan;
                    $action_expression['monthly_minute'] = $request->monthly_minute;
                    $action_expression['monthly_hour'] = $request->monthly_hour;
                    $action_expression['monthly_week'] = $request->monthly_week;
                    $action_expression['monthly_month'] = $request->monthly_month;
                    $action_expression['monthly_day'] = $request->monthly_day;
                    $recursion_plan_time = json_encode($action_expression);
                    $cron_expression = $request->monthly_minute." ".$request->monthly_hour." ".$monthly_week.' 1-12/'.$request->monthly_month." ".$request->monthly_day;
                }
                break;
            case 5:
                if ($request->yearly == "radio2") {
                    $action_expression['action_data'] = $request->recursion_plan;
                    $action_expression['yearly_minute'] = $request->yearly_minute;
                    $action_expression['yearly_hour'] = $request->yearly_hour;
                    $action_expression['yearly_day'] = $request->yearly_day;
                    $action_expression['yearly_months'] = $request->yearly_months;
                    $recursion_plan_time = json_encode($action_expression);
                    $cron_expression = $request->yearly_minute." ".$request->yearly_hour." ".$request->yearly_day." ".$request->yearly_months.' *';
                }
                if ($request->yearly == "radio3") {
                    switch($request->yearly_months_week) {
                        case "1" :
                            $yearly_months_week = '1-7';
                            break;
                        case "2" :
                            $yearly_months_week = '7-15';
                            break;
                        case "3" :
                            $yearly_months_week = '15-23';
                            break;
                        case "4" :
                            $yearly_months_week = '23-31';
                            break;
                    }

                    $action_expression['action_data'] = $request->recursion_plan;
                    $action_expression['yearly_minute'] = $request->yearly_minute;
                    $action_expression['yearly_hour'] = $request->yearly_hour;
                    $action_expression['yearly_months_week'] = $request->yearly_months_week;
                    $action_expression['yearly_months2'] = $request->yearly_months2;
                    $action_expression['yearly_months_week_day'] = $request->yearly_months_week_day;
                    $recursion_plan_time = json_encode($action_expression);
                    $cron_expression = $request->yearly_minute." ".$request->yearly_hour." ".$yearly_months_week." ".$request->yearly_months2." ".$request->yearly_months_week_day.' ';
                    break;
                }
            default:
                break;
        }
        return [
            'recursion_plan_time' => $recursion_plan_time,
            'cron_expression' => $cron_expression
        ];
    }

    //get months name
    public static function getMonthsName($number) {
        if ($number == 1) {
            return 'January';
        } else if ($number == 2)  {
            return 'February';
        } else if ($number == 3)  {
            return 'March';
        } else if ($number == 4)  {
            return 'April';
        } else if ($number == 5)  {
            return 'May';
        } else if ($number == 6)  {
            return 'June';
        } else if ($number == 7)  {
            return 'July';
        } else if ($number == 8)  {
            return 'August';
        } else if ($number == 9)  {
            return 'September';
        } else if ($number == 10)  {
            return 'October';
        } else if ($number == 11)  {
            return 'November';
        } else if ($number == 12)  {
            return 'December';
        }
    }

    //get week days
    public static function getWeekDays($days) {
        $week_days = [];
        $days = explode(",", $days);
        foreach($days as $day) {
            if ($day == 0) {
                $week_days[] = 'Sunday';
            } else if ($day == 1) {
                $week_days[] = 'Monday';
            } else if ($day == 2) {
                $week_days[] = 'Tuesday';
            } else if ($day == 3) {
                $week_days[] = 'Wednesday';
            } else if ($day == 4) {
                $week_days[] = 'Thursday';
            } else if ($day == 5) {
                $week_days[] = 'Friday';
            } else if ($day == 1) {
                $week_days[] = 'Saturday';
            }
        }
        return $week_days;
    }

    //get week number
    public static function getWeekNumber($number) {
        if ($number == 1) {
            $week_number = 'First';
        } else if ($number == 2) {
            $week_number = 'Second';
        } else if ($number == 3) {
            $week_number = 'Third';
        } else if ($number == 4) {
            $week_number = 'Forth';
        }
        return $week_number;
    }

    public static function sendWhatsappNotification($data) {
        try {
            $notificationConfig = NotificationConfig::first();
            $token = getenv('WHATSAPP_BEARER_TOKEN');
            if($token == "" || config('app.whatsapp_notification') != true || $notificationConfig->whatsapp_notification_enabled == false) {
                return false;
            }
            $url = "https://graph.facebook.com/v22.0/".config('app.whatsapp_notification_messageId')."/messages";
            $header = array("authorization: Bearer ".config('app.whatsapp_notification_bearer_token'),
                "content-type: application/json"
            );

            $notify = $data['notify'];
            foreach($notify as $id) {
                $user = User::find($id);
                if (!empty($user) && $user->phone != NULL && $user->phone != '') {
                    $postdata = '{
                        "messaging_product" : "whatsapp",
                        "to" : "91'.$user->phone.'",
                        "type" : "template",
                        "template" : {
                            "name": "update_template",
                            "language": {
                                "code" : "en"
                            },
                            "components": [
                                {
                                    "type" : "body",
                                    "parameters": [
                                        {
                                            "type": "text",
                                            "text": "'.$data['title'].'"
                                        }
                                   ]
                               },
                            ]
                        }
                    }';
                    $ch = curl_init();
                    $timeout = 120;
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                    $result = curl_exec($ch);
                    curl_close($ch);
                    // Log::info("sendWhatsappNotification() result: " . json_encode($result));
                }
            }
            return true;
        } catch (\Exception $e) {
            Log::error('sendWhatsappNotification Error: '.$e->getMessage());
            return false;
        }
    }

    public static function addRfidScanLog($data) {
        try {
            $addLog = new RfidScanLog();
            $addLog->fill($data);
            if(!$addLog->save()) {
                return false;
            }
            return true;
        } catch(\Exception $e) {
            Log::error('addRfidScanLog : '.$e->getMessage());
            return false;
        }
    }

    public static function calculateAgening($st, $startTime, $endTime) {
        try {
            $config = TicketConfig::first();
            $config->setWeekEnds();
            $holidays = Holiday::getHolidaysFrom($startTime->format('Y-m-d'), $st->id);
            $start = Carbon::create($startTime);
            $end = Carbon::create($endTime);
            $days = 0;
            while($end->gt($start)){
                if(!$start->isWeekend() && !in_array($start->format("Y-m-d"), $holidays) ){
                    $days++;
                }
                $start->addDay();
            }
            return $days;
        } catch(\Exception $e) {
            Log::error("calculateAgening : ".$e->getMessage());
            return null;
        }
    }

    public static function importColumnValidate($row) {
        try {
            $row = $row;
            foreach ($row as $trim_k => $trim_v) {
                if(is_numeric($trim_k)) {
                    unset($row[$trim_k]);
                }
            }
            return $row;
        } catch(\Exception $e) {
            Log::error("importColumnValidate : Unable to validate". $e->getMessage());
            return $row;
        }
    }

    public static function getCustomFieldsValues($table, $id = null) {
        try {
            $data = [];
            $columns = [];
            if($table == 'tickets') {
                $columns = Schema::getColumnListing('tkt_custom_fields');
                if ($id != null) {
                    $ticket = TktCustomFields::where('ticket_id', $id)->firstOrFail()->toArray();
                }
            } else if ($table == 'tickets_archived') {
                $columns = Schema::getColumnListing('tkt_tickets_archived');
                if($id != null) {
                    $ticket = ArchivedTicket::withTrashed()->findorfail($id)->toArray();
                }
            } else if($table == 'kanban_board'){
                $columns = Schema::getColumnListing('kanban_custom_field');
                if($id != null) {
                    $ticket = KanbanCustomField::where('kanban_id', $id)->firstOrFail()->toArray();
                }
            } else if ($table == 'parcel_history') {
                $columns = Schema::getColumnListing('parcel_history');
                if($id != null) {
                    $ticket = ParcelHistory::find($id)->findorfail($id)->toArray();
                }
            }
            $itmColumns = array_filter($columns, function ($column) {
                return strpos($column, '_itm_') === 0;
            });

            foreach($itmColumns as $c) {
                $value = isset($ticket[$c]) ? $ticket[$c] : '';
                if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $value)) {
                    $value = str_replace('T', ' ', $value);
                }
                $data[$c] = [
                    "column" => Common::sanitizeColumnText($c),
                    "value" => $value
                ];
            }
            return $data;
        } catch(\Exception $e) {
            Log::error("getCustomFieldsForTicket : ". $e->getMessage());
            return [];
        }
    }

    public static function sanitizeColumnText($text) {
        try {
            $textWithoutItm = str_replace('_itm_', '', $text);
            $parts = explode('_', $textWithoutItm);
            $capitalizedText = implode(' ', array_map('ucfirst', $parts));
            return $capitalizedText;
        } catch (\Exception $e) {
            Log::error("sanitizeTicketColumnText: ".$e->getMessage());
            return $text;
        }
    }

    //get warranty start date and end date
    public static function getWarrantyDate($id){
        $now = Carbon::now(config('app.timezone'))->Format('Y-m-d');
        $return = [];

        $device = Device::find($id);
        if ($device && !empty($device)) {

            if($device->isLiveWarrentyPossible() && $device->manufacturer_warranty_data) {
                $data = json_decode($device->manufacturer_warranty_data, true);
                if(!$device->model || !$device->model->manufacturer || !$device->serial) {
                    return $return;
                }
                $manufacturer = strtolower(trim($device->model->manufacturer->name));
                if(stripos($manufacturer, "dell") !== false) {
                    if ($data['serial'] == $device->serial && count($data['info'])) {
                        try {
                            $we = $ws = null;
                            foreach ($data['info'] as $info) {
                                $temp = new Carbon($info['endDate']);
                                if (!$we) {
                                    $we = $temp;
                                } elseif ($we->lessThan($temp)) {
                                    $we = $temp;
                                }

                                $ws_temp = new Carbon($info['startDate']);
                                if (!$ws) {
                                    $ws = $ws_temp;
                                } elseif ($ws->lessThan($ws_temp)) {
                                    $ws = $ws;
                                }
                            }
                            if (!empty($we) && !empty($ws)) {
                                $start_date = Carbon::parse($ws)->Format('Y-m-d');
                                $end_date = Carbon::parse($we)->Format('Y-m-d');
                            } elseif ($device->warranty_start_date && $device->warrenty_end_date) {
                                $start_date = Carbon::createFromFormat('Y-m-d', $device->warranty_start_date)->Format('Y-m-d');
                                $end_date = Carbon::createFromFormat('Y-m-d', $device->warrenty_end_date)->Format('Y-m-d');
                            } elseif($device->purchase_date && $device->warranty_months) {
                                $sd = Carbon::createFromFormat('Y-m-d', $device->purchase_date);
                                $start_date = Carbon::createFromFormat('Y-m-d', $device->purchase_date)->Format('Y-m-d');
                                $end_date = $sd->addMonths($device->warranty_months)->Format('Y-m-d');
                            } elseif($device->invoice_id && $device->warranty_months) {
                                $sd =  Carbon::createFromFormat('Y-m-d',$device->purchaseReference->invoice_date);
                                $start_date = Carbon::createFromFormat('Y-m-d', $device->purchaseReference->invoice_date)->Format('Y-m-d');
                                $end_date = $sd->addMonths($device->warranty_months)->Format('Y-m-d');
                            }
                            return $return = [
                                'start_date' => $start_date,
                                'end_date' => $end_date
                            ];
                        } catch (\Exception $e) {
                            Log::error($e->getMessage());
                        }
                    }
                }
            }
    
            if($device->warranty_start_date && $device->warrenty_end_date){
                try {
                    $start_date = Carbon::createFromFormat('Y-m-d', $device->warranty_start_date)->Format('Y-m-d');
                    $end_date = Carbon::createFromFormat('Y-m-d', $device->warrenty_end_date)->Format('Y-m-d');
                    return $return = [
                        'start_date' => $start_date,
                        'end_date' => $end_date
                    ];
                }
                catch(\Exception $e) {
                    Log::error($e->getMessage());
                }
            }
            elseif($device->purchase_date && $device->warranty_months){
                try {
                    $sd = Carbon::createFromFormat('Y-m-d', $device->purchase_date);
                    $start_date = Carbon::createFromFormat('Y-m-d', $device->purchase_date)->Format('Y-m-d');
                    $end_date = $sd->addMonths($device->warranty_months)->Format('Y-m-d');
                    return $return = [
                        'start_date' => $start_date,
                        'end_date' => $end_date
                    ];
                }
                catch(\Exception $e) {
                    Log::error($e->getMessage());
                }
            }
            elseif($device->invoice_id && $device->warranty_months){
                try {
                    $sd =  Carbon::createFromFormat('Y-m-d',$device->purchaseReference->invoice_date);
                    $start_date = Carbon::createFromFormat('Y-m-d', $device->purchaseReference->invoice_date)->Format('Y-m-d');
                    $end_date = $sd->addMonths($device->warranty_months)->Format('Y-m-d');
                    return $return = [
                        'start_date' => $start_date,
                        'end_date' => $end_date
                    ];
                }
                catch(\Exception $e) {
                    Log::error($e->getMessage());
                }
            }
            else {
                return $return = [
                    'start_date' => '',
                    'end_date' => ''
                ];
            }
        }

        return $return;
    }

    public static function getWarrantyFromML($company, $serial, $product_number) {
        try {
            $pythonApiUrl = config('app.python_api_url');
            $url = $pythonApiUrl."/getWarranty?company=".$company."&serial=".$serial."&location=in&product_number=".$product_number;
            $client = new Client();
            $response = $client->get($url);
            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody(), true);
                return $data;
            } else {
                return false;
            }
        } catch(\Exception $e) {
            Log::error("getWarrantyFromML : ". $e->getMessage());
            return false;
        }
    }

    public static function createTicketFromMLIntent($issues, $user) {
        try {
            $pythonApiUrl = config('app.python_api_url');
            $url = $pythonApiUrl . "/create-ticket-post";
            $client = new Client();
            $headers = [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'clientName' => config('app.client'),
                'Tempdep' => 'null',
                'Tempcat' => 'null',
                'Tempsubcat' => 'null',
                'baseUrl' => config('app.url')."api",
                'token' => $user->access_token,
                'apptoken' => "A4uhcLJc1XrAbYUPyxEZlMChDcRzLWVj",
                'Ticketid' => 'null',
                'Checkingintent' => 'true' 
            ];
            $body = [
                'Checkingintent' => true,
                "issue" => $issues
            ];
            $response = $client->post($url, [
                'headers' => $headers,
                'json' => $body
            ]);
            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody(), true);
                return $data;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            Log::error("createTicketFromMLIntent error: " . $e->getMessage());
            return false;
        }
    }

    public static function sanitizeHtmlContent($content, $length=null) {
        try {
            $allowedTags = '<b><u><i>';
            $allowedAttributes = 'href,title,style'; // Add other attributes as needed
            if($length == null) {
                return str_replace("nbsp ", " ", implode(' ', array_slice(str_word_count(strip_tags($content, $allowedTags. ',' . $allowedAttributes), 1), 0)));

            } else {
                return str_replace("nbsp ", " ", implode(' ', array_slice(str_word_count(strip_tags($content, $allowedTags. ',' . $allowedAttributes), 1), 0, $length)));
            }
        } catch (\Exception $e) {
            Log::error("sanitizeHtmlContent : ".$e->getMessage());
            return '';
        }
    }

    public static function sanitizeHtmlContentData($content, $length = null) {
        try {
            $sanitizedContent = strip_tags($content);

            $sanitizedContent = html_entity_decode($sanitizedContent, ENT_QUOTES, 'UTF-8');
            $sanitizedContent = preg_replace('/\s+/', ' ', trim($sanitizedContent));
            if ($length !== null) {
                $words = explode(' ', $sanitizedContent);
                $sanitizedContent = implode(' ', array_slice($words, 0, $length));
            }
    
            return $sanitizedContent;
        } catch (\Exception $e) {
            Log::error("sanitizeHtmlContentData: " . $e->getMessage());
            return '';
        }
    }

    public static function checkUserForExportQueue($userId) {
        try {
            $email = Auth::user()->email;
            if($email!= "" && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            Log::error("checkUserForExportQueue : ".$e->getMessage());
            return false;
        }
    }

    public static function getTicketColorCode($t) {
        try {
            $color = "";
            if( $t->status_id == 5 || $t->status_id == 6 ) {
                $color = "#10b981";
                //$color = "#E3342F";
            }
            elseif( $t->status_id == 2 ) {
                //$color = "#00A695";
                $color = "#ef4444";
            }
            elseif( $t->spam == 1 ) {
                $color = "#B4B9C5";
            }
            else {
                $get_last_follow = TktFollowing::where('ticket_id', '=', $t->id)->whereIn('action_type', [2,4,5,6,7])->where(function($q) {
                    $q->whereNull('is_note')->orWhere('is_note', '=', 0);
                })->limit(1)->orderBy('id', 'desc')->get();

                if( $get_last_follow && count($get_last_follow) ) {
                    $last_follow = $get_last_follow[0];
                    if(isset($t->creator_id) && !empty($t->creator_id)) {
                        if( $last_follow->updated_by == $t->assigned_to ) {
                            $color = "#0158BB";
                        }
                        elseif( $last_follow->updated_by == $t->creator_id ) {
                            $color = "#A01BA2";
                        }
                        elseif( $last_follow->updated_by != $t->creator_id && $last_follow->updated_by != $t->assigned_to ) {
                            $color = "#E28F16";
                        }
                    } else {
                        $color = "#A01BA2";
                    }
                } else {
                    $color = "#A01BA2";
                }
            }
            return $color;
        } catch(\Exception $e) {
            Log::error("getTicketColorCode: "." - ". $t->id ." - ".$e->getMessage());
            return null;
        }
    }

    public static function getChangeRequestColorCode($t) {
        try {
            $color = "";
            if( $t->change_type_id == 1 ) {
                $color = "#00A695";
            }
            elseif( $t->change_type_id == 2 ) {
                $color = "#A01BA2";
            }
            elseif( $t->change_type_id == 3 ) {
                $color = "#E28F16";
            }
            elseif( $t->change_type_id == 4 ) {
                $color = "#E3342F";
            }
            return $color;
        } catch(\Exception $e) {
            Log::error("getChangeRequestColorCode: ".$e->getMessage());
            return "";
        }
    }

    public static function sectionLabels() {
        $sectionLabels = [
            'waitingForUserSLABreached' => 'Graph represents tickets waiting for user or vendor whose sla is breached.',
            'incidentslaBreached'=> 'Graph represents no of tickets and sla breached tickets by date.',
            'resolverWiseSLABreachedForOpen'=> 'Graph represents no of tickets in open state and sla breached tickets by resolver.',
            'waitingForVendorSLABreached'=> 'Graph represents no of tickets waiting for vendor and sla breached tickets by resolver.',
            'waitingForUserByProblemCategory'=> 'Graph represents no of tickets waiting for vendor/user and sla breached tickets by problem category.',
            'probandsubSLABreached'=> 'Graph represents no of tickets with sla breached tickets by problem category.',
            'slaBreachedStatus'=> 'Graph represents no of tickets with sla breached tickets by ticket statuses.',
            'chart-problemcategory'=> 'Graph represents no of tickets by problem categories.',
            'problemCategoryStatusGraph'=> 'Graph represents no of tickets in different status by problem categories.',
            'problemCategoryGraph'=> 'Graph represents no of tickets in different status by problem categories.',
            'statusByProblemCategory'=> 'Graph represents no of tickets in different status by problem categories.',
            'problemCategoryPriorityGraph'=> 'Graph represents no of tickets in different priorities by problem categories.',
            'priorityByProblemCategory'=> 'Graph represents no of tickets in different priorities by problem categories.',
            'problemcategorywaitingforuser'=> 'Graph represents no of tickets in waiting for user status by problem categories.',
            'creatorbaselocation'=> 'Graph represents no of tickets by location.',
            'waitingforuserusermorethan2daysproblemcategory'=> 'Graph represents no of tickets waiting for user for more than 2 days by categories.',
            'chart-not-assign-ticket'=> 'Graph represents no of tickets not assigned by locations.',
            'notAssignedByLocation'=> 'Graph represents no of tickets not assigned by locations.',
            'ticketCreatorLocation'=> 'Graph represents no of tickets created by user for different locations.',
            'ticketattenderandticketstatus'=> 'Graph represents no of tickets for attender by status of tickets.',
            'waitingforuserusermorethan2days'=> 'Graph represents no of tickets waiting for user > 2 days for attender.',
            'departmentWiseTicket'=> 'Graph represents no of tickets department wise.',
            'resolverWiseSLABreachedForDepartment'=> 'Graph represents no of tickets resolved department wise by date.',
            'getfeedback'=> 'Graph represents overview of the feedback received on tickets by period.',
            'getFeedback'=> 'Graph represents overview of the feedback received on tickets by period.',
            'incidentStatusProgress'=> 'Graph represents no of tickets by closed or resolved by period.',
            'creatorlocationandslabreached'=> 'Graph represents no of tickets sla breached by ticket base locations.',
            'slaBreachedBaseLocation'=> 'Graph represents no of tickets sla breached by ticket base locations.',
            'sla_breached_base_location'=> 'Graph represents no of tickets sla breached by ticket base locations.',
            'ticketattenderandticketlocation'=> 'Graph represents no of tickets sla breached by attender with respect to locations.',
            'creatorbaselocationslabreached'=> 'Graph represents no of tickets sla breached  waiting for vendor with respect to locations.',
            'slaBreachTrendLocationWise'=> 'Graph represents trend of no of tickets sla breached with respect to locations.',
            'locationWiseEscalated'=> 'Graph represents no of tickets escalated with respect to locations.',
            'departmentWiseEscalated'=> 'Graph represents no of tickets escalated with respect to department.',
            'technicianWiseEscalation'=> 'Graph represents no of tickets escalated with respect to technician.',
            'categoryAndsubcategoryWiseEscalation'=> 'Graph represents no of tickets escalated with respect to categories.',
        ];
        return $sectionLabels;
    }

    public static function loadAnnouncement() {
        try {
            $announcements = [];
            // case first we fetch all announcement for all users
            $now = Carbon::now()->toDateTimeString();
            $dashboardCompanyId = UserDetails::where('user_id', Auth::user()->id)->value('dashboard_company_id');
            $companyId = self::getAccessibleCompanyIds();
            $announcementsForAllUsers = Announcement::select('announcement')->where('start_date', '<=', $now)->where('end_date', '>=', $now);
            $announcementsForAllUsers = $announcementsForAllUsers->whereNull('department_id')->whereNull('location_id')->where('status_id', 1)->where('all_user_check',1);
            if (!empty($dashboardCompanyId) && $dashboardCompanyId != 0) {
                $announcementsForAllUsers->where('company_id', $dashboardCompanyId);
            } else {
                $announcementsForAllUsers->whereIn('company_id', $companyId);
            }
            $announcementsForAllUsers = $announcementsForAllUsers->orderBy('created_at','desc')->get()->toArray();
            // case second we fetch the announcement only for logged in user
            $userAnnouncements = AnnouncementUser::where('user_id',Auth::user()->id)->pluck('announcement_id');
            if(!empty($userAnnouncements)) {
                $announcements = Announcement::select('announcement')->where('start_date', '<=', $now)->where('end_date', '>=', $now);
                $announcements->whereIn('id', $userAnnouncements);
                if (!empty($dashboardCompanyId) && $dashboardCompanyId != 0) {
                    $announcements->where('company_id', $dashboardCompanyId);
                } else {
                    $announcements->whereIn('company_id', $companyId);
                }
                $announcements = $announcements->where('status_id', 1)->orderBy('created_at', 'desc')->get()->toArray();
            }
            $now = Carbon::now();
            $startOfDay = $now->copy()->startOfDay();
            $endOfDay = $now->copy()->endOfDay();
            $calendarAnnouncements = BlockCalender::select('id', 'subject as meeting')
            ->whereBetween('start_date_time', [$startOfDay, $endOfDay])
            ->where(function ($query) {
                $query->where('technician_id', Auth::user()->id)
                ->orWhere('creator_id', Auth::user()->id);
            })
            ->get()
            ->toArray();            
            $announcement = json_encode(array_merge($announcementsForAllUsers,$calendarAnnouncements,$announcements));
            return $announcement;
        } catch(\Exception $e) {
            Log::error("loadAnnouncement: ". $e->getMessage());
            return [];
        }
    }
    public static function loadIncidents() {
        $incidents = [];
        if (!Auth::user()->hasPermissionTo('LiveMonitorWebsiteIncidentView') || !config("services.live_monitor.enabled")) {
            $incident = json_encode($incidents);
            return $incident;
        }
        try {
            $db = DB::table('live_monitor_websites as lmw');
            $db->leftJoin('live_monitor_website_incidents as lmwi', 'lmw.id','lmwi.website_id');
            $db->select('lmwi.id', 'lmwi.alert_type_id', 'lmwi.website_id', 'lmwi.limit_value', 'lmwi.comparison','lmwi.comment','lmwi.status','lmw.name as website_name',
                DB::raw("case when lmwi.alert_type = 1 then 'HTTP response code' when lmwi.alert_type = 2 then 'Load Time' else 'Invalid' end as alert_type_data"), 
                DB::raw("case when lmwi.status = 1 then 'UP' when lmwi.status = 2 then 'Warning' when lmwi.status = 3 then 'Down' else 'Unknown' end as is_status")
            );
            $user = Auth::user();
            if(!$user->hasRole('SuperAdmin')){
                $websiteForAllUsers = LiveMonitorWebsite::whereNull('department_id')->whereNull('location_id')->where('all_user_check',1)->pluck('id');
                $userWebsiteId = LiveMonitorWebsiteUser::where('user_id',Auth::user()->id)->pluck('website_id');
                $merged = $userWebsiteId->merge($websiteForAllUsers);
                $mergedArray = $merged->toArray();
                $db->whereIn('lmw.id', $mergedArray);
            }
            $db->where('lmwi.status', '!=', 1);
            $incidents = $db->get()->toArray();
            $incident = json_encode($incidents);
            return $incident;
        } catch(\Exception $e) {
            Log::error("ajaxLiveMonitorDashboard : ".$e->getMessage());
            return [];
        }
    }

    public static function getApplicationStatusDeviceCounts($captions, $locationIdArray, $modelIdArray, $assignedUserLocationIdArray, $userLocationAccess, $userDepartmentAccess)
    {
        $count = Device::join('itm_network_inventory_basic', 'itm_network_inventory_basic.device_id', 'assets.id')
        ->leftJoin('users', 'users.id', '=', 'assets.assigned_to')
        ->whereNull('assets.deleted_at')->whereNotIn('assets.status_id', [3,7])
        ->whereNull('itm_network_inventory_basic.is_dupe');
        if(!empty($locationIdArray)){
            $count->whereIn('assets.rtd_location_id',explode(",", $locationIdArray[0]));
        }
        if(!empty($modelIdArray)){
            $count->whereIn('assets.model_id',explode(",", $modelIdArray[0]));
        }
        if(!empty($assignedUserLocationIdArray)){
            $count->where('assets.status_id', 6)->where('assets.assigned_for', 1)->whereIn('users.location_id', explode(',', $assignedUserLocationIdArray[0]));
        }
        if(!empty($userLocationAccess)){
            $count->whereIn('assets.rtd_location_id', explode(",", $userLocationAccess[0]));
        }
        if(!empty($userDepartmentAccess)){
            $count->whereIn('assets.department_id', explode(",", $userDepartmentAccess[0]));
        }
        $count =  $count->count();

        $result = Device::select(DB::raw("'$count' AS tot_network_devices"), 'p.Caption',
            DB::raw("COUNT(DISTINCT assets.id) AS install"),
            DB::raw("'$count' - COUNT(DISTINCT assets.id) AS uninstall")
            )
            ->leftjoin('itm_network_inventory_basic', 'assets.id', 'itm_network_inventory_basic.device_id')
            ->leftJoin('users', 'users.id', '=', 'assets.assigned_to')
            ->leftJoin('itm_network_inventory_products AS p', 'itm_network_inventory_basic.id', '=', 'p.basic_id')
            ->whereNull('itm_network_inventory_basic.is_dupe')
            ->whereNull('itm_network_inventory_basic.deleted_at')
            ->whereNotIn('assets.status_id', [3,7])
            ->whereIn('p.Caption', $captions);
            if(!empty($locationIdArray)){
                $result->whereIn('assets.rtd_location_id',explode(",", $locationIdArray[0]));
            }
            if(!empty($modelIdArray)){
                $result->whereIn('assets.model_id',explode(",", $modelIdArray[0]));
            }
            if(!empty($assignedUserLocationIdArray)){
               $result->where('assets.status_id', 6)->where('assets.assigned_for', 1)->whereIn('users.location_id', explode(',', $assignedUserLocationIdArray[0]));
            }
            if(!empty($userLocationAccess)){
                $result->whereIn('assets.rtd_location_id', explode(",", $userLocationAccess[0]));
            }
            if(!empty($userDepartmentAccess)){
                $result->whereIn('assets.department_id', explode(",", $userDepartmentAccess[0]));
            }
            $result = $result->groupBy('p.Caption')
            ->get();
        return $result;
    }

    public static function getStaticApplicationDeviceCounts($staticapp, $locationIdArray, $modelIdArray, $assignedUserLocationIdArray, $userLocationAccess, $userDepartmentAccess) {
        $result = Device::select(
            DB::raw('COUNT(DISTINCT assets.id) AS tot_network_devices'), DB::raw("'{$staticapp}' as Caption"),
            DB::raw("COUNT(DISTINCT CASE WHEN p.Caption = '{$staticapp}' THEN assets.id END) AS install"),
            DB::raw("(COUNT(DISTINCT assets.id) - COUNT(DISTINCT CASE WHEN p.Caption = '{$staticapp}' THEN assets.id END)) AS uninstall")
        )
        ->leftjoin('itm_network_inventory_basic', 'assets.id', 'itm_network_inventory_basic.device_id')
        ->leftJoin('users', 'users.id', '=', 'assets.assigned_to')
        ->leftJoin('itm_network_inventory_products AS p', 'itm_network_inventory_basic.id', 'p.basic_id')
        ->whereNull('assets.deleted_at')
        ->whereNotIn('assets.status_id', [3,7])
        ->whereNull('itm_network_inventory_basic.is_dupe')
        ->whereNull('itm_network_inventory_basic.deleted_at');
        if(!empty($locationIdArray)){
            $result->whereIn('assets.rtd_location_id', explode(",", $locationIdArray[0]));
        }
        if(!empty($modelIdArray)){
            $result->whereIn('assets.model_id', explode(',', $modelIdArray[0]));
        }
        if(!empty($assignedUserLocationIdArray)){
            $result->where('assets.status_id', 6)->where('assets.assigned_for', 1)->whereIn('users.location_id', explode(',', $assignedUserLocationIdArray[0]));
        }
        if(!empty($userLocationAccess)){
            $result->whereIn('assets.rtd_location_id', explode(",", $userLocationAccess[0]));
        }
        if(!empty($userDepartmentAccess)){
            $result->whereIn('assets.department_id', explode(",", $userDepartmentAccess[0]));
        }
        $result = $result->first();
        return $result;
    }

    public static function deleteSecondaryTickets($primaryTicketId) {
        try {
            $secondaryTickets = Ticket::where('merge_primary', $primaryTicketId)->get();
            foreach ($secondaryTickets as $secondaryTicket) {
                $secondaryTicket->deleted_by = Auth::user()->id;
                $secondaryTicket->save();
                $secondaryTicket->delete();
            }
        } catch (\Exception $e) {
            Log::error("Error deleting secondary tickets: " . $e->getMessage());
        }
    }

    public static function getUserPendingAction($uid) {
        $logUserDevice = DB::table("assets as a")
            ->leftJoin('asset_logs as al', 'al.id', 'a.chkout_log_id')
            ->where('a.assigned_for', 1)
            ->where('a.assigned_to', $uid)
            ->where('a.accepted', "pending")
            ->count();

        if($logUserDevice > 0) {
            return "myAction";
        } else {
            return "overall";
        }
    }
    public static function getProcurementRequestColorCode($t) {
        try {
            $color = "";
            if( $t->priority_id == 1 ) {
                $color = "red";
            }
            elseif( $t->priority_id == 2 ) {
                $color = "gray";
            }
            elseif( $t->priority_id == 3 ) {
                $color = "yellow";
            }
            elseif( $t->priority_id == 4 ) {
                $color = "green";
            }
            return $color;
        } catch(\Exception $e) {
            Log::error("getProcurementRequestColorCode: ".$e->getMessage());
            return "";
        }
    }

    public static function setDataForNotification($obj, $type, $notificationType) {
        try {
            if ($type === 'ticket') {
                $obj->status_name = $obj->status->name;
                $obj->department_name = $obj->department->name;
                $obj->problem_category_name = $obj->ProblemCategory->name;
                $obj->sub_category_name = isset($obj->subCategory) ? $obj->subCategory->name : null;
                $obj->priority_name = $obj->priority->name;
                $obj->company_name = $obj->department->company->name;
                $obj->assigned_username = $obj->assignedTo->username;
                $obj->creator_name = $obj->Creator->username;
                $obj->notification_type = $obj->Creator->is_vip_user == 1 ? 'buzzer' : $notificationType;
                $obj->module_type = $type;
            } else if($type === 'request') {
                $obj->status_name = $obj->status->name;
                $obj->department_name = $obj->department->name;
                $obj->problem_category_name = $obj->ProblemCategory->name;
                $obj->sub_category_name = isset($obj->subCategory) ? $obj->subCategory->name : null;
                $obj->priority_name = $obj->priority->name;
                $obj->company_name = $obj->department->company->name;
                $obj->assigned_username = isset($obj->assignedTo->username) ? $obj->assignedTo->username : null;
                $obj->creator_name = $obj->Creator->username;
                $obj->notification_type = $obj->Creator->is_vip_user == 1 ? 'buzzer' : $notificationType;
                $obj->module_type = $type;
            } else if($type === 'task'){
                $obj->status_name = $obj->status->name;
                $obj->priority_name = $obj->priority->name;
                $obj->assigned_username = isset($obj->assignedTo->username) ? $obj->assignedTo->username : null;
                $obj->notification_type = $notificationType;
                $obj->module_type = $type;
            } else {
               return null;
            }
            return $obj;
        } catch(\Exception $e) {
            Log::error("setDataForNotification : ".$e->getMessage());
            return null;
        }
    }

    // smartDate 
    public static function smartDate($timestamp)
    {
        if (empty($timestamp)) return __('Never');

        if ($timestamp instanceof Carbon) {
            $timestamp = $timestamp->timestamp;
        } elseif (is_string($timestamp) && strpos($timestamp, ' ') !== false) {
            $timestamp = strtotime($timestamp);
        }

        $diff = time() - $timestamp;

        if ($diff <= 0) {
            return __('Now');
        }
        else if ($diff < 60) {
            return trans_choice(':count second ago|:count seconds ago', floor($diff), ['count' => floor($diff)]);
        }
        else if ($diff < 60*60) {
            return trans_choice(':count minute ago|:count minutes ago', floor($diff/60), ['count' => floor($diff/60)]);
        }
        else if ($diff < 60*60*24) {
            return trans_choice(':count hour ago|:count hours ago', floor($diff/(60*60)), ['count' => floor($diff/(60*60))]);
        }
        else if ($diff < 60*60*24*30) {
            return trans_choice(':count day ago|:count days ago', floor($diff/(60*60*24)), ['count' => floor($diff/(60*60*24))]);
        }
        else if ($diff < 60*60*24*30*12) {
            return trans_choice(':count month ago|:count months ago', floor($diff/(60*60*24*30)), ['count' => floor($diff/(60*60*24*30))]);
        }
        else {
            return trans_choice(':count year ago|:count years ago', floor($diff/(60*60*24*30*12)), ['count' => floor($diff/(60*60*24*30*12))]);
        }
    }

    // custom compare
    public static function compare($what, $with, $how) {
        $result = false;

        switch($how) {
            case "==":
                if($what == $with) $result = true; else $result = false;
            break;

            case ">=":
                if($what >= $with) $result = true; else $result = false;
            break;

            case "<=":
                if($what <= $with) $result = true; else $result = false;
            break;

            case ">":
                if($what > $with) $result = true; else $result = false;
            break;

            case "<":
                if($what < $with) $result = true; else $result = false;
            break;

            case "!=":
                if($what != $with) $result = true; else $result = false;
            break;
        }

        return $result;
    }

    public static function uptime($websiteId, $period) {
        $end = now();
        if ($period == "24h") {
            $start = now()->subHours(24);
            $totalSecs = 86400;
        } elseif ($period == "7days") {
            $start = now()->subDays(7);
            $totalSecs = 604800;
        } elseif ($period == "30days") {
            $start = now()->subDays(30);
            $totalSecs = 2592000;
        } elseif ($period == "12months") {
            $start = now()->subYear();
            $totalSecs = 31536000;
        } elseif ($period == "selected") {
            $end = session('range_end');
            $start = session('range_start');
            $totalSecs = strtotime($end) - strtotime($start);
        } else {
            return [
                'up' => 0,
                'down' => 0,
                'unknown' => 0,
            ];
        }

        $firstHistory = LiveMonitorWebsiteHistory::where('website_id', $websiteId)->first();
        if (!$firstHistory) {
            return [
                'up' => 0,
                'down' => 0,
                'unknown' => 0,
            ];
        }

        // $firstCreatedAt = $firstHistory->created_at;

        // $diffDays = now()->diffInDays($firstCreatedAt);
        // if ($period == "7days" && $diffDays < 1) {
        //     return [
        //         'up' => 0,
        //         'down' => 0,
        //         'unknown' => 0,
        //     ];
        // }
        // if ($period == "30days" && $diffDays < 7) {
        //     return [
        //         'up' => 0,
        //         'down' => 0,
        //         'unknown' => 0,
        //     ];
        // }
        // if ($period == "12months" && $diffDays < 30) {
        //     return [
        //         'up' => 0,
        //         'down' => 0,
        //         'unknown' => 0,
        //     ];
        // }


        $histories = DB::table('live_monitor_website_histories')
        ->where('website_id', $websiteId)
        ->whereBetween('created_at', [$start, $end])
        ->orderBy('created_at')
        ->get();
        

        if ($histories->isEmpty()) {
            return [
                'up' => 0,
                'down' => 0,
                'unknown' => 100,
            ];
        }
        $totalSecsUp = 0;
        $totalSecsDown = 0;
        $totalSecsUnknown= 0;
        $duration = 300;
        $device_settings = DeviceSetting::getDeviceSettings();
        if($device_settings->live_monitor_auto_run_in_minute != null){
            $duration = $device_settings->live_monitor_auto_run_in_minute* 60;
        }
        foreach ($histories as $history) {
            if ($history->statuscode == "200") {
                $totalSecsUp += $duration;
            } elseif ($history->statuscode != "200") {
                $totalSecsDown += $duration;
            }
        }

        $totalSecsUnknown = $totalSecs - ($totalSecsUp+$totalSecsDown); // Remaining time as unknown
        $upPercentage = ($totalSecsUp / $totalSecs) * 100;
        $downPercentage = ($totalSecsDown / $totalSecs) * 100;
        $unknownPercentage = ($totalSecsUnknown / $totalSecs) * 100;

        return [
            'up' => round($upPercentage, 2),
            'down' => round($downPercentage, 2),
            'unknown' => round($unknownPercentage, 2),
        ];
    }
     

    public static function sendDeviceCountToSocket()
    {
        $client = new Client();
        $client->post(rtrim(config('app.url'), '/').':8086/updateDeviceCount', [
            'json' => []
        ]);
    }
    
    public static function sendTicketCountToSocket()
    {
        $client = new Client();
        $client->post(rtrim(config('app.url'), '/').':8086/updateTicketCount', [
            'json' => []
        ]);
    }

    public static function getGlobalAlertEmail() {
        $settings = Settings::getSettings();
        $allEmails = [];

        if ($settings->alerts_type != null && $settings->alerts_type == 1) {
            if ($settings->alerts_value != null) {
                $roleIds = explode(',', $settings->alerts_value);
                if (!is_array($roleIds)) {
                    $roleIds = [];
                }
                $allEmails = User::whereHas('roles', function ($query) use ($roleIds) {
                    $query->whereIn('id', $roleIds);
                })->where('activated', 1)->whereNotNull('email')->pluck('email')->toArray();
                // Log::info("Emails send based on roles: ", $allEmails);
            } else {
                // Log::info("No alerts_value found.");
                return $allEmails;
            }
        } else if ($settings->alerts_type != null && $settings->alerts_type == 2) {
            if ($settings->alerts_users_email != null) {
                $allEmails = explode(',', $settings->alerts_users_email);
                // Log::info("Emails send based on alerts users email: ", $allEmails);
            } else {
                // Log::info("No alerts_users_email found.");
                return $allEmails;
            }
        } else if ($settings->alerts_type != null && $settings->alerts_type == 3) {
            $allEmails = User::permission('DeviceRead')->where('activated', 1)->whereNotNull('email')->pluck('email')->toArray();
            // Log::info("Emails send based on Device Read permission: ", $allEmails);
        }
        if(isset($settings->alert_email) && $settings->alert_email != null){
            array_push($allEmails, $settings->alert_email);
        }
        $allEmails = array_unique($allEmails);
        // Log::info("list of emails to return: ", $allEmails);
        return $allEmails;
    }
    public static function getPCNumberOfDays($pro_request) {
        $no_of_day = '';
        if (isset($pro_request->sub_category_id)) {
            $no_of_days = ProblemCategory::select('number_of_days','privilege_access')->find($pro_request->sub_category_id);
        } elseif (isset($pro_request->problem_category_id)) {
            $no_of_days = ProblemCategory::select('number_of_days','privilege_access')->find($pro_request->problem_category_id);
        }
        if (!empty($no_of_days) && !empty($no_of_days->privilege_access)) {
            $no_of_day =  $no_of_days->number_of_days != NULL ? $no_of_days->number_of_days : 30;
        }
        return $no_of_day;
    }
    public static function checkDuplicateTickets($data) {
        try {
            $return = ["status" => "fail", "msg" => "Duplicate ticket not available!",'action' => true];
            $similarTickets = Ticket::whereRaw("MATCH(content) AGAINST(? IN NATURAL LANGUAGE MODE)", [$data['description']])
                ->where('department_id', $data['department_id'])
                ->where('problem_category_id', $data['problem_category_id'])
                ->whereNotIn('status_id', [5, 6])
                ->whereNull('deleted_at') // Corrected null checking
                ->whereNull('is_temp') // Ensure it's a proper nullable check
                ->orderBy('id', 'asc')
                ->limit(10) // Optional: Limits the results to improve performance
                ->get();

            if ($similarTickets->count() > 0) {
                $checkPer = TKTAutoUpdateSetting::first();
                if($checkPer['enable_duplicate_issues_check'] == 1){
                    if($checkPer['enable_duplicate_issues_check'] == 1) { 
                        $return = [
                            "data" => $similarTickets,
                            "ticket" => $similarTickets[0],
                            "action" => 'enable_duplicate_issues_check',
                            'status' => 'success',
                        ];
                    }
                    if($checkPer['auto_merge_duplicate_issues'] == 1){
                        $return = [
                            "data" => $similarTickets,
                            "ticket" => $similarTickets[0],
                            "action" => 'auto_merge',
                            'status' => 'success',
                        ];
                    }
                    if($checkPer['allow_user_to_continue_ticket_creation_on_duplicate'] == 1) {
                        $return = [
                            "data" => $similarTickets, 
                            "ticket" => $similarTickets[0],
                            "action" =>'allow_user_for_duplicate',
                            'status' => 'success',
                        ];
                    }
                    if($checkPer['auto_merge_duplicate_issues'] == 1 && $checkPer['allow_user_to_continue_ticket_creation_on_duplicate'] == 1) {
                        $return = [
                            "data" => $similarTickets, 
                            "ticket" => $similarTickets[0],
                            "action" =>'both',
                            'status' => 'success',
                        ];
                    }
                }
            }
            return $return;
        } catch (\Exception $e) {
            Log::error("checkDuplicateTickets: ".$e->getMessage());
            return false;
        }
    }

    public static function getAcessControllTicket($user, $action_ctrl_type) {
        $get_action_controls = $user->ticketActionControl()->first();
        if (!$get_action_controls) {
            return true;
        }
        $controlMapping = [
            'spam' => 'ctrl_mark_spam',
            'self_assign' => 'ctrl_self_assign',
            'delete' => 'ctrl_delete',
            'transfer' => 'ctrl_transfer',
            'assignto' => 'ctrl_assign',
            'change_creator' => 'ctrl_change_creator',
        ];
    
        return empty($controlMapping[$action_ctrl_type]) || !$get_action_controls->{$controlMapping[$action_ctrl_type]};
    }

    public static function convertFormatToRegex($format) {
        $patterns = [
            ""  => "string",  // Default
            "[a-zA-Z]*"   => "regex:/^[a-zA-Z]*$/",
            "[0-9]*" => "regex:/^[0-9]*$/",
            "[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}" => "regex:/^([a-fA-F0-9]{2}:){5}[a-fA-F0-9]{2}$/",
            "([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])" => "regex:/^([01]?\d\d?|2[0-4]\d|25[0-5])\.([01]?\d\d?|2[0-4]\d|25[0-5])\.([01]?\d\d?|2[0-4]\d|25[0-5])\.([01]?\d\d?|2[0-4]\d|25[0-5])$/",
            "YYYY-MM-DD" => "date_format:Y-m-d",
            // "HH:MM" => "regex:/^([01]\d|2[0-3]):[0-5]\d$/",
            "HH:MM" => "date_format:H:i",
            "YYYY-MM-DD HH:MM" => "date_format:Y-m-d\TH:i",
        ];
        return $patterns[$format] ?? "string";
    }

    public static function checkPabMember($pabID, $creatorID) {
        $pabs = TicketPab::whereIn('id', $pabID)->get();
        $creator = User::find($creatorID);
        
        if (!$creator) {
            return ['msg' => trans('ticket.service_ticket_fields.user_not_found'), 'status' => 'fail'];
        }
        
        foreach ($pabs as $pab) {
            switch ($pab->hierarchy_approval) {
                case 4:
                    if (is_null($creator->manager_id)) {
                        return ['msg' => trans('ticket.service_ticket_fields.approver_is_not_exist') . $pab->name, 'status' => 'fail'];
                    }
                    $manager = User::find($creator->manager_id);
                    if ($manager && $manager->last_working_date && Carbon::parse($manager->last_working_date)->lte(Carbon::today())) {
                        return ['msg' => trans('ticket.service_ticket_fields.approver_no_longer_employed'), 'status' => 'fail'];
                    }
                    break;
                
                case 9:
                    $duCheck = $creator->duHead();
                    if (empty($duCheck) || $duCheck === 'not set') {
                        return ['msg' => trans('ticket.service_ticket_fields.du_head_not_set'), 'status' => 'fail'];
                    }
                    foreach ($duCheck as $duHead) {
                        $duHeadUser = User::find($duHead->user_id);
                        if ($duHeadUser && $duHeadUser->last_working_date && Carbon::parse($duHeadUser->last_working_date)->lte(Carbon::today())) {
                            return ['msg' => trans('ticket.service_ticket_fields.approver_no_longer_employed'), 'status' => 'fail'];
                        }
                    }
                    break;
                    
                case 10:
                    $buCheck = $creator->buHead();
                    if (empty($buCheck) || $buCheck === 'not set') {
                        return ['msg' => trans('ticket.service_ticket_fields.bu_head_not_set'), 'status' => 'fail'];
                    }
                    foreach ($buCheck as $buHead) {
                        $buHeadUser = User::find($buHead->user_id);
                        if ($buHeadUser && $buHeadUser->last_working_date && Carbon::parse($buHeadUser->last_working_date)->lte(Carbon::today())) {
                            return ['msg' => trans('ticket.service_ticket_fields.approver_no_longer_employed'), 'status' => 'fail'];
                    }
                    }
                    break;
                    
                case 2:
                case 1:
                case 3:
                case 5:
                    $pabMembersCount = TicketPabMember::where('pab_id', $pab->id)->count();
                    if ($pabMembersCount == 0) {
                        return ['msg' => trans('ticket.service_ticket_fields.approver_is_not_exist') . $pab->name, 'status' => 'fail'];
                    }
                    if ($pab->hierarchy_approval == 2 && $pabMembersCount < $pab->required_minimum_approvals) {
                        return ['msg' => trans('ticket.service_ticket_fields.member_not_sufficient') . $pab->name, 'status' => 'fail'];
                    }
                    if($pab->hierarchy_approval == 5){
                        $pabMembersLocCount = TicketPabMember::where('pab_id', $pab->id)->where('location_id',$creator->location_id)->count();
                        if($pabMembersLocCount == 0){
                            return ['msg' => trans('ticket.service_ticket_fields.approver_is_not_exist') . $pab->name, 'status' => 'fail'];
                        }
                    }
                 
                    $pabMembers = $pab->hierarchy_approval == 5 
                        ? TicketPabMember::where('pab_id', $pab->id)->where('location_id', $creator->location_id)->get()
                        : TicketPabMember::where('pab_id', $pab->id)->get();
                    
                    foreach ($pabMembers as $member) {
                        $memberUser = User::find($member->user_id);
                        if ($memberUser && $memberUser->last_working_date && Carbon::parse($memberUser->last_working_date)->lte(Carbon::today())) {
                            return ['msg' => trans('ticket.service_ticket_fields.approver_no_longer_employed'), 'status' => 'fail'];
                        }
                    }
                    break;

                case 11:
                    if (empty($creator->department)) {
                        return ['msg' => "Department is not set for user. Please set the department to continue.", 'status' => 'fail'];
                    }
                    if (empty($creator->department->department_head_id)) {
                        return ['msg' => "Department head is not set as department head approval mode is selected.", 'status' => 'fail'];
                    }
                    $deptHead = User::find($creator->department->department_head_id);
                    if ($deptHead && $deptHead->last_working_date && Carbon::parse($deptHead->last_working_date)->lte(Carbon::today())) {
                        return ['msg' => trans('ticket.service_ticket_fields.approver_no_longer_employed'), 'status' => 'fail'];
                    }
                    break;
                case 12:
                    if($pab->required_minimum_approvals > 0){
                        $nextUsers = $pab->hasNestedManager($creatorID, $pab->required_minimum_approvals);
                        if (count($nextUsers) < 1) {
                            return ['msg' => trans('ticket.service_ticket_fields.required_manager_level_not_match'),'status'=>'fail' ];
                        }
                        if(!empty($nextUsers)){
                            $nextmanagers = User::whereIn('id',$nextUsers)->get();
                            foreach($nextmanagers as $manager){
                                if ($manager && $manager->last_working_date && Carbon::parse($manager->last_working_date)->lte(Carbon::today())) {
                                    return ['msg' => $pab->name.": The assigned approver, ".$manager->fullName().", ".trans('ticket.service_ticket_fields.approver_no_longer_employed'), 'status' => 'fail'];
                                }
                                if ($manager && $manager->activated == 0 ) {
                                    return ['msg' => $pab->name.": The assigned approver, ".$manager->fullName().", is inactive.", 'status' => 'fail'];
                                }
                            }
                        }
                    }
                    break;
                case 13:
                case 14:
                    $buCheck = $creator->buHead();
                    $checkbuSet = true;
                    $checkduSet = true;
                    if (empty($buCheck) || $buCheck === 'not set') {
                        $checkbuSet = false;
                    }
                    $duCheck = $creator->duHead();
                    if (empty($duCheck) || $duCheck === 'not set') {
                        $checkduSet = false;
                    }
                    if($checkbuSet == false && $checkduSet == false){
                        return ['msg' => trans('ticket.service_ticket_fields.bu_du_head_not_set'), 'status' => 'fail'];
                    }
                    if($checkbuSet){
                        foreach ($buCheck as $buHead) {
                            $buHeadUser = User::find($buHead->user_id);
                            if ($buHeadUser && $buHeadUser->last_working_date && Carbon::parse($buHeadUser->last_working_date)->lte(Carbon::today())) {
                                return ['msg' => trans('ticket.service_ticket_fields.approver_no_longer_employed'), 'status' => 'fail'];
                            }
                        }
                    }
                    if($checkduSet) {
                        foreach ($duCheck as $duHead) {
                            $duHeadUser = User::find($duHead->user_id);
                            if ($duHeadUser && $duHeadUser->last_working_date && Carbon::parse($duHeadUser->last_working_date)->lte(Carbon::today())) {
                                return ['msg' => trans('ticket.service_ticket_fields.approver_no_longer_employed'), 'status' => 'fail'];
                            }
                        }
                    }
            }
        }
        
        return ['msg' => trans('ticket.service_ticket_fields.approval_checks_passed'), 'status' => 'success'];
    }
    public static function CardUICreate($column, $type) {
        $return['html'] = (string) view('patch_management.sub_menu_dropdown_left')->with('data', $column)->with('title', $type);
        return $return;
    }

    public static function CardUIDeviceCreate($devices) {
        $return['html'] = (string) view('patch_management.sub_menu_device_dropdown_left')->with('devices', $devices);
        return $return;
    }

    public static function makeCardUICreate($column, $type) {
        $return['html'] = (string) view('patch_management.device.sub_menu_dropdown_left')->with('data', $column)->with('title', $type);
        return $return;
    }

    public static function makeCardUIDeviceCreate($devices) {
        $return['html'] = (string) view('patch_management.device.sub_menu_device_dropdown_left')->with('devices', $devices);
        return $return;
    }

    public static function updateStatusCounts($oldStatus, $newStatus, $devices = null, $deleted = null , $modelChange = null)
    {
        $catgoryWiseAssestSummary = [];
        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();
        if($modelChange == null) {
            $catName = $devices->model->category->name ?? null;
            if(strtolower($catName) == 'laptop') {
                $catgoryWiseAssestSummary = [0,1];
            } else if(strtolower($catName) == 'desktop') {
                $catgoryWiseAssestSummary = [0,2];
            } else {
                $catgoryWiseAssestSummary = [0,3];
            }
        } else {
            $model = Model::where('id', $modelChange)->first();
            $catNameOld = $model->category->name ?? null;
            $catName = $devices->model->category->name ?? null;
            if(strtolower($catName) == 'laptop' && strtolower($catNameOld) == 'desktop') {
                $catgoryWiseAssestSummary = [2,1];
            } else if(strtolower($catName) == 'laptop' && strtolower($catNameOld) != 'laptop' && strtolower($catNameOld) != 'desktop') {
                $catgoryWiseAssestSummary = [3,1];
            } else if(strtolower($catName) == 'desktop' && strtolower($catNameOld) != 'laptop' && strtolower($catNameOld) != 'desktop') {
                $catgoryWiseAssestSummary = [3,2];
            } else if(strtolower($catName) == 'desktop' && strtolower($catNameOld) == 'laptop') {
                $catgoryWiseAssestSummary = [1,2];
            } else if(strtolower($catName) != 'laptop' && strtolower($catName) != 'desktop'  && strtolower($catNameOld) == 'laptop') {
                $catgoryWiseAssestSummary = [1,3];
            } else if(strtolower($catName) != 'laptop' && strtolower($catName) != 'desktop' && strtolower($catNameOld) == 'desktop') {
                $catgoryWiseAssestSummary = [2,3];
            }  
        }

        if (!empty($catgoryWiseAssestSummary)) {
            foreach ($catgoryWiseAssestSummary as $key => $value) {
                if ($value != 0){
                    $columns = DB::getSchemaBuilder()->getColumnListing('category_wise_asset_summaries');
                    $columns = array_diff($columns, ['id', 'category_wise_id', 'date', 'created_at', 'updated_at', 'total_devices']);
                    $todayMultipleData = CategoryWiseAssetSummary::where('date',$today)->get();
                    if ($todayMultipleData->isEmpty() == true) {
                        $yesterdayMultipleData = CategoryWiseAssetSummary::where('date', $yesterday)->get();
                        foreach ($yesterdayMultipleData as $val) {
                            $newData = $val->toArray();
                            unset($newData['id'], $newData['date'], $newData['created_at'], $newData['updated_at']);
                            $newData['date'] = $today;
                            CategoryWiseAssetSummary::firstOrCreate(
                                ['date' => $today, 'category_wise_id' => $val->category_wise_id],
                                $newData
                            );
                        }
                    }
                    $assetSummary = CategoryWiseAssetSummary::firstOrCreate(['date' => $today,'category_wise_id' => $value]);
                } else {            
                    $columns = DB::getSchemaBuilder()->getColumnListing('asset_summary');
                    $columns = array_diff($columns, ['id', 'date', 'created_at', 'updated_at', 'total_devices']);
                    $todayData = AssetSummary::where('date',$today)->first();
                    if ($todayData == null) {
                        $yesterdayData = AssetSummary::where('date', $yesterday)->first();
                        if ($yesterdayData) {
                            $newData = $yesterdayData->toArray();
                            unset($newData['id'], $newData['date'], $newData['created_at'], $newData['updated_at']);
                            AssetSummary::firstOrCreate(['date' => $today],$newData);
                        }
                    }
                    $assetSummary = AssetSummary::firstOrCreate(['date' => $today]);
                }
                $formattedColumns = array_map(function ($col) {
                    $formatted = ucwords(str_replace('_', ' ', $col));
                    $hyphenatedFormatted = str_replace(' ', '-', $formatted);
                    $exists = DB::table('status_labels')->where('name', $hyphenatedFormatted)->exists();
                    return $exists ? $hyphenatedFormatted : $formatted;
                }, $columns);
                
                $statusIds = DB::table('status_labels')
                ->whereIn('name', $formattedColumns)
                ->pluck('id')->toArray();

                if ($modelChange != null && isset($catNameOld) && isset($catName) && $catNameOld != null && $catName != null && $catNameOld != $catName) {
                    if ($key == 0){
                        $statusName = Label::where('id', $oldStatus)->value('name');
                        $s = strtolower(str_replace(' ', '_', $statusName));
                        if (!isset($assetSummary->$s) || $assetSummary->$s === null) {
                            $assetSummary->$s = 0;
                        }
                        if ($assetSummary->$s > 0) {
                            $assetSummary->$s = $assetSummary->$s - 1;
                        }
                        $assetSummary->total_devices = ($assetSummary->total_devices ?? 0) - 1;
                    } else if($key == 1){
                        $statusName = Label::where('id', $newStatus)->value('name');
                        $s = strtolower(str_replace(' ', '_', $statusName));
                        if (!isset($assetSummary->$s) || $assetSummary->$s === null) {
                            $assetSummary->$s = 0;
                        }
                        $assetSummary->$s = $assetSummary->$s + 1;
                        $assetSummary->total_devices = ($assetSummary->total_devices ?? 0) + 1;
                    }
                } else {
                    // Decrease count from old status
                    if ($oldStatus && in_array($oldStatus, $statusIds)) {
                        $statusName = Label::where('id', $oldStatus)->value('name');
                        $s = strtolower(str_replace(' ', '_', $statusName));
                    
                        if (!isset($assetSummary->$s) || $assetSummary->$s === null) {
                            $assetSummary->$s = 0;
                        }
                        if ($assetSummary->$s > 0) {
                            $assetSummary->$s = $assetSummary->$s - 1;
                        }
                    }
                    // Increase count for new status
                    if ($newStatus && in_array($newStatus, $statusIds)) {
                        $statusName = Label::where('id', $newStatus)->value('name');
                        $s = strtolower(str_replace(' ', '_', $statusName));
                        if (!isset($assetSummary->$s) || $assetSummary->$s === null) {
                            $assetSummary->$s = 0;
                        }
                        $assetSummary->$s = $assetSummary->$s + 1;
                    }
                    // If it's a new device being addeds
                    if (!isset($oldStatus)) {
                        $assetSummary->total_devices = ($assetSummary->total_devices ?? 0) + 1;
                    } else if($deleted == 'deleted'){
                        $assetSummary->total_devices = ($assetSummary->total_devices ?? 0) - 1;
                    } 
                }
                $assetSummary->save();
            }
        }
    }
    public static function whatsappOptInNotification(){
        $user = Auth::user();
        return config('app.whatsapp_notification') && NotificationConfig::first()->whatsapp_notification_enabled && $user->phone != null && UserDetails::where('user_id', $user->id)->value('opt_in_for_wa_notification') == null;
    }

    public static function getBotId() {
        $array = [
            'botName' => "GIB",
            'botId' => "itm-rp",
        ];
        switch(config('app.client')) {
            case "ltts":
                if(config('app.sub_client') == 'dev') {
                    $array['botName'] = "Support BOT(UAT)";
                    $array['botId'] = 'lnt-bot-uat';
                } else {
                    $array['botName'] = "Support BOT";
                    $array['botId'] = 'itm-bot';
                }
                break;
            case "rolepermission":
                $array['botName'] = "MATI AI";
                $array['botId'] = 'itm-rp';
                break;
            case "greenitco":
                $array['botName'] = "GIB";
                $array['botId'] = 'greenitco';
                break;
            case "grdemo":
                $array['botName'] = "GIB";
                $array['botId'] = 'itm-rp';
                break;
            case "ril":
                $array['botName'] = "MATI AI";
                $array['botId'] = 'ril';
                break;
        }
        return $array;
    }
    public static function getAddressFromCoordinates($latitude, $longitude) {
        if(empty($latitude) || empty($longitude)) {
            // Log::error("getAddressFromCoordinates: Latitude or Longitude is empty");
            return false;
        }
        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat=$latitude&lon=$longitude";
        $opts = ['http' => ['header' => "User-Agent: LaravelApp/1.0\r\n"]];
        $context = stream_context_create($opts);
        try {
            $response = file_get_contents($url, false, $context);
            $json = json_decode($response);

            return $json->display_name ?? 'Location not found';
        } catch (\Exception $e) {
            // Log::info('getAddressFromCoordinates',[$e]);
            return 'Location fetch failed';
        }
    }

    public static function checkEscalationMember($ticket_request, $loc_info)
    {
        $return = ['status' => 'pass' , 'msg' => 'Escalation check pass'];
        $escalation = self::getEscalationGroup($ticket_request);

        if (!$escalation) {
            return $return; // Or handle as needed
        }
        foreach($escalation as $escalationItem){
            $result = self::validateEscalationGroupMembers($escalationItem, $loc_info);
            if($result['status'] == 'fail'){
                return $result; // Return the failure message
            }
        }
        // $groupUsers = EscalationGroupUser::where('group_id', $escalation->id)->get();

        // if ($escalation->location_based == 1) {
        //     $users = self::filterUsersByLocation($groupUsers, $loc_info['location_id']);
        //     if (empty($users)) {
        //         return ['status' => 'fail', 'msg' => 'Location head does not exist for ticket escalation.'];
        //     }
        // } elseif ($groupUsers->isEmpty()) {
        //     return ['status' => 'fail', 'msg' => 'No member exists for ticket escalation.'];
        // }

        return $return; // No issues
    }

    public static function getEscalationGroup($ticket_request)
    {
        if (!empty($ticket_request['sub_category_id']) && !empty($ticket_request['problem_category_id'])) {
            // $escalation = EscalationGroup::where('department_id', $ticket_request['department_id'])->where('sub_category_id', $ticket_request['sub_category_id'])->where('problem_category_id', $ticket_request['problem_category_id'])->first();
            //$escalations = Esclation::where('escalation_group_id', $escalation->id)->first();
            $escalations = Esclation::whereIn('problem_category_id', [$ticket_request['sub_category_id'], $ticket_request['problem_category_id']])->whereNull('esclate_to')->get();
            if (!empty($escalations)){
                return $escalations;
            }else{
                return null;
            }
        }

        if (!empty($ticket_request['problem_category_id'])) {
            // $escalationGr =  EscalationGroup::where('department_id', $ticket_request['department_id'])->where('problem_category_id', $ticket_request['problem_category_id'])->first();
                $escalations = Esclation::whereIn('problem_category_id', [$ticket_request['sub_category_id'], $ticket_request['problem_category_id']])->whereNull('esclate_to')->get();           if (!empty($escalations)){
                // $escalations = Esclation::where('escalation_group_id', $escalationGr->id)->first();
                return $escalations;
            }else{
                return null;
            }
        }

        return null;
    }
    public static function validateEscalationGroupMembers($escalation, $loc_info)
    {
        $groupUsers = EscalationGroupUser::where('group_id', $escalation->escalation_group_id)->get();

        if ($escalation->location_based == 1) {
            $users = self::filterUsersByLocation($groupUsers, $loc_info['location_id']);
            if (empty($users)) {
                return ['status' => 'fail', 'msg' => 'Location head does not exist for ticket escalation.'];
            }
        } elseif ($groupUsers->isEmpty()) {
            return ['status' => 'fail', 'msg' => 'No member exists for ticket escalation.'];
        }

        return ['status' => 'pass', 'msg' => 'Escalation check pass'];
    }

    public static function filterUsersByLocation($groupUsers, $locationId)
    {
        $users = [];
        foreach ($groupUsers as $user) {
            if (!empty($user->location_id)) {
                $permitted_loc = explode(",", $user->location_id);
                if (in_array($locationId, $permitted_loc)) {
                    $users[] = $user->user_id;
                }
            }
        }
        return $users;
    }
    public static function RandomTrackingID(){
        return Str::upper(self::settings()->auto_increment_prefix).random_int(11111111,99999999);
    }

    public static function getMailroomUserPermissions() {
        $user = auth()->user();
        if (!$user) {
            return null;
        }

        $assignment = MailroomUserAssignment::where('user_id', $user->id)->first();
        if (!$assignment) {
            return null;
        }

        $mailroomUser = MailroomUser::with(['role.permissions', 'sites.site'])->find($assignment->mailroom_user_id);
        if (!$mailroomUser) {
            return null;
        }

        $roleName = $mailroomUser->role ? $mailroomUser->role->name : null;
        $permissions = $mailroomUser->role && $mailroomUser->role->permissions
            ? $mailroomUser->role->permissions->pluck('name')->toArray()
            : [];

        $sites = $mailroomUser->sites->filter(fn($site)     => $site->site !== null);
        $siteIds = $sites->pluck('site.id')->toArray();
        $siteHeads = $sites->pluck('site.site_head_id')->toArray();

        // Site IDs where this user is a Site Head (from `sites` table directly)
        $authSiteHeadIds = Site::where('site_head_id', $user->id)->pluck('id')->toArray();

        return [
            'mailroom_role'     => $roleName,
            'permissions'       => $permissions,
            'site_ids'          => $siteIds,
            'site_heads'        => $siteHeads,
            'auth_site_head_ids'=> $authSiteHeadIds,
        ];
    }

    public static function hasMailroomPermission(string $permission): bool
    {
        $permissions = self::getMailroomUserPermissions()['permissions'] ?? [];
        return in_array($permission, $permissions);
    }

    public static function checkMailroomPermissionOrRedirect(string $permission, string $redirectTo = 'dashboard')
    {
        if (!self::hasMailroomPermission($permission)) {
            if($redirectTo == 'api'){
                 return response()->json([
                    'status' => 'danger',
                    'msg' => "Permission denied",
                ]);
            }else{
                return redirect($redirectTo)->with('msg', [
                    'status' => 'danger',
                    'msg' => "Permission denied",
                ]);
            }
        }

        return true;
    }

    public static function problemTatPriority($actionControl, $problem_category) {
        $result = [
            'priority' => null,
            'tat' => null
        ];

        if (isset($actionControl) && $actionControl->ctrl_priority == 0) {
            $result['priority'] = $problem_category->priority_id;
        }
        if (isset($actionControl) && $actionControl->ctrl_tat == 0) {
            if (isset($problem_category->tat)) {
                $result['tat'] = $problem_category->tat;
            } else {
                $getPriority = Priority::find($problem_category->priority_id);
                $result['tat'] = $getPriority ? intval($getPriority->service_time) : null;
            }
        }
        return $result;
    }

    public static function subPriorityAndTat($actionControl, $category) {
        $result = [
            'priority' => null,
            'tat' => null
        ];

        if (isset($actionControl) && $actionControl->ctrl_priority == 0) {
            $result['priority'] = $category->priority_id;
        }
        if (isset($actionControl) && $actionControl->ctrl_tat == 0) {
            if (isset($category->tat)) {
                $result['tat'] = $category->tat;
            } else {
                $priority = Priority::find($category->priority_id);
                $result['tat'] = $priority ? intval($priority->service_time) : null;
            }
        }
        return $result;
    }
    
    public static function getWorkingDaysCustom($start, $end, $weekends, $holidays) {
        $days = 0;

        $weekends = array_map('strtolower', $weekends);
        $holidays = array_flip($holidays);

        foreach (CarbonPeriod::create($start, $end) as $date) {
            $dayName = strtolower($date->format('D'));
            $currentDate = $date->format('Y-m-d');

            if (in_array($dayName, $weekends)) {
                continue;
            }

            if (isset($holidays[$currentDate])) {
                continue;
            }

            $days++;
        }

        return $days;
    }

    public static function searchAIResponse($query) {
        try {
            $return = ['status' => 'fail', 'msg' => 'Unable to fetch response'];
            $key = config('app.gemini_ai_key');
            if(empty($key)) {
                $return['msg'] = 'AI key not found';
                return response()->json($return);
            }
            $customContext = "Use information from 'IT Assets Management by GreenItCo' website/documentation for the most accurate answer. "
                . "If the information is not available there, you may provide general knowledge or best practices. "
                . "Please limit your response to under 2000 characters.\n\n";
            $query = $customContext . $query;
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $key, [
                "contents" => [
                    [
                        "parts" => [
                            [
                                "text" => $query
                            ]
                        ]
                    ]
                ]
            ]);
            $data = ($response->json());
            Log::info("Gemini AI Response: ".json_encode($data));
            $aiText = isset($data["candidates"][0]["content"]["parts"][0]["text"]) ? $data["candidates"][0]["content"]["parts"][0]["text"] : null;
            return $aiText;
        } catch(Exception $e) {
            Log::error("searchAIResponse() : ".$e->getMessage());
            return response()->json($return);
        }
    }

    public static function getGeminiEmbedding($text) {
        try {
            $key = config('app.gemini_ai_key');
            if (empty($key)) {
                return response()->json(['msg' => 'AI key not found']);
            }
            $url = "https://generativelanguage.googleapis.com/v1beta/models/embedding-001:embedContent?key=" . $key;
            $data = [
                "content" => [
                    "parts" => [
                        ["text" => $text]
                    ]
                ]
            ];
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json"
                ]
            ]);
            $response = curl_exec($ch);
            curl_close($ch);
            $json = json_decode($response, true);
            return isset($json['embedding']['values']) ? json_encode($json['embedding']['values']) : null;
        } catch (Exception $e) {
            Log::error("getGeminiEmbedding: " . $e->getMessage());
            return null;
        }
    }

    public function cosineSimilarity(array $a, array $b): float {
        $dot = $normA = $normB = 0.0;
        for ($i = 0; $i < count($a); $i++) {
            $dot += $a[$i] * $b[$i];
            $normA += $a[$i] ** 2;
            $normB += $b[$i] ** 2;
        }
        return $dot / (sqrt($normA) * sqrt($normB));
    }

    public static function searchSimilarQuery($inputEmbedding, $threshold = 0.85) {
        $aiexistings = AIKnowledgeBase::select("id", "query", "response", "embedding")->get();
        dd($aiexistings);
        foreach($aiexistings as $aiexisting) {
            $storedEmbedding = json_decode($aiexisting->embedding, true);
            $similarity = self::cosineSimilarity(json_decode($inputEmbedding), $storedEmbedding);
            if ($similarity >= $threshold) {
                return $aiexisting->response;
            }
        }
        return null;
    }

    public static function checkTAT($tat, $priority){
        if(isset($tat) && $tat > 0){
            return $tat;
        }
        return (int) Priority::where('id', $priority)->value('service_time');
    }


    public static function getAIStatusPrediction($text, $ticket_id, $user_id)
    {
        try {
            $url = "http://172.105.41.52:5000/predict-status";
            $text = strip_tags($text);
            $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $text = preg_replace('/\s+/', ' ', $text);
            $text = trim($text);
            $data = [
                "text" => $text
            ];
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS     => json_encode($data),
                CURLOPT_HTTPHEADER     => ["Content-Type: application/json"],
            ]);
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                Log::error("cURL Error: " . curl_error($ch));
                curl_close($ch);
                return null;
            }
            curl_close($ch);
            $json = json_decode($response, true);
            if (!isset($json["status_label"])) {
                Log::warning("response invalid", ['response' => $json]);
                return null;
            }
            $status = trim($json["status_label"]);
            Log::info("Status change : ". json_encode($status));
            $statusArrayToCheck = config("app.client") == "safari" ? ["Tested Released", "Spam", "Merging", "Under Observation", "Resolved", "Enhancement", "Uploaded on UAT", "Waiting for the Approval"] : ["Resolved"];
            if(in_array($status, $statusArrayToCheck)) {
                $ticket = DB::table('tkt_tickets')->find($ticket_id);
                if (!$ticket) {
                    Log::error("Ticket not found", ['ticket_id' => $ticket_id]);
                    return null;
                }

                if (!isset($ticket->tat) || $ticket->tat == null) {
                    $priority = Priority::find($ticket->priority_id);
                    if ($priority) {
                        $ticket->tat = $priority->service_time;
                    }
                }

                $request = new Request([
                    'id'         => $ticket_id,
                    'status_id'  => 5,
                    'priority_id'=> $ticket->priority_id,
                    'tat'        => $ticket->tat,
                    'tmp_id'     => uniqid(),
                    'comment'    => $text,
                    'user_id'    => $user_id
                ]);

                $controller = new IndexController();
                return $controller->updateStatusViaAi($request);
            }
            Log::error("Status prediction of the AI for ticket id ".$ticket_id. " is ".$status);
            return null;

        } catch (\Exception $e) {
            Log::error("getAIStatusPrediction Exception: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public static function getAIAutoReply($text, $ticket_id, $userId)
    {
        try {
            $key = config('app.gemini_ai_key');
            if (empty($key)) {
                Log::error("Gemini API key not found");
                return response()->json(['msg' => 'AI key not found'], 500);
            }

            $prompt = "You are an AI assistant. Answer the following query in a short, precise, and clear manner.
            - Use simple language.
            - Keep the response under 50 words.
            - Do not add extra explanations or unnecessary details.
            - Do not repeat the query in the answer.
            Query: ".$text;
            $autoResponse = self::searchAIResponse($prompt);
            $converter = new CommonMarkConverter();
            
            $autoResponse = $converter->convertToHtml($autoResponse);
            if(isset($return["status"]) && $return["status"] == "fail") {
                Log::error("New email to ticket ai response creation failed.");
            } else {
                $indexConObj = new IndexController();
                $commentReqObjArray = [
                    'id' => $ticket_id,
                    'comment' => $autoResponse,
                    'tmp_id' => "tmp_".rand(2,9),
                    'user_id' => $userId
                ];
                $commentReqObj = new Request($commentReqObjArray);
                $indexConObjData = $indexConObj->addComment($commentReqObj);
                Log::Info("Auto response comment response ". json_encode($indexConObjData));
            }

            Log::error("Ai Auto reply for ticket id ".$ticket_id);
            return null;

        } catch (\Exception $e) {
            Log::error("getAIAutoReply Exception: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public static function renderKanbanContent($content, &$attachments=[], $direct_view=false) {
        if(!$content || !count($attachments)){
            return $content;
        }
        $matches = [];
        preg_match_all('/[\'\"]cid:(.*?)[\'\"]/', $content, $matches);
        if(count($matches) != 2) {
            return $content;
        }
        $matches_b = $matches[1];
        foreach( $matches_b as $b ) {
            if( !isset($attachments[$b]) ) {
                continue;
            }
            $attachment = $attachments[$b];
            if (!empty($attachment->kid)) {
                $url = $direct_view
                    ? url('ticket/attachment/view/d', $attachment->id)
                    : url('ticket/attachment/view', $attachment->id);
            } else {
                $url = $direct_view
                    ? url('ticket/kanban-board/card_attachment/view/d', $attachment->id)
                    : url('ticket/kanban-board/card_attachment/view', $attachment->id);
            }
            $content = str_ireplace("cid:" . $b, $url, $content);
        }
        return $content;
    }
    public static function userIsTechnician($id) {
        try {
            $checkUser = User::find($id);
            if ($checkUser) {
                return $checkUser->hasPermission("service_tickets");
            }
            return false;
        } catch(\Exception $e) {
            Log::error("userIsTechnician : ".$e->getMessage());
            return false;
        }

    }

    public static function getApproval($pr_id, $status = 2)
    {
        return TicketApprovalRequest::where('pr_id', $pr_id)->where('approve_status', $status)->get();
    }

    public static function is_card_overdue_before_xhours(){
        $res = KanbanConfig::pluck('overdue_mail_before_hours')->first();
        return $res;
    }

    public static function getAITechnicianOverview($text)
    {
        try {
            $key = config('app.gemini_ai_key');
            if (empty($key)) {
                Log::error("Gemini API key not found");
                return response()->json(['msg' => 'AI key not found'], 500);
            }

            $prompt = "
            You are a senior operations analyst generating an internal performance insight for a technician.

            Generate a visually structured, engaging HTML overview based on the JSON data below.

            Strict rules:
            - Return ONLY raw HTML (no markdown, no code blocks)
            - Do NOT mention rank, position, or comparative standing
            - Do NOT infer rankings or say 'top', 'best', or 'number one'
            - Ignore any rank-related fields in the JSON
            - Focus on trends, quality, and behavior — not relative comparison
            - Do NOT repeat or describe the input JSON
            - Keep the response under 200 words
            - times are in hours so show as hours where applicable

            HTML structure:
            - <h3> for section titles
            - <ul><li> for insights
            - <strong> for highlights
            - Use minimal, professional emojis

            Sections:
            1. Performance Snapshot
            2. Key Strengths
            3. Risk Signals or Improvement Areas (only if applicable)
            4. Actionable Recommendation (optional)

            JSON data:
            {$text}

            Return ONLY raw HTML.";

            $aiResponse = self::searchAIResponse($prompt);
            $aiResponse = self::cleanAIHtml($aiResponse != null ? $aiResponse : '');
            return (string) $aiResponse;
        } catch (\Exception $e) {
            Log::error("getAITechnicianOverview Exception: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
    public static function cleanAIHtml(string $html) {
        $html = preg_replace('/^```(?:html)?/i', '', trim($html));
        $html = preg_replace('/```$/', '', trim($html));
        return trim($html);
    }
     public static function generateTicketId($ticket, $deptObject = null) {
        if(config('app.client') == 'ril') {
            $ticketInitial = UserSeatNumber::getTicketInitial($ticket->seat_no, $ticket->creator_id);
            return (string) $ticketInitial;
        }
        $config = TicketConfig::first();
        if (!$config || empty($config->ticket_initial)) {
            return (string) $ticket->id;
        }

        $initials = json_decode($config->ticket_initial, true)  ?: explode(',', $config->ticket_initial);
        $separator = $config->ticket_initial_separator ?: '-';

        $tags = [];
        foreach ($initials as $initial) {
            switch (trim($initial)) {

                // Company Tag
                case 'com':
                    $tags[] = optional(Auth::user()->company)->company_tag;
                    break;

                // Department Tag
                case 'dep':
                    $tags[] = optional($ticket->department)->department_tag;
                    break;

                // Category / Subcategory Tag (priority logic)
                case 'cat':
                    // Subcategory has priority
                    if (!empty(optional($ticket->subCategory)->category_tag)) {
                        $tags[] = $ticket->subCategory->category_tag;
                    // Fallback to Category
                    } else {
                        $tags[] = optional($ticket->problemCategory)->category_tag;
                    }
                    break;
            }
        }

        // Remove null / empty
        $tags = array_filter($tags);
        $ticket_tag = implode($separator, $tags) . $separator . $ticket->id;

        // Update ticket column
        Ticket::find($ticket->id)->update([
            'ticket_tag' => $ticket_tag
        ]);

        return $ticket_tag;
    }

    public static function extractMailContent($content) {
        try {
            $text = Html2Text::convert($content, [
                'ignore_errors' => true
            ]);
            $text = preg_replace("/\r\n|\r/", "\n", $text);
            $text = trim($text);
            $parser = new EmailParser();
            $email = $parser->parse($text);
            return trim($email->getVisibleText());
        } catch (\Exception $e) {
            Log::error("extractMailContent Exception: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return trim($content);
        }
    }
    public static function renderTaskContent($content, &$attachments=[], $direct_view=false) {
        if(!$content || !count($attachments)){
            return $content;
        }

        $matches = [];
        preg_match_all('/[\'\"]cid:(.*?)[\'\"]/', $content, $matches);
        if(count($matches) != 2) {
            return $content;
        }

        $matches_b = $matches[1];

        foreach( $matches_b as $b ) {
            if( !isset($attachments[$b]) ) {
                continue;
            }

            $url = $direct_view ? url('task/view_attachment/d/', $attachments[$b]->id) : url('task-management/view_attachment/', $attachments[$b]->id);
            $content = str_ireplace("cid:" . $b, $url, $content);
        }

        return $content;
    }
    public static function getAccessibleCompanyIds($userId = null)
    {
        $user = isset($userId)? User::find($userId): Auth::user();
        $permission = json_decode($user->permission, true) ?? [];

        $userCompanyPrev = [];

        if ((!empty($permission['service_tickets']) && $permission['service_tickets'] == 1)|| $user->hasRole('SuperAdmin')) {
            $userCompanyPrev = TktCompanyPreviledge::where('user_id', $user->id)->pluck('company_id')->toArray();
        }
        $userCompany = UserDetails::where('user_id', $user->id)->pluck('user_companies')->flatMap(fn ($item) => explode(',', $item))->toArray();

        return collect($userCompanyPrev)->merge($userCompany)->push($user->company_id)->filter()->map(fn ($id) => (int) $id)->unique()->values()->toArray();
    }

    public static function getPrevilegedDepartmentIdsByCompanyAccess($user = null) {
        $userId = isset($user)? $user : Auth::user()->id;
        $dashboardCompanyId = UserDetails::where('user_id', $userId)->value('dashboard_company_id');
        $companyId = self::getAccessibleCompanyIds($userId);
        if (!empty($dashboardCompanyId) && $dashboardCompanyId != 0) {
            $privilegeDepartmentIds = Privilege::where('user_id', $userId)->where('company_id',$dashboardCompanyId)->pluck('department_id');
        }else{
            $privilegeDepartmentIds = Privilege::where('user_id', $userId)->whereIn('company_id',$companyId)->pluck('department_id');
        }
        return $privilegeDepartmentIds;
    }

    public static function getUserByCompanyAccess($query, $userAlias = 'u') {
        $userId = Auth::id();
        $dashboardCompanyId = UserDetails::where('user_id', $userId)->value('dashboard_company_id');

        if (!$dashboardCompanyId) {
            return $query;
        }
        // privileged users for dashboard company
        $privilegedUserIds = TktCompanyPreviledge::where('company_id', $dashboardCompanyId)->pluck('user_id')->toArray();

        return $query->where(function ($q) use ($dashboardCompanyId, $privilegedUserIds, $userAlias) {
            $q->where("{$userAlias}.company_id", $dashboardCompanyId);
            if (!empty($privilegedUserIds)) {
                $q->orWhereIn("{$userAlias}.id", $privilegedUserIds);
            }
        });
    }

    public static function checkStatusApprovalSetForTicket($ticketId, $statusId) {
        if (!$statusId || !$ticketId) {
            Log::warning('TicketApprovalHelper missing params', [
                'ticket_id' => $ticketId,
                'status_id' => $statusId
            ]);
            return [
                'status' => false,
                'approval_required' => false,
                'msg' => 'Invalid request parameters'
            ];
        }

        $ticket = Ticket::select('id','company_id','department_id','problem_category_id','sub_category_id')->find($ticketId);
        if (!$ticket) {
            Log::warning("Ticket not found for approval check: {$ticketId}");
            return [
                'status' => false,
                'approval_required' => false,
                'msg' => 'Ticket not found'
            ];
        }

        $query = StatusApprovalConfiguration::where('company_id', $ticket->company_id)->where('department_id', $ticket->department_id)->where('category_id', $ticket->problem_category_id)->where('status_id', $statusId);
        
        if (!empty($ticket->sub_category_id)) {
            $query->where('subcategory_id', $ticket->sub_category_id);
        } else {
            $query->whereNull('subcategory_id');
        }
        
        $config = $query->first();
        $users = collect();
        if(isset($config->approval_from) && $config->approval_from == 1){
            $userIds = StatusApprovalMembers::where('status_approval_configuration_id', $config->id)->whereNotNull('user_id')->pluck('user_id');
            $users = User::whereIn('id', $userIds)->select('id','email',DB::raw('CASE WHEN users.displayName IS NOT NULL AND users.displayName != "" THEN users.displayName ELSE CONCAT(users.first_name, " ", users.last_name) END as full_name'))->get();
        }elseif(isset($config->approval_from) && $config->approval_from == 2){
            $groupIds = StatusApprovalMembers::where('status_approval_configuration_id', $config->id)->whereNotNull('group_id')->pluck('group_id');
            $userIds = UserGroupMember::whereIn('user_group_id', $groupIds)->pluck('user_id');
            $users = User::whereIn('id', $userIds)->select('id','email',DB::raw('CASE WHEN users.displayName IS NOT NULL AND users.displayName != "" THEN users.displayName ELSE CONCAT(users.first_name, " ", users.last_name) END as full_name'))->get();
        }
        $users = $users->unique('id')->values()->toArray();
        
        if (!$config) {
            return [
                'status' => true,
                'approval_required' => false,
                'msg' => 'No approval configuration found'
            ];
        }
        return [
            'status' => true,
            'approval_required' => true,
            'need_time_duration' => (bool) $config->need_time_duration,
            'fallback_status_id' => $config->fallback_status_id,
            'config_id' => $config->id,
            'users' => $users
        ];
    }

    // $type will have 3 values, datetime, date and time
    // $for will have 2 values, display for displaying values in tables, and excel for excel export.
    public static function dateTimeFormat($type = 'datetime', $for = 'display') {
        if ($for == 'excel') {
            $dateKey = 'app.excel_date_format';
            $timeKey = 'app.excel_time_format';
        } else {
            $dateKey = 'app.date_format';
            $timeKey = 'app.time_format';
        }

        $dateFormat = config($dateKey);
        $timeFormat = config($timeKey);

        if ($type === 'date') {
            return $dateFormat;
        }

        if ($type === 'time') {
            return $timeFormat;
        }

        return trim($dateFormat . ' ' . $timeFormat);
    }

    public static function displayDateTime($value, $type = 'datetime', $for = "display") {
        return Carbon::make($value)?->format(self::dateTimeFormat($type, $for)) ?? '';
    }

    public static function mysqlDateTimeFormat($type = "datetime", $for = "display") {
        $format = self::dateTimeFormat($type, $for);

        $map = [
            'd' => '%d', 'j' => '%e', 'm' => '%m', 'n' => '%c',
            'Y' => '%Y', 'y' => '%y', 'H' => '%H', 'G' => '%k',
            'h' => '%h', 'g' => '%l', 'i' => '%i', 's' => '%s',
            'A' => '%p', 'a' => '%p', 'M' => '%b', 'F' => '%M',
            'D' => '%a', 'l' => '%W', 'N' => '%W',
        ];

        return strtr($format, $map);
    }
}
if (! function_exists('str_random')) {
        function str_random($length = 16)
        {
            return Str::random($length);
        }
    }


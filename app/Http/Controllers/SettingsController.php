<?php

namespace App\Http\Controllers;

use App\Models\CssTheme;
use App\Models\CustomFieldset;
use App\Models\CustomField;
use App\Models\Settings;
use App\Models\Notification;
use App\Models\Currency;
use App\Models\Category;
use App\Models\Device\DeviceSetting;
use App\Models\NotificationConfig;
use App\Models\OutGoingEmail;
use App\Models\TermsConditionHistory;
use App\Models\User;
use App\Helpers\Common as CommonHelper;
use App\Models\Model;
use App\Models\Department;
use App\Models\SmtpSetting;
use Illuminate\Http\Request;
use Validator;
use Storage;
use Image;
use Log;
use Auth;
use DB;
use Exception;
use Illuminate\Support\Facades\File;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Hash;


class SettingsController extends Controller
{   
    public function outGoingIndex(Request $request) {

        $vd = [];
        $vd["out_going_email"] = OutGoingEmail::select('mail_service_enabled', 'mail_driver', 'mail_host', 'mail_port', 'mail_encryption', 'mail_username','mail_password','mail_from_address','mail_from_name')->first();
        return view("tickets.config.out_going_index")->with('vd', $vd);
    }

    /* update OutGoing Email */

    public function updateOutGoingEmail(Request $request){
        $return = ['status' => 'failure', 'msg' => 'Unable to update account'];

        $data = $request->only('mail_service_enabled', 'mail_driver', 'mail_host', 'mail_port', 'mail_encryption', 'mail_username', 'mail_password', 'mail_from_address', 'mail_from_name');

        $rules = [
            'mail_service_enabled'    => 'required|integer|min:0|max:1',
            'mail_driver'             => 'required|string|max:255',
            'mail_host'               => 'nullable|string|max:255',
            'mail_port'               => 'nullable|string|max:255',
            'mail_encryption'         => 'nullable|string|max:255',
            'mail_username'           => 'nullable|string|max:255',
            'mail_password'           => 'nullable|string|max:255',
            'mail_from_address'       => 'nullable|string|max:255',
            'mail_from_name'          => 'nullable|string|max:255'
        ];
        $messages = [];

        $validator = Validator::make($data, $rules, $messages);
        
        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }
        $email_based_ticketing ="";
        try {
            $email_based_ticketing = OutGoingEmail::first();
        }
        catch(\Exception $e) {
            $email_based_ticketing = new OutGoingEmail();
        }

        $email_based_ticketing->mail_service_enabled = $request->mail_service_enabled;
        if( $email_based_ticketing->mail_service_enabled == 0 ) {
            $email_based_ticketing->mail_driver = $request->mail_driver;
            $email_based_ticketing->mail_host = $request->mail_host;
            $email_based_ticketing->mail_port = $request->mail_port;
            $email_based_ticketing->mail_encryption = $request->mail_encryption;
            $email_based_ticketing->mail_username = $request->mail_username;
            $email_based_ticketing->mail_password = $request->mail_password;
            $email_based_ticketing->mail_from_address = $request->mail_from_address;
            $email_based_ticketing->mail_from_name = $request->mail_from_name;
        }
        
        try {
            if($email_based_ticketing->save()) {
                $return["msg"] = "Email Account has been updated successfully";
                $return["status"] = "success";
            }
        }
        catch(\Exception $e) {
            $return["msg"] = "Unable to update Email Account";
            $return["status"] = "faill";
        }

        return response()->json($return);
    }

    public function basic() {
        $settings = Settings::getSettings();
        $return = [
            'status' => 'success',
            'site_name' => $settings->site_name,
            'site_color' => $settings->header_color,
            'azure' => config('services.azure'),
            'client' => config("app.client"),
            'logo' => asset('uploads/' . $settings->logo_thumbnail)
        ];
        /*if(config("services.network_inventory.enabled")) {
            $return["agent_token"] = config("services.network_inventory.agent_token");
        }*/

        return response()->json($return);
    }

    /* new settings page */
    public function edit() {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if(! Auth::user()->hasPermissionTo('SettingEdit')) {
            return redirect('dashboard')->with("msg", $return);
        }
        $companyIds = CommonHelper::getSelectedCompanyIds();

        $settings = Settings::where('company_id', $companyIds)->first();
        $blockDeviceIntimationUsers = null;
        if(isset($settings->block_device_movement_intimation_users) && $settings->block_device_movement_intimation_users != "") {
            $userArray = explode(",", $settings->block_device_movement_intimation_users);
            $users = User::whereIn('id', $userArray)->get();
            $blockDeviceIntimationUsers = $users;
        }
        $set = NotificationConfig::where('company_id', $companyIds)->first();
        if (!$set) {
            $set = new NotificationConfig();
        }
        $ni_not_detected_set = DeviceSetting::where('company_id', $companyIds)->first();
        // dd($ni_not_detected_set);
        $assetTagData = isset($ni_not_detected_set->asset_tag_data) ? json_decode($ni_not_detected_set->asset_tag_data, true) : null;

        $editRole = [];
        if(!empty($ni_not_detected_set) && $ni_not_detected_set->ni_not_detected_value != null) {
            $editRole = DB::table('roles')->whereIn('id', explode(",", $ni_not_detected_set->ni_not_detected_value))->select('id','name')->get();
        }
        $editRoleReminder = [];
        if(!empty($ni_not_detected_set) && $ni_not_detected_set->send_reminder_users_value != null) {
            $editRoleReminder = DB::table('roles')->whereIn('id', explode(",", $ni_not_detected_set->send_reminder_users_value))->select('id','name')->get();
        }

        $editAlertRoleReminder = [];
        if(!empty($settings) && $settings->alerts_value != null) {
            $editAlertRoleReminder = DB::table('roles')->whereIn('id', explode(",", $settings->alerts_value))->select('id','name')->get();
        }
        /* get currencies */
        $get_currencies = Currency::getCurrencies();
        $currencies = [];
        foreach($get_currencies as $cur_key=>$cur_data) {
            $currencies[$cur_key] = $cur_data['name'] . " (" . $cur_data['symbol_html'] . ") ";
        }
        // if($settings->enable_2fa_authentication_with != null) {
        //     $enable_2fa_authentication_with = explode(",", $settings->enable_2fa_authentication_with);
        // } else {
        //     $enable_2fa_authentication_with = [];
        // }
        $enable_2fa_authentication_with = !empty($settings?->enable_2fa_authentication_with)? explode(',', $settings->enable_2fa_authentication_with): [];
        $customFieldset = CustomFieldset::all();
        $customFieldsetForDevice = DB::table('asset_custom_field')->get();
        $customFieldsetForSez  = CustomFieldset::where('custom_field_set_types', 3)->get();
        $customFieldsetForComponent  = DB::table('asset_custom_field')->get();
        $customFieldsetForConsumable  = DB::table('asset_custom_field')->get();
        $customFieldsetForAccessories  = DB::table('asset_custom_field')->get();
        $customFieldsetForLicense  = DB::table('asset_custom_field')->get();
        $termsConditionDropdown = TermsConditionHistory::where('company_id', $companyIds)->get();
        $colors = CssTheme::all();
        // $termsConditionObj = TermsConditionHistory::where('company_id', $companyIds)->where("id", $settings->history_id)->first();
        $termsConditionObj = $settings ? TermsConditionHistory::where('company_id', $companyIds)->where('id', $settings->history_id)->first(): null;
        $roles = DB::table('roles')->get();
        // $departments = Department::all();
        // $problemCategories = DB::table('tkt_problem_categories')->where(['department_id' => $settings->tckt_department, 'deleted_by' => NULL])->get();
        if (!Auth::user()->hasAnyRole(['SuperAdmin', 'Admin'])) {
            $roles = DB::table('roles')->whereNotIn('id', [1, 2])->get();
        }
        $smtp_config = SmtpSetting::where('company_id',$companyIds)->first();
        if (!$smtp_config) {
            $smtp_config = new SmtpSetting();
        }
        return view("settings.edit")->with("settings", $settings)
            ->with("currencies", $currencies)
            ->with("customFieldsets", $customFieldset)
            ->with( "set", $set)
            ->with("colors", $colors)
            ->with("enable_2fa_authentication_with", $enable_2fa_authentication_with)
            ->with("termsConditionObj", $termsConditionObj)
            ->with("termsConditionDropdown", $termsConditionDropdown)
            ->with('blockDeviceIntimationUsers', $blockDeviceIntimationUsers)
            ->with("customFieldsetForDevice", $customFieldsetForDevice)
            ->with("customFieldsetForAccessories", $customFieldsetForAccessories)
            ->with("customFieldsetForComponent", $customFieldsetForComponent)
            ->with("customFieldsetForConsumable", $customFieldsetForConsumable)
            ->with("customFieldsetForLicense", $customFieldsetForLicense)
            ->with("customFieldsetForSez", $customFieldsetForSez)
            ->with('roles',$roles)
            ->with('editRole',$editRole)
            ->with('editRoleReminder',$editRoleReminder)
            ->with('editAlertRoleReminder',$editAlertRoleReminder)
            ->with('assetTagData',$assetTagData)
            ->with('ni_not_detected_set',$ni_not_detected_set)
            ->with('smtp_config',$smtp_config)
            ->with('company_id',$companyIds);
            
    }

    /* update settings page */
    public function saveChanges(Request $request) {
        $return = ['status' => 'failure', 'msg' => 'Unable to update settings'];
        if(! Auth::user()->hasPermissionTo('SettingEdit')) {
            $return['msg'] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }

        $data = $request->all();
        // dd($data);
        $rules = [
            "site_name" => "required|string|max:80",
            "logo" =>"sometimes|mimes:jpeg,bmp,png,gif,svg",
            "brand" =>"required|min:1|numeric",
            "default_currency" => "required",
            "alerts_enabled" => "nullable|boolean",
            "accessory_block_checkin" => "nullable|integer|min:0|max:1",
            "auto_increment_assets" => "nullable",
            "auto_increment_prefix" =>"nullable|string|max:25",
            "qr_code" => "nullable|string",
            "barcode_type" => "nullable",
            "qr_text" => "nullable|string|max:255",
            "default_eula_text" => "nullable|string",
            "ldap_enabled" => "nullable",
            "ldap_active_flag" => "nullable",
            "ldap_emp_num" => "nullable",
            "ldap_email" => "sometimes|max:255",
            "custom_fieldset_id" => "nullable|integer",
            "licence_custom_fieldset_id" => "nullable|integer",
            "accessories_custom_fieldset_id" => "nullable|integer",
            "component_custom_fieldset_id" => "nullable|integer",
            "consumable_custom_fieldset_id" => "nullable|integer",
            "sez_custom_fieldset_id" => "nullable|integer",
            "ni_not_detected" =>"nullable|integer",
            "location_config" => "nullable|integer",
            "department_config" => "nullable|integer",
            "cost_earned" => "nullable|integer",
            "tnc_accept" => "nullable|boolean",
            // "history_id" => "required|boolean",
            "css_theme" => "nullable|integer",
        ];

        $validator = Validator::make($data, $rules);

        $validator->sometimes(['alert_email'], 'required|email', function ($input) {
            return $input->alerts_enabled == '1' ;
        });
        $validator->sometimes(['tnc_content'], 'nullable|string', function ($input) {
            return $input->tnc_accept == '1' ;
        });
        // $validator->sometimes(['history_id'], 'required|string', function ($input) {
        //     return $input->tnc_accept == '1' ;
        // });

        $validator->sometimes(['ldap_server', 'ldap_uname', 'ldap_pword', 'ldap_basedn', 'ldap_filter', 'ldap_username_field', 'ldap_lname_field','ldap_fname_field', 'ldap_auth_filter_query', 'ldap_version'], 'required', function ($input) {
            return $input->ldap_enabled == '1';
        });

        $validator->sometimes(['ldap_version'], 'required|integer', function ($input) {
            return $input->ldap_enabled == '1';
        });

        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }
        try {
            $objSettings = Settings::firstOrNew(['company_id' => $request->company_id]);
            if(isset($request->custom_fieldset_id)) {
                $cat_fieldset_id = Category::where('category_type', 'asset')->where('fieldset_id', $request->custom_fieldset_id)->get();
                if(count($cat_fieldset_id) > 0) {
                    $return["msg"] = trans('content.licenses_fields.custom_field_is_already_used_for_category');
                    return response()->json($return);
                }

                $fieldset_id = Model::where('fieldset_id', $request->custom_fieldset_id)->get();
                if(count($fieldset_id) > 0) {
                    $return["msg"] = trans('content.device_fields.custom_field_is_already_used_for_model');
                    return response()->json($return);
                }
            }
            if(isset($request->accessories_custom_fieldset_id)) {
                $fieldset_id = Category::where('category_type', 'accessory')->where('fieldset_id', $request->accessories_custom_fieldset_id)->get();

                if(count($fieldset_id) > 0) {
                    $return["msg"] = trans('content.licenses_fields.custom_field_is_already_used_for_category');
                    return response()->json($return);
                }
            }

            if(isset($request->licence_custom_fieldset_id)) {
                $fieldset_id = Category::where('category_type', 'license')->where('fieldset_id', $request->licence_custom_fieldset_id)->get();

                if(count($fieldset_id) > 0) {
                    $return["msg"] = trans('content.licenses_fields.custom_field_is_already_used_for_category');
                    return response()->json($return);
                }
            }

            if(isset($request->component_custom_fieldset_id)) {
                $fieldset_id = Category::where('category_type', 'component')->where('fieldset_id', $request->component_custom_fieldset_id)->get();

                if(count($fieldset_id) > 0) {
                    $return["msg"] = trans('content.licenses_fields.custom_field_is_already_used_for_category');
                    return response()->json($return);
                }
            }

            if(isset($request->consumable_custom_fieldset_id)) {
                $fieldset_id = Category::where('category_type', 'consumable')->where('fieldset_id', $request->consumable_custom_fieldset_id)->get();

                if(count($fieldset_id) > 0) {
                    $return["msg"] = trans('content.licenses_fields.custom_field_is_already_used_for_category');
                    return response()->json($return);
                }
            }

            if(isset($request->block_device_movement_intimation_users)) {
                $users = implode(",", $request->block_device_movement_intimation_users);
            }
            $objSettings->user_id = Auth::user()->id;
            $objSettings->company_id = $request->company_id;
            $objSettings->site_name = $data['site_name'];
            $objSettings->full_multiple_companies_support = 0;
            $objSettings->brand = $data['brand'];
            $objSettings->default_currency = $data['default_currency'];
            $objSettings->alert_email = $request->alert_email ;
            $objSettings->alerts_enabled = $request->alerts_enabled == '1' ? 1 : 0;
            $objSettings->header_color = ($objSettings->header_color == null) ? "#cd3333" : $objSettings->header_color;
            $objSettings->custom_css = null;
            $objSettings->css_theme = ($request->css_theme != null) ? $request->css_theme : null;
            $objSettings->per_page = 50;
            $objSettings->custom_css  = $request->header_color_option ;
            $objSettings->accessory_block_checkin = (int) $data['accessory_block_checkin'];
            $objSettings->block_device_movement_intimation_users = isset($users) ? $users : null;

            if ($request->clear_logo == '1') {
                if (!empty($objSettings->logo) && Storage::disk('settings')->exists($objSettings->logo)) {
                    Storage::disk('settings')->delete($objSettings->logo);
                }
                if (!empty($objSettings->logo_thumbnail)
                    && Storage::disk('uploads')->exists('settings/'.$objSettings->logo_thumbnail)) {
                    Storage::disk('uploads')->delete('settings/'.$objSettings->logo_thumbnail);
                }
                $objSettings->logo = null;
                $objSettings->logo_thumbnail = null;
            }
            // dd($request->all());
            /* check for logo upload */            
            if ($request->hasFile('logo')) {
                // Delete old files
                if (!empty($objSettings->logo) && Storage::disk('settings')->exists($objSettings->logo)) {
                    Storage::disk('settings')->delete($objSettings->logo);
                }
                if (!empty($objSettings->logo_thumbnail)
                    && Storage::disk('uploads')->exists('settings/'.$objSettings->logo_thumbnail)) {

                    Storage::disk('uploads')->delete('settings/'.$objSettings->logo_thumbnail);
                }
                $uploadedImage = $request->file('logo');
                $fileName = uniqid('', true).'.'.$uploadedImage->getClientOriginalExtension();
                $thumbName = uniqid('', true).'_thumbnail.'.$uploadedImage->getClientOriginalExtension();
                Storage::disk('settings')->putFileAs('', $uploadedImage, $fileName);
                if (!Storage::disk('uploads')->exists('settings')) {
                    Storage::disk('uploads')->makeDirectory('settings');
                }
                $manager = new ImageManager(new Driver());
                $image = $manager->read($uploadedImage->getRealPath());
                $image->scale(width: 300);
                $image->save(public_path('uploads/settings/'.$thumbName));
                $objSettings->logo = $fileName;
                $objSettings->logo_thumbnail = $thumbName;
            }

            
            $objSettings->auto_increment_assets = $request->auto_increment_assets == '1' ? 1 : 0;
            $objSettings->auto_increment_prefix = $request->auto_increment_prefix;
            $objSettings->custom_fieldset_id = $request->custom_fieldset_id;
            $objSettings->licence_custom_fieldset_id = $request->licence_custom_fieldset_id;
            $objSettings->accessories_custom_fieldset_id = $request->accessories_custom_fieldset_id;
            $objSettings->component_custom_fieldset_id = $request->component_custom_fieldset_id;
            $objSettings->consumable_custom_fieldset_id = $request->consumable_custom_fieldset_id;
            $objSettings->sez_custom_fieldset_id = $request->sez_custom_fieldset_id;
            $objSettings->qr_code = $request->qr_code == '1' ? 1 : 0;
            $objSettings->barcode_type = $request->barcode_type;
            $objSettings->qr_text = $request->qr_text;
            $objSettings->default_eula_text = trim($request->default_eula_text);
            $objSettings->ni_not_detected = $request->ni_not_detected;

            $objSettings->ldap_enabled = $request->ldap_enabled == '1' ? 1 : 0;
            $objSettings->ldap_server = $request->ldap_server;
            $objSettings->ldap_server_cert_ignore = $request->ldap_server_cert_ignore == '1' ? 1 : 0;
            $objSettings->ldap_uname = $request->ldap_uname;
            $objSettings->ldap_pword = $request->ldap_pword;
            $objSettings->ldap_basedn = $request->ldap_basedn;
            $objSettings->ldap_filter = $request->ldap_filter;
            $objSettings->ldap_username_field = $request->ldap_username_field;
            $objSettings->ldap_lname_field = $request->ldap_lname_field;
            $objSettings->ldap_fname_field = $request->ldap_fname_field;
            $objSettings->ldap_auth_filter_query = $request->ldap_auth_filter_query;
            $objSettings->ldap_version = $request->ldap_version;
            $objSettings->ldap_active_flag = $request->ldap_active_flag;
            $objSettings->ldap_emp_num = $request->ldap_emp_num;
            $objSettings->ldap_email = $request->ldap_email;
            $objSettings->location_config = $request->location_config == '1' ? 1 : 0;
            $objSettings->department_config = $request->department_config == '1' ? 1 : 0;
            $objSettings->agents_alert_enabled = $request->agents_alert_enabled == '1' ? 1 : 0;
            $objSettings->cost_earned = $request->cost_earned == '1' ? 1 : 0;
            $objSettings->enable_2fa_authentication = $request->enable_2fa_authentication == '1' ? 1 : 0;
            $objSettings->enable_2fa_authentication_with = $request->enable_2fa_authentication_with  ? implode(",",$request->enable_2fa_authentication_with) : null;
            $objSettings->tnc_accept = $request->tnc_accept == '1' ? 1 : 0;
            $objSettings->tnc_content =  $request->tnc_content;
            // $objSettings->history_id = $data['history_id'];
            $objSettings->alerts_type = $request->alerts_type == "null" ? null : $request->alerts_type;
            $objSettings->alerts_value = (isset($request->alert_value) && !empty($request->alert_value)) ? implode(",", $request->alert_value) : null;
            $objSettings->alerts_users_email = (isset($request->alert_users_email) && !empty($request->alert_users_email)) ? implode(",", $request->alert_users_email) : null;

            // --- MODIFIED BLOCK: Make DeviceSetting company-specific ---
            $obj = DeviceSetting::firstOrNew(['company_id' => $request->company_id]);
            $data = [
                'asset_tag_type' => $request->asset_tag_type,
                'asset_tag_location' => $request->asset_tag_location == '1' ? 1 : 0,
                'asset_tag_department' => $request->asset_tag_department == '1' ? 1 : 0,
                'asset_tag_pur_date' => $request->asset_tag_pur_date == '1' ? 1 : 0,
                'asset_tag_year' => $request->asset_tag_year == '1' ? 1 : 0,
                'asset_tag_month' => $request->asset_tag_month == '1' ? 1 : 0,
                'asset_tag_saperator' => $request->asset_tag_saperator,
            ];
            
            $obj->asset_tag_data = json_encode($data);
            $obj->device_report_email = (isset($request->device_report_email) && !empty($request->device_report_email)) ? implode(",", $request->device_report_email) : null;
            $obj->save();
            // --- END MODIFICATION ---

            $currentColor = Settings::firstOrNew(['company_id' => $request->company_id]);
            // if($currentColor->history_id != "" && $objSettings->tnc_accept == '1') {
            //     $Users = User::wherenotnull('tnc_accepted')->update(['tnc_accepted' => null]);
            // }

            // if(Auth::user()->isSuperUser() && isset($data['header_color']) && $data['header_color'] != $currentColor->header_color) {
            //     try {
            //         list($r, $g, $b, $a) = sscanf($data['header_color'], "#%02x%02x%02x%02x");

            //         $light = $this->hex2rgba($data['header_color'],0.3);
            //         $primary = $this->hex2rgba($data['header_color'],0.7);
            //         $secondary = $this->hex2rgba($data['header_color'],0.5);
            //         $color4 = $this->hex2rgba($data['header_color'],0.9);

            //         $objSettings->light_color = $light;
            //         $objSettings->primary_color = $primary;
            //         $objSettings->secondary_color = $secondary;
            //         $objSettings->header_color = $request->header_color;
            //         $objSettings->custom_css  = $request->header_color_option ;

            //         $fs = [
            //             resource_path('assets/sass/theme_darkaddition.scss'),
            //             public_path('js/change_management/dashboard.js'),
            //             public_path('js/dashboard/newdashboard/asset.js'),
            //             public_path('js/dashboard/newdashboard/overall.js'),
            //             public_path('js/procurement/requests/approver_info.js'),
            //             public_path('js/procurement/dashboard.js'),
            //             public_path('js/tickets/dashboard.js'),
            //             public_path('js/dashboard/newdashboard/compliance.js'),
            //             public_path('js/dashboard/newdashboard/location.js'),
            //             public_path('js/live_monitor/live_monitor_websites.js'),
            //             public_path('js/live_monitor/live_monitor_websites_details.js'),
            //             public_path('js/dashboard/newdashboard/live_monitor_dashboard.js'),
            //         ];
                    
            //         $shadesChanges = [
            //             resource_path('assets/sass/theme_darkaddition.scss'),
            //             public_path('js/change_management/dashboard.js'),
            //             public_path('js/dashboard/newdashboard/asset.js'),
            //             public_path('js/dashboard/newdashboard/overall.js'),
            //             public_path('js/procurement/requests/approver_info.js'),
            //             public_path('js/procurement/dashboard.js'),
            //             public_path('js/tickets/dashboard.js'),
            //             public_path('js/dashboard/newdashboard/compliance.js'),
            //             public_path('js/dashboard/newdashboard/location.js'),
            //             public_path('js/live_monitor/live_monitor_websites.js'),
            //             public_path('js/live_monitor/live_monitor_websites_details.js'),
            //             public_path('js/dashboard/newdashboard/live_monitor_dashboard.js'),
            //         ];

            //         foreach ($fs as $f) {
            //             if (!is_writable($f)) {
            //                 Log::error("File is not writable: $f");
            //                 continue;
            //             }
                    
            //             $file = fopen($f, 'r');
            //             if (!$file) {
            //                 Log::error("Failed to open file for reading: $f");
            //                 continue;
            //             }
                        
            //             $content = fread($file, filesize($f));
            //             fclose($file);
                    
            //             if (!isset($currentColor->header_color)) {
            //                 $color = '#cd3333';
            //             } else {
            //                 $color = $currentColor->header_color;
            //             }
                    
            //             $primaryColor = $primary;
            //             $secondaryColor = $secondary;
            //             $lightColor = $light;
            //             $currentColor->color4 = str_replace('0.3', '0.9', $currentColor->light_color);
                    
            //             if (in_array($f, $shadesChanges)) {
            //                 $stringReplaced = str_replace($color, $data['header_color'], $content);
            //                 $stringReplaced = str_replace($currentColor->primary_color, $primaryColor, $stringReplaced);
            //                 $stringReplaced = str_replace($currentColor->secondary_color, $secondaryColor, $stringReplaced);
            //                 $stringReplaced = str_replace($currentColor->light_color, $lightColor, $stringReplaced);
            //                 $stringReplaced = str_replace($currentColor->color4, $color4, $stringReplaced);
            //             } else {
            //                 $stringReplaced = str_replace($color, $data['header_color'], $content);
            //             }
                    
            //             /** Write the updated color to the file */
            //             $file = fopen($f, 'w');
            //             if (!$file) {
            //                 Log::error("Failed to open file for writing: $f");
            //                 continue;
            //             }
                    
            //             fwrite($file, $stringReplaced);
            //             fclose($file);
            //         }
                    
            //         try {
            //             if (shell_exec('npm run dev')) {
            //                 Log::info('Set ColorCode success:' . $request->header_color);
            //             }
            //         } catch(Exception $e) {
            //             Log::error('npm run dev error: ' . $e->getMessage());
            //         }
            //     } catch(Exception $e) {
            //         Log::error('Set ColorCode error: ' . $e->getMessage());
            //     }
            // }

            if($objSettings->save()) {
                $return["msg"] = "Settings has been updated successfully!";
                $return["status"] = "success";
            }
        }catch (\Exception $e) {
            // dd($e->getTrace());
            \Log::error('saveChanges error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $return["msg"] = 'An error occurred: ' . $e->getMessage();
            return response()->json($return, 500);
        }

        return response()->json($return);        
    }
    public function getTicketCategoriesByDepartment(Request $request) {
        $departmentId = $request->input('department_id');
        $categories = DB::table('tkt_problem_categories')
            ->where('department_id', $departmentId)
            ->select('id', 'name as text')
            ->get();
        return response()->json($categories);
    }
    public function updateTicketSettings(Request $request) {
        $return = ['status' => 'failure', 'message' => 'Unable to update ticket settings'];
        if(! Auth::user()->hasPermissionTo('SettingEdit')) {
            $return['message'] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $validator = Validator::make($request->all(), [
            'tckt_department' => 'required|integer|exists:departments,id',
            'tckt_prob_categ' => 'required|integer',
        ]);
        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["message"] = $e[0];
            return response()->json($return);
        }
        $settings = Settings::first();
        if (!$settings) {
            $settings = new Settings();
        }
        $settings->tckt_department = $request->input('tckt_department');
        $settings->tckt_prob_categ = $request->input('tckt_prob_categ');
        try {
            if($settings->save()) {
                $return["message"] = trans('header.application_setting_fields.ticket_settings_updated_successfully');
                $return["status"] = "success";
                return response()->json($return);
            }
        } catch(\Exception $e) {
            $return["message"] = trans('header.application_setting_fields.ticket_settings_update_failed');
            Log::error("Error updating ticket settings: " . $e->getMessage());
            return response()->json($return);
        }
        return response()->json($return);        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if(! Auth::user()->hasPermissionTo('SettingRead')) {
            return redirect('dashboard')->with("msg", $return);
        }
        $companyIds = CommonHelper::getSelectedCompanyIds();
        $settings = Settings::where('company_id', $companyIds)->first();
        if (!$settings) {
            $settings = new Settings();
        }
        return view("settings.settings-view")
        ->with("settings", $settings);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function editgeneral(Request $request){
        print_r($request->all());
    }
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function show($temp)
    {
        return Settings::all()->toJson();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function settingsEdit(Request $request)
    {
        $objSettings = Settings::first();
        $rules = [
            'site_name'         => 'required',
            'full_multiple_companies_support'=>'',
            'logo'              =>'sometimes|mimes:jpeg,bmp,png,gif',
            'brand'             =>'required|min:1|numeric',
            'default_currency'  => 'required',
            'alerts_enabled'    =>'nullable|boolean',
            'header_color'      => '',
            'custom_css'        => '',
            'per_page'          =>'required|min:1|numeric',
            'accessory_block_checkin' => 'nullable|integer|min:0|max:1',

            'auto_increment_assets' => '',
            'auto_increment_prefix' =>'nullable|string|max:25',
            'qr_code'           =>'',
            'barcode_type'      =>'',
            'qr_text'           => 'nullable|string|max:255',
            'default_eula_text' => 'nullable|string|max:255',

            'ldap_enabled'      => '',
            'ldap_active_flag'  => '',
            'ldap_emp_num'      => '',
            'ldap_email'        => 'sometimes|max:255'
        ];

        $messages = [                   ];
        $validator = Validator::make($request->all(), $rules,$messages);

        $validator->sometimes(['alert_email'], 'required|email', function ($input) {
            return $input->alerts_enabled == '1' ;
        });

        $validator->sometimes(['ldap_server', 'ldap_uname', 'ldap_pword', 'ldap_basedn', 'ldap_filter', 'ldap_username_field', 'ldap_lname_field','ldap_fname_field', 'ldap_auth_filter_query', 'ldap_version'], 'required', function ($input) {
            return $input->ldap_enabled == '1';
        });

        $validator->sometimes(['ldap_version'], 'required|integer', function ($input) {
            return $input->ldap_enabled == '1';
        });

        if ($validator->fails()) {
            //return back()->withInput()->withErrors($validator->messages());
            return array('status'=>'error','errors'=>$validator->errors()->getMessages());
        }else{


            $data = $request->validate($rules);
            // print_r($data);die;
            $objSettings->site_name         = $data['site_name'];
            $objSettings->full_multiple_companies_support = $request->full_multiple_companies_support == '1' ? 1 : 0;
            $objSettings->brand             = $data['brand'];
            $objSettings->default_currency  = $data['default_currency'];
            $objSettings->alert_email       = $request->alert_email ;
            $objSettings->alerts_enabled    = $request->alerts_enabled == '1' ? 1 : 0;
            $objSettings->header_color      = $data['header_color'];
            $objSettings->custom_css        = $data['custom_css'];
            $objSettings->per_page          = $data['per_page'];
            $objSettings->accessory_block_checkin = (int) $data['accessory_block_checkin'];

            if(isset( $data['logo'])){
                $attachment = $data['logo'];        
                unset($data['logo']);
            }

            if($request->clear_logo =='1') {//delete profile image
                Storage::disk('uploads')->delete($objSettings->logo);
                $objSettings->logo = null;
            }

            if(!empty($attachment)){  //resize and store another version of
                $logofileName    = uniqid('',true);
                $logoname        = $logofileName.'.'.$request->file('logo')->getClientOriginalExtension();
                $transfer_stat   = Storage::disk('uploads')->put($logoname, file_get_contents($request->file('logo')->getRealPath()) );
                $objSettings->logo = $logoname;

                $uploaded_img = $request->file('logo');
                $resizedLogo = $logofileName . '_thumbnail.' . $uploaded_img->getClientOriginalExtension();
                $path = public_path("uploads/" . $resizedLogo);
                Image::make(public_path("uploads/" . $logoname))->resize(300, null, function($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save($path);
                $objSettings->logo_thumbnail = $resizedLogo;
            }

            $objSettings->auto_increment_assets     = $request->auto_increment_assets == '1' ? 1 : 0;
            $objSettings->auto_increment_prefix     = $request->auto_increment_prefix;
            $objSettings->qr_code                   = $request->qr_code == '1' ? 1 : 0;
            $objSettings->barcode_type              = $request->barcode_type ;
            $objSettings->qr_text                   = $request->qr_text;
            $objSettings->default_eula_text         = $request->default_eula_text;

            $objSettings->ldap_enabled              = $request->ldap_enabled == '1' ? 1 : 0;
            $objSettings->ldap_server               = $request->ldap_server;
            $objSettings->ldap_server_cert_ignore   = $request->ldap_server_cert_ignore == '1' ? 1 : 0;
            $objSettings->ldap_uname                = $request->ldap_uname;
            $objSettings->ldap_pword                = $request->ldap_pword;
            $objSettings->ldap_basedn               = $request->ldap_basedn;
            $objSettings->ldap_filter               = $request->ldap_filter;
            $objSettings->ldap_username_field       = $request->ldap_username_field;
            $objSettings->ldap_lname_field          = $request->ldap_lname_field;
            $objSettings->ldap_fname_field          = $request->ldap_fname_field;
            $objSettings->ldap_auth_filter_query    = $request->ldap_auth_filter_query;
            $objSettings->ldap_version              = $request->ldap_version;
            $objSettings->ldap_active_flag          = $request->ldap_active_flag;
            $objSettings->ldap_emp_num              = $request->ldap_emp_num;
            $objSettings->ldap_email                = $request->ldap_email;

            if ($objSettings->save()) {
                $msg["msg"] = "Settings has been updated successfully!";
                $msg["status"] = "success";
                return array($msg);
            }
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, settings $settings)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function destroy(settings $settings)
    {
        //
    }

    public function generalSettings(Request $request){
        $objSettings = Settings::first();
        $rules = [
            'site_name' => 'required',
            'full_multiple_companies_support'=>'',
            'logo'=>'sometimes|mimes:jpeg,bmp,png,gif',
            'brand'=>'required|min:1|numeric',
            'default_currency' => 'required',
            'alerts_enabled'=>'nullable|boolean',
            'header_color' => '',
            'custom_css'=>'',
            'per_page'=>'required|min:1|numeric',
        ];
        $messages = [                   ];
        $validator = Validator::make($request->all(), $rules,$messages);
        $validator->sometimes(['alert_email'], 'required|email', function ($input) {
            return $input->alerts_enabled == '1' ;
        });
        if ($validator->fails()) {
            //return back()->withInput()->withErrors($validator->messages());
            return array('status'=>'error','errors'=>$validator->errors()->getMessages());
        }else{
            $data = $request->validate($rules);
            // print_r($data);die;
            $objSettings->site_name = $data['site_name'];
            $objSettings->full_multiple_companies_support = $request->full_multiple_companies_support == '1' ? 1 : 0;
            $objSettings->brand = $data['brand'];
            $objSettings->default_currency = $data['default_currency'];
            $objSettings->alert_email = $request->alert_email ;
            $objSettings->alerts_enabled = $request->alerts_enabled == '1' ? 1 : 0;
            $objSettings->header_color = $data['header_color'];
            $objSettings->custom_css = $data['custom_css'];
            $objSettings->per_page = $data['per_page'];

            if(isset( $data['logo'])){
                $attachment = $data['logo'];        unset($data['logo']);
            }

            if($request->clear_logo =='1') {//delete profile image
                Storage::disk('uploads')->delete($objSettings->logo);
                $objSettings->logo = null;
            }

            if(!empty($attachment)){  //resize and store another version of
                $logofileName = uniqid('',true);
                $logoname = $logofileName.'.'.$request->file('logo')->getClientOriginalExtension();
                $transfer_stat = Storage::disk('uploads')->put($logoname, file_get_contents($request->file('logo')->getRealPath()) );
                $objSettings->logo = $logoname;

                $uploaded_img = $request->file('logo');
                $resizedLogo = $logofileName . '_thumbnail.' . $uploaded_img->getClientOriginalExtension();
                $path = public_path("uploads/" . $resizedLogo);
                Image::make(public_path("uploads/" . $logoname))->resize(300, null, function($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save($path);
                $objSettings->logo_thumbnail = $resizedLogo;
            }

            if ($objSettings->save()) {
                $msg["msg"] = "General Settings updated successfully";
                $msg["status"] = "success";
                return array($msg);
            }
        }
    }

    public function assetSettings(Request $request){
        $objSettings = Settings::first();
        $rules = [
            'auto_increment_assets' => '',
            'auto_increment_prefix'=>'nullable|string|max:25',
            'qr_code'=>'',
            'barcode_type'=>'',
            'qr_text' => 'nullable|string|max:255',
            'default_eula_text' => 'nullable|string|max:255'
        ];
        $messages = [ ];
        $validator = Validator::make($request->all(), $rules,$messages);

        if ($validator->fails()) {
            //return back()->withInput()->withErrors($validator->messages());
            return array('status'=>'error','errors'=>$validator->errors()->getMessages());
        }else{
            $data = $request->validate($rules);

            $objSettings->auto_increment_assets = $request->auto_increment_assets == '1' ? 1 : 0;
            $objSettings->auto_increment_prefix = $request->auto_increment_prefix;
            $objSettings->qr_code = $request->qr_code == '1' ? 1 : 0;
            $objSettings->barcode_type = $request->barcode_type ;
            $objSettings->qr_text = $request->qr_text;
            $objSettings->default_eula_text = $request->default_eula_text;

            if ($objSettings->save()) {
                $msg["msg"] = "Asset Settings updated successfully";
                $msg["status"] = "success";
                return array($msg);
            }
        }
    }
    public function ldapSettings(Request $request){
        $objSettings = Settings::first();
        $rules = [
            'ldap_enabled' => '',
            'ldap_active_flag' => '',
            'ldap_emp_num' => '',
            'ldap_email' => 'sometimes|max:255'
        ];
        $messages = [ ];
        $validator = Validator::make($request->all(), $rules,$messages);

        $validator->sometimes(['ldap_server', 'ldap_uname', 'ldap_pword', 'ldap_basedn', 'ldap_filter', 'ldap_username_field', 'ldap_lname_field','ldap_fname_field', 'ldap_auth_filter_query', 'ldap_version'], 'required', function ($input) {
            return $input->ldap_enabled == '1';
        });

        $validator->sometimes(['ldap_version'], 'required|integer', function ($input) {
            return $input->ldap_enabled == '1';
        });

        if ($validator->fails()) {
            //return back()->withInput()->withErrors($validator->messages());
            return array('status'=>'error','errors'=>$validator->errors()->getMessages());
        }else{
            $data = $request->validate($rules);

            $objSettings->ldap_enabled              = $request->ldap_enabled == '1' ? 1 : 0;
            $objSettings->ldap_server               = $request->ldap_server;
            $objSettings->ldap_server_cert_ignore   = $request->ldap_server_cert_ignore == '1' ? 1 : 0;
            $objSettings->ldap_uname                = $request->ldap_uname;
            $objSettings->ldap_pword                = $request->ldap_pword;
            $objSettings->ldap_basedn               = $request->ldap_basedn;
            $objSettings->ldap_filter               = $request->ldap_filter;
            $objSettings->ldap_username_field       = $request->ldap_username_field;
            $objSettings->ldap_lname_field          = $request->ldap_lname_field;
            $objSettings->ldap_fname_field          = $request->ldap_fname_field;
            $objSettings->ldap_auth_filter_query    = $request->ldap_auth_filter_query;
            $objSettings->ldap_version              = $request->ldap_version;
            $objSettings->ldap_active_flag          = $request->ldap_active_flag;
            $objSettings->ldap_emp_num              = $request->ldap_emp_num;
            $objSettings->ldap_email                = $request->ldap_email;
            

            if ($objSettings->save()) {
                $msg["msg"] = "LDAP Settings updated successfully";
                $msg["status"] = "success";
                return array($msg);
            }
        }
    }

    public function summerybody(){
        $settings = Settings::all();
        return view("settings.settings-summery-tbody")
        ->with("settings", $settings);
    }

    public function hex2rgba($color, $opacity = false) {
        $default = 'rgb(0,0,0)';
        if(empty($color))
            return $default;
        if ($color[0] == '#' ) {
            $color = substr( $color, 1 );
        }
        if (strlen($color) == 6) {
            $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
        } elseif ( strlen( $color ) == 3 ) {
            $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
        } else {
            return $default;
        }
        $rgb =  array_map('hexdec', $hex);
        if($opacity){
            if(abs($opacity) > 1)
                $opacity = 1.0;
            $output = 'rgba('.implode(",",$rgb).','.$opacity.')';
        } else {
            $output = 'rgb('.implode(",",$rgb).')';
        }
        return $output;
    }

    public function saveNotification(Request $request){
        $return = ['status' => 'failure', 'msg' => 'Unable to update settings'];

        $data = $request->all();
        $rules = [
            "New_ticket_created" => "nullable|boolean",
            "Ticket_reopened" => "nullable|boolean",
            "Ticket_commented" => "nullable|boolean",
            "sla_breached" => "nullable|boolean",
            "device_checkin" => "nullable|boolean",
            "device_checkout" => "nullable|boolean",
            "New_user_added" => "nullable|boolean",
            "user_deleted" => "nullable|boolean",
            "whatsapp_notification_enabled" => "nullable|boolean",
            "company_id" => "required|integer",
        ];
        $validator = Validator::make($data, $rules);
        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $objSettings = NotificationConfig::firstOrNew([
            'company_id' => $request->company_id
        ]);

        $objSettings->company_id = $request->company_id;
        $objSettings->new_ticket_created = $request->New_ticket_created == '1' ? 1 : 0;
        $objSettings->ticket_reopened = $request->Ticket_reopened == '1' ? 1 : 0;
        $objSettings->ticket_commented = $request->Ticket_commented == '1' ? 1 : 0;
        $objSettings->sla_breached = $request->sla_breached == '1' ? 1 : 0;
        $objSettings->new_user_added = $request->New_user_added == '1' ? 1 : 0;
        $objSettings->user_deleted = $request->user_deleted == '1' ? 1 : 0;
        $objSettings->whatsapp_notification_enabled = $request->whatsapp_notification_enabled == '1' ? 1 : 0;

        if ($objSettings->save()) {
            $return["status"] = "success";
        }

        return response()->json($return);

    }

    public function niNotDetectedNotification(Request $request) {
        $return = ['status' => 'error', 'msg' => 'Unable to update settings'];
        $data = $request->all();
        // dd($data);
        $rules = [
            "ni_not_detected_type" => "nullable",
            "ni_not_detected_value" => "nullable",
            "ni_not_detected_email" => "nullable",
            "ni_not_detected" =>"nullable|integer",
        ];

        $validator = Validator::make($data, $rules);
        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $objSettings = DeviceSetting::where('company_id' , $request->company_id)->first();
        if(!empty($objSettings)) {
            $objSettings->device_checkin = isset($request->device_checkin) ? ($request->device_checkin == "1" ? 1:0):0;
            $objSettings->device_checkout = isset($request->device_checkout) ? ($request->device_checkout == "1" ? 1:0):0;
            $objSettings->live_monitor_auto_refresh = isset($request->live_monitor_auto_refresh) ? $request->live_monitor_auto_refresh:$objSettings->live_monitor_auto_refresh;
            $objSettings->live_monitor_auto_run_in_minute = isset($request->live_monitor_auto_run_in_minute) ? $request->live_monitor_auto_run_in_minute:$objSettings->live_monitor_auto_run_in_minute;
            $objSettings->high_priority_ni_not_detected = isset($request->high_priority_ni_not_detected) ? ($request->high_priority_ni_not_detected == "1" ? 1:0):0;
            $objSettings->ni_not_detected = $request->ni_not_detected ?? 1;
            $objSettings->ni_not_detected_type = $request->ni_not_detected_type == "null" ? null : $request->ni_not_detected_type;
            $objSettings->ni_not_detected_value = (isset($request->ni_not_detected_value) && !empty($request->ni_not_detected_value)) ? implode(",", $request->ni_not_detected_value) : null;
            $objSettings->ni_not_detected_email = (isset($request->ni_not_detected_email) && !empty($request->ni_not_detected_email)) ? implode(",", $request->ni_not_detected_email) : null;
            $objSettings->send_reminder = isset($request->send_reminder) ? ($request->send_reminder == "1" ? 1:0):0;
            $objSettings->number_of_devices = $request->number_of_devices ?? 0;
            $objSettings->send_reminder_users_type = $request->send_reminder_users_type == "null" ? null : $request->send_reminder_users_type;
            $objSettings->send_reminder_users_value = (isset($request->send_reminder_users_value) && !empty($request->send_reminder_users_value)) ? implode(",", $request->send_reminder_users_value) : null;
            $objSettings->send_reminder_users_email = (isset($request->send_reminder_users_email) && !empty($request->send_reminder_users_email)) ? implode(",", $request->send_reminder_users_email) : null;
        } else {
            $objSettings = new DeviceSetting();
            $objSettings->device_checkin = isset($request->device_checkin) ? ($request->device_checkin == "1" ? 1: 0) : 0;
            $objSettings->device_checkout = isset($request->device_checkout) ? ($request->device_checkout == "1" ? 1:0):0;
            $objSettings->live_monitor_auto_refresh = isset($request->live_monitor_auto_refresh) ? $request->live_monitor_auto_refresh:0;
            $objSettings->live_monitor_auto_run_in_minute = isset($request->live_monitor_auto_run_in_minute) ? $request->live_monitor_auto_run_in_minute:5;
            $objSettings->high_priority_ni_not_detected = isset($request->high_priority_ni_not_detected) ? ($request->high_priority_ni_not_detected == "1" ? 1:0):0;
            $objSettings->ni_not_detected = $request->ni_not_detected ?? 10;
            $objSettings->ni_not_detected_type = $request->ni_not_detected_type == "null" ? 1 : $request->ni_not_detected_type;
            $objSettings->ni_not_detected_value = (isset($request->ni_not_detected_value) && !empty($request->ni_not_detected_value)) ? implode(",", $request->ni_not_detected_value) : 1;
            $objSettings->ni_not_detected_email = (isset($request->ni_not_detected_email) && !empty($request->ni_not_detected_email)) ? implode(",", $request->ni_not_detected_email) : null;
            $objSettings->send_reminder = isset($request->send_reminder) ? ($request->send_reminder == "1" ? 1:0):0;
            $objSettings->number_of_devices = $request->number_of_devices ?? 0;
            $objSettings->send_reminder_users_type = $request->send_reminder_users_type == "null" ? null : $request->send_reminder_users_type;
            $objSettings->send_reminder_users_value = (isset($request->send_reminder_users_value) && !empty($request->send_reminder_users_value)) ? implode(",", $request->send_reminder_users_value) : null;
            $objSettings->send_reminder_users_email = (isset($request->send_reminder_users_email) && !empty($request->send_reminder_users_email)) ? implode(",", $request->send_reminder_users_email) : null;
            $objSettings->company_id = $request->company_id;
        }
        $objNotificationSettings = NotificationConfig::where('company_id' , $request->company_id)->first();
        if(!empty($objNotificationSettings)) {
            $objNotificationSettings->device_checkin = isset($request->device_checkin) ? ($request->device_checkin == "1" ? 1: 0) : 0;
            $objNotificationSettings->device_checkout = isset($request->device_checkout) ? ($request->device_checkout == "1" ? 1:0):0;
            $objNotificationSettings->save();
        }
        if($objSettings->save()) {
            $return["msg"] = trans('settings.notification.notification_update_success');
            $return["status"] = "success";
        }
        return response()->json($return);
    }

    public function smtpConfig(Request $request)
    {
        $return = ['status' => 'error', 'msg' => 'Unable to update email settings'];
        $data = $request->all();
        $rules = [
            'mail_driver'          => 'required|string|max:50',
            'mail_host'            => 'required|string|max:255',
            'mail_port'            => 'required|integer|min:1|max:65535',
            'mail_from_address'    => 'required|email|max:255',
            'mail_from_name'       => 'required|string|max:255',
            'mail_username'        => 'nullable|string|max:255',
            'mail_password'        => 'nullable|string|max:255', // optional
            'mail_encryption'      => 'nullable|string|in:tls,ssl,',
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }
        if (empty($request->company_id)) {
            $return['msg'] = 'Company ID is required.';
            return response()->json($return);
        }
        $settings = SmtpSetting::where('company_id', $request->company_id)->first();
        if (!$settings) {
            $settings = new SmtpSetting();
            $settings->company_id = $request->company_id;
        }

        $settings->mail_service_enabled = $request->has('mail_service_enabled') && $request->mail_service_enabled == '1' ? 1 : 0;
        $settings->mail_driver          = $request->mail_driver;
        $settings->mail_host            = $request->mail_host;
        $settings->mail_port            = (int) $request->mail_port;
        $settings->mail_username        = $request->mail_username ?? null;
        $settings->mail_encryption      = $request->mail_encryption ?? null;
        $settings->mail_from_address    = $request->mail_from_address;
        $settings->mail_from_name       = $request->mail_from_name;

        if ($request->filled('mail_password')) {
            $settings->mail_password = Hash::make($request->mail_password);
        }
       
        if ($settings->save()) {
            $return["msg"]    = trans('settings.notification.email_settings_updated') ?: 'Email settings updated successfully.';
            $return["status"] = "success";
        }

        return response()->json($return);
    }
}

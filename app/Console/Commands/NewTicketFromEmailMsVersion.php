<?php

namespace App\Console\Commands;
use App\Events\TicketCreated;
use App\Helpers\Common as CommonHelper;
use App\Http\Controllers\Ticket\IndexController;
use App\Jobs\TktEventWithProcessTicketSentiment;
use App\Models\Location;
use App\Models\Ticket\Request\TicketRequestHistory;
use App\Models\Ticket\TicketTrigger;
use Illuminate\Console\Command;
use App\Models\Settings;
use App\Models\User;
use App\Models\Group;
use App\Models\UserGroup;
use App\Models\Holiday;
use App\Models\Department;
use App\Models\Ticket\AutoCreateFromEmail;
use App\Models\Ticket\AutoCreationAccount;
use App\Models\Ticket\ProblemCategory;
use App\Models\Ticket\Ticket;
use App\Models\Ticket\AliasEmailAccount;
use App\Models\Ticket\Config;
use App\Models\Ticket\Status;
use App\Models\TktFollowing;
use App\Models\Ticket\Attachment;
use App\Models\Ticket\ExceptionNotification;
use App\Models\Ticket\TicketProcureRequest;
use App\Models\Ticket\TicketPab;
use App\Http\Controllers\Ticket\RequestController;
use App\Models\Ticket\TicketPabMember;
use App\Models\Ticket\TicketApprovalRequest;
use App\Models\Ticket\TktDetail;
use App\Mail\Ticket\Request\CreationServiceRequest;
use App\Mail\Ticket\Request\TicketApproverInfo;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Log;
use DB;
use Mail;
use Spatie\Permission\Models\Role;
use Validator;
use Storage;
use Image;
use Carbon\Carbon;
use Webklex\IMAP\Client;

use App\Mail\Ticket\IntimateSuccessCreation;
use App\Mail\Ticket\IntimateAssigned;
use App\Mail\Ticket\Resolved;
use App\Mail\Ticket\StatusChanged;
use App\Mail\Ticket\UserComment;
use App\Mail\UserCredentialNotification;
use App\Http\Traits\OutlookTrait;
use App\Models\TKTAutoUpdateSetting;

class NewTicketFromEmailMsVersion extends Command
{

    use OutlookTrait;
    public const LOGID = 'NewTicketFromMSEmail';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'new_ticket_from_ms_email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Program to generate new service ticket from email';

    protected $suspect_words = ["issue", "complaint", "request", "need", "urgent", "shop", "problem", "resolve", "solution", "require", "make", "ready", "avail", "please", "create", "add", "activate", "required", "help", "give", "alert", "critical"];

    protected $account_config;

    protected $credentials, $auto_create_email, $rules;

    protected $tkt_status_arr, $tkt_status_arr_with_id, $tkt_config, $allowed_domains, $allowed_emails;

    protected $def_department, $def_prob_category, $def_sub_category, $reserved_ticket_options;

    const CONTENT_DIVIDER = '--- please write reply above this line ---';

    protected $blocked_accounts = "";

    protected $holidays = [];

    /* alias accounts */
    protected $alias_count = 0;
    protected $alias_accounts = [];
    protected $alias_account = null;
    protected $alias_email = "";

    /* data for user account creation */
    protected $newly_generated_user = false;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function load_tkt_status() {
        try {
            $j = DB::table('tkt_statuses')->select('id', 'name')->get();
            foreach($j as $x) {
                $this->tkt_status_arr[] = strtolower($x->name);
                $this->tkt_status_arr_with_id[] = [
                    'id' => $x->id,
                    'name' => strtolower($x->name)
                ];
            }
        }
        catch(\Exception $e) {
            Log::error("load_tkt_status: " . $e->getMessage());
        }
    }

    protected function getStatusIdByName($name) {
        try {
            foreach($this->tkt_status_arr_with_id as $x) {
                if($x['name'] == $name) {
                    return $x['id'];
                }
            }

            return false;
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    protected function load_account_info() {
        try {
			$aem = DB::table('tkt_ac_email_accounts')->where("email", "like", trim($this->credentials["username"]))->get();
			$this->auto_create_email = $aem[0];
            // $this->auto_create_email = AutoCreateFromEmail::where("email", "like", $this->credentials["username"])->first();
			if($this->auto_create_email) {
				return true;
			}
			return false;
        }
        catch(\Exception $e) {
            $this->auto_create_email = null;
            Log::error("load_account_info: " . $e->getMessage());
			return false;
        }
    }

    protected function check_is_processed($uid, $msg_id) {
        if($uid) {
            try {
                $get_count = Ticket::where("ac_email_uid", "like", $uid)->where("ac_email_id", "=", $this->account_config->id)->count();
                if($get_count) {
                    return true;
                }
            }
            catch(\Exception $e) {
                Log::error("check_is_processed uid: " . $e->getMessage());
            }
        }

        if($msg_id) {
            try {
                $get_count = Ticket::where("ac_email_message_id", "like", $msg_id)->where("ac_email_id", "=", $this->account_config->id)->count();
                if($get_count) {
                    return true;
                }
            }
            catch(\Exception $e) {
                Log::error("check_is_processed msg_id: " . $e->getMessage());
            }
        }

        return false;
    }

    protected function check_is_processed_comment($uid, $msg_id) {
        if($uid) {
            try {
                $get_count = TktFollowing::where("ac_email_uid", "like", $uid)->where("ac_email_id", "=", $this->account_config->id)->count();
                if($get_count) {
                    return true;
                }
            }
            catch(\Exception $e) {
                Log::error("check_is_processed_comment uid: " . $e->getMessage());
            }
        }

        if($msg_id) {
            try {
                $get_count = TktFollowing::where("ac_email_message_id", "like", $msg_id)->where("ac_email_id", "=", $this->account_config->id)->count();
                if($get_count) {
                    return true;
                }
            }
            catch(\Exception $e) {
                Log::error("check_is_processed_comment msg_id: " . $e->getMessage());
            }
        }

        return false;
    }

    protected function load_only_existing_user($user_info) {
        try {
            $allow = false;
            if( is_array($this->allowed_emails) ) {
                if( in_array($user_info, $this->allowed_emails) ) {
                    $allow = true;
                }
            }

            if( $allow == false && is_array( $this->allowed_domains) ) {
                foreach( $this->allowed_domains as $ad ) {
                    if( stripos($user_info, ('@' . $ad)) > 0 ) {
                        $allow = true;
                        break;
                    }
                }

                if(! $allow) {
                    return false;
                }
            }

            $get_user = User::where("email", "like", $user_info)->where('activated', 1)->first();
            
            if($get_user) {
                $this->user = $get_user;
                return true;
            }
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
        }

        return false;
    }

    protected function load_user($user_info) {
        try {
            $this->newly_generated_user = false;

            $allow = false;
            if( is_array($this->allowed_emails) ) {
                if( in_array($user_info, $this->allowed_emails) ) {
                    $allow = true;
                }
            }

            if( $allow == false && is_array($this->allowed_domains) ) {
                foreach( $this->allowed_domains as $ad ) {
                    if( stripos($user_info, ('@' . $ad)) > 0 ) {
                        $allow = true;
                        break;
                    }
                }

                if(! $allow) {
                    return false;
                }
            }

            $get_user = User::withTrashed()->where("email", "like", $user_info)->first();
            if($get_user) {
                $this->user = $get_user;
                if($get_user->activated == 0 || $get_user->deleted_at != null) {
                    return false;    
                }
                return true;
            }

            $name_prepare = preg_replace("/[^A-Za-z0-9 ]/", '', strip_tags($user_info));
            $new_account = [];
            $new_account["first_name"] = $name_prepare;
            $new_account["last_name"] = $name_prepare;
            $new_account["username"] = $user_info;
            $new_account["password"] = 'temp-pw';
            $new_account["company_id"] = Auth::user()->company_id;
            $new_account["email"] = $user_info;
            $new_account["job_type"] = 2;
            $new_account["activated"] = 1;
            $new_account["created_by"] = Auth::user()->id;
            $new_account["create_mode"] = 3;
            $otherLocation = Location::where('name', 'like', 'Other')->first();
            if(empty($otherLocation)) {
                $otherLocation = new Location();
                $otherLocation->name = $otherLocation->address = "Other";
                $otherLocation->country = "IN";
                $otherLocation->country_id = 101;
                $otherLocation->state_id = 4008;
                $otherLocation->city_id = 133024;
                $otherLocation->currency = "INR";
                $otherLocation->user_id = Auth::user()->id;
                $otherLocation->save();
            }
            $new_account["location_id"] = (Auth::user()->location_id == "") ? $otherLocation->id : Auth::user()->location_id;
            
            // $get_user_group = Group::where("permissions", "like", '%"users":1%')->first();

            $this->user = User::create($new_account);
            if(! $this->user) {
                return false;
            }
            
            $this->user->setPassword(null, true);
            $this->user->save();
            $roleObj = Role::where('name', 'User')->first();
            if(!empty($roleObj)) {
                $this->user->assignRole($roleObj);
            }
            /*$group_record = [
                'user_id' => $this->user->id,
                'group_id' => $get_user_group->id
            ];
            UserGroup::create($group_record);*/
            $this->newly_generated_user = true; 
            return true;
        }
        catch(\Exception $e) {
            Log::error(self::LOGID . ": load_user: " . $e->getMessage());
            return false;
        }
    }

    protected function find_matched_alias($email) {
        if(is_array($email)) {
            foreach ($email as $mail) {
                $email = strtolower($mail);
                foreach ($this->alias_accounts as $alias_account) {
                    if (strtolower($alias_account->alias_email) == $email) {
                        return $alias_account;
                    }
                }
            }
        } else {
            $email = strtolower($email);
            foreach ($this->alias_accounts as $alias_account) {
                if (strtolower($alias_account->alias_email) == $email) {
                    return $alias_account;
                }
            }
        }

        return false;
    }

    protected function reserve_default_ticket_options() {
        $this->reserved_ticket_options = [];
        $this->reserved_ticket_options['def_department'] = $this->def_department;
        $this->reserved_ticket_options['def_prob_category'] = $this->def_prob_category;
        $this->reserved_ticket_options['def_sub_category'] = $this->def_sub_category;
    }

    protected function restore_default_ticket_options() {
        $this->def_department = $this->reserved_ticket_options['def_department'];
        $this->def_prob_category = $this->reserved_ticket_options['def_prob_category'];
        $this->def_sub_category = $this->reserved_ticket_options['def_sub_category'];
    }

    protected function load_alias_ticket_options(AliasEmailAccount $alias) {
        try {
            if( $alias->default_department_id && $alias->default_prob_cat_id ) {
                // $this->info("alias option:" . $alias->default_department_id . " - " .  $alias->default_prob_cat_id . " - " . $alias->default_sub_cat_id);
                $this->def_department = Department::findOrFail($alias->default_department_id);
                $this->def_prob_category = ProblemCategory::findOrFail($alias->default_prob_cat_id);

                if($this->def_prob_category->department_id != $this->def_department->id || $this->def_department->module_ticket_enabled != 1) {
                    throw new \Exception("Uncorrected department configured for alias ticket");
                }

                $this->def_sub_category = false;
                if( $this->def_prob_category->totSubCategories() > 0 ) {
                    if(isset($alias->default_sub_cat_id) && $alias->default_sub_cat_id > 0) {
                        $this->def_sub_category = ProblemCategory::where('id', $alias->default_sub_cat_id)->first();
                    }
                }

                return $this->def_department && $this->def_prob_category && $this->def_sub_category;
            }

            return false;
        }
        catch(\Exception $e) {
            return false;
        }
    }

    protected function load_default_ticket_options() {
        try {
            $this->alias_account = null;
            /* load default as per configuration */
            if( $this->account_config->default_department_id && $this->account_config->default_prob_cat_id ) {
                $this->def_department = Department::findOrFail($this->account_config->default_department_id);
                $this->def_prob_category = ProblemCategory::findOrFail($this->account_config->default_prob_cat_id);
                if($this->def_prob_category->department_id != $this->def_department->id || $this->def_department->module_ticket_enabled != 1) {
                    throw new \Exception("Uncorrected department configured for ticket");
                }

                $this->def_sub_category = false;
                if( $this->def_prob_category->totSubCategories() > 0 && $this->account_config->default_sub_cat_id) {
                    $this->def_sub_category = ProblemCategory::where('id', $this->account_config->default_sub_cat_id)->where('parent_id', $this->def_prob_category->id)->first();
                }

                return $this->def_department && $this->def_prob_category;
            }

            /* if no default configured, the auto select by possibilities */
            $get_departments = Department::where("company_id", "=", Auth::user()->company_id)->where("module_ticket_enabled", "=", 1)->select('id')->get();
            $department_ids = $get_departments->pluck('id');
            $this->def_prob_category = ProblemCategory::whereIn('department_id', $department_ids)->first();
            $this->def_department = Department::find($this->def_prob_category->department_id);

            $this->def_sub_category = false;
            if( $this->def_prob_category->totSubCategories() > 0 ) {
                $this->def_sub_category = ProblemCategory::where('parent_id', $this->def_prob_category->id)->first();
            }

            return $this->def_department && $this->def_prob_category;
        }
        catch(\Exception $e) {
            Log::error("load_default_ticket_options: " . $e->getMessage());
            return false;
        }
    }

    protected function updateTicketWithStatus(&$st, &$param_data, &$msg) {

        $ticket_id = $st->id;
        $data['status_id'] = $param_data['status_id'];
        $is_status_changing_now = $st->status_id != $data['status_id'];

        /* ticket getting closed (or) reopend by the user */
        // $needToStoreComment = false;
        // if(($st->status_id != $data['status_id'] && in_array($data['status_id'], [2,5])) || (! empty($this->tkt_config) && $this->tkt_config->mail_all_status_changes)) {
        //     $needToStoreComment = true;
        // }

        $is_tat_changed = false;

        $is_resolved_now = false;
        if($data["status_id"] != $st->status_id && $st->status_id != 6 && $data["status_id"] == 5) {
            $is_resolved_now = true;
            $st->resolved_at = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');

            if( $this->tkt_config->isReqDeptChgBfrResolve == 1 && $this->tkt_config->default_department_id == $st->department_id ) {
                $data["status_id"] = $st->status_id;
                $is_status_changing_now = false;
                $is_resolved_now = false;
                $st->resolved_at = null;
            }
        }

        /* for tat halt hrs calculation */
        $target_status = Status::find($data["status_id"]);
        if($data["status_id"] != $st->status_id) {
            if($target_status->tat_halt && !$st->status->tat_halt) {
                // need calc
                $expire_at_carbon = Carbon::createFromFormat('Y-m-d H:i:s', $st->tat_expire);
                // $data["tat_remaining_mins"] = Carbon::now(config('app.timezone'))->diffInMinutes($expire_at_carbon);
                $data["tat_remaining_mins"] = $this->tkt_config->calculateRemainingTat($expire_at_carbon, $this->holidays);
            }
            elseif(!$target_status->tat_halt && $st->status->tat_halt) { 
                // need calc
                if($st->tat_remaining_mins && !$is_tat_changed) {
                    // $st->tat_expire = Carbon::now(config('app.timezone'))->addMinutes($st->tat_remaining_mins)->format('Y-m-d H:i:s');
                    $st->tat_expire = $this->tkt_config->calculateAdvancedTat($st->tat_remaining_mins, Carbon::now(config('app.timezone')), $this->holidays, "m");
                }
                $st->tat_remaining_mins = 0;
            }
        }

        $st->fill($data);
        $st->save();
        $st = Ticket::find($ticket_id);

        $alertnotify = null;
        if($this->tkt_config->alerts_enabled == 1){
            $alertnotify = $this->tkt_config->alert_email;
        }

        /* ticket got closed (or) reopened just now. Or as per config, need to store the user comment on followings */
        // if($needToStoreComment) {
        $tf = new TktFollowing();
        $tf->ticket_id = $st->id;
        $tf->is_note = 0;

        $processResult = Attachment::processForB64Imgs($param_data['comment'], $st->id, $this->user->id);
        $param_data['comment'] = $processResult['content'];
        $tf->remarks = trim($param_data['comment']);

        $tf->ac_email_id = $this->account_config->id;
        $tf->ac_email_uid = $msg->id;
        $tf->ac_email_message_id = $msg->internetMessageId;
        $tf->alias_acc_id = $this->alias_account ? $this->alias_account->id : null;
		
		/* get CC's */
        $config = Config::first();
        // if($config->checked_cc_checkbox == 1){
            $cc_emails = $this->make_cc_list($msg->toRecipients, $msg->ccRecipients);
            if( count($cc_emails) ) {
                $tf->cc_emails = implode(",", $cc_emails);

                /* check any new email address found, if yes, then add them to master cc emails */
                $new_cc_master = $this->checkNewCcAddress($st->cc_emails, $cc_emails);
                if( is_array($new_cc_master) && count($new_cc_master) ) {
                    $st->cc_emails = implode(",", $new_cc_master);
                }
            }
        // }
        $tf->updated_by = $this->user->id;
        $tf->updated_status = $is_status_changing_now ? $data['status_id'] : null;
        $tf->action_type = $is_status_changing_now ? 2 : 7;
        $tf->save();

        $tkt_update['ticket_id'] = $st->id;
        $tkt_update['status'] = $data['status_id'];
        $tkt_update['action_type'] = 2;
        $tkt_update['updated_by'] = $this->user->id;
        CommonHelper::ticketStatusHistory($tkt_update);
        // }

        if( count($processResult) && count($processResult['attachments']) ) {
            Attachment::whereIn('id', $processResult['attachments'])->update(['following_id' => $tf->id]);
        }

        /* attachment adding for ticket following */
        $sender = $msg->sender->emailAddress->address;
        $msg_attachments = $this->getMailAttachment($this->account_config->ebts_username, $msg->id);
        if(!empty($msg_attachments)) {
            foreach($msg_attachments as $messageAttachment) {
                $this->add_attachment($msg, $messageAttachment, $st->id);
            }
        }

        $st->updated_at = date('Y-m-d H:i:s');
        $st->save();

        if($is_resolved_now) {
            $creator = $st->creator;
            $handler = User::find($st->assigned_to);
            $dep = Department::find($st->department_id);
            $dephandler = User::find($dep->attender_id);

            if(config('mail.service_enabled') && $creator && $creator->email && filter_var($creator->email, FILTER_VALIDATE_EMAIL)) {
                try {
                    if($this->tkt_config->cc_to_settings_mail === 1 && $alertnotify) {
                        Mail::to($creator->email)->cc($alertnotify)->queue(new Resolved($st, $creator, trim($param_data['comment']), $tf));
                    }
                    else {
                        Mail::to($creator->email)->queue(new Resolved($st, $creator, trim($param_data['comment']), $tf));
                    }
                }
                catch(\Exception $e) {
                    Log::error('Issue at 326 of newticktfrom email cron');
                    Log::error($e->getMessage());
                }
            }
        }
        elseif(!empty($this->tkt_config) && $this->tkt_config->mail_all_status_changes) {
            $creator = $st->creator;
            $handler = User::find($st->assigned_to);
            $dep = Department::find($st->department_id);
            $dephandler = User::find($dep->attender_id);

            if(config('mail.service_enabled') && $creator && $creator->email && filter_var($creator->email, FILTER_VALIDATE_EMAIL)) {
                try {
                    if($this->tkt_config->cc_to_settings_mail === 1 && $alertnotify) {
                        Mail::to($creator->email)->cc($alertnotify)->queue(new StatusChanged($st, $creator, trim($param_data['comment']), $tf));
                    }
                    else {
                        Mail::to($creator->email)->queue(new StatusChanged($st, $creator, trim($param_data['comment']), $tf));
                    }
                }
                catch(\Exception $e) {
                    Log::error('Issue at 326 of newticktfrom email cron');
                    Log::error($e->getMessage());
                }
            }
        }

        return true;
    }

    protected function is_updatable_comment($suspected_id, &$msg, $mail_subject, $mail_body) {
        try {
            // Log::info("is_updatable_comment ");
            $sender = $msg->sender->emailAddress->address;
            $mail_body = CommonHelper::extractMailContent($mail_body);
            try {
                $ticket = Ticket::findOrFail($suspected_id);
                /*if($ticket->is_temp || in_array($ticket->status_id, [6])) {
                    $this->_logException($msg, $sender, $mail_subject, "The target ticket #{$suspected_id} might be closed/invalid");
                    return false;
                }*/
            }
            catch(\Exception $e) {
                $this->_logException($msg, $sender,  $mail_subject, "The target ticket #{$suspected_id} might be closed/invalid");
                return false;
            }

            /* check whether is it already processed */
            if($this->check_is_processed($msg->id, $msg->internetMessageId)) {
                return false;
            }

            /* check whether is it already processed comment */
            if($this->check_is_processed_comment($msg->id, $msg->internetMessageId)) {
                return false;
            }

            /* check for user by "From" email address. If not exists, then create & continue the following steps */
            if(! $this->load_user($sender)) {
                $this->_logException($msg, $sender,  $mail_subject, "Third-party email for the ticket #{$suspected_id} has been stopped. It might be the reason of blocked domain/accounts.");
                return false;
            }

            /* create new ticket comment */
            $ticket_data = array(
                "subject" => $mail_subject,
                "content" => $mail_body
            );

            $validator = Validator::make($ticket_data, $this->rules, []);
        
            if($validator->fails()) {
                Log::error("Ticket Update via Email Error:");
                $v = $validator->errors()->toArray();
                Log::error($v);
                $this->_logException($msg, $sender, $mail_subject, "Either content is empty (or) might be contain invalid characters.", $ticket->id);
                return false;
            }

            /* check for intention to change status, if yes, then call controller function */
            $lower_subject = strtolower($mail_subject);
            $is_status_tag_found = stripos($lower_subject, '###status:');
            if( $is_status_tag_found !== false && $this->user->id != $ticket->creator_id && $this->user->id == $ticket->assigned_to ) {
                $status_word = trim(substr($lower_subject, $is_status_tag_found + 10));
                
                $end_hash_part = strpos($status_word, '#');
                if( $end_hash_part !== false ) {
                    $status_word = substr($status_word, 0, $end_hash_part);
                }
                // Log::info("word ".$status_word);
                // Log::info($status_word." -- ".json_encode($this->tkt_status_arr));

                if( in_array($status_word, $this->tkt_status_arr) ) {
                    $status_id = $this->getStatusIdByName($status_word);

                    $param_data = [];
                    $param_data['status_id'] = $status_id;
                    $param_data['comment'] = trim($mail_body);
                    $so_result = $this->updateTicketWithStatus($ticket, $param_data, $msg);

                    if( $so_result ) {
                        // $msg->setFlag(['Seen']);
                        $this->setMessageAsSeen($this->account_config->ebts_username, $msg->id);
                    }
 
                    return $so_result;
                }
            } 

            /* is reopen by ticket creator */
            $is_reopen = false;
            if( $ticket->creator_id == $this->user->id && $ticket->status_id == 5 ) {
                /* assume the creator wants to reopen the ticket */
                $is_reopen = true;
            }
			
            $tf = new TktFollowing();
            $tf->ticket_id = $ticket->id;
            // $tf->remarks = trim($mail_body);
            $tf->is_note = 0;
            $tf->updated_by = $this->user->id;

            $tf->ac_email_id = $this->account_config->id;
            $tf->ac_email_uid = $msg->id;
            $tf->ac_email_message_id = $msg->internetMessageId;
            $tf->alias_acc_id = $this->alias_account ? $this->alias_account->id : null;

            $processResult = Attachment::processForB64Imgs($mail_body, $ticket->id, Auth::user()->id);
            $tf->remarks = $processResult['content'];
			
			/* get CC's */
            $config = Config::first();
            // if($config->checked_cc_checkbox == 1){
                $cc_emails = $this->make_cc_list($msg->toRecipients, $msg->ccRecipients);
                if( count($cc_emails) ) {
                    $tf->cc_emails = implode(",", $cc_emails);

                    /* check any new email address found, if yes, then add them to master cc emails */
                    $new_cc_master = $this->checkNewCcAddress($ticket->cc_emails, $cc_emails);
                    if( is_array($new_cc_master) && count($new_cc_master) ) {
                        $ticket->cc_emails = implode(",", $new_cc_master);
                    }
                }
            // }

            if($is_reopen) {
                $tf->action_type = 2;
                $tf->updated_status = 2;
                $ticket->status_id = 2;
            }
            else {
                $tf->action_type = 7;
                if($ticket->is_temp == 1) {
                    $tf->is_service_request = 1;
                } else {
                    $tf->is_service_request = 0;
                }
            }

            if(! $tf->save()) {
                Log::error("Ticket Update via Email Error: Unable to save tf");
                return false;
            }

            if( count($processResult) && count($processResult['attachments']) ) {
                Attachment::whereIn('id', $processResult['attachments'])->update(['following_id' => $tf->id]);
            }

            /* attachment adding for ticket following */
            $msg_attachments = $this->getMailAttachment($this->account_config->ebts_username, $msg->id);
            if(!empty($msg_attachments)) {
                foreach ($msg_attachments as $messageAttachment) {
                    $this->add_attachment($msg, $messageAttachment, $ticket->id);
                }
            }

            $ticket->updated_at = date('Y-m-d H:i:s');
            $ticket->save();

            /* notify user / handler based on commenter */
            $alertnotify = null;
            if($this->tkt_config->alerts_enabled == 1){
                $alertnotify = $this->tkt_config->alert_email;
            }

            $is_user_same_creator = $ticket->creator_id == $this->user->id ? true : false;
            $creator = User::find($ticket->creator_id);
            $handler = User::find($ticket->assigned_to);
            $is_user_cc = $ticket->norCreatorOrHandler($this->user->id);
            
            if(config('mail.service_enabled')) {
                if( $is_user_cc ) {
                    /* if commenter is cc, then inform to both creator and handler */
                    try {
                        $temp_to = [];
                        if( $creator && $creator->email && filter_var($creator->email, FILTER_VALIDATE_EMAIL) ) {
                            $temp_to[] = $creator->email;
                        }
                        if( $handler && $handler->email && filter_var($handler->email, FILTER_VALIDATE_EMAIL) ) {
                            $temp_to[] = $handler->email;
                        }

                        if($this->tkt_config->cc_to_settings_mail === 1 && $alertnotify) {
                            Mail::to($temp_to)->cc($alertnotify)->queue(new UserComment($ticket, $creator, $tf->remarks, $tf));
                        }
                        else {
                            Mail::to($temp_to)->queue(new UserComment($ticket, $creator, $tf->remarks, $tf));
                        }
                    }
                    catch(\Exception $e) {
                        Log::error("Issue Commenter is cc: ");
                        Log::error($e->getMessage());
                    }

                }
                /* commenter is ticket creator then send notification to attendar */
                elseif( $is_user_same_creator ) {
                    if($handler && $handler->email && filter_var($handler->email, FILTER_VALIDATE_EMAIL)) {
                        try {
                            if($this->tkt_config->cc_to_settings_mail === 1 && $alertnotify) {
                                Mail::to($handler->email)->cc($alertnotify)->queue(new UserComment($ticket, $handler, $tf->remarks, $tf));
                            }
                            else {
                                Mail::to($handler->email)->queue(new UserComment($ticket, $handler, $tf->remarks, $tf));
                            }
                        }
                        catch(\Exception $e) {
                            Log::error('Issue at 440 of newticktfrom email cron');
                            Log::error($e->getMessage());
                        }
                    }
                }
                elseif( $creator && $creator->email && filter_var($creator->email, FILTER_VALIDATE_EMAIL) ) {
                    /* else notify the ticket creator */
                    try {
                        if($this->tkt_config->cc_to_settings_mail === 1 && $alertnotify) {
                            Mail::to($creator->email)->cc($alertnotify)->queue(new UserComment($ticket, $creator, $tf->remarks, $tf));
                        }
                        else {
                            Mail::to($creator->email)->queue(new UserComment($ticket, $creator, $tf->remarks, $tf));
                        }
                    }
                    catch(\Exception $e) {
                        Log::error('Issue at 475 of newticktfrom email cron');
                        Log::error($e->getMessage());
                    }
                }
            }

            if( $creator->id == $this->user->id && $ticket->status_id == 7 ) {
                $ticket->status_id = Status::STATUS_IN_PROGRESS;

                $c = Config::first();
                $c->setWeekEnds();
                $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $ticket->created_at);
                $holidays = Holiday::getHolidaysFrom($created_at);
                $diffInMin = $ticket->tat_remaining_mins;
                $ticket->tat_expire = $c->calculateAdvancedTat($diffInMin, Carbon::now(config('app.timezone')), $holidays, "m");
                $ticket->tat_remaining_mins = $c->calculateRemainingTat(Carbon::createFromFormat('Y-m-d H:i:s', $ticket->tat_expire), $holidays);

                $ticket->save();

                $tf = new TktFollowing();
                $tf->ticket_id = $ticket->id;
                $tf->remarks = "Ticket Status updated by system as ticket creator commented on ticket";
                $tf->is_note = 0;
                $tf->updated_by = 0;
                $tf->updated_status = Status::STATUS_IN_PROGRESS;
                // if($config->checked_cc_checkbox == 1){
                    $tf->cc_emails = $cc_emails && count($cc_emails) ? implode(",", $cc_emails) : null;
                // }
                $tf->action_type = 2;
                $tf->save();

                /** Code is for update status in ticket history */
                $tkt_update['ticket_id'] = $ticket->id;
                $tkt_update['status'] = Status::STATUS_IN_PROGRESS;
                $tkt_update['action_type'] = 2;
                $tkt_update['updated_by'] = 0;
                CommonHelper::ticketStatusHistory($tkt_update);
                /** Code ends here */
            }

            if( $is_status_tag_found == false) {
                $tkt_update['ticket_id'] = $ticket->id;
                $tkt_update['action_type'] = 7;
                $tkt_update['updated_by'] = $this->user->id;
                CommonHelper::ticketStatusHistory($tkt_update);
            }

            // $msg->setFlag(['Seen']);
            $this->setMessageAsSeen($this->account_config->ebts_username, $msg->id);
            return true;
        }
        catch(\Exception $e) {
            Log::error("is_updatable_comment: " . $e->getMessage());
            return false;
        }
    }

    protected function inreplyto_updatable_comment($suspected_id, &$msg, $mail_subject, $mail_body) {
        try {
            $sender = $msg->sender->emailAddress->address;
            $mail_body = CommonHelper::extractMailContent($mail_body);

            try {
                $ticket = Ticket::findOrFail($suspected_id);
                if($ticket->is_temp || in_array($ticket->status_id, [6])) {
                    $this->_logException($msg, $sender, $mail_subject, "The target ticket #{$suspected_id} might be closed/invalid");
                    return false;
                }
            }
            catch(\Exception $e) {
                $this->_logException($msg, $sender,  $mail_subject, "The target ticket #{$suspected_id} might be closed/invalid");
                return false;
            }
            
            /* check for user by "From" email address. If not exists, then create & continue the following steps */
            if(! $this->load_user($sender)) {
                $this->_logException($msg, $sender,  $mail_subject, "Third-party email for the ticket #{$suspected_id} has been stopped. It might be the reason of blocked domain/accounts.");
                return false;
            }

            /* create new ticket comment */
            $ticket_data = array(
                "subject" => $mail_subject,
                "content" => $mail_body
            );

            $tf = new TktFollowing();
            $tf->ticket_id = $ticket->id;
            // $tf->remarks = trim($mail_body);
            $tf->is_note = 0;
            $tf->updated_by = $this->user->id;

            $tf->ac_email_id = $this->account_config->id;
            $tf->ac_email_uid = $msg->id;
            $tf->ac_email_message_id = $msg->internetMessageId;
            $tf->alias_acc_id = $this->alias_account ? $this->alias_account->id : null;

            $processResult = Attachment::processForB64Imgs($mail_body, $ticket->id, Auth::user()->id);
            $tf->remarks = $processResult['content'];

			/* get CC's */
            $config = Config::first();
            // if($config->checked_cc_checkbox == 1){
                $cc_emails = $this->make_cc_list($msg->toRecipients, $msg->ccRecipients);
                if( count($cc_emails) ) {
                    $tf->cc_emails = implode(",", $cc_emails);

                    /* check any new email address found, if yes, then add them to master cc emails */
                    $new_cc_master = $this->checkNewCcAddress($ticket->cc_emails, $cc_emails);
                    if( is_array($new_cc_master) && count($new_cc_master) ) {
                        $ticket->cc_emails = implode(",", $new_cc_master);
                    }
                }
            // }

            // if($is_reopen) {
            //     $tf->action_type = 2;
            //     $tf->updated_status = 2;
            //     $ticket->status_id = 2;
            // }
            // else {
                $tf->action_type = 7;
            // }

            if(! $tf->save()) {
                Log::error("Ticket Update via Email Error: Unable to save tf");
                return false;
            }

            if( count($processResult) && count($processResult['attachments']) ) {
                Attachment::whereIn('id', $processResult['attachments'])->update(['following_id' => $tf->id]);
            }

            /* attachment adding for ticket following */
            $msg_attachments = $this->getMailAttachment($this->account_config->ebts_username, $msg->id);
            if(!empty($msg_attachments)) {
                foreach ($msg_attachments as $messageAttachment) {
                    $this->add_attachment($msg, $messageAttachment, $ticket->id);
                }
            }

            $ticket->updated_at = date('Y-m-d H:i:s');
            $ticket->save();

            /* notify user / handler based on commenter */
            $alertnotify = null;
            if($this->tkt_config->alerts_enabled == 1){
                $alertnotify = $this->tkt_config->alert_email;
            }

            $is_user_same_creator = $ticket->creator_id == $this->user->id ? true : false;
            $creator = User::find($ticket->creator_id);
            $handler = User::find($ticket->assigned_to);
            $is_user_cc = $ticket->norCreatorOrHandler($this->user->id);
            
            if(config('mail.service_enabled')) {
                if( $is_user_cc ) {
                    /* if commenter is cc, then inform to both creator and handler */
                    try {
                        $temp_to = [];
                        if( $creator && $creator->email && filter_var($creator->email, FILTER_VALIDATE_EMAIL) ) {
                            $temp_to[] = $creator->email;
                        }
                        if( $handler && $handler->email && filter_var($handler->email, FILTER_VALIDATE_EMAIL) ) {
                            $temp_to[] = $handler->email;
                        }

                        if($this->tkt_config->cc_to_settings_mail === 1 && $alertnotify) {
                            Mail::to($temp_to)->cc($alertnotify)->queue(new UserComment($ticket, $creator, $tf->remarks, $tf));
                        }
                        else {
                            Mail::to($temp_to)->queue(new UserComment($ticket, $creator, $tf->remarks, $tf));
                        }
                    }
                    catch(\Exception $e) {
                        Log::error("Issue Commenter is cc: ");
                        Log::error($e->getMessage());
                    }

                }
                /* commenter is ticket creator then send notification to attendar */
                elseif( $is_user_same_creator ) {
                    if($handler && $handler->email && filter_var($handler->email, FILTER_VALIDATE_EMAIL)) {
                        try {
                            if($this->tkt_config->cc_to_settings_mail === 1 && $alertnotify) {
                                Mail::to($handler->email)->cc($alertnotify)->queue(new UserComment($ticket, $handler, $tf->remarks, $tf));
                            }
                            else {
                                Mail::to($handler->email)->queue(new UserComment($ticket, $handler, $tf->remarks, $tf));
                            }
                        }
                        catch(\Exception $e) {
                            Log::error('Issue at 440 of newticktfrom email cron');
                            Log::error($e->getMessage());
                        }
                    }
                }
                elseif( $creator && $creator->email && filter_var($creator->email, FILTER_VALIDATE_EMAIL) ) {
                    /* else notify the ticket creator */
                    try {
                        if($this->tkt_config->cc_to_settings_mail === 1 && $alertnotify) {
                            Mail::to($creator->email)->cc($alertnotify)->queue(new UserComment($ticket, $creator, $tf->remarks, $tf));
                        }
                        else {
                            Mail::to($creator->email)->queue(new UserComment($ticket, $creator, $tf->remarks, $tf));
                        }
                    }
                    catch(\Exception $e) {
                        Log::error('Issue at 475 of newticktfrom email cron');
                        Log::error($e->getMessage());
                    }
                }
            }
            
            // $msg->setFlag(['Seen']);
            $this->setMessageAsSeen($this->account_config->ebts_username, $msg->id);
            return true;
        }
        catch(\Exception $e) {
            Log::error("inreplyto_updatable_comment: " . $e->getMessage());
            return false;
        }
    }

    public function checkIsExistingConversation($conversationIds) {
        try {
            if (empty($conversationIds)) {
                return false;
            }
            Log::info("Checking existing conversation for IDs: " . implode(", ", $conversationIds));
            $normalizedIds = array_map(function ($id) {
                return strtolower(trim(preg_replace('/\s+/', '', $id)));
            }, $conversationIds);
            $ticket = Ticket::whereIn(
                DB::raw('LOWER(TRIM(ac_email_message_id))'),
                $normalizedIds
            )->first();
            Log::info("Existing conversation check result: " . ($ticket ? "Found (Ticket ID: {$ticket->id})" : "Not Found"));
            return $ticket ? $ticket->id : false;
        } catch (\Exception $e) {
            Log::error("checkIsExistingConversation : ".$e->getMessage());
            return false;
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->tkt_config = Config::first();
        $this->tkt_config->setWeekEnds();
        // $this->blocked_accounts = $this->tkt_config->ticketing_blocked_accounts;
        // $this->allowed_emails = Config::getAllowedEmails($this->tkt_config);

        $this->rules = array(
            'subject' => 'required|string|max:1000',
            'content' => 'required|string'
        );

        $this->tkt_status_arr = [];
        $this->tkt_status_arr_with_id = [];

        // $this->blocked_accounts = strtolower($this->blocked_accounts);

        if(! Config::isAutoCreateFromEmail()) {
            return;
        }

        /* get the accounts */
        $autoCreationAccounts = AutoCreationAccount::where('auto_create_from_email', AutoCreationAccount::ACFE_ENABLED)->orderBy('id', 'desc')->get();

        if(!$autoCreationAccounts || !count($autoCreationAccounts)) {
            return;
        }

        $this->alias_accounts = AliasEmailAccount::where('account_status', 1)->get();
        $this->alias_count = count($this->alias_accounts); 

        /* login as Super User */
        // $get_super = User::where('permission', 'like', '%superuser":1%')->whereNull('deleted_at')->where('activated', '=', 1)->limit(1)->get();
        $get_super = User::whereHas("roles", function($q){ $q->where("name", "SuperAdmin"); })->get();
        Auth::loginUsingId($get_super[0]->id);

        foreach($autoCreationAccounts as $account) {
            $this->account_config = $account;
            $this->credentials = $this->account_config->getEbtsConfig();

            if(! $this->credentials) {
                Log::error(self::LOGID . ": Invalid ebts config. ID: " . $this->account_config->id);
                continue;
            }

            $this->allowed_domains = $this->account_config->getAllowedDomains();
            if(! $this->allowed_domains) {
                continue;
            }

            $this->blocked_accounts = strtolower($this->account_config->ticketing_blocked_accounts);
            $this->allowed_emails = $this->account_config->getAllowedEmails();

            $current_datetime = Carbon::now(config('app.timezone'));
            $before_five_minute = Carbon::now('UTC')->subMinutes(120000);

            if(! $this->load_default_ticket_options()) {
                continue;
            }

            $this->reserve_default_ticket_options();

            $this->load_tkt_status();

            /* load holidays */
            $this->holidays = Holiday::getHolidaysFrom($before_five_minute->format('Y-m-d'));
            $this->info($before_five_minute->format('Y-m-d\TH:i:s\Z'));

            try {
                $userName = $this->account_config->ebts_username;
                $folders = $this->getFolders($userName);
                foreach($folders as $folder) {
                    try {
                        if( property_exists($folder, "displayName") ) {
                            if(strtoupper($folder->displayName) != "INBOX") {
                                continue;
                            }
                        }
                    }
                    catch(\Exception $e) {
                        Log::error($e->getMessage());
                    }

                    try {
                        if( property_exists($folder, "displayName") ) {
                            if(strtoupper($folder->displayName) != "INBOX") {
                                continue;
                            }
                        }
                    }
                    catch(\Exception $e) {
                        Log::error($e->getMessage());
                        continue;
                    }

                    $userName = $this->account_config->ebts_username;
                    $msgs = $this->getFolderMail($userName, $folder->id, $before_five_minute->format('Y-m-d\TH:i:s\Z'));
                    //$this->info(json_encode($msgs));
                    $this->info("connected");
                    foreach($msgs as $msg) {
                        $tos = $msg->toRecipients;
                        // dd(array_column(array_column($tos, 'emailAddress'), 'address'));
                        $mail_subject = mb_decode_mimeheader($msg->subject);
                        $mail_body = $msg->body->content;
                        $this->info("P " . json_encode(array_column($tos, 'mail')));
                        $headers = $msg->internetMessageHeaders; // Access the headers array
                        $references = null;
                        foreach ($headers as $header) {
                            if (isset($header->name) && $header->name === "References") {
                                $references = explode(" ", $header->value);
                                array_push($references, );
                                break; // Exit the loop once the References header is found
                            }
                        }
                        if(isset($references) && count($references) > 0){
                            $conversationCheck = $this->checkIsExistingConversation($references);
                            if( $conversationCheck ) {
                                $this->inreplyto_updatable_comment($conversationCheck, $msg, $mail_subject, $mail_body);
                                continue;
                            }
                        }


                        /* if already on exception log, skip it */
                        $isExistOnExceptionLog = $this->_isExistOnExceptionLog($msg->id);
                        if( $isExistOnExceptionLog ) {
                            continue;
                        }

                        $aliasEmail = $this->checkALiasMail($msg);
                        $this->info(json_encode($aliasEmail));
                        if(isset($aliasEmail) && !$this->account_config->isToEmailSame($aliasEmail)) {
                            $alias_account = $this->find_matched_alias($aliasEmail);
                            if($alias_account == true) {
                                if( $this->alias_count == 0 ) {
                                    continue;
                                }
                                $this->info("alias pass");
                                $alias_account = $this->find_matched_alias($aliasEmail);
                                $this->alias_account = $alias_account;
                                $load_ticket_options = $this->load_alias_ticket_options($alias_account);
                                if(! $load_ticket_options) {
                                    $this->restore_default_ticket_options();
                                }
                            }
                        }
                        else {
                            $this->info("no alias: ");
                            $this->alias_account = null;
                            $this->restore_default_ticket_options();
                        }

                        $this->info("dept: " . $this->def_department->id);
                        $this->info("cat: " . $this->def_prob_category->id);
                        if(trim($mail_body) == "") {
                            if(isset($msg->body->contentType) && strtolower($msg->body->contentType) == 'html') {
                                $mail_body = trim(strip_tags($msg->body->content));
                            } elseif(isset($msg->bodyPreview)) {
                                $mail_body = trim($msg->bodyPreview);
                            }
                        }
                        
                        $found_divider = stripos($mail_body, self::CONTENT_DIVIDER);
                        if( $found_divider !== false ) {
                            $new_mail_body = "";
                            if($found_divider <= 1) {
                                $new_mail_body = str_ireplace(self::CONTENT_DIVIDER, "", $mail_body);
                            }
                            else {
                                $new_mail_body = substr($mail_body, 0, $found_divider);
                            }

                            $new_mail_body = trim($new_mail_body);                        
                            if( $new_mail_body ) {
                                $mail_body = $new_mail_body;
                            }
                        }

                        $lower_subject = strtolower($mail_subject);
                        $lower_body = strtolower($mail_body);

                        /* Part 1 : Email to Ticket based on Suspected words disabled */
                        // $found = false; 
                        // foreach($this->suspect_words as $word) {
                        //     if(strpos($lower_subject, $word) !== false || strpos($lower_body, $word) !== false) {
                        //         $found = true;
                        //         break;
                        //     }   
                        // }

                        //mecm integration with email to ticket
                        if(in_array(config('app.client'), ['ltts', 'rolepermission', 'grdemo']) && (config('app.sub_client') == "live" || config('app.sub_client') == "dev")) {
                            if(strpos($mail_subject, "Please approve this request from Software Center") !== false){
                                preg_match_all('/#(.*?)#/', $mail_subject, $matches);
                                $parsedElements = array_filter($matches[1]);
                                
                                if($parsedElements && count($parsedElements) ==3) {
                                    $mail_body = "<b>Software Installation request</b> <br> <b>User Email :</b> ".$parsedElements[0]." <br> <b>Software :</b> ".$parsedElements[1]." <br> <b>Request :</b> ".$parsedElements[2];
                                    $requestForUser = User::where("email", $parsedElements[0])->first();
                                    if($requestForUser && !empty($requestForUser)) {
                                        $mecmCreatedFor = $requestForUser->id;
                                    }
                                    $department = Department::where('name', 'like', 'IT Service Request')->first();
                                    if(empty($department)) {
                                        Log::error("MECM Department not found");
                                        continue;        
                                    }
                                    if($department && isset($department->id)) {
                                        $this->def_department = $department;
                                    }
                                    $category = ProblemCategory::where('name', 'like', 'MECM Software Installation Request')->first();
                                    if(empty($category)) {
                                        Log::error("MECM Category not found");
                                        continue;        
                                    }
                                    if($category && isset($category->id)) {
                                        $this->def_prob_category = $category;
                                    }
                                    $this->def_sub_category = false;
                                }   
                            }
                        }

                        /* check it has ticket id */
                        $check_for_tid = [];
                        preg_match('/###(\d*)###/', $lower_subject, $check_for_tid);
                        if(count($check_for_tid) > 1) {
                            $suspected_id = (int) $check_for_tid[1];
                            $isClosedTicket = Ticket::findOrFail($suspected_id);
                            if($isClosedTicket->is_temp || !in_array($isClosedTicket->status_id, [6])) {
                                $this->is_updatable_comment($suspected_id, $msg, $mail_subject, $mail_body);
                                continue;
                            }
                        }

                        $this->info("Continue: ");
                        /* Part 2 : Email to Ticket based on Suspected words disabled */
                        // if(! $found) {
                        //     continue;
                        // }

                        $get_sender = $msg->sender;
                        $sender = "";
                        $tot_senders = $get_sender->emailAddress->address != null ? 1 : 0;
                        if($tot_senders > 0) {
                            $sender = $get_sender->emailAddress->address;

                            /* to avoid the block list emails */
                            $found_blocked_email = false;
                            if( strpos( $this->blocked_accounts, strtolower($get_sender->emailAddress->address) ) !== false ) {
                                $found_blocked_email = true;
                            }

                            if($found_blocked_email) {
                                continue;
                            }
                        }
                        $this->info("mail_subject: ". $mail_subject);
                        /* check whether is it already processed */
                        if($this->check_is_processed($msg->id, $msg->id)) {
                            continue;
                        }
                        $this->info("In mail_subject: ". $mail_subject);

                        /* check for user by "From" email address. If not exists, create account as external user */
                        if(! $this->load_user($sender)) {
                            if(!empty($this->user) && ($this->user->activated == 0 || $this->user->deleted_at != null)){
                                $this->_logException($msg, $msg->sender->emailAddress->address, $mail_subject, "Unable to convert the email to the ticket. It might be reason of the deleted/inactive user accounts.");
                            }else{
                                $this->_logException($msg, $msg->sender->emailAddress->address, $mail_subject, "Unable to convert the email of third party to the ticket. It might be reason of the blocked domain/email accounts.");
                            }
                            continue;
                        }
                        // $this->info("P ticket" );
                        /* create new ticket */
                        $ticket_data = array(
                            "subject" => $mail_subject,
                            "content" => $mail_body,
                            "company_id" => $this->account_config->company_id,
                        );
                        $combined_text = $mail_subject . ' - ' . CommonHelper::sanitizeHtmlContentData($mail_body);
                        if(config("app.client") == "safari") {
                            $ticketMLData = CommonHelper::createTicketFromMLIntent($combined_text, $this->user);                        
                            if(isset($ticketMLData["status"]) && $ticketMLData["status"] == "success") {
                                if(isset($ticketMLData["data"]["department_name"])) {    
                                    $this->def_department = Department::find($ticketMLData["data"]["department_name"]["id"]);
                                }
                                if(isset($ticketMLData["data"]["category_name"])) {
                                    $this->def_prob_category = ProblemCategory::find($ticketMLData["data"]["category_name"]["id"]);
                                }
                                if(isset($ticketMLData["data"]["subcategory_name"])) {
                                    $this->def_sub_category = ProblemCategory::find($ticketMLData["data"]["subcategory_name"]["id"]);
                                }
                                $createdViaML= 6;
                            }
                        }
                        
                        $validator = Validator::make($ticket_data, $this->rules, []);
        
                        if($validator->fails()) {
                            Log::error("Email Auto Creation Error:");
                            $v = $validator->errors()->toArray();
                            Log::error($v);
                            $this->_logException($msg, $msg->sender->emailAddress->address, $mail_subject, "Either subject/content is empty (or) might be contain invalid characters.");
                            continue;
                        }                        

                        $ticket_data["is_temp"] = null;
                        $ticket_data["created_via"] = isset($createdViaML) ? $createdViaML : 3;
                        $ticket_data["creator_id"] = isset($mecmCreatedFor) ? $mecmCreatedFor : $this->user->id;
                        $ticket_data["location_id"] = $this->user->location_id;
                        $ticket_data["created_by"] = Auth::user()->id;
                        $ticket_data["created_at"] = $current_datetime->format('Y-m-d H:i:s');
                        $ticket_data["department_id"] = $this->def_department->id;
                        $ticket_data["problem_category_id"] = $this->def_prob_category->id;
                        $ticket_data["priority_id"] = $this->def_prob_category->priority_id ? $this->def_prob_category->priority_id : 3;
                        
                        if($this->def_sub_category) {
                            $ticket_data["sub_category_id"] = $this->def_sub_category->id;
                            if( $this->def_sub_category->priority_id ) {
                                $ticket_data["priority_id"] = $this->def_sub_category->priority_id;
                            }
                        }

                        $ticket_data["ac_email_id"] = $this->account_config->id;
                        $ticket_data["ac_email_uid"] = $msg->id;
                        $ticket_data["ac_email_message_id"] = $msg->internetMessageId;
                        $ticket_data["alias_acc_id"] = $this->alias_account ? $this->alias_account->id : null;
                        
                        /* if CC there, then store it as comma separated */
                        $cc_emails = $this->make_cc_list($msg->toRecipients, $msg->ccRecipients);
                        if( count($cc_emails) ) {
                            $ticket_data["cc_emails"] = implode(",", $cc_emails);
                        }
                        
                        $this->ticket = new Ticket($ticket_data);
                        $this->ticket->tat = $ticket_data['tat'] = $this->def_prob_category->tat != "" ? $this->def_prob_category->tat : $this->ticket->priority->service_time;
                        $this->ticket->tat_expire = $this->tkt_config->calculateAdvancedTat($this->ticket->tat, $current_datetime, $this->holidays);
                        // Carbon::now(config('app.timezone'))->addHours($this->ticket->tat)->format('Y-m-d H:i:s');
                        $this->ticket->status_id = 1;      
                        $this->ticket->assignByHirarchy();
                        $this->ticket->save();
                        $creator_id = User::find($this->ticket->creator_id);
                        $tkt_detail = new TktDetail();
                        $tkt_detail->seat_no = isset($creator_id->seat_no) ? $creator_id->seat_no : null;
                        $tkt_detail->ticket_id =  $this->ticket->id;
                        $tkt_detail->save();
                        /* process for b64 images */
                        $processResult = Attachment::processForB64Imgs($mail_body, null, Auth::user()->id);
                        $this->ticket->content = $processResult['content'];
                        $systemApprovalOrNot = null;
                        if ( ($this->def_sub_category && $this->def_sub_category->approval_required == 1 && $this->def_sub_category->pab_id == 0) || ($this->def_prob_category->approval_required == 1 && $this->def_prob_category->pab_id == 0) ) {
                            throw new \Exception(trans('content.service_ticket_fields.select_PAB'));
                        } elseif ( ($this->def_sub_category && $this->def_sub_category->approval_required == 1 && $this->def_sub_category->pab_id != 0) || ($this->def_prob_category->approval_required == 1 && $this->def_prob_category->pab_id != 0) ) {
                            try {
                                $systemApprovalOrNot = 1;
                                if(isset($this->def_sub_category) && !empty($this->def_sub_category) && $this->def_sub_category->approval_required == 1 && $this->def_sub_category->pab_id != 0) {
                                    $problem_category = ProblemCategory::find($this->def_sub_category->id);
                                } else {
                                    $problem_category = ProblemCategory::find($this->def_prob_category->id);
                                }
                                $input['id'] = $this->ticket->id;
                                $ticketProcureRequestObj = new TicketProcureRequest();
                                $ticketProcureRequestData = $ticketProcureRequestObj->getDataFields($ticket_data);
                                $ticketProcureRequestData['id'] = $input['id'];
                                $ticketProcureRequestData['ticket_id'] = $input['id'];
                                $ticketProcureRequestData['sub_category_id'] = $this->def_sub_category ? $this->def_sub_category->id : null;
                                $pab_ids = explode(",", $problem_category->pab_id);
                                $pab = array();
                                foreach ($pab_ids as $key => $val) {
                                    $pab[$val] = 0;
                                }
                                $ticketProcureRequestData['pab_id'] = json_encode($pab);
                                $ticketProcureRequestObj->fill($ticketProcureRequestData);
                                // $ticketProcureRequestObj->assignByHirarchy();
                                $otherLocation = Location::where('name', 'like', 'Other')->first();
                                if(empty($otherLocation)) {
                                    $otherLocation = new Location();
                                    $otherLocation->name = $otherLocation->address = "Other";
                                    $otherLocation->country = "IN";
                                    $otherLocation->country_id = 101;
                                    $otherLocation->state_id = 4008;
                                    $otherLocation->city_id = 133024;
                                    $otherLocation->currency = "INR";
                                    $otherLocation->user_id = Auth::user()->id;
                                    $otherLocation->save();
                                }
                                if(isset($ticketProcureRequestObj->creator_id) && $ticketProcureRequestObj->creator_id != "") {
                                    $creatorObj = User::where('id', $ticketProcureRequestObj->creator_id)->first();
                                    if(!empty($creatorObj)) {
                                        $location_id = (isset($creatorObj->location_id) && $creatorObj->location_id == "" && Auth::user()->location_id == "") ? $otherLocation->id : Auth::user()->location_id;
                                        $ticketProcureRequestObj->location_id = ($creatorObj->location_id == "") ? $location_id : $creatorObj->location_id;
                                    }
                                } else {
                                    $ticketProcureRequestObj->location_id = (Auth::user()->location_id == "") ? $otherLocation->id : Auth::user()->location_id;
                                }
                                if ($ticketProcureRequestObj->save()) {
                                    $ticketProcureRequestObj = TicketProcureRequest::find($ticketProcureRequestObj->id);
                                    $ticketProcureRequestObj->update([
                                        "ticket_id" => $ticketProcureRequestObj->id,
                                    ]);

                                    $ticketProcureRequestObj->fillRequestTag();
                                    $acfe = AutoCreationAccount::where('auto_create_from_email', 1)->where('id', $ticketProcureRequestObj->department->tkt_auto_creation_id)->first();
                                    $ticketProcureRequestObj->ac_email_id = (!empty($acfe)) ? $acfe->id : 0;
                                    $pab_ids = explode(",", $problem_category->pab_id);
                                    $pab = TicketPab::find($pab_ids[0]);

                                    $history = new TicketRequestHistory();
                                    $history->user_id = $this->ticket->creator_id;
                                    $history->pr_id = $ticketProcureRequestObj->id;
                                    $history->change_info = trans('content.service_ticket_fields.new_ticket_approval', ['id' => $ticketProcureRequestObj->id]);
                                    $history->save();

                                    // 8 = System Approval
                                    if(!empty($pab) && $pab->hierarchy_approval == 8) {
                                        $systemApprovalOrNot = $pab->hierarchy_approval;
                                        $ticketProcureRequestObj = TicketProcureRequest::find($ticketProcureRequestObj->id);
                                        $ticketProcureRequestObj->update([
                                            "status_id" => 3,
                                            "approved_at" => Carbon::now()
                                        ]);

                                        if(!$ticketProcureRequestObj->save()) {
                                            $this->_logException($msg, $msg->sender->emailAddress->address, $mail_subject, "Unable to create new SR for System Approval");
                                            continue;
                                        }

                                        $return["status"] = "success";
                                        $return["msg"] =  trans('content.service_ticket_fields.new_ticket_system_approval', ['id' =>  $ticketProcureRequestObj->id]);

                                        $history = new TicketRequestHistory();
                                        $history->user_id = Auth::user()->id;
                                        $history->pr_id = $ticketProcureRequestObj->id;
                                        $history->change_info = $return["msg"];
                                        $history->save();

                                        // send email to create new request for user
                                        $user = User::find($ticketProcureRequestObj->creator_id);
                                        try {
                                            $cc_emails = [];
                                            if(!empty($user) && $user->email != "") {
                                                $manager =  User::find($user->manager_id);
                                                if(!empty($manager) && $manager->email != "") {
                                                    // $cc_emails[] = $manager->email;
                                                }
                                                if($this->tkt_config->checked_cc_checkbox == 1){
                                                    $cc_emails = array_merge($cc_emails,explode(",",$ticket_data["cc_emails"]));
                                                }
                                            }
                                            if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                                Log::info("CreationServiceRequest Mail: " . json_encode($user->email));
                                                Mail::to($user->email)->cc($cc_emails)->queue(new CreationServiceRequest($ticketProcureRequestObj, $user));
                                            }
                                        } catch (\Exception $ex) {
                                            Log::error("CreationServiceRequest Mail: " . $ex->getMessage());
                                        }
                                        // send push notification
                                        $notify_people = [];
                                        array_push($notify_people, $user->id);
                                        $notificationText = "New Service Request #".$ticketProcureRequestObj->procure_tag." has been created successfully!";
                                        $data = [
                                            'title' => $notificationText,
                                            'data' => $ticketProcureRequestObj,
                                            'notify' => $notify_people,
                                        ];
                                        $sendNotifications = CommonHelper::sendPushNotification($data);
                                        $sendNotifications = CommonHelper::sendWhatsappNotification($data);
                                        if($sendNotifications != false) {
                                            $response = json_decode($sendNotifications);
                                            if(isset($response->failure) && $response->failure == 1) {
                                                Log::error("create service request id push notification:" . $ticketProcureRequestObj->id. " notification error " .json_encode($response));
                                            }
                                        }
                                    } else {
                                        // 4 = Manager Approval
                                        $this->request = new Request($ticket_data);
                                        if (!empty($pab) && $pab->hierarchy_approval != 4) {
                                            if (!$pab || $pab->totMembers() < 1) {
                                                $return['msg'] = trans('content.service_ticket_fields.No_user_found_send_request');
                                                throw new \Exception("No user found send request on chosen PAB");
                                            }
                                            app(RequestController::class)->__sendApprovalRequest($this->request, $ticketProcureRequestObj);

                                            $pab_members = [];
                                            if ($pab->hierarchy_approval == 1) {
                                                $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->orderBy('hierarchy_level', 'asc')->get();
                                            } else if ($pab->hierarchy_approval == 5) {
                                                if ($ticketProcureRequestObj->location_id != null) {
                                                    $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->where('location_id', $ticketProcureRequestObj->location_id)->orderBy('id', 'asc')->get();
                                                    if(count($pab_members) == 0) {
                                                        throw new \Exception(trans('content.service_ticket_fields.No_user_found_send_request'));
                                                    }
                                                } else {
                                                    throw new \Exception(trans('content.service_ticket_fields.No_user_found_send_request'));
                                                }
                                            } else {
                                                $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->orderBy('id', 'asc')->get();
                                            }

                                            foreach ($pab_members as $k => $member) {
                                                $approvalRequest = new TicketApprovalRequest();
                                                $approvalRequest->pr_id = $ticketProcureRequestObj->id;
                                                $approvalRequest->pab_id = $pab_ids[0];
                                                $approvalRequest->user_id = $member->user_id;
                                                $user = User::find($approvalRequest->user_id);
                                                $pab_member_emails[] = $user->email;
                                            }
                                            $pro_not_mem = array_unique(array_merge([$user->email], $pab_member_emails));
                                            $creators = array_filter($pro_not_mem);
                                        } else {
                                            if (empty(Auth::user()->manager)) {
                                                $return["msg"] = trans('content.service_ticket_fields.Manager_is_not_found');
                                                return response()->json($return);
                                            }

                                            app(RequestController::class)->__sendApprovalRequest($this->request, $ticketProcureRequestObj);
                                            $creators = [];
                                        }
                                        $this->ticket->is_temp = 1;
                                        $alertnotify = null;
                                        if (Settings::first()->alerts_enabled == 1) {
                                            $alertnotify = CommonHelper::getGlobalAlertEmail();
                                        }
                                        // send email to create new request for user
                                        $user = User::find($ticketProcureRequestObj->creator_id);
                                        try {
                                            if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                                Log::info("Create new Service request sent mail: " . json_encode($user->email));
                                                Mail::to($user->email)->cc($alertnotify)->queue(new CreationServiceRequest($ticketProcureRequestObj, $user));
                                            }
                                        } catch (\Exception $ex) {
                                            Log::error("create request send mail to creator: " . $ex->getMessage());
                                        }
                                        // send push notification
                                        $notify_people = [];
                                        array_push($notify_people, $user->id);
                                        $notificationText = "New Service Request #".$ticketProcureRequestObj->procure_tag." has been created successfully!";
                                        $data = [
                                            'title' => $notificationText,
                                            'data' => $ticketProcureRequestObj,
                                            'notify' => $notify_people,
                                        ];
                                        $sendNotifications = CommonHelper::sendPushNotification($data);
                                        $sendNotifications = CommonHelper::sendWhatsappNotification($data);
                                        if($sendNotifications != false) {
                                            $response = json_decode($sendNotifications);
                                            if(isset($response->failure) && $response->failure == 1) {
                                                Log::error("create service request id push notification:" . $ticketProcureRequestObj->id. " notification error " .json_encode($response));
                                            }
                                        }

                                        // send email to all PAB Member for new request
                                        try {
                                            if (config('mail.service_enabled') && !empty($creators)) {
                                                Log::info("Ticket create email: " . json_encode($creators));
                                                Log::info("Ticket create email cc: " . json_encode($alertnotify));
                                                Mail::to($creators)->cc($alertnotify)->queue(new TicketApproverInfo($ticketProcureRequestObj, $pab));
                                            }
                                        } catch (\Exception $ex) {
                                            Log::error("TicketApproverInfo email to ticket: " . $ex->getMessage());
                                        }
                                        // DB::commit();
                                    }
                                }
                            } catch (\Exception $e) {
                                // DB::rollback();
                                Log::error("SR Error - ".self::LOGID . ": " . $this->credentials["username"] . " - " . $e->getMessage());
                            }
                        }
                        if($systemApprovalOrNot == null || $systemApprovalOrNot == 8) {
                            $this->ticket->assignByHirarchy();
                        }
                        $this->ticket->save();
                        app(RequestController::class)->createTasksFromCategory($this->ticket);
                        // $this->info("P ticket create" );
                        /* mark b64 imgs with ticket id */
                        if( count($processResult['attachments']) ) {
                            Attachment::whereIn('id', $processResult['attachments'])->update(['ticket_id' => $this->ticket->id]);
                        }

                        /* attachment adding */
                        $msg_attachments = $this->getMailAttachment($userName, $msg->id);
                        if(!empty($msg_attachments)) {
                            foreach($msg_attachments as $messageAttachment) {
                                $this->add_attachment($msg, $messageAttachment, $this->ticket->id);
                            }
                        }

                        /** Code is for add new ticket functionality in ticket history */
                        $tkt_update['ticket_id'] = $this->ticket->id;
                        $tkt_update['updated_by'] = $this->user->id;
                        $tkt_update['action_type'] = 14;
                        CommonHelper::ticketStatusHistory($tkt_update, $this->ticket);
                        /** Code ends here */

                        /** Code is for ticket auto assign to technician functionality in ticket history */
                        if ($this->ticket->assigned_to && ($systemApprovalOrNot == null|| $systemApprovalOrNot == 8 )) {
                            $tkt_update['ticket_id'] = $this->ticket->id;
                            $tkt_update['updated_by'] = 0;
                            $tkt_update['assigned_to'] = $this->ticket->assigned_to;
                            $tkt_update['action_type'] = 1;
                            $tkt_update['tat'] = $this->ticket->tat;
                            CommonHelper::ticketStatusHistory($tkt_update);
                        }
                        /** Code ends here */
                        // Ticket generate apply trigger.
                        if( !in_array(config('app.client'), ["ltts"]) && config("app.ai_enabled")) {
                            TktEventWithProcessTicketSentiment::dispatch($this->ticket);
                        } else {
                            $triggerObj = TicketTrigger::count();
                            if($triggerObj > 0) {
                                event(new TicketCreated($this->ticket));
                            }
                        }

                        $user = isset($requestForUser) && !empty($requestForUser) ? $requestForUser : $this->user;

                        $alertnotify = false;
                        if($this->tkt_config->alerts_enabled == 1) {
                            $alertnotify = $this->tkt_config->alert_email;
                        }

                        if(config('mail.service_enabled') && ($systemApprovalOrNot == null || $systemApprovalOrNot == 8) && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                            try {
                                
                                /* to send the login credential for newly created account */
                                if($this->newly_generated_user) {
                                    // Mail::to($user->email)->queue(new UserCredentialNotification($user, $user->getPassword()));
                                    $this->newly_generated_user = false;
                                }

                                if($alertnotify) {
                                    $cc_emails[] = $alertnotify;
                                }

                                if(isset($cc_emails) && count($cc_emails)) {
                                    // Mail::to($user->email)->cc($cc_emails)->queue(new IntimateSuccessCreation($this->ticket, $user));
                                    Mail::to($user->email)->queue(new IntimateSuccessCreation($this->ticket, $user));
                                }
                                else {
                                    Mail::to($user->email)->queue(new IntimateSuccessCreation($this->ticket, $user));
                                }
                            }
                            catch(\Exception $e) {
                                Log::error("NewTicketFromEmailMsVersion IntimateSuccessCreation: " . $e->getMessage());
                            }
                        }

                        if($this->ticket->assigned_to) {
                            $user = User::find($this->ticket->assigned_to);
                            if(config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                try {
                                    if($alertnotify) {
                                        Mail::to($user->email)->cc($alertnotify)->queue(new IntimateAssigned($this->ticket, $user));
                                    }
                                    else {
                                        Mail::to($user->email)->queue(new IntimateAssigned($this->ticket, $user));
                                    }
                                }
                                catch(\Exception $e) {
                                    Log::error("Unable to send mail on create ticket from email " . $e->getMessage());
                                }
                            }
                        }

                        //Auto reply setting
                        $autoUpdateConfig = TKTAutoUpdateSetting::first();
                        if(!empty($autoUpdateConfig) && isset($autoUpdateConfig->auto_response_for_ticket) && $autoUpdateConfig->auto_response_for_ticket == 1) {
                            $autoResponse = CommonHelper::searchAIResponse($combined_text);
                            if(isset($return["status"]) && $return["status"] == "fail") {
                                Log::error("New email to ticket ai response creation failed.");
                            } else {
                                $indexConObj = new IndexController();
                                $commentReqObjArray = [
                                    'id' => $this->ticket->id,
                                    'comment' => $autoResponse,
                                    'tmp_id' => rand(2,9),
                                    'user_id' => isset($createdViaML) ? 0 : $this->user->id
                                ];
                                $commentReqObj = new Request($commentReqObjArray);
                                $indexConObj->addComment($commentReqObj);
                            }
                        }
                        $this->setMessageAsSeen($this->account_config->ebts_username, $msg->id);
                        Log::channel('ticket')->info("NewTicketFromEmailMsVersion: TID: " . $this->ticket->id);
                    }
                }
            }
            catch(\Exception $e) {
                Log::error(self::LOGID . ": " . $this->credentials["username"] . " - " . $e->getMessage());
            }
        }
    }

    public function checkAliasMail($msg) {
        try {
            $headers = $msg->internetMessageHeaders;
            $aliasMail = [];
            foreach ($headers as $header) {
                if (isset($header->name) && in_array($header->name, ["To", "Cc"])) {
                    preg_match_all('/<(.*?)>/', $header->value, $matches);
                    if (!empty($matches[1])) {
                        $aliasMail = array_merge($aliasMail, $matches[1]);
                    }
                }
            }
            return $aliasMail;
        } catch (\Exception $e) {
            Log::error("checkAliasMail : " . $e->getMessage());
            return [];
        }
    }

    public function add_attachment(&$msg, &$file, $ticket_id=false, $following_id=null) {
        try {
            $filePath = date("Y").'/'.date('m').'/'.date('d');
            $checkFolderPath = CommonHelper::attachmentFolderStructure('storage_tkt', $filePath);
            if(!$checkFolderPath) {
                $return["msg"] = "Attachment Directory not found";
                Log::error("MS Attachment Directory not found");

            }

            $a = new Attachment();
            $a->ticket_id = $ticket_id;
            $a->original_file_name = $file->name;
            $a->original_file_name = $a->trimUnfittedName($a->original_file_name);
            $ext = pathinfo($file->name);
            $a->extension = isset($ext['extension']) ? $ext['extension'] : 'txt';
            $a->cid = $file->contentId;

            if( $a->isBlockedExtension($a->extension) ) {
                return;
            }

            $a->file_name = $filePath . DIRECTORY_SEPARATOR . $this->get_random_file_name($a->original_file_name, $a->extension);
            $a->uploader_id = $this->user->id;
            $a->tmp_id = null;
            $a->following_id = $following_id != null ? $following_id : null;
            if( $this->downloadAttachment($file, $a->file_name) ) {
                $a->save();
            }

            /* generate thumb if image */
            if( in_array($a->extension, ["png", "jpeg", "jpg"]) !== false ) {
                try {
                    $disk = Storage::disk('storage_tkt');
                    $path = $disk->path($a->file_name);
                    // $path = storage_path('tkt_attachments') . DIRECTORY_SEPARATOR . $a->file_name;
                    $thumb_name = $a->getThumbName();
                    if(! $thumb_name) {
                        throw new \Exception("Invalid Image Name " . $a->ticket_id);
                    }
                    $thumb_path = $disk->path($thumb_name);
                    // $thumb_path = storage_path('tkt_attachments') . DIRECTORY_SEPARATOR . $thumb_name;

                    Image::make($path)->resize(100, null, function($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->save($thumb_path);

                    $a->thumbnail = $thumb_name;
                    $a->save();
                } catch(\Exception $e) {
                    Log::error("thumbnail: " . $e->getMessage());
                }
            }
        }
        catch(\Exception $e) {
            Log::error("new ticket from email issue @ add attachment: ".json_encode($msg));
            $this->_logException($msg, $this->user->email, "", "Unable to get the Attached File. It might be any of following reason - Unsupported file format/invalid file size/corrupted file", $ticket_id, $following_id);
            Log::error($e->getMessage());
        }
    }

    protected function get_random_file_name($file_name, $extension) {
        try {
            $e = explode('.', $file_name);
            $r = array_reverse($e);
            $a = $r[0];
            $g = Str::random(28);
            $g = $g . (date('ymdhis'));
            $g = $g . '.' . $a;
            return $g;
        }
        catch(\Exception $e) {
            Log::error("Rand file name generate issu at ticket form email ");
            Log::error($e->getMessage());
            return (Str::random(28) . '.' . $extension);
        }
    }   

	protected function make_cc_list($to, $cc) {
		$cc_emails = [];
		
		/* cc collection start from To */
		$get_tos = $to;
        foreach($get_tos as $get_to){
            try {
                $cc_emails[] = trim(strtolower($get_to->emailAddress->address));
            }
            catch(\Exception $e) {
                Log::error($e->getMessage());
            }
        }
		
		/* cc collection from CC */
		$get_ccs = $cc; 
		$tot_ccs = is_array($get_ccs) ? count($get_ccs) : 0;
		foreach($get_ccs as $get_cc){
            try {
                $cc_emails[] = trim(strtolower($get_cc->emailAddress->address));
            }
            catch(\Exception $e) {
                Log::error($e->getMessage());
            }
        }
		
		/* sanitize CCs */
		$updated_cc_emails = array_unique($cc_emails);
		$keyOfAcEmail = array_search($this->account_config->ebts_username, $updated_cc_emails);
		if($keyOfAcEmail !== false) {
			unset($updated_cc_emails[$keyOfAcEmail]);
        }
        
        if($this->alias_account) {
            $keyOfAcEmail = array_search($this->alias_account->alias_email, $updated_cc_emails);
            if($keyOfAcEmail !== false) {
                unset($updated_cc_emails[$keyOfAcEmail]);
            }
        }

		return $updated_cc_emails;
	}
	
	protected function checkNewCcAddress($master_cc, $cc) {
		if( !$cc || !is_array($cc) || !count($cc) ) {
			return [];
		}
		
		$arr_master = $master_cc && strlen($master_cc) > 0 ? explode(",", $master_cc) : [];
		$get_diff = array_diff($cc, $arr_master);
		
		if( ! is_array($get_diff) || count($get_diff) < 1 ) {
			return [];
		}
		
		$keyOfAcEmail = array_search($this->account_config->ebts_username, $get_diff);
		if($keyOfAcEmail !== false) {
			unset($get_diff[$keyOfAcEmail]);
        }
        
        if($this->alias_account) {
            $keyOfAcEmail = array_search($this->alias_account->alias_email, $get_diff);
            if($keyOfAcEmail !== false) {
                unset($get_diff[$keyOfAcEmail]);
            }
        }
		
		if(! count($get_diff) ) {
			return [];
		}
		
		return array_unique(array_merge($arr_master, $get_diff), SORT_REGULAR);
    }

    protected function _logException($msg, $sender_mail, $subject, $reason, $ticket_id=null, $following_id=null) {
        try {
            $short_subject = "";
            if($subject != null && $subject != "" && is_string($subject)) {
                $short_subject = $subject;
                if(strlen($subject) > 80) {
                    $short_subject = substr($subject, 0, 80);
                }
            }

            $newExceptionLog = new ExceptionNotification();
            $newExceptionLog->ac_email_message_id = $msg->internetMessageId;
            $newExceptionLog->ac_email = $this->account_config->ebts_username;
            $newExceptionLog->mail_from = $sender_mail; // $sender->mail;
            $newExceptionLog->mail_datetime = Carbon::createFromFormat('Y-m-d\TH:i:s\Z', $msg->receivedDateTime);
            $newExceptionLog->subject = $short_subject;
            $newExceptionLog->reason = $reason;
            $newExceptionLog->ticket_id = $ticket_id;
            $newExceptionLog->following_id = $following_id;
            $newExceptionLog->auto_creation_account_id = $this->account_config->id;
            $newExceptionLog->save();

            $this->setMessageAsSeen($this->account_config->ebts_username, $msg->id);
        }
        catch(\Exception $e) {
            Log::error("_logException Error: " . $e->getMessage());
        }
    }

    protected function _isExistOnExceptionLog($ac_email_message_id) {
        try {
            $count = ExceptionNotification::where('ac_email_message_id', 'like', $ac_email_message_id)->count();
            return $count > 0 ? true : false;
        }
        catch(\Exception $e) {
            Log::error("_isExistOnExceptionLog: " . $e->getMessage());
            return false;
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Events\TicketCreated;
use App\Helpers\Common as CommonHelper;
use App\Jobs\TktEventWithProcessTicketSentiment;
use App\Models\Location;
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
use Auth;
use Illuminate\Support\Str;
use Log;
use DB;
use Mail;
use Spatie\Permission\Models\Role;
use Validator;
use Storage;
use Image;
use Carbon\Carbon;
use Webklex\PHPIMAP\ClientManager;

use App\Mail\Ticket\IntimateSuccessCreation;
use App\Mail\Ticket\IntimateAssigned;
use App\Mail\Ticket\Resolved;
use App\Mail\Ticket\StatusChanged;
use App\Mail\Ticket\UserComment;
use App\Mail\UserCredentialNotification;
use App\Http\Controllers\Ticket\RequestController;
use App\Models\TKTAutoUpdateSetting;

class NewTicketFromEmail extends Command
{
    public const LOGID = 'NewTicketFromEmail';

    protected $signature = 'ticket:new_ticket_from_email';

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

    public function __construct()
    {
        parent::__construct();
    }

    protected function getDateStr($msg): string
    {
        try {
            $date = $msg->getDate();
            // v3: Attribute object — first() ya toDate() se Carbon milta hai
            if (is_object($date) && method_exists($date, 'toDate')) {
                return $date->toDate()->format('Y-m-d H:i:s');
            }
            if (is_object($date) && method_exists($date, 'first')) {
                $first = $date->first();
                if ($first instanceof \Carbon\Carbon) {
                    return $first->format('Y-m-d H:i:s');
                }
                return Carbon::parse((string)$first)->format('Y-m-d H:i:s');
            }
            // fallback
            return Carbon::parse((string)$date)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            Log::error("getDateStr: " . $e->getMessage());
            return Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
        }
    }


    // -------------------------------------------------------------------------
    // HELPER: PHPIMAP v3 — extract string value from an Attribute or plain value
    // -------------------------------------------------------------------------
    protected function attrVal($value): string
    {
        if (is_null($value)) {
            return '';
        }
        // Webklex\PHPIMAP\Attribute object
        if (is_object($value) && method_exists($value, 'toString')) {
            return (string) $value->toString();
        }
        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }
        if (is_array($value)) {
            return implode(' ', $value);
        }
        return (string) $value;
    }

    // -------------------------------------------------------------------------
    // HELPER: safe getTo / getCc — always returns plain array of objects with ->mail
    // -------------------------------------------------------------------------
    protected function getAddressList($addressCollection): array
    {
        if (is_null($addressCollection)) {
            return [];
        }
        // PHPIMAP v3 returns an AddressCollection (iterable)
        if (is_object($addressCollection) && method_exists($addressCollection, 'toArray')) {
            return $addressCollection->toArray();
        }
        if (is_array($addressCollection)) {
            return $addressCollection;
        }
        return [];
    }

    // -------------------------------------------------------------------------
    // HELPER: safe getReferences — always returns string
    // -------------------------------------------------------------------------
    protected function getReferencesStr($msg): string
    {
        try {
            $ref = $msg->getReferences();
            if (is_null($ref)) return '';
            return $this->attrVal($ref);
        } catch (\Exception $e) {
            return '';
        }
    }

    // -------------------------------------------------------------------------
    // HELPER: safe getHeader raw string
    // -------------------------------------------------------------------------
    protected function getHeaderStr($msg): string
    {
        try {
            $h = $msg->getHeader();
            if (is_null($h)) return '';
            // v3: Header object — use raw property
            if (is_object($h) && isset($h->raw)) {
                return (string) $h->raw;
            }
            if (is_object($h) && method_exists($h, '__toString')) {
                return (string) $h;
            }
            return (string) $h;
        } catch (\Exception $e) {
            return '';
        }
    }

    // -------------------------------------------------------------------------
    // HELPER: safe getMessageId — always string
    // -------------------------------------------------------------------------
    protected function getMessageId($msg): string
    {
        try {
            return $this->attrVal($msg->getMessageId());
        } catch (\Exception $e) {
            return '';
        }
    }

    // -------------------------------------------------------------------------
    // HELPER: safe getUid
    // -------------------------------------------------------------------------
    protected function getUid($msg)
    {
        try {
            $uid = $msg->getUid();
            return is_object($uid) ? $this->attrVal($uid) : $uid;
        } catch (\Exception $e) {
            return null;
        }
    }

    // -------------------------------------------------------------------------
    // HELPER: safe subject decode
    // -------------------------------------------------------------------------
    protected function getSubject($msg): string
    {
        try {
            $s = $msg->getSubject();
            $str = $this->attrVal($s);
            return mb_decode_mimeheader($str);
        } catch (\Exception $e) {
            return '';
        }
    }

    // -------------------------------------------------------------------------
    // HELPER: safe getSender — returns array of address objects with ->mail, ->full
    // -------------------------------------------------------------------------
    protected function getSenderList($msg): array
    {
        try {
            $senders = $msg->getSender();
            return $this->getAddressList($senders);
        } catch (\Exception $e) {
            return [];
        }
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
            Log::error($e->getMessage());
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
            if($this->auto_create_email) {
                return true;
            }
            return false;
        }
        catch(\Exception $e) {
            $this->auto_create_email = null;
            Log::error($e->getMessage());
            return false;
        }
    }

    protected function check_is_processed($uid, $msg_id) {
        if($msg_id) {
            try {
                $get_count = Ticket::where("ac_email_message_id", "like", $msg_id)->where("ac_email_id", "=", $this->account_config->id)->count();
                if($get_count) {
                    return true;
                }
            }
            catch(\Exception $e) {
                Log::error($e->getMessage());
            }
        }
        return false;
    }

    protected function check_is_processed_comment($uid, $msg_id) {
        if($msg_id) {
            try {
                $get_count = TktFollowing::where("ac_email_message_id", "like", $msg_id)->where("ac_email_id", "=", $this->account_config->id)->count();
                if($get_count) {
                    return true;
                }
            }
            catch(\Exception $e) {
                Log::error($e->getMessage());
            }
        }
        return false;
    }

    protected function load_only_existing_user($user_info) {
        try {
            $allow = false;
            if( is_array($this->allowed_emails) ) {
                if( in_array($user_info->mail, $this->allowed_emails) ) {
                    $allow = true;
                }
            }

            if( $allow == false && is_array($this->allowed_domains) ) {
                foreach( $this->allowed_domains as $ad ) {
                    if( stripos($user_info->mail, ('@' . $ad)) > 0 ) {
                        $allow = true;
                        break;
                    }
                }
                if(! $allow) {
                    return false;
                }
            }

            $get_user = User::withTrashed()->where("email", "like", $user_info->mail)->first();
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
            if(empty($user_info)) {
                return false;
            }

            $this->newly_generated_user = false;

            $allow = false;
            if( is_array($this->allowed_emails) ) {
                if( in_array($user_info->mail, $this->allowed_emails) ) {
                    $allow = true;
                }
            }

            if( $allow == false && is_array($this->allowed_domains) ) {
                foreach( $this->allowed_domains as $ad ) {
                    if( stripos($user_info->mail, ('@' . $ad)) > 0 ) {
                        $allow = true;
                        break;
                    }
                }
                if(! $allow) {
                    return false;
                }
            }

            $get_user = User::withTrashed()->where("email", "like", $user_info->mail)->first();
            if($get_user) {
                $this->user = $get_user;
                return true;
            }

            $name_prepare = preg_replace("/[^A-Za-z0-9 ]/", '', strip_tags($user_info->full));

            $new_account = [];
            $new_account["first_name"] = $name_prepare;
            $new_account["last_name"]  = $name_prepare;
            $new_account["username"]   = $user_info->mail;
            $new_account["password"]   = 'temp-pw';
            $new_account["company_id"] = Auth::user()->company_id;
            $new_account["email"]      = $user_info->mail;
            $new_account["job_type"]   = 2;
            $new_account["activated"]  = 1;
            $new_account["created_by"] = Auth::user()->id;
            $new_account["create_mode"]= 3;

            $otherLocation = Location::where('name', 'like', 'Other')->first();
            if(empty($otherLocation)) {
                $otherLocation = new Location();
                $otherLocation->name = $otherLocation->address = $otherLocation->city = $otherLocation->state = "Other";
                $otherLocation->country    = "IN";
                $otherLocation->country_id = 101;
                $otherLocation->state_id   = 4008;
                $otherLocation->city_id    = 133024;
                $otherLocation->currency   = "INR";
                $otherLocation->user_id    = Auth::user()->id;
                $otherLocation->save();
            }
            $new_account["location_id"] = (Auth::user()->location_id == "") ? $otherLocation->id : Auth::user()->location_id;

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
                $mail = strtolower($mail);
                foreach ($this->alias_accounts as $alias_account) {
                    if (strtolower($alias_account->alias_email) == $mail) {
                        return $alias_account;
                    }
                }
            }
        } else {
            $email = strtolower($email);
            foreach($this->alias_accounts as $alias_account) {
                if(strtolower($alias_account->alias_email) == $email) {
                    return $alias_account;
                }
            }
        }
        return false;
    }

    protected function reserve_default_ticket_options() {
        $this->reserved_ticket_options = [];
        $this->reserved_ticket_options['def_department']    = $this->def_department;
        $this->reserved_ticket_options['def_prob_category'] = $this->def_prob_category;
        $this->reserved_ticket_options['def_sub_category']  = $this->def_sub_category;
    }

    protected function restore_default_ticket_options() {
        $this->def_department    = $this->reserved_ticket_options['def_department'];
        $this->def_prob_category = $this->reserved_ticket_options['def_prob_category'];
        $this->def_sub_category  = $this->reserved_ticket_options['def_sub_category'];
    }

    protected function load_alias_ticket_options(AliasEmailAccount $alias) {
        try {
            if( $alias->default_department_id && $alias->default_prob_cat_id ) {
                $this->def_department    = Department::findOrFail($alias->default_department_id);
                $this->def_prob_category = ProblemCategory::findOrFail($alias->default_prob_cat_id);

                if($this->def_prob_category->department_id != $this->def_department->id || $this->def_department->module_ticket_enabled != 1) {
                    throw new \Exception("Uncorrected department configured for alias ticket");
                }

                $this->def_sub_category = false;
                if( $this->def_prob_category->totSubCategories() > 0 ) {
                    $this->def_sub_category = ProblemCategory::where('parent_id', $this->def_prob_category->id)->first();
                }

                return $this->def_department && $this->def_prob_category;
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
            if( $this->account_config->default_department_id && $this->account_config->default_prob_cat_id ) {
                $this->def_department    = Department::findOrFail($this->account_config->default_department_id);
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

            $get_departments = Department::where("company_id", "=", Auth::user()->company_id)->where("module_ticket_enabled", "=", 1)->select('id')->get();
            $department_ids = $get_departments->pluck('id');
            $this->def_prob_category = ProblemCategory::whereIn('department_id', $department_ids)->first();
            $this->def_department    = Department::find($this->def_prob_category->department_id);

            $this->def_sub_category = false;
            if( $this->def_prob_category->totSubCategories() > 0 ) {
                $this->def_sub_category = ProblemCategory::where('parent_id', $this->def_prob_category->id)->first();
            }

            return $this->def_department && $this->def_prob_category;
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    protected function updateTicketWithStatus(&$st, &$param_data, &$msg) {
        $ticket_id = $st->id;
        $data['status_id'] = $param_data['status_id'];
        $is_status_changing_now = $st->status_id != $data['status_id'];

        $is_tat_changed = false;

        $is_resolved_now = false;
        if($data["status_id"] != $st->status_id && $st->status_id != 6 && $data["status_id"] == 5) {
            $is_resolved_now = true;
            $st->resolved_at = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');

            if( $this->tkt_config->isReqDeptChgBfrResolve == 1 && $this->tkt_config->default_department_id == $st->department_id ) {
                $data["status_id"]      = $st->status_id;
                $is_status_changing_now = false;
                $is_resolved_now        = false;
                $st->resolved_at        = null;
            }
        }

        $target_status = Status::find($data["status_id"]);
        if($data["status_id"] != $st->status_id) {
            if($target_status->tat_halt && !$st->status->tat_halt) {
                $expire_at_carbon = Carbon::createFromFormat('Y-m-d H:i:s', $st->tat_expire);
                $data["tat_remaining_mins"] = $this->tkt_config->calculateRemainingTat($expire_at_carbon, $this->holidays);
            }
            elseif(!$target_status->tat_halt && $st->status->tat_halt) {
                if($st->tat_remaining_mins && !$is_tat_changed) {
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

        $tf = new TktFollowing();
        $tf->ticket_id = $st->id;
        $tf->is_note   = 0;

        $processResult       = Attachment::processForB64Imgs($param_data['comment'], $st->id, $this->user->id);
        $param_data['comment'] = $processResult['content'];
        $tf->remarks         = trim($param_data['comment']);
        Log::info("remarks ".json_encode($tf->remarks));
        $tf->ac_email_id       = $this->account_config->id;
        $tf->ac_email_uid      = $this->getUid($msg);
        $tf->ac_email_message_id = $this->getMessageId($msg);
        $tf->alias_acc_id      = $this->alias_account ? $this->alias_account->id : null;

        /* get CC's */
        $cc_emails = $this->make_cc_list($this->getAddressList($msg->getTo()), $this->getAddressList($msg->getCc()));
        if( count($cc_emails) ) {
            $tf->cc_emails = implode(",", $cc_emails);
            $new_cc_master = $this->checkNewCcAddress($st->cc_emails, $cc_emails);
            if( is_array($new_cc_master) && count($new_cc_master) ) {
                $st->cc_emails = implode(",", $new_cc_master);
            }
        }

        $tf->updated_by    = $this->user->id;
        $tf->updated_status = $is_status_changing_now ? $data['status_id'] : null;
        $tf->action_type   = $is_status_changing_now ? 2 : 7;
        $tf->save();

        $tkt_update['ticket_id']  = $st->id;
        $tkt_update['status']     = $data['status_id'];
        $tkt_update['action_type']= 2;
        $tkt_update['updated_by'] = $this->user->id;
        CommonHelper::ticketStatusHistory($tkt_update);

        if( count($processResult) && count($processResult['attachments']) ) {
            Attachment::whereIn('id', $processResult['attachments'])->update(['following_id' => $tf->id]);
        }

        /* attachment adding for ticket following */
        $msg_attachments = $msg->getAttachments();
        try {
            $msg_attachments->each(function($file) use($st, $tf, $msg) {
                $this->add_attachment($msg, $file, $st->id, $tf->id);
            });
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
            $this->_logException($msg, $this->user->mail, $st->subject, "Unable to fetch the attachments.", $st->id, $tf->id);
        }

        $st->updated_at = date('Y-m-d H:i:s');
        $st->save();

        if($is_resolved_now) {
            $creator    = $st->creator;
            $dep        = Department::find($st->department_id);

            if(config('mail.service_enabled') && $creator && $creator->email && filter_var($creator->email, FILTER_VALIDATE_EMAIL)) {
                try {
                    if($this->tkt_config->cc_to_settings_mail === 1 && $alertnotify) {
                        Mail::to($creator->email)->cc($alertnotify)->queue(new Resolved($st, $creator, trim($param_data['comment']), $tf));
                    } else {
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
            $dep     = Department::find($st->department_id);

            if(config('mail.service_enabled') && $creator && $creator->email && filter_var($creator->email, FILTER_VALIDATE_EMAIL)) {
                try {
                    if($this->tkt_config->cc_to_settings_mail === 1 && $alertnotify) {
                        Mail::to($creator->email)->cc($alertnotify)->queue(new StatusChanged($st, $creator, trim($param_data['comment']), $tf));
                    } else {
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
            $senders = $this->getSenderList($msg);
            $sender  = count($senders) ? $senders[0] : "";

            try {
                $ticket = Ticket::findOrFail($suspected_id);
                if($ticket->is_temp || in_array($ticket->status_id, [6])) {
                    $this->_logException($msg, $sender->mail ?? '', $mail_subject, "The target ticket #{$suspected_id} might be closed/invalid");
                    return false;
                }
            }
            catch(\Exception $e) {
                $this->_logException($msg, $sender->mail ?? '', $mail_subject, "The target ticket #{$suspected_id} might be closed/invalid");
                return false;
            }

            if($this->check_is_processed($this->getUid($msg), $this->getMessageId($msg))) {
                return false;
            }

            if($this->check_is_processed_comment($this->getUid($msg), $this->getMessageId($msg))) {
                return false;
            }

            if(! $this->load_user($sender)) {
                $this->_logException($msg, $sender->mail ?? '', $mail_subject, "Third-party email for the ticket #{$suspected_id} has been stopped. It might be the reason of blocked domain/accounts.");
                return false;
            }

            $ticket_data = array(
                "subject" => $mail_subject,
                "content" => $mail_body
            );

            $validator = Validator::make($ticket_data, $this->rules, []);
            if($validator->fails()) {
                Log::error("Ticket Update via Email Error:");
                Log::error($validator->errors()->toArray());
                $this->_logException($msg, $sender->mail ?? '', $mail_subject, "Either content is empty (or) might be contain invalid characters.", $ticket->id);
                return false;
            }

            $lower_subject      = strtolower($mail_subject);
            $is_status_tag_found = stripos($lower_subject, '###status:');
            if( $is_status_tag_found !== false && $this->user->id != $ticket->creator_id && $this->user->id == $ticket->assigned_to ) {
                $status_word = trim(substr($lower_subject, $is_status_tag_found + 10));
                $end_hash_part = strpos($status_word, '#');
                if( $end_hash_part !== false ) {
                    $status_word = substr($status_word, 0, $end_hash_part);
                }

                Log::info("word ".$status_word);
                Log::info($status_word." -- ".json_encode($this->tkt_status_arr));

                if( in_array($status_word, $this->tkt_status_arr) ) {
                    $status_id  = $this->getStatusIdByName($status_word);
                    $param_data = [];
                    $param_data['status_id'] = $status_id;
                    $param_data['comment']   = trim($mail_body);
                    $so_result = $this->updateTicketWithStatus($ticket, $param_data, $msg);
                    if( $so_result ) {
                        $msg->setFlag(['Seen']);
                    }
                    return $so_result;
                }
            }

            $is_reopen = false;
            if( $ticket->creator_id == $this->user->id && $ticket->status_id == 5 ) {
                $is_reopen = true;
            }

            $tf = new TktFollowing();
            $tf->ticket_id  = $ticket->id;
            $tf->is_note    = 0;
            $tf->updated_by = $this->user->id;

            $tf->ac_email_id       = $this->account_config->id;
            $tf->ac_email_uid      = $this->getUid($msg);
            $tf->ac_email_message_id = $this->getMessageId($msg);
            $tf->alias_acc_id      = $this->alias_account ? $this->alias_account->id : null;

            $processResult = Attachment::processForB64Imgs($mail_body, $ticket->id, Auth::user()->id);
            $tf->remarks   = $processResult['content'];

            $cc_emails = $this->make_cc_list($this->getAddressList($msg->getTo()), $this->getAddressList($msg->getCc()));
            if( count($cc_emails) ) {
                $tf->cc_emails = implode(",", $cc_emails);
                $new_cc_master = $this->checkNewCcAddress($ticket->cc_emails, $cc_emails);
                if( is_array($new_cc_master) && count($new_cc_master) ) {
                    $ticket->cc_emails = implode(",", $new_cc_master);
                }
            }

            if($is_reopen) {
                $tf->action_type    = 2;
                $tf->updated_status = 2;
                $ticket->status_id  = 2;
            } else {
                $tf->action_type = 7;
            }

            if(! $tf->save()) {
                Log::error("Ticket Update via Email Error: Unable to save tf");
                return false;
            }

            if( count($processResult) && count($processResult['attachments']) ) {
                Attachment::whereIn('id', $processResult['attachments'])->update(['following_id' => $tf->id]);
            }

            $msg_attachments = $msg->getAttachments();
            try {
                $msg_attachments->each(function($file) use($ticket, $tf, $msg) {
                    $this->add_attachment($msg, $file, $ticket->id, $tf->id);
                });
            }
            catch(\Exception $e) {
                Log::error($e->getMessage());
                $this->_logException($msg, $sender->mail ?? '', $mail_subject, "Unable to fetch the attachments.", $ticket->id, $tf->id);
            }

            $ticket->updated_at = date('Y-m-d H:i:s');
            $ticket->save();

            $alertnotify = null;
            if($this->tkt_config->alerts_enabled == 1){
                $alertnotify = $this->tkt_config->alert_email;
            }

            $is_user_same_creator = $ticket->creator_id == $this->user->id ? true : false;
            $creator    = User::find($ticket->creator_id);
            $handler    = User::find($ticket->assigned_to);
            $is_user_cc = $ticket->norCreatorOrHandler($this->user->id);

            if(config('mail.service_enabled')) {
                if( $is_user_cc ) {
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
                        } else {
                            Mail::to($temp_to)->queue(new UserComment($ticket, $creator, $tf->remarks, $tf));
                        }
                    }
                    catch(\Exception $e) {
                        Log::error("Issue Commenter is cc: ");
                        Log::error($e->getMessage());
                    }
                }
                elseif( $is_user_same_creator ) {
                    if($handler && $handler->email && filter_var($handler->email, FILTER_VALIDATE_EMAIL)) {
                        try {
                            if($this->tkt_config->cc_to_settings_mail === 1 && $alertnotify) {
                                Mail::to($handler->email)->cc($alertnotify)->queue(new UserComment($ticket, $handler, $tf->remarks, $tf));
                            } else {
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
                    try {
                        if($this->tkt_config->cc_to_settings_mail === 1 && $alertnotify) {
                            Mail::to($creator->email)->cc($alertnotify)->queue(new UserComment($ticket, $creator, $tf->remarks, $tf));
                        } else {
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
                $holidays   = Holiday::getHolidaysFrom($created_at);
                $diffInMin  = $ticket->tat_remaining_mins;
                $ticket->tat_expire         = $c->calculateAdvancedTat($diffInMin, Carbon::now(config('app.timezone')), $holidays, "m");
                $ticket->tat_remaining_mins = $c->calculateRemainingTat(Carbon::createFromFormat('Y-m-d H:i:s', $ticket->tat_expire), $holidays);
                $ticket->save();

                $tf2 = new TktFollowing();
                $tf2->ticket_id    = $ticket->id;
                $tf2->remarks      = "Ticket Status updated by system as ticket creator commented on ticket";
                $tf2->is_note      = 0;
                $tf2->updated_by   = 0;
                $tf2->updated_status = Status::STATUS_IN_PROGRESS;
                $tf2->cc_emails    = $cc_emails && count($cc_emails) ? implode(",", $cc_emails) : null;
                $tf2->action_type  = 2;
                $tf2->save();

                $tkt_update['ticket_id']  = $ticket->id;
                $tkt_update['status']     = Status::STATUS_IN_PROGRESS;
                $tkt_update['action_type']= 2;
                $tkt_update['updated_by'] = 0;
                CommonHelper::ticketStatusHistory($tkt_update);
            }

            if( $is_status_tag_found == false) {
                $tkt_update['ticket_id']  = $ticket->id;
                $tkt_update['action_type']= 7;
                $tkt_update['updated_by'] = $this->user->id;
                CommonHelper::ticketStatusHistory($tkt_update);
            }

            if(config("app.client") == "safari"){
                $autoUpdateConfig = TKTAutoUpdateSetting::first();
                if(!empty($autoUpdateConfig) && isset($autoUpdateConfig->auto_resolve_ticket) && $autoUpdateConfig->auto_resolve_ticket == 1) {
                    Log::info("Auto resolve check initiated ".json_encode($autoUpdateConfig). " - Handler : " .$ticket->assigned_to. " - Commentore : ". $this->user->id);
                    $statusPrediction = CommonHelper::getAIStatusPrediction($mail_body, $suspected_id, $this->user->id);
                    Log::info("AI status prediction from email : ".json_encode($statusPrediction));
                }
            }

            $msg->setFlag(['Seen']);
            return true;
        }
        catch(\Exception $e) {
            Log::error("is_updatable_comment: " . $e->getMessage());
            return false;
        }
    }

    public function checkIsExistingConversation($conversationIds) {
        try {
            if(empty($conversationIds)) {
                return false;
            }
            $normalizedIds = array_map(function ($id) {
                return strtolower(trim(preg_replace('/\s+/', '', str_replace(['<', '>'], '', $id))));
            }, $conversationIds);
            $ticket = Ticket::whereIn(
                DB::raw('LOWER(TRIM(ac_email_message_id))'),
                $normalizedIds
            )->orderby('id', 'desc')->first();
            return (!empty($ticket)) ? $ticket->id : false;
        } catch (\Exception $e) {
            Log::error("checkIsExistingConversation : ".$e->getMessage());
            return false;
        }
    }

    public function cleanText($text) {
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);
        return $text;
    }

    public function checkDuplicateTicketCreation($mail_subject, $mail_body) {
        try {
            $checkTime = Carbon::now(config('app.timezone'))->subDays(15);

            $duplicateTickets = DB::table('tkt_tickets')
                ->whereNotIn('status_id', [5,6])
                ->whereNull('deleted_at')
                ->whereNull('is_temp')
                ->where('created_at', '>=', $checkTime)
                ->whereRaw("MATCH(subject) AGAINST (? IN NATURAL LANGUAGE MODE)", [$mail_subject])
                ->selectRaw("id, content, MATCH(subject) AGAINST (?) AS score", [$mail_subject])
                ->orderByDesc('score')
                ->limit(5)
                ->get();

            $duplicateIds = [];
            foreach ($duplicateTickets as $candidate) {
                similar_text(
                    $this->cleanText($mail_body),
                    $this->cleanText($candidate->content),
                    $percent
                );
                if ($percent >= 80) {
                    $duplicateIds[] = $candidate->id;
                }
            }

            if (!empty($duplicateIds)) {
                Log::info("Duplicate ticket(s) found with IDs: " . implode(", ", $duplicateIds));
                rsort($duplicateIds);
                return $duplicateIds[0];
            }
            return false;
        } catch (\Exception $e) {
            Log::error("checkDuplicateTicketCreation : ".$e->getMessage());
            return false;
        }
    }

    public function handle()
    {
        $this->tkt_config = Config::first();
        $this->tkt_config->setWeekEnds();

        $this->rules = array(
            'subject' => 'required|string|max:1000',
            'content' => 'required|string'
        );

        $this->tkt_status_arr        = [];
        $this->tkt_status_arr_with_id = [];

        if(! Config::isAutoCreateFromEmail()) {
            return;
        }

        $autoCreationAccounts = AutoCreationAccount::where('auto_create_from_email', AutoCreationAccount::ACFE_ENABLED)->orderBy('id', 'desc')->get();

        $this->alias_accounts = AliasEmailAccount::where('account_status', 1)->get();
        $this->alias_count    = count($this->alias_accounts);

        $get_super = User::whereHas("roles", function($q){ $q->where("name", "SuperAdmin"); })->limit(1)->get();
        Auth::loginUsingId($get_super[0]->id);

        foreach($autoCreationAccounts as $account) {
            $this->account_config = $account;
            $this->credentials    = $this->account_config->getEbtsConfig();

            if(! $this->credentials) {
                Log::error(self::LOGID . ": Invalid ebts config. ID: " . $this->account_config->id);
                continue;
            }

            $this->allowed_domains = $this->account_config->getAllowedDomains();
            if(! $this->allowed_domains) {
                continue;
            }

            $this->blocked_accounts = strtolower($this->account_config->ticketing_blocked_accounts);
            $this->allowed_emails   = $this->account_config->getAllowedEmails();

            $current_datetime    = Carbon::now(config('app.timezone'));
            $before_five_minute  = Carbon::now(config('app.timezone'))->subMinutes(120);

            if(! $this->load_default_ticket_options()) {
                continue;
            }

            $this->reserve_default_ticket_options();
            $this->load_tkt_status();

            $this->holidays = Holiday::getHolidaysFrom($before_five_minute->format('Y-m-d'));
            $this->info($before_five_minute->format('Y-m-d H:i:s'));

            try {
                $cm   = new ClientManager();
                $conn = $cm->make($this->credentials);
                $conn->connect();

                if(! $conn->isConnected()) {
                    throw new \Exception("No connection established");
                }

                $folders = $conn->getFolders(false);

                foreach($folders as $folder) {
                    // ---------------------------------------------------------
                    // Only process INBOX — v3 compatible folder name check
                    // ---------------------------------------------------------
                    try {
                        $folderName = '';
                        if (isset($folder->full_name)) {
                            $folderName = $folder->full_name;
                        } elseif (isset($folder->fullName)) {
                            $folderName = $folder->fullName;
                        } elseif (method_exists($folder, 'getFullName')) {
                            $folderName = $folder->getFullName();
                        } elseif (method_exists($folder, 'getName')) {
                            $folderName = $folder->getName();
                        }
                        if (strtoupper((string)$folderName) !== 'INBOX') {
                            continue;
                        }
                    }
                    catch(\Exception $e) {
                        Log::error($e->getMessage());
                        continue;
                    }

                    // ---------------------------------------------------------
                    // Fetch unseen messages — setFetchAttachment removed (v3)
                    // ---------------------------------------------------------
                    $msgs = $folder->query()
                        ->unseen()
                        ->since($before_five_minute)
                        ->leaveUnread()
                        ->setFetchFlags(false)
                        ->get();

                    foreach($msgs as $msg) {
                        $tos         = $this->getAddressList($msg->getTo());
                        $mail_subject = $this->getSubject($msg);
                        $mail_body   = (string)($msg->getHTMLBody() ?? '');
                        $this->info("P: " . $mail_subject);

                        // -------------------------------------------------
                        // Auto-reply / Out-of-Office detection
                        // -------------------------------------------------
                        $headers_str = $this->getHeaderStr($msg);
                        if (str_contains($headers_str, 'Auto-Submitted: auto-replied')) {
                            $this->info("Auto-reply detected (Out of Office)");
                            continue;
                        }

                        $lower_subject = strtolower($mail_subject);
                        $lower_body    = strtolower($mail_body);
                        $isOutOfOffice = false;

                        $outOfOfficePhrases = [
                            'out of office',
                            'auto reply',
                            'automatic reply',
                            'i am currently out of the office',
                            'i will respond upon my return',
                        ];
                        foreach ($outOfOfficePhrases as $phrase) {
                            if (str_contains($lower_subject, $phrase) || str_contains($lower_body, $phrase)) {
                                $isOutOfOffice = true;
                                break;
                            }
                        }
                        if ($isOutOfOffice) {
                            $this->info("Detected Out of Office: " . json_encode(array_column($tos, 'mail')));
                            continue;
                        }

                        // -------------------------------------------------
                        // References / conversation threading
                        // -------------------------------------------------
                        $references = $this->getReferencesStr($msg);
                        $this->info("message reference : " . json_encode($references));

                        if($references) {
                            $normalizedReferences = preg_replace('/\s+/', ' ', $references);
                            $normalizedReferences = str_replace(["\r", "\n", "\t"], ' ', $normalizedReferences);
                            $normalizedReferences = preg_replace_callback('/<([^>]+)>/', function($matches) {
                                $cleaned = preg_replace('/\s+/', '', $matches[1]);
                                return "<$cleaned>";
                            }, $normalizedReferences);
                            preg_match_all('/<([^>]+)>/', $normalizedReferences, $matches);
                            $normalizedReferencesArray = array_map('trim', $matches[1]);
                            $conversationCheck = $this->checkIsExistingConversation($normalizedReferencesArray);
                            if($conversationCheck) {
                                $isClosedTicket = Ticket::findOrFail($conversationCheck);
                                if(!in_array($isClosedTicket->status_id, [5, 6])) {
                                    $this->inreplyto_updatable_comment($conversationCheck, $msg, $mail_subject, $mail_body);
                                    continue;
                                }
                            }
                        }

                        // -------------------------------------------------
                        // Exception log check
                        // -------------------------------------------------
                        $isExistOnExceptionLog = $this->_isExistOnExceptionLog($this->getMessageId($msg));
                        if( $isExistOnExceptionLog ) {
                            continue;
                        }

                        // -------------------------------------------------
                        // To / alias routing
                        // -------------------------------------------------
                        $tos_mails = array_map(function($t){ return isset($t->mail) ? strtolower($t->mail) : ''; }, $tos);

                        if( ! $this->account_config->isToEmailSame($tos_mails) ) {
                            if( $this->alias_count < 1 ) {
                                continue;
                            }
                            if(in_array(strtolower($this->account_config->ebts_username), $tos_mails)) {
                                $alias_account = $this->find_matched_alias($this->account_config->ebts_username);
                                if (!$alias_account) {
                                    continue;
                                }
                                $this->alias_account = $alias_account;
                                $load_ticket_options = $this->load_alias_ticket_options($alias_account);
                                if (!$load_ticket_options) {
                                    $this->restore_default_ticket_options();
                                }
                            }
                        } else {
                            $this->alias_account = null;
                            $this->restore_default_ticket_options();
                        }

                        // -------------------------------------------------
                        // Body fallback to text
                        // -------------------------------------------------
                        if(trim($mail_body) == "") {
                            $mail_body = (string)($msg->getTextBody() ?? '');
                        }

                        $found_divider = stripos($mail_body, self::CONTENT_DIVIDER);
                        if( $found_divider !== false ) {
                            $new_mail_body = "";
                            if($found_divider <= 1) {
                                $new_mail_body = str_ireplace(self::CONTENT_DIVIDER, "", $mail_body);
                            } else {
                                $new_mail_body = substr($mail_body, 0, $found_divider);
                            }
                            $new_mail_body = trim($new_mail_body);
                            if( $new_mail_body ) {
                                $mail_body = $new_mail_body;
                            }
                        }

                        $lower_subject = strtolower($mail_subject);
                        $lower_body    = strtolower($mail_body);

                        // -------------------------------------------------
                        // Check for existing ticket ID in subject
                        // -------------------------------------------------
                        $check_for_tid = [];
                        preg_match('/###(\d*)###/', $lower_subject, $check_for_tid);
                        if(count($check_for_tid) > 1) {
                            $suspected_id = (int) $check_for_tid[1];
                            Log::Info('is_updatable_comment from NewTicketFromEmail');
                            $this->is_updatable_comment($suspected_id, $msg, $mail_subject, $mail_body);
                            continue;
                        }

                        // -------------------------------------------------
                        // Sender / blocklist check
                        // -------------------------------------------------
                        $get_sender  = $this->getSenderList($msg);
                        $sender      = "";
                        $tot_senders = count($get_sender);

                        if($tot_senders > 0) {
                            $sender = $get_sender[0];

                            $found_blocked_email = false;
                            foreach( $get_sender as $gs ) {
                                if( strpos( $this->blocked_accounts, strtolower($gs->mail) ) !== false ) {
                                    $found_blocked_email = true;
                                    break;
                                }
                            }
                            if($found_blocked_email) {
                                continue;
                            }
                        }

                        if($this->check_is_processed($this->getUid($msg), $this->getMessageId($msg))) {
                            continue;
                        }

                        if(! $this->load_user($sender)) {
                            $this->_logException($msg, $sender->mail ?? '', $mail_subject, "Unable to convert the email of third party to the ticket. It might be reason of the blocked domain/email accounts.");
                            continue;
                        }

                        // -------------------------------------------------
                        // Create new ticket
                        // -------------------------------------------------
                        $ticket_data   = array(
                            "subject" => $mail_subject,
                            "content" => $mail_body,
                            "company_id" => $this->account_config->company_id,
                        );
                        $combined_text = $mail_subject . ' - ' . CommonHelper::sanitizeHtmlContentData($mail_body);

                        if (config("app.client") == "safari") {
                            $ticketMLData = CommonHelper::createTicketFromMLIntent($combined_text, $this->user);
                            if (isset($ticketMLData["status"]) && $ticketMLData["status"] == "success") {
                                Log::info("Ticket ML Data" . json_encode($ticketMLData));
                                if (isset($ticketMLData["data"]["department_name"])) {
                                    $this->def_department = Department::find($ticketMLData["data"]["department_name"]["id"]);
                                }
                                if (isset($ticketMLData["data"]["category_name"])) {
                                    $this->def_prob_category = ProblemCategory::find($ticketMLData["data"]["category_name"]["id"]);
                                }
                                if (isset($ticketMLData["data"]["subcategory_name"])) {
                                    $this->def_sub_category = ProblemCategory::find($ticketMLData["data"]["subcategory_name"]["id"]);
                                }
                                $createdViaML = 6;
                            }
                        }

                        $validator = Validator::make($ticket_data, $this->rules, []);
                        if($validator->fails()) {
                            Log::error("Email Auto Creation Error:");
                            Log::error($validator->errors()->toArray());
                            $this->_logException($msg, $sender->mail ?? '', $mail_subject, "Either subject/content is empty (or) might be contain invalid characters.");
                            continue;
                        }

                        $ticket_data["is_temp"]            = null;
                        $ticket_data["created_via"]        = isset($createdViaML) ? $createdViaML : 3;
                        $ticket_data["creator_id"]         = $this->user->id;
                        $ticket_data["location_id"]        = $this->user->location_id;
                        $ticket_data["created_by"]         = Auth::user()->id;
                        $ticket_data["created_at"]         = $current_datetime->format('Y-m-d H:i:s');
                        $ticket_data["department_id"]      = $this->def_department->id;
                        $ticket_data["problem_category_id"]= $this->def_prob_category->id;
                        $ticket_data["priority_id"]        = $this->def_prob_category->priority_id ? $this->def_prob_category->priority_id : 3;

                        if($this->def_sub_category) {
                            $ticket_data["sub_category_id"] = $this->def_sub_category->id;
                            if( $this->def_sub_category->priority_id ) {
                                $ticket_data["priority_id"] = $this->def_sub_category->priority_id;
                            }
                        }

                        $ticket_data["ac_email_id"]        = $this->account_config->id;
                        $ticket_data["ac_email_uid"]       = $this->getUid($msg);
                        $ticket_data["ac_email_message_id"]= $this->getMessageId($msg);
                        $ticket_data["alias_acc_id"]       = $this->alias_account ? $this->alias_account->id : null;

                        $cc_emails = $this->make_cc_list($this->getAddressList($msg->getTo()), $this->getAddressList($msg->getCc()));
                        if( count($cc_emails) ) {
                            $ticket_data["cc_emails"] = implode(",", $cc_emails);
                        }

                        $this->ticket = new Ticket($ticket_data);
                        $this->ticket->tat        = $this->def_prob_category->tat != "" ? $this->def_prob_category->tat : $this->ticket->priority->service_time;
                        $this->ticket->tat_expire = $this->tkt_config->calculateAdvancedTat($this->ticket->tat, $current_datetime, $this->holidays);
                        $this->ticket->status_id  = 1;

                        if(config("app.client") == "safari") {
                            $this->ticket->assignByHirarchy(false, $tos);
                        } else {
                            $this->ticket->assignByHirarchy();
                        }

                        $processResult        = Attachment::processForB64Imgs($mail_body, null, Auth::user()->id);
                        $this->ticket->content = $processResult['content'];
                        $this->ticket->save();

                        app(RequestController::class)->createTasksFromCategory($this->ticket);

                        if( count($processResult['attachments']) ) {
                            Attachment::whereIn('id', $processResult['attachments'])->update(['ticket_id' => $this->ticket->id]);
                        }

                        $msg_attachments = $msg->getAttachments();
                        try {
                            $msg_attachments->each(function($file) use($msg) {
                                $this->add_attachment($msg, $file, $this->ticket->id);
                            });
                        }
                        catch(\Exception $e) {
                            Log::error($e->getMessage());
                        }

                        $tkt_update['ticket_id']  = $this->ticket->id;
                        $tkt_update['updated_by'] = Auth::user()->id;
                        $tkt_update['action_type']= 14;
                        CommonHelper::ticketStatusHistory($tkt_update, $this->ticket);

                        if ($this->ticket->assigned_to) {
                            $tkt_update['ticket_id']  = $this->ticket->id;
                            $tkt_update['updated_by'] = 0;
                            $tkt_update['assigned_to']= $this->ticket->assigned_to;
                            $tkt_update['action_type']= 1;
                            $tkt_update['tat']        = $this->ticket->tat;
                            CommonHelper::ticketStatusHistory($tkt_update);
                        }

                        if(config("app.ai_enabled")) {
                            TktEventWithProcessTicketSentiment::dispatch($this->ticket);
                        } else {
                            $triggerObj = TicketTrigger::count();
                            if($triggerObj > 0) {
                                event(new TicketCreated($this->ticket));
                            }
                        }

                        $user        = $this->user;
                        $alertnotify = false;
                        if($this->tkt_config->alerts_enabled == 1) {
                            $alertnotify = $this->tkt_config->alert_email;
                        }

                        if(config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                            try {
                                if($this->newly_generated_user) {
                                    $this->newly_generated_user = false;
                                }
                                if($alertnotify) {
                                    $cc_emails[] = $alertnotify;
                                }
                                Mail::to($user->email)->queue(new IntimateSuccessCreation($this->ticket, $user));
                            }
                            catch(\Exception $e) {
                                Log::error("NewTicketFromEmail IntimateSuccessCreation: " . $e->getMessage());
                            }
                        }

                        if($this->ticket->assigned_to) {
                            $user = User::find($this->ticket->assigned_to);
                            if(config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                try {
                                    if($alertnotify) {
                                        Mail::to($user->email)->cc($alertnotify)->queue(new IntimateAssigned($this->ticket, $user));
                                    } else {
                                        Mail::to($user->email)->queue(new IntimateAssigned($this->ticket, $user));
                                    }
                                }
                                catch(\Exception $e) {
                                    Log::error("Unable to send mail on create ticket from email " . $e->getMessage());
                                }
                            }
                        }

                        $autoUpdateConfig = TKTAutoUpdateSetting::first();
                        if (!empty($autoUpdateConfig) && isset($autoUpdateConfig->auto_response_for_ticket) && $autoUpdateConfig->auto_response_for_ticket == 1) {
                            $userId      = isset($createdViaML) ? 0 : $this->user->id;
                            $autoReplryObj = CommonHelper::getAIAutoReply($combined_text, $this->ticket->id, $userId);
                            Log::info("Auto reply object from AI ML : " . json_encode($autoReplryObj));
                        }

                        $msg->setFlag(['Seen']);
                        Log::channel('ticket')->info("NewTicketFromEmail: TID: " . $this->ticket->id);
                    }
                }
            }
            catch(\Exception $e) {
                Log::error(self::LOGID . ": " . ($this->credentials["username"] ?? '') . " - " . $e->getMessage());
            }
        }
    }

    public function checkAliasMail($headers) {
        try {
            $headerArray = explode("\r\n", $headers);
            $aliasMail   = [];
            foreach($headerArray as $key => $value) {
                if(strpos($value,"To") === 0) {
                    $aliasExplode = explode(":", $value);
                    if (preg_match('/<([^>]+)>/', $aliasExplode[1], $matches)) {
                        array_push($aliasMail, trim($matches[1]));
                    }
                    $checkNextLine = true;
                    $index = $key;
                    while($checkNextLine){
                        $nextValue = isset($headerArray[$index++]) ? $headerArray[$index++] : null;
                        if (strpos($nextValue, ':') !== false) {
                            $checkNextLine = false;
                            continue;
                        }
                        if(!empty($nextValue)) {
                            if (preg_match('/<([^>]+)>/', $nextValue, $matches)) {
                                array_push($aliasMail, trim($matches[1]));
                            }
                        }
                    }
                }
            }
            return $aliasMail;
        } catch(\Exception $e) {
            Log::error("checkAliasMail : ".$e->getMessage());
            return [];
        }
    }

    public function add_attachment(&$msg, &$file, $ticket_id=false, $following_id=null) {
        try {
            $filePath = date("Y").'/'.date('m').'/'.date('d');
            $checkFolderPath = CommonHelper::attachmentFolderStructure('storage_tkt', $filePath);
            if(!$checkFolderPath) {
                Log::error("Attachment Directory not found");
                return;
            }

            $a = new Attachment();
            $a->ticket_id        = $ticket_id;
            $a->original_file_name = $file->getName();
            $a->original_file_name = $a->trimUnfittedName($a->original_file_name);
            $a->extension        = $file->getExtension();
            $a->cid              = $file->id;

            if( $a->isBlockedExtension($a->extension) ) {
                return;
            }

            $a->file_name    = $filePath . DIRECTORY_SEPARATOR . $this->get_random_file_name($a->original_file_name, $a->extension);
            $a->uploader_id  = $this->user->id;
            $a->tmp_id       = null;
            $a->following_id = $following_id != null ? $following_id : null;

            $path = Storage::disk('storage_tkt')->path("");
            if( $file->save($path, $a->file_name) ) {
                $a->save();
            }

            if( in_array($a->extension, ["png", "jpeg", "jpg"]) !== false ) {
                try {
                    $disk      = Storage::disk('storage_tkt');
                    $path      = $disk->path($a->file_name);
                    $thumb_name = $a->getThumbName();
                    if(! $thumb_name) {
                        throw new \Exception("Invalid Image Name " . $a->ticket_id);
                    }
                    $thumb_path = $disk->path($thumb_name);

                    Image::make($path)->resize(100, null, function($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->save($thumb_path);

                    $a->thumbnail = $thumb_name;
                    $a->save();
                }
                catch(\Exception $e) {
                    Log::error($e->getMessage());
                }
            }
        }
        catch(\Exception $e) {
            Log::error("new ticket from email issue @ add attachment");
            $this->_logException($msg, $this->user->email, "", "Unable to get the Attached File. It might be any of following reason - Unsupported file format/invalid file size/corrupted file", $ticket_id, $following_id);
            Log::error($e->getMessage());
        }
    }

    protected function get_random_file_name($file_name, $extension) {
        try {
            $e = explode('.', $file_name);
            $r = array_reverse($e);
            $g = Str::random(28) . date('ymdhis') . '.' . $r[0];
            return $g;
        }
        catch(\Exception $e) {
            Log::error("Rand file name generate issue at ticket from email ");
            Log::error($e->getMessage());
            return (Str::random(28) . '.' . $extension);
        }
    }

    protected function make_cc_list($to, $cc) {
        $cc_emails = [];

        $get_tos  = is_array($to) ? $to : [];
        foreach($get_tos as $t) {
            try {
                $cc_emails[] = trim(strtolower($t->mail));
            } catch(\Exception $e) {
                Log::error($e->getMessage());
            }
        }

        $get_ccs = is_array($cc) ? $cc : [];
        foreach($get_ccs as $c) {
            try {
                $cc_emails[] = trim(strtolower($c->mail));
            } catch(\Exception $e) {
                Log::error($e->getMessage());
            }
        }

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
        $get_diff   = array_diff($cc, $arr_master);

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
                $short_subject = strlen($subject) > 80 ? substr($subject, 0, 80) : $subject;
            }

            $newExceptionLog = new ExceptionNotification();
            $newExceptionLog->ac_email_message_id    = $this->getMessageId($msg);
            $newExceptionLog->ac_email               = $this->account_config->ebts_username;
            $newExceptionLog->mail_from              = $sender_mail;
            $newExceptionLog->mail_datetime          = $this->getDateStr($msg);
            $newExceptionLog->subject                = $short_subject;
            $newExceptionLog->reason                 = $reason;
            $newExceptionLog->ticket_id              = $ticket_id;
            $newExceptionLog->following_id           = $following_id;
            $newExceptionLog->auto_creation_account_id = $this->account_config->id;
            $newExceptionLog->save();

            $msg->setFlag(['Seen']);
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    protected function _isExistOnExceptionLog($ac_email_message_id) {
        try {
            $count = ExceptionNotification::where('ac_email_message_id', 'like', $ac_email_message_id)->count();
            return $count > 0 ? true : false;
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    protected function inreplyto_updatable_comment($suspected_id, &$msg, $mail_subject, $mail_body) {
        try {
            $senders = $this->getSenderList($msg);
            $sender  = count($senders) ? $senders[0] : "";

            if(! $this->load_user($sender)) {
                $this->_logException($msg, $sender->mail ?? '', $mail_subject, "Third-party email for the ticket #{$suspected_id} has been stopped. It might be the reason of blocked domain/accounts.");
                return false;
            }

            try {
                $ticket = Ticket::findOrFail($suspected_id);
                if($ticket->is_temp || in_array($ticket->status_id, [6])) {
                    $this->_logException($msg, $sender->mail ?? '', $mail_subject, "The target ticket #{$suspected_id} might be closed/invalid");
                    return false;
                }
            }
            catch(\Exception $e) {
                $this->_logException($msg, $sender->mail ?? '', $mail_subject, "The target ticket #{$suspected_id} might be closed/invalid");
                return false;
            }

            $tf = new TktFollowing();
            $tf->ticket_id  = $ticket->id;
            $tf->is_note    = 0;
            $tf->updated_by = $this->user->id;

            $tf->ac_email_id       = $this->account_config->id;
            $tf->ac_email_uid      = $this->getUid($msg);
            $tf->ac_email_message_id = $this->getMessageId($msg);
            $tf->alias_acc_id      = $this->alias_account ? $this->alias_account->id : null;

            $processResult = Attachment::processForB64Imgs($mail_body, $ticket->id, Auth::user()->id);
            $tf->remarks   = $processResult['content'];

            $cc_emails = $this->make_cc_list($this->getAddressList($msg->getTo()), $this->getAddressList($msg->getCc()));
            if( count($cc_emails) ) {
                $tf->cc_emails = implode(",", $cc_emails);
                $new_cc_master = $this->checkNewCcAddress($ticket->cc_emails, $cc_emails);
                if( is_array($new_cc_master) && count($new_cc_master) ) {
                    $ticket->cc_emails = implode(",", $new_cc_master);
                }
            }

            $tf->action_type = 7;

            if(! $tf->save()) {
                Log::error("Ticket Update via Email Error: Unable to save tf");
                return false;
            }

            $lower_subject       = strtolower($mail_subject);
            $is_status_tag_found = stripos($lower_subject, '###status:');
            if( $is_status_tag_found !== false && $this->user->id != $ticket->creator_id && $this->user->id == $ticket->assigned_to ) {
                $status_word = trim(substr($lower_subject, $is_status_tag_found + 10));
                $end_hash_part = strpos($status_word, '#');
                if( $end_hash_part !== false ) {
                    $status_word = substr($status_word, 0, $end_hash_part);
                }
                if( in_array($status_word, $this->tkt_status_arr) ) {
                    $status_id  = $this->getStatusIdByName($status_word);
                    $param_data = [];
                    $param_data['status_id'] = $status_id;
                    $param_data['comment']   = trim($mail_body);
                    $so_result = $this->updateTicketWithStatus($ticket, $param_data, $msg);
                    if( $so_result ) {
                        $msg->setFlag(['Seen']);
                    }
                    return $so_result;
                }
            }

            if( count($processResult) && count($processResult['attachments']) ) {
                Attachment::whereIn('id', $processResult['attachments'])->update(['following_id' => $tf->id]);
            }

            $msg_attachments = $msg->getAttachments();
            try {
                $msg_attachments->each(function($file) use($ticket, $tf, $msg) {
                    $this->add_attachment($msg, $file, $ticket->id, $tf->id);
                });
            }
            catch(\Exception $e) {
                Log::error($e->getMessage());
                $this->_logException($msg, $sender->mail ?? '', $mail_subject, "Unable to fetch the attachments.", $ticket->id, $tf->id);
            }

            $ticket->updated_at = date('Y-m-d H:i:s');
            $ticket->save();

            $alertnotify = null;
            if($this->tkt_config->alerts_enabled == 1){
                $alertnotify = $this->tkt_config->alert_email;
            }

            $is_user_same_creator = $ticket->creator_id == $this->user->id ? true : false;
            $creator    = User::find($ticket->creator_id);
            $handler    = User::find($ticket->assigned_to);
            $is_user_cc = $ticket->norCreatorOrHandler($this->user->id);

            if(config('mail.service_enabled')) {
                $remarks = isset($tf->remarks) && !empty($tf->remarks) ? $tf->remarks : " ";
                if( $is_user_cc ) {
                    try {
                        $temp_to = [];
                        if( $creator && $creator->email && filter_var($creator->email, FILTER_VALIDATE_EMAIL) ) {
                            $temp_to[] = $creator->email;
                        }
                        if( $handler && $handler->email && filter_var($handler->email, FILTER_VALIDATE_EMAIL) ) {
                            $temp_to[] = $handler->email;
                        }
                        if($this->tkt_config->cc_to_settings_mail === 1 && $alertnotify) {
                            Mail::to($temp_to)->cc($alertnotify)->queue(new UserComment($ticket, $creator, $remarks, $tf));
                        } else {
                            $this->info("mail_subject: " . $mail_subject);
                            Mail::to($temp_to)->queue(new UserComment($ticket, $creator, $remarks, $tf));
                        }
                    }
                    catch(\Exception $e) {
                        Log::error("Issue Commenter is cc: ");
                        Log::error($e->getMessage());
                    }
                }
                elseif( $is_user_same_creator ) {
                    if($handler && $handler->email && filter_var($handler->email, FILTER_VALIDATE_EMAIL)) {
                        try {
                            if($this->tkt_config->cc_to_settings_mail === 1 && $alertnotify) {
                                Mail::to($handler->email)->cc($alertnotify)->queue(new UserComment($ticket, $handler, $remarks, $tf));
                            } else {
                                Mail::to($handler->email)->queue(new UserComment($ticket, $handler, $remarks, $tf));
                            }
                        }
                        catch(\Exception $e) {
                            Log::error('Issue at 440 of newticktfrom email cron');
                            Log::error($e->getMessage());
                        }
                    }
                }
                elseif( $creator && $creator->email && filter_var($creator->email, FILTER_VALIDATE_EMAIL) ) {
                    try {
                        if($this->tkt_config->cc_to_settings_mail === 1 && $alertnotify) {
                            Mail::to($creator->email)->cc($alertnotify)->queue(new UserComment($ticket, $creator, $remarks, $tf));
                        } else {
                            Mail::to($creator->email)->queue(new UserComment($ticket, $creator, $remarks, $tf));
                        }
                    }
                    catch(\Exception $e) {
                        Log::error('Issue at 475 of newticktfrom email cron');
                        Log::error($e->getMessage());
                    }
                }
            }

            $msg->setFlag(['Seen']);
            return true;
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }
}
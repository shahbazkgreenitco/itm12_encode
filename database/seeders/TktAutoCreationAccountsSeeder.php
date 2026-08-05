<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TktAutoCreationAccountsSeeder extends Seeder
{
    public function run()
    {
        for ($i = 1; $i <= 5; $i++) {

            DB::table('tkt_auto_creation_accounts')->insert([
                'auto_create_from_email' => 'test'.$i.'@mail.com',
                'ebts_host' => 'imap.mail.com',
                'ebts_port' => 993,
                'ebts_encryption' => 'ssl',
                'validate_cert' => 1,
                'ebts_username' => 'test'.$i.'@mail.com',
                'ebts_password' => bcrypt('password123'),

                'default_department_id' => rand(1,5),
                'default_prob_cat_id' => rand(1,5),
                'default_sub_cat_id' => rand(1,5),

                'isReqDeptChgBfrResolve' => rand(0,1),
                'except_notify_status' => 'open,closed',
                'except_notify_tos' => 'notify'.$i.'@mail.com',
                'except_notify_ccs' => 'cc'.$i.'@mail.com',

                'ticketing_restricted' => rand(0,1),
                'ticketing_allowed_domains' => 'gmail.com,yahoo.com',
                'ticketing_allowed_emails' => 'allowed'.$i.'@mail.com',
                'ticketing_blocked_domains' => 'spam.com',
                'ticketing_blocked_accounts' => 'blocked'.$i.'@mail.com',

                'restricted_words' => 'test,spam,block',

                'user_id' => 1,
                'outgoing_mail_id' => 1,

                'isReqDeptChgBfrAssigned' => rand(0,1),
                'isReqDeptChgBfrTransfer' => rand(0,1),

                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
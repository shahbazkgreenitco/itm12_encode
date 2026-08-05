<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\ChangeLogMail;
use Mail;
class ChangeLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change-log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Change Log Email to Customers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try{
            $emails = [
                    ['email'=> 'sorenp@greenitco.com','name'=> 'Soren Paswan'],
                    ['email' => 'nareshv@greenitco.com','name' => 'Naresh'],
                ];
            foreach($emails as $email) {
                Mail::to($email['email'])->send(new ChangeLogMail($email['name']));
            }
        } catch(Exception $e) {
            Log::error('change log email issues : '.$e->getMessage());
            return false;
        }
    }
}

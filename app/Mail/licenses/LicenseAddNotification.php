<?php

namespace App\Mail\licenses;

use App\Models\Actionlog;
use App\Models\License;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LicenseAddNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $objLic,
        $user,
        $site_name,
        $email_thankuby;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($objLic, $user)
    {
        $this->objLic = $objLic;
        $this->user = $user;
        $this->site_name = Settings::getSettings()->site_name;
        $this->email_thankuby = Settings::getSettings()->email_thankuby;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $client_name = config('app.client');

        switch ($client_name) {
            case 'tscpl':
                return $this->view('mail.clients.tbsl.license_add');
            default:
                return $this->view('mail.Licenses.license_add');
        }
    }
}

<?php

namespace App\Mail\licenses;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\License;
use App\Models\Actionlog;
use App\Models\User;
use App\Models\Settings;

class LicenseCheckinNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $license, $logaction, $user, $eula, $site_name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($license, $logaction, $user)
    {
        $this->license = $license;
        $this->logaction = $logaction;
        $this->user = $user;
        $this->eula = $license->getEula();
        $this->site_name = Settings::getSettings()->site_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $client_name = config('app.client');

        switch($client_name) {
            case "tscpl":
                return $this->view('mail.clients.tbsl.license_checkin_notification');
            default:
                return $this->view('mail.licenses.license_checkin_notification');
        }
    }
}

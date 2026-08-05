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

class LicenseCheckoutNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $license,
        $logaction,
        $user,
        $eula,
        $site_name,
        $email_thankuby;

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
            case 'tbsl':
                return $this->view('mail.clients.tbsl.license_checkout_notification');
            default:
                return $this->view('mail.licenses.license_checkout_notification');
        }
    }
}

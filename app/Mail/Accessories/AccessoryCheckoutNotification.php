<?php
namespace App\Mail\Accessories;

use App\Models\Accessory;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccessoryCheckoutNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $accessory, $log, $user, $eula, $site_name, $email_thankuby, $target_chkout_to, $target_assigned_name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($accessory, $log, $user, $target_chkout_to, $target_assigned_name)
    {
        $this->accessory            = $accessory;
        $this->log                  = $log;
        $this->user                 = $user;
        $this->eula                 = $accessory->getEula();
        $this->site_name            = Settings::getSettings()->site_name;
        $this->email_thankuby       = Settings::getSettings()->email_thankuby;
        $this->target_chkout_to     = $target_chkout_to;
        $this->target_assigned_name = $target_assigned_name;
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
            case "tscpl":
                return $this->view('mail.clients.tbsl.accessory_checkout_notification');
            case "ltsct":
                return $this->view('mail.clients.ltsct.assets.accessory_checkout_notification');
            case "knightfrank":
                return $this->view('mail.clients.knightfrank.accessory_checkout_notification');
            default:
                return $this->view('mail.accessories.accessory_checkout_notification');
        }
    }
}

<?php
namespace App\Mail\Accessories;

use App\Models\Accessory;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccessoryCheckinNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $accessory, $log, $user, $eula, $site_name;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($accessory, $log, $user)
    {
        $this->accessory = $accessory;
        $this->log       = $log;
        $this->user      = $user;
        $this->eula      = $accessory->getEula();
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
        switch ($client_name) {
            case "tscpl":
                return $this->view('mail.clients.tbsl.accessory_checkin_notification');
            case "knightfrank":
                return $this->view('mail.clients.knightfrank.accessory_checkin_notification');
            default:
                return $this->view('mail.accessories.accessory_checkin_notification');
        }
    }
}

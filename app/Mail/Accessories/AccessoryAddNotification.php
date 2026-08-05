<?php
namespace App\Mail\Accessories;

use App\Models\Settings;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccessoryAddNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $acc, $user, $site_name, $email_thankuby;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($acc, $user)
    {
        $this->acc            = $acc;
        $this->user           = $user;
        $this->site_name      = Settings::getSettings()->site_name;
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
            case "tscpl":
                return $this->view('mail.clients.tbsl.accessory_add');
            case "knightfrank":
                return $this->view('mail.clients.knightfrank.accessory_add');
            default:
                return $this->view('mail.accessories.accessory_add');
        }
    }
}

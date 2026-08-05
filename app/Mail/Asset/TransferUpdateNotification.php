<?php

namespace App\Mail\Asset;

use App\Models\Transfer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;
use App\Models\Settings;

class TransferUpdateNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user, $site_name, $transferObj, $email_thankuby, $type;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Transfer $transferObj, User $user ,?string $type = null)
    {
        $this->type = $type;
        $this->user = $user;
        $this->transferObj = $transferObj;
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
        return $this->subject('Asset Transfer Details')->view('mail.asset.transfer_notification');
        switch ($client_name) {
            case "ltts":
                return $this->subject('Asset Transfer Details')->view('mail.clients.ltts.assets.transfer_notification');
            case "tscpl":
                return $this->subject('Asset Transfer Details')->view('mail.clients.tbsl.asset.transfer_notification');
            case "dnatalogistics":
                return $this->subject('Asset Transfer Details')->view('mail.clients.dnata.transfer_notification');
            case "etherealmachines":
                return $this->subject('Asset Transfer Details')->view('mail.clients.etherealmachines.transfer_notification');
            default:
                return $this->subject('Asset Transfer Details')->view('mail.asset.transfer_notification');
        }
    }
}

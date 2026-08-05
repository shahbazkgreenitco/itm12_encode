<?php
namespace App\Mail\Accessories;

use App\Models\Actionlog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccessoryScrapRevert extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $actionlog;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Actionlog $actionlog)
    {
        $this->actionlog = $actionlog;
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
                return $this->subject("Accessories Scrap Revert")->view('mail.clients.tbsl.accessory_scrap_revert')->with('actionlog', $this->actionlog);
            default:
                return $this->subject("Accessories Scrap Revert")->view('mail.accessories.accessory_scrap_revert')->with('actionlog', $this->actionlog);
        }
    }
}

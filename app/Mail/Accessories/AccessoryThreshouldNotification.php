<?php
namespace App\Mail\Accessories;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccessoryThreshouldNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $thresholdcat;
    public $eula_text;
    public $accDetails, $accThreshold, $availableSingleAcc, $accessoryQuantity;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($accDetails, $accThreshold, $availableSingleAcc, $accessoryQuantity)
    {
        $this->accDetails         = $accDetails;
        $this->accThreshold       = $accThreshold;
        $this->availableSingleAcc = $availableSingleAcc;
        $this->accessoryQuantity  = $accessoryQuantity;
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
                return $this->view('mail.clients.tbsl.accessoryThreshouldNotification');
            default:
                return $this->view('mail.accessories.accessoryThreshouldNotification');
        }
    }
}

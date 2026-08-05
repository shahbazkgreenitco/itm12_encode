<?php

namespace App\Mail;

use App\Models\Category;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ThreshouldLicenseNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $thresholdcat;
    public $eula_text;

    public $catDetail,
        $thresouldValue,
        $catLicenseCount;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Category $catDetail, $thresouldValue, $catLicenseCount)
    {
        $this->catDetail = $catDetail;
        $this->thresouldValue = $thresouldValue;
        $this->catLicenseCount = $catLicenseCount;
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
                return $this->view('mail.clients.tbsl.threshouldLicenseNotification')->with('catDetail', $this->catDetail)->with('thresouldValue', $this->thresouldValue)->with('catLicenseCount', $this->catLicenseCount);
            default:
                return $this->view('mail.licenses.threshouldLicenseNotification')->with('catDetail', $this->catDetail)->with('thresouldValue', $this->thresouldValue)->with('catLicenseCount', $this->catLicenseCount);
        }
    }
}

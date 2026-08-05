<?php

namespace App\Mail\Consumables;

use App\Models\Category;
use App\Models\Settings;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ThreshouldNotification extends Mailable
{
    use Queueable, SerializesModels;
    public $catDetail, $thresouldValue, $deployableCatDeviceCount, $site_name, $email_thankuby, $eula_text, $thresholdcat;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Category $catDetail,$thresouldValue,$deployableCatDeviceCount)
    {
        $this->catDetail = $catDetail ;
        $this->thresouldValue = $thresouldValue ;
        $this->deployableCatDeviceCount = $deployableCatDeviceCount;
        $this->site_name = Settings::getSettings()->site_name;
        $this->email_thankuby = Settings::getSettings()->email_thankuby;
    }

    /**
     * Get the message envelope.
    */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Threshold Notification",
        );
    }
    /**
     * Get the message content definition.
     */
    public function content()
    {
        $client_name = config('app.client');

        $view = match ($client_name) {
            'tscpl' => 'mail.clients.tbsl.threshouldNotification',
            default => 'mail.threshouldNotification',
        };

         return new Content(
            view: $view,
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}

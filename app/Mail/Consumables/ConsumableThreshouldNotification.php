<?php

namespace App\Mail\Consumables;

use App\Models\Category;
use App\Models\Settings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConsumableThreshouldNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $thresholdcat;
    public $eula_text;
    public $consumableDetails, $singleConsThreshold, $availableSingleConsumables, $consumableQuantity, $site_name, $email_thankuby;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($consumableDetails, $singleConsThreshold, $availableSingleConsumables, $consumableQuantity)
    {
        $this->consumableDetails = $consumableDetails ;
        $this->singleConsThreshold = $singleConsThreshold ;
        $this->availableSingleConsumables = $availableSingleConsumables;
        $this->consumableQuantity = $consumableQuantity;
        $this->site_name = Settings::getSettings()->site_name;
        $this->email_thankuby = Settings::getSettings()->email_thankuby;
    }

    /**
     * Get the message envelope.
    */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Consumable Threshould Notification',
        );
    }

     /**
     * Get the message content definition.
     */
    public function content()
    {
        $client_name = config('app.client');

        $view = match ($client_name) {
            'tscpl' => 'mail.clients.tbsl.consumableThreshouldNotification',
            default => 'mail.consumables.consumableThreshouldNotification',
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
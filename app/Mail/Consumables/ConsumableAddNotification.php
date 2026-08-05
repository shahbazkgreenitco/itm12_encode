<?php

namespace App\Mail\Consumables;

use App\Models\Settings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConsumableAddNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    
    public $objConsumable, $user, $site_name, $email_thankuby;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($objConsumable, $user)
    {
        $this->objConsumable = $objConsumable;
        $this->user = $user;
        $this->site_name = Settings::getSettings()->site_name;
        $this->email_thankuby = Settings::getSettings()->email_thankuby;
    }

    /**
     * Get the message envelope.
    */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Consumable Add Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content()
    {
        $client_name = config('app.client');

        $view = match ($client_name) {
            'tbsl' => 'mail.clients.tbsl.consumable_add',
            default => 'mail.consumables.consumable_add',
        };

        return new Content(
            view: $view,
            with: [
                'objConsumable' => $this->objConsumable,
                'email_thankuby' => $this->email_thankuby,
                'site_name' => $this->site_name,
                'user' => $this->user,
            ],
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

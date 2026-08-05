<?php

namespace App\Mail\Consumables;

use App\Models\Actionlog;
use App\Models\Settings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConsumableCheckin extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $actionlog, $site_name, $email_thankuby;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Actionlog $actionlog)
    {
        $this->actionlog = $actionlog;
        $this->site_name = Settings::getSettings()->site_name;
        $this->email_thankuby = Settings::getSettings()->email_thankuby;
    }

    /**
     * Get the message envelope.
    */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Consumable Check-in',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content()
    {
        $client_name = config('app.client');

        $view = match ($client_name) {
            'tbsl' => 'mail.clients.tbsl.consumableCheckin',
            default => 'mail.consumables.consumableCheckin',
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

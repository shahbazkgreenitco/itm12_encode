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

class ConsumableScrapRevert extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Actionlog $actionlog;
    public $site_name, $email_thankuby, $consumable;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Actionlog $actionlog, $consumable)
    {
        $this->consumable = $consumable;
        $settings = Settings::first();
        if($settings) {
            $this->site_name = $settings->site_name;
            $this->email_thankuby = $settings->email_thankuby;
        }
        $this->actionlog = $actionlog;
    }

    /**
     * Get the message envelope.
    */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Consumable Scrap Revert',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content()
    {
        $client_name = config('app.client');

        $view = match ($client_name) {
            'tscpl' => 'mail.clients.tbsl.ConsumableScrapRevert',
            default => 'mail.consumables.ConsumableScrapRevert',
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
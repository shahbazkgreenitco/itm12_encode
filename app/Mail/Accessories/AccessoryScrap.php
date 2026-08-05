<?php

namespace App\Mail\Accessories;

use App\Models\Actionlog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccessoryScrap extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Actionlog $actionlog;
    public $companyId;

    /**
     * Create a new message instance.
     */
    public function __construct(Actionlog $actionlog, $companyId = null)
    {
        $this->actionlog = $actionlog;
        $this->companyId = $companyId;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Accessory Scrap',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
       
        $clientName = config('app.client');

        $view = match ($clientName) {
            'tscpl' => 'mail.clients.tbsl.accessory_scrap',
            default => 'mail.accessories.accessory_scrap',
        };

        return new Content(
            view: $view,
            with: [
                'actionlog' => $this->actionlog,
                'companyId' => $this->companyId,
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
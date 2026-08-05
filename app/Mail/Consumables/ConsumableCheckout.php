<?php

namespace App\Mail\Consumables;

use App\Models\Actionlog;
use App\Models\Consumable;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConsumableCheckout extends Mailable
{
    use Queueable, SerializesModels;

    public $consumable, $log, $eula, $site_name, $email_thankuby, $assignedTo, $target_assigned_name, $require_acceptance;

    public function __construct(Consumable $consumable, $assignedTo, $target_assigned_name, Actionlog $actionlog, $eula_text)
    {
        $this->consumable = $consumable;
        $this->log = $actionlog ;
        $this->eula = $eula_text ;
        $this->require_acceptance = $consumable->category->require_acceptance;
        $this->site_name = Settings::getSettings()->site_name;
        $this->email_thankuby = Settings::getSettings()->email_thankuby;
        $this->assignedTo = $assignedTo;
        $this->target_assigned_name = $target_assigned_name;
        $this->subject = !empty($consumable->name) ? "Consumable Checkout"." - ".$consumable->name : "Consumable Checkout";
    }

    /**
     * Get the message envelope.
    */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content()
    {
        $client_name = config('app.client');

        $view = match ($client_name) {
            'cactus' => 'mail.clients.cactus.consumableCheckout',
            'tscpl' => 'mail.clients.tbsl.consumableCheckout',
            default => 'mail.consumables.consumableCheckout',
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

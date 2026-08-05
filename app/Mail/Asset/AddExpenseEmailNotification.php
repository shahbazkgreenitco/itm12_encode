<?php

namespace App\Mail\Asset;

use App\Models\Settings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;


class AddExpenseEmailNotification extends Mailable
{
    use Queueable, SerializesModels;
    public $dm, $user_name, $site_name, $device, $email_thankuby;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($dm, $user_name, $device)
    {
        $this->dm = $dm;
        $this->user_name = $user_name;
        $this->device = $device;
        $this->site_name = Settings::getSettings()->site_name;
        $this->email_thankuby = Settings::getSettings()->email_thankuby;
    }

    /**
     * Get the message content definition.
     */
    public function content()
    {
        $client_name = config('app.client');

        $view = match ($client_name) {
            'tbsl' => 'mail.clients.ltts.expense_notification',
            default => 'mail.devices.expense_notification',
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

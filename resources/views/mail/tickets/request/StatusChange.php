<?php

namespace App\Mail\Ticket\Request;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;
use App\Models\Settings;

class StatusChange extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    
    public $pro_request, $pab, $site_name, $email_thankuby;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pro_request, $pab)
    {
        $this->pro_request = $pro_request;
        $this->pab = $pab;
        $this->site_name = Settings::getSettings()->site_name;
        $this->email_thankuby = Settings::getSettings()->email_thankuby;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = '#' . $this->pro_request->procure_tag . ' Status Changed to ' . $this->pro_request->status->name;
        if(config('app.client') == "ltts" && config('app.sub_client') == 'admin') {
            return $this->subject($subject)->view('mail.clients.ltts_admin.sr_status_change');
        } else {
            return $this->subject($subject)->view('mail.tickets.request.statusChange');
        }
    }
}

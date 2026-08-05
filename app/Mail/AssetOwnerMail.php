<?php

namespace App\Mail;

use App\Models\NetworkInventory\Basic;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Device;
use App\Models\Actionlog;
use App\Models\User;
use App\Models\Settings;
use App\Models\AssetAllocationType;

class AssetOwnerMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    
    public $device, $log, $asset_owner_mail, $site_name, $email_thankuby, $eula;
    public $ram, $hdd, $os, $processorName, $allocationType;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Device $device, Actionlog $log = null, $asset_owner_mail)
    {
        $this->device = $device;
        $this->asset_owner_mail = $asset_owner_mail;
        $this->log = $log;
        $this->site_name = Settings::getSettings()->site_name;
        $this->email_thankuby = Settings::getSettings()->email_thankuby;
        $this->eula = $device->getEula();

        try {
            $get_ni_basic = Basic::where('BIOSSerialNumber', 'like', $this->device->serial)->get();

            if($get_ni_basic && count($get_ni_basic)) {
                $basic = $get_ni_basic[0];
                $this->os = $basic->OSCaption;
                $this->ram = $basic->RamSize;
                $this->hdd = $basic->HddSize;
                $this->processorName = $basic->ProcessorName;
            }

            $checkoutLog = Actionlog::where('id', $device->chkout_log_id)->first();
            if(!empty($checkoutLog) && $checkoutLog->allocation_type_id != "") {
                $allocationType = AssetAllocationType::find($checkoutLog->allocation_type_id);
                $this->allocationType = $allocationType->name;
            }
        }
        catch(\Exception $e) {
            $this->ram = "";
            $this->hdd = "";
            $this->os = "";
            $this->processorName = "";
            $this->allocationType = "";
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $client_name = config('app.client');

        switch($client_name) {
            case "safari":

                try {
                    $get_ni_basic = Basic::where('BIOSSerialNumber', 'like', $this->device->serial)->get();

                    if($get_ni_basic && count($get_ni_basic)) {
                        $basic = $get_ni_basic[0];
                        $this->os = $basic->OSCaption;
                        $this->ram = $basic->RamSize;
                        $this->hdd = $basic->HddSize;
                    }
                }
                catch(\Exception $e) {
                    $this->ram = "";
                    $this->hdd = "";
                    $this->os = "";
                }

                return $this->subject('Mapped to asset owner')->view('mail.clients.safari.asset_owner');
            case "ltts":
                return $this->subject('Device Mapped to asset owner')->view('mail.clients.ltts.assets.asset_owner');
            case "tscpl":
                return $this->subject('Mapped to asset owner')->view('mail.clients.tbsl.asset_owner');
            case "knightfrank":
                return $this->subject('Mapped to asset owner')->view('mail.clients.knightfrank.asset_owner');
            default:
                return $this->subject('Mapped to asset owner')->view('mail.asset_owner');
        }
    }
}

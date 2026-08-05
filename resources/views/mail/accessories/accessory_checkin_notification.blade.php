{{-- 
/**
------------------------------------------------------------
File: accessory_checkin.blade.php
Module: Accessories
ACC/26/06
------------------------------------------------------------
Version: 1.0.0
Author: Safdar Ali
Page ID: #ACC-018
Created On: 2026-06-08
Reviewed By: -
------------------------------------------------------------
Change Log:
[1.0.0] - Initial version and set according to new mail template. - Safdar Ali - 2026-06-08
------------------------------------------------------------
*/
--}}

{{-- Accessory Check-In Notification Mail --}}

@extends('mail.mailTemplate')
@section('content')
<h1 style="margin:0 0 15px;color:#d72638;font-size:30px;line-height:38px;font-weight:bold;">
    Hello {{ $user->first_name }},
</h1>
<p style="margin:0 0 25px;color:#1e293b;font-size:16px;line-height:28px;">
    The accessory has been successfully checked in from you. Below are the details:
</p>
<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td style="padding-bottom:12px;">
            <table width="100%" cellpadding="0" cellspacing="0"
                style="border:1px solid #f3b7be;border-radius:10px;background:#ffffff;">
                <tr>
                    <td style="padding:14px 18px;font-size:15px;color:#1e293b;">
                        <strong>Accessory Name:</strong>
                        {{ $accessory->name }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    @if($accessory->category)
    <tr>
        <td style="padding-bottom:12px;">
            <table width="100%" cellpadding="0" cellspacing="0"
                style="border:1px solid #f3b7be;border-radius:10px;background:#ffffff;">
                <tr>
                    <td style="padding:14px 18px;font-size:15px;color:#1e293b;">
                        <strong>Category:</strong>
                        {{ $accessory->category->name }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    @endif

    <tr>
        <td style="padding-bottom:12px;">
            <table width="100%" cellpadding="0" cellspacing="0"
                style="border:1px solid #f3b7be;border-radius:10px;background:#ffffff;">
                <tr>
                    <td style="padding:14px 18px;font-size:15px;color:#1e293b;">
                        <strong>Check-In Date:</strong>
                        {{ CommonHelper::getDateAs($log->created_at, "d/m/Y", "Y-m-d H:i:s") }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    @if($log->note)
    <tr>
        <td style="padding-bottom:12px;">
            <table width="100%" cellpadding="0" cellspacing="0"
                style="border:1px solid #f3b7be;border-radius:10px;background:#ffffff;">
                <tr>
                    <td style="padding:14px 18px;font-size:15px;color:#1e293b;">
                        <strong>Additional Notes:</strong>
                        {{ $log->note }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    @endif
    ```

</table>

<p
    style="margin-top:25px;padding:15px;background:#fff5f5;border-left:4px solid #d72638;color:#1e293b;border-radius:6px;">
    This accessory has been successfully returned and is now available in inventory.
</p>

@endsection
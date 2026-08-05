{{-- 
/**
------------------------------------------------------------
File: accessory_expected_date.blade.php
Module: Accessories
ACC/26/06
------------------------------------------------------------
Version: 1.0.0
Author: Safdar Ali
Page ID: #ACC-020
Created On: 2026-06-08
Reviewed By: -
------------------------------------------------------------
Change Log:
[1.0.0] - Initial version and set according to new mail template. - Safdar Ali - 2026-06-08
------------------------------------------------------------
*/
--}}

@extends('mail.mailTemplate')

@section('content')

<p style="margin:0 0 15px 0; font-size:14px; color:#333;">
    Hello,
</p>

<p style="margin:0 0 20px 0; font-size:14px; color:#333;">
    FYI, the expected return date for the accessory has expired on
    <strong>{{ $accessory['expected_checkin'] }}</strong>.
</p>

<p style="margin:0 0 20px 0; font-size:14px; color:#333;">
    Please review the accessory details below:
</p>

<table width="100%" cellpadding="0" cellspacing="0"
    style="border:1px solid #e5e7eb;border-radius:6px;border-collapse:collapse;background:#ffffff;">
    <tbody>

        <tr>
            <td
                style="width:220px;padding:12px;font-weight:600;border-right:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb;color:#111827;">
                Accessory Name:
            </td>
            <td style="padding:12px;border-bottom:1px solid #e5e7eb;color:#374151;">
                {{ $accessory['name'] }}
            </td>
        </tr>

        <tr>
            <td
                style="padding:12px;font-weight:600;border-right:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb;color:#111827;">
                Accessory Tag:
            </td>
            <td style="padding:12px;border-bottom:1px solid #e5e7eb;color:#374151;">
                {{ $accessory['acc_batch_no'] }}
            </td>
        </tr>

        <tr>
            <td style="padding:12px;font-weight:600;border-right:1px solid #e5e7eb;color:#111827;">
                Expected Return Date:
            </td>
            <td style="padding:12px;color:#374151;">
                {{ $accessory['expected_checkin'] }}
            </td>
        </tr>

    </tbody>

</table>

<p style="
    margin-top:25px;
    padding:15px;
    background:#fff5f5;
    border-left:4px solid #d72638;
    color:#1e293b;
    border-radius:6px;
">
    This accessory has exceeded its expected return date. Please take the necessary action to return the item as soon as
    possible.
</p>

<p style="margin-top:25px;color:#333;">
    Thank you,<br>
    {{ $site_name }}
</p>

@endsection
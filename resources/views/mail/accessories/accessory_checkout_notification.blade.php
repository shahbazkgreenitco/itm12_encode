{{-- 
/**
------------------------------------------------------------
File: accessory_checkout.blade.php
Module: Accessories
ACC/26/06
------------------------------------------------------------
Version: 1.0.0
Author: Safdar Ali
Page ID: #ACC-019
Created On: 2026-06-08
Reviewed By: -
------------------------------------------------------------
Change Log:
[1.0.0] - Initial version and set according to new mail template. - Safdar Ali - 2026-06-08
------------------------------------------------------------
*/
--}}

{{-- Accessory Checkout Notification Mail --}}

@extends('mail.mailTemplate')
@section('content')

<p style="margin:0 0 15px 0; font-size:14px; color:#333;">
    Hello {{ $user->getGuranteedNameText() }},
</p>

<p style="margin:0 0 20px 0; font-size:14px; color:#333;">
    A new Accessory item has been checked out to the {{ $target_chkout_to }} - {{ $target_assigned_name }}. Details are
    below:
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
                {{ $accessory->name }}
            </td>
        </tr>

        @if($accessory->category)
        <tr>
            <td
                style="padding:12px;font-weight:600;border-right:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb;color:#111827;">
                Category:
            </td>
            <td style="padding:12px;border-bottom:1px solid #e5e7eb;color:#374151;">
                {{ $accessory->category->name }}
            </td>
        </tr>
        @endif

        <tr>
            <td
                style="padding:12px;font-weight:600;border-right:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb;color:#111827;">
                Checked Out To:
            </td>
            <td style="padding:12px;border-bottom:1px solid #e5e7eb;color:#374151;">
                {{ $target_chkout_to }} - {{ $target_assigned_name }}
            </td>
        </tr>

        <tr>
            <td
                style="padding:12px;font-weight:600;border-right:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb;color:#111827;">
                Checkout Date:
            </td>
            <td style="padding:12px;border-bottom:1px solid #e5e7eb;color:#374151;">
                {{ CommonHelper::getDateAs($log->created_at, "d/m/Y", "Y-m-d H:i:s") }}
            </td>
        </tr>

        @if($log->note)
        <tr>
            <td
                style="padding:12px;font-weight:600;border-right:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb;color:#111827;">
                Additional Notes:
            </td>
            <td style="padding:12px;border-bottom:1px solid #e5e7eb;color:#374151;">
                {{ $log->note }}
            </td>
        </tr>
        @endif

    </tbody>

</table>

@if($accessory->requireAcceptance())

<p style="margin-top:20px;">
    <a href="{{ Config::get('app.url') }}accessory/accept_checkout/{{ $log->id }}"
        style="background:#156FF5;color:#ffffff;text-decoration:none;padding:12px 20px;border-radius:5px;display:inline-block;">
        Confirm Accessory Receipt
    </a>
</p>
@endif

<p style="margin-top:25px;color:#333;">
    Thank you,<br>
    @if($email_thankuby)
    {{ $email_thankuby }}<br>
    @endif
    {{ $site_name }}
</p>

@endsection
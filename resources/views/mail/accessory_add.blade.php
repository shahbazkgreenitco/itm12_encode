{{-- 
/**
------------------------------------------------------------
File: accessory_add.blade.php
Module: Accessories
ACC/26/06
------------------------------------------------------------
Version: 1.0.0
Author: Safdar Ali
Page ID: #ACC-017
Created On: 2026-06-08
Reviewed By: -

------------------------------------------------------------
Change Log:
[1.0.0] - Initial version and set according to new mail tempate. - Safdar Ali - 2026-06-08
------------------------------------------------------------
*/
--}}

@extends('mail.mailTemplate')
@section('content')

<p style="margin:0 0 15px 0; font-size:14px; color:#333;">
    Hello {{ $user->getGuranteedNameText() }},
</p>

<p style="margin:0 0 20px 0; font-size:14px; color:#333;">
    A new Accessory has been added. Details are below:
</p>

<table width="100%" cellpadding="0" cellspacing="0"
    style="border:1px solid #e5e7eb;border-radius:6px;border-collapse:collapse;background:#ffffff;">
    <tbody>

    <tr>
        <td style="width:220px;padding:12px;font-weight:600;border-right:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb;color:#111827;">
            Batch No:
        </td>
        <td style="padding:12px;border-bottom:1px solid #e5e7eb;color:#374151;">
            {{ $acc->batch_no }}
        </td>
    </tr>

    <tr>
        <td style="padding:12px;font-weight:600;border-right:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb;color:#111827;">
            Accessory Name:
        </td>
        <td style="padding:12px;border-bottom:1px solid #e5e7eb;color:#374151;">
            {{ $acc->name }}
        </td>
    </tr>

    @if($acc->category)
    <tr>
        <td style="padding:12px;font-weight:600;border-right:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb;color:#111827;">
            Category:
        </td>
        <td style="padding:12px;border-bottom:1px solid #e5e7eb;color:#374151;">
            {{ $acc->category->name }}
        </td>
    </tr>
    @endif

    <tr>
        <td style="padding:12px;font-weight:600;border-right:1px solid #e5e7eb;color:#111827;">
            Created Date:
        </td>
        <td style="padding:12px;color:#374151;">
            {{ CommonHelper::getDateAs($acc->created_at, "d/m/Y", "Y-m-d H:i:s") }}
        </td>
    </tr>

</tbody>

</table>

<p style="margin-top:25px;color:#333;">
    Thank you,<br>
    @if($email_thankuby)
        {{ $email_thankuby }}<br>
    @endif
    {{ $site_name }}
</p>

@endsection
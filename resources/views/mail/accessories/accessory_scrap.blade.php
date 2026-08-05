{{-- 
/**
------------------------------------------------------------
File: accessory_scrap.blade.php
Module: Accessories
ACC/26/06
------------------------------------------------------------
Version: 1.0.0
Author: Safdar Ali
Page ID: #ACC-022
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
    Hello User,
</p>
<p style="margin:0 0 20px 0; font-size:14px; color:#333;">
    Accessory has been scrap. Details are below:
</p>

<table width="100%" cellpadding="0" cellspacing="0"
    style="border:1px solid #e5e7eb;border-radius:6px;border-collapse:collapse;background:#ffffff;">
    <tbody>
        <tr>
            <td
                style="width:220px; padding:12px; font-weight:600; border-right:1px solid #e5e7eb; border-bottom:1px solid #e5e7eb; color:#111827;">
                Accessory Name:
            </td>
            <td style="padding:12px; border-bottom:1px solid #e5e7eb; color:#374151;">
                {{ \App\Models\Accessory::find($actionlog->accessory_id)->name }}
            </td>
        </tr>

        <tr>
            <td
                style="padding:12px; font-weight:600; border-right:1px solid #e5e7eb; border-bottom:1px solid #e5e7eb; color:#111827;">
                Batch No:
            </td>
            <td style=" padding:12px; border-bottom:1px solid #e5e7eb; color:#374151; ">
                A{{ \App\Models\Accessory::find($actionlog->accessory_id)->id }}
            </td>
        </tr>

        <tr>
            <td
                style="padding:12px; font-weight:600;  border-right:1px solid #e5e7eb; border-bottom:1px solid #e5e7eb; color:#111827; ">
                Scrap Time:
            </td>
            <td style="padding:12px;  border-bottom:1px solid #e5e7eb;  color:#374151;">
                {{ $actionlog->created_at }}
            </td>
        </tr>

        <tr>
            <td
                style="padding:12px;font-weight:600; border-right:1px solid #e5e7eb; border-bottom:1px solid #e5e7eb; color:#111827; ">
                Note:
            </td>
            <td style="padding:12px;border-right:1px solid #e5e7eb; border-bottom:1px solid #e5e7eb; color:#374151;">
                {{ $actionlog->note }}
            </td>
        </tr>

        @if (!empty($alertnotify))
        <tr>
            <td style="padding:12px; font-weight:600; vertical-align:top; color:#111827; ">
                Alert Recipients:
            </td>
            <td style="padding:12px; color:#374151; ">
                @foreach ($alertnotify as $email)
                <div>{{ $email }}</div>
                @endforeach
            </td>
        </tr>
        @endif

    </tbody>
</table>

<p style="margin-top:25px; color:#333;">
    Thank you,<br>
    {{ \App\Models\Settings::first()->site_name }}
</p>

@endsection
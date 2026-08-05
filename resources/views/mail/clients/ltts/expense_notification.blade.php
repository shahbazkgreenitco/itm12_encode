{{--
/**
* ------------------------------------------------------------
* File: expense_notification.blade.php
* Module: Device
* DEV/26/07
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #DEV-020
* Created On: 2026-14-07
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}
@extends('mail.ltts-mailTemplate')
@section('content')
<p>Hello {{ $user_name }} ,</p>

<p>The Expense details are saved with following details:</p>

<p>Device Tag: {{ $device->asset_tag }}</p>
<p>Expense Title: {{ $dm->title }}</p>
<p>Expense Type:
    @if ($dm->expense_type == 1)
        Maintenance
    @elseif ($dm->expense_type == 2)
        Repair
    @elseif ($dm->expense_type == 3)
        Upgrade
    @elseif ($dm->expense_type == 4)
        Miscellaneous
    @elseif ($dm->expense_type == 5)
        Audit
    @endif
</p>

@if($dm->cost && $dm->currency_format)
    <p>Expense Cost: {{ $dm->cost }} {{ $dm->currency_format }}</p>
@endif
<p>Expense Date: {{ CommonHelper::getDateAs($dm->expense_date, "d/m/Y", "Y-m-d") }}</p>
@if($dm->notes)
    <p>Additional Notes: {{ $dm->notes }}
@endif
<br/>

@if($site_name) 
    <p>Thanks,<br/>{{ $site_name }}</p>
@endif

@endsection

{{--
/**
* ------------------------------------------------------------
* File: threshouldNotification.blade.php
* Module: Consumables
* CON/26/06
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #CON-29
* Created On: 2026-04-06
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}

@extends('mail.tbsl-mailTemplate')
@section('content')
<p>Hello Admin,</p>

<p>A category {{ $catDetail->category_type }} availability is less than the threshold level.</p>
<p>
    <span style="font-weight: bold">Category Name:</span>
    <span>{{ $catDetail->name }}</span>
</p>
<p>
    <span style="font-weight: bold">Category Threshold:</span>
    <span>{{ $thresouldValue }}</span>
</p>
<p>
    <span style="font-weight: bold">Category Availability:</span>
    <span>{{ $deployableCatDeviceCount }}</span>
</p>
<p>Please keep the necessary number of item in stock.</p>

<p>Regards,</p>
<p>{{ App\Models\Settings::first()->site_name }}</p>
<p style="display: none !important;">Date: {{ date("m-d-Y H:i:s")}}</p>

@endsection

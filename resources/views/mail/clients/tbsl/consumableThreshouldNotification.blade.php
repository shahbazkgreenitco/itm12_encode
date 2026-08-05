{{--
/**
* ------------------------------------------------------------
* File: consumableThreshouldNotification.blade.php
* Module: Consumables
* CON/26/06
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #CON-27
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
<p>A Consumable {{ $consumableDetails->name }} availability is less than the threshold level.</p>
<p>
    <span style="font-weight: bold">Consumable Name:</span>
    <span>{{ $consumableDetails->name }}</span>
</p>
<p>
    <span style="font-weight: bold">Consumable Batch:</span>
    <span>CNS{{ $consumableDetails->id }}</span>
</p>
@if(!empty($consumableDetails->unique_tag))
<p>
    <span style="font-weight: bold">Unique Tag:</span>
    <span>{{ $consumableDetails->unique_tag }}</span>
</p>
@endif
<p>
    <span style="font-weight: bold">Consumable Quantity:</span>
    <span>{{ $consumableQuantity }}</span>
</p>
<p>
    <span style="font-weight: bold">Consumable Availability:</span>
    <span>{{ $availableSingleConsumables }}</span>
</p>
<p>
    <span style="font-weight: bold">Consumable Threshold:</span>
    <span>{{ $singleConsThreshold }}</span>
</p>
@if (!empty($consumableDetails->location_id) && isset($consumableDetails->location->name))
    <p>
        <span style="font-weight: bold">Consumable Location:</span>
        <span>{{ $consumableDetails->location->name }}</span>
    </p>
    <p>Please keep the necessary number of item in stock.</p>
@endif
<p>Regards,</p>
<p>{{ App\Models\Settings::first()->site_name }}</p>
<p style="display: none !important;">Date: {{ date("m-d-Y H:i:s")}}</p>

@endsection
{{--
/**
* ------------------------------------------------------------
* File: consumableCheckout.blade.php
* Module: Consumables
* CON/26/06
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #CON-24
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
<p>Hello {{ $assignedTo->getGuranteedNameText() }},</p>

<p>A new Consumable item has been checked out under your name. Below are the details:</p>

<p>Consumable Batch No: {{ "CNS".$consumable->id }}</p>
<p>Consumable Name: {{ $consumable->name }}</p>
@if($consumable->category)
<p>Category: {{ $consumable->category->name }}</p>
@endif
<p>Checkout For: 
    {{ $log->assigned_for == 1 ? 'User' : ($log->assigned_for == 2 ? 'Place' : ($log->assigned_for == 3 ? 'Device' : '-')) }}
</p>

<p>Checkout To: {{ $target_assigned_name }}</p>
@if(!empty($log->note))
<p>Note: {{ $log->note }}</p>
@endif
<p>Checkout Date: {{ CommonHelper::getDateAs($log->created_at, "d/m/Y", "Y-m-d H:i:s") }}</p>

@if ( $require_acceptance == 1 && $eula!='' )
<p>Please read the terms of use below, and click on the link at the bottom to confirm that you read and agree to the terms of use, and have received the consumable.</p>
@elseif ( $require_acceptance == 1 && $eula == '' )
<p>Please click on the link at the bottom to confirm that you have received the consumable.</p>
@elseif ( $require_acceptance == 0 && $eula != '' )
<p>Please read the terms of use below:</p>
@endif

<p><blockquote>{!! $eula !!}</blockquote></p>

@if($require_acceptance == 1)
<p><strong><a href="{{ url('/accept-consumable/'.$actionlog->id.'/') }}">I have read and agree to the terms of use, and have received this item.</a></strong></p>
@endif

@if($site_name) 
<p>
Thank you,
<br/>
@if($email_thankuby)
{{ $email_thankuby }} <br/> 
@endif
{{ $site_name }}
</p>
@endif

@endsection

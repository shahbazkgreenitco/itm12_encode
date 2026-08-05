{{--
/**
* ------------------------------------------------------------
* File: consumableCheckout.blade.php
* Module: Consumables
* CON/26/06
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #CON-22
* Created On: 2026-04-06
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}

<p>Hello {{ $assignedTo->getGuranteedNameText() }},</p>

<p>A Consumable product has been allocated to you. Below are the details:</p>

<p>Consumable Batch No: {{ "CNS".$consumable->id }}</p>
<p>Consumable Name: {{ $consumable->name }}</p>
@if($consumable->category)
<p>Category: {{ $consumable->category->name }}</p>
@endif
<p>Checkout For: 
    {{ $log->assigned_for == 1 ? 'User' : ($log->assigned_for == 2 ? 'Place' : ($log->assigned_for == 3 ? 'Device' : '-')) }}
</p>

<p>Checkout To: {{ $target_assigned_name }}</p>

<p>Checkout Date: {{ CommonHelper::getDateAs($log->created_at, "d/m/Y", "Y-m-d H:i:s") }}</p>

<p>Please read the terms of use below:</p>
<p>The allocated Asset is a property of Cactus Communications and should be used only for official purpose. Acceptance of this asset means you are accepting the terms and conditions laid down by Cactus Communications and it’s IT Policy. This Asset needs to be returned back to IT Team on departure.</p>

<p>For any clarifications please contact IT Support</p>


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
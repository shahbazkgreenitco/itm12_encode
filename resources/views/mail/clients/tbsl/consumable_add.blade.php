{{--
/**
* ------------------------------------------------------------
* File: consumable_add.blade.php
* Module: Consumables
* CON/26/06
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #CON-28
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
<p>Hello {{ $user->getGuranteedNameText() }},</p>

<p>A new Consumable item has been added. Below are the details:</p>

<p>Batch No:<span>CNS</span> {{ $objConsumable->id }}</p>
<p>Consumable Name: {{ $objConsumable->name }}</p>
@if($objConsumable->category)
<p>Category: {{ $objConsumable->category->name }}</p>
@endif
<p>Created Date: {{ CommonHelper::getDateAs($objConsumable->created_at, "d/m/Y", "Y-m-d H:i:s") }}</p>

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

{{--
/**
* ------------------------------------------------------------
* File: consumableScrap.blade.php
* Module: Consumables
* CON/26/06
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #CON-26
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
<p>Hello {{ $log->note ?? 'User' }},</p>

<p>One consumable has been scrap. Details are below:</p>

<table>
	<tr>
		<td style="background-color:#ccc">Consumable Name:</td>
		<td><strong>{{ \App\Models\Consumable::find($actionlog->consumable_id)->name }}</strong></td>
	</tr>
    <tr>
		<td style="background-color:#ccc">Batch No:</td>
		<td><strong>CNS{{ \App\Models\Consumable::find($actionlog->consumable_id)->id }}</strong></td>
	</tr>
	<tr>
		<td style="background-color:#ccc">Scrap Time:</td>
		<td><strong>{{ $actionlog->created_at }}</strong></td>
	</tr>
    <tr>
        <td style="background-color:#ccc">Note:</td>
        <td><strong>{{ $actionlog->note }}</strong></td>
    </tr>
    @if (!empty($alertnotify))
    <tr>
        <td style="background-color:#ccc">Alert Recipients:</td>
        <td>
            @foreach ($alertnotify as $email)
                <div>{{ $email }}</div>
            @endforeach
        </td>
    </tr>
    @endif
</table>

<p>Thank you,<br/>{{ \App\Models\Settings::first()->site_name }}</p>
@endsection
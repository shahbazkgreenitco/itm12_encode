{{--
/**
* ------------------------------------------------------------
* File: consumableCheckin.blade.php
* Module: Consumables
* CON/26/06
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #CON-25
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
{{--  @extends('layouts.layout1')  --}}

{{--  @section('title', 'Consumable Checkout Confirmation')  --}}

{{--  @section  --}}
<p>Hello {{ App\Models\User::find($actionlog->checkedout_to)->first_name }},</p>


<p>One consumable is revoked from you, details are below. 

<table>
	<tr>
		<td style="background-color:#ccc">
		   Consumable Name:
		</td>
		<td>
			<strong>{{ App\Models\Consumable::where('id','=',$actionlog->consumable_id)->first()->name }}</strong>
		</td>
	</tr>
	<tr>
		<td style="background-color:#ccc">
			Checkin Time :
		</td>
		<td>
			<strong>{{ $actionlog->created_at }}</strong>
		</td>
	</tr>

</table>
<p>Thank you,<br/>{{ App\Models\Settings::first()->site_name }}</p>

{{--  @endsection  --}}
@endsection

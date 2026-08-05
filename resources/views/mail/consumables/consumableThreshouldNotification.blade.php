{{--
/**
* ------------------------------------------------------------
* File: consumableThreshouldNotification.blade.php
* Module: Consumables
* CON/26/06
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #CON-33
* Created On: 2026-04-06
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version
* [1.0.1] - Changes for the new mail template changes
* ------------------------------------------------------------
*/
--}}

@extends('mail.mailTemplate')
@section('content')
    <p class="email-text">Hello Admin,</p>
    <p class="email-text mb-medium">A Consumable {{ $consumableDetails->name }} availability is less than the threshold level.</p>
    <p class="email-text mb-medium">This is to inform you that the Consumable {{ $consumableDetails->name }} has reached the defined threshold limit as of {{ date("m-d-Y")}}</p>

    <table class="info-table mb-medium">
        <tbody>
            <tr>
                <td class="label-column">Consumable Name:</td>
                <td class="value-column">{{ $consumableDetails->name }}</td>
            </tr>
            <tr>
                <td class="label-column">Consumable Batch:</td>
                <td class="value-column">
                    @if (config("app.client") == "etherealmachines")
                        <span>CN{{ $consumableDetails->id }}</span>
                    @else
                        <span>CNS{{ $consumableDetails->id }}</span>
                    @endif
                </td>
            </tr>
            @if(!empty($consumableDetails->unique_tag))
                <tr>
                    <td class="label-column">Unique Tag:</td>
                    <td class="value-column">{{ $consumableDetails->unique_tag }}</td>
                </tr>
            @endif
            <tr>
                <td class="label-column">Consumable Quantity:</td>
                <td class="value-column">{{ $consumableQuantity }}</td>
            </tr>
            <tr>
                <td class="label-column">Consumable Availability:</td>
                <td class="value-column">{{ $availableSingleConsumables }}</td>
            </tr>
            <tr>
                <td class="label-column">Consumable Threshold:</td>
                <td class="value-column">{{ $singleConsThreshold }}</td>
            </tr>
            @if (!empty($consumableDetails->location_id) && isset($consumableDetails->location->name))
                <tr>
                    <td class="label-column">Consumable Location:</td>
                    <td class="value-column">{{ $consumableDetails->location->name }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <p class="email-text">Please review the current usage and keep the necessary number of item in stock.</p>

    @if(isset($site_name))
        <p class="email-footer mt-large">
            Thank you,<br>

            @if(isset($email_thankuby))
                {{ $email_thankuby }}<br>
            @endif

            {{ $site_name }}
        </p>
    @endif
@endsection
{{--
/**
* ------------------------------------------------------------
* File: threshouldNotification.blade.php
* Module: Consumables
* CON/26/06
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #CON-36
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
    <p class="email-text mb-medium">A category {{ $catDetail->category_type }} availability is less than the threshold level.</p>

    <table class="info-table mb-medium">
        <tbody>
            <tr>
                <td class="label-column">Category Name:</td>
                <td class="value-column">{{ $catDetail->name }}</td>
            </tr>
            <tr>
                <td class="label-column">Category Threshold:</td>
                <td class="value-column">{{ $thresouldValue }}</td>
            </tr>
            <tr>
                <td class="label-column">Category Availability:</td>
                <td class="value-column">{{ $deployableCatDeviceCount }}</td>
            </tr>
        </tbody>
    </table>

    <p class="email-text">Please keep the necessary number of item in stock.</p>

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

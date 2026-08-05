{{--
/**
* ------------------------------------------------------------
* File: expense_notification.blade.php
* Module: Device
* DEV/26/07
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #DEV-021
* Created On: 2026-14-07
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}

@extends('mail.mailTemplate')
@section('content')
    <p class="email-text">Hello {{ $user_name }},</p>

    <p class="email-text mb-medium">The Expense details are saved with following details:</p>

    <table class="info-table mb-medium">
        <tbody>
            <tr>
                <td class="label-column">Device Tag:</td>
                <td class="value-column">{{ $device->asset_tag }}</td>
            </tr>
            <tr>
                <td class="label-column">Expense Title:</td>
                <td class="value-column">{{ $dm->title }}</td>
            </tr>
            <tr>
                <td class="label-column">Expense Type:</td>
                <td class="value-column">
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
                </td>
            </tr>
            @if($dm->cost && $dm->currency_format)
                <tr>
                    <td class="label-column">Expense Cost:</td>
                    <td class="value-column">{{ $dm->cost }} {{ $dm->currency_format }}</td>
                </tr>
            @endif
            <tr>
                <td class="label-column">Expense Date:</td>
                <td class="value-column">{{ CommonHelper::displayDateTime($dm->expense_date, "date", "display") }}</td>
            </tr>
            @if($dm->notes)
                <tr>
                    <td class="label-column">Additional Notes:</td>
                    <td class="value-column">{{ $dm->notes }}</td>
                </tr>
            @endif
        </tbody>
    </table>

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

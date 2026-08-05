{{--
/**
* ------------------------------------------------------------
* File: consumableCheckout.blade.php
* Module: Consumables
* CON/26/06
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #CON-30
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
    <p class="email-text">
        Hello {{ $assignedTo->getGuranteedNameText() }},
    </p>

    <p class="email-text mb-medium">
        A new consumable item has been checked out under your name. Below are the details:
    </p>

    <table class="info-table mb-medium">
        <tbody>
            <tr>
                <td class="label-column">
                    Consumable Batch No:
                </td>
                <td class="value-column">
                    @if (config("app.client") == "etherealmachines")
                        <p>{{ "CN".$consumable->id }}</p>
                    @else
                        <p>{{ "CNS".$consumable->id }}</p>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label-column">
                    Consumable Name:
                </td>
                <td class="value-column">
                    {{ $consumable->name }}
                </td>
            </tr>
            @if($consumable->category)
                <tr>
                    <td class="label-column">
                        Category:
                    </td>
                    <td class="value-column">
                        {{ $consumable->category->name }}
                    </td>
                </tr>
            @endif
            <tr>
                <td class="label-column">
                    Checked Out To:
                </td>
                <td class="value-column">
                    {{ $log->assigned_for == 1 ? 'User' : ($log->assigned_for == 2 ? 'Place' : ($log->assigned_for == 3 ? 'Device' : '-')) }} - {{ $target_assigned_name }}
                </td>
            </tr>
            <tr>
                <td class="label-column">
                    Checkout Date:
                </td>
                <td class="value-column">
                    {{ CommonHelper::getDateAs($log->created_at, "d/m/Y", "Y-m-d H:i:s") }}
                </td>
            </tr>
            @if($log->note)
                <tr>
                    <td class="label-column">
                        Additional Notes:
                    </td>
                    <td class="value-column">
                        {{ $log->note }}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    @if ( $require_acceptance == 1 && $eula!='' )
        <p class="email-text">Please read the terms of use below, and click on the link at the bottom to confirm that you read and agree to the terms of use, and have received the consumable.</p>
    @elseif ( $require_acceptance == 1 && $eula == '' )
        <p class="email-text">Please click on the link at the bottom to confirm that you have received the consumable.</p>
    @elseif ( $require_acceptance == 0 && $eula != '' && $consumable->showEula() == 1 )
        <p class="email-text">Please read the terms of use below:</p>
    @endif

    @if($require_acceptance == 1)
        <p class="mt-medium">
            <a href="{{ url('/accept-consumable/'.$actionlog->id.'/') }}" class="btn-primary">
                I have read and agree to the terms of use, and have received this item.
            </a>
        </p>
    @endif

    @if(isset($site_name)) 
        <p class="email-footer mt-large">
            Thank you,<br>
            @if($email_thankuby)
                {{ $email_thankuby }}<br>
            @endif
            {{ $site_name }}
        </p>
    @endif
@endsection

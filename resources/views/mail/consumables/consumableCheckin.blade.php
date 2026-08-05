{{--
/**
* ------------------------------------------------------------
* File: consumableCheckin.blade.php
* Module: Consumables
* CON/26/06
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #CON-31
* Created On: 2026-04-29
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}

@extends('mail.mailTemplate')

@section('content')s
    <p class="email-text">Hello {{ $actionlog->note ?? 'User' }},</p>
    <p class="email-text mb-medium">One consumable has been revoked. Details are below:</p>

    <table class="info-table mb-medium">
        <tbody>
            <tr>
                <td class="label-column">
                    Consumable Name:
                </td>
                <td class="value-column">
                    {{ \App\Models\Consumable::find($actionlog->consumable_id)->name }}
                </td>
            </tr>
            <tr>
                <td class="label-column">
                    Checkin Time:
                </td>
                <td class="value-column">
                    {{ $actionlog->created_at }}
                </td>
            </tr>
            <tr>
                <td class="label-column">
                    Revoked From:
                </td>
                <td class="value-column">
                    {{ $actionlog->note }}
                </td>
            </tr>
            @if(!empty($alertnotify))
                <tr>
                    <td class="label-column">Alert Recipients:</td>
                    <td class="value-column">
                        @foreach ($alertnotify as $email)
                            <div>{{ $email }}</div>
                        @endforeach
                    </td>
                </tr>
            @endif
            <tr>
                <td class="label-column">
                    Checkout Date:
                </td>
                <td class="value-column">
                    {{ CommonHelper::getDateAs($actionlog->created_at, "d/m/Y", "Y-m-d H:i:s") }}
                </td>
            </tr>
        </tbody>
    </table>

    @php
        $settings = \App\Models\Settings::first();
    @endphp

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
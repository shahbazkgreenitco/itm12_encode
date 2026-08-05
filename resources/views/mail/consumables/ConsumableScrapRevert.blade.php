{{--
/**
* ------------------------------------------------------------
* File: ConsumableScrapRevert.blade.php
* Module: Consumables
* CON/26/06
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #CON-21
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
    <p class="email-text">Hello User,</p>
    <p class="email-text mb-medium">Consumable has been revert scrap. Details are below:</p>

    <table class="info-table mb-medium">
        <tbody>
            <tr>
                <td>Consumable Name:</td>
                <td>{{ $consumable->name }}</td>
            </tr>
            <tr>
                <td>Batch No:</td>
                <td>
                    @if (config("app.client") == "etherealmachines")
                        <p>{{ "CN".$consumable->id }}</p>
                    @else
                        <p>{{ "CNS".$consumable->id }}</p>
                    @endif
                </td>
            </tr>
            <tr>
                <td>Scrap Revert Time:</td>
                <td>{{ $actionlog->created_at }}</td>
            </tr>
            <tr>
                <td>Note:</td>
                <td>{{ $actionlog->note }}</td>
            </tr>
            @if (!empty($alertnotify))
                <tr>
                    <td>Alert Recipients:</td>
                    <td>
                        @foreach ($alertnotify as $email)
                            <div>{{ $email }}</div>
                        @endforeach
                    </td>
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
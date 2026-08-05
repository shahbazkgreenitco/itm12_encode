{{--
/**
* ------------------------------------------------------------
* File: consumable_add.blade.php
* Module: Consumables
* CON/26/06
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #CON-34
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
    <p class="email-text">Hello {{ $user->getGuranteedNameText() }},</p>
    <p class="email-text mb-medium">A new Consumable item has been added. Below are the details:</p>

    <table class="info-table mb-medium">
        <tbody>
            <tr>
                <td class="label-column">Consumable Name:</td>
                <td class="value-column">{{ $objConsumable->name }}</td>
            </tr>
            <tr>
                <td class="label-column">Batch No:</td>
                <td class="value-column">
                     @if (config("app.client") == "etherealmachines")
                        <p>{{ "CN".$objConsumable->id }}</p>
                    @else
                        <p>{{ "CNS".$objConsumable->id }}</p>
                    @endif
                </td>
            </tr>
            @if($objConsumable->category)
                <tr>
                    <td class="label-column">Category:</td>
                    <td class="value-column">{{ $objConsumable->category->name }}</td>
                </tr>
            @endif
            <tr>
                <td class="label-column">Created Date:</td>
                <td class="value-column">{{ CommonHelper::getDateAs($objConsumable->created_at, "d/m/Y", "Y-m-d H:i:s") }}</td>
            </tr>
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
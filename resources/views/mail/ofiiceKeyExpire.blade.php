@extends('mail.mailTemplate')
@section('content')
    <p>Hello,</p>
    @if ($days === 0)
        <p>Your secret key expires today ({{ config('services.azure.office365_expire_date') }}).</p>
    @elseif ($days < 0)
        <p>Your secret key expired on {{ config('services.azure.office365_expire_date') }}.</p>
    @else
        <p>Your secret key will expire in {{ $days }} days.</p>
    @endif
    <p>Please renew the secret key as soon as possible to continue uninterrupted access.</p>

    @if ($site_name)
        <p>
            Thank you,
            <br />
            @if ($email_thankuby)
                {{ $email_thankuby }} <br />
            @endif
            {{ $site_name }}
        </p>
    @endif
@endsection

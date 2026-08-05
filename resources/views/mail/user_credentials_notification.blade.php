@extends('mail.mailTemplate')
@section('content')
<p>@if($user) Hello {{ $user->first_name }}, @else Hello, @endif </p>

<p>Your IT Asset portal account credentials are below,</p>
<p><strong>URL:</strong> &nbsp; <span style="color:blue">{{ Config::get("app.url") }}</span></p>
<p><strong>Username:</strong> {{ $user->username }}</p>
<p><strong>Password:</strong> {{ $password }}</p>
@if(config('aap.client') == "smifs")
<p>Please login and change your password after first login.</p>
@endif
@if($site_name) 
<p>Thanks,<br/>{{ $site_name }}</p>
@else
<p>Thanks</p>
@endif
@endsection

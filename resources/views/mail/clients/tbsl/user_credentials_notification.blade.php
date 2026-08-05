@extends('mail.tbsl-mailTemplate')
@section('content')
<p>@if($user) Hello {{ $user->first_name }}, @else Hello, @endif </p>

<p>Your IT Asset portal account credentials are below,</p>
<p><strong>URL:</strong> {{ Config::get("app.url") }}</p>
<p><strong>Username:</strong> {{ $user->username }}</p>
<p><strong>Password:</strong> {{ $password }}</p>

@if($site_name) 
<p>Thanks,<br/>{{ $site_name }}</p>
@else
<p>Thanks</p>
@endif
@endsection

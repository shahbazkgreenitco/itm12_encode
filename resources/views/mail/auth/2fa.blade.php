@extends('mail.mailTemplate')
@section('content')
<p>Hello {{ $user->first_name }},</p>

<p>The code for login to your portal is : {{$token}}</p>
@if(!empty($impersonateRequestEmail))
  <p>The code was requested by the user: {{ $impersonateRequestEmail }}</p>
@endif

@if($site_name) 
<p>Thanks,<br/>{{ $site_name }}</p>
@endif
@endsection

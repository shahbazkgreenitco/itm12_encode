@extends('mail.mailTemplate')
@section('content')
<p>Hello {{ $user->first_name }},</p>

<p>This is notification mail regarding your account password has been updated successfully.</p>

@if($site_name) 
<p>Thanks,<br/>{{ $site_name }}</p>
@endif
@endsection

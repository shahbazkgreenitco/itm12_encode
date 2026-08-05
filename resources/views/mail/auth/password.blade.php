@extends('mail.mailTemplate')
@section('content')
<p>Hello {{ $user->first_name }},</p>
<?php $link = url('password/reset') . "/" . $token . "/" . $reset_id; ?>
<p>We have send this mail to reset the account login password. Please click here to reset password:</p>
<p><a href="{{ $link }}"> Reset Password </a></p>
<p>(or)</p>
<p>Copy &amp; Paste the below link on browser</p>
<p>{{ $link }}</p>
<p>(or)</p>
<p>Use the code below from ITAM mobile app</p>
<p>{{ $short_token }}</p>

@if($site_name) 
<p>Thanks,<br/>{{ $site_name }}</p>
@endif

@endsection

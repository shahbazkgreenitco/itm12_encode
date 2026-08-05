@extends('mail.mailTemplate')
@section('content')
<p>Hello,</p>
<p>Please find below the attached report for your reference.</p>
@if(isset($dataElement) &&  $dataElement['dateRange'])
  <p>This report covers data from<strong> {{ $dataElement['dateRange'] }}</strong>.</p>
@endif


<p>Thank You</p>
@endsection

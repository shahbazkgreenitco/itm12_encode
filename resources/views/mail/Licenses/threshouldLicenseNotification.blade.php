@extends('mail.mailTemplate')
@section('content')
<p>Hello Admin,</p>

<p>A category {{ $catDetail->category_type }} availability is less than the threshold level.</p>
<p>
    <span style="font-weight: bold">Category Name:</span>
    <span>{{ $catDetail->name }}</span>
</p>
<p>
    <span style="font-weight: bold">Category Threshold:</span>
    <span>{{ $thresouldValue }}</span>
</p>
<p>
    <span style="font-weight: bold">Category Availability:</span>
    <span>{{ $catLicenseCount }}</span>
</p>
<p>Please keep the necessary number of items in stock.</p>

<p>Regards,</p>
<p>{{ App\Models\Settings::first()->site_name }}</p>
<p style="display: none !important;">Date: {{ date("m-d-Y H:i:s")}}</p>

@endsection
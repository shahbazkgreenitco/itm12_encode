@extends('mail.mailTemplate')
@section('content')
<p>Hello @if($user) {{ $user->getGuranteedNameText(true) }}, @endif </p>

<p>You have been assigned a problem.</p>
<p>Carefully view the details, get it resolved within the timeline.</p>
<p>You can refer to the details, </p>

<p>Subject: <b>{{ $problem->decodedSubject() }}</b></p>
<p>Department: <b>{{ $problem->department->name }}</b></p>
<p>Category: @if($problem->problem_category_id) <b>{{ $problem->problemCategory->name }}</b></p>  @endif</p>
<p>Sub Category: @if($problem->sub_category_id) <b>{{ $problem->subCategory->name }}</b></p>  @endif</p>
<p>Priority: @if($problem->priority) <b> {{ $problem->priority->name }}</b> @endif</p> 

@if($user)
<p>You can access the problem on below link</p>
<p><b><a href="{{ Config::get("app.url") }}problem_management/view/1/{{ $problem->id }}"><u>Click Here</u></a></b></p>
@endif

@if($site_name)
<p>Thanks,<br/>{{ $site_name }}</p>
@else
<p>Thanks</p>
@endif
@endsection

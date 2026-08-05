@extends('mail.mailTemplate')

@section('content')
    <p>Hello User,</p>

    <h4>Kanban Board Deleted</h4>
    <p>The board <strong>{{ $boardName }}</strong> has been deleted by <strong>{{ $deletedBy }}</strong>.</p>

    <p>If you were a member of this board, you no longer have access to its tasks.</p>

    @if($site_name)
    <p>Thanks,<br/>{{ $site_name }}</p>
    @else
    <p>Thanks</p>
    @endif

@endsection
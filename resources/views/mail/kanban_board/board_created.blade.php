@extends('mail.mailTemplate')
@section('content')
    <p>@if($creator) Hello {{ $creator->fullName() }}, @else Hello, @endif </p>

    <h4>Kanban Board Created</h4>
    <p>A new Kanban board <strong>{{ $board->name }}</strong> has been created.</p>
    <p>Created by: {{ $creator->fullName() }}</p>
    <p>
        Board Type: 
        @if($board->board_items_type == 1)
            Ticket
        @elseif($board->board_items_type == 2)
            Custom
        @else
            Unknown
        @endif
    </p>

    <p>You can access the board on below link</p>
    <p><b><a href='{{ Config::get("app.url") }}/tickets/kanban-board/boards'><u>Click Here</u></a></b></p>

    @if($site_name)
    <p>Thanks,<br/>{{ $site_name }}</p>
    @else
    <p>Thanks</p>
    @endif
@endsection
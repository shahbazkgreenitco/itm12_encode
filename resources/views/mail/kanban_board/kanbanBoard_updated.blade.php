@extends('mail.mailTemplate')
@section('content')
     <p>@if($board->creator->fullName()) Hello {{ $board->creator->fullName() }}, @else Hello, @endif </p>

    <p>Kanban Board <strong>{{ $board->name }}</strong> has been updated successfully.</p>
    <p>KanBan Board updated by: {{ $board_updated_by->fullName() }}</p>
    <p>Board Name: {{ $board->name }}</p>
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

    <p>You can access the kanban board on below link</p>
    <p><b><a href='{{ Config::get("app.url") }}/tickets/kanban-board/boards'><u>Click Here</u></a></b></p>

    @if($site_name)
    <p>Thanks,<br/>{{ $site_name }}</p>
    @else
    <p>Thanks</p>
    @endif 
@endsection
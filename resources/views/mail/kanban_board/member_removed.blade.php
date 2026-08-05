@extends('mail.mailTemplate')

@section('content')
    <p>
        @if($member) 
            Hello {{ $member->fullName() }}, 
        @else 
            Hello, 
        @endif 
    </p>

    <h4>Removed from Kanban Board</h4>
    <p>You have been removed from the Kanban board <strong>{{ $board->name }}</strong>.</p>

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

    @if($site_name)
        <p>Thanks,<br/>{{ $site_name }}</p>
    @else
        <p>Thanks</p>
    @endif
@endsection

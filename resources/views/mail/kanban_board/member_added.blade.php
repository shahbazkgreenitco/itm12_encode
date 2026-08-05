@extends('mail.mailTemplate')

@section('content')
    <p>
        Hello User, 
    </p>

    <h4>Added to Kanban Board</h4>
    <p>You have been added to the Kanban board <strong>{{ $board->name }}</strong>.</p>

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

    <p>You can access the board at the link below:</p>
    <p>
        <b>
            <a href="{{ Config::get('app.url') }}/tickets/kanban-board/boards">
                <u>Click Here</u>
            </a>
        </b>
    </p>

    @if($site_name)
        <p>Thanks,<br/>{{ $site_name }}</p>
    @else
        <p>Thanks</p>
    @endif
@endsection

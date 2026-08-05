@extends('mail.mailTemplate')

@section('content')
    <p>
        Hello User,
    </p>

    <h4>New Card Assigned to You</h4>
    <p>
        You have been assigned to the card on the Kanban board <strong>{{ $board->name }}</strong>.
    </p>

    @if(!empty($card_updated_by))
        <p>Card updated by: {{ $card_updated_by }}</p>
    @endif

    <p>
        <b>Title: </b>{{ $boardItem->title }}
    </p>

    @if(!empty($boardItem->description))
        <p>
            <b>Description: </b>{!! $boardItem->description !!}
        </p>
    @endif

    @if(!empty($boardItem->statusData->name))
        <p>
            <b>Status: </b>{{ $boardItem->statusData->name }}
        </p>
    @endif

    @if(!empty($boardItem->priorityData->name))
        <p>
            <b>Priority: </b>{{ $boardItem->priorityData->name }}
        </p>
    @endif

    @if(!empty($boardItem->expected_date))
        <p>
            <b>Expected Date: </b>{{ $boardItem->expected_date }}
        </p>
    @endif

    @if(!empty($boardItem->ticket_id))
        <p>
            <b>Reference Ticket: </b>#{{ $boardItem->ticket_id }}
        </p>
    @endif

    @if(!empty($assignedUserNames))
        <p>
            <b>Assigned To: </b>{{ $assignedUserNames }}
        </p>
    @endif

    <p>You can access the kanban card on below link</p>
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

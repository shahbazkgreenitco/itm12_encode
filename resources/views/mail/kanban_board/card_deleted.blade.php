@extends('mail.mailTemplate')

@section('content')
    <p>Hello User,</p>
    <p>
        The card <strong>{{ $cardTitle }}</strong> has been deleted from the Kanban board <strong>{{ $board->name }}</strong>.
    </p>

    @if(!empty($card_updated_by))
        <p>Card updated by: {{ $card_updated_by }}</p>
    @endif

    @if(!empty($boardItem->kanbanBoardItem->item_name))
        <p>Board Item Name: {{ $boardItem->kanbanBoardItem->item_name }}</p>
    @endif

    @if(!empty($boardItem->title))
        <p>Title: {{ $boardItem->title }}</p>
    @endif

    @if(!empty($boardItem->statusData->name))
        <p>Status: {{ $boardItem->statusData->name }}</p>
    @endif

    @if(!empty($boardItem->priorityData->name))
        <p>Priority: {{ $boardItem->priorityData->name }}</p>
    @endif

    @if(!empty($assignedToUsers))
        <p>Assigned To: {{ $assignedToUsers }}</p>
    @endif

    @if(!empty($boardItem->referenceTicket))
        <p>Reference Ticket:
            <a href="{{ url('ticket/' . $boardItem->referenceTicket->id) }}" target="_blank">
                #{{ $boardItem->referenceTicket->id }}
            </a>
        </p>
    @endif

    @if(!empty($boardItem->expected_date))
        <p>Expected Date: {{ $boardItem->expected_date }}</p>
    @endif

    @if(!empty($boardItem->created_at))
        <p>Created At: {{ $boardItem->created_at }}</p>
    @endif


    @if($site_name)
        <p>Thanks,<br/>{{ $site_name }}</p>
    @else
        <p>Thanks</p>
    @endif
@endsection

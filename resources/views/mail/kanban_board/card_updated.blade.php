@extends('mail.mailTemplate')
@section('content')
    <p>Hello,</p>
    
    @if($type === 'auto' && $boardItem->task_type == 2 && $boardItem->title)
        <p>Kanban Card <strong>{{ $boardItem->title }} </strong> has been closed by system</p>
    @elseif($type === 'archive' && $boardItem->task_type == 2 && $boardItem->title)
        <p>Kanban Card <strong> {{ $boardItem->title }} </strong> has been archived.</p>
    @elseif($boardItem->task_type == 2 && !empty($boardItem->title))
        <p>Kanban card <strong>{{ $boardItem->title }}</strong> has been updated successfully.</p>
    @else
        <p>Kanban card has been updated successfully.</p>
    @endif

    @if(!empty($type) && $type == 'auto')
        <p>Card closed by: System</p>
    @elseif(!empty($card_updated_by))
        <p>Card updated by: {{ $card_updated_by  }}</p>
    @endif

    @if(!empty($board->name))
        <p>Board Name: {{ $board->name }}</p>
    @endif

    @if($boardItem->task_type == 1)
        <p>Board Type: Ticket</p>

        @if(!empty($boardItem->kanbanBoardItem->item_name))
            <p>Board Item Name: {{ $boardItem->kanbanBoardItem->item_name }}</p>
        @endif

        @if(!empty($boardItem->ticket_id))
            <p>Ticket Id:
                <a href="{{ url('ticket/' . $boardItem->ticket_id) }}" target="_blank">#{{ $boardItem->ticket_id }}</a>
            </p>
        @endif

    @elseif($boardItem->task_type == 2)
        <p>Board Type: Custom</p>

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
    @endif

    <p>You can access the kanban card on below link</p>
    <p>
        <b>
            <a href='{{ Config::get("app.url") }}tickets/kanban-board/boards'>
                <u>Click Here</u>
            </a>
        </b>
    </p>

    @if(!empty($site_name))
        <p>Thanks,<br/>{{ $site_name }}</p>
    @else
        <p>Thanks</p>
    @endif 
@endsection

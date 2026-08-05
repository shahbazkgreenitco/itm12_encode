@extends('mail.mailTemplate')
@section('content')
    <p>Hello,</p>

    @if($boardItem->task_type == 2 && !empty($boardItem->title))
        <p>Kanban card <strong>{{ $boardItem->title }}</strong> {{ $hour && $hour >= 1 ? 'is about to overdue.' :'has been overdue.' }}</p>
    @else
        <p>Kanban card {{ $hour && $hour >= 1 ? 'is about to overdue' :'has been overdue.' }}</p>
    @endif

    @if (isset($hour) && !empty($hour))
        <p>OverDue In: {{ $hour }} {{ $hour > 1 ? 'Hours' : 'Hour'}}</p>
    @endif  

    @if(!empty($board->name))
        <p>Board Name: {{ $board->name }}</p>
    @endif

    <p>Board Type: Custom</p>

    @if(!empty($boardItem->kanbanBoardItem->item_name))
        <p>Board Item Name: {{ $boardItem->kanbanBoardItem->item_name }}</p>
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
        <p>
            Reference Ticket:
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

    @if(!empty($site_name))
        <p>Thanks,<br/>{{ $site_name }}</p>
    @else
        <p>Thanks</p>
    @endif 
@endsection

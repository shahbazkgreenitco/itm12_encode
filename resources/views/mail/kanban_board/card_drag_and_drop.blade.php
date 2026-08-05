@extends('mail.mailTemplate')
@section('content')
    <p>Hello User,</p>

   @if (!empty($groupById))
        @if ($groupById == 1)
            <p>Kanban Card <b>#{{ $card->id }}</b> {{ $card->title }}'s Status changed successfully.</p>
        @elseif($groupById == 2)
            <p>Kanban Card <b>#{{ $card->id }}</b> {{ $card->title }}'s Priority changed successfully.</p>
        @elseif($groupById == 3)
             <p>Kanban Card <b>#{{ $card->id }}</b> {{ $card->title }}'s assigned user changed successfully.</p>
        @else
            <p>Kanban Card <b>#{{ $card->id }}</b> {{ $card->title }}'s moved successfully.</p>
        @endif
    @else
        <p>Kanban Card <b>#{{ $card->id }}</b> {{ $card->title }} moved from <b>{{ $pre_sprint_name }}</b> to <b>{{ $curr_sprint_name }}</b></p>
    @endif

    @if(!empty($card_updated_by))
        <p>Card updated by: {{ $card_updated_by }}</p>
    @endif
    
    @if(!empty($card->title))
        <p>Card Name: {{ $card->title }}</p>
    @endif

    @if(!empty($card->creator))
        <p>Created by: {{ $card->creator->username }}</p>
    @endif

    <p>Card Type: Custom</p>

    @if(!empty($card->statusData->name))
        <p>Status:{{ $card->statusData->name }}</p>
    @endif

    @if(!empty($card->priorityData->name))
        <p>Priority:{{ $card->priorityData->name }}
        </p>
    @endif

    @if (!empty($board['name']))
        <p>Board Name: {{$board['name']}}</p>
    @endif

    @if(!empty($card_assignedTo))
        <p>Card Assigned To: {{ $card_assignedTo->pluck('username')->implode(', ') }}</p>
    @endif

     @if(!empty($card['ticket_id']))
        <p>Reference Ticket:
            <a href="{{ url('ticket/' . $card['ticket_id']) }}" target="_blank">
                #{{  $card['ticket_id'] }}
            </a>
        </p>
    @endif

    @if(!empty($card->expected_date))
        <p>Expected Date: {{ $card->expected_date }}</p>
    @endif

    @if(!empty($card->created_at))
        <p>Created At: {{ $card->created_at }}</p>
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

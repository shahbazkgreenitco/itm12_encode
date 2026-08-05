@extends('mail.mailTemplate')

@section('content')

    <p>Hello User,</p>

    <p>The board item <strong>{{ $originalItem->item_name }}</strong> has been cloned successfully.</p>

    @if(!empty($originalItem->item_name))
        <p>
            <strong>Original Sprint:</strong> {{ $originalItem->item_name }}
        </p>
    @endif

    @if(!empty($newItem->item_name))
        <p>
            <strong>New Sprint:</strong> {{ $newItem->item_name }}
        </p>
    @endif

    @if(!empty($clonedBy->fullName()))
        <p>
            <strong>Cloned By:</strong> {{ $clonedBy->fullName() }}
        </p>
    @endif

    @if(!empty(now()))
        <p>
            <strong>Date:</strong> {{ now()->format('d-m-Y H:i A') }}
        </p>
    @endif

    <p>You can access the kanban card on below link</p>
    <p>
        <b>
            <a href='{{ Config::get("app.url") }}/tickets/kanban-board/boards'>
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

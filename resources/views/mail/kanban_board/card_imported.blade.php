@extends('mail.mailTemplate')
@section('content')
    <p>Hello,</p>
    <p>Your import process has been completed successfully.</p>

    @if (!empty($cards_count))
        <p>Total Records Imported: {{$cards_count}}</p>
    @endif 
    
    @if (!empty($importedBy->fullName()))
        <p>Records Imported By: {{$importedBy->fullName()}}</p>
    @endif
    
    @if (!empty($imported_on))
        <p>Imported On: {{$imported_on}}</p>
    @endif 

    @if (!empty($board))
        <p>Board Name: {{$board->name}}</p>
    @endif 

    <p>You can access the kanban cards on below link</p>
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
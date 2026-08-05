@extends('mail.newMailTemplate')
@section('content')
<p style="text-align: left;">
    <b>Ticket</b> {{ $ticket->ticket_tag ?? '#' . $ticket->id }} has breached response SLA.
</p>
<p style="text-align: left;">
    <b>Subject:</b> {{ $ticket->subject }}
</p>
<p style="text-align: left;"> 
    <b>Assigned To :</b> {{ $ticket->assignedTo->name ?? 'Unassigned' }}
</p>
<p style="text-align: left;">
    <b>Creator :</b> {{ $ticket->creator->fullName() ?? 'Unknown' }}
</p>
<p style="text-align: left;">
    <b>Created At:</b> {{ $ticket->created_at }}
</p>
<p style="text-align: left;">
    <b>ResponseSLA:</b> {{ $ticket->response_sla }}
</p>
<p style="text-align: left;">
    <b>Tat Expire:</b> {{ $ticket->tat_expire }}
</p>
<p style="text-align: left;">
    Please take immediate action.
</p>
@endsection

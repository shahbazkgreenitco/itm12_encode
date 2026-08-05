@extends('mail.mailTemplate')
@section('content')
    <p>Hello,</p>
    <p>Please find below the summary report for your reference.</p>

    <h4>Total No Of Tickets Yesterday</h4>
    <table border=1 style="border-collapse: collapse;width:100%;" class="table table-striped">
        <tr>
            <th>Total Tickets</th>
            @foreach($ticketTypes as $tp)
                <th>{{ $tp->name }}</th>
            @endforeach
            @foreach($ticketStatus as $ts)
                <th>{{ $ts->name }}</th>
            @endforeach
        </tr>
        <tr>
            @foreach ($vd['overallReport']->toArray() as $k => $v)
                @if($k != "departmenyWiseOverallReport")
                    <td style="text-align:center;">{{ $v ?? 0 }}</td>
                @endif
            @endforeach
        </tr>
    </table>
    <br>
    @if(!empty($vd['overallReport']['departmenyWiseOverallReport']))
        @foreach($vd['overallReport']['departmenyWiseOverallReport'] as $key => $value)
            <h4>Overall Ticket Overview For Department - {{$value->department}}</h4>
            <table border=1 style="border-collapse: collapse;width:100%;" class="table table-striped">
                <tr>
                    <th>Total Tickets</th>
                    @foreach ($ticketTypes as $tp)
                        <th>{{ $tp->name }}</th>
                    @endforeach
                    @foreach ($ticketStatus as $ts)
                        <th>{{ $ts->name }}</th>
                    @endforeach
                </tr>
                <tr>
                    @foreach ($value->toArray() as $k => $v)
                        @if($k != 'department')
                            <td style="text-align:center;">{{$v ?? 0}}</td>
                        @endif
                    @endforeach
                </tr>
            </table>
            <br><br>
        @endforeach
    @endif
    <hr>
    <h4>Technician Ticket Overview</h4>
    <table border=1 style="border-collapse: collapse;width:100%;" class="table table-striped">
        <tr>
            <th>Technicain</th>
            <th>Total Tickets</th>
            @foreach ($ticketTypes as $tp)
                <th>{{ $tp->name }}</th>
            @endforeach
            @foreach ($ticketStatus as $ts)
                <th>{{ $ts->name }}</th>
            @endforeach
        </tr>
        @foreach($vd['technicainData'] as $data)
            <tr>
                @foreach ($data->toArray() as $k => $v)
                    <td style="text-align:center;">{{ $v }}</td>
                @endforeach
            </tr>
        @endforeach
    </table>
    <br><br>
    <hr>
    <h4>Total No Of Requests Yesterday</h4>
    <table border=1 style="border-collapse: collapse;width:100%;" class="table table-striped">
        <tr>
            <th>Total Request</th>
            <th>Created</th>
            <th>Waiting For Approval</th>
            <th>Approved</th>
            <th>Rejected</th>
            <th>Cancelled</th>
            <th>On Hold</th>
        </tr>
        <tr>
            <td style="text-align:center;">{{ $vd['overallRequestReport']->ticket_count ?? 0 }}</td>
            <td style="text-align:center;">{{ $vd['overallRequestReport']->created ?? 0 }}</td>
            <td style="text-align:center;">{{ $vd['overallRequestReport']->wfa ?? 0 }}</td>
            <td style="text-align:center;">{{ $vd['overallRequestReport']->approved ?? 0 }}</td>
            <td style="text-align:center;">{{ $vd['overallRequestReport']->rejected ?? 0 }}</td>
            <td style="text-align:center;">{{ $vd['overallRequestReport']->cancelled ?? 0 }}</td>
            <td style="text-align:center;">{{ $vd['overallRequestReport']->onHold ?? 0 }}</td>
        </tr>
    </table>
    <br>
    @if(!empty($vd['overallRequestReport']->departmentOverallServiceRequest))
        @foreach($vd['overallRequestReport']->departmentOverallServiceRequest as $key => $value)
            <h4>Total No Of Requests Yesterday For Department - {{ $value->department }}</h4>
            <table border=1 style="border-collapse: collapse;width:100%;" class="table table-striped">
                <tr>
                    <th>Total Request</th>
                    <th>Created</th>
                    <th>Waiting For Approval</th>
                    <th>Approved</th>
                    <th>Rejected</th>
                    <th>Cancelled</th>
                    <th>On Hold</th>
                </tr>
                <tr>
                    <td style="text-align:center;">{{ $value->ticket_count ?? 0 }}</td>
                    <td style="text-align:center;">{{ $value->created ?? 0 }}</td>
                    <td style="text-align:center;">{{ $value->wfa ?? 0 }}</td>
                    <td style="text-align:center;">{{ $value->approved ?? 0 }}</td>
                    <td style="text-align:center;">{{ $value->rejected ?? 0 }}</td>
                    <td style="text-align:center;">{{ $value->cancelled ?? 0 }}</td>
                    <td style="text-align:center;">{{ $value->onHold ?? 0 }}</td>
                </tr>
            </table>
        @endforeach
    @endif
    <br><br>
    <hr>
    <h4>Total SLA Breached Tickets Yesterday</h4>
    <table border=1 style="border-collapse: collapse;width:100%;" class="table table-striped">
        <tr>
            <th>Total Request</th>
            <th>Breached</th>
            <th>Unbreached</th>
        </tr>
        <tr>
            <td style="text-align:center;">{{ $vd['slaBreachInfo']['total'] ?? 0 }}</td>
            <td style="text-align:center;">{{ $vd['slaBreachInfo']['breached'] ?? 0 }}</td>
            <td style="text-align:center;">{{ $vd['slaBreachInfo']['unbreached'] ?? 0 }}</td>
        </tr>
    </table>
    <br>
    @if(!empty($vd['slaBreachInfo']['departmentWiseSlaBreachedCount']))
        @foreach($vd['slaBreachInfo']['departmentWiseSlaBreachedCount'] as $key => $val )
            <h4>Total SLA Breached Tickets Yesterday For Department : {{$val->department}}</h4>
            <table border=1 style="border-collapse: collapse;width:100%;" class="table table-striped">
                <tr>
                    <th>Total Request</th>
                    <th>Breached</th>
                    <th>Unbreached</th>
                </tr>
                <tr>
                    <td style="text-align:center;">{{ $val->total ?? 0 }}</td>
                    <td style="text-align:center;">{{ $val->overdue ?? 0 }}</td>
                    <td style="text-align:center;">{{ $val->not_breached ?? 0 }}</td>
                </tr>
            </table>
        @endforeach
    @endif
    <br><br>
    <hr>
    <h4>Yesterday's Feedback Overview</h4>
    <table border=1 style="border-collapse: collapse;width:100%;" class="table table-striped">
        <tr>
            <th>Ticket Id</th>
            <th>Rating</th>
            <th>CommentS</th>
        </tr>
        @if(count($vd['feedbacks']) == 0)
            <tr>
                <td style="text-align: center;" colspan="3">No Records Found</td>
            </tr>
        @else
            @foreach($vd['feedbacks'] as $f)
                <tr>
                    <td style="text-align:center;">{{$f->id}}</td>
                    <td style="text-align:center;">{{$f->feedback}}</td>
                    <td style="text-align:center;">{{$f->feedback_comment}}</td>
                </tr>
            @endforeach
        @endif
    </table>
    <br>
    @foreach($vd['feedbacks']->departmentfeedbacks as $key => $val )
        <h4>Yesterday's Feedback Overview For Department : {{$key}}</h4>
        <table border=1 style="border-collapse: collapse;width:100%;" class="table table-striped">
            <tr>
                <th>Ticket Id</th>
                <th>Rating</th>
                <th>CommentS</th>
            </tr>
            @if(count($val) == 0)
                <tr>
                    <td style="text-align: center;" colspan="3">No Records Found</td>
                </tr>
            @else
                @foreach($val as $f)
                    <tr>
                        <td style="text-align:center;">{{$f->id}}</td>
                        <td style="text-align:center;">{{$f->feedback}}</td>
                        <td style="text-align:center;">{{$f->feedback_comment}}</td>
                    </tr>
                @endforeach
            @endif
        </table>
    @endforeach
    @if(isset($site_name))
        <p>Thanks,<br/>{{ $site_name }}</p>
    @else
        <p>Thanks</p>
    @endif
    <style>
        table {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        th, td {
            padding: 12px;
        }
    </style>
@endsection
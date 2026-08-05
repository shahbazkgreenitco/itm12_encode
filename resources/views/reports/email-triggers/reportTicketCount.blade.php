@extends('mail.mailTemplate')
@section('content')
    <p>Hello,</p>
    <p>Please find below the summary report for your reference.</p>

    <h4>Total Tickets opened & closed</h4>
    <table border="1" style="border-collapse: collapse; width: 100%;" class="table table-striped">
        <thead>
            <tr>
                <th></th> 
                <th>Open</th>
                <th>Closed</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Service Ticket</td>
                <td>{{ $openTicket }}</td>
                <td>{{ $closeTicket }}</td>
            </tr>
            <tr>
                <td>Service Request</td>
                <td>{{ $openProcureRequest }}</td>
                <td>{{ $closeProcureRequest }}</td>
            </tr>
        </tbody>
    </table>
    
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
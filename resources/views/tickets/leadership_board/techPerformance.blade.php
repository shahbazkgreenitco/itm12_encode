<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Technician Performance</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            background: #f5f6f8;
            color: #333;
        }

        .page {
            padding: 20px;
        }

        .card {
            background: #fff;
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 18px;
            border: 1px solid #dcdcdc;

            /* Shadow (PDF-safe) */
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .section-title {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 12px;
            color: #1f2d3d;
        }

        .profile-table {
            width: 100%;
        }

        .avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #eee;
            border: 2px solid #cfcfcf;
            overflow: hidden;
            /* important for circle crop */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Actual image */
        .profile-avatar {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* no distortion */
            border-radius: 50%;
        }

        .rank-box {
            background: #e59a42;
            color: #fff;
            text-align: center;
            padding: 10px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            border: 1px solid #d48932;

            /* Shadow */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
        }

        .metrics-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
        }

        .metric-card {
            background: #ffffff;
            border-radius: 10px;
            padding: 12px;
            border-left: 5px solid #ddd;
            border-top: 1px solid #e5e5e5;
            border-right: 1px solid #e5e5e5;
            border-bottom: 1px solid #e5e5e5;

            /* Shadow */
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
        }

        .metric-title {
            font-size: 12px;
            color: #777;
        }

        .metric-value {
            font-size: 18px;
            font-weight: bold;
            margin-top: 6px;
            color: #222;
        }

        .metric-weight {
            font-size: 11px;
            color: #999;
            margin-top: 2px;
        }

        /* Color Indicators */
        .feedback {
            border-left-color: #1f3c88;
        }

        .escalation {
            border-left-color: #ff9800;
        }

        .sla {
            border-left-color: #f44336;
        }

        .response {
            border-left-color: #4caf50;
        }


        .list {
            padding-left: 18px;
            margin-top: 6px;
        }

        .list li {
            margin-bottom: 6px;
        }
    </style>
</head>

<body>
    <div class="page">

        <div class="card">
            <table class="profile-table">
                <tr>
                    <td width="15%">
                        <div class="avatar">
                            {{-- <img src="{{ $tech->avatar ? asset('storage/avatar/' . $tech->avatar) : asset('imgs/profile-75.jpg') }}" class="profile-avatar" alt="Profile Avatar"> --}}
                            <img src="{{ $tech->avatar ? asset('storage/avatar/'.$tech->avatar) : asset('imgs/profile-75.jpg') }}" class="profile-avatar" alt="Profile Avatar" onerror="this.onerror=null;this.src='{{ asset('imgs/profile-75.jpg') }}';">
                        </div>
                    </td>
                    <td width="65%">
                        <strong style="font-size:16px;">{{ $tech->full_name ?? '-' }}</strong><br>
                        Tech #{{ $tech->technician_id }}<br>
                        {{ $tech->email ?? '-' }}<br>
                        {{ $tech->phone ?? '-' }}
                    </td>
                    <td width="20%" align="right">
                        <div class="rank-box">
                            #{{ $rankno ?? '-' }}<br>
                            Rank
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section-title" align="center">Performance Metrics</div>

        <div class="card">
            <table class="metrics-table" cellpadding="8">
                <tr>
                    <td width="33%">
                        <div class="metric-card feedback">
                            <div class="metric-title">Feedback Score</div>
                            <div class="metric-value">{{ $tech->avg_feedback }}</div>
                            <div class="metric-weight">40% Weight</div>
                        </div>
                    </td>
                    <td width="33%">
                        <div class="metric-card escalation">
                            <div class="metric-title">Escalations</div>
                            <div class="metric-value">{{ $tech->escalation_count }}</div>
                            <div class="metric-weight">30% Weight</div>
                        </div>
                    </td>
                    <td width="33%">
                        <div class="metric-card sla">
                            <div class="metric-title">SLA Breached</div>
                            <div class="metric-value">{{ $tech->sla_breached }}</div>
                            <div class="metric-weight">20% Weight</div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td width="33%">
                        <div class="metric-card response">
                            <div class="metric-title">Avg Response Time</div>
                            <div class="metric-value">{{ $tech->avg_response_time }} hrs</div>
                            <div class="metric-weight">10% Weight</div>
                        </div>
                    </td>
                    <td width="33%">
                        <div class="metric-card response">
                            <div class="metric-title">Not Responded Ticket</div>
                            <div class="metric-value">{{ $tech->not_responded ?? 0 }}</div>
                            <div class="metric-weight">No Weight</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="card">
            <div class="section-title">AI Performance Summary</div>
            {!! $tech->ai_summary !!}
        </div>

        <div class="section-title" align="center">Ticket Summary</div>

        <div class="card">
            <table class="metrics-table">
                <tr>
                    <td width="25%">
                        <div class="metric-card feedback">
                            <div class="metric-title">Total Tickets</div>
                            <div class="metric-value">{{ $ticketStats->total }}</div>
                        </div>
                    </td>
                    <td width="25%">
                        <div class="metric-card response">
                            <div class="metric-title">Resolved</div>
                            <div class="metric-value">{{ $ticketStats->resolved }}</div>
                        </div>
                    </td>
                    <td width="25%">
                        <div class="metric-card escalation">
                            <div class="metric-title">Closed</div>
                            <div class="metric-value">{{ $ticketStats->closed }}</div>
                        </div>
                    </td>
                    <td width="25%">
                        <div class="metric-card sla">
                            <div class="metric-title">Open</div>
                            <div class="metric-value">{{ $ticketStats->open }}</div>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="card" style="text-align:center;">
                <img src="{{ $ticketChart }}" alt="Ticket Chart" style="width:300px; margin:auto;">
            </div>
        </div>

        <div class="card">
            <div class="section-title">Recent Escalations</div>

            <table width="100%" cellpadding="8" cellspacing="0" border="1"
                style="border-collapse:collapse; font-size:11px;">
                <thead style="background:#f1f3f6;">
                    <tr>
                        <th width="10%">ID</th>
                        <th width="50%">Subject</th>
                        <th width="20%">Status</th>
                        <th width="20%">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($escalations as $e)
                        <tr>
                            <td>#{{ $e->id }}</td>
                            <td>{{ $e->subject }}</td>
                            <td>{{ $e->status_name }}</td>
                            <td>{{ $e->created_at }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" align="center">No escalations found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card">
            <div class="section-title">Recent Feedback</div>

            <table width="100%" cellpadding="8" cellspacing="0" border="1"
                style="border-collapse:collapse; font-size:11px;">
                <thead style="background:#f1f3f6;">
                    <tr>
                        <th width="10%">ID</th>
                        <th width="40%">Subject</th>
                        <th width="15%">Rating</th>
                        <th width="35%">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feedbacks as $f)
                        <tr>
                            <td>#{{ $f->id }}</td>
                            <td>{{ $f->subject }}</td>
                            <td>{{ $f->feedback }}</td>
                            <td>{{ $f->remarks ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" align="center">No feedback available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
    <script>
        window.print();
    </script>
</body>

</html>
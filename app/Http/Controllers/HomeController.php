<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\UserDetails;
use App\Models\Settings;
use App\Models\Company;
use App\Models\Location;
use App\Models\Ticket\Ticket;
use App\Models\Ticket\Privilege;
use App\Models\Ticket\Status;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;
use Auth;
use DB;
use App\Helpers\Common as CommonHelper;
use App\Models\Ticket\TicketProcureRequest;
use App\Models\UserGdprAcceptedInfo;

use Carbon\CarbonPeriod;


class HomeController extends Controller
{
    public function newdashboard(Request $request) 
    {
        try {
            $currentUser = Auth::user();
            $settings = Settings::getSettings();
            if(config('app.client') == "ltts" && $settings->tnc_accept == 1 &&  $currentUser->location_id != null && in_array($currentUser->location_id, $overseasLocation)) {
                $tncAcceptance = $currentUser->tnc_accepted;
            } else if($currentUser->location_id != null && $settings->tnc_accept == 1) {
                $tncAcceptance = $currentUser->tnc_accepted;
            } else {
                $tncAcceptance = "NA";
            }
            $userDatails = UserDetails::select('dashboard_company_id')->where('user_id', Auth::user()->id)->first();
            $companyId = CommonHelper::getAccessibleCompanyIds();
            if ($userDatails) {
                $companys = Company::select('id', 'name as text')->where('id', $userDatails->dashboard_company_id)->get();
            }else {
                $companys = Company::select('id', 'name as text')->whereIn('id', $companyId)->get();
            }
            if(Auth::user()->hasPermission("service_tickets")) {
                $privileges = CommonHelper::getPrevilegedDepartmentIdsByCompanyAccess();
                $departments = Department::where('module_ticket_enabled', 1)->whereIn('id',$privileges)->select('id', 'name')->get();  
                return view('dashboard.ticket.index')->with('departments',$departments);
            }else{
                return view('dashboard.dashboard_end_user')->with(compact('tncAcceptance', 'companys', 'userDatails'));
            }
        } catch (\Exception $e) {
            Log::error("newdashboard error: ". $e->getMessage());
        }
    }
    public function getDailyTicketOverview(Request $request)
    {
        $today = Carbon::today();
        $now = Carbon::now();
        $userId = Auth::id();
        $departmentId = $request->department_id;
        $companyId = CommonHelper::getSelectedCompanyIds();
        $privileges = CommonHelper::getPrevilegedDepartmentIdsByCompanyAccess();
        $status_halt_enable = Status::where('tat_halt', 1)->whereIn("company_id", array_merge($companyId,[0]))->pluck('id')->toArray();
        $status_halt_disable = Status::where('tat_halt', 0)->whereIn("company_id", array_merge($companyId,[0]))->pluck('id')->toArray();
        $oneHourLater = $now->copy()->addHour();

        $baseQuery = Ticket::whereDate('created_at', $today)->whereNull('is_temp')->whereNotIn('status_id',[10])->whereIn('department_id',$privileges)->whereIn('company_id',$companyId)->whereNull('deleted_at');
        if (!empty($departmentId)) {
            $baseQuery->where('department_id', $departmentId);
        }

        $openTickets = (clone $baseQuery)->whereNotIn('status_id', [5, 6])->count();
        $resolvedTickets = (clone $baseQuery)->whereIn('status_id', [5,6])->count();
        $slaAboutToBreach = (clone $baseQuery)
            ->whereNotIn('status_id', [5, 6, 10])
            ->where(function ($q) use ($now,$oneHourLater,$status_halt_enable,$status_halt_disable) {
                $q->where(function ($q1) use ($status_halt_enable) {
                    $q1->whereIn('status_id', $status_halt_enable)
                    ->where('tat_remaining_mins', '>', 0);
                })
                ->orWhere(function ($q2) use ($status_halt_disable, $now, $oneHourLater) {
                    $q2->whereIn('status_id', $status_halt_disable)
                    ->whereBetween('tat_expire', [$now, $oneHourLater]);
                });

            })->count();

        $slaBreached = (clone $baseQuery)->whereNotIn('status_id', [5, 6, 10])->where(function ($q) use ($now, $status_halt_enable, $status_halt_disable) {
            $q->where(function ($q1) use ($status_halt_enable) {
                $q1->whereIn('status_id', $status_halt_enable)
                ->where('tat_remaining_mins', '<=', 0);
            })
            ->orWhere(function ($q2) use ($status_halt_disable, $now) {
                $q2->whereIn('status_id', $status_halt_disable)
                ->where('tat_expire', '<', $now);
            });
        })->count();

       $tickets = (clone $baseQuery)->pluck('id');

        $totalMinutes = 0;
        $count = 0;

        if ($tickets->isNotEmpty()) {
            $followings = DB::table('tkt_followings')
                ->select('ticket_id', 'action_type', 'created_at')
                ->whereIn('ticket_id', $tickets)
                ->whereIn('action_type', [1, 2, 7])
                ->orderBy('id')
                ->get()
                ->groupBy('ticket_id');

            foreach ($followings as $actions) {

                $assigned = null;
                $responded = null;

                foreach ($actions as $f) {

                    if ($f->action_type == 1 && !$assigned) {
                        $assigned = $f;
                    }

                    if (in_array($f->action_type, [2, 7]) && !$responded) {
                        $responded = $f;
                    }

                    if ($assigned && $responded) break;
                }

                if ($assigned && $responded) {

                    $minutes = max(0, Carbon::parse($assigned->created_at)
                        ->diffInMinutes(Carbon::parse($responded->created_at), false));
                    $count++;
                }
            }
        }

        $avgResponseTime = $count > 0 ? round($totalMinutes / $count, 2): 0;

        $data = [
            ['category' => 'Open Tickets', 'value' => $openTickets],
            ['category' => 'Resolved Tickets', 'value' => $resolvedTickets],
            ['category' => 'SLA About to Breach', 'value' => $slaAboutToBreach],
            ['category' => 'SLA Breached', 'value' => $slaBreached],
        ];

        $total = $openTickets+$resolvedTickets;

        $slaPercentage = $total > 0 ? round((($total - $slaBreached) / $total) * 100) : 0;

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $total,
            'sla_percentage' => $slaPercentage,
            'avg_response_time' => $avgResponseTime
        ]);
    }

    public function getTicketStatusTrend(Request $request)
    {
        $start = Carbon::now()->subDays(6)->startOfDay();
        $end   = Carbon::now()->endOfDay();
        $departmentId = $request->department_id;
        $companyId = CommonHelper::getSelectedCompanyIds();
        $privileges = CommonHelper::getPrevilegedDepartmentIdsByCompanyAccess();
        $query = Ticket::query()
            ->selectRaw("
                DATE(created_at) AS day,
                COUNT(CASE WHEN status_id = 1 THEN 1 END) AS open,
                COUNT(CASE WHEN status_id = 3 THEN 1 END) AS inprogress,
                COUNT(CASE WHEN status_id = 5 THEN 1 END) AS resolved,
                COUNT(CASE WHEN status_id = 4 THEN 1 END) AS hold
            ")
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('department_id',$privileges)
            ->whereIn('company_id',$companyId)
            ->whereNull('is_temp')
            ->whereNotIn('status_id',[10]);
        if (!empty($departmentId)) {
            $query->where('department_id', $departmentId);
        }
        $rows= $query->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at)')
            ->get()
            ->keyBy('day');

        $period = CarbonPeriod::create($start, Carbon::now()->startOfDay());

        $days       = [];
        $dayDates   = [];
        $open       = [];
        $inprogress = [];
        $resolved   = [];
        $hold       = [];

        foreach ($period as $date) {
            $key  = $date->toDateString();
            $row  = $rows->get($key);

            $days[]       = $date->format('M j');
            $dayDates[]   = $key;
            $open[]       = (int) ($row->open       ?? 0);
            $inprogress[] = (int) ($row->inprogress ?? 0);
            $resolved[]   = (int) ($row->resolved   ?? 0);
            $hold[]       = (int) ($row->hold       ?? 0);
        }

        return response()->json([
            'days'       => $days,
            'day_dates'  => $dayDates,
            'open'       => $open,
            'inprogress' => $inprogress,
            'resolved'   => $resolved,
            'hold'       => $hold,
            'totals'     => [
                'open'       => array_sum($open),
                'inprogress' => array_sum($inprogress),
                'resolved'   => array_sum($resolved),
                'hold'       => array_sum($hold),
            ],
        ]);
    }

    public function getTrendAnalytics(Request $request)
    {
        $fromDate = Carbon::now()->subDays(6)->startOfDay();
        $toDate   = Carbon::now()->endOfDay();

        $companyId = CommonHelper::getSelectedCompanyIds();
        $privileges = CommonHelper::getPrevilegedDepartmentIdsByCompanyAccess();
        $baseQuery = Ticket::query()
            ->whereNull('is_temp')
            ->whereNotIn('status_id',[10])
            ->whereIn('department_id',$privileges)
            ->whereIn('company_id',$companyId)
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$fromDate, $toDate]);

        if ($request->department_id) {
            $baseQuery->where('department_id', $request->department_id);
        }
        $chartData = (clone $baseQuery)
            ->selectRaw('DATE(created_at) as ticket_date, COUNT(*) as total')
            ->groupBy('ticket_date')
            ->orderBy('ticket_date')
            ->pluck('total', 'ticket_date');

        $ticketVolume = (clone $baseQuery)->count();
        $tickets = (clone $baseQuery)->pluck('id');

        $responseTotalHours = 0;
        $responseCount = 0;

        if ($tickets->isNotEmpty()) {

            $followings = DB::table('tkt_followings')
                ->select('ticket_id', 'action_type', 'created_at')
                ->whereIn('ticket_id', $tickets)
                ->whereIn('action_type', [1, 2, 7])
                ->orderBy('id')
                ->get()
                ->groupBy('ticket_id');

            foreach ($followings as $actions) {

                $assigned = null;
                $responded = null;

                foreach ($actions as $f) {

                    if ($f->action_type == 1 && !$assigned) {
                        $assigned = $f;
                    }

                    if (in_array($f->action_type, [2, 7]) && !$responded) {
                        $responded = $f;
                    }

                    if ($assigned && $responded) {
                        break;
                    }
                }

                if ($assigned && $responded) {

                    $hours = Carbon::parse($responded->created_at)
                        ->diffInMinutes(
                            Carbon::parse($assigned->created_at),
                            false
                        ) / 60;

                    $responseTotalHours += max(0, $hours);

                    $responseCount++;
                }
            }
        }

        $avgResponseTime = $responseCount > 0
            ? round($responseTotalHours / $responseCount, 2)
            : 0;


        $resolvedTickets = (clone $baseQuery)
            ->whereNotNull('resolved_at')
            ->pluck('id');

        $resolutionTotalHours = 0;
        $resolvedCount = 0;

        if ($resolvedTickets->isNotEmpty()) {

            $followings = DB::table('tkt_followings')
                ->select('ticket_id', 'action_type', 'created_at')
                ->whereIn('ticket_id', $resolvedTickets)
                ->whereIn('action_type', [1, 7])
                ->orderBy('id')
                ->get()
                ->groupBy('ticket_id');

            foreach ($followings as $actions) {

                $assigned = $actions->firstWhere('action_type', 1);
                $resolved = $actions->firstWhere('action_type', 7);

                if ($assigned && $resolved) {

                    $hours = Carbon::parse($resolved->created_at)
                        ->diffInMinutes(
                            Carbon::parse($assigned->created_at),
                            false
                        ) / 60;

                    $resolutionTotalHours += max(0, $hours);

                    $resolvedCount++;
                }
            }
        }

        $avgResolutionTime = $resolvedCount > 0
            ? round($resolutionTotalHours / $resolvedCount, 2)
            : 0;

        $status_halt_enable = Status::where('tat_halt', 1)->whereIn("company_id", array_merge($companyId,[0]))
            ->pluck('id')
            ->toArray();

        $status_halt_disable = Status::where('tat_halt', 0)
            ->whereIn("company_id", array_merge($companyId,[0]))->pluck('id')
            ->toArray();

        $now = now();

        $slaBreached = (clone $baseQuery)
            ->whereNotIn('status_id', [5, 6, 10])
            ->where(function ($q) use (
                $now,
                $status_halt_enable,
                $status_halt_disable
            ) {
                $q->where(function ($q1) use ($status_halt_enable) {

                    $q1->whereIn('status_id', $status_halt_enable)
                        ->where('tat_remaining_mins', '<=', 0);

                })->orWhere(function ($q2) use (
                    $status_halt_disable,
                    $now
                ) {

                    $q2->whereIn('status_id', $status_halt_disable)
                        ->where('tat_expire', '<', $now);

                });
            })
            ->count();

        return response()->json([
            'series' => [
                round($avgResponseTime, 2),
                round($avgResolutionTime, 2),
                (int) $ticketVolume,
                (int) $slaBreached
            ],
            'categories' => [
                'Response',
                'Resolution',
                'Volume',
                'SLA'
            ],
            'labels' => [
                'Avg Response Time'   => round($avgResponseTime, 2),
                'Avg Resolution Time' => round($avgResolutionTime, 2),
                'Ticket Volume'       => $ticketVolume,
                'SLA Breached'        => $slaBreached
            ]
        ]);
    }
    public function ticketSourceTrend(Request $request)
    {   
        $departmentId = $request->department_id;
        $daterange = trim($request->daterange ?? '');
        
        if (!empty($daterange)) {
            [$startDate, $endDate] = explode(' - ', $daterange);

            $start = Carbon::parse($startDate);
            $end   = Carbon::parse($endDate);
        }
        $companyId = CommonHelper::getSelectedCompanyIds();
        $privileges = CommonHelper::getPrevilegedDepartmentIdsByCompanyAccess();
        $sources = [
            "1" => "Portal",
            "2" => "Chat",
            "3" => "Email",
            "4" => "Mobile",
            "5" => "Call",
            "6" => "BOT"
        ];

        $colors = [
            "1" => "#050b3c",
            "2" => "#6366f1",
            "3" => "#f59e0b",
            "4" => "#f97316",
            "5" => "#9ca3af",
            "6" => "#10b981"
        ];

        $query = Ticket::select('created_via', DB::raw('COUNT(*) as total'))
            ->groupBy('created_via')->whereNull('is_temp')->whereNotIn('status_id',[10])->whereIn('department_id',$privileges)->whereIn('company_id',$companyId);
        if (!empty($daterange)) {
            $query->whereBetween('created_at', [$start, $end]);
        }

        if (!empty($departmentId)) {
            $query->where('department_id', $departmentId);
        }
        $sourceData= $query->pluck('total', 'created_via');

        $labels = [];

        foreach ($sources as $id => $name) {
            $labels[] = [
                'id' => $id,
                'label' => $name,
                'count' => $sourceData[$id] ?? 0,
                'color' => $colors[$id] ?? '#ccc'
            ];
        }

        return response()->json([
            'labels' => $labels
        ]);
    }

    public function feedbackDashboard(Request $request)
    {
        $departmentId = $request->department_id;
        $daterange = trim($request->daterange ?? '');
        if (!empty($daterange)) {
            [$startDate, $endDate] = explode(' - ', $daterange);

            $start = Carbon::parse($startDate);
            $end   = Carbon::parse($endDate);
        }
        $companyId = CommonHelper::getSelectedCompanyIds();
        $privileges = CommonHelper::getPrevilegedDepartmentIdsByCompanyAccess();
        $query= DB::table('tkt_followings as f')
            ->leftJoin('tkt_tickets as t', 't.id', '=', 'f.ticket_id')
            ->leftJoin('users as u', 'u.id', '=', 'f.updated_by')
            ->where('f.action_type', 3)
            ->whereNull('t.is_temp')
            ->whereNotIn('t.status_id',[10])
            ->whereIn('t.department_id',$privileges)
            ->whereIn('t.company_id',$companyId)
            ->whereNull('t.deleted_at');
        if (!empty($daterange)) {
            $query->whereBetween('t.created_at', [$start, $end]);
        }

        if (!empty($departmentId)) {
            $query->where('t.department_id', $departmentId);
        }
        $feedbacks= $query->select(
                'f.ticket_id',
                'f.remarks',
                't.feedback',
                'u.id as user_id',
                'u.first_name',
                'u.last_name'
            )
            ->orderBy('f.id')
            ->get()
            ->groupBy('ticket_id');

        $clean = [];

        foreach ($feedbacks as $items) {
            $clean[] = $items->first();
        }

        $positive = [];
        $negative = [];

        foreach ($clean as $fb) {

            $rating = $fb->feedback ?? 0;

            $fullName = trim($fb->first_name . ' ' . $fb->last_name);

            $user = \App\Models\User::find($fb->user_id);
            $profileImg = $user ? $user->getProfileImg() : null;

            $item = [
                'initials'   => strtoupper(substr($fullName, 0, 2)),
                'name'       => $fullName,
                'remarks'    => $fb->remarks,
                'rating'     => $rating,
                'profile'    => $profileImg
            ];

            if ($rating >= 4) {
                $positive[] = $item;
            } else {
                $negative[] = $item;
            }
        }
        usort($positive, function ($a, $b) {
            return $b['rating'] <=> $a['rating'];
        });

        usort($negative, function ($a, $b) {
            return $a['rating'] <=> $b['rating'];
        });

        return response()->json([
            'positive' => array_slice($positive, 0, 5),
            'negative' => array_slice($negative, 0, 5),
        ]);
    }

    public function issueTypeChart(Request $request)
    {

        $departmentId = $request->department_id;
        $daterange = trim($request->daterange ?? '');
        if (!empty($daterange)) {
            [$startDate, $endDate] = explode(' - ', $daterange);

            $start = Carbon::parse($startDate);
            $end   = Carbon::parse($endDate);
        }
        $companyId = CommonHelper::getSelectedCompanyIds();
        $privileges = CommonHelper::getPrevilegedDepartmentIdsByCompanyAccess();

        $query= DB::table('tkt_tickets as t')
            ->leftJoin('ticket_types as tt', 'tt.id', '=', 't.ticket_type')
            ->select(
                't.ticket_type',
                DB::raw("
                    CASE
                        WHEN t.ticket_type = 0 THEN 'Normal'
                        ELSE COALESCE(tt.name, 'Unknown')
                    END as label
                "),
                DB::raw('COUNT(*) as total')
            )
            ->whereNull('t.is_temp')
            ->whereNotIn('t.status_id',[10])
            ->whereIn('t.department_id',$privileges)
            ->whereIn('t.company_id',$companyId)
            ->whereNull('t.deleted_at');
        if (!empty($daterange)) {
            $query->whereBetween('t.created_at', [$start, $end]);
        }

        if (!empty($departmentId)) {
            $query->where('t.department_id', $departmentId);
        }
        $data= $query->groupBy('t.ticket_type', 'tt.name')
            ->get();

        $series = [];
        $labels = [];
        $ids = [];

        foreach ($data as $row) {
            $ids[] = (int) $row->ticket_type;
            $labels[] = $row->label;
            $series[] = $row->total;
        }

        return response()->json([
            'ids' => $ids,
            'labels' => $labels,
            'series' => $series,
            'total' => array_sum($series)
        ]);
    }
    public function getslaPriority(Request $request)
    {
        $departmentId = $request->department_id;
        $daterange = trim($request->daterange ?? '');
        if (!empty($daterange)) {
            [$startDate, $endDate] = explode(' - ', $daterange);

            $start = Carbon::parse($startDate);
            $end   = Carbon::parse($endDate);
        }
        $companyId = CommonHelper::getSelectedCompanyIds();
        $privileges = CommonHelper::getPrevilegedDepartmentIdsByCompanyAccess();
        $now = now();
        $twoHoursLater = $now->copy()->addHours(2);

        $status_halt_enable = Status::where('tat_halt', 1)->pluck('id')->whereIn("company_id", array_merge($companyId,[0]))->toArray();
        $status_halt_disable = Status::where('tat_halt', 0)->pluck('id')->whereIn("company_id", array_merge($companyId,[0]))->toArray();

        $baseFilter = [5, 6, 10];

        $trend= Ticket::selectRaw("
                DATE(tat_expire) as day,
                SUM(
                    CASE 
                        WHEN (
                            (status_id IN (" . implode(',', $status_halt_enable) . ")
                                AND tat_remaining_mins > 0
                                AND tat_remaining_mins <= 120
                            )
                            OR
                            (status_id IN (" . implode(',', $status_halt_disable) . ")
                                AND tat_expire BETWEEN '{$now}' AND '{$twoHoursLater}'
                            )
                        )
                        THEN 1 ELSE 0
                    END
                ) as total
            ")
            ->whereNotIn('status_id', $baseFilter)->whereNull('is_temp')->whereIn('department_id',$privileges)->whereIn('company_id',$companyId)->whereNull('deleted_at')
            ->whereDate('tat_expire', '>=', $now->copy()->subDays(6));
        if (!empty($daterange)) {
            $trend->whereBetween('created_at', [$start, $end]);
        }

        if (!empty($departmentId)) {
            $trend->where('department_id', $departmentId);
        }
        $trendData= $trend->groupBy(DB::raw('DATE(tat_expire)'))
            ->orderBy('day')
            ->get();

        $days = [];
        $dayDates = [];
        $trend = [];

        for ($i = 6; $i >= 0; $i--) {

            $date = $now->copy()->subDays($i)->format('Y-m-d');

            $days[] = $now->copy()->subDays($i)->format('D');
            $dayDates[] = $date;

            $row = $trendData->firstWhere('day', $date);

            $trend[] = $row ? (int) $row->total : 0;
        }

        $priorityQuery = Ticket::leftJoin(
                'tkt_priorities as p',
                'p.id',
                '=',
                'tkt_tickets.priority_id'
            )
            ->selectRaw("
                tkt_tickets.priority_id as id,
                COALESCE(p.name, 'Unknown') as label,
                COUNT(*) as total
            ")
            ->whereNotNull('tkt_tickets.priority_id')
            ->whereNull('tkt_tickets.is_temp')->whereNotIn('tkt_tickets.status_id',[10])->whereIn('tkt_tickets.department_id',$privileges)->whereIn('tkt_tickets.company_id',$companyId)->whereNull('tkt_tickets.deleted_at');
        if (!empty($daterange)) {
            $priorityQuery->whereBetween('tkt_tickets.created_at', [$start, $end]);
        }

        if (!empty($departmentId)) {
            $priorityQuery->where('tkt_tickets.department_id', $departmentId);
        }
        $priorityData= $priorityQuery->groupBy('tkt_tickets.priority_id', 'p.name')
            ->orderByDesc('total')
            ->get();

        $totalPriorityTickets = $priorityData->sum('total');

        $priorities = [];

        foreach ($priorityData as $row) {
            $priorities[] = [
                'id' => $row->id,
                'label' => $row->label,
                'count' => (int) $row->total,
                'percentage' => $totalPriorityTickets > 0
                    ? round(($row->total / $totalPriorityTickets) * 100)
                    : 0
            ];
        }

        $breachingTicketsQuery = Ticket::whereNotIn('status_id', $baseFilter)
            ->where(function ($q) use ($now, $twoHoursLater, $status_halt_enable, $status_halt_disable) {

                $q->where(function ($q1) use ($status_halt_enable) {
                    $q1->whereIn('status_id', $status_halt_enable)
                    ->where('tat_remaining_mins', '>', 0)
                    ->where('tat_remaining_mins', '<=', 120);
                })

                ->orWhere(function ($q2) use ($status_halt_disable, $now, $twoHoursLater) {
                    $q2->whereIn('status_id', $status_halt_disable)
                    ->whereBetween('tat_expire', [$now, $twoHoursLater]);
                });

            })->whereNull('is_temp')->whereIn('department_id',$privileges)->whereIn('company_id',$companyId)->whereNull('deleted_at');
            if (!empty($daterange)) {
                $breachingTicketsQuery->whereBetween('created_at', [$start, $end]);
            }

            if (!empty($departmentId)) {
                $breachingTicketsQuery->where('department_id', $departmentId);
            }
        $breachingTickets= $breachingTicketsQuery->count();

        $breachedTicketsQuery = Ticket::whereNotIn('status_id', $baseFilter)
            ->where(function ($q) use ($now, $status_halt_enable, $status_halt_disable) {
                $q->where(function ($q1) use ($status_halt_enable) {
                    $q1->whereIn('status_id', $status_halt_enable)
                    ->where('tat_remaining_mins', '<=', 0);
                })
                ->orWhere(function ($q2) use ($status_halt_disable, $now) {
                    $q2->whereIn('status_id', $status_halt_disable)
                    ->where('tat_expire', '<', $now);
                });

            })->whereNull('is_temp')->whereIn('department_id',$privileges)->whereIn('company_id',$companyId)->whereNull('deleted_at');

            if (!empty($daterange)) {
                $breachedTicketsQuery->whereBetween('created_at', [$start, $end]);
            }

            if (!empty($departmentId)) {
                $breachedTicketsQuery->where('department_id', $departmentId);
            }
            $breachedTickets= $breachedTicketsQuery->count();

        return response()->json([
            'total_breaching' => $breachingTickets,
            'breached_count'  => $breachedTickets,
            'next_hours'      => 2,

            'days'            => $days,
            'day_dates'       => $dayDates,
            'trend'           => $trend,

            'priorities'      => $priorities
        ]);
    }

    public function getTopIssues(Request $request)
    {
        $companyId = CommonHelper::getSelectedCompanyIds();
        $privileges = CommonHelper::getPrevilegedDepartmentIdsByCompanyAccess();

        $departmentId = $request->department_id;
        $daterange = trim($request->daterange ?? '');
        if (!empty($daterange)) {
            [$startDate, $endDate] = explode(' - ', $daterange);

            $start = Carbon::parse($startDate);
            $end   = Carbon::parse($endDate);
        }
        $allTagsQuery = Ticket::whereNotNull('tags')
            ->whereNull('is_temp')
            ->whereNotIn('status_id',[10])
            ->whereIn('department_id',$privileges)
            ->whereIn('company_id',$companyId)
            ->whereNull('deleted_at');
        if (!empty($daterange)) {
            $allTagsQuery->whereBetween('created_at', [$start, $end]);
        }

        if (!empty($departmentId)) {
            $allTagsQuery->where('department_id', $departmentId);
        }
         $allTags =$allTagsQuery->where('tags', '!=', '')
            ->pluck('tags');

        $tagCounts = [];

        foreach ($allTags as $tags) {

            foreach (explode(',', $tags) as $tagId) {

                $tagId = trim($tagId);

                if (!$tagId) {
                    continue;
                }

                $tagCounts[$tagId] = ($tagCounts[$tagId] ?? 0) + 1;
            }
        }

        arsort($tagCounts);

        $topTagIds = array_slice(array_keys($tagCounts), 0, 10);

        $tagNames = DB::table('knowledge_document_tags')
            ->whereIn('id', $topTagIds)
            ->pluck('tags', 'id');

        $issues = [];

        foreach ($topTagIds as $tagId) {

            $issues[] = [
                'id'    => $tagId,
                'name'  => $tagNames[$tagId] ?? 'Unknown',
                'count' => $tagCounts[$tagId]
            ];
        }

        return response()->json([
            'issues' => $issues
        ]);
    }

    public function getTechnicianLeaderboard(Request $request)
    {
        $companyId = CommonHelper::getSelectedCompanyIds();
        $privileges = CommonHelper::getPrevilegedDepartmentIdsByCompanyAccess();
        $departmentId = $request->department_id;
        $daterange = trim($request->daterange ?? '');

        $filters = [
            'department_id' => $departmentId,
            'daterange'     => $daterange
        ];
        $technicians = $this->buildBaseQuery($privileges, $filters)->get();

        $rankedTechnicians = $this->calculateScoresAndRank($technicians, $filters);

        $leaders = $rankedTechnicians
            ->take(4)
            ->map(function ($row) {

                $user = User::find($row->technician_id);

                return [
                    'rank' => $row->rank,

                    'name' => $row->full_name,

                    'ticketsResolved' => (int) $row->resolved_tickets,

                    'score' => $row->total_score,

                    'avatar' => $user
                        ? $user->getProfileImg()
                        : asset('images/default-user.png')
                ];
            })
            ->values();

        return response()->json([
            'data' => $leaders
        ]);
    }

    private function buildBaseQuery($privileges, $filters)
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $companyId = CommonHelper::getSelectedCompanyIds();

        $status_halt_enable = Status::where('tat_halt', 1)->whereIn("company_id", array_merge($companyId,[0]))->pluck('id')->toArray();
        $status_halt_disable = Status::where('tat_halt', 0)->whereIn("company_id", array_merge($companyId,[0]))->pluck('id')->toArray();

        $query = DB::table('tkt_tickets')
            ->select(
                'u.id',
                'u.username',
                'u.email',
                'u.phone',
                'u.avatar',
                'u.id as technician_id',

                DB::raw("CONCAT(u.first_name,' ',u.last_name) as full_name"),

                DB::raw("COUNT(DISTINCT tkt_tickets.id) as total_tickets"),

                DB::raw("
                    SUM(
                        CASE
                            WHEN tkt_tickets.status_id IN (5,6)
                            THEN 1
                            ELSE 0
                        END
                    ) as resolved_tickets
                "),

                DB::raw("
                    SUM(
                        CASE
                            WHEN tkt_tickets.status_id IN (5,6)
                            AND tkt_tickets.resolved_at IS NOT NULL
                            AND tkt_tickets.resolved_at <= tkt_tickets.tat_expire
                            THEN 1
                            ELSE 0
                        END
                    ) as sla_met_tickets
                "),

                DB::raw("
                    SUM(
                        CASE
                            WHEN tkt_tickets.feedback IS NOT NULL
                            THEN tkt_tickets.feedback
                            ELSE 0
                        END
                    ) as total_feedback
                "),

                DB::raw("
                    SUM(
                        CASE
                            WHEN tkt_tickets.feedback IS NOT NULL
                            THEN 1
                            ELSE 0
                        END
                    ) as feedback_count
                "),

                DB::raw("
                    SUM(
                        CASE
                            WHEN (
                                tkt_tickets.status_id IN (" . implode(',', $status_halt_enable) . ")
                                AND tat_remaining_mins <= 0
                            )
                            OR (
                                tkt_tickets.status_id IN (" . implode(',', $status_halt_disable) . ")
                                AND tat_expire < '{$now}'
                            )
                            THEN 1
                            ELSE 0
                        END
                    ) as sla_breached
                "),

                DB::raw("
                    SUM(
                        CASE
                            WHEN NOT EXISTS (
                                SELECT 1
                                FROM tkt_followings tf
                                WHERE tf.ticket_id = tkt_tickets.id
                                AND tf.updated_by = tkt_tickets.assigned_to
                            )
                            THEN 1
                            ELSE 0
                        END
                    ) as not_responded_tickets
                ")
            )

            ->leftJoin(
                'users as u',
                'u.id',
                '=',
                'tkt_tickets.assigned_to'
            )

            ->where('u.activated', 1)
            ->whereNotNull('tkt_tickets.assigned_to')
            ->whereNull('tkt_tickets.deleted_at')
            ->whereNull('tkt_tickets.is_temp')
            ->whereIn('tkt_tickets.company_id',$companyId)
            ->where('tkt_tickets.status_id', '!=', 10);

        if (!Auth::user()->isSuperUser()) {
            $query->whereIn('tkt_tickets.department_id', $privileges);
        }

        if (!empty($filters['department_id'])) {
            $query->where('tkt_tickets.department_id', $filters['department_id']);
        }

        if (!empty($filters['daterange'])) {
            $daterange = explode(' - ', $filters['daterange']);

            if (count($daterange) == 2) {
                $startDate = Carbon::parse($daterange[0])->startOfDay();
                $endDate   = Carbon::parse($daterange[1])->endOfDay();

                $query->whereBetween(
                    'tkt_tickets.created_at',
                    [$startDate, $endDate]
                );
            }
        }

        $query->groupBy(
            'u.id',
            'u.username',
            'u.email',
            'u.phone',
            'u.avatar',
            'u.first_name',
            'u.last_name'
        );

        return $query;
    }

    private function calculateScoresAndRank($technicians, $filters)
    {
        foreach ($technicians as $tech) {
            // Calculate average feedback score (40%)  // Normalize to 40%
            $avgFeedback = $tech->feedback_count > 0 ? ($tech->total_feedback / $tech->feedback_count) : 0;
            $feedbackScore = ($avgFeedback / 5) * 40;

            // Calculate escalation count (30%) // Deduct 2 points per escalation
            $escalationCount = $this->getEscalationCount($tech->id, $filters);
            $escalationScore = max(0, 30 - ($escalationCount * 2));

            // Calculate SLA breach score (20%) // Deduct 1 point per breach
            $slaScore = max(0, 20 - ($tech->sla_breached * 1));

            // Calculate average response time score (10%)
            $avgResponseTime = $this->getAverageResponseTime($tech->id, $filters);


            // Lower response time is better, so invert the score // Convert minutes to hours
            $responseScore = $avgResponseTime > 0 ? max(0, 10 - ($avgResponseTime / 60)) : 10;

            // Calculate total score
            $tech->feedback_score = round($feedbackScore, 2);
            $tech->escalation_score = round($escalationScore, 2);
            $tech->sla_score = round($slaScore, 2);
            $tech->response_score = round($responseScore, 2);
            $tech->total_score = round($feedbackScore + $escalationScore + $slaScore + $responseScore, 2);

            // Additional metrics
            $tech->avg_feedback = round($avgFeedback, 2);
            $tech->escalation_count = $escalationCount;
            $tech->avg_response_time = $avgResponseTime;
        }

        // Sort by total score (descending) and assign rank
        $sorted = $technicians->sortByDesc('total_score')->values();
        $rank = 1;
        foreach ($sorted as $tech) {
            $tech->rank = $rank++;
        }

        return $sorted;
    }

    private function getEscalationCount($techId, $filters)
    {
        $db = DB::table('tkt_esclation_logs as tel')->leftJoin('tkt_tickets as t', 't.id', '=', 'tel.ticket_id')->select('t.id');

        $privileges = CommonHelper::getPrevilegedDepartmentIdsByCompanyAccess();
        $db->whereIn('t.department_id', $privileges);
        $db->whereNull('t.deleted_at')->whereNull('t.is_temp')->where('t.assigned_to', $techId);

        if ($filters) {
            if (!empty($filters['department_id'])) {
                $db->where('t.department_id', $filters['department_id']);
            }

            if (!empty($filters['daterange'])) {
                $daterange = explode(' - ', $filters['daterange']);

                if (count($daterange) == 2) {
                    $startDate = Carbon::parse($daterange[0])->startOfDay();
                    $endDate   = Carbon::parse($daterange[1])->endOfDay();

                    $db->whereBetween(
                        't.created_at',
                        [$startDate, $endDate]
                    );
                }
            }
        } else {
            $fromDate = Carbon::now()->subMonths(3)->startOfDay();
            $toDate   = Carbon::now()->endOfDay();
            $date_range = $fromDate->format('Y-m-d') . ' - ' . $toDate->format('Y-m-d');
            $filters['date_range'] = $date_range;
            $daterange = explode(" - ", $filters["date_range"]);
            if (count($daterange) === 2) {
                $from_date = date("Y-m-d H:i:s", strtotime($daterange[0]));
                $to_date   = date("Y-m-d H:i:s", strtotime($daterange[1]));
                $db->whereBetween('t.created_at', [$from_date, $to_date]);
            }
        }

        $db->groupBy('t.id');

        return count($db->get());
    }

    private function getAverageResponseTime($techId, $filters) 
    {
        $totalHours = 0;
        $responseCount = 0;

        $query = Ticket::with('tktFollowingsLimited')
            ->where('assigned_to', $techId)
            ->whereNull('deleted_at')
            ->whereNull('is_temp')
            ->where('status_id', '!=', 10)
            ->select('id', 'assigned_to');


        if ($filters) {
                if (!empty($filters['department_id'])) {
                    $query->where('department_id', $filters['department_id']);
                }

                if (!empty($filters['daterange'])) {
                    $daterange = explode(' - ', $filters['daterange']);

                    if (count($daterange) == 2) {
                        $startDate = Carbon::parse($daterange[0])->startOfDay();
                        $endDate   = Carbon::parse($daterange[1])->endOfDay();

                        $query->whereBetween(
                            'created_at',
                            [$startDate, $endDate]
                        );
                    }
                }
        } else {
            $fromDate = Carbon::now()->subMonths(3)->startOfDay();
            $toDate   = Carbon::now()->endOfDay();
            $date_range = $fromDate->format('Y-m-d') . ' - ' . $toDate->format('Y-m-d');
            $filters['date_range'] = $date_range;
            $daterange = explode(" - ", $filters["date_range"]);
            if (count($daterange) === 2) {
                $from_date = date("Y-m-d H:i:s", strtotime($daterange[0]));
                $to_date   = date("Y-m-d H:i:s", strtotime($daterange[1]));
                $query->whereBetween('created_at', [$from_date, $to_date]);
            }
        }

        $query = $query->get();
        foreach ($query as $ticket) {
            $followings = $ticket->tktFollowingsLimited ?? collect();

            $assigned = $followings->firstWhere('action_type', 1);
            $responded = $followings->first(fn($f) => in_array($f->action_type, [2, 7]));

            if ($assigned && $responded) {
                $totalHours += $responded->created_at->diffInHours($assigned->created_at);
                $responseCount++;
            }
        }

        return $count = $responseCount > 0
            ? round($totalHours / $responseCount, 2)
            : 0;
    }
    public function getTicketByPlaces(Request $request) { 
        $companyId = CommonHelper::getSelectedCompanyIds(); 
        $privileges = CommonHelper::getPrevilegedDepartmentIdsByCompanyAccess(); 
        $query = Ticket::query();

        if (in_array(config('app.client'), ['ril'])) {
            $query->leftJoin('tkt_detail as td', 'td.ticket_id', '=', 'tkt_tickets.id')
                ->leftJoin('locations as l', 'l.id', '=', 'td.location_id');
        } else {
            $query->leftJoin('locations as l', 'l.id', '=', 'tkt_tickets.location_id');
        }

        $query->leftJoin('cities as ct', 'ct.id', '=', 'l.city_id')
                ->selectRaw("
                    ct.id as city_id,
                    ct.name as city_name,
                    ct.latitude as lat,
                    ct.longitude as lng,
                    COUNT(DISTINCT tkt_tickets.id) as total
                ");
        if (in_array(config('app.client'), ['ril'])) {
            $query->whereNotNull('td.location_id');
        } else {
            $query->whereNotNull('tkt_tickets.location_id');
        }
        $query->whereNotNull('l.city_id')
                ->whereNull('tkt_tickets.is_temp')
                ->whereNotIn('tkt_tickets.status_id',[10])
                ->whereIn('tkt_tickets.department_id',$privileges)
                ->whereIn('tkt_tickets.company_id',$companyId)
                ->whereNull('tkt_tickets.deleted_at');
        if ($request->department_id) { 
            $query->where('tkt_tickets.department_id', $request->department_id); 
        } 
        if ($request->daterange) { 
            [$start, $end] = explode(' - ', $request->daterange); 
            $query->whereBetween( 'tkt_tickets.created_at', 
                [ Carbon::parse($start)->startOfDay(), Carbon::parse($end)->endOfDay() ] ); 
        } 
        $data = $query->groupBy(
            'ct.id',
            'ct.name',
            'ct.latitude',
            'ct.longitude'
        )->get();
        return response()->json([ 'data' => $data ]); 
    }

     public function getDashboardData(Request $request){
        $req = $request->all();
        $return = [
            "status" => "fail",
            "msg" => "Error during get dashboard data"
        ];
        try {
            $userId = Auth::id();
            $dashboardCompanyId = UserDetails::where('user_id', $userId)->value('dashboard_company_id');
            if (!empty($dashboardCompanyId) && $dashboardCompanyId != 0) {
                $company = [(int) $dashboardCompanyId];
            } else {
                $company = CommonHelper::getAccessibleCompanyIds();
            }
            $locations = Location::withTrashed()->whereIn('company_id', $company)->get()->pluck('id');
            $lfbAssigned = '-';
            $currentUser = Auth::user();
            $get_user_privileges = CommonHelper::getPrevilegedDepartmentIdsByCompanyAccess();
            $usr_prv_dep = $get_user_privileges;
            $upd = '';
            foreach($usr_prv_dep as $pd){
                if($upd!=''){
                    $upd = $upd.",".$pd;
                }else{
                    $upd = $pd;
                }
            }

            $fbs = Ticket::where("creator_id", "=", $currentUser->id)->whereIn("status_id", [5,6])->whereNull("deleted_at")->whereNotNull("feedback")
                ->select('id', 'feedback')->orderBy("id", "desc")->get();
            if(!$fbs || $fbs->count() == 0) {
                $lfb = '-';
            } else {
                $lfb = $fbs->first()->feedback;
            }

            // Overall My department data
            $deptWIseOverall = [];
            $dataMyWaitingApprovalsInfo = 0;
            $myAssignedTicketsAboutToBreachedInfo = $dataMyAssignedTicketsAboutToBreachedInfo = $datamyAssignedTicketsBreachedInfo = $dataAssignedTicketInfo = $datamyAssignedTicketsInfo = $datamyAssignedTicketsRatingInfo = $dataTicketInfo = $datamyTicketsInfo = $datamyTicketsRatingInfo = $dataServiceRequestCounts = $dataServiceRequestWaitingForMe = $board_info = $dataMyApprovalsInfo = $dataMyrequest = $tot_devices = $tot_accessories = $tot_licenses = $tot_consumable = [];
            $dataMyWaitingApprovalsInfo = [];
            if (config("services.service_ticket.enabled")) {
    
                // status board service
                if(config("services.status_board.enabled")) {
                    $board_info = DB::select("select count(*) as tot_items, sum(case when status = 1 then 1 else 0 end) as tot_active, sum(case when status = 2 then 1 else 0 end) as tot_per_iss, sum(case when status = 3 then 1 else 0 end) as tot_par_out, sum(case when status = 4 then 1 else 0 end) as tot_major from status_board_items where is_enabled = 1");
                }
                
                //service Request Data
                $serviceRequestcounts = TicketProcureRequest::select(DB::raw('count(status_id) as total'),DB::raw('sum(case when status_id in (1) then 1 else 0 end) as pending'),DB::raw('sum(case when status_id in (2) then 1 else 0 end) as waiting'),DB::raw('sum(case when status_id in (3) then 1 else 0 end) as approved'));
                if (isset($req['filters']['from_date']) && $req['filters']['from_date'] != null && isset($req['filters']['to_date']) && $req['filters']['to_date'] != null) {
                    $fromdate = date("Y-m-d H:i:s",strtotime($req['filters']['from_date']));
                    $todate = date("Y-m-d H:i:s",strtotime($req['filters']['to_date']));
                    $serviceRequestcounts->where('created_at','<=',date("Y-m-d H:i:s",strtotime($req['filters']['to_date'])));
                    $serviceRequestcounts->where('created_at','>=',date("Y-m-d H:i:s",strtotime($req['filters']['from_date'])));
                }
                if (isset($req['filters']['filter_by_department']) && $req['filters']['filter_by_department']) {
                    $serviceRequestcounts->where('department_id','>=',date("Y-m-d H:i:s",strtotime($req['filters']['filter_by_department'])));
                }
                $dataServiceRequestCounts = $serviceRequestcounts->where('creator_id',$currentUser->id)->whereIn('company_id',$company)->whereIn('location_id', $locations)->get();

                // user own service tickets

                $ticket_info = Ticket::select(DB::raw('count(tkt_tickets.id) as tot'), DB::raw('sum(case when status_id in (1) then 1 else 0 end) as tot_open'), DB::raw('sum(case when status_id in (2) then 1 else 0 end) as tot_reopened'), DB::raw('sum(case when status_id in (3) then 1 else 0 end) as tot_ip'), DB::raw('sum(case when status_id in (8) then 1 else 0 end) as tot_wfv'), DB::raw('sum(case when status_id in (7) then 1 else 0 end) as tot_wfu'), DB::raw('sum(case when status_id in (5) then 1 else 0 end) as tot_resolved'), DB::raw('sum(case when status_id in (7) then 1 else 0 end) as tot_wfu'), DB::raw('sum(case when status_id in (6) then 1 else 0 end) as tot_closed'), DB::raw('sum(case when status_id in (10) then 1 else 0 end) as tot_spam'),  DB::raw('sum(case when status_id not in (5,6) then 1 else 0 end) as tot_not_resolved'), DB::raw('sum(case when priority_id = 1 then 1 else 0 end) as tot_critical'), DB::raw('sum(case when priority_id = 2 then 1 else 0 end) as tot_high'), DB::raw('sum(case when priority_id = 3 then 1 else 0 end) as tot_medium'), DB::raw('sum(case when priority_id = 4 then 1 else 0 end) as tot_low'))
                ->whereNull('deleted_at')
                ->where('tkt_tickets.creator_id',$currentUser->id)
                ->whereNull('tkt_tickets.is_temp')
                ->whereIn('tkt_tickets.company_id', $company);
                if (isset($req['filters']['from_date']) && $req['filters']['from_date'] != null && isset($req['filters']['to_date']) && $req['filters']['to_date'] != null) {
                    $fromdate = date("Y-m-d H:i:s",strtotime($req['filters']['from_date']));
                    $todate = date("Y-m-d H:i:s",strtotime($req['filters']['to_date']));
                    $ticket_info->where('tkt_tickets.created_at','<=',date("Y-m-d H:i:s",strtotime($req['filters']['to_date'])));
                    $ticket_info->where('tkt_tickets.created_at','>=',date("Y-m-d H:i:s",strtotime($req['filters']['from_date'])));
                }
                if (isset($req['filters']['filter_by_department']) && $req['filters']['filter_by_department']) {
                    $ticket_info->where('tkt_tickets.department_id','>=',date("Y-m-d H:i:s",strtotime($req['filters']['filter_by_department'])));
                }
                $dataTicketInfo = $ticket_info->get();

                $db = TicketProcureRequest::select('tkt_procure_requests.*')->whereIn('tkt_procure_requests.company_id',$company)->whereIn('tkt_procure_requests.location_id', $locations);
                $db->Join('tkt_approval_request as ar', function ($join) use ($currentUser) {
                    $join->on('tkt_procure_requests.id', '=', 'ar.pr_id');
                    $join->where(function ($subQ) use ($currentUser) {
                        $subQ->where('ar.user_id', $currentUser->id)->orWhere('ar.delegated_user_id', $currentUser->id);
                    });
                });
                $dataMyApprovalsInfo = $db->count();
                $db->where('tkt_procure_requests.creator_id', '=', $currentUser->id);
                $dataMyrequest = $db->count();
                
                $waiting_approval_db = TicketProcureRequest::select('tkt_procure_requests.*')->whereIn('tkt_procure_requests.company_id',$company)->whereIn('tkt_procure_requests.location_id', $locations);
                $waiting_approval_db->whereIn('status_id',[1,2]);
                $waiting_approval_db->Join('tkt_approval_request as ar', function ($join) use ($currentUser) {
                    $join->on('tkt_procure_requests.id', '=', 'ar.pr_id');
                    $join->where(function ($subQ) use ($currentUser) {
                        $subQ->where('ar.user_id', $currentUser->id)->orWhere('ar.delegated_user_id', $currentUser->id);
                    });
                });
                $dataMyWaitingApprovalsInfo = $waiting_approval_db->where('ar.approve_status', 3)->distinct('tkt_procure_requests.id')->count('tkt_procure_requests.id');                
                $myTicketsInfo = Ticket::leftJoin('tkt_statuses as ts', 'ts.id', 'tkt_tickets.status_id')
                ->leftJoin('tkt_priorities as tp', 'tp.id', 'tkt_tickets.priority_id')
                ->leftJoin('departments as dep', 'tkt_tickets.department_id', '=', 'dep.id')
                ->leftJoin('tkt_procure_requests', 'tkt_tickets.id', '=','tkt_procure_requests.ticket_id');
                if(in_array(config('app.client'), ["ril"])) {
                    $myTicketsInfo->leftJoin('tkt_detail as tkt_d', 'tkt_tickets.id', '=','tkt_d.ticket_id');
                }
                $myTicketsInfo->select('tkt_tickets.id', 'tkt_tickets.subject', 'tkt_tickets.department_id', 'tkt_tickets.assigned_to', 'tkt_tickets.status_id', 'tkt_tickets.created_at', 'tkt_tickets.updated_at', 'ts.name as status', 'tp.name as priority', DB::raw('DATE_FORMAT(tkt_tickets.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'),DB::raw('DATE_FORMAT(tkt_tickets.created_at, "%d %b %Y %h:%i %p") as created_at_format'), 'tkt_procure_requests.id as request_id', 'tkt_procure_requests.procure_tag as request_tag','dep.name as department_name')
                ->where('tkt_tickets.creator_id', Auth()->user()->id)->whereNull('tkt_tickets.is_temp')->whereIn('tkt_tickets.company_id',$company);
                if(in_array(config('app.client'), ["ril"])) {
                    $myTicketsInfo->whereIn('tkt_d.location_id', $locations);
                }else{
                    $myTicketsInfo->whereIn('tkt_tickets.location_id', $locations);
                }
                $myTicketsInfo->orderBy('updated_at', 'desc');
                if(isset($req['filters']['from_date']) && $req['filters']['from_date'] != null && isset($req['filters']['to_date']) && $req['filters']['to_date'] != null){
                    $myTicketsInfo->where('tkt_tickets.created_at','<=',date("Y-m-d H:i:s",strtotime($req['filters']['to_date'])));
                    $myTicketsInfo->where('tkt_tickets.created_at','>=',date("Y-m-d H:i:s",strtotime($req['filters']['from_date'])));                    

                }
                if(isset($req['filters']['filter_by_department']) && $req['filters']['filter_by_department'] != null){
                    $myTicketsInfo->where('tkt_tickets.department_id',$req['filters']['filter_by_department']);
                }
                $datamyTicketsInfo = $myTicketsInfo->limit(5)->get();


                $ratingMyTickets = Ticket::select(DB::raw('round(avg(tkt_tickets.feedback),1) as overall_rating'), DB::raw('max(tkt_tickets.feedback) as highest_rating'), DB::raw('min(tkt_tickets.feedback) as lowest_rating'));
                if(in_array(config('app.client'), ["ril"])) {
                    $ratingMyTickets->leftJoin('tkt_detail as tkt_d', 'tkt_tickets.id', '=','tkt_d.ticket_id');
                }
                $ratingMyTickets->where('tkt_tickets.creator_id', Auth()->user()->id)
                ->whereNull('tkt_tickets.is_temp')
                ->whereIn('tkt_tickets.company_id',$company);
                if(in_array(config('app.client'), ["ril"])) {
                    $ratingMyTickets->whereIn('tkt_d.location_id', $locations);
                }else{
                    $ratingMyTickets->whereIn('tkt_tickets.location_id', $locations);
                }
                if(isset($req['filters']['from_date']) && $req['filters']['from_date'] != null && isset($req['filters']['to_date']) && $req['filters']['to_date'] != null) {
                    $ratingMyTickets->where('tkt_tickets.created_at', '<=', date("Y-m-d H:i:s", strtotime($req['filters']['to_date'])));
                    $ratingMyTickets->where('tkt_tickets.created_at', '>=', date("Y-m-d H:i:s", strtotime($req['filters']['from_date'])));
                }
                if(isset($req['filters']['filter_by_department']) && $req['filters']['filter_by_department'] != null){
                    $ratingMyTickets->where('tkt_tickets.department_id',$req['filters']['filter_by_department']);
                }
                $datamyTicketsRatingInfo = $ratingMyTickets->get();

                $fbs = Ticket::where("assigned_to", "=", $currentUser->id)->whereIn("status_id", [5,6])->whereNull("deleted_at")->whereNotNull("feedback")
                    ->select('id', 'feedback')->whereIn('company_id',$company)->whereIn('location_id', $locations)->orderBy("id", "desc")->get();
                if(!$fbs || $fbs->count() == 0) {
                    $lfbAssigned = '-';
                } else {
                    $lfbAssigned = $fbs->first()->feedback;
                }


                // user assigned service tickets
                if (Auth::user()->hasPermission("service_tickets")) {

                    if ($upd != "") {
                        $companyIds = implode(',', $company);
                        $locationId = implode(',', $locations->toArray());
                        // $sql = "select count(case when t.status_id != 10 then t.id end) as tot, sum(case when t.status_id = 1 then 1 else 0 end) as 'open', sum(case when t.status_id = 2 then 1 else 0 end) as 'reopened', sum(case when t.status_id = 3 then 1 else 0 end) as 'in_progress', sum(case when t.status_id = 4 then 1 else 0 end) as 'on_hold', sum(case when t.status_id = 5 then 1 else 0 end) as 'resolved', sum(case when t.status_id = 6 then 1 else 0 end) as 'closed', sum(case when t.status_id = 7 then 1 else 0 end) as 'waiting_for_the_user', sum(case when t.status_id = 8 then 1 else 0 end) as 'waiting_for_the_vendor', sum(case when t.status_id = 9 then 1 else 0 end) as 'waiting_for_the_approval', sum(case when t.status_id = 10 then 1 else 0 end) as 'spam' from tkt_tickets as t where t.department_id in (" . $upd . ") and t.company_id in (".$companyIds.") and t.location_id IN (."$locationId".) and t.is_temp is null and t.deleted_at is null";
                        if(in_array(config('app.client'), ["ril"])) {
                            $sql = "SELECT count(CASE WHEN t.status_id != 10 THEN t.id END) AS tot, SUM(CASE WHEN t.status_id = 1 THEN 1 ELSE 0 END) AS open, SUM(CASE WHEN t.status_id = 2 THEN 1 ELSE 0 END) AS reopened, SUM(CASE WHEN t.status_id = 3 THEN 1 ELSE 0 END) AS in_progress, SUM(CASE WHEN t.status_id = 4 THEN 1 ELSE 0 END) AS on_hold, SUM(CASE WHEN t.status_id = 5 THEN 1 ELSE 0 END) AS resolved, SUM(CASE WHEN t.status_id = 6 THEN 1 ELSE 0 END) AS closed, SUM(CASE WHEN t.status_id = 7 THEN 1 ELSE 0 END) AS waiting_for_the_user, SUM(CASE WHEN t.status_id = 8 THEN 1 ELSE 0 END) AS waiting_for_the_vendor, SUM(CASE WHEN t.status_id = 9 THEN 1 ELSE 0 END) AS waiting_for_the_approval, SUM(CASE WHEN t.status_id = 10 THEN 1 ELSE 0 END) AS spam FROM tkt_tickets AS t LEFT JOIN tkt_detail AS td ON td.ticket_id = t.id WHERE t.department_id IN (".$upd.") AND t.company_id IN (".$companyIds.") AND td.location_id IN (".$locationId.") AND t.is_temp IS NULL AND t.deleted_at IS NULL";
                        }else{
                            $sql = "select count(case when t.status_id != 10 then t.id end) as tot, sum(case when t.status_id = 1 then 1 else 0 end) as 'open', sum(case when t.status_id = 2 then 1 else 0 end) as 'reopened', sum(case when t.status_id = 3 then 1 else 0 end) as 'in_progress', sum(case when t.status_id = 4 then 1 else 0 end) as 'on_hold', sum(case when t.status_id = 5 then 1 else 0 end) as 'resolved', sum(case when t.status_id = 6 then 1 else 0 end) as 'closed', sum(case when t.status_id = 7 then 1 else 0 end) as 'waiting_for_the_user', sum(case when t.status_id = 8 then 1 else 0 end) as 'waiting_for_the_vendor', sum(case when t.status_id = 9 then 1 else 0 end) as 'waiting_for_the_approval', sum(case when t.status_id = 10 then 1 else 0 end) as 'spam' from tkt_tickets as t where t.department_id in (" . $upd . ") and t.company_id in (".$companyIds.") and t.location_id IN (".$locationId.") and t.is_temp is null and t.deleted_at is null";
                        }
                        $deptWIseOverall = DB::select($sql);
                    }

                    $assignedTicket_info = Ticket::select(DB::raw('count(tkt_tickets.id) as tot'), DB::raw('sum(case when status_id in (1) then 1 else 0 end) as tot_open'), DB::raw('sum(case when status_id in (2) then 1 else 0 end) as tot_reopened'), DB::raw('sum(case when status_id in (3) then 1 else 0 end) as tot_ip'), DB::raw('sum(case when status_id in (8) then 1 else 0 end) as tot_wfv'), DB::raw('sum(case when status_id in (7) then 1 else 0 end) as tot_wfu'), DB::raw('sum(case when status_id in (5) then 1 else 0 end) as tot_resolved'), DB::raw('sum(case when status_id in (7) then 1 else 0 end) as tot_wfu'), DB::raw('sum(case when status_id in (6) then 1 else 0 end) as tot_closed'), DB::raw('sum(case when status_id in (10) then 1 else 0 end) as tot_spam'),  DB::raw('sum(case when status_id not in (5,6) then 1 else 0 end) as tot_not_resolved'), DB::raw('sum(case when priority_id = 1 then 1 else 0 end) as tot_critical'), DB::raw('sum(case when priority_id = 2 then 1 else 0 end) as tot_high'), DB::raw('sum(case when priority_id = 3 then 1 else 0 end) as tot_medium'), DB::raw('sum(case when priority_id = 4 then 1 else 0 end) as tot_low'));
                    if(in_array(config('app.client'), ["ril"])) {
                        $assignedTicket_info->leftJoin('tkt_detail as tkt_d', 'tkt_tickets.id', '=','tkt_d.ticket_id');
                    }
                    $assignedTicket_info->whereNull('tkt_tickets.deleted_at')
                    ->where('tkt_tickets.assigned_to',$currentUser->id)
                    ->whereNull('tkt_tickets.is_temp')
                    ->whereIn('tkt_tickets.company_id',$company);
                    if(in_array(config('app.client'), ["ril"])) {
                        $assignedTicket_info->whereIn('tkt_d.location_id', $locations);
                    }else{
                        $assignedTicket_info->whereIn('tkt_tickets.location_id', $locations);
                    }
                    if (isset($req['filters']['from_date']) && $req['filters']['from_date'] != null && isset($req['filters']['to_date']) && $req['filters']['to_date'] != null) {
                        $fromdate = date("Y-m-d H:i:s",strtotime($req['filters']['from_date']));
                        $todate = date("Y-m-d H:i:s",strtotime($req['filters']['to_date']));
                        $assignedTicket_info->where('tkt_tickets.created_at','<=',date("Y-m-d H:i:s",strtotime($req['filters']['to_date'])));
                        $assignedTicket_info->where('tkt_tickets.created_at','>=',date("Y-m-d H:i:s",strtotime($req['filters']['from_date'])));
                    }
                    if (isset($req['filters']['filter_by_department']) && $req['filters']['filter_by_department']) {
                        $assignedTicket_info->where('tkt_tickets.department_id','>=',date("Y-m-d H:i:s",strtotime($req['filters']['filter_by_department'])));
                    }
                    $dataAssignedTicketInfo = $assignedTicket_info->get();
                    
                    $myAssignedTicketsInfo = Ticket::leftJoin('tkt_statuses as ts', 'ts.id', 'tkt_tickets.status_id')
                    ->leftJoin('tkt_priorities as tp', 'tp.id', 'tkt_tickets.priority_id')
                    ->whereNotIn('tkt_tickets.status_id', [5,6,7])
                    ->select('tkt_tickets.id', 'tkt_tickets.subject', 'tkt_tickets.department_id', 'tkt_tickets.assigned_to', 'tkt_tickets.status_id', 'tkt_tickets.created_at', 'tkt_tickets.updated_at', 'ts.name as status', 'tp.name as priority', DB::raw('DATE_FORMAT(tkt_tickets.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'));
                    if(isset($req['filters']['from_date']) && $req['filters']['from_date'] != null && isset($req['filters']['to_date']) && $req['filters']['to_date'] != null) {
                            $myAssignedTicketsInfo->where('tkt_tickets.created_at', '<=', date("Y-m-d H:i:s", strtotime($req['filters']['to_date'])));
                            $myAssignedTicketsInfo->where('tkt_tickets.created_at', '>=', date("Y-m-d H:i:s", strtotime($req['filters']['from_date'])));
                    }
                    if(isset($req['filters']['filter_by_department']) && $req['filters']['filter_by_department'] != null){
                        $myAssignedTicketsInfo->where('tkt_tickets.department_id',$req['filters']['filter_by_department']);
                    }
                    $datamyAssignedTicketsInfo = $myAssignedTicketsInfo->where('tkt_tickets.assigned_to', Auth()->user()->id)->whereNull('tkt_tickets.is_temp')->whereIn('tkt_tickets.company_id',$company)->orderBy('updated_at', 'desc')->limit(5)->get();

                    // Breached Ticket
                    $myAssignedTicketsBreachedInfo = Ticket::select('tkt_tickets.id', 'tkt_tickets.subject', 'tkt_tickets.department_id', 'tkt_tickets.assigned_to', 'tkt_tickets.status_id', 'tkt_tickets.created_at', 'tkt_tickets.updated_at', 'ts.name as status', 'tp.name as priority', DB::raw('DATE_FORMAT(tkt_tickets.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'))
                        ->leftJoin('tkt_statuses as ts', 'ts.id', 'tkt_tickets.status_id')
                        ->leftJoin('tkt_priorities as tp', 'tp.id', 'tkt_tickets.priority_id');
                    if(in_array(config('app.client'), ["ril"])) {
                        $myAssignedTicketsBreachedInfo->leftJoin('tkt_detail as tkt_d', 'tkt_tickets.id', '=','tkt_d.ticket_id');
                    }
                    if(isset($req['filters']['from_date']) && $req['filters']['from_date'] != null && isset($req['filters']['to_date']) && $req['filters']['to_date'] != null) {
                            $myAssignedTicketsBreachedInfo->where('tkt_tickets.created_at', '<=', date("Y-m-d H:i:s", strtotime($req['filters']['to_date'])));
                            $myAssignedTicketsBreachedInfo->where('tkt_tickets.created_at', '>=', date("Y-m-d H:i:s", strtotime($req['filters']['from_date'])));
                    }
                    if(isset($req['filters']['filter_by_department']) && $req['filters']['filter_by_department'] != null){
                        $myAssignedTicketsBreachedInfo->where('tkt_tickets.department_id',$req['filters']['filter_by_department']);
                    }

                    $tat_halt_enable = Status::whereNotIn('id', [4,5,6,10])->where('tat_halt', 1)->get()->pluck('id');
                    $myAssignedTicketsBreachedInfo->where(function($query) use($tat_halt_enable) {
                        $query->whereIn('tkt_tickets.status_id', $tat_halt_enable)->where('tkt_tickets.tat_remaining_mins', '<=', 0);
                        $tat_halt_disable = Status::where('tat_halt', 0)->get()->pluck('id');
                        $query->orWhere(function($query) use($tat_halt_disable) {
                            $query->whereIn('tkt_tickets.status_id', $tat_halt_disable)->where('tkt_tickets.tat_expire', '<', Carbon::now()->format("Y-m-d H:i:s"));
                        });
                    });

                    $myAssignedTicketsBreachedInfo->whereNull('tkt_tickets.is_temp')->whereNull('tkt_tickets.merge_primary')->whereIn('tkt_tickets.company_id',$company);
                    if(in_array(config('app.client'), ["ril"])) {
                        $myAssignedTicketsBreachedInfo->whereIn('tkt_d.location_id', $locations);
                    }else{
                        $myAssignedTicketsBreachedInfo->whereIn('tkt_tickets.location_id', $locations);
                    }
                    $data = $myAssignedTicketsBreachedInfo->where('tkt_tickets.assigned_to', Auth()->user()->id)
                        ->orderBy('updated_at', 'desc')
                        ->limit(5)->get();
                    $datamyAssignedTicketsBreachedInfo = $data;
                    // myAssignedTicketsAboutToBreachedInfo
                    $nextHour = date("Y-m-d H:i:s", strtotime("+4 hour"));
                    $myAssignedTicketsAboutToBreachedInfo = Ticket::select('tkt_tickets.id', 'tkt_tickets.subject', 'tkt_tickets.department_id', 'tkt_tickets.assigned_to', 'tkt_tickets.status_id', 'tkt_tickets.created_at', 'tkt_tickets.updated_at', 'ts.name as status', 'tp.name as priority', DB::raw('DATE_FORMAT(tkt_tickets.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'))
                    ->leftJoin('tkt_statuses as ts', 'ts.id', 'tkt_tickets.status_id')
                    ->leftJoin('tkt_priorities as tp', 'tp.id', 'tkt_tickets.priority_id')
                    ->whereNull('tkt_tickets.is_temp')->whereNull('tkt_tickets.merge_primary')
                    ->whereNotIn('status_id',[5,6,7])
                    ->where('tkt_tickets.tat_expire', '>=', date("Y-m-d H:i:s"))
                    ->where('tkt_tickets.tat_expire', '<=', $nextHour)
                    ->whereIn('tkt_tickets.company_id',$company)
                    ->whereIn('tkt_tickets.location_id', $locations);
                    if(isset($req['filters']['from_date']) && $req['filters']['from_date'] != null && isset($req['filters']['to_date']) && $req['filters']['to_date'] != null) {
                            $myAssignedTicketsAboutToBreachedInfo->where('tkt_tickets.created_at', '<=', date("Y-m-d H:i:s", strtotime($req['filters']['to_date'])));
                            $myAssignedTicketsAboutToBreachedInfo->where('tkt_tickets.created_at', '>=', date("Y-m-d H:i:s", strtotime($req['filters']['from_date'])));
                    }
                    if(isset($req['filters']['filter_by_department']) && $req['filters']['filter_by_department'] != null){
                        $myAssignedTicketsAboutToBreachedInfo->where('tkt_tickets.department_id', $req['filters']['filter_by_department']);
                    }
                    $dataMyAssignedTicketsAboutToBreachedInfo = $myAssignedTicketsAboutToBreachedInfo->where('tkt_tickets.assigned_to', Auth()->user()->id)
                    ->orderBy('updated_at', 'desc')->limit(5)->get();

                    //rating Overview 
                    $ratingTickets = Ticket::select(DB::raw('round(avg(tkt_tickets.feedback),1) as overall_rating'), DB::raw('max(tkt_tickets.rating) as feedback'), DB::raw('min(tkt_tickets.rating) as feedback'));
                    if(in_array(config('app.client'), ["ril"])) {
                        $ratingTickets->leftJoin('tkt_detail as tkt_d', 'tkt_tickets.id', '=','tkt_d.ticket_id');
                    }
                    $ratingTickets->where('tkt_tickets.assigned_to', Auth()->user()->id)
                    ->whereNull('tkt_tickets.is_temp')
                    ->whereIn('tkt_tickets.company_id',$company);
                    if(in_array(config('app.client'), ["ril"])) {
                        $ratingTickets->whereIn('tkt_d.location_id', $locations);
                    }else{
                        $ratingTickets->whereIn('tkt_tickets.location_id', $locations);
                    }
                    if(isset($req['filters']['from_date']) && $req['filters']['from_date'] != null && isset($req['filters']['to_date']) && $req['filters']['to_date'] != null) {
                        $ratingTickets->where('tkt_tickets.created_at', '<=', date("Y-m-d H:i:s", strtotime($req['filters']['to_date'])));
                        $ratingTickets->where('tkt_tickets.created_at', '>=', date("Y-m-d H:i:s", strtotime($req['filters']['from_date'])));
                    }
                    if(isset($req['filters']['filter_by_department']) && $req['filters']['filter_by_department'] != null){
                        $ratingTickets->where('tkt_tickets.department_id',$req['filters']['filter_by_department']);
                    }
                    $datamyAssignedTicketsRatingInfo = $ratingTickets->get();
                }
            }

            $tot_devices =[];
            $tot_accessories =[];
            $tot_licenses =[];
            $tot_devices =[];
            $tot_consumable =[];
            if(config("services.assets.enabled")) {
                $tot_devices = DB::table('assets as a')
                ->join('status_labels as sl', 'sl.id', '=', 'a.status_id')
                ->where('a.assigned_to', $currentUser->id)
                ->whereNull('a.deleted_at')
                ->where('sl.deployed', 1)
                ->count();

                $tot_accessories = DB::table('accessories_users as au')
                    ->join('accessories as a', 'a.id', '=', 'au.accessory_id')
                    ->where('au.assigned_to', $currentUser->id)
                    ->whereNull('a.deleted_at')
                    ->count();

                $tot_licenses = DB::table('license_seats as ls')
                ->leftJoin('assets as dev', function ($join) {
                    $join->on('dev.id', '=', 'ls.asset_id')
                        ->whereNull('ls.deleted_at');
                })
                ->where('ls.assigned_to', $currentUser->id)
                ->orWhere('dev.assigned_to', $currentUser->id)
                ->count();

                $tot_consumable = DB::table('consumables_users as cu')
                ->join('consumables as c', 'c.id', '=', 'cu.consumable_id')
                ->where('cu.assigned_to', $currentUser->id)
                ->whereNull('c.deleted_at')
                ->count();
            }

            $return['data'] = [
                'assignedTicket_info' => $dataAssignedTicketInfo,
                'myAssignedTicketsInfo' => $datamyAssignedTicketsInfo,
                'ticket_info' => $dataTicketInfo,
                'myTicketsInfo' => $datamyTicketsInfo,
                'myAssignedTicketsBreachedInfo' => $datamyAssignedTicketsBreachedInfo,
                'myAssignedTicketsAboutToBreachedInfo' => $dataMyAssignedTicketsAboutToBreachedInfo,
                'myAssignedTicketsRatingInfo' => $datamyAssignedTicketsRatingInfo,
                'myTicketsRatingInfo' => $datamyTicketsRatingInfo,
                'dataserviceRequestcounts' => $dataServiceRequestCounts,
                'statusBoardInfo' => $board_info,
                'lastFeedback' => $lfb,
                'lastFeedbackAssigned' => $lfbAssigned,
                'ticketsOverallForMyDept' => $deptWIseOverall,
                'tot_approval' => $dataMyApprovalsInfo,
                'tot_myRequest' => $dataMyrequest,
                'tot_awaiting_my_approval' => $dataMyWaitingApprovalsInfo,
                'tot_devices' => $tot_devices,
                'tot_accessories' => $tot_accessories,
                'tot_licenses' => $tot_licenses,
                'tot_consumable' => $tot_consumable
            ];
            $return['status'] = "success";
            $return['msg'] = "Get dashboard data successfully";
            return response()->json($return);
        } catch(Exception $e) {
            Log::error('getDashboardData() :'. Auth::user()->id .' - '.$e->getMessage());
            return response()->json($return);
        }
    }

    public function tncAcceptance(Request $request) {
        $return = ['status' => 'danger', 'msg' => 'Unable to accept the terms and conditions.'];
        try {
            DB::beginTransaction();
            $user = User::find(Auth::user()->id);
            $user->tnc_accepted = date("Y-m-d H:i:s");
            if(!$user->save()) {
                return $return;
            }
            $extraInfo = new UserGdprAcceptedInfo();
            $data = [
                'user_id' => Auth::user()->id,
                'ip' => $request->ip(),
                'browser' => $request->header('User-Agent'),
                'history_id' => CommonHelper::settings()->history_id
            ];
            $extraInfo->fill($data);
            if(!$extraInfo->save()) {
                return $return;
            }
            DB::commit();
            $return = [
                'status' => 'success', 'msg' => 'Cookie Policy and Privacy Policy accepted successfully.',
            ];
            return $return;
        } catch(\Exception $e) {
            DB::rollBack();
            Log::error("tncAcceptance error: ".$e->getMessage());
            return $return;
        }
    }

    public function typographyv1(Request $request) {
        return view('dashboard.typographyV1');
    } 
    public function typographyv2(Request $request) {
        return view('dashboard.typographyV2');
    } 

}

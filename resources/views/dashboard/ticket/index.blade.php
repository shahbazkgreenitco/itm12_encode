{{-- @page-meta
{
  "page_no": "STD-01",
  "file": "index.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "Priya Maru",
      "from": "2026-06-18",
      "reviewer": null,
      "description":"Service Ticket Dashboard"
    }
  ]
}
--}}
@extends('layouts.layout1')
@section('title', trans('dashboard.title'))
@section('content')
    {{-- ① HEADER --}}
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4 ">
        <h3 class="h3-text mb-0">
            {{ trans('dashboard.title') }}
        </h3>
    
        <div class="d-flex align-items-center gap-2 flex-shrink-0">            
            <select class="std-drop" id="departmentFilter">
                @foreach($departments as $index => $dept)
                    <option value="{{ $dept->id }}" {{ $index == 0 ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                @endforeach
            </select>
            <div id="range" class="std-drop" style="cursor:pointer">
                <span>Select Date Range</span>
                <svg width="15px" height="15px" viewBox="0 0 48 48" fill="currentColor">
                    <title>{{ trans("content.filter_heading.filter_by_daterange") }}</title>
                    <g id="Layer_2" data-name="Layer 2">
                        <g id="invisible_box" data-name="invisible box">
                            <rect width="48" height="48" fill="none" />
                        </g>
                        <g id="icons_Q2" data-name="icons Q2">
                            <path
                                d="M44,8H35V4.1A2.1,2.1,0,0,0,33.3,2,2,2,0,0,0,31,4V8H17V4.1A2.1,2.1,0,0,0,15.3,2,2,2,0,0,0,13,4V8H4a2,2,0,0,0-2,2V42a2,2,0,0,0,2,2H44a2,2,0,0,0,2-2V10A2,2,0,0,0,44,8ZM42,40H6V20H42Zm0-24H6V12H42Z" />
                            <rect x="8" y="24" width="8" height="8" rx="2" ry="2" />
                            <rect x="32" y="24" width="8" height="8" rx="2" ry="2" />
                            <rect x="20" y="24" width="8" height="8" rx="2" ry="2" />
                        </g>
                    </g>
                </svg>
                <i class="bi bi-x-circle clear-date"></i>
                <input type="hidden" name="daterange" id="daterange">
            </div>
        </div>
    </div>

    {{-- ② MAIN —— LEFT + RIGHT grid --}}
    <main class="main-content" id="mainContent">
        <div class="std-wrap mt-2 ms-3">
            {{-- ═══════════════════════════ LEFT COLUMN ═══════════════════════════ --}}
            <div class="std-left">

                {{-- AI Banner --}}
                {{-- <div class="ai-banner">
                    <div class="ai-top">
                        <div class="ai-lbl">
                            <div class="ai-dot"><svg viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="6" fill="white" />
                                </svg></div>
                            AI Ticket status summary
                        </div>
                        <div class="ai-nav" style="display:flex;gap:6px;"><button>‹</button><button>›</button></div>
                    </div>
                    <div class="ai-tiles">
                        <div class="ai-tile">
                            <span class="ai-badge ab-g">100 Tickets</span>
                            <div class="ai-n">18% <span class="au">↑</span></div>
                            <p class="ai-d">Ticket volume increased by 18% today</p>
                        </div>
                        <div class="ai-tile">
                            <span class="ai-badge ab-b">20 Tickets</span>
                            <div class="ai-n">40% <span class="au">↑</span></div>
                            <p class="ai-d">Tickets resolved within 2 hours</p>
                        </div>
                        <div class="ai-tile">
                            <span class="ai-badge ab-r">15 Tickets</span>
                            <div class="ai-n">03 <span class="ad">↓</span></div>
                            <p class="ai-d">Technicians are overloaded in 10 technicians</p>
                        </div>
                    </div>
                </div> --}}
               
                {{-- Charts Row 1: Daily Overview + Status Trends --}}
                <div class="chart-row">

                    {{-- Daily Ticket Overview --}}
                    <div class="amg-dashboard-card">
                        <div class="pb-2">
                            <div class="px-3 pt-3">
                                <span class="s2-text fw-medium">{{ trans('dashboard.daily_ticket_overview') }}</span>
                            </div>

                            <div id="ticketOverviewChart" class="px-3"></div>
                            <div class="d-flex justify-content-between border-bottom px-3 pb-2 mb-2">
                                <div class="d-flex flex-column align-items-start" id="slaPercentage">
                                    <div>
                                        <span class="b5-text fw-bold">0%</span>
                                        <span class="au b5-text fw-bold">↑</span>
                                    </div>
                                    <span class="b7-text" style="color:#7F7F7F">SLA</span>
                                </div>

                                <div class="d-flex flex-column align-items-end" id="avgResponseTime">
                                    <div>
                                        <span class="ad b5-text fw-bold">↓</span>
                                        <span class="value b5-text fw-bold">0</span>
                                        <span class="b5-text fw-bold">min</span>
                                    </div>
                                    <span class="b7-text" style="color:#7F7F7F">Avg. Response</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between px-3 py-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="amg-dashboard-legend legend-swatch--navy"></span>
                                    <span style="color:#7F7F7F" class="b7-text">Open Tickets</span>
                                </div>
                                <span class="b7-text fw-bold" id="openCount">0</span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between px-3 py-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="amg-dashboard-legend legend-swatch--teal"></span>
                                    <div style="color:#7F7F7F" class="b7-text">Resolved Tickets</div>
                                </div>
                                <span class="b7-text fw-bold" id="resolvedCount">0</span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between px-3 py-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="amg-dashboard-legend legend-swatch--orange"></span>
                                    <span style="color:#7F7F7F" class="b7-text">SLA About to Breach</span>
                                </div>
                                <span class="b7-text fw-bold" id="aboutCount">0</span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between px-3 py-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="amg-dashboard-legend legend-swatch--purple"></span>
                                    <span style="color:#7F7F7F" class="b7-text" >SLA Breached</span>
                                </div>
                                <span class="b7-text fw-bold" id="breachedCount">0</span>
                            </div>
                        </div>
                    </div>

                    {{-- Ticket Status Trends --}}
                    <div class="amg-dashboard-card">
                        <div class="pb-2">
                            <div class="px-3 pt-3">
                                <span class="s2-text fw-medium"> {{ trans('dashboard.ticket_status_trends') }}</span>
                            </div>

                            <div id="ticketStatusTrendChart"></div>
                            <div class="d-flex justify-content-between border-bottom px-3 pb-2 mb-2">
                                <span class="b7-text" style="color:#7F7F7F">Last 7 Days</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between px-3 py-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="amg-dashboard-legend legend-swatch--navy"></span>
                                    <span style="color:#7F7F7F" class="b7-text">Open Tickets</span>
                                </div>
                                <span class="b7-text fw-semibold" id="openTicketsTotal">0</span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between px-3 py-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="amg-dashboard-legend legend-swatch--teal"></span>
                                    <span style="color:#7F7F7F" class="b7-text">Inprogress</span>
                                </div>
                                <span class="b7-text fw-semibold" id="inprogressTicketsTotal">0</span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between px-3 py-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="amg-dashboard-legend legend-swatch--purple"></span>
                                    <span style="color:#7F7F7F" class="b7-text">Resolved</span>
                                </div>
                                <span class="b7-text fw-semibold" id="resolvedTicketsTotal">0</span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between px-3 py-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="amg-dashboard-legend legend-swatch--orange"></span>
                                    <span style="color:#7F7F7F" class="b7-text">On Hold</span>
                                </div>
                                <span class="b7-text fw-semibold" id="holdTicketsTotal">0</span>
                            </div>
                        </div>
                    </div>

                </div>{{-- /chart-row 1 --}}

                {{-- Charts Row 2: Trend Analytics + Issue Type --}}
                <div class="chart-row">

                    {{-- Trend Analytics --}}
                    <div class="amg-dashboard-card">
                        <div class="pb-2">
                            <div class="px-3 pt-3">
                                <span class="s2-text fw-medium"> {{ trans('dashboard.trend_analytics') }}</span>
                            </div>

                            <div id="trendChart"></div>

                            <div class="d-flex justify-content-between border-bottom px-3 pb-2 mb-2">
                                <span class="b7-text" style="color:#7F7F7F">Last 7 Days</span>
                            </div>

                            <div class="b7-text fw-semibold" id="trendAnalyticsStats"></div>

                        </div>
                    </div>

                    {{-- Issue Type --}}
                    <div class="amg-dashboard-card">
                        <div class="pb-2">
                            <div class="px-3 pt-3">
                                <span class="s2-text fw-medium">{{ trans('dashboard.issue_type') }}</span>
                            </div>

                            <div class="d-flex justify-content-center mb-3">
                                <div id="issueChart" style="width:130px;height:130px;"></div>
                            </div>
                            <div class="d-flex justify-content-between border-bottom px-3 mb-2"></div>
                            <div id="issueList" class="px-3" ></div>

                        </div>
                    </div>

                </div>{{-- /chart-row 2 --}}

                {{-- Feedback --}}
            <div class="amg-dashboard-card p-3">
                <div class="amg-dashboard-card shadow-none">
                    <div class="amg-dashboard-fb-card">

                        <!-- POSITIVE -->
                        <div class="fb-col">
                            <div class="fb-title">
                                <span class="fb-icon fbi-p">
                                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.335 0C12.9383 0 16.67 3.73167 16.67 8.335C16.67 12.9375 12.9383 16.6683 8.335 16.6683C3.73167 16.6683 0 12.9375 0 8.335C0 3.73167 3.73167 0 8.335 0ZM5.38667 10.6542C5.33589 10.5897 5.2729 10.5358 5.2013 10.4956C5.1297 10.4554 5.05089 10.4298 4.96937 10.4201C4.80473 10.4004 4.63904 10.447 4.50875 10.5496C4.44424 10.6004 4.39036 10.6633 4.35019 10.7349C4.31002 10.8065 4.28435 10.8854 4.27464 10.9669C4.25502 11.1315 4.30162 11.2972 4.40417 11.4275C4.87178 12.0228 5.46867 12.504 6.14968 12.8346C6.83069 13.1652 7.57798 13.3366 8.335 13.3358C9.09091 13.3366 9.83712 13.1657 10.5173 12.836C11.1975 12.5063 11.794 12.0264 12.2617 11.4325C12.3126 11.3681 12.3503 11.2943 12.3726 11.2153C12.395 11.1364 12.4016 11.0537 12.392 10.9722C12.3825 10.8907 12.357 10.8119 12.3169 10.7402C12.2769 10.6685 12.2232 10.6055 12.1587 10.5546C12.0943 10.5037 12.0206 10.466 11.9416 10.4436C11.8626 10.4213 11.78 10.4147 11.6985 10.4242C11.617 10.4338 11.5381 10.4593 11.4665 10.4993C11.3948 10.5393 11.3317 10.5931 11.2808 10.6575C10.9302 11.1033 10.4828 11.4635 9.97242 11.711C9.46208 11.9584 8.90217 12.0866 8.335 12.0858C7.17 12.0858 6.09333 11.5525 5.38667 10.6542ZM5.835 5.62667C5.69578 5.62273 5.55719 5.64675 5.42742 5.6973C5.29764 5.74786 5.17933 5.82393 5.07946 5.921C4.9796 6.01808 4.90021 6.1342 4.84601 6.26249C4.7918 6.39079 4.76387 6.52864 4.76387 6.66792C4.76387 6.80719 4.7918 6.94505 4.84601 7.07334C4.90021 7.20163 4.9796 7.31775 5.07946 7.41483C5.17933 7.51191 5.29764 7.58798 5.42742 7.63853C5.55719 7.68909 5.69578 7.71311 5.835 7.70917C6.10606 7.7015 6.36344 7.58843 6.55245 7.394C6.74147 7.19956 6.84721 6.93908 6.84721 6.66792C6.84721 6.39675 6.74147 6.13627 6.55245 5.94184C6.36344 5.7474 6.10606 5.63434 5.835 5.62667ZM10.835 5.62667C10.6958 5.62273 10.5572 5.64675 10.4274 5.6973C10.2976 5.74786 10.1793 5.82393 10.0795 5.921C9.9796 6.01808 9.90022 6.1342 9.84601 6.26249C9.7918 6.39079 9.76387 6.52864 9.76387 6.66792C9.76387 6.80719 9.7918 6.94505 9.84601 7.07334C9.90022 7.20163 9.9796 7.31775 10.0795 7.41483C10.1793 7.51191 10.2976 7.58798 10.4274 7.63853C10.5572 7.68909 10.6958 7.71311 10.835 7.70917C11.1061 7.7015 11.3634 7.58843 11.5525 7.394C11.7415 7.19956 11.8472 6.93908 11.8472 6.66792C11.8472 6.39675 11.7415 6.13627 11.5525 5.94184C11.3634 5.7474 11.1061 5.63434 10.835 5.62667Z" fill="#186B43"/>
                                    </svg>
                                </span>
                                 <span class="b3-text fw-medium">{{ trans('dashboard.positive_fb') }}</span>
                            </div>

                            <div id="positiveFeedback"></div>
                        </div>

                        <!-- NEGATIVE -->
                        <div class="fb-col">
                            <div class="fb-title">
                                <span class="fb-icon fbi-n">
                                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8.33333 0C12.9358 0 16.6667 3.73083 16.6667 8.33333C16.6667 12.9358 12.9358 16.6667 8.33333 16.6667C3.73083 16.6667 0 12.9358 0 8.33333C0 3.73083 3.73083 0 8.33333 0ZM8.33333 9.5775C7.66769 9.57748 7.00874 9.71037 6.39514 9.96837C5.78153 10.2264 5.22562 10.6043 4.76 11.08C4.70118 11.1383 4.65453 11.2077 4.62277 11.2842C4.59101 11.3607 4.57477 11.4428 4.575 11.5256C4.57523 11.6084 4.59192 11.6904 4.6241 11.7667C4.65628 11.843 4.70331 11.9122 4.76246 11.9702C4.8216 12.0282 4.89168 12.0738 4.96862 12.1045C5.04557 12.1351 5.12784 12.1502 5.21065 12.1488C5.29347 12.1474 5.37518 12.1295 5.45103 12.0962C5.52688 12.063 5.59537 12.015 5.6525 11.955C6.00176 11.598 6.41881 11.3144 6.87916 11.1208C7.33952 10.9272 7.83392 10.8275 8.33333 10.8275C9.35417 10.8275 10.3083 11.2358 11.0092 11.95C11.0663 12.01 11.1349 12.058 11.2108 12.0912C11.2867 12.1245 11.3684 12.1423 11.4513 12.1436C11.5341 12.145 11.6164 12.1299 11.6934 12.0991C11.7703 12.0684 11.8404 12.0227 11.8995 11.9646C11.9586 11.9065 12.0056 11.8373 12.0377 11.7609C12.0698 11.6845 12.0864 11.6025 12.0865 11.5197C12.0866 11.4368 12.0703 11.3548 12.0384 11.2783C12.0065 11.2018 11.9598 11.1324 11.9008 11.0742C11.436 10.5995 10.8809 10.2225 10.2682 9.96551C9.65559 9.70848 8.99772 9.57656 8.33333 9.5775ZM5.1825 3.88333L5.10583 3.83C4.9763 3.75428 4.82384 3.72768 4.67632 3.75507C4.5288 3.78247 4.39605 3.86201 4.30233 3.97919C4.20861 4.09636 4.16018 4.24334 4.16587 4.39328C4.17156 4.54322 4.231 4.6861 4.33333 4.79583L4.40083 4.85917L5.44833 5.69583C5.27243 5.7658 5.11892 5.88239 5.0043 6.03305C4.88968 6.18372 4.81828 6.36278 4.79779 6.55098C4.7773 6.73917 4.80848 6.9294 4.88798 7.1012C4.96748 7.27301 5.09231 7.4199 5.24903 7.5261C5.40575 7.63229 5.58845 7.69376 5.77749 7.70391C5.96653 7.71406 6.15476 7.6725 6.32195 7.5837C6.48914 7.4949 6.62897 7.36222 6.72641 7.19991C6.82386 7.0376 6.87523 6.85181 6.875 6.6625C6.99678 6.66289 7.11601 6.62764 7.21801 6.56111C7.32001 6.49457 7.40032 6.39965 7.44903 6.28804C7.49775 6.17643 7.51276 6.053 7.4922 5.93297C7.47164 5.81293 7.41642 5.70154 7.33333 5.6125L7.265 5.55L5.1825 3.88333ZM12.3633 3.98083C12.2698 3.86378 12.1373 3.78423 11.99 3.7567C11.8427 3.72917 11.6904 3.75548 11.5608 3.83083L11.4842 3.88333L9.4025 5.55L9.33417 5.6125C9.25699 5.69595 9.20404 5.79888 9.181 5.91019C9.15797 6.0215 9.16573 6.13698 9.20344 6.24421C9.24116 6.35144 9.3074 6.44635 9.39504 6.51874C9.48269 6.59112 9.58841 6.63823 9.70083 6.655L9.7925 6.66167C9.79222 6.8466 9.84118 7.02827 9.93434 7.18802C10.0275 7.34777 10.1615 7.47984 10.3226 7.57066C10.4837 7.66147 10.6661 7.70777 10.851 7.70478C11.0359 7.70179 11.2167 7.64963 11.3748 7.55366C11.5328 7.45769 11.6625 7.32136 11.7505 7.15868C11.8384 6.99601 11.8815 6.81285 11.8752 6.62802C11.869 6.4432 11.8136 6.26337 11.7149 6.10701C11.6161 5.95065 11.4775 5.8234 11.3133 5.73833L11.2192 5.69583L12.2658 4.85917L12.3333 4.79583C12.435 4.68598 12.4938 4.54331 12.4992 4.39375C12.5045 4.24419 12.456 4.09767 12.3625 3.98083" fill="#F12F35"/>
                                    </svg>
                                </span>
                                <span class="b3-text fw-medium">{{ trans('dashboard.negative_fb') }}</span>
                            </div>

                            <div id="negativeFeedback"></div>
                        </div>

                    </div>
                </div>

                {{-- Leaderboard --}}
                   <div class="leaderboard-wrapper mt-3">
                        <div class="leaderboard-header mb-2">
                            <div class="b3-text fw-medium"> {{ trans('dashboard.technician_leader_board') }}</div>
                                <button class="view-all-btn px-3 py-1 b5-text fw-medium border-0 rounded-pill cursor-pointer"
                                    onclick="window.open('{{ url('technician-leaderboard') }}', '_blank')">
                                     {{ trans('dashboard.view_all') }}
                                </button>                        
                            </div>

                        <div class="card-row" id="leaderboardContainer"></div>
                    </div>
                </div>

                {{-- Map --}}
                <div class="amg-dashboard-card mb-5">
                    <div class="sp sb" style="padding-bottom:12px;">
                        <span class="b1-text fw-medium"> {{ trans('dashboard.ticket_by_places') }}</span>
                    </div>

                    <div style="padding:12px 16px;">
                        <div id="ticketLocationMap" style="height:350px;"></div>
                    </div>
                </div>

            </div>{{-- /std-left --}}

            {{-- ═══════════════════════════ RIGHT ASIDE ═══════════════════════════ --}}
            <div class="std-right">

                {{-- AI Recommended Actions --}}
                {{-- <div class="sc">
                    <div class="sp">
                        <div class="sh">
                            <div class="st">
                                <div class="ai-dot"><svg viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="6" fill="white" />
                                    </svg></div>
                                AI Recommended actions
                            </div>
                            <button class="sm">⋯</button>
                        </div>
                        @foreach ([['user', 'Assign 12 unassigned tickets immediately'], ['alert', 'Prioritize high priority network tickets'], ['clock', 'Escalate SLA risk tickets within 2 hours']] as [$ic, $txt])
                            <div class="rec-i">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    @if ($ic === 'user')
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                        <circle cx="12" cy="7" r="4" />
                                    @elseif($ic === 'alert')
                                        <circle cx="12" cy="12" r="10" />
                                        <line x1="12" y1="8" x2="12" y2="12" />
                                        <line x1="12" y1="16" x2="12.01" y2="16" />
                                    @else
                                        <circle cx="12" cy="12" r="10" />
                                        <polyline points="12 6 12 12 16 14" />
                                    @endif
                                </svg>
                                {{ $txt }}
                            </div>
                        @endforeach
                        <div class="rec-btns">
                            <button class="rec-apply">✓ Apply Recommendation</button>
                            <button class="rec-dismiss">✕ Dismiss</button>
                        </div>
                    </div>
                </div> --}}

                {{-- SLA & Ticket Priority --}}
                <div class="amg-dashboard-card">
                    <div class="">
                        <div class="px-3 pt-3">
                            <span class="s2-text fw-medium"> {{ trans('dashboard.sla_ticket_priority') }}</span>
                        </div>

                        <div id="slaTrendChart" class="px-3 py-3"></div>

                        <div class="sla-n d-flex align-items-center gap-1 px-3 my-0"></div>

                        <div class="sla-s d-flex justify-content-between align-items-center w-100 px-3 my-0" ></div>

                        <div id="slaPriorityContainer" class="px-3 my-0  mt-3 py-1 border-top"></div>
                    </div>
                </div>

                {{-- Ticket Stats --}}
                <div class="amg-dashboard-card">
                    <div class="pb-2 px-3">
                        <div class="pt-3">
                            <span class="s2-text fw-medium"> {{ trans('dashboard.ticket_stats') }}</span>
                        </div>

                        {{-- Sub 1: AI Performance --}}
                        {{-- <div class="ts-sub">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;">
                                <div>
                                    <div class="ts-t">AI Performance</div>
                                    <div class="perf-r">120 : Tickets Resolved by AI</div>
                                    <div class="perf-r">12% : AI Contribution</div>
                                </div>
                                <div class="mati-glow">MATI</div>
                            </div>
                        </div> --}}

                        {{-- Sub 2: Source Trend --}}
                        <div class="border rounded-2 px-3 py-3 my-2 stats-card-gardient">
                            <div class="b3-text fw-medium mb-1"> {{ trans('dashboard.ticket_source_trend') }}</div>

                            <div class="d-flex align-items-center gap-2">

                                <div id="sourceList" style="flex:1;"></div>

                                <div id="sourceChart" style="width:120px;height:120px;"></div>

                            </div>
                        </div>
                        {{-- Sub 3: Top Issues --}}
                        <div class="border rounded-2 px-3 py-3 my-2 stats-card-gardient">
                            <div class="b3-text fw-medium mb-1"> {{ trans('dashboard.top_tags') }}</div>

                            <div id="topIssuesContainer"></div>
                        </div>

                    </div>
                </div>

            </div>{{-- /std-right --}}
        </div>{{-- /std-wrap --}}
    </main>
@endsection

@push('css')
    <style>
        /* ── HEADER ─────────────────────────────────────────── */
        .std-drop {
            height: 30px;
            min-height: 30px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 0 10px;
            border: 1.5px solid #00000024;
            border-radius: 8px;
            background: #fff;
            font-size: 13px;
            color: #6b7280;
        }

        .select2-container .select2-selection--single {
            height: 29px !important;
            border: 1.5px solid #00000024 !important;
            border-radius: 8px !important;
            display: flex !important;
            align-items: center !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-left: 10px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 33px !important;
        }

        [data-bs-theme="dark"] .select2-container .select2-selection--single {
            border: 1.5px solid #00000024 !important;
            background-color: var(--dark-secondary) !important;
            color: var(--text-primary) !important;
        }

        [data-bs-theme="dark"] .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--text-primary) !important;
        }

        [data-bs-theme="dark"] .select2-dropdown {
            background-color: var(--dark-secondary) !important;
            border-color: #3a3a3a !important;
        }

        [data-bs-theme="dark"] .select2-results__option {
            color: var(--text-primary) !important;
        }

        [data-bs-theme="dark"] .select2-results__option--highlighted {
            background-color: #374151 !important;
        }

        [data-bs-theme="dark"] .select2-search__field {
            background-color: var(--dark-secondary) !important;
            color: var(--text-primary) !important;
        }
        [data-bs-theme="dark"] .std-drop {
            border: 1.5px solid #00000024;
            background-color: var(--dark-secondary);
            color: var(--text-primary);
        } 

        /* ── OUTER WRAPPER ───────────────────────────────────── */
        .std-wrap {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 16px;
            padding: 14px 16px calc(var(--footer-height, 30px)+20px);
            background: var(--app-bg, #f8f9fa);
            box-sizing: border-box;
            min-width: 0;
            overflow-x: hidden;
            align-items: start;
        }

        [data-bs-theme="dark"] .std-wrap {
            background: var(--app-bg, #141414) !important;
        }

        /* Left & right columns */
        .std-left {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .std-right {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        /* ── CARD BASE ───────────────────────────────────────── */
        .amg-dashboard-card {
            background: var(--app-surface, #fff);
            /* border: 1px solid var(--app-border, #dee2e6); */
            border-radius: 8px;
            box-shadow: 0px 1px 4px 0px rgba(133, 146, 173, 0.2);
            overflow: hidden;
            min-width: 0;
        }

        [data-bs-theme="dark"] .amg-dashboard-card {
            background: #191919;
            border-color: #2a2a2d;
            box-shadow: 0px 1px 4px 0px rgba(0, 0, 0, 0.4);
        }
        .amg-dashboard-card .border,
        .amg-dashboard-card .border-bottom{
            border-color: #F0F0F0 !important;
        }
        [data-bs-theme="dark"] .amg-dashboard-card .border,
        [data-bs-theme="dark"] .amg-dashboard-card .border-bottom{
            border-color: #2a2a2a !important;
        }

        .sp {
            padding: 14px 16px;
        }

        .sh {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .st {
            font-size: 16px;
            font-weight: 600;
            color: var(--app-text, #212529);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        [data-bs-theme="dark"] .st {
            color: var(--text-primary, #fff) !important;
        }

        .sm {
            background: none;
            border: none;
            cursor: pointer;
            color: #9ca3af;
            font-size: 18px;
            line-height: 1;
            padding: 2px 4px;
        }

        .sb {
            border-bottom: 1px solid var(--app-border, #dee2e6);
        }

        [data-bs-theme="dark"] .sb {
            border-color: var(--dark-border, #2a2a2d) !important;
        }

        /* Legend row */
        /* .lr {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            padding: 4px 0;
            color: var(--app-text, #212529);
        }

        [data-bs-theme="dark"] .lr {
            color: var(--text-secondary, #e5e7eb) !important;
        } */

        /* .ll {
            display: flex;
            align-items: center;
            gap: 6px;
        } */

        .amg-dashboard-legend{
            width: 9px;
            height: 9px;
            border-radius: 2px;
            flex-shrink: 0;
        }
        .amg-dashboard-legend.legend-swatch--navy {
        background-color: #07427A; 
        }

        .amg-dashboard-legend.legend-swatch--teal {
        background-color: #99EBE2; 
        }

        .amg-dashboard-legend.legend-swatch--purple {
        background-color: #6175DE; 
        }

        .amg-dashboard-legend.legend-swatch--orange {
        background-color: #FFCD85; 
        }

        /* .ln {
            font-weight: 600;
        } */

        .dashboard-ticket-link,
        .dashboard-status-link,
        .dashboard-trend-link,
        .dashboard-source-link,
        .dashboard-issue-link,
        .dashboard-priority-link,
        .dashboard-sla-link {
            cursor: pointer;
            font-size:12px;
            /* color: grey; */
        }

        /* Donut inner fill */
        .di {
            fill: var(--app-surface, #fff);
        }

        [data-bs-theme="dark"] .di {
            fill: var(--dark-secondary, #2a2a2a) !important;
        }

        .di2 {
            fill: var(--app-surface, #fff);
        }

        [data-bs-theme="dark"] .di2 {
            fill: var(--dark-primary, #191919) !important;
        }

        /* ═══════════════════════════════════════════════════════
        AI BANNER
        ═══════════════════════════════════════════════════════ */
        .ai-banner {
            background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 55%, #c4b5fd 100%);
            border: 1px solid #c4b5fd;
            border-radius: 12px;
            padding: 16px;
        }

        [data-bs-theme="dark"] .ai-banner {
            background: linear-gradient(135deg, #1e1b4b 0%, #2d2060 100%) !important;
            border-color: #4c1d95 !important;
        }

        .ai-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .ai-lbl {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 700;
            color: #3b0764;
        }

        [data-bs-theme="dark"] .ai-lbl {
            color: #c4b5fd !important;
        }

        .ai-dot {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 8px rgba(99, 102, 241, .5);
            flex-shrink: 0;
        }

        .ai-dot svg {
            width: 10px;
            height: 10px;
            fill: #fff;
        }

        .ai-nav button {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .5);
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: #4c1d95;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        [data-bs-theme="dark"] .ai-nav button {
            background: rgba(255, 255, 255, .1) !important;
            color: #c4b5fd !important;
        }

        .ai-tiles {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .ai-tile {
            background: #fff;
            border-radius: 10px;
            padding: 12px 14px;
            position: relative;
            min-width: 0;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(99, 102, 241, .12);
        }

        [data-bs-theme="dark"] .ai-tile {
            background: rgba(255, 255, 255, .08) !important;
            box-shadow: none !important;
        }

        .ai-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 10px;
            white-space: nowrap;
        }

        .ab-g {
            background: #d1fae5;
            color: #065f46;
        }

        .ab-b {
            background: #dbeafe;
            color: #1e40af;
        }

        .ab-r {
            background: #fee2e2;
            color: #991b1b;
        }

        [data-bs-theme="dark"] .ab-g {
            background: #052e16 !important;
            color: #4ade80 !important;
        }

        [data-bs-theme="dark"] .ab-b {
            background: #1e2d4a !important;
            color: #93c5fd !important;
        }

        [data-bs-theme="dark"] .ab-r {
            background: #2d0f0e !important;
            color: #f87171 !important;
        }

        .ai-n {
            font-size: 24px;
            font-weight: 800;
            color: #1e1b4b;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 6px;
        }

        [data-bs-theme="dark"] .ai-n {
            color: #e0d9ff !important;
        }

        .ai-d {
            font-size: 12px;
            color: #5b21b6;
            line-height: 1.45;
            margin: 0;
        }

        [data-bs-theme="dark"] .ai-d {
            color: #a78bfa !important;
        }

        .au {
            color: #22c55e;
        }

        .ad {
            color: #ef4444;
        }

        /* ═══════════════════════════════════════════════════════
        2-COL CHART ROW
        ═══════════════════════════════════════════════════════ */
        .chart-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        /* Donut */
        .dw {
            position: relative;
            flex-shrink: 0;
        }

        .dc {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .dv {
            font-size: 22px;
            font-weight: 800;
            color: var(--app-text, #212529);
            line-height: 1;
        }

        .dl {
            font-size: 10px;
            color: #9ca3af;
            margin-top: 2px;
        }

        [data-bs-theme="dark"] .dv {
            color: var(--text-primary, #fff) !important;
        }
        [data-bs-theme="dark"] .kpi-row {
            border-color: var(--dark-border, #2a2a2a) !important;
        }

        [data-bs-theme="dark"] .apexcharts-tooltip {
            background: #1f2937 !important;
            color: #e5e7eb !important;
            border: 0 !important;
            box-shadow: none;
        }

        /* KPI row */
        .kpi-row{
            display:flex;
            justify-content:space-between;
            border-bottom:1px solid #dee2e6;
        }

        .kpi{
            display:flex;
            flex-direction:column;
            align-items:center;
            font-size:14px;
            font-weight:600;
        }

        .kl{
            margin-top:4px;
            font-size:12px;
            color:#9ca3af;
        }
        [data-bs-theme="dark"] .kpi {
            color: var(--text-primary, #fff) !important;
        }

        /* Pill chart */
        .pill-chart {
            display: flex;
            align-items: flex-end;
            gap: 6px;
            margin-bottom: 8px;
        }

        .pc {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
        }

        .pb-wrap {
            display: flex;
            flex-direction: column-reverse;
            gap: 3px;
            align-items: center;
            width: 100%;
        }

        .pb {
            width: 14px;
            border-radius: 7px;
            min-height: 8px;
            margin: 0 auto;
        }

        .plbl {
            font-size: 9px;
            color: #9ca3af;
            margin-top: 5px;
            white-space: nowrap;
        }

        /* Bar chart */
        .bar-chart {
            display: flex;
            align-items: flex-end;
            gap: 6px;
            height: 90px;
            margin-bottom: 8px;
        }

        .bg {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
        }

        .bv {
            font-size: 9px;
            color: #6b7280;
            font-weight: 600;
        }

        .bb {
            width: 100%;
            border-radius: 4px 4px 0 0;
        }

        /* ═══════════════════════════════════════════════════════
        FEEDBACK  — 2-col, plain list (no sub-cards)
        ═══════════════════════════════════════════════════════ */
        .amg-dashboard-fb-card{
            display:grid;
            grid-template-columns:1fr 1fr;
            border:1px solid #e5e7eb;
            border-radius:8px;
            overflow:hidden;
        }

        [data-bs-theme="dark"] .amg-dashboard-fb-card{
            border:1px solid #2F2F2F;
        }

        .fb-col{
            padding:0;
            /* min-height:280px; */
        }

        .fb-col:first-child{
            border-right:1px solid var(--app-border,#dee2e6);
        }

        [data-bs-theme="dark"] .fb-col:first-child {
            border-color: #2F2F2F;
        }

       .amg-dashboard-fb-card .fb-title{
            display:flex;
            align-items:center;
            gap:10px;
            height:48px;
            padding:0 18px;
            margin:0;
            font-size:14px;
            font-weight:600;
            background:#EFF2FA;
            color: var(--app-text, #212529);
            border-bottom:1px solid #e5e7eb;
        }

        [data-bs-theme="dark"] .amg-dashboard-fb-card .fb-title{
            border-bottom:1px solid #2F2F2F;
        }


        [data-bs-theme="dark"] .fb-title {
            color: var(--text-primary, #fff) !important;
            background:#2A2A2A !important;
        }

        #positiveFeedback,
        #negativeFeedback{
            max-height:220px;
            overflow-y:auto;
            padding:16px 18px;
        }

        .fb-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 14px;
        }

        .fb-item:last-child {
            margin-bottom: 0;
        }

        .fb-av {
            width: 30px;
            height: 30px;
            /* border-radius: 50%;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center; */
        }

        .fb-stars span {
            color: #EEC200 !important;
        }

        .fb-av-initials{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            width:32px;
            height:32px;
            border-radius:50%;
            background-color:#6C63FF;
            color:#fff;
            font-size:12px;
            font-weight:600;
            text-transform:uppercase;
        }
        
        .fb-stars{
            color: #F0F0F0;
            font-size: 14px;
            letter-spacing: 1px;
        }

        [data-bs-theme="dark"] .fb-stars span {
            color: #f59e0b !important;
        }

        .fb-q {
            font-size: 12px;
            color: #374151;
            font-style: italic;
            line-height: 1.4;
            margin-bottom: 2px;
        }

        .fb-by {
            font-size: 11px;
            color: #9ca3af;
        }

        [data-bs-theme="dark"] .fb-q {
            color: var(--text-secondary, #e5e7eb) !important;
        }

        [data-bs-theme="dark"] .fb-by {
            color: var(--text-muted, #757575) !important;
        }
        .fb-av {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 600;
            font-size: 12px;
            overflow: hidden;
        }

        .fb-av-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        /* ═══════════════════════════════════════════════════════
        LEADERBOARD — inside one card, 4 cols no border
        ═══════════════════════════════════════════════════════ */
        /* .lb-row {
            display: flex;
            padding: 14px;
            gap: 0;
        } */
/* 
        .lb-col {
            flex: 1;
            text-align: center;
            padding: 8px;
        } */

        /* .lb-av {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            margin: 0 auto 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 700;
            color: #fff;
        } */

        /* .lb-n {
            font-size: 13px;
            font-weight: 600;
            color: var(--app-text, #212529);
            margin-bottom: 2px;
        }

        .lb-p {
            font-size: 11px;
            color: #9ca3af;
            margin-bottom: 4px;
        }

        .lb-c {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 8px;
        }

        [data-bs-theme="dark"] .lb-n {
            color: var(--text-primary, #fff) !important;
        }

        [data-bs-theme="dark"] .lb-c {
            color: var(--text-muted, #757575) !important;
        } */

        /* .sla-p {
            font-size: 11px;
            font-weight: 600;
            padding: 3px 12px;
            border-radius: 20px;
            display: inline-block;
        } */

        /* .slp-g {
            background: #d1fae5;
            color: #065f46;
        }

        .slp-y {
            background: #fef3c7;
            color: #92400e;
        }

        [data-bs-theme="dark"] .slp-g {
            background: #052e16 !important;
            color: #4ade80 !important;
        }

        [data-bs-theme="dark"] .slp-y {
            background: #3a2a0a !important;
            color: #fbbf24 !important;
        } */

        /* ═══════════════════════════════════════════════════════
        RIGHT ASIDE — AI Rec + SLA + Ticket Stats
        ═══════════════════════════════════════════════════════ */
        /* AI Rec */
        .rec-i {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-size: 12px;
            color: #6b7280;
            padding: 6px 0;
            border-bottom: 1px solid var(--app-border, #dee2e6);
        }

        .rec-i:last-of-type {
            border-bottom: none;
            margin-bottom: 10px;
        }

        .rec-i svg {
            width: 14px;
            height: 14px;
            flex-shrink: 0;
            margin-top: 1px;
            color: #9ca3af;
        }

        [data-bs-theme="dark"] .rec-i {
            color: var(--text-muted, #757575) !important;
            border-color: var(--dark-border, #2a2a2d) !important;
        }

        .rec-btns {
            display: flex;
            gap: 8px;
        }

        .rec-apply {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            background: #065f46;
            color: #fff !important;
            border: none;
            border-radius: 7px;
            padding: 8px;
            font-size: 11.5px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
        }

        .rec-dismiss {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            background: #ef4444;
            color: #fff !important;
            border: none;
            border-radius: 7px;
            padding: 8px;
            font-size: 11.5px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
        }

        /* SLA */
        /* .sla-n{
            display:flex;
            align-items:center;
            gap:6px;
            font-size:20px;
            font-weight:700;
            line-height:1;
            margin:8px 0 4px;
            color:var(--app-text,#111827);
        } */

        /* [data-bs-theme="dark"] .sla-n {
            color: var(--text-primary, #fff) !important;
        } */
        
        /* .sla-s{
            display:flex;
            justify-content:space-between;
            align-items:center;
            width:100%;
        } */

        /* .prow {
            margin-bottom: 10px;
        } */

        /* .plbl {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: var(--app-text, #212529);
            opacity:.8;
            margin-bottom: 5px;
        } */

        /* [data-bs-theme="dark"] .plbl {
            color: var(--text-secondary, #e5e7eb) !important;
        } */

        /* .prog {
            height: 5px;
            border-radius: 0px;
            background: #f3f4f6;
            opacity:.8;
            overflow: hidden;
        } */

        .amg-progress-bar{
            height: 5px; 
            background: #f3f4f6;
        }

        [data-bs-theme="dark"] .amg-progress-bar {
            background: var(--dark-hover, #262626) !important;
        }

        /* .progb {
            height: 100%;
            border-radius: 0px;
        } */

        /* Ticket Stats — 3 sub-cards */
        .ts-sub {
            background: var(--app-surface, #fff);
            border: 1px solid var(--app-border, #dee2e6);
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 10px;
        }

        .ts-sub:last-child {
            margin-bottom: 0;
        }

        [data-bs-theme="dark"] .ts-sub {
            background: var(--dark-primary, #191919) !important;
            border-color: var(--dark-border, #2a2a2d) !important;
        }

        .ts-t {
            font-size: 13px;
            font-weight: 700;
            color: var(--app-text, #212529);
            margin-bottom: 8px;
        }

        [data-bs-theme="dark"] .ts-t {
            color: var(--text-primary, #fff) !important;
        }

        /* MATI glowing badge */
        .mati-glow {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1a1060, #3730a3, #6366f1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9px;
            font-weight: 800;
            color: #fff;
            letter-spacing: .5px;
            flex-shrink: 0;
            box-shadow: 0 0 16px rgba(99, 102, 241, .75), 0 0 32px rgba(99, 102, 241, .3);
        }

        .perf-r {
            font-size: 12px;
            color: #6b7280;
            padding: 2px 0;
        }

        [data-bs-theme="dark"] .perf-r {
            color: var(--text-muted, #757575) !important;
        }

        .src-r {
            display: flex;
            align-items: center;
            font-size: 12px;
            padding: 3px 0;
        }

        .src-l {
            display: flex;
            align-items: center;
            gap: 7px;
            color: var(--app-text, #212529);
        }

        [data-bs-theme="dark"] .src-l {
            color: var(--text-secondary, #e5e7eb) !important;
        }

        .src-d {
            width: 8px;
            height: 8px;
            border-radius: 2px;
            flex-shrink: 0;
        }

        .src-n {
            font-weight: 600;
            min-width: 24px;
        }

        .src-lab {
            color: #9ca3af;
            font-size: 11.5px;
        }

        /* Top Issues tags — pink/rose */
        .itag {
            display: inline-flex;
            align-items: center;
            background: #FFE5E5;
            color: #F12F35 !important;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 400;
            padding: 4px 16px;
            margin: 3px 3px 0 0;
            white-space: nowrap;
        }

        [data-bs-theme="dark"] .itag {
            background: #4a0d2e !important;
            color: #f9a8d4 !important;
        }
        #amg-company-select+.select2-container--default .select2-selection--single .select2-selection__arrow b {
            margin-top: -4px !important;
        }
        /* Sdot for source donut */
        .sdot {
            fill: var(--app-surface, #fff);
        }

        [data-bs-theme="dark"] .sdot {
            fill: var(--dark-primary, #191919) !important;
        }

        /* Map */
        .map-wrap {
            border-radius: 8px;
            overflow: hidden;
        }

        /* ── RESPONSIVE ─────────────────────────────────────── */
        @media (max-width:1199px) {
            .std-wrap {
                grid-template-columns: 1fr 250px;
            }
        }

        @media (max-width:991px) {
            .std-wrap {
                grid-template-columns: 1fr;
                padding: 10px;
            }

            .chart-row {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width:767px) {
            .chart-row {
                grid-template-columns: 1fr;
            }

            .ai-tiles {
                grid-template-columns: 1fr;
            }

            .amg-dashboard-fb-card {
                grid-template-columns: 1fr;
            }

            .fb-col:first-child {
                border-right: none;
                border-bottom: 1px solid var(--app-border, #dee2e6);
            }

            /* .lb-row {
                flex-wrap: wrap;
            } */

            /* .lb-col {
                min-width: 45%;
            } */

            .std-wrap {
                padding: 8px 8px 80px;
            }
        }
        #issueList {
            max-height: 100px;  
            overflow-y: auto;
            padding-right: 10px;
        }

        #issueList::-webkit-scrollbar {
            width: 6px;
        }

        .stats-card-gardient {
            background: linear-gradient(to left, #EFF2FA, #FFFFFF);
        }
        [data-bs-theme=dark] .stats-card-gardient {
            background: linear-gradient(to left, #2a2a2a, #191919);
        }

        #issueList::-webkit-scrollbar-thumb {
            background: #F1F1F1;
            border-radius: 10px;
        }
           .leaderboard-wrapper {
            /* background: #ffffff; */
            /* border-radius: 16px; */
            /* padding: 24px 28px 32px; */
        }

        .leaderboard-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .leaderboard-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .view-all-btn {
            background: #F2E5FF;
            color: #5C00E5;
        }
        [data-bs-theme="dark"] .view-all-btn {
            background: #5C00E5;
            color: #F2E5FF;
        }

        .card-row {
            display: flex;
            gap: 18px;
        }

        .tech-card {
            flex: 1;
            border: 1px solid #F0F0F0;
            border-radius: 8px;
            padding: 20px 16px 18px;
            text-align: center;
            background: #FFFFFF;
        }

        [data-bs-theme=dark] .tech-card {
            border: 1px solid #2F2F2F;
            background: #191919;
        }

        .avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 12px;
            display: block;
        }

        .tech-name {
            font-size: 15px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 4px;
        }

        .tech-place {
            font-size: 13px;
            color: #9aa0ab;
            margin-bottom: 9px;
        }

        .tickets-resolved {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .sla-badge {
            display: inline-block;
            background: #C8EDCE;
            color: #186B43;
            font-size: 13px;
            font-weight: 600;
            padding: 6px 30px;
            border-radius: 4px;
            /* width: 80%; */
        }
        [data-bs-theme=dark] .sla-badge {
            color: #186B43 !important;
        }
       
    </style>
@endpush

@push('scripts')
    <script type="text/javascript" src="{!! CommonHelper::asset('js/dashboard/ticket/index.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/amchart/index.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/amchart/map.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/amchart/animation.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/amchart/worldLow.js') !!}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            var config = new Object;
            config.url = new Object;
            config.url.dailyTicketOverview = "{{ url('dashboard/daily-ticket-overview') }}";
            config.url.ticketStatusTrend ="{{ url('dashboard/ticket-status-trend') }}";
            config.url.ticketTrendAnalytics ="{{ url('dashboard/trend-analytics') }}";
            config.url.ticketSourceTrend ="{{ url('dashboard/ticket-source-trend') }}";
            config.url.ticketFeedback ="{{ url('dashboard/feedback-dashboard') }}";
            config.url.issueType ="{{ url('dashboard/issue-type') }}";
            config.url.getSlaPriority ="{{ url('dashboard/slaPriority') }}";
            config.url.getTopIssue ="{{ url('dashboard/topIssue') }}";
            config.url.ticketList = "{{ url('tickets/newlist/all-tickets') }}";
            config.url.getTechnicianLeaderboard = "{{ url('dashboard/getTechnicianLeaderboard') }}";
            config.url.getTicketByPlaces = "{{ url('dashboard/getTicketByPlaces') }}";

            new MyApp(config);	
        });
    </script>
@endpush


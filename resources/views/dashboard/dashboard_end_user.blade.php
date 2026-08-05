{{-- @page-meta 
    { 
        "page_no": "STD-01", 
        "version": "4.0" 
    },
 --}}
@extends('layouts.layout1')
@section('title', 'Service Ticket Dashboard')
@section('content')
<div id="main-user-list-wrapper">
    <section class="content">    
        {{-- ① HEADER --}}
        <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
            <h3 class="h3-text mb-0">{{ trans('dashboard.user_dashboard') }}</h3>
        </div>
        {{-- ② MAIN —— LEFT + RIGHT grid --}}
        <main class="main-content" id="mainContent">
            <div class="std-wrap" id="userDash">
                <div class="std-left">
                    <div class="ai-banner">
                        <div class="ai-top">
                            <div class="ai-lbl">
                                <div><img src="{{ asset('images/mati_text.png') }}" class="img-fluid" alt="Logo" width="30px"></img></div>
                                {{ trans('dashboard.my_tickets') }}
                            </div>
                           
                           
                            <div class="ai-nav" aria-label="Ticket status summary navigation">
                                <button type="button" class="ai-nav-btn ai-prev" aria-label="Previous">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 6 9 12l6 6"/></svg>
                                </button>
                                <button type="button" class="ai-nav-btn ai-next" aria-label="Next">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="ai-tiles" id="myTicketStatusTiles">
                            <div class="ai-tile" data-count-source="#myTicket_total_tickets">
                                <a href="{{ url('tickets/newlist/my-tickets') }}" target="_blank">
                                    <div class="ai-stat-row">
                                        <div class="ai-n" id="myTicket_total_tickets">0</div>
                                        <span class="ai-badge ab-g"><span class="ai-count-value">0</span> Tickets</span>
                                    </div>
                                    <p class="ai-d">{{ trans('dashboard.total_ticket') }} </p>
                                </a>
                            </div>
                            <div class="ai-tile" data-count-source="#myTicket_total_open">
                                <a href="{{ url('tickets/newlist/my-tickets?status=1') }}" target="_blank">
                                    <div class="ai-stat-row">
                                        <div class="ai-n" id="myTicket_total_open">0</div>
                                        <span class="ai-badge ab-g"><span class="ai-count-value">0</span> Tickets</span>
                                    </div>
                                    <p class="ai-d">{{ trans('dashboard.open_ticket') }}</p>
                                </a>
                            </div>
                            <div class="ai-tile" data-count-source="#myTicket_total_in_progress">
                                <a href="{{ url('tickets/newlist/my-tickets?status=3') }}" target="_blank">
                                    <div class="ai-stat-row">
                                        <div class="ai-n" id="myTicket_total_in_progress">0</div>
                                        <span class="ai-badge ab-r"><span class="ai-count-value">0</span> Tickets</span>
                                    </div>
                                    <p class="ai-d">{{ trans('dashboard.in_progress_ticket') }}</p>
                                </a>
                            </div>
                            <div class="ai-tile" data-count-source="#myTicket_total_resolved">
                                <a href="{{ url('tickets/newlist/my-tickets?status=5') }}" target="_blank">
                                    <div class="ai-stat-row">
                                        <div class="ai-n" id="myTicket_total_resolved">0</div>
                                        <span class="ai-badge ab-g"><span class="ai-count-value">0</span> Tickets</span>
                                    </div>
                                    <p class="ai-d">{{ trans('dashboard.resolved_ticket') }}</p>
                                </a>
                            </div>
                            <div class="ai-tile" data-count-source="#myTicket_waiting_for_user">
                                <a href="{{ url('tickets/newlist/my-tickets?status=7') }}" target="_blank">
                                    <div class="ai-stat-row">
                                        <div class="ai-n" id="myTicket_waiting_for_user">0</div>
                                        <span class="ai-badge ab-r"><span class="ai-count-value">0</span> Tickets</span>
                                    </div>
                                    <p class="ai-d">{{ trans('dashboard.waiting_for_user_ticket') }}</p>
                                </a>
                            </div>
                            <div class="ai-tile" data-count-source="#myTicket_waiting_for_vendor">
                                <a href="{{ url('tickets/newlist/my-tickets?status=8') }}" target="_blank">
                                    <div class="ai-stat-row">
                                        <div class="ai-n" id="myTicket_waiting_for_vendor">0</div>
                                        <span class="ai-badge ab-r"><span class="ai-count-value">0</span> Tickets</span>
                                    </div>
                                    <p class="ai-d">{{ trans('dashboard.waiting_for_vendor_ticket') }}</p>
                                </a>
                            </div>
                            <div class="ai-tile" data-count-source="#myTicket_total_closed">
                                <a href="{{ url('tickets/newlist/my-tickets?status=6') }}" target="_blank">
                                    <div class="ai-stat-row">
                                        <div class="ai-n" id="myTicket_total_closed">0</div>
                                        <span class="ai-badge ab-g"><span class="ai-count-value">0</span> Tickets</span>
                                    </div>
                                    <p class="ai-d">{{ trans('dashboard.closed_ticket') }}</p>
                                </a>
                            </div>
                            <div class="ai-tile hide" data-count-source="#myTicket_total_spam">
                                <a href="{{ url('tickets/newlist/my-tickets?status=10') }}" target="_blank">
                                    <div class="ai-stat-row">
                                        <div class="ai-n" id="myTicket_total_spam">0</div>
                                        <span class="ai-badge ab-r"><span class="ai-count-value">0</span> Tickets</span>
                                    </div>
                                    <p class="ai-d">{{ trans('dashboard.spam_ticket') }}</p>
                                </a>
                            </div>
                            <div class="ai-tile" data-count-source="#myTicket_total_reopened">
                                <a href="{{ url('tickets/newlist/my-tickets?status=2') }}" target="_blank">
                                    <div class="ai-stat-row">
                                        <div class="ai-n" id="myTicket_total_reopened">0</div>
                                        <span class="ai-badge ab-r"><span class="ai-count-value">0</span> Tickets</span>
                                    </div>
                                    <p class="ai-d">{{ trans('dashboard.reopened_ticket') }}</p>
                                </a>
                            </div>
                        </div>

                    </div>

                    <div class="sc">
                        <div class="sp sb" style="padding-bottom:12px;">
                            <div style="display:flex;align-items:center;justify-content:space-between;">
                                <span class="st">{{ trans('dashboard.my_latest_active_tickets') }}</span>
                            </div>
                        </div>
                         <div class="js-user-list-view-panel">
                            <div class="table-responsive">
                                <table id="mytable" class="table table-striped table-hover display app-data-table">
                                    <thead>
                                        <tr>
                                            <th><h4>{{ trans("content.service_ticket_fields.Ticket_Id") }}</h4></th>
                                            <th><h4>{{ trans("content.ticket_procurement.Subject") }}</h4></th>
                                            <th><h4>{{ trans("content.ticket_procurement.Department") }}</h4></th>
                                            <th><h4>{{ trans("content.service_ticket_fields.Priority") }}</h4></th>
                                            <th><h4>{{ trans("content.ticket_procurement.Status") }}</h4></th>
                                            <th><h4>{{ trans("content.ticket_procurement.Created_Date") }}</h4></th>
                                            <th><h4>{{ trans("content.ticket_procurement.Updated_Date") }}</h4></th>
                                        </tr>
                                    </thead>
                                    <tbody id="myTicketLits">
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-lg-4 hide">
                                <div id="dt" name="dt"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-1 shadow-sm rating-overview-section">
                        <div class="sp sb" style="padding-bottom:12px;">
                            <div style="display:flex;align-items:center;justify-content:space-between;">
                                <span class="st ms-2">{{ trans('dashboard.rating_overview') }}</span>
                            </div>
                        </div>
                            
                        <div class="card-body p-3">
                            {{-- Grid Section --}}
                            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3 justify-content-evenly pb-2">
                                <div class="col">
                                    <div class="h-100 text-center hover-lift">
                                        <div class="card-body d-flex flex-column align-items-center p-0 justify-content-center">
                                            
                                            {{-- Profile Image Container --}}
                                            <div class="rounded-circle d-flex align-items-center justify-content-center mb-3 overflow-hidden border border-2 border-white" 
                                                style="width: 56px; height: 56px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); background-color: #f3f4f6;">
                                                <span style="font-size: 40px;">⭐</span>
                                            </div>

                                            <h6 class="fw-bold mb-1 text-dark">{{ trans('dashboard.overall_rating') }}</h6>
                                            <span class="text-muted small mb-2">{{ trans('dashboard.rating') }}</span>
                                            <p class="fw-semibold text-secondary mb-0" style="font-size: 13px;" id="myTickets_rating_overall">0</p>
                                            
                                        </div>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="h-100 text-center hover-lift">
                                        <div class="card-body d-flex flex-column align-items-center p-0 justify-content-center">
                                            
                                            {{-- Profile Image Container --}}
                                            <div class="rounded-circle d-flex align-items-center justify-content-center mb-3 overflow-hidden border border-2 border-white" 
                                                style="width: 56px; height: 56px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); background-color: #f3f4f6;">
                                                 <span style="font-size: 40px;">🤩</span>
                                            </div>

                                            <h6 class="fw-bold mb-1 text-dark">{{ trans('dashboard.highest_rating') }}</h6>
                                            <span class="text-muted small mb-2">{{ trans('dashboard.rating') }}</span>
                                            <p class="fw-semibold text-secondary mb-0" style="font-size: 13px;" id="myTickets_rating_highest">0</p>
                                            
                                        </div>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="h-100 text-center hover-lift">
                                        <div class="card-body d-flex flex-column align-items-center p-0 justify-content-center">
                                            
                                            {{-- Profile Image Container --}}
                                            <div class="rounded-circle d-flex align-items-center justify-content-center mb-3 overflow-hidden border border-2 border-white" 
                                                style="width: 56px; height: 56px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); background-color: #f3f4f6;">
                                                <span style="font-size: 40px;">😞</span>
                                            </div>

                                            <h6 class="fw-bold mb-1 text-dark">{{ trans('dashboard.last_rating') }}</h6>
                                            <span class="text-muted small mb-2">{{ trans('dashboard.rating') }}</span>
                                            <p class="fw-semibold text-secondary mb-0" style="font-size: 13px;" id="myTickets_rating_last">0</p>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(config("services.knowledge_document.enabled") == 1 && in_array('KnowledgeDocumentRead', $permissionArray))
                    <div class="card border-1 shadow-sm mb-4" id="main-knowledge-document-wrapper">
                         <div class="sp sb" style="padding-bottom:12px;">
                            <div style="display:flex;align-items:center;justify-content:space-between;">
                                <span class="st ps-0">{{ trans('dashboard.knowledge_document') }}</span>
                                <div class="amg-list-searchbar">
                                    <button type="button" class="amg-list-searchbar__icon-btn btn-searchbox" aria-label="Search Ticket Status" title="{{ trans('content.service_ticket_fields.search') }}">
                                        <svg class="amg-list-searchbar__icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                        </svg>
                                    </button>
                                    <input type="text" name="search" class="amg-list-searchbar__input kdsearch plain-search" placeholder="{{ trans('content.service_ticket_fields.search') }}" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            {{-- Header Section --}}
                            {{-- <div class="d-flex align-items-center justify-content-between mb-4">
                                <span class="st">{{ trans('dashboard.knowledge_document') }}</span>
                                <div class="position-relative" style="min-width: 240px;">
                                    <input type="search" 
                                        class="form-control form-control-sm kdsearch" 
                                        placeholder="{{ trans('content.service_ticket_fields.search') }}" 
                                        style="padding-left: 36px; border-radius: 20px;" />
                                    <i class="bi bi-search position-absolute" 
                                    style="left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 13px;"></i>
                                </div>
                            </div> --}}

                            {{-- Articles Grid Container --}}
                            <div class="row" id="home"></div>

                            {{-- Pagination Section --}}
                            <div class="row align-items-center mt-4 pt-3 border-top">
                                <div class="col-lg-5 col-md-6">
                                    <div id="page-btm-summary" class="text-muted small"></div>
                                </div>
                                <div class="col-lg-7 col-md-6">
                                    <div id="pagebtns" class="d-flex justify-content-lg-end justify-content-md-start"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>{{-- /std-left --}}
            </div>{{-- /std-wrap --}}
        </main>
         @include('dashboard.termandcondtion')
    </section>
</div>
@endsection
@push('css')
<link href="{!! CommonHelper::asset('plugins/simple_pagination/pagination.css') !!}" rel="stylesheet" />
<style>
    [data-bs-theme="dark"] #main-knowledge-document-wrapper {
        --page-bg: var(--app-bg);
        --arrow-surface: var(--app-bg);
        --text: var(--text-primary);
        --muted: var(--text-muted);
        --line: var(--dark-border);
        --chip-bg: var(--dark-secondary);
        --chip-text: var(--text-secondary);
        --chip-border: var(--dark-tertiary);
        --chip-more-text: var(--text-icon);
        --field-bg: var(--dark-secondary);
        --field-border: var(--dark-border);
        --icon-muted: var(--text-icon);
        --hero-fallback: var(--dark-secondary);
    }
    .kd_details_cards {
        margin: 10px;
    }

    #main-knowledge-document-wrapper{
        border-radius: 0.75rem !important;
    }

    .card-container {
        display: flex;
        flex-wrap: wrap;
    }

    .kd-stack {
        position: relative;
        height: 100%;
    }

    .kd-card-image {
        position: relative;
        min-height: 24.5625rem;
        border-radius: 12px;
        overflow: hidden;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        cursor: pointer;
    }

    .kd-card-image::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(
            to top,
            rgba(0,0,0,.75),
            rgba(0,0,0,.15)
        );
    }

    .kd-overlay {
        position: absolute;
        left: 15px;
        right: 15px;
        bottom: 15px;
        z-index: 2;
    }

    .h4-text {
        color: #fff;
        font-size: 16px;
        font-weight: 600;
        line-height: 1.4;
    }

    .kd-pill {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 4px;
        background: rgba(255,255,255,.15);
        color: #fff;
        border: 1px solid rgba(255,255,255,.3);
        font-size: 12px;
        backdrop-filter: blur(4px);
    }

    .kd-arrow {
        position: absolute;
        right: -0.700rem;
        bottom: -0.625rem;
        z-index: 5;
        width: 3.75rem;
        height: 3.625rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-top-left-radius: 1.125rem;
        background: var(--arrow-surface, #fff);
    }

    .kd-arrow::before,
    .kd-arrow::after {
        position: absolute;
        content: "";
        width: 1.375rem;
        height: 1.375rem;
        background: transparent;
        border-bottom-right-radius: 1.125rem;
        box-shadow: 0.4375rem 0.4375rem var(--arrow-surface, #fff);
    }

    .kd-arrow::before {
        left: -1.375rem;
        bottom: 0.625rem;
    }

    .kd-arrow::after {
        right: 0.700rem;
        top: -1.375rem;
    }

    .kd-arrow svg {
        position: relative;
        z-index: 1;
        width: 2.75rem;
        height: 2.5rem;
    }

    .kd-side-image .kd-arrow {
        right: -0.75rem;
        bottom: -0.5625rem;
        width: 2.625rem;
        height: 2.625rem;
        border-top-left-radius: 0.75rem;
    }

    .kd-side-image .kd-arrow::before,
    .kd-side-image .kd-arrow::after {
        width: 1.375rem;
        height: 1.375rem;
        border-bottom-right-radius: 0.6875rem;
        box-shadow: 0.3125rem 0.3125rem var(--arrow-surface, #fff);
    }

    .kd-side-image .kd-arrow::before {
        left: -0.9375rem;
        bottom: 0.5625rem;
    }

    .kd-side-image .kd-arrow::after {
        right: 0.75rem;
        top: -0.9375rem;
    }

    .kd-side-image .kd-arrow svg {
        width: 1.875rem;
        height: 1.75rem;
    }
    .no-record-found {
        text-align:center;
    }

    #pagebtns {
        display: flex;
        justify-content: end;
        align-items: center;
    }

    #pagebtns ul {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
        gap: 4px;
    }

    #pagebtns li a,
    #pagebtns li span {
        display: block;
        padding: 6px 12px;
        border: 1px solid var(--bs-border-color);
        border-radius: var(--bs-border-radius);
        text-decoration: none;
    }

    #pagebtns li.active a,
    #pagebtns li.active span,
    #pagebtns .current {
        color: #fff !important;
    }

    #pagebtns li.disabled a,
    #pagebtns li.disabled span {
        opacity: .5;
        pointer-events: none;
    }

    #pagebtns li a:hover {
        color: var(--bs-primary);
    }
    @media (max-width: 991px) {
        .col-md-3 {
            width: 50%;
        }
    }

    @media (max-width: 767px) {
        .col-md-3 {
            width: 100%;
        }

        .kd-card-image {
            min-height: 220px;
        }
    }
       .std-hdr {
        background:var(--app-surface,#fff);
        border-bottom:1px solid var(--app-border,#dee2e6);
        min-height:57px;
        display:flex !important; align-items:center !important;
        justify-content:space-between !important;
        flex-wrap:nowrap !important; gap:12px;
        padding-right:1.25rem !important;
    }
    [data-bs-theme="dark"] .std-hdr {
        background:var(--dark-primary,#191919) !important;
        border-color:var(--dark-border,#2a2a2d) !important;
    }
    .std-hdr-t { display:flex;align-items:center;gap:10px;font-size:15px;font-weight:700;color:var(--app-text,#212529); }
    .std-hdr-t a { color:inherit;text-decoration:none;display:inline-flex;align-items:center; }
    .std-hdr-t a svg { width:16px;height:16px; }
    [data-bs-theme="dark"] .std-hdr-t { color:var(--text-primary,#fff) !important; }
    .std-drop {
        display:inline-flex;align-items:center;gap:6px;
        border:1px solid var(--app-border,#dee2e6);border-radius:8px;
        padding:6px 12px;font-size:12.5px;font-family:inherit;
        color:var(--app-text,#212529) !important;background:var(--app-surface,#fff);
        cursor:pointer;white-space:nowrap;
    }
    .std-drop svg { width:12px;height:12px;color:#9ca3af; }
    [data-bs-theme="dark"] .std-drop {
        background:var(--dark-primary,#191919) !important;
        border-color:var(--dark-border,#2a2a2d) !important;
        color:var(--text-secondary,#e5e7eb) !important;
    }

    /* ── OUTER WRAPPER ───────────────────────────────────── */
    .std-wrap {
        display:grid;
        /* grid-template-columns:1fr 280px; */
        gap:16px;
        padding:14px 16px calc(var(--footer-height,30px)+20px);
        background:var(--app-bg,#f8f9fa);
        box-sizing:border-box;
        min-width:0; overflow-x:hidden;
        align-items:start;
        margin: 35px;
    }
    [data-bs-theme="dark"] .std-wrap { background:var(--app-bg,#141414) !important; }

    /* Left & right columns */
    .std-left  { min-width:0;display:flex;flex-direction:column;gap:14px; }
    .std-right { min-width:0;display:flex;flex-direction:column;gap:14px; }

    /* ── CARD BASE ───────────────────────────────────────── */
    .sc {
        background:var(--app-surface,#fff);
        border:1px solid var(--app-border,#dee2e6);
        border-radius:0.75rem; overflow:hidden;
        min-width:0;
    }
    [data-bs-theme="dark"] .sc {
        background:var(--dark-secondary,#2a2a2a) !important;
        border-color:var(--dark-border,#2a2a2d) !important;
    }
    .sp { padding:14px 16px;  background-color: #eff2fa !important; border-top-right-radius: 0.75rem; border-top-left-radius: 0.75rem;}
    .sh { display:flex;align-items:center;justify-content:space-between;margin-bottom:14px; }
    .st { font-size:13.5px;font-weight:700;color:var(--app-text,#212529);display:flex;align-items:center;gap:6px; margin-left: 1rem; margin-top: .5rem; }
    [data-bs-theme="dark"] .st { color:var(--text-primary,#fff) !important; }
    .sm { background:none;border:none;cursor:pointer;color:#9ca3af;font-size:18px;line-height:1;padding:2px 4px; }
    .sb { border-bottom:1px solid var(--app-border,#dee2e6); }
    [data-bs-theme="dark"] .sb { border-color:var(--dark-border,#2a2a2d) !important; }

    /* Legend row */
    .lr { display:flex;align-items:center;justify-content:space-between;font-size:12px;padding:4px 0;color:var(--app-text,#212529); }
    [data-bs-theme="dark"] .lr { color:var(--text-secondary,#e5e7eb) !important; }
    .ll { display:flex;align-items:center;gap:7px; }
    .ld { width:10px;height:10px;border-radius:2px;flex-shrink:0; }
    .ln { font-weight:600; }

    /* Donut inner fill */
    .di { fill:var(--app-surface,#fff); }
    [data-bs-theme="dark"] .di { fill:var(--dark-secondary,#2a2a2a) !important; }
    .di2 { fill:var(--app-surface,#fff); }
    [data-bs-theme="dark"] .di2 { fill:var(--dark-primary,#191919) !important; }

    /* ═══════════════════════════════════════════════════════
    AI BANNER
    ═══════════════════════════════════════════════════════ */
    .ai-banner {
        background:linear-gradient(106deg,#f1e2ff 0%,#dce2ff 52%,#e6b8ff 100%);
        border:none;
        border-radius:.75rem;
        box-shadow:0 8px 16px rgba(93,70,140,.12);
        padding:28px 26px 26px;
        overflow:hidden;
    }
    [data-bs-theme="dark"] .ai-banner {
        background:linear-gradient(106deg,#32234c 0%,#24304f 52%,#4a2a5c 100%) !important;
    }
    .ai-top {
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:16px;
        margin-bottom:22px;
    }
    .ai-lbl {
        display:flex;
        align-items:center;
        gap:12px;
        font-size:22px;
        font-weight:500;
        line-height:1.2;
        color:#111827;
    }
    [data-bs-theme="dark"] .ai-lbl { color:#f8fafc !important; }
    .ai-dot {
        width:24px;
        height:24px;
        border-radius:50%;
        background:radial-gradient(circle at 38% 34%,#0fb9ff 0 18%,#022de0 34%,#010935 70%);
        border:1px solid rgba(255,255,255,.72);
        display:flex;
        align-items:center;
        justify-content:center;
        box-shadow:0 1px 7px rgba(2,45,224,.35);
        flex-shrink:0;
    }
    .ai-dot::before {
        content:"AI";
        color:#fff;
        font-size:7px;
        font-weight:800;
        letter-spacing:0;
        line-height:1;
    }
    .ai-dot svg { display:none; }
    .ai-nav {
        display:flex;
        align-items:center;
        gap:18px;
        flex-shrink:0;
    }
    .ai-nav-btn {
        width:16px;
        height:26px;
        padding:0;
        border:0;
        background:transparent;
        color:#33343b;
        cursor:pointer;
        display:inline-flex;
        align-items:center;
        justify-content:center;
    }
    .ai-nav-btn svg {
        width:24px;
        height:24px;
        fill:none;
        stroke:currentColor;
        stroke-width:1.8;
        stroke-linecap:round;
        stroke-linejoin:round;
    }
    .ai-nav-btn:hover { color:#111827; }
    [data-bs-theme="dark"] .ai-nav-btn { color:#f8fafc !important; }
    .ai-tiles {
        --ai-tile-gap:16px;
        --ai-visible-tiles:4;
        display:flex;
        gap:var(--ai-tile-gap);
        overflow-x:auto;
        overflow-y:hidden;
        scroll-behavior:smooth;
        scroll-snap-type:x mandatory;
        scrollbar-width:none;
        padding:0 0 1px;
    }
    .ai-tiles::-webkit-scrollbar { display:none; }
    .ai-tile {
        flex:0 0 calc((100% - (var(--ai-tile-gap) * (var(--ai-visible-tiles) - 1))) / var(--ai-visible-tiles));
        min-height:128px;
        background:#fff;
        border-radius:10px;
        padding:23px 22px 20px;
        min-width:0;
        overflow:hidden;
        scroll-snap-align:start;
    }
    .ai-tile.hide { display:none !important; }
    .ai-tile a {
        display:flex;
        flex-direction:column;
        height:100%;
        color:inherit;
        text-decoration:none;
    }
    [data-bs-theme="dark"] .ai-tile { background:rgba(255,255,255,.1) !important; }
    .ai-stat-row {
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:14px;
        margin-bottom:12px;
    }
    .ai-badge {
        border-radius:999px;
        font-size:11px;
        font-weight:700;
        line-height:1;
        padding:5px 9px;
        white-space:nowrap;
        margin-top:3px;
    }
    .ab-g { background:#dcfce7;color:#0a8f39; }
    .ab-b { background:#dbeafe;color:#1e40af; }
    .ab-r { background:#ffe1e5;color:#ff3b30; }
    [data-bs-theme="dark"] .ab-g { background:#052e16 !important;color:#4ade80 !important; }
    [data-bs-theme="dark"] .ab-b { background:#1e2d4a !important;color:#93c5fd !important; }
    [data-bs-theme="dark"] .ab-r { background:#3f1418 !important;color:#fca5a5 !important; }
    .ai-n {
        font-size:24px;
        font-weight:700;
        color:#050505;
        line-height:1;
        min-width:0;
    }
    [data-bs-theme="dark"] .ai-n { color:#f8fafc !important; }
    .ai-d {
        color:#7b7b7b;
        font-size:16px;
        line-height:1.42;
        margin:0;
        max-width:270px;
    }
    [data-bs-theme="dark"] .ai-d { color:#d1d5db !important; }

    /* ═══════════════════════════════════════════════════════
    2-COL CHART ROW
    ═══════════════════════════════════════════════════════ */
    .chart-row { display:grid;grid-template-columns:1fr 1fr;gap:14px; }

    /* Donut */
    .dw { position:relative;flex-shrink:0; }
    .dc { position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center; }
    .dv { font-size:22px;font-weight:800;color:var(--app-text,#212529);line-height:1; }
    .dl { font-size:10px;color:#9ca3af;margin-top:2px; }
    [data-bs-theme="dark"] .dv { color:var(--text-primary,#fff) !important; }

    /* KPI row */
    .kpi-row {
        display:flex;align-items:center;gap:24px;
        padding:10px 0;border-bottom:1px solid var(--app-border,#dee2e6);margin-bottom:10px;
    }
    [data-bs-theme="dark"] .kpi-row { border-color:var(--dark-border,#2a2a2d) !important; }
    .kpi { font-size:14px;font-weight:700;color:var(--app-text,#212529);display:flex;align-items:baseline;gap:4px; }
    .kl  { font-size:11px;color:#9ca3af;font-weight:400; }
    [data-bs-theme="dark"] .kpi { color:var(--text-primary,#fff) !important; }

    /* Pill chart */
    .pill-chart { display:flex;align-items:flex-end;gap:6px;margin-bottom:8px; }
    .pc { display:flex;flex-direction:column;align-items:center;flex:1; }
    .pb-wrap { display:flex;flex-direction:column-reverse;gap:3px;align-items:center;width:100%; }
    .pb { width:14px;border-radius:7px;min-height:8px;margin:0 auto; }
    .plbl { font-size:9px;color:#9ca3af;margin-top:5px;white-space:nowrap; }

    /* Bar chart */
    .bar-chart { display:flex;align-items:flex-end;gap:6px;height:90px;margin-bottom:8px; }
    .bg { flex:1;display:flex;flex-direction:column;align-items:center;gap:3px; }
    .bv { font-size:9px;color:#6b7280;font-weight:600; }
    .bb { width:100%;border-radius:4px 4px 0 0; }

    /* ═══════════════════════════════════════════════════════
    FEEDBACK  — 2-col, plain list (no sub-cards)
    ═══════════════════════════════════════════════════════ */
    .fb-2col { display:grid;grid-template-columns:1fr 1fr; }
    .fb-col  { padding:14px 16px; }
    .fb-col:first-child { border-right:1px solid var(--app-border,#dee2e6); }
    [data-bs-theme="dark"] .fb-col:first-child { border-color:var(--dark-border,#2a2a2d) !important; }
    .fb-title { display:flex;align-items:center;gap:8px;font-size:13px;font-weight:700;color:var(--app-text,#212529);margin-bottom:12px; }
    [data-bs-theme="dark"] .fb-title { color:var(--text-primary,#fff) !important; }
    .fb-icon { width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0; }
    .fbi-p { background:#d1fae5; } .fbi-n { background:#fee2e2; }
    .fb-item { display:flex;align-items:flex-start;gap:10px;margin-bottom:14px; }
    .fb-item:last-child { margin-bottom:0; }
    .fb-av {
        width:36px;height:36px;border-radius:50%;flex-shrink:0;
        display:flex;align-items:center;justify-content:center;
        font-size:11px;font-weight:700;color:#fff;
    }
    .fb-stars { color:#f59e0b;font-size:12px;letter-spacing:1px;margin-bottom:2px; }
    .fb-grey  { color:#e5e7eb; }
    .fb-q  { font-size:12px;color:#374151;font-style:italic;line-height:1.4;margin-bottom:2px; }
    .fb-by { font-size:11px;color:#9ca3af; }
    [data-bs-theme="dark"] .fb-q  { color:var(--text-secondary,#e5e7eb) !important; }
    [data-bs-theme="dark"] .fb-by { color:var(--text-muted,#757575) !important; }

    /* ═══════════════════════════════════════════════════════
    LEADERBOARD — inside one card, 4 cols no border
    ═══════════════════════════════════════════════════════ */
    .lb-row { display:flex;padding:14px;gap:0; }
    .lb-col { flex:1;text-align:center;padding:8px; }
    .lb-av {
        width:52px;height:52px;border-radius:50%;
        margin:0 auto 8px;display:flex;align-items:center;
        justify-content:center;font-size:15px;font-weight:700;color:#fff;
    }
    .lb-n  { font-size:13px;font-weight:600;color:var(--app-text,#212529);margin-bottom:2px; }
    .lb-p  { font-size:11px;color:#9ca3af;margin-bottom:4px; }
    .lb-c  { font-size:11px;color:#6b7280;margin-bottom:8px; }
    [data-bs-theme="dark"] .lb-n { color:var(--text-primary,#fff) !important; }
    [data-bs-theme="dark"] .lb-c { color:var(--text-muted,#757575) !important; }
    .sla-p { font-size:11px;font-weight:600;padding:3px 12px;border-radius:20px;display:inline-block; }
    .slp-g { background:#d1fae5;color:#065f46; }
    .slp-y { background:#fef3c7;color:#92400e; }
    [data-bs-theme="dark"] .slp-g { background:#052e16 !important;color:#4ade80 !important; }
    [data-bs-theme="dark"] .slp-y { background:#3a2a0a !important;color:#fbbf24 !important; }

    /* ═══════════════════════════════════════════════════════
    RIGHT ASIDE — AI Rec + SLA + Ticket Stats
    ═══════════════════════════════════════════════════════ */
    /* AI Rec */
    .rec-i {
        display:flex;align-items:flex-start;gap:8px;
        font-size:12px;color:#6b7280;padding:6px 0;
        border-bottom:1px solid var(--app-border,#dee2e6);
    }
    .rec-i:last-of-type { border-bottom:none;margin-bottom:10px; }
    .rec-i svg { width:14px;height:14px;flex-shrink:0;margin-top:1px;color:#9ca3af; }
    [data-bs-theme="dark"] .rec-i { color:var(--text-muted,#757575) !important;border-color:var(--dark-border,#2a2a2d) !important; }
    .rec-btns { display:flex;gap:8px; }
    .rec-apply {
        flex:1;display:flex;align-items:center;justify-content:center;gap:4px;
        background:#065f46;color:#fff !important;border:none;border-radius:7px;
        padding:8px;font-size:11.5px;font-weight:600;cursor:pointer;font-family:inherit;
    }
    .rec-dismiss {
        flex:1;display:flex;align-items:center;justify-content:center;gap:4px;
        background:#ef4444;color:#fff !important;border:none;border-radius:7px;
        padding:8px;font-size:11.5px;font-weight:600;cursor:pointer;font-family:inherit;
    }

    /* SLA */
    .sla-n { font-size:18px;font-weight:700;color:var(--app-text,#212529);display:flex;align-items:center;gap:6px;margin-bottom:2px; }
    [data-bs-theme="dark"] .sla-n { color:var(--text-primary,#fff) !important; }
    .sla-s { display:flex;justify-content:space-between;font-size:11px;color:#9ca3af;margin-bottom:14px; }
    .prow { margin-bottom:10px; }
    .plbl { display:flex;justify-content:space-between;font-size:12px;color:var(--app-text,#212529);margin-bottom:5px; }
    [data-bs-theme="dark"] .plbl { color:var(--text-secondary,#e5e7eb) !important; }
    .prog { height:7px;border-radius:4px;background:#f3f4f6;overflow:hidden; }
    [data-bs-theme="dark"] .prog { background:var(--dark-hover,#262626) !important; }
    .progb { height:100%;border-radius:4px; }

    /* Ticket Stats — 3 sub-cards */
    .ts-sub {
        background:var(--app-surface,#fff);
        border:1px solid var(--app-border,#dee2e6);
        border-radius:10px;padding:12px 14px;margin-bottom:10px;
    }
    .ts-sub:last-child { margin-bottom:0; }
    [data-bs-theme="dark"] .ts-sub {
        background:var(--dark-primary,#191919) !important;
        border-color:var(--dark-border,#2a2a2d) !important;
    }
    .ts-t { font-size:13px;font-weight:700;color:var(--app-text,#212529);margin-bottom:8px; }
    [data-bs-theme="dark"] .ts-t { color:var(--text-primary,#fff) !important; }

    /* MATI glowing badge */
    .mati-glow {
        width:46px;height:46px;border-radius:50%;
        background:linear-gradient(135deg,#1a1060,#3730a3,#6366f1);
        display:flex;align-items:center;justify-content:center;
        font-size:9px;font-weight:800;color:#fff;letter-spacing:.5px;flex-shrink:0;
        box-shadow:0 0 16px rgba(99,102,241,.75), 0 0 32px rgba(99,102,241,.3);
    }

    .perf-r { font-size:12px;color:#6b7280;padding:2px 0; }
    [data-bs-theme="dark"] .perf-r { color:var(--text-muted,#757575) !important; }

    .src-r { display:flex;align-items:center;font-size:12px;padding:3px 0; }
    .src-l { display:flex;align-items:center;gap:7px;color:var(--app-text,#212529); }
    [data-bs-theme="dark"] .src-l { color:var(--text-secondary,#e5e7eb) !important; }
    .src-d { width:8px;height:8px;border-radius:2px;flex-shrink:0; }
    .src-n { font-weight:600;min-width:24px; }
    .src-lab { color:#9ca3af;font-size:11.5px; }

    /* Top Issues tags — pink/rose */
    .itag {
        display:inline-flex;align-items:center;
        background:#fce7f3;color:#be185d !important;
        border-radius:20px;font-size:11.5px;font-weight:500;
        padding:4px 12px;margin:3px 3px 0 0;white-space:nowrap;
    }
    [data-bs-theme="dark"] .itag { background:#4a0d2e !important;color:#f9a8d4 !important; }

    /* Sdot for source donut */
    .sdot { fill:var(--app-surface,#fff); }
    [data-bs-theme="dark"] .sdot { fill:var(--dark-primary,#191919) !important; }

    /* Map */
    .map-wrap { border-radius:8px;overflow:hidden; }
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        background-color: #F2E5FF !important ;
        padding: 1.4rem 0;
    }
    [data-bs-theme="dark"]  .hover-lift{
        background-color: #2a3851 !important ;
    }
    .hover-lift:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
    }
    /* Fix for Bootstrap 5 warning text color in badges */
    .text-warning {
        color: #d97706 !important;
    }
    /* ── RESPONSIVE ─────────────────────────────────────── */
    @media (max-width:1199px) { .std-wrap { grid-template-columns:1fr 250px; } }
    @media (max-width:991px) {
        .std-wrap { grid-template-columns:1fr;padding:10px; }
        .chart-row { grid-template-columns:1fr 1fr; }
        .ai-tiles { --ai-visible-tiles:2; }
    }
    @media (max-width:767px) {
        .std-hdr { padding-right:.75rem !important;min-height:52px; }
        .std-hdr-t { font-size:13px; }
        .chart-row { grid-template-columns:1fr; }
        .ai-banner { border-radius:12px;padding:20px 18px; }
        .ai-top { margin-bottom:16px; }
        .ai-lbl { font-size:18px; }
        .ai-tiles { --ai-visible-tiles:1; }
        .fb-2col  { grid-template-columns:1fr; }
        .fb-col:first-child { border-right:none;border-bottom:1px solid var(--app-border,#dee2e6); }
        .lb-row { flex-wrap:wrap; }
        .lb-col { min-width:45%; }
        .std-wrap { padding:8px 8px 80px; }
    }

    .js-user-list-view-panel {
        margin:20px;
    }

    /* .table tbody td{
       padding: 1rem 1.5rem !important;
    } */
    .rating-overview-section{
        border-radius: 12px !important;
    }
</style>
@endpush

@push('scripts')

<script type="text/javascript" src="{!! CommonHelper::asset('plugins/simple_pagination/pagination.js') !!}"></script>
<script src="{!! CommonHelper::asset('js/dashboard/end_user.js') !!}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var config = {};
        config.tncAccepted = "{{ $tncAcceptance }}";
        config.permissions = {!! json_encode($permissionArray) !!};
        config.isKdEnable = "{{ config("services.knowledge_document.enabled") }}";
        config.isAssetsEnable = "{{ config("services.assets.enabled") }}";
        config.tncAcceptance = "{{ route('tncAcceptance') }}";
        config.logOut = "{{ route('logout') }}";
        config.getDashboardData = "{{ route('getDashboardData') }}";
        config.requestInfo = "{{ url('tickets/requestInfo') }}";
        config.getKdUrl = "{{ url('knowledge_document/document/jx-article-list')}}";
        config.kdViewUrl = "{{ url('knowledge_document/article/view') }}";
        config.defaultImage = "{{ asset('uploads/article/17.png') }}";
        config.current_id = {!! json_encode(Auth::user()->id) !!};
        config.articleImagePath = "{{ url('uploads/article') }}";
        config.staring = "{{ url('knowledge_document/article/staring') }}";
        config.token = "{{ csrf_token() }}";
        config.assigned_pending_devices = "{{ url('user/jx-assigned-pending-devices') }}";
        config.user_id = "{{ Auth::user()->id}}";
        config.device_conformation = "{{ url('confirm') }}";
        config.userAcceptance =  @json(config('app.user_acceptance'));
        config.sr_approval_pending = "{{ url('user/jx-srApproval-pending') }}";
        config.requestInfo = "{{ url('tickets/requestInfo') }}";
        config.getTaskData = "{{ url('ajaxTaskDashboard') }}";
        config.company_defulte = {!! json_encode($companys) !!};
	    config.company_user_detail = {!! json_encode($userDatails) !!};
        config.get_company_by_user_access = "{{ url('getCompanyByUserAccess') }}";
        config.translations = {
            Available_Records: '{{ trans('content.knowledge_document.Available_Records') }}',
            No_records_Found: '{{ trans('content.knowledge_document.No_records_Found') }}',
            are_you_star: '{{ trans('content.service_ticket_fields.are_you_star') }}',
            Search: '{{ trans('content.my_items_fields.Search') }}',
            press_enter_with_Search: '{{ trans('content.my_items_fields.press_enter_with_Search') }}',
        };
        config.token = "{{ csrf_token() }}";
        var myApp = new MyApp(config);
        $('#myitems').on("click",function() {
            var myItemsTab = new MyItemsTab(config);
        });
    });
</script>
@endpush
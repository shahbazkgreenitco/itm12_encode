{{-- @page-meta
{
  "page_no": "TKD01-26",
  "file": "ticket-detail.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "Sandeep Verma",
      "from": "2026-05",
      "reviewer": null,
      "description": "Initial Page Design and Develop"
    }
  ]
}
--}}

@extends('layouts.layout1')
@section('title', trans('ticket.ticket_detail.ticket_details'))
@section('content')
    <div class="ticket-detail-wrapper" id="page_boxed">
        <div class="header-actions-wrapper header-actions-white-wrapper d-flex align-items-center justify-content-between pe-4">
           <div class="d-flex gap-3">
                <button type="button" class="d-flex gap-3 align-items-center bg-transparent border-0 text-decoration-none p-0">
                    <a href="javascript:void(0);" id="backBtnTicket">
                        <svg width="20" height="20" viewBox="0 0 25 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11.5662 0.440759C11.706 0.580116 11.817 0.745702 11.8927 0.928029C11.9684 1.11036 12.0074 1.30584 12.0074 1.50326C12.0074 1.70068 11.9684 1.89616 11.8927 2.07848C11.817 2.26081 11.706 2.4264 11.5662 2.56576L5.12993 9.00201L23.5049 9.00201C23.9028 9.00201 24.2843 9.16004 24.5656 9.44135C24.8469 9.72265 25.0049 10.1042 25.0049 10.502C25.0049 10.8998 24.8469 11.2814 24.5656 11.5627C24.2843 11.844 23.9028 12.002 23.5049 12.002L5.12993 12.002L11.5662 18.4408C11.848 18.7226 12.0063 19.1047 12.0063 19.5033C12.0063 19.9018 11.848 20.284 11.5662 20.5658C11.2844 20.8475 10.9022 21.0059 10.5037 21.0059C10.1052 21.0059 9.72298 20.8475 9.44118 20.5658L0.441182 11.5658C0.301343 11.4264 0.190387 11.2608 0.114679 11.0785C0.0389711 10.8962 -4.50154e-07 10.7007 -4.58778e-07 10.5033C-4.67401e-07 10.3058 0.038971 10.1104 0.114679 9.92803C0.190387 9.7457 0.301342 9.58011 0.441182 9.44076L9.44118 0.440759C9.58054 0.30092 9.74613 0.189964 9.92845 0.114255C10.1108 0.0385471 10.3063 -0.000425789 10.5037 -0.000425798C10.7011 -0.000425806 10.8966 0.038547 11.0789 0.114255C11.2612 0.189964 11.4268 0.30092 11.5662 0.440759Z"
                                    fill="currentColor"></path>
                        </svg>
                    </a>
                </button>
                <h3 class="h3-text mb-0">
                    {{trans('ticket.ticket_detail.service_ticket_detail')}} - {{ $ticket->ticket_tag ?? '#' . $ticket->id }}
                    @if (!empty($ticket->serviceRequest))
                        ({{ $ticket->serviceRequest->procure_tag }})
                    @endif
                    {{-- VIP badge (only for VIP tickets) --}}
                    @if ($isVipUser)
                        <span class="tkt-vip-badge ms-3">
                            <svg width="36" height="20" viewBox="0 0 46 25" fill="none">
                                <rect x="0.5" y="0.5" width="45" height="24" rx="3.5" fill="#F6EEFF" />
                                <rect x="0.5" y="0.5" width="45" height="24" rx="3.5" stroke="url(#vip1)" />
                                <path d="M14.0229 7.31818L16.8567 15.6108H16.9711L19.8049 7.31818H21.4654L17.8013 17.5H16.0264L12.3624 7.31818H14.0229ZM24.1919 7.31818V17.5H22.6557V7.31818H24.1919ZM26.1492 17.5V7.31818H29.7784C30.5706 7.31818 31.2268 7.46236 31.7472 7.75071C32.2675 8.03906 32.657 8.43347 32.9155 8.93395C33.174 9.43111 33.3033 9.99124 33.3033 10.6143C33.3033 11.2408 33.1724 11.8042 32.9105 12.3047C32.652 12.8018 32.2609 13.1963 31.7372 13.4879C31.2169 13.7763 30.5623 13.9205 29.7734 13.9205H27.2777V12.6179H29.6342C30.1347 12.6179 30.5407 12.5317 30.8523 12.3594C31.1638 12.1837 31.3925 11.9451 31.5384 11.6435C31.6842 11.3419 31.7571 10.9988 31.7571 10.6143C31.7571 10.2299 31.6842 9.88849 31.5384 9.5902C31.3925 9.2919 31.1622 9.05824 30.8473 8.8892C30.5358 8.72017 30.1248 8.63565 29.6144 8.63565H27.6854V17.5H26.1492Z" fill="#EB0964" />
                                <defs>
                                    <linearGradient id="vip1" x1="23" y1="0" x2="23" y2="25" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#4102FF" />
                                        <stop offset="1" stop-color="#FB0956" />
                                    </linearGradient>
                                </defs>
                            </svg>
                        </span>
                    @endif
                </h3>
           </div>
            <div>
                @if (!empty($ticket->serviceRequest))
                    <a href="{{ url('tickets/requestInfo') }}/{{ $ticket->serviceRequest->id }}{{ request()->query('b') ? '?b=' . request()->query('b') : '' }}"
                        target="_blank" data-bs-toggle="tooltip" title="{{trans('ticket.ticket_detail.service_request')}}">
                        <button class="header-icon-btn header-icon-btn-sm" type="button">
                            <svg width="16" height="16" viewBox="0 0 21 15" fill="none"
                                xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0">
                                <path
                                    d="M20.25 5.25C20.4489 5.25 20.6397 5.17098 20.7803 5.03033C20.921 4.88968 21 4.69891 21 4.5V1.5C21 1.10218 20.842 0.720644 20.5607 0.43934C20.2794 0.158035 19.8978 0 19.5 0H1.5C1.10218 0 0.720644 0.158035 0.43934 0.43934C0.158035 0.720644 0 1.10218 0 1.5V4.5C0 4.69891 0.0790176 4.88968 0.21967 5.03033C0.360322 5.17098 0.551088 5.25 0.75 5.25C1.34674 5.25 1.91903 5.48705 2.34099 5.90901C2.76295 6.33097 3 6.90326 3 7.5C3 8.09674 2.76295 8.66903 2.34099 9.09099C1.91903 9.51295 1.34674 9.75 0.75 9.75C0.551088 9.75 0.360322 9.82902 0.21967 9.96967C0.0790176 10.1103 0 10.3011 0 10.5V13.5C0 13.8978 0.158035 14.2794 0.43934 14.5607C0.720644 14.842 1.10218 15 1.5 15H19.5C19.8978 15 20.2794 14.842 20.5607 14.5607C20.842 14.2794 21 13.8978 21 13.5V10.5C21 10.3011 20.921 10.1103 20.7803 9.96967C20.6397 9.82902 20.4489 9.75 20.25 9.75C19.6533 9.75 19.081 9.51295 18.659 9.09099C18.2371 8.66903 18 8.09674 18 7.5C18 6.90326 18.2371 6.33097 18.659 5.90901C19.081 5.48705 19.6533 5.25 20.25 5.25ZM1.5 11.175C2.34772 11.0029 3.10986 10.543 3.65728 9.87319C4.20471 9.20343 4.50376 8.36502 4.50376 7.5C4.50376 6.63498 4.20471 5.79657 3.65728 5.12681C3.10986 4.45705 2.34772 3.99714 1.5 3.825V1.5H6.75V13.5H1.5V11.175ZM19.5 11.175V13.5H8.25V1.5H19.5V3.825C18.6523 3.99714 17.8901 4.45705 17.3427 5.12681C16.7953 5.79657 16.4962 6.63498 16.4962 7.5C16.4962 8.36502 16.7953 9.20343 17.3427 9.87319C17.8901 10.543 18.6523 11.0029 19.5 11.175Z"
                                    fill="currentColor" />
                            </svg>
                        </button>
                    </a>
                @endif

                @if (
                    $ticket->form_id != null &&
                        $ticket->form_type != 2 &&
                        (Auth::user()->isSuperUser() ||
                            $ticket->creator_id == Auth::user()->id ||
                            $ticket->assigned_to == Auth::user()->id ||
                            $ticket->myApproval))
                    <a href="{{ url('requested_form/view/') }}/{{ $ticket->form_id }}{{ request()->query('b') ? '?b=' . request()->query('b') : '' }}"
                        target="_blank" data-bs-toggle="tooltip"
                        title="{{ trans('ticket.create_ticket.view_form') }}">
                        <button class="header-icon-btn header-icon-btn-sm" type="button">
                            <svg width="17" height="17" viewBox="0 0 17 17" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M3 2.4C2.84087 2.4 2.68826 2.46321 2.57574 2.57574C2.46321 2.68826 2.4 2.84087 2.4 3C2.4 3.15913 2.46321 3.31174 2.57574 3.42426C2.68826 3.53679 2.84087 3.6 3 3.6H13.8C13.9591 3.6 14.1117 3.53679 14.2243 3.42426C14.3368 3.31174 14.4 3.15913 14.4 3C14.4 2.84087 14.3368 2.68826 14.2243 2.57574C14.1117 2.46321 13.9591 2.4 13.8 2.4H3ZM4.2 9.6C4.67739 9.6 5.13523 9.41036 5.47279 9.07279C5.81036 8.73523 6 8.27739 6 7.8C6 7.32261 5.81036 6.86477 5.47279 6.52721C5.13523 6.18964 4.67739 6 4.2 6C3.72261 6 3.26477 6.18964 2.92721 6.52721C2.58964 6.86477 2.4 7.32261 2.4 7.8C2.4 8.27739 2.58964 8.73523 2.92721 9.07279C3.26477 9.41036 3.72261 9.6 4.2 9.6ZM4.2 8.4C4.04087 8.4 3.88826 8.33679 3.77574 8.22426C3.66321 8.11174 3.6 7.95913 3.6 7.8C3.6 7.64087 3.66321 7.48826 3.77574 7.37573C3.88826 7.26321 4.04087 7.2 4.2 7.2C4.35913 7.2 4.51174 7.26321 4.62426 7.37573C4.73679 7.48826 4.8 7.64087 4.8 7.8C4.8 7.95913 4.73679 8.11174 4.62426 8.22426C4.51174 8.33679 4.35913 8.4 4.2 8.4ZM6 12.6C6 13.0774 5.81036 13.5352 5.47279 13.8728C5.13523 14.2104 4.67739 14.4 4.2 14.4C3.72261 14.4 3.26477 14.2104 2.92721 13.8728C2.58964 13.5352 2.4 13.0774 2.4 12.6C2.4 12.1226 2.58964 11.6648 2.92721 11.3272C3.26477 10.9896 3.72261 10.8 4.2 10.8C4.67739 10.8 5.13523 10.9896 5.47279 11.3272C5.81036 11.6648 6 12.1226 6 12.6ZM4.8 12.6C4.8 12.4409 4.73679 12.2883 4.62426 12.1757C4.51174 12.0632 4.35913 12 4.2 12C4.04087 12 3.88826 12.0632 3.77574 12.1757C3.66321 12.2883 3.6 12.4409 3.6 12.6C3.6 12.7591 3.66321 12.9117 3.77574 13.0243C3.88826 13.1368 4.04087 13.2 4.2 13.2C4.35913 13.2 4.51174 13.1368 4.62426 13.0243C4.73679 12.9117 4.8 12.7591 4.8 12.6ZM7.2 7.8C7.2 7.64087 7.26321 7.48826 7.37573 7.37573C7.48826 7.26321 7.64087 7.2 7.8 7.2H13.8C13.9591 7.2 14.1117 7.26321 14.2243 7.37573C14.3368 7.48826 14.4 7.64087 14.4 7.8C14.4 7.95913 14.3368 8.11174 14.2243 8.22426C14.1117 8.33679 13.9591 8.4 13.8 8.4H7.8C7.64087 8.4 7.48826 8.33679 7.37573 8.22426C7.26321 8.11174 7.2 7.95913 7.2 7.8ZM7.8 12C7.64087 12 7.48826 12.0632 7.37573 12.1757C7.26321 12.2883 7.2 12.4409 7.2 12.6C7.2 12.7591 7.26321 12.9117 7.37573 13.0243C7.48826 13.1368 7.64087 13.2 7.8 13.2H13.8C13.9591 13.2 14.1117 13.1368 14.2243 13.0243C14.3368 12.9117 14.4 12.7591 14.4 12.6C14.4 12.4409 14.3368 12.2883 14.2243 12.1757C14.1117 12.0632 13.9591 12 13.8 12H7.8ZM3.6 0C2.64522 0 1.72955 0.379285 1.05442 1.05442C0.379285 1.72955 0 2.64522 0 3.6V13.2C0 14.1548 0.379285 15.0705 1.05442 15.7456C1.72955 16.4207 2.64522 16.8 3.6 16.8H13.2C14.1548 16.8 15.0705 16.4207 15.7456 15.7456C16.4207 15.0705 16.8 14.1548 16.8 13.2V3.6C16.8 2.64522 16.4207 1.72955 15.7456 1.05442C15.0705 0.379285 14.1548 0 13.2 0H3.6ZM1.2 3.6C1.2 2.96348 1.45286 2.35303 1.90294 1.90294C2.35303 1.45286 2.96348 1.2 3.6 1.2H13.2C13.8365 1.2 14.447 1.45286 14.8971 1.90294C15.3471 2.35303 15.6 2.96348 15.6 3.6V13.2C15.6 13.8365 15.3471 14.447 14.8971 14.8971C14.447 15.3471 13.8365 15.6 13.2 15.6H3.6C2.96348 15.6 2.35303 15.3471 1.90294 14.8971C1.45286 14.447 1.2 13.8365 1.2 13.2V3.6Z"
                                    fill="#7F7F7F" />
                            </svg>
                        </button>
                    </a>
                @endif
            </div>
        </div>
       
        @if($role_name !== 'User')
            @php
                $ticket_sentiment = trans('ticket.ticket_detail.ticket_sentiment_na');
                $sentiment_icon =
                    '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-emoji-expressionless-fill" viewBox="0 0 16 16"><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16M4.5 6h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1m5 0h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1m-5 4h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1 0-1"/></svg>';
                if (!empty($tkt_details) && !is_null($tkt_details->ticket_sentiment) && config('app.ai_enabled')) {
                    if ($tkt_details->ticket_sentiment == 1) {
                        $ticket_sentiment = trans('ticket.update_status.positive');
                        $sentiment_icon =
                            '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-emoji-heart-eyes-fill" viewBox="0 0 16 16"><path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0M4.756 4.566c.763-1.424 4.02-.12.952 3.434-4.496-1.596-2.35-4.298-.952-3.434m6.559 5.448a.5.5 0 0 1 .548.736A4.5 4.5 0 0 1 7.965 13a4.5 4.5 0 0 1-3.898-2.25.5.5 0 0 1 .548-.736h.005l.017.005.067.015.252.055c.215.046.515.108.857.169.693.124 1.522.242 2.152.242s1.46-.118 2.152-.242a27 27 0 0 0 1.109-.224l.067-.015.017-.004.005-.002zm-.07-5.448c1.397-.864 3.543 1.838-.953 3.434-3.067-3.554.19-4.858.952-3.434z"/></svg>';
                    } elseif ($tkt_details->ticket_sentiment == 2) {
                        $ticket_sentiment = trans('ticket.update_status.neutral');
                        $sentiment_icon =
                            '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-emoji-expressionless-fill" viewBox="0 0 16 16"><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16M4.5 6h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1m5 0h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1m-5 4h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1 0-1"/></svg>';
                    } elseif ($tkt_details->ticket_sentiment == 3) {
                        $ticket_sentiment = trans('ticket.update_status.negative');
                        $sentiment_icon =
                            '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-emoji-angry-fill" viewBox="0 0 16 16"><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16M4.053 4.276a.5.5 0 0 1 .67-.223l2 1a.5.5 0 0 1 .166.76c.071.206.111.44.111.687C7 7.328 6.552 8 6 8s-1-.672-1-1.5c0-.408.109-.778.285-1.049l-1.009-.504a.5.5 0 0 1-.223-.67zm.232 8.157a.5.5 0 0 1-.183-.683A4.5 4.5 0 0 1 8 9.5a4.5 4.5 0 0 1 3.898 2.25.5.5 0 1 1-.866.5A3.5 3.5 0 0 0 8 10.5a3.5 3.5 0 0 0-3.032 1.75.5.5 0 0 1-.683.183M10 8c-.552 0-1-.672-1-1.5 0-.247.04-.48.11-.686a.502.502 0 0 1 .166-.761l2-1a.5.5 0 1 1 .448.894l-1.009.504c.176.27.285.64.285 1.049 0 .828-.448 1.5-1 1.5"/></svg>';
                    }
                }
            @endphp
        @endif

        <main class="main-content" id="mainContent">
            <div class="tkd-action-bar d-flex align-items-center gap-2 flex-wrap">
                @if($role_name !== 'User')
                    @if (config('app.ai_enabled'))
                        <button class="tkd-action-btn {{ $ticket_sentiment == 'N/A' ? '' : 'sentiment-button' }}"
                            data-ticket-id="{{ $ticket->id }}" type="button" data-bs-placement="bottom"
                            data-bs-toggle="tooltip" title="{{ trans('ticket.ticket_detail.ticket_sentiment') }}">
                            {!! $sentiment_icon !!}
                            <span>{{ $ticket_sentiment }}</span>
                        </button>
                    @endif
                @endif

                @if ($ticket->spam != 1)
                    @if(Auth::user()->hasPermission("service_tickets"))
                    <button class="tkd-action-btn js-act-staring" type="button" data-bs-toggle="tooltip" title="{{ trans('ticket.ticket_detail.Add_Star') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-star" viewBox="0 0 16 16">
                            <path
                                d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z" />
                        </svg>
                        <span>{{ trans('ticket.ticket_detail.Add_Star') }}</span>
                    </button>
                    @endif
                @endif

                @if ($ticket->status_id != 6)
                    @if ($ticket->spam != 1)
                        @if (empty($revokeTicket))
                            <button class="tkd-action-btn js-act-reopen hidden" type="button"  data-bs-toggle="tooltip" title="{{ trans('ticket.ticket_detail.reopen_tikcet') }}">
                                <i class="bi bi-arrow-counterclockwise"></i>
                                <span>{{ trans('ticket.ticket_detail.reopen_tikcet') }}</span>
                            </button>
                        @endif

                        @can('EditServiceTicket')
                            @if ($ticket->status_id != 5 && $ticket->status_id != 6 && $access_privilege == true)
                                <button class="tkd-action-btn js-act-edit-ticket" type="button" data-bs-toggle="tooltip" title="{{ trans('ticket.ticket_detail.edit_ticket') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        class="bi bi-pencil-square" viewBox="0 0 16 16">
                                        <path
                                            d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                        <path fill-rule="evenodd"
                                            d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                    </svg>
                                    <span>{{ trans('ticket.ticket_detail.edit_ticket') }}</span>
                                </button>
                            @endif
                        @endcan

                        @if (isset($action_controls['ctrl_transfer']) &&
                                $action_controls['ctrl_transfer'] == 1 &&
                                !in_array($ticket->status_id, [5, 6]))
                            <button class="tkd-action-btn js-act-transfer hidden" type="button" data-bs-toggle="tooltip" title="{{ trans('ticket.ticket_detail.Transfer') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-arrow-left-right" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M1 11.5a.5.5 0 0 0 .5.5h11.793l-3.147 3.146a.5.5 0 0 0 .708.708l4-4a.5.5 0 0 0 0-.708l-4-4a.5.5 0 0 0-.708.708L13.293 11H1.5a.5.5 0 0 0-.5.5m14-7a.5.5 0 0 1-.5.5H2.707l3.147 3.146a.5.5 0 1 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 4H14.5a.5.5 0 0 1 .5.5" />
                                </svg>
                                <span>{{ trans('ticket.ticket_detail.Transfer') }}</span>
                            </button>
                        @endif

                        @if (!in_array($ticket->status_id, [4, 5]))
                            <button class="tkd-action-btn js-act-assign-to hidden" type="button" data-bs-toggle="tooltip" title="{{ trans('ticket.ticket_detail.Assign_To') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-person-plus" viewBox="0 0 16 16">
                                    <path
                                        d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H1s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C9.516 10.68 8.289 10 6 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z" />
                                    <path fill-rule="evenodd"
                                        d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5" />
                                </svg>
                                <span>{{ trans('ticket.ticket_detail.Assign_To') }}</span>
                            </button>
                        @endif

                        @if (
                            $ticket->assigned_to != Auth::user()->id &&
                                $ticket->creator_id != Auth::user()->id &&
                                isset($action_controls['ctrl_self_assign']) &&
                                $action_controls['ctrl_self_assign'] == 1 &&
                                !in_array($ticket->status_id, [5, 6]))
                            <button class="tkd-action-btn js-act-self-assign hidden" type="button" data-bs-toggle="tooltip" title="{{ trans('ticket.ticket_detail.Self_Assign') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-person-check" viewBox="0 0 16 16">
                                    <path
                                        d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m1.679-4.493-1.335 2.226a.75.75 0 0 1-1.174.144l-.774-.773a.5.5 0 0 1 .708-.708l.547.548 1.17-1.951a.5.5 0 1 1 .858.514M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
                                    <path
                                        d="M8.256 14a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844 9.002 8 9c-5 0-6 3-6 4s1 1 1 1z" />
                                </svg>
                                <span>{{ trans('ticket.ticket_detail.Self_Assign') }}</span>
                            </button>
                        @endif

                        @if (isset($action_controls['ctrl_delete']) && $action_controls['ctrl_delete'] == 1)
                            <button class="tkd-action-btn js-act-delete hidden" type="button" data-bs-toggle="tooltip" title="{{ trans('ticket.ticket_detail.Delete') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-trash" viewBox="0 0 16 16">
                                    <path
                                        d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                    <path
                                        d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                </svg>
                                <span>{{ trans('ticket.ticket_detail.Delete') }}</span>
                            </button>
                        @endif
                    @endif

                    @if (isset($action_controls['ctrl_mark_spam']) && $action_controls['ctrl_mark_spam'] == 1 && $ticket->status_id != 5)
                        @if (Auth::id() != $ticket->creator_id)
                            <button class="tkd-action-btn js-act-spam hidden" type="button" data-bs-toggle="tooltip"
                                title="{{ trans('ticket.ticket_detail.Spam') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="bi bi-shield-x" viewBox="0 0 16 16">
                                    <path
                                        d="M5.338 1.59a61 61 0 0 0-2.837.856.48.48 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.7 10.7 0 0 0 2.287 2.233c.346.244.652.42.893.533q.18.085.293.118a1 1 0 0 0 .101.025 1 1 0 0 0 .1-.025q.114-.034.294-.118c.24-.113.547-.29.893-.533a10.7 10.7 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.8 11.8 0 0 1-2.517 2.453 7 7 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7 7 0 0 1-1.048-.625 11.8 11.8 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 63 63 0 0 1 5.072.56" />
                                    <path
                                        d="M6.146 5.146a.5.5 0 0 1 .708 0L8 6.293l1.146-1.147a.5.5 0 1 1 .708.708L8.707 7l1.147 1.146a.5.5 0 0 1-.708.708L8 7.707 6.854 8.854a.5.5 0 1 1-.708-.708L7.293 7 6.146 5.854a.5.5 0 0 1 0-.708" />
                                </svg>
                                <span>{{ trans('ticket.ticket_detail.Spam') }}</span>
                            </button>
                        @endif
                    @endif
                @endif

                <button class="tkd-action-btn js-act-tkt-history" type="button" data-bs-toggle="tooltip" title="{{ trans('ticket.ticket_detail.Tkt_History') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-clock-history" viewBox="0 0 16 16">
                        <path
                            d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z" />
                        <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z" />
                        <path
                            d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5" />
                    </svg>
                    <span>{{ trans('ticket.ticket_detail.Tkt_History') }}</span>
                </button>

                @if (in_array(config('app.client'), ['rolepermission', 'grdemo']) &&
                        !in_array($ticket->status_id, [5, 6]) &&
                        (Auth::user()->hasAnyRole(['SuperAdmin', 'Admin']) || Auth::user()->id == $ticket->assigned_to))
                    <button class="tkd-action-btn js-act-tkt-book-calendar" type="button" data-bs-toggle="tooltip" title="{{ trans('ticket.ticket_detail.book_calendar') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-calendar-event" viewBox="0 0 16 16">
                            <path d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z" />
                            <path
                                d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                        </svg>
                        <span>{{ trans('ticket.ticket_detail.book_calendar') }}</span>
                    </button>
                @endif
                <button class="tkd-action-btn tkd-action-more js-action-more d-none" type="button"
                    data-bs-toggle="tooltip" title="{{ trans('ticket.ticket_detail.more_actions') }}" aria-label="More actions" aria-expanded="false">
                    <svg style="height:12px;" class="extender-icon" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.31754 7.31754L1.06753 13.5675C0.95026 13.6848 0.7912 13.7507 0.625347 13.7507C0.459495 13.7507 0.300435 13.6848 0.18316 13.5675C0.0658846 13.4503 3.26935e-09 13.2912 0 13.1253C-3.26935e-09 12.9595 0.0658846 12.8004 0.18316 12.6832L5.99175 6.87535L0.18316 1.06753C0.0658846 0.95026 0 0.7912 0 0.625347C0 0.459495 0.0658846 0.300435 0.18316 0.18316C0.300435 0.0658846 0.459495 0 0.625347 0C0.7912 0 0.95026 0.0658846 1.06753 0.18316L7.31754 6.43316C7.37565 6.49121 7.42175 6.56014 7.4532 6.63601C7.48465 6.71188 7.50084 6.79321 7.50084 6.87535C7.50084 6.95748 7.48465 7.03881 7.4532 7.11469C7.42175 7.19056 7.37565 7.25949 7.31754 7.31754ZM13.5675 6.43316L7.31754 0.18316C7.20026 0.0658846 7.0412 0 6.87535 0C6.7095 0 6.55044 0.0658846 6.43316 0.18316C6.31588 0.300435 6.25 0.459495 6.25 0.625347C6.25 0.7912 6.31588 0.95026 6.43316 1.06753L12.2418 6.87535L6.43316 12.6832C6.31588 12.8004 6.25 12.9595 6.25 13.1253C6.25 13.2912 6.31588 13.4503 6.43316 13.5675C6.55044 13.6848 6.7095 13.7507 6.87535 13.7507C7.0412 13.7507 7.20026 13.6848 7.31754 13.5675L13.5675 7.31754C13.6256 7.25949 13.6717 7.19056 13.7032 7.11469C13.7347 7.03881 13.7508 6.95748 13.7508 6.87535C13.7508 6.79321 13.7347 6.71188 13.7032 6.63601C13.6717 6.56014 13.6256 6.49121 13.5675 6.43316Z" fill="currentColor"></path>
                    </svg>
                </button>
            </div>
            @php
                $ticketCreatorName = $creator ? $creator->fullname() : null;
                $assignedToName = optional($ticket->assignedTo)->fullname();
                $ticketCreatedAt = $ticket->created_at
                    ? CommonHelper::getDateAs($ticket->created_at, 'd/m/Y h:i A', 'Y-m-d H:i:s')
                    : null;
                $ticketUpdatedAt = $ticket->updated_at
                    ? CommonHelper::getDateAs($ticket->updated_at, 'd/m/Y h:i A', 'Y-m-d H:i:s')
                    : null;
                $ticketStatusName = optional($ticket->status)->name;
                $ticketPriorityName = optional($ticket->priority)->name;
                $hasTicketStatus = trim((string) $ticketStatusName) !== '';
                $hasTicketPriority = trim((string) $ticketPriorityName) !== '';
                $hasTicketTat = $ticket->tat !== null && trim((string) $ticket->tat) !== '';
                $getInitials = function ($name) {
                    $name = trim((string) $name);
                    if ($name === '') {
                        return 'NA';
                    }

                    $parts = preg_split('/\s+/', $name);
                    $initials = '';
                    foreach (array_slice($parts, 0, 2) as $part) {
                        $initials .= strtoupper(substr($part, 0, 1));
                    }

                    return $initials ?: 'NA';
                };

                $ticketLoggerName = null;
                if (isset($actual_creator) && $actual_creator) {
                    $actualCreatorFullName = trim((string) $actual_creator->fullname());
                    $ticketLoggerName = $actualCreatorFullName !== '' ? $actualCreatorFullName : null;
                }

                $ticketDetailFields = [
                    ['icon' => 'bi bi-building', 'label' => trans("content.service_ticket_fields.Company"), 'value' => optional(optional($ticket->department)->company)->name],
                    [
                        'icon' => 'bi bi-diagram-3',
                        'label' => trans('content.service_ticket_fields.department'),
                        'value' => optional($ticket->department)->name,
                    ],
                    [
                        'icon' => 'bi bi-folder',
                        'label' => trans('content.service_ticket_fields.Prob_Category'),
                        'value' => optional($ticket->problemCategory)->name,
                    ],
                    [
                        'icon' => 'bi bi-folder2-open',
                        'label' => trans('content.service_ticket_fields.sub_category'),
                        'value' => optional($ticket->subCategory)->name,
                    ],
                    // ['icon' => 'bi bi-check2-circle', 'label' => trans("content.service_ticket_fields.Ticket_Status"), 'value' => optional($ticket->status)->name],
                    // ['icon' => 'bi bi-person-check', 'label' => trans("content.service_ticket_fields.Assigned_To"), 'value' => optional($ticket->assignedTo)->fullname(), 'email' => $ticket->assignedTo->email ?? null],
                    // ['icon' => 'bi bi-flag', 'label' => trans("content.service_ticket_fields.Priority"), 'value' => optional($ticket->priority)->name],
                    [
                        'icon' => 'bi bi-geo-alt',
                        'label' => trans('content.service_ticket_fields.location'),
                        'value' => optional($ticket->locations)->name,
                    ],
                    // ['icon' => 'bi bi-person', 'label' => 'Ticket Creator', 'value' => $ticketCreatorName, 'email' => $creator->email ?? null],
                    // ['icon' => 'bi bi-person-lines-fill', 'label' => trans("content.procurement_fields.ticket_logger"), 'value' => $ticketLoggerName, 'email' => optional($ticket->actualCreator)->email],
                    [
                        'icon' => 'bi bi-calendar-plus',
                        'label' => trans('content.service_ticket_fields.Created_At'),
                        'value' => $ticketCreatedAt,
                    ],
                    [
                        'icon' => 'bi bi-calendar-check',
                        'label' => trans('content.common_doc_list.updated_at'),
                        'value' => $ticketUpdatedAt,
                    ],
                ];

                if (isset($device) && $device) {
                    $ticketDetailFields[] = [
                        'icon' => 'bi bi-pc-display',
                        'label' => trans('content.service_ticket_fields.Related_Device'),
                        'value' => Auth::user()->hasAnyRole(['SuperAdmin', 'Admin'])
                            ? '<a class="text-red" target="_blank" href="' .
                                url('device/info/' . $device->id) .
                                '">' .
                                e($device->asset_tag) .
                                '</a>'
                            : $device->asset_tag,
                        'isHtml' => Auth::user()->hasAnyRole(['SuperAdmin', 'Admin']),
                        'isAsset' => Auth::user()->hasAnyRole(['SuperAdmin', 'Admin']),
                    ];
                }

                // seat_number hide
                if (
                    isset($ticket_prob) &&
                    in_array(config('app.client'), ['ril', 'rolepermission']) &&
                    !empty($ticket_prob->seat_no)
                ) {
                    $ticketDetailFields[] = [
                        'icon' => 'bi bi-grid-3x3-gap',
                        'label' => 'Seat No',
                        'value' => $ticket_prob->seat_no,
                    ];
                }

                // $ticketDetailFields[] = ['icon' => 'bi bi-clock-history', 'label' => trans("content.service_ticket_fields.TAT"), 'value' => $ticket->tat ? $ticket->tat . " Hrs" : null];

                $createdViaLabel = null;
                switch ($ticket->created_via) {
                    case 1:
                        $createdViaLabel = '<i class="bi bi-display"></i> via Portal';
                        break;
                    case 2:
                        $createdViaLabel = '<i class="bi bi-chat-dots"></i> via Chat';
                        break;
                    case 3:
                        $createdViaLabel =
                            '<i class="bi bi-envelope"></i> via E-mail' .
                            ($ticket->autoCreationAccount
                                ? ' - ' . e($ticket->autoCreationAccount->ebts_username)
                                : '');
                        break;
                    case 4:
                        $createdViaLabel = '<i class="bi bi-phone"></i> via Mobile';
                        break;
                    case 5:
                        $createdViaLabel = '<i class="bi bi-telephone"></i> via Call';
                        break;
                    case 6:
                        $createdViaLabel = '<i class="bi bi-robot"></i> via BOT';
                        break;
                    default:
                        $createdViaLabel = !empty($creator->username) ? e($creator->username) : null;
                        break;
                }

                // if ($createdViaLabel) {
                //     $ticketDetailFields[] = ['icon' => 'bi bi-send', 'label' => trans("content.service_ticket_fields.Created_Via"), 'value' => $createdViaLabel, 'isHtml' => true];
                // }

                $ticketDetailFields[] = [
                    'icon' => 'bi bi-check-circle',
                    'label' => trans('content.common_doc_list.Resolved_At'),
                    'value' =>
                        $ticket->resolved_at && in_array($ticket->status_id, [5, 6])
                            ? CommonHelper::getDateAs($ticket->resolved_at, 'd/m/Y h:i A', 'Y-m-d H:i:s')
                            : null,
                ];

                if (isset($ticket->closed_at) && $ticket->closed_at != null && in_array($ticket->status_id, [5, 6])) {
                    $ticketDetailFields[] = [
                        'icon' => 'bi bi-lock',
                        'label' => trans('content.common_doc_list.Closed_At'),
                        'value' => CommonHelper::getDateAs($ticket->closed_at, 'd/m/Y h:i A', 'Y-m-d H:i:s'),
                    ];
                }

                if (isset($cm_record)) {
                    $ticketDetailFields[] = [
                        'icon' => 'bi bi-arrow-left-right',
                        'label' => 'Cm Record ID',
                        'value' =>
                            '<a href="' .
                            url('change-management/info', Crypt::encrypt($cm_record->record_tag)) .
                            '" target="_blank">#' .
                            e($cm_record->record_tag) .
                            '</a>',
                        'isHtml' => true,
                    ];
                }

                $ticketRefs = [
                    [
                        'key' => 'new_ticket_reference',
                        'label' => trans('content.service_ticket_fields.ticket_reference'),
                    ],
                    ['key' => 'old_ticket_ref', 'label' => trans('content.service_ticket_fields.old_ticket_reference')],
                    ['key' => 'original_ticket_reference', 'label' => 'Original Ticket Ref'],
                ];
                foreach ($ticketRefs as $ref) {
                    if (
                        isset($ticket->{$ref['key']}) &&
                        $ticket->{$ref['key']} != null &&
                        (config('app.client') == 'ltts' && !in_array(config('app.sub_client'), ['admin', 'ceo']))
                    ) {
                        $ticketDetailFields[] = [
                            'icon' => 'bi bi-link-45deg',
                            'label' => $ref['label'],
                            'value' =>
                                '<a href="' .
                                url('ticket', $ticket->{$ref['key']}) .
                                '" target="_blank">' .
                                e($ticket->{$ref['key']}) .
                                '</a>',
                            'isHtml' => true,
                        ];
                    }
                }

                // if (isset($customFieldsFromTableForDepartments)) {
                //     foreach ($customFieldsFromTableForDepartments as $field) {
                //         if (!empty($field['value'])) {
                //             $ticketDetailFields[] = [
                //                 'icon' => 'bi bi-list-check',
                //                 'label' => $field['column'],
                //                 'value' => $field['value'],
                //             ];
                //         }
                //     }
                // }

                if (isset($ProblemManagement) && $ProblemManagement->ProblemImpactedTicket == 1) {
                    $ticketDetailFields[] = [
                        'icon' => 'bi bi-exclamation-triangle',
                        'label' => trans('content.service_ticket_fields.problem_management'),
                        'value' =>
                            '<a href="' .
                            url('problem_manager/' . $ProblemManagement->id) .
                            '" target="_blank">' .
                            e($ProblemManagement->name) .
                            '</a>',
                        'isHtml' => true,
                    ];
                }

                if (isset($incident) && $incident->IncidentImpactedTicket == 1) {
                    $ticketDetailFields[] = [
                        'icon' => 'bi bi-broadcast',
                        'label' => trans('content.service_ticket_fields.incident_management'),
                        'value' =>
                            '<a href="' .
                            url('tickets/incidentList') .
                            '" target="_blank">#' .
                            e($incident->id) .
                            ' - ' .
                            e($incident->subject) .
                            '</a>',
                        'isHtml' => true,
                    ];
                }

                $ticketDetailFields = array_values(
                    array_filter($ticketDetailFields, function ($field) {
                        return !empty($field['value']);
                    }),
                );
            @endphp
            <div class="tkd-body">
                <div class="tkd-main-card">
                    <div class="tkd-status-strip py-2 px-4 d-flex align-items-center gap-2 flex-wrap">
                        <span data-bs-toggle="tooltip" title="{{trans('ticket.service_ticket_fields.status')}}" class=amg-badge badge-high{{ $hasTicketStatus ? '' : ' hide' }}" id="statusInfo"
                            data-ticket-summary="status">{{ $ticketStatusName }}</span>
                        <span class="amg-badge badge-icon hide" data-bs-toggle="tooltip" title="{{trans('ticket.ticket_detail.tat_expire_at')}}" id="expireInfo" data-ticket-summary="tat-sla">
                            <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <g opacity="0.4">
                                    <path d="M12 2.25C10.0716 2.25 8.18657 2.82183 6.58319 3.89317C4.97982 4.96451 3.73013 6.48726 2.99218 8.26884C2.25422 10.0504 2.06114 12.0108 2.43735 13.9021C2.81355 15.7934 3.74215 17.5307 5.10571 18.8943C6.46928 20.2579 8.20656 21.1865 10.0979 21.5627C11.9892 21.9389 13.9496 21.7458 15.7312 21.0078C17.5127 20.2699 19.0355 19.0202 20.1068 17.4168C21.1782 15.8134 21.75 13.9284 21.75 12C21.7473 9.41498 20.7192 6.93661 18.8913 5.10872C17.0634 3.28084 14.585 2.25273 12 2.25ZM17.25 12.75H12C11.8011 12.75 11.6103 12.671 11.4697 12.5303C11.329 12.3897 11.25 12.1989 11.25 12V6.75C11.25 6.55109 11.329 6.36032 11.4697 6.21967C11.6103 6.07902 11.8011 6 12 6C12.1989 6 12.3897 6.07902 12.5303 6.21967C12.671 6.36032 12.75 6.55109 12.75 6.75V11.25H17.25C17.4489 11.25 17.6397 11.329 17.7803 11.4697C17.921 11.6103 18 11.8011 18 12C18 12.1989 17.921 12.3897 17.7803 12.5303C17.6397 12.671 17.4489 12.75 17.25 12.75Z" fill="currentColor" />
                                </g>
                            </svg>
                            <span data-ticket-summary-value="tat-sla">Tat Expire: N/A</span>
                        </span>
                        <span  data-bs-toggle="tooltip" title="{{trans('ticket.ticket_detail.tat')}}" class="amg-badge{{ $hasTicketTat ? '' : ' hide' }}" id="tatInfo"
                            data-ticket-summary="tat">
                            {{ $hasTicketTat ? 'TAT: ' . $ticket->tat . ' Hrs' : '' }}
                        </span>
                        <span  data-bs-toggle="tooltip" title="{{trans('ticket.edit_ticket.priority')}}" class="amg-badge badge-icon{{ $hasTicketPriority ? '' : ' hide' }}" id="priorityInfo"
                            data-ticket-summary="priority">
                            <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.5 12L12 3L21.5 12H15.5V21H8.5V12H2.5Z" fill="#FF0A0E" stroke="#FF0A0E"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span data-ticket-summary-value="priority">{{ $ticketPriorityName }}</span>
                        </span>
                        @if ($ticket->spam)
                            <span  data-bs-toggle="tooltip" title="{{ trans('ticket.ticket_detail.Spam_Ticket') }}" class="amg-badge badge-icon{{ $hasTicketPriority ? '' : ' hide' }}" id="priorityInfo"
                                data-ticket-summary="priority">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                                </svg>
                                <span>{{ trans('ticket.ticket_detail.Spam_Ticket') }}</span>
                            </span>
                        @endif
                        <span data-bs-toggle="tooltip" title="{{trans('ticket.ticket_detail.workaround')}}" class="amg-badge badge-icon hide" id="workaroundInfo" data-ticket-summary="workaround-sla">
                            <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <g opacity="0.4">
                                    <path
                                        d="M12 2.25C10.0716 2.25 8.18657 2.82183 6.58319 3.89317C4.97982 4.96451 3.73013 6.48726 2.99218 8.26884C2.25422 10.0504 2.06114 12.0108 2.43735 13.9021C2.81355 15.7934 3.74215 17.5307 5.10571 18.8943C6.46928 20.2579 8.20656 21.1865 10.0979 21.5627C11.9892 21.9389 13.9496 21.7458 15.7312 21.0078C17.5127 20.2699 19.0355 19.0202 20.1068 17.4168C21.1782 15.8134 21.75 13.9284 21.75 12C21.7473 9.41498 20.7192 6.93661 18.8913 5.10872C17.0634 3.28084 14.585 2.25273 12 2.25ZM17.25 12.75H12C11.8011 12.75 11.6103 12.671 11.4697 12.5303C11.329 12.3897 11.25 12.1989 11.25 12V6.75C11.25 6.55109 11.329 6.36032 11.4697 6.21967C11.6103 6.07902 11.8011 6 12 6C12.1989 6 12.3897 6.07902 12.5303 6.21967C12.671 6.36032 12.75 6.55109 12.75 6.75V11.25H17.25C17.4489 11.25 17.6397 11.329 17.7803 11.4697C17.921 11.6103 18 11.8011 18 12C18 12.1989 17.921 12.3897 17.7803 12.5303C17.6397 12.671 17.4489 12.75 17.25 12.75Z"
                                        fill="currentColor" />
                                </g>
                            </svg>
                            <span data-ticket-summary-value="workaround-sla">Workaround SLA: N/A</span>
                        </span>
                        <span class="amg-badge badge-icon hide" data-bs-toggle="tooltip" title="{{trans('ticket.ticket_detail.response_sla')}}" id="responseInfo" data-ticket-summary="response-sla">
                            <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <g opacity="0.4">
                                    <path
                                        d="M12 2.25C10.0716 2.25 8.18657 2.82183 6.58319 3.89317C4.97982 4.96451 3.73013 6.48726 2.99218 8.26884C2.25422 10.0504 2.06114 12.0108 2.43735 13.9021C2.81355 15.7934 3.74215 17.5307 5.10571 18.8943C6.46928 20.2579 8.20656 21.1865 10.0979 21.5627C11.9892 21.9389 13.9496 21.7458 15.7312 21.0078C17.5127 20.2699 19.0355 19.0202 20.1068 17.4168C21.1782 15.8134 21.75 13.9284 21.75 12C21.7473 9.41498 20.7192 6.93661 18.8913 5.10872C17.0634 3.28084 14.585 2.25273 12 2.25ZM17.25 12.75H12C11.8011 12.75 11.6103 12.671 11.4697 12.5303C11.329 12.3897 11.25 12.1989 11.25 12V6.75C11.25 6.55109 11.329 6.36032 11.4697 6.21967C11.6103 6.07902 11.8011 6 12 6C12.1989 6 12.3897 6.07902 12.5303 6.21967C12.671 6.36032 12.75 6.55109 12.75 6.75V11.25H17.25C17.4489 11.25 17.6397 11.329 17.7803 11.4697C17.921 11.6103 18 11.8011 18 12C18 12.1989 17.921 12.3897 17.7803 12.5303C17.6397 12.671 17.4489 12.75 17.25 12.75Z"
                                        fill="currentColor" />
                                </g>
                            </svg>
                            <span data-ticket-summary-value="response-sla">Response SLA: N/A</span>
                        </span>
                    </div>

                    <section class="tkd-ticket-summary position-relative">
                        <button id="toggle_tiny_view" class="tkd-summary-expand" type="button" data-bs-toggle="tooltip"
                            title="Expand or collapse ticket" aria-label="Expand or collapse ticket" aria-expanded="true">
                            <i class="bi-arrows-angle-contract"></i>
                        </button>
                        <h1 id="tkt-title" class="tkd-ticket-title mb-2">
                            {{ $ticket->decodedSubject() }}
                            @if ($ticket->merge_primary)
                                <span class="label-merged">{{trans('ticket.ticket_detail.merged')}} - <a
                                        href="{{ url('ticket/' . $ticket->merge_primary) }}">#{{ $ticket->merge_primary }}</a></span>
                            @elseif($ticket->is_merge_primary)
                                <span
                                    class="label-merged-primary">{{ trans('ticket.ticket_detail.Merge_Primary') }}</span>
                            @endif
                        </h1>
                        <div id="tkt-content" class="tkd-ticket-content">
                            @if (CommonHelper::isEmailHtml($ticket->content))
                                <iframe src="{{ route('ticket.mail.body', $ticket->id) }}" class="tkd-mail-frame">
                                </iframe>
                            @else
                                {!! CommonHelper::renderTktContent($ticket->content, $embedded_attachments) !!}
                            @endif
                            <div class="main_attachments attachments mar-top"></div>
                        </div>
                    </section>         
                               
                    <section class="tkd-detail-block" id="ticketDetailBlock">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="tkd-owner-chip">
                                <span>{{ trans('ticket.ticket_detail.created_by') }}</span>
                                <strong>
                                    @if(Auth::user()->getProfileImg(true) != null)
                                        <img src="{{ $creator->getProfileImg() }}" alt="{{ $creator->fullName() }}" class="rounded-circle flex-shrink-0 av-16">
                                    @else
                                        <span class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0 text-white av-16 av-indigo">
                                            {{ $getInitials($ticketCreatorName) }}
                                        </span>
                                    @endif
                                    {{ $ticketCreatorName ?? 'N/A' }}
                                </strong>
                            </div>

                            <div class="tkd-owner-chip ">
                                <span>{{ trans('ticket.ticket_detail.assign_to') }}</span>
                                <strong>
                                    @if (!empty($assigned_to) && $assigned_to->getProfileImg() != \App\Models\User::defaultProfileImg())
                                        <img src="{{ $assigned_to->getProfileImg() }}"
                                            alt="{{ $assigned_to->fullName() }}"
                                            class="rounded-circle flex-shrink-0 av-16">
                                    @else
                                        <span
                                            class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0 text-white av-16 av-teal">
                                            {{ $getInitials($assignedToName) }}
                                        </span>
                                    @endif

                                    {{ $assignedToName ?? 'N/A' }}
                                </strong>
                            </div>

                            <button type="button" class="tkd-icon-plain ms-auto"  id="toggleTicketDetail" data-bs-toggle="tooltip" title="Collapse" aria-label="Expand or collapse ticket" aria-expanded="true">
                                <i class="bi bi-arrows-angle-contract"></i>
                            </button>
                        </div>
                        <div id="ticketDetailContent">
                            <div class="tkd-detail-grid">
                                @foreach ($ticketDetailFields as $field)
                                    <div class="tkd-detail-row{{ !empty($field['email']) ? ' dtsBtnWrapper' : '' }}">
                                        <span class="tkd-detail-label"><i
                                                class="{{ $field['icon'] ?? 'bi bi-info-circle' }}"></i>
                                            {{ $field['label'] }}</span>
                                        <span
                                            class="tkd-detail-value{{ !empty($field['isTags']) ? ' tkd-detail-tags' : '' }}{{ !empty($field['isAsset']) ? ' text-red' : '' }}"
                                            title="{{ strip_tags($field['value']) }}">
                                            @if (!empty($field['isTags']))
                                                {!! $field['value'] !!}
                                            @elseif(!empty($field['isHtml']))
                                                {!! $field['value'] !!}
                                            @else
                                                {{ \Illuminate\Support\Str::limit($field['value'], 30) }}
                                            @endif

                                            @if (!empty($field['email']))
                                                <span class="tkd-detail-email">({{ $field['email'] }})</span>
                                                <button type="button" class="ccb-btn btn dtsbtn tkd-copy-email-btn"
                                                    data-clipboard-text="{{ $field['email'] }}" data-toggle="tooltip"
                                                    data-placement="left" data-original-title="Copy">
                                                    <i class="bi bi-clipboard"></i>
                                                </button>
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                                <form class="ticket_type_fieldset_forms ticket-type-fields-div tkd-ticket-type-form">
                                    @csrf
                                    <div class="tkd-detail-row">
                                        <span class="tkd-detail-label"><i class="bi bi-ticket-perforated"></i>
                                            {{ trans('content.service_ticket_fields.ticket_type') }}</span>
                                        <span id="tkt-problem_type">
                                            <select name="ticket_type" class="ticketType amg-table-pagination-dropdown userModulePageLenth ">
                                                <option value="0">{{ trans('ticket.ticket_detail.normal') }}</option>
                                                @foreach ($ticketTypes as $type)
                                                    <option value="{{ $type->id }}"
                                                        @if ($type->id == $ticket->ticket_type) selected @endif>{{ $type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </span>
                                    </div>
                                    <div class="customFieldset mb-1 p-1"></div>
                                    <div class="fieldsets mb-1 p-1"></div>
                                    <input type="hidden" id="ticketID" name="ticket_id" value="{{ $ticket->id }}" />
                                    @if ($canManageTicketType == 1)
                                        <button id="bugDetailSave" class="btn_ticket_type_save btn btn-primary hide"
                                            type="submit">{{ trans('button.save_changes') }}</button>
                                    @endif
                                </form>
                            </div>

                            @if ($ticket->status_id != 5 && $ticket->status_id != 6)
                                <div class="tkd-management-row">
                                    @can('TicketAddToProblemManagement')
                                        <label for="add-problem-mgt">
                                            <input type="checkbox" id="add-problem-mgt" class="add-problem-mgt form-check-input"
                                                @if (isset($ProblemManagement) && $ProblemManagement->ProblemImpactedTicket == 1) checked @endif>
                                            <span>{{ trans('content.service_ticket_fields.ticket_problem_mgt') }}</span>
                                        </label>
                                    @endcan
                                    @can('TicketAddToIncident')
                                        <label for="add-incident">
                                            <input type="checkbox" id="add-incident" class="add-incident form-check-input"
                                                @if (isset($incident) && $incident->IncidentImpactedTicket == 1) checked @endif>
                                            <span>{{ trans('content.service_ticket_fields.ticket_incident') }}</span>
                                        </label>
                                    @endcan
                                </div>
                            @endif
                        </div>
                    </section>
                    <div id="cc_master" class="border p-3 p-md-4 bg-light mb-4 position-relative">
                        <!-- Header: Label + Update button -->
                        <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                            <label class="fw-semibold fs-4 mb-0 d-flex align-items-center gap-1">
                                <i class="bi bi-envelope"></i>
                                {{ trans("content.service_ticket_fields.CC_EMails") }}:
                            </label>
                            <span class="set1 d-inline-block">
                                <button class="btn_act_update amg-btn amg-btn-primary amg-btn-sm">
                                    {{ trans("button.update") }}
                                </button>
                            </span>
                        </div>

                        <!-- Display value (view mode) -->
                        <p id="cc_master_value" class="set1 text-secondary mb-0 pb-1"></p>

                        <!-- Edit mode (hidden by default) -->
                        <div class="set2 hide mt-3">
                            <p class="text-muted small mb-2">
                                <i class="bi bi-info-circle me-1"></i>
                                {{ trans("content.service_ticket_fields.comma_separated") }}
                            </p>
                            <div class="form-controls">
                                <textarea name="cc_emails" id="cc_emails" class="form-control" rows="3" placeholder="{{ trans('content.service_ticket_fields.Please_enter_CC_Recepients_emails_as_comma_separated') }}"></textarea>
                            </div>
                            <div class="d-flex gap-2 mt-3 flex-wrap">
                                <button class="btn_act_save amg-btn amg-btn-primary amg-btn-sm">
                                    <i class="bi bi-check2 me-1"></i>
                                    {{ trans("button.save_changes") }}
                                </button>
                                <button class="btn_act_cancel amg-btn amg-btn-sm">
                                    <i class="bi bi-x-lg me-1"></i>
                                    {{ trans("button.cancel") }}
                                </button>
                            </div>
                        </div>
                    </div>

                    @if (config('services.task_module.enabled'))
                        @can('TaskRead')
                            @php
                                $completedTasks = $tasks->whereIn('status_id', [4, 7, 9])->count();
                                $completionPercentage =
                                    $tasks->count() > 0 ? round(($completedTasks / $tasks->count()) * 100) : 0;
                                $isTechnicianUser = CommonHelper::userIsTechnician(Auth::id());
                            @endphp
                            @if ($tasks->count() > 0 || $isTechnicianUser)
                                <div class="task-card collapsed current">
                                    <div class="task-card-header" style="cursor:pointer">
                                        <button class="task-header-left" type="button" aria-expanded="false">
                                            <span class="task-title">
                                                {{ trans('content.service_ticket_fields.related_task') }}
                                                <span class="total-task badge badge-light">
                                                    {{ $tasks->count() }}
                                                </span>
                                                <span class="expand-icon">
                                                    <i class="bi bi-chevron-down"></i>
                                                </span>
                                            </span>
                                        </button>
                                        <div class="task-header-right">
                                            <div class="task-header-actions">
                                                @if (!empty($ticket->serviceRequest))
                                                    <a href="{{ url('tickets/requestInfo') }}/{{ $ticket->serviceRequest->id }}{{ request()->query('b') ? '?b=' . request()->query('b') : '' }}"
                                                        class="btn btn-default btn-white right_side_button"
                                                        data-bs-toggle="tooltip" title="Service Request" target="_blank">
                                                        <svg width="16" height="16" viewBox="0 0 21 15"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg"
                                                            class="flex-shrink-0">
                                                            <path
                                                                d="M20.25 5.25C20.4489 5.25 20.6397 5.17098 20.7803 5.03033C20.921 4.88968 21 4.69891 21 4.5V1.5C21 1.10218 20.842 0.720644 20.5607 0.43934C20.2794 0.158035 19.8978 0 19.5 0H1.5C1.10218 0 0.720644 0.158035 0.43934 0.43934C0.158035 0.720644 0 1.10218 0 1.5V4.5C0 4.69891 0.0790176 4.88968 0.21967 5.03033C0.360322 5.17098 0.551088 5.25 0.75 5.25C1.34674 5.25 1.91903 5.48705 2.34099 5.90901C2.76295 6.33097 3 6.90326 3 7.5C3 8.09674 2.76295 8.66903 2.34099 9.09099C1.91903 9.51295 1.34674 9.75 0.75 9.75C0.551088 9.75 0.360322 9.82902 0.21967 9.96967C0.0790176 10.1103 0 10.3011 0 10.5V13.5C0 13.8978 0.158035 14.2794 0.43934 14.5607C0.720644 14.842 1.10218 15 1.5 15H19.5C19.8978 15 20.2794 14.842 20.5607 14.5607C20.842 14.2794 21 13.8978 21 13.5V10.5C21 10.3011 20.921 10.1103 20.7803 9.96967C20.6397 9.82902 20.4489 9.75 20.25 9.75C19.6533 9.75 19.081 9.51295 18.659 9.09099C18.2371 8.66903 18 8.09674 18 7.5C18 6.90326 18.2371 6.33097 18.659 5.90901C19.081 5.48705 19.6533 5.25 20.25 5.25ZM1.5 11.175C2.34772 11.0029 3.10986 10.543 3.65728 9.87319C4.20471 9.20343 4.50376 8.36502 4.50376 7.5C4.50376 6.63498 4.20471 5.79657 3.65728 5.12681C3.10986 4.45705 2.34772 3.99714 1.5 3.825V1.5H6.75V13.5H1.5V11.175ZM19.5 11.175V13.5H8.25V1.5H19.5V3.825C18.6523 3.99714 17.8901 4.45705 17.3427 5.12681C16.7953 5.79657 16.4962 6.63498 16.4962 7.5C16.4962 8.36502 16.7953 9.20343 17.3427 9.87319C17.8901 10.543 18.6523 11.0029 19.5 11.175Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </a>
                                                @endif

                                                @if (
                                                    $ticket->form_id != null &&
                                                        $ticket->form_type != 2 &&
                                                        (Auth::user()->isSuperUser() ||
                                                            $ticket->creator_id == Auth::user()->id ||
                                                            $ticket->assigned_to == Auth::user()->id ||
                                                            $ticket->myApproval))
                                                    <a href="{{ url('requested_form/view/') }}/{{ $ticket->form_id }}{{ request()->query('b') ? '?b=' . request()->query('b') : '' }}"
                                                        class="btn btn-default btn-white right_side_button"
                                                        data-bs-toggle="tooltip" title="{{trans('ticket.create_ticket.view_form')}}" target="_blank">
                                                        <svg width="17" height="17" viewBox="0 0 17 17"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M3 2.4C2.84087 2.4 2.68826 2.46321 2.57574 2.57574C2.46321 2.68826 2.4 2.84087 2.4 3C2.4 3.15913 2.46321 3.31174 2.57574 3.42426C2.68826 3.53679 2.84087 3.6 3 3.6H13.8C13.9591 3.6 14.1117 3.53679 14.2243 3.42426C14.3368 3.31174 14.4 3.15913 14.4 3C14.4 2.84087 14.3368 2.68826 14.2243 2.57574C14.1117 2.46321 13.9591 2.4 13.8 2.4H3ZM4.2 9.6C4.67739 9.6 5.13523 9.41036 5.47279 9.07279C5.81036 8.73523 6 8.27739 6 7.8C6 7.32261 5.81036 6.86477 5.47279 6.52721C5.13523 6.18964 4.67739 6 4.2 6C3.72261 6 3.26477 6.18964 2.92721 6.52721C2.58964 6.86477 2.4 7.32261 2.4 7.8C2.4 8.27739 2.58964 8.73523 2.92721 9.07279C3.26477 9.41036 3.72261 9.6 4.2 9.6ZM4.2 8.4C4.04087 8.4 3.88826 8.33679 3.77574 8.22426C3.66321 8.11174 3.6 7.95913 3.6 7.8C3.6 7.64087 3.66321 7.48826 3.77574 7.37573C3.88826 7.26321 4.04087 7.2 4.2 7.2C4.35913 7.2 4.51174 7.26321 4.62426 7.37573C4.73679 7.48826 4.8 7.64087 4.8 7.8C4.8 7.95913 4.73679 8.11174 4.62426 8.22426C4.51174 8.33679 4.35913 8.4 4.2 8.4ZM6 12.6C6 13.0774 5.81036 13.5352 5.47279 13.8728C5.13523 14.2104 4.67739 14.4 4.2 14.4C3.72261 14.4 3.26477 14.2104 2.92721 13.8728C2.58964 13.5352 2.4 13.0774 2.4 12.6C2.4 12.1226 2.58964 11.6648 2.92721 11.3272C3.26477 10.9896 3.72261 10.8 4.2 10.8C4.67739 10.8 5.13523 10.9896 5.47279 11.3272C5.81036 11.6648 6 12.1226 6 12.6ZM4.8 12.6C4.8 12.4409 4.73679 12.2883 4.62426 12.1757C4.51174 12.0632 4.35913 12 4.2 12C4.04087 12 3.88826 12.0632 3.77574 12.1757C3.66321 12.2883 3.6 12.4409 3.6 12.6C3.6 12.7591 3.66321 12.9117 3.77574 13.0243C3.88826 13.1368 4.04087 13.2 4.2 13.2C4.35913 13.2 4.51174 13.1368 4.62426 13.0243C4.73679 12.9117 4.8 12.7591 4.8 12.6ZM7.2 7.8C7.2 7.64087 7.26321 7.48826 7.37573 7.37573C7.48826 7.26321 7.64087 7.2 7.8 7.2H13.8C13.9591 7.2 14.1117 7.26321 14.2243 7.37573C14.3368 7.48826 14.4 7.64087 14.4 7.8C14.4 7.95913 14.3368 8.11174 14.2243 8.22426C14.1117 8.33679 13.9591 8.4 13.8 8.4H7.8C7.64087 8.4 7.48826 8.33679 7.37573 8.22426C7.26321 8.11174 7.2 7.95913 7.2 7.8ZM7.8 12C7.64087 12 7.48826 12.0632 7.37573 12.1757C7.26321 12.2883 7.2 12.4409 7.2 12.6C7.2 12.7591 7.26321 12.9117 7.37573 13.0243C7.48826 13.1368 7.64087 13.2 7.8 13.2H13.8C13.9591 13.2 14.1117 13.1368 14.2243 13.0243C14.3368 12.9117 14.4 12.7591 14.4 12.6C14.4 12.4409 14.3368 12.2883 14.2243 12.1757C14.1117 12.0632 13.9591 12 13.8 12H7.8ZM3.6 0C2.64522 0 1.72955 0.379285 1.05442 1.05442C0.379285 1.72955 0 2.64522 0 3.6V13.2C0 14.1548 0.379285 15.0705 1.05442 15.7456C1.72955 16.4207 2.64522 16.8 3.6 16.8H13.2C14.1548 16.8 15.0705 16.4207 15.7456 15.7456C16.4207 15.0705 16.8 14.1548 16.8 13.2V3.6C16.8 2.64522 16.4207 1.72955 15.7456 1.05442C15.0705 0.379285 14.1548 0 13.2 0H3.6ZM1.2 3.6C1.2 2.96348 1.45286 2.35303 1.90294 1.90294C2.35303 1.45286 2.96348 1.2 3.6 1.2H13.2C13.8365 1.2 14.447 1.45286 14.8971 1.90294C15.3471 2.35303 15.6 2.96348 15.6 3.6V13.2C15.6 13.8365 15.3471 14.447 14.8971 14.8971C14.447 15.3471 13.8365 15.6 13.2 15.6H3.6C2.96348 15.6 2.35303 15.3471 1.90294 14.8971C1.45286 14.447 1.2 13.8365 1.2 13.2V3.6Z"
                                                                fill="#7F7F7F" />
                                                        </svg>
                                                    </a>
                                                @endif

                                                @if (
                                                    $ticket->form_id != null &&
                                                        $ticket->form_type == 2 &&
                                                        (Auth::user()->isSuperUser() ||
                                                            $ticket->creator_id == Auth::user()->id ||
                                                            $ticket->assigned_to == Auth::user()->id ||
                                                            $ticket->myApproval))
                                                    <a href="{{ url('requested_form/custom_form/view/') }}/{{ $ticket->form_id }}{{ request()->query('b') ? '?b=' . request()->query('b') : '' }}"
                                                        class="btn btn-default btn-white right_side_button"
                                                        data-bs-toggle="tooltip"
                                                        title="{{ trans('service_ticket.service_detail.view_form') }}"
                                                        target="_blank">
                                                        <svg width="17" height="17" viewBox="0 0 17 17"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M3 2.4C2.84087 2.4 2.68826 2.46321 2.57574 2.57574C2.46321 2.68826 2.4 2.84087 2.4 3C2.4 3.15913 2.46321 3.31174 2.57574 3.42426C2.68826 3.53679 2.84087 3.6 3 3.6H13.8C13.9591 3.6 14.1117 3.53679 14.2243 3.42426C14.3368 3.31174 14.4 3.15913 14.4 3C14.4 2.84087 14.3368 2.68826 14.2243 2.57574C14.1117 2.46321 13.9591 2.4 13.8 2.4H3ZM4.2 9.6C4.67739 9.6 5.13523 9.41036 5.47279 9.07279C5.81036 8.73523 6 8.27739 6 7.8C6 7.32261 5.81036 6.86477 5.47279 6.52721C5.13523 6.18964 4.67739 6 4.2 6C3.72261 6 3.26477 6.18964 2.92721 6.52721C2.58964 6.86477 2.4 7.32261 2.4 7.8C2.4 8.27739 2.58964 8.73523 2.92721 9.07279C3.26477 9.41036 3.72261 9.6 4.2 9.6ZM4.2 8.4C4.04087 8.4 3.88826 8.33679 3.77574 8.22426C3.66321 8.11174 3.6 7.95913 3.6 7.8C3.6 7.64087 3.66321 7.48826 3.77574 7.37573C3.88826 7.26321 4.04087 7.2 4.2 7.2C4.35913 7.2 4.51174 7.26321 4.62426 7.37573C4.73679 7.48826 4.8 7.64087 4.8 7.8C4.8 7.95913 4.73679 8.11174 4.62426 8.22426C4.51174 8.33679 4.35913 8.4 4.2 8.4ZM6 12.6C6 13.0774 5.81036 13.5352 5.47279 13.8728C5.13523 14.2104 4.67739 14.4 4.2 14.4C3.72261 14.4 3.26477 14.2104 2.92721 13.8728C2.58964 13.5352 2.4 13.0774 2.4 12.6C2.4 12.1226 2.58964 11.6648 2.92721 11.3272C3.26477 10.9896 3.72261 10.8 4.2 10.8C4.67739 10.8 5.13523 10.9896 5.47279 11.3272C5.81036 11.6648 6 12.1226 6 12.6ZM4.8 12.6C4.8 12.4409 4.73679 12.2883 4.62426 12.1757C4.51174 12.0632 4.35913 12 4.2 12C4.04087 12 3.88826 12.0632 3.77574 12.1757C3.66321 12.2883 3.6 12.4409 3.6 12.6C3.6 12.7591 3.66321 12.9117 3.77574 13.0243C3.88826 13.1368 4.04087 13.2 4.2 13.2C4.35913 13.2 4.51174 13.1368 4.62426 13.0243C4.73679 12.9117 4.8 12.7591 4.8 12.6ZM7.2 7.8C7.2 7.64087 7.26321 7.48826 7.37573 7.37573C7.48826 7.26321 7.64087 7.2 7.8 7.2H13.8C13.9591 7.2 14.1117 7.26321 14.2243 7.37573C14.3368 7.48826 14.4 7.64087 14.4 7.8C14.4 7.95913 14.3368 8.11174 14.2243 8.22426C14.1117 8.33679 13.9591 8.4 13.8 8.4H7.8C7.64087 8.4 7.48826 8.33679 7.37573 8.22426C7.26321 8.11174 7.2 7.95913 7.2 7.8ZM7.8 12C7.64087 12 7.48826 12.0632 7.37573 12.1757C7.26321 12.2883 7.2 12.4409 7.2 12.6C7.2 12.7591 7.26321 12.9117 7.37573 13.0243C7.48826 13.1368 7.64087 13.2 7.8 13.2H13.8C13.9591 13.2 14.1117 13.1368 14.2243 13.0243C14.3368 12.9117 14.4 12.7591 14.4 12.6C14.4 12.4409 14.3368 12.2883 14.2243 12.1757C14.1117 12.0632 13.9591 12 13.8 12H7.8ZM3.6 0C2.64522 0 1.72955 0.379285 1.05442 1.05442C0.379285 1.72955 0 2.64522 0 3.6V13.2C0 14.1548 0.379285 15.0705 1.05442 15.7456C1.72955 16.4207 2.64522 16.8 3.6 16.8H13.2C14.1548 16.8 15.0705 16.4207 15.7456 15.7456C16.4207 15.0705 16.8 14.1548 16.8 13.2V3.6C16.8 2.64522 16.4207 1.72955 15.7456 1.05442C15.0705 0.379285 14.1548 0 13.2 0H3.6ZM1.2 3.6C1.2 2.96348 1.45286 2.35303 1.90294 1.90294C2.35303 1.45286 2.96348 1.2 3.6 1.2H13.2C13.8365 1.2 14.447 1.45286 14.8971 1.90294C15.3471 2.35303 15.6 2.96348 15.6 3.6V13.2C15.6 13.8365 15.3471 14.447 14.8971 14.8971C14.447 15.3471 13.8365 15.6 13.2 15.6H3.6C2.96348 15.6 2.35303 15.3471 1.90294 14.8971C1.45286 14.447 1.2 13.8365 1.2 13.2V3.6Z"
                                                                fill="#7F7F7F" />
                                                        </svg>
                                                    </a>
                                                @endif
                                            </div>
                                            <div class="task-header-progress">
                                                <span class="task-percentage">{{ $completionPercentage }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="fill" style="width:{{ $completionPercentage }}%;"></div>
                                    </div>
                                    <div class="task-section"></div>
                                </div>
                            @endif
                        @endcan
                    @endif

                    <div class="tkd-conversation hide">
                        <div class="d-flex align-items-center justify-content-between mt-1 mb-3">
                            <p class="b2-text tkd-section-title mb-0">{{trans('ticket.ticket_detail.conversation')}}</p>
                            <button id="toggle_conversation_view" class="tkd-icon-plain tkd-conversation-all-toggle"
                                type="button" data-bs-toggle="tooltip" title="Collapse all conversations"
                                aria-label="Collapse or expand conversations" aria-expanded="true">
                                <i class="bi bi-arrows-angle-expand"></i>
                            </button>
                        </div>
                        <div id="ticket_timeline" class="tkd-conversation-list hide"></div>
                        <div class="load-comment hide mt-2 text-center" aria-live="polite">
                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            <span class="b4-text opacity-70">{{trans('ticket.ticket_detail.loading_comment')}}</span>
                        </div>
                    </div>

                    <div class="tkd-reply panel">
                        <div id="comment_loader" class="comment-loader" style="display:none;">
                            <div class="loader-content">
                                <span class="spinner-border" role="status" aria-hidden="true"
                                    style="width:35px;height:35px;"></span>
                                <p>{{trans('ticket.ticket_detail.posting_comment')}}</p>
                            </div>
                        </div>
                        <form name="frm_comment" id="frm_comment" action="{{ url('ticket/add_comment') }}"
                            method="POST" enctype="multipart/form-data" class="form-horizontal">
                            @csrf
                            <input type="hidden" id="id" name="id" value="{{ $ticket->id }}" />
                            <input type="hidden" id="ticket_id" name="ticket_id" value="{{ $ticket->id }}" />
                            <input type="hidden" id="tmp_id" name="tmp_id" value="" />
                            <label class="tkd-message-label b5-text fw-bold mb-2">{{trans('ticket.ticket_detail.message')}} <span>*</span></label>
                            <textarea id="reply_comment" class="comment summernote" name="comment"></textarea>
                             <div id="shows_error"></div>
                            <div id="attachment-dropper-cover" class="amg-uploader tkd-comment-uploader" data-amg-uploader
                                data-multiple="true" data-auto-upload="true" data-max-files="5" data-max-size="10"
                                data-extra-inputs="#frm_comment #ticket_id, #id"
                                data-accept=".jpg,.jpeg,.png,.gif,.xls,.xlsx,.doc,.docx,.ppt,.pdf,.txt,.msg,.zip,.psd,.csv,.eml,.mp4"
                                data-upload-url="{{ url('ticket/attachment/add') }}"
                                data-remove-url="{{ url('ticket/attachment/remove') }}" data-token="{{ csrf_token() }}"
                                data-record-input="#frm_comment #tmp_id" data-extra-inputs="#frm_comment #ticket_id,#id"
                                data-upload-field="attachment">
                                <label class="tkd-upload-label mt-3 mb-1">{{trans('ticket.update_status.attachment')}}</label>
                                <input type="file" id="attachment_input" class="amg-uploader__input" multiple hidden>
                                <div id="attachment-dropper" class="amg-uploader__dropzone">
                                    <div class="amg-uploader__message" style="margin: 0 auto;">
                                        <span style="font-size: 10px"
                                            class="d-flex justify-content-center align-items-center gap-1">
                                            <span class="amg-uploader__icon-wrap"><svg xmlns="http://www.w3.org/2000/svg"
                                                    width="13" height="13" fill="currentColor"
                                                    class="bi bi-cloud-arrow-up-fill" viewBox="0 0 16 16">
                                                    <path
                                                        d="M8 2a5.53 5.53 0 0 0-3.594 1.342c-.766.66-1.321 1.52-1.464 2.383C1.266 6.095 0 7.555 0 9.318 0 11.366 1.708 13 3.781 13h8.906C14.502 13 16 11.57 16 9.773c0-1.636-1.242-2.969-2.834-3.194C12.923 3.999 10.69 2 8 2m2.354 5.146a.5.5 0 0 1-.708.708L8.5 6.707V10.5a.5.5 0 0 1-1 0V6.707L6.354 7.854a.5.5 0 1 1-.708-.708l2-2a.5.5 0 0 1 .708 0z" />
                                                </svg>
                                            </span>
                                            {{trans('ticket.ticket_detail.drap_and_upload')}}
                                        </span>

                                    </div>
                                    <button type="button" id="manual_file_trigger" name="manual_file_trigger"
                                        class="amg-btn amg-btn-outline amg-btn-sm amg-uploader__trigger">
                                        {{ trans('content.service_ticket_fields.Add_Attachment') }}
                                    </button>
                                </div>
                                <div id="attachments" class="amg-uploader__preview tkd-upload-preview"></div>
                                <div class="amg-uploader__error"></div>
                            </div>
                           

                            <div class="d-flex align-items-center justify-content-start mt-3">
                                <button class="amg-btn amg-btn-primary amg-btn-sm px-5" type="submit">
                                    <span>{{trans('ticket.ticket_detail.save_button')}}</span>
                                </button>
                            </div>
                            @if (Auth::user()->hasPermission('service_tickets') && $ticket->hasAccessPrivilege(Auth::user()->id))
                                <div class="form-check small-check mt-3">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="is_note"
                                        name="is_note"
                                        value="1">

                                    <label class="form-check-label tkd-internal-note b7-text opacity-70" for="is_note">
                                        {{ trans('ticket.ticket_detail.make_internal_note') }}
                                    </label>
                                </div>
                            @endif

                            <div class="form-check small-check mt-3">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="add_back_trail"
                                    name="add_back_trail"
                                    value="1"
                                    checked>

                                <label class="form-check-label tkd-internal-note b7-text opacity-70" for="add_back_trail">
                                    {{ trans('ticket.ticket_detail.add_back_trail') }}
                                </label>
                            </div>

                            <div class="form-check small-check mt-3 mb-3">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="follow_cc"
                                    name="follow_cc"
                                    value="1">

                                <label class="form-check-label tkd-internal-note b7-text opacity-70" for="follow_cc">
                                    {{ trans('ticket.ticket_detail.cc_mails') }}
                                </label>
                            </div>
                            <select name="cc_emails" id="cc_emails" class="cc_emails form-control comment_cc"
                                multiple></select>
                            <div id="shows_error_cc"></div>
                        </form>
                    </div>
                </div>

                <div class="tkt-right-section">
                    <div class="d-flex flex-column gap-3">
                        {{-- Feedback section --}}
                        <div id="feedBackRating" data-img="{{ url('images/emo') }}" class="panel hide tkd-panel">
                            <div id="ifFBF" class="hide">
                                <div class="tkd-panel-head">
                                    <span class="b4-text">{{ trans('content.service_ticket_fields.Feedback') }} </span>
                                </div>
                                <div class="p-1">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <div class="tkd-feedback-card">
                                                <div class="d-flex align-items-center gap-3">
                                                    <img id="lfb-img" class="tkd-feedback-emoji" src="#">
                                                    <div>
                                                        <div class="b7-text text-muted">
                                                            {{ trans('content.service_ticket_fields.Last_Feedback') }}
                                                        </div>
                                                        <div id="lfb-score" class="b5-text fw-semibold text-center"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="tkd-feedback-card">
                                                <div class="d-flex align-items-center gap-3">
                                                    <img id="ofb-img" class="tkd-feedback-emoji" src="#">
                                                    <div>
                                                        <div class="b7-text text-muted">
                                                            {{ trans('content.service_ticket_fields.Overall') }}</div>
                                                        <div id="ofb-score" class="b5-text fw-semibold text-center"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @if (($wai == 24 || $wai == 25) && $ticket->status_id == 5 && $ticket->creator_id == Auth::user()->id)
                                            <div class="col-12 text-center mt-2">
                                                <button class="amg-btn amg-btn-secondary js-act-edit-feedback">
                                                    <i class="bi bi-pencil-square me-1"></i>
                                                    {{trans('ticket.ticket_detail.edit_feedback')}}
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- First Feedback Awaited --}}
                            <div id="elseFBF" class="p-1 text-center">
                                <div class="d-flex flex-column align-items-center gap-2">
                                    <i class="bi bi-chat-square-heart fs-2 opacity-50"></i>
                                    <div class="b5-text">{{ trans('content.service_ticket_fields.First_Feedback_Awaited') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="tkd-panel panel hide">
                            <div class="tkd-panel-head d-flex align-items-center justify-content-between tkd-update-header"
                                data-bs-toggle="collapse" data-bs-target="#updateTicket" role="button"
                                aria-expanded="true" aria-controls="updateTicket">
                                <div class="d-flex align-items-center gap-2">
                                    <svg class="chevron" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="1.5">
                                        <polyline points="6 9 12 15 18 9" />
                                    </svg>
                                    <span class="b4-text tkd-panel-title">{{trans('ticket.ticket_detail.update_detail')}}</span>
                                    <span class="js-act-status-approval btn btn-link p-0" data-toggle="tooltip"
                                        data-title="Status Approval" id="openCanvas">
                                        <i class="bi bi-shield-check"></i>
                                    </span>
                                </div>
                                <div id="last-feed-back" class="fb"></div>
                            </div>

                            <div id="updateTicket" class="collapse show">
                                <div class="tkd-update-form p-3">
                                    <form id="frm_update_status" name="frm_update_status" action="#"
                                        class="form-horizontal d-flex flex-column gap-3">
                                        @csrf
                                        <input type="hidden" id="id" name="id"
                                            value="{{ $ticket->id }}" />
                                        <input type="hidden" id="ticket_status_form_id" name="ticket_status_form_id"
                                            value="" />
                                        <input type="hidden" id="tmp_id" name="tmp_id" value="" />
                                        <input type="hidden" id="ticket_id" name="ticket_id"
                                            value="{{ $ticket->id }}" />
                                        <!-- Change Status -->
                                        <div>
                                            <label class="d-block b5-text mb-1 opacity-70 required">{{trans('ticket.ticket_detail.change_detail')}}</label>
                                            <select class="tkd-select2 select2" id="status_id" name="status_id">
                                                @foreach ($statuses as $statusOption)
                                                    <option value="{{ $statusOption->id }}"
                                                        form="{{ $statusOption->status_form ?? 0 }}"
                                                        @if ($ticket->status_id == $statusOption->id) selected @endif>
                                                        {{ $statusOption->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Change Priority -->
                                        <div>
                                            <label class="d-block b5-text mb-1 opacity-70 required">{{trans('ticket.ticket_detail.change_priority')}}</label>
                                            <select name="priority_id" id="status_priority_id"
                                                class="tkd-select2 select2"
                                                @if (!empty($action_controls) && $action_controls['ctrl_priority'] != 1) disabled @endif></select>
                                        </div>

                                        <!-- TAT -->
                                        <div>
                                            <label class="d-block b5-text mb-1 opacity-70 required">{{trans('ticket.edit_ticket.tat')}}</label>
                                            <div class="input-group">
                                                <input type="text" id="tat"
                                                    class="form-control under-input-group" name="tat"
                                                    value="{{ $ticket->tat }}"
                                                    @if (!empty($action_controls) && $action_controls['ctrl_tat'] != 1) disabled @endif />
                                                <span class="input-group-text">{{trans('ticket.ticket_detail.hrs')}}</span>
                                            </div>
                                        </div>

                                        <!-- Start/End Time (hidden by default) -->
                                        <div class="hide" id="need_time_duration">
                                            <label class="d-block b5-text mb-1 opacity-70 required">{{trans('ticket.ticket_detail.start_time')}}</label>
                                            <input type="datetime-local" name="start_time" id="start_time"
                                                class="form-control start_time" />
                                            <label class="d-block b5-text mb-1 mt-2 opacity-70 required">{{trans('ticket.ticket_detail.end_time')}}</label>
                                            <input type="datetime-local" name="end_time" id="end_time"
                                                class="form-control end_time" />
                                        </div>

                                        <!-- Comment with Summernote -->
                                        <span class="d-block b5-text mb-1 opacity-70 required">{{trans('ticket.ticket_detail.message')}}</span>
                                        <div id="summernote-wrapper" class="final-comment-wrapper">
                                            <textarea id="status_comment" class="comment summernote" name="comment"></textarea>
                                            <div id="shows_error"></div>
                                        </div>

                                        <!-- Attachment Uploader -->
                                        <div id="update-dropper-cover"
                                            class="amg-uploader tkd-comment-uploader tkd-update-uploader mt-2"
                                            data-amg-uploader data-multiple="true" data-auto-upload="true"
                                            data-max-files="5" data-max-size="10"
                                            data-accept=".jpg,.jpeg,.png,.gif,.xls,.xlsx,.doc,.docx,.ppt,.pdf,.txt,.msg,.zip,.psd,.csv,.eml"
                                            data-upload-url="{{ url('ticket/attachment/add') }}"
                                            data-remove-url="{{ url('ticket/attachment/remove') }}"
                                            data-token="{{ csrf_token() }}"
                                            data-record-input="#frm_update_status #tmp_id"
                                            data-extra-inputs="#frm_update_status #ticket_id"
                                            data-upload-field="attachment">
                                            <label class="tkd-upload-label mb-1">{{trans('ticket.update_status.attachment')}}</label>
                                            <input type="file" id="update_attachment_input"
                                                class="amg-uploader__input" multiple hidden>
                                            <div id="update-dropper" class="amg-uploader__dropzone d-flex flex-column">
                                                <div class="amg-uploader__message">
                                                    <span style="font-size: 10px"
                                                        class="d-flex justify-content-center align-items-center gap-1">
                                                        <span class="amg-uploader__icon-wrap"><svg
                                                                xmlns="http://www.w3.org/2000/svg" width="13"
                                                                height="13" fill="currentColor"
                                                                class="bi bi-cloud-arrow-up-fill" viewBox="0 0 16 16">
                                                                <path
                                                                    d="M8 2a5.53 5.53 0 0 0-3.594 1.342c-.766.66-1.321 1.52-1.464 2.383C1.266 6.095 0 7.555 0 9.318 0 11.366 1.708 13 3.781 13h8.906C14.502 13 16 11.57 16 9.773c0-1.636-1.242-2.969-2.834-3.194C12.923 3.999 10.69 2 8 2m2.354 5.146a.5.5 0 0 1-.708.708L8.5 6.707V10.5a.5.5 0 0 1-1 0V6.707L6.354 7.854a.5.5 0 1 1-.708-.708l2-2a.5.5 0 0 1 .708 0z" />
                                                            </svg>
                                                        </span>
                                                        {{trans('ticket.ticket_detail.drap_and_upload')}}
                                                    </span>
                                                </div>
                                                <button type="button" id="update_file_trigger"
                                                    name="update_file_trigger"
                                                    class="amg-btn amg-btn-outline amg-btn-sm amg-uploader__trigger"
                                                    style="font-size: 9px;">
                                                    {{ trans('content.service_ticket_fields.Add_Attachment') }}
                                                </button>
                                            </div>
                                            <div id="attachment_updates" class="amg-uploader__preview tkd-upload-preview">
                                            </div>
                                            <div class="amg-uploader__error"></div>
                                        </div>

                                        <div id="custom_field_data_section"></div>

                                        <!-- Workaround checkbox (conditional) -->
                                        @if (isset($ticket->workaround_sla) && !is_null($ticket->workaround_sla))
                                            <label for="is_workaround"
                                                class="tkd-internal-note d-flex align-items-center gap-2 mb-0 comment_cc">
                                                <input type="checkbox" id="is_workaround" name="is_workaround"
                                                    value="1" class="form-check-input"/>
                                                <span class="b7-text opacity-70">{{trans('ticket.ticket_detail.is_workaround')}}</span>
                                            </label>
                                        @endif

                                        <!-- Add back-trail -->
                                        <label for="update_add_back_trail"
                                            class="tkd-internal-note d-flex align-items-center gap-2 mb-0 comment_cc">
                                            <input type="checkbox" id="update_add_back_trail" name="add_back_trail"
                                                value="1" class="form-check-input"/>
                                            <span class="b7-text opacity-70">{{trans('ticket.update_status.add_back_trail')}}</span>
                                        </label>

                                        <!-- CC Emails toggle -->
                                        <label for="follow_cc"
                                            class="tkd-internal-note d-flex align-items-center gap-2 mb-0 comment_cc">
                                            <input type="checkbox" id="follow_cc" name="follow_cc" value="1" class="form-check-input"/>
                                            <span class="b7-text opacity-70">{{trans('ticket.ticket_detail.cc_mails')}}</span>
                                        </label>

                                        <div class="comment_cc">
                                            <select name="cc_emails" id="update_cc_emails"
                                                class="cc_emails tkd-select2 select2 form-control comment_cc"
                                                multiple></select>
                                        </div>

                                        <div id="shows_error_cc"></div>

                                        @if (isset($ticket->formData) && in_array(config('app.client'), ['rolepermission', 'grdemo', 'ltts']))
                                            <input type="hidden" name="formData"
                                                value="{{ json_encode($ticket->formData) }}">
                                        @endif

                                        <div class="hide revoke_access" id="revoke_access_div">
                                            @if (in_array(config('app.client'), ['rolepermission', 'grdemo', 'ltts']) && $category['privilege_access'] == 1)
                                                <label for="revoke_access">
                                                    <input type="checkbox" name="revoke_access" id="revoke_access"
                                                        value="1" class="b7-text opacity-70 form-check-input"
                                                        @if (isset($ticket->revoke_access_at) && $ticket->revoke_access_at != null) checked @endif>
                                                    {{ trans('ticket.ticket_detail.revoke_access') }}
                                                </label>
                                            @endif
                                        </div>

                                        <div class="row" id="set_edit_status_form"></div>

                                        <button class="amg-btn amg-btn-primary amg-btn-sm" type="button" id="btnSubmit">
                                            <span>{{trans('ticket.ticket_detail.update_ticket')}}</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @php
                            $isTicketCreator = Auth::id() == $ticket->creator_id;
                        @endphp

                        @if ($isTicketCreator)
                            @include('tickets.partials.assign_section')
                            @include('tickets.partials.creator_section')
                        @else
                            @include('tickets.partials.creator_section')
                            @include('tickets.partials.assign_section')
                        @endif

                        @if (Auth::user()->hasAnyRole(['SuperAdmin', 'Admin']) || Auth::user()->hasPermission('service_tickets'))
                            <div class="tkd-panel">
                                <div class="tkd-panel-head d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="b4-text">
                                            {{ trans('content.service_ticket_fields.tags') }}
                                        </span>
                                    </div>
                                    {{-- @if (isset($_REQUEST['b']) && $_REQUEST['b'] != 'archived' && !in_array($ticket->status_id, [5, 6])) --}}
                                    <a href="javascript:void(0)" data-ticketId="{{ $ticket->id }}"
                                        class="getTicketTags js-act-edit-tags amg-link-sm me-2" data-toggle="modal"
                                        data-target="#mdl-add-tag">
                                        <i class="bi bi-tags"></i>
                                       {{trans('ticket.ticket_detail.add_tags')}}
                                    </a>
                                    {{-- @endif --}}
                                </div>

                                <div class="p-3 pt-2" id="tags_panel">
                                    @if (count($tags) > 0)
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($tags as $tag)
                                                @if (!empty($tag->tags))
                                                    <span class="tkd-tag">
                                                        {{ $tag->tags }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="b7-text opacity-50">
                                           {{trans('ticket.ticket_detail.no_tags_found')}}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Event Section --}}
                        @if (count($today_events))
                            <div class="tkd-panel">
                                <div class="tkd-panel-head d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="b4-text">{{trans('ticket.ticket_detail.today_meetings')}}</span>
                                    </div>
                                </div>
                                <div class="tkd-meeting-slider p-3">
                                    <div id="meetingSlider" class="carousel slide" data-bs-ride="carousel">
                                        <div class="carousel-inner">
                                            @foreach ($today_events as $key => $event)
                                                <div class="carousel-item {{ $loop->first ? 'active' : '' }} meeting-details-card"
                                                    data-id="{{ $event['id'] }}">
                                                    <div class="tkd-meeting-card">
                                                        <div class="tkd-meeting-header">
                                                            <h5 class="b5-text fw-semibold mb-0" data-bs-toggle="tooltip"
                                                                data-bs-title="{{ $event['subject'] ?? '' }}">
                                                                {{ \Illuminate\Support\Str::limit($event['subject'], 30, '...') }}
                                                            </h5>
                                                        </div>
                                                        <div
                                                            class="tkd-meeting-time d-flex align-items-center gap-2 mt-2 mb-2">
                                                            <svg width="14" height="14" viewBox="0 0 20 20"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M10 0C8.68678 0 7.38642 0.258658 6.17317 0.761205C4.95991 1.26375 3.85752 2.00035 2.92893 2.92893C1.05357 4.8043 0 7.34784 0 10C0 12.6522 1.05357 15.1957 2.92893 17.0711C3.85752 17.9997 4.95991 18.7362 6.17317 19.2388C7.38642 19.7413 8.68678 20 10 20C12.6522 20 15.1957 18.9464 17.0711 17.0711C18.9464 15.1957 20 12.6522 20 10C20 8.68678 19.7413 7.38642 19.2388 6.17317C18.7362 4.95991 17.9997 3.85752 17.0711 2.92893C16.1425 2.00035 15.0401 1.26375 13.8268 0.761205C12.6136 0.258658 11.3132 0 10 0ZM14.2 14.2L9 11V5H10.5V10.2L15 12.9L14.2 14.2Z"
                                                                    fill="#188544" />
                                                            </svg>
                                                            <span class="b7-text opacity-70" data-bs-toggle="tooltip"
                                                                data-bs-title="{{ \Carbon\Carbon::parse($event['start_date_time'])->format('d M Y h:i A') }} - {{ \Carbon\Carbon::parse($event['end_date_time'])->format('d M Y h:i A') }}">
                                                                {{ \Carbon\Carbon::parse($event['start_date_time'])->format('d M Y h:i A') }}
                                                                -
                                                                {{ \Carbon\Carbon::parse($event['end_date_time'])->format('d M Y h:i A') }}
                                                            </span>
                                                        </div>
                                                        <div class="tkd-meeting-attendees d-flex flex-wrap gap-3 mt-2">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <img src="{{ $event['technician_avatar'] ?? asset('imgs/profile-40.jpg') }}"
                                                                    alt="{{ $event['technician_name'] }}"
                                                                    class="rounded-circle" width="28" height="28">
                                                                <div>
                                                                    <div class="b7-text text-muted">{{trans('ticket.ticket_detail.Organizer')}}</div>
                                                                    <div class="b7-text fw-semibold"
                                                                        data-bs-toggle="tooltip"
                                                                        data-bs-title="{{ $event['technician_name'] ?? '' }}">
                                                                        {{ \Illuminate\Support\Str::limit($event['technician_name'], 15, '...') }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <img src="{{ $event['creator_avatar'] ?? asset('imgs/profile-40.jpg') }}"
                                                                    alt="{{ $event['creator_name'] }}"
                                                                    class="rounded-circle" width="28" height="28">
                                                                <div>
                                                                    <div class="b7-text text-muted">{{trans('ticket.ticket_detail.Attender')}}</div>
                                                                    <div class="b7-text fw-semibold"
                                                                        data-bs-toggle="tooltip"
                                                                        data-bs-title="{{ $event['creator_name'] ?? '' }}">
                                                                        {{ \Illuminate\Support\Str::limit($event['creator_name'], 15, '...') }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        @if (count($today_events) > 1)
                                            <button class="carousel-control-prev" type="button"
                                                data-bs-target="#meetingSlider" data-bs-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">{{trans('ticket.ticket_detail.Previous')}}</span>
                                            </button>
                                            <button class="carousel-control-next" type="button"
                                                data-bs-target="#meetingSlider" data-bs-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">{{trans('ticket.ticket_detail.Next')}}</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (count($d) > 0 && config('services.knowledge_document.enabled'))
                            @can('KnowledgeDocumentRead')
                                <div class="tkd-panel">
                                    <div class="tkd-panel-head d-flex align-items-center justify-content-between">
                                        <span class="b4-text">
                                            {{ trans('content.service_ticket_fields.knowledge_document') }}
                                        </span>

                                        <div class="d-flex gap-1">
                                            <button class="kb-nav" type="button" data-bs-target="#kdSlider"
                                                data-bs-slide="prev">
                                                <i class="bi bi-chevron-left"></i>
                                            </button>

                                            <button class="kb-nav" type="button" data-bs-target="#kdSlider"
                                                data-bs-slide="next">
                                                <i class="bi bi-chevron-right"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="tkd-kd-slider">
                                        <div id="kdSlider" class="carousel slide" data-bs-ride="carousel"
                                            data-bs-interval="5000">
                                            <div class="carousel-inner">
                                                @foreach ($d as $kd)
                                                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                                        <a href="{{ url('knowledge_document/article/view/' . $kd->id) }}"
                                                            target="_blank" class="text-decoration-none">
                                                            <div class="kb-card">
                                                                <img src="{{ !empty($kd->card_img) ? asset('uploads/article/' . $kd->card_img) : asset('imgs/kd_image.png') }}"
                                                                    alt="{{ $kd->title }}" class="kb-card-img">
                                                                <div class="kb-card-body">
                                                                    <div class="kb-title">
                                                                        {{ $kd->title }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endcan
                        @endif

                        <div class="tkd-panel">
                            <a class="d-flex align-items-center px-3 py-2 border-bottom rounded-top tkd-panel-head gap-2"
                                data-bs-toggle="collapse" href="#tkdTimeline" role="button" aria-expanded="true"
                                aria-controls="tkdTimeline">
                                <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.5">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                                <span class="b4-text">{{trans('ticket.ticket_detail.ticket_timeline')}}</span>

                            </a>
                            <div id="tkdTimeline" class="collapse show">
                                <div class="p-3">
                                    <div class="tkd-tl d-flex flex-column overflow-auto ps-1">
                                        <div class="tkd-tl-item d-flex gap-2 align-items-start position-relative">
                                            <div class="tkd-tl-dot"></div>
                                            <div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="b6-text">{{ trans('ticket.ticket_detail.created_by') }}</div>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <a href="{{ config('app.url') }}/user/info/{{$creator->id}}">
                                                            @if (!empty($creator) && $creator->getProfileImg() != \App\Models\User::defaultProfileImg())
                                                                <img src="{{ $creator->getProfileImg() }}"
                                                                    alt="{{ $creator->fullName() }}"
                                                                    class="rounded-circle flex-shrink-0 av-16">
                                                            @else
                                                            <span
                                                                    class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0 text-white av-16 av-teal">
                                                                    {{ $getInitials($ticketCreatorName) }}
                                                                </span>
                                                            @endif

                                                            <span class="b6-text fw-normal">
                                                                {{ $ticketCreatorName ?? 'N/A' }}
                                                            </span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="b7-text fw-regular fst-italic opacity-70">
                                                    {{ $ticketCreatedAt ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tkd-tl-item d-flex gap-2 align-items-start position-relative">
                                            <div class="tkd-tl-dot"></div>
                                            <div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="b6-text">{{ trans('ticket.ticket_detail.assign_to') }}</div>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <a href="{{ config('app.url') }}/user/info/{{$creator->id}}">

                                                            @if (!empty($assigned_to) && $assigned_to->getProfileImg() != \App\Models\User::defaultProfileImg())
                                                                <img src="{{ $assigned_to->getProfileImg() }}"
                                                                    alt="{{ $assigned_to->fullName() }}"
                                                                    class="rounded-circle flex-shrink-0 av-16">
                                                            @else
                                                            <span
                                                                    class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0 text-white av-16 av-teal">
                                                                    {{ $getInitials($assignedToName) }}
                                                                </span>
                                                            @endif

                                                            <span class="b6-text fw-normal">
                                                                {{ $assignedToName ?? 'N/A' }}
                                                            </span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="b7-text fw-regular fst-italic opacity-70">
                                                    {{ $ticketUpdatedAt ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tkd-panel people-section">
                            <a class="d-flex align-items-center px-3 py-2 border-bottom rounded-top tkd-panel-head gap-2"
                                data-bs-toggle="collapse" href="#tkdPeople" role="button" aria-expanded="true"
                                aria-controls="tkdPeople">
                                <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.5">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                                <span class="b4-text">{{ trans('ticket.ticket_detail.People') }}</span>
                            </a>

                            <div id="tkdPeople" class="collapse show">
                                <div class="py-2 px-3 d-flex flex-column gap-2">

                                    <div class="d-flex align-items-center justify-content-start gap-2">
                                        <span class="b6-text">{{ trans('ticket.ticket_detail.assign_to') }}</span>
                                        <span class="d-flex align-items-center gap-1">
                                            @if (!empty($assigned_to) && $assigned_to->getProfileImg() != \App\Models\User::defaultProfileImg())
                                                <img src="{{ $assigned_to->getProfileImg() }}"
                                                    alt="{{ $assigned_to->fullName() }}"
                                                    class="rounded-circle flex-shrink-0 av-16">
                                            @else
                                            <span
                                                    class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0 text-white av-16 av-teal">
                                                    {{ $getInitials($assignedToName) }}
                                                </span>
                                            @endif

                                            <span class="b6-text fw-normal">
                                                {{ $assignedToName ?? 'N/A' }}
                                            </span>
                                        </span>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-start gap-2">
                                        <span class="b6-text">{{ trans('ticket.ticket_detail.created_by') }}</span>
                                        <span class="d-flex align-items-center gap-1">
                                            @if(Auth::user()->getProfileImg(true) != null)
                                                <img src="{{ $creator->getProfileImg() }}"
                                                    alt="{{ $creator->fullName() }}"
                                                    class="rounded-circle flex-shrink-0 av-16">
                                            @else
                                            <span
                                                    class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0 text-white av-16 av-indigo">
                                                    {{ $getInitials($ticketCreatorName) }}
                                                </span>
                                            @endif

                                            <span class="b6-text fw-normal">
                                                {{ $ticketCreatorName ?? 'N/A' }}
                                            </span>
                                        </span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        @include('tickets.ai_assist_modal')
        @include('tickets.assign_to_modal')
        @include('tickets.ticket-list.edit-ticket')
        @include('tickets.ticket-list.ticket-transfar')
        @include('tickets.ticket-list.ticket-history')
        @include('tickets.feedback_modal')
        @include('tickets.reopen_modal')
        @include('tickets.ticket_sentiment')
        @include('tickets.change_creator_modal')
        @include("tickets.feedback_update")
        @include('tickets.ticket-list.modal_delete')
        @include("tickets.ticket-list.sentiment_analysis")
        @include('tickets.add_tag')
        @include('tickets.status_form_modal')
        @include('tickets.task_update')
        @include('tickets.add_problem')
        @include('tickets.add_to_incident')
        @include('tickets.book_calendar_list_mdl')
        @include('tickets.add_block_calendar_mdl')
        @include('tickets.add_event_comment')
        @include('tickets.fields_info')
        @include('task-management.taskHistory')
        @include('tickets/modal_description')
        @include('tickets.ticket-list.add-task-modal')
        @include('tickets/status-approval/approval')
        @include('tickets.form_modal')
        <a id="temp"></a>
    </div>
@endsection

@push('css')
    <link href="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.css') !!}" rel="stylesheet" />
    <link href="{!! CommonHelper::asset('plugins/swipebox/css/swipebox.min.css') !!}" rel="stylesheet" />
    <link href="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.css') !!}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.css">
    <link href="{!! CommonHelper::asset('plugins/flatpicker/css/flatpicker.min.css') !!}" rel="stylesheet" />
    <style>
    :root {
        --tkd-danger: #ed1117;
    }
    .error {
            display: block ;
            margin-top: 4px ;
            color: #dc3545 ;
            font-size: 0.875em ;
            clear: both ;   /* prevent floating */
            width: 100% ;   /* force full width inside the column */
        }

    .sentiment-modal {
        --sm-bg: var(--bs-body-bg);
        --sm-card: var(--bs-tertiary-bg);
        --sm-border: var(--bs-border-color);
        --sm-text: var(--bs-body-color);
        --sm-muted: var(--bs-secondary-color);

        border: 0;
        border-radius: 18px;
        background: var(--sm-bg);
        color: var(--sm-text);
        overflow: hidden;

        box-shadow:
            0 10px 30px rgba(0, 0, 0, .12),
            0 3px 10px rgba(0, 0, 0, .05);

        transition: all .25s ease;
    }

    /* DARK MODE */
    [data-bs-theme="dark"] .sentiment-modal {
        box-shadow:
            0 12px 35px rgba(0, 0, 0, .45),
            0 3px 12px rgba(0, 0, 0, .2);
    }

    /* MODAL SPACING */
    .sentiment-modal .modal-header,
    .sentiment-modal .modal-body,
    .sentiment-modal .modal-footer {
        border: 0;
        padding-inline: 1.25rem;
    }

    .sentiment-modal .modal-header {
        padding-top: 1.15rem;
        padding-bottom: .6rem;
    }

    .sentiment-modal .modal-body {
        padding-bottom: 1rem;
    }

    .sentiment-modal .modal-footer {
        padding-top: 0;
        padding-bottom: 1.2rem;
    }

    /* =========================
       TITLE
    ========================= */

    .sentiment-title {
        width: 100%;
        text-align: center;
        font-size: 1.3rem;
        font-weight: 700;
        letter-spacing: -.3px;
    }

    /* =========================
       STATUS
    ========================= */

    .sentiment-status {
        text-align: center;
        margin-bottom: 1rem;
    }

    .emoji-wrap {
        width: 62px;
        height: 62px;
        margin: auto;
        border-radius: 50%;

        display: grid;
        place-items: center;

        font-size: 2rem;

        background: rgba(var(--bs-danger-rgb), .12);

        transition: .25s ease;
    }

    .negative-text {
        color: var(--bs-danger);
        font-size: 1.08rem;
        font-weight: 700;
        margin-top: .8rem;
    }

    .sentiment-sub-text {
        color: var(--sm-muted);
        font-size: .82rem;
        margin: .3rem 0 0;
        line-height: 1.45;
    }

    /* =========================
       COMMON CARD
    ========================= */

    .score-card,
    .explanation-card,
    .tone-card {
        background: var(--sm-card);
        border: 1px solid var(--sm-border);
        border-radius: 16px;
        padding: 1rem;

        transition: all .2s ease;
    }

    .score-card,
    .explanation-card {
        margin-bottom: .95rem;
    }

    /* =========================
       SCORE
    ========================= */

    .score-label,
    .score-footer,
    .progress-labels,
    .tone-title {
        font-size: .72rem;
        color: var(--sm-muted);
    }

    .score-value {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
        margin-top: .2rem;

        color: var(--bs-danger);

        transition: .25s ease;
    }

    .score-badge {
        padding: .38rem .8rem;
        border-radius: 50rem;

        font-size: .72rem;
        font-weight: 700;

        transition: .25s ease;
    }

    /* BADGE STATES */

    .score-badge.positive {
        background: rgba(var(--bs-success-rgb), .14);
        color: var(--bs-success);
    }

    .score-badge.neutral {
        background: rgba(var(--bs-warning-rgb), .14);
        color: var(--bs-warning);
    }

    .score-badge.negative {
        background: rgba(var(--bs-danger-rgb), .14);
        color: var(--bs-danger);
    }

    /* =========================
       PROGRESS
    ========================= */

    .progress-wrapper {
        margin-top: .2rem;
    }

    .sentiment-progress {
        height: 12px;
        border-radius: 50rem;

        position: relative;
        overflow: hidden;

        margin: .85rem 0 .4rem;

        background: linear-gradient(90deg,
                #ef4444 0%,
                #f97316 25%,
                #facc15 50%,
                #84cc16 75%,
                #22c55e 100%);
    }

    .progress-indicator {
        position: absolute;
        top: 50%;
        left: 50%;

        width: 16px;
        height: 16px;

        border-radius: 50%;

        background: #111;
        border: 3px solid #fff;

        transform: translate(-50%, -50%);

        box-shadow: 0 2px 6px rgba(0, 0, 0, .2);

        transition: left .3s ease;
    }

    [data-bs-theme="dark"] .progress-indicator {
        background: #fff;
        border-color: #111;
    }

    .progress-labels {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .negative-label {
        color: var(--bs-danger);
    }

    .neutral-label {
        color: var(--bs-warning);
    }

    .positive-label {
        color: var(--bs-success);
    }

    /* =========================
       FOOTER
    ========================= */

    .score-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;

        margin-top: .9rem;
        gap: .5rem;
        flex-wrap: wrap;
    }

    .footer-status {
        font-weight: 600;
        color: var(--sm-text);
    }

    /* =========================
       EXPLANATION
    ========================= */

    .section-title {
        display: flex;
        align-items: center;
        gap: .75rem;

        font-size: .92rem;
        font-weight: 700;

        margin-bottom: .7rem;
    }

    .icon-circle,
    .tone-icon {
        width: 36px;
        height: 36px;

        border-radius: 10px;

        display: grid;
        place-items: center;

        flex-shrink: 0;
    }

    .icon-circle {
        background: rgba(var(--bs-info-rgb), .15);
        color: var(--bs-info);
    }

    .tone-icon {
        background: rgba(var(--bs-warning-rgb), .15);
        color: var(--bs-warning);
    }

    .explanation-content,
    .tone-description {
        font-size: .84rem;
        line-height: 1.6;
        color: var(--sm-text);
    }

    /* =========================
       TONE CARD
    ========================= */

    .tone-card {
        display: flex;
        align-items: center;
        gap: .8rem;
    }

    .tone-content {
        flex: 1;
    }

    .tone-title {
        margin-bottom: .2rem;
        font-weight: 600;
    }

    /* =========================
       BUTTON
    ========================= */

    .sentiment-close-btn {
        min-width: 115px;

        border: 0;
        border-radius: 12px;

        padding: .58rem 1rem;

        font-size: .83rem;
        font-weight: 600;

        background: var(--bs-dark);
        color: #fff;

        transition: .2s ease;
    }

    .sentiment-close-btn:hover {
        opacity: .92;
        transform: translateY(-1px);
    }

    [data-bs-theme="dark"] .sentiment-close-btn {
        background: var(--bs-light);
        color: var(--bs-dark);
    }

    /* =========================
       MOBILE
    ========================= */

    @media (max-width: 576px) {

        .sentiment-modal .modal-header,
        .sentiment-modal .modal-body,
        .sentiment-modal .modal-footer {
            padding-inline: 1rem;
        }

        .sentiment-title {
            font-size: 1.08rem;
        }

        .emoji-wrap {
            width: 56px;
            height: 56px;
            font-size: 1.7rem;
        }

        .score-value {
            font-size: 1.55rem;
        }

        .score-footer {
            flex-direction: column;
            align-items: flex-start;
        }

        .tone-card {
            align-items: flex-start;
        }
    }
        /* Ticket Sentiment modal */

        /* comment color  */
        .color-code-yellow-text {
            color: #ffa726 !important;
        }
        .color-code-blue-text {
                color: #42518C !important;
        }
        .color-code-rose-text {
                color: #f275ad !important;
        }

        .tkd-feedback-emoji{
            height:26px;
        }

        .cc-more-mails{
            cursor:pointer;
            font-weight:600;
        }

        .ticketType.userModulePageLenth:disabled + .select2 .select2-selection {
            background-color: #f5f5f5 !important;
            cursor: not-allowed;
        }

        [data-bs-theme="dark"] .label-spam-info_ticket {
            background: var(--dark-secondary, #2a2a2a) !important;
            border-color: var(--dark-border, #2a2a2d) !important;
            color: var(--text-muted, #b7b7b7) !important;
        }

        #mdlDeleteTicket .label-spam-info_ticket {
            background-color: #9ca3af !important;
            color: #fff;
        }

        #updateTicket .amg-uploader__item {
            width: 250px !important;
        }

        #tkt-problem_type .select2-dropdown--below {
            width: 140px !important;
        }

        .fieldsets .form-control {
            width: 180px;
        }

        .js-act-status-approval i {
            color: white !important;
        }

        #assigned_user_panel .b7-text.text-muted,
        #creator_info_panel .b7-text.text-muted {
            display: flex;
            justify-content: space-between;
        }

        #assigned_user_panel .b7-text.text-muted span,
        #creator_info_panel .b7-text.text-muted span {
            margin-right: 2rem;
        }

        .tkt-right-section {
            padding: .8rem;
            border-top: 1px solid var(--tkd-border);
            border-left: 1px solid var(--tkd-border);
            border-bottom: 1px solid var(--tkd-border);
            border-bottom: unset;
        }

        .tk-kd-card {
            position: relative;
            min-height: 14.75rem;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            border-radius: 0.75rem;
            padding: 1.25rem 4.5rem 1.25rem 1.25rem;
            overflow: visible;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            isolation: isolate;
            box-shadow: 0 0.75rem 1.625rem rgba(15, 23, 42, 0.16);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .task-pane-inner {
            border: 1px solid var(--tkd-border);
            border-radius: 5px;
            width: 100%;
        }

        .feedback-emoji {
            width: 56px;
            height: 56px;
            object-fit: contain;
            transition: transform .2s ease;
            cursor: pointer;
        }

        .option1 label:hover .feedback-emoji {
            transform: scale(1.08);
        }

        .tkd-kd-card::before {
            position: absolute;
            content: "";
            inset: 0;
            z-index: -1;
            border-radius: inherit;
            background:
                linear-gradient(180deg, rgba(0, 0, 0, 0.08) 0%, rgba(0, 0, 0, 0.18) 42%, rgba(0, 0, 0, 0.76) 100%),
                linear-gradient(90deg, rgba(0, 0, 0, 0.28), rgba(0, 0, 0, 0.02));
            pointer-events: none;
        }

        .tkd-kd-card-default::after {
            position: absolute;
            content: "";
            inset: 0;
            z-index: -1;
            border-radius: inherit;
            opacity: 0.42;
            background:
                repeating-radial-gradient(ellipse at 5% 100%, transparent 0 0.9375rem, rgba(255, 255, 255, 0.34) 1rem 1.0625rem, transparent 1.125rem 1.875rem),
                linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.05));
            pointer-events: none;
        }

        .tkd-kd-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 1rem 2rem rgba(15, 23, 42, 0.22);
        }

        .tkd-tag {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            line-height: 1.4;
            font-weight: 500;
            background: var(--bs-tertiary-bg);
            border: 1px solid var(--bs-border-color);
            color: var(--bs-body-color);
        }

        .tkd-kd-badge {
            position: absolute;
            top: 0.875rem;
            left: 0.875rem;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            min-height: 1.25rem;
            padding: 0.125rem 0.625rem;
            border: 0.0625rem solid rgba(255, 255, 255, 0.58);
            border-radius: 0.875rem;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 0.625rem;
            font-weight: 700;
            line-height: 1;
            letter-spacing: 0.04em;
            backdrop-filter: blur(0.375rem);
            box-shadow: 0 0.25rem 0.875rem rgba(0, 0, 0, 0.16);
        }

        .tkd-kd-content {
            position: relative;
            z-index: 2;
            color: #fff;
            text-shadow: 0 0.0625rem 0.25rem rgba(0, 0, 0, 0.5);
        }

        .tkd-kd-title {
            max-width: 100%;
            color: #fff;
            font-size: 1rem;
            line-height: 1.25;
            letter-spacing: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .tkd-kd-tags {
            max-height: 3.375rem;
            overflow: hidden;
        }

        .tkd-kd-tag {
            display: inline-flex;
            align-items: center;
            max-width: 100%;
            min-height: 1.0625rem;
            padding: 0.0625rem 0.5625rem;
            border: 0.0625rem solid rgba(255, 255, 255, 0.7);
            border-radius: 0.875rem;
            background: transparent;
            color: #fff;
            font-size: 0.625rem;
            line-height: 1.3;
            white-space: nowrap;
            text-shadow: none;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .tkd-kd-slider {
            --tkd-kd-arrow-surface: var(--tkd-surface, #fff);
        }

        [data-bs-theme="dark"] .tkd-kd-slider {
            --tkd-kd-arrow-surface: var(--tkd-surface, #191f25);
        }

        .tkd-kd-arrow {
            position: absolute;
            right: -0.875rem;
            bottom: -0.625rem;
            z-index: 5;
            width: 3.75rem;
            height: 3.625rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-top-left-radius: 1.125rem;
            background: var(--tkd-kd-arrow-surface);
        }

        .tkd-kd-arrow::before,
        .tkd-kd-arrow::after {
            position: absolute;
            content: "";
            width: 1.375rem;
            height: 1.375rem;
            background: transparent;
            border-bottom-right-radius: 1.125rem;
            box-shadow: 0.4375rem 0.4375rem var(--tkd-kd-arrow-surface);
        }

        .bottom-right {
            border-top: 1px solid var(--tkd-border);
        }

        .tkd-kd-arrow::before {
            left: -1.375rem;
            bottom: 0.625rem;
        }

        .tkd-kd-arrow::after {
            right: 0.875rem;
            top: -1.375rem;
        }

        .tkd-kd-arrow svg {
            position: relative;
            z-index: 1;
            width: 2.75rem;
            height: 2.5rem;
            margin-top: -0.7rem;
            margin-left: -0.8rem;
            filter: drop-shadow(0 0.125rem 0.25rem rgba(0, 0, 0, 0.18));
        }

        .tkd-kd-card:hover .tkd-kd-arrow svg {
            transform: translateX(0.125rem);
            transition: transform 0.2s ease;
        }

        /* Carousel indicators – modern dots */
        .tkd-kd-slider .carousel-indicators {
            position: relative;
            bottom: auto;
            margin: 0.75rem 0 0;
            gap: 0.5rem;
        }

        .tkd-kd-slider .carousel-indicators button {
            width: 1.625rem;
            height: 0.25rem;
            border: 0;
            border-radius: 2rem;
            background-color: rgba(218, 26, 26, 0.26);
            opacity: 1;
            transition: all 0.2s ease;
        }

        .tkd-kd-slider .carousel-indicators button.active {
            width: 2.5rem;
            background-color: #DA1A1A;
        }

        /* Carousel controls – consistent with meeting slider */
        .tkd-kd-slider .carousel-control-prev,
        .tkd-kd-slider .carousel-control-next {
            width: 2.125rem;
            height: 2.125rem;
            border: 0.0625rem solid rgba(255, 255, 255, 0.62);
            border-radius: 50%;
            background: rgba(17, 24, 39, 0.6);
            backdrop-filter: blur(0.375rem);
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.72;
            transition: opacity 0.2s ease, background-color 0.2s ease;
        }

        .tkd-kd-slider .carousel-control-prev {
            left: 0.75rem;
        }

        .tkd-kd-slider .carousel-control-next {
            right: 1.5rem;
        }

        .tkd-kd-slider .carousel-control-prev:hover,
        .tkd-kd-slider .carousel-control-next:hover {
            opacity: 1;
            background: rgba(218, 26, 26, 0.86);
        }

        .tkd-kd-slider .carousel-control-prev-icon,
        .tkd-kd-slider .carousel-control-next-icon {
            width: 1rem;
            height: 1rem;
            background-size: 100% 100%;
        }

        /* Dark mode adjustments */
        [data-bs-theme="dark"] .tkd-kd-slider .carousel-indicators button {
            background-color: rgba(255, 255, 255, 0.3);
        }

        [data-bs-theme="dark"] .tkd-kd-slider .carousel-indicators button.active {
            background-color: #DA1A1A;
        }

        [data-bs-theme="dark"] .tkd-kd-slider .carousel-control-prev,
        [data-bs-theme="dark"] .tkd-kd-slider .carousel-control-next {
            background: rgba(0, 0, 0, 0.7);
        }

        [data-bs-theme="dark"] .tkd-kd-card {
            box-shadow: 0 0.75rem 1.625rem rgba(0, 0, 0, 0.34);
        }

        /* Responsive */
        @media (max-width: 576px) {
            .tkd-kd-slider .carousel-inner {
                padding-right: 0.625rem;
            }

            .tkd-kd-card {
                min-height: 12rem;
                padding: 1rem 3.625rem 1rem 1rem;
            }

            .tkd-kd-title {
                font-size: 0.875rem;
            }

            .tkd-kd-tag {
                font-size: 0.6rem;
            }

            .tkd-kd-arrow {
                right: -0.625rem;
                bottom: -0.5rem;
                width: 3rem;
                height: 2.875rem;
                border-top-left-radius: 0.875rem;
            }

            .tkd-kd-arrow::before,
            .tkd-kd-arrow::after {
                width: 1rem;
                height: 1rem;
                border-bottom-right-radius: 0.875rem;
                box-shadow: 0.375rem 0.375rem var(--tkd-kd-arrow-surface);
            }

            .tkd-kd-arrow::before {
                left: -1rem;
                bottom: 0.5rem;
            }

            .tkd-kd-arrow::after {
                right: 0.625rem;
                top: -1rem;
            }

            .tkd-kd-arrow svg {
                width: 2.125rem;
                height: 2rem;
            }

            .tkd-kd-slider .carousel-control-prev,
            .tkd-kd-slider .carousel-control-next {
                width: 1.875rem;
                height: 1.875rem;
            }

            .tkd-kd-slider .carousel-control-next {
                right: 1.125rem;
            }
        }

        /* Document Section */


        /* ticket meeting   */
        .tkd-meeting-slider .carousel-inner {
            border-radius: 0.5rem;
        }

        .tkd-meeting-card {
            background: var(--tkd-surface, #fff);
            border: 1px solid var(--tkd-border, #e5e5e5);
            border-radius: 0.5rem;
            padding: 1rem;
            transition: box-shadow 0.2s;
        }

        .tkd-meeting-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .tkd-meeting-header h5 {
            font-size: 0.75rem;
            /* 14px */
            line-height: 1.3;
        }

        .tkd-meeting-time span,
        .tkd-meeting-attendees .b7-text {
            font-size: 0.75rem;
            /* 12px */
        }

        .tkd-meeting-time svg {
            flex-shrink: 0;
        }

        .tkd-meeting-attendees img {
            object-fit: cover;
        }

        /* Carousel controls – simple, subtle */
        .tkd-meeting-slider .carousel-control-prev,
        .tkd-meeting-slider .carousel-control-next {
            width: 1rem;
            height: 1rem;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.6;
            transition: opacity 0.2s;
        }

        .tkd-meeting-slider .carousel-control-prev-icon,
        .tkd-meeting-slider .carousel-control-next-icon {
            width: .7rem;
            height: 7rem;
            background-size: 100% 100%;
        }

        .tkd-meeting-slider .carousel-control-prev:hover,
        .tkd-meeting-slider .carousel-control-next:hover {
            opacity: 1;
        }

        .tkd-meeting-slider .carousel-control-prev {
            left: -1rem;
        }

        .tkd-meeting-slider .carousel-control-next {
            right: -1rem;
        }

        /* Dark mode support */
        [data-bs-theme="dark"] .tkd-meeting-card {
            background: var(--tkd-surface);
            border-color: var(--tkd-border);
        }

        [data-bs-theme="dark"] .tkd-meeting-slider .carousel-control-prev,
        [data-bs-theme="dark"] .tkd-meeting-slider .carousel-control-next {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Responsive: adjust control positions on small screens */
        @media (max-width: 576px) {
            .tkd-meeting-slider .carousel-control-prev {
                left: -0.5rem;
            }

            .tkd-meeting-slider .carousel-control-next {
                right: -0.5rem;
            }
        }

        /* ticket meeting   */
        .tkd-body {
            display: grid;
            grid-template-columns: 1fr 300px;
            /* gap: 15px; */
            padding: 16px 20px 80px;
        }

        .tkd-main-card {
            background: var(--bs-body-bg);
            border: 1px solid var(--tkd-border);
            border-radius: 8px;
            border-top-right-radius: unset;
            border-bottom-right-radius: unset;
            border-bottom: unset;
            overflow: hidden;
        }

        .tkd-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .meta-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            gap: 8px;
            font-size: 12.5px;
            margin-bottom: 9px;
        }

        .meta-row:last-child {
            margin-bottom: 0;
        }

        .av-24 {
            width: 24px;
            height: 24px;
            font-size: 12px;
        }

        .av-16 {
            width: 16px;
            height: 16px;
            font-weight: 400;
            font-size: 6px;
        }

        .av-teal {
            background: linear-gradient(135deg, #14b8a6, #0891b2);
        }

        .av-indigo {
            background: linear-gradient(135deg, #6366f1, #4338ca);
        }

        .tkd-conversation {
            background: #faeeef70;
            padding: 16px;
        }

        .tkd-reply {
            padding: 12px 16px 14px;
            background: #faeeef70;
        }

        .tkd-reply .note-minibar i {
            font-size: 18px;
        }

        #tkd-file-input {
            display: none;
        }

        /* Chips */
        .tkd-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 8px;
        }

        .tkd-chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #f0f4ff;
            border: 1px solid #c7d2fe;
            border-radius: 20px;
            padding: 2px 10px 2px 8px;
            font-size: 11px;
            color: #3730a3 !important;
            max-width: 180px;
        }

        .availabilitySuccess {
            color: #15803d !important;
        }

        /* .select2-dropdown.select2-dropdown--below{
                width: 140px !important;
            } */

        #tkt-problem_type .amg-table-pagination-dropdown+.select2-container--custom .select2-selection--single {
            width: unset !important;
        }

        .tkd-chip span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .tkd-chip button {
            background: none;
            border: none;
            color: #6366f1 !important;
            padding: 0;
            font-size: 14px;
            line-height: 1;
        }

        [data-bs-theme="dark"] .tkd-chip {
            background: #1e1b4b;
            border-color: #4338ca;
            color: #a5b4fc !important;
        }

        /* ── RIGHT SIDEBAR ───────────────────────────────────────── */

        #updateTicket.panel {
            margin: 0;
            background: var(--bs-body-bg);
            box-shadow: none;
        }

        .tkd-panel {
            background: var(--bs-body-bg);
            border: 1px solid #E5E5E5;
            border-radius: 7px;
            overflow: hidden;
        }


        .tkd-panel-head {
            background: #F4F6FF;
            cursor: pointer;
            border-bottom: 1px solid #E5E5E5;
        }

        .tkd-panel-head .chevron {
            width: 20px;
            height: 20px;
            /* color: var(--bs-secondary-color) !important; */
            transition: transform .2s;
        }


        /* Timeline */
        .tkd-tl {
            max-height: 70px;
        }

        .tkd-tl::-webkit-scrollbar {
            width: 3px;
        }

        .tkd-tl::-webkit-scrollbar-thumb {
            background: var(--bs-border-color);
            border-radius: 4px;
        }

        .tkd-tl-item {
            padding-bottom: 12px;
        }

        .tkd-tl-item:last-child {
            padding-bottom: 0;
        }

        .tkd-tl-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: 3px;
            top: 7px;
            bottom: 8px;
            width: 1px;
            background: #EBEBEB;
            z-index: 0;
        }

        .amg-link-sm i {
            font-size: 0.75rem !important;
        }

        .tkd-tl-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #EBEBEB;
            flex-shrink: 0;
            margin-top: 1px;
            position: relative;
            z-index: 1;
        }

        .people-row+.people-row {
            margin-top: 8px;
        }

        /* comment summernote  start ===================================== */

        .tkd-body .note-editor.note-frame {
            border: 1.5px solid #E5E5E5 !important;
            border-radius: 5px;
            box-shadow: none !important;
            overflow: hidden;
        }

        /* .tkd-body .note-editor.note-frame .note-toolbar {
                display: none !important;
            } */

        .tkd-body .note-editor.note-frame .note-editing-area .note-editable {
            padding: 12px 14px 44px;
            font-size: 14px;
            color: #111827;
            background: #fff;
            caret-color: #2563eb;
        }

        .tkd-body .note-editor.note-frame .note-editing-area .note-editable[contenteditable="true"]:empty:before {
            color: #9ca3af;
            font-weight: 500;
        }

        .tkd-body .note-editor.note-frame .note-statusbar {
            display: none !important;
        }

        /* ── Custom bottom toolbar ── */
        .tkd-body .custom-toolbar {
            display: flex;
            align-items: center;
            gap: 0px;
            padding: 3px 8px;
            border-top: 1.5px solid #e5e7eb;
            background: #fafafa;
            border-radius: 0 0 9px 9px;
            margin-top: -2px;
        }

        .tkd-body .custom-toolbar button {
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px 6px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            color: #6b7280;
            font-size: 13px;
            font-weight: 600;
            transition: background 0.15s, color 0.15s;
        }

        .tkd-body .custom-toolbar button:hover {
            background: #f3f4f6;
            color: #111827;
        }


        .tkd-body .note-popover {
            display: none !important;
        }

        .tkd-body .note-minibar {
            display: flex !important;
            gap: 4px !important;
            padding: 0px !important;
            border: none !important;
            border-radius: 8px !important;
            margin-bottom: 6px !important;
            box-shadow: none !important;
            flex-wrap: wrap !important;
            border-color: #d1d5db !important;
        }

        .tkd-body .note-minibar button {
            background: #fff !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 3px !important;
            padding: 3px 7px !important;
            font-size: 13px !important;
            cursor: pointer !important;
            color: #374151 !important;
            /* your custom overrides below */
        }

        .tkd-body .note-minibar button:hover {
            background: #f3f4f6 !important;
            color: #111827 !important;
        }

        .tkd-body .note-editor.note-airframe .note-placeholder,
        .tkd-body .note-editor.note-frame .note-placeholder {
            color: #000000;
            padding: 8px 16px !important;
        }


        .tkd-body .note-editor.note-airframe .note-editing-area .note-editable,
        .tkd-body .note-editor.note-frame .note-editing-area .note-editable {
            word-wrap: break-word;
            overflow: auto;
            padding: 10px;
        }

        .text-grey {
            color: #fff !important;
            background-color: #70727E !important;
            font-size: .6rem !important;
        }


        .tkd-body #reply-summernote-wrapper .note-editor.note-frame {
            border: 1px solid #E5E5E5;
            border-radius: 8px;
            box-shadow: none !important;
        }

        .tkd-body #reply-summernote-wrapper .note-editing-area .note-editable {
            min-height: 150px;
            padding: 10px 14px;
            font-size: 12.5px;
            color: var(--bs-body-color);
            background: var(--bs-body-bg);
        }

        .tkd-body #reply-summernote-wrapper .custom-toolbar {
            display: flex;
            align-items: center;
            gap: 0px;
            padding: 14px 20px;
            border-top: 1.5px solid #e5e7eb;
            background: #fafafa;
            border-radius: 0 0 9px 9px;
            margin-top: -2px;
        }

        .tkd-body #reply-summernote-wrapper .note-editor.note-frame .note-placeholder {
            color: #000000;
            padding: 16px 20px !important;
        }

        /* reply summernote end =======================================================*/

        .tkd-body #summernote-wrapper,
        .tkd-body #reply-summernote-wrapper {
            visibility: hidden;
            min-height: 80px;
        }

        .tkd-body #summernote-wrapper.sn-ready,
        .tkd-body #reply-summernote-wrapper.sn-ready {
            visibility: visible;
        }

        #tkt-problem_type {
            width: 50%;
        }

        .s2-icon {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        .select2-container .user-list-avatar {
            width: 30px;
            height: 30px;
            flex: 0 0 30px;
            border-radius: 999px;
            object-fit: cover;
            background: #eef2f7;
            border: 1px solid rgba(148, 163, 184, .16);
        }

        .select2-container .user-list-avatar-fallback {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 600;
            color: #41516b;
        }

        .select2-container .active-user,
        .select2-container .inactive-user {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-left: 5px;
            vertical-align: middle;
        }

        .select2-container .active-user {
            background-color: #5cb85c;
        }

        .select2-container .inactive-user {
            background-color: #d9534f;
        }

        @media (max-width: 1199px) {
            .tkd-body {
                grid-template-columns: 1fr 260px;
                padding: 12px 14px 80px;
            }
        }

        @media (max-width: 991px) {
            .tkd-body {
                grid-template-columns: 1fr;
                padding: 12px 12px 90px;
            }

            .tkd-meta {
                grid-template-columns: 1fr;
            }

            .tkd-resolve-desktop {
                display: none !important;
            }
        }

        @media (max-width: 575px) {
            .tkd-fav-text {
                display: none;
            }

            .meta-row {
                grid-template-columns: 90px 1fr;
                font-size: 12px;
            }

            .tkd-body {
                padding: 8px 8px 90px;
                gap: 10px;
            }
        }

        /* Figma aligned ticket details overrides */

        .tkd-page-header svg {
            width: 18px;
            height: 18px;
            color: #8D8D8D;
        }

        .tkd-page-header .h3-text {
            color: #444444;
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .tkd-action-bar {
            min-height: 54px;
            padding: 12px 20px;
            background: #F5F7FB;
            border-bottom: 1px solid #E7EBF2;
        }

        .tkd-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            height: 30px;
            padding: 0 12px;
            border: 1px solid #E1E5EC;
            border-radius: 5px;
            background: #FFFFFF;
            color: #8A8F98;
            font-size: 0.75rem;
            font-weight: 400;
            line-height: 1;
            transform: translateY(0);
            opacity: 1;
            transition: opacity .18s ease, transform .18s ease, background .15s ease, color .15s ease, border-color .15s ease;
        }

        .tkd-action-btn.tkd-action-animating-in {
            animation: tkdActionIn .18s ease both;
        }

        .tkd-action-btn.tkd-action-animating-out {
            opacity: 0;
            transform: translateY(-4px);
            pointer-events: none;
        }

        .tkd-action-btn.hidden {
            display: none;
        }

        .tkd-action-btn.tkd-action-overflow-hidden {
            display: none !important;
        }

        .tkd-action-more {
            width: 30px;
            padding: 0;
            justify-content: center;
        }

        .tkd-action-bar.is-expanded {
            position: relative;
            padding-right: 60px;
            transition: all 1s ease-in-out !important;

        }

        .tkd-action-bar.is-expanded .tkd-action-more {
            /* position: absolute; */
            top: 12px;
            right: 20px;
        }

        @keyframes tkdActionIn {
            from {
                opacity: 0;
                transform: translateY(-4px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .tkd-action-btn svg,
        .tkd-action-btn i {
            font-size: 0.75rem;
            color: #A4A9B1;
        }

        .tkd-body {
            --tkd-page: #FFFFFF;
            --tkd-surface: #FFFFFF;
            --tkd-soft: #F4F6FF;
            --tkd-softer: #F7F8FB;
            --tkd-border: #E5E5E5;
            --tkd-text: #111C2D;
            --tkd-muted: #6B7280;
            --tkd-toolbar: #EDF2FA;
            --tkd-danger: #ed1117;
            /* gap: 24px;
                padding: 16px 20px 28px; */
            background: var(--tkd-page);
        }

        .tkd-status-strip {
            min-height: 58px;
            background: var(--tkd-surface);
            border-color: var(--tkd-border) !important;
        }

        .tkd-body .amg-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            min-height: 33px;
            padding: 0 12px;
            border: 1px solid var(--tkd-border);
            border-radius: 4px;
            background: var(--tkd-surface);
            color: var(--tkd-text);
            font-size: .75rem;
            font-weight: 500;
            line-height: 1;
        }

        .tkd-body .amg-badge svg {
            width: 18px;
            height: 18px;
        }

        .tkd-body .badge-high {
            color: #6B4F4F;
            background: #FFF7F5;
            font-weight: 500;
        }

        .tkd-meta {
            grid-template-columns: 1fr 0.78fr;
            min-height: 210px;
            border-bottom: 1px solid var(--tkd-border);
        }

        .meta-row svg path,
        .tkd-body .custom-toolbar svg path {
            fill: currentColor;
        }

        .tkd-assignment-card {
            width: 76% !important;
            min-width: 19.063rem;
            margin-left: auto;
            background: var(--tkd-surface) !important;
            border-color: var(--tkd-border) !important;
        }

        .tkd-conversation {
            position: relative;
            background: var(--tkd-surface);
            border-bottom: 1px solid var(--tkd-border);
        }

        .tkd-reply {
            padding: 18px 24px 22px;
            background: var(--tkd-surface);
        }

        .tkd-section-title,
        .tkd-panel-title {
            color: var(--tkd-text) !important;
            font-size: .8rem !important;
        }

        .tkd-panel-title {
            color: white !important;
        }

        .tkd-icon-plain {
            width: 26px;
            height: 26px;
            border: 0;
            background: transparent;
            color: var(--tkd-muted);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .tkd-conversation-list {
            overflow-y: auto;
            padding: 9px 5px 2px 6px;
            scrollbar-color: #fff3f7;
            max-height: 250px;
        }

       
        [data-bs-theme="dark"] .tkd-conversation-card {
            padding: .7rem;
            border: 1px solid var(--tkd-border);
            border-radius: 6px;
        }

        .tkd-conversation-card:last-child {
            margin-bottom: 0;
        }

        .tkd-conversation-card>div {
            width: 100%;
        }

        .tkd-comment-body {
            overflow: hidden;
            /* max-height: 1000px; */
            opacity: 1;
            transition: max-height .22s ease, opacity .18s ease, margin .18s ease, padding .18s ease;
        }

        .tkd-conversation-card.is-collapsed .tkd-comment-body {
            display: -webkit-box !important;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 3;
            overflow: hidden;
            text-overflow: ellipsis;
            max-height: 60px;
        }

        .tkd-comment-toggle i {
            pointer-events: none;
        }

        .tkd-conversation-list::-webkit-scrollbar {
            width: 5px;
        }

        .tkd-conversation-list::-webkit-scrollbar-track {
            background: transparent;
        }

        .tkd-conversation-list::-webkit-scrollbar-thumb {
            background: #D6DAE1;
            border-radius: 10px;
        }

        .tkd-message-body {
            width: min(760px, 100%);
            margin-bottom: 0;
            line-height: 1.5;
            color: var(--tkd-muted) !important;
        }

        .tkd-message-label {
            display: block;
            color: var(--tkd-text);
        }

        .tkd-message-label span {
            color: var(--tkd-danger);
        }

        .primary-red,
        .availabilityError {
            color: var(--tkd-danger) !important;
        }

        .tkd-internal-note input {
            width: 14px;
            height: 14px;
            accent-color: var(--tkd-danger);
            ;
        }

        .tkd-panel {
            background: var(--tkd-surface);
            border-color: var(--tkd-border);
        }

        .tkd-panel-head {
            background: var(--tkd-soft);
            border-bottom-color: var(--tkd-border) !important;
            text-decoration: none;
        }

        .tkd-tl-item:not(:last-child)::before,
        .tkd-tl-dot {
            background: var(--tkd-border);
        }

        .tkd-body .note-editor.note-frame {
            border-color: var(--tkd-border) !important;
            background: var(--tkd-surface);
        }

        .tkd-body .note-editor.note-frame .note-editing-area .note-editable {
            color: var(--tkd-text);
            background: var(--tkd-surface);
        }

        .tkd-body .custom-toolbar {
            border-color: var(--tkd-border);
            background: var(--tkd-softer);
        }

        .tkd-body .custom-toolbar button {
            color: var(--tkd-muted);
        }

        .tkd-body .note-editor.note-airframe .note-placeholder,
        .tkd-body .note-editor.note-frame .note-placeholder {
            color: var(--tkd-muted);
        }

        .tkd-body #reply-summernote-wrapper .note-editor.note-frame {
            /* display: flex !important;
                    flex-direction: column; */
            border-color: var(--tkd-border) !important;
            border-radius: 5px;
        }

        .tkd-body #reply-summernote-wrapper .note-editing-area .note-editable {
            height: 142px !important;
            min-height: 142px;
            color: var(--tkd-text);
            background: var(--tkd-surface);
        }

        .tkd-body #reply-summernote-wrapper .custom-toolbar {
            order: -1;
            padding: 7px 12px;
            border-top: 0;
            border-bottom: 1px solid var(--tkd-border);
            background: var(--tkd-toolbar);
            border-radius: 5px 5px 0 0;
            margin-top: 0;
        }

        .tkd-body #reply-summernote-wrapper .note-editor.note-frame .note-placeholder {
            color: var(--tkd-muted);
            /* padding: 52px 14px 14px !important; */
        }

        #updateTicket {
            background: var(--tkd-surface);
            border-color: var(--tkd-border) !important;
        }

        #updateTicket .select2-container--default .select2-selection--single {
            border-color: var(--tkd-border) !important;
            background: var(--tkd-surface) !important;
        }

        #updateTicket .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--tkd-text) !important;
        }

        .select2-container--default .select2-results__option {
            color: var(--tkd-text);
        }

        [data-bs-theme="dark"] .tkd-page-header {
            background: #2B211C !important;
            border-bottom-color: var(--dark-border, #2a2a2d);
        }

        [data-bs-theme="dark"] .tkd-page-header .h3-text,
        [data-bs-theme="dark"] .tkd-page-header svg {
            color: var(--text-primary, #ffffff);
        }

        [data-bs-theme="dark"] .tkd-action-bar {
            background: var(--dark-primary, #191919);
            border-bottom-color: var(--dark-border, #2a2a2d);
        }

        [data-bs-theme="dark"] .tkd-action-btn {
            background: var(--dark-secondary, #2a2a2a);
            border-color: var(--dark-border, #2a2a2d);
            color: var(--text-muted, #b7b7b7);
        }

        [data-bs-theme="dark"] .tkd-body {
            --tkd-page: var(--dark-primary, #191919);
            --tkd-surface: var(--dark-secondary, #2a2a2a);
            --tkd-soft: #202434;
            --tkd-softer: #1F1F1F;
            --tkd-border: var(--dark-border, #2a2a2d);
            --tkd-text: var(--text-primary, #ffffff);
            --tkd-muted: var(--text-muted, #b7b7b7);
            --tkd-toolbar: #242A36;
        }

        [data-bs-theme="dark"] .tkd-body .amg-badge,
        [data-bs-theme="dark"] .tkd-assignment-card,
        [data-bs-theme="dark"] #updateTicket {
            background: var(--tkd-surface) !important;
            border-color: var(--tkd-border) !important;
            color: var(--tkd-text) !important;
        }

        [data-bs-theme="dark"] .tkd-body .badge-high {
            background: #2D201F !important;
            color: #FFD0CB !important;
        }


        [data-bs-theme="dark"] .tkd-body .opacity-70,
        [data-bs-theme="dark"] .tkd-body .opacity-60 {
            color: var(--tkd-muted) !important;
            opacity: 1 !important;
        }

        [data-bs-theme="dark"] .tkd-body .border-bottom,
        [data-bs-theme="dark"] .tkd-body .border,
        [data-bs-theme="dark"] .tkd-body .border-end {
            border-color: var(--tkd-border) !important;
        }

        [data-bs-theme="dark"] .tkd-body .custom-toolbar button,
        [data-bs-theme="dark"] .tkd-body .note-minibar button {
            color: var(--tkd-muted) !important;
            background: transparent !important;
            border-color: var(--tkd-border) !important;
        }

        [data-bs-theme="dark"] .tkd-body .note-editor.note-frame .note-placeholder,
        [data-bs-theme="dark"] .tkd-body .note-editor.note-frame .note-editable,
        [data-bs-theme="dark"] #updateTicket .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--tkd-text) !important;
        }

        [data-bs-theme="dark"] .tkd-body #reply-summernote-wrapper .note-editor.note-frame {
            border-color: var(--tkd-muted) !important;
        }

        [data-bs-theme="dark"] .tkd-body .note-editor.note-frame {
            border-color: var(--tkd-muted) !important;
        }

        @media (max-width: 1199px) {
            .tkd-body {
                grid-template-columns: 1fr 270px;
                gap: 18px;
                padding: 14px 14px 28px;
            }
        }

        @media (max-width: 991px) {

            .tkd-body,
            .tkd-meta {
                grid-template-columns: 1fr;
            }

            .tkd-assignment-card {
                width: 100% !important;
                min-width: 0;
            }
        }

        @media (max-width: 575px) {
            .tkd-page-header {
                padding-left: 14px;
            }

            .tkd-page-header .h3-text {
                font-size: 14px;
            }

            .tkd-action-bar {
                padding: 10px 12px;
            }

            .tkd-body {
                padding: 10px 10px 24px;
            }

            .tkd-reply {
                padding: 16px;
            }
        }


        .tkd-status-strip {
            min-height: 58px;
            padding: 14px 28px !important;
            gap: 10px !important;
        }

        #statusInfo {
            order: 1;
        }

        #expireInfo {
            order: 2;
        }

        #tatInfo {
            order: 3;
        }

        #priorityInfo {
            order: 4;
        }

        #workaroundInfo {
            order: 5;
        }

        #responseInfo {
            order: 6;
        }

        .tkd-status-strip .amg-badge.hide {
            display: none !important;
        }

        .tkd-body .amg-badge {
            min-height: 32px;
            border-radius: 3px;
            background: #fff;
            font-size: 12px;
            font-weight: 500;
        }

        .hide {
            display: none !important;
        }

        .tkd-body .badge-high {
            color: #111;
            background: #fff;
        }

        .tkd-ticket-summary {
            padding: 4px 28px 4px;
            border-bottom: 1px solid #eeeeee;
        }

        .tkd-summary-expand {
            position: absolute;
            top: 22px;
            right: 22px;
            width: 24px;
            height: 24px;
            border: 0;
            background: transparent;
            color: #7b7f86;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .tkd-ticket-title {
            max-width: calc(100% - 42px);
            color: #050505;
            font-size: 1rem !important;
            font-weight: 600;
            line-height: 1.35;
        }

        .tkd-ticket-content {
            max-width: 880px;
            color: #565d66;
            font-size: 11px;
            line-height: 1.45;
            opacity: 1;
            overflow: visible;
            transition: opacity .18s ease;
        }

        .tkd-ticket-summary.is-collapsed {
            padding-bottom: 1px;
        }

        .tkd-ticket-summary.is-collapsed .tkd-ticket-content {
            display: none;
            opacity: 0;
            margin-top: 0 !important;
        }

        .tkd-ticket-content p,
        .tkd-ticket-content div {
            margin-bottom: 0.35rem;
        }

        .tkd-mail-frame {
            width: 100%;
            min-height: 180px;
            border: 1px solid #e5e5e5;
            border-radius: 4px;
        }

        .label-merged,
        .label-merged-primary {
            display: inline-flex;
            margin-left: 8px;
            padding: 2px 6px;
            border-radius: 3px;
            background: #fff4f4;
            color: #cc0000;
            font-size: 11px;
            font-weight: 600;
        }

        [data-bs-theme="dark"] .mar-rgt {
            color: rgba(255, 255, 255, .85) !important;
        }

        [data-bs-theme="dark"] .attach-item-cntnt {
            background-color: #141414 !important;
        }

        [data-bs-theme="dark"] .attach-item-cntnt {
            color: var(--text-primary) !important;
        }

        .tkd-ticket-summary .main_attachments>div {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
            margin-top: 5px;
        }

        .tkd-ticket-summary .main_attachments .text-bold {
            color: #000;
            font-size: 11px;
            font-weight: 500 !important;
        }

        .tkd-ticket-summary .main_attachments .attach-item {
            width: min(100%, 320px);
            min-height: 36px;
            border: 1px solid #ececec;
            border-radius: 6px;
            background: #f1f1f1;
            background-size: cover;
            background-position: center;
            padding: 0;
            overflow: hidden;
            width: fit-content !important;
        }

        .tkd-ticket-summary .main_attachments .attach-item-cntnt {
            min-height: 36px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 8px;
            padding: 0 10px;
            margin-bottom: 0;
        }

        .tkd-ticket-summary .main_attachments .attach-name {
            order: 2;
            min-width: 0;
            flex: 1;
            font-size: 11px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .tkd-ticket-summary .main_attachments .icons {
            order: 1;
            display: flex;
            gap: 6px;
            flex-shrink: 0;
        }

        .people-section {
            margin-bottom: 3rem;
        }

        .tkd-ticket-summary .main_attachments .icons a,
        .tkd-ticket-summary .main_attachments .icons button {
            min-height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            border: 0;
            border-radius: 4px;
            background: transparent;
            color: var(--tkd-danger);
            padding: 0;
            font-size: 10px;
            font-weight: 600;
            line-height: 1;
            text-decoration: none;
        }

        .tkd-ticket-summary .main_attachments .icons button {
            cursor: pointer;
        }

        .tkd-detail-block {
            padding: 4px 28px 4px;
            border-bottom: 1px solid #eeeeee;
        }

        .tkd-owner-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 12px;
        }

        .tkd-owner-chip {
            min-height: 34px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 0 10px;
            border: 1px solid #e9e9e9;
            border-radius: 2px;
            /* background: #fff; */
            color: #111;
            font-size: 11px;
        }

        .tkd-owner-chip strong {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            /* color: #4f555d; */
            font-weight: 500;
        }

        .tkd-detail-grid {
            width: min(560px, 100%);
            /* display: grid; */
            gap: 7px;
        }

        .tkd-detail-row {
            display: grid;
            grid-template-columns: 200px minmax(0, 1fr);
            align-items: center;
            gap: 12px;
            color: #444;
            font-size: 12px;
            /* line-height: 1.25; */
            padding: .3rem 0;
        }

        .tkd-detail-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #111;
            font-weight: 500;
        }

        .tkd-detail-label i {
            width: 14px;
            color: #8b8f96;
            text-align: center;
        }

        .tkd-detail-value {
            min-width: 0;
            align-items: center;
            flex-wrap: wrap;
            color: #4f555d;
            word-break: break-word;
        }

        .tkd-detail-value a {
            color: var(--tkd-danger);
            text-decoration: none;
        }

        .tkd-detail-value a:hover {
            text-decoration: underline;
        }

        .tkd-detail-value>i.bi {
            color: #8b8f96;
            font-size: 12px;
        }

        .tkd-detail-email {
            color: #777;
            font-size: 11px;
            overflow-wrap: anywhere;
        }

        .tkd-copy-email-btn {
            width: 22px;
            height: 22px;
            min-width: 22px;
            padding: 0;
            border: 0;
            border-radius: 2px;
            background: transparent;
            color: #8b8f96;
            line-height: 1;
        }

        .tkd-copy-email-btn:hover {
            background: #f5f5f5;
            color: var(--tkd-danger);
        }

        .tkd-detail-tags .badge {
            border-radius: 2px;
            background: #f1f3f5;
            color: #555;
            font-size: 11px;
            font-weight: 500;
            padding: 3px 6px;
        }

        .tkd-detail-tags .badge:not(:first-child) {
            margin-left: 2px;
        }

        .tkd-ticket-type-form {
            display: contents;
        }

        .tkd-ticket-type-form .customFieldset,
        .tkd-ticket-type-form .fieldsets {
            grid-column: 1 / -1;
            margin-top: 6px;
            /* border: 1px solid var(--app-border) !important; */
            border-radius: 8px;

        }

        /* .tkd-ticket-type-form .select2-container,
            .tkd-ticket-type-form .ticketType {
                width: 180px !important;
                max-width: 100%;
            } */

        .tkd-ticket-type-form .select2-selection--single,
        .tkd-ticket-type-form .form-control {
            height: 28px !important;
            min-height: 28px !important;
            border: 1px solid #e5e5e5 !important;
            border-radius: 2px !important;
            /* background: #fff !important; */
            font-size: 12px !important;
        }

        .tkd-ticket-type-form .select2-selection__rendered {
            line-height: 26px !important;
        }

        .tkd-ticket-type-form .btn_ticket_type_save {
            grid-column: 2;
            width: max-content;
            min-height: 28px;
            padding: 4px 12px;
            border-radius: 4px;
            background: var(--tkd-danger);
            border-color: var(--tkd-danger);
            font-size: 11px;
            font-weight: 600;
        }

        .tkd-management-row {
            display: flex;
            flex-wrap: wrap;
            gap: 26px;
            margin-top: 16px;
        }

        .tkd-management-row label {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin: 0;
            color: #777;
            font-size: 11px;
            font-weight: 400;
        }

        .tkd-management-row input,
        .tkd-internal-note input {
            accent-color: #ff2d32;
        }

        #tkt-problem_type .select2-selection__clear {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .tkd-conversation {
            padding: 16px 28px 14px;
            background-color: #FFFFFF;
        }

        .attachment-content{
            background-color: #f1f1f1 !important;
            padding: .5rem;
            margin-top: .3rem;
            width: fit-content;
            border-radius: 8px;
        }

        [data-bs-theme="dark"] .tkd-conversation {
            background-color: var(--dark-primary, #191919) !important;
        }


        .tkd-conversation-card {
            margin-bottom: 10px;
            padding: 0;
            border: 0;
            border-radius: 0;
            background: transparent;
            box-shadow: none;
        }

        .tkd-conversation-card .av-24 {
            width: 24px;
            height: 24px;
            font-size: 9px;
        }

        .tkd-conversation-card .border-end {
            border-color: #d8d8d8 !important;
        }

        .tkd-conversation-all-toggle i {
            font-size: 15px;
        }

        .tkd-comment-body {
            /* margin-left: 31px !important; */
            padding-left: 0 !important;
        }

        .tkd-message-body {
            width: min(840px, 100%);
            color: #42464d !important;
            font-size: 12px;
            line-height: 1.45;
        }

        .tkd-reply {
            padding: 18px 28px 22px;
        }

        .tkd-body #reply-summernote-wrapper .note-editor.note-frame {
            border-radius: 2px;
        }

        .note-editor {
            width: 100% !important;
        }

        .tkd-body #reply-summernote-wrapper .custom-toolbar {
            min-height: 39px;
            padding: 6px 12px;
            background: #eef2f8;
            border-bottom: 1px solid #e4e8f0;
            border-radius: 2px 2px 0 0;
        }

        .tkd-body #reply-summernote-wrapper .note-editing-area .note-editable {
            height: 150px !important;
            min-height: 150px;
            padding: 12px;
            font-size: 12px;
        }

        .tkd-upload-label {
            display: block;
            color: #111;
            font-size: 11px;
            font-weight: 600;
        }


        .tkd-comment-uploader .amg-uploader__dropzone {
            cursor: pointer;
            /* min-height: 42px; */
            justify-content: center;
            border: 1px dashed #d8dde6;
            border-radius: 3px;
            background: #fff;
            padding: 8px 12px;
            text-align: center;
        }

        .tkd-comment-uploader .amg-uploader__message {
            justify-content: center;
            color: #5c636d;
            font-size: 11px;
            font-weight: 400;
        }

        .tkd-comment-uploader .amg-uploader__icon-wrap {
            width: auto;
            height: auto;
            min-width: 0;
            border-radius: 0;
            background: transparent;
            color: #848a93;
        }

        .tkd-upload-link {
            border: 0;
            background: transparent;
            color: #4d5560;
            font: inherit;
            padding: 0;
            text-decoration: none;
        }

        .tkd-upload-helper {
            margin-top: 6px;
            color: #777;
            font-size: 10px;
            font-style: italic;
        }

        .tkd-upload-preview .attach,
        .tkd-chips .attachs {
            border: 1px solid #edf0f5;
            border-radius: 6px;
            background: #f9fafb;
            padding: 8px 10px;
        }

        .tkd-upload-preview .bord-btm p,
        .tkd-chips .bord-btm p {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            max-width: calc(100% - 78px);
        }

        .tkd-upload-preview .bord-btm,
        .tkd-chips .bord-btm {
            border-bottom: 0 !important;
        }

        .tkd-upload-preview p,
        .tkd-chips p {
            margin: 0;
            color: #1f2937;
            font-size: 12px;
        }

        .tkd-upload-preview .remove-attach,
        .tkd-chips .remove-attach {
            color: #667085;
            font-size: 12px;
        }

        .tkd-upload-preview .progress,
        .tkd-chips .progress {
            height: 4px;
            margin: 6px 0 0;
        }

        .file-icon {
            color: #64748b;
            font-size: 18px;
            line-height: 1;
        }

        .file-icon.image-icon {
            color: #0284c7;
        }

        .file-icon.excel-icon,
        .file-icon.csv-icon {
            color: #15803d;
        }

        .file-icon.word-icon {
            color: #2563eb;
        }

        .file-icon.pdf-icon {
            color: #dc2626;
        }

        .file-icon.zip-icon,
        .file-icon.psd-icon {
            color: #7c3aed;
        }

        .file-icon.video-icon,
        .file-icon.ppt-icon {
            color: #ea580c;
        }

        .attachment-actions {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .attachment-actions .tri-view,
        .attachment-actions .tri-download,
        .main_attachments .tri-view,
        .main_attachments .tri-download {
            border: 0;
            background: transparent;
            color: var(--tkd-danger);
            cursor: pointer;
            line-height: 1;
            padding: 0;
        }

        .tkd-reply .amg-btn-primary,
        #updateTicket .amg-btn-primary {
            min-height: 30px;
            border-radius: 5px;
            background: var(--tkd-danger);
            border-color: var(--tkd-danger);
            font-size: 11px;
            font-weight: 600;
        }

        .tkd-reply .select2-container {
            width: 100% !important;
            max-width: 100%;
        }

        .tkd-reply .select2-selection--multiple {
            min-height: 32px;
            border-color: #e5e5e5 !important;
            border-radius: 3px !important;
        }

        .tkd-update-header {
            min-height: 40px;
            display: flex;
            align-items: center;
            background: #001f5b;
        }

        #updateTicket .tkd-update-header span {
            padding-left: 18px !important;
            font-size: 13px;
            font-weight: 500 !important;
        }

        #updateTicket .form-horizontal {
            gap: 5px !important;
            padding: 2px 2px !important;
        }

        #updateTicket,
        #updateTicket .d-block.b5-text,
        #updateTicket .b5-text {
            color: #5f6670 !important;
            font-size: 11px !important;
            font-weight: 500;
        }

        #updateTicket .b7-text {
            color: #6d747d !important;
            font-size: 10px !important;
        }

        .tkd-body #summernote-wrapper .note-editor.note-frame {
            border-radius: 3px;
        }

        .tkd-body #summernote-wrapper .note-editor.note-frame .note-toolbar {
            display: flex !important;
            flex-wrap: wrap;
            gap: 4px;
            padding: 4px 6px;
            /* background-color: #EFF2FA !important; */
        }

        .note-toolbar {
            background-color: #EFF2FA !important;
        }

        .btn-default {
            background-color: unset !important;
            color: #7F7F7F !important;
            border: unset !important;
        }

        .note-btn.btn.btn-default.btn-sm.active {
            background-color: #E5E5E5 !important;
            border-color: #7F7F7F !important;
        }

        .tkd-body #summernote-wrapper .note-editing-area .note-editable {
            height: 120px !important;
            min-height: 120px;
            padding: 8px 10px;
            font-size: 11px;
        }

        .tab-content>.tab-pane {
            display: none;
        }

        .tab-content>.active {
            display: block;
        }

        .tkd-body #summernote-wrapper .custom-toolbar {
            padding: 4px 8px;
            border-top: 0;
            background: #fff;
        }

        .tkd-panel-head {
            min-height: 40px;
            padding-top: 8px !important;
            padding-bottom: 8px !important;
        }

        .tkd-panel-title {
            font-size: 0.8rem !important;
            font-weight: 500;
        }

        .tkd-tl {
            max-height: 90px;
        }

        .attachment-header {
            color: var(--tkd-danger) !important;
        }

        .file-details .file-name {
            color: #001f5b !important;
            font-size: .75rem;
        }

        .file-details{
            display: flex;
            gap: .8rem;
            justify-content: center;
            align-items: center;
            flex-direction: row-reverse;
        }

        .task-card {
            margin: 0;
            padding: 16px 28px 18px;
            border-top: 1px solid var(--tkd-border);
            background: var(--tkd-surface);
            color: var(--tkd-text);
        }

        .task-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }

        .task-header-left {
            border: 0;
            background: transparent;
            color: inherit;
            padding: 0;
            text-align: left;
        }

        .task-title {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            color: var(--tkd-text);
            font-size: 13px;
            font-weight: 600;
        }

        .task-card .total-task {
            min-width: 22px;
            border-radius: 10px;
            background: #f1f3f5;
            color: #4f555d;
            font-size: 11px;
            font-weight: 600;
            line-height: 1;
            padding: 4px 7px;
        }

        .task-header-right,
        .task-header-actions {
            display: flex;
            align-items: center;
        }

        .task-header-right {
            gap: 14px;
        }

        .task-header-actions {
            gap: 6px;
        }

        .task-header-actions a,
        .task-actions .dtActbtn {
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--tkd-border) !important;
            border-radius: 4px !important;
            background: var(--tkd-surface) !important;
            color: #667085 !important;
            padding: 0 !important;
            text-decoration: none !important;
            line-height: 1 !important;
        }

        .task-header-actions a:hover,
        .task-actions .dtActbtn:hover {
            border-color: var(--tkd-danger) !important;
            color: var(--tkd-danger) !important;
        }

        .task-percentage {
            color: #4f555d;
            font-size: 12px;
            font-weight: 700;
        }

        .task-card>.progress-bar {
            width: 206px;
            height: 6px;
            margin: 8px 0 0 auto;
            border-radius: 4px;
            background: #ecfae9;
            box-shadow: none;
            overflow: hidden;
        }

        .task-card>.progress-bar .fill {
            height: 100%;
            background: #398d5f;
            transition: width 0.25s ease;
        }

        .task-section {
            margin-top: 12px;
        }

        .task-card.collapsed .task-section {
            display: none;
        }

        .task-card .expand-icon i {
            display: inline-block;
            color: #8b8f96;
            font-size: 14px;
            transition: transform 0.2s ease;
        }

        .task-card:not(.collapsed) .expand-icon i {
            transform: rotate(180deg);
        }

        .task-tabs-container {
            display: flex;
            align-items: center;
            gap: 14px;
            /* border-bottom: 1px solid var(--tkd-border); */
            white-space: nowrap;
        }

        .task-card .nav-tabs {
            display: flex;
            gap: 8px;
            max-width: 100%;
            margin: 10px 0 6px;
            border-bottom: 0;
            overflow-x: auto;
            overflow-y: hidden;
            scrollbar-width: thin;
            scrollbar-color: #d7d7d7 transparent;
        }

        .task-card .nav-tabs li {
            margin-bottom: 5px !important;
        }

        .task-card .task-tab {
            border: 1px solid var(--tkd-border);
            border-radius: 16px;
            line-height: 1.5;
        }

        .task-card .task-tab-link,
        .task-card .nav-tabs>li>a,
        .task-card .nav-tabs>li>a:focus,
        .task-card .nav-tabs>li>a:hover,
        .task-card .nav-tabs>li.active>a,
        .task-card .nav-tabs>li.active>a:focus,
        .task-card .nav-tabs>li.active>a:hover {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border: 0 !important;
            background: transparent !important;
            color: inherit !important;
            box-shadow: none !important;
            padding: 4px 10px !important;
            font-size: 12px;
            text-decoration: none;
        }

        #expandTabs {
            margin-left: auto;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .task-card #expandTabs .btn-add-task {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-height: 28px;
            border: 1px solid var(--tkd-danger);
            border-radius: 4px;
            color: var(--tkd-danger);
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 10px;
        }

        .task-card #taskbutton {
            margin-top: 10px;
        }

        .task-actions {
            justify-content: flex-end;
        }

        [data-bs-theme="dark"] .task-actions {
            /* background-color: var(--dark-primary, #191919) !important;  */
            padding: .5rem;
        }

        .task-card .box-container {
            grid-template-columns: minmax(0, 1fr) minmax(240px, 1fr);
            border: 1px solid var(--tkd-border);
            border-radius: 5px;
            border-top-left-radius: unset !important;
            border-bottom-left-radius: unset !important;
            overflow: hidden;
        }

        .left-side-task-card {
            width: 60%;
        }

        .right-task-box {
            width: 35%;
        }

        .task-card .box-container .box {
            padding: 16px 12px;
        }

        .task-card .top-left {
            /* border-right: 1px solid var(--tkd-border);s */
            border-bottom: 1px solid var(--tkd-border);
        }

        .task-card .bottom-left {
            border-right: 1px solid var(--tkd-border);
        }

        .task-card .top-right {
            border-bottom: 1px solid var(--tkd-border);
            border-top: 1px solid var(--tkd-border);
            border-right: 1px solid var(--tkd-border);
            border-top-right-radius: 5px;
            border-bottom-right-radius: 5px;

        }

        .task-details {
            display: flex;
            /* justify-content: space-between; */
            align-items: center;
            gap: 12px;
            padding-top: 8px;
        }

        .task-name {
            margin-bottom: 8px;
        }

        .task-status,
        .task-priority {
            display: inline-block;
            border-radius: 12px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 600;
        }

        .task-priority {
            background: #ffe5e6;
            color: #e3200b;
        }

        [data-bs-theme="dark"] .task-priority {
            background: var(--dark-primary, #191919);
        }

        .progress-text {
            display: block;
            color: #6d747d;
            font-size: 11px;
            font-weight: 500;
        }

        .circular-progress {
            width: 42px;
            height: 42px;
        }

        .circle-bg {
            fill: none;
            stroke: #ddd;
            stroke-width: 3.8;
        }

        .circle {
            fill: none;
            stroke-linecap: round;
            transform-origin: center;
            transition: stroke-dasharray 0.5s ease-in-out;
        }

        .assignee-info {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 8px;
        }

        .assignee-details {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .assignee-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
        }

        .task-card .timeline-vertical {
            display: flex;
            flex-direction: column;
            position: relative;
            gap: 10px;
        }

        .task-card .timeline-vertical::before {
            content: '';
            position: absolute;
            top: 12px;
            left: 9px;
            width: 2px;
            height: calc(100% - 30px);
            border-left: 2px dashed #ffa4a4;
            z-index: 0;
        }

        .task-card .timeline-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            position: relative;
            z-index: 1;
        }

        .task-card .timeline-item .timeline-icon {
            width: 20px;
            height: 20px;
            border: 2px solid #ffa4a4;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffa4a4;
            font-size: 12px;
            background: var(--tkd-surface) !important;
            flex-shrink: 0;
            position: absolute;
        }

        .task-card .timeline-content {
            display: flex;
            flex-direction: column;
            padding-left: 30px;
        }

        .task-card .timeline-date {
            margin: 0;
        }

        .task-card .timeline-block {
            position: relative;
            top: 10px;
        }

        .task-card .label {
            padding: 10px 0 0 43px;
        }

        .task-card .date {
            padding-left: 43px;
        }

        .task-card .icon {
            position: absolute;
            top: 0;
            left: 13px;
        }

        .task-card .note-editor {
            border: 0 solid transparent !important;
            overflow: visible !important;
            position: relative;
        }

        .task-card .note-editor .note-toolbar {
            /* position: absolute !important; */
            right: 0 !important;
            top: 0 !important;
            left: 0 !important;
            height: auto !important;
            z-index: 999 !important;
        }

        .task-card .note-editor .note-editing-area {
            margin-bottom: 40px;
        }

        .shadow-lg {
            box-shadow: unset !important;
        }

        #commentWrapper {
            background: #fff3f7 !important;
        }

        [data-bs-theme="dark"] #commentWrapper {
            background: var(--dark-primary, #191919) !important;
        }

        [data-bs-theme="dark"] .nobg {
            background-color: unset !important;
        }

        [data-bs-theme="dark"] .note-toolbar {
            background-color: var(--dark-primary, #191919) !important;
        }

        #task-attachment-dropper {
            min-height: 78px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px dashed #d8dde6;
            border-radius: 4px;
            background: #fff;
            color: #5c636d;
            font-size: 12px;
        }

        #task_manual_file_trigger {
            border: 0;
            background: transparent;
            color: var(--tkd-danger);
            font-size: 12px;
            font-weight: 600;
            padding: 0;
        }

        #task_update_details #update-dropper {
            min-height: 78px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px dashed #d8dde6;
            border-radius: 4px;
            background: #fff;
            color: #5c636d;
            font-size: 12px;
        }

        #task_update_details #task_file_triggers {
            border: 0;
            background: transparent;
            color: var(--tkd-danger);
            font-size: 12px;
            font-weight: 600;
            padding: 0;
        }

        #task_attachments .attach,
        #task_update_details #task_attachment_updates .attachs {
            border: 1px solid #edf0f5;
            border-radius: 6px;
            background: #f9fafb;
            padding: 8px 10px;
        }

        .task-empty-wrapper {
            margin-top: 20px;
            text-align: center;
        }

        .task-empty-img {
            width: min(420px, 100%);
            height: auto;
        }

        .task-empty-text {
            margin-top: 10px;
            color: #666;
            font-size: 13px;
        }

        #ticket-detail-page .color-code-yellow-text {
            color: #ffa726 !important;
        }

        #ticket-detail-page .color-code-blue-text {
            color: #42518c !important;
        }

        #ticket-detail-page .color-code-rose-text {
            color: #f275ad !important;
        }

        .task-card .ai-assist-btn {
            border: 0 !important;
            border-radius: 15px !important;
            background: linear-gradient(45deg, #ff007f, #ff7300);
            color: #fff !important;
            font-size: 12px !important;
            font-weight: 700 !important;
            padding: 5px 14px !important;
        }

        .error{
            color: #ed1117 !important;
        }

        #tat-error{
          color: #ed1117 !important;
        }

        .tkd-action-more .extender-icon {
            transition: transform 0.3s ease-in-out;
            transform: rotate(0deg) !important;
        }
        .tkd-action-bar.is-expanded .tkd-action-more .extender-icon {
            transform: rotate(180deg) !important;
        }
        

        @media (max-width: 991px) {
            .tkd-body {
                grid-template-columns: 1fr;
            }

            .tkd-detail-row {
                grid-template-columns: 160px minmax(0, 1fr);
            }

            .task-card .box-container {
                grid-template-columns: 1fr;
            }

            .task-card .top-left,
            .task-card .bottom-left {
                border-right: 0;
            }

            .task-card .top-right {
                border-top: 1px solid var(--tkd-border);
            }
        }



        @media (max-width: 575px) {

            .tkd-status-strip,
            .tkd-ticket-summary,
            .tkd-detail-block,
            .tkd-conversation,
            .tkd-reply,
            .task-card {
                padding-left: 14px !important;
                padding-right: 14px !important;
            }

            .tkd-detail-row {
                grid-template-columns: 1fr;
                gap: 4px;
            }

            .tkd-action-bar.is-expanded {
                padding-right: 52px;
            }

            .tkd-action-bar.is-expanded .tkd-action-more {
                top: 10px;
                right: 12px;
            }

            .task-card-header,
            .task-header-right {
                align-items: flex-start;
                flex-direction: column;
            }

            .task-card>.progress-bar {
                width: 100%;
                margin-left: 0;
            }

            .task-details {
                align-items: flex-start;
                flex-direction: column;
            }
        }

        /* Creator Info */
        .tkd-panel-head {
            padding-left: 1rem;
            cursor: pointer;
            transition: background 0.2s;

            &[aria-expanded="true"] .chevron {
                transform: rotate(180deg);
            }
        }

        .chevron {
            transition: transform 0.2s ease;
        }

        /* Creator Info */

        /* Knowledge Document  */

        /* Knowledge Base */

        .tkd-kd-slider {
            padding: 12px;
        }

        .kb-card {
            background: #f5f5f5;
            border-radius: 8px;
            overflow: hidden;
            transition: all .2s ease;
        }

        .kb-card:hover {
            transform: translateY(-2px);
        }

        .kb-card-img {
            width: 100%;
            height: 140px;
            object-fit: cover;
            display: block;
        }

        .kb-card-body {
            padding: 5px;
        }

        .kb-title {
            color: #4a4a4a;
            font-size: .875rem;
            line-height: 1.35;
            font-weight: 400;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: fit-content;
        }

        #kdSlider .carousel-indicators {
            display: none;
        }

        #kdSlider .carousel-control-prev,
        #kdSlider .carousel-control-next {
            display: none;
        }

        .kb-nav {
            width: 23px;
            height: 23px;
            border: none;
            border-radius: 50%;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .15);

            display: flex;
            align-items: center;
            justify-content: center;

            cursor: pointer;
            transition: .2s;
        }

        .kb-nav:hover {
            background: #f1f1f1;
        }

        .kb-nav i {
            font-size: 12px;
            color: #555;
        }
        .error{
            color:#f12f35;
        }

        .tkd-detail-row{
            display: grid;
            column-gap: 18px;
            align-items: start;
        }

        .tkd-detail-label{
            grid-column: 1;
        }

        .tkd-detail-value{
            grid-column: 2;
            width: 100%;
        }

        .tkd-detail-row > label.error{
            grid-column: 2;
            margin: 6px 0 0;
            color: #dc3545;
            font-size: 13px;
            font-weight: 400;
            line-height: 1.4;
        }

        .custom-option-group > label.error{
            width: 100%;
            /* margin-top: 6px; */
            color: #dc3545;
            /* font-size: 13px; */
            font-weight: 400;
        }

    .tkd-detail-value{
        display:flex;
        flex-direction:column;
        align-items:flex-start;
    }

    .tkd-detail-value label.error{
        color:#dc3545;
        font-weight:400;
    }

    .custom-option-group label.error{
        width:100%;
        color:#dc3545;
        font-weight:400;
    }

    .select2 + label.error{
        display:block;
        margin-top:6px;
    }

    .custome-field-border{
        border: 1px solid var(--app-border) !important;
        border-radius: 8px !important;
    }

    .form-check-label{
        margin-top: .15rem !important;
        margin-left: .2rem;
    }

   .fieldsets .select2-container--default .select2-selection--single .select2-selection__rendered{
        height: unset !important;
    }

    .fieldsets .select2-container--default .select2-selection--single .select2-selection__clear{
        margin-top: -.5rem;
       padding-right: 0.5rem;
    }

    .fieldsets .select2-container--default .select2-selection--single .select2-selection__arrow b{
        margin-left: unset !important;
        margin-top: -5px !important;
    }
    .custom-field-row,
    .tkd-detail-row{
        display:flex;
        align-items:flex-start;
        /* margin-bottom:18px; */
        gap:18px;
    }

    .custom-field-row .col-md-3,
    .tkd-detail-label{
        width:220px;
        min-width:220px;
        max-width:220px;

        font-weight:600;
        color:#212529;

        display:flex;
        align-items:center;
        gap:6px;
    }

    .custom-field-row .col-md-9,
    .tkd-detail-value{
        flex:1;
        min-width:0;
    }

    .tkd-detail-value .input-group,
    .custom-field-row .d-flex,
    .tkd-detail-value .d-flex{
        display:flex;
        flex-wrap:wrap;
        align-items:flex-start;
        gap:8px;

    }

    .input-group>.form-control,
    .d-flex>.select2{
        flex:1;
    }

    .input-group>.custom-field-help,
    .d-flex>.custom-field-help,
    .input-group>.input-group-text{
        flex-shrink:0;
    }

    .custom-option-group{
        display:flex;
        flex-wrap:wrap;
        align-items:center;
        gap:16px;

    }

    .custom-option-group .form-check{
        display:flex;
        align-items:center;
        margin:0;
    }

    .custom-option-group input{
        margin-top:0;
    }

    .custom-option-group label.form-check-label{
        margin-left:6px;
        margin-bottom:0;
    }

    label.error{
        display:block;
        width:100%;
        color:#dc3545 !important;
        font-size:13px;
        line-height:1.4;
        margin-top:6px !important;
        margin-bottom:0;
    }

    .input-group>label.error,
    .d-flex>label.error{
        flex:0 0 100%;
        order:100;
    }

    .custom-option-group>label.error{
        flex:0 0 100%;
        order:999;
        margin-top:8px !important;
    }

    input.error,
    select.error,
    textarea.error{
        border-color:#dc3545 !important;
    }

    .select2-hidden-accessible.error+.select2 .select2-selection{
        border-color:#dc3545 !important;
    }
    .select2{
        width:100% !important;
    }
    .select2-selection{
        min-height:38px !important;
    }

    /* .select2-selection__rendered{
        line-height:36px !important;
    } */
    .select2-selection__arrow{
        height:36px !important;
    }

    .custom-option-group{
        display:flex !important;
        flex-wrap:wrap !important;
        align-items:flex-start !important;
        gap:12px 20px;
        width:100%;
    }

    .custom-option-group .form-check{
        display:inline-flex !important;
        align-items:center;
        margin:0;
        flex:0 0 auto;
        white-space:nowrap;
    }

    .custom-option-group .form-check-input{
        margin-top:0;
        margin-right:6px;
    }

    .custom-option-group .form-check-label{
        margin:0;
        white-space:nowrap;
    }

    /* Help icon */
    .custom-option-group .custom-field-help{
        margin-left:auto;
        flex:0 0 auto;
    }

    /* Error should always go to next line */
    .custom-option-group > label.error{
        flex:0 0 100% !important;
        order:999 !important;
        margin-top:6px !important;
        margin-bottom:0 !important;
        color:#dc3545;
    }

    @media(max-width:768px){
        .custom-field-row,
        .tkd-detail-row{
            flex-direction:column;
            gap:8px;
        }
        .custom-field-row .col-md-3,
        .tkd-detail-label{
            width:100%;
            min-width:100%;
            max-width:100%;
        }
    }
    .tkd-detail-block.is-collapsed {
        display: none;
    }
        
    #ticketDetailContent.is-collapsed{
        display:none;
    }

    #cc_master_value {
        border-bottom: 1px dashed #dee2e6 !important;
    }
    #cc_master .hide {
        display: none !important;
    }
    #cc_master .bi {
        font-size: 1.1rem;
    }
    .tkd-conversation-card{
        border: 1px solid #e9ecef;
        border-radius: 10px;
        margin-bottom: 15px;
        box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);
    }
    .tml-content-container p{
        font-size: 13px !important;
    }
    /* 1st */
    .timeline-card-pink{
        background: #F6DBEE33;

    }
   .tl-note-background {
        background: #fffcc21f !important;
    }
    /* 2nd */
    .timeline-card-blue{
        background-color: #F6F8FC;
    }
    .color-code-yellow-text {
        color: #ffa726 !important;
    }
    .color-code-blue-text {
        color: #42518C !important;
    }
    .color-code-rose-text {
        color: #f275ad !important;
    }
    .form-check.small-check {
        display: flex;
        align-items: center;
        gap: 6px;
        padding-left: 0;
    }

    .form-check.small-check .form-check-input {
        width: 14px;
        height: 14px;
        margin: 0;
        flex-shrink: 0;
    }

    .form-check.small-check .form-check-label {
        margin: 0;
        line-height: 14px;
    }
    .tkt-hst-avatar {
        background:#E4570C !important;
    }
    </style>
@endpush

@push('scripts')
    <script src="{{ CommonHelper::asset('js/drag-drap.js') }}"></script>
    <script src="{!! CommonHelper::asset('js/task_management/history.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('plugins/moment-business-days/index.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('plugins/moment-timezone/moment-timezone.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('plugins/jquery_countdown/jquery.countdown.min.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/tickets/work-hour-count-down.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.js') !!}"></script>
    {{-- <script type="text/javascript" src="{!! CommonHelper::asset('plugins/swipebox/js/jquery.swipebox.min.js') !!}"></script> --}}
    <script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.min.js') !!}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.11/clipboard.min.js"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('plugins/flatpicker/js/flatpicker.js') !!}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.js"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/customfield.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/form/depends_render.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('plugins/swipebox/js/jquery.swipebox.min.js') !!}"></script>
    @if (in_array(config('app.client'), ['ltts', 'grdemo', 'rolepermission']))
        <script type="text/javascript" src="{!! CommonHelper::asset('js/tickets/customform/customform.js') !!}"></script>
    @endif
    @if (in_array(config('app.client'), ['ltsct']))
        <script type="text/javascript" src="{!! CommonHelper::asset('js/tickets/customform/ltsct_customform.js') !!}"></script>
    @endif
    <script>
        (function($) {
            if (!$ || !window.bootstrap || !window.bootstrap.Modal || $.fn.modal) {
                return;
            }

            $.fn.modal = function(action) {
                return this.each(function() {
                    var modal = window.bootstrap.Modal.getOrCreateInstance(this);

                    if (action === 'hide') {
                        modal.hide();
                    } else if (action === 'toggle') {
                        modal.toggle();
                    } else {
                        modal.show();
                    }
                });
            };

            $(document).on('click', '[data-dismiss="modal"]', function() {
                var modalEl = this.closest('.modal');
                if (modalEl) {
                    window.bootstrap.Modal.getOrCreateInstance(modalEl).hide();
                }
            });
        })(window.jQuery);
    </script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/tickets/ticket-detail.js') !!}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.addEventListener('click', function(event) {
                var button = event.target.closest('.tkd-comment-toggle');
                if (!button) return;

                var card = button.closest('.tkd-conversation-card');
                if (!card) return;

                var isCollapsed = card.classList.toggle('is-collapsed');
                button.setAttribute('aria-expanded', String(!isCollapsed));
                button.setAttribute('aria-label', isCollapsed ? 'Expand conversation' :
                    'Collapse conversation');
                button.innerHTML = isCollapsed ?
                    '<i class="bi bi-arrows-angle-expand"></i>' :
                    '<i class="bi bi-arrows-angle-contract"></i>';
            });

            document.querySelectorAll('.collapse').forEach(function(el) {
                var trig = document.querySelector('[href="#' + el.id + '"], [data-bs-target="#' + el.id +
                    '"]');
                if (!trig) return;
                el.addEventListener('show.bs.collapse', function() {
                    trig.setAttribute('aria-expanded', 'true');
                    trig.classList.add('border-0');
                });
                el.addEventListener('hide.bs.collapse', function() {
                    trig.setAttribute('aria-expanded', 'false');
                    trig.classList.remove('border-0');
                });
            });

        });

        $(document).ready(function() {
            var config = {};
            config.url = {};
            @if (isset($_REQUEST['b']) && $_REQUEST['b'] == 'archived')
                config.archive = true;
            @else
                config.archive = false;
            @endif
            config.botIcon = '{{ asset('images/mati.png') }}';
            config.url.geminiUrl = "{{ url('ai-search') }}";
            config.url.submit_problem_mgt = "{{ url('problem-management/add_impacted_ticket') }}";
            config.url.problem_mgt = "{{ url('jx-get-problem-mgt-select2') }}";
            config.url.problem_mgt_delete = "{{ url('problem-management/remove_impacted_ticket') }}";
            config.url.init_ticket = "{{ url('tickets/init') }}";
            config.url.create_ticket = "{{ url('tickets/create') }}";
            config.url.update_status = "{{ url('ticket/update_status') }}";
            config.url.editTicket = "{{ url('ticket/edit') }}";
            config.url.delete = "{{ url('ticket/delete') }}";
            config.url.getCustomFieldNote = "{{ url('getCustomFieldNote') }}";
            config.url.add_comment = "{{ url('ticket/add_comment') }}";
            config.url.get_timeline = "{{ url('ticket/get_timeline') }}";
            config.url.get_timeline_archived = "{{ url('ticket/get_timeline_archived') }}";
            config.url.mytickets = "{{ url('tickets/newlist/my-tickets') }}";
            config.url.alltickets = "{{ url('tickets/newlist/all-tickets') }}";
            config.url.transfer = "{{ url('ticket/transfer') }}";
            config.url.get_self_assign_mode = "{{ url('ticket/get-self-assign-mode') }}";
            config.url.add_feedback = "{{ url('ticket/feedback/add') }}";
            config.url.get_data_for_transfer = "{{ url('ticket/get-data-for-transfer') }}";
            config.url.get_users_to_assign = "{{ url('ticket/get_users_to_assign') }}";
            config.url.get_users_to_assign_by_dep = "{{ url('ticket/get_users_to_assign_by_dep') }}";
            config.url.assign_to = "{{ url('ticket/assign_to') }}";
            config.url.self_assign = "{{ url('ticket/self_assign') }}";
            config.url.change_creator = "{{ url('ticket/change_creator') }}";
            config.url.reopen = "{{ url('ticket/reopen') }}";
            config.url.staring = "{{ url('ticket/staring') }}";
            config.url.spam = "{{ url('ticket/spam') }}";
            config.url.current_page = "{{ url('ticket/') }}";
            config.url.back_to = "{{ url('tickets/newlist/' . $back_to) }}";
            config.url.departments_by_company = "{{ url('departments/by-company') }}";
            config.url.departments_with_company = "{{ url('tickets/departments') }}";
            config.url.problem_categories_by_company = "{{ url('tickets/problem-categories/by-dept') }}";
            config.url.attachment_add = "{{ url('ticket/attachment/add') }}";
            config.url.attachment_remove = "{{ url('ticket/attachment/remove') }}";
            config.url.attachment_download = "{{ url('ticket/attachment/download') }}";
            config.url.attachment_view = "{{ url('ticket/attachment/view') }}";
            config.url.getUserByAjax = "{{ url('getUserByQuery') }}";
            config.url.update_master_cc = "{{ url('tickets/update-master-cc') }}";
            config.url.ticket_history = "{{ url('ticket/ticket_history') }}";
            config.url.ticket_history_archived = "{{ url('ticket/ticket_history_archived') }}";
            config.url.getDeviceByAjax = "{{ url('getDeviceForDropDown') }}";
            config.url.getUserDeviceByAjax = "{{ url('getUserDeviceForDropDown') }}";
            config.url.getTagDetails = "{{ url('ticket/getTagDetails') }}";
            config.url.edit_feedback = "{{ url('ticket/feedback/edit') }}";
            config.url.update_feedback = "{{ url('ticket/feedback/update') }}";
            config.url.getTechCurrentStatusById = "{{ url('technician/get-tech-curren-status-id') }}";
            config.url.get_users_to_assign_by_avability =
                "{{ url('ticket/get_users_to_assign_by_availability') }}";
            config.url.get_users_to_assign_by_dep_by_availability =
                "{{ url('ticket/get_users_to_assign_by_dep_by_availability') }}";
            config.url.updateTicketTags = "{{ route('updateTicketTags') }}";
            config.url.requested_form = "{{ url('requested_form') }}";
            config.url.requestInfo = "{{ url('tickets/requestInfo') }}";
            config.url.service_request_form = "{{ url('tickets/serviceRequestForm') }}";
            config.url.updateTicketTypeDetails = "{{ route('updateTicketTypeDetails') }}";
            config.url.getStatusByTicketType = "{{ route('getStatusByTicketType') }}";
            config.url.getManagerByAjax = "{{ url('getUserByQuery') }}";
            config.url.getLocationByAjax = "{{ url('getLocationByQuery') }}";
            config.url.getTicketTypeFieldsets = "{{ url('ticket/getTicketTypeFieldsets') }}";
            config.url.view_status_form = "{{ url('status_requested_form/view') }}";
            config.url.is_valid_workaround = "{{ url('ticket/is_valid_workaround') }}",
            config.url.getFormIdFromTicketProblemCategory = "{{ url('get-status-formid') }}";
            config.url.incident = "{{ url('jx-get-incident-select2') }}";
            config.url.submit_incident = "{{ url('tickets/incident/add_impacted_ticket') }}";
            config.url.incident_delete = "{{ url('tickets/incident/remove_impacted_ticket') }}";
            config.url.getFormByTicketProblemCategory = "{{ url('get-status-form') }}";
            config.url.getUserCCByAjax = "{{ url('getUserCCByQuery') }}";
            config.url.blockCalendarsList = "{{ url('block-calendar/get-block-calendars') }}";
            config.url.addBlockCalendar = "{{ url('block-calendar/add-block-calendar') }}";
            config.url.editBlockCalendar = "{{ url('block-calendar/edit-block-calendar') }}";
            config.url.updateBlockCalendar = "{{ url('block-calendar/update-block-calendar') }}";
            config.url.clendarAuthentication = "{{ url('block-calendar/login/microsoft') }}";
            config.url.get_tickets = "{{ url('tickets/list/jx-ticket-detail') }}";
            config.url.get_archived_tickets = "{{ url('tickets/list/jx-ticket-archived-detail') }}";
            config.url.get_ticket_calendars = "{{ url('block-calendar/get-ticket-calendars') }}";
            config.url.event_feedback = "{{ url('block-calendar/event_feedback') }}";
            config.url.update_event_feedback = "{{ url('block-calendar/update_event_feedback') }}";
            config.url.requested_custom_form = "{{ url('requested_form/custom_form/edit') }}";
            config.url.requested_custom_formqty = "{{ url('requested_form/custom_form/edit_qty') }}";
            config.url.fetchAvailable = "{{ url('fetchAvailableItem') }}";
            config.url.remove_table_row = "{{ url('requested_form/custom_form/remove-item') }}";
            config.url.user_info = "{{ url('user/info') }}";
            config.url.task_info = "{{ url('task-management/info') }}";
            config.url.device_info = "{{ url('device/info') }}";
            config.url.getRelatedTask = "{{ url('tickets/get-relevant-tasks') }}";
            config.url.addTask = "{{ url('task-management/ajaxAddTask') }}";
            config.url.editTask = "{{ url('edit-task') }}";
            config.url.deleteTask = "{{ url('delete-task') }}";
            config.url.ajaxEditTask = "{{ url('task-management/ajaxEditTask') }}";
            config.url.ajaxStatusEditTask = "{{ url('task-management/ajaxStatusEditTask') }}";
            config.url.image = "{{ asset('images/notask.png') }}";
            config.url.matiImage = "{{ asset('images/mati.png') }}";
            config.url.Taskhistory = "{{ url('task-management/taskhistory') }}";
            config.url.add_task_comment = "{{ url('task-management/add_comment') }}";
            config.url.add_task_attachment = "{{ url('task-management/add_attachment') }}";
            config.url.remove_task_attachment = "{{ url('task-management/remove_attachment') }}";
            config.url.get_task_timeline = "{{ url('task-management/get_comment') }}";
            config.url.download_task_attachment = "{{ url('task-management/download_attachment') }}";
            config.url.view_task_attachment = "{{ url('task-management/view_attachment') }}";
            config.url.self_assign_task = "{{ url('task-management/self-assign') }}";
            config.url.getSentiment = "{{ url('ticket-getSentiment') }}";
            config.url.getStatusApproval = "{{ url('get-status-approval') }}";
            config.url.sub_category = "{{ url('tickets/fetch-sub-category') }}";
            config.url.getQueryComponent = "{{ url('getByCustomDropDown/getComponent') }}";
			config.url.getByQueryDevice = "{{ url('getByCustomDropDown/getDevice') }}";
			config.url.getQueryLocation = "{{ url('getByCustomDropDown/getLocation') }}";
			config.url.getQueryTicket = "{{ url('getByCustomDropDown/getTicket') }}";
			config.url.getQueryUser = "{{ url('getByCustomDropDown/getUser') }}";
			config.url.getQueryPlace = "{{ url('getByCustomDropDown/getPlace') }}";
			config.url.getQueryManufacture = "{{ url('getByCustomDropDown/getManufacture') }}";
			config.url.getQueryModel = "{{ url('getByCustomDropDown/getModel') }}";
			config.url.getQueryTicketProcureRequest = "{{ url('getByCustomDropDown/getTicketProcureRequest') }}";
			config.url.getQueryRecord = "{{ url('getByCustomDropDown/getRecord') }}";
			config.url.getQueryTask = "{{ url('getByCustomDropDown/getTask') }}";
			config.url.getQueryLicense = "{{ url('getByCustomDropDown/getLicense') }}";
			config.url.getQueryProject = "{{ url('getByCustomDropDown/getProject') }}";
			config.url.getQueryPurchase = "{{ url('getByCustomDropDown/getPurchase') }}";
			config.url.getQuerySupplier = "{{ url('getByCustomDropDown/getSupplier') }}";
			config.url.getQueryContract = "{{ url('getByCustomDropDown/getContract') }}";
            config.url.base_url = "{{ url('') }}"
            config.url.getQueryDepartment = "{{ url('getDepartmentsWithCompanyByQuery') }}";
            config.tkt_block_calendar = {!! json_encode([
                'subject' => $ticket->subject,
                'assigned_to' => $ticket->assignedTo->id ?? null,
                'assigned_to_email' => $ticket->assignedTo->email ?? null,
                'creator_id' => $ticket->creator_id,
                'creator_email' => $ticket->creator->email,
            ]) !!};

            config.token = "{{ csrf_token() }}";
            config.creator_isTechnician = "{{ CommonHelper::userIsTechnician($ticket->creator_id) }}";
            config.authuser_isTechnician = "{{ CommonHelper::userIsTechnician(Auth::id()) }}";
            config.user = {!! json_encode(Auth::user()->only('id', 'first_name', 'last_name', 'username', 'company_id')) !!};
            config.user.role = {!! json_encode(Auth::user()->getRoleNames()->first()) !!};
            config.companies = {!! json_encode($companies) !!};
            config.company_defulte = {!! json_encode($company_id) !!};
            config.company_user_detail = {!! json_encode($userDatail) !!};
            config.statuses = {!! json_encode($statuses) !!};
            config.statuses1 = {!! json_encode($statuses1) !!};
            config.priorities = {!! json_encode($priorities) !!};
            config.task_statuses = {!! json_encode($task_statuses) !!}
            config.data = {!! json_encode(
                array_merge(
                    $ticket->only(
                        'id',
                        'subject',
                        'content',
                        'department_id',
                        'service_type_id',
                        'problem_type_id',
                        'priority_id',
                        'status_id',
                        'tat',
                        'tat_expire',
                        'starred',
                        'feedback',
                        'spam',
                        'assigned_to',
                        'merge_primary',
                        'is_merge_primary',
                        'merged_ids',
                        'cc_emails',
                        'status',
                        'sub_category_id',
                        'ticket_type',
                        'creator_id',
                        'company_id',
                    ),
                    ['tat_halt' => $ticket->status->tat_halt],
                    ['device' => $device],
                    ['self_star' => $ticket->selfCheckStarred()],
                ),
            ) !!};
            config.data.assignToName = {!! json_encode($ticket->assignedTo->fullName() ?? '') !!};
            config.main_attachments = {!! json_encode($attachments) !!};
            config.data.expire_info = "{{ $ticket->expireInfo() }}";
            config.wai = {{ $ticket->wai() }};
            config.access_privilege = "{{ $access_privilege }}";
            config.action_controls = {!! json_encode($action_controls) !!};
            config.tkt_config = {!! json_encode($tkt_config) !!};
            config.holidays = {!! json_encode($holidays) !!};
            config.ticketTypeStatus = {!! json_encode($ticketTypeStatus) !!};
            config.ticketTypeFields = {!! json_encode($ticketTypeFields) !!};
            config.ticket_type_details = {!! json_encode($ticket_type_details) !!};
            config.canManageTicketType = {{ $canManageTicketType == 1 ? 'true' : 'false' }};
            config.status_spam = {{ $status_spam }};
            config.halt_statuses = {!! json_encode($halt_statuses) !!};
            config.feedback =
                @if ($wai)
                    {!! json_encode($ticket->feedBackRating()) !!};
                @else
                    [];
                @endif
            config.timezone = "{{ config('app.timezone') }}";
            config.client = "{{ config('app.client') }}";
            config.sub_client = "{{ config('app.sub_client') }}";
            config.startDateID = {!! json_encode($customFields) !!};
            config.url.getDayForEndDate = "{{ route('getDayForEndDate') }}";
            config.data.work_around_info = "{{ $ticket->workaroundInfo() }}";
            config.data.response_info = "{{ $ticket->responseInfo() }}";
            config.aiAsssistEnabled = "{{ config('app.gemini_ai_key') }}";
            config.permissions = {!! json_encode($permissionArray) !!};
            config.department = {!! json_encode(optional($ticket->department)->name) !!}
            config.parent_Category = {!! json_encode(optional($ticket->problemCategory)->name) !!};
            config.sub_Category = {!! json_encode(optional($ticket->subCategory)->name) !!};
            config.department_id = {!! json_encode(optional($ticket->department)->id) !!}
            config.parent_Category_id = {!! json_encode(optional($ticket->problemCategory)->id) !!};
            config.sub_Category_id = {!! json_encode(optional($ticket->subCategory)->id) !!};
            config.user_privilege_departments = {!! json_encode(array_column($user_privilege_departments, 'department_id')) !!}
            config.url.checkReopen = "{{ url('tickets/check-reopen-eligibility') }}";
            config.url.getTicketStatusApprovalList = "{{ url('get-status-approval-list') }}";
            config.url.getCustomField = "{{ url('ticket/custom-fields') }}";
            {{-- @if (isset($ticket->sub_category_id) && $ticket->sub_category_id != null)
            config.category = {!! json_encode($ticket->sub_category_id) !!}
        @else
            config.category = {!! json_encode($ticket->problem_category_id) !!}
        @endif --}}

            @if (isset($category['category']) && $category['category'] != '')
                config.category = {!! json_encode($category['category']) !!}
            @else
                config.category = {!! json_encode($ticket->problem_category_id) !!}
            @endif

            config.custom_fieldData = {!! json_encode($ticket->custom_fields) !!};
            // @if (isset($ticket->sub_category_id) && isset($ticket->subCategory->custom_fieldset))
            //     config.customFieldset = {!! json_encode($ticket->subCategory->custom_fieldset) !!}
            // @else
            //     config.customFieldset = {!! json_encode($ticket->problemCategory->custom_fieldset) !!}
            // @endif
            // moment.tz.setDefault(config.timezone);
            // moment.updateLocale('us', {
            //     holidays: config.holidays,
            //     holidayFormat: 'YYYY-MM-DD',
            //     workingWeekdays: {!! $tkt_config->workingWeekdays(true) !!}
            // });
            @if (in_array($ticket->problem_category_id, $crPCID))
                config.pcName = {!! json_encode($ticket->problemCategory->name) !!}
            @else
                config.pcName = "{{ '' }}"
            @endif

            @if (isset($ticket->form_type) && $ticket->form_type == 2)
                config.form_id = {!! json_encode($ticket->form_id) !!};
                config.form_type = {!! json_encode($ticket->form_type) !!};
            @endif
            config.translations = {
                upload_note: "{{ trans('ticket.ticket_detail.upload_note') }}",
                enter_your_message:  "{{ trans('ticket.create_ticket.enter_your_message') }}",
                Add_Attachment: "{{ trans('ticket.ticket_detail.Add_Attachment') }}",
                upload_maximum_limit: "{{ trans('ticket.ticket_detail.upload_maximum_limit') }}",
                file_name: "{{ trans('ticket.ticket_detail.file_name') }}",
                comment: "{{ trans('ticket.ticket_detail.Comment') }}",
                something_went_wrong: '{{ trans('content.user_fields.something_went_wrong') }}',
                Spam_t: "{{ trans('ticket.ticket_detail.Spam') }}",
                Merge_Primary_t: '{{ trans('ticket.ticket_detail.Merge_Primary') }}',
                Creator_Info_t: '{{ trans('ticket.ticket_detail.Creator_Info') }}',
                Department_Info_t: '{{ trans('ticket.ticket_detail.Department_Info') }}',
                CreatedUpdated_At_t: '{{ trans('ticket.ticket_detail.CreatedUpdated_At') }}',
                Assgined_To_t: '{{ trans('ticket.ticket_detail.Assgined_To') }}',
                View_t: '{{ trans('ticket.ticket_detail.View') }}',
                Add_to_Merge_t: '{{ trans('ticket.ticket_detail.Add_to_Merge') }}',
                Make_Primary_t: '{{ trans('ticket.ticket_detail.Make_Primary') }}',
                Remove_t: '{{ trans('ticket.ticket_detail.Remove') }}',
                Primary_t: '{{ trans('ticket.ticket_detail.Primary') }}',
                No_Filter: '{{ trans('ticket.ticket_detail.No_Filter') }}',
                filter_by_status: '{{ trans('content.filter_heading.filter_by_status') }}',
                Filter_By_Priority: '{{ trans('content.filter_heading.Filter_By_Priority') }}',
                filter_by_department: '{{ trans('content.filter_heading.filter_by_department') }}',
                Filter_Based_on: '{{ trans('content.filter_heading.Filter_Based_on') }}',
                Filter_By_Problem_Category: '{{ trans('content.filter_heading.Filter_By_Problem_Category') }}',
                Filter_By_Sub_Category: '{{ trans('content.filter_heading.Filter_By_Sub_Category') }}',
                Filter_By_Ticket_Handler: '{{ trans('content.filter_heading.Filter_By_Ticket_Handler') }}',
                Filter_By_Ticket_Creator: '{{ trans('content.filter_heading.Filter_By_Ticket_Creator') }}',
                Select_the_User: '{{ trans('ticket.ticket_detail.Select_the_User') }}',
                select_device: '{{ trans('ticket.ticket_detail.select_device') }}',
                connect: '{{ trans('ticket.ticket_detail.connect') }}',
                enter_first_few_letter: '{{ trans('ticket.ticket_detail.enter_first_few_letter') }}',
                select_priority: '{{ trans('ticket.ticket_detail.select_priority') }}',
                select_reply_to_account: '{{ trans('ticket.ticket_detail.select_reply_to_account') }}',
                New_Ticket: '{{ trans('ticket.ticket_detail.New_Ticket') }}',
                New_Schedular: '{{ trans('ticket.ticket_detail.New_Schedular') }}',
                Create: '{{ trans('ticket.ticket_detail.Create') }}',
                Transfer_Ticket: '{{ trans('ticket.ticket_detail.Transfer_Ticket') }}',
                Select_User: '{{ trans('ticket.ticket_detail.Select_User') }}',
                Enter_starting: '{{ trans('ticket.ticket_detail.Enter_starting') }}',
                Select_Problem_Category: '{{ trans('ticket.ticket_detail.Select_Problem_Category') }}',
                Select_Sub_Category: '{{ trans('ticket.ticket_detail.Select_Sub_Category') }}',
                Assign_Ticket_To: '{{ trans('ticket.ticket_detail.Assign_Ticket_To') }}',
                Add_Star: '{{ trans('ticket.ticket_detail.Add_Star') }}',
                Starred: '{{ trans('ticket.ticket_detail.Starred') }}',
                are_you_delete: '{{ trans('ticket.ticket_detail.are_you_delete') }}',
                are_you_star: '{{ trans('ticket.ticket_detail.are_you_star') }}',
                unstar_ticket: '{{ trans('ticket.ticket_detail.unstar_ticket') }}',
                are_you_spam: '{{ trans('ticket.ticket_detail.are_you_spam') }}',
                are_you_pick: '{{ trans('ticket.ticket_detail.are_you_pick') }}',
                feedback: '{{ trans('ticket.ticket_detail.feedback') }}',
                feedback_is: '{{ trans('ticket.ticket_detail.feedback_is') }}',
                Changed_tats: '{{ trans('ticket.ticket_detail.Changed_tats') }}',
                comment_summer: '{{ trans('ticket.ticket_detail.share_comment') }}',
                ticket_problem_mgt: '{{ trans('ticket.ticket_detail.select_problem_mgt') }}',
                are_you_delete_problem_mgt: '{{ trans('ticket.ticket_detail.are_you_delete_problem_mgt') }}',
                upload_file: '{{ trans('ticket.ticket_detail.upload_file') }}',
                Changes_Status_From: '{{ trans('ticket.ticket_detail.Changes_Status_From') }}',
                Changes_Priority_From: '{{ trans('ticket.ticket_detail.Changes_Priority_From') }}',
                To: '{{ trans('ticket.ticket_detail.to') }}',
                From: '{{ trans('ticket.ticket_detail.from') }}',
                Ticket_Transfer_Department_From: '{{ trans('ticket.ticket_detail.Ticket_Transfer_Department_From') }}',
                Ticket_Transfer_Category_Changes_From: '{{ trans('ticket.ticket_detail.Ticket_Transfer_Category_Changes_From') }}',
                Ticket_Transfer_subCategory_Changes_From: '{{ trans('ticket.ticket_detail.Ticket_Transfer_subCategory_Changes_From') }}',
                Ticket_Edit_subCategory_Changes_From: '{{ trans('ticket.ticket_detail.Ticket_Edit_subCategory_Changes_From') }}',
                Ticket_Edit_subCategory_Changes_To: '{{ trans('ticket.ticket_detail.Ticket_Edit_subCategory_Changes_To') }}',
                Ticket_Edit_Category_Changes_From: '{{ trans('ticket.ticket_detail.Ticket_Edit_Category_Changes_From') }}',
                Ticket_Edit_Category_Changes_To: '{{ trans('ticket.ticket_detail.Ticket_Edit_Category_Changes_To') }}',
                Changes_Done_By: '{{ trans('ticket.ticket_detail.Changes_Done_By') }}',
                Ticket_Transfer_subCategory_Changes_To: '{{ trans('ticket.ticket_detail.Ticket_Transfer_subCategory_Changes_To') }}',
                checkRequired: '{{ trans('ticket.ticket_detail.fill_custom_field') }}',
                New_ticket_created_to: '{{ trans('ticket.ticket_detail.New_ticket_created_to') }}',
                New_ticket_created: '{{ trans('ticket.ticket_detail.New_ticket_created') }}',
                Changes_Status_To: '{{ trans('ticket.ticket_detail.Changes_Status_To') }}',
                please_select: '{{ trans('ticket.ticket_detail.please_select') }}',
                select_incident: '{{ trans('ticket.ticket_detail.select_incident') }}',
                are_you_delete_incident: '{{ trans('ticket.ticket_detail.are_you_delete_incident') }}',
                Subject: '{{ trans('ticket.ticket_detail.Subject') }}',
                ticket_id: '{{ trans('ticket.ticket_detail.ticket_id') }}',
                Company: '{{ trans('ticket.ticket_detail.Company') }}',
                Related_Device: '{{ trans('ticket.ticket_detail.Related_Device') }}',
                Prob_Category: '{{ trans('ticket.ticket_detail.Prob_Category') }}',
                Prob_Subcategory: '{{ trans('ticket.ticket_detail.Prob_Subcategory') }}',
                edit_event: '{{ trans('ticket.ticket_detail.edit_event') }}',
                add_task: '{{ trans('ticket.ticket_detail.add_task') }}',
                edit_task: '{{ trans('ticket.ticket_detail.edit_task') }}',
                delete_task: '{{ trans('ticket.ticket_detail.delete_task') }}',
                search_placeholder: '{{ trans('content.task_management.press_enter_with_search_text') }}',
                search: '{{ trans('content.task_management.Search') }}',
                refresh: '{{ trans('content.task_management.Refresh_List') }}',
                are_you_pick_task: '{{ trans('content.task_management.are_you_pick_task') }}',
                download: 'Download',
                mark_spam:"{{trans('ticket.ticket_detail.mark_spam')}}",
                mark_not_spam:"{{trans('ticket.ticket_detail.mark_not_spam')}}",
                add_to_spam_list:"{{trans('ticket.ticket_detail.add_to_spam_list')}}",
                remove_from_spam_list:"{{trans('ticket.ticket_detail.remove_from_spam_list')}}",
                attachment:"{{trans('ticket.update_status.attachment')}}",

            };
            new MyApp(config);
            // var cb = new Clipboard('.ccb-btn');
            // cb.on('success', function (e) {
            //     e.clearSelection();
            //     $(e.trigger).attr('data-original-title', 'Copied').tooltip('show');
            //     setTimeout(function () {
            //         $(e.trigger).tooltip('hide').attr('data-original-title', 'Copy');
            //     }, 500);
            // }).on('error', function (e) {
            //     $(e.trigger).attr('data-original-title', 'Unable to copy').tooltip('show');
            //     setTimeout(function () {
            //         $(e.trigger).tooltip('hide').attr('data-original-title', 'Copy');
            //     }, 500);
            // });
        });
    </script>
@endpush

{{-- @page-meta
{
  "page_no": "SRD-01",
  "file": "request-detail.blade.php",
  "versions": [
    {
      "version": "1.2",
      "writer": "Priya Maru",
      "from": "2026-05-14",
      "reviewer": null,
    }
  ]
}
--}}
@extends('layouts.layout1')
@section('title', 'Service Request Details')
@section('content')
    <div class="sr-detail-wrapper">
        <div
            class="header-actions-wrapper header-actions-white-wrapper d-flex align-items-center justify-content-between pe-4">
            <div class="py-3">
                <div class="container-fluid ps-0">
                    <button type="button" class="bg-transparent border-0 d-flex align-items-center gap-2"
                        onclick="window.location.href='{{ url('tickets/requestList') }}'">
                        <svg width="20" height="20" viewBox="0 0 25 21" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M11.5662 0.440759C11.706 0.580116 11.817 0.745702 11.8927 0.928029C11.9684 1.11036 12.0074 1.30584 12.0074 1.50326C12.0074 1.70068 11.9684 1.89616 11.8927 2.07848C11.817 2.26081 11.706 2.4264 11.5662 2.56576L5.12993 9.00201L23.5049 9.00201C23.9028 9.00201 24.2843 9.16004 24.5656 9.44135C24.8469 9.72265 25.0049 10.1042 25.0049 10.502C25.0049 10.8998 24.8469 11.2814 24.5656 11.5627C24.2843 11.844 23.9028 12.002 23.5049 12.002L5.12993 12.002L11.5662 18.4408C11.848 18.7226 12.0063 19.1047 12.0063 19.5033C12.0063 19.9018 11.848 20.284 11.5662 20.5658C11.2844 20.8475 10.9022 21.0059 10.5037 21.0059C10.1052 21.0059 9.72298 20.8475 9.44118 20.5658L0.441182 11.5658C0.301343 11.4264 0.190387 11.2608 0.114679 11.0785C0.0389711 10.8962 -4.50154e-07 10.7007 -4.58778e-07 10.5033C-4.67401e-07 10.3058 0.038971 10.1104 0.114679 9.92803C0.190387 9.7457 0.301342 9.58011 0.441182 9.44076L9.44118 0.440759C9.58054 0.30092 9.74613 0.189964 9.92845 0.114255C10.1108 0.0385471 10.3063 -0.000425789 10.5037 -0.000425798C10.7011 -0.000425806 10.8966 0.038547 11.0789 0.114255C11.2612 0.189964 11.4268 0.30092 11.5662 0.440759Z"
                                fill="currentColor"></path>
                        </svg>
                        <h3 class="h3-text mb-0">{{ trans('service_ticket.service_detail.service_request_details') }} - #{{ $request->procure_tag }}</h3>
                    </button>
                </div>
            </div>
            <div class="d-flex gap-8">
                @if ($request->status_id == 3 && ($isTechnician || $creator->id == Auth::id()))
                    <a href="{{ url('ticket/') }}/{{ $request->ticket_id }}" target="_blank">
                        <button class="header-icon-btn header-icon-btn-sm" type="button">
                            <svg width="16" height="16" viewBox="0 0 21 15" fill="none"
                                xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0">
                                <path
                                    d="M20.25 5.25C20.4489 5.25 20.6397 5.17098 20.7803 5.03033C20.921 4.88968 21 4.69891 21 4.5V1.5C21 1.10218 20.842 0.720644 20.5607 0.43934C20.2794 0.158035 19.8978 0 19.5 0H1.5C1.10218 0 0.720644 0.158035 0.43934 0.43934C0.158035 0.720644 0 1.10218 0 1.5V4.5C0 4.69891 0.0790176 4.88968 0.21967 5.03033C0.360322 5.17098 0.551088 5.25 0.75 5.25C1.34674 5.25 1.91903 5.48705 2.34099 5.90901C2.76295 6.33097 3 6.90326 3 7.5C3 8.09674 2.76295 8.66903 2.34099 9.09099C1.91903 9.51295 1.34674 9.75 0.75 9.75C0.551088 9.75 0.360322 9.82902 0.21967 9.96967C0.0790176 10.1103 0 10.3011 0 10.5V13.5C0 13.8978 0.158035 14.2794 0.43934 14.5607C0.720644 14.842 1.10218 15 1.5 15H19.5C19.8978 15 20.2794 14.842 20.5607 14.5607C20.842 14.2794 21 13.8978 21 13.5V10.5C21 10.3011 20.921 10.1103 20.7803 9.96967C20.6397 9.82902 20.4489 9.75 20.25 9.75C19.6533 9.75 19.081 9.51295 18.659 9.09099C18.2371 8.66903 18 8.09674 18 7.5C18 6.90326 18.2371 6.33097 18.659 5.90901C19.081 5.48705 19.6533 5.25 20.25 5.25ZM1.5 11.175C2.34772 11.0029 3.10986 10.543 3.65728 9.87319C4.20471 9.20343 4.50376 8.36502 4.50376 7.5C4.50376 6.63498 4.20471 5.79657 3.65728 5.12681C3.10986 4.45705 2.34772 3.99714 1.5 3.825V1.5H6.75V13.5H1.5V11.175ZM19.5 11.175V13.5H8.25V1.5H19.5V3.825C18.6523 3.99714 17.8901 4.45705 17.3427 5.12681C16.7953 5.79657 16.4962 6.63498 16.4962 7.5C16.4962 8.36502 16.7953 9.20343 17.3427 9.87319C17.8901 10.543 18.6523 11.0029 19.5 11.175Z"
                                    fill="currentColor" />
                            </svg>
                            <span>{{ trans('service_ticket.service_detail.service_ticket') }}</span>
                        </button>
                    </a>
                @endif

                <button class="header-icon-btn header-icon-btn-sm btn-ticket-history" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span>{{ trans('service_ticket.service_detail.history') }}</span>
                </button>

                <button class="header-icon-btn header-icon-btn-sm btn-print-section" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    <span>{{ trans('service_ticket.service_detail.print') }}</span>
                </button>
                @if(!empty($request_form) && $request->form_type != 2 && (Auth::user()->isSuperUser() || $request_form->created_by == Auth::user()->id || $assigned_to->id == Auth::user()->id || $myApproval == true))
                    <a href="{{ url('requested_form/view/') }}/{{ $request_form->id }}" target="_blank">
                        <button class="header-icon-btn header-icon-btn-sm" type="button">
                            <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 2.4C2.84087 2.4 2.68826 2.46321 2.57574 2.57574C2.46321 2.68826 2.4 2.84087 2.4 3C2.4 3.15913 2.46321 3.31174 2.57574 3.42426C2.68826 3.53679 2.84087 3.6 3 3.6H13.8C13.9591 3.6 14.1117 3.53679 14.2243 3.42426C14.3368 3.31174 14.4 3.15913 14.4 3C14.4 2.84087 14.3368 2.68826 14.2243 2.57574C14.1117 2.46321 13.9591 2.4 13.8 2.4H3ZM4.2 9.6C4.67739 9.6 5.13523 9.41036 5.47279 9.07279C5.81036 8.73523 6 8.27739 6 7.8C6 7.32261 5.81036 6.86477 5.47279 6.52721C5.13523 6.18964 4.67739 6 4.2 6C3.72261 6 3.26477 6.18964 2.92721 6.52721C2.58964 6.86477 2.4 7.32261 2.4 7.8C2.4 8.27739 2.58964 8.73523 2.92721 9.07279C3.26477 9.41036 3.72261 9.6 4.2 9.6ZM4.2 8.4C4.04087 8.4 3.88826 8.33679 3.77574 8.22426C3.66321 8.11174 3.6 7.95913 3.6 7.8C3.6 7.64087 3.66321 7.48826 3.77574 7.37573C3.88826 7.26321 4.04087 7.2 4.2 7.2C4.35913 7.2 4.51174 7.26321 4.62426 7.37573C4.73679 7.48826 4.8 7.64087 4.8 7.8C4.8 7.95913 4.73679 8.11174 4.62426 8.22426C4.51174 8.33679 4.35913 8.4 4.2 8.4ZM6 12.6C6 13.0774 5.81036 13.5352 5.47279 13.8728C5.13523 14.2104 4.67739 14.4 4.2 14.4C3.72261 14.4 3.26477 14.2104 2.92721 13.8728C2.58964 13.5352 2.4 13.0774 2.4 12.6C2.4 12.1226 2.58964 11.6648 2.92721 11.3272C3.26477 10.9896 3.72261 10.8 4.2 10.8C4.67739 10.8 5.13523 10.9896 5.47279 11.3272C5.81036 11.6648 6 12.1226 6 12.6ZM4.8 12.6C4.8 12.4409 4.73679 12.2883 4.62426 12.1757C4.51174 12.0632 4.35913 12 4.2 12C4.04087 12 3.88826 12.0632 3.77574 12.1757C3.66321 12.2883 3.6 12.4409 3.6 12.6C3.6 12.7591 3.66321 12.9117 3.77574 13.0243C3.88826 13.1368 4.04087 13.2 4.2 13.2C4.35913 13.2 4.51174 13.1368 4.62426 13.0243C4.73679 12.9117 4.8 12.7591 4.8 12.6ZM7.2 7.8C7.2 7.64087 7.26321 7.48826 7.37573 7.37573C7.48826 7.26321 7.64087 7.2 7.8 7.2H13.8C13.9591 7.2 14.1117 7.26321 14.2243 7.37573C14.3368 7.48826 14.4 7.64087 14.4 7.8C14.4 7.95913 14.3368 8.11174 14.2243 8.22426C14.1117 8.33679 13.9591 8.4 13.8 8.4H7.8C7.64087 8.4 7.48826 8.33679 7.37573 8.22426C7.26321 8.11174 7.2 7.95913 7.2 7.8ZM7.8 12C7.64087 12 7.48826 12.0632 7.37573 12.1757C7.26321 12.2883 7.2 12.4409 7.2 12.6C7.2 12.7591 7.26321 12.9117 7.37573 13.0243C7.48826 13.1368 7.64087 13.2 7.8 13.2H13.8C13.9591 13.2 14.1117 13.1368 14.2243 13.0243C14.3368 12.9117 14.4 12.7591 14.4 12.6C14.4 12.4409 14.3368 12.2883 14.2243 12.1757C14.1117 12.0632 13.9591 12 13.8 12H7.8ZM3.6 0C2.64522 0 1.72955 0.379285 1.05442 1.05442C0.379285 1.72955 0 2.64522 0 3.6V13.2C0 14.1548 0.379285 15.0705 1.05442 15.7456C1.72955 16.4207 2.64522 16.8 3.6 16.8H13.2C14.1548 16.8 15.0705 16.4207 15.7456 15.7456C16.4207 15.0705 16.8 14.1548 16.8 13.2V3.6C16.8 2.64522 16.4207 1.72955 15.7456 1.05442C15.0705 0.379285 14.1548 0 13.2 0H3.6ZM1.2 3.6C1.2 2.96348 1.45286 2.35303 1.90294 1.90294C2.35303 1.45286 2.96348 1.2 3.6 1.2H13.2C13.8365 1.2 14.447 1.45286 14.8971 1.90294C15.3471 2.35303 15.6 2.96348 15.6 3.6V13.2C15.6 13.8365 15.3471 14.447 14.8971 14.8971C14.447 15.3471 13.8365 15.6 13.2 15.6H3.6C2.96348 15.6 2.35303 15.3471 1.90294 14.8971C1.45286 14.447 1.2 13.8365 1.2 13.2V3.6Z" fill="#7F7F7F"/>
                            </svg>
                            <span>{{ trans('service_ticket.service_detail.view_form') }}</span>
                        </button>
                    </a>
                @endif
            </div>
        </div>
        <main class="main-content" id="mainContent">
            <div class="srd-body row g-3 p-3 pb-5">
                {{-- ═══ LEFT ═══ --}}
                <div class="srd-left col-12 col-xl">
                    <div class="srd-card">
                        {{-- INFO --}}
                        <div class="srd-info">
                            <div class="d-flex align-items-start justify-content-between gap-3 mb-2">
                                <div class="flex-grow-1">
                                    <h2 class="srd-req-h mb-2">
                                        {{ $request->decodedSubject() }}
                                    </h2>
                                    {{-- Preview Content --}}
                                    <div class="srd-content-wrapper">
                                        <div class="srd-content-preview" id="srdContentPreview">
                                            @if (CommonHelper::isEmailHtml($request->content))
                                                <iframe
                                                    src="{{ route('ticket.mail.body', [$request->ticket_id, 'request' => $request->id]) }}"
                                                    style="width:100%; min-height:350px; border:1px solid #ddd;">
                                                </iframe>
                                            @else
                                                {!! CommonHelper::renderTktContent($request->content, $embedded_attachments) !!}
                                            @endif
                                            <div class="main_attachments attachments mt-2"></div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Expand / Collapse --}}
                                <button type="button" class="btn btn-sm border-0 p-0 flex-shrink-0" id="srd-expand-btn" data-bs-toggle="tooltip" title="{{  trans('service_ticket.expand/collapse')}}">
                                    <svg id="expandIcon" width="15" height="15" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <polyline points="15 3 21 3 21 9"></polyline>
                                        <polyline points="9 21 3 21 3 15"></polyline>
                                        <line x1="21" y1="3" x2="14" y2="10"></line>
                                        <line x1="3" y1="21" x2="10" y2="14"></line>
                                    </svg>
                                </button>
                            </div>
                            {{-- Bordered box: status + meta --}}
                            <div>
                                <div class="d-flex flex-wrap gap-2">
                                    <div class="srd-badge srd-badge-success">
                                        <span class="b5-text fw-medium">{{ trans('service_ticket.service_detail.status') }}</span>
                                        <div class="d-flex align-items-center gap-1">
                                            <svg width="16" height="15" viewBox="0 0 16 15" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M0 7.5L7.91667 0L15.8333 7.5H10.8333V15H5V7.5H0Z" fill="#186B43" />
                                            </svg>
                                            <span class="b5-text">{{ $request->status->name }}</span>
                                        </div>
                                    </div>
                                    {{-- CREATED BY --}}
                                    <div class="srd-badge srd-badge-light">
                                        <span class="b5-text fw-medium">{{ trans('service_ticket.service_detail.created_by') }}</span>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="srd-avatar">
                                                <img src="{{ $creator->getProfileImg() }}"
                                                    alt="{{ $creator->fullName() }}">
                                            </div>
                                            <span class="b5-text">
                                                {{ $creator->fullName() }}
                                            </span>

                                        </div>
                                    </div>
                                    {{-- ASSIGNED --}}
                                    @if ($request->assigned_to != '' && $request->assigned_to > 0)
                                        <div class="srd-badge srd-badge-light">
                                            <span class="b5-text fw-medium">{{ trans('service_ticket.service_detail.assigned_to') }}</span>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="srd-avatar">
                                                    <img src="{{ $assigned_to->getProfileImg() }}"
                                                        alt="{{ $assigned_to->fullName() }}">
                                                </div>
                                                <span class="b5-text">{{ $assigned_to->fullName() }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Details --}}
                                <div class="p-3">
                                    {{-- Company --}}
                                    @if ($request->company)
                                        <div class="row align-items-center mb-2">

                                            <div class="col-md-5 col-6">
                                                <div class="d-flex align-items-center gap-2 text-dark small fw-medium">

                                                    <svg width="16" height="16" viewBox="0 0 24 24"
                                                        fill="none" stroke="#8b8b8b" stroke-width="1.8">
                                                        <rect x="2" y="7" width="20" height="14" rx="2" />
                                                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                                                    </svg>

                                                    <span>{{ trans('content.service_ticket_fields.Company') }}</span>

                                                </div>
                                            </div>

                                            <div class="col-md-7 col-6">
                                                <span class="small text-muted" data-toggle="tooltip"
                                                    title="{{ $request->company->name }}">
                                                    {{ \Illuminate\Support\Str::limit($request->department->company->name, 40) }}
                                                </span>
                                            </div>

                                        </div>
                                    @endif

                                    {{-- Department --}}
                                    @if ($request->department)
                                        <div class="row align-items-center mb-2">
                                            <div class="col-md-5 col-6">
                                                <div class="d-flex align-items-center gap-2 text-dark small fw-medium">
                                                    <svg width="16" height="16" viewBox="0 0 24 24"
                                                        fill="none" stroke="#8b8b8b" stroke-width="1.8">
                                                        <rect x="3" y="3" width="7" height="7" />
                                                        <rect x="14" y="3" width="7" height="7" />
                                                        <rect x="3" y="14" width="7" height="7" />
                                                        <rect x="14" y="14" width="7" height="7" />
                                                    </svg>
                                                    <span>{{ trans('content.service_ticket_fields.department') }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-7 col-6">
                                                <span class="small text-muted" title="{{ $request->department->name }}">
                                                    {{ \Illuminate\Support\Str::limit($request->department->name, 40) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Problem Category --}}
                                    @if ($request->problemCategory)
                                        <div class="row align-items-center mb-2">
                                            <div class="col-md-5 col-6">
                                                <div class="d-flex align-items-center gap-2 text-dark small fw-medium">
                                                    <svg width="16" height="16" viewBox="0 0 24 24"
                                                        fill="none" stroke="#8b8b8b" stroke-width="1.8">
                                                        <path
                                                            d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" />
                                                        <line x1="7" y1="7" x2="7.01"
                                                            y2="7" />
                                                    </svg>
                                                    <span>{{ trans('content.service_ticket_fields.Prob_Category') }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-7 col-6">
                                                <span class="small text-muted"
                                                    title="{{ $request->problemCategory->name }}">
                                                    {{ \Illuminate\Support\Str::limit($request->problemCategory->name, 40) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Sub Category --}}
                                    @if ($request->sub_category_id && $request->subCategory)
                                        <div class="row align-items-center mb-2">
                                            <div class="col-md-5 col-6">
                                                <div class="d-flex align-items-center gap-2 text-dark small fw-medium">
                                                    <svg width="16" height="16" viewBox="0 0 24 24"
                                                        fill="none" stroke="#8b8b8b" stroke-width="1.8">
                                                        <polygon points="12 2 2 7 12 12 22 7 12 2" />
                                                        <polyline points="2 17 12 22 22 17" />
                                                        <polyline points="2 12 12 17 22 12" />
                                                    </svg>
                                                    <span>{{ trans('content.service_ticket_fields.sub_category') }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-7 col-6">
                                                <span class="small text-muted" title="{{ $request->subCategory->name }}">
                                                    {{ \Illuminate\Support\Str::limit($request->subCategory->name, 40) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Location --}}
                                    @if ($request->location)
                                        <div class="row align-items-center mb-2">
                                            <div class="col-md-5 col-6">
                                                <div class="d-flex align-items-center gap-2 text-dark small fw-medium">
                                                    <svg width="16" height="16" viewBox="0 0 24 24"
                                                        fill="none" stroke="#8b8b8b" stroke-width="1.8">
                                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                                        <circle cx="12" cy="10" r="3" />
                                                    </svg>
                                                    <span>{{ trans('content.user_fields.location') }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-7 col-6">
                                                <span class="small text-muted">
                                                    {{ $request->location->name }}
                                                </span>
                                            </div>

                                        </div>
                                    @endif

                                    {{-- Related Device --}}
                                    @if ($device)
                                        <div class="row align-items-center mb-2">

                                            <div class="col-md-5 col-6">
                                                <div class="d-flex align-items-center gap-2 text-dark small fw-medium">

                                                    <svg width="16" height="16" viewBox="0 0 24 24"
                                                        fill="none" stroke="#8b8b8b" stroke-width="1.8">
                                                        <rect x="4" y="3" width="16" height="12" rx="2" />
                                                        <path d="M8 21h8" />
                                                        <path d="M12 15v6" />
                                                    </svg>

                                                    <span>{{ trans('content.service_ticket_fields.Related_Device') }}</span>

                                                </div>
                                            </div>

                                            <div class="col-md-7 col-6">
                                                <span class="small text-muted">

                                                    @hasanyrole('SuperAdmin|Admin')
                                                        <a class="text-primary" target="_blank"
                                                            href="{{ url('device/info') }}/{{ $device->id }}">
                                                            {{ $device->asset_tag }}
                                                        </a>
                                                    @else
                                                        {{ $device->asset_tag }}
                                                    @endhasanyrole

                                                </span>
                                            </div>

                                        </div>
                                    @endif

                                    {{-- Seat No --}}
                                    @if (
                                        (config('app.client') == 'ril' || config('app.client') == 'rolepermission') &&
                                            optional($request->ticketDetail)->seat_no)
                                        <div class="row align-items-center mb-2">

                                            <div class="col-md-5 col-6">
                                                <div class="d-flex align-items-center gap-2 text-dark small fw-medium">

                                                    <svg width="16" height="16" viewBox="0 0 24 24"
                                                        fill="none" stroke="#8b8b8b" stroke-width="1.8">
                                                        <rect x="3" y="5" width="18" height="14" rx="2" />
                                                        <path d="M7 9h10" />
                                                    </svg>

                                                    <span>{{ trans('service_ticket.service_detail.seat_no') }}</span>

                                                </div>
                                            </div>

                                            <div class="col-md-7 col-6">
                                                <span class="small text-muted">
                                                    {{ optional($request->ticketDetail)->seat_no }}
                                                </span>
                                            </div>

                                        </div>
                                    @endif

                                    {{-- Old SR --}}
                                    @if (!empty($request->old_ticket_ref))
                                        <div class="row align-items-center mb-2">

                                            <div class="col-md-5 col-6">
                                                <div class="d-flex align-items-center gap-2 text-dark small fw-medium">
                                                    <span>{{ trans('service_ticket.service_detail.old_sr_reference') }}</span>
                                                </div>
                                            </div>

                                            <div class="col-md-7 col-6">
                                                <a href="{{ url('tickets/requestInfo', $request->old_ticket_ref) }}"
                                                    target="_blank" class="small text-primary">
                                                    {{ $request->old_ticket_ref }}
                                                </a>
                                            </div>

                                        </div>
                                    @endif

                                    {{-- New SR --}}
                                    @if (!empty($request->new_ticket_reference))
                                        <div class="row align-items-center mb-2">

                                            <div class="col-md-5 col-6">
                                                <div class="d-flex align-items-center gap-2 text-dark small fw-medium">
                                                    <span>{{ trans('service_ticket.service_detail.new_sr_reference') }}</span>
                                                </div>
                                            </div>

                                            <div class="col-md-7 col-6">
                                                <a href="{{ url('tickets/requestInfo', $request->new_ticket_reference) }}"
                                                    target="_blank" class="small text-muted">
                                                    {{ $request->new_ticket_reference }}
                                                </a>
                                            </div>

                                        </div>
                                    @endif

                                    {{-- Updated At --}}
                                    <div class="row align-items-center mb-2">

                                        <div class="col-md-5 col-6">
                                            <div class="d-flex align-items-center gap-2 text-dark small fw-medium">

                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                    stroke="#8b8b8b" stroke-width="1.8">
                                                    <circle cx="12" cy="12" r="10" />
                                                    <polyline points="12 6 12 12 16 14" />
                                                </svg>

                                                <span>{{ trans('content.common_doc_list.updated_at') }}</span>

                                            </div>
                                        </div>

                                        <div class="col-md-7 col-6">
                                            <span class="small text-muted">
                                                {{ CommonHelper::getDateAs($request->updated_at, 'd/m/Y h:i A', 'Y-m-d H:i:s') }}
                                            </span>
                                        </div>

                                    </div>

                                    {{-- Created At --}}
                                    <div class="row align-items-center mb-2">

                                        <div class="col-md-5 col-6">
                                            <div class="d-flex align-items-center gap-2 text-dark small fw-medium">

                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                    stroke="#8b8b8b" stroke-width="1.8">
                                                    <rect x="3" y="4" width="18" height="18" rx="2" />
                                                    <line x1="16" y1="2" x2="16" y2="6" />
                                                    <line x1="8" y1="2" x2="8" y2="6" />
                                                    <line x1="3" y1="10" x2="21" y2="10" />
                                                </svg>

                                                <span>{{ trans('content.service_ticket_fields.Created_At') }}</span>

                                            </div>
                                        </div>

                                        <div class="col-md-7 col-6">
                                            <span class="small text-muted">
                                                {{ CommonHelper::getDateAs($request->created_at, 'd/m/Y h:i A', 'Y-m-d H:i:s') }}
                                            </span>
                                        </div>

                                    </div>
                                    @if (isset($customFieldsFromTableForDepartments))
                                        <div class="mt-3">

                                            @foreach ($customFieldsFromTableForDepartments as $field)
                                                @if ($field['value'] != null)
                                                    <div class="row align-items-center mb-2">

                                                        {{-- Label --}}
                                                        <div class="col-md-5 col-6">
                                                            <div
                                                                class="d-flex align-items-center gap-2 text-dark small fw-medium">

                                                                <svg width="16" height="16" viewBox="0 0 24 24"
                                                                    fill="none" stroke="#8b8b8b" stroke-width="1.8">
                                                                    <rect x="3" y="3" width="18" height="18"
                                                                        rx="2" />
                                                                    <path d="M8 12h8" />
                                                                    <path d="M8 8h8" />
                                                                    <path d="M8 16h5" />
                                                                </svg>

                                                                <span>{{ $field['column'] }}</span>

                                                            </div>
                                                        </div>

                                                        {{-- Value --}}
                                                        <div class="col-md-7 col-6">
                                                            <span class="small text-muted">
                                                                {{ $field['value'] }}
                                                            </span>
                                                        </div>

                                                    </div>
                                                @endif
                                            @endforeach

                                        </div>
                                    @endif

                                </div>
                            </div>{{-- /srd-ibox --}}
                        </div>{{-- /srd-info --}}

                        {{-- TABS --}}
                        <div class="tab-bar position-relative">

                            <ul class="nav nav-underline pe-5" id="myTab" role="tablist">

                                <li class="nav-item border-0">
                                    <a class="nav-link active" id="approval-tab" data-bs-toggle="tab"
                                        href="#srd-panel-approvals" role="tab" aria-expanded="true">

                                        <span>{{ trans('service_ticket.service_detail.approvals') }}</span>

                                    </a>
                                </li>

                                <li class="nav-item border-0">
                                    <a class="nav-link" id="conversations-tab" data-bs-toggle="tab"
                                        href="#srd-panel-conversations" role="tab" aria-controls="link1">

                                        <span>{{ trans('service_ticket.service_detail.conversations') }}</span>

                                    </a>
                                </li>

                            </ul>

                            {{-- Expand / Collapse --}}
                            <button type="button" class="srd-expand-btn btn btn-sm border-0 p-0" data-bs-toggle="tooltip" title="{{  trans('service_ticket.expand/collapse')}}">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">

                                    <polyline points="15 3 21 3 21 9"></polyline>
                                    <polyline points="9 21 3 21 3 15"></polyline>

                                    <line x1="21" y1="3" x2="14" y2="10"></line>
                                    <line x1="3" y1="21" x2="10" y2="14"></line>

                                </svg>

                            </button>

                        </div>

                        {{-- APPROVALS --}}
                        <div class="srd-panel active" id="srd-panel-approvals">
                            @php
                                use App\Models\Ticket\TicketPab;

                                $pabIdArray = json_decode($request->pab_id, true);
                                $pabIds = array_keys($pabIdArray ?? []);
                                $pabsData = TicketPab::whereIn('id', $pabIds)->get()->keyBy('id');

                                $firstPabId = reset($pabIds);

                                $isFirstHierarchyEight =
                                    isset($pabsData[$firstPabId]) && $pabsData[$firstPabId]->hierarchy_approval == 8;
                            @endphp
                            @if ($systemApproval == true && $isFirstHierarchyEight == true)
                                <div class="mb-4">

                                    <div class="d-flex align-items-center fw-semibold mb-3">
                                        System Approval
                                    </div>

                                    <div class="d-flex flex-wrap gap-2 mb-3">

                                        {{-- User --}}
                                        <div class="srd-badge srd-badge-light">
                                            <div class="d-flex align-items-center gap-2">

                                                <div class="srd-avatar">
                                                    <div class="d-flex align-items-center justify-content-center rounded-circle"
                                                        style="width:32px;height:32px;background:#186B43;color:#fff;font-size:12px;">
                                                        S
                                                    </div>
                                                </div>

                                                <span class="b5-text">
                                                    System
                                                </span>

                                            </div>
                                        </div>

                                        {{-- Status --}}
                                        <div class="srd-badge srd-badge-success">

                                            <span class="b5-text fw-medium">
                                                {{ trans('service_ticket.service_detail.status') }}
                                            </span>

                                            <div class="d-flex align-items-center gap-1">

                                                <svg width="16" height="15" viewBox="0 0 16 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M0 7.5L7.91667 0L15.8333 7.5H10.8333V15H5V7.5H0Z"
                                                        fill="#186B43" />
                                                </svg>

                                                <span class="b5-text">
                                                    Approved
                                                </span>

                                            </div>

                                        </div>

                                        {{-- Date --}}
                                        <div class="srd-badge srd-badge-light">

                                            <span class="b5-text fw-medium">
                                               {{ trans('service_ticket.service_detail.date') }}
                                            </span>

                                            <div class="d-flex align-items-center gap-2">

                                                <svg width="14" height="14" viewBox="0 0 20 20"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M16.25 2.5H14.375V1.875C14.375 1.70924 14.3092 1.55027 14.1919 1.43306C14.0747 1.31585 13.9158 1.25 13.75 1.25C13.5842 1.25 13.4253 1.31585 13.3081 1.43306C13.1908 1.55027 13.125 1.70924 13.125 1.875V2.5H6.875V1.875C6.875 1.70924 6.80915 1.55027 6.69194 1.43306C6.57473 1.31585 6.41576 1.25 6.25 1.25C6.08424 1.25 5.92527 1.31585 5.80806 1.43306C5.69085 1.55027 5.625 1.70924 5.625 1.875V2.5H3.75C3.41848 2.5 3.10054 2.6317 2.86612 2.86612C2.6317 3.10054 2.5 3.41848 2.5 3.75V16.25C2.5 16.5815 2.6317 16.8995 2.86612 17.1339C3.10054 17.3683 3.41848 17.5 3.75 17.5H16.25C16.5815 17.5 16.8995 17.3683 17.1339 17.1339C17.3683 16.8995 17.5 16.5815 17.5 16.25V3.75C17.5 3.41848 17.3683 3.10054 17.1339 2.86612C16.8995 2.6317 16.5815 2.5 16.25 2.5ZM5.625 3.75V4.375C5.625 4.54076 5.69085 4.69973 5.80806 4.81694C5.92527 4.93415 6.08424 5 6.25 5C6.41576 5 6.57473 4.93415 6.69194 4.81694C6.80915 4.69973 6.875 4.54076 6.875 4.375V3.75H13.125V4.375C13.125 4.54076 13.1908 4.69973 13.3081 4.81694C13.4253 4.93415 13.5842 5 13.75 5C13.9158 5 14.0747 4.93415 14.1919 4.81694C14.3092 4.69973 14.375 4.54076 14.375 4.375V3.75H16.25V6.25H3.75V3.75H5.625ZM16.25 16.25H3.75V7.5H16.25V16.25ZM11.25 11.875C11.25 12.1222 11.1767 12.3639 11.0393 12.5695C10.902 12.775 10.7068 12.9352 10.4784 13.0299C10.2499 13.1245 9.99861 13.1492 9.75614 13.101C9.51366 13.0528 9.29093 12.9337 9.11612 12.7589C8.9413 12.5841 8.82225 12.3613 8.77402 12.1189C8.72579 11.8764 8.75054 11.6251 8.84515 11.3966C8.93976 11.1682 9.09998 10.973 9.30554 10.8357C9.5111 10.6983 9.75277 10.625 10 10.625C10.3315 10.625 10.6495 10.7567 10.8839 10.9911C11.1183 11.2255 11.25 11.5435 11.25 11.875Z"
                                                        fill="#7F7F7F" />
                                                </svg>

                                                <span class="b5-text">
                                                    {{ date('d M Y h:i a', strtotime($request->approved_at ?: $request->created_at)) }}
                                                </span>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="border-top my-4"></div>

                                </div>
                            @endif

                            @if ($approval_requests && count($approval_requests) && isset($vd->json_pab) && !empty($vd->json_pab))

                                @foreach ($pabs as $pab)
                                    @foreach ($vd->json_pab as $key => $status)
                                        @foreach ($approval_requests as $ar)
                                            @if ($ar->pab_id == $pab->id && $ar->pab_id == $key && (!isset($pabsData[$ar->pab_id]) || $pabsData[$ar->pab_id]->hierarchy_approval != 8))
                                                @php

                                                    $statusClass = match ($ar->approve_status) {
                                                        1 => 'srd-badge-success',
                                                        2 => 'srd-badge-danger',
                                                        default => 'srd-badge-info',
                                                    };

                                                    $statusText = $ar->statusLabel();

                                                    $userName = optional($ar->user)->fullName();

                                                    $initials = optional($ar->user)->getProfileImg();

                                                    $statusIcon = match ($ar->approve_status) {
                                                        // Approved
                                                        1 => '
                                                            <svg width="16" height="15" viewBox="0 0 16 15" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M0 7.5L7.91667 0L15.8333 7.5H10.8333V15H5V7.5H0Z"
                                                                    fill="#186B43"/>
                                                            </svg>
                                                        ',
                                                        // Rejected
                                                        2 => '
                                                            <svg width="16" height="15" viewBox="0 0 16 15" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M15.8333 7.5L7.91667 15L0 7.5H5V0H10.8333V7.5H15.8333Z"
                                                                    fill="#DC3545"/>
                                                            </svg>
                                                        ',
                                                        // Pending / Requested
                                                        default => '
                                                            <svg width="16" height="15" viewBox="0 0 16 15" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M0 7.5L7.91667 0L15.8333 7.5H10.8333V15H5V7.5H0Z"
                                                                    fill="#0035E5"/>
                                                            </svg>
                                                        ',
                                                    };

                                                @endphp

                                                <div class="mb-4">

                                                    {{-- Heading --}}
                                                    <div class="d-flex align-items-center fw-semibold mb-3">
                                                        {{ $pab->name }}
                                                        ({{ $pab->getApprovalModeName() }})
                                                    </div>

                                                    {{-- Meta --}}
                                                    <div class="d-flex flex-wrap gap-2 mb-3">

                                                        {{-- User --}}
                                                        <div class="srd-badge srd-badge-light">

                                                            <div class="d-flex align-items-center gap-2">

                                                                <div class="srd-avatar">
                                                                    <img src="{{ $initials }}"
                                                                        alt="{{ $userName }}">
                                                                </div>

                                                                <span class="b5-text">
                                                                    {{ $userName }}
                                                                </span>

                                                            </div>

                                                        </div>

                                                        {{-- Delegated --}}
                                                        @if (isset($ar->delegatedUser->username))
                                                            <div class="srd-badge srd-badge-light">

                                                                <span class="b5-text fw-medium">
                                                                    {{ trans('service_ticket.service_detail.delegated_to') }}
                                                                </span>

                                                                <div class="d-flex align-items-center gap-2">

                                                                    <svg width="14" height="14"
                                                                        viewBox="0 0 24 24" fill="none">
                                                                        <path
                                                                            d="M16 21V19C16 17.9391 15.5786 16.9217 14.8284 16.1716C14.0783 15.4214 13.0609 15 12 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21"
                                                                            stroke="#7F7F7F" stroke-width="2" />
                                                                        <circle cx="8.5" cy="7" r="4"
                                                                            stroke="#7F7F7F" stroke-width="2" />
                                                                    </svg>

                                                                    <span class="b5-text">
                                                                        {{ optional($ar->delegatedUser)->delegatedUserName() }}
                                                                    </span>

                                                                </div>

                                                            </div>
                                                        @elseif ($ar->delegated_user_id === 0 && in_array(config('app.client'), ['rolepermission', 'ltts']))
                                                            <div class="srd-badge srd-badge-light">

                                                                <span class="b5-text fw-medium">
                                                                    {{ trans('service_ticket.service_detail.delegated_to') }}
                                                                </span>

                                                                <span class="b5-text">
                                                                    System
                                                                </span>

                                                            </div>
                                                        @endif

                                                        {{-- Status --}}
                                                        <div class="srd-badge {{ $statusClass }}">

                                                            <span class="b5-text fw-medium">
                                                               {{ trans('service_ticket.service_detail.status') }}
                                                            </span>

                                                            <div class="d-flex align-items-center gap-1">

                                                                {!! $statusIcon !!}

                                                                <span class="b5-text">
                                                                    {{ $statusText }}
                                                                </span>

                                                            </div>

                                                        </div>
                                                        {{-- Date --}}
                                                        <div class="srd-badge srd-badge-light">

                                                            <span class="b5-text fw-medium">
                                                               {{ trans('service_ticket.service_detail.date') }}
                                                            </span>

                                                            <div class="d-flex align-items-center gap-2">

                                                                <svg width="14" height="14" viewBox="0 0 20 20"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path
                                                                        d="M16.25 2.5H14.375V1.875C14.375 1.70924 14.3092 1.55027 14.1919 1.43306C14.0747 1.31585 13.9158 1.25 13.75 1.25C13.5842 1.25 13.4253 1.31585 13.3081 1.43306C13.1908 1.55027 13.125 1.70924 13.125 1.875V2.5H6.875V1.875C6.875 1.70924 6.80915 1.55027 6.69194 1.43306C6.57473 1.31585 6.41576 1.25 6.25 1.25C6.08424 1.25 5.92527 1.31585 5.80806 1.43306C5.69085 1.55027 5.625 1.70924 5.625 1.875V2.5H3.75C3.41848 2.5 3.10054 2.6317 2.86612 2.86612C2.6317 3.10054 2.5 3.41848 2.5 3.75V16.25C2.5 16.5815 2.6317 16.8995 2.86612 17.1339C3.10054 17.3683 3.41848 17.5 3.75 17.5H16.25C16.5815 17.5 16.8995 17.3683 17.1339 17.1339C17.3683 16.8995 17.5 16.5815 17.5 16.25V3.75C17.5 3.41848 17.3683 3.10054 17.1339 2.86612C16.8995 2.6317 16.5815 2.5 16.25 2.5ZM5.625 3.75V4.375C5.625 4.54076 5.69085 4.69973 5.80806 4.81694C5.92527 4.93415 6.08424 5 6.25 5C6.41576 5 6.57473 4.93415 6.69194 4.81694C6.80915 4.69973 6.875 4.54076 6.875 4.375V3.75H13.125V4.375C13.125 4.54076 13.1908 4.69973 13.3081 4.81694C13.4253 4.93415 13.5842 5 13.75 5C13.9158 5 14.0747 4.93415 14.1919 4.81694C14.3092 4.69973 14.375 4.54076 14.375 4.375V3.75H16.25V6.25H3.75V3.75H5.625ZM16.25 16.25H3.75V7.5H16.25V16.25ZM11.25 11.875C11.25 12.1222 11.1767 12.3639 11.0393 12.5695C10.902 12.775 10.7068 12.9352 10.4784 13.0299C10.2499 13.1245 9.99861 13.1492 9.75614 13.101C9.51366 13.0528 9.29093 12.9337 9.11612 12.7589C8.9413 12.5841 8.82225 12.3613 8.77402 12.1189C8.72579 11.8764 8.75054 11.6251 8.84515 11.3966C8.93976 11.1682 9.09998 10.973 9.30554 10.8357C9.5111 10.6983 9.75277 10.625 10 10.625C10.3315 10.625 10.6495 10.7567 10.8839 10.9911C11.1183 11.2255 11.25 11.5435 11.25 11.875Z"
                                                                        fill="#7F7F7F" />
                                                                </svg>

                                                                <span class="b5-text">
                                                                    {{ CommonHelper::getDateAs($ar->updated_at, 'd M, h:i A', 'Y-m-d H:i:s') }}
                                                                </span>

                                                            </div>

                                                        </div>

                                                        {{-- Days --}}
                                                        @if (isset($request->approved_day) && $request->approved_day != null && $ar->approve_status == 1)
                                                            <div class="srd-badge srd-badge-light">

                                                                <span class="b5-text fw-medium">
                                                                    {{ trans('service_ticket.service_detail.days') }}
                                                                </span>

                                                                <span class="b5-text">
                                                                    {{ $request->approved_day }} days
                                                                </span>

                                                            </div>
                                                        @endif

                                                    </div>

                                                    {{-- Comment --}}
                                                    @if ($ar->comments)
                                                        <div class="small fw-semibold mb-2 approval-comment">
                                                            {{ trans('service_ticket.service_detail.commented_by') }} {{ $userName }}
                                                        </div>

                                                        <div
                                                            class="border px-3 py-2 small d-flex align-items-center gap-2 srd-cbox approval-comment-box">
                                                            <svg width="24" height="26" viewBox="0 0 24 26"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M3.5 18.5H14.5V17.5H3.5V18.5ZM3.5 15.5H14.5V14.5H3.5V15.5ZM3.5 12.5H14.5V11.5H3.5V12.5ZM18 25.077L14.923 22H1.616C1.15534 22 0.770669 21.846 0.462002 21.538C0.153335 21.23 -0.000664511 20.8457 2.15517e-06 20.385V9.615C2.15517e-06 9.155 0.154336 8.77067 0.463002 8.462C0.771669 8.15334 1.15567 7.99934 1.615 8H16.385C16.845 8 17.229 8.154 17.537 8.462C17.845 8.77 17.9993 9.15434 18 9.615V25.077ZM1.616 21H15.35L17 22.644V9.616C17 9.462 16.936 9.32067 16.808 9.192C16.68 9.06334 16.539 8.99934 16.385 9H1.615C1.46167 9 1.32067 9.064 1.192 9.192C1.06334 9.32 0.999335 9.461 1 9.615V20.385C1 20.5383 1.064 20.6793 1.192 20.808C1.32 20.9367 1.461 21.0007 1.615 21"
                                                                    fill="#7F7F7F" />
                                                                <g clip-path="url(#clip0_5390_94102)">
                                                                    <path
                                                                        d="M17.4746 6.34904L16.7496 5.63654C16.6579 5.54488 16.5434 5.49904 16.4061 5.49904C16.2688 5.49904 16.1499 5.54904 16.0496 5.64904C15.9579 5.74071 15.9121 5.85738 15.9121 5.99904C15.9121 6.14071 15.9579 6.25738 16.0496 6.34904L17.1246 7.42404C17.2246 7.52404 17.3413 7.57404 17.4746 7.57404C17.6079 7.57404 17.7246 7.52404 17.8246 7.42404L19.9496 5.29904C20.0496 5.19904 20.0974 5.08238 20.0931 4.94904C20.0888 4.81571 20.0409 4.69904 19.9496 4.59904C19.8496 4.49904 19.7309 4.44704 19.5936 4.44304C19.4563 4.43904 19.3374 4.48688 19.2371 4.58654L17.4746 6.34904ZM16.0746 10.874L15.3496 9.64904L13.9746 9.34904C13.8496 9.32404 13.7496 9.25954 13.6746 9.15554C13.5996 9.05154 13.5704 8.93688 13.5871 8.81154L13.7246 7.39904L12.7871 6.32404C12.7038 6.23238 12.6621 6.12404 12.6621 5.99904C12.6621 5.87404 12.7038 5.76571 12.7871 5.67404L13.7246 4.59904L13.5871 3.18654C13.5704 3.06154 13.5996 2.94688 13.6746 2.84254C13.7496 2.73821 13.8496 2.67371 13.9746 2.64904L15.3496 2.34904L16.0746 1.12404C16.1413 1.01571 16.2329 0.94271 16.3496 0.905043C16.4663 0.867376 16.5829 0.87371 16.6996 0.924043L17.9996 1.47404L19.2996 0.924043C19.4163 0.874043 19.5329 0.86771 19.6496 0.905043C19.7663 0.942376 19.8579 1.01538 19.9246 1.12404L20.6496 2.34904L22.0246 2.64904C22.1496 2.67404 22.2496 2.73871 22.3246 2.84304C22.3996 2.94738 22.4288 3.06188 22.4121 3.18654L22.2746 4.59904L23.2121 5.67404C23.2954 5.76571 23.3371 5.87404 23.3371 5.99904C23.3371 6.12404 23.2954 6.23238 23.2121 6.32404L22.2746 7.39904L22.4121 8.81154C22.4288 8.93654 22.3996 9.05121 22.3246 9.15554C22.2496 9.25988 22.1496 9.32438 22.0246 9.34904L20.6496 9.64904L19.9246 10.874C19.8579 10.9824 19.7663 11.0554 19.6496 11.093C19.5329 11.1307 19.4163 11.1244 19.2996 11.074L17.9996 10.524L16.6996 11.074C16.5829 11.124 16.4663 11.1304 16.3496 11.093C16.2329 11.0557 16.1413 10.9827 16.0746 10.874Z"
                                                                        fill="#186B43" />
                                                                </g>
                                                                <defs>
                                                                    <clipPath id="clip0_5390_94102">
                                                                        <rect width="12" height="12"
                                                                            fill="white" transform="translate(12)" />
                                                                    </clipPath>
                                                                </defs>
                                                            </svg>
                                                                {{ html_entity_decode(strip_tags($ar->comments)) }}
                                                        </div>
                                                    @endif

                                                    {{-- Modify --}}
                                                    @if ($ar->canRevokeDecision(Auth::user()->id))
                                                        <div class="mt-3">

                                                            <a href="#"
                                                                class="btn btn-sm border revoke-approval-request-decision"
                                                                data-id="{{ $ar->id }}">

                                                                {{ trans('service_ticket.service_detail.modify_approval') }}

                                                            </a>

                                                        </div>
                                                    @endif

                                                    {{-- Decision Form --}}
                                                    @if (
                                                        $ar->canSendApprovalStatus($type) &&
                                                            $status != 1 &&
                                                            ((isset($ar->delegated_user_id) && $ar->delegated_user_id == Auth::user()->id) ||
                                                                $ar->user_id == Auth::user()->id))
                                                        <div class="mt-4">

                                                            <form id="decide_on_request" name="decide_on_request"
                                                                method="POST" action="">

                                                                @csrf

                                                                <input type="hidden" name="approve_request_id"
                                                                    value="{{ $ar->id }}" />

                                                                <div class="mb-3 col-md-4">

                                                                    <label class="form-label small fw-semibold mandatory">
                                                                        {{ trans('service_ticket.service_detail.status') }}
                                                                    </label>

                                                                    <select name="approve_status" id="approve_status"
                                                                        class="form-select form-select-sm">

                                                                        <option value="1">
                                                                            Approve
                                                                        </option>

                                                                        <option value="2">
                                                                            Reject
                                                                        </option>

                                                                    </select>

                                                                </div>

                                                                <div class="mb-3">

                                                                    <label class="form-label small fw-semibold mandatory">
                                                                        {{ trans('service_ticket.service_detail.message') }}

                                                                    </label>

                                                                    <textarea name="comments" id="comments" class="form-control" rows="5"></textarea>

                                                                    <div id="shows_error_approve" class="error"></div>

                                                                </div>

                                                                @if (in_array(config('app.client'), ['rolepermission', 'ltts', 'grdemo']) && $request->privilege_access == 1)
                                                                    <div class="mb-3">

                                                                        <label
                                                                            class="form-label small fw-semibold mandatory">
                                                                             {{ trans('service_ticket.service_detail.days') }}

                                                                        </label>

                                                                        <input autocomplete="off" class="form-control"
                                                                            name="approved_day"
                                                                            value="{{ $access_requested_day }}"
                                                                            placeholder="Enter a number" />

                                                                    </div>

                                                                    <input type="hidden" name="old_ticket_ref"
                                                                        value="{{ $request->old_ticket_ref }}">
                                                                @endif

                                                                <button id="approve-button"
                                                                    class="amg-btn amg-btn-primary px-5 rounded-2 fw-semibold w-40">
                                                                    {{ trans('service_ticket.button.update') }}

                                                                </button>

                                                            </form>

                                                        </div>
                                                    @endif

                                                    <div class="border-top my-4"></div>

                                                </div>
                                            @endif
                                        @endforeach
                                    @endforeach
                                @endforeach
                            @elseif(!$systemApproval)
                                <div class="text-center py-4 text-muted">

                                   {{ trans('service_ticket.service_detail.no_approval_requests_found') }}

                                </div>

                            @endif

                            @if ($systemApproval == true && $isFirstHierarchyEight == false)
                                <div class="mt-4">
                                    <div class="d-flex align-items-center fw-semibold mb-3">
                                        System Approval
                                    </div>

                                    <div class="d-flex flex-wrap gap-2 mb-3">

                                        {{-- User --}}
                                        <div class="srd-badge srd-badge-light">

                                            <div class="d-flex align-items-center gap-2">

                                                <div class="srd-avatar">
                                                    <div class="d-flex align-items-center justify-content-center rounded-circle"
                                                        style="width:32px;height:32px;background:#186B43;color:#fff;font-size:12px;">
                                                        S
                                                    </div>
                                                </div>

                                                <span class="b5-text">
                                                    System
                                                </span>

                                            </div>

                                        </div>

                                        {{-- Status --}}
                                        <div class="srd-badge srd-badge-success">

                                            <span class="b5-text fw-medium">
                                                {{ trans('service_ticket.service_detail.status') }}
                                            </span>

                                            <div class="d-flex align-items-center gap-1">

                                                <svg width="16" height="15" viewBox="0 0 16 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M0 7.5L7.91667 0L15.8333 7.5H10.8333V15H5V7.5H0Z"
                                                        fill="#186B43" />
                                                </svg>

                                                <span class="b5-text">
                                                    Approved
                                                </span>

                                            </div>

                                        </div>

                                        {{-- Date --}}
                                        <div class="srd-badge srd-badge-light">

                                            <span class="b5-text fw-medium">
                                                {{ trans('service_ticket.service_detail.date') }}
                                            </span>

                                            <div class="d-flex align-items-center gap-2">

                                                <svg width="14" height="14" viewBox="0 0 20 20"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M16.25 2.5H14.375V1.875C14.375 1.70924 14.3092 1.55027 14.1919 1.43306C14.0747 1.31585 13.9158 1.25 13.75 1.25C13.5842 1.25 13.4253 1.31585 13.3081 1.43306C13.1908 1.55027 13.125 1.70924 13.125 1.875V2.5H6.875V1.875C6.875 1.70924 6.80915 1.55027 6.69194 1.43306C6.57473 1.31585 6.41576 1.25 6.25 1.25C6.08424 1.25 5.92527 1.31585 5.80806 1.43306C5.69085 1.55027 5.625 1.70924 5.625 1.875V2.5H3.75C3.41848 2.5 3.10054 2.6317 2.86612 2.86612C2.6317 3.10054 2.5 3.41848 2.5 3.75V16.25C2.5 16.5815 2.6317 16.8995 2.86612 17.1339C3.10054 17.3683 3.41848 17.5 3.75 17.5H16.25C16.5815 17.5 16.8995 17.3683 17.1339 17.1339C17.3683 16.8995 17.5 16.5815 17.5 16.25V3.75C17.5 3.41848 17.3683 3.10054 17.1339 2.86612C16.8995 2.6317 16.5815 2.5 16.25 2.5ZM5.625 3.75V4.375C5.625 4.54076 5.69085 4.69973 5.80806 4.81694C5.92527 4.93415 6.08424 5 6.25 5C6.41576 5 6.57473 4.93415 6.69194 4.81694C6.80915 4.69973 6.875 4.54076 6.875 4.375V3.75H13.125V4.375C13.125 4.54076 13.1908 4.69973 13.3081 4.81694C13.4253 4.93415 13.5842 5 13.75 5C13.9158 5 14.0747 4.93415 14.1919 4.81694C14.3092 4.69973 14.375 4.54076 14.375 4.375V3.75H16.25V6.25H3.75V3.75H5.625ZM16.25 16.25H3.75V7.5H16.25V16.25ZM11.25 11.875C11.25 12.1222 11.1767 12.3639 11.0393 12.5695C10.902 12.775 10.7068 12.9352 10.4784 13.0299C10.2499 13.1245 9.99861 13.1492 9.75614 13.101C9.51366 13.0528 9.29093 12.9337 9.11612 12.7589C8.9413 12.5841 8.82225 12.3613 8.77402 12.1189C8.72579 11.8764 8.75054 11.6251 8.84515 11.3966C8.93976 11.1682 9.09998 10.973 9.30554 10.8357C9.5111 10.6983 9.75277 10.625 10 10.625C10.3315 10.625 10.6495 10.7567 10.8839 10.9911C11.1183 11.2255 11.25 11.5435 11.25 11.875Z"
                                                        fill="#7F7F7F" />
                                                </svg>

                                                <span class="b5-text">
                                                    {{ date('d M Y h:i a', strtotime($request->approved_at ?: $request->created_at)) }}
                                                </span>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="border-top my-4"></div>
                                </div>
                            @endif

                        </div>{{-- /approvals --}}

                        {{-- CONVERSATIONS --}}
                        <div class="srd-panel p-3" id="srd-panel-conversations">

                            {{-- Timeline --}}
                            <div id="ticket_timeline" class="timeline no-bg hide" style="margin-top:10px"></div>

                            {{-- Comment Box --}}
                            @if ($request->showCommentBox == true)
                                <div class="srd-conversation-card">
                                    <form name="frm_comment" id="frmReqComment" action="#" class="form-horizontal">
                                        <input type="hidden" id="id" name="id" value="{{ $request->id }}" />
                                        <input type="hidden" id="tmp_id" name="tmp_id" value="" />

                                        {{-- Message --}}
                                        <div class="mb-3">

                                            <label class="form-label fw-semibold small mandatory">
                                                {{ trans('service_ticket.service_detail.message') }}
                                            </label>

                                            <div class="border rounded overflow-hidden">

                                                <textarea id="comment"
                                                    name="comment"
                                                    class="form-control border-0 shadow-none rounded-0"
                                                    placeholder="{{ trans('content.service_ticket_fields.share_comment') }}"></textarea>

                                            </div>

                                            <div id="shows_error" class="error"></div>

                                        </div>

                                        {{-- Attachment --}}
                                        <div class="mb-3">

                                            <label class="form-label fw-semibold small mb-2">
                                                {{ trans('service_ticket.service_detail.attachment') }}
                                            </label>

                                            <div id="attachment-dropper-cover-comment"
                                                class="amg-uploader"
                                                data-amg-uploader
                                                data-multiple="true"
                                                data-auto-upload="true"
                                                data-max-files="10"
                                                data-max-size="10"
                                                data-accept=".jpg,.jpeg,.png,.gif,.xls,.xlsx,.doc,.docx,.ppt,.pdf,.txt,.msg,.zip,.psd,.csv"
                                                data-upload-url="{{ url('ticket/attachment/add') }}"
                                                data-remove-url="{{ url('ticket/attachment/remove') }}"
                                                data-token="{{ csrf_token() }}"
                                                data-record-input="#frmReqComment #tmp_id"
                                                data-upload-field="attachment"
                                                data-ticket-id="{{ $request->id }}">

                                                {{-- File Input --}}
                                                <input type="file"
                                                    class="amg-uploader__input"
                                                    multiple
                                                    hidden>

                                                {{-- Dropzone --}}
                                                <div class="amg-uploader__dropzone border rounded d-flex flex-column align-items-center justify-content-center text-center srd-attachment-box">

                                                    <div class="small d-flex align-items-center gap-2">

                                                        <svg width="21"
                                                            height="16"
                                                            viewBox="0 0 21 16"
                                                            fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">

                                                            <path
                                                                d="M11.25 15.75V11.25H14.25L10.5 6.75L6.75 11.25H9.75V15.75H6V15.7125C5.87344 15.7195 5.75391 15.75 5.625 15.75C4.13316 15.75 2.70242 15.1574 1.64752 14.1025C0.592632 13.0476 0 11.6168 0 10.125C0.00194455 8.74787 0.511116 7.41972 1.43026 6.39422C2.34941 5.36872 3.61411 4.71774 4.98281 4.56562C5.22893 3.28221 5.91421 2.12454 6.92099 1.2914C7.92776 0.458267 9.19321 0.00166223 10.5 0C11.8072 0.00111796 13.0732 0.457475 14.0805 1.29066C15.0877 2.12384 15.7733 3.28182 16.0195 4.56562C17.3874 4.71881 18.651 5.37026 19.5691 6.39565C20.4873 7.42104 20.9958 8.7486 20.9977 10.125C20.9977 11.6168 20.405 13.0476 19.3501 14.1025C18.2952 15.1574 16.8645 15.75 15.3727 15.75C15.2484 15.75 15.1266 15.7195 14.9977 15.7125V15.75H11.25Z"
                                                                fill="#7F7F7F" />

                                                        </svg>

                                                        <span class="amg-uploader__trigger text-muted">

                                                           {{ trans('service_ticket.service_detail.drag_drop_upload_files') }}

                                                        </span>

                                                    </div>

                                                </div>

                                                {{-- Preview --}}
                                                <div class="amg-uploader__preview mt-2" id="comment_attachments"></div>

                                                {{-- Error --}}
                                                <div class="amg-uploader__error text-danger mt-1"></div>

                                            </div>

                                            <input type="hidden" name="form_type" id="form_type" value="0">

                                            <div class="text-muted mt-1 srd-attachment-note">
                                                {{ trans('service_ticket.service_detail.supported_file_types') }}
                                            </div>
                                            <div class="text-muted mt-1 srd-attachment-note">
                                                {{ trans('service_ticket.service_detail.max_files_size_message') }}
                                            </div>

                                        </div>

                                        {{-- Checkboxes --}}
                                        <div class="mb-3">

                                            {{-- Internal Note --}}
                                            <div class="form-check mb-2">

                                                <input class="form-check-input"
                                                    type="checkbox"
                                                    id="is_note"
                                                    name="is_note"
                                                    value="1" />

                                                <label class="form-check-label text-muted small" for="is_note">

                                                    {{ trans('content.service_ticket_fields.internal_purpose') }}

                                                </label>

                                            </div>

                                            {{-- Back Trail --}}
                                            <div class="form-check mb-2 comment_cc">

                                                <input class="form-check-input"
                                                    type="checkbox"
                                                    id="add_back_trail"
                                                    name="add_back_trail"
                                                    value="1"
                                                    checked="checked" />

                                                <label class="form-check-label text-muted small" for="add_back_trail">

                                                    {{ trans('content.service_ticket_fields.back_trail') }}

                                                </label>

                                            </div>

                                            {{-- CC Emails --}}
                                            <div class="form-check mb-2 comment_cc">

                                                <input class="form-check-input"
                                                    type="checkbox"
                                                    id="follow_cc"
                                                    name="follow_cc"
                                                    value="1" />

                                                <label class="form-check-label text-muted small" for="follow_cc">

                                                    {{ trans('content.service_ticket_fields.CC_EMails') }}

                                                </label>

                                            </div>

                                            {{-- CC Textarea --}}
                                            <div class="comment_cc mt-2">

                                                <textarea name="cc_emails" id="cc_emails" class="form-control textarea-s" rows="3">{{ $request->cc_emails }}</textarea>

                                            </div>

                                        </div>

                                        {{-- Button --}}
                                        <button class="amg-btn amg-btn-primary px-5 rounded-2 fw-semibold w-40"
                                            id="commentButton">

                                            {{ trans('content.service_ticket_fields.Comment') }}

                                        </button>

                                    </form>

                                </div>

                            @endif
                        </div>{{-- /conversations --}}

                    </div>{{-- /srd-card --}}
                </div>{{-- /srd-left --}}

                {{-- ═══ RIGHT SIDEBAR ═══ --}}
                <div class="srd-right col-12 col-xl-auto">

                    {{-- Update Status --}}
                    @if ($approval_requests && count($approval_requests) && isset($vd->json_pab) && !empty($vd->json_pab))

                        @foreach ($vd->json_pab as $pabId => $status)
                            @php
                                $shouldShowForm = false;

                                if ($status != 1) {
                                    $requestsForThisPab = $approval_requests->where('pab_id', $pabId);

                                    $hierarchyApproval = $requestsForThisPab->first()->hierarchy_approval ?? null;

                                    if ($requestsForThisPab->isNotEmpty()) {
                                        if ($hierarchyApproval != 1) {
                                            $shouldShowForm = $requestsForThisPab->contains('user_id', Auth::id());
                                        } else {
                                            $sortedRequests = $requestsForThisPab->sortBy('level')->values();

                                            foreach ($sortedRequests as $req) {
                                                if ($req->approve_status != 1) {
                                                    if ($req->user_id == Auth::id()) {
                                                        $shouldShowForm = true;
                                                    }
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                            @endphp

                            @if ($vd->form_visible->tr_update_form && $shouldShowForm)
                                <div class="card rounded overflow-hidden">

                                    {{-- Header --}}
                                    <div class="px-4 py-3 text-white fw-semibold fs-3 srd-update-header">
                                        {{ trans('service_ticket.service_detail.update_status') }}
                                    </div>

                                    {{-- Body --}}
                                    <div class="p-4">

                                        <form id="tkt_reuqest_update_status_form" name="tkt_reuqest_update_status_form"
                                            method="POST" action="#">

                                            @csrf

                                            <input type="hidden" id="id" name="id"
                                                value="{{ $request->id }}" />
                                            <input type="hidden" id="tmp_id" name="tmp_id" value="" />

                                            {{-- Status --}}
                                            <div class="mb-3">

                                                <label for="status_id" class="form-label fw-semibold mb-2 mandatory">
                                                    {{ trans('service_ticket.service_detail.status') }}
                                                </label>

                                                <select name="status_id" id="status_id" class="form-select">
                                                    @foreach ($vd->statuses as $s)
                                                        <option value="{{ $s->id }}"
                                                            {{ $request->status_id == $s->id ? 'selected' : '' }}>

                                                            {{ $s->name }}

                                                        </option>
                                                    @endforeach

                                                </select>

                                            </div>

                                            {{-- Message --}}
                                            <div class="mb-3">

                                                <label class="form-label fw-semibold small mandatory">
                                                    {{ trans('service_ticket.service_detail.message') }}
                                                </label>

                                                <textarea name="comment" id="comment" class="form-control"
                                                    placeholder="{{ trans('content.service_ticket_fields.placeholder_comment') }}"></textarea>

                                                <div id="shows_error" class="error"></div>

                                            </div>

                                            {{-- Attachment --}}
                                            <div class="mb-3">

                                                <label class="form-label fw-semibold small mb-2">
                                                   {{ trans('service_ticket.service_detail.attachment') }}
                                                </label>


                                                <div id="attachment-dropper-cover-update"
                                                    class="amg-uploader"
                                                    data-amg-uploader
                                                    data-multiple="true"
                                                    data-auto-upload="true"
                                                    data-max-files="10"
                                                    data-max-size="10"
                                                    data-accept=".jpg,.jpeg,.png,.gif,.xls,.xlsx,.doc,.docx,.ppt,.pdf,.txt,.msg,.zip,.psd,.csv"
                                                    data-upload-url="{{ url('ticket/attachment/add') }}"
                                                    data-remove-url="{{ url('ticket/attachment/remove') }}"
                                                    data-token="{{ csrf_token() }}"
                                                    data-record-input="#tkt_reuqest_update_status_form #tmp_id"
                                                    data-upload-field="attachment"
                                                    data-ticket-id="{{ $request->id }}">

                                                    {{-- File Input --}}
                                                    <input type="file"
                                                        class="amg-uploader__input"
                                                        multiple
                                                        hidden>

                                                    {{-- Dropzone --}}
                                                    <div class="amg-uploader__dropzone border rounded d-flex flex-column align-items-center justify-content-center text-center srd-attachment-box">

                                                        <div class="small d-flex align-items-center gap-2">

                                                            <svg width="21"
                                                                height="16"
                                                                viewBox="0 0 21 16"
                                                                fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">

                                                                <path
                                                                    d="M11.25 15.75V11.25H14.25L10.5 6.75L6.75 11.25H9.75V15.75H6V15.7125C5.87344 15.7195 5.75391 15.75 5.625 15.75C4.13316 15.75 2.70242 15.1574 1.64752 14.1025C0.592632 13.0476 0 11.6168 0 10.125C0.00194455 8.74787 0.511116 7.41972 1.43026 6.39422C2.34941 5.36872 3.61411 4.71774 4.98281 4.56562C5.22893 3.28221 5.91421 2.12454 6.92099 1.2914C7.92776 0.458267 9.19321 0.00166223 10.5 0C11.8072 0.00111796 13.0732 0.457475 14.0805 1.29066C15.0877 2.12384 15.7733 3.28182 16.0195 4.56562C17.3874 4.71881 18.651 5.37026 19.5691 6.39565C20.4873 7.42104 20.9958 8.7486 20.9977 10.125C20.9977 11.6168 20.405 13.0476 19.3501 14.1025C18.2952 15.1574 16.8645 15.75 15.3727 15.75C15.2484 15.75 15.1266 15.7195 14.9977 15.7125V15.75H11.25Z"
                                                                    fill="#7F7F7F" />

                                                            </svg>

                                                            <span class="amg-uploader__trigger text-muted">

                                                               {{ trans('service_ticket.service_detail.drag_drop_upload_files') }}

                                                            </span>

                                                        </div>

                                                    </div>

                                                    {{-- Preview --}}
                                                    <div class="amg-uploader__preview mt-2" id="update_attachments"></div>

                                                    {{-- Error --}}
                                                    <div class="amg-uploader__error text-danger mt-1"></div>

                                                </div>

                                            <input type="hidden" name="form_type" id="form_type" value="0">

                                                <div class="text-muted mt-1 srd-attachment-note">
                                                    {{ trans('service_ticket.service_detail.supported_file_types') }}
                                                </div>
                                                <div class="text-muted mt-1 srd-attachment-note">
                                                    {{ trans('service_ticket.service_detail.max_files_size_message') }}
                                                </div>

                                            </div>

                                            {{-- Checkboxes --}}
                                            <div class="mb-3">

                                                <div class="form-check mb-2">

                                                    <input class="form-check-input" type="checkbox" id="is_note"
                                                        name="is_note" value="1">

                                                    <label class="form-check-label text-muted small" for="is_note">
                                                        {{ trans('content.service_ticket_fields.internal_purpose') }}
                                                    </label>

                                                </div>

                                                <div class="form-check mb-2">

                                                    <input class="form-check-input" type="checkbox" id="add_back_trail"
                                                        name="add_back_trail" value="1" checked>

                                                    <label class="form-check-label text-muted small" for="add_back_trail">
                                                        {{ trans('content.service_ticket_fields.back_trail') }}
                                                    </label>

                                                </div>

                                                <div class="form-check mb-2">

                                                    <input class="form-check-input" type="checkbox" id="follow_cc"
                                                        name="follow_cc" value="1">

                                                    <label class="form-check-label text-muted small" for="follow_cc">
                                                        {{ trans('content.service_ticket_fields.CC_EMails') }}
                                                    </label>

                                                </div>

                                                <div class="mt-2">

                                                    <textarea name="cc_emails" id="cc_emails" class="form-control" rows="2">{{ $request->cc_emails }}</textarea>

                                                </div>

                                            </div>

                                            {{-- Button --}}
                                            <button type="button" id="updateStatus"
                                                class="amg-btn amg-btn-primary px-5 rounded-2 fw-semibold w-100">

                                                {{ trans('service_ticket.button.update') }}

                                            </button>

                                        </form>

                                        <input type="file" id="update_attachment" name="attachment"
                                            style="visibility:hidden" />

                                    </div>

                                </div>
                            @endif
                        @endforeach

                    @endif

                    {{-- Creator Info — collapsible --}}
                    <div class="card rounded-3 overflow-hidden">

                        {{-- Header --}}
                        <div class="px-3 py-3 fw-semibold border-bottom srd-creator-info-header">
                           {{ trans('ticket.ticket_detail.creator_info') }}
                        </div>

                        {{-- Body --}}
                        <div class="px-3 pt-3 pb-3">

                            {{-- Avatar + Name --}}
                            <div class="d-flex align-items-center gap-3 mb-3">

                                <div class="rounded-circle overflow-hidden flex-shrink-0 srd-creator-avatar">

                                    <img src="{{ $creator->getProfileImg() }}" alt="{{ $creator->fullName() }}"
                                        class="w-100 h-100 object-fit-cover">

                                </div>

                                <div>

                                    <div class="fw-semibold text-dark srd-creator-name">

                                        {{ $creator->fullName() }}

                                    </div>

                                    @if ($creator->jobtitle)
                                        <div class="text-muted small"
                                          @if(strlen($creator->jobtitle) > 25)
                                                data-bs-toggle="tooltip"
                                                title="{{ $creator->jobtitle }}"
                                            @endif
                                        >
                                        {{ \Illuminate\Support\Str::limit($creator->jobtitle, 25) }}

                                        </div>
                                    @endif

                                </div>

                            </div>

                            {{-- Info Rows --}}
                            <div class="d-flex flex-column gap-1 pb-1">
                                @php
                                    $employee_code = $creator->employee_num;
                                    $location = optional($creator->location)->name;
                                    $phone = $creator->phone;
                                    $email = $creator->email;
                                    $ex_user_company = $creator->ex_user_company;
                                    $user_company = optional($creator->company)->name;
                                @endphp

                                @if ($creator->employee_num)
                                    <div class="d-flex align-items-start gap-2 srd-creator-info-row">

                                        <span class="text-dark srd-creator-label">
                                            {{ trans('ticket.create_ticket.employee_code') }}
                                        </span>

                                        <span class="text-muted">:</span>

                                        <span class="text-muted"
                                            @if(strlen($employee_code) > 25)
                                                data-bs-toggle="tooltip"
                                                title="{{ $employee_code }}"
                                            @endif
                                        >
                                        {{ \Illuminate\Support\Str::limit($employee_code, 25) }}
                                        </span>

                                    </div>
                                @endif

                                @if ($creator->location)
                                    <div class="d-flex align-items-start gap-2 srd-creator-info-row">

                                        <span class="text-dark srd-creator-label">
                                            {{ trans('ticket.create_ticket.location') }}
                                        </span>

                                        <span class="text-muted">:</span>

                                        <span class="text-muted"
                                            @if(strlen($location) > 25)
                                                data-bs-toggle="tooltip"
                                                title="{{ $location }}"
                                            @endif
                                        >
                                        {{ \Illuminate\Support\Str::limit($location, 25) }}
                                        </span>

                                    </div>
                                @endif

                                @if ($creator->phone)
                                    <div class="d-flex align-items-start gap-2 srd-creator-info-row">

                                        <span class="text-dark srd-creator-label">
                                           {{ trans('ticket.create_ticket.mobile') }}
                                        </span>

                                        <span class="text-muted">:</span>

                                        <span class="text-muted"
                                            @if(strlen($phone) > 25)
                                                data-bs-toggle="tooltip"
                                                title="{{ $phone }}"
                                            @endif
                                        >
                                        {{ \Illuminate\Support\Str::limit($phone, 25) }}
                                        </span>

                                    </div>
                                @endif

                                @if ($creator->email)
                                    <div class="d-flex align-items-start gap-2 srd-creator-info-row">

                                        <span class="text-dark srd-creator-label">
                                            {{ trans('ticket.create_ticket.email') }}
                                        </span>

                                        <span class="text-muted">:</span>

                                        <span class="text-muted"
                                            @if(strlen($email) > 25)
                                                data-bs-toggle="tooltip"
                                                title="{{ $email }}"
                                            @endif
                                        >
                                        {{ \Illuminate\Support\Str::limit($email, 25) }}
                                        </span>

                                    </div>
                                @endif
                                @if (($creator->job_type > 0 && $creator->ex_user_company) || optional($creator->company)->name)

                                    <div class="d-flex align-items-start gap-2 srd-creator-info-row">

                                        <span class="text-dark srd-creator-label">
                                           {{ trans('ticket.create_ticket.company') }}
                                        </span>

                                        <span class="text-muted">:</span>

                                        <span class="text-muted"

                                            @if ($creator->job_type > 0)
                                                @if(strlen($ex_user_company) > 25)
                                                    data-bs-toggle="tooltip"
                                                    title="{{ $ex_user_company }}"
                                                @endif
                                            >
                                                {{ \Illuminate\Support\Str::limit($ex_user_company, 25) }}
                                            @else
                                                   @if(strlen($user_company) > 25)
                                                    data-bs-toggle="tooltip"
                                                    title="{{ $user_company }}"
                                                @endif
                                            >
                                                {{ \Illuminate\Support\Str::limit($user_company, 25) }}
                                            @endif

                                        </span>

                                    </div>

                                @endif

                            </div>

                        </div>

                    </div>
                </div>{{-- /srd-right --}}
            </div>{{-- /srd-body --}}
        </main>
    </div>
    @include('tickets.ticket-list.ticket-history')
@endsection
@push('css')
    <link href="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.css') !!}" rel="stylesheet" />
    <link href="{!! CommonHelper::asset('plugins/swipebox/css/swipebox.min.css') !!}" rel="stylesheet" />
    <style>
        .srd-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 4px;
        }

        .srd-badge-success {
            background: #eef8ea;
            border: 1px solid #d8ebce;
        }

        .srd-badge-light {
            background: #faf3f5;
            border: 1px solid #efdfe2;
        }

        .srd-avatar {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #186B43;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            flex-shrink: 0;
        }

        .srd-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            display: block;
        }

        .srd-badge-info {
            background: #E6FFFF;
            border: 1px solid #cff1f1;
        }

        .srd-comment-box {
            border: 1px solid #dee2e6;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            border-radius: 4px;
        }

        .srd-conv-avatar {
            width: 28px;
            height: 28px;
            background: #14b8a6;
            font-size: 10px;
        }

        .srd-attachment-box {
            border-style: dashed !important;
            min-height: 72px;
        }

        .srd-attachment-note {
            font-size: 11px;
        }

        .srd-update-header {
            background: #001f5b;
        }

        .srd-creator-info-header {
            background: #eff2fa;
            font-size: 15px;
        }

        .srd-creator-avatar {
            width: 40px;
            height: 40px;
            background: #4dbfa0;
        }

        .srd-creator-name {
            font-size: 15px;
        }

        .srd-creator-info-row {
            font-size: 13px;
        }

        .srd-creator-label {
            min-width: 88px;
        }

        .srd-card {
            background: var(--app-surface);
            border: 1px solid var(--app-border);
            border-radius: 12px;
            overflow: hidden;
        }

        [data-bs-theme="dark"] .srd-card ,  [data-bs-theme="dark"] .card{
            background: #191919 !important;
            border: 1px solid #2A2A2D;
        }

        .srd-info {
            padding: 14px 16px;
            border-bottom: 1px solid var(--app-border);
        }

        [data-bs-theme="dark"] .srd-info {
            border-color: var(--dark-border) !important;
        }

        .srd-req-h {
            font-size: 14.5px;
            font-weight: 700;
            color: var(--app-text);
            margin: 0 0 3px;
            line-height: 1.4;
        }

        .srd-req-p {
            font-size: 12.5px;
            color: #6b7280;
            margin: 0 0 12px;
            line-height: 1.5;
        }

        [data-bs-theme="dark"] .srd-req-h {
            color: var(--text-primary) !important;
        }

        [data-bs-theme="dark"] .srd-req-p {
            color: var(--text-muted) !important;
        }

        .srd-panel {
            display: none;
            padding: 16px;
        }

        .srd-panel.active {
            display: block;
        }

        .srd-cbox {
            background: #F0F0F0;
        }

        [data-bs-theme="dark"] .srd-cbox {
            background: var(--dark-primary) !important;
        }

        .srd-meta-row {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 14px;
        }

        .srd-meta-label {
            min-width: 180px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #2b2b2b;
        }

        .srd-meta-value {
            flex: 1;
            font-size: 13px;
            color: #6b7280;
            word-break: break-word;
        }

        @media (max-width:767px) {

            .srd-meta-row {
                flex-direction: column;
                gap: 4px;
                margin-bottom: 12px;
            }

            .srd-meta-label {
                min-width: 100%;
            }

        }
        .tab-bar {
            position: relative;
        }

        .srd-expand-btn {
            position: absolute;
            top: 12px;
            right: 20px;
        }

        .srd-content-preview {
            max-height: 40px;
            overflow: hidden;
            position: relative;
            transition: all .3s ease;
        }

        .srd-content-preview.expanded {
            max-height: 100%;
        }

        .error {
            color: #c53030;
            font-size: 13px;
        }

        .srd-conv-avatar,
        .srd-conv-avatar-img {
            width: 20px;
            height: 20px;
            min-width: 20px;
            object-fit: cover;
        }

        .srd-conv-avatar {
            background: #2962ff;
            font-size: 14px;
        }

        .srd-comment-item {
            max-height: 70px;
            overflow: hidden;
            transition: all .3s ease;
            position: relative;
        }

        .srd-comment-item.srd-comment-expanded {
            max-height: 1000px;
            overflow: visible;
        }

        #ticket_timeline {
            max-height: 200px;
            overflow-y: auto;
        }

        .approval-comment-hidden {
            display: none !important;
        }
        [data-bs-theme="dark"] .srd-badge-success{
            background: #0f2f28;
            border: 1px solid #0f2f28;
        }

        [data-bs-theme="dark"] .srd-badge-light{
            background: #2a0f14;
            border: 1px solid#2a0f14;
        }
        [data-bs-theme="dark"] .srd-badge-info{
            background: #071B22;
            border: 1px solid#071B22;
        }
        [data-bs-theme=dark] .sr-detail-wrapper .note-toolbar {
            border-bottom: 1.5px solid #2A2A2D !important;
            background: #1D1D1D !important;
        }
        [data-bs-theme=dark] .select2-container--default .select2-selection--single,[data-bs-theme=dark] textarea{
            background: #191919 !important;
            border: 1px solid  #2A2A2D !important;
        }
        [data-bs-theme=dark] .sr-detail-wrapper .note-btn-group .note-btn{
            color: #7F7F7F !important;
        }
        [data-bs-theme=dark] .srd-creator-info-header{
           background: #1D1D1D !important;  
        }
        .sr-detail-wrapper .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
            top: 0;
        }

        .sr-detail-wrapper .select2-container--default .select2-selection--single .select2-selection__arrow b {
            margin-top: 12px;
        }

        #attachment-dropper-cover-comment, #attachment-dropper-cover-update{
            cursor: pointer !important;
        }
    </style>
@endpush
@push('scripts')
    <script type="text/javascript" src="{!! CommonHelper::asset('js/tickets/serviceRequest/request-info.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('plugins/swipebox/js/jquery.swipebox.min.js') !!}"></script>
    <script src="{{ CommonHelper::asset('js/drag-drap.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var config = new Object;
            config.url = new Object;
            config.attachments = {!! json_encode($attachments) !!};
            config.client = "{{ config('app.client') }}";
            config.sub_client = "{{ config('app.sub_client') }}";
            config.data = {!! json_encode($request) !!};
            config.token = "{{ csrf_token() }}";
            config.tkt_config = {!! json_encode($tkt_config) !!};

            config.id = "{{ $request->id }}";
            config.url.info = "{{ url('tickets/requestInfo') }}";
            config.url.approve = "{{ url('tickets/requestApprove') }}/{{ $request->id }}";
            config.url.attachment_view = "{{ url('ticket/attachment/view') }}";
            config.url.attachment_download = "{{ url('ticket/attachment/download') }}";
            config.url.update = "{{ url('tickets/updateRequestInfo') }}/{{ $request->id }}";
            config.url.revokeDecision = "{{ route('tickets.revokeDecision', [':pr_id']) }}";
            config.url.getRefreshedTimeLine = "{{ url('tickets/request/timeline') }}";
            config.url.mail_body = "{{ url('ticket/mail/body') }}";
            config.url.getTicketRequestHistory = "{{ url('tickets/requestsHistory') }}";

            config.translations = {
                service_request_history: '{{ trans('service_ticket.service_detail.service_request_history') }}',
            }

            new TicketRequestInfo(config);
        });
    </script>
@endpush

{{-- @page-meta
{
  "page_no": "INCMD-02",
  "file": "import.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "Muzaffar Shaikh",
      "from": "2026-04",
      "reviewer": null,
      "description": "Initial setup"
    }
  ]
}
--}}
@extends('layouts.layout1')
@section('title', 'Ticket Incident')
@section('content')
    <div id="ticket-incident-list-wrapper">
        <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
            <h3 class="h3-text mb-0">{{ trans('ticket_incident.ticket_incident.title') }}</h3>

            <div class="d-flex gap-8">
                <button class="header-icon-btn-only header-icon-btn-only-sm" id="print" data-bs-toggle="tooltip"
                   data-bs-original-title="{{ trans('ticket_incident.ticket_incident.print') }}" >
                    <svg width="16px" height="16px" viewBox="0 -2 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">
                        <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"
                            sketch:type="MSPage">
                            <g id="Icon-Set" sketch:type="MSLayerGroup" transform="translate(-100.000000, -205.000000)"
                                fill="currentColor">
                                <path
                                    d="M130,226 C130,227.104 129.104,228 128,228 L125.858,228 C125.413,226.278 123.862,225 122,225 L110,225 C108.138,225 106.587,226.278 106.142,228 L104,228 C102.896,228 102,227.104 102,226 L102,224 C102,222.896 102.896,222 104,222 L128,222 C129.104,222 130,222.896 130,224 L130,226 L130,226 Z M122,231 L110,231 C108.896,231 108,230.104 108,229 C108,227.896 108.896,227 110,227 L122,227 C123.104,227 124,227.896 124,229 C124,230.104 123.104,231 122,231 L122,231 Z M108,209 C108,207.896 108.896,207 110,207 L122,207 C123.104,207 124,207.896 124,209 L124,220 L108,220 L108,209 L108,209 Z M128,220 L126,220 L126,209 C126,206.791 124.209,205 122,205 L110,205 C107.791,205 106,206.791 106,209 L106,220 L104,220 C101.791,220 100,221.791 100,224 L100,226 C100,228.209 101.791,230 104,230 L106.142,230 C106.587,231.723 108.138,233 110,233 L122,233 C123.862,233 125.413,231.723 125.858,230 L128,230 C130.209,230 132,228.209 132,226 L132,224 C132,221.791 130.209,220 128,220 L128,220 Z"
                                    id="print" sketch:type="MSShapeGroup"></path>
                            </g>
                        </g>
                    </svg>
                </button>
            </div>

        </div>
        <main class="main-content view-incident-details" id="mainContent">
            <div class="container-fluid px-4 mt-4 incident-view-card">

                <p class="b1-text fw-bold mb-3 subject-text">{{ trans('ticket_incident.ticket_incident.subject') }} : {{ $data->subject }}</span></p>

                <div class="card-panel shadow-none incident-summary-card rounded-3">
                    <div class="summary-grid">
                        <div class="summary-tile d-flex justify-content-between align-items-center px-4 py-3">
                            <div class="d-flex flex-column gap-1">
                                <div class="tile-label b3-text opacity-70">{{ trans('ticket_incident.ticket_incident.company') }}</div>
                                <div class="tile-value b1-text">{{ $data->company_name }}</div>
                            </div>

                            <div>
                                <img style="height: 40px;object-fit: contain;width: 40px;" src="{{ asset('imgs/view-incident-card-icon.png') }}" alt="">
                            </div>
                        </div>

                        <div class="summary-tile d-flex justify-content-between align-items-center px-4 py-3">
                            <div class="d-flex flex-column gap-1">
                                <div class="tile-label b3-text opacity-70">{{ trans('ticket_incident.ticket_incident.ticket_id') }}</div>
                                @if (isset($data->ticket_id))
                                    <div class="tile-value b1-text">#{{ $data->ticket_id }}</div>
                                @else
                                    <div class="tile-value b1-text">-</div>
                                @endif
                            </div>
                            <div>
                                <img style="height: 40px;object-fit: contain;width: 40px;" src="{{ asset('imgs/view-incident-card-icon.png') }}" alt="">
                            </div>
                        </div>

                        <div class="summary-tile d-flex justify-content-between align-items-center px-4 py-3">
                            <div class="d-flex flex-column gap-1">
                                <div class="tile-label b3-text opacity-70">{{ trans('ticket_incident.ticket_incident.dept_id') }}</div>
                                <div class="tile-value b1-text">{{ $data->dept_name }}</div>
                            </div>
                            <div>
                                <img style="height: 40px;object-fit: contain;width: 40px;" src="{{ asset('imgs/view-incident-card-icon.png') }}" alt="">
                            </div>
                        </div>

                        <div class="summary-tile d-flex justify-content-between align-items-center px-4 py-3">
                            <div class="d-flex flex-column gap-1">
                                <div class="tile-label b3-text opacity-70">{{ trans('ticket_incident.ticket_incident.created_at') }}</div>
                                <div class="tile-value b1-text">{{ $data->username }}</div>
                            </div>
                            <div>
                                <img style="height: 40px;object-fit: contain;width: 40px;" src="{{ asset('imgs/view-incident-card-icon.png') }}" alt="">
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row g-4">

                    <div class="col-lg-6">
                        <div class="card-panel incident-summary-card mb-0 px-4 py-3 h-100 shadow-none rounded-3">
                            <div>

                                @php
                                    $priorityColors = [
                                        'critical' => ['bg' => '#F12F35', 'text' => '#fff'],
                                        'high' => ['bg' => '#C0392B', 'text' => '#fff'],
                                        'medium' => ['bg' => '#E67E22', 'text' => '#fff'],
                                        'low' => ['bg' => '#186B43', 'text' => '#fff'],
                                    ];
                                    $priority = strtolower(trim($data->priority_name));
                                    $color = $priorityColors[$priority] ?? ['bg' => '#6c757d', 'text' => '#fff'];
                                @endphp

                                <div class="detail-row d-flex justify-content-between align-items-start">
                                    <span class="detail-key b1-text">{{ trans('ticket_incident.ticket_incident.priority') }}</span>
                                    <span class="">
                                        <span class="badge rounded-pill"
                                            style="font-size: 16px; background-color: {{ $color['bg'] }}; color: {{ $color['text'] }};">
                                            {{ $data->priority_name }}
                                        </span>
                                    </span>
                                </div>

                                <div class="detail-row d-flex justify-content-between align-items-start">
                                    <span class="detail-key b1-text">{{ trans('ticket_incident.ticket_incident.location') }}</span>

                                    @if (isset($data->location_name))
                                        <div class="b1-text muted-text">{{ $data->location_name }}</div>
                                    @else
                                        <div class="b1-text muted-text">—</div>
                                    @endif
                                </div>

                                <div class="detail-row d-flex justify-content-between align-items-start">
                                    <span class="detail-key b1-text">{{ trans('ticket_incident.ticket_incident.internal_place') }}</span>
                                    @if (isset($data->place_name))
                                        <div class="b1-text muted-text">{{ $data->place_name }}</div>
                                    @else
                                        <div class="b1-text muted-text">—</div>
                                    @endif
                                </div>

                                <div class="detail-row d-flex justify-content-between align-items-start">
                                    <span class="detail-key b1-text">{{ trans('ticket_incident.ticket_incident.problem_category') }}</span>
                                    <span class=""><span class="badge bg-success"
                                            style="font-size: 16px; background-color:#E8FFF6 !important;color:#000000">{{ $data->cat_name }}</span></span>
                                </div>

                                <div class="detail-row d-flex justify-content-between align-items-start">
                                    <span class="detail-key b1-text">{{ trans('ticket_incident.ticket_incident.sub_category') }}</span>
                                    <span class="b1-text muted-text">{{ $data->sub_cat_name }}</span>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card-panel incident-summary-card  mb-0 px-4 py-3 h-100 shadow-none rounded-3">
                            <div class="detail-block">

                                @php
                                    $statusColors = [
                                        'opened' => ['bg' => '#186B43', 'text' => '#fff'],
                                        'closed' => ['bg' => '#C0392B', 'text' => '#fff'],
                                    ];
                                    $status = strtolower(trim($data->status));
                                    $color = $statusColors[$status] ?? ['bg' => '#6c757d', 'text' => '#fff'];
                                @endphp

                                <div class="detail-row d-flex justify-content-between align-items-start">
                                    <span class="detail-key b1-text">{{ trans('ticket_incident.ticket_incident.status') }}</span>
                                    <span class="">
                                        <span class="badge rounded-pill"
                                            style="font-size: 16px; background-color: {{ $color['bg'] }}; color: {{ $color['text'] }};">
                                            {{ $data->status }}
                                        </span>
                                    </span>
                                </div>
                                <div class="detail-row d-flex justify-content-between align-items-start">
                                    <span class="detail-key b1-text">{{ trans('ticket_incident.ticket_incident.incident_created_date_time') }}</span>
                                    <span class="b1-text muted-text">
                                        {{ $data->incident_start_date ? \Carbon\Carbon::parse($data->incident_start_date)->format('d M Y, h:i A') : '-' }}
                                    </span>
                                </div>
                                <div class="detail-row d-flex justify-content-between align-items-start">
                                    <span class="detail-key b1-text">{{ trans('ticket_incident.ticket_incident.incident_end_date_time') }}</span>
                                    <span class="b1-text muted-text">
                                        {{ $data->incident_end_date ? \Carbon\Carbon::parse($data->incident_end_date)->format('d M Y, h:i A') : '-' }}
                                    </span>
                                </div>
                                <div class="detail-row d-flex justify-content-between align-items-start">
                                    <span class="detail-key b1-text">{{ trans('ticket_incident.ticket_incident.service_impacted') }}</span>
                                    <span class="d-flex flex-wrap gap-2">
                                        @foreach (explode(',', $data->service_impacted) as $service)
                                            <span class="badge bg-dark rounded-pill" style="font-size: 16px;">
                                                {{ trim($service) }}
                                            </span>
                                        @endforeach
                                    </span>
                                </div>

                                <div class="detail-row d-flex justify-content-between align-items-start">
                                    <span class="detail-key b1-text">{{ trans('ticket_incident.ticket_incident.sla_breaches') }}</span>
                                    <span class="b1-text muted-text">{{ $data->sla_breaches }}</span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4" style="">
                    <div class="col-lg-12">
                        <div class="card-panel incident-summary-card mb-0 py-2 px-3 rounded-2 shadow-none">
                            <div class="detail-row d-flex justify-content-start align-items-start p-0">
                                <span class="detail-key b1-text col-3" style="min-width: 250px;">{{ trans('ticket_incident.ticket_incident.content') }}</span>
                                <div class="b1-text muted-text m-0 text-start col-9"
                                    style="word-break: break-word; overflow-wrap: break-word; flex: 1;">
                                    {{ html_entity_decode(strip_tags($data->content)) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4" style="">
                    <div class="col-lg-12">
                        <div class="card-panel incident-summary-card mb-0 py-2 px-3 rounded-2 shadow-none">
                            <div class="detail-row d-flex justify-content-start align-items-start p-0">
                                <span class="detail-key b1-text text-start col-3"
                                    style="min-width: 250px;">{{ trans('ticket_incident.ticket_incident.rca') }}</span>
                                <div class="b1-text muted-text m-0 text-start col-9"
                                    style="word-break: break-word; overflow-wrap: break-word; flex: 1;">
                                    {{ html_entity_decode(strip_tags($data->rca)) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4" style="">
                    <div class="col-lg-12">
                        <div class="card-panel incident-summary-card mb-0 py-2 px-3 rounded-2 shadow-none">
                            <div class="detail-row d-flex justify-content-start align-items-start py-1">
                                <span class="detail-key b1-text text-start col-3">{{ trans('ticket_incident.ticket_incident.why_incident_happen') }}</span>
                                <div class="m-0 text-start col-9 b1-text muted-text"
                                    style="word-break: break-word; overflow-wrap: break-word;">
                                    {{ html_entity_decode(strip_tags($data->why_incident_happen)) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4 mb-5" style="">
                    <div class="col-lg-12">
                        <div class="card-panel incident-summary-card mb-0 py-2 px-3 rounded-2 shadow-none">
                            <div class="detail-row d-flex justify-content-start align-items-start py-1">
                                <span class="detail-key b1-text text-start col-3"
                                    style="min-width: 250px">{{ trans('ticket_incident.ticket_incident.preventive_measure_taken') }}</span>
                                <div class="m-0 text-start p-0 text-start col-9 b1-text muted-text">{{ html_entity_decode(strip_tags($data->preventive_measure_taken)) }}</div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </main>
    </div>
@endsection

@push('css')
    <link href="{!! CommonHelper::asset('newcss/select2.min.css') !!}" rel="stylesheet" />
    <link href="{!! CommonHelper::asset('plugins/swipebox/css/swipebox.min.css') !!}" rel="stylesheet" />
    <style>
        :root {
            --bg: #f4f6f9;
            --surface: #ffffff;
            --border: #e4e8ef;
            --text-main: #1a1f2e;
            --text-muted: #6b7280;
            --accent: #4361ee;
            --high: #ef4444;
            --tag-cloud-bg: #e8f5e9;
            --tag-cloud-fg: #2e7d32;
            --shadow: 0 1px 4px rgba(0, 0, 0, .08), 0 4px 16px rgba(0, 0, 0, .05);
        }

        .subject-text {
            color: #000000 !important;
        }

        [data-bs-theme="dark"] .subject-text {
            color: #fff !important;
        }

        [data-bs-theme="dark"] .incident-summary-card {
            background: #191919;
            border: none;
        }

        [data-bs-theme="dark"] .incident-summary-card .summary-tile {
            border: 1px solid #2A2A2D;
        }

        [data-bs-theme="dark"] .incident-summary-card . {
            border: 1px solid #2A2A2D;
        }

        /* [data-bs-theme="dark"] .incident-summary-card span,
        p {
            color: #fff !important;
        }
        .incident-summary-card span,
        p {
            color: #000 !important;
        } */

        .incident-summary-card .badge {
            color: #fff !important;
        }

        [data-bs-theme="dark"] .incident-summary-card .badge.bg-success,
        .incident-summary-card .badge.bg-success
        {
            color: #000 !important;
        }

        [data-bs-theme="dark"] .incident-summary-card .badge.bg-dark {
            background: #2a2a2a !important;
            color: #fff !important;
        }

        .muted-text{
            color: #7F7F7F !important;
        }

        .card-panel {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: var(--shadow);
            padding: 1.25rem;
            margin-bottom: 1.25rem;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }

        @media (max-width: 768px) {
            .summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .summary-grid {
                grid-template-columns: 1fr;
            }
        }

        .summary-tile {
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 1rem 1.1rem;
            position: relative;
            transition: box-shadow .2s;
        }

        .summary-tile:hover {
            box-shadow: 0 4px 14px rgba(67, 97, 238, .12);
        }

        .tile-label {
            font-weight: 400;
            color: #202224;
            text-transform: capitalize;
        }

        .tile-value {
            color: #202224;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .detail-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.25rem 0;
            font-size: .875rem;
        }

        .detail-key {
            min-width: 130px;
        }
    </style>
@endpush
@push('scripts')
    <script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.js') !!}"></script>
    <script src="{!! CommonHelper::asset('js/tickets/ticket-incident/index.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.min.js') !!}"></script>
    <script>
        var t = $(this);
        var config = {};
        config.url = {};
        config.url.attachment_download = "{{ url('tickets/attachment/download') }}";
        config.url.attachment_view = "{{ url('tickets/attachment/viewItem') }}";
        config.main_attachments = {!! json_encode($attachments) !!};
        var shedId = "{{ last(request()->segments()) }}";

        function viewIncident(e) {
            e.preventDefault();
            var url = "{{ url('tickets/exportTicketIncidentModalPDF') }}/" + shedId;
            window.open(url, '_blank');
        }
        $(document).on('click', '#print', viewIncident);


        function decodeURIComponentSafe(uri, mod) {
            var out = new String(),
                arr,
                i = 0,
                l,
                x;
            typeof mod === "undefined" ? mod = 0 : 0;
            arr = uri.split(/(%(?:d0|d1)%.{2})/);
            for (l = arr.length; i < l; i++) {
                try {
                    x = decodeURIComponent(arr[i]);
                } catch (e) {
                    x = mod ? arr[i].replace(/%(?!\d+)/g, '%25') : arr[i];
                }
                out += x;
            }
            return out;
        }

        t.setMainAttachments = function() {
            var at = [];
            $.each(config.main_attachments, function(i, v) {
                var sext = v.ext.toLowerCase();
                var eye_link = "";

                if (["png", "jpeg", "jpg"].includes(sext)) {
                    eye_link = '<span class="image-view" data-view_mode="1" data-view="' + config.url
                        .attachment_view + "/" + v.id + '" data-name="' + decodeURIComponent(v.name) +
                        '"><i class="fa fa-eye"></i></span>';
                }

                var ai_bg = v.thumb == 1 ? "ai-bg" : "";
                var bg = "background-image: url(" + config.url.attachment_view + "/" + v.id;
                at.push('<div class="attach-item ' + ai_bg + '" style="' + bg + '" >' +
                    '<div class="attach-item-cntnt">' + '<span class="attach-name">' +
                    decodeURIComponentSafe(unescape(v.name)) + "</span>" + '<div class="icons">' +
                    eye_link + '<span class="tri-download" data-url="' + config.url.attachment_download +
                    "/" + v.id + '"><i class="fa fa-download"></i></span>' + "</div></div></div>");
            });

            if (at.length > 0) {
                $('.main_attachments').html('<div>' + at.join("") + '</div>');
                $("#article-info-page").find(".attach-doc").removeClass("hide");
            }
        }

        t.attachmentView = function(e) {
            e.preventDefault();
            var type = $(this).attr('data-view_mode');
            if (type == 1) {
                $.swipebox([{
                    href: $(this).attr('data-view'),
                    title: $(this).attr('data-name')
                }]);
            }
        };

        t.attachmentDownload = function(e) {
            e.preventDefault();
            window.location = $(this).attr('data-url');
        }

        t.setMainAttachments();
        $('.main_attachments').on("click", ".image-view", $.proxy(t.attachmentView));
        $('.main_attachments').on("click", ".tri-download", $.proxy(t.attachmentDownload));
    </script>
@endpush

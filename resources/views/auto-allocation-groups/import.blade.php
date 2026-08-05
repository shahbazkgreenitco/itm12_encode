@extends('layouts.layout1')

@section('title', 'Auto Allocation Group Import')

@section('content')

<style>
        :root {
            --primary: #1a2e5a;
            --primary-hover: #243d7a;
            --border-dash: #b0bec5;
            --bg-upload: #f8fafc;
            --bg-page: #eef2f7;
            --text-muted: #607d8b;
            --accent: #1565c0;
        }

        .import-wrapper {
            padding: .4rem 0rem 1rem 0rem;
            width: 100%;
        }

        .import-wrapper .upload-doc-btn,
        .import-wrapper .save-doc-btn {
            font-size: 12px;
            height: 26px;
            min-width: 160px
        }

        .import-wrapper .upload-doc-btn svg,
        .import-wrapper .save-doc-btn svg {
            width: 14px;
            height: 14px;
            color: #fff;
        }

        .import-wrapper h5 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 1.5rem;
            letter-spacing: 0.02em;
        }

        .import-wrapper #btnRemove {
            display: block;
            width: 100%;
            color: #00000070;
            font-size: 12px;
            text-decoration: underline;
            text-underline-offset: 3px;
            text-decoration-thickness: 1.10px;
            transition: opacity 0.3s ease;
            margin-top: 7px;
        }

        .import-wrapper #btnRemove:hover {
            color: #00000090;
        }

        .header-actions-wrapper.header-actions-white-wrapper {
            background: #F8FAFD !important;
            background-image: none !important;
            border-bottom: 1px solid #E8E8E8 !important;
        }

        .drop-zone {
            border: 2px dashed var(--border-dash);
            border-radius: 7px;
            background: var(--bg-upload);
            padding: 1.5rem 1.1rem;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
            position: relative;
        }

        .drop-zone:hover,
        .drop-zone.dragover {
            border-color: var(--accent);
            background: #e8f0fe;
        }

        .drop-zone input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .excel-icon {
            width: 52px;
            height: 52px;
            margin: 0 auto 1rem;
            display: block;
        }

        .drop-zone .drag-text,
        .drop-zone .file-selected-name {
            color: #000;
            font-size: 12px;
            margin-bottom: 0.85rem;
            font-weight: 400;
        }

        .btn-upload {
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 7px;
            padding: 0.5rem 1.25rem;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.01em;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            cursor: pointer;
            pointer-events: none;
            transition: background 0.2s;
        }

        .drop-zone:hover .btn-upload {
            background: var(--primary-hover);
        }

        .file-label {
            font-size: 12px;
            display: block;
            color: #000000;
            opacity: .60;
            font-weight: 400;
            margin-top: 7px;
        }

        .divider {
            display: none;
        }

        .right-panel {
            display: flex;
            align-items: end;
            justify-content: start;
            height: 100%;
        }

        @media (max-width: 575.98px) {
            .right-panel {
                border-left: none;
                border-top: 1px solid #e0e7ef;
                padding-top: 1.25rem;
                margin-top: 0.5rem;
            }

            .import-wrapper {
                padding: 1.5rem 1rem;
            }
        }

        .drop-zone.file-selected {
            border-color: #c8d6e5;
        }

        .drop-zone.file-selected:hover {
            border-color: #F12F35;
            background: #f12f3520;
        }

        .file-selected-name {
            font-weight: 400;
            color: #000000;
        }

        .label-required {
            color: var(--red);
        }

        .instructions-card {
            background: #fff;
            border: 1px solid #DFDFE1;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            width: 100%;
            font-size: 0.84rem;
            color: #374151;
            line-height: 1.75;
        }

        .instructions-card #readMoreBtn {
            text-decoration: underline;
            text-underline-offset: 3px;
            text-decoration-thickness: 1.10px;
            transition: opacity 0.3s ease;
        }

        .instructions-card #readMoreBtn:hover {
            opacity: .8;
        }

        .instructions-card .header-text {
            font-size: 0.84rem;
            color: #374151;
            margin-bottom: 0.4rem;
        }

        .instructions-card .notice {
            font-style: italic;
            font-weight: 600;
            color: #1a2e5a;
            margin-bottom: 0.75rem;
        }

        .instructions-card .section-title {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.6rem;
            font-size: 0.9rem;
        }

        .instructions-card ul {
            padding-left: 1.25rem;
            margin-bottom: 0.75rem;
            list-style: disc;
        }

        .instructions-card ul li {
            margin-bottom: 0.25rem;
            color: #4b5563;
        }

        .instructions-card ul li strong {
            color: #111827;
            font-weight: 600;
        }

        .card-scroll-body {
            max-height: 200px;
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 4px;
        }

        .card-scroll-body.expanded {
            max-height: none;
            overflow-y: visible;
        }

        .card-scroll-body::-webkit-scrollbar {
            width: 4px;
        }

        .card-scroll-body::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .card-scroll-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .card-scroll-body::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .read-more-link {
            color: var(--red);
            font-size: 0.82rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: color 0.2s;
        }

        .read-more-link:hover {
            color: #c62828;
            text-decoration: underline;
        }

    </style>

<section class="content auto-allocation-import-page">

    {{-- Header --}}
    <div class="header-actions-wrapper header-actions-white-wrapper d-flex align-items-center justify-content-between pe-4">
        <div class="d-flex align-items-center gap-1">
            <button onclick="location.href='{{ route('auto-allocation-groups') }}'" class="d-flex gap-3 align-items-center bg-transparent outline-none border-0" data-bs-toggle="tooltip" title="Back" >
                <svg width="16" height="21" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11.5662 0.440759C11.706 0.580116 11.817 0.745702 11.8927 0.928029C11.9684 1.11036 12.0074 1.30584 12.0074 1.50326C12.0074 1.70068 11.9684 1.89616 11.8927 2.07848C11.817 2.26081 11.706 2.4264 11.5662 2.56576L5.12993 9.00201L23.5049 9.00201C23.9028 9.00201 24.2843 9.16004 24.5656 9.44135C24.8469 9.72265 25.0049 10.1042 25.0049 10.502C25.0049 10.8998 24.8469 11.2814 24.5656 11.5627C24.2843 11.844 23.9028 12.002 23.5049 12.002L5.12993 12.002L11.5662 18.4408C11.848 18.7226 12.0063 19.1047 12.0063 19.5033C12.0063 19.9018 11.848 20.284 11.5662 20.5658C11.2844 20.8475 10.9022 21.0059 10.5037 21.0059C10.1052 21.0059 9.72298 20.8475 9.44118 20.5658L0.441182 11.5658C0.301343 11.4264 0.190387 11.2608 0.114679 11.0785C0.0389711 10.8962 -4.50154e-07 10.7007 -4.58778e-07 10.5033C-4.67401e-07 10.3058 0.038971 10.1104 0.114679 9.92803C0.190387 9.7457 0.301342 9.58011 0.441182 9.44076L9.44118 0.440759C9.58054 0.30092 9.74613 0.189964 9.92845 0.114255C10.1108 0.0385471 10.3063 -0.000425789 10.5037 -0.000425798C10.7011 -0.000425806 10.8966 0.038547 11.0789 0.114255C11.2612 0.189964 11.4268 0.30092 11.5662 0.440759Z" fill="currentColor"></path>
                </svg>
            </button>            
            <h3 class="h3-text mb-0"  > Import Allocation Group</h3>
        </div>
    </div>

    <main class="main-content" id="mainContent">
        <div class="container-fluid pt-3 pb-5">

            {{-- Session Message --}}
            @php
                $message = session('msg');
                $messageStatus = $message['status'] ?? '';
                $sessionMessage = $message['msg'] ?? '';

                $alertFailMsgs = [];

                if (!empty($message) && $messageStatus === 'danger' && !empty(session('fail_msgs'))) {
                    foreach (session('fail_msgs') as $failMsg) {
                        if ($failMsg !== $sessionMessage) {
                            $alertFailMsgs[] = $failMsg;
                        }
                    }
                }
            @endphp

            @if(!empty($sessionMessage))
                <div class="alert customize-alert alert-dismissible alert-light-{{ $messageStatus }} bg-{{ $messageStatus }}-subtle text-{{ $messageStatus }} fade show remove-close-icon" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

                    <div class="d-flex align-items-start me-3 gap-3">
                        <svg width="18px" height="18px" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" fill="none">
                            <path fill="currentColor" fill-rule="evenodd"
                                d="M10 3a7 7 0 100 14 7 7 0 000-14zm-9 7a9 9 0 1118 0 9 9 0 01-18 0zm8-4a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1zm.01 8a1 1 0 102 0V9a1 1 0 10-2 0v5z"/>
                        </svg>

                        <div>
                            {{ $sessionMessage }}

                            @if(count($alertFailMsgs))
                                <ul class="mt-2 mb-0">
                                    @foreach($alertFailMsgs as $msg)
                                        <li>{{ $msg }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            @if(session('fail', 0) > 0)
                <div class="alert customize-alert alert-dismissible alert-light-danger bg-danger-subtle text-danger fade show remove-close-icon" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

                    <div>
                        <strong>Failure records count: {{ session('fail') }}</strong>

                        @if(session('fail_msgs'))
                            <ul class="mt-2 mb-0">
                                @foreach(session('fail_msgs') as $msg)
                                    <li>{{ $msg }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Import Wrapper --}}
            <div class="import-wrapper">
                <div class="row g-3 align-items-stretch">

                    {{-- Upload Section --}}
                    <div class="col-12 col-sm-4">
                        <div class="drop-zone" id="dropZone">
                            <input type="file" id="fileInput" accept=".xlsx,.xls">

                            {{-- Default State --}}
                            <div id="defaultState">
                                <svg width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_2145_7517)">
                                        <path d="M27.4997 2.75H12.833C12.3469 2.75 11.8808 2.94309 11.537 3.2868C11.1933 3.63051 11.0002 4.09668 11.0002 4.58275V12.3745L27.4997 21.999L35.7494 25.4802L43.9991 21.999V12.3745L27.4997 2.75Z" fill="#21A366"></path>
                                        <path d="M11.0002 12.375H27.4997V21.9995H11.0002V12.375Z" fill="#107C41"></path>
                                        <path d="M42.1663 2.75H27.4996V12.3745H43.9991V4.58275C43.9991 4.09668 43.806 3.63051 43.4623 3.2868C43.1185 2.94309 42.6524 2.75 42.1663 2.75Z" fill="#33C481"></path>
                                        <path d="M27.4997 22H11.0002V39.4172C11.0002 39.6579 11.0476 39.8962 11.1398 40.1186C11.2319 40.3409 11.3669 40.543 11.537 40.7131C11.7072 40.8833 11.9093 41.0183 12.1316 41.1104C12.354 41.2025 12.5923 41.2499 12.833 41.2499H42.1673C42.4079 41.2499 42.6463 41.2025 42.8686 41.1104C43.091 41.0183 43.293 40.8833 43.4632 40.7131C43.6334 40.543 43.7684 40.3409 43.8605 40.1186C43.9526 39.8962 44 39.6579 44 39.4172V31.6245L27.4997 22Z" fill="#185C37"></path>
                                        <path d="M27.4996 22H43.9991V31.6245H27.4996V22Z" fill="#107C41"></path>
                                        <path opacity="0.1" d="M22.9145 9.625H11.0002V35.7499H22.9145C23.4003 35.7491 23.8661 35.5561 24.21 35.2131C24.5539 34.87 24.7481 34.4047 24.7501 33.919V11.4578C24.7486 10.9716 24.5546 10.5059 24.2107 10.1624C23.8667 9.81898 23.4006 9.62574 22.9145 9.625Z" fill="black"></path>
                                        <path opacity="0.2" d="M21.5425 11H11.0002V37.1249H21.5425C22.0278 37.1234 22.4929 36.9301 22.8362 36.5871C23.1795 36.2441 23.3733 35.7793 23.3753 35.294V12.8328C23.3738 12.3471 23.1802 11.8818 22.8368 11.5384C22.4935 11.195 22.0281 11.0015 21.5425 11Z" fill="black"></path>
                                        <path opacity="0.2" d="M21.5425 11H11.0002V34.3743H21.5425C22.0281 34.3729 22.4935 34.1793 22.8368 33.8359C23.1802 33.4925 23.3738 33.0272 23.3753 32.5416V12.8328C23.3738 12.3471 23.1802 11.8818 22.8368 11.5384C22.4935 11.195 22.0281 11.0015 21.5425 11Z" fill="black"></path>
                                        <path opacity="0.2" d="M20.1677 11H11.0002V34.3743H20.1677C20.6533 34.3729 21.1187 34.1793 21.462 33.8359C21.8054 33.4925 21.999 33.0272 22.0005 32.5416V12.8328C21.999 12.3471 21.8054 11.8818 21.462 11.5384C21.1187 11.195 20.6533 11.0015 20.1677 11Z" fill="black"></path>
                                        <path d="M1.83275 11H20.1696C20.6557 11 21.1218 11.1931 21.4655 11.5368C21.8092 11.8805 22.0023 12.3467 22.0023 12.8328V31.1696C22.0023 31.6556 21.8092 32.1218 21.4655 32.4655C21.1218 32.8092 20.6557 33.0023 20.1696 33.0023H1.83275C1.59184 33.0023 1.35329 32.9548 1.13074 32.8625C0.908203 32.7703 0.706038 32.635 0.535814 32.4645C0.365591 32.2941 0.23065 32.0917 0.13871 31.869C0.0467697 31.6463 -0.000364935 31.4077 2.12747e-06 31.1668V12.8328C2.12747e-06 12.3467 0.193095 11.8805 0.536803 11.5368C0.88051 11.1931 1.34668 11 1.83275 11Z" fill="#107C41"></path>
                                        <path d="M4.85785 28.8748L9.30709 21.9804L5.23203 15.125H8.51125L10.7359 19.5072C10.94 19.9224 11.0809 20.2326 11.1585 20.438H11.1873C11.3338 20.106 11.4873 19.7834 11.6481 19.47L14.0253 15.125H17.0355L12.8553 21.9422L17.137 28.8748H13.9369L11.367 24.0607C11.246 23.8558 11.1436 23.6406 11.0607 23.4175H11.0226C10.9476 23.6357 10.848 23.8446 10.7256 24.0402L8.08494 28.8748H4.85785Z" fill="white"></path>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_2145_7517">
                                            <rect width="44" height="44" fill="white"></rect>
                                        </clipPath>
                                    </defs>
                                </svg>
                                <p class="drag-text mt-3 mb-2 b1-text">{{ trans("config.file_upload.drag_drop") }}</p>
                                <button class="amg-btn amg-btn-secondary" type="button" style="min-width:203px;">
                                    <span>{{ trans("config.file_upload.upload_file") }}</span>
                                </button>
                                <span class="file-label b1-text">{{ trans("config.file_upload.no_file_selected") }}</span>
                            </div>

                            {{-- Selected State --}}
                            <div id="selectedState" style="display:none;">
                                <svg width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip1_2145_7517)">
                                        <path d="M27.4997 2.75H12.833C12.3469 2.75 11.8808 2.94309 11.537 3.2868C11.1933 3.63051 11.0002 4.09668 11.0002 4.58275V12.3745L27.4997 21.999L35.7494 25.4802L43.9991 21.999V12.3745L27.4997 2.75Z" fill="#21A366"></path>
                                        <path d="M11.0002 12.375H27.4997V21.9995H11.0002V12.375Z" fill="#107C41"></path>
                                        <path d="M42.1663 2.75H27.4996V12.3745H43.9991V4.58275C43.9991 4.09668 43.806 3.63051 43.4623 3.2868C43.1185 2.94309 42.6524 2.75 42.1663 2.75Z" fill="#33C481"></path>
                                        <path d="M27.4997 22H11.0002V39.4172C11.0002 39.6579 11.0476 39.8962 11.1398 40.1186C11.2319 40.3409 11.3669 40.543 11.537 40.7131C11.7072 40.8833 11.9093 41.0183 12.1316 41.1104C12.354 41.2025 12.5923 41.2499 12.833 41.2499H42.1673C42.4079 41.2499 42.6463 41.2025 42.8686 41.1104C43.091 41.0183 43.293 40.8833 43.4632 40.7131C43.6334 40.543 43.7684 40.3409 43.8605 40.1186C43.9526 39.8962 44 39.6579 44 39.4172V31.6245L27.4997 22Z" fill="#185C37"></path>
                                        <path d="M27.4996 22H43.9991V31.6245H27.4996V22Z" fill="#107C41"></path>
                                        <path opacity="0.1" d="M22.9145 9.625H11.0002V35.7499H22.9145C23.4003 35.7491 23.8661 35.5561 24.21 35.2131C24.5539 34.87 24.7481 34.4047 24.7501 33.919V11.4578C24.7486 10.9716 24.5546 10.5059 24.2107 10.1624C23.8667 9.81898 23.4006 9.62574 22.9145 9.625Z" fill="black"></path>
                                        <path opacity="0.2" d="M21.5425 11H11.0002V37.1249H21.5425C22.0278 37.1234 22.4929 36.9301 22.8362 36.5871C23.1795 36.2441 23.3733 35.7793 23.3753 35.294V12.8328C23.3738 12.3471 23.1802 11.8818 22.8368 11.5384C22.4935 11.195 22.0281 11.0015 21.5425 11Z" fill="black"></path>
                                        <path opacity="0.2" d="M21.5425 11H11.0002V34.3743H21.5425C22.0281 34.3729 22.4935 34.1793 22.8368 33.8359C23.1802 33.4925 23.3738 33.0272 23.3753 32.5416V12.8328C23.3738 12.3471 23.1802 11.8818 22.8368 11.5384C22.4935 11.195 22.0281 11.0015 21.5425 11Z" fill="black"></path>
                                        <path opacity="0.2" d="M20.1677 11H11.0002V34.3743H20.1677C20.6533 34.3729 21.1187 34.1793 21.462 33.8359C21.8054 33.4925 21.999 33.0272 22.0005 32.5416V12.8328C21.999 12.3471 21.8054 11.8818 21.462 11.5384C21.1187 11.195 20.6533 11.0015 20.1677 11Z" fill="black"></path>
                                        <path d="M1.83275 11H20.1696C20.6557 11 21.1218 11.1931 21.4655 11.5368C21.8092 11.8805 22.0023 12.3467 22.0023 12.8328V31.1696C22.0023 31.6556 21.8092 32.1218 21.4655 32.4655C21.1218 32.8092 20.6557 33.0023 20.1696 33.0023H1.83275C1.59184 33.0023 1.35329 32.9548 1.13074 32.8625C0.908203 32.7703 0.706038 32.635 0.535814 32.4645C0.365591 32.2941 0.23065 32.0917 0.13871 31.869C0.0467697 31.6463 -0.000364935 31.4077 2.12747e-06 31.1668V12.8328C2.12747e-06 12.3467 0.193095 11.8805 0.536803 11.5368C0.88051 11.1931 1.34668 11 1.83275 11Z" fill="#107C41"></path>
                                        <path d="M4.85785 28.8748L9.30709 21.9804L5.23203 15.125H8.51125L10.7359 19.5072C10.94 19.9224 11.0809 20.2326 11.1585 20.438H11.1873C11.3338 20.106 11.4873 19.7834 11.6481 19.47L14.0253 15.125H17.0355L12.8553 21.9422L17.137 28.8748H13.9369L11.367 24.0607C11.246 23.8558 11.1436 23.6406 11.0607 23.4175H11.0226C10.9476 23.6357 10.848 23.8446 10.7256 24.0402L8.08494 28.8748H4.85785Z" fill="white"></path>
                                    </g>
                                    <defs>
                                        <clipPath id="clip1_2145_7517">
                                            <rect width="44" height="44" fill="white"></rect>
                                        </clipPath>
                                    </defs>
                                </svg>
                                <p class="file-selected-name mb-2 mt-3" id="selectedFileName">file.xlsx</p>
                                <button class="amg-btn amg-btn-primary" type="button" style="min-width:203px" id="btnSave">
                                    <span>{{ trans("config.file_actions.save") }}</span>
                                </button>
                                <button class="amg-btn amg-btn-link amg-btn-link-plain mt-2" id="btnRemove">{{ trans("config.file_actions.remove_file") }}</button>
                            </div>
                        </div>

                        {{-- Hidden Form --}}
                        <form name="confirmForm" id="importForm" method="post" action="" enctype="multipart/form-data" style="display:none;">
                            {{ csrf_field() }}
                            <input type="file" name="allocation_group_import_file" id="hiddenFileInput">
                        </form>
                    </div>


                     <div class="col-12 col-sm-7">
                        <div class="right-panel h-100">
                            <a class="amg-btn amg-btn-white amg-btn-md" href="{!! CommonHelper::asset('samples/AutoAllocationGroupsImportFormat.xlsx') !!}" target="_blank">
                                <svg width="14" height="17" viewBox="0 0 14 17" fill="currentColor"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M9 3.99935L5 3.99935C4.45 3.99935 4 4.44935 4 4.99935L4 9.99935L2.41 9.99935C1.52 9.99935 1.07 11.0794 1.7 11.7094L6.29 16.2994C6.38251 16.3921 6.4924 16.4656 6.61338 16.5158C6.73435 16.566 6.86403 16.5918 6.995 16.5918C7.12597 16.5918 7.25565 16.566 7.37662 16.5158C7.4976 16.4656 7.60749 16.3921 7.7 16.2994L12.29 11.7094C12.92 11.0794 12.48 9.99935 11.59 9.99935L10 9.99935L10 4.99935C10 4.44935 9.55 3.99935 9 3.99935ZM13 1.99935L1 1.99935C0.450001 1.99935 1.31505e-06 1.54935 1.36313e-06 0.999352C1.41122e-06 0.449353 0.450001 -0.000647776 1 -0.000647728L13 -0.000646679C13.55 -0.000646631 14 0.449354 14 0.999353C14 1.54935 13.55 1.99935 13 1.99935Z"
                                        fill="currentColor" />
                                </svg>
                                <span class="font-weight-normal">{{ trans("content.common_doc_list.download_format") }}</span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Instructions --}}
            <div class="row justify-content-center">
                <div class="col-12 px-5">
                    <div class="instructions-card mt-3">
                        <h4 class="h4-text" style="color:#7F7F7F">{{ trans("auto-allocation-group.header") }}:</h4>
                        <div class="ps-3 ps-sm-4">
                            <p class="fst-italic s1-text mb-2 text-muted">
                                <i>{{ trans("auto-allocation-group.nvr_ch") }}</i>
                            </p>
                            <div class="card-scroll-body" id="cardScrollBody">
                                <ul class="ps-3">
                                    <li>
                                        <span class="me-3 required">{{ trans("auto-allocation-group.auto_allocation_group_company_name") }}</span>
                                        <span class="b1-text text-muted">{{ trans("auto-allocation-group.member_import.fields.company_name_help") }}</span>
                                    </li>
                                    <li>
                                        <span class="me-3 required">{{ trans("auto-allocation-group.auto_allocation_group_name") }}</span>
                                        <span class="b1-text text-muted">{{ trans("auto-allocation-group.auto_allocation_group_name_required") }}</span>
                                    </li>
                                    <li>
                                        <span class="me-3 required">{{ trans("auto-allocation-group.auto_allocation_group_desc") }}</span>
                                        <span class="b1-text text-muted">{{ trans("auto-allocation-group.auto_allocation_group_desc_required") }}</span>
                                    </li>
                                    <li>
                                        <span class="me-3 required">{{ trans("auto-allocation-group.auto_allocation_group_department_name") }}</span>
                                        <span class="b1-text text-muted">{{ trans("auto-allocation-group.auto_allocation_group_department_required") }}</span>
                                    </li>
                                    <li>
                                        <span class="me-3 required">{{ trans("auto-allocation-group.auto_allocation_group_approval_location") }}</span>
                                        <span class="b1-text text-muted">{{ trans("auto-allocation-group.auto_allocation_group_approval_location_required") }}</span>
                                    </li>
                                    <li>
                                        <span class="me-3">{{ trans("auto-allocation-group.auto_allocation_group_approval_location_for") }}</span>
                                        <span class="b1-text text-muted">{{ trans("auto-allocation-group.auto_allocation_group_approval_location_for_required") }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</section>
@endsection

@push('scripts')
<script>
$(function () {
    let droppedFile = null;

    function showSelectedState(name) {
        $('#selectedFileName').text(name);
        $('#defaultState').hide();
        $('#selectedState').show();
        $('#dropZone').addClass('file-selected');
        $('#fileInput').css('pointer-events', 'none');
    }

    function showDefaultState() {
        droppedFile = null;
        $('#defaultState').show();
        $('#selectedState').hide();
        $('#dropZone').removeClass('file-selected');
        $('#fileInput').css('pointer-events', '').val('');
    }

    $('#fileInput').on('change', function () {
        if (this.files.length > 0) {
            droppedFile = null;
            showSelectedState(this.files[0].name);
        }
    });

    $('#btnRemove').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        showDefaultState();
    });

    $('#btnSave').on('click', function () {
        let file = $('#fileInput')[0].files[0] || droppedFile;

        if (!file) {
            alert('No file selected.');
            return;
        }

        let ext = file.name.split('.').pop().toLowerCase();
        if (!['xlsx', 'xls'].includes(ext)) {
            alert('Invalid file type. Please upload an .xlsx or .xls file.');
            return;
        }

        let dt = new DataTransfer();
        dt.items.add(file);
        $('#hiddenFileInput')[0].files = dt.files;
        $('#importForm').submit();
    });

    $('#dropZone').on('dragover', function (e) {
        e.preventDefault();
        $(this).addClass('dragover');
    });

    $('#dropZone').on('dragleave', function () {
        $(this).removeClass('dragover');
    });

    $('#dropZone').on('drop', function (e) {
        e.preventDefault();
        $(this).removeClass('dragover');

        let files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            let ext = files[0].name.split('.').pop().toLowerCase();
            if (!['xlsx', 'xls'].includes(ext)) {
                alert('Invalid file type. Please drop an .xlsx or .xls file.');
                return;
            }
            droppedFile = files[0];
            showSelectedState(files[0].name);
        }
    });

    let expanded = false;

    $('#readMoreBtn').on('click', function (e) {
        e.preventDefault();
        expanded = !expanded;

        if (expanded) {
            $('#cardScrollBody').addClass('expanded');
            $(this).text("{{ trans('config.user_bulk_import_fields.read_less') }}");
        } else {
            $('#cardScrollBody').removeClass('expanded');
            $(this).text("{{ trans('config.user_import_fields.read_more') }}");
        }
    });
});
</script>
@endpush

@push('css')
    <link href="{!! CommonHelper::asset('newcss/select2.min.css') !!}" rel="stylesheet" />
@endpush

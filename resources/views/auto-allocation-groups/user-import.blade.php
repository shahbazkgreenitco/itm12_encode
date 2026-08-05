{{-- @page-meta
{
  "page_no": "USR05I-26",
  "file": "import.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "Sandeep Verma",
      "from": "2026-03",
      "reviewer": null,
      "description": "Initial setup"
    }
  ]
}
--}}
@extends('layouts.layout1')
@section('title', trans('auto-allocation-group.member_import.title'))

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

        [data-bs-theme="dark"] .drag-text{
            color:#fff;
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

        [data-bs-theme="dark"] .file-selected-name{
            color:#fff;
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

        .alert ul {
            list-style-type: disc;
            padding-left: 1.2rem;
        }
    </style>
  <!-- CONTENT -->
 <section class="content auto-allocation-import-page">
    <div class="header-actions-wrapper header-actions-white-wrapper d-flex align-items-center justify-content-between pe-4">
        
        <div class="d-flex align-items-center gap-1">
            <button onclick="location.href='{{ route('auto-allocation-groups') }}'" class="d-flex gap-3 align-items-center bg-transparent outline-none border-0" data-bs-toggle="tooltip" title="Back" >
                <svg width="16" height="21" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11.5662 0.440759C11.706 0.580116 11.817 0.745702 11.8927 0.928029C11.9684 1.11036 12.0074 1.30584 12.0074 1.50326C12.0074 1.70068 11.9684 1.89616 11.8927 2.07848C11.817 2.26081 11.706 2.4264 11.5662 2.56576L5.12993 9.00201L23.5049 9.00201C23.9028 9.00201 24.2843 9.16004 24.5656 9.44135C24.8469 9.72265 25.0049 10.1042 25.0049 10.502C25.0049 10.8998 24.8469 11.2814 24.5656 11.5627C24.2843 11.844 23.9028 12.002 23.5049 12.002L5.12993 12.002L11.5662 18.4408C11.848 18.7226 12.0063 19.1047 12.0063 19.5033C12.0063 19.9018 11.848 20.284 11.5662 20.5658C11.2844 20.8475 10.9022 21.0059 10.5037 21.0059C10.1052 21.0059 9.72298 20.8475 9.44118 20.5658L0.441182 11.5658C0.301343 11.4264 0.190387 11.2608 0.114679 11.0785C0.0389711 10.8962 -4.50154e-07 10.7007 -4.58778e-07 10.5033C-4.67401e-07 10.3058 0.038971 10.1104 0.114679 9.92803C0.190387 9.7457 0.301342 9.58011 0.441182 9.44076L9.44118 0.440759C9.58054 0.30092 9.74613 0.189964 9.92845 0.114255C10.1108 0.0385471 10.3063 -0.000425789 10.5037 -0.000425798C10.7011 -0.000425806 10.8966 0.038547 11.0789 0.114255C11.2612 0.189964 11.4268 0.30092 11.5662 0.440759Z" fill="currentColor"></path>
                </svg>
            </button>            
            <h3 class="h3-text mb-0"  > {{ trans('auto-allocation-group.member_import.title') }}</h3>
        </div>
    </div>

     <main class="main-content" id="mainContent">

        <div class="container-fluid pt-3 pb-5">
            @if(session('msg'))
                @php
                    $messageStatus = session('msg')['status'] ?? 'danger';
                    $sessionMessage = session('msg')['msg'] ?? '';
                    $alertFailMsgs = [];
                    if ($messageStatus === 'danger' && !empty($fail_msgs)) {
                        foreach ($fail_msgs as $failMsg) {
                            if ($failMsg !== $sessionMessage) {
                                $alertFailMsgs[] = $failMsg;
                            }
                        }
                    }
                @endphp
                <div class="alert customize-alert alert-dismissible alert-light-{{ $messageStatus }} bg-{{ $messageStatus }}-subtle text-{{ $messageStatus }} fade show remove-close-icon" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <div class="d-flex align-items-start me-3 gap-3 me-md-0">
                        <svg width="18px" height="18px" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" fill="none">
                            <path fill="currentColor" fill-rule="evenodd" d="M10 3a7 7 0 100 14 7 7 0 000-14zm-9 7a9 9 0 1118 0 9 9 0 01-18 0zm8-4a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1zm.01 8a1 1 0 102 0V9a1 1 0 10-2 0v5z"/>
                        </svg>
                        <div>
                            {{ $sessionMessage }}
                            @if(!empty($alertFailMsgs))
                                <ul class="mt-1 mb-0">
                                    @foreach($alertFailMsgs as $msg)
                                        <li>{{ $msg }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if((isset($success) || isset($fail)) && !session('msg'))

                

                @if($fail > 0)
                    <div class="alert customize-alert alert-dismissible alert-light-danger bg-danger-subtle text-danger fade show remove-close-icon" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <div class="d-flex align-items-start me-3 gap-2 me-md-0">
                            <svg width="18px" height="18px" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" fill="none">
                                <path fill="currentColor" fill-rule="evenodd" d="M10 3a7 7 0 100 14 7 7 0 000-14zm-9 7a9 9 0 1118 0 9 9 0 01-18 0zm8-4a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1zm.01 8a1 1 0 102 0V9a1 1 0 10-2 0v5z"/>
                            </svg>
                            <div>
                                {{ trans('auto-allocation-group.member_import.fail_summary', ['count' => $fail]) }}
                                @if(!empty($fail_msgs))
                                    <ul class="mt-1 mb-0">
                                        @foreach($fail_msgs as $msg)
                                            <li>{{ $msg }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if($success == 0 && $fail == 0 && !session('msg'))
                    <div class="alert customize-alert alert-dismissible alert-light-warning bg-warning-subtle text-warning fade show remove-close-icon" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <div class="d-flex align-items-center me-3 gap-3 me-md-0">
                            <svg width="18px" height="18px" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" fill="none">
                                <path fill="currentColor" fill-rule="evenodd" d="M10 3a7 7 0 100 14 7 7 0 000-14zm-9 7a9 9 0 1118 0 9 9 0 01-18 0zm8-4a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1zm.01 8a1 1 0 102 0V9a1 1 0 10-2 0v5z"/>
                            </svg>
                            {{ trans('auto-allocation-group.member_import.none_imported') }}
                        </div>
                    </div>
                @endif

            @endif
            <div class="import-wrapper">

                <div class="row g-3 align-items-stretch">
                    <div class="col-12 col-sm-3">
                        <div class="drop-zone" id="dropZone">
                            <input type="file" id="fileInput" accept=".xlsx,.xls" />

                            <div id="defaultState">
                                <svg width="30" height="30" viewBox="0 0 44 44" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_2145_7517)">
                                        <path
                                            d="M27.4997 2.75H12.833C12.3469 2.75 11.8808 2.94309 11.537 3.2868C11.1933 3.63051 11.0002 4.09668 11.0002 4.58275V12.3745L27.4997 21.999L35.7494 25.4802L43.9991 21.999V12.3745L27.4997 2.75Z"
                                            fill="#21A366" />
                                        <path d="M11.0002 12.375H27.4997V21.9995H11.0002V12.375Z" fill="#107C41" />
                                        <path
                                            d="M42.1663 2.75H27.4996V12.3745H43.9991V4.58275C43.9991 4.09668 43.806 3.63051 43.4623 3.2868C43.1185 2.94309 42.6524 2.75 42.1663 2.75Z"
                                            fill="#33C481" />
                                        <path
                                            d="M27.4997 22H11.0002V39.4172C11.0002 39.6579 11.0476 39.8962 11.1398 40.1186C11.2319 40.3409 11.3669 40.543 11.537 40.7131C11.7072 40.8833 11.9093 41.0183 12.1316 41.1104C12.354 41.2025 12.5923 41.2499 12.833 41.2499H42.1673C42.4079 41.2499 42.6463 41.2025 42.8686 41.1104C43.091 41.0183 43.293 40.8833 43.4632 40.7131C43.6334 40.543 43.7684 40.3409 43.8605 40.1186C43.9526 39.8962 44 39.6579 44 39.4172V31.6245L27.4997 22Z"
                                            fill="#185C37" />
                                        <path d="M27.4996 22H43.9991V31.6245H27.4996V22Z" fill="#107C41" />
                                        <path opacity="0.1"
                                            d="M22.9145 9.625H11.0002V35.7499H22.9145C23.4003 35.7491 23.8661 35.5561 24.21 35.2131C24.5539 34.87 24.7481 34.4047 24.7501 33.919V11.4578C24.7486 10.9716 24.5546 10.5059 24.2107 10.1624C23.8667 9.81898 23.4006 9.62574 22.9145 9.625Z"
                                            fill="black" />
                                        <path opacity="0.2"
                                            d="M21.5425 11H11.0002V37.1249H21.5425C22.0278 37.1234 22.4929 36.9301 22.8362 36.5871C23.1795 36.2441 23.3733 35.7793 23.3753 35.294V12.8328C23.3738 12.3471 23.1802 11.8818 22.8368 11.5384C22.4935 11.195 22.0281 11.0015 21.5425 11Z"
                                            fill="black" />
                                        <path opacity="0.2"
                                            d="M21.5425 11H11.0002V34.3743H21.5425C22.0281 34.3729 22.4935 34.1793 22.8368 33.8359C23.1802 33.4925 23.3738 33.0272 23.3753 32.5416V12.8328C23.3738 12.3471 23.1802 11.8818 22.8368 11.5384C22.4935 11.195 22.0281 11.0015 21.5425 11Z"
                                            fill="black" />
                                        <path opacity="0.2"
                                            d="M20.1677 11H11.0002V34.3743H20.1677C20.6533 34.3729 21.1187 34.1793 21.462 33.8359C21.8054 33.4925 21.999 33.0272 22.0005 32.5416V12.8328C21.999 12.3471 21.8054 11.8818 21.462 11.5384C21.1187 11.195 20.6533 11.0015 20.1677 11Z"
                                            fill="black" />
                                        <path
                                            d="M1.83275 11H20.1696C20.6557 11 21.1218 11.1931 21.4655 11.5368C21.8092 11.8805 22.0023 12.3467 22.0023 12.8328V31.1696C22.0023 31.6556 21.8092 32.1218 21.4655 32.4655C21.1218 32.8092 20.6557 33.0023 20.1696 33.0023H1.83275C1.59184 33.0023 1.35329 32.9548 1.13074 32.8625C0.908203 32.7703 0.706038 32.635 0.535814 32.4645C0.365591 32.2941 0.23065 32.0917 0.13871 31.869C0.0467697 31.6463 -0.000364935 31.4077 2.12747e-06 31.1668V12.8328C2.12747e-06 12.3467 0.193095 11.8805 0.536803 11.5368C0.88051 11.1931 1.34668 11 1.83275 11Z"
                                            fill="#107C41" />
                                        <path
                                            d="M4.85785 28.8748L9.30709 21.9804L5.23203 15.125H8.51125L10.7359 19.5072C10.94 19.9224 11.0809 20.2326 11.1585 20.438H11.1873C11.3338 20.106 11.4873 19.7834 11.6481 19.47L14.0253 15.125H17.0355L12.8553 21.9422L17.137 28.8748H13.9369L11.367 24.0607C11.246 23.8558 11.1436 23.6406 11.0607 23.4175H11.0226C10.9476 23.6357 10.848 23.8446 10.7256 24.0402L8.08494 28.8748H4.85785Z"
                                            fill="white" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_2145_7517">
                                            <rect width="44" height="44" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                                <p class="drag-text mt-2 mb-1">{{ trans("config.file_upload.drag_drop") }}</p>
                                <button class="amg-btn amg-btn-secondary upload-doc-btn">
                                    <svg width="14" height="17" viewBox="0 0 14 17" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M5 12.5924L9 12.5924C9.55 12.5924 10 12.1424 10 11.5924L10 6.59244L11.59 6.59244C12.48 6.59244 12.93 5.51244 12.3 4.88244L7.71 0.292444C7.61749 0.19974 7.5076 0.126193 7.38662 0.0760114C7.26565 0.02583 7.13597 4.15368e-10 7.005 4.07744e-10C6.87403 4.00121e-10 6.74435 0.02583 6.62338 0.0760114C6.5024 0.126193 6.39251 0.19974 6.3 0.292444L1.71 4.88244C1.08 5.51244 1.52 6.59244 2.41 6.59244L4 6.59244L4 11.5924C4 12.1424 4.45 12.5924 5 12.5924ZM1 14.5924L13 14.5924C13.55 14.5924 14 15.0424 14 15.5924C14 16.1424 13.55 16.5924 13 16.5924L1 16.5924C0.45 16.5924 -9.39615e-10 16.1424 -9.076e-10 15.5924C-8.75586e-10 15.0424 0.45 14.5924 1 14.5924Z"
                                            fill="currentColor" />
                                    </svg>
                                    <span>{{ trans("config.file_upload.upload_file") }}</span>
                                </button>
                                <span class="file-label">{{ trans("config.file_upload.no_file_selected") }}</span>
                            </div>

                            <div id="selectedState" style="display:none;">
                                <svg width="30" height="30" viewBox="0 0 44 44" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_2145_7517)">
                                        <path
                                            d="M27.4997 2.75H12.833C12.3469 2.75 11.8808 2.94309 11.537 3.2868C11.1933 3.63051 11.0002 4.09668 11.0002 4.58275V12.3745L27.4997 21.999L35.7494 25.4802L43.9991 21.999V12.3745L27.4997 2.75Z"
                                            fill="#21A366" />
                                        <path d="M11.0002 12.375H27.4997V21.9995H11.0002V12.375Z" fill="#107C41" />
                                        <path
                                            d="M42.1663 2.75H27.4996V12.3745H43.9991V4.58275C43.9991 4.09668 43.806 3.63051 43.4623 3.2868C43.1185 2.94309 42.6524 2.75 42.1663 2.75Z"
                                            fill="#33C481" />
                                        <path
                                            d="M27.4997 22H11.0002V39.4172C11.0002 39.6579 11.0476 39.8962 11.1398 40.1186C11.2319 40.3409 11.3669 40.543 11.537 40.7131C11.7072 40.8833 11.9093 41.0183 12.1316 41.1104C12.354 41.2025 12.5923 41.2499 12.833 41.2499H42.1673C42.4079 41.2499 42.6463 41.2025 42.8686 41.1104C43.091 41.0183 43.293 40.8833 43.4632 40.7131C43.6334 40.543 43.7684 40.3409 43.8605 40.1186C43.9526 39.8962 44 39.6579 44 39.4172V31.6245L27.4997 22Z"
                                            fill="#185C37" />
                                        <path d="M27.4996 22H43.9991V31.6245H27.4996V22Z" fill="#107C41" />
                                        <path opacity="0.1"
                                            d="M22.9145 9.625H11.0002V35.7499H22.9145C23.4003 35.7491 23.8661 35.5561 24.21 35.2131C24.5539 34.87 24.7481 34.4047 24.7501 33.919V11.4578C24.7486 10.9716 24.5546 10.5059 24.2107 10.1624C23.8667 9.81898 23.4006 9.62574 22.9145 9.625Z"
                                            fill="black" />
                                        <path opacity="0.2"
                                            d="M21.5425 11H11.0002V37.1249H21.5425C22.0278 37.1234 22.4929 36.9301 22.8362 36.5871C23.1795 36.2441 23.3733 35.7793 23.3753 35.294V12.8328C23.3738 12.3471 23.1802 11.8818 22.8368 11.5384C22.4935 11.195 22.0281 11.0015 21.5425 11Z"
                                            fill="black" />
                                        <path opacity="0.2"
                                            d="M21.5425 11H11.0002V34.3743H21.5425C22.0281 34.3729 22.4935 34.1793 22.8368 33.8359C23.1802 33.4925 23.3738 33.0272 23.3753 32.5416V12.8328C23.3738 12.3471 23.1802 11.8818 22.8368 11.5384C22.4935 11.195 22.0281 11.0015 21.5425 11Z"
                                            fill="black" />
                                        <path opacity="0.2"
                                            d="M20.1677 11H11.0002V34.3743H20.1677C20.6533 34.3729 21.1187 34.1793 21.462 33.8359C21.8054 33.4925 21.999 33.0272 22.0005 32.5416V12.8328C21.999 12.3471 21.8054 11.8818 21.462 11.5384C21.1187 11.195 20.6533 11.0015 20.1677 11Z"
                                            fill="black" />
                                        <path
                                            d="M1.83275 11H20.1696C20.6557 11 21.1218 11.1931 21.4655 11.5368C21.8092 11.8805 22.0023 12.3467 22.0023 12.8328V31.1696C22.0023 31.6556 21.8092 32.1218 21.4655 32.4655C21.1218 32.8092 20.6557 33.0023 20.1696 33.0023H1.83275C1.59184 33.0023 1.35329 32.9548 1.13074 32.8625C0.908203 32.7703 0.706038 32.635 0.535814 32.4645C0.365591 32.2941 0.23065 32.0917 0.13871 31.869C0.0467697 31.6463 -0.000364935 31.4077 2.12747e-06 31.1668V12.8328C2.12747e-06 12.3467 0.193095 11.8805 0.536803 11.5368C0.88051 11.1931 1.34668 11 1.83275 11Z"
                                            fill="#107C41" />
                                        <path
                                            d="M4.85785 28.8748L9.30709 21.9804L5.23203 15.125H8.51125L10.7359 19.5072C10.94 19.9224 11.0809 20.2326 11.1585 20.438H11.1873C11.3338 20.106 11.4873 19.7834 11.6481 19.47L14.0253 15.125H17.0355L12.8553 21.9422L17.137 28.8748H13.9369L11.367 24.0607C11.246 23.8558 11.1436 23.6406 11.0607 23.4175H11.0226C10.9476 23.6357 10.848 23.8446 10.7256 24.0402L8.08494 28.8748H4.85785Z"
                                            fill="white" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_2145_7517">
                                            <rect width="44" height="44" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                                <p class="file-selected-name mt-2 mb-1" id="selectedFileName">
                                    {{ trans("config.file_actions.default_filename") }}</p>
                                <button class="amg-btn amg-btn-primary save-doc-btn" type="button" id="btnSave">
                                    <svg width="14" height="17" viewBox="0 0 14 17" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M5 12.5924L9 12.5924C9.55 12.5924 10 12.1424 10 11.5924L10 6.59244L11.59 6.59244C12.48 6.59244 12.93 5.51244 12.3 4.88244L7.71 0.292444C7.61749 0.19974 7.5076 0.126193 7.38662 0.0760114C7.26565 0.02583 7.13597 4.15368e-10 7.005 4.07744e-10C6.87403 4.00121e-10 6.74435 0.02583 6.62338 0.0760114C6.5024 0.126193 6.39251 0.19974 6.3 0.292444L1.71 4.88244C1.08 5.51244 1.52 6.59244 2.41 6.59244L4 6.59244L4 11.5924C4 12.1424 4.45 12.5924 5 12.5924ZM1 14.5924L13 14.5924C13.55 14.5924 14 15.0424 14 15.5924C14 16.1424 13.55 16.5924 13 16.5924L1 16.5924C0.45 16.5924 -9.39615e-10 16.1424 -9.076e-10 15.5924C-8.75586e-10 15.0424 0.45 14.5924 1 14.5924Z"
                                            fill="currentColor" />
                                    </svg>
                                    <span>{{ trans("config.file_actions.save") }}</span>
                                </button>
                                <button class="amg-btn amg-btn-link amg-btn-link-plain p-0"
                                 id="btnRemove">{{ trans("config.file_actions.remove_file") }}</button>
                            </div>
                        </div>

                        <form id="importForm" method="POST" action="{{ route('auto-allocation-member-import') }}" enctype="multipart/form-data" style="display:none;">
                            @csrf
                            <input type="file" name="import_file" id="hiddenFileInput" />
                        </form>

                    </div>

                    <!-- Download Template -->
                    <div class="col-12 col-sm-9">
                        <div class="right-panel h-100">
                            <a class="amg-btn amg-btn-white amg-btn-md" href="{!! CommonHelper::asset('samples/AutoAllocationUserImportFormat.xlsx') !!}" target="_blank">
                                <svg width="14" height="17" viewBox="0 0 14 17" fill="currentColor"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M9 3.99935L5 3.99935C4.45 3.99935 4 4.44935 4 4.99935L4 9.99935L2.41 9.99935C1.52 9.99935 1.07 11.0794 1.7 11.7094L6.29 16.2994C6.38251 16.3921 6.4924 16.4656 6.61338 16.5158C6.73435 16.566 6.86403 16.5918 6.995 16.5918C7.12597 16.5918 7.25565 16.566 7.37662 16.5158C7.4976 16.4656 7.60749 16.3921 7.7 16.2994L12.29 11.7094C12.92 11.0794 12.48 9.99935 11.59 9.99935L10 9.99935L10 4.99935C10 4.44935 9.55 3.99935 9 3.99935ZM13 1.99935L1 1.99935C0.450001 1.99935 1.31505e-06 1.54935 1.36313e-06 0.999352C1.41122e-06 0.449353 0.450001 -0.000647776 1 -0.000647728L13 -0.000646679C13.55 -0.000646631 14 0.449354 14 0.999353C14 1.54935 13.55 1.99935 13 1.99935Z"
                                        fill="currentColor" />
                                </svg>
                                <span class="font-weight-normal">{{ trans('auto-allocation-group.member_import.download_template') }}</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
            
            <div class="row justify-content-center">
        
                <div class="col-12 px-3">
            
                    <div class="instructions-card mt-1">
            
                        <h4 class="fst-italic s1-text mb-2 text-muted" style="color:#7F7F7F">
                            {{ trans('auto-allocation-group.member_import.instructions_title') }}
                        </h4>
                
                        <div class="ps-3 ps-sm-4">

                            <p class="fst-italic s1-text mb-2 text-muted">
                                {{ trans('auto-allocation-group.member_import.instructions_note') }} <span class="text-danger required"></span> {{ trans('auto-allocation-group.member_import.fields_marked_mandatory') }}
                            </p>

                            <!-- Scrollable area -->
                            <div class="card-scroll-body" id="cardScrollBody">
                                <ul class="ps-3">
                                    <li>
                                        <span class="required">{{ trans('auto-allocation-group.member_import.fields.company_name') }}</span>
                                        <span class="b5-text text-muted"> - {{ trans('auto-allocation-group.member_import.fields.company_name_help') }}</span>
                                    </li>
                                    <li>
                                        <span class="required">{{ trans('auto-allocation-group.member_import.fields.department_name') }}</span>
                                        <span class="b5-text text-muted"> - {{ trans('auto-allocation-group.member_import.fields.department_name_help') }}</span>
                                    </li>
                                    <li>
                                        <span class="required">{{ trans('auto-allocation-group.member_import.fields.group') }}</span>
                                        <span class="b5-text text-muted"> - {{ trans('auto-allocation-group.member_import.fields.group_help') }}</span>
                                    </li>
                                    <li>
                                        <span class="required">{{ trans('auto-allocation-group.member_import.fields.username') }}</span>
                                        <span class="b5-text text-muted"> - {{ trans('auto-allocation-group.member_import.fields.username_help') }}</span>
                                    </li>
                                    <li>
                                        <span class="required">{{ trans('auto-allocation-group.member_import.fields.max_ticket_per_hour') }}</span>
                                        <span class="b5-text text-muted"> - {{ trans('auto-allocation-group.member_import.fields.max_ticket_per_hour_help') }}</span>
                                    </li>
                                    <li>
                                        <span class="required">{{ trans('auto-allocation-group.member_import.fields.max_ticket_per_day') }}</span>
                                        <span class="b5-text text-muted"> - {{ trans('auto-allocation-group.member_import.fields.max_ticket_per_day_help') }}</span>
                                    </li>
                                    <li>
                                        <span>{{ trans('auto-allocation-group.member_import.fields.all_location') }}</span>
                                        <span class="b5-text text-muted"> - {{ trans('auto-allocation-group.member_import.fields.all_location_help') }}</span>
                                    </li>
                                    <li>
                                        <span>{{ trans('auto-allocation-group.member_import.fields.location_access') }}</span>
                                        <span class="b5-text text-muted"> - {{ trans('auto-allocation-group.member_import.fields.location_access_help') }}</span>
                                    </li>
                                    <li>
                                        <span>{{ trans('auto-allocation-group.member_import.fields.internal_places') }}</span>
                                        <span class="b5-text text-muted"> - {{ trans('auto-allocation-group.member_import.fields.internal_places_help') }}</span>
                                    </li>
                                </ul>
                            </div>

                            <a class="amg-btn amg-btn-link amg-btn-link-plain" id="readMoreBtn">
                                {{ trans('auto-allocation-group.member_import.read_more') }}
                            </a>

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
        var droppedFile = null;
        var importText = {!! json_encode([
            'noFileSelected' => trans('auto-allocation-group.member_import.no_file_selected'),
            'invalidUploadType' => trans('auto-allocation-group.member_import.invalid_file_type_upload'),
            'invalidDropType' => trans('auto-allocation-group.member_import.invalid_file_type_drop'),
            'readMore' => trans('auto-allocation-group.member_import.read_more'),
            'readLess' => trans('auto-allocation-group.member_import.read_less'),
        ]) !!};

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
            e.stopPropagation();
            showDefaultState();
        });

        $('#btnSave').on('click', function (e) {
            e.stopPropagation();

            var file = $('#fileInput')[0].files[0] || droppedFile;

            if (!file) {
                alert(importText.noFileSelected);
                return;
            }

            var ext = file.name.split('.').pop().toLowerCase();
            if (!['xlsx', 'xls'].includes(ext)) {
                alert(importText.invalidUploadType);
                return;
            }

            var dt = new DataTransfer();
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

            var files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                var ext = files[0].name.split('.').pop().toLowerCase();
                if (!['xlsx', 'xls'].includes(ext)) {
                    alert(importText.invalidDropType);
                    return;
                }
                droppedFile = files[0];
                showSelectedState(files[0].name);
            }
        });

        var expanded = false;
    
        $('#readMoreBtn').on('click', function (e) {
            e.preventDefault();
            expanded = !expanded;

            if (expanded) {
                $('#cardScrollBody').addClass('expanded');
                $(this).text(importText.readLess);
            } else {
                $('#cardScrollBody').removeClass('expanded');
                $(this).text(importText.readMore);

                $('html, body').animate({
                    scrollTop: $('.instructions-card').offset().top - 20
                }, 300);
            }
        });

    });

    </script>
@endpush

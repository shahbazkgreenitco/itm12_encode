{{-- @page-meta
{
    "page_no": "VF05-26",
    "file": "view_form.blade.php",
    "versions": [
        {
        "version": "1.2",
        "writer": "Priya Maru",
        "from": "2026-05",
        "reviewer": null,
        "description": "Dynamic Form View"
        }
    ]
}
--}}

@extends('layouts.layout1')
@section('title', 'View Form')
@section('content')

<div class="srd-form-view-card">

    <!-- Header -->
    <div class="srd-form-header">
        <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
             <h3 class="h3-text mb-0"> {{ $view_form['form_name'] }}</h3>
        </div>
    </div>

    <!-- Body -->
    <main class="main-content" id="mainContent">
        <div class="srd-form-body">

            @foreach($valueArray as $v)

                @if(!empty($v['fieldValue']))

                    <div class="srd-form-row">

                        <!-- Label -->
                        <div class="srd-form-label">

                            {!! $v['fieldName'] !!}

                            @if(($v['required'] == true))
                                <sup class="text-danger">*</sup>
                            @endif

                        </div>

                        <!-- Value -->
                        <div class="srd-form-value">

                            @if(!is_array($v['fieldValue']))

                                @if($v['type'] == 'header')

                                    <<?= $v['subtype']; ?> class="mb-0 fw-bold">
                                        {!! $v['fieldValue'] !!}
                                    </<?= $v['subtype']; ?>>

                                @else

                                    <div class="text-dark">
                                        {!! $v['fieldValue'] !!}
                                    </div>

                                @endif

                            @else

                                @foreach($v['fieldValue'] as $key => $val)

                                    {{-- Badge Values --}}
                                    @if(!in_array($v['type'],['text','textarea','date','file','header']))

                                        @if(!is_array($val))

                                            <span class="badge rounded-pill bg-light text-dark border me-1 mb-1 px-3 py-2">
                                                {!! str_replace("-"," ",$val) !!}
                                            </span>

                                        @else

                                            @foreach($val as $k => $vv)

                                                <span class="badge rounded-pill bg-light text-dark border me-1 mb-1 px-3 py-2">
                                                    {!! str_replace("-"," ",$vv) !!}
                                                </span>

                                            @endforeach

                                        @endif

                                    {{-- File Attachments --}}
                                    @elseif(($v['type'] == 'file') && count($v['fieldValue']) > 0)

                                        @if(file_exists(public_path('uploads/request_form')."/".$val))

                                            @php
                                                $extension = strtolower(pathinfo($val, PATHINFO_EXTENSION));

                                                $isImage = in_array($extension, ['png','jpg','jpeg','gif','webp']);

                                                $icon = 'bi bi-file-earmark';

                                                if($extension == 'pdf'){
                                                    $icon = 'bi bi-file-earmark-pdf text-danger';
                                                }elseif(in_array($extension,['xls','xlsx','csv'])){
                                                    $icon = 'bi bi-file-earmark-excel text-success';
                                                }elseif(in_array($extension,['doc','docx'])){
                                                    $icon = 'bi bi-file-earmark-word text-primary';
                                                }elseif(in_array($extension,['zip'])){
                                                    $icon = 'bi bi-file-earmark-zip';
                                                }elseif(in_array($extension,['ppt','pptx'])){
                                                    $icon = 'bi bi-file-earmark-ppt text-warning';
                                                }elseif($isImage){
                                                    $icon = 'bi bi-image text-primary';
                                                }
                                            @endphp

                                            <div class="srd-attachment-item">

                                                <!-- Icon -->
                                                <div class="srd-attachment-icon">
                                                    <i class="{{ $icon }}"></i>
                                                </div>

                                                <!-- Name -->
                                                <div class="srd-attachment-name text-truncate">
                                                    {{ $val }}
                                                </div>

                                                <!-- Actions -->
                                                <div class="srd-attachment-actions">

                                                    @if($isImage)

                                                        <span class="tri-view text-secondary"
                                                            data-view_mode="1"
                                                            data-view="{{ url('/uploads/request_form/') }}/{{ $val }}"
                                                            data-name="{{ $val }}">

                                                            <i class="bi bi-eye"></i>

                                                        </span>

                                                    @endif

                                                    <a href="{{ url('uploads/request_form') }}/{{ $val }}"
                                                        download="{{ $val }}"
                                                        class="text-secondary ms-2">

                                                        <i class="bi bi-download"></i>

                                                    </a>

                                                </div>

                                            </div>

                                        @endif

                                    {{-- Normal Values --}}
                                    @else

                                        <div class="text-dark">
                                            {!! $val !!}
                                        </div>

                                    @endif

                                @endforeach

                            @endif

                        </div>

                    </div>

                @endif

            @endforeach

        </div>
    </main>

</div>
  
@endsection

@push('css')


<link href="{!! CommonHelper::asset('plugins/swipebox/css/swipebox.min.css') !!}" rel="stylesheet" />

<style>
    /* =========================
    DARK MODE
    ========================= */
    [data-bs-theme="dark"] .srd-form-view-card{
        background:#1f2937;
        border-color:#374151;
    }

    [data-bs-theme="dark"] .srd-form-header{
        background:#111827;
        border-bottom:1px solid #374151;
    }

    [data-bs-theme="dark"] .srd-form-body{
        background:#191919;
    }

    [data-bs-theme="dark"] .srd-form-row,{
        border-bottom:1px solid #374151;
    }

    [data-bs-theme="dark"] .srd-form-label{
        color:#d1d5db;
    }

    [data-bs-theme="dark"] .srd-form-value,
    [data-bs-theme="dark"] .srd-form-value .text-dark{
        color:#f3f4f6 !important;
    }

    [data-bs-theme="dark"] .srd-form-title,
    [data-bs-theme="dark"] .h3-text{
        color:#ffffff;
    }

    /* Badges */
    [data-bs-theme="dark"] .badge{
        background:#374151 !important;
        border-color:#4b5563 !important;
        color:#f3f4f6 !important;
    }

    /* Attachments */
    [data-bs-theme="dark"] .srd-attachment-item{
        background:#111827;
        border-color:#374151;
    }

    [data-bs-theme="dark"] .srd-attachment-icon{
        background:#1f2937;
        border-color:#4b5563;
        color:#f3f4f6;
    }

    [data-bs-theme="dark"] .srd-attachment-name{
        color:#f3f4f6;
    }

    [data-bs-theme="dark"] .srd-attachment-actions a,
    [data-bs-theme="dark"] .srd-attachment-actions span{
        color:#d1d5db !important;
    }

    [data-bs-theme="dark"] .srd-attachment-actions a:hover
    [data-bs-theme="dark"] .srd-attachment-actions span:hover{
        color:#ffffff !important;
    }

    .srd-form-view-card{
        background:#fff;
        border:1px solid #e9ecef;
        border-radius:16px;
        overflow:hidden;
    }
    .srd-form-title{
        font-size:20px;
        font-weight:700;
        color:#111827;
    }

    .srd-form-body{
        padding:22px;
    }

    .srd-form-row{
        display:flex;
        flex-wrap:wrap;
        padding:16px 0;
        gap:12px;
    }

    .srd-form-row:last-child{
        border-bottom:0;
    }

    .srd-form-label{
        width:260px;
        font-size:14px;
        font-weight:600;
        color:#374151;
    }

    .srd-form-value{
        min-width:250px;
        font-size:14px;
        color:#111827;
    }

    .srd-attachment-item{
        display:flex;
        align-items:center;
        gap:12px;
        border:1px solid #e9ecef;
        border-radius:12px;
        background:#f9fafb;
        padding:10px 12px;
        margin-bottom:10px;
    }

    .srd-attachment-icon{
        width:42px;
        height:42px;
        border-radius:10px;
        background:#fff;
        border:1px solid #e5e7eb;
        display:flex;
        align-items:center;
        justify-content:center;
        flex-shrink:0;
        font-size:18px;
    }

    .srd-attachment-name{
        flex:1;
        font-size:13px;
        font-weight:500;
        color:#111827;
    }

    .srd-attachment-actions{
        display:flex;
        align-items:center;
        font-size:16px;
        flex-shrink:0;
    }

    .badge{
        font-size:12px;
        font-weight:500;
    }

    @media(max-width:768px){

        .srd-form-row{
            flex-direction:column;
        }

        .srd-form-label{
            width:100%;
        }

    }

</style>

@endpush

@push('scripts')
<script src="{!! CommonHelper::asset('js/form-builder.min.js') !!}"></script>
<script src="{!! CommonHelper::asset('js/form-render.min.js') !!}"></script>
<script type="text/javascript" src="{!! CommonHelper::asset('plugins/swipebox/js/jquery.swipebox.min.js') !!}"></script>
    <script>
        var t = this;
        t.content = $(".srd-form-view-card");

        t.attachmentView = function(e) {
            e.preventDefault();
            var type = $(this).attr('data-view_mode');
            if (type == 1) {
                $.swipebox([{ href: $(this).attr('data-view'), title: $(this).attr('data-name') }]);
            }
        };

        t.attachmentDownload = function(e) {
            e.preventDefault();
            window.location = $(this).attr('data-url');
        }
        t.content.on("click", ".tri-view", $.proxy(t.attachmentView));
        t.content.on("click", ".tri-download", $.proxy(t.attachmentDownload));
    </script>
@endpush

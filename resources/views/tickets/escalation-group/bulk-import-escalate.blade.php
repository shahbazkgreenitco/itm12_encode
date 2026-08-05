{{--
/**
* ------------------------------------------------------------
* File: bulk-import-escalate.blade.php
* Module: Escalation Groups
* Page ID: ESG04ESG-26
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Sandeep Verma
* Created On: 2026-03
* Reviewed By: N/A
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial setup
* ------------------------------------------------------------
*/
--}}
@extends('layouts.layout1')

@section('title', trans("escalation_group.import_escalation"))

@section('content')

<section class="content">

    {{-- Header --}}
    <div class="header-actions-wrapper header-actions-white-wrapper d-flex align-items-center justify-content-between pe-4">
        <div class="py-3">
            <div class="container-fluid">

                <button onclick="location.href='{{ url('tickets/escalation-groups') }}'"
                    class="d-flex gap-3 align-items-center bg-transparent outline-none border-0">

                    <svg width="25" height="21" viewBox="0 0 25 21" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M11.5662 0.440759C11.706 0.580116 11.817 0.745702 11.8927 0.928029C11.9684 1.11036 12.0074 1.30584 12.0074 1.50326C12.0074 1.70068 11.9684 1.89616 11.8927 2.07848C11.817 2.26081 11.706 2.4264 11.5662 2.56576L5.12993 9.00201L23.5049 9.00201C23.9028 9.00201 24.2843 9.16004 24.5656 9.44135C24.8469 9.72265 25.0049 10.1042 25.0049 10.502C25.0049 10.8998 24.8469 11.2814 24.5656 11.5627C24.2843 11.844 23.9028 12.002 23.5049 12.002L5.12993 12.002L11.5662 18.4408C11.848 18.7226 12.0063 19.1047 12.0063 19.5033C12.0063 19.9018 11.848 20.284 11.5662 20.5658C11.2844 20.8475 10.9022 21.0059 10.5037 21.0059C10.1052 21.0059 9.72298 20.8475 9.44118 20.5658L0.441182 11.5658C0.301343 11.4264 0.190387 11.2608 0.114679 11.0785C0.0389711 10.8962 -4.50154e-07 10.7007 -4.58778e-07 10.5033C-4.67401e-07 10.3058 0.038971 10.1104 0.114679 9.92803C0.190387 9.7457 0.301342 9.58011 0.441182 9.44076L9.44118 0.440759C9.58054 0.30092 9.74613 0.189964 9.92845 0.114255C10.1108 0.0385471 10.3063 -0.000425789 10.5037 -0.000425798C10.7011 -0.000425806 10.8966 0.038547 11.0789 0.114255C11.2612 0.189964 11.4268 0.30092 11.5662 0.440759Z"
                            fill="currentColor" />
                    </svg>

                    <h2 class="h2-text mb-0">
                        {{ trans("escalation_group.import_escalation") }}
                    </h2>

                </button>

            </div>
        </div>
    </div>

    <main class="main-content" id="mainContent">

        <div class="container-fluid pt-3 pb-5">

            {{-- Success --}}
            @if(isset($success) && $success)
                <div class="alert customize-alert alert-dismissible alert-light-success bg-success-subtle text-success fade show remove-close-icon"
                    role="alert">

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

                    <div class="d-flex align-items-center gap-3">
                        Escalation Successfully Imported, Records Count(s): {{ $success }}
                    </div>

                </div>
            @endif

            {{-- Fail --}}
            @if(isset($fail) && $fail)
                <div class="alert customize-alert alert-dismissible alert-light-danger bg-danger-subtle text-danger fade show remove-close-icon"
                    role="alert">

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

                    <div>
                        Failure records count: {{ $fail }}

                        @if(isset($fail_msgs) && count($fail_msgs))
                            <ul class="mt-2 mb-0">
                                @foreach($fail_msgs as $m)
                                    <li>{{ $m }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                </div>
            @endif

            {{-- File --}}
            @if(isset($given_file_original_name) && $given_file_original_name)
                <div class="alert alert-secondary fade show" role="alert">

                    File name :

                    <a download="{{ $given_file_original_name }}"
                        href="{{ url('uploads/bulk_documents/',[$doc_link]) }}">

                        {{ $given_file_original_name }}

                    </a>

                </div>
            @endif

            {{-- Import Wrapper --}}
            <div class="import-wrapper">

                <div class="row g-3 align-items-stretch">

                    {{-- Upload Section --}}
                    <div class="col-12 col-sm-5">

                        <div class="drop-zone" id="dropZone">

                            <input type="file"
                                id="fileInput"
                                accept=".xlsx,.xls">

                            {{-- Default --}}
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

                                <p class="drag-text mt-3 mb-2 b1-text">
                                    {{ trans("config.file_upload.drag_drop") }}
                                </p>

                                <button class="amg-btn amg-btn-secondary"
                                    style="min-width:203px;">

                                    <span>
                                        {{ trans("config.file_upload.upload_file") }}
                                    </span>

                                </button>

                                <span class="file-label b1-text">
                                    {{ trans("config.file_upload.no_file_selected") }}
                                </span>

                            </div>

                            {{-- Selected --}}
                            <div id="selectedState" style="display:none;">

                                <img src="{{ CommonHelper::asset('images/excel-icon.svg') }}"
                                    width="44"
                                    alt="Excel">

                                <p class="file-selected-name mb-2 mt-3"
                                    id="selectedFileName">

                                    file.xlsx

                                </p>

                                <button class="amg-btn amg-btn-primary"
                                    style="min-width:203px"
                                    type="button"
                                    id="btnSave">

                                    <span>Upload</span>

                                </button>

                                <button class="amg-btn amg-btn-link amg-btn-link-plain mt-2"
                                    id="btnRemove">

                                    Remove File

                                </button>

                            </div>

                        </div>

                        {{-- Hidden Form --}}
                        <form name="confirmForm"
                            id="importForm"
                            method="post"
                            action="{{ url('tickets/bulk-escalation-import') }}"
                            enctype="multipart/form-data"
                            style="display:none;">

                            {{ csrf_field() }}

                            <input type="file"
                                name="import_file"
                                id="hiddenFileInput">

                        </form>

                    </div>

                    {{-- Download --}}
                    <div class="col-12 col-sm-7">

                        <div class="right-panel h-100">

                            <a class="amg-btn amg-btn-white amg-btn-lg"
                                target="_blank"
                                href="{!! CommonHelper::asset('samples/EscalationGroupBulkImport.xlsx') !!}">

                                <span>
                                    {{ trans("content.common_doc_list.download_format") }}
                                </span>

                            </a>

                        </div>

                    </div>

                </div>

            </div>

            {{-- Instructions --}}
            <div class="row justify-content-center">

                <div class="col-12 px-5">

                    <div class="instructions-card mt-3">

                        <h4 class="b2-text" style="color:#7F7F7F">
                            {{ trans("content.download_format.header") }} :
                        </h4>
                        <div class="ps-3 ps-sm-4">
                            <p class="fst-italic b1-text mb-2">
                                {{ trans("content.download_format.nvr_ch") }}
                            </p>
                            <div class="card-scroll-body" id="cardScrollBody">
                                <ul class="ps-3">
                                    <li>
                                        <span class="b3-text me-1 required">
                                            {{ trans("escalation_group.escalate_group_name") }}
                                        </span>

                                        <span class="b5-text text-muted">
                                            {{ trans("escalation_group.escalate_group_name_desc") }}
                                        </span>
                                    </li>

                                    <li>
                                        <span class="b3-text me-1 required">
                                            {{ trans("escalation_group.company_name") }}
                                        </span>

                                        <span class="b5-text text-muted">
                                            {{ trans("escalation_group.company_name_desc") }}
                                        </span>
                                    </li>

                                    <li>
                                        <span class="b3-text me-1 required">
                                            {{ trans("content.download_format.department_usr") }}
                                        </span>

                                        <span class="b5-text text-muted">
                                            {{ trans("content.download_format.department_usr_field") }}
                                        </span>
                                    </li>

                                    <li>
                                        <span class="b3-text me-1 required">
                                            {{ trans("content.download_format.category_name") }}
                                        </span>

                                        <span class="b5-text text-muted">
                                            {{ trans("escalation_group.problem_category_desc") }}
                                        </span>
                                    </li>

                                    <li>
                                        <span class="b3-text me-1">
                                            {{ trans("escalation_group.sub_category_label") }}
                                        </span>

                                        <span class="b5-text text-muted">
                                            {{ trans("escalation_group.sub_category_desc") }}
                                        </span>
                                    </li>

                                    <li>
                                        <span class="b3-text me-1 required">
                                            {{ trans("escalation_group.user_name") }}
                                        </span>

                                        <span class="b5-text text-muted">
                                            {{ trans("escalation_group.escalate_value_desc") }}
                                        </span>
                                    </li>

                                    <li>
                                        <span class="b3-text me-1 required">
                                            {{ trans("escalation_group.form.location_based") }}
                                        </span>

                                        <span class="b5-text text-muted">
                                            {{ trans("escalation_group.location_based_desc") }}
                                        </span>
                                    </li>

                                    <li>
                                        <span class="b3-text me-1">
                                            {{ trans("escalation_group.access_location") }}
                                        </span>

                                        <span class="b5-text text-muted">
                                            {{ trans("escalation_group.location_access_desc") }}
                                        </span>
                                    </li>

                                    <li>
                                        <span class="b3-text me-1">
                                            {{ trans("escalation_group.internal_location_access") }}
                                        </span>

                                        <span class="b5-text text-muted">
                                            {{ trans("escalation_group.internal_location_access_desc") }}
                                        </span>
                                    </li>

                                </ul>

                            </div>

                            <a class="amg-btn amg-btn-link amg-btn-link-plain"
                                id="readMoreBtn">

                                {{ trans("config.user_import_fields.read_more") }}

                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </main>

</section>

@endsection

@push('css')
<style>

    .import-wrapper{
        padding:2rem;
        width:100%;
    }

    .drop-zone{
        border:2px dashed #cbd5e1;
        border-radius:12px;
        background:#f8fafc;
        padding:2.5rem 1.5rem;
        text-align:center;
        cursor:pointer;
        transition:all .3s ease;
        position:relative;
    }

    .drop-zone:hover,
    .drop-zone.dragover{
        border-color:#F12F35;
        background:#f12f3510;
    }

    .drop-zone input[type="file"]{
        position:absolute;
        inset:0;
        opacity:0;
        cursor:pointer;
        width:100%;
        height:100%;
    }

    .file-label{
        display:block;
        margin-top:.75rem;
        opacity:.6;
    }

    .file-selected-name{
        font-weight:500;
    }

    .right-panel{
        display:flex;
        align-items:end;
        height:100%;
    }

    .instructions-card{
        background:#fff;
        border:1px solid #DFDFE1;
        border-radius:12px;
        padding:1.5rem 1.75rem;
    }

    .card-scroll-body{
        max-height:220px;
        overflow-y:auto;
        overflow-x:hidden;
    }

    .card-scroll-body.expanded{
        max-height:none;
        overflow:visible;
    }

    .card-scroll-body::-webkit-scrollbar{
        width:4px;
    }

    .card-scroll-body::-webkit-scrollbar-thumb{
        background:#cbd5e1;
        border-radius:10px;
    }

    #btnRemove{
        text-decoration:underline;
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

    @media(max-width:575px){

        .import-wrapper{
            padding:1rem;
        }

        .right-panel{
            margin-top:1rem;
        }

    }

</style>
@endpush

@push('scripts')
<script>

    $(function () {

        let droppedFile = null;

        function showSelectedState(name){

            $('#selectedFileName').text(name);

            $('#defaultState').hide();
            $('#selectedState').show();

            $('#dropZone').addClass('file-selected');

            $('#fileInput').css('pointer-events','none');

        }

        function showDefaultState(){

            droppedFile = null;

            $('#defaultState').show();
            $('#selectedState').hide();

            $('#dropZone').removeClass('file-selected');

            $('#fileInput').css('pointer-events','').val('');

        }

        $('#fileInput').on('change', function(){

            if(this.files.length > 0){

                droppedFile = null;

                showSelectedState(this.files[0].name);

            }

        });

        $('#btnRemove').on('click', function(e){

            e.preventDefault();

            showDefaultState();

        });

        $('#btnSave').on('click', function(){

            let file = $('#fileInput')[0].files[0] || droppedFile;

            if(!file){

                alert('No file selected.');
                return;

            }

            let ext = file.name.split('.').pop().toLowerCase();

            if(!['xlsx','xls'].includes(ext)){

                alert('Invalid file type.');
                return;

            }

            let dt = new DataTransfer();

            dt.items.add(file);

            $('#hiddenFileInput')[0].files = dt.files;

            $('#importForm').submit();

        });

        $('#dropZone').on('dragover', function(e){

            e.preventDefault();

            $(this).addClass('dragover');

        });

        $('#dropZone').on('dragleave', function(){

            $(this).removeClass('dragover');

        });

        $('#dropZone').on('drop', function(e){

            e.preventDefault();

            $(this).removeClass('dragover');

            let files = e.originalEvent.dataTransfer.files;

            if(files.length > 0){

                let ext = files[0].name.split('.').pop().toLowerCase();

                if(!['xlsx','xls'].includes(ext)){

                    alert('Invalid file type.');
                    return;

                }

                droppedFile = files[0];

                showSelectedState(files[0].name);

            }

        });

        let expanded = false;

        $('#readMoreBtn').on('click', function(e){

            e.preventDefault();

            expanded = !expanded;

            if(expanded){

                $('#cardScrollBody').addClass('expanded');

                $(this).text("{{ trans('config.user_bulk_import_fields.read_less') }}");

            }else{

                $('#cardScrollBody').removeClass('expanded');

                $(this).text("{{ trans('config.user_import_fields.read_more') }}");

            }

        });

    });

</script>
@endpush
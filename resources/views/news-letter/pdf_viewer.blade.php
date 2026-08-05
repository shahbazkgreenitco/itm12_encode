@extends('layouts.layout1')
@section('title', trans('newsletter/ongoing.newsletter'))

@section('content')
<div id="main-newsletter-wrapper">
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
        <h3 class="h3-text mb-0">{{ trans('sidebar.newletter') }}</h3>
        <div class="d-flex gap-8">
        </div>
    </div>

    <main class="main-content" id="mainContent">
        <div class="container-fluid px-0">
            <div class="card rounded-0">
                <div class="card-body p-0">
                    <div class="pdf-container" style="height: calc(100vh - 180px);">
                        <iframe
                            src='{{ asset("pdfjs/web/viewer.html") }}?file={{ urlencode(asset($name)) }}'
                            style="width:100%; height:100%; border:none; display:block;">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@push('css')
    <style>
        #main-newsletter-wrapper .pdf-container {
            height: calc(100vh - 180px);
            min-height: 500px;
        }
        @media (max-width: 768px) {
            #main-newsletter-wrapper .pdf-container {
                height: calc(100vh - 140px);
                min-height: 300px;
            }
        }
    </style>
@endpush

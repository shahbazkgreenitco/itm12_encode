{{-- @page-meta
{
  "page_no": "DYF03-03",
  "file": "form_view_history.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "Muzaffar Shaikh",
      "from": "2026-05-15",
      "reviewer": null,
      "description": "Implemented Dynamic Form History View"
    }
  ]
}
--}}
@extends('layouts.layout1')
@section('title', trans('content.dynamic_form.dynamic_form_history_view'))

@section('content')
<div id="history-view-wrapper">
    <div class="header-actions-wrapper header-actions-white-wrapper d-flex align-items-center justify-content-between pe-4">
        <div class="py-3">
            <div class="container-fluid ps-0">
                <button type="button"
                    class="bg-transparent border-0 d-flex align-items-center gap-2"
                    onclick="window.location.href='{{ url()->previous() }}'">
                    <svg width="20" height="20" viewBox="0 0 25 21" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                        <path
                                d="M11.5662 0.440759C11.706 0.580116 11.817 0.745702 11.8927 0.928029C11.9684 1.11036 12.0074 1.30584 12.0074 1.50326C12.0074 1.70068 11.9684 1.89616 11.8927 2.07848C11.817 2.26081 11.706 2.4264 11.5662 2.56576L5.12993 9.00201L23.5049 9.00201C23.9028 9.00201 24.2843 9.16004 24.5656 9.44135C24.8469 9.72265 25.0049 10.1042 25.0049 10.502C25.0049 10.8998 24.8469 11.2814 24.5656 11.5627C24.2843 11.844 23.9028 12.002 23.5049 12.002L5.12993 12.002L11.5662 18.4408C11.848 18.7226 12.0063 19.1047 12.0063 19.5033C12.0063 19.9018 11.848 20.284 11.5662 20.5658C11.2844 20.8475 10.9022 21.0059 10.5037 21.0059C10.1052 21.0059 9.72298 20.8475 9.44118 20.5658L0.441182 11.5658C0.301343 11.4264 0.190387 11.2608 0.114679 11.0785C0.0389711 10.8962 -4.50154e-07 10.7007 -4.58778e-07 10.5033C-4.67401e-07 10.3058 0.038971 10.1104 0.114679 9.92803C0.190387 9.7457 0.301342 9.58011 0.441182 9.44076L9.44118 0.440759C9.58054 0.30092 9.74613 0.189964 9.92845 0.114255C10.1108 0.0385471 10.3063 -0.000425789 10.5037 -0.000425798C10.7011 -0.000425806 10.8966 0.038547 11.0789 0.114255C11.2612 0.189964 11.4268 0.30092 11.5662 0.440759Z"
                            fill="currentColor"></path>
                    </svg>
                    <h2 class="h2-text mb-0">{{ trans('content.dynamic_form.dynamic_form_history_view') }}</h2>
                </button>
            </div>
        </div>
    </div>

    <main class="main-content" id="mainContent">
        <div class="container-fluid px-0">
            <div class="card rounded-0">
                <div class="card-body">

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                {{ trans('content.dynamic_form.name') }}
                            </label>
                            <input type="text" class="form-control"
                                value="{{ $form->form_name }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                {{ trans('content.dynamic_form.description') }}
                            </label>
                            <input type="text" class="form-control"
                                value="{{ $form->descriptions }}" readonly>
                        </div>
                    </div>

                    <hr class="mb-4">

                    <div id="fb-render-view"></div>

                </div>
            </div>
        </div>
    </main>

</div>
@endsection

@push('css')
    <style>
        #fb-render-view input,
        #fb-render-view textarea,
        #fb-render-view select {
            pointer-events: none;
            background-color: #f8f9fa;
            opacity: 0.85;
        }
        #fb-render-view button[type="submit"] {
            display: none;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
    <script src="{!! CommonHelper::asset('js/form-builder.min.js') !!}"></script>
    <script src="{!! CommonHelper::asset('js/form-render.min.js') !!}"></script>
   =@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
    <script src="{!! CommonHelper::asset('js/form-builder.min.js') !!}"></script>
    <script src="{!! CommonHelper::asset('js/form-render.min.js') !!}"></script>
    <script>
        $(document).ready(function () {

            console.log("formRender available:", typeof $.fn.formRender);
            
            var rawFields = {!! json_encode($form->fields) !!};
            console.log("rawFields type:", typeof rawFields);
            console.log("rawFields value:", rawFields);

            var formData = rawFields;

            if (typeof formData === "string") {
                try {
                    formData = JSON.parse(formData);
                    console.log("Parsed formData:", formData);
                } catch(e) {
                    console.error("JSON.parse failed:", e);
                }
            }

            if (formData && formData.length > 0) {
                try {
                    $("#fb-render-view").formRender({
                        formData: JSON.stringify(formData)  
                    });
                } catch(e) {
                    console.error("formRender error:", e);
                    $("#fb-render-view").html('<p class="text-muted">{{ trans("content.dynamic_form.could_not_render") }}</p>');
                }
            } else {
                $("#fb-render-view").html('<p class="text-muted">{{ trans("content.dynamic_form.form_name_required") }}</p>');
            }
        });
    </script>
@endpush
@endpush
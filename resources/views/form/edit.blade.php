{{-- @page-meta
{
  "page_no": "DYF04-05",
  "file": "edit.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "Muzaffar Shaikh",
      "from": "2026-05-15",
      "reviewer": null,
      "description": "Implemented Edit Dynamic Form UI with Edit functionality"
    }
  ]
}
--}}
@extends('layouts.layout1')
@section('title', trans('content.dynamic_form.edit_form'))

@section('content')
    <div id="edit-form-wrapper">
        <div
            class="header-actions-wrapper header-actions-white-wrapper d-flex align-items-center justify-content-between pe-4">
            <div class="py-3">
                <div class="container-fluid ps-0">
                    <button type="button" class="bg-transparent border-0 d-flex align-items-center gap-2"
                        onclick="window.location.href='{{ url('dynamic_form') }}'">
                        <svg width="20" height="20" viewBox="0 0 25 21" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M11.5662 0.440759C11.706 0.580116 11.817 0.745702 11.8927 0.928029C11.9684 1.11036 12.0074 1.30584 12.0074 1.50326C12.0074 1.70068 11.9684 1.89616 11.8927 2.07848C11.817 2.26081 11.706 2.4264 11.5662 2.56576L5.12993 9.00201L23.5049 9.00201C23.9028 9.00201 24.2843 9.16004 24.5656 9.44135C24.8469 9.72265 25.0049 10.1042 25.0049 10.502C25.0049 10.8998 24.8469 11.2814 24.5656 11.5627C24.2843 11.844 23.9028 12.002 23.5049 12.002L5.12993 12.002L11.5662 18.4408C11.848 18.7226 12.0063 19.1047 12.0063 19.5033C12.0063 19.9018 11.848 20.284 11.5662 20.5658C11.2844 20.8475 10.9022 21.0059 10.5037 21.0059C10.1052 21.0059 9.72298 20.8475 9.44118 20.5658L0.441182 11.5658C0.301343 11.4264 0.190387 11.2608 0.114679 11.0785C0.0389711 10.8962 -4.50154e-07 10.7007 -4.58778e-07 10.5033C-4.67401e-07 10.3058 0.038971 10.1104 0.114679 9.92803C0.190387 9.7457 0.301342 9.58011 0.441182 9.44076L9.44118 0.440759C9.58054 0.30092 9.74613 0.189964 9.92845 0.114255C10.1108 0.0385471 10.3063 -0.000425789 10.5037 -0.000425798C10.7011 -0.000425806 10.8966 0.038547 11.0789 0.114255C11.2612 0.189964 11.4268 0.30092 11.5662 0.440759Z"
                                fill="currentColor"></path>
                        </svg>
                        <h2 class="h2-text mb-0">{{ trans('content.dynamic_form.edit_form') }}</h2>
                    </button>
                </div>
            </div>
        </div>

        <main class="main-content" id="mainContent">
            <div class="container-fluid px-0">
                <div class="card rounded-0">
                    <div class="card-body">
                        <form id="dynamic_form_edit" name="dynamic_form1" novalidate>
                            @csrf
                            <input type="hidden" name="id" id="form_id" value="{{ $form->id }}">
                            <div class="row mb-3">
                                <div class="col-md-6 amg-form-field amg-form-field-row">
                                    <label for="form_name" class="form-label fw-semibold">
                                        {{ trans('content.dynamic_form.name') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                    <input type="text" name="form_name" id="form_name" class="form-control"
                                        value="{{ $form->form_name }}" placeholder="{{ trans('content.dynamic_form.enter_form_name') }}">
                                </div>
                                </div>

                                <div class="col-md-6 amg-form-field amg-form-field-row">
                                    <label for="descriptions" class="form-label fw-semibold">
                                        {{ trans('content.dynamic_form.description') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                    <input type="text" name="descriptions" id="descriptions" class="form-control"
                                        value="{{ $form->descriptions }}" placeholder="{{ trans('content.dynamic_form.enter_description') }}">
                                </div>
                            </div>
                            </div>

                            <!-- Hidden Field -->
                            <div class="col-md-6 amg-form-field amg-form-field-row d-none">
                                <label class="control-label mandatory" for="descriptions"></label>
                                <div class="input-group">
                                        <textarea name="fields" id="fields" class="form-control"></textarea>
                                </div>
                            </div>

                            <!-- Form Builder -->
                            <div id="build-wrap"></div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    @include('form.depends_modal')
@endsection

@push('css')
    <link href="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.css') !!}" rel="stylesheet" />
    <style>
        .frmb {
            height: 500px !important;
            overflow-y: scroll !important;
            scrollbar-width: thin !important;
        }

        [data-bs-theme=dark] #dynamic_form_edit .form-control,
        [data-bs-theme=dark] .form-select {
            border-color: #262323 !important;
            color: #fff9 !important;
            background-color: #2A2A2A !important;
        }

        [data-bs-theme=dark] .form-wrap.form-builder.formbuilder-embedded-bootstrap .formbuilder-autocomplete .form-control {
            background: #fff;
            color: #000 !important;
        }

        [data-bs-theme="dark"] .form-wrap.form-builder .frmb-control li {
            background: #2A2A2A !important;
            border: 1px solid #262323 !important;
        }

        [data-bs-theme="dark"] .form-wrap.form-builder .frmb-control li:hover {
            background-color: #3f3f3f !important;
        }

        [data-bs-theme="dark"] .form-wrap.form-builder .frmb .prev-holder label {
            color: #000 !important;
        }

        [data-bs-theme="dark"] .form-wrap.form-builder .frmb li.form-field {
            background-color: #2A2A2A;
            color: #fff
        }

        /* [data-bs-theme=dark] .form-control,
        [data-bs-theme=dark] .form-select {
            border-color: #313e54;
            color: #fff9;
            background-color: #2A2A2A;
        } */

        [data-bs-theme=dark] .form-wrap.form-builder .frmb li.form-field .field-label,
        [data-bs-theme=dark] .form-wrap.form-builder .frmb .option-actions button,
        [data-bs-theme=dark] .form-wrap.form-builder .frmb .option-actions a,
        [data-bs-theme="dark"] .form-wrap.form-builder .frmb .prev-holder .formbuilder-radio label,
        [data-bs-theme="dark"] .form-wrap.form-builder .frmb .prev-holder .formbuilder-checkbox label {
            color: #fff9 !important;
        }

        [data-bs-theme=dark] .form-wrap.form-builder .frmb .form-elements {
            background: #353535;
            border: 1px solid #4a4a4a;
        }

        [data-bs-theme=dark] .form-wrap.form-builder .frmb .form-elements input[type=text],
        [data-bs-theme=dark] .form-wrap.form-builder .frmb .form-elements [contenteditable].form-control {
            color: #000 !important;
        }

        [data-bs-theme=dark] .form-,
        [data-bs-theme=dark] .form-wrap.form-builder .frmb .option-actions button,
        [data-bs-theme=dark] .form-wrap.form-builder .frmb .option-actions a,
        [data-bs-theme=dark] .form-wrap.form-builder .frmb .form-elements select,
        [data-bs-theme=dark] .form-wrap.form-builder.formbuilder-embedded-bootstrap .form-control::placeholder {
            color: #000 !important;
        }

        [data-bs-theme=dark] .form-control[type=file]::file-selector-button {
            background-color: #353535;
            !important;
            color: #fff !important;
        }

        [data-bs-theme=dark] .form-wrap.form-builder .frmb li.deleting,
        [data-bs-theme=dark] .form-wrap.form-builder .frmb li.delete:hover,
        [data-bs-theme=dark] .form-wrap.form-builder .frmb li:hover li.delete:hover,
        [data-bs-theme=dark] .form-wrap.form-builder .frmb li.form-field.hidden-field {
            background-color: #2A2A2A !important;
        }

        [data-bs-theme=dark] .form-wrap.form-builder .frmb .prev-holder input[type=text],
        [data-bs-theme=dark] .form-wrap.form-builder .frmb .prev-holder input[type=number],
        [data-bs-theme=dark] .form-wrap.form-builder .frmb textarea,
        [data-bs-theme=dark] .form-wrap.form-builder .frmb input[type=number] {
            color: black;
        }

        [data-bs-theme=dark] .form-wrap.form-builder .frmb .prev-holder select {
            color: black;
        }

        [data-bs-theme=dark] .form-wrap.form-builder .frmb li.deleting,
        [data-bs-theme=dark] .form-wrap.form-builder .frmb li.delete:hover,
        [data-bs-theme=dark] .form-wrap.form-builder .frmb li:hover li.delete:hover {
            background-color: #2A2A2A !important;
        }

        [data-bs-theme=dark] .form-wrap.form-builder .stage-wrap.empty {
            border: 3px dashed #2A2A2A;
            background: #4a4a4a;
        }
        .form-field {
            border: 1px solid #dcdcdc;
            border-radius: 6px;
            background: #fff;
            padding: 12px;
            margin-bottom: 12px;
            transition: all 0.2s ease;
        }
        #edit-form-wrapper .error {
            display: block;
            margin: 0;
            color: #F12F35;
            font-size: 12px;
            font-weight: 500;
            line-height: 1.4;
        }
    </style>
@endpush

@push('scripts')
    <script src="{!! CommonHelper::asset('assets/libs/jquery-ui/dist/jquery-ui.min.js') !!}"></script>
    <script src="{!! CommonHelper::asset('js/form-builder.min.js') !!}"></script>
    <script src="{!! CommonHelper::asset('js/form/depends_setup.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/form/editform.js') !!}"></script>
    <script>
        $(document).ready(function() {
            var config = {};
            config.url = {};
            config.url.form_list = "{{ url('dynamic_form') }}";
            config.url.update = "{{ url('dynamic_form/update') }}";
            config.token = "{{ csrf_token() }}";
            config.form = {!! json_encode($form) !!};
            config.translations = {
                edit_form: '{{ trans('content.dynamic_form.edit_form') }}',
                something_wrong:'{{ trans('content.dynamic_form.something_wrong') }}',
                atleast_one_field:'{{ trans('content.dynamic_form.atleast_one_field') }}',
                form_name_required:'{{ trans('content.dynamic_form.form_name_required') }}',
                description_required:'{{ trans('content.dynamic_form.description_required') }}',
                save: '{{ trans('content.dynamic_form.save') }}',
                cancel: '{{ trans('content.dynamic_form.cancel') }}',
            };
            jQuery(function($) {
                var test = config.form.fields;
                var fbTemplate = document.getElementById('build-wrap'),
                $formContainer = $(document.getElementById('fields')),
                options = {
                    formData: test,
                    // disabledActionButtons: ['data'],
                    onSave: function() {
                        $formContainer.val(formBuilder.formData);
                    },
                    controlOrder: [
                        'text',
                        'textarea'
                    ]
                };
                myFormBuilder = $(fbTemplate).formBuilder(addDependsOn({
                    // add your option here
                    formData: test,
                    // disabledActionButtons: ['data'],
                    onSave: function() {
                        console.log("OOO: " + myFormBuilder.formData);
                        $formContainer.val(myFormBuilder.formData);
                    },
                    controlOrder: [
                        'text',
                        'textarea'
                    ]
                }));

                myFormBuilder.promise.then(formBuilder => {
                        formBuilder.actions.setData(
                            formBuilder.formData
                        );
                    }
                );
            });
            new EditForm(config);
        });
    </script>
@endpush

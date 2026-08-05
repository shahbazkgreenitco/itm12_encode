{{-- @page-meta
{
  "page_no": "KBCFG-04",
  "file": "index.blade.php",
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
@section('title', trans('kanban.kanban_board_config.kanban_board_config'))
@section('content')
    <div id="ticket-incident-list-wrapper">
        <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
            <button type="button" onclick="window.history.back()"
                class="d-flex gap-3 align-items-center bg-transparent border-0 text-decoration-none p-0">
                <svg width="20" height="20" viewBox="0 0 25 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M11.5662 0.440759C11.706 0.580116 11.817 0.745702 11.8927 0.928029C11.9684 1.11036 12.0074 1.30584 12.0074 1.50326C12.0074 1.70068 11.9684 1.89616 11.8927 2.07848C11.817 2.26081 11.706 2.4264 11.5662 2.56576L5.12993 9.00201L23.5049 9.00201C23.9028 9.00201 24.2843 9.16004 24.5656 9.44135C24.8469 9.72265 25.0049 10.1042 25.0049 10.502C25.0049 10.8998 24.8469 11.2814 24.5656 11.5627C24.2843 11.844 23.9028 12.002 23.5049 12.002L5.12993 12.002L11.5662 18.4408C11.848 18.7226 12.0063 19.1047 12.0063 19.5033C12.0063 19.9018 11.848 20.284 11.5662 20.5658C11.2844 20.8475 10.9022 21.0059 10.5037 21.0059C10.1052 21.0059 9.72298 20.8475 9.44118 20.5658L0.441182 11.5658C0.301343 11.4264 0.190387 11.2608 0.114679 11.0785C0.0389711 10.8962 -4.50154e-07 10.7007 -4.58778e-07 10.5033C-4.67401e-07 10.3058 0.038971 10.1104 0.114679 9.92803C0.190387 9.7457 0.301342 9.58011 0.441182 9.44076L9.44118 0.440759C9.58054 0.30092 9.74613 0.189964 9.92845 0.114255C10.1108 0.0385471 10.3063 -0.000425789 10.5037 -0.000425798C10.7011 -0.000425806 10.8966 0.038547 11.0789 0.114255C11.2612 0.189964 11.4268 0.30092 11.5662 0.440759Z"
                        fill="currentColor"></path>
                </svg>
                <h3 class="h3-text mb-0">
                    {{trans('kanban.kanban_board_config.kanban_board_config')}}
                </h3>
            </button>
        </div>
        <main class="main-content view-incident-details" id="mainContent">

            <ul class="nav nav-underline" id="incident-tabs" role="tablist">
                <li class="nav-item border-0">
                    <a class="nav-link active px-1" id="general-settings" data-bs-toggle="tab" href="#general-settings"
                        role="tab" aria-controls="overview" aria-expanded="true"><span class="b4-text fw-bold">{{trans('kanban.kanban_board_config.general_settings')}}</span></a>
                </li>
            </ul>
            <div class="tab-content tabcontent-border pt-3" id="kanabanConfigTabs">
                <div role="tabpanel" class="tab-pane fade show active" id="general-settings">
                    <div class="container-fluid px-0">
                        <div class="card rounded-0 border-0 shadow-none">
                            <div class="card-body bg-none">
                                <form id="general_config">
                                    <div class="row mb-4 px-4">
                                        <div class="col-4">
                                            <div class="amg-form-field amg-form-field-row">
                                                <label for="before_overdue"
                                                    class="form-label b1-text mb-2 fw-bold">
                                                    {{trans('kanban.kanban_board_config.overdue_mail_hours')}}
                                                </label>

                                                <div class="input-group">
                                                    <input name="before_overdue" id="before_overdue" autocomplete="off"
                                                        type="number" class="form-control"
                                                        placeholder="{{trans('kanban.kanban_board_config.overdue_mail_hours')}}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="amg-form-footer modal-footer d-flex justify-content-start py-2 px-4">
                                        <button autocomplete="off" type="button" id="btnSubmit"
                                            class="amg-btn amg-btn-primary amg-btn-md min-w-150">
                                            {{trans('kanban.kanban_board_config.update')}}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection

@push('css')
<style>
    [data-bs-theme=dark] .amg-form-field .input-group input{
        background: #191919;
        border: 1px solid #2A2A2D;
        border-radius: 2px;
    }
</style>
@endpush
@push('scripts')
    <script src="{!! CommonHelper::asset('js/tickets/kanban-board/kanban-config.js') !!}"></script>
    <script>
        $(document).ready(function() {
            var config = {};
            config.overdue_mail_before_id = {!! json_encode($old_overdue_mail_before_hours) !!}
            config.url = {};
            config.url.update = "{{ url('tickets/kanban-board/config') }}"
            config.translations = {
               something_went_wrong: '{{ trans('kanban.kanban_board_config.something_went_wrong') }}',
            }
            new KanbanConfig(config);
            
        })
    </script>
@endpush

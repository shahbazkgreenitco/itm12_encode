{{-- @page-meta
{
  "page_no": "PC010TH-26",
  "file": "history.blade.php",
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
@section('title', trans("problem_category.service_ticket_fields.problem_category_task_history"))
@section('content')
<div id="main-user-list-wrapper" class="pbmg-list-wrapper">
    <div class="header-actions-wrapper d-flex align-items-center pe-4">
        <button onclick="window.location.href='{{ url()->previous() }}'" class="d-flex gap-3 align-items-center bg-transparent outline-none border-0" data-bs-toggle="tooltip" title="Back">
            <svg width="16" height="21" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M11.5662 0.440759C11.706 0.580116 11.817 0.745702 11.8927 0.928029C11.9684 1.11036 12.0074 1.30584 12.0074 1.50326C12.0074 1.70068 11.9684 1.89616 11.8927 2.07848C11.817 2.26081 11.706 2.4264 11.5662 2.56576L5.12993 9.00201L23.5049 9.00201C23.9028 9.00201 24.2843 9.16004 24.5656 9.44135C24.8469 9.72265 25.0049 10.1042 25.0049 10.502C25.0049 10.8998 24.8469 11.2814 24.5656 11.5627C24.2843 11.844 23.9028 12.002 23.5049 12.002L5.12993 12.002L11.5662 18.4408C11.848 18.7226 12.0063 19.1047 12.0063 19.5033C12.0063 19.9018 11.848 20.284 11.5662 20.5658C11.2844 20.8475 10.9022 21.0059 10.5037 21.0059C10.1052 21.0059 9.72298 20.8475 9.44118 20.5658L0.441182 11.5658C0.301343 11.4264 0.190387 11.2608 0.114679 11.0785C0.0389711 10.8962 -4.50154e-07 10.7007 -4.58778e-07 10.5033C-4.67401e-07 10.3058 0.038971 10.1104 0.114679 9.92803C0.190387 9.7457 0.301342 9.58011 0.441182 9.44076L9.44118 0.440759C9.58054 0.30092 9.74613 0.189964 9.92845 0.114255C10.1108 0.0385471 10.3063 -0.000425789 10.5037 -0.000425798C10.7011 -0.000425806 10.8966 0.038547 11.0789 0.114255C11.2612 0.189964 11.4268 0.30092 11.5662 0.440759Z" fill="currentColor"></path>
            </svg>
        </button>
        <h3 class="h3-text mb-0">{{ trans("problem_category.service_ticket_fields.problem_category_task_history") }} {{ $task_id->name ? " - $task_id->name" : '' }}</h3>
    </div>
    <main class="main-content" id="mainContent">
        <div class="container-fluid px-0">
            <div class="card rounded-0">
                <div class="card-body pt-3">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <div class="col-auto">
                            <select class="amg-table-pagination-dropdown userModulePageLenth user-list-page-length">
                                <option value="10" selected>{{ trans("problem_category.service_ticket_fields.show_10") }}</option>
                                <option value="25">{{ trans("problem_category.service_ticket_fields.show_25") }}</option>
                                <option value="50">{{ trans("problem_category.service_ticket_fields.show_50") }}</option>
                                <option value="100">{{ trans("problem_category.service_ticket_fields.show_100") }}</option>
                            </select>
                        </div>
                        <div class="flex-grow-1"></div>

                        <div>
                            <div class="amg-list-searchbar">
                                <svg class="amg-list-searchbar__icon btn-searchbox plain-search-icon" width="18" height="18" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                </svg>
                                <input type="text" class="amg-list-searchbar__input user-list-search searchbox plain-search" placeholder="{{ trans("problem_category.service_ticket_fields.search_placeholder") }}">
                            </div>
                        </div>

                        <button class="amg-refresh-btn btn-reload-list">
                            <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="currentColor"></path>
                            </svg>
                            <span>{{ trans("problem_category.service_ticket_fields.refresh_list") }}</span>
                        </button>
                    </div>

                    <div class="js-user-list-view-panel">
                        <div class="table-responsive">
                            <table id="mytable" class="table display app-data-table">
                                <thead>
                               <tr>
                                    <th><h4>{{ trans("problem_category.service_ticket_fields.id") }}</h4></th>
                                    <th><h4>{{ trans("problem_category.service_ticket_fields.task_name") }}</h4></th>
                                    <th><h4>{{ trans("problem_category.service_ticket_fields.department") }}</h4></th>
                                    <th><h4>{{ trans("problem_category.service_ticket_fields.category") }}</h4></th>
                                    <th><h4>{{ trans("problem_category.service_ticket_fields.sub_category") }}</h4></th>
                                    <th><h4>{{ trans("problem_category.service_ticket_fields.description") }}</h4></th>
                                    <th><h4>{{ trans("problem_category.service_ticket_fields.assign_to") }}</h4></th>
                                    <th><h4>{{ trans("problem_category.service_ticket_fields.assign_to_user_group") }}</h4></th>
                                    <th><h4>{{ trans("problem_category.service_ticket_fields.TAT_h") }}</h4></th>
                                    <th><h4>{{ trans("problem_category.service_ticket_fields.task_flow") }}</h4></th>
                                    <th><h4>{{ trans("problem_category.service_ticket_fields.th_status") }}</h4></th>
                                    <th><h4>{{ trans("problem_category.service_ticket_fields.updated_at") }}</h4></th>
                                    <th><h4>{{ trans("problem_category.service_ticket_fields.updated_by") }}</h4></th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="amg-modal modal fade" id="descriptionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-5 bg-white">
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title px-4">{{ trans("problem_category.service_ticket_fields.description") }}</h3>
                    <button type="button" data-bs-dismiss="modal" class="modal-close px-4" aria-label="{{ trans("problem_category.service_ticket_fields.Close") }}">
                        <svg style="height: 27px; width: 27px; min-width: 27px; flex-shrink: 0;" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z" fill="currentColor" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body px-4 py-3"></div>
                <div class="modal-footer justify-content-start mb-4 py-0 px-4">
                    <button type="button" class="amg-btn amg-btn-secondary amg-btn-md mb-2 ms-2" data-bs-dismiss="modal">{{ trans("problem_category.service_ticket_fields.Close") }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    #descriptionModal .modal-body {
        max-height: 65vh;
        overflow-y: auto;
        word-break: break-word;
    }

    /* .task-description-preview {
        min-width: 220px;
        max-width: 360px;
        white-space: normal;
    } */

    .task-description-preview .read-more {
        /* color: #cc3333; */
        font-weight: 600;
        margin-left: 4px;
    }
</style>
@endpush

@push('scripts')
<script src="{!! CommonHelper::asset('js/tickets/problem-category/task/index.js') !!}"></script>
<script>
     $(document).ready(function() {
        var config = {};
        config.url = {};
        config.url.getTaskHistory = "{{url('tickets/problem-categories/task-history-ajax')}}";
        config.translations = {
            something_went_wrong: '{{ trans('problem_category.service_ticket_fields.something_went_wrong') }}',
            please_enter_valid_search: '{{ trans('problem_category.service_ticket_fields.please_enter_valid_search') }}',
        };
        config.id = {!! json_encode($task_id) !!};
        config.datatable_translations =  @json(trans('datatable.datatable'));
        new History(config);
    });
</script>
@endpush

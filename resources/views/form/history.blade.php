{{-- @page-meta
{
  "page_no": "DYF02-02",
  "file": "history.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "Muzaffar Shaikh",
      "from": "2026-05-15",
      "reviewer": null,
      "description": "Implemented Dynamic form History Lisiting"
    }
  ]
}
--}}
@extends('layouts.layout1')
@section('title', trans('content.dynamic_form.dynamic_form_history'))

@section('content')
<div id="form-history-wrapper">
    <div class="header-actions-wrapper header-actions-white-wrapper d-flex align-items-center justify-content-between pe-4">
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
                    <h2 class="h2-text mb-0">{{ trans('content.dynamic_form.dynamic_form_history') }}</h2>
                </button>
            </div>
        </div>
    </div>

    <main class="main-content" id="mainContent">
        <div class="container-fluid px-0">
            <div class="card rounded-0">
                <div class="card-body">

                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="col-auto">
                            <select class="amg-table-pagination-dropdown amgTablePageLenth form-history-page-length">
                                <option value="10" selected>{{ trans('content.dynamic_form.show') }} (10)</option>
                                <option value="25">{{ trans('content.dynamic_form.show') }} (25)</option>
                                <option value="50">{{ trans('content.dynamic_form.show') }} (50)</option>
                                <option value="100">{{ trans('content.dynamic_form.show') }} (100)</option>
                            </select>
                        </div>
                        <div class="flex-grow-1"></div>
                        <div>
                            <div class="amg-list-searchbar">
                                <svg class="amg-list-searchbar__icon" width="18" height="18"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z"/>
                                </svg>
                                <input type="text" class="amg-list-searchbar__input history-search"
                                    placeholder="{{ trans('content.dynamic_form.press_enter_with_Search') }}">
                            </div>
                        </div>
                        <button class="amg-refresh-btn btn-reload-list"
                            title="{{ trans('content.dynamic_form.refresh_list') }}">
                            <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none">
                                <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="#7F7F7F"/>
                            </svg>
                            <span>{{ trans('content.dynamic_form.refresh_list') }}</span>
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table id="form-history-table" class="amg-datatable table display app-data-table">
                            <thead>
                                <tr>
                                    <th>{{ trans('content.dynamic_form.id') }}</th>
                                    <th>{{ trans('content.dynamic_form.name') }}</th>
                                    <th>{{ trans('content.dynamic_form.description') }}</th>
                                    <th>{{ trans('content.dynamic_form.remark') }}</th>
                                    <th>{{ trans('content.dynamic_form.created_at') }}</th>
                                    <th>{{ trans('content.dynamic_form.created_by') }}</th>
                                    <th>{{ trans('content.dynamic_form.updated_by') }}</th>
                                    <th>{{ trans('content.dynamic_form.deleted_by') }}</th>
                                    <th>{{ trans('content.dynamic_form.action') }}</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </main>

</div>
@endsection

@push('css')
    <style></style>
@endpush

@push('scripts')
    <script type="text/javascript" src="{!! CommonHelper::asset('js/form/history.js') !!}"></script>
    <script>
        $(document).ready(function () {
            var config = {};
            config.url = {};
            config.url.form_list = "{{ url('jx-form-list-history') }}";
            config.url.list_form = "{{ url('dynamic_form') }}";
            config.url.view      = "{{ url('dynamic_form/view/history') }}";
            config.form_id       = {!! json_encode($form_id) !!};
            config.token         = "{{ csrf_token() }}";
            config.translations  = {
                search                 : '{{ trans('content.dynamic_form.search') }}',
                refresh_list           : '{{ trans('content.dynamic_form.refresh_list') }}',
                press_enter_with_Search: '{{ trans('content.dynamic_form.press_enter_with_Search') }}',
                view                   : '{{ trans('content.dynamic_form.view') }}',
            };
            new FormHistory(config);
        });
    </script>
@endpush
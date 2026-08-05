{{-- @page-meta
{
  "page_no": "",
  "file": "index.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "AI Migration",
      "from": "2026-07",
      "reviewer": null,
      "description": "Migrated from Laravel 8 / Bootstrap 3 legacy procurement advisory board module"
    }
  ]
}
--}}
@extends('layouts.layout1')
@section('title', trans('content.procurement_fields.Procurement_Advisory_Board'))
@section('content')
<div id="main-pab-list-wrapper">
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
        <h3 class="h3-text mb-0">{{ trans('content.procurement_fields.Procurement_Advisory_Board') }}</h3>
        <div class="d-flex gap-8">
            <button class="header-icon-btn header-icon-btn-sm btn-open-filter" type="button"
                    data-bs-toggle="tooltip" title="{{ trans('content.user_fields.Filter') }}">
                <i class="bi bi-funnel"></i>
                <span class="b6-text opacity-50">{{ trans('content.user_fields.Filter') }}</span>
                <span class="filter-count-badge d-none">0</span>
            </button>
            <button class="amg-btn amg-btn-primary amg-btn-sm btn-add-pab" type="button"
                    data-bs-toggle="tooltip" title="{{ trans('content.procurement_fields.create_pab') }}">
                <i class="bi bi-plus-lg"></i>
                <span>{{ trans('content.procurement_fields.create_pab') }}</span>
            </button>
        </div>
    </div>
    <main class="main-content" id="mainContent">
        <div class="container-fluid px-0">
            <div class="card rounded-0">
                <div class="card-body pt-3">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <div class="flex-grow-1"></div>
                        <div>
                            <div class="amg-list-searchbar">
                                <i class="bi bi-search amg-list-searchbar__icon search-icon"></i>
                                <input type="text" class="amg-list-searchbar__input pab-list-search"
                                       placeholder="{{ trans('content.user_fields.please_enter_valid_search') }}">
                            </div>
                        </div>
                        <button class="amg-refresh-btn btn-reload-list" type="button">
                            <i class="bi bi-arrow-clockwise amg-refresh-btn__icon"></i>
                            <span>{{ trans('content.procurement_fields.Refresh_List') }}</span>
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="pab_table" class="table display" style="width:100%">
                            <thead>
                                <tr>
                                    <th><h4 class="b2-text">{{ trans('content.procurement_fields.Action') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.procurement_fields.PAB_Name') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.procurement_fields.Description') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.procurement_fields.Approval_Mode') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.procurement_fields.Total_Members') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.procurement_fields.Created_date') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.procurement_fields.updated_date') }}</h4></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- Advance filter modal — FIXED id per project convention --}}
    <div class="modal fade" id="advanceFilterModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ trans('content.user_fields.Filter') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <div class="amg-form-field">
                                <label class="form-label">{{ trans('content.procurement_fields.Filter_By_Approval_Mode') }}</label>
                                <select id="filter_by_approver" name="filter_by_approver[]" class="form-select"></select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-clear-filter" id="btnClrFilter">
                        {{ trans('content.filter_heading.Clear') }}
                    </button>
                    <button type="button" class="amg-btn amg-btn-primary btn-filter">
                        {{ trans('button.filter') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('procurements.pabs.modal_html')
</div>
@endsection

@push('css')
<link href="{!! CommonHelper::asset('newcss/select2.min.css') !!}" rel="stylesheet" />
<link href="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.css') !!}" rel="stylesheet" />
@endpush

@push('scripts')
<script src="{!! CommonHelper::asset('js/procurement/pab/pab.js') !!}"></script>
<script>
    var config = {};
    config.url = {};
    config.url.getlist = "{{ url('procurements/pab/ajax-list') }}";
    config.url.add = "{{ url('procurements/pab/add') }}";
    config.url.get = "{{ url('procurements/pab/get') }}";
    config.url.edit = "{{ url('procurements/pab/edit') }}";
    config.url.delete = "{{ url('procurements/pab/delete') }}";
    config.url.manage_users = "{{ url('procurements/pab/members') }}";
    config.url.export = "{{ url('procurements/pab/export') }}";
    config.token = "{{ csrf_token() }}";
    config.approval = {!! json_encode($vd["approval"]) !!};
    config.translations = {
        search_option: '{{ trans('content.procurement_fields.serach_option') }}',
        Select_Department: '{{ trans('content.procurement_fields.Select_Department') }}',
        press_enter_with_Search: '{{ trans('content.procurement_fields.press_enter_with_Search') }}',
        Search: '{{ trans('content.procurement_fields.search') }}',
        Refresh_List: '{{ trans('content.procurement_fields.Refresh_List') }}',
        Edit_PAB: '{{ trans('content.procurement_fields.Edit_PAB') }}',
        Member_List:'{{ trans('content.procurement_fields.Member_List') }}',
        Delete_PAB: '{{ trans('content.procurement_fields.Delete_PAB') }}',
        are_you_delete: '{{ trans('content.procurement_fields.are_you_delete_pab') }}',
        save_changes: '{{ trans('button.save_changes') }}',
        add_PAB: '{{ trans('content.procurement_fields.create_pab') }}',
        Filter: '{{ trans('content.user_fields.Filter') }}',
        btn_update: '{{ trans('button.update') }}',
        btn_save: '{{ trans('button.save') }}',
        enter_text: '{{ trans('content.procurement_fields.Enter_Description_About_PAB') }}',
        Filter_By_Approval_Mode:'{{ trans('content.procurement_fields.Filter_By_Approval_Mode') }}',
        please_enter_valid_search: '{{ trans('content.user_fields.please_enter_valid_search') }}',
        download_excel: '{{trans('content.service_ticket_fields.download_excel')}}'
    };
    new PABList(config);
</script>
@endpush

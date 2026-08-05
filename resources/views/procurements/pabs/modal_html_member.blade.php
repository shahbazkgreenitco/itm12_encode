{{-- @page-meta
{
  "page_no": "",
  "file": "procurements/pab_members.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "AI Migration",
      "from": "2026-07",
      "reviewer": null,
      "description": "Migrated from Laravel 8 / Bootstrap 3 legacy module"
    }
  ]
}
--}}
@extends('layouts.layout1')

@section('title', trans('content.procurement_fields.PAB_Members'))

@section('content')
<div id="main-pab-members-wrapper">
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
        <h3 class="h3-text mb-0">{{ trans('content.procurement_fields.PAB_Members') }}</h3>
        <div class="d-flex gap-8">
            @if(Auth::user()->hasPermissionTo('PAB Members - Add'))
                <button type="button" class="header-icon-btn header-icon-btn-sm amg-btn amg-btn-primary" data-bs-toggle="tooltip" title="{{ trans('button.add') }}">
                    <i class="bi bi-plus-lg"></i> {{ trans('button.add') }}
                </button>
            @endif
            @if(Auth::user()->hasPermissionTo('PAB Members - Refresh'))
                <button type="button" class="header-icon-btn header-icon-btn-only header-icon-btn-only-sm amg-refresh-btn" data-bs-toggle="tooltip" title="{{ trans('button.refresh') }}">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            @endif
        </div>
    </div>

    <main class="main-content" id="mainContent">
        <div class="container-fluid px-0">
            <div class="card rounded-0">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table id="pabMembersTable" class="table display" style="width:100%">
                            <thead>
                                <tr>
                                    <th><h4 class="b2-text">{{ trans('content.procurement_fields.Users') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.procurement_fields.User') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.procurement_fields.Approval_Level') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('button.actions') }}</h4></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pab_members as $member)
                                    <tr>
                                        <td>
                                            @if(!empty($member->users))
                                                @foreach($member->users as $user)
                                                    <span class="badge bg-secondary rounded-pill me-1 mb-1">{{ $user->name }}</span>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>{{ $member->user->name ?? '' }}</td>
                                        <td>{{ $member->hierarchy_level ?? '' }}</td>
                                        <td>
                                            @if(Auth::user()->hasPermissionTo('PAB Members - Edit'))
                                                <button type="button" class="btn btn-sm btn-outline-secondary edit-btn" data-bs-toggle="tooltip" title="{{ trans('button.edit') }}">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            @endif
                                            @if(Auth::user()->hasPermissionTo('PAB Members - Delete'))
                                                <button type="button" class="btn btn-sm btn-outline-secondary delete-btn" data-bs-toggle="tooltip" title="{{ trans('button.delete') }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- PAB Member Modal --}}
    <div class="modal fade" id="pabMemberModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog">
            <form id="pabMemberForm" method="post" action="#" enctype="multipart/form-data" autocomplete="off">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="pabMemberModalLabel">{{ trans("content.procurement_fields.New_Member") }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <div class="amg-form-field">
                                    <label for="users" class="form-label">{{ trans("content.procurement_fields.Users") }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-users"></i></span>
                                        <select multiple name="users[]" id="users" class="form-select" placeholder="{{ trans('content.procurement_fields.select_user') }}"></select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="amg-form-field">
                                    <label for="user" class="form-label">{{ trans("content.procurement_fields.User") }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <select name="user" id="user" class="form-select" placeholder="{{ trans('content.procurement_fields.select_user') }}"></select>
                                    </div>
                                </div>
                            </div>
                            
                            @if($pab->hierarchy_approval == 1)
                                <div class="col-md-12">
                                    <div class="amg-form-field">
                                        <label for="hierarchy_level" class="form-label">{{ trans("content.procurement_fields.Approval_Level") }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-level-up"></i></span>
                                            <select name="hierarchy_level" id="hierarchy_level" class="form-select">
                                                @for($i=1;$i<=15;$i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" id="btnClear" data-bs-dismiss="modal">
                            {{ trans('button.close') }}
                        </button>
                        <button type="button" id="btnSubmit" class="amg-btn amg-btn-primary">
                            {{ trans('button.add') }}
                            <span class="spinner-border spinner-border-sm d-none" id="pabMemberLoader" role="status"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{!! CommonHelper::asset('js/procurements/pab_members.js') !!}"></script>
    <script>
        var config = {};
        config.url = {};
        config.url.list = "{{ route('procurements.pab_members.index') }}";
        config.url.store = "{{ route('procurements.pab_members.store') }}";
        config.url.update = "{{ route('procurements.pab_members.update') }}";
        config.url.destroy = "{{ route('procurements.pab_members.destroy') }}";
        config.url.users = "{{ route('procurements.pab_members.users') }}";
        config.token = "{{ csrf_token() }}";
        config.pabId = "{{ $pab->id ?? '' }}";
        config.hierarchyApproval = "{{ $pab->hierarchy_approval ?? 0 }}";
        new PabMembers(config);
    </script>
@endpush

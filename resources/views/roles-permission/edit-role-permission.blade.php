{{-- @page-meta
{
  "page_no": "RP02U-26",
  "file": "edit-role.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "Sandeep Verma",
      "from": "2026-03",
      "reviewer": null,
      "description": "Initial setup"
    },
    {
      "version": "1.01",
      "writer": "Prithvi Pillai",
      "from": "2026-06",
      "reviewer": null,
      "description": "Changes for adding the back button for ui consistency issue"
    },
    {
      "version": "1.02",
      "writer": "Prithvi Pillai",
      "from": "2026-06",
      "reviewer": null,
      "description": "Changes for removing the previous back button and removing cancel button since it is not required"
    },
  ]
}
--}}
@extends('layouts.layout1')
@section('title', trans('role-permission.page_title'))
@section('content')
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
        <div class="py-3">
            <div class="container-fluid ps-0">
                <button onclick="location.href='{{ url('roles-permission') }}'" class="d-flex gap-3 align-items-center bg-transparent outline-none border-0">
                    <svg width="25" height="21" viewBox="0 0 25 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.5662 0.440759C11.706 0.580116 11.817 0.745702 11.8927 0.928029C11.9684 1.11036 12.0074 1.30584 12.0074 1.50326C12.0074 1.70068 11.9684 1.89616 11.8927 2.07848C11.817 2.26081 11.706 2.4264 11.5662 2.56576L5.12993 9.00201L23.5049 9.00201C23.9028 9.00201 24.2843 9.16004 24.5656 9.44135C24.8469 9.72265 25.0049 10.1042 25.0049 10.502C25.0049 10.8998 24.8469 11.2814 24.5656 11.5627C24.2843 11.844 23.9028 12.002 23.5049 12.002L5.12993 12.002L11.5662 18.4408C11.848 18.7226 12.0063 19.1047 12.0063 19.5033C12.0063 19.9018 11.848 20.284 11.5662 20.5658C11.2844 20.8475 10.9022 21.0059 10.5037 21.0059C10.1052 21.0059 9.72298 20.8475 9.44118 20.5658L0.441182 11.5658C0.301343 11.4264 0.190387 11.2608 0.114679 11.0785C0.0389711 10.8962 -4.50154e-07 10.7007 -4.58778e-07 10.5033C-4.67401e-07 10.3058 0.038971 10.1104 0.114679 9.92803C0.190387 9.7457 0.301342 9.58011 0.441182 9.44076L9.44118 0.440759C9.58054 0.30092 9.74613 0.189964 9.92845 0.114255C10.1108 0.0385471 10.3063 -0.000425789 10.5037 -0.000425798C10.7011 -0.000425806 10.8966 0.038547 11.0789 0.114255C11.2612 0.189964 11.4268 0.30092 11.5662 0.440759Z" fill="currentColor" />
                    </svg>
                    <h3 class="h3-text mb-0">{{ trans('role-permission.header_title') }}</h3>
                </button>
            </div>
        </div>
        <div class="d-flex gap-8">
            <div class="header-icon-search header-icon-search-sm d-flex align-items-center">
                <svg class="opacity-50" height="16" width="16" viewBox="0 0 20 20" fill="none"><path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor" /></svg>
                <input class="border-0 bg-transparent outline-none focus-none global-permission-search" placeholder="{{ trans('role-permission.search_placeholder') }}" style="height: 20px;" type="text">
            </div>

            {{-- <button class="header-icon-btn-only header-icon-btn-only-sm">
                <svg viewBox="0 0 20 20" fill="none"><path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor" /></svg>
            </button> --}}
            {{-- commented search icon --}}
        </div>
    </div>
    <main class="main-content" id="mainContent">
        @php
            $ticketChildren = [
                11 => trans('role-permission.ticket_children.ticket'),
                12 => trans('role-permission.ticket_children.ticket_basics'),
                14 => trans('role-permission.ticket_children.scheduler'),
                15 => trans('role-permission.ticket_children.ticket_trigger'),
                16 => trans('role-permission.ticket_children.incident'),
                20 => trans('role-permission.ticket_children.task'),
                21 => trans('role-permission.ticket_children.knowledge_document'),
            ];
        @endphp
        <div class="card rounded-0 d-flex flex-row">
            <div class="card-body d-flex g-5">
                <div class="role-name d-flex align-items-center">
                    <strong>{{ trans('role-permission.edit_page.role_name_label') }}</strong>
                    <small class="ps-1">{{ $role->name }}</small>
                </div>
                <div class="number-of-role d-flex ps-4 align-items-center">
                    <strong>{{ trans('role-permission.edit_page.number_of_users_label') }}</strong>
                    <small class="ps-1"><a href="{{ url('users?role=') }}{{ $role->id }}">{{ $role->users_count }}</small>
                </div>
            </div>
            {{-- <div class="back-button d-flex justify-content-center align-items-center me-5">
                <a class="amg-btn amg-btn-outline" href="{{ url('roles-permission') }}">{{ trans('role-permission.edit_page.back') }}</a>
            </div> --}}
        </div>
        @can('UserBasePermission')
            <form class="permissionForm">
                <input type="hidden" name='roleId' id="roleId" value="{{ $role->id }}" />
                <div class="permission-tab-wrapper">
                    <div class="container-fluid py-3">
                        <div class="row g-3">
                            <!-- Left: Main Overview Card -->
                            <div class="col-lg-3 col-md-4">
                                <div class="card rounded-4 overflow-hidden">
                                    <div class="card-body p-0 show-permissions">

                                        <div class="nav flex-column nav-pills blue-pills mb-4 mb-md-0" id="v-pills-tab"
                                            role="tablist" aria-orientation="vertical">

                                            @foreach ($moduleDef as $key => $mod)
                                                @if (isset($mod['status']) && $mod['status'] == 1)
                                                    <a class="nav-link {{ $loop->first ? 'active' : '' }}"
                                                        id="v-pills-{{ $key }}-tab" data-bs-toggle="pill"
                                                        href="#v-pills-{{ $key }}" role="tab"
                                                        aria-controls="v-pills-{{ $key }}"
                                                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                                        {{ $mod['module'] }}
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-9 col-md-8">
                                <div class="card rounded-4">
                                    <div class="card-body">
                                        <div class="tab-content" id="v-pills-tabContent">
                                            @foreach ($moduleDef as $key => $mod)
                                                @if (isset($mod['status']) && $mod['status'] == 1)
                                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"  id="v-pills-{{ $key }}" role="tabpanel" aria-labelledby="v-pills-{{ $key }}-tab">
                                                        <div class="mb-3"><h5 class="s1-text fw-semibold mb-0">{{ $mod['module'] }}</h5></div>
                                                        @if ($key == 11)
                                                            <ul class="nav nav-pills gray-pills rounded-2 overflow-hidden"
                                                                role="tablist">
                                                                @foreach ($ticketChildren as $childKey => $title)
                                                                    <li class="nav-item">
                                                                        <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab" href="#ticket-child-{{ $childKey }}" role="tab"><span>{{ $title }}</span></a>
                                                                    </li>
                                                                @endforeach
                                                            </ul>

                                                            <div class="tab-content border mt-2">
                                                                @foreach ($ticketChildren as $childKey => $title)
                                                                    <div class="tab-pane p-3 {{ $loop->first ? 'active show' : '' }}"
                                                                        id="ticket-child-{{ $childKey }}" role="tabpanel">
                                                                        <div class="row px-1">
                                                                            <div class="form-check py-2">
                                                                                <input type="checkbox" class="form-check-input selectAllPermission">
                                                                                <label class="form-check-label">{{ trans('role-permission.edit_page.select_all') }}</label>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row permissions-container">
                                                                            @if (!empty($permissionObj[$childKey]['permissions']))
                                                                                @foreach ($permissionObj[$childKey]['permissions'] as $p)
                                                                                    <div class="col-md-4">
                                                                                        <div class="form-check py-2">
                                                                                            <input type="checkbox" class="form-check-input permissions" name="permission[{{ $p->id }}]" value="1"
                                                                                                {{ $p->enableForRole == true ? 'checked' : '' }}>
                                                                                            <label class="form-check-label  text-break">{{ $p->name }}</label>
                                                                                        </div>
                                                                                    </div>
                                                                                @endforeach
                                                                            @else
                                                                                <div class="text-muted p-3">{{ trans('role-permission.edit_page.no_permissions_available') }} </div>
                                                                            @endif
                                                                        </div>

                                                                        <div class="amg-btn-group mt-3">
                                                                            {{-- <button type="button"
                                                                                class="amg-btn amg-btn-ghost">{{ trans('role-permission.buttons.cancel') }}</button> --}}
                                                                            <button type="button"
                                                                                class="amg-btn amg-btn-primary updateRole">{{ trans('role-permission.buttons.save_changes') }}</button>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <div class="row px-1">
                                                                <div class="form-check py-2">
                                                                    <input type="checkbox" class="form-check-input selectAllPermission">
                                                                    <label class="form-check-label">{{ trans('role-permission.edit_page.select_all') }}</label>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                @if (!empty($permissionObj[$key]['permissions']))
                                                                    @foreach ($permissionObj[$key]['permissions'] as $p)
                                                                        <div class="col-md-4">
                                                                            <div class="form-check py-2">
                                                                                <input type="checkbox" class="form-check-input permissions" name="permission[{{ $p->id }}]" value="1" {{ $p->enableForRole == true ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-break">{{ $p->name }}</label>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                @else
                                                                    <div class="text-muted p-3">{{ trans('role-permission.edit_page.no_permissions_available') }}</div>
                                                                @endif

                                                            </div>

                                                            <div class="amg-btn-group mt-3">
                                                                <button type="button" class="amg-btn amg-btn-primary updateRole">{{ trans('role-permission.buttons.save_changes') }}</button>
                                                                {{-- <button type="button" class="amg-btn amg-btn-ghost bg-black text-white ">{{ trans('role-permission.buttons.cancel') }}</button> --}}
                                                            </div>
                                                        @endif

                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="assets" role="tabpanel" aria-labelledby="assets-tab">
                        <p>{{ trans('role-permission.edit_page.tab_assets') }}</p>
                    </div>
                    <div class="tab-pane fade" id="tickets" role="tabpanel" aria-labelledby="tickets-tab">
                        <p>{{ trans('role-permission.edit_page.tab_tickets') }}</p>
                    </div>
                    <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
                        <p>{{ trans('role-permission.edit_page.tab_documents') }} </p>
                    </div>
                    <div class="tab-pane fade" id="activity" role="tabpanel" aria-labelledby="activity-tab">
                        <p>{{ trans('role-permission.edit_page.tab_activity') }}</p>
                    </div>
                </div>
            </form>
        @endcan
    </main>

    @include('roles-permission.add-role-modal')
@endsection

@push('scripts')
    <script src="{!! CommonHelper::asset('js/role-permission/index.js') !!}"></script>
    <script>
        $(function() {
            var config = {};
            config.url = {};
            config.url.getRoles = "{{ route('getRoles') }}";
            config.url.role_edit = "{{ url('edit-role') }}";
            config.url.updateRole = "{{ route('updateRole') }}";
            config.translations = {
                something_went_wrong: "{{ trans('role-permission.messages.something_went_wrong') }}"
            };

            new rolePermission(config);
        });
    </script>
@endpush

@extends('layouts.layout1')
@section('title', trans('ticket.service_ticket_fields.my_board'))

@section('content')
    <section class="content" id="kanban-boards">
        <!-- kanban new header -->
        <div id="board-header" class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
            <h3 class="h3-text mb-0">Kanban Board</h3>
            <div class="d-flex align-items-center justify-content-between gap-2" id="board-actions">
                <div class="amg-list-searchbar">
                    <svg class="amg-list-searchbar__icon" width="16" height="16" viewBox="0 0 20 20"
                        fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z"
                            fill="currentColor"></path>
                    </svg>
                    <input type="text" id="searchPlain" class="amg-list-searchbar__input sr-list-search searchbox plain-search" placeholder="{{ trans('service_ticket.search') }}"/>
                     <div id="clear-search"  class="clear-icon hide">
                        <svg fill="#000000" xmlns="http://www.w3.org/2000/svg" 
                            width="16px" height="16px" viewBox="0 0 52 52">
                            <path d="M26,2C12.7,2,2,12.7,2,26s10.7,24,24,24s24-10.7,24-24S39.3,2,26,2z M30.9,26.8l7.8,7.8c0.4,0.4,0.4,1,0,1.4
                                    l-2.8,2.8c-0.4,0.4-1,0.4-1.4,0L26.7,31c-0.4-0.4-1-0.4-1.4,0l-7.8,7.8c-0.4,0.4-1,0.4-1.4,0L13.3,36c-0.4-0.4-0.4-1,0-1.4l7.8-7.8
                                    c0.4-0.4,0.4-1,0-1.4l-7.9-7.9c-0.4-0.4-0.4-1,0-1.4l2.8-2.8c0.4-0.4,1-0.4,1.4,0l7.9,7.9c0.4,0.4,1,0.4,1.4,0l7.8-7.8
                                    c0.4-0.4,1-0.4,1.4,0l2.8,2.8c0.4,0.4,0.4,1,0,1.4l-7.8,7.8C30.6,25.8,30.6,26.4,30.9,26.8z"/>
                        </svg>
                        <!-- <small style="color:grey">clear</small> -->
                    </div>
                </div>

                <!-- filter button -->
                <button class="header-icon-btn header-icon-btn-sm btn-open-filter" type="button" data-bs-toggle="tooltip"
                    title="{{ trans('service_ticket.filter') }}">
                    <svg viewBox="0 0 20 18" fill="none">
                        <path
                            d="M19.3658 0.893462C19.2504 0.626753 19.0591 0.399864 18.8157 0.241013C18.5723 0.0821617 18.2877 -0.00164211 17.997 2.43793e-05H1.49705C1.20673 0.000596404 0.922821 0.0854027 0.679756 0.244155C0.436692 0.402908 0.244919 0.628785 0.127702 0.894384C0.0104851 1.15998 -0.0271385 1.45389 0.0193952 1.74045C0.065929 2.02701 0.19462 2.29391 0.389858 2.50877L0.397358 2.51721L6.74705 9.29721V16.5C6.74698 16.7715 6.8206 17.0379 6.96004 17.2708C7.09948 17.5038 7.29953 17.6945 7.53885 17.8227C7.77816 17.9508 8.04778 18.0117 8.31894 17.9986C8.59011 17.9856 8.85266 17.8993 9.07861 17.7488L12.0786 15.7481C12.2843 15.6112 12.4529 15.4255 12.5695 15.2076C12.6861 14.9898 12.7471 14.7465 12.747 14.4994V9.29721L19.0977 2.51721L19.1052 2.50877C19.3025 2.29489 19.4324 2.02764 19.4788 1.74037C19.5251 1.4531 19.4858 1.15854 19.3658 0.893462ZM11.4514 8.4919C11.3217 8.62945 11.2487 8.81094 11.247 9.00002V14.4994L8.24705 16.5V9.00002C8.2471 8.80958 8.17471 8.62624 8.04455 8.48721L1.49705 1.50002H17.997L11.4514 8.4919Z"
                            fill="currentColor" />
                    </svg>
                    <span class="b6-text opacity-50">{{ trans('service_ticket.filter') }}</span>
                    <span class="filter-count-badge d-none" aria-label="Active filters">0</span>
                </button>

                <div class="amg-sort-wrapper position-relative" data-bs-toggle="tooltip" title="Sort">
                    <button type="button" class="amg-header-sort header-icon-btn header-icon-btn-sm" id="srqSortDrop">
                        <span class="sort-action">
                            <i id="sortDirectionIcon" class="bi bi-sort-down"></i>
                        </span>
                        <span class="dropdown-action">
                            <i class="bi bi-caret-down-fill" style="font-size:15px !important;"></i>
                        </span>
                    </button>
                    <ul class="dropdown-menu amg-sort-menu" id="short_items"></ul>
                </div>  
       
                <!-- reload button -->
                <button class="header-icon-btn header-icon-btn-sm btn-reload-list rotate-wrapper" data-bs-toggle="tooltip" title="Refresh List">
                    <i class="bi bi-arrow-repeat"></i>
                </button>

                <!-- expand button -->
                <button class="header-icon-btn header-icon-btn-sm expend-view" data-bs-toggle="tooltip" title="Expand/Collapse" id="board-expand">
                    <i id="expandSvg" class="bi bi-arrows-angle-expand" style="font-size:15px !important;"></i>
                    <i id="compressSvg" class="bi bi-arrows-angle-contract d-none" style="font-size:15px !important;"></i>
                </button>

                <!-- archive button -->
                @can('KanbanViewArchivedCards')
                <button class="header-icon-btn header-icon-btn-sm btn-getarchived" data-bs-toggle="tooltip" title="{{trans('ticket.service_ticket_fields.view_archived_cards')}}">
                        <i id="archive-unact" class="bi bi-archive fs-5" style="font-size:15px !important;"></i>
                        <i id="archive-act" class="bi bi-archive-fill fs-5 board-icon-active d-none" style="font-size:15px !important;"></i>
                </button>
                @endcan

                <!-- mark archive button -->
                <button class="header-icon-btn header-icon-btn-sm btn-markarchived" data-bs-toggle="tooltip" title="{{trans('ticket.service_ticket_fields.mark_cards_archived')}}">
                    <i class="bi bi-box-arrow-down fs-5" style="font-size:15px !important;"></i>
                    <i class="bi bi-box-arrow-down-fill fs-5 d-none" style="font-size:15px !important;"></i>
                </button>

                <!-- import button -->
                @can('KanbanBoardImport')
                    <button class="header-icon-btn header-icon-btn-sm btn-import" data-bs-toggle="tooltip" title="Import" >
                        <i class="bi bi-upload fs-5" style="font-size:15px !important;"></i>
                    </button>
                @endcan

                <!-- export button -->
                @can('KanbanBoardExport')
                    <button class="header-icon-btn header-icon-btn-sm download-report" data-bs-toggle="tooltip" title="Download" >
                        <i class="bi bi-download fs-5" style="font-size:15px !important;"></i>
                    </button>
                @endcan

                <!-- group by button -->

                @can('KanbanBoardGroupBy')
                    <div class="dropdown groupby-buttons">
                        <button class="header-icon-btn header-icon-btn-sm dropdown-toggle"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                                title="Group By"
                                data-bs-toggle-tooltip="tooltip">
                            <span class="groupbybtns group_by">
                                <i id="groupby-unact" class="bi bi-collection fs-6" style="font-size:15px !important;"></i>

                                <i id="groupby-act" class="bi bi-collection-fill fs-6 d-none" style="font-size:15px !important;"></i>
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right list-groupby groupby-fields p-0"></ul>
                    </div>
                @endcan

                <!-- visible content button -->
                <button class="header-icon-btn header-icon-btn-sm btn-visible-content" data-bs-toggle="tooltip" title="Column Visibility" >
                    <span id="columnVisibilityButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                         <i id="col-vis-unact" class="bi bi-grid fs-5" style="font-size:15px !important;"></i>
                         <i id="col-vis-act" class="bi bi-grid-fill fs-5 d-none" style="font-size:15px !important;"></i>
                    </span>
                    <ul class="dropdown-menu dropdown-menu-right list-group column-fields p-0"></ul>
                </button>

                <!-- Add button -->
                <button class="amg-btn amg-btn-primary amg-btn-sm d-none" id="btnCreateTicket" data-bs-toggle="tooltip" title="Add" style="background:#E20505; opacity:1" >
                    <svg style="opacity:1" width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill="#ffff" d="M19.375 10C19.375 10.2984 19.2565 10.5845 19.0455 10.7955C18.8345 11.0065 18.5484 11.125 18.25 11.125H11.125V18.25C11.125 18.5484 11.0065 18.8345 10.7955 19.0455C10.5845 19.2565 10.2984 19.375 10 19.375C9.70163 19.375 9.41548 19.2565 9.2045 19.0455C8.99353 18.8345 8.875 18.5484 8.875 18.25V11.125H1.75C1.45163 11.125 1.16548 11.0065 0.954505 10.7955C0.743526 10.5845 0.625 10.2984 0.625 10C0.625 9.70163 0.743526 9.41548 0.954505 9.2045C1.16548 8.99353 1.45163 8.875 1.75 8.875H8.875V1.75C8.875 1.45163 8.99353 1.16548 9.2045 0.954505C9.41548 0.743526 9.70163 0.625 10 0.625C10.2984 0.625 10.5845 0.743526 10.7955 0.954505C11.0065 1.16548 11.125 1.45163 11.125 1.75V8.875H18.25C18.5484 8.875 18.8345 8.99353 19.0455 9.2045C19.2565 9.41548 19.375 9.70163 19.375 10Z" fill="#F8FAFD"/>
                    </svg>
                    <span style="color:#ffffff; opacity:1">Add Ticket</span>
                </button>

                @can('KanbanCutomBoardCardAdd')
                    <button class="amg-btn amg-btn-primary amg-btn-sm d-none btn-Add" data-bs-toggle="tooltip" title="Add" style="background:#E20505; opacity:1" >
                        <svg style="opacity:1" width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill="#ffff" d="M19.375 10C19.375 10.2984 19.2565 10.5845 19.0455 10.7955C18.8345 11.0065 18.5484 11.125 18.25 11.125H11.125V18.25C11.125 18.5484 11.0065 18.8345 10.7955 19.0455C10.5845 19.2565 10.2984 19.375 10 19.375C9.70163 19.375 9.41548 19.2565 9.2045 19.0455C8.99353 18.8345 8.875 18.5484 8.875 18.25V11.125H1.75C1.45163 11.125 1.16548 11.0065 0.954505 10.7955C0.743526 10.5845 0.625 10.2984 0.625 10C0.625 9.70163 0.743526 9.41548 0.954505 9.2045C1.16548 8.99353 1.45163 8.875 1.75 8.875H8.875V1.75C8.875 1.45163 8.99353 1.16548 9.2045 0.954505C9.41548 0.743526 9.70163 0.625 10 0.625C10.2984 0.625 10.5845 0.743526 10.7955 0.954505C11.0065 1.16548 11.125 1.45163 11.125 1.75V8.875H18.25C18.5484 8.875 18.8345 8.99353 19.0455 9.2045C19.2565 9.41548 19.375 9.70163 19.375 10Z" fill="#F8FAFD"/>
                        </svg>
                        <span style="color:#ffffff; opacity:1">Add Card</span>
                    </button>
                @endcan
            </div>
        </div>
        <main class="main-content" id="mainContent">
            <div class="box panel" id="kanban-panel" >
                <div class="panel-body box-body">
                    <div id="api_loader">
                        <svg id="kanban-board-loader" class="fa-spin" stroke="currentColor" fill="currentColor" width="120" height="120" version="1.1" id="Layer_1"
                            xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                            x="0px" y="0px" viewBox="0 0 300 300" xml:space="preserve">
                            <g>
                                <path class="st1" d="M176.22,85.97c6.63,0,12.81,3.57,16.12,9.31l26.22,45.42c3.32,5.74,3.32,12.88,0,18.62l-26.22,45.42
                                    c-3.32,5.74-9.49,9.31-16.12,9.31h-52.44c-6.63,0-12.81-3.57-16.12-9.31l-26.22-45.41c-3.32-5.74-3.32-12.88,0-18.62l26.22-45.42
                                    c3.32-5.74,9.49-9.31,16.12-9.31H176.22 M176.22,76.97h-52.44c-9.87,0-18.98,5.26-23.92,13.81l-26.22,45.42
                                    c-4.93,8.55-4.93,19.07,0,27.62l26.22,45.41c4.93,8.55,14.05,13.81,23.92,13.81h52.44c9.87,0,18.98-5.26,23.92-13.81l26.22-45.41
                                    c4.93-8.55,4.93-19.07,0-27.62l-26.22-45.42C195.21,82.23,186.09,76.97,176.22,76.97L176.22,76.97z">
                                </path>
                            </g>
                        </svg>
                    </div>
                    <div class="container-fluid" style="padding-inline: 0px;">
                    <div id="kanban-header-container">
                        <div id="board-legends" class="header-icon-btn header-icon-btn-sm">
                            <ul class="board-legends-items" style="border-radius: none; box-shadow:none" >
                                <li class="loading">
                                    Loading statuses
                                    <span class="dots">
                                        <span>.</span>
                                        <span>.</span>
                                        <span>.</span>
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div class="form-group select-board amg-modal amg-form-modal" style="margin-bottom: 0;">
                            <div id="kanban-board-controls">
                                <div>
                                    <select id="board_id" class="form-control board_id amg-form-field">
                                        @foreach ($kanbanLists as $kanban)
                                            <option value="{{ $kanban->id }}" {{ $loop->first ? 'selected' : '' }} data-created_by={{ $kanban->created_by??'' }} data-type={{ $kanban->board_items_type }}>
                                                {{ $kanban->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            
                                <div class="viewport-control">
                                    <select id="columnsPerRow" class="form-select" style="display: inline-block; width: 200px;">
                                        <option value="all" selected >All Columns</option>
                                        <option value="1">1 Column</option>
                                        <option value="2">2 Columns</option>
                                        <option value="3">3 Columns</option>
                                        <option value="4">4 Columns</option>
                                    </select>
                                </div>

                                <div class=" assigned-users" id="show_members"></div> 
                            </div>
                        </div>
                    </div>
                    </div>
                    <div id="kanban-wrapper">
                        <div id="kanban-board">
                            <div id="kanban-card"></div>
                        </div>
                    </div>
                </div>
            </div> 
        </main>
        {{-- display the board member list modal --}}
        <div class="amg-modal modal fade" id="memberList" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content rounded-5">

                    <div class="modal-header d-flex align-items-center py-3 pt-4">
                        <h4 class="modal-title s1-text fw-semibold">
                            Board Member List
                        </h4>

                        <button data-bs-dismiss="modal" class="modal-close px-4" aria-label="Close">
                            <svg style="height: 27px; width: 27px; min-width: 27px; flex-shrink: 0;" viewBox="0 0 31 31"
                                fill="none">
                                <path
                                    d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z"
                                    fill="#7F7F7F" />
                            </svg>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table display app-data-table" id="myTable">
                                <thead>
                                    <tr>
                                        <th>Profile Pic</th>
                                        <th>Username</th>
                                        <th>Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody"></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-end mb-4 pb-4 py-0">
                        <div class="amg-btn-group mt-3 px-4">
                            <button type="button"
                                class="amg-btn amg-btn-ghost bg-black text-white s2-text"
                                data-bs-dismiss="modal">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- display the all statuses for board --}}
        <div class="modal fade" id="legendsList" tabindex="-1" role="dialog" aria-labelledby="legendsListLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="legendsListLabel">All Statuses</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                       <div id="board-legends" class="col-md-6 header-icon-btn header-icon-btn-sm" >
                            <ul class="board-legends-items">
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn btn-theme-black" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="amg-modal modal fade" id="advanceFilterModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
            data-bs-keyboard="false">
           <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content rounded-5">

                    <div class="modal-header d-flex align-items-center py-3 pt-4">
                        <h4 class="modal-title s1-text fw-semibold">
                            Filter
                        </h4>

                        <button data-bs-dismiss="modal" class="modal-close px-4" aria-label="Close">
                            <svg style="height: 27px; width: 27px; min-width: 27px; flex-shrink: 0;" viewBox="0 0 31 31"
                                fill="none">
                                <path
                                    d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z"
                                    fill="#7F7F7F" />
                            </svg>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3 px-3">
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <label class="form-label b5-text">{{ trans('ticket.service_ticket_fields.filter_by_department') }}</label>
                                <select id="filter_by_department" class="form-control" name="department[]" multiple>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <label class="form-label b5-text">{{ trans('ticket.service_ticket_fields.filter_by_problem_category') }}</label>
                                <select id="filter_by_problem_category" class="form-control"
                                    name="problem_category[]" multiple>
                                </select>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <label class="form-label b5-text">{{ trans('ticket.service_ticket_fields.filter_by_sub_category') }}</label>
                                <select id="filter_by_sub_category" class="form-control"
                                    name="sub_category[]" multiple>
                                </select>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <label class="form-label b5-text">{{ trans('ticket.service_ticket_fields.filter_by_priority') }}</label>
                                <select id="filter_by_priority_id" class="form-control priority_id"
                                    name="priority_id[]" multiple>
                                    @foreach ($priorities as $priority)
                                        <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <label class="form-label b5-text">{{ trans('ticket.service_ticket_fields.filter_by_assigned_to') }}</label>
                                <select id="filter_by_assigned_to" class="form-control assigned_to"
                                    name="assigned_to[]" multiple>
                                </select>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <label class="form-label b5-text">{{ trans('ticket.service_ticket_fields.filter_by_creator') }}</label>
                                <select id="filter_by_creator_id" class="form-control creator_id"
                                    name="creator_id[]" multiple>
                                </select>
                            </div>
                
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <label class="form-label b5-text">{{ trans('content.filter_heading.filter_by_date') }}</label>
                                <select name="filter_by_date" id="filter_by_date" class="form-control">
                                    <option value="null">{{ trans('content.service_ticket_fields.No_Filter') }}</option>
                                    <option value="1">Created Date</option>
                                    <option value="4">Updated Date</option>
                                </select>
                            </div>
    
                            <div class="col-xl-5 col-lg-4 col-md-6">
                                <label class="form-label b5-text">{{ trans('content.filter_heading.filter_by_daterange') }}</label>
                                <div id="reportrange"
                                    class="form-control d-flex align-items-center justify-content-between"
                                    style="cursor:pointer;">
                                    <span></span>
                                    <svg width="15px" height="15px" viewBox="0 0 48 48" fill="currentColor">
                                        <title>{{ trans("content.filter_heading.filter_by_daterange") }}</title>
                                        <g id="Layer_2" data-name="Layer 2">
                                            <g id="invisible_box" data-name="invisible box">
                                                <rect width="48" height="48" fill="none" />
                                            </g>
                                            <g id="icons_Q2" data-name="icons Q2">
                                                <path
                                                    d="M44,8H35V4.1A2.1,2.1,0,0,0,33.3,2,2,2,0,0,0,31,4V8H17V4.1A2.1,2.1,0,0,0,15.3,2,2,2,0,0,0,13,4V8H4a2,2,0,0,0-2,2V42a2,2,0,0,0,2,2H44a2,2,0,0,0,2-2V10A2,2,0,0,0,44,8ZM42,40H6V20H42Zm0-24H6V12H42Z" />
                                                <rect x="8" y="24" width="8" height="8" rx="2" ry="2" />
                                                <rect x="32" y="24" width="8" height="8" rx="2" ry="2" />
                                                <rect x="20" y="24" width="8" height="8" rx="2" ry="2" />
                                            </g>
                                        </g>
                                    </svg>
                                    <input type="hidden" name="daterange" id="daterange">
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <label class="form-label b5-text">Filter By Status</label>
                                <select id="filter_by_status_id" class="form-control status_id"
                                    name="status_id[]" multiple>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <label class="form-label b5-text" for="filter_by_custom_field">{{ trans("ticket.tkt_filters.filter_by_custom_field") }}</label>
                                <select id="filter_by_custom_field" name="filter_by_custom_field" class="form-select filter-input"></select>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <label class="form-label b5-text" for="filter_by_custom_field_value">{{ trans("ticket.tkt_filters.filter_by_custom_field_value") }}</label>
                                <select id="filter_by_custom_field_value" name="filter_by_custom_field_value" class="form-select filter-input"> </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-end mb-4 pb-4 py-0">
                        <div class="amg-btn-group mt-3 gap-3 px-4">

                            <button type="button" class="amg-btn amg-btn-primary s2-text btn-filter col-md-6" id="advanced_filter">
                                {{ trans('content.report_fields.Filter') }}
                            </button>

                            <button type="button" id="advanced_filterClrFilter"
                                class="amg-btn amg-btn-ghost bg-black text-white s2-text col-md-6">
                                {{ trans('content.filter_heading.Clear') }}
                            </button>

                        </div>
                    </div>

                </div>
            </div>
        </div>
        @include("tickets.kanban-board.card_details")
        @include('tickets.ticket-list.create-ticket')
        @include("tickets.kd_suggestion")
        @include('tickets.ticket-list.edit-ticket')
        @include('tickets.ticket-list.ticket-transfar')
        @include('tickets.ticket-list.ticket-history')
        @include('tickets.assign_to_modal')
        @include('tickets.kanban-board.viewCard')
        @include('tickets.add_problem')
        @include('tickets.add_to_incident')
        @include('tickets.kanban-board.groupby_modal')
        @include('tickets.kanban-board.customCard_modal')
        @include('tickets.kanban-board.filter_modal')
        @include("tickets.kanban-board.history")
        @include('tickets.kanban-board.status_form')
        @include('tickets.status_form_modal')
        @include('tickets.form_modal')
        @include('tickets.ticket-list.task_detail')
        @include('tickets/modal_description')
        @include("tickets.fields_info")
        @include('tickets.ticket-list.add-task-modal')
        @include('tickets.task_update')
        @include('task-management.taskHistory')

        {{-- @include("tickets.ai_assist_modal")
        @include('tickets.reopen_modal') --}}
    </section>
@endsection

@push('css')
    <link href="{!! CommonHelper::asset('plugins/swipebox/css/swipebox.min.css') !!}" rel="stylesheet" />
    <link href="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.css') !!}" rel="stylesheet" />
    <link href="{!! CommonHelper::asset('plugins/flatpicker/css/flatpicker.min.css') !!}" rel="stylesheet" />


    <style type="text/css">
        .header-icon-btn svg,.header-icon-btn svg path{
            stroke-width:1 !important;
        }     
        .more-attachments-icon {
            position: absolute;
            top:5px;
            right: 22px;
            background: rgba(0, 0, 0, 0.6);
            color: #fff;
            padding: 2px 3px;
            border-radius: 4px;
            font-size: 11px;
            display: flex;
            align-items: center;
            gap: 2px;
        }
        .custom_field_info i{
			cursor: pointer;
			font-size: 16px;
		}
        .text-2x.darkpanel-title {
            white-space: nowrap;      
            overflow: hidden;         
            text-overflow: ellipsis;   
            max-width: 300px;          
            display: inline-block;   
            vertical-align: middle;
            cursor: pointer; 
        }
        .container {
            padding-left: 0px;
            margin-left: 0px;
        }

        ul.black-slide.black-slide-links {
            max-width: none;
        }

        .black-slide {
            background-color: white;
        }

        .nav-pills>li.active>a,
        .nav-pills>li.active>a:focus,
        .nav-pills>li.active>a:hover {
            color: #fff;
            background-color: black;
            width: 150px;
            height: 50px;
            text-align: center;
        }

        #exTab2 h3 {
            color: white;
            background-color: #428bca;
            padding: 5px 15px;
        }

        #exTab3 .nav-pills>li>a {
            border-radius: 0;
        }

        #exTab3 .tab-content {
            background-color: white;
            padding: 5px 15px;
        }

        hr {
            border-color: none;
            margin-top: 0px;
            margin-bottom: 0px;
        }

        @media(max-width: 990px) {
            #exTab3 .tab-content {
                padding: 0px;
            }
        }

        .mainWrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .element {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 98%;
            height: 60px;
            margin: 5px 0px 10px 30px;
            border: 1px solid #e8e1e1;
            border-radius: 15px;
            padding: 20px;
            transition: all .25s ease;
            box-shadow: 0 0 0 0 black;
        }

        .elements {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            height: 60px;
            margin: 5px;
            border: 1px solid #e8e1e1;
            border-radius: 15px;
            padding: 10px;
            transition: all .25s ease;
            box-shadow: 0 0 0 0 black;
        }

        .moving {
            transition: none;
        }

        .element:hover {
            transform: scale(1.01);
            box-shadow: 0 8px 20px 0px #00000085;
        }

        .leftSide {
            width: 50%;
            pointer-events: none;
        }

        .title,
        .description {
            border-radius: 7px;
        }

        .description {
            width: 80%;
            height: 22px;
        }

        .button {
            pointer-events: none;
        }

        .button div {
            width: 15px;
            height: 15px;
            border-radius: 100%;
            background-color: #848484;
        }

        .button div:nth-child(2) {
            margin: 6px 0;
        }

        i.fa.fa-trash {
            font-size: 14px;
        }

        i.fa.fa-cog {
            font-size: 20px;
            padding: 5px;
        }

        .page-header {
            width: 90%;
            color: #6c9400;
            margin: 2em auto 3em auto;
            border-bottom: .5em #8ff0ff solid;
        }

        .page-title {
            font-size: 3em;
            margin: 0;
        }

        .page-sub-title {
            margin: 0 0 1em 0;
        }

        .tag-links:link,
        .tag-links:visited {
            display: inline-block;
            color: #fff;
            background-color: #CD3333;
            text-decoration: none;
            padding: .5em 1em;
            border-radius: 1em;
            margin: .125em;
        }

        .posts {
            width: 100%;
            padding: 0;
            margin: 0 auto 1em auto;
        }

        .posts-title {
            font-size: 2em;
        }

        .post {
            box-sizing: border-box;
            position: relative;
            display: inline-block;
            min-width: 15em;
            margin: .25em 0;
            padding: .5em 1em 1em 1em;
            border: .0625em solid #ebfcff;
            vertical-align: top;
            line-height: normal;
        }

        .post-tags {
            display: none;
            box-sizing: inherit;
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            color: #fff;
            background-color: #70f;
            padding: 1em;
        }

        .topnav-right {
            float: right;
        }

        button.btn.btn-default.btn-round {
            background-color: #CD3333;
            color: white;
            margin-top: 10px;
            padding: 5px;
        }

        button.btn.btn-round {
            padding: 0px;
        }

        i.fa.fa-plus-circle {
            font-size: 20px;
            padding: 3px;
        }

        #left_category {
            height: auto;
            border: 1px solid #e2dddd;
            padding: 5px;
            overflow-y: scroll;
            scrollbar-width: thin !important;
            height: 200px;
            margin-top: 10px;
        }

        div#form_check1 {
            margin-left: 20px;
        }

        .form-check {
            margin-left: 10px;
        }

        .form-check-label {
            font-weight: normal;
            padding-top: 6px;
            margin-left: 3px;
        }

        .note {
            padding-left: 0px;
            padding-right: 0px;
        }

        button.btn.btn-secondary {
            width: 79px;
        }

        button.btn.btn-secondary:hover {
            background-color: #d9d9d9;
        }

        #tags {
            border: 1px solid #ccc;
            padding: 5px;
        }

        #tags>span {
            cursor: pointer;
            display: block;
            float: left;
            color: #fff;
            background: #cc3333;
            padding: 5px;
            padding-right: 25px;
            margin: 4px;
        }

        #tags>span:hover {
            opacity: 0.7;
        }

        #tags>span:after {
            position: absolute;
            content: "×";
            padding: 2px 5px;
            margin-left: 3px;
            font-size: 11px;
        }

        #tags>input {
            background: #eee;
            border: 0;
            margin: 4px;
            padding: 7px;
            width: auto;
            z-index: 5;
        }

        .tag-wrapper {
            text-align: right;
        }

        .tag-wrapper .badge {
            background: #D80505;
        }

        .tags-container {
            display: inline-block;
            float: inline-end;
            margin-top: 10px;
            padding-right: 5px;
        }

        .list-group {
            padding-left: 0;
            margin-bottom: 20px;
            margin-top: 20px;
        }
        .panel-body.chat {
            padding: 0px;
        }

        label.badge.badge-secondary {
            width: 100px;
            height: 30px;
            background-color: #1acc48;
            border-radius: 5px;
            display: table-cell;
            vertical-align: middle;
        }

        label.badge.badge-primary {
            width: 100px;
            height: 30px;
            background-color: #CD3333;
            border-radius: 5px;
            display: table-cell;
            vertical-align: middle;
        }

        .panel.panel-default.chat {
            width: 100%;
            border-radius: 15px;
        }

        .panel.panel-default.chat:hover {
            background-color: #F5F4F2;
        }

        .row.borders .text-muted {
            font-weight: 600;
            font-size: 14px;
            color: #8c8f92;
        }

        .title .text-muted {
            color: #8c8f92;
        }

        .kb_details {
            background: rgba(0, 0, 0, 0.29);
            padding: 15px;
            height: 220px
        }
        #kanban-board {
            /* display: flex; */
            justify-content: flex-start;
            overflow-x: auto;
            padding-bottom: 10px;
            white-space: nowrap;
            gap: 20px;
            transform: rotateX(180deg);
            /* padding-right: 200px; */
            font-family: Inter;
            margin-top: 20px;
            scrollbar-width: thin; 
        }

        #kanban-board > * {
            transform: rotateX(180deg);
        }

        #kanban-card::-webkit-scrollbar {
            height: 12px;
            display: block;
        }

        #kanban-card::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        #kanban-card::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        #kanban-card::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

       #kanban-card {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto; 
            overflow-y: visible; 
            gap: 16px;
            align-items: flex-start;
            padding-bottom: 10px;  
        }
        
        .kanban-column {
            background: #FFFFFF;
            padding: 2px;
            border-radius: 20px;
            position: relative;
            display: inline-block;
            vertical-align: top;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease, width 0.3s ease, flex 0.3s ease;
            /* margin-right: 18px; */
            flex-shrink: 0;
            min-width: 300px;
            max-width: 350px;
            height: auto; 
        }
        [data-bs-theme=dark] .kanban-column,[data-bs-theme=dark] .mainDiv{
           background: #2A2A2A; 
        }

        #kanban-card.columns-all .kanban-column {
            flex: 0 0 auto;
            width: 300px !important;  
            min-width: 300px;
            max-width: 350px;
        }

        #kanban-card.columns-1 .kanban-column {
            flex: 0 0 calc(100% - 20px) !important; 
            min-width: 0 !important;  
            width: calc(100% - 20px) !important;  
            max-width: none !important;  
        }

        #kanban-card.columns-2 .kanban-column {
            flex: 0 0 calc(50% - 18px) !important;
            min-width: 0 !important;
            width: calc(50% - 18px) !important;
            max-width: none !important;
        }

        #kanban-card.columns-3 .kanban-column {
            flex: 0 0 calc(33.333% - 17px) !important;
            min-width: 0 !important;
            width: calc(33.333% - 17px) !important;
            max-width: none !important;
        }

        #kanban-card.columns-4 .kanban-column {
            flex: 0 0 calc(25% - 16px) !important;
            min-width: 0 !important;
            width: calc(25% - 16px) !important;
            max-width: none !important;
        }

        #kanban-card.columns-5 .kanban-column {
            flex: 0 0 calc(20% - 15px) !important;
            min-width: 0 !important;
            width: calc(20% - 15px) !important;
            max-width: none !important;
        }

        #kanban-card.columns-6 .kanban-column {
            flex: 0 0 calc(16.666% - 14px) !important;
            min-width: 0 !important;
            width: calc(16.666% - 14px) !important;
            max-width: none !important;
        }

        #kanban-card,
        .kanban-column {
            transition: all 0.3s ease;
        }

        @media (max-width: 575.98px) {
            #kanban-card {
                gap: 10px;
                padding-bottom: 8px;
            }
            
            .kanban-column {
                min-width: 280px;
                margin-right: 10px;
            }
            
            #kanban-card.columns-all .kanban-column,
            #kanban-card.columns-2 .kanban-column,
            #kanban-card.columns-3 .kanban-column,
            #kanban-card.columns-4 .kanban-column,
            #kanban-card.columns-5 .kanban-column,
            #kanban-card.columns-6 .kanban-column {
                flex: 0 0 calc(100% - 20px) !important;
                width: calc(100% - 20px) !important;
                min-width: 0 !important;
                max-width: none !important;
            }
        }

        @media (min-width: 576px) and (max-width: 767.98px) {
            #kanban-card {
                gap: 12px;
            }
            
            .kanban-column {
                min-width: 280px;
            }
            
            #kanban-card.columns-3 .kanban-column,
            #kanban-card.columns-4 .kanban-column,
            #kanban-card.columns-5 .kanban-column,
            #kanban-card.columns-6 .kanban-column {
                flex: 0 0 calc(100% - 20px) !important;
                width: calc(100% - 20px) !important;
            }
            
            #kanban-card.columns-2 .kanban-column {
                flex: 0 0 calc(100% - 20px) !important;
                width: calc(100% - 20px) !important;
            }
        }

        @media (min-width: 768px) and (max-width: 991.98px) {
            .kanban-column {
                min-width: 290px;
            }
            
            #kanban-card.columns-3 .kanban-column,
            #kanban-card.columns-4 .kanban-column,
            #kanban-card.columns-5 .kanban-column,
            #kanban-card.columns-6 .kanban-column {
                flex: 0 0 calc(50% - 18px) !important;
                width: calc(50% - 18px) !important;
            }
        }

        @media (min-width: 992px) and (max-width: 1199.98px) {
            #kanban-card.columns-4 .kanban-column,
            #kanban-card.columns-5 .kanban-column,
            #kanban-card.columns-6 .kanban-column {
                flex: 0 0 calc(33.333% - 17px) !important;
                width: calc(33.333% - 17px) !important;
            }
        }

        @media (min-width: 1200px) and (max-width: 1399.98px) {
            #kanban-card.columns-5 .kanban-column,
            #kanban-card.columns-6 .kanban-column {
                flex: 0 0 calc(25% - 16px) !important;
                width: calc(25% - 16px) !important;
            }
        }

        @media (min-width: 1400px) {
            .kanban-column {
                min-width: 300px;
                max-width: 350px;
            }
        }

        #kanban-header-container{
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        @media (max-width: 710px) {
            #board-add span{
                display: none !important;
            }
           
         }

        @media (max-width: 1100px) {
            #board-header{
                align-items: start !important;
            }
            #board-add span{
                display:block !important;
            }
            #board-header{
                display: flex;
                flex-direction: column;
            }
           
         }

        @media (max-width: 1320px) {

            #board-add span{
                display: none;
            }
            #kanban-header-container {
                flex-wrap: wrap;
                flex-direction: column;
                align-items: stretch;   
            }

            #kanban-board-controls {
                width: 100% !important;  
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                justify-content: flex-start;
            }

            #kanban-board-controls > div {
                flex: 1 1 auto; 
            }


            .show-more-legends-dropdown {
                margin-left: auto;
            }
            .assigned-users {
                margin-left: auto;       
            }

            .board-legends-items {
                width: 100%
            }
        }

           @media (max-width: 640px) {
                .show-more-legends-dropdown {
                    margin-left: auto;
                }
                .show-more-legends-dropdown button span {
                    display: none;  
                }
            }



        #kanban-board-controls{
            display: flex;
            flex-direction: row;
            gap: 10px;
            align-items: center;
        }

        #kanban-board-controls .container-fluid{
            padding-inline: 0px;
        }
        .assigned-users {
            display: flex;
            align-items: center;
        }

         #kanban-board-controls [class^="col-"]:not(.pad-no) {
            padding: 6px 5px 0px !important;
        }

        .member-item {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #6c757d;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 2px solid #fff;
            position: relative;
            margin-left: -15px;
            overflow: hidden;
        }

        .assigned-users .member-item:first-child {
            margin-left: 0;
        }

        .userAvatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }

        #countMember {
            background: #343a40;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #countMember .count-link {
            color: #fff;
            text-decoration: none;
            font-size: 8px;
        }


        .board-legends-items {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 14px 24px;
            /* border-radius: 10px; */
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            list-style: none;
            flex-wrap: wrap;
        }

        .board-legends-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 4px 8px;
            border-radius: 4px;
            transition: 0.2s ease;
            cursor: default;
        }

        .board-legends-item:hover {
            background: #efefef;
        }

        .board-legends-item-color-code {
            width: 10px !important;
            height: 10px !important;
            border-radius: 50%;
            border: 2px solid #fff !important;
            box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.25);
        }

       
        .show-more-legends-dropdown {
            position: relative;
        }

        .show-more-btn {
            background: #D80505;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 4px 8px;
            font-size: 10px;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .show-more-btn:hover {
            background: #b80000;
        }

        .show-more-legends-content {
            position: absolute;
            right: 0;
            top: 36px;
            background: #fff;
            border-radius: 4px;
            /* padding: 12px; */
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            display: none;
            width: 180px;
            font-size: 10px;
            z-index: 99;
        }

        .show-more-legends-dropdown:hover .show-more-legends-content {
            display: block;
        }

        .show-more-legends-content ul {
            padding: 0;
            margin: 0;
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .show-more-legends-content .board-legends-item {
            background: transparent;
        }

        @media (max-width: 576px) {
            .board-legends-items {
                padding: 10px 14px;
                gap: 10px;
                flex-wrap: wrap;
            }

            .board-legends-item {
                padding: 4px 10px;
                gap: 6px;
                font-size: 12px;
            }
            .board-legends-item-color-code {
                width: 12px !important;
                height: 12px !important;
            }

       
            .show-more-btn {
                padding: 4px 10px;
                font-size: 12px;
            }
        }

        @media (min-width: 577px) and (max-width: 768px) {
            .board-legends-items {
                flex-wrap: wrap;
                gap: 14px;
            }

            .board-legends-item {
                padding: 5px 12px;
            }

            .show-more-btn {
                padding: 6px 12px;
            }
        }

        #kanban-panel{
            padding: 20px;
        }

        .kanban-column h3 {
            display: flex;
            align-items: center;
            font-size: 20px;
            font-weight: 520;
            line-height: 29.05px;
            padding: 0px 15px;
            margin-top: 5%;
        }

        .reload-rotate {
            animation: spin 0.8s linear infinite;
            stroke: #D80505 ;
            fill: #D80505;
            color: #D80505 ;
            opacity: 1 !important;
        }

        #filter-act, #archive-act, #groupby-act, #col-vis-act{
            color: #D80505;
        }

        @keyframes spinReverse {
            from { transform: rotate(0deg); }
            to { transform: rotate(-360deg); }
        }

        .kanban-title {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .kanban-count {
            font-weight: 400;
            font-size: 18px;
            line-height: 24.2px;
        }

        .kanban-actions {
            display: flex;
            align-items: center;
            margin-left: auto;
        }

        .kanban-actions .ps-icon {
            cursor: pointer;
            transition: transform 0.2s;
        }

        .kanban-actions .ps-icon:hover {
            transform: scale(1.1);
        }

        .field-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .field i {
            /* color: #555; */
            margin-right: 5px;
        }
         .mainDiv {
            padding: 2px 10px;
            min-height: 100px;
            border: 1px dashed transparent;
            overflow-y: auto;
            max-height: 600px;
            background-color: #FFFFFF;
            border-radius: 12px;
            scrollbar-width:thin;
        }
        .section.hidden-section {
            padding: 0 !important;
            margin: 0 !important;
            border: none !important;
        }

        #cardDialog .content-text {
            margin: 0;
            word-wrap: break-word;
            word-break: break-word;
            overflow-wrap: break-word;
            white-space: normal;
            max-width: 100%;
        }

        #cardDialog .dialog-content {
            max-width: 500px;
            overflow: hidden;
        }

        #cardDialog .section {
            overflow: hidden;
        }

        #kanban-board-loader, #kanban-sprint-loader, #kanban-cards-loader{
            color: #da1a1a;
        }

        .mainDiv::-webkit-scrollbar {
            width: 3px;
        }

        .mainDiv::-webkit-scrollbar-track {
            background: transparent;
        }

        .mainDiv::-webkit-scrollbar-thumb {
            background-color: #FFFFFF; /* matches bg, looks hidden */
            border-radius: 3px;
        }

        /* On hover: scrollbar becomes visible */
        /* .mainDiv:hover {
            scrollbar-color: #da1a1a transparent !important;
        }

        .mainDiv:hover::-webkit-scrollbar-thumb {
            background-color: #da1a1a; 
        } */

        .card {
            min-width: 260px;
            margin: 13px 0;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            gap: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            transition: box-shadow 0.3s ease, transform 0.3s ease;
            overflow:hidden;
            font-family: inter;
            border-radius: 8px;
            /* padding: 18px; */
        }
        .card-body {
            padding: 6px 12px;
        }

         .card-footer {
            padding: 2px 12px 14px;
        }

        .card .card-header{
            height: 38px;
            display: flex;
            align-items: center;
            border-radius: 0px !important;
        }

        .card .card-header h3{
            margin: 0;
            font-size: 16px;
            font-weight:600;
            color: #ffff;
        }

        .status-label {
            font-size: 12px;
            font-weight: medium;
            margin-left: 6px;
            /* color: #FF5C00; */
            animation: blink 2s infinite;
        }

        .status-label-green {
            font-size: 12px;
            font-weight: medium;
            margin-left: 4px;
            /* color: #13530b;*/
            animation: blink 2s infinite;
        }

        .status-label-gray {
            font-size: 12px;
            font-weight: medium;
            margin-left: -5px;
            animation: blink 2s infinite;
        }

        .status-label-yellow {
            font-size: 12px;
            font-weight: medium;
            margin-left: 20px;
            /* color: #ff9800 ;*/
            animation: blink 2s infinite;
        }

        .green {
            background: #13530b
        }

        .yellow {
            background: #ff9800
        }

        @keyframes blink {

            0%,
            50%,
            100% {
                opacity: 1;
            }

            25%,
            75% {
                opacity: 0;
            }
        }

        .card:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(-5px);
        }

        .card h4 {
            margin: 0;
            font-size: 18px;
            word-wrap: break-word;
            text-wrap: wrap;
            font-weight: 500;
            color: #333;
        }

        .card p {
            margin: 0;
            word-wrap: break-word;
            text-wrap: wrap;
            color: #666;
        }

        .card .icons {
            display: flex;
            gap: 9px;
            position: absolute;
            bottom: 1%;
        }

        .card .icons i {
            font-size: 16px;
            color: #000000;
        }

        .card .user-profile {
            display: flex;
            align-items: center;
            position: absolute;
            bottom: -4px;
            right: 10px;
            left: 150px;
            gap: 5px;
        }

        .card .user-profile img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #007bff;
        }

        .card .user-profile span {
            font-size: 14px;
            color: #333;
        }

        .scrollable .empty {
            background-image: url('{{ asset('images/kanban_image.png') }}');
            background-repeat: no-repeat;
            background-position: center;
            background-size: 230px auto;
            min-height: 230px;
        }

        .add-task-form {
            margin-top: 20px;
            padding: 10px;
            width: 400px;
            display: grid;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .add-task-form input {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .add-task-form button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .add-task-form button:hover {
            background-color: #0056b3;
        }

        #add-status-form {
            margin-top: 20px;
            padding: 10px;
            width: 300px;
            display: grid;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        #add-status-form input {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        #add-status-form button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        #add-status-form button:hover {
            background-color: #0056b3;
        }

        .card .status {
            background-color: #e0e0e0;
            padding: 5px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            color: #333;
        }

        .ui-state-highlight {
            height: 200px !important;
            width: 100%;
            background: rgba(255, 255, 255, 0.5);
            margin: 10px 0;
            border-radius: 5px;
            position: relative;
            text-align: center;
        }

        .ui-state-highlight .card-placeholder {
            opacity: 0.5;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            border: 2px dashed #ffa500;
            padding: 10px;
        }

        .ticket-bio-data {
            /* margin-left: 10px; */
            padding-top: 5px;
        }

        .ticket-bio-data p {
            background: #eee;
        }

        .ticket-bio-data span.pad-rgt {
            display: inline-block;
            width: 115px;
            background: #555;
            padding: 8px 7px;
            color: #FFFFFF;
        }

        .ticket-bio-data p span.text-dark {
            padding: 0px 6px;
            color: #797979;
            display: inline-block;
        }

        /* .timeline-entry {
            position: relative;
            clear: both;
        } */

        .table_data_view {
            width: 100%;
        }

        .table_data_view.loading {
            position: relative;
            margin-top: 30px;
        }

        .table_data_view.loading:after {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3) url('{{ asset('imgs/loader.gif') }}') center no-repeat;
            background-position: center;
            background-repeat: no-repeat;
            content: "";
        }

        .expireInfo {
            background: #f2f2f2 !important;
        }

        .card-div:hover {
            background-color: #D7D7D7;
            cursor: pointer;
            border-radius: 5px;
            padding: 5px 1px;
        }

        .card-div.disable-hover:hover {
            background-color: transparent;
            cursor: default;
        }

        .add-card-input:focus {
            border: 1px solid #CD3333;
            outline: none;
        }
        .task-box{
            background-color: #FFF2F0 !important;
        }
        .group-by-item .like-radio {
            display: inline-block;
            width: 10px;
            height: 10px;
            margin: 0 3px -1px 0;
            border-radius: 10px;
            border:1px solid black;
        }
        .active_radio{
            background: rgb(132, 219, 2) !important;
        }
        #ticket-detail-page div#page-content {
            padding: 0px 15px 0
        }

        #ticket-detail-page .timeline-label.tl-note {
            background: #fffcd3;
            box-shadow: 0 1px 1px rgba(0, 0, 0, .05);
        }

        #ticket-detail-page .timeline-label.tl-note::after {
            border-right: 9px solid #fffcd3;
        }

        #ticket-detail-page .js-act-staring i.fa {
            color: #f50808;
        }

        #ticket-detail-page .fb {
            display: block;
            width: 55px;
            height: 50px;
            position: absolute;
            right: 0;
            top: 0;
            background-size: 48px !important;
        }

        #ticket-detail-page .fb1 {
            background: url(../images/emo/1.gif) 50% 50% no-repeat;
        }

        #ticket-detail-page .fb2 {
            background: url(../images/emo/2.gif) 50% 50% no-repeat;
        }

        #ticket-detail-page .fb3 {
            background: url(../images/emo/3.gif) 50% 50% no-repeat;
        }

        #ticket-detail-page .fb4 {
            background: url(../images/emo/4.gif) 50% 50% no-repeat;
        }

        #ticket-detail-page .fb5 {
            background: url(../images/emo/5.gif) 50% 50% no-repeat;
        }

        #ticket-detail-page .attachments a {
            position: relative;
            display: inline-block;
        }

        /* .btn.spammed-tkt {
                background: #ed1818;
                color: #fff;
                border: 1px solid #fa1414;
            } */
        #ticket-detail-page span.label-merged-primary,
        span.label-merged {
            text-transform: uppercase;
            border-radius: 3px;
            font-size: 11px;
            padding: 3px 5px;
            font-weight: bold;
            vertical-align: middle;
        }

        #ticket-detail-page span.label-merged-primary {
            color: #000000;
            background: #ffa726;
        }

        #ticket-detail-page span.label-merged {
            color: #000000;
            background: #ffa726;
        }

        #ticket-detail-page .label-merged a {
            color: #000000;
            text-transform: uppercase;
        }

        #ticket-detail-page textarea.textarea-s {
            /* min-height: 60px; */
            /* max-height: 100px; */
            max-width: 100%;
            min-width: 100%;
        }

        #ticket-detail-page .txt1 {
            font-size: 12px;
            color: #474747;
            font-family: helvetica, Arial;
        }

        #ticket-detail-page .force-br {
            -ms-word-break: break-all;
            word-break: break-all;
            word-break: break-word;
            -webkit-hyphens: auto;
            -moz-hyphens: auto;
            hyphens: auto;
        }

        #ticket-detail-page li.carousel-1.active {
            color: black;
        }

        #ticket-detail-page .expireInfo {
            background: none;
            padding: 0px;
        }

        @media screen and (min-width:1380px) {
            #ticket-detail-page .box-container {
                flex-direction: row
            }
        }

        .box-item {
            position: relative;
            -webkit-backface-visibility: hidden;
            /*width: 415px;*/
            max-width: 100%;
        }

        .flip-box {
            -ms-transform-style: preserve-3d;
            transform-style: preserve-3d;
            -webkit-transform-style: preserve-3d;
            perspective: 1000px;
            -webkit-perspective: 1000px;
            animation: rot 7s ease-in-out infinite;
        }

        .flip-box-front,
        .flip-box-back {
            background-size: cover;
            background-position: center;
            border-radius: 8px;
            min-height: 90px;
            -ms-transition: transform 0.7s cubic-bezier(.4, .2, .2, 1);
            transition: transform 0.7s cubic-bezier(.4, .2, .2, 1);
            -webkit-transition: transform 0.7s cubic-bezier(.4, .2, .2, 1);
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;

        }

        .flip-box-front {
            -ms-transform: rotateY(0deg);
            -webkit-transform: rotateY(0deg);
            transform: rotateY(0deg);
            -webkit-transform-style: preserve-3d;
            -ms-transform-style: preserve-3d;
            transform-style: preserve-3d;

        }

        .flip-box-back {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            -ms-transform: rotateY(180deg);
            -webkit-transform: rotateY(180deg);
            transform: rotateY(180deg);
            -webkit-transform-style: preserve-3d;
            -ms-transform-style: preserve-3d;
            transform-style: preserve-3d;

        }

        .flip-box .inner {
            position: absolute;
            left: 0;
            width: 100%;
            outline: 1px solid transparent;
            -webkit-perspective: inherit;
            perspective: inherit;
            z-index: 2;
            transform: translateY(-50%) translateZ(60px) scale(.94);
            -webkit-transform: translateY(-50%) translateZ(60px) scale(.94);
            -ms-transform: translateY(-50%) translateZ(60px) scale(.94);
            top: 50%;
        }

        .flip-box-header {
            font-size: 20px;
            color: white;
            margin-top: 0px;
            font-weight: bolder;
        }

        .flip-box-front {
            background-color: #cc3333;
        }

        .flip-box-back {
            background-color: #1a1a1a;
        }

        @keyframes rot {
            50% {
                transform: rotateY(.5turn);
            }

            100% {
                transform: rotateY(1turn);
            }
        }

        #ticket-detail-page span.tat {
            font-size: 24px;
            font-weight: bold;
        }

        #ticket-detail-page span.expire {
            font-size: 24px;
        }

        #ticket-detail-page div#expireInfo {
            border-bottom: none;
        }

        .creator-info {
            background: white;
            color: #CD3333;
            padding: 3px;
            position: relative;
            border: 1px solid #cd33339e;
            margin-top: 21px;
        }

        .assigned-info {
            background: white;
            color: #CD3333;
            padding: 3px;
            position: relative;
            border: 1px solid #cd33339e;
            margin-top: 21px;
            padding-bottom: 12px;
        }

        .creator-info::after {
            content: "";
            position: absolute;
            height: 10px;
            width: 10px;
            background: #CD3333;
            bottom: -11px;
            right: -2px;
            border-bottom: 10px solid #f2f2f2;
            border-left: 10px solid #CD3333;
        }

        .creator-info::before {
            content: "";
            width: 10px;
            height: 10px;
            background: #CD3333;
            position: absolute;
            top: -11px;
            right: -2px;
            border-right: 10px solid #f2f2f2;
            border-bottom: 10px solid #CD3333;
        }

        .assigned-info::after {
            content: "";
            position: absolute;
            height: 10px;
            width: 10px;
            background: #CD3333;
            bottom: -11px;
            right: -2px;
            border-bottom: 10px solid #f2f2f2;
            border-left: 10px solid #CD3333;
        }

        .assigned-info::before {
            content: "";
            width: 10px;
            height: 10px;
            background: #CD3333;
            position: absolute;
            top: -11px;
            right: -2px;
            border-right: 10px solid #f2f2f2;
            border-bottom: 10px solid #CD3333;
        }

        #ticket-detail-page .panel-title {
            font-size: 1em;
        }

        #ticket-detail-page .panel-title,
        a.collapsed {
            color: #333333;
            text-overflow: inherit;
        }

        #ticket-detail-page .panel>.panel-heading:after,
        .panel.panel-colorful>.panel-heading:after {
            border-bottom: none;
        }

        .fa-arrow-left {
            -webkit-animation: bounceLeft 2s infinite;
            animation: bounceLeft 2s infinite;
        }

        @keyframes bounceLeft {

            0%,
            20%,
            50%,
            80%,
            100% {
                -ms-transform: translateX(0);
                transform: translateX(0);
            }

            60% {
                -ms-transform: translateX(15px);
                transform: translateX(15px);
            }
        }

        #ticket-detail-page span.button-text {
            color: #333333;
            font-size: 13px;
            margin: 9px;
        }

        i.fa.fa-arrow-left {
            color: #CD3333;
            font-size: 12px;
            margin-left: 7px;
        }

        #ticket-detail-page div.creator_box {
            padding: 0px 0px 0px 10px;
            color: #313131;
        }

        #ticket-detail-page div#kd {
            height: 350px;
            padding: 0px;
        }

        .carousel-indicators {
            top: 320px;
        }

        .kd_doc_head {
            height: 25px;
        }

        #ticket-detail-page button .btn.btn-default.btn-white.js-act-edit-feedback {
            margin-bottom: 15px;
        }

        .taggg {
            background: #cc3333;
            border-radius: 3px 0 0 3px;
            color: white !important;
            font-size: 14px !important;
            display: inline-block;
            height: 26px;
            line-height: 26px;
            padding: 0 20px 0 23px;
            position: relative;
            margin: 10px 10px 10px 0;
            text-decoration: none;
            -webkit-transition: color 0.2s;
            float: left;
        }

        .taggg::before {
            background: #fff;
            border-radius: 10px;
            box-shadow: inset 0 1px rgba(0, 0, 0, 0.25);
            /* content: ''; */
            height: 6px;
            left: 10px;
            position: absolute;
            width: 6px;
            top: 10px;
        }

        .taggg::after {
            background: #fff;
            border-bottom: 14px solid transparent;
            border-left: 10px solid #cc3333;
            border-top: 14px solid transparent;
            content: '';
            position: absolute;
            right: 0;
            top: 0;
        }

        #feedBackRating {
            margin-top: 0px;
            padding: 7px;
        }

        #feedBackRating .commentDiv {
            border-top: 3px solid lightgray;
            padding: 5px 0;
        }

        #frm-ticket-history .timeline {
            position: static !important;
            padding-bottom: 3px !important;
            color: #758697 !important
        }

        .modal.right .modal-dialog {
            position: fixed;
            margin: auto;
            /* width: 360px; */
            max-width: 600px;
            height: 100%;
        }

        #result .changed-to {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px; 
        }

        #result {
            /* max-height: 650px; */
            max-height: 80vh;
            overflow-y: auto; 
        }

        .card-history-content .history-icon-container{
            margin-block: 20px;
            padding-inline: 15px;
            display:flex;
            flex-direction:column;
            gap:20px"
        }

        .modal.right .modal-content {
            /* height: 100%; */
            /* height: 100vh; */
            overflow-y: auto;
            scrollbar-width: thin !important;
        }

        .modal.right .modal-body {
            padding: 15px 15px;
            /* min-height: 610px !important; */
            min-height: 85vh !important;
        }

        .modal.right.fade .modal-dialog {
            right: -320px;
        }

        .modal.right.fade.in .modal-dialog {
            right: 0;
        }

        .fa-arrow-circle-right {
            font-size: 30px;
        }

        .timeline-entry.tiny-view.tiny-view-on .timeline-label {
            max-height: 93px;
            overflow: hidden;
            margin-top: 13px;
            margin-bottom: 0px;
        }

        #ticket-detail-page input[type=checkbox] {
            line-height: normal;
            vertical-align: sub;
            position: relative;
            bottom: 2px;
        }

     
     

        p.mar-no.pad-btm.txt1.force-br,
        p.comments {
            border: none;
        }

        #ticket-detail-page a:link {
            text-decoration: none !important;
        }
       
        #cardDialog {
            background: transparent;
            border: none;
            outline: none;
            padding: 0; 
            min-width: 400px; 
            /* width: auto;  */
            margin: auto;
            top: 50%; /
            transform: translateY(-50%); 
            overflow: visible; 
            z-index: 999;
        }   

        dialog::backdrop {
            background: transparent;
            pointer-events: none;
        }

        dialog {
            border: none;
            outline: none;
        }

        #cardDialog .dialog-backdrop {
            position: fixed;
            inset: 0;
            background: transparent;
            z-index: -1;
            cursor: pointer;
        }

        #cardDialog .dialog-content {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 450px;
            overflow: hidden;
            position: relative;
            z-index: 1;
            margin: 0 auto;
        }

        #cardDialog .card-header {
            display: flex;
            height: 40px;
            justify-content: space-between;
            align-items: center;
            padding: 14px 4px 18px 14px;
            border-bottom: 1px solid #e0e0e0;
        }

        .timezone {
            color: #5f6368;
            font-size: 14px;
        }

        #cardDialog .header-actions {
            display: flex;
            gap: 8px;
        }

        #cardDialog .icon-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #5f6368;
            transition: background 0.2s;
        }

        #cardDialog .icon-btn:hover {
            background: #f1f3f4;
        }

        #cardDialog .card-body {
            padding: 14px 18px;
            max-height: 350px;
            overflow-y: auto;
        }

        .event-header {
            display: flex;
            gap: 16px;
            margin-bottom: 16px;
        }

        .event-indicator {
            width: 12px;
            height: 12px;
            background: #0b8043;
            border-radius: 50%;
            margin-top: 6px;
            flex-shrink: 0;
        }

        .event-title {
            font-size: 22px;
            font-weight: 400;
            color: #202124;
            line-height: 28px;
        }

        .event-date {
            color: #5f6368;
            font-size: 14px;
            margin-bottom: 24px;
            padding-left: 28px;
        }

        .event-details {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .detail-row {
            display: flex;
            gap: 16px;
            align-items: flex-start;
        }

        .detail-icon {
            width: 24px;
            height: 24px;
            color: #5f6368;
            flex-shrink: 0;
        }

        .detail-content {
            flex: 1;
        }

        .detail-label {
            font-size: 14px;
            color: #202124;
            margin-bottom: 4px;
            font-weight: 500;
        }

        .detail-text {
            font-size: 14px;
            color: #5f6368;
            line-height: 20px;
        }

        .detail-link {
            color: #5f6368;
            text-decoration: none;
        }

        .detail-link:hover {
            text-decoration: underline;
        }

        .card-details-close svg {
            color: #4E5151;
        }


        /* Title Section */
        .title-section {
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 1px solid #e0e0e0;
        }

        .card-title {
            font-size: 20px;
            font-weight: 500;
            color: #202124;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .card-meta {
            display: flex;
            gap: 8px;
            align-items: center;
            font-size: 13px;
            color: #5f6368;
        }

        .priority-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            line-height: 1.5;
            font-size: 12px;
            font-weight: 500;
        }

        .priority-high {
            background: #fef3f2;
            color: #d92d20;
        }

        .priority-medium {
            background: #fff4ed;
            color: #f79009;
        }

        .priority-low {
            background: #ecfdf3;
            color: #079455;
        }

        /* Section */
        .section {
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 1px solid #e0e0e0;
        }

        .section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        #cardDialog .content-title {
            font-size: 13px;
            font-weight: 600;
            color: #202124;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Content */
        .content-text {
            font-size: 12px;
            color: #3c4043;
            line-height: 1.6;
        }
        .update-name{
            font-size: 13px;
        }
        /* Last Updated */
        .update-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e8eaed;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 500;
            color: #5f6368;
            overflow: hidden;
        }
        .update-details {
            flex: 1;
        }

        .update-name {
            font-size: 14px;
            color: #202124;
            font-weight: 500;
        }

        .update-time {
            font-size: 13px;
            color: #5f6368;
        }

        /* Comments */
        .comments-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .comment-item {
            display: flex;
            gap: 12px;
        }

        .comment-content {
            flex: 1;
        }

        .comment-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }

        .comment-author {
            font-size: 13px;
            font-weight: 500;
            color: #202124;
        }

        .comment-time {
            font-size: 12px;
            color: #5f6368;
        }

        .comment-section p {
            font-size: 14px;
            color: #3c4043;
            line-height: 1.5;
        }

        .no-comments {
            font-size: 14px;
            color: #5f6368;
            font-style: italic;
        }

        /* Attachments */
        .attachments-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .attachment-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .attachment-item:hover {
            background: #f8f9fa;
        }

        .attachment-icon {
            width: 36px;
            height: 36px;
            background: #e8eaed;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #5f6368;
            font-size: 18px;
        }

        .attachment-info {
            flex: 1;
        }

        .attachment-name {
            font-size: 14px;
            color: #202124;
            font-weight: 500;
            margin-bottom: 2px;
        }

        .attachment-size {
            font-size: 12px;
            color: #5f6368;
        }

        .no-attachments {
            font-size: 14px;
            color: #5f6368;
            font-style: italic;
        }

        /* Demo button */
        .demo-btn {
            padding: 12px 24px;
            background: #1a73e8;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
        }

        .demo-btn:hover {
            background: #1557b0;
        }

        /* Scrollbar */
        .dialog-content::-webkit-scrollbar {
            width: 8px;
        }

        .dialog-content::-webkit-scrollbar-track {
            background: transparent;
        }

        .dialog-content::-webkit-scrollbar-thumb {
            background: #dadce0;
            border-radius: 4px;
        }

        .dialog-content::-webkit-scrollbar-thumb:hover {
            background: #bdc1c6;
        }

        #ticket-detail-page .active-user,
        #ticket-detail-page .inactive-user {
            display: inline-block;
            width: 13px;
            height: 13px;
            margin: 0 7px -1px 0;
            border-radius: 10px;
        }

        #ticket-detail-page .active-user {
            background: #5bd810;
        }

        #ticket-detail-page .inactive-user {
            background: #f74c3f;
        }

        #mdl_popup_loader {
            display: none;
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 9999999999999999;
            background: rgba(0, 0, 0, 0.3) url('{{ asset('imgs/loader.gif') }}') center no-repeat;
            transition: all 0.3s ease-in-out;
        }

        #mdl_popup_loader.active {
            display: block;
        }

        /*#manual_file_trigger:hover, #manual_file_trigger:focus {*/
        #manual_file_trigger,
        #card_file_triggers:hover,
        #manual_file_trigger,
        #card_file_triggers:focus {
            border-color: #ffffff;
            color: #ffffff;
        }

        #manual_file_trigger,
        #card_file_triggers,
        #manual_file_trigger_custom {
            border: 1px solid #d8d8d8;
            background: #cd3333;
            border-radius: 3px;
            color: white;
            padding: 4px 15px;
        }

        #manual_file_trigger,
        #file_triggers:hover,
        #manual_file_trigger,
        #file_triggers:focus {
            border-color: #ffffff;
            color: #ffffff;
        }

        #manual_file_trigger,
        #file_triggers,
        #manual_file_trigger_custom {
            border: 1px solid #d8d8d8;
            background: #cd3333;
            border-radius: 3px;
            color: white;
            padding: 4px 15px;
        }

        .ticket-type-fields-div .select2.select2-container--default {
            /* width: 210px !important; */
            width: 100% !important;
        }

        @media (max-width: 1024px) {
            .ticket-type-fields-div .select2.select2-container--default {
                width: 107px !important;
            }

            .fieldsets .pad-rgt {
                padding-top: 11px !important;
                padding-bottom: 9px !important;
            }

            #tkt-problem_type .form-control {
                padding: 10px 12px !important;
                padding-top: 10.1px !important;
            }

            #ticket-detail-page input[type="date"] {
                line-height: 30px !important;
            }
        }

        @media (max-width: 768px) {
            .ticket-type-fields-div .select2.select2-container--default {
                width: 300px !important;
            }

        }

        @media (max-width: 480px) {
            .ticket-type-fields-div .select2.select2-container--default {
                width: 200px !important;
            }

            .navbar-content {
                background-color: #313131;
            }

            #content-container {
                padding-top: 90px;
            }
        }

        @media (max-width: 320px) {
            .ticket-type-fields-div .select2.select2-container--default {
                width: 145px !important;
            }

        }

        #tkt-problem_type .form-control {
            padding: 9px 12px !important;
        }

        .fieldsets .pad-rgt {
            padding-top: 10px !important;
        }

        #tkt-problem_type .select2-container--default .select2-selection--single {
            height: 32px !important;
        }

        .ticket-bio-data p {
            background: #eee;
        }

        .bgpad-rgt {
            display: inline-block;
            width: 145px;
            background: #555;
            padding: 8px 7px;
            color: #FFFFFF;
        }

        .fieldwidth {
            width: 100% !important
        }

        .pdisplay {
            display: flex;
        }

        .ticket-bio-data span.pad-rgt {
            display: inline-block;
            width: 115px;
            background: #555;
            padding: 8px 7px;
            color: #FFFFFF;
        }


        .ticket-bio-data p span.text-dark {
            padding: 0px 6px;
            color: #797979;
            display: inline-block;
        }


        #ticket-detail-page input[type="date"] {
            /*border: none;*/
            line-height: 28px;
            padding: 0px 0px !important
        }

        #ticket-detail-page .customFields {
            display: inline-block;
            /* width: 108px; */
            color: #616161;
        }

        #ticket-detail-page .form-group .select2-container {
            position: relative;
            z-index: 2;
            float: left;
            width: 100%;
            margin-bottom: 0;
            display: table;
            table-layout: fixed;
        }

        .label-spam-info_ticket {
            color: #fff;
            background-color: #777;
            font-size: 13px;
            font-weight: bold;
            border: medium none;
            border-radius: 3px;
            margin: 10px;
            padding: 10px 5px;
        }

        #ticket-detail-page a.btn-link.text-main.text-bold.color-code-bar.color-code-yellow-text {
            color: #ffa726 !important;
        }

        #ticket-detail-page a.btn-link.text-main.text-bold.color-code-bar.color-code-blue-text {
            color: #42518C !important;
        }

        #ticket-detail-page a.btn-link.text-main.text-bold.color-code-bar.color-code-rose-text {
            color: #f275ad !important;
        }

        /* For Desktop View */
        @media screen and (min-width: 992px) and (max-device-width: 1167px) {
            .ticket-bio-data p span.text-dark {
                /*width: 111px;*/
                line-height: 10px;
                /*padding: 5px 6px;*/
                vertical-align: middle;
                font-size: 10px;
            }

            .customFields {
                display: inline-block;
                /*width: 240px;*/
                background: #F2F2F2;
                padding: 11px 5px;
                color: #616161;
            }

            /* #ticket-detail-page input[type="date"]{
                line-height: 24px !important;
            } */
            .customField {
                width: 118px !important;
            }
        }

      
        .truncate-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: inline-block;
            vertical-align: middle;
        }

        .lite-clr-btn-border button:hover {
            padding: 8px 6px;
        }

        .badge i {
            background-color: #dc3545;
            color: #fcfcfc;
            border-radius: 50%;
            padding: 2px;
            margin-left: 5px;
            font-size: 8px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .label-high {
            background: #faad14;
            color: #fff;
            padding: 1px 4px;
            border-radius: 2px;
        }

        .label-low {
            background: #52C41A;
            color: #fff;
            padding: 1px 4px;
            border-radius: 2px;
        }

        .label-critical {
            background: #ff4d4f;
            color: #fff;
            padding: 1px 4px;
            border-radius: 2px;
        }

        .label-medium {
            background: #fadb14;
            color: #fff;
            padding: 1px 4px;
            border-radius: 2px;
        }

        .list-group-item label {
            display: unset;
            max-width: 100%;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-controls textarea {
            height: 40px !important;
        }

        #scrollableDiv {
            /* width: 400px; */
            height: 300px;
            /* border: 1px solid #ccc; */
            overflow-y: auto;
            /* padding: 10px; */
        }

        #ticket-detail-page .attach-item.ai-bg {
            background-size: 100%;
            background-repeat: no-repeat;
        }

        #ticket-detail-page .ai-bg:hover .attach-item-cntnt {
            display: block !important;
        }

        #ticket-detail-page .icons {
            position: absolute;
            width: 124px;
            top: 54px;
            height: 34px;
        }

        #ticket-detail-page .icons span {
            padding: 6px 10px;
            background: #000;
            border-radius: 3px;
            margin-left: 15px;
            cursor: pointer;
            color: #fff;
            display: inline-block;
        }

        .attach-item.ai-bg {
            width: 125px;
            height: 90px;
            overflow: hidden;
            position: relative;
            border: 1px solid #c2c2c2;
            border-radius: 3px;
            display: inline-block;
            margin-right: 7px;
            background-color: #f1f1f1;
        }

        #ticket-detail-page .attach-item .attach-name {
            font-size: 11px;
            position: absolute;
            top: 0;
            width: 124px;
            text-align: center;
            padding: 8px 6px;
            font-weight: 600;
            word-break: break-all;
            max-height: 50px;
            overflow: hidden;
        }

        #ticket-detail-page .ai-bg .attach-item-cntnt {
            display: none;
        }

        #ticket-detail-page .ai-bg:hover .attach-item-cntnt {
            display: block;
        }

        #ticket-detail-page .attach-item {
            width: 125px;
            height: 90px;
            overflow: hidden;
            position: relative;
            border: 1px solid #c2c2c2;
            border-radius: 3px;
            display: inline-block;
            margin-right: 7px;
            background-color: #f1f1f1;
        }

        #ticket-detail-page .attach-item-cntnt {
            position: absolute;
            height: 90px;
            width: 125px;
            top: 0;
            left: 0;
            background: #f1f1f1;
        }

        #ticket-detail-page span.title_2 {
            text-transform: capitalize;
            font-size: 17px;
            font-weight: bold;
            color: black;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            margin: 5px 0px 0px 25px;
        }

        #ticket-detail-page p.tag_wrapper {
            position: absolute;
            top: 25px;
            left: 15px;
            text-align: left;
            font-size: 12px;
            font-weight: 900;
        }

        #ticket-detail-page p.tag_wrapper.badge {
            transform: translate(-50%, -50%);
            font-size: 15px;
            color: white;
            display: inline;
            margin: 1px;
            background-color: black;
            padding: 1px 5px
        }

        #ticket-detail-page span.problem_cat {
            position: absolute;
            top: 163px;
            left: 105px;
            text-transform: capitalize;
            font-size: 15px;
            color: black;
        }

        #ticket-detail-page span.icon-prev,
        span.icon-next {
            color: black;
        }

        .header_wrapper {
            /*height: 37px;*/
            /*font-color: #fff;*/
        }

        .assigned-users {
            display: flex;
            gap: 1px;
            /* margin-left: 20px; */
        }

        .assigned-users img {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ddd;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .assigned-users .userInitials {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ddd;
            cursor: pointer;
            transition: transform 0.2s ease;
            margin-left: -14px;
            font-size: 12px;
        }

        .assigned-users img:hover {
            transform: scale(1.1);
        }

        .assigned-users .userInitials:hover {
            transform: scale(1.1);
        }

        #countMember {
            font-size: 10px;
        }

        #statusKanbanMdl {
            height: 100% !important;
            overflow-y: scroll !important;
        }

        #mdl-filterModal.force-show {
            display: block !important;
            opacity: 1 !important;
        }

        #filter_counting {
            background-color: #cd3333;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            align-items: center;
            justify-content: center;
            position: absolute;
            bottom: 60px;
            right: 13px;
            z-index: 1;
        }

        #mdl-customCard {
            height: 100% !important;
            overflow-y: scroll !important;
        }

        #ticket-mdl {
            height: 100% !important;
            overflow-y: scroll !important;
        }

        .custom-container {
            border-radius: 8px 8px 0px 0px;
            overflow: hidden;
            max-width: 100%;
            margin: 11px;
        }

        .custom-body {
            padding: 15px;
        }

        .custom-header {
            background-color: #cc0000;
            color: white;
            padding: 11px;
            font-size: 16px;
        }

        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            text-align: center;
        }

        .header-item {
            padding: 0px 20px 0px 20px;
        }

        .custom-body .cards {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
            padding: 12px 10px 10px 15px;
            margin: 5px 1px;
        }

        .custom-body .cards h5 {
            margin-bottom: 5px;
            display: inline-block;
            padding-bottom: 5px;
            font-family: Inter;
            font-size: 15px;
            font-weight: 100;
            line-height: 16px;
            letter-spacing: 0.01em;
            color: #424141;
        }

        .custom-body .cards p {
            letter-spacing: 0.01em;
            margin-left: 8%;
        }

        #viewKanbanMdl .attachment-header {
            font-size: 14px;
            font-weight: bold;
            color: #666;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        #viewKanbanMdl .attachment-container {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        #viewKanbanMdl .attachment-item {
            background: #FFF5F5;
            padding: 10px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            width: 220px;
        }

        #viewKanbanMdl .attachment-content {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #viewKanbanMdl .file-icon {
            font-size: 24px;
            color: #CD3333;
        }

        #viewKanbanMdl .file-details {
            display: flex;
            flex-direction: column;
        }

        #viewKanbanMdl .file-name {
            font-weight: bold;
            font-size: 12px;
            color: #000000;
        }

        #viewKanbanMdl .file-info-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            /* Space between file size and actions */
        }

        #viewKanbanMdl .file-size {
            font-size: 14px;
            color: #666;
        }

        #viewKanbanMdl .attachment-actions {
            display: flex;
            gap: 8px;
            /* Space between icons */
        }


        #viewKanbanMdl .tri-view i,
        #viewKanbanMdl .tri-download i {
            cursor: pointer;
            font-size: 14px;
            color: #777;
        }

        #viewKanbanMdl .tri-view i:hover,
        #viewKanbanMdl .tri-download i:hover {
            color: #0056b3;
        }

        #viewKanbanMdl .rounded-circle {
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #bcbcbc;
        }

        #viewKanbanMdl .note-editor {
            border: 0px solid transparent !important;
        }

        #viewKanbanMdl .note-editor .note-toolbar {
            position: absolute !important;
            bottom: 0px !important;
            right: auto !important;
            left: -5px !important;
            top: auto !important;
            height: 43px !important;
            background-color: transparent !important;
            border: none !important;
        }

        #viewKanbanMdl .note-statusbar{
            margin-top:10px !important;
        }

        #viewKanbanMdl .card {
            cursor: default;
        }

        #frm_comment .card {
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.15) !important;
            border-radius: 8px;
            max-width: 100%;
        }

        label.error {
            color: #dc3545 !important;
        }

        #ticket_timeline .card,
        #card_timeline .card {
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.15) !important;
            border-radius: 8px;
            max-width: 100%;
            height: 150px;
            overflow: hidden;
            transition: height 0.3s ease;
            padding-bottom: 5px;
            display: flex;
            flex-direction: column;
        }

        #ticket_timeline .card-body,
        #card_timeline .card-body {
            flex-grow: 1;
            overflow: hidden;
        }

        #ticket_timeline .card-footer,
        #card_timeline .card-footer {
            padding: 2px 18px 14px;
        }


        #swipebox-prev,
        #swipebox-next {
            font-size: 24px;
            color: white;
            padding: 10px;
            border-radius: 50%;
        }

        #viewKanbanMdl .load-more {
            width: 100%;
            font-size: medium;
            background: #ffffff;
            border: 1px solid #CD33334D;
            border-radius: 5px;
        }

        #viewKanbanMdl #header-button {
            position: absolute;
            bottom: 12px;
            right: 70px;
            font-size: 23px;
            padding-right: 8px;
        }

        .tl-note-background {
            background: #fffcd3 !important;
        }

        .user_data {
            display: flex;
            gap: 3px;
            overflow-x: auto;
            white-space: nowrap;
            max-width: 55px;
        }

        .user_data::-webkit-scrollbar {
            display: none;
        }

        .user_data {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .user-stack {
            position: relative;
            display: inline-block;
        }

        .main-user {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid white;
        }

        .color-code-bar.color-code-resolved {
            background: #E5FFEC !important;
            border: 1px solid black;
        }

        .color-code-bar.color-code-closed {
            background: #ECECEC !important;
            border: 1px solid black;
        }

        #board-legends {
            height: auto;
            font-family: inter;
            border-radius: 5px;
            padding: 4px 6px;
            display: flex;
            align-items: center;
            justify-content: start;
            width: fit-content;
        }

        #board-legends ul{
            padding: 2px;
            margin:0;
        }

        .show-more-legends {
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%); 
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 4px 8px;
            font-size: 12px;
            cursor: pointer;
            z-index: 10; 
        }


        #legendsList .modal-body {
            max-height: 300px;
            background: transparent !important;
            padding: 20px;
            overflow-y: auto;
        }

        #legendsList #board-legends {
            background-color: transparent !important;
            color: white;
            font-family: inter;
            border-radius: 5px;
            height: 100%;
            width: 100%;
        }



        .select-board .select2-container--default .select2-selection--single {
            background-color: #fff;
            border: 1px solid #e9e9e9;
            border-radius: 5px;
        }
       
        .select-board .select2-container .select2-selection--single .select2-selection__rendered {
            display: block;
            padding-left: 15px;
            padding-right: 30px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .select-board .select2-container--default .select2-selection--single {
            background-color: #fff;
            border: 1px solid #e9e9e9;
            border-radius: 8px;
        }

        .select-board .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #888 transparent transparent transparent;
            border-style: solid;
            border-width: 5px 4px 0 4px;
            height: 0;
            left: 50%;
            margin-left: -8px;
            margin-top: -2px;
            position: absolute;
            top: 50%;
            width: 0;
        }
        #kanban-board-controls .select2-selection--single{
            min-width: 200px ;
            width: 100% ;
        }
        .board-icon-active path,.board-icon-active{
            fill:#CD3333;
            opacity: 1 !important;
        }
        .user-count {
            position: absolute;
            right: -5px;
            bottom: 0;
            width: 25px;
            height: 25px;
            background-color: #e0e0e0; 
            color: rgb(20, 20, 20);
            border-radius: 50%;
            font-size: 10px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgb(225, 222, 222);
            cursor: pointer;
        }

        .card-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            width: 100%;
            gap: 2px;
            margin-bottom: 1%;
        }
        .left-column {
            display: flex;
            flex-direction: row;
            flex: 1;
            gap: 4px;
            align-items: center;
        }

        .field-row.timeline-card {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-top: 5px;
        }

        .timeline-card-item {
            position: relative;
            padding-left: 5px;
            margin-bottom: 2px;
        }


        .timeline-card-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 6px;
            width: 7px;
            height: 7px;
            background-color: #00774B;
            border-radius: 50%;
            box-shadow: 0 0 0 2px white, 0 0 0 3px #00774B;
            z-index: 1;
        }

        .timeline-card-item:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 3px;
            top: 15px;
            height: calc(100% - 5px);
            border-left: 1.5px solid #00774B;
            z-index: 0;
        }



        .timeline-card-label {
            font-size: 12px;
            color: #000000;
            line-height: 21.78px;
            margin-left: 15px;
            font-weight: 400;
        }
        .priority-box {
            display: flex;
            background: #FFECE1B2;
            color: #FF5C00;
            font-size: 10px;
            font-weight: 500;
            letter-spacing: .3px;
            text-transform: uppercase;
            padding: 4px 7px;
            border-radius: 4px;
            align-items: center;
            max-width: 60px; 
        }

        .priority-box span {
            display: block; 
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            min-width: 0;
            flex: 1;    
        }


        .tat-box {
            display: flex;
            align-items: center;
            background: #E1F6FFB2;
            color: #2C62B4;
            font-size: 10px;
            font-weight: 500;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            padding: 4px 7px;
            border-radius: 4px;
            max-width: 60px;
        }

        .tat-box span {
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            min-width: 0;
            flex: 1;
        }

        .ticket-id-box {
            display: inline-block;
            background: #EEEEEE;
            color: black;
            font-size: 10px;
            font-weight: 500;
            line-height: 18px;
            letter-spacing: .3px;
            text-transform: uppercase;
            padding: 4px 7px;
            text-underline-position: from-font;
            text-decoration-skip-ink: none;
            border-radius: 4px;
        }
        .priority-dot {
            display: inline-block;
            border-radius: 50%;
            background-color: #FF5C00;
            margin-right: 5px;
            width: 7px;
            height: 7px;
            top: 2px;
            left: 4px;
            gap: 0px;
            opacity: 0px;
        }


        .progress-circle-container {
            position: relative;
            display: inline-block;
            border-radius: 50%;
            height: 50px;
            width: 50px;
        }

        #card-actions span{
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .progress-circle {
            position: relative;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: conic-gradient(var(--border-color) calc(var(--percentage) * 1%), #ddd 0%);
            padding: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .progress-bar-circle {
            position: relative;
            width: 45px;
            height: 45px;
            background: #fff;
            border-radius: 50%;
            z-index: 1;
        }


        .fa-warning {
            position: absolute;
            font-size: 15px;
            color: #FF5C00;
            z-index: 2;
        }

        .fa-thumbs-up {
            position: absolute;
            font-size: 15px;
            color: #1BAA50;
            z-index: 2;
        }

        .fa-thumbs-down {
            position: absolute;
            font-size: 15px;
            color: #ff9800;
            z-index: 2;
        }

        .fa-minus-circle {
            position: absolute;
            font-size: 15px;
            color: #313131;
            z-index: 2;
        }

        .light-gray-line {
            border: none;
            height: 1px;
            background-color: #d3d3d3;
            width: 100%;
            margin-bottom: inherit;
            margin-top: -9px;
        }

        .user-image {
            border: 2px solid #d3d3d3;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 5px;
        }

        .subject {
            font-weight: 600;
            font-size: 14px;
            line-height: 24.2px;
            display: flex;
            align-items: center;
            width: 100%;
            color: #ffff;
            height: 100%;
            margin: 0;
            justify-content: space-between;
        }

        .subject i.ps-icon.fa{
            color: white;
            opacity: .7;
        }

        .subject i.ps-icon.fa:hover{
            opacity: .9;
        }

        .card .fa-clock-o {
            gap: 0px;
            opacity: 0px;
            color: #2C62B4
        }

        #expectedDate {
            height: 35px;
        }
        .group-by-item {
            position: relative;
            display: block;
            padding: 10px 15px;
            margin-bottom: -1px;
            border: 1px solid #ddd;
        }

        .specific_loadingContainer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.2);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 5;
        }

        .specific_loading {
            /* width: 40px; */
            left: 40%;
            /* height: 40px; */
            top: 50%;
            position: absolute;
            /* border: 4px solid rgba(0, 0, 0, 0.105); */
            /* border-top: 4px dotted #000000; */
            /* border-radius: 50%; */
            animation: spin 4s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .column-fields {
            max-height: 450px !important; 
            overflow-y: auto !important; 
        }
        .field.pull-right.user_data .tooltip-inner {
            background-color: #cd3333;
            color: white
        }

        .field.pull-right.user_data .bs-tooltip-top .arrow::before,
        .field.pull-right.user_data .bs-tooltip-bottom .arrow::before,
        .field.pull-right.user_data .bs-tooltip-left .arrow::before,
        .field.pull-right.user_data .bs-tooltip-right .arrow::before {
            border-top-color: #cd3333 !important;
        }
        #viewKanbanMdl{
            height: 100% !important;
            overflow-y: scroll !important;
        }
        #frm-ticket-history .timeline-label {
            display:table-cell;
        }
        #frm-ticket-history [class^="col-"] {
            padding: 0 7px !important;
        }

        .ticket-details p {
            background: transparent;
            padding: 0;
            font-size: 12px;
            border: 1px solid #eaeaea;
            margin: 0 0 2px 0;
            border-radius: 3px;
            overflow: hidden;
            font-family: Helvetica, Arial, sans-serif;
        }
        .ticket-details span.pad-rgt {
            display: inline-block;
            width: 108px;
            background: #f2f2f2;
            padding: 5px 5px;
            color: #616161;
        }
        .ticket-details p span.text-dark {
            padding: 5px 6px;
            color: #797979;
            display: inline-block;
        }
        .timeline:before, .timeline:after {
            /* background-color: #bec6ce; */
        content: "";
        display: none;
        position: absolute;
        }

        #result .history-detail-info-modified-by span {
            font-size: 14px;
            font-weight: 600;
            line-height: 140%;
            color: #202020;
        }

        #result .modfied-by-image {
            height: 20px;
            width: 20px;
        }

        #result .history-detail-info-container {
            flex: 1;
            padding: 12px 16px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            border: 1px solid #D5D5D5;
            border-radius: 8px;
            border-left: 7px solid #D80505;
        }

        #result .history-detail-info-modified-status, .history-detail-info-modified-by {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 8px;
        }

        #result .history-detail-info-modified-status a {
            font-size: 13px;
            font-weight: 400;
            line-height: 100%;
            color: #08558b;
        }
        #result .history-detail-info-time-row-title {
            font-size: 12px;
            opacity: 70%;
        }

        #result .history-detail-info-time-text svg {
            opacity: 50%;
            height: 13px;
            width: 13px;
        }

        #result .history-detail-info-time-text {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        #result .history-detail-info-accrodian-icon {
            /* margin-block: 8px; */
            opacity: 50%;
            width: 18px;
            height: 18px;
        }
        .tml-content {
            display: block;
            overflow-x: auto;
            margin-top: 5px;
        }
        .timeline-time::after {
            content: '';
            position: absolute;
            width: 1px;
            top: 0;
            bottom: 0;
            left: 52px;
            margin-left: -3px;
            height: 22px;
        }
        .mar-btm-6 {
            margin-bottom:0px;
        }
        .pad-btm {
            padding-bottom:0px;
        }

        #frm-ticket-history .modal-body #result {
            overflow-y: scroll;
            max-height: 510px;
        }
        .timeline_overall_x {
            max-width: 200px;
            overflow-x: auto;
        }
        #mdl-history .modal-body #result{
            overflow-y: scroll;
            max-height: 510px;
        }     

        .custom-body .cards .card-title {
            font-size: 18px;
            margin-bottom: 0px;
            margin-top: 5px;
            margin-bottom:5px;
        }
        .history-timeline-item {
            display: flex;
            flex-direction: row;
            gap: 24px;
            /* height: 121px; */
            /* border: 1px solid red; */
        }

        #loadKanbanCardHistory{
            max-height: 550px;
            overflow-y: auto;
        }

        .history-timeline-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: 1px solid #D5D5D5;
            color: #666;
            flex-shrink: 0;
        }
        
        .history-timeline-icon svg {
            opacity: 30%;
            height: 24px;
            width: 24px;
        }
        
        .history-timeline-line {
            width: 1px;
            background-color: #D5D5D5;
            flex: 1;
            /* min-height: 60px; */
        }
        
        .history-icon-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0;
        }
       
        .history-detail-container{
            display: flex;
            flex-direction:row;
            align-items:center;
            flex:1;border-radius: 7px;
            border: 1px solid #D5D5D5;
            overflow: hidden;
        }

        .history-detail-border{
            height: 121px;
            width:7px;
            background-color:#F73019;
            border-top-left-radius:25px;
            border-bottom-left-radius: 25px;
        }

        .history-detail-info-container {
            flex: 1;
            padding:12px 16px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            border: 1px solid #D5D5D5;
            border-radius: 8px;
            border-left: 7px solid #D80505;
        }

        .history-detail-info-time-row{
            display: flex;flex-direction:row;
            align-items:center;
            justify-content:space-between; 
        }
        .history-detail-info-time-text{
            display:flex;
            flex-direction:row;
            align-items:center;
            justify-content:center;
            gap:7px;
        }

        .history-detail-info-time-row a{
            text-decoration: none;
            color: #141415;
            outline: 0;
            display: flex;
            align-items: center;
        }

        .history-detail-info-time-text svg {
            opacity: 50%;
            height: 16px;
            width: 16px;
        }

        .history-detail-info-time-row-title{
            font-size:14px;
            opacity:70%;
        }

        .history-detail-info-accrodian-icon{
            /* margin-block: 8px; */
            opacity: 50%;
            width: 24px;
            height: 24px;
        }

        .history-detail-info-modify-status{
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 12;
        }
        .history-detail-info-modified-status, .history-detail-info-modified-by{
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 12px;
        }
        .modfied-by-image {
            height: 29px;
            width: 29px;
        }
        .modfied-by-image img{
            height: 100%;
            width: 100%;
            object-fit: contain;
            border-radius: 50%;
        }

        .history-detail-info-modified-by span{
          font-size: 18px;
          font-weight: 600;
          line-height: 140%;
          color: #202020;
        }
        .history-detail-info-modified-status-middot{
            font-size: 35px;
            line-height: 100%;
            color: #202020;
        }
        .history-detail-info-modified-status a{
            font-size: 16px;
            font-weight: 400;
            line-height: 140%;
            color: #08558b;
        }

        .history-detail-info-changed{
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 8px;
        }

        .history-detail-info-changed span{
            font-size: 14px;
            font-weight: 400;
            line-height: 140%;
            opacity: 70%;
        }

        #result .history-timeline-icon svg {
            opacity: 30%;
            height: 23px;
            width: 23px;
        }

        .history-detail-info-changed svg{
            width: 15px;
            height: 13px;
            opacity: 50%;
        }

        .history-detail-info-accrodian-icon {
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .history-detail-info-accrodian-icon.expanded {
            transform: rotate(180deg);
        }
        .accordion-content {
            display: grid;
            grid-template-rows: 0fr;
            transition: grid-template-rows 0.3s ease, opacity 0.3s ease, margin-top 0.3s ease;
            opacity: 0;
        }

        .accordion-content.expanded {
            grid-template-rows: 1fr;
            opacity: 1;
            margin-top: 6px;
        }

        .accordion-content-inner {
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }


        .wrapper_tab_List {
            width: 100%;
        }

        .black-slide-links {
            border-bottom: 1px solid #e0e0e0;
            padding: 0;
            margin: 0;
        }

        .black-slide-links li {
            margin-bottom: -1px;
            margin-right: 20px;
        }

        .black-slide-links li a {
            border: none;
            border-bottom: 3px solid transparent;
            background: transparent;
            color: #666;
            font-size: 14px;
            font-weight: 500;
            padding: 12px 16px;
            transition: all 0.3s ease;
        }

        .black-slide-links li a:hover {
            background: transparent;
            border: none;
            border-bottom: 3px solid #ccc;
            color: #333;
        }

        .black-slide-links li.active a,
        .black-slide-links li.active a:hover,
        .black-slide-links li.active a:focus {
            border: none;
            border-bottom: 3px solid #D80505; 
            background: transparent;
            color: #333;
            font-weight: 600;
        }

        .black-slide-links li a:focus {
            outline: none;
            background: transparent;
        }

    

        #load-more-history {
            padding: 10px 30px;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        #load-more-history {
            padding: 10px 30px;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        #load-more-history svg {
            display: block;
            /* animation: bounce 1.5s ease-in-out infinite; */
        }

        #load-more-history svg path {
            fill: #a0a0a0;
            transition: fill 0.3s ease;
        }

        #load-more-history:hover svg path {
            fill: #D80505;
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-8px);
            }
        }

        #load-more-history:hover svg {
            transform: translateY(-5px);
        }
    
        .nav-tabs > li.active > a, .nav-tabs > li.active > a:focus, .nav-tabs > li.active > a:hover {
            background-color: #333;
            color: #fff;
            border: 1px solid #ddd;
            border-bottom-color: transparent;
        }
        #api_loader{
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(0,0,0,0.3);
            z-index: 10;
        }
        .loading {
            font-style: italic;
            padding-inline: 10px;
            color: #6b7280; /* neutral SaaS gray */
        }

        .dots span {
            animation: blink 1.4s infinite both;
        }

        .dots span:nth-child(1) {
            animation-delay: 0s;
        }
        .dots span:nth-child(2) {
            animation-delay: 0.2s;
        }
        .dots span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes blink {
            0% { opacity: 0; }
            20% { opacity: 1; }
            100% { opacity: 0; }
        }
        
        .customFieldset{
            border: 1px solid var(--app-border) !important;
            border-radius: 8px !important;
        }

        .fieldsets {
            border: 1px solid var(--app-border) !important;
            border-radius: 8px !important;
        }
        .task-card #expandTabs .btn-add-task {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-height: 28px;
            border: 1px solid #ed1117;
            border-radius: 4px;
            color: #ed1117;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 10px;
        }
    </style>
@endpush

@push('scripts')
    <script src="{!! CommonHelper::asset('js/task_management/history.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('plugins/swipebox/js/jquery.swipebox.min.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/form/depends_render.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/tickets/kanban-board/index.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/customfield.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/tickets/create_ticket.js') !!}"></script>
    <script src="{{ CommonHelper::asset('js/drag-drap.js') }}"></script>
    <script src="{!! CommonHelper::asset('js/common.js') !!}"></script>
    <script src="{!! CommonHelper::asset('assets/libs/jquery-ui/dist/jquery-ui.min.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('plugins/moment-business-days/index.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('plugins/moment-timezone/moment-timezone.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/form/depends_render.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('plugins/flatpicker/js/flatpicker.js') !!}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var config = {};
            config.url = {};
            config.url.baseurl = "{{ config('app.url') }}";
            config.asset = "{{ asset('') }}";
            config.url.getCompanyWiseLocation = "{{ url('get-company-wise-location') }}";
            config.url.getInternalPlaceByAjax = "{{ url('getInternalPlaceByAjax') }}";
            config.url.KanbanBoardList = "{{ url('tickets/kanban-board/board-list') }}";
            config.url.departments_with_company = "{{ url('tickets/departments') }}";
            config.url.problem_categories_by_company = "{{ url('tickets/problem-categories/by-dept') }}";
            config.url.store_kanaban = "{{ url('tickets/js-kanban-store') }}";
            config.url.edit_kanaban = "{{ url('tickets/js-kanban-edit') }}";
            config.url.update_kanban = "{{ url('tickets/js-kanban-update') }}";
            config.url.delete_kanban = "{{ url('tickets/js-kanban-delete') }}";
            config.url.member_kanban = "{{ url('tickets/kanban-member') }}";
            config.url.GetBoardData = "{{ url('ticket/get-board-data') }}";
            config.url.getGroupedBoardData = "{{ url('ticket/get-grouped-board-data') }}";
            config.url.get_users_to_assign_by_avability = "{{ url('ticket/get_users_to_assign_by_availability') }}";
            config.url.get_users_to_assign_by_dep_by_availability = "{{ url('ticket/get_users_to_assign_by_dep_by_availability') }}";
            config.url.assign_to = "{{ url('ticket/assign_to') }}";
            config.url.get_tickets = "{{ url('tickets/list/jx-ticket-detail') }}";
            config.url.get_archived_tickets = "{{ url('tickets/list/jx-ticket-archived-detail') }}";
            config.url.ticket_history = "{{ url('ticket/ticket_history') }}";
            config.url.ticket_history_archived = "{{ url('ticket/ticket_history_archived') }}";
            config.url.get_kanban_fields = "{{ url('tickets/getKanbanBoardFields') }}";
            config.url.getTechCurrentStatusById = "{{ url('technician/get-tech-curren-status-id') }}";
            config.url.get_timeline = "{{ url('ticket/get_timeline') }}";
            config.url.get_timeline_archived = "{{ url('ticket/get_timeline_archived') }}";
            config.url.attachment_add = "{{ url('ticket/attachment/add') }}";
            config.url.attachment_remove = "{{ url('ticket/attachment/remove') }}";
            config.url.attachment_download = "{{ url('ticket/attachment/download') }}";
            config.url.attachment_view = "{{ url('ticket/attachment/view') }}";
            config.url.viewTicket = "{{ url('tickets/list/jx-ticket-card-view') }}";
            config.url.update_status = "{{ url('ticket/update_status') }}";
            config.url.board_item_ticket_update = "{{ url('tickets/board-item-ticket-update') }}";
            config.url.loadCards = "{{ url('tickets/loadCards') }}";
            config.url.loadCustomCards = "{{ url('tickets/loadCustomCards') }}";
            config.url.getUserByAjax = "{{ url('getUserByQuery') }}";
            config.url.get_data_for_transfer = "{{ url('ticket/get-data-for-transfer') }}";
            config.url.requested_form = "{{ url('requested_form') }}";
            config.url.service_request_form = "{{ url('tickets/serviceRequestForm') }}";
            config.permissions = {!! json_encode($permissionArray) !!};
            config.priorities = {!! json_encode($priorities) !!};
            config.url.editTicket = "{{ url('ticket/edit') }}";
            config.url.transfer = "{{ url('ticket/transfer') }}";
            config.url.submit_problem_mgt = "{{ url('problem-management/add_impacted_ticket') }}";
            config.url.problem_mgt = "{{ url('jx-get-problem-mgt-select2') }}";
            config.url.problem_mgt_delete = "{{ url('problem-management/remove_impacted_ticket') }}";
            config.user = {!! json_encode(
                Auth::user()->only('id', 'first_name', 'last_name', 'username', 'company_id', 'location', 'displayName'),
            ) !!};
            config.full_name = {!! json_encode(Auth::user()->fullName()) !!};
            config.url.getUserDeviceByAjax = "{{ url('getUserDeviceForDropDown') }}";
            config.url.loadMemberImage = "{{ url('tickets/loadMemberImage') }}";
            config.url.removeBoardMember = "{{ url('tickets/removeBoardMember') }}";
            config.url.kanbanCardExport = "{{ url('tickets/kanban-card-export') }}";
            config.url.getFormByTicketProblemCategory = "{{ url('get-status-form') }}";
            config.url.getTickets = "{{ url('tickets/getTickets') }}";
            config.url.storeBoardItemTicket = "{{ url('tickets/store-board-item-ticket') }}";
            config.url.card_attachment_add = "{{ url('tickets/attachment/card_attachment') }}";
            config.url.archived_card = "{{ url('custom/archive-cards') }}";
            config.url.card_attachemnt_remove = "{{ url('tickets/attachment/card_attachment_remove') }}";
            config.url.getcard = "{{ url('tickets/kanban-board/get-card') }}";
            config.url.updateCard = "{{ url('tickets/kanban-board/update-card') }}";
            config.url.card_attachment_download = "{{ url('tickets/attachment/card_attachment_download') }}";
            config.url.card_delete = "{{ url('ticket/kanban-board/delete_card') }}";
            config.url.card_add = "{{ url('ticket/kanban-board/add_card') }}";
            config.url.getTagDetails = "{{ url('ticket/getTagDetails') }}";
            config.url.user_basic_info = "{{ url('user/basic-info') }}";
            config.url.getUserCCByAjax = "{{ url('getUserCCByQuery') }}";
            config.url.create_ticket = "{{ url('tickets/create') }}";
            config.url.getDepartmentCustomFields = "{{ url('ticket/getDepartmentCustomFields') }}";
            config.url.import = "{{ url('tickets/kanban-board/import') }}";
            config.url.updateColumnOrder = "{{ url('tickets/kanban-board/updateColumnOrder') }}";
            config.url.image = "{{ asset('images/kanban_image.png') }}";
            config.url.getRelatedTask = "{{ url('tickets/get-relevant-tasks') }}";
            config.url.taskInfoPage = "{{ url('task-management/info')}}";
            config.url.Taskhistory = "{{ url('task-management/taskhistory') }}";
            config.url.editKanbanItem = "{{url('tickets/kanban-board/update-kanban-title')}}";
            config.url.cloneKanbanItem = "{{url('tickets/kanban-board/clone-item')}}";
            config.url.getItemsByBoard = "{{url('tickets/kanban-board/get-items-by-board')}}";
            config.url.getItemsAttachments = "{{url('kanban-card/attachments')}}";
            config.url.getQueryComponent = "{{ url('getByCustomDropDown/getComponent') }}";
            config.url.getByQueryDevice = "{{ url('getByCustomDropDown/getDevice') }}";
            config.url.getQueryLocation = "{{ url('getByCustomDropDown/getLocation') }}";
            config.url.getQueryTicket = "{{ url('getByCustomDropDown/getTicket') }}";
            config.url.getQueryUser = "{{ url('getByCustomDropDown/getUser') }}";
            config.url.getQueryPlace = "{{ url('getByCustomDropDown/getPlace') }}";
            config.url.getQueryManufacture = "{{ url('getByCustomDropDown/getManufacture') }}";
            config.url.getQueryModel = "{{ url('getByCustomDropDown/getModel') }}";
            config.url.getQueryTicketProcureRequest = "{{ url('getByCustomDropDown/getTicketProcureRequest') }}";
            config.url.getQueryRecord = "{{ url('getByCustomDropDown/getRecord') }}";
            config.url.getQueryTask = "{{ url('getByCustomDropDown/getTask') }}";
            config.url.getQueryLicense = "{{ url('getByCustomDropDown/getLicense') }}";
            config.url.getQueryProject = "{{ url('getByCustomDropDown/getProject') }}";
            config.url.getQueryPurchase = "{{ url('getByCustomDropDown/getPurchase') }}";
            config.url.getQuerySupplier = "{{ url('getByCustomDropDown/getSupplier') }}";
            config.url.getQueryContract = "{{ url('getByCustomDropDown/getContract') }}";
            config.url.getCustomFieldNote = "{{ url('getCustomFieldNote') }}";
            config.url.getBoardItemStatus = "{{ url('tickets/getKanbanItemStatusInfo') }}";
            config.url.getCardHistory = "{{ url('tickets/kanban-board/getCardHistory') }}";
            config.url.get_company_by_user_access = "{{ url('getCompanyByUserAccess') }}";
            config.url.fetchccEmailID = "{{ url('tickets/fetch-cc-users-email-id') }}";
            config.url.getRelatedDocuments = "{{ url('kd/getRelatedDocuments') }}";
            config.url.articleImagePath = "{{ url('uploads/article') }}";
            config.url.view_article = "{{ url('knowledge_document/article/view') }}";
            config.url.create_ticket_by_user = "{{ url('tickets/create-by-user') }}";
            config.url.base_url = "{{ url('') }}";
            config.url.sub_category = "{{ url('tickets/fetch-sub-category') }}";
            config.url.getCustomFieldsValueForFilter = "{{ url('ticket/getCustomFieldsValueForFilter') }}";
            config.url.customFieldsForFilter = "{{ url('ticket/getCustomFieldsForFilter') }}";
            config.url.getKanbanCustomFieldsValueForFilter = "{{ url('ticket/getKanbanCustomFieldsValueForFilter') }}";
            config.sort_dir = {id: '',dir: ''};
            config.sort_fields = {!! json_encode($sort_fields) !!};
            config.group_fields = {!! json_encode($group_fields) !!};
            config.statuses = {!! json_encode($statuses) !!};
            config.created_via = {!! json_encode($created_via) !!};
            config.departments = {!! json_encode($departments) !!};
            config.tkt_config = {!! json_encode($tkt_config) !!};
            config.user.action_controls = {!! json_encode($action_controls) !!};
            config.company_defulte = {!! json_encode($company_id) !!};
            config.user.limits = "{{ Auth::user()->hasPermission("service_tickets") }}";
            config.isNormalUser = @json(Auth::user()->isNormalUser());
            config.taskModule = {{ config('services.task_module.enabled') ? 1 : 0 }};
            config.url.user_info = "{{ url('user/info') }}";
            config.translations = {
                something_went_wrong: '{{ trans('content.user_fields.something_went_wrong') }}',
                serach_option: '{{ trans('ticket.service_ticket_fields.serach_option') }}',
                search: '{{ trans('ticket.service_ticket_fields.search') }}',
                add_new: '{{ trans('ticket.service_ticket_fields.add_new') }}',
                Refresh_List: '{{ trans('ticket.service_ticket_fields.Refresh_List') }}',
                select_department: '{{ trans('ticket.service_ticket_fields.select_department') }}',
                select_prob_category: '{{ trans('ticket.service_ticket_fields.select_prob_category') }}',
                select_sub_category: '{{ trans('ticket.service_ticket_fields.select_sub_category') }}',
                edit: '{{ trans('ticket.service_ticket_fields.edit') }}',
                delete: '{{ trans('ticket.service_ticket_fields.delete') }}',
                members: '{{ trans('ticket.service_ticket_fields.members') }}',
                add_kanaban: '{{ trans('ticket.service_ticket_fields.add_kanaban') }}',
                are_you_delete: '{{ trans('ticket.service_ticket_fields.are_you_delete') }}',
                Available_Records: '{{ trans('content.knowledge_document.Available_Records') }}',
                No_records_Found: '{{ trans('content.knowledge_document.No_records_Found') }}',
                Assign_Ticket_To: '{{ trans('content.service_ticket_fields.Assign_Ticket_To') }}',
                upload_file: '{{ trans('content.service_ticket_fields.upload_file') }}',
                Enter_starting: '{{ trans('content.service_ticket_fields.Enter_starting') }}',
                Select_Problem_Category: '{{ trans('content.service_ticket_fields.Select_Problem_Category') }}',
                please_select: '{{ trans('content.service_ticket_fields.please_select') }}',
                select_device: '{{ trans('content.service_ticket_fields.select_device') }}',
                tags: '{{ trans('content.service_ticket_fields.tags') }}',
                Subject: '{{ trans('content.service_ticket_fields.Subject') }}',
                ticket_id: '{{ trans('content.service_ticket_fields.ticket_id') }}',
                Company: '{{ trans('content.service_ticket_fields.Company') }}',
                Related_Device: '{{ trans('content.service_ticket_fields.Related_Device') }}',
                Prob_Category: '{{ trans('content.service_ticket_fields.Prob_Category') }}',
                Prob_Subcategory: '{{ trans('content.service_ticket_fields.Prob_Subcategory') }}',
                feedback: '{{ trans('content.service_ticket_fields.feedback') }}',
                feedback_is: '{{ trans('content.service_ticket_fields.feedback_is') }}',
                Changes_Status_To: '{{ trans('content.service_ticket_fields.Changes_Status_To') }}',
                Changes_Status_From: '{{ trans('content.service_ticket_fields.Changes_Status_From') }}',
                Changes_Priority_From: '{{ trans('content.service_ticket_fields.Changes_Priority_From') }}',
                Ticket_Transfer_Department_From: '{{ trans('content.service_ticket_fields.Ticket_Transfer_Department_From') }}',
                Ticket_Transfer_Department: '{{ trans('content.service_ticket_fields.Ticket_Transfer_Department') }}',
                Ticket_Transfer_Category_Changes_From: '{{ trans('content.service_ticket_fields.Ticket_Transfer_Category_Changes_From') }}',
                Ticket_Transfer_Category_Changes_To: '{{ trans('content.service_ticket_fields.Ticket_Transfer_Category_Changes_To') }}',
                To: '{{ trans('content.service_ticket_fields.to') }}',
                Ticket_Transfer_subCategory_Changes_From: '{{ trans('content.service_ticket_fields.Ticket_Transfer_subCategory_Changes_From') }}',
                Changes_Done_By: '{{ trans('content.service_ticket_fields.Changes_Done_By') }}',
                Ticket_Transfer_subCategory_Changes_To: '{{ trans('content.service_ticket_fields.Ticket_Transfer_subCategory_Changes_To') }}',
                New_ticket_created_to: '{{ trans('content.service_ticket_fields.New_ticket_created_to') }}',
                New_ticket_created: '{{ trans('content.service_ticket_fields.New_ticket_created') }}',
                Ticket_Edit_Category_Changes_From: '{{ trans('content.service_ticket_fields.Ticket_Edit_Category_Changes_From') }}',
                Changed_tats: '{{ trans('content.service_ticket_fields.Changed_tats') }}',
            };

            config.token = "{{ csrf_token() }}";

            new boardList(config);
            new cardMoveToChangeStatus(config);
            new CreateTicket(config);
        });
    </script>
@endpush

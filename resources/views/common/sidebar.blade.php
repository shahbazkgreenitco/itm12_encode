{{-- @page-meta
{
  "page_no": "LYSDB-04",
  "file": "sidebar.blade.php",
  "versions": [
    {
      "version": "1.4",
      "writer": "Muzaffar Shaikh",
      "from": "2026-04",
      "reviewer": null,
      "description": "Redesign Sidebar to maintain screen ratio"
    },
    {
      "version": "1.5",
      "writer": "Prithvi Pillai",
      "from": "2026-06",
      "reviewer": null,
      "description": "Changes for not showing the user tab if permission not given issue fixed"
    },
    {
      "version": "1.6",
      "writer": "Prithvi Pillai",
      "from": "2026-07",
      "reviewer": null,
      "description": "Changes for the myitems and devices icons, permissions and translation changes"
    },
    {
        "version": "1.7",
        "writer": "Muzaffar Shaikh",
        "from": "2026-07",
        "reviewer": null,
        "description": "Sidebar UX improvements: fixed auto-close on navigation, added hover-to-preview and hover effects on mini-icons, active-state highlighting for all links/submenus, tooltips, and global sidebar search"
    }
  ]
}
--}}
    <!-- DESKTOP SIDEBAR -->
    <aside class="mini-sidebar desktop-only app-sidebar">
        <div class="global-sidebar-search">
            <button type="button" id="globalSearchTrigger" class="global-search-icon-btn" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Search Menu">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                    <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>

            <div id="globalSearchOverlay" class="global-search-overlay hidden">
                <div class="global-search-box">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                        <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <input type="text" id="globalSidebarSearch" class="global-search-input" placeholder="Search menu...">
                    <button type="button" id="globalSearchClose" class="global-search-close">✕</button>
                </div>
                <div id="globalSearchResults" class="global-search-results hidden"></div>
            </div>
        </div>
        <div class="scroll-indicator scroll-up hidden">▲</div>
        <div class="mini-sidebar-scroll">
            <!-- no expanded sidebar -->
        <a class="mini-icon @if(Request::path() == 'dashboard' || Request::path() == '/') active @endif"
            href="{{ route('dashboard') }}" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="{{ trans('sidebar.mini.home') }}" data-section="home">
                <div class="icon-wrapper">
                    <svg width="25" height="25" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M23.987 11.8872L15.237 3.13716C14.9089 2.80922 14.4639 2.625 14 2.625C13.5361 2.625 13.0911 2.80922 12.763 3.13716L4.01298 11.8872C3.84969 12.0492 3.72025 12.2422 3.63218 12.4547C3.54412 12.6673 3.49919 12.8952 3.50001 13.1253V23.6253C3.50001 23.8573 3.5922 24.0799 3.75629 24.244C3.92039 24.4081 4.14295 24.5003 4.37501 24.5003H11.375C11.6071 24.5003 11.8296 24.4081 11.9937 24.244C12.1578 24.0799 12.25 23.8573 12.25 23.6253V17.5003H15.75V23.6253C15.75 23.8573 15.8422 24.0799 16.0063 24.244C16.1704 24.4081 16.3929 24.5003 16.625 24.5003H23.625C23.8571 24.5003 24.0796 24.4081 24.2437 24.244C24.4078 24.0799 24.5 23.8573 24.5 23.6253V13.1253C24.5008 12.8952 24.4559 12.6673 24.3678 12.4547C24.2798 12.2422 24.1503 12.0492 23.987 11.8872ZM22.75 22.7503H17.5V16.6253C17.5 16.3932 17.4078 16.1707 17.2437 16.0066C17.0796 15.8425 16.8571 15.7503 16.625 15.7503H11.375C11.1429 15.7503 10.9204 15.8425 10.7563 16.0066C10.5922 16.1707 10.5 16.3932 10.5 16.6253V22.7503H5.25001V13.1253L14 4.37528L22.75 13.1253V22.7503Z"
                        fill="#F8FAFD" />
                    </svg>
                </div>
                <span class="mini-text">{{ trans('sidebar.mini.home') }}</span>
                <span class="active-bar"></span>
            </a>

            <!-- has expanded sidebar -->
        <a class="mini-icon @if(Request::is('asset*') || Request::is('device*') || Request::is('my-items*')) active @endif"
            data-section="asset" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="{{ trans('sidebar.mini.asset') }}">
                <div class="icon-wrapper">
                    <svg width="25" height="25" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M25.375 18.375H24.5V7.875C24.5 7.17881 24.2234 6.51113 23.7312 6.01884C23.2389 5.52656 22.5712 5.25 21.875 5.25H6.125C5.42881 5.25 4.76113 5.52656 4.26884 6.01884C3.77656 6.51113 3.5 7.17881 3.5 7.875V18.375H2.625C2.39294 18.375 2.17038 18.4672 2.00628 18.6313C1.84219 18.7954 1.75 19.0179 1.75 19.25V21C1.75 21.6962 2.02656 22.3639 2.51884 22.8562C3.01113 23.3484 3.67881 23.625 4.375 23.625H23.625C24.3212 23.625 24.9889 23.3484 25.4812 22.8562C25.9734 22.3639 26.25 21.6962 26.25 21V19.25C26.25 19.0179 26.1578 18.7954 25.9937 18.6313C25.8296 18.4672 25.6071 18.375 25.375 18.375ZM5.25 7.875C5.25 7.64294 5.34219 7.42038 5.50628 7.25628C5.67038 7.09219 5.89294 7 6.125 7H21.875C22.1071 7 22.3296 7.09219 22.4937 7.25628C22.6578 7.42038 22.75 7.64294 22.75 7.875V18.375H5.25V7.875ZM24.5 21C24.5 21.2321 24.4078 21.4546 24.2437 21.6187C24.0796 21.7828 23.8571 21.875 23.625 21.875H4.375C4.14294 21.875 3.92038 21.7828 3.75628 21.6187C3.59219 21.4546 3.5 21.2321 3.5 21V20.125H24.5V21ZM16.625 9.625C16.625 9.85706 16.5328 10.0796 16.3687 10.2437C16.2046 10.4078 15.9821 10.5 15.75 10.5H12.25C12.0179 10.5 11.7954 10.4078 11.6313 10.2437C11.4672 10.0796 11.375 9.85706 11.375 9.625C11.375 9.39294 11.4672 9.17038 11.6313 9.00628C11.7954 8.84219 12.0179 8.75 12.25 8.75H15.75C15.9821 8.75 16.2046 8.84219 16.3687 9.00628C16.5328 9.17038 16.625 9.39294 16.625 9.625Z"
                        fill="white" />
                    </svg>
                </div>
                <span class="mini-text">{{ trans('sidebar.mini.asset') }}</span>
                <span class="active-bar"></span>
            </a>

            @if(config("services.service_ticket.enabled"))
                <a class="mini-icon @if(Request::is('ticket*') || Request::is('tickets*') || Request::is('change_request*')) active @endif"
                    data-section="ticket" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="{{ trans('sidebar.mini.ticket') }}">
                    <div class="icon-wrapper">
                        <svg width="25" height="25" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M25.375 11.375C25.6071 11.375 25.8296 11.2828 25.9937 11.1187C26.1578 10.9546 26.25 10.7321 26.25 10.5V7C26.25 6.53587 26.0656 6.09075 25.7374 5.76256C25.4092 5.43437 24.9641 5.25 24.5 5.25H3.5C3.03587 5.25 2.59075 5.43437 2.26256 5.76256C1.93437 6.09075 1.75 6.53587 1.75 7V10.5C1.75 10.7321 1.84219 10.9546 2.00628 11.1187C2.17038 11.2828 2.39294 11.375 2.625 11.375C3.32119 11.375 3.98887 11.6516 4.48116 12.1438C4.97344 12.6361 5.25 13.3038 5.25 14C5.25 14.6962 4.97344 15.3639 4.48116 15.8562C3.98887 16.3484 3.32119 16.625 2.625 16.625C2.39294 16.625 2.17038 16.7172 2.00628 16.8813C1.84219 17.0454 1.75 17.2679 1.75 17.5V21C1.75 21.4641 1.93437 21.9092 2.26256 22.2374C2.59075 22.5656 3.03587 22.75 3.5 22.75H24.5C24.9641 22.75 25.4092 22.5656 25.7374 22.2374C26.0656 21.9092 26.25 21.4641 26.25 21V17.5C26.25 17.2679 26.1578 17.0454 25.9937 16.8813C25.8296 16.7172 25.6071 16.625 25.375 16.625C24.6788 16.625 24.0111 16.3484 23.5188 15.8562C23.0266 15.3639 22.75 14.6962 22.75 14C22.75 13.3038 23.0266 12.6361 23.5188 12.1438C24.0111 11.6516 24.6788 11.375 25.375 11.375ZM3.5 18.2875C4.489 18.0867 5.37817 17.5501 6.01683 16.7687C6.6555 15.9873 7.00439 15.0092 7.00439 14C7.00439 12.9908 6.6555 12.0127 6.01683 11.2313C5.37817 10.4499 4.489 9.91333 3.5 9.7125V7H9.625V21H3.5V18.2875ZM24.5 18.2875V21H11.375V7H24.5V9.7125C23.511 9.91333 22.6218 10.4499 21.9832 11.2313C21.3445 12.0127 20.9956 12.9908 20.9956 14C20.9956 15.0092 21.3445 15.9873 21.9832 16.7687C22.6218 17.5501 23.511 18.0867 24.5 18.2875Z"
                            fill="white" />
                        </svg>
                    </div>
                    <span class="mini-text">{{ trans('sidebar.mini.ticket') }}</span>
                    <span class="active-bar"></span>
                </a>
            @endif

        <a class="mini-icon @if(Request::is('task-management*') || Request::is('task*') || Request::is('tasks*')) active @endif" data-section="task" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="{{ trans('task.task_management.task_management') }}">
            <div class="icon-wrapper">
                <svg width="25" height="25" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 5.25C3 4.00736 4.00736 3 5.25 3H18.75C19.9926 3 21 4.00736 21 5.25V12.0218C20.5368 11.7253 20.0335 11.4858 19.5 11.3135V5.25C19.5 4.83579 19.1642 4.5 18.75 4.5H5.25C4.83579 4.5 4.5 4.83579 4.5 5.25V18.75C4.5 19.1642 4.83579 19.5 5.25 19.5H11.3135C11.4858 20.0335 11.7253 20.5368 12.0218 21H5.25C4.00736 21 3 19.9926 3 18.75V5.25Z" fill="#fff"/>
                <path d="M10.7803 7.71967C11.0732 8.01256 11.0732 8.48744 10.7803 8.78033L8.78033 10.7803C8.48744 11.0732 8.01256 11.0732 7.71967 10.7803L6.71967 9.78033C6.42678 9.48744 6.42678 9.01256 6.71967 8.71967C7.01256 8.42678 7.48744 8.42678 7.78033 8.71967L8.25 9.18934L9.71967 7.71967C10.0126 7.42678 10.4874 7.42678 10.7803 7.71967Z" fill="#fff"/>
                <path d="M10.7803 13.2197C11.0732 13.5126 11.0732 13.9874 10.7803 14.2803L8.78033 16.2803C8.48744 16.5732 8.01256 16.5732 7.71967 16.2803L6.71967 15.2803C6.42678 14.9874 6.42678 14.5126 6.71967 14.2197C7.01256 13.9268 7.48744 13.9268 7.78033 14.2197L8.25 14.6893L9.71967 13.2197C10.0126 12.9268 10.4874 12.9268 10.7803 13.2197Z" fill="#fff"/>
                <path d="M17.5 12C20.5376 12 23 14.4624 23 17.5C23 20.5376 20.5376 23 17.5 23C14.4624 23 12 20.5376 12 17.5C12 14.4624 14.4624 12 17.5 12ZM18.0011 20.5035L18.0006 18H20.503C20.7792 18 21.003 17.7762 21.003 17.5C21.003 17.2239 20.7792 17 20.503 17H18.0005L18 14.4993C18 14.2231 17.7761 13.9993 17.5 13.9993C17.2239 13.9993 17 14.2231 17 14.4993L17.0005 17H14.4961C14.22 17 13.9961 17.2239 13.9961 17.5C13.9961 17.7762 14.22 18 14.4961 18H17.0006L17.0011 20.5035C17.0011 20.7797 17.225 21.0035 17.5011 21.0035C17.7773 21.0035 18.0011 20.7797 18.0011 20.5035Z" fill="#fff"/>
                <path d="M13.25 8.5C12.8358 8.5 12.5 8.83579 12.5 9.25C12.5 9.66421 12.8358 10 13.25 10H16.75C17.1642 10 17.5 9.66421 17.5 9.25C17.5 8.83579 17.1642 8.5 16.75 8.5H13.25Z" fill="#fff"/>
                </svg>
            </div>
            <span class="mini-text">{{ trans('task.task_management.task_management') }}</span>
            <span class="active-bar"></span>
        </a>

        @if(auth()->user()->can('UserRead') || auth()->user()->hasAnyRole(['SuperAdmin']))
        <a class="mini-icon @if(Request::is('users*') || Request::is('roles-permission*') || Request::is('edit-role/*')) active @endif"
            data-section="users" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="{{ trans('sidebar.mini.users') }}">
                <div class="icon-wrapper">
                    <svg width="25" height="25" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M12.8242 17.2713C13.9969 16.4905 14.8873 15.3531 15.3636 14.0273C15.8399 12.7014 15.8768 11.2574 15.469 9.9089C15.0612 8.56038 14.2302 7.37891 13.099 6.53919C11.9678 5.69948 10.5963 5.24609 9.18749 5.24609C7.77866 5.24609 6.40723 5.69948 5.27599 6.53919C4.14476 7.37891 3.31379 8.56038 2.90597 9.9089C2.49814 11.2574 2.53511 12.7014 3.01141 14.0273C3.48771 15.3531 4.37805 16.4905 5.55077 17.2713C3.42944 18.0531 1.61777 19.4999 0.386087 21.3958C0.321354 21.492 0.276391 21.6001 0.25381 21.7139C0.23123 21.8276 0.231482 21.9447 0.254554 22.0583C0.277625 22.172 0.323055 22.2799 0.388203 22.3758C0.453351 22.4718 0.536916 22.5538 0.634043 22.6171C0.731169 22.6805 0.839919 22.7239 0.953971 22.7448C1.06802 22.7658 1.1851 22.7639 1.2984 22.7392C1.4117 22.7145 1.51896 22.6675 1.61395 22.601C1.70894 22.5345 1.78976 22.4497 1.85171 22.3517C2.6462 21.1298 3.73335 20.1256 5.01444 19.4305C6.29553 18.7354 7.72997 18.3713 9.18749 18.3713C10.645 18.3713 12.0795 18.7354 13.3605 19.4305C14.6416 20.1256 15.7288 21.1298 16.5233 22.3517C16.6516 22.5425 16.8499 22.675 17.0752 22.7208C17.3005 22.7665 17.5347 22.7218 17.7273 22.5962C17.9198 22.4706 18.0552 22.2742 18.1042 22.0496C18.1531 21.825 18.1117 21.5901 17.9889 21.3958C16.7572 19.4999 14.9455 18.0531 12.8242 17.2713ZM4.37499 11.8113C4.37499 10.8594 4.65724 9.92899 5.18605 9.13757C5.71485 8.34616 6.46646 7.72933 7.34583 7.36509C8.2252 7.00084 9.19283 6.90554 10.1264 7.09123C11.0599 7.27692 11.9174 7.73526 12.5904 8.4083C13.2635 9.08135 13.7218 9.93885 13.9075 10.8724C14.0932 11.8059 13.9979 12.7736 13.6337 13.6529C13.2694 14.5323 12.6526 15.2839 11.8612 15.8127C11.0698 16.3415 10.1393 16.6238 9.18749 16.6238C7.91158 16.6223 6.68835 16.1148 5.78614 15.2126C4.88393 14.3104 4.37644 13.0872 4.37499 11.8113ZM27.3591 22.6066C27.1647 22.7333 26.928 22.7777 26.7009 22.7299C26.4738 22.6821 26.2751 22.5461 26.1483 22.3517C25.3547 21.129 24.2677 20.1245 22.9864 19.4296C21.705 18.7348 20.2701 18.3718 18.8125 18.3738C18.5804 18.3738 18.3579 18.2816 18.1938 18.1175C18.0297 17.9534 17.9375 17.7308 17.9375 17.4988C17.9375 17.2667 18.0297 17.0441 18.1938 16.88C18.3579 16.7159 18.5804 16.6238 18.8125 16.6238C19.5212 16.6231 20.221 16.4659 20.862 16.1634C21.5029 15.8609 22.0691 15.4206 22.5201 14.8739C22.9711 14.3273 23.2959 13.6877 23.4711 13.001C23.6463 12.3143 23.6677 11.5974 23.5337 10.9014C23.3997 10.2055 23.1136 9.54773 22.696 8.97516C22.2783 8.40259 21.7393 7.92933 21.1176 7.5892C20.4958 7.24907 19.8066 7.05047 19.0992 7.00758C18.3918 6.96469 17.6836 7.07858 17.0253 7.3411C16.918 7.3875 16.8024 7.41192 16.6855 7.4129C16.5686 7.41389 16.4526 7.39143 16.3445 7.34685C16.2364 7.30227 16.1384 7.23648 16.0561 7.15336C15.9739 7.07024 15.9091 6.97147 15.8657 6.8629C15.8223 6.75433 15.801 6.63816 15.8033 6.52125C15.8055 6.40433 15.8311 6.28905 15.8787 6.18222C15.9262 6.07538 15.9947 5.97916 16.08 5.89924C16.1654 5.81931 16.2659 5.7573 16.3756 5.71688C17.8822 5.11603 19.558 5.09441 21.0796 5.65619C22.6012 6.21796 23.8608 7.32333 24.6155 8.75906C25.3702 10.1948 25.5664 11.8591 25.1664 13.431C24.7664 15.0029 23.7984 16.371 22.4492 17.2713C24.5705 18.0531 26.3822 19.4999 27.6139 21.3958C27.7406 21.5902 27.785 21.8269 27.7372 22.054C27.6894 22.281 27.5534 22.4798 27.3591 22.6066Z"
                        fill="white" />
                    </svg>
                </div>
                <span class="mini-text">{{ trans('sidebar.mini.users') }}</span>
                <span class="active-bar"></span>
            </a>
        @endif


        <a class="mini-icon @if(Request::is('procurements*') || Request::is('procurements*') || Request::is('procurements*')) active @endif" data-section="procurements" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="{{ trans('content.procurement_fields.procurement_management') }}">
            <div class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39A2 2 0 0 0 9.64 16H19a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
            </div>
            <span class="mini-text">{{ trans('content.procurement_fields.procurement_management') }}</span>
            <span class="active-bar"></span>
        </a>


        <!-- Reports -->
        <?php
        $reportPermissionCount = (count(Auth::user()->getAllPermissions()->where('module_id', 22)) > 0) ? Auth::user()->getAllPermissions()->where('module_id', 22)->count() : 0;
        $hasTicketTaskReportPermission = Auth::user()
            ->getAllPermissions()
            ->where('module_id', 20)
            ->contains('name', 'TicketTaskReport');
        ?>
        @if($reportPermissionCount > 0 || $hasTicketTaskReportPermission == true)
            <a class="mini-icon @if(Request::is('reports*') || Request::is('reports*') || Request::is('reports*')) active @endif" data-section="reports" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="{{trans('sidebar.report_menu.reports')}}">
                <div class="icon-wrapper" >
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-medical" viewBox="0 0 16 16">
                        <path d="M7.5 5.5a.5.5 0 0 0-1 0v.634l-.549-.317a.5.5 0 1 0-.5.866L6 7l-.549.317a.5.5 0 1 0 .5.866l.549-.317V8.5a.5.5 0 1 0 1 0v-.634l.549.317a.5.5 0 1 0 .5-.866L8 7l.549-.317a.5.5 0 1 0-.5-.866l-.549.317zm-2 4.5a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1z"/>
                        <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                    </svg>
                </div>
                <span class="mini-text">{{trans('sidebar.report_menu.reports')}}</span>
                <span class="active-bar"></span>
            </a>
        @endif

        @if(config("services.change_module.enabled"))
            <a class="mini-icon @if(Request::is('change-management*') || Request::is('change-management*') || Request::is('change-management*')) active @endif" data-bs-toggle="tooltip" data-bs-original-title="{{trans('change-management.menu.change_management')}}" data-section="change-management">
                <div class="icon-wrapper" >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-building-gear" viewBox="0 0 16 16">
                        <path d="M2 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6.5a.5.5 0 0 1-1 0V1H3v14h3v-2.5a.5.5 0 0 1 .5-.5H8v4H3a1 1 0 0 1-1-1z"/>
                        <path d="M4.5 2a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-6 3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-6 3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm4.386 1.46c.18-.613 1.048-.613 1.229 0l.043.148a.64.64 0 0 0 .921.382l.136-.074c.561-.306 1.175.308.87.869l-.075.136a.64.64 0 0 0 .382.92l.149.045c.612.18.612 1.048 0 1.229l-.15.043a.64.64 0 0 0-.38.921l.074.136c.305.561-.309 1.175-.87.87l-.136-.075a.64.64 0 0 0-.92.382l-.045.149c-.18.612-1.048.612-1.229 0l-.043-.15a.64.64 0 0 0-.921-.38l-.136.074c-.561.305-1.175-.309-.87-.87l.075-.136a.64.64 0 0 0-.382-.92l-.148-.045c-.613-.18-.613-1.048 0-1.229l.148-.043a.64.64 0 0 0 .382-.921l-.074-.136c-.306-.561.308-1.175.869-.87l.136.075a.64.64 0 0 0 .92-.382zM14 12.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0"/>
                    </svg>
                </div>
                <span class="mini-text">{{trans('change-management.menu.change_management')}}</span>
                <span class="active-bar"></span>
            </a>    
        @endif

        <!-- Knowledge Management -->
        <a class="mini-icon @if(Request::is('knowledge_document*')) active @endif" data-section="knowledge-management" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="{{ trans('sidebar.mini.kd') }}">
            <div class="icon-wrapper">
                <i class="bi bi-journal-richtext"></i>
            </div>
            <span class="mini-text">{{ trans('sidebar.mini.kd') }}</span>
            <span class="active-bar"></span>
        </a>

        @if(in_array(config('app.client'), ["rolepermission", "grdemo", "greenitco"]))
            @can('EmailLogRead')
                <a href="{{ url('emails-details') }}" class="mini-icon @if(Request::is('emails-details*')) active @endif" data-section="email-log" data-bs-toggle="tooltip"
                    data-bs-placement="right" data-bs-original-title="{{ trans('sidebar.mini.email_log') }}">
                    <div class="icon-wrapper">
                        <i class="bi bi-envelope-paper"></i>
                    </div>
                    <span class="mini-text">{{ trans('sidebar.mini.email_log') }}</span>
                    <span class="active-bar"></span>
                </a>
            @endcan
        @endif
        {{-- Jobs  --}}
        @hasanyrole("SuperAdmin")
        <a class="mini-icon @if(Request::path() == 'jobs') active @endif " data-section="jobs" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Jobs">
            <div class="icon-wrapper">
                <i class="bi bi-bag-check"></i>
            </div>
            <span class="mini-text">{{ trans('sidebar.jobs') }}</span>
            <span class="active-bar"></span>
        </a>
        @endhasanyrole

        @if(in_array(config('app.client'), ['rolepermission', 'ltts']))
            <a class="mini-icon @if(Request::segment(1) === 'view-secure-pdf') active @endif " data-section="newsletter" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="newsletter">
                <div class="icon-wrapper">
                    <i class="bi bi-newspaper"></i>
                </div>
                <span class="mini-text">News Letter</span>
                <span class="active-bar"></span>
            </a>
        @endif       
        <!-- Typography V1 -->
        <a class="mini-icon @if(Request::path() == 'typographyv1') active @endif" href="{{ route('typographyv1') }}" data-section="typographyv2" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="{{ trans('sidebar.mini.typography') }} V1">
            <div class="icon-wrapper">
                <svg fill="none" width="25" height="25" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.414 15l1.63 4.505a.75.75 0 001.411-.51l-5.08-14.03a1.463 1.463 0 00-2.75 0l-5.08 14.03a.75.75 0 101.41.51L3.586 15h6.828zm-.544-1.5L7 5.572 4.13 13.5h5.74zm5.076-3.598c.913-1.683 2.703-2.205 4.284-2.205 1.047 0 2.084.312 2.878.885.801.577 1.392 1.455 1.392 2.548v8.12a.75.75 0 01-1.5 0v-.06a3.123 3.123 0 01-.044.025c-.893.52-2.096.785-3.451.785-1.051 0-2.048-.315-2.795-.948-.76-.643-1.217-1.578-1.217-2.702 0-.919.349-1.861 1.168-2.563.81-.694 2-1.087 3.569-1.087H22v-1.57c0-.503-.263-.967-.769-1.332-.513-.37-1.235-.6-2.001-.6-1.319 0-2.429.43-2.966 1.42a.75.75 0 01-1.318-.716zM22 14.2h-2.77c-1.331 0-2.134.333-2.593.726a1.82 1.82 0 00-.644 1.424c0 .689.267 1.203.686 1.557.43.365 1.065.593 1.826.593 1.183 0 2.102-.235 2.697-.581.582-.34.798-.74.798-1.134V14.2z" fill="white" />
                </svg>
            </div>
            <span class="mini-text">{{ trans('sidebar.mini.typography') }}</span>
            <span class="active-bar"></span>
        </a>

         <!-- Typography V2 -->
        <a class="mini-icon @if(Request::path() == 'typographyv2') active @endif" href="{{ route('typographyv2') }}" data-section="typographyv2" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="{{ trans('sidebar.mini.typography') }} V2">
            <div class="icon-wrapper">
                <svg fill="none" width="25" height="25" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.414 15l1.63 4.505a.75.75 0 001.411-.51l-5.08-14.03a1.463 1.463 0 00-2.75 0l-5.08 14.03a.75.75 0 101.41.51L3.586 15h6.828zm-.544-1.5L7 5.572 4.13 13.5h5.74zm5.076-3.598c.913-1.683 2.703-2.205 4.284-2.205 1.047 0 2.084.312 2.878.885.801.577 1.392 1.455 1.392 2.548v8.12a.75.75 0 01-1.5 0v-.06a3.123 3.123 0 01-.044.025c-.893.52-2.096.785-3.451.785-1.051 0-2.048-.315-2.795-.948-.76-.643-1.217-1.578-1.217-2.702 0-.919.349-1.861 1.168-2.563.81-.694 2-1.087 3.569-1.087H22v-1.57c0-.503-.263-.967-.769-1.332-.513-.37-1.235-.6-2.001-.6-1.319 0-2.429.43-2.966 1.42a.75.75 0 01-1.318-.716zM22 14.2h-2.77c-1.331 0-2.134.333-2.593.726a1.82 1.82 0 00-.644 1.424c0 .689.267 1.203.686 1.557.43.365 1.065.593 1.826.593 1.183 0 2.102-.235 2.697-.581.582-.34.798-.74.798-1.134V14.2z" fill="white" />
                </svg>
            </div>
            <span class="mini-text">{{ trans('sidebar.mini.typography') }}</span>
            <span class="active-bar"></span>
        </a>
        <!-- More -->
            <a class="mini-icon" data-section="more" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="{{ trans('sidebar.mini.more') }}">
                <div class="icon-wrapper">
                    <svg width="25" height="25" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.33341 7.0013C9.33341 7.62014 9.08758 8.21363 8.65 8.65122C8.21241 9.0888 7.61892 9.33464 7.00008 9.33464C6.38124 9.33464 5.78775 9.0888 5.35017 8.65122C4.91258 8.21363 4.66675 7.62014 4.66675 7.0013C4.66675 6.38246 4.91258 5.78897 5.35017 5.35139C5.78775 4.9138 6.38124 4.66797 7.00008 4.66797C7.61892 4.66797 8.21241 4.9138 8.65 5.35139C9.08758 5.78897 9.33341 6.38246 9.33341 7.0013ZM9.33341 14.0013C9.33341 14.6201 9.08758 15.2136 8.65 15.6512C8.21241 16.0888 7.61892 16.3346 7.00008 16.3346C6.38124 16.3346 5.78775 16.0888 5.35017 15.6512C4.91258 15.2136 4.66675 14.6201 4.66675 14.0013C4.66675 13.3825 4.91258 12.789 5.35017 12.3514C5.78775 11.9138 6.38124 11.668 7.00008 11.668C7.61892 11.668 8.21241 11.9138 8.65 12.3514C9.08758 12.789 9.33341 13.3825 9.33341 14.0013ZM7.00008 23.3346C7.61892 23.3346 8.21241 23.0888 8.65 22.6512C9.08758 22.2136 9.33341 21.6201 9.33341 21.0013C9.33341 20.3825 9.08758 19.789 8.65 19.3514C8.21241 18.9138 7.61892 18.668 7.00008 18.668C6.38124 18.668 5.78775 18.9138 5.35017 19.3514C4.91258 19.789 4.66675 20.3825 4.66675 21.0013C4.66675 21.6201 4.91258 22.2136 5.35017 22.6512C5.78775 23.0888 6.38124 23.3346 7.00008 23.3346ZM16.3334 7.0013C16.3334 7.62014 16.0876 8.21363 15.65 8.65122C15.2124 9.0888 14.6189 9.33464 14.0001 9.33464C13.3812 9.33464 12.7878 9.0888 12.3502 8.65122C11.9126 8.21363 11.6667 7.62014 11.6667 7.0013C11.6667 6.38246 11.9126 5.78897 12.3502 5.35139C12.7878 4.9138 13.3812 4.66797 14.0001 4.66797C14.6189 4.66797 15.2124 4.9138 15.65 5.35139C16.0876 5.78897 16.3334 6.38246 16.3334 7.0013ZM14.0001 16.3346C14.6189 16.3346 15.2124 16.0888 15.65 15.6512C16.0876 15.2136 16.3334 14.6201 16.3334 14.0013C16.3334 13.3825 16.0876 12.789 15.65 12.3514C15.2124 11.9138 14.6189 11.668 14.0001 11.668C13.3812 11.668 12.7878 11.9138 12.3502 12.3514C11.9126 12.789 11.6667 13.3825 11.6667 14.0013C11.6667 14.6201 11.9126 15.2136 12.3502 15.6512C12.7878 16.0888 13.3812 16.3346 14.0001 16.3346ZM16.3334 21.0013C16.3334 21.6201 16.0876 22.2136 15.65 22.6512C15.2124 23.0888 14.6189 23.3346 14.0001 23.3346C13.3812 23.3346 12.7878 23.0888 12.3502 22.6512C11.9126 22.2136 11.6667 21.6201 11.6667 21.0013C11.6667 20.3825 11.9126 19.789 12.3502 19.3514C12.7878 18.9138 13.3812 18.668 14.0001 18.668C14.6189 18.668 15.2124 18.9138 15.65 19.3514C16.0876 19.789 16.3334 20.3825 16.3334 21.0013ZM21.0001 9.33464C21.6189 9.33464 22.2124 9.0888 22.65 8.65122C23.0876 8.21363 23.3334 7.62014 23.3334 7.0013C23.3334 6.38246 23.0876 5.78897 22.65 5.35139C22.2124 4.9138 21.6189 4.66797 21.0001 4.66797C20.3812 4.66797 19.7878 4.9138 19.3502 5.35139C18.9126 5.78897 18.6667 6.38246 18.6667 7.0013C18.6667 7.62014 18.9126 8.21363 19.3502 8.65122C19.7878 9.0888 20.3812 9.33464 21.0001 9.33464ZM23.3334 14.0013C23.3334 14.6201 23.0876 15.2136 22.65 15.6512C22.2124 16.0888 21.6189 16.3346 21.0001 16.3346C20.3812 16.3346 19.7878 16.0888 19.3502 15.6512C18.9126 15.2136 18.6667 14.6201 18.6667 14.0013C18.6667 13.3825 18.9126 12.789 19.3502 12.3514C19.7878 11.9138 20.3812 11.668 21.0001 11.668C21.6189 11.668 22.2124 11.9138 22.65 12.3514C23.0876 12.789 23.3334 13.3825 23.3334 14.0013ZM21.0001 23.3346C21.6189 23.3346 22.2124 23.0888 22.65 22.6512C23.0876 22.2136 23.3334 21.6201 23.3334 21.0013C23.3334 20.3825 23.0876 19.789 22.65 19.3514C22.2124 18.9138 21.6189 18.668 21.0001 18.668C20.3812 18.668 19.7878 18.9138 19.3502 19.3514C18.9126 19.789 18.6667 20.3825 18.6667 21.0013C18.6667 21.6201 18.9126 22.2136 19.3502 22.6512C19.7878 23.0888 20.3812 23.3346 21.0001 23.3346Z" fill="white" />
                    </svg>
                </div>
                <span class="mini-text">{{ trans('sidebar.mini.more') }}</span>
                <span class="active-bar"></span>
            </a>
        </div>

        <div class="sidebar-extender" style="opacity:0; pointer-events:none;">
            <svg class="extender-icon" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M7.31754 7.31754L1.06753 13.5675C0.95026 13.6848 0.7912 13.7507 0.625347 13.7507C0.459495 13.7507 0.300435 13.6848 0.18316 13.5675C0.0658846 13.4503 3.26935e-09 13.2912 0 13.1253C-3.26935e-09 12.9595 0.0658846 12.8004 0.18316 12.6832L5.99175 6.87535L0.18316 1.06753C0.0658846 0.95026 0 0.7912 0 0.625347C0 0.459495 0.0658846 0.300435 0.18316 0.18316C0.300435 0.0658846 0.459495 0 0.625347 0C0.7912 0 0.95026 0.0658846 1.06753 0.18316L7.31754 6.43316C7.37565 6.49121 7.42175 6.56014 7.4532 6.63601C7.48465 6.71188 7.50084 6.79321 7.50084 6.87535C7.50084 6.95748 7.48465 7.03881 7.4532 7.11469C7.42175 7.19056 7.37565 7.25949 7.31754 7.31754ZM13.5675 6.43316L7.31754 0.18316C7.20026 0.0658846 7.0412 0 6.87535 0C6.7095 0 6.55044 0.0658846 6.43316 0.18316C6.31588 0.300435 6.25 0.459495 6.25 0.625347C6.25 0.7912 6.31588 0.95026 6.43316 1.06753L12.2418 6.87535L6.43316 12.6832C6.31588 12.8004 6.25 12.9595 6.25 13.1253C6.25 13.2912 6.31588 13.4503 6.43316 13.5675C6.55044 13.6848 6.7095 13.7507 6.87535 13.7507C7.0412 13.7507 7.20026 13.6848 7.31754 13.5675L13.5675 7.31754C13.6256 7.25949 13.6717 7.19056 13.7032 7.11469C13.7347 7.03881 13.7508 6.95748 13.7508 6.87535C13.7508 6.79321 13.7347 6.71188 13.7032 6.63601C13.6717 6.56014 13.6256 6.49121 13.5675 6.43316Z" fill="currentColor"/>
            </svg>
        </div>
        <div class="scroll-indicator scroll-down hidden">▼</div>
    </aside>

    <!-- EXPANDED SIDEBARS (DESKTOP) -->
    <div id="expanded-asset" class="expanded-sidebar desktop-only @if(!(Request::is('asset*') || Request::is('device*') || Request::is('my-items*'))) hidden @endif app-sidebar">
        <div class="expanded-sidebar-header">{{ trans('sidebar.headers.asset') }}</div>
        <ul class="expanded-menu">
            <li @if(Request::is('asset*') || Request::is('device*') || Request::is('my-items*')) class="active-link" @endif><a href="#">
                    <i class="bi bi-grid"></i>
                    <span>{{ trans('sidebar.asset_menu.dashboard') }}</span>
                </a>
            </li>
            <li>
                <a href="{{ url('my-items') }}">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_9521_7587)">
                        <path d="M13.1611 1H6.83631C3.61192 1 0.998047 3.61388 0.998047 6.83826V13.163C0.998047 16.3874 3.61192 19.0013 6.83631 19.0013H13.1611C16.3855 19.0013 18.9993 16.3874 18.9993 13.163V6.83826C18.9993 3.61388 16.3855 1 13.1611 1Z" stroke="black" stroke-width="1.5"/>
                        <path d="M5.78711 14.107V9.14546M10.1181 14.107V5.89062M14.2098 14.107V7.87758" stroke="black" stroke-width="1.6" stroke-linecap="round"/>
                        </g>
                        <defs>
                        <clipPath id="clip0_9521_7587">
                        <rect width="20" height="20" fill="white"/>
                        </clipPath>
                        </defs>
                    </svg>
                   <span>{{ trans('sidebar.asset_menu.my_items') }}</span>
                </a>
            </li>
            <li @if(Request::is('device_list*')) class="active-link" @endif>
                <a href="{{ route('device_list') }}">
                    <i class="bi bi-box-seam"></i>
                    <span>Devices</span>
                </a>
            </li>
            <li @if(Request::is('device_details*')) class="active-link" @endif>
                <a href="{{ route('device_details') }}">
                    <i class="bi bi-ticket-perforated"></i>
                    <span>Device Details</span>
                </a>
            </li>
            <li>
                <a href="{{ url('licenses') }}">
                    <i class="bi bi-ticket-perforated"></i>
                    <span>{{ trans('sidebar.asset_menu.licenses') }}</span>
                </a>
            </li>
            <li class="has-submenu">
                <a href="#">
                    <i class="bi bi-journal-check"></i>
                    <span>{{ trans('sidebar.asset_menu.my_approvals') }}</span>
                </a>

                <ul class="submenu">
                    <li><a href="#">Sub Menu 1</a></li>
                    <li><a href="#">Sub Menu 2</a></li>
                    <li><a href="#">Sub Menu 3</a></li>
                </ul>
            </li>
            <li class="has-submenu">
                <a href="#">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6.66602 5.83333H3.33268C2.89065 5.83333 2.46673 6.00893 2.15417 6.32149C1.84161 6.63405 1.66602 7.05797 1.66602 7.5V15.8333C1.66602 16.2754 1.84161 16.6993 2.15417 17.0118C2.46673 17.3244 2.89065 17.5 3.33268 17.5H16.666C17.108 17.5 17.532 17.3244 17.8445 17.0118C18.1571 16.6993 18.3327 16.2754 18.3327 15.8333V7.5C18.3327 7.05797 18.1571 6.63405 17.8445 6.32149C17.532 6.00893 17.108 5.83333 16.666 5.83333H13.3327M6.66602 5.83333V3C6.66602 2.86739 6.71869 2.74021 6.81246 2.64645C6.90623 2.55268 7.03341 2.5 7.16602 2.5H12.8327C12.9653 2.5 13.0925 2.55268 13.1862 2.64645C13.28 2.74021 13.3327 2.86739 13.3327 3V5.83333M6.66602 5.83333H13.3327" stroke="black" stroke-width="1.5"/>
                    </svg>
                    {{ trans('sidebar.asset_menu.device') }}
                </a>
                <ul class="submenu">
                    <li @if(Request::is('devices*')) class="active-link" @endif><a href="{{ url('devices') }}">{{ trans('sidebar.asset_menu.device_details') }}</a></li>
                    @hasanyrole("SuperAdmin")
                        <li @if(Request::is('blockIp*')) class="active-link" @endif><a href="{{ url('blockIp') }}">{{ trans('sidebar.asset_menu.blocked_ip') }}</a></li>
                    @endhasanyrole
                    @can("DeviceConfiguration")
                        <li><a href="#">{{ trans('sidebar.asset_menu.configuration') }}</a></li>
                    @endcan
                </ul>
            </li>
            @can("AccessoriesRead")
            <li @if(Request::is('accessories*')) class="active-link" @endif>
                <a href="{{url('accessories')}}">
                    <svg width="15" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.2 14.2222V4C3.2 2.89543 4.09543 2 5.2 2H18.8C19.9046 2 20.8 2.89543 20.8 4V14.2222M3.2 14.2222H20.8M3.2 14.2222L1.71969 19.4556C1.35863 20.7321 2.31762 22 3.64418 22H20.3558C21.6824 22 22.6414 20.7321 22.2803 19.4556L20.8 14.2222" stroke="#131927" stroke-width="1.5" />
                        <path d="M11.6667 5L10 8H14L12.3333 11" stroke="#131927" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M11 19L13 19" stroke="#131927" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Accessories
                </a>
            </li>
            @endcan
            
            <li @if(Request::is('components*')) class="active-link" @endif>
                <a href="{{ url('components') }}">
                    <svg width="15" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.2 14.2222V4C3.2 2.89543 4.09543 2 5.2 2H18.8C19.9046 2 20.8 2.89543 20.8 4V14.2222M3.2 14.2222H20.8M3.2 14.2222L1.71969 19.4556C1.35863 20.7321 2.31762 22 3.64418 22H20.3558C21.6824 22 22.6414 20.7321 22.2803 19.4556L20.8 14.2222" stroke="#131927" stroke-width="1.5" />
                        <path d="M11.6667 5L10 8H14L12.3333 11" stroke="#131927" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M11 19L13 19" stroke="#131927" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ trans('sidebar.mini.components') }}
                </a>
            </li>
            
            @can("ConsumableRead")
            <li @if(Request::is('consumables*')) class="active-link" @endif>
                <a href="{{ url('consumables') }}">
                    <svg width="15" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.2 14.2222V4C3.2 2.89543 4.09543 2 5.2 2H18.8C19.9046 2 20.8 2.89543 20.8 4V14.2222M3.2 14.2222H20.8M3.2 14.2222L1.71969 19.4556C1.35863 20.7321 2.31762 22 3.64418 22H20.3558C21.6824 22 22.6414 20.7321 22.2803 19.4556L20.8 14.2222" stroke="#131927" stroke-width="1.5" />
                        <path d="M11.6667 5L10 8H14L12.3333 11" stroke="#131927" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M11 19L13 19" stroke="#131927" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ trans('sidebar.mini.consumables') }}
                </a>
            </li>
            @endcan
            @can("NIRead")
                <li class="has-submenu">
                    <a href="#">
                        <svg width="15" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3.2 14.2222V4C3.2 2.89543 4.09543 2 5.2 2H18.8C19.9046 2 20.8 2.89543 20.8 4V14.2222M3.2 14.2222H20.8M3.2 14.2222L1.71969 19.4556C1.35863 20.7321 2.31762 22 3.64418 22H20.3558C21.6824 22 22.6414 20.7321 22.2803 19.4556L20.8 14.2222" stroke="#131927" stroke-width="1.5" />
                            <path d="M11.6667 5L10 8H14L12.3333 11" stroke="#131927" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M11 19L13 19" stroke="#131927" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>{{ trans('sidebar.asset_menu.network_inventory') }}</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="{{ url('devices/network_inventory/list') }}"><span>{{ trans('sidebar.ni_menu.ni_devices') }}</span></a></li>
                    </ul>
                </li>
            @endcan
        </ul>
    </div>

    <!-- Ticket -->
    @if(config("services.service_ticket.enabled"))
        <div id="expanded-ticket" class="expanded-sidebar desktop-only @if(!(Request::is('ticket*') || Request::is('tickets*') || Request::is('change_request*'))) hidden @endif app-sidebar">
            <div class="expanded-sidebar-header">{{ trans('sidebar.headers.ticket') }}</div>
            <div class="sidebar-search">
                <input type="text" class="sidebar-search-input" placeholder="Search menu..." data-target="expanded-ticket">
            </div>
            <ul class="expanded-menu">
                <!-- <li @if(Request::is('ticket*') || Request::is('tickets*') || Request::is('change_request*')) class="active-link" @endif><a href="{{ url('ticket_dashboard') }}"><i class="bi bi-grid"></i><span> {{ trans('sidebar.ticket_menu.dashboard') }}</span></a></li> -->
                <li class="has-submenu">
                    <a href="#">
                        <i class="bi bi-ticket-perforated"></i>
                        <span>{{ trans('ticket.ticket_menu.service_ticket') }}</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="{{ route('dashboard') }}"><span>{{ trans('sidebar.mini.home') }}</span></a></li>
                        <li @if(Request::is('tickets/newlist/my-tickets')) class="active-link" @endif><a href="{{ url('tickets/newlist/my-tickets') }}"><span>{{ trans('ticket.ticket_menu.my_tickets') }}</span></a></li>
                        @if(Auth::user()->hasPermission("service_tickets"))
                            <li @if(Request::is('tickets/newlist/all-tickets')) class="active-link" @endif><a href="{{ url('tickets/newlist/all-tickets') }}"><span>{{ trans('ticket.ticket_menu.all_tickets') }}</span></a></li>
                            <li @if(Request::is('tickets/newlist/assigned')) class="active-link" @endif><a href="{{ url('tickets/newlist/assigned') }}"><span>{{ trans('ticket.ticket_menu.assigned_tickets') }}</span></a></li>
                            <li @if(Request::is('tickets/newlist/not-assigned')) class="active-link" @endif><a href="{{ url('tickets/newlist/not-assigned') }}"><span>{{ trans('ticket.ticket_menu.not_assigned_tickets') }}</span></a></li>
                            <li @if(Request::is('tickets/newlist/closed')) class="active-link" @endif><a href="{{ url('tickets/newlist/closed') }}"><span>{{ trans('ticket.ticket_menu.closed_tickets') }}</span></a></li>
                            <li @if(Request::is('tickets/newlist/spam')) class="active-link" @endif><a href="{{ url('tickets/newlist/spam') }}"><span>{{ trans('ticket.ticket_menu.spam_tickets') }}</span></a></li>
                        @endif
                    </ul>
                </li>

                <li class="has-submenu">
                    <a href="#">
                        <i class="bi bi-ticket-perforated"></i>
                        <span>{{ trans('sidebar.ticket_menu.service_requests') }}</span>
                    </a>
                    <ul class="submenu">
                        <li @if(Request::is('tickets/requestList/myapproval')) class="active-link" @endif><a href="{{ url('tickets/requestList/myapproval') }}"><span>{{ trans('sidebar.ticket_menu.my_approvals') }}</span></a></li>
                        @if(in_array(config("app.client"), ["ltts"]) && in_array(config("app.sub_client"), ["live", "dev"]))
                            <li @if(Request::is('tickets/requestList/pendingrequests')) class="active-link" @endif><a href="{{ url('tickets/requestList/pendingrequests') }}"><span>{{ trans("sidebar.ticket_menu.my_pending_approvals") }}</span></a></li>
                        @endif
                        @if (Auth::user()->hasPermission("service_tickets"))
                            <li @if(Request::is('tickets/requestList/all')) class="active-link" @endif><a href="{{ url('tickets/requestList/all') }}"><span>{{ trans('sidebar.ticket_menu.all_service_requests') }}</span></a></li>
                        @endif
                        <li @if(Request::is('tickets/requestList/myRequest')) class="active-link" @endif><a href="{{ url('tickets/requestList/myRequest') }}"><span>{{ trans('sidebar.ticket_menu.my_service_requests') }}</span></a></li>
                        @if (Auth::user()->hasPermission("service_tickets"))
                            <li @if(Request::is('tickets/newlist/assigned-request-tickets')) class="active-link" @endif><a href="{{ url('tickets/newlist/assigned-request-tickets') }}"><span>{{ trans('sidebar.ticket_menu.my_assigned_requests') }}</span></a></li>
                        @endif
                        <li @if(Request::is('tickets/archived-requests')) class="active-link" @endif><a href="{{ url('tickets/archived-requests') }}"><span>{{ trans('sidebar.ticket_menu.archive_requests') }}</span></a></li>
                        @can("DynamicFormRead")
                            <li @if(Request::is('dynamic_form')) class="active-link" @endif><a href="{{ url('dynamic_form') }}"><span>{{ trans('content.dynamic_form.dynamic_form') }}</span></a></li>
                        @endcan
                        @hasanyrole("SuperAdmin|Admin")
                            <li @if(Request::is('tickets/srat/list')) class="active-link" @endif><a href="{{ url('tickets/srat/list') }}"><span>{{ trans("sidebar.ticket_menu.ticket_srat") }}</span></a></li>
                        @endhasanyrole
                    </ul>   
                </li>
                @hasanyrole("SuperAdmin")
                    @can("TicketConfiguration")
                        <li @if(Request::is('tickets/config*')) class="active-link" @endif><a href="{{ url('tickets/config') }}"><i class="bi bi-gear"></i> <span>{{ trans("ticket.ticket_configuration.Ticket_Configuration") }}</span></a></li>
                    @endcan
                @endhasanyrole
                @if(in_array(config('app.client'), ["rolepermission", "grdemo", "greenitco"]))
                    @can('Kanban')
                        <li class="has-submenu">
                            <a href="#">
                            <i class="bi bi-kanban"></i>
                                <span>{{ trans('sidebar.kanban_menu.kanban_board') }}</span>
                            </a>
                            <ul class="submenu">
                                @can('KanbanAllBoards')
                                    <li @if(Request::path() == 'tickets/kanban-board') class="active-link" @endif><a href="{{ url('tickets/kanban-board') }}"><span>{{ trans("kanban.all_kanban_board.all_kanban_board") }}</span></a></li>
                                @endcan
                                @can('KanbanMyBoards')
                                    <li @if(Request::path() == 'tickets/kanban-board/boards') class="active-link" @endif><a href="{{ url('tickets/kanban-board/boards') }}">My Board</a></li>
                                @endcan
                                @can('KanbanConfiguration')
                                    <li @if(Request::path() == 'tickets/kanban-board/config') class="active-link" @endif><a href="{{ url('tickets/kanban-board/config') }}"><span>{{ trans('sidebar.kanban_menu.kanban_board_config') }}</span></a></li>
                                @endcan
                            </ul>   
                        </li>
                    @endcan
                @endif
                <li @if(Request::is('tickets/archive-list*')) class="active-link" @endif><a href="{{ url('tickets/archive-list') }}"><i class="bi bi-archive-fill"></i> <span>{{ trans('content.archived.archived_ticket') }}</span></a></li>
        </span></span></a></li>
                @can("ProblemCategoriesRead")
                    <li @if(Request::is('tickets/problem-categories*')) class="active-link" @endif ><a href="{{ url('tickets/problem-categories') }}"><i class="bi bi-gear"></i><span>{{ trans('sidebar.ticket_menu.problem_category') }}</span></a></li>
                @endcan
                @hasanyrole("SuperAdmin")
                    <li class="has-submenu">
                        <a href="#">
                        <i class="bi bi-list-check"></i>
                            <span>{{ trans('sidebar.ticket_menu.all_status') }}</span>
                        </a>
                        <ul class="submenu">
                            <li @if(Request::is('ticket-status*')) class="active-link" @endif><a href="{{ url('ticket-status') }}"><span>{{ trans('sidebar.ticket_menu.ticket_status') }}</span></a></li>
                            <li @if(Request::is('status-approval*')) class="active-link" @endif><a href="{{ url('status-approval') }}">{{ trans('sidebar.ticket_menu.status_approval_config') }}</a></li>
                        </ul>
                    </li>
                @endhasanyrole
                @if(auth()->user()->hasRole('SuperAdmin') || auth()->user()->statusApprovalCount() > 0)
                    <li class="has-submenu">
                        <a href="#">
                            <i class="bi bi-journal-check"></i>
                            <span>{{ trans('sidebar.ticket_menu.status_approval') }}</span>
                        </a>
                        <ul class="submenu">
                            @hasanyrole("SuperAdmin")
                            <li @if(Request::is('status-change-approvals/all*')) class="active-link" @endif>
                                <a href="{{ url('status-change-approvals/all') }}">
                                    {{ trans('sidebar.ticket_menu.all_status_approval') }}
                                </a>
                            </li>
                            @endhasanyrole
                            @if(auth()->user()->statusApprovalCount() > 0)
                                <li @if(Request::is('status-change-approvals/my_approvals*')) class="active-link" @endif>
                                    <a href="{{ url('status-change-approvals/my_approvals') }}">
                                        {{ trans('sidebar.ticket_menu.my_status_approval') }}
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                @hasanyrole("SuperAdmin")
                    <li class="has-submenu">
                        <a href="#">
                        <i class="bi bi-universal-access-circle"></i>
                            <span>{{ trans('sidebar.ticket_menu.auto_allocation') }}</span>
                        </a>
                        <ul class="submenu">
                            @can("TicketAllocationGroupRead")
                                <li @if(Request::is('auto-allocation-groups*')) class="active-link" @endif><a href="{{ route('auto-allocation-groups') }}">{{ trans('sidebar.ticket_menu.auto_allocation') }}</a></li>
                            @endcan
                            @can("TicketAllocationGroupMemberImport")
                                <li @if(Request::is('auto-allocation-member-import*')) class="active-link" @endif><a href="{{ route('auto-allocation-member-import') }}">{{ trans('auto-allocation-group.member_import.title') }}</a></li>
                            @endcan
                            @can("TechnicianLiveStatusRead")
                                <li @if(Request::is('technician/live-status*')) class="active-link" @endif><a href="{{ url('technician/live-status') }}">{{ trans('technician_status.tech_live_status') }}</a></li>
                            @endcan
                        </ul>
                    </li>
                    <li @if(Request::is('tickets/user-groups*')) class="active-link" @endif><a href="{{ url('tickets/user-groups') }}"><i class="bi bi-people"></i><span>{{ trans('sidebar.ticket_menu.cc_mail_group') }}</span></a></li>
                    <li @if(Request::is('tickets/escalation-groups*')) class="active-link" @endif><a href="{{ url('tickets/escalation-groups') }}"><i class="bi bi-alarm"></i><span>{{ trans('sidebar.ticket_menu.escalation_group') }}</span></a></li>

                    <li @if(Request::is('ticket-types*')) class="active-link" @endif><a href="{{ url('ticket-types') }}"><i class="bi bi-arrows-move"></i><span>{{ trans('sidebar.ticket_menu.ticket_types') }}</span></a></li>
                @endhasanyrole
                @can("TicketTriggerRead")
                <li @if(Request::is('tickets/trigger*')) class="active-link" @endif><a href="{{ url('tickets/trigger') }}"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-ticket-gear" viewBox="0 0 16 16"><path d="M1.5 3A1.5 1.5 0 0 0 0 4.5V6a.5.5 0 0 0 .5.5 1.5 1.5 0 1 1 0 3 .5.5 0 0 0-.5.5v1.5A1.5 1.5 0 0 0 1.5 13h13a1.5 1.5 0 0 0 1.5-1.5V10a.5.5 0 0 0-.5-.5 1.5 1.5 0 0 1 0-3A.5.5 0 0 0 16 6V4.5A1.5 1.5 0 0 0 14.5 3zM1 4.5a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 .5.5v1.05a2.5 2.5 0 0 0 0 4.9v1.05a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-1.05a2.5 2.5 0 0 0 0-4.9z"/><path d="M4 4.85v.9h1v-.9zm7 0v.9h1v-.9zm-7 1.8v.9h1v-.9zm7 0v.9h1v-.9zm-7 1.8v.9h1v-.9zm7 0v.9h1v-.9zm-7 1.8v.9h1v-.9zm7 0v.9h1v-.9z"/> <line x1="8" y1="3.2" x2="8" y2="12.8" stroke="currentColor" stroke-width="0.5" stroke-dasharray="1.2 1.2"/><g transform="translate(11, 8)"><circle cx="0" cy="0" r="1.8" fill="none" stroke="currentColor" stroke-width="0.8"/><path d="M0 -3 L0 -2.2 M0 2.2 L0 3 M-3 0 L-2.2 0 M2.2 0 L3 0" stroke="currentColor" stroke-width="0.7" stroke-linecap="round"/><path d="M-2.1 -2.1 L-1.6 -1.6 M1.6 1.6 L2.1 2.1 M-2.1 2.1 L-1.6 1.6 M1.6 -1.6 L2.1 -2.1" stroke="currentColor" stroke-width="0.6" stroke-linecap="round"/><circle cx="0" cy="0" r="1" fill="none" stroke="currentColor" stroke-width="0.6"/> </g></svg>{{trans("content.ticket_trigger.ticket_trigger")}}</a></li>
                @endcan
                @can("TicketIncidentRead")
                    <li @if(Request::is('tickets/incidentList*')) class="active-link" @endif><a href="{{ url('tickets/incidentList') }}"><i class="bi bi-exclamation-triangle"></i><span>{{trans("content.ticket_incident.ticket_incident")}}</span></a></li>
                @endcan
                @can("SchedularRead")
                <li @if(Request::is('tickets/schedularList*')) class="active-link" @endif><a href="{{ url('tickets/schedularList') }}"><i class="bi bi-calendar2-plus"></i><span>{{ trans('sidebar.ticket_menu.ticket_scheduler') }}</span></a></li>
                @endcan
                @can("AllProblemLists")
                    <li @if(Request::is('problem_manager/AllProblemLists*')) class="active-link" @endif><a href="{{ url('problem_manager/AllProblemLists') }}"><i class="bi bi-exclamation-triangle"></i>{{trans("content.service_ticket_fields.Problem_Management")}}</a></li>
                @endcan
                @hasanyrole("SuperAdmin")
                    <li @if(Request::is('tickets/out-going-mail*')) class="active-link" @endif><a href="{{ url('tickets/out-going-mail') }}"><i class="bi bi-envelope-arrow-up"></i><span>{{ trans('sidebar.ticket_menu.outgoing_mail') }}</span></a></li>
                @endhasanyrole
                <li @if(Request::is('technician-leaderboard*')) class="active-link" @endif><a href="{{ url('technician-leaderboard') }}"><i class="bi bi-person-workspace"></i><span>{{ trans("ticket.leaderboard.technician_leaderboard") }}</span></a></li>
            </ul>
        </div>
    @endif 

    @if(config("services.change_module.enabled"))
     @can("ChangeRequestRead")
        <div id="expanded-change-management" class="expanded-sidebar desktop-only @if(!(Request::is('change-management*') || Request::is('change-management*') || Request::is('change-management*'))) hidden @endif app-sidebar">
            <div class="expanded-sidebar-header">{{trans('change-management.menu.change_management')}}</div>
            <ul class="expanded-menu">

                @can("ChangeRequestDashboard")
                    <li @if(Request::path() == 'change-management/dashboard') class="active-link" @endif>
                        <a href="{{ url('change-management/dashboard') }}">
                            <i class="bi bi-speedometer2"></i>
                            <span>{{ trans("change-management.menu.dashboard") }}</span>
                        </a>
                    </li>
                @endcan

                @if(count(Auth::user()->approvalChangeRequestCount()) != 0)
                    <li class="{{ request()->is('change-management/myRequestList', 'change-management/myRequestList/myPendingApproval') ? 'active-link' : '' }}">
                        <a href="{{ url('change-management/myRequestList') }}">
                            <i class="bi bi-check2-square"></i>
                            <span>{{ trans("change-management.menu.my_approvals") }}</span>
                        </a>
                    </li>
                @endif

                @if (!Auth::user()->hasAnyRole(['User']))
                    <li @if(Request::path() == 'change-management/myRequestList/all') class="active-link" @endif>
                        <a href="{{ url('change-management/myRequestList/all') }}">
                            <i class="bi bi-list-task"></i>
                            <span>{{ trans("change-management.menu.change_request") }}</span>
                        </a>
                    </li>
                @endif

                <li @if(Request::is('change-details*')) class="active-link" @endif>
                    <a href="{{ route('change-details') }}">
                        <i class="bi bi-person-lines-fill"></i>
                        <span>Details</span>
                    </a>
                </li>
                <li @if(Request::path() == 'change-management/myRequestList/myRequest') class="active-link" @endif>
                    <a href="{{ url('change-management/myRequestList/myRequest') }}">
                        <i class="bi bi-person-lines-fill"></i>
                        <span>{{ trans("change-management.menu.my_change_request") }}</span>
                    </a>
                </li>

                @php
                    $counts = Auth::user()->changeRequestCount();
                @endphp

                @if($counts['implementer'] != 0)
                    <li @if(Request::path() == 'change-management/myRequestList/implementer') class="active-link" @endif>
                        <a href="{{ url('change-management/myRequestList/implementer') }}">
                            <i class="bi bi-tools"></i>
                            <span>{{ trans("change-management.menu.change_implemeter") }}</span>
                        </a>
                    </li>
                @endif

                @if($counts['reviewer'] != 0)
                    <li @if(Request::path() == 'change-management/myRequestList/reviewer') class="active-link" @endif>
                        <a href="{{ url('change-management/myRequestList/reviewer') }}">
                            <i class="bi bi-eye"></i>
                            <span>{{ trans("change-management.menu.change_reviewer") }}</span>
                        </a>
                    </li>
                @endif

                @if($counts['manager'] != 0)
                    <li @if(Request::path() == 'change-management/myRequestList/manager') class="active-link" @endif>
                        <a href="{{ url('change-management/myRequestList/manager') }}">
                            <i class="bi bi-person-workspace"></i>
                            <span>{{ trans("change-management.menu.change_manager") }}</span>
                        </a>
                    </li>
                @endif

                @if(Config('app.client') == "ltts")
                    <li @if(Request::path() == 'change-management/myRequestList/myPendingReview') class="active-link" @endif>
                        <a href="{{ url('change-management/myRequestList/myPendingReview') }}">
                            <i class="bi bi-journal-check"></i>
                            <span>{{ trans("content.change_management_fields.My_Review") }}</span>
                        </a>
                    </li>
                @endif

                @if (!Auth::user()->hasAnyRole(['User']))

                    @can("CABRead")
                        <li @if(Request::path() == 'change-management/cab/list') class="active-link" @endif>
                            <a href="{{ url('change-management/cab/list') }}">
                                <i class="bi bi-people"></i>
                                <span>{{ trans("change-management.menu.cab") }}</span>
                            </a>
                        </li>
                    @endcan

                    @can("ChangeCategoryRead")
                        <li @if(Request::path() == 'change-management/categories') class="active-link" @endif>
                            <a href="{{ url('change-management/categories') }}">
                                <i class="bi bi-tags"></i>
                                <span>{{ trans("change-management.menu.categories") }}</span>
                            </a>
                        </li>
                    @endcan

                @endif

            </ul>
        </div>
     @endcan
    @endif

    @if(config("services.task_module.enabled"))
        <div id="expanded-task" class="expanded-sidebar desktop-only @if(!(Request::is('tasks*') || Request::is('task-management*') || Request::is('task*'))) hidden @endif app-sidebar">
            <div class="expanded-sidebar-header">{{ trans('task.task_management.task_management') }}</div>
            <ul class="expanded-menu">
                @can("TaskRead")
                    <li @if(Request::is('task-management/list/all*')) class="active-link" @endif><a href="{{ url('task-management/list/all') }}"><i class="bi bi-list-task"></i><span>{{ trans('task.task_management.all_tasks') }}</span></a></li>
                @endcan
                @can("TaskRead")
                    <li @if(Request::is('task-management/list/my_task*')) class="active-link" @endif><a href="{{ url('task-management/list/my_task') }}"> <i class="bi bi-person-gear"></i><span>{{ trans('task.task_management.my_tasks') }}</span></a></li>
                @endcan
                <li @if(Request::is('task-management/list/assigned*')) class="active-link" @endif><a href="{{ url('task-management/list/assigned') }}"><i class="bi bi-person-check"></i>  <span>{{ trans('task.task_management.assigned_tasks') }}</span></a></li>
                @can("TaskRead")
                    <li @if(Request::is('task-management/list/archived*')) class="active-link" @endif><a href="{{ url('task-management/list/archived') }}"><i class="bi bi-archive"></i> <span>{{ trans('task.task_management.archived_tasks') }}</span></a></li>
                @endcan     
            </ul>
        </div>
    @endif

    <div id="expanded-procurements" class="expanded-sidebar desktop-only @if(!(Request::is('procurement*') || Request::is('procurements*') || Request::is('procurement*'))) hidden @endif app-sidebar">
        <div class="expanded-sidebar-header">{{ trans('content.procurement_fields.procurement_management') }}</div>
        <ul class="expanded-menu">
            <li><a href="{{ url('custom_tax') }}"><i class="bi bi-cash-stack"></i><span>Custom Tax</span></a></li>
            <li><a href="{{ url('procurements/custom_charge_elements/list') }}"><i class="bi bi-currency-dollar"></i><span>Custom Charges</span></a></li>
            <li><a href="{{ url('procurements/companies') }}"><i class="bi bi-building"></i><span>PO Copmany</span></a></li>
            <li><a href="{{ url('procurements/users-privileges') }}"><i class="bi bi-person-badge"></i><span>User Privileges</span></a></li>
            <li><a href="{{ url('procurements/pab/list') }}"><i class="bi bi-boxes"></i><span>PAB</span></a></li>     
        </ul>
    </div>

    @if(auth()->user()->can('UserRead') || auth()->user()->hasAnyRole(['SuperAdmin']))
        <div id="expanded-users" class="expanded-sidebar desktop-only @if(!(Request::is('users*') || Request::is('user/*') || Request::is('roles-permission*'))) hidden @endif app-sidebar">
            <div class="expanded-sidebar-header">{{ trans('sidebar.headers.users') }}</div>
            <ul class="expanded-menu">
                @can("UserRead")
                    <li @if(Request::is('users*') || Request::is('user/*')) class="active-link" @endif ><a href="{{ url('users') }}"><i class="bi bi-people"></i>{{ trans('sidebar.users_menu.all_users') }}</a></li>
                @endcan
                @hasanyrole("SuperAdmin")
                    <li @if(Request::is('roles-permission*')) class="active-link" @endif><a href="{{ route('role-permission') }}"><i class="bi bi-shield"></i> {{ trans('sidebar.users_menu.roles_permissions') }}</a></li>
                @endhasanyrole
            </ul>
        </div>
    @endif
    

<!-- Knowledge Management Expanded -->
    <div id="expanded-knowledge-management" class="expanded-sidebar desktop-only @if(!(Request::is('knowledge-document*') || Request::is('categories*'))) hidden @endif app-sidebar">
        <div class="expanded-sidebar-header">{{ trans('sidebar.mini.kd') }}</div>
        <ul class="expanded-menu">
            <li @if(Request::is('knowledge_document/document*')) class="active-link" @endif><a href="{{ url('knowledge_document/document') }}"><i class="bi bi-stack"></i>Details</a></li>
            <li @if(Request::is('knowledge_document/categories*')) class="active-link" @endif>
                <a href="{{ url('knowledge_document/categories') }}">
                    <i class="bi bi-folder"></i>
                    <span>{{ trans('sidebar.kd_menu.kd_category') }}</span>
                </a>
            </li>
        </ul>
    </div>

    {{-- jobs --}}
    <div id="expanded-jobs" class="expanded-sidebar desktop-only @if(Request::path() == 'jobs') hidden @endif app-sidebar">
        <div class="expanded-sidebar-header">{{ trans('sidebar.jobs') }}</div>
        <ul class="expanded-menu">
            <li @if(Request::path() == 'jobs') class="active-link" @endif><a href="{{ url('jobs') }}"><i class="bi bi-stack"></i>{{ trans('sidebar.ongoing_jobs') }}</a></li>
            <li @if(Request::path() == 'failed_jobs') class="active-link" @endif>
                <a href="{{ url('failed_jobs') }}">
                    <i class="bi bi-folder"></i>
                    <span>{{ trans('sidebar.failed_jobs') }}</span>
            </a>
            </li>
        </ul>
    </div>
    {{-- News Letters --}}

    @if(in_array(config('app.client'), ['rolepermission', 'ltts']))
    <div id="expanded-newsletter" class="expanded-sidebar desktop-only @if(Request::segment(1) === 'view-secure-pdf') hidden @endif app-sidebar">
        <div class="expanded-sidebar-header">News Letters</div>
        <ul class="expanded-menu">
            <li @if(Request::segment(1) === 'view-secure-pdf') class="active-link" @endif><a href="{{ url('view-secure-pdf', encrypt('US_Immigration_Awareness_Session.pdf')) }}"><i class="bi bi-stack"></i>{{ trans("sidebar.us_immigration_awareness_session") }}</a></li>
        </ul>
    </div>
    @endif

    <div id="expanded-reports" class="expanded-sidebar desktop-only @if(!(Request::is('reports*'))) hidden @endif app-sidebar">
        <div class="expanded-sidebar-header">{{trans('sidebar.report_menu.reports')}}</div>
        <ul class="expanded-menu">
            @can("TicketReport")
                <li @if(Request::path() == 'reports/tickets/ticket-report') class="active-link" @endif >
                    <a href="{{ url('reports/tickets/ticket-report') }}">
                       <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="64" height="64">
                        <!-- Paper Roll -->
                        <path d="M80 270 C80 235 110 230 150 230 L150 430 C150 455 130 470 110 470 C90 470 75 455 75 430 L75 275 C75 272 77 270 80 270Z"
                                fill="currentColor" opacity="0.25"/>
                        <path d="M80 270 C80 235 110 230 150 230 L150 430 C150 455 130 470 110 470 C90 470 75 455 75 430 L75 275 C75 272 77 270 80 270Z"
                                fill="none" stroke="currentColor" stroke-width="10"/>

                        <!-- Main Receipt -->
                        <rect x="150" y="85" width="280" height="385" rx="28"
                                fill="currentColor" opacity="0.15"/>
                        <rect x="150" y="85" width="280" height="385" rx="28"
                                fill="none" stroke="currentColor" stroke-width="10"/>

                        <!-- Dollar Sign -->
                        <text x="210" y="165" font-size="70" font-family="Arial, sans-serif" font-weight="bold" fill="currentColor">$</text>

                        <!-- Label Box -->
                        <rect x="300" y="140" width="145" height="70" rx="12"
                                fill="currentColor" opacity="0.1"/>
                        <rect x="300" y="140" width="145" height="70" rx="12"
                                fill="none" stroke="currentColor" stroke-width="8"/>

                        <line x1="325" y1="165" x2="410" y2="165"
                                stroke="currentColor" stroke-width="8" stroke-linecap="round"/>
                        <line x1="325" y1="190" x2="390" y2="190"
                                stroke="currentColor" stroke-width="8" stroke-linecap="round"/>

                        <!-- Receipt Lines -->
                        <line x1="200" y1="290" x2="395" y2="290"
                                stroke="currentColor" stroke-width="8" stroke-linecap="round"/>
                        <line x1="200" y1="325" x2="245" y2="325"
                                stroke="currentColor" stroke-width="8" stroke-linecap="round"/>
                        <line x1="270" y1="325" x2="395" y2="325"
                                stroke="currentColor" stroke-width="8" stroke-linecap="round"/>
                        <line x1="200" y1="360" x2="315" y2="360"
                                stroke="currentColor" stroke-width="8" stroke-linecap="round"/>
                        <line x1="350" y1="360" x2="395" y2="360"
                                stroke="currentColor" stroke-width="8" stroke-linecap="round"/>
                        <line x1="200" y1="395" x2="245" y2="395"
                                stroke="currentColor" stroke-width="8" stroke-linecap="round"/>
                        <line x1="270" y1="395" x2="395" y2="395"
                                stroke="currentColor" stroke-width="8" stroke-linecap="round"/>
                        </svg>
                        {{ trans("custom_report.ticket_report") }}
                    </a>
                </li>
            @endcan
            @can("TicketLocationWiseReport")
            <li @if(Request::path() == 'reports/tickets/location-wise') class="active-link" @endif><a href="{{ url('reports/tickets/location-wise') }}"><i class="bi bi-geo-alt"></i>{{trans('sidebar.report_menu.loaction_wise')}}</a></li>
            @endcan
            @can("TicketDepartmentWiseReport")
            <li @if(Request::path() == 'reports/tickets/department-wise') class="active-link" @endif><a href="{{ url('reports/tickets/department-wise') }}"><i class="bi bi-building"></i>{{trans('sidebar.report_menu.department_wise_tickets')}}</a></li>
            @endcan
            @can("TicketHandlerWiseReport")
                <li @if(Request::path() == 'reports/tickets/ticket-handler-wise') class="active" @endif><a href="{{ url('reports/tickets/ticket-handler-wise') }}"><i class="bi bi-person-badge"></i><span>{{trans('sidebar.report_menu.ticket_hanndler_wise')}}</span></a></li>
            @endcan
            @can("TicketMergedReport")
                <li @if(Request::path() == 'reports/tickets/merged-tickets') class="active" @endif ><a href="{{ url('reports/tickets/merged-tickets') }}"><i class="bi bi-sign-merge-left"></i>{{ trans("sidebar.report_menu.merged_tickets") }}</a></li>
            @endcan
            @can("TicketEscalationReport")
                <li @if(Request::path() == 'reports/tickets/esclate-tickets') class="active" @endif ><a href="{{ url('reports/tickets/esclate-tickets') }}"><i class="bi bi-arrow-up-circle"></i>{{ trans("sidebar.report_menu.esclate_tickets") }}</a></li>
            @endcan
            @can("TicketOverdueReport")
                <li @if(Request::path() == 'reports/tickets/overdue-tickets') class="active" @endif ><a href="{{ url('reports/tickets/overdue-tickets') }}"><i class="bi bi-alarm"></i>{{ trans("sidebar.report_menu.overdue_ticket") }}</a></li>
            @endcan
            @can("HvacReport")
                <li @if(Request::path() == 'reports/tickets/hvac-report') class="active" @endif ><a href="{{ url('reports/tickets/hvac-report') }}"><i class="bi bi-tools"></i>{{ trans("sidebar.report_menu.hvac_report") }}</a></li>
            @endcan
            @can("TicketReport")
                <li @if(Request::path() == 'reports/tickets/categorywise-agening-report') class="active" @endif><a href="{{ url('reports/tickets/categorywise-agening-report') }}"><i class="bi bi-building"></i>{{ trans("sidebar.report_menu.categorywise_agening_report") }}</a></li>
            @endcan
            @can("EmailReportTriggerRead")
                <li @if(Request::path() == 'reports/tickets/email-triggers') class="active-link" @endif ><a href="{{ url('reports/tickets/email-triggers') }}"><i class="bi bi-envelope-fill"></i>{{ trans("sidebar.report_menu.reports_email_trigger") }}</a></li>
            @endcan
            @can("TicketAutoResolvedReport")
                <li @if(Request::path() == 'reports/tickets/auto-resolved-ticket-report') class="active-link" @endif ><a href="{{ url('reports/tickets/auto-resolved-ticket-report') }}"><i class="bi bi-arrow-repeat"></i>{{ trans("sidebar.report_menu.auto_report_ticket") }}</a></li>
            @endcan
            @can("TechnicianActivityLogReport")
                <li @if(Request::path() == 'reports/tickets/tech-log-report') class="active-link" @endif ><a href="{{ url('reports/tickets/tech-log-report') }}"><i class="bi bi-person-video2"></i>{{ trans("sidebar.report_menu.tec_log_reports") }}</a></li>
            @endcan
            @if(config("services.task_module.enabled") && (Auth::user()->can('TaskRead') || Auth::user()->can('TaskAdd')))
                @can("TicketTaskReport")
                    <li @if(Request::path() == 'tickets/ticket-task-report') class="active-link" @endif ><a href="{{ url('reports/tickets/ticket-task-report') }}"><i class="bi bi-clipboard-check"></i>{{ trans("sidebar.report_menu.ticket_task_report") }}</a></li>
                @endcan
            @endIf
            @can("TicketExceptionReport")
                <li><a href="{{ url('reports/tickets/exception-notification') }}"><i class="bi bi-bell-slash"></i>{{ trans("sidebar.report_menu.tickets_exception_notifications") }}</a></li>
            @endcan
            @can("TicketAutoResolvedReport")
                <li><a href="{{ url('reports/tickets/auto-resolved-ticket-report') }}"><i class="bi bi-building"></i>{{ trans("sidebar.report_menu.auto_resolved_tickets_report") }}</a></li>
            @endcan
            @can("TicketHandlerWiseReport")
                <li><a href="{{ url('reports/tickets/day-wise-handler-report') }}"><i class="bi bi-building"></i>{{ trans("sidebar.report_menu.day_wise_handler_report") }}</a></li>
            @endcan
            @can("TicketFeedbackReport")
                <li @if(Request::path() == 'reports/tickets/feedback-report') class="active-link" @endif ><a href="{{ url('reports/tickets/feedback-report') }}"><i class="bi bi-chat-left-text"></i>{{ trans("content.report_fields.feedback_report") }}</a></li>
            @endcan
            @can("TicketReopenReport")
                <li @if(Request::path() == 'reports/tickets/reopen-ticket-report') class="active-link" @endif ><a href="{{ url('reports/tickets/reopen-ticket-report') }}"><i class="bi bi-arrow-clockwise"></i>{{ trans("content.report_fields.reopen_ticket_report") }}</a></li>
            @endcan
            @can('InactiveUserReportRead')
                <li @if(Request::path() == 'reports/tickets/inactive-user-report') class="active-link" @endif ><a href="{{ url('reports/tickets/inactive-user-report') }}"><i class="bi bi-person-x"></i>{{ trans("sidebar.report_menu.inactive_user") }}</a></li>
            @endcan
            @can("CustomReport")
            <li class="has-submenu">
                <a href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" fill="currentColor" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 387 511.39"><path fill-rule="nonzero" d="M63.38 0h198.16c3.35 0 6.38 1.45 8.47 3.77l113.47 109.17c2.32 2.23 3.49 5.22 3.49 8.2l.03 326.88c0 34.72-28.65 63.37-63.37 63.37H63.38C28.62 511.39 0 482.78 0 448.02V63.38C0 28.57 28.58 0 63.38 0zm212.2 40.69v30.37c2.4 31.65 23.82 43.6 53.52 44.11l23.84-.04-77.36-74.44zm88.61 95.97c-1.57.81-3.35 1.27-5.24 1.27l-30.04-.05c-41.78-.67-72.7-21.39-76.03-65.17l-.12-1.65V22.81H63.38c-22.36 0-40.57 18.21-40.57 40.57v186.57l341.38-.12V136.66zM22.81 272.53v175.49c0 22.32 18.26 40.56 40.57 40.56h260.25c22.16 0 40.56-18.4 40.56-40.56V272.65l-341.38-.12zm41.01-129.58H94.1c1.13 0 1.88.75 1.88 1.88v87.61c0 1.12-.75 1.88-1.88 1.88H63.82c-1.14 0-1.9-.76-1.9-1.88v-87.61c-.36-1.13.76-1.88 1.9-1.88zm57.3-35.96h30.27c1.14 0 1.88.74 1.88 1.88v123.57c0 1.12-.74 1.88-1.88 1.88h-30.27c-1.12 0-1.88-.76-1.88-1.88V108.87c0-1.14.76-1.88 1.88-1.88zm171.87 79.12h30.28c1.14 0 1.89.76 1.89 1.89v44.06c0 1.12-.75 1.86-1.89 1.86h-30.28c-1.11 0-1.85-.74-1.85-1.86V188c0-.76.74-1.89 1.85-1.89zm-114.58-21.4h30.28c1.14 0 1.88.75 1.88 1.88v65.47c0 1.12-.74 1.86-1.88 1.86h-30.28c-1.11 0-1.85-.74-1.85-1.86v-65.47c0-.75.74-1.88 1.85-1.88zm57.28-15.25h30.28c1.13 0 1.89.79 1.89 1.89v81.09c0 1.12-.76 1.88-1.89 1.88h-30.28c-1.13 0-1.88-.76-1.88-1.88v-81.09c0-1.1.75-1.89 1.88-1.89zm-33.6 243.69 60.31 1.07c0 21.64-10.64 41.51-28.38 53.57l-31.93-54.64zm-4.03-93.33c14.18 1.09 23.75 2.83 36.55 9.22 24.49 12.78 41.53 38.32 42.94 67.43l.35 4.61h-4.61l-69.9-2.14h-3.92l-1.41-79.12zm9.23 70.61 61.39 1.78c-3.66-35.41-28.36-56.77-62.1-63.53l.71 61.75zm-25.44 16.44 34.42 59.62c-10.64 6.03-22.34 9.24-34.42 9.24-37.97 0-68.84-30.88-68.84-68.86 0-36.9 29.1-67.42 66-68.85l2.84 68.85z"/></svg>
                    <span>{{ trans('sidebar.ticket_menu.custom_report') }}</span>
                </a>
                <ul class="submenu">
                    <li @if(Request::path() == 'reports/custom-reports/view-name') class="active" @endif>
                        <a href="{{ url('reports/custom-reports/view-name') }}">
                            <span>{{trans('sidebar.ticket_menu.all_custom_report')}}</span>
                        </a>
                    </li>
                    <li @if(Request::path() == 'reports/custom-reports') class="active" @endif>
                        <a href="{{ url('reports/custom-reports') }}">
                            <span>{{trans('sidebar.ticket_menu.custom_report_defination')}}</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endcan
            @canany(["ServiceRequestReport", "UsbAdminAccessReport"])
                <li class="has-submenu">
                    <a href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" fill="currentColor" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 387 511.39"><path fill-rule="nonzero" d="M63.38 0h198.16c3.35 0 6.38 1.45 8.47 3.77l113.47 109.17c2.32 2.23 3.49 5.22 3.49 8.2l.03 326.88c0 34.72-28.65 63.37-63.37 63.37H63.38C28.62 511.39 0 482.78 0 448.02V63.38C0 28.57 28.58 0 63.38 0zm212.2 40.69v30.37c2.4 31.65 23.82 43.6 53.52 44.11l23.84-.04-77.36-74.44zm88.61 95.97c-1.57.81-3.35 1.27-5.24 1.27l-30.04-.05c-41.78-.67-72.7-21.39-76.03-65.17l-.12-1.65V22.81H63.38c-22.36 0-40.57 18.21-40.57 40.57v186.57l341.38-.12V136.66zM22.81 272.53v175.49c0 22.32 18.26 40.56 40.57 40.56h260.25c22.16 0 40.56-18.4 40.56-40.56V272.65l-341.38-.12zm41.01-129.58H94.1c1.13 0 1.88.75 1.88 1.88v87.61c0 1.12-.75 1.88-1.88 1.88H63.82c-1.14 0-1.9-.76-1.9-1.88v-87.61c-.36-1.13.76-1.88 1.9-1.88zm57.3-35.96h30.27c1.14 0 1.88.74 1.88 1.88v123.57c0 1.12-.74 1.88-1.88 1.88h-30.27c-1.12 0-1.88-.76-1.88-1.88V108.87c0-1.14.76-1.88 1.88-1.88zm171.87 79.12h30.28c1.14 0 1.89.76 1.89 1.89v44.06c0 1.12-.75 1.86-1.89 1.86h-30.28c-1.11 0-1.85-.74-1.85-1.86V188c0-.76.74-1.89 1.85-1.89zm-114.58-21.4h30.28c1.14 0 1.88.75 1.88 1.88v65.47c0 1.12-.74 1.86-1.88 1.86h-30.28c-1.11 0-1.85-.74-1.85-1.86v-65.47c0-.75.74-1.88 1.85-1.88zm57.28-15.25h30.28c1.13 0 1.89.79 1.89 1.89v81.09c0 1.12-.76 1.88-1.89 1.88h-30.28c-1.13 0-1.88-.76-1.88-1.88v-81.09c0-1.1.75-1.89 1.88-1.89zm-33.6 243.69 60.31 1.07c0 21.64-10.64 41.51-28.38 53.57l-31.93-54.64zm-4.03-93.33c14.18 1.09 23.75 2.83 36.55 9.22 24.49 12.78 41.53 38.32 42.94 67.43l.35 4.61h-4.61l-69.9-2.14h-3.92l-1.41-79.12zm9.23 70.61 61.39 1.78c-3.66-35.41-28.36-56.77-62.1-63.53l.71 61.75zm-25.44 16.44 34.42 59.62c-10.64 6.03-22.34 9.24-34.42 9.24-37.97 0-68.84-30.88-68.84-68.86 0-36.9 29.1-67.42 66-68.85l2.84 68.85z"/></svg>
                        <span>{{ trans('sidebar.ticket_menu.service_request') }}</span>
                    </a>
                    <ul class="submenu">
                        @can("ServiceRequestReport")
                            <li @if(Request::is('reports/tickets/request-report')) class="active-link" @endif><a href="{{ url('reports/tickets/request-report') }}"><span>{{trans('sidebar.ticket_menu.service_request_report')}}</span></a></li>
                            <li @if(Request::is('reports/tickets/request-member-wise')) class="active-link" @endif><a href="{{ url('reports/tickets/request-member-wise') }}">{{ trans("sidebar.ticket_menu.sr_member_report") }}</a></li>
                        @endcan
                        @if(in_array(config('app.client'), ["ltts", "grdemo", "rolepermission"]))
                            @can("UsbAdminAccessReport")
                                <li @if(Request::is('reports/tickets/email-reminder-report')) class="active-link" @endif><a href="{{ url('reports/tickets/email-reminder-report') }}">{{ trans("sidebar.ticket_menu.email_reminder_report") }}</a></li>
                            @endcan
                            @can('PrivilegeCategoryRead')
                                <li @if(Request::is('reports/category-priville-access')) class="active-link" @endif><a href="{{ url('reports/category-priville-access') }}">{{ trans("sidebar.ticket_menu.category_privilege_access") }}</a></li>
                            @endcan
                            @can('Du/BuHeadView')
                                <li @if(Request::is('getDuBuHead')) class="active-link" @endif><a href="{{ url('getDuBuHead') }}">{{ trans("sidebar.ticket_menu.du_bu_head") }}</a></li>
                            @endcan
                            @can('PrivilegeCategoryRead')
                                <li @if(Request::is('reports/tickets/exception-access-report')) class="active-link" @endif><a href="{{ url('reports/tickets/exception-access-report') }}">{{ trans("sidebar.ticket_menu.exception_access_report") }}</a></li>
                            @endcan
                        @endif
                    </ul>
                </li>
            @endcan
            @can("TicketPendencyReportRead")
                <li @if(Request::path() == 'reports/tickets/ticket-pendency-report') class="active-link" @endif ><a href="{{ url('reports/tickets/ticket-pendency-report') }}"><i class="bi bi-chat-left-text"></i>{{ trans("report.ticket_pendency_report") }}</a></li>
            @endcan
        </ul>
    </div>

    <!-- MOBILE SIDEBAR (Bottom Navigation) -->
    <aside class="mini-sidebar mobile-only">
        <a class="mini-icon @if(Request::path() == 'dashboard' || Request::path() == '/') active @endif" data-section="home">
            <svg width="18" height="18" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M23.987 11.8872L15.237 3.13716C14.9089 2.80922 14.4639 2.625 14 2.625C13.5361 2.625 13.0911 2.80922 12.763 3.13716L4.01298 11.8872C3.84969 12.0492 3.72025 12.2422 3.63218 12.4547C3.54412 12.6673 3.49919 12.8952 3.50001 13.1253V23.6253C3.50001 23.8573 3.5922 24.0799 3.75629 24.244C3.92039 24.4081 4.14295 24.5003 4.37501 24.5003H11.375C11.6071 24.5003 11.8296 24.4081 11.9937 24.244C12.1578 24.0799 12.25 23.8573 12.25 23.6253V17.5003H15.75V23.6253C15.75 23.8573 15.8422 24.0799 16.0063 24.244C16.1704 24.4081 16.3929 24.5003 16.625 24.5003H23.625C23.8571 24.5003 24.0796 24.4081 24.2437 24.244C24.4078 24.0799 24.5 23.8573 24.5 23.6253V13.1253C24.5008 12.8952 24.4559 12.6673 24.3678 12.4547C24.2798 12.2422 24.1503 12.0492 23.987 11.8872ZM22.75 22.7503H17.5V16.6253C17.5 16.3932 17.4078 16.1707 17.2437 16.0066C17.0796 15.8425 16.8571 15.7503 16.625 15.7503H11.375C11.1429 15.7503 10.9204 15.8425 10.7563 16.0066C10.5922 16.1707 10.5 16.3932 10.5 16.6253V22.7503H5.25001V13.1253L14 4.37528L22.75 13.1253V22.7503Z" fill="#F8FAFD" />
            </svg>
        </a>
        <a class="mini-icon @if(Request::is('asset*') || Request::is('device*') || Request::is('my-items*')) active @endif" data-section="asset">
            <svg width="18" height="18" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M25.375 18.375H24.5V7.875C24.5 7.17881 24.2234 6.51113 23.7312 6.01884C23.2389 5.52656 22.5712 5.25 21.875 5.25H6.125C5.42881 5.25 4.76113 5.52656 4.26884 6.01884C3.77656 6.51113 3.5 7.17881 3.5 7.875V18.375H2.625C2.39294 18.375 2.17038 18.4672 2.00628 18.6313C1.84219 18.7954 1.75 19.0179 1.75 19.25V21C1.75 21.6962 2.02656 22.3639 2.51884 22.8562C3.01113 23.3484 3.67881 23.625 4.375 23.625H23.625C24.3212 23.625 24.9889 23.3484 25.4812 22.8562C25.9734 22.3639 26.25 21.6962 26.25 21V19.25C26.25 19.0179 26.1578 18.7954 25.9937 18.6313C25.8296 18.4672 25.6071 18.375 25.375 18.375ZM5.25 7.875C5.25 7.64294 5.34219 7.42038 5.50628 7.25628C5.67038 7.09219 5.89294 7 6.125 7H21.875C22.1071 7 22.3296 7.09219 22.4937 7.25628C22.6578 7.42038 22.75 7.64294 22.75 7.875V18.375H5.25V7.875ZM24.5 21C24.5 21.2321 24.4078 21.4546 24.2437 21.6187C24.0796 21.7828 23.8571 21.875 23.625 21.875H4.375C4.14294 21.875 3.92038 21.7828 3.75628 21.6187C3.59219 21.4546 3.5 21.2321 3.5 21V20.125H24.5V21ZM16.625 9.625C16.625 9.85706 16.5328 10.0796 16.3687 10.2437C16.2046 10.4078 15.9821 10.5 15.75 10.5H12.25C12.0179 10.5 11.7954 10.4078 11.6313 10.2437C11.4672 10.0796 11.375 9.85706 11.375 9.625C11.375 9.39294 11.4672 9.17038 11.6313 9.00628C11.7954 8.84219 12.0179 8.75 12.25 8.75H15.75C15.9821 8.75 16.2046 8.84219 16.3687 9.00628C16.5328 9.17038 16.625 9.39294 16.625 9.625Z" fill="white" />
            </svg>
        </a>
        <a class="mini-icon @if(Request::is('ticket*') || Request::is('tickets*') || Request::is('change_request*')) active @endif" data-section="ticket">
            <svg width="18" height="18" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M25.375 11.375C25.6071 11.375 25.8296 11.2828 25.9937 11.1187C26.1578 10.9546 26.25 10.7321 26.25 10.5V7C26.25 6.53587 26.0656 6.09075 25.7374 5.76256C25.4092 5.43437 24.9641 5.25 24.5 5.25H3.5C3.03587 5.25 2.59075 5.43437 2.26256 5.76256C1.93437 6.09075 1.75 6.53587 1.75 7V10.5C1.75 10.7321 1.84219 10.9546 2.00628 11.1187C2.17038 11.2828 2.39294 11.375 2.625 11.375C3.32119 11.375 3.98887 11.6516 4.48116 12.1438C4.97344 12.6361 5.25 13.3038 5.25 14C5.25 14.6962 4.97344 15.3639 4.48116 15.8562C3.98887 16.3484 3.32119 16.625 2.625 16.625C2.39294 16.625 2.17038 16.7172 2.00628 16.8813C1.84219 17.0454 1.75 17.2679 1.75 17.5V21C1.75 21.4641 1.93437 21.9092 2.26256 22.2374C2.59075 22.5656 3.03587 22.75 3.5 22.75H24.5C24.9641 22.75 25.4092 22.5656 25.7374 22.2374C26.0656 21.9092 26.25 21.4641 26.25 21V17.5C26.25 17.2679 26.1578 17.0454 25.9937 16.8813C25.8296 16.7172 25.6071 16.625 25.375 16.625C24.6788 16.625 24.0111 16.3484 23.5188 15.8562C23.0266 15.3639 22.75 14.6962 22.75 14C22.75 13.3038 23.0266 12.6361 23.5188 12.1438C24.0111 11.6516 24.6788 11.375 25.375 11.375ZM3.5 18.2875C4.489 18.0867 5.37817 17.5501 6.01683 16.7687C6.6555 15.9873 7.00439 15.0092 7.00439 14C7.00439 12.9908 6.6555 12.0127 6.01683 11.2313C5.37817 10.4499 4.489 9.91333 3.5 9.7125V7H9.625V21H3.5V18.2875ZM24.5 18.2875V21H11.375V7H24.5V9.7125C23.511 9.91333 22.6218 10.4499 21.9832 11.2313C21.3445 12.0127 20.9956 12.9908 20.9956 14C20.9956 15.0092 21.3445 15.9873 21.9832 16.7687C22.6218 17.5501 23.511 18.0867 24.5 18.2875Z" fill="white" />
            </svg>
        </a>
        <a class="mini-icon @if(Request::is('users*') || Request::is('user/*') || Request::is('roles-permission*')) active @endif" data-section="users">
            <svg width="18" height="18" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12.8242 17.2713C13.9969 16.4905 14.8873 15.3531 15.3636 14.0273C15.8399 12.7014 15.8768 11.2574 15.469 9.9089C15.0612 8.56038 14.2302 7.37891 13.099 6.53919C11.9678 5.69948 10.5963 5.24609 9.18749 5.24609C7.77866 5.24609 6.40723 5.69948 5.27599 6.53919C4.14476 7.37891 3.31379 8.56038 2.90597 9.9089C2.49814 11.2574 2.53511 12.7014 3.01141 14.0273C3.48771 15.3531 4.37805 16.4905 5.55077 17.2713C3.42944 18.0531 1.61777 19.4999 0.386087 21.3958C0.321354 21.492 0.276391 21.6001 0.25381 21.7139C0.23123 21.8276 0.231482 21.9447 0.254554 22.0583C0.277625 22.172 0.323055 22.2799 0.388203 22.3758C0.453351 22.4718 0.536916 22.5538 0.634043 22.6171C0.731169 22.6805 0.839919 22.7239 0.953971 22.7448C1.06802 22.7658 1.1851 22.7639 1.2984 22.7392C1.4117 22.7145 1.51896 22.6675 1.61395 22.601C1.70894 22.5345 1.78976 22.4497 1.85171 22.3517C2.6462 21.1298 3.73335 20.1256 5.01444 19.4305C6.29553 18.7354 7.72997 18.3713 9.18749 18.3713C10.645 18.3713 12.0795 18.7354 13.3605 19.4305C14.6416 20.1256 15.7288 21.1298 16.5233 22.3517C16.6516 22.5425 16.8499 22.675 17.0752 22.7208C17.3005 22.7665 17.5347 22.7218 17.7273 22.5962C17.9198 22.4706 18.0552 22.2742 18.1042 22.0496C18.1531 21.825 18.1117 21.5901 17.9889 21.3958C16.7572 19.4999 14.9455 18.0531 12.8242 17.2713ZM4.37499 11.8113C4.37499 10.8594 4.65724 9.92899 5.18605 9.13757C5.71485 8.34616 6.46646 7.72933 7.34583 7.36509C8.2252 7.00084 9.19283 6.90554 10.1264 7.09123C11.0599 7.27692 11.9174 7.73526 12.5904 8.4083C13.2635 9.08135 13.7218 9.93885 13.9075 10.8724C14.0932 11.8059 13.9979 12.7736 13.6337 13.6529C13.2694 14.5323 12.6526 15.2839 11.8612 15.8127C11.0698 16.3415 10.1393 16.6238 9.18749 16.6238C7.91158 16.6223 6.68835 16.1148 5.78614 15.2126C4.88393 14.3104 4.37644 13.0872 4.37499 11.8113ZM27.3591 22.6066C27.1647 22.7333 26.928 22.7777 26.7009 22.7299C26.4738 22.6821 26.2751 22.5461 26.1483 22.3517C25.3547 21.129 24.2677 20.1245 22.9864 19.4296C21.705 18.7348 20.2701 18.3718 18.8125 18.3738C18.5804 18.3738 18.3579 18.2816 18.1938 18.1175C18.0297 17.9534 17.9375 17.7308 17.9375 17.4988C17.9375 17.2667 18.0297 17.0441 18.1938 16.88C18.3579 16.7159 18.5804 16.6238 18.8125 16.6238C19.5212 16.6231 20.221 16.4659 20.862 16.1634C21.5029 15.8609 22.0691 15.4206 22.5201 14.8739C22.9711 14.3273 23.2959 13.6877 23.4711 13.001C23.6463 12.3143 23.6677 11.5974 23.5337 10.9014C23.3997 10.2055 23.1136 9.54773 22.696 8.97516C22.2783 8.40259 21.7393 7.92933 21.1176 7.5892C20.4958 7.24907 19.8066 7.05047 19.0992 7.00758C18.3918 6.96469 17.6836 7.07858 17.0253 7.3411C16.918 7.3875 16.8024 7.41192 16.6855 7.4129C16.5686 7.41389 16.4526 7.39143 16.3445 7.34685C16.2364 7.30227 16.1384 7.23648 16.0561 7.15336C15.9739 7.07024 15.9091 6.97147 15.8657 6.8629C15.8223 6.75433 15.801 6.63816 15.8033 6.52125C15.8055 6.40433 15.8311 6.28905 15.8787 6.18222C15.9262 6.07538 15.9947 5.97916 16.08 5.89924C16.1654 5.81931 16.2659 5.7573 16.3756 5.71688C17.8822 5.11603 19.558 5.09441 21.0796 5.65619C22.6012 6.21796 23.8608 7.32333 24.6155 8.75906C25.3702 10.1948 25.5664 11.8591 25.1664 13.431C24.7664 15.0029 23.7984 16.371 22.4492 17.2713C24.5705 18.0531 26.3822 19.4999 27.6139 21.3958C27.7406 21.5902 27.785 21.8269 27.7372 22.054C27.6894 22.281 27.5534 22.4798 27.3591 22.6066Z" fill="white" />
            </svg>
        </a>
        <a class="mini-icon @if(Request::is('patch-management*')) active @endif" data-section="patch-management">
            <svg width="18" height="18" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M22.75 4.375H5.25C4.78587 4.375 4.34075 4.55937 4.01256 4.88756C3.68437 5.21575 3.5 5.66087 3.5 6.125V12.25C3.5 18.0162 6.29125 21.5108 8.63297 23.427C11.1552 25.4898 13.6642 26.1898 13.7736 26.2194C13.924 26.2603 14.0826 26.2603 14.233 26.2194C14.3423 26.1898 16.8481 25.4898 19.3736 23.427C21.7087 21.5108 24.5 18.0162 24.5 12.25V6.125C24.5 5.66087 24.3156 5.21575 23.9874 4.88756C23.6592 4.55937 23.2141 4.375 22.75 4.375ZM22.75 12.25C22.75 16.3045 21.2559 19.5956 18.3094 22.0303C17.0267 23.0866 15.5679 23.9085 14 24.4584C12.4526 23.9181 11.0118 23.1107 9.74312 22.073C6.76156 19.6339 5.25 16.3297 5.25 12.25V6.125H22.75V12.25ZM9.00594 15.4941C8.84175 15.3299 8.74951 15.1072 8.74951 14.875C8.74951 14.6428 8.84175 14.4201 9.00594 14.2559C9.17012 14.0918 9.39281 13.9995 9.625 13.9995C9.85719 13.9995 10.0799 14.0918 10.2441 14.2559L12.25 16.263L17.7559 10.7559C17.8372 10.6746 17.9337 10.6102 18.04 10.5662C18.1462 10.5222 18.26 10.4995 18.375 10.4995C18.49 10.4995 18.6038 10.5222 18.71 10.5662C18.8163 10.6102 18.9128 10.6746 18.9941 10.7559C19.0754 10.8372 19.1398 10.9337 19.1838 11.04C19.2278 11.1462 19.2505 11.26 19.2505 11.375C19.2505 11.49 19.2278 11.6038 19.1838 11.71C19.1398 11.8163 19.0754 11.9128 18.9941 11.9941L12.8691 18.1191C12.7878 18.2004 12.6913 18.265 12.5851 18.309C12.4788 18.353 12.365 18.3757 12.25 18.3757C12.135 18.3757 12.0212 18.353 11.9149 18.309C11.8087 18.265 11.7122 18.2004 11.6309 18.1191L9.00594 15.4941Z" fill="white" />
            </svg>
        </a>
        <a class="mini-icon @if(Request::is('procurement*')) active @endif" data-section="procurement">
            <svg width="18" height="18" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M22.75 4.375H5.25C4.78587 4.375 4.34075 4.55937 4.01256 4.88756C3.68437 5.21575 3.5 5.66087 3.5 6.125V12.25C3.5 18.0162 6.29125 21.5108 8.63297 23.427C11.1552 25.4898 13.6642 26.1898 13.7736 26.2194C13.924 26.2603 14.0826 26.2603 14.233 26.2194C14.3423 26.1898 16.8481 25.4898 19.3736 23.427C21.7087 21.5108 24.5 18.0162 24.5 12.25V6.125C24.5 5.66087 24.3156 5.21575 23.9874 4.88756C23.6592 4.55937 23.2141 4.375 22.75 4.375ZM22.75 12.25C22.75 16.3045 21.2559 19.5956 18.3094 22.0303C17.0267 23.0866 15.5679 23.9085 14 24.4584C12.4526 23.9181 11.0118 23.1107 9.74312 22.073C6.76156 19.6339 5.25 16.3297 5.25 12.25V6.125H22.75V12.25ZM9.00594 15.4941C8.84175 15.3299 8.74951 15.1072 8.74951 14.875C8.74951 14.6428 8.84175 14.4201 9.00594 14.2559C9.17012 14.0918 9.39281 13.9995 9.625 13.9995C9.85719 13.9995 10.0799 14.0918 10.2441 14.2559L12.25 16.263L17.7559 10.7559C17.8372 10.6746 17.9337 10.6102 18.04 10.5662C18.1462 10.5222 18.26 10.4995 18.375 10.4995C18.49 10.4995 18.6038 10.5222 18.71 10.5662C18.8163 10.6102 18.9128 10.6746 18.9941 10.7559C19.0754 10.8372 19.1398 10.9337 19.1838 11.04C19.2278 11.1462 19.2505 11.26 19.2505 11.375C19.2505 11.49 19.2278 11.6038 19.1838 11.71C19.1398 11.8163 19.0754 11.9128 18.9941 11.9941L12.8691 18.1191C12.7878 18.2004 12.6913 18.265 12.5851 18.309C12.4788 18.353 12.365 18.3757 12.25 18.3757C12.135 18.3757 12.0212 18.353 11.9149 18.309C11.8087 18.265 11.7122 18.2004 11.6309 18.1191L9.00594 15.4941Z" fill="white" />
            </svg>
        </a>
        <a class="mini-icon @if(Request::is('mailroom*')) active @endif" data-section="mailroom">
            <svg width="18" height="18" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M24.9856 9.77213L14.4856 2.77213C14.3418 2.6762 14.1729 2.625 14 2.625C13.8271 2.625 13.6582 2.6762 13.5144 2.77213L3.01438 9.77213C2.89451 9.8521 2.79625 9.96044 2.72833 10.0875C2.6604 10.2146 2.62491 10.3565 2.625 10.5006V21.8756C2.625 22.3397 2.80937 22.7848 3.13756 23.113C3.46575 23.4412 3.91087 23.6256 4.375 23.6256H23.625C24.0891 23.6256 24.5342 23.4412 24.8624 23.113C25.1906 22.7848 25.375 22.3397 25.375 21.8756V10.5006C25.3751 10.3565 25.3396 10.2146 25.2717 10.0875C25.2037 9.96044 25.1055 9.8521 24.9856 9.77213ZM14 4.55166L22.9589 10.5246L15.6308 15.7506H12.3714L5.04328 10.5246L14 4.55166ZM4.375 21.8756V12.1992L11.5828 17.3398C11.7312 17.4458 11.909 17.5028 12.0914 17.5028H15.9086C16.091 17.5028 16.2688 17.4458 16.4172 17.3398L23.625 12.1992V21.8756H4.375Z" fill="white" />
            </svg>
        </a>
        <a class="mini-icon @if(Request::is('rdp*')) active @endif" data-section="rdp">
            <svg width="18" height="18" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M23.625 7.87501H14.362L11.375 4.88798C11.2131 4.72477 11.0203 4.59537 10.808 4.50731C10.5956 4.41925 10.3679 4.37427 10.138 4.37501H4.375C3.91087 4.37501 3.46575 4.55938 3.13756 4.88757C2.80937 5.21576 2.625 5.66088 2.625 6.12501V21.9428C2.62558 22.3888 2.80299 22.8163 3.11834 23.1317C3.43369 23.447 3.86122 23.6244 4.30719 23.625H23.7223C24.1605 23.6244 24.5805 23.4501 24.8903 23.1403C25.2001 22.8305 25.3744 22.4105 25.375 21.9724V9.62501C25.375 9.16088 25.1906 8.71576 24.8624 8.38757C24.5342 8.05938 24.0891 7.87501 23.625 7.87501ZM10.138 6.12501L11.888 7.87501H4.375V6.12501H10.138ZM23.625 21.875H4.375V9.62501H23.625V21.875ZM11.375 14.875H16.625C16.8571 14.875 17.0796 14.9672 17.2437 15.1313C17.4078 15.2954 17.5 15.5179 17.5 15.75C17.5 15.9821 17.4078 16.2046 17.2437 16.3687C17.0796 16.5328 16.8571 16.625 16.625 16.625H11.375C11.1429 16.625 10.9204 16.5328 10.7563 16.3687C10.5922 16.2046 10.5 15.9821 10.5 15.75C10.5 15.5179 10.5922 15.2954 10.7563 15.1313C10.9204 14.9672 11.1429 14.875 11.375 14.875Z" fill="white" />
            </svg>
        </a>
        <a class="mini-icon @if(Request::is('live-monitoring*')) active @endif" data-section="live-monitoring">
            <svg width="18" height="18" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M22.75 4.375H5.25C4.55381 4.375 3.88613 4.65156 3.39384 5.14384C2.90156 5.63613 2.625 6.30381 2.625 7V19.25C2.625 19.9462 2.90156 20.6139 3.39384 21.1062C3.88613 21.5984 4.55381 21.875 5.25 21.875H22.75C23.4462 21.875 24.1139 21.5984 24.6062 21.1062C25.0984 20.6139 25.375 19.9462 25.375 19.25V7C25.375 6.30381 25.0984 5.63613 24.6062 5.14384C24.1139 4.65156 23.4462 4.375 22.75 4.375ZM23.625 19.25C23.625 19.4821 23.5328 19.7046 23.3687 19.8687C23.2046 20.0328 22.9821 20.125 22.75 20.125H5.25C5.01794 20.125 4.79538 20.0328 4.63128 19.8687C4.46719 19.7046 4.375 19.4821 4.375 19.25V7C4.375 6.76794 4.46719 6.54538 4.63128 6.38128C4.79538 6.21719 5.01794 6.125 5.25 6.125H22.75C22.9821 6.125 23.2046 6.21719 23.3687 6.38128C23.5328 6.54538 23.625 6.76794 23.625 7V19.25ZM18.375 24.5C18.375 24.7321 18.2828 24.9546 18.1187 25.1187C17.9546 25.2828 17.7321 25.375 17.5 25.375H10.5C10.2679 25.375 10.0454 25.2828 9.88128 25.1187C9.71719 24.9546 9.625 24.7321 9.625 24.5C9.625 24.2679 9.71719 24.0454 9.88128 23.8813C10.0454 23.7172 10.2679 23.625 10.5 23.625H17.5C17.7321 23.625 17.9546 23.7172 18.1187 23.8813C18.2828 24.0454 18.375 24.2679 18.375 24.5Z" fill="white" />
            </svg>
        </a>

        <!-- Procurement Menu -->
        <a class="mini-icon @if(Request::is('procurement*') || Request::is('pab*') || Request::is('custom-tax*') || Request::is('user-privileges*') || Request::is('custom-charge*') || Request::is('po-companies*')) active @endif"
            data-section="procurement" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Procurement">
                <div class="icon-wrapper">
                    <svg width="25" height="25" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M24.5 8.75H19.25V7C19.25 5.53587 18.6312 4.13118 17.5374 3.13256C16.4437 2.13395 14.9608 1.75 13.4375 1.75H14.5625C16.0858 1.75 17.5687 2.13395 18.6624 3.13256C19.7562 4.13118 20.375 5.53587 20.375 7V8.75H24.5C24.9641 8.75 25.4092 8.93437 25.7374 9.26256C26.0656 9.59075 26.25 10.0359 26.25 10.5V24.5C26.25 24.9641 26.0656 25.4092 25.7374 25.7374C25.4092 26.0656 24.9641 26.25 24.5 26.25H3.5C3.03587 26.25 2.59075 26.0656 2.26256 25.7374C1.93437 25.4092 1.75 24.9641 1.75 24.5V10.5C1.75 10.0359 1.93437 9.59075 2.26256 9.26256C2.59075 8.93437 3.03587 8.75 3.5 8.75H8.75V7C8.75 5.53587 9.36875 4.13118 10.4625 3.13256C11.5562 2.13395 13.0392 1.75 14.5625 1.75C16.0858 1.75 17.5687 2.13395 18.6624 3.13256C19.7562 4.13118 20.375 5.53587 20.375 7V8.75H14.5625V7C14.5625 6.76794 14.4703 6.54538 14.3062 6.38128C14.1421 6.21719 13.9196 6.125 13.6875 6.125C13.4554 6.125 13.2329 6.21719 13.0688 6.38128C12.9047 6.54538 12.8125 6.76794 12.8125 7V8.75H8.75V7C8.75 6.76794 8.65781 6.54538 8.49372 6.38128C8.32962 6.21719 8.10706 6.125 7.875 6.125C7.64294 6.125 7.42038 6.21719 7.25628 6.38128C7.09219 6.54538 7 6.76794 7 7V8.75H3.5C3.26794 8.75 3.04538 8.84219 2.88128 9.00628C2.71719 9.17038 2.625 9.39294 2.625 9.625V24.5C2.625 24.7321 2.71719 24.9546 2.88128 25.1187C3.04538 25.2828 3.26794 25.375 3.5 25.375H24.5C24.7321 25.375 24.9546 25.2828 25.1187 25.1187C25.2828 24.9546 25.375 24.7321 25.375 24.5V10.5C25.375 10.2679 25.2828 10.0454 25.1187 9.88128C24.9546 9.71719 24.7321 9.625 24.5 9.625H20.375V11.375C20.375 11.6071 20.2828 11.8296 20.1187 11.9937C19.9546 12.1578 19.7321 12.25 19.5 12.25C19.2679 12.25 19.0454 12.1578 18.8813 11.9937C18.7172 11.8296 18.625 11.6071 18.625 11.375V9.625H9.375V11.375C9.375 11.6071 9.28281 11.8296 9.11872 11.9937C8.95462 12.1578 8.73206 12.25 8.5 12.25C8.26794 12.25 8.04538 12.1578 7.88128 11.9937C7.71719 11.8296 7.625 11.6071 7.625 11.375V9.625H3.5V10.5H24.5V8.75Z" fill="white" />
                    </svg>
                </div>
                <span class="mini-text">Procurement</span>
                <span class="active-bar"></span>
            </a>

        <a class="mini-icon" data-section="more">
            <svg width="18" height="18" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9.33341 7.0013C9.33341 7.62014 9.08758 8.21363 8.65 8.65122C8.21241 9.0888 7.61892 9.33464 7.00008 9.33464C6.38124 9.33464 5.78775 9.0888 5.35017 8.65122C4.91258 8.21363 4.66675 7.62014 4.66675 7.0013C4.66675 6.38246 4.91258 5.78897 5.35017 5.35139C5.78775 4.9138 6.38124 4.66797 7.00008 4.66797C7.61892 4.66797 8.21241 4.9138 8.65 5.35139C9.08758 5.78897 9.33341 6.38246 9.33341 7.0013ZM9.33341 14.0013C9.33341 14.6201 9.08758 15.2136 8.65 15.6512C8.21241 16.0888 7.61892 16.3346 7.00008 16.3346C6.38124 16.3346 5.78775 16.0888 5.35017 15.6512C4.91258 15.2136 4.66675 14.6201 4.66675 14.0013C4.66675 13.3825 4.91258 12.789 5.35017 12.3514C5.78775 11.9138 6.38124 11.668 7.00008 11.668C7.61892 11.668 8.21241 11.9138 8.65 12.3514C9.08758 12.789 9.33341 13.3825 9.33341 14.0013ZM7.00008 23.3346C7.61892 23.3346 8.21241 23.0888 8.65 22.6512C9.08758 22.2136 9.33341 21.6201 9.33341 21.0013C9.33341 20.3825 9.08758 19.789 8.65 19.3514C8.21241 18.9138 7.61892 18.668 7.00008 18.668C6.38124 18.668 5.78775 18.9138 5.35017 19.3514C4.91258 19.789 4.66675 20.3825 4.66675 21.0013C4.66675 21.6201 4.91258 22.2136 5.35017 22.6512C5.78775 23.0888 6.38124 23.3346 7.00008 23.3346ZM16.3334 7.0013C16.3334 7.62014 16.0876 8.21363 15.65 8.65122C15.2124 9.0888 14.6189 9.33464 14.0001 9.33464C13.3812 9.33464 12.7878 9.0888 12.3502 8.65122C11.9126 8.21363 11.6667 7.62014 11.6667 7.0013C11.6667 6.38246 11.9126 5.78897 12.3502 5.35139C12.7878 4.9138 13.3812 4.66797 14.0001 4.66797C14.6189 4.66797 15.2124 4.9138 15.65 5.35139C16.0876 5.78897 16.3334 6.38246 16.3334 7.0013ZM14.0001 16.3346C14.6189 16.3346 15.2124 16.0888 15.65 15.6512C16.0876 15.2136 16.3334 14.6201 16.3334 14.0013C16.3334 13.3825 16.0876 12.789 15.65 12.3514C15.2124 11.9138 14.6189 11.668 14.0001 11.668C13.3812 11.668 12.7878 11.9138 12.3502 12.3514C11.9126 12.789 11.6667 13.3825 11.6667 14.0013C11.6667 14.6201 11.9126 15.2136 12.3502 15.6512C12.7878 16.0888 13.3812 16.3346 14.0001 16.3346ZM16.3334 21.0013C16.3334 21.6201 16.0876 22.2136 15.65 22.6512C15.2124 23.0888 14.6189 23.3346 14.0001 23.3346C13.3812 23.3346 12.7878 23.0888 12.3502 22.6512C11.9126 22.2136 11.6667 21.6201 11.6667 21.0013C11.6667 20.3825 11.9126 19.789 12.3502 19.3514C12.7878 18.9138 13.3812 18.668 14.0001 18.668C14.6189 18.668 15.2124 18.9138 15.65 19.3514C16.0876 19.789 16.3334 20.3825 16.3334 21.0013ZM21.0001 9.33464C21.6189 9.33464 22.2124 9.0888 22.65 8.65122C23.0876 8.21363 23.3334 7.62014 23.3334 7.0013C23.3334 6.38246 23.0876 5.78897 22.65 5.35139C22.2124 4.9138 21.6189 4.66797 21.0001 4.66797C20.3812 4.66797 19.7878 4.9138 19.3502 5.35139C18.9126 5.78897 18.6667 6.38246 18.6667 7.0013C18.6667 7.62014 18.9126 8.21363 19.3502 8.65122C19.7878 9.0888 20.3812 9.33464 21.0001 9.33464ZM23.3334 14.0013C23.3334 14.6201 23.0876 15.2136 22.65 15.6512C22.2124 16.0888 21.6189 16.3346 21.0001 16.3346C20.3812 16.3346 19.7878 16.0888 19.3502 15.6512C18.9126 15.2136 18.6667 14.6201 18.6667 14.0013C18.6667 13.3825 18.9126 12.789 19.3502 12.3514C19.7878 11.9138 20.3812 11.668 21.0001 11.668C21.6189 11.668 22.2124 11.9138 22.65 12.3514C23.0876 12.789 23.3334 13.3825 23.3334 14.0013ZM21.0001 23.3346C21.6189 23.3346 22.2124 23.0888 22.65 22.6512C23.0876 22.2136 23.3334 21.6201 23.3334 21.0013C23.3334 20.3825 23.0876 19.789 22.65 19.3514C22.2124 18.9138 21.6189 18.668 21.0001 18.668C20.3812 18.668 19.7878 18.9138 19.3502 19.3514C18.9126 19.789 18.6667 20.3825 18.6667 21.0013C18.6667 21.6201 18.9126 22.2136 19.3502 22.6512C19.7878 23.0888 20.3812 23.3346 21.0001 23.3346Z" fill="white" />
            </svg>
        </a>
    </aside>

    <!-- EXPANDED SIDEBARS (MOBILE) -->
    <div id="expanded-home-mobile" class="expanded-sidebar mobile-only hidden">
        <div class="expanded-sidebar-header">Home</div>
        <ul class="expanded-menu">
            <li><a href="#"><i class="bi bi-house-door"></i> Dashboard</a></li>
            <li><a href="#"><i class="bi bi-bell"></i> Notifications</a></li>
            <li><a href="#"><i class="bi bi-person-circle"></i> Profile</a></li>
            <li><a href="#"><i class="bi bi-gear"></i> Settings</a></li>
        </ul>
    </div>

    <div id="expanded-knowledge-management" class="expanded-sidebar desktop-only @if(!(Request::is('knowledge-document*') || Request::is('categories*'))) hidden @endif app-sidebar">
        <div class="expanded-sidebar-header">{{ trans('sidebar.mini.kd') }}</div>

        <ul class="expanded-menu">

            {{-- <li><a href="{{ url('kd/knowledge_base') }}"><i class="bi bi-book"></i>Knowledge Articles</a>   </li> --}}
            <li><a href="{{ url('knowledge_document/document') }}"><i class="bi bi-stack"></i>Details</a></li>
            <li @if(Request::is('knowledge_document/categories*')) class="active-link" @endif>
                <a href="{{ url('knowledge_document/categories') }}">
                    <i class="bi bi-folder"></i>
                    <span>{{ trans('sidebar.kd_menu.kd_category') }}</span>
                </a>
            </li>
        </ul>
    </div>

    <div id="expanded-asset-mobile" class="expanded-sidebar mobile-only hidden">
        <div class="expanded-sidebar-header">{{ trans('sidebar.headers.asset') }}</div>
        <ul class="expanded-menu">
            <li><a href="#"><i class="bi bi-grid"></i>{{ trans('sidebar.asset_menu.dashboard') }}</a></li>
            <li><a href="#"><i class="bi bi-laptop"></i> {{ trans('sidebar.asset_menu.my_items') }}</a></li>
            <li><a href="#"><i class="bi bi-plus-circle"></i> {{ trans('sidebar.asset_menu.my_tickets') }}</a></li>
            <li><a href="#"><i class="bi bi-bar-chart"></i> {{ trans('sidebar.asset_menu.my_approvals') }}</a></li>
        </ul>
    </div>

    <div id="expanded-ticket-mobile" class="expanded-sidebar mobile-only hidden">
        <div class="expanded-sidebar-header">{{ trans('sidebar.headers.ticket') }}</div>
        <ul class="expanded-menu">
            <li><a href="#"><i class="bi bi-grid"></i> {{ trans('sidebar.ticket_menu.dashboard') }}</a></li>
            <li><a href="#"><i class="bi bi-ticket-perforated"></i>{{ trans('sidebar.ticket_menu.my_tickets') }}</a></li>
            <li><a href="#"><i class="bi bi-check-circle"></i> {{ trans('sidebar.ticket_menu.my_approvals') }}</a></li>
            <li><a href="#"><i class="bi bi-megaphone"></i> {{ trans('sidebar.ticket_menu.announcements') }}</a></li>
        </ul>
    </div>

    <div id="expanded-users-mobile" class="expanded-sidebar mobile-only hidden">
        <div class="expanded-sidebar-header">{{ trans('sidebar.headers.users') }}</div>
        <ul class="expanded-menu">
            <li><a href="#"><i class="bi bi-people"></i>{{ trans('sidebar.users_menu.all_users') }}</a></li>
            <li><a href="#"><i class="bi bi-person-plus"></i>{{ trans('sidebar.users_menu.add_user') }}</a></li>
            <li><a href="#"><i class="bi bi-shield"></i>{{ trans('sidebar.users_menu.roles_permissions') }}</a></li>
        <li><a href="#"><i class="bi bi-activity"></i>{{ trans('sidebar.users_menu.user_activity') }}</a></li>
        </ul>
    </div>

    <div id="expanded-procurement-mobile" class="expanded-sidebar mobile-only hidden">
        <div class="expanded-sidebar-header">Procurement</div>
        <ul class="expanded-menu">
            <li><a href="#"><i class="bi bi-receipt"></i> PAB (Purchase Authorization)</a></li>
            <li><a href="#"><i class="bi bi-percent"></i> Custom Tax</a></li>
            <li><a href="#"><i class="bi bi-shield-lock"></i> User Privileges</a></li>
            <li><a href="#"><i class="bi bi-receipt-cutoff"></i> Custom Charge</a></li>
            <li><a href="#"><i class="bi bi-building"></i> PO Companies</a></li>
        </ul>
    </div>

    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>
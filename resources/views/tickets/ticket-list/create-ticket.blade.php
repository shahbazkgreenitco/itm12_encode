{{-- @page-meta
{
    "page_no": "CT05F-26",
    "file": "create-ticket.blade.php",
    "versions": [
        {
            "version": "1.1",
            "writer": "Priya Maru",
            "version": "1.1",
            "writer": "Priya Maru",
            "from": "2026-05",
            "reviewer": null,
            "description": "Initial setup"
        }, {
            "version": "1.2",
            "writer": "Priya Maru",
            "from": "2026-05",
            "reviewer": null,
            "description": "close button ui change and tag multiple apply"
        }
    ]
}
--}}
<div class="amg-modal amg-form-modal modal fade" id="createTicket" tabindex="-1" aria-labelledby="createTicketLabel"
    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xxl modal-dialog-centered">
        <form id="ticket-mdl-frm" class="form-horizontal w-100 amg-form-theme" autocomplete="off"
            onsubmit="return false;">
            @csrf
            <input type="hidden" name="tab_action" value="2">
            <input type="hidden" name="id" id="editid">
            <input type="hidden" id="tmp_id" name="tmp_id" value="" />
            <div class="modal-content rounded-5">
                <!-- Modal Header -->
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <div class="d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M21.75 9.75C21.9489 9.75 22.1397 9.67098 22.2803 9.53033C22.421 9.38968 22.5 9.19891 22.5 9V6C22.5 5.60218 22.342 5.22064 22.0607 4.93934C21.7794 4.65804 21.3978 4.5 21 4.5H3C2.60218 4.5 2.22064 4.65804 1.93934 4.93934C1.65804 5.22064 1.5 5.60218 1.5 6V9C1.5 9.19891 1.57902 9.38968 1.71967 9.53033C1.86032 9.67098 2.05109 9.75 2.25 9.75C2.84674 9.75 3.41903 9.98705 3.84099 10.409C4.26295 10.831 4.5 11.4033 4.5 12C4.5 12.5967 4.26295 13.169 3.84099 13.591C3.41903 14.0129 2.84674 14.25 2.25 14.25C2.05109 14.25 1.86032 14.329 1.71967 14.4697C1.57902 14.6103 1.5 14.8011 1.5 15V18C1.5 18.3978 1.65804 18.7794 1.93934 19.0607C2.22064 19.342 2.60218 19.5 3 19.5H21C21.3978 19.5 21.7794 19.342 22.0607 19.0607C22.342 18.7794 22.5 18.3978 22.5 18V15C22.5 14.8011 22.421 14.6103 22.2803 14.4697C22.1397 14.329 21.9489 14.25 21.75 14.25C21.1533 14.25 20.581 14.0129 20.159 13.591C19.7371 13.169 19.5 12.5967 19.5 12C19.5 11.4033 19.7371 10.831 20.159 10.409C20.581 9.98705 21.1533 9.75 21.75 9.75ZM3 15.675C3.84772 15.5029 4.60986 15.043 5.15728 14.3732C5.70471 13.7034 6.00376 12.865 6.00376 12C6.00376 11.135 5.70471 10.2966 5.15728 9.62681C4.60986 8.95705 3.84772 8.49714 3 8.325V6H8.25V18H3V15.675ZM21 15.675V18H9.75V6H21V8.325C20.1523 8.49714 19.3901 8.95705 18.8427 9.62681C18.2953 10.2966 17.9962 11.135 17.9962 12C17.9962 12.865 18.2953 13.7034 18.8427 14.3732C19.3901 15.043 20.1523 15.5029 21 15.675Z" fill="currentColor"/>
                        </svg>
                        <h3 class="modal-title px-1" id="createTicketTitle">{{ __('ticket.create_ticket.create_ticket') }}</h3>
                    </div>
                    <button type="button" data-bs-dismiss="modal" class="modal-close px-4" aria-label="Close">
                        <svg width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z"
                                fill="#515151" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body ticket-modal-body">
                    <div class="row g-4">
                        <!-- LEFT SECTION -->
                        <div class="col-lg-9">
                            <div class="ticket-form-wrapper">
                                <!-- Company -->
                                <div class="amg-form-field row align-items-center mb-3  amg-form-field-row">
                                    <div class="col-md-3 text-start text-md-end">
                                        <label for="company_id" class="form-label fw-normal mb-0 required">{{ __('ticket.create_ticket.company') }}</label>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="input-group ticket-input-group">
                                            <span class="input-group-text">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                    <path
                                                        d="M18.125 17.5H16.25V2.5H16.875C17.0408 2.5 17.1997 2.43415 17.3169 2.31694C17.4342 2.19973 17.5 2.04076 17.5 1.875C17.5 1.70924 17.4342 1.55027 17.3169 1.43306C17.1997 1.31585 17.0408 1.25 16.875 1.25H3.125C2.95924 1.25 2.80027 1.31585 2.68306 1.43306C2.56585 1.55027 2.5 1.70924 2.5 1.875C2.5 2.04076 2.56585 2.19973 2.68306 2.31694C2.80027 2.43415 2.95924 2.5 3.125 2.5H3.75V17.5H1.875C1.70924 17.5 1.55027 17.5658 1.43306 17.6831C1.31585 17.8003 1.25 17.9592 1.25 18.125C1.25 18.2908 1.31585 18.4497 1.43306 18.5669C1.55027 18.6842 1.70924 18.75 1.875 18.75H18.125C18.2908 18.75 18.4497 18.6842 18.5669 18.5669C18.6842 18.4497 18.75 18.2908 18.75 18.125C18.75 17.9592 18.6842 17.8003 18.5669 17.6831C18.4497 17.5658 18.2908 17.5 18.125 17.5ZM5 2.5H15V17.5H12.5V14.375C12.5 14.2092 12.4342 14.0503 12.3169 13.9331C12.1997 13.8158 12.0408 13.75 11.875 13.75H8.125C7.95924 13.75 7.80027 13.8158 7.68306 13.9331C7.56585 14.0503 7.5 14.2092 7.5 14.375V17.5H5V2.5ZM11.25 17.5H8.75V15H11.25V17.5Z"
                                                        fill="currentColor"></path>
                                                </svg>
                                            </span>
                                            <select id="company_id" name="company_id"
                                                class="form-select filter-input"></select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Department -->
                                <div class="amg-form-field row align-items-center mb-3  amg-form-field-row">
                                    <div class="col-md-3 text-start text-md-end">
                                        <label for="department_id" class="form-label fw-normal mb-0 required">{{ __('ticket.create_ticket.department') }}</label>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="input-group ticket-input-group">
                                            <span class="input-group-text">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-building" viewBox="0 0 16 16">
                                                    <path
                                                        d="M4 2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zM4 5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM7.5 5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM4.5 8a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5z" />
                                                    <path
                                                        d="M2 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1zm11 0H3v14h3v-2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5V15h3z" />
                                                </svg>
                                            </span>
                                            <select id="department_id" name="department_id"
                                                class="form-select filter-input"></select>
                                        </div>
                                    </div>
                                </div>

                                <!-- User -->
                                <div class="amg-form-field row align-items-center mb-3 amg-form-field-row d-none" id="create_ticket_for_others">
                                    <div class="col-md-3 text-start text-md-end">
                                        <label for="creator_id" class="form-label fw-normal mb-0 required">{{ __('ticket.create_ticket.user') }} ({{ __('ticket.create_ticket.ticket_raiser') }})</label>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="input-group ticket-input-group">
                                            <span class="input-group-text">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                                                    <path
                                                        d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z" />
                                                </svg>
                                            </span>
                                            <select id="creator_id" name="creator_id"
                                                class="form-select filter-input"></select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Problem Category -->
                                <div class="amg-form-field row align-items-center mb-3  amg-form-field-row">
                                    <div class="col-md-3 text-start text-md-end">
                                        <label for="problem_category_id" class="form-label fw-normal mb-0 required">{{ __('ticket.create_ticket.problem_category') }}</label>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="input-group ticket-input-group">
                                            <span class="input-group-text">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                    <path
                                                        d="M17.5 5H7.5C7.33424 5 7.17527 4.93415 7.05806 4.81694C6.94085 4.69973 6.875 4.54076 6.875 4.375C6.875 4.20924 6.94085 4.05027 7.05806 3.93306C7.17527 3.81585 7.33424 3.75 7.5 3.75H17.5C17.6658 3.75 17.8247 3.81585 17.9419 3.93306C18.0592 4.05027 18.125 4.20924 18.125 4.375C18.125 4.54076 18.0592 4.69973 17.9419 4.81694C17.8247 4.93415 17.6658 5 17.5 5ZM17.5 10.625H7.5C7.33424 10.625 7.17527 10.5592 7.05806 10.4419C6.94085 10.3247 6.875 10.1658 6.875 10C6.875 9.83424 6.94085 9.67527 7.05806 9.55806C7.17527 9.44085 7.33424 9.375 7.5 9.375H17.5C17.6658 9.375 17.8247 9.44085 17.9419 9.55806C18.0592 9.67527 18.125 9.83424 18.125 10C18.125 10.1658 18.0592 10.3247 17.9419 10.4419C17.8247 10.5592 17.6658 10.625 17.5 10.625ZM17.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H17.5C17.6658 15 17.8247 15.0658 17.9419 15.1831C18.0592 15.3003 18.125 15.4592 18.125 15.625C18.125 15.7908 18.0592 15.9497 17.9419 16.0669C17.8247 16.1842 17.6658 16.25 17.5 16.25ZM3.75 5.625C3.5025 5.625 3.26041 5.55169 3.05419 5.41434C2.84798 5.27698 2.68675 5.08176 2.59114 4.85335C2.49554 4.62495 2.47005 4.37361 2.51779 4.13114C2.56554 3.88867 2.68434 3.66587 2.85901 3.49121C3.03367 3.31654 3.25647 3.19773 3.49894 3.14999C3.74141 3.10225 3.99275 3.12774 4.22115 3.22334C4.44956 3.31894 4.64478 3.48018 4.78214 3.68639C4.91949 3.8926 4.9928 4.13469 4.9928 4.38219C4.9928 4.71369 4.86119 5.03176 4.62759 5.26536C4.394 5.49895 4.07594 5.62957 3.74444 5.62957L3.75 5.625ZM3.75 11.25C3.5025 11.25 3.26041 11.1767 3.05419 11.0393C2.84798 10.902 2.68675 10.7068 2.59114 10.4784C2.49554 10.2499 2.47005 9.99861 2.51779 9.75614C2.56554 9.51367 2.68434 9.29087 2.85901 9.11621C3.03367 8.94154 3.25647 8.82273 3.49894 8.77499C3.74141 8.72725 3.99275 8.75274 4.22115 8.84834C4.44956 8.94394 4.64478 9.10518 4.78214 9.31139C4.91949 9.5176 4.9928 9.75969 4.9928 10.0072C4.9928 10.3387 4.86119 10.6568 4.62759 10.8904C4.394 11.1239 4.07594 11.2546 3.74444 11.2546L3.75 11.25ZM3.75 16.875C3.5025 16.875 3.26041 16.8017 3.05419 16.6643C2.84798 16.527 2.68675 16.3318 2.59114 16.1034C2.49554 15.8749 2.47005 15.6236 2.51779 15.3811C2.56554 15.1387 2.68434 14.9159 2.85901 14.7412C3.03367 14.5665 3.25647 14.4477 3.49894 14.4C3.74141 14.3522 3.99275 14.3777 4.22115 14.4733C4.44956 14.5689 4.64478 14.7302 4.78214 14.9364C4.91949 15.1426 4.9928 15.3847 4.9928 15.6322C4.9928 15.9637 4.86119 16.2818 4.62759 16.5154C4.394 16.7489 4.07594 16.8796 3.74444 16.8796L3.75 16.875Z"
                                                        fill="currentColor"></path>
                                                </svg>
                                            </span>
                                            <select id="problem_category_id" name="problem_category_id"
                                                class="form-select filter-input cc_email">
                                                <option>{{ __('ticket.create_ticket.select_category') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- SubCategory -->
                                <div class="amg-form-field row align-items-center mb-3 hide amg-form-field-row" id="sub_category_id_cvr">
                                    <div class="col-md-3 text-start text-md-end">
                                        <label for="sub_category_id" class="form-label fw-normal mb-0 required">{{ __('ticket.create_ticket.subcategory') }}</label>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="input-group ticket-input-group">
                                            <span class="input-group-text">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-textarea-t" viewBox="0 0 16 16">
                                                    <path
                                                        d="M1.5 2.5A1.5 1.5 0 0 1 3 1h10a1.5 1.5 0 0 1 1.5 1.5v3.563a2 2 0 0 1 0 3.874V13.5A1.5 1.5 0 0 1 13 15H3a1.5 1.5 0 0 1-1.5-1.5V9.937a2 2 0 0 1 0-3.874zm1 3.563a2 2 0 0 1 0 3.874V13.5a.5.5 0 0 0 .5.5h10a.5.5 0 0 0 .5-.5V9.937a2 2 0 0 1 0-3.874V2.5A.5.5 0 0 0 13 2H3a.5.5 0 0 0-.5.5zM2 7a1 1 0 1 0 0 2 1 1 0 0 0 0-2m12 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2" />
                                                    <path
                                                        d="M11.434 4H4.566L4.5 5.994h.386c.21-1.252.612-1.446 2.173-1.495l.343-.011v6.343c0 .537-.116.665-1.049.748V12h3.294v-.421c-.938-.083-1.054-.21-1.054-.748V4.488l.348.01c1.56.05 1.963.244 2.173 1.496h.386z" />
                                                </svg>
                                            </span>
                                            <select id="sub_category_id" name="sub_category_id"
                                                class="form-select filter-input cc_email">
                                                <option selected>{{ __('ticket.create_ticket.select_subcategory') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="amg-form-field row align-items-center mb-3 amg-form-field-row departmentCustomFieldWrapper"></div>
                                <div class="amg-form-field row align-items-center mb-3 amg-form-field-row categoryCustomFieldWrapper"></div>

                                <div class="amg-form-field row align-items-center mb-3 amg-form-field-row d-none">
                                    <!-- Priority -->
                                    <div class="col-md-3 text-start text-md-end">
                                        <label for="priority_id" class="form-label fw-normal mb-0 required">{{ __('ticket.create_ticket.priority') }}</label>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <div class="input-group ticket-input-group">
                                                    <span class="input-group-text">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                            fill="currentColor" class="bi bi-justify-left"
                                                            viewBox="0 0 16 16">
                                                            <path fill-rule="evenodd"
                                                                d="M2 12.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5m0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5m0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5" />
                                                        </svg>
                                                    </span>
                                                    <select id="priority_id" name="priority_id" class="form-select filter-input">
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- TAT -->
                                            <div class="col-md-6">
                                                <div class="amg-form-field row align-items-center amg-form-field-row d-none">
                                                    <div class="col-md-3 text-start text-md-end">
                                                        <label for="tat" class="form-label fw-normal mb-0 required">{{ __('ticket.create_ticket.tat') }}</label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <div class="input-group ticket-input-group">
                                                            <span class="input-group-text">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                    height="16" fill="currentColor" class="bi bi-clock"
                                                                    viewBox="0 0 16 16">
                                                                    <path
                                                                        d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z" />
                                                                    <path
                                                                        d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0" />
                                                                </svg>
                                                            </span>
                                                            <input type="text" id="tat" name="tat" class="form-control filter-input" placeholder="{{ __('ticket.create_ticket.enter_tat') }}">
                                                            <span class="input-group-text">
                                                                {{ __('ticket.create_ticket.hrs') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Device -->
                                <div class="amg-form-field row align-items-center mb-3 amg-form-field-row">
                                    <div class="col-md-3 text-start text-md-end">
                                        <label for="device_id" class="form-label fw-normal mb-0">{{ __('ticket.create_ticket.device') }}</label>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="input-group ticket-input-group">
                                            <span class="input-group-text">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M2 15.5V2.6C2 2.26863 2.26863 2 2.6 2H21.4C21.7314 2 22 2.26863 22 2.6V15.5M2 15.5V17.4C2 17.7314 2.26863 18 2.6 18H21.4C21.7314 18 22 17.7314 22 17.4V15.5M2 15.5H22M9 22H10.5M10.5 22V18M10.5 22H13.5M13.5 22H15M13.5 22L13.5 18"
                                                        stroke="CurrentColor" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            </span>
                                            <select id="device_id" name="device_id" class="form-select filter-input"></select>
                                        </div>
                                    </div>
                                </div>
                                <!-- Reply To -->
                                <div class="amg-form-field row align-items-center mb-3 amg-form-field-row d-none">
                                    <div class="col-md-3 text-start text-md-end">
                                        <label for="ac_email_id" class="form-label fw-normal mb-0">{{ __('ticket.create_ticket.reply_to') }}</label>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="input-group ticket-input-group">
                                            <span class="input-group-text">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-reply" viewBox="0 0 16 16">
                                                    <path
                                                        d="M6.598 5.013a.144.144 0 0 1 .202.134V6.3a.5.5 0 0 0 .5.5c.667 0 2.013.005 3.3.822.984.624 1.99 1.76 2.595 3.876-1.02-.983-2.185-1.516-3.205-1.799a8.7 8.7 0 0 0-1.921-.306 7 7 0 0 0-.798.008h-.013l-.005.001h-.001L7.3 9.9l-.05-.498a.5.5 0 0 0-.45.498v1.153c0 .108-.11.176-.202.134L2.614 8.254l-.042-.028a.147.147 0 0 1 0-.252l.042-.028zM7.8 10.386q.103 0 .223.006c.434.02 1.034.086 1.7.271 1.326.368 2.896 1.202 3.94 3.08a.5.5 0 0 0 .933-.305c-.464-3.71-1.886-5.662-3.46-6.66-1.245-.79-2.527-.942-3.336-.971v-.66a1.144 1.144 0 0 0-1.767-.96l-3.994 2.94a1.147 1.147 0 0 0 0 1.946l3.994 2.94a1.144 1.144 0 0 0 1.767-.96z" />
                                                </svg>
                                            </span>
                                            <select id="ac_email_id" name="ac_email_id" class="form-select filter-input">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!-- Created Via -->
                                <div class="amg-form-field row align-items-center mb-3 amg-form-field-row d-none">
                                    <div class="col-md-3 text-start text-md-end">
                                        <label for="created_via" class="form-label fw-normal mb-0">{{ __('ticket.create_ticket.created_via') }}</label>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="input-group ticket-input-group">
                                            <span class="input-group-text">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
                                                    <path
                                                        d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0" />
                                                    <path
                                                        d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z" />
                                                </svg>
                                            </span>
                                            <select name="created_via" id="created_via"
                                                class="form-select filter-input">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!-- Subject -->
                                <div class="amg-form-field row align-items-center mb-3 amg-form-field-row">
                                    <div class="col-md-3 text-start text-md-end">
                                        <label class="form-label fw-normal mb-0 required">{{ __('ticket.create_ticket.subject') }}</label>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="input-group ticket-input-group">
                                            <span class="input-group-text">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-book" viewBox="0 0 16 16">
                                                    <path
                                                        d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783" />
                                                </svg>
                                            </span>
                                            <input id="subject" name="subject" type="text"
                                                class="form-control filter-input" placeholder="{{ __('ticket.create_ticket.enter_subject') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="amg-form-field row align-items-center mb-3 d-none amg-form-field-row" id="other_location">
                                    <!-- Location -->
                                    <div class="col-md-3 text-start text-md-end">
                                        <label for="location_id" class="form-label fw-normal mb-0 required">{{ __('ticket.create_ticket.location') }}</label>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <div class="input-group ticket-input-group">
                                                    <span class="input-group-text">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                            fill="currentColor" class="bi bi-justify-left"
                                                            viewBox="0 0 16 16">
                                                            <path fill-rule="evenodd"
                                                                d="M2 12.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5m0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5m0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5" />
                                                        </svg>
                                                    </span>
                                                    <select id="location_id" name="location_id" class="form-select filter-input">
                                                    </select>
                                                </div>
                                            </div>
                                            <!--Internal Location -->
                                            <div class="col-md-6">
                                                <div class="amg-form-field row align-items-center amg-form-field-row">
                                                    <div class="col-md-3 text-start text-md-end">
                                                        <label for="" class="form-label fw-normal mb-0">{{ __('ticket.create_ticket.internal_location') }}</label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <div class="input-group ticket-input-group">
                                                            <span class="input-group-text">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                    height="16" fill="currentColor" class="bi bi-clock"
                                                                    viewBox="0 0 16 16">
                                                                    <path
                                                                        d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z" />
                                                                    <path
                                                                        d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0" />
                                                                </svg>
                                                            </span>
                                                            <select id="internal_location_id" id="internal_location_id" class="form-select filter-input">
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Seat No -->
                                <div class="amg-form-field row align-items-center mb-3 amg-form-field-row">
                                    <div class="col-md-3 text-start text-md-end">
                                        <label for="seat_no" class="form-label fw-normal mb-0">{{ __('ticket.create_ticket.seat_no') }}</label>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="input-group ticket-input-group">
                                            <span class="input-group-text">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M4 18L4 21" stroke="currentColor" stroke-width="1.5"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                    <path
                                                        d="M5 10V5C5 3.89543 5.89543 3 7 3L17 3C18.1046 3 19 3.89543 19 5V10"
                                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M19.5 10C18.1193 10 17 11.1193 17 12.5V14H7V12.5C7 11.1193 5.88071 10 4.5 10C3.11929 10 2 11.1193 2 12.5C2 13.7095 2.85888 14.7184 4 14.95V18H20V14.95C21.1411 14.7184 22 13.7095 22 12.5C22 11.1193 20.8807 10 19.5 10Z"
                                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path d="M20 18L20 21" stroke="currentColor" stroke-width="1.5"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>

                                            </span>
                                            <input type="text" id="seat_no" name="seat_no"
                                                class="form-control filter-input" placeholder="Enter Seat No">
                                        </div>
                                    </div>
                                </div>
                                <!-- Tags -->
                                <div class="amg-form-field row align-items-center mb-3 amg-form-field-row d-none">
                                    <div class="col-md-3 text-start text-md-end">
                                        <label for="tags" class="form-label fw-normal mb-0">{{ __('ticket.create_ticket.tags') }}</label>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="input-group ticket-input-group">
                                            <span class="input-group-text">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-tag" viewBox="0 0 16 16">
                                                    <path
                                                        d="M6 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m-1 0a.5.5 0 1 0-1 0 .5.5 0 0 0 1 0" />
                                                    <path
                                                        d="M2 1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 1 6.586V2a1 1 0 0 1 1-1m0 5.586 7 7L13.586 9l-7-7H2z" />
                                                </svg>
                                            </span>
                                            <select name="tags[]" id="tags" multiple="true" class="form-select filter-input">
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="amg-form-field row align-items-start mb-3 amg-form-field-row">
                                    <div class="col-md-3 text-start text-md-end">
                                        <label for="content" class="form-label fw-normal mb-0 required">
                                            {{ __('ticket.create_ticket.content') }}
                                        </label>
                                    </div>

                                    <div class="col-md-9">
                                        <div class="input-group ticket-input-group">
                                            <textarea  name="content" id="content" class="amg-summernote w-100"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <!-- Attachment -->
                                <div class="row align-items-start">
                                    <div class="col-md-3 text-start text-md-end">
                                        <label for="content" class="form-label fw-normal mb-0"> {{ __('ticket.create_ticket.attachment') }} </label>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="ticket-attachment-wrapper">
                                            <div id="attachment-dropper-cover"
                                                class="amg-uploader"
                                                data-amg-uploader
                                                data-multiple="true"
                                                data-auto-upload="true"
                                                data-max-files="10"
                                                data-max-size="10"
                                                data-record-id
                                                data-accept=".jpg,.jpeg,.png,.gif,.xls,.xlsx,.doc,.docx,.ppt,.pdf,.txt,.msg,.zip,.psd,.csv,.eml,msd"
                                                data-upload-url="{{ url('ticket/attachment/add') }}"
                                                data-remove-url="{{ url('ticket/attachment/remove') }}"
                                                data-token="{{ csrf_token() }}"
                                                data-record-input="#ticket-mdl-frm #tmp_id"
                                                data-upload-field="attachment">
                                                <input type="file" id="attachment_input" class="amg-uploader__input" multiple hidden>
                                                <!-- Dropzone -->
                                                <div id="attachment-dropper" class="amg-uploader__dropzone">
                                                    <div class="amg-uploader__message">
                                                        <span class="amg-uploader__icon-wrap">
                                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                                <path
                                                                    d="M16.5625 9.05781L9.64687 15.9734C8.76042 16.8599 7.55875 17.3581 6.30469 17.3581C5.05062 17.3581 3.84895 16.8599 2.9625 15.9734C2.07605 15.087 1.57788 13.8853 1.57788 12.6312C1.57788 11.3772 2.07605 10.1755 2.9625 9.28906L9.87812 2.37344C10.4718 1.7797 11.2765 1.44531 12.1156 1.44531C12.9547 1.44531 13.7594 1.7797 14.3531 2.37344C14.9469 2.96719 15.2812 3.77187 15.2812 4.61094C15.2812 5.45 14.9469 6.25469 14.3531 6.84844L7.43 13.764"
                                                                    stroke="black" stroke-width="1.25"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                        </span>
                                                        <span> {{ trans("content.service_ticket_fields.upload_note") }}
                                                            {{ trans("content.service_ticket_fields.or") }}
                                                            <span class="amg-uploader__hint">{{ __('ticket.create_ticket.drop_files_here') }}</span>
                                                        </span>
                                                    </div>
                                                    <button type="button" id="manual_file_trigger" name="manual_file_trigger" class="amg-btn amg-btn-outline amg-btn-sm amg-uploader__trigger">{{ __('ticket.create_ticket.add_attachment') }}</button>
                                                </div>
                                                <div class="mt-1 fs-1">
                                                    {{ trans('service_ticket.service_detail.supported_file_types') }}
                                                </div>
                                                <div class="mt-1 fs-1">
                                                    {{ trans('service_ticket.service_detail.max_files_size_message') }}
                                                </div>
                                                <!-- Preview -->
                                                <div id="attachments" class="amg-uploader__preview"></div>
                                                <!-- Error -->
                                                <div class="amg-uploader__error"></div>
                                            </div>
                                            <input type="hidden" name="form_type" id="form_type" value="0">
                                        </div>
                                    </div>
                                </div>

                                <div class="amg-form-field row align-items-center mb-3 amg-form-field-row">
                                    <div class="col-md-3 text-start text-md-end">
                                        <label for="follow_cc" class="form-label fw-normal mb-0">{{ __('ticket.create_ticket.cc_emails') }}</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input name="follow_cc" id="follow_cc" autocomplete="off" type="checkbox"
                                            value="1" class="form-check-input mt-2">
                                    </div>
                                </div>
                                <div class="amg-form-field row align-items-center mb-3 amg-form-field-row">
                                    <div class="col-md-3 text-start text-md-end">
                                        <label for="cc_emails" class="form-label fw-normal mb-0">{{ __('ticket.create_ticket.emails') }}</label>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="input-group ticket-input-group">
                                            <span class="input-group-text">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-tag" viewBox="0 0 16 16">
                                                    <path
                                                        d="M6 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m-1 0a.5.5 0 1 0-1 0 .5.5 0 0 0 1 0" />
                                                    <path
                                                        d="M2 1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 1 6.586V2a1 1 0 0 1 1-1m0 5.586 7 7L13.586 9l-7-7H2z" />
                                                </svg>
                                            </span>
                                            <select id="cc_emails" name="cc_emails" multiple="true" class="form-select filter-input">
                                                <option>{{ __('ticket.create_ticket.select_cc_emails') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- RIGHT SECTION -->
                        <div class="col-lg-3">

                           <div id="creator-info-2" class="creator-info-card tkt-rsr-card mb-3">
                                <h2 class="s1-text mb-0 fw-bold text-center">{{ __('ticket.create_ticket.ticket_raiser_info') }}</h2>
                                   <div class="avatar-wrap mt-3">
                                        <div class="avatar">
                                        <img class="profile-img" id="profile" src="{{Auth::user()->gravatar}}" alt="Profile">
                                    </div>
                                </div>
                                <div class="b3-text fw-medium text-center my-3" id="fullname">{{Auth::user()->getGuranteedNameText()}}</div>
                                <table class="details">
                                <tr>
                                    <td class="b3-text fw-medium py-1">{{ __('ticket.create_ticket.employee_code') }}</td>
                                    <td class="b3-text fw-medium colon py-1">:</td>
                                    <td class="b3-text fw-normal py-1" id="emp_code" style="color:#757575!important">{{Auth::user()->employee_num}}</td>
                                </tr>
                                <tr>
                                    <td class="b3-text fw-medium py-1">{{ __('ticket.create_ticket.designation') }}</td>
                                    <td class="b3-text fw-medium colon py-1">:</td>
                                    <td class="b3-text fw-normal py-1" id="job_title" style="color:#757575!important">{{Auth::user()->jobtitle}}</td>
                                </tr>
                                <tr>
                                    <td class="b3-text fw-medium py-1">{{ __('ticket.create_ticket.location') }}</td>
                                    <td class="b3-text fw-medium colon py-1">:</td>
                                    <td class="b3-text fw-normal py-1" id="location_name" style="color:#757575!important">{{isset(Auth::user()->location) ? Auth::user()->location->name : ''}}</td>
                                </tr>
                                 <tr>
                                    <td class="b3-text fw-medium py-1">{{ __('ticket.create_ticket.base_location') }}</td>
                                    <td class="b3-text fw-medium colon py-1">:</td>
                                        <td class="b3-text fw-normal py-1" id="base_location_name" style="color:#757575!important">{{isset(Auth::user()->baseLocation) ? Auth::user()->baseLocation->name : ''}}</td>
                                </tr>
                                <tr>
                                    <td class="b3-text fw-medium py-1">{{ __('ticket.create_ticket.mobile') }}</td>
                                    <td class="b3-text fw-medium colon py-1">:</td>
                                    <td class="b3-text fw-normal py-1" id="mobile" style="color:#757575!important">{{Auth::user()->phone}}</td>
                                </tr>
                                <tr>
                                    <td class="b3-text fw-medium py-1">{{ __('ticket.create_ticket.email') }}</td>
                                    <td class="b3-text fw-medium colon py-1">:</td>
                                    <td class="b3-text fw-normal py-1" id="email" style="color:#757575!important">{{Auth::user()->email}}</td>
                                </tr>
                                <tr>
                                    <td class="b3-text fw-medium py-1">{{ __('ticket.create_ticket.ext_user_company') }}</td>
                                    <td class="b3-text fw-medium colon py-1">:</td>
                                    <td class="b3-text fw-normal py-1" id="excompany" style="color:#757575!important"></td>
                                </tr>
                                <tr>
                                    <td class="b3-text fw-medium py-1">{{ __('ticket.create_ticket.company') }}</td>
                                    <td class="b3-text fw-medium colon py-1">:</td>
                                    <td class="b3-text fw-normal py-1" id="company" style="color:#757575!important">{{isset(Auth::user()->companyProp()->name) ? Auth::user()->companyProp()->name : ''}}</td>
                                </tr>
                                </table>
                            </div>
                            <!-- Notes -->
                            <div class="tkt-rsr-card mb-3 p-0 hide" id="description-info">
                                <div class="s1-text fw-bold p-2">{{ __('ticket.create_ticket.notes') }}</div>
                                <div class="ticket-side-body px-2 pt-1 border-top"><span id="descriptions">{{ __('ticket.create_ticket.add_ticket_notes') }}</span></div>
                                </div>
                                <div id="form-view-info" class="form-view-info media-block hide tkt-rsr-card p-1">
                                    <div class="s1-text fw-bold p-2">
                                        {{ __('ticket.create_ticket.view_form') }}
                                    </div>
                                    <div class="media-body">
                                        <p class="text-muted text-sm" id="request-form-view-url"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="amg-form-footer modal-footer d-flex justify-content-end py-4 py-0">
                        <button type="button" id="btnSubmit" style="min-width:300px"
                            class="amg-btn amg-btn-primary amg-btn-block col-md-2 amg-btn-md">
                            {{ trans("button.create") }}</button>
                        <button type="button" id="btnClear" data-bs-dismiss="modal" style="min-width:120px"
                            class="amg-btn amg-btn-secondary amg-btn-block col-md-2 amg-btn-md">
                            {{ trans("button.close") }}</button>
                    </div>
                </div>
        </form>
    </div>
</div>
@push('css')
<style>
      .kd_details_cards .arrow_back {
        padding: 4px 4px;
        height: 54px;
        width: 60px;
        position: absolute;
        right: -15px;
        bottom: -10px;
        z-index: 3;
        border-top-left-radius: 11px;
        background: #f2f2f2;
    }

    .kd_details_cards .arrow_back::after {
        width: 2.125rem;
        height: 1.125rem;
        background-color:transparent;
        content: "";
        top: -11px;
        right: 14.5px;
        border-bottom-right-radius: 20px;
        position: absolute;
        box-shadow: 0.375rem 0.375rem #f2f2f2;
    }

    .kd_details_cards .arrow_back::before {
        width: 1.125rem;
        height: 1.125rem;
        background-color: transparent;
        content: "";
        bottom: 9.5px;
        right: 59.5px;
        border-bottom-right-radius: 20px;
        position: absolute;
        box-shadow: 0.375rem 0.375rem #f2f2f2;
    }

    .kd_details_cards .card-container .card {
        position: relative;
        height: 280px;
        border-radius: 20px;
        flex-shrink: 0;
        background-size: cover;
        background-position: center;
        display: flex;
        align-items: flex-end;
        padding: 20px;
        margin-right: 4px;
    }

    .kd_details_cards .card-container .card-content {
        position: relative;
        z-index: 2;
    }

    .kd_details_cards .card-container .big-kd {
        position: absolute;
        top: 100px;
        right: -15px;
        font-size: 100px;
        font-weight: bold;
        line-height: 0.8;
        color: transparent;
        opacity: 0.8;
        transform: rotate(270deg);
        z-index: 2;
        -webkit-text-stroke: 1px white;
        font-family: 'Inter'
    }

    .kd_details_cards .card-container .title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
        color: white;
    }

    .kd_details_cards .card-container .tags {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .kd_details_cards .card-container .tag {
        border: 1px solid #F8FAFD;
        padding: 2px 10px;
        border-radius: 20px;
        color: white;
        background: transparent;
        font-size: 10px;
    }

    .kd_details_cards .card-container .arro_back {
        padding: 4px 4px;
        height: 60px;
        width: 60px;
        position: absolute;
        right: -15px;
        bottom: -10px;
        z-index: 3;
        border-top-left-radius: 20px;
        background: #F8FAFD;
    }

    .kd_details_cards .card-container .arro_back::after {
        width: 2.125rem;
        height: 2.125rem;
        background-color: transparent;
        content: "";
        top: -34px;
        right: 14.5px;
        border-bottom-right-radius: 11px;
        position: absolute;
        box-shadow: 0.375rem 0.375rem #F8FAFD;
    }

    .kd_details_cards .card-container .arro_back::before {
        width: 2.125rem;
        height: 2.125rem;
        background-color: transparent;
        content: "";
        bottom: 9.5px;
        right: 59.5px;
        border-bottom-right-radius: 11px;
        position: absolute;
        box-shadow: 0.375rem 0.375rem #F8FAFD;
    }

    .kd_details_cards .card-container::-webkit-scrollbar {
        display: none;
    }
    .kd_details_cards .card-container .col-md-3 {
        padding-bottom: 15.5px;
    }
    
    /*ticket raiser info card  */
    .tkt-rsr-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 6px 14px;
        border: 1px  solid #EEEEEE;
    }

    [data-bs-theme=dark] .tkt-rsr-card {
        background: #1D1D1D;
        border: 1px  solid #2A2A2D;
    }
    
    ..tkt-rsr-card td.colon {
        color: #6b6b6b;
        padding: 7px 10px;
        width: 14px;
    }
    .avatar-wrap {
        display: flex;
        justify-content: center;
    }
    .tkt-rsr-card  .avatar {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .tkt-rsr-card .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>
@endpush

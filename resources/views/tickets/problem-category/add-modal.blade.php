{{-- @page-meta
{
  "page_no": "PC02C-26",
  "file": "add-modal.blade.php",
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
<div class="amg-modal amg-form-modal modal fade" id="TicketProblemTypeModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form id="frm_problem_type" name="frm_problem_type" method="post" action="" class="form-horizontal w-100 amg-form-theme" enctype="multipart/form-data" onsubmit="return false;" autocomplete="off">
            @csrf
            <input type="hidden" id="id" name="id" value="">
            <div class="modal-content rounded-5">

                {{-- HEADER --}}
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title s2-text fw-semibold px-4" id="tktProblemTypeTitle">---</h3>
                    <button type="button" data-bs-dismiss="modal" class="modal-close px-4" aria-label="Close">
                        <svg class="amg-modal-close-icon" viewBox="0 0 31 31" fill="none">
                            <path d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z" fill="currentColor"/>
                        </svg>
                    </button>
                </div>

                {{-- BODY --}}
                <div class="modal-body">
                    <div class="container-fluid py-3">
                        <div class="row section-row">
                            <div class="col">
                                {{-- ROW 1: Company | Problem Category --}}
                                <div class="row my-4 px-4">
                                    <div class="col-md-6">
                                        <div class="amg-form-field d-flex align-items-center">
                                            <label for="company_id" class="form-label b1-text me-2 mb-0 required">{{ trans("problem_category.service_ticket_fields.company") ?? 'Company' }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M18.125 17.5H16.25V2.5H16.875C17.0408 2.5 17.1997 2.43415 17.3169 2.31694C17.4342 2.19973 17.5 2.04076 17.5 1.875C17.5 1.70924 17.4342 1.55027 17.3169 1.43306C17.1997 1.31585 17.0408 1.25 16.875 1.25H3.125C2.95924 1.25 2.80027 1.31585 2.68306 1.43306C2.56585 1.55027 2.5 1.70924 2.5 1.875C2.5 2.04076 2.56585 2.19973 2.68306 2.31694C2.80027 2.43415 2.95924 2.5 3.125 2.5H3.75V17.5H1.875C1.70924 17.5 1.55027 17.5658 1.43306 17.6831C1.31585 17.8003 1.25 17.9592 1.25 18.125C1.25 18.2908 1.31585 18.4497 1.43306 18.5669C1.55027 18.6842 1.70924 18.75 1.875 18.75H18.125C18.2908 18.75 18.4497 18.6842 18.5669 18.5669C18.6842 18.4497 18.75 18.2908 18.75 18.125C18.75 17.9592 18.6842 17.8003 18.5669 17.6831C18.4497 17.5658 18.2908 17.5 18.125 17.5ZM5 2.5H15V17.5H12.5V14.375C12.5 14.2092 12.4342 14.0503 12.3169 13.9331C12.1997 13.8158 12.0408 13.75 11.875 13.75H8.125C7.95924 13.75 7.80027 13.8158 7.68306 13.9331C7.56585 14.0503 7.5 14.2092 7.5 14.375V17.5H5V2.5ZM11.25 17.5H8.75V15H11.25V17.5Z" fill="currentColor"/></svg>
                                                </span>
                                                <select name="company_id" id="company_id" class="form-select select2">
                                                    <option value="">{{ trans("problem_category.service_ticket_fields.select_the_company") }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="amg-form-field d-flex align-items-center">
                                            <label for="name" class="form-label b1-text me-2 mb-0 required">{{ trans("problem_category.service_ticket_fields.problem_category") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z" fill="currentColor"/></svg>
                                                </span>
                                                <input type="text" name="name" id="name" autocomplete="off" class="form-control" placeholder="{{ trans('problem_category.service_ticket_fields.Enter_Problem_Category') }}"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- ROW 2: Department | Parent Category --}}
                                <div class="row mb-4 px-4">
                                    <div class="col-md-6">
                                        <div class="amg-form-field d-flex align-items-center">
                                            <label for="department_id" class="form-label b1-text me-2 mb-0 required">{{ trans("problem_category.service_ticket_fields.department") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z" fill="currentColor"/></svg>
                                                </span>
                                                <select class="form-select" name="department_id" id="department_id">
                                                    <option value=""></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="amg-form-field d-flex align-items-center">
                                            <label for="parent_id" class="form-label b1-text me-2 mb-0">{{ trans("problem_category.service_ticket_fields.Parent_Category") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.25 2.5H14.375V1.875C14.375 1.70924 14.3092 1.55027 14.1919 1.43306C14.0747 1.31585 13.9158 1.25 13.75 1.25C13.5842 1.25 13.4253 1.31585 13.3081 1.43306C13.1908 1.55027 13.125 1.70924 13.125 1.875V2.5H6.875V1.875C6.875 1.70924 6.80915 1.55027 6.69194 1.43306C6.57473 1.31585 6.41576 1.25 6.25 1.25C6.08424 1.25 5.92527 1.31585 5.80806 1.43306C5.69085 1.55027 5.625 1.70924 5.625 1.875V2.5H3.75C3.41848 2.5 3.10054 2.6317 2.86612 2.86612C2.6317 3.10054 2.5 3.41848 2.5 3.75V16.25C2.5 16.5815 2.6317 16.8995 2.86612 17.1339C3.10054 17.3683 3.41848 17.5 3.75 17.5H16.25C16.5815 17.5 16.8995 17.3683 17.1339 17.1339C17.3683 16.8995 17.5 16.5815 17.5 16.25V3.75C17.5 3.41848 17.3683 3.10054 17.1339 2.86612C16.8995 2.6317 16.5815 2.5 16.25 2.5ZM5.625 3.75V4.375C5.625 4.54076 5.69085 4.69973 5.80806 4.81694C5.92527 4.93415 6.08424 5 6.25 5C6.41576 5 6.57473 4.93415 6.69194 4.81694C6.80915 4.69973 6.875 4.54076 6.875 4.375V3.75H13.125V4.375C13.125 4.54076 13.1908 4.69973 13.3081 4.81694C13.4253 4.93415 13.5842 5 13.75 5C13.9158 5 14.0747 4.93415 14.1919 4.81694C14.3092 4.69973 14.375 4.54076 14.375 4.375V3.75H16.25V6.25H3.75V3.75H5.625ZM16.25 16.25H3.75V7.5H16.25V16.25Z" fill="currentColor"/></svg>
                                                </span>
                                                <select class="form-select" name="parent_id" id="parent_id">
                                                    <option value=""></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
    
                                {{-- ROW 3: Ticket Attender | Category Tag (Initial) --}}
                                <div class="row mb-4 px-4">
                                    <div class="col-md-6">
                                        <div class="amg-form-field d-flex align-items-center">
                                            <label for="ticket_attender" class="form-label b1-text me-2 mb-0">{{ trans("problem_category.service_ticket_fields.Ticket_Attender") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M10 10C11.3261 10 12.5979 9.47322 13.5355 8.53553C14.4732 7.59785 15 6.32608 15 5C15 3.67392 14.4732 2.40215 13.5355 1.46447C12.5979 0.526784 11.3261 0 10 0C8.67392 0 7.40215 0.526784 6.46447 1.46447C5.52678 2.40215 5 3.67392 5 5C5 6.32608 5.52678 7.59785 6.46447 8.53553C7.40215 9.47322 8.67392 10 10 10ZM10 1.25C10.9946 1.25 11.9484 1.64509 12.6517 2.34835C13.3549 3.05161 13.75 4.00544 13.75 5C13.75 5.99456 13.3549 6.94839 12.6517 7.65165C11.9484 8.35491 10.9946 8.75 10 8.75C9.00544 8.75 8.05161 8.35491 7.34835 7.65165C6.64509 6.94839 6.25 5.99456 6.25 5C6.25 4.00544 6.64509 3.05161 7.34835 2.34835C8.05161 1.64509 9.00544 1.25 10 1.25ZM17.5 18.75C17.5 20 16.25 20 16.25 20H3.75C3.75 20 2.5 20 2.5 18.75C2.5 17.5 3.75 13.75 10 13.75C16.25 13.75 17.5 17.5 17.5 18.75ZM16.25 18.745C16.2488 18.4375 16.0575 17.5138 15.2138 16.67C14.4025 15.8588 12.8713 15 10 15C7.1275 15 5.5975 15.8588 4.785 16.67C3.9425 17.5138 3.7525 18.4375 3.75 18.745H16.25Z" fill="currentColor"/></svg>
                                                </span>
                                                <select class="form-select" name="ticket_attender" id="ticket_attender">
                                                    <option value=""></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="amg-form-field d-flex align-items-center">
                                            <label for="category_tag" class="form-label b1-text me-2 mb-0">{{ trans("problem_category.service_ticket_fields.initial") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z" fill="currentColor"/></svg>
                                                </span>
                                                <input type="text" name="category_tag" id="category_tag" autocomplete="off" class="form-control" placeholder="{{ trans('problem_category.service_ticket_fields.initial') }}"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ROW 4: Auto Allocation Group | Custom Fieldset --}}
                                <div class="row mb-4 px-4">
                                    <div class="col-md-6">
                                        <div class="amg-form-field d-flex align-items-center">
                                            <label for="auto_allocation_group" class="form-label b1-text me-2 mb-0">{{ trans("problem_category.service_ticket_fields.auto_allocation_group") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z" fill="currentColor"/></svg>
                                                </span>
                                                <select class="form-select" name="auto_allocation_group[]" id="auto_allocation_group" multiple></select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="amg-form-field d-flex align-items-center">
                                            <label for="custom_fieldset" class="form-label b1-text me-2 mb-0">{{ trans("problem_category.service_ticket_fields.custom_fieldset") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z" fill="currentColor"/></svg>
                                                </span>
                                                <select class="form-select" name="custom_fieldset[]" id="custom_fieldset" multiple></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ROW 5: Priority | TAT (hrs) --}}
                                <div class="row mb-4 px-4">
                                    <div class="col-md-6">
                                        <div class="amg-form-field d-flex align-items-center">
                                            <label for="priority_id" class="form-label b1-text me-2 mb-0 required">{{ trans("problem_category.service_ticket_fields.Priority") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z" fill="currentColor"/></svg>
                                                </span>
                                                <select class="form-select" name="priority_id" id="priority_id">
                                                    <option value=""></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="amg-form-field d-flex align-items-center">
                                            <label for="tat" class="form-label b1-text me-2 mb-0">{{ trans("problem_category.service_ticket_fields.TAT_h") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M10 1.25C8.26942 1.25 6.57769 1.76318 5.13876 2.72464C3.69983 3.6861 2.57832 5.05267 1.91606 6.65152C1.25379 8.25037 1.08051 10.0097 1.41815 11.707C1.75579 13.4044 2.58911 14.9635 3.81282 16.1872C5.03653 17.4109 6.59563 18.2442 8.29296 18.5819C9.9903 18.9195 11.7496 18.7462 13.3485 18.0839C14.9473 17.4217 16.3139 16.3002 17.2754 14.8612C18.2368 13.4223 18.75 11.7306 18.75 10C18.7476 7.67936 17.8248 5.45466 16.1845 3.81449C14.5443 2.17431 12.3196 1.25243 10 1.25ZM10 17.5C8.51664 17.5 7.0666 17.0601 5.83323 16.236C4.59986 15.4119 3.63856 14.2406 3.07091 12.8701C2.50325 11.4997 2.35472 9.99168 2.64411 8.53682C2.9335 7.08197 3.64781 5.74559 4.6967 4.6967C5.7456 3.64781 7.08197 2.9335 8.53683 2.64411C9.99168 2.35472 11.4997 2.50325 12.8701 3.07091C14.2406 3.63856 15.4119 4.59986 16.236 5.83323C17.0601 7.0666 17.5 8.51664 17.5 10C17.4977 11.9885 16.7067 13.8948 15.3008 15.3008C13.8948 16.7067 11.9885 17.4977 10 17.5ZM10.625 9.74125V5.625C10.625 5.45924 10.5592 5.30027 10.4419 5.18306C10.3247 5.06585 10.1658 5 10 5C9.83425 5 9.67528 5.06585 9.55807 5.18306C9.44086 5.30027 9.375 5.45924 9.375 5.625V10C9.37503 10.1658 9.4409 10.3247 9.55813 10.4419L12.0581 12.9419C12.1761 13.0558 12.3339 13.1189 12.4978 13.1175C12.6617 13.116 12.8183 13.0501 12.9344 12.9341C13.0504 12.818 13.1163 12.6614 13.1177 12.4975C13.1192 12.3336 13.0561 12.1758 12.9422 12.0578L10.625 9.74125Z" fill="currentColor"/></svg>
                                                </span>
                                                <input type="text" name="tat" id="tat" autocomplete="off" class="form-control" placeholder="{{ trans('problem_category.service_ticket_fields.Enter_TAT_Hrs') }}"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ROW 6: Response SLA | Workaround SLA --}}
                                <div class="row mb-4 px-4">
                                    <div class="col-md-6">
                                        <div class="amg-form-field d-flex align-items-center">
                                            <label for="response_sla" class="form-label b1-text me-2 mb-0">{{ trans("problem_category.service_ticket_fields.Response_sla") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z" fill="currentColor"/></svg>
                                                </span>
                                                <input type="text" name="response_sla" id="response_sla" autocomplete="off" class="form-control" placeholder="{{ trans('problem_category.service_ticket_fields.Enter_Response_sla') }}"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="amg-form-field d-flex align-items-center">
                                            <label for="workaround_sla" class="form-label b1-text me-2 mb-0">{{ trans("problem_category.service_ticket_fields.Workaround_sla") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z" fill="currentColor"/></svg>
                                                </span>
                                                <input type="text" name="workaround_sla" id="workaround_sla" autocomplete="off" class="form-control" placeholder="{{ trans('problem_category.service_ticket_fields.Enter_Workaround_sla') }}"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ROW 7: Status | Approval Required --}}
                                <div class="row mb-4 px-4">
                                    <div class="col-md-6">
                                        <div class="amg-form-field d-flex align-items-center">
                                            <label for="status" class="form-label b1-text me-2 mb-0">{{ trans("problem_category.service_ticket_fields.th_status") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z" fill="currentColor"/></svg>
                                                </span>
                                                <select name="status" id="status" class="form-select">
                                                    <option value="1">Enable</option>
                                                    <option value="0">Disable</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="amg-form-field d-flex align-items-center">
                                            <label for="approval_required" class="form-label b1-text me-2 mb-0 required">{{ trans("problem_category.service_ticket_fields.Authority_Approval") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z" fill="currentColor"/></svg>
                                                </span>
                                                <select name="approval_required" id="approval_required" class="form-select">
                                                    <option value="1">{{ trans("problem_category.service_ticket_fields.Required") }}</option>
                                                    <option value="0" selected>{{ trans("problem_category.service_ticket_fields.Not_Required") }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ROW 8: Close After Days | Reopen Until Days --}}
                                <div class="row mb-4 px-4">
                                    <div class="col-md-6">
                                        <div class="amg-form-field d-flex align-items-center">
                                            <label for="close_ticket_after_days" class="form-label b1-text me-2 mb-0">{{ trans('problem_category.service_ticket_fields.close_ticket_after_days') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z" fill="currentColor"/></svg>
                                                </span>
                                                <input type="text" name="close_ticket_after_days" id="close_ticket_after_days" autocomplete="off" class="form-control" placeholder="{{ trans('problem_category.service_ticket_fields.close_ticket_after_days') }}"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="amg-form-field d-flex align-items-center">
                                            <label for="reopen_ticket_until_days" class="form-label b1-text me-2 mb-0">{{ trans('problem_category.service_ticket_fields.reopen_ticket_until_days') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z" fill="currentColor"/></svg>
                                                </span>
                                                <input type="text" name="reopen_ticket_until_days" id="reopen_ticket_until_days" autocomplete="off" class="form-control" placeholder="{{ trans('problem_category.service_ticket_fields.reopen_ticket_until_days') }}"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                {{-- ROW 9: Auto Resolve (checkbox) | PAB Authority Board --}}
                                <div class="row mb-4 px-4">
                                   

                                    @if(in_array(config('app.client'), ["grdemo", "ril", "rolepermission"]))
                                        <div class="col-md-6">
                                            <div class="amg-form-field d-flex align-items-center">
                                                <label for="role_id" class="form-label b1-text me-2 mb-0">{{ trans("problem_category.service_ticket_fields.Roles") }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z" fill="currentColor"/></svg>
                                                    </span>
                                                    <select name="role_id[]" id="role_id" class="form-select" multiple></select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                               
                                    <div class="col-md-6 parentcover">
                                        <div class="amg-form-field d-flex align-items-center">
                                            <label for="pab_id" class="form-label b1-text me-2 mb-0 required">{{ trans("content.service_ticket_fields.Authority_Board_SRAT") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z" fill="currentColor"/></svg>
                                                </span>
                                                        <select name="pab_id[]" id="pab_id" class="form-select" multiple>
                                                            @foreach($pabs as $pab)
                                                                <option value="{{ $pab['id'] }}" data-pab="{{$pab['hierarchy_approval']}}">{{ $pab['name'] }}</option>
                                                            @endforeach
                                                        </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ROW 10: Is Form Required | Form Name (conditional) --}}
                                <div class="row mb-4 px-4">
                                    <div class="col-md-6 parentcover">
                                        <div class="amg-form-field d-flex align-items-center">
                                            <label for="is_form_required" class="form-label b1-text me-2 mb-0">{{ trans("problem_category.service_ticket_fields.form_required") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z" fill="currentColor"/></svg>
                                                </span>
                                                <select name="is_form_required" id="is_form_required" class="form-select">
                                                    <option value="1">{{ trans("problem_category.service_ticket_fields.Required") }}</option>
                                                    <option value="0" selected>{{ trans("problem_category.service_ticket_fields.Not_Required") }}</option>
                                                    @if(in_array(config('app.client'), ["rolepermission", "grdemo", "ltts","ltsct"]))
                                                        <option value="2">{{ trans("problem_category.service_ticket_fields.required_custom_form") }}</option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 parentcover hide" id="form_id_wrapper">
                                        <div class="amg-form-field d-flex align-items-center">
                                            <label for="form_id" class="form-label b1-text me-2 mb-0">{{ trans("problem_category.service_ticket_fields.form_name") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z" fill="currentColor"/></svg>
                                                </span>
                                                <select class="form-select" name="form_id" id="form_id">
                                                    <option value=""></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- CLIENT-SPECIFIC: Privilege Access (ltts, grdemo, rolepermission) --}}
                                @if(in_array(config('app.client'), ["ltts", "grdemo", "rolepermission"]))
                                <div class="row mb-4 px-4">
                                    <div class="col-md-6">
                                        <div class="amg-form-field d-flex align-items-center">
                                            <label for="privilege_access" class="form-label b1-text me-2 mb-0">{{ trans("problem_category.service_ticket_fields.privilege_access") }}</label>
                                           <div class="input-group align-self-center">
                                             <div class="form-check ms-5">
                                                <input type="checkbox" class="form-check-input checkall" id="privilege_access" name="privilege_access">
                                            </div>
                                           </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 hide" id="no_of_approval_days">
                                        <div class="amg-form-field d-flex align-items-center">
                                            <label for="number_of_days" class="form-label b1-text me-2 mb-0">{{ trans("problem_category.service_ticket_fields.number_of_days") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z" fill="currentColor"/></svg>
                                                </span>
                                                <input type="text" name="number_of_days" id="number_of_days" autocomplete="off" class="form-control" placeholder="{{ trans('problem_category.service_ticket_fields.number_of_days') }}"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                {{-- ROW 11: Remarks / Notes (full width) --}}
                                 <div class="row mb-4 px-4">
                                    <div class="col-md-6">
                                        <div class="amg-form-field d-flex align-items-center">
                                            <label for="auto_resolve_ticket" class="form-label b1-text me-2 mb-0">{{ trans('problem_category.service_ticket_fields.auto_resolve_ticket') }}</label>
                                            <div class="input-group align-self-center">
                                                <div class="form-check ms-5">
                                                    <input type="checkbox" class="form-check-input " id="auto_resolve_ticket" name="auto_resolve_ticket" value="1">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4 px-4">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-start">
                                            <label for="remarks" class="form-label b1-text me-2 mb-0 pt-2">{{ trans("problem_category.service_ticket_fields.notes") }}</label>
                                            <div class="input-group">
                                                <textarea name="remarks" class="amg-summernote form-control" id="remarks" rows="4" placeholder="{{ trans('problem_category.service_ticket_fields.notes') }}"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="amg-form-footer modal-footer d-flex justify-content-end row py-4">
                    <button type="button" id="btnClose" class="amg-btn amg-btn-secondary amg-btn-md mb-2 ms-2 col-md-2" data-bs-dismiss="modal">{{ trans("problem_category.service_ticket_fields.Close") }}</button>
                    {{-- <button type="button" id="btnClr" class="amg-btn amg-btn-ghost s2-text col-md-2 amg-btn-md mb-2">{{ trans("problem_category.service_ticket_fields.Clear") }}</button> --}}
                    <button type="button" id="btnSubmit" class="amg-btn amg-btn-primary col-md-2 amg-btn-block amg-btn-md mb-2 action">{{ trans("problem_category.service_ticket_fields.Save") }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
<style>
    .note-editor{
        width: 100%
    }
</style>

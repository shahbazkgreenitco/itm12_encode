{{--
/**
* ------------------------------------------------------------
* File: consumable-modal.blade.php
* Module: Consumables
* CON/26/06
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #CON-09
* Created On: 2026-01-06
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version
* [1.0.1] - Changes for the clone image issue and image div changes for showing error properly
* [1.0.2] - Changes for the custom field changes
* ------------------------------------------------------------
*/
--}}

<div id="consumablemodal" class="amg-modal modal fade user-mdl-box amg-form-modal" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form id="ConsumableForm" name="ConsumableForm" method="post" action="" class="form-horizontal w-100">
            <input type="hidden" name="clone_img" id="clone_img" class="hidden" value="" />
            <input type="hidden" name="delete_img" id="delete_img" class="hidden" value="" />
            <input type="hidden" name="id" id="id" class="hidden" value="" />
            {{ csrf_field() }}
            <div class="modal-content rounded-5">
                {{-- Modal Header --}}
                <div class="modal-header">
                    <h3 class="modal-title px-4">
                        <span id="modalHead">--</span> {{ trans("consumables.modal.consumable") }}
                    </h3>
                    <button type="button" class="modal-close px-4" data-bs-dismiss="modal" aria-label="Close">
                        <svg style="height: 27px; width: 27px; min-width: 27px; flex-shrink: 0;" viewBox="0 0 31 31"
                            fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z"
                                fill="currentColor" />
                        </svg>
                    </button>
                </div>
                
                {{-- Modal Body --}}
                <div class="modal-body py-0 back-gray no-side-pad">
                    <div id="consumableTabs" class="nav-tabs-custom tab-base">
                        {{-- Tab Navigation --}}
                        <div class="tab-bar">
                            <ul class="nav nav-underline border-0 border-bottom" role="tablist">
                                <li class="nav-item border-0">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#consumable-mdl-basic-info">Basic Info</a>
                                </li>
                                <li class="nav-item cp-bsd-view" id="userPermission">
                                    <a class="nav-link" data-bs-toggle="tab" href="#consumable-mdl-settings">Settings</a>
                                </li>
                            </ul>
                        </div>
                        
                        {{-- Tab Content --}}
                        <div class="tab-content py-4">
                            <!-- Basic Info Tab -->
                            <div class="tab-pane fade show active" id="consumable-mdl-basic-info" role="tabpanel">
                                <div class="row g-4 px-4">
                                    <div class="col-md-6 amg-form-field-row">
                                        <label for="unique_tag" class="form-label b1-text fw-bold mb-2">Consumable Tag</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                    <path
                                                        d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                        fill="black" />
                                                </svg>
                                            </span>
                                            <input type="text" name="unique_tag" id="unique_tag" autocomplete="off" class="form-control" placeholder="Enter The Consumable Tag(Unique Tag)" />
                                        </div>
                                    </div>
                                    <div class="col-md-6 amg-form-field-row">
                                        <label for="company_id" class="form-label b1-text fw-bold mb-2 mandatory">{{ trans("consumables.modal.company") }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M18.125 17.5H16.25V2.5H16.875C17.0408 2.5 17.1997 2.43415 17.3169 2.31694C17.4342 2.19973 17.5 2.04076 17.5 1.875C17.5 1.70924 17.4342 1.55027 17.3169 1.43306C17.1997 1.31585 17.0408 1.25 16.875 1.25H3.125C2.95924 1.25 2.80027 1.31585 2.68306 1.43306C2.56585 1.55027 2.5 1.70924 2.5 1.875C2.5 2.04076 2.56585 2.19973 2.68306 2.31694C2.80027 2.43415 2.95924 2.5 3.125 2.5H3.75V17.5H1.875C1.70924 17.5 1.55027 17.5658 1.43306 17.6831C1.31585 17.8003 1.25 17.9592 1.25 18.125C1.25 18.2908 1.31585 18.4497 1.43306 18.5669C1.55027 18.6842 1.70924 18.75 1.875 18.75H18.125C18.2908 18.75 18.4497 18.6842 18.5669 18.5669C18.6842 18.4497 18.75 18.2908 18.75 18.125C18.75 17.9592 18.6842 17.8003 18.5669 17.6831C18.4497 17.5658 18.2908 17.5 18.125 17.5ZM5 2.5H15V17.5H12.5V14.375C12.5 14.2092 12.4342 14.0503 12.3169 13.9331C12.1997 13.8158 12.0408 13.75 11.875 13.75H8.125C7.95924 13.75 7.80027 13.8158 7.68306 13.9331C7.56585 14.0503 7.5 14.2092 7.5 14.375V17.5H5V2.5ZM11.25 17.5H8.75V15H11.25V17.5ZM6.875 5C6.875 4.83424 6.94085 4.67527 7.05806 4.55806C7.17527 4.44085 7.33424 4.375 7.5 4.375H8.75C8.91576 4.375 9.07473 4.44085 9.19194 4.55806C9.30915 4.67527 9.375 4.83424 9.375 5C9.375 5.16576 9.30915 5.32473 9.19194 5.44194C9.07473 5.55915 8.91576 5.625 8.75 5.625H7.5C7.33424 5.625 7.17527 5.55915 7.05806 5.44194C6.94085 5.32473 6.875 5.16576 6.875 5ZM10.625 5C10.625 4.83424 10.6908 4.67527 10.8081 4.55806C10.9253 4.44085 11.0842 4.375 11.25 4.375H12.5C12.6658 4.375 12.8247 4.44085 12.9419 4.55806C13.0592 4.67527 13.125 4.83424 13.125 5C13.125 5.16576 13.0592 5.32473 12.9419 5.44194C12.8247 5.55915 12.6658 5.625 12.5 5.625H11.25C11.0842 5.625 10.9253 5.55915 10.8081 5.44194C10.6908 5.32473 10.625 5.16576 10.625 5ZM6.875 8.125C6.875 7.95924 6.94085 7.80027 7.05806 7.68306C7.17527 7.56585 7.33424 7.5 7.5 7.5H8.75C8.91576 7.5 9.07473 7.56585 9.19194 7.68306C9.30915 7.80027 9.375 7.95924 9.375 8.125C9.375 8.29076 9.30915 8.44973 9.19194 8.56694C9.07473 8.68415 8.91576 8.75 8.75 8.75H7.5C7.33424 8.75 7.17527 8.68415 7.05806 8.56694C6.94085 8.44973 6.875 8.29076 6.875 8.125ZM10.625 8.125C10.625 7.95924 10.6908 7.80027 10.8081 7.68306C10.9253 7.56585 11.0842 7.5 11.25 7.5H12.5C12.6658 7.5 12.8247 7.56585 12.9419 7.68306C13.0592 7.80027 13.125 7.95924 13.125 8.125C13.125 8.29076 13.0592 8.44973 12.9419 8.56694C12.8247 8.68415 12.6658 8.75 12.5 8.75H11.25C11.0842 8.75 10.9253 8.68415 10.8081 8.56694C10.6908 8.44973 10.625 8.29076 10.625 8.125ZM6.875 11.25C6.875 11.0842 6.94085 10.9253 7.05806 10.8081C7.17527 10.6908 7.33424 10.625 7.5 10.625H8.75C8.91576 10.625 9.07473 10.6908 9.19194 10.8081C9.30915 10.9253 9.375 11.0842 9.375 11.25C9.375 11.4158 9.30915 11.5747 9.19194 11.6919C9.07473 11.8092 8.91576 11.875 8.75 11.875H7.5C7.33424 11.875 7.17527 11.8092 7.05806 11.6919C6.94085 11.5747 6.875 11.4158 6.875 11.25ZM10.625 11.25C10.625 11.0842 10.6908 10.9253 10.8081 10.8081C10.9253 10.6908 11.0842 10.625 11.25 10.625H12.5C12.6658 10.625 12.8247 10.6908 12.9419 10.8081C13.0592 10.9253 13.125 11.0842 13.125 11.25C13.125 11.4158 13.0592 11.5747 12.9419 11.6919C12.8247 11.8092 12.6658 11.875 12.5 11.875H11.25C11.0842 11.875 10.9253 11.8092 10.8081 11.6919C10.6908 11.5747 10.625 11.4158 10.625 11.25Z" fill="black" /></svg>
                                            </span>
                                            <select name="company_id" id="company_id" class="form-control">
                                                <option value="">{{ trans("consumables.modal.select_company") }}</option>
                                                {{-- @foreach($companies as $company)
                                                    <option value="{{ $company['id'] }}">{{ $company['text'] }}</option>
                                                @endforeach --}}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 amg-form-field-row">
                                        <label for="name" class="form-label b1-text fw-bold mb-2 mandatory">{{ trans("consumables.modal.consumable_name") }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                    <path
                                                        d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                        fill="black" />
                                                </svg>
                                            </span>
                                            <input type="text" id="name" name="name" class="form-control" placeholder="{{ trans('consumables.modal.enter_consumable_name') }}" />
                                        </div>
                                    </div>
                                    <div class="col-md-6 amg-form-field-row">
                                        <label for="category_id" class="form-label b1-text fw-bold mb-2 mandatory">{{ trans("consumables.modal.category") }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-grid"></i>
                                            </span>
                                            <select name="category_id" id="category_id" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 amg-form-field-row">
                                        <label for="manufacturer_id" class="form-label b1-text fw-bold mb-2">{{ trans("consumables.modal.manufacturer") }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-tools"></i>
                                            </span>
                                            <select name="manufacturer_id" id="manufacturer_id" class="form-control select2">
                                                <option value="">{{ trans("consumables.modal.select_manufacturer") }}</option>
                                                {{-- @foreach(App\Models\Manufacture::all() as $Manufac)
                                                    <option value="{{ $Manufac->id }}">{{ $Manufac->name }}</option>
                                                @endforeach --}}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 amg-form-field-row">
                                        <label for="department_id" class="form-label b1-text fw-bold mb-2">{{ trans("consumables.modal.department") }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-diagram-3"></i>
                                            </span>
                                            <select name="department_id" id="department_id" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 amg-form-field-row">
                                        <label for="location_id" class="form-label b1-text fw-bold mb-2 mandatory">{{ trans("consumables.modal.location") }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-geo-alt"></i>
                                            </span>
                                            <select name="location_id" id="location_id" class="form-control select2"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 amg-form-field-row">
                                        <label for="internal_place" class="form-label b1-text fw-bold mb-2">{{ trans("consumables.modal.internal_place") }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-pin-map"></i>
                                            </span>
                                            <select name="internal_place" id="internal_place" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 amg-form-field-row">
                                        <label for="supplier_id" class="form-label b1-text fw-bold mb-2">{{ trans("consumables.modal.supplier") }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-truck"></i>
                                            </span>
                                            <select name="supplier_id" id="supplier_id" class="form-control" placeholder="{{ trans('consumables.modal.select_supplier') }}"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 amg-form-field-row">
                                        <label for="order_number" class="form-label b1-text fw-bold mb-2">{{ trans("consumables.modal.order_no") }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-receipt"></i>
                                            </span>
                                            <input type="text" id="order_number" name="order_number" class="form-control" placeholder="{{ trans('consumables.modal.enter_order_number') }}" />
                                        </div>
                                    </div>
                                    <div class="col-md-6 amg-form-field-row">
                                        <label for="invoice_id" class="form-label b1-text fw-bold mb-2">{{ trans("consumables.modal.purchase_reference") }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </span>
                                            <select name="invoice_id" id="invoice_id" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 amg-form-field-row">
                                        <label for="purchase_date" class="form-label b1-text fw-bold mb-2">{{ trans("consumables.modal.purchase_date") }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <svg  width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.25 2.5H14.375V1.875C14.375 1.70924 14.3092 1.55027 14.1919 1.43306C14.0747 1.31585 13.9158 1.25 13.75 1.25C13.5842 1.25 13.4253 1.31585 13.3081 1.43306C13.1908 1.55027 13.125 1.70924 13.125 1.875V2.5H6.875V1.875C6.875 1.70924 6.80915 1.55027 6.69194 1.43306C6.57473 1.31585 6.41576 1.25 6.25 1.25C6.08424 1.25 5.92527 1.31585 5.80806 1.43306C5.69085 1.55027 5.625 1.70924 5.625 1.875V2.5H3.75C3.41848 2.5 3.10054 2.6317 2.86612 2.86612C2.6317 3.10054 2.5 3.41848 2.5 3.75V16.25C2.5 16.5815 2.6317 16.8995 2.86612 17.1339C3.10054 17.3683 3.41848 17.5 3.75 17.5H16.25C16.5815 17.5 16.8995 17.3683 17.1339 17.1339C17.3683 16.8995 17.5 16.5815 17.5 16.25V3.75C17.5 3.41848 17.3683 3.10054 17.1339 2.86612C16.8995 2.6317 16.5815 2.5 16.25 2.5ZM5.625 3.75V4.375C5.625 4.54076 5.69085 4.69973 5.80806 4.81694C5.92527 4.93415 6.08424 5 6.25 5C6.41576 5 6.57473 4.93415 6.69194 4.81694C6.80915 4.69973 6.875 4.54076 6.875 4.375V3.75H13.125V4.375C13.125 4.54076 13.1908 4.69973 13.3081 4.81694C13.4253 4.93415 13.5842 5 13.75 5C13.9158 5 14.0747 4.93415 14.1919 4.81694C14.3092 4.69973 14.375 4.54076 14.375 4.375V3.75H16.25V6.25H3.75V3.75H5.625ZM16.25 16.25H3.75V7.5H16.25V16.25Z" fill="black" /></svg>
                                            </span>
                                            <input type="text" id="purchase_date" name="purchase_date" class="form-control datepicker" data-date-format="dd-mm-yyyy" placeholder="{{ trans('consumables.modal.choose_purchase_date') }}" />
                                        </div>
                                    </div>
                                    <div class="col-md-6 amg-form-field-row">
                                        <label for="currency" class="form-label b1-text fw-bold mb-2">{{ trans("consumables.modal.currency") }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-currency-exchange"></i>
                                            </span>
                                            <select name="currency" id="currency" class="form-control select2">
                                                <option value="">Select Currency</option>
                                                @foreach(App\Models\Currency::getCurrencies() as $key => $value)
                                                    <option value="{{ $key }}">{!! $value['name'] . " (" . $value['symbol_html'] . ")" !!}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 amg-form-field-row">
                                        <label for="purchase_cost" class="form-label b1-text fw-bold mb-2">{{ trans("consumables.modal.purchase_cost") }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-cash-coin"></i>
                                            </span>
                                            <input type="text" id="purchase_cost" name="purchase_cost" class="form-control" placeholder="{{ trans('consumables.modal.enter_purchase_cost') }}" value="0.00" />
                                        </div>
                                    </div>
                                    <div class="col-md-6 amg-form-field-row">
                                        <label for="unit" class="form-label b1-text fw-bold mb-2">{{ trans("consumables.modal.unit") }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-rulers"></i>
                                            </span>
                                            <select name="unit" id="unit" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 amg-form-field-row">
                                        <label for="qty" class="form-label b1-text fw-bold mb-2 mandatory">{{ trans("consumables.modal.quantity") }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-123"></i>
                                            </span>
                                            <input type="text" id="qty" name="qty" class="form-control" placeholder="{{ trans('consumables.modal.enter_quantity_of_consumable') }}" />
                                        </div>
                                    </div>
                                    <div class="custom-fields-follow"></div>
                                    <div class="col-md-12 amg-form-field-row">
                                        <label for="notes" class="form-label b1-text fw-bold mb-2">{{ trans("consumables.modal.notes") }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                    <path
                                                        d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                        fill="black" />
                                                </svg>
                                            </span>
                                            <textarea name="notes" id="notes" class="form-control" placeholder="Notes"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12 amg-form-field-row">
                                        <input type="checkbox" name="requestable_consumables" id="requestable_consumables" class="form-check-input" value="1">
                                        <label for="requestable_consumables" class="form-check-label b1-text">
                                            {{ trans("consumables.modal.requestable_consumables") }}
                                        </label>
                                    </div>
                                    <div class="col-md-12 amg-form-field-row">
                                        <div id="preview-image" class="col-md-12" style="display:none;">
                                            <label class="form-label b1-text fw-bold mb-2">
                                                {{ trans('consumables.modal.preview_image') }}
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <path
                                                            d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                            fill="black" />
                                                    </svg>
                                                </span>
                                                <ul class="list-group" id="consumables-image-list"></ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 amg-form-field-row">
                                        <label for="image" class="form-label b1-text fw-bold mb-2">{{ trans("consumables.modal.image") }}</label>
                                        <div class="input-group">
                                            <input type="file" name="image" id="image" class="form-control" />
                                        </div>
                                        <div class="form-text small">{{trans('consumables.modal.file_allowed')}}</div>
                                    </div>
                                    <div class="col-md-12 imgviewcover" style="display:none;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="delete_img" id="delete_img" value="1">
                                            <label class="form-check-label" for="delete_img">
                                                {{ trans("consumables.modal.delete_present_image") }}
                                            </label>
                                        </div>
                                        <img id="imgview" src="" alt="Image Preview" class="img-thumbnail mt-2" style="max-width: 100px;">
                                    </div>
                                </div>
                            </div>

                            <!-- Settings Tab -->
                            <div class="tab-pane fade cp-bsd-view" id="consumable-mdl-settings" role="tabpanel">
                                <div class="row g-4 px-4">
                                    <div class="col-md-12 amg-form-field-row">
                                        <label for="consumable_thresholds" class="form-label b1-text fw-bold mb-2">Threshold Limit</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                    <path
                                                        d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                        fill="black" />
                                                </svg>
                                            </span>
                                            <input type="number" autocomplete="off" name="consumable_thresholds" id="consumable_thresholds" class="form-control" onblur="if(this.value === '') this.value = 0;" value="0" min="0" placeholder="{{ trans('consumables.modal.threshold_limit' ) }}" />
                                        </div>
                                    </div>
                                    <div class="col-md-12 amg-form-field-row">
                                        <label for="reorder_limits" class="form-label b1-text fw-bold mb-2">Reorder Limit</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                    <path
                                                        d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                        fill="black" />
                                                </svg>
                                            </span>
                                            <input type="number" autocomplete="off" name="reorder_limits" id="reorder_limits" class="form-control" onblur="if(this.value === '') this.value = 0;" value="0" min="0" placeholder="Reorder Limits" />
                                        </div>
                                    </div>
                                    <div class="col-md-12 amg-form-field-row">
                                        <label for="threshold_alert_users" class="form-label b1-text fw-bold mb-2">Alert To</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <svg  width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M18.0407 16.5627C16.8508 14.5056 15.0172 13.0306 12.8774 12.3314C13.9358 11.7013 14.7582 10.7412 15.2182 9.59845C15.6781 8.45573 15.7503 7.19361 15.4235 6.00592C15.0968 4.81823 14.3892 3.77064 13.4094 3.02402C12.4296 2.2774 11.2318 1.87305 10 1.87305C8.76821 1.87305 7.57044 2.2774 6.59067 3.02402C5.6109 3.77064 4.90331 4.81823 4.57654 6.00592C4.24978 7.19361 4.32193 8.45573 4.78189 9.59845C5.24186 10.7412 6.06422 11.7013 7.12268 12.3314C4.98284 13.0299 3.14925 14.5049 1.9594 16.5627C1.91577 16.6338 1.88683 16.713 1.87429 16.7955C1.86174 16.878 1.86585 16.9622 1.88638 17.0431C1.9069 17.124 1.94341 17.2 1.99377 17.2665C2.04413 17.3331 2.10731 17.3889 2.17958 17.4306C2.25185 17.4724 2.33175 17.4992 2.41457 17.5096C2.49738 17.5199 2.58143 17.5136 2.66176 17.491C2.74209 17.4683 2.81708 17.4298 2.88228 17.3777C2.94749 17.3256 3.00161 17.261 3.04143 17.1877C4.51331 14.6439 7.11487 13.1252 10 13.1252C12.8852 13.1252 15.4867 14.6439 16.9586 17.1877C16.9984 17.261 17.0526 17.3256 17.1178 17.3777C17.183 17.4298 17.258 17.4683 17.3383 17.491C17.4186 17.5136 17.5027 17.5199 17.5855 17.5096C17.6683 17.4992 17.7482 17.4724 17.8205 17.4306C17.8927 17.3889 17.9559 17.3331 18.0063 17.2665C18.0566 17.2 18.0932 17.124 18.1137 17.0431C18.1342 16.9622 18.1383 16.878 18.1258 16.7955C18.1132 16.713 18.0843 16.6338 18.0407 16.5627ZM5.62503 7.50017C5.62503 6.63488 5.88162 5.78902 6.36235 5.06955C6.84308 4.35009 7.52636 3.78933 8.32579 3.4582C9.12521 3.12707 10.0049 3.04043 10.8535 3.20924C11.7022 3.37805 12.4818 3.79473 13.0936 4.40658C13.7055 5.01843 14.1222 5.79799 14.291 6.64665C14.4598 7.49532 14.3731 8.37499 14.042 9.17441C13.7109 9.97384 13.1501 10.6571 12.4306 11.1379C11.7112 11.6186 10.8653 11.8752 10 11.8752C8.84009 11.8739 7.72801 11.4126 6.90781 10.5924C6.0876 9.77219 5.62627 8.66011 5.62503 7.50017Z" fill="black" /></svg>
                                            </span>
                                            <select multiple name="threshold_alert_users[]" id="threshold_alert_users" class="form-control"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Modal Footer --}}
                <div class="modal-footer justify-content-end mb-4 pb-4 py-0" style="padding-inline: 40px;">
                    <div class="d-flex gap-2">
                        <button type="button" class="amg-btn amg-btn-secondary" id="btnClear" data-bs-dismiss="modal">
                            {{ trans("consumables.modal.close_button") }}
                        </button>
                        <button type="submit" class="amg-btn amg-btn-primary" id="submitBtn">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

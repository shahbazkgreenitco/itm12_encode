{{--
/**
* ------------------------------------------------------------
* File: info.blade.php
* Module: Suppliers
* SUP/26/04
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #SUP-010
* Created On: 2026-04-28
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}

@extends('layouts.layout1')

@section('title', trans('suppliers.supplier_info.view.supplier_info'))

@section('content')
<main class="main-content" id="main-user-list-wrapper">
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between px-4 mb-3">
        <button onclick="location.href='{{ url('suppliers') }}'"
            class="d-flex gap-3 align-items-center bg-transparent outline-none border-0">
            <svg width="25" height="21" viewBox="0 0 25 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M11.5662 0.440759C11.706 0.580116 11.817 0.745702 11.8927 0.928029C11.9684 1.11036 12.0074 1.30584 12.0074 1.50326C12.0074 1.70068 11.9684 1.89616 11.8927 2.07848C11.817 2.26081 11.706 2.4264 11.5662 2.56576L5.12993 9.00201L23.5049 9.00201C23.9028 9.00201 24.2843 9.16004 24.5656 9.44135C24.8469 9.72265 25.0049 10.1042 25.0049 10.502C25.0049 10.8998 24.8469 11.2814 24.5656 11.5627C24.2843 11.844 23.9028 12.002 23.5049 12.002L5.12993 12.002L11.5662 18.4408C11.848 18.7226 12.0063 19.1047 12.0063 19.5033C12.0063 19.9018 11.848 20.284 11.5662 20.5658C11.2844 20.8475 10.9022 21.0059 10.5037 21.0059C10.1052 21.0059 9.72298 20.8475 9.44118 20.5658L0.441182 11.5658C0.301343 11.4264 0.190387 11.2608 0.114679 11.0785C0.0389711 10.8962 -4.50154e-07 10.7007 -4.58778e-07 10.5033C-4.67401e-07 10.3058 0.038971 10.1104 0.114679 9.92803C0.190387 9.7457 0.301342 9.58011 0.441182 9.44076L9.44118 0.440759C9.58054 0.30092 9.74613 0.189964 9.92845 0.114255C10.1108 0.0385471 10.3063 -0.000425789 10.5037 -0.000425798C10.7011 -0.000425806 10.8966 0.038547 11.0789 0.114255C11.2612 0.189964 11.4268 0.30092 11.5662 0.440759Z"
                    fill="currentColor" />
            </svg>
            <h3 class="h3-text mb-0">{{ trans('suppliers.supplier_info.view.supplier') }}</h3>
        </button>
        <div class="icon-btn-group d-flex gap-8">
            <button class="header-icon-btn-only header-icon-btn-only-sm btna-active btn-supplier-edit"
                data-id="{{ $supplier->id }}" type="button" data-bs-toggle="tooltip"
                data-original-title="{{ trans('suppliers.supplier_info.view.edit_supplier') }}"
                title="{{ trans('suppliers.supplier_info.view.edit_supplier') }}">
                <svg width="40" height="38" viewBox="9 8 22 22" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M29.3103 13.8791L25.1216 9.68946C24.9823 9.55014 24.8169 9.43962 24.6349 9.36421C24.4529 9.28881 24.2578 9.25 24.0608 9.25C23.8638 9.25 23.6687 9.28881 23.4867 9.36421C23.3047 9.43962 23.1393 9.55014 23 9.68946L11.4397 21.2507C11.2998 21.3895 11.1889 21.5547 11.1134 21.7367C11.0379 21.9188 10.9994 22.114 11 22.311V26.5007C11 26.8985 11.158 27.2801 11.4393 27.5614C11.7207 27.8427 12.1022 28.0007 12.5 28.0007H16.6897C16.8868 28.0013 17.082 27.9628 17.264 27.8873C17.446 27.8118 17.6112 27.7009 17.75 27.561L29.3103 16.0007C29.4496 15.8614 29.5602 15.696 29.6356 15.514C29.711 15.332 29.7498 15.1369 29.7498 14.9399C29.7498 14.7429 29.711 14.5478 29.6356 14.3658C29.5602 14.1838 29.4496 14.0184 29.3103 13.8791ZM16.6897 26.5007H12.5V22.311L20.75 14.061L24.9397 18.2507L16.6897 26.5007ZM26 17.1895L21.8103 13.0007L24.0603 10.7507L28.25 14.9395L26 17.1895Z"
                        fill="#7F7F7F" stroke="#7F7F7F" stroke-width="0.5" stroke-linejoin="round" />
                </svg>
            </button>
            <button class="header-icon-btn-only header-icon-btn-only-sm btna-active delete-link" type="button"
                data-bs-toggle="tooltip" title="{{ trans('suppliers.supplier_info.view.delete_supplier') }}"
                data-id="{{ $supplier->id }}">
                <svg width="40" height="38" viewBox="9 8 22 22" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M28.25 11.5H24.5V10.75C24.5 10.1533 24.2629 9.58097 23.841 9.15901C23.419 8.73705 22.8467 8.5 22.25 8.5H17.75C17.1533 8.5 16.581 8.73705 16.159 9.15901C15.7371 9.58097 15.5 10.1533 15.5 10.75V11.5H11.75C11.5511 11.5 11.3603 11.579 11.2197 11.7197C11.079 11.8603 11 12.0511 11 12.25C11 12.4489 11.079 12.6397 11.2197 12.7803C11.3603 12.921 11.5511 13 11.75 13H12.5V26.5C12.5 26.8978 12.658 27.2794 12.9393 27.5607C13.2206 27.842 13.6022 28 14 28H26C26.3978 28 26.7794 27.842 27.0607 27.5607C27.342 27.2794 27.5 26.8978 27.5 26.5V13H28.25C28.4489 13 28.6397 12.921 28.7803 12.7803C28.921 12.6397 29 12.4489 29 12.25C29 12.0511 28.921 11.8603 28.7803 11.7197C28.6397 11.579 28.4489 11.5 28.25 11.5ZM17 10.75C17 10.5511 17.079 10.3603 17.2197 10.2197C17.3603 10.079 17.5511 10 17.75 10H22.25C22.4489 10 22.6397 10.079 22.7803 10.2197C22.921 10.3603 23 10.5511 23 10.75V11.5H17V10.75ZM26 26.5H14V13H26V26.5ZM18.5 16.75V22.75C18.5 22.9489 18.421 23.1397 18.2803 23.2803C18.1397 23.421 17.9489 23.5 17.75 23.5C17.5511 23.5 17.3603 23.421 17.2197 23.2803C17.079 23.1397 17 22.9489 17 22.75V16.75C17 16.5511 17.079 16.3603 17.2197 16.2197C17.3603 16.079 17.5511 16 17.75 16C17.9489 16 18.1397 16.079 18.2803 16.2197C18.421 16.3603 18.5 16.5511 18.5 16.75ZM23 16.75V22.75C23 22.9489 22.921 23.1397 22.7803 23.2803C22.6397 23.421 22.4489 23.5 22.25 23.5C22.0511 23.5 21.8603 23.421 21.7197 23.2803C21.579 23.1397 21.5 22.9489 21.5 22.75V16.75C21.5 16.5511 21.579 16.3603 21.7197 16.2197C21.8603 16.079 22.0511 16 22.25 16C22.4489 16 22.6397 16.079 22.7803 16.2197C22.921 16.3603 23 16.5511 23 16.75Z"
                        fill="#7F7F7F" stroke="#7F7F7F" stroke-width="0.5" stroke-linejoin="round" />
                </svg>
            </button>
            {{-- <button class="header-icon-btn-only header-icon-btn-only-sm btna-active btn-add-pan" type="button"
                data-bs-toggle="tooltip" data-original-title="{{ trans('suppliers.supplier_info.view.add_pan') }}"
            title="{{ trans('suppliers.supplier_info.view.add_pan') }}" data-item_type="pan">
            <svg width="40" height="38" viewBox="10 8 20 22" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M20 17C22.2091 17 24 15.2091 24 13C24 10.7909 22.2091 9 20 9C17.7909 9 16 10.7909 16 13C16 15.2091 17.7909 17 20 17Z"
                    stroke="#7F7F7F" stroke-width="1.5" />
                <path
                    d="M28 24.5C28 26.985 28 29 20 29C12 29 12 26.985 12 24.5C12 22.015 15.582 20 20 20C24.418 20 28 22.015 28 24.5Z"
                    stroke="#7F7F7F" stroke-width="1.5" stroke-linejoin="round" />
            </svg>
            </button> --}}
            <button class="header-icon-btn-only header-icon-btn-only-sm btna-active courier_details_modal_btn"
                type="button" id="courier_details_modal_btn" data-bs-toggle="tooltip"
                data-item_type="courier_info_modal"
                data-original-title="{{ trans('suppliers.supplier_info.view.courier_charges') }}"
                title="{{ trans('suppliers.supplier_info.view.courier_charges') }}">
                <svg width="40" height="38" viewBox="9 10 22 18" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M12.616 26C12.1553 26 11.771 25.846 11.463 25.538C11.155 25.23 11.0007 24.8453 11 24.384V13.616C11 13.1553 11.1543 12.771 11.463 12.463C11.7717 12.155 12.1557 12.0007 12.615 12H27.385C27.845 12 28.229 12.1543 28.537 12.463C28.845 12.7717 28.9993 13.156 29 13.616V24.385C29 24.845 28.8457 25.2293 28.537 25.538C28.2283 25.8467 27.8443 26.0007 27.385 26H12.616ZM20 19.116L12 13.885V24.385C12 24.5643 12.0577 24.7117 12.173 24.827C12.2883 24.9423 12.436 25 12.616 25H27.385C27.5643 25 27.7117 24.9423 27.827 24.827C27.9423 24.7117 28 24.564 28 24.384V13.884L20 19.116ZM20 18L27.692 13H12.308L20 18ZM12 13.885V13V24.385C12 24.5643 12.0577 24.7117 12.173 24.827C12.2883 24.9423 12.436 25 12.616 25H12V13.885Z"
                        fill="#7F7F7F" stroke="#7F7F7F" stroke-width="0.5" stroke-linejoin="round" />
                </svg>
            </button>
            <button class="header-icon-btn-only header-icon-btn-only-sm btna-active go-add-address" type="button"
                data-bs-toggle="tooltip" data-original-title="{{ trans('suppliers.supplier_info.view.add_address') }}"
                title="{{ trans('suppliers.supplier_info.view.add_address') }}" data-id="{{ $supplier->id }}">
                <svg width="40" height="38" viewBox="8 9 24 20" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M14 17.5H22.25V19H14V17.5ZM14 21.25H23.75V22.75H14V21.25Z" fill="#7F7F7F" stroke="#7F7F7F"
                        stroke-width="0.5" />
                    <path
                        d="M29 10H11C10.6022 10 10.2206 10.158 9.93934 10.4393C9.65804 10.7206 9.5 11.1022 9.5 11.5V26.5C9.5 26.8978 9.65804 27.2794 9.93934 27.5607C10.2206 27.842 10.6022 28 11 28H29C29.3978 28 29.7794 27.842 30.0607 27.5607C30.342 27.2794 30.5 26.8978 30.5 26.5V11.5C30.5 11.1022 30.342 10.7206 30.0607 10.4393C29.7794 10.158 29.3978 10 29 10ZM29 11.5V13H11V11.5H29ZM11 26.5V14.5H29V26.5H11Z"
                        fill="#7F7F7F" stroke="#7F7F7F" stroke-width="0.5" stroke-linejoin="round" />
                </svg>
            </button>
            {{-- <button class="header-icon-btn-only header-icon-btn-only-sm btna-active go-add-account" type="button"
                data-bs-toggle="tooltip" data-original-title="{{ trans('suppliers.supplier_info.view.add_account') }}"
            title="{{ trans('suppliers.supplier_info.view.add_account') }}" data-id="{{ $supplier->id }}">
            <svg width="40" height="38" viewBox="8 9 24 20" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M14 17.5H22.25V19H14V17.5ZM14 21.25H23.75V22.75H14V21.25Z" fill="#7F7F7F"
                    stroke="#7F7F7F" stroke-width="0.5" />
                <path
                    d="M29 10H11C10.6022 10 10.2206 10.158 9.93934 10.4393C9.65804 10.7206 9.5 11.1022 9.5 11.5V26.5C9.5 26.8978 9.65804 27.2794 9.93934 27.5607C10.2206 27.842 10.6022 28 11 28H29C29.3978 28 29.7794 27.842 30.0607 27.5607C30.342 27.2794 30.5 26.8978 30.5 26.5V11.5C30.5 11.1022 30.342 10.7206 30.0607 10.4393C29.7794 10.158 29.3978 10 29 10ZM29 11.5V13H11V11.5H29ZM11 26.5V14.5H29V26.5H11Z"
                    fill="#7F7F7F" stroke="#7F7F7F" stroke-width="0.5" stroke-linejoin="round" />
            </svg>
            </button> --}}
            {{-- <button class="header-icon-btn-only header-icon-btn-only-sm btna-active btn-add-gstin" type="button"
                data-bs-toggle="tooltip" data-item_type="gstin"
                data-original-title="{{ trans('suppliers.supplier_info.view.add_gstin') }}"
            title="{{ trans('suppliers.supplier_info.view.add_gstin') }}">
            <svg width="40" height="38" viewBox="10 9 20 23" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M15.25 15.5C15.25 15.3011 15.329 15.1103 15.4697 14.9697C15.6103 14.829 15.8011 14.75 16 14.75H24C24.1989 14.75 24.3897 14.829 24.5303 14.9697C24.671 15.1103 24.75 15.3011 24.75 15.5C24.75 15.6989 24.671 15.8897 24.5303 16.0303C24.3897 16.171 24.1989 16.25 24 16.25H16C15.8011 16.25 15.6103 16.171 15.4697 16.0303C15.329 15.8897 15.25 15.6989 15.25 15.5ZM16 18.25C15.8011 18.25 15.6103 18.329 15.4697 18.4697C15.329 18.6103 15.25 18.8011 15.25 19C15.25 19.1989 15.329 19.3897 15.4697 19.5303C15.6103 19.671 15.8011 19.75 16 19.75H21C21.1989 19.75 21.3897 19.671 21.5303 19.5303C21.671 19.3897 21.75 19.1989 21.75 19C21.75 18.8011 21.671 18.6103 21.5303 18.4697C21.3897 18.329 21.1989 18.25 21 18.25H16Z"
                    fill="#7F7F7F" />
                <path
                    d="M17.945 9.75C16.578 9.75 15.475 9.75 14.608 9.867C13.708 9.987 12.95 10.247 12.348 10.848C11.746 11.45 11.488 12.208 11.367 13.108C11.25 13.975 11.25 15.078 11.25 16.445V24.555C11.25 25.922 11.25 27.025 11.367 27.892C11.487 28.792 11.747 29.55 12.348 30.152C12.95 30.754 13.708 31.012 14.608 31.134C15.475 31.25 16.578 31.25 17.945 31.25H22.055C23.422 31.25 24.525 31.25 25.392 31.134C26.292 31.012 27.05 30.754 27.652 30.152C28.254 29.55 28.512 28.792 28.634 27.892C28.75 27.025 28.75 25.922 28.75 24.555V16.445C28.75 15.078 28.75 13.975 28.634 13.108C28.512 12.208 28.254 11.45 27.652 10.848C27.05 10.246 26.292 9.988 25.392 9.867C24.525 9.75 23.422 9.75 22.055 9.75H17.945ZM13.41 11.909C13.687 11.632 14.075 11.452 14.81 11.353C15.564 11.252 16.566 11.25 18.001 11.25H22.001C23.436 11.25 24.437 11.252 25.193 11.353C25.927 11.452 26.315 11.633 26.592 11.909C26.869 12.186 27.049 12.574 27.148 13.309C27.249 14.063 27.251 15.065 27.251 16.5V23.75H15.782C14.964 23.75 14.406 23.75 13.927 23.878C13.5061 23.9918 13.108 24.1772 12.75 24.426V16.5C12.75 15.065 12.752 14.063 12.853 13.308C12.952 12.574 13.134 12.186 13.41 11.909ZM12.777 26.749C12.792 27.103 12.816 27.414 12.853 27.692C12.952 28.426 13.133 28.814 13.409 29.091C13.686 29.368 14.074 29.548 14.809 29.647C15.563 29.748 16.565 29.75 18 29.75H22C23.435 29.75 24.436 29.748 25.192 29.647C25.926 29.548 26.314 29.367 26.591 29.091C26.868 28.814 27.048 28.426 27.147 27.691C27.23 27.076 27.246 26.296 27.249 25.25H15.898C14.92 25.25 14.578 25.256 14.315 25.327C13.9625 25.4215 13.6384 25.6004 13.3704 25.8481C13.1025 26.0958 12.8988 26.405 12.777 26.749Z"
                    fill="#7F7F7F" />
            </svg>
            </button> --}}
        </div>
    </div>
    <div class="tab-bar mb-3">
        <ul class="nav nav-underline" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#basic-info-page">
                    {{ trans('suppliers.supplier_info.view.basic_info') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link licenses-tab" data-bs-toggle="tab" href="#account">
                    {{ trans('suppliers.supplier_info.view.account') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link accessory-tab" data-bs-toggle="tab" href="#pan">
                    {{ trans('suppliers.supplier_info.view.pan_numbers') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link consumable-tab" data-bs-toggle="tab" href="#address">
                    {{ trans('suppliers.supplier_info.view.address') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link components-tab" data-bs-toggle="tab" href="#gst">
                    {{ trans('suppliers.supplier_info.view.gst_ins') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link documents-tab" data-bs-toggle="tab" href="#courier">
                    {{ trans('suppliers.supplier_info.view.courier') }}
                </a>
            </li>
        </ul>
    </div>
    <div class="tab-content">
        <div class="tab-pane fade show active p-4 pt-0" id="basic-info-page">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <p class=" mb-3 small">{{ trans('suppliers.supplier_info.view.supplier_information') }}</p>
                <div class="row g-3">
                    <div class="col-xl-3 col-lg-6">
                        <div
                            class="border rounded-4 p-3 d-flex justify-content-between align-items-center bg-white h-100">
                            <div class="text-truncate">
                                <div class=" small mb-1">{{ trans('suppliers.supplier_info.view.supplier_name') }}
                                </div>
                                <div class="fw-bold text-dark">{{ $supplier->name ?? '-' }}</div>
                            </div>
                            <div class="ms-2 position-relative d-inline-block" style="width: 40px; height: 40px;">
                                <img src="{{ asset('assets/images/supplier/polygon.svg') }}" alt=""
                                    class="position-absolute top-0 start-0 w-100 h-100">

                                <img src="{{ asset('assets/images/supplier/mdi-light_phone.svg') }}" alt="Display"
                                    class="position-absolute top-50 start-50 translate-middle"
                                    style="width: 18px; height: 18px;">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6">
                        <div
                            class="border rounded-4 p-3 d-flex justify-content-between align-items-center bg-white h-100">
                            <div>
                                <div class=" small mb-1">{{ trans('suppliers.supplier_info.view.phone') }}</div>
                                <div class="fw-bold text-dark">{{ $supplier->phone ?? '-' }}</div>
                            </div>
                            <div class="ms-2 position-relative d-inline-block" style="width: 40px; height: 40px;">
                                <img src="{{ asset('assets/images/supplier/polygon.svg') }}" alt=""
                                    class="position-absolute top-0 start-0 w-100 h-100">

                                <img src="{{ asset('assets/images/supplier/mdi-light_phone.svg') }}" alt="Display"
                                    class="position-absolute top-50 start-50 translate-middle"
                                    style="width: 18px; height: 18px;">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6">
                        <div
                            class="border rounded-4 p-3 d-flex justify-content-between align-items-center bg-white h-100">
                            <div>
                                <div class=" small mb-1">{{ trans('suppliers.supplier_info.view.contact_person') }}
                                </div>
                                <div class="fw-bold text-dark">{{ $supplier->contact ?? '-' }}</div>
                            </div>
                            <div class="ms-2 position-relative d-inline-block" style="width: 40px; height: 40px;">
                                <img src="{{ asset('assets/images/supplier/polygon.svg') }}" alt=""
                                    class="position-absolute top-0 start-0 w-100 h-100">

                                <img src="{{ asset('assets/images/supplier/vector.svg') }}" alt="Display"
                                    class="position-absolute top-50 start-50 translate-middle"
                                    style="width: 18px; height: 18px;">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6">
                        <div
                            class="border rounded-4 p-3 d-flex justify-content-between align-items-center bg-white h-100">
                            <div>
                                <div class=" small mb-1">{{ trans('suppliers.supplier_info.view.email') }}</div>
                                <div class="fw-bold text-dark">{{ $supplier->email ?? '-' }}</div>
                            </div>
                            <div class="ms-2 position-relative d-inline-block" style="width: 40px; height: 40px;">
                                <img src="{{ asset('assets/images/supplier/polygon.svg') }}" alt=""
                                    class="position-absolute top-0 start-0 w-100 h-100">

                                <img src="{{ asset('assets/images/supplier/mdi-light_email.svg') }}" alt="Display"
                                    class="position-absolute top-50 start-50 translate-middle"
                                    style="width: 18px; height: 18px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <span>{{ trans('suppliers.supplier_info.view.business_categories') }}: <span
                        class="fw-bold text-dark">
                        @forelse ($supplier->businessCategories() as $category)
                        {{ $category->business_tag }}@if (!$loop->last)
                        ,
                        @endif
                        @empty
                        -
                        @endforelse
                    </span>
                </span>
            </div>
            <div class="row g-4 pt-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        @php
                        $firstAddress = $addresses->first() ?? null;
                        @endphp
                        <h5 class="mb-4 fw-bold text-dark">{{ trans('suppliers.supplier_info.view.address') }}</h5>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span
                                class="text-dark fw-medium">{{ trans('suppliers.supplier_info.view.street1') }}</span>
                            <span>{{ $firstAddress->address ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span
                                class="text-dark fw-medium">{{ trans('suppliers.supplier_info.view.street2') }}</span>
                            <span>{{ $firstAddress->address2 ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span
                                class="text-dark fw-medium">{{ trans('suppliers.supplier_info.view.country') }}</span>
                            <span>{{ $firstAddress?->supplierAdditionalCountry?->name ?: '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-dark fw-medium">{{ trans('suppliers.supplier_info.view.state') }}</span>
                            <span>{{ $firstAddress?->supplierAdditionalState?->name ?: '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-dark fw-medium">{{ trans('suppliers.supplier_info.view.city') }}</span>
                            <span>{{ $firstAddress?->supplierAdditionalCity?->name ?: '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span
                                class="text-dark fw-medium">{{ trans('suppliers.supplier_info.view.zip_postal_code') }}</span>
                            <span>{{ $firstAddress->zip ?? '-' }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    @php
                    $firstAccount = $accounts->first() ?? null;
                    @endphp
                    @php
                    $copyAccountText = trim(
                    implode("\n", [
                    trans('suppliers.supplier_info.view.account_number') .
                    ': ' .
                    ($firstAccount->bank_acc_number ?? '-'),
                    trans('suppliers.supplier_info.view.bank_name') .
                    ': ' .
                    ($firstAccount->bank_name ?? '-'),
                    trans('suppliers.supplier_info.view.ifsc_code') .
                    ': ' .
                    ($firstAccount->bank_ifsc ?? '-'),
                    trans('suppliers.supplier_info.view.branch_name') .
                    ': ' .
                    ($firstAccount->bank_branch ?? '-'),
                    trans('suppliers.supplier_info.view.account_name_as_per_bank_account') .
                    ': ' .
                    ($firstAccount->bank_acc_name ?? '-'),
                    trans('suppliers.supplier_info.view.website_url') .
                    ': ' .
                    ($firstAccount->url ?? '-'),
                    ]),
                    );
                    @endphp
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-4 fw-bold text-dark">{{ trans('suppliers.supplier_info.view.account') }}
                            </h5>
                            <button type="button" class="btn btn-sm mb-4 copy-text-basic-info"
                                data-copy-text="{{ $copyAccountText }}">
                                <i class="bi bi-copy"></i>
                            </button>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span
                                class="text-dark fw-medium">{{ trans('suppliers.supplier_info.view.account_number') }}</span>
                            <span>{{ $firstAccount->bank_acc_number ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span
                                class="text-dark fw-medium">{{ trans('suppliers.supplier_info.view.bank_name') }}</span>
                            <span
                                class="badge rounded-pill bg-success-subtle text-success px-3 py-2 fw-normal">{{ $firstAccount->bank_name ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span
                                class="text-dark fw-medium">{{ trans('suppliers.supplier_info.view.ifsc_code') }}</span>
                            <span>{{ $firstAccount->bank_ifsc ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span
                                class="text-dark fw-medium">{{ trans('suppliers.supplier_info.view.branch_name') }}</span>
                            <span>{{ $firstAccount->bank_branch ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span
                                class="text-dark fw-medium">{{ trans('suppliers.supplier_info.view.account_name_as_per_bank_account') }}</span>
                            <span>{{ $firstAccount->bank_acc_name ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span
                                class="text-dark fw-medium">{{ trans('suppliers.supplier_info.view.website_url') }}</span>
                            <span>{{ $firstAccount->url ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        @php
                        $firstPan = $pans->first() ?? null;
                        @endphp
                        @if ($firstPan)
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-4 fw-bold text-dark">
                                {{ trans('suppliers.supplier_info.view.pan_number') }}
                            </h5>
                            <button type="button" class="btn btn-sm mb-4 copy-text-basic-info"
                                data-copy-text="{{ $firstPan->item_value ?? '-' }}">
                                <i class="bi bi-copy"></i>
                            </button>
                        </div>
                        <div
                            class="border rounded-3 p-2 px-3 d-flex justify-content-between align-items-center bg-white hidden-icon-control">
                            <div class="d-flex align-items-center">
                                <span class="fw-bold text-dark">{{ $firstPan->item_value }}</span>
                            </div>
                        </div>
                        @else
                        <div class="text-center py-4 border border-dashed rounded-3 bg-light">
                            <span
                                class="small">{{ trans('suppliers.supplier_info.view.no_pan_records_available') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        @php
                        $firstGst = $gstins->first() ?? null;
                        @endphp
                        @if ($firstGst)
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-4 fw-bold text-dark">
                                {{ trans('suppliers.supplier_info.view.gst_number') }}
                            </h5>
                            <button type="button" class="btn btn-sm mb-4 copy-text-basic-info"
                                data-copy-text="{{ $firstGst->item_value ?? '-' }}">
                                <i class="bi bi-copy"></i>
                            </button>
                        </div>
                        <div
                            class="border rounded-3 p-2 px-3 d-flex justify-content-between align-items-center bg-white hidden-icon-control">
                            <div class="d-flex align-items-center">
                                <span class="fw-bold text-dark">{{ $firstGst->item_value ?? '-' }}</span>
                            </div>
                        </div>
                        @else
                        <div class="text-center py-4 border border-dashed rounded-3 bg-light">
                            <span
                                class="small">{{ trans('suppliers.supplier_info.view.no_gst_records_available') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane fade p-4 pt-0" id="account">
            <div class="card">
                <div class="card-body table-responsive">
                    <div class="d-flex align-items-center gap-2 mb-4" data-select2-id="select2-data-5-1om7">
                        <div class="flex-grow-1"></div>
                        <div>
                            <div class="amg-list-searchbar">
                                <svg class="amg-list-searchbar__icon" width="20" height="20"
                                    viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z"
                                        fill="currentColor"></path>
                                </svg>
                                <input type="text" class="amg-list-searchbar__input"
                                    placeholder="{{ trans('suppliers.supplier_info.view.search') }}"
                                    id="accountSearch">
                            </div>
                        </div>
                        <button class="amg-refresh-btn btn-reload-list" data-table="accounts">
                            <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z"
                                    fill="currentColor"></path>
                            </svg>
                            <span>{{ trans('suppliers.supplier_info.view.refresh') }}</span>
                        </button>
                        <button class="amg-btn amg-btn-primary go-add-account" type="button"
                            data-bs-toggle="tooltip" title="{{ trans('suppliers.supplier_info.view.add_account') }}">
                            <svg width="19" height="19" viewBox="0 0 19 19" fill="none">
                                <path
                                    d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z"
                                    fill="currentColor" />
                            </svg>
                            <span>{{ trans('suppliers.supplier_info.view.add') }}</span>
                        </button>
                    </div>
                    <table id="accountTable" class="table w-100">
                        <thead>
                            <tr>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.id') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.status') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.account_number') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.bank_name') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.ifsc_code') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.branch_name') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.account_name_as_per_bank_account') }}
                                    </h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.website_url') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.notes') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.updated_at') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.actions') }}</h4>
                                </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane fade p-4 pt-0" id="pan">
            <div class="card">
                <div class="card-body table-responsive">
                    <div class="d-flex align-items-center gap-2 mb-4" data-select2-id="select2-data-5-1om7">
                        <div class="flex-grow-1"></div>
                        <div>
                            <div class="amg-list-searchbar">
                                <svg class="amg-list-searchbar__icon" width="20" height="20"
                                    viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z"
                                        fill="currentColor"></path>
                                </svg>
                                <input type="text" class="amg-list-searchbar__input"
                                    placeholder="{{ trans('suppliers.supplier_info.view.search') }}" id="panSearch">
                            </div>
                        </div>
                        <button class="amg-refresh-btn btn-reload-list" data-table="pan">
                            <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z"
                                    fill="currentColor"></path>
                            </svg>
                            <span>{{ trans('suppliers.supplier_info.view.refresh') }}</span>
                        </button>
                        <button class="amg-btn amg-btn-primary btn-add-pan" type="button" data-bs-toggle="tooltip"
                            title="{{ trans('suppliers.supplier_info.view.add_pan') }}" data-item_type="pan">
                            <svg width="19" height="19" viewBox="0 0 19 19" fill="none">
                                <path
                                    d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z"
                                    fill="currentColor" />
                            </svg>
                            <span>{{ trans('suppliers.supplier_info.view.add') }}</span>
                        </button>
                    </div>
                    <table id="panTable" class="table w-100">
                        <thead>
                            <tr>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.id') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.status') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.pan') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.username') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.updated_at') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.actions') }}</h4>
                                </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane fade p-4 pt-0" id="address">
            <div class="card">
                <div class="card-body table-responsive">
                    <div class="d-flex align-items-center gap-2 mb-4" data-select2-id="select2-data-5-1om7">
                        <div class="flex-grow-1"></div>
                        <div>
                            <div class="amg-list-searchbar">
                                <svg class="amg-list-searchbar__icon" width="20" height="20"
                                    viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z"
                                        fill="currentColor"></path>
                                </svg>
                                <input type="text" class="amg-list-searchbar__input"
                                    placeholder="{{ trans('suppliers.supplier_info.view.search') }}"
                                    id="addressSearch">
                            </div>
                        </div>
                        <button class="amg-refresh-btn btn-reload-list" data-table="address">
                            <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z"
                                    fill="currentColor"></path>
                            </svg>
                            <span>{{ trans('suppliers.supplier_info.view.refresh') }}</span>
                        </button>
                        <button class="amg-btn amg-btn-primary go-add-address" type="button"
                            data-bs-toggle="tooltip" title="{{ trans('suppliers.supplier_info.view.add_address') }}">
                            <svg width="19" height="19" viewBox="0 0 19 19" fill="none">
                                <path
                                    d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z"
                                    fill="currentColor" />
                            </svg>
                            <span>{{ trans('suppliers.supplier_info.view.add') }}</span>
                        </button>
                    </div>
                    <table id="addressTable" class="table w-100">
                        <thead>
                            <tr>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.id') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.status') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.street1') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.street2') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.country') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.state') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.city') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.zip_postal_code') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.phone') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.email') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.fax') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.updated_at') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.actions') }}</h4>
                                </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane fade p-4 pt-0" id="gst">
            <div class="card">
                <div class="card-body table-responsive">
                    <div class="d-flex align-items-center gap-2 mb-4" data-select2-id="select2-data-5-1om7">
                        <div class="flex-grow-1"></div>
                        <div>
                            <div class="amg-list-searchbar">
                                <svg class="amg-list-searchbar__icon" width="20" height="20"
                                    viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z"
                                        fill="currentColor"></path>
                                </svg>
                                <input type="text" class="amg-list-searchbar__input"
                                    placeholder="{{ trans('suppliers.supplier_info.view.search') }}" id="gstSearch">
                            </div>
                        </div>
                        <button class="amg-refresh-btn btn-reload-list" data-table="gst">
                            <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z"
                                    fill="currentColor"></path>
                            </svg>
                            <span>{{ trans('suppliers.supplier_info.view.refresh') }}</span>
                        </button>
                        <button class="amg-btn amg-btn-primary btn-add-gstin" type="button" data-bs-toggle="tooltip"
                            title="{{ trans('suppliers.supplier_info.view.add_gstin') }}" data-item_type="gstin">
                            <svg width="19" height="19" viewBox="0 0 19 19" fill="none">
                                <path
                                    d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z"
                                    fill="currentColor" />
                            </svg>
                            <span>{{ trans('suppliers.supplier_info.view.add') }}</span>
                        </button>
                    </div>
                    <table id="gstTable" class="table w-100">
                        <thead>
                            <tr>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.id') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.status') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.gst') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.username') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.updated_at') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('suppliers.supplier_info.view.actions') }}</h4>
                                </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane fade px-4 pb-4 pt-2" id="courier" role="tabpanel">
            <div class="tab-bar mb-3">
                <ul class="nav nav-underline" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#courier-basic" role="tab">
                            {{ trans('suppliers.supplier_info.view.basic_details') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#courier-insurance" role="tab">
                            {{ trans('suppliers.supplier_info.view.insurance') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#courier-taxation" role="tab">
                            {{ trans('suppliers.supplier_info.view.taxation') }}
                        </a>
                    </li>
                </ul>
            </div>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="courier-basic" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                                <h5 class="mb-4 fw-bold text-dark">
                                    {{ trans('suppliers.supplier_info.view.basic_details') }}
                                </h5>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span
                                        class="text-dark fw-medium">{{ trans('suppliers.supplier_info.view.basic_charge') }}</span>
                                    <span>{{ $courierData['base_charge']['base_charge'] ?? '-' }}</span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span
                                        class="text-dark fw-medium">{{ trans('suppliers.supplier_info.view.rate_per_gram') }}</span>
                                    <span>{{ $courierData['base_charge']['rate_per_gram'] ?? '-' }}</span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span
                                        class="text-dark fw-medium">{{ trans('suppliers.supplier_info.view.enable_volumetric_charge') }}</span>
                                    <span>{{ ($courierData['base_charge']['enable_volumetric_charge'] ?? 0) == 1 ? trans('suppliers.supplier_info.view.yes') : trans('suppliers.supplier_info.view.no') }}</span>
                                </div>

                                <div
                                    class="d-flex justify-content-between align-items-center mb-3 {{ ($courierData['base_charge']['enable_volumetric_charge'] ?? 0) == 1 ? '' : 'd-none' }}">
                                    <span
                                        class="text-dark fw-medium">{{ trans('suppliers.supplier_info.view.volumetric_divisor') }}</span>
                                    <span>{{ $courierData['base_charge']['volumetric_divisor'] ?? '-' }}</span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span
                                        class="text-dark fw-medium">{{ trans('suppliers.supplier_info.view.fragile_liquid_charge') }}</span>
                                    <span>{{ $courierData['base_charge']['fragile_liquid_charge'] ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="courier-insurance" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                                <h5 class="mb-4 fw-bold text-dark">
                                    {{ trans('suppliers.supplier_info.view.insurance') }}
                                </h5>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span
                                        class="text-dark fw-medium">{{ trans('suppliers.supplier_info.view.insurance_rate') }}</span>
                                    <span>{{ $courierData['base_charge']['insurance_rate'] ?? '-' }}</span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span
                                        class="text-dark fw-medium">{{ trans('suppliers.supplier_info.view.insurance_handling_charges') }}</span>
                                    <span>{{ $courierData['base_charge']['insurance_handling_charges'] ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="courier-taxation" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                                <h5 class="mb-4 fw-bold text-dark">
                                    {{ trans('suppliers.supplier_info.view.taxation') }}
                                </h5>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span
                                        class="text-dark fw-medium">{{ trans('suppliers.supplier_info.view.tax_name') }}</span>
                                    <span>{{ $courierData['dropdown']['tax']['text'] ?? '-' }}</span>
                                </div>

                                @forelse ($courierData['dropdown']['taxElements'] ?? [] as $key => $elementValue)
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-dark fw-medium">{{ $key }}</span>
                                    <span>{{ $elementValue ?? '-' }}</span>
                                </div>
                                @empty
                                <div class="text-center text-muted small py-2">
                                    {{ trans('suppliers.supplier_info.view.no_tax_elements_found') }}
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('suppliers.addresses.add')
    @include('suppliers.numbers.add')
    @include('suppliers.add')
    @include('suppliers.basic_info_html')
    @include('suppliers.accounts.add')
</main>
@endsection

@push('scripts')
<script src="{!! CommonHelper::asset('js/common.js') !!}"></script>
<script src="{!! CommonHelper::asset('js/support_validate.js') !!}"></script>
<script src="{!! CommonHelper::asset('js/suppliers/info.js') !!}"></script>
<script src="{!! CommonHelper::asset('js/suppliers/courier_charges.js') !!}"></script>

<script type="text/javascript">
    $(function() {
        var addressPhase = new AddressPhase({
            translations: {
                press_enter_with_Search: '{{ trans('suppliers.supplier_info.config.press_enter_with_Search') }}',
                reload: '{{ trans('suppliers.supplier_info.config.reload') }}',
                License_Tag: '{{ trans('suppliers.supplier_info.config.License_Tag') }}',
                Accessory_Tag: '{{ trans('suppliers.supplier_info.config.Accessory_Tag') }}',
                Component_Tag: '{{ trans('suppliers.supplier_info.config.Component_Tag') }}',
                batch_no: '{{ trans('suppliers.supplier_info.config.batch_no') }}',
                Download_Document: '{{ trans('suppliers.supplier_info.config.Download_Document') }}',
                Refresh_List: '{{ trans('suppliers.supplier_info.config.Refresh_List') }}',
                Search: '{{ trans('suppliers.supplier_info.config.Search') }}',
                select_the_business_category: '{{ trans('suppliers.supplier_info.config.select_the_business_category') }}',
                add_new: '{{ trans('suppliers.supplier_info.config.add_new') }}',
                edit_new: '{{ trans('suppliers.supplier_info.config.edit_new') }}',
                enter: '{{ trans('suppliers.supplier_info.config.enter') }}',
                add_account_details: '{{ trans('suppliers.supplier_info.config.add_account_details') }}',
                edit_account_details: '{{ trans('suppliers.supplier_info.config.edit_account_details') }}',
                edit_supplier_details: '{{ trans('suppliers.supplier_info.config.edit_supplier_details') }}',
                please_enter_valid_search: '{{ trans('suppliers.supplier_info.config.please_enter_valid_search') }}',
                no_number_data_found: '{{ trans('suppliers.supplier_info.config.no_number_data_found') }}',
                no_account_data_found: '{{ trans('suppliers.supplier_info.config.no_account_data_found') }}',
                no_address_data_found: '{{ trans('suppliers.supplier_info.config.no_address_data_found') }}',
                something_went_wrong_check_details_are_correct: '{{ trans('suppliers.supplier_info.config.something_went_wrong_check_details_are_correct') }}',
                are_you_delete_number: '{{ trans('suppliers.supplier_info.config.are_you_delete_number') }}',
                are_you_delete_account: '{{ trans('suppliers.supplier_info.config.are_you_delete_account') }}',
                marked_as_primary_successfully: '{{ trans('suppliers.supplier_info.config.marked_as_primary_successfully') }}',
                address_marked_as_primary_successfully: '{{ trans('suppliers.supplier_info.config.address_marked_as_primary_successfully') }}',
                account_marked_as_primary_successfully: '{{ trans('suppliers.supplier_info.config.account_marked_as_primary_successfully') }}',
                primary: '{{ trans('suppliers.supplier_info.config.primary') }}',          
                secondary: '{{ trans('suppliers.supplier_info.config.secondary') }}',
                edit_pan: '{{ trans('suppliers.supplier_info.config.edit_pan') }}',
                edit_gst: '{{ trans('suppliers.supplier_info.config.edit_gst') }}',
                edit_account: '{{ trans('suppliers.supplier_info.config.edit_account') }}',
                action_view_supplier: '{{ trans('suppliers.supplier_info.config.action_view_supplier') }}',
                action_edit_supplier: '{{ trans('suppliers.supplier_info.config.action_edit_supplier') }}',
                action_delete_supplier: '{{ trans('suppliers.supplier_info.config.action_delete_supplier') }}',
                something_went_wrong: '{{ trans('suppliers.supplier_info.config.something_went_wrong') }}',
                are_you_delete: '{{ trans('suppliers.supplier_info.config.are_you_delete') }}',
                are_you_delete_address: '{{ trans('suppliers.supplier_info.config.are_you_delete_address') }}',
                press_enter_with_Search: '{{ trans('suppliers.supplier_info.config.press_enter_with_Search') }}',
                reload: '{{ trans('suppliers.supplier_info.config.reload') }}',
                select_country: '{{ trans('suppliers.supplier_info.config.select_country') }}',
                select_state: '{{ trans('suppliers.supplier_info.config.select_state') }}',
                select_city: '{{ trans('suppliers.supplier_info.config.select_city') }}',
                are_you_delete_record: '{{ trans('suppliers.supplier_info.config.are_you_delete_record') }}',
                add_address_details: '{{ trans('suppliers.supplier_info.config.add_address_details') }}',
                edit_address_details: '{{ trans('suppliers.supplier_info.config.edit_address_details') }}',
                are_you_delete_attachment: '{{ trans('suppliers.config.are_you_delete_attachment') }}',
                
            },
            url: {
                list: "{{ url('suppliers') }}",
                info: "{{ url('supplier/info') }}",
                getInfo: "{{ url('supplier/get-info') }}",
                addAddress: "{{ url('supplier/address/add') }}",
                editAddress: "{{ url('supplier/address/edit') }}",
                getBusinessCategories: "{{ url('getBusinessCategories') }}",
                country: "{{ url('getCountryByQuery') }}",
                state: "{{ url('fetchStateByAjax') }}",
                city: "{{ url('fetchCityByAjax') }}",
                supplier: "{{ url('supplier') }}",
                deleteAddress: "{{ url('supplier/address/delete') }}",
                addImage: "{{ url('supplier-upload-image/' . $supplier->id) }}",
                deleteNumber: "{{ url('supplier/number/delete') }}",
                edit: "{{ url('supplier/edit') }}",
                custom_tax: "{{ url('get-customtax') }}",
                custom_element: "{{ url('get-element') }}",
                addCourierDetails: "{{ url('supplier/add-courier-details') }}",
                updateCourierDetails: "{{ url('supplier/update-courier-details') }}",
                getCourierDetails: "{{ url('supplier/get-courier-details') }}",
                delete: "{{ url('suppliers/delete') }}",
                panList: "{{ url('supplier/panList') }}",
                gstList: "{{ url('supplier/gstList') }}",
                addressList: "{{ url('supplier/addressList') }}",
                getNumberDetails: "{{ url('supplier/number/edit') }}",
                getAccountList: "{{ url('supplier/accountList') }}",
                addAccount: "{{ url('supplier/account/add') }}",
                editAccount: "{{ url('supplier/account/edit') }}",
                deleteAccount: "{{ url('supplier/account/delete') }}",
                makePrimaryAccount: "{{ url('supplier/account/makePrimary') }}",
                makePrimaryAddress: "{{ url('supplier/address/makePrimary') }}",
                makePrimaryNumber: "{{ url('supplier/number/makePrimary') }}",
            },
            token: "{{ csrf_token() }}",
            supplier_id: "{{ $supplier->id }}"
        });
    });
    var config = {};
    config.url = {};
    config.url.custom_tax = "{{ url('get-customtax') }}";
    config.url.custom_element = "{{ url('get-element') }}";
    config.url.addCourierDetails = "{{ url('supplier/add-courier-details') }}";
    config.url.updateCourierDetails = "{{ url('supplier/update-courier-details') }}";
    config.url.getCourierDetails = "{{ url('supplier/get-courier-details') }}";

    config.token = "{{ csrf_token() }}";
    config.supplier_id = "{{ $supplier->id }}";

    config.translations = {
        // press_enter_with_Search: '{{ trans('suppliers.supplier_info.config.press_enter_with_Search') }}',
        // reload: '{{ trans('suppliers.supplier_info.config.reload') }}',
        // License_Tag: '{{ trans('suppliers.supplier_info.config.License_Tag') }}',
        // Accessory_Tag: '{{ trans('suppliers.supplier_info.config.Accessory_Tag') }}',
        // Component_Tag: '{{ trans('suppliers.supplier_info.config.Component_Tag') }}',
        // batch_no: '{{ trans('suppliers.supplier_info.config.batch_no') }}',
        // Download_Document: '{{ trans('suppliers.supplier_info.config.Download_Document') }}',
        // Refresh_List: '{{ trans('suppliers.supplier_info.config.Refresh_List') }}',
        // Search: '{{ trans('suppliers.supplier_info.config.Search') }}',
        // select_the_business_category: '{{ trans('suppliers.supplier_info.config.select_the_business_category') }}',
        // add_new: '{{ trans('suppliers.supplier_info.config.add_new') }}',
        // edit_new: '{{ trans('suppliers.supplier_info.config.edit_new') }}',
        // enter: '{{ trans('suppliers.supplier_info.config.enter') }}',
        // add_account_details: '{{ trans('suppliers.supplier_info.config.add_account_details') }}',
        // edit_account_details: '{{ trans('suppliers.supplier_info.config.edit_account_details') }}',
        // edit_supplier_details: '{{ trans('suppliers.supplier_info.config.edit_supplier_details') }}',
        // please_enter_valid_search: '{{ trans('suppliers.supplier_info.config.please_enter_valid_search') }}',
        // no_number_data_found: '{{ trans('suppliers.supplier_info.config.no_number_data_found') }}',
        // no_account_data_found: '{{ trans('suppliers.supplier_info.config.no_account_data_found') }}',
        // no_address_data_found: '{{ trans('suppliers.supplier_info.config.no_address_data_found') }}',
        // something_went_wrong_check_details_are_correct: '{{ trans('suppliers.supplier_info.config.something_went_wrong_check_details_are_correct') }}',
        // are_you_delete_number: '{{ trans('suppliers.supplier_info.config.are_you_delete_number') }}',
        // are_you_delete_account: '{{ trans('suppliers.supplier_info.config.are_you_delete_account') }}',
        // marked_as_primary_successfully: '{{ trans('suppliers.supplier_info.config.marked_as_primary_successfully') }}',
        // address_marked_as_primary_successfully: '{{ trans('suppliers.supplier_info.config.address_marked_as_primary_successfully') }}',
        // account_marked_as_primary_successfully: '{{ trans('suppliers.supplier_info.config.account_marked_as_primary_successfully') }}',
        // primary: '{{ trans('suppliers.supplier_info.config.primary') }}',
        // secondary: '{{ trans('suppliers.supplier_info.config.secondary') }}',
        // edit_pan: '{{ trans('suppliers.supplier_info.config.edit_pan') }}',
        // edit_gst: '{{ trans('suppliers.supplier_info.config.edit_gst') }}',
        // edit_account: '{{ trans('suppliers.supplier_info.config.edit_account') }}',
        // action_view_supplier: '{{ trans('suppliers.supplier_info.config.action_view_supplier') }}',
        // action_edit_supplier: '{{ trans('suppliers.supplier_info.config.action_edit_supplier') }}',
        // action_delete_supplier: '{{ trans('suppliers.supplier_info.config.action_delete_supplier') }}',
        // something_went_wrong: '{{ trans('suppliers.supplier_info.config.something_went_wrong') }}',
        // are_you_delete: '{{ trans('suppliers.supplier_info.config.are_you_delete') }}',
        // are_you_delete_address: '{{ trans('suppliers.supplier_info.config.are_you_delete_address') }}',
        // press_enter_with_Search: '{{ trans('suppliers.supplier_info.config.press_enter_with_Search') }}',
        // reload: '{{ trans('suppliers.supplier_info.config.reload') }}',
        // select_country: '{{ trans('suppliers.supplier_info.config.select_country') }}',
        // select_state: '{{ trans('suppliers.supplier_info.config.select_state') }}',
        // select_city: '{{ trans('suppliers.supplier_info.config.select_city') }}',
        // are_you_delete_record: '{{ trans('suppliers.supplier_info.config.are_you_delete_record') }}',
        // add_address_details: '{{ trans('suppliers.supplier_info.config.add_address_details') }}',
        // edit_address_details: '{{ trans('suppliers.supplier_info.config.edit_address_details') }}',
        chooseTaxname: '{{ trans('suppliers.supplier_info.config.chooseTaxname') }}',
        something_went_wrong_check_details_are_correct: '{{ trans('suppliers.supplier_info.config.something_went_wrong_check_details_are_correct') }}',
        percentage: '{{ trans('suppliers.supplier_info.config.percentage') }}',
        select_tax_name: '{{ trans('suppliers.supplier_info.config.select_tax_name') }}',
        tax_name: '{{ trans('suppliers.supplier_info.config.tax_name') }}',
    };
    new MyApp(config);
</script>
@endpush
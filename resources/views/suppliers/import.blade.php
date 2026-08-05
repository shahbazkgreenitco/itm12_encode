{{--
/**
* ------------------------------------------------------------
* File: import.blade.php
* Module: Suppliers
* SUP/26/04
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #SUP-008
* Created On: 2026-04-28
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.1] - Changes in the layout for the pagination section going under the footer issue
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}

@extends('layouts.layout1')
@section('title', trans('suppliers_import.view.header'))

@section('content')
<style>
    :root {
        --primary: #1a2e5a;
        --primary-hover: #243d7a;
        --border-dash: #b0bec5;
        --bg-upload: #f8fafc;
        --bg-page: #eef2f7;
        --text-muted: #607d8b;
        --accent: #1565c0;
    }

    .import-wrapper {
        padding: .4rem 0rem 1rem 0rem;
        width: 100%;
    }

    .import-wrapper .upload-doc-btn,
    .import-wrapper .save-doc-btn {
        font-size: 12px;
        height: 26px;
        min-width: 160px
    }

    .import-wrapper .upload-doc-btn svg,
    .import-wrapper .save-doc-btn svg {
        width: 14px;
        height: 14px;
        color: #fff;
    }

    .import-wrapper h5 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--primary);
        margin-bottom: 1.5rem;
        letter-spacing: 0.02em;
    }

    .import-wrapper #btnRemove {
        display: block;
        width: 100%;
        color: #00000070;
        font-size: 12px;
        text-decoration: underline;
        text-underline-offset: 3px;
        text-decoration-thickness: 1.10px;
        transition: opacity 0.3s ease;
        margin-top: 7px;
    }

    .import-wrapper #btnRemove:hover {
        color: #00000090;
    }

    .header-actions-wrapper.header-actions-white-wrapper {
        background: #F8FAFD !important;
        background-image: none !important;
        border-bottom: 1px solid #E8E8E8 !important;
    }

    [data-bs-theme="dark"] .header-actions-wrapper.header-actions-white-wrapper {
        border-bottom: 0px !important;
    }

    .drop-zone {
        border: 2px dashed var(--border-dash);
        border-radius: 7px;
        background: var(--bg-upload);
        padding: 1.5rem 1.1rem;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
        position: relative;
    }

    .drop-zone:hover,
    .drop-zone.dragover {
        border-color: var(--accent);
        background: #e8f0fe;
    }

    .drop-zone input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }

    .excel-icon {
        width: 52px;
        height: 52px;
        margin: 0 auto 1rem;
        display: block;
    }

    .drop-zone .drag-text,
    .drop-zone .file-selected-name {
        color: #000;
        font-size: 12px;
        margin-bottom: 0.85rem;
        font-weight: 400;
    }

    .btn-upload {
        background: var(--primary);
        color: #fff;
        border: none;
        border-radius: 7px;
        padding: 0.5rem 1.25rem;
        font-size: 0.85rem;
        font-weight: 600;
        letter-spacing: 0.01em;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        cursor: pointer;
        pointer-events: none;
        transition: background 0.2s;
    }

    .drop-zone:hover .btn-upload {
        background: var(--primary-hover);
    }

    .file-label {
        font-size: 12px;
        display: block;
        color: #000000;
        opacity: .60;
        font-weight: 400;
        margin-top: 7px;
    }

    .divider {
        display: none;
    }

    .right-panel {
        display: flex;
        align-items: end;
        justify-content: start;
        height: 100%;
    }

    @media (max-width: 575.98px) {
        .right-panel {
            border-left: none;
            border-top: 1px solid #e0e7ef;
            padding-top: 1.25rem;
            margin-top: 0.5rem;
        }

        .import-wrapper {
            padding: 1.5rem 1rem;
        }
    }

    .drop-zone.file-selected {
        border-color: #c8d6e5;
    }

    .drop-zone.file-selected:hover {
        border-color: #F12F35;
        background: #f12f3520;
    }

    .file-selected-name {
        font-weight: 400;
        color: #000000;
    }

    .label-required {
        color: var(--red);
    }

    .instructions-card {
        background: #fff;
        border: 1px solid #DFDFE1;
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        width: 100%;
        font-size: 0.84rem;
        color: #374151;
        line-height: 1.75;
    }

    .instructions-card #readMoreBtn {
        text-decoration: underline;
        text-underline-offset: 3px;
        text-decoration-thickness: 1.10px;
        transition: opacity 0.3s ease;
    }

    .instructions-card #readMoreBtn:hover {
        opacity: .8;
    }

    .instructions-card .header-text {
        font-size: 0.84rem;
        color: #374151;
        margin-bottom: 0.4rem;
    }

    .instructions-card .notice {
        font-style: italic;
        font-weight: 600;
        color: #1a2e5a;
        margin-bottom: 0.75rem;
    }

    .instructions-card .section-title {
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.6rem;
        font-size: 0.9rem;
    }

    .instructions-card ul {
        padding-left: 1.25rem;
        margin-bottom: 0.75rem;
        list-style: disc;
    }

    .instructions-card ul li {
        margin-bottom: 0.25rem;
        color: #4b5563;
    }

    .instructions-card ul li strong {
        color: #111827;
        font-weight: 600;
    }

    .card-scroll-body {
        max-height: 200px;
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 4px;
    }

    .card-scroll-body.expanded {
        max-height: none;
        overflow-y: visible;
    }

    .card-scroll-body::-webkit-scrollbar {
        width: 4px;
    }

    .card-scroll-body::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }

    .card-scroll-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .card-scroll-body::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .read-more-link {
        color: var(--red);
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: color 0.2s;
    }

    .read-more-link:hover {
        color: #c62828;
        text-decoration: underline;
    }

    /* ── Footer ── */
    .page-footer {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.75rem;
        color: #90a4ae;
        padding: 0.6rem 0.25rem 0;
        margin-top: 0.75rem;
        border-top: 1px solid #e0e7ef;
    }

    .select2-container--custom {
        min-width: 130px;
    }

    .select2-container--custom .select2-selection--single {
        height: 36px;
        width: 158px;
        border: 1.5px solid #00000024;
        border-radius: 8px;
        background: #ffffff;
        padding: 0 36px 0 14px;
        outline: none;
        transition: border-color 0.18s ease, box-shadow 0.18s ease;
        cursor: pointer;
        position: relative;
        box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.06);

    }

    .select2-container--custom .select2-selection--single:hover {
        border-color: #b0b0b0;
    }

    .select2-container--custom.select2-container--open .select2-selection--single {
        border-color: #b0b0b0;
    }

    .select2-container--custom .select2-selection--single .select2-selection__rendered {
        font-size: 16px;
        font-weight: 400;
        color: #00000070;
        line-height: 33px;
        padding: 0;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .select2-container--custom .select2-selection--single .select2-selection__arrow {
        position: absolute;
        top: 0;
        right: 10px;
        height: 36px;
        width: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .select2-container--custom .select2-selection--single .select2-selection__arrow b {
        display: none;
    }

    .select2-container--custom .select2-selection--single .select2-selection__arrow::after {
        content: '';
        display: block;
        width: 12px;
        height: 12px;
        border-right: 1.7px solid #00000050;
        border-bottom: 1.7px solid #00000050;
        transform: rotate(45deg) translate(-1px, -3px);
        transition: transform 0.2s ease;
    }

    .select2-container--custom.select2-container--open .select2-selection--single .select2-selection__arrow::after {
        transform: rotate(225deg) translate(-1px, -3px);
    }

    .select2-container--custom .select2-dropdown {
        border: 1.5px solid #e0e0e0;
        border-radius: 12px !important;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.09);
        overflow: hidden;
        margin-top: 5px;
    }

    .select2-container--custom.select2-container--below .select2-dropdown {
        border-top: 1.5px solid #e0e0e0;
        border-radius: 12px;
    }

    .select2-container--custom .select2-search--dropdown {
        display: none !important;
        padding: 0;
    }

    .select2-container--custom .select2-results {
        border-radius: 12px;
    }

    .select2-container--custom .select2-results__options {
        padding: 5px 0;
        max-height: 220px;
        overflow-y: auto;
    }

    .select2-container--custom .select2-results__option {
        font-size: 0.875rem;
        color: #1a1a1a;
        padding: 8px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: background 0.12s ease;
        cursor: pointer;
        background: #fff;
    }

    .select2-container--custom .select2-results__option--highlighted[aria-selected] {
        background: #f5f5f5 !important;
        color: #1a1a1a !important;
    }

    .select2-container--custom .select2-results__option[aria-selected="true"] {
        background: #ffffff !important;
        font-weight: 500;
        color: #1a1a1a !important;
    }

    .select2-container--custom .select2-results__option[aria-selected="true"]::after {
        content: '';
        display: inline-block;
        flex-shrink: 0;
        width: 14px;
        height: 14px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 14 14' fill='none'%3E%3Cpath d='M2 7L5.5 10.5L12 3.5' stroke='%231a1a1a' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-size: contain;
    }

    .select2-container--custom .select2-results__options::-webkit-scrollbar {
        width: 4px;
    }

    .select2-container--custom .select2-results__options::-webkit-scrollbar-thumb {
        background: #d0d0d0;
        border-radius: 10px;
    }
</style>
<!-- CONTENT -->
<section class="content">
    <div class="container-fluid px-0">
        <div
            class="header-actions-wrapper header-actions-white-wrapper d-flex align-items-center justify-content-between pe-4">
            <div class="py-3">
                <div class="container-fluid ps-0">
                    <button onclick="location.href='{{ url('suppliers') }}'"
                        class="d-flex gap-3 align-items-center bg-transparent outline-none border-0">
                        <svg width="25" height="21" viewBox="0 0 25 21" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M11.5662 0.440759C11.706 0.580116 11.817 0.745702 11.8927 0.928029C11.9684 1.11036 12.0074 1.30584 12.0074 1.50326C12.0074 1.70068 11.9684 1.89616 11.8927 2.07848C11.817 2.26081 11.706 2.4264 11.5662 2.56576L5.12993 9.00201L23.5049 9.00201C23.9028 9.00201 24.2843 9.16004 24.5656 9.44135C24.8469 9.72265 25.0049 10.1042 25.0049 10.502C25.0049 10.8998 24.8469 11.2814 24.5656 11.5627C24.2843 11.844 23.9028 12.002 23.5049 12.002L5.12993 12.002L11.5662 18.4408C11.848 18.7226 12.0063 19.1047 12.0063 19.5033C12.0063 19.9018 11.848 20.284 11.5662 20.5658C11.2844 20.8475 10.9022 21.0059 10.5037 21.0059C10.1052 21.0059 9.72298 20.8475 9.44118 20.5658L0.441182 11.5658C0.301343 11.4264 0.190387 11.2608 0.114679 11.0785C0.0389711 10.8962 -4.50154e-07 10.7007 -4.58778e-07 10.5033C-4.67401e-07 10.3058 0.038971 10.1104 0.114679 9.92803C0.190387 9.7457 0.301342 9.58011 0.441182 9.44076L9.44118 0.440759C9.58054 0.30092 9.74613 0.189964 9.92845 0.114255C10.1108 0.0385471 10.3063 -0.000425789 10.5037 -0.000425798C10.7011 -0.000425806 10.8966 0.038547 11.0789 0.114255C11.2612 0.189964 11.4268 0.30092 11.5662 0.440759Z"
                                fill="currentColor" />
                        </svg>
                        <h2 class="h2-text mb-0">{{ trans('suppliers_import.view.page_heading') }}</h2>
                    </button>
                </div>
            </div>
        </div>

        <main class="main-content" id="mainContent">
            <div class="tab-bar">
                <ul class="nav nav-underline" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="info-tab-link" data-bs-toggle="tab" href="#info-tab"
                            role="tab">
                            <span>{{ trans('suppliers_import.view.bulk_support_import') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="import-info-tab-link" data-bs-toggle="tab" href="#import-info-tab"
                            role="tab">
                            <span>{{ trans('suppliers_import.view.bulk_supplier_import_info') }}</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="tab-content">
                <div class="tab-pane fade show active container-fluid pt-3 pb-5" id="info-tab">
                    {{-- @if (session('msg'))
                        <div class="alert customize-alert alert-dismissible alert-light-danger bg-danger-subtle text-danger fade show remove-close-icon"
                            role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <div class="d-flex align-items-center me-3 gap-3 me-md-0">
                                <svg width="18px" height="18px" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"
                                    fill="none">
                                    <path fill="currentColor" fill-rule="evenodd"
                                        d="M10 3a7 7 0 100 14 7 7 0 000-14zm-9 7a9 9 0 1118 0 9 9 0 01-18 0zm8-4a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1zm.01 8a1 1 0 102 0V9a1 1 0 10-2 0v5z" />
                            </svg>
                            {{ session('msg')['msg'] }}
                </div>
            </div>
            @endif --}}

            @if (session()->has('success') || session()->has('fail'))
            {{-- Success count --}}
            @if (session('success', 0) > 0)
            <div class="alert customize-alert alert-dismissible alert-light-success bg-success-subtle text-success fade show remove-close-icon"
                role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
                <div class="d-flex align-items-center me-3 gap-3 me-md-0">
                    <svg width="18px" height="18px" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg" fill="none">
                        <path fill="currentColor" fill-rule="evenodd"
                            d="M10 3a7 7 0 100 14 7 7 0 000-14zm-9 7a9 9 0 1118 0 9 9 0 01-18 0zm8-4a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1zm.01 8a1 1 0 102 0V9a1 1 0 10-2 0v5z" />
                    </svg>
                    {{ session('success', 0) }}
                    {{ trans('suppliers_import.view.suppliers_imported_successfully') }}
                </div>
            </div>
            @endif

            @if (session('fail', 0) > 0)
            <div class="alert customize-alert alert-dismissible alert-light-danger bg-danger-subtle text-danger fade show remove-close-icon"
                role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
                <div class="d-flex align-items-start me-3 gap-2 me-md-0">
                    <svg width="18px" height="18px" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg" fill="none">
                        <path fill="currentColor" fill-rule="evenodd"
                            d="M10 3a7 7 0 100 14 7 7 0 000-14zm-9 7a9 9 0 1118 0 9 9 0 01-18 0zm8-4a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1zm.01 8a1 1 0 102 0V9a1 1 0 10-2 0v5z" />
                    </svg>
                    <div>
                        {{ session('fail', 0) }}
                        {{ trans('suppliers_import.view.suppliers_failed_import') }}
                        @if (!empty(session('fail_msgs')))
                        <ul class="mt-1 mb-0">
                            @foreach (session('fail_msgs') as $msg)
                            <li>{{ $msg }}</li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            @if (session('success', 0) == 0 && session('fail', 0) == 0)
            <div class="alert customize-alert alert-dismissible alert-light-warning bg-warning-subtle text-warning fade show remove-close-icon"
                role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
                <div class="d-flex align-items-center me-3 gap-3 me-md-0">
                    <svg width="18px" height="18px" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg" fill="none">
                        <path fill="currentColor" fill-rule="evenodd"
                            d="M10 3a7 7 0 100 14 7 7 0 000-14zm-9 7a9 9 0 1118 0 9 9 0 01-18 0zm8-4a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1zm.01 8a1 1 0 102 0V9a1 1 0 10-2 0v5z" />
                    </svg>
                    {{ trans('suppliers_import.view.no_supplier_imported') }}
                </div>
            </div>
            @endif

            @endif
            <div class="import-wrapper">

                <div class="row g-3 align-items-stretch">
                    <div class="col-12 col-sm-3">
                        <div class="drop-zone" id="dropZone">
                            <input type="file" id="fileInput" accept=".xlsx" />

                            <div id="defaultState">
                                <svg width="30" height="30" viewBox="0 0 44 44" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_2145_7517)">
                                        <path
                                            d="M27.4997 2.75H12.833C12.3469 2.75 11.8808 2.94309 11.537 3.2868C11.1933 3.63051 11.0002 4.09668 11.0002 4.58275V12.3745L27.4997 21.999L35.7494 25.4802L43.9991 21.999V12.3745L27.4997 2.75Z"
                                            fill="#21A366" />
                                        <path d="M11.0002 12.375H27.4997V21.9995H11.0002V12.375Z"
                                            fill="#107C41" />
                                        <path
                                            d="M42.1663 2.75H27.4996V12.3745H43.9991V4.58275C43.9991 4.09668 43.806 3.63051 43.4623 3.2868C43.1185 2.94309 42.6524 2.75 42.1663 2.75Z"
                                            fill="#33C481" />
                                        <path
                                            d="M27.4997 22H11.0002V39.4172C11.0002 39.6579 11.0476 39.8962 11.1398 40.1186C11.2319 40.3409 11.3669 40.543 11.537 40.7131C11.7072 40.8833 11.9093 41.0183 12.1316 41.1104C12.354 41.2025 12.5923 41.2499 12.833 41.2499H42.1673C42.4079 41.2499 42.6463 41.2025 42.8686 41.1104C43.091 41.0183 43.293 40.8833 43.4632 40.7131C43.6334 40.543 43.7684 40.3409 43.8605 40.1186C43.9526 39.8962 44 39.6579 44 39.4172V31.6245L27.4997 22Z"
                                            fill="#185C37" />
                                        <path d="M27.4996 22H43.9991V31.6245H27.4996V22Z" fill="#107C41" />
                                        <path opacity="0.1"
                                            d="M22.9145 9.625H11.0002V35.7499H22.9145C23.4003 35.7491 23.8661 35.5561 24.21 35.2131C24.5539 34.87 24.7481 34.4047 24.7501 33.919V11.4578C24.7486 10.9716 24.5546 10.5059 24.2107 10.1624C23.8667 9.81898 23.4006 9.62574 22.9145 9.625Z"
                                            fill="black" />
                                        <path opacity="0.2"
                                            d="M21.5425 11H11.0002V37.1249H21.5425C22.0278 37.1234 22.4929 36.9301 22.8362 36.5871C23.1795 36.2441 23.3733 35.7793 23.3753 35.294V12.8328C23.3738 12.3471 23.1802 11.8818 22.8368 11.5384C22.4935 11.195 22.0281 11.0015 21.5425 11Z"
                                            fill="black" />
                                        <path opacity="0.2"
                                            d="M21.5425 11H11.0002V34.3743H21.5425C22.0281 34.3729 22.4935 34.1793 22.8368 33.8359C23.1802 33.4925 23.3738 33.0272 23.3753 32.5416V12.8328C23.3738 12.3471 23.1802 11.8818 22.8368 11.5384C22.4935 11.195 22.0281 11.0015 21.5425 11Z"
                                            fill="black" />
                                        <path opacity="0.2"
                                            d="M20.1677 11H11.0002V34.3743H20.1677C20.6533 34.3729 21.1187 34.1793 21.462 33.8359C21.8054 33.4925 21.999 33.0272 22.0005 32.5416V12.8328C21.999 12.3471 21.8054 11.8818 21.462 11.5384C21.1187 11.195 20.6533 11.0015 20.1677 11Z"
                                            fill="black" />
                                        <path
                                            d="M1.83275 11H20.1696C20.6557 11 21.1218 11.1931 21.4655 11.5368C21.8092 11.8805 22.0023 12.3467 22.0023 12.8328V31.1696C22.0023 31.6556 21.8092 32.1218 21.4655 32.4655C21.1218 32.8092 20.6557 33.0023 20.1696 33.0023H1.83275C1.59184 33.0023 1.35329 32.9548 1.13074 32.8625C0.908203 32.7703 0.706038 32.635 0.535814 32.4645C0.365591 32.2941 0.23065 32.0917 0.13871 31.869C0.0467697 31.6463 -0.000364935 31.4077 2.12747e-06 31.1668V12.8328C2.12747e-06 12.3467 0.193095 11.8805 0.536803 11.5368C0.88051 11.1931 1.34668 11 1.83275 11Z"
                                            fill="#107C41" />
                                        <path
                                            d="M4.85785 28.8748L9.30709 21.9804L5.23203 15.125H8.51125L10.7359 19.5072C10.94 19.9224 11.0809 20.2326 11.1585 20.438H11.1873C11.3338 20.106 11.4873 19.7834 11.6481 19.47L14.0253 15.125H17.0355L12.8553 21.9422L17.137 28.8748H13.9369L11.367 24.0607C11.246 23.8558 11.1436 23.6406 11.0607 23.4175H11.0226C10.9476 23.6357 10.848 23.8446 10.7256 24.0402L8.08494 28.8748H4.85785Z"
                                            fill="white" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_2145_7517">
                                            <rect width="44" height="44" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                                <p class="drag-text mt-2 mb-1">{{ trans('suppliers_import.view.drag') }}
                                    &amp; {{ trans('suppliers_import.view.drop_here') }}</p>
                                <button class="amg-btn amg-btn-secondary upload-doc-btn">
                                    <svg width="14" height="17" viewBox="0 0 14 17" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M5 12.5924L9 12.5924C9.55 12.5924 10 12.1424 10 11.5924L10 6.59244L11.59 6.59244C12.48 6.59244 12.93 5.51244 12.3 4.88244L7.71 0.292444C7.61749 0.19974 7.5076 0.126193 7.38662 0.0760114C7.26565 0.02583 7.13597 4.15368e-10 7.005 4.07744e-10C6.87403 4.00121e-10 6.74435 0.02583 6.62338 0.0760114C6.5024 0.126193 6.39251 0.19974 6.3 0.292444L1.71 4.88244C1.08 5.51244 1.52 6.59244 2.41 6.59244L4 6.59244L4 11.5924C4 12.1424 4.45 12.5924 5 12.5924ZM1 14.5924L13 14.5924C13.55 14.5924 14 15.0424 14 15.5924C14 16.1424 13.55 16.5924 13 16.5924L1 16.5924C0.45 16.5924 -9.39615e-10 16.1424 -9.076e-10 15.5924C-8.75586e-10 15.0424 0.45 14.5924 1 14.5924Z"
                                            fill="currentColor" />
                                    </svg>
                                    <span>{{ trans('suppliers_import.view.upload_file') }}</span>
                                </button>
                                <span
                                    class="file-label">{{ trans('suppliers_import.view.files_selected') }}</span>
                            </div>

                            <!-- Selected file state (hidden by default) -->
                            <div id="selectedState" style="display:none;">
                                <svg width="30" height="30" viewBox="0 0 44 44" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_2145_7517)">
                                        <path
                                            d="M27.4997 2.75H12.833C12.3469 2.75 11.8808 2.94309 11.537 3.2868C11.1933 3.63051 11.0002 4.09668 11.0002 4.58275V12.3745L27.4997 21.999L35.7494 25.4802L43.9991 21.999V12.3745L27.4997 2.75Z"
                                            fill="#21A366" />
                                        <path d="M11.0002 12.375H27.4997V21.9995H11.0002V12.375Z"
                                            fill="#107C41" />
                                        <path
                                            d="M42.1663 2.75H27.4996V12.3745H43.9991V4.58275C43.9991 4.09668 43.806 3.63051 43.4623 3.2868C43.1185 2.94309 42.6524 2.75 42.1663 2.75Z"
                                            fill="#33C481" />
                                        <path
                                            d="M27.4997 22H11.0002V39.4172C11.0002 39.6579 11.0476 39.8962 11.1398 40.1186C11.2319 40.3409 11.3669 40.543 11.537 40.7131C11.7072 40.8833 11.9093 41.0183 12.1316 41.1104C12.354 41.2025 12.5923 41.2499 12.833 41.2499H42.1673C42.4079 41.2499 42.6463 41.2025 42.8686 41.1104C43.091 41.0183 43.293 40.8833 43.4632 40.7131C43.6334 40.543 43.7684 40.3409 43.8605 40.1186C43.9526 39.8962 44 39.6579 44 39.4172V31.6245L27.4997 22Z"
                                            fill="#185C37" />
                                        <path d="M27.4996 22H43.9991V31.6245H27.4996V22Z" fill="#107C41" />
                                        <path opacity="0.1"
                                            d="M22.9145 9.625H11.0002V35.7499H22.9145C23.4003 35.7491 23.8661 35.5561 24.21 35.2131C24.5539 34.87 24.7481 34.4047 24.7501 33.919V11.4578C24.7486 10.9716 24.5546 10.5059 24.2107 10.1624C23.8667 9.81898 23.4006 9.62574 22.9145 9.625Z"
                                            fill="black" />
                                        <path opacity="0.2"
                                            d="M21.5425 11H11.0002V37.1249H21.5425C22.0278 37.1234 22.4929 36.9301 22.8362 36.5871C23.1795 36.2441 23.3733 35.7793 23.3753 35.294V12.8328C23.3738 12.3471 23.1802 11.8818 22.8368 11.5384C22.4935 11.195 22.0281 11.0015 21.5425 11Z"
                                            fill="black" />
                                        <path opacity="0.2"
                                            d="M21.5425 11H11.0002V34.3743H21.5425C22.0281 34.3729 22.4935 34.1793 22.8368 33.8359C23.1802 33.4925 23.3738 33.0272 23.3753 32.5416V12.8328C23.3738 12.3471 23.1802 11.8818 22.8368 11.5384C22.4935 11.195 22.0281 11.0015 21.5425 11Z"
                                            fill="black" />
                                        <path opacity="0.2"
                                            d="M20.1677 11H11.0002V34.3743H20.1677C20.6533 34.3729 21.1187 34.1793 21.462 33.8359C21.8054 33.4925 21.999 33.0272 22.0005 32.5416V12.8328C21.999 12.3471 21.8054 11.8818 21.462 11.5384C21.1187 11.195 20.6533 11.0015 20.1677 11Z"
                                            fill="black" />
                                        <path
                                            d="M1.83275 11H20.1696C20.6557 11 21.1218 11.1931 21.4655 11.5368C21.8092 11.8805 22.0023 12.3467 22.0023 12.8328V31.1696C22.0023 31.6556 21.8092 32.1218 21.4655 32.4655C21.1218 32.8092 20.6557 33.0023 20.1696 33.0023H1.83275C1.59184 33.0023 1.35329 32.9548 1.13074 32.8625C0.908203 32.7703 0.706038 32.635 0.535814 32.4645C0.365591 32.2941 0.23065 32.0917 0.13871 31.869C0.0467697 31.6463 -0.000364935 31.4077 2.12747e-06 31.1668V12.8328C2.12747e-06 12.3467 0.193095 11.8805 0.536803 11.5368C0.88051 11.1931 1.34668 11 1.83275 11Z"
                                            fill="#107C41" />
                                        <path
                                            d="M4.85785 28.8748L9.30709 21.9804L5.23203 15.125H8.51125L10.7359 19.5072C10.94 19.9224 11.0809 20.2326 11.1585 20.438H11.1873C11.3338 20.106 11.4873 19.7834 11.6481 19.47L14.0253 15.125H17.0355L12.8553 21.9422L17.137 28.8748H13.9369L11.367 24.0607C11.246 23.8558 11.1436 23.6406 11.0607 23.4175H11.0226C10.9476 23.6357 10.848 23.8446 10.7256 24.0402L8.08494 28.8748H4.85785Z"
                                            fill="white" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_2145_7517">
                                            <rect width="44" height="44" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                                <p class="file-selected-name mt-2 mb-1" id="selectedFileName">filename.xlsx
                                </p>
                                <button class="amg-btn amg-btn-primary" style="min-width:203px"
                                    type="button" id="btnSave">
                                    <svg width="14" height="17" viewBox="0 0 14 17" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M5 12.5924L9 12.5924C9.55 12.5924 10 12.1424 10 11.5924L10 6.59244L11.59 6.59244C12.48 6.59244 12.93 5.51244 12.3 4.88244L7.71 0.292444C7.61749 0.19974 7.5076 0.126193 7.38662 0.0760114C7.26565 0.02583 7.13597 4.15368e-10 7.005 4.07744e-10C6.87403 4.00121e-10 6.74435 0.02583 6.62338 0.0760114C6.5024 0.126193 6.39251 0.19974 6.3 0.292444L1.71 4.88244C1.08 5.51244 1.52 6.59244 2.41 6.59244L4 6.59244L4 11.5924C4 12.1424 4.45 12.5924 5 12.5924ZM1 14.5924L13 14.5924C13.55 14.5924 14 15.0424 14 15.5924C14 16.1424 13.55 16.5924 13 16.5924L1 16.5924C0.45 16.5924 -9.39615e-10 16.1424 -9.076e-10 15.5924C-8.75586e-10 15.0424 0.45 14.5924 1 14.5924Z"
                                            fill="currentColor" />
                                    </svg>
                                    <span>Save</span>
                                </button>
                                <button class="amg-btn amg-btn-link amg-btn-link-plain p-0"
                                    id="btnRemove">{{ trans('suppliers_import.view.remove_file') }}</button>
                            </div>
                        </div>

                        {{-- ↓ Hidden form for file submission — outside drop-zone, inside col ↓ --}}
                        <form id="importForm" method="POST" action="" enctype="multipart/form-data"
                            style="display:none;">
                            @csrf
                            <input type="file" name="import_file" id="hiddenFileInput" />
                        </form>

                    </div>

                    <!-- Download Template -->
                    <div class="col-12 col-sm-9">
                        <div class="right-panel h-100">
                            <a class="amg-btn amg-btn-white amg-btn-md" href="{!! CommonHelper::asset('samples/SupplierImportFormat.xlsx') !!}"
                                target="_blank">
                                <svg width="14" height="17" viewBox="0 0 14 17" fill="currentColor"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M9 3.99935L5 3.99935C4.45 3.99935 4 4.44935 4 4.99935L4 9.99935L2.41 9.99935C1.52 9.99935 1.07 11.0794 1.7 11.7094L6.29 16.2994C6.38251 16.3921 6.4924 16.4656 6.61338 16.5158C6.73435 16.566 6.86403 16.5918 6.995 16.5918C7.12597 16.5918 7.25565 16.566 7.37662 16.5158C7.4976 16.4656 7.60749 16.3921 7.7 16.2994L12.29 11.7094C12.92 11.0794 12.48 9.99935 11.59 9.99935L10 9.99935L10 4.99935C10 4.44935 9.55 3.99935 9 3.99935ZM13 1.99935L1 1.99935C0.450001 1.99935 1.31505e-06 1.54935 1.36313e-06 0.999352C1.41122e-06 0.449353 0.450001 -0.000647776 1 -0.000647728L13 -0.000646679C13.55 -0.000646631 14 0.449354 14 0.999353C14 1.54935 13.55 1.99935 13 1.99935Z"
                                        fill="currentColor" />
                                </svg>
                                <span
                                    class="font-weight-normal">{{ trans('suppliers_import.view.download_template_button') }}</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row justify-content-center">

                <div class="col-12 px-3">

                    <div class="instructions-card mt-1">

                        <h4 class="b2-text" style="color:#7F7F7F">
                            {{ trans('suppliers_import.view.read_notes') }}
                        </h4>

                        <div class="ps-3 ps-sm-4">
                            <div class="card-scroll-body" id="cardScrollBody">
                                <ul class="ps-3">
                                    <li>
                                        <span
                                            class="b3-text me-3">{{ trans('suppliers_import.view.supplier_name') }}
                                            <span class="mandatory"></span></span>
                                        <span
                                            class="b5-text text-muted">{{ trans('suppliers_import.view.supplier_name_text') }}</span>
                                    </li>

                                    <li>
                                        <span
                                            class="b3-text">{{ trans('suppliers_import.view.business_categories') }}</span>
                                        <span
                                            class="b5-text text-muted">{{ trans('suppliers_import.view.business_categories_text') }}</span>
                                    </li>

                                    <li>
                                        <span
                                            class="b3-text">{{ trans('suppliers_import.view.contact_person_name') }}</span>
                                        <span
                                            class="b5-text text-muted">{{ trans('suppliers_import.view.contact_person_name_text') }}</span>
                                    </li>
                                    {{-- <li>
                                            <span class="s1-text">{{ trans('suppliers_import.view.street1') }}</span>
                                    <span
                                        class="b1-text text-muted">{{ trans('suppliers_import.view.street1_text') }}</span>
                                    </li> --}}

                                    {{-- <li>
                                            <span class="s1-text">{{ trans('suppliers_import.view.country') }}</span>
                                    <span
                                        class="b1-text text-muted">{{ trans('suppliers_import.view.country_text') }}</span>
                                    </li> --}}
                                    {{-- <li>
                                            <span class="s1-text">{{ trans('suppliers_import.view.state') }}</span>
                                    <span
                                        class="b1-text text-muted">{{ trans('suppliers_import.view.state_text') }}</span>
                                    </li> --}}

                                    {{-- <li>
                                            <span class="s1-text">{{ trans('suppliers_import.view.city') }}</span>
                                    <span
                                        class="b1-text text-muted">{{ trans('suppliers_import.view.city_text') }}</span>
                                    </li> --}}
                                    {{-- <li>
                                            <span class="s1-text">{{ trans('suppliers_import.view.zip') }}</span>
                                    <span
                                        class="b1-text text-muted">{{ trans('suppliers_import.view.zip_text') }}</span>
                                    </li> --}}

                                    <li>
                                        <span
                                            class="b3-text">{{ trans('suppliers_import.view.phone') }}</span>
                                        <span
                                            class="b5-text text-muted">{{ trans('suppliers_import.view.phone_text') }}</span>
                                    </li>
                                    <li>
                                        <span
                                            class="b3-text">{{ trans('suppliers_import.view.email') }}</span>
                                        <span
                                            class="b5-text text-muted">{{ trans('suppliers_import.view.email_text') }}</span>
                                    </li>

                                    {{-- <li>
                                                <span
                                                    class="s1-text">{{ trans('suppliers_import.view.account_number') }}</span>
                                    <span
                                        class="b1-text text-muted">{{ trans('suppliers_import.view.account_number_text') }}</span>
                                    </li> --}}
                                    {{-- <li>
                                            <span class="s1-text">{{ trans('suppliers_import.view.ifsc') }}</span>
                                    <span
                                        class="b1-text text-muted">{{ trans('suppliers_import.view.ifsc_text') }}</span>
                                    </li> --}}

                                    {{-- <li>
                                                <span
                                                    class="s1-text">{{ trans('suppliers_import.view.branch_name') }}</span>
                                    <span
                                        class="b1-text text-muted">{{ trans('suppliers_import.view.branch_name_text') }}</span>
                                    </li> --}}
                                    {{-- <li>
                                                <span
                                                    class="s1-text">{{ trans('suppliers_import.view.account_name') }}</span>
                                    <span
                                        class="b1-text text-muted">{{ trans('suppliers_import.view.account_name_text') }}</span>
                                    </li> --}}

                                    {{-- <li>
                                                <span
                                                    class="s1-text">{{ trans('suppliers_import.view.website_url') }}</span>
                                    <span
                                        class="b1-text text-muted">{{ trans('suppliers_import.view.website_url_text') }}</span>
                                    </li> --}}

                                    {{-- <li>
                                            <span class="s1-text">{{ trans('suppliers_import.view.notes') }}</span>
                                    <span
                                        class="b1-text text-muted">{{ trans('suppliers_import.view.notes_text') }}</span>
                                    </li> --}}
                                </ul>
                            </div>
                            {{-- <a class="amg-btn amg-btn-link amg-btn-link-plain" id="readMoreBtn">
                                        {{ trans('config.user_import_fields.read_more') }}
                            </a> --}}
                        </div>
                        <h4 class="b2-text" style="color:#7F7F7F">
                            {{ trans('suppliers_import.view.change_first_row') }}
                        </h4>
                    </div>
                </div>
            </div>
    </div>
    <div class="mt-3 tab-pane fade table-responsive border-top-0" id="import-info-tab">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-4" data-select2-id="select2-data-5-1om7">
                    <!-- show select -->
                    <div id="customLengthContainer" class="col-auto"
                        data-select2-id="select2-data-4-sbib">
                        <select id="showSelect"
                            class="showSelect amg-table-pagination-dropdown userModulePageLenth user-list-page-length">
                            <option value="10" selected>{{ trans('suppliers_import.view.show') }} (10)
                            </option>
                            <option value="25">{{ trans('suppliers_import.view.show') }} (25)</option>
                            <option value="50">{{ trans('suppliers_import.view.show') }} (50)</option>
                            <option value="100">{{ trans('suppliers_import.view.show') }} (100)
                            </option>
                        </select>
                    </div>

                    <!-- spacer -->
                    <div class="flex-grow-1"></div>

                    <!-- searchbar -->
                    <div style="width: 280px;">
                        <div class="amg-list-searchbar">
                            <svg class="amg-list-searchbar__icon" width="20" height="20"
                                viewBox="0 0 20 20" fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z"
                                    fill="currentColor"></path>
                            </svg>
                            <input type="text" class="amg-list-searchbar__input" id="tableSearch"
                                placeholder="{{ trans('suppliers_import.view.search') }}...">
                        </div>
                    </div>

                    <!-- refresh button -->
                    <button class="amg-refresh-btn btn-reload-list">
                        <svg class="amg-refresh-btn__icon" width="20" height="18"
                            viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z"
                                fill="currentColor"></path>
                        </svg>
                        <span>{{ trans('suppliers_import.view.refresh') }}</span>
                    </button>
                </div>
                <table id="tblHistory" class="table display">
                    <thead>
                        <tr>
                            <th>
                                <h4>{{ trans('suppliers_import.table.id') }}</h4>
                            </th>
                            <th>
                                <h4>{{ trans('suppliers_import.table.document_name') }}</h4>
                            </th>
                            <th>
                                <h4>{{ trans('suppliers_import.table.total_upload') }}</h4>
                            </th>
                            <th>
                                <h4>{{ trans('suppliers_import.table.total_success') }}</h4>
                            </th>
                            <th>
                                <h4>{{ trans('suppliers_import.table.total_failure') }}</h4>
                            </th>
                            <th>
                                <h4>{{ trans('suppliers_import.table.updated_at') }}</h4>
                            </th>
                            <th>
                                <h4>{{ trans('suppliers_import.table.username') }}</h4>
                            </th>
                            <th>
                                <h4>{{ trans('suppliers_import.table.actions') }}</h4>
                            </th>
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
</section>
@endsection

@push('scripts')
<script src="{!! CommonHelper::asset('js/common.js') !!}"></script>
<script src="{!! CommonHelper::asset('js/suppliers/supplier_import.js') !!}"></script>

<script>
    var config = {};
    config.bulk_import_info = "{{ url('supplier/bulk-import-info') }}";
    config.document_download = "{{ url('uploads/bulk_documents') }}";
    config.token = "{{ csrf_token() }}";
    config.translations = {
        Download_Document: '{{ trans('    suppliers_import.config.Download_Documents ') }}',
        search: '{{ trans('    suppliers_import.config.search ') }}',
        reload: '{{ trans('    suppliers_import.config.reload ') }}',
        please_enter_valid_search: '{{ trans('    suppliers_import.config.please_enter_valid_search ') }}',
        import_file: "{{ trans('suppliers_import.config.import_file') }}",
        Please_upload_valid_xlsx: "{{ trans('suppliers_import.config.upload_valid') }}",
    };

    new MyApp(config);

    $(function() {

        var droppedFile = null;

        function showSelectedState(name) {
            $('#selectedFileName').text(name);
            $('#defaultState').hide();
            $('#selectedState').show();
            $('#dropZone').addClass('file-selected');
            $('#fileInput').css('pointer-events', 'none');
        }

        function showDefaultState() {
            droppedFile = null;
            $('#defaultState').show();
            $('#selectedState').hide();
            $('#dropZone').removeClass('file-selected');
            $('#fileInput').css('pointer-events', '').val('');
        }

        $('#fileInput').on('change', function() {
            if (this.files.length > 0) {
                droppedFile = null;
                showSelectedState(this.files[0].name);
            }
        });

        $('#btnRemove').on('click', function(e) {
            e.stopPropagation();
            showDefaultState();
        });

        $('#btnSave').on('click', function(e) {
            e.stopPropagation();

            var file = $('#fileInput')[0].files[0] || droppedFile;

            if (!file) {
                alert('No file selected.');
                return;
            }

            var ext = file.name.split('.').pop().toLowerCase();
            if (!['xlsx'].includes(ext)) {
                alert('Invalid file type. Please upload an .xlsx file.');
                return;
            }

            var dt = new DataTransfer();
            dt.items.add(file);
            $('#hiddenFileInput')[0].files = dt.files;

            $('#importForm').submit();
        });

        $('#dropZone').on('dragover', function(e) {
            e.preventDefault();
            $(this).addClass('dragover');
        });

        $('#dropZone').on('dragleave', function() {
            $(this).removeClass('dragover');
        });

        $('#dropZone').on('drop', function(e) {
            e.preventDefault();
            $(this).removeClass('dragover');

            var files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                var ext = files[0].name.split('.').pop().toLowerCase();
                if (!['xlsx'].includes(ext)) {
                    alert('Invalid file type. Please drop an .xlsx file.');
                    return;
                }
                droppedFile = files[0];
                showSelectedState(files[0].name);
            }
        });

        var expanded = false;

        $('#readMoreBtn').on('click', function(e) {
            e.preventDefault();
            expanded = !expanded;

            if (expanded) {
                $('#cardScrollBody').addClass('expanded');
                $(this).text('Read Less');
            } else {
                $('#cardScrollBody').removeClass('expanded');
                $(this).text('Read More');
                $('html, body').animate({
                    scrollTop: $('.instructions-card').offset().top - 20
                }, 300);
            }
        });

    });
</script>
@endpush
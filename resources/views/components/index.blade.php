{{-- @page-meta
{
  "page_no": "USR01L-26",
  "file": "index.blade.php",
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
      "from": "2026-05",
      "reviewer": null,
      "description": "Changes for the off white color text for dark mode and url changes for the edit user image"
    }
  ]
}
--}}
@extends('layouts.layout1')
@section('title', trans('user.list.title'))

@section('content')
    <main class="main-content" id="mainContent">
        <div class="container-fluid px-0">
            <div class="header-actions-wrapper d-flex align-items-center justify-content-between px-4">
                <div class="mb-0">
                    <h2>{{ trans('components.view.page_heading') }}</h2>
                </div>
                <div class="d-flex gap-8">
                    {{-- Filter --}}
                    <button class="header-icon-btn header-icon-btn-sm btn-open-filter" type="button" data-bs-toggle="tooltip"
                        title="{{ trans('user.user_toolbar.filter') }}">
                        <svg viewBox="0 0 20 18" fill="none">
                            <path
                                d="M19.3658 0.893462C19.2504 0.626753 19.0591 0.399864 18.8157 0.241013C18.5723 0.0821617 18.2877 -0.00164211 17.997 2.43793e-05H1.49705C1.20673 0.000596404 0.922821 0.0854027 0.679756 0.244155C0.436692 0.402908 0.244919 0.628785 0.127702 0.894384C0.0104851 1.15998 -0.0271385 1.45389 0.0193952 1.74045C0.065929 2.02701 0.19462 2.29391 0.389858 2.50877L0.397358 2.51721L6.74705 9.29721V16.5C6.74698 16.7715 6.8206 17.0379 6.96004 17.2708C7.09948 17.5038 7.29953 17.6945 7.53885 17.8227C7.77816 17.9508 8.04778 18.0117 8.31894 17.9986C8.59011 17.9856 8.85266 17.8993 9.07861 17.7488L12.0786 15.7481C12.2843 15.6112 12.4529 15.4255 12.5695 15.2076C12.6861 14.9898 12.7471 14.7465 12.747 14.4994V9.29721L19.0977 2.51721L19.1052 2.50877C19.3025 2.29489 19.4324 2.02764 19.4788 1.74037C19.5251 1.4531 19.4858 1.15854 19.3658 0.893462ZM11.4514 8.4919C11.3217 8.62945 11.2487 8.81094 11.247 9.00002V14.4994L8.24705 16.5V9.00002C8.2471 8.80958 8.17471 8.62624 8.04455 8.48721L1.49705 1.50002H17.997L11.4514 8.4919Z"
                                fill="currentColor" />
                        </svg>
                        <span class="b3-text opacity-50">{{ trans('user.user_toolbar.filter') }}</span>
                        <span class="filter-count-badge d-none" aria-label="Active filters">0</span>
                    </button>
                    {{-- Download Excel --}}
                    {{-- @if(Auth::user()->hasPermissionTo('ComponentDownload')) --}} 
                        <button class="header-icon-btn-only header-icon-btn-only-sm btn-users-export" type="button" data-bs-toggle="tooltip" title="{{ trans('components.component_toolbar.download') }} Excel">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 21.4V2.6C4 2.26863 4.26863 2 4.6 2H16.2515C16.4106 2 16.5632 2.06321 16.6757 2.17574L19.8243 5.32426C19.9368 5.43679 20 5.5894 20 5.74853V21.4C20 21.7314 19.7314 22 19.4 22H4.6C4.26863 22 4 21.7314 4 21.4Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8 10L16 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8 18L16 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8 14L12 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M16 2V5.4C16 5.73137 16.2686 6 16.6 6H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    {{-- @endif  --}}

                    {{-- Download pdf --}}
                    {{--@if(Auth::user()->hasPermissionTo('ComponentDownload')) --}}
                        <button class="header-icon-btn-only header-icon-btn-only-sm btn-users-export-pdf" type="button" data-bs-toggle="tooltip" title="{{ trans('components.component_toolbar.download_pdf') }}">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 21.4V2.6C4 2.26863 4.26863 2 4.6 2H16.2515C16.4106 2 16.5632 2.06321 16.6757 2.17574L19.8243 5.32426C19.9368 5.43679 20 5.5894 20 5.74853V21.4C20 21.7314 19.7314 22 19.4 22H4.6C4.26863 22 4 21.7314 4 21.4Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M16 2V5.4C16 5.73137 16.2686 6 16.6 6H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    {{-- @endif --}}
                    {{-- Bulk Import --}}
                    <!-- @if(Auth::user()->hasPermissionTo('UserImport')) -->
                        <button class="header-icon-btn-only header-icon-btn-only-sm btn-users-import" type="button" data-bs-toggle="tooltip" title="{{ trans('components.component_toolbar.import_component') }}">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 20L18 20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 16V4M12 4L15.5 7.5M12 4L8.5 7.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    <!-- @endif -->

                    {{-- Bulk Update  --}}
                    <!-- @if(Auth::user()->hasPermissionTo('UserBulkUpdate')) -->
                        <button class="header-icon-btn-only header-icon-btn-only-sm btn-users-update" type="button" data-bs-toggle="tooltip" title="{{ trans('components.component_toolbar.update_bulk_component') }}">
                            <svg viewBox="0 0 19 19" fill="none" ><path d="M18.735 1.42007C18.7128 1.05438 18.5575 0.709463 18.2984 0.450401C18.0394 0.191339 17.6945 0.0360463 17.3288 0.0138229C16.1494 -0.0564896 13.1353 0.0513228 10.6341 2.55164L10.1888 3.00257H4.72033C4.52259 3.00146 4.3266 3.03967 4.14377 3.115C3.96093 3.19032 3.7949 3.30125 3.65533 3.44132L0.439701 6.65882C0.242496 6.85589 0.104115 7.10403 0.0400901 7.37537C-0.0239351 7.64671 -0.0110716 7.93053 0.0772373 8.19497C0.165546 8.45941 0.325805 8.69401 0.540029 8.87243C0.754253 9.05085 1.01396 9.16605 1.29001 9.20507L4.89658 9.70851L9.04126 13.8532L9.5447 17.4616C9.58342 17.7377 9.69858 17.9974 9.87717 18.2115C10.0558 18.4255 10.2907 18.5853 10.5553 18.6729C10.7095 18.7244 10.8709 18.7507 11.0335 18.7507C11.2302 18.7511 11.4252 18.7125 11.607 18.6372C11.7888 18.5619 11.9539 18.4514 12.0928 18.312L15.3103 15.0963C15.4504 14.9568 15.5613 14.7907 15.6367 14.6079C15.712 14.4251 15.7502 14.2291 15.7491 14.0313V8.56289L16.1963 8.1157C18.6975 5.61445 18.8053 2.60039 18.735 1.42007ZM4.72033 4.50257H8.68876L4.98283 8.20757L1.49908 7.72195L4.72033 4.50257ZM11.6963 3.61664C12.4169 2.89151 13.2841 2.32852 14.2397 1.96525C15.1953 1.60198 16.2174 1.4468 17.2378 1.51007C17.3035 2.53099 17.15 3.55415 16.7874 4.51079C16.4249 5.46743 15.8618 6.33538 15.136 7.05632L9.74908 12.4413L6.31033 9.00257L11.6963 3.61664ZM14.2491 14.0313L11.0306 17.2526L10.5441 13.7679L14.2491 10.0629V14.0313ZM7.29658 14.9219C6.8747 15.8463 5.46376 18.0026 1.49908 18.0026C1.30016 18.0026 1.1094 17.9236 0.968746 17.7829C0.828094 17.6423 0.749076 17.4515 0.749076 17.2526C0.749076 13.2879 2.90533 11.8769 3.8297 11.4541C3.91933 11.4133 4.01613 11.3904 4.11458 11.387C4.21302 11.3835 4.31119 11.3995 4.40346 11.4339C4.49574 11.4684 4.58033 11.5207 4.65239 11.5879C4.72445 11.6551 4.78258 11.7358 4.82345 11.8254C4.86433 11.915 4.88715 12.0118 4.89061 12.1103C4.89407 12.2087 4.87811 12.3069 4.84364 12.3992C4.80917 12.4914 4.75685 12.576 4.68969 12.6481C4.62253 12.7201 4.54183 12.7783 4.4522 12.8191C3.84939 13.0938 2.51908 13.9779 2.2847 16.4669C4.77376 16.2326 5.6597 14.9023 5.93251 14.2994C5.97339 14.2098 6.03152 14.1291 6.10358 14.062C6.17564 13.9948 6.26022 13.9425 6.3525 13.908C6.44478 13.8735 6.54294 13.8576 6.64139 13.861C6.73984 13.8645 6.83664 13.8873 6.92626 13.9282C7.01589 13.9691 7.09659 14.0272 7.16375 14.0993C7.23092 14.1713 7.28323 14.2559 7.3177 14.3482C7.35217 14.4405 7.36814 14.5386 7.36467 14.6371C7.36121 14.7355 7.33839 14.8323 7.29751 14.9219H7.29658Z" fill="currentColor" /></svg>
                        </button>
                    <!-- @endif -->

                    {{-- Show Deleted --}}
                    @if(Auth::user()->hasPermissionTo('ComponentDelete'))
                        <button class="header-icon-btn-only header-icon-btn-only-sm btn-show-users" type="button" data-bs-toggle="tooltip" title="{{ trans('components.component_toolbar.show_deleted_component') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="17" viewBox="0 0 22 17" fill="none"><mask id="path-1-inside-1_2605_28551" fill="white"><path d="M13.7256 14.1051H16.6418C17.041 14.1051 17.3833 13.9631 17.6684 13.6789C17.9536 13.3948 18.097 13.054 18.0987 12.6565V7.14607H19.085V6.07143H16.2653V5.32464H14.102V6.07143H11.2823V7.14607H12.2687V12.6565C12.2687 13.0532 12.4117 13.3936 12.6977 13.6777C12.9837 13.9619 13.3267 14.1039 13.7268 14.1039M13.3503 7.14607H17.017V12.6565C17.017 12.7658 16.9816 12.8552 16.9107 12.9249C16.8398 12.9945 16.7501 13.0297 16.6418 13.0305H13.7256C13.6156 13.0305 13.5251 12.9953 13.4542 12.9249C13.3841 12.8552 13.3491 12.7658 13.3491 12.6565L13.3503 7.14607ZM1.97511 17C1.41207 17 0.942333 16.813 0.565889 16.439C0.189444 16.065 0.000814815 15.5979 0 15.0377V1.96229C0 1.4029 0.18863 0.936214 0.565889 0.562214C0.943148 0.188214 1.41248 0.000809524 1.97389 0H8.06178L10.5062 2.42857H20.0261C20.5883 2.42857 21.0581 2.61598 21.4353 2.99078C21.8126 3.36559 22.0008 3.83229 22 4.39086V15.0389C22 15.5975 21.8118 16.0642 21.4353 16.439C21.0589 16.8138 20.5891 17.0008 20.0261 17H1.97511ZM1.97511 15.7857H20.0261C20.2453 15.7857 20.4254 15.7157 20.5663 15.5756C20.7073 15.4356 20.7778 15.2567 20.7778 15.0389V4.38964C20.7778 4.17188 20.7073 3.99298 20.5663 3.85293C20.4254 3.71288 20.2453 3.64286 20.0261 3.64286H10.0161L7.57167 1.21429H1.97389C1.7547 1.21429 1.57463 1.28431 1.43367 1.42436C1.2927 1.5644 1.22222 1.74371 1.22222 1.96229V15.0389C1.22222 15.2567 1.2927 15.4356 1.43367 15.5756C1.57463 15.7157 1.75511 15.7857 1.97511 15.7857Z"/></mask><path d="M13.7256 14.1051H16.6418C17.041 14.1051 17.3833 13.9631 17.6684 13.6789C17.9536 13.3948 18.097 13.054 18.0987 12.6565V7.14607H19.085V6.07143H16.2653V5.32464H14.102V6.07143H11.2823V7.14607H12.2687V12.6565C12.2687 13.0532 12.4117 13.3936 12.6977 13.6777C12.9837 13.9619 13.3267 14.1039 13.7268 14.1039M13.3503 7.14607H17.017V12.6565C17.017 12.7658 16.9816 12.8552 16.9107 12.9249C16.8398 12.9945 16.7501 13.0297 16.6418 13.0305H13.7256C13.6156 13.0305 13.5251 12.9953 13.4542 12.9249C13.3841 12.8552 13.3491 12.7658 13.3491 12.6565L13.3503 7.14607ZM1.97511 17C1.41207 17 0.942333 16.813 0.565889 16.439C0.189444 16.065 0.000814815 15.5979 0 15.0377V1.96229C0 1.4029 0.18863 0.936214 0.565889 0.562214C0.943148 0.188214 1.41248 0.000809524 1.97389 0H8.06178L10.5062 2.42857H20.0261C20.5883 2.42857 21.0581 2.61598 21.4353 2.99078C21.8126 3.36559 22.0008 3.83229 22 4.39086V15.0389C22 15.5975 21.8118 16.0642 21.4353 16.439C21.0589 16.8138 20.5891 17.0008 20.0261 17H1.97511ZM1.97511 15.7857H20.0261C20.2453 15.7857 20.4254 15.7157 20.5663 15.5756C20.7073 15.4356 20.7778 15.2567 20.7778 15.0389V4.38964C20.7778 4.17188 20.7073 3.99298 20.5663 3.85293C20.4254 3.71288 20.2453 3.64286 20.0261 3.64286H10.0161L7.57167 1.21429H1.97389C1.7547 1.21429 1.57463 1.28431 1.43367 1.42436C1.2927 1.5644 1.22222 1.74371 1.22222 1.96229V15.0389C1.22222 15.2567 1.2927 15.4356 1.43367 15.5756C1.57463 15.7157 1.75511 15.7857 1.97511 15.7857Z" fill="currentColor"/><path d="M1.22222 15.7857H1.72222V1.21429H1.22222H0.722222V15.7857H1.22222ZM18.0987 12.6565L19.0987 12.6606V12.6565H18.0987ZM18.0987 7.14607V6.14607H17.0987V7.14607H18.0987ZM19.085 7.14607V8.14607H20.085V7.14607H19.085ZM19.085 6.07143H20.085V5.07143H19.085V6.07143ZM16.2653 6.07143H15.2653V7.07143H16.2653V6.07143ZM16.2653 5.32464H17.2653V4.32464H16.2653V5.32464ZM14.102 5.32464V4.32464H13.102V5.32464H14.102ZM14.102 6.07143V7.07143H15.102V6.07143H14.102ZM11.2823 6.07143V5.07143H10.2823V6.07143H11.2823ZM11.2823 7.14607H10.2823V8.14607H11.2823V7.14607ZM12.2687 7.14607H13.2687V6.14607H12.2687V7.14607ZM13.3503 7.14607V6.14607H12.3506L12.3503 7.14585L13.3503 7.14607ZM17.017 7.14607H18.017V6.14607H17.017V7.14607ZM16.6418 13.0305V14.0305L16.6492 14.0305L16.6418 13.0305ZM13.4542 12.9249L12.7494 13.6343L13.4542 12.9249ZM13.3491 12.6565L12.3491 12.6563V12.6565H13.3491ZM0 15.0377H-1L-0.999999 15.0392L0 15.0377ZM1.97389 0V-1L1.97245 -0.999999L1.97389 0ZM8.06178 0L8.76658 -0.709406L8.47408 -1H8.06178V0ZM10.5062 2.42857L9.80142 3.13798L10.0939 3.42857H10.5062V2.42857ZM22 4.39086L21 4.3894V4.39086H22ZM20.0261 17L20.0275 16H20.0261V17ZM1.97511 15.7857V14.7857V15.7857ZM20.7778 15.0389H19.7778H20.7778ZM20.0261 3.64286V2.64286V3.64286ZM10.0161 3.64286L9.31131 4.35226L9.6038 4.64286H10.0161V3.64286ZM7.57167 1.21429L8.27647 0.504879L7.98397 0.214285H7.57167V1.21429ZM1.22222 1.96229H0.222222H1.22222ZM13.7256 14.1051V15.1051H16.6418V14.1051V13.1051H13.7256V14.1051ZM16.6418 14.1051V15.1051C17.3081 15.1051 17.9034 14.8565 18.3743 14.3873L17.6684 13.6789L16.9626 12.9705C16.8631 13.0697 16.774 13.1051 16.6418 13.1051V14.1051ZM17.6684 13.6789L18.3743 14.3873C18.8441 13.9192 19.0959 13.3263 19.0987 12.6606L18.0987 12.6565L17.0987 12.6524C17.0981 12.7817 17.0632 12.8703 16.9626 12.9705L17.6684 13.6789ZM18.0987 12.6565H19.0987V7.14607H18.0987H17.0987V12.6565H18.0987ZM18.0987 7.14607V8.14607H19.085V7.14607V6.14607H18.0987V7.14607ZM19.085 7.14607H20.085V6.07143H19.085H18.085V7.14607H19.085ZM19.085 6.07143V5.07143H16.2653V6.07143V7.07143H19.085V6.07143ZM16.2653 6.07143H17.2653V5.32464H16.2653H15.2653V6.07143H16.2653ZM16.2653 5.32464V4.32464H14.102V5.32464V6.32464H16.2653V5.32464ZM14.102 5.32464H13.102V6.07143H14.102H15.102V5.32464H14.102ZM14.102 6.07143V5.07143H11.2823V6.07143V7.07143H14.102V6.07143ZM11.2823 6.07143H10.2823V7.14607H11.2823H12.2823V6.07143H11.2823ZM11.2823 7.14607V8.14607H12.2687V7.14607V6.14607H11.2823V7.14607ZM12.2687 7.14607H11.2687V12.6565H12.2687H13.2687V7.14607H12.2687ZM12.2687 12.6565H11.2687C11.2687 13.3244 11.5214 13.9187 11.9929 14.3871L12.6977 13.6777L13.4025 12.9683C13.3019 12.8684 13.2687 12.782 13.2687 12.6565H12.2687ZM12.6977 13.6777L11.9929 14.3871C12.4646 14.8558 13.0603 15.1039 13.7268 15.1039V14.1039V13.1039C13.5931 13.1039 13.5027 13.0679 13.4025 12.9683L12.6977 13.6777ZM13.3503 7.14607V8.14607H17.017V7.14607V6.14607H13.3503V7.14607ZM17.017 7.14607H16.017V12.6565H17.017H18.017V7.14607H17.017ZM17.017 12.6565H16.017C16.017 12.602 16.0261 12.5245 16.0607 12.4372C16.0961 12.3479 16.1492 12.2711 16.21 12.2114L16.9107 12.9249L17.6114 13.6383C17.8938 13.3609 18.017 13.0046 18.017 12.6565H17.017ZM16.9107 12.9249L16.21 12.2114C16.2673 12.155 16.3403 12.1059 16.4246 12.0727C16.5073 12.0402 16.5811 12.0309 16.6343 12.0305L16.6418 13.0305L16.6492 14.0305C16.9913 14.0279 17.3377 13.9071 17.6114 13.6383L16.9107 12.9249ZM16.6418 13.0305V12.0305H13.7256V13.0305V14.0305H16.6418V13.0305ZM13.7256 13.0305V12.0305C13.7783 12.0305 13.8532 12.0392 13.938 12.0722C14.0247 12.106 14.0999 12.1567 14.159 12.2154L13.4542 12.9249L12.7494 13.6343C13.0274 13.9104 13.3807 14.0305 13.7256 14.0305V13.0305ZM13.4542 12.9249L14.159 12.2154C14.2197 12.2757 14.2719 12.3525 14.3065 12.4408C14.3403 12.5272 14.3491 12.6034 14.3491 12.6565H13.3491H12.3491C12.3491 13.001 12.4694 13.3561 12.7494 13.6343L13.4542 12.9249ZM13.3491 12.6565L14.3491 12.6567L14.3503 7.14629L13.3503 7.14607L12.3503 7.14585L12.3491 12.6563L13.3491 12.6565ZM1.97511 17V16C1.65829 16 1.44748 15.9052 1.27069 15.7296L0.565889 16.439L-0.138911 17.1484C0.437186 17.7208 1.16586 18 1.97511 18V17ZM0.565889 16.439L1.27069 15.7296C1.0942 15.5542 1.00045 15.3471 0.999999 15.0363L0 15.0377L-0.999999 15.0392C-0.998821 15.8487 -0.715307 16.5758 -0.138911 17.1484L0.565889 16.439ZM0 15.0377H1V1.96229H0H-1V15.0377H0ZM0 1.96229H1C1 1.65388 1.09297 1.4478 1.26992 1.27238L0.565889 0.562214L-0.138144 -0.147954C-0.715713 0.424626 -1 1.15193 -1 1.96229H0ZM0.565889 0.562214L1.26992 1.27238C1.4488 1.09505 1.66055 1.00045 1.97533 0.999999L1.97389 0L1.97245 -0.999999C1.16441 -0.998834 0.437498 -0.718622 -0.138144 -0.147954L0.565889 0.562214ZM1.97389 0V1H8.06178V0V-1H1.97389V0ZM8.06178 0L7.35698 0.709406L9.80142 3.13798L10.5062 2.42857L11.211 1.71916L8.76658 -0.709406L8.06178 0ZM10.5062 2.42857V3.42857H20.0261V2.42857V1.42857H10.5062V2.42857ZM20.0261 2.42857V3.42857C20.341 3.42857 20.5522 3.523 20.7305 3.70019L21.4353 2.99078L22.1401 2.28138C21.564 1.70895 20.8357 1.42857 20.0261 1.42857V2.42857ZM21.4353 2.99078L20.7305 3.70019C20.9081 3.87663 21.0004 4.0827 21 4.3894L22 4.39086L23 4.39232C23.0012 3.58187 22.7171 2.85456 22.1401 2.28138L21.4353 2.99078ZM22 4.39086H21V15.0389H22H23V4.39086H22ZM22 15.0389H21C21 15.3468 20.9072 15.5537 20.7298 15.7304L21.4353 16.439L22.1409 17.1476C22.7164 16.5747 23 15.8482 23 15.0389H22ZM21.4353 16.439L20.7298 15.7304C20.5532 15.9061 20.3432 16.0005 20.0275 16L20.0261 17L20.0247 18C20.8351 18.0012 21.5646 17.7215 22.1409 17.1476L21.4353 16.439ZM20.0261 17V16H1.97511V17V18H20.0261V17ZM1.97511 15.7857V16.7857H20.0261V15.7857V14.7857H1.97511V15.7857ZM20.0261 15.7857V16.7857C20.4795 16.7857 20.9227 16.6313 21.2711 16.285L20.5663 15.5756L19.8615 14.8662C19.8858 14.8421 19.9213 14.8175 19.9631 14.8012C20.0031 14.7856 20.0283 14.7857 20.0261 14.7857V15.7857ZM20.5663 15.5756L21.2711 16.285C21.6201 15.9383 21.7778 15.4949 21.7778 15.0389H20.7778H19.7778C19.7778 15.0394 19.7777 15.0326 19.78 15.02C19.7823 15.0072 19.7866 14.9899 19.7945 14.9699C19.8117 14.9264 19.8373 14.8903 19.8615 14.8662L20.5663 15.5756ZM20.7778 15.0389H21.7778V4.38964H20.7778H19.7778V15.0389H20.7778ZM20.7778 4.38964H21.7778C21.7778 3.93365 21.6201 3.49022 21.2711 3.14352L20.5663 3.85293L19.8615 4.56233C19.8373 4.53823 19.8117 4.50222 19.7945 4.45869C19.7866 4.43864 19.7823 4.42137 19.78 4.40855C19.7777 4.39595 19.7778 4.38917 19.7778 4.38964H20.7778ZM20.5663 3.85293L21.2711 3.14352C20.9227 2.79732 20.4795 2.64286 20.0261 2.64286V3.64286V4.64286C20.0283 4.64286 20.0031 4.64293 19.9631 4.62739C19.9213 4.6111 19.8858 4.58645 19.8615 4.56233L20.5663 3.85293ZM20.0261 3.64286V2.64286H10.0161V3.64286V4.64286H20.0261V3.64286ZM10.0161 3.64286L10.7209 2.93345L8.27647 0.504879L7.57167 1.21429L6.86687 1.92369L9.31131 4.35226L10.0161 3.64286ZM7.57167 1.21429V0.214285H1.97389V1.21429V2.21429H7.57167V1.21429ZM1.97389 1.21429V0.214285C1.52047 0.214285 1.07734 0.368742 0.728867 0.71495L1.43367 1.42436L2.13847 2.13376C2.1142 2.15788 2.07874 2.18253 2.03687 2.19882C1.99689 2.21436 1.97174 2.21429 1.97389 2.21429V1.21429ZM1.43367 1.42436L0.728867 0.71495C0.379146 1.0624 0.222222 1.50666 0.222222 1.96229H1.22222H2.22222C2.22222 1.9615 2.22207 1.98823 2.20577 2.0297C2.18872 2.07306 2.16311 2.10928 2.13847 2.13376L1.43367 1.42436ZM1.22222 1.96229H0.222222V15.0389H1.22222H2.22222V1.96229H1.22222ZM1.22222 15.0389H0.222222C0.222222 15.4949 0.379899 15.9383 0.728867 16.285L1.43367 15.5756L2.13847 14.8662C2.16273 14.8903 2.18833 14.9264 2.20548 14.9699C2.21338 14.9899 2.21774 15.0072 2.22002 15.02C2.22225 15.0326 2.22222 15.0394 2.22222 15.0389H1.22222ZM1.43367 15.5756L0.728867 16.285C1.07808 16.632 1.52203 16.7857 1.97511 16.7857V15.7857V14.7857C1.9721 14.7857 1.99673 14.7855 2.03646 14.8009C2.07816 14.8171 2.11382 14.8417 2.13847 14.8662L1.43367 15.5756Z" fill="currentColor" mask="url(#path-1-inside-1_2605_28551)"/></svg>
                        </button>

                        <button class="header-icon-btn-only header-icon-btn-only-sm btn-show-users non-deleted-icon d-none" type="button" data-bs-toggle="tooltip" title="{{ trans('components.component_toolbar.show_non_deleted_component') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M8.5 4H6C4.89543 4 4 4.89543 4 6V20C4 21.1046 4.89543 22 6 22H12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M15.5 4H18C19.1046 4 20 4.89543 20 6V15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M8 6.4V4.5C8 4.22386 8.22386 4 8.5 4C8.77614 4 9.00422 3.77604 9.05152 3.50398C9.19968 2.65171 9.77399 1 12 1C14.226 1 14.8003 2.65171 14.9485 3.50398C14.9958 3.77604 15.2239 4 15.5 4C15.7761 4 16 4.22386 16 4.5V6.4C16 6.73137 15.7314 7 15.4 7H8.6C8.26863 7 8 6.73137 8 6.4Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M15.5 20.5L17.5 22.5L22.5 17.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>    
                        </button>
                    @endif

                {{-- Print Label --}}
                    @if(Auth::user()->hasPermissionTo('ComponentPrintLabel'))
                    <button class="header-icon-btn-only header-icon-btn-only-sm btn-print-label" type="button" data-bs-toggle="tooltip" title="{{ trans('components.component_toolbar.print_label') }}">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 6.6V8.4C9 8.73137 8.73137 9 8.4 9H6.6C6.26863 9 6 8.73137 6 8.4V6.6C6 6.26863 6.26863 6 6.6 6H8.4C8.73137 6 9 6.26863 9 6.6Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6 12H9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M15 12V15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 18H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 12.0111L12.01 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18 12.0111L18.01 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 15.0111L12.01 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18 15.0111L18.01 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18 18.0111L18.01 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 9.01111L12.01 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 6.01111L12.01 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9 15.6V17.4C9 17.7314 8.73137 18 8.4 18H6.6C6.26863 18 6 17.7314 6 17.4V15.6C6 15.2686 6.26863 15 6.6 15H8.4C8.73137 15 9 15.2686 9 15.6Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18 6.6V8.4C18 8.73137 17.7314 9 17.4 9H15.6C15.2686 9 15 8.73137 15 8.4V6.6C15 6.26863 15.2686 6 15.6 6H17.4C17.7314 6 18 6.26863 18 6.6Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18 3H21V6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18 21H21V18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6 3H3V6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6 21H3V18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    @endif


                {{-- Add Component --}}
                @if(Auth::user()->hasPermissionTo('ComponentAdd'))
                        <button class="amg-btn amg-btn-primary amg-btn-sm btn-add-user" type="button" data-bs-toggle="tooltip" title="{{ trans('components.component_toolbar.add_component') }}">
                            <svg width="19" height="19" viewBox="0 0 19 19" fill="none" ><path d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z" fill="currentColor" /></svg>
                            <span>{{ trans('components.component_toolbar.add_component') }}</span>
                    </button>
                @endif
                </div>
            </div>
            <div class="tab-pane fade show active" id="main-user-list-wrapper">
                <div class="card">
                    <div class="card-body table-responsive">
                        <div class="d-flex align-items-center gap-2 mb-1" data-select2-id="select2-data-5-1om7">
                            <div id="customLengthContainer" class="col-auto" data-select2-id="select2-data-4-sbib">
                                <select id="showSelect"
                                    class="showSelect amg-table-pagination-dropdown userModulePageLenth user-list-page-length">
                                    <option value="10" selected>{{ trans('components.view.show') }} (10)</option>
                                    <option value="25">{{ trans('components.view.show') }} (25)</option>
                                    <option value="50">{{ trans('components.view.show') }} (50)</option>
                                    <option value="100">{{ trans('components.view.show') }} (100)</option>
                                </select>
                            </div>
                            <div class="flex-grow-1"></div>
                            <div>
                                <div class="amg-list-searchbar">
                                    <svg class="amg-list-searchbar__icon" width="20" height="20"
                                        viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z"
                                            fill="currentColor"></path>
                                    </svg>
                                    <input type="text" class="amg-list-searchbar__input" id="tableSearch"
                                        placeholder="{{ trans('components.view.search') }}...">
                                </div>
                            </div>
                            <button class="amg-refresh-btn btn-reload-list">
                                <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z"
                                        fill="currentColor"></path>
                                </svg>
                                <span>{{ trans('components.view.refresh') }}</span>
                            </button>
                        </div>
                        <table id="mytable" class="mytable table display" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-center-checkbox"><input class="form-check-input" type="checkbox" value="" id="select-all"></th>
                                    <th><h4 class="text-off-white">{{ trans('components.table_headers.tag') }}</h4></th>
                                    <th><h4 class="text-off-white">{{ trans('components.table_headers.component_info') }}</h4></th>
                                    <th><h4 class="text-off-white">{{ trans('components.table_headers.status') }}</h4></th>
                                    <th><h4 class="text-off-white">{{ trans('components.table_headers.checkout_info') }}</h4></th>
                                    <th><h4 class="text-off-white">{{ trans('components.table_headers.purchase_cost') }}</h4></th>
                                    <th><h4 class="text-off-white">{{ trans('components.table_headers.origin_info') }}</h4></th>
                                    <th><h4 class="text-off-white">{{ trans('components.table_headers.updated_on') }}</h4></th>
                                    <th><h4 class="text-off-white">{{ trans('components.table_headers.actions') }}</h4></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @include('consumables.filter')
        @include('consumables.checkin_modal')
        @include('consumables.consumable-modal')
        @include('consumables.checkout-consumable-modal')
        <a id="print_label_starter" target="_blank" href="" class="hidden"></a>
        <input type="hidden" id="auth_user" value="{{ json_encode(Auth::check() ? Auth::user() : null) }}">
    </main>
@endsection

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link href="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.css') !!}" rel="stylesheet" />
<link href="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.css') !!}" rel="stylesheet" />
<style>
    .dataTables_scrollBody{
        width: unset;
    }
    #user-mdl-frm .select2-selection__clear{
        display: none;
    }
    .lable-one-line {
        min-width: 170px !important;
    }
    #user-mdl-frm #phone_country_id + .select2 .select2-selection__clear,
    #user-mdl-frm #phone2_country_id + .select2 .select2-selection__clear,
    #user-mdl-frm #work_phone_country_id + .select2 .select2-selection__clear {
        display: inline-block !important;
    }
    .text-center-checkbox {
        text-align: center;
        vertical-align: middle;
    }

    .text-center-checkbox .form-check-input {
        margin: 0 auto;
        display: block;
    }
</style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.min.js') !!}"></script>
   <script src="{!! CommonHelper::asset('js/components/index.js') !!}"></script>
   <script type="text/javascript">
    $(document).ready(function() {
		// PNotify.prototype.options.styling = "bootstrap3";
		var config = new Object;
		config.url = new Object;
		config.data_print = {};
		config.print_component_array = [];
		config.component_id_array = [];
		config.imgviewpath = "{{ url('uploads/component') }}";
		config.url.components = "{{ url('jx-components') }}";
		config.url.add = "{{ url('component/add') }}";
		config.url.edit = "{{ url('component/edit') }}";
		config.url.delete = "{{ url('component/delete') }}";
		config.url.get = "{{ url('component/get') }}";
		config.url.checkout = "{{ url('component/checkout') }}";
		config.url.checkin = "{{ url('component/checkin') }}";
		config.url.info = "{{ url('component/info') }}";
		config.url.device_info = "{{ url('device/info') }}";
		config.url.user_info = "{{ url('user/info') }}";
		config.url.export_components = "{{ url('export-components') }}";
		config.url.export_components_pdf = "{{ url('export-components-pdf') }}";
		config.url.import_components = "{{ url('components/import') }}";
		config.url.bulk_update = "{{ url('components/bulk/update') }}";
		config.url.bulk_checkout = "{{ url('components/bulk/checkout') }}";
		config.url.bulk_checkin = "{{ url('components/bulk/checkin') }}";
		config.url.restore = "{{ url('component/restore') }}";
		config.url.device = "{{ url('getDeviceForDropDown') }}";
		config.url.getAssetDepartments = "{{ route('getAssetDepartments') }}";
		config.getCustomFieldsByCategory = "{{ url('getCustomFieldsByCategory') }}";
		config.location_filter = "{{ $location_filter }}";
		config.department_filter = "{{ $department_filter }}";
		config.category_id_filter = {!! json_encode($category_id)!!};
	    config.getUserByAjax = "{{ url('getUserForDropDown') }}"; 
		config.ajaxGetInternalPlace = "{{url('ajaxGetInternalPlace')}}",
		config.getInternalPlaceByAjax = "{{ url('getInternalPlaceByAjax') }}";
		config.getInternalPlaceByLocation = "{{ url('getInternalPlaceByLocation') }}";
		config.getPredefinedDropdownByQuery = "{{url('getByCustomDropDown')}}"  
    	config.token = "{{ csrf_token() }}";
		config.translations = {
			edit_component: '{{ trans('content.component_fields.edit_component') }}',
			clone_component: '{{ trans('content.component_fields.clone_component') }}',
			delete_component: '{{ trans('content.component_fields.delete_component') }}',
			add_new_component: '{{ trans('content.component_fields.add_new_component') }}',
			save: '{{ trans('button.save') }}',
			edit: '{{ trans('button.edit') }}',
			something_went_wrong: '{{ trans('content.component_fields.something_went_wrong') }}',
			please_enter_valid_search: '{{ trans('content.component_fields.please_enter_valid_search') }}',
			No_Filter:'{{ trans('content.component_fields.No_Filter') }}',
			restore_component:'{{ trans('content.component_fields.restore_component') }}',
			no_components:'{{ trans('content.component_fields.no_components') }}',
			show_deleted:'{{ trans('content.component_fields.show_deleted') }}',
			show_non_delete:'{{ trans('content.component_fields.show_non_delete') }}',
			Select_the_Device:'{{ trans('content.component_fields.Select_the_Device') }}',
			are_you_want_restore:'{{ trans('content.component_fields.are_you_want_restore') }}',
			are_you_want_delete:'{{ trans('content.component_fields.are_you_want_delete') }}',
			Select_Status:'{{ trans('content.component_fields.Select_Status') }}',
			Choose_any_option:'{{ trans('content.component_fields.Choose_any_option') }}',
			Select_Company:'{{ trans('content.component_fields.Select_Company') }}',
			Select_Category:'{{ trans('content.component_fields.Select_Category') }}',
			Select_Currency_Format:'{{ trans('content.component_fields.Select_Currency_Format') }}',
			Select_Location:'{{ trans('content.component_fields.Select_Location') }}',
			select_internal_place: '{{ trans('content.component_fields.select_internal_place') }}',
			Select_Supplier:'{{ trans('content.component_fields.Select_Supplier') }}',
			Select_Category:'{{ trans('content.component_fields.Select_Category') }}',
			Select_the_Purchase_Reference:'{{ trans('content.component_fields.Select_the_Purchase_Reference') }}',
			component_name:'{{ trans('content.component_fields.component_name') }}',
			Component_Manufacturer:'{{ trans('content.component_fields.Component_Manufacturer') }}',
			Component_Serial:'{{ trans('content.component_fields.Component_Serial') }}',
			category:'{{ trans('content.component_fields.category') }}',
			company_name:'{{ trans('content.component_fields.company_name') }}',
			Location_Name:'{{ trans('content.component_fields.Location_Name') }}',
			Department_Name:'{{ trans('content.component_fields.Department_Name') }}',
			device_tag:'{{ trans('content.component_fields.device_tag') }}',
			checked_out_date:'{{ trans('content.component_fields.checked_out_date') }}',
			expected_checkIn_date:'{{ trans('content.component_fields.expected_checkIn_date') }}',
			Invoice:'{{ trans('content.component_fields.Invoice') }}',
			press_enter_with_Search: '{{ trans('content.user_fields.press_enter_with_Search') }}',
			Reload:'{{ trans('content.user_fields.Reload') }}',
			Select_the_Purchase_Invoice: '{{ trans('content.accessory_fields.Select_the_Purchase_Invoice') }}',
			Download_Document:'{{ trans('content.user_fields.Download_Document') }}',
			Select_Device: '{{ trans('content.filter_heading.filter_by_assigned_device') }}',
			view_component:'{{ trans('content.component_fields.view_component') }}',
			save_component:'{{ trans('content.component_fields.save_component') }}'	,
			Purchase_Date:'{{ trans('content.component_fields.Purchase_Date') }}',
			Search:'{{ trans('content.user_fields.Search') }}',			
			select_the_device:'{{trans('content.component_fields.select_the_device')}}',
			select_the_model: '{{trans('content.component_fields.select_the_model')}}',
			select_the_internal_place: '{{trans('content.component_fields.select_the_internal_place')}}',
			select_the_project: '{{trans('content.component_fields.select_the_project')}}',
			select_the_contract:'{{trans('content.component_fields.select_the_contract')}}',
			select_the_component:'{{trans('content.component_fields.select_the_component')}}',
			select_the_license: '{{trans('content.component_fields.select_the_license')}}',
			select_the_task: '{{trans('content.component_fields.select_the_task')}}',
			select_the_change_management: '{{trans('content.component_fields.select_the_change_management')}}',
			select_the_manufacturer:'{{ trans('content.component_fields.Select_Manufacturer') }}',
			select_the_ticket:'{{trans('content.component_fields.select_the_ticket')}}',
			select_the_ticket_procure_request: '{{trans('content.component_fields.select_the_ticket_procure_request')}}',
			Select_the_User: '{{trans('content.component_fields.select_the_user')}}',
			select_the_location: '{{trans('content.component_fields.select_the_location')}}',
			select_the_supplier: '{{trans('content.component_fields.select_the_supplier')}}',
			select_the_department: '{{trans('content.component_fields.select_the_department')}}',
			component: '{{trans('content.component_fields.component')}}',
			has_been_added: '{{trans('content.component_fields.has_been_added')}}',
            add_for_label_print: '{{trans('content.component_fields.add_for_label_print')}}',
			Please_select_atleast_one_component: '{{trans('content.component_fields.Please_select_atleast_one_component')}}',
			select_print_Option: '{{trans('content.component_fields.select_print_Option')}}',
			unable_to_add_device_label_print: '{{trans('content.component_fields.unable_to_add_device_label_print')}}',
			is_already_added_in_merge_list: '{{trans('content.component_fields.is_already_added_in_merge_list')}}',
			print_label: '{{trans('content.component_fields.print_label')}}'
		};
			
		config.companies = {!! json_encode($companies) !!};
		config.categories = {!! json_encode($categories) !!};
		config.f_location = {!! json_encode($location) !!};
		config.currencies = {!! json_encode($currencies) !!};
		config.internalPlaces = {!! json_encode($internalPlaces) !!};
		config.statusOptions = {!! json_encode($component->statusOptions) !!};
		config.orginFromOptions = {!! json_encode($component->orginFromOptions) !!};
		config.permissions = {!! json_encode($permissionArray) !!};
		config.getLocationByAjax = "{{ url('getLocationByQuery') }}";
		config.getInternalPlaceByAjax = "{{ url('getInternalPlaceByAjax') }}";
		config.getSupplierByAjax = "{{ url('getSupplierByQuery') }}";
		config.getCategoryByAjax = "{{ url('getCategoryByQuery') }}";
		config.getDeviceByAjax = "{{ url('getDeviceForDropDown') }}";
		config.getDeviceForCheckoutDropDown = "{{ url('getDeviceForCheckoutDropDown') }}";
		config.getInvoiceByAjax = "{{ url('getInvoiceByQuery') }}";
		config.default_currency_format = "{{ CommonHelper::settings()->default_currency }}";
		config.sort_fields = {!! json_encode($sort_fields) !!};
		config.purchaseFilter = {!! json_encode($purchaseFilter) !!};
		config.url.print_component_barcode = "{{ url('print-component-barcode') }}";
		config.url.print_component_onecol = "{{ url('print-component-one-col') }}";
		config.url.print_component_twocol = "{{ url('print-component-two-col') }}";
		config.url.print_verticalcol = "{{ url('print-component-vertical-col') }}";
		
		config.custom_fields = {!! !empty($companyFieldset->fields) ? json_encode(CommonHelper::formCustomFields($companyFieldset->fields)) : json_encode('') !!};
		config.sort_dir = {id:10,dir:2};
		new MyApp(config);
    });
</script>
@endpush

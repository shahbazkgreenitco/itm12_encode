{{--
/**
------------------------------------------------------------
File: index.blade.php
Module: Accessories
ACC/26/04
------------------------------------------------------------
Version: 1.0.0
Author: Safdar Ali
Page ID: #ACC-001
Created On: 2026-04-27
Reviewed By: -
------------------------------------------------------------
Change Log:
[1.0.0] - Initial version
------------------------------------------------------------
*/
--}}
@extends('layouts.layout1')
@section('title', trans('content.accessory_fields.accessories'))
@section('content')
<section class="content">
    <div id="main-user-list-wrapper">
        <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
            <h3 class="h3-text mb-0">{{ trans('accessories.accessory_fields.accessories') }}</h3>
            <div class="d-flex gap-8">
                {{-- Filter --}}
                <button class="header-icon-btn header-icon-btn-sm btn-open-filter" type="button"
                    data-bs-toggle="tooltip" title="{{ trans('accessories.accessory_fields.Filter') }}">
                    <svg viewBox="0 0 20 18" fill="none">
                        <path
                            d="M19.3658 0.893462C19.2504 0.626753 19.0591 0.399864 18.8157 0.241013C18.5723 0.0821617 18.2877 -0.00164211 17.997 2.43793e-05H1.49705C1.20673 0.000596404 0.922821 0.0854027 0.679756 0.244155C0.436692 0.402908 0.244919 0.628785 0.127702 0.894384C0.0104851 1.15998 -0.0271385 1.45389 0.0193952 1.74045C0.065929 2.02701 0.19462 2.29391 0.389858 2.50877L0.397358 2.51721L6.74705 9.29721V16.5C6.74698 16.7715 6.8206 17.0379 6.96004 17.2708C7.09948 17.5038 7.29953 17.6945 7.53885 17.8227C7.77816 17.9508 8.04778 18.0117 8.31894 17.9986C8.59011 17.9856 8.85266 17.8993 9.07861 17.7488L12.0786 15.7481C12.2843 15.6112 12.4529 15.4255 12.5695 15.2076C12.6861 14.9898 12.7471 14.7465 12.747 14.4994V9.29721L19.0977 2.51721L19.1052 2.50877C19.3025 2.29489 19.4324 2.02764 19.4788 1.74037C19.5251 1.4531 19.4858 1.15854 19.3658 0.893462ZM11.4514 8.4919C11.3217 8.62945 11.2487 8.81094 11.247 9.00002V14.4994L8.24705 16.5V9.00002C8.2471 8.80958 8.17471 8.62624 8.04455 8.48721L1.49705 1.50002H17.997L11.4514 8.4919Z"
                            fill="currentColor" />
                    </svg>
                    <span class="b3-text opacity-50">{{ trans('accessories.accessory_fields.Filter') }}</span>
                    <span class="filter-count-badge d-none" aria-label="Active filters">0</span>
                </button>


                {{-- Download Excel --}}
                <button class="header-icon-btn-only header-icon-btn-only-sm btn-users-export" type="button"
                    data-bs-toggle="tooltip" title="{{ trans('accessories.accessory_fields.Download') }}">
                    <svg viewBox="0 0 18 18" fill="none">
                        <path
                            d="M18 11.25V17.25C18 17.4489 17.921 17.6397 17.7803 17.7803C17.6397 17.921 17.4489 18 17.25 18H0.75C0.551088 18 0.360322 17.921 0.21967 17.7803C0.0790178 17.6397 0 17.4489 0 17.25V11.25C0 11.0511 0.0790178 10.8603 0.21967 10.7197C0.360322 10.579 0.551088 10.5 0.75 10.5C0.948912 10.5 1.13968 10.579 1.28033 10.7197C1.42098 10.8603 1.5 11.0511 1.5 11.25V16.5H16.5V11.25C16.5 11.0511 16.579 10.8603 16.7197 10.7197C16.8603 10.579 17.0511 10.5 17.25 10.5C17.4489 10.5 17.6397 10.579 17.7803 10.7197C17.921 10.8603 18 11.0511 18 11.25ZM8.46937 11.7806C8.53903 11.8504 8.62175 11.9057 8.7128 11.9434C8.80384 11.9812 8.90144 12.0006 9 12.0006C9.09856 12.0006 9.19616 11.9812 9.2872 11.9434C9.37825 11.9057 9.46097 11.8504 9.53063 11.7806L13.2806 8.03063C13.3503 7.96094 13.4056 7.87822 13.4433 7.78717C13.481 7.69613 13.5004 7.59855 13.5004 7.5C13.5004 7.40145 13.481 7.30387 13.4433 7.21283C13.4056 7.12178 13.3503 7.03906 13.2806 6.96937C13.2109 6.89969 13.1282 6.84442 13.0372 6.8067C12.9461 6.76899 12.8485 6.74958 12.75 6.74958C12.6515 6.74958 12.5539 6.76899 12.4628 6.8067C12.3718 6.84442 12.2891 6.89969 12.2194 6.96937L9.75 9.43969V0.75C9.75 0.551088 9.67098 0.360322 9.53033 0.21967C9.38968 0.0790176 9.19891 0 9 0C8.80109 0 8.61032 0.0790176 8.46967 0.21967C8.32902 0.360322 8.25 0.551088 8.25 0.75V9.43969L5.78063 6.96937C5.63989 6.82864 5.44902 6.74958 5.25 6.74958C5.05098 6.74958 4.86011 6.82864 4.71937 6.96937C4.57864 7.11011 4.49958 7.30098 4.49958 7.5C4.49958 7.69902 4.57864 7.88989 4.71937 8.03063L8.46937 11.7806Z"
                            fill="currentColor" />
                    </svg>
                </button>

                {{-- Download pdf --}}
                <button class="header-icon-btn-only header-icon-btn-only-sm btn-export-accessories-pdf" type="button"
                    data-bs-toggle="tooltip" title="{{ trans('accessories.accessory_fields.Download_PDF') }}">
                    <svg width="17" height="20" viewBox="0 0 17 20" fill="none">
                        <path
                            d="M16.2806 5.46938L11.0306 0.219375C10.9609 0.149749 10.8782 0.094539 10.7871 0.0568979C10.6961 0.0192569 10.5985 -7.72394e-05 10.5 2.31899e-07H1.5C1.10218 2.31899e-07 0.720644 0.158035 0.43934 0.43934C0.158035 0.720645 0 1.10218 0 1.5V18C0 18.3978 0.158035 18.7794 0.43934 19.0607C0.720644 19.342 1.10218 19.5 1.5 19.5H15C15.3978 19.5 15.7794 19.342 16.0607 19.0607C16.342 18.7794 16.5 18.3978 16.5 18V6C16.5001 5.90148 16.4807 5.80391 16.4431 5.71286C16.4055 5.62182 16.3503 5.53908 16.2806 5.46938ZM11.25 2.56031L13.9397 5.25H11.25V2.56031ZM15 18H1.5V1.5H9.75V6C9.75 6.19891 9.82902 6.38968 9.96967 6.53033C10.1103 6.67098 10.3011 6.75 10.5 6.75H15V18ZM11.0306 12.2194C11.1004 12.289 11.1557 12.3717 11.1934 12.4628C11.2312 12.5538 11.2506 12.6514 11.2506 12.75C11.2506 12.8486 11.2312 12.9462 11.1934 13.0372C11.1557 13.1283 11.1004 13.211 11.0306 13.2806L8.78063 15.5306C8.71097 15.6004 8.62825 15.6557 8.5372 15.6934C8.44616 15.7312 8.34856 15.7506 8.25 15.7506C8.15144 15.7506 8.05384 15.7312 7.96279 15.6934C7.87175 15.6557 7.78903 15.6004 7.71937 15.5306L5.46937 13.2806C5.32864 13.1399 5.24958 12.949 5.24958 12.75C5.24958 12.551 5.32864 12.3601 5.46937 12.2194C5.61011 12.0786 5.80098 11.9996 6 11.9996C6.19902 11.9996 6.38989 12.0786 6.53063 12.2194L7.5 13.1897V9C7.5 8.80109 7.57902 8.61032 7.71967 8.46967C7.86032 8.32902 8.05109 8.25 8.25 8.25C8.44891 8.25 8.63968 8.32902 8.78033 8.46967C8.92098 8.61032 9 8.80109 9 9V13.1897L9.96937 12.2194C10.039 12.1496 10.1217 12.0943 10.2128 12.0566C10.3038 12.0188 10.4014 11.9994 10.5 11.9994C10.5986 11.9994 10.6962 12.0188 10.7872 12.0566C10.8783 12.0943 10.961 12.1496 11.0306 12.2194Z"
                            fill="currentColor" />
                    </svg>
                </button>

                {{-- Bulk Import --}}
                <button class="header-icon-btn-only header-icon-btn-only-sm btn-import-accessory" type="button"
                    data-bs-toggle="tooltip" title="{{ trans('accessories.accessory_fields.accessory_import') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="15" viewBox="0 0 18 15" fill="none">
                        <path
                            d="M18.0006 7.50042C18.0006 7.69933 17.9216 7.8901 17.7809 8.03075C17.6403 8.1714 17.4495 8.25042 17.2506 8.25042H7.50059C7.30168 8.25042 7.11091 8.1714 6.97026 8.03075C6.82961 7.8901 6.75059 7.69933 6.75059 7.50042C6.75059 7.3015 6.82961 7.11074 6.97026 6.97009C7.11091 6.82943 7.30168 6.75042 7.50059 6.75042H17.2506C17.4495 6.75042 17.6403 6.82943 17.7809 6.97009C17.9216 7.11074 18.0006 7.3015 18.0006 7.50042ZM7.50059 2.25042H17.2506C17.4495 2.25042 17.6403 2.1714 17.7809 2.03075C17.9216 1.89009 18.0006 1.69933 18.0006 1.50042C18.0006 1.3015 17.9216 1.11074 17.7809 0.970087C17.6403 0.829435 17.4495 0.750417 17.2506 0.750417H7.50059C7.30168 0.750417 7.11091 0.829435 6.97026 0.970087C6.82961 1.11074 6.75059 1.3015 6.75059 1.50042C6.75059 1.69933 6.82961 1.89009 6.97026 2.03075C7.11091 2.1714 7.30168 2.25042 7.50059 2.25042ZM17.2506 12.7504H0.75059C0.551678 12.7504 0.360912 12.8294 0.22026 12.9701C0.0796077 13.1107 0.000589907 13.3015 0.000589907 13.5004C0.000589907 13.6993 0.0796077 13.8901 0.22026 14.0307C0.360912 14.1714 0.551678 14.2504 0.75059 14.2504H17.2506C17.4495 14.2504 17.6403 14.1714 17.7809 14.0307C17.9216 13.8901 18.0006 13.6993 18.0006 13.5004C18.0006 13.3015 17.9216 13.1107 17.7809 12.9701C17.6403 12.8294 17.4495 12.7504 17.2506 12.7504ZM0.219965 8.78104C0.28962 8.85077 0.372336 8.90609 0.463385 8.94384C0.554433 8.98158 0.652028 9.00101 0.75059 9.00101C0.849151 9.00101 0.946747 8.98158 1.0378 8.94384C1.12884 8.90609 1.21156 8.85077 1.28121 8.78104L5.03122 5.03104C5.10095 4.96139 5.15627 4.87867 5.19401 4.78762C5.23175 4.69657 5.25118 4.59898 5.25118 4.50042C5.25118 4.40186 5.23175 4.30426 5.19401 4.21321C5.15627 4.12216 5.10095 4.03945 5.03122 3.96979L1.28121 0.219792C1.14048 0.0790615 0.949613 0 0.75059 0C0.551567 0 0.360695 0.0790615 0.219965 0.219792C0.0792344 0.360523 0.000172913 0.551394 0.000172913 0.750417C0.000172913 0.94944 0.0792344 1.14031 0.219965 1.28104L3.44028 4.50042L0.219965 7.71979C0.150233 7.78945 0.0949134 7.87216 0.0571702 7.96321C0.019427 8.05426 0 8.15186 0 8.25042C0 8.34898 0.019427 8.44657 0.0571702 8.53762C0.0949134 8.62867 0.150233 8.71139 0.219965 8.78104Z"
                            fill="currentColor" />
                    </svg>
                </button>

                {{-- Show Deleted --}}
                <button class="header-icon-btn-only header-icon-btn-only-sm btn-show-users" type="button"
                    data-bs-toggle="tooltip" title="{{ trans('accessories.accessory_fields.show_deleted_accessory') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="17" viewBox="0 0 22 17" fill="none">
                        <mask id="path-1-inside-1_2605_28551" fill="white">
                            <path
                                d="M13.7256 14.1051H16.6418C17.041 14.1051 17.3833 13.9631 17.6684 13.6789C17.9536 13.3948 18.097 13.054 18.0987 12.6565V7.14607H19.085V6.07143H16.2653V5.32464H14.102V6.07143H11.2823V7.14607H12.2687V12.6565C12.2687 13.0532 12.4117 13.3936 12.6977 13.6777C12.9837 13.9619 13.3267 14.1039 13.7268 14.1039M13.3503 7.14607H17.017V12.6565C17.017 12.7658 16.9816 12.8552 16.9107 12.9249C16.8398 12.9945 16.7501 13.0297 16.6418 13.0305H13.7256C13.6156 13.0305 13.5251 12.9953 13.4542 12.9249C13.3841 12.8552 13.3491 12.7658 13.3491 12.6565L13.3503 7.14607ZM1.97511 17C1.41207 17 0.942333 16.813 0.565889 16.439C0.189444 16.065 0.000814815 15.5979 0 15.0377V1.96229C0 1.4029 0.18863 0.936214 0.565889 0.562214C0.943148 0.188214 1.41248 0.000809524 1.97389 0H8.06178L10.5062 2.42857H20.0261C20.5883 2.42857 21.0581 2.61598 21.4353 2.99078C21.8126 3.36559 22.0008 3.83229 22 4.39086V15.0389C22 15.5975 21.8118 16.0642 21.4353 16.439C21.0589 16.8138 20.5891 17.0008 20.0261 17H1.97511ZM1.97511 15.7857H20.0261C20.2453 15.7857 20.4254 15.7157 20.5663 15.5756C20.7073 15.4356 20.7778 15.2567 20.7778 15.0389V4.38964C20.7778 4.17188 20.7073 3.99298 20.5663 3.85293C20.4254 3.71288 20.2453 3.64286 20.0261 3.64286H10.0161L7.57167 1.21429H1.97389C1.7547 1.21429 1.57463 1.28431 1.43367 1.42436C1.2927 1.5644 1.22222 1.74371 1.22222 1.96229V15.0389C1.22222 15.2567 1.2927 15.4356 1.43367 15.5756C1.57463 15.7157 1.75511 15.7857 1.97511 15.7857Z" />
                        </mask>
                        <path
                            d="M13.7256 14.1051H16.6418C17.041 14.1051 17.3833 13.9631 17.6684 13.6789C17.9536 13.3948 18.097 13.054 18.0987 12.6565V7.14607H19.085V6.07143H16.2653V5.32464H14.102V6.07143H11.2823V7.14607H12.2687V12.6565C12.2687 13.0532 12.4117 13.3936 12.6977 13.6777C12.9837 13.9619 13.3267 14.1039 13.7268 14.1039M13.3503 7.14607H17.017V12.6565C17.017 12.7658 16.9816 12.8552 16.9107 12.9249C16.8398 12.9945 16.7501 13.0297 16.6418 13.0305H13.7256C13.6156 13.0305 13.5251 12.9953 13.4542 12.9249C13.3841 12.8552 13.3491 12.7658 13.3491 12.6565L13.3503 7.14607ZM1.97511 17C1.41207 17 0.942333 16.813 0.565889 16.439C0.189444 16.065 0.000814815 15.5979 0 15.0377V1.96229C0 1.4029 0.18863 0.936214 0.565889 0.562214C0.943148 0.188214 1.41248 0.000809524 1.97389 0H8.06178L10.5062 2.42857H20.0261C20.5883 2.42857 21.0581 2.61598 21.4353 2.99078C21.8126 3.36559 22.0008 3.83229 22 4.39086V15.0389C22 15.5975 21.8118 16.0642 21.4353 16.439C21.0589 16.8138 20.5891 17.0008 20.0261 17H1.97511ZM1.97511 15.7857H20.0261C20.2453 15.7857 20.4254 15.7157 20.5663 15.5756C20.7073 15.4356 20.7778 15.2567 20.7778 15.0389V4.38964C20.7778 4.17188 20.7073 3.99298 20.5663 3.85293C20.4254 3.71288 20.2453 3.64286 20.0261 3.64286H10.0161L7.57167 1.21429H1.97389C1.7547 1.21429 1.57463 1.28431 1.43367 1.42436C1.2927 1.5644 1.22222 1.74371 1.22222 1.96229V15.0389C1.22222 15.2567 1.2927 15.4356 1.43367 15.5756C1.57463 15.7157 1.75511 15.7857 1.97511 15.7857Z"
                            fill="currentColor" />
                        <path
                            d="M1.22222 15.7857H1.72222V1.21429H1.22222H0.722222V15.7857H1.22222ZM18.0987 12.6565L19.0987 12.6606V12.6565H18.0987ZM18.0987 7.14607V6.14607H17.0987V7.14607H18.0987ZM19.085 7.14607V8.14607H20.085V7.14607H19.085ZM19.085 6.07143H20.085V5.07143H19.085V6.07143ZM16.2653 6.07143H15.2653V7.07143H16.2653V6.07143ZM16.2653 5.32464H17.2653V4.32464H16.2653V5.32464ZM14.102 5.32464V4.32464H13.102V5.32464H14.102ZM14.102 6.07143V7.07143H15.102V6.07143H14.102ZM11.2823 6.07143V5.07143H10.2823V6.07143H11.2823ZM11.2823 7.14607H10.2823V8.14607H11.2823V7.14607ZM12.2687 7.14607H13.2687V6.14607H12.2687V7.14607ZM13.3503 7.14607V6.14607H12.3506L12.3503 7.14585L13.3503 7.14607ZM17.017 7.14607H18.017V6.14607H17.017V7.14607ZM16.6418 13.0305V14.0305L16.6492 14.0305L16.6418 13.0305ZM13.4542 12.9249L12.7494 13.6343L13.4542 12.9249ZM13.3491 12.6565L12.3491 12.6563V12.6565H13.3491ZM0 15.0377H-1L-0.999999 15.0392L0 15.0377ZM1.97389 0V-1L1.97245 -0.999999L1.97389 0ZM8.06178 0L8.76658 -0.709406L8.47408 -1H8.06178V0ZM10.5062 2.42857L9.80142 3.13798L10.0939 3.42857H10.5062V2.42857ZM22 4.39086L21 4.3894V4.39086H22ZM20.0261 17L20.0275 16H20.0261V17ZM1.97511 15.7857V14.7857V15.7857ZM20.7778 15.0389H19.7778H20.7778ZM20.0261 3.64286V2.64286V3.64286ZM10.0161 3.64286L9.31131 4.35226L9.6038 4.64286H10.0161V3.64286ZM7.57167 1.21429L8.27647 0.504879L7.98397 0.214285H7.57167V1.21429ZM1.22222 1.96229H0.222222H1.22222ZM13.7256 14.1051V15.1051H16.6418V14.1051V13.1051H13.7256V14.1051ZM16.6418 14.1051V15.1051C17.3081 15.1051 17.9034 14.8565 18.3743 14.3873L17.6684 13.6789L16.9626 12.9705C16.8631 13.0697 16.774 13.1051 16.6418 13.1051V14.1051ZM17.6684 13.6789L18.3743 14.3873C18.8441 13.9192 19.0959 13.3263 19.0987 12.6606L18.0987 12.6565L17.0987 12.6524C17.0981 12.7817 17.0632 12.8703 16.9626 12.9705L17.6684 13.6789ZM18.0987 12.6565H19.0987V7.14607H18.0987H17.0987V12.6565H18.0987ZM18.0987 7.14607V8.14607H19.085V7.14607V6.14607H18.0987V7.14607ZM19.085 7.14607H20.085V6.07143H19.085H18.085V7.14607H19.085ZM19.085 6.07143V5.07143H16.2653V6.07143V7.07143H19.085V6.07143ZM16.2653 6.07143H17.2653V5.32464H16.2653H15.2653V6.07143H16.2653ZM16.2653 5.32464V4.32464H14.102V5.32464V6.32464H16.2653V5.32464ZM14.102 5.32464H13.102V6.07143H14.102H15.102V5.32464H14.102ZM14.102 6.07143V5.07143H11.2823V6.07143V7.07143H14.102V6.07143ZM11.2823 6.07143H10.2823V7.14607H11.2823H12.2823V6.07143H11.2823ZM11.2823 7.14607V8.14607H12.2687V7.14607V6.14607H11.2823V7.14607ZM12.2687 7.14607H11.2687V12.6565H12.2687H13.2687V7.14607H12.2687ZM12.2687 12.6565H11.2687C11.2687 13.3244 11.5214 13.9187 11.9929 14.3871L12.6977 13.6777L13.4025 12.9683C13.3019 12.8684 13.2687 12.782 13.2687 12.6565H12.2687ZM12.6977 13.6777L11.9929 14.3871C12.4646 14.8558 13.0603 15.1039 13.7268 15.1039V14.1039V13.1039C13.5931 13.1039 13.5027 13.0679 13.4025 12.9683L12.6977 13.6777ZM13.3503 7.14607V8.14607H17.017V7.14607V6.14607H13.3503V7.14607ZM17.017 7.14607H16.017V12.6565H17.017H18.017V7.14607H17.017ZM17.017 12.6565H16.017C16.017 12.602 16.0261 12.5245 16.0607 12.4372C16.0961 12.3479 16.1492 12.2711 16.21 12.2114L16.9107 12.9249L17.6114 13.6383C17.8938 13.3609 18.017 13.0046 18.017 12.6565H17.017ZM16.9107 12.9249L16.21 12.2114C16.2673 12.155 16.3403 12.1059 16.4246 12.0727C16.5073 12.0402 16.5811 12.0309 16.6343 12.0305L16.6418 13.0305L16.6492 14.0305C16.9913 14.0279 17.3377 13.9071 17.6114 13.6383L16.9107 12.9249ZM16.6418 13.0305V12.0305H13.7256V13.0305V14.0305H16.6418V13.0305ZM13.7256 13.0305V12.0305C13.7783 12.0305 13.8532 12.0392 13.938 12.0722C14.0247 12.106 14.0999 12.1567 14.159 12.2154L13.4542 12.9249L12.7494 13.6343C13.0274 13.9104 13.3807 14.0305 13.7256 14.0305V13.0305ZM13.4542 12.9249L14.159 12.2154C14.2197 12.2757 14.2719 12.3525 14.3065 12.4408C14.3403 12.5272 14.3491 12.6034 14.3491 12.6565H13.3491H12.3491C12.3491 13.001 12.4694 13.3561 12.7494 13.6343L13.4542 12.9249ZM13.3491 12.6565L14.3491 12.6567L14.3503 7.14629L13.3503 7.14607L12.3503 7.14585L12.3491 12.6563L13.3491 12.6565ZM1.97511 17V16C1.65829 16 1.44748 15.9052 1.27069 15.7296L0.565889 16.439L-0.138911 17.1484C0.437186 17.7208 1.16586 18 1.97511 18V17ZM0.565889 16.439L1.27069 15.7296C1.0942 15.5542 1.00045 15.3471 0.999999 15.0363L0 15.0377L-0.999999 15.0392C-0.998821 15.8487 -0.715307 16.5758 -0.138911 17.1484L0.565889 16.439ZM0 15.0377H1V1.96229H0H-1V15.0377H0ZM0 1.96229H1C1 1.65388 1.09297 1.4478 1.26992 1.27238L0.565889 0.562214L-0.138144 -0.147954C-0.715713 0.424626 -1 1.15193 -1 1.96229H0ZM0.565889 0.562214L1.26992 1.27238C1.4488 1.09505 1.66055 1.00045 1.97533 0.999999L1.97389 0L1.97245 -0.999999C1.16441 -0.998834 0.437498 -0.718622 -0.138144 -0.147954L0.565889 0.562214ZM1.97389 0V1H8.06178V0V-1H1.97389V0ZM8.06178 0L7.35698 0.709406L9.80142 3.13798L10.5062 2.42857L11.211 1.71916L8.76658 -0.709406L8.06178 0ZM10.5062 2.42857V3.42857H20.0261V2.42857V1.42857H10.5062V2.42857ZM20.0261 2.42857V3.42857C20.341 3.42857 20.5522 3.523 20.7305 3.70019L21.4353 2.99078L22.1401 2.28138C21.564 1.70895 20.8357 1.42857 20.0261 1.42857V2.42857ZM21.4353 2.99078L20.7305 3.70019C20.9081 3.87663 21.0004 4.0827 21 4.3894L22 4.39086L23 4.39232C23.0012 3.58187 22.7171 2.85456 22.1401 2.28138L21.4353 2.99078ZM22 4.39086H21V15.0389H22H23V4.39086H22ZM22 15.0389H21C21 15.3468 20.9072 15.5537 20.7298 15.7304L21.4353 16.439L22.1409 17.1476C22.7164 16.5747 23 15.8482 23 15.0389H22ZM21.4353 16.439L20.7298 15.7304C20.5532 15.9061 20.3432 16.0005 20.0275 16L20.0261 17L20.0247 18C20.8351 18.0012 21.5646 17.7215 22.1409 17.1476L21.4353 16.439ZM20.0261 17V16H1.97511V17V18H20.0261V17ZM1.97511 15.7857V16.7857H20.0261V15.7857V14.7857H1.97511V15.7857ZM20.0261 15.7857V16.7857C20.4795 16.7857 20.9227 16.6313 21.2711 16.285L20.5663 15.5756L19.8615 14.8662C19.8858 14.8421 19.9213 14.8175 19.9631 14.8012C20.0031 14.7856 20.0283 14.7857 20.0261 14.7857V15.7857ZM20.5663 15.5756L21.2711 16.285C21.6201 15.9383 21.7778 15.4949 21.7778 15.0389H20.7778H19.7778C19.7778 15.0394 19.7777 15.0326 19.78 15.02C19.7823 15.0072 19.7866 14.9899 19.7945 14.9699C19.8117 14.9264 19.8373 14.8903 19.8615 14.8662L20.5663 15.5756ZM20.7778 15.0389H21.7778V4.38964H20.7778H19.7778V15.0389H20.7778ZM20.7778 4.38964H21.7778C21.7778 3.93365 21.6201 3.49022 21.2711 3.14352L20.5663 3.85293L19.8615 4.56233C19.8373 4.53823 19.8117 4.50222 19.7945 4.45869C19.7866 4.43864 19.7823 4.42137 19.78 4.40855C19.7777 4.39595 19.7778 4.38917 19.7778 4.38964H20.7778ZM20.5663 3.85293L21.2711 3.14352C20.9227 2.79732 20.4795 2.64286 20.0261 2.64286V3.64286V4.64286C20.0283 4.64286 20.0031 4.64293 19.9631 4.62739C19.9213 4.6111 19.8858 4.58645 19.8615 4.56233L20.5663 3.85293ZM20.0261 3.64286V2.64286H10.0161V3.64286V4.64286H20.0261V3.64286ZM10.0161 3.64286L10.7209 2.93345L8.27647 0.504879L7.57167 1.21429L6.86687 1.92369L9.31131 4.35226L10.0161 3.64286ZM7.57167 1.21429V0.214285H1.97389V1.21429V2.21429H7.57167V1.21429ZM1.97389 1.21429V0.214285C1.52047 0.214285 1.07734 0.368742 0.728867 0.71495L1.43367 1.42436L2.13847 2.13376C2.1142 2.15788 2.07874 2.18253 2.03687 2.19882C1.99689 2.21436 1.97174 2.21429 1.97389 2.21429V1.21429ZM1.43367 1.42436L0.728867 0.71495C0.379146 1.0624 0.222222 1.50666 0.222222 1.96229H1.22222H2.22222C2.22222 1.9615 2.22207 1.98823 2.20577 2.0297C2.18872 2.07306 2.16311 2.10928 2.13847 2.13376L1.43367 1.42436ZM1.22222 1.96229H0.222222V15.0389H1.22222H2.22222V1.96229H1.22222ZM1.22222 15.0389H0.222222C0.222222 15.4949 0.379899 15.9383 0.728867 16.285L1.43367 15.5756L2.13847 14.8662C2.16273 14.8903 2.18833 14.9264 2.20548 14.9699C2.21338 14.9899 2.21774 15.0072 2.22002 15.02C2.22225 15.0326 2.22222 15.0394 2.22222 15.0389H1.22222ZM1.43367 15.5756L0.728867 16.285C1.07808 16.632 1.52203 16.7857 1.97511 16.7857V15.7857V14.7857C1.9721 14.7857 1.99673 14.7855 2.03646 14.8009C2.07816 14.8171 2.11382 14.8417 2.13847 14.8662L1.43367 15.5756Z"
                            fill="currentColor" mask="url(#path-1-inside-1_2605_28551)" />
                    </svg>
                </button>
                <button class="header-icon-btn-only header-icon-btn-only-sm btn-show-users non-deleted-icon d-none"
                    type="button" data-bs-toggle="tooltip"
                    title="{{ trans('accessories.accessory_fields.show_non_deleted_accessory') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M8.5 4H6C4.89543 4 4 4.89543 4 6V20C4 21.1046 4.89543 22 6 22H12" stroke="#131927"
                            stroke-width="1.5" stroke-linecap="round" />
                        <path d="M15.5 4H18C19.1046 4 20 4.89543 20 6V15" stroke="#131927" stroke-width="1.5"
                            stroke-linecap="round" />
                        <path
                            d="M8 6.4V4.5C8 4.22386 8.22386 4 8.5 4C8.77614 4 9.00422 3.77604 9.05152 3.50398C9.19968 2.65171 9.77399 1 12 1C14.226 1 14.8003 2.65171 14.9485 3.50398C14.9958 3.77604 15.2239 4 15.5 4C15.7761 4 16 4.22386 16 4.5V6.4C16 6.73137 15.7314 7 15.4 7H8.6C8.26863 7 8 6.73137 8 6.4Z"
                            stroke="#131927" stroke-width="1.5" stroke-linecap="round" />
                        <path d="M15.5 20.5L17.5 22.5L22.5 17.5" stroke="#131927" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>

                <button class="header-icon-btn-only header-icon-btn-only-sm btn-checkin btn-bulk-checkin" type="button"
                    data-bs-toggle="tooltip" title="{{trans('accessories.accessory_fields.Bulk_Checkin') }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path
                            d="M9 17L4 12L9 7V10H16V14H9V17ZM21 5H11V3H21C22.1 3 23 3.9 23 5V19C23 20.1 22.1 21 21 21H11V19H21V5Z"
                            fill="currentColor"></path>
                    </svg>
                </button>

                <button class="header-icon-btn-only header-icon-btn-only-sm btn-checkout btn-bulk-checkout"
                    type="button" data-bs-toggle="tooltip"
                    title="{{trans('accessories.accessory_fields.Bulk_Checkout') }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path
                            d="M10 17V14H3V10H10V7L15 12L10 17ZM21 19H11V21H21C22.1 21 23 20.1 23 19V5C23 3.9 22.1 3 21 3H11V5H21V19Z"
                            fill="currentColor"></path>
                    </svg>
                </button>
                {{-- Add accessory --}}
                <button class="amg-btn amg-btn-primary amg-btn-sm btn-add-accessory" type="button"
                    data-bs-toggle="tooltip" title="{{trans('accessories.accessory_fields.add_accessory') }}">
                    <svg width="19" height="19" viewBox="0 0 19 19" fill="none">
                        <path
                            d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z"
                            fill="currentColor" />
                    </svg>
                    <span>{{ trans('accessories.accessory_fields.add_accessory') }}</span>
                </button>
            </div>
        </div>
        <main class="main-content" id="mainContent">
            <div class="container-fluid px-0">
                <div class="card rounded-0">
                    <div class="card-body pt-3">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="col-auto">
                                <select class="amg-table-pagination-dropdown userModulePageLenth user-list-page-length">
                                    <option value="10" selected>{{ trans('accessories.accessory_fields.show') }} (10)
                                    </option>
                                    <option value="25">{{ trans('accessories.accessory_fields.show') }} (25)</option>
                                    <option value="50">{{ trans('accessories.accessory_fields.show') }} (50)</option>
                                    <option value="100">{{ trans('accessories.accessory_fields.show') }} (100)</option>
                                </select>
                            </div>
                            <div class="flex-grow-1"></div>

                            <div>
                                <div class="amg-list-searchbar">
                                    <svg class="amg-list-searchbar__icon" width="18" height="18" viewBox="0 0 20 20"
                                        fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z"
                                            fill="currentColor"></path>
                                    </svg>
                                    <input type="text" class="amg-list-searchbar__input user-list-search"
                                        placeholder="{{ trans('accessories.accessory_fields.Search') }}...">
                                </div>
                            </div>

                            <button class="amg-refresh-btn btn-reload-list">
                                <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z"
                                        fill="currentColor"></path>
                                </svg>
                                <span>{{ trans('accessories.accessory_fields.Refresh_List') }}</span>
                            </button>
                        </div>

                        <div class="js-user-list-view-panel">
                            <div class="table-responsive">
                                <table id="mytable" class="table display app-data-table">
                                    <thead>
                                        <tr>
                                            <th>
                                                <h4>#</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans("accessories.accessory_fields.accessory") }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans("accessories.accessory_fields.location") }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans("accessories.accessory_fields.total") }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans("accessories.accessory_fields.avail") }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans("accessories.accessory_fields.scrap") }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans("accessories.accessory_fields.threshold") }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans("accessories.accessory_fields.purchase_info") }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans("accessories.accessory_fields.updated_on") }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans("accessories.accessory_fields.actions") }}</h4>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    @include("accessories.filter")
    @include("accessories.modal_html")
    @include("accessories.checkout_modal")
    @include("accessories.note")
</section>
@endsection

@push('css')
<link href="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.css') !!}" rel="stylesheet" />
@endpush

@push('scripts')
<script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.min.js') !!}">
</script>
<script type="text/javascript" src="{!! CommonHelper::asset('js/accessories/index.js') !!}"></script>
<script src="{!! CommonHelper::asset('js/common.js') !!}"></script>
<script type="text/javascript">
    var baseURL = '{{ URL::to('/') }}';
    var baseConfig = {
        today: "{{ date('d/m/Y') }}"
    };
    $(document).ready(function() {
		var config = {};
		config.url = {};
		config.imgviewpath = "{{ url('uploads/accessories') }}";
		config.url.accessories = "{{ url('jx-accessories') }}";
		config.url.add = "{{ url('accessory/add') }}";
		config.url.edit = "{{ url('accessory/edit') }}";
		config.url.restore = "{{ url('accessories-restore') }}";
		config.url.delete = "{{ url('accessory/delete') }}";
		config.url.get = "{{ url('accessory/get') }}";
		config.url.info = "{{ url('accessory/info') }}";
		config.getInternalPlaceByAjax = "{{ url('getInternalPlaceByAjax') }}";
		config.url.checkout = "{{ url('accessory/checkout') }}";
		config.url.download_url = "{{ url('export-accessories') }}";
		config.url.download_url_pdf = "{{ url('export-accessories-pdf') }}";
		config.getLocationByAjax = "{{ url('getLocationByQuery') }}";
		config.getUserByAjax = "{{ url('getUserForDropDown') }}";
		config.getDeviceForDropDown = "{{ url('getDeviceForDropDown') }}";
		config.ajaxGetInternalPlace = "{{ url('ajaxGetInternalPlace') }}";
		config.getDeviceForCheckoutDropDown = "{{ url('getDeviceForCheckoutDropDown') }}";
		config.getManufacturerByAjax = "{{ url('getManufacturerByQuery') }}";
		config.getInvoiceByAjax = "{{ url('getInvoiceByQuery') }}";
		config.getSupplierByAjax = "{{ url('getSupplierByQuery') }}";
		config.url.bulkcheckout = "{{ url('accessories/bulk/checkout') }}";
		config.url.bulkcheckin = "{{ url('accessories/bulk/checkin') }}";
		config.url.import_url = "{{ url('accessory/import') }}",
		config.getCustomFieldsByCategory = "{{ url('getCustomFieldsByCategory') }}";
		config.url.getAssetDepartments = "{{ route('getAssetDepartments') }}";
		config.internalPlaces = {!! json_encode($vd->internal_places)!!};
		config.client = @json(config('app.client'));
		config.ajaxGetInternalPlace = "{{url('ajaxGetInternalPlace')}}";
		config.getPredefinedDropdownByQuery = "{{url('getByCustomDropDown')}}";
		config.url.getActivatedUsers = "{{ url('getActivatedUsers') }}";
		config.token = "{{ csrf_token() }}";
		config.translations = {
			add_new_accessory: '{{ trans('accessories.accessory_fields.add_new_accessory') }}',
			edit_accessory: '{{ trans('accessories.accessory_fields.edit_accessory') }}',
			save: '{{ trans('accessories.accessory_fields.save_accessory') }}',
			press_enter_with_Search: '{{ trans('accessories.accessory_fields.press_enter_with_Search') }}',
			please_enter_valid_search: '{{ trans('accessories.accessory_fields.please_enter_valid_search') }}',
			something_went_wrong: '{{ trans('accessories.accessory_fields.something_went_wrong') }}',
			Search: '{{ trans('accessories.accessory_fields.Search') }}',
			Filter: '{{ trans('accessories.accessory_fields.Filter') }}',
			filter_by_internal_place:'{{ trans('accessories.filter_heading.filter_by_internal_place') }}',
			Refresh_List: '{{ trans('accessories.accessory_fields.Refresh_List') }}',
			Bulk_Checkout: '{{ trans('accessories.accessory_fields.Bulk_Checkout') }}',
			Bulk_CheckIn: '{{ trans('accessories.accessory_fields.Bulk_CheckIn') }}',
			Download: '{{ trans('accessories.accessory_fields.Download') }}',
			Download_PDF: '{{ trans('accessories.accessory_fields.Download_PDF') }}',
			Restore_Accessory: '{{ trans('accessories.accessory_fields.Restore_Accessory') }}',
			Edit_Accessory: '{{ trans('accessories.accessory_fields.Edit_Accessory') }}',
			Clone_Accessory: '{{ trans('accessories.accessory_fields.Clone_Accessory') }}',
			Delete_Accessory: '{{ trans('accessories.accessory_fields.Delete_Accessory') }}',
			accessory_name: '{{ trans('accessories.accessory_fields.accessory_name') }}',
			view_notes: '{{ trans('accessories.accessory_fields.view_notes') }}',
			batch_no: '{{ trans('accessories.accessory_fields.batch_no') }}',
			category: '{{ trans('accessories.accessory_fields.category') }}',
			Company_Name: '{{ trans('accessories.accessory_fields.Company_Name') }}',
			Select_Place: '{{ trans('accessories.accessory_fields.Select_Place') }}',
			are_you_want: '{{ trans('accessories.accessory_fields.are_you_want') }}',
			Select_Currency_Format: '{{ trans('accessories.accessory_fields.Select_Currency_Format') }}',
			Select_the_Location: '{{ trans('accessories.accessory_fields.Select_the_Location') }}',
			select_internal_place: '{{ trans('accessories.accessory_fields.select_internal_place') }}',
			Select_the_Purchase_Invoice: '{{ trans('accessories.accessory_fields.Select_the_Purchase_Invoice') }}',
			Select_the_manufacturer: '{{ trans('accessories.accessory_fields.Select_the_manufacturer') }}',
			Select_the_User: '{{ trans('accessories.accessory_fields.Select_the_User') }}',
			Select_the_Device: '{{ trans('accessories.accessory_fields.Select_the_Device') }}',
			Select_the_Supplier: '{{ trans('accessories.accessory_fields.Select_the_Supplier') }}',
			Select_Company: '{{ trans('accessories.accessory_fields.Select_Company') }}',
			Select_Category: '{{ trans('accessories.accessory_fields.Select_Category') }}',
			No_Filter:'{{ trans('accessories.accessory_fields.No_Filter') }}',
			select_the_department: '{{ trans('accessories.accessory_fields.Select_the_Department') }}',
			select_the_catagory: '{{ trans('accessories.accessory_fields.select_the_catagory') }}',
			select_company_name: '{{ trans('accessories.accessory_fields.select_company_name') }}',
			select_the_accessory: '{{ trans('accessories.accessory_fields.select_the_accessory') }}',
			view_accessory: '{{ trans('accessories.accessory_fields.view_accessory') }}',
			select_the_model: '{{trans('accessories.accessory_fields.select_the_model')}}',
			select_the_internal_place: '{{trans('accessories.accessory_fields.select_the_internal_place')}}',
			select_the_project: '{{trans('accessories.accessory_fields.select_the_project')}}',
			select_the_contract:'{{trans('accessories.accessory_fields.select_the_contract')}}',
			select_the_component:'{{trans('accessories.accessory_fields.select_the_component')}}',
			select_the_license: '{{trans('accessories.accessory_fields.select_the_license')}}',
			select_the_task: '{{trans('accessories.accessory_fields.select_the_task')}}',
			select_the_change_management: '{{trans('accessories.accessory_fields.select_the_change_management')}}',
			select_the_ticket:'{{trans('accessories.accessory_fields.select_the_ticket')}}',
			select_the_ticket_procure_request: '{{trans('accessories.accessory_fields.select_the_ticket_procure_request')}}',
			location:'{{trans('accessories.accessory_fields.location')}}',
			internal_place:'{{trans('accessories.accessory_fields.internal_place')}}',
			unique_tag:'{{trans('accessories.accessory_fields.unique_tag')}}',
			show_deleted_accessory: '{{trans('accessories.accessory_fields.show_deleted_accessory')}}',
		    show_non_deleted_accessory: '{{trans('accessories.accessory_fields.show_non_deleted_accessory')}}',
		    are_you_restore: '{{trans('accessories.accessory_fields.are_you_restore')}}',
			accessory_restored: '{{trans('accessories.accessory_fields.accessory_restored')}}',
            Check_Out:'{{trans('accessories.accessory_fields.Check_Out')}}',
		};
		config.companies = {!! json_encode($companies) !!};
		config.categories = {!! json_encode($categories) !!};
		config.currencies = {!! json_encode($currencies) !!};
		config.places = {!! json_encode($vd->places) !!};
		config.location = {!! json_encode($vd->location) !!};
		config.assignedForOptions = {!! json_encode($vd->assignedForOptions) !!};
		config.permissions = {!! json_encode($permissionArray) !!};
		config.location_filter = "{{ $location }}";
		config.department_filter = "{{ $department }}";
		config.cat_id_filter = "{{ $cat_id }}";
		config.type = "{{ $type }}";
		config.accessoryfilter = {!! json_encode($accessoryfilter) !!};
		// config.custom_fields = {!! !empty($companyFieldset->fields) ? json_encode(CommonHelper::formCustomFieldsLicence($companyFieldset->fields)) : json_encode('') !!};
		config.custom_fields = {!! !empty($companyFieldset->fields) ? json_encode(CommonHelper::formCustomFields($companyFieldset->fields)) : json_encode('') !!};
		config.default_currency_format = "{{ CommonHelper::settings()->default_currency }}";
        new MyApp(config);
    });
</script>
@endpush
{{--
/**
* ------------------------------------------------------------
* File: info.blade.php
* Module: Devices
* DEV/26/07
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #DEV-017
* Created On: 2026-01-07
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}

@extends('layouts.layout1')
@section('title', trans("devices.device_info.view.title"))

@section('content')
<div id="main-user-list-wrapper">
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
        <button onclick="location.href='{{ url('devices') }}'" class="d-flex gap-3 align-items-center bg-transparent outline-none border-0">
            <svg width="25" height="21" viewBox="0 0 25 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11.5662 0.440759C11.706 0.580116 11.817 0.745702 11.8927 0.928029C11.9684 1.11036 12.0074 1.30584 12.0074 1.50326C12.0074 1.70068 11.9684 1.89616 11.8927 2.07848C11.817 2.26081 11.706 2.4264 11.5662 2.56576L5.12993 9.00201L23.5049 9.00201C23.9028 9.00201 24.2843 9.16004 24.5656 9.44135C24.8469 9.72265 25.0049 10.1042 25.0049 10.502C25.0049 10.8998 24.8469 11.2814 24.5656 11.5627C24.2843 11.844 23.9028 12.002 23.5049 12.002L5.12993 12.002L11.5662 18.4408C11.848 18.7226 12.0063 19.1047 12.0063 19.5033C12.0063 19.9018 11.848 20.284 11.5662 20.5658C11.2844 20.8475 10.9022 21.0059 10.5037 21.0059C10.1052 21.0059 9.72298 20.8475 9.44118 20.5658L0.441182 11.5658C0.301343 11.4264 0.190387 11.2608 0.114679 11.0785C0.0389711 10.8962 -4.50154e-07 10.7007 -4.58778e-07 10.5033C-4.67401e-07 10.3058 0.038971 10.1104 0.114679 9.92803C0.190387 9.7457 0.301342 9.58011 0.441182 9.44076L9.44118 0.440759C9.58054 0.30092 9.74613 0.189964 9.92845 0.114255C10.1108 0.0385471 10.3063 -0.000425789 10.5037 -0.000425798C10.7011 -0.000425806 10.8966 0.038547 11.0789 0.114255C11.2612 0.189964 11.4268 0.30092 11.5662 0.440759Z" fill="currentColor" />
            </svg>
            <h2 class="h2-text mb-0">
                @if($device->asset_tag)
                    {{ trans("devices.device_info.view.page_title") }} - {{ $device->asset_tag }}
                @else
                    {{ trans("devices.device_info.view.page_title") }}
                @endif
            </h2>
        </button>
        <div class="d-flex gap-8 align-items-center">
            @if( config('services.network_inventory.enabled') == 1 && !empty($network))
                @if(optional($network)->is_AD == 0)
                    <button class="header-icon-btn header-icon-btn-sm" type="button" data-id="" data-bs-toggle="tooltip" title="{{ trans('devices.device_info.view.non_ad') }}">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.5 15.8346H10M10 15.8346H17.5M10 15.8346V10.8346M10 10.8346H15V4.16797H5V10.8346H10ZM7.5 7.50964L7.50833 7.50047M10 7.50964L10.0083 7.50047" stroke="#7F7F7F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                @endif
                @if(optional($network)->is_AD == 1)
                    <button class="header-icon-btn header-icon-btn-sm" type="button" data-id="" data-bs-toggle="tooltip" title="{{ trans('devices.device_info.view.ad') }}">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.4995 10.4994C18.8516 10.5005 18.2213 10.7111 17.7028 11.0997C17.1843 11.4882 16.8053 12.034 16.6223 12.6556L12.6651 12.0931C12.4847 12.0676 12.3198 11.9773 12.2011 11.8391L8.85234 7.92594C9.47887 7.60839 9.97412 7.08107 10.2518 6.43586C10.5294 5.79065 10.5719 5.06847 10.3717 4.39518C10.1716 3.72188 9.74153 3.14017 9.15651 2.7514C8.57149 2.36264 7.86861 2.19147 7.17035 2.26774C6.47209 2.34401 5.82273 2.66288 5.33543 3.16877C4.84814 3.67466 4.55381 4.33551 4.50373 5.03613C4.45365 5.73676 4.65101 6.43274 5.0614 7.0028C5.4718 7.57285 6.06921 7.98082 6.74952 8.15563V15.8431C6.04253 16.0257 5.42638 16.4598 5.01657 17.0641C4.60676 17.6685 4.43143 18.4015 4.52343 19.1259C4.61544 19.8502 4.96847 20.5162 5.51635 20.9989C6.06423 21.4816 6.76934 21.7479 7.49952 21.7479C8.2297 21.7479 8.93482 21.4816 9.4827 20.9989C10.0306 20.5162 10.3836 19.8502 10.4756 19.1259C10.5676 18.4015 10.3923 17.6685 9.98248 17.0641C9.57267 16.4598 8.95652 16.0257 8.24952 15.8431V9.52719L11.062 12.8084C11.4175 13.2231 11.9116 13.4944 12.4523 13.5716L16.5773 14.1603C16.7019 14.7116 16.9796 15.2165 17.3785 15.6169C17.7774 16.0172 18.2813 16.2967 18.8321 16.4232C19.3829 16.5498 19.9583 16.5181 20.4919 16.3319C21.0255 16.1457 21.4957 15.8126 21.8483 15.3709C22.2009 14.9292 22.4215 14.3969 22.4848 13.8353C22.548 13.2736 22.4514 12.7056 22.2059 12.1965C21.9605 11.6874 21.5762 11.258 21.0974 10.9577C20.6186 10.6575 20.0647 10.4986 19.4995 10.4994ZM5.99952 5.24938C5.99952 4.9527 6.0875 4.66269 6.25232 4.41602C6.41714 4.16935 6.65141 3.97709 6.9255 3.86356C7.19959 3.75002 7.50119 3.72032 7.79216 3.7782C8.08313 3.83608 8.3504 3.97894 8.56018 4.18872C8.76996 4.39849 8.91282 4.66577 8.9707 4.95674C9.02858 5.24771 8.99887 5.54931 8.88534 5.8234C8.77181 6.09749 8.57955 6.33176 8.33288 6.49658C8.08621 6.6614 7.7962 6.74938 7.49952 6.74938C7.1017 6.74938 6.72017 6.59134 6.43886 6.31004C6.15756 6.02873 5.99952 5.6472 5.99952 5.24938ZM8.99952 18.7494C8.99952 19.046 8.91155 19.3361 8.74673 19.5827C8.58191 19.8294 8.34764 20.0217 8.07355 20.1352C7.79946 20.2487 7.49786 20.2784 7.20689 20.2206C6.91592 20.1627 6.64864 20.0198 6.43886 19.81C6.22909 19.6003 6.08622 19.333 6.02835 19.042C5.97047 18.751 6.00017 18.4494 6.1137 18.1754C6.22724 17.9013 6.41949 17.667 6.66617 17.5022C6.91284 17.3373 7.20285 17.2494 7.49952 17.2494C7.89735 17.2494 8.27888 17.4074 8.56018 17.6887C8.84149 17.97 8.99952 18.3516 8.99952 18.7494ZM19.4995 14.9994C19.2029 14.9994 18.9128 14.9114 18.6662 14.7466C18.4195 14.5818 18.2272 14.3475 18.1137 14.0734C18.0002 13.7993 17.9705 13.4977 18.0283 13.2067C18.0862 12.9158 18.2291 12.6485 18.4389 12.4387C18.6486 12.2289 18.9159 12.0861 19.2069 12.0282C19.4979 11.9703 19.7995 12 20.0735 12.1136C20.3476 12.2271 20.5819 12.4193 20.7467 12.666C20.9116 12.9127 20.9995 13.2027 20.9995 13.4994C20.9995 13.8972 20.8415 14.2787 20.5602 14.56C20.2789 14.8413 19.8973 14.9994 19.4995 14.9994Z" fill="#7F7F7F"/>
                        </svg>
                    </button>
                @endif
            @endif
            @if($device->added_from == 3 && $device->azure->id != "")
                <button class="header-icon-btn header-icon-btn-sm" type="button" data-id="" data-bs-toggle="tooltip" title="{{ trans('devices.device_info.view.azure') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0z" fill="none" />
                        <path fill="#7F7F7F" d="M6.5 20q-2.275 0-3.887-1.575T1 14.575q0-1.95 1.175-3.475T5.25 9.15q.625-2.3 2.5-3.725T12 4q2.925 0 4.963 2.038T19 11q1.725.2 2.863 1.488T23 15.5q0 1.875-1.312 3.188T18.5 20z" />
                    </svg>
                </button>
            @endif
            @can("DeviceEdit")
                @if($device->status_id != 7 && $transfer_status != 1)
                    <button class="header-icon-btn header-icon-btn-sm" type="button" data-id="" data-bs-toggle="tooltip" title="{{ trans('devices.device_info.view.edit_device') }}">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21.3103 6.87915L17.1216 2.68946C16.9823 2.55014 16.8169 2.43962 16.6349 2.36421C16.4529 2.28881 16.2578 2.25 16.0608 2.25C15.8638 2.25 15.6687 2.28881 15.4867 2.36421C15.3047 2.43962 15.1393 2.55014 15 2.68946L3.43969 14.2507C3.2998 14.3895 3.18889 14.5547 3.11341 14.7367C3.03792 14.9188 2.99938 15.114 3.00001 15.311V19.5007C3.00001 19.8985 3.15804 20.2801 3.43935 20.5614C3.72065 20.8427 4.10218 21.0007 4.50001 21.0007H8.6897C8.88675 21.0013 9.08197 20.9628 9.26399 20.8873C9.44602 20.8118 9.61122 20.7009 9.75001 20.561L21.3103 9.00071C21.4496 8.86142 21.5602 8.69604 21.6356 8.51403C21.711 8.33202 21.7498 8.13694 21.7498 7.93993C21.7498 7.74292 21.711 7.54784 21.6356 7.36582C21.5602 7.18381 21.4496 7.01844 21.3103 6.87915ZM8.6897 19.5007H4.50001V15.311L12.75 7.06102L16.9397 11.2507L8.6897 19.5007ZM18 10.1895L13.8103 6.00071L16.0603 3.75071L20.25 7.93946L18 10.1895Z"
                                fill="#7F7F7F" />
                        </svg>
                    </button>
                @endif
            @endcan
            @can("DeviceRestore")
                @if($device->status_id == 7 && Auth::user()->isSuperUser())
                    <button class="header-icon-btn header-icon-btn-sm" type="button" data-id="" data-bs-toggle="tooltip" title="{{ trans('devices.device_info.view.restore_dispose_device') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                            <path d="M0 0h24v24H0z" fill="none" />
                            <path fill="#7F7F7F" d="m21.82 15.42l-2.5 4.33c-.49.86-1.4 1.31-2.32 1.25h-2v2l-2.5-4.5L15 14v2h2.82l-2.22-3.85l4.33-2.5l1.8 3.12c.52.77.59 1.8.09 2.65M9.21 3.06h5c.98 0 1.83.57 2.24 1.39l1 1.74l1.73-1l-2.64 4.41l-5.15.09l1.73-1l-1.41-2.45l-2.21 3.85l-4.34-2.5l1.8-3.12c.41-.83 1.26-1.41 2.25-1.41m-4.16 16.7l-2.5-4.33c-.49-.85-.42-1.87.09-2.64l1-1.73l-1.73-1l5.14.08l2.65 4.42l-1.73-1L6.56 16H11v5H7.4a2.51 2.51 0 0 1-2.35-1.24" />
                        </svg>
                    </button>
                @endif
            @endcan
            @can("DeviceAdd")
                @if($transfer_status != 1)
                    <button class="header-icon-btn header-icon-btn-sm" type="button" data-id="" data-bs-toggle="tooltip" title="{{ trans('devices.device_info.view.clone_device') }}">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.25 3H8.25C8.05109 3 7.86032 3.07902 7.71967 3.21967C7.57902 3.36032 7.5 3.55109 7.5 3.75V7.5H3.75C3.55109 7.5 3.36032 7.57902 3.21967 7.71967C3.07902 7.86032 3 8.05109 3 8.25V20.25C3 20.4489 3.07902 20.6397 3.21967 20.7803C3.36032 20.921 3.55109 21 3.75 21H15.75C15.9489 21 16.1397 20.921 16.2803 20.7803C16.421 20.6397 16.5 20.4489 16.5 20.25V16.5H20.25C20.4489 16.5 20.6397 16.421 20.7803 16.2803C20.921 16.1397 21 15.9489 21 15.75V3.75C21 3.55109 20.921 3.36032 20.7803 3.21967C20.6397 3.07902 20.4489 3 20.25 3ZM15 19.5H4.5V9H15V19.5ZM19.5 15H16.5V8.25C16.5 8.05109 16.421 7.86032 16.2803 7.71967C16.1397 7.57902 15.9489 7.5 15.75 7.5H9V4.5H19.5V15Z" fill="#7F7F7F" />
                        </svg>
                    </button>
                @endif
            @endcan
            @can("DevicePrintLabel")
                <button class="header-icon-btn header-icon-btn-sm" type="button" data-id="" data-bs-toggle="tooltip" title="{{ trans('devices.device_info.view.print_label') }}">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 6.6V8.4C9 8.73137 8.73137 9 8.4 9H6.6C6.26863 9 6 8.73137 6 8.4V6.6C6 6.26863 6.26863 6 6.6 6H8.4C8.73137 6 9 6.26863 9 6.6Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M6 12H9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M15 12V15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M12 18H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M12 12.0111L12.01 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M18 12.0111L18.01 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M12 15.0111L12.01 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M18 15.0111L18.01 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M18 18.0111L18.01 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M12 9.01111L12.01 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M12 6.01111L12.01 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M9 15.6V17.4C9 17.7314 8.73137 18 8.4 18H6.6C6.26863 18 6 17.7314 6 17.4V15.6C6 15.2686 6.26863 15 6.6 15H8.4C8.73137 15 9 15.2686 9 15.6Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M18 6.6V8.4C18 8.73137 17.7314 9 17.4 9H15.6C15.2686 9 15 8.73137 15 8.4V6.6C15 6.26863 15.2686 6 15.6 6H17.4C17.7314 6 18 6.26863 18 6.6Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M18 3H21V6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M18 21H21V18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M6 3H3V6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M6 21H3V18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>
            @endcan
            @can("DeviceResale")
                @if($transfer_status != 1 && $device->status_id != 4 && $device->status_id != 7 && $device->status_id != 2)
                    <button class="header-icon-btn header-icon-btn-sm" type="button" data-id="" data-bs-toggle="tooltip" title="{{ trans('devices.device_info.view.dispose') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="100%" height="100%">
                            <path d="M21 3L3 10.5L11.5 13L14 21.5L21 3Z" fill="#7F7F7F" stroke="#7F7F7F" stroke-width="1" stroke-linejoin="round"/>
                        </svg>
                    </button>
                @endif
            @endcan
            @can("DeviceDelete")   
                @if($device->status_id != 7 && $transfer_status != 1)
                    <button class="header-icon-btn header-icon-btn-sm" type="button" data-id="" data-bs-toggle="tooltip" title="{{ trans('devices.device_info.view.delete_device') }}">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.25 4.5H16.5V3.75C16.5 3.15326 16.2629 2.58097 15.841 2.15901C15.419 1.73705 14.8467 1.5 14.25 1.5H9.75C9.15326 1.5 8.58097 1.73705 8.15901 2.15901C7.73705 2.58097 7.5 3.15326 7.5 3.75V4.5H3.75C3.55109 4.5 3.36032 4.57902 3.21967 4.71967C3.07902 4.86032 3 5.05109 3 5.25C3 5.44891 3.07902 5.63968 3.21967 5.78033C3.36032 5.92098 3.55109 6 3.75 6H4.5V19.5C4.5 19.8978 4.65804 20.2794 4.93934 20.5607C5.22064 20.842 5.60218 21 6 21H18C18.3978 21 18.7794 20.842 19.0607 20.5607C19.342 20.2794 19.5 19.8978 19.5 19.5V6H20.25C20.4489 6 20.6397 5.92098 20.7803 5.78033C20.921 5.63968 21 5.44891 21 5.25C21 5.05109 20.921 4.86032 20.7803 4.71967C20.6397 4.57902 20.4489 4.5 20.25 4.5ZM9 3.75C9 3.55109 9.07902 3.36032 9.21967 3.21967C9.36032 3.07902 9.55109 3 9.75 3H14.25C14.4489 3 14.6397 3.07902 14.7803 3.21967C14.921 3.36032 15 3.55109 15 3.75V4.5H9V3.75ZM18 19.5H6V6H18V19.5ZM10.5 9.75V15.75C10.5 15.9489 10.421 16.1397 10.2803 16.2803C10.1397 16.421 9.94891 16.5 9.75 16.5C9.55109 16.5 9.36032 16.421 9.21967 16.2803C9.07902 16.1397 9 15.9489 9 15.75V9.75C9 9.55109 9.07902 9.36032 9.21967 9.21967C9.36032 9.07902 9.55109 9 9.75 9C9.94891 9 10.1397 9.07902 10.2803 9.21967C10.421 9.36032 10.5 9.55109 10.5 9.75ZM15 9.75V15.75C15 15.9489 14.921 16.1397 14.7803 16.2803C14.6397 16.421 14.4489 16.5 14.25 16.5C14.0511 16.5 13.8603 16.421 13.7197 16.2803C13.579 16.1397 13.5 15.9489 13.5 15.75V9.75C13.5 9.55109 13.579 9.36032 13.7197 9.21967C13.8603 9.07902 14.0511 9 14.25 9C14.4489 9 14.6397 9.07902 14.7803 9.21967C14.921 9.36032 15 9.55109 15 9.75Z" fill="#7F7F7F"></path>
                        </svg>
                    </button>
                @endif 
            @endcan    
        </div>
    </div>
    <main class="main-content">
        <div class="tab-bar overflow-y-auto">
            <ul class="nav nav-underline flex-nowrap w-max-content" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link text-nowrap active" id="info" data-bs-toggle="tab" href="#info-tab" role="tab" aria-controls="info-tab" aria-expanded="true">
                        <span>{{ trans('devices.device_info.view.info') }}</span>
                    </a>
                </li>
                @if(config('services.rdp.enabled') == "1" && config('services.rdp.group_id') != "")
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" id="rdp-power" data-bs-toggle="tab" href="#rdp-power-tab" role="tab" aria-controls="rdp-power-tab" aria-expanded="false">
                            <span>{{ trans('devices.device_info.view.rdp_power') }}</span>
                        </a>
                    </li>
                @endif
                @if( (config('services.network_inventory.enabled') == 1) && ($network) && config("services.patch_management.enabled") && $itmAgentInstalled)
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" id="patch-management" data-bs-toggle="tab" href="#patch-management-tab" role="tab" aria-controls="patch-management-tab" aria-expanded="false">
                            <span>{{ trans('devices.device_info.view.patch_management') }}</span>
                        </a>
                    </li>
                @endif
                @can('DeviceExpenseRead')
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" id="maintenances" data-bs-toggle="tab" href="#maintenances-tab" role="tab" aria-controls="maintenances-tab" aria-expanded="false">
                            <span>{{ trans('devices.device_info.view.expense') }}</span>
                        </a>
                    </li>
                @endcan
                <li class="nav-item">
                    <a class="nav-link text-nowrap" id="maintenance-cost" data-bs-toggle="tab" href="#maintenance-cost-tab" role="tab" aria-controls="maintenance-cost-tab" aria-expanded="false">
                        <span>{{ trans('devices.device_info.view.schedule_maintenance') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap" id="licenses" data-bs-toggle="tab" href="#licenses-tab" role="tab" aria-controls="licenses-tab" aria-expanded="false">
                        <span>{{ trans('devices.device_info.view.licenses') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap" id="accessories" data-bs-toggle="tab" href="#accessories-tab" role="tab" aria-controls="accessories-tab" aria-expanded="false">
                        <span>{{ trans('devices.device_info.view.accessories') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap" id="consumable" data-bs-toggle="tab" href="#consumable-tab" role="tab" aria-controls="consumable-tab" aria-expanded="false">
                        <span>{{ trans('devices.device_info.view.consumables') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap" id="components" data-bs-toggle="tab" href="#components-tab" role="tab" aria-controls="components-tab" aria-expanded="false">
                        <span>{{ trans('devices.device_info.view.components') }}</span>
                    </a>
                </li>
                @if(config("services.service_ticket.enabled"))
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" id="tickets" data-bs-toggle="tab" href="#tickets-tab" role="tab" aria-controls="tickets-tab" aria-expanded="false">
                            <span>{{ trans('devices.device_info.view.tickets') }}</span>
                        </a>
                    </li>
                @endif
                @if(isset($device->sez_device) && $device->sez_device)
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" id="sez" data-bs-toggle="tab" href="#sez-tab" role="tab" aria-controls="sez-tab" aria-expanded="false">
                            <span>{{ trans('devices.device_info.view.sez') }}</span>
                        </a>
                    </li>
                @endif
                @can("DeviceDocumentsUpload")
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" id="documents" data-bs-toggle="tab" href="#documents-tab" role="tab" aria-controls="documents-tab" aria-expanded="false">
                            <span>{{ trans('devices.device_info.view.documents') }}</span>
                        </a>
                    </li>
                @endcan
                <li class="nav-item">
                    <a class="nav-link text-nowrap" id="history" data-bs-toggle="tab" href="#history-tab" role="tab" aria-controls="history-tab" aria-expanded="false">
                        <span>{{ trans('devices.device_info.view.history') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap" id="request" data-bs-toggle="tab" href="#request-tab" role="tab" aria-controls="request-tab" aria-expanded="false">
                        <span>{{ trans('devices.device_info.view.requests') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap" id="audit" data-bs-toggle="tab" href="#audit-tab" role="tab" aria-controls="audit-tab" aria-expanded="false">
                        <span>{{ trans('devices.device_info.view.audits') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap" id="map" data-bs-toggle="tab" href="#map-tab" role="tab" aria-controls="map-tab" aria-expanded="false">
                        <span>{{ trans('devices.device_info.view.map_view') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap" id="track" data-bs-toggle="tab" href="#track-tab" role="tab" aria-controls="track-tab" aria-expanded="false">
                        <span>{{ trans('devices.device_info.view.tracked_location') }}</span>
                    </a>
                </li>
                @if(config('services.assets.rfid_integration') && Auth::user()->hasPermissionTo('DeviceMovement'))
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" id="deviceMovement" data-bs-toggle="tab" href="#deviceMovement-tab" role="tab" aria-controls="deviceMovement-tab" aria-expanded="false">
                            <span>{{ trans('devices.device_info.view.device_movement') }}</span>
                        </a>
                    </li>
                @endif
                @if( config('services.rdp.enabled') == 1 && config('services.rdp.group_id') != "")
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" id="rdp" data-bs-toggle="tab" href="#rdp-tab" role="tab" aria-controls="rdp-tab" aria-expanded="false">
                            <span>{{ trans('devices.device_info.view.rdp') }}</span>
                        </a>
                    </li>
                @endif
                @can('DeviceAccounting')
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" id="accounting-tab" data-bs-toggle="tab" href="#accounting" role="tab" aria-controls="accounting" aria-expanded="false">
                            <span>{{ trans('devices.device_info.view.accounting') }}</span>
                        </a>
                    </li>
                @endcan
                @if($settings->cost_earned == 1)
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" id="cost-earned-tab" data-bs-toggle="tab" href="#cost-earned" role="tab" aria-controls="cost-earned" aria-expanded="false">
                            <span>{{ trans('devices.device_info.view.cost_earning') }}</span>
                        </a>
                    </li>
                @endif
                @if( config('services.gatepass.enabled') == 1 )
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" id="gatepass-tab" data-bs-toggle="tab" href="#gatepass" role="tab" aria-controls="gatepass" aria-expanded="false">
                            <span>{{ trans('devices.device_info.view.gatepass') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
        <div class="row g-4">
            <div class="col-lg-8 col-xl-9">
                <div class="tab-content tabcontent-border p-3" id="myTabContent">
                    <div class="tab-pane fade show active" id="info-tab" role="tabpanel" aria-labelledby="info">
                        <div class="container-fluid p-0 card-body">
                            <div class="row g-4">
                                <div id="device_info_tab">@include('devices.info_tab')</div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="rdp-power-tab" role="tabpanel" aria-labelledby="rdp-power">
                        <div class="d-flex justify-content-center align-items-center">
                            <h3>{{ trans('devices.device_info.view.coming_soon') }}</h3>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="patch-management-tab" role="tabpanel" aria-labelledby="patch-management">
                        <div class="d-flex justify-content-center align-items-center">
                            <h3>{{ trans('devices.device_info.view.coming_soon') }}</h3>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="maintenances-tab" role="tabpanel" aria-labelledby="maintenances">
                        <div class="card">
                            <div class="card-body table-responsive">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <div class="col-auto">
                                        <select class="showSelect amg-table-pagination-dropdown datatable-pagelength" data-select-table="maintenances">
                                            <option value="10" selected>{{ trans('devices.device_info.view.show') }} (10)</option>
                                            <option value="25">{{ trans('devices.device_info.view.show') }} (25)</option>
                                            <option value="50">{{ trans('devices.device_info.view.show') }} (50)</option>
                                            <option value="100">{{ trans('devices.device_info.view.show') }} (100)</option>
                                        </select>
                                    </div>
                                    <div class="flex-grow-1"></div>
                                    <div class="amg-list-searchbar">
                                        <svg data-select-table="maintenances" class="amg-list-searchbar__icon datatable-search-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                        </svg>
                                        <input type="text" class="amg-list-searchbar__input datatable-search-input" placeholder="{{ trans('devices.device_info.view.search') }}" data-select-table="maintenances">
                                    </div>
                                    <button class="amg-refresh-btn btn-reload-list" data-select-table="maintenances">
                                        <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="currentColor"></path>
                                        </svg>
                                        <span>{{ trans('devices.device_info.view.refresh') }}</span>
                                    </button>
                                    @if(!in_array($deviceStatus?->status_id, [5, 7]) && auth()->user()->can('DeviceExpenseAdd'))
                                        <button class="amg-btn amg-btn-primary open-add-modal btn-add-maintenance" type="button" data-bs-toggle="tooltip" title="{{ trans('consumables.consumables_info.view.add') }}">
                                            <svg width="19" height="19" viewBox="0 0 19 19" fill="none">
                                                <path d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z" fill="currentColor" />
                                            </svg>
                                            <span>{{ trans('consumables.consumables_info.view.add') }}</span>
                                        </button>
                                    @endif
                                    @can('DeviceExpenseDownload')
                                        <button class="header-icon-btn-only header-icon-btn-only-sm btn-export-maintenance" data-bs-toggle="tooltip" aria-label="Download" data-bs-original-title="Download">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M4 21.4V2.6C4 2.26863 4.26863 2 4.6 2H16.2515C16.4106 2 16.5632 2.06321 16.6757 2.17574L19.8243 5.32426C19.9368 5.43679 20 5.5894 20 5.74853V21.4C20 21.7314 19.7314 22 19.4 22H4.6C4.26863 22 4 21.7314 4 21.4Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                </path>
                                                <path d="M8 10L16 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M8 18L16 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M8 14L12 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M16 2V5.4C16 5.73137 16.2686 6 16.6 6H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                            </svg>
                                        </button>
                                    @endcan
                                </div>
                                <table id="tblMaintenance" class="table w-100">
                                    <thead>
                                        <tr>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.id') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.expense_type') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.table_title') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.expense_date') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.cost') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.updated_on') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.actions') }}</h4>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="maintenance-cost-tab" role="tabpanel" aria-labelledby="maintenance-cost">
                        <div class="d-flex justify-content-center align-items-center">
                            <h3>{{ trans('devices.device_info.view.coming_soon') }}</h3>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="licenses-tab" role="tabpanel" aria-labelledby="licenses">
                        <div class="card">
                            <div class="card-body table-responsive">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <div class="col-auto">
                                        <select class="showSelect amg-table-pagination-dropdown datatable-pagelength" data-select-table="licenses">
                                            <option value="10" selected>{{ trans('devices.device_info.view.show') }} (10)</option>
                                            <option value="25">{{ trans('devices.device_info.view.show') }} (25)</option>
                                            <option value="50">{{ trans('devices.device_info.view.show') }} (50)</option>
                                            <option value="100">{{ trans('devices.device_info.view.show') }} (100)</option>
                                        </select>
                                    </div>
                                    <div class="flex-grow-1"></div>
                                    <div class="amg-list-searchbar">
                                        <svg data-select-table="licenses" class="amg-list-searchbar__icon datatable-search-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                        </svg>
                                        <input type="text" class="amg-list-searchbar__input datatable-search-input" placeholder="{{ trans('devices.device_info.view.search') }}" data-select-table="licenses">
                                    </div>
                                    <button class="amg-refresh-btn btn-reload-list" data-select-table="licenses">
                                        <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="currentColor"></path>
                                        </svg>
                                        <span>{{ trans('devices.device_info.view.refresh') }}</span>
                                    </button>
                                </div>
                                <table id="tblLicense" class="table w-100">
                                    <thead>
                                        <tr>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.id') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.license') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.serial_code') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.cost') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.actions') }}</h4>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="accessories-tab" role="tabpanel" aria-labelledby="accessories">
                        <div class="card">
                            <div class="card-body table-responsive">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <div class="col-auto">
                                        <select class="showSelect amg-table-pagination-dropdown datatable-pagelength" data-select-table="accessories">
                                            <option value="10" selected>{{ trans('devices.device_info.view.show') }} (10)</option>
                                            <option value="25">{{ trans('devices.device_info.view.show') }} (25)</option>
                                            <option value="50">{{ trans('devices.device_info.view.show') }} (50)</option>
                                            <option value="100">{{ trans('devices.device_info.view.show') }} (100)</option>
                                        </select>
                                    </div>
                                    <div class="flex-grow-1"></div>
                                    <div class="amg-list-searchbar">
                                        <svg data-select-table="accessories" class="amg-list-searchbar__icon datatable-search-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                        </svg>
                                        <input type="text" class="amg-list-searchbar__input datatable-search-input" placeholder="{{ trans('devices.device_info.view.search') }}" data-select-table="accessories">
                                    </div>
                                    <button class="amg-refresh-btn btn-reload-list" data-select-table="accessories">
                                        <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="currentColor"></path>
                                        </svg>
                                        <span>{{ trans('devices.device_info.view.refresh') }}</span>
                                    </button>
                                </div>
                                <table id="tblAccessory" class="table w-100">
                                    <thead>
                                        <tr>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.id') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.accessory_name') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.category') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.cost') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.order_number') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.checkout_date') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.expected_checkin_date') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.actions') }}</h4>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="consumable-tab" role="tabpanel" aria-labelledby="consumable">
                        <div class="card">
                            <div class="card-body table-responsive">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <div class="col-auto">
                                        <select class="showSelect amg-table-pagination-dropdown datatable-pagelength" data-select-table="consumable">
                                            <option value="10" selected>{{ trans('devices.device_info.view.show') }} (10)</option>
                                            <option value="25">{{ trans('devices.device_info.view.show') }} (25)</option>
                                            <option value="50">{{ trans('devices.device_info.view.show') }} (50)</option>
                                            <option value="100">{{ trans('devices.device_info.view.show') }} (100)</option>
                                        </select>
                                    </div>
                                    <div class="flex-grow-1"></div>
                                    <div class="amg-list-searchbar">
                                        <svg data-select-table="consumable" class="amg-list-searchbar__icon datatable-search-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                        </svg>
                                        <input type="text" class="amg-list-searchbar__input datatable-search-input" placeholder="{{ trans('devices.device_info.view.search') }}" data-select-table="consumable">
                                    </div>
                                    <button class="amg-refresh-btn btn-reload-list" data-select-table="consumable">
                                        <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="currentColor"></path>
                                        </svg>
                                        <span>{{ trans('devices.device_info.view.refresh') }}</span>
                                    </button>
                                </div>
                                <table id="tblConsumable" class="table w-100">
                                    <thead>
                                        <tr>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.id') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.batch_no') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.consumable_name') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.category') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.cost') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.checkout_date') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.actions') }}</h4>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                     <div class="tab-pane fade" id="components-tab" role="tabpanel" aria-labelledby="components">
                        <div class="card">
                            <div class="card-body table-responsive">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <div class="col-auto">
                                        <select class="showSelect amg-table-pagination-dropdown datatable-pagelength" data-select-table="components">
                                            <option value="10" selected>{{ trans('devices.device_info.view.show') }} (10)</option>
                                            <option value="25">{{ trans('devices.device_info.view.show') }} (25)</option>
                                            <option value="50">{{ trans('devices.device_info.view.show') }} (50)</option>
                                            <option value="100">{{ trans('devices.device_info.view.show') }} (100)</option>
                                        </select>
                                    </div>
                                    <div class="flex-grow-1"></div>
                                    <div class="amg-list-searchbar">
                                        <svg data-select-table="components" class="amg-list-searchbar__icon datatable-search-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                        </svg>
                                        <input type="text" class="amg-list-searchbar__input datatable-search-input" placeholder="{{ trans('devices.device_info.view.search') }}" data-select-table="components">
                                    </div>
                                    <button class="amg-refresh-btn btn-reload-list" data-select-table="components">
                                        <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="currentColor"></path>
                                        </svg>
                                        <span>{{ trans('devices.device_info.view.refresh') }}</span>
                                    </button>
                                </div>
                                <table id="tblComponent" class="table w-100">
                                    <thead>
                                        <tr>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.id') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.component_name') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.category') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.cost') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.checkout_date') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.expected_checkin_date') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.actions') }}</h4>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tickets-tab" role="tabpanel" aria-labelledby="tickets">
                        <div class="d-flex justify-content-center align-items-center">
                            <h3>{{ trans('devices.device_info.view.coming_soon') }}</h3>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="sez-tab" role="tabpanel" aria-labelledby="sez">
                        <div class="d-flex justify-content-center align-items-center">
                            <h3>{{ trans('devices.device_info.view.coming_soon') }}</h3>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="documents-tab" role="tabpanel" aria-labelledby="documents">
                        <div class="card">
                            <div class="card-body table-responsive">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <div class="col-auto">
                                        <select class="showSelect amg-table-pagination-dropdown datatable-pagelength" data-select-table="documents">
                                            <option value="10" selected>{{ trans('devices.device_info.view.show') }} (10)</option>
                                            <option value="25">{{ trans('devices.device_info.view.show') }} (25)</option>
                                            <option value="50">{{ trans('devices.device_info.view.show') }} (50)</option>
                                            <option value="100">{{ trans('devices.device_info.view.show') }} (100)</option>
                                        </select>
                                    </div>
                                    <div class="flex-grow-1"></div>
                                    <div class="amg-list-searchbar">
                                        <svg data-select-table="documents" class="amg-list-searchbar__icon datatable-search-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                        </svg>
                                        <input type="text" class="amg-list-searchbar__input datatable-search-input" placeholder="{{ trans('devices.device_info.view.search') }}" data-select-table="documents">
                                    </div>
                                    <button class="amg-refresh-btn btn-reload-list" data-select-table="documents">
                                        <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="currentColor"></path>
                                        </svg>
                                        <span>{{ trans('devices.device_info.view.refresh') }}</span>
                                    </button>
                                    @can('DeviceDocumentsUpload')
                                        <button class="amg-btn amg-btn-primary open-add-modal btn_upload_document" type="button" data-bs-toggle="tooltip" title="{{ trans('consumables.consumables_info.view.add') }}">
                                            <svg width="19" height="19" viewBox="0 0 19 19" fill="none">
                                                <path d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z" fill="currentColor" />
                                            </svg>
                                            <span>{{ trans('consumables.consumables_info.view.add') }}</span>
                                        </button>
                                    @endcan
                                </div>
                                <table id="tblDocument" class="table w-100">
                                    <thead>
                                        <tr>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.id') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.document_name') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.updated_on') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.notes') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.actions') }}</h4>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="history-tab" role="tabpanel" aria-labelledby="history">
                        <div class="card">
                            <div class="card-body table-responsive">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <div class="col-auto">
                                        <select class="showSelect amg-table-pagination-dropdown datatable-pagelength" data-select-table="history">
                                            <option value="10" selected>{{ trans('devices.device_info.view.show') }} (10)</option>
                                            <option value="25">{{ trans('devices.device_info.view.show') }} (25)</option>
                                            <option value="50">{{ trans('devices.device_info.view.show') }} (50)</option>
                                            <option value="100">{{ trans('devices.device_info.view.show') }} (100)</option>
                                        </select>
                                    </div>
                                    <div class="flex-grow-1"></div>
                                    <div class="amg-list-searchbar">
                                        <svg data-select-table="history" class="amg-list-searchbar__icon datatable-search-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                        </svg>
                                        <input type="text" class="amg-list-searchbar__input datatable-search-input" placeholder="{{ trans('devices.device_info.view.search') }}" data-select-table="history">
                                    </div>
                                    <button class="amg-refresh-btn btn-reload-list" data-select-table="history">
                                        <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="currentColor"></path>
                                        </svg>
                                        <span>{{ trans('devices.device_info.view.refresh') }}</span>
                                    </button>
                                </div>
                                <table id="tblHistory" class="table w-100">
                                    <thead>
                                        <tr>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.id') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.date') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.admin') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.action_type') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.user_place') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.project_name') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.checkin_checkout_reason') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.notes') }}</h4>
                                            </th>
                                            <th>
                                                <h4>{{ trans('devices.device_info.view.view') }}</h4>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="request-tab" role="tabpanel" aria-labelledby="request">
                        <div class="d-flex justify-content-center align-items-center">
                            <h3>{{ trans('devices.device_info.view.coming_soon') }}</h3>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="audit-tab" role="tabpanel" aria-labelledby="audit">
                        <div class="d-flex justify-content-center align-items-center">
                            <h3>{{ trans('devices.device_info.view.coming_soon') }}</h3>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="map-tab" role="tabpanel" aria-labelledby="map">
                        <div class="d-flex justify-content-center align-items-center">
                            <h3>{{ trans('devices.device_info.view.coming_soon') }}</h3>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="track-tab" role="tabpanel" aria-labelledby="track">
                        <div class="d-flex justify-content-center align-items-center">
                            <h3>{{ trans('devices.device_info.view.coming_soon') }}</h3>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="deviceMovement-tab" role="tabpanel" aria-labelledby="deviceMovement">
                        <div class="d-flex justify-content-center align-items-center">
                            <h3>{{ trans('devices.device_info.view.coming_soon') }}</h3>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="rdp-tab" role="tabpanel" aria-labelledby="rdp">
                        <div class="d-flex justify-content-center align-items-center">
                            <h3>{{ trans('devices.device_info.view.coming_soon') }}</h3>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="accounting" role="tabpanel" aria-labelledby="accounting-tab">
                        <div class="d-flex justify-content-center align-items-center">
                            <h3>{{ trans('devices.device_info.view.coming_soon') }}</h3>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="cost-earned" role="tabpanel" aria-labelledby="cost-earned-tab">
                        <div class="d-flex justify-content-center align-items-center">
                            <h3>{{ trans('devices.device_info.view.coming_soon') }}</h3>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="gatepass" role="tabpanel" aria-labelledby="gatepass-tab">
                        <div class="d-flex justify-content-center align-items-center">
                            <h3>{{ trans('devices.device_info.view.coming_soon') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-xl-3">
                <div class="card card-custom mb-0 mt-3">
                    {{-- rdp development pending --}}
                    <div class="card-body border-bottom d-none">
                        <h6 class="fw-bold mb-2">{{ trans('devices.device_info.view.current_rdp_status') }}</h6>
                        <div class="d-grid gap-2">
                            <button class="amg-btn amg-btn-ghost text-white" disabled>
                                <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3.5 3.5L17 17M20 10.25C20 12.8359 18.9728 15.3158 17.1443 17.1443C15.3158 18.9728 12.8359 20 10.25 20C7.66414 20 5.18419 18.9728 3.35571 17.1443C1.52723 15.3158 0.5 12.8359 0.5 10.25C0.5 7.66414 1.52723 5.18419 3.35571 3.35571C5.18419 1.52723 7.66414 0.5 10.25 0.5C12.8359 0.5 15.3158 1.52723 17.1443 3.35571C18.9728 5.18419 20 7.66414 20 10.25Z" stroke="white" stroke-linejoin="round"/>
                                </svg>
                                {{ trans('devices.device_info.view.disabled') }}
                            </button>
                            <button class="amg-btn amg-btn-primary">{{ trans('devices.device_info.view.cancel') }}</button>
                        </div>
                    </div>
                    <div class="card-body border-bottom">
                        <div class="d-flex justify-content-between align-items-end mb-1">
                            <h6 class="fw-bold mb-0">{{ trans('devices.device_info.view.warranty_status') }}</h6>
                        </div>

                        <div id="warranty-data-present" class="d-none">
                            <div class="progress" style="height: 6px; background-color: #d1e7dd;" data-bs-toggle="tooltip" data-bs-title="0%">
                                <div id="warranty-progress" class="progress-bar bg-success" role="progressbar" style="width:0%;" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>
                            </div>
    
                            <div class="d-flex justify-content-between mt-2 small text-muted">
                                <span id="warranty-start-date">--</span>
                                <span id="warranty-end-date">---</span>
                            </div>
                        </div>

                        <div id="warranty-data-absent" class="">
                            <h6 class="fw-bold mb-0 text-center mt-4">{{ trans('devices.device_info.view.no_data_available') }}</h6>
                        </div>
                    </div>
                    <div class="card-body border-bottom">
                        <h6 class="fw-bold mb-2">{{ trans('devices.device_info.view.device_images') }}</h6>
                        <img src="{{ $return['device_img'] }}" class="img-fluid rounded border w-100" style="max-height: 100px; object-fit: cover;" alt="Device Image" onerror="this.classList.add('d-none'); this.nextElementSibling.classList.remove('d-none');">
                        <h6 class="fw-bold mb-0 text-center mt-4 d-none">{{ trans('devices.device_info.view.no_image_present') }}</h6>
                    </div>
                    <div class="card-body border-bottom">
                        <h6 class="fw-bold mb-2">{{ trans('devices.device_info.view.company_logo') }}</h6>
                        <img src="{{ $return['company_logo'] }}" class="img-fluid rounded border w-100" style="max-height: 100px; object-fit: cover;" alt="{{ trans('devices.device_info.view.company_logo') }}" onerror="this.classList.add('d-none'); this.nextElementSibling.classList.remove('d-none');">
                        <h6 class="fw-bold mb-0 text-center mt-4 d-none">{{ trans('devices.device_info.view.no_image_present') }}</h6>
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold mb-2">{{ trans('devices.device_info.view.barcode_image') }}</h6>
                        <div class="text-center">
                            <img src="{{ $return['qr'] }}" class="img-fluid border border-dark p-1 rounded-1" alt="{{ trans('devices.device_info.view.qr_code') }}" onerror="this.classList.add('d-none'); this.nextElementSibling.classList.remove('d-none');">
                            <h6 class="fw-bold mb-0 text-center mt-4 d-none">{{ trans('devices.device_info.view.no_qr_present') }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include("devices.modal_expense_html")
        @include("documents.upload_modal")
        @include('devices.history_modal')
    </main>
</div>
@endsection

@push('css')
@endpush

@push('scripts')

<script src="{!! CommonHelper::asset('js/common.js') !!}"></script>
<script src="{!! CommonHelper::asset('js/devices/info.js') !!}"></script>

<script>
    var config = new Object;
    config.url = new Object;
    // config.url.patch_manager_request = "{{url('patch-manager-request')}}";
    config.url.patch_manager_request = "{{url('patch-requested-list')}}";
    config.url.device_requests = "{{ url('device/jx-requests') }}";
    config.url.reject_request = "{{ url('device/reject-requests') }}";
    config.url.devices = "{{ url('devices') }}";
    config.url.info = "{{ url('device/info') }}";
    config.url.gatepass = "{{ url('device/gatepass') }}"; 
    // config.url.patch_management = "{{url('device/patch/list')}}"
    // config.url.patch_management_export = "{{url('device/patch/list/export')}}"
    config.url.patch_management = "{{url('device-patch-list')}}"
    config.url.patch_management_export = "{{url('device-patch-list-export')}}"
    config.url.device_basic_info = "{{ url('device/info-tab') }}";
    config.url.device_history = "{{ url('device/jx-history') }}";
    config.url.device_audits = "{{ url('device/device-audits/ajaxList') }}";
    config.url.assigned_licenses = "{{ url('device/jx-assigned-licenses') }}";
    config.url.assigned_accessories = "{{ url('device/jx-assigned-accessories') }}";
    config.url.assigned_consumables = "{{ url('device/jx-assigned-consumable') }}";
    config.url.component_list = "{{ url('device/jx-assigned-components') }}";
    config.url.assigned_tickets = "{{ url('device/jx-assigned-tickets') }}";
    config.url.accessory_info = "{{ url('accessory/info') }}",
    config.url.consumable_info = "{{ url('consumable-info') }}",
    config.url.component_info = "{{ url('component/info') }}",
    config.url.license_detail = "{{ url('license/detail') }}";
    config.url.license_checkin = "{{ url('licenseseat/checkin') }}";
    config.url.documents = "{{ url('document/jx-documents') }}";
    config.url.document_upload = "{{ url('document/jx-upload') }}";
    config.url.document_download = "{{ url('uploads/documents') }}";
    config.url.purchase_document_download = "{{ url('purchase_attachments') }}";
    config.url.document_delete = "{{ url('document/delete') }}";
    config.url.zip_download = "{{ url('download-zip') }}";
    config.url.maintenance_cost = "{{ url('device-maintenance/cost') }}";
    config.url.deviceMovements = "{{ url('device-movements/jx-deviceMovements') }}";
    config.url.device_sez_info = "{{ url('device/sez-info-tab') }}";
    config.url.device_edit_sez = "{{ url('device/device-edit-sez') }}";
    config.url.device_get_sez = "{{ url('device/device-get-sez') }}";
    config.url.attachment_view  = "{{ url('document-attachment/view') }}";
    config.url.devices_change_info = "{{ url('devices/change-info') }}";
    config.getActivatedUsers = "{{ url('getActivatedUsers') }}";
    config.url.dm = {
        "list": "{{ url('jx-expense') }}",
        "save": "{{ url('expense/save') }}",
        "get": "{{ url('expense/get') }}",
        "delete": "{{ url('expense/delete') }}",
        "export": "{{url('expense-export')}}"
    }

    config.url.add_audit = "{{ url('device-audits/ajaxAdd') }}";
    config.url.edit_audit = "{{ url('device-audits/ajaxEdit') }}";
    config.url.get_audit = "{{ url('device-audits/ajaxGet') }}";
    config.url.delete_audit = "{{ url('device-audits/ajaxDelete') }}";
    config.url.get_image = "{{ url('audit/get-images') }}";
    config.url.add = "{{ url('device/add') }}";
    config.url.edit = "{{ url('device/edit') }}";
    config.url.get = "{{ url('device/get') }}";
    config.url.checkout = "{{ url('device/checkout') }}";
    config.url.checkin = "{{ url('device/checkin') }}";
    config.url.resale = "{{ url('device/resale') }}";
    config.url.restore_sold_device = "{{ url('device/restore_sold_device') }}";
    config.url.delete = "{{ url('device/delete') }}";
    config.url.restore = "{{ url('device/restore') }}";
    config.url.print_device_barcode = "{{ url('print-device-barcode') }}";
    config.url.print_device_onecol = "{{ url('print-one-col') }}";
    config.url.print_device_twocol = "{{ url('print-two-col') }}";
    config.url.print_verticalcol = "{{ url('print-vertical-col') }}";
    config.url.editsez = "{{ url('device/editsez') }}";
    config.url.task_details = "{{ url('my-allocated-task-list') }}";
    config.url.update_status = "{{ url('my-allocated-plan-update-status') }}";
    config.url.get_allocated_plan = "{{ url('get-allocated-plan') }}";
    config.url.status_history = "{{ url('get-status-history') }}";
    config.getCostByAjax = "{{ url('getCostByAjax') }}";
    config.getDropDown = "{{ url('getDeviceRateDropDown') }}";
    config.url.getRdp = "{{ url('getRdpPower') }}";
    config.url.supplier_add = "{{ url('supplier/add') }}";
    config.ajaxGetInternalPlace = "{{url('ajaxGetInternalPlace')}}",
    config.getPredefinedDropdownByQuery = "{{url('getByCustomDropDown')}}",
    config.url.getReasonOptions = "{{ url('getReasonsByQuery') }}",
    config.companies = {!! json_encode($companies) !!};
    config.deviceStatus = {!! json_encode($deviceStatus) !!};
    config.currencies = {!! json_encode($currencies) !!};
    config.places = {!! json_encode($vd->places) !!};
    config.project = {!! json_encode($vd->project) !!};
    config.status = {!! json_encode($vd->status)!!};
    config.assetType = {!! json_encode($vd ->assetType)!!}
    config.assignedForOptions = {!! json_encode($vd->assignedForOptions) !!};
    config.internalPlaces = {!! json_encode($vd->internal_places)!!};
    config.statusLabels = {!! json_encode($statusLabels) !!};
    config.maintenanceTypes = {!! json_encode($deviceMain->maintenance_types) !!};
    config.dropdown = {!! json_encode($dropdown) !!};
    config.audit_device = {!! json_encode($audit_device) !!};
    config.permissions = {!! json_encode($permissionArray, true) !!};
    config.auth_user = {{ Auth::user()->id }};
    config.client = @json(config('app.client'));
    config.getLocationByAjax = "{{ url('getLocationByQuery') }}";
    config.getInternalPlaceByAjax = "{{ url('getInternalPlaceByAjax') }}";
    config.getInternalPlaceByLocation = "{{ url('getInternalPlaceByLocation') }}";
    config.url.getAssetDepartments = "{{ route('getAssetDepartments') }}";
    config.getSupplierByAjax = "{{ url('getSupplierByQuery') }}";
    config.getModelByAjax = "{{ url('getModelByQuery') }}";
    config.getUserByAjax = "{{ url('getUserForDropDown') }}";
    config.getLeaseByAjax = "{{ url('getLeaseByQuery') }}";
    config.getCustomFieldsByModel = "{{ url('getCustomFieldsByModel') }}";
    config.getInvoiceByAjax = "{{ url('getInvoiceByQuery') }}";
    config.nextIdByAjax = "{{ url('getincrementedAssetID') }}";
    config.imgview_path = "{{ url('device/info') }}";
    config.imgviewpath = "{{ url('uploads/devices') }}";
    config.status_list = {!! json_encode($vd->scheduleMaintenanceStatus)!!};
    config.translations = {
        add_new_device: '{{ trans('devices.device_info.config.add_new_device') }}',
        edit_device: '{{ trans('devices.device_info.config.edit_device') }}',
        save: '{{ trans('devices.device_info.config.save') }}',
        download_document: '{{ trans('devices.device_info.config.download_document') }}',
        refresh_list: '{{ trans('devices.device_info.config.refresh_list') }}',
        press_enter_with_search: '{{ trans('devices.device_info.config.press_enter_with_search') }}',
        please_enter_valid_search: '{{ trans('devices.device_info.config.please_enter_valid_search') }}',
        add_expense: '{{ trans('devices.device_info.config.add_expense') }}',
        save_details: '{{ trans('devices.device_info.config.save_details') }}',
        edit_device_maintenance_details: '{{ trans('devices.device_info.config.edit_device_maintenance_details') }}',
        something_went_wrong: '{{ trans('devices.device_info.config.something_went_wrong') }}',
        are_you_restore: '{{ trans('devices.device_info.config.are_you_restore') }}',
        are_you_delete: '{{ trans('devices.device_info.config.are_you_delete') }}',
        are_you_sold: '{{ trans('devices.device_info.config.are_you_sold') }}',
        are_you_delete_record: '{{ trans('devices.device_info.config.are_you_delete_record') }}',
        select_the_service_type: '{{ trans('devices.device_info.config.select_the_service_type') }}',
        select_the_supplier: '{{ trans('devices.device_info.config.select_the_supplier') }}',
        select_currency_format: '{{ trans('devices.device_info.config.select_currency_format') }}',
        new_document_upload: '{{ trans('devices.device_info.config.new_document_upload') }}',
        save_changes: '{{ trans('devices.device_info.config.save_changes') }}',
        delete_document: '{{ trans('devices.device_info.config.delete_document') }}',
        upload_document: '{{ trans('devices.device_info.config.upload_document') }}',
        select_status: '{{ trans('devices.device_info.config.select_status') }}',
        select_company: '{{ trans('devices.device_info.config.select_company') }}',
        select_place: '{{ trans('devices.device_info.config.select_place') }}',
        select_project: '{{ trans('devices.device_info.config.select_project') }}',
        no_tracks_available: '{{ trans('devices.device_info.config.no_tracks_available') }}',
        enable: '{{ trans('devices.device_info.config.enable') }}',
        disable: '{{ trans('devices.device_info.config.disable') }}',
        asset_type: '{{ trans('devices.device_info.config.asset_type') }}',
        select_the_department: '{{ trans('devices.device_info.config.select_the_department') }}',
        select_the_location: '{{ trans('devices.device_info.config.select_the_location') }}',
        select_the_purchase_invoice: '{{ trans('devices.device_info.config.select_the_purchase_invoice') }}',
        enter_rfid_tags: '{{ trans('devices.device_info.config.enter_rfid_tags') }}',
        select_the_type: '{{ trans('devices.device_info.config.select_the_type') }}',
        edit_expense: '{{ trans('devices.device_info.config.edit_expense') }}',
        select_internal_place: '{{ trans('devices.device_info.config.select_internal_place') }}',
        cost: '{{ trans('devices.device_info.config.cost') }}',
        task_details: '{{ trans('devices.device_info.config.task_details') }}',
        status: '{{ trans('devices.device_info.config.status') }}',
        history: '{{ trans('devices.device_info.config.history') }}',
        changes_done_by: '{{ trans('devices.device_info.config.changes_done_by') }}',
        status_changes: '{{ trans('devices.device_info.config.status_changes') }}',
        remarked_as: '{{ trans('devices.device_info.config.remarked_as') }}',
        status_history_not_available: '{{ trans('devices.device_info.config.status_history_not_available') }}',
        no_data_avilable: '{{ trans('devices.device_info.config.no_data_avilable') }}',
        select_the_amc_supplier: '{{ trans('devices.device_info.config.select_the_amc_supplier') }}',
        search: '{{ trans('devices.device_info.config.search') }}',
        updated: '{{ trans('devices.device_info.config.updated') }}',
        request: '{{ trans('devices.device_info.config.request') }}',
        os: '{{ trans('devices.device_info.config.os') }}',
        patch_name: '{{ trans('devices.device_info.config.patch_name') }}',
        patch_description: '{{ trans('devices.device_info.config.patch_description') }}',
        patch_version: '{{ trans('devices.device_info.config.patch_version') }}',
        severity: '{{ trans('devices.device_info.config.severity') }}',
        reboot_required: '{{ trans('devices.device_info.config.reboot_required') }}',
        approved_status: '{{ trans('devices.device_info.config.approved_status') }}',
        patch_details: '{{ trans('devices.device_info.config.patch_details') }}',
        download: '{{ trans('devices.device_info.config.download') }}',
        bulk_patch: '{{ trans('devices.device_info.config.bulk_patch') }}',
        please_select_at_least: '{{ trans('devices.device_info.config.please_select_at_least') }}',
        select_the_devices: '{{ trans('devices.device_info.config.select_the_devices') }}',
        select_the_project: '{{ trans('devices.device_info.config.select_the_project') }}',
        select_the_contract: '{{ trans('devices.device_info.config.select_the_contract') }}',
        select_the_component: '{{ trans('devices.device_info.config.select_the_component') }}',
        select_the_license: '{{ trans('devices.device_info.config.select_the_license') }}',
        select_the_task: '{{ trans('devices.device_info.config.select_the_task') }}',
        select_the_change_management: '{{ trans('devices.device_info.config.select_the_change_management') }}',
        select_the_ticket: '{{ trans('devices.device_info.config.select_the_ticket') }}',
        select_the_ticket_procure_request: '{{ trans('devices.device_info.config.select_the_ticket_procure_request') }}',
        select_the_manufacturer: '{{ trans('devices.device_info.config.select_the_manufacturer') }}',
        select_the_device: '{{ trans('devices.device_info.config.select_the_device') }}',
        select_the_user: '{{ trans('devices.device_info.config.select_the_user') }}',
        select_the_model: '{{ trans('devices.device_info.config.select_the_model') }}',
        dispose: '{{ trans('devices.device_info.config.dispose') }}',
        clone_device: '{{ trans('devices.device_info.config.clone_device') }}',
        requested_list: '{{ trans('devices.device_info.config.requested_list') }}',
        column_visibility: '{{ trans('devices.device_info.config.column_visibility') }}',
        system_update: '{{ trans('devices.device_info.config.system_update') }}',
        checkinout_reason: '{{ trans('devices.device_info.config.checkinout_reason') }}',
        checkout_reason: '{{ trans('devices.device_info.config.checkout_reason') }}',
        select_checkout_reason: '{{ trans('devices.device_info.config.select_checkout_reason') }}',
        checkin_reason: '{{ trans('devices.device_info.config.checkin_reason') }}',
        select_checkin_reason: '{{ trans('devices.device_info.config.select_checkin_reason') }}',
        batch_no: '{{ trans('devices.device_info.config.batch_no') }}',
        check_in: '{{ trans('devices.device_info.config.check_in') }}',
        component_unique_tag: '{{ trans('devices.device_info.config.component_unique_tag') }}',
        valid_cost: '{{ trans('devices.device_info.controller.pls_enter_valid_cost_length') }}',
        license_tag: '{{ trans('devices.device_info.config.license_tag') }}',
        document_delete_permanently: '{{ trans('devices.device_info.config.document_delete_permanently') }}',
        action_download_asset: '{{ trans('devices.device_info.config.action_download_asset') }}',
        action_delete_asset: '{{ trans('devices.device_info.config.action_delete_asset') }}',
        action_view_asset: '{{ trans('devices.device_info.config.action_view_asset') }}',
        pls_upload_document: '{{ trans('devices.device_info.config.pls_upload_document') }}',
        invalid_file_extension: '{{ trans('devices.device_info.config.invalid_file_extension') }}',
        filesize_validation: '{{ trans('devices.device_info.config.filesize_validation') }}',
        file_size_max: '{{ trans('devices.device_info.config.file_size_max') }}'
    };
    config.default_currency_format = "{{ CommonHelper::settings()->default_currency }}";
    config.deployedLabel = {!! $deployedLabel !!};
    config.firstDeployable = {!! $firstDeployable !!};
    config.trackedLocations = {!! json_encode($vd->trackedLocations) !!};
    config.ratingsOptions = {!! json_encode($vd->ratingsOptions) !!};
    config.custom_fields = {!! !empty($companyFieldset->fields) ? json_encode(CommonHelper::formCustomFields($companyFieldset->fields)) : json_encode('') !!};
    config.device_id = {{ $device->id }};
    config.device = {!! json_encode($device) !!};
    config.is_rdp_enabled = "{{ config('services.rdp.enabled') }}";
    config.loginToken = "{{ config('services.rdp.token') }}";
    config.rdpAccessToken = "";
    config.getDeviceByAjax = "{{ url('getDeviceByQuery') }}";
    config.token = "{{ csrf_token() }}";
    config.deviceMovementEnabled = "{{ config('services.assets.rfid_integration') }}";
    config.url.network_info = "{{ url('devices/network_inventory/info') }}";
    config.url.rdp_update_history = "{{ url('device/add-rdp-history') }}";
    config.url.getRdpHistory = "{{ url('device/jx-rdp-history') }}";
    config.url.getCostEarned = "{{ url('device/jx-cost-earning-history') }}";
    config.url.getData = "{{ url('device/accounting') }}";
    config.url.updateRdpStatus = "{{ url('device/jx-update-rdp-status') }}";
    config.getDescriptionByAjax ="{{ url('getDescriptionByAjax') }}"
    config.url.getDeviceForDropDown = "{{ url('sw_patch/getDeviceForDropDown') }}";
    config.url.getUsersEmailSelect = "{{ url('getUsersEmailSelect') }}";
    // config.url.bulk_patch = "{{ url('request-patch-by-device') }}";
    config.url.bulk_patch = "{{url('patch-request-by-device')}}";
    config.check_access = @if(Auth::user()->hasRole(['SuperAdmin','Admin'])) 1 @else 0; @endif;
    config.url.getWarrantyDate = "{{ url('device/get-warranty-date') }}";
    config.url.getDeviceItems = "{{url('getDeviceItems')}}",
    config.url.getPublisherByAjax = "{{url('getPublisherByAjax')}}",
    @if($isCheckinCall)
        config.isCheckinCall = true;
    @endif

    new MyApp(config);
</script>
@endpush

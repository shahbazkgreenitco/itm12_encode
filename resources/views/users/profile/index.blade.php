{{--
/**
* ------------------------------------------------------------
* File: index.blade.php
* Module: Profile
* PROF/26/01
* ------------------------------------------------------------
* Version: 1.0.1
* Author: Hrishikesh Pandey
* Page ID: #PROF-001
* Created On: 2026-05-27
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.1] - Changes for the translations and adding new labels
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}

@extends('layouts.layout1')
@section('title', trans('user.profile.users_profile'))
@section('content')
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
        <h3 class="h3-text mb-0">{{ trans('user.profile.users_profile') }}</h3>
    </div>
    <!-- CONTENT -->
    <main class="main-content" id="mainContent">

        <div class="container-fluid py-5">
            <div class="card shadow-none rounded-1 card-border-light">

                <!-- Header Section -->
                <div class="card-body border-bottom">
                    <div class="row align-items-center">

                        <!-- Left: Profile Info -->
                        <div class="col-md-8 col-12">
                            <div class="d-flex align-items-center">
                                {{-- @dd($user->getProfileImg()); --}}
                                <img class="profile-img me-3" src="{{ $user->getProfileImg() }}" alt="">
                                <div>
                                    {{-- <h4 class="mb-1 fw-semibold">Ananth Selvaraj</h4> --}}
                                    <h4 class="mb-1 fw-semibold">{{ $user->getGuranteedNameText(true) }}</h4>
                                    {{-- <small class="b1-text text-muted">User</small> --}}
                                    <small class="b1-text text-muted">@foreach(Auth::user()->roles->pluck('name') as $rolename) {{$rolename}} @endforeach</small>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Edit Button -->
                        <div class="col-md-4 col-12 text-md-end mt-3 mt-md-0">
                            <!-- <button class="btn btn-outline-primary">Edit Profile</button> -->
                            <button class="amg-btn amg-btn-outline edit-profile rounded-1 "
                                data-href="{{ url('profile/edit') }}">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none"
                                    >
                                    <path
                                        d="M15.2594 3.85684L11.768 0.366218C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366218L0.366412 9.99981C0.249834 10.1155 0.157407 10.2531 0.0945056 10.4048C0.0316038 10.5565 -0.000518312 10.7192 6.32418e-06 10.8834V14.3748C6.32418e-06 14.7063 0.131702 15.0243 0.366123 15.2587C0.600543 15.4931 0.918485 15.6248 1.25001 15.6248H14.375C14.5408 15.6248 14.6997 15.559 14.8169 15.4418C14.9342 15.3245 15 15.1656 15 14.9998C15 14.8341 14.9342 14.6751 14.8169 14.5579C14.6997 14.4407 14.5408 14.3748 14.375 14.3748H6.50938L15.2594 5.62481C15.3755 5.50873 15.4676 5.37092 15.5304 5.21925C15.5933 5.06757 15.6256 4.905 15.6256 4.74083C15.6256 4.57665 15.5933 4.41408 15.5304 4.26241C15.4676 4.11073 15.3755 3.97292 15.2594 3.85684ZM3.69688 12.8123L10.7547 5.7545L12.0586 7.05762L5.00001 14.1162L3.69688 12.8123ZM9.86876 4.87012L2.81251 11.9287L1.5086 10.6248L8.56719 3.567L9.86876 4.87012ZM1.25001 12.1334L3.49141 14.3748H1.25001V12.1334Z"
                                        fill="currentColor" />
                                </svg>

                                <span>{{ trans('user.profile.edit_profile') }}</span>

                            </button>
                        </div>

                    </div>
                </div>

                <!-- Body Section -->
                <div class="card-body">
                    <div class="row">

                        <!-- Left: User Details -->
                        <div class="col-md-9 col-12">
                            <div class="profile-details p-4">
                                <!-- Row 1 -->
                                <div class="row align-items-center mb-3">
                                    <div class="col-md-4 col-12 d-flex align-items-center text-muted info-label">
                                        <svg style="opacity:.8;" width="15" height="17" viewBox="0 0 15 17"
                                            fill="none" >
                                            <path
                                                d="M2.91667 4.58333C2.91667 3.36776 3.39955 2.20197 4.25909 1.34243C5.11864 0.482886 6.28442 0 7.5 0C8.71558 0 9.88136 0.482886 10.7409 1.34243C11.6004 2.20197 12.0833 3.36776 12.0833 4.58333C12.0833 5.79891 11.6004 6.9647 10.7409 7.82424C9.88136 8.68378 8.71558 9.16667 7.5 9.16667C6.28442 9.16667 5.11864 8.68378 4.25909 7.82424C3.39955 6.9647 2.91667 5.79891 2.91667 4.58333ZM0 14.1667C0 13.0616 0.438987 12.0018 1.22039 11.2204C2.00179 10.439 3.0616 10 4.16667 10H10.8333C11.9384 10 12.9982 10.439 13.7796 11.2204C14.561 12.0018 15 13.0616 15 14.1667V16.6667H0V14.1667Z"
                                                fill="currentColor" />
                                        </svg>

                                        <span class="s1-text text-muted">{{ trans('user.profile.username') }}</span>
                                    </div>
                                    <div class="col-md-8 col-12">
                                        <span class="s1-text">{{ $user->username }}</span>
                                    </div>
                                </div>
                                @if($user->employee_num != "")
                                    <div class="row align-items-center mb-3">
                                        <div class="col-md-4 col-12 d-flex align-items-center text-muted info-label">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.36672 8.99809C11.3488 8.99809 12.9556 7.39129 12.9556 5.4092C12.9556 3.42711 11.3488 1.82031 9.36672 1.82031C7.38463 1.82031 5.77783 3.42711 5.77783 5.4092C5.77783 7.39129 7.38463 8.99809 9.36672 8.99809Z" fill="#7F7F7F"/>
                                                <path d="M11.6665 15.5547H15.5554V16.3325H11.6665V15.5547Z" fill="#7F7F7F"/>
                                                <path d="M8.33326 16.6668V18.3335C8.33326 18.4808 8.39179 18.6221 8.49597 18.7263C8.60016 18.8305 8.74147 18.889 8.88881 18.889H18.3333C18.4806 18.889 18.6219 18.8305 18.7261 18.7263C18.8303 18.6221 18.8888 18.4808 18.8888 18.3335V12.7779C18.8888 12.6306 18.8303 12.4893 18.7261 12.3851C18.6219 12.2809 18.4806 12.2224 18.3333 12.2224H14.4444V11.4057C14.4444 11.2583 14.3858 11.117 14.2816 11.0128C14.1775 10.9087 14.0362 10.8501 13.8888 10.8501C13.7415 10.8501 13.6002 10.9087 13.496 11.0128C13.3918 11.117 13.3333 11.2583 13.3333 11.4057V12.2224H12.2221V10.2335C11.2781 10.079 10.3232 10.001 9.36659 10.0001C7.25703 9.99116 5.17112 10.4444 3.25548 11.3279C2.94019 11.4767 2.6742 11.7128 2.48908 12.0082C2.30395 12.3036 2.20745 12.646 2.21103 12.9946V16.6668H8.33326ZM17.7777 17.7779H9.44437V13.3335H13.3333V13.5668C13.3333 13.7141 13.3918 13.8554 13.496 13.9596C13.6002 14.0638 13.7415 14.1224 13.8888 14.1224C14.0362 14.1224 14.1775 14.0638 14.2816 13.9596C14.3858 13.8554 14.4444 13.7141 14.4444 13.5668V13.3335H17.7777V17.7779Z" fill="#7F7F7F"/>
                                            </svg>
                                            <span class="s1-text text-muted">{{ trans('user.profile.employee_number') }}</span>
                                        </div>
                                        <div class="col-md-8 col-12">
                                            <span class="s1-text">{{ $user->employee_num }}</span>
                                        </div>
                                    </div>
                                @endif
                                @if($user->email != "")
                                <div class="row align-items-center mb-3">
                                    <div class="col-md-4 col-12 d-flex  align-items-center text-muted  info-label">
                                        <svg style="opacity:.8;" width="16" height="14" viewBox="0 0 16 14" fill="none" >
                                            <path d="M14.4 0H1.6C0.72 0 0.00799999 0.7875 0.00799999 1.75L0 12.25C0 13.2125 0.72 14 1.6 14H14.4C15.28 14 16 13.2125 16 12.25V1.75C16 0.7875 15.28 0 14.4 0ZM14.4 3.5L8 7.875L1.6 3.5V1.75L8 6.125L14.4 1.75V3.5Z" fill="currentColor" />
                                        </svg>

                                        <span class="s1-text text-muted">{{ trans('user.profile.email') }}</span>
                                    </div>
                                    <div class="col-md-8 col-12">
                                        <span class="s1-text">{{ $user->email }}</span>
                                    </div>
                                </div>
                                @endif
                                @if($user->phone != "")
                                <div class="row align-items-center mb-3">
                                    <div class="col-md-4 col-12 d-flex align-items-center text-muted  info-label">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M16.025 12.7161L13.9084 12.4745C13.6595 12.4452 13.4072 12.4728 13.1705 12.5551C12.9338 12.6373 12.7188 12.7722 12.5417 12.9495L11.0084 14.4828C8.64286 13.2794 6.72009 11.3567 5.5167 8.99115L7.05837 7.44948C7.4167 7.09115 7.5917 6.59115 7.53337 6.08281L7.2917 3.98281C7.24463 3.57622 7.04959 3.20117 6.74375 2.92914C6.43792 2.65712 6.04268 2.50715 5.63337 2.50781H4.1917C3.25003 2.50781 2.4667 3.29115 2.52503 4.23281C2.9667 11.3495 8.65837 17.0328 15.7667 17.4745C16.7084 17.5328 17.4917 16.7495 17.4917 15.8078V14.3661C17.5 13.5245 16.8667 12.8161 16.025 12.7161Z" fill="#7F7F7F"/>
                                            </svg>
                                            <span class="s1-text text-muted">{{ trans('user.profile.phone') }}</span>
                                        </div>
                                        <div class="col-md-8 col-12">
                                            <span class="s1-text">{{ $user->phone }}</span>
                                        </div>
                                    </div>
                                @endif
                                @if($user->department_id != "")
                                    <div class="row align-items-center mb-3">
                                        <div class="col-md-4 col-12 d-flex align-items-center text-muted  info-label">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M5.99984 5.33594V2.66927C5.99984 2.31565 6.14031 1.97651 6.39036 1.72646C6.64041 1.47641 6.97955 1.33594 7.33317 1.33594H12.6665C13.0201 1.33594 13.3593 1.47641 13.6093 1.72646C13.8594 1.97651 13.9998 2.31565 13.9998 2.66927V5.33594H15.9998C16.3535 5.33594 16.6926 5.47641 16.9426 5.72646C17.1927 5.97651 17.3332 6.31565 17.3332 6.66927V14.6693C17.3332 15.0229 17.1927 15.362 16.9426 15.6121C16.6926 15.8621 16.3535 16.0026 15.9998 16.0026H3.99984C3.64622 16.0026 3.30708 15.8621 3.05703 15.6121C2.80698 15.362 2.6665 15.0229 2.6665 14.6693V6.66927C2.6665 6.31565 2.80698 5.97651 3.05703 5.72646C3.30708 5.47641 3.64622 5.33594 3.99984 5.33594H5.99984ZM7.33317 2.66927V5.33594H12.6665V2.66927H7.33317Z" fill="#7F7F7F"/>
                                            </svg>

                                            <span class="s1-text text-muted">{{ trans('user.profile.department_name') }}</span>
                                        </div>
                                        <div class="col-md-8 col-12">
                                            <span class="s1-text">{{ $user->department->name ?? "" }}</span>
                                        </div>
                                    </div>
                                @endif
                                @if($user->managerProp("username") != "")
                                    <div class="row align-items-center mb-3">
                                        <div class="col-md-4 col-12 d-flex align-items-center text-muted  info-label">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M17 16.9016V14.4016C17 13.7016 16.9 13.0016 16.5 12.3016C16.1 11.6016 15.6 11.0016 14.9 10.6016C14.2 10.1016 12.7 10.0016 12 10.0016L10.4 11.7016L11 13.0016V16.0016L10 17.1016L9 16.0016V13.0016L9.7 11.7016L8 10.0016C7.2 10.0016 5.7 10.1016 5 10.6016C4.3 11.0016 3.9 11.6016 3.5 12.3016C3.1 13.0016 3 13.6016 3 14.4016V16.9016C3 16.9016 5.6 18.0016 10 18.0016C14.4 18.0016 17 16.9016 17 16.9016ZM10 2.10156C8.1 2.10156 7 3.90156 7.3 5.90156C7.6 7.90156 8.6 9.30156 10 9.30156C11.4 9.30156 12.4 7.90156 12.7 5.90156C13 3.80156 11.9 2.10156 10 2.10156Z" fill="#7F7F7F"/>
                                            </svg>

                                            <span class="s1-text text-muted">{{ trans('user.profile.manager_name') }}</span>
                                    </div>
                                    <div class="col-md-8 col-12">
                                        <span class="s1-text">{{ $user->managerProp('username') }}</span>
                                    </div>
                                </div>
                                @endif
                                @if($user->locationProp("name") != "")
                                    <div class="row align-items-center mb-3">
                                    <div class="col-md-4 col-12 d-flex align-items-center text-muted  info-label">

                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M10.0002 1.66406C6.32524 1.66406 3.33357 4.65573 3.33357 8.33073C3.30857 13.6974 9.26691 17.9974 9.51691 18.1807C9.65857 18.2807 9.83357 18.3391 10.0002 18.3391C10.1669 18.3391 10.3419 18.2891 10.4836 18.1807C10.7336 17.9974 16.6919 13.7057 16.6669 8.33073C16.6669 4.65573 13.6752 1.66406 10.0002 1.66406ZM10.0002 11.6641C8.15857 11.6641 6.66691 10.1724 6.66691 8.33073C6.66691 6.48906 8.15857 4.9974 10.0002 4.9974C11.8419 4.9974 13.3336 6.48906 13.3336 8.33073C13.3336 10.1724 11.8419 11.6641 10.0002 11.6641Z" fill="#7F7F7F"/>
                                        </svg>

                                            <span class="s1-text text-muted">{{ trans('user.profile.location') }}</span>

                                    </div>
                                        <div class="col-md-8 col-12">
                                            <span class="s1-text">{{ $user->locationProp('name') }}</span>
                                        </div>
                                    </div>
                                @endif
                                @if($user->userDetails != null)
                                    <div class="row align-items-center mb-3">
                                        <div class="col-md-4 col-12 d-flex align-items-center text-muted  info-label">

                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M10.5609 5.8602C10.6625 5.7566 10.7132 5.6285 10.7132 5.4759C10.7132 5.3233 10.662 5.19567 10.5595 5.093C10.4571 4.99033 10.3304 4.939 10.1795 4.939C10.0285 4.939 9.90208 4.99033 9.80008 5.093C9.69808 5.19567 9.64731 5.324 9.64777 5.478C9.64823 5.632 9.69946 5.75963 9.80146 5.8609C9.90346 5.96217 10.0302 6.0135 10.1815 6.0149C10.3329 6.0163 10.4594 5.96427 10.5609 5.8602ZM6 17V3H14.361V6.381H15V8.9612H14.361V17H6Z" fill="#7F7F7F"/>
                                            </svg>
                                            <span class="s1-text text-muted">{{ $user->userDetails->platform_type == 2 ? trans("user.profile.ios_version") : trans('user.profile.android_version') }}</span>
                                        </div>
                                        <div class="col-md-8 col-12">
                                            <span class="s1-text">{{  $user->userDetails->app_version ?? '' }}</span>
                                        </div>
                                    </div>
                                @endif
                                @if($user->website != "")
                                    <div class="row align-items-center mb-3">
                                        <div class="col-md-4 col-12 d-flex align-items-center text-muted  info-label">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M10 2.5C11.9891 2.5 13.8968 3.29018 15.3033 4.6967C16.7098 6.10322 17.5 8.01088 17.5 10C17.5 11.9891 16.7098 13.8968 15.3033 15.3033C13.8968 16.7098 11.9891 17.5 10 17.5M17.5 10H2.5M10 2.5C8.01088 2.5 6.10322 3.29018 4.6967 4.6967C3.29018 6.10322 2.5 8.01088 2.5 10C2.5 11.9891 3.29018 13.8968 4.6967 15.3033C6.10322 16.7098 8.01088 17.5 10 17.5M10 17.5C11.6108 17.5 12.9167 14.1417 12.9167 10C12.9167 5.85833 11.6108 2.5 10 2.5M10 17.5C8.38917 17.5 7.08333 14.1417 7.08333 10C7.08333 5.85833 8.38917 2.5 10 2.5" stroke="#7F7F7F" stroke-width="2" stroke-linejoin="round"/>
                                            </svg>
                                            <span class="s1-text text-muted">{{ trans('user.profile.website') }}</span>
                                        </div>
                                        <div class="col-md-8 col-12">
                                            <span class="s1-text">{{ $user->website }}</span>
                                        </div>
                                    </div>
                                @endif
                                @if($user->companyProp('name') != "")
                                    <div class="row align-items-center mb-3">
                                        <div class="col-md-4 col-12 d-flex align-items-center text-muted  info-label">
                                            <svg style="opacity:.8;" width="14" height="17" viewBox="0 0 14 17"
                                                fill="none" >
                                                <path d="M7 0C8.93375 0 10.5 1.69056 10.5 3.77778C10.5 5.865 8.93375 7.55556 7 7.55556C5.06625 7.55556 3.5 5.865 3.5 3.77778C3.5 1.69056 5.06625 0 7 0ZM10.5 9.95444C10.5 10.9556 10.255 13.2883 8.58375 15.895L7.875 11.3333L8.6975 9.55778C8.155 9.49167 7.58625 9.44444 7 9.44444C6.41375 9.44444 5.845 9.49167 5.3025 9.55778L6.125 11.3333L5.41625 15.895C3.745 13.2883 3.5 10.9556 3.5 9.95444C1.40875 10.6156 0 11.8056 0 13.2222V17H14V13.2222C14 11.8056 12.6 10.6156 10.5 9.95444Z" fill="currentColor" />
                                            </svg>
                                            <span class="s1-text text-muted">{{ trans('user.profile.company') }}</span>
                                        </div>
                                        <div class="col-md-8 col-12">
                                            <span class="s1-text">{{ $user->companyProp('name') }}</span>
                                        </div>
                                    </div>
                                @endif
                                @can('DelegateRequest')
                                    @if(!empty($reqDelegation) || !empty($ticketDelegation) || !empty($changeDelegation) || !empty($procureDelegation))
                                        <div class="row align-items-start mb-3">
                                            <div class="col-md-4 col-12 d-flex align-items-center text-muted info-label">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M6.6665 16.6901V9.16927H7.99984C8.09706 9.16927 8.19428 9.17983 8.2915 9.20094C8.38873 9.22205 8.48595 9.24622 8.58317 9.27344L14.354 11.4193C14.5484 11.4887 14.7048 11.6137 14.8232 11.7943C14.9415 11.9748 15.0004 12.1623 14.9998 12.3568C14.9998 12.6484 14.899 12.8845 14.6973 13.0651C14.4957 13.2457 14.2632 13.3359 13.9998 13.3359H11.8123C11.7429 13.3359 11.6909 13.3326 11.6565 13.3259C11.6221 13.3193 11.5768 13.3018 11.5207 13.2734L10.1873 12.7526L9.9165 13.5651L11.5207 14.1276C11.5484 14.1415 11.5901 14.1521 11.6457 14.1593C11.7012 14.1665 11.7498 14.1698 11.7915 14.1693H17.4998C17.9443 14.1693 18.3332 14.329 18.6665 14.6484C18.9998 14.9679 19.1665 15.3637 19.1665 15.8359L12.5207 18.3359L6.6665 16.6901ZM1.6665 18.3359V9.16927H4.99984V18.3359H1.6665ZM11.604 9.21094L8.06234 5.66927L9.24984 4.5026L11.604 6.85677L16.3332 2.14844L17.4998 3.3151L11.604 9.21094Z" fill="#7F7F7F"/>
                                                </svg>
                                                <span class="s1-text text-muted ms-2">{{ trans('user.profile.delegation') }}</span>
                                            </div>
                                            <div class="col-md-8 col-12">
                                                <div class="row g-3">
                                                    @if(!empty($reqDelegation))
                                                        <div class="col-md-6">
                                                            <div class="border rounded p-2">
                                                                <div class="small text-muted">{{ trans('user.profile.request') }}</div>
                                                                <div class="s1-text">{{ $reqDelegation->username ?? "-" }}</div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if(!empty($ticketDelegation))
                                                        <div class="col-md-6">
                                                            <div class="border rounded p-2">
                                                                <div class="small text-muted">{{ trans('user.profile.ticket') }}</div>
                                                                <div class="s1-text">{{ $ticketDelegation->username ?? "-" }}</div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if(!empty($changeDelegation))
                                                        <div class="col-md-6">
                                                            <div class="border rounded p-2">
                                                                <div class="small text-muted">{{ trans('user.profile.change') }}</div>
                                                                <div class="s1-text">{{ $changeDelegation->username ?? "-" }}</div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if(!empty($procureDelegation))
                                                        <div class="col-md-6">
                                                            <div class="border rounded p-2">
                                                                <div class="small text-muted">{{ trans('user.profile.procurement') }}</div>
                                                                <div class="s1-text">{{ $procureDelegation->username ?? "-" }}</div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endcan
                            </div>
                        </div>

                        <!-- Right: QR Section -->
                        <div class="col-md-3 col-12 mt-4 mt-md-0 text-center">
                            <div class="qr-section text-center p-4 ">
                                <div class="user_qrcode"></div>

                                <!-- QR Image -->
                                {{-- <img src="{{ asset('assets/images/profile/qr-img.png') }}" alt="QR Code"
                                        class="img-fluid qr-img mb-3"> --}}

                                <!-- Text -->
                                <p class="b1-text text-muted mb-0">
                                    {{ trans('user.profile.scan_login_mobile_app') }}
                                </p>


                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </div>

    </main>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $(".edit-profile").on("click", function(e) {
                e.preventDefault();
                window.location.href = $(this).attr("data-href");
            });

            function performAjax() {
                var user_id = "{{ encrypt(Auth::user()->id) }}";
                $.ajax({
                    type: "POST",
                    url: "{{ url('user-qrcode') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        user_id: user_id
                    },
                    success: function(response) {
                        if (response.data) {
                            // $(".user_qrcode").html('<img src="data:image/png;base64,' + response.data + '" alt="User QR Code">');
                            $(".user_qrcode").html(
                                '<img src="data:image/png;base64,' +
                                response.data + '" alt="User QR Code">');
                        } else if (response.error) {
                            $(".user_qrcode").html('<p>' + response.error + '</p>');
                        } else {
                            console.error("Unexpected response format");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX call failed:", error);
                    }
                });
            }

            performAjax();
        });
    </script>
@endpush

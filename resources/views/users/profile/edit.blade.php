{{--
/**
* ------------------------------------------------------------
* File: edit.blade.php
* Module: Profile
* PROF/26/02
* ------------------------------------------------------------
* Version: 1.0.1
* Author: Hrishikesh Pandey
* Page ID: #PROF-002
* Created On: 2026-05-27
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version
* [1.0.1] - Changes for the translations and adding new labels
* [1.0.2] - Development for the documents and delegation tab
* [1.0.3] - class changes for service request delegation select and label ui not looking proper
* ------------------------------------------------------------
*/
--}}

@extends('layouts.layout1')
@section('title', trans('user.profile.users_profile'))

@section('content')
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
        <div class="d-flex gap-3">
            <button class="d-flex gap-4 align-items-center bg-transparent outline-none border-0 back-profile" data-href="{{ url('profile') }}">
                <svg width="20" height="20" viewBox="0 0 25 21" fill="none">
                    <path d="M11.5662 0.440759C11.706 0.580116 11.817 0.745702 11.8927 0.928029C11.9684 1.11036 12.0074 1.30584 12.0074 1.50326C12.0074 1.70068 11.9684 1.89616 11.8927 2.07848C11.817 2.26081 11.706 2.4264 11.5662 2.56576L5.12993 9.00201L23.5049 9.00201C23.9028 9.00201 24.2843 9.16004 24.5656 9.44135C24.8469 9.72265 25.0049 10.1042 25.0049 10.502C25.0049 10.8998 24.8469 11.2814 24.5656 11.5627C24.2843 11.844 23.9028 12.002 23.5049 12.002L5.12993 12.002L11.5662 18.4408C11.848 18.7226 12.0063 19.1047 12.0063 19.5033C12.0063 19.9018 11.848 20.284 11.5662 20.5658C11.2844 20.8475 10.9022 21.0059 10.5037 21.0059C10.1052 21.0059 9.72298 20.8475 9.44118 20.5658L0.441182 11.5658C0.301343 11.4264 0.190387 11.2608 0.114679 11.0785C0.0389711 10.8962 -4.50154e-07 10.7007 -4.58778e-07 10.5033C-4.67401e-07 10.3058 0.038971 10.1104 0.114679 9.92803C0.190387 9.7457 0.301342 9.58011 0.441182 9.44076L9.44118 0.440759C9.58054 0.30092 9.74613 0.189964 9.92845 0.114255C10.1108 0.0385471 10.3063 -0.000425789 10.5037 -0.000425798C10.7011 -0.000425806 10.8966 0.038547 11.0789 0.114255C11.2612 0.189964 11.4268 0.30092 11.5662 0.440759Z"fill="black" />
                </svg> 
            </button>
            <h3 class="h3-text mb-0">
                {{ trans('user.profile.update_profile') }}
            </h3>
        </div>
    </div>

    <main class="main-content" id="mainContent">

        <div class="container-fluid py-2">

            <ul class="nav nav-underline" id="edit-profile-tabs" role="tablist">
                <li class="nav-item border-0">
                    <a class="nav-link active" id="edit-profile-tab" data-bs-toggle="tab" href="#edit-profile"
                        role="tab" aria-controls="overview" aria-expanded="true">
                        <span class="h3-text fw-normal">{{ trans('user.profile.edit_profile') }}</span>
                    </a>
                </li>
                @can('DelegateRequest')
                <li class="nav-item border-0">
                    <a class="nav-link " id="delegation-tab" data-bs-toggle="tab" href="#delegation" role="tab"
                        aria-controls="link1">
                        <span class="h3-text fw-normal">{{ trans('user.profile.delegation') }}</span>
                    </a>
                </li>
                @endcan
                <li class="nav-item border-0">
                    <a class="nav-link" id="documents-tab" data-bs-toggle="tab" href="#documents" role="tab"
                        aria-controls="link2">
                        <span class="h3-text fw-normal">{{ trans('user.profile.documents') }}</span>
                    </a>
                </li>
            </ul>


            <div class="tab-content tabcontent-border p-3" id="myTabContent">
                <!-- overview tab 1 -->
                <div role="tabpanel" class="tab-pane fade show active" id="edit-profile" aria-labelledby="edit-profile-tab">
                    <div class="container-fluid py-3">
                        <!-- content -->
                        <div class="profile-upload-section mb-4">

                            <!-- profile and file upload -->
                            <div class="d-flex align-items-start">
                                <!-- Profile Image -->
                                {{-- <img class="profile-img me-3" src="{{ asset('assets/images/profile/user-2.png') }}" alt=""> --}}
                                <img class="profile-img me-3"  id="profilePreviewImage" src="{{ $user->getProfileImg() }}" alt="">
                                <!-- Name + Buttons -->
                                <div class="flex-grow-1">
                                    <!-- Name -->
                                    <h4 class="fw-semibold mb-2">{{ $user->getGuranteedNameText(true) }}</h4>
                                    <!-- Buttons -->
                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                        <input type="file" id="profileImageInput" accept="image/*" style="display:none;">
                                        <button class="amg-btn amg-btn-sm amg-btn-ghost" id="uploadProfileBtn">
                                            <svg viewBox="0 0 14 17" fill="none" >
                                                <path
                                                    d="M5 12.5924L9 12.5924C9.55 12.5924 10 12.1424 10 11.5924L10 6.59244L11.59 6.59244C12.48 6.59244 12.93 5.51244 12.3 4.88244L7.71 0.292444C7.61749 0.19974 7.5076 0.126193 7.38662 0.0760114C7.26565 0.02583 7.13597 4.15368e-10 7.005 4.07744e-10C6.87403 4.00121e-10 6.74435 0.02583 6.62338 0.0760114C6.5024 0.126193 6.39251 0.19974 6.3 0.292444L1.71 4.88244C1.08 5.51244 1.52 6.59244 2.41 6.59244L4 6.59244L4 11.5924C4 12.1424 4.45 12.5924 5 12.5924ZM1 14.5924L13 14.5924C13.55 14.5924 14 15.0424 14 15.5924C14 16.1424 13.55 16.5924 13 16.5924L1 16.5924C0.45 16.5924 -9.39615e-10 16.1424 -9.076e-10 15.5924C-8.75586e-10 15.0424 0.45 14.5924 1 14.5924Z"
                                                    fill="black" />
                                            </svg>
                                            <span class="b1-text">{{ trans('user.profile.upload_image') }}</span>
                                        </button>
                                        <button class="amg-btn amg-btn-sm amg-btn-subtle @if(!auth()->user()->avatar) d-none @endif" id="remove_image">
                                            <span class="b1-text" style="color:#00000060">{{ trans('user.profile.remove') }}</span>
                                        </button>
                                    </div>

                                    <!-- Helper Text -->
                                    <span class="text-muted b1-text">
                                        {{ trans('user.profile.supported_image_text') }}
                                    </span>

                                </div>

                            </div>

                            <!-- form -->

                            <div class="container-fluid mt-5 amg-modal">

                                <div class="row">
                                    <div class="col-xl-8 col-lg-9 col-md-10 col-12 ps-0">

                                        <form  id="profileForm" method="post" enctype="multipart/form-data">
                                            {{ csrf_field() }}
                                            <div class="row g-4">

                                                <!-- First Name -->
                                                <div class="col-md-6 amg-form-field-row">
                                                    <div class="">
                                                        <label class="form-label b1-text">{{ trans('user.profile.first_name') }} <span class="required"></span> </label>
                                                        <input type="text" class="form-control"
                                                            placeholder="{{ trans('user.profile.first_name') }}" id="first_name" name="first_name"
                                                            value="{{ count($errors) ? old('first_name') : $user->first_name }}" />
                                                    </div>

                                                </div>

                                                <!-- Last Name -->
                                                <div class="col-md-6 amg-form-field-row">
                                                    <div class="">
                                                        <label class="form-label b1-text">{{ trans('user.profile.last_name') }}<span class="required"></span> </label>
                                                        <input type="text" class="form-control" name="last_name" id="last_name"
                                                            placeholder="{{ trans('user.profile.last_name') }}"
                                                            value="{{ count($errors) ? old('last_name') : $user->last_name }}" />
                                                    </div>
                                                </div>

                                                <!-- Website -->
                                                <div class="col-12 amg-form-field-row">
                                                    <label class="form-label b1-text"
                                                        placeholder="Last Name">{{ trans('user.profile.website_url') }}</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text" id="basic-addon1">
                                                            <svg width="22" height="22" viewBox="0 0 22 22"
                                                                fill="none" >
                                                                <g clip-path="url(#clip0_868_29724)">
                                                                    <mask id="mask0_868_29724" style="mask-type:luminance"
                                                                        maskUnits="userSpaceOnUse" x="0" y="0"
                                                                        width="22" height="22">
                                                                        <path
                                                                            d="M2.56114e-09 0L22 1.28057e-09L22 22L1.28057e-09 22L2.56114e-09 0Z"
                                                                            fill="white" />
                                                                    </mask>
                                                                    <g mask="url(#mask0_868_29724)">
                                                                        <path
                                                                            d="M21.421 10.9998C21.421 16.7552 16.7552 21.4209 10.9999 21.4209C5.24459 21.4209 0.578857 16.7552 0.578857 10.9998C0.578857 5.24453 5.24459 0.578793 10.9999 0.578793C16.7552 0.578793 21.421 5.24453 21.421 10.9998Z"
                                                                            fill="#88C9F9" />
                                                                    </g>
                                                                    <mask id="mask1_868_29724" style="mask-type:luminance"
                                                                        maskUnits="userSpaceOnUse" x="0" y="0"
                                                                        width="22" height="22">
                                                                        <path
                                                                            d="M0.578857 10.9998C0.578857 16.7552 5.24459 21.4209 10.9999 21.4209C16.7552 21.4209 21.421 16.7552 21.421 10.9998C21.421 5.24453 16.7552 0.578793 10.9999 0.578793C5.24459 0.578793 0.578857 5.24453 0.578857 10.9998Z"
                                                                            fill="white" />
                                                                    </mask>
                                                                    <g mask="url(#mask1_868_29724)">
                                                                        <path
                                                                            d="M16.7535 1.48327C15.419 0.937319 14.7996 2.60469 13.714 2.27932C12.6285 1.95337 11.1811 1.23027 10.2403 2.13458C9.29956 3.03948 9.08245 3.87143 10.2403 3.83495C11.3982 3.79906 12.1943 2.42406 12.7732 3.003C13.3522 3.58195 13.2074 4.05206 11.8325 4.23327C10.4575 4.4139 8.97361 4.631 8.17756 4.631C7.38208 4.631 7.20087 5.10169 7.74335 5.60827C8.2864 6.11485 7.41798 6.15132 6.51366 6.6579C5.60877 7.16448 6.73077 7.38158 7.49035 7.81579C8.24992 8.25 8.82887 7.70695 9.15482 6.98327C9.48019 6.25958 10.8801 5.39116 11.4648 5.49943C12.0496 5.60827 12.0496 5.89774 11.9407 6.40432C11.8325 6.9109 12.3755 6.76616 12.4114 6.18721C12.4479 5.60827 13.0992 5.17406 13.6058 5.13758C14.1123 5.10169 14.546 5.82537 13.9676 6.18721C13.3887 6.54906 12.8097 6.83853 13.5334 7.01974C14.2571 7.20037 14.7631 8.14116 13.9311 8.53948C13.0992 8.93721 11.6512 9.19079 10.9999 8.86485C10.3486 8.53948 8.86535 8.06879 8.46703 8.32237C8.06929 8.57537 7.85219 8.83706 7.38208 9.01364C6.9114 9.19079 4.99392 10.0592 4.95745 11.1806C4.92156 12.3021 4.88508 13.5688 5.64524 13.6047C6.40482 13.6412 8.24992 13.1346 8.82887 12.6639C9.40782 12.1938 10.2039 12.3021 10.4575 12.8451C10.7105 13.3876 10.5298 13.7135 10.2762 14.582C10.0232 15.4504 10.4424 15.8846 10.8477 16.6442C11.2535 17.4043 11.5789 17.8385 11.5789 18.381C11.5789 18.9241 12.1584 19.4665 13.0268 18.5622C13.8952 17.6573 14.6907 15.8846 15.0526 15.2333C15.4144 14.582 15.7044 13.1346 16.1387 12.8451C16.5729 12.5556 17.2601 11.8678 16.7535 11.9767C16.2469 12.085 15.2338 11.9402 14.9802 11.2165C14.7272 10.4934 13.6417 9.11843 14.1482 8.97369C14.6548 8.82895 15.2338 9.77785 15.4509 10.226C15.668 10.6741 15.9574 11.2165 16.3917 11.3613C16.8259 11.506 17.7667 10.3933 18.0561 10.226C18.3456 10.0592 18.1285 9.37143 17.5496 9.44379C16.9706 9.51616 16.211 9.44379 16.211 9.08195C16.211 8.72011 16.9341 8.25 17.5131 8.35827C18.0926 8.46711 18.5268 8.64774 18.8887 9.08195C19.2499 9.51616 19.9736 10.3122 20.2631 10.8188C20.5526 11.3254 20.7697 11.3977 20.9144 10.4934C21.0591 9.58853 21.2762 9.29906 21.4216 8.61185C21.5663 7.92406 19.9377 2.7859 16.7535 1.48327Z"
                                                                            fill="#5C913B" />
                                                                    </g>
                                                                </g>
                                                                <defs>
                                                                    <clipPath id="clip0_868_29724">
                                                                        <rect width="22" height="22"
                                                                            fill="white" />
                                                                    </clipPath>
                                                                </defs>
                                                            </svg>
                                                        </span>
                                                        <input type="text" class="form-control" name="website" id="website"
                                                            placeholder="{{ trans('user.profile.website_url') }}" aria-label="Username"
                                                            aria-describedby="basic-addon1"
                                                            value="{{ count($errors) ? old('website') : $user->website }}" />
                                                    </div>

                                                </div>


                                                <!-- Facebook -->
                                                {{-- <div class="col-md-6">
                                                    <label class="form-label b1-text"
                                                        placeholder="Last Name">Facebook URL</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text" id="basic-addon1">

                                                            <svg width="20" height="20" viewBox="0 0 20 20"
                                                                fill="none" >
                                                                <g clip-path="url(#clip0_868_29740)">
                                                                    <path
                                                                        d="M15.8549 9.22877e-10L4.14508 2.41275e-10C1.85581 1.08023e-10 1.05613e-09 1.85581 9.22878e-10 4.14508L2.41275e-10 15.8549C1.08022e-10 18.1442 1.85581 20 4.14508 20L15.8549 20C18.1442 20 20 18.1442 20 15.8549L20 4.14508C20 1.85581 18.1442 1.05613e-09 15.8549 9.22877e-10Z"
                                                                        fill="#1778F2" />
                                                                    <path
                                                                        d="M10.7974 15.644L10.7974 10.2912L12.5942 10.2912L12.8637 8.20507L10.7974 8.20507L10.7974 6.87315C10.7974 6.26922 10.9652 5.85761 11.8313 5.85761L12.936 5.85761L12.936 3.99233C12.4013 3.93576 11.8639 3.90835 11.3263 3.91025C9.73351 3.91025 8.64315 4.88238 8.64315 6.66777L8.64315 8.20507L6.8418 8.20507L6.8418 10.2912L8.64315 10.2912L8.64315 15.644L10.7974 15.644Z"
                                                                        fill="#FDFDFD" />
                                                                </g>
                                                                <defs>
                                                                    <clipPath id="clip0_868_29740">
                                                                        <rect width="20" height="20"
                                                                            fill="white" />
                                                                    </clipPath>
                                                                </defs>
                                                            </svg>

                                                        </span>
                                                        <input type="text" class="form-control"
                                                            placeholder="Facebook URL" aria-label="Username"
                                                            aria-describedby="basic-addon1" />
                                                    </div>
                                                </div> --}}

                                                <!-- LinkedIn -->
                                                {{-- <div class="col-md-6">
                                                    <label class="form-label b1-text"
                                                        placeholder="Last Name">Linkedin URL</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text" id="basic-addon1">
                                                            <svg width="20" height="20" viewBox="0 0 20 20"
                                                                fill="none" >
                                                                <path
                                                                    d="M15.5109 9.02853e-10L4.48909 2.61299e-10C2.00983 1.16988e-10 1.04717e-09 2.00983 9.02854e-10 4.48909L2.61299e-10 15.5109C1.16987e-10 17.9902 2.00983 20 4.48909 20L15.5109 20C17.9902 20 20 17.9902 20 15.5109L20 4.48909C20 2.00983 17.9902 1.04716e-09 15.5109 9.02853e-10Z"
                                                                    fill="#0B86CA" />
                                                                <path
                                                                    d="M5.54198 8.31265L7.62027 8.31265L7.62027 15.0186L5.54198 15.0186L5.54198 8.31265ZM6.60191 4.98047C6.84002 4.98047 7.07279 5.051 7.27087 5.18314C7.46894 5.31529 7.62344 5.50314 7.71488 5.723C7.80631 5.94285 7.83058 6.18486 7.78463 6.4185C7.73868 6.65213 7.62456 6.86692 7.45668 7.03578C7.28879 7.20463 7.07466 7.31997 6.84129 7.36726C6.60792 7.41455 6.36578 7.39167 6.1454 7.3015C5.92502 7.21132 5.73629 7.0579 5.60301 6.86059C5.46973 6.66327 5.39787 6.43091 5.3965 6.1928C5.39559 6.03392 5.4261 5.87643 5.48626 5.72938C5.54643 5.58234 5.63508 5.44864 5.7471 5.33597C5.85912 5.2233 5.99231 5.13389 6.13901 5.07288C6.28571 5.01187 6.44303 4.98047 6.60191 4.98047ZM8.95037 8.31265L10.9524 8.31265L10.9524 9.22709C11.1529 8.88507 11.4424 8.60389 11.7901 8.41356C12.1378 8.22322 12.5307 8.13088 12.9268 8.14638C15.0051 8.14638 15.4277 9.53191 15.4277 11.34L15.4277 15.0186L13.3494 15.0186L13.3494 11.7765C13.3494 11.0006 13.3494 9.99606 12.2687 9.99606C11.188 9.99606 11.0217 10.8412 11.0217 11.7141L11.0217 15.0324L8.94344 15.0324L8.95037 8.31265Z"
                                                                    fill="white" />
                                                            </svg>

                                                        </span>
                                                        <input type="text" class="form-control"
                                                            placeholder="LinkedIn URL" aria-label="Username"
                                                            aria-describedby="basic-addon1" />
                                                    </div>
                                                </div> --}}

                                                <!-- Buttons -->
                                                <div class="amg-btn-group" style="margin-top: 30px;">
                                                    <button class="amg-btn amg-btn-primary px-5 radius-1" id="btnSubmit">
                                                        <span class="h4-text text-white">{{ trans('user.profile.save') }}</span>
                                                    </button>
                                                    <button class="amg-btn amg-btn-ghost s2-text bg-black text-white px-5 radius-1" id="btnClear">
                                                        <span class="h4-text">{{ trans('user.profile.cancel') }}</span>
                                                    </button>
                                                </div>
                                            </div>

                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @can('DelegateRequest')
                <div role="tabpanel" class="tab-pane fade" id="delegation" aria-labelledby="delegation-tab">
                        <div class="col-md-6 amg-form-modal">
                            <form id="delegationForm" action="{{ url('profile/edit') }}" method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                
                                <div class="row align-items-center mb-4 gx-3"> 
                                    <div class="col-sm-5 col-md-4 text-sm-end">
                                        <label for="request_approval_delegated_user" class="form-label b1-text mb-sm-0 text-nowrap">
                                            {{ trans('user.user_form.service_request_delegation') }}
                                        </label>
                                    </div>
                                    <div class="col-sm-7 col-md-8">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M18.0407 16.5627C16.8508 14.5056 15.0172 13.0306 12.8774 12.3314C13.9358 11.7013 14.7582 10.7412 15.2182 9.59845C15.6781 8.45573 15.7503 7.19361 15.4235 6.00592C15.0968 4.81823 14.3892 3.77064 13.4094 3.02402C12.4296 2.2774 11.2318 1.87305 10 1.87305C8.76821 1.87305 7.57044 2.2774 6.59067 3.02402C5.6109 3.77064 4.90331 4.81823 4.57654 6.00592C4.24978 7.19361 4.32193 8.45573 4.78189 9.59845C5.24186 10.7412 6.06422 11.7013 7.12268 12.3314C4.98284 13.0299 3.14925 14.5049 1.9594 16.5627C1.91577 16.6338 1.88683 16.713 1.87429 16.7955C1.86174 16.878 1.86585 16.9622 1.88638 17.0431C1.9069 17.124 1.94341 17.2 1.99377 17.2665C2.04413 17.3331 2.10731 17.3889 2.17958 17.4306C2.25185 17.4724 2.33175 17.4992 2.41457 17.5096C2.49738 17.5199 2.58143 17.5136 2.66176 17.491C2.74209 17.4683 2.81708 17.4298 2.88228 17.3777C2.94749 17.3256 3.00161 17.261 3.04143 17.1877C4.51331 14.6439 7.11487 13.1252 10 13.1252C12.8852 13.1252 15.4867 14.6439 16.9586 17.1877C16.9984 17.261 17.0526 17.3256 17.1178 17.3777C17.183 17.4298 17.258 17.4683 17.3383 17.491C17.4186 17.5136 17.5027 17.5199 17.5855 17.5096C17.6683 17.4992 17.7482 17.4724 17.8205 17.4306C17.8927 17.3889 17.9559 17.3331 18.0063 17.2665C18.0566 17.2 18.0932 17.124 18.1137 17.0431C18.1342 16.9622 18.1383 16.878 18.1258 16.7955C18.1132 16.713 18.0843 16.6338 18.0407 16.5627ZM5.62503 7.50017C5.62503 6.63488 5.88162 5.78902 6.36235 5.06955C6.84308 4.35009 7.52636 3.78933 8.32579 3.4582C9.12521 3.12707 10.0049 3.04043 10.8535 3.20924C11.7022 3.37805 12.4818 3.79473 13.0936 4.40658C13.7055 5.01843 14.1222 5.79799 14.291 6.64665C14.4598 7.49532 14.3731 8.37499 14.042 9.17441C13.7109 9.97384 13.1501 10.6571 12.4306 11.1379C11.7112 11.6186 10.8653 11.8752 10 11.8752C8.84009 11.8739 7.72801 11.4126 6.90781 10.5924C6.0876 9.77219 5.62627 8.66011 5.62503 7.50017Z" fill="currentColor"></path></svg>
                                            </span>
                                            <select name="request_approval_delegated_user" id="request_approval_delegated_user" class="form-select delegation"></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row align-items-center mb-4 gx-3 d-none">
                                    <div class="col-sm-5 col-md-4 text-sm-end">
                                        <label for="ticket_handler_delegated_user" class="form-label b1-text mb-sm-0 text-nowrap">
                                            {{ trans('user.profile.service_ticket_delegation') }}
                                        </label>
                                    </div>
                                    <div class="col-sm-7 col-md-8">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M18.0407 16.5627C16.8508 14.5056 15.0172 13.0306 12.8774 12.3314C13.9358 11.7013 14.7582 10.7412 15.2182 9.59845C15.6781 8.45573 15.7503 7.19361 15.4235 6.00592C15.0968 4.81823 14.3892 3.77064 13.4094 3.02402C12.4296 2.2774 11.2318 1.87305 10 1.87305C8.76821 1.87305 7.57044 2.2774 6.59067 3.02402C5.6109 3.77064 4.90331 4.81823 4.57654 6.00592C4.24978 7.19361 4.32193 8.45573 4.78189 9.59845C5.24186 10.7412 6.06422 11.7013 7.12268 12.3314C4.98284 13.0299 3.14925 14.5049 1.9594 16.5627C1.91577 16.6338 1.88683 16.713 1.87429 16.7955C1.86174 16.878 1.86585 16.9622 1.88638 17.0431C1.9069 17.124 1.94341 17.2 1.99377 17.2665C2.04413 17.3331 2.10731 17.3889 2.17958 17.4306C2.25185 17.4724 2.33175 17.4992 2.41457 17.5096C2.49738 17.5199 2.58143 17.5136 2.66176 17.491C2.74209 17.4683 2.81708 17.4298 2.88228 17.3777C2.94749 17.3256 3.00161 17.261 3.04143 17.1877C4.51331 14.6439 7.11487 13.1252 10 13.1252C12.8852 13.1252 15.4867 14.6439 16.9586 17.1877C16.9984 17.261 17.0526 17.3256 17.1178 17.3777C17.183 17.4298 17.258 17.4683 17.3383 17.491C17.4186 17.5136 17.5027 17.5199 17.5855 17.5096C17.6683 17.4992 17.7482 17.4724 17.8205 17.4306C17.8927 17.3889 17.9559 17.3331 18.0063 17.2665C18.0566 17.2 18.0932 17.124 18.1137 17.0431C18.1342 16.9622 18.1383 16.878 18.1258 16.7955C18.1132 16.713 18.0843 16.6338 18.0407 16.5627ZM5.62503 7.50017C5.62503 6.63488 5.88162 5.78902 6.36235 5.06955C6.84308 4.35009 7.52636 3.78933 8.32579 3.4582C9.12521 3.12707 10.0049 3.04043 10.8535 3.20924C11.7022 3.37805 12.4818 3.79473 13.0936 4.40658C13.7055 5.01843 14.1222 5.79799 14.291 6.64665C14.4598 7.49532 14.3731 8.37499 14.042 9.17441C13.7109 9.97384 13.1501 10.6571 12.4306 11.1379C11.7112 11.6186 10.8653 11.8752 10 11.8752C8.84009 11.8739 7.72801 11.4126 6.90781 10.5924C6.0876 9.77219 5.62627 8.66011 5.62503 7.50017Z" fill="currentColor"></path></svg>
                                            </span>
                                            <select name="ticket_handler_delegated_user" id="ticket_handler_delegated_user" class="form-select delegation"></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row align-items-center mb-4 gx-3 d-none">
                                    <div class="col-sm-5 col-md-4 text-sm-end">
                                        <label for="Change_approver_delegated_user" class="form-label b1-text mb-sm-0 text-nowrap">
                                            {{ trans('user.profile.change_delegation') }}
                                        </label>
                                    </div>
                                    <div class="col-sm-7 col-md-8">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M18.0407 16.5627C16.8508 14.5056 15.0172 13.0306 12.8774 12.3314C13.9358 11.7013 14.7582 10.7412 15.2182 9.59845C15.6781 8.45573 15.7503 7.19361 15.4235 6.00592C15.0968 4.81823 14.3892 3.77064 13.4094 3.02402C12.4296 2.2774 11.2318 1.87305 10 1.87305C8.76821 1.87305 7.57044 2.2774 6.59067 3.02402C5.6109 3.77064 4.90331 4.81823 4.57654 6.00592C4.24978 7.19361 4.32193 8.45573 4.78189 9.59845C5.24186 10.7412 6.06422 11.7013 7.12268 12.3314C4.98284 13.0299 3.14925 14.5049 1.9594 16.5627C1.91577 16.6338 1.88683 16.713 1.87429 16.7955C1.86174 16.878 1.86585 16.9622 1.88638 17.0431C1.9069 17.124 1.94341 17.2 1.99377 17.2665C2.04413 17.3331 2.10731 17.3889 2.17958 17.4306C2.25185 17.4724 2.33175 17.4992 2.41457 17.5096C2.49738 17.5199 2.58143 17.5136 2.66176 17.491C2.74209 17.4683 2.81708 17.4298 2.88228 17.3777C2.94749 17.3256 3.00161 17.261 3.04143 17.1877C4.51331 14.6439 7.11487 13.1252 10 13.1252C12.8852 13.1252 15.4867 14.6439 16.9586 17.1877C16.9984 17.261 17.0526 17.3256 17.1178 17.3777C17.183 17.4298 17.258 17.4683 17.3383 17.491C17.4186 17.5136 17.5027 17.5199 17.5855 17.5096C17.6683 17.4992 17.7482 17.4724 17.8205 17.4306C17.8927 17.3889 17.9559 17.3331 18.0063 17.2665C18.0566 17.2 18.0932 17.124 18.1137 17.0431C18.1342 16.9622 18.1383 16.878 18.1258 16.7955C18.1132 16.713 18.0843 16.6338 18.0407 16.5627ZM5.62503 7.50017C5.62503 6.63488 5.88162 5.78902 6.36235 5.06955C6.84308 4.35009 7.52636 3.78933 8.32579 3.4582C9.12521 3.12707 10.0049 3.04043 10.8535 3.20924C11.7022 3.37805 12.4818 3.79473 13.0936 4.40658C13.7055 5.01843 14.1222 5.79799 14.291 6.64665C14.4598 7.49532 14.3731 8.37499 14.042 9.17441C13.7109 9.97384 13.1501 10.6571 12.4306 11.1379C11.7112 11.6186 10.8653 11.8752 10 11.8752C8.84009 11.8739 7.72801 11.4126 6.90781 10.5924C6.0876 9.77219 5.62627 8.66011 5.62503 7.50017Z" fill="currentColor"></path></svg>
                                            </span>
                                            <select name="change_approval_delegated_user" id="change_approval_delegated_user" class="form-select delegation"></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row align-items-center mb-4 gx-3 d-none">
                                    <div class="col-sm-5 col-md-4 text-sm-end">
                                        <label for="procurement_approver_delegated_user" class="form-label b1-text mb-sm-0 text-nowrap">
                                            {{ trans('user.profile.procurement_delegation') }}
                                        </label>
                                    </div>
                                    <div class="col-sm-7 col-md-8">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M18.0407 16.5627C16.8508 14.5056 15.0172 13.0306 12.8774 12.3314C13.9358 11.7013 14.7582 10.7412 15.2182 9.59845C15.6781 8.45573 15.7503 7.19361 15.4235 6.00592C15.0968 4.81823 14.3892 3.77064 13.4094 3.02402C12.4296 2.2774 11.2318 1.87305 10 1.87305C8.76821 1.87305 7.57044 2.2774 6.59067 3.02402C5.6109 3.77064 4.90331 4.81823 4.57654 6.00592C4.24978 7.19361 4.32193 8.45573 4.78189 9.59845C5.24186 10.7412 6.06422 11.7013 7.12268 12.3314C4.98284 13.0299 3.14925 14.5049 1.9594 16.5627C1.91577 16.6338 1.88683 16.713 1.87429 16.7955C1.86174 16.878 1.86585 16.9622 1.88638 17.0431C1.9069 17.124 1.94341 17.2 1.99377 17.2665C2.04413 17.3331 2.10731 17.3889 2.17958 17.4306C2.25185 17.4724 2.33175 17.4992 2.41457 17.5096C2.49738 17.5199 2.58143 17.5136 2.66176 17.491C2.74209 17.4683 2.81708 17.4298 2.88228 17.3777C2.94749 17.3256 3.00161 17.261 3.04143 17.1877C4.51331 14.6439 7.11487 13.1252 10 13.1252C12.8852 13.1252 15.4867 14.6439 16.9586 17.1877C16.9984 17.261 17.0526 17.3256 17.1178 17.3777C17.183 17.4298 17.258 17.4683 17.3383 17.491C17.4186 17.5136 17.5027 17.5199 17.5855 17.5096C17.6683 17.4992 17.7482 17.4724 17.8205 17.4306C17.8927 17.3889 17.9559 17.3331 18.0063 17.2665C18.0566 17.2 18.0932 17.124 18.1137 17.0431C18.1342 16.9622 18.1383 16.878 18.1258 16.7955C18.1132 16.713 18.0843 16.6338 18.0407 16.5627ZM5.62503 7.50017C5.62503 6.63488 5.88162 5.78902 6.36235 5.06955C6.84308 4.35009 7.52636 3.78933 8.32579 3.4582C9.12521 3.12707 10.0049 3.04043 10.8535 3.20924C11.7022 3.37805 12.4818 3.79473 13.0936 4.40658C13.7055 5.01843 14.1222 5.79799 14.291 6.64665C14.4598 7.49532 14.3731 8.37499 14.042 9.17441C13.7109 9.97384 13.1501 10.6571 12.4306 11.1379C11.7112 11.6186 10.8653 11.8752 10 11.8752C8.84009 11.8739 7.72801 11.4126 6.90781 10.5924C6.0876 9.77219 5.62627 8.66011 5.62503 7.50017Z" fill="currentColor"></path></svg>
                                            </span>
                                            <select name="procurement_approval_delegated_user" id="procurement_approval_delegated_user" class="form-select delegation"></select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Buttons -->
                                <div class="amg-btn-group" style="margin-top: 30px;">
                                    <button type="button" class="amg-btn amg-btn-ghost s2-text bg-black text-white px-5 radius-1">
                                        <a href="{{ url('profile') }}" class="h4-text text-white text-decoration-none">{{ trans('user.profile.cancel') }}</a>
                                    </button>
                                    <button type="submit" class="amg-btn amg-btn-primary px-5 radius-1" id="btnSubmitDelegation">
                                        <span class="h4-text text-white">{{ trans('user.profile.save') }}</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                </div>
                @endcan
                <div role="tabpanel" class="tab-pane fade" id="documents" aria-labelledby="documents-tab">
                    <!-- content -->
                    {{-- {{ trans('user.profile.documents') }} --}}
                    <div class="card" id="main-user-list-wrapper">
                        <div class="card-body table-responsive">
                            <div class="d-flex align-items-center gap-2 mb-1" data-select2-id="select2-data-5-1om7">
                                <div id="customLengthContainer" class="col-auto" data-select2-id="select2-data-4-sbib">
                                    <select id="showSelect" class="showSelect amg-table-pagination-dropdown userModulePageLenth user-list-page-length">
                                        <option value="10" selected>{{ trans('user.profile.show') }} (10)</option>
                                        <option value="25">{{ trans('user.profile.show') }} (25)</option>
                                        <option value="50">{{ trans('user.profile.show') }} (50)</option>
                                        <option value="100">{{ trans('user.profile.show') }} (100)</option>
                                    </select>
                                </div>
                                <div class="flex-grow-1"></div>
                                <div>
                                    <div class="amg-list-searchbar">
                                        <svg class="amg-list-searchbar__icon-documents" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                        </svg>
                                        <input type="text" class="amg-list-searchbar__input" placeholder="{{ trans('user.profile.search') }}" id="searchbox">
                                    </div>
                                </div>
                                <button class="amg-refresh-btn btn-reload-list" id="btn_reload" data-table="documents">
                                    <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="currentColor"></path>
                                    </svg>
                                    <span>{{ trans('user.profile.refresh') }}</span>
                                </button>
                                @can('UserDocumentsUpload')
                                    <button class="header-icon-btn-only header-icon-btn-only-sm" data-bs-toggle="tooltip" title="{{ trans('user.profile.upload') }}" id="btn_upload_document">
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6 20L18 20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                            <path d="M12 16V4M12 4L15.5 7.5M12 4L8.5 7.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                    </button>
                                @endcan
                            </div>
                            <table id="tblDocument" class="table w-100">
                                <thead>
                                    <tr>
                                        <th>
                                            <h4>{{ trans('user.profile.id') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('user.profile.document_name') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('user.profile.uploaded_on') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('user.profile.notes') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('user.profile.actions') }}</h4>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('consumables.note-modal')
        @include("documents.upload_modal")
    </main>
@endsection
@push('scripts')
    <script src="{!! CommonHelper::asset('js/common.js') !!}"></script>
    <script src="{!! CommonHelper::asset('js/profile/index.js') !!}"></script>
    <script src="{!! CommonHelper::asset('newjs/select2.full.min.js') !!}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $(".back-profile").on("click", function(e) {
                e.preventDefault();
                window.location.href = $(this).attr("data-href");
            });
            var config = new Object;
            config.url = new Object;
            config.url.getUser = "{{ url('getUserByQuery') }}";
            config.url.profileImage = "{{url('profile/avatar-upload')}}"
            config.url.document_delete = "{{ url('document/delete') }}";
            config.url.documents = "{{ url('document/jx-documents') }}";
            config.url.attachment_view = "{{ url('document-attachment/view') }}";
            config.url.document_download = "{{ url('uploads/documents') }}";
            config.url.document_upload = "{{ url('document/jx-upload') }}";
            config.url.getUsersParameterBase = "{{ url('getUsersParameterBase') }}";
            config.url.deleteImage = "{{ url('profile/remove-avatar') }}";
            config.token = "{{ csrf_token() }}";
            config.user_id = {!! json_encode(Auth::user()->only('id')) !!};
            config.reqDelegation = {!! json_encode($reqDelegation) !!};
            config.ticketDelegation = {!! json_encode($ticketDelegation) !!};
            config.changeDelegation = {!! json_encode($changeDelegation) !!};
            config.procureDelegation = {!! json_encode($procureDelegation) !!};
            config.translations = {
                Delete_Document: '{{ trans('user.profile.Delete_Document') }}',
                Download_Document: '{{ trans('user.profile.Download_Document') }}',
                Reload: '{{ trans('user.profile.Reload') }}',
                Upload_Document: '{{ trans('user.profile.Upload_Document') }}',
                something_went_wrong: '{{ trans('user.profile.something_went_wrong') }}',
                delete_document: '{{ trans('user.profile.delete_document') }}',
                Search: '{{ trans('user.profile.Search') }}',
                press_enter_with_Search: '{{ trans('user.profile.press_enter_with_Search') }}',
                New_Document_Upload: '{{ trans('user.profile.New_Document_Upload') }}',
                are_you_delete: '{{ trans('user.profile.are_you_delete') }}',
                modal_yes_button: '{{ trans('user.profile.modal_yes_button') }}',
                modal_cancel_button: '{{ trans('user.profile.modal_cancel_button') }}',
                deleted_successfully: '{{ trans('user.profile.deleted_successfully') }}',
                deleted_fail: '{{ trans('user.profile.deleted_fail') }}',
                invalid_file: '{{ trans('user.profile.invalid_file') }}',
                allowed_avatar_formats: '{{ trans('user.profile.allowed_avatar_formats') }}',
                file_too_large: '{{ trans('user.profile.file_too_large') }}',
                avatar_max_size: '{{ trans('user.profile.avatar_max_size') }}',
                deleting: '{{ trans('user.profile.deleting') }}',
                deleted: '{{ trans('user.profile.deleted') }}',
                error: '{{ trans('user.profile.error') }}',
                upload_failed_default: '{{ trans('user.profile.upload_failed_default') }}',
                select_user_for_delegation: '{{ trans('user.profile.select_user_for_delegation') }}',
                no_data_available: '{{ trans('user.profile.no_data_available') }}',
                view_document: '{{ trans('user.profile.view_document') }}'
            }
            new ProfileAdd(config);
        });
    </script>
@endpush

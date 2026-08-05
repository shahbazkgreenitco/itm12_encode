@extends('layouts.auth')
@section('title', 'Login - AMG')

@section('style')
    <style>
        .passwordDiv,
        #btnSubmit,
        #btnSubmitOtp,
        .emailDiv,
        .validateUserMsg {
            display: none;
        }
        #username-error,
        #password-error,
        .validateUserMsg {
            color: #cc0000;
        }
        body {
            padding: unset;
        }
    </style>
@endsection

@section('content')
    <div id="main-wrapper">
        <div class="position-relative overflow-hidden radial-gradient min-vh-100 w-100">
            <div class="position-relative z-index-5">
                <div class="row">

                    <!-- Left Side -->
                    <div class="col-lg-6 col-xl-7 col-xxl-6 position-relative overflow-hidden bg-dark d-none d-lg-block">
                        <div class="circle-top"></div>
                        <div>
                            <img src="{{ asset('assets/images/logos/logo-icon.svg') }}" class="circle-bottom"
                                alt="Logo-Dark" />
                        </div>

                        <div class="masalert alert alert-success" style="display: none;"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><span>You have logged out successfully</span></div>

                        <div class="d-lg-flex align-items-center z-index-5 position-relative h-n80">
                            <div class="row justify-content-center w-100">
                                <div class="col-lg-7">
                                    <h2 class="text-white fs-10 mb-3">
                                        {{ trans('login.login.welcome') }} <br> GREENITCO ITM
                                    </h2>
                                    <span class="opacity-75 fs-4 text-white d-block mb-3">
                                        {{ trans('login.login.tagline') }}
                                    </span>
                                    <a href="https://greenitco.com/products/best-it-asset-management-software"
                                        class="btn btn-primary" target="_blank">
                                        {{ trans('login.login.learn-more') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side -->
                    <div class="col-lg-6 col-xl-5 col-xxl-6">
                        <div
                            class="min-vh-100 bg-body row justify-content-center justify-content-lg-center align-items-center p-5">
                            <div class="col-sm-8 col-md-9 col-xxl-6 auth-card cls-content text-center">

                                @if(session()->has('msg'))
                                    @php $type = session('msg')['type'] ?? 'danger'; @endphp

                                    <div class="alert alert-{{ $type }} text-center mb-3">
                                        {{ session('msg')['msg'] }}
                                    </div>
                                @endif


                                <a href="javascript:void(0)" class="text-nowrap logo-img d-block w-100 mb-10">
                                    <img src="{{ asset('main_asset/assets/images/AMG logo.png') }}" class="dark-logo"
                                        alt="Logo-Dark" />
                                </a>


                                @if (config('services.azure.client_id') != '')
                                    <h2 class="mb-2 mt-4 fs-7 fw-bolder text-center">
                                        {{ trans('login.login.sign-in') }}
                                    </h2>
                                    <div class="row">
                                        <div class="col-12 mb-2 mb-sm-0">
                                            <a class="btn btn-link border border-muted d-flex align-items-center justify-content-center rounded-2 py-8 text-decoration-none"
                                                href="javascript:void(0)" role="button">
                                                <svg  width="32" height="32"
                                                    viewBox="0 0 256 256" aria-label="Microsoft logo">
                                                    <rect width="256" height="256" fill="none" />
                                                    <rect x="16" y="16" width="112" height="112" fill="#F25022" />
                                                    <rect x="128" y="16" width="112" height="112" fill="#7FBA00" />
                                                    <rect x="16" y="128" width="112" height="112" fill="#00A4EF" />
                                                    <rect x="128" y="128" width="112" height="112" fill="#FFB900" />
                                                </svg>
                                                &nbsp;&nbsp;{{ trans('login.login.sign-with-microsoft') }}
                                            </a>
                                        </div>
                                    </div>

                                    <div class="position-relative text-center my-4">
                                        <p
                                            class="mb-0 fs-4 px-3 d-inline-block bg-body text-dark z-index-5 position-relative">
                                            {{ trans('login.login.or-sign-width') }}
                                        </p>
                                        <span class="border-top w-100 position-absolute top-50 start-50 translate-middle"></span>
                                    </div>
                                @endif

                                <form id="loginFrm" action="{{ url('login') }}" method="post">
                                    {{ csrf_field() }}

                                    <!-- Username -->
                                    <div class="mb-3 form-group has-feedback">
                                        <label for="username" class="form-label">
                                            {{ trans('login.login.username') }}
                                        </label>
                                        <input type="text" class="form-control text" id="username" name="username"
                                            placeholder="{{ trans('login.login.username-placeholder') }}"
                                            autocomplete="off" required />
                                    </div>

                                    <span class="validateUserMsg">
                                        {{ trans('login.login.enter_valid_username') }}
                                    </span>

                                    <!-- Password -->
                                    <div class="mb-4 form-group has-feedback passwordDiv">
                                        <label for="password" class="form-label">
                                            {{ trans('login.login.password') }}
                                        </label>
                                        <input type="password" class="form-control text" id="password" name="password"
                                            placeholder="{{ trans('login.login.password-placeholder') }}"
                                            autocomplete="off" />
                                    </div>

                                    <!-- Impersonation Email -->
                                    @if (config('app.impersonate') === 'yes')
                                        <div class="mb-4 form-group has-feedback emailDiv">
                                            <label for="email" class="form-label">
                                                {{ trans('login.login.email') }}
                                            </label>
                                            <input type="email" class="form-control text" id="email" name="email"
                                                placeholder="{{ trans('login.login.email-placeholder') }}"
                                                autocomplete="off" />
                                        </div>
                                    @endif

                                    <div
                                        class="d-sm-flex align-items-center justify-content-between mb-4 form-group remember pt-2">
                                        <div class="form-check">
                                            <input class="form-check-input primary" name="remember_me" type="checkbox"
                                                value="1" id="remember_me" />
                                            <label class="form-check-label text-dark" for="remember_me">
                                                {{ trans('login.login.remember') }}
                                            </label>
                                        </div>
                                        <a class="text-primary fw-medium" href="{{ url('/forgot-password') }}">
                                            {{ trans('login.login.forgot-password') }}
                                        </a>
                                    </div>

                                    <!-- Continue Button -->
                                    <button class="btn btn-primary w-100 py-8 mb-4 rounded-2 btnContinue" type="button">
                                        <span>{{ trans('login.login.continue') }}</span>
                                    </button>

                                    <!-- Sign In -->
                                    <button id="btnSubmit" class="btn btn-primary w-100 py-8 mb-4 rounded-2" name="action_type" value="sign_in" type="submit">
                                        <span>{{ trans('login.login.sign-in') }}</span>
                                    </button>

                                    <!-- OTP Button -->
                                    @if (config('app.impersonate') === 'yes')
                                        <button class="btn btn-primary w-100 py-8 mb-4 rounded-2" name="action_type"
                                            value="otp_login" type="submit">
                                            {{ trans('login.login.send-otp') }}
                                        </button>
                                    @endif

                                    <div class="d-flex align-items-center justify-content-center mt-2">
                                        <p>
                                            <a href="javascript:void(0)"
                                                class="link-underline-light text-decoration-underline">
                                                {{ trans('login.login.terms&condition') }}
                                            </a> |
                                            <a href="javascript:void(0)"
                                                class="link-underline-light text-decoration-underline">
                                                {{ trans('login.login.contact-us') }}
                                            </a>
                                        </p>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


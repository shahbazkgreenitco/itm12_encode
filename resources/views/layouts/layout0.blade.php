<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Confirmation Page')</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ url('favicon.ico') }}">

    {{-- Common Head Includes --}}
    @include('auth.auth-head')

    {{-- Custom Head Stack --}}
    @stack('head')

    {{-- Additional CSS --}}
    <style>
        /* Layout 0 specific styles */
        .layout0-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            padding: 20px;
        }

        .layout0-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }

        .layout0-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 30px;
        }

        /* Dark mode support */
        html[data-bs-theme="dark"] .layout0-wrapper {
            background-color: #1a1a2e;
        }

        html[data-bs-theme="dark"] .layout0-card {
            background: #2d2d44;
            color: #e2e8f0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .layout0-card {
                padding: 20px;
                border-radius: 8px;
            }
        }

        @media (max-width: 576px) {
            .layout0-wrapper {
                padding: 10px;
            }

            .layout0-card {
                padding: 15px;
                border-radius: 6px;
            }
        }
    </style>
</head>

<body>
    {{-- Preloader --}}
    <div class="preloader">
        <img src="{{ url('favicon.ico') }}" class="lds-ripple img-fluid" alt="Loading" />
    </div>

    {{-- Main Content --}}
    <div class="layout0-wrapper">
        <div class="layout0-container">
            <div class="layout0-card">
                @yield('content')
            </div>
        </div>
    </div>

    {{-- Footer Scripts --}}
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/dist/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/theme/app.init.js') }}"></script>
    <script src="{{ asset('assets/js/theme/theme.js') }}"></script>
    <script src="{{ asset('assets/js/theme/app.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

    <script src="{{ asset('js/common.js') }}"></script>
    <script src="{{ asset('js/support_validate.js') }}"></script>
    <script src="{{ asset('js/profile/index.js') }}"></script>

    {{-- Custom Script Stack --}}
    @stack('scripts')

    {{-- Vite Assets --}}
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

    {{-- Additional Scripts --}}
    @stack('lib')
</body>

</html>
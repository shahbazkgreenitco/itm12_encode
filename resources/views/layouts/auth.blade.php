<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">
@include('auth.auth-head')
<body>
    <div class="preloader">
        <img src="{{ url('favicon.ico') }}" class="lds-ripple img-fluid" />
    </div>
    @yield('content')

   @include('auth.auth-scripts')
   {{-- @vite(['resources/css/app.css','resources/js/app.js']) --}}
   @stack('script')
</body>
</html>

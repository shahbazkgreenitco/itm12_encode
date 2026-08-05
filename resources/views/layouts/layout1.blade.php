<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
@include('common.head')
<style>
    :root {
        --sort-icon: url("{{ config('app.url') }}/imgs/icons/dataTable/sort.svg");
        --sort-asc-icon: url("{{ config('app.url') }}/imgs/icons/dataTable/sort-asc.svg");
        --sort-desc-icon: url("{{ config('app.url') }}/imgs/icons/dataTable/sort-desc.svg");
        --close-circle: url("{{ config('app.url') }}/imgs/icons/close-circle.svg");
    }
</style>
<body class="@yield('body-class') remove-validation-errors">
    <div id="body_wrapper" class="body_wrapper">
        @include('common.navbar')
        @include('common.announcement')
        @include('common.sidebar')
        @yield('content')
        @include('common.footer')
        @include('common.scripts')
        @stack('scripts')
    </div>
</body>

</html>
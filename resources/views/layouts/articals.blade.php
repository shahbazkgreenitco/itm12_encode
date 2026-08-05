<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
@include('common.head')
<body class="@yield('body-class')">
    <div id="body_wrapper" class="body_wrapper">
        @yield('content')
        @include('common.footer')
    </div>   
    {{-- @include('common.scripts') --}}
    <!-- jQuery -->
 {{-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> --}}
 <script src="{{ asset('newjs/jquery-3.7.1.min.js') }}"></script>
 <script src="{{ asset('newjs/moment.min.js') }}"></script>
 <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
 <script src="{{ asset('assets/libs/datatables.net/js/dataTables.fixedColumns.min.js') }}"></script>
 <!-- <script src="@@webRoot/node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script> -->
 {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script> --}}
 <!-- <script src="@@webRoot/node_modules/apexcharts/dist/apexcharts.min.js"></script> -->
 <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
 <script src="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}"></script>
 <script src="{{ asset('assets/libs/select2/dist/js/select2.full.min.js') }}"></script>
 <script src="{{ asset('assets/libs/select2/dist/js/select2.min.js') }}"></script>
 <script src="{{ asset('assets/scripts/forms/select2.init.js') }}"></script>
 <script src="{{ asset('assets/libs/sweetalert2/dist/sweetalert2.min.js') }}"></script>
 <script src="{{ asset('assets/scripts/forms/sweet-alert.init.js') }}"></script>
 <script src="{{ asset('assets/scripts/plugins/toastr-init.js') }}"></script>
 <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
 <script src="{{ asset('js/support_validate.js') }}"></script>
 <script src="{{ asset('newjs/moment.min.js') }}"></script>
 <script src="{{ asset('js/sweetAlert.js') }}"></script>
 {{-- <script src="../../libs/datatables.net/js/jquery.dataTables.min.js"></script> --}}
 <script src="{{ asset('assets/js/datatable/datatable-advanced.init.js') }}"></script>
 <script src="{{ asset('main_asset/scripts/index.js') }}" defer></script>
 <script src="{{ asset('main_asset/scripts/theme-toggle.js') }}"></script>
 <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
 <script src="{{ asset('assets/libs/jquery-validation/dist/jquery.validate.min.js') }}"></script>
 <script src="{{ asset('assets/libs/jquery-validation/dist/additional-methods.min.js') }}"></script>
 <script src="{{ asset('assets/libs/jquery.filedrop.js') }}"></script>
 <script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.min.js') !!}"></script>
    @stack('scripts')
</body>

</html>

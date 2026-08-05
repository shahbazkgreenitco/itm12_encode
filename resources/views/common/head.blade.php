{{-- @page-meta
{
  "page_no": "LYHDB-02",
  "file": "head.blade.php",
  "versions": [
    {
      "version": "1.2",
      "writer": "Muzaffar Shaikh",
      "from": "2026-04",
      "reviewer": null,
      "description": "updated CSS and CDN"
    }
  ]
}
--}}
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
    <title>@yield('title') - ITM</title>
    <link rel="icon" type="image/x-icon" href="{{ url('favicon.svg') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"/> --}}
    <link href="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.css') !!}" rel="stylesheet" />
    <link href="{!! CommonHelper::asset('nfy/plugins/pace/pace.min.css') !!}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet"/>

    @vite(['resources/css/app.css'])
    @stack('css')
</head>

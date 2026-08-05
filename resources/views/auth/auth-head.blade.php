<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="{{ url('favicon.ico') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'AMG Login')</title>
    @vite(['resources/css/app.css'])
    @yield('style')
</head>

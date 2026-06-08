<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') — SwiftPay Soluções</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('site/img/favicon-32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('site/img/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site/manifest.json') }}">
    <meta name="theme-color" content="#1E2E5E">

    {{-- App styles (Bootstrap + custom SCSS compiled) --}}
    <link rel="stylesheet" href="{{ asset('site/style.css') }}?v={{ time() }}">
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js" defer></script>
</head>
<body>

    @yield('content')

    <script src="{{ asset('site/jquery.js') }}"></script>
    <script src="{{ asset('site/bootstrap.js') }}"></script>
    <script src="{{ asset('site/functions.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"
        integrity="sha512-pHVGpX7F/27yZ0ISY+VVjyULApbDlD0/X0rgGbTqCE7WFW5MezNTWG/dnhtbBuICzsd0WQPgpE4REBLv+UqChw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @yield('scriptTable')

    @include('partials.alerts')
</body>
</html>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    <link rel="icon" href="{{ asset('site/img/favico.ico') }}" sizes="32x32">

    <link rel="stylesheet" href="{{ asset('site/style.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

</head>
<body>

@php
$currentRoute = Request::route()->getName();
@endphp


    <nav class="navbar navbar-expand-lg fixed-top" style="background-color:#242a74;">
      <div class="container-fluid">
        <a class="navbar-brand" href="#" style="cursor: default;"><img src="{{ asset('/site/img/swift_pay_solucoes_png.png') }}" width="250" height="63" alt="Swift Pay Soluções"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling">
          <span class="navbar-toggler-icon"></span>
        </button>

      </div>
    </nav>
    <div class="d-flex d-flex-container" style="min-height: 100vh;">

        @yield('content')


    </div>



    <script src="{{ asset('site/functions.js') }}"></script>
    <script src="{{ asset('site/jquery.js') }}"></script>
    <script src="{{ asset('site/bootstrap.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"
    integrity="sha512-pHVGpX7F/27yZ0ISY+VVjyULApbDlD0/X0rgGbTqCE7WFW5MezNTWG/dnhtbBuICzsd0WQPgpE4REBLv+UqChw=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>




@yield('scriptTable')

</body>
</html>

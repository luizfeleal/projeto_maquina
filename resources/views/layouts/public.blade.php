<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title') — SwiftPay Soluções</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('site/img/favicon-32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('site/img/apple-touch-icon.png') }}">
    <meta name="theme-color" content="#1E2E5E">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    {{-- App styles --}}
    <link rel="stylesheet" href="{{ asset('site/style.css') }}?v={{ time() }}">

    {{-- Iconify --}}
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js" defer></script>

    <style>
        :root {
            --pub-primary:    #1E2E5E;
            --pub-accent:     #2C9BA5;
            --pub-green:      #1a6b4a;
            --pub-bg:         #f8fafc;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--pub-bg);
            color: #1f2937;
            line-height: 1.6;
        }

        /* ── Navbar pública ── */
        .pub-navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            padding: 0 max(24px, 5vw);
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
            gap: 16px;
        }
        .pub-navbar .logo img { height: 36px; }
        .pub-navbar .nav-cta {
            background: var(--pub-primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 9px 20px;
            font-size: .88rem;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
        }
        .pub-navbar .nav-cta:hover { background: #162248; }

        /* ── Seção geral ── */
        .pub-section {
            padding: 80px max(24px, 5vw);
            max-width: 1200px;
            margin: 0 auto;
        }
        .pub-section--alt { background: #fff; max-width: 100%; }
        .pub-section--alt .inner { max-width: 1200px; margin: 0 auto; padding: 80px max(24px, 5vw); }

        /* ── Footer ── */
        .pub-footer {
            background: var(--pub-primary);
            color: rgba(255,255,255,.7);
            text-align: center;
            padding: 32px 24px;
            font-size: .82rem;
        }
        .pub-footer a { color: rgba(255,255,255,.8); text-decoration: none; }
    </style>

    @yield('head')
</head>
<body>

<nav class="pub-navbar">
    <a href="/" class="logo">
        <img src="{{ asset('site/img/swift_pay_solucoes_png.png') }}" alt="SwiftPay Soluções">
    </a>
    <a href="{{ route('login-view') }}" class="nav-cta">Acessar plataforma</a>
</nav>

@yield('content')

<footer class="pub-footer">
    &copy; {{ date('Y') }} SwiftPay Soluções. Todos os direitos reservados. &mdash;
    <a href="{{ route('login-view') }}">Login</a>
</footer>

<script src="{{ asset('site/jquery.js') }}"></script>
<script src="{{ asset('site/bootstrap.js') }}"></script>
@yield('scripts')
</body>
</html>

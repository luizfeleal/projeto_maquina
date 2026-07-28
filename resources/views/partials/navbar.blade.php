@php
    $userName = session('usuario_nome', '');
    $userRole = session('grupo_nome', '');
    $parts    = explode(' ', trim($userName));
    $initials = strtoupper(
        (isset($parts[0]) ? mb_substr($parts[0], 0, 1) : '') .
        (isset($parts[1]) ? mb_substr($parts[1], 0, 1) : '')
    );
@endphp

<header class="top-navbar">
    <button class="navbar-mobile-toggle" id="sidebarToggleBtn" type="button" aria-label="Abrir menu">
        <iconify-icon icon="solar:hamburger-menu-bold"></iconify-icon>
    </button>

    <div class="navbar-brand-mobile">
        <img src="{{ asset('site/img/swift_pay_solucoes_png.png') }}" alt="Swift Pay">
    </div>

    <div class="navbar-spacer"></div>

    <button type="button" class="navbar-pwa-install d-none" id="navbarPwaInstallBtn" title="Instalar app" aria-label="Instalar app SwiftPay">
        <iconify-icon icon="solar:download-minimalistic-bold-duotone"></iconify-icon>
        <span class="navbar-pwa-label">Instalar APP</span>
    </button>

    <button type="button" class="navbar-theme-toggle" id="themeToggleBtn" title="Alternar tema" aria-label="Alternar tema claro/escuro">
        <iconify-icon icon="solar:moon-bold-duotone" class="theme-icon-light"></iconify-icon>
        <iconify-icon icon="solar:sun-bold-duotone"  class="theme-icon-dark"></iconify-icon>
    </button>

    <div class="navbar-user">
        <div class="user-info">
            <span class="user-name">{{ $userName }}</span>
            <span class="user-role">{{ $userRole }}</span>
        </div>
        <div class="user-avatar" title="{{ $userName }}">{{ $initials ?: 'U' }}</div>
        <a href="{{ route('logout') }}" class="navbar-logout">
            <iconify-icon icon="solar:logout-3-bold"></iconify-icon>
            <span>Sair</span>
        </a>
    </div>
</header>

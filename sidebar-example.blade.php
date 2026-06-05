@php
    $currentRoute = Request::route()->getName();
@endphp

<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>

    <div>
        <a href="{{ route('clientes-dashboard') }}" class="sidebar-logo">
            <img src="{{ asset('site/img/logo_caron_mais_pdf.png') }}" alt="site logo" class="light-logo">
            <img src="{{ asset('site/img/logo_caron_mais_pdf.png') }}" alt="site logo" class="dark-logo">
            <img src="{{ asset('site/img/logo_caron_mais_pdf.png') }}" alt="site logo" class="logo-icon">
        </a>
    </div>

    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
            @foreach (getSidebar() as $menu)
                @php
                    $hasSubmenu = isset($menu['sub_menu']);
                    $route = $menu['route'] ?? null;
                    $icon = $menu['icon'] ?? null;
                    $title = $menu['title'] ?? null;
                    $activeRoutes = $menu['active_routes'] ?? [];

                    $userId = (int) session('id_usuario');
                    $allowedUsers = isset($menu['allowed_users']) ? array_map('intval', $menu['allowed_users']) : null;

                    if ($allowedUsers !== null) {
                        \Illuminate\Support\Facades\Log::info('DEBUG SIDEBAR PIX', [
                            'id_usuario_sessao' => session('id_usuario'),
                            'tipo' => gettype(session('id_usuario')),
                            'userId_cast' => $userId,
                            'allowedUsers' => $allowedUsers,
                            'in_array_result' => in_array($userId, $allowedUsers, true),
                        ]);
                    }
                @endphp

                @if ($allowedUsers !== null && !in_array($userId, $allowedUsers, true))
                    @continue
                @endif

                @if (!$hasSubmenu)
                    @if ($icon && $title)
                        <li>
                            <a href="{{ $route ? route($route) : '#' }}"
                                {{ isset($menu['target']) ? 'target=' . $menu['target'] : '' }}
                                class="{{ in_array($currentRoute, $activeRoutes) ? 'active-page' : '' }}">
                                <iconify-icon icon="{{ $icon }}" class="menu-icon" inline></iconify-icon>
                                <span>{{ $title }}</span>
                            </a>
                        </li>
                    @elseif($title)
                        <li class="sidebar-menu-group-title">{{ $title }}</li>
                    @endif
                @else
                    <li class="dropdown">
                        <a href="{{ $route ? route($route) : 'javascript:void(0)' }}">
                            @if ($icon)
                                <iconify-icon icon="{{ $icon }}" class="menu-icon" inline></iconify-icon>
                            @endif
                            <span>{{ $title }}</span>
                        </a>
                        @if (is_array($menu['sub_menu']))
                            <ul class="sidebar-submenu">
                                @foreach ($menu['sub_menu'] as $submenu)
                                    @if (isset($submenu['title'], $submenu['route']))
                                        <li>
                                            <a href="{{ route($submenu['route'], $submenu['params'] ?? []) }}">
                                                <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
                                                {{ $submenu['title'] }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</aside>

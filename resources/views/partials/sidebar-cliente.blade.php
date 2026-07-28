@php
    $currentRoute = Request::route()->getName();
    $menu = getSidebar('cliente');
@endphp

<aside class="sidebar" id="appSidebar">
    <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Fechar menu">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>

    <a href="{{ route('cliente-home') }}" class="sidebar-logo">
        <img src="{{ asset('site/img/swift_pay_solucoes_png.png') }}" alt="Swift Pay Soluções">
    </a>

    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
            @foreach ($menu as $item)
                @php
                    $hasSubmenu  = isset($item['sub_menu']) && is_array($item['sub_menu']);
                    $route       = $item['route'] ?? null;
                    $icon        = $item['icon'] ?? null;
                    $title       = $item['title'] ?? null;
                    $activeRoutes = $item['active_routes'] ?? [];
                    $isActive    = in_array($currentRoute, $activeRoutes);

                    $allowedUsers = isset($item['allowed_users'])
                        ? array_map('intval', $item['allowed_users'])
                        : null;
                    $userId = (int) session('id_usuario');
                @endphp

                @if ($allowedUsers !== null && !in_array($userId, $allowedUsers, true))
                    @continue
                @endif

                @if (!$hasSubmenu)
                    @if ($icon && $title)
                        <li>
                            <a href="{{ $route ? route($route) : '#' }}"
                               {{ isset($item['target']) ? 'target="' . $item['target'] . '"' : '' }}
                               class="{{ $isActive ? 'active-page' : '' }}">
                                <iconify-icon icon="{{ $icon }}" class="menu-icon" inline></iconify-icon>
                                <span>{{ $title }}</span>
                            </a>
                        </li>
                    @elseif ($title)
                        <li class="sidebar-menu-group-title">{{ $title }}</li>
                    @endif
                @else
                    <li class="dropdown {{ $isActive ? 'active' : '' }}">
                        <a href="javascript:void(0)" class="{{ $isActive ? 'open' : '' }}">
                            @if ($icon)
                                <iconify-icon icon="{{ $icon }}" class="menu-icon" inline></iconify-icon>
                            @endif
                            <span>{{ $title }}</span>
                        </a>
                        <ul class="sidebar-submenu {{ $isActive ? 'open' : '' }}">
                            @foreach ($item['sub_menu'] as $sub)
                                @if (isset($sub['title'], $sub['route']))
                                    <li>
                                        <a href="{{ route($sub['route'], $sub['params'] ?? []) }}"
                                           class="{{ $currentRoute === $sub['route'] ? 'active-submenu' : '' }}">
                                            <span class="circle-icon"></span>
                                            {{ $sub['title'] }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</aside>

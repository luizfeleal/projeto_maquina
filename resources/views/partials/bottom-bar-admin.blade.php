@php
    $r = Request::route()->getName();
@endphp

<script>document.documentElement.classList.add('has-bottom-nav'); document.body.classList.add('has-bottom-nav');</script>

<nav class="bottom-nav" aria-label="Navegação principal" data-fixed-nav="true">

    {{-- Home --}}
    <a href="{{ route('home') }}"
       class="bottom-nav-item {{ $r === 'home' ? 'active' : '' }}"
       aria-label="Home"
       @if($r === 'home') aria-current="page" @endif>
        <iconify-icon icon="solar:home-2-bold-duotone" aria-hidden="true"></iconify-icon>
        <span>Home</span>
    </a>

    {{-- Máquinas --}}
    <a href="{{ route('maquinas') }}"
       class="bottom-nav-item {{ in_array($r, ['maquinas','maquinas-show','maquinas-editar','maquinas-transacoes','maquinas-acumulado','maquinas-cartao']) ? 'active' : '' }}"
       aria-label="Máquinas"
       @if(in_array($r, ['maquinas','maquinas-show','maquinas-editar','maquinas-transacoes','maquinas-acumulado','maquinas-cartao'])) aria-current="page" @endif>
        <iconify-icon icon="solar:monitor-bold-duotone" aria-hidden="true"></iconify-icon>
        <span>Máquinas</span>
    </a>

    {{-- QR Code --}}
    <a href="{{ route('qr') }}"
       class="bottom-nav-item {{ in_array($r, ['qr','qr-criar']) ? 'active' : '' }}"
       aria-label="QR Code"
       @if(in_array($r, ['qr','qr-criar'])) aria-current="page" @endif>
        <iconify-icon icon="solar:qr-code-bold-duotone" aria-hidden="true"></iconify-icon>
        <span>QR Code</span>
    </a>

    {{-- Usuários --}}
    <a href="{{ route('usuarios') }}"
       class="bottom-nav-item {{ in_array($r, ['usuarios','usuario-criar','usuario-editar','usuario-detalhar']) ? 'active' : '' }}"
       aria-label="Usuários"
       @if(in_array($r, ['usuarios','usuario-criar','usuario-editar','usuario-detalhar'])) aria-current="page" @endif>
        <iconify-icon icon="solar:users-group-two-rounded-bold-duotone" aria-hidden="true"></iconify-icon>
        <span>Usuários</span>
    </a>

    {{-- Mais (abre sidebar como drawer) --}}
    <button type="button"
            class="bottom-nav-item bottom-nav-more"
            id="bottomNavMore"
            aria-label="Mais opções"
            aria-expanded="false">
        <iconify-icon icon="solar:menu-dots-bold-duotone" aria-hidden="true"></iconify-icon>
        <span>Mais</span>
    </button>

</nav>

<script src="{{ asset('site/bottom-nav.js') }}?v=1"></script>

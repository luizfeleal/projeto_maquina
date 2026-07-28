@php
    $r = Request::route()->getName();
@endphp

<script>document.body.classList.add('has-bottom-nav');</script>

<nav class="bottom-nav" aria-label="Navegação principal" data-fixed-nav="true">

    {{-- Dashboard --}}
    <a href="{{ route('financeiro-home') }}"
       class="bottom-nav-item {{ $r === 'financeiro-home' ? 'active' : '' }}"
       aria-label="Dashboard"
       @if($r === 'financeiro-home') aria-current="page" @endif>
        <iconify-icon icon="solar:pie-chart-2-bold-duotone" aria-hidden="true"></iconify-icon>
        <span>Dashboard</span>
    </a>

    {{-- Despesas --}}
    <a href="#"
       class="bottom-nav-item {{ in_array($r, ['financeiro-despesas', 'financeiro-despesas-criar']) ? 'active' : '' }}"
       aria-label="Despesas">
        <iconify-icon icon="solar:bill-list-bold-duotone" aria-hidden="true"></iconify-icon>
        <span>Despesas</span>
    </a>

    {{-- Inadimplência --}}
    <a href="{{ route('financeiro-inadimplencia') }}"
       class="bottom-nav-item {{ in_array($r, ['financeiro-inadimplencia']) ? 'active' : '' }}"
       aria-label="Inadimplência">
        <iconify-icon icon="solar:danger-triangle-bold-duotone" aria-hidden="true"></iconify-icon>
        <span>Inadimpl.</span>
    </a>

    {{-- Mensagens --}}
    <a href="#"
       class="bottom-nav-item {{ in_array($r, ['financeiro-mensagens']) ? 'active' : '' }}"
       aria-label="Mensagens">
        <iconify-icon icon="solar:chat-round-dots-bold-duotone" aria-hidden="true"></iconify-icon>
        <span>Mensagens</span>
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

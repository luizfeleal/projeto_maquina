@php
    $r = Request::route()->getName();
@endphp

<script>document.body.classList.add('has-bottom-nav');</script>

<nav class="bottom-nav" aria-label="Navegação principal" data-fixed-nav="true">

    {{-- Home --}}
    <a href="{{ route('cliente-home') }}"
       class="bottom-nav-item {{ $r === 'cliente-home' ? 'active' : '' }}"
       aria-label="Home">
        <iconify-icon icon="solar:home-2-bold-duotone" aria-hidden="true"></iconify-icon>
        <span>Home</span>
    </a>

    {{-- Máquinas --}}
    <a href="{{ route('clientes-maquinas') }}"
       class="bottom-nav-item {{ in_array($r, ['clientes-maquinas','clientes-maquinas-transacoes','clientes-maquinas-acumulado','cliente-maquinas-cartao']) ? 'active' : '' }}"
       aria-label="Máquinas">
        <iconify-icon icon="solar:monitor-bold-duotone" aria-hidden="true"></iconify-icon>
        <span>Máquinas</span>
    </a>

    {{-- Liberar Jogada --}}
    <a href="{{ route('view-clientes-maquinas-liberar-jogadas') }}"
       class="bottom-nav-item {{ $r === 'view-clientes-maquinas-liberar-jogadas' ? 'active' : '' }}"
       aria-label="Liberar Jogada">
        <iconify-icon icon="solar:play-circle-bold-duotone" aria-hidden="true"></iconify-icon>
        <span>Jogada</span>
    </a>

    {{-- QR Code --}}
    <a href="{{ route('cliente-qr') }}"
       class="bottom-nav-item {{ in_array($r, ['cliente-qr','cliente-qr-criar']) ? 'active' : '' }}"
       aria-label="QR Code">
        <iconify-icon icon="solar:qr-code-bold-duotone" aria-hidden="true"></iconify-icon>
        <span>QR Code</span>
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

<script>
(function () {
    function initBottomNav() {
        var btn     = document.getElementById('bottomNavMore');
        var sidebar = document.getElementById('appSidebar');
        var overlay = document.getElementById('sidebarOverlay');
        if (!btn || !sidebar) return;

        function openSidebar() {
            sidebar.classList.add('sidebar-open');
            if (overlay) overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
            btn.setAttribute('aria-expanded', 'true');
            btn.classList.add('active');
        }

        function closeSidebar() {
            sidebar.classList.remove('sidebar-open');
            if (overlay) overlay.classList.remove('active');
            document.body.style.overflow = '';
            btn.setAttribute('aria-expanded', 'false');
            btn.classList.remove('active');
        }

        btn.addEventListener('click', function () {
            var isOpen = sidebar.classList.contains('sidebar-open');
            isOpen ? closeSidebar() : openSidebar();
        });

        if (overlay) overlay.addEventListener('click', closeSidebar);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBottomNav);
    } else {
        initBottomNav();
    }
}());
</script>

@php
    $r = Request::route()->getName();
@endphp

<nav class="bottom-nav" aria-label="Navegação principal">

    {{-- Home --}}
    <a href="{{ route('home') }}"
       class="bottom-nav-item {{ $r === 'home' ? 'active' : '' }}"
       aria-label="Home">
        <iconify-icon icon="solar:home-2-bold-duotone" aria-hidden="true"></iconify-icon>
        <span>Home</span>
    </a>

    {{-- Máquinas --}}
    <a href="{{ route('maquinas') }}"
       class="bottom-nav-item {{ in_array($r, ['maquinas','maquinas-show','maquinas-editar','maquinas-transacoes','maquinas-acumulado','maquinas-cartao']) ? 'active' : '' }}"
       aria-label="Máquinas">
        <iconify-icon icon="solar:monitor-bold-duotone" aria-hidden="true"></iconify-icon>
        <span>Máquinas</span>
    </a>

    {{-- QR Code --}}
    <a href="{{ route('qr') }}"
       class="bottom-nav-item {{ in_array($r, ['qr','qr-criar']) ? 'active' : '' }}"
       aria-label="QR Code">
        <iconify-icon icon="solar:qr-code-bold-duotone" aria-hidden="true"></iconify-icon>
        <span>QR Code</span>
    </a>

    {{-- Usuários --}}
    <a href="{{ route('usuarios') }}"
       class="bottom-nav-item {{ in_array($r, ['usuarios','usuario-criar','usuario-editar','usuario-detalhar']) ? 'active' : '' }}"
       aria-label="Usuários">
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

<script>
(function () {
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
}());
</script>

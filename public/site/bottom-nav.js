(function () {
    'use strict';

    function activateNavItem(item) {
        var nav = item.closest('.bottom-nav');
        if (!nav) {
            return;
        }

        nav.querySelectorAll('.bottom-nav-item').forEach(function (el) {
            el.classList.remove('active', 'is-pressed');
            el.removeAttribute('aria-current');
        });

        item.classList.add('active', 'is-pressed');
        item.setAttribute('aria-current', 'page');
        nav.classList.add('is-navigating');
    }

    function initBottomNavLinks(nav) {
        nav.querySelectorAll('a.bottom-nav-item').forEach(function (link) {
            link.addEventListener('pointerdown', function (event) {
                if (event.pointerType === 'mouse' && event.button !== 0) {
                    return;
                }

                activateNavItem(link);
            }, { passive: true });

            link.addEventListener('keydown', function (event) {
                if (event.key !== 'Enter' && event.key !== ' ') {
                    return;
                }

                activateNavItem(link);
            });
        });
    }

    function initBottomNavMore(nav) {
        var btn = nav.querySelector('#bottomNavMore');
        var sidebar = document.getElementById('appSidebar');
        var overlay = document.getElementById('sidebarOverlay');

        if (!btn || !sidebar) {
            return;
        }

        function openSidebar() {
            sidebar.classList.add('sidebar-open');
            if (overlay) {
                overlay.classList.add('active');
            }
            document.body.classList.add('sidebar-drawer-open');
            btn.setAttribute('aria-expanded', 'true');
            btn.classList.add('active');
        }

        function closeSidebar() {
            sidebar.classList.remove('sidebar-open');
            if (overlay) {
                overlay.classList.remove('active');
            }
            document.body.classList.remove('sidebar-drawer-open');
            btn.setAttribute('aria-expanded', 'false');
            btn.classList.remove('active');
        }

        btn.addEventListener('click', function () {
            if (sidebar.classList.contains('sidebar-open')) {
                closeSidebar();
                return;
            }

            nav.querySelectorAll('a.bottom-nav-item').forEach(function (el) {
                el.classList.remove('active', 'is-pressed');
                el.removeAttribute('aria-current');
            });

            openSidebar();
        });

        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }
    }

    function initBottomNav() {
        var nav = document.querySelector('.bottom-nav');
        if (!nav) {
            return;
        }

        initBottomNavLinks(nav);
        initBottomNavMore(nav);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBottomNav);
    } else {
        initBottomNav();
    }
}());

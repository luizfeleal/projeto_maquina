<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') — Swift Pay</title>

    <link rel="icon" href="{{ asset('site/img/favico.ico') }}" sizes="32x32">

    {{-- DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

    {{-- Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">

    {{-- Bootstrap + Font Awesome --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    {{-- App styles --}}
    <link rel="stylesheet" href="{{ asset('site/style.css') }}?v={{ time() }}">

    {{-- Iconify --}}
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js" defer></script>
</head>

<body>

    <div class="app-wrapper">

        @include('partials.sidebar-cliente')

        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <div class="main-content">

            @include('partials.navbar')

            <div class="content-body">
                <div class="page-container">
                    @include('partials.breadcrumb')
                    @yield('content')
                </div>
            </div>

        </div>

    </div>

    <div id="loader" class="loader" style="display: none;">
        <div class="spinner-border spinner-load" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="{{ asset('site/jquery.js') }}"></script>
    <script src="{{ asset('site/bootstrap.js') }}"></script>
    <script src="{{ asset('site/functions.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"
        integrity="sha512-pHVGpX7F/27yZ0ISY+VVjyULApbDlD0/X0rgGbTqCE7WFW5MezNTWG/dnhtbBuICzsd0WQPgpE4REBLv+UqChw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @yield('scriptTable')

    @include('partials.alerts')

    <script>
        // =========================================================
        // SweetAlert2 — interceptor global de modais de confirmação
        // =========================================================
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-bs-toggle="modal"][data-bs-target]').forEach(function (trigger) {
                const targetId = (trigger.getAttribute('data-bs-target') || '').replace('#', '');
                if (!targetId.startsWith('ModalCenter')) return;

                const modal = document.getElementById(targetId);
                if (!modal) return;

                const originalOnclick = trigger.getAttribute('onclick') || '';

                trigger.removeAttribute('data-bs-toggle');
                trigger.removeAttribute('data-bs-target');

                trigger.addEventListener('click', function (e) {
                    e.preventDefault();

                    if (originalOnclick) {
                        try { eval(originalOnclick); } catch (_) {}
                    }

                    const title     = modal.querySelector('.modal-title')?.textContent?.trim() || 'Confirmar';
                    const text      = modal.querySelector('.modal-body p')?.textContent?.trim() || 'Tem certeza?';
                    const submitBtn = modal.querySelector('.modal-footer [type="submit"], .modal-footer .btn-primary');
                    const confirmFn = submitBtn?.getAttribute('onclick') || '';
                    const isDelete  = targetId.toLowerCase().includes('excluir');

                    Swal.fire({
                        title: title,
                        text: text,
                        icon: isDelete ? 'warning' : 'question',
                        showCancelButton: true,
                        confirmButtonColor: isDelete ? '#ef4444' : '#242A74',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: submitBtn?.textContent?.trim() || 'Confirmar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true,
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            if (confirmFn) {
                                try { eval(confirmFn); } catch (_) {}
                            } else if (submitBtn) {
                                submitBtn.closest('form')?.submit();
                            }
                        }
                    });
                });
            });
        });
        // ---- Loader ----
        window.addEventListener('load', function () {
            document.getElementById('loader').style.display = 'none';
        });

        // ---- Bootstrap tooltips ----
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        [...tooltipTriggerList].forEach(el => new bootstrap.Tooltip(el));

        // ---- Sidebar toggle (mobile) ----
        const sidebar   = document.getElementById('appSidebar');
        const overlay   = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggleBtn');
        const closeBtn  = document.getElementById('sidebarCloseBtn');

        function openSidebar() {
            sidebar.classList.add('sidebar-open');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('sidebar-open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
        if (closeBtn)  closeBtn.addEventListener('click', closeSidebar);
        if (overlay)   overlay.addEventListener('click', closeSidebar);

        // ---- Sidebar accordion ----
        document.querySelectorAll('.sidebar-menu .dropdown > a').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const parentLi = this.closest('.dropdown');
                const submenu  = parentLi.querySelector('.sidebar-submenu');
                const isOpen   = submenu && submenu.classList.contains('open');

                document.querySelectorAll('.sidebar-menu .dropdown').forEach(function (li) {
                    li.querySelector('a').classList.remove('open');
                    const sub = li.querySelector('.sidebar-submenu');
                    if (sub) sub.classList.remove('open');
                });

                if (!isOpen) {
                    this.classList.add('open');
                    if (submenu) submenu.classList.add('open');
                }
            });
        });

        // ---- Bootstrap form validation ----
        (function () {
            'use strict';
            document.querySelectorAll('.needs-validation').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    if (!form.checkValidity()) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>

</body>
</html>

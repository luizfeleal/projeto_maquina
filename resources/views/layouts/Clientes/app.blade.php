<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') — SwiftPay Soluções</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('site/img/favicon-32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('site/img/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site/manifest.json') }}">
    <meta name="theme-color" content="#1E2E5E">

    {{-- DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

    {{-- Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    {{-- App styles (Bootstrap + custom SCSS compiled) --}}
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

    {{-- CardView Toggle — deve vir antes do @yield('scriptTable') --}}
    <script>
    ;(function ($) {
        'use strict';

        var STORAGE_KEY   = 'pm_view_mode';
        var ACTION_HDRS   = ['detalhar','excluir','editar','ações','acao','acoes','ação','action','actions',''];

        function isActionHeader(h) {
            return ACTION_HDRS.indexOf(h.toLowerCase().trim()) !== -1;
        }

        function getHeaders($table) {
            var h = [];
            $table.find('thead tr:first-child th').each(function () {
                h.push($(this).text().trim());
            });
            return h;
        }

        var ACTION_CLASS_MAP = {
            'detalhar':   'card-action-view',
            'visualizar': 'card-action-view',
            'editar':     'card-action-edit',
            'excluir':    'card-action-delete',
        };

        function enrichActionCell(html, header) {
            var $tmp = $('<div>').append($.parseHTML(html));
            var hKey = header.toLowerCase().trim();
            var colVariant = ACTION_CLASS_MAP[hKey] || '';

            $tmp.find('a, button').each(function () {
                var $el  = $(this);
                var text = $el.text().trim().toLowerCase();

                var variant = colVariant;
                if (!variant) {
                    for (var keyword in ACTION_CLASS_MAP) {
                        if (text.indexOf(keyword) !== -1) {
                            variant = ACTION_CLASS_MAP[keyword];
                            break;
                        }
                    }
                }
                if (!variant) {
                    if ($el.hasClass('btn-danger'))  variant = 'card-action-delete';
                    else if ($el.hasClass('btn-success')) variant = 'card-action-edit';
                }

                var hasText = $el.text().trim().length > 0;

                $el.addClass('card-action-btn');
                if (variant) $el.addClass(variant);

                if (!$el.attr('aria-label') && text) {
                    $el.attr('aria-label', text || header);
                }

                if (!hasText && header) {
                    $el.append(
                        '<span class="card-action-text">' +
                        $('<span>').text(header).html() +
                        '</span>'
                    );
                }
            });

            return $tmp.contents();
        }

        function buildCard(rowEl, headers) {
            var $row      = $(rowEl);
            var $card     = $('<article class="data-card" tabindex="0" role="article"></article>');
            var $fields   = $('<div class="card-fields"></div>');
            var $actions  = $('<div class="card-actions" aria-label="Ações"></div>');
            var titleDone = false;

            $row.find('td').each(function (i) {
                var $cell  = $(this);
                var header = headers[i] !== undefined ? headers[i] : '';
                var html   = ($cell.html() || '').trim();
                var text   = ($cell.text() || '').trim();

                if (isActionHeader(header)) {
                    if (html) $actions.append(enrichActionCell(html, header));
                    return;
                }

                if (!titleDone && text) {
                    var $tmp = $('<div>').text(text);
                    $card.prepend('<h3 class="card-title">' + $tmp.html() + '</h3>');
                    titleDone = true;
                    return;
                }

                if (text || html) {
                    var labelHtml = header
                        ? '<span class="card-label">' + $('<div>').text(header).html() + '</span>'
                        : '';
                    $fields.append(
                        '<div class="card-field">' + labelHtml +
                        '<span class="card-value">' + html + '</span></div>'
                    );
                }
            });

            if ($fields.children().length) $card.append($fields);
            if ($actions.children().length) $card.append($actions);
            return $card;
        }

        function renderCards($grid, dt, headers) {
            $grid.empty();
            var total = dt.rows({ search: 'applied' }).count();

            if (total === 0) {
                $grid.append(
                    '<div class="card-empty-state" role="status">' +
                    '<iconify-icon icon="solar:inbox-line-duotone" aria-hidden="true"></iconify-icon>' +
                    '<p>Nenhum registro encontrado.</p>' +
                    '</div>'
                );
                return 0;
            }

            var nodes = dt.rows({ page: 'current' }).nodes();
            $(nodes).each(function () { $grid.append(buildCard(this, headers)); });
            return total;
        }

        function setup($table, dt) {
            var headers     = getHeaders($table);
            var $wrapper    = $table.closest('.dataTables_wrapper');
            var $responsive = $wrapper.closest('.tabela_responsiva');
            var $target     = $responsive.length ? $responsive : ($wrapper.length ? $wrapper : $table);
            var initMode    = localStorage.getItem(STORAGE_KEY) || 'card';

            var $bar = $(
                '<div class="view-toggle-bar" role="toolbar" aria-label="Controles de visualização">' +
                  '<div class="view-search-wrap">' +
                    '<span class="view-search-icon" aria-hidden="true">' +
                      '<iconify-icon icon="solar:magnifer-linear"></iconify-icon>' +
                    '</span>' +
                    '<input type="search" class="view-search-input" placeholder="Pesquisar..." ' +
                           'aria-label="Pesquisar registros" autocomplete="off">' +
                  '</div>' +
                  '<div class="view-btn-group" role="group" aria-label="Modo de visualização">' +
                    '<button type="button" class="view-btn view-btn-card" ' +
                            'title="Visualizar em cards" aria-label="Visualizar em cards" aria-pressed="false">' +
                      '<iconify-icon icon="solar:widget-bold-duotone"></iconify-icon>' +
                    '</button>' +
                    '<button type="button" class="view-btn view-btn-table" ' +
                            'title="Visualizar em tabela" aria-label="Visualizar em tabela" aria-pressed="false">' +
                      '<iconify-icon icon="solar:list-bold-duotone"></iconify-icon>' +
                    '</button>' +
                  '</div>' +
                  '<span class="view-count-badge" aria-live="polite" aria-atomic="true"></span>' +
                '</div>'
            );

            var $grid = $('<div class="card-grid" role="region" aria-label="Registros em cards"></div>');
            var $cardPager = $('<div class="card-pager" style="display:none; padding:12px 0 4px;"></div>');

            $target.before($bar);
            $target.after($grid);
            $grid.after($cardPager);
            $wrapper.find('.dataTables_filter').hide();

            function updateCardPager() {
                var info = dt.page.info();
                if (!info || info.pages <= 1) {
                    $cardPager.empty().hide();
                    return;
                }
                $cardPager.show();

                var cur   = info.page;
                var total = info.pages;
                var html  = '<nav aria-label="Paginação"><ul class="pagination justify-content-center flex-wrap mb-0">';

                html += '<li class="page-item' + (cur === 0 ? ' disabled' : '') + '">' +
                        '<button type="button" class="page-link card-page-btn" data-page="' + (cur - 1) + '" aria-label="Anterior">' +
                        '<iconify-icon icon="solar:arrow-left-linear" aria-hidden="true"></iconify-icon></button></li>';

                var s = Math.max(0, cur - 2);
                var e = Math.min(total - 1, cur + 2);

                if (s > 0) {
                    html += '<li class="page-item"><button type="button" class="page-link card-page-btn" data-page="0">1</button></li>';
                    if (s > 1) html += '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
                }
                for (var p = s; p <= e; p++) {
                    html += '<li class="page-item' + (p === cur ? ' active' : '') + '">' +
                            '<button type="button" class="page-link card-page-btn" data-page="' + p + '">' + (p + 1) + '</button></li>';
                }
                if (e < total - 1) {
                    if (e < total - 2) html += '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
                    html += '<li class="page-item"><button type="button" class="page-link card-page-btn" data-page="' + (total - 1) + '">' + total + '</button></li>';
                }

                html += '<li class="page-item' + (cur >= total - 1 ? ' disabled' : '') + '">' +
                        '<button type="button" class="page-link card-page-btn" data-page="' + (cur + 1) + '" aria-label="Próxima">' +
                        '<iconify-icon icon="solar:arrow-right-linear" aria-hidden="true"></iconify-icon></button></li>';

                html += '</ul></nav>';
                $cardPager.html(html);

                $cardPager.find('.card-page-btn').on('click', function () {
                    var pg = parseInt($(this).data('page'));
                    if (pg >= 0 && pg < total) { dt.page(pg).draw('page'); }
                });
            }

            function refresh() {
                var count = renderCards($grid, dt, headers);
                var label = count + ' registro' + (count !== 1 ? 's' : '');
                $bar.find('.view-count-badge').text(label).attr('aria-label', label);
                updateCardPager();
            }

            refresh();

            function applyMode(mode, persist) {
                if (persist !== false) localStorage.setItem(STORAGE_KEY, mode);
                var $cardBtn  = $bar.find('.view-btn-card');
                var $tableBtn = $bar.find('.view-btn-table');
                if (mode === 'card') {
                    $target.hide(); $grid.show();
                    $cardPager.show();
                    updateCardPager();
                    $cardBtn.addClass('active').attr('aria-pressed', 'true');
                    $tableBtn.removeClass('active').attr('aria-pressed', 'false');
                } else {
                    $target.show(); $grid.hide();
                    $cardPager.hide();
                    $tableBtn.addClass('active').attr('aria-pressed', 'true');
                    $cardBtn.removeClass('active').attr('aria-pressed', 'false');
                }
            }

            applyMode(initMode, false);

            $bar.find('.view-search-input').on('input', function () {
                dt.search($(this).val()).draw();
            });

            $grid.on('keydown', '.data-card', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    var $first = $(this).find('.card-actions a, .card-actions button').first();
                    if ($first.length) { e.preventDefault(); $first.trigger('click'); }
                }
            });

            $table.on('draw.dt', function () { refresh(); });

            $bar.find('.view-btn-card').on('click',  function () { applyMode('card');  });
            $bar.find('.view-btn-table').on('click', function () { applyMode('table'); });
        }

        $(document).on('init.dt', function (e, settings) {
            var $table = $(settings.nTable);
            if (!$table.attr('id') || $table.hasClass('no-card-view')) return;
            setup($table, $table.DataTable());
        });

    }(jQuery));
    </script>

    @yield('scriptTable')

    @include('partials.alerts')

    <script>
        // =========================================================
        // SweetAlert2 — interceptor via show.bs.modal
        // =========================================================
        document.addEventListener('show.bs.modal', function (e) {
            const modal = e.target;
            if (!modal || !modal.id || !modal.id.startsWith('ModalCenter')) return;

            e.preventDefault();

            const title     = modal.querySelector('.modal-title')?.textContent?.trim() || 'Confirmar';
            const text      = modal.querySelector('.modal-body p')?.textContent?.trim() || 'Tem certeza?';
            const submitBtn = modal.querySelector('.modal-footer [type="submit"]');
            const confirmFn = submitBtn?.getAttribute('onclick') || '';
            const isDelete  = modal.id.toLowerCase().includes('excluir');

            Swal.fire({
                title: title,
                text: text,
                icon: isDelete ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonColor: isDelete ? '#ef4444' : '#2C9BA5',
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

    @include('partials.bottom-bar-cliente')

</body>
</html>

@php
    $dashboardTipo = $dashboardTipo ?? 'admin';
    $isCliente     = $dashboardTipo === 'cliente';

    $verTodasRoute = $isCliente ? 'clientes-maquinas' : 'maquinas';
    $sectionTitle  = $isCliente ? 'Minhas máquinas' : 'Máquinas';

    $maqItems = [];
    foreach ($maquinasDashboard ?? [] as $maq) {
        $idMaquina = $maq['id_maquina'];
        $idLocal   = $maq['id_local'] ?? null;
        $possuiQr  = $maq['possui_qr'] ?? false;

        if ($isCliente) {
            $qrHref = ($idLocal && $possuiQr)
                ? route('cliente-qr', ['id_local' => $idLocal, 'id_maquina' => $idMaquina, 'abrir' => true])
                : route('cliente-qr-criar');
            $links = [
                'transacoes' => route('clientes-maquinas-transacoes', ['id_maquina' => $idMaquina]),
                'qr'         => $qrHref,
                'jogada'     => route('view-clientes-maquinas-liberar-jogadas', ['id_maquina' => $idMaquina]),
                'editar'     => route('clientes-maquinas-editar', ['id_maquina' => $idMaquina]),
            ];
        } else {
            $qrHref = ($idLocal && $possuiQr)
                ? route('qr', ['id_local' => $idLocal, 'id_maquina' => $idMaquina, 'abrir' => true])
                : route('qr-criar');
            $links = [
                'transacoes' => route('maquinas-transacoes', ['id_maquina' => $idMaquina]),
                'qr'         => $qrHref,
                'jogada'     => route('view-liberar-jogadas', ['id_maquina' => $idMaquina]),
                'editar'     => route('maquinas-editar', ['id_maquina' => $idMaquina]),
            ];
        }

        $maqItems[] = [
            'id_maquina'        => $idMaquina,
            'maquina_nome'      => $maq['maquina_nome'] ?? 'Máquina',
            'local_nome'        => $maq['local_nome'] ?? '—',
            'id_placa'          => $maq['id_placa'] ?? '—',
            'maquina_status'    => $maq['maquina_status'] ?? 1,
            'saldo_periodo'     => (float) ($maq['saldo_periodo'] ?? 0),
            'total_maquina'     => (float) ($maq['total_maquina'] ?? 0),
            'data_ultimo_reset' => $maq['data_ultimo_reset'] ?? null,
            'possui_qr'         => $possuiQr,
            'links'             => $links,
        ];
    }
@endphp

<section id="maquinas" class="dash-section">

    <div class="dash-section-header" style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <div>
            <h2>
                <iconify-icon icon="solar:devices-bold-duotone" style="color:var(--sp-teal);"></iconify-icon>
                {{ $sectionTitle }}
            </h2>
            <p>Status, saldo e atalhos rápidos por máquina</p>
        </div>
        <a href="{{ route($verTodasRoute) }}" class="dash-btn-primary" style="font-size:.78rem; padding:8px 16px;">
            <iconify-icon icon="solar:arrow-right-bold-duotone"></iconify-icon>
            Ver todas
        </a>
    </div>

    @php
        $resetRoute = $isCliente ? 'clientes-maquinas-reset-parcial' : 'maquinas-reset-parcial';
    @endphp

    <form id="formResetParcialDash" method="POST" action="{{ route($resetRoute) }}" style="display:none">
        @csrf
        <input type="hidden" name="id_maquina" id="resetDashIdMaquina">
    </form>

    @if(empty($maqItems))
        <div class="dash-card" style="padding:48px 24px; text-align:center; color:#9ca3af;">
            <iconify-icon icon="solar:devices-bold-duotone" style="font-size:2.5rem; display:block; margin:0 auto 12px;"></iconify-icon>
            <p style="margin:0; font-size:.9rem; font-weight:600;">Nenhuma máquina encontrada.</p>
        </div>
    @else
        <div class="maq-toolbar">
            <div class="maq-search-wrap">
                <iconify-icon icon="solar:magnifer-linear" class="maq-search-icon" aria-hidden="true"></iconify-icon>
                <input type="search" id="maq-dashboard-search" class="maq-search-input"
                       placeholder="Pesquisar máquina, local ou ID..." autocomplete="off"
                       aria-label="Pesquisar máquinas">
            </div>
            <span id="maq-dashboard-count" class="maq-count-badge" aria-live="polite"></span>
        </div>

        <div id="maq-dashboard-grid" class="maq-grid" role="list"></div>

        <div id="maq-dashboard-empty-filter" class="dash-card maq-empty-filter" hidden>
            <iconify-icon icon="solar:magnifer-linear" style="font-size:2rem; display:block; margin:0 auto 10px; color:#9ca3af;"></iconify-icon>
            <p style="margin:0; font-size:.9rem; font-weight:600; color:#6b7280;">Nenhuma máquina corresponde à pesquisa.</p>
        </div>

        <nav id="maq-dashboard-pagination" class="maq-pagination" aria-label="Paginação de máquinas"></nav>
    @endif
</section>

@if(!empty($maqItems))
@push('scriptTable')
<script>
$(function () {
    var maquinas = @json($maqItems);
    var perPage  = 6;
    var page     = 1;
    var term     = '';

    var $grid    = $('#maq-dashboard-grid');
    var $pager   = $('#maq-dashboard-pagination');
    var $count   = $('#maq-dashboard-count');
    var $search  = $('#maq-dashboard-search');
    var $empty   = $('#maq-dashboard-empty-filter');

    function brl(val) {
        var n = Number(val);
        if (isNaN(n)) n = 0;
        return 'R$ ' + n.toFixed(2).replace('.', ',');
    }

    function escapeHtml(str) {
        return $('<div>').text(str || '').html();
    }

    function filtered() {
        if (!term) return maquinas;
        var q = term.toLowerCase();
        return maquinas.filter(function (m) {
            return (m.maquina_nome || '').toLowerCase().indexOf(q) !== -1
                || (m.local_nome || '').toLowerCase().indexOf(q) !== -1
                || String(m.id_placa || '').toLowerCase().indexOf(q) !== -1
                || String(m.id_maquina || '').toLowerCase().indexOf(q) !== -1;
        });
    }

    function fmtDate(iso) {
        if (!iso) return null;
        try {
            return new Date(iso).toLocaleString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        } catch (e) { return null; }
    }

    function cardHtml(m) {
        var ativo      = Number(m.maquina_status) === 1;
        var saldo      = m.saldo_periodo || 0;
        var total      = m.total_maquina || 0;
        var saldoClr   = saldo < 0 ? '#dc2626' : 'var(--sp-teal)';
        var qrLabel    = m.possui_qr ? 'QR Code' : 'Gerar QR';
        var statusIcon = ativo
            ? '<span class="maq-status-dot maq-status-dot--online" aria-label="Online"></span>'
            : '<span class="maq-status-dot maq-status-dot--offline" aria-label="Offline"></span>';
        var ultimoReset = fmtDate(m.data_ultimo_reset);
        var resetLabel  = ultimoReset ? ultimoReset : 'Sem reset';

        return '<article class="dash-card maq-card-item" role="listitem" style="display:flex; flex-direction:column;">' +
            '<div style="padding:14px 16px 10px; border-bottom:1px solid #f3f4f6; display:flex; align-items:flex-start; justify-content:space-between; gap:8px;">' +
                '<div style="display:flex; align-items:center; gap:10px; min-width:0;">' +
                    '<div class="dash-icon dash-icon--teal" style="width:38px; height:38px;">' +
                        '<iconify-icon icon="solar:monitor-bold-duotone" style="font-size:1.2rem;"></iconify-icon>' +
                    '</div>' +
                    '<div style="min-width:0;">' +
                        '<p style="margin:0 0 4px; font-size:.88rem; font-weight:700; color:#111827; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">' +
                            escapeHtml(m.maquina_nome) + '</p>' +
                        '<p class="maq-card-meta">' +
                            '<iconify-icon icon="solar:map-point-bold-duotone" style="font-size:.8rem;"></iconify-icon>' +
                            '<span>Local: <strong>' + escapeHtml(m.local_nome || '—') + '</strong></span>' +
                        '</p>' +
                        '<p class="maq-card-meta" style="margin-top:3px;">' +
                            '<iconify-icon icon="solar:cpu-bold-duotone" style="font-size:.8rem;"></iconify-icon>' +
                            '<span>ID: <strong>' + escapeHtml(m.id_placa || m.id_maquina || '—') + '</strong></span>' +
                        '</p>' +
                    '</div>' +
                '</div>' +
                '<span style="flex-shrink:0; font-size:1rem; line-height:1;" title="' + (ativo ? 'Online' : 'Offline') + '">' +
                    statusIcon +
                '</span>' +
            '</div>' +
            '<div style="padding:10px 16px; border-bottom:1px solid #f3f4f6;">' +
                '<div style="display:flex; gap:8px;">' +
                    '<div style="flex:1; text-align:center;">' +
                        '<p style="margin:0 0 2px; font-size:.58rem; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:#9ca3af;">Saldo período</p>' +
                        '<p style="margin:0; font-size:.88rem; font-weight:700; color:' + saldoClr + ';">' + brl(saldo) + '</p>' +
                    '</div>' +
                    '<div style="width:1px; background:#f3f4f6;"></div>' +
                    '<div style="flex:1; text-align:center;">' +
                        '<p style="margin:0 0 2px; font-size:.58rem; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:#9ca3af;">Total máquina</p>' +
                        '<p style="margin:0; font-size:.88rem; font-weight:700; color:#111827;">' + brl(total) + '</p>' +
                    '</div>' +
                '</div>' +
                '<div style="margin-top:8px; text-align:center;">' +
                    '<p style="margin:0; font-size:.65rem; color:#9ca3af;">' +
                        '<iconify-icon icon="solar:clock-circle-bold-duotone" style="font-size:.7rem; vertical-align:middle;"></iconify-icon>' +
                        ' Último reset: <strong style="color:' + (ultimoReset ? '#6b7280' : '#d1d5db') + ';">' + escapeHtml(resetLabel) + '</strong>' +
                    '</p>' +
                '</div>' +
            '</div>' +
            '<div style="padding:12px 16px; display:flex; flex-wrap:wrap; gap:6px; margin-top:auto;">' +
                '<a href="' + m.links.transacoes + '" class="dash-action-btn">' +
                    '<iconify-icon icon="solar:transfer-horizontal-bold-duotone" style="color:var(--sp-teal);"></iconify-icon> Extrato</a>' +
                '<a href="' + m.links.qr + '" class="dash-action-btn">' +
                    '<iconify-icon icon="solar:qr-code-bold-duotone" style="color:var(--sp-navy);"></iconify-icon> ' + qrLabel + '</a>' +
                '<a href="' + m.links.jogada + '" class="dash-action-btn">' +
                    '<iconify-icon icon="solar:play-circle-bold-duotone" style="color:#ca8a04;"></iconify-icon> Jogada</a>' +
                '<a href="' + m.links.editar + '" class="dash-action-btn">' +
                    '<iconify-icon icon="solar:pen-bold-duotone" style="color:var(--sp-navy);"></iconify-icon> Editar</a>' +
                '<button type="button" class="dash-action-btn maq-btn-reset-dash" ' +
                    'data-id="' + escapeHtml(String(m.id_maquina)) + '" ' +
                    'data-nome="' + escapeHtml(m.maquina_nome) + '" ' +
                    'data-saldo="' + saldo + '">' +
                    '<iconify-icon icon="solar:restart-bold-duotone" style="color:#f59e0b;"></iconify-icon> Reset</button>' +
            '</div>' +
        '</article>';
    }

    function renderPager(total, pages) {
        if (pages <= 1) {
            $pager.empty().hide();
            return;
        }
        $pager.show();
        var html = '<ul class="pagination justify-content-center flex-wrap mb-0">';
        html += '<li class="page-item' + (page <= 1 ? ' disabled' : '') + '">' +
            '<button type="button" class="page-link maq-page-btn" data-page="' + (page - 1) + '" aria-label="Anterior">' +
            '<iconify-icon icon="solar:arrow-left-linear"></iconify-icon></button></li>';

        var s = Math.max(1, page - 2);
        var e = Math.min(pages, page + 2);
        if (s > 1) {
            html += '<li class="page-item"><button type="button" class="page-link maq-page-btn" data-page="1">1</button></li>';
            if (s > 2) html += '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
        }
        for (var p = s; p <= e; p++) {
            html += '<li class="page-item' + (p === page ? ' active' : '') + '">' +
                '<button type="button" class="page-link maq-page-btn" data-page="' + p + '">' + p + '</button></li>';
        }
        if (e < pages) {
            if (e < pages - 1) html += '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
            html += '<li class="page-item"><button type="button" class="page-link maq-page-btn" data-page="' + pages + '">' + pages + '</button></li>';
        }

        html += '<li class="page-item' + (page >= pages ? ' disabled' : '') + '">' +
            '<button type="button" class="page-link maq-page-btn" data-page="' + (page + 1) + '" aria-label="Próxima">' +
            '<iconify-icon icon="solar:arrow-right-linear"></iconify-icon></button></li>';
        html += '</ul>';
        $pager.html(html);
    }

    function render() {
        var list  = filtered();
        var total = list.length;
        var pages = Math.max(1, Math.ceil(total / perPage));

        if (page > pages) page = pages;

        $count.text(total + ' máquina' + (total !== 1 ? 's' : ''));

        if (total === 0) {
            $grid.empty().hide();
            $empty.show();
            $pager.empty().hide();
            return;
        }

        $empty.hide();
        $grid.show();

        var start = (page - 1) * perPage;
        var slice = list.slice(start, start + perPage);
        $grid.html(slice.map(cardHtml).join(''));

        renderPager(total, pages);
    }

    $search.on('input', function () {
        term = $(this).val().trim().toLowerCase();
        page = 1;
        render();
    });

    $pager.on('click', '.maq-page-btn', function () {
        if ($(this).parent().hasClass('disabled')) return;
        var pg = parseInt($(this).data('page'), 10);
        var pages = Math.max(1, Math.ceil(filtered().length / perPage));
        if (pg >= 1 && pg <= pages) {
            page = pg;
            render();
            document.getElementById('maquinas').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });

    $grid.on('click', '.maq-btn-reset-dash', function () {
        var id    = $(this).data('id');
        var nome  = $(this).data('nome');
        var saldo = $(this).data('saldo');

        Swal.fire({
            icon: 'warning',
            title: 'Confirmar Reset Parcial?',
            html:
                '<p class="mb-1">Máquina: <strong>' + $('<div>').text(nome).html() + '</strong></p>' +
                '<p class="mb-2">Saldo do período atual: <strong>' + brl(saldo) + '</strong></p>' +
                '<p class="text-muted small">O total acumulado da máquina <strong>não será alterado</strong>.<br>Apenas o saldo do período será reiniciado.</p>',
            showCancelButton: true,
            confirmButtonText: '<iconify-icon icon="solar:restart-bold-duotone"></iconify-icon> Confirmar Reset',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#6c757d',
            reverseButtons: true,
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $('#resetDashIdMaquina').val(id);
            $('#formResetParcialDash').submit();
        });
    });

    render();
});
</script>
@endpush
@endif

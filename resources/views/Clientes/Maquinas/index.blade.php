@extends('layouts.Clientes.app')
@section('title', 'Minhas Máquinas')

@section('content')
<div class="page-heading">
    <h1>Minhas Máquinas</h1>
    <p>Visão geral das suas máquinas e atalhos de ação</p>
</div>

<div class="content-body" style="padding-top:0;">

    <form id="formResetParcialMaquinas" method="POST"
          action="{{ route('clientes-maquinas-reset-parcial') }}" style="display:none">
        @csrf
        <input type="hidden" name="id_maquina" id="resetMaquinasIdMaquina">
    </form>

    @if(!$temMaquinas)
        <div style="background:#fff; border:1px dashed #e8ecf0; border-radius:14px;
                    padding:60px 24px; text-align:center; color:#9ca3af;">
            <iconify-icon icon="solar:devices-bold-duotone"
                          style="font-size:2.5rem; display:block; margin:0 auto 12px;"></iconify-icon>
            <p style="margin:0; font-size:.9rem; font-weight:600;">Nenhuma máquina encontrada.</p>
        </div>
    @else
        <form id="form-busca-maquinas" method="GET" action="{{ route('clientes-maquinas') }}"
              class="view-toggle-bar" role="search" aria-label="Buscar máquinas">
            <div class="view-search-wrap">
                <span class="view-search-icon" aria-hidden="true">
                    <iconify-icon icon="solar:magnifer-linear"></iconify-icon>
                </span>
                <input type="search" name="busca" class="view-search-input"
                       placeholder="Pesquisar por máquina, local ou ID da placa..."
                       value="{{ $busca }}" aria-label="Pesquisar máquinas" autocomplete="off">
            </div>
            <span class="view-count-badge" aria-live="polite" aria-atomic="true">
                @if($paginator->hasPages())
                    Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }} ·
                @endif
                Exibindo {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}
                de {{ $paginator->total() }} máquina{{ $paginator->total() !== 1 ? 's' : '' }}
            </span>
        </form>

        @if($paginator->isEmpty())
            <div style="background:#fff; border:1px dashed #e8ecf0; border-radius:14px;
                        padding:48px 24px; text-align:center; color:#9ca3af;">
                <iconify-icon icon="solar:magnifer-zoom-in-bold-duotone"
                              style="font-size:2.2rem; display:block; margin:0 auto 12px;"></iconify-icon>
                <p style="margin:0 0 8px; font-size:.9rem; font-weight:600;">Nenhuma máquina encontrada para esta busca.</p>
                @if($busca !== '')
                    <a href="{{ route('clientes-maquinas') }}"
                       style="font-size:.82rem; color:#2C9BA5; text-decoration:none; font-weight:600;">
                        Limpar busca
                    </a>
                @endif
            </div>
        @else
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px,1fr));
                    gap:16px;">
            @foreach($maquinas as $maq)
            @php
                $ativo      = ($maq['maquina_status'] ?? 1) == 1;
                $total      = $maq['total_maquina']   ?? 0;
                $saldo      = $maq['saldo_periodo']    ?? $total;
                $temReset   = $maq['tem_reset']        ?? false;
                $idMaquina  = $maq['id_maquina'];
                $idLocal    = $maq['id_local']         ?? null;
                $possuiQr   = $maq['possui_qr']        ?? false;
                $nome       = $maq['maquina_nome']     ?? 'Máquina';
                $local      = $maq['local_nome']       ?? '—';
                $idPlaca       = $maq['id_placa'] ?? '—';
                $serialCurto   = ($idPlaca !== '—' && strlen($idPlaca) >= 4)
                    ? substr($idPlaca, -4)
                    : $idPlaca;
                $qrHref     = ($idLocal && $possuiQr)
                    ? route('cliente-qr', ['id_local' => $idLocal, 'id_maquina' => $idMaquina, 'abrir' => true])
                    : route('cliente-qr-criar');
            @endphp
            <div style="background:#fff; border:1px solid #e8ecf0; border-radius:16px;
                        box-shadow:0 1px 4px rgba(0,0,0,.05); overflow:hidden; display:flex; flex-direction:column;">

                {{-- Cabeçalho do card --}}
                <div style="padding:18px 20px 14px; border-bottom:1px solid #f3f4f6;
                            display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div style="display:flex; align-items:center; gap:12px; min-width:0;">
                        <div style="width:44px; height:44px; border-radius:12px; flex-shrink:0;
                                    background:#e0f2fe; display:flex; align-items:center; justify-content:center;">
                            <iconify-icon icon="solar:monitor-bold-duotone"
                                          style="font-size:1.4rem; color:#0284c7;"></iconify-icon>
                        </div>
                        <div style="min-width:0;">
                            <p style="margin:0 0 2px; font-size:.95rem; font-weight:700; color:#111827;
                                      white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                {{ $nome }}
                            </p>
                            <p style="margin:0; font-size:.75rem; color:#9ca3af;
                                      display:flex; align-items:center; gap:4px;">
                                <iconify-icon icon="solar:map-point-bold-duotone"
                                              style="font-size:.85rem;"></iconify-icon>
                                Local: <span style="font-weight:600; color:#6b7280;">{{ $local }}</span>
                            </p>
                            <p style="margin:3px 0 0; font-size:.72rem; color:#9ca3af;
                                      display:flex; align-items:center; gap:4px;">
                                <iconify-icon icon="solar:cpu-bold-duotone"
                                              style="font-size:.8rem;"></iconify-icon>
                                ID: <span style="font-weight:600; color:#6b7280;" title="Serial: {{ $idPlaca }}">...{{ $serialCurto }}</span>
                            </p>
                        </div>
                    </div>
                    <span style="flex-shrink:0; display:flex; align-items:center; padding-top:2px;"
                          title="{{ $ativo ? 'Online' : 'Offline' }}">
                        <span aria-label="{{ $ativo ? 'Online' : 'Offline' }}"
                              style="display:inline-block;width:10px;height:10px;border-radius:50%;flex-shrink:0;background:{{ $ativo ? '#16a34a' : '#dc2626' }};{{ $ativo ? 'box-shadow:0 0 0 2px #bbf7d0;' : '' }}"></span>
                    </span>
                </div>

                {{-- Totais --}}
                <div style="padding:14px 20px; display:flex; gap:16px; border-bottom:1px solid #f3f4f6;">
                    <div style="flex:1; text-align:center;">
                        <p style="margin:0 0 2px; font-size:.65rem; font-weight:600; text-transform:uppercase;
                                   letter-spacing:.06em; color:#111827;">Total acumulado</p>
                        <p style="margin:0; font-size:1rem; font-weight:700; color:#16a34a;">
                            R$ {{ number_format($total, 2, ',', '.') }}
                        </p>
                    </div>
                    <div style="width:1px; background:#f3f4f6;"></div>
                    <div style="flex:1; text-align:center;">
                        <p style="margin:0 0 2px; font-size:.65rem; font-weight:600; text-transform:uppercase;
                                   letter-spacing:.06em; color:#111827;">Saldo do período</p>
                        <p data-saldo-maquina="{{ $idMaquina }}"
                           style="margin:0; font-size:1rem; font-weight:700;
                                  color:{{ $saldo < 0 ? '#dc2626' : '#16a34a' }};">
                            R$ {{ number_format($saldo, 2, ',', '.') }}
                        </p>
                    </div>
                    @if($temReset)
                    <div style="width:1px; background:#f3f4f6;"></div>
                    <div style="flex:1; text-align:center;">
                        <p style="margin:0 0 2px; font-size:.65rem; font-weight:600; text-transform:uppercase;
                                   letter-spacing:.06em; color:#111827;">Último reset</p>
                        <p style="margin:0; font-size:.75rem; font-weight:600; color:#6b7280;">
                            {{ $maq['data_ultimo_reset'] ? date('d/m/Y', strtotime($maq['data_ultimo_reset'])) : '—' }}
                        </p>
                    </div>
                    @endif
                </div>

                {{-- Ações --}}
                <div style="padding:14px 20px; display:flex; flex-wrap:wrap; gap:8px; margin-top:auto;">
                    <a href="{{ route('clientes-maquinas-transacoes', ['id_maquina' => $idMaquina]) }}"
                       style="flex:1; min-width:100px; text-align:center; text-decoration:none;
                              background:#f8fafc; border:1px solid #e8ecf0; border-radius:8px;
                              padding:8px 10px; font-size:.78rem; font-weight:600; color:#374151;
                              display:flex; align-items:center; justify-content:center; gap:5px;">
                        <iconify-icon icon="solar:transfer-horizontal-bold-duotone"
                                      style="font-size:.95rem; color:#2C9BA5;"></iconify-icon>
                        Extrato
                    </a>


                    <a href="{{ route('view-clientes-maquinas-liberar-jogadas', ['id_maquina' => $idMaquina]) }}"
                       style="flex:1; min-width:100px; text-align:center; text-decoration:none;
                              background:#f8fafc; border:1px solid #e8ecf0; border-radius:8px;
                              padding:8px 10px; font-size:.78rem; font-weight:600; color:#374151;
                              display:flex; align-items:center; justify-content:center; gap:5px;">
                        <iconify-icon icon="solar:play-circle-bold-duotone"
                                      style="font-size:.95rem; color:#ca8a04;"></iconify-icon>
                        Liberar Jogada
                    </a>

                    <a href="{{ $qrHref }}"
                       style="flex:1; min-width:100px; text-align:center; text-decoration:none;
                              background:#f8fafc; border:1px solid #e8ecf0; border-radius:8px;
                              padding:8px 10px; font-size:.78rem; font-weight:600; color:#374151;
                              display:flex; align-items:center; justify-content:center; gap:5px;">
                        <iconify-icon icon="solar:qr-code-bold-duotone"
                                      style="font-size:.95rem; color:#7c3aed;"></iconify-icon>
                        {{ $possuiQr ? 'Ver QR Code' : 'Gerar QR Code' }}
                    </a>

                    <a href="{{ route('clientes-maquinas-editar', ['id_maquina' => $idMaquina]) }}"
                       style="flex:1; min-width:100px; text-align:center; text-decoration:none;
                              background:#f8fafc; border:1px solid #e8ecf0; border-radius:8px;
                              padding:8px 10px; font-size:.78rem; font-weight:600; color:#374151;
                              display:flex; align-items:center; justify-content:center; gap:5px;">
                        <iconify-icon icon="solar:pen-bold-duotone"
                                      style="font-size:.95rem; color:#6366f1;"></iconify-icon>
                        Editar
                    </a>

                    <button type="button"
                            class="btn-reset-maquina"
                            data-id="{{ $idMaquina }}"
                            data-nome="{{ $nome }}"
                            data-saldo="{{ $saldo }}"
                            style="flex:1; min-width:130px; text-align:center;
                                   background:#fffbeb; border:1px solid #fde68a; border-radius:8px;
                                   padding:8px 10px; font-size:.78rem; font-weight:600; color:#92400e;
                                   display:flex; align-items:center; justify-content:center; gap:5px;
                                   cursor:pointer;">
                        <iconify-icon icon="solar:restart-bold-duotone"
                                      style="font-size:.95rem; color:#f59e0b;"></iconify-icon>
                        Resetar Aferição
                    </button>
                </div>

            </div>
            @endforeach
        </div>

        @if($paginator->hasPages())
            @php
                $paginaAtual = $paginator->currentPage();
                $ultimaPagina = $paginator->lastPage();
                $inicio = max(1, $paginaAtual - 2);
                $fim    = min($ultimaPagina, $paginaAtual + 2);
                $linkPagina = fn($p) => route('clientes-maquinas', array_filter([
                    'busca' => $busca !== '' ? $busca : null,
                    'page'  => $p > 1 ? $p : null,
                ]));
            @endphp
            <div class="card-pager" style="padding:16px 0 4px;">
                <nav aria-label="Paginação de máquinas">
                    <ul class="pagination justify-content-center flex-wrap mb-0">
                        <li class="page-item {{ $paginaAtual <= 1 ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $linkPagina($paginaAtual - 1) }}" aria-label="Anterior">
                                <iconify-icon icon="solar:arrow-left-linear" aria-hidden="true"></iconify-icon>
                            </a>
                        </li>

                        @if($inicio > 1)
                            <li class="page-item">
                                <a class="page-link" href="{{ $linkPagina(1) }}">1</a>
                            </li>
                            @if($inicio > 2)
                                <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                            @endif
                        @endif

                        @for($p = $inicio; $p <= $fim; $p++)
                            <li class="page-item {{ $p === $paginaAtual ? 'active' : '' }}">
                                <a class="page-link" href="{{ $linkPagina($p) }}">{{ $p }}</a>
                            </li>
                        @endfor

                        @if($fim < $ultimaPagina)
                            @if($fim < $ultimaPagina - 1)
                                <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                            @endif
                            <li class="page-item">
                                <a class="page-link" href="{{ $linkPagina($ultimaPagina) }}">{{ $ultimaPagina }}</a>
                            </li>
                        @endif

                        <li class="page-item {{ $paginaAtual >= $ultimaPagina ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $linkPagina($paginaAtual + 1) }}" aria-label="Próxima">
                                <iconify-icon icon="solar:arrow-right-linear" aria-hidden="true"></iconify-icon>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        @endif
        @endif
    @endif

</div>
@endsection

@section('scriptTable')
<script>
$(document).ready(function () {
    var $form  = $('#form-busca-maquinas');
    var $input = $form.find('.view-search-input');
    var timer;

    function brl(val) {
        var n = Number(val);
        if (isNaN(n)) n = 0;
        return 'R$ ' + n.toFixed(2).replace('.', ',');
    }

    $input.on('input', function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
            $form.submit();
        }, 400);
    });

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: @json(session('success')),
            timer: 3000,
            showConfirmButton: false,
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Erro',
            text: @json(session('error')),
        });
    @endif

    var RESET_URL = '{{ route('clientes-maquinas-reset-parcial-ajax') }}';
    var CSRF      = '{{ csrf_token() }}';

    $(document).on('click', '.btn-reset-maquina', function () {
        var $btn  = $(this);
        var id    = $btn.data('id');
        var nome  = $btn.data('nome');
        var saldo = $btn.data('saldo');

        Swal.fire({
            icon: 'warning',
            title: 'Resetar Aferição?',
            html:
                '<p class="mb-1">Máquina: <strong>' + $('<div>').text(nome).html() + '</strong></p>' +
                '<p class="mb-2">Saldo do período atual: <strong>' + brl(saldo) + '</strong></p>' +
                '<p class="text-muted small">O total acumulado da máquina <strong>não será alterado</strong>.<br>' +
                'Apenas o saldo do período será reiniciado.</p>',
            showCancelButton: true,
            confirmButtonText: 'Confirmar Reset',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#6c757d',
            reverseButtons: true,
        }).then(function (result) {
            if (!result.isConfirmed) return;

            $btn.prop('disabled', true).css('opacity', '.6');

            fetch(RESET_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ id_maquina: id }),
            })
            .then(function (resp) { return resp.json(); })
            .then(function (data) {
                if (data.success) {
                    // Atualiza saldo do card em tempo real (sem reload)
                    var $saldoEl = $('[data-saldo-maquina="' + id + '"]');
                    $saldoEl.text('R$ 0,00').css('color', '#16a34a');
                    $btn.data('saldo', 0);

                    Swal.fire({
                        icon: 'success',
                        title: 'Aferição resetada!',
                        text: data.message,
                        timer: 2500,
                        showConfirmButton: false,
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Erro', text: data.message });
                }
            })
            .catch(function () {
                Swal.fire({ icon: 'error', title: 'Erro', text: 'Falha na comunicação com o servidor.' });
            })
            .finally(function () {
                $btn.prop('disabled', false).css('opacity', '1');
            });
        });
    });
});
</script>
@endsection

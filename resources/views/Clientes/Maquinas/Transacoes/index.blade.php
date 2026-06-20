@extends('layouts.Clientes.app')
@section('title', 'Minhas Máquinas -> Extrato')
@section('content')

@php
    $totalAcumulado = $resumo['total_acumulado'] ?? 0;
    $totalSaldo     = $resumo['total_saldo']     ?? 0;
    $idsMaquinas    = $resumo['ids_maquinas']    ?? [];
    $totalPix       = $resumo['total_pix']       ?? 0;
    $totalCartao    = $resumo['total_cartao']    ?? 0;
    $totalDinheiro  = $resumo['total_dinheiro']  ?? 0;
    $totalDevolucao = $resumo['total_devolucao'] ?? 0;
    $qtdMaquinas    = count($idsMaquinas);
    $resetUnico     = $qtdMaquinas === 1;
    $idMaquinaReset = $resetUnico ? ($idsMaquinas[0] ?? null) : null;
    $brl = fn($v) => 'R$ ' . number_format($v, 2, ',', '.');
@endphp

{{-- ── Cabeçalho com título + botão de reset ─────────────────────── --}}
<div style="display:flex; align-items:center; justify-content:space-between;
            flex-wrap:wrap; gap:12px; margin:16px 24px 20px;">
    <div>
        <h1 style="margin:0; font-size:1.5rem; font-weight:700; color:#111827;">Extrato</h1>
        <p style="margin:4px 0 0; color:#6b7280; font-size:.875rem;">
            Histórico completo de movimentações das suas máquinas
        </p>
    </div>
    <button id="btn-reset-todas"
            @if($qtdMaquinas === 0) disabled @endif
            style="background:{{ $qtdMaquinas === 0 ? '#e5e7eb' : '#fbbf24' }}; border:none; border-radius:10px;
                   padding:12px 24px; font-weight:700; font-size:.9rem;
                   color:{{ $qtdMaquinas === 0 ? '#9ca3af' : '#1a1a1a' }};
                   cursor:{{ $qtdMaquinas === 0 ? 'not-allowed' : 'pointer' }};
                   display:flex; align-items:center; gap:8px;
                   box-shadow:0 1px 4px rgba(0,0,0,.12); white-space:nowrap;">
        <iconify-icon icon="solar:restart-bold-duotone" style="font-size:1.1rem;"></iconify-icon>
        Reset Parcial
    </button>
</div>

<div class="content-body" style="padding-top:0;">

    {{-- ── Filtro de máquina ──────────────────────────────────────── --}}
    @if(count($listaMaquinas) > 1)
    <form method="GET" action="{{ route('clientes-maquinas-transacoes') }}"
          style="margin-bottom:16px; display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
        <label style="font-size:.825rem; font-weight:600; color:#374151; white-space:nowrap;">
            <iconify-icon icon="solar:filter-bold-duotone" style="vertical-align:middle; margin-right:4px;"></iconify-icon>
            Filtrar máquina:
        </label>
        <div style="min-width:280px; max-width:400px; flex:1;">
            <select name="id_maquina" id="filtro-maquina-transacoes"
                    class="select-filtro-maquina-transacoes form-control">
                <option value="">Todas as máquinas</option>
                @foreach($listaMaquinas as $maq)
                    <option value="{{ $maq['id_maquina'] }}"
                        {{ (string)$idMaquinaSel === (string)$maq['id_maquina'] ? 'selected' : '' }}>
                        {{ $maq['maquina_nome'] }} — {{ $maq['local_nome'] }}
                    </option>
                @endforeach
            </select>
        </div>
        @if($idMaquinaSel)
            <a href="{{ route('clientes-maquinas-transacoes') }}"
               style="font-size:.8rem; color:#6b7280; text-decoration:none;">
                ✕ Limpar filtro
            </a>
        @endif
    </form>
    @endif

    {{-- ── Cards: Acumulado + Saldo ───────────────────────────────── --}}
    <div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:12px;">

        <div style="flex:1; min-width:200px; background:#fff; border:1px solid #e8ecf0;
                    border-radius:14px; padding:16px 20px;
                    box-shadow:0 1px 4px rgba(0,0,0,.05); display:flex; align-items:center; gap:14px;">
            <div style="width:46px; height:46px; border-radius:12px; flex-shrink:0;
                        background:#e0f2fe; display:flex; align-items:center; justify-content:center;">
                <iconify-icon icon="solar:wallet-money-bold-duotone"
                              style="font-size:1.4rem; color:#0284c7;"></iconify-icon>
            </div>
            <div>
                <p style="font-size:.7rem; font-weight:600; text-transform:uppercase;
                           letter-spacing:.06em; color:#9ca3af; margin:0 0 4px;">Total acumulado</p>
                <p style="font-size:1.25rem; font-weight:700; color:#0284c7; margin:0;">
                    {{ $brl($totalAcumulado) }}
                </p>
            </div>
        </div>

        <div style="flex:1; min-width:200px; background:#fff; border:1px solid #e8ecf0;
                    border-radius:14px; padding:16px 20px;
                    box-shadow:0 1px 4px rgba(0,0,0,.05); display:flex; align-items:center; gap:14px;">
            <div style="width:46px; height:46px; border-radius:12px; flex-shrink:0;
                        background:{{ $totalSaldo < 0 ? '#fee2e2' : '#dcfce7' }};
                        display:flex; align-items:center; justify-content:center;">
                <iconify-icon icon="solar:chart-2-bold-duotone"
                              style="font-size:1.4rem; color:{{ $totalSaldo < 0 ? '#dc2626' : '#16a34a' }};"></iconify-icon>
            </div>
            <div>
                <p style="font-size:.7rem; font-weight:600; text-transform:uppercase;
                           letter-spacing:.06em; color:#9ca3af; margin:0 0 4px;">Saldo do período</p>
                <p style="font-size:1.25rem; font-weight:700; margin:0;
                          color:{{ $totalSaldo < 0 ? '#dc2626' : '#16a34a' }};">
                    {{ $brl($totalSaldo) }}
                </p>
            </div>
        </div>

    </div>

    {{-- ── Botão expandir detalhes ────────────────────────────────── --}}
    <div style="margin-bottom:10px;">
        <button id="btn-detalhes" onclick="toggleDetalhes()"
                style="background:none; border:1px solid #e8ecf0; border-radius:8px;
                       padding:7px 16px; font-size:.825rem; font-weight:600; color:#6b7280;
                       cursor:pointer; display:inline-flex; align-items:center; gap:6px;
                       background:#fff; box-shadow:0 1px 3px rgba(0,0,0,.05);">
            <iconify-icon id="btn-detalhes-icon" icon="solar:alt-arrow-up-bold-duotone"
                          style="font-size:1rem; transition:transform .2s;"></iconify-icon>
            <span id="btn-detalhes-label">Ocultar detalhes</span>
        </button>
    </div>

    {{-- ── Cards: PIX / Cartão / Físico / Devolução ───────────────── --}}
    <div id="cards-detalhes" style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:20px;">

        <div style="flex:1; min-width:140px; background:#fff; border:1px solid #e8ecf0;
                    border-radius:14px; padding:14px 18px;
                    box-shadow:0 1px 4px rgba(0,0,0,.05); display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; flex-shrink:0;
                        background:#f0fdf4; display:flex; align-items:center; justify-content:center;">
                <iconify-icon icon="solar:qr-code-bold-duotone"
                              style="font-size:1.2rem; color:#16a34a;"></iconify-icon>
            </div>
            <div>
                <p style="font-size:.68rem; font-weight:600; text-transform:uppercase;
                           letter-spacing:.06em; color:#9ca3af; margin:0 0 3px;">Total PIX</p>
                <p style="font-size:1rem; font-weight:700; color:#16a34a; margin:0;">
                    {{ $brl($totalPix) }}
                </p>
            </div>
        </div>

        <div style="flex:1; min-width:140px; background:#fff; border:1px solid #e8ecf0;
                    border-radius:14px; padding:14px 18px;
                    box-shadow:0 1px 4px rgba(0,0,0,.05); display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; flex-shrink:0;
                        background:#eff6ff; display:flex; align-items:center; justify-content:center;">
                <iconify-icon icon="solar:card-bold-duotone"
                              style="font-size:1.2rem; color:#2563eb;"></iconify-icon>
            </div>
            <div>
                <p style="font-size:.68rem; font-weight:600; text-transform:uppercase;
                           letter-spacing:.06em; color:#9ca3af; margin:0 0 3px;">Total cartão</p>
                <p style="font-size:1rem; font-weight:700; color:#2563eb; margin:0;">
                    {{ $brl($totalCartao) }}
                </p>
            </div>
        </div>

        <div style="flex:1; min-width:140px; background:#fff; border:1px solid #e8ecf0;
                    border-radius:14px; padding:14px 18px;
                    box-shadow:0 1px 4px rgba(0,0,0,.05); display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; flex-shrink:0;
                        background:#fefce8; display:flex; align-items:center; justify-content:center;">
                <iconify-icon icon="solar:banknote-bold-duotone"
                              style="font-size:1.2rem; color:#ca8a04;"></iconify-icon>
            </div>
            <div>
                <p style="font-size:.68rem; font-weight:600; text-transform:uppercase;
                           letter-spacing:.06em; color:#9ca3af; margin:0 0 3px;">Total físico</p>
                <p style="font-size:1rem; font-weight:700; color:#ca8a04; margin:0;">
                    {{ $brl($totalDinheiro) }}
                </p>
            </div>
        </div>

        <div style="flex:1; min-width:140px; background:#fff; border:1px solid #e8ecf0;
                    border-radius:14px; padding:14px 18px;
                    box-shadow:0 1px 4px rgba(0,0,0,.05); display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; flex-shrink:0;
                        background:#fef2f2; display:flex; align-items:center; justify-content:center;">
                <iconify-icon icon="solar:arrow-left-down-bold-duotone"
                              style="font-size:1.2rem; color:#dc2626;"></iconify-icon>
            </div>
            <div>
                <p style="font-size:.68rem; font-weight:600; text-transform:uppercase;
                           letter-spacing:.06em; color:#9ca3af; margin:0 0 3px;">Devoluções</p>
                <p style="font-size:1rem; font-weight:700; color:#dc2626; margin:0;">
                    {{ $brl($totalDevolucao) }}
                </p>
            </div>
        </div>

    </div>{{-- /cards-detalhes --}}

    {{-- Forms ocultos para reset --}}
    <form id="formResetUnico" method="POST"
          action="{{ route('clientes-maquinas-reset-parcial') }}" style="display:none">
        @csrf
        <input type="hidden" name="id_maquina" id="resetUnicoIdMaquina" value="{{ $idMaquinaReset }}">
    </form>

    <form id="formResetTodas" method="POST"
          action="{{ route('clientes-maquinas-reset-parcial-todas') }}" style="display:none">
        @csrf
        @foreach($idsMaquinas as $idMaq)
            <input type="hidden" name="ids_maquinas[]" value="{{ $idMaq }}">
        @endforeach
    </form>

    {{-- ── Tabela de extrato ────────────────────────────────────── --}}
    <div style="background:#fff; border:1px solid #e8ecf0; border-radius:14px; padding:20px;
                box-shadow:0 1px 4px rgba(0,0,0,.05);">
        <table id="tabela_maquinas_transacao" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>Local</th>
                    <th>Máquina</th>
                    <th>Valor</th>
                    <th>Fonte</th>
                    <th>Data e Hora</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resultado as $tx)
                    @php
                        $isCredito = ($tx['extrato_operacao'] ?? 'C') === 'C';
                        $valor     = $tx['extrato_operacao_valor'] ?? 0;
                    @endphp
                    <tr>
                        <td>{{ $tx['local_nome'] ?? '—' }}</td>
                        <td>{{ $tx['maquina_nome'] ?? '—' }}</td>
                        <td>
                            <span style="font-weight:700;
                                         color:{{ $isCredito ? '#16a34a' : '#ef4444' }};
                                         white-space:nowrap;">
                                {{ $isCredito ? '+' : '−' }} R$ {{ number_format($valor, 2, ',', '.') }}
                            </span>
                        </td>
                        <td>{{ $tx['extrato_operacao_tipo'] ?? '—' }}</td>
                        <td>{{ date('d/m/Y H:i:s', strtotime($tx['data_criacao'])) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Local</th>
                    <th>Máquina</th>
                    <th>Valor</th>
                    <th>Fonte</th>
                    <th>Data e Hora</th>
                </tr>
            </tfoot>
        </table>
    </div>

</div>

@endsection

@section('scriptTable')
<script>
$(document).ready(function () {

    var $filtroMaquina = $('#filtro-maquina-transacoes');
    if ($filtroMaquina.length) {
        $filtroMaquina.select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Todas as máquinas',
            allowClear: true,
            minimumResultsForSearch: 0,
            language: {
                noResults: function () { return 'Nenhuma máquina encontrada'; },
                searching: function () { return 'Pesquisando...'; }
            }
        });

        $filtroMaquina.on('change', function () {
            $(this).closest('form').submit();
        });
    }

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: '{{ session("success") }}',
            timer: 3000,
            showConfirmButton: false,
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Erro',
            text: '{{ session("error") }}',
        });
    @endif

    window.toggleDetalhes = function () {
        var $cards  = $('#cards-detalhes');
        var $icon   = $('#btn-detalhes-icon');
        var $label  = $('#btn-detalhes-label');
        var aberto  = $cards.is(':visible');

        if (aberto) {
            $cards.hide();
            $icon.attr('icon', 'solar:alt-arrow-down-bold-duotone');
            $label.text('Ver mais informações');
        } else {
            $cards.css('display', 'flex');
            $icon.attr('icon', 'solar:alt-arrow-up-bold-duotone');
            $label.text('Ocultar detalhes');
        }
    };

    $('#tabela_maquinas_transacao').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' },
        order: [[4, 'desc']],
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100]
    });

    $('#btn-reset-todas').on('click', function () {
        if ($(this).prop('disabled')) {
            return;
        }

        var totalAcumulado = @json($brl($totalAcumulado));
        var totalSaldo     = @json($brl($totalSaldo));
        var qtdMaquinas    = {{ $qtdMaquinas }};
        var resetUnico     = {{ $resetUnico ? 'true' : 'false' }};
        var maquinaNome    = @json($maquinaNomeSel ?? null);

        if (qtdMaquinas === 0) {
            Swal.fire({
                icon: 'info',
                title: 'Sem máquinas',
                text: 'Não há máquinas disponíveis para realizar o reset.',
            });
            return;
        }

        var detalhesHtml = '';
        if (resetUnico && maquinaNome) {
            detalhesHtml =
                '<p class="mb-1">Máquina: <strong>' + $('<div>').text(maquinaNome).html() + '</strong></p>';
        } else {
            detalhesHtml =
                '<p class="mb-2">Máquinas afetadas: <strong>' + qtdMaquinas + '</strong></p>';
        }

        Swal.fire({
            icon: 'warning',
            title: 'Confirmar Reset Parcial?',
            html:
                detalhesHtml +
                '<p class="mb-1">Total acumulado: <strong>' + totalAcumulado + '</strong></p>' +
                '<p class="mb-2">Saldo do período: <strong>' + totalSaldo + '</strong></p>' +
                '<p class="text-muted small">O contador principal <strong>não será alterado</strong>.<br>' +
                'Apenas o saldo do período será reiniciado.</p>',
            showCancelButton: true,
            confirmButtonText: 'Confirmar Reset',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#fbbf24',
            cancelButtonColor: '#6c757d',
            reverseButtons: true,
        }).then(function (result) {
            if (!result.isConfirmed) return;
            if (resetUnico) {
                $('#formResetUnico').submit();
            } else {
                $('#formResetTodas').submit();
            }
        });
    });

});
</script>
@endsection

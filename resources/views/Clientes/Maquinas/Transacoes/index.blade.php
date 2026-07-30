@extends('layouts.Clientes.app')
@section('title', 'Minhas Máquinas -> Extrato')
@section('content')

<style>
/* ── Extrato: CSS variables para dark mode ──────────────────────── */
:root {
    --ex-bg:          #fff;
    --ex-border:      #e8ecf0;
    --ex-shadow:      0 1px 4px rgba(0,0,0,.05);
    --ex-text-h:      #111827;
    --ex-text-sub:    #6b7280;
    --ex-text-lbl:    #374151;
    --ex-ic-blue:     #e0f2fe;
    --ex-ic-saldo-p:  #dcfce7;
    --ex-ic-saldo-n:  #fee2e2;
    --ex-ic-pix:      #f0fdf4;
    --ex-ic-cartao:   #eff6ff;
    --ex-ic-dinheiro: #fefce8;
    --ex-ic-dev:      #fef2f2;
}
[data-theme="dark"] {
    --ex-bg:          #1e2844;
    --ex-border:      #2a3349;
    --ex-shadow:      0 1px 4px rgba(0,0,0,.3);
    --ex-text-h:      #e8ecf0;
    --ex-text-sub:    #94a3b8;
    --ex-text-lbl:    #94a3b8;
    --ex-ic-blue:     #0c2a3a;
    --ex-ic-saldo-p:  #052e16;
    --ex-ic-saldo-n:  #2d0a0a;
    --ex-ic-pix:      #052e16;
    --ex-ic-cartao:   #0f1f3d;
    --ex-ic-dinheiro: #2d1f00;
    --ex-ic-dev:      #2d0a0a;
}
</style>

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
    $saldoIcVar     = $totalSaldo < 0 ? 'var(--ex-ic-saldo-n)' : 'var(--ex-ic-saldo-p)';
    $saldoColor     = $totalSaldo < 0 ? '#dc2626' : '#16a34a';
@endphp

{{-- ── Cabeçalho com título + botão de reset ─────────────────────── --}}
<div style="display:flex; align-items:center; justify-content:space-between;
            flex-wrap:wrap; gap:12px; margin:16px 24px 20px;">
    <div>
        <h1 style="margin:0; font-size:1.5rem; font-weight:700; color:var(--ex-text-h, #111827);">Extrato</h1>
        <p style="margin:4px 0 0; color:var(--ex-text-sub, #6b7280); font-size:.875rem;">
            Histórico completo de movimentações das suas máquinas
        </p>
    </div>
    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
        <form action="{{ route('cliente-relatorio-xlsx-download') }}" method="post" id="form-extrato-export" style="margin:0;">
            @csrf
            <input type="hidden" name="tipo_csv" value="extrato_filtrado">
            <input type="hidden" name="data" value="{{ json_encode($resultado) }}">
            <button type="submit" id="btn-exportar-extrato"
                    @if(empty($resultado)) disabled @endif
                    style="background:{{ empty($resultado) ? '#e5e7eb' : 'var(--bs-primary, #2C9BA5)' }};
                           border:none; border-radius:10px; padding:12px 20px; font-weight:700;
                           font-size:.9rem; color:{{ empty($resultado) ? '#9ca3af' : '#fff' }};
                           cursor:{{ empty($resultado) ? 'not-allowed' : 'pointer' }};
                           display:flex; align-items:center; gap:8px; white-space:nowrap;">
                <iconify-icon icon="solar:file-download-bold-duotone" style="font-size:1.1rem;"></iconify-icon>
                Gerar Arquivo
            </button>
        </form>
        <button id="btn-reset-todas"
            @if($qtdMaquinas === 0) disabled @endif
            style="background:{{ $qtdMaquinas === 0 ? '#e5e7eb' : '#fbbf24' }}; border:none; border-radius:10px;
                   padding:12px 24px; font-weight:700; font-size:.9rem;
                   color:{{ $qtdMaquinas === 0 ? '#9ca3af' : '#1a1a1a' }};
                   cursor:{{ $qtdMaquinas === 0 ? 'not-allowed' : 'pointer' }};
                   display:flex; align-items:center; gap:8px;
                   box-shadow:0 1px 4px rgba(0,0,0,.12); white-space:nowrap;">
            <iconify-icon icon="solar:restart-bold-duotone" style="font-size:1.1rem;"></iconify-icon>
            Reset
        </button>
    </div>
</div>

<div class="content-body" style="padding-top:0;">

    {{-- ── Filtros ──────────────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('clientes-maquinas-transacoes') }}"
          style="margin-bottom:16px; display:flex; align-items:flex-end; gap:10px; flex-wrap:wrap;">
        @if(count($listaMaquinas) > 1)
        <div style="min-width:280px; max-width:400px; flex:1;">
            <label style="font-size:.825rem; font-weight:600; color:var(--ex-text-lbl, #374151); display:block; margin-bottom:4px;">
                <iconify-icon icon="solar:filter-bold-duotone" style="vertical-align:middle; margin-right:4px;"></iconify-icon>
                Máquina
            </label>
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
        @endif

        <div>
            <label for="filtro-data-inicio" style="font-size:.825rem; font-weight:600; color:var(--ex-text-lbl, #374151); display:block; margin-bottom:4px;">
                Data início
            </label>
            <input type="date" name="data_inicio" id="filtro-data-inicio" class="form-control"
                   value="{{ $dataInicio ?? '' }}">
        </div>

        <div>
            <label for="filtro-data-fim" style="font-size:.825rem; font-weight:600; color:var(--ex-text-lbl, #374151); display:block; margin-bottom:4px;">
                Data fim
            </label>
            <input type="date" name="data_fim" id="filtro-data-fim" class="form-control"
                   value="{{ $dataFim ?? '' }}">
        </div>

        <div>
            <label for="filtro-tipo-operacao" style="font-size:.825rem; font-weight:600; color:var(--ex-text-lbl, #374151); display:block; margin-bottom:4px;">
                Forma de pagamento
            </label>
            <select name="tipo_operacao" id="filtro-tipo-operacao" class="form-control">
                <option value="">Todos os tipos</option>
                <option value="pix" {{ ($tipoOperacao ?? '') === 'pix' ? 'selected' : '' }}>PIX</option>
                <option value="cartao" {{ ($tipoOperacao ?? '') === 'cartao' ? 'selected' : '' }}>Cartão</option>
                <option value="dinheiro" {{ ($tipoOperacao ?? '') === 'dinheiro' ? 'selected' : '' }}>Dinheiro</option>
            </select>
        </div>

        <div style="display:flex; gap:8px; align-items:flex-end; flex-wrap:wrap;">
            <button type="submit" class="btn btn-primary btn-sm">
                <iconify-icon icon="solar:filter-bold-duotone"></iconify-icon>
                Filtrar
            </button>
            @if($idMaquinaSel || !empty($dataInicio) || !empty($dataFim) || !empty($tipoOperacao))
                <a href="{{ route('clientes-maquinas-transacoes') }}"
                   class="btn btn-outline-secondary btn-sm">
                    Limpar
                </a>
            @endif
            <label class="taxa-toggle-label mb-0" title="Incluir transações de taxa no extrato">
                <span class="taxa-toggle-track">
                    <input type="checkbox" class="taxa-toggle-input" id="toggle-taxas-cb"
                           {{ ($mostrarTaxas ?? false) ? 'checked' : '' }}>
                    <span class="taxa-toggle-thumb"></span>
                </span>
                <span>Exibir taxa</span>
            </label>
        </div>

        <input type="hidden" name="mostrar_taxas" id="input-mostrar-taxas"
               value="{{ ($mostrarTaxas ?? false) ? '1' : '0' }}">
    </form>

    {{-- ── Cards: Acumulado + Saldo (só sem filtro de pagamento) ────── --}}
    @if(empty($tipoOperacao))
    <div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:12px;">

        <div style="flex:1; min-width:200px; background:var(--ex-bg, #fff); border:1px solid var(--ex-border, #e8ecf0);
                    border-radius:14px; padding:16px 20px;
                    box-shadow:var(--ex-shadow); display:flex; align-items:center; gap:14px;">
            <div style="width:46px; height:46px; border-radius:12px; flex-shrink:0;
                        background:var(--ex-ic-blue, #e0f2fe); display:flex; align-items:center; justify-content:center;">
                <iconify-icon icon="solar:wallet-money-bold-duotone"
                              style="font-size:1.4rem; color:#0284c7;"></iconify-icon>
            </div>
            <div>
                <p style="font-size:.7rem; font-weight:600; text-transform:uppercase;
                           letter-spacing:.06em; color:var(--ex-text-h, #111827); margin:0 0 4px;">Total acumulado</p>
                <p style="font-size:1.25rem; font-weight:700; color:#16a34a; margin:0;">
                    {{ $brl($totalAcumulado) }}
                </p>
            </div>
        </div>

        <div style="flex:1; min-width:200px; background:var(--ex-bg, #fff); border:1px solid var(--ex-border, #e8ecf0);
                    border-radius:14px; padding:16px 20px;
                    box-shadow:var(--ex-shadow); display:flex; align-items:center; gap:14px;">
            <div style="width:46px; height:46px; border-radius:12px; flex-shrink:0;
                        background:{{ $saldoIcVar }};
                        display:flex; align-items:center; justify-content:center;">
                <iconify-icon icon="solar:chart-2-bold-duotone"
                              style="font-size:1.4rem; color:{{ $saldoColor }};"></iconify-icon>
            </div>
            <div>
                <p style="font-size:.7rem; font-weight:600; text-transform:uppercase;
                           letter-spacing:.06em; color:var(--ex-text-h, #111827); margin:0 0 4px;">Saldo do período</p>
                <p style="font-size:1.25rem; font-weight:700; margin:0; color:{{ $saldoColor }};">
                    {{ $brl($totalSaldo) }}
                </p>
            </div>
        </div>

    </div>

    {{-- ── Botão expandir detalhes ────────────────────────────────── --}}
    <div style="margin-bottom:10px;">
        <button id="btn-detalhes" onclick="toggleDetalhes()"
                style="background:var(--ex-bg, #fff); border:1px solid var(--ex-border, #e8ecf0); border-radius:8px;
                       padding:7px 16px; font-size:.825rem; font-weight:600; color:var(--ex-text-sub, #6b7280);
                       cursor:pointer; display:inline-flex; align-items:center; gap:6px;
                       box-shadow:0 1px 3px rgba(0,0,0,.05);">
            <iconify-icon id="btn-detalhes-icon" icon="solar:alt-arrow-up-bold-duotone"
                          style="font-size:1rem; transition:transform .2s;"></iconify-icon>
            <span id="btn-detalhes-label">Ocultar detalhes</span>
        </button>
    </div>

    {{-- ── Cards: PIX / Cartão / Dinheiro / Devolução ───────────────── --}}
    <div id="cards-detalhes" style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:20px;">

        <div style="flex:1; min-width:140px; background:var(--ex-bg, #fff); border:1px solid var(--ex-border, #e8ecf0);
                    border-radius:14px; padding:14px 18px;
                    box-shadow:var(--ex-shadow); display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; flex-shrink:0;
                        background:var(--ex-ic-pix, #f0fdf4); display:flex; align-items:center; justify-content:center;">
                <iconify-icon icon="solar:qr-code-bold-duotone"
                              style="font-size:1.2rem; color:#16a34a;"></iconify-icon>
            </div>
            <div>
                <p style="font-size:.68rem; font-weight:600; text-transform:uppercase;
                           letter-spacing:.06em; color:var(--ex-text-h, #111827); margin:0 0 3px;">Total PIX</p>
                <p style="font-size:1.1rem; font-weight:700; color:#16a34a; margin:0;">
                    {{ $brl($totalPix) }}
                </p>
            </div>
        </div>

        <div style="flex:1; min-width:140px; background:var(--ex-bg, #fff); border:1px solid var(--ex-border, #e8ecf0);
                    border-radius:14px; padding:14px 18px;
                    box-shadow:var(--ex-shadow); display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; flex-shrink:0;
                        background:var(--ex-ic-cartao, #eff6ff); display:flex; align-items:center; justify-content:center;">
                <iconify-icon icon="solar:card-bold-duotone"
                              style="font-size:1.2rem; color:#2563eb;"></iconify-icon>
            </div>
            <div>
                <p style="font-size:.68rem; font-weight:600; text-transform:uppercase;
                           letter-spacing:.06em; color:var(--ex-text-h, #111827); margin:0 0 3px;">Total cartão</p>
                <p style="font-size:1.1rem; font-weight:700; color:#2563eb; margin:0;">
                    {{ $brl($totalCartao) }}
                </p>
            </div>
        </div>

        <div style="flex:1; min-width:140px; background:var(--ex-bg, #fff); border:1px solid var(--ex-border, #e8ecf0);
                    border-radius:14px; padding:14px 18px;
                    box-shadow:var(--ex-shadow); display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; flex-shrink:0;
                        background:var(--ex-ic-dinheiro, #fefce8); display:flex; align-items:center; justify-content:center;">
                <iconify-icon icon="solar:banknote-bold-duotone"
                              style="font-size:1.2rem; color:#ca8a04;"></iconify-icon>
            </div>
            <div>
                <p style="font-size:.68rem; font-weight:600; text-transform:uppercase;
                           letter-spacing:.06em; color:var(--ex-text-h, #111827); margin:0 0 3px;">Total dinheiro</p>
                <p style="font-size:1.1rem; font-weight:700; color:#ca8a04; margin:0;">
                    {{ $brl($totalDinheiro) }}
                </p>
            </div>
        </div>

        <div style="flex:1; min-width:140px; background:var(--ex-bg, #fff); border:1px solid var(--ex-border, #e8ecf0);
                    border-radius:14px; padding:14px 18px;
                    box-shadow:var(--ex-shadow); display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; flex-shrink:0;
                        background:var(--ex-ic-dev, #fef2f2); display:flex; align-items:center; justify-content:center;">
                <iconify-icon icon="solar:arrow-left-down-bold-duotone"
                              style="font-size:1.2rem; color:#dc2626;"></iconify-icon>
            </div>
            <div>
                <p style="font-size:.68rem; font-weight:600; text-transform:uppercase;
                           letter-spacing:.06em; color:var(--ex-text-h, #111827); margin:0 0 3px;">Devoluções</p>
                <p style="font-size:1.1rem; font-weight:700; color:#dc2626; margin:0;">
                    {{ $brl($totalDevolucao) }}
                </p>
            </div>
        </div>

    </div>{{-- /cards-detalhes --}}
    @else
    {{-- ── Card único para o tipo de pagamento filtrado ────────────── --}}
    <div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:20px;">
        @if($tipoOperacao === 'pix')
        <div style="flex:1; min-width:140px; background:var(--ex-bg, #fff); border:1px solid var(--ex-border, #e8ecf0);
                    border-radius:14px; padding:14px 18px;
                    box-shadow:var(--ex-shadow); display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; flex-shrink:0;
                        background:var(--ex-ic-pix, #f0fdf4); display:flex; align-items:center; justify-content:center;">
                <iconify-icon icon="solar:qr-code-bold-duotone"
                              style="font-size:1.2rem; color:#16a34a;"></iconify-icon>
            </div>
            <div>
                <p style="font-size:.68rem; font-weight:600; text-transform:uppercase;
                           letter-spacing:.06em; color:var(--ex-text-h, #111827); margin:0 0 3px;">Total PIX</p>
                <p style="font-size:1.1rem; font-weight:700; color:#16a34a; margin:0;">
                    {{ $brl($totalPix) }}
                </p>
            </div>
        </div>
        @elseif($tipoOperacao === 'cartao')
        <div style="flex:1; min-width:140px; background:var(--ex-bg, #fff); border:1px solid var(--ex-border, #e8ecf0);
                    border-radius:14px; padding:14px 18px;
                    box-shadow:var(--ex-shadow); display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; flex-shrink:0;
                        background:var(--ex-ic-cartao, #eff6ff); display:flex; align-items:center; justify-content:center;">
                <iconify-icon icon="solar:card-bold-duotone"
                              style="font-size:1.2rem; color:#2563eb;"></iconify-icon>
            </div>
            <div>
                <p style="font-size:.68rem; font-weight:600; text-transform:uppercase;
                           letter-spacing:.06em; color:var(--ex-text-h, #111827); margin:0 0 3px;">Total cartão</p>
                <p style="font-size:1.1rem; font-weight:700; color:#2563eb; margin:0;">
                    {{ $brl($totalCartao) }}
                </p>
            </div>
        </div>
        @elseif($tipoOperacao === 'dinheiro')
        <div style="flex:1; min-width:140px; background:var(--ex-bg, #fff); border:1px solid var(--ex-border, #e8ecf0);
                    border-radius:14px; padding:14px 18px;
                    box-shadow:var(--ex-shadow); display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; flex-shrink:0;
                        background:var(--ex-ic-dinheiro, #fefce8); display:flex; align-items:center; justify-content:center;">
                <iconify-icon icon="solar:banknote-bold-duotone"
                              style="font-size:1.2rem; color:#ca8a04;"></iconify-icon>
            </div>
            <div>
                <p style="font-size:.68rem; font-weight:600; text-transform:uppercase;
                           letter-spacing:.06em; color:var(--ex-text-h, #111827); margin:0 0 3px;">Total dinheiro</p>
                <p style="font-size:1.1rem; font-weight:700; color:#ca8a04; margin:0;">
                    {{ $brl($totalDinheiro) }}
                </p>
            </div>
        </div>
        @endif
    </div>
    @endif

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
    <div style="background:var(--ex-bg, #fff); border:1px solid var(--ex-border, #e8ecf0); border-radius:14px; padding:20px;
                box-shadow:var(--ex-shadow);">
        <table id="tabela_maquinas_transacao" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>Local</th>
                    <th>Máquina</th>
                    <th>Valor</th>
                    <th>Forma de pagamento</th>
                    <th>Data e Hora</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
                <tr>
                    <th>Local</th>
                    <th>Máquina</th>
                    <th>Valor</th>
                    <th>Forma de pagamento</th>
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

    $('#toggle-taxas-cb').on('change', function () {
        $('#input-mostrar-taxas').val(this.checked ? '1' : '0');
        $(this).closest('form').submit();
    });

    $('#tabela_maquinas_transacao').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' },
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('clientes-maquinas-transacoes-dados') }}',
            data: function (d) {
                d.id_maquina    = @json($idMaquinaSel ?? '');
                d.data_inicio   = @json($dataInicio ?? '');
                d.data_fim      = @json($dataFim ?? '');
                d.tipo_operacao = @json($tipoOperacao ?? '');
                d.mostrar_taxas = {{ ($mostrarTaxas ?? false) ? 'true' : 'false' }};
            },
            error: function (xhr) {
                if (xhr.status === 401) {
                    window.location.href = '{{ route('login-view') }}';
                }
            }
        },
        columns: [
            { data: 'local_nome', defaultContent: '—' },
            { data: 'maquina_nome', defaultContent: '—' },
            {
                data: 'extrato_operacao_valor',
                render: function (data, type, row) {
                    if (type !== 'display') { return data; }
                    var isCredito = (row.extrato_operacao || 'C') === 'C';
                    var valor = parseFloat(data || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    var cor = isCredito ? '#16a34a' : '#ef4444';
                    return '<span style="font-weight:700; color:' + cor + '; white-space:nowrap;">' +
                           (isCredito ? '+' : '−') + ' R$ ' + valor + '</span>';
                }
            },
            { data: 'extrato_operacao_tipo', defaultContent: '—' },
            { data: 'data_criacao', defaultContent: '—' }
        ],
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
            title: 'Confirmar Reset?',
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

@extends('layouts.app')
@section('title', 'Máquinas → Extrato')
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
    $totalPix       = $resumo['total_pix']       ?? 0;
    $totalCartao    = $resumo['total_cartao']    ?? 0;
    $totalDinheiro  = $resumo['total_dinheiro']  ?? 0;
    $totalDevolucao = $resumo['total_devolucao'] ?? 0;
    $brl = fn($v) => 'R$ ' . number_format($v, 2, ',', '.');
    $saldoIcVar = $totalSaldo < 0 ? 'var(--ex-ic-saldo-n)' : 'var(--ex-ic-saldo-p)';
    $saldoColor = $totalSaldo < 0 ? '#dc2626' : '#16a34a';
@endphp

{{-- ── Cabeçalho ──────────────────────────────────────────────────── --}}
<div style="display:flex; align-items:center; justify-content:space-between;
            flex-wrap:wrap; gap:12px; margin:16px 24px 20px;">
    <div>
        <h1 style="margin:0; font-size:1.5rem; font-weight:700; color:var(--ex-text-h, #111827);">Extrato</h1>
        <p style="margin:4px 0 0; color:var(--ex-text-sub, #6b7280); font-size:.875rem;">
            Histórico completo de movimentações das máquinas
        </p>
    </div>
    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
        <form action="{{ route('relatorio-xlsx-download') }}" method="post" id="form-extrato-export" style="margin:0;">
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
    </div>
</div>

<div class="content-body" style="padding-top:0;">

    {{-- ── Filtros ──────────────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('maquinas-transacoes') }}"
          style="margin-bottom:16px; display:flex; align-items:flex-end; gap:10px; flex-wrap:wrap;">

        <div style="min-width:200px; max-width:320px; flex:1;">
            <label style="font-size:.825rem; font-weight:600; color:var(--ex-text-lbl, #374151); display:block; margin-bottom:4px;">
                <iconify-icon icon="solar:filter-bold-duotone" style="vertical-align:middle; margin-right:4px;"></iconify-icon>
                Cliente
            </label>
            <select name="id_cliente" id="filtro-cliente" class="select-filtro-cliente form-control">
                <option value="">Todos os clientes</option>
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente['id_cliente'] }}"
                        {{ (string)($idClienteSel ?? '') === (string)$cliente['id_cliente'] ? 'selected' : '' }}>
                        {{ $cliente['cliente_nome'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="min-width:200px; max-width:320px; flex:1;">
            <label style="font-size:.825rem; font-weight:600; color:var(--ex-text-lbl, #374151); display:block; margin-bottom:4px;">
                Local
            </label>
            <select name="id_local" id="filtro-local" class="select-filtro-local form-control">
                <option value="">Todos os locais</option>
                @foreach($locais as $local)
                    <option value="{{ $local['id_local'] }}"
                        {{ (string)($idLocalSel ?? '') === (string)$local['id_local'] ? 'selected' : '' }}>
                        {{ $local['local_nome'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="min-width:200px; max-width:320px; flex:1;">
            <label style="font-size:.825rem; font-weight:600; color:var(--ex-text-lbl, #374151); display:block; margin-bottom:4px;">
                Máquina
            </label>
            <select name="id_maquina" id="filtro-maquina" class="select-filtro-maquina form-control">
                <option value="">Todas as máquinas</option>
                @foreach($maquinas as $maq)
                    <option value="{{ $maq['id_maquina'] }}"
                        {{ (string)($idMaquinaSel ?? '') === (string)$maq['id_maquina'] ? 'selected' : '' }}>
                        {{ $maq['maquina_nome'] }}
                    </option>
                @endforeach
            </select>
        </div>

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
            @if($idMaquinaSel || $idClienteSel || $idLocalSel || !empty($dataInicio) || !empty($dataFim) || !empty($tipoOperacao))
                <a href="{{ route('maquinas-transacoes') }}"
                   class="btn btn-outline-secondary btn-sm">
                    Limpar
                </a>
            @endif
            <label class="taxa-toggle-label mb-0" title="Incluir transações de taxa no extrato">
                <span class="taxa-toggle-track">
                    <input type="checkbox" class="taxa-toggle-input" id="toggle-taxas-cb"
                           name="mostrar_taxas" value="1"
                           {{ ($mostrarTaxas ?? false) ? 'checked' : '' }}>
                    <span class="taxa-toggle-thumb"></span>
                </span>
                <span>Exibir taxa</span>
            </label>
        </div>
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
                <p style="font-size:1.25rem; font-weight:700; color:#0284c7; margin:0;">
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
                <p style="font-size:1rem; font-weight:700; color:var(--ex-text-h, #111827); margin:0;">
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
                <p style="font-size:1rem; font-weight:700; color:var(--ex-text-h, #111827); margin:0;">
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
                <p style="font-size:1rem; font-weight:700; color:var(--ex-text-h, #111827); margin:0;">
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
                <p style="font-size:1rem; font-weight:700; color:var(--ex-text-h, #111827); margin:0;">
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
                <p style="font-size:1rem; font-weight:700; color:var(--ex-text-h, #111827); margin:0;">
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
                <p style="font-size:1rem; font-weight:700; color:var(--ex-text-h, #111827); margin:0;">
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
                <p style="font-size:1rem; font-weight:700; color:var(--ex-text-h, #111827); margin:0;">
                    {{ $brl($totalDinheiro) }}
                </p>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- ── Tabela de extrato ────────────────────────────────────────── --}}
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
                        <td>{{ $tx['data_criacao'] ?? '—' }}</td>
                    </tr>
                @endforeach
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

    $('.select-filtro-cliente').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Todos os clientes',
        allowClear: true,
    });
    $('.select-filtro-local').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Todos os locais',
        allowClear: true,
    });
    $('.select-filtro-maquina').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Todas as máquinas',
        allowClear: true,
    });

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
        $(this).closest('form').submit();
    });

    $('#tabela_maquinas_transacao').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' },
        order: [[4, 'desc']],
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100]
    });

});
</script>
@endsection

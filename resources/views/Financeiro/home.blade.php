@extends('layouts.Financeiro.app')

@section('title', 'Dashboard Financeiro')

@section('content')

{{-- ── Cabeçalho ── --}}
<div class="d-flex align-items-center gap-2 mb-4">
    <iconify-icon icon="solar:pie-chart-2-bold-duotone"
                  style="font-size:1.6rem; color:var(--financeiro-primary,#1a6b4a);"></iconify-icon>
    <div>
        <h4 class="mb-0 fw-semibold">Dashboard Financeiro</h4>
        <p class="text-muted small mb-0">Visão geral de receitas, despesas e comunicação das máquinas.</p>
    </div>
</div>

{{-- ── Cards de resumo ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="rounded-3 p-2 flex-shrink-0" style="background:#e8f5ee;">
                    <iconify-icon icon="solar:dollar-minimalistic-bold-duotone"
                                  style="font-size:1.6rem;color:#1a6b4a;display:block;"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <div class="text-muted" style="font-size:.7rem; text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Receitas</div>
                    <div class="fw-bold" style="font-size:1.05rem; color:#1a6b4a;">
                        R$ {{ number_format($totalReceitas, 2, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="rounded-3 p-2 flex-shrink-0" style="background:#fef2f2;">
                    <iconify-icon icon="solar:bill-list-bold-duotone"
                                  style="font-size:1.6rem;color:#ef4444;display:block;"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <div class="text-muted" style="font-size:.7rem; text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Despesas</div>
                    <div class="fw-bold" style="font-size:1.05rem; color:#ef4444;">
                        R$ {{ number_format($totalDespesas, 2, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="rounded-3 p-2 flex-shrink-0" style="background:#fffbeb;">
                    <iconify-icon icon="solar:danger-triangle-bold-duotone"
                                  style="font-size:1.6rem;color:#f59e0b;display:block;"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <div class="text-muted" style="font-size:.7rem; text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Inadimplência</div>
                    <div class="fw-bold" style="font-size:1.05rem; color:#f59e0b;">R$ —</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="rounded-3 p-2 flex-shrink-0" style="background:#eff6ff;">
                    <iconify-icon icon="solar:chat-round-dots-bold-duotone"
                                  style="font-size:1.6rem;color:#3b82f6;display:block;"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <div class="text-muted" style="font-size:.7rem; text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Mensagens</div>
                    <div class="fw-bold" style="font-size:1.05rem; color:#3b82f6;">—</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Gráficos ── --}}
<div class="row g-3 mb-4">
    {{-- Gráfico mensal --}}
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3">
                <iconify-icon icon="solar:chart-2-bold-duotone"
                              style="font-size:1.2rem;color:#1a6b4a;"></iconify-icon>
                <span class="fw-semibold" style="font-size:.92rem;">Receitas por Mês</span>
            </div>
            <div class="card-body p-3">
                <div id="chart-mensal" style="min-height:260px;"></div>
            </div>
        </div>
    </div>

    {{-- Gráfico trimestral --}}
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3">
                <iconify-icon icon="solar:pie-chart-bold-duotone"
                              style="font-size:1.2rem;color:#1a6b4a;"></iconify-icon>
                <span class="fw-semibold" style="font-size:.92rem;">Receitas por Trimestre</span>
            </div>
            <div class="card-body p-3">
                <div id="chart-trimestral" style="min-height:260px;"></div>
            </div>
        </div>
    </div>
</div>

{{-- ── Status de comunicação das máquinas ── --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3">
        <iconify-icon icon="solar:wifi-bold-duotone"
                      style="font-size:1.2rem;color:#1a6b4a;"></iconify-icon>
        <span class="fw-semibold" style="font-size:.92rem;">Status de Comunicação</span>
        <span class="badge bg-secondary ms-auto" style="font-size:.72rem;">{{ $maquinas->count() }} máquinas</span>
    </div>

    @if($maquinas->isEmpty())
        <div class="card-body text-center py-5 text-muted">
            <iconify-icon icon="solar:inbox-line-duotone" style="font-size:2.5rem;"></iconify-icon>
            <p class="mt-2 mb-0 small">Nenhuma máquina encontrada.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr style="font-size:.78rem; text-transform:uppercase; letter-spacing:.04em; color:#6b7280;">
                        <th>Máquina</th>
                        <th>Local</th>
                        <th class="text-center">Online</th>
                        <th class="text-center">Pix</th>
                        <th class="text-center">Cartão</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($maquinas as $m)
                    @php
                        $online        = (int)($m['maquina_status'] ?? 1) === 1;
                        $statusPix     = (int)($m['status_pix']    ?? 1) === 1;
                        $statusCartao  = (int)($m['status_cartao'] ?? 1) === 1;
                    @endphp
                    <tr>
                        <td class="fw-semibold" style="font-size:.88rem;">{{ $m['maquina_nome'] }}</td>
                        <td class="text-muted" style="font-size:.82rem;">{{ $m['local_nome'] }}</td>
                        <td class="text-center">
                            @if($online)
                                <span class="badge rounded-pill"
                                      style="background:#dcfce7; color:#16a34a; font-size:.72rem; font-weight:600;">
                                    ● Ativo
                                </span>
                            @else
                                <span class="badge rounded-pill"
                                      style="background:#fee2e2; color:#dc2626; font-size:.72rem; font-weight:600;">
                                    ● Inativo
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($statusPix)
                                <span class="badge rounded-pill"
                                      style="background:#dcfce7; color:#16a34a; font-size:.72rem; font-weight:600;">
                                    ● OK
                                </span>
                            @else
                                <span class="badge rounded-pill"
                                      style="background:#fee2e2; color:#dc2626; font-size:.72rem; font-weight:600;">
                                    ● Falha
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($statusCartao)
                                <span class="badge rounded-pill"
                                      style="background:#dcfce7; color:#16a34a; font-size:.72rem; font-weight:600;">
                                    ● OK
                                </span>
                            @else
                                <span class="badge rounded-pill"
                                      style="background:#fee2e2; color:#dc2626; font-size:.72rem; font-weight:600;">
                                    ● Falha
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection

@endsection

@push('scriptTable')
<script>
(function () {
    'use strict';

    const green  = '#1a6b4a';
    const green2 = '#27ae60';
    const green3 = '#86efac';

    // ── Gráfico Mensal (barras) ──
    const mesesLabels  = @json($mesesLabels);
    const mesesValores = @json($mesesValores);

    if (document.getElementById('chart-mensal')) {
        new ApexCharts(document.getElementById('chart-mensal'), {
            series: [{ name: 'Receitas', data: mesesValores }],
            chart: {
                type: 'bar',
                height: 260,
                toolbar: { show: false },
                fontFamily: 'inherit',
            },
            colors: [green],
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    columnWidth: '60%',
                },
            },
            dataLabels: { enabled: false },
            xaxis: {
                categories: mesesLabels,
                labels: { style: { fontSize: '11px', colors: '#6b7280' } },
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: {
                labels: {
                    formatter: (v) => 'R$ ' + Number(v).toLocaleString('pt-BR', { minimumFractionDigits: 0 }),
                    style: { fontSize: '11px', colors: '#6b7280' },
                },
            },
            grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
            tooltip: {
                y: {
                    formatter: (v) => 'R$ ' + Number(v).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                },
            },
        }).render();
    }

    // ── Gráfico Trimestral (donut) ──
    const trimLabels  = @json($trimestresLabels);
    const trimValores = @json($trimestresValores);

    if (document.getElementById('chart-trimestral') && trimValores.length) {
        new ApexCharts(document.getElementById('chart-trimestral'), {
            series: trimValores,
            labels: trimLabels,
            chart: {
                type: 'donut',
                height: 260,
                toolbar: { show: false },
                fontFamily: 'inherit',
            },
            colors: [green, green2, green3, '#a7f3d0'],
            dataLabels: {
                enabled: true,
                formatter: (val) => val.toFixed(1) + '%',
            },
            legend: {
                position: 'bottom',
                fontSize: '12px',
                labels: { colors: '#374151' },
            },
            tooltip: {
                y: {
                    formatter: (v) => 'R$ ' + Number(v).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                },
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: (w) => {
                                    const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    return 'R$ ' + Number(total).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                },
                            },
                        },
                    },
                },
            },
        }).render();
    } else if (document.getElementById('chart-trimestral') && !trimValores.length) {
        document.getElementById('chart-trimestral').innerHTML =
            '<p class="text-center text-muted py-4 small">Sem dados suficientes para o gráfico trimestral.</p>';
    }
})();
</script>
@endpush

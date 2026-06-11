@php
    $totalMaquinas = count($maquinas_online) + count($maquinas_offline);
    $percOnline    = $totalMaquinas > 0 ? round(count($maquinas_online) / $totalMaquinas * 100) : 0;
    $variacaoSaldo = $saldo['mes_passado'] > 0
        ? round((($saldo['mes_atual'] - $saldo['mes_passado']) / $saldo['mes_passado']) * 100, 1)
        : null;
    $brl = fn($v) => 'R$ ' . number_format($v, 2, ',', '.');
@endphp

<section id="resumo" class="dash-section">

    <div class="dash-section-header">
        <h2>
            <iconify-icon icon="solar:chart-2-bold-duotone" style="color:var(--sp-teal);"></iconify-icon>
            Resumo financeiro
        </h2>
        <p>Faturamento, devoluções e status da sua operação</p>
    </div>

    {{-- KPIs rápidos --}}
    <div class="dash-kpi-grid">
        <!--<div class="dash-kpi">
            <div class="dash-kpi-label">
                <iconify-icon icon="solar:clock-circle-bold-duotone" style="color:var(--sp-teal);"></iconify-icon>
                Saldo hoje
            </div>
            <div class="dash-kpi-value">{{ $brl($saldo['hoje']) }}</div>
            <div class="dash-kpi-sub">referente ao dia de hoje</div>
        </div>
        <div class="dash-kpi">
            <div class="dash-kpi-label">
                <iconify-icon icon="solar:wallet-money-bold-duotone" style="color:var(--sp-navy);"></iconify-icon>
                Saldo este mês
            </div>
            <div class="dash-kpi-value">{{ $brl($saldo['mes_atual']) }}</div>
            @if($variacaoSaldo !== null)
            <div class="dash-kpi-sub" style="color:{{ $variacaoSaldo >= 0 ? '#16a34a' : '#dc2626' }};">
                <iconify-icon icon="{{ $variacaoSaldo >= 0 ? 'solar:arrow-up-bold' : 'solar:arrow-down-bold' }}"></iconify-icon>
                {{ abs($variacaoSaldo) }}% vs. mês passado
            </div>
            @endif
        </div>-->
        <div class="dash-kpi">
            <div class="dash-kpi-label">
                <iconify-icon icon="solar:monitor-bold-duotone" style="color:#16a34a;"></iconify-icon>
                Máquinas online
            </div>
            <div class="dash-kpi-value">
                {{ count($maquinas_online) }}
                <span style="font-size:.8rem; font-weight:500; color:#9ca3af;">/ {{ $totalMaquinas }}</span>
            </div>
            @if($totalMaquinas > 0)
            <div style="margin-top:8px; height:4px; border-radius:99px; background:#fee2e2; overflow:hidden;">
                <div style="height:100%; width:{{ $percOnline }}%; background:#16a34a; border-radius:99px;"></div>
            </div>
            @endif
        </div>
        <div class="dash-kpi">
            <div class="dash-kpi-label">
                <iconify-icon icon="solar:danger-triangle-bold-duotone" style="color:#dc2626;"></iconify-icon>
                Máquinas offline
            </div>
            <div class="dash-kpi-value" style="color:{{ count($maquinas_offline) > 0 ? '#dc2626' : '#111827' }};">
                {{ count($maquinas_offline) }}
            </div>
            <div class="dash-kpi-sub">
                {{ count($maquinas_offline) > 0 ? 'requer atenção' : 'tudo funcionando' }}
            </div>
        </div>
    </div>

    {{-- Cards detalhados --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px,1fr)); gap:16px;">

        {{-- Faturamento --}}
        <div class="dash-card" style="display:flex; flex-direction:column;">
            <div class="dash-card-header">
                <div class="dash-icon dash-icon--navy">
                    <iconify-icon icon="solar:chart-2-bold-duotone" style="font-size:1.1rem;"></iconify-icon>
                </div>
                <div>
                    <h3 style="margin:0; font-size:.92rem; font-weight:700;">Faturamento</h3>
                    <p style="margin:0; font-size:.72rem; color:#9ca3af;">Receitas por período</p>
                </div>
            </div>
            <div class="dash-card-body">
                <div class="dash-row-line">
                    <span style="font-size:.82rem; color:var(--sp-muted);">Hoje</span>
                    <strong>{{ $brl($saldo['hoje']) }}</strong>
                </div>
                <div class="dash-row-line">
                    <span style="font-size:.82rem; color:var(--sp-muted);">Este mês</span>
                    <strong>{{ $brl($saldo['mes_atual']) }}</strong>
                </div>
                <div class="dash-row-line">
                    <span style="font-size:.82rem; color:var(--sp-muted);">Mês passado</span>
                    <span style="color:#9ca3af;">{{ $brl($saldo['mes_passado']) }}</span>
                </div>
            </div>
            <div class="dash-card-footer">
                <a href="#relatorios-totalTransacoes" class="dash-btn-secondary rel-atalho">
                    <iconify-icon icon="solar:document-text-bold-duotone" style="color:var(--sp-teal);"></iconify-icon>
                    Gerar relatório
                </a>
            </div>
        </div>

        {{-- Devoluções --}}
        <div class="dash-card" style="display:flex; flex-direction:column;">
            <div class="dash-card-header">
                <div class="dash-icon dash-icon--warn">
                    <iconify-icon icon="solar:arrow-left-down-bold-duotone" style="font-size:1.1rem;"></iconify-icon>
                </div>
                <div>
                    <h3 style="margin:0; font-size:.92rem; font-weight:700;">Devoluções</h3>
                    <p style="margin:0; font-size:.72rem; color:#9ca3af;">Estornos por período</p>
                </div>
            </div>
            <div class="dash-card-body">
                <div class="dash-row-line">
                    <span style="font-size:.82rem; color:var(--sp-muted);">Hoje</span>
                    <strong>{{ $brl($devolucoes['hoje']) }}</strong>
                </div>
                <div class="dash-row-line">
                    <span style="font-size:.82rem; color:var(--sp-muted);">Este mês</span>
                    <strong>{{ $brl($devolucoes['mes_atual']) }}</strong>
                </div>
                <div class="dash-row-line">
                    <span style="font-size:.82rem; color:var(--sp-muted);">Mês passado</span>
                    <span style="color:#9ca3af;">{{ $brl($devolucoes['mes_passado']) }}</span>
                </div>
            </div>
            <div class="dash-card-footer">
                <a href="#relatorios-totalTransacoes" class="dash-btn-secondary rel-atalho rel-atalho-estorno">
                    <iconify-icon icon="solar:document-text-bold-duotone" style="color:#ca8a04;"></iconify-icon>
                    Gerar relatório
                </a>
            </div>
        </div>

        {{-- Acumulado do período --}}
        <div class="dash-card" style="display:flex; flex-direction:column;">
            <div class="dash-card-header">
                <div class="dash-icon dash-icon--teal">
                    <iconify-icon icon="solar:wallet-money-bold-duotone" style="font-size:1.1rem;"></iconify-icon>
                </div>
                <div>
                    <h3 style="margin:0; font-size:.92rem; font-weight:700;">Acumulado</h3>
                    <p style="margin:0; font-size:.72rem; color:#9ca3af;">Totais do período atual</p>
                </div>
            </div>
            <div class="dash-card-body">
                <div class="dash-row-line">
                    <span style="font-size:.82rem; color:var(--sp-muted);">Total acumulado</span>
                    <strong style="color:var(--sp-teal);">{{ $brl($resumoFinanceiro['total_acumulado']) }}</strong>
                </div>
                <div class="dash-row-line">
                    <span style="font-size:.82rem; color:var(--sp-muted);">Saldo do período</span>
                    <strong style="color:{{ $resumoFinanceiro['total_saldo'] < 0 ? '#dc2626' : '#16a34a' }};">
                        {{ $brl($resumoFinanceiro['total_saldo']) }}
                    </strong>
                </div>
            </div>
            <div class="dash-card-footer">
                <a href="{{ route('clientes-maquinas-transacoes') }}" class="dash-btn-secondary">
                    <iconify-icon icon="solar:chart-bold-duotone" style="color:var(--sp-teal);"></iconify-icon>
                    Ver acumulado completo
                </a>
            </div>
        </div>

    </div>
</section>

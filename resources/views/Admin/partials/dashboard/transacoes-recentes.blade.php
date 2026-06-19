@php
    $brl = fn($v) => 'R$ ' . number_format($v, 2, ',', '.');
@endphp

<section id="transacoes" class="dash-section">

    <div class="dash-section-header" style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <div>
            <h2>
                <iconify-icon icon="solar:transfer-horizontal-bold-duotone" style="color:var(--sp-teal);"></iconify-icon>
                Extrato recente
            </h2>
            <p>Últimas movimentações e totais por forma de pagamento</p>
        </div>
        <a href="{{ route('maquinas-transacoes', $idMaquinaFiltro ? ['id_maquina' => $idMaquinaFiltro] : []) }}"
           class="dash-btn-primary" style="font-size:.78rem; padding:8px 16px;">
            <iconify-icon icon="solar:arrow-right-bold-duotone"></iconify-icon>
            Ver todas
        </a>
    </div>

    @if(count($listaMaquinas) > 1)
    <form method="GET" action="{{ route('home') }}#transacoes"
          style="margin-bottom:16px; display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
        <label style="font-size:.8rem; font-weight:600; color:#374151; white-space:nowrap;">
            <iconify-icon icon="solar:filter-bold-duotone" style="vertical-align:middle;"></iconify-icon>
            Filtrar máquina:
        </label>
        <select name="id_maquina" class="dash-filter-select" onchange="this.form.submit()">
            <option value="">Todas as máquinas</option>
            @foreach($listaMaquinas as $maq)
                <option value="{{ $maq['id_maquina'] }}"
                    {{ (string)$idMaquinaFiltro === (string)$maq['id_maquina'] ? 'selected' : '' }}>
                    {{ $maq['maquina_nome'] }} — {{ $maq['local_nome'] }}
                </option>
            @endforeach
        </select>
        @if($idMaquinaFiltro)
            <a href="{{ route('home') }}#transacoes"
               style="font-size:.78rem; color:var(--sp-muted); text-decoration:none;">✕ Limpar</a>
        @endif
    </form>
    @endif

    <div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:16px;">
        <div class="dash-kpi" style="flex:1; min-width:130px; margin:0;">
            <div class="dash-kpi-label">PIX</div>
            <div class="dash-kpi-value" style="font-size:1rem; color:#16a34a;">{{ $brl($resumoFinanceiro['total_pix']) }}</div>
        </div>
        <div class="dash-kpi" style="flex:1; min-width:130px; margin:0;">
            <div class="dash-kpi-label">Cartão</div>
            <div class="dash-kpi-value" style="font-size:1rem; color:var(--sp-navy);">{{ $brl($resumoFinanceiro['total_cartao']) }}</div>
        </div>
        <div class="dash-kpi" style="flex:1; min-width:130px; margin:0;">
            <div class="dash-kpi-label">Dinheiro</div>
            <div class="dash-kpi-value" style="font-size:1rem; color:#ca8a04;">{{ $brl($resumoFinanceiro['total_dinheiro']) }}</div>
        </div>
        <div class="dash-kpi" style="flex:1; min-width:130px; margin:0;">
            <div class="dash-kpi-label">Devoluções</div>
            <div class="dash-kpi-value" style="font-size:1rem; color:#dc2626;">{{ $brl($resumoFinanceiro['total_devolucao']) }}</div>
        </div>
    </div>

    <div class="dash-card">
        @if(empty($ultimasTransacoes))
            <div style="padding:40px 24px; text-align:center; color:#9ca3af;">
                <iconify-icon icon="solar:inbox-line-bold-duotone" style="font-size:2rem; display:block; margin:0 auto 10px;"></iconify-icon>
                <p style="margin:0; font-size:.85rem;">Nenhum extrato encontrado.</p>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table class="dash-tx-table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Máquina</th>
                            <th>Tipo</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ultimasTransacoes as $tx)
                        @php
                            $isCredito = ($tx['extrato_operacao'] ?? 'C') === 'C';
                            $valor     = $tx['extrato_operacao_valor'] ?? 0;
                        @endphp
                        <tr>
                            <td style="white-space:nowrap;">{{ date('d/m/Y H:i', strtotime($tx['data_criacao'])) }}</td>
                            <td>{{ $tx['maquina_nome'] ?? '—' }}</td>
                            <td>{{ $tx['extrato_operacao_tipo'] ?? '—' }}</td>
                            <td>
                                <span style="font-weight:700; color:{{ $isCredito ? '#16a34a' : '#dc2626' }}; white-space:nowrap;">
                                    {{ $isCredito ? '+' : '−' }} {{ $brl($valor) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="dash-card-footer" style="text-align:center;">
                <a href="{{ route('maquinas-transacoes', $idMaquinaFiltro ? ['id_maquina' => $idMaquinaFiltro] : []) }}"
                   class="dash-btn-secondary" style="max-width:280px; margin:0 auto;">
                    <iconify-icon icon="solar:arrow-right-bold-duotone" style="color:var(--sp-teal);"></iconify-icon>
                    Ver histórico completo
                </a>
            </div>
        @endif
    </div>
</section>

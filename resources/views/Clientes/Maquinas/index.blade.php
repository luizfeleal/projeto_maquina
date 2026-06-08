@extends('layouts.Clientes.app')
@section('title', 'Extrato de Transações')

@php
    // Agrupa as transações por data (dia)
    $grouped = [];
    $totalCredito = 0;
    $totalDebito  = 0;

    foreach ($resultado as $item) {
        $dia = date('Y-m-d', strtotime($item['data_criacao']));
        $grouped[$dia][] = $item;
        if (($item['extrato_operacao'] ?? 'C') === 'C') {
            $totalCredito += $item['extrato_operacao_valor'] ?? 0;
        } else {
            $totalDebito += $item['extrato_operacao_valor'] ?? 0;
        }
    }
    krsort($grouped); // mais recente primeiro

    $hoje    = date('Y-m-d');
    $ontem   = date('Y-m-d', strtotime('-1 day'));

    function diaLabel(string $dia, string $hoje, string $ontem): string {
        if ($dia === $hoje)  return 'Hoje';
        if ($dia === $ontem) return 'Ontem';
        return date('d/m/Y', strtotime($dia));
    }

    function tipoIcon(string $tipo): string {
        return match(strtolower($tipo)) {
            'pix'      => 'solar:transfer-horizontal-bold-duotone',
            'cartão', 'cartao' => 'solar:card-bold-duotone',
            'dinheiro' => 'solar:banknote-2-bold-duotone',
            default    => 'solar:wallet-money-bold-duotone',
        };
    }

    function tipoColor(string $tipo): string {
        return match(strtolower($tipo)) {
            'pix'      => '#2C9BA5',
            'cartão', 'cartao' => '#1E2E5E',
            'dinheiro' => '#16a34a',
            default    => '#6b7280',
        };
    }
@endphp

@section('content')
<div class="page-heading">
    <h1>Extrato de Transações</h1>
    <p>Histórico de movimentações das suas máquinas</p>
</div>

<div class="content-body" style="padding-top: 0;">

    {{-- ── Resumo ─────────────────────────────────────────────── --}}
    <div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:20px;">

        <div style="flex:1; min-width:160px; background:#fff; border:1px solid #e8ecf0;
                    border-radius:14px; padding:18px 20px;
                    box-shadow:0 1px 4px rgba(0,0,0,.05);">
            <p style="font-size:.72rem; font-weight:600; text-transform:uppercase;
                       letter-spacing:.06em; color:#9ca3af; margin:0 0 6px;">Total de entradas</p>
            <p style="font-size:1.35rem; font-weight:700; color:#16a34a; margin:0;">
                + R$ {{ number_format($totalCredito, 2, ',', '.') }}
            </p>
        </div>

        <div style="flex:1; min-width:160px; background:#fff; border:1px solid #e8ecf0;
                    border-radius:14px; padding:18px 20px;
                    box-shadow:0 1px 4px rgba(0,0,0,.05);">
            <p style="font-size:.72rem; font-weight:600; text-transform:uppercase;
                       letter-spacing:.06em; color:#9ca3af; margin:0 0 6px;">Total de transações</p>
            <p style="font-size:1.35rem; font-weight:700; color:#111827; margin:0;">
                {{ count($resultado) }}
            </p>
        </div>

    </div>

    {{-- ── Lista agrupada por dia ──────────────────────────────── --}}
    @if(count($resultado) === 0)
        <div style="background:#fff; border:1px dashed #e8ecf0; border-radius:14px;
                    padding:60px 24px; text-align:center; color:#9ca3af;">
            <iconify-icon icon="solar:inbox-line-duotone" style="font-size:2.5rem; display:block; margin:0 auto 10px;"></iconify-icon>
            <p style="margin:0; font-size:.875rem;">Nenhuma transação encontrada.</p>
        </div>
    @else
        @foreach($grouped as $dia => $itens)
            {{-- Separador de data --}}
            <div style="display:flex; align-items:center; gap:10px; margin:20px 0 10px;">
                <span style="font-size:.75rem; font-weight:700; color:#6b7280;
                             text-transform:uppercase; letter-spacing:.06em; white-space:nowrap;">
                    {{ diaLabel($dia, $hoje, $ontem) }}
                </span>
                <div style="flex:1; height:1px; background:#e8ecf0;"></div>
                <span style="font-size:.72rem; color:#9ca3af; white-space:nowrap;">
                    {{ count($itens) }} {{ count($itens) === 1 ? 'transação' : 'transações' }}
                </span>
            </div>

            {{-- Card do dia --}}
            <div style="background:#fff; border:1px solid #e8ecf0; border-radius:14px;
                        box-shadow:0 1px 4px rgba(0,0,0,.05); overflow:hidden;">
                @foreach($itens as $i => $tx)
                    @php
                        $isCredito = ($tx['extrato_operacao'] ?? 'C') === 'C';
                        $tipo      = $tx['extrato_operacao_tipo'] ?? 'Outro';
                        $status    = $tx['extrato_operacao_status'] ?? 'aprovado';
                        $valor     = $tx['extrato_operacao_valor'] ?? 0;
                        $nome      = $tx['maquina_nome'] ?? '—';
                        $hora      = date('H:i', strtotime($tx['data_criacao']));
                        $icon      = tipoIcon($tipo);
                        $iconColor = tipoColor($tipo);
                        $isLast    = $i === count($itens) - 1;
                    @endphp

                    <div style="display:flex; align-items:center; gap:14px; padding:14px 18px; background:#fff;
                                {{ $isLast ? '' : 'border-bottom:1px solid #f3f4f6;' }}">

                        {{-- Ícone do tipo de pagamento --}}
                        <div style="width:44px; height:44px; border-radius:12px; flex-shrink:0;
                                    background:{{ $iconColor }}18;
                                    display:flex; align-items:center; justify-content:center;">
                            <iconify-icon icon="{{ $icon }}"
                                          style="font-size:22px; color:{{ $iconColor }};"></iconify-icon>
                        </div>

                        {{-- Descrição --}}
                        <div style="flex:1; min-width:0;">
                            <p style="margin:0 0 3px; font-size:.875rem; font-weight:600;
                                      color:#111827; white-space:nowrap; overflow:hidden;
                                      text-overflow:ellipsis;">
                                {{ $nome }}
                            </p>
                            <p style="margin:0; font-size:.75rem; color:#9ca3af;
                                      display:flex; align-items:center; gap:5px; flex-wrap:wrap;">
                                <span>{{ $tipo }}</span>
                                <span style="width:3px; height:3px; background:#e5e7eb; border-radius:50%; display:inline-block; flex-shrink:0;"></span>
                                <span>{{ $hora }}</span>
                                @if(strtolower($status) === 'aprovado')
                                    <span style="width:3px; height:3px; background:#e5e7eb; border-radius:50%; display:inline-block; flex-shrink:0;"></span>
                                    <span style="color:#16a34a; font-weight:600;">✓ Aprovado</span>
                                @elseif(strtolower($status) === 'pendente')
                                    <span style="width:3px; height:3px; background:#e5e7eb; border-radius:50%; display:inline-block; flex-shrink:0;"></span>
                                    <span style="color:#d97706; font-weight:600;">⏳ Pendente</span>
                                @elseif(strtolower($status) === 'cancelado' || strtolower($status) === 'recusado')
                                    <span style="width:3px; height:3px; background:#e5e7eb; border-radius:50%; display:inline-block; flex-shrink:0;"></span>
                                    <span style="color:#ef4444; font-weight:600;">✕ {{ ucfirst($status) }}</span>
                                @endif
                            </p>
                        </div>

                        {{-- Valor em destaque --}}
                        <div style="text-align:right; flex-shrink:0;">
                            <p style="margin:0; font-size:1.05rem; font-weight:700; letter-spacing:-.01em;
                                      color:{{ $isCredito ? '#16a34a' : '#ef4444' }};">
                                {{ $isCredito ? '+' : '−' }}&nbsp;R$&nbsp;{{ number_format($valor, 2, ',', '.') }}
                            </p>
                        </div>

                    </div>
                @endforeach
            </div>
        @endforeach
    @endif

</div>
@endsection

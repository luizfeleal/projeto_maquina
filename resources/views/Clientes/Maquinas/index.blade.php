@extends('layouts.Clientes.app')
@section('title', 'Minhas Máquinas')

@section('content')
<div class="page-heading">
    <h1>Minhas Máquinas</h1>
    <p>Visão geral das suas máquinas e atalhos de ação</p>
</div>

<div class="content-body" style="padding-top:0;">

    @if(empty($maquinas))
        <div style="background:#fff; border:1px dashed #e8ecf0; border-radius:14px;
                    padding:60px 24px; text-align:center; color:#9ca3af;">
            <iconify-icon icon="solar:devices-bold-duotone"
                          style="font-size:2.5rem; display:block; margin:0 auto 12px;"></iconify-icon>
            <p style="margin:0; font-size:.9rem; font-weight:600;">Nenhuma máquina encontrada.</p>
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
                $nome       = $maq['maquina_nome']     ?? 'Máquina';
                $local      = $maq['local_nome']       ?? '—';
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
                                {{ $local }}
                            </p>
                        </div>
                    </div>
                    {{-- Badge de status --}}
                    <span style="flex-shrink:0; font-size:.7rem; font-weight:700; padding:3px 10px;
                                 border-radius:20px; white-space:nowrap; display:flex; align-items:center; gap:5px;
                                 background:{{ $ativo ? '#dcfce7' : '#fee2e2' }};
                                 color:{{ $ativo ? '#16a34a' : '#dc2626' }};">
                        <span style="width:6px; height:6px; border-radius:50%; flex-shrink:0;
                                     background:{{ $ativo ? '#16a34a' : '#dc2626' }};
                                     {{ $ativo ? 'box-shadow:0 0 0 2px #bbf7d0;' : '' }}"></span>
                        {{ $ativo ? 'Online' : 'Offline' }}
                    </span>
                </div>

                {{-- Totais --}}
                <div style="padding:14px 20px; display:flex; gap:16px; border-bottom:1px solid #f3f4f6;">
                    <div style="flex:1; text-align:center;">
                        <p style="margin:0 0 2px; font-size:.65rem; font-weight:600; text-transform:uppercase;
                                   letter-spacing:.06em; color:#9ca3af;">Total acumulado</p>
                        <p style="margin:0; font-size:1rem; font-weight:700; color:#0284c7;">
                            R$ {{ number_format($total, 2, ',', '.') }}
                        </p>
                    </div>
                    <div style="width:1px; background:#f3f4f6;"></div>
                    <div style="flex:1; text-align:center;">
                        <p style="margin:0 0 2px; font-size:.65rem; font-weight:600; text-transform:uppercase;
                                   letter-spacing:.06em; color:#9ca3af;">Saldo do período</p>
                        <p style="margin:0; font-size:1rem; font-weight:700;
                                  color:{{ $saldo < 0 ? '#dc2626' : '#16a34a' }};">
                            R$ {{ number_format($saldo, 2, ',', '.') }}
                        </p>
                    </div>
                    @if($temReset)
                    <div style="width:1px; background:#f3f4f6;"></div>
                    <div style="flex:1; text-align:center;">
                        <p style="margin:0 0 2px; font-size:.65rem; font-weight:600; text-transform:uppercase;
                                   letter-spacing:.06em; color:#9ca3af;">Último reset</p>
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
                        Transações
                    </a>

                    <a href="{{ route('clientes-maquinas-acumulado') }}"
                       style="flex:1; min-width:100px; text-align:center; text-decoration:none;
                              background:#f8fafc; border:1px solid #e8ecf0; border-radius:8px;
                              padding:8px 10px; font-size:.78rem; font-weight:600; color:#374151;
                              display:flex; align-items:center; justify-content:center; gap:5px;">
                        <iconify-icon icon="solar:chart-bold-duotone"
                                      style="font-size:.95rem; color:#16a34a;"></iconify-icon>
                        Acumulado
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

                    <a href="{{ route('clientes-maquinas-editar', ['id_maquina' => $idMaquina]) }}"
                       style="flex:1; min-width:100px; text-align:center; text-decoration:none;
                              background:#f8fafc; border:1px solid #e8ecf0; border-radius:8px;
                              padding:8px 10px; font-size:.78rem; font-weight:600; color:#374151;
                              display:flex; align-items:center; justify-content:center; gap:5px;">
                        <iconify-icon icon="solar:pen-bold-duotone"
                                      style="font-size:.95rem; color:#6366f1;"></iconify-icon>
                        Editar
                    </a>
                </div>

            </div>
            @endforeach
        </div>
    @endif

</div>
@endsection

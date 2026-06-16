<section id="maquinas" class="dash-section">

    <div class="dash-section-header" style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <div>
            <h2>
                <iconify-icon icon="solar:devices-bold-duotone" style="color:var(--sp-teal);"></iconify-icon>
                Máquinas
            </h2>
            <p>Status, saldo e atalhos rápidos por máquina</p>
        </div>
        <a href="{{ route('maquinas') }}" class="dash-btn-primary" style="font-size:.78rem; padding:8px 16px;">
            <iconify-icon icon="solar:arrow-right-bold-duotone"></iconify-icon>
            Ver todas
        </a>
    </div>

    @if(empty($maquinasDashboard))
        <div class="dash-card" style="padding:48px 24px; text-align:center; color:#9ca3af;">
            <iconify-icon icon="solar:devices-bold-duotone" style="font-size:2.5rem; display:block; margin:0 auto 12px;"></iconify-icon>
            <p style="margin:0; font-size:.9rem; font-weight:600;">Nenhuma máquina encontrada.</p>
        </div>
    @else
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px,1fr)); gap:14px;">
            @foreach($maquinasDashboard as $maq)
            @php
                $ativo     = ($maq['maquina_status'] ?? 1) == 1;
                $saldo     = $maq['saldo_periodo'] ?? 0;
                $idMaquina = $maq['id_maquina'];
                $idLocal   = $maq['id_local'] ?? null;
                $possuiQr  = $maq['possui_qr'] ?? false;
                $qrHref    = ($idLocal && $possuiQr)
                    ? route('qr', ['id_local' => $idLocal, 'id_maquina' => $idMaquina, 'abrir' => true])
                    : route('qr-criar');
            @endphp
            <div class="dash-card" style="display:flex; flex-direction:column;">

                <div style="padding:14px 16px 10px; border-bottom:1px solid #f3f4f6;
                            display:flex; align-items:flex-start; justify-content:space-between; gap:8px;">
                    <div style="display:flex; align-items:center; gap:10px; min-width:0;">
                        <div class="dash-icon dash-icon--teal" style="width:38px; height:38px;">
                            <iconify-icon icon="solar:monitor-bold-duotone" style="font-size:1.2rem;"></iconify-icon>
                        </div>
                        <div style="min-width:0;">
                            <p style="margin:0 0 2px; font-size:.88rem; font-weight:700; color:#111827;
                                      white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                {{ $maq['maquina_nome'] ?? 'Máquina' }}
                            </p>
                            <p style="margin:0; font-size:.72rem; color:#9ca3af; display:flex; align-items:center; gap:3px;">
                                <iconify-icon icon="solar:map-point-bold-duotone" style="font-size:.8rem;"></iconify-icon>
                                {{ $maq['local_nome'] ?? '—' }}
                            </p>
                        </div>
                    </div>
                    <span style="flex-shrink:0; font-size:.65rem; font-weight:700; padding:3px 8px;
                                 border-radius:20px; display:flex; align-items:center; gap:4px;
                                 background:{{ $ativo ? '#dcfce7' : '#fee2e2' }};
                                 color:{{ $ativo ? '#16a34a' : '#dc2626' }};">
                        <span style="width:5px; height:5px; border-radius:50%;
                                     background:{{ $ativo ? '#16a34a' : '#dc2626' }};"></span>
                        {{ $ativo ? 'Online' : 'Offline' }}
                    </span>
                </div>

                <div style="padding:12px 16px; display:flex; gap:12px; border-bottom:1px solid #f3f4f6;">
                    <div style="flex:1; text-align:center;">
                        <p style="margin:0 0 2px; font-size:.62rem; font-weight:600; text-transform:uppercase;
                                   letter-spacing:.05em; color:#9ca3af;">Saldo período</p>
                        <p style="margin:0; font-size:.9rem; font-weight:700;
                                  color:{{ $saldo < 0 ? '#dc2626' : 'var(--sp-teal)' }};">
                            R$ {{ number_format($saldo, 2, ',', '.') }}
                        </p>
                    </div>
                </div>

                <div style="padding:12px 16px; display:flex; flex-wrap:wrap; gap:6px; margin-top:auto;">
                    <a href="{{ route('maquinas-transacoes', ['id_maquina' => $idMaquina]) }}" class="dash-action-btn">
                        <iconify-icon icon="solar:transfer-horizontal-bold-duotone" style="color:var(--sp-teal);"></iconify-icon>
                        Transações
                    </a>
                    <a href="{{ $qrHref }}" class="dash-action-btn">
                        <iconify-icon icon="solar:qr-code-bold-duotone" style="color:var(--sp-navy);"></iconify-icon>
                        {{ $possuiQr ? 'QR Code' : 'Gerar QR' }}
                    </a>
                    <a href="{{ route('view-liberar-jogadas', ['id_maquina' => $idMaquina]) }}" class="dash-action-btn">
                        <iconify-icon icon="solar:play-circle-bold-duotone" style="color:#ca8a04;"></iconify-icon>
                        Jogada
                    </a>
                    <a href="{{ route('maquinas-editar', ['id_maquina' => $idMaquina]) }}" class="dash-action-btn">
                        <iconify-icon icon="solar:pen-bold-duotone" style="color:var(--sp-navy);"></iconify-icon>
                        Editar
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</section>

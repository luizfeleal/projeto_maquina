<section id="acoes" class="dash-section">

    <div class="dash-section-header">
        <h2>
            <iconify-icon icon="solar:bolt-bold-duotone" style="color:var(--sp-teal);"></iconify-icon>
            Ações rápidas
        </h2>
        <p>Atalhos para as operações mais usadas</p>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(160px,1fr)); gap:12px;">

        <a href="{{ route('clientes-maquinas-transacoes') }}" class="dash-card"
           style="padding:18px 16px; text-decoration:none; text-align:center; display:flex;
                  flex-direction:column; align-items:center; gap:8px; transition:box-shadow .15s;">
            <div class="dash-icon dash-icon--teal" style="width:44px; height:44px;">
                <iconify-icon icon="solar:transfer-horizontal-bold-duotone" style="font-size:1.3rem;"></iconify-icon>
            </div>
            <span style="font-size:.8rem; font-weight:700; color:#374151;">Extrato</span>
        </a>

        <a href="{{ route('view-clientes-maquinas-liberar-jogadas') }}" class="dash-card"
           style="padding:18px 16px; text-decoration:none; text-align:center; display:flex;
                  flex-direction:column; align-items:center; gap:8px;">
            <div class="dash-icon dash-icon--warn" style="width:44px; height:44px;">
                <iconify-icon icon="solar:play-circle-bold-duotone" style="font-size:1.3rem;"></iconify-icon>
            </div>
            <span style="font-size:.8rem; font-weight:700; color:#374151;">Liberar Jogada</span>
        </a>

        <a href="{{ route('cliente-qr-criar') }}" class="dash-card"
           style="padding:18px 16px; text-decoration:none; text-align:center; display:flex;
                  flex-direction:column; align-items:center; gap:8px;">
            <div class="dash-icon dash-icon--navy" style="width:44px; height:44px;">
                <iconify-icon icon="solar:qr-code-bold-duotone" style="font-size:1.3rem;"></iconify-icon>
            </div>
            <span style="font-size:.8rem; font-weight:700; color:#374151;">Gerar QR (Efí)</span>
        </a>

        <a href="{{ route('clientes-maquinas') }}" class="dash-card"
           style="padding:18px 16px; text-decoration:none; text-align:center; display:flex;
                  flex-direction:column; align-items:center; gap:8px;">
            <div class="dash-icon dash-icon--teal" style="width:44px; height:44px;">
                <iconify-icon icon="solar:devices-bold-duotone" style="font-size:1.3rem;"></iconify-icon>
            </div>
            <span style="font-size:.8rem; font-weight:700; color:#374151;">Minhas Máquinas</span>
        </a>

        <a href="{{ route('cliente-maquinas-cartao') }}" class="dash-card"
           style="padding:18px 16px; text-decoration:none; text-align:center; display:flex;
                  flex-direction:column; align-items:center; gap:8px;">
            <div class="dash-icon dash-icon--navy" style="width:44px; height:44px;">
                <iconify-icon icon="solar:card-recive-bold-duotone" style="font-size:1.3rem;"></iconify-icon>
            </div>
            <span style="font-size:.8rem; font-weight:700; color:#374151;">Máquina Cartão (Pagbank)</span>
        </a>

        <a href="{{ route('cliente-credencial-listar') }}" class="dash-card"
           style="padding:18px 16px; text-decoration:none; text-align:center; display:flex;
                  flex-direction:column; align-items:center; gap:8px;">
            <div class="dash-icon dash-icon--teal" style="width:44px; height:44px;">
                <iconify-icon icon="solar:key-bold-duotone" style="font-size:1.3rem;"></iconify-icon>
            </div>
            <span style="font-size:.8rem; font-weight:700; color:#374151;">Credenciais</span>
        </a>

    </div>
</section>

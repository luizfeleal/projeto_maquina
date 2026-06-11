<section id="relatorios" class="dash-section">

    <div class="dash-section-header">
        <h2>
            <iconify-icon icon="solar:document-text-bold-duotone" style="color:var(--sp-teal);"></iconify-icon>
            Relatórios
        </h2>
        <p>Escolha o relatório, configure os filtros e clique em Gerar</p>
    </div>

    <div class="dash-card" style="overflow:hidden;">

        <div style="display:flex; flex-wrap:wrap; gap:0; border-bottom:1px solid #f3f4f6;
                    background:var(--sp-bg); padding:8px 8px 0;">
            <button type="button" class="rel-tab-btn active" data-target="relatorios-totalTransacoes"
                    style="flex:1; min-width:130px; padding:10px 12px; border:none; background:transparent;
                           border-radius:10px 10px 0 0; font-size:.76rem; font-weight:600; color:var(--sp-muted);
                           cursor:pointer; display:flex; align-items:center; justify-content:center; gap:5px;
                           border-bottom:2px solid transparent; margin-bottom:-1px;">
                <iconify-icon icon="solar:transfer-horizontal-bold-duotone"></iconify-icon>
                Transações
            </button>
            <button type="button" class="rel-tab-btn" data-target="relatorios-taxasDesconto"
                    style="flex:1; min-width:130px; padding:10px 12px; border:none; background:transparent;
                           border-radius:10px 10px 0 0; font-size:.76rem; font-weight:600; color:var(--sp-muted);
                           cursor:pointer; display:flex; align-items:center; justify-content:center; gap:5px;
                           border-bottom:2px solid transparent; margin-bottom:-1px;">
                <iconify-icon icon="solar:tag-price-bold-duotone"></iconify-icon>
                Taxas
            </button>
            <button type="button" class="rel-tab-btn" data-target="relatorios-relatorioErros"
                    style="flex:1; min-width:130px; padding:10px 12px; border:none; background:transparent;
                           border-radius:10px 10px 0 0; font-size:.76rem; font-weight:600; color:var(--sp-muted);
                           cursor:pointer; display:flex; align-items:center; justify-content:center; gap:5px;
                           border-bottom:2px solid transparent; margin-bottom:-1px;">
                <iconify-icon icon="solar:danger-triangle-bold-duotone"></iconify-icon>
                Erros
            </button>
            <button type="button" class="rel-tab-btn" data-target="relatorios-maquinasOnOff"
                    style="flex:1; min-width:130px; padding:10px 12px; border:none; background:transparent;
                           border-radius:10px 10px 0 0; font-size:.76rem; font-weight:600; color:var(--sp-muted);
                           cursor:pointer; display:flex; align-items:center; justify-content:center; gap:5px;
                           border-bottom:2px solid transparent; margin-bottom:-1px;">
                <iconify-icon icon="solar:devices-bold-duotone"></iconify-icon>
                On/Off
            </button>
        </div>

        <div id="relatorios-totalTransacoes" class="rel-panel" style="padding:24px;">
            <form action="{{ route('cliente-relatorio-criar') }}" method="post">
                @csrf
                <input type="hidden" name="tipo" value="totalTransacoes">
                <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(220px,1fr)); gap:16px;">
                    <div style="grid-column:1/-1;">
                        <label class="form-label" style="font-size:.75rem; font-weight:600;">Máquina</label>
                        <select class="select-maquina-transacoes form-control" name="id_maquina[]" multiple>
                            @foreach($maquinasRelatorio as $maquina)
                                <option value="{{ $maquina['id_maquina'] }}">{{ $maquina['maquina_nome'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="font-size:.75rem; font-weight:600;">Local</label>
                        <select class="select-local-transacoes form-control" name="id_local[]" multiple>
                            @foreach($locais as $local)
                                <option value="{{ $local['id_local'] }}">{{ $local['local_nome'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="font-size:.75rem; font-weight:600;">Tipo</label>
                        <select class="form-select" name="tipo_transacao">
                            <option value="" selected>Todos</option>
                            <option value="PIX">Pix</option>
                            <option value="Cartão">Cartão</option>
                            <option value="Dinheiro">Dinheiro</option>
                            <option value="Estorno">Estorno</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="font-size:.75rem; font-weight:600;">Data início</label>
                        <input type="date" name="data_inicio" class="form-control">
                    </div>
                    <div>
                        <label class="form-label" style="font-size:.75rem; font-weight:600;">Data fim</label>
                        <input type="date" name="data_fim" class="form-control">
                    </div>
                </div>
                <div style="margin-top:20px; display:flex; justify-content:flex-end;">
                    <button type="submit" class="dash-btn-primary">
                        <iconify-icon icon="solar:document-add-bold-duotone"></iconify-icon>
                        Gerar relatório
                    </button>
                </div>
            </form>
        </div>

        <div id="relatorios-taxasDesconto" class="rel-panel" style="padding:24px; display:none;">
            <form action="{{ route('cliente-relatorio-criar') }}" method="post">
                @csrf
                <input type="hidden" name="tipo" value="taxasDesconto">
                <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(220px,1fr)); gap:16px;">
                    <div style="grid-column:1/-1;">
                        <label class="form-label" style="font-size:.75rem; font-weight:600;">Máquina</label>
                        <select class="select-maquina form-control" name="id_maquina[]" multiple>
                            @foreach($maquinasRelatorio as $maquina)
                                <option value="{{ $maquina['id_maquina'] }}">{{ $maquina['maquina_nome'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="font-size:.75rem; font-weight:600;">Local</label>
                        <select class="select-local form-control" name="id_local[]" multiple>
                            @foreach($locais as $local)
                                <option value="{{ $local['id_local'] }}">{{ $local['local_nome'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="font-size:.75rem; font-weight:600;">Situação</label>
                        <select class="form-select" name="situacao_recebimento">
                            <option value="" selected>Todas</option>
                            <option value="A Pagar">A Pagar</option>
                            <option value="Pago">Pago</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="font-size:.75rem; font-weight:600;">Data início</label>
                        <input type="date" name="data_inicio" class="form-control">
                    </div>
                    <div>
                        <label class="form-label" style="font-size:.75rem; font-weight:600;">Data fim</label>
                        <input type="date" name="data_fim" class="form-control">
                    </div>
                </div>
                <div style="margin-top:20px; display:flex; justify-content:flex-end;">
                    <button type="submit" class="dash-btn-primary">
                        <iconify-icon icon="solar:document-add-bold-duotone"></iconify-icon>
                        Gerar relatório
                    </button>
                </div>
            </form>
        </div>

        <div id="relatorios-relatorioErros" class="rel-panel" style="padding:24px; display:none;">
            <form action="{{ route('cliente-relatorio-criar') }}" method="post">
                @csrf
                <input type="hidden" name="tipo" value="relatorioErros">
                <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(220px,1fr)); gap:16px;">
                    <div>
                        <label class="form-label" style="font-size:.75rem; font-weight:600;">Máquina</label>
                        <select class="select-maquina-erros form-control" name="id_maquina[]" multiple>
                            @foreach($maquinasRelatorio as $maquina)
                                <option value="{{ $maquina['id_maquina'] }}">{{ $maquina['maquina_nome'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="font-size:.75rem; font-weight:600;">Local</label>
                        <select class="select-local-erros form-control" name="id_local[]" multiple>
                            @foreach($locais as $local)
                                <option value="{{ $local['id_local'] }}">{{ $local['local_nome'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="font-size:.75rem; font-weight:600;">Data início</label>
                        <input type="date" name="data_inicio" class="form-control">
                    </div>
                    <div>
                        <label class="form-label" style="font-size:.75rem; font-weight:600;">Data fim</label>
                        <input type="date" name="data_fim" class="form-control">
                    </div>
                </div>
                <div style="margin-top:20px; display:flex; justify-content:flex-end;">
                    <button type="submit" class="dash-btn-primary">
                        <iconify-icon icon="solar:document-add-bold-duotone"></iconify-icon>
                        Gerar relatório
                    </button>
                </div>
            </form>
        </div>

        <div id="relatorios-maquinasOnOff" class="rel-panel" style="padding:24px; display:none;">
            <p style="margin:0 0 16px; font-size:.85rem; color:var(--sp-muted);">
                Gera um relatório com o status atual de todas as suas máquinas (online e offline).
            </p>
            <form action="{{ route('cliente-relatorio-criar') }}" method="post">
                @csrf
                <input type="hidden" name="tipo" value="maquinasOnOff">
                <div style="display:flex; justify-content:flex-end;">
                    <button type="submit" class="dash-btn-primary">
                        <iconify-icon icon="solar:document-add-bold-duotone"></iconify-icon>
                        Gerar relatório
                    </button>
                </div>
            </form>
        </div>

    </div>
</section>

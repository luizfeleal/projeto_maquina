@extends('layouts.app')
@section('title', 'Cadastro de Cliente')
@section('content')

<div class="cadastro-cliente-page">

    <div class="cadastro-cliente-header">
        <div>
            <span class="cadastro-cliente-eyebrow">Novo cliente</span>
            <h1 class="cadastro-cliente-title">Cadastro de cliente</h1>
            <p class="cadastro-cliente-subtitle">
                Preencha os dados abaixo para abrir a conta, liberar o acesso à plataforma e vincular os
                produtos de estoque que o cliente vai receber.
            </p>
        </div>
    </div>

    <form action="{{ route('usuario-registrar') }}" id="cadastro-cliente-form" class="cadastro-cliente-grid needs-validation" method="post" enctype="multipart/form-data" novalidate>
        @csrf

        <div class="cadastro-cliente-form-col">

            {{-- 1. Empresa --}}
            <section class="cc-section">
                <header class="cc-section-header">
                    <span class="cc-step">01</span>
                    <div>
                        <iconify-icon icon="solar:buildings-2-bold-duotone" class="cc-section-icon"></iconify-icon>
                        <span class="cc-section-title">Dados da empresa</span>
                    </div>
                </header>
                <div class="cc-section-body">
                    <div class="mb-3">
                        <label for="cliente_nome" class="form-label">Nome completo ou razão social*</label>
                        <input type="text" class="form-control" name="cliente_nome" id="cliente_nome" placeholder="Ex: Comércio de Bebidas Bom Preço LTDA" required>
                        <div class="invalid-feedback">
                            <p class="invalid-p" id="cliente_nome_mensagem">Campo obrigatório</p>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label for="cliente_cpf_cnpj" class="form-label">CNPJ*</label>
                        <input type="text" class="form-control" name="cliente_cpf_cnpj" id="cliente_cpf_cnpj" placeholder="00.000.000/0000-00" maxlength="18" required>
                        <div class="invalid-feedback">
                            <p class="invalid-p invalid-p-cpf-cnpj">Campo obrigatório</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- 2. Endereço --}}
            <section class="cc-section">
                <header class="cc-section-header">
                    <span class="cc-step">02</span>
                    <div>
                        <iconify-icon icon="solar:map-point-bold-duotone" class="cc-section-icon"></iconify-icon>
                        <span class="cc-section-title">Endereço</span>
                    </div>
                </header>
                <div class="cc-section-body">
                    <div class="row g-3 mb-3">
                        <div class="col-sm-4">
                            <label for="cliente_cep" class="form-label">CEP*</label>
                            <div class="cc-cep-wrap">
                                <input type="text" class="form-control" name="cliente_cep" id="cliente_cep" placeholder="00000-000" maxlength="9" required>
                                <iconify-icon icon="solar:refresh-bold-duotone" id="cepSpinner" class="cc-cep-spinner"></iconify-icon>
                            </div>
                            <div class="invalid-feedback">
                                <p class="invalid-p">Campo obrigatório</p>
                            </div>
                            <div class="form-text text-muted">Preenche o endereço automaticamente (ViaCEP).</div>
                        </div>
                        <div class="col-sm-8">
                            <label for="cliente_logradouro" class="form-label">Logradouro*</label>
                            <input type="text" class="form-control" name="cliente_logradouro" id="cliente_logradouro" placeholder="Rua, avenida..." required>
                            <div class="invalid-feedback">
                                <p class="invalid-p">Campo obrigatório</p>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mb-0">
                        <div class="col-sm-3">
                            <label for="cliente_numero" class="form-label">Número*</label>
                            <input type="text" class="form-control" name="cliente_numero" id="cliente_numero" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p">Campo obrigatório</p>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label for="cliente_bairro" class="form-label">Bairro*</label>
                            <input type="text" class="form-control" name="cliente_bairro" id="cliente_bairro" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p">Campo obrigatório</p>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label for="cliente_cidade" class="form-label">Cidade*</label>
                            <input type="text" class="form-control" name="cliente_cidade" id="cliente_cidade" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p">Campo obrigatório</p>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <label for="cliente_uf" class="form-label">UF*</label>
                            <input type="text" class="form-control text-uppercase" name="cliente_uf" id="cliente_uf" maxlength="2" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p">Obrigatório</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- 3. Contato e acesso --}}
            <section class="cc-section">
                <header class="cc-section-header">
                    <span class="cc-step">03</span>
                    <div>
                        <iconify-icon icon="solar:user-id-bold-duotone" class="cc-section-icon"></iconify-icon>
                        <span class="cc-section-title">Contato e acesso à plataforma</span>
                    </div>
                </header>
                <div class="cc-section-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="cliente_celular" class="form-label">Telefone*</label>
                            <input type="text" class="form-control" name="cliente_celular" id="cliente_celular" placeholder="(00) 00000-0000" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p" id="cliente_celular_mensagem">Campo obrigatório</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="cliente_email" class="form-label">Email*</label>
                            <input type="email" class="form-control" name="cliente_email" id="cliente_email" placeholder="contato@empresa.com.br" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mb-0">
                        <div class="col-md-6">
                            <label for="cliente_senha" class="form-label">Senha de acesso*</label>
                            <input type="password" class="form-control" name="cliente_senha" id="cliente_senha" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p">Campo obrigatório</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="cliente_confirmar_senha" class="form-label">Confirmar senha*</label>
                            <input type="password" class="form-control" name="cliente_confirmar_senha" id="cliente_confirmar_senha" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- 4. Credencial de cobrança --}}
            <section class="cc-section">
                <header class="cc-section-header">
                    <span class="cc-step">04</span>
                    <div>
                        <iconify-icon icon="solar:card-recive-bold-duotone" class="cc-section-icon"></iconify-icon>
                        <span class="cc-section-title">Credencial de cobrança</span>
                    </div>
                </header>
                <div class="cc-section-body">
                    <p class="text-muted small mb-3">
                        Defina por qual gateway este cliente vai receber. Isso também determina o nível de
                        acesso liberado no login dele na plataforma.
                    </p>

                    <div class="cc-gateway-row" id="row-efi">
                        <div class="cc-gateway-info">
                            <iconify-icon id="icon-efi" icon="solar:money-bag-bold-duotone" class="cc-gateway-icon"></iconify-icon>
                            <div>
                                <p class="cc-gateway-name">Efí — Pix</p>
                                <p id="label-efi" class="cc-gateway-status">Não vinculado</p>
                            </div>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" name="checkbox_efi" type="checkbox" role="switch" id="checkboxEfi">
                        </div>
                    </div>

                    <div class="cc-gateway-row" id="row-pagbank">
                        <div class="cc-gateway-info">
                            <iconify-icon id="icon-pagbank" icon="solar:card-recive-bold-duotone" class="cc-gateway-icon"></iconify-icon>
                            <div>
                                <p class="cc-gateway-name">PagBank — Maquininha</p>
                                <p id="label-pagbank" class="cc-gateway-status">Não vinculado</p>
                            </div>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" name="checkbox_pagbank" type="checkbox" role="switch" id="checkboxPagbank">
                        </div>
                    </div>
                </div>
            </section>

            {{-- 5. Produtos do estoque --}}
            <section class="cc-section">
                <header class="cc-section-header">
                    <span class="cc-step">05</span>
                    <div>
                        <iconify-icon icon="solar:box-bold-duotone" class="cc-section-icon"></iconify-icon>
                        <span class="cc-section-title">Produtos do estoque</span>
                    </div>
                </header>
                <div class="cc-section-body">
                    <p class="text-muted small mb-3">
                        Opcional. Os produtos adicionados aqui têm a quantidade baixada do estoque
                        automaticamente assim que o cliente for cadastrado.
                    </p>

                    @if($produtosEstoque->isEmpty())
                        <div class="cc-empty-stock">
                            <iconify-icon icon="solar:box-minimalistic-bold-duotone"></iconify-icon>
                            Não há produtos com estoque disponível no momento.
                        </div>
                    @else
                        <div class="row g-3 align-items-end mb-3">
                            <div class="col-sm-6">
                                <label for="produto_select" class="form-label">Produto</label>
                                <select id="produto_select" class="form-select js-select2" data-placeholder="Selecione um produto">
                                    <option value=""></option>
                                    @foreach($produtosEstoque as $p)
                                        <option value="{{ $p['id'] }}"
                                                data-nome="{{ $p['nome_produto'] }}"
                                                data-valor="{{ $p['valor'] }}"
                                                data-disponivel="{{ $p['quantidade'] }}">
                                            {{ $p['nome_produto'] }} — {{ $p['quantidade'] }} disp. — R$ {{ number_format($p['valor'], 2, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label for="produto_quantidade" class="form-label">Quantidade</label>
                                <input type="number" id="produto_quantidade" class="form-control" min="1" value="1">
                            </div>
                            <div class="col-sm-3">
                                <button type="button" id="btn-add-produto" class="btn btn-outline-success w-100">
                                    <iconify-icon icon="solar:add-circle-bold-duotone" inline></iconify-icon>
                                    Adicionar
                                </button>
                            </div>
                        </div>

                        <div id="produtos-lista" class="cc-produtos-lista">
                            <p class="text-muted small mb-0" id="produtos-vazio">Nenhum produto adicionado ainda.</p>
                        </div>
                        <div id="produtos-inputs"></div>
                    @endif
                </div>
            </section>

            <div class="cc-submit-row">
                <a href="{{ route('usuarios') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button class="btn btn-primary cc-submit-btn" type="submit">
                    <iconify-icon icon="solar:check-circle-bold-duotone" inline></iconify-icon>
                    Cadastrar cliente
                </button>
            </div>
        </div>

        {{-- Coluna de pré-visualização --}}
        <aside class="cadastro-cliente-preview-col">
            <div class="cc-preview-sticky">
                <div class="cc-card" id="previewCard">
                    <div class="cc-card-chip" aria-hidden="true"></div>
                    <div class="cc-card-top">
                        <span class="cc-card-brand">SwiftPay · Cliente</span>
                        <span class="cc-card-gateway" id="previewGateway">Sem credencial definida</span>
                    </div>
                    <div class="cc-card-cnpj" id="previewCnpj">00.000.000/0000-00</div>
                    <div class="cc-card-razao" id="previewRazao">Razão social da empresa</div>
                    <div class="cc-card-endereco" id="previewEndereco">O endereço aparece aqui conforme você preenche o CEP</div>
                    <div class="cc-card-footer">
                        <div>
                            <span class="cc-card-label">Contato</span>
                            <span id="previewContato">—</span>
                        </div>
                        <div class="text-end">
                            <span class="cc-card-label">Acesso liberado</span>
                            <span id="previewGrupo">Nenhuma credencial</span>
                        </div>
                    </div>
                </div>

                <div class="cc-preview-produtos">
                    <div class="cc-preview-produtos-header">
                        <iconify-icon icon="solar:cart-check-bold-duotone"></iconify-icon>
                        Produtos que serão vinculados
                    </div>
                    <ul id="previewProdutosList" class="cc-preview-produtos-list">
                        <li class="text-muted small">Nenhum produto selecionado ainda.</li>
                    </ul>
                    <div class="cc-preview-produtos-total">
                        <span>Total em produtos</span>
                        <strong id="previewProdutosTotal">R$ 0,00</strong>
                    </div>
                </div>
            </div>
        </aside>

    </form>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&display=swap');

    .cadastro-cliente-page {
        max-width: 1180px;
        margin: 0 auto;
        padding-top: .25rem;
    }

    .cadastro-cliente-header { margin-bottom: 20px; }

    .cadastro-cliente-eyebrow {
        font-family: 'Space Grotesk', sans-serif;
        font-size: .72rem;
        font-weight: 600;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: #2C9BA5;
    }

    .cadastro-cliente-title {
        font-family: 'Space Grotesk', sans-serif;
        font-size: 1.6rem;
        font-weight: 700;
        color: #111827;
        margin: 4px 0 6px;
    }

    .cadastro-cliente-subtitle {
        color: #6b7280;
        font-size: .92rem;
        max-width: 560px;
        margin: 0;
    }

    .cadastro-cliente-grid {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 22px;
        align-items: start;
    }

    @media (max-width: 992px) {
        .cadastro-cliente-grid { grid-template-columns: 1fr; }
        .cadastro-cliente-preview-col { order: -1; }
    }

    /* ---- Seções do formulário ---- */
    .cc-section {
        background: #fff;
        border: 1px solid #e8ecf0;
        border-radius: 16px;
        box-shadow: 0 1px 4px rgba(0,0,0,.05);
        overflow: hidden;
        margin-bottom: 16px;
    }

    .cc-section-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        border-bottom: 1px solid #f3f4f6;
    }

    .cc-step {
        font-family: 'Space Grotesk', sans-serif;
        font-weight: 700;
        font-size: .78rem;
        color: #2C9BA5;
        background: #e6f5f6;
        border-radius: 8px;
        padding: 3px 8px;
        letter-spacing: .04em;
    }

    .cc-section-header > div {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .cc-section-icon { font-size: 1.1rem; color: #2C9BA5; }

    .cc-section-title {
        font-size: .9rem;
        font-weight: 700;
        color: #111827;
    }

    .cc-section-body { padding: 20px 24px; }

    /* ---- CEP com spinner ---- */
    .cc-cep-wrap { position: relative; }
    .cc-cep-spinner {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #2C9BA5;
        opacity: 0;
        font-size: 1rem;
    }
    .cc-cep-spinner.spinning {
        opacity: 1;
        animation: cc-spin 0.8s linear infinite;
    }
    @media (prefers-reduced-motion: reduce) {
        .cc-cep-spinner.spinning { animation: none; }
    }
    @keyframes cc-spin { to { transform: translateY(-50%) rotate(360deg); } }

    /* ---- Credenciais (toggle) ---- */
    .cc-gateway-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 12px;
        border-radius: 10px;
        border: 1px solid #f3f4f6;
        background: #f9fafb;
        margin-bottom: 12px;
    }
    .cc-gateway-row:last-child { margin-bottom: 0; }
    .cc-gateway-info { display: flex; align-items: center; gap: 10px; }
    .cc-gateway-icon { font-size: 1.2rem; color: #9ca3af; }
    .cc-gateway-name { margin: 0; font-size: .85rem; font-weight: 600; color: #374151; }
    .cc-gateway-status { margin: 0; font-size: .72rem; font-weight: 700; color: #9ca3af; }

    /* ---- Produtos ---- */
    .cc-empty-stock {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 14px 16px;
        border-radius: 10px;
        background: #f9fafb;
        color: #6b7280;
        font-size: .85rem;
    }
    .cc-empty-stock iconify-icon { font-size: 1.2rem; }

    .cc-produtos-lista { display: flex; flex-direction: column; gap: 8px; margin-bottom: 4px; }

    .produto-linha {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        border-radius: 10px;
        background: #f9fafb;
        border: 1px solid #f0f2f5;
    }
    .produto-linha-info { display: flex; flex-direction: column; }
    .produto-linha-info strong { font-size: .85rem; color: #111827; }
    .produto-linha-info span { font-size: .76rem; color: #6b7280; }
    .produto-linha-remover {
        border: none;
        background: transparent;
        color: #ef4444;
        font-size: 1.05rem;
        line-height: 1;
        padding: 4px;
        border-radius: 6px;
    }
    .produto-linha-remover:hover { background: #fee2e2; }

    .cc-submit-row {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 4px;
    }
    .cc-submit-btn { min-width: 190px; font-weight: 600; }

    /* ---- Cartão de pré-visualização (elemento de assinatura) ---- */
    .cc-preview-sticky { position: sticky; top: 16px; display: flex; flex-direction: column; gap: 16px; }

    .cc-card {
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        padding: 26px 24px 22px;
        color: #fff;
        background: linear-gradient(135deg, #0F1A3B 0%, #16526D 100%);
        box-shadow: 0 20px 40px -14px rgba(15, 26, 59, .55);
        transition: background .25s ease;
    }

    .cc-card::after {
        content: '';
        position: absolute;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(44,155,165,.35), transparent 70%);
        top: -80px;
        right: -60px;
        pointer-events: none;
    }

    .cc-card-chip {
        width: 34px;
        height: 24px;
        border-radius: 5px;
        background: linear-gradient(135deg, #d4b962, #f4e4a1 45%, #c9a94a);
        margin-bottom: 18px;
        position: relative;
        z-index: 1;
    }

    .cc-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        position: relative;
        z-index: 1;
    }

    .cc-card-brand {
        font-family: 'Space Grotesk', sans-serif;
        font-size: .72rem;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: rgba(255,255,255,.65);
    }

    .cc-card-gateway {
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .04em;
        padding: 4px 10px;
        border-radius: 999px;
        background: rgba(255,255,255,.14);
        color: #fff;
        transition: background .2s ease, color .2s ease;
    }
    .cc-card.gw-efi .cc-card-gateway { background: #16a34a; }
    .cc-card.gw-pagbank .cc-card-gateway { background: #2563eb; }
    .cc-card.gw-both .cc-card-gateway { background: #2C9BA5; }

    .cc-card-cnpj {
        font-family: 'Space Grotesk', sans-serif;
        font-size: 1.28rem;
        font-weight: 600;
        letter-spacing: .06em;
        font-variant-numeric: tabular-nums;
        margin-bottom: 6px;
        position: relative;
        z-index: 1;
        word-break: break-all;
    }

    .cc-card-razao {
        font-size: .88rem;
        font-weight: 600;
        color: rgba(255,255,255,.92);
        margin-bottom: 4px;
        position: relative;
        z-index: 1;
    }

    .cc-card-endereco {
        font-size: .76rem;
        color: rgba(255,255,255,.62);
        margin-bottom: 20px;
        min-height: 2.2em;
        position: relative;
        z-index: 1;
    }

    .cc-card-footer {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 10px;
        padding-top: 14px;
        border-top: 1px solid rgba(255,255,255,.16);
        font-size: .76rem;
        position: relative;
        z-index: 1;
    }

    .cc-card-footer > div { display: flex; flex-direction: column; gap: 2px; max-width: 60%; }
    .cc-card-footer > div:last-child { max-width: 45%; }
    .cc-card-label {
        font-size: .64rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: rgba(255,255,255,.5);
    }
    .cc-card-footer span:not(.cc-card-label) {
        color: #fff;
        font-weight: 600;
        word-break: break-word;
    }

    .cc-preview-produtos {
        background: #fff;
        border: 1px solid #e8ecf0;
        border-radius: 16px;
        box-shadow: 0 1px 4px rgba(0,0,0,.05);
        padding: 18px 20px;
    }

    .cc-preview-produtos-header {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: .82rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 12px;
    }
    .cc-preview-produtos-header iconify-icon { color: #2C9BA5; font-size: 1.1rem; }

    .cc-preview-produtos-list {
        list-style: none;
        margin: 0 0 12px;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 8px;
        max-height: 220px;
        overflow-y: auto;
    }
    .cc-preview-produtos-list li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: .8rem;
        color: #374151;
        gap: 8px;
    }
    .cc-preview-produtos-list li strong { color: #111827; white-space: nowrap; }

    .cc-preview-produtos-total {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 12px;
        border-top: 1px solid #f3f4f6;
        font-size: .84rem;
        font-weight: 600;
        color: #111827;
    }

    :root[data-theme="dark"] .cc-section,
    :root[data-theme="dark"] .cc-preview-produtos { background: #161c2c; border-color: #262f45; }
    :root[data-theme="dark"] .cc-section-title,
    :root[data-theme="dark"] .cadastro-cliente-title,
    :root[data-theme="dark"] .cc-preview-produtos-header,
    :root[data-theme="dark"] .produto-linha-info strong,
    :root[data-theme="dark"] .cc-preview-produtos-total,
    :root[data-theme="dark"] .cc-card-footer span:not(.cc-card-label) { color: #f3f4f6; }
    :root[data-theme="dark"] .cc-section-header,
    :root[data-theme="dark"] .cc-gateway-row,
    :root[data-theme="dark"] .produto-linha { border-color: #262f45; background: #1d2437; }
</style>

@endsection

@section('scriptTable')

<script>
    $(document).ready(function () {
        $('.js-select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            allowClear: true,
            placeholder: function () { return $(this).data('placeholder'); }
        });

        validaSenhas();

        $("#cliente_nome").on('blur input', () => {
            validarCampoNome('cliente_nome', 'cliente_nome_mensagem');
            atualizarPreviewGeral();
        });

        $("#cliente_celular").on('blur', () => {
            validarCelular('cliente_celular', 'cliente_celular_mensagem');
        });
        $('#cliente_celular').mask('(00) 00000-0000');
        $('#cliente_cep').mask('00000-000');

        $("#cliente_cpf_cnpj").on('input', function () {
            $(this).val($(this).val().replace(/\D/g, '').replace(/(\d{2})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1/$2').replace(/(\d{4})(\d)/, '$1-$2').substring(0, 18));
            atualizarPreviewGeral();
        });
        $("#cliente_cpf_cnpj").on('blur', () => {
            var numeros = $("#cliente_cpf_cnpj").val().replace(/\D/g, '');
            if (numeros.length === 14 && typeof validarCNPJ === 'function' && validarCNPJ(numeros)) {
                $("#cliente_cpf_cnpj").removeClass('is-invalid').addClass('is-valid');
                $(".invalid-p-cpf-cnpj").text('Campo obrigatório');
            } else {
                $("#cliente_cpf_cnpj").removeClass('is-valid').addClass('is-invalid');
                $(".invalid-p-cpf-cnpj").text('CNPJ inválido');
            }
        });

        $("#cliente_email").on('blur', () => {
            validarCelular('cliente_email', 'cliente_email_mensagem');
        });

        // ---- CEP -> ViaCEP ----
        $('#cliente_cep').on('blur', async function () {
            var cepValor = $('#cliente_cep').val().replace(/\D/g, '');
            if (cepValor.length !== 8) { return; }

            $('#cepSpinner').addClass('spinning');
            var dados = await coletaEndereco(cepValor);
            $('#cepSpinner').removeClass('spinning');

            if (!dados || dados.erro) {
                Swal.fire({
                    icon: 'warning',
                    title: 'CEP não encontrado',
                    text: 'Confira o CEP informado ou preencha o endereço manualmente.',
                    confirmButtonColor: '#2C9BA5',
                });
                return;
            }

            $('#cliente_logradouro').val(dados.logradouro || '');
            $('#cliente_bairro').val(dados.bairro || '');
            $('#cliente_cidade').val(dados.localidade || '');
            $('#cliente_uf').val(dados.uf || '');
            $('#cliente_numero').focus();
            atualizarPreviewGeral();
        });

        $('#cliente_logradouro, #cliente_numero, #cliente_bairro, #cliente_cidade, #cliente_uf, #cliente_email').on('input', atualizarPreviewGeral);

        // ---- Toggle credenciais ----
        function updateGatewayRow(rowId, iconId, labelId, enabled, color) {
            $('#' + rowId).css('background', enabled ? 'rgba(' + color + ',.08)' : '#f9fafb');
            $('#' + iconId).css('color', enabled ? 'rgb(' + color + ')' : '#9ca3af');
            $('#' + labelId).text(enabled ? 'Vinculado' : 'Não vinculado').css('color', enabled ? 'rgb(' + color + ')' : '#9ca3af');
        }

        $('#checkboxEfi').on('change', function () {
            updateGatewayRow('row-efi', 'icon-efi', 'label-efi', this.checked, '22,163,74');
            atualizarPreviewGeral();
        });
        $('#checkboxPagbank').on('change', function () {
            updateGatewayRow('row-pagbank', 'icon-pagbank', 'label-pagbank', this.checked, '37,99,235');
            atualizarPreviewGeral();
        });

        // ---- Produtos do estoque ----
        var produtosAdicionados = [];

        function formatarMoeda(valor) {
            return 'R$ ' + Number(valor || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function renderProdutos() {
            var $lista = $('#produtos-lista');
            var $inputs = $('#produtos-inputs');
            $inputs.empty();

            if (produtosAdicionados.length === 0) {
                $lista.html('<p class="text-muted small mb-0" id="produtos-vazio">Nenhum produto adicionado ainda.</p>');
            } else {
                var html = '';
                produtosAdicionados.forEach(function (p, i) {
                    html += '<div class="produto-linha" data-index="' + i + '">' +
                        '<div class="produto-linha-info"><strong>' + p.nome + '</strong>' +
                        '<span>' + p.quantidade + ' un. · ' + formatarMoeda(p.valor * p.quantidade) + '</span></div>' +
                        '<button type="button" class="produto-linha-remover" data-index="' + i + '" aria-label="Remover ' + p.nome + '">' +
                        '<iconify-icon icon="solar:trash-bin-trash-bold-duotone"></iconify-icon></button>' +
                        '</div>';
                    $inputs.append('<input type="hidden" name="produtos[' + i + '][id_estoque_produto]" value="' + p.id + '">');
                    $inputs.append('<input type="hidden" name="produtos[' + i + '][quantidade]" value="' + p.quantidade + '">');
                });
                $lista.html(html);
            }

            atualizarPreviewProdutos();
        }

        $('#btn-add-produto').on('click', function () {
            var $opt = $('#produto_select option:selected');
            var id = $('#produto_select').val();

            if (!id) {
                Swal.fire({ icon: 'warning', title: 'Selecione um produto', confirmButtonColor: '#2C9BA5' });
                return;
            }

            var qtd = parseInt($('#produto_quantidade').val() || '1', 10);
            if (isNaN(qtd) || qtd < 1) { qtd = 1; }

            var disponivel = parseInt($opt.data('disponivel'), 10);
            var jaAdicionado = produtosAdicionados
                .filter(function (p) { return p.id === id; })
                .reduce(function (acc, p) { return acc + p.quantidade; }, 0);

            if (jaAdicionado + qtd > disponivel) {
                Swal.fire({
                    icon: 'error',
                    title: 'Estoque insuficiente',
                    text: 'Disponível: ' + disponivel + ' unidade(s) para este produto.',
                    confirmButtonColor: '#2C9BA5',
                });
                return;
            }

            produtosAdicionados.push({
                id: id,
                nome: $opt.data('nome'),
                valor: parseFloat($opt.data('valor')),
                quantidade: qtd,
            });

            renderProdutos();
            $('#produto_select').val('').trigger('change');
            $('#produto_quantidade').val(1);
        });

        $(document).on('click', '.produto-linha-remover', function () {
            var i = $(this).data('index');
            produtosAdicionados.splice(i, 1);
            renderProdutos();
        });

        // ---- Pré-visualização geral ----
        window.atualizarPreviewProdutos = function () {
            var $list = $('#previewProdutosList');
            var total = 0;

            if (produtosAdicionados.length === 0) {
                $list.html('<li class="text-muted small">Nenhum produto selecionado ainda.</li>');
            } else {
                var html = '';
                produtosAdicionados.forEach(function (p) {
                    var subtotal = p.valor * p.quantidade;
                    total += subtotal;
                    html += '<li><span>' + p.quantidade + 'x ' + p.nome + '</span><strong>' + formatarMoeda(subtotal) + '</strong></li>';
                });
                $list.html(html);
            }

            $('#previewProdutosTotal').text(formatarMoeda(total));
        };

        window.atualizarPreviewGeral = function () {
            $('#previewRazao').text($('#cliente_nome').val() || 'Razão social da empresa');
            $('#previewCnpj').text($('#cliente_cpf_cnpj').val() || '00.000.000/0000-00');

            var linha1 = [$('#cliente_logradouro').val(), $('#cliente_numero').val()].filter(Boolean).join(', ');
            var cidadeUf = [$('#cliente_cidade').val(), $('#cliente_uf').val()].filter(Boolean).join('/');
            var linhaEndereco = [linha1, $('#cliente_bairro').val(), cidadeUf].filter(Boolean).join(' — ');
            $('#previewEndereco').text(linhaEndereco || 'O endereço aparece aqui conforme você preenche o CEP');

            var contato = [$('#cliente_email').val(), $('#cliente_celular').val()].filter(Boolean).join(' · ');
            $('#previewContato').text(contato || '—');

            var efi = $('#checkboxEfi').is(':checked');
            var pagbank = $('#checkboxPagbank').is(':checked');
            var $gateway = $('#previewGateway');
            var $card = $('#previewCard');
            $card.removeClass('gw-none gw-efi gw-pagbank gw-both');

            if (efi && pagbank) {
                $gateway.text('Efí + PagBank');
                $card.addClass('gw-both');
                $('#previewGrupo').text('Pix (Efí) e Maquininha (PagBank)');
            } else if (efi) {
                $gateway.text('Efí · Pix');
                $card.addClass('gw-efi');
                $('#previewGrupo').text('Pix (Efí)');
            } else if (pagbank) {
                $gateway.text('PagBank · Maquininha');
                $card.addClass('gw-pagbank');
                $('#previewGrupo').text('Maquininha (PagBank)');
            } else {
                $gateway.text('Sem credencial definida');
                $card.addClass('gw-none');
                $('#previewGrupo').text('Nenhuma credencial');
            }
        };

        atualizarPreviewGeral();
        atualizarPreviewProdutos();

        // ---- Confirma senhas antes de enviar ----
        $('#cadastro-cliente-form').on('submit', function (e) {
            if ($('#cliente_senha').val() !== $('#cliente_confirmar_senha').val()) {
                e.preventDefault();
                e.stopPropagation();
                Swal.fire({ icon: 'error', title: 'As senhas não conferem', confirmButtonColor: '#2C9BA5' });
            }
        });
    });
</script>

@endsection

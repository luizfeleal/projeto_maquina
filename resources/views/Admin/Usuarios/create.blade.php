@extends('layouts.app')
@section('title', 'Criar Usuário')
@section('content')


<div class="content-body" style="padding-top:0; max-width:720px; margin:0 auto;">

    <form action="{{ route('usuario-registrar') }}" id="novo-local-form" class="w-100 needs-validation" method="post" enctype="multipart/form-data" novalidate>
        @csrf

        {{-- ── Dados do usuário (topo) ── --}}
        <div style="background:#fff; border:1px solid #e8ecf0; border-radius:16px;
                    box-shadow:0 1px 4px rgba(0,0,0,.05); overflow:hidden; margin-bottom:16px;">
            <div style="padding:16px 20px; border-bottom:1px solid #f3f4f6;
                        display:flex; align-items:center; gap:8px;">
                <iconify-icon icon="solar:user-bold-duotone"
                              style="font-size:1.1rem; color:#2C9BA5;"></iconify-icon>
                <h3 style="margin:0; font-size:.9rem; font-weight:700; color:#111827;">Dados do Usuário</h3>
            </div>
            <div style="padding:20px 24px;">
                <div class="mb-3">
                    <label for="cliente_nome" class="form-label">Nome Completo*</label>
                    <input type="text" class="form-control input-text" name="cliente_nome" id="cliente_nome" required>
                    <div class="invalid-feedback">
                        <p class="invalid-p" id="cliente_nome_mensagem">Campo obrigatório</p>
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="cliente_celular" class="form-label">Celular*</label>
                        <input type="text" class="form-control" name="cliente_celular" id="cliente_celular" required>
                        <div class="invalid-feedback">
                            <p class="invalid-p" id="cliente_celular_mensagem">Campo obrigatório</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="cliente_email" class="form-label">Email*</label>
                        <input type="email" class="form-control" name="cliente_email" id="cliente_email" required>
                        <div class="invalid-feedback">
                            <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                        </div>
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="cliente_senha" class="form-label">Senha*</label>
                        <input type="password" class="form-control" name="cliente_senha" id="cliente_senha" required>
                        <div class="invalid-feedback">
                            <p class="invalid-p">Campo obrigatório</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="cliente_confirmar_senha" class="form-label">Confirmar Senha*</label>
                        <input type="password" class="form-control" name="cliente_confirmar_senha" id="cliente_confirmar_senha" required>
                        <div class="invalid-feedback">
                            <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                        </div>
                    </div>
                </div>
                <div class="mb-0">
                    <label for="cliente_cpf_cnpj" class="form-label">CPF/CNPJ*</label>
                    <input type="text" class="form-control" name="cliente_cpf_cnpj" id="cliente_cpf_cnpj" required>
                    <div class="invalid-feedback">
                        <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Permissões de pagamento (baixo) ── --}}
        <div style="background:#fff; border:1px solid #e8ecf0; border-radius:16px;
                    box-shadow:0 1px 4px rgba(0,0,0,.05); overflow:hidden;">
            <div style="padding:16px 20px; border-bottom:1px solid #f3f4f6;
                        display:flex; align-items:center; gap:8px;">
                <iconify-icon icon="solar:lock-bold-duotone"
                              style="font-size:1.1rem; color:#ca8a04;"></iconify-icon>
                <h3 style="margin:0; font-size:.9rem; font-weight:700; color:#111827;">Permissões de Pagamento</h3>
            </div>

            <div style="padding:16px 20px; display:flex; flex-direction:column; gap:12px;">

                <div id="row-efi"
                     style="display:flex; align-items:center; justify-content:space-between; gap:8px;
                            padding:12px; border-radius:10px; border:1px solid #f3f4f6; background:#fff7f7;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <iconify-icon id="icon-efi" icon="solar:money-bag-bold-duotone"
                                      style="font-size:1.1rem; color:#dc2626;"></iconify-icon>
                        <div>
                            <p style="margin:0; font-size:.85rem; font-weight:600; color:#374151;">Jogada Pix</p>
                            <p id="label-efi" style="margin:0; font-size:.72rem; font-weight:700; color:#dc2626;">Bloqueado</p>
                        </div>
                    </div>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" name="checkbox_efi" type="checkbox"
                               role="switch" id="checkboxEfi">
                    </div>
                </div>

                <div id="row-pagbank"
                     style="display:flex; align-items:center; justify-content:space-between; gap:8px;
                            padding:12px; border-radius:10px; border:1px solid #f3f4f6; background:#fff7f7;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <iconify-icon id="icon-pagbank" icon="solar:card-recive-bold-duotone"
                                      style="font-size:1.1rem; color:#dc2626;"></iconify-icon>
                        <div>
                            <p style="margin:0; font-size:.85rem; font-weight:600; color:#374151;">Jogada Máquininha</p>
                            <p id="label-pagbank" style="margin:0; font-size:.72rem; font-weight:700; color:#dc2626;">Bloqueado</p>
                        </div>
                    </div>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" name="checkbox_pagbank" type="checkbox"
                               role="switch" id="checkboxPagbank">
                    </div>
                </div>

            </div>

            <div style="padding:0 20px 20px;">
                <button class="btn btn-primary w-100" type="submit">Criar usuário</button>
            </div>
        </div>

    </form>
</div>

@endsection

@section('scriptTable')

<script>
    $(document).ready(function() {
        $('.select-tipo').select2({
            theme: "classic"
        });

        validaSenhas();

        $("#cliente_nome").on('blur', () => {
            validarCampoNome('cliente_nome', 'cliente_nome_mensagem');
        });

        $("#cliente_celular").on('blur', () => {
            validarCelular('cliente_celular', 'cliente_celular_mensagem');
        });

        $('#cliente_celular').mask('(00) 00000-0000');

        $("#cliente_cpf_cnpj").on("blur", () => {
            if (validarDocumento($("#cliente_cpf_cnpj").val(), "cliente_cpf_cnpj")) {
                $("#cliente_cpf_cnpj").removeClass('is-invalid');
                $(".invalid-p-cpf-cnpj").empty();
                $(".invalid-p-cpf-cnpj").append("Campo obrigatório");
            } else {
                $("#cliente_cpf_cnpj").addClass('is-invalid');
                $(".invalid-p-cpf-cnpj").empty();
                $(".invalid-p-cpf-cnpj").append("Documento inválido");
            }
        });

        $("#cliente_email").on('blur', () => {
            validarCelular('cliente_email', 'cliente_email_mensagem');
        });

        $("#select_tipo").on('select2:close', () => {
            validarSelectLocalCliente('select-local', 'select_tipo_mensagem');
        });

        function updatePermRow(rowId, iconId, labelId, enabled) {
            var color = enabled ? '#16a34a' : '#dc2626';
            var bg    = enabled ? '#f0fdf4' : '#fff7f7';
            $('#' + rowId).css('background', bg);
            $('#' + iconId).attr('style', 'font-size:1.1rem; color:' + color + ';');
            $('#' + labelId).text(enabled ? 'Liberado' : 'Bloqueado').css('color', color);
        }

        $('#checkboxEfi').on('change', function () {
            updatePermRow('row-efi', 'icon-efi', 'label-efi', this.checked);
        });

        $('#checkboxPagbank').on('change', function () {
            updatePermRow('row-pagbank', 'icon-pagbank', 'label-pagbank', this.checked);
        });
    });
</script>

@endsection

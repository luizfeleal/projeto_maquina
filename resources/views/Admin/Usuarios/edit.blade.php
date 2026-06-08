@extends('layouts.app')
@section('title', 'Atualizar Usuário')
@section('content')

<div class="usuarios div-center-column w-100"
    style="padding-top: 99px;">

    <div class="container section container-platform div-center-column"
        style="margin-top: 15px; height: 100%;">

        <form action="{{ route('usuario-atualizar') }}" id="novo-local-form" class="w-100 needs-validation form-center" method="post" enctype="multipart/form-data" novalidate>
            @csrf

            <input type="hidden" name="id_cliente" value="{{$cliente['id_cliente']}}">
            <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                <div class="col-md-8">
                    <label for="cliente_nome" class="form-label"> Nome Completo*:</label>
                    <input type="text" class="form-control input-text" name="cliente_nome" id="cliente_nome" value="{{$cliente['cliente_nome']}}" required>
                    <div class="invalid-feedback">
                        <p class="invalid-p" id="cliente_nome_mensagem">Campo obrigatório</p>
                    </div>

                </div>
            </div>
            <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                <div class="col-md-4">
                    <label for="cliente_celular" class="form-label"> Celular*:</label>
                    <input type="text" class="form-control" name="cliente_celular" id="cliente_celular" value="{{$cliente['cliente_celular']}}" required>
                    <div class="invalid-feedback">
                        <p class="invalid-p" id="cliente_celular_mensagem">Campo obrigatório</p>
                    </div>

                </div>
                <div class="col-md-4">
                    <label for="cliente_email" class="form-label"> Email*:</label>
                    <input type="email" class="form-control" name="cliente_email" id="cliente_email" value="{{$cliente['cliente_email']}}" required>
                    <div class="invalid-feedback">
                        <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                    </div>

                </div>
            </div>

            <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                <div class="col-md-8">
                    <label for="cliente_cpf_cnpj" class="form-label"> CPF/CNPJ*:</label>
                    <input type="text" class="form-control" name="cliente_cpf_cnpj" id="cliente_cpf_cnpj" value="{{$cliente['cliente_cpf_cnpj']}}" required>
                    <div class="invalid-feedback">
                        <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                    </div>

                </div>
            </div>
            <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                <div class="col-md-8">
                    <h5 class="perm-section-label">Permissões:</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-check form-switch perm-switch-wrap">
                                <input class="form-check-input perm-switch" name="checkbox_efi"
                                       type="checkbox" role="switch" id="checkboxEfi"
                                       @if($cliente['checkbox_efi'] == 1) checked @endif>
                                <label class="form-check-label" for="checkboxEfi">Pix</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch perm-switch-wrap">
                                <input class="form-check-input perm-switch" name="checkbox_pagbank"
                                       type="checkbox" role="switch" id="checkboxPagbank"
                                       @if($cliente['checkbox_pagbank'] == 1) checked @endif>
                                <label class="form-check-label" for="checkboxPagbank">Máquininha de cartão (Pagbank)</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display:flex; justify-content: center; align-items: center; margin-top: 50px;">
                <button class="btn btn-primary" type="submit">Atualizar usuário</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scriptTable')

<script>
    $(document).ready(function() {
        $('.select-tipo').select2({
            theme: "classic"
        });

        validaData();
        validaSenhas();
        $("#cliente_nome").on('blur', () => {
            validarCampoNome('cliente_nome', 'cliente_nome_mensagem');
        });

        $("#cliente_celular").on('blur', () => {
            validarCelular('cliente_celular', 'cliente_celular_mensagem');
        });

        $('#cliente_celular').mask('(00) 00000-0000');
        $('#cliente_cep').mask('00000-000');
        $('#cliente_data_nascimento').mask('00/00/0000');

        $('#cliente_cep').on('blur', async () => {
            var valorCep = $('#cliente_cep').val()
            var dadoEndereco = await coletaEndereco(valorCep)
            preencherEnderecoFocoNumero('cliente_cidade', 'cliente_uf', 'cliente_logradouro', 'cliente_bairro', 'cliente_numero', dadoEndereco)
        });

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
    });
</script>

@endsection
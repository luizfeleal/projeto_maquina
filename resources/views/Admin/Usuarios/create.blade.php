@extends('layouts.app')
@section('title', 'Criar Usuário')
@section('content')

        <div  class="usuarios div-center-column w-100"
                style="padding-top: 99px;">

                <h1  style="padding-top: 80px; text-align: center;">Criar Usuário</h1>
            <div class="container section container-platform div-center-column"
                style="margin-top: 15px; height: 100%;">

                <form action="{{ route('usuario-registrar') }}" id="novo-local-form"  class="w-100 needs-validation form-center"  method="post" enctype="multipart/form-data" novalidate>
                    @csrf

                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                        <div class="col-md-8">
                            <label for="cliente_nome" class="form-label"> Nome Completo*:</label>
                            <input type="text" class="form-control input-text" name="cliente_nome" id="cliente_nome" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p" id="cliente_nome_mensagem">Campo obrigatório</p>
                            </div>

                        </div>
                    </div>
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                        <div class="col-md-4">
                            <label for="cliente_celular" class="form-label"> Celular*:</label>
                            <input type="text"  class="form-control" name="cliente_celular" id="cliente_celular" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p" id="cliente_celular_mensagem">Campo obrigatório</p>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <label for="cliente_email" class="form-label"> Email*:</label>
                            <input type="email" class="form-control" name="cliente_email" id="cliente_email" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                    </div>
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                        <div class="col-md-4">
                            <label for="cliente_senha" class="form-label"> Senha*:</label>
                            <input type="password"  class="form-control" name="cliente_senha" id="cliente_senha" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p" id="cliente_celular_mensagem">Campo obrigatório</p>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <label for="cliente_confirmar_senha" class="form-label"> Confirmar Senha*:</label>
                            <input type="password" class="form-control" name="cliente_confirmar_senha" id="cliente_confirmar_senha" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                    </div>
                    
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                        <div class="col-md-8">
                            <label for="cliente_cpf_cnpj" class="form-label"> CPF/CNPJ*:</label>
                            <input type="text" class="form-control" name="cliente_cpf_cnpj" id="cliente_cpf_cnpj" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                    </div>

                    <h5>Permissões:</h5>
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                        <div class="col-md-8">
                            <div class="form-check form-switch">
                                <input class="form-check-input" name="checkbox_efi" type="checkbox" role="switch" id="checkboxEfi">
                                <label class="form-check-label" for="flexSwitchCheckDefault">Pix</label>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                        <div class="col-md-8">
                            <div class="form-check form-switch">
                                <input class="form-check-input" name="checkbox_pagbank" type="checkbox" role="switch" id="checkboxPagbank">
                                <label class="form-check-label" for="flexSwitchCheckDefault">Máquininha de cartão (Pagbank)</label>
                            </div>
                        </div>
                    </div>

                    <div style="display:flex; justify-content: center; align-items: center; margin-top: 50px;">
                        <button class="btn btn-primary"  type="submit">Criar usuário</button>
                    </div>
                </form>
                            @if(session('success'))

                                <div class="modal fade show" id="modalSuccess" tabindex="-1" aria-labelledby="exampleModalCenterTitle" aria-modal="true" role="dialog" style="display: block;">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalCenterTitle">Sucesso</h1>
                                                <button type="button" class="btn-close" onclick="fechaModal('modalSuccess')" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>{{ session('success') }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary" onclick="fechaModal('modalSuccess')" data-dismiss="modal" aria-label="Close">Ok</button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="modal-backdrop fade show" id="modalSuccess-backdrop"></div>
                            @elseif(session('error'))
                                <div class="modal fade show" id="modalError" tabindex="-1" aria-labelledby="exampleModalCenterTitle" aria-modal="true" role="dialog" style="display: block;">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">

                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="exampleModalCenterTitle">Erro</h1>
                                                    <button type="button" class="btn-close" onclick="fechaModal('modalError')" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>{{ session('error') }}</p>
                                                </div>
                                                <div class="modal-footer">
                                                        <button type="button" class="btn btn-primary" onclick="fechaModal('modalError')" data-bs-dismiss="modal" aria-label="Close">OK</button>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-backdrop fade show" id="modalError-backdrop"></div>
                            @endif
            </div>
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
    });
</script>

@endsection


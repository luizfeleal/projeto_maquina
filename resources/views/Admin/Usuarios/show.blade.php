@extends('layouts.app')
@section('title', 'Detalhar Usuário')
@section('content')

        <div  class="usuarios div-center-column w-100"
                style="padding-top: 99px;">

                <h1  style="padding-top: 80px; text-align: center;">Detalhar Usuário</h1>
            <div class="container section container-platform div-center-column"
                style="margin-top: 15px; height: 100%;">

                <form action="{{ route('usuario-registrar') }}" id="novo-local-form"  class="w-100 needs-validation form-center"  method="post" enctype="multipart/form-data" novalidate>
                    @csrf

                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                        <div class="col-md-8">
                            <label for="cliente_nome" class="form-label"> Nome Completo*:</label>
                            <input type="text" value="{{$cliente['cliente_nome']}}" class="form-control input-text" name="cliente_nome" id="cliente_nome" disabled>
                            <div class="invalid-feedback">
                                <p class="invalid-p" id="cliente_nome_mensagem">Campo obrigatório</p>
                            </div>

                        </div>
                       
                    </div>
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                        <div class="col-md-4">
                            <label for="cliente_celular" class="form-label"> Celular*:</label>
                            <input type="text"  class="form-control" value="{{$cliente['cliente_celular']}}" name="cliente_celular" id="cliente_celular" disabled>
                            <div class="invalid-feedback">
                                <p class="invalid-p" id="cliente_celular_mensagem">Campo obrigatório</p>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <label for="cliente_email" class="form-label"> Email*:</label>
                            <input type="email" class="form-control" value="{{$cliente['cliente_email']}}" name="cliente_email" id="cliente_email" disabled>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                    </div>
                    
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                        <div class="col-md-8">
                            <label for="cliente_cpf_cnpj" class="form-label"> CPF/CNPJ*:</label>
                            <input type="text" class="form-control" value="{{$cliente['cliente_cpf_cnpj']}}" name="cliente_cpf_cnpj" id="cliente_cpf_cnpj" disabled>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                    </div>
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                        <div class="col-md-4">
                            <label for="cliente_id" class="form-label">Client ID*:</label>
                            <input type="text" class="form-control" value="************..." name="cliente_id" id="cliente_id" disabled>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <label for="cliente_secret" class="form-label">Client Secret*:</label>
                            <input type="text" class="form-control" value="************..." name="cliente_secret" id="cliente_secret" disabled>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                        
                    </div>


                    <h2 style="margin-top: 50px;">Locais</h2>

                    @if(empty($locais))
                            <p style="margin-top: 25px; margin-bottom: 25px;">Nenhum local encontrado</p>
                    @else

                    @foreach($locais as $local)
                        
                            <h5 style="margin-top: 25px;"><i class="fa-solid fa-location-dot icon-sidebar" style=" font-size: 25px;padding-right:5px;"></i> <strong>Nome:</strong> {{$local['local_nome']}}</h5>
                       
                    @endforeach
                    @endif
                    
                    <h2 style="margin-top: 50px;">Credenciais configuradas</h2>

                    <div style="text-align: left;">
                        <h5>EFÍ: <span>{{!empty($credencial_efi) ? 'Configurado' : 'Não Configurado'}}</span></h5>
                        <h5>Pagbank: {{!empty($credencial_pagbank) ? 'Configurado' : 'Não Configurado'}}</h5>
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
            preencherEnderecoFocoNumero('cliente_cidade', 'cliente_estado', 'cliente_logradouro', 'cliente_bairro', 'cliente_numero', dadoEndereco)
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


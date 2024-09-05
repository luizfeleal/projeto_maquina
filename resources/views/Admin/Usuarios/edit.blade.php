@extends('layouts.app')
@section('title', 'Atualizar Usuário')
@section('content')

        <div  class="usuarios div-center-column w-100"
                style="padding-top: 99px;">

                <h1  style="padding-top: 80px; text-align: center;">Atualizar Usuário</h1>
            <div class="container section container-platform div-center-column"
                style="margin-top: 15px; height: 100%;">

                <form action="{{ route('usuario-atualizar') }}" id="novo-local-form"  class="w-100 needs-validation form-center"  method="post" enctype="multipart/form-data" novalidate>
                    @csrf

                    <input type="hidden" name="id_cliente" value="{{$cliente['id_cliente']}}">
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                        <div class="col-md-4">
                            <label for="cliente_nome" class="form-label"> Nome Completo*:</label>
                            <input type="text" class="form-control input-text" name="cliente_nome" id="cliente_nome" value="{{$cliente['cliente_nome']}}" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p" id="cliente_nome_mensagem">Campo obrigatório</p>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <label for="cliente_data_nascimento" class="form-label"> Data Nascimento*:</label>
                            <input type="text" class="form-control input-text mascara-dinamica" name="cliente_data_nascimento" value="{{$cliente['cliente_data_nascimento']}}" id="cliente_data_nascimento" data-mask="00/00/0000" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p" id="select_tipo_mensagem">Campo obrigatório</p>
                            </div>

                        </div>
                    </div>
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                        <div class="col-md-4">
                            <label for="cliente_celular" class="form-label"> Celular*:</label>
                            <input type="text"  class="form-control" name="cliente_celular" id="cliente_celular" value="{{$cliente['cliente_celular']}}" required>
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
                        <div class="col-md-4">
                            <label for="cliente_cep" class="form-label">CEP*:</label>
                            <input type="text" class="form-control" name="cliente_cep" value="{{$cliente['cliente_cep']}}" id="cliente_cep" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <label for="cliente_logradouro" class="form-label">Logradouro*:</label>
                            <input type="text" class="form-control" name="cliente_logradouro" value="{{$cliente['cliente_logradouro']}}" id="cliente_logradouro" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                    </div>

                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                        <div class="col-md-4">
                            <label for="cliente_cidade" class="form-label">Cidade*:</label>
                            <input type="text" class="form-control" name="cliente_cidade" id="cliente_cidade" value="{{$cliente['cliente_cidade']}}" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <label for="cliente_bairro" class="form-label">Bairro*:</label>
                            <input type="text" class="form-control" name="cliente_bairro" id="cliente_bairro" value="{{$cliente['cliente_bairro']}}" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                    </div>
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                        <div class="col-md-4">
                            <label for="cliente_complemento" class="form-label">Complemento:</label>
                            <input type="text" class="form-control" name="cliente_complemento" id="cliente_complemento" value="{{$cliente['cliente_complemento']}}">
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                        <div class="col-md-2">
                            <label for="cliente_uf" class="form-label">UF*:</label>
                            <input type="text" class="form-control" name="cliente_uf" id="cliente_uf" value="{{$cliente['cliente_uf']}}" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                        <div class="col-md-2">
                            <label for="cliente_numero" class="form-label">Número*:</label>
                            <input type="text" class="form-control" name="cliente_numero" id="cliente_numero" value="{{$cliente['cliente_numero']}}" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                    </div>

                    <div style="display:flex; justify-content: center; align-items: center; margin-top: 50px;">
                        <button class="btn btn-primary"  type="submit">Atualizar usuário</button>
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


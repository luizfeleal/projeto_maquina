@extends('layouts.app')
@section('title', 'Detalhar Máquina')
@section('content')

<div id="maquinas" class="maquina div-center-column w-100"
    style="padding-top: 99px;">

    <h1 style="padding-top: 80px; text-align: center;">Detalhar Máquina</h1>
    <div class="container section container-platform div-center-column"
        style="margin-top: 15px; height: 100%;">


        <div style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-top: 50px;">

            @if($maquinas['maquina_status'] == 0)
            <p><strong>Status: </strong> <i class="fa-solid fa-circle text-danger"></i></p>
            @else
            <p><strong>Status: </strong> <i class="fa-solid fa-circle text-success"></i></p>
            @endif
        </div>


        <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-top: 100px;">
            <div class="col-md-4">
                <label for="maquina_nome" class="form-label">Nome Máquina:</label>
                <input type="text" name="maquina_nome" id="maquina_nome" value="{{$maquinas['maquina_nome']}}" class="form-control input-text" placeholder="Nome da Máquina" aria-label="Nome da Máquina" disabled>
                <div class="invalid-feedback">
                    <p class="invalid-p" id="maquina_nome_mensagem"></p>
                </div>

            </div>
            <div class="col-md-4">
                <label for="id_placa" class="form-label">ID da placa:</label>
                <input type="text" name="id_placa" id="id_placa" value="{{$maquinas['id_placa']}}" class="form-control input-text" placeholder="Nome da Máquina" aria-label="Nome da Máquina" disabled>
                <div class="invalid-feedback">
                    <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                </div>

            </div>
        </div>

        <div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-top: 10px; width: 100%;">
            <div class="col-md-8">
                <label for="local_nome" class="form-label">Local:</label>
                <input type="text" name="local_nome" id="local_nome" value="{{$locais['local_nome']}}" class="form-control input-text" placeholder="Local" aria-label="Local" disabled>

                <div class="invalid-feedback">
                    <p class="invalid-p" id="select_local_mensagem">Campo obrigatório</p>
                </div>
            </div>
        </div>
        <div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-top: 10px; width: 100%;">
            <div class="col-md-8">
                <label for="select-cliente" class="form-label">Cliente(s):</label>
                <select class="select-cliente js-example-basic-multiple js-states form-control" id="select-cliente" placeholder="Selecione" name="select-cliente[]" multiple="multiple" disabled>
                    @foreach($clientes as $cliente)
                    <option value="{{$cliente['id_cliente']}}" selected>{{$cliente['cliente_nome']}}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback">
                    <p class="invalid-p" id="select_cliente_mensagem">Campo obrigatório</p>
                </div>

            </div>
        </div>

        <div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-top: 10px;  width: 100%; margin-bottom: 20px;">
            <div class="col-md-4">
                <label for="ultimo_acesso" class="form-label">Último contato:</label>
                <input type="text" name="ultimo_acesso" id="ultimo_acesso" value="{{$maquinas['maquina_ultimo_contato']}}" class="form-control input-text" placeholder="Nome da Máquina" aria-label="Nome da Máquina" disabled>
                <div class="invalid-feedback">
                    <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                </div>

            </div>
            <div class="col-md-4">
                <label for="data_criacao" class="form-label">Data de Criação:</label>
                <input type="text" name="data_criacao" id="data_criacao" value="{{$maquinas['data_criacao']}}" class="form-control input-text" placeholder="Nome da Máquina" aria-label="Nome da Máquina" disabled>
                <div class="invalid-feedback">
                    <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                </div>

            </div>
        </div>

        <h5>Configurações:</h5>
        <div class="row"style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
            <div class="col-md-8">
                <p><b>Máquina de cartão: </b> {{$possuiMaquinaCartaoAssociada ? 'Sim' : 'Não'}}</p>
            </div>
        </div>
        <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
            <div class="col-md-8">
                <p><b>QR Code associado: </b> {{$possuiQrCode ? 'Sim' : 'Não'}}</p>
            </div>
        </div>

        <h5>Permissões:</h5>
        <form action="{{route('maquinas-atualizar')}}" method="POST" id="atualizar-permissao" style="width: 100%;">
            <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                <div class="col-md-8">
                    <div class="form-check form-switch">
                        @if($maquinas['bloqueio_jogada_efi'] == 1)

                        <input class="form-check-input" name="bloqueio_jogada_efi" type="checkbox" role="switch" id="checkboxEfi" checked>
                        @else

                        <input class="form-check-input" name="bloqueio_jogada_efi" type="checkbox" role="switch" id="checkboxEfi">
                        @endif
                        <label class="form-check-label" for="flexSwitchCheckDefault">Bloquear jogada Pix</label>
                    </div>
                </div>
            </div>

            <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                <div class="col-md-8">
                    <div class="form-check form-switch">
                        @if($maquinas['bloqueio_jogada_pagbank'] == 1)
                        <input class="form-check-input" name="bloqueio_jogada_pagbank" type="checkbox" role="switch" id="checkboxPagbank" checked>
                        @else
                        <input class="form-check-input" name="bloqueio_jogada_pagbank" type="checkbox" role="switch" id="checkboxPagbank">
                        @endif
                        <label class="form-check-label" for="flexSwitchCheckDefault">Bloquear jogada Máquininiha</label>
                    </div>
                </div>
            </div>

            <div style="display:flex; justify-content: center; align-items: center;  margin-top: 50px;">
                <button class="btn btn-primary" type="submit">Salvar permissões</button>
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
                        <button type="button" class="btn btn-primary" onclick="fechaModal('modalSuccess')" data-dismiss="modal" aria-label="Close">OK</button>
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

        $('.select-cliente').select2({
            theme: 'bootstrap-5'
        });
        $('.select-local').select2({
            theme: 'bootstrap-5'
        });
        $(".select-local").on('change', () => {
            setComplementoCliente()
        });
        $(".select-cliente").on('change', () => {
            setComplementoLocal()
        });

        $("#botao-gerar-id-placa").on('click', async function() {

            showLoader();
            var url = $("#url_web").val() + '/api/gerarIdPlaca';

            // Configurações da requisição
            const options = {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            };

            try {
                const response = await fetch(url, options);
                if (!response.ok) {
                    throw new Error('Erro ao enviar a requisição: ' + response.statusText);
                }

                const result = await response.json();
                $("#id_placa").val(result.id_placa)
                $("#id_placa_input").val(result.id_placa)
                $("#id_placa").removeClass('is-invalid')
            } catch (error) {
                console.log('deu erro:', error); // Trate qualquer erro
            } finally {
                hideLoader();
            }
        });

        //Eventos de validação

        $("#maquina_nome").on('blur', () => {
            validarCampoNome('maquina_nome', 'maquina_nome_mensagem');
        });

        $(".select-local").on('select2:close', () => {
            validarSelectLocalCliente('select-local', 'select_local_mensagem');
        });
        $(".select-local").on('change', () => {
            validarSelectLocalCliente('select-local', 'select_local_mensagem');
        });

        $("#select-cliente").on('select2:close', () => {
            validarSelectLocalCliente('select-cliente', 'select_cliente_mensagem');
        });
        $("#select-cliente").on('change', () => {
            validarSelectLocalCliente('select-cliente', 'select_cliente_mensagem');
        });

    });
</script>

@endsection
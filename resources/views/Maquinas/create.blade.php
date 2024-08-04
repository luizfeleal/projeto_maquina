@extends('layouts.app')
@section('title', 'Criar máquina')
@section('content')

        <div id="maquinas" class="maquina div-center-column w-100"
                style="padding-top: 99px;">

                <h1  style="padding-top: 80px; text-align: center;">Máquinas -> criar máquinas</h1>
            <div class="container section container-platform div-center-column"
                style="margin-top: 15px; height: 100%;">
                <input type="hidden" id="input_locais" value="{{json_encode($locais)}}">
                <input type="hidden" id="input_clientes" value="{{json_encode($clientes)}}">
                <input type="hidden" id="url_web" value="{{env('APP_URL')}}">

                <form action="{{route('maquinas-registrar')}}" id="nova-maquina" class="w-100 needs-validation" novalidate>
                    @csrf

                    <input type="hidden" name="id_placa_input" id="id_placa_input" value="">

                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-top: 150px;">
                        <div class="col-md-6">
                            <label for="maquina_nome" class="form-label">Nome Máquina*:</label>
                            <input type="text" name="maquina_nome" id="maquina_nome" class="form-control input-text" placeholder="Nome da Máquina" aria-label="Nome da Máquina" required>
                            <div class="invalid-feedback" >
                                <p class="invalid-p" id="maquina_nome_mensagem"></p>
                            </div>

                        </div>
                    </div>
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-top: 10px;  width: 100%; ">
                        <div class="col-md-6">
                            <label for="id_placa" class="form-label">Gerar ID da placa* <a id="botao-gerar-id-placa"><i class="fa-solid fa-repeat"></i></a></label>
                            <input type="text" name="id_placa" id="id_placa" class="form-control required input-text" placeholder="Id da placa" aria-label="ID da placa" required disabled>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                    </div>
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-top: 10px; width: 100%;">
                        <div class="col-md-6">
                            <label for="select-local1" class="form-label">Local*:</label>
                            <select class="select-local js-example-basic-multiple js-states form-control" id="select-local" placeholder="Selecione" name="select-local"  required>

                                <option value="">Selecione...</option>
                            @foreach($locais as $local)
                                <option value="{{$local['id_local']}}">{{$local['local_nome']}}</option>
                            @endforeach
                            </select>
                            <div class="invalid-feedback">
                                <p class="invalid-p" id="select_local_mensagem">Campo obrigatório</p>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-top: 10px; width: 100%;">
                        <div class="col-md-6">
                            <label for="select-cliente1" class="form-label">Cliente*:</label>
                            <select class="select-cliente js-example-basic-multiple js-states form-control" id="select-cliente" placeholder="Selecione" name="select-cliente"  required>
                            <option value="">Selecione...</option>
                            @foreach($clientes as $cliente)
                                <option value="{{$cliente['id_cliente']}}">{{$cliente['cliente_nome']}}</option>
                            @endforeach
                            </select>
                            <div class="invalid-feedback">
                                <p class="invalid-p" id="select_cliente_mensagem">Campo obrigatório</p>
                            </div>

                        </div>
                    </div>

                    <div style="display:flex; justify-content: center; align-items: center;  margin-top: 50px;">
                        <button class="btn btn-primary"  data-bs-toggle="modal" data-bs-target="#ModalCenterCriar" onclick="setMaquinaNome('.modal-body', '#maquina_nome')" type="button">Criar</button>
                    </div>
                </form>

                
                    <div class="modal fade" id="ModalCenterCriar" tabindex="-1" aria-labelledby="ModalCenterCriar" aria-modal="true" role="dialog">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="ModalCenterTitleCriar">Criar</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Deseja criar a Máquina ?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secundary" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>
                                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal" aria-label="Close" onclick="sendFormCriar('nova-maquina')">Sim</button>
                                </div>
                            </div>
                        </div>
                    </div>
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
        })
        $(".select-cliente").on('change', () => {
            setComplementoLocal()
        })

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


@extends('layouts.app')
@section('title', 'Criar local')
@section('content')

        <div id="local-incluir-usuario" class="local div-center-column w-100"
                style="padding-top: 99px;">

                <h1  style="padding-top: 80px; text-align: center;">Incluir usuários no Local</h1>
            <div class="container section container-platform div-center-column"
                style="margin-top: 15px; height: 100%;">

                <form action="{{ route('local-registrar') }}" id="novo-local-form" class="w-100">
                    @csrf

                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                        <div class="col-md-6">
                            <p >Selecione o local que deseja incluir um novo usuário:</p>
                            <label for="nome_local" class="form-label">Local*:</label>
                            <select class="select-local js-example-basic-multiple js-states form-control" id="select-local" placeholder="Selecione" name="select-local">

                            <option value="" selected>Selecione</option>
                            @foreach($locais as $local)
                                <option value="{{$local['id_local']}}">{{$local['local_nome']}}</option>
                            @endforeach
                            </select>
                            <div class="invalid-feedback">
                                <p class="invalid-p" id="select_local_mensagem">Campo obrigatório</p>
                            </div>

                        </div>
                    </div>
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;  width: 100%; ">
                        <div class="col-md-6">
                            <label for="select-cliente" class="form-label">Selecione o(s) cliente(s) que deseja incluir*:</label>
                            <select class="select-cliente js-example-basic-multiple js-states form-control" id="select-cliente" placeholder="Selecione" name="select-cliente[]" multiple="multiple">

                            @foreach($clientes as $cliente)
                                <option value="{{$cliente['id_cliente']}}">{{$cliente['cliente_nome']}}</option>
                            @endforeach
                            </select>
                            <div class="invalid-feedback">
                                <p class="invalid-p" id="select_cliente_mensagem">Campo obrigatório</p>
                            </div>

                        </div>
                    </div>

                    <div style="display:flex; justify-content: center; align-items: center; margin-top: 50px;">
                        <button class="btn btn-primary"  data-bs-toggle="modal" data-bs-target="#ModalCenterCriar" onclick="setMensagemIncluirUsuario('.modal-body', '#select-local', '#select-cliente')" type="button">Incluir</button>
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
                                    <p>Realmente deseja incluir o(s) usuário(s) no local? Ao incluir, o usuário verá todas as informações deste local.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secundary" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>
                                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal" aria-label="Close" onclick="sendFormCriarLocal()">Sim</button>
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


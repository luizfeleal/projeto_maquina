@extends('layouts.app')
@section('title', 'Incluir Máquina Cartão')
@section('content')

        <div id="local-incluir-usuario" class="local div-center-column w-100"
                style="padding-top: 99px;">

                <h1  style="padding-top: 80px; text-align: center;">Incluir Máquina Cartão</h1>
            <div class="container section container-platform div-center-column"
                style="margin-top: 15px; height: 100%;">


                <form action="{{ route('maquinas-cartao-registrar') }}" method="post" id="incluir_usuario_local_form" class="w-100">
                    @csrf

                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                        <div class="col-md-6">
                            <p >Selecione a máquina que deseja incluir a máquina de cartão:</p>
                            <label for="maquina" class="form-label">Máquina*:</label>
                            <select class="select-maquina js-example-basic-multiple js-states form-control" id="select-maquina" placeholder="Selecione" name="select-maquina">

                            <option value="" selected>Selecione</option>
                            @foreach($maquinas_exibir as $maquina)
                                <option value="{{$maquina['id_maquina']}}">{{$maquina['maquina_nome']}}</option>
                            @endforeach
                            </select>
                            <div class="invalid-feedback">
                                <p class="invalid-p" id="select_local_mensagem">Campo obrigatório</p>
                            </div>

                        </div>
                    </div>
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;  width: 100%; ">
                        <div class="col-md-6">
                            <label for="device" class="form-label">Insira o número da máquina de cartão*:</label>
                            <input type="text" id="device" name="device" class="form-control input-text" placeholder="Número" aria-label="Número" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p" id="select_cliente_mensagem">Campo obrigatório</p>
                            </div>

                        </div>
                    </div>

                    <div style="display:flex; justify-content: center; align-items: center; margin-top: 50px;">
                        <button class="btn btn-primary"  type="submit">Cadastrar</button>
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
                                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal" aria-label="Close" onclick="sendFormIncluirUsuarioLocal()">Sim</button>
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
        $('#select-maquina').select2({
            theme: 'bootstrap-5'
        });
    });
</script>

@endsection


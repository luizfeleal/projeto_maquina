@extends('layouts.app')
@section('title', 'Planos')
@section('content')

        <div id="planos" class="planos div-center-column w-100"
                style="padding-top: 99px;">

                <h1  style="padding-top: 80px; text-align: center;">Máquinas</h1>
            <div class="container section container-platform div-center-column"
                style="margin-top: 15px; height: 100%;">

                <form action="" id="nova-maquina" class="w-100">
                    @csrf

                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-top: 150px;">
                        <div class="col-md-6">
                            <label for="plano_nome" class="form-label">Nome Máquina*:</label>
                            <input type="text" name="plano_nome" id="plano_nome" class="form-control input-text" placeholder="Nome do Plano" aria-label="Nome do Plano" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                    </div>
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;  width: 100%; ">
                        <div class="col-md-6">
                            <label for="id_placa" class="form-label">Gerar ID da placa*:</label>
                            <input type="text" name="id_placa" id="id_placa" class="form-control required input-text" placeholder="Id da placa" aria-label="ID da placa" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                    </div>
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center; width: 100%; ">
                        <div class="col-md-6">
                            <label for="maquina_local" class="form-label">Local*:</label>
                            <input type="text" name="maquina_local" id="maquina_local" class="form-control required input-text" placeholder="Local da máquina" aria-label="Local da máquina" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                    </div>
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center; width: 100%;">
                        <div class="col-md-6">
                            <label for="cliente" class="form-label">Cliente*:</label>
                            <input type="text" name="cliente" id="cliente" class="form-control required input-text" placeholder="cliente" aria-label="cliente" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                    </div>
                </form>

                <div class="modal fade" id="ModalAtivarPlano" tabindex="-1" aria-labelledby="ModalAtivarPlanoTitle" aria-modal="true" role="dialog">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">

                            <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="ModalAtivarPlanoTitle">Ativar</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Tem certeza que deseja Ativar esse plano?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <form action="/" method="post">
                                            @csrf
                                            <input type="hidden" name="id_plano_ativar" id="id_plano_ativar" value="">
                                            <input type="hidden" name="ativo" value="1">
                                            <button type="button" class="btn btn-secundary" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>
                                            <button type="submit" class="btn btn-primary">Sim</button>
                                        </form>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="exampleModalCenter" tabindex="-1" aria-labelledby="exampleModalCenterTitle" aria-modal="true" role="dialog">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">

                            <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalCenterTitle">Inativar</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Tem certeza que deseja inativar esse plano?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <form action="/" method="post">
                                            @csrf
                                            <input type="hidden" name="id_plano_inativar" id="id_plano_inativar" value="">
                                            <input type="hidden" name="ativo" value="0">
                                            <button type="button" class="btn btn-secundary" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>
                                            <button type="submit" class="btn btn-primary">Sim</button>
                                        </form>
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

@endsection


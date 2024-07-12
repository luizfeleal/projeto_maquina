@extends('layouts.app')
@section('title', 'Relatórios')
@section('content')

        <div id="relatorios" class="relatorios w-100 div-center-column"
                style=" padding-top: 99px; padding-bottom: 100px;">

                <h1 style="padding-top: 80px; text-align: center;">Relatórios</h1>
            <div class="container section container-platform div-center-column"
                style=" height: 100%;">



                        <!--<div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-bottom: 20px; width: 100%; margin-top: 50px;">-->
                        <div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-bottom: 20px; width: 100%; margin-top: 100px; ">
                            <div class="col-md-3" style="cursor: pointer;">
                                <button type="button" class="btn btn-primary  w-100 botao-acionado" data-bs-toggle="collapse" href="#multiCollapseExample1" role="button" aria-expanded="true" aria-controls="multiCollapseExample1">Total Transações</button>
                            </div>
                            <div class="col-md-3" style="cursor: pointer;">
                                <button type="button" class="btn btn-primary w-100"  data-bs-toggle="collapse" data-bs-target="#multiCollapseExample2" aria-expanded="false" aria-controls="multiCollapseExample2">Taxas de Descontos</button>
                            </div>
                            <div class="col-md-3" style="cursor: pointer;">
                                <button type="button" class="btn btn-primary w-100"  data-bs-toggle="collapse" data-bs-target="#multiCollapseExample3" aria-expanded="false" aria-controls="multiCollapseExample3">Relatório de erros</button>
                            </div>
                            <div class="col-md-3" style="cursor: pointer;">
                                <form action="{{ route('relatorio-criar') }}" method="post" class="form-center">
                                @csrf
                                    <input type="hidden" name="tipo" value="maquinasOnOff">
                                    <button type="submit" class="btn btn-primary w-100">Máquinas Online/Off-line</button>
                                </form>
                            </div>
                            </div>
                        </div>
                        <!--RELATÓRIO DE TRANSAÇÕES-->
                        <div class="collapse multi-collapse collapse show" id="multiCollapseExample1" style="width: 100%;">
                            <form action="{{ route('relatorio-criar') }}" method="post" class="form-center">
                                @csrf
                                <input type="hidden" name="tipo" value="recebimento">
                                <div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-bottom: 20px; width: 100%;">
                                    <div class="col-md-4">
                                        <label for="id_usuario" class="form-label">Usuário (back-ofice):</label>
                                        <select class="form-select" name="id_usuario" aria-label="Default select example">
                                            <option value="" selected>Escolher...</option>

                                            <option value="1">1</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                        </div>

                                    </div>
                                    <div class="col-md-4">
                                        <label for="id_termo" class="form-label">Termo:</label>
                                        <select class="form-select" name="id_termo" aria-label="Default select example">
                                            <option value="" selected>Escolher...</option>
                                            
                                        </select>
                                        <div class="invalid-feedback">
                                            <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                        </div>

                                    </div>

                                </div>
                                <div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-bottom: 20px; width: 100%;">
                                    <div class="col-md-4">
                                        <label for="situacao_recebimento" class="form-label">Situação:</label>
                                        <select class="form-select" name="situacao_recebimento" id="situacao_recebimento" aria-label="Default select example">
                                            <option value="" selected>Escolher...</option>
                                            <option value="A Pagar">A Pagar</option>
                                            <option value="Pago">Pago</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                        </div>

                                    </div>
                                    <div class="col-md-2">
                                        <label for="data_inicio" class="form-label">Data início:</label>
                                        <input type="date" name="data_inicio" id="data_extrato_inicio" class="form-control required">
                                        <div class="invalid-feedback">
                                            <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                        </div>

                                    </div>
                                    <div class="col-md-2">
                                        <label for="data_fim" class="form-label">Data fim:</label>
                                        <input type="date" name="data_fim" id="data_extrato_fim" class="form-control required " >
                                        <div class="invalid-feedback">
                                            <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                        </div>

                                    </div>
                                </div>
                                <div class="div-button" style="padding-top: 70px;">
                                    <button class="btn btn-primary" type="submit" style="width: 120px;">Gerar</button>
                                </div>
                            </form>

                        </div>
                        <!--RELATÓRIO DE TAXAS DE DESCONTO-->
                        <div class="collapse multi-collapse" id="multiCollapseExample2" style="width: 100%;">
                            <form action="{{ route('relatorio-criar') }}" method="post" class="form-center">
                                @csrf
                                <input type="hidden" name="tipo" value="guias">
                                <div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-bottom: 20px; width: 100%;">
                                    <div class="col-md-4">
                                        <label for="situacao_procedimento" class="form-label">Situação Procedimento:</label>
                                        <select class="form-select" name="situacao_procedimento" aria-label="Default select example">
                                            <option value="" selected>Escolher...</option>
                                            <option value="A Fazer">A Fazer</option>
                                            <option value="Realizado">Realizado</option>
                                            <option value="Cancelado">Cancelado</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                        </div>

                                    </div>
                                    <div class="col-md-4">
                                        <label for="situacao_guia" class="form-label">Situação Guia:</label>
                                        <select class="form-select" name="situacao_guia" aria-label="Default select example">
                                            <option value="" selected>Escolher...</option>
                                            <option value="Autorizada">Autorizada</option>
                                            <option value="Fechada">Fechada</option>
                                            <option value="Cancelada">Cancelada</option>
                                            <option value="Saldo Insuficiente">Saldo Insuficiente</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                        </div>

                                    </div>
                                </div>
                                <div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-bottom: 20px; width: 100%;">
                                    <div class="col-md-4">
                                        <label for="id_termo" class="form-label">Termo:</label>
                                        <select class="form-select" name="id_termo" aria-label="Default select example">
                                            <option value="" selected>Escolher...</option>
                                           
                                        </select>
                                        <div class="invalid-feedback">
                                            <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                        </div>

                                    </div>
                                    <div class="col-md-4">
                                        <label for="id_prestador" class="form-label">Prestador:</label>
                                        <select class="form-select" name="id_prestador" aria-label="Default select example">
                                            <option value="" selected>Escolher...</option>
                                            
                                        </select>
                                        <div class="invalid-feedback">
                                            <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                        </div>

                                    </div>
                                </div>
                                <div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-bottom: 20px; width: 100%;">
                                    <div class="col-md-4">
                                        <label for="id_prestador" class="form-label">Grupo Procedimentos:</label>
                                        <select class="form-select" name="grupo_procedimento" aria-label="Default select example">
                                            <option value="" selected>Escolher...</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                        </div>

                                    </div>
                                    <div class="col-md-2">
                                        <label for="range_valor_inicial" class="form-label">Valor (de):</label>
                                        <input type="text" class="form-control" name="range_valor_inicial" id="range_valor_inicial">
                                        <div class="invalid-feedback">
                                            <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                        </div>

                                    </div>
                                    <div class="col-md-2">
                                        <label for="range_valor_final" class="form-label">Valor (até):</label>
                                        <input type="text" class="form-control" name="range_valor_final" id="range_valor_final">
                                        <div class="invalid-feedback">
                                            <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                        </div>

                                    </div>
                                </div>

                                <div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-bottom: 20px; width: 100%;">
                                    <div class="col-md-4">
                                        <label for="data_inicio_guia" class="form-label">Data início:</label>
                                            <input type="date" class="form-control" name="data_inicio_guia" id="data_inicio_guia">
                                        <div class="invalid-feedback">
                                            <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                        </div>

                                    </div>
                                    <div class="col-md-4">
                                        <label for="data_fim_guia" class="form-label">Data fim:</label>
                                            <input type="date" class="form-control" name="data_fim_guia" id="data_fim_guia">
                                        <div class="invalid-feedback">
                                            <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                        </div>

                                    </div>
                                </div>

                                <div class="div-button" style="padding-top: 70px;">
                                    <button class="btn btn-primary" type="submit" style="width: 120px;">Gerar</button>
                                </div>
                            </form>
                        </div>

                        <!--RELATÓRIO DE ERROS-->
                        <div class="collapse multi-collapse" id="multiCollapseExample3" style="width: 100%;">
                        <form action="{{ route('relatorio-criar') }}" method="post" class="form-center">
                                @csrf
                                <input type="hidden" name="tipo" value="planos">
                            <div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-bottom: 20px; width: 100%;">
                                <div class="col-md-4">
                                    <label for="id_termo" class="form-label">Termo:</label>
                                    <select class="form-select" name="id_termo" aria-label="Default select example">
                                        <option value= " " selected>Escolher...</option>
                                            
                                    </select>
                                    <div class="invalid-feedback">
                                        <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                    </div>

                                </div>
                                <div class="col-md-4">
                                    <label for="id_plano" class="form-label">Plano:</label>
                                    <select class="form-select" name="id_plano" aria-label="Default select example">
                                        <option value= " " selected>Escolher...</option>
                                        
                                    </select>
                                    <div class="invalid-feedback">
                                        <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                    </div>

                                </div>

                            </div>
                            <div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-bottom: 20px; width: 100%;">
                                <div class="col-md-4">
                                    <label for="situacao_boleto" class="form-label">Situação Boleto:</label>
                                    <select class="form-select" name="situacao_boleto" aria-label="Default select example">
                                        <option value= " " selected>Escolher...</option>
                                        <option value="A Pagar">Aberto</option>
                                        <option value="Pago">Pago</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                    </div>

                                </div>
                                <div class="col-md-2">
                                    <label for="data_plano_inicio" class="form-label">Data início:</label>
                                    <input type="date" name="data_plano_inicio" id="data_plano_inicio" class="form-control required" placeholder="Código" aria-label="Código do Procedimento" >
                                    <div class="invalid-feedback">
                                        <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                    </div>

                                </div>
                                <div class="col-md-2">
                                    <label for="data_plano_fim" class="form-label">Data fim:</label>
                                    <input type="date" name="data_plano_fim" id="data_plano_fim" class="form-control required " placeholder="Código" aria-label="Código do Procedimento" >
                                    <div class="invalid-feedback">
                                        <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                    </div>

                                </div>
                            </div>

                            <div class="div-button" style="padding-top: 70px;">
                                    <button class="btn btn-primary" type="submit" style="width: 120px;">Gerar</button>
                            </div>
                        </form>

                        </div>

                    <!--</form>-->

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

        $(document).ready(function(){


            $('#example').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
                },
                "columns": [
                    null,
                    null,
                    null,
                ] // Use o array de objetos de coluna dinamicamente criado
            });
        });
    </script>

@endsection

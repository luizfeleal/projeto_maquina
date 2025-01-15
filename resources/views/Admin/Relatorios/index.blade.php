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
                                <button type="button" class="btn btn-primary  w-100" data-bs-toggle="collapse" href="#multiCollapseExample1" role="button" aria-expanded="true" aria-controls="multiCollapseExample1">Total Transações</button>
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
                        <div class="collapse multi-collapse " id="multiCollapseExample1" style="width: 100%;">
                            <form action="{{ route('relatorio-criar') }}" method="post" class="form-center">
                                @csrf
                                <input type="hidden" name="tipo" value="totalTransacoes">
                                <div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-bottom: 20px; width: 100%;">
                                    <div class="col-md-4">
                                        <label for="id_cliente" class="form-label">Cliente:</label>
                                       
                                            <select class="select-cliente-transacoes js-example-basic-multiple js-states form-control" id="id_cliente" placeholder="Selecione" name="id_cliente[]" multiple="multiple" >

                                                @foreach($clientes as $cliente)
                                                    <option value="{{$cliente['id_cliente']}}">{{$cliente['cliente_nome']}}</option>
                                                @endforeach
                                            </select>
                                        
                                        <div class="invalid-feedback">
                                            <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                        </div>

                                    </div>
                                    <div class="col-md-4">
                                        <label for="id_maquina" class="form-label">Maquina:</label>
                                        <select class="select-maquina-transacoes js-example-basic-multiple js-states form-control" id="id_maquina" placeholder="Selecione" name="id_maquina[]" multiple="multiple" >

                                                @foreach($maquinas as $maquina)
                                                    <option value="{{$maquina['id_maquina']}}">{{$maquina['maquina_nome']}}</option>
                                                @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                        </div>

                                    </div>

                                </div>
                                <div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-bottom: 20px; width: 100%;">
                                    <!--<div class="col-md-4">
                                        <label for="id_local" class="form-label">Local:</label>
                                        <select class="select-local-transacoes js-example-basic-multiple js-states form-control" id="id_local" placeholder="Selecione" name="id_local[]" multiple="multiple" >

                                            @foreach($locais as $local)
                                                <option value="{{$local['id_local']}}">{{$local['local_nome']}}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                        </div>

                                    </div>-->
                                    <div class="col-md-8">
                                        <label for="tipo_transacao" class="form-label">Tipo:</label>
                                        <select class="form-select" name="tipo_transacao" id="tipo_transacao" aria-label="Default select example">
                                            <option value="" selected>Escolher...</option>
                                            <option value="PIX">Pix</option>
                                            <option value="Cartão">Cartão</option>
                                            <option value="Dinheiro">Dinheiro</option>
                                            <option value="Estorno">Estorno</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                        </div>

                                    </div>

                                </div>
                                <div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-bottom: 20px; width: 100%;">
                                    
                                    <div class="col-md-4">
                                        <label for="data_inicio" class="form-label">Data início:</label>
                                        <input type="date" name="data_inicio" id="data_extrato_inicio" class="form-control required">
                                        <div class="invalid-feedback">
                                            <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                        </div>

                                    </div>
                                    <div class="col-md-4">
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
                                <input type="hidden" name="tipo" value="taxasDesconto">
                                <div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-bottom: 20px; width: 100%;">
                                    <div class="col-md-4">
                                        <label for="id_cliente" class="form-label">Cliente:</label>
                                        <select class="select-cliente js-example-basic-multiple js-states form-control" id="id_cliente_taxa" placeholder="Selecione" name="id_cliente[]" multiple="multiple" >

                                                @foreach($clientes as $cliente)
                                                    <option value="{{$cliente['id_cliente']}}">{{$cliente['cliente_nome']}}</option>
                                                @endforeach
                                            </select>
                                        <div class="invalid-feedback">
                                            <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                        </div>

                                    </div>
                                    <div class="col-md-4">
                                        <label for="id_maquina" class="form-label">Maquina:</label>
                                        <select class="select-maquina js-example-basic-multiple js-states form-control" id="id_maquina_taxa" placeholder="Selecione" name="id_maquina[]" multiple="multiple" >

                                                @foreach($maquinas as $maquina)
                                                    <option value="{{$maquina['id_maquina']}}">{{$maquina['maquina_nome']}}</option>
                                                @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                        </div>

                                    </div>

                                </div>
                                <div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-bottom: 20px; width: 100%;">
                                    <div class="col-md-8">
                                        <label for="id_local" class="form-label">Local:</label>
                                        <select class="select-local js-example-basic-multiple js-states form-control" id="id_local_taxa" placeholder="Selecione" name="id_local[]" multiple="multiple" >

                                            @foreach($locais as $local)
                                                <option value="{{$local['id_local']}}">{{$local['local_nome']}}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                        </div>

                                    </div>

                                </div>
                                <div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-bottom: 20px; width: 100%;">
                                    
                                    <div class="col-md-4">
                                        <label for="data_inicio" class="form-label">Data início:</label>
                                        <input type="date" name="data_inicio" id="data_extrato_inicio" class="form-control required">
                                        <div class="invalid-feedback">
                                            <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                        </div>

                                    </div>
                                    <div class="col-md-4">
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

                        <!--RELATÓRIO DE ERROS-->
                        <div class="collapse multi-collapse" id="multiCollapseExample3" style="width: 100%;">
                            <form action="{{ route('relatorio-criar') }}" method="post" class="form-center">
                                    @csrf
                                    <input type="hidden" name="tipo" value="relatorioErros">
                                    <div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-bottom: 20px; width: 100%;">
                                        
                                        <div class="col-md-4">
                                            <label for="id_maquina" class="form-label">Maquina:</label>
                                            <select class="select-maquina-erros js-example-basic-multiple js-states form-control" id="id_maquina_erros" placeholder="Selecione" name="id_maquina[]" multiple="multiple" >

                                                @foreach($maquinas as $maquina)
                                                    <option value="{{$maquina['id_maquina']}}">{{$maquina['maquina_nome']}}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">
                                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                            </div>

                                        </div>
                                        <div class="col-md-4">
                                            <label for="id_local" class="form-label">Local:</label>
                                            <select class="select-local-erros js-example-basic-multiple js-states form-control" id="id_local_erros" placeholder="Selecione" name="id_local[]" multiple="multiple" >

                                                @foreach($locais as $local)
                                                    <option value="{{$local['id_local']}}">{{$local['local_nome']}}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">
                                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                            </div>

                                        </div>

                                    </div>
                                    <div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-bottom: 20px; width: 100%;">
                                        
                                        <div class="col-md-4">
                                            <label for="data_inicio" class="form-label">Data início:</label>
                                            <input type="date" name="data_inicio" id="data_extrato_inicio" class="form-control required">
                                            <div class="invalid-feedback">
                                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                            </div>

                                        </div>
                                        <div class="col-md-4">
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

            $('.select-cliente-transacoes').select2({
                theme: 'bootstrap-5'
            });
            $('.select-maquina-transacoes').select2({
                theme: 'bootstrap-5'
            });
            $('.select-local-transacoes').select2({
                theme: 'bootstrap-5'
            });
            $('.select-cliente-erros').select2({
                theme: 'bootstrap-5'
            });
            $('.select-maquina-erros').select2({
                theme: 'bootstrap-5'
            });
            $('.select-local-erros').select2({
                theme: 'bootstrap-5'
            });
            $('.select-cliente').select2({
                theme: 'bootstrap-5'
            });
            $('.select-maquina').select2({
                theme: 'bootstrap-5'
            });
            $('.select-local').select2({
                theme: 'bootstrap-5'
            });
        });
    </script>

@endsection

@extends('layouts.app')

@section('title', 'Relatórios > Relatório de Erros')

@section('content')
        <div id="reports_maquinas_online_offline" class="relatorios div-center-column w-100"
                style="padding-top: 99px;">

                <div class="container section container-platform"
                style="margin-top: 15px; display: flex;flex-direction: column;justify-content: center;align-items: center; height: 100%;">

                
                <form action="{{ route('relatorio-xlsx-download') }}" method="post" class="form-center" id="form-csv">
                <input type="hidden" name="data" value="json_encode($resultArray)">
                    <input type="hidden" name="tipo_csv" value="relatorioErros">
                        <h1>Relatório de Erros</h1>

                        @csrf

                        
                        <table id="total_transacoes" class="table table-striped table-responsive" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Descrição</th>
                                    <th>Ação</th>
                                    <th>Placa Máquina</th>
                                    <th>Data e Hora</th>
                                </tr>
                            </thead>
                            <tbody>

                            @foreach($resultadosFiltrados as $resultado)
                                <tr>
                                    <td>{{$resultado['status']}}</td>
                                    <td>{{$resultado['descricao']}}</td>
                                    <td>{{$resultado['acao']}}</td>
                                    
                                    <td>{{$resultado['id_placa']}}</td>
                                    
                                    <td>{{date('d/m/Y H:i:s', strtotime($resultado['data_criacao']))}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Status</th>
                                    <th>Descrição</th>
                                    <th>Ação</th>
                                    <th>Placa Máquina</th>
                                    <th>Data e Hora</th>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="div-button" style="padding-top: 70px; padding-bottom: 30px;">
                            <button class="btn btn-primary" id="btn-baixar-csv" type="submit" style="width: 130px;">Gerar Arquivo</button>
                        </div>
                    </form>
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



@endsection

@section('scriptTable')
<script>
            $(document).ready(function(){
             // Adiciona a definição de tipo de dados para DataTables
                $.fn.dataTable.ext.type.order['datetime-ddmmyyyy-pre'] = function(d) {
                    if (d === 'Data não disponível') {
                        return 0;
                    }
                    var parts = d.split('/');
                    return new Date(parts[2], parts[1] - 1, parts[0]).getTime();
                };

            $('#total_transacoes').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
                },
                "columns": [
                    null,
                    null,
                    null,
                    null,
                    { "type": "datetime-ddmmyyyy" }
                ],
            });
        });

        var tabelaVazia = document.querySelectorAll('td').length;


        // Agora, você pode usar a variável 'tabelaVazia' conforme necessário
        if (tabelaVazia > 0) {
            document.getElementById("btn-baixar-csv").disabled = false
        } else {
            console.log('A tabela está vazia.');
            document.getElementById("btn-baixar-csv").disabled = true

        }
        </script>
@endsection
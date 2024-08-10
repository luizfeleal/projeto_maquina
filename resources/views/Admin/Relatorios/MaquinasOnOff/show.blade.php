@extends('layouts.app')

@section('title', 'Relatórios > Máquinas Online/Offline')

@section('content')
        <div id="reports_maquinas_online_offline" class="relatorios div-center-column w-100"
                style="padding-top: 99px;">

                <div class="container section container-platform"
                style="margin-top: 15px; display: flex;flex-direction: column;justify-content: center;align-items: center; height: 100%;">

                <form action="{{ route('relatorio-xlsx-download') }}" method="post" class="form-center" id="form-csv">
                    <h1>Máquinas Online/Offline</h1>
                    
                    @csrf
                    <input type="hidden" name="data" value="{{json_encode($resultArray)}}">
                    <input type="hidden" name="tipo_csv" value="maquinaOnlineOffline">

                        <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-top: 50px; margin-bottom: 30px;">
                            <div class="col-md-3">
                                <p><strong>Total de Máquinas Online: </strong> {{count($maquinasOnline)}}</p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Total de Máquinas Offline: </strong> {{count($maquinasOffline)}}</p>
                            </div>
                        </div>

                        <table id="maquinas_online_offline" class="table table-striped table-responsive" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Máquina</th>
                                    <th>Local</th>
                                    <th>Status</th>
                                    <th>Último Contato</th>
                                </tr>
                            </thead>
                            <tbody>

                            @foreach($maquinasOnline as $online)
                                <tr>
                                    <td>{{$online['maquina_nome']}}</td>
                                    <td>{{$locais[$online['id_local']]['local_nome']}}</td>
                                    @if($online['maquina_status'] == 0) 
                                        <td><i class="fa-solid fa-circle text-danger" ></i></td>
                                    @else
                                        <td><i class="fa-solid fa-circle text-success" ></i></td>
                                    @endif
                                    
                                    <td>{{date('d/m/Y H:i:s', strtotime($online['maquina_ultimo_contato']))}}</td>
                                </tr>
                            @endforeach
                            @foreach($maquinasOffline as $offline)
                                <tr>
                                    <td>{{$offline['maquina_nome']}}</td>
                                    <td>{{$locais[$offline['id_local']]['local_nome']}}</td>
                                    @if($offline['maquina_status'] == 0) 
                                        <td><i class="fa-solid fa-circle text-danger" ></i></td>
                                    @else
                                        <td><i class="fa-solid fa-circle text-success" ></i></td>
                                    @endif
                                    <td>{{date('d/m/Y H:i:s', strtotime($offline['maquina_ultimo_contato']))}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Máquina</th>
                                    <th>Local</th>
                                    <th>Status</th>
                                    <th>Último Contato</th>
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

            $('#maquinas_online_offline').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
                },
                "columns": [
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
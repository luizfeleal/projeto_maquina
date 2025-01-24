@extends('layouts.app')
@section('title', 'Minhas Máquinas -> Transações')
@section('content')

        <div id="guias" class="maquina w-100 div-center-column"
                style=" padding-top: 99px; padding-bottom: 100px;">

                <h1 style="padding-top: 80px; text-align: center; padding-bottom: 50px;">Minhas Máquinas -> Transações</h1>

            <div class="container section container-platform div-center-column"
                style="margin-top: 15px; height: 100%;">
                
                <div class="tabela_responsiva">
                    <table id="tabela_maquinas_transacao" class="display responsive table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Local</th>
                                <th>Máquina</th>
                                <th>Última transação</th>
                                <th>Fonte</th>
                                <th>Data e Hora</th>
    
    
                            </tr>
                        </thead>
                        <tbody>
    
                            
    
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Local</th>
                                <th>Máquina</th>
                                <th>Última transação</th>
                                <th>Fonte</th>
                                <th>Data e Hora</th>
                            </tr>
                        </tfoot>
                    </table>
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

$(document).ready(function () {
    async function fetchToken() {
        try {
            let response = await fetch('https://www.swiftpaysolucoes.com/api/getToken');
            let data = await response.json();
            return data.token;
        } catch (error) {
            console.error('Erro ao obter o token:', error);
            return null;
        }
    }

    fetchToken().then(token => {
        if (token) {
            $('#tabela_maquinas_transacao').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                scrollX: true,
                ajax: {
                    url: 'https://services.swiftpaysolucoes.com/api/extratoMaquina',
                    type: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    data: function (d) {
                        // DataTables envia os parâmetros conforme a estrutura do backend
                        d.start = d.start || 0; // Índice inicial
                        d.length = d.length || 10; // Número de registros por página
                        d.search = d.search.value; // Filtro de pesquisa
                    }
                },
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
                },
                columns: [
                    {
                        data: 'local_nome',
                        title: 'Local'
                    },
                    {
                        data: 'maquina_nome',
                        title: 'Máquina'
                    },
                    {
                        data: 'extrato_operacao',
                        title: 'Última Transação',
                        render: function (data, type, row) {
                            var extrato_valor = row.extrato_operacao_valor ? row.extrato_operacao_valor : 0;
                            var valor = parseFloat(extrato_valor).toFixed(2).replace('.', ',');
                            return data === 'C' ? '+ R$ ' + valor : '- R$ ' + valor;
                        }
                    },
                    {
                        data: 'extrato_operacao_tipo',
                        title: 'Fonte'
                    },
                    {
                        data: 'data_criacao',
                        title: 'Data e Hora',
                        render: function (data) {
                            // A data já vem formatada do backend como dd/mm/aaaa hh:mm
                            return data;
                        }
                    }
                ],
                order: [[4, 'desc']], // Ordenar pela coluna "Data e Hora" (índice 4) em ordem decrescente
                pageLength: 10,
                paging: true,
                lengthMenu: [10, 25, 50, 100],
                ordering: true // Ativar ordenação
            });
        }
    });
});
    </script>

@endsection


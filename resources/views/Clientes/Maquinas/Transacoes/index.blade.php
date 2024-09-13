@extends('layouts.Clientes.app')
@section('title', 'Minhas Máquinas -> Transações')
@section('content')

        <div id="guias" class="maquina w-100 div-center-column"
                style=" padding-top: 99px; padding-bottom: 100px;">

                <h1 style="padding-top: 80px; text-align: center; padding-bottom: 50px;">Minhas Máquinas -> Transações</h1>

            <div class="container section container-platform div-center-column"
                style="margin-top: 15px; height: 100%;">
                
                <table id="tabela_maquinas_transacao" class="table table-striped" style="width:100%">
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
                        ajax: {
                            url: 'http://127.0.0.1:5001/api/totalTransacaoMaquinaCliente', // URL da sua API
                            type: 'POST', // Tipo de requisição
                            dataSrc: 'data', // Propriedade da resposta que contém os dados
                            headers: {
                                'Authorization': 'Bearer ' + token, // Adicione seu token de autenticação
                            },
                            data: function (d) {
                                d.id_cliente = {!!json_decode($id_cliente)!!}
                                d.page = (d.start / d.length) + 1; // DataTables usa índice baseado em 0
                                d.per_page = d.length; // Define o número de registros por página
                            }
                        },
                        language: {
                            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json" // Idioma
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
                                render: function(data, type, row) {
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
                                render: function(data) {
                                    var date = new Date(data);
                                    return date.toLocaleDateString('pt-BR') + ' ' + date.toLocaleTimeString('pt-BR');
                                }
                            }
                        ],
                        pageLength: 10,
                        paging: true,
                        lengthMenu: [10, 25, 50, 100]
                    });
                }
            });
        });
    </script>

@endsection


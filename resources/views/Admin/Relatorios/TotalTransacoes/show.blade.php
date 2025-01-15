@extends('layouts.app')

@section('title', 'Relatórios > Total Transações')

@section('content')
    <div id="reports_maquinas_online_offline" class="relatorios div-center-column w-100"
         style="padding-top: 99px;">

        <div class="container section container-platform"
             style="margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%;">

            <form action="{{ route('relatorio-xlsx-download') }}" method="post" class="form-center" id="form-csv">
                @csrf
                <input type="hidden" name="tipo_csv" value="total_transacao">
                <input type="hidden" name="data" value="{{json_encode($bodyReq)}}">
                <h1>Total Transações</h1>

                <div class="tabela_responsiva">

                    <table id="total_transacoes" class="table table-striped table-responsive" style="width:100%">
                        <thead>
                            <tr>
                                <!--<th>Local</th>-->
                                <th>Maquina</th>
                                <th>Tipo Transação</th>
                                <th>Valor</th>
                                <th>Data e Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dados serão carregados via AJAX -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <!--<th>Local</th>-->
                                <th>Maquina</th>
                                <th>Tipo Transação</th>
                                <th>Valor</th>
                                <th>Data e Hora</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="row" style="display: flex; flex-direction: row; justify-content: center; width: 100%; margin-top: 50px;">
                    <div class="col-md-2">
                        <p><strong>Pix: </strong> R$ 

                        @foreach($total as $item)
                            @if($item['tipo'] == "Pix")
                            <span id="valor_total_pix">{{number_format($item['total'], 2, ',', '.')}}</span>
                            @endif
                        @endforeach
                         </p>
                    </div>
                    <div class="col-md-2">
                        <p><strong>Cartão: </strong> R$ 
                        @foreach($total as $item)
                            @if($item['tipo'] == "Cartão")
                            <span id="valor_total_pix">{{number_format($item['total'], 2, ',', '.')}}</span>
                            @endif
                        @endforeach </p>
                    </div>
                    <div class="col-md-2">
                        <p><strong>Dinheiro: </strong> R$
                        @foreach($total as $item)
                            @if($item['tipo'] == "Dinheiro")
                            <span id="valor_total_pix">{{number_format($item['total'], 2, ',', '.')}}</span>
                            @endif
                        @endforeach </p>
                    </div>
                    <div class="col-md-2">
                        <p><strong>Estorno: </strong> R$ 
                        @foreach($total as $item)
                            @if($item['tipo'] == "Estorno")
                            <span id="valor_total_pix">{{number_format($item['total'], 2, ',', '.')}}</span>
                            @endif
                        @endforeach </p>
                    </div>
                </div>
                <div class="row" style="display: flex; flex-direction: row; justify-content: center; width: 100%; margin-top: 10px; margin-bottom: 30px;">
                    <div class="col-md-8">
                        <h4 style="color: #242a74;"><strong>Total Transações: </strong> R$
                            <span id="valor_total_pix">{{number_format($totalTransacoes, 2, ',', '.')}}</span></h4>
                    </div>
                </div>

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
$(document).ready(function () {
    // Definir o tipo de ordenação personalizada para datas no formato dd/mm/yyyy
    $.fn.dataTable.ext.type.order['datetime-ddmmyyyy-pre'] = function (d) {
        if (d === 'Data não disponível' || !d) {
            return 0;
        }
        var parts = d.split('/');
        return new Date(parts[2], parts[1] - 1, parts[0]).getTime();
    };

    var table = $('#total_transacoes').DataTable({
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
        },
        processing: true,
        serverSide: true,
        scrollX: true,
        ordering: true,
        ajax: {
            url: "{{ route('relatorio-criar') }}",
            type: "POST",
            data: function (d) {
                // Mesclar os parâmetros enviados pelo DataTables com os adicionais
                return $.extend({}, d, {
                    _token: '{{ csrf_token() }}',
                    tipo: 'totalTransacoes',
                    id_maquina: @json($id_maquina),
                    id_cliente: @json($id_cliente),
                    tipo_transacao: @json($tipo_transacao),
                    data_inicio: @json($data_extrato_inicio),
                    data_fim: @json($data_extrato_fim)
                });
            }
        },
        columns: [
            // Coluna Nome da Máquina
            { data: "maquina_nome", title: "Máquina" },
            // Coluna Tipo de Operação
            { data: "extrato_operacao_tipo", title: "Fonte" },
            // Coluna Valor da Transação
            {
                data: "extrato_operacao_valor",
                title: "Valor",
                orderable: true,
                render: function (data, type, row) {
                    if (data === null || isNaN(parseFloat(data))) {
                        return ''; // Retorna vazio se não houver valor
                    }
                    var valor = parseFloat(data).toFixed(2).replace('.', ',');
                    return row.extrato_operacao === "C"
                        ? '+ R$ ' + valor
                        : '- R$ ' + valor;
                }
            },
            // Coluna Data da Criação
            {
                data: "data_criacao",
                title: "Data e Hora",
                type: "datetime-ddmmyyyy",
                render: function (data) {
                    if (!data) return 'Data não disponível';
                    return data; // Assumimos que o backend já retorna no formato dd/mm/yyyy hh:mm
                }
            }
        ],
        order: [[3, 'desc']], // Ordenar pela coluna "Data e Hora" (índice 3) decrescente
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        drawCallback: function (settings) {
            var api = this.api();

            // Atualizar valores no DOM
            $('#btn-baixar-csv').prop('disabled', api.data().length === 0);
        }
    });
});


</script>
@endsection

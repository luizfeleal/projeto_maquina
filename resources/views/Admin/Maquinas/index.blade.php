@extends('layouts.app')
@section('title', 'Minhas Máquinas')
@section('content')

<div id="guias" class="maquina w-100 div-center-column"
     style="padding-top: 99px; padding-bottom: 100px;">

    <div class="container section container-platform div-center-column"
         style="margin-top: 15px; height: 100%;">

        <div class="tabela_responsiva">
            <table id="tabela_maquinas" class="display nowrap table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Máquina</th>
                        <th>Local</th>
                        <th>ID Placa</th>
                        <th>Status</th>
                        <th>Último extrato</th>
                        <th>Forma de pagamento</th>
                        <th>Data e Hora</th>
                        <th>Detalhar</th>
                        <th>Excluir</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <div class="modal fade" id="ModalCenterExcluir" tabindex="-1" aria-labelledby="ModalCenterExcluir" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="ModalCenterTitleExcluir">Excluir Maquina</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Deseja excluir essa máquina?</p>
                    </div>
                    <div class="modal-footer">
                        <form action="{{ route('maquinas-excluir') }}" method="post" id="excluir-maquina" class="w-100">
                            @csrf
                            <input type="hidden" name="id_maquina" id="id_maquina_input_excluir" value="">
                            <button type="submit" class="btn btn-primary" data-bs-dismiss="modal" aria-label="Close">Sim</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@section('scriptTable')
<style>
    .maq-status-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .maq-status-dot--online {
        background: #16a34a;
        box-shadow: 0 0 0 2px #bbf7d0;
    }
    .maq-status-dot--offline {
        background: #dc2626;
    }
</style>
<script>
$(document).ready(function () {

    function formatDataHora(val) {
        if (!val) return '—';
        var d = new Date(val);
        if (isNaN(d.getTime())) return val;
        return d.toLocaleString('pt-BR');
    }

    function formatValor(row) {
        var val = parseFloat(row.extrato_operacao_valor || 0).toFixed(2).replace('.', ',');
        var op  = row.extrato_operacao;
        if (op === 'C' || op === 'N/A') return '+ R$ ' + val;
        return '- R$ ' + val;
    }

    function statusDot(status) {
        var online = Number(status) !== 0;
        var label  = online ? 'Online' : 'Offline';
        return '<span class="maq-status-dot ' + (online ? 'maq-status-dot--online' : 'maq-status-dot--offline') + '" ' +
               'aria-label="' + label + '" title="' + label + '"></span>';
    }

    $('#tabela_maquinas').DataTable({
        processing: true,
        deferRender: true,
        ajax: {
            url: '{{ route('maquinas-dados') }}',
            dataSrc: function (json) {
                return Array.isArray(json) ? json : (json.data || []);
            }
        },
        language: {
            emptyTable: 'Nenhum registro encontrado',
            info: 'Mostrando _START_ até _END_ de _TOTAL_ registros',
            infoEmpty: 'Mostrando 0 até 0 de 0 registros',
            infoFiltered: '(filtrado de _MAX_ registros)',
            lengthMenu: 'Exibir _MENU_ registros',
            loadingRecords: 'Carregando...',
            processing: 'Processando...',
            search: 'Pesquisar:',
            zeroRecords: 'Nenhum registro encontrado',
            paginate: {
                first: 'Primeiro',
                last: 'Último',
                next: 'Próximo',
                previous: 'Anterior'
            }
        },
        scrollX: true,
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        order: [[6, 'desc']],
        columns: [
            { data: 'maquina_nome', defaultContent: '—' },
            { data: 'local_nome', defaultContent: '—' },
            { data: 'id_placa', defaultContent: '—' },
            {
                data: 'maquina_status',
                orderable: false,
                className: 'text-center',
                render: function (data) {
                    return statusDot(data);
                }
            },
            {
                data: null,
                orderable: false,
                render: function (_data, _type, row) {
                    return formatValor(row);
                }
            },
            { data: 'extrato_operacao_tipo', defaultContent: '—' },
            {
                data: 'data_criacao',
                render: function (data, type) {
                    if (type === 'sort' || type === 'type') {
                        var t = Date.parse(data);
                        return isNaN(t) ? 0 : t;
                    }
                    return formatDataHora(data);
                }
            },
            {
                data: 'id_maquina',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function (data) {
                    return '<a href="/maquinas/visualizar?id=' + data + '" aria-label="Detalhar">' +
                           '<i class="fa-solid fa-eye"></i></a>';
                }
            },
            {
                data: 'id_maquina',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function (data) {
                    return '<a href="#" style="color:red!important" data-bs-toggle="modal" ' +
                           'data-bs-target="#ModalCenterExcluir" aria-label="Excluir" ' +
                           'onclick="setIdMaquinaExcluir(' + data + ', \'#id_maquina_input_excluir\')">' +
                           '<i class="fa-solid fa-trash"></i></a>';
                }
            }
        ]
    });
});
</script>
@endsection

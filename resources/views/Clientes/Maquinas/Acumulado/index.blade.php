@extends('layouts.Clientes.app')
@section('title', 'Minhas Máquinas -> Acumulado')
@section('content')

<div id="guias" class="maquina w-100 div-center-column"
        style="padding-top: 99px; padding-bottom: 100px;">

    <div class="container section container-platform div-center-column"
        style="margin-top: 15px; height: 100%;">

        <table id="tabela-acumulado" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>Local</th>
                    <th>Máquina</th>
                    <th>Total máquina</th>
                    <th>Total PIX</th>
                    <th>Total cartão</th>
                    <th>Total dinheiro</th>
                    <th>Última coleta</th>
                    <th>Saldo do período</th>
                    <th>Último reset</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
                <tr>
                    <th>Local</th>
                    <th>Máquina</th>
                    <th>Total máquina</th>
                    <th>Total PIX</th>
                    <th>Total cartão</th>
                    <th>Total dinheiro</th>
                    <th>Última coleta</th>
                    <th>Saldo do período</th>
                    <th>Último reset</th>
                    <th>Ações</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- Form oculto submetido pelo SweetAlert --}}
<form id="formResetParcial" method="POST" action="{{ route('clientes-maquinas-reset-parcial') }}" style="display:none">
    @csrf
    <input type="hidden" name="id_maquina" id="resetIdMaquina">
</form>

@endsection

@section('scriptTable')
<script>
$(document).ready(function () {
    var brl = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });
    var idCliente = {!! json_encode($id_cliente) !!};

    function formatBrl(val) {
        if (val === null || val === undefined) return 'R$ 0,00';
        return brl.format(val);
    }

    function formatDate(iso) {
        if (!iso) return '—';
        return new Date(iso).toLocaleString('pt-BR');
    }

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: '{{ session("success") }}',
            timer: 3000,
            showConfirmButton: false,
        }).then(function () {
            if (window.tabelaAcumulado) {
                window.tabelaAcumulado.ajax.reload(null, false);
            }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Erro',
            text: '{{ session("error") }}',
        });
    @endif

    async function fetchToken() {
        try {
            let r = await fetch('https://www.swiftpaysolucoes.com/api/getToken');
            let d = await r.json();
            return d.token;
        } catch (e) { return null; }
    }

    fetchToken().then(function (token) {
        if (!token) return;

        window.tabelaAcumulado = $('#tabela-acumulado').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ env("APP_URL_API") }}/totalTransacaoMaquinaAcumuladoCliente',
                type: 'POST',
                headers: { 'Authorization': 'Bearer ' + token },
                data: function (d) {
                    d.id_cliente = idCliente;
                    d.page       = (d.start / d.length) + 1;
                    d.per_page   = d.length;
                },
                dataSrc: 'data'
            },
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
            },
            columns: [
                { data: 'local_nome',   title: 'Local' },
                { data: 'maquina_nome', title: 'Máquina' },
                {
                    data: 'total_maquina', title: 'Total máquina',
                    render: function (data) { return formatBrl(data); }
                },
                {
                    data: 'total_pix', title: 'Total PIX',
                    render: function (data) { return formatBrl(data); }
                },
                {
                    data: 'total_cartao', title: 'Total cartão',
                    render: function (data) { return formatBrl(data); }
                },
                {
                    data: 'total_dinheiro', title: 'Total dinheiro',
                    render: function (data) { return formatBrl(data); }
                },
                {
                    data: 'ultima_coleta', title: 'Última coleta',
                    render: function (data, type, row) {
                        if (!row.tem_reset || data === null) return '<span class="text-muted">Sem reset</span>';
                        return formatBrl(data);
                    }
                },
                {
                    data: 'saldo_periodo', title: 'Saldo do período',
                    render: function (data) {
                        var formatted = formatBrl(data);
                        if (data < 0) return '<span class="text-danger fw-bold">' + formatted + '</span>';
                        return '<span class="fw-bold">' + formatted + '</span>';
                    }
                },
                {
                    data: 'data_ultimo_reset', title: 'Último reset',
                    render: function (data) { return formatDate(data); }
                },
                {
                    data: null, title: 'Ações', orderable: false, searchable: false,
                    render: function (data, type, row) {
                        return '<button class="btn btn-sm btn-warning btn-reset-parcial" ' +
                               'data-id="' + row.id_maquina + '" ' +
                               'data-nome="' + (row.maquina_nome || '').replace(/"/g, '&quot;') + '" ' +
                               'data-total="' + (row.total_maquina || 0) + '">' +
                               '<i class="fa-solid fa-rotate-right me-1"></i>Reset</button>';
                    }
                }
            ],
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100]
        });

        $('#tabela-acumulado tbody').on('click', '.btn-reset-parcial', function () {
            var id    = $(this).data('id');
            var nome  = $(this).data('nome');
            var total = $(this).data('total');

            Swal.fire({
                icon: 'warning',
                title: 'Confirmar Reset Parcial?',
                html:
                    '<p class="mb-1">Máquina: <strong>' + nome + '</strong></p>' +
                    '<p class="mb-2">Total acumulado atual: <strong>' + brl.format(total) + '</strong></p>' +
                    '<p class="text-muted small">O contador principal <strong>não será alterado</strong>.<br>Apenas o saldo do período será reiniciado.</p>',
                showCancelButton: true,
                confirmButtonText: '<i class="fa-solid fa-rotate-right me-1"></i>Confirmar Reset',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                reverseButtons: true,
            }).then(function (result) {
                if (!result.isConfirmed) return;
                $('#resetIdMaquina').val(id);
                $('#formResetParcial').submit();
            });
        });
    });
});
</script>
@endsection

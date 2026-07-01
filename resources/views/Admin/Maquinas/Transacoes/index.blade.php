@extends('layouts.app')
@section('title', 'Máquinas → Extrato')
@section('content')

<div id="guias" class="maquina w-100 div-center-column"
        style="padding-top: 99px; padding-bottom: 100px;">

    <div class="container section container-platform div-center-column"
        style="margin-top: 15px; height: 100%;">

        {{-- ── Filtros ────────────────────────────────────────────────────── --}}
        <div class="card mb-3 w-100">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="filter-cliente" class="form-label fw-semibold">Cliente</label>
                        <select id="filter-cliente" class="form-select select2-filter">
                            <option value="">Todos os clientes</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente['id_cliente'] }}">{{ $cliente['cliente_nome'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="filter-local" class="form-label fw-semibold">Local</label>
                        <select id="filter-local" class="form-select select2-filter">
                            <option value="">Todos os locais</option>
                            @foreach($locais as $local)
                                <option value="{{ $local['id_local'] }}">{{ $local['local_nome'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="filter-maquina" class="form-label fw-semibold">Máquina</label>
                        <select id="filter-maquina" class="form-select select2-filter">
                            <option value="">Todas as máquinas</option>
                            @foreach($maquinas as $maquina)
                                <option value="{{ $maquina['id_maquina'] }}">{{ $maquina['maquina_nome'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="filter-data-inicio" class="form-label fw-semibold">Data início</label>
                        <input type="date" id="filter-data-inicio" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label for="filter-data-fim" class="form-label fw-semibold">Data fim</label>
                        <input type="date" id="filter-data-fim" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label for="filter-tipo-operacao" class="form-label fw-semibold">Forma de pagamento</label>
                        <select id="filter-tipo-operacao" class="form-select">
                            <option value="">Todos os tipos</option>
                            <option value="pix">PIX</option>
                            <option value="cartao">Cartão</option>
                            <option value="dinheiro">Dinheiro</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="d-flex gap-2">
                        <button id="btn-aplicar-filtro" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-filter me-1"></i>Aplicar filtro
                        </button>
                        <button id="btn-limpar-filtro" class="btn btn-outline-secondary btn-sm">
                            <i class="fa-solid fa-xmark me-1"></i>Limpar
                        </button>
                    </div>
                    <button id="btn-toggle-taxas" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-eye me-1"></i>Exibir transações de taxa
                    </button>
                </div>
            </div>
        </div>

        {{-- ── Tabela ─────────────────────────────────────────────────────── --}}
        <div class="tabela_responsiva w-100">
            <table id="tabela_maquinas_transacao" class="display table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Local</th>
                        <th>Máquina</th>
                        <th>Último extrato</th>
                        <th>Forma de pagamento</th>
                        <th>Data e Hora</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <th>Local</th>
                        <th>Máquina</th>
                        <th>Último extrato</th>
                        <th>Forma de pagamento</th>
                        <th>Data e Hora</th>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
</div>

@endsection

@section('scriptTable')
<script>
$(document).ready(function () {

    var locaisData      = @json($locais);
    var maquinasData    = @json($maquinas);
    var clienteLocalData = @json($clienteLocal);

    $('.select2-filter').select2({ theme: 'bootstrap-5', width: '100%' });

    function locaisDoCliente(idCliente) {
        if (!idCliente) return locaisData;
        var ids = clienteLocalData
            .filter(function (cl) { return String(cl.id_cliente) === String(idCliente); })
            .map(function (cl) { return String(cl.id_local); });
        return locaisData.filter(function (l) { return ids.indexOf(String(l.id_local)) !== -1; });
    }

    function maquinasDoFiltro(idCliente, idLocal) {
        var lista = maquinasData;
        if (idCliente) {
            var locaisIds = locaisDoCliente(idCliente).map(function (l) { return String(l.id_local); });
            lista = lista.filter(function (m) { return locaisIds.indexOf(String(m.id_local)) !== -1; });
        }
        if (idLocal) {
            lista = lista.filter(function (m) { return String(m.id_local) === String(idLocal); });
        }
        return lista;
    }

    function repopularSelect($select, placeholder, items, valueKey, labelKey, selected) {
        var html = '<option value="">' + placeholder + '</option>';
        items.forEach(function (item) {
            var val = item[valueKey];
            var sel = selected && String(selected) === String(val) ? ' selected' : '';
            html += '<option value="' + val + '"' + sel + '>' + item[labelKey] + '</option>';
        });
        $select.html(html).trigger('change.select2');
    }

    function atualizarOpcoesLocal() {
        var idCliente = $('#filter-cliente').val();
        var idLocal   = $('#filter-local').val();
        repopularSelect(
            $('#filter-local'),
            'Todos os locais',
            locaisDoCliente(idCliente),
            'id_local',
            'local_nome',
            idLocal
        );
        if (idLocal && !$('#filter-local').val()) {
            $('#filter-local').val('').trigger('change.select2');
        }
    }

    function atualizarOpcoesMaquina() {
        var idCliente = $('#filter-cliente').val();
        var idLocal   = $('#filter-local').val();
        var idMaquina = $('#filter-maquina').val();
        repopularSelect(
            $('#filter-maquina'),
            'Todas as máquinas',
            maquinasDoFiltro(idCliente, idLocal),
            'id_maquina',
            'maquina_nome',
            idMaquina
        );
        if (idMaquina && !$('#filter-maquina').val()) {
            $('#filter-maquina').val('').trigger('change.select2');
        }
    }

    $('#filter-cliente').on('change', function () {
        atualizarOpcoesLocal();
        atualizarOpcoesMaquina();
    });

    $('#filter-local').on('change', function () {
        atualizarOpcoesMaquina();
    });

    var mostrarTaxas = false;

    var tabela = $('#tabela_maquinas_transacao').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: {
            url: '{{ route('maquinas-transacoes-dados') }}',
            type: 'GET',
            data: function (d) {
                d.search = d.search.value;
                d.mostrar_taxas = mostrarTaxas ? 1 : 0;

                var idCliente = $('#filter-cliente').val();
                var idLocal   = $('#filter-local').val();
                var idMaquina = $('#filter-maquina').val();
                if (idCliente) d.id_cliente = idCliente;
                if (idLocal)   d.id_local   = idLocal;
                if (idMaquina) d.id_maquina = idMaquina;

                var dataInicio = $('#filter-data-inicio').val();
                var dataFim    = $('#filter-data-fim').val();
                if (dataInicio) d.data_inicio = dataInicio;
                if (dataFim)    d.data_fim    = dataFim;

                var tipoOperacao = $('#filter-tipo-operacao').val();
                if (tipoOperacao) d.tipo_operacao = tipoOperacao;
            }
        },
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
        },
        columns: [
            { data: 'local_nome', title: 'Local' },
            { data: 'maquina_nome', title: 'Máquina' },
            {
                data: 'extrato_operacao',
                title: 'Último extrato',
                render: function (data, type, row) {
                    var val = parseFloat(row.extrato_operacao_valor || 0).toFixed(2).replace('.', ',');
                    return data === 'C' ? '+ R$ ' + val : '- R$ ' + val;
                }
            },
            { data: 'extrato_operacao_tipo', title: 'Forma de pagamento' },
            {
                data: 'data_criacao',
                title: 'Data e Hora',
                render: function (data) { return data || '—'; }
            }
        ],
        order: [[4, 'desc']],
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        ordering: true
    });

    function ajustarColunasTabela() {
        setTimeout(function () {
            tabela.columns.adjust();
            if (tabela.fixedColumns) {
                tabela.fixedColumns().relayout();
            }
        }, 50);
    }

    $(document).on('click', '.view-btn-table', ajustarColunasTabela);

    tabela.on('draw.dt', function () {
        var info  = tabela.page.info();
        var count = (info && info.recordsDisplay !== undefined)
            ? info.recordsDisplay
            : tabela.rows({ search: 'applied' }).count();
        var label = count + ' registro' + (count !== 1 ? 's' : '');
        $('.view-toggle-bar .view-count-badge').text(label).attr('aria-label', label);
    });

    function recarregarTabela() {
        tabela.page('first').ajax.reload(null, false);
    }

    $('#btn-aplicar-filtro').on('click', recarregarTabela);

    $('#btn-limpar-filtro').on('click', function () {
        $('#filter-cliente').val('').trigger('change.select2');
        $('#filter-local').val('').trigger('change.select2');
        $('#filter-maquina').val('').trigger('change.select2');
        $('#filter-data-inicio').val('');
        $('#filter-data-fim').val('');
        $('#filter-tipo-operacao').val('');
        atualizarOpcoesLocal();
        atualizarOpcoesMaquina();
        recarregarTabela();
    });

    $('#btn-toggle-taxas').on('click', function () {
        mostrarTaxas = !mostrarTaxas;
        recarregarTabela();

        $(this).html(mostrarTaxas
            ? '<i class="fa-solid fa-eye-slash me-1"></i>Ocultar transações de taxa'
            : '<i class="fa-solid fa-eye me-1"></i>Exibir transações de taxa');
    });

});
</script>
@endsection

@extends('layouts.app')
@section('title', 'Máquinas → Transações')
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
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <button id="btn-aplicar-filtro" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-filter me-1"></i>Aplicar filtro
                    </button>
                    {{-- ── Botão de taxas ───────────────────────────────────────── --}}
                    <button id="btn-toggle-taxas" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-eye me-1"></i>Mostrar Taxas
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
                        <th>Última Transação</th>
                        <th>Fonte</th>
                        <th>Data e Hora</th>
                        <th>Taxa</th>
                        <th>Taxa %</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <th>Local</th>
                        <th>Máquina</th>
                        <th>Última Transação</th>
                        <th>Fonte</th>
                        <th>Data e Hora</th>
                        <th>Taxa</th>
                        <th>Taxa %</th>
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

    // ── Select2 nos filtros ──────────────────────────────────────────────
    $('.select2-filter').select2({ theme: 'bootstrap-5', width: '100%' });

    // ── Token ────────────────────────────────────────────────────────────
    async function fetchToken() {
        try {
            let r = await fetch('https://www.swiftpaysolucoes.com/api/getToken');
            let d = await r.json();
            return d.token;
        } catch (e) {
            console.error('Erro ao obter token:', e);
            return null;
        }
    }

    fetchToken().then(function (token) {
        if (!token) return;

        var taxasVisiveis = false;

        var tabela = $('#tabela_maquinas_transacao').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: {
                url: 'https://services.swiftpaysolucoes.com/api/extratoMaquina',
                type: 'GET',
                headers: { 'Authorization': 'Bearer ' + token },
                data: function (d) {
                    d.start  = d.start  || 0;
                    d.length = d.length || 10;
                    d.search = d.search.value;

                    var idCliente = $('#filter-cliente').val();
                    var idLocal   = $('#filter-local').val();
                    var idMaquina = $('#filter-maquina').val();
                    if (idCliente) d.id_cliente = idCliente;
                    if (idLocal)   d.id_local   = idLocal;
                    if (idMaquina) d.id_maquina = idMaquina;
                }
            },
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
            },
            columns: [
                { data: 'local_nome',  title: 'Local' },
                { data: 'maquina_nome', title: 'Máquina' },
                {
                    data: 'extrato_operacao',
                    title: 'Última Transação',
                    render: function (data, type, row) {
                        var val = parseFloat(row.extrato_operacao_valor || 0).toFixed(2).replace('.', ',');
                        return data === 'C' ? '+ R$ ' + val : '- R$ ' + val;
                    }
                },
                { data: 'extrato_operacao_tipo', title: 'Fonte' },
                {
                    data: 'data_criacao',
                    title: 'Data e Hora',
                    render: function (data) { return data || '—'; }
                },
                // Colunas de taxa — ocultas por padrão
                {
                    data: 'extrato_taxa',
                    title: 'Taxa',
                    visible: false,
                    render: function (data) {
                        if (data === null || data === undefined || data === '') return '—';
                        return 'R$ ' + parseFloat(data).toFixed(2).replace('.', ',');
                    }
                },
                {
                    data: 'extrato_taxa_percentual',
                    title: 'Taxa %',
                    visible: false,
                    render: function (data) {
                        if (data === null || data === undefined || data === '') return '—';
                        return parseFloat(data).toFixed(2).replace('.', ',') + '%';
                    }
                }
            ],
            order: [[4, 'desc']],
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            ordering: true
        });

        // ── Corrigir alinhamento dos cabeçalhos ao exibir a tabela ──────
        $(document).on('click', '.view-btn-table', function () {
            setTimeout(function () { tabela.columns.adjust(); }, 50);
        });

        // ── Corrigir o contador de registros no badge do toggle bar ──────
        tabela.on('draw.dt', function () {
            var info  = tabela.page.info();
            var count = (info && info.recordsDisplay !== undefined)
                ? info.recordsDisplay
                : tabela.rows({ search: 'applied' }).count();
            var label = count + ' registro' + (count !== 1 ? 's' : '');
            $('.view-toggle-bar .view-count-badge').text(label).attr('aria-label', label);
        });

        // ── Botão aplicar filtro ─────────────────────────────────────────
        $('#btn-aplicar-filtro').on('click', function () {
            tabela.ajax.reload();
        });

        // ── Botão toggle taxas ───────────────────────────────────────────
        $('#btn-toggle-taxas').on('click', function () {
            taxasVisiveis = !taxasVisiveis;
            tabela.columns([5, 6]).visible(taxasVisiveis);
            tabela.columns.adjust();

            $(this).html(taxasVisiveis
                ? '<i class="fa-solid fa-eye-slash me-1"></i>Ocultar Taxas'
                : '<i class="fa-solid fa-eye me-1"></i>Mostrar Taxas');
        });
    });

});
</script>
@endsection

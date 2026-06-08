@extends('layouts.Clientes.app')
@section('title', 'Minhas Máquinas -> Transações')
@section('content')

    <div class="page-heading">
        <h1>Transações</h1>
        <p>Histórico completo de movimentações das suas máquinas</p>
    </div>

    <div class="content-body" style="padding-top:0;">
        <div class="page-container">
            <div class="page-header">
                <div class="d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:transfer-horizontal-bold-duotone"
                                  style="font-size:1.2rem; color:#2C9BA5;"></iconify-icon>
                    <span class="page-header-title">Transações</span>
                </div>
            </div>

            <div style="padding:20px;">
                <table id="tabela_maquinas_transacao" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Local</th>
                            <th>Máquina</th>
                            <th>Valor</th>
                            <th>Fonte</th>
                            <th>Data e Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($resultado as $tx)
                            @php
                                $isCredito = ($tx['extrato_operacao'] ?? 'C') === 'C';
                                $valor     = $tx['extrato_operacao_valor'] ?? 0;
                            @endphp
                            <tr>
                                <td>{{ $tx['local_nome'] ?? '—' }}</td>
                                <td>{{ $tx['maquina_nome'] ?? '—' }}</td>
                                <td>
                                    <span style="font-weight:700; color:{{ $isCredito ? '#16a34a' : '#ef4444' }}; white-space:nowrap;">
                                        {{ $isCredito ? '+' : '−' }} R$ {{ number_format($valor, 2, ',', '.') }}
                                    </span>
                                </td>
                                <td>{{ $tx['extrato_operacao_tipo'] ?? '—' }}</td>
                                <td>{{ date('d/m/Y H:i:s', strtotime($tx['data_criacao'])) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Local</th>
                            <th>Máquina</th>
                            <th>Valor</th>
                            <th>Fonte</th>
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
        $('#tabela_maquinas_transacao').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
            },
            order: [[4, 'desc']],
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100]
        });
    });
</script>
@endsection

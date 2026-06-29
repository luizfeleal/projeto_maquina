@extends('layouts.Financeiro.app')

@section('title', 'Despesas')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 gap-2 flex-wrap">
    <div class="d-flex align-items-center gap-2">
        <iconify-icon icon="solar:bill-list-bold-duotone" style="font-size:1.5rem; color:#1a6b4a;"></iconify-icon>
        <h4 class="mb-0 fw-semibold">Despesas</h4>
    </div>
    <a href="{{ route('financeiro-despesas-criar') }}" class="btn btn-success btn-sm">
        <iconify-icon icon="solar:add-circle-bold-duotone" inline></iconify-icon>
        Nova despesa
    </a>
</div>

@if($despesas->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5 text-muted">
            <iconify-icon icon="solar:inbox-line-duotone" style="font-size:3rem;"></iconify-icon>
            <p class="mt-2 mb-0">Nenhuma despesa registrada.</p>
        </div>
    </div>
@else
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table id="tbl-despesas" class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Descrição</th>
                        <th>Tipo</th>
                        <th>Data</th>
                        <th>Valor</th>
                        <th>Comprovante</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($despesas as $d)
                    <tr>
                        <td class="fw-semibold">{{ $d['descricao'] }}</td>
                        <td>{{ $d['tipo'] ?? '—' }}</td>
                        <td>{{ $d['data_despesa']?->format('d/m/Y') ?? '—' }}</td>
                        <td class="fw-bold text-danger">R$ {{ number_format($d['valor'], 2, ',', '.') }}</td>
                        <td>
                            @if(!empty($d['comprovante_url']))
                                <a href="{{ $d['comprovante_url'] }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                    <iconify-icon icon="solar:document-bold-duotone" inline></iconify-icon>
                                    Ver
                                </a>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('financeiro-despesas-excluir') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="id" value="{{ $d['id'] }}">
                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('Confirmar exclusão?')">
                                    <iconify-icon icon="solar:trash-bin-trash-bold-duotone" inline></iconify-icon>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection

@section('scriptTable')
<script>
$(document).ready(function () {
    $('#tbl-despesas').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' },
        order: [[2, 'desc']],
        pageLength: 25,
    });
});
</script>
@endsection

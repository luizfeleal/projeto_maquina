@extends('layouts.Financeiro.app')

@section('title', 'Estoque')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 gap-2 flex-wrap">
    <div class="d-flex align-items-center gap-2">
        <iconify-icon icon="solar:box-bold-duotone" style="font-size:1.5rem; color:#1a6b4a;"></iconify-icon>
        <h4 class="mb-0 fw-semibold">Estoque</h4>
    </div>
    <a href="{{ route('financeiro-estoque-criar') }}" class="btn btn-success btn-sm">
        <iconify-icon icon="solar:add-circle-bold-duotone" inline></iconify-icon>
        Registrar produto
    </a>
</div>

@if($produtos->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5 text-muted">
            <iconify-icon icon="solar:inbox-line-duotone" style="font-size:3rem;"></iconify-icon>
            <p class="mt-2 mb-0">Nenhum produto registrado no estoque.</p>
        </div>
    </div>
@else
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table id="tbl-estoque" class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Produto</th>
                        <th>Lote</th>
                        <th>Quantidade</th>
                        <th>Valor</th>
                        <th>Cobrança</th>
                        <th>Registrado em</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($produtos as $p)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $p['nome_produto'] }}</div>
                            @if($p['descricao'])
                                <div class="small text-muted">{{ $p['descricao'] }}</div>
                            @endif
                        </td>
                        <td>{{ $p['lote'] ?? '—' }}</td>
                        <td>{{ $p['quantidade'] }}</td>
                        <td>R$ {{ number_format($p['valor'], 2, ',', '.') }}</td>
                        <td>
                            @if($p['cobrar_mensal'])
                                <span class="badge bg-info-subtle text-info-emphasis">Mensal</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary-emphasis">Único</span>
                            @endif
                        </td>
                        <td>{{ $p['created_at']?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td>
                            <form action="{{ route('financeiro-estoque-excluir') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="id" value="{{ $p['id'] }}">
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
    $('#tbl-estoque').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' },
        order: [[5, 'desc']],
        pageLength: 25,
    });
});
</script>
@endsection

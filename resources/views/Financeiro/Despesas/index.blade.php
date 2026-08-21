@extends('layouts.Financeiro.app')

@section('title', 'Despesas')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 gap-2 flex-wrap">
    <div class="d-flex align-items-center gap-2">
        <iconify-icon icon="solar:bill-list-bold-duotone" style="font-size:1.5rem; color:#2C9BA5;"></iconify-icon>
        <h4 class="mb-0 fw-semibold">Despesas</h4>
    </div>
    <a href="{{ route('financeiro-despesas-criar') }}" class="btn btn-primary btn-sm">
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
                        <th>Registrado por</th>
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
                        <td>{{ $d['usuario_nome'] ?? '—' }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('financeiro-despesas-detalhar', $d['id']) }}"
                                   class="btn btn-outline-primary btn-sm" title="Ver detalhes" aria-label="Ver detalhes">
                                    <iconify-icon icon="solar:eye-bold-duotone" inline></iconify-icon>
                                </a>
                                <form action="{{ route('financeiro-despesas-excluir') }}" method="POST" class="d-inline btn-excluir-despesa-form">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $d['id'] }}">
                                    <button type="button" class="btn btn-outline-danger btn-sm btn-excluir-despesa"
                                            title="Excluir" aria-label="Excluir">
                                        <iconify-icon icon="solar:trash-bin-trash-bold-duotone" inline></iconify-icon>
                                    </button>
                                </form>
                            </div>
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

    $(document).on('click', '.btn-excluir-despesa', function () {
        var $form = $(this).closest('form');
        Swal.fire({
            title: 'Excluir despesa?',
            text: 'Essa ação não pode ser desfeita.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Excluir',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6b7280',
            reverseButtons: true,
        }).then(function (result) {
            if (result.isConfirmed) {
                $form.trigger('submit');
            }
        });
    });
});
</script>
@endsection

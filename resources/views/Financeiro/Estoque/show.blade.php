@extends('layouts.Financeiro.app')

@section('title', 'Detalhes do Produto')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">

        <div class="d-flex align-items-center justify-content-between mb-4 gap-2 flex-wrap">
            <div class="d-flex align-items-center gap-2">
                <iconify-icon icon="solar:box-bold-duotone" style="font-size:1.5rem; color:#2C9BA5;"></iconify-icon>
                <h4 class="mb-0 fw-semibold">Detalhes do produto</h4>
            </div>
            <a href="{{ route('financeiro-estoque') }}" class="btn btn-outline-secondary btn-sm">
                <iconify-icon icon="solar:arrow-left-linear" inline></iconify-icon>
                Voltar para a lista
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">
                <h5 class="fw-semibold mb-1">{{ $produto['nome_produto'] }}</h5>
                @if($produto['cobrar_mensal'])
                    <span class="badge bg-info-subtle text-info-emphasis mb-4">Cobrança mensal</span>
                @else
                    <span class="badge bg-secondary-subtle text-secondary-emphasis mb-4">Cobrança única</span>
                @endif

                <dl class="row mb-0 mt-3">
                    <dt class="col-5 col-sm-4 text-muted fw-normal">Lote</dt>
                    <dd class="col-7 col-sm-8">{{ $produto['lote'] ?? '—' }}</dd>

                    <dt class="col-5 col-sm-4 text-muted fw-normal">Quantidade</dt>
                    <dd class="col-7 col-sm-8">{{ $produto['quantidade'] }}</dd>

                    <dt class="col-5 col-sm-4 text-muted fw-normal">Valor</dt>
                    <dd class="col-7 col-sm-8 fw-bold">R$ {{ number_format($produto['valor'], 2, ',', '.') }}</dd>

                    <dt class="col-5 col-sm-4 text-muted fw-normal">Registrado em</dt>
                    <dd class="col-7 col-sm-8">{{ $produto['created_at']?->format('d/m/Y H:i') ?? '—' }}</dd>

                    @if($produto['descricao'])
                        <dt class="col-5 col-sm-4 text-muted fw-normal">Descrição</dt>
                        <dd class="col-7 col-sm-8">{{ $produto['descricao'] }}</dd>
                    @endif
                </dl>

                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('financeiro-estoque') }}" class="btn btn-outline-secondary">
                        Voltar
                    </a>
                    <a href="{{ route('financeiro-estoque-editar', $produto['id']) }}" class="btn btn-warning">
                        <iconify-icon icon="solar:pen-bold-duotone" inline></iconify-icon>
                        Editar produto
                    </a>
                    <form action="{{ route('financeiro-estoque-excluir') }}" method="POST" id="form-excluir-produto">
                        @csrf
                        <input type="hidden" name="id" value="{{ $produto['id'] }}">
                        <button type="button" class="btn btn-danger" id="btn-excluir-produto">
                            <iconify-icon icon="solar:trash-bin-trash-bold-duotone" inline></iconify-icon>
                            Excluir produto
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scriptTable')
<script>
$(document).ready(function () {
    $('#btn-excluir-produto').on('click', function () {
        Swal.fire({
            title: 'Excluir produto?',
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
                document.getElementById('form-excluir-produto').submit();
            }
        });
    });
});
</script>
@endsection

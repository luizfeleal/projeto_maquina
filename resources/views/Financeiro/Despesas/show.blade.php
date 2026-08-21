@extends('layouts.Financeiro.app')

@section('title', 'Detalhes da Despesa')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-9">

        <div class="d-flex align-items-center justify-content-between mb-4 gap-2 flex-wrap">
            <div class="d-flex align-items-center gap-2">
                <iconify-icon icon="solar:bill-list-bold-duotone" style="font-size:1.5rem; color:#2C9BA5;"></iconify-icon>
                <h4 class="mb-0 fw-semibold">Detalhes da despesa</h4>
            </div>
            <a href="{{ route('financeiro-despesas') }}" class="btn btn-outline-secondary btn-sm">
                <iconify-icon icon="solar:arrow-left-linear" inline></iconify-icon>
                Voltar para a lista
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">
                <div class="row g-4">

                    <div class="col-12 col-lg-6">
                        <h5 class="fw-semibold mb-1">{{ $despesa['descricao'] }}</h5>
                        @if($despesa['tipo'])
                            <span class="badge rounded-pill text-bg-light border mb-4">{{ $despesa['tipo'] }}</span>
                        @endif

                        <dl class="row mb-0 mt-3">
                            <dt class="col-5 col-sm-4 text-muted fw-normal">Valor</dt>
                            <dd class="col-7 col-sm-8 fw-bold text-danger">
                                R$ {{ number_format($despesa['valor'], 2, ',', '.') }}
                            </dd>

                            <dt class="col-5 col-sm-4 text-muted fw-normal">Data</dt>
                            <dd class="col-7 col-sm-8">
                                {{ $despesa['data_despesa']?->format('d/m/Y') ?? '—' }}
                            </dd>

                            @if($despesa['forma_pagamento'])
                                <dt class="col-5 col-sm-4 text-muted fw-normal">Forma de pagamento</dt>
                                <dd class="col-7 col-sm-8">
                                    {{ $despesa['forma_pagamento'] }}
                                </dd>
                            @endif

                            <dt class="col-5 col-sm-4 text-muted fw-normal">Máquina</dt>
                            <dd class="col-7 col-sm-8">
                                @if($despesa['maquina_nome'])
                                    {{ $despesa['maquina_nome'] }}
                                @elseif($despesa['id_maquina'])
                                    Máquina #{{ $despesa['id_maquina'] }}
                                @else
                                    <span class="text-muted">Nenhuma máquina associada</span>
                                @endif
                            </dd>

                            <dt class="col-5 col-sm-4 text-muted fw-normal">Registrado por</dt>
                            <dd class="col-7 col-sm-8">
                                {{ $despesa['usuario_nome'] ?? '—' }}
                            </dd>
                        </dl>

                        <div class="d-flex gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('financeiro-despesas') }}" class="btn btn-outline-secondary btn-despesa-cancelar">
                                Voltar
                            </a>
                            <form action="{{ route('financeiro-despesas-excluir') }}" method="POST" id="form-excluir-despesa">
                                @csrf
                                <input type="hidden" name="id" value="{{ $despesa['id'] }}">
                                <button type="button" class="btn btn-danger btn-despesa-excluir" id="btn-excluir-despesa">
                                    <iconify-icon icon="solar:trash-bin-trash-bold-duotone" inline></iconify-icon>
                                    Excluir despesa
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <label class="form-label fw-semibold text-muted small text-uppercase">Comprovante</label>
                        <div class="comprovante-frame">
                            @if($despesa['comprovante_url'])
                                @if($despesa['comprovante_e_imagem'])
                                    <a href="{{ $despesa['comprovante_url'] }}" target="_blank" rel="noopener">
                                        <img src="{{ $despesa['comprovante_url'] }}"
                                             alt="Comprovante da despesa {{ $despesa['descricao'] }}"
                                             class="img-fluid rounded">
                                    </a>
                                @else
                                    <div class="d-flex flex-column align-items-center justify-content-center gap-2 py-5">
                                        <iconify-icon icon="solar:document-bold-duotone" style="font-size:2.5rem; color:#6b7280;"></iconify-icon>
                                        <a href="{{ $despesa['comprovante_url'] }}" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm">
                                            Abrir comprovante (PDF)
                                        </a>
                                    </div>
                                @endif
                            @else
                                <div class="d-flex flex-column align-items-center justify-content-center gap-2 py-5 text-muted">
                                    <iconify-icon icon="solar:gallery-broken-bold-duotone" style="font-size:2.5rem;"></iconify-icon>
                                    <p class="mb-0">Nenhum comprovante anexado</p>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .comprovante-frame {
        background: #f9fafb;
        border: 1px dashed #d1d5db;
        border-radius: .75rem;
        min-height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .comprovante-frame img {
        max-height: 420px;
        width: auto;
        max-width: 100%;
        display: block;
    }

    .btn-despesa-cancelar,
    .btn-despesa-excluir {
        padding: .55rem 1.25rem;
        font-weight: 600;
        border-radius: .5rem;
    }
</style>
@endsection

@section('scriptTable')
<script>
$(document).ready(function () {
    $('#btn-excluir-despesa').on('click', function () {
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
                document.getElementById('form-excluir-despesa').submit();
            }
        });
    });
});
</script>
@endsection

@extends('layouts.Financeiro.app')

@section('title', 'Registrar Produto')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-7">

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3">
                <iconify-icon icon="solar:box-bold-duotone" style="font-size:1.4rem; color:#2C9BA5;"></iconify-icon>
                <h5 class="mb-0 fw-semibold">Registrar Produto no Estoque</h5>
            </div>
            <div class="card-body p-4 p-lg-5">

                <form action="{{ route('financeiro-estoque-registrar') }}"
                      method="POST"
                      class="needs-validation"
                      novalidate>
                    @csrf

                    <div class="row g-3 mb-4">
                        <div class="col-sm-8">
                            <label class="form-label fw-semibold" for="nome_produto">
                                Nome do produto <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   id="nome_produto"
                                   name="nome_produto"
                                   class="form-control @error('nome_produto') is-invalid @enderror"
                                   placeholder="Ex: Placa controladora XYZ"
                                   value="{{ old('nome_produto') }}"
                                   required maxlength="150">
                            @error('nome_produto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold" for="lote">
                                Lote <span class="text-muted fw-normal">(opcional)</span>
                            </label>
                            <input type="text"
                                   id="lote"
                                   name="lote"
                                   class="form-control @error('lote') is-invalid @enderror"
                                   placeholder="Ex: LOTE-2026-07"
                                   value="{{ old('lote') }}"
                                   maxlength="100">
                            @error('lote')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="descricao">
                            Descrição <span class="text-muted fw-normal">(opcional)</span>
                        </label>
                        <textarea id="descricao"
                                  name="descricao"
                                  rows="3"
                                  class="form-control @error('descricao') is-invalid @enderror"
                                  placeholder="Detalhes do produto"
                                  maxlength="1000">{{ old('descricao') }}</textarea>
                        @error('descricao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold" for="quantidade">
                                Quantidade <span class="text-danger">*</span>
                            </label>
                            <input type="number"
                                   id="quantidade"
                                   name="quantidade"
                                   class="form-control @error('quantidade') is-invalid @enderror"
                                   placeholder="0"
                                   value="{{ old('quantidade') }}"
                                   min="0" step="1"
                                   required>
                            @error('quantidade')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold" for="valor_display">Valor <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text"
                                       id="valor_display"
                                       inputmode="decimal"
                                       autocomplete="off"
                                       class="form-control @error('valor') is-invalid @enderror"
                                       placeholder="0,00"
                                       required>
                                @error('valor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <input type="hidden" id="valor" name="valor" value="{{ old('valor') }}">
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input type="checkbox"
                               id="cobrar_mensal"
                               name="cobrar_mensal"
                               class="form-check-input"
                               value="1"
                               {{ old('cobrar_mensal') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="cobrar_mensal">
                            Cobrar mensalmente
                        </label>
                        <div class="form-text text-muted">Marque se este produto gera cobrança recorrente ao cliente.</div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('financeiro-estoque') }}" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <iconify-icon icon="solar:disk-bold-duotone" inline></iconify-icon>
                            Salvar produto
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scriptTable')
<script>
$(document).ready(function () {
    $('#valor_display').mask('#.##0,00', { reverse: true });

    function syncValorOculto() {
        var raw = $('#valor_display').val() || '';
        var normalizado = raw.replace(/\./g, '').replace(',', '.');
        $('#valor').val(normalizado);
    }

    @if(old('valor'))
        $('#valor_display').val(
            Number('{{ old('valor') }}').toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
        );
    @endif
    syncValorOculto();

    $('#valor_display').on('input blur', syncValorOculto);
    $('form.needs-validation').on('submit', syncValorOculto);
});
</script>
@endsection

@extends('layouts.Financeiro.app')

@section('title', 'Nova Despesa')

@section('content')
<div class="row justify-content-center despesa-form-page">
    <div class="col-12 col-lg-7">

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3">
                <iconify-icon icon="solar:bill-list-bold-duotone" style="font-size:1.4rem; color:#1a6b4a;"></iconify-icon>
                <h5 class="mb-0 fw-semibold">Registrar Despesa</h5>
            </div>
            <div class="card-body p-4 p-lg-5">

                <form action="{{ route('financeiro-despesas-registrar') }}"
                      method="POST"
                      enctype="multipart/form-data"
                      class="needs-validation"
                      novalidate>
                    @csrf

                    <div class="row g-3 mb-4">
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
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold" for="data_despesa">Data <span class="text-danger">*</span></label>
                            <input type="date"
                                   id="data_despesa"
                                   name="data_despesa"
                                   class="form-control @error('data_despesa') is-invalid @enderror"
                                   value="{{ old('data_despesa', date('Y-m-d')) }}"
                                   required>
                            @error('data_despesa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold" for="tipo">Tipo / Categoria</label>
                            <select id="tipo"
                                    name="tipo"
                                    data-placeholder="Selecione uma categoria"
                                    class="form-select js-select2 @error('tipo') is-invalid @enderror">
                                <option value=""></option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria }}" {{ old('tipo') === $categoria ? 'selected' : '' }}>{{ $categoria }}</option>
                                @endforeach
                            </select>
                            @error('tipo')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold" for="id_maquina">Máquina <span class="text-muted fw-normal">(opcional)</span></label>
                            <select id="id_maquina"
                                    name="id_maquina"
                                    data-placeholder="Nenhuma máquina específica"
                                    class="form-select js-select2 @error('id_maquina') is-invalid @enderror">
                                <option value=""></option>
                                @foreach($maquinas as $maquina)
                                    <option value="{{ $maquina['id_maquina'] }}" {{ (string) old('id_maquina') === (string) $maquina['id_maquina'] ? 'selected' : '' }}>
                                        {{ $maquina['maquina_nome'] }}@if($maquina['local_nome']) — {{ $maquina['local_nome'] }}@endif
                                    </option>
                                @endforeach
                            </select>
                            @error('id_maquina')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="descricao">
                            Descrição <span class="text-muted fw-normal">(opcional)</span>
                        </label>
                        <input type="text"
                               id="descricao"
                               name="descricao"
                               class="form-control @error('descricao') is-invalid @enderror"
                               placeholder="Ex: Manutenção de máquina no ponto Centro"
                               value="{{ old('descricao') }}"
                               maxlength="255">
                        @error('descricao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="comprovante">
                            <iconify-icon icon="solar:document-bold-duotone" style="font-size:1rem;"></iconify-icon>
                            Nota fiscal / Comprovante
                        </label>
                        <input type="file"
                               id="comprovante"
                               name="comprovante"
                               class="form-control @error('comprovante') is-invalid @enderror"
                               accept=".pdf,.jpg,.jpeg,.png">
                        <div class="form-text text-muted">Formatos aceitos: PDF, JPG, JPEG, PNG. Máximo 5 MB.</div>
                        @error('comprovante')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2 justify-content-end despesa-form-actions">
                        <a href="{{ route('financeiro-despesas') }}" class="btn btn-outline-secondary btn-despesa-cancelar">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-success btn-despesa-salvar">
                            <iconify-icon icon="solar:disk-bold-duotone" inline></iconify-icon>
                            Salvar despesa
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>

<style>
    .despesa-form-page {
        padding-top: .5rem;
    }

    .despesa-form-page .card-body {
        padding-bottom: 2.25rem !important;
    }

    .despesa-form-actions {
        margin-top: .5rem;
        padding-top: 1.25rem;
        border-top: 1px solid rgba(0, 0, 0, .06);
    }

    .btn-despesa-cancelar,
    .btn-despesa-salvar {
        min-width: 140px;
        padding: .55rem 1.25rem;
        font-weight: 600;
        border-radius: .5rem;
    }

    .btn-despesa-cancelar {
        color: #495057 !important;
        border-color: #ced4da;
        background-color: #fff;
    }

    .btn-despesa-cancelar:hover,
    .btn-despesa-cancelar:focus {
        background-color: #f1f3f5;
        border-color: #adb5bd;
        color: #212529 !important;
    }

    .btn-despesa-salvar {
        box-shadow: 0 2px 6px rgba(25, 135, 84, .25);
    }

    .btn-despesa-salvar:hover,
    .btn-despesa-salvar:focus {
        box-shadow: 0 4px 10px rgba(25, 135, 84, .35);
        transform: translateY(-1px);
    }

    .select2-container--bootstrap-5 .select2-selection {
        min-height: calc(1.5em + .75rem + 2px);
    }
</style>
@endsection

@section('scriptTable')
<script>
$(document).ready(function () {
    $('.js-select2').select2({
        theme: 'bootstrap-5',
        width: '100%',
        allowClear: true,
        placeholder: function () {
            return $(this).data('placeholder');
        }
    });

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

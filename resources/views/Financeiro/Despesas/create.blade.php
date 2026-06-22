@extends('layouts.Financeiro.app')

@section('title', 'Nova Despesa')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-7">

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3">
                <iconify-icon icon="solar:bill-list-bold-duotone" style="font-size:1.4rem; color:#1a6b4a;"></iconify-icon>
                <h5 class="mb-0 fw-semibold">Registrar Despesa</h5>
            </div>
            <div class="card-body p-4">

                <form action="{{ route('financeiro-despesas-registrar') }}"
                      method="POST"
                      enctype="multipart/form-data"
                      class="needs-validation"
                      novalidate>
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="descricao">Descrição <span class="text-danger">*</span></label>
                        <input type="text"
                               id="descricao"
                               name="descricao"
                               class="form-control @error('descricao') is-invalid @enderror"
                               placeholder="Ex: Manutenção de máquina"
                               value="{{ old('descricao') }}"
                               required maxlength="255">
                        @error('descricao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold" for="valor">Valor (R$) <span class="text-danger">*</span></label>
                            <input type="number"
                                   id="valor"
                                   name="valor"
                                   class="form-control @error('valor') is-invalid @enderror"
                                   placeholder="0,00"
                                   value="{{ old('valor') }}"
                                   min="0.01" step="0.01"
                                   required>
                            @error('valor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="tipo">Tipo / Categoria</label>
                        <input type="text"
                               id="tipo"
                               name="tipo"
                               class="form-control @error('tipo') is-invalid @enderror"
                               placeholder="Ex: Manutenção, Aluguel, Outros"
                               value="{{ old('tipo') }}"
                               maxlength="100">
                        @error('tipo')
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

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('financeiro-despesas') }}" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-success">
                            <iconify-icon icon="solar:disk-bold-duotone" inline></iconify-icon>
                            Salvar despesa
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
@endsection

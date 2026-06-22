@extends('layouts.Financeiro.app')

@section('title', 'Registrar Placa')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-6">

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3">
                <iconify-icon icon="solar:cpu-bold-duotone" style="font-size:1.4rem; color:#1a6b4a;"></iconify-icon>
                <h5 class="mb-0 fw-semibold">Registrar Serial de Placa</h5>
            </div>
            <div class="card-body p-4">

                <p class="text-muted small mb-4">
                    Informe o número de série completo do hardware. O sistema exibirá apenas os últimos 4 dígitos nas telas de monitoramento.
                </p>

                <form action="{{ route('financeiro-placas-registrar') }}"
                      method="POST"
                      class="needs-validation"
                      novalidate>
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="placa_serial">
                            Serial completo da placa <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               id="placa_serial"
                               name="placa_serial"
                               class="form-control font-monospace @error('placa_serial') is-invalid @enderror"
                               placeholder="Ex: SN-2024-ABCD-1234"
                               value="{{ old('placa_serial') }}"
                               required maxlength="255"
                               style="letter-spacing:.05em; text-transform:uppercase;">
                        @error('placa_serial')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted" id="serial-preview" style="display:none;">
                            Será exibido como: <strong id="serial-curto" class="font-monospace text-dark"></strong>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="id_maquina">ID da Máquina (opcional)</label>
                        <input type="text"
                               id="id_maquina"
                               name="id_maquina"
                               class="form-control @error('id_maquina') is-invalid @enderror"
                               placeholder="Ex: 42"
                               value="{{ old('id_maquina') }}"
                               maxlength="100">
                        @error('id_maquina')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('financeiro-placas') }}" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-success">
                            <iconify-icon icon="solar:disk-bold-duotone" inline></iconify-icon>
                            Salvar placa
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
document.getElementById('placa_serial').addEventListener('input', function () {
    const val = this.value.toUpperCase();
    this.value = val;
    const preview = document.getElementById('serial-preview');
    const curto   = document.getElementById('serial-curto');
    if (val.length >= 4) {
        curto.textContent = '...' + val.slice(-4);
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
});
</script>
@endsection

@extends('layouts.Financeiro.app')

@section('title', 'Nova Mensalidade')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-7">

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3">
                <iconify-icon icon="solar:wallet-money-bold-duotone" style="font-size:1.4rem; color:#2C9BA5;"></iconify-icon>
                <h5 class="mb-0 fw-semibold">Nova Mensalidade</h5>
            </div>
            <div class="card-body p-4 p-lg-5">

                <form action="{{ route('financeiro-mensalidades-registrar') }}"
                      method="POST"
                      class="needs-validation"
                      novalidate>
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="id_cliente">Cliente <span class="text-danger">*</span></label>
                        <select id="id_cliente"
                                name="id_cliente"
                                data-placeholder="Selecione o cliente"
                                class="form-select js-select2 @error('id_cliente') is-invalid @enderror"
                                required>
                            <option value=""></option>
                            @foreach($clientes as $c)
                                <option value="{{ $c['id_cliente'] }}" {{ (string) old('id_cliente') === (string) $c['id_cliente'] ? 'selected' : '' }}>{{ $c['cliente_nome'] }}</option>
                            @endforeach
                        </select>
                        @error('id_cliente')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

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
                            <label class="form-label fw-semibold" for="vencimento">Vencimento <span class="text-danger">*</span></label>
                            <input type="date"
                                   id="vencimento"
                                   name="vencimento"
                                   class="form-control @error('vencimento') is-invalid @enderror"
                                   value="{{ old('vencimento') }}"
                                   required>
                            @error('vencimento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="status">Status</label>
                        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                            @foreach($status as $s)
                                <option value="{{ $s }}" {{ old('status', 'pendente') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check mb-4">
                        <input type="checkbox"
                               id="gerar_boleto"
                               name="gerar_boleto"
                               class="form-check-input"
                               value="1"
                               {{ old('gerar_boleto', true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="gerar_boleto">
                            Gerar boleto automaticamente (Efí)
                        </label>
                        <div class="form-text text-muted">Se marcado, um boleto de cobrança é criado assim que a mensalidade for salva.</div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('financeiro-mensalidades') }}" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <iconify-icon icon="solar:disk-bold-duotone" inline></iconify-icon>
                            Salvar mensalidade
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

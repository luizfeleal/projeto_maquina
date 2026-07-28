@extends('layouts.Financeiro.app')

@section('title', 'Detalhes da Mensalidade')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">

        <div class="d-flex align-items-center justify-content-between mb-4 gap-2 flex-wrap">
            <div class="d-flex align-items-center gap-2">
                <iconify-icon icon="solar:wallet-money-bold-duotone" style="font-size:1.5rem; color:#1a6b4a;"></iconify-icon>
                <h4 class="mb-0 fw-semibold">Detalhes da mensalidade</h4>
            </div>
            <a href="{{ route('financeiro-mensalidades') }}" class="btn btn-outline-secondary btn-sm">
                <iconify-icon icon="solar:arrow-left-linear" inline></iconify-icon>
                Voltar para a lista
            </a>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4 p-lg-5">
                <h5 class="fw-semibold mb-1">{{ $mensalidade['cliente_nome'] ?? ('Cliente #' . $mensalidade['id_cliente']) }}</h5>
                @if(!empty($mensalidade['cliente_email']))
                    <div class="text-muted small mb-3">{{ $mensalidade['cliente_email'] }}</div>
                @endif

                @php
                    $badgeClass = match($mensalidade['status'] ?? '') {
                        'pago' => 'bg-success',
                        'atrasado' => 'bg-danger',
                        default => 'bg-warning text-dark',
                    };
                @endphp
                <span class="badge {{ $badgeClass }} mb-4">{{ ucfirst($mensalidade['status'] ?? '—') }}</span>

                <dl class="row mb-0 mt-3">
                    <dt class="col-5 col-sm-4 text-muted fw-normal">Valor</dt>
                    <dd class="col-7 col-sm-8 fw-bold">R$ {{ number_format((float) $mensalidade['valor'], 2, ',', '.') }}</dd>

                    <dt class="col-5 col-sm-4 text-muted fw-normal">Vencimento</dt>
                    <dd class="col-7 col-sm-8">{{ \Illuminate\Support\Carbon::parse($mensalidade['vencimento'])->format('d/m/Y') }}</dd>

                    <dt class="col-5 col-sm-4 text-muted fw-normal">Cadastrada em</dt>
                    <dd class="col-7 col-sm-8">{{ !empty($mensalidade['created_at']) ? \Illuminate\Support\Carbon::parse($mensalidade['created_at'])->format('d/m/Y H:i') : '—' }}</dd>
                </dl>

                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-danger btn-sm" id="btn-excluir-mensalidade">
                        <iconify-icon icon="solar:trash-bin-trash-bold-duotone" inline></iconify-icon>
                        Excluir mensalidade
                    </button>
                </div>
                <form action="{{ route('financeiro-mensalidades-excluir') }}" method="POST" id="form-excluir-mensalidade" class="d-none">
                    @csrf
                    <input type="hidden" name="id" value="{{ $mensalidade['id'] }}">
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3">
                <iconify-icon icon="solar:pen-bold-duotone" style="font-size:1.2rem; color:#b45309;"></iconify-icon>
                <h6 class="mb-0 fw-semibold">Editar valores e status</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('financeiro-mensalidades-atualizar', $mensalidade['id']) }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold" for="valor_display">Valor</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" id="valor_display" inputmode="decimal" autocomplete="off" class="form-control" placeholder="0,00" required>
                            </div>
                            <input type="hidden" id="valor" name="valor" value="{{ $mensalidade['valor'] }}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold" for="vencimento">Vencimento</label>
                            <input type="date" id="vencimento" name="vencimento" class="form-control" value="{{ \Illuminate\Support\Carbon::parse($mensalidade['vencimento'])->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold" for="status">Status</label>
                            <select id="status" name="status" class="form-select" required>
                                @foreach($status as $s)
                                    <option value="{{ $s }}" {{ $mensalidade['status'] === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-warning">
                            <iconify-icon icon="solar:disk-bold-duotone" inline></iconify-icon>
                            Salvar alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3">
                <iconify-icon icon="solar:bill-check-bold-duotone" style="font-size:1.2rem; color:#1a6b4a;"></iconify-icon>
                <h6 class="mb-0 fw-semibold">Cobrança (boleto Efí)</h6>
            </div>
            <div class="card-body p-4">
                @if(!empty($mensalidade['efi_charge_id']))
                    <dl class="row mb-0">
                        <dt class="col-5 col-sm-4 text-muted fw-normal">Status da cobrança</dt>
                        <dd class="col-7 col-sm-8">
                            <span class="badge bg-secondary-subtle text-secondary-emphasis">{{ $mensalidade['boleto_status'] ?? '—' }}</span>
                        </dd>

                        <dt class="col-5 col-sm-4 text-muted fw-normal">Identificador (Efí)</dt>
                        <dd class="col-7 col-sm-8">{{ $mensalidade['efi_charge_id'] }}</dd>

                        @if(!empty($mensalidade['boleto_barcode']))
                            <dt class="col-5 col-sm-4 text-muted fw-normal">Código de barras</dt>
                            <dd class="col-7 col-sm-8" style="word-break: break-all;">{{ $mensalidade['boleto_barcode'] }}</dd>
                        @endif
                    </dl>

                    <div class="d-flex flex-wrap gap-2 mt-4 pt-3 border-top">
                        @if(!empty($mensalidade['boleto_link']))
                            <a href="{{ $mensalidade['boleto_link'] }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm">
                                <iconify-icon icon="solar:link-bold-duotone" inline></iconify-icon>
                                Ver boleto
                            </a>
                        @endif
                        @if(!empty($mensalidade['boleto_pdf']))
                            <a href="{{ $mensalidade['boleto_pdf'] }}" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm">
                                <iconify-icon icon="solar:file-download-bold-duotone" inline></iconify-icon>
                                Baixar PDF
                            </a>
                        @endif
                        <button type="button" class="btn btn-outline-success btn-sm" id="btn-reenviar-boleto">
                            <iconify-icon icon="solar:letter-bold-duotone" inline></iconify-icon>
                            Reenviar por e-mail
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" id="btn-cancelar-boleto">
                            <iconify-icon icon="solar:close-circle-bold-duotone" inline></iconify-icon>
                            Cancelar boleto
                        </button>
                    </div>

                    <form action="{{ route('financeiro-mensalidades-boleto-cancelar', $mensalidade['id']) }}" method="POST" id="form-cancelar-boleto" class="d-none">
                        @csrf
                    </form>
                    <form action="{{ route('financeiro-mensalidades-boleto-reenviar', $mensalidade['id']) }}" method="POST" id="form-reenviar-boleto" class="d-none">
                        @csrf
                        <input type="hidden" name="email" id="reenviar-email">
                    </form>
                @else
                    <p class="text-muted mb-3">Esta mensalidade ainda não possui boleto gerado.</p>
                    <form action="{{ route('financeiro-mensalidades-boleto-gerar', $mensalidade['id']) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">
                            <iconify-icon icon="solar:bill-check-bold-duotone" inline></iconify-icon>
                            Gerar boleto
                        </button>
                    </form>
                @endif
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

    $('#valor_display').val(
        Number($('#valor').val() || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
    );
    syncValorOculto();

    $('#valor_display').on('input blur', syncValorOculto);
    $('form.needs-validation').on('submit', syncValorOculto);

    $('#btn-excluir-mensalidade').on('click', function () {
        Swal.fire({
            title: 'Excluir mensalidade?',
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
                document.getElementById('form-excluir-mensalidade').submit();
            }
        });
    });

    $('#btn-cancelar-boleto').on('click', function () {
        Swal.fire({
            title: 'Cancelar boleto?',
            text: 'O boleto será cancelado na Efí e não poderá mais ser pago.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Cancelar boleto',
            cancelButtonText: 'Voltar',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6b7280',
            reverseButtons: true,
        }).then(function (result) {
            if (result.isConfirmed) {
                document.getElementById('form-cancelar-boleto').submit();
            }
        });
    });

    $('#btn-reenviar-boleto').on('click', function () {
        Swal.fire({
            title: 'Reenviar boleto',
            input: 'email',
            inputLabel: 'E-mail de destino (opcional, usa o e-mail do cliente se vazio)',
            inputPlaceholder: 'cliente@email.com',
            showCancelButton: true,
            confirmButtonText: 'Reenviar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6b7280',
            reverseButtons: true,
        }).then(function (result) {
            if (result.isConfirmed) {
                document.getElementById('reenviar-email').value = result.value || '';
                document.getElementById('form-reenviar-boleto').submit();
            }
        });
    });
});
</script>
@endsection

@extends('layouts.Financeiro.app')

@section('title', 'Estoque de Placas')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 gap-2 flex-wrap">
    <div class="d-flex align-items-center gap-2">
        <iconify-icon icon="solar:cpu-bold-duotone" style="font-size:1.5rem; color:#1a6b4a;"></iconify-icon>
        <h4 class="mb-0 fw-semibold">Estoque de Placas</h4>
    </div>
    <a href="{{ route('financeiro-placas-criar') }}" class="btn btn-success btn-sm">
        <iconify-icon icon="solar:add-circle-bold-duotone" inline></iconify-icon>
        Registrar placa
    </a>
</div>

@if($placas->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5 text-muted">
            <iconify-icon icon="solar:inbox-line-duotone" style="font-size:3rem;"></iconify-icon>
            <p class="mt-2 mb-0">Nenhuma placa registrada.</p>
        </div>
    </div>
@else
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table id="tbl-placas" class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Serial completo</th>
                        <th>Identificação (últimos 4)</th>
                        <th>Máquina vinculada</th>
                        <th>Registrado em</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($placas as $p)
                    <tr>
                        <td class="font-monospace small text-muted">{{ $p['placa_serial'] }}</td>
                        <td>
                            <span class="badge bg-dark font-monospace fs-6">...{{ $p['serial_curto'] }}</span>
                        </td>
                        <td>{{ $p['id_maquina'] ?? '—' }}</td>
                        <td>{{ $p['created_at']?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td>
                            <form action="{{ route('financeiro-placas-excluir') }}" method="POST" class="d-inline">
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
    $('#tbl-placas').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' },
        order: [[3, 'desc']],
        pageLength: 25,
    });
});
</script>
@endsection

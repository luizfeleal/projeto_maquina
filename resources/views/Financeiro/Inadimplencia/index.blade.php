@extends('layouts.Financeiro.app')

@section('title', 'Inadimplência')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 gap-2 flex-wrap">
    <div class="d-flex align-items-center gap-2">
        <iconify-icon icon="solar:danger-triangle-bold-duotone" style="font-size:1.5rem; color:#f59e0b;"></iconify-icon>
        <div>
            <h4 class="mb-0 fw-semibold">Inadimplência</h4>
            <p class="text-muted small mb-0">Mensalidades não pagas com vencimento há mais de {{ $diasTolerancia }} dia(s).</p>
        </div>
    </div>
</div>

@if(empty($inadimplentes))
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5 text-muted">
            <iconify-icon icon="solar:check-circle-bold-duotone" style="font-size:3rem; color:#16a34a;"></iconify-icon>
            <p class="mt-2 mb-0">Nenhum cliente inadimplente no momento.</p>
        </div>
    </div>
@else
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table id="tbl-inadimplentes" class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Cliente</th>
                        <th>Status</th>
                        <th>Vencimento</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inadimplentes as $m)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $m['cliente_nome'] }}</div>
                            @if(!empty($m['cliente_email']))
                                <div class="text-muted" style="font-size:.8rem;">{{ $m['cliente_email'] }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ ($m['status'] ?? '') === 'atrasado' ? 'bg-danger' : 'bg-warning text-dark' }}">
                                {{ ucfirst($m['status'] ?? '—') }}
                            </span>
                        </td>
                        <td>{{ \Illuminate\Support\Carbon::parse($m['vencimento'])->format('d/m/Y') }}</td>
                        <td class="fw-bold text-danger">R$ {{ number_format((float) $m['valor'], 2, ',', '.') }}</td>
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
    $('#tbl-inadimplentes').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' },
        order: [[2, 'asc']],
        pageLength: 25,
    });
});
</script>
@endsection

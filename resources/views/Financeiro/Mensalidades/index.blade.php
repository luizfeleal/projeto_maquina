@extends('layouts.Financeiro.app')

@section('title', 'Mensalidades')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 gap-2 flex-wrap">
    <div class="d-flex align-items-center gap-2">
        <iconify-icon icon="solar:wallet-money-bold-duotone" style="font-size:1.5rem; color:#2C9BA5;"></iconify-icon>
        <h4 class="mb-0 fw-semibold">Mensalidades</h4>
    </div>
    <a href="{{ route('financeiro-mensalidades-criar') }}" class="btn btn-primary btn-sm">
        <iconify-icon icon="solar:add-circle-bold-duotone" inline></iconify-icon>
        Nova mensalidade
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1">Pagas</div>
                <div class="fw-bold fs-5 text-success">R$ {{ number_format($resumo['pago']['valor_total'] ?? 0, 2, ',', '.') }}</div>
                <div class="text-muted small">{{ $resumo['pago']['total'] ?? 0 }} mensalidade(s)</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1">Pendentes</div>
                <div class="fw-bold fs-5 text-warning">R$ {{ number_format($resumo['pendente']['valor_total'] ?? 0, 2, ',', '.') }}</div>
                <div class="text-muted small">{{ $resumo['pendente']['total'] ?? 0 }} mensalidade(s)</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1">Atrasadas</div>
                <div class="fw-bold fs-5 text-danger">R$ {{ number_format($resumo['atrasado']['valor_total'] ?? 0, 2, ',', '.') }}</div>
                <div class="text-muted small">{{ $resumo['atrasado']['total'] ?? 0 }} mensalidade(s)</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                @php
                    $totalGeral = ($resumo['pago']['valor_total'] ?? 0) + ($resumo['pendente']['valor_total'] ?? 0) + ($resumo['atrasado']['valor_total'] ?? 0);
                    $totalQtd   = ($resumo['pago']['total'] ?? 0) + ($resumo['pendente']['total'] ?? 0) + ($resumo['atrasado']['total'] ?? 0);
                @endphp
                <div class="text-muted small text-uppercase fw-semibold mb-1">Total</div>
                <div class="fw-bold fs-5">R$ {{ number_format($totalGeral, 2, ',', '.') }}</div>
                <div class="text-muted small">{{ $totalQtd }} mensalidade(s)</div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('financeiro-mensalidades') }}" class="row g-3 align-items-end">
            <div class="col-sm-6 col-lg-3">
                <label class="form-label fw-semibold mb-1" for="f_cliente">Cliente</label>
                <select id="f_cliente" name="id_cliente" data-placeholder="Todos os clientes" class="form-select js-select2">
                    <option value=""></option>
                    @foreach($clientes as $c)
                        <option value="{{ $c['id_cliente'] }}" {{ (string) ($filtros['id_cliente'] ?? '') === (string) $c['id_cliente'] ? 'selected' : '' }}>{{ $c['cliente_nome'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-lg-2">
                <label class="form-label fw-semibold mb-1" for="f_status">Status</label>
                <select id="f_status" name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="pago" {{ ($filtros['status'] ?? '') === 'pago' ? 'selected' : '' }}>Pago</option>
                    <option value="pendente" {{ ($filtros['status'] ?? '') === 'pendente' ? 'selected' : '' }}>Pendente</option>
                    <option value="atrasado" {{ ($filtros['status'] ?? '') === 'atrasado' ? 'selected' : '' }}>Atrasado</option>
                </select>
            </div>
            <div class="col-sm-6 col-lg-2">
                <label class="form-label fw-semibold mb-1" for="f_vi">Vencimento de</label>
                <input type="date" id="f_vi" name="vencimento_inicio" class="form-control" value="{{ $filtros['vencimento_inicio'] ?? '' }}">
            </div>
            <div class="col-sm-6 col-lg-2">
                <label class="form-label fw-semibold mb-1" for="f_vf">Vencimento até</label>
                <input type="date" id="f_vf" name="vencimento_fim" class="form-control" value="{{ $filtros['vencimento_fim'] ?? '' }}">
            </div>
            <div class="col-lg-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <iconify-icon icon="solar:filter-bold-duotone" inline></iconify-icon>
                    Filtrar
                </button>
                <a href="{{ route('financeiro-mensalidades') }}" class="btn btn-outline-secondary">Limpar</a>
            </div>
        </form>
    </div>
</div>

@if(empty($mensalidades))
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5 text-muted">
            <iconify-icon icon="solar:inbox-line-duotone" style="font-size:3rem;"></iconify-icon>
            <p class="mt-2 mb-0">Nenhuma mensalidade encontrada.</p>
        </div>
    </div>
@else
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table id="tbl-mensalidades" class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Cliente</th>
                        <th>Valor</th>
                        <th>Vencimento</th>
                        <th>Status</th>
                        <th>Cobrança</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mensalidades as $m)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $m['cliente_nome'] ?? '—' }}</div>
                            @if(!empty($m['cliente_email']))
                                <div class="text-muted" style="font-size:.8rem;">{{ $m['cliente_email'] }}</div>
                            @endif
                        </td>
                        <td class="fw-bold">R$ {{ number_format((float) ($m['valor'] ?? 0), 2, ',', '.') }}</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($m['vencimento'])->format('d/m/Y') }}</td>
                        <td>
                            @php
                                $badgeClass = match($m['status'] ?? '') {
                                    'pago' => 'bg-success',
                                    'atrasado' => 'bg-danger',
                                    default => 'bg-warning text-dark',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ ucfirst($m['status'] ?? '—') }}</span>
                        </td>
                        <td>
                            @if(!empty($m['boleto_status']))
                                <span class="badge bg-secondary-subtle text-secondary-emphasis">{{ $m['boleto_status'] }}</span>
                            @else
                                <span class="text-muted small">Sem boleto</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('financeiro-mensalidades-detalhar', $m['id']) }}"
                               class="btn btn-outline-primary btn-sm" title="Ver detalhes" aria-label="Ver detalhes">
                                <iconify-icon icon="solar:eye-bold-duotone" inline></iconify-icon>
                            </a>
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
    $('.js-select2').select2({
        theme: 'bootstrap-5',
        width: '100%',
        allowClear: true,
        placeholder: function () {
            return $(this).data('placeholder');
        }
    });

    $('#tbl-mensalidades').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' },
        order: [[2, 'desc']],
        pageLength: 25,
    });
});
</script>
@endsection

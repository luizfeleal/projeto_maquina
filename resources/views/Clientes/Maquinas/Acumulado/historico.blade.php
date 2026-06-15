@extends('layouts.Clientes.app')
@section('title', 'Histórico de Resets Parciais')
@section('content')

<div class="w-100 div-center-column" style="padding-top: 99px; padding-bottom: 100px;">
    <div class="container section container-platform div-center-column" style="margin-top: 15px;">

        <h4 class="mb-4">Histórico de Resets Parciais</h4>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show w-100" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Filtros --}}
        <form method="GET" action="{{ route('clientes-maquinas-resets-historico') }}" class="row g-3 mb-4">
            <div class="col-md-5">
                <label class="form-label">ID da Máquina</label>
                <input type="text" name="id_maquina" class="form-control" value="{{ request('id_maquina') }}" placeholder="ID da máquina">
            </div>
            <div class="col-md-3">
                <label class="form-label">Data início</label>
                <input type="date" name="data_inicio" class="form-control" value="{{ request('data_inicio') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Data fim</label>
                <input type="date" name="data_fim" class="form-control" value="{{ request('data_fim') }}">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
            </div>
        </form>

        @php $data = $resets['data'] ?? []; @endphp

        @if(empty($data))
            <div class="alert alert-info">Nenhum reset parcial encontrado.</div>
        @else
        <div class="tabela_responsiva">
            <table class="table table-striped table-hover" style="width:100%">
                <thead class="table-dark">
                    <tr>
                        <th>Máquina</th>
                        <th>Local</th>
                        <th>Valor coletado</th>
                        <th>Realizado por</th>
                        <th>Data/hora</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $reset)
                    <tr>
                        <td>{{ $reset['maquina_nome'] ?? $reset['id_maquina'] }}</td>
                        <td>{{ $reset['local_nome'] ?? '—' }}</td>
                        <td>R$ {{ number_format($reset['valor_ultima_coleta'], 2, ',', '.') }}</td>
                        <td>{{ $reset['realizado_por_nome'] ?? $reset['realizado_por'] ?? '—' }}</td>
                        <td>{{ \Carbon\Carbon::parse($reset['created_at'])->format('d/m/Y H:i:s') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @php
            $currentPage = $resets['current_page'] ?? 1;
            $lastPage    = $resets['last_page'] ?? 1;
            $total       = $resets['total'] ?? count($data);
        @endphp
        <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted">Total: {{ $total }} registro(s)</small>
            <div class="d-flex gap-2">
                @if($currentPage > 1)
                    <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}" class="btn btn-sm btn-outline-secondary">Anterior</a>
                @endif
                @if($currentPage < $lastPage)
                    <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}" class="btn btn-sm btn-outline-secondary">Próxima</a>
                @endif
            </div>
        </div>
        @endif

        <div class="mt-4">
            <a href="{{ route('clientes-maquinas-acumulado') }}" class="btn btn-outline-primary">
                <i class="fa-solid fa-arrow-left me-1"></i>Voltar ao Acumulado
            </a>
        </div>

    </div>
</div>

@endsection

@extends('layouts.Financeiro.app')

@section('title', 'Clientes')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 gap-2 flex-wrap">
    <div class="d-flex align-items-center gap-2">
        <iconify-icon icon="solar:users-group-rounded-bold-duotone" style="font-size:1.5rem; color:#1a6b4a;"></iconify-icon>
        <h4 class="mb-0 fw-semibold">Clientes</h4>
    </div>
    <a href="{{ route('financeiro-clientes-criar') }}" class="btn btn-success btn-sm">
        <iconify-icon icon="solar:add-circle-bold-duotone" inline></iconify-icon>
        Novo cliente
    </a>
</div>

@if($clientes->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5 text-muted">
            <iconify-icon icon="solar:inbox-line-duotone" style="font-size:3rem;"></iconify-icon>
            <p class="mt-2 mb-0">Nenhum cliente cadastrado ainda.</p>
        </div>
    </div>
@else
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table id="tbl-clientes" class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Razão social</th>
                        <th>CNPJ</th>
                        <th>Cidade/UF</th>
                        <th>Contato</th>
                        <th>Credencial</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clientes as $c)
                    @php
                        $efi = ($c['checkbox_efi'] ?? 0) == 1;
                        $pagbank = ($c['checkbox_pagbank'] ?? 0) == 1;
                        $cidadeUf = collect([$c['cliente_cidade'] ?? null, $c['cliente_uf'] ?? null])->filter()->implode('/');
                    @endphp
                    <tr>
                        <td class="fw-semibold">{{ $c['cliente_nome'] }}</td>
                        <td>{{ $c['cliente_cpf_cnpj'] ?? '—' }}</td>
                        <td>{{ $cidadeUf ?: '—' }}</td>
                        <td>
                            <div>{{ $c['cliente_email'] ?? '—' }}</div>
                            <div class="text-muted" style="font-size:.8rem;">{{ $c['cliente_celular'] ?? '' }}</div>
                        </td>
                        <td>
                            @if($efi && $pagbank)
                                <span class="badge bg-info-subtle text-info-emphasis">Efí + PagBank</span>
                            @elseif($efi)
                                <span class="badge bg-success-subtle text-success-emphasis">Efí</span>
                            @elseif($pagbank)
                                <span class="badge bg-primary-subtle text-primary-emphasis">PagBank</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary-emphasis">Nenhuma</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('usuario-detalhar', $c['id_cliente']) }}"
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
    $('#tbl-clientes').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' },
        order: [[0, 'asc']],
        pageLength: 25,
    });
});
</script>
@endsection

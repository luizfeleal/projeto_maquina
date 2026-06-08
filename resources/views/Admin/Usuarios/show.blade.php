@extends('layouts.app')
@section('title', 'Detalhar Usuário')
@section('content')

@php
    // Avatar: 2 iniciais do nome
    $nameParts = array_values(array_filter(explode(' ', trim($cliente['cliente_nome'] ?? ''))));
    $initials  = strtoupper(
        substr($nameParts[0] ?? '', 0, 1) .
        substr(count($nameParts) > 1 ? end($nameParts) : '', 0, 1)
    );

    $normalizeCred = function ($items) {
        if (empty($items)) {
            return null;
        }

        $cred = (array) (is_array($items) ? reset($items) : $items);
        $cred['id'] = $cred['id_credencial']
            ?? $cred['id_cred_api_pix']
            ?? $cred['id']
            ?? $cred['id_cred']
            ?? null;

        return $cred;
    };

    $maskValue = function ($value, $visible = 4) {
        if ($value === null || $value === '') {
            return '-';
        }

        $value = (string) $value;
        $length = strlen($value);

        if ($length <= $visible) {
            return str_repeat('*', $length);
        }

        return substr($value, 0, $visible) . str_repeat('*', min(12, $length - $visible));
    };

    $efiCred     = $normalizeCred($credencial_efi);
    $pagbankCred = $normalizeCred($credencial_pagbank);

    $hasEfi     = !empty($efiCred);
    $hasPagbank = !empty($pagbankCred);

    $efiId     = $efiCred['id'] ?? null;
    $pagbankId = $pagbankCred['id'] ?? null;

    $permiteEfi     = ($cliente['checkbox_efi'] ?? 0) == 1;
    $permitePagbank = ($cliente['checkbox_pagbank'] ?? 0) == 1;

    $totalLocais = count($locais);
@endphp

<div class="container py-4">
    <div class="row g-4">

        {{-- ── Cabeçalho de perfil ── --}}
        <div class="col-12">
            <div class="detail-profile-card">

                {{-- Avatar --}}
                <div class="detail-avatar" aria-hidden="true">{{ $initials }}</div>

                {{-- Informações principais --}}
                <div class="detail-profile-body">
                    <h2 class="detail-name">{{ $cliente['cliente_nome'] }}</h2>

                    <div class="detail-meta">
                        <span class="meta-item">
                            <iconify-icon icon="solar:letter-linear"></iconify-icon>
                            {{ $cliente['cliente_email'] }}
                        </span>
                        <span class="meta-item">
                            <iconify-icon icon="solar:phone-linear"></iconify-icon>
                            {{ $cliente['cliente_celular'] }}
                        </span>
                        <span class="meta-item">
                            <iconify-icon icon="solar:user-id-linear"></iconify-icon>
                            {{ $cliente['cliente_cpf_cnpj'] }}
                        </span>
                    </div>
                </div>

                {{-- Ações --}}
                <div class="detail-profile-actions">
                    <a href="{{ route('usuario-editar', $cliente['id_cliente']) }}"
                       class="btn btn-primary btn-sm">
                        <iconify-icon icon="solar:pen-linear"></iconify-icon>
                        Editar
                    </a>
                    <a href="{{ route('usuarios') }}" class="btn btn-outline-secondary btn-sm">
                        <iconify-icon icon="solar:arrow-left-linear"></iconify-icon>
                        Voltar
                    </a>
                </div>

            </div>
        </div>

        {{-- ── Coluna esquerda: Credenciais ── --}}
        <div class="col-lg-6">
            <div class="detail-section-card">

                <div class="detail-section-header">
                    <iconify-icon icon="solar:key-bold-duotone"></iconify-icon>
                    <h3>Credenciais de Pagamento</h3>
                </div>

                <div class="detail-section-body">

                    @if(!$permiteEfi && !$permitePagbank)
                        <div class="detail-empty">
                            <iconify-icon icon="solar:key-minimalistic-bold-duotone"></iconify-icon>
                            <p>Nenhuma permissão de pagamento habilitada para este usuário.</p>
                        </div>
                    @else

                    {{-- EFÍ Bank --}}
                    @if($permiteEfi)
                    <div class="credential-item">
                        <div class="credential-main">
                            <div class="credential-header-row">
                                <span class="credential-name">EFÍ Bank (Pix)</span>
                                <span class="credential-status {{ $hasEfi ? 'status-ok' : 'status-missing' }}">
                                    <iconify-icon icon="{{ $hasEfi ? 'solar:check-circle-bold' : 'solar:close-circle-bold' }}"></iconify-icon>
                                    {{ $hasEfi ? 'Configurado' : 'Não Configurado' }}
                                </span>
                            </div>

                            @if($hasEfi)
                            <div class="credential-details">
                                <div class="credential-field">
                                    <span class="credential-field-label">Client ID</span>
                                    <span class="credential-field-value">{{ $efiCred['client_id'] ?? '-' }}</span>
                                </div>
                                <div class="credential-field">
                                    <span class="credential-field-label">Client Secret</span>
                                    <span class="credential-field-value">{{ $maskValue($efiCred['client_secret'] ?? null) }}</span>
                                </div>
                                @if(!empty($efiCred['caminho_certificado']))
                                <div class="credential-field">
                                    <span class="credential-field-label">Certificado</span>
                                    <span class="credential-field-value">{{ basename($efiCred['caminho_certificado']) }}</span>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>

                        <div class="credential-actions">
                            @if($hasEfi && $efiId)
                                <a href="{{ route('credencial-editar-efi', $efiId) }}"
                                   class="btn btn-sm btn-primary"
                                   title="Editar credencial EFÍ">
                                    <iconify-icon icon="solar:pen-linear"></iconify-icon>
                                    Editar
                                </a>
                                <button type="button"
                                        class="btn btn-sm btn-danger btn-excluir-credencial-detail"
                                        data-id="{{ $efiId }}"
                                        data-tipo="EFI"
                                        title="Excluir credencial EFÍ">
                                    <iconify-icon icon="solar:trash-bin-trash-linear"></iconify-icon>
                                    Excluir
                                </button>
                            @else
                                <a href="{{ route('credencial-criar-efi') }}?id_cliente={{ $cliente['id_cliente'] }}"
                                   class="btn btn-sm btn-primary"
                                   title="Configurar credencial EFÍ">
                                    <iconify-icon icon="solar:pen-linear"></iconify-icon>
                                    Editar
                                </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- PagBank --}}
                    @if($permitePagbank)
                    <div class="credential-item">
                        <div class="credential-main">
                            <div class="credential-header-row">
                                <span class="credential-name">PagBank</span>
                                <span class="credential-status {{ $hasPagbank ? 'status-ok' : 'status-missing' }}">
                                    <iconify-icon icon="{{ $hasPagbank ? 'solar:check-circle-bold' : 'solar:close-circle-bold' }}"></iconify-icon>
                                    {{ $hasPagbank ? 'Configurado' : 'Não Configurado' }}
                                </span>
                            </div>

                            @if($hasPagbank)
                            <div class="credential-details">
                                <div class="credential-field">
                                    <span class="credential-field-label">Client ID</span>
                                    <span class="credential-field-value">{{ $pagbankCred['client_id'] ?? '-' }}</span>
                                </div>
                                <div class="credential-field">
                                    <span class="credential-field-label">Client Secret</span>
                                    <span class="credential-field-value">{{ $maskValue($pagbankCred['client_secret'] ?? null) }}</span>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="credential-actions">
                            @if($hasPagbank && $pagbankId)
                                <a href="{{ route('credencial-editar-pagbank', $pagbankId) }}"
                                   class="btn btn-sm btn-primary"
                                   title="Editar credencial PagBank">
                                    <iconify-icon icon="solar:pen-linear"></iconify-icon>
                                    Editar
                                </a>
                                <button type="button"
                                        class="btn btn-sm btn-danger btn-excluir-credencial-detail"
                                        data-id="{{ $pagbankId }}"
                                        data-tipo="PagBank"
                                        title="Excluir credencial PagBank">
                                    <iconify-icon icon="solar:trash-bin-trash-linear"></iconify-icon>
                                    Excluir
                                </button>
                            @else
                                <a href="{{ route('credencial-criar-pagbank') }}?id_cliente={{ $cliente['id_cliente'] }}"
                                   class="btn btn-sm btn-primary"
                                   title="Configurar credencial PagBank">
                                    <iconify-icon icon="solar:pen-linear"></iconify-icon>
                                    Editar
                                </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    @endif
                </div>
            </div>
        </div>

        {{-- ── Coluna direita: Locais ── --}}
        <div class="col-lg-6">
            <div class="detail-section-card">

                <div class="detail-section-header">
                    <iconify-icon icon="solar:map-point-bold-duotone"></iconify-icon>
                    <h3>Locais Vinculados</h3>
                    @if($totalLocais)
                        <span class="detail-count-badge">{{ $totalLocais }}</span>
                    @endif
                </div>

                <div class="detail-section-body">
                    @if($totalLocais === 0)
                        <div class="detail-empty">
                            <iconify-icon icon="solar:map-point-remove-bold-duotone"></iconify-icon>
                            <p>Nenhum local vinculado a este usuário.</p>
                        </div>
                    @else
                        <ul class="location-list" role="list" aria-label="Locais vinculados">
                            @foreach($locais as $local)
                                <li class="location-item" role="listitem">
                                    <iconify-icon icon="solar:map-point-bold-duotone" aria-hidden="true"></iconify-icon>
                                    {{ $local['local_nome'] }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

            </div>
        </div>

    </div>
</div>

<form id="formExcluirCredencialDetail" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@endsection

@section('scriptTable')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-excluir-credencial-detail').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.getAttribute('data-id');
            var tipo = btn.getAttribute('data-tipo');

            Swal.fire({
                icon: 'warning',
                title: 'Excluir credencial?',
                text: 'Tem certeza que deseja excluir a credencial ' + tipo + '? Esta ação não pode ser desfeita.',
                showCancelButton: true,
                confirmButtonText: 'Excluir',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
            }).then(function (result) {
                if (!result.isConfirmed) {
                    return;
                }

                var form = document.getElementById('formExcluirCredencialDetail');
                form.action = @json(url('credenciais/excluir')) + '/' + id;
                form.submit();
            });
        });
    });
});
</script>
@endsection

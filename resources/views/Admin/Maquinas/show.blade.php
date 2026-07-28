@extends('layouts.app')
@section('title', 'Detalhar Máquina')
@section('content')

@php
    $ativo          = ($maquinas['maquina_status']       ?? 0) == 1;
    $bloqueioEfi    = ($maquinas['bloqueio_jogada_efi']    ?? 0) == 1;
    $bloqueioPagbank = ($maquinas['bloqueio_jogada_pagbank'] ?? 0) == 1;
@endphp

<div class="page-heading">
    <h1>Detalhar Máquina</h1>
    <p>Informações e configurações da máquina</p>
</div>

<div class="content-body" style="padding-top:0;">

    {{-- ── Cabeçalho da máquina ── --}}
    <div style="background:#fff; border:1px solid #e8ecf0; border-radius:16px;
                box-shadow:0 1px 4px rgba(0,0,0,.05); overflow:hidden; margin-bottom:16px;">

        <div style="padding:20px 24px; border-bottom:1px solid #f3f4f6;
                    display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
            <div style="display:flex; align-items:center; gap:14px;">
                <div style="width:52px; height:52px; border-radius:14px; flex-shrink:0;
                            background:#e0f2fe; display:flex; align-items:center; justify-content:center;">
                    <iconify-icon icon="solar:monitor-bold-duotone"
                                  style="font-size:1.6rem; color:#0284c7;"></iconify-icon>
                </div>
                <div>
                    <h2 style="margin:0 0 3px; font-size:1.15rem; font-weight:700; color:#111827;">
                        {{ $maquinas['maquina_nome'] }}
                    </h2>
                    <p style="margin:0; font-size:.8rem; color:#9ca3af;
                              display:flex; align-items:center; gap:5px;">
                        <iconify-icon icon="solar:map-point-bold-duotone"
                                      style="font-size:.9rem;"></iconify-icon>
                        {{ $locais['local_nome'] }}
                    </p>
                </div>
            </div>

            <span style="font-size:.8rem; font-weight:700; padding:6px 14px; border-radius:20px;
                         display:flex; align-items:center; gap:6px;
                         background:{{ $ativo ? '#dcfce7' : '#fee2e2' }};
                         color:{{ $ativo ? '#16a34a' : '#dc2626' }};">
                <span style="width:8px; height:8px; border-radius:50%; flex-shrink:0;
                             background:{{ $ativo ? '#16a34a' : '#dc2626' }};
                             {{ $ativo ? 'box-shadow:0 0 0 2px #bbf7d0;' : '' }}"></span>
                {{ $ativo ? 'Online' : 'Offline' }}
            </span>
        </div>

        {{-- Dados técnicos --}}
        <div style="padding:20px 24px; display:grid;
                    grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:20px;
                    border-bottom:1px solid #f3f4f6;">
            <div>
                <p style="margin:0 0 4px; font-size:.68rem; font-weight:700; text-transform:uppercase;
                           letter-spacing:.06em; color:#9ca3af;">ID da Placa</p>
                <p style="margin:0; font-size:.9rem; font-weight:600; color:#374151;">
                    {{ $maquinas['id_placa'] }}
                </p>
            </div>
            <div>
                <p style="margin:0 0 4px; font-size:.68rem; font-weight:700; text-transform:uppercase;
                           letter-spacing:.06em; color:#9ca3af;">Último Contato</p>
                <p style="margin:0; font-size:.9rem; font-weight:600; color:#374151;">
                    {{ $maquinas['maquina_ultimo_contato'] }}
                </p>
            </div>
            <div>
                <p style="margin:0 0 4px; font-size:.68rem; font-weight:700; text-transform:uppercase;
                           letter-spacing:.06em; color:#9ca3af;">Data de Criação</p>
                <p style="margin:0; font-size:.9rem; font-weight:600; color:#374151;">
                    {{ $maquinas['data_criacao'] }}
                </p>
            </div>
        </div>

        {{-- Clientes associados --}}
        @if(!empty($clientes))
        <div style="padding:16px 24px;">
            <p style="margin:0 0 10px; font-size:.68rem; font-weight:700; text-transform:uppercase;
                       letter-spacing:.06em; color:#9ca3af;">Clientes Associados</p>
            <div style="display:flex; flex-wrap:wrap; gap:6px;">
                @foreach($clientes as $cliente)
                <span style="background:#f1f5f9; border:1px solid #e2e8f0; border-radius:8px;
                             padding:4px 12px; font-size:.78rem; font-weight:600; color:#374151;
                             display:flex; align-items:center; gap:5px;">
                    <iconify-icon icon="solar:user-bold-duotone"
                                  style="font-size:.85rem; color:#6366f1;"></iconify-icon>
                    {{ $cliente['cliente_nome'] }}
                </span>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- ── Grid: Configurações + Permissões ── --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:16px;">

        {{-- Configurações --}}
        <div style="background:#fff; border:1px solid #e8ecf0; border-radius:16px;
                    box-shadow:0 1px 4px rgba(0,0,0,.05); overflow:hidden;">
            <div style="padding:16px 20px; border-bottom:1px solid #f3f4f6;
                        display:flex; align-items:center; gap:8px;">
                <iconify-icon icon="solar:settings-bold-duotone"
                              style="font-size:1.1rem; color:#6366f1;"></iconify-icon>
                <h3 style="margin:0; font-size:.9rem; font-weight:700; color:#111827;">Configurações</h3>
            </div>
            <div style="padding:16px 20px; display:flex; flex-direction:column; gap:12px;">

                {{-- Máquina de cartão --}}
                <div style="display:flex; align-items:center; justify-content:space-between; gap:8px;
                            padding:12px; border-radius:10px; border:1px solid #f3f4f6;
                            background:{{ $possuiMaquinaCartaoAssociada ? '#f0fdf4' : '#fff7f7' }};">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <iconify-icon icon="solar:card-bold-duotone"
                                      style="font-size:1.1rem; color:{{ $possuiMaquinaCartaoAssociada ? '#16a34a' : '#dc2626' }};"></iconify-icon>
                        <span style="font-size:.85rem; font-weight:600; color:#374151;">Máquina de Cartão</span>
                    </div>
                    <span style="font-size:.72rem; font-weight:700; padding:3px 10px; border-radius:20px; white-space:nowrap;
                                 background:{{ $possuiMaquinaCartaoAssociada ? '#dcfce7' : '#fee2e2' }};
                                 color:{{ $possuiMaquinaCartaoAssociada ? '#16a34a' : '#dc2626' }};">
                        {{ $possuiMaquinaCartaoAssociada ? 'Configurado' : 'Não configurado' }}
                    </span>
                </div>

                {{-- QR Code --}}
                <div style="display:flex; align-items:center; justify-content:space-between; gap:8px;
                            padding:12px; border-radius:10px; border:1px solid #f3f4f6;
                            background:{{ $possuiQrCode ? '#f0fdf4' : '#fff7f7' }};">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <iconify-icon icon="solar:qr-code-bold-duotone"
                                      style="font-size:1.1rem; color:{{ $possuiQrCode ? '#16a34a' : '#dc2626' }};"></iconify-icon>
                        <span style="font-size:.85rem; font-weight:600; color:#374151;">QR Code</span>
                    </div>
                    <span style="font-size:.72rem; font-weight:700; padding:3px 10px; border-radius:20px; white-space:nowrap;
                                 background:{{ $possuiQrCode ? '#dcfce7' : '#fee2e2' }};
                                 color:{{ $possuiQrCode ? '#16a34a' : '#dc2626' }};">
                        {{ $possuiQrCode ? 'Configurado' : 'Não configurado' }}
                    </span>
                </div>

            </div>
        </div>

        {{-- Permissões de pagamento --}}
        <div style="background:#fff; border:1px solid #e8ecf0; border-radius:16px;
                    box-shadow:0 1px 4px rgba(0,0,0,.05); overflow:hidden;">
            <div style="padding:16px 20px; border-bottom:1px solid #f3f4f6;
                        display:flex; align-items:center; gap:8px;">
                <iconify-icon icon="solar:lock-bold-duotone"
                              style="font-size:1.1rem; color:#ca8a04;"></iconify-icon>
                <h3 style="margin:0; font-size:.9rem; font-weight:700; color:#111827;">Permissões de Pagamento</h3>
            </div>

            <form action="{{ route('maquinas-atualizar') }}" method="POST" id="atualizar-permissao">
                @csrf
                <input type="hidden" name="id_maquina" value="{{ $maquinas['id_maquina'] }}">

                <div style="padding:16px 20px; display:flex; flex-direction:column; gap:12px;">

                    {{-- Jogada Pix --}}
                    <div id="row-efi"
                         style="display:flex; align-items:center; justify-content:space-between; gap:8px;
                                padding:12px; border-radius:10px; border:1px solid #f3f4f6;
                                background:{{ $bloqueioEfi ? '#fff7f7' : '#f0fdf4' }};">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <iconify-icon id="icon-efi" icon="solar:money-bag-bold-duotone"
                                          style="font-size:1.1rem; color:{{ $bloqueioEfi ? '#dc2626' : '#16a34a' }};"></iconify-icon>
                            <div>
                                <p style="margin:0; font-size:.85rem; font-weight:600; color:#374151;">Jogada Pix</p>
                                <p id="label-efi"
                                   style="margin:0; font-size:.72rem; font-weight:700;
                                          color:{{ $bloqueioEfi ? '#dc2626' : '#16a34a' }};">
                                    {{ $bloqueioEfi ? 'Bloqueado' : 'Liberado' }}
                                </p>
                            </div>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" name="bloqueio_jogada_efi" type="checkbox"
                                   role="switch" id="checkboxEfi" {{ $bloqueioEfi ? 'checked' : '' }}>
                        </div>
                    </div>

                    {{-- Jogada Máquininha --}}
                    <div id="row-pagbank"
                         style="display:flex; align-items:center; justify-content:space-between; gap:8px;
                                padding:12px; border-radius:10px; border:1px solid #f3f4f6;
                                background:{{ $bloqueioPagbank ? '#fff7f7' : '#f0fdf4' }};">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <iconify-icon id="icon-pagbank" icon="solar:card-recive-bold-duotone"
                                          style="font-size:1.1rem; color:{{ $bloqueioPagbank ? '#dc2626' : '#16a34a' }};"></iconify-icon>
                            <div>
                                <p style="margin:0; font-size:.85rem; font-weight:600; color:#374151;">Jogada Máquininha</p>
                                <p id="label-pagbank"
                                   style="margin:0; font-size:.72rem; font-weight:700;
                                          color:{{ $bloqueioPagbank ? '#dc2626' : '#16a34a' }};">
                                    {{ $bloqueioPagbank ? 'Bloqueado' : 'Liberado' }}
                                </p>
                            </div>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" name="bloqueio_jogada_pagbank" type="checkbox"
                                   role="switch" id="checkboxPagbank" {{ $bloqueioPagbank ? 'checked' : '' }}>
                        </div>
                    </div>

                </div>

                <div style="padding:0 20px 20px;">
                    <button class="btn btn-primary w-100" type="submit">Salvar Permissões</button>
                </div>
            </form>
        </div>

    </div>

</div>

@endsection

@section('scriptTable')
<script>
$(document).ready(function () {

    function updateToggleRow(rowId, iconId, labelId, blocked) {
        var $row   = $('#' + rowId);
        var $icon  = $('#' + iconId);
        var $label = $('#' + labelId);
        var color  = blocked ? '#dc2626' : '#16a34a';
        var bg     = blocked ? '#fff7f7' : '#f0fdf4';

        $row.css('background', bg);
        $icon.attr('style', 'font-size:1.1rem; color:' + color + ';');
        $label.text(blocked ? 'Bloqueado' : 'Liberado').css('color', color);
    }

    $('#checkboxEfi').on('change', function () {
        updateToggleRow('row-efi', 'icon-efi', 'label-efi', this.checked);
    });

    $('#checkboxPagbank').on('change', function () {
        updateToggleRow('row-pagbank', 'icon-pagbank', 'label-pagbank', this.checked);
    });

});
</script>
@endsection

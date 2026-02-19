@extends('layouts.Clientes.app')
@section('title', 'Editar Credenciais')
@section('content')

<div class="usuarios div-center-column w-100" style="padding-top: 99px;">
    <h1 style="padding-top: 80px; text-align: center;">Editar Credenciais API PIX</h1>
    <div class="container section container-platform div-center-column" style="margin-top: 15px; height: 100%;">

        <form method="GET" action="{{ route('cliente-credencial-listar') }}" class="w-100 mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="tipo_cred" class="form-label">Tipo</label>
                    <select name="tipo_cred" id="tipo_cred" class="form-select">
                        <option value="">Todos os tipos</option>
                        <option value="efi" {{ ($tipo_cred ?? '') == 'efi' ? 'selected' : '' }}>EFI</option>
                        <option value="pagbank" {{ ($tipo_cred ?? '') == 'pagbank' ? 'selected' : '' }}>PagBank</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-magnifying-glass"></i> Buscar
                    </button>
                    <a href="{{ route('cliente-credencial-listar') }}" class="btn btn-secondary">Limpar</a>
                </div>
            </div>
        </form>

        <div class="table-responsive w-100">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($credenciais as $credencial)
                        @php
                            $cliente_nome = !empty($clientes) ? (reset($clientes)['cliente_nome'] ?? 'Meu cadastro') : 'Meu cadastro';
                        @endphp
                        <tr>
                            <td>{{ $credencial['id'] ?? '-' }}</td>
                            <td>{{ $cliente_nome }}</td>
                            <td>
                                <span class="badge {{ $credencial['tipo_cred'] == 'efi' ? 'bg-primary' : 'bg-success' }}">
                                    {{ strtoupper($credencial['tipo_cred']) }}
                                </span>
                            </td>
                            <td>
                                @if($credencial['tipo_cred'] == 'efi')
                                    <a href="{{ route('cliente-credencial-editar-efi', $credencial['id']) }}" class="btn btn-sm btn-primary">
                                        <i class="fa-solid fa-pen"></i> Editar
                                    </a>
                                @else
                                    <a href="{{ route('cliente-credencial-editar-pagbank', $credencial['id']) }}" class="btn btn-sm btn-success">
                                        <i class="fa-solid fa-pen"></i> Editar
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <i class="fa-solid fa-inbox fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">Nenhuma credencial encontrada.</p>
                                <small class="text-muted">Você pode <a href="{{ route('cliente-credencial-criar-efi') }}">criar uma credencial EFI</a> ou <a href="{{ route('cliente-credencial-criar-pagbank') }}">PagBank</a>.</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(session('success'))
            <div class="alert alert-success mt-3">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mt-3">{{ session('error') }}</div>
        @endif
    </div>
</div>

@endsection

@section('scriptTable')
<script>
    $(document).ready(function() {});
</script>
@endsection

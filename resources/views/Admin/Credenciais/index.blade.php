@extends('layouts.app')
@section('title', 'Editar Credenciais')
@section('content')

<div class="usuarios div-center-column w-100" style="padding-top: 99px;">
    <h1 style="padding-top: 80px; text-align: center;">Editar Credenciais API PIX</h1>
    <div class="container section container-platform div-center-column" style="margin-top: 15px; height: 100%;">

        <form method="GET" action="{{ route('credencial-listar') }}" class="w-100 mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="id_cliente" class="form-label">Cliente</label>
                    <select name="id_cliente" id="id_cliente" class="form-select select-cliente">
                        <option value="">Todos os clientes</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente['id_cliente'] }}" {{ ($id_cliente ?? '') == $cliente['id_cliente'] ? 'selected' : '' }}>
                                {{ $cliente['cliente_nome'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="tipo_cred" class="form-label">Tipo</label>
                    <select name="tipo_cred" id="tipo_cred" class="form-select">
                        <option value="">Todos os tipos</option>
                        <option value="efi" {{ ($tipo_cred ?? '') == 'efi' ? 'selected' : '' }}>EFI</option>
                        <option value="pagbank" {{ ($tipo_cred ?? '') == 'pagbank' ? 'selected' : '' }}>PagBank</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-magnifying-glass"></i> Buscar
                    </button>
                    <a href="{{ route('credencial-listar') }}" class="btn btn-secondary">Limpar</a>
                </div>
            </div>
        </form>

        <div class="table-responsive w-100">
            <table id="tabela-credenciais" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($credenciais as $credencial)
                        @php
                            $credId = $credencial['id'] ?? $credencial['id_cred_api_pix'] ?? null;
                            $idCliente = $credencial['id_cliente'] ?? null;
                            $tipoCred = $credencial['tipo_cred'] ?? '';
                            $cliente_nome = collect($clientes)->firstWhere('id_cliente', $idCliente)['cliente_nome'] ?? 'Cliente #' . $idCliente;
                        @endphp
                        <tr>
                            <td>{{ $credId ?? '-' }}</td>
                            <td>{{ $cliente_nome }}</td>
                            <td>
                                <span class="badge {{ $tipoCred == 'efi' ? 'bg-primary' : 'bg-success' }}">
                                    {{ strtoupper($tipoCred) }}
                                </span>
                            </td>
                            <td>
                                @if($credId)
                                    @if($tipoCred == 'efi')
                                        <a href="{{ route('credencial-editar-efi', $credId) }}" class="btn btn-sm btn-primary">
                                            <i class="fa-solid fa-pen"></i> Editar
                                        </a>
                                    @else
                                        <a href="{{ route('credencial-editar-pagbank', $credId) }}" class="btn btn-sm btn-primary">
                                            <i class="fa-solid fa-pen"></i> Editar
                                        </a>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-danger btn-excluir-credencial" data-id="{{ $credId }}" data-cliente="{{ $cliente_nome }}" data-tipo="{{ strtoupper($tipoCred) }}">
                                        <i class="fa-solid fa-trash"></i> Excluir
                                    </button>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(empty($credenciais))
            <div class="text-center py-4">
                <i class="fa-solid fa-inbox fa-2x text-muted mb-2"></i>
                <p class="text-muted mb-0">Nenhuma credencial encontrada com os filtros selecionados.</p>
                <small class="text-muted">Tente alterar os filtros ou <a href="{{ route('credencial-criar-efi') }}">crie uma nova credencial</a>.</small>
            </div>
        @endif

        <!-- Modal de confirmação de exclusão -->
        <div class="modal fade" id="modalExcluirCredencial" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Excluir credencial</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Tem certeza que deseja excluir a credencial <strong id="modalExcluirCredencialInfo"></strong>? Esta ação não pode ser desfeita.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <form id="formExcluirCredencial" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Excluir</button>
                        </form>
                    </div>
                </div>
            </div>
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
    $(document).ready(function() {
        $('.select-cliente').select2({ theme: 'bootstrap-5' });

        $('#tabela-credenciais').DataTable({
            language: { url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json" }
        });

        $('.btn-excluir-credencial').on('click', function() {
            var id = $(this).data('id');
            var cliente = $(this).data('cliente');
            var tipo = $(this).data('tipo');
            $('#modalExcluirCredencialInfo').text(cliente + ' (' + tipo + ')');
            $('#formExcluirCredencial').attr('action', '{{ route("credencial-excluir", "") }}/' + id);
            new bootstrap.Modal('#modalExcluirCredencial').show();
        });
    });
</script>
@endsection

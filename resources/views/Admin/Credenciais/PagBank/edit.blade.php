@extends('layouts.app')
@section('title', 'Editar Credencial PagBank')
@section('content')

        <div  class="usuarios div-center-column w-100"
                style="padding-top: 99px;">

                <h1  style="padding-top: 80px; text-align: center;">Editar Credencial PagBank</h1>
            <div class="container section container-platform div-center-column"
                style="margin-top: 15px; height: 100%;">

                <form action="{{ route('credencial-atualizar', $credencial['id'] ?? $credencial['id_cred_api_pix']) }}" id="editar-credencial-form"  class="w-100 needs-validation form-center"  method="post" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="tipo_cred" value="pagbank">
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;  width: 100%;  margin-bottom: 20px;">
                        <div class="col-md-8">
                            <label for="cliente-exibicao" class="form-label">Cliente:</label>
                            <input type="text" class="form-control bg-light" id="cliente-exibicao" value="{{ collect($clientes)->firstWhere('id_cliente', $credencial['id_cliente'] ?? null)['cliente_nome'] ?? 'Cliente #' . ($credencial['id_cliente'] ?? '') }}" readonly>
                            <input type="hidden" name="id_cliente" value="{{ $credencial['id_cliente'] ?? '' }}">
                        </div>
                    </div>
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                        <div class="col-md-4">
                            <label for="cliente_id" class="form-label">Usuário: <i class="fa-solid fa-circle-info"  data-bs-toggle="tooltip" data-bs-title="Email da conta da pagbank"></i></label>
                            <input type="text" class="form-control" name="cliente_id" id="cliente_id" value="{{ $credencial['client_id'] ?? '' }}" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <label for="cliente_secret" class="form-label">Token:</label>
                            <input type="text" class="form-control" name="cliente_secret" id="cliente_secret" value="{{ $credencial['client_secret'] ?? '' }}" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                        
                    </div>

                    <div style="display:flex; justify-content: center; align-items: center; margin-top: 50px; gap: 10px;">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancelar</a>
                        <button class="btn btn-primary"  type="submit">Atualizar credencial</button>
                    </div>
                </form>
                            @if(session('success'))

                                <div class="modal fade show" id="modalSuccess" tabindex="-1" aria-labelledby="exampleModalCenterTitle" aria-modal="true" role="dialog" style="display: block;">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalCenterTitle">Sucesso</h1>
                                                <button type="button" class="btn-close" onclick="fechaModal('modalSuccess')" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>{{ session('success') }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary" onclick="fechaModal('modalSuccess')" data-dismiss="modal" aria-label="Close">Ok</button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="modal-backdrop fade show" id="modalSuccess-backdrop"></div>
                            @elseif(session('error'))
                                <div class="modal fade show" id="modalError" tabindex="-1" aria-labelledby="exampleModalCenterTitle" aria-modal="true" role="dialog" style="display: block;">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">

                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="exampleModalCenterTitle">Erro</h1>
                                                    <button type="button" class="btn-close" onclick="fechaModal('modalError')" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>{{ session('error') }}</p>
                                                </div>
                                                <div class="modal-footer">
                                                        <button type="button" class="btn btn-primary" onclick="fechaModal('modalError')" data-bs-dismiss="modal" aria-label="Close">OK</button>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-backdrop fade show" id="modalError-backdrop"></div>
                            @endif
            </div>
        </div>

@endsection

@section('scriptTable')

<script>
    $(document).ready(function() {
        validaData();
    });
</script>

@endsection

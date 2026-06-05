@extends('layouts.app')
@section('title', 'Editar Credencial EFÍ')
@section('content')

        <div  class="usuarios div-center-column w-100"
                style="padding-top: 99px;">

            <div class="container section container-platform div-center-column"
                style="margin-top: 15px; height: 100%;">

                <form action="{{ route('credencial-atualizar', $credencial['id'] ?? $credencial['id_cred_api_pix']) }}" id="editar-credencial-form"  class="w-100 needs-validation form-center"  method="post" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="tipo_cred" value="efi">
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;  width: 100%;  margin-bottom: 20px;">
                        <div class="col-md-8">
                            <label for="cliente-exibicao" class="form-label">Cliente:</label>
                            <input type="text" class="form-control bg-light" id="cliente-exibicao" value="{{ collect($clientes)->firstWhere('id_cliente', $credencial['id_cliente'] ?? null)['cliente_nome'] ?? 'Cliente #' . ($credencial['id_cliente'] ?? '') }}" readonly>
                            <input type="hidden" name="id_cliente" value="{{ $credencial['id_cliente'] ?? '' }}">
                        </div>
                    </div>
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                        <div class="col-md-4">
                            <label for="cliente_id" class="form-label">Client ID:</label>
                            <input type="text" class="form-control" name="cliente_id" id="cliente_id" value="{{ $credencial['client_id'] ?? '' }}" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <label for="cliente_secret" class="form-label">Client Secret:</label>
                            <input type="text" class="form-control" name="cliente_secret" id="cliente_secret" value="{{ $credencial['client_secret'] ?? '' }}" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                        
                    </div>
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                        <div class="col-md-8">
                            <label for="cliente_certificado" class="form-label">Certificado (deixe em branco para manter o atual):</label>
                            <input type="file" class="form-control" name="cliente_certificado" id="cliente_certificado">
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>
                            @if(isset($credencial['caminho_certificado']) && $credencial['caminho_certificado'])
                                <small class="form-text text-muted">Certificado atual: {{ basename($credencial['caminho_certificado']) }}</small>
                            @endif

                        </div>
                        
                    </div>

                    <div style="display:flex; justify-content: center; align-items: center; margin-top: 50px; gap: 10px;">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancelar</a>
                        <button class="btn btn-primary"  type="submit">Atualizar credencial</button>
                    </div>
                </form>
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

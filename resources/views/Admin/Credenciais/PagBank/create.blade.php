@extends('layouts.app')
@section('title', 'Criar Credencial PagBank')
@section('content')

        <div  class="usuarios div-center-column w-100"
                style="padding-top: 99px;">

                <h1  style="padding-top: 80px; text-align: center;">Criar Credencial PagBank</h1>
            <div class="container section container-platform div-center-column"
                style="margin-top: 15px; height: 100%;">

                <form action="{{ route('credencial-registrar') }}" id="novo-local-form"  class="w-100 needs-validation form-center"  method="post" enctype="multipart/form-data" novalidate>
                    @csrf

                    <input type="hidden" name="tipo_cred" value="pagbank">
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;  width: 100%;  margin-bottom: 20px;">
                        <div class="col-md-8">
                            <label for="select-cliente" class="form-label">Selecione o cliente*:</label>
                            <select class="select-cliente js-example-basic-multiple js-states form-control" id="select-cliente" placeholder="Selecione" name="select-cliente" required>

                            <option value="">Selecione</option>
                            @foreach($clientes as $cliente)
                                <option value="{{$cliente['id_cliente']}}">{{$cliente['cliente_nome']}}</option>
                            @endforeach
                            </select>
                            <div class="invalid-feedback">
                                <p class="invalid-p" id="select_cliente_mensagem">Campo obrigatório</p>
                            </div>

                        </div>
                    </div>
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                        <div class="col-md-4">
                            <label for="cliente_id" class="form-label">Client ID:</label>
                            <input type="text" class="form-control" name="cliente_id" id="cliente_id" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <label for="cliente_secret" class="form-label">Client Secret:</label>
                            <input type="text" class="form-control" name="cliente_secret" id="cliente_secret" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                        
                    </div>
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                        <div class="col-md-8">
                            <label for="cliente_certificado" class="form-label">Certificado:</label>
                            <input type="file" class="form-control" name="cliente_certificado" id="cliente_certificado" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                        
                    </div>

                    <div style="display:flex; justify-content: center; align-items: center; margin-top: 50px;">
                        <button class="btn btn-primary"  type="submit">Criar credencial</button>
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
        $('.select-cliente').select2({
            theme: 'bootstrap-5'
        });

        validaData();
    });
</script>

@endsection


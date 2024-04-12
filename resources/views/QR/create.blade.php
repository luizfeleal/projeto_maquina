@extends('layouts.app')
@section('title', 'Criar local')
@section('content')

        <div id="criar_qr" class="planos div-center-column w-100"
                style="padding-top: 99px;">

                <h1  style="padding-top: 80px; text-align: center;">Novo QR</h1>
            <div class="container section container-platform div-center-column"
                style="margin-top: 15px; height: 100%;">

                <form action="{{ route('local-registrar') }}" id="novo-local-form" class="w-100">
                    @csrf

                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                        <div class="col-md-6">
                            <label for="select-cliente" class="form-label"> Escolha para qual local será esse QR:</label>
                            <select class="select-cliente js-example-basic-multiple js-states form-control" placeholder="Selecione" name="select-cliente[]" multiple="multiple">

                                @foreach($locais as $local)
                                    <option value="{{$cliente['id_cliente']}}">{{$cliente['cliente_nome']}}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <label for="select-cliente" class="form-label"> Escolha para qual máquina será esse QR:</label>
                            <select class="select-cliente js-example-basic-multiple js-states form-control" placeholder="Selecione" name="select-cliente[]" multiple="multiple">

                                @foreach($maquinas as $maquina)
                                    <option value="{{$maquina['id_maquina']}}">{{$maquina['maquina_nome']}}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                    </div>
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;  width: 100%; ">
                    <p>Insira as informações contidas na plataforma de pagamento:</p>
                        <div class="col-md-6">
                            <label for="select-cliente" class="form-label">Client ID*:</label>
                            <input type="text" name="client_id" id="client_id" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <label for="select-cliente" class="form-label">Client Secret*:</label>
                            <input type="text" name="client_secret" id="client_secret" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                    </div>

                    <div style="display:flex; justify-content: center; align-items: center; margin-top: 50px;">
                        <button class="btn btn-primary"  data-bs-toggle="modal" data-bs-target="#ModalCenterCriar" onclick="setLocalNome('.modal-body', '#nome_local')" type="button">Criar</button>
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
                                                <button type="button" class="btn btn-primary" onclick="fechaModal('modalSuccess')" data-dismiss="modal" aria-label="Close">Imprimir QR</button>
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
            theme: "classic"
        });
    });
</script>

@endsection


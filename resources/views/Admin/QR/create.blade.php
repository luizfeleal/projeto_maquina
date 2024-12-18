@extends('layouts.app')
@section('title', 'Criar local')
@section('content')

        <div id="criar_qr" class="qr div-center-column w-100"
                style="padding-top: 99px;">

                <h1  style="padding-top: 80px; text-align: center;">Novo QR</h1>
            <div class="container section container-platform div-center-column"
                style="margin-top: 15px; height: 100%;">

                <form action="{{ route('qr-registrar') }}" method="post" id="novo-qr-form" class="w-100">
                    @csrf

                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                        <div class="col-md-4">
                            <label for="select-local" class="form-label"> Escolha para qual local será esse QR*:</label>
                            <select class="select-local js-example-basic-multiple js-states form-control" placeholder="Selecione" name="select_local">

                            <option value="" selected>Selecione</option>
                                @foreach($locais as $local)
                                    <option value="{{$local['id_local']}}">{{$local['local_nome']}}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <label for="select-maquina" class="form-label"> Escolha para qual máquina será esse QR*:</label>
                            <select class="select-maquina js-example-basic-multiple js-states form-control" placeholder="Selecione" name="select_maquina">

                            <option value="">Selecione</option>
                                @foreach($maquinas as $maquina)
                                    <option value="{{$maquina['id_maquina']}}">{{$maquina['maquina_nome']}}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                    </div>
                    <!--<div class="row" style="display: flex; flex-direction: row; justify-content: center;  width: 100%; ">
                    <h5 style="text-align: center; padding-top: 25px; padding-bottom: 25px;">Insira as informações contidas na plataforma de pagamento:</h5>
                        <div class="col-md-4">
                            <label for="select-cliente" class="form-label">Client ID*:</label>
                            <input type="text" name="client_id" id="client_id" class="form-control input-text" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <label for="select-cliente" class="form-label">Client Secret*:</label>
                            <input type="text" name="client_secret" id="client_secret" class="form-control input-text" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                    </div>-->

                    <div style="display:flex; justify-content: center; align-items: center; margin-top: 50px;">
                        <button class="btn btn-primary"  data-bs-toggle="modal" data-bs-target="#ModalCenterCriar" type="button">Criar</button>
                    </div>
                </form>

                    <div class="modal fade" id="ModalCenterCriar" tabindex="-1" aria-labelledby="ModalCenterCriar" aria-modal="true" role="dialog">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="ModalCenterTitleCriar">Criar</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Deseja criar o QR Code?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secundary" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>
                                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal" aria-label="Close" onclick="sendFormCriarQr('novo-qr-form')">Sim</button>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                                <a href="{{route('qr-download', ['id_local'=> session('id_local'), 'id_maquina' => session('id_maquina')])}}"   data-dismiss="modal" aria-label="Close"><button class="btn btn-primary" type="button">Imprimir QR</button></a>
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
        $('.select-local').select2({
            theme: 'bootstrap-5'
        });
        $('.select-maquina').select2({
            theme: 'bootstrap-5'
        });
    });
</script>

@endsection


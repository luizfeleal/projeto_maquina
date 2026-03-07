@extends('layouts.Clientes.app')
@section('title', 'Editar máquina')
@section('content')

    <div id="maquinas" class="maquina div-center-column w-100"
            style="padding-top: 99px; padding-bottom: 100px;">

            <h1  style="padding-top: 80px; text-align: center;">Home -> Editar máquina</h1>
        <div class="container section container-platform div-center-column"
            style="margin-top: 15px; height: 100%;">
            
            <form action="{{route('clientes-maquinas-atualizar')}}" method="POST" id="atualizar-maquina" class="w-100 needs-validation" novalidate>
                @csrf

                <input type="hidden" name="id_placa" id="placa_hidden" value="{{$maquinas['id_placa']}}">
                <input type="hidden" name="maquina_status" id="maquina_status" value="{{$maquinas['maquina_status']}}">
                <input type="hidden" name="id_maquina" id="id_maquina" value="{{$maquinas['id_maquina']}}">

                <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-top: 100px;">
                    <div class="col-md-4">
                        <label for="maquina_nome" class="form-label">Nome Máquina:</label>
                        <input type="text" name="maquina_nome" id="maquina_nome" value="{{$maquinas['maquina_nome']}}" class="form-control input-text" placeholder="Nome da Máquina" aria-label="Nome da Máquina" required>
                        <div class="invalid-feedback">
                            <p class="invalid-p" id="maquina_nome_mensagem">Por favor, insira um nome para a máquina.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="placa" class="form-label">ID da placa:</label>
                        <input type="text" name="placa" id="placa" value="{{$maquinas['id_placa']}}" class="form-control input-text" placeholder="Placa" aria-label="Placa" disabled>
                    </div>
                </div>

                <div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-top: 10px; width: 100%;">
                    <div class="col-md-8">
                        <label for="local_nome" class="form-label">Local:</label>
                        <input type="text" name="local_nome" id="local_nome" value="{{$locais['local_nome']}}" class="form-control input-text" placeholder="Local" aria-label="Local" disabled>
                    </div>
                </div>

                <div style="display:flex; justify-content: center; align-items: center;  margin-top: 50px;">
                    <button class="btn btn-primary" type="submit">Atualizar</button>
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
                                <button type="button" class="btn btn-primary" onclick="fechaModal('modalSuccess')" data-dismiss="modal" aria-label="Close">OK</button>
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
        // Validation logic if needed
    });
</script>
@endsection

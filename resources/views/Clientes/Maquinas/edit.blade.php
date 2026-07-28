@extends('layouts.Clientes.app')
@section('title', 'Editar máquina')
@section('content')

    <div id="maquinas" class="maquina div-center-column w-100"
            style="padding-top: 99px; padding-bottom: 100px;">

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

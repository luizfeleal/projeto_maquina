@extends('layouts.app')
@section('title', 'Home')
@section('content')

<div id="relatorios" class="relatorios w-100 div-center-column"
    style=" padding-top: 99px; padding-bottom: 100px;">

    <div class="container section container-platform div-center-column"
        style=" height: 100%;">



        <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px; margin-top: 50px;">
            <div class="col-sm-4 text-center mb-3 mb-sm-0">
                <div class="card" style="height: 100%;">
                    <h5 class="card-header">Saldo Disponível</h5>
                    <div class="card-body">
                    <p class="card-text"><strong>Hoje:</strong> {{number_format($saldo['hoje'], 2, ',', '.')}}</p>
                        <p class="card-text"><strong>Esse Mês:</strong> {{number_format($saldo['mes_atual'], 2, ',', '.')}}</p>
                        <p class="card-text"><strong>Mês Passado:</strong> {{number_format($saldo['mes_passado'], 2, ',', '.')}}</p>
                        <form action="{{ route('relatorio-criar') }}" method="post" class="form-center">
                            @csrf
                            <input type="hidden" name="tipo" value="totalTransacoes">
                            <button type="submit" class="btn btn-primary w-60">Ver detalhes</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 text-center mb-3 mb-sm-0">
                <div class="card" style="height: 100%;">
                    <h5 class="card-header">Máquinas</h5>
                    <div class="card-body">
                        <div style="display: flex; flex-direction: row; justify-content: space-evenly;">
                            <div class="card text-bg-success mb-3" style="max-width: 18rem; color: #fff; background-color: green;">
                                <div class="card-header">Online</div>
                                <div class="card-body">
                                    <h5 class="card-title">{{count($maquinas_online)}}</h5>
                                </div>
                            </div>
                            <div class="card text-bg-danger mb-3" style="max-width: 18rem; color: #fff; background-color: red;">
                                <div class="card-header">Offline</div>
                                <div class="card-body">
                                    <h5 class="card-title">{{count($maquinas_offline)}}</h5>
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('relatorio-criar') }}" method="post" class="form-center">
                            @csrf
                            <input type="hidden" name="tipo" value="maquinasOnOff">
                            <button type="submit" class="btn btn-primary w-60">Ver detalhes</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 text-center mb-3 mb-sm-0">
                <div class="card" style="height: 100%;">
                    <h5 class="card-header">Devoluções</h5>
                    <div class="card-body">
                        <p class="card-text"><strong>Hoje:</strong> {{number_format($devolucoes['hoje'], 2, ',', '.')}}</p>
                        <p class="card-text"><strong>Esse Mês:</strong> {{number_format($devolucoes['mes_atual'], 2, ',', '.')}}</p>
                        <p class="card-text"><strong>Mês Passado:</strong> {{number_format($devolucoes['mes_passado'], 2, ',', '.')}}</p>
                        <form action="{{ route('relatorio-criar') }}" method="post" class="form-center">
                            @csrf
                            <input type="hidden" name="tipo" value="totalTransacoes">
                            <input type="hidden" name="tipo_transacao" value="Estorno">
                            <button type="submit" class="btn btn-primary w-60">Ver detalhes</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        <div class="tabela_responsiva">
            <table id="tabela_maquinas" class="display nowrap table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>ID Placa</th>
                        <th>Máquina</th>
                        <th>QR Code</th>
                        <th>Liberar Jogada</th>
                        <th>Editar</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($maquinas as $maquina)
                    <tr>
                        <td>{{$maquina['id_placa']}}</td>
                        <td>{{$maquina['maquina_nome']}}</td>
                        <td style="text-align: center;"><a href="/qr?id_local={{$maquina['id_local']}}&id_maquina={{$maquina['id_maquina']}}&abrir=true"><i class="fa-solid fa-qrcode icon-sidebar"></i></a></td>
                        <td style="text-align: center;"><a href="/maquinas/liberarJogada?id_maquina={{$maquina['id_maquina']}}"><i class="fa-solid fa-play icon-sidebar"></i></a></td>
                        <td style="text-align: center;"><a href="/maquinas/editar?id_maquina={{$maquina['id_maquina']}}"><i class="fa-solid fa-pen"></i></a></td>
                    </tr>
                    @endforeach

                </tbody>
                <tfoot>
                    <tr>
                        <th>ID Placa</th>
                        <th>Máquina</th>
                        <th>QR Code</th>
                        <th>Liberar Jogada</th>
                        <th>Editar</th>
                    </tr>
                </tfoot>
            </table>
        </div>


    </div>
</div>




@endsection

@section('scriptTable')
<script>
    $(document).ready(function() {


        $('#tabela_maquinas').DataTable({
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
            },
            "scrollX": true,
            "columns": [
                null,
                null,
                null,
                null,
                null,
            ] // Use o array de objetos de coluna dinamicamente criado
        });

        $('.select-cliente-transacoes').select2({
            theme: 'bootstrap-5'
        });
        $('.select-maquina-transacoes').select2({
            theme: 'bootstrap-5'
        });
        $('.select-local-transacoes').select2({
            theme: 'bootstrap-5'
        });
        $('.select-cliente-erros').select2({
            theme: 'bootstrap-5'
        });
        $('.select-maquina-erros').select2({
            theme: 'bootstrap-5'
        });
        $('.select-local-erros').select2({
            theme: 'bootstrap-5'
        });
        $('.select-cliente').select2({
            theme: 'bootstrap-5'
        });
        $('.select-maquina').select2({
            theme: 'bootstrap-5'
        });
        $('.select-local').select2({
            theme: 'bootstrap-5'
        });
    });
</script>

@endsection
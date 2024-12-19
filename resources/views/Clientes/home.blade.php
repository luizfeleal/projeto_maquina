@extends('layouts.Clientes.app')
@section('title', 'Home')
@section('content')

<div id="relatorios" class="relatorios w-100 div-center-column"
    style=" padding-top: 99px; padding-bottom: 100px;">

    <h1 style="padding-top: 80px; text-align: center;">Home</h1>
    <div class="container section container-platform div-center-column"
        style=" height: 100%;">



        <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px; margin-top: 50px;">
            <div class="col-sm-4 text-center mb-3 mb-sm-0">
                <div class="card" style="height: 100%;">
                    <h5 class="card-header">Saldo Disponível</h5>
                    <div class="card-body" style="display: flex; justify-content: center; align-items: center;">
                        <p class="card-text"> <strong>R$ {{number_format($saldo['data'], 2, ',', '.')}}</strong></p>
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
                    </tr>
                </thead>
                <tbody>

                    @foreach($maquinas as $maquina)
                    <tr>
                        <td>{{$maquina['id_placa']}}</td>
                        <td>{{$maquina['maquina_nome']}}</td>
                        <td style="text-align: center;"><a href="/clientes-qr?id_local={{$maquina['id_local']}}&id_maquina={{$maquina['id_maquina']}}&abrir=true"><i class="fa-solid fa-qrcode icon-sidebar"></i></a></td>
                        <td style="text-align: center;"><a href="/clientes-maquinas/viewLiberarJogada?id_maquina={{$maquina['id_maquina']}}"><i class="fa-solid fa-play icon-sidebar"></i></a></td>
                    </tr>
                    @endforeach

                </tbody>
                <tfoot>
                    <tr>
                        <th>ID Placa</th>
                        <th>Máquina</th>
                        <th>QR Code</th>
                        <th>Liberar Jogada</th>
                    </tr>
                </tfoot>
            </table>
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
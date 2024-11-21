@extends('layouts.Clientes.app')
@section('title', 'Minhas Máquinas')
@section('content')

        <div id="guias" class="maquina w-100 div-center-column"
                style=" padding-top: 99px; padding-bottom: 100px;">

                <h1 style="padding-top: 80px; text-align: center; padding-bottom: 50px;">Minhas Máquinas</h1>

            <div class="container section container-platform div-center-column"
                style="margin-top: 15px; height: 100%;">
                
                <table id="tabela_maquinas" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Máquina</th>
                            <th>Última transação</th>
                            <th>Fonte</th>
                            <th>Data e Hora</th>


                        </tr>
                    </thead>
                    <tbody>

                        @foreach($resultado as $extrato)
                            <tr>
                                <!--<td>{{$extrato['local_nome']}}</td>-->
                                <td>{{$extrato['maquina_nome']}}</td>
                                @if($extrato['extrato_operacao'] == "C")
                                    <td>+ R$ {{number_format($extrato['extrato_operacao_valor'], 2, ',', '.')}}</td>
                                @else
                                    <td>- R$ {{number_format($extrato['extrato_operacao_valor'], 2, ',', '.')}}</td>
                                @endif

                                <td>{{$extrato['extrato_operacao_tipo']}}</td>
                                <td>{{date('d/m/Y H:i:s', strtotime($extrato['data_criacao']));}}</td>
                            </tr>
                        @endforeach

                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Máquina</th>
                            <th>Última transação</th>
                            <th>Fonte</th>
                            <th>Data e Hora</th>
                        </tr>
                    </tfoot>
                </table>


                <div class="modal fade" id="ModalCenterExcluir" tabindex="-1" aria-labelledby="ModalCenterExcluir" aria-modal="true" role="dialog">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="ModalCenterTitleExcluir">Excluir Maquina</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Deseja excluir essa máquina?</p>
                                </div>
                                <div class="modal-footer">
                                    <form action="{{route('maquinas-excluir')}}" method="post" id="excluir-maquina" class="w-100 " >
                                    @csrf
                                        <input type="hidden" name="id_maquina" id="id_maquina_input_excluir" value="" >
                                        <button type="submit" class="btn btn-primary" data-bs-dismiss="modal" aria-label="Close" >Sim</button>
                                    </form>
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

        $(document).ready(function(){

            $('#tabela_maquinas').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
                },
                "columns": [
                    null,
                    null,
                    null,
                    { "type": "datetime-ddmmyyyy" }
                ],
            });
        });
    </script>

@endsection


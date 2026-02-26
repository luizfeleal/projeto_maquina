@extends('layouts.Clientes.app')
@section('title', 'Maquina de Cartão')
@section('content')

        <div id="guias" class="usuarios w-100 div-center-column"
                style=" padding-top: 99px; padding-bottom: 100px;">

                <h1 style="padding-top: 80px; text-align: center; padding-bottom: 50px;">Máquina de Cartão</h1>

            <div class="container section container-platform div-center-column"
                style="margin-top: 15px; height: 100%;">
                <div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-bottom: 20px; width: 100%;">
                    <div class="col-md-4">
                    
                    </div>
                    <div class="col-md-4" style="display: flex; flex-direction: row; justify-content: end; align-items: center;">
                        <a  href="{{route('cliente-maquinas-cartao-criar')}}"><button class="btn btn-primary">Nova Máquina <i class="fa-solid fa-plus"></i></button></a>
                    </div>
                </div>

                <div class="tabela_responsiva">

                    <table id="tabela-local" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Nome Máquina</th>
                                <th>Número Máquina</th>
                                <th>Status</th>
                                <th>Excluir</th>
    
    
                            </tr>
                        </thead>
                        <tbody>
    
                        
                        @foreach($maquinasCartao as $maquina)
                            <tr>
                                <td>{{$maquina['maquina_nome']}}</td>
                                <td>{{$maquina['device']}}</td>
                                <td>{{$maquina['status'] == 1 ? "Ativo" : "Inativo"}}</td>


                                <td style="text-align: center;">
                                    <a href="#" class="btn btn-sm btn-danger btn-excluir-maquina-cartao" data-bs-toggle="modal" data-bs-target="#ModalCenterExcluir" data-id="{{ $maquina['id'] }}" data-info="{{ $maquina['maquina_nome'] }} - {{ $maquina['device'] }}" style="background-color: #dc3545; border-color: #dc3545;">
                                        <i class="fa-solid fa-trash"></i> Excluir
                                    </a>
                                </td>
                            </tr>
                        @endforeach
    
    
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Nome Máquina</th>
                                <th>Número Máquina</th>
                                <th>Status</th>
                                <th>Excluir</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>


                <div class="modal fade" id="ModalCenterExcluir" tabindex="-1" aria-labelledby="ModalCenterExcluir" aria-modal="true" role="dialog">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content modal-purple">

                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="ModalCenterTitleExcluir">Excluir máquina de cartão</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Tem certeza que deseja excluir a máquina de cartão <strong id="modalExcluirMaquinaInfo"></strong>? Esta ação não pode ser desfeita e o registro será removido permanentemente da base de dados.</p>
                                </div>
                                <div class="modal-footer">
                                    <form action="{{route('cliente-maquinas-cartao-excluir')}}" method="post" id="formExcluirMaquinaCartao" class="w-100">
                                    @csrf
                                    @method('DELETE')
                                        <input type="hidden" name="id_device" id="id_device_excluir" value="">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-purple">Excluir</button>
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
            $(document).on('click', '.btn-excluir-maquina-cartao', function() {
                var id = $(this).data('id');
                var info = $(this).data('info');
                $('#id_device_excluir').val(id);
                $('#modalExcluirMaquinaInfo').text(info);
            });

            var tabelaGuias= $('#tabela-local').DataTable({
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
        });
    </script>

@endsection


@extends('layouts.app')
@section('title', 'Detalhar Local')
@section('content')

        <div id="local-criar" class="local div-center-column w-100"
                style="padding-top: 99px;">

                <h1  style="padding-top: 80px; text-align: center;">Detalhar Local</h1>
            <div class="container section container-platform div-center-column"
                style="margin-top: 15px; height: 100%;">

                

                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                        <div class="col-md-8">
                            <label for="nome_local" class="form-label">Nome do Local:</label>
                            <input type="text" name="nome_local" id="nome_local" class="form-control input-text" value="{{$local['local_nome']}}" placeholder="Nome local" aria-label="Nome local" disabled>
                            <div class="invalid-feedback">
                                <p class="invalid-p" id="nome_local_mensagem">Campo obrigatório</p>
                            </div>

                        </div>
                    </div>
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;  width: 100%; ">
                        <div class="col-md-8">
                            <label for="select-cliente" class="form-label">Clientes Associados ao Local:</label>
                            <select class="select-cliente js-example-basic-multiple js-states form-control" id="select-cliente" placeholder="Selecione" name="select-cliente[]" multiple="multiple" disabled>

                            @foreach($clienteFiltrado as $cliente)
                                <option selected value="{{$cliente['id_cliente']}}">{{$cliente['cliente_nome']}}</option>
                            @endforeach
                            </select>
                            <div class="invalid-feedback">
                                <p class="invalid-p" id="select_cliente_mensagem">Campo obrigatório</p>
                            </div>

                        </div>
                    </div>

                    
                    <div style="padding-top: 50px; text-align: center;">
                        <h4 style="padding-bottom: 40px;"><i class="fa-solid fa-desktop"></i> Máquinas Associadas  </h4>

                        <table id="tabela-maquinas" class="table table-striped" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Local</th>
                                <th>Máquina</th>
                                <th>Status</th>
                                <th>Total máquina</th>
                                <th>Total PIX</th>
                                <th>Total cartão</th>
                                <th>Total físico</th>
    
    
                            </tr>
                        </thead>
                        <tbody>
    
                        
                        @foreach($maquinasFiltradas as $maquina)
                            <tr>
    
                                <td>{{$maquina['local_nome']}}</td>
                                
                                <td>{{$maquina['maquina_nome']}}</td>
                                    @if($maquina['maquina_status'] == 0) 
                                        <td><i class="fa-solid fa-circle text-danger" ></i></td>
                                    @else
                                        <td><i class="fa-solid fa-circle text-success" ></i></td>
                                    @endif
                                
                                <td>R$ {{number_format($maquina['total_maquina'], 2, ',', '.')}}</td>
                                <td>R$ {{number_format($maquina['total_pix'], 2, ',', '.')}}</td>
                                    
                                <td>R$ {{number_format($maquina['total_cartao'], 2, ',', '.')}}</td>
                                <td>R$ {{number_format($maquina['total_dinheiro'], 2, ',', '.')}}</td>
                            </tr>
    
                        @endforeach
    
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Local</th>
                                <th>Máquina</th>
                                <th>Status</th>
                                <th>Total máquina</th>
                                <th>Total PIX</th>
                                <th>Total cartão</th>
                                <th>Total físico</th>
                            </tr>
                        </tfoot>
                    </table>
                    </div>




                    <div class="modal fade" id="ModalCenterCriar" tabindex="-1" aria-labelledby="ModalCenterCriar" aria-modal="true" role="dialog">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="ModalCenterTitleCriar">Criar</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Deseja criar o Local ?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secundary" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>
                                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal" aria-label="Close" onclick="sendFormCriarLocal()">Sim</button>
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
    $(document).ready(function() {
        $('.select-cliente').select2({
            theme: 'bootstrap-5'
        });

        $("#nome_local").on('blur', () => {
            validarCampoNome('nome_local', 'nome_local_mensagem');
        });

        $("#select-cliente").on('select2:close', () => {
            validarSelectLocalCliente('select-cliente', 'select_cliente_mensagem');
        });

        var tabelaMaquinas= $('#tabela-maquinas').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
                },
                "columns": [
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null
                ] // Use o array de objetos de coluna dinamicamente criado
            });
    });
</script>

@endsection

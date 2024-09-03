@extends('layouts.app')
@section('title', 'Usuários')
@section('content')

        <div id="guias" class="usuarios w-100 div-center-column"
                style=" padding-top: 99px; padding-bottom: 100px;">

                <h1 style="padding-top: 80px; text-align: center; padding-bottom: 50px;">Usuários</h1>

            <div class="container section container-platform div-center-column"
                style="margin-top: 15px; height: 100%;">
                <div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-bottom: 20px; width: 100%;">
                    <div class="col-md-4">
                    
                    </div>
                    <div class="col-md-4" style="display: flex; flex-direction: row; justify-content: end; align-items: center;">
                        <a  href="{{route('usuario-criar')}}"><button class="btn btn-primary">Novo usuário <i class="fa-solid fa-plus"></i></button></a>
                    </div>
                </div>

                <table id="tabela-local" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>CPF/CNPJ</th>
                            <th>Email</th>
                            <th>Celular</th>
                            <th>Detalhar</th>
                            <th>Excluir</th>


                        </tr>
                    </thead>
                    <tbody>

                    
                    @foreach($clientes as $cliente)
                        <tr>
                            <td>{{$cliente['cliente_nome']}}</td>
                            <td>{{$cliente['cliente_cpf_cnpj']}}</td>
                            <td>{{$cliente['cliente_email']}}</td>
                            <td>{{$cliente['cliente_celular']}}</td>
                            <td style="text-align: center;"><a href="{{route('usuario-detalhar', $cliente['id_cliente'])}}"><i class="fa-solid fa-eye"></i></a></td>
                            <td style="text-align: center;"><a href="#" style="color: red !important;" data-bs-toggle="modal" data-bs-target="#ModalCenterExcluir" onclick="setIdClienteExcluir({{$cliente['id_cliente']}}, '#id_cliente_input_excluir')"><i class="fa-solid fa-trash"></i></a></td>

                        </tr>
                    @endforeach


                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Nome</th>
                            <th>CPF/CNPJ</th>
                            <th>Email</th>
                            <th>Celular</th>
                            <th>Detalhar</th>
                            <th>Excluir</th>
                        </tr>
                    </tfoot>
                </table>


                <div class="modal fade" id="ModalCenterExcluir" tabindex="-1" aria-labelledby="ModalCenterExcluir" aria-modal="true" role="dialog">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="ModalCenterTitleExcluir">Excluir Cliente</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Deseja excluir esse cliente?</p>
                                </div>
                                <div class="modal-footer">
                                    <form action="{{route('usuario-excluir')}}" method="post" id="excluir-cliente" class="w-100 " >
                                    @csrf
                                        <input type="hidden" name="id_cliente" id="id_cliente_input_excluir" value="" >
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
            $('#input_filtro_cliente').select2({
            theme: "classic",
            width: "100%"
            });
            $('#input_filtro_local').select2({
                theme: "classic",
            width: "100%"
            });
            $('#input_filtro_maquina').select2({
                theme: "classic",
            width: "100%"
            });


            var tabelaGuias= $('#tabela-local').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
                },
                "columns": [
                    null,
                    null,
                    null,
                    null,
                    null,
                    null
                ] // Use o array de objetos de coluna dinamicamente criado
            });

            
            var dadosTabela = tabelaGuias.rows().data().toArray();
            var startDate = ''
            var endDate = ''

            $("#input_data_inicio_filtro").on('change', () => {
                startDate = $("#input_data_inicio_filtro").val();
            });

            $("#input_data_fim_filtro").on('change', () => {
                endDate = $("#input_data_fim_filtro").val();
            });

            function getDatesBetween(startDate, endDate) {
                const dates = [];
                let currentDate;

                // Verificar se a data de início é fornecida
                if (startDate) {
                    currentDate = new Date(startDate);
                } else {
                    // Se não for fornecida, use a primeira data da tabela (assumindo que dadosTabela está definido)
                    currentDate = new Date(dadosTabela[0][0]);
                }

                // Verificar se a data de término é fornecida
                let endDateValue;
                if (endDate) {
                    endDateValue = new Date(endDate);
                } else {
                    // Se não for fornecida, use a data atual
                    endDateValue = new Date();
                }

                // Loop para adicionar datas ao array
                while (currentDate <= endDateValue) {
                    const dia = currentDate.getDate().toString().padStart(2, '0');
                    const mes = (currentDate.getMonth() + 1).toString().padStart(2, '0');
                    const ano = currentDate.getFullYear();
                    const dataFormatada = `${dia}/${mes}/${ano}`;

                    dates.push(dataFormatada);
                    currentDate.setDate(currentDate.getDate() + 1);
                }

                return dates;
            }

            function filterTable() {

                var filtros = {};

                $('.filtro-checkbox:checked, .filtro-select, .filtro-date').each(function () {
                    var coluna = $(this).data('column');

                    if($(this).attr('type') == 'date'){

                        var datas = getDatesBetween(startDate, endDate);
                        for(var valor of datas){
                            if (!filtros[coluna]) {
                                filtros[coluna] = [];
                            }

                            filtros[coluna].push(valor);
                        }

                    }else{

                        var valor = $(this).val();

                        if (!filtros[coluna]) {
                        filtros[coluna] = [];
                        }

                        filtros[coluna].push(valor);
                    }


                });

                // Atualize o filtro na tabela
                tabelaGuias.columns().search('').draw();

                // Aplica os filtros
                $.each(filtros, function(coluna, valores) {
                    tabelaGuias.column(coluna).search(valores.join('|'), true, false).draw();
                });
            }

            $('.filtro-checkbox, .filtro-select, .filtro-date').on('change', function () {
                filterTable();
            });
        });
    </script>

@endsection


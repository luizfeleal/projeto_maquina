@extends('layouts.app')
@section('title', 'Minhas Máquinas')
@section('content')

        <div id="guias" class="maquina w-100 div-center-column"
                style=" padding-top: 99px; padding-bottom: 100px;">

                <h1 style="padding-top: 80px; text-align: center; padding-bottom: 50px;">Minhas Máquinas</h1>

            <div class="container section container-platform div-center-column"
                style="margin-top: 15px; height: 100%;">
                
                <div class="tabela_responsiva">
                    <table id="tabela_maquinas" class="display nowrap table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Máquina</th>
                                <th>Status</th>
                                <th>Última transação</th>
                                <th>Fonte</th>
                                <th>Data e Hora</th>
                                <th>Detalhar</th>
                                <th>Excluir</th>
    
    
                            </tr>
                        </thead>
                        <tbody>
    
                            @foreach($resultado as $extrato)
                                <tr>
                                    <!--<td>{{$extrato['local_nome']}}</td>-->
                                    <td>{{$extrato['maquina_nome']}}</td>
                                    @if($extrato['maquina_status'] == 0) 
                                            <td><i class="fa-solid fa-circle text-danger" ></i></td>
                                        @else
                                            <td><i class="fa-solid fa-circle text-success" ></i></td>
                                        @endif
                                    @if($extrato['extrato_operacao'] == "C" || $extrato['extrato_operacao'] == "N/A")
                                        <td>+ R$ {{number_format($extrato['extrato_operacao_valor'], 2, ',', '.')}}</td>
                                    @else
                                        <td>- R$ {{number_format($extrato['extrato_operacao_valor'], 2, ',', '.')}}</td>
                                    @endif
    
                                    <td>{{$extrato['extrato_operacao_tipo']}}</td>
                                    <td>{{date('d/m/Y H:i:s', strtotime($extrato['data_criacao']));}}</td>
                                    <td style="text-align: center;"><a href="/maquinas/visualizar?id={{$extrato['id_maquina']}}"><i class="fa-solid fa-eye"></i></a></td>
                                    <td style="text-align: center;"><a href="#" style="color: red !important;" data-bs-toggle="modal" data-bs-target="#ModalCenterExcluir" onclick="setIdMaquinaExcluir({{$extrato['id_maquina']}}, '#id_maquina_input_excluir')"><i class="fa-solid fa-trash"></i></a></td>
                                </tr>
                            @endforeach
    
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Máquina</th>
                                <th>Status</th>
                                <th>Última transação</th>
                                <th>Fonte</th>
                                <th>Data e Hora</th>
                                <th>Detalhar</th>
                                <th>Excluir</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>


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
                "scrollX": true,
                "columns": [
                    null,
                    null,
                    null,
                    null,
                    { "type": "datetime-ddmmyyyy" },
                    null,
                    null
                ],
            });

            /*$('#input_filtro_cliente').select2({
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
            });*/


            

            
            /*var dadosTabela = tabelaGuias.rows().data().toArray();
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
            });*/
        });
    </script>

@endsection


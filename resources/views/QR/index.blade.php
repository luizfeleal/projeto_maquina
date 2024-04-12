@extends('layouts.app')
@section('title', 'QR Code')
@section('content')

        <div id="guias" class="guias w-100 div-center-column"
                style=" padding-top: 99px; padding-bottom: 100px;">

                <h1 style="padding-top: 80px; text-align: center; padding-bottom: 50px;">QR Code</h1>

            <div class="container section container-platform div-center-column"
                style="margin-top: 15px; height: 100%;">
                
                
                    <div class="row div-center-row" style=" margin-bottom: 30px; width: 100%;">
                        <div class="form-check form-check-inline col-md-3">
                            <label for="input_data_inicio_filtro">Local:</label>
                            <select id="input_filtro_local" class="form-control js-example-basic-multiple js-states" data-column="0">
                                <option value=""></option>
                                @foreach($locais as $local)
                                    <option value="{{$local['id_local']}}">{{$local['local_nome']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-check form-check-inline col-md-3">
                            <label for="input_data_inicio_filtro">Cliente:</label>
                            <select id="input_filtro_cliente" class="form-control js-example-basic-multiple js-states" data-column="0">
                            <option value=""></option>
                                @foreach($clientes as $cliente)
                                    <option value="{{$cliente['id_cliente']}}">{{$cliente['cliente_nome']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-check form-check-inline col-md-3">
                            <label for="input_data_inicio_filtro ">Máquina:</label>
                            <select id="input_filtro_maquina" class="form-control js-example-basic-multiple js-states" data-column="0">
                            <option value=""></option>

                                @foreach($maquinas as $maquina)
                                    <option value="{{$maquina['id_maquina']}}">{{$maquina['maquina_nome']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                

                    <div><h1>
                        Aqui vai o qr code
                    </h1></div>

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
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
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


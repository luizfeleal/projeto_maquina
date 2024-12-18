@extends('layouts.Clientes.app')
@section('title', 'QR Code')
@section('content')

        <div id="guias" class="qr w-100 div-center-column"
                style=" padding-top: 99px; padding-bottom: 100px;">

                <h1 style="padding-top: 80px; text-align: center; padding-bottom: 50px;">QR Code</h1>

            <div class="container section container-platform div-center-column"
                style="margin-top: 15px; height: 100%;">
                
                <form action="{{ route('cliente-qr') }}" class="needs-validation form-center" method="GET" id="novo-qr-form" class="w-100" novalidate>
                    @csrf

                    <div class="row div-center-row" style=" margin-bottom: 30px; width: 100%;">
                        <div class="form-check form-check-inline col-md-3">
                            <label for="input_filtro_local">Local*:</label>
                            <select id="input_filtro_local" name="id_local" class="input_filtro_local form-control js-example-basic-multiple js-states" data-column="0" required>
                                <option value="">Selecione...</option>
                                @foreach($locais as $local)
                                    <option value="{{$local['id_local']}}">{{$local['local_nome']}}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                    <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                </div>
                        </div>
                        <div class="form-check form-check-inline col-md-3">
                            <label for="input_filtro_cliente">Cliente:</label>
                            <select id="input_filtro_cliente" class="input_filtro_cliente form-control js-example-basic-multiple js-states" data-column="0">
                            <option value="">Selecione...</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{$cliente['id_cliente']}}">{{$cliente['cliente_nome']}}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                    <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                </div>
                        </div>
                        <div class="form-check form-check-inline col-md-3">
                            <label for="input_filtro_maquina ">Máquina*:</label>
                            <select id="input_filtro_maquina" name="id_maquina" class="input_filtro_maquina form-control js-example-basic-multiple js-states" data-column="0" required>
                            <option value="">Selecione...</option>

                                @foreach($maquinas as $maquina)
                                    <option value="{{$maquina['id_maquina']}}">{{$maquina['maquina_nome']}}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                    <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                                </div>
                        </div>
                    </div>
                

                    <div class="div-center-row" style="padding-top: 40px; "><button type="submit" class="btn btn-primary">Buscar</button></div>

                </form>

                @if(session('imageQr'))

                    <div id="imagem_qr" class="div-imagem-qr-index">
                        <div class="container">
                            
                            <div class="row w-100 d-flex">
                                <div class="col-md-10" style="justify-content: center; align-items: center; text-align: center; padding-left: 80px;">
                                    <h5>QR Gerado</h5>
                                </div>

                                <div class="col-md-1">
                                    <a href="#" style="color: red !important;" data-bs-toggle="modal" data-bs-target="#ModalCenterExcluir" onclick="setIdQrExcluir({{session('dadosQr')['id_qr']}}, '#id_qr_input_excluir')"><i class="fa-solid fa-trash"></i></a>
                                </div>
                            </div>
                            <div style="width: 60%;">
                                <p><i class="fa-solid fa-location-dot"></i> {{session('local')['local_nome']}}</p>
                                <p><i class="fa-solid fa-desktop"></i> {{session('maquina')['maquina_nome']}}</p>
                            </div>
                            <img src="{{session('imageQr')}}" width="350" alt="qr_code" style="padding-bottom: 10px;">
    
    
                            <a href="{{route('cliente-qr-download',  ['qr_base64_image'=> session('imageQr')])}}" class="btn btn-primary"><i class="fa-solid fa-download"></i> Download</a>
                        </div>
                    </div>
                
                @endif

                <div class="modal fade" id="ModalCenterExcluir" tabindex="-1" aria-labelledby="ModalCenterExcluir" aria-modal="true" role="dialog">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="ModalCenterTitleExcluir">Excluir QR Code</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Deseja excluir esse QR Code?</p>
                                </div>
                                <div class="modal-footer">
                                    <form action="{{route('cliente-qr-excluir')}}" method="post" id="excluir-qr" class="w-100 " >
                                    @csrf
                                        <input type="hidden" name="id_qr" id="id_qr_input_excluir" value="" >
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
            $('.input_filtro_cliente').select2({
                theme: 'bootstrap-5',
                width: "100%"
            });
            $('.input_filtro_local').select2({
                theme: 'bootstrap-5',
                width: "100%"
            });
            $('.input_filtro_maquina').select2({
                theme: 'bootstrap-5',
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


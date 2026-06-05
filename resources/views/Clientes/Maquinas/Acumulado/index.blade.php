@extends('layouts.Clientes.app')
@section('title', 'Minhas Máquinas -> Acumulado')
@section('content')

        <div id="guias" class="maquina w-100 div-center-column"
                style=" padding-top: 99px; padding-bottom: 100px;">


            <div class="container section container-platform div-center-column"
                style="margin-top: 15px; height: 100%;">
                

                <table id="tabela-local" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Local</th>
                            <th>Máquina</th>
                            <th>Total máquina</th>
                            <th>Total PIX</th>
                            <th>Total cartão</th>
                            <th>Total físico</th>


                        </tr>
                    </thead>
                    <tbody>

                    
                    

                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Local</th>
                            <th>Máquina</th>
                            <th>Total máquina</th>
                            <th>Total PIX</th>
                            <th>Total cartão</th>
                            <th>Total físico</th>
                        </tr>
                    </tfoot>
                </table>


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


            async function fetchToken() {
                try {
                    let response = await fetch('https://www.swiftpaysolucoes.com/api/getToken');
                    let data = await response.json();
                    return data.token;
                } catch (error) {
                    console.error('Erro ao obter o token:', error);
                    return null;
                }
            }

            fetchToken().then(token => {
                if (token) {
                    var tabelaGuias = $('#tabela-local').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: 'https://services.swiftpaysolucoes.com/api/totalTransacaoMaquinaAcumuladoCliente', // URL da sua API
                            type: 'POST', // Tipo de requisição
                            dataSrc: 'data', // Propriedade da resposta que contém os dados
                            headers: {
                                'Authorization': 'Bearer ' + token, // Adicione seu token de autenticação
                            },
                            data: function (d) {
                                d.id_cliente = {!!json_decode($id_cliente)!!}
                                d.page = (d.start / d.length) + 1; // DataTables usa índice baseado em 0
                                d.per_page = d.length; // Define o número de registros por página
                            }
                        },
                        language: {
                            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json" // Idioma
                        },
                        columns: [
                            { data: 'local_nome', title: 'Local' },
        { data: 'maquina_nome', title: 'Máquina' },
        { 
            data: 'total_maquina', 
            title: 'Total máquina', 
            render: function(data) { 
                if (data === null || data === undefined) {
                    return 'R$ 0,00';
                }
                return 'R$ ' + new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(data);
            } 
        },
        { 
            data: 'total_pix', 
            title: 'Total PIX', 
            render: function(data) { 
                if (data === null || data === undefined) {
                    return 'R$ 0,00';
                }
                return 'R$ ' + new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(data);
            } 
        },
        { 
            data: 'total_cartao', 
            title: 'Total cartão', 
            render: function(data) { 
                if (data === null || data === undefined) {
                    return 'R$ 0,00';
                }
                return 'R$ ' + new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(data);
            } 
        },
        { 
            data: 'total_dinheiro', 
            title: 'Total físico', 
            render: function(data) { 
                if (data === null || data === undefined) {
                    return 'R$ 0,00';
                }
                return 'R$ ' + new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(data);
            } 
        }
                        ],
                        pageLength: 10,
                        paging: true,
                        lengthMenu: [10, 25, 50, 100]
                    });
                }
            });

            
            
        });
    </script>

@endsection


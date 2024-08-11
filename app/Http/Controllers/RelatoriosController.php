<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LocaisService;
use App\Services\MaquinasService;
use App\Services\ExtratoMaquinaService;
use App\Services\ClientesService;
use App\Services\LogsService;
use App\Services\QrCodeService;
use App\Services\AuthService;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class RelatoriosController extends Controller
{
    public function view(Request $request){

        $locais = LocaisService::coletar();
        $clientes = ClientesService::coletar();
        $maquinas = MaquinasService::coletar();
        return view('Admin.Relatorios.index', compact('locais', 'maquinas', 'clientes'));

    }

    public function exibirRelatorio(Request $request){
        
        

        $nomeRelatorio = $request['tipo'];
        if($nomeRelatorio == "maquinasOnOff"){
            $locais = LocaisService::coletar();
            $clientes = ClientesService::coletar();
            $maquinas = MaquinasService::coletar();
            if($request['cliente']){
                $locais = LocaisService::coletarComFiltro(['id_cliente' => $request['cliente']], 'in');
                $maquinas = array_filter($maquinas, function($maquina) use($locais){
                    foreach ($locais as $local) {
                        if ($maquina['id_local'] == $local->id_local) {
                            return true;
                        }
                    }
                    return false;
                });
            }

            $locais = array_reduce($locais, function($result, $local) {
                $result[$local['id_local']] = $local;
                return $result;
            }, []);

            

            $maquinasOnline = array_filter($maquinas, function($maquina) {
                return $maquina['maquina_status'] == 1;
            });

            $maquinasOffline = array_filter($maquinas, function($maquina) {
                return $maquina['maquina_status'] == 0;
            });

            $resultArray = [];

            foreach($maquinas as $maquina){
                $resultArray[] =[
                    "maquina"=>$maquina['maquina_nome'],
                    "local"=>$locais[$maquina['id_local']]['local_nome'],
                    "status"=>$maquina['maquina_status'],
                    "ultimo_contato"=>$maquina['maquina_ultimo_contato']
                ];
            }

            return view('Admin.Relatorios.MaquinasOnOff.show', compact('maquinasOnline', 'maquinasOffline', 'locais', 'resultArray'));

            
        }

        if($nomeRelatorio == "totalTransacoes"){

            $cliente = $request->input('id_cliente');
            $maquina = $request->input('id_maquina');
            $local = $request->input('id_local');
            $tipoTransacao = $request->input('tipo_transacao');
            $data_inicio = $request->input('data_inicio');
            $data_fim = $request->input('data_fim');

            $locais = LocaisService::coletar();
            $maquinas = MaquinasService::coletar();

            if($local){

                $locais = array_filter($locais, function($item) use ($local) {
                    return in_array($item['id_local'], $local);
                });
            }

            if($maquina){
                
                $maquinas = array_filter($maquinas, function($item) use ($maquina) {
                    return in_array($item['id_maquina'], $maquina);
                });
            }

            if($cliente){
                $locais = array_filter($locais, function($item) use ($cliente) {
                    return in_array($item['id_cliente'], $cliente);
                });
            }

            // Extrair todos os id_local dos locais filtrados
            $idLocais = array_column($locais, 'id_local');

            $maquinas = array_filter($maquinas, function($item) use ($idLocais) {
                return in_array($item['id_local'], $idLocais);
            });

            $maquinasPorId = [];
            foreach ($maquinas as &$maquina) {
                $local = array_filter($locais, function($item) use($maquina){
                    return $item['id_local'] == $maquina['id_local'];
                });


                if(!empty($local) && isset($local[0])){
                    $local_nome = $local[0];
                }else{
                    $local_nome = '';
                }

                //dd($local[0]['local_nome']);
                $maquina['nome_local'] = $local_nome;
                $maquinasPorId[$maquina['id_maquina']] = $maquina;
            }

            $idMaquinas = array_column($maquinas, 'id_maquina');


            $extratos = ExtratoMaquinaService::coletar();
            $extratos = array_filter($extratos, function($item) use ($idMaquinas) {
                return in_array($item['id_maquina'], $idMaquinas);
            });

            if ($tipoTransacao) {
                $extratos = array_filter($extratos, function($item) use ($tipoTransacao) {
                    return $item['extrato_operacao_tipo'] == $tipoTransacao;
                    
                });
            }

            $resultadosFiltrados = [];
            $valor_total_pix = 0;
            $valor_total_cartao = 0;
            $valor_total_dinheiro = 0;
            $valor_total_estorno = 0;
            $valor_total = 0;

            
            $resultArray= [];
               foreach ($extratos as &$item) {
                    $item['maquina_nome'] = $maquinasPorId[$item['id_maquina']]['maquina_nome'];
                    $item['nome_local'] = $maquinasPorId[$item['id_maquina']]['nome_local'];
                    $dataItem = strtotime($item["data_criacao"]);

                    // Verificar se dataFim está vazio
                    if (empty($dataFim)) {
                        $dataFimTimestamp = time(); // Usar a data atual se dataFim estiver vazio
                    } else {
                        $dataFimTimestamp = strtotime($dataFim . " 23:59:59");
                    }

                    // Verificar se dataInicio está vazio
                    if (empty($dataInicio)) {
                        // Considerar todos os inícios até dataFim se dataInicio estiver vazio
                        $resultadosFiltrados[] = $item;
                    } else {
                        $dataInicioTimestamp = strtotime($dataInicio . " 00:00:00");

                        // Verificar se a data está dentro do intervalo
                        if ($dataItem >= $dataInicioTimestamp && $dataItem <= $dataFimTimestamp) {
                            // Adicionar o item à lista resultante
                            $resultadosFiltrados[] = $item;
                        }
                    }

                    if($item['extrato_operacao_tipo'] == "PIX"){
                        $valor_total_pix += $item['extrato_operacao_valor'];
                    }else if($item['extrato_operacao_tipo'] == "Cartão"){
                        $valor_total_cartao += $item['extrato_operacao_valor'];
                    }else if($item['extrato_operacao_tipo'] == "Dinheiro"){
                        $valor_total_dinheiro += $item['extrato_operacao_valor'];
                    }else if($item['extrato_operacao_tipo'] == "Estorno"){
                        $valor_total_estorno += $item['extrato_operacao_valor'];
                    }

                    $valor_total += $item['extrato_operacao_valor'];
                    
                    $resultArray[] =[
                        "local" => $maquinasPorId[$item['id_maquina']]['nome_local']['local_nome'],
                        "maquina" => $maquinasPorId[$item['id_maquina']]['maquina_nome'],
                        "tipo_transacao" => $item['extrato_operacao_tipo'],
                        "valor" => $item['extrato_operacao_valor'],
                        "data_e_hora" => date('d/m/Y H:i:s', strtotime($item['data_criacao']))
    
                    ];
                }

                

            return view('Admin.Relatorios.TotalTransacoes.show', compact('resultadosFiltrados', 'valor_total', 'valor_total_pix', 'valor_total_cartao', 'valor_total_dinheiro', 'valor_total_estorno', 'resultArray'));



        }

        if($nomeRelatorio == "taxasDesconto"){

            $cliente = $request->input('id_cliente');
            $maquina = $request->input('id_maquina');
            $local = $request->input('id_local');
            $tipoTransacao = "Taxa";
            $data_inicio = $request->input('data_inicio');
            $data_fim = $request->input('data_fim');

            $locais = LocaisService::coletar();
            $maquinas = MaquinasService::coletar();

            if($local){

                $locais = array_filter($locais, function($item) use ($local) {
                    return in_array($item['id_local'], $local);
                });
            }

            if($maquina){
                
                $maquinas = array_filter($maquinas, function($item) use ($maquina) {
                    return in_array($item['id_maquina'], $maquina);
                });
            }

            if($cliente){
                $locais = array_filter($locais, function($item) use ($cliente) {
                    return in_array($item['id_cliente'], $cliente);
                });
            }

            // Extrair todos os id_local dos locais filtrados
            $idLocais = array_column($locais, 'id_local');

            $maquinas = array_filter($maquinas, function($item) use ($idLocais) {
                return in_array($item['id_local'], $idLocais);
            });

            $maquinasPorId = [];
            foreach ($maquinas as &$maquina) {
                $local = array_filter($locais, function($item) use($maquina){
                    return $item['id_local'] == $maquina['id_local'];
                });


                if(!empty($local) && isset($local[0])){
                    $local_nome = $local[0];
                }else{
                    $local_nome = '';
                }

                //dd($local[0]['local_nome']);
                $maquina['nome_local'] = $local_nome;
                $maquinasPorId[$maquina['id_maquina']] = $maquina;
            }

            $idMaquinas = array_column($maquinas, 'id_maquina');


            $extratos = ExtratoMaquinaService::coletar();
            $extratos = array_filter($extratos, function($item) use ($idMaquinas) {
                return in_array($item['id_maquina'], $idMaquinas);
            });

            if ($tipoTransacao) {
                $extratos = array_filter($extratos, function($item) use ($tipoTransacao) {
                    return $item['extrato_operacao_tipo'] == $tipoTransacao;
                    
                });
            }

            $resultadosFiltrados = [];
            //$valor_total_pix = 0;
            //$valor_total_cartao = 0;
            //$valor_total_dinheiro = 0;
            //$valor_total_estorno = 0;
            $valor_total = 0;

            
            
               foreach ($extratos as &$item) {
                    $item['maquina_nome'] = $maquinasPorId[$item['id_maquina']]['maquina_nome'];
                    $item['nome_local'] = $maquinasPorId[$item['id_maquina']]['nome_local'];
                    $dataItem = strtotime($item["data_criacao"]);

                    // Verificar se dataFim está vazio
                    if (empty($dataFim)) {
                        $dataFimTimestamp = time(); // Usar a data atual se dataFim estiver vazio
                    } else {
                        $dataFimTimestamp = strtotime($dataFim . " 23:59:59");
                    }

                    // Verificar se dataInicio está vazio
                    if (empty($dataInicio)) {
                        // Considerar todos os inícios até dataFim se dataInicio estiver vazio
                        $resultadosFiltrados[] = $item;
                    } else {
                        $dataInicioTimestamp = strtotime($dataInicio . " 00:00:00");

                        // Verificar se a data está dentro do intervalo
                        if ($dataItem >= $dataInicioTimestamp && $dataItem <= $dataFimTimestamp) {
                            // Adicionar o item à lista resultante
                            $resultadosFiltrados[] = $item;
                        }
                    }

                    $resultArray[] = [
                        "local" => $maquinasPorId[$item['id_maquina']]['nome_local'],
                        "maquina" => $maquinasPorId[$item['id_maquina']]['maquina_nome'],
                        "tipo_transacao" => $item['extrato_operacao_tipo'],
                        "valor" => $item['extrato_operacao_valor'],
                        "data_e_hora" => date('d/m/Y H:i:s', strtotime($item['data_criacao']))
                    ];
                    /*if($item['extrato_operacao_tipo'] == "PIX"){
                        $valor_total_pix += $item['extrato_operacao_valor'];
                    }else if($item['extrato_operacao_tipo'] == "Cartão"){
                        $valor_total_cartao += $item['extrato_operacao_valor'];
                    }else if($item['extrato_operacao_tipo'] == "Dinheiro"){
                        $valor_total_dinheiro += $item['extrato_operacao_valor'];
                    }else if($item['extrato_operacao_tipo'] == "Estorno"){
                        $valor_total_estorno += $item['extrato_operacao_valor'];
                    }*/

                    $valor_total += $item['extrato_operacao_valor'];
                }

                

            return view('Admin.Relatorios.TaxasDesconto.show', compact('resultadosFiltrados','valor_total', 'resultArray'));



        }

        if($nomeRelatorio == "relatorioErros"){
            $maquina = $request->input('id_maquina');
            $local = $request->input('id_local');

            $relatorioDeErros = LogsService::coletarComFiltro(['status' => "erro"], 'where');


            
            if(isset($maquina)){
                $relatorioDeErros = array_filter($relatorioDeErros, function($item) use($maquina){
                    return $item['id_maquina'] = $maquina;
                });
            }

            if(isset($local)){
                $relatorioDeErros = array_filter($relatorioDeErros, function($item) use($local){
                    return $item['id_local'] = $local;
                });
            }

            $relatorioDeErros = array_filter($relatorioDeErros, function($item) use($local){
                return isset($item['id_maquina']);
            });

            $maquinas = collect(MaquinasService::coletar())->keyBy('id_maquina')->toArray();

            
            $resultadosFiltrados = [];

            foreach($relatorioDeErros as &$item){

                $item['id_placa'] = $maquinas[$item['id_maquina']]['id_placa'];
                $dataItem = strtotime($item["data_criacao"]);
                // Verificar se dataFim está vazio
                if (empty($dataFim)) {
                    $dataFimTimestamp = time(); // Usar a data atual se dataFim estiver vazio
                } else {
                    $dataFimTimestamp = strtotime($dataFim . " 23:59:59");
                }
    
                // Verificar se dataInicio está vazio
                if (empty($dataInicio)) {
                    // Considerar todos os inícios até dataFim se dataInicio estiver vazio
                    $resultadosFiltrados[] = $item;
                } else {
                    $dataInicioTimestamp = strtotime($dataInicio . " 00:00:00");
    
                    // Verificar se a data está dentro do intervalo
                    if ($dataItem >= $dataInicioTimestamp && $dataItem <= $dataFimTimestamp) {
                        // Adicionar o item à lista resultante
                        $resultadosFiltrados[] = $item;
                    }
                }
            }

            return view('Admin.Relatorios.RelatorioErros.show', compact('resultadosFiltrados'));


        }


        return view('QR.create', compact('locais', 'clientes', 'maquinas'));
    }

    public function downloadXlsxRelatorio(Request $request)
    {
        
        // Decodifica os dados JSON da requisição
        $data = json_decode($request->input('data'));


        $isTaxaDesconto = isset($request['tipo_csv']) && $request['tipo_csv'] == 'taxa_desconto';
        $isTotalTransacoes = isset($request['tipo_csv']) && $request['tipo_csv'] == 'total_transacao';
        // Definindo os tipos de XLSX

        // Criação do Spreadsheet e do cabeçalho
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cabecalho = array_keys((array) $data[0]);

        // Escrever o cabeçalho no arquivo
        $sheet->fromArray($cabecalho, NULL, 'A1');

        $totalValorFinal = 0;
        $rowNum = 2;

    
        // Escrever o conteúdo no arquivo
        foreach ($data as $item) {
            $itemArray = (array) $item;

            if ($isTaxaDesconto) {
                $totalValorFinal += $itemArray['extrato_operacao_valor'];
            }
            if ($isTotalTransacoes) {
                $totalValorFinal += $itemArray['valor'];
            }

            // Escrever a linha de dados no arquivo
            $sheet->fromArray($itemArray, NULL, 'A' . $rowNum);
            $rowNum++;
        }


        //Adicionar a linha de total se necessário
        if ($isTotalTransacoes || $isTaxaDesconto ) {
            $totalRow = ['Total: R$ ' . number_format($totalValorFinal, 2, ',', '.')];
            $sheet->fromArray($totalRow, NULL, 'A' . $rowNum);
        }

        // Definindo o caminho do arquivo
        $fileName = 'export_' . md5(uniqid()) . '.xlsx';
        $filePath = storage_path('app/xlsx/' . $fileName);

        // Certifique-se de que o diretório de destino exista
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        // Criar o writer e salvar o arquivo
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);


        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        // Retornar o arquivo como resposta para download
        return Response::download($filePath, $fileName, $headers)->deleteFileAfterSend(true);
    }
}

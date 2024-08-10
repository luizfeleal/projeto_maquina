<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LocaisService;
use App\Services\MaquinasService;
use App\Services\ExtratoMaquinaService;
use App\Services\ClientesService;
use App\Services\QrCodeService;
use App\Services\AuthService;
use Illuminate\Support\Facades\Response;


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

            return view('Admin.Relatorios.MaquinasOnOff.show', compact('maquinasOnline', 'maquinasOffline', 'locais'));

            
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
                }

                

            return view('Admin.Relatorios.TotalTransacoes.show', compact('resultadosFiltrados', 'valor_total', 'valor_total_pix', 'valor_total_cartao', 'valor_total_dinheiro', 'valor_total_estorno'));



        }


        return view('QR.create', compact('locais', 'clientes', 'maquinas'));
    }

    public function downloadXlsxRelatorio(Request $request)
    {
        // Obtém a imagem base64 do request de forma segura
        $base64_image = $request->input('qr_base64_image');

        // Verifica e remove o prefixo de dados se necessário
        if (strpos($base64_image, 'data:image') === 0) {
            $base64_image = preg_replace('#^data:image/\w+;base64,#i', '', $base64_image);
        }

        // Decodifica a imagem base64
        $decoded_image = base64_decode($base64_image);

        // Verifica se a decodificação foi bem-sucedida
        if ($decoded_image === false) {
            return response()->json(['error' => 'Invalid base64 string'], 400);
        }

        // Define o nome do arquivo de saída
        $output_file = 'qr_code.png';

        // Abre o arquivo para escrita
        if (false === ($ifp = fopen($output_file, 'wb'))) {
            return response()->json(['error' => 'Failed to open file for writing'], 500);
        }

        // Escreve os dados decodificados no arquivo
        if (false === fwrite($ifp, $decoded_image)) {
            fclose($ifp);
            return response()->json(['error' => 'Failed to write to file'], 500);
        }

        // Fecha o arquivo
        fclose($ifp);

        // Define o cabeçalho para download do arquivo
        return response()->download($output_file, 'qr_code.png')->deleteFileAfterSend(true);
    }
}

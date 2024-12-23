<?php

namespace App\Http\Controllers\Clientes;

use Illuminate\Http\Request;
use App\Services\LocaisService;
use App\Services\MaquinasService;
use App\Services\ExtratoMaquinaService;
use App\Services\ClientesService;
use App\Services\ClienteLocalService;
use App\Services\LogsService;
use App\Services\QrCodeService;
use App\Services\AuthService;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Http\Controllers\Controller;


class RelatoriosController extends Controller
{
    public function view(Request $request)
    {

        $id_cliente = session()->get('id_cliente');
        $localCliente = ClienteLocalService::coletar();
        $locais = LocaisService::coletar();
        $clientes = ClientesService::coletar();
        $maquinas = MaquinasService::coletar();

        $locaisPermitidos = array_filter($localCliente, function ($local) use ($id_cliente) {
            return $local['id_cliente'] == $id_cliente;
        });

        $idsLocaisPermitidos = array_column($locaisPermitidos, 'id_local');

        $maquinas = array_filter($maquinas, function ($maquina) use ($idsLocaisPermitidos) {
            return in_array($maquina['id_local'], $idsLocaisPermitidos);
        });

        $locais = array_filter($locais, function ($item) use ($idsLocaisPermitidos) {
            return in_array($item['id_local'], $idsLocaisPermitidos);
        });

        return view('Clientes.Relatorios.index', compact('locais', 'maquinas'));
    }

    public function exibirRelatorio(Request $request)
    {



        $nomeRelatorio = $request['tipo'];
        if ($nomeRelatorio == "maquinasOnOff") {
            $id_cliente = session()->get('id_cliente');
            $locais = LocaisService::coletar();
            $clientes = ClientesService::coletar();
            $localCliente = ClienteLocalService::coletar();
            $maquinas = MaquinasService::coletar();

            $locaisPermitidos = array_filter($localCliente, function ($local) use ($id_cliente) {
                return $local['id_cliente'] == $id_cliente;
            });

            $idsLocaisPermitidos = array_column($locaisPermitidos, 'id_local');

            $maquinas = array_filter($maquinas, function ($maquina) use ($idsLocaisPermitidos) {
                return in_array($maquina['id_local'], $idsLocaisPermitidos);
            });

            $locais = array_filter($locais, function ($item) use ($idsLocaisPermitidos) {
                return in_array($item['id_local'], $idsLocaisPermitidos);
            });


            $locais = array_reduce($locais, function ($result, $local) {
                $result[$local['id_local']] = $local;
                return $result;
            }, []);



            $maquinasOnline = array_filter($maquinas, function ($maquina) {
                return $maquina['maquina_status'] == 1;
            });

            $maquinasOffline = array_filter($maquinas, function ($maquina) {
                return $maquina['maquina_status'] == 0;
            });

            $resultArray = [];

            foreach ($maquinas as $maquina) {
                $resultArray[] = [
                    "maquina" => $maquina['maquina_nome'],
                    "local" => $locais[$maquina['id_local']]['local_nome'],
                    "status" => $maquina['maquina_status'],
                    "ultimo_contato" => $maquina['maquina_ultimo_contato']
                ];
            }

            return view('Clientes.Relatorios.MaquinasOnOff.show', compact('maquinasOnline', 'maquinasOffline', 'locais', 'resultArray'));
        }

        if ($nomeRelatorio == "totalTransacoes") {

            $id_cliente = session()->get('id_cliente');
            $resultado = ExtratoMaquinaService::coletarRelatorioTotalTransacoes($request->all(), $id_cliente);
            $total = ExtratoMaquinaService::coletarRelatorioTotalTransacoesTotal($request->all(), $id_cliente);

            // Calcular totais



            $totalTransacoes = 0;
            $estorno = 0;

            foreach ($total as $item) {
                if ($item['tipo'] != "Estorno") {
                    $totalTransacoes += $item['total'];
                } else {
                    $estorno += $item['total'];
                }
            }

            $totalTransacoes = $totalTransacoes - $estorno;

            $bodyReq = $request->all();
            if ($request->ajax()) {
                return response()->json($resultado);
            }
            $id_maquina = $request['id_maquina'];
            $id_cliente = $request['id_cliente'];
            $tipo_transacao = $request['tipo_transacao'];
            $data_extrato_inicio = $request['data_extrato_inicio'];
            $data_extrato_fim = $request['data_extrato_fim'];
            return view('Clientes.Relatorios.TotalTransacoes.show', compact('resultado', 'total', 'totalTransacoes', 'bodyReq', 'id_maquina', 'id_cliente', 'tipo_transacao','data_extrato_inicio', 'data_extrato_fim'));
        }

        if ($nomeRelatorio == "taxasDesconto") {

            $resultadosFiltrados = ExtratoMaquinaService::coletarRelatorioTotalTransacoesTaxa($request->all())['data'];
            $resultArray = $resultadosFiltrados;

            $valor_total = 0;

            foreach ($resultadosFiltrados as $resultado) {
                $valor_total += $resultado['extrato_operacao_valor'];
            }
            return view('Clientes.Relatorios.TaxasDesconto.show', compact('resultadosFiltrados', 'resultArray', 'valor_total'));
        }

        if ($nomeRelatorio == "relatorioErros") {
            $maquina = $request->input('id_maquina');
            $local = $request->input('id_local');

            $relatorioDeErros = LogsService::coletar();

            $relatorioDeErros = array_filter($relatorioDeErros, function ($item) {
                return $item['status'] == "erro";
            });



            if (isset($maquina)) {
                $relatorioDeErros = array_filter($relatorioDeErros, function ($item) use ($maquina) {
                    return $item['id_maquina'] == $maquina;
                });
            }

            if (isset($local)) {
                $relatorioDeErros = array_filter($relatorioDeErros, function ($item) use ($local) {
                    return $item['id_local'] == $local;
                });
            }

            $relatorioDeErros = array_filter($relatorioDeErros, function ($item) use ($local) {
                return isset($item['id_maquina']);
            });

            $maquinas = collect(MaquinasService::coletar())->keyBy('id_maquina')->toArray();


            $resultadosFiltrados = [];

            foreach ($relatorioDeErros as &$item) {

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

            return view('Clientes.Relatorios.RelatorioErros.show', compact('resultadosFiltrados'));
        }


        return view('QR.create', compact('locais', 'clientes', 'maquinas'));
    }

    public function downloadXlsxRelatorio(Request $request)
    {

        // Decodifica os dados JSON da requisição
        $data = json_decode($request->input('data'));
        $data = (array) $data;
        $isTaxaDesconto = isset($request['tipo_csv']) && $request['tipo_csv'] == 'taxa_desconto';
        $isTotalTransacoes = isset($request['tipo_csv']) && $request['tipo_csv'] == 'total_transacao';
        if ($isTotalTransacoes) {
            $data = ExtratoMaquinaService::coletarRelatorioTotalTransacoes($data)['data'];
        }


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
                $totalValorFinal += $itemArray['extrato_operacao_valor'];
            }

            // Escrever a linha de dados no arquivo
            $sheet->fromArray($itemArray, NULL, 'A' . $rowNum);
            $rowNum++;
        }


        //Adicionar a linha de total se necessário
        if ($isTotalTransacoes || $isTaxaDesconto) {
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

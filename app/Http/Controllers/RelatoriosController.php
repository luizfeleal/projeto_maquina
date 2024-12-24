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
    public function view(Request $request)
    {

        $locais = LocaisService::coletar();
        $clientes = ClientesService::coletar();
        $maquinas = MaquinasService::coletar();
        return view('Admin.Relatorios.index', compact('locais', 'maquinas', 'clientes'));
    }

    public function exibirRelatorio(Request $request)
    {



        $nomeRelatorio = $request['tipo'];
        if ($nomeRelatorio == "maquinasOnOff") {
            $locais = LocaisService::coletar();
            $clientes = ClientesService::coletar();
            $maquinas = MaquinasService::coletar();
            if ($request['cliente']) {
                $locais = LocaisService::coletarComFiltro(['id_cliente' => $request['cliente']], 'in');
                $maquinas = array_filter($maquinas, function ($maquina) use ($locais) {
                    foreach ($locais as $local) {
                        if ($maquina['id_local'] == $local->id_local) {
                            return true;
                        }
                    }
                    return false;
                });
            }

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

            return view('Admin.Relatorios.MaquinasOnOff.show', compact('maquinasOnline', 'maquinasOffline', 'locais', 'resultArray'));
        }

        if ($nomeRelatorio == "totalTransacoes") {

            $resultado = ExtratoMaquinaService::coletarRelatorioTotalTransacoes($request->all());

            $total = ExtratoMaquinaService::coletarRelatorioTotalTransacoesTotal($request->all());

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
            return view('Admin.Relatorios.TotalTransacoes.show', compact('resultado', 'total', 'totalTransacoes', 'bodyReq', 'id_maquina', 'id_cliente', 'tipo_transacao','data_extrato_inicio', 'data_extrato_fim'));
        }

        if ($nomeRelatorio == "taxasDesconto") {

            $resultadosFiltrados = ExtratoMaquinaService::coletarRelatorioTotalTransacoesTaxa($request->all())['data'];
            $resultArray = $resultadosFiltrados;

            $valor_total = 0;

            foreach ($resultadosFiltrados as $resultado) {
                $valor_total += $resultado['extrato_operacao_valor'];
            }
            return view('Admin.Relatorios.TaxasDesconto.show', compact('resultadosFiltrados', 'resultArray', 'valor_total'));
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

            $maquinas = collect(MaquinasService::coletarComLixo())->keyBy('id_maquina')->toArray();


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

            $resultadosFiltrados = collect($resultadosFiltrados) ->sortByDesc('data_criacao')->values()->toArray();

            return view('Admin.Relatorios.RelatorioErros.show', compact('resultadosFiltrados'));
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
            $data = ExtratoMaquinaService::coletarRelatorioTotalTransacoes($data);
            return $data;
        }

        // Criação do Spreadsheet e do cabeçalho
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Transformar os nomes das colunas em maiúsculas
        $cabecalho = array_map('strtoupper', array_keys((array) $data[0]));

        // Escrever o cabeçalho no arquivo
        $sheet->fromArray($cabecalho, NULL, 'A1');

        // Aumentar a fonte do cabeçalho para 12
        $headerStyle = $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1');
        $headerStyle->getFont()->setBold(true)->setSize(12);

        $totalValorFinal = 0;
        $rowNum = 2;

        // Escrever o conteúdo no arquivo
        foreach ($data as &$item) {
            $itemArray = (array) $item;

            if ($isTaxaDesconto) {
                $totalValorFinal += $itemArray['extrato_operacao_valor'];
            }
            if ($isTotalTransacoes) {
                unset($itemArray['extrato_operacao']);
                $totalValorFinal += $itemArray['extrato_operacao_valor'];
            }

            // Escrever a linha de dados no arquivo
            $sheet->fromArray($itemArray, NULL, 'A' . $rowNum);

            // Aumentar a fonte para 12 em cada linha de dados
            $rowStyle = $sheet->getStyle('A' . $rowNum . ':' . $sheet->getHighestColumn() . $rowNum);
            $rowStyle->getFont()->setSize(12);

            // Formatar a coluna C como moeda brasileira
            $cellValue = $sheet->getCell('C' . $rowNum)->getValue();
            if (is_numeric($cellValue)) {
                $sheet->getStyle('C' . $rowNum)
                    ->getNumberFormat()
                    ->setFormatCode('"R$" #,##0.00');
            }

            $rowNum++;
        }

        // Adicionar a linha de total se necessário
        if ($isTotalTransacoes) {
            // Variáveis para os totais
            $totalDiaAtual = 0;
            $totalMesAtual = 0;
            $totalMesAnterior = 0;
        
            // Datas atuais
            $hoje = date('Y-m-d');
            $mesAtual = date('Y-m');
            $mesAnterior = date('Y-m', strtotime('-1 month'));
        
            foreach ($data as $item) {
                $itemArray = (array) $item;
        
                // Converter a data do item para o formato padrão (YYYY-MM-DD)
                $dataItem = date('Y-m-d', strtotime($itemArray['data_criacao']));
        
                // Somar os valores com base no período
                if ($dataItem == $hoje) {
                    $totalDiaAtual += $itemArray['extrato_operacao_valor'];
                }
                if (strpos($dataItem, $mesAtual) === 0) {
                    $totalMesAtual += $itemArray['extrato_operacao_valor'];
                }
                if (strpos($dataItem, $mesAnterior) === 0) {
                    $totalMesAnterior += $itemArray['extrato_operacao_valor'];
                }
            }
        
            // Adicionar os totais ao final do relatório
            $totais = [
                ['Período', 'Total'],
                ['Dia Atual', 'R$ ' . number_format($totalDiaAtual, 2, ',', '.')],
                ['Mês Atual', 'R$ ' . number_format($totalMesAtual, 2, ',', '.')],
                ['Mês Anterior', 'R$ ' . number_format($totalMesAnterior, 2, ',', '.')],
                ['Total Geral', 'R$ ' . number_format($totalValorFinal, 2, ',', '.')]
            ];
        
            // Escrever os totais no arquivo
            foreach ($totais as $linha) {
                $sheet->fromArray($linha, NULL, 'A' . $rowNum);
                $rowStyle = $sheet->getStyle('A' . $rowNum . ':' . $sheet->getHighestColumn() . $rowNum);
                $rowStyle->getFont()->setBold(true)->setSize(12)->getColor()->setRGB('FF0000');
                $rowNum++;
            }
        } elseif ($isTaxaDesconto) {
            // Adicionar apenas o total geral se for taxa de desconto
            $totalRow = ['Total: R$ ' . number_format($totalValorFinal, 2, ',', '.')];
            $sheet->fromArray($totalRow, NULL, 'A' . $rowNum);
        
            // Estilizar a célula com o total
            $style = $sheet->getStyle('A' . $rowNum);
            $style->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('FF0000'); // Fonte vermelha e maior
            $style->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('FFC7CE'); // Fundo vermelho claro
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

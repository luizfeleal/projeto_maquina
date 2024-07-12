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
        return view('Relatorios.index', compact('locais', 'maquinas', 'clientes'));

    }

    public function exibirRelatorio(Request $request){
        $locais = LocaisService::coletar();
        $clientes = ClientesService::coletar();
        

        $nomeRelatorio = $request['tipo'];
        if($nomeRelatorio == "maquinasOnOff"){

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

            return view('Relatorios.MaquinasOnOff.show', compact('maquinasOnline', 'maquinasOffline', 'locais'));

            
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

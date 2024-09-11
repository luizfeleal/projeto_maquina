<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LocaisService;
use App\Services\MaquinasService;
use App\Services\ExtratoMaquinaService;
use App\Services\ClientesService;
use App\Services\ClienteLocalService;
use App\Services\QrCodeService;
use App\Services\CredApiPixService;
use App\Services\AuthService;
use Illuminate\Support\Facades\Response;


class QrCodeController extends Controller
{
    public function coletarQr(Request $request){

        if($request->has('id_local') && $request->has('id_maquina')){

            $qrCode = QrCodeService::coletarComFiltro(['id_local' => $request['id_local'], 'id_maquina' => $request['id_maquina']], 'where');
            $maquina = MaquinasService::coletar($request['id_maquina']);
            $local = LocaisService::coletar($request['id_local']);

            if(empty($qrCode)){
                return back()->with('error', 'Nenhum QR Code encontrado para os dados fornecidos.');
            }

            
            return back()->with(['imageQr'=>$qrCode[0]['qr_image'], 'dadosQr' => $qrCode[0], 'maquina' => $maquina, 'local' => $local]);
        }else{
            //return back()->with('error', 'Máquina não encontrada');
            $locais = LocaisService::coletar();
            $maquinas = MaquinasService::coletar();
            $clientes = ClientesService::coletar();
            return view('Admin.QR.index', compact('locais', 'maquinas', 'clientes'));
        }

        

    }

    public function criarQr(Request $request){
        $locais = LocaisService::coletar();
        $clientes = ClientesService::coletar();
        $maquinas = MaquinasService::coletar();

        $qrCode = QrCodeService::coletar();

        // Passo 1: Extrair todos os IDs de máquinas presentes nos QR codes
        $qrCodeIdsMaquinas = array_column($qrCode, 'id_maquina');
        $qrCodeIdsLocais = array_column($qrCode, 'id_local');

        // Passo 2: Filtrar as máquinas que não têm o 'id_maquina' presente no array de QR codes
        $maquinas = array_filter($maquinas, function($maquina) use ($qrCodeIdsMaquinas) {
            return !in_array($maquina['id_maquina'], $qrCodeIdsMaquinas);
        });

        // Passo 2: Filtrar as máquinas que não têm o 'id_maquina' presente no array de QR codes
        $locais = array_filter($locais, function($local) use ($qrCodeIdsLocais) {
            return !in_array($local['id_local'], $qrCodeIdsLocais);
        });


        $qrCodeFiltrado = array_filter($qrCode, function($qrCode){

        });

        return view('Admin.QR.create', compact('locais', 'clientes', 'maquinas'));
    }

    public function registrarQr(Request $request){
        
        try{

            if($request['select_local'] == null || $request['select_maquina'] == null){
                return back()->with('error', 'Todos os campos obrigatórios devem ser preenchidos para a criação do QR Code.');
            }
            $qrExistente = QrCodeService::coletarComFiltro(['id_local' => $request['select_local'], 'id_maquina' => $request['select_maquina']], 'where');

            if(!empty($qrExistente)){
                return back()->with('error', 'Não foi possível gerar um QR para os dados passados, pois já existe um QR Code para o local e máquina especificados.');
            }

            $id_local = $request['select_local'];
            $cliente_local = ClienteLocalService::coletar();

            $cliente_local = array_filter($cliente_local, function($item) use($id_local){
                return $item['id_local'] == $id_local && $item['cliente_local_principal'] == 1;
            });

            if(empty($cliente_local)){
                return back()->with('error', 'Não foi possível gerar um QR para os dados passados, pois não foi encontrado um cliente para o local especificado.');

            }
            $cliente_local = array_values($cliente_local);

            $id_cliente = $cliente_local[0]['id_cliente'];

            $credenciais = CredApiPixService::coletar();

            $credencial = array_filter($credenciais, function($item) use($id_cliente){
                return $item['id_cliente'] == $id_cliente && $item['tipo_cred'] == "efi";
            });

            if(empty($credencial)){
                return back()->with('error', 'Não foi possível gerar um QR para os dados passados, pois não foi encontrado uma credencial para o cliente informado');

            }
            $id_usuario_logado = session()->get('id_usuario');
            $request['id_usuario'] = $id_usuario_logado;
            $request['id_cliente'] = $cliente_local[0]['id_cliente'];

            $qr = QrCodeService::criar($request);
            return $qr;

            if($qr['message'] == "Qr Code cadastrado com sucesso!"){
                return back()-> with(['success' => $qr['message'], 'qr_base64_imagem' => $qr['response']['qr_image']]);
            }else{
                return back()-> with('error', $qr['message']);
            }
        }catch(Throwable $e){
            return back()->with('error', 'Houve um erro inesperado ao tentar registrar o QR Code.');
        }

    }

    public function downloadQr(Request $request)
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

    public function excluirQr(Request $request){
        try{

             $id_qr= $request['id_qr'];
             $result = QrCodeService::deletar($id_qr);
             return back()->with('success', $result['message']);
         }catch(\Throwable $e){
             return back()->with('error', 'Houve um erro ao tentar remover o QR Code');
         }
    }
}

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
use App\Helpers\QrImageHelper;
use Illuminate\Support\Facades\Response;


class QrCodeController extends Controller
{
    public function coletarQr(Request $request)
    {
        if ($request->has('id_local') && $request->has('id_maquina')) {
            $qrCode  = QrCodeService::coletarComFiltro(['id_local' => $request['id_local'], 'id_maquina' => $request['id_maquina']], 'where');
            $maquina = MaquinasService::coletar($request['id_maquina']);
            $local   = LocaisService::coletar($request['id_local']);

            if (empty($qrCode)) {
                return back()->with('error', 'Nenhum QR Code encontrado para os dados fornecidos.');
            }

            $rawBase64 = preg_replace('#^data:image/\w+;base64,#i', '', $qrCode[0]['qr_image']);
            $fontPath  = public_path('site/fonts/Roboto-Bold.ttf');
            $qrImagem  = QrImageHelper::buildQrImage($rawBase64, $maquina['id_placa'], $fontPath);

            if ($request->has('abrir')) {
                $locais   = LocaisService::coletar();
                $maquinas = MaquinasService::coletar();
                $clientes = ClientesService::coletar();

                session()->flash('imageQr', $qrImagem);
                session()->flash('dadosQr', $qrCode[0]);
                session()->flash('maquina', $maquina);
                session()->flash('local', $local);
                return view('Admin.QR.index', compact('locais', 'clientes', 'maquinas'));
            }

            return back()->with(['imageQr' => $qrImagem, 'dadosQr' => $qrCode[0], 'maquina' => $maquina, 'local' => $local]);
        }

        $locais   = LocaisService::coletar();
        $maquinas = MaquinasService::coletar();
        $clientes = ClientesService::coletar();
        return view('Admin.QR.index', compact('locais', 'maquinas', 'clientes'));
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

            if(isset($qr['message'])){
                if($qr['message'] == "Qr Code cadastrado com sucesso!"){
                    return back()-> with(['success' => $qr['message'], 'id_local' => $request['select_local'], 'id_maquina' =>$request['select_maquina']]);
                }else{
                    return back()-> with('error', $qr['message']);
                }
            }else{
                return back()-> with('error', 'Houve um erro ao tentar registrar o QR Code. Verifique se as Credenciais estão cadastradas corretamente e tente novamente.');
            }
        }catch(\Throwable $e){
            return back()->with('error', 'Houve um erro inesperado ao tentar registrar o QR Code.');
        }

    }

    public function downloadQr(Request $request)
    {
        if ($request->has('id_local') && $request->has('id_maquina')) {
            $qrCode  = QrCodeService::coletarComFiltro(['id_local' => $request['id_local'], 'id_maquina' => $request['id_maquina']], 'where');
            $maquina = MaquinasService::coletar($request['id_maquina']);

            if (empty($qrCode)) {
                return back()->with('error', 'Nenhum QR Code encontrado para os dados fornecidos.');
            }

            $rawBase64 = preg_replace('#^data:image/\w+;base64,#i', '', $qrCode[0]['qr_image']);
            $fontPath  = public_path('site/fonts/Roboto-Bold.ttf');
            $qrImagem  = QrImageHelper::buildQrImage($rawBase64, $maquina['id_placa'], $fontPath);

            $imageData  = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $qrImagem));
            $outputFile = storage_path('app/public/qr_code_download.png');
            file_put_contents($outputFile, $imageData);

            return response()->download($outputFile, 'qr_code.png')->deleteFileAfterSend(true);
        }

        $locais   = LocaisService::coletar();
        $maquinas = MaquinasService::coletar();
        $clientes = ClientesService::coletar();
        return view('Admin.QR.index', compact('locais', 'maquinas', 'clientes'));
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

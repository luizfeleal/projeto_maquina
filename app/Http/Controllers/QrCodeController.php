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
    public function coletarQr(Request $request)
{
    if ($request->has('id_local') && $request->has('id_maquina')) {
        $qrCode = QrCodeService::coletarComFiltro(['id_local' => $request['id_local'], 'id_maquina' => $request['id_maquina']], 'where');
        $maquina = MaquinasService::coletar($request['id_maquina']);
        $local = LocaisService::coletar($request['id_local']);

        if (empty($qrCode)) {
            return back()->with('error', 'Nenhum QR Code encontrado para os dados fornecidos.');
        }

        // Caminho da imagem de fundo
        $backgroundPath = public_path('/site/img/qr-background.png');

        // Base64 da imagem que será sobreposta
        $base64Image = $qrCode[0]['qr_image'];
        $base64Image = preg_replace('#^data:image/\w+;base64,#i', '', $base64Image);

        // Decodifica a imagem base64
        $decodedImage = base64_decode($base64Image);

        // Cria uma imagem a partir do background (PNG)
        $background = imagecreatefrompng($backgroundPath);

        // Cria uma imagem a partir da base64 decodificada
        $overlay = imagecreatefromstring($decodedImage);

        // Obtém as dimensões originais da imagem sobreposta
        $overlayWidth = imagesx($overlay);
        $overlayHeight = imagesy($overlay);

        // Define o novo tamanho da imagem sobreposta (por exemplo, aumentar 50%)
        $newWidth = $overlayWidth * 1.4; // 150% do tamanho original
        $newHeight = $overlayHeight * 1.4;

        // Cria uma nova imagem vazia com o novo tamanho
        $resizedOverlay = imagecreatetruecolor($newWidth, $newHeight);

        // Mantém a transparência ao redimensionar
        imagealphablending($resizedOverlay, false);
        imagesavealpha($resizedOverlay, true);

        // Redimensiona a imagem sobreposta
        imagecopyresampled(
            $resizedOverlay,
            $overlay,
            0,
            0,
            0,
            0,
            $newWidth,
            $newHeight,
            $overlayWidth,
            $overlayHeight
        );

        // Define a posição da imagem sobreposta (centralizada horizontalmente e deslocada 120px para baixo)
        $x = (imagesx($background) - $newWidth) / 2;
        $y = (imagesy($background) - $newHeight) / 2 + 150;

        // Sobrepõe a imagem redimensionada sobre a de fundo
        imagecopy($background, $resizedOverlay, $x, $y, 0, 0, $newWidth, $newHeight);

        // Adiciona texto à imagem
        $text = $maquina['id_placa'];
        $fontSize = 30;
        $textWidth = imagefontwidth($fontSize) * strlen($text);
        $textHeight = imagefontheight($fontSize);
        $textX = (imagesx($background) - $textWidth) / 2;
        $textY = imagesy($background) - $textHeight - 20;
        $textColor = imagecolorallocate($background, 255, 255, 255);

        imagestring($background, $fontSize, $textX, $textY, $text, $textColor);

        // Cria um buffer para armazenar a imagem como string
        ob_start();
        imagepng($background);
        $imageData = ob_get_contents();
        ob_end_clean();

        // Converte a imagem final para base64
        $qrImagem = 'data:image/png;base64,' . base64_encode($imageData);

        // Libera memória
        imagedestroy($background);
        imagedestroy($overlay);

        if ($request->has('abrir')) {
            $locais = LocaisService::coletar();
            $maquinas = MaquinasService::coletar();
            $clientes = ClientesService::coletar();

            session()->flash('imageQr', $qrImagem);
            session()->flash('dadosQr', $qrCode[0]);
            session()->flash('maquina', $maquina);
            session()->flash('local', $local);
            return view('Admin.QR.index', [
                'locais' => $locais,
                'clientes' => $clientes,
                'maquinas' => $maquinas,
            ]);
        } else {
            return back()->with(['imageQr' => $qrImagem, 'dadosQr' => $qrCode[0], 'maquina' => $maquina, 'local' => $local]);
        }
    } else {
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
    $id_local = $request['id_local'];
    $id_maquina = $request['id_maquina'];
    $maquina = MaquinasService::coletar($id_maquina);
    $qrCode = QrCodeService::coletarComFiltro(['id_local' => $id_local, 'id_maquina' => $id_maquina], 'where');

    if (empty($qrCode)) {
        return response()->json(['error' => 'Nenhum QR Code encontrado para os dados fornecidos.'], 404);
    }

    // Caminho da imagem de fundo
    $backgroundPath = public_path('/site/img/qr-background.png');

    // Base64 da imagem que será sobreposta
    $base64Image = preg_replace('#^data:image/\w+;base64,#i', '', $qrCode[0]['qr_image']);

    // Decodifica a imagem base64
    $decodedImage = base64_decode($base64Image);

    // Cria uma imagem a partir do background (PNG)
    $background = imagecreatefrompng($backgroundPath);

    // Cria uma imagem a partir da base64 decodificada
    $overlay = imagecreatefromstring($decodedImage);

    // Obtém as dimensões originais da imagem sobreposta
    $overlayWidth = imagesx($overlay);
    $overlayHeight = imagesy($overlay);

    // Define o novo tamanho da imagem sobreposta (por exemplo, aumentar 50%)
    $newWidth = $overlayWidth * 1.4;  // 150% do tamanho original
    $newHeight = $overlayHeight * 1.4;

    // Cria uma nova imagem vazia com o novo tamanho
    $resizedOverlay = imagecreatetruecolor($newWidth, $newHeight);

    // Mantém a transparência ao redimensionar
    imagealphablending($resizedOverlay, false);
    imagesavealpha($resizedOverlay, true);

    // Redimensiona a imagem sobreposta
    imagecopyresampled(
        $resizedOverlay,
        $overlay,
        0,
        0,
        0,
        0,
        $newWidth,
        $newHeight,
        $overlayWidth,
        $overlayHeight
    );

    // Define a posição da imagem sobreposta (centralizada horizontalmente e deslocada 120px para baixo)
    $x = (imagesx($background) - $newWidth) / 2;
    $y = (imagesy($background) - $newHeight) / 2 + 150;

    // Sobrepõe a imagem redimensionada sobre a de fundo
    imagecopy($background, $resizedOverlay, $x, $y, 0, 0, $newWidth, $newHeight);

    // Adiciona texto à imagem
    $text = $id_maquina['id_placa']; // Texto que será adicionado
    $fontSize = 30; // Tamanho da fonte (1 a 5)
    $textWidth = imagefontwidth($fontSize) * strlen($text);
    $textHeight = imagefontheight($fontSize);
    $textX = (imagesx($background) - $textWidth) / 2; // Centralizado horizontalmente
    $textY = $y - $textHeight - 10; // Posicionado 10px acima do QR code
    $textColor = imagecolorallocate($background, 255, 255, 255); // Cor do texto: branco

    imagestring($background, $fontSize, $textX, $textY, $text, $textColor);

    // Cria um buffer para armazenar a imagem como string
    ob_start();
    imagepng($background);
    $imageData = ob_get_contents();
    ob_end_clean();

    // Define o nome do arquivo de saída
    $outputFile = public_path('qr_code.png');

    // Salva a imagem gerada
    file_put_contents($outputFile, $imageData);

    // Libera memória
    imagedestroy($background);
    imagedestroy($overlay);

    // Define o cabeçalho para download do arquivo
    return response()->download($outputFile, 'qr_code.png')->deleteFileAfterSend(true);
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

<?php

namespace App\Http\Controllers\Clientes;

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
use App\Http\Controllers\Controller;


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
        $textColor = imagecolorallocate($background, 255, 255, 255);

        // Para aumentar o tamanho do texto de verdade, precisa usar fonte TTF (imagettftext).
        // Coloque um arquivo .ttf em: public/site/fonts/DejaVuSans.ttf (ou altere o caminho abaixo).
        $fontPath = public_path('/site/fonts/DejaVuSans.ttf');
        $fontSize = 30;
        $angle = 0;

        if (is_file($fontPath)) {
            $bbox = imagettfbbox($fontSize, $angle, $fontPath, $text);
            $minX = min($bbox[0], $bbox[2], $bbox[4], $bbox[6]);
            $maxX = max($bbox[0], $bbox[2], $bbox[4], $bbox[6]);
            $minY = min($bbox[1], $bbox[3], $bbox[5], $bbox[7]);
            $maxY = max($bbox[1], $bbox[3], $bbox[5], $bbox[7]);

            $textWidth = $maxX - $minX;
            $imgW = imagesx($background);
            $imgH = imagesy($background);

            // Centraliza horizontalmente e posiciona o "bottom" do texto a 20px da borda inferior
            $textX = (($imgW - $textWidth) / 2) - $minX;
            $desiredBottom = $imgH - 20;
            $textY = $desiredBottom - $maxY;

            imagettftext($background, $fontSize, $angle, (int) $textX, (int) $textY, $textColor, $fontPath, $text);
        } else {
            // Fallback (fonte interna do GD): tamanho máximo é 5
            $font = 5;
            $textWidth = imagefontwidth($font) * strlen($text);
            $textHeight = imagefontheight($font);
            $textX = (imagesx($background) - $textWidth) / 2;
            $textY = imagesy($background) - $textHeight - 20;

            imagestring($background, $font, (int) $textX, (int) $textY, $text, $textColor);
        }

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
                return view('Clientes.QR.index', [
                    'locais' => $locais,
                    'clientes' => $clientes,
                    'maquinas' => $maquinas,
                ]);
            } else {
                return back()->with(['imageQr' => $qrImagem, 'dadosQr' => $qrCode[0], 'maquina' => $maquina, 'local' => $local]);
            }
        } else {
            //return back()->with('error', 'Máquina não encontrada');
            $id_cliente = session()->get('id_cliente');

            $locais_clientes = ClienteLocalService::coletar();
            $locais_clientes = array_filter($locais_clientes, function($item) use($id_cliente){
                return $item['id_cliente'] == $id_cliente;
            });
            $locais_clientes = collect($locais_clientes)->pluck('id_local')->toArray();

            $locais = LocaisService::coletar();
            $locais = array_filter($locais, function($item) use($locais_clientes){
                return in_array($item['id_local'], $locais_clientes);
            });
            $clientes = ClientesService::coletar();

            $clientes = array_filter($clientes, function($item) use($id_cliente){
                return $item['id_cliente'] == $id_cliente;
            });

            $maquinas = MaquinasService::coletar();

            $maquinas = array_filter($maquinas, function($item) use($locais_clientes){
                return in_array($item['id_local'], $locais_clientes);
            });
            return view('Clientes.QR.index', compact('locais', 'maquinas', 'clientes'));
        }
    }

    public function criarQr(Request $request)
    {

        $id_cliente = session()->get('id_cliente');

            $locais_clientes = ClienteLocalService::coletar();
            $locais_clientes = array_filter($locais_clientes, function($item) use($id_cliente){
                return $item['id_cliente'] == $id_cliente;
            });
            $locais_clientes = collect($locais_clientes)->pluck('id_local')->toArray();

            $locais = LocaisService::coletar();
            $locais = array_filter($locais, function($item) use($locais_clientes){
                return in_array($item['id_local'], $locais_clientes);
            });
            $clientes = ClientesService::coletar();

            $clientes = array_filter($clientes, function($item) use($id_cliente){
                return $item['id_cliente'] == $id_cliente;
            });

            $maquinas = MaquinasService::coletar();

            $maquinas = array_filter($maquinas, function($item) use($locais_clientes){
                return in_array($item['id_local'], $locais_clientes);
            });

        $qrCode = QrCodeService::coletar();

        // Passo 1: Extrair todos os IDs de máquinas presentes nos QR codes
        $qrCodeIdsMaquinas = array_column($qrCode, 'id_maquina');
        $qrCodeIdsLocais = array_column($qrCode, 'id_local');

        // Passo 2: Filtrar as máquinas que não têm o 'id_maquina' presente no array de QR codes
        $maquinas = array_filter($maquinas, function ($maquina) use ($qrCodeIdsMaquinas) {
            return !in_array($maquina['id_maquina'], $qrCodeIdsMaquinas);
        });

        // Passo 2: Filtrar as máquinas que não têm o 'id_maquina' presente no array de QR codes


        $qrCodeFiltrado = array_filter($qrCode, function ($qrCode) {});

        return view('Clientes.QR.create', compact('locais', 'clientes', 'maquinas'));
    }

    public function registrarQr(Request $request)
    {

        try {

            if ($request['select_local'] == null || $request['select_maquina'] == null) {
                return back()->with('error', 'Todos os campos obrigatórios devem ser preenchidos para a criação do QR Code.');
            }
            $qrExistente = QrCodeService::coletarComFiltro(['id_local' => $request['select_local'], 'id_maquina' => $request['select_maquina']], 'where');

            if (!empty($qrExistente)) {
                return back()->with('error', 'Não foi possível gerar um QR para os dados passados, pois já existe um QR Code para o local e máquina especificados.');
            }

            $id_local = $request['select_local'];
            $cliente_local = ClienteLocalService::coletar();

            $cliente_local = array_filter($cliente_local, function ($item) use ($id_local) {
                return $item['id_local'] == $id_local && $item['cliente_local_principal'] == 1;
            });

            if (empty($cliente_local)) {
                return back()->with('error', 'Não foi possível gerar um QR para os dados passados, pois não foi encontrado um cliente para o local especificado.');
            }
            $cliente_local = array_values($cliente_local);

            $id_cliente = $cliente_local[0]['id_cliente'];

            $credenciais = CredApiPixService::coletar();

            $credencial = array_filter($credenciais, function ($item) use ($id_cliente) {
                return $item['id_cliente'] == $id_cliente && $item['tipo_cred'] == "efi";
            });

            if (empty($credencial)) {
                return back()->with('error', 'Não foi possível gerar um QR para os dados passados, pois não foi encontrado uma credencial para o cliente informado');
            }
            $id_usuario_logado = session()->get('id_usuario');
            $request['id_usuario'] = $id_usuario_logado;
            $request['id_cliente'] = $cliente_local[0]['id_cliente'];

            $qr = QrCodeService::criar($request);

            if(isset($qr['message'])){
                if($qr['message'] == "Qr Code cadastrado com sucesso!"){
                    return back()-> with(['success' => $qr['message'], 'qr_base64_imagem' => $qr['response']['qr_image'], 'id_local' => $request['select_local'], 'id_maquina' =>$request['select_maquina']]);
                }else{
                    return back()-> with('error', $qr['message']);
                }
            }else{
                return back()-> with('error', 'Houve um erro ao tentar registrar o QR Code. Verifique se as Credenciais estão cadastradas corretamente e tente novamente.');
            }
        } catch (\Throwable $e) {
            return back()->with('error', 'Houve um erro inesperado ao tentar registrar o QR Code.');
        }
    }

    public function downloadQr(Request $request)
    {
        $id_local = $request['id_local'];
        $id_maquina = $request['id_maquina'];
        $maquina = MaquinasService::coletar($request['id_maquina']);
        $qrCode = QrCodeService::coletarComFiltro(['id_local' => $id_local, 'id_maquina' => $id_maquina], 'where');
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
        $textColor = imagecolorallocate($background, 255, 255, 255);

        // Para aumentar o tamanho do texto de verdade, precisa usar fonte TTF (imagettftext).
        // Coloque um arquivo .ttf em: public/site/fonts/DejaVuSans.ttf (ou altere o caminho abaixo).
        $fontPath = public_path('/site/fonts/DejaVuSans.ttf');
        $fontSize = 30;
        $angle = 0;

        if (is_file($fontPath)) {
            $bbox = imagettfbbox($fontSize, $angle, $fontPath, $text);
            $minX = min($bbox[0], $bbox[2], $bbox[4], $bbox[6]);
            $maxX = max($bbox[0], $bbox[2], $bbox[4], $bbox[6]);
            $minY = min($bbox[1], $bbox[3], $bbox[5], $bbox[7]);
            $maxY = max($bbox[1], $bbox[3], $bbox[5], $bbox[7]);

            $textWidth = $maxX - $minX;
            $imgW = imagesx($background);
            $imgH = imagesy($background);

            // Centraliza horizontalmente e posiciona o "bottom" do texto a 20px da borda inferior
            $textX = (($imgW - $textWidth) / 2) - $minX;
            $desiredBottom = $imgH - 20;
            $textY = $desiredBottom - $maxY;

            imagettftext($background, $fontSize, $angle, (int) $textX, (int) $textY, $textColor, $fontPath, $text);
        } else {
            // Fallback (fonte interna do GD): tamanho máximo é 5
            $font = 5;
            $textWidth = imagefontwidth($font) * strlen($text);
            $textHeight = imagefontheight($font);
            $textX = (imagesx($background) - $textWidth) / 2;
            $textY = imagesy($background) - $textHeight - 20;

            imagestring($background, $font, (int) $textX, (int) $textY, $text, $textColor);
        }

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

        // Verifica e remove o prefixo de dados se necessário
        if (strpos($qrImagem, 'data:image') === 0) {
            $qrImagem = preg_replace('#^data:image/\w+;base64,#i', '', $qrImagem);
        }

        // Decodifica a imagem base64
        $decoded_image = base64_decode($qrImagem);

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

    public function excluirQr(Request $request)
    {
        try {

            $id_qr = $request['id_qr'];
            $result = QrCodeService::deletar($id_qr);
            return back()->with('success', $result['message']);
        } catch (\Throwable $e) {
            return back()->with('error', 'Houve um erro ao tentar remover o QR Code');
        }
    }
}

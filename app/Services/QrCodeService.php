<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;



class QrCodeService
{


    public static function criar($dados){
        $url = env('APP_URL_API') . "/QRCode";

        $token = AuthService::getToken();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json'
        ])->post($url, $dados->all());
        
        
        return $response;
    }

    public static function coletar(string $id = Null)
    {
        if(is_null($id)){
            $url = env('APP_URL_API') . "/QRCode";
        }else{
            $url = env('APP_URL_API') . "/QRCode/$id";
        }
        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get($url);

        $maquinas = $response->json();

        return $maquinas;
    }

    public static function coletarComFiltro($filtros, $tipo)
    {
        $url = env('APP_URL_API') . "/QRCode";

        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get($url);

        
        $qrCode = $response->json();
        
        // Filtrar os QR codes com base nos filtros fornecidos
        foreach ($filtros as $chave => $valor) {
            if (isset($chave) && $valor !== null) {
                $qrCode = array_filter($qrCode, function ($qr) use ($chave, $valor) {
                    return isset($qr[$chave]) && $qr[$chave] == $valor;
                });
            }
        }

        // Reindexa o array filtrado para evitar problemas com índices
        $qrCode = array_values($qrCode);
        return $qrCode;
            
        
    }

    public function atualizar($dados, $id){
        $url = env('APP_URL_API') . "/maquinas/$id";

        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->post($url, $dados);

        $maquina = $response->json();

        return $maquina;
    }

    public static function deletar($id)
    {
        $url = env('APP_URL_API') . "/QRCode/$id";
        $token = AuthService::getToken();
    
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Seguir redirecionamentos
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Accept: application/json',
        ]);
    
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

    
        if ($status == 200 && $response !== false) {
            return json_decode($response, true);
        } else {
            return response()->json([
                'error' => 'Failed to delete the resource.',
                'status' => $status,
                'message' => $response,
                'curl_error' => $error,
            ], $status);
        }
    }

}

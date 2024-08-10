<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;



class LocaisService
{

    public static function criar($dados){
        $url = env('APP_URL_API') . "/locais";

        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->post($url, $dados);

        $locais = $response->json();

        return $locais;
    }

    public static function coletar(string $id = Null)
    {
        if(is_null($id)){
            $url = env('APP_URL_API') . "/locais";
        }else{
            $url = env('APP_URL_API') . "/locais/$id";
        }
        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get($url);

        $locais = $response->json();

        return $locais;
    }

    public static function coletarComFiltro($filtros, $tipo)
    {
        $url = env('APP_URL_API') . "/locais";

        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get($url);

        if ($response->successful()) {
            // Obtenha os clientes da resposta JSON
            $locais = $response->json();

            // Filtrar os clientes com base nos filtros fornecidos
            foreach ($filtros as $chave => $valor) {
                // Verifique se a chave existe e se o valor não está vazio
                if (isset($locais[$chave]) && $valor !== null) {
                    // Filtrar os clientes com base no valor do filtro
                    $locais = array_filter($locais, function ($local) use ($chave, $valor) {
                        return $local[$chave] === $valor;
                    });
                }
            }

            // Retorna os clientes filtrados
            return $locais;
        } else {
            // Em caso de falha na chamada à API, retorne um array vazio ou uma mensagem de erro
            return [];
        }
    }

    public function atualizar($dados, $id){
        $url = env('APP_URL_API') . "/locais/$id";

        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->post($url, $dados);

        $local = $response->json();

        return $local;
    }

    public static function deletar($id)
    {
        $url = env('APP_URL_API') . "/locais/$id";
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

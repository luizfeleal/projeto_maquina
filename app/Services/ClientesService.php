<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Services\LogsService;
use Illuminate\Support\Facades\Http;



class ClientesService
{


    public static function criar($dados){
        $url = env('APP_URL_API') . "/clientes";

        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->post($url, $dados);

        // Verifica se a requisição foi bem-sucedida
        if ($response->successful()) {
            return [
                'success' => true,
                'data' => $response->json()
            ];
        }else {
            return [
                'success' => false,
                'status' => $response->status(),
                'error' => $response->json()
            ];
        }
    }

    public static function coletar(string $id = Null)
    {
        if(is_null($id)){
            $url = env('APP_URL_API') . "/clientes";
        }else{
            $url = env('APP_URL_API') . "/clientes/$id";
        }
        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get($url);

        $clientes = $response->json();

        return $clientes;
    }

    public static function coletarComFiltro($filtros, $tipo)
    {
        // Realize a chamada à API para obter os clientes
        $url = env('APP_URL_API') . "/clientes";
        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get($url);

        // Verifique se a chamada à API foi bem-sucedida
        if ($response->successful()) {
            // Obtenha os clientes da resposta JSON
            $clientes = $response->json();

            // Filtrar os clientes com base nos filtros fornecidos
            foreach ($filtros as $chave => $valor) {
                // Verifique se a chave existe e se o valor não está vazio
                if (isset($clientes[$chave]) && $valor !== null) {
                    // Filtrar os clientes com base no valor do filtro
                    $clientes = array_filter($clientes, function ($cliente) use ($chave, $valor) {
                        return $cliente[$chave] === $valor;
                    });
                }
            }

            // Retorna os clientes filtrados
            return $clientes;
        } else {
            // Em caso de falha na chamada à API, retorne um array vazio ou uma mensagem de erro
            return [];
        }
    }

    public function atualizar($dados, $id){
        $url = env('APP_URL_API') . "/clientes/$id";

        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->post($url, $dados);

        $clientes = $response->json();

        return $clientes;
    }

    public static function deletar($id)
    {
        $url = env('APP_URL_API') . "/clientes/$id";
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

    
        return $response;
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

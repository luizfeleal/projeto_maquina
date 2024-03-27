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

        $clientes = $response->json();

        return $clientes;
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

}

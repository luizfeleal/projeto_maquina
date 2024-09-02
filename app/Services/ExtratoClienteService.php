<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;



class ExtratoClienteService
{

    public static function criar($dados){
        $url = env('APP_URL_API') . "/extratoCliente";

        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->post($url, $dados);

        $extrato_cliente = $response->json();

        return $extrato_cliente;
    }

    public static function coletar(string $id = Null)
    {
        if(is_null($id)){
            $url = env('APP_URL_API') . "/extratoCliente";
        }else{
            $url = env('APP_URL_API') . "/extratoCliente/$id";
        }
        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get($url);

        $extrato_cliente = $response->json();

        return $extrato_cliente;
    }

    public static function coletarComFiltro($filtros, $tipo)
    {
        $url = env('APP_URL_API') . "/extratoCliente";

        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get($url);

        if ($response->successful()) {
            // Obtenha os clientes da resposta JSON
            $extrato_clientes = $response->json();

            // Filtrar os clientes com base nos filtros fornecidos
            foreach ($filtros as $chave => $valor) {
                // Verifique se a chave existe e se o valor não está vazio
                if (isset($extrato_clientes[$chave]) && $valor !== null) {
                    // Filtrar os clientes com base no valor do filtro
                    $extrato_clientes = array_filter($extrato_clientes, function ($extrato_cliente) use ($chave, $valor) {
                        return $extrato_cliente[$chave] === $valor;
                    });
                }
            }

            // Retorna os clientes filtrados
            return $extrato_clientes;
        } else {
            // Em caso de falha na chamada à API, retorne um array vazio ou uma mensagem de erro
            return [];
        }
    }

    public function atualizar($dados, $id){
        $url = env('APP_URL_API') . "/extratoCliente/$id";

        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->post($url, $dados);

        $extrato_cliente = $response->json();

        return $extrato_cliente;
    }

}

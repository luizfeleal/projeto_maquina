<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;



class GruposAcessoService
{

    public static function criar($dados){
        $url = env('APP_URL_API') . "/gruposAcesso";

        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->post($url, $dados);

        $grupos = $response->json();

        return $grupos;
    }

    public static function coletar(string $id = Null)
    {
        if(is_null($id)){
            $url = env('APP_URL_API') . "/gruposAcesso";
        }else{
            $url = env('APP_URL_API') . "/gruposAcesso/$id";
        }
        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get($url);

        $grupos = $response->json();

        return $grupos;
    }

    public static function coletarComFiltro($filtros, $tipo)
    {
        $url = env('APP_URL_API') . "/gruposAcesso";

        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get($url);

        if ($response->successful()) {
            // Obtenha os clientes da resposta JSON
            $grupos = $response->json();

            // Filtrar os clientes com base nos filtros fornecidos
            foreach ($filtros as $chave => $valor) {
                // Verifique se a chave existe e se o valor não está vazio
                if (isset($grupos[$chave]) && $valor !== null) {
                    // Filtrar os clientes com base no valor do filtro
                    $grupos = array_filter($grupos, function ($grupo) use ($chave, $valor) {
                        return $grupo[$chave] === $valor;
                    });
                }
            }

            // Retorna os clientes filtrados
            return $grupos;
        } else {
            // Em caso de falha na chamada à API, retorne um array vazio ou uma mensagem de erro
            return [];
        }
    }

    public function atualizar($dados, $id){
        $url = env('APP_URL_API') . "/gruposAcesso/$id";

        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->post($url, $dados);

        $local = $response->json();

        return $grupo;
    }

}

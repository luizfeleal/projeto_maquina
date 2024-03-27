<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;



class MaquinasService
{


    public static function criar($dados){
        $url = env('APP_URL_API') . "/maquinas";

        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->post($url, $dados);

        $maquinas = $response->json();

        return $maquinas;
    }

    public static function coletarMaquinas(string $id = Null)
    {
        if(is_null($id)){
            $url = env('APP_URL_API') . "/maquinas";
        }else{
            $url = env('APP_URL_API') . "/maquinas/$id";
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
        $url = env('APP_URL_API') . "/maquinas";

        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get($url);

        if ($response->successful()) {
            // Obtenha os clientes da resposta JSON
            $maquinas = $response->json();

            // Filtrar os clientes com base nos filtros fornecidos
            foreach ($filtros as $chave => $valor) {
                // Verifique se a chave existe e se o valor não está vazio
                if (isset($maquinas[$chave]) && $valor !== null) {
                    // Filtrar os clientes com base no valor do filtro
                    $maquinas = array_filter($maquinas, function ($maquina) use ($chave, $valor) {
                        return $maquina[$chave] === $valor;
                    });
                }
            }

            // Retorna os clientes filtrados
            return $maquinas;
        } else {
            // Em caso de falha na chamada à API, retorne um array vazio ou uma mensagem de erro
            return [];
        }
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


}

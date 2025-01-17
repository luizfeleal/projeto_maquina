<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;



class UsuariosService
{


    public static function criar($dados){
        $url = env('APP_URL_API') . "/usuarios";

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
            $url = env('APP_URL_API') . "/usuarios";
        }else{
            $url = env('APP_URL_API') . "/usuarios/$id";
        }
        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get($url);

        $usuarios = $response->json();

        return $usuarios;
    }

    public static function coletarComFiltro($filtros, $tipo)
{
    $url = env('APP_URL_API') . "/usuarios";

    $token = AuthService::getToken();
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $token
    ])->get($url);

    if ($response->successful()) {
        // Obtenha os clientes da resposta JSON
        $usuarios = $response->json();

        // Inicializar a variável de filtragem com todos os usuários
        $usuariosFiltrado = $usuarios;

        // Filtrar os clientes com base nos filtros fornecidos
        foreach ($filtros as $chave => $valor) {
            // Verifique se o valor do filtro não está vazio
            if ($valor !== null) {
                // Filtrar os clientes com base no valor do filtro
                $usuariosFiltrado = array_filter($usuariosFiltrado, function ($usuario) use ($chave, $valor) {
                    return isset($usuario[$chave]) && $usuario[$chave] == $valor;
                });
            }
        }

        // Retorna os clientes filtrados
        return $usuariosFiltrado;
    } else {
        // Em caso de falha na chamada à API, retorne um array vazio ou uma mensagem de erro
        return [];
    }
}


    public static function atualizar($dados, $id){
        $url = env('APP_URL_API') . "/usuarios/$id";

        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->put($url, $dados);

        $usuario = $response->json();

        return $usuario;
    }

}

<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Services\LogsService;
use Illuminate\Support\Facades\Http;



class AcessosTelaService
{


    public static function criar($dados){
        $url = env('APP_URL_API') . "/acessosTela";

        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->post($url, $dados);

        $acessos = $response->json();

        return $acessos;
    }

    public static function coletar(string $id = Null)
    {
        try {
            if(is_null($id)){
                $url = env('APP_URL_API') . "/acessosTela";
            }else{
                $url = env('APP_URL_API') . "/acessosTela/$id";
            }
            
            $token = AuthService::getToken();
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->get($url);

            if(!$response->successful()){
                \Log::error('Falha ao buscar acessos', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return [];
            }

            $acessos = $response->json();
            
            // Garantir que sempre retorna array
            return is_array($acessos) ? $acessos : [];
            
        } catch (\Exception $e) {
            \Log::error('Erro ao coletar acessos: ' . $e->getMessage());
            return [];
        }
    }

    public static function coletarComFiltro($filtros, $tipo)
    {
        $url = env('APP_URL_API') . "/acessosTela";
        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get($url);

        if ($response->successful()) {
            $acessos = $response->json();

            foreach ($filtros as $chave => $valor) {
                if (isset($acessos[$chave]) && $valor !== null) {
                    $acessos = array_filter($acessos, function ($acesso) use ($chave, $valor) {
                        return $acesso[$chave] === $valor;
                    });
                }
            }

            return $acessos;
        } else {
            // Em caso de falha na chamada à API, retorne um array vazio ou uma mensagem de erro
            return [];
        }
    }

    public function atualizar($dados, $id){
        $url = env('APP_URL_API') . "/acessosTela/$id";

        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->post($url, $dados);

        $acessos = $response->json();

        return $acessos;
    }

}

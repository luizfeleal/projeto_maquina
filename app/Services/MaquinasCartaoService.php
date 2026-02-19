<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;



class MaquinasCartaoService
{


    public static function criar($dados){
        $url = env('APP_URL_API') . "/maquinasCartao";

        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->post($url, $dados);

        $maquinas = $response;

        return $maquinas;
    }

    public static function coletar(string $id = Null)
    {
        if(is_null($id)){
            $url = env('APP_URL_API') . "/maquinasCartao";
        }else{
            $url = env('APP_URL_API') . "/maquinasCartao/$id";
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
        $url = env('APP_URL_API') . "/maquinasCartao";

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
                if (isset($chave) && $valor !== null) {
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

    public static function atualizar($dados){
        $url = env('APP_URL_API') . "/maquinasCartaoAtualizar";


        $token = AuthService::getToken();

        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Accept: application/json',
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        $maquina = json_decode($response);

        return $maquina;
    }

    public static function excluir($id)
    {
        $url = env('APP_URL_API') . "/maquinasCartao/$id";
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
            $data = json_decode($response, true);
            return ['success' => true, 'message' => $data['message'] ?? 'Máquina de cartão excluída com sucesso.'];
        }
        return ['success' => false, 'message' => 'Erro ao excluir a máquina de cartão.'];
    }
}


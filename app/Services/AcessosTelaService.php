<?php

namespace App\Services;

use App\Support\ApiClient;
use Illuminate\Support\Facades\Log;

class AcessosTelaService
{
    public static function criar($dados)
    {
        return ApiClient::post('/acessosTela', $dados)->json();
    }

    public static function coletar(string $id = null)
    {
        try {
            $path = is_null($id) ? '/acessosTela' : "/acessosTela/{$id}";
            $response = ApiClient::get($path);

            if (!$response->successful()) {
                Log::error('Falha ao buscar acessos', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [];
            }

            $acessos = $response->json();
            return is_array($acessos) ? $acessos : [];
        } catch (\Exception $e) {
            Log::error('Erro ao coletar acessos: ' . $e->getMessage());
            return [];
        }
    }

    public static function coletarComFiltro($filtros, $tipo)
    {
        $response = ApiClient::get('/acessosTela');

        if (!$response->successful()) {
            return [];
        }

        $acessos = $response->json();

        foreach ($filtros as $chave => $valor) {
            if ($valor !== null) {
                $acessos = array_filter($acessos, function ($acesso) use ($chave, $valor) {
                    return isset($acesso[$chave]) && $acesso[$chave] == $valor;
                });
            }
        }

        return $acessos;
    }

    public function atualizar($dados, $id)
    {
        return ApiClient::post("/acessosTela/{$id}", $dados)->json();
    }
}

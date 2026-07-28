<?php

namespace App\Services;

use App\Support\ApiClient;

class GruposAcessoService
{
    public static function criar($dados)
    {
        return ApiClient::post('/gruposAcesso', $dados)->json();
    }

    public static function coletar(string $id = null)
    {
        $path = is_null($id) ? '/gruposAcesso' : "/gruposAcesso/{$id}";
        return ApiClient::get($path)->json();
    }

    public static function coletarComFiltro($filtros, $tipo)
    {
        $response = ApiClient::get('/gruposAcesso');

        if (!$response->successful()) {
            return [];
        }

        $grupos = $response->json();

        foreach ($filtros as $chave => $valor) {
            if ($valor !== null) {
                $grupos = array_filter($grupos, function ($grupo) use ($chave, $valor) {
                    return isset($grupo[$chave]) && $grupo[$chave] == $valor;
                });
            }
        }

        return $grupos;
    }

    public function atualizar($dados, $id)
    {
        return ApiClient::post("/gruposAcesso/{$id}", $dados)->json();
    }
}

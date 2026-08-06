<?php

namespace App\Services;

use App\Support\ApiClient;
use App\Support\CacheReferencia;

class LocaisService
{
    use CacheReferencia;

    public static function criar($dados)
    {
        self::esquecerCacheReferencia();

        return ApiClient::post('/locais', $dados)->json();
    }

    public static function coletar(string $id = null)
    {
        if (!is_null($id)) {
            return ApiClient::get("/locais/{$id}")->json();
        }

        // Lista completa: buscada em quase toda tela, muda raramente.
        return self::lembrarReferencia(
            fn () => ApiClient::get('/locais')->json()
        );
    }

    public static function coletarComFiltro($filtros, $tipo)
    {
        $response = ApiClient::get('/locais');

        if (!$response->successful()) {
            return [];
        }

        $locais = $response->json();

        foreach ($filtros as $chave => $valor) {
            if ($valor !== null) {
                $locais = array_filter($locais, function ($local) use ($chave, $valor) {
                    return isset($local[$chave]) && $local[$chave] == $valor;
                });
            }
        }

        return $locais;
    }

    public function atualizar($dados, $id)
    {
        self::esquecerCacheReferencia();

        return ApiClient::post("/locais/{$id}", $dados)->json();
    }

    public static function deletar($id)
    {
        self::esquecerCacheReferencia();

        $response = ApiClient::delete("/locais/{$id}");

        if ($response->successful()) {
            return $response->json();
        }

        return response()->json([
            'error' => 'Failed to delete the resource.',
            'status' => $response->status(),
            'message' => $response->body(),
        ], $response->status());
    }
}

<?php

namespace App\Services;

use App\Support\ApiClient;

class LocaisService
{
    public static function criar($dados)
    {
        return ApiClient::post('/locais', $dados)->json();
    }

    public static function coletar(string $id = null)
    {
        $path = is_null($id) ? '/locais' : "/locais/{$id}";
        return ApiClient::get($path)->json();
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
        return ApiClient::post("/locais/{$id}", $dados)->json();
    }

    public static function deletar($id)
    {
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

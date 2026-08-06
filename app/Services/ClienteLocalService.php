<?php

namespace App\Services;

use App\Support\ApiClient;
use App\Support\CacheReferencia;

class ClienteLocalService
{
    use CacheReferencia;

    public static function criar($dados)
    {
        self::esquecerCacheReferencia();

        $response = ApiClient::post('/clienteLocal', $dados);

        if ($response->successful()) {
            return [
                'success' => true,
                'data' => $response->json(),
            ];
        }

        return [
            'success' => false,
            'status' => $response->status(),
            'error' => $response->json(),
        ];
    }

    public static function coletar(string $id = null)
    {
        if (!is_null($id)) {
            return ApiClient::get("/clienteLocal/{$id}")->json();
        }

        // Lista completa: buscada em quase toda tela, muda raramente.
        return self::lembrarReferencia(
            fn () => ApiClient::get('/clienteLocal')->json()
        );
    }

    public static function coletarComFiltro($filtros, $tipo)
    {
        $response = ApiClient::get('/clienteLocal');

        if (!$response->successful()) {
            return [];
        }

        $clientes = $response->json();

        foreach ($filtros as $chave => $valor) {
            if ($valor !== null) {
                $clientes = array_filter($clientes, function ($cliente) use ($chave, $valor) {
                    return isset($cliente[$chave]) && $cliente[$chave] == $valor;
                });
            }
        }

        return $clientes;
    }

    public function atualizar($dados, $id)
    {
        self::esquecerCacheReferencia();

        return ApiClient::post("/clienteLocal/{$id}", $dados)->json();
    }

    public static function deletar($id)
    {
        self::esquecerCacheReferencia();

        $response = ApiClient::delete("/clienteLocal/{$id}");

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

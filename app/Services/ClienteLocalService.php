<?php

namespace App\Services;

use App\Support\ApiClient;

class ClienteLocalService
{
    public static function criar($dados)
    {
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
        $path = is_null($id) ? '/clienteLocal' : "/clienteLocal/{$id}";
        return ApiClient::get($path)->json();
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
        return ApiClient::post("/clienteLocal/{$id}", $dados)->json();
    }

    public static function deletar($id)
    {
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

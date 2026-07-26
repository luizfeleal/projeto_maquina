<?php

namespace App\Services;

use App\Support\ApiClient;

class ClienteEstoqueProdutoService
{
    public static function vincular(int $idCliente, array $produtos): array
    {
        $response = ApiClient::post("/clientes/{$idCliente}/produtos", ['produtos' => $produtos]);

        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json('response') ?? $response->json()];
        }

        return [
            'success' => false,
            'message' => $response->json('message') ?? 'Erro ao vincular os produtos ao cliente.',
            'errors'  => $response->json('errors') ?? [],
        ];
    }

    public static function listarPorCliente(int $idCliente): array
    {
        $response = ApiClient::get("/clientes/{$idCliente}/produtos");

        if (!$response->successful()) {
            return [];
        }

        $data = $response->json();
        return is_array($data) ? $data : [];
    }

    public static function desvincular(int $id): array
    {
        $response = ApiClient::delete("/clientes/produtos/{$id}");

        if ($response->successful()) {
            return ['success' => true];
        }

        return [
            'success' => false,
            'message' => $response->json('message') ?? 'Erro ao desvincular o produto.',
        ];
    }
}

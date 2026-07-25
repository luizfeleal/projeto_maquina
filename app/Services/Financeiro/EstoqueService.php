<?php

namespace App\Services\Financeiro;

use App\Support\ApiClient;
use Carbon\Carbon;

class EstoqueService
{
    public static function coletar(array $filtros = []): array
    {
        $response = ApiClient::get('/financeiro/estoqueProdutos', $filtros);

        if (!$response->successful()) {
            return [];
        }

        $data = $response->json();
        return is_array($data) ? $data : [];
    }

    public static function criar(array $dados): array
    {
        $response = ApiClient::post('/financeiro/estoqueProdutos', $dados);

        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json()];
        }

        return [
            'success' => false,
            'message' => $response->json('message') ?? 'Erro ao registrar o produto.',
            'errors'  => $response->json('errors') ?? [],
        ];
    }

    public static function excluir(int $id): array
    {
        $response = ApiClient::delete("/financeiro/estoqueProdutos/{$id}");

        if ($response->successful()) {
            return ['success' => true];
        }

        return [
            'success' => false,
            'message' => $response->json('message') ?? 'Erro ao excluir o produto.',
        ];
    }

    public static function normalizarParaView(array $produto): array
    {
        return [
            'id'            => $produto['id'],
            'lote'          => $produto['lote'] ?? null,
            'nome_produto'  => $produto['nome_produto'] ?? '',
            'descricao'     => $produto['descricao'] ?? null,
            'quantidade'    => (int) ($produto['quantidade'] ?? 0),
            'valor'         => (float) ($produto['valor'] ?? 0),
            'cobrar_mensal' => (bool) ($produto['cobrar_mensal'] ?? false),
            'created_at'    => isset($produto['created_at']) ? Carbon::parse($produto['created_at']) : null,
        ];
    }
}

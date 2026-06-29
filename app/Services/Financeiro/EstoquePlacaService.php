<?php

namespace App\Services\Financeiro;

use App\Support\ApiClient;
use Carbon\Carbon;

class EstoquePlacaService
{
    public static function coletar(array $filtros = []): array
    {
        $response = ApiClient::get('/financeiro/estoquePlacas', $filtros);

        if (!$response->successful()) {
            return [];
        }

        $data = $response->json();
        return is_array($data) ? $data : [];
    }

    public static function criar(array $dados): array
    {
        $response = ApiClient::post('/financeiro/estoquePlacas', $dados);

        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json()];
        }

        return [
            'success' => false,
            'message' => $response->json('message') ?? 'Erro ao registrar a placa.',
            'errors'  => $response->json('errors') ?? [],
        ];
    }

    public static function excluir(int $id): array
    {
        $response = ApiClient::delete("/financeiro/estoquePlacas/{$id}");

        if ($response->successful()) {
            return ['success' => true];
        }

        return [
            'success' => false,
            'message' => $response->json('message') ?? 'Erro ao excluir a placa.',
        ];
    }

    public static function normalizarParaView(array $placa): array
    {
        $serial = $placa['serial'] ?? '';

        return [
            'id'           => $placa['id'],
            'placa_serial' => $serial,
            'serial_curto' => strlen($serial) >= 4 ? substr($serial, -4) : $serial,
            'id_maquina'   => $placa['id_cliente_associado'] ?? null,
            'created_at'   => isset($placa['created_at']) ? Carbon::parse($placa['created_at']) : null,
        ];
    }
}

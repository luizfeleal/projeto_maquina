<?php

namespace App\Services\Financeiro;

use App\Support\ApiClient;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class DespesaService
{
    public static function coletar(array $filtros = []): array
    {
        $response = ApiClient::get('/financeiro/despesas', $filtros);

        if (!$response->successful()) {
            return [];
        }

        $data = $response->json();
        return is_array($data) ? $data : [];
    }

    public static function criar(array $dados, ?UploadedFile $arquivo = null): array
    {
        $payload = [
            'titulo'    => $dados['titulo'],
            'descricao' => $dados['descricao'] ?? null,
            'valor'     => $dados['valor'],
            'data'      => $dados['data'],
        ];

        if ($arquivo) {
            $response = ApiClient::postMultipart(
                '/financeiro/despesas',
                $payload,
                [
                    'name'     => 'arquivo',
                    'contents' => file_get_contents($arquivo->getRealPath()),
                    'filename' => $arquivo->getClientOriginalName(),
                ]
            );
        } else {
            $response = ApiClient::post('/financeiro/despesas', $payload);
        }

        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json()];
        }

        return [
            'success' => false,
            'message' => $response->json('message') ?? 'Erro ao registrar a despesa.',
            'errors'  => $response->json('errors') ?? [],
        ];
    }

    public static function excluir(int $id): array
    {
        $response = ApiClient::delete("/financeiro/despesas/{$id}");

        if ($response->successful()) {
            return ['success' => true];
        }

        return [
            'success' => false,
            'message' => $response->json('message') ?? 'Erro ao excluir a despesa.',
        ];
    }

    public static function normalizarParaView(array $despesa): array
    {
        $anexo = $despesa['anexo_path'] ?? null;
        $baseUrl = rtrim(str_replace('/api', '', env('APP_URL_API', '')), '/');

        return [
            'id'               => $despesa['id'],
            'descricao'        => $despesa['titulo'] ?? $despesa['descricao'] ?? '—',
            'tipo'             => $despesa['descricao'] ?? null,
            'data_despesa'     => isset($despesa['data']) ? Carbon::parse($despesa['data']) : null,
            'valor'            => (float) ($despesa['valor'] ?? 0),
            'comprovante_path' => $anexo,
            'comprovante_url'  => $anexo ? $baseUrl . '/storage/' . ltrim($anexo, '/') : null,
        ];
    }
}

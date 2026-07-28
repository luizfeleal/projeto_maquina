<?php

namespace App\Services;

use App\Support\ApiClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * A tabela de mensalidades e a cobrança (boletos Efí) vivem na API
 * (projeto_maquina_api); o painel não tem escrita própria sobre esses
 * dados, então toda leitura de inadimplência passa por aqui.
 */
class MensalidadeService
{
    public static function coletar(array $filtros = []): array
    {
        try {
            $response = ApiClient::get('/financeiro/mensalidades', $filtros);
        } catch (\Throwable $e) {
            Log::warning('[MensalidadeService] Falha ao consultar mensalidades na API.', [
                'filtros' => $filtros,
                'erro'    => $e->getMessage(),
            ]);

            return [];
        }

        if (!$response->successful()) {
            Log::warning('[MensalidadeService] API retornou erro ao consultar mensalidades.', [
                'filtros' => $filtros,
                'status'  => $response->status(),
            ]);

            return [];
        }

        return $response->json() ?? [];
    }

    /**
     * Mensalidades não pagas com vencimento além da tolerância configurada,
     * já cruzadas com nome/e-mail do cliente.
     */
    public static function listarInadimplentes(int $diasTolerancia): array
    {
        $limite = Carbon::today()->subDays($diasTolerancia)->toDateString();

        $mensalidades = collect(self::coletar(['vencimento_fim' => $limite]))
            ->filter(fn ($m) => ($m['status'] ?? null) !== 'pago')
            ->values();

        if ($mensalidades->isEmpty()) {
            return [];
        }

        $clientes = collect(ClientesService::coletar())->keyBy('id_cliente');

        return $mensalidades->map(function ($m) use ($clientes) {
            $cliente = $clientes->get($m['id_cliente']);
            $m['cliente_nome']  = $cliente['cliente_nome'] ?? "Cliente #{$m['id_cliente']}";
            $m['cliente_email'] = $cliente['cliente_email'] ?? null;

            return $m;
        })->sortBy('vencimento')->values()->all();
    }

    public static function totalInadimplencia(int $diasTolerancia): float
    {
        return round(
            collect(self::listarInadimplentes($diasTolerancia))->sum(fn ($m) => (float) ($m['valor'] ?? 0)),
            2
        );
    }

    /**
     * Todas as mensalidades (qualquer status) já cruzadas com nome/e-mail do cliente,
     * usadas na tela de gestão de mensalidades do módulo Financeiro.
     */
    public static function listarComCliente(array $filtros = []): array
    {
        $mensalidades = collect(self::coletar($filtros));

        if ($mensalidades->isEmpty()) {
            return [];
        }

        $clientes = collect(ClientesService::coletar())->keyBy('id_cliente');

        return $mensalidades->map(function ($m) use ($clientes) {
            $cliente = $clientes->get($m['id_cliente']);
            $m['cliente_nome']  = $cliente['cliente_nome'] ?? "Cliente #{$m['id_cliente']}";
            $m['cliente_email'] = $cliente['cliente_email'] ?? null;

            return $m;
        })->sortByDesc('vencimento')->values()->all();
    }

    public static function buscar(int $id): ?array
    {
        try {
            $response = ApiClient::get("/financeiro/mensalidades/{$id}");
        } catch (\Throwable $e) {
            Log::warning('[MensalidadeService] Falha ao buscar mensalidade na API.', [
                'id' => $id, 'erro' => $e->getMessage(),
            ]);

            return null;
        }

        if (!$response->successful()) {
            return null;
        }

        $mensalidade = $response->json();

        if (is_array($mensalidade) && !empty($mensalidade['id_cliente'])) {
            $cliente = ClientesService::coletar((string) $mensalidade['id_cliente']);
            $mensalidade['cliente_nome']  = $cliente['cliente_nome'] ?? "Cliente #{$mensalidade['id_cliente']}";
            $mensalidade['cliente_email'] = $cliente['cliente_email'] ?? null;
        }

        return is_array($mensalidade) ? $mensalidade : null;
    }

    public static function resumo(array $filtros = []): array
    {
        try {
            $response = ApiClient::get('/financeiro/mensalidades-resumo', $filtros);
        } catch (\Throwable $e) {
            Log::warning('[MensalidadeService] Falha ao consultar resumo de mensalidades.', [
                'erro' => $e->getMessage(),
            ]);

            return [];
        }

        if (!$response->successful()) {
            return [];
        }

        return $response->json() ?? [];
    }

    public static function criar(array $dados): array
    {
        $response = ApiClient::post('/financeiro/mensalidades', $dados);

        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json('response') ?? $response->json()];
        }

        return [
            'success' => false,
            'message' => $response->json('message') ?? 'Erro ao cadastrar a mensalidade.',
            'errors'  => $response->json('errors') ?? [],
        ];
    }

    public static function atualizar(int $id, array $dados): array
    {
        $response = ApiClient::put("/financeiro/mensalidades/{$id}", $dados);

        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json('response') ?? $response->json()];
        }

        return [
            'success' => false,
            'message' => $response->json('message') ?? 'Erro ao atualizar a mensalidade.',
            'errors'  => $response->json('errors') ?? [],
        ];
    }

    public static function excluir(int $id): array
    {
        $response = ApiClient::delete("/financeiro/mensalidades/{$id}");

        if ($response->successful()) {
            return ['success' => true];
        }

        return [
            'success' => false,
            'message' => $response->json('message') ?? 'Erro ao excluir a mensalidade.',
        ];
    }

    public static function gerarBoleto(int $id): array
    {
        $response = ApiClient::post("/financeiro/mensalidades/{$id}/boleto/gerar");

        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json()];
        }

        return [
            'success' => false,
            'message' => $response->json('message') ?? 'Erro ao gerar o boleto.',
        ];
    }

    public static function cancelarBoleto(int $id): array
    {
        $response = ApiClient::post("/financeiro/mensalidades/{$id}/boleto/cancelar");

        if ($response->successful()) {
            return ['success' => true];
        }

        return [
            'success' => false,
            'message' => $response->json('message') ?? 'Erro ao cancelar o boleto.',
        ];
    }

    public static function reenviarBoleto(int $id, ?string $email = null): array
    {
        $response = ApiClient::post("/financeiro/mensalidades/{$id}/boleto/reenviar", $email ? ['email' => $email] : []);

        if ($response->successful()) {
            return ['success' => true];
        }

        return [
            'success' => false,
            'message' => $response->json('message') ?? 'Erro ao reenviar o boleto.',
        ];
    }
}

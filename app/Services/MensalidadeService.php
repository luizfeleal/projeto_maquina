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
}

<?php

namespace App\Mocks;

use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;

class MockRouter
{
    /** @var array<string, array{key: string, id: string}> */
    private static array $resources = [
        'clientes' => ['key' => 'clientes', 'id' => 'id_cliente'],
        'usuarios' => ['key' => 'usuarios', 'id' => 'id_usuario'],
        'locais' => ['key' => 'locais', 'id' => 'id_local'],
        'clienteLocal' => ['key' => 'clienteLocal', 'id' => 'id_cliente_local'],
        'gruposAcesso' => ['key' => 'gruposAcesso', 'id' => 'id_grupo_acesso'],
        'acessosTela' => ['key' => 'acessosTela', 'id' => 'id_acesso_tela'],
        'maquinas' => ['key' => 'maquinas', 'id' => 'id_maquina'],
        'maquinasCartao' => ['key' => 'maquinasCartao', 'id' => 'id_maquina_cartao'],
        'credApiPix' => ['key' => 'credApiPix', 'id' => 'id_credencial'],
        'QRCode' => ['key' => 'qrcode', 'id' => 'id_qr'],
        'logs' => ['key' => 'logs', 'id' => 'id'],
        'extratoMaquina' => ['key' => 'extratoMaquina', 'id' => 'id_extrato_maquina'],
        'extratoCliente' => ['key' => 'extratoCliente', 'id' => 'id_extrato_cliente'],
    ];

    public static function handle(string $method, string $path, $data = [], array $query = [], array $options = []): Response
    {
        Log::debug('[MockRouter]', compact('method', 'path'));

        if ($path === '/auth/login' && $method === 'POST') {
            return self::json(['access_token' => config('mock.token')]);
        }

        // Endpoints especiais (antes do CRUD genérico)
        $special = self::handleSpecial($method, $path, $data, $query);
        if ($special !== null) {
            return $special;
        }

        // CRUD genérico: /recurso ou /recurso/{id}
        foreach (self::$resources as $segment => $meta) {
            if ($path === "/{$segment}" || str_starts_with($path, "/{$segment}/")) {
                return self::handleResource($method, $path, $segment, $meta, $data, $query);
            }
        }

        return self::json(['message' => "Mock não implementado: {$method} {$path}"], 404);
    }

    private static function handleSpecial(string $method, string $path, $data, array $query): ?Response
    {
        if ($method === 'POST' && $path === '/hardware/maquinasDisponiveis') {
            $placas = MockStore::collection('placasDisponiveis');
            return self::json([
                'response' => [
                    'resposta' => ['pendingDevices' => $placas],
                ],
            ]);
        }

        if ($method === 'POST' && $path === '/hardware/liberarJogada') {
            return self::json(['message' => 'Jogada liberada com sucesso']);
        }

        if ($method === 'GET' && $path === '/totalMaquinas') {
            return self::json(self::buildTotalMaquinas());
        }

        if ($method === 'GET' && $path === '/maquinas' && ($query['withTrash'] ?? false)) {
            return self::json(MockStore::collection('maquinas'));
        }

        if ($method === 'POST' && $path === '/maquinasCartaoAtualizar') {
            $payload = is_array($data) ? $data : [];
            if (isset($payload['id_maquina_cartao'])) {
                MockStore::update('maquinasCartao', $payload['id_maquina_cartao'], $payload, 'id_maquina_cartao');
            }
            return self::json(['message' => 'Máquina de cartão atualizada com sucesso.']);
        }

        if ($method === 'POST' && $path === '/relatorioTotalTransacoes') {
            return self::json(['data' => self::buildRelatorioTransacoes($data)]);
        }

        if ($method === 'POST' && $path === '/relatorioTotalTransacoesTotal') {
            return self::json([
                ['tipo' => 'PIX', 'total' => 10.50],
                ['tipo' => 'Cartão', 'total' => 5.00],
                ['tipo' => 'Dinheiro', 'total' => 2.00],
            ]);
        }

        if ($method === 'POST' && $path === '/relatorioTotalTransacoesTaxa') {
            return self::json(['data' => [
                ['maquina_nome' => 'Máquina Demo 01', 'taxa' => 0.50, 'valor' => 10.50],
                ['maquina_nome' => 'Máquina Demo 02', 'taxa' => 0.25, 'valor' => 2.00],
            ]]);
        }

        if ($method === 'POST' && $path === '/transacaoMaquinaCliente') {
            $maquinas = MockStore::collection('maquinas');
            $maquinasById = [];
            foreach ($maquinas as $m) {
                $maquinasById[$m['id_maquina']] = $m;
            }
            $extrato = MockStore::collection('extratoMaquina');
            foreach ($extrato as &$item) {
                $maq = $maquinasById[$item['id_maquina']] ?? null;
                $item['maquina_nome'] = $maq['maquina_nome'] ?? 'Desconhecida';
                $item['local_nome']   = 'Local ' . ($maq['id_local'] ?? '-');
            }
            unset($item);
            return self::json($extrato);
        }

        if ($method === 'POST' && $path === '/totalTransacaoMaquinaCliente') {
            return self::json(['total' => 17.50]);
        }

        if ($method === 'GET' && preg_match('#^/extrato/saldo(/.*)?$#', $path)) {
            return self::json(self::buildSaldoResumo());
        }

        if ($method === 'GET' && preg_match('#^/extrato/devolucao(/.*)?$#', $path)) {
            return self::json(self::buildDevolucoesResumo());
        }

        // PUT credApiPix com certificado via POST /credApiPix/{id}/atualizar
        if ($method === 'POST' && preg_match('#^/credApiPix/(\d+)/atualizar$#', $path, $m)) {
            $updated = MockStore::update('credApiPix', $m[1], is_array($data) ? $data : [], 'id_credencial');
            return self::json(['response' => $updated ?? [], 'message' => 'Credencial atualizada com sucesso.']);
        }

        return null;
    }

    private static function handleResource(string $method, string $path, string $segment, array $meta, $data, array $query): Response
    {
        $key = $meta['key'];
        $idField = $meta['id'];

        // /recurso/{id}
        if (preg_match("#^/{$segment}/([^/]+)$#", $path, $matches)) {
            $id = $matches[1];

            switch ($method) {
                case 'GET':
                    $item = MockStore::find($key, $id, $idField);
                    return $item ? self::json($item) : self::json(['message' => 'Registro não encontrado'], 404);

                case 'PUT':
                    $updated = MockStore::update($key, $id, is_array($data) ? $data : [], $idField);
                    return $updated ? self::json($updated) : self::json(['message' => 'Registro não encontrado'], 404);

                case 'DELETE':
                    $deleted = MockStore::delete($key, $id, $idField);
                    return $deleted
                        ? self::json(['message' => self::deleteMessage($segment)])
                        : self::json(['message' => 'Registro não encontrado'], 404);

                case 'POST':
                    // Alguns recursos usam POST para update (legado da API)
                    $updated = MockStore::update($key, $id, is_array($data) ? $data : [], $idField);
                    return $updated ? self::json($updated) : self::json(['message' => 'Registro não encontrado'], 404);
            }
        }

        // /recurso (lista ou criação)
        if ($path === "/{$segment}") {
            switch ($method) {
                case 'GET':
                    $items = MockStore::collection($key);

                    if ($segment === 'extratoMaquina' && !empty($query)) {
                        return self::json([
                            'data' => $items,
                            'current_page' => (int) ($query['page'] ?? 1),
                            'last_page' => 1,
                        ]);
                    }

                    return self::json(array_values($items));

                case 'POST':
                    return self::handleCreate($segment, $key, $idField, $data);
            }
        }

        return self::json(['message' => "Mock não implementado: {$method} {$path}"], 404);
    }

    private static function handleCreate(string $segment, string $key, string $idField, $data): Response
    {
        $payload = self::normalizePayload($data);
        $now = now()->format('Y-m-d H:i:s');

        switch ($segment) {
            case 'clientes':
                $record = MockStore::create($key, array_merge($payload, ['data_criacao' => $now]), $idField);
                return self::json(['response' => $record]);

            case 'usuarios':
                $record = MockStore::create($key, array_merge($payload, [
                    'ativo' => $payload['ativo'] ?? 1,
                    'data_inclusao' => $now,
                ]), $idField);
                return self::json(['response' => $record]);

            case 'locais':
                $record = MockStore::create($key, array_merge($payload, ['data_criacao' => $now]), $idField);
                return self::json(['response' => $record]);

            case 'clienteLocal':
                $record = MockStore::create($key, $payload, $idField);
                return self::json(['response' => $record]);

            case 'maquinas':
                $record = MockStore::create($key, array_merge($payload, [
                    'maquina_status' => $payload['maquina_status'] ?? 0,
                    'maquina_ultimo_contato' => $now,
                    'data_criacao' => $now,
                    'deleted_at' => null,
                ]), $idField);
                return self::json(['message' => 'Máquina cadastrada com sucesso!', 'response' => $record]);

            case 'maquinasCartao':
                $record = MockStore::create($key, array_merge($payload, ['data_criacao' => $now]), $idField);
                return self::json(['message' => 'Máquina de cartão cadastrada com sucesso.', 'response' => $record]);

            case 'credApiPix':
                $record = MockStore::create($key, array_merge($payload, ['data_criacao' => $now]), $idField);
                return self::json(['response' => $record]);

            case 'QRCode':
                $record = MockStore::create($key, array_merge($payload, [
                    'ativo' => 1,
                    'qr_image' => 'data:image/png;base64,' . MockData::QR_IMAGE_BASE64,
                    'data_criacao' => $now,
                ]), $idField);
                return self::json(['message' => 'Qr Code cadastrado com sucesso!', 'response' => $record]);

            case 'logs':
                $record = MockStore::create($key, array_merge($payload, ['data_criacao' => $now]), $idField);
                return self::json($record);

            case 'extratoMaquina':
            case 'extratoCliente':
                $record = MockStore::create($key, array_merge($payload, ['data_criacao' => $now]), $idField);
                return self::json($record);

            case 'gruposAcesso':
            case 'acessosTela':
                $record = MockStore::create($key, $payload, $idField);
                return self::json($record);

            default:
                $record = MockStore::create($key, $payload, $idField);
                return self::json(['response' => $record]);
        }
    }

    private static function normalizePayload($data): array
    {
        if (is_array($data)) {
            return $data;
        }

        if (is_object($data) && method_exists($data, 'all')) {
            return $data->all();
        }

        return [];
    }

    private static function buildTotalMaquinas(): array
    {
        $maquinas = MockStore::collection('maquinas');
        $extratos = MockStore::collection('extratoMaquina');
        $result = [];

        foreach ($maquinas as $maquina) {
            if (!empty($maquina['deleted_at'])) {
                continue;
            }

            $ultimo = null;
            foreach ($extratos as $extrato) {
                if ($extrato['id_maquina'] == $maquina['id_maquina']) {
                    $ultimo = $extrato;
                }
            }

            $result[] = array_merge($maquina, [
                'extrato_operacao' => $ultimo['extrato_operacao'] ?? 'N/A',
                'extrato_operacao_valor' => $ultimo['extrato_operacao_valor'] ?? 0,
                'extrato_operacao_tipo' => $ultimo['extrato_operacao_tipo'] ?? 'N/A',
                'data_criacao' => $ultimo['data_criacao'] ?? $maquina['data_criacao'],
            ]);
        }

        return $result;
    }

    private static function buildRelatorioTransacoes($filtros): array
    {
        $extratos = MockStore::collection('extratoMaquina');
        $maquinas = collect(MockStore::collection('maquinas'))->keyBy('id_maquina');

        return array_map(function ($extrato) use ($maquinas) {
            $maq = $maquinas->get($extrato['id_maquina'], []);
            return array_merge($extrato, [
                'maquina_nome' => $maq['maquina_nome'] ?? 'N/A',
                'local_nome' => 'Local Central Mock',
            ]);
        }, $extratos);
    }

    /**
     * Estrutura esperada pelas views Admin/home e Clientes/home.
     */
    private static function buildSaldoResumo(): array
    {
        return [
            'hoje' => 115.50,
            'mes_atual' => 842.30,
            'mes_passado' => 1250.75,
        ];
    }

    private static function buildDevolucoesResumo(): array
    {
        return [
            'hoje' => 5.00,
            'mes_atual' => 32.40,
            'mes_passado' => 18.90,
        ];
    }

    private static function deleteMessage(string $segment): string
    {
        $messages = [
            'clientes' => 'Cliente excluído com sucesso.',
            'locais' => 'Local excluído com sucesso.',
            'clienteLocal' => 'Associação removida com sucesso.',
            'maquinas' => 'Máquina removida com sucesso.',
            'maquinasCartao' => 'Máquina de cartão excluída com sucesso.',
            'QRCode' => 'QR Code removido com sucesso.',
            'credApiPix' => 'Credencial excluída com sucesso.',
        ];

        return $messages[$segment] ?? 'Registro excluído com sucesso.';
    }

    private static function json($body, int $status = 200): Response
    {
        return new Response(new Psr7Response(
            $status,
            ['Content-Type' => 'application/json'],
            json_encode($body, JSON_UNESCAPED_UNICODE)
        ));
    }
}

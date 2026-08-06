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
        'financeiro/despesas' => ['key' => 'despesas', 'id' => 'id'],
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

        // GET /QRCode?sem_imagem=1 — lista sem o base64 do QR
        if ($method === 'GET' && $path === '/QRCode' && filter_var($query['sem_imagem'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            return self::json(array_values(array_map(function ($qr) {
                unset($qr['qr_image']);
                return $qr;
            }, MockStore::collection('qrcode'))));
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

        // GET /extrato/acumulado — retorna dados enriquecidos com campos de reset
        if ($method === 'GET' && $path === '/extrato/acumulado') {
            return self::json(self::buildAcumuladoComReset($query));
        }

        // GET /extrato/resumoHome — resumo consolidado da Home do admin
        if ($method === 'GET' && $path === '/extrato/resumoHome') {
            return self::json(self::buildResumoHome($query));
        }

        // GET /extrato/resumoHomeCliente — resumo consolidado da Home do cliente
        if ($method === 'GET' && $path === '/extrato/resumoHomeCliente') {
            return self::json(self::buildResumoHomeCliente($query));
        }

        // GET /extrato/resumoTransacoes — totais agregados da tela de Extrato
        if ($method === 'GET' && $path === '/extrato/resumoTransacoes') {
            return self::json(self::buildResumoTransacoes($query));
        }

        // GET /extrato/resumoFinanceiro — totais por mês do dashboard financeiro
        if ($method === 'GET' && $path === '/extrato/resumoFinanceiro') {
            return self::json(self::buildResumoFinanceiro());
        }

        // POST /maquinas/{id}/reset-parcial
        if ($method === 'POST' && preg_match('#^/maquinas/([^/]+)/reset-parcial$#', $path, $m)) {
            return self::handleResetParcial($m[1], is_array($data) ? $data : []);
        }

        // GET /reset-parcial/historico
        if ($method === 'GET' && $path === '/reset-parcial/historico') {
            return self::json(self::buildHistoricoResets($query));
        }

        // POST /totalTransacaoMaquinaAcumuladoCliente — enriquece com campos de reset
        if ($method === 'POST' && $path === '/totalTransacaoMaquinaAcumuladoCliente') {
            $idCliente = is_array($data) ? ($data['id_cliente'] ?? null) : null;
            return self::json(self::buildAcumuladoClienteComReset($idCliente, $query));
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
                        return self::json(self::buildExtratoMaquinaPaginated($query));
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

            case 'financeiro/despesas':
                $usuario = null;
                foreach (MockStore::collection('usuarios') as $u) {
                    if (isset($payload['realizado_por']) && (string) ($u['id_usuario'] ?? '') === (string) $payload['realizado_por']) {
                        $usuario = $u;
                        break;
                    }
                }

                $record = MockStore::create($key, array_merge($payload, [
                    'realizado_por_nome' => $usuario['usuario_nome'] ?? null,
                    'data_criacao' => $now,
                ]), $idField);

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

            $local = MockStore::find('locais', $maquina['id_local'], 'id_local');

            $result[] = array_merge($maquina, [
                'local_nome' => $local['local_nome'] ?? ('Local ' . ($maquina['id_local'] ?? '-')),
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

    private static function buildAcumuladoComReset(array $query): array
    {
        $maquinas  = MockStore::collection('maquinas');
        $resets    = MockStore::collection('resetsParciais');
        $extratos  = MockStore::collection('extratoMaquina');

        $ultimoReset = [];
        foreach ($resets as $reset) {
            $idMaq = (string) $reset['id_maquina'];
            if (!isset($ultimoReset[$idMaq]) || $reset['created_at'] > $ultimoReset[$idMaq]['created_at']) {
                $ultimoReset[$idMaq] = $reset;
            }
        }

        $data = [];
        foreach ($maquinas as $maq) {
            if (!empty($maq['deleted_at'])) {
                continue;
            }

            $idMaq = (string) $maq['id_maquina'];
            $reset = $ultimoReset[$idMaq] ?? null;
            $txMaquina = array_values(array_filter(
                $extratos,
                fn($tx) => (string) ($tx['id_maquina'] ?? '') === $idMaq
            ));

            $totalMaq = self::somarExtratoMock($txMaquina);
            $desdeReset = $reset['created_at'] ?? null;
            $saldo = self::somarExtratoMock($txMaquina, $desdeReset);

            $data[] = [
                'id_maquina'        => $idMaq,
                'maquina_nome'      => $maq['maquina_nome'],
                'local_nome'        => 'Local Central Mock',
                'total_maquina'     => $totalMaq,
                'total_pix'         => self::somarExtratoMock($txMaquina, $desdeReset, 'PIX'),
                'total_cartao'      => self::somarExtratoMock($txMaquina, $desdeReset, 'Cartão'),
                'total_dinheiro'    => self::somarExtratoMock($txMaquina, $desdeReset, 'Dinheiro'),
                'ultima_coleta'     => $reset ? (float) $reset['valor_ultima_coleta'] : null,
                'saldo_periodo'     => round($saldo, 2),
                'data_ultimo_reset' => $reset ? self::formatIso8601Mock($reset['created_at']) : null,
                'tem_reset'         => $reset !== null,
            ];
        }

        $page    = (int) ($query['page'] ?? 1);
        $perPage = (int) ($query['per_page'] ?? $query['length'] ?? 10);
        $start   = (int) ($query['start'] ?? 0);
        $length  = (int) ($query['length'] ?? $perPage);
        $total   = count($data);

        if (isset($query['start']) || isset($query['length'])) {
            $data = array_slice($data, $start, $length > 0 ? $length : null);
        }

        return [
            'draw'            => (int) ($query['draw'] ?? 1),
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'data'            => array_values($data),
            'current_page'    => $page,
            'last_page'       => 1,
        ];
    }

    private static function somarExtratoMock(array $transacoes, ?string $desde = null, ?string $tipo = null): float
    {
        $total = 0.0;

        foreach ($transacoes as $tx) {
            $dataTx = (string) ($tx['data_criacao'] ?? '');
            if ($desde !== null && $desde !== '' && $dataTx !== '' && $dataTx <= $desde) {
                continue;
            }

            if ($tipo !== null && ($tx['extrato_operacao_tipo'] ?? '') !== $tipo) {
                continue;
            }

            $valor = (float) ($tx['extrato_operacao_valor'] ?? 0);
            $op    = $tx['extrato_operacao'] ?? 'C';
            $total += ($op === 'D') ? -$valor : $valor;
        }

        return round($total, 2);
    }

    private static function formatIso8601Mock(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return date('Y-m-d\TH:i:s.000000\Z', strtotime($value));
    }

    private static function buildAcumuladoClienteComReset($idCliente, array $query): array
    {
        $resultado = self::buildAcumuladoComReset($query);
        return $resultado;
    }

    private static function handleResetParcial(string $idMaquina, array $data): Response
    {
        $resets  = MockStore::collection('resetsParciais');
        $maquinas = MockStore::collection('maquinas');

        $maquina = null;
        foreach ($maquinas as $m) {
            if ((string) $m['id_maquina'] === $idMaquina) {
                $maquina = $m;
                break;
            }
        }

        if (!$maquina) {
            return self::json(['message' => 'Máquina não encontrada.'], 404);
        }

        if (empty($data['realizado_por'])) {
            return self::json(['message' => 'O campo realizado_por é obrigatório.', 'errors' => ['realizado_por' => ['Campo obrigatório']]], 422);
        }

        $totalMaquina = self::somarExtratoMock(
            array_values(array_filter(
                MockStore::collection('extratoMaquina'),
                fn($tx) => (string) ($tx['id_maquina'] ?? '') === $idMaquina
            ))
        );
        $now = now()->format('Y-m-d H:i:s');

        $novoReset = [
            'id'                  => count($resets) + 1,
            'id_maquina'          => (int) $idMaquina,
            'maquina_nome'        => $maquina['maquina_nome'],
            'local_nome'          => 'Local Central Mock',
            'valor_ultima_coleta' => $totalMaquina,
            'valor_acumulado_total' => $totalMaquina,
            'realizado_por'       => $data['realizado_por'],
            'realizado_por_nome'  => 'Usuário Mock',
            'observacao'          => $data['observacao'] ?? null,
            'created_at'          => $now,
        ];

        MockStore::create('resetsParciais', $novoReset, 'id');

        return self::json([
            'message' => 'Reset parcial registrado com sucesso.',
            'data' => array_merge($novoReset, [
                'saldo_periodo' => 0.00,
                'data_ultimo_reset' => self::formatIso8601Mock($now),
                'created_at' => self::formatIso8601Mock($now),
            ]),
        ], 201);
    }

    private static function buildExtratoMaquinaPaginated(array $query): array
    {
        $maquinas = MockStore::collection('maquinas');
        $maquinasById = [];
        foreach ($maquinas as $m) {
            $maquinasById[$m['id_maquina']] = $m;
        }

        $locaisByCliente = [];
        foreach (MockStore::collection('clienteLocal') as $cl) {
            $locaisByCliente[$cl['id_cliente']][] = $cl['id_local'];
        }

        $items = [];
        foreach (MockStore::collection('extratoMaquina') as $item) {
            $maq = $maquinasById[$item['id_maquina']] ?? null;
            if (!$maq) {
                continue;
            }

            $item['maquina_nome'] = $maq['maquina_nome'] ?? 'Desconhecida';
            $item['local_nome']   = 'Local ' . ($maq['id_local'] ?? '-');
            $item['id_local']     = $maq['id_local'] ?? null;
            $items[] = $item;
        }

        if (!empty($query['id_maquina'])) {
            $items = array_values(array_filter(
                $items,
                fn($i) => (string) $i['id_maquina'] === (string) $query['id_maquina']
            ));
        }

        if (!empty($query['id_local'])) {
            $items = array_values(array_filter(
                $items,
                fn($i) => (string) ($i['id_local'] ?? '') === (string) $query['id_local']
            ));
        }

        if (!empty($query['id_cliente'])) {
            $locaisCliente = $locaisByCliente[$query['id_cliente']] ?? [];
            $items = array_values(array_filter(
                $items,
                fn($i) => in_array($i['id_local'], $locaisCliente)
            ));
        }

        if (!empty($query['search_value']) || !empty($query['search'])) {
            $search = strtolower($query['search_value'] ?? $query['search'] ?? '');
            $items = array_values(array_filter($items, function ($i) use ($search) {
                return str_contains(strtolower($i['maquina_nome'] ?? ''), $search)
                    || str_contains(strtolower($i['local_nome'] ?? ''), $search)
                    || str_contains(strtolower($i['extrato_operacao_tipo'] ?? ''), $search);
            }));
        }

        if (!empty($query['data_inicio'])) {
            $inicioTs = strtotime($query['data_inicio'] . ' 00:00:00');
            $items = array_values(array_filter($items, function ($i) use ($inicioTs) {
                $ts = strtotime($i['data_criacao'] ?? '');
                return $ts !== false && $ts >= $inicioTs;
            }));
        }

        if (!empty($query['data_fim'])) {
            $fimTs = strtotime($query['data_fim'] . ' 23:59:59');
            $items = array_values(array_filter($items, function ($i) use ($fimTs) {
                $ts = strtotime($i['data_criacao'] ?? '');
                return $ts !== false && $ts <= $fimTs;
            }));
        }

        if (!empty($query['tipo_operacao'])) {
            $tipo = strtolower((string) $query['tipo_operacao']);
            $items = array_values(array_filter($items, function ($i) use ($tipo) {
                $tipoTx = strtolower($i['extrato_operacao_tipo'] ?? '');
                return match ($tipo) {
                    'pix' => str_contains($tipoTx, 'pix'),
                    'cartao', 'cartão' => str_contains($tipoTx, 'cart'),
                    'dinheiro' => str_contains($tipoTx, 'dinheir'),
                    default => $tipoTx === $tipo,
                };
            }));
        }

        $total   = count($items);
        $perPage = max((int) ($query['length'] ?? $query['per_page'] ?? 10), 1);
        $start   = (int) ($query['start'] ?? 0);
        if (isset($query['start']) || isset($query['length'])) {
            $page   = (int) floor($start / $perPage) + 1;
            $offset = $start;
        } else {
            $page   = max((int) ($query['page'] ?? 1), 1);
            $offset = ($page - 1) * $perPage;
        }

        return [
            'draw'            => (int) ($query['draw'] ?? 1),
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'data'            => array_slice($items, $offset, $perPage),
            'current_page'    => $page,
            'last_page'       => max(1, (int) ceil($total / $perPage)),
            'total'           => $total,
        ];
    }

    /**
     * Transações do extrato já filtradas, sem paginação — base dos resumos.
     * Reusa os filtros de buildExtratoMaquinaPaginated e acrescenta o de taxa,
     * que só os endpoints de resumo usam.
     */
    private static function filtrarTransacoesParaResumo(array $query): array
    {
        $paginado = self::buildExtratoMaquinaPaginated(array_merge($query, [
            'start'  => 0,
            'length' => PHP_INT_MAX,
        ]));

        $items = $paginado['data'] ?? [];

        if (array_key_exists('mostrar_taxas', $query) && !filter_var($query['mostrar_taxas'], FILTER_VALIDATE_BOOLEAN)) {
            $items = array_values(array_filter(
                $items,
                fn($i) => !str_contains(strtolower($i['extrato_operacao_tipo'] ?? ''), 'taxa')
            ));
        }

        return $items;
    }

    /**
     * Mesma classificação da API: devolução ganha de tudo, depois PIX, Cartão e
     * Dinheiro, nessa ordem.
     */
    private static function somarTotaisPorTipo(array $items): array
    {
        $totais = [
            'total_registros' => count($items),
            'total_acumulado' => 0.0,
            'total_saldo'     => 0.0,
            'total_pix'       => 0.0,
            'total_cartao'    => 0.0,
            'total_dinheiro'  => 0.0,
            'total_devolucao' => 0.0,
        ];

        foreach ($items as $tx) {
            $valor = (float) ($tx['extrato_operacao_valor'] ?? 0);
            $tipo  = strtolower($tx['extrato_operacao_tipo'] ?? '');
            $op    = $tx['extrato_operacao'] ?? 'C';

            $totais['total_acumulado'] += ($op === 'D') ? -$valor : $valor;

            if ($op === 'D') {
                $totais['total_devolucao'] += $valor;
            } elseif (str_contains($tipo, 'pix')) {
                $totais['total_pix'] += $valor;
            } elseif (str_contains($tipo, 'cart')) {
                $totais['total_cartao'] += $valor;
            } elseif (str_contains($tipo, 'dinheir') || str_contains($tipo, 'físic') || str_contains($tipo, 'fisic')) {
                $totais['total_dinheiro'] += $valor;
            }
        }

        $totais['total_acumulado'] = round($totais['total_acumulado'], 2);
        $totais['total_devolucao'] = round($totais['total_devolucao'], 2);
        $totais['total_saldo']     = round($totais['total_acumulado'] - $totais['total_devolucao'], 2);
        $totais['total_pix']       = round($totais['total_pix'], 2);
        $totais['total_cartao']    = round($totais['total_cartao'], 2);
        $totais['total_dinheiro']  = round($totais['total_dinheiro'], 2);

        return $totais;
    }

    private static function buildResumoTransacoes(array $query): array
    {
        return self::somarTotaisPorTipo(self::filtrarTransacoesParaResumo($query));
    }

    private static function buildResumoHome(array $query): array
    {
        $items = self::filtrarTransacoesParaResumo($query);

        usort($items, fn($a, $b) => strtotime($b['data_criacao'] ?? '') <=> strtotime($a['data_criacao'] ?? ''));

        $totaisPorTipoMes = [];
        foreach ($items as $tx) {
            if (($tx['extrato_operacao'] ?? 'C') === 'D') {
                continue;
            }
            $ts = strtotime($tx['data_criacao'] ?? '');
            if ($ts === false) {
                continue;
            }
            $chave = date('Y', $ts) . '-' . date('n', $ts) . '-' . strtolower($tx['extrato_operacao_tipo'] ?? '');
            if (!isset($totaisPorTipoMes[$chave])) {
                $totaisPorTipoMes[$chave] = [
                    'ano'   => (int) date('Y', $ts),
                    'mes'   => (int) date('n', $ts),
                    'tipo'  => strtolower($tx['extrato_operacao_tipo'] ?? ''),
                    'total' => 0.0,
                ];
            }
            $totaisPorTipoMes[$chave]['total'] += (float) ($tx['extrato_operacao_valor'] ?? 0);
        }

        $totalDevolucao = 0.0;
        foreach ($items as $tx) {
            if (($tx['extrato_operacao'] ?? 'C') === 'D') {
                $totalDevolucao += (float) ($tx['extrato_operacao_valor'] ?? 0);
            }
        }

        return [
            'saldo'                  => self::buildSaldoResumo(),
            'devolucoes'             => self::buildDevolucoesResumo(),
            'maquinas'               => array_values(MockStore::collection('maquinas')),
            'locais'                 => array_values(MockStore::collection('locais')),
            'clientes'               => array_values(MockStore::collection('clientes')),
            'qr_codes'               => self::qrCodesSemImagem(),
            'acumulado'              => self::buildAcumuladoComReset(['length' => 5000, 'start' => 0])['data'] ?? [],
            'ultimas_transacoes'     => array_slice($items, 0, 15),
            'totais_por_tipo_mes'    => array_values($totaisPorTipoMes),
            'total_devolucao_filtro' => round($totalDevolucao, 2),
        ];
    }

    private static function buildResumoHomeCliente(array $query): array
    {
        $idCliente = $query['id_cliente'] ?? null;

        $idsLocais = [];
        foreach (MockStore::collection('clienteLocal') as $cl) {
            if ((string) ($cl['id_cliente'] ?? '') === (string) $idCliente) {
                $idsLocais[] = $cl['id_local'];
            }
        }

        $maquinas = array_values(array_filter(
            MockStore::collection('maquinas'),
            fn($m) => in_array($m['id_local'] ?? null, $idsLocais)
        ));

        $locais = array_values(array_filter(
            MockStore::collection('locais'),
            fn($l) => in_array($l['id_local'] ?? null, $idsLocais)
        ));

        // Mesma semântica do endpoint real: a última transação de cada máquina.
        $ultimaPorMaquina = [];
        foreach (self::filtrarTransacoesParaResumo(['id_cliente' => $idCliente]) as $tx) {
            $idMaq = (string) ($tx['id_maquina'] ?? '');
            $atual = $ultimaPorMaquina[$idMaq] ?? null;
            if ($atual === null || strtotime($tx['data_criacao'] ?? '') > strtotime($atual['data_criacao'] ?? '')) {
                $ultimaPorMaquina[$idMaq] = $tx;
            }
        }

        return [
            'saldo'      => self::buildSaldoResumo(),
            'devolucoes' => self::buildDevolucoesResumo(),
            'maquinas'   => $maquinas,
            'locais'     => $locais,
            'qr_codes'   => self::qrCodesSemImagem(),
            'acumulado'  => self::buildAcumuladoClienteComReset($idCliente, ['length' => 5000, 'start' => 0])['data'] ?? [],
            'transacoes' => array_values($ultimaPorMaquina),
        ];
    }

    private static function buildResumoFinanceiro(): array
    {
        $porMes = [];
        $totalReceitas = 0.0;
        $totalDespesas = 0.0;

        foreach (MockStore::collection('extratoMaquina') as $tx) {
            $valor = (float) ($tx['extrato_operacao_valor'] ?? 0);
            $op    = $tx['extrato_operacao'] ?? null;

            if ($op === 'C') {
                $totalReceitas += $valor;

                $ts = strtotime($tx['data_criacao'] ?? '');
                if ($ts !== false) {
                    $mes = date('Y-m', $ts);
                    $porMes[$mes] = ($porMes[$mes] ?? 0.0) + $valor;
                }
            } elseif ($op === 'D') {
                $totalDespesas += $valor;
            }
        }

        ksort($porMes);

        return [
            'por_mes' => array_map(
                fn($mes, $total) => ['mes' => $mes, 'total' => round($total, 2)],
                array_keys($porMes),
                array_values($porMes)
            ),
            'total_receitas' => round($totalReceitas, 2),
            'total_despesas' => round($totalDespesas, 2),
        ];
    }

    /** QR codes sem o base64 da imagem, como faz o endpoint real. */
    private static function qrCodesSemImagem(): array
    {
        return array_values(array_map(function ($qr) {
            unset($qr['qr_image']);
            return $qr;
        }, array_filter(
            MockStore::collection('qrcode'),
            fn($qr) => ($qr['ativo'] ?? 0) == 1
        )));
    }

    private static function buildHistoricoResets(array $query): array
    {
        $resets  = MockStore::collection('resetsParciais');
        $filtrados = array_values($resets);

        if (!empty($query['id_maquina'])) {
            $filtrados = array_values(array_filter($filtrados, fn($r) => (string) $r['id_maquina'] === (string) $query['id_maquina']));
        }

        $page    = (int) ($query['page'] ?? 1);
        $perPage = (int) ($query['per_page'] ?? 10);

        return [
            'current_page' => $page,
            'last_page'    => 1,
            'per_page'     => $perPage,
            'total'        => count($filtrados),
            'data'         => $filtrados,
        ];
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

<?php

namespace App\Services;

use App\Support\ApiClient;

class ExtratoMaquinaService
{
    public static function criar($dados)
    {
        return ApiClient::post('/extratoMaquina', $dados)->json();
    }

    public static function coletar(string $id = null)
    {
        $path = is_null($id) ? '/extratoMaquina' : "/extratoMaquina/{$id}";
        return ApiClient::get($path)->json();
    }

    public static function coletarComPaginacao(array $filtros = [], int $page = 1)
    {
        $params = array_merge($filtros, ['page' => $page]);
        $response = ApiClient::get('/extratoMaquina', $params);

        if ($response->successful()) {
            return $response->json();
        }

        return [
            'data' => [],
            'current_page' => 1,
            'last_page' => 1,
        ];
    }

    public static function coletarRelatorioTotalTransacoes($dados, $id_cliente = null)
    {
        if (isset($id_cliente)) {
            $dados['id_cliente'] = [$id_cliente];
        }

        return ApiClient::post('/relatorioTotalTransacoes', $dados)->json();
    }

    public static function coletarRelatorioTotalTransacoesTotal($dados, $id_cliente = null)
    {
        if (isset($id_cliente)) {
            $dados['id_cliente'] = [$id_cliente];
        }

        return ApiClient::post('/relatorioTotalTransacoesTotal', $dados)->json();
    }

    public static function coletarRelatorioTotalTransacoesTaxa($dados, $id_cliente = null)
    {
        if (isset($id_cliente)) {
            $dados['id_cliente'] = [$id_cliente];
        }

        return ApiClient::post('/relatorioTotalTransacoesTaxa', $dados)->json();
    }

    public static function coletarExtratoDasMaquinasDeUmCliente($dados)
    {
        return ApiClient::post('/transacaoMaquinaCliente', $dados)->json();
    }

    public static function coletarTotalTransacaoDasMaquinasDeUmCliente($dados)
    {
        return ApiClient::post('/totalTransacaoMaquinaCliente', $dados)->json();
    }

    public static function coletarComFiltro($filtros, $tipo)
    {
        $response = ApiClient::get('/extratoMaquina');

        if (!$response->successful()) {
            return [];
        }

        $extrato_maquinas = $response->json();

        foreach ($filtros as $chave => $valor) {
            if ($valor !== null) {
                $extrato_maquinas = array_filter($extrato_maquinas, function ($extrato_maquina) use ($chave, $valor) {
                    return isset($extrato_maquina[$chave]) && $extrato_maquina[$chave] == $valor;
                });
            }
        }

        return $extrato_maquinas;
    }

    public function atualizar($dados, $id)
    {
        return ApiClient::post("/extratoMaquina/{$id}", $dados)->json();
    }

    public static function coletarSaldoTotal($id = null)
    {
        $path = is_null($id) ? '/extrato/saldo/' : "/extrato/saldo/{$id}";
        return ApiClient::get($path)->json();
    }

    public static function coletarDevolucoes($id = null)
    {
        $path = is_null($id) ? '/extrato/devolucao/' : "/extrato/devolucao/{$id}";
        return ApiClient::get($path)->json();
    }

    public static function coletarAcumulado(array $filtros = [])
    {
        $response = ApiClient::get('/extrato/acumulado', $filtros);

        if ($response->successful()) {
            return $response->json();
        }

        return ['data' => [], 'current_page' => 1, 'last_page' => 1];
    }

    public static function resetParcial(string $idMaquina, array $dados)
    {
        $response = ApiClient::post("/maquinas/{$idMaquina}/reset-parcial", $dados);

        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json()];
        }

        $body = $response->json();
        return [
            'success' => false,
            'message' => $body['message'] ?? 'Erro ao registrar o reset parcial.',
        ];
    }

    public static function historicoResets(array $filtros = [])
    {
        $response = ApiClient::get('/reset-parcial/historico', $filtros);

        if ($response->successful()) {
            return $response->json();
        }

        return ['data' => [], 'current_page' => 1, 'last_page' => 1, 'total' => 0];
    }
}

<?php

namespace App\Services;

use App\Support\ApiClient;

class ExtratoClienteService
{
    public static function criar($dados)
    {
        return ApiClient::post('/extratoCliente', $dados)->json();
    }

    public static function coletar(string $id = null)
    {
        $path = is_null($id) ? '/extratoCliente' : "/extratoCliente/{$id}";
        return ApiClient::get($path)->json();
    }

    public static function coletarComFiltro($filtros, $tipo)
    {
        $response = ApiClient::get('/extratoCliente');

        if (!$response->successful()) {
            return [];
        }

        $extrato_clientes = $response->json();

        foreach ($filtros as $chave => $valor) {
            if ($valor !== null) {
                $extrato_clientes = array_filter($extrato_clientes, function ($extrato_cliente) use ($chave, $valor) {
                    return isset($extrato_cliente[$chave]) && $extrato_cliente[$chave] == $valor;
                });
            }
        }

        return $extrato_clientes;
    }

    public function atualizar($dados, $id)
    {
        return ApiClient::post("/extratoCliente/{$id}", $dados)->json();
    }
}

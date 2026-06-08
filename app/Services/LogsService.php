<?php

namespace App\Services;

use App\Support\ApiClient;

class LogsService
{
    public static function criar($dados)
    {
        return ApiClient::post('/logs', $dados)->json();
    }

    public static function coletar(string $id = null)
    {
        $path = is_null($id) ? '/logs' : "/logs/{$id}";
        return ApiClient::get($path)->json();
    }

    public static function coletarComFiltro($filtros, $tipo)
    {
        $response = ApiClient::get('/logs');

        if (!$response->successful()) {
            return [];
        }

        $logs = $response->json();

        foreach ($filtros as $chave => $valor) {
            if ($valor !== null) {
                $logs = array_filter($logs, function ($log) use ($chave, $valor) {
                    return isset($log[$chave]) && $log[$chave] == $valor;
                });
            }
        }

        return $logs;
    }
}

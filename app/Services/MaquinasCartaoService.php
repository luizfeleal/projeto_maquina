<?php

namespace App\Services;

use App\Support\ApiClient;

class MaquinasCartaoService
{
    public static function criar($dados)
    {
        return ApiClient::post('/maquinasCartao', $dados);
    }

    public static function coletar(string $id = null)
    {
        $path = is_null($id) ? '/maquinasCartao' : "/maquinasCartao/{$id}";
        return ApiClient::get($path)->json();
    }

    public static function coletarComFiltro($filtros, $tipo)
    {
        $response = ApiClient::get('/maquinasCartao');

        if (!$response->successful()) {
            return [];
        }

        $maquinas = $response->json();

        foreach ($filtros as $chave => $valor) {
            if ($valor !== null) {
                $maquinas = array_filter($maquinas, function ($maquina) use ($chave, $valor) {
                    return isset($maquina[$chave]) && $maquina[$chave] == $valor;
                });
            }
        }

        return $maquinas;
    }

    public static function atualizar($dados)
    {
        return ApiClient::post('/maquinasCartaoAtualizar', $dados)->json();
    }

    public static function excluir($id)
    {
        $response = ApiClient::delete("/maquinasCartao/{$id}");

        if ($response->successful()) {
            $data = $response->json();
            return ['success' => true, 'message' => $data['message'] ?? 'Máquina de cartão excluída com sucesso.'];
        }

        return ['success' => false, 'message' => 'Erro ao excluir a máquina de cartão.'];
    }
}

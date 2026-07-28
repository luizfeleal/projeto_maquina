<?php

namespace App\Services;

use App\Support\ApiClient;

class MaquinasService
{
    public static function criar($dados)
    {
        return ApiClient::post('/maquinas', $dados);
    }

    public static function coletar(string $id = null)
    {
        $path = is_null($id) ? '/maquinas' : "/maquinas/{$id}";
        return ApiClient::get($path)->json();
    }

    public static function coletarComLixo()
    {
        return ApiClient::get('/maquinas', ['withTrash' => 'true'])->json();
    }

    public static function coletarComFiltro($filtros, $tipo)
    {
        $response = ApiClient::get('/maquinas');

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

    public static function atualizar($dados, $id)
    {
        $response = ApiClient::put("/maquinas/{$id}", $dados);

        if ($response->successful()) {
            return $response->json();
        }

        return response()->json([
            'error' => 'Houve um erro ao tentar atualizar a máquina',
            'status' => $response->status(),
            'message' => $response->body(),
        ], $response->status());
    }

    public static function deletar($id)
    {
        $response = ApiClient::delete("/maquinas/{$id}");

        if ($response->successful()) {
            return $response->json();
        }

        return response()->json([
            'error' => 'Failed to delete the resource.',
            'status' => $response->status(),
            'message' => $response->body(),
        ], $response->status());
    }

    public static function coletarPlacasDisponiveis()
    {
        return ApiClient::post('/hardware/maquinasDisponiveis', [], 1);
    }

    public static function coletarTodasAsMaquinasComUltimaTransacao()
    {
        return ApiClient::get('/totalMaquinas')->json();
    }
}

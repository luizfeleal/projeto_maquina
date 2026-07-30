<?php

namespace App\Services;

use App\Support\ApiClient;
use Illuminate\Support\Facades\Log;

class AcessosTelaService
{
    public static function criar($dados)
    {
        return ApiClient::post('/acessosTela', $dados)->json();
    }

    /**
     * @throws \RuntimeException quando a API não responde/retorna erro, para que o
     *         chamador consiga diferenciar "sem permissões" de "falha ao consultar".
     */
    public static function coletar(string $id = null)
    {
        try {
            $path = is_null($id) ? '/acessosTela' : "/acessosTela/{$id}";
            $response = ApiClient::get($path);
        } catch (\Throwable $e) {
            Log::error('Erro ao coletar acessos: ' . $e->getMessage());
            throw new \RuntimeException('Falha ao consultar acessos: ' . $e->getMessage(), 0, $e);
        }

        if (!$response->successful()) {
            Log::error('Falha ao buscar acessos', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('Falha ao buscar acessos: HTTP ' . $response->status());
        }

        $acessos = $response->json();
        return is_array($acessos) ? $acessos : [];
    }

    public static function coletarComFiltro($filtros, $tipo)
    {
        $response = ApiClient::get('/acessosTela');

        if (!$response->successful()) {
            return [];
        }

        $acessos = $response->json();

        foreach ($filtros as $chave => $valor) {
            if ($valor !== null) {
                $acessos = array_filter($acessos, function ($acesso) use ($chave, $valor) {
                    return isset($acesso[$chave]) && $acesso[$chave] == $valor;
                });
            }
        }

        return $acessos;
    }

    public function atualizar($dados, $id)
    {
        return ApiClient::post("/acessosTela/{$id}", $dados)->json();
    }
}


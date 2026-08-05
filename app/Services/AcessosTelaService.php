<?php

namespace App\Services;

use App\Support\ApiClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AcessosTelaService
{
    private const CACHE_KEY = 'acessos_tela_lista';
    private const CACHE_TTL_MINUTOS = 30;

    public static function criar($dados)
    {
        $resultado = ApiClient::post('/acessosTela', $dados)->json();
        Cache::forget(self::CACHE_KEY);
        return $resultado;
    }

    /**
     * @throws \RuntimeException quando a API não responde/retorna erro, para que o
     *         chamador consiga diferenciar "sem permissões" de "falha ao consultar".
     */
    public static function coletar(string $id = null)
    {
        // A lista completa é consultada pelo middleware ChecarPermissoes em TODA requisição
        // protegida, de TODOS os usuários. Como a API autentica com uma única credencial de
        // máquina, sem cache esse volume estoura o rate limit da API (429) rapidinho em uso
        // normal. Os dados mudam raramente, então um cache curto elimina a maior parte do
        // tráfego redundante sem atrasar a propagação de mudanças reais de permissão.
        if (is_null($id)) {
            return Cache::remember(self::CACHE_KEY, now()->addMinutes(self::CACHE_TTL_MINUTOS), function () {
                return self::buscar('/acessosTela');
            });
        }

        return self::buscar("/acessosTela/{$id}");
    }

    private static function buscar(string $path)
    {
        try {
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
        $resultado = ApiClient::post("/acessosTela/{$id}", $dados)->json();
        Cache::forget(self::CACHE_KEY);
        return $resultado;
    }
}


<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

/**
 * Cache curto para as listas de referência da API (clientes, locais,
 * cliente_local).
 *
 * Essas listas são buscadas em quase toda tela do painel — muitas vezes mais de
 * uma vez na mesma requisição — e cada busca é um HTTP para a API, que por sua
 * vez consulta um banco remoto. Como o conteúdo muda só quando alguém cadastra
 * ou edita um registro (e a própria escrita invalida o cache aqui), guardar por
 * alguns minutos elimina a maior parte dessas idas e voltas sem defasagem
 * perceptível.
 *
 * Só vale para listas estáveis: nada de cachear dados voláteis como o status
 * online/offline das máquinas ou valores de extrato.
 */
trait CacheReferencia
{
    private static function ttlCacheReferencia(): int
    {
        return (int) env('CACHE_REFERENCIA_SEGUNDOS', 300);
    }

    private static function chaveCacheReferencia(): string
    {
        return 'ref_' . str_replace('\\', '_', static::class);
    }

    /**
     * Executa $callback no máximo uma vez por janela de cache.
     */
    protected static function lembrarReferencia(callable $callback)
    {
        $ttl = self::ttlCacheReferencia();

        if ($ttl <= 0) {
            return $callback();
        }

        return Cache::remember(self::chaveCacheReferencia(), now()->addSeconds($ttl), $callback);
    }

    /**
     * Invalida o cache. Chamado por toda escrita do serviço para que a alteração
     * apareça na tela seguinte, sem esperar o TTL.
     */
    public static function esquecerCacheReferencia(): void
    {
        Cache::forget(self::chaveCacheReferencia());
    }
}

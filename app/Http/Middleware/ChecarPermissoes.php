<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Importe a classe Log para registrar mensagens de log
use App\Services\UsuariosService;
use App\Services\GruposAcessoService;
use App\Services\AcessosTelaService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
class ChecarPermissoes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        Log::info('Middleware ChecarPermissoes está sendo executado.');

        if (!session()->has('id_usuario')) {
            return redirect()->route('login-view');
        }

        $acessos = AcessosTelaService::coletar();

        if (!is_array($acessos) || empty($acessos)) {
            Log::error('AcessosTelaService retornou vazio ou inválido', [
                'tipo' => gettype($acessos),
                'total' => is_array($acessos) ? count($acessos) : null,
            ]);

            session()->flush();

            return redirect()->route('login-view')
                ->with('error', 'Erro ao carregar permissões. Tente fazer login novamente.');
        }

        $routeName = $request->route()->getName();
        if ($routeName === 'maquinas-transacoes-dados') {
            $routeName = 'maquinas-transacoes';
        }
        if ($routeName === 'maquinas-dados') {
            $routeName = 'maquinas';
        }

        $acesso = array_filter($acessos, function ($item) use ($routeName) {
            return isset($item['id_grupo_acesso'])
                && isset($item['acesso_tela_viewname'])
                && $item['id_grupo_acesso'] == session()->get('id_grupo_acesso')
                && $item['acesso_tela_viewname'] == $routeName;
        });

        Log::info('Verificação de Acesso', [
            'rota' => $request->route()->getName(),
            'grupo' => session()->get('id_grupo_acesso'),
            'total_acessos' => count($acessos),
            'encontrou' => !empty($acesso),
        ]);

        if (empty($acesso)) {
            Log::warning('Acesso negado', [
                'usuario' => session()->get('usuario_nome'),
                'grupo' => session()->get('id_grupo_acesso'),
                'rota' => $request->route()->getName(),
            ]);

            $homeRoute = session('grupo_nome') === 'admin' ? 'home' : 'cliente-home';

            if ($routeName === $homeRoute) {
                session()->flush();

                return redirect()->route('login-view')
                    ->with('error', 'O usuário não possui permissão de acesso.');
            }

            return redirect()->route($homeRoute)
                ->with('error', 'O usuário não possui permissão de acesso.');
        }

        return $next($request);
    }
}

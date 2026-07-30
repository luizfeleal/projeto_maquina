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
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Sessão expirada. Faça login novamente.'], 401);
            }

            return redirect()->route('login-view');
        }

        try {
            $acessos = AcessosTelaService::coletar();
        } catch (\Throwable $e) {
            // Falha ao consultar a API de permissões (rede, token interno expirado, API fora do ar, etc.)
            // é um problema transitório de infraestrutura, não um problema com a sessão do usuário.
            // Preservamos a sessão para que o usuário não seja deslogado por uma instabilidade momentânea.
            // Importante: não usar status 401 aqui, pois o front-end (ex.: DataTables) trata 401
            // como "sessão expirada" e força um redirect para o login.
            Log::error('Não foi possível verificar permissões, mantendo sessão do usuário', [
                'erro' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Não foi possível verificar suas permissões no momento. Tente novamente.'], 503);
            }

            return back()->with('error', 'Não foi possível verificar suas permissões no momento. Tente novamente em instantes.');
        }

        if (empty($acessos)) {
            Log::warning('AcessosTelaService retornou lista vazia de permissões', [
                'usuario' => session()->get('usuario_nome'),
                'grupo' => session()->get('id_grupo_acesso'),
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Nenhuma permissão de acesso encontrada.'], 503);
            }

            return back()->with('error', 'Nenhuma permissão de acesso encontrada. Contate o administrador.');
        }

        $routeName = $request->route()->getName();
        if ($routeName === 'maquinas-transacoes-dados') {
            $routeName = 'maquinas-transacoes';
        }
        if ($routeName === 'maquinas-dados') {
            $routeName = 'maquinas';
        }
        if ($routeName === 'clientes-maquinas-transacoes-dados') {
            $routeName = 'clientes-maquinas-transacoes';
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

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Usuário não possui permissão de acesso.'], 403);
            }

            $homeRoute = resolveHomeRouteForGrupo(session('grupo_nome'));

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

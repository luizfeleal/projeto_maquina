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
            // Importante: não usar status 401 aqui (o front-end trata 401 como sessão expirada) nem
            // back()/redirect (se a falha for persistente, isso gera um loop de redirecionamento entre
            // a página atual e a anterior). Por isso respondemos direto, sem redirecionar.
            Log::error('Não foi possível verificar permissões, mantendo sessão do usuário', [
                'erro' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Não foi possível verificar suas permissões no momento. Tente novamente.'], 503);
            }

            return response()->view('errors.permissao-indisponivel', [
                'mensagem' => 'Não foi possível verificar suas permissões no momento. Sua sessão continua ativa, tente novamente em instantes.',
            ], 503);
        }

        if (empty($acessos)) {
            Log::warning('AcessosTelaService retornou lista vazia de permissões', [
                'usuario' => session()->get('usuario_nome'),
                'grupo' => session()->get('id_grupo_acesso'),
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Nenhuma permissão de acesso encontrada.'], 503);
            }

            return response()->view('errors.permissao-indisponivel', [
                'mensagem' => 'Nenhuma permissão de acesso encontrada para o seu usuário. Contate o administrador.',
            ], 503);
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

        // Sem log de sucesso: este middleware roda em toda requisição protegida,
        // então logar cada verificação bem-sucedida só engorda o arquivo de log.
        // Negativas e falhas continuam registradas abaixo.
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

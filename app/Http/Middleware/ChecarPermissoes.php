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

        if(empty(session()->has('id_usuario'))){
            return redirect()->route('login-view');
        }else{
            $acessos = AcessosTelaService::coletar();

            // Verificar se acessos é válido
            if(!is_array($acessos)){
                Log::error('AcessosTelaService retornou null ou não é array', [
                    'tipo' => gettype($acessos),
                    'valor' => $acessos
                ]);
                return back()->with('error', 'Erro ao carregar permissões. Tente fazer login novamente.');
            }

            $acesso = array_filter($acessos, function($item) use($request){
                return isset($item['id_grupo_acesso']) 
                    && isset($item['acesso_tela_viewname'])
                    && $item['id_grupo_acesso'] == session()->get('id_grupo_acesso') 
                    && $item['acesso_tela_viewname'] == $request->route()->getName();
            });

            Log::info('Verificação de Acesso', [
                'rota' => $request->route()->getName(),
                'grupo' => session()->get('id_grupo_acesso'),
                'total_acessos' => count($acessos),
                'encontrou' => !empty($acesso)
            ]);
            
            if(empty($acesso)){
                Log::warning('Acesso negado', [
                    'usuario' => session()->get('usuario_nome'),
                    'grupo' => session()->get('id_grupo_acesso'),
                    'rota' => $request->route()->getName()
                ]);
                return back()->with('error', 'O usuário não possui permissão de acesso.');
            }
            
        }

        return $next($request);
    }
}

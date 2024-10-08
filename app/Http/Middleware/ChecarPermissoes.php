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

            $acesso = array_filter($acessos, function($item) use($request){
                return $item['id_grupo_acesso'] == session()->get('id_grupo_acesso') && $item['acesso_tela_viewname'] == $request->route()->getName() && $item['ativo'] == 1;
            });

            if(empty($acesso)){
                return back()->with('error', 'O usuário não possui permissão de acesso.');
            }
            
        }

        return $next($request);
    }
}

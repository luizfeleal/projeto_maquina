<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\AuthService;

class LoginController extends Controller
{
    private function autenticar($usuarioLogin, $senhaLogin, $token){

        if(isset($usuarioLogin) && isset($senhaLogin)){
            $url = env('APP_URL_API') . '/usuarios';
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->get($url);

            $usuarios = $response->json();
            $usuarioArray = array_filter($usuarios, function ($usuario) use ($usuarioLogin, $senhaLogin) {
                return $usuario['usuario_login'] == $usuarioLogin && $usuario['usuario_senha'] == $senhaLogin;
            });
            if (empty($usuarioArray)) {
                
                return null; 
            } else {
                
                return $usuarioArray[0];
            }
        }
    }
    public function logout(){
        session()->flush();
        return redirect()->route('login-view');
    }
    public function login(){
        if(session('id_cliente') && session('id_grupo_acesso')){
            return redirect()->route('dashboard');
        }
        
        return view('Login');
    }

    public function autenticarUsuario(Request $request){
        $token = AuthService::getToken();

        $response = $this->autenticar($request->input('usuario'), $request->input('senha'), $token);
        if(isset($response)){
            $request->session()->put([
                'id_cliente' => $response['id_cliente'],
                'id_grupo_acesso' => $response['id_grupo_acesso'],
                'usuario_nome' => $response['usuario_nome']
            ]);

            return redirect()->route('dashboard');

        }else{
            
            return back()->with(["error", "Nenhum usuário foi encotrado! Email ou senha incorreto(s)"]);
        }
    }

    
}

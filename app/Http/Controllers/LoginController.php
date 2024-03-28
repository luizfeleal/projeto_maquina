<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\AuthService;
use App\Services\EmailService;


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

        return view('Login.Login');
    }

    public function autenticarUsuario(Request $request){
        $token = AuthService::getToken();

        $response = $this->autenticar($request->input('usuario'), $request->input('senha'), $token);
        if(isset($response)){
        session([
                'id_cliente' => $response['id_cliente'],
                'id_grupo_acesso' => $response['id_grupo_acesso'],
                'usuario_nome' => $response['usuario_nome']
            ]);

            return redirect()->route('maquinas');

        }else{

            return back()->with("error", "Nenhum usuário foi encotrado! Email ou senha incorreto(s)");
        }
    }

    public function redefinir(){
        return view('Login.Redefinir.Redefinir');
    }

    public function enviarEmailRedefinir(Request $request){
        try{
            $email = $request['usuario_email'];

            $token = AuthService::getToken();
            //$usuario = UsuariosService::coletarComFiltro(['usuario_email'=>$email], 'where');
            $url = env('APP_URL_API') . '/usuarios';
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->get($url);

            $usuarios = $response->json();
            $usuarioArray = array_filter($usuarios, function ($usuario) use ($email) {
                return $usuario['usuario_email'] == $email;
            });

            if(empty($usuarioArray)){
                return back()->with('error', 'Não foi encontrado nenhum usuário com o email informado.');
            }
            $id_usuario = $usuarioArray[0]['id_usuario'];

            EmailService::enviarEmailGenerico($email, 'Redefinição de senha', 'Mail.redefineSenha', ['link'=>env('APP_URL') . "/login/senha/alterar?id=" . urldecode($id_usuario). "&token=" . urldecode(hash('sha256', $email))]);

            return back()->with('success', 'Email de redefinição de senha enviado com sucesso. Acesse seu email e clique no link para poder redefinir sua senha.');
         }catch(\Throwable $e){
            return back()->with('error', 'Houve um erro inesperado ao tentar enviar o email de redefinição de senha.');
         }
    }

    public function novaSenhaView(Request $request){
        $id_usuario = $request->id;
        $tokenEmail = $request->token;
        if(isset($tokenEmail)){

            $token = AuthService::getToken();
            //$usuario = UsuariosService::coletarComFiltro(['usuario_email'=>$email], 'where');
            $url = env('APP_URL_API') . "/usuarios/$id_usuario";
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->get($url);

            $usuarios = $response->json();

            //return hash('sha256', $usuarios['usuario_email']) == $tokenEmail;
           if(!empty($usuarios)){
              if(hash('sha256', $usuarios['usuario_email']) == $tokenEmail){
                 return view('Login.Redefinir.CriarSenha', compact('id_usuario', 'tokenEmail'));
              }
           }
        }else{

            return redirect()->route('login-view');
        }
    }

    public function registrarSenha(Request $request){
        try{
            $id_usuario = $request['id_usuario'];
            $senha = $request['usuario_senha'];
            $tokenEmail = $request['token'];
            if($request['usuario_senha'] == $request['usuario_confirmacao_senha']){
                $token = AuthService::getToken();
                //$usuario = UsuariosService::coletarComFiltro(['usuario_email'=>$email], 'where');
                $url = env('APP_URL_API') . "/usuarios/$id_usuario";
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $token
                ])->get($url);

                $usuarios = $response->json();


               if(!empty($usuarios) && hash('sha256', $usuarios['usuario_email']) == $request['token']){
                  $dados = [];
                  $dados['usuario_senha'] = $senha;
                  $url = env('APP_URL_API') . "/usuarios/$id_usuario";
                  $response = Http::withHeaders([
                      'Authorization' => 'Bearer ' . $token
                  ])->put($url, $dados);
                  return back()->with('success', 'Senha alterada com sucesso!');
               }else{
                  return back()->with('error', "Não foi possível redefinir a senha para esse usuário.");
               }
            }else{
            return back()->with('error', "Houve um erro inesperado ao tentar redefinir a senha");
            }
         }catch(Exception $e){
            return back()->with('error', "Houve um erro inesperado ao tentar redefinir a senha");
         }
    }


}

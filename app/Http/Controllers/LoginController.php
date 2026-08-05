<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\ApiClient;
use App\Services\AuthService;
use App\Services\EmailService;
use App\Services\UsuariosService;
use App\Services\GruposAcessoService;


class LoginController extends Controller
{
    
    public function autenticar(Request $request)
   {
      
      $usuario_request = $request['usuario'];
      $senha = $request['senha'];

      if(!empty($usuario_request) && !empty($senha)){
          $usuarios = UsuariosService::buscarPorLoginOuEmail($usuario_request);
          $usuario = array_values(array_filter($usuarios, function ($item) use ($usuario_request, $senha) {
              $loginOuEmail = ($item['usuario_login'] ?? '') === $usuario_request
                  || ($item['usuario_email'] ?? '') === $usuario_request;

              return $loginOuEmail && ($item['usuario_senha'] ?? '') === $senha;
          }));
          if(empty($usuario)){

            return back()->with('error', 'Nenhum usuário com os respectivos dados informados foi encontrado.');
         }else if($usuario[0]['ativo'] == 0){
            return back()->with('error', 'O usuário com os respectivos dados de acesso se encontra inativo.');
         }else{
            $grupo_acesso = GruposAcessoService::coletar($usuario[0]['id_grupo_acesso']);

            if(!isset($grupo_acesso['grupo_acesso_nome'])){
                return back()->with('error', 'Não foi encontrado um grupo de acesso para o usuário informado.');
            }

             $grupoNome = normalizeGrupoNome($grupo_acesso['grupo_acesso_nome'] ?? '');

             session([
                 'id_usuario' => $usuario[0]['id_usuario'],
                 'id_grupo_acesso' => $usuario[0]['id_grupo_acesso'],
                 'usuario_nome' => $usuario[0]['usuario_nome'],
                 'grupo_nome' => $grupoNome,
                 'id_cliente' => $usuario[0]['id_cliente'],
             ]);

             return redirect()->route(resolveHomeRouteForGrupo($grupoNome));
         }
      }
      return back()->with('error', 'Os dados de Usuário e Senha são obrigatórios para o login.');

   }

    public function logout(){
        session()->flush();
        return redirect()->route('login-view');
    }
    public function login(){
        if(session()->has('id_usuario') && session()->has('id_grupo_acesso')){
            return redirect()->route(resolveHomeRouteForGrupo(session('grupo_nome')));
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

            AuthService::getToken();
            $usuarios = ApiClient::get('/usuarios')->json();

            $usuarioArray = array_filter($usuarios, function ($usuario) use ($email) {
                return $usuario['usuario_email'] == $email;
            });

            if(empty($usuarioArray)){
                return back()->with('error', 'Não foi encontrado nenhum usuário com o email informado.');
            }
            $usuarioArray = array_values($usuarioArray);
            $id_usuario = $usuarioArray[0]['id_usuario'];

            EmailService::enviarEmailGenerico($email, 'Redefinição de senha', 'Mail.redefineSenha', ['link'=>env('APP_URL') . "/login/senha/alterar?id=" . urldecode($id_usuario). "&token=" . urldecode(hash('sha256', $email))]);

            return back()->with('success', 'Email de redefinição de senha enviado com sucesso. Acesse seu email e clique no link para poder redefinir sua senha.');
         }catch(\Throwable $e){
            return $e;
            return back()->with('error', 'Houve um erro inesperado ao tentar enviar o email de redefinição de senha.');
         }
    }

    public function novaSenhaView(Request $request){
        $id_usuario = $request->id;
        $tokenEmail = $request->token;
        if(isset($tokenEmail)){

            AuthService::getToken();
            $usuarios = ApiClient::get("/usuarios/{$id_usuario}")->json();

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
                AuthService::getToken();
                $usuarios = ApiClient::get("/usuarios/{$id_usuario}")->json();

               if(!empty($usuarios) && hash('sha256', $usuarios['usuario_email']) == $request['token']){
                  $dados = ['usuario_senha' => $senha];
                  ApiClient::put("/usuarios/{$id_usuario}", $dados);
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

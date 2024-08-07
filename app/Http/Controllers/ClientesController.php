<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LocaisService;
use App\Services\MaquinasService;
use App\Services\ExtratoMaquinaService;
use App\Services\ClientesService;
use App\Services\UsuariosService;
use App\Services\CredApiPixService;
use App\Services\GruposAcessoService;
use App\Services\AuthService;

class ClientesController extends Controller
{
    public function coletarClientePorId(Request $request){

        if($request->has('id')){
            $clientes = ClientesService::coletar($request->id);
            return view('Usuarios.index', compact('clientes'));
        }else{
            return back()->with('error', 'Cliente não encontrada');
        }

    }

    public function criarCliente(Request $request){
        $grupos = GruposAcessoService::coletar();
        $clientes = ClientesService::coletar();

        return view('Usuarios.create', compact('grupos', 'clientes'));
    }
    public function registrarCliente(Request $request){
        
    
        $dadosCliente = $request->except(['cliente_senha', 'cliente_confirmar_senha', 'cliente_id', 'cliente_secret', 'cliente_certificado']);

        $cliente = ClientesService::criar($dadosCliente);
        
        if($cliente['success']){

            //Cadastrar credenciais
            $id_cliente = $cliente['data']['response']['id_cliente'];
    
            $dadosCredApiPix = [
                "id_cliente" => $id_cliente,
                "client_secret" => $request['cliente_secret'],
                "client_id" => $request['cliente_id'],
                "caminho_certificado" => $request->file('cliente_certificado')
            ];

            
            $credApi = CredApiPixService::criar($dadosCredApiPix);
    
            
            //Criar acesso a plataforma
            $dadoUsuarioAcesso = [
                "id_cliente" => $id_cliente,
                "id_grupo_acesso" => 2,
                "usuario_nome" => $request['cliente_nome'],
                "usuario_email" => $request['cliente_email'],
                "usuario_login" => $request['cliente_email'],
                "usuario_senha" => $request['cliente_senha'],
                "ativo" => 1
            ];

            $usuarioAcesso = UsuariosService::criar($dadoUsuarioAcesso);
            return back()->with('success', 'Cliente cadastrado com sucesso!');
        }

        return $cliente;
    }

    /*public function coletarTodasAsMaquinasPorCliente(){

    }*/

    public function gerarIdPlaca(){
        $id_aleatorio = rand(10000000, 99999999);
        $maquinas = MaquinasService::coletarComFiltro(['id_placa' => $id_aleatorio], 'where');
    
        if(empty($maquinas)){
            return response()->json(["id_placa"=>$id_aleatorio], 200); // Correção aqui
        }else{
            return response()->json($maquinas, 200); // Correção aqui
        }
    }

    public function coletarCliente(Request $request){
        $clientes = ClientesService::coletar();
        
        return view('Usuarios.index', compact('clientes'));
    }

    public function transacaoMaquinas(Request $request){
        $maquinas_extrato = ExtratoMaquinaService::coletar();
        return view('Maquinas.Transacoes.Index', compact('maquinas_extrato'));
    }

    public function acumuladoMaquinas(Request $request){
        $maquinas_extrato = ExtratoMaquinaService::coletar();
        return view('Maquinas.Acumulado.Index', compact('maquinas_extrato'));

    }
}

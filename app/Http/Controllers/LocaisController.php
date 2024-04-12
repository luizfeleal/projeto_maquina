<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LocaisService;
use App\Services\MaquinasService;
use App\Services\ExtratoMaquinaService;
use App\Services\ClientesService;
use App\Services\AuthService;

class LocaisController extends Controller
{
    
    public function criarLocais(Request $request){
        $clientes = ClientesService::coletar();

        return view('Local.create', compact('clientes'));
    }
    public function registrarLocais(Request $request){

        try{
            $clientes = $request['select-cliente'];
            $dados = [];
            $dados['local_nome'] = $request['nome_local'];
            foreach($clientes as $cliente){
                $dados['id_cliente'] = $cliente;
                LocaisService::criar($dados);
            }
    
            return back()->with('success', 'Local cadastrado com sucesso!');
        }catch(\Throwable $e){
            return $e;
            return back()->with('error', 'Houve um erro ao tentar cadastrar o local');
        }
    }

    public function coletarLocais(Request $request){

        if($request->has('id')){
            $locais = LocaisService::coletar($request->id);
        }else{
            $locais = LocaisService::coletar();
            $clientes = ClientesService::coletar();
            $maquinas = MaquinasService::coletar();
            $extrato_maquina = ExtratoMaquinaService::coletar();

            foreach($locais as &$local){
                $clientesNomes = '';
                foreach($clientes as $cliente){
                    if($local['id_cliente'] == $cliente['id_cliente']){
                        $clientesNomes =  $cliente['cliente_nome'] . ' ';
                        $local['cliente_nome'] = $clientesNomes;
                    }
                }
                foreach($maquinas as $maquina){
                    if($maquina['id_local'] == $local['id_local']){
                        $local['maquina_nome'] = $maquina['maquina_nome'];
                        $local['maquina_status'] = $maquina['maquina_status'];
                    }
                }
            }

            
            return view('Local.index', compact('locais', 'extrato_maquina', 'maquinas', 'clientes'));
        }

    }

    public function incluirUsuarioLocal(){
        $locais = LocaisService::coletar();
        $clientes = ClientesService::coletar();

        return view('Local.Usuarios.create', compact('locais', 'clientes'));
    }
}

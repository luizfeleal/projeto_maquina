<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LocaisService;
use App\Services\MaquinasService;
use App\Services\ExtratoMaquinaService;
use App\Services\ClientesService;
use App\Services\ClienteLocalService;
use App\Services\AuthService;

class LocaisController extends Controller
{
    
    public function criarLocais(Request $request){
        $clientes = ClientesService::coletar();

        return view('Admin.Local.create', compact('clientes'));
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
            $clienteLocal = collect(ClienteLocalService::coletar())->keyBy('id_local');
            $maquinas = MaquinasService::coletar();
            $extrato_maquina = ExtratoMaquinaService::coletar();


            foreach($locais as &$local){
                $clientesNomes = '';
                foreach($clientes as $cliente){
                    if($clienteLocal[$local['id_local']]['id_cliente'] == $cliente['id_cliente']){
                        $clientesNomes =  $cliente['cliente_nome'] . ' ';
                        $local['cliente_nome'] = $clientesNomes;
                    }
                }
                foreach($maquinas as $maquina){
                    if($maquina['id_local'] == $local['id_local']){
                        $local['maquina_nome'] = $maquina['maquina_nome'];
                        $local['maquina_status'] = $maquina['maquina_status'];
                    }else{
                        $local['maquina_nome'] = "";
                        $local['maquina_status'] = "";
                    }
                }
            }

            
            return view('Admin.Local.index', compact('locais', 'extrato_maquina', 'maquinas', 'clientes'));
        }

    }

    public function incluirUsuarioLocal(){
        $locais = LocaisService::coletar();
        $clientes = ClientesService::coletar();

        return view('Admin.Local.Usuarios.create', compact('locais', 'clientes'));
    }

    public function excluirLocais(Request $request){
        try{

             $id_local = $request['id_local'];
             $maquinas = MaquinasService::coletarComFiltro(["id_local"=>$id_local],'where');

             if(!empty($maquinas)){
                return back()->with('error', 'O local não pôde ser removido pois há máquina(s) associada(s) à ele.');
             }
 
             $result = LocaisService::deletar($id_local);
             return back()->with('success', $result['message']);
         }catch(\Throwable $e){
             return back()->with('error', 'Houve um erro ao tentar remover o local');
         }
    }
}

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
            $local = LocaisService::criar($dados);
            $id_local = $local['response']['id_local'];
            foreach($clientes as $cliente){
                $dadosClienteLocal = [];
                $dadosClienteLocal['id_cliente'] = $cliente;
                $dadosClienteLocal['id_local'] = $id_local;
                ClienteLocalService::criar($dadosClienteLocal);

            }

            return back()->with('success', 'Local cadastrado com sucesso!');
        }catch(\Throwable $e){
            return back()->with('error', 'Houve um erro ao tentar cadastrar o local');
        }
    }

    public function coletarLocaisPorId($id){
        $local = LocaisService::coletar($id);

        if(empty($local)){
            return back()->with('error', 'Local não encontrado!');
        }
        $clienteLocal = ClienteLocalService::coletar();
        $clientes= ClientesService::coletar();
        $maquinas = MaquinasService::coletar();
        
        $maquinasFiltradas = array_filter($maquinas, function($item) use($id){
            return $item['id_local'] == $id;
        });

        $clienteLocalFiltrado = array_filter($clienteLocal, function($item) use($id){
            return $item['id_local'] == $id;
        });




        // Extraindo apenas os valores de "id_cliente"
        $idClientes = array_map(function($item) {
            return $item['id_cliente'];
        }, $clienteLocalFiltrado);

        $clienteFiltrado = array_filter($clientes, function($item) use($idClientes){
            return in_array($item['id_cliente'],  $idClientes);
        });


        $maquinas_extrato = ExtratoMaquinaService::coletar();

        foreach($maquinasFiltradas as &$maquina){
            $total_pix = 0;
            $total_cartao = 0;
            $total_dinheiro = 0;
            $total_maquina = 0;

            $extrato_por_maquina = array_filter($maquinas_extrato, function($item) use($maquina){
                return $item['id_maquina'] == $maquina['id_maquina'];
            });



            foreach($extrato_por_maquina as $em){
                $total_maquina += $em['extrato_operacao_valor'];
                if($em['extrato_operacao_tipo'] == "PIX"){
                    $total_pix += $em['extrato_operacao_valor'];
                } else if($em['extrato_operacao_tipo'] == "Cartão"){
                    $total_cartao += $em['extrato_operacao_valor'];
                }else if($em['extrato_operacao_tipo'] == "Dinheiro"){
                    $total_dinheiro += $em['extrato_operacao_valor'];
                }
            }
            $maquina['total_pix'] = $total_pix;
            $maquina['total_cartao'] = $total_cartao;
            $maquina['total_dinheiro'] = $total_dinheiro;
            $maquina['total_maquina'] = $total_maquina;
            $maquina['local_nome'] = $local['local_nome'];

        }


        return view('Admin.Local.show', compact('clienteFiltrado', 'maquinasFiltradas', 'local'));
    }

    public function coletarLocais(Request $request){
        if($request->has('id')){
            $locais = LocaisService::coletar($request->id);
        }else{
            $locais = LocaisService::coletar();
            $clientes = ClientesService::coletar();
            $clientesPorId = collect($clientes)->keyBy('id_cliente')->toArray();
            $clienteLocal = collect(ClienteLocalService::coletar())->keyBy('id_local')->toArray();
            $maquinas = MaquinasService::coletar();
            $maquinas_extrato = ExtratoMaquinaService::coletar();
        
	                
            $locais_indexados = [];
        foreach ($locais as &$local) {
            
            $id_local = $local['id_local'];
            $clienteId = $clienteLocal[$local['id_local']]['id_cliente'] ?? null;
            $nome_local = isset($clientesPorId[$clienteId]['cliente_nome']) ? $clientesPorId[$clienteId]['cliente_nome'] : '';
            $local['cliente_nome'] = $nome_local;
            $maquinasDoLocal = array_filter($maquinas, function($item) use($id_local){
                return $item['id_local'] == $id_local;
            });
            $local['qtde_maquinas'] = count($maquinasDoLocal);
            $locais_indexados[$local['id_local']] = $local;
        }

            
            
        return view('Admin.Local.index', compact( 'locais', 'clientes', 'maquinas'));
        }

    }

    public function incluirUsuarioLocal(){
        $locais = LocaisService::coletar();
        $clientes = ClientesService::coletar();

        return view('Admin.Local.Usuarios.create', compact('locais', 'clientes'));
    }

    public function registrarUsuarioLocal(Request $request){
        $clientes = $request['select-cliente'];

        $local = $request['select-local'];


        foreach($clientes as $cliente){
            $localCliente = ClienteLocalService::coletar();

            $localEncontrado = array_filter($localCliente, function($item) use($cliente, $local){
                return $item['id_cliente'] == $cliente && $item['id_local'] == $local;
            });

            
            if(empty($localEncontrado)){

                ClienteLocalService::criar(["id_cliente" => $cliente, "id_local"=>$local]);
            }
            //}
        }

        return back()->with("success", "Cliente(s) incluso com sucesso!");
    }

    public function excluirLocais(Request $request){
        try{

             $id_local = $request['id_local'];
             $maquinas = MaquinasService::coletarComFiltro(["id_local"=>$id_local],'where');

             if(!empty($maquinas)){
                return back()->with('error', 'O local não pôde ser removido pois há máquina(s) associada(s) à ele.');
             }
 
             $result = LocaisService::deletar($id_local);
             $clienteLocalService = ClienteLocalService::coletar();
             $clienteLocalService = array_filter($clienteLocalService, function($item) use($id_local){
                return $item['id_local'] == $id_local;
             });
             foreach($clienteLocalService as $associacao){
                 ClienteLocalService::deletar($associacao['id_cliente_local']);
             }
             return back()->with('success', $result['message']);
         }catch(\Throwable $e){
             return back()->with('error', 'Houve um erro ao tentar remover o local');
         }
    }
}

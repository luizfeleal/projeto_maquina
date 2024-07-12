<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LocaisService;
use App\Services\MaquinasService;
use App\Services\ExtratoMaquinaService;
use App\Services\ClientesService;
use App\Services\AuthService;

class MaquinasController extends Controller
{
    public function coletarMaquinaPorId(Request $request){

        if($request->has('id')){
            $maquinas = MaquinasService::coletarMaquinas($request->id);
            return view('Maquinas.index', compact('maquinas'));
        }else{
            return back()->with('error', 'Máquina não encontrada');
        }

    }

    public function criarMaquinas(Request $request){
        $locais = LocaisService::coletar();
        $clientes = ClientesService::coletar();

        return view('Maquinas.create', compact('locais', 'clientes'));
    }

    public function registrarMaquinas(Request $request){


        try{

            $dados = [];
            $dados['id_local'] = $request['select-local'];
            $dados['id_placa'] = $request['id_placa_input'];
            $dados['maquina_nome'] = $request['maquina_nome'];
            $dados['maquina_status'] = 0;

            $result = MaquinasService::criar($dados);

    
            return back()->with('success', $result['message']);
        }catch(\Throwable $e){
            return back()->with('error', 'Houve um erro ao tentar cadastrar o local');
        }
    }

    public function gerarIdPlaca(){
        $id_aleatorio = rand(10000000, 99999999);
        $maquinas = MaquinasService::coletarComFiltro(['id_placa' => $id_aleatorio], 'where');
    
        if(empty($maquinas)){
            return response()->json(["id_placa"=>$id_aleatorio], 200); // Correção aqui
        }else{
            return response()->json($maquinas, 200); // Correção aqui
        }
    }

    public function coletarTodasAsMaquinas(Request $request){
        $maquinas = MaquinasService::coletar();
        return view('Maquinas.index', compact('maquinas'));
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

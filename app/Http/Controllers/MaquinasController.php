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

    public function coletarTodasAsMaquinas(Request $request){
        $maquinas = MaquinasService::coletarMaquinas();
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

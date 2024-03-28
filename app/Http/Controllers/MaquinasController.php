<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MaquinasService;
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

    public function criarMaquina(Request $request){
        $dadosMaquina = [];
        $dados['nome_maquina'] = $request['nome_maquina'];
        $dadosMaquina['id_placa'] = $request['id_placa'];

    }

    /*public function coletarTodasAsMaquinasPorCliente(){

    }*/

    public function coletarTodasAsMaquinas(Request $request){
        $maquinas = MaquinasService::coletarMaquinas();
        return view('Maquinas.index', compact('maquinas'));
    }
}

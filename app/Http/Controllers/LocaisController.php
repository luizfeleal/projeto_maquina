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

        return view('Local.Usuarios.index', compact('clientes'));
    }
    public function registrarLocais(Request $request){
        return $request;

        return view('Local.Usuarios.index', compact('clientes'));
    }

    public function coletarLocais(Request $request){

        if($request->has('id')){
            $locais = LocaisService::coletar($request->id);
            //return view('Locais.index', compact('locais'));
        }else{
            $locais = LocaisService::coletar();
            $clientes = ClientesService::coletar();
            $maquina = MaquinasService::coletar();
            $extrato_maquina = ExtratoMaquinaService::coletar();


            return view('Local.index');
        }

    }
}

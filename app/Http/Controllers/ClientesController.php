<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LocaisService;
use App\Services\MaquinasService;
use App\Services\ExtratoMaquinaService;
use App\Services\ClientesService;
use App\Services\GruposAcessoService;
use App\Services\UsuariosService;
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
        $dadosCliente = [];
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

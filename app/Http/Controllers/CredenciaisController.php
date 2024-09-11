<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LocaisService;
use App\Services\MaquinasService;
use App\Services\Hardware\MaquinasService as HardwareMaquinas;
use App\Services\ExtratoMaquinaService;
use App\Services\LiberarJogadaService;
use App\Services\CredApiPixService;
use App\Services\ClientesService;
use App\Services\AuthService;

class CredenciaisController extends Controller
{

    public function criarCredencial(Request $request){
        $clientes = ClientesService::coletar();
        return view('Admin.Credenciais.create', compact('clientes'));
    }

    public function registrarCredencial(Request $request){


        //try{

            $dados = [];
            $dados['id_cliente'] = $request['select-cliente'];
            $dados['client_id'] = $request['cliente_id'];
            $dados['client_secret'] = $request['cliente_secret'];
            $dados['caminho_certificado'] = $request['cliente_certificado'];

            $result = CredApiPixService::criar($dados);

            return back()->with('success', $result['message']);
        //}catch(\Throwable $e){
            return back()->with('error', 'Houve um erro ao tentar cadastrar a credencial');
        //}
    }

  
}

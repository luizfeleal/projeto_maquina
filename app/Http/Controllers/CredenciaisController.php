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

    public function criarCredencialEfi(Request $request){
        $clientes = ClientesService::coletar();
        return view('Admin.Credenciais.EFI.create', compact('clientes'));
    }
    public function criarCredencialPagbank(Request $request){
        $clientes = ClientesService::coletar();
        return view('Admin.Credenciais.PagBank.create', compact('clientes'));
    }

    public function registrarCredencial(Request $request){


        //try{

            $dados = [];
            $dados['id_cliente'] = $request['select-cliente'];
            $dados['client_id'] = $request['cliente_id'];
            $dados['client_secret'] = $request['cliente_secret'];
            $dados['tipo_cred'] = $request['tipo_cred'];
            if($request['tipo_cred'] == "efi"){
                $dados['caminho_certificado'] = $request['cliente_certificado'];
            }

            $credencial = CredApiPixService::coletar();

            $credencial_existente = array_filter($credencial, function($item) use($request){
                return $item['id_cliente'] == $request['id_cliente'] && $item['tipo_cred'] == $request['tipo_cred'];
            });

            if(!empty($credencial_existente)){
                return back()->with('error', 'O usuário já possui uma credencial cadastrada');
            }


            $result = CredApiPixService::criar($dados);

            return $result;
            if($result['success'] != true){
                return back()->with('error', 'Houve um erro ao tentar cadastrar a credencial');
            }else{

                return back()->with('success', $result['data']['message']);
            }
        //}catch(\Throwable $e){
            //return $e;
            //return back()->with('error', 'Houve um erro ao tentar cadastrar a credencial');
        //}
    }

  
}

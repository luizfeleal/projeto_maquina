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

    public function coletarCredenciais(Request $request) {
        $credencial = CredApiPixService::coletar();
        return $credencial;
    }

    public function registrarCredencial(Request $request){


        try{

            $dados = [];
            $dados['id_cliente'] = $request['select-cliente'];
            if($request['tipo_cred'] == "pagbank"){
                $dados['client_id'] = strtolower($request['cliente_id']);
            }else{
                $dados['client_id'] = $request['cliente_id'];
            }
            $dados['client_secret'] = $request['cliente_secret'];
            $dados['tipo_cred'] = $request['tipo_cred'];
            if($request['tipo_cred'] == "efi"){
                $dados['caminho_certificado'] = $request['cliente_certificado'];
            }

            $tipo_cred = $request['tipo_cred'];
            $id_cliente = $request['select-cliente'];
            $credencial = CredApiPixService::coletar();

            $credencial_existente = array_filter($credencial, function($item) use($id_cliente, $tipo_cred){
                return $item['id_cliente'] == $id_cliente && $item['tipo_cred'] == $tipo_cred;
            });


            
            if(!empty($credencial_existente)){
                return back()->with('error', 'O usuário já possui uma credencial cadastrada');
            }


            $result = CredApiPixService::criar($dados);

            if($result['success'] != true){
                return back()->with('error', 'Houve um erro ao tentar cadastrar a credencial');
            }else{

                return back()->with('success', $result['data']['message']);
            }
        }catch(\Throwable $e){
            return $e;
            return back()->with('error', 'Houve um erro ao tentar cadastrar a credencial');
        }
    }

    public function editarCredencialEfi(Request $request, $id){
        $clientes = ClientesService::coletar();
        $credencial = CredApiPixService::coletar($id);
        
        if(!$credencial || $credencial['tipo_cred'] !== 'efi'){
            return redirect()->back()->with('error', 'Credencial não encontrada');
        }
        
        return view('Admin.Credenciais.EFI.edit', compact('clientes', 'credencial'));
    }

    public function editarCredencialPagbank(Request $request, $id){
        $clientes = ClientesService::coletar();
        $credencial = CredApiPixService::coletar($id);
        
        if(!$credencial || $credencial['tipo_cred'] !== 'pagbank'){
            return redirect()->back()->with('error', 'Credencial não encontrada');
        }
        
        return view('Admin.Credenciais.PagBank.edit', compact('clientes', 'credencial'));
    }

    public function atualizarCredencial(Request $request, $id){
        try{
            $dados = [];
            $dados['id_cliente'] = $request['select-cliente'];
            if($request['tipo_cred'] == "pagbank"){
                $dados['client_id'] = strtolower($request['cliente_id']);
            }else{
                $dados['client_id'] = $request['cliente_id'];
            }
            $dados['client_secret'] = $request['cliente_secret'];
            $dados['tipo_cred'] = $request['tipo_cred'];
            
            if($request['tipo_cred'] == "efi" && $request->hasFile('cliente_certificado')){
                $dados['caminho_certificado'] = $request['cliente_certificado'];
            }

            $result = CredApiPixService::atualizarCredencial($dados, $id);

            if($result['success'] != true){
                return back()->with('error', 'Houve um erro ao tentar atualizar a credencial');
            }else{
                return back()->with('success', $result['data']['message']);
            }
        }catch(\Throwable $e){
            return back()->with('error', 'Houve um erro ao tentar atualizar a credencial: ' . $e->getMessage());
        }
    }

  
}

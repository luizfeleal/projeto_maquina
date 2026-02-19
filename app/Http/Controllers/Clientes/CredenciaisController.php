<?php

namespace App\Http\Controllers\Clientes;

use Illuminate\Http\Request;
use App\Services\LocaisService;
use App\Services\MaquinasService;
use App\Services\Hardware\MaquinasService as HardwareMaquinas;
use App\Services\ExtratoMaquinaService;
use App\Services\LiberarJogadaService;
use App\Services\CredApiPixService;
use App\Services\ClientesService;
use App\Services\AuthService;
use App\Http\Controllers\Controller;

class CredenciaisController extends Controller
{

    public function listarCredenciais(Request $request){
        $id_cliente_session = session()->get('id_cliente');

        $clientes = ClientesService::coletar();
        $clientes = array_filter($clientes, fn($c) => $c['id_cliente'] == $id_cliente_session);
        $clientes = array_values($clientes);

        $credenciais = CredApiPixService::coletar();
        if(!is_array($credenciais)){
            $credenciais = [];
        }

        // Normalizar para array associativo com chave 'id' (tabela usa id_cred_api_pix)
        $credenciais = array_map(function($c) {
            $c = (array) $c;
            $c['id'] = $c['id_cred_api_pix'] ?? $c['id'] ?? $c['id_cred'] ?? null;
            return $c;
        }, $credenciais);

        // Cliente só vê suas próprias credenciais
        $credenciais = array_filter($credenciais, fn($c) => ($c['id_cliente'] ?? null) == $id_cliente_session);

        // Filtro por tipo
        $tipo_cred = $request->get('tipo_cred');
        if($tipo_cred){
            $credenciais = array_filter($credenciais, fn($c) => ($c['tipo_cred'] ?? '') == $tipo_cred);
        }

        $credenciais = array_values($credenciais);
        
        return view('Clientes.Credenciais.index', compact('clientes', 'credenciais', 'tipo_cred'));
    }

    public function criarCredencialEfi(Request $request){

        $id_cliente = session()->get('id_cliente');

        $clientes = ClientesService::coletar();

        $clientes = array_filter($clientes, function($item) use($id_cliente){
            return $item['id_cliente'] == $id_cliente;
        });
        return view('Clientes.Credenciais.EFI.create', compact('clientes'));
    }
    public function criarCredencialPagbank(Request $request){
        $id_cliente = session()->get('id_cliente');

        $clientes = ClientesService::coletar();

        $clientes = array_filter($clientes, function($item) use($id_cliente){
            return $item['id_cliente'] == $id_cliente;
        });
        return view('Clientes.Credenciais.PagBank.create', compact('clientes'));
    }

    public function registrarCredencial(Request $request){
        try{

            $dados = [];
            $dados['id_cliente'] = $request['select-cliente'];
            $dados['client_id'] = $request['cliente_id'];
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
        $id_cliente_session = session()->get('id_cliente');

        $clientes = ClientesService::coletar();
        $clientes = array_filter($clientes, function($item) use($id_cliente_session){
            return $item['id_cliente'] == $id_cliente_session;
        });

        $credencial = CredApiPixService::coletar($id);
        
        if(!$credencial){
            return redirect()->back()->with('error', 'Credencial não encontrada');
        }
        
        $credencial = (array) $credencial;
        $credencial['id'] = $credencial['id_cred_api_pix'] ?? $credencial['id'] ?? $id;
        if(($credencial['tipo_cred'] ?? '') !== 'efi' || ($credencial['id_cliente'] ?? null) != $id_cliente_session){
            return redirect()->back()->with('error', 'Credencial não encontrada');
        }
        
        return view('Clientes.Credenciais.EFI.edit', compact('clientes', 'credencial'));
    }

    public function editarCredencialPagbank(Request $request, $id){
        $id_cliente_session = session()->get('id_cliente');

        $clientes = ClientesService::coletar();
        $clientes = array_filter($clientes, function($item) use($id_cliente_session){
            return $item['id_cliente'] == $id_cliente_session;
        });

        $credencial = CredApiPixService::coletar($id);
        
        if(!$credencial){
            return redirect()->back()->with('error', 'Credencial não encontrada');
        }
        
        $credencial = (array) $credencial;
        $credencial['id'] = $credencial['id_cred_api_pix'] ?? $credencial['id'] ?? $id;
        if(($credencial['tipo_cred'] ?? '') !== 'pagbank' || ($credencial['id_cliente'] ?? null) != $id_cliente_session){
            return redirect()->back()->with('error', 'Credencial não encontrada');
        }
        
        return view('Clientes.Credenciais.PagBank.edit', compact('clientes', 'credencial'));
    }

    public function atualizarCredencial(Request $request, $id){
        try{
            $id_cliente_session = session()->get('id_cliente');
            
            $dados = [];
            $dados['id_cliente'] = $request['select-cliente'];
            
            // Verifica se o cliente está tentando editar suas próprias credenciais
            if($dados['id_cliente'] != $id_cliente_session){
                return back()->with('error', 'Você não tem permissão para editar esta credencial');
            }
            
            $dados['client_id'] = $request['cliente_id'];
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
